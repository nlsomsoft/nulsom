<?php
// define filename
$file_name = $_SERVER['PHP_SELF'];
$log_name = 'SOWSHOT';

// error_log(print_r($_SERVER,1),0);
if ($_SERVER['REMOTE_ADDR'] != '211.174.61.140') {
    error_log('['.$log_name.'][E]...[REMOTE_ADDR]['.$file_name.']', 0);
    exit;
}

// $servername = 'localhost';
$servername = '127.0.0.1';
$username	= 'sowshot';
$password	= '@sowshot@';
$database	= 'lguplus1';

$cur_time = time();
$limit_count = 50000;
$table_name = 'MMS_MSG';
$backup_table_name = 'MMS_LOG_BACKUP';

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
	error_log('['.$log_name.'][E]...[CONNECT DB]['.$file_name.']', 0);
	exit;
}
// error_log('['.$log_name.'][S]...['.$file_name.']', 0);

$sql = "
	SELECT COUNT(*) cnt
	FROM {$table_name}
	WHERE STATUS = '5'
";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
mysqli_free_result($result);
if (!$row['cnt']) {
	mysqli_close($conn);
	// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
	exit;
}

// mysqli_autocommit($conn, FALSE);
$sql = "
	UPDATE {$table_name}
	SET STATUS = '6'
	WHERE STATUS = '5'
	LIMIT {$limit_count}
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	// mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}

$sql = "
	INSERT INTO {$backup_table_name}
	SELECT *
	FROM {$table_name}
	WHERE STATUS = '6'
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	// mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}

$sql = "
	DELETE
	FROM {$table_name}
	WHERE STATUS = '6'
";
// error_log($sql,0);
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	// mysqli_rollback($conn);
	mysqli_close($conn);
    exit;
}
// mysqli_commit($conn);
mysqli_close($conn);
// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
exit;
