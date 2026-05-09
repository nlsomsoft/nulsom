<?php
// define filename
$file_name = $_SERVER['PHP_SELF'];
$log_name = $_SERVER['STORENAME'];

//error_log(print_r($_SERVER,1),0);
if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
    error_log('['.$log_name.'][E]...[REMOTE_ADDR]['.$file_name.']', 0);
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

$datetime = time();
$agent = 'XROSHOTLMS2';
$sql = "SELECT * FROM controller_result WHERE agent='{$agent}'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
mysqli_free_result($result);
$gateway_val = (int)$row['gateway'];
$send_val = (int)$row['send'];
$cnt_val = (int)$row['cnt'];

if ($cnt_val > 5) {
	$sql  = "UPDATE controller_result SET gateway = 1, cnt = 1 WHERE agent = '{$agent}'";
	if (!mysqli_query($conn, $sql)) {
		error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}

	$gdate = date('Y-m-d H:i:s',$gateway_val);
	error_log('['.$log_name.'][CONTROLLER_RESULT:'.$agent.']...[DATE:'.$gdate.'][CNT:'.$cnt_val.']', 0);
	mysqli_close($conn);
	exit;
}
if ($gateway_val > 1) {
	$sql  = "UPDATE controller_result SET cnt = cnt + 1 WHERE agent = '{$agent}'";
	if (!mysqli_query($conn, $sql)) {
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		// mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}

	$gdate = date('Y-m-d H:i:s',$gateway_val);
	error_log('['.$log_name.'][CONTROLLER_RESULT:'.$agent.']...[DATE:'.$gdate.'][CNT:'.$cnt_val.']', 0);
	mysqli_close($conn);
	exit;
}

//2520342561262ac1060a5
$unique_key = uniqid(date('dHis'));
if (strlen($unique_key) < 21) {
	mysqli_close($conn);
	error_log('['.$log_name.'][KEY ERROR]...['.$file_name.']', 0);
	exit;
}

$sql = "
	SELECT COUNT(*) cnt
	FROM SDK_MMS_REPORT_A a, SDK_MMS_REPORT_DETAIL_A b
	WHERE a.reserved5 = 'N'
		AND a.reserved1 > '0'
		AND b.tcs_result >= '0'
		AND a.msg_id = b.msg_id
";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
mysqli_free_result($result);
if (!$row['cnt']) {
	mysqli_close($conn);
	// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
	exit;
}

$sql  = "UPDATE controller_result SET gateway = '{$datetime}' WHERE agent='{$agent}'";
// error_log($sql,0);
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	// mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}


mysqli_autocommit($conn, FALSE);

//reserved5 varchar(50)
$sql = "
	UPDATE SDK_MMS_REPORT_A a, SDK_MMS_REPORT_DETAIL_A b
	SET a.reserved5 = '{$unique_key}',
		a.reserved6 = b.tcs_result,
		a.reserved7 = b.phone_number,
		a.reserved8 = b. mobile_info,
		a.reserved9 = b.deliver_date,
		b.subjob_id = 9
	WHERE a.reserved5 = 'N'
		AND a.reserved1 > '0'
		AND b.tcs_result >= '0'
		AND a.msg_id = b.msg_id
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}

$sql = "
	SELECT
		reserved1,
		SUM(CASE WHEN reserved6 = 0 THEN 1 ELSE 0 END) AS success,
		SUM(CASE WHEN reserved6 > 0 THEN 1 ELSE 0 END) AS fail
	FROM SDK_MMS_REPORT_A
	WHERE reserved5 = '{$unique_key}'
	GROUP BY reserved1
";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
		$procid = (int)$row['reserved1'];
		$success = (int)$row['success'];
		$fail = (int)$row['fail'];
		$sum = $success + $fail;

		$sql = "
			UPDATE sow_processunit_msg
			SET remain_units = remain_units - $sum,
				success = success + $success,
				fail = fail + $fail
			WHERE procid = '{$procid}'
				AND status = '10'
		";
		if (!mysqli_query($conn, $sql)) {
		    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
			mysqli_rollback($conn);
			mysqli_close($conn);
			exit;
		}
	}
}

$sql = "
	INSERT INTO result_0
	(
		procid,
		storeno,
		userno,
		targetname,
		targetno,
		callback,
		result,
		telecom,
		send_time,
		result_time
	)
	SELECT
		reserved1,
		reserved3,
		reserved4,
		reserved2,
		reserved7,
		callback,
		reserved6,
		reserved8,
		DATE_FORMAT(send_date, '%Y-%m-%d %H:%i:%s'),
		CASE WHEN reserved9 != '' THEN DATE_FORMAT(reserved9, '%Y-%m-%d %H:%i:%s') ELSE NOW() END
		FROM SDK_MMS_REPORT_A
		WHERE reserved5 = '{$unique_key}'
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}
/*
$sql = "
	UPDATE SDK_MMS_REPORT_A
	SET reserved5 = 'Y'
	WHERE reserved5 = 'C'
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}
*/
$sql = "
	DELETE FROM SDK_MMS_REPORT_A
	WHERE reserved5 = '{$unique_key}'
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}
$sql = "
	DELETE FROM SDK_MMS_REPORT_DETAIL_A
	WHERE subjob_id = 9
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}


$sql  = "UPDATE controller_result SET gateway = 1, cnt = 1 WHERE agent = '{$agent}'";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}

mysqli_commit($conn);
mysqli_free_result($result);
mysqli_close($conn);
// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
exit;
