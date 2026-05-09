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

$cost_array = array();
$sql = "
	SELECT storeno,contract_sms,contract_lms,contract_mms,contract_kko
	FROM store
";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
    	$storeno = (int)$row['storeno'];
		$cost_array[$storeno]['sms'] = (float)$row['contract_sms'];
		$cost_array[$storeno]['lms'] = (float)$row['contract_lms'];
		$cost_array[$storeno]['mms'] = (float)$row['contract_mms'];
		$cost_array[$storeno]['kko'] = (float)$row['contract_kko'];
    }
}
mysqli_free_result($result);

$channel_price_array = array();
$sql = "
	SELECT channel,price
	FROM channel
";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
    	$priority = (int)$row['channel'];
		$channel_price_array[$priority] = (float)$row['price'];
    }
}
mysqli_free_result($result);

// error_log(print_r($channel_price_array,1),0);
// exit;


$stat_array = array();
$cur_time = time();
$yyyy = (int)date('Y');
$mm = (int)date('m');
$yyyymm = sprintf('%04d%02d',$yyyy,$mm);

$sql = "
	SELECT storeno,groupno,productcode,priority,SUM(success) success,SUM(realamount) realamount
	FROM sow_processunit_msg
	WHERE status > 0
		AND yyyy = '{$yyyy}' AND mm = '{$mm}'
	GROUP BY storeno, groupno, productcode, priority
";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
    	$storeno = (int)$row['storeno'];
    	$groupno = (int)$row['groupno'];
    	$productcode = trim($row['productcode']);
    	$priority = (int)$row['priority'];
    	$success = (int)$row['success'];
    	$realamount = (float)$row['realamount'];

    	if ($productcode == 'SMS1' || $productcode == 'SMS2') {
    		$basicamount = (float)($success * (float)$cost_array[$storeno]['sms']);
    		$success_cnt = (!isset($stat_array[$storeno][$groupno]['sms_cnt']) ? $success : ($stat_array[$storeno][$groupno]['sms_cnt'] + $success));
    		$basic_amount = (!isset($stat_array[$storeno][$groupno]['sms_basic_amount']) ? $basicamount : ($stat_array[$storeno][$groupno]['sms_basic_amount'] + $basicamount));
    		$real_amount = (!isset($stat_array[$storeno][$groupno]['sms_real_amount']) ? $realamount : ($stat_array[$storeno][$groupno]['sms_real_amount'] + $realamount));

    		$channelamount = (float)($success * (float)$channel_price_array[$priority]);
    		$channel_amount = (!isset($stat_array[$storeno][$groupno]['sms_channel_amount']) ? $channelamount : ($stat_array[$storeno][$groupno]['sms_channel_amount'] + $channelamount));

    		$stat_array[$storeno][$groupno]['sms_cnt'] = $success_cnt;
    		$stat_array[$storeno][$groupno]['sms_basic_amount'] = $basic_amount;
    		$stat_array[$storeno][$groupno]['sms_real_amount'] = $real_amount;
    		$stat_array[$storeno][$groupno]['sms_channel_amount'] = $channel_amount;
    	} else if ($productcode == 'LMS1' || $productcode == 'LMS2') {
    		$basicamount = (float)($success * (float)$cost_array[$storeno]['lms']);
    		$success_cnt = (!isset($stat_array[$storeno][$groupno]['lms_cnt']) ? $success : ($stat_array[$storeno][$groupno]['lms_cnt'] + $success));
    		$basic_amount = (!isset($stat_array[$storeno][$groupno]['lms_basic_amount']) ? $basicamount : ($stat_array[$storeno][$groupno]['lms_basic_amount'] + $basicamount));
    		$real_amount = (!isset($stat_array[$storeno][$groupno]['lms_real_amount']) ? $realamount : ($stat_array[$storeno][$groupno]['lms_real_amount'] + $realamount));

    		$channelamount = (float)($success * (float)$channel_price_array[$priority]);
    		$channel_amount = (!isset($stat_array[$storeno][$groupno]['lms_channel_amount']) ? $channelamount : ($stat_array[$storeno][$groupno]['lms_channel_amount'] + $channelamount));

    		$stat_array[$storeno][$groupno]['lms_cnt'] = $success_cnt;
    		$stat_array[$storeno][$groupno]['lms_basic_amount'] = $basic_amount;
    		$stat_array[$storeno][$groupno]['lms_real_amount'] = $real_amount;
    		$stat_array[$storeno][$groupno]['lms_channel_amount'] = $channel_amount;
    	} else if ($productcode == 'MMS1' || $productcode == 'MMS2') {
    		$basicamount = (float)($success * (float)$cost_array[$storeno]['mms']);
    		$success_cnt = (!isset($stat_array[$storeno][$groupno]['mms_cnt']) ? $success : ($stat_array[$storeno][$groupno]['mms_cnt'] + $success));
    		$basic_amount = (!isset($stat_array[$storeno][$groupno]['mms_basic_amount']) ? $basicamount : ($stat_array[$storeno][$groupno]['mms_basic_amount'] + $basicamount));
    		$real_amount = (!isset($stat_array[$storeno][$groupno]['mms_real_amount']) ? $realamount : ($stat_array[$storeno][$groupno]['mms_real_amount'] + $realamount));

    		$channelamount = (float)($success * (float)$channel_price_array[$priority]);
    		$channel_amount = (!isset($stat_array[$storeno][$groupno]['mms_channel_amount']) ? $channelamount : ($stat_array[$storeno][$groupno]['mms_channel_amount'] + $channelamount));

    		$stat_array[$storeno][$groupno]['mms_cnt'] = $success_cnt;
    		$stat_array[$storeno][$groupno]['mms_basic_amount'] = $basic_amount;
    		$stat_array[$storeno][$groupno]['mms_real_amount'] = $real_amount;
    		$stat_array[$storeno][$groupno]['mms_channel_amount'] = $channel_amount;
    	} else if ($productcode == 'KAT' || $productcode == 'KFT' || $productcode == 'KFTM') {
    		$basicamount = (float)($success * (float)$cost_array[$storeno]['kko']);
    		$success_cnt = (!isset($stat_array[$storeno][$groupno]['kko_cnt']) ? $success : ($stat_array[$storeno][$groupno]['kko_cnt'] + $success));
    		$basic_amount = (!isset($stat_array[$storeno][$groupno]['kko_basic_amount']) ? $basicamount : ($stat_array[$storeno][$groupno]['kko_basic_amount'] + $basicamount));
    		$real_amount = (!isset($stat_array[$storeno][$groupno]['kko_real_amount']) ? $realamount : ($stat_array[$storeno][$groupno]['kko_real_amount'] + $realamount));

    		$channelamount = (float)($success * (float)$channel_price_array[$priority]);
    		$channel_amount = (!isset($stat_array[$storeno][$groupno]['kko_channel_amount']) ? $channelamount : ($stat_array[$storeno][$groupno]['kko_channel_amount'] + $channelamount));

    		$stat_array[$storeno][$groupno]['kko_cnt'] = $success_cnt;
    		$stat_array[$storeno][$groupno]['kko_basic_amount'] = $basic_amount;
    		$stat_array[$storeno][$groupno]['kko_real_amount'] = $real_amount;
    		$stat_array[$storeno][$groupno]['kko_channel_amount'] = $channel_amount;
    	}
    }
}
mysqli_free_result($result);

$commit_flag = 0;
mysqli_autocommit($conn, FALSE);
foreach ($stat_array as $storeno => $sval) {
	if (!$storeno) continue;
	$sms_cnt = 0;
	$sms_basic_amount = 0;
	$sms_real_amount = 0;
	$sms_channel_amount = 0;
	$lms_cnt = 0;
	$lms_basic_amount = 0;
	$lms_real_amount = 0;
	$lms_channel_amount = 0;
	$mms_cnt = 0;
	$mms_basic_amount = 0;
	$mms_real_amount = 0;
	$mms_channel_amount = 0;
	$kko_cnt = 0;
	$kko_basic_amount = 0;
	$kko_real_amount = 0;
	$kko_channel_amount = 0;

	foreach ($sval as $groupno => $gval) {
		$sms_cnt = (isset($gval['sms_cnt']) ? (float)$gval['sms_cnt'] : 0);
		$sms_basic_amount = (isset($gval['sms_basic_amount']) ? (float)$gval['sms_basic_amount'] : 0);
		$sms_real_amount = (isset($gval['sms_real_amount']) ? (float)$gval['sms_real_amount'] : 0);
		$sms_channel_amount = (isset($gval['sms_channel_amount']) ? (float)$gval['sms_channel_amount'] : 0);

		$lms_cnt = (isset($gval['lms_cnt']) ? (float)$gval['lms_cnt'] : 0);
		$lms_basic_amount = (isset($gval['lms_basic_amount']) ? (float)$gval['lms_basic_amount'] : 0);
		$lms_real_amount = (isset($gval['lms_real_amount']) ? (float)$gval['lms_real_amount'] : 0);
		$lms_channel_amount = (isset($gval['lms_channel_amount']) ? (float)$gval['lms_channel_amount'] : 0);

		$mms_cnt = (isset($gval['mms_cnt']) ? (float)$gval['mms_cnt'] : 0);
		$mms_basic_amount = (isset($gval['mms_basic_amount']) ? (float)$gval['mms_basic_amount'] : 0);
		$mms_real_amount = (isset($gval['mms_real_amount']) ? (float)$gval['mms_real_amount'] : 0);
		$mms_channel_amount = (isset($gval['mms_channel_amount']) ? (float)$gval['mms_channel_amount'] : 0);

		$kko_cnt = (isset($gval['kko_cnt']) ? (float)$gval['kko_cnt'] : 0);
		$kko_basic_amount = (isset($gval['kko_basic_amount']) ? (float)$gval['kko_basic_amount'] : 0);
		$kko_real_amount = (isset($gval['kko_real_amount']) ? (float)$gval['kko_real_amount'] : 0);
		$kko_channel_amount = (isset($gval['kko_channel_amount']) ? (float)$gval['kko_channel_amount'] : 0);

		$sql2 = "
			INSERT INTO stat_sending_month
			SET yyyy = '{$yyyy}',
				mm = '{$mm}',
				yyyymm = '{$yyyymm}',
				storeno = '{$storeno}',
				groupno = '{$groupno}',
				sms_cnt = '{$sms_cnt}',
				sms_basic_amount = '{$sms_basic_amount}',
				sms_real_amount = '{$sms_real_amount}',
				sms_channel_amount = '{$sms_channel_amount}',
				lms_cnt = '{$lms_cnt}',
				lms_basic_amount = '{$lms_basic_amount}',
				lms_real_amount = '{$lms_real_amount}',
				lms_channel_amount = '{$lms_channel_amount}',
				mms_cnt = '{$mms_cnt}',
				mms_basic_amount = '{$mms_basic_amount}',
				mms_real_amount = '{$mms_real_amount}',
				mms_channel_amount = '{$mms_channel_amount}',
				kko_cnt = '{$kko_cnt}',
				kko_basic_amount = '{$kko_basic_amount}',
				kko_real_amount = '{$kko_real_amount}',
				kko_channel_amount = '{$kko_channel_amount}'
			ON DUPLICATE KEY
			UPDATE
				yyyy = '{$yyyy}',
				mm = '{$mm}',
				yyyymm = '{$yyyymm}',
				storeno = '{$storeno}',
				groupno = '{$groupno}',
				sms_cnt = '{$sms_cnt}',
				sms_basic_amount = '{$sms_basic_amount}',
				sms_real_amount = '{$sms_real_amount}',
				sms_channel_amount = '{$sms_channel_amount}',
				lms_cnt = '{$lms_cnt}',
				lms_basic_amount = '{$lms_basic_amount}',
				lms_real_amount = '{$lms_real_amount}',
				lms_channel_amount = '{$lms_channel_amount}',
				mms_cnt = '{$mms_cnt}',
				mms_basic_amount = '{$mms_basic_amount}',
				mms_real_amount = '{$mms_real_amount}',
				mms_channel_amount = '{$mms_channel_amount}',
				kko_cnt = '{$kko_cnt}',
				kko_basic_amount = '{$kko_basic_amount}',
				kko_real_amount = '{$kko_real_amount}',
				kko_channel_amount = '{$kko_channel_amount}'
		";
		if (!mysqli_query($conn, $sql2)) {
		    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
			mysqli_rollback($conn);
			mysqli_close($conn);
			exit;
		}
		$commit_flag = 1;
	}
}

if ($commit_flag) mysqli_commit($conn);
mysqli_close($conn);
// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
exit;
