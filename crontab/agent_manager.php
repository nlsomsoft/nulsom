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

$restrictive_store_list = '';
$available_capacity_agent = array();
$limit_capacity_agent = array();

/*************************************************************************
- NPRO 등록 시 장문(LMS)의 경우 restrict_merge 테이블에 priority 값을 등록
- NPRO는 구조 상 merge 서비스를 이용할 수 없다.
*************************************************************************/
$agent_table_name[100] = 'SC_TRAN';		// LG SMS
// $agent_table_name[110] = 'sowshot_que';		// SOW SMS
$agent_table_name[500] = 'LG_MMS_MSG';	// LG LMS
// $agent_table_name[510] = 'sowshot_que';	// SOW LMS
$agent_table_name[900] = 'LG_MMS_MSG';	// LG MMS
// $agent_table_name[910] = 'sowshot_que';	// SOW MMS

$limit_capacity_agent = array();
$limit_capacity_agent[0] = 50000;

$select_time_gap = '3 HOUR';
$agent_name = array (
	'100' => 'LGT_SMS',
	'110' => 'SOW_SMS',
	'500' => 'LGT_LMS',
	'510' => 'SOW_LMS',
	'900' => 'LGT_MMS',
	'910' => 'SOW_MMS',
);
// kind : 0(SMS), 1(LMS), 2(MMS)
// define agent name and limit count

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
if (!$conn) {
	error_log('['.$log_name.'][E]...[CONNECT DB]['.$file_name.']', 0);
	exit;
}
// error_log('Connected successfully', 0);
// error_log('['.$log_name.'][S]...agent manager', 0);

$datetime = time();
$sql = 'SELECT * FROM controller';
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
mysqli_free_result($result);
$gateway_val = (int)$row['gateway'];
$send_val = (int)$row['send'];
$cnt_val = (int)$row['cnt'];

if ($send_val > 1) {
	$sdate = date('Y-m-d H:i:s',$send_val);
	error_log('['.$log_name.'][CONTROLLER.SEND]...[DATE:'.$sdate.']', 0);
	mysqli_close($conn);
	exit;
}
if ($cnt_val > 5) {
	$sql  = 'UPDATE controller SET gateway = 1, cnt = 1';
	if (!mysqli_query($conn, $sql)) {
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		// mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}

	$gdate = date('Y-m-d H:i:s',$gateway_val);
	error_log('['.$log_name.'][CONTROLLER.GATEWAY]...[DATE:'.$gdate.'][CNT:'.$cnt_val.']', 0);
	mysqli_close($conn);
	exit;
}
if ($gateway_val > 1) {
	$sql  = "UPDATE controller SET cnt = cnt + 1";
	if (!mysqli_query($conn, $sql)) {
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		// mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}

	$gdate = date('Y-m-d H:i:s',$gateway_val);
	error_log('['.$log_name.'][CONTROLLER.GATEWAY]...[DATE:'.$gdate.'][CNT:'.$cnt_val.']', 0);
	mysqli_close($conn);
	exit;
}

$sql  = "UPDATE controller SET gateway = '{$datetime}'";
// error_log($sql,0);
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	// mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}

// $restrictive_store_list = get_restrictive_store_list($conn,$database);
$restrictive_store_list = '';
$available_capacity_agent = get_available_capacity($conn, $agent_table_name, $limit_capacity_agent, $select_time_gap);

// error_log(print_r($available_capacity_agent,1), 0);
// if ($restrictive_store_list) error_log('['.$log_name.'][L]... '.$restrictive_store_list, 0);

$agent_pool = array();
$sql = "
	SELECT DISTINCT(priority)
	FROM sow_send_data
	WHERE state = '0'
";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
		$priority = (int)$row['priority'];
		$agent_pool[$priority] = 1;
	}
}
error_log(print_r($agent_pool,1), 0);

$add_cnt = 1;
$commit_flag = 0;
mysqli_autocommit($conn, FALSE);

foreach ($available_capacity_agent as $key => $val) {
	if ((int)$val < 1) continue;

	$insert_limit_count = 1000;
	$max_limit_cnt = (int)($val > $insert_limit_count ? $insert_limit_count : $val);
	$state = time() + $add_cnt ++;

	// if ($key == 111) $priority = 110;
	// else if ($key == 121) $priority = 120;
	// else $priority = $key;
	$priority = $key;

	$sql_where  = '';
	$sql_where .= "
		WHERE state = '0' AND priority = '{$priority}' AND reserve_time < NOW()
	";
	// if ($restrictive_store_list != '') $sql_where .= $restrictive_store_list;

	$row_cnt = 0;
	if (isset($agent_pool[$priority])) {
		$row_cnt = $agent_pool[$priority];
	}
	if (!$row_cnt) {
		error_log('['.$log_name.']['.$key.']['.$agent_name[$key].'] INSERT COUNT : '.$row_cnt, 0);
		continue;
	}


	// $limit_cnt = ($row_cnt > $max_limit_cnt ? $max_limit_cnt : $row_cnt);
	$limit_cnt = 1000;

	$sql  = '';
	$sql .= "
		UPDATE sow_send_data
		SET state = '{$state}'
		{$sql_where}
		ORDER BY id ASC
		LIMIT {$limit_cnt}
	";
	if (!mysqli_query($conn, $sql)) {
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}
	$row_cnt = 0;
	$row_cnt = mysqli_affected_rows($conn);
error_log('['.$log_name.']['.$key.']['.$agent_name[$key].'] INSERT COUNT : '.$row_cnt, 0);
	if (!$row_cnt) continue;

	if ($key == 100) {
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
				NOW(),
				'0',
				'0',
				phone,
				callback,
				message,
				procid,
				destname,
				storeno,
				userno
			FROM sow_send_data
			WHERE state = '{$state}'
		";
		if (!mysqli_query($conn, $sql)) {
			mysqli_rollback($conn);
			mysqli_close($conn);
		    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
			exit;
		}
	}
	else if ($key == 110 || $key == 510) {
		$sql = '';
		$sql = "
			INSERT INTO {$agent_table_name[$key]}
			(
				kind, 
				callbackNo, 
				receiveNo, 
				subject, 
				message, 
				reqdate, 
				registTime, 
				etc1, 
				etc2, 
				etc3, 
				etc4, 
				etc5
			)
			SELECT
				kind,
				callback,
				phone,
				subject,
				message,
				NOW(),
				NOW(),
				procid,
				storeno,
				userno,
				'N',
				destname
			FROM sow_send_data
			WHERE state = '{$state}'
		";
		if (!mysqli_query($conn, $sql)) {
			mysqli_rollback($conn);
			mysqli_close($conn);
		    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
			exit;
		}

	}
	else if ($key == 500) {
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
				subject,
				phone,
				callback,
				'0',
				NOW(),
				message,
				file_cnt,
				file_path1,
				'0',
				procid,
				destname,
				storeno,
				userno
			FROM sow_send_data
			WHERE state = '{$state}'
		";
		if (!mysqli_query($conn, $sql)) {
			mysqli_rollback($conn);
			mysqli_close($conn);
		    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
			exit;
		}
	}
	else if ($key == 900) {
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
				subject,
				phone,
				callback,
				'0',
				NOW(),
				message,
				file_cnt,
				file_path1,
				'0',
				procid,
				destname,
				storeno,
				userno
			FROM sow_send_data
			WHERE state = '{$state}'
		";
		if (!mysqli_query($conn, $sql)) {
			mysqli_rollback($conn);
			mysqli_close($conn);
		    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
			exit;
		}
	}
	else if ($key == 910) {
		$sql = '';
		$sql = "
			INSERT INTO {$agent_table_name[$key]}
			(
				kind, 
				callbackNo, 
				receiveNo, 
				subject, 
				message, 
				file_cnt,
				file_path1,
				reqdate, 
				registTime, 
				etc1, 
				etc2, 
				etc3, 
				etc4, 
				etc5
			)
			SELECT
				kind,
				callback,
				phone,
				subject,
				message,
				'1',
				file_path1,
				NOW(),
				NOW(),
				procid,
				storeno,
				userno,
				'N',
				destname
			FROM sow_send_data
			WHERE state = '{$state}'
		";
		if (!mysqli_query($conn, $sql)) {
			mysqli_rollback($conn);
			mysqli_close($conn);
		    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
			exit;
		}

	}

	$sql = '';
	$sql = "
		UPDATE sow_send_data
		SET state = '{$key}'
		WHERE state = '{$state}'
	";
	if (!mysqli_query($conn, $sql)) {
		mysqli_rollback($conn);
		mysqli_close($conn);
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		exit;
	}
	// $commit_flag = 1;
}

$sql  = 'UPDATE controller SET gateway = 1, cnt = 1';
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}
// if ($commit_flag) mysqli_commit($conn);
mysqli_commit($conn);
mysqli_close($conn);
// error_log('['.$log_name.'][F]...agent manager', 0);

function get_restrictive_store_list($conn,$database) {
	// $database_name = ($database == 'sowcorea' ? 'unisms' : 'sowcorea');
	$restrictive_store_list = '';
	$store_list = '';
	// $store_list = '8'; //munjaw
	$sql = "
		SELECT a.storeno
		FROM store_balance a
		LEFT JOIN store b
		ON a.storeno = b.storeno
		WHERE a.balance < 0 AND b.restrict_sending = '1'
	";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
	    // output data of each row
	    while ($row = mysqli_fetch_assoc($result)) {
	    	if ($store_list != '') $store_list .= ',';
	    	$store_list .= $row['storeno'];
	    }
	}
	mysqli_free_result($result);
/*
	$sql = "
		SELECT a.storeno
		FROM {$database_name}.store_balance a
		LEFT JOIN {$database_name}.store b
		ON a.storeno = b.storeno
		WHERE a.balance < 0 AND b.restrict_sending = '1'
	";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
	    // output data of each row
	    while ($row = mysqli_fetch_assoc($result)) {
	    	if ($store_list != '') $store_list .= ',';
	    	$store_list .= $row['storeno'];
	    }
	}
	mysqli_free_result($result);
*/
	if ($store_list != '') $restrictive_store_list = " AND storeno NOT IN ({$store_list}) ";
	return $restrictive_store_list;
}
function get_available_capacity($conn, $agent_table_name, $limit_capacity_agent, $select_time_gap) {
	$agent_count = false;
	$result_array = array();
	foreach ($agent_table_name as $key => $t_name) {
		if ($agent_count == false) {
			$result_array[$key] = 50000;
			continue;
		}

		$limit_capacity_agent_count = (isset($limit_capacity_agent[$key]) ? $limit_capacity_agent[$key] : $limit_capacity_agent[0]);

		if ($key == 100) {
			$sql = "
				SELECT COUNT(*) cnt
				FROM {$t_name}
				WHERE TR_SENDDATE >= DATE_ADD(NOW(), INTERVAL - {$select_time_gap})
			";
			$result = mysqli_query($conn, $sql);
			$row = mysqli_fetch_assoc($result);
			mysqli_free_result($result);
			$result_array[$key] = $limit_capacity_agent_count - (int)$row['cnt'];
		}
		else if ($key == 510) {
			$sql = "
				SELECT COUNT(*) cnt
				FROM {$t_name}
				WHERE REQDATE >= DATE_ADD(NOW(), INTERVAL - {$select_time_gap})
			";
			$result = mysqli_query($conn, $sql);
			$row = mysqli_fetch_assoc($result);
			mysqli_free_result($result);
			$result_array[$key] = $limit_capacity_agent_count - (int)$row['cnt'];
		}
		else if ($key == 900) {
			$sql = "
				SELECT COUNT(*) cnt
				FROM {$t_name}
				WHERE REQDATE >= DATE_ADD(NOW(), INTERVAL - {$select_time_gap})
			";
			$result = mysqli_query($conn, $sql);
			$row = mysqli_fetch_assoc($result);
			mysqli_free_result($result);
			$result_array[$key] = $limit_capacity_agent_count - (int)$row['cnt'];
		}
	}
	return $result_array;
}
exit;
