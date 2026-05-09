<?php
// define filename
$file_name = $_SERVER['PHP_SELF'];
$log_name = $_SERVER['STORENAME'];

//error_log(print_r($_SERVER,1),0);
if ($_SERVER['REMOTE_ADDR'] != '203.240.244.32' && $_SERVER['REMOTE_ADDR'] != '203.240.244.33' && $_SERVER['REMOTE_ADDR'] != '203.240.244.34') {
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

$cur_time = time();
$table_name = 'phone_080';

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
	error_log('['.$log_name.'][E]...[CONNECT DB]['.$file_name.']', 0);
	exit;
}
// error_log('['.$log_name.'][S]...['.$file_name.']', 0);
// error_log(print_r($_POST,1),0);

$tid = $_POST['T_ID'];
$ttime = $_POST['T_TIME'];
$menu_name = $_POST['MENU_NAME'];
$num080 = substr($menu_name, 0, 10);
$num080 = str_replace ('-', '', $num080);
$ani = $_POST['ANI'];
$dtmf_cnt = $_POST['DTMF_CNT'];
// $dtmf_1 = $_POST['DTMF_1'];

// $sql = "
// 	SELECT xid, state
// 	FROM {$table_name}
// 	WHERE mobile = '{$ani}' AND phone_080 = '{$num080}'
// 	ORDER BY xid DESC
// ";
// // error_log($sql, 0);
// $result = mysqli_query($conn, $sql);
// $row = mysqli_fetch_assoc($result);
// mysqli_free_result($result);
// $xid = (int)$row['xid'];
// $state = (int)$row['state']; // state : 0(등록) 2(삭제)

// // 존재하지 않거나, 삭제되었을 경우 등록
// if (!$xid || $state == 2) {
	$sql  = "INSERT INTO {$table_name} (mobile, phone_080, reg_time, remove_date) VALUES ('{$ani}', '{$num080}', NOW(), '0000-00-00 00:00:00') ";
	if (!mysqli_query($conn, $sql)) {
		error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		mysqli_close($conn);
		exit;
	}
// }
mysqli_close($conn);


echo "
	<html>
	<body>
	<form name='frm' method='post'>
	<input type='hidden' name='T_ID' value='{$tid}'>
	<input type='hidden' name='T_TIME' value='{$ttime}'>
	<input type='hidden' name='RESULT' value='0'>
	<input type='hidden' name='MENU_NAME' value='{$menu_name}'>
	<input type='hidden' name='ACTION_TYPE' value='3'>
	<input type='hidden' name='NEXT_MENU' value=''>
	<input type='hidden' name='MENT_FLAG' value='0'>
	<input type='hidden' name='MENT_CNT' value='1'>
	<input type='hidden' name='MENT_1' value='F_succ'>
	</form>
	</body>
	</html>
";
exit;