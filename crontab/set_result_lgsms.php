<?php
// define filename
$file_name = $_SERVER['PHP_SELF'];
$log_name = strtoupper($_SERVER['STORENAME']);
$agent_name = 'LGSMS';

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
// error_log('['.$log_name.'][S]...['.$file_name.']', 0);

$limit_count = 3000;
$datetime = time();
$agent = 'LGSMS';
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

//2520342561262ac1060a5
// $unique_key = uniqid(date('dHis'));
// if (strlen($unique_key) < 21) {
// 	mysqli_close($conn);
// 	error_log('['.$log_name.'][KEY ERROR]...['.$file_name.']', 0);
// 	exit;
// }

$table_name = 'SC_TRAN';
$backup_table_name = 'SC_LOG_BACKUP';

$sql = "
	SELECT COUNT(*) cnt
	FROM {$table_name}
	WHERE TR_ETC5 IS NULL 
		AND TR_SENDSTAT = '2'
";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
mysqli_free_result($result);
if (!$row['cnt']) {
	mysqli_close($conn);
	unlink($file_path);
	// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
	exit;
}


mysqli_autocommit($conn, FALSE);

//TR_ETC6 varchar(160)
$sql = "
	UPDATE {$table_name}
	SET TR_ETC5 = 'B'
	WHERE TR_ETC5 IS NULL
		AND TR_SENDSTAT = '2'
	LIMIT {$limit_count}
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	unlink($file_path);
	exit;
}

$sql = "
	SELECT
		TR_ETC1,
		SUM(CASE WHEN TR_RSLTSTAT = '06' THEN 1 ELSE 0 END) AS SUCCESS,
		SUM(CASE WHEN TR_RSLTSTAT != '06' THEN 1 ELSE 0 END) AS FAIL
	FROM {$table_name}
	WHERE TR_ETC5 = 'B'
	GROUP BY TR_ETC1
";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
	// $commit_flag = 0;
	// mysqli_autocommit($conn, FALSE);
    // output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
		$procid = (int)$row['TR_ETC1'];
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
			unlink($file_path);
			exit;
		}
error_log("{$log_name} / {$agent_name} / S:{$success} / F:{$fail} / T:{$sum}", 0);
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
		TR_ETC1,
		TR_ETC3,
		TR_ETC4,
		TR_ETC2,
		TR_PHONE,
		TR_CALLBACK,
		CASE WHEN TR_RSLTSTAT = '06' THEN 0 ELSE TR_RSLTSTAT END,
		CASE WHEN TR_NET IS NOT NULL THEN tr_net ELSE 'ETC' END,
		TR_SENDDATE,
		CASE WHEN TR_RSLTDATE IS NULL THEN NOW() ELSE TR_RSLTDATE END
	FROM {$table_name}
	WHERE TR_ETC5 = 'B'
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	unlink($file_path);
	exit;
}

$sql = "
	UPDATE {$table_name}
	SET TR_ETC5 = 'E'
	WHERE TR_ETC5 = 'B'
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	unlink($file_path);
	exit;
}

$sql = "
	INSERT INTO {$backup_table_name}
	SELECT *
	FROM {$table_name}
	WHERE TR_ETC5 = 'E'
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	unlink($file_path);
	exit;
}

$sql = "
	DELETE FROM {$table_name}
	WHERE TR_ETC5 = 'E'
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	unlink($file_path);
	exit;
}

mysqli_commit($conn);
mysqli_free_result($result);
mysqli_close($conn);
unlink($file_path);
// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
exit;
