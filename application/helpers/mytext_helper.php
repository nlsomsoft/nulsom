<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('my_character_limiter')) {
function my_character_limiter($str, $n = 500, $end_char = '...') {
    $CI =& get_instance();
    $charset = $CI->config->item('charset');

    if (mb_strlen( $str , $charset) < $n) {
        return $str ;
    }

    $str = preg_replace("/\s+/iu", ' ', str_replace(array('\r\n', '\r', '\n'), ' ', $str));

    if (mb_strlen($str , $charset) <= $n) {
        return $str;
    }
    return mb_substr(trim($str), 0, $n ,$charset) . $end_char ;
}
}