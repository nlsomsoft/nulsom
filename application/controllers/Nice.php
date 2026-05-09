<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nice extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('form','url','nice','cookie'));
    }
    public function nice_succ() {
        // session_start();
        $sitecode = NICE_SITECODE;
        $sitepasswd = NICE_SITEPASS;
        $cb_encode_path = $_SERVER['DOCUMENT_ROOT'].'/nice/CPClient_linux_x64';

        $enc_data = $_REQUEST["EncodeData"];        // 암호화된 결과 데이타

        $error_flag = 0;
        if (preg_match('~[^0-9a-zA-Z+/=]~', $enc_data, $match)) {
            // echo "입력 값 확인이 필요합니다 : ".$match[0];
            // exit;
            $error_flag = 1;
        }
        if (base64_encode(base64_decode($enc_data))!=$enc_data) {
            // echo "입력 값 확인이 필요합니다";
            // exit;
            $error_flag = 1;
        }

        // default value
        $data['errcode'] = '1';
        $data['ret_msg'] = '본인인증이 실패입니다.';

        if ($error_flag == 0 && $enc_data != "") {
            $plaindata = `$cb_encode_path DEC $sitecode $sitepasswd $enc_data`;     // 암호화된 결과 데이터의 복호화
            // echo "[plaindata]  " . $plaindata . "<br>";
// error_log($plaindata, 0);
            if ($plaindata == -1){
                $returnMsg  = "암/복호화 시스템 오류";
            }else if ($plaindata == -4){
                $returnMsg  = "복호화 처리 오류";
            }else if ($plaindata == -5){
                $returnMsg  = "HASH값 불일치 - 복호화 데이터는 리턴됨";
            }else if ($plaindata == -6){
                $returnMsg  = "복호화 데이터 오류";
            }else if ($plaindata == -9){
                $returnMsg  = "입력값 오류";
            }else if ($plaindata == -12){
                $returnMsg  = "사이트 비밀번호 오류";
            }else{
                // 복호화가 정상적일 경우 데이터를 파싱합니다.
                $ciphertime = `$cb_encode_path CTS $sitecode $sitepasswd $enc_data`;    // 암호화된 결과 데이터 검증 (복호화한 시간획득)
            
                $req_seq = GetValue($plaindata , "REQ_SEQ");
                $res_seq = GetValue($plaindata , "RES_SEQ");
                $auth_type = GetValue($plaindata , "AUTH_TYPE");
                // $name = GetValue($plaindata , "NAME");
                $user_name = urldecode(GetValue($plaindata , "UTF8_NAME")); //charset utf8 사용시 주석 해제 후 사용
                $birthdate = GetValue($plaindata , "BIRTHDATE");
                $gender = GetValue($plaindata , "GENDER");
                $nainfo = GetValue($plaindata , "NATIONALINFO");  //내/외국인정보(사용자 매뉴얼 참조)
                $di = GetValue($plaindata , "DI");
                $ci = GetValue($plaindata , "CI");
                $mobile_no = GetValue($plaindata , "MOBILE_NO");
                $mobile_co = GetValue($plaindata , "MOBILE_CO");

                // $REQ_SEQ = $_SESSION["REQ_SEQ"];
                $REQ_SEQ = $this->session->userdata('REQ_SEQ',true);
// error_log('01:'.$req_seq, 0);
// error_log('02:'.$res_seq, 0);
// error_log('03:'.$auth_type, 0);
// error_log('04:'.$user_name, 0);
// error_log('05:'.$birthdate, 0);
// error_log('06:'.$gender, 0);
// error_log('07:'.$nainfo, 0);
// error_log('08:'.$di, 0);
// error_log('09:'.$ci, 0);
// error_log('10:'.$mobile_no, 0);
// error_log('11:'.$mobile_co, 0);
// error_log('12:'.$REQ_SEQ, 0);
                $ci = '';
                if (strcmp($REQ_SEQ, $req_seq) != 0)
                {
                    // echo "세션값이 다릅니다. 올바른 경로로 접근하시기 바랍니다.<br>";
                    $req_seq = "";
                    $res_seq = "";
                    $auth_type = "";
                    $user_name = "";
                    $birthdate = "";
                    $gender = "";
                    $nainfo = "";
                    $di = "";
                    $ci = "";
                    $mobile_no = "";
                    $mobile_co = "";
                }
                else
                {
                    $adult_date = date('Ymd', strtotime("-19 years"));
                    $check_birth_date = (int)$birthdate;
                    if ($check_birth_date >= $adult_date) {
                        $data['errcode'] = '1';
                        $data['ret_msg'] = '미성년자(만 19세)는 가입할 수 없습니다. 관리자에게 문의하세요.';
                    }
                    else
                    {
                        $this->load->model('userModels');
                        $option = array(
                            'ci' => $ci,
                            'di' => $di,
                            'storename' => $_SERVER['STORENAME'],
                        );
                        $kmcis = $this->userModels->get_nice($option);
                        if ($kmcis->userid != '') {
                            // $this->session->set_flashdata('notice', '이미 가입된 회원입니다.');
                            error_log('auth failed', 0);
                            $data['errcode'] = '1';
                            $data['ret_msg'] = '이미 가입된 회원입니다.';
                        }
                        else
                        {
                            $kno = $kmcis->kno;
                            if (!$kno) {
                                $option = array(
                                    'storename' => $_SERVER['STORENAME'],
                                    'req_seq' => $req_seq,
                                    'res_seq' => $res_seq,
                                    'auth_type' => $auth_type,
                                    'user_name' => $user_name,
                                    'birthdate' => $birthdate,
                                    'gender' => $gender,
                                    'nainfo' => $nainfo,
                                    'di' => $di,
                                    'ci' => $ci,
                                    'mobile_no' => $mobile_no,
                                    'mobile_co' => $mobile_co,
                                );
                                $kno = $this->userModels->add_nice($option);
                            }
                            // signup_type :1 개인, 2 기업
                            $user_type = get_cookie('user_type');
                            // $user_type = '1';
                            if (!$user_type) $user_type = '1';
                            $newdata = array(
                                'user_type' => $user_type,
                                'kmc_name' => $user_name,
                                'kmc_mobile' => $mobile_no,
                                'kmc_kno' => $kno,
                                'kmc_table' => 'nice_logs',
                            );
                            $this->session->set_userdata($newdata, true);
                            $data['errcode'] = '0';
                            $data['ret_msg'] = '본인인증이 완료 되었습니다.';
                        }
                    }
                }
            }
        }
        $this->load->view('nice/nice_succ', $data);
    }
    public function nice_fail() {
        //**************************************************************************************
        //NICE평가정보 Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED
        
        //서비스명 :  체크플러스 - 안심본인인증 서비스
        //페이지명 :  체크플러스 - 결과 페이지
        
        //보안을 위해 제공해드리는 샘플페이지는 서비스 적용 후 서버에서 삭제해 주시기 바랍니다. 
        //**************************************************************************************

        $sitecode = NICE_SITECODE;
        $sitepasswd = NICE_SITEPASS;
        $cb_encode_path = "/data/xoju/munjabest/nice/CPClient_linux_x64";
            
        $enc_data = $_REQUEST["EncodeData"];        // 암호화된 결과 데이타

            //////////////////////////////////////////////// 문자열 점검///////////////////////////////////////////////
        if(preg_match('~[^0-9a-zA-Z+/=]~', $enc_data, $match)) {echo "입력 값 확인이 필요합니다 : ".$match[0]; exit;} // 문자열 점검 추가. 
        if(base64_encode(base64_decode($enc_data))!=$enc_data) {echo "입력 값 확인이 필요합니다"; exit;}

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////
            
        if ($enc_data != "") {

            $plaindata = `$cb_encode_path DEC $sitecode $sitepasswd $enc_data`;     // 암호화된 결과 데이터의 복호화

            if ($plaindata == -1){
                $returnMsg  = "암/복호화 시스템 오류";
            }else if ($plaindata == -4){
                $returnMsg  = "복호화 처리 오류";
            }else if ($plaindata == -5){
                $returnMsg  = "HASH값 불일치 - 복호화 데이터는 리턴됨";
            }else if ($plaindata == -6){
                $returnMsg  = "복호화 데이터 오류";
            }else if ($plaindata == -9){
                $returnMsg  = "입력값 오류";
            }else if ($plaindata == -12){
                $returnMsg  = "사이트 비밀번호 오류";
            }else{
                // 복호화가 정상적일 경우 데이터를 파싱합니다.
                $ciphertime = `$cb_encode_path CTS $sitecode $sitepasswd $enc_data`;    // 암호화된 결과 데이터 검증 (복호화한 시간획득)
                
                $requestnumber = GetValue($plaindata , "REQ_SEQ");
                $errcode = GetValue($plaindata , "ERR_CODE");
                $authtype = GetValue($plaindata , "AUTH_TYPE");
            }
        }


        $this->load->view('nice/nice_fail', $data);
    }
}
