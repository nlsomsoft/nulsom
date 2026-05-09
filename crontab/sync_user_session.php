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

$sql = 'SELECT * FROM session_users';
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
	$redis = new Redis();
	try {
	    $redis->connect('127.0.0.1','6379', 2.5, NULL, 150);
	} catch(RedisException $e) {
		error_log('['.$log_name.'][E]...[CONNECT REDIS]['.$file_name.']', 0);
		exit;
	}

    // output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
    	$storeno = (int)$row['storeno'];
    	$userid = trim($row['userid']);

        //session 정보 초기화
        $cache_key = 'session_'.$storeno.'_'.$userid;
	    $value = '1';
	    $ttl = 3600;
		$redis->setex($cache_key, $ttl, $value);

		$sql = "
			DELETE
			FROM session_users
			WHERE xid = '{$row['xid']}'
		";
		if (!mysqli_query($conn, $sql)) {
		    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		}
    }
	$redis->close();
}
mysqli_free_result($result);
mysqli_close($conn);
// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
exit;
