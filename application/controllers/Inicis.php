<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inicis extends CI_Controller {
	private $current_time;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        $this->load->helper(array('form','url','kcp','cookie'));
    }
    public function request() {
    	initialize_session_userdata($this);
    	if ($this->session->userdata('pg') != 'Y') {
    		$this->session->set_flashdata('notice', '접근 권한이 없습니다.');
    		redirect('/pay/list');
    	}

        $this->load->library('form_validation');
        $this->form_validation->set_rules('rd_pay_type', 'Rd Pay Type', 'in_list[1,2,3]|integer|required');
        $this->form_validation->set_rules('ipt_amount', 'Ipt Amount', 'integer|required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $rd_pay_type = (int)$this->input->post('rd_pay_type');
        $ipt_amount = (int)$this->input->post('ipt_amount');

		$this->load->helper(array('inistdpayutil'));
		$SignatureUtil = new INIStdPayUtil();
		/*
		  //*** 위변조 방지체크를 signature 생성 ***
		  oid, price, timestamp 3개의 키와 값을
		  key=value 형식으로 하여 '&'로 연결한 하여 SHA-256 Hash로 생성 된값
		  ex) oid=INIpayTest_1432813606995&price=819000&timestamp=2012-02-01 09:19:04.004
		 * key기준 알파벳 정렬
		 * timestamp는 반드시 signature생성에 사용한 timestamp 값을 timestamp input에 그대로 사용하여야함
		 */

		//############################################
		// 1.전문 필드 값 설정(***가맹점 개발수정***)
		//############################################
		// 여기에 설정된 값은 Form 필드에 동일한 값으로 설정
		$data['mid'] = INICIS_MID;  // 가맹점 ID(가맹점 수정후 고정)
		//인증
		$data['signKey'] = INICIS_KEY; // 가맹점에 제공된 웹 표준 사인키(가맹점 수정후 고정)
		$data['timestamp'] = $SignatureUtil->getTimestamp();   // util에 의해서 자동생성

		$data['orderNumber'] = $this->session->userdata('userno').'_'.INICIS_MID . '_' . $SignatureUtil->getTimestamp(); // 가맹점 주문번호(가맹점에서 직접 설정)
		$data['price'] = $ipt_amount;        // 상품가격(특수기호 제외, 가맹점에서 직접 설정)

		// $cardNoInterestQuota = "11-2:3:,34-5:12,14-6:12:24,12-12:36,06-9:12,01-3:4";  // 카드 무이자 여부 설정(가맹점에서 직접 설정)
		// $cardQuotaBase = "2:3:4:5:6:11:12:24:36";  // 가맹점에서 사용할 할부 개월수 설정
		$data['cardNoInterestQuota'] = '';
		$data['cardQuotaBase'] = '';
		//###################################
		// 2. 가맹점 확인을 위한 signKey를 해시값으로 변경 (SHA-256방식 사용)
		//###################################
		$data['mKey'] = $SignatureUtil->makeHash($data['signKey'], "sha256");

		$data['params'] = array(
		    'oid' => $data['orderNumber'],
		    'price' => $data['price'],
		    'timestamp' => $data['timestamp']
		);
		$data['sign'] = $SignatureUtil->makeSignature($data['params'], "sha256");
		//$config['base_url']
		/* 기타 */
		$data['siteDomain'] = $this->config->item('base_url');
		// 페이지 URL에서 고정된 부분을 적는다.
		// Ex) returnURL이 http://localhost:8082/demo/INIpayStdSample/INIStdPayReturn.jsp 라면
		//                 http://localhost:8082/demo/INIpayStdSample 까지만 기입한다.

		if ($rd_pay_type == '1') $data['gopaymethod'] = 'DirectBank';
		else if ($rd_pay_type == '2') $data['gopaymethod'] = 'VBank';
		else if ($rd_pay_type == '3') $data['gopaymethod'] = 'Card';
		$data['except_bootstrap'] = true;
        $this->load->view('templates/header', $data);
    	$this->load->view('inicis/inistdpayrequest', $data);
        $this->load->view('templates/footer');
    }
    public function close() {
    	echo '<script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay_close.js" charset="UTF-8"></script>';
    }
    public function popup() {
    	echo '<script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay_popup.js" charset="UTF-8"></script>';
    }
    public function vbank() {
		$TEMP_IP = getenv("REMOTE_ADDR");
		$PG_IP = substr($TEMP_IP, 0, 10);

		if ($PG_IP != '203.238.37' && $PG_IP != '39.115.212') {
			error_log('INICIS IP AUTH ERROR', 0);
			exit;
		}
        $this->load->library('form_validation');
        $this->form_validation->set_rules('len', 'len', 'required');
        $this->form_validation->set_rules('no_oid', 'no_oid', 'required');
        $this->form_validation->set_rules('amt_input', 'amt_input', 'required');
        if ($this->form_validation->run() === false) {
            error_log('INICIS FORM VALIDATION ERROR', 0);
            exit;
        }
	    // $nm_inputbank = iconv('EUC-KR', 'UTF-8', $this->input->post('nm_inputbank'));
	    // $nm_input = iconv('EUC-KR', 'UTF-8', $this->input->post('nm_input'));
	    $nm_inputbank = $this->input->post('nm_inputbank');
	    $nm_input = $this->input->post('nm_input');

        $option = array(
        	'len' => $this->input->post('len'),
		    'no_tid' => $this->input->post('no_tid'),
		    'moid' => $this->input->post('no_oid'),
		    'id_merchant' => $this->input->post('id_merchant'),
		    'cd_bank' => $this->input->post('cd_bank'),
		    'cd_deal' => $this->input->post('cd_deal'),
		    'dt_trans' => $this->input->post('dt_trans'),
		    'tm_trans' => $this->input->post('tm_trans'),
		    'no_msgseq' => $this->input->post('no_msgseq'),
		    'cd_joinorg' => $this->input->post('cd_joinorg'),
		    'dt_transbase' => $this->input->post('dt_transbase'),
		    'no_transseq' => $this->input->post('no_transseq'),
		    'type_msg' => $this->input->post('type_msg'),
		    'cl_trans' => $this->input->post('cl_trans'),
		    'cl_close' => $this->input->post('cl_close'),
		    'cl_kor' => $this->input->post('cl_kor'),
		    'no_msgmanage' => $this->input->post('no_msgmanage'),
		    'no_vacct' => $this->input->post('no_vacct'),
		    'amt_input' => (int)$this->input->post('amt_input'),
		    'amt_check' => (int)$this->input->post('amt_check'),
		    'nm_inputbank' => $nm_inputbank,
		    'nm_input' => $nm_input,
		    'dt_inputstd' => $this->input->post('dt_inputstd'),
		    'dt_calculstd' => $this->input->post('dt_calculstd'),
		    'flg_close' => $this->input->post('flg_close'),
		    'dt_cshr' => $this->input->post('dt_cshr'),
		    'tm_cshr' => $this->input->post('letm_cshrn'),
		    'no_cshr_appl' => $this->input->post('no_cshr_appl'),
		    'no_cshr_tid' => $this->input->post('no_cshr_tid'),
        );
		$this->load->model('payModels');
		$vbank = $this->payModels->get_inicis_vbank_logs($option);
		if (!$vbank) {
			error_log('INICIS get_inicis_vbank_logs ERROR', 0);
			exit;
		}
		if ($option['amt_input'] != (int)$vbank->totprice) {
			error_log('INICIS AMOUNT ERROR', 0);
			exit;
		}
		if ($vbank->no_tid != '') {
			error_log('INICIS ALREADY UPDATE ERROR', 0);
			exit;
		}
		$option1 = array(
			'userno' => $vbank->userno,
			'userid' => $vbank->userid,
			'storeno' => $vbank->storeno,
			'groupno' => $vbank->groupno,
			'totprice' => $vbank->totprice
		);
		$result = $this->payModels->modify_inicis_vbank_logs($option,$option1);
		if (!$result) {
			error_log('INICIS modify_inicis_vbank_logs ERROR', 0);
			exit;
		}

		$body = '';
		foreach ($option as $key => $val) {
			$body .= "{$key} : {$val}\n";
		}
		$body .= '\n';
		echo 'OK';

        //session 정보 초기화
        $cache_key = 'session_'.$vbank->storeno.'_'.$vbank->userid;
        $this->cache->redis->save($cache_key, '1', 3600); //1시간 자동삭제

		$this->load->helper('file');
        $date = date('Ym');
        $file_path = APPPATH.'logs/vbank_'.$date.'.log';
		write_file($file_path, $body, 'w+');
		error_log('INICIS VBANK SUCCESS', 0);
		exit;
    }
    public function result() {
		initialize_session_userdata($this);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('resultCode', 'resultCode', 'required');
        $this->form_validation->set_rules('resultMsg', 'resultMsg', 'required');
        $this->form_validation->set_rules('returnUrl', 'returnUrl', 'required');
        $this->form_validation->set_rules('authUrl', 'authUrl', 'required');
        $this->form_validation->set_rules('orderNumber', 'orderNumber', 'required');
        $this->form_validation->set_rules('authToken', 'authToken', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
			redirect('/pay/confirm/fail/');
        }

		$this->load->helper(array('inistdpayutil'));
		$this->load->helper(array('httpclient'));

        $util = new INIStdPayUtil();
        try {
            //#############################
            // 인증결과 파라미터 일괄 수신
            //#############################
            //		$var = $_REQUEST["data"];

            //#####################
            // 인증이 성공일 경우만
            //#####################
            if (strcmp("0000", $_REQUEST["resultCode"]) == 0) {
                // echo "####인증성공/승인요청####";
                // echo "<br/>";
                //############################################
                // 1.전문 필드 값 설정(***가맹점 개발수정***)
                //############################################;
                $mid 			= $_REQUEST["mid"];     					// 가맹점 ID 수신 받은 데이터로 설정
                $signKey 		= INICIS_KEY; 		// 가맹점에 제공된 키(이니라이트키) (가맹점 수정후 고정) !!!절대!! 전문 데이터로 설정금지
                $timestamp 		= $util->getTimestamp();   					// util에 의해서 자동생성
                $charset 		= "UTF-8";        							// 리턴형식[UTF-8,EUC-KR](가맹점 수정후 고정)
                $format 		= "JSON";        							// 리턴형식[XML,JSON,NVP](가맹점 수정후 고정)
                $authToken 		= $_REQUEST["authToken"];   				// 취소 요청 tid에 따라서 유동적(가맹점 수정후 고정)
                $authUrl 		= $_REQUEST["authUrl"];    					// 승인요청 API url(수신 받은 값으로 설정, 임의 세팅 금지)
                $netCancel 		= $_REQUEST["netCancelUrl"];   				// 망취소 API url(수신 받은f값으로 설정, 임의 세팅 금지)
                $mKey 			= hash("sha256", $signKey);					// 가맹점 확인을 위한 signKey를 해시값으로 변경 (SHA-256방식 사용)

                //#####################
                // 2.signature 생성
                //#####################
                $signParam["authToken"] 	= $authToken;  	// 필수
                $signParam["timestamp"] 	= $timestamp;  	// 필수
                // signature 데이터 생성 (모듈에서 자동으로 signParam을 알파벳 순으로 정렬후 NVP 방식으로 나열해 hash)
                $signature = $util->makeSignature($signParam);

                //#####################
                // 3.API 요청 전문 생성
                //#####################
                $authMap["mid"] 			= $mid;   		// 필수
                $authMap["authToken"] 		= $authToken; 	// 필수
                $authMap["signature"] 		= $signature; 	// 필수
                $authMap["timestamp"] 		= $timestamp; 	// 필수
                $authMap["charset"] 		= $charset;  	// default=UTF-8
                $authMap["format"] 			= $format;  	// default=XML

                try {
                    $httpUtil = new HttpClient();
                    //#####################
                    // 4.API 통신 시작
                    //#####################
                    $authResultString = "";
                    if ($httpUtil->processHTTP($authUrl, $authMap)) {
                        $authResultString = $httpUtil->body;
                        // echo "<p><b>RESULT DATA :</b> $authResultString</p>";			//PRINT DATA
                    } else {
                        // echo "Http Connect Error\n";
                        // echo $httpUtil->errormsg;
						throw new Exception("Http Connect Error");
                    }

                    //############################################################
                    //5.API 통신결과 처리(***가맹점 개발수정***)
                    //############################################################
                    // echo "## 승인 API 결과 ##";
                    $resultMap = json_decode($authResultString, true);
                    // echo "<pre>";
                    // echo "<table width='565' border='0' cellspacing='0' cellpadding='0'>";

                    /*************************  결제보안 추가 2016-05-18 START ****************************/
                    $secureMap["mid"]		= $mid;							//mid
                    $secureMap["tstamp"]	= $timestamp;					//timestemp
                    $secureMap["MOID"]		= $resultMap["MOID"];			//MOID
                    $secureMap["TotPrice"]	= $resultMap["TotPrice"];		//TotPrice

                    // signature 데이터 생성
                    $secureSignature = $util->makeSignatureAuth($secureMap);
                    /*************************  결제보안 추가 2016-05-18 END ****************************/

					if ((strcmp("0000", $resultMap["resultCode"]) == 0) && (strcmp($secureSignature, $resultMap["authSignature"]) == 0) ){	//결제보안 추가 2016-05-18
					   /*****************************************************************************
				       * 여기에 가맹점 내부 DB에 결제 결과를 반영하는 관련 프로그램 코드를 구현한다.

						 [중요!] 승인내용에 이상이 없음을 확인한 뒤 가맹점 DB에 해당건이 정상처리 되었음을 반영함
								처리중 에러 발생시 망취소를 한다.
				       ******************************************************************************/
                        // echo "<tr><th class='td01'><p>거래 성공 여부</p></th>";
                        // echo "<td class='td02'><p>성공</p></td></tr>";


						if ($resultMap['payMethod'] == 'VBank') {
							$option = array(
							    'userno' => $this->session->userdata('userno'),
							    'userid' => $this->session->userdata('userid'),
							    'storeno' => $this->session->userdata('storeno'),
							    'groupno' => $this->session->userdata('groupno'),
							    'buyertel' => $resultMap['buyerTel'],
							    'buyeremail' => $resultMap['buyerEmail'],
							    'resultcode' => $resultMap['resultCode'],
							    'authsignature' => $resultMap['authSignature'],
							    'tid' => $resultMap['tid'],
							    'totprice' => $resultMap['TotPrice'],
							    'paymethod' => $resultMap['payMethod'],
							    'moid' => $resultMap['MOID'],
							    'currency' => $resultMap['currency'],
							    'appldate' => $resultMap['applDate'],
							    'vact_date' => $resultMap['VACT_Date'],
							    'vact_name' => $resultMap['VACT_Name'],
							    'vact_inputname' => $resultMap['VACT_InputName'],
							    'vact_time' => $resultMap['VACT_Time'],
							    'vact_bankcode' => $resultMap['VACT_BankCode'],
							    'vactbankname' => $resultMap['vactBankName'],
							    'appltime' => $resultMap['applTime'],
							    'goodsname' => $resultMap['goodsName'],
							    'buyername' => $resultMap['buyerName'],
							    'resultmsg' => $resultMap['resultMsg'],
							    'vact_num' => $resultMap['VACT_Num'],
							    'add_date' => $this->current_time,
							);
							$this->load->model('payModels');
							$result = $this->payModels->add_inicis_vbank_logs($option);
							if (!$result) {
								$this->session->set_flashdata('notice', '결제가 실패했습니다.');
								redirect('/pay/confirm/fail/');
							}
							$deposit_name = str_replace(array('(', ')'),'',$resultMap['buyerName']);
							$deposit_bank = str_replace(array('(', ')'),'',$resultMap['vactBankName']);
							redirect("/pay/confirm/succ/vbank/{$resultMap['TotPrice']}/{$deposit_name}/{$deposit_bank}");
						} else {
							$option = array(
							    'userno' => $this->session->userdata('userno'),
							    'userid' => $this->session->userdata('userid'),
							    'storeno' => $this->session->userdata('storeno'),
							    'groupno' => $this->session->userdata('groupno'),
							    'buyertel' => $resultMap['buyerTel'],
							    'buyeremail' => $resultMap['buyerEmail'],
							    'resultcode' => $resultMap['resultCode'],
							    'authsignature' => $resultMap['authSignature'],
							    'tid' => $resultMap['tid'],
							    'totprice' => $resultMap['TotPrice'],
							    'paymethod' => $resultMap['payMethod'],
							    'moid' => $resultMap['MOID'],
							    'currency' => $resultMap['currency'],
							    'appltime' => $resultMap['applTime'],
							    'goodsname' => $resultMap['goodsName'],
							    'card_code' => $resultMap['CARD_Code'],
							    'card_bankcode' => $resultMap['CARD_BankCode'],
							    'buyername' => $resultMap['buyerName'],
							    'applnum' => $resultMap['applNum'],
							    'resultmsg' => $resultMap['resultMsg'],
							    'card_applprice' => $resultMap['CARD_ApplPrice'],
							    'appldate' => $resultMap['applDate'],
							    'acct_bankcode' => $resultMap['ACCT_BankCode'],
							    'acct_bankname' => $resultMap['ACCT_BankName'],
							    'acct_name' => $resultMap['ACCT_Name'],
							    'acct_num' => $resultMap['ACCT_Num'],
							    'acct_applprice' => $resultMap['ACCT_ApplPrice'],
							    'acct_gwcode' => $resultMap['ACCT_GWCode'],
							    'add_date' => $this->current_time,
							);
							$this->load->model('payModels');
							$result = $this->payModels->add_inicis_logs($option);
							if (!$result) {
								$this->session->set_flashdata('notice', '관리자에게 문의하십시오.');
								redirect('/pay/confirm/error/');
							}
					        //session 정보 초기화
					        $cache_key = 'session_'.$this->session->userdata('storeno').'_'.$this->session->userdata('userid');
					        $this->cache->redis->save($cache_key, '1', 3600); //1시간 자동삭제

							$deposit_type = ($resultMap['payMethod'] == 'Card' ? 'card' : 'directbank');
							$deposit_bank = ($resultMap['payMethod'] == 'Card' ? '신용카드' : $resultMap['ACCT_BankName']);
							$deposit_name = str_replace(array('(', ')'),'',$resultMap['buyerName']);
							$deposit_bank = str_replace(array('(', ')'),'',$deposit_bank);
							redirect("/pay/confirm/succ/{$deposit_type}/{$resultMap['TotPrice']}/{$deposit_name}/{$deposit_bank}");
						}
					} else {
						// echo "<tr><th class='td01'><p>거래 성공 여부</p></th>";
						// echo "<td class='td02'><p>실패</p></td></tr>";
						// echo "<tr><th class='line' colspan='2'><p></p></th></tr>
						// <tr><th class='td01'><p>결과 코드</p></th>
						// <td class='td02'><p>" . @(in_array($resultMap["resultCode"] , $resultMap) ? $resultMap["resultCode"] : "null" ) . "</p></td></tr>";

						// //결제보안키가 다른 경우.
						// if (strcmp($secureSignature, $resultMap["authSignature"]) != 0) {
						// echo "<tr><th class='line' colspan='2'><p></p></th></tr>
						// <tr><th class='td01'><p>결과 내용</p></th>
						// <td class='td02'><p>" . "* 데이터 위변조 체크 실패" . "</p></td></tr>";

						// //망취소
						// if(strcmp("0000", $resultMap["resultCode"]) == 0) {
						// throw new Exception("데이터 위변조 체크 실패");
						// }
						// } else {
						// echo "<tr><th class='line' colspan='2'><p></p></th></tr>
						// <tr><th class='td01'><p>결과 내용</p></th>
						// <td class='td02'><p>" . @(in_array($resultMap["resultMsg"] , $resultMap) ? $resultMap["resultMsg"] : "null" ) . "</p></td></tr>";
						// }
			            $this->session->set_flashdata('notice', '결제가 실패했습니다.');
						redirect('/pay/confirm/fail/');
                    }

                    // 수신결과를 파싱후 resultCode가 "0000"이면 승인성공 이외 실패
                    // 가맹점에서 스스로 파싱후 내부 DB 처리 후 화면에 결과 표시
                    // payViewType을 popup으로 해서 결제를 하셨을 경우
                    // 내부처리후 스크립트를 이용해 opener의 화면 전환처리를 하세요
                    //throw new Exception("강제 Exception");
                } catch (Exception $e) {
                    // $s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
                    //####################################
                    // 실패시 처리(***가맹점 개발수정***)
                    //####################################
                    //---- db 저장 실패시 등 예외처리----//
                    $s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
                    // echo $s;
                    error_log($s, 0);
                    //#####################
                    // 망취소 API
                    //#####################
                    // $netcancelResultString = ""; // 망취소 요청 API url(고정, 임의 세팅 금지)
                    // if ($httpUtil->processHTTP($netCancel, $authMap)) {
                    //     $netcancelResultString = $httpUtil->body;
                    // } else {
                    //     echo "Http Connect Error\n";
                    //     echo $httpUtil->errormsg;
                    //     throw new Exception("Http Connect Error");
                    // }
					// echo "<br/>## 망취소 API 결과 ##<br/>";
					/*##XML output##*/
					//$netcancelResultString = str_replace("<", "&lt;", $$netcancelResultString);
					//$netcancelResultString = str_replace(">", "&gt;", $$netcancelResultString);
                    // 취소 결과 확인
                    // echo "<p>". $netcancelResultString . "</p>";
		            $this->session->set_flashdata('notice', '관리자에게 문의하십시오.(1)');
					redirect('/pay/confirm/error/');
                }
            } else {
                //#############
                // 인증 실패시
                //#############
                // echo "<br/>";
                // echo "####인증실패####";
                // echo "<pre>" . var_dump($_REQUEST) . "</pre>";
	            $this->session->set_flashdata('notice', '결제가 실패했습니다.');
				redirect('/pay/confirm/fail/');
            }
        } catch (Exception $e) {
            $s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
            // echo $s;
            error_log($s, 0);
            $this->session->set_flashdata('notice', '결제가 실패했습니다.');
			redirect('/pay/confirm/fail/');
        }
    }
}