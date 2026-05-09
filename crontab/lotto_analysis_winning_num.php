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


include_once(BASEPATH.'/application/helpers/lotto_helper.php');
$cur_divide = (int)lotto_current_divide_num();
$divide = (int)($cur_divide - 1);



$sql = "SELECT * FROM lotto_winning_num WHERE divide = '{$divide}' ORDER BY xid DESC LIMIT 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
mysqli_free_result($result);
$xid = (int)$row['xid'];
$num = trim($row['num']);
$add_date = trim($row['add_date']);

if (!$xid) {
	exit;
}
// error_log($num, 0);
// error_log($divide, 0);
// error_log($add_date, 0);

$tmp_array = explode('|', $num);
$winning_array = explode(',', $tmp_array[0]);
$bonus_array[] = (int)$tmp_array[1];

$sql = "
	SELECT *
	FROM lotto_recommend
	WHERE divide = '{$divide}'
		AND state = '0'
";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
	// $commit_flag = 0;
	// mysqli_autocommit($conn, FALSE);
    // output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
		$xid = (int)$row['xid'];
		$num = trim($row['num']);
// error_log($num, 0);
		$user_array = array();
		$user_array = explode(',', $num);
// error_log(print_r($user_array, 1), 0);

		$intersection = array_values(array_intersect($winning_array, $user_array));
// error_log(print_r($intersection, 1), 0);
		$winner_cnt = count($intersection);
		$grade = 0;
		if ($winner_cnt == 3) $grade = 5;
		else if ($winner_cnt == 4) $grade = 4;
		else if ($winner_cnt == 6) $grade = 1;
		else if ($winner_cnt == 5) {
			$intersection = array_values(array_intersect($user_array, $bonus_array));
			$bonus_cnt = count($intersection);
			if ($bonus_cnt) $grade = 2;
			else $grade = 3;
		}

		$sql = "
			UPDATE lotto_recommend
			SET grade = '{$grade}',
				state = '1'
			WHERE xid = '{$xid}'
		";
		if (!mysqli_query($conn, $sql)) {
		    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
			mysqli_rollback($conn);
			mysqli_close($conn);
			exit;
		}
	}
}

mysqli_free_result($result);
mysqli_close($conn);
exit;
