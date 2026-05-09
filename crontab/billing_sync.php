<?php
exit;


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
error_log('['.$log_name.'][S]...['.$file_name.']', 0);


$sql = "
	SELECT xid,userno
	FROM billing
	WHERE groupno = '0'
";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
    	$xid = (int)$row['xid'];
    	$userno = (int)$row['userno'];

		$sql1 = "SELECT groupno FROM users WHERE userno='{$userno}'";
		$result1 = mysqli_query($conn, $sql1);
		$row1 = mysqli_fetch_assoc($result1);
		mysqli_free_result($result1);
		$groupno = (int)$row1['groupno'];
		if (!$groupno) continue;

		$sql2 = "
			UPDATE billing
			SET groupno = '{$groupno}'
			WHERE xid = '{$xid}' AND userno = '{$userno}'
		";
		if (!mysqli_query($conn, $sql2)) {
		    error_log('['.$log_name.'][E]...['.$sql2.']['.$file_name.']', 0);
		}
    }
}
mysqli_free_result($result);
mysqli_close($conn);
error_log('['.$log_name.'][F]...['.$file_name.']', 0);
