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
$sql = "SELECT * FROM controller_refund";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
mysqli_free_result($result);
$gateway_val = (int)$row['gateway'];
$send_val = (int)$row['send'];
$cnt_val = (int)$row['cnt'];

if ($cnt_val > 2) {
	$sql  = 'UPDATE controller_refund SET gateway = 1, cnt = 1';
	if (!mysqli_query($conn, $sql)) {
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		// mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}

	$gdate = date('Y-m-d H:i:s',$gateway_val);
	error_log('['.$log_name.'][CONTROLLER.REFUND]...[DATE:'.$gdate.'][CNT:'.$cnt_val.']', 0);
	mysqli_close($conn);
	exit;
}
if ($gateway_val > 1) {
	$sql  = "UPDATE controller_refund SET cnt = cnt + 1";
	if (!mysqli_query($conn, $sql)) {
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		// mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}

	$gdate = date('Y-m-d H:i:s',$gateway_val);
	error_log('['.$log_name.'][CONTROLLER.REFUND]...[DATE:'.$gdate.'][CNT:'.$cnt_val.']', 0);
	mysqli_close($conn);
	exit;
}

$sql  = "UPDATE controller_refund SET gateway = '{$datetime}'";
// error_log($sql,0);
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	// mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}

$yyyy = date('Y');
$mm = date('m');
$dd = date('d');
$timestamp = time();
$sql = "
	SELECT *
	FROM sow_processunit_msg
	WHERE (status = '10' OR status = '11')
		AND remain_units = '0'
	LIMIT 0, 1
";

$commit_flag = 0;
mysqli_autocommit($conn, FALSE);

$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
		$success = (int)$row['success'];
		$fail = (int)$row['fail'];
		$storeno = (int)$row['storeno'];
		$userno = (int)$row['userno'];
		$userid = $row['userid'];
		$refund_val = (int)$row['refund_val']; // 실패건환불
		$procid = (int)$row['procid'];
		$price = (float)$row['price'];
		$realamount = (float)$row['realamount'];
		$fail_amount = (float)($fail * $price);
		// $newamount = $realamount - $fail_amount;
		$newamount = (float)($success * $price);

		$sql = "
			UPDATE sow_processunit_msg
			SET status = '100',
				realamount = '{$newamount}'
			WHERE procid = '{$procid}'
		";
		if (!mysqli_query($conn, $sql)) {
		    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
			mysqli_rollback($conn);
			mysqli_close($conn);
			exit;
		}
		//$refund_val : 1(실패건환불불가) 0(실패건환불)
		if ($refund_val == 0 && $fail_amount > 0) {
			$sql = "
				UPDATE users
				SET cash = cash + $fail_amount
				WHERE userno = '{$userno}'
			";
			if (!mysqli_query($conn, $sql)) {
			    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
				mysqli_rollback($conn);
				mysqli_close($conn);
				exit;
			}

			$sql = "
				INSERT INTO billing (
					storeno,
					userno,
					userid,
					procid,
					amount,
					mode,
					reg_time,
					yyyy,
					mm,
					dd
				) VALUES (
					'{$storeno}',
					'{$userno}',
					'{$userid}',
					'{$procid}',
					'{$fail_amount}',
					'PF',
					NOW(),
					'{$yyyy}',
					'{$mm}',
					'{$dd}'
				)
			";
			if (!mysqli_query($conn, $sql)) {
			    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
				mysqli_rollback($conn);
				mysqli_close($conn);
				exit;
			}

			$sql = "
				INSERT IGNORE INTO session_users (
					storeno,
					userid
				) VALUES (
					'{$storeno}',
					'{$userid}'
				)
			";
			if (!mysqli_query($conn, $sql)) {
			    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
				mysqli_rollback($conn);
				mysqli_close($conn);
				exit;
			}
		}
		// $commit_flag = 1;
	}
	// if ($commit_flag) mysqli_commit($conn);
}

$sql  = "UPDATE controller_refund SET gateway = 1, cnt = 1";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}
mysqli_commit($conn);
mysqli_free_result($result);
mysqli_close($conn);
exit;
// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
