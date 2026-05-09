<?php
declare(strict_types=1);

// define filename
$file_name = $_SERVER['PHP_SELF'];
$log_name = $_SERVER['STORENAME'];

//error_log(print_r($_SERVER,1),0);
if ($_SERVER['REMOTE_ADDR'] != '175.197.168.101') {
    error_log('['.$log_name.'][E]...[REMOTE_ADDR]['.$file_name.']', 0);
    exit;
}

require __DIR__ . '/reco_common.php';
// error_log($_SERVER['HTTP_X_BROKER_TOKEN'], 0);

$config = rc_load_config();
rc_require_token($config);

$action = isset($_GET['action']) ? rc_string($_GET['action']) : '';
if ($action === '') {
rc_fail(400, 'missing_action');
}

$body = rc_get_json_body();
$tradingDate = isset($body['trading_date']) ? rc_string($body['trading_date']) : '';
$items = isset($body['items']) && is_array($body['items']) ? $body['items'] : array();

if (!rc_validate_date($tradingDate)) {
rc_fail(400, 'invalid_trading_date');
}

if (empty($items)) {
rc_fail(400, 'items_required');
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
if (!$conn) {
	error_log('['.$log_name.'][E]...[CONNECT DB]['.$file_name.']', 0);
	exit;
}
mysqli_set_charset($conn, 'utf8');
// error_log('Connected successfully', 0);
// error_log('['.$log_name.'][S]...agent manager', 0);


try {
if ($action === 'daytrade_recommendations') {
$accepted = array();
$rejected = array();

foreach ($items as $idx => $row) {
$ticker = isset($row['ticker']) ? rc_string($row['ticker']) : '';
$name = isset($row['name']) ? rc_string($row['name']) : '';
$finalScore = isset($row['final_score']) ? rc_nullable_float($row['final_score']) : null;
$recommendationSession = isset($row['recommendation_session']) ? rc_string($row['recommendation_session']) : null;
$reasons = isset($row['reasons']) ? rc_string($row['reasons']) : '';
$theme = isset($row['theme']) ? rc_string($row['theme']) : '';

if ($ticker === '' || $name === '' || $finalScore === null || $reasons == '' || $theme == '') {
$rejected[] = array(
'index' => $idx,
'ticker' => $ticker,
'recommendation_session' => $recommendationSession,
'reason' => 'invalid_item'
);
continue;
}

$accepted[] = array(
'ticker' => $ticker,
'name' => $name,
'reasons' => $reasons,
'theme' => $theme,
'final_score' => $finalScore,
'recommendation_session' => $recommendationSession,
);

/*
----------------------------------------------------------------
TODO: DB INSERT / UPDATE (신대표님 구현 영역)
----------------------------------------------------------------
여기서 trading_date + ticker 기준으로 upsert 하시면 됩니다.

저장 대상 예시:
- trading_date
- ticker
- name
- final_score
- created_at
- updated_at

의사 코드 예시:

1) trading_date + ticker 존재 여부 확인
2) 있으면 UPDATE
- name = ?
- final_score = ?
- updated_at = NOW()
3) 없으면 INSERT
- trading_date, ticker, name, final_score, created_at, updated_at

주의:
- name은 UTF-8 기준 저장
- final_score는 decimal/float 컬럼 권장
- bulk 처리 시 transaction 권장
*/
}

// error_log(print_r($accepted,1),0);
$yyyymmdd = date('Ymd');
foreach ($accepted as $row) {
	$ticker = trim($row['ticker']);
	$name = trim($row['name']);
	$reasons = trim($row['reasons']);
	$theme = trim($row['theme']);
	$final_score = $row['final_score'];
	$recommendation_session = trim($row['recommendation_session']);

	$sql  = "
		INSERT INTO kiwoom_trading 
		(
			ticker,
			name,
			reasons,
			theme,
			final_score,
			recommendation_session,
			yyyymmdd
		)
		VALUES 
		(
			'{$row['ticker']}',
			'{$row['name']}',
			'{$row['reasons']}',
			'{$row['theme']}',
			'{$row['final_score']}',
			'{$row['recommendation_session']}',
			'{$yyyymmdd}'
		) ON DUPLICATE KEY UPDATE recommendation_session = '3'
	";
	if (!mysqli_query($conn, $sql)) {
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}
}

// if ($commit_flag) mysqli_commit($conn);
mysqli_commit($conn);
mysqli_close($conn);

rc_json_response(array(
'ok' => true,
'action' => $action,
'trading_date' => $tradingDate,
'accepted_count' => count($accepted),
'rejected_count' => count($rejected),
'accepted' => $accepted,
'rejected' => $rejected
), 200);
}

if ($action === 'daytrade_gap_update') {
$accepted = array();
$rejected = array();

foreach ($items as $idx => $row) {
$ticker = isset($row['ticker']) ? rc_string($row['ticker']) : '';
$expectedGapPct = isset($row['expected_gap_pct']) ? rc_nullable_float($row['expected_gap_pct']) : null;

if ($ticker === '' || $expectedGapPct === null) {
$rejected[] = array(
'index' => $idx,
'ticker' => $ticker,
'reason' => 'invalid_item'
);
continue;
}

$accepted[] = array(
'ticker' => $ticker,
'expected_gap_pct' => $expectedGapPct
);

/*
----------------------------------------------------------------
TODO: DB UPDATE / UPSERT (신대표님 구현 영역)
----------------------------------------------------------------
여기서 trading_date + ticker 기준으로 expected_gap_pct를 업데이트하시면 됩니다.

저장 대상 예시:
- trading_date
- ticker
- expected_gap_pct
- updated_at

의사 코드 예시:

1) trading_date + ticker 로 기존 row 조회
2) 있으면 UPDATE
- expected_gap_pct = ?
- updated_at = NOW()
3) 없으면 INSERT 또는 skip
- 운영정책에 따라 결정

추천:
- 08:00 추천목록이 먼저 들어오고
- 08:50 gap update는 기존 row update만 수행하는 방식이 가장 깔끔
- 다만 누락 복구까지 원하시면 upsert로 처리
*/
}

// error_log(print_r($accepted,1),0);
$yyyymmdd = date('Ymd');
foreach ($accepted as $row) {
	$ticker = trim($row['ticker']);
	$expected_gap_pct = $row['expected_gap_pct'];

	$sql  = "
		UPDATE kiwoom_trading
		SET expected_gap_pct = '{$row['expected_gap_pct']}'
		WHERE ticker = '{$row['ticker']}' AND yyyymmdd = '{$yyyymmdd}'
	";
	if (!mysqli_query($conn, $sql)) {
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}
}

// if ($commit_flag) mysqli_commit($conn);
mysqli_commit($conn);
mysqli_close($conn);

rc_json_response(array(
'ok' => true,
'action' => $action,
'trading_date' => $tradingDate,
'accepted_count' => count($accepted),
'rejected_count' => count($rejected),
'accepted' => $accepted,
'rejected' => $rejected
), 200);
}

if ($action === 'market_signal_lights') {
$accepted = array();
$rejected = array();

foreach ($items as $idx => $row) {
$signalLight = isset($row['signal_light']) ? rc_string($row['signal_light']) : '';
$totalScore = isset($row['total_score']) ? rc_nullable_float($row['total_score']) : null;
$ewyScore = isset($row['ewy_score']) ? rc_nullable_float($row['ewy_score']) : null;
$soxScore = isset($row['sox_score']) ? rc_nullable_float($row['sox_score']) : null;
$nasdaqScore = isset($row['nasdaq_score']) ? rc_nullable_float($row['nasdaq_score']) : null;
$wtiScore = isset($row['wti_score']) ? rc_nullable_float($row['wti_score']) : null;
$usdkrwScore = isset($row['usdkrw_score']) ? rc_nullable_float($row['usdkrw_score']) : null;
$us10yScore = isset($row['us10y_score']) ? rc_nullable_float($row['us10y_score']) : null;
$geoRiskScore = isset($row['geo_risk_score']) ? rc_nullable_float($row['geo_risk_score']) : null;

if ($signalLight === '' || $totalScore === null) {
$rejected[] = array(
'index' => $idx,
'reason' => 'invalid_item'
);
continue;
}

$accepted[] = array(
'signal_light' => $signalLight,
'total_score' => $totalScore,
'ewy_score' => $ewyScore,
'sox_score' => $soxScore,
'nasdaq_score' => $nasdaqScore,
'wti_score' => $wtiScore,
'usdkrw_score' => $usdkrwScore,
'us10y_score' => $us10yScore,
'geo_risk_score' => $geoRiskScore
);
}

$yyyymmdd = date('Ymd');
foreach ($accepted as $row) {
	$sql  = "
		INSERT INTO kiwoom_manager 
		(
			yyyymmdd,
			signal_light,
			total_score,
			ewy_score,
			sox_score,
			nasdaq_score,
			wti_score,
			usdkrw_score,
			us10y_score,
			geo_risk_score
		)
		VALUES 
		(
			'{$yyyymmdd}',
			'{$row['signal_light']}',
			'{$row['total_score']}',
			'{$row['ewy_score']}',
			'{$row['sox_score']}',
			'{$row['nasdaq_score']}',
			'{$row['wti_score']}',
			'{$row['usdkrw_score']}',
			'{$row['us10y_score']}',
			'{$row['geo_risk_score']}'
		) ON DUPLICATE KEY UPDATE 
			signal_light = '{$row['signal_light']}',
			total_score = '{$row['total_score']}',
			ewy_score = '{$row['ewy_score']}',
			sox_score = '{$row['sox_score']}',
			nasdaq_score = '{$row['nasdaq_score']}',
			wti_score = '{$row['wti_score']}',
			usdkrw_score = '{$row['usdkrw_score']}',
			us10y_score = '{$row['us10y_score']}',
			geo_risk_score = '{$row['geo_risk_score']}'
	";
	if (!mysqli_query($conn, $sql)) {
	    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
		mysqli_rollback($conn);
		mysqli_close($conn);
		exit;
	}
}

mysqli_commit($conn);
mysqli_close($conn);

rc_json_response(array(
'ok' => true,
'action' => $action,
'trading_date' => $tradingDate,
'accepted_count' => count($accepted),
'rejected_count' => count($rejected),
'accepted' => $accepted,
'rejected' => $rejected
), 200);
}


rc_fail(400, 'unknown_action');
} catch (Throwable $e) {
rc_fail(500, 'server_error', array(
'detail' => $e->getMessage()
));
}
