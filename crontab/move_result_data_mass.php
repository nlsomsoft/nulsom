<?php
// define filename
$file_name = $_SERVER['PHP_SELF'];
$log_name = $_SERVER['STORENAME'];

//error_log(print_r($_SERVER,1),0);
if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
    error_log('['.$log_name.'][E]...[REMOTE_ADDR]['.$file_name.']', 0);
    exit;
}

if (date('H') > 6) {
    error_log('['.$log_name.']...[ILLEGAL_MASS TIME]', 0);
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

include_once ($_SERVER['DOCUMENT_ROOT'].'/application/views/templates/define.php');
if (MAX_RESULT_CNT == '') {
	error_log('['.$log_name.'][E]...[DEFINE]['.$file_name.']', 0);
	exit;
}
$tbl = MAX_RESULT_CNT;
$result_table = 'result_'.$tbl;


// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
	error_log('['.$log_name.'][E]...[CONNECT DB]['.$file_name.']', 0);
	exit;
}
error_log('['.$log_name.'][S]...['.$file_name.']', 0);

$datetime = time();
$agent = 'RESULT_MASS';
$sql = "SELECT * FROM controller_batch WHERE agent='{$agent}'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
mysqli_free_result($result);
$gateway_val = (int)$row['gateway'];
$send_val = (int)$row['send'];
$cnt_val = (int)$row['cnt'];

if ($cnt_val > 1) {
	$sql  = "UPDATE controller_batch SET gateway = 1, cnt = 1 WHERE agent = '{$agent}'";
	if (!mysqli_query($conn, $sql)) {
		error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}

	$gdate = date('Y-m-d H:i:s',$gateway_val);
	error_log('['.$log_name.'][CONTROLLER_BATCH:'.$agent.']...[DATE:'.$gdate.'][CNT:'.$cnt_val.']', 0);
	mysqli_close($conn);
	exit;
}
if ($gateway_val > 1) {
	$sql  = "UPDATE controller_batch SET cnt = cnt + 1 WHERE agent = '{$agent}'";
	if (!mysqli_query($conn, $sql)) {
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		// mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}

	$gdate = date('Y-m-d H:i:s',$gateway_val);
	error_log('['.$log_name.'][CONTROLLER_BATCH:'.$agent.']...[DATE:'.$gdate.'][CNT:'.$cnt_val.']', 0);
	mysqli_close($conn);
	exit;
}

$sql  = "UPDATE controller_batch SET gateway = '{$datetime}' WHERE agent='{$agent}'";
// error_log($sql,0);
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	// mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}


mysqli_autocommit($conn, FALSE);

$yyyy = date('Y');
$mm = date('m');
$dd = date('d');
$timestamp = time();
$sql = "
	SELECT *
	FROM sow_processunit_msg
	WHERE status = '100'
		AND total_units >= 10
		AND tbl = '0'
	ORDER BY procid ASC
	LIMIT 0, 1
";
// error_log($sql,0);
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
	// $commit_flag = 0;
	// mysqli_autocommit($conn, FALSE);
    // output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
		$procid = (int)$row['procid'];
		$userno = (int)$row['userno'];
		if (!$procid || !$userno) {
			error_log('[critical error][userno,procid is empty]...['.$procid.']['.$userno.']['.$file_name.']', 0);
			$sql  = "UPDATE sow_processunit_msg SET tbl = '997' WHERE procid = '{$procid}' ";
			error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
			if (!mysqli_query($conn, $sql)) {
			    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
				mysqli_rollback($conn);
				mysqli_close($conn);
				exit;
			}
			continue;
		}

		$sql11 = "SELECT xid FROM result_0 WHERE procid = '{$procid}' LIMIT 1 ";
		$result11 = mysqli_query($conn, $sql11);
		$row11 = mysqli_fetch_assoc($result11);
		mysqli_free_result($result11);
		$xid11 = (int)$row11['xid'];
		if (!$xid11) {
			error_log('[critical error][result_0 is empty]...['.$sql11.']['.$file_name.']', 0);
			$sql  = "UPDATE sow_processunit_msg SET tbl = '999' WHERE procid = '{$procid}' ";
			error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
			if (!mysqli_query($conn, $sql)) {
			    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
				mysqli_rollback($conn);
				mysqli_close($conn);
				exit;
			}
			continue;
		}

		$sql = "
			INSERT INTO {$result_table}
			SELECT *
			FROM result_0
			WHERE procid = '{$procid}'
				AND userno = '{$userno}'
		";
// error_log($sql, 0);
		if (!mysqli_query($conn, $sql)) {
		    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
			mysqli_rollback($conn);
			mysqli_close($conn);
			exit;
		}

		$sql12 = "SELECT xid FROM {$result_table} WHERE procid = '{$procid}' LIMIT 1 ";
		$result12 = mysqli_query($conn, $sql12);
		$row12 = mysqli_fetch_assoc($result12);
		mysqli_free_result($result12);
		$xid12 = (int)$row12['xid'];
		if (!$xid12) {
			error_log('[critical error]['.$result_table.' is empty]...['.$sql12.']['.$file_name.']', 0);
			$sql  = "UPDATE sow_processunit_msg SET tbl = '998' WHERE procid = '{$procid}' ";
			error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
			if (!mysqli_query($conn, $sql)) {
			    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
				mysqli_rollback($conn);
				mysqli_close($conn);
				exit;
			}
			continue;
		}

		$sql = "
			DELETE FROM result_0
			WHERE procid = '{$procid}'
				AND userno = '{$userno}'
		";
// error_log($sql, 0);
		if (!mysqli_query($conn, $sql)) {
		    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
			mysqli_rollback($conn);
			mysqli_close($conn);
			exit;
		}

		$sql = "
			UPDATE sow_processunit_msg
			SET tbl = '{$tbl}'
			WHERE procid = '{$procid}'
				AND userno = '{$userno}'
		";
// error_log($sql, 0);
		if (!mysqli_query($conn, $sql)) {
		    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
			mysqli_rollback($conn);
			mysqli_close($conn);
			exit;
		}
		// $commit_flag = 1;
	}
	// if ($commit_flag) mysqli_commit($conn);
}

$sql  = "UPDATE controller_batch SET gateway = 1, cnt = 1 WHERE agent = '{$agent}'";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}

mysqli_commit($conn);
mysqli_free_result($result);
mysqli_close($conn);
error_log('['.$log_name.'][F]...['.$file_name.']', 0);
exit;
