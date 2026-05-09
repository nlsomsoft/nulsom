<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admuser extends CI_Controller {
    private $current_time;
    private $rows_per_page;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        $this->rows_per_page = 100;
        $this->load->helper(array('form','url','phone','mydate','bill'));
        initialize_session_userdata($this,true);
    }
    public function sessdata() {
        error_log(print_r($this->session->all_userdata(), 1), 0);
    }
    public function users() {
        $this->load->helper(array('cookie'));
        if ($this->input->get('stx')) { //검색
            $sfl = $this->input->get('sfl');
            $stx = trim($this->input->get('stx'));
            if ($sfl == 'mobile') $stx = str_replace('-', '', $stx);
            $offset = 0;
        } else {
            $sfl = '000';
            $stx = '000';
            $offset =  (int)$this->uri->segment(5);
        }
        $config['base_url'] = "/admuser/users/{$sfl}/{$stx}";
        $config['per_page'] = $this->rows_per_page;

        $this->load->model('userModels');
        $option = array();
        $data['total_rows'] = (int)$this->userModels->get_users_count($option);
        $option = array(
            'state' => '0',
        );
        $data['total_rows1'] = (int)$this->userModels->get_users_count($option);
        $option = array(
            'state' => '3',
        );
        $data['total_rows2'] = (int)$this->userModels->get_users_count($option);

        $cash_info = array();
        if ($_SERVER['STORENAME'] == 'aone') {
            $option = array();
            $cash_info['total'] = (int)$this->userModels->get_users_cash_by_admin($option);
            $option = array('ch_sms' => '150');
            $cash_info['150'] = (int)$this->userModels->get_users_cash_by_admin($option);
        }
        $data['cash_info'] = $cash_info;

        $option = array(
            'sfl' => ($sfl == '000' ? '' : $sfl),
            'stx' => ($stx == '000' ? '' : $stx),
            'limit' => $this->rows_per_page,
            'offset' => $offset,
            'state' => array('0','3'),
        );

        if ($sfl == 'birth_day') {
            $count_result = $this->userModels->get_users_birthday_count_by_admin_new($option);
            $config['total_rows'] = (int)$count_result->cnt;
            $data['result'] = $this->userModels->get_users_birthday_limit_by_admin_new($option);
        } else {
            // $config['total_rows'] = (int)$this->userModels->get_users_count_by_admin($option);
            // $data['result'] = $this->userModels->get_users_limit_by_admin($option);
            $config['total_rows'] = (int)$this->userModels->get_users_count_by_admin_new($option);
            $data['result'] = $this->userModels->get_users_limit_by_admin_new($option);
        }

        if ((int)$this->session->userdata('level') > 3) {
            if (0 && $_SERVER['SERVER_DEVICE'] != 'unisms' || ($_SERVER['SERVER_DEVICE'] == 'unisms' && $_SERVER['STORENAME'] == 'unisms')) {
                $data['admin_balance'] = $this->userModels->get_admin_balance();
            }
        }

        $this->load->model('resultModels');
        $channel = $this->resultModels->get_channel($option = array());
        $channel_info = array('0' => '채널없음');
        foreach ($channel as $row) {
            $channel_info[$row->channel] = $row->channel_exp;
        }
        $data['channel_info'] = $channel_info;

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['sfl'] = ($sfl == '000' ? '' : $sfl);
        $data['stx'] = ($stx == '000' ? '' : $stx);
        $this->load->view('templates/adm_header');
        $this->load->view('admuser/users', $data);
        $this->load->view('templates/adm_footer');
    }
    public function block_users() {
        $this->load->helper(array('cookie'));
        if ($this->input->get('stx')) { //검색
            $sfl = $this->input->get('sfl');
            $stx = trim($this->input->get('stx'));
            if ($sfl == 'mobile') $stx = str_replace('-', '', $stx);
            $offset = 0;
        } else {
            $sfl = '000';
            $stx = '000';
            $offset =  (int)$this->uri->segment(5);
        }
        $config['base_url'] = "/admuser/block_users/{$sfl}/{$stx}";
        $config['per_page'] = $this->rows_per_page;

        $this->load->model('userModels');
        $option = array();
        $data['total_rows'] = (int)$this->userModels->get_users_count($option);
        $option = array(
            'state' => '1',
        );
        $data['total_rows1'] = (int)$this->userModels->get_users_count($option);
        $option = array(
            'state' => '2',
        );
        $data['total_rows2'] = (int)$this->userModels->get_users_count($option);

        $option = array(
            'sfl' => ($sfl == '000' ? '' : $sfl),
            'stx' => ($stx == '000' ? '' : $stx),
            'limit' => $this->rows_per_page,
            'offset' => $offset,
            'state' => array('1','2'),
            'order' => 'a.remove_date',
        );

        if ($sfl == 'birth_day') {
            $count_result = $this->userModels->get_users_birthday_count_by_admin_new($option);
            $config['total_rows'] = (int)$count_result->cnt;
            $data['result'] = $this->userModels->get_users_birthday_limit_by_admin_new($option);
        } else {
            // $config['total_rows'] = (int)$this->userModels->get_users_count_by_admin($option);
            // $data['result'] = $this->userModels->get_users_limit_by_admin($option);
            $config['total_rows'] = (int)$this->userModels->get_users_count_by_admin_new($option);
            $data['result'] = $this->userModels->get_users_limit_by_admin_new($option);
        }

        if ((int)$this->session->userdata('level') > 3) {
            if (0 && $_SERVER['SERVER_DEVICE'] != 'unisms' || ($_SERVER['SERVER_DEVICE'] == 'unisms' && $_SERVER['STORENAME'] == 'unisms')) {
                $data['admin_balance'] = $this->userModels->get_admin_balance();
            }
        }

        $this->load->model('resultModels');
        $channel = $this->resultModels->get_channel($option = array());
        $channel_info = array('0' => '채널없음');
        foreach ($channel as $row) {
            $channel_info[$row->channel] = $row->channel_exp;
        }
        $data['channel_info'] = $channel_info;

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['sfl'] = ($sfl == '000' ? '' : $sfl);
        $data['stx'] = ($stx == '000' ? '' : $stx);
        $this->load->view('templates/adm_header');
        $this->load->view('admuser/block_users', $data);
        $this->load->view('templates/adm_footer');
    }
    public function group_list() {
        auth_session_userdata($this, 3);
        if ($this->input->get('stx')) { //검색
            $stx = $this->input->get('stx');
            $offset = 0;
        } else {
            $stx = '000';
            $offset =  (int)$this->uri->segment(4);
        }
        $config['base_url'] = "/admuser/grlist/{$stx}";
        $config['per_page'] = $this->rows_per_page;

        $this->load->model('userModels');
        $config['total_rows'] = (int)$this->userModels->get_group_count_by_admin();
        $data['result'] = $this->userModels->get_group_by_admin();
        $data['store_list'] = $this->userModels->get_store_by_admin();

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['stx'] = ($stx == '000' ? '' : $stx);
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admuser/group_list', $data);
        $this->load->view('templates/adm_footer');
    }
    public function group_auth() {
        auth_session_userdata($this,5);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_storeno', 'Ipt storeno', 'required');
        $this->form_validation->set_rules('ipt_groupid', 'Ipt groupid', 'required|is_unique[group.groupid]');
        $this->form_validation->set_rules('ipt_userid', 'Ipt userid', 'required');
        $this->form_validation->set_rules('ipt_group_name', 'ipt_group_name', 'required');
        $this->form_validation->set_rules('ipt_type', 'ipt_type', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option = array(
            'storeno'  => $this->input->post('ipt_storeno'),
            'groupid'  => $this->input->post('ipt_groupid'),
            'group_name'  => $this->input->post('ipt_group_name'),
            'userid'   => $this->input->post('ipt_userid'),
            'phone'    => $this->input->post('ipt_phone'),
            'type'    => (int)$this->input->post('ipt_type'),
        );
        $this->load->model('userModels');
        $result = $this->userModels->add_group($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function block() {
        auth_session_userdata($this,5); // < level

        $this->load->library('form_validation');
        $this->form_validation->set_rules('act_button', 'act button', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option1 = $this->input->post('chk');
        if (!is_array($option1)) {
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->load->model('userModels');
        $option = array(
            'remove_date' => $this->current_time,
            'state' => ($this->input->post('act_button') == '차단하기' ? '1' : '2'),
        );
        $result = $this->userModels->modify_user_by_query_by_admin($option,$option1);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $result = $this->userModels->get_userid_by_userno_for_session($option1);
        foreach ($result as $row) {
            //session 정보 초기화
            $cache_key = 'session_'.$row->storeno.'_'.$row->userid;
            $this->cache->redis->save($cache_key, '1', 3600); //1시간 자동삭제
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function detail() {
        auth_session_userdata($this, 3);
        $userno = (int)$this->uri->segment(3);
        $option = array(
            'userno' => $userno,
        );
        $this->load->model('userModels');
        $data['result'] = $this->userModels->get_user_by_admin($option);
        if ((int)$this->session->userdata('level') < (int)$data['result']->level) {
            $this->session->set_flashdata('notice', '해당 회원 정보는 수정할 권한이 없습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ((int)$this->session->userdata('level') > 5) {
            if ((int)$data['result']->level != 9) {
                $this->session->set_flashdata('notice', '사용권한이 없습니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $data['user_callback'] = $this->userModels->get_user_callback($option);
        $data['store_list'] = $this->userModels->get_store_by_admin();
        $data['group_list'] = $this->userModels->get_group_by_admin();
        $option1 = array(
            'userid' => $data['result']->userid
        );
        // $data['rinfo'] = $this->userModels->get_register_info_by_admin($option1);
        $data['auth_info'] = $this->userModels->get_auth_userinfo_by_agent($option1);

        $this->load->model('resultModels');
        $option2 = array(
            'status' => '1'
        );
        $result = $this->resultModels->get_channel($option2);
        $data['channel'] = array();
        foreach ($result as $row) {
            $data['channel'][$row->type][$row->channel] = $row->channel_exp;
        }

        $option = array(
            'userno' => $userno,
        );
        $data['ban_list'] = $this->userModels->get_ban_list_by_admin($option);

        $this->load->view('templates/adm_header');
        $this->load->view('admuser/detail', $data);
        $this->load->view('templates/adm_footer');
    }
    public function newpasswd() {
        auth_session_userdata($this, 5);

        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_pass_storeno', 'ipt_pass_storeno', 'trim|required');
        $this->form_validation->set_rules('ipt_pass_groupno', 'ipt_pass_groupno', 'trim|required');
        $this->form_validation->set_rules('ipt_pass_userno', 'ipt_pass_userno', 'trim|required');
        $this->form_validation->set_rules('ipt_pass_userid', 'ipt_pass_userid', 'trim|required');
        $this->form_validation->set_rules('ipt_password', 'ipt_password', 'trim|required');
        
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option1 = array();
        $option1[0] = $this->input->post('ipt_pass_userno');

        $ipt_password = trim($this->input->post('ipt_password'));
        if ($ipt_password == '') {
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $option = array();
        $option['password'] = password_hash($ipt_password, PASSWORD_BCRYPT);
        

        $this->load->model('userModels');
        $result = $this->userModels->get_user_by_userno();
        $db_hash = trim($result->password);

        $option2 = array(
            'userno' => $this->input->post('ipt_pass_userno'),
            'userid' => $this->input->post('ipt_pass_userid'),
            'password' => $db_hash,
            'type' => '1',
            'ip' => $this->input->ip_address()
        );
        $result2 = $this->userModels->record_password_history($option2);
        if (!$result2) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $result = $this->userModels->modify_user_by_admin($option,$option1);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        //session 정보 초기화
        $cache_key = 'session_'.$this->input->post('ipt_pass_storeno').'_'.$this->input->post('ipt_pass_userid');
        $this->cache->redis->save($cache_key, '1', 3600); //1시간 자동삭제

        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function update() {
        auth_session_userdata($this, 5);

        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_storeno', 'Ipt Storeno', 'trim|numeric|required');
        $this->form_validation->set_rules('ipt_groupno', 'Ipt Groupno', 'trim|numeric|required');
        $this->form_validation->set_rules('ipt_userno', 'Ipt Userno', 'trim|required');
        $this->form_validation->set_rules('ipt_userid', 'Ipt Userid', 'trim|required');
        // $this->form_validation->set_rules('ipt_realname', 'Ipt Realname', 'trim|required');
        $this->form_validation->set_rules('ipt_mobile', 'Ipt Mobile', 'trim');
        $this->form_validation->set_rules('ipt_email', 'Ipt Email', 'trim');
        $this->form_validation->set_rules('ipt_user_type', 'Ipt User type', 'trim|required');
        $this->form_validation->set_rules('ipt_remove_date', 'ipt_remove_date', 'trim|required');
        $this->form_validation->set_rules('rd_paytype_value', 'Radio Pay type', 'trim|required|in_list[0,1]');
        $this->form_validation->set_rules('rd_adtype_value', 'Radio Ad type', 'trim|required|in_list[0,1]');
        // $this->form_validation->set_rules('rd_blacklist_value', 'Radio BlackList', 'trim|required|in_list[0,1]');
        $this->form_validation->set_rules('rd_pg_value', 'Radio Pg type', 'trim|required|in_list[N,Y]');
        $this->form_validation->set_rules('ipt_level', 'Ipt Level', 'trim|numeric|required');
        $this->form_validation->set_rules('ipt_sms1', 'Ipt sms1', 'trim|numeric|required');
        $this->form_validation->set_rules('ipt_lms1', 'Ipt lms1', 'trim|numeric|required');
        $this->form_validation->set_rules('ipt_mms1', 'Ipt mms1', 'trim|numeric|required');

        if ((int)$this->session->userdata('level') >= 5) {
            $this->form_validation->set_rules('ipt_ch_sms', 'Ipt ch_sms', 'trim|is_natural|required');
            $this->form_validation->set_rules('ipt_ch_lms', 'Ipt ch_lms', 'trim|is_natural|required');
            $this->form_validation->set_rules('ipt_ch_mms', 'Ipt ch_mms', 'trim|is_natural|required');
        }
        if ($this->input->post('ipt_user_type') == '2') {
            $this->form_validation->set_rules('ipt_com_name', 'Ipt Com name', 'trim|required');
            $this->form_validation->set_rules('ipt_com_number', 'Ipt Com number', 'trim|required');
        }
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $ipt_mobile = strip_phone($this->input->post('ipt_mobile'));
        $ipt_phone = strip_phone($this->input->post('ipt_phone'));
        $ipt_phone_080 = strip_phone($this->input->post('ipt_phone_080'));

        $chk_deny_value = $this->input->post('chk_deny_value');
        $chk_block_value = $this->input->post('chk_block_value');
        $chk_unblock_value = $this->input->post('chk_unblock_value');
        $chk_sendblock_value = $this->input->post('chk_sendblock_value');
        $chk_unsendblock_value = $this->input->post('chk_unsendblock_value');


        $option1 = array();
        $option1[0] = $this->input->post('ipt_userno');

        $option = array();
        $option['storeno'] = (int)$this->input->post('ipt_storeno');
        $option['groupno'] = (int)$this->input->post('ipt_groupno');
        $option['email'] = $this->input->post('ipt_email');
        $option['mobile'] = $ipt_mobile;
        $option['phone'] = $ipt_phone;
        $option['phone_080'] = $ipt_phone_080;

        $ban_option = array();
        // if ($chk_block_value == '1') $option['state'] = '1';
        // else if ($chk_unblock_value == '1') $option['state'] = '0';
        // if ($chk_deny_value == '1') $option['state'] = '2';
        //state : 0:정상, 1:차단, 2:탈퇴, 3:발송제한
        if ($chk_unblock_value == '1') {
            $option['state'] = '0';
            // 차단해제
            $ban_option['type'] = '1';
            $ban_option['ban'] = '0';
        }
        else if ($chk_block_value == '1') {
            $option['state'] = '1';
            // 차단
            $ban_option['type'] = '1';
            $ban_option['ban'] = '1';
        }
        else if ($chk_unsendblock_value == '1') {
            $option['state'] = '0';
            // 발송제한 해제
            $ban_option['type'] = '3';
            $ban_option['ban'] = '0';
        }
        else if ($chk_sendblock_value == '1') {
            $option['state'] = '3';
            // 발송제한
            $ban_option['type'] = '3';
            $ban_option['ban'] = '1';
        }
        if ($chk_deny_value == '1') {
            $option['state'] = '2';
            // 탈퇴
            $ban_option['type'] = '2';
            $ban_option['ban'] = '1';
        }

        $chk_refund_value = $this->input->post('chk_refund_value');
        if ($chk_refund_value == '1') $option['refund_val'] = '1';
        else $option['refund_val'] = '0';

        // (차단,탈퇴)일자
        if ($option['state'] == '1' || $option['state'] == '2') {
            if ($this->input->post('ipt_remove_date') == '0000-00-00 00:00:00') {
                $option['remove_date'] = $this->current_time;
            }
        }
        $ipt_password = '';
        // $ipt_password = trim($this->input->post('ipt_password'));
        if ($ipt_password != '') $option['password'] = password_hash($ipt_password, PASSWORD_BCRYPT);
        $option['pay_type'] = $this->input->post('rd_paytype_value');
        $option['ad_type'] = $this->input->post('rd_adtype_value');
        // $option['black_list'] = $this->input->post('rd_blacklist_value');
        $option['level'] = $this->input->post('ipt_level');
        $option['pg'] = $this->input->post('rd_pg_value');
        $option['sms1'] = $this->input->post('ipt_sms1');
        $option['lms1'] = $this->input->post('ipt_lms1');
        $option['mms1'] = $this->input->post('ipt_mms1');
        $option['ad_title'] = trim($this->input->post('ipt_ad_title'));

        if ((int)$this->session->userdata('level') >= 5) {
            $option['ch_sms'] = $this->input->post('ipt_ch_sms');
            $option['ch_lms'] = $this->input->post('ipt_ch_lms');
            $option['ch_mms'] = $this->input->post('ipt_ch_mms');
        }

        if ((int)$this->session->userdata('level') == 9 || (int)$this->session->userdata('level') == 5) {
            $option['memo5'] = $this->input->post('memo5');
        }
        if ((int)$this->session->userdata('level') == 9 || (int)$this->session->userdata('level') == 3) {
            $option['memo3'] = $this->input->post('memo3');
        }


        $this->load->model('userModels');
        if (count($ban_option)) {
            $ban_option['userno'] = (int)$this->input->post('ipt_userno');
            $result = $this->userModels->add_ban_list_by_admin($ban_option);
            if (!$result) {
                $this->session->set_flashdata('notice', '시스템 오류입니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $result = $this->userModels->modify_user_by_admin($option,$option1);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        //session 정보 초기화
        $cache_key = 'session_'.$this->input->post('ipt_storeno').'_'.$this->input->post('ipt_userid');
        $this->cache->redis->save($cache_key, '1', 3600); //1시간 자동삭제

        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function cblist() {
        auth_session_userdata($this, 3);

        if ($this->input->get('sfl') && $this->input->get('stx')) { //검색
            $sfl = $this->input->get('sfl');
            $stx = trim($this->input->get('stx'));
            $offset = 0;
        } else {
            $sfl = $this->uri->segment(3);
            $stx = $this->uri->segment(4);
            $offset =  (int)$this->uri->segment(5);
        }
        $org_stx = $stx;

        if (!$stx) $stx = '000';
        if (!$sfl) $sfl = '000';
        if ($sfl == 'callback') $stx = str_replace('-','',$stx);

        $config['base_url'] = "/admuser/cblist/{$sfl}/{$stx}";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'sfl' => ($sfl == '000' ? '' : $sfl),
            'val' => ($stx == '000' ? '' : $stx),
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );
        $this->load->model('userModels');
        $config['total_rows'] = (int)$this->userModels->get_callback_count_by_admin($option);
        $data['result'] = $this->userModels->get_callback_limit_by_admin($option);

        $cnt = (int)$this->userModels->get_callback_new_count();
        $cache_key = 'sow_callback_cnt';
        $this->cache->redis->save($cache_key, $cnt, 600); //10분 후 자동삭제

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['stx'] = ($stx == '000' ? '' : $org_stx);
        $data['sfl'] = ($stx == '000' ? '' : $sfl);
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admuser/cblist', $data);
        $this->load->view('templates/adm_footer');
    }
    public function cblist_ban() {
        auth_session_userdata($this, 3);

        if ($this->input->get('sfl') && $this->input->get('stx')) { //검색
            $sfl = $this->input->get('sfl');
            $stx = trim($this->input->get('stx'));
            $offset = 0;
        } else {
            $sfl = $this->uri->segment(3);
            $stx = $this->uri->segment(4);
            $offset =  (int)$this->uri->segment(5);
        }
        $org_stx = $stx;

        if (!$stx) $stx = '000';
        if (!$sfl) $sfl = '000';
        if ($sfl == 'callback') $stx = str_replace('-','',$stx);

        $config['base_url'] = "/admuser/cblist_ban/{$sfl}/{$stx}";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'sfl' => ($sfl == '000' ? '' : $sfl),
            'val' => ($stx == '000' ? '' : $stx),
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );
        $this->load->model('userModels');
        $config['total_rows'] = (int)$this->userModels->get_callback_ban_count_by_admin($option);
        $data['result'] = $this->userModels->get_callback_ban_limit_by_admin($option);

        // $cnt = (int)$this->userModels->get_callback_new_count();
        // $cache_key = 'sow_callback_cnt';
        // $this->cache->redis->save($cache_key, $cnt, 600); //10분 후 자동삭제

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['stx'] = ($stx == '000' ? '' : $org_stx);
        $data['sfl'] = ($stx == '000' ? '' : $sfl);
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admuser/cblist_ban', $data);
        $this->load->view('templates/adm_footer');
    }
    public function add_memo() {
        auth_session_userdata($this, 5);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('xid', 'xid', 'required');
        $this->form_validation->set_rules('new_memo', 'new_memo', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option = array(
            'xid' => $this->input->post('xid'),
            'memo' => $this->input->post('new_memo'),
        );
        $this->load->model('userModels');
        $result = $this->userModels->modify_callback_files_by_admin($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function cbflist() {
        auth_session_userdata($this, 3);

        if ($this->input->get('sfl') && $this->input->get('stx')) { //검색
            $sfl = $this->input->get('sfl');
            $stx = trim($this->input->get('stx'));
            $offset = 0;
        } else {
            $sfl = $this->uri->segment(3);
            $stx = $this->uri->segment(4);
            $offset =  (int)$this->uri->segment(5);
        }
        $org_stx = $stx;

        if (!$stx) $stx = '000';
        if (!$sfl) $sfl = '000';
        if ($sfl == 'callback') $stx = str_replace('-','',$stx);

        $config['base_url'] = "/admuser/cbflist/{$sfl}/{$stx}";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'sfl' => ($sfl == '000' ? '' : $sfl),
            'val' => ($stx == '000' ? '' : $stx),
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );
        $this->load->model('userModels');
        $config['total_rows'] = (int)$this->userModels->get_callback_files_count_by_admin($option);
        $data['result'] = $this->userModels->get_callback_files_limit_by_admin($option);

        // $cnt = (int)$this->userModels->get_callback_new_count();
        // $cache_key = 'sow_callback_cnt';
        // $this->cache->redis->save($cache_key, $cnt, 600); //10분 후 자동삭제

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['stx'] = ($stx == '000' ? '' : $org_stx);
        $data['sfl'] = ($stx == '000' ? '' : $sfl);
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admuser/cbflist', $data);
        $this->load->view('templates/adm_footer');
    }
    public function store_list() {
        auth_session_userdata($this,9);
        if ($this->input->get('stx')) { //검색
            $stx = $this->input->get('stx');
            $offset = 0;
        } else {
            $stx = '000';
            $offset =  (int)$this->uri->segment(4);
        }
        $config['base_url'] = "/admuser/users/{$stx}";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'val' => ($stx == '000' ? '' : $stx),
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );
        $this->load->model('userModels');
        $config['total_rows'] = (int)$this->userModels->get_store_count_by_admin();
        $data['result'] = $this->userModels->get_store_by_admin();

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['stx'] = ($stx == '000' ? '' : $stx);
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admuser/store_list', $data);
        $this->load->view('templates/adm_footer');
    }
    public function store_auth() {
        auth_session_userdata($this,9);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_storename', 'Ipt storename', 'required|is_unique[store.storename]');
        $this->form_validation->set_rules('ipt_sms1', 'Ipt sms1', 'required');
        $this->form_validation->set_rules('ipt_sms2', 'Ipt sms2', 'required');
        $this->form_validation->set_rules('ipt_lms1', 'Ipt lms1', 'required');
        $this->form_validation->set_rules('ipt_lms2', 'Ipt lms2', 'required');
        $this->form_validation->set_rules('ipt_mms1', 'Ipt mms1', 'required');
        $this->form_validation->set_rules('ipt_mms2', 'Ipt mms2', 'required');
        $this->form_validation->set_rules('ipt_kat', 'Ipt kat', 'required');
        $this->form_validation->set_rules('ipt_kft', 'Ipt kft', 'required');
        $this->form_validation->set_rules('ipt_kftm', 'Ipt kftm', 'required');
        $this->form_validation->set_rules('ipt_ch_sms', 'Ipt ch_sms', 'required');
        $this->form_validation->set_rules('ipt_ch_lms', 'Ipt ch_lms', 'required');
        $this->form_validation->set_rules('ipt_ch_mms', 'Ipt ch_mms', 'required');
        $this->form_validation->set_rules('ipt_ch_kko', 'Ipt ch_kko', 'required');
        $this->form_validation->set_rules('ipt_contract_sms', 'ipt_contract_sms', 'required');
        $this->form_validation->set_rules('ipt_contract_lms', 'ipt_contract_lms', 'required');
        $this->form_validation->set_rules('ipt_contract_mms', 'ipt_contract_mms', 'required');
        // $this->form_validation->set_rules('ipt_contract_kko', 'ipt_contract_kko', 'required');
        $this->form_validation->set_rules('ipt_restrict_sending', 'ipt_restrict_sending', 'in_list[0,1]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option = array(
            'storename'         => $this->input->post('ipt_storename'),
            'url'               => $this->input->post('ipt_url'),
            'sms1'              => $this->input->post('ipt_sms1'),
            'sms2'              => $this->input->post('ipt_sms2'),
            'lms1'              => $this->input->post('ipt_lms1'),
            'lms2'              => $this->input->post('ipt_lms2'),
            'mms1'              => $this->input->post('ipt_mms1'),
            'mms2'              => $this->input->post('ipt_mms2'),
            'kat'               => $this->input->post('ipt_kat'),
            'kft'               => $this->input->post('ipt_kft'),
            'kftm'              => $this->input->post('ipt_kftm'),
            'ch_sms'            => $this->input->post('ipt_ch_sms'),
            'ch_lms'            => $this->input->post('ipt_ch_lms'),
            'ch_mms'            => $this->input->post('ipt_ch_mms'),
            'ch_kko'            => $this->input->post('ipt_ch_kko'),
            'contract_sms'      => $this->input->post('ipt_contract_sms'),
            'contract_lms'      => $this->input->post('ipt_contract_lms'),
            'contract_mms'      => $this->input->post('ipt_contract_mms'),
            'contract_kko'      => $this->input->post('ipt_contract_kko'),
            'restrict_sending'  => $this->input->post('ipt_restrict_sending'),
            'check_balance'     => $this->input->post('ipt_check_balance'),
        );
        $this->load->model('userModels');
        $result = $this->userModels->add_store($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function cb_auth() {
        auth_session_userdata($this, 5);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('act_button', 'act button', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $option1 = $this->input->post('chk');
        if (!is_array($option1)) {
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }


        $status = ($this->input->post('act_button') == '차단하기' ? '2' : '3');
        if ($status == '2') {
            $option = array(
                'status' => $status,
                'ban_time' => $this->current_time,
            );
        } else {
            $option = array(
                'status' => $status,
                'app_time' => $this->current_time,
            );
        }

        $this->load->model('userModels');
        $result = $this->userModels->modify_callback_by_admin($option,$option1);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $result = $this->userModels->get_callback_by_admin($option1);
        foreach ($result as $row) {
            //session 정보 초기화
            $cache_key = 'callback_'.$row->storeno.'_'.$row->userid;
            $this->cache->redis->save($cache_key, '1', 3600); //1시간 자동삭제
        }
        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function bill() {
        auth_session_userdata($this, 5);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_storeno', 'Ipt storeno', 'required');
        $this->form_validation->set_rules('ipt_userno', 'Ipt userno', 'required');
        $this->form_validation->set_rules('ipt_userid', 'Ipt userid', 'required');
        $this->form_validation->set_rules('rd_bill_mode', 'Rd bill mode', 'required');
        $this->form_validation->set_rules('ipt_amount', 'Ipt amount', 'required');
        // $this->form_validation->set_rules('ipt_memo', 'Ipt memo', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $ipt_amount = (int)str_replace(',', '', $this->input->post('ipt_amount'));
        if ($ipt_amount <= 0) {
            $this->session->set_flashdata('notice', '금액 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->input->post('rd_bill_mode') == 'MA' || $this->input->post('rd_bill_mode') == 'MB') {
            $ipt_amount = $ipt_amount * (-1);
        }

        $option2 = array(
            'userno' => $this->input->post('ipt_userno'),
        );
        $this->load->model('userModels');
        $groupinfo = $this->userModels->get_groupno_by_userno($option2);
        $userinfo = $this->userModels->get_user_by_admin($option2);
        $new_balance = $userinfo->cash + $ipt_amount;
        if ($new_balance < 0) {
            $this->session->set_flashdata('notice', '보유금액 보다 더 많은 금액을 차감하셨습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option = array(
            'userno' => $this->input->post('ipt_userno'),
            'userid' => $this->input->post('ipt_userid'),
            'storeno' => $this->input->post('ipt_storeno'),
            'groupno' => $groupinfo->groupno,
        );
        $option1 = array(
            'amount' => $ipt_amount,
            'bill_mode' => $this->input->post('rd_bill_mode'),
            'memo' => trim($this->input->post('ipt_memo')),
        );

        $this->load->model('payModels');
        $result = $this->payModels->modify_cash_by_admin($option, $option1);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        //session 정보 초기화
        $cache_key = 'session_'.$this->input->post('ipt_storeno').'_'.$this->input->post('ipt_userid');
        $this->cache->redis->save($cache_key, '1', 3600); //1시간 자동삭제

        $this->session->set_flashdata('notice', '정상적으로 등록되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function callback() {
        auth_session_userdata($this, 5);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_userid', 'Ipt userid', 'trim|required');
        $this->form_validation->set_rules('rd_cert_type', 'Rb cert type', 'required');
        $this->form_validation->set_rules('ipt_callback', 'Ipt callback', 'trim|required');
        // $this->form_validation->set_rules('ipt_name', 'Ipt name', 'required');
        // $this->form_validation->set_rules('ipt_name', 'Ipt memo', 'trim|required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $ipt_callback = str_replace('-', '', trim($this->input->post('ipt_callback')));
        if ($ipt_callback == '') {
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $ipt_userid = trim($this->input->post('ipt_userid'));
        $ipt_memo = trim($this->input->post('ipt_memo'));
        $ipt_name = trim($this->input->post('ipt_name'));

        $this->load->model('userModels');
        $option = array(
            'userid' => $ipt_userid
        );
        $result = $this->userModels->get_user($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '발신번호 등록이 제한된 회원 입니다. (발신제한,차단,탈퇴,미존재 회원)');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $option1 = array(
            'userno' => $result->userno,
            'storeno' => $result->storeno,
            'userid' => $ipt_userid,
            'name' => $ipt_name,
            'memo' => $ipt_memo,
            'callback' => $ipt_callback,
            'cert_type' => $this->input->post('rd_cert_type'),
            'authcode' => '',
            'status' => '3',
        );

        $user_cnt = (int)$this->userModels->get_callback_user_count_by_admin($option1);
        if ($user_cnt > 10) {
            $this->session->set_flashdata('notice', '발신번호는 10개 이상 등록할 수 없습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $result = $this->userModels->is_callback_in_store_unique($option1);
        if ($result->callback != '') {
            $this->session->set_flashdata('notice', '발신번호가 존재합니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $result = $this->userModels->add_callback($option1);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $cache_key = 'callback_'.$option1['storeno'].'_'.$ipt_userid;
        $this->cache->redis->save($cache_key, '1', 3600); //1시간 자동삭제

        $this->session->set_flashdata('notice', '정상적으로 등록되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function add_user() {
        auth_session_userdata($this, 5);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt1_userid', 'ipt1_userid', 'trim|is_unique[users.userid]|required',
            array(
                'is_unique' => '이미 존재하는 아이디 입니다.'));
        $this->form_validation->set_rules('ipt1_realname', 'ipt1_realname', 'trim|required');
        $this->form_validation->set_rules('ipt1_password', 'ipt1_password', 'trim|required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '(아이디 중복체크) 파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $ipt1_password = trim($this->input->post('ipt1_password'));
        $ipt1_mobile = str_replace('-', '', $this->input->post('ipt1_mobile'));

        $this->load->model('userModels');
        $option = array(
            'storeno' => $this->session->userdata('storeno'),
        );
        $result = $this->userModels->get_store($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $store = array(
            'sms1'      => (float)$result->sms1,
            'sms2'      => (float)$result->sms2,
            'lms1'      => (float)$result->lms1,
            'lms2'      => (float)$result->lms2,
            'mms1'      => (float)$result->mms1,
            'mms2'      => (float)$result->mms2,
            'kat'       => (float)$result->kat,
            'kft'       => (float)$result->kft,
            'kftm'      => (float)$result->kftm,
            'ch_sms'    => (int)$result->ch_sms,
            'ch_lms'    => (int)$result->ch_lms,
            'ch_mms'    => (int)$result->ch_mms,
            'ch_kko'    => (int)$result->ch_kko
        );

        $option = array(
            'storeno'   => (int)$this->session->userdata('storeno'),
            'groupno'   => (int)$this->session->userdata('groupno'),
            'userid'    => $this->input->post('ipt1_userid'),
            'realname'  => $this->input->post('ipt1_realname'),
            'password'  => password_hash($ipt1_password, PASSWORD_BCRYPT),
            'mobile'    => $ipt1_mobile,
            'email'     => $this->input->post('ipt1_email'),
            'sms1'      => $store['sms1'],
            'sms2'      => $store['sms2'],
            'lms1'      => $store['lms1'],
            'lms2'      => $store['lms2'],
            'mms1'      => $store['mms1'],
            'mms2'      => $store['mms2'],
            'kat'       => $store['kat'],
            'kft'       => $store['kft'],
            'kftm'      => $store['kftm'],
            'ch_sms'    => $store['ch_sms'],
            'ch_lms'    => $store['ch_lms'],
            'ch_mms'    => $store['ch_mms'],
            'ch_kko'    => $store['ch_kko'],
        );
        $option1 = array(
            'userid' => $this->input->post('ipt1_userid'),
            'name' => '기본발신번호',
            'callback' => $ipt1_mobile,
            'cert_type' => '1',
            'authcode' => '',
            'status' => '3',
        );

        $userno = $this->userModels->add_user_by_admin($option,$option1);
        if (!$userno) {
            $this->session->set_flashdata('notice', '회원가입에 실패 했습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->session->set_flashdata('notice', '정상적으로 등록했습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function phone080() {
        auth_session_userdata($this, 5);
        if ($this->input->get('stx')) { //검색
            $stx = $this->input->get('stx');
            $stx = str_replace('-','',$stx);
            $offset = 0;
        } else {
            $stx = '000';
            $offset =  (int)$this->uri->segment(4);
        }
        $config['base_url'] = "/admuser/phone080/{$stx}";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'val' => ($stx == '000' ? '' : $stx),
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );
        $this->load->model('addressModels');
        $config['total_rows'] = (int)$this->addressModels->get_phone_080_count_by_admin($option);
        $data['result'] = $this->addressModels->get_phone_080_limit_by_admin($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['stx'] = ($stx == '000' ? '' : $stx);
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admuser/phone080', $data);
        $this->load->view('templates/adm_footer');
    }
    public function phone080_delete() {
        auth_session_userdata($this, 5);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('act_button', 'act_button', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option = $this->input->post('chk');
        if (!is_array($option)) {
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->load->model('addressModels');
        $result = $this->addressModels->modify_phone_080($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function add_phone080() {
        auth_session_userdata($this, 5);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_mobile', 'ipt_mobile', 'required');
        $this->form_validation->set_rules('ipt_phone_080', 'ipt_phone_080', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $ipt_mobile = trim($this->input->post('ipt_mobile'));
        $ipt_mobile = strip_phone($ipt_mobile);
        $ipt_phone_080 = $this->input->post('ipt_phone_080');


        $option = array(
            'mobile' => $ipt_mobile,
            'phone_080' => $ipt_phone_080,
            'storeno' => $this->session->userdata('storeno'),
            'state' => '0',
            'reg_time' => '0000-00-00 00:00:00'
        );
        $this->load->model('addressModels');
        $result = $this->addressModels->add_phone_080($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function cb_delete() {
        auth_session_userdata($this, 5);
        $option = $this->input->post('chk');
        if (!is_array($option)) {
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->load->model('userModels');
        $sent_info = '';
        foreach ($option as $xid) {
            $sent_info = $this->userModels->get_sent_count_by_callback_xid($xid);
            if ($sent_info['callback'] == '') {
                $this->session->set_flashdata('notice', '시스템 오류입니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
            if ($sent_info['sent_cnt']) {
                $this->session->set_flashdata('notice', $sent_info['callback'].' 발송내역이 있어 삭제할 수 없습니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $result = $this->userModels->delete_callback_by_admin($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $result = $this->userModels->get_delete_callback_by_admin($option);
        foreach ($result as $row) {
            //session 정보 초기화
            $cache_key = 'callback_'.$row->storeno.'_'.$row->userid;
            $this->cache->redis->save($cache_key, '1', 3600); //1시간 자동삭제
        }
        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function get_channel_list() {
        auth_session_userdata($this,5); // < level
        header("Content-Type: application/json");

        $this->load->library('form_validation');
        $this->form_validation->set_rules('channel_type', 'channel_type', 'required');
        $this->form_validation->set_rules('where', 'Auth', 'required|in_list[admuser]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }
        $channel_type = $this->input->post('channel_type');

        $this->load->model('userModels');
        $option = array(
            'type' => $channel_type,
        );
        $result_dest = $this->userModels->get_channel_list_by_admin($option);
        $option = array(
            'type' => $channel_type,
            'status' => '1',
        );
        $result_target = $this->userModels->get_channel_list_by_admin($option);

        $i = 0;
        $channel_dest = array();
        foreach ($result_dest as $row) {
            $channel_dest[$i++] = $row->channel.'|'.$row->channel_exp;
        }
        $i = 0;
        $channel_target = array();
        foreach ($result_target as $row) {
            $channel_target[$i++] = $row->channel.'|'.$row->channel_exp;
        }

        $data_json['result'] = 'success';
        $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        $data_json['message'] = '정상적으로 처리되었습니다.';
        $data_json['channel_dest'] = $channel_dest;
        $data_json['channel_target'] = $channel_target;
        exit (json_encode($data_json));
    }
    public function change_channel() {
        auth_session_userdata($this,5); // < level

        $this->load->library('form_validation');
        $this->form_validation->set_rules('channel_type', 'channel_type', 'required|in_list[sms,lms,mms]');
        $this->form_validation->set_rules('channel_dest', 'channel_dest', 'required');
        $this->form_validation->set_rules('channel_target', 'channel_target', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $channel_type = $this->input->post('channel_type');
        $channel_dest = (int)$this->input->post('channel_dest');
        $channel_target = (int)$this->input->post('channel_target');

        $option = array(
            'channel_type' => $channel_type,
            'channel_dest' => $channel_dest,
            'channel_target' => $channel_target,
        );

        $this->load->model('userModels');
        $result = $this->userModels->modify_channel_all_by_admin($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        //session 정보 초기화
        if ($channel_type == 'sms') $cache_key = 'session_change_channel_sms';
        else if ($channel_type == 'lms') $cache_key = 'session_change_channel_lms';
        else if ($channel_type == 'mms') $cache_key = 'session_change_channel_mms';
        $this->cache->redis->save($cache_key, $channel_dest, 3600); //1시간 자동삭제

        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function user_bill() {
        if ($this->input->get('ipt_date_from') && $this->input->get('ipt_date_to')) { //검색
            $date_from = $this->input->get('ipt_date_from');
            $date_to = $this->input->get('ipt_date_to');
            $stx = trim($this->input->get('stx'));
            $sfl = $this->input->get('sfl');
            $offset = 0;
        } else {
            $date_from = $this->uri->segment(3);
            $date_to = $this->uri->segment(4);
            $stx = $this->uri->segment(5);
            $sfl = $this->uri->segment(6);
            $offset =  (int)$this->uri->segment(7);
        }
        if (!$stx) $stx = '000';
        if (!$sfl) $sfl = '000';
        $ci_date_from = ($date_from == '' ? date('Y-m-d') : $date_from);
        $ci_date_to = ($date_to == '' ? date('Y-m-d') : $date_to);

        if ($sfl == 'callback') $stx = str_replace('-','',$stx);

        $config['base_url'] = "/admuser/user_bill/{$ci_date_from}/{$ci_date_to}/{$stx}/{$sfl}";
        $config['per_page'] = $this->rows_per_page;

        $date_from = $ci_date_from;
        $date_to = $ci_date_to;

        $option = array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'userid' => ($stx == '000' ? '' : $stx),
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );

        $this->load->model('payModels');
        $result1 = $this->payModels->get_service_count_by_admin($option);
        $config['total_rows'] = (int)$result1->cnt;
        $data['result'] = $this->payModels->get_service_limit_by_admin($option);
        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['stx'] = ($stx == '000' ? '' : $stx);
        $data['sfl'] = ($stx == '000' ? '' : $sfl);
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admuser/user_bill', $data);
        $this->load->view('templates/adm_footer');
    }
    public function login() {
        auth_session_userdata($this,5); // < level
        $userno = (int)$this->uri->segment(3);

        if (!$userno) {
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->load->model('userModels');
        $option = array(
            'userno' => $userno
        );
        $result = $this->userModels->modify_allow_login_by_admin($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        redirect($_SERVER['HTTP_REFERER']);
    }
}
