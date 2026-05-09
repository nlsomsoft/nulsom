<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Info extends CI_Controller {
    private $current_time;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        $this->load->helper(array('form','url','phone','mydate'));
        initialize_session_userdata($this);
    }
    // public function sessdata() {
    //     error_log(print_r($this->session->all_userdata(), 1), 0);
    // }
    public function add_file() {
        $date = date('Ym');
        $file_path = FCPATH.'uploads/'.$date;
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, TRUE);
        }
        $file_name = $this->session->userdata('uniqueno').date('is');

        $config['upload_path'] = $file_path;
        $config['file_name'] = $file_name;
        $config['allowed_types'] = 'jpg|jpeg|gif|png|pdf';
        $config['max_size'] = 10240; // 10M
        // $config['max_width'] = 1024;
        // $config['max_height'] = 1024;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('file_info')) {
            error_log(print_r($this->upload->display_errors(),1),0);
            $this->session->set_flashdata('notice', '이미지 등록 중 오류가 발생했습니다.1');
            redirect('/info/cblist');
        }
        $upload_info = $this->upload->data();

        $config['image_library'] = 'gd2';
        $config['source_image'] = $file_path.'/'.$file_name.$upload_info['file_ext'];
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width'] = 1024;
        $config['height'] = 768;
        $config['quality'] = 60;
// error_log(print_r($config,1),0);
        if (strtolower($upload_info['file_ext']) != '.pdf') {
            $this->load->library('image_lib', $config);
            if (!$this->image_lib->resize()) {
                error_log(print_r($this->image_lib->display_errors(),1),0);
                $this->session->set_flashdata('notice', '이미지 등록 중 오류가 발생했습니다.2');
                redirect('/info/cblist');
            }
            $photo_array = array(
                'file_path' => $file_path.'/'.$file_name.'_thumb'.$upload_info['file_ext'],
                'image_path' => '/'.str_replace(FCPATH,'',$file_path.'/'.$file_name.'_thumb'.$upload_info['file_ext']),
            );
            @unlink($config['source_image']);
        } else {
            $photo_array = array(
                'file_path' => $file_path.'/'.$file_name.$upload_info['file_ext'],
                'image_path' => '/'.str_replace(FCPATH,'',$file_path.'/'.$file_name.$upload_info['file_ext']),
            );
        }
// error_log(print_r($photo_array,1),0);
        $file_path = $photo_array['file_path'];
        $image_path = $photo_array['image_path'];
        if ($file_path == '') {
            $this->session->set_flashdata('notice', '이미지 등록 중 오류가 발생했습니다.3');
            redirect('/info/cblist');
        }

        $option = array(
            'storeno' => $this->session->userdata('storeno'),
            'userno' => $this->session->userdata('userno'),
            'userid' => $this->session->userdata('userid'),
            'file_path' => $file_path,
            'image_path' => $image_path
        );
        $this->load->model('userModels');
        $result = $this->userModels->add_callback_files($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect('/info/cblist');
        }

        $this->session->set_flashdata('notice', '정상적으로 등록되었습니다.');
        redirect('/info/cblist');
    }
    public function register() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_email', 'Email', 'required|valid_email');
        // if ($this->input->post('user_type') == '2') {
        //     $this->form_validation->set_rules('ipt_com_name', 'Company name', 'required|min_length[2]|max_length[10]');
        //     $this->form_validation->set_rules('ipt_com_number', 'Company number', 'required|min_length[10]|max_length[12]');
        // }
        if ($this->input->post('ipt_phone') != '') {
            $this->form_validation->set_rules('ipt_phone', 'Phone Value', 'required|min_length[9]|max_length[13]');
        }
        $this->form_validation->set_rules('ipt_ad_title', 'ipt_ad_title', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option = array(
            'email' => trim($this->input->post('ipt_email')),
            'phone' => trim($this->input->post('ipt_phone')),
            'ad_title' => trim($this->input->post('ipt_ad_title')),
            'mod_date'  => $this->current_time
        );
        $this->load->model('userModels');
        $result = $this->userModels->modify_user($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $result = $this->userModels->get_user_by_userno();
        // $this->load->helper('cache');
        $this->session->set_userdata(set_session_userdata($result));

        $data = array(
            'userid' => $this->session->userdata('userid'),
            'memo' => '회원정보변경',
            'ip' => $this->input->ip_address(),
        );
        $result = $this->userModels->add_users_history($data);

        $this->session->set_flashdata('notice', '정상적으로 변경되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function confirm() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_password', 'Password', 'required|min_length[8]|max_length[20]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect('/');
        }
        $this->load->model('userModels');
        $result = $this->userModels->get_user_by_userno();
        $db_hash = trim($result->password);
        $ipt_password = trim($this->input->post('ipt_password'));

        // $this->load->driver('cache');
        if (password_verify($ipt_password, $db_hash)) {
            $cache_key = 'sow_auth'.$this->session->userdata('uniqueno');
            $this->cache->redis->save($cache_key, 'auth', 1800); //30분 후 자동삭제
            $cache_key = 'sow_auth_nums'.$this->session->userdata('uniqueno');
            $this->cache->redis->save($cache_key, 0, 1800); //30분 후 자동삭제
        } else {
            $cache_key = 'sow_auth_nums'.$this->session->userdata('uniqueno');
            $this->cache->redis->increment($cache_key, 1);
            if ((int)$this->cache->redis->get($cache_key) >= 3) {
                $this->session->sess_destroy();
                $cache_key = 'sow_auth'.$this->session->userdata('uniqueno');
                $this->cache->redis->save($cache_key, '', 60);
                $cache_key = 'sow_auth_nums'.$this->session->userdata('uniqueno');
                $this->cache->redis->save($cache_key, '', 60);
                redirect('/');
            }
            $this->session->set_flashdata('notice', '비밀번호가 일치하지 않습니다.');
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function intro() {
        // $this->load->driver('cache');
        $cache_key = 'sow_auth'.$this->session->userdata('uniqueno');
        $this->load->view('templates/header');
        if ($this->cache->redis->get($cache_key) != 'auth') {
            $this->load->view('info/confirm');
        } else {
            if ($this->session->userdata('user_type') == '2') {
                $this->load->view('info/intro_business');
            } else {
                $this->load->view('info/intro_nomal');
            }
        }
        $this->load->view('templates/footer');
    }
    public function pform() {
        $this->load->view('templates/header');
        $this->load->view('info/pform');
        $this->load->view('templates/footer');
    }
    public function password() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_password', 'Password', 'required|min_length[8]|max_length[20]');
        $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[8]|max_length[20]|matches[re_password]');
        $this->form_validation->set_rules('re_password', 'Re New Password', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->load->model('userModels');
        $result = $this->userModels->get_user_by_userno();
        $db_hash = trim($result->password);

        // $this->load->driver('cache');
        if (!password_verify($this->input->post('ipt_password'), $db_hash)) {
            $this->session->set_flashdata('notice', '비밀번호 인증 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option1 = array(
            'userno' => $this->session->userdata('userno'),
            'userid' => $this->session->userdata('userid'),
            'password' => $db_hash,
            'type' => '0',
            'ip' => $this->input->ip_address()
        );
        $result1 = $this->userModels->record_password_history($option1);
        if (!$result1) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $new_password = trim($this->input->post('new_password'));
        $option = array(
            'password' => password_hash($new_password, PASSWORD_BCRYPT),
            'mod_date' => $this->current_time
        );
        $result = $this->userModels->modify_user($option);

        $data = array(
            'userid' => $this->session->userdata('userid'),
            'memo' => '비밀번호변경',
            'ip' => $this->input->ip_address(),
        );
        $result = $this->userModels->add_users_history($data);

        $this->session->set_flashdata('notice', '정상적으로 변경되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function wform() {
        $this->load->view('templates/header');
        $this->load->view('info/wform');
        $this->load->view('templates/footer');
    }
    public function withdrawal() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_password', 'Password', 'required|min_length[8]|max_length[20]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->load->model('userModels');
        $result = $this->userModels->get_user_by_userno();
        $db_hash = trim($result->password);

        // $this->load->driver('cache');
        if (!password_verify($this->input->post('ipt_password'), $db_hash)) {
            $this->session->set_flashdata('notice', '비밀번호가 일치하지 않습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option = array(
            'password' => password_hash(rand(100000,99999), PASSWORD_BCRYPT),
            'state' => '2',
            'remove_date' => $this->current_time
        );
        $result = $this->userModels->modify_user($option);
        $this->userModels->withdrawal_user();

        $data = array(
            'userid' => $this->session->userdata('userid'),
            'memo' => '회원탈퇴',
            'ip' => $this->input->ip_address(),
        );
        $result = $this->userModels->add_users_history($data);

        $this->session->sess_destroy();
        $this->session->set_flashdata('notice', '정상적으로 탈퇴 처리되었습니다.');
        redirect('/');
    }
    public function cblist() {
        $this->load->model('userModels');
        $data['result'] = $this->userModels->get_callback();
        $this->load->view('templates/header');
        $this->load->view('info/cblist', $data);
        $this->load->view('templates/footer');
    }
    public function callback_name() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('xid', 'Xid', 'required');
        $this->form_validation->set_rules('new_value', 'New Value', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option = array(
            'xid' => $this->input->post('xid'),
            'name' => $this->input->post('new_value'),
        );
        $this->load->model('userModels');
        $result = $this->userModels->modify_callback_memo($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
        } else {
            $this->session->set_flashdata('notice', '정상적으로 변경되었습니다.');
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function request_auth() {
        header("Content-Type: application/json");

        $this->load->library('form_validation');
        $this->form_validation->set_rules('cname', 'Cname', 'required');
        $this->form_validation->set_rules('cnumber', 'Cnumber', 'required');
        $this->form_validation->set_rules('where', 'Auth', 'required|in_list[info]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }

        $mobile = strip_phone(trim($this->input->post('cnumber')));
        $phone_type = substr($mobile, 0, 3);
        $is_mobile = false;
        if ($phone_type == '010' || $phone_type == '011' || $phone_type == '016' || $phone_type == '017' || $phone_type == '018' || $phone_type == '019') {
            if (!valid_phone($mobile)) {
                $data_json['result'] = 'error';
                $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
                $data_json['message'] = '발신번호 형식 오류입니다.';
                exit (json_encode($data_json));
            }
            $is_mobile = true;
        }

        $this->load->model('userModels');
        $option = array(
            'userid' => $this->session->userdata('userid'),
            'type' => '3'
        );
        $result = $this->userModels->get_auth_mobile($option);
        if ($result != false) { //값이 존재할 경우
            $add_unixtime = strtotime($result->add_date);
            // if (time() < ($add_unixtime + 300)) { //5분
            if (time() < ($add_unixtime + 60)) { //1분
                $data_json['result'] = 'error';
                $data_json['authed_mobile'] = $result->mobile;
                $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
                $data_json['message'] = '인증번호가 이미 발송되었습니다. 1분 후 다시 인증요청이 가능합니다.';
                exit (json_encode($data_json));
            }
        }

        $option = array(
            'storeno' => $this->session->userdata('storeno'),
            'userno' => $this->session->userdata('userno'),
            'callback' => $mobile,
        );

        $user_cnt = (int)$this->userModels->get_callback_user_count_by_admin($option);
        if ($user_cnt > 10) {
            $data_json['result'] = 'error';
            $data_json['authed_mobile'] = $result->mobile;
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '발신번호는 10개 이상 등록할 수 없습니다.';
            exit (json_encode($data_json));
        }

        $result = $this->userModels->is_callback_in_store_unique($option);
        if ($result) {
            $data_json['result'] = 'error';
            $data_json['authed_mobile'] = $result->mobile;
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '해당 번호는 이미 존재하는 발신번호 입니다.';
            exit (json_encode($data_json));
        }

        if ($is_mobile == true) {
            $rand_no = rand(111111, 999999);
        } else {
            $rand_no = rand(111, 999);
        }
        $option = array(
            'userid' => $this->session->userdata('userid'),
            'mobile' => $mobile,
            'memo' => $this->input->post('cname'),
            'auth_no' => $rand_no,
            'type' => '3',
        );

        if ($is_mobile == true) {
            $result = $this->userModels->add_auth_mobile($option);
        } else {
            $result = $this->userModels->add_auth_ars($option);
        }
        if (!$result) {
            $data_json['result'] = 'error';
            $data_json['authed_mobile'] = $result->mobile;
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '인증번호 발송에 실패했습니다.';
            exit (json_encode($data_json));
        }

        $data_json['result'] = 'success';
        $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        $data_json['message'] = '인증번호를 발송하였습니다.';
        exit (json_encode($data_json));
    }
    public function confirm_auth() {
        header("Content-Type: application/json");

        $this->load->library('form_validation');
        $this->form_validation->set_rules('auth_number', 'Auth_number', 'required');
        $this->form_validation->set_rules('where', 'Auth', 'required|in_list[info]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }

        $this->load->model('userModels');
        $option = array(
            'userid' => $this->session->userdata('userid'),
            'type' => '3'
        );
        $result = $this->userModels->get_auth_mobile($option);
        if (!$result) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '먼저 인증요청을 하신 후 등록이 가능합니다.';
            exit (json_encode($data_json));
        }
        $ano = (int)$result->ano;
        $add_unixtime = strtotime($result->add_date);
        // if (time() > ($add_unixtime + 300)) { //5분
        if (time() > ($add_unixtime + 60)) { //1분
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '인증번호 유효시간이 초과되었습니다. 다시 인증해 주세요.';
            exit (json_encode($data_json));
        }
        if ($this->input->post('auth_number') != $result->auth_no) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '인증번호가 일치하지 않습니다.';
            exit (json_encode($data_json));
        }
        //cert_type:1, status:1 is default value
        $option = array(
            'userno' => $this->session->userdata('userno'),
            'storeno' => $this->session->userdata('storeno'),
            'userid' => $this->session->userdata('userid'),
            'name' => $result->memo,
            'callback' => $result->mobile,
            'authcode' => $result->auth_no,
            'cert_type' => '1',
            'status'    => '1',
        );

        $user_cnt = (int)$this->userModels->get_callback_user_count_by_admin($option);
        if ($user_cnt > 10) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '발신번호는 10개 이상 등록할 수 없습니다.';
            exit (json_encode($data_json));
        }

        $result = $this->userModels->add_callback($option);
        if (!$result) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '시스템 오류입니다.';
            exit (json_encode($data_json));
        }

        $option = array(
            'ano' => $ano
        );
        $result = $this->userModels->modify_auth_mobile($option);
        $data_json['result'] = 'success';
        $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        $data_json['message'] = '정상적으로 등록되었습니다.';
        exit (json_encode($data_json));
    }
    public function delete_nums() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('chk_nums', 'Check Nums', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $carray = explode(',', $this->input->post('chk_nums'));
        $this->load->model('userModels');
        $result = $this->userModels->delete_callback_selected($carray);
        if (!$result) {
            $this->session->set_flashdata('notice', '처리과정 중 오류가 발생했습니다.');
            redirect('/info/cblist');
        }
        $this->session->set_flashdata('notice', '정상적으로 처리했습니다.');
        redirect('/info/cblist');
    }
    public function change_nums() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('chk_nums', 'Check Nums', 'required');
        $this->form_validation->set_rules('status', 'status', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option = array(
            'status' => $this->input->post('status'),
        );
        $option1 = explode(',', $this->input->post('chk_nums'));
        $this->load->model('userModels');
        $result = $this->userModels->modify_callback_by_admin($option,$option1);
        if (!$result) {
            $this->session->set_flashdata('notice', '처리과정 중 오류가 발생했습니다.');
            redirect('/info/cblist');
        }

        $cache_key = 'callback_'.$this->session->userdata('storeno').'_'.$this->session->userdata('userid');
        $this->cache->redis->save($cache_key, '1', 3600); //1시간 자동삭제

        $this->session->set_flashdata('notice', '정상적으로 처리했습니다.');
        redirect('/info/cblist');
    }
    public function notice() {
        $offset =  (int)$this->uri->segment(3);
        $config['base_url'] = "/info/notice/";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'del_flag' => '0',
            'limit'  => $this->rows_per_page,
            'offset' => $offset,
        );
        $this->load->model('settingModels');
        $config['total_rows'] = (int)$this->settingModels->get_noticebbs_count($option);
        $data['result'] = $this->settingModels->get_noticebbs_limit($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/header');
        $this->load->view('info/notice', $data);
        $this->load->view('templates/footer');
    }
    public function notice_view() {
        $xid =  (int)$this->uri->segment(3);
        if (!$xid) {
            $this->session->set_flashdata('notice', '잘못된 접근입니다.');
            redirect('/info/notice');
        }
        $option = array(
            'xid' => $xid,
        );
        $this->load->model('settingModels');
        $result = $this->settingModels->get_noticebbs_by_admin($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '잘못된 접근입니다.');
            redirect('/info/notice');
        }

        $data['result'] = $result;
        $this->load->view('templates/header');
        $this->load->view('info/notice_view', $data);
        $this->load->view('templates/footer');
    }
    public function manager_auth() {
        header("Content-Type: application/json");

        $this->load->library('form_validation');
        $this->form_validation->set_rules('manager_mobile', 'manager_mobile', 'required');
        $this->form_validation->set_rules('where', 'Auth', 'required|in_list[main]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }

// error_log(print_r($_POST,1),0);
// error_log($this->session->userdata('userno'), 0);
// error_log($this->session->userdata('userid'), 0);
// error_log($this->session->userdata('level'), 0);

        if ((int)$this->session->userdata('level') < 3 || $this->session->userdata('authed_manager') === true) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '해당 서비스는 사용권한이 없습니다.';
            exit (json_encode($data_json));
        }

        $mobile = strip_phone(trim($this->input->post('manager_mobile')));
        $phone_type = substr($mobile, 0, 3);
        if ($phone_type == '010') {
            if (!valid_phone($mobile)) {
                $data_json['result'] = 'error';
                $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
                $data_json['message'] = '휴대폰번호 형식 오류입니다.';
                exit (json_encode($data_json));
            }
        }

        $manager_phone = trim($this->session->userdata('manager_phone'));
        if ($manager_phone == '') {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '휴대폰 번호가 등록되지 않았습니다. 관리자에게 문의하세요.';
            exit (json_encode($data_json));
        }
        $manager_phone_array = explode(',',$manager_phone);
        // error_log(print_r($manager_phone_array,1),0);
        if (!in_array($mobile, $manager_phone_array)) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '휴대폰 번호가 일치하지 않습니다. 관리자에게 문의하세요.';
            exit (json_encode($data_json));
        }


        $this->load->model('userModels');
        $option = array(
            'userid' => $this->session->userdata('userid'),
            'mobile' => $mobile,
            'type' => '1'
        );
        $result = $this->userModels->get_auth_mobile_for_admin_auth($option);
        if ($result != false) { //값이 존재할 경우
            $add_unixtime = strtotime($result->add_date);
            // if (time() < ($add_unixtime + 300)) { //5분
            if (time() < ($add_unixtime + 60)) { //1분
                $data_json['result'] = 'error';
                $data_json['authed_mobile'] = $result->mobile;
                $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
                $data_json['message'] = '인증번호가 이미 발송되었습니다. 1분 후 다시 인증요청이 가능합니다.';
                exit (json_encode($data_json));
            }
        }

        $rand_no = rand(111111, 999999);
        $option = array(
            'userid' => $this->session->userdata('userid'),
            'mobile' => $mobile,
            'memo' => '관리자인증',
            'auth_no' => $rand_no,
            'type' => '1',
        );

        $result = $this->userModels->add_auth_mobile($option);
        if (!$result) {
            $data_json['result'] = 'error';
            $data_json['authed_mobile'] = $result->mobile;
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '인증번호 발송에 실패했습니다.';
            exit (json_encode($data_json));
        }

        if ((int)$this->session->userdata('level') == 9) {
            $newdata = array(
                'authed_manager' => true,
            );
            $this->session->set_userdata($newdata, true);

            $data_json['result'] = 'complete';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '';
            exit (json_encode($data_json));
        } else {
            $newdata = array(
                'auth_phone' => $mobile,
            );
            $this->session->set_userdata($newdata, true);

            $data_json['result'] = 'success';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '인증번호를 발송하였습니다.';
            exit (json_encode($data_json));
        }
    }
    public function confirm_manager() {
        header("Content-Type: application/json");

        $this->load->library('form_validation');
        // $this->form_validation->set_rules('auth_phone', 'auth_phone', 'required');
        $this->form_validation->set_rules('auth_number', 'auth_number', 'required');
        $this->form_validation->set_rules('where', 'Auth', 'required|in_list[main]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }

        if ((int)$this->session->userdata('level') < 3 || $this->session->userdata('authed_manager') === true) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '해당 서비스는 사용권한이 없습니다.';
            exit (json_encode($data_json));
        }

        $this->load->model('userModels');
        $option = array(
            'userid' => $this->session->userdata('userid'),
            'mobile' => $this->session->userdata('auth_phone'),
            'type' => '1'
        );
// error_log(print_r($option,1),0);
        $result = $this->userModels->get_auth_mobile_for_admin_auth($option);
        if (!$result) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '먼저 문자로 인증요청을 한 후 관리자 권한을 요청하세요.';
            exit (json_encode($data_json));
        }
        if ($this->input->post('auth_number') != $result->auth_no) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '인증번호가 일치하지 않습니다.';
            exit (json_encode($data_json));
        }
        $add_unixtime = strtotime($result->add_date);
        if (time() > ($add_unixtime + 300)) { //5분
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '인증번호 유효시간이 초과되었습니다. 다시 인증해 주세요.';
            exit (json_encode($data_json));
        }
        $ano = (int)$result->ano;
        $option = array(
            'ano' => $ano
        );
        $result = $this->userModels->modify_auth_mobile($option);

        $newdata = array(
            'auth_phone' => '',
            'authed_manager' => true,
        );
        $this->session->set_userdata($newdata, true);

        $data_json['result'] = 'success';
        $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        $data_json['message'] = '정상적으로 인증되었습니다.';
        exit (json_encode($data_json));
    }
}
