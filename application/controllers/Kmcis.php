<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kmcis extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'kmcis', 'cookie'));
    }
	public function result() {
	    $rec_cert = $_REQUEST['rec_cert'];
		$certNum  = $_REQUEST['certNum']; // 쿠키 또는 Session을 생성하지 않았을때 certNum 수신처리
		if (strlen($rec_cert) == 0 || strlen($certNum) == 0) {
			error_log('kmcis result error', 0);
			return;
			// exit('실명인증 결과값 비정상');
		}
		$data['rec_cert'] = $rec_cert;
		$data['certNum'] = $certNum;
        $this->load->view('kmcis/result', $data);
	}
	public function result_update() {
		$rec_cert       = $_REQUEST['rec_cert'];
		$cookieCertNum  = $_REQUEST['certNum']; // 쿠키 또는 Session을 생성하지 않았을때 certNum 값 수신처리
		$iv = $cookieCertNum;  // 쿠키 또는 Session을 생성하지 않았을때 수신한 certNum 값을 복호화키에 세팅

		//암호화모듈 호출
		if (!extension_loaded('ICERTSecu')) {
			error_log('kmcis module1 error', 0);
			$this->session->set_flashdata('notice', '암호화 모듈 오류입니다.');
        	redirect('/signup/join_agree');
		}
		//01.인증결과 1차 복호화

		$rec_cert = ICertSeed(2,0,$iv,$rec_cert);
		//02.복호화 데이터 Split (rec_cert 1차암호화데이터 / 위변조 검증값 / 암복화확장변수)
		$decStr_Split = explode("/", $rec_cert);
		$encPara  = $decStr_Split[0];		//rec_cert 1차 암호화데이터
		$encMsg   = $decStr_Split[1];		//위변조 검증값
		//03.인증결과 2차 복호화

		$rec_cert = ICertSeed(2,0,$iv,$encPara);
		//03-1. 위변조 검증

		// $encMsg2 = ICertHMac($encPara);
		$encMsg2 = "abcdabcdabcdabcdabcdabcdabcdabcdabcdabcd";

		if (strcmp($encMsg, $encMsg2) === 1) {
			error_log('kmcis module2 error', 0);
			$this->session->set_flashdata('notice', '암호화 모듈 오류입니다.');
        	redirect('/signup/join_agree');
		}

		//04. 복호화 된 결과자료 "/"로 Split 하기
		$decStr_Split = explode("/", $rec_cert);
		$certNum    = $decStr_Split[0];
		$date       = $decStr_Split[1];
		$CI         = $decStr_Split[2];
		$phoneNo    = $decStr_Split[3];
		$phoneCorp  = $decStr_Split[4];
		$birthDay   = $decStr_Split[5];
		$gender     = $decStr_Split[6];
		$nation     = $decStr_Split[7];
		$name       = $decStr_Split[8];
		$result     = $decStr_Split[9];
		$certMet    = $decStr_Split[10];
		$ip         = $decStr_Split[11];
		$M_name     = $decStr_Split[12];
		$M_birthDay = $decStr_Split[13];
		$M_Gender   = $decStr_Split[14];
		$M_nation   = $decStr_Split[15];
		$plusInfo   = $decStr_Split[16];
		$DI         = $decStr_Split[17];

		//05. CI,DI 복호화
		if (strlen($CI) > 0) {
			$CI = ICertSeed(2,0,$iv,$CI);
		}
		if (strlen($DI) > 0) {
			$DI = ICertSeed(2,0,$iv,$DI);
		}
		// 요청번호 (최대 40byte까지 유효)
		if (strlen($certNum) > 40 || strlen($certNum) == 0) {
			// echo("<script>alert('요청번호 비정상 ($certNum)');</script>");
			error_log('kmcis error No1', 0);
			$this->session->set_flashdata('notice', '요청번호 비정상');
        	redirect('/signup/join_agree');
		}
		// 요청일시 (숫자 14자리만 유효)
		$patn = "/^[0-9]*$/";
		if (strlen($date) != 14 || paramchk($patn, $date) == 0) {
			// echo("<script>alert('요청일시 비정상 ($date)');</script>");
			error_log('kmcis error No2', 0);
			$this->session->set_flashdata('notice', '요청일시 비정상');
        	redirect('/signup/join_agree');
		}
		// 휴대폰번호 (값이 있는 경우에는 숫자 10 또는 11자리까지만 유효)
		$patn = "/^[0-9]*$/";
		if ((strlen($phoneNo) != 10 && strlen($phoneNo) != 11) || paramChk($patn, $phoneNo) == 0) {
			// echo("<script>alert('휴대폰번호 비정상 ($phoneNo)');</script>");
			error_log('kmcis error No3', 0);
			$this->session->set_flashdata('notice', '휴대폰번호 비정상');
        	redirect('/signup/join_agree');
		}
		// 이동통신사 (값이 있는 경우에는 영문대문자 3자리만 유효)
		$patn = "/^[[:upper:]]*$/";
		if (strlen($phoneCorp) != 3 || paramChk($patn, $phoneCorp) == 0) {
			// echo("<script>alert('이동통신사 비정상 ($phoneCorp)');</script>");
			error_log('kmcis error No4', 0);
			$this->session->set_flashdata('notice', '이동통신사 비정상');
        	redirect('/signup/join_agree');
		}
		// 생년월일 (값이 있는 경우에는 숫자 8자리만 유효)
		$patn = "/^[0-9]*$/";
		if (strlen($birthDay) != 8 || paramChk($patn, $birthDay) == 0) {
			// echo("<script>alert('생년월일 비정상 ($birthDay)');</script>");
			error_log('kmcis error No5', 0);
			$this->session->set_flashdata('notice', '생년월일 비정상');
        	redirect('/signup/join_agree');
		}
		// 성별 (값이 있는 경우에는 숫자 1자리만 유효)
		$patn = "/^[0-9]*$/";
		if (strlen($gender) != 1 || paramChk($patn, $gender) == 0) {
			// echo("<script>alert('성별 비정상 ($gender)');</script>");
			error_log('kmcis error No6', 0);
			$this->session->set_flashdata('notice', '성별 비정상');
        	redirect('/signup/join_agree');
		}
		// 내외국인 (값이 있는 경우에는 숫자 1자리만 유효)
		$patn = "/^[0-9]*$/";
		if (strlen($nation) != 1 || paramChk($patn, $nation) == 0) {
			// echo("<script>alert('내/외국인 비정상 ($nation)');</script>");
			error_log('kmcis error No7', 0);
			$this->session->set_flashdata('notice', '내/외국인 비정상');
        	redirect('/signup/join_agree');
		}
		// 성명 (값이 있는 경우에는 최대 30byte까지만 유효)
		$patn = "/^[\xa1-\xfea-zA-Z[:space:],.-]*$/";
		if (strlen($name) > 60 || paramChk($patn, $name) == 0) {
			// echo("<script>alert('성명 비정상 ($name)');</script>");
			error_log('kmcis error No8', 0);
			$this->session->set_flashdata('notice', '성명 비정상');
        	redirect('/signup/join_agree');
		}
		// 결과값 (영문대문자 1자리만 유효)
		$patn = "/^[[:upper:]]*$/";
		if (strlen($result) != 1 || paramChk($patn, $result) == 0) {
			// echo("<script>alert('결과값 비정상 ($result)');</script>");
			error_log('kmcis error No9', 0);
			$this->session->set_flashdata('notice', '결과값 비정상');
        	redirect('/signup/join_agree');
		}
		// 본인인증방법 (영문대문자 1자리만 유효)
		$patn = "/^[[:upper:]]*$/";
		if (strlen($certMet) != 1 || paramChk($patn, $certMet) == 0) {
			// echo("<script>alert('본인인증방법 비정상 ($certMet)');</script>");
			error_log('kmcis error No10', 0);
			$this->session->set_flashdata('notice', '본인인증방법 비정상');
        	redirect('/signup/join_agree');
		}
		// 미성년자 성명 (값이 있는 경우에는 60자 이하 한글, 영대소문자 유효)
		$patn = "/^[\xa1-\xfea-zA-Z[:space:],.-]*$/";
		if (strlen($M_name) != 0) {
			if (strlen($M_name) > 60 || paramChk($patn, $M_name) == 0) {
				// echo("<script>alert('미성년자 성명 ($M_name)');</script>");
				error_log('kmcis error No11', 0);
				$this->session->set_flashdata('notice', '미성년자 성명 비정상');
	        	redirect('/signup/join_agree');
			}
		}
		// 미성년자 생년월일 (값이 있는 경우에는 숫자 8자리만 유효)
		$patn = "/^[0-9]*$/";
		if (strlen($M_birthDay) != 0) {
			if (strlen($M_birthDay) != 8 || paramChk($patn, $M_birthDay) == 0){
				// echo("<script>alert('생년월일 비정상 ($M_birthDay)');</script>");
				error_log('kmcis error No12', 0);
				$this->session->set_flashdata('notice', '생년월일 비정상');
	        	redirect('/signup/join_agree');
			}
		}
		// 미성년자 성별 (값이 있는 경우에는 숫자 1자리만 유효)
		$patn = "/^[0-9]*$/";
		if (strlen($M_Gender) != 0) {
			if (strlen($M_Gender) != 1 || paramChk($patn, $M_Gender) == 0) {
				// echo("<script>alert('성별 비정상 ($M_Gender)');</script>");
				error_log('kmcis error No13', 0);
				$this->session->set_flashdata('notice', '성별 비정상');
	        	redirect('/signup/join_agree');
			}
		}
		// 미성년자 내외국인 (값이 있는 경우에는 숫자 1자리만 유효)
		$patn = "/^[0-9]*$/";
		if (strlen($M_nation) != 0) {
			if (strlen($M_nation) != 1 || paramChk($patn, $M_nation) == 0) {
				// echo("<script>alert('내/외국인 비정상 ($M_nation)');</script>");
				error_log('kmcis error No14', 0);
				$this->session->set_flashdata('notice', '내/외국인 비정상');
	        	redirect('/signup/join_agree');
			}
		}


	    $adult_date = date('Ymd', strtotime("-19 years"));
	    $check_birth_date = (int)$birthDay;
	    if ($check_birth_date >= $adult_date) {
			error_log('kmcis error check birth date', 0);
			$this->session->set_flashdata('notice', '미성년자(만 19세)는 가입할 수 없습니다. 관리자에게 문의하세요.');
        	redirect('/signup/join_agree');
	    }

		// 1. date 값 검증
		$end_date = date('YmdHis'); //	현재 서버 시각 구하기
		$start_date = $date;
		//mktime()을 만들기 위해 각 시간 단위로 분할
		$yy = substr($end_date, 0, 4);
		$mm = substr($end_date, 4, 2);
		$dd = substr($end_date, 6, 2);
		$hh = substr($end_date, 8, 2);
		$ii = substr($end_date, 10, 2);
		$ss = substr($end_date, 12, 2);
		//mktime()을 만들기 위해 DB에서 불러온 datetime 값을 시간 단위로 분할
		$yy_start = substr($start_date, 0, 4);
		$mm_start = substr($start_date, 4, 2);
		$dd_start = substr($start_date, 6, 2);
		$hh_start = substr($start_date, 8, 2);
		$ii_start = substr($start_date, 10, 2);
		$ss_start = substr($start_date, 12, 2);
		$toDate = mktime($hh, $ii, $ss, $mm, $dd, $yy);
		$fromDate = mktime($hh_start, $ii_start, $ss_start, $mm_start, $dd_start, $yy_start);
		$timediff = intval(($toDate - $fromDate) / 60); // 분
		if ($timediff < -30 || 30 < $timediff) {
			error_log('kmcis time error', 0);
			$this->session->set_flashdata('notice', '비정상적인 접근입니다. (요청시간경과)');
        	redirect('/signup/join_agree');
		}

		// 2. ip 값 검증
		$client_ip = ''; // 사용자IP 구하기
		if (isset($_SERVER)) {
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
				$client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			if (isset($_SERVER['HTTP_CLIENT_IP']))
				$client_ip = $_SERVER['HTTP_CLIENT_IP'];
			$client_ip = $_SERVER['REMOTE_ADDR'];
		}
		if (getenv('HTTP_X_FORWARDED_FOR')) {
			$client_ip = getenv('HTTP_X_FORWARDED_FOR');
		}
		if (getenv('HTTP_CLIENT_IP')) {
			$client_ip = getenv('HTTP_CLIENT_IP');
		}
		if ($client_ip == '') {
			$client_ip = getenv('REMOTE_ADDR');
		}
		$client_ip_list = explode(",",$client_ip);
		$client_ip = $client_ip_list[0];
		if ($client_ip != $ip) {
			error_log('kmcis ip error', 0);
			$this->session->set_flashdata('notice', '비정상적인 접근입니다.');
        	redirect('/signup/join_agree');
		}

        $this->load->model('userModels');
        $option = array(
            'ci' => $CI,
            'di' => $DI,
            'storename' => $_SERVER['STORENAME'],
        );
		$kmcis = $this->userModels->get_kmcis($option);
		if ($kmcis->userid != '') {
			$this->session->set_flashdata('notice', '이미 가입된 회원입니다.');
        	redirect('/signup/join_agree');
		}
		$kno = $kmcis->kno;
		$name = iconv("EUC-KR", "UTF-8", $name);
		$M_name = iconv("EUC-KR", "UTF-8", $M_name);
		if (!$kno) {
	        $option = array(
	            'storename' => $_SERVER['STORENAME'],
	            'encmsg' => $rec_cert,
	            'certnum' => $certNum,
	            'curdate' => $date,
	            'ci' => $CI,
	            'di' => $DI,
	            'phoneno' => $phoneNo,
	            'phonecorp' => $phoneCorp,
	            'birthday' => $birthDay,
	            'nation' => $nation,
	            'gender' => $gender,
	            'name' => $name,
	            'result' => $result,
	            'certmet' => $certMet,
	            'ip' => $ip,
	            'm_name' => $M_name,
	            'm_birthday' => $M_birthDay,
	            'm_gender' => $M_Gender,
	            'm_nation' => $M_nation,
	            'plusinfo' => $plusInfo,
	        );
	        $kno = $this->userModels->add_kmcis($option);
	    }
		// signup_type :1 개인, 2 기업
		$user_type = get_cookie('user_type');
		$newdata = array(
			'user_type' => $user_type,
			'kmc_name' => $name,
			'kmc_mobile' => $phoneNo,
			'kmc_kno' => $kno,
			'kmc_table' => 'kmcis_logs',
		);
        $this->session->set_userdata($newdata, true);
        redirect("/signup/join_form");
	}
}
