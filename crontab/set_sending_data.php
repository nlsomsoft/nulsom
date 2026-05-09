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
mysqli_set_charset($conn, 'utf8');
// error_log('['.$log_name.'][S]...['.$file_name.']', 0);

$datetime = time();
$agent = 'QUEUE';
$sql = "SELECT * FROM controller_queue WHERE agent='{$agent}'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
mysqli_free_result($result);
$gateway_val = (int)$row['gateway'];
$send_val = (int)$row['send'];
$cnt_val = (int)$row['cnt'];

if ($cnt_val > 5) {
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
// $sql = "
// 	SELECT *
// 	FROM sow_processunit_msg
// 	WHERE status = '0'
// 		AND total_units < 1000
// 		AND deleteflag = 'N'
// 		AND reserve_time >= DATE_ADD(NOW(), INTERVAL -1 HOUR)
// 		AND reserve_time < NOW()
// 		AND priority > '0'
// 	LIMIT 0, 50
// ";
$sql = "
	SELECT *
	FROM sow_processunit_msg
	WHERE status = '0'
		AND total_units < 1000
		AND deleteflag = 'N'
		AND reserve_time >= DATE_ADD(NOW(), INTERVAL -3 HOUR)
		AND reserve_time < NOW()
		AND priority > '0'
	LIMIT 0, 5
";
// error_log($sql, 0);
$commit_flag = 0;
mysqli_autocommit($conn, FALSE);

$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
		// $sql = "
		// 	DELETE
		// 	FROM sow_pu_msgdata
		// 	WHERE procid = '{$row['procid']}'
		// ";
		// if (!mysqli_query($conn, $sql)) {
		//     error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		//     continue;
		// }
		// $sql = "
		// 	UPDATE sow_processunit_msg
		// 	SET processed = '1'
		// 	WHERE procid = '{$row['procid']}'
		// ";
		// if (!mysqli_query($conn, $sql)) {
		//     error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		// }

		$priority = $row['priority'];
		$procid = $row['procid'];
		$storeno = $row['storeno'];
		$userno = $row['userno'];
		$reg_time = $row['reg_time'];
		$callback = $row['callback'];
		$file_cnt = $row['file_cnt'];
		$file_path_1 = $row['file_path_1'];
		$merge = $row['merge'];
		$nproid = (int)$row['nproid'];
		$subject = addslashes($row['subject']);
		$msg = addslashes($row['msg']);


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
		} else if ($priority < 500) {
			// $wdb->execute( "UPDATE sow_processunit_msg SET status = 10 WHERE procid = $procid" );
			// if( $wdb->getErrno() != 0 ) $wdb->disconnect();
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

			// $query = "INSERT INTO sow_send_data (kind, phone, callback, message, reserve_time, reg_time, procid, storeno, userno, dataid, destname, nproid)";
			// if( $merge == "N" ) $query .= " SELECT '0', targetno, '".$callback."', '".$msg."', reserve_time, NOW(), procid, '".$storeno."', '".$userno."', xid, targetname,".$nproid ;
			// else $query .= " SELECT '0', targetno, '".$callback."', msgbody, reserve_time, NOW(), procid, '".$storeno."', '".$userno."', xid, targetname,".$nproid ;
			// $query .= " FROM sow_pu_msgdata WHERE procid = $procid";
			// $wdb->execute( $query );
			// if( $wdb->getErrno() != 0 ) $wdb->disconnect();

			$sql = "
				INSERT INTO sow_send_data (
					kind,
					phone,
					callback,
					priority,
					message,
					reserve_time,
					reg_time,
					procid,
					storeno,
					userno,
					dataid,
					destname,
					nproid
				)
			";
			if ($merge == 'N') {
				// $sql .= " SELECT '0', targetno, '".$callback."', '".$msg."', reserve_time, NOW(), procid, '".$storeno."', '".$userno."', xid, targetname,".$nproid ;
				// $sql .= " FROM sow_pu_msgdata WHERE procid = $procid";
				$sql .= "
					SELECT
						'0',
						targetno,
						'{$callback}',
						'{$priority}',
						'{$msg}',
						reserve_time,
						NOW(),
						procid,
						'{$storeno}',
						'{$userno}',
						xid,
						targetname,
						'{$nproid}'
					FROM sow_pu_msgdata
					WHERE procid = '{$procid}'
				";
			} else {
				// $sql .= " SELECT '0', targetno, '".$callback."', msgbody, reserve_time, NOW(), procid, '".$storeno."', '".$userno."', xid, targetname,".$nproid ;
				// $sql .= " FROM sow_pu_msgdata WHERE procid = $procid";
				$sql .= "
					SELECT
						'0',
						targetno,
						'{$callback}',
						'{$priority}',
						msgbody,
						reserve_time,
						NOW(),
						procid,
						'{$storeno}',
						'{$userno}',
						xid,
						targetname,
						'{$nproid}'
					FROM sow_pu_msgdata
					WHERE procid = '{$procid}'
				";
			}
			if (!mysqli_query($conn, $sql)) {
			    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
				mysqli_rollback($conn);
				mysqli_close($conn);
				exit;
			}
		} else if ($priority < 900) {
			// $wdb->execute( "UPDATE sow_processunit_msg SET status = 10 WHERE procid = $procid" );
			// if( $wdb->getErrno() != 0 ) $wdb->disconnect();
			$sql = "
				UPDATE sow_processunit_msg
				SET status = '10'
				WHERE procid = '{$procid}'
			";
			if (!mysqli_query($conn, $sql)) {
			    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
				mysqli_rollback($conn);
				mysqli_close($conn);
				exit;
			}

			$sql = "
				INSERT INTO sow_send_data (
					kind,
					phone,
					callback,
					priority,
					subject,
					message,
					reserve_time,
					reg_time,
					procid,
					storeno,
					userno,
					dataid,
					destname,
					nproid
				)
			";
			if ($merge == 'N') {
				// $query .= " SELECT '1', targetno, '".$callback."', '" . $subject . "', '".$msg."', reserve_time, NOW(), procid, '".$storeno."', '".$userno."', xid, targetname,".$nproid ;
				// $query .= " FROM sow_pu_msgdata WHERE procid = $procid";
				// $wdb->execute( $query );
				// if( $wdb->getErrno() != 0 ) $wdb->disconnect();

				$sql .= "
					SELECT
						'1',
						targetno,
						'{$callback}',
						'{$priority}',
						'{$subject}',
						'{$msg}',
						reserve_time,
						NOW(),
						procid,
						'{$storeno}',
						'{$userno}',
						xid,
						targetname,
						'{$nproid}'
					FROM sow_pu_msgdata
					WHERE procid = '{$procid}'
				";
			} else {
				// $query .= " SELECT '1', targetno, '".$callback."', '" . $subject . "', msgbody, reserve_time, NOW(), procid, '".$storeno."', '".$userno."', xid, targetname,".$nproid ;
				// $query .= " FROM sow_pu_msgdata WHERE procid = $procid";
				// $wdb->execute( $query );
				// if( $wdb->getErrno() != 0 ) $wdb->disconnect();
				$sql .= "
					SELECT
						'1',
						targetno,
						'{$callback}',
						'{$priority}',
						'{$subject}',
						msgbody,
						reserve_time,
						NOW(),
						procid,
						'{$storeno}',
						'{$userno}',
						xid,
						targetname,
						'{$nproid}'
					FROM sow_pu_msgdata
					WHERE procid = '{$procid}'
				";
			}
			if (!mysqli_query($conn, $sql)) {
			    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
				mysqli_rollback($conn);
				mysqli_close($conn);
				exit;
			}
		} else if ($priority >= 900) {
			// $wdb->execute( "UPDATE sow_processunit_msg SET status = 10 WHERE procid = $procid" );
			// if( $wdb->getErrno() != 0 ) $wdb->disconnect();
			$sql = "
				UPDATE sow_processunit_msg
				SET status = '10'
				WHERE procid = '{$procid}'
			";
			if (!mysqli_query($conn, $sql)) {
			    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
				mysqli_rollback($conn);
				mysqli_close($conn);
				exit;
			}

			// $query = "INSERT INTO sow_send_data (kind, phone, callback, subject, message, file_cnt, file_path1, reserve_time, reg_time, procid, storeno, userno, dataid, destname, nproid)";
			$sql = "
				INSERT INTO sow_send_data (
					kind,
					phone,
					callback,
					priority,
					subject,
					message,
					file_cnt,
					file_path1,
					reserve_time,
					reg_time,
					procid,
					storeno,
					userno,
					dataid,
					destname,
					nproid
				)
			";

			if ($merge == 'N') {
				// $query .= " SELECT '2', targetno, '".$callback."', '" . $subject . "', '".$msg."', '".$file_cnt."', '".$file_path_1."', reserve_time, NOW(), procid, '".$storeno."', '".$userno."', xid, targetname,".$nproid ;
				// $query .= " FROM sow_pu_msgdata WHERE procid = $procid";
				// $wdb->execute( $query );
				// if( $wdb->getErrno() != 0 ) $wdb->disconnect();
				$sql .= "
					SELECT
						'2',
						targetno,
						'{$callback}',
						'{$priority}',
						'{$subject}',
						'{$msg}',
						'{$file_cnt}',
						'{$file_path_1}',
						reserve_time,
						NOW(),
						procid,
						'{$storeno}',
						'{$userno}',
						xid,
						targetname,
						'{$nproid}'
					FROM sow_pu_msgdata
					WHERE procid = '{$procid}'
				";
			} else {
				// $query .= " SELECT '2', targetno, '".$callback."', '" . $subject . "', msgbody, '".$file_cnt."', '".$file_path_1."', reserve_time, NOW(), procid, '".$storeno."', '".$userno."', xid, targetname,".$nproid ;
				// $query .= " FROM sow_pu_msgdata WHERE procid = $procid";
				// $wdb->execute( $query );
				// if( $wdb->getErrno() != 0 ) $wdb->disconnect();
				$sql .= "
					SELECT
						'2',
						targetno,
						'{$callback}',
						'{$priority}',
						'{$subject}',
						msgbody,
						'{$file_cnt}',
						'{$file_path_1}',
						reserve_time,
						NOW(),
						procid,
						'{$storeno}',
						'{$userno}',
						xid,
						targetname,
						'{$nproid}'
					FROM sow_pu_msgdata
					WHERE procid = '{$procid}'
				";
			}
			if (!mysqli_query($conn, $sql)) {
			    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
				mysqli_rollback($conn);
				mysqli_close($conn);
				exit;
			}
		}
		// $commit_flag = 1;
    }
	// if ($commit_flag) mysqli_commit($conn);
}

$sql  = "UPDATE controller_queue SET gateway = 1, cnt = 1 WHERE agent = '{$agent}'";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}

mysqli_commit($conn);
mysqli_free_result($result);
mysqli_close($conn);
// error_log('['.$log_name.'][F]...['.$file_name.']', 0);
exit;
