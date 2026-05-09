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
$username	= $db['default']['username'];
$password	= $db['default']['password'];
$database	= $db['default']['database'];

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
	error_log('['.$log_name.'][E]...[CONNECT DB]['.$file_name.']', 0);
	exit;
}
error_log('['.$log_name.'][S]...['.$file_name.']', 0);

$limit_count = 10000;
$datetime = time();
$agent = 'AGENT_RST';
$gdate = date('Y-m-d H:i:s');
$file_path = $_SERVER['DOCUMENT_ROOT'].'/uploads/'.$agent;

if (!file_exists($file_path)) {
	if (!($fp = fopen($file_path, 'w+'))) {
		error_log('['.$log_name.']['.$file_name.']...[DATE:'.$gdate.'] [Error1]', 0);
		exit;
	}
	fwrite($fp, time());
	fclose($fp);
} else {
	error_log('['.$log_name.']['.$file_name.']...[DATE:'.$gdate.'] [Error2]', 0);
	mysqli_close($conn);
	exit;
}

$cur_time = time();
$agent_name = 'result_0';

mysqli_autocommit($conn, FALSE);
$sql = "
	DELETE
	FROM {$agent_name}
	WHERE send_time < DATE_ADD(NOW(), INTERVAL -30 DAY)
	LIMIT {$limit_count}
";
// error_log($sql,0);
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
    unlink($file_path);
	mysqli_rollback($conn);
	mysqli_close($conn);
    exit;
}
mysqli_commit($conn);
mysqli_close($conn);
unlink($file_path);
error_log('['.$log_name.'][F]...['.$file_name.']', 0);
exit;