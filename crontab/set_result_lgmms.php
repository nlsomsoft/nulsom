<?php
// define filename
$file_name = $_SERVER['PHP_SELF'];
$log_name = strtoupper($_SERVER['STORENAME']);
$agent_name = 'LGMMS';

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
$agent = 'LGMMS';
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

// $time_flag = date('dHis');
// $rand_val = rand(100,999); //22131805553
// $unique_key = $time_flag.$rand_val;
//2520342561262ac1060a5
// $unique_key = uniqid(date('dHis'));
// if (strlen($unique_key) < 21) {
// 	mysqli_close($conn);
// 	error_log('['.$log_name.'][KEY ERROR]...['.$file_name.']', 0);
// 	exit;
// }

$table_name = 'LG_MMS_MSG';
$backup_table_name = 'LG_MMS_LOG_BACKUP';

$sql = "
	SELECT COUNT(*) cnt
	FROM {$table_name}
	WHERE ID IS NULL 
		AND status = '3'
";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
mysqli_free_result($result);
if (!$row['cnt']) {
	mysqli_close($conn);
	// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
	unlink($file_path);
	exit;
}

mysqli_autocommit($conn, FALSE);

// ETC2 varchar(32)
$sql = "
	UPDATE {$table_name}
	SET ID = 'B'
	WHERE ID IS NULL
		AND STATUS = '3'
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
		ETC1,
		SUM(CASE WHEN RSLT = '1000' THEN 1 ELSE 0 END) AS SUCCESS,
		SUM(CASE WHEN RSLT != '1000' THEN 1 ELSE 0 END) AS FAIL
	FROM {$table_name}
	WHERE ID = 'B'
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
		ETC1,
		ETC3,
		ETC4,
		ETC2,
		PHONE,
		CALLBACK,
		RSLT,
		CASE WHEN TELCOINFO IS NOT NULL THEN TELCOINFO ELSE 'ETC' END,
		reqdate,
		CASE WHEN REPORTDATE IS NULL THEN NOW() ELSE REPORTDATE END
		FROM {$table_name}
		WHERE ID = 'B'
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
	SET ID = 'E'
	WHERE ID = 'B'
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
	SELECT * FROM {$table_name}
	WHERE ID = 'E'
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
	WHERE ID = 'E'
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
