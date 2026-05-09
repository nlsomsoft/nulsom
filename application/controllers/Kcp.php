<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kcp extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('form','url','kcp','cookie'));
    }
    public function kcp_req() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('param_opt_1', 'param_opt_1', 'required|numeric');
        $this->form_validation->set_rules('ordr_idxx', 'ordr_idxx', 'required|numeric');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect('/signup/join_agree');
        }
        $this->config->load('kcp',TRUE);
        $data['g_conf_home_dir'] = $this->config->item('g_conf_home_dir', 'kcp');
        $data['g_conf_site_cd'] = $this->config->item('g_conf_site_cd', 'kcp');
        $data['g_conf_web_siteid'] = $this->config->item('g_conf_web_siteid', 'kcp');
        $data['g_conf_ENC_KEY'] = $this->config->item('g_conf_ENC_KEY', 'kcp');
        $data['g_conf_Ret_URL'] = $this->config->item('g_conf_Ret_URL', 'kcp');
        $data['g_conf_gw_url'] = $this->config->item('g_conf_gw_url', 'kcp');

        $req_tx        = '';
        $site_cd       = '';
        $ordr_idxx     = $this->input->post('ordr_idxx');
        $year          = '';
        $month         = '';
        $day           = '';
        $user_name     = '';
        $sex_code      = '';
        $local_code    = '';
        $cert_able_yn  = '';
        $web_siteid    = '';
        $web_siteid_hashYN    = '';
        $up_hash       = '';
        /*------------------------------------------------------------------------*/
        /*  :: 전체 파라미터 남기기                                               */
        /*------------------------------------------------------------------------*/

        $ct_cert = new C_CT_CLI;
        $ct_cert->mf_clear();

        // request 로 넘어온 데이터 처리
        foreach($_POST as $nmParam => $valParam) {
            if ($nmParam == 'site_cd') {
                $site_cd = f_get_parm_str ( $valParam );
            }
            else if ($nmParam == 'req_tx') {
                $req_tx = f_get_parm_str ( $valParam );
            }
            else if ($nmParam == 'ordr_idxx') {
                $ordr_idxx = f_get_parm_str ( $valParam );
            }
            else if ($nmParam == 'user_name') {
                $user_name = f_get_parm_str ( $valParam );
            }
            else if ($nmParam == 'year') {
                $year = f_get_parm_int ( $valParam );
            }
            else if ($nmParam == 'month') {
                $month = f_get_parm_int ( $valParam );
            }
            else if ($nmParam == 'day') {
                $day = f_get_parm_int ( $valParam );
            }
            else if ($nmParam == 'sex_code') {
                $sex_code = f_get_parm_str ( $valParam );
            }
            else if ($nmParam == 'local_code') {
                $local_code = f_get_parm_str ( $valParam );
            }
            else if ($nmParam == 'cert_able_yn') {
                $cert_able_yn = f_get_parm_str ( $valParam );
            }
            else if ($nmParam == 'web_siteid_hashYN') {
                $web_siteid_hashYN = f_get_parm_str ( $valParam );
            }
            else if ($nmParam == 'web_siteid') {
                $web_siteid = f_get_parm_str ( $valParam );
            }
            // 인증창으로 넘기는 form 데이터 생성 필드
            $sbParam .= "<input type='hidden' name='" . $nmParam . "' value='" . f_get_parm_str( $valParam ) . "'/>";
        }

        if ($req_tx == 'cert') {
            if ( $web_siteid_hashYN != 'Y') {
                // web_siteid 검증을 안할시 해당 값을 ""(null) 로 설정
                $web_siteid = '';
            }
            if ($cert_able_yn == 'Y') {
                // input 박스 활성화시 up_hash 생성 데이터
                $hash_data = $site_cd                  .
                             $ordr_idxx                .
                             $web_siteid               .
                             ''                        .
                             '00'                      .
                             '00'                      .
                             '00'                      .
                             ''                        .
                             '';
            }
            else
            {
                // !!up_hash 데이터 생성시 주의 사항
                // year , month , day 가 비어 있는 경우 "00" , "00" , "00" 으로 설정이 됩니다
                // 그외의 값은 없을 경우 ""(null) 로 세팅하시면 됩니다.
                // up_hash 데이터 생성시 site_cd 와 ordr_idxx 는 필수 값입니다.
                $hash_data = $site_cd                  .
                             $ordr_idxx                .
                             $web_siteid               .
                             $user_name                .
                             f_get_parm_int ($year)    .
                             f_get_parm_int ($month)   .
                             f_get_parm_int ($day)     .
                             $sex_code                 .
                             $local_code;
            }
            $up_hash = $ct_cert->make_hash_data($data['g_conf_home_dir'], $data['g_conf_ENC_KEY'] ,$hash_data );
            // 인증창으로 넘기는 form 데이터 생성 필드 ( up_hash )
            $sbParam .= "<input type='hidden' name='up_hash' value='" . $up_hash . "'/>";
            // KCP 본인확인 라이브러리 버전 정보
            $sbParam .= "<input type='hidden' name='kcp_cert_lib_ver' value='" . $ct_cert->get_kcp_lib_ver($data['g_conf_home_dir']) . "'/>";
        }
        $ct_cert->mf_clear();
        $data['sbParam'] = $sbParam;
        $this->load->view('kcp/kcp_req', $data);
    }
    public function result() {
        $this->config->load('kcp',TRUE);
        $g_conf_home_dir = $this->config->item('g_conf_home_dir', 'kcp');
        $g_conf_site_cd = $this->config->item('g_conf_site_cd', 'kcp');
        $g_conf_web_siteid = $this->config->item('g_conf_web_siteid', 'kcp');
        $g_conf_ENC_KEY = $this->config->item('g_conf_ENC_KEY', 'kcp');
        $g_conf_Ret_URL = $this->config->item('g_conf_Ret_URL', 'kcp');
        $g_conf_gw_url = $this->config->item('g_conf_gw_url', 'kcp');
// error_log(print_r($_POST,1),0);
        $site_cd       = '';
        $ordr_idxx     = '';

        $cert_no       = '';
        $cert_enc_use  = '';
        $enc_info      = '';
        $enc_data      = '';
        $req_tx        = '';

        $enc_cert_data2 = '';
        $cert_info     = '';

        $tran_cd       = '';
        $res_cd        = '';
        $res_msg       = '';

        $dn_hash       = '';
        /*------------------------------------------------------------------------*/
        /*  :: 전체 파라미터 남기기                                               */
        /*------------------------------------------------------------------------*/

        // request 로 넘어온 값 처리
        foreach ($_POST as $nmParam => $valParam) {
            if ($nmParam == 'site_cd') {
                $site_cd = f_get_parm_str ( $valParam );
            }
            if ($nmParam == 'ordr_idxx') {
                $ordr_idxx = f_get_parm_str ( $valParam );
            }
            if ($nmParam == 'res_cd') {
                $res_cd = f_get_parm_str ( $valParam );
            }
            if ($nmParam == 'cert_enc_use') {
                $cert_enc_use = f_get_parm_str ( $valParam );
            }
            if ($nmParam == 'req_tx') {
                $req_tx = f_get_parm_str ( $valParam );
            }
            if ($nmParam == 'cert_no') {
                $cert_no = f_get_parm_str ( $valParam );
            }
            if ($nmParam == 'enc_cert_data2') {
                $enc_cert_data2 = f_get_parm_str ( $valParam );
            }
            if ($nmParam == 'dn_hash') {
                $dn_hash = f_get_parm_str ( $valParam );
            }
            if ($nmParam == 'param_opt_1') {
                $user_type = f_get_parm_str ( $valParam );
            }
            // 부모창으로 넘기는 form 데이터 생성 필드
            $sbParam .= "<input type='hidden' name='" . $nmParam . "' value='" . f_get_parm_str( $valParam ) . "'/>";
        }

        $ct_cert = new C_CT_CLI;
        $ct_cert->mf_clear();
        // 결과 처리
        if ($cert_enc_use == 'Y') {
            if ($res_cd == '0000') {
                // dn_hash 검증
                // KCP 가 리턴해 드리는 dn_hash 와 사이트 코드, 요청번호 , 인증번호를 검증하여
                // 해당 데이터의 위변조를 방지합니다
                 $veri_str = $site_cd.$ordr_idxx.$cert_no; // 사이트 코드 + 요청번호 + 인증거래번호
                if ($ct_cert->check_valid_hash ($g_conf_home_dir , $g_conf_ENC_KEY , $dn_hash , $veri_str) != "1" ) {
                    // 검증 실패시 처리 영역
                    // echo "dn_hash 변조 위험있음";
                    // 오류 처리 ( dn_hash 변조 위험있음)
                    $ct_cert->mf_clear();
                    error_log('dn_hash', 0);
                    $data['sbParam']  = $sbParam;
                    $data['sbParam'] .= "<input type='hidden' name='site_auth' value='1000' />";
                    $this->load->view('kcp/kcp_res', $data);
                }
                else
                {
                    // 인증데이터 복호화 함수
                    // 해당 함수는 암호화된 enc_cert_data2 를
                    // site_cd 와 cert_no 를 가지고 복화화 하는 함수 입니다.
                    // 정상적으로 복호화 된경우에만 인증데이터를 가져올수 있습니다.
                    $opt = '1' ; // 복호화 인코딩 옵션 ( UTF - 8 사용시 "1" )
                    $ct_cert->decrypt_enc_cert( $g_conf_home_dir , $g_conf_ENC_KEY , $site_cd , $cert_no , $enc_cert_data2 , $opt );

                    $comm_id = $ct_cert->mf_get_key_value('comm_id');
                    $phone_no = $ct_cert->mf_get_key_value('phone_no');
                    $user_name = $ct_cert->mf_get_key_value('user_name');
                    $birth_day = $ct_cert->mf_get_key_value('birth_day');
                    $sex_code = $ct_cert->mf_get_key_value('sex_code');
                    $local_code = $ct_cert->mf_get_key_value('local_code');
                    $ci_url = $ct_cert->mf_get_key_value('ci_url');
                    $di_url = $ct_cert->mf_get_key_value('di_url');
                    $web_siteid = $ct_cert->mf_get_key_value('web_siteid');
                    $res_cd = $ct_cert->mf_get_key_value('res_cd');
                    $res_msg = $ct_cert->mf_get_key_value('res_msg');
                    $ct_cert->mf_clear();

                    $adult_date = date('Ymd', strtotime("-19 years"));
                    $check_birth_date = (int)$birth_day;
                    if ($check_birth_date >= $adult_date) {
                        $this->session->set_flashdata('notice', '미성년자(만 19세)는 가입할 수 없습니다. 관리자에게 문의하세요.');
                        $ct_cert->mf_clear();
                        error_log('auth failed, check birth date');
                        $data['sbParam']  = $sbParam;
                        $data['sbParam'] .= "<input type='hidden' name='site_auth' value='1000' />";
                        $this->load->view('kcp/kcp_res', $data);
                    }
                    else
                    {
                        $this->load->model('userModels');
                        $option = array(
                            'ci' => $ci_url,
                            'di' => $di_url,
                            'storename' => $_SERVER['STORENAME'],
                        );
                        $kcp_cnt = (int)$this->userModels->get_kcp_available_count($option);
                        if ($kcp_cnt > 2) { //3명까지 가능
                            $this->session->set_flashdata('notice', '이미 가입된 회원입니다. 관리자에게 문의 하십시오.');
                            $ct_cert->mf_clear();
                            error_log('auth failed, already added user');
                            $data['sbParam']  = $sbParam;
                            $data['sbParam'] .= "<input type='hidden' name='site_auth' value='1000' />";
                            $this->load->view('kcp/kcp_res', $data);
                        }
                        else
                        {
                            $kno = 0;
                            if (!$kno) {
                                $option = array(
                                    'storename' => $_SERVER['STORENAME'],
                                    'comm_id' => $comm_id,
                                    'phone_no' => $phone_no,
                                    'user_name' => $user_name,
                                    'birth_day' => $birth_day,
                                    'sex_code' => $sex_code,
                                    'local_code' => $local_code,
                                    'ci_url' => $ci_url,
                                    'di_url' => $di_url,
                                    'web_siteid' => $web_siteid,
                                    'res_cd' => $res_cd,
                                    'res_msg' => $res_msg,
                                );
                                $kno = $this->userModels->add_kcp($option);
                            }
                            // signup_type :1 개인, 2 기업
                            // $user_type = get_cookie('user_type');
                            // $user_type = '1';
                            $newdata = array(
                                'user_type' => $user_type,
                                'kmc_name' => $user_name,
                                'kmc_mobile' => $phone_no,
                                'kmc_kno' => $kno,
                                'kmc_table' => 'kcp_logs',
                            );
                            $this->session->set_userdata($newdata, true);
                            $ct_cert->mf_clear();
                            $data['sbParam']  = $sbParam;
                            $data['sbParam'] .= "<input type='hidden' name='site_auth' value='0000' />";
                            $this->load->view('kcp/kcp_res', $data);
                        }
                    }
                }
            }
            else/*if( res_cd.equals( "0000" ) != true )*/
            {
                $ct_cert->mf_clear();
                error_log('auth failed');
                $data['sbParam']  = $sbParam;
                $data['sbParam'] .= "<input type='hidden' name='site_auth' value='1000' />";
                $this->load->view('kcp/kcp_res', $data);
               // 인증실패
            }
        }
    }
}
