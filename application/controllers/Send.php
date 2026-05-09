<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Send extends CI_Controller {
    private $current_time;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        $this->load->helper(array('form','url','phone','bill'));
        initialize_session_userdata($this);
    }
    public function text_sms() {
        // $_checktime = (int)$this->checktime;
        // if ((int)$this->session->userdata('checktime') >= $_checktime) {
        //     $this->session->set_userdata('checktime', $_checktime);
        //     redirect($_SERVER['HTTP_REFERER']);
        // }
        // $this->session->set_userdata('checktime', (int)($_checktime + 1));

        $this->load->library('form_validation');
        $this->form_validation->set_rules('send_type', 'Send Type', 'required|in_list[1,2,3]');
        $this->form_validation->set_rules('vote_flag', 'Vote Flag', 'required|in_list[1,2]');
        // $this->form_validation->set_rules('send_list', 'Send List', 'required');
        $this->form_validation->set_rules('vote_msg_top', 'Vote Msg Top', 'required');
        // $this->form_validation->set_rules('vote_msg_bottom', 'Vote Msg Bottom', 'required');
        if ($this->input->post('send_type') != '3') {
            $this->form_validation->set_rules('content', 'Content', 'required');
        }
        $this->form_validation->set_rules('reserve_yn', 'Reserve YN', 'required|in_list[Y,N]');
        $this->form_validation->set_rules('divide_yn', 'Divide YN', 'required|in_list[Y,N]');
        $this->form_validation->set_rules('callback', 'Callback', 'required');
        if ($this->input->post('send_type') == '3') {
            $this->form_validation->set_rules('photo_image', 'Photo Image', 'required');
        }

        $this->form_validation->set_rules('bulk_list', 'Bulk List', 'required');
        // $this->form_validation->set_rules('bulk_current_count', 'Bulk Curent Count', 'required|integer');
        $this->form_validation->set_rules('bulk_count', 'Bulk Count', 'required|integer');

        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $send_time = '';
        if ($this->input->post('reserve_yn') == 'Y') {
            $send_time = sprintf('%s %02d:%02d:00',$this->input->post('rsv_date'),(int)$this->input->post('rsv_hour'),(int)$this->input->post('rsv_min'));
            $send_time_unixtime = strtotime($send_time);
            if (time() > $send_time_unixtime) {
                $this->session->set_flashdata('notice', '예약하신 전송시간 오류입니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        $campaign_array = array();
        $campaign_array['tcount'] = 0; //total count
        $campaign_array['bcount'] = 0; //ban count
        $campaign_array['dcount'] = 0; //duplicated count
        // $campaign_array['wcount'] = 0; //wrong count
        $campaign_array['send_type'] = $this->input->post('send_type');
        $campaign_array['subject'] = $this->input->post('subject');
        $campaign_array['callback'] = $this->input->post('callback');
        $campaign_array['send_time'] = $send_time; //YYYY-MM-DD 00:00:00
        $campaign_array['file_cnt'] = 0;
        $campaign_array['file_path_1'] = '';
        $campaign_array['file_path_2'] = '';
        $campaign_array['file_path_3'] = '';
        $campaign_array['msg'] = $this->input->post('content');
        $campaign_array['div_cnt'] = ($this->input->post('divide_yn') == 'Y' ? (int)$this->input->post('div_cnt') : 0);
        $campaign_array['div_min'] = ($this->input->post('divide_yn') == 'Y' ? (int)$this->input->post('div_min') : 0);
        $campaign_array['amount'] = 0;
        $campaign_array['reserve_yn'] = $this->input->post('reserve_yn');
        $campaign_array['divide_yn'] = $this->input->post('divide_yn');
        $campaign_array['merge_yn'] = 'N';
        $campaign_array['ip'] = $this->input->ip_address();
        $campaign_array['storeno'] = $this->session->userdata('storeno');
        $campaign_array['groupno'] = (int)$this->session->userdata('groupno');
        $campaign_array['http_referer'] = $_SERVER['HTTP_REFERER'];
        $campaign_array['vote_flag'] = $this->input->post('vote_flag');
        $campaign_array['message_body'] = $this->input->post('message_body');
        $campaign_array['cate_flag'] = $this->input->post('cate_flag');

        if ($this->session->userdata('ad_type') == '1' && strpos($this->input->post('content'), '(광고)') !== 0) {
            $this->session->set_flashdata('notice', '(광고) 표기 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $cache_keys = array();
        $send_pool = array();
        $name_array = array();
        $bulk_list = explode(',', $this->input->post('bulk_list'));
        // $this->load->driver('cache');
        foreach ($bulk_list as $val) {
            if (!$val) continue;
            //M|sow52340100698|6
            $send_value = explode('|', $val);

            // 맨 앞 0 이 없을 경우 자동 생성
            $tmp_phone = strip_phone($send_value[0]);
            $tmp_str = substr($tmp_phone,0,1);
            if ($tmp_str != '0') $tmp_phone = '0'.$tmp_phone;

            $send_pool[] = $tmp_phone;
            if ($send_value[1] != '') {
                $name_array[$send_value[0]][0] = $send_value[1];
            }
        }
        unset($bulk_list);

        $bans_array = array();
        if (0 && $this->session->userdata('black_list') == '1') {
            $cache_key = 'sow_black_list';
            $bans_array = $this->cache->redis->get($cache_key);
            $ban_cnt = count($bans_array);
            if (!is_array($bans_array) || !$ban_cnt) {
                $this->session->set_flashdata('notice', '블랙 리스트 설정 오류입니다. 관리자에게 문의하시기 바랍니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $this->load->model('addressModels');
        $result = $this->addressModels->get_bans();
        // $bans_array = array();
        foreach ($result as $row) {
            $bans_array[] = $row->mobile;
        }
        if ($this->session->userdata('phone_080') != '') {
            $result = $this->addressModels->get_phone_080();
            foreach ($result as $row) {
                $bans_array[] = strip_phone(trim($row->mobile));
            }
        }
        //total count
        $campaign_array['tcount'] = count($send_pool);
        //중복제거
        $unique_array = array();
        $unique_array = array_unique($send_pool);
        $fcount = count($unique_array);
        unset($send_pool);
        //duplicated count
        $campaign_array['dcount'] = $campaign_array['tcount'] - $fcount;

        //수신거부 차단하기
        $send_array = array_diff($unique_array, $bans_array);
        $scount = count($send_array);
        unset($bans_array);
        unset($unique_array);
        //send count
        $campaign_array['scount'] = $scount;
        //baned count
        $campaign_array['bcount'] = $fcount - $scount;
        //포토문자
        // $cache_key = 'sow_photo'.$this->session->userdata('uniqueno');
        // $photo_array = $this->cache->redis->get($cache_key);
        // if ($this->input->post('send_type') == '3' && $photo_array['file_path'] != '') {
        //     $campaign_array['file_cnt'] = 1;
        //     $campaign_array['file_path_1'] = $photo_array['file_path'];
        //     $campaign_array['image_path_1'] = $photo_array['image_path'];
        // }
        if ($this->input->post('send_type') == '3') {
            $campaign_array['file_cnt'] = 1;
            $campaign_array['file_path_1'] = FCPATH.$this->input->post('photo_image');
            $campaign_array['file_path_1'] = str_replace('//', '/', $campaign_array['file_path_1']);
            $campaign_array['image_path_1'] = $this->input->post('photo_image');
        }

        if ($this->input->post('send_type') == '1') {
            $campaign_array['productcode'] = 'SMS1';
            $campaign_array['priority'] = $this->session->userdata('ch_sms');
            $campaign_array['price'] = (float)$this->session->userdata('sms1');
            $campaign_array['bill_mode'] = 'MS';
        } else if ($this->input->post('send_type') == '2') {
            $campaign_array['productcode'] = 'LMS1';
            $campaign_array['priority'] = $this->session->userdata('ch_lms');
            $campaign_array['price'] = (float)$this->session->userdata('lms1');
            $campaign_array['bill_mode'] = 'ML';
        } else {
            $campaign_array['productcode'] = 'MMS1';
            $campaign_array['priority'] = $this->session->userdata('ch_mms');
            $campaign_array['price'] = (float)$this->session->userdata('mms1');
            $campaign_array['bill_mode'] = 'MM';
        }
        //금액설정
        $campaign_array['amount'] = ($campaign_array['scount'] * $campaign_array['price']);
        $campaign_array['realamount'] = ($campaign_array['scount'] * $campaign_array['price']);
        if ($campaign_array['amount'] > $this->session->userdata('cash')) {
            $this->session->set_flashdata('notice', '충전금액이 부족합니다. 금액을 충전하신 후 이용해 주시기 바랍니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $send_array, 600); //10분 후 자동삭제
        $cache_key = 'sow_name'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $name_array, 600); //10분 후 자동삭제
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $campaign_array, 600); //10분 후 자동삭제
        foreach ($cache_keys as $val) {
            $this->cache->redis->save($val, '', 60);
        }

        //aone 의 경우 발송한 내용,제목을 표기를 원함
        // if ($_SERVER['STORENAME'] == 'aone') {
            $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
            $this->cache->redis->save($cache_key, $campaign_array, 39600); //12시간
        // }
        unset($campaign_array);
        unset($send_array);
        redirect('/send/confirm');
    }
    public function sms_file() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('send_type', 'Send Type', 'required|in_list[1,2,3]');
        $this->form_validation->set_rules('vote_flag', 'Vote Flag', 'required|in_list[1,2]');
        // $this->form_validation->set_rules('send_list', 'Send List', 'required');
        $this->form_validation->set_rules('vote_msg_top', 'Vote Msg Top', 'required');
        // $this->form_validation->set_rules('vote_msg_bottom', 'Vote Msg Bottom', 'required');
        if ($this->input->post('send_type') != '3') {
            $this->form_validation->set_rules('content', 'Content', 'required');
        }
        $this->form_validation->set_rules('reserve_yn', 'Reserve YN', 'required|in_list[Y,N]');
        $this->form_validation->set_rules('divide_yn', 'Divide YN', 'required|in_list[Y,N]');
        $this->form_validation->set_rules('callback', 'Callback', 'required');
        if ($this->input->post('send_type') == '3') {
            $this->form_validation->set_rules('photo_image', 'Photo Image', 'required');
        }
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $send_time = '';
        if ($this->input->post('reserve_yn') == 'Y') {
            $send_time = sprintf('%s %02d:%02d:00',$this->input->post('rsv_date'),(int)$this->input->post('rsv_hour'),(int)$this->input->post('rsv_min'));
            $send_time_unixtime = strtotime($send_time);
            if (time() > $send_time_unixtime) {
                $this->session->set_flashdata('notice', '예약하신 전송시간 오류입니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        $campaign_array = array();
        $campaign_array['tcount'] = 0; //total count
        $campaign_array['bcount'] = 0; //ban count
        $campaign_array['dcount'] = 0; //duplicated count
        // $campaign_array['wcount'] = 0; //wrong count
        $campaign_array['send_type'] = $this->input->post('send_type');
        $campaign_array['subject'] = $this->input->post('subject');
        $campaign_array['callback'] = $this->input->post('callback');
        $campaign_array['send_time'] = $send_time; //YYYY-MM-DD 00:00:00
        $campaign_array['file_cnt'] = 0;
        $campaign_array['file_path_1'] = '';
        $campaign_array['file_path_2'] = '';
        $campaign_array['file_path_3'] = '';
        $campaign_array['msg'] = $this->input->post('content');
        $campaign_array['div_cnt'] = ($this->input->post('divide_yn') == 'Y' ? (int)$this->input->post('div_cnt') : 0);
        $campaign_array['div_min'] = ($this->input->post('divide_yn') == 'Y' ? (int)$this->input->post('div_min') : 0);
        $campaign_array['amount'] = 0;
        $campaign_array['reserve_yn'] = $this->input->post('reserve_yn');
        $campaign_array['divide_yn'] = $this->input->post('divide_yn');
        $campaign_array['merge_yn'] = 'N';
        $campaign_array['ip'] = $this->input->ip_address();
        $campaign_array['storeno'] = $this->session->userdata('storeno');
        $campaign_array['groupno'] = (int)$this->session->userdata('groupno');
        $campaign_array['http_referer'] = $_SERVER['HTTP_REFERER'];
        $campaign_array['vote_flag'] = $this->input->post('vote_flag');
        $campaign_array['message_body'] = $this->input->post('message_body');
        $campaign_array['cate_flag'] = $this->input->post('cate_flag');

        if ($this->session->userdata('ad_type') == '1' && strpos($this->input->post('content'), '(광고)') !== 0) {
            $this->session->set_flashdata('notice', '(광고) 표기 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $date = date('Ym');
        $file_path = FCPATH.'uploads/'.$date;
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, TRUE);
        }
        $file_name = $this->session->userdata('uniqueno').date('is');

        $config['upload_path'] = $file_path;
        $config['file_name'] = $file_name;
        $config['allowed_types'] = 'txt';
        $config['max_size'] = 10240; // 10M
        // $config['max_width'] = 1024;
        // $config['max_height'] = 1024;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('text_file')) {
            error_log(print_r($this->upload->display_errors(),1),0);
            $this->session->set_flashdata('notice', '텍스트 파일 오류가 발생했습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $upload_info = $this->upload->data();

        $send_pool = array();
        $name_array = array();
        $fp = fopen($upload_info['full_path'],'r');
        if ($fp) {
            while (!feof($fp)) {
                $tmp_phone = '';
                $tmp_name = '';

                $content_array = array();
                $content_array = explode('|', fgets($fp, 1024));

                $tmp_phone = strip_phone($content_array[0]);
                $tmp_name = $content_array[1];

                // 맨 앞 0 이 없을 경우 자동 생성
                $tmp_str = substr($tmp_phone,0,1);
                if ($tmp_str != '0') $tmp_phone = '0'.$tmp_phone;

                if (valid_phone($tmp_phone) == false) continue;
                $send_pool[] = $tmp_phone;
                $name_array[$tmp_phone][0] = $tmp_name;
            }
        }
        fclose($fp);

        $pool_cnt = count($send_pool);
        if ($pool_cnt > 50000) {
            unset($send_pool);
            $this->session->set_flashdata('notice', '최대 50,000 개 까지 발송이 가능합니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $bans_array = array();
        if (0 && $this->session->userdata('black_list') == '1') {
            $cache_key = 'sow_black_list';
            $bans_array = $this->cache->redis->get($cache_key);
            $ban_cnt = count($bans_array);
            if (!is_array($bans_array) || !$ban_cnt) {
                $this->session->set_flashdata('notice', '블랙 리스트 설정 오류입니다. 관리자에게 문의하시기 바랍니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $this->load->model('addressModels');
        $result = $this->addressModels->get_bans();
        // $bans_array = array();
        foreach ($result as $row) {
            $bans_array[] = $row->mobile;
        }
        if ($this->session->userdata('phone_080') != '') {
            $result = $this->addressModels->get_phone_080();
            foreach ($result as $row) {
                $bans_array[] = strip_phone(trim($row->mobile));
            }
        }
        //total count
        $campaign_array['tcount'] = count($send_pool);
        //중복제거
        $unique_array = array();
        $unique_array = array_unique($send_pool);
        $fcount = count($unique_array);
        unset($send_pool);
        //duplicated count
        $campaign_array['dcount'] = $campaign_array['tcount'] - $fcount;

        //수신거부 차단하기
        $send_array = array_diff($unique_array, $bans_array);
        $scount = count($send_array);
        unset($bans_array);
        unset($unique_array);
        //send count
        $campaign_array['scount'] = $scount;
        //baned count
        $campaign_array['bcount'] = $fcount - $scount;
        //포토문자
        // $cache_key = 'sow_photo'.$this->session->userdata('uniqueno');
        // $photo_array = $this->cache->redis->get($cache_key);
        // if ($this->input->post('send_type') == '3' && $photo_array['file_path'] != '') {
        //     $campaign_array['file_cnt'] = 1;
        //     $campaign_array['file_path_1'] = $photo_array['file_path'];
        //     $campaign_array['image_path_1'] = $photo_array['image_path'];
        // }
        if ($this->input->post('send_type') == '3') {
            $campaign_array['file_cnt'] = 1;
            $campaign_array['file_path_1'] = FCPATH.$this->input->post('photo_image');
            $campaign_array['file_path_1'] = str_replace('//', '/', $campaign_array['file_path_1']);
            $campaign_array['image_path_1'] = $this->input->post('photo_image');
        }

        if ($this->input->post('send_type') == '1') {
            $campaign_array['productcode'] = 'SMS1';
            $campaign_array['priority'] = $this->session->userdata('ch_sms');
            $campaign_array['price'] = (float)$this->session->userdata('sms1');
            $campaign_array['bill_mode'] = 'MS';
        } else if ($this->input->post('send_type') == '2') {
            $campaign_array['productcode'] = 'LMS1';
            $campaign_array['priority'] = $this->session->userdata('ch_lms');
            $campaign_array['price'] = (float)$this->session->userdata('lms1');
            $campaign_array['bill_mode'] = 'ML';
        } else {
            $campaign_array['productcode'] = 'MMS1';
            $campaign_array['priority'] = $this->session->userdata('ch_mms');
            $campaign_array['price'] = (float)$this->session->userdata('mms1');
            $campaign_array['bill_mode'] = 'MM';
        }
        //금액설정
        $campaign_array['amount'] = ($campaign_array['scount'] * $campaign_array['price']);
        $campaign_array['realamount'] = ($campaign_array['scount'] * $campaign_array['price']);
        if ($campaign_array['amount'] > $this->session->userdata('cash')) {
            $this->session->set_flashdata('notice', '충전금액이 부족합니다. 금액을 충전하신 후 이용해 주시기 바랍니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $send_array, 600); //10분 후 자동삭제
        $cache_key = 'sow_name'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $name_array, 600); //10분 후 자동삭제
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $campaign_array, 600); //10분 후 자동삭제
        foreach ($cache_keys as $val) {
            $this->cache->redis->save($val, '', 60);
        }

        //aone 의 경우 발송한 내용,제목을 표기를 원함
        // if (1 || $_SERVER['STORENAME'] == 'aone') {
            $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
            $this->cache->redis->save($cache_key, $campaign_array, 39600); //12시간
        // }
        unset($campaign_array);
        unset($send_array);
        redirect('/send/confirm');
    }
    public function sms_excel() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('send_type', 'Send Type', 'required|in_list[1,2,3]');
        $this->form_validation->set_rules('vote_flag', 'Vote Flag', 'required|in_list[1,2]');
        // $this->form_validation->set_rules('send_list', 'Send List', 'required');
        $this->form_validation->set_rules('vote_msg_top', 'Vote Msg Top', 'required');
        // $this->form_validation->set_rules('vote_msg_bottom', 'Vote Msg Bottom', 'required');
        if ($this->input->post('send_type') != '3') {
            $this->form_validation->set_rules('content', 'Content', 'required');
        }
        $this->form_validation->set_rules('reserve_yn', 'Reserve YN', 'required|in_list[Y,N]');
        $this->form_validation->set_rules('divide_yn', 'Divide YN', 'required|in_list[Y,N]');
        $this->form_validation->set_rules('callback', 'Callback', 'required');
        if ($this->input->post('send_type') == '3') {
            $this->form_validation->set_rules('photo_image', 'Photo Image', 'required');
        }
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $send_time = '';
        if ($this->input->post('reserve_yn') == 'Y') {
            $send_time = sprintf('%s %02d:%02d:00',$this->input->post('rsv_date'),(int)$this->input->post('rsv_hour'),(int)$this->input->post('rsv_min'));
            $send_time_unixtime = strtotime($send_time);
            if (time() > $send_time_unixtime) {
                $this->session->set_flashdata('notice', '예약하신 전송시간 오류입니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        $campaign_array = array();
        $campaign_array['tcount'] = 0; //total count
        $campaign_array['bcount'] = 0; //ban count
        $campaign_array['dcount'] = 0; //duplicated count
        // $campaign_array['wcount'] = 0; //wrong count
        $campaign_array['send_type'] = $this->input->post('send_type');
        $campaign_array['subject'] = $this->input->post('subject');
        $campaign_array['callback'] = $this->input->post('callback');
        $campaign_array['send_time'] = $send_time; //YYYY-MM-DD 00:00:00
        $campaign_array['file_cnt'] = 0;
        $campaign_array['file_path_1'] = '';
        $campaign_array['file_path_2'] = '';
        $campaign_array['file_path_3'] = '';
        $campaign_array['msg'] = $this->input->post('content');
        $campaign_array['div_cnt'] = ($this->input->post('divide_yn') == 'Y' ? (int)$this->input->post('div_cnt') : 0);
        $campaign_array['div_min'] = ($this->input->post('divide_yn') == 'Y' ? (int)$this->input->post('div_min') : 0);
        $campaign_array['amount'] = 0;
        $campaign_array['reserve_yn'] = $this->input->post('reserve_yn');
        $campaign_array['divide_yn'] = $this->input->post('divide_yn');
        $campaign_array['merge_yn'] = 'N';
        $campaign_array['ip'] = $this->input->ip_address();
        $campaign_array['storeno'] = $this->session->userdata('storeno');
        $campaign_array['groupno'] = (int)$this->session->userdata('groupno');
        $campaign_array['http_referer'] = $_SERVER['HTTP_REFERER'];
        $campaign_array['vote_flag'] = $this->input->post('vote_flag');
        $campaign_array['message_body'] = $this->input->post('message_body');
        $campaign_array['cate_flag'] = $this->input->post('cate_flag');

        if ($this->session->userdata('ad_type') == '1' && strpos($this->input->post('content'), '(광고)') !== 0) {
            $this->session->set_flashdata('notice', '(광고) 표기 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $date = date('Ym');
        $file_path = FCPATH.'uploads/'.$date;
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, TRUE);
        }
        $file_name = $this->session->userdata('uniqueno').date('is');

        $config['upload_path'] = $file_path;
        $config['file_name'] = $file_name;
        $config['allowed_types'] = 'xlsx|xls';
        $config['max_size'] = 10240; // 10M
        // $config['max_width'] = 1024;
        // $config['max_height'] = 1024;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('text_file')) {
            error_log(print_r($this->upload->display_errors(),1),0);
            $this->session->set_flashdata('notice', '텍스트 파일 오류가 발생했습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $upload_info = $this->upload->data();


        $this->load->library("PHPExcel");
        $objPHPExcel = new PHPExcel();
        $objPHPExcel = PHPExcel_IOFactory::load($upload_info['full_path']);
        $sheetsCount = $objPHPExcel->getSheetCount();

        $send_pool = array();
        $name_array = array();

        /* 쉬트별로 읽기 */
        for ($i = 0; $i < $sheetsCount; $i++)
        {
            $objPHPExcel->setActiveSheetIndex($i);
            $sheet = $objPHPExcel->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            /* 한줄읽기 */
            for ($row = 1; $row <= $highestRow; $row++)
            {
                /* $rowData가 한줄의 데이터를 셀별로 배열처리 됩니다. */
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

                $tmp_phone = '';
                $tmp_phone = strip_phone($rowData[0][0]);
                $tmp_name = '';
                $tmp_name = $rowData[0][1];

                // 맨 앞 0 이 없을 경우 자동 생성
                $tmp_str = substr($tmp_phone,0,1);
                if ($tmp_str != '0') $tmp_phone = '0'.$tmp_phone;

                if (valid_phone($tmp_phone) == false) continue;
                $send_pool[] = $tmp_phone;
                $name_array[$tmp_phone][0] = $tmp_name;
            }
        }

        $pool_cnt = count($send_pool);
        if ($pool_cnt > 50000) {
            unset($send_pool);
            $this->session->set_flashdata('notice', '최대 50,000 개 까지 발송이 가능합니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $bans_array = array();
        if (0 && $this->session->userdata('black_list') == '1') {
            $cache_key = 'sow_black_list';
            $bans_array = $this->cache->redis->get($cache_key);
            $ban_cnt = count($bans_array);
            if (!is_array($bans_array) || !$ban_cnt) {
                $this->session->set_flashdata('notice', '블랙 리스트 설정 오류입니다. 관리자에게 문의하시기 바랍니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $this->load->model('addressModels');
        $result = $this->addressModels->get_bans();
        // $bans_array = array();
        foreach ($result as $row) {
            $bans_array[] = $row->mobile;
        }
        if ($this->session->userdata('phone_080') != '') {
            $result = $this->addressModels->get_phone_080();
            foreach ($result as $row) {
                $bans_array[] = strip_phone(trim($row->mobile));
            }
        }
        //total count
        $campaign_array['tcount'] = count($send_pool);
        //중복제거
        $unique_array = array();
        $unique_array = array_unique($send_pool);
        $fcount = count($unique_array);
        unset($send_pool);
        //duplicated count
        $campaign_array['dcount'] = $campaign_array['tcount'] - $fcount;

        //수신거부 차단하기
        $send_array = array_diff($unique_array, $bans_array);
        $scount = count($send_array);
        unset($bans_array);
        unset($unique_array);
        //send count
        $campaign_array['scount'] = $scount;
        //baned count
        $campaign_array['bcount'] = $fcount - $scount;
        //포토문자
        // $cache_key = 'sow_photo'.$this->session->userdata('uniqueno');
        // $photo_array = $this->cache->redis->get($cache_key);
        // if ($this->input->post('send_type') == '3' && $photo_array['file_path'] != '') {
        //     $campaign_array['file_cnt'] = 1;
        //     $campaign_array['file_path_1'] = $photo_array['file_path'];
        //     $campaign_array['image_path_1'] = $photo_array['image_path'];
        // }
        if ($this->input->post('send_type') == '3') {
            $campaign_array['file_cnt'] = 1;
            $campaign_array['file_path_1'] = FCPATH.$this->input->post('photo_image');
            $campaign_array['file_path_1'] = str_replace('//', '/', $campaign_array['file_path_1']);
            $campaign_array['image_path_1'] = $this->input->post('photo_image');
        }

        if ($this->input->post('send_type') == '1') {
            $campaign_array['productcode'] = 'SMS1';
            $campaign_array['priority'] = $this->session->userdata('ch_sms');
            $campaign_array['price'] = (float)$this->session->userdata('sms1');
            $campaign_array['bill_mode'] = 'MS';
        } else if ($this->input->post('send_type') == '2') {
            $campaign_array['productcode'] = 'LMS1';
            $campaign_array['priority'] = $this->session->userdata('ch_lms');
            $campaign_array['price'] = (float)$this->session->userdata('lms1');
            $campaign_array['bill_mode'] = 'ML';
        } else {
            $campaign_array['productcode'] = 'MMS1';
            $campaign_array['priority'] = $this->session->userdata('ch_mms');
            $campaign_array['price'] = (float)$this->session->userdata('mms1');
            $campaign_array['bill_mode'] = 'MM';
        }
        //금액설정
        $campaign_array['amount'] = ($campaign_array['scount'] * $campaign_array['price']);
        $campaign_array['realamount'] = ($campaign_array['scount'] * $campaign_array['price']);
        if ($campaign_array['amount'] > $this->session->userdata('cash')) {
            $this->session->set_flashdata('notice', '충전금액이 부족합니다. 금액을 충전하신 후 이용해 주시기 바랍니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $send_array, 600); //10분 후 자동삭제
        $cache_key = 'sow_name'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $name_array, 600); //10분 후 자동삭제
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $campaign_array, 600); //10분 후 자동삭제
        foreach ($cache_keys as $val) {
            $this->cache->redis->save($val, '', 60);
        }

        //aone 의 경우 발송한 내용,제목을 표기를 원함
        // if (1 || $_SERVER['STORENAME'] == 'aone') {
            $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
            $this->cache->redis->save($cache_key, $campaign_array, 39600); //12시간
        // }
        unset($campaign_array);
        unset($send_array);
        redirect('/send/confirm');
    }
    public function switch() {
        // $_checktime = (int)$this->checktime;
        // if ((int)$this->session->userdata('checktime') >= $_checktime) {
        //     $this->session->set_userdata('checktime', $_checktime);
        //     redirect($_SERVER['HTTP_REFERER']);
        // }
        // $this->session->set_userdata('checktime', (int)($_checktime + 1));

        $this->load->library('form_validation');
        $this->form_validation->set_rules('send_type', 'Send Type', 'required|in_list[1,2,3]');
        $this->form_validation->set_rules('vote_flag', 'Vote Flag', 'required|in_list[1,2]');
        $this->form_validation->set_rules('send_list', 'Send List', 'required');
        $this->form_validation->set_rules('vote_msg_top', 'Vote Msg Top', 'required');
        // $this->form_validation->set_rules('vote_msg_bottom', 'Vote Msg Bottom', 'required');
        $this->form_validation->set_rules('content', 'Content', 'required');
        $this->form_validation->set_rules('reserve_yn', 'Reserve YN', 'required|in_list[Y,N]');
        $this->form_validation->set_rules('divide_yn', 'Divide YN', 'required|in_list[Y,N]');
        $this->form_validation->set_rules('callback', 'Callback', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $send_time = '';
        if ($this->input->post('reserve_yn') == 'Y') {
            $send_time = sprintf('%s %02d:%02d:00',$this->input->post('rsv_date'),(int)$this->input->post('rsv_hour'),(int)$this->input->post('rsv_min'));
            $send_time_unixtime = strtotime($send_time);
            if (time() > $send_time_unixtime) {
                $this->session->set_flashdata('notice', '예약하신 전송시간 오류입니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        $campaign_array = array();
        $campaign_array['tcount'] = 0; //total count
        $campaign_array['bcount'] = 0; //ban count
        $campaign_array['dcount'] = 0; //duplicated count
        // $campaign_array['wcount'] = 0; //wrong count
        $campaign_array['send_type'] = $this->input->post('send_type');
        $campaign_array['subject'] = $this->input->post('subject');
        $campaign_array['callback'] = $this->input->post('callback');
        $campaign_array['send_time'] = $send_time; //YYYY-MM-DD 00:00:00
        $campaign_array['file_cnt'] = 0;
        $campaign_array['file_path_1'] = '';
        $campaign_array['file_path_2'] = '';
        $campaign_array['file_path_3'] = '';
        $campaign_array['msg'] = $this->input->post('content');
        $campaign_array['div_cnt'] = ($this->input->post('divide_yn') == 'Y' ? (int)$this->input->post('div_cnt') : 0);
        $campaign_array['div_min'] = ($this->input->post('divide_yn') == 'Y' ? (int)$this->input->post('div_min') : 0);
        $campaign_array['amount'] = 0;
        $campaign_array['reserve_yn'] = $this->input->post('reserve_yn');
        $campaign_array['divide_yn'] = $this->input->post('divide_yn');
        $campaign_array['merge_yn'] = 'Y';
        $campaign_array['ip'] = $this->input->ip_address();
        $campaign_array['storeno'] = $this->session->userdata('storeno');
        $campaign_array['groupno'] = (int)$this->session->userdata('groupno');
        $campaign_array['http_referer'] = $_SERVER['HTTP_REFERER'];
        $campaign_array['vote_flag'] = $this->input->post('vote_flag');
        $campaign_array['message_body'] = $this->input->post('message_body')[0];
        $campaign_array['cate_flag'] = $this->input->post('cate_flag');

        if ($this->session->userdata('ad_type') == '1' && strpos($this->input->post('content'), '(광고)') !== 0) {
            $this->session->set_flashdata('notice', '(광고) 표기 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $row_mobile = '';
        $name_array = array();
        $send_pool = array();
        $send_list = explode('|,|', $this->input->post('send_list'));
        foreach ($send_list as $val) {
            if (!$val) continue;
            $send_value = explode('|^|', $val);
            $row_mobile = '';
            $row_mobile = trim(strip_phone($send_value[0]));
            if (!$row_mobile) continue;

            // 맨 앞 0 이 없을 경우 자동 생성
            $tmp_str = substr($row_mobile,0,1);
            if ($tmp_str != '0') $row_mobile = '0'.$row_mobile;

            $send_pool[] = $row_mobile;
            $name_array[$row_mobile][0] = $send_value[1];
            $name_array[$row_mobile][1] = $send_value[2];
            $name_array[$row_mobile][2] = $send_value[3];
            $name_array[$row_mobile][3] = $send_value[4];
            $name_array[$row_mobile][4] = $send_value[5];
        }
        unset($send_list);

        $bans_array = array();
        if (0 && $this->session->userdata('black_list') == '1') {
            $cache_key = 'sow_black_list';
            $bans_array = $this->cache->redis->get($cache_key);
            $ban_cnt = count($bans_array);
            if (!is_array($bans_array) || !$ban_cnt) {
                $this->session->set_flashdata('notice', '블랙 리스트 설정 오류입니다. 관리자에게 문의하시기 바랍니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $this->load->model('addressModels');
        $result = $this->addressModels->get_bans();
        // $bans_array = array();
        foreach ($result as $row) {
            $bans_array[] = $row->mobile;
        }
        if ($this->session->userdata('phone_080') != '') {
            $result = $this->addressModels->get_phone_080();
            foreach ($result as $row) {
                $bans_array[] = strip_phone(trim($row->mobile));
            }
        }
        //total count
        $campaign_array['tcount'] = count($send_pool);
        //중복제거
        $unique_array = array();
        $unique_array = array_unique($send_pool);
        $fcount = count($unique_array);
        unset($send_pool);
        //duplicated count
        $campaign_array['dcount'] = $campaign_array['tcount'] - $fcount;

        //수신거부 차단하기
        $send_array = array_diff($unique_array, $bans_array);
        $scount = count($send_array);
        unset($bans_array);
        unset($unique_array);
        //send count
        $campaign_array['scount'] = $scount;
        //baned count
        $campaign_array['bcount'] = $fcount - $scount;

        if ($this->input->post('send_type') == '1') {
            $campaign_array['productcode'] = 'SMS1';
            $campaign_array['priority'] = $this->session->userdata('ch_sms');
            $campaign_array['price'] = (float)$this->session->userdata('sms1');
            $campaign_array['bill_mode'] = 'MS';
        } else if ($this->input->post('send_type') == '2') {
            $campaign_array['productcode'] = 'LMS1';
            $campaign_array['priority'] = $this->session->userdata('ch_lms');
            $campaign_array['price'] = (float)$this->session->userdata('lms1');
            $campaign_array['bill_mode'] = 'ML';

            $option = array(
                'priority' => $campaign_array['priority'],
            );
            $this->load->model('smsModels');
            $result = $this->smsModels->get_priority_restrict_merge($option);
            if ($result->xid) {
                $this->session->set_flashdata('notice', '해당 회원의 장문 채널은 내용바꿔 보내기 서비스를 이용할 수 없습니다. 관리자에게 문의하세요.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $campaign_array['productcode'] = 'MMS1';
            $campaign_array['priority'] = $this->session->userdata('ch_mms');
            $campaign_array['price'] = (float)$this->session->userdata('mms1');
            $campaign_array['bill_mode'] = 'MM';
        }
        //금액설정
        $campaign_array['amount'] = ($campaign_array['scount'] * $campaign_array['price']);
        $campaign_array['realamount'] = ($campaign_array['scount'] * $campaign_array['price']);
        if ($campaign_array['amount'] > $this->session->userdata('cash')) {
            $this->session->set_flashdata('notice', '충전금액이 부족합니다. 금액을 충전하신 후 이용해 주시기 바랍니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        // $this->load->driver('cache');
        $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $send_array, 600); //10분 후 자동삭제
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $campaign_array, 600); //10분 후 자동삭제
        $cache_key = 'sow_name'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $name_array, 600); //10분 후 자동삭제

        //aone 의 경우 발송한 내용,제목을 표기를 원함
        // if ($_SERVER['STORENAME'] == 'aone') {
            $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
            $this->cache->redis->save($cache_key, $campaign_array, 39600); //12시간
        // }

        unset($send_array);
        unset($name_array);
        unset($campaign_array);
        redirect('/send/confirm');
    }
    public function sms() {
        // $_checktime = (int)$this->checktime;
        // if ((int)$this->session->userdata('checktime') >= $_checktime) {
        //     $this->session->set_userdata('checktime', $_checktime);
        //     redirect($_SERVER['HTTP_REFERER']);
        // }
        // $this->session->set_userdata('checktime', (int)($_checktime + 1));
        $this->load->library('form_validation');
        $this->form_validation->set_rules('send_type', 'Send Type', 'required|in_list[1,2,3]');
        $this->form_validation->set_rules('vote_flag', 'Vote Flag', 'required|in_list[1,2]');
        $this->form_validation->set_rules('send_list', 'Send List', 'required');
        $this->form_validation->set_rules('vote_msg_top', 'Vote Msg Top', 'required');
        // $this->form_validation->set_rules('vote_msg_bottom', 'Vote Msg Bottom', 'required');
        if ($this->input->post('send_type') != '3') {
            $this->form_validation->set_rules('content', 'Content', 'required');
        }
        $this->form_validation->set_rules('reserve_yn', 'Reserve YN', 'required|in_list[Y,N]');
        $this->form_validation->set_rules('divide_yn', 'Divide YN', 'required|in_list[Y,N]');
        $this->form_validation->set_rules('callback', 'Callback', 'required');
        if ($this->input->post('send_type') == '3') {
            $this->form_validation->set_rules('photo_image', 'Photo Image', 'required');
        }
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $send_time = '';
        if ($this->input->post('reserve_yn') == 'Y') {
            $send_time = sprintf('%s %02d:%02d:00',$this->input->post('rsv_date'),(int)$this->input->post('rsv_hour'),(int)$this->input->post('rsv_min'));
            $send_time_unixtime = strtotime($send_time);
            if (time() > $send_time_unixtime) {
                $this->session->set_flashdata('notice', '예약하신 전송시간 오류입니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        $campaign_array = array();
        $campaign_array['tcount'] = 0; //total count
        $campaign_array['bcount'] = 0; //ban count
        $campaign_array['dcount'] = 0; //duplicated count
        // $campaign_array['wcount'] = 0; //wrong count
        $campaign_array['send_type'] = $this->input->post('send_type');
        $campaign_array['subject'] = $this->input->post('subject');
        $campaign_array['callback'] = $this->input->post('callback');
        $campaign_array['send_time'] = $send_time; //YYYY-MM-DD 00:00:00
        $campaign_array['file_cnt'] = 0;
        $campaign_array['file_path_1'] = '';
        $campaign_array['file_path_2'] = '';
        $campaign_array['file_path_3'] = '';
        $campaign_array['msg'] = $this->input->post('content');
        $campaign_array['div_cnt'] = ($this->input->post('divide_yn') == 'Y' ? (int)$this->input->post('div_cnt') : 0);
        $campaign_array['div_min'] = ($this->input->post('divide_yn') == 'Y' ? (int)$this->input->post('div_min') : 0);
        $campaign_array['amount'] = 0;
        $campaign_array['reserve_yn'] = $this->input->post('reserve_yn');
        $campaign_array['divide_yn'] = $this->input->post('divide_yn');
        $campaign_array['merge_yn'] = 'N';
        $campaign_array['ip'] = $this->input->ip_address();
        $campaign_array['storeno'] = $this->session->userdata('storeno');
        $campaign_array['groupno'] = (int)$this->session->userdata('groupno');
        $campaign_array['http_referer'] = $_SERVER['HTTP_REFERER'];
        $campaign_array['vote_flag'] = $this->input->post('vote_flag');
        $campaign_array['message_body'] = $this->input->post('message_body');
        $campaign_array['cate_flag'] = $this->input->post('cate_flag');

// error_log($campaign_array['message_body'], 0);
// error_log(print_r($_POST,1),0);
// error_log($this->input->post('message_body'), 0);

        if ($this->session->userdata('ad_type') == '1' && strpos($this->input->post('content'), '(광고)') !== 0) {
            $this->session->set_flashdata('notice', '(광고) 표기 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $cache_keys = array();
        $send_pool = array();
        $send_list = explode(',', $this->input->post('send_list'));
        // $this->load->driver('cache');
        foreach ($send_list as $val) {
            if (!$val) continue;
            //M|sow52340100698|6
            $send_value = explode('|', $val);
            if ($send_value[1] == '') continue;

            if ($send_value[0] == 'P') {
                $send_pool[] = strip_phone($send_value[1]);
            } else {
                $cached_array = $this->cache->redis->get($send_value[1]);
                if (!is_array($cached_array)) continue;
                $send_pool = array_merge($send_pool, $cached_array);
                $cache_keys[] = $send_value[1];
            }
        }
        unset($send_list);

        $bans_array = array();
        if (0 && $this->session->userdata('black_list') == '1') {
            $cache_key = 'sow_black_list';
            $bans_array = $this->cache->redis->get($cache_key);
            $ban_cnt = count($bans_array);
            if (!is_array($bans_array) || !$ban_cnt) {
                $this->session->set_flashdata('notice', '블랙 리스트 설정 오류입니다. 관리자에게 문의하시기 바랍니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $this->load->model('addressModels');
        $result = $this->addressModels->get_bans();
        // $bans_array = array();
        foreach ($result as $row) {
            $bans_array[] = $row->mobile;
        }
        if ($this->session->userdata('phone_080') != '') {
            $result = $this->addressModels->get_phone_080();
            foreach ($result as $row) {
                $bans_array[] = strip_phone(trim($row->mobile));
            }
        }
        //total count
        $campaign_array['tcount'] = count($send_pool);
        //중복제거
        $unique_array = array();
        $unique_array = array_unique($send_pool);
        $fcount = count($unique_array);
        unset($send_pool);
        //duplicated count
        $campaign_array['dcount'] = $campaign_array['tcount'] - $fcount;

        //수신거부 차단하기
        $send_array = array_diff($unique_array, $bans_array);
        $scount = count($send_array);
        unset($bans_array);
        unset($unique_array);
        //send count
        $campaign_array['scount'] = $scount;
        //baned count
        $campaign_array['bcount'] = $fcount - $scount;
        //포토문자
        // $cache_key = 'sow_photo'.$this->session->userdata('uniqueno');
        // $photo_array = $this->cache->redis->get($cache_key);
        // if ($this->input->post('send_type') == '3' && $photo_array['file_path'] != '') {
        //     $campaign_array['file_cnt'] = 1;
        //     $campaign_array['file_path_1'] = $photo_array['file_path'];
        //     $campaign_array['image_path_1'] = $photo_array['image_path'];
        // }
        if ($this->input->post('send_type') == '3') {
            $campaign_array['file_cnt'] = 1;
            $campaign_array['file_path_1'] = FCPATH.$this->input->post('photo_image');
            $campaign_array['file_path_1'] = str_replace('//', '/', $campaign_array['file_path_1']);
            $campaign_array['image_path_1'] = $this->input->post('photo_image');
        }

        if ($this->input->post('send_type') == '1') {
            $campaign_array['productcode'] = 'SMS1';
            $campaign_array['priority'] = $this->session->userdata('ch_sms');
            $campaign_array['price'] = (float)$this->session->userdata('sms1');
            $campaign_array['bill_mode'] = 'MS';
        } else if ($this->input->post('send_type') == '2') {
            $campaign_array['productcode'] = 'LMS1';
            $campaign_array['priority'] = $this->session->userdata('ch_lms');
            $campaign_array['price'] = (float)$this->session->userdata('lms1');
            $campaign_array['bill_mode'] = 'ML';
        } else {
            $campaign_array['productcode'] = 'MMS1';
            $campaign_array['priority'] = $this->session->userdata('ch_mms');
            $campaign_array['price'] = (float)$this->session->userdata('mms1');
            $campaign_array['bill_mode'] = 'MM';
        }
        //금액설정
        $campaign_array['amount'] = ($campaign_array['scount'] * $campaign_array['price']);
        $campaign_array['realamount'] = ($campaign_array['scount'] * $campaign_array['price']);
        if ($campaign_array['amount'] > $this->session->userdata('cash')) {
            $this->session->set_flashdata('notice', '충전금액이 부족합니다. 금액을 충전하신 후 이용해 주시기 바랍니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $send_array, 600); //10분 후 자동삭제
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $campaign_array, 600); //10분 후 자동삭제
        foreach ($cache_keys as $val) {
            $this->cache->redis->save($val, '', 60);
        }

        //aone 의 경우 발송한 내용,제목을 표기를 원함
        // if ($_SERVER['STORENAME'] == 'aone') {
            $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
            $this->cache->redis->save($cache_key, $campaign_array, 39600); //12시간
        // }
        unset($campaign_array);
        unset($send_array);
        redirect('/send/confirm');
    }
    public function send_sms() {
        // $_checktime = (int)$this->checktime;
        // if ((int)$this->session->userdata('checktime') >= $_checktime) {
        //     $this->session->set_userdata('checktime', $_checktime);
        //     redirect($_SERVER['HTTP_REFERER']);
        // }
        // $this->session->set_userdata('checktime', (int)($_checktime + 1));

        $this->load->library('form_validation');
        $this->form_validation->set_rules('send_type', 'Send Type', 'required|in_list[1,2,3]');
        $this->form_validation->set_rules('vote_flag', 'Vote Flag', 'required|in_list[1,2]');
        $this->form_validation->set_rules('send_list', 'Send List', 'required');
        $this->form_validation->set_rules('vote_msg_top', 'Vote Msg Top', 'required');
        // $this->form_validation->set_rules('vote_msg_bottom', 'Vote Msg Bottom', 'required');
        if ($this->input->post('send_type') != '3') {
            $this->form_validation->set_rules('content', 'Content', 'required');
            $this->form_validation->set_rules('message_body', 'Message Body', 'required');
        }
        // $this->form_validation->set_rules('reserve_yn', 'Reserve YN', 'required|in_list[Y,N]');
        // $this->form_validation->set_rules('divide_yn', 'Divide YN', 'required|in_list[Y,N]');
        $this->form_validation->set_rules('callback', 'Callback', 'required');
        if ($this->input->post('send_type') == '3') {
            $this->form_validation->set_rules('photo_image', 'Photo Image', 'required');
        }
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('layer_notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $send_time = '';
        if ($this->input->post('reserve_yn') == 'Y') {
            $send_time = sprintf('%s %02d:%02d:00',$this->input->post('rsv_date'),(int)$this->input->post('rsv_hour'),(int)$this->input->post('rsv_min'));
            $send_time_unixtime = strtotime($send_time);
            if (time() > $send_time_unixtime) {
                $this->session->set_flashdata('notice', '예약하신 전송시간 오류입니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        if ($this->input->post('trash_list')) {
            $trash_array = array();
            $trash_array = explode('|', $this->input->post('trash_list'));
        }

        $campaign_array = array();
        $campaign_array['tcount'] = 0; //total count
        $campaign_array['bcount'] = 0; //ban count
        $campaign_array['dcount'] = 0; //duplicated count
        // $campaign_array['wcount'] = 0; //wrong count
        $campaign_array['send_type'] = $this->input->post('send_type');
        $campaign_array['subject'] = $this->input->post('subject');
        $campaign_array['callback'] = $this->input->post('callback');
        $campaign_array['send_time'] = $send_time; //YYYY-MM-DD 00:00:00
        $campaign_array['file_cnt'] = 0;
        $campaign_array['file_path_1'] = '';
        $campaign_array['file_path_2'] = '';
        $campaign_array['file_path_3'] = '';
        $campaign_array['msg'] = $this->input->post('content');
        $campaign_array['div_cnt'] = ($this->input->post('divide_yn') == 'Y' ? (int)$this->input->post('div_cnt') : 0);
        $campaign_array['div_min'] = ($this->input->post('divide_yn') == 'Y' ? (int)$this->input->post('div_min') : 0);
        $campaign_array['amount'] = 0;
        $campaign_array['reserve_yn'] = 'N';
        $campaign_array['divide_yn'] = 'N';
        $campaign_array['merge_yn'] = 'N';
        $campaign_array['ip'] = $this->input->ip_address();
        $campaign_array['storeno'] = $this->session->userdata('storeno');
        $campaign_array['groupno'] = (int)$this->session->userdata('groupno');
        $campaign_array['vote_flag'] = $this->input->post('vote_flag');
        $campaign_array['message_body'] = $this->input->post('message_body');
        $campaign_array['cate_flag'] = $this->input->post('cate_flag');

        if ($this->session->userdata('ad_type') == '1' && strpos($this->input->post('content'), '(광고)') !== 0) {
            $this->session->set_flashdata('notice', '(광고) 표기 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $send_pool = array();
        $send_list = explode(',', $this->input->post('send_list'));
        // $this->load->driver('cache');
        foreach ($send_list as $val) {
            if (!$val) continue;
            //M|sow52340100698|6
            $send_value = explode('|', $val);
            if ($send_value[1] == '') continue;
            if ($send_value[0] != 'P') continue;
            $send_pool[] = strip_phone($send_value[1]);
        }
        unset($send_list);

        $bans_array = array();
        if (0 && $this->session->userdata('black_list') == '1') {
            $cache_key = 'sow_black_list';
            $bans_array = $this->cache->redis->get($cache_key);
            $ban_cnt = count($bans_array);
            if (!is_array($bans_array) || !$ban_cnt) {
                $this->session->set_flashdata('notice', '블랙 리스트 설정 오류입니다. 관리자에게 문의하시기 바랍니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $this->load->model('addressModels');
        $result = $this->addressModels->get_bans();
        // $bans_array = array();
        foreach ($result as $row) {
            $bans_array[] = $row->mobile;
        }
        if ($this->session->userdata('phone_080') != '') {
            $result = $this->addressModels->get_phone_080();
            foreach ($result as $row) {
                $bans_array[] = strip_phone(trim($row->mobile));
            }
        }
        //중복제거
        $unique_array = array();
        $unique_array = array_unique($send_pool);
        // unset($send_pool);

        //수신거부 차단하기
        $send_array = array_diff($unique_array, $bans_array);
        $scount = count($send_array);
        if (!$scount) {
            error_log(print_r($campaign_array,1),0);
            unset($send_pool);
            unset($send_array);
            unset($name_array);
            unset($campaign_array);
            $this->session->set_flashdata('layer_notice', '전송할 데이타가 존재하지 않습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        unset($bans_array);
        unset($unique_array);

        //send count
        $campaign_array['scount'] = $scount;

        //포토문자
        // $cache_key = 'sow_photo'.$this->session->userdata('uniqueno');
        // $photo_array = $this->cache->redis->get($cache_key);
        // if ($this->input->post('send_type') == '3' && $photo_array['file_path'] != '') {
        //     $campaign_array['file_cnt'] = 1;
        //     $campaign_array['file_path_1'] = $photo_array['file_path'];
        //     $campaign_array['image_path_1'] = $photo_array['image_path'];
        // }
        if ($this->input->post('send_type') == '3') {
            $campaign_array['file_cnt'] = 1;
            $campaign_array['file_path_1'] = FCPATH.$this->input->post('photo_image');
            $campaign_array['file_path_1'] = str_replace('//', '/', $campaign_array['file_path_1']);
            $campaign_array['image_path_1'] = $this->input->post('photo_image');
        }

        if ($this->input->post('send_type') == '1') {
            $campaign_array['productcode'] = 'SMS2';
            $campaign_array['priority'] = $this->session->userdata('ch_sms');
            $campaign_array['price'] = (float)$this->session->userdata('sms2');
            $campaign_array['bill_mode'] = 'MS';
        } else if ($this->input->post('send_type') == '2') {
            $campaign_array['productcode'] = 'LMS2';
            $campaign_array['priority'] = $this->session->userdata('ch_lms');
            $campaign_array['price'] = (float)$this->session->userdata('lms2');
            $campaign_array['bill_mode'] = 'ML';
        } else {
            $campaign_array['productcode'] = 'MMS2';
            $campaign_array['priority'] = $this->session->userdata('ch_mms');
            $campaign_array['price'] = (float)$this->session->userdata('mms2');
            $campaign_array['bill_mode'] = 'MM';
        }
        //금액설정
        $campaign_array['amount'] = ($campaign_array['scount'] * $campaign_array['price']);
        $campaign_array['realamount'] = ($campaign_array['scount'] * $campaign_array['price']);

        if ($campaign_array['amount'] > $this->session->userdata('cash')) {
            unset($send_pool);
            unset($send_array);
            unset($campaign_array);
            unset($name_array);
            $this->session->set_flashdata('layer_notice', '충전 금액이 부족합니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        // $this->load->driver('cache');
        $cache_key = 'sow_elect_name'.$this->session->userdata('uniqueno');
        $name_array = $this->cache->redis->get($cache_key);

        $this->load->model('smsModels');
        $result = $this->smsModels->add_campaign_bulk($campaign_array,$send_array,$name_array);
        if (!$result) {
            error_log('(error)....add_campaign_bulk', 0);
            error_log(print_r($campaign_array,1),0);
            unset($send_pool);
            unset($send_array);
            unset($campaign_array);
            unset($name_array);
            $this->session->set_flashdata('layer_notice', '발송 중 오류가 발생했습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $campaign_array, 39600); //12시간
        unset($send_array);
        unset($campaign_array);
        unset($name_array);

        //remove data that is sent.
        $cache_key = 'sow_elect_remain'.$this->session->userdata('uniqueno');
        $cached_remain_array = $this->cache->redis->get($cache_key);

        if (is_array($trash_array)) {
            $remove_array = array_unique(array_merge($trash_array,$send_pool));
            $new_remain_array = array_diff($cached_remain_array, $remove_array);
        } else {
            $new_remain_array = array_diff($cached_remain_array, $send_pool);
        }
        unset($cached_remain_array);
        $this->cache->redis->save($cache_key, $new_remain_array, 39600); //12시간
        unset($new_remain_array);
        unset($send_pool);

        //session 정보 초기화
        $cache_key = 'session_'.$this->session->userdata('storeno').'_'.$this->session->userdata('userid');
        $this->cache->redis->save($cache_key, '1', 3600); //1시간 자동삭제
        $this->session->set_flashdata('layer_notice', '정상적으로 전송했습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function send_switch() {
        // $_checktime = (int)$this->checktime;
        // if ((int)$this->session->userdata('checktime') >= $_checktime) {
        //     $this->session->set_userdata('checktime', $_checktime);
        //     redirect($_SERVER['HTTP_REFERER']);
        // }
        // $this->session->set_userdata('checktime', (int)($_checktime + 1));
        $this->load->library('form_validation');
        $this->form_validation->set_rules('send_type', 'Send Type', 'required|in_list[1,2,3]');
        $this->form_validation->set_rules('vote_flag', 'Vote Flag', 'required|in_list[1,2]');
        $this->form_validation->set_rules('send_list', 'Send List', 'required');
        $this->form_validation->set_rules('vote_msg_top', 'Vote Msg Top', 'required');
        // $this->form_validation->set_rules('vote_msg_bottom', 'Vote Msg Bottom', 'required');
        $this->form_validation->set_rules('content', 'Content', 'required');
        // $this->form_validation->set_rules('message_body', 'Message Body', 'required');
        // $this->form_validation->set_rules('reserve_yn', 'Reserve YN', 'required|in_list[Y,N]');
        // $this->form_validation->set_rules('divide_yn', 'Divide YN', 'required|in_list[Y,N]');
        $this->form_validation->set_rules('callback', 'Callback', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('layer_notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $send_time = '';
        if ($this->input->post('reserve_yn') == 'Y') {
            $send_time = sprintf('%s %02d:%02d:00',$this->input->post('rsv_date'),(int)$this->input->post('rsv_hour'),(int)$this->input->post('rsv_min'));
            $send_time_unixtime = strtotime($send_time);
            if (time() > $send_time_unixtime) {
                $this->session->set_flashdata('notice', '예약하신 전송시간 오류입니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        $campaign_array = array();
        $campaign_array['tcount'] = 0; //total count
        $campaign_array['bcount'] = 0; //ban count
        $campaign_array['dcount'] = 0; //duplicated count
        // $campaign_array['wcount'] = 0; //wrong count
        $campaign_array['send_type'] = $this->input->post('send_type');
        $campaign_array['subject'] = $this->input->post('subject');
        $campaign_array['callback'] = $this->input->post('callback');
        $campaign_array['send_time'] = $send_time; //YYYY-MM-DD 00:00:00
        $campaign_array['file_cnt'] = 0;
        $campaign_array['file_path_1'] = '';
        $campaign_array['file_path_2'] = '';
        $campaign_array['file_path_3'] = '';
        $campaign_array['msg'] = $this->input->post('content');
        $campaign_array['div_cnt'] = ($this->input->post('divide_yn') == 'Y' ? (int)$this->input->post('div_cnt') : 0);
        $campaign_array['div_min'] = ($this->input->post('divide_yn') == 'Y' ? (int)$this->input->post('div_min') : 0);
        $campaign_array['amount'] = 0;
        $campaign_array['reserve_yn'] = 'N';
        $campaign_array['divide_yn'] = 'N';
        $campaign_array['merge_yn'] = 'Y';
        $campaign_array['ip'] = $this->input->ip_address();
        $campaign_array['storeno'] = $this->session->userdata('storeno');
        $campaign_array['groupno'] = (int)$this->session->userdata('groupno');
        $campaign_array['vote_flag'] = $this->input->post('vote_flag');
        $campaign_array['message_body'] = $this->input->post('message_body')[0];
        $campaign_array['cate_flag'] = $this->input->post('cate_flag');

        if ($this->session->userdata('ad_type') == '1' && strpos($this->input->post('content'), '(광고)') !== 0) {
            $this->session->set_flashdata('notice', '(광고) 표기 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $send_pool = array();
        $send_list = explode(',', $this->input->post('send_list'));
        // $this->load->driver('cache');
        foreach ($send_list as $val) {
            if (!$val) continue;
            //M|sow52340100698|6
            $send_value = explode('|', $val);
            if ($send_value[1] == '') continue;
            if ($send_value[0] != 'P') continue;
            $send_pool[] = strip_phone($send_value[1]);
        }
        unset($send_list);

        $i = 0;
        $name_array = array();
        $excel_list = explode('|,|', $this->input->post('excel_list'));
        foreach ($excel_list as $val) {
            if (!$val) continue;
            //신상인|^|11|^|22|^|33|^|44
            $send_value = explode('|^|', $val);
            $row_mobile = '';
            // $row_mobile = trim(strip_phone($send_value[0]));
            // if (!$row_mobile) continue;
            if ($send_value[0] != '' || $send_value[1] != '' || $send_value[2] != '' || $send_value[3] != '' || $send_value[4] != '') {
                $name_array[$send_pool[$i]][0] = $send_value[0];
                $name_array[$send_pool[$i]][1] = $send_value[1];
                $name_array[$send_pool[$i]][2] = $send_value[2];
                $name_array[$send_pool[$i]][3] = $send_value[3];
                $name_array[$send_pool[$i]][4] = $send_value[4];
            }
            $i++;
        }
        unset($excel_list);

        $bans_array = array();
        if (0 && $this->session->userdata('black_list') == '1') {
            $cache_key = 'sow_black_list';
            $bans_array = $this->cache->redis->get($cache_key);
            $ban_cnt = count($bans_array);
            if (!is_array($bans_array) || !$ban_cnt) {
                $this->session->set_flashdata('notice', '블랙 리스트 설정 오류입니다. 관리자에게 문의하시기 바랍니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $this->load->model('addressModels');
        $result = $this->addressModels->get_bans();
        // $bans_array = array();
        foreach ($result as $row) {
            $bans_array[] = $row->mobile;
        }
        if ($this->session->userdata('phone_080') != '') {
            $result = $this->addressModels->get_phone_080();
            foreach ($result as $row) {
                $bans_array[] = strip_phone(trim($row->mobile));
            }
        }
        //중복제거
        $unique_array = array();
        $unique_array = array_unique($send_pool);
        $fcount = count($unique_array);
        // unset($send_pool);

        //수신거부 차단하기
        $send_array = array_diff($unique_array, $bans_array);
        $scount = count($send_array);
        if (!$scount) {
            error_log(print_r($campaign_array,1),0);
            unset($send_pool);
            unset($send_array);
            unset($name_array);
            unset($campaign_array);
            $this->session->set_flashdata('layer_notice', '전송할 데이타가 존재하지 않습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        unset($bans_array);
        unset($unique_array);
        //send count
        $campaign_array['scount'] = $scount;

        if ($this->input->post('send_type') == '1') {
            $campaign_array['productcode'] = 'SMS2';
            $campaign_array['priority'] = $this->session->userdata('ch_sms');
            $campaign_array['price'] = (float)$this->session->userdata('sms2');
            $campaign_array['bill_mode'] = 'MS';
        } else if ($this->input->post('send_type') == '2') {
            $campaign_array['productcode'] = 'LMS2';
            $campaign_array['priority'] = $this->session->userdata('ch_lms');
            $campaign_array['price'] = (float)$this->session->userdata('lms2');
            $campaign_array['bill_mode'] = 'ML';

            $option = array(
                'priority' => $campaign_array['priority'],
            );
            $this->load->model('smsModels');
            $result = $this->smsModels->get_priority_restrict_merge($option);
            if ($result->xid) {
                $this->session->set_flashdata('notice', '해당 회원의 장문 채널은 내용바꿔 보내기 서비스를 이용할 수 없습니다. 관리자에게 문의하세요.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $campaign_array['productcode'] = 'MMS2';
            $campaign_array['priority'] = $this->session->userdata('ch_mms');
            $campaign_array['price'] = (float)$this->session->userdata('mms2');
            $campaign_array['bill_mode'] = 'MM';
        }
        //금액설정
        $campaign_array['amount'] = ($campaign_array['scount'] * $campaign_array['price']);
        $campaign_array['realamount'] = ($campaign_array['scount'] * $campaign_array['price']);

        if ($campaign_array['amount'] > $this->session->userdata('cash')) {
            unset($send_pool);
            unset($send_array);
            unset($campaign_array);
            unset($name_array);
            $this->session->set_flashdata('layer_notice', '충전 금액이 부족합니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->load->model('smsModels');
        $result = $this->smsModels->add_campaign_bulk($campaign_array,$send_array,$name_array);
        if (!$result) {
            error_log('error add_campaign_bulk', 0);
            error_log(print_r($campaign_array,1),0);
            unset($send_pool);
            unset($send_array);
            unset($campaign_array);
            unset($name_array);
            $this->session->set_flashdata('layer_notice', '발송 중 오류가 발생했습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $campaign_array, 3600);
        unset($send_array);
        unset($campaign_array);
        unset($name_array);

        //remove data that is sent.
        $cache_key = 'sow_elect_remain'.$this->session->userdata('uniqueno');
        $cached_remain_array = $this->cache->redis->get($cache_key);
        $new_remain_array = array_diff($cached_remain_array, $send_pool);
        unset($cached_remain_array);
        $this->cache->redis->save($cache_key, $new_remain_array, 3600);
        unset($new_remain_array);
        unset($send_pool);

        //session 정보 초기화
        $cache_key = 'session_'.$this->session->userdata('storeno').'_'.$this->session->userdata('userid');
        $this->cache->redis->save($cache_key, '1', 3600); //1시간 자동삭제
        $this->session->set_flashdata('layer_notice', '정상적으로 전송했습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function send() {
        if ((int)$this->session->userdata('state') > 0) {
            $this->session->set_flashdata('notice', '해당 사용자는 발송할 권한이 없습니다. 관리자에게 문의하세요.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $_checktime = time();
        if ((int)$this->session->userdata('send_checktime') >= $_checktime) {
            error_log('exit..........send double click');
            $this->session->set_flashdata('notice', '중복 요청으로 마지막 요청은 차단되었습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->session->set_userdata('send_checktime', time()+7);

        $campaign_array = array();
        $send_array = array();
        $name_array = array();
        // $this->load->driver('cache');
        $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        $send_array = $this->cache->redis->get($cache_key);
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $campaign_array = $this->cache->redis->get($cache_key);
        $cache_key = 'sow_name'.$this->session->userdata('uniqueno');
        $name_array = $this->cache->redis->get($cache_key);
        $http_referer = $campaign_array['http_referer'];

        if (!is_array($send_array) || !is_array($campaign_array)) {
            unset($send_array);
            unset($campaign_array);
            unset($name_array);
            error_log(print_r($campaign_array,1),0);
            $this->session->set_flashdata('notice', '전송 중 오류가 발생했습니다.');
            redirect($http_referer);
        }
        if ($campaign_array['amount'] > $this->session->userdata('cash')) {
            unset($send_array);
            unset($campaign_array);
            unset($name_array);
            $this->session->set_flashdata('notice', '충전금액이 부족합니다. 금액을 충전하신 후 이용해 주시기 바랍니다.');
            redirect($http_referer);
        }

        $this->load->model('smsModels');
        $result = $this->smsModels->add_campaign_bulk($campaign_array,$send_array,$name_array);
        if (!$result) {
            unset($send_array);
            unset($campaign_array);
            unset($name_array);
            error_log('error add_campaign_bulk', 0);
            $this->session->set_flashdata('notice', '발송 중 오류가 발생했습니다.');
            redirect($http_referer);
        }
        // $send_cnt = count($campaign_array['tcount']);
        // $reserve_time = ($campaign_array['send_time'] != '' ? $campaign_array['send_time'] : $this->current_time);

        //session 정보 초기화
        $cache_key = 'session_'.$this->session->userdata('storeno').'_'.$this->session->userdata('userid');
        $this->cache->redis->save($cache_key, '1', 3600); //1시간 자동삭제
        unset($send_array);
        unset($campaign_array);
        unset($name_array);
        $this->session->set_flashdata('notice', '정상적으로 전송했습니다.');
        // if ($send_cnt > 0) {
        //     $this->load->helper('api');
        //     $msg = '[문자발송] ID:'.$this->session->userdata('userid').' 발송시간:'.$reserve_time.' 건수:'.$send_cnt.' 발송';
        //     noti_message($msg);
        // }
        redirect($http_referer);
    }
    public function confirm() {
        // $check_hour = date('H');
        // if ($check_hour >= 19) {
        //     $this->session->set_flashdata('notice', '서비스 점검 중입니다.');
        //     redirect($_SERVER['HTTP_REFERER']);
        // }
        if ($this->session->userdata('level') < 9 && defined('RESTRICT_SENDING_TIME_YN') && RESTRICT_SENDING_TIME_YN == 'Y') {
            $check_hour = date('H');
            $s_hour = (int)SENDING_TIME_START_HOUR;
            $e_hour = (int)SENDING_TIME_END_HOUR;
            if (!($check_hour >= $s_hour && $check_hour < $e_hour)) {
                $this->session->set_flashdata('notice', '문자 발송 가능 시간은 '.$s_hour.':00 ~ '.$e_hour.':00 입니다. 시간을 확인해 주십시오.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        // $this->load->driver('cache');
        // $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        // $data['send_array'] = $this->cache->redis->get($cache_key);
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $campaign_array = $this->cache->redis->get($cache_key);
        if (!is_array($campaign_array)) redirect($_SERVER['HTTP_REFERER']);
        // if ($campaign_array['vote_flag'] == '1') {
        //     $this->session->set_flashdata('notice', '광고성 문자일 경우\n\'광고 문자 수신 거부 의무화 정책\'으로 \n\'광고\' 문구를 시작되는 부분에 삽입해야 합니다.');
        // }

        $tcount = (int)$campaign_array['tcount'];
        if ($tcount > 50000) {
            $this->session->set_flashdata('notice', '1회 발송 한도 50,000 건을 초과하셨습니다. ');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $send_type = $campaign_array['send_type'];
        if ($send_type == '1') {
            if ($this->session->userdata('ch_sms') == '0') {
                $this->session->set_flashdata('notice', '단문 발송 채널이 설정되지 않았습니다. 관리자에게 문의 하십시오.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else if ($send_type == '2') {
            if ($this->session->userdata('ch_lms') == '0') {
                $this->session->set_flashdata('notice', '장문 발송 채널이 설정되지 않았습니다. 관리자에게 문의 하십시오.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else if ($send_type == '3') {
            if ($this->session->userdata('ch_mms') == '0') {
                $this->session->set_flashdata('notice', '포토 발송 채널이 설정되지 않았습니다. 관리자에게 문의 하십시오.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $callback = trim($campaign_array['callback']);
        $option = array(
            'callback' => $callback,
            'status' => '3',
        );

        $cb_array = array();
        $this->load->model('userModels');

        $filter_array = $this->userModels->get_filter_word();
        if ($filter_array['word'] != '') {
            $filter = explode(",", trim($filter_array['word']));
            foreach($filter as $val) {
                if (stripos($campaign_array['message_body'], $val) !== false) {
                    $this->session->set_flashdata('notice', '문자 내용에 금지단어가 포함되어 있습니다');
                    redirect($_SERVER['HTTP_REFERER']);
                }
            } 
        }

        $cb_array = $this->userModels->get_callback_by_userno_callbackno($option);
        if ($cb_array['callback'] == '') {
            $this->session->set_flashdata('notice', '유효한 발신번호가 아닙니다. 관리자에게 문의하십시오.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $user_info = $this->userModels->get_user_by_userno();
        if ($user_info->state != '0') {
            $this->session->set_flashdata('notice', '발송권한이 없는 회원입니다. 관리자에게 문의하십시오.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $priority = $campaign_array['priority'];
        $this->load->model('smsModels');
        $data['channel'] = $this->smsModels->get_channel($priority);

        $data['campaign_array'] = $campaign_array;
        $this->load->view('templates/header');
        $this->load->view('send/confirm', $data);
        $this->load->view('templates/footer');
    }
}
