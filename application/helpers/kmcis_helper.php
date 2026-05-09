<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('paramChk')) {
    // Start - [ 입력값 유효성 검증 ]----------------------------------------------------------------------------------
    // 비정상적인 호출, XSS공격, SQL Injection 방지를 위해 입력값 유효성 검증 후 서비스를 호출해야 함
    function paramChk($pattern, $param){
        $result = preg_match($pattern, $param);
        return $result;
    }
}
