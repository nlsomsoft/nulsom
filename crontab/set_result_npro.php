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

$limit_count = 3000;
$datetime = time();
$agent = 'NPRO';
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

$time_flag = time() + rand(100,999);
$sql = "
	SELECT COUNT(*) cnt
	FROM NPRO_MSG_LOG
	WHERE ETC6 = 'N'
		AND CUR_STATE = '3'
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

$sql = "
	UPDATE NPRO_MSG_LOG
	SET ETC6 = 'Y',
		ETC5 = '{$time_flag}'
	WHERE ETC6 = 'N'
		AND CUR_STATE = '3'
	LIMIT {$limit_count}
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}

//need to check
$sql = "
	SELECT COUNT(*) cnt
	FROM NPRO_MSG_LOG
	WHERE ETC6 = 'Y'
		AND ETC5 = '{$time_flag}'
";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
mysqli_free_result($result);
if (!$row['cnt']) {
	mysqli_rollback($conn);
	mysqli_close($conn);
	// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
	exit;
}


$sql = "
	SELECT
		ETC1,
		SUM(CASE WHEN RSLT_CODE2 = '0' THEN 1 ELSE 0 END) AS success,
		SUM(CASE WHEN RSLT_CODE2 != '0' THEN 1 ELSE 0 END) AS fail
		FROM NPRO_MSG_LOG
	WHERE ETC6 = 'Y'
		AND ETC5 = '{$time_flag}'
	GROUP BY ETC1
";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
	// $commit_flag = 0;
	// mysqli_autocommit($conn, FALSE);
    // output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
		$procid = (int)$row['ETC1'];
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
		ETC1,
		ETC2,
		ETC4,
		ETC3,
		CALL_TO,
		CALL_FROM,
		CASE WHEN RSLT_CODE2 IN ('0', '1', '2') THEN RSLT_CODE2 ELSE 9 END,
		CASE WHEN RSLT_NET IS NOT NULL THEN RSLT_NET ELSE 'ETC' END,
		SENT_DATE,
		CASE WHEN RSLT_DATE IS NULL THEN NOW() ELSE RSLT_DATE END
	FROM NPRO_MSG_LOG
	WHERE ETC6 = 'Y'
		AND ETC5 = '{$time_flag}'
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}

$sql = "
	INSERT INTO NPRO_MSG_LOG_BACKUP
	SELECT *
	FROM NPRO_MSG_LOG
	WHERE ETC6 = 'Y'
		AND ETC5 = '{$time_flag}'
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}

$sql = "
	DELETE
	FROM NPRO_MSG_LOG
	WHERE ETC6 = 'Y'
		AND ETC5 = '{$time_flag}'
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
