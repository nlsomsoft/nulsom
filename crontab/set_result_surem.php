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
$agent = 'SUREM';
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

$result_table = 'SUREData_res';
$result_table_backup = 'SUREData_res_backup';
$timestamp = time();
$time_flag = $timestamp + rand(100,999);

$sql = "
	SELECT COUNT(*) cnt
	FROM {$result_table}
	WHERE ETC4 = 'N'
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

/*
ETC1 : procid
ETC2 : storeno
ETC3 : userno
ETC4 : 'N'
ETC5 : ''
*/

$sql = "
	UPDATE {$result_table}
	SET ETC4 = 'C',
		ETC5 = '{$time_flag}'
	WHERE ETC4 = 'N'
	LIMIT {$limit_count}
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}

$sql = "
	SELECT  ETC1,
			SUM(CASE WHEN RESULT = '2' THEN 1 ELSE 0 END) AS SUCCESS,
			SUM(CASE WHEN RESULT != '2' THEN 1 ELSE 0 END) AS FAIL
	FROM {$result_table}
	WHERE ETC4 = 'C'
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
		$success = (int)$row['SUCCESS'];
		$fail = (int)$row['FAIL'];
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
		ETC3,
		CALLNAME,
		CALLPHONE,
		REQPHONE,
		CASE WHEN RESULT = '2' THEN 0 ELSE ERRCODE END,
		MEDIATYPE,
		INTIME,
		RECVTIME
	FROM {$result_table}
	WHERE ETC4 = 'C'
		AND ETC5 = '{$time_flag}'
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}
/*
$sql = "
	DELETE FROM BIZ_LOG
	WHERE MOVE_FLAG = 'C'
		AND TIME_FLAG = '{$time_flag}'
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}
*/

$sql = "
	INSERT INTO {$result_table_backup}
	SELECT *
	FROM {$result_table}
	WHERE ETC4 = 'C'
		AND ETC5 = '{$time_flag}'
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}

$sql = "
	DELETE FROM {$result_table}
	WHERE ETC4 = 'C'
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
