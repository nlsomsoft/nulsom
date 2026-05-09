<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('mydate_format')) {
	function mydate_format($format, $date) {
		if ($format == 'Y-m-d') {
			return substr($date, 0, 10);
		}
		else if ($format == 'Y-m-d H:i') {
			return substr($date, 0, 16);
		}
		else if ($format == 'y-m-d') {
			return substr($date, 2, 8);
		}
		else if ($format == 'y-m-d H:i') {
			return substr($date, 2, 14);
		}
		else return $date;
	}
}
if (!function_exists('mydate_format1')) {
	function mydate_format1($format, $date) {
		if ($format == 'Y-m-d') {
			return sprintf('%04d-%02d-%02d',substr($date,0,4),substr($date,4,2),substr($date,6,2));
		}
		else if ($format == 'Y-m-d H:i') {
			return sprintf('%04d-%02d-%02d %02d:%02d',substr($date,0,4),substr($date,4,2),substr($date,6,2),substr($date,8,2),substr($date,10,2));
		}
		else if ($format == 'y-m-d') {
			return sprintf('%02d-%02d-%02d',substr($date,0,2),substr($date,2,2),substr($date,4,2));
		}
		else if ($format == 'y-m-d H:i') {
			return sprintf('%02d-%02d-%02d %02d:%02d',substr($date,0,2),substr($date,2,2),substr($date,4,2),substr($date,6,2),substr($date,8,2));
		}
		else return $date;
	}
}
if (!function_exists('convert_display_today_time')) {
	function convert_display_today_time($date) {
		$today = strtotime(date('Y-m-d'));
		$target = strtotime(substr($date, 0, 10));
		if ($today == $target) {
			return substr($date, 11, 5);
		} else {
			return substr($date, 2, 14);
		}
	}
}
if (!function_exists('convert_display_age')) {
	function convert_display_age($birthday) {
		if (!$birthday) return '';

		$today = date('Ymd');
		$birthymd = date('Ymd', strtotime($birthday));
		$age = floor(($today - $birthymd) / 10000);
		return $age;
	}
}