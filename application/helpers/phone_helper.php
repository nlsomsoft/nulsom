<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('format_phone')) {
	function format_phone($phone) {
	    $phone = preg_replace("/[^0-9]/", "", $phone);
	    $length = strlen($phone);

		if (substr($phone,0,2) =='02') {
			return preg_replace("/([0-9]{2})([0-9]{3,4})([0-9]{4})$/","\\1-\\2-\\3", $phone);
		}
		else if ($length == 8 && (substr($phone,0,2) == '15' || substr($phone,0,2) =='16' ||  substr($phone,0,2) == '18')) {
			return preg_replace("/([0-9]{4})([0-9]{4})$/","\\1-\\2", $phone);
		}
		else {
			return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/","\\1-\\2-\\3", $phone);
		}
/*
	    switch($length){
	      case 11 :
	          return preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1-$2-$3", $phone);
	          break;
	      case 10:
	          return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);
	          break;
	      default :
	          return $phone;
	          break;
	    }
*/
	}
}
if (!function_exists('phone_format')) {
	function phone_format($phone) {
	    $phone = preg_replace("/[^0-9]/", "", $phone);
	    $length = strlen($phone);

		if (substr($phone,0,2) =='02') {
			return preg_replace("/([0-9]{2})([0-9]{3,4})([0-9]{4})$/","\\1-\\2-\\3", $phone);
		}
		else if ($length == 8 && (substr($phone,0,2) == '15' || substr($phone,0,2) =='16' ||  substr($phone,0,2) == '18')) {
			return preg_replace("/([0-9]{4})([0-9]{4})$/","\\1-\\2", $phone);
		}
		else {
			return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/","\\1-\\2-\\3", $phone);
		}
	}
}
if (!function_exists('strip_phone')) {
	function strip_phone($phone) {
		$phone = trim($phone);
		$search = array(
			'-',
			' '
		);
		$replace = array(
			'',
			''
		);
		return str_replace($search, $replace, $phone);
	}
}
if (!function_exists('valid_phone')) {
	function valid_phone($phone) {
		$phone = trim($phone);
		$phone_length = strlen($phone);
		$str = '';
		$str = substr($phone,0,3);
		if ($str == '010' || $str == '011' || $str == '016' || $str == '017' || $str == '018' || $str == '019') {
			if ($phone_length != 10 && $phone_length != 11) return false;
			else return true;
		}
		else if ($str == '050') {
			if ($phone_length != 11 && $phone_length != 12) return false;
			else return true;
		}
		else return false;
		// if (strlen($phone) != 11) return false;
		// else {
		// 	$str = '';
		// 	$str = substr($phone,0,3);
		// 	if ($str != '010' && $str != '011' && $str != '017' && $str != '018' && $str != '019' && $str != '050') return false;
		// 	unset($str);
		// }
		// return true;
	}
}
