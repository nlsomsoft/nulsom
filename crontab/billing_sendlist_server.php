<?php
// define filename
$file_name = $_SERVER['PHP_SELF'];
$log_name = $_SERVER['STORENAME'];

//error_log(print_r($_SERVER,1),0);
if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
    error_log('['.$log_name.'][E]...[REMOTE_ADDR]['.$file_name.']', 0);
    exit;
}

define('BASEPATH', $_SERVER['DOCUMENT_ROOT']);
define('ENVIRONMENT', '');
include_once(BASEPATH.'/application/config/database.php');
// $servername = 'localhost';
$servername = $db['default']['hostname'];
$username   = $db['default']['username'];
$password   = $db['default']['password'];
$database   = $db['default']['database'];

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
    error_log('['.$log_name.'][E]...[CONNECT DB]['.$file_name.']', 0);
    exit;
}
// error_log('Connected successfully', 0);
// error_log('['.$log_name.'][S]...['.$file_name.']', 0);

$i = 0;
// $siteinfo[$i]['url'] = 'http://kopo365.com/';
// $siteinfo[$i]['company'] = 'kopo_kt';
// $siteinfo[$i++]['priority'] = '200,700';


// error_log(print_r($siteinfo,1),0);
if (!$i) exit;

$rcv_data = array();
foreach ($siteinfo as $value) {
    $url = '';
    $site = '';
    $param = '';
    foreach ($value as $key => $val) {
        if (!$val) continue;
        if ($key == 'url') $url = $val;
        else $param[$key] = $val;

        if ($key == 'company') $site = $val;
    }
    $res = getWebPage($url, $param);
    $rcv_data[$site] = $res;
}
// error_log(print_r($rcv_data,1),0);


if ($_SERVER['STORENAME'] == 'yeil') $table_name = 'yeil_company_send_list';
else $table_name = 'company_send_list';


mysqli_autocommit($conn, FALSE);


foreach ($rcv_data as $gkey => $gval) {
// error_log($gkey.':'.$gval,0);

    $company = $gkey;
    $all_array = array();
    $all_array = explode(':', $gval);
    $cate = 0;
    foreach ($all_array as $arow) {
        $param_array = explode('|', $arow);
    // error_log(print_r($param_array,1),0);

        foreach ($param_array as $row) {
            $row_array = explode('-', $row);
            $yyyymm = (isset($row_array[0]) ? $row_array[0] : '');
            $send_type = (isset($row_array[1]) ? $row_array[1] : '');
            $send_cnt = (isset($row_array[2]) ? $row_array[2] : '');

            $col_name = '';
            if ($send_type == 'SMS' || $send_type == 'SMS1') {
                $col_name = 'sms_cnt';
            } else if ($send_type == 'LMS' || $send_type == 'LMS1') {
                $col_name = 'lms_cnt';
            } else if ($send_type == 'MMS' || $send_type == 'MMS1') {
                $col_name = 'mms_cnt';
            } else {
                continue;
            }


            $sql = "
                INSERT INTO {$table_name} (
                    company,
                    yyyymm,
                    cate,
                    {$col_name}
                ) VALUES (
                    '{$company}',
                    '{$yyyymm}',
                    '{$cate}',
                    '{$send_cnt}'
                )
                ON DUPLICATE KEY UPDATE 
                    {$col_name} = '{$send_cnt}'
            ";
            if (!mysqli_query($conn, $sql)) {
                mysqli_rollback($conn);
                mysqli_close($conn);
                error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
            }
        }
        $cate ++;
    }
}

mysqli_commit($conn);
mysqli_close($conn);
// error_log('['.$log_name.'][F]...['.$file_name.']', 0);

function getWebPage($url,$param) {
    $dest_url = $url.'/crontab/billing_sendlist_client.php';
    $post_field_string = http_build_query($param, '', '&');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $dest_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field_string);
    curl_setopt($ch, CURLOPT_POST, true);
    $response = curl_exec($ch);
    curl_close ($ch);
     
    return $response;
}
exit;
