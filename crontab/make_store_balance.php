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
// error_log('Connected successfully', 0);
// error_log('['.$log_name.'][S]...['.$file_name.']', 0);

$store_array = array();
$sql = "
	SELECT storeno
	FROM store
	WHERE check_balance = '1'
";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
    	$store_array[] = (int)$row['storeno'];
    }
}
mysqli_free_result($result);

$commit_flag = 0;
mysqli_autocommit($conn, FALSE);

foreach ($store_array as $storeno) {
	if (!$storeno) continue;

	$inc_amount = 0;
	$out_amount = 0;
	$balance = 0;
	$sql = "
		SELECT SUM(amount) amount
		FROM store_billing
		WHERE storeno = '{$storeno}'
	";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
	    // output data of each row
	    while ($row = mysqli_fetch_assoc($result)) {
	    	$inc_amount = (float)$row['amount'];
	    }
	}
	mysqli_free_result($result);

	$sql = "
		SELECT
			SUM(sms_channel_amount) amount1,
			SUM(lms_channel_amount) amount2,
			SUM(mms_channel_amount) amount3,
			SUM(kko_channel_amount) amount4
		FROM stat_sending_month
		WHERE storeno = '{$storeno}'
	";

	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
	    // output data of each row
	    while ($row = mysqli_fetch_assoc($result)) {
	    	$out_amount = (float)$row['amount1'] + (float)$row['amount2'] + (float)$row['amount3'] + (float)$row['amount4'];
	    }
	}
	mysqli_free_result($result);

	$balance = $inc_amount - $out_amount;
	$sql2 = "
		INSERT INTO store_balance
		SET storeno = '{$storeno}',
			balance = '{$balance}',
			check_time = '{$current_time}'
		ON DUPLICATE KEY
		UPDATE
			storeno = '{$storeno}',
			balance = '{$balance}',
			check_time = '{$current_time}'
	";
	if (!mysqli_query($conn, $sql2)) {
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}
	$commit_flag = 1;
}
if ($commit_flag) mysqli_commit($conn);
mysqli_close($conn);
// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
exit;
