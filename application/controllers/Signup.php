<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {
    private $current_time;

    function __construct() {
        parent::__construct();
        $this->current_time = date('YmdHis');
        $this->load->helper(array('form','url','kmcis','kcp'));
    }
    public function pass() {
        // $val = '';
        // echo (password_hash($val, PASSWORD_BCRYPT));
    }
    public function check_id() {
        header("Content-Type: application/json");

        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_user_id', 'User Id', 'required|min_length[6]|max_length[20]|is_unique[users.userid]');
        $this->form_validation->set_rules('where', 'Auth', 'required|in_list[signup]');

        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '사용할 수 없는 아이디 입니다.';
            exit (json_encode($data_json));
        }
        else {
            $data_json['result'] = 'success';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '사용 가능한 아이디 입니다.';
            exit (json_encode($data_json));
        }
    }
    //kcp 인증모듈
    public function join_info1() {
        if ($this->session->flashdata('is_agree') !== true) {
            redirect('/signup/join_agree');
        }
        $this->config->load('kcp',TRUE);
        $data['g_conf_home_dir'] = $this->config->item('g_conf_home_dir', 'kcp');
        $data['g_conf_site_cd'] = $this->config->item('g_conf_site_cd', 'kcp');
        $data['g_conf_web_siteid'] = $this->config->item('g_conf_web_siteid', 'kcp');
        $data['g_conf_ENC_KEY'] = $this->config->item('g_conf_ENC_KEY', 'kcp');
        $data['g_conf_Ret_URL'] = $this->config->item('g_conf_Ret_URL', 'kcp');
        $data['g_conf_gw_url'] = $this->config->item('g_conf_gw_url', 'kcp');

        $this->load->view('templates/header');
        $this->load->view('signup/join_info1', $data);
        $this->load->view('templates/footer');
    }
    //kmcis 인증모듈
    public function join_info() {
        if ($this->session->flashdata('is_agree') !== true) {
            redirect('/signup/join_agree');
        }
        //01.입력값 변수로 받기
        $cpId       = KMCIS_CPID;   // 회원사ID
        $urlCode    = KMCIS_URLCODE;     // URL 코드
        $certNum    = $this->current_time.rand(100, 999);     // 요청번호
        $date       = $this->current_time;        // 요청일시
        $certMet    = 'M';
        $birthDay   = $_REQUEST['birthDay'];    // 생년월일
        $gender     = $_REQUEST['gender'];      // 성별
        $name       = $_REQUEST['name'];        // 성명
        $phoneNo    = $_REQUEST['phoneNo'];     // 휴대폰번호
        $phoneCorp  = $_REQUEST['phoneCorp'];   // 이동통신사
        $nation     = $_REQUEST['nation'];      // 내외국인 구분
        $plusInfo   = $_REQUEST['plusInfo'];    // 추가DATA정보
        $base_url  = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
        $base_url .= "://".$_SERVER['HTTP_HOST'].'/';
        $tr_url     = $base_url.'kmcis/result';      // 본인인증 결과수신 POPUP URL
        $tr_add     = '';      // IFrame사용여부
        $extendVar  = "0000000000000000";       // 확장변수

        // [ certNum 주의사항 ]--------------------------------------------------------------------------------------
        // 1. 본인인증 결과값 복호화를 위한 키로 활용되므로 중요함.
        // 2. 본인인증 요청시 중복되지 않게 생성해야함. (예-시퀀스번호)
        // 3. certNum값 생성 후 쿠키 또는 Session에 저장한 후 본인인증 결과값 수신 후 복호화키로 사용함.
        // 4. 아래 샘플은 쿠키를 사용하지 않았음.
        //----------------------------------------------------------------------------------------------------------
        $name = str_replace(" ", "+", $name) ;  //성명에 space가 들어가는 경우 "+"로 치환하여 암호화 처리
        //02. tr_cert 데이터변수 조합 (서버로 전송할 데이터 "/"로 조합)
        $tr_cert = $cpId . "/" . $urlCode . "/" . $certNum . "/" . $date . "/" . $certMet . "/" . $birthDay . "/" . $gender . "/" . $name . "/" . $phoneNo . "/" . $phoneCorp . "/" . $nation . "/" . $plusInfo . "/" . $extendVar;

        //암호화모듈 호출
        if (extension_loaded('ICERTSecu')) {
            //03. 1차암호화
            $enc_tr_cert = ICertSeed(1,0,'',$tr_cert);
            //04. 변조검증값 생성
            $enc_tr_cert_hash = ICertHMac($enc_tr_cert);
            //05. 2차암호화
            $enc_tr_cert = $enc_tr_cert . "/" . $enc_tr_cert_hash . "/" . "0000000000000000";
            $enc_tr_cert = ICertSeed(1,0,'',$enc_tr_cert);
        } else {
           $this->session->set_flashdata('notice', '암호화 모듈 실패입니다.');
           redirect('/signup/join_agree');
        }

        $data['tr_cert'] = $enc_tr_cert;
        $data['tr_url'] = $tr_url;
        $data['tr_add'] = $tr_add;
        $this->load->view('templates/header');
        $this->load->view('signup/join_info', $data);
        $this->load->view('templates/footer');
    }
    public function join_agree() {
        if (1) {
            $this->session->set_flashdata('notice', '해당 서비스 중지로 인하여 회원가입 할 수 없습니다. 관리자에게 문의하세요.');
            redirect('/');
        }

        $this->session->set_flashdata('is_agree', true);
        $this->load->view('templates/header');
        $this->load->view('signup/join_agree');
        $this->load->view('templates/footer');
    }
    public function join_form() {
        $this->load->view('templates/header');
        if ($this->session->userdata('kmc_name') == '' ||
            $this->session->userdata('kmc_mobile') == '') {
            redirect('/signup/join_agree');
        }
        // if ($_SERVER['STORENAME'] == 'sowkorea') {
            $this->load->model('userModels');
            $option = array(
                'storename' => $_SERVER['STORENAME'],
                'type' => '1',
            );
            $data['group_list'] = $this->userModels->get_group_list($option);
        // }
        if ($this->session->userdata('user_type') == '1') {
            $this->load->view('signup/join_form_normal', $data);
        } else {
            $this->load->view('signup/join_form_business', $data);
        }
        $this->load->view('templates/footer');
    }
    public function welcome() {
        $this->load->view('templates/header');
        $this->load->view('signup/welcome');
        $this->load->view('templates/footer');
    }
    public function logout() {
        $this->session->sess_destroy();
        $cache_key = 'sow_name'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_send'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_photo'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_elect_total'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_elect_total_count'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_elect_remain'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_elect_name'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        $cache_key = 'sow_elect_campaign'.$this->session->userdata('uniqueno');
        $this->cache->redis->save($cache_key, '', 60);
        redirect('/');
    }
    public function register() {
        if ($this->input->post('user_type') != $this->session->userdata('user_type') ||
            $this->input->post('kmc_name') != $this->session->userdata('kmc_name') ||
            $this->input->post('kmc_mobile') != $this->session->userdata('kmc_mobile') ||
            $this->input->post('kmc_kno') != $this->session->userdata('kmc_kno') ||
            $this->input->post('kmc_table') != $this->session->userdata('kmc_table')) {
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect('/signup/join_agree');
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('user_type', 'Kmc User Type', 'required|numeric');
        $this->form_validation->set_rules('kmc_mobile', 'Kmc Mobile', 'required|numeric');
        $this->form_validation->set_rules('kmc_name', 'Kmc Name', 'required|min_length[2]|max_length[5]');
        $this->form_validation->set_rules('kmc_kno', 'Kmc kno', 'required');
        $this->form_validation->set_rules('ipt_user_id', 'User Id', 'required|min_length[6]|max_length[20]|is_unique[users.userid]');
        $this->form_validation->set_rules('ipt_password', 'Password', 'required|min_length[8]|max_length[20]|matches[ipt_re_password]');
        $this->form_validation->set_rules('ipt_re_password', 'Re_password', 'required');
        $this->form_validation->set_rules('ipt_email', 'Email', 'valid_email');
        if ($this->input->post('user_type') == '2') {
            $this->form_validation->set_rules('ipt_com_name', 'Company name', 'required|min_length[2]|max_length[10]');
            $this->form_validation->set_rules('ipt_com_number', 'Company number', 'required|min_length[10]|max_length[12]');
        }
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect('/signup/join_form');
        }
        $ipt_phone = str_replace('-', '', $this->input->post('ipt_phone'));
        $groupno = (int)$this->input->post('sel_groupno');
        if ($groupno > 1000) $groupno = 0;

        $this->load->model('userModels');
        $option = array(
            'storename' => $_SERVER['STORENAME'],
        );
        $result = $this->userModels->get_store($option);
        $storeno = $result->storeno;
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
        if (!$storeno) {
            $this->session->set_flashdata('notice', '시스템 오류입니다. E:[STORENAME]');
            redirect('/signup/join_form');
        }
        if ($_SERVER['GROUPNAME'] != '') {
            $option = array(
                'group_name' => $_SERVER['GROUPNAME'],
            );
            $result = $this->userModels->get_group($option);
            $groupno = $result->groupno;
            if (!$groupno) {
                $this->session->set_flashdata('notice', '시스템 오류입니다. E:[GROUPNAME]');
                redirect('/signup/join_form');
            }
        }

        $level = ($this->input->post('user_type') == '1' ? '1' : '0');
        $ipt_password = trim($this->input->post('ipt_password'));
        $option = array(
            'storeno'   => (int)$storeno,
            'groupno'   => (int)$groupno,
            'user_type' => $this->input->post('user_type'),
            'userid'    => trim($this->input->post('ipt_user_id')),
            'realname'  => $this->input->post('kmc_name'),
            'mobile'    => $this->input->post('kmc_mobile'),
            'phone'     => $ipt_phone,
            'email'     => $this->input->post('ipt_email'),
            'com_name'  => $this->input->post('ipt_com_name'),
            'com_number'=> $this->input->post('ipt_com_number'),
            'password'  => password_hash($ipt_password, PASSWORD_BCRYPT),
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
            'level'     => $level,
            'register_ip' => $this->input->ip_address(),
        );

        $cb_status = ($this->input->post('user_type') == '1' ? '3' : '1');
        $option1 = array(
            'userno' => (int)$userno,
            'userid' => trim($this->input->post('ipt_user_id')),
            'name' => '기본발신번호',
            'callback' => $this->input->post('kmc_mobile'),
            'cert_type' => '1',
            'authcode' => '',
            'status' => $cb_status,
        );
        $option2 = array(
            'kmc_kno' => $this->input->post('kmc_kno'),
            'kmc_table' => $this->input->post('kmc_table'),
        );

        $kcpno = $this->userModels->get_kcp_number($option2);
        if ($kcpno->userid != '') {
            $this->session->set_flashdata('notice', '이미 가입된 회원입니다.');
            $newdata = array(
                'user_type' => '',
                'kmc_mobile' => '',
                'kmc_kno' => '',
            );
            $this->session->set_userdata($newdata, true);
            redirect('/signup/join_agree');
        }

        $cbno = $this->userModels->is_callback_in_store_unique($option1);
        if ($cbno->callback != '') {
            $this->session->set_flashdata('notice', '이미 존재하는 발신번호입니다. 관리자에게 문의하세요.');
            $newdata = array(
                'user_type' => '',
                'kmc_mobile' => '',
                'kmc_kno' => '',
            );
            $this->session->set_userdata($newdata, true);
            redirect('/signup/join_agree');
        }

        $userno = $this->userModels->add_user($option,$option1,$option2);
        if (!$userno) {
            $this->session->set_flashdata('notice', '회원가입에 실패 했습니다.');
            redirect('/signup/join_form');
        }

        $newdata = array(
            'user_type' => '',
            'kmc_mobile' => '',
            'kmc_kno' => '',
        );
        $this->session->set_userdata($newdata, true);

        // $this->session->set_flashdata('notice', '회원가입에 성공했습니다.');
        redirect('/signup/welcome');
    }
    public function login() {
        if ($this->session->userdata('logged_in') === true) {
            redirect('/');
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user_id', 'User Id', 'required|min_length[6]|max_length[20]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[30]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect('/');
        }
        $ipt_password = trim($this->input->post('password'));

        if ($this->input->post('user_id') != 'digitalshin') {
            $this->session->set_flashdata('notice', '해당 서비스 중지로 인하여 로그인 할 수 없습니다. 관리자에게 문의하세요.');
            redirect('/');
        }

        $this->load->model('userModels');
        $data = array(
            'userid' => trim($this->input->post('user_id'))
        );
        $result = $this->userModels->get_user_all_info($data);
        if ($result->state == '1' || $result->state == '2') {
            $this->session->set_flashdata('notice', '해당 아이디는 로그인 할 수 없는 아이디 입니다. 관리자에게 문의하세요.');
            redirect('/');
        }
        $db_hash = trim($result->password);
        if (!password_verify($ipt_password, $db_hash)) {
            $this->session->set_flashdata('notice', '아이디,비밀번호가 일치하지 않습니다.');
            redirect('/');
        }
        if ($_SERVER['STORENAME'] != $result->storename || ($_SERVER['GROUPNAME'] != '' && $_SERVER['GROUPNAME'] != $result->groupid)) {
            $this->session->set_flashdata('notice', '로그인에 실패 했습니다. 관리자에게 문의하세요.');
            redirect('/');
        }

        if ($result->level == '0') {
            $this->session->set_flashdata('notice', '관리자 승인 후 로그인이 가능합니다. 관리자에게 문의하세요.');
            redirect('/');
        }

        // $this->load->helper('cache');
        $this->session->set_userdata(set_session_userdata($result, true));
        // if ($result->phone_080 == '0808000419' && $result->phone_ext == '') {
        //     $data = array(
        //         'phone_ext' => (int)($result->userno) + 100,
        //         'login_date' => $this->current_time
        //     );
        // } else {
        //     $data = array(
        //         'ip' => $this->input->ip_address(),
        //         'login_date' => $this->current_time
        //     );
        // }

        $data = array(
            'ip' => $this->input->ip_address(),
            'login_date' => $this->current_time
        );
        $result = $this->userModels->modify_user($data);
        $callback_list = $this->userModels->get_callback_to_session();
        $this->session->set_userdata('callback', $callback_list);

        $data = array(
            'userid' => trim($this->input->post('user_id')),
            'memo' => '로그인',
            'ip' => $this->input->ip_address(),
        );
        $result = $this->userModels->add_users_history($data);

        redirect('/');
    }
    public function terms() {
        $data['person'] = $this->uri->segment(3);
        $this->load->view('templates/header');
        $this->load->view('signup/terms',$data);
        $this->load->view('templates/footer');
    }
    public function id_form() {
        $this->load->view('templates/header');
        $this->load->view('signup/id_form');
        $this->load->view('templates/footer');
    }
    public function passwd_form() {
        $this->load->view('templates/header');
        $this->load->view('signup/passwd_form');
        $this->load->view('templates/footer');
    }
    public function find_id() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_name', 'Ipt Name', 'required|min_length[2]|max_length[20]');
        $this->form_validation->set_rules('ipt_mobile', 'Ipt Mobile', 'required|max_length[13]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect('/signup/id_form');
        }
        $ipt_mobile = str_replace('-', '', $this->input->post('ipt_mobile'));

        $this->load->model('userModels');
        $option = array(
            'realname' => $this->input->post('ipt_name'),
            'mobile' => $ipt_mobile,
        );
        $data['result'] = $this->userModels->get_user($option);

        $this->load->helper('mydate');
        $this->load->view('templates/header');
        $this->load->view('signup/result_id', $data);
        $this->load->view('templates/footer');
    }
    public function find_passwd() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_userid', 'Ipt UserId', 'required|min_length[6]|max_length[20]');
        $this->form_validation->set_rules('ipt_name', 'Ipt Name', 'required|min_length[2]|max_length[20]');
        $this->form_validation->set_rules('ipt_mobile', 'Ipt Mobile', 'required|max_length[13]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect('/signup/passwd_form');
        }
        $ipt_mobile = str_replace('-', '', $this->input->post('ipt_mobile'));

        $this->load->model('userModels');
        $option = array(
            'userid' => $this->input->post('ipt_userid'),
            'realname' => $this->input->post('ipt_name'),
            'mobile' => $ipt_mobile,
        );
        $result = $this->userModels->get_user($option);
        if (!$result) {
            redirect('/signup/result_passwd/0');
        }

        $option = array(
            'userid' => $this->input->post('ipt_userid'),
            'type' => '2'
        );
        $result = $this->userModels->get_auth_mobile($option);
        if ($result != false) { //값이 존재할 경우
            $add_unixtime = strtotime($result->add_date);
            if (time() < ($add_unixtime + 120)) {
                $this->session->set_flashdata('notice', '인증번호가 이미 발송되었습니다. 인증번호는 2분간 유효합니다.');
                redirect('/signup/passwd_form');
            }
        }

        $rand_no = rand(111111, 999999);
        $option = array(
            'userid' => $this->input->post('ipt_userid'),
            'mobile' => $ipt_mobile,
            'auth_no' => $rand_no,
            'memo' => '비밀번호재설정',
            'type' => '2',
        );
        $result = $this->userModels->add_auth_mobile($option);

        $this->load->driver('cache');
        $cache_key = 'sow'.date('dHis').'HASH'.rand(10,99);
        $this->cache->redis->save($cache_key, $this->input->post('ipt_userid'), 300); //5분 후 자동삭제
        redirect("/signup/result_passwd/{$cache_key}");
    }
    public function result_passwd() {
        $data['auth_key'] = $this->uri->segment(3);
        $this->load->view('templates/header');
        $this->load->view('signup/result_passwd', $data);
        $this->load->view('templates/footer');
    }
    public function auth_nums() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_auth_num', 'Ipt Auth Num', 'required|max_length[6]');
        $this->form_validation->set_rules('auth_key', 'Auth Key', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect('/signup/result_passwd/'.$this->input->post('auth_key'));
        }

        $this->load->driver('cache');
        $cache_key = $this->input->post('auth_key');

        $this->load->model('userModels');
        $option = array(
            'userid' => $this->cache->redis->get($cache_key),
            'type' => '2'
        );
        $result = $this->userModels->get_auth_mobile($option);
        // if (!$result) {
        //     $this->session->set_flashdata('notice', '잘못된 접근입니다. 다시 시도해 주세요.');
        //     redirect('/signup/passwd_form');
        // }
        $ano = (int)$result->ano;
        if (!$ano) {
            $this->session->set_flashdata('notice', '잘못된 접근입니다. 다시 시도해 주세요.');
            redirect('/signup/passwd_form');
        }

        $add_unixtime = strtotime($result->add_date);
        if (time() > ($add_unixtime + 120)) {
            $this->session->set_flashdata('notice', '인증번호 유효시간(2분)이 초과되었습니다. 다시 인증해 주세요.');
            redirect('/signup/passwd_form');
        }
        if ($this->input->post('ipt_auth_num') != $result->auth_no) {
            $this->session->set_flashdata('notice', '인증번호가 일치하지 않습니다.');
            redirect('/signup/result_passwd/'.$this->input->post('auth_key'));
        }

        $option = array(
            'ano' => $ano,
            'userid' => $this->cache->redis->get($cache_key),
            'type' => '2',
            'state' => '1',
        );
        $result = $this->userModels->modify_auth_mobile_authed($option);

        $cache_key_val = $this->cache->redis->get($cache_key).'|'.time();
        $this->cache->redis->save($cache_key, $cache_key_val, 300); //5분 후 자동삭제

        redirect('/signup/newpasswd_form/'.$cache_key);
    }
    public function newpasswd_form() {
        $auth_key = trim($this->uri->segment(3));
        if (!$auth_key) {
            $this->session->set_flashdata('notice', '잘못된 접근입니다. 다시 시도해 주세요.');
            redirect('/signup/passwd_form');
        }

        $this->load->driver('cache');
        $auth_val = trim($this->cache->redis->get($auth_key));
        if (!$auth_val) {
            $this->session->set_flashdata('notice', '잘못된 접근입니다. 다시 시도해 주세요.');
            redirect('/signup/passwd_form');
        }

        $auth_array = array();
        $auth_array = explode('|', $auth_val);
        $userid = $auth_array[0];
        $auth_time = (int)$auth_array[1];

        if (!$userid || !$auth_time) {
            $this->session->set_flashdata('notice', '잘못된 접근입니다. 다시 시도해 주세요.');
            redirect('/signup/passwd_form');
        }

        if ((time() - $auth_time) > 120) {
            $this->session->set_flashdata('notice', '인증번호 유효시간(2분)이 초과되었습니다. 다시 인증해 주세요.');
            redirect('/signup/passwd_form');
        }


        $this->load->model('userModels');
        $option = array(
            'userid' => $userid,
            'type' => '2',
        );

        $result = $this->userModels->get_auth_mobile_authed($option);
        // if (!$result) {
        //     $this->session->set_flashdata('notice', '비밀번호 재설정 중 오류가 발생했습니다. 다시 시도해 주세요.');
        //     redirect('/signup/passwd_form');
        // }
        $state = (int)$result->state;
        if ($state != 1) {
            $this->session->set_flashdata('notice', '잘못된 접근입니다. 다시 시도해 주세요.');
            redirect('/signup/passwd_form');
        }
        $ano = (int)$result->ano;
        $add_unixtime = strtotime($result->add_date);
        if (time() > ($add_unixtime + 120)) {
            $this->session->set_flashdata('notice', '인증번호 유효시간(2분)이 초과되었습니다. 다시 인증해 주세요.');
            redirect('/signup/passwd_form');
        }

        $data['auth_key'] = $auth_key;
        $data['userid'] = $userid;
        $this->load->view('templates/header');
        $this->load->view('signup/newpasswd_form', $data);
        $this->load->view('templates/footer');
    }
    public function newpasswd() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('auth_key', 'Auth Key', 'required');
        $this->form_validation->set_rules('ipt_new_passwd', 'Ipt New Passwd', 'required|min_length[8]|max_length[20]');
        $this->form_validation->set_rules('ipt_re_passwd', 'Ipt Re Password', 'required|min_length[8]|max_length[20]|matches[ipt_new_passwd]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect('/signup/result_passwd/'.$this->input->post('auth_key'));
        }

        $auth_key = $this->input->post('auth_key');
        if (!$auth_key) {
            $this->session->set_flashdata('notice', '잘못된 접근입니다. 다시 시도해 주세요.');
            redirect('/signup/passwd_form');
        }

        $this->load->driver('cache');
        $auth_val = trim($this->cache->redis->get($auth_key));
        if (!$auth_val) {
            $this->session->set_flashdata('notice', '잘못된 접근입니다. 다시 시도해 주세요.');
            redirect('/signup/passwd_form');
        }

        $auth_array = array();
        $auth_array = explode('|', $auth_val);
        $userid = $auth_array[0];
        $auth_time = (int)$auth_array[1];

        if (!$userid || !$auth_time) {
            $this->session->set_flashdata('notice', '잘못된 접근입니다. 다시 시도해 주세요.');
            redirect('/signup/passwd_form');
        }

        if ((time() - $auth_time) > 120) {
            $this->session->set_flashdata('notice', '인증번호 유효시간(2분)이 초과되었습니다. 다시 인증해 주세요.');
            redirect('/signup/passwd_form');
        }


        $this->load->model('userModels');
        $option = array(
            'userid' => $userid,
            'type' => '2'
        );
        $result = $this->userModels->get_auth_mobile_authed($option);
        // if (!$result) {
        //     $this->session->set_flashdata('notice', '비밀번호 재설정 중 오류가 발생했습니다. 다시 시도해 주세요.');
        //     redirect('/signup/passwd_form');
        // }
        $state = (int)$result->state;
        if ($state != 1) {
            $this->session->set_flashdata('notice', '잘못된 접근입니다. 다시 시도해 주세요.');
            redirect('/signup/passwd_form');
        }
        $ano = (int)$result->ano;
        $add_unixtime = strtotime($result->add_date);
        if (time() > ($add_unixtime + 120)) {
            $this->session->set_flashdata('notice', '인증번호 유효시간(2분)이 초과되었습니다. 다시 인증해 주세요.');
            redirect('/signup/passwd_form');
        }

        $option = array(
            'ano' => $ano,
            'userid' => $userid,
            'type' => '2',
            'state' => '2',
        );
        $result = $this->userModels->modify_auth_mobile_authed($option);

        $option = array(
            'userid' => $userid,
            'password' => password_hash($this->input->post('ipt_new_passwd'), PASSWORD_BCRYPT),
            'mod_date' => $this->current_time
        );
        $result = $this->userModels->modify_password($option);

        $data = array(
            'userid' => $userid,
            'memo' => '비밀번호찾기',
            'ip' => $this->input->ip_address(),
        );
        $result = $this->userModels->add_users_history($data);

        $this->session->set_flashdata('notice', '정상적으로 변경되었습니다.');
        $this->cache->redis->save($auth_key, '', 60);
        redirect('/signup/passwd_form');
    }
}
