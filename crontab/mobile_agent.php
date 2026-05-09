<?php
exit;

$file_name = $_SERVER['PHP_SELF'];
$log_name = $_SERVER['STORENAME'];

// error_log(print_r($_POST,1), 0);
// error_log(print_r($_GET,1), 0);
//site_procid_targetno

$msg = trim($_GET['msg']);
if (!$msg) {
	error_log('['.$log_name.'][E]...['.$msg.']['.$file_name.']', 0);
	exit;
}

$pos = strpos($msg, '_');
if ($pos === false) {
	// error_log('['.$log_name.'][E]...[format error]['.$file_name.']', 0);
	exit;
}

$msg_array = explode('_', $msg);
if (!$msg_array[0] || !$msg_array[1] || !$msg_array[2]) {
	error_log('['.$log_name.'][E]...['.$msg.']['.$file_name.']', 0);
	exit;
}

// define database
define('BASEPATH', $_SERVER['DOCUMENT_ROOT']);
define('ENVIRONMENT', '');
include_once(BASEPATH.'/application/config/database.php');
// $servername = 'localhost';
$servername = $db['default']['hostname'];
$username	= $db['default']['username'];
$password	= $db['default']['password'];
$database	= $db['default']['database'];
// define database

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
	error_log('['.$log_name.'][E]...[CONNECT DB]['.$file_name.']', 0);
	exit;
}
// error_log('['.$log_name.'][S]...['.$file_name.']', 0);


mysqli_autocommit($conn, FALSE);
$sql = "
	INSERT INTO mobile_test_logs
	SET store = '{$msg_array[0]}',
		procid = '{$msg_array[1]}',
		mobile = '{$msg_array[2]}'
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}
mysqli_commit($conn);
mysqli_close($conn);

$param['para1'] = strtolower($msg_array[0]);
$param['para2'] = $msg_array[1];
$param['para3'] = $msg_array[2];

if ($param['para1'] == 'kopo') {
	$url = 'http://kopo365.com';
} else if ($param['para1'] == 'aone1000') {
	$url = 'http://cron.aone1000.com';
} else {
	error_log('['.$log_name.'][E]...[undefined site : '.$param['para1'].']['.$file_name.']', 0);
	exit;
}

exit;



$dest_url = $url.'/crontab/mobile_client.php';
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

// error_log('response:'.$response, 0);
exit;

