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

$xid = trim($_POST['xid']);
$amount = trim($_POST['amount']);
$company = trim($_POST['company']);
$userid = trim($_POST['userid']);
$ip = $_SERVER['REMOTE_ADDR'];

if (!$xid || !$amount || !$company || !$userid) {
	error_log('['.$log_name.'][E]...[PARMETER]['.$xid.']['.$amount.']['.$company.']['.$userid.']['.$file_name.']', 0);
	exit('0');
}


$sql = "
	INSERT INTO company_deposit_list (
		company,
		ip,
		userid,
		memo,
		amount
	) VALUES (
		'{$company}',
		'{$ip}',
		'{$userid}',
		'{$xid}',
		'{$amount}'
	)
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
}

mysqli_close($conn);
// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
exit('100');
