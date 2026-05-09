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

$sql = "
	SELECT gno,userno
	FROM address_group
	WHERE state = '1'
		AND remove_date < DATE_ADD(NOW(), interval - 2 day)
	LIMIT 0, 1
";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
    	$n = (int)((int)$row['userno'] % 10);
		$sql = "
			DELETE
			FROM address_{$n}
			WHERE gno = '{$row['gno']}'
		";
		if (!mysqli_query($conn, $sql)) {
		    error_log('[UNI][E]...['.$sql.']['.$file_name.']', 0);
		    continue;
		}
		$sql = "
			DELETE
			FROM address_group
			WHERE gno = '{$row['gno']}'
		";
		if (!mysqli_query($conn, $sql)) {
		    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		}
    }
}
mysqli_free_result($result);
mysqli_close($conn);
// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
exit;
