<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('noti_message')) {
function noti_message($message) {
    $data = array(
        'uuid' => '84fcdfaf8497c099',
        'secret_key' => 'YbcM874rO0',
        'code' => 'sowkorea'
    );
    $data['body'] = $message;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://push.doday.net/api/push');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt ($ch, CURLOPT_TIMEOUT,1);
    $contents = curl_exec($ch);
    curl_close($ch);
}
}
