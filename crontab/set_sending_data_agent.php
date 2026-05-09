<?php
exit;


// define filename
$file_name = $_SERVER['PHP_SELF'];
$log_name = strtoupper($_SERVER['STORENAME']);
$log_flag = false;

//error_log(print_r($_SERVER,1),0);
if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
    error_log('['.$log_name.'][E]...[REMOTE_ADDR]['.$file_name.']', 0);
    exit;
}

define('BASEPATH', $_SERVER['DOCUMENT_ROOT']);
define('ENVIRONMENT', '');
include_once(BASEPATH.'/application/config/database.php');
// $servername = 'localhost';
$servername = $db['default']['hostname'];
$username	= $db['default']['username'];
$password	= $db['default']['password'];
$database	= $db['default']['database'];

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
	error_log('['.$log_name.'][E]...[CONNECT DB]['.$file_name.']', 0);
	exit;
}
mysqli_set_charset($conn, 'utf8');
// error_log('['.$log_name.'][S]...['.$file_name.']', 0);

$agent_table_name = array();
$agent_table_name[100] = 'SC_TRAN';		// LGT SMS
$agent_table_name[500] = 'LG_MMS_MSG';	// LGT LMS
$agent_table_name[900] = 'LG_MMS_MSG';	// LG MMS



$agent_name = array (
	'100' => 'LGT_SMS',
	'500' => 'LGT_LMS',
	'900' => 'LGT_MMS',
);


$datetime = time();
$agent = 'QUEUE_AGENT';
$sql = "SELECT * FROM controller_queue WHERE agent='{$agent}'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
mysqli_free_result($result);
$gateway_val = (int)$row['gateway'];
$send_val = (int)$row['send'];
$cnt_val = (int)$row['cnt'];

if ($cnt_val >= 3) {
	$sql  = "UPDATE controller_queue SET gateway = 1, cnt = 1 WHERE agent = '{$agent}'";
	if (!mysqli_query($conn, $sql)) {
		error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}

	$gdate = date('Y-m-d H:i:s',$gateway_val);
	error_log('['.$log_name.'][CONTROLLER_QUEUE:'.$agent.']...[DATE:'.$gdate.'][CNT:'.$cnt_val.']', 0);
	mysqli_close($conn);
	exit;
}
if ($gateway_val > 1) {
	$sql  = "UPDATE controller_queue SET cnt = cnt + 1 WHERE agent = '{$agent}'";
	if (!mysqli_query($conn, $sql)) {
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		// mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}

	$gdate = date('Y-m-d H:i:s',$gateway_val);
	error_log('['.$log_name.'][CONTROLLER_QUEUE:'.$agent.']...[DATE:'.$gdate.'][CNT:'.$cnt_val.']', 0);
	mysqli_close($conn);
	exit;
}

$sql  = "UPDATE controller_queue SET gateway = '{$datetime}' WHERE agent='{$agent}'";
// error_log($sql,0);
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	// mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}

$timestamp = time();

$sql = "
	SELECT *
	FROM sow_processunit_msg
	WHERE status = '0'
		AND deleteflag = 'N'
		AND reserve_time >= DATE_ADD(NOW(), INTERVAL -3 HOUR)
		AND reserve_time < NOW()
		AND priority > 0
	ORDER BY procid ASC
	LIMIT 1
";
// error_log($sql, 0);
$commit_flag = 0;
mysqli_autocommit($conn, FALSE);

$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
		$priority = $row['priority'];
		$procid = $row['procid'];
		$storeno = $row['storeno'];
		$userno = $row['userno'];
		$reg_time = $row['reg_time'];
		$callback = $row['callback'];
		$file_cnt = (int)$row['file_cnt'];
		$file_path_1 = $row['file_path_1'];
		$merge = $row['merge'];
		$total_units = $row['total_units'];
		$nproid = (int)$row['nproid'];
		$subject = addslashes($row['subject']);
		$msg = addslashes($row['msg']);

		if ($merge == 'N') $content = "'{$msg}'";
		else $content = 'msgbody';
		$key = $priority;

		if (!$priority) {
			error_log('['.$log_name.'][E]...[proiorty value]['.$file_name.']', 0);
			// $wdb->execute( "UPDATE sow_processunit_msg SET status = -10 WHERE procid = $procid" );
			// if( $wdb->getErrno() != 0 ) $wdb->disconnect();
			$sql = "
				UPDATE sow_processunit_msg
				SET status = '-10'
				WHERE procid = '{$procid}'
			";
			if (!mysqli_query($conn, $sql)) {
			    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
				mysqli_rollback($conn);
				mysqli_close($conn);
				exit;
			}
		}
		else {
error_log('['.$log_name.']['.$priority.']['.$agent_name[$priority].'] INSERT COUNT : '.$total_units, 0);

			$sql = "
				UPDATE sow_processunit_msg
				SET status = 10
				WHERE procid = '{$procid}'
			";
			if (!mysqli_query($conn, $sql)) {
			    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
				mysqli_rollback($conn);
				mysqli_close($conn);
				exit;
			}

			if (!$key) continue;


			else if ($key == 100) {
				$sql = '';
				$sql = "
					INSERT INTO {$agent_table_name[$key]}
					(
						TR_SENDDATE,
						TR_SENDSTAT,
						TR_MSGTYPE,
						TR_PHONE,
						TR_CALLBACK,
						TR_MSG,
						TR_ETC1,
						TR_ETC2,
						TR_ETC3,
						TR_ETC4
					)
					SELECT
						reserve_time,
						'0',
						'0',
						targetno,
						'{$callback}',
						REPLACE({$content}, '\r\n', '\n'),
						procid,
						targetname,
						'{$storeno}',
						'{$userno}'
					FROM sow_pu_msgdata
					WHERE procid = '{$procid}'
				";
				if (!mysqli_query($conn, $sql)) {
					mysqli_rollback($conn);
					error_process($conn, $procid);
					mysqli_close($conn);
				    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
					exit;
				}
			}
			else if ($key == 500 || $key == 900) {
				$sql = '';
				$sql = "
					INSERT INTO {$agent_table_name[$key]}
					(
						SUBJECT,
						PHONE,
						CALLBACK,
						STATUS,
						REQDATE,
						MSG,
						FILE_CNT,
						FILE_PATH1,
						TYPE,
						ETC1,
						ETC2,
						ETC3,
						ETC4
					)
					SELECT
						'{$subject}',
						targetno,
						'{$callback}',
						'0',
						reserve_time,
						REPLACE({$content}, '\r\n', '\n'),
						{$file_cnt},
						'{$file_path_1}',
						'0',
						procid,
						targetname,
						'{$storeno}',
						'{$userno}'
					FROM sow_pu_msgdata
					WHERE procid = '{$procid}'
				";
				if (!mysqli_query($conn, $sql)) {
					mysqli_rollback($conn);
					error_process($conn, $procid);
					mysqli_close($conn);
				    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
					exit;
				}
			}
		}
    }
}

$sql  = "UPDATE controller_queue SET gateway = 1, cnt = 1 WHERE agent = '{$agent}'";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	error_process($conn, $procid);
	mysqli_close($conn);
	exit;
}

mysqli_commit($conn);
mysqli_free_result($result);
mysqli_close($conn);
// error_log('['.$log_name.'][F]...['.$file_name.']', 0);


function error_process($conn, $procid) {
	global $log_name;
	global $file_name;

	$sql = "
		UPDATE sow_processunit_msg
		SET status = '-5'
		WHERE procid = '{$procid}'
	";
	if (!mysqli_query($conn, $sql)) {
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}
}
exit;
