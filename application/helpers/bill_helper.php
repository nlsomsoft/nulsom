<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('convert_billing_mode')) {
	function convert_billing_mode($mode) {
		if ($mode == 'PA') return '관리자적립';
		else if ($mode == 'PB') return '무통장입금';
		else if ($mode == 'PC') return '신용카드';
		else if ($mode == 'PD') return '실시간이체';
		else if ($mode == 'PE') return '가상계좌';
		else if ($mode == 'PF') return '실패건환불';
		else if ($mode == 'PG') return '취소건환불';
		else if ($mode == 'MA') return '관리자차감';
		else if ($mode == 'MB') return '환불';
		else if ($mode == 'MF') return '친구톡전송';
		else if ($mode == 'MK') return '알림톡전송';
		else if ($mode == 'ML') return '장문전송';
		else if ($mode == 'MM') return '포토전송';
		else if ($mode == 'MP') return '친구톡포토';
		else if ($mode == 'MS') return '단문전송';
		else return $mode;
	}
}
if (!function_exists('convert_product_code')) {
	function convert_product_code($code) {
		if ($code == 'SMS1') return '단문';
		else if ($code == 'SMS2') return '선거단문';
		else if ($code == 'LMS1') return '장문';
		else if ($code == 'LMS2') return '선거장문';
		else if ($code == 'MMS1') return '포토';
		else if ($code == 'MMS2') return '선거포토';
		else if ($code == 'KAT') return '알림톡';
		else if ($code == 'KFT') return '친구톡';
		else if ($code == 'KFTM') return '친구톡포토';
		else return $code;
	}
}
if (!function_exists('convert_channel_type')) {
	function convert_channel_type($code) {
		if ($code == 'SMS1' || $code == 'SMS2') return 'sms';
		else if ($code == 'LMS1' || $code == 'LMS2') return 'lms';
		else if ($code == 'MMS1' || $code == 'MMS2') return 'mms';
		else if ($code == 'KAT' || $code == 'KFT' || $code == 'KFTM') return 'kakao';
		else return $code;
	}
}
if (!function_exists('convert_send_type')) {
	function convert_send_type($code) {
		if ($code == 'SMS1' || $code == 'SMS2') return '1';
		else if ($code == 'LMS1' || $code == 'LMS2') return '2';
		else if ($code == 'MMS1' || $code == 'MMS2') return '3';
		else if ($code == 'KAT' || $code == 'KFT' || $code == 'KFTM') return '4';
		else return $code;
	}
}
if (!function_exists('convert_campaign_status')) {
	function convert_campaign_status($code) {
		if ($code == '0') return '대기';
		else if ($code == '100') return '완료';
		else return '전송중';
	}
}
if (!function_exists('convert_campaign_result')) {
	function convert_campaign_result($code) {
		if ($code == '0' || $code == '1000') return '성공';
		else return '실패';
	}
}
if (!function_exists('convert_result_telecom')) {
	// sowshot :1(SK) 7(KT) 8(LG)
	function convert_result_telecom($val) {
		if ($val == 'LG' || $val == 'L' || $val == 'LGT' || $val == '01990' || $val == '019' || $val == '3' || $val == '8') return 'LG';
		else if ($val == 'SK' || $val == 'S' || $val == 'SKT' || $val == '01190' || $val == '011' || $val == '1') return 'SK';
		else if ($val == 'KT' || $val == 'K' || $val == 'KTF' || $val == '01690' || $val == '016' || $val == '2' || $val == '7') return 'KT';
		else if ($val == '') return 'ETC';
		else return $val;
	}
}
if (!function_exists('convert_campaign_priority')) {
	function convert_campaign_priority($code) {
		if ($code == '100') return 'SMS(LG)';
		else if ($code == '500') return 'LMS(LG)';
		else if ($code == '900') return 'MMS(LG)';
		return $code;
	}
}
