<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Sms extends CI_Controller {
    private $current_time;
    private $rows_per_page;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        $this->rows_per_page = 100;
        $this->load->helper(array('form','url','phone','cookie'));
        initialize_session_userdata($this);
    }
    public function add_photo() {
        $this->session->set_userdata('csrf_sowkorea_name', $this->security->get_csrf_hash());
        $date = date('Ym');
        $file_path = FCPATH.'uploads/'.$date;
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, TRUE);
        }
        $file_name = $this->session->userdata('uniqueno').date('is');

        $config['upload_path'] = $file_path;
        $config['file_name'] = $file_name;
        $config['allowed_types'] = 'jpg';
        $config['max_size'] = 5120; // 5M
        // $config['max_width'] = 1024;
        // $config['max_height'] = 1024;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('photo_file')) {
            error_log(print_r($this->upload->display_errors(),1),0);
            $this->session->set_flashdata('notice', '이미지 등록 중 오류가 발생했습니다.');
            redirect('/sms/photo_popup');
        }
        $upload_info = $this->upload->data();

        $config['image_library'] = 'gd2';
        $config['source_image'] = $file_path.'/'.$file_name.$upload_info['file_ext'];
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width'] = 640;
        $config['height'] = 960;
        $config['quality'] = 60;

        $this->load->library('image_lib', $config);
        if (!$this->image_lib->resize()) {
            error_log(print_r($this->image_lib->display_errors(),1),0);
            $this->session->set_flashdata('notice', '이미지 등록 중 오류가 발생했습니다.');
            redirect('/sms/photo_popup');
        }
        $photo_array = array(
            'file_path' => $file_path.'/'.$file_name.'_thumb'.$upload_info['file_ext'],
            'image_path' => '/'.str_replace(FCPATH,'',$file_path.'/'.$file_name.'_thumb'.$upload_info['file_ext']),
        );

        // $this->load->driver('cache');
        $cache_key = 'sow_photo'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, $photo_array, 1800); //30분 후 자동삭제
        $this->session->set_flashdata('notice', '정상적으로 등록되었습니다.');
        redirect('/sms/photo_popup');
    }
    public function photo_popup() {
        // $this->load->driver('cache');
        $cache_key = 'sow_photo'.$this->session->userdata('uniqueno');
        $data['photo_array'] = $this->cache->redis->get($cache_key);
        $this->load->view('sms/photo_popup', $data);
    }
    public function sms() {
        if ($this->session->userdata('ad_type') != '0') {
            $this->session->set_flashdata('notice', '해당 서비스는 사용할 권한이 없습니다. 관리자에게 문의하세요.');
            redirect('/sms/adsms');
        }

        $data['svc'] = $this->uri->segment(3);
        // $this->load->driver('cache');
        $cache_key = 'sow_name'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        //선거용구분
        $this->session->set_userdata('elect', false);

        if (SENT_MEMORY_YN == 'Y') {
            $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
            $data['cached_campaign_array'] = $this->cache->redis->get($cache_key);
        }

        $this->load->view('templates/header');
        $this->load->view('sms/sms',$data);
        $this->load->view('templates/footer');
    }
    public function adsms() {
        $data['svc'] = $this->uri->segment(3);
        // $this->load->driver('cache');
        $cache_key = 'sow_name'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        //선거용구분
        $this->session->set_userdata('elect', false);

        if (SENT_MEMORY_YN == 'Y') {
            $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
            $data['cached_campaign_array'] = $this->cache->redis->get($cache_key);
        }

        $this->load->view('templates/header');
        $this->load->view('sms/adsms',$data);
        $this->load->view('templates/footer');
    }
    public function adswitch() {
        $data['svc'] = $this->uri->segment(3);
        // $this->load->driver('cache');
        $cache_key = 'sow_name'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        //선거용구분
        $this->session->set_userdata('elect', false);

        if (SENT_MEMORY_YN == 'Y') {
            $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
            $data['cached_campaign_array'] = $this->cache->redis->get($cache_key);
        }

        $this->load->view('templates/header');
        $this->load->view('sms/adswitch',$data);
        $this->load->view('templates/footer');
    }
    public function adtext() {
        $data['svc'] = $this->uri->segment(3);
        // $this->load->driver('cache');
        $cache_key = 'sow_name'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        //선거용구분
        $this->session->set_userdata('elect', false);

        if (SENT_MEMORY_YN == 'Y') {
            $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
            $data['cached_campaign_array'] = $this->cache->redis->get($cache_key);
        }

        $this->load->view('templates/header');
        $this->load->view('sms/adtext',$data);
        $this->load->view('templates/footer');
    }
    public function adfile() {
        $data['svc'] = $this->uri->segment(3);
        // $this->load->driver('cache');
        $cache_key = 'sow_name'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        //선거용구분
        $this->session->set_userdata('elect', false);

        if (SENT_MEMORY_YN == 'Y') {
            $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
            $data['cached_campaign_array'] = $this->cache->redis->get($cache_key);
        }

        $this->load->view('templates/header');
        $this->load->view('sms/adfile',$data);
        $this->load->view('templates/footer');
    }
    public function adexcel() {
        $data['svc'] = $this->uri->segment(3);
        // $this->load->driver('cache');
        $cache_key = 'sow_name'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        //선거용구분
        $this->session->set_userdata('elect', false);

        if (SENT_MEMORY_YN == 'Y') {
            $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
            $data['cached_campaign_array'] = $this->cache->redis->get($cache_key);
        }

        $this->load->view('templates/header');
        $this->load->view('sms/adexcel',$data);
        $this->load->view('templates/footer');
    }
    public function photo() {
        if ($this->session->userdata('ad_type') != '0') {
            $this->session->set_flashdata('notice', '해당 서비스는 사용할 권한이 없습니다. 관리자에게 문의하세요.');
            redirect('/sms/adsms');
        }

        $data['svc'] = $this->uri->segment(3);
        // $this->load->driver('cache');
        $cache_key = 'sow_name'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_photo'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        //선거용구분
        $this->session->set_userdata('elect', false);

        if (SENT_MEMORY_YN == 'Y') {
            $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
            $data['cached_campaign_array'] = $this->cache->redis->get($cache_key);
        }

        $this->load->view('templates/header');
        $this->load->view('sms/photo', $data);
        $this->load->view('templates/footer');
    }
    public function adphoto() {
        $data['svc'] = $this->uri->segment(3);
        // $this->load->driver('cache');
        $cache_key = 'sow_name'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_photo'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        //선거용구분
        $this->session->set_userdata('elect', false);

        if (SENT_MEMORY_YN == 'Y') {
            $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
            $data['cached_campaign_array'] = $this->cache->redis->get($cache_key);
        }

        $this->load->view('templates/header');
        $this->load->view('sms/adphoto', $data);
        $this->load->view('templates/footer');
    }
    public function switch() {
        if ($this->session->userdata('ad_type') != '0') {
            $this->session->set_flashdata('notice', '해당 서비스는 사용할 권한이 없습니다. 관리자에게 문의하세요.');
            redirect('/sms/adsms');
        }

        $data['svc'] = $this->uri->segment(3);
        // $this->load->driver('cache');
        $cache_key = 'sow_name'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        //선거용구분
        $this->session->set_userdata('elect', false);

        if (SENT_MEMORY_YN == 'Y') {
            $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
            $data['cached_campaign_array'] = $this->cache->redis->get($cache_key);
        }

        $this->load->view('templates/header');
        $this->load->view('sms/switch',$data);
        $this->load->view('templates/footer');
    }
    public function newsms() {
        if ($this->session->userdata('ad_type') != '0') {
            $this->session->set_flashdata('notice', '해당 서비스는 사용할 권한이 없습니다. 관리자에게 문의하세요.');
            redirect('/sms/adsms');
        }

        // $this->load->driver('cache');
        // $this->load->helper('cache');
        check_elect_cached($this->session, $this->cache->redis);
        $cache_key = 'sow_elect_total_count'.$this->session->userdata('uniqueno');
        $data['tcount'] = (int)$this->cache->redis->get($cache_key);
        $cache_key = 'sow_elect_remain'.$this->session->userdata('uniqueno');
        $cached_remain_array = $this->cache->redis->get($cache_key);
        $data['rcount'] = (is_array($cached_remain_array) ? count($cached_remain_array) : 0);
        $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
        $data['cached_campaign_array'] = $this->cache->redis->get($cache_key);

        $data['cached_remain_array'] = array();
        if (is_array($cached_remain_array)) {
            $i = 0;
            foreach($cached_remain_array as $val) {
                if ($i >= 20) break;
                $data['cached_remain_array'][] = $val;
                $i++;
            }
        }
        unset($cached_remain_array);
        $data['svc'] = 'elect';
        $this->session->set_userdata('elect', true);

        $this->load->view('templates/header');
        $this->load->view('sms/newsms', $data);
        $this->load->view('templates/footer');
    }
    public function newphoto() {
        if ($this->session->userdata('ad_type') != '0') {
            $this->session->set_flashdata('notice', '해당 서비스는 사용할 권한이 없습니다. 관리자에게 문의하세요.');
            redirect('/sms/adsms');
        }

        // $this->load->driver('cache');
        // $this->load->helper('cache');
        check_elect_cached($this->session, $this->cache->redis);
        $cache_key = 'sow_photo'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);

        $cache_key = 'sow_elect_total_count'.$this->session->userdata('uniqueno');
        $data['tcount'] = (int)$this->cache->redis->get($cache_key);
        $cache_key = 'sow_elect_remain'.$this->session->userdata('uniqueno');
        $cached_remain_array = $this->cache->redis->get($cache_key);
        $data['rcount'] = (is_array($cached_remain_array) ? count($cached_remain_array) : 0);
        $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
        $data['cached_campaign_array'] = $this->cache->redis->get($cache_key);

        $data['cached_remain_array'] = array();
        if (is_array($cached_remain_array)) {
            $i = 0;
            foreach($cached_remain_array as $val) {
                if ($i >= 20) break;
                $data['cached_remain_array'][] = $val;
                $i++;
            }
        }
        unset($cached_remain_array);
        $data['svc'] = 'elect';
        $this->session->set_userdata('elect', true);
        $this->load->view('templates/header');
        $this->load->view('sms/newphoto', $data);
        $this->load->view('templates/footer');
    }
    public function newswitch() {
        if ($this->session->userdata('ad_type') != '0') {
            $this->session->set_flashdata('notice', '해당 서비스는 사용할 권한이 없습니다. 관리자에게 문의하세요.');
            redirect('/sms/adsms');
        }

        // $this->load->driver('cache');
        // $this->load->helper('cache');
        check_elect_cached($this->session, $this->cache->redis);

        $cache_key = 'sow_elect_total_count'.$this->session->userdata('uniqueno');
        $data['tcount'] = (int)$this->cache->redis->get($cache_key);
        $cache_key = 'sow_elect_remain'.$this->session->userdata('uniqueno');
        $cached_remain_array = $this->cache->redis->get($cache_key);
        $data['rcount'] = (is_array($cached_remain_array) ? count($cached_remain_array) : 0);
        $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
        $data['cached_campaign_array'] = $this->cache->redis->get($cache_key);

        $data['cached_remain_array'] = array();
        if (is_array($cached_remain_array)) {
            $i = 0;
            foreach($cached_remain_array as $val) {
                if ($i >= 20) break;
                $data['cached_remain_array'][] = $val;
                $i++;
            }
        }
        unset($cached_remain_array);
        $this->session->set_userdata('elect', true);
        $data['svc'] = 'elect';
        $this->load->view('templates/header');
        $this->load->view('sms/newswitch', $data);
        $this->load->view('templates/footer');
    }
    public function delete_smsg() {
        header("Content-Type: application/json");
        // if ($this->session->userdata('checktime')+2 >= date('ymdHis')) {
        //     $data_json['result'] = 'error';
        //     $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        //     // $data_json['message'] = '더블클릭 방지로 제한되었습니다.';
        //     $data_json['message'] = '';
        //     exit (json_encode($data_json));
        // }
        // $this->session->set_userdata('checktime', date('ymdHis'));

        $this->load->library('form_validation');
        $this->form_validation->set_rules('chk_nums', 'Delete List', 'required');
        $this->form_validation->set_rules('where', 'Auth', 'required|in_list[sms]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }

        $smsg_arr = explode(',', $this->input->post('chk_nums'));
        $this->load->model('smsModels');
        $result = $this->smsModels->delete_saved_message($smsg_arr);
        if (!$result) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '처리과정 중 오류가 발생했습니다.';
            exit (json_encode($data_json));
        }
        $data_json['result'] = 'success';
        $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        $data_json['message'] = '정상적으로 처리했습니다.';
        exit (json_encode($data_json));
    }
    public function msg_saved() {
        if ($this->input->get('sv')) { //검색
            $sv = urldecode($this->input->get('sv'));
            $offset = 0;
            $sf = '1';
        } else {
            $sf = (int)$this->uri->segment(3);
            $sv = ($sf == '1' ? urldecode($this->uri->segment(4)) : '');
            $offset =  (int)($sf == '1' ? $this->uri->segment(5) : $this->uri->segment(4));
        }

        $config['base_url'] = "/sms/msg_saved/{$sf}/{$sv}/";
        $config['per_page'] = 6;

        $option = array(
            'val' => $sv,
            'limit' => 6,
            'offset' => $offset,
            'send_type' => array('1','2'),
        );
        $this->load->model('smsModels');
        $total_rows = (int)$this->smsModels->get_saved_msg_count($option);
        $config['total_rows'] = $total_rows;
        $data['result'] = $this->smsModels->get_saved_msg_limit($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['sf'] = $sf;
        $data['sv'] = $sv;
        $data['gno'] = $sg;
        $data['total_rows'] = $total_rows;
        $this->load->helper('mydate');
        $this->load->view('sms/msg_saved',$data);
    }
    public function photo_saved() {
        if ($this->input->get('sv')) { //검색
            $sv = urldecode($this->input->get('sv'));
            $offset = 0;
            $sf = '1';
        } else {
            $sf = (int)$this->uri->segment(3);
            $sv = ($sf == '1' ? urldecode($this->uri->segment(4)) : '');
            $offset =  (int)($sf == '1' ? $this->uri->segment(5) : $this->uri->segment(4));
        }

        $config['base_url'] = "/sms/photo_saved/{$sf}/{$sv}/";
        $config['per_page'] = 3;

        $option = array(
            'val' => $sv,
            'limit' => 3,
            'offset' => $offset,
            'send_type' => '3',
        );
        $this->load->model('smsModels');
        $total_rows = (int)$this->smsModels->get_saved_msg_count($option);
        $config['total_rows'] = $total_rows;
        $data['result'] = $this->smsModels->get_saved_msg_limit($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['sf'] = $sf;
        $data['sv'] = $sv;
        $data['gno'] = $sg;
        $data['total_rows'] = $total_rows;

        $this->load->helper('mydate');
        $this->load->view('sms/photo_saved',$data);
    }
    public function save_msg() {
        header("Content-Type: application/json");
        // if ($this->session->userdata('checktime')+2 >= date('ymdHis')) {
        //     $data_json['result'] = 'error';
        //     $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        //     // $data_json['message'] = '더블클릭 방지로 제한되었습니다.';
        //     $data_json['message'] = '';
        //     exit (json_encode($data_json));
        // }
        // $this->session->set_userdata('checktime', date('ymdHis'));
        $this->load->library('form_validation');
        if ($this->input->post('send_type') != '3') {
            $this->form_validation->set_rules('message_body', 'MessageBody', 'required');
        }
        $this->form_validation->set_rules('where', 'Where', 'required');
        $this->form_validation->set_rules('bytes', 'Byte', 'required');
        $this->form_validation->set_rules('send_type', 'Send type', 'required|in_list[1,2,3]');
        if ($this->input->post('send_type') == '3') {
            $this->form_validation->set_rules('photo_image', 'Photo Image', 'required');
        }
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }
        // $msg_legnth = mb_strlen($this->input->post('message_body'),'euc-kr');
        // $msg_legnth = mb_strlen($this->input->post('message_body'),'CP949');
        // $msg_legnth = iconv_strlen($this->input->post('message_body'),'euc-kr');
        // $msg_legnth = mb_strlen($this->input->post('message_body'),'utf-8');
        // $msg_legnth = mb_strwidth($this->input->post('message_body'),'utf-8');

        $messages['subject'] = ($this->input->post('subject') == '' ? '제목없음' : $this->input->post('subject'));
        $messages['msg'] = $this->input->post('message_body');
        $messages['send_type'] = $this->input->post('send_type');
        $messages['bytes'] = $this->input->post('bytes');
        $messages['photo_image'] = $this->input->post('photo_image');

        $this->load->model('smsModels');
        if (!$this->smsModels->save_msg($messages)) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '시스템 오류입니다.';
            exit (json_encode($data_json));
        }
        $data_json['result'] = 'success';
        $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        $data_json['message'] = '정상적으로 처리되었습니다.';
        exit (json_encode($data_json));
    }
    public function sms_excel() {
        $cache_key = 'sow_elect_remain'.$this->session->userdata('uniqueno');
        $cached_remain_array = $this->cache->redis->get($cache_key);

        $cache_key = 'sow_elect_name'.$this->session->userdata('uniqueno');
        $cached_name_array = $this->cache->redis->get($cache_key);

        $excel_fields = array(
            '전화번호',
            '이름'
        );
        $dkey = 0;
        $excel_data = array();
        foreach ($cached_remain_array as $row) {
            $excel_data[$dkey][0] = $row;
            $excel_data[$dkey][1] = $cached_name_array[$row][0];
            $dkey ++;
        }

        // 라이브러리를 로드한다.
        $this->load->library('excel',$params);
        // 시트를 지정한다.
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Sheet1');

        // 첫 줄에 필드명을 기록한다.
        // $fields = $this->read_field_names();
        $col = 0;
        foreach ($excel_fields as $field_name) {
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field_name);
            $col ++;
        }

        $max_cols = count($excel_fields);
        // 데이터를 읽어서 순차로 기록한다.
        // $records = $this->read_table_records();
        // $row = 2;
        foreach ($excel_data as $ekey => $eval) {
            foreach ($eval as $col => $val) {
                $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, ($ekey+2), $val);
            }
            // $row ++;
        }

        // 메모리제한 늘리기
        // ini_set('memory_limit','512M');
        ini_set('memory_limit','256M');

        // 파일로 내보낸다.
        $filename = 'sms_'.date('ymdHi');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        // Excel5 포맷(excel 2003 .XLS file)으로 저장한다.
        // 두 번째 매개변수를 'Excel2007'로 바꾸면 Excel 2007 .XLSX 포맷으로 저장한다.
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //출력버퍼를 지우고 출력 버퍼링을 종료
        ob_end_clean();
        //출력 버퍼링 시작
        ob_start();
        // 이용자가 다운로드하여 컴퓨터 HD에 저장하도록 강제한다.
        $objWriter->save('php://output');
    }
}
