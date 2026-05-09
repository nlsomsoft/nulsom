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

$stat_array = array();
$cur_time = time();
$yyyy = (int)date('Y', strtotime('-1 day', $cur_time));
$mm = (int)date('m', strtotime('-1 day', $cur_time));
$dd = (int)date('d', strtotime('-1 day', $cur_time));
$yyyymmdd = sprintf('%04d%02d%02d',$yyyy,$mm,$dd);
$agent_name = 'agent_msgresult';
$db_name = ($database == 'unisms' ? 'sowcorea' : '');

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
	error_log('['.$log_name.'][E]...[CONNECT DB]['.$file_name.']', 0);
	exit;
}
// error_log('Connected successfully', 0);
error_log('['.$log_name.'][S]...['.$file_name.']', 0);

$sql = "
	SELECT sno
	FROM stat_agent_log
	WHERE yyyymmdd = '{$yyyymmdd}' AND agent = '{$agent_name}'
";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
mysqli_free_result($result);
if ($row['sno']) {
	error_log('['.$log_name.'][F]...['.$file_name.']', 0);
	exit;
}

$stat_array = array();
$sql = "
	SELECT COUNT(id) cnt, DATE(`registTime`) AS `yyyymmdd`, storeno, kind
	FROM {$agent_name}
	WHERE result = '0'
		AND DATE(registTime) = '{$yyyymmdd}'
	GROUP BY `yyyymmdd`, storeno, kind
";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
	$i = 0;
    while ($row = mysqli_fetch_assoc($result)) {
    	$stat_array[$i]['kind'] = (int)$row['kind'];
    	$stat_array[$i]['storeno'] = (int)$row['storeno'];
		$stat_array[$i]['cnt'] = (int)$row['cnt'];
		$stat_array[$i]['yyyymmdd'] = str_replace('-','',$row['yyyymmdd']);
		$i ++;
    }
}
mysqli_free_result($result);


$commit_flag = 0;
mysqli_autocommit($conn, FALSE);

foreach ($stat_array as $val) {
	if (!$val['storeno']) continue;
	$yyyymm = substr($val['yyyymmdd'], 0, 6);

	$sql  = '';
	if ($db_name != '') $sql .= "INSERT INTO {$db_name}.stat_agent_daily";
	else $sql .= "INSERT INTO stat_agent_daily";
	$sql .= "
		SET storeno = '{$val['storeno']}',
			groupno = '0',
			yyyymm = '{$yyyymm}',
			yyyymmdd = '{$val['yyyymmdd']}',
	";
	if ($val['kind'] == '1') $sql .= "kp_lms = '{$val['cnt']}' ";
	else if ($val['kind'] == '2') $sql .= "kp_mms = '{$val['cnt']}' ";
	else $sql .= "kp_sms = '{$val['cnt']}' ";
	$sql .= "
		ON DUPLICATE KEY
		UPDATE
	";
	if ($val['kind'] == '1') $sql .= "kp_lms = '{$val['cnt']}' ";
	else if ($val['kind'] == '2') $sql .= "kp_mms = '{$val['cnt']}' ";
	else $sql .= "kp_sms = '{$val['cnt']}' ";

	if (!mysqli_query($conn, $sql)) {
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}
	$commit_flag = 1;
}

if ($commit_flag) {
	$sql = "
		INSERT INTO stat_agent_log
		SET yyyymmdd = '{$yyyymmdd}',
			agent = '{$agent_name}'
	";
	if (!mysqli_query($conn, $sql)) {
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}
}
if ($commit_flag) mysqli_commit($conn);
mysqli_close($conn);
error_log('['.$log_name.'][F]...['.$file_name.']', 0);
exit;
