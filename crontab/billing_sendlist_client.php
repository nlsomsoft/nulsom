<?php
// define filename
$file_name = $_SERVER['PHP_SELF'];
$log_name = $_SERVER['STORENAME'];

// error_log(print_r($_SERVER,1),0);
if ($_SERVER['REMOTE_ADDR'] != '121.78.112.224' && $_SERVER['REMOTE_ADDR'] != '182.162.73.223' && $_SERVER['REMOTE_ADDR'] != '211.174.61.140' && $_SERVER['REMOTE_ADDR'] != '121.78.112.224') {
    error_log('['.$log_name.'][E]...[REMOTE_ADDR]['.$file_name.']', 0);
    exit('0');
}

define('BASEPATH', $_SERVER['DOCUMENT_ROOT']);
define('ENVIRONMENT', '');
include_once(BASEPATH.'/application/config/database.php');
// $servername = 'localhost';
$servername = $db['default']['hostname'];
$username	= $db['default']['username'];
$password	= $db['default']['password'];
$database	= $db['default']['database'];


// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
	error_log('['.$log_name.'][E]...[CONNECT DB]['.$file_name.']', 0);
	exit('0');
}
// error_log('Connected successfully', 0);
// error_log('['.$log_name.'][S]...['.$file_name.']', 0);
//error_log(print_r($_SERVER,1),0);
//error_log(print_r($_POST,1),0);
//error_log(print_r($_GET,1),0);
/*
for ($i = 0; $i < 10; $i++) {
	$sql = "
		DELETE
		FROM address_{$i}
		WHERE state = '1'
			AND remove_date < DATE_ADD(NOW(), interval - 2 day)
		LIMIT 50000
	";
	if (!mysqli_query($conn, $sql)) {
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	}
}
*/

$priority = trim($_POST['priority']);
// $company = trim($_POST['company']);


if (!$priority) {
	error_log('['.$log_name.'][E]...[PARMETER]['.$priority.']['.$file_name.']', 0);
	exit('0');
}


$send_val = '';
$sql = "
	SELECT CONCAT(DATE_FORMAT(reserve_time, '%Y%m'), '-', productcode, '-', SUM(success)) AS sentinfo 
	FROM sow_processunit_msg 
	WHERE reserve_time >= LAST_DAY(NOW() - INTERVAL 2 MONTH) + INTERVAL 1 DAY 
		AND priority IN ({$priority}) 
	GROUP BY DATE_FORMAT(reserve_time, '%Y%m'), productcode;
";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
		$sentinfo = $row['sentinfo'];
		if ($send_val != '') $send_val .= '|';
		$send_val .= $sentinfo;
	}
}
mysqli_free_result($result);
mysqli_close($conn);
// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
exit($send_val);
