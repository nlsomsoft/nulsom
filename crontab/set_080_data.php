<?php
// define filename
$file_name = $_SERVER['PHP_SELF'];
$log_name = $_SERVER['STORENAME'];

//error_log(print_r($_SERVER,1),0);
if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
    error_log('['.$log_name.'][E]...[REMOTE_ADDR]['.$file_name.']', 0);
    exit;
}

$servername = '58.229.206.20';
$username = '080_CALLDB1110';
$password = 'calldb3355@3355';
$database = '080_CALLDB1110';

// Create connection
$conn1 = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn1) {
	error_log('['.$log_name.'][E]...[CONNECT FIRSTNET DB]['.$file_name.']', 0);
	exit;
}
// error_log('['.$log_name.'][S]...['.$file_name.']', 0);

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

$sql = "
	SELECT MAX(reg_time) reg_time
	FROM phone_080
";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
mysqli_free_result($result);
$reg_time = $row['reg_time'];
$storeno = '1'; //default value


$timestamp = time();
$sql = "
	SELECT *
	FROM 080_CALLDB1110.CALL_INFO_VIEW
	WHERE CALL_SDT >= '$reg_time'
	ORDER BY CALL_SDT ASC
	LIMIT 100
";
$result1 = mysqli_query($conn1, $sql);
if (mysqli_num_rows($result1) > 0) {
	$last_check_date = '';
	$commit_flag = 0;
	mysqli_autocommit($conn, FALSE);

    // output data of each row
    while ($row = mysqli_fetch_assoc($result1)) {
// error_log(print_r($row,1),0);
		$TEL_FROM = $row['TEL_FROM'];
		$TEL_TO = $row['TEL_TO'];
		$CALL_SDT = $row['CALL_SDT'];
		// $CALL_DTMF = $row['CALL_DTMF'];
		if ($TEL_FROM == '' || $TEL_TO == '' || $CALL_SDT == '') continue;

		$sql = "
			INSERT IGNORE INTO phone_080 (
				mobile,
				phone_080,
				reg_time,
				storeno
			) VALUES (
				'{$TEL_FROM}',
				'{$TEL_TO}',
				'{$CALL_SDT}',
				'{$storeno}'
			)
		";
		if (!mysqli_query($conn, $sql)) {
		    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
			// mysqli_rollback($conn);
			// mysqli_close($conn);
			// exit;
		}
		$commit_flag = 1;
	}
	if ($commit_flag) mysqli_commit($conn);
}
mysqli_free_result($result1);
mysqli_close($conn1);
// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
exit;
