<?php
declare(strict_types=1);

require __DIR__ . '/kiwoom_common.php';

$configPath = '/data/sow/private/kiwoom_secrets.php';
if (!is_file($configPath)) {
fail_response(500, 'missing_secret_config');
}

$config = require $configPath;
if (!is_array($config)) {
fail_response(500, 'invalid_secret_config');
}

require_broker_token($config);
load_kiwoom_config($config);

try {
$tokenRes = issue_token();
$token = isset($tokenRes['token']) ? (string)$tokenRes['token'] : '';
if ($token === '') {
fail_response(500, 'token_missing');
}

$action = isset($_GET['action']) ? trim((string)$_GET['action']) : '';

if ($action === 'account_info') {
$res = http_json(
'POST',
kiwoom_base_url() . '/api/dostk/acnt',
auth_headers('ka00001', $token),
array()
);
assert_kiwoom_ok($res, 'account_info');

json_response(array(
'ok' => true,
'account_masked' => mask_account(isset($res['acctNo']) ? $res['acctNo'] : null),
'return_code' => isset($res['return_code']) ? $res['return_code'] : 0,
'return_msg' => isset($res['return_msg']) ? $res['return_msg'] : ''
), 200);
}

if ($action === 'preopen_quote' || $action === 'quote') {
$ticker = isset($_GET['ticker']) ? trim((string)$_GET['ticker']) : '';
$market = isset($_GET['market']) ? trim((string)$_GET['market']) : 'KRX';

if ($ticker === '') {
fail_response(400, 'missing_ticker');
}

$stkCd = normalize_ticker($ticker, $market);

$res = http_json(
'POST',
kiwoom_base_url() . '/api/dostk/stkinfo',
auth_headers('ka10001', $token),
array(
'stk_cd' => $stkCd
)
);
assert_kiwoom_ok($res, 'stock_info');

// 가격 계열은 절대값 처리
$expectedPrice = to_price_abs(isset($res['exp_cntr_pric']) ? $res['exp_cntr_pric'] : null);
$currentPrice = to_price_abs(isset($res['cur_prc']) ? $res['cur_prc'] : null);
$prevClose = to_price_abs(isset($res['base_pric']) ? $res['base_pric'] : null);
$expectedQty = to_price_abs(isset($res['exp_cntr_qty']) ? $res['exp_cntr_qty'] : null);

// 등락률/전일대비는 signed 유지
$changePct = to_float_signed(isset($res['flu_rt']) ? $res['flu_rt'] : null);
$changeValue = to_float_signed(isset($res['pred_pre']) ? $res['pred_pre'] : null);

if ($expectedPrice !== null && $expectedPrice == 0.0) {
$expectedPrice = null;
}
if ($currentPrice !== null && $currentPrice == 0.0) {
$currentPrice = null;
}

$effectivePrice = $expectedPrice !== null ? $expectedPrice : $currentPrice;

if ($changePct === null && $effectivePrice !== null && $prevClose !== null && $prevClose > 0) {
$changePct = (($effectivePrice - $prevClose) / $prevClose) * 100.0;
}

json_response(array(
'ok' => true,
'ticker' => $stkCd,
'market' => strtoupper($market),
'quote' => array(
'expected_price' => $expectedPrice,
'current_price' => $currentPrice,
'effective_price' => $effectivePrice,
'prev_close' => $prevClose,
'expected_change_pct' => $changePct,
'change_value' => $changeValue,
'expected_contract_qty' => $expectedQty
),
'return_code' => isset($res['return_code']) ? $res['return_code'] : 0,
'return_msg' => isset($res['return_msg']) ? $res['return_msg'] : ''
), 200);
}


if ($action === 'intraday_ohlc') {
$ticker = isset($_GET['ticker']) ? trim((string)$_GET['ticker']) : '';
$market = isset($_GET['market']) ? trim((string)$_GET['market']) : 'KRX';

if ($ticker === '') {
fail_response(400, 'missing_ticker');
}

$stkCd = normalize_ticker($ticker, $market);

/*
키움 API 호출 부분
- 여기서는 당일 시세(시가/고가/저가/현재가/전일종가)가 포함된 API를 호출해야 합니다.
- 현재 신대표님 환경에서 이미 사용 중인 주식 현재가/기본시세 계열 API를 그대로 쓰시면 됩니다.
*/
$res = http_json(
'POST',
kiwoom_base_url() . '/api/dostk/stkinfo',
auth_headers('ka10001', $token),
array(
'stk_cd' => $stkCd
)
);
assert_kiwoom_ok($res, 'intraday_ohlc');

/*
아래 키들은 실제 키움 응답 필드명에 맞게 매핑해야 합니다.
지금 preopen_quote에서 쓰는 값과 동일한 패턴으로 절대값 처리만 일관되게 적용합니다.
*/
$openPrice = to_price_abs(isset($res['open_pric']) ? $res['open_pric'] : null);
$highPrice = to_price_abs(isset($res['high_pric']) ? $res['high_pric'] : null);
$lowPrice = to_price_abs(isset($res['low_pric']) ? $res['low_pric'] : null);
$currentPrice = to_price_abs(isset($res['cur_prc']) ? $res['cur_prc'] : null);
$prevClose = to_price_abs(isset($res['base_pric']) ? $res['base_pric'] : null);

$changePct = to_float_signed(isset($res['flu_rt']) ? $res['flu_rt'] : null);

if ($changePct === null && $prevClose !== null && $prevClose > 0 && $currentPrice !== null) {
$changePct = (($currentPrice - $prevClose) / $prevClose) * 100.0;
}

json_response(array(
'ok' => true,
'ticker' => $stkCd,
'market' => strtoupper($market),
'quote' => array(
'open_price' => $openPrice,
'high_price' => $highPrice,
'low_price' => $lowPrice,
'close_price' => $currentPrice,
'current_price' => $currentPrice,
'prev_close' => $prevClose,
'change_pct' => $changePct
),
'return_code' => isset($res['return_code']) ? $res['return_code'] : 0,
'return_msg' => isset($res['return_msg']) ? $res['return_msg'] : ''
), 200);
}


if ($action === 'daily_ohlcv') {
    $ticker = isset($_GET['ticker']) ? trim((string)$_GET['ticker']) : '';
    $market = isset($_GET['market']) ? trim((string)$_GET['market']) : 'KRX';
    $date = isset($_GET['date']) ? trim((string)$_GET['date']) : date('Ymd');

    if ($ticker === '') {
        fail_response(400, 'missing_ticker');
    }

    $stkCd = normalize_ticker($ticker, $market);
    $chart = get_daily_ohlcv_chart($token, $stkCd, $market, $date);

    $openPrice = to_price_abs(isset($chart['open_price']) ? $chart['open_price'] : null);
    $highPrice = to_price_abs(isset($chart['high_price']) ? $chart['high_price'] : null);
    $lowPrice = to_price_abs(isset($chart['low_price']) ? $chart['low_price'] : null);
    $closePrice = to_price_abs(isset($chart['close_price']) ? $chart['close_price'] : null);
    $currentPrice = to_price_abs(isset($chart['current_price']) ? $chart['current_price'] : $closePrice);
    $prevClose = to_price_abs(isset($chart['prev_close']) ? $chart['prev_close'] : null);

    $volume = to_price_abs(isset($chart['volume']) ? $chart['volume'] : null);
    $turnoverRaw = isset($chart['turnover']) ? $chart['turnover'] : null;
    $turnover = brokerNormalizeDailyTurnover(
        $turnoverRaw,
        $volume,
        $closePrice !== null ? $closePrice : $currentPrice,
        isset($chart['raw']) && is_array($chart['raw']) ? $chart['raw'] : null
    );

    $changePct = to_float_signed(isset($chart['change_pct']) ? $chart['change_pct'] : null);

    if ($changePct === null && $prevClose !== null && $prevClose > 0 && $currentPrice !== null) {
        $changePct = (($currentPrice - $prevClose) / $prevClose) * 100.0;
    }
    json_response(array(
        'ok' => true,
        'ticker' => $stkCd,
        'market' => strtoupper($market),
        'date' => preg_replace('/[^0-9]/', '', (string)$date),
        'quote' => array(
            'open_price' => $openPrice,
            'high_price' => $highPrice,
            'low_price' => $lowPrice,
            'close_price' => $closePrice !== null ? $closePrice : $currentPrice,
            'current_price' => $currentPrice,
            'prev_close' => $prevClose,
            'change_pct' => $changePct,
            'volume' => $volume !== null ? (int) round($volume) : null,
            'turnover' => $turnover
        ),
        'return_code' => isset($chart['return_code']) ? $chart['return_code'] : 0,
        'return_msg' => isset($chart['return_msg']) ? $chart['return_msg'] : ''
    ), 200);
}


/*
kiwoom_broker.php URI 탐색 버전
목적:
- ka90001 / ka90002 가 실제로 붙는 URI 후보 탐색
*/

$themeUriCandidates = array(
    '/api/dostk/rkinfo',
    '/api/dostk/theme',
    '/api/dostk/thme',
    '/api/dostk/thema',
    '/api/dostk/stkinfo',
    '/api/dostk/mrkinfo'
);

/*
ka90001 파라미터 탐색 축소판
확정:
- URI = /api/dostk/thme
- 필수 입력값 = qry_tp, date_tp
*/

if ($action === 'theme_groups') {
/*
$candidates = array(
array('qry_tp' => '0', 'date_tp' => '0', 'flu_pl_amt_tp' => '0'),
array('qry_tp' => '0', 'date_tp' => '1', 'flu_pl_amt_tp' => '0'),
array('qry_tp' => '1', 'date_tp' => '0', 'flu_pl_amt_tp' => '0'),
array('qry_tp' => '1', 'date_tp' => '1', 'flu_pl_amt_tp' => '0'),

array('qry_tp' => '0', 'date_tp' => '0', 'flu_pl_amt_tp' => '1'),
array('qry_tp' => '0', 'date_tp' => '1', 'flu_pl_amt_tp' => '1'),
array('qry_tp' => '1', 'date_tp' => '0', 'flu_pl_amt_tp' => '1'),
array('qry_tp' => '1', 'date_tp' => '1', 'flu_pl_amt_tp' => '1'),

array('qry_tp' => '0', 'date_tp' => '0', 'flu_pl_amt_tp' => '2'),
array('qry_tp' => '0', 'date_tp' => '1', 'flu_pl_amt_tp' => '2'),
array('qry_tp' => '1', 'date_tp' => '0', 'flu_pl_amt_tp' => '2'),
array('qry_tp' => '1', 'date_tp' => '1', 'flu_pl_amt_tp' => '2'),

array('qry_tp' => '0', 'date_tp' => '0', 'flu_pl_amt_tp' => '0', 'mrkt_tp' => '000'),
array('qry_tp' => '0', 'date_tp' => '1', 'flu_pl_amt_tp' => '0', 'mrkt_tp' => '000'),
array('qry_tp' => '0', 'date_tp' => '0', 'flu_pl_amt_tp' => '0', 'mrkt_tp' => '001'),
array('qry_tp' => '0', 'date_tp' => '1', 'flu_pl_amt_tp' => '0', 'mrkt_tp' => '001')
);
*/

$candidates = array(
array('qry_tp' => '0', 'date_tp' => '0', 'flu_pl_amt_tp' => '0', 'stex_tp' => '1'),
array('qry_tp' => '0', 'date_tp' => '1', 'flu_pl_amt_tp' => '0', 'stex_tp' => '1'),
array('qry_tp' => '1', 'date_tp' => '0', 'flu_pl_amt_tp' => '0', 'stex_tp' => '1'),
array('qry_tp' => '1', 'date_tp' => '1', 'flu_pl_amt_tp' => '0', 'stex_tp' => '1'),

array('qry_tp' => '0', 'date_tp' => '0', 'flu_pl_amt_tp' => '1', 'stex_tp' => '1'),
array('qry_tp' => '0', 'date_tp' => '1', 'flu_pl_amt_tp' => '1', 'stex_tp' => '1'),

array('qry_tp' => '0', 'date_tp' => '0', 'flu_pl_amt_tp' => '0', 'stex_tp' => '2'),
array('qry_tp' => '0', 'date_tp' => '0', 'flu_pl_amt_tp' => '0', 'stex_tp' => '3')
);


$trials = array();

foreach ($candidates as $body) {
$res = http_json(
'POST',
kiwoom_base_url() . '/api/dostk/thme',
auth_headers('ka90001', $token),
$body
);

$http = isset($res['_http_code']) ? (int)$res['_http_code'] : 0;
$rc = isset($res['return_code']) ? (string)$res['return_code'] : '0';
$msg = isset($res['message']) ? $res['message'] : (isset($res['return_msg']) ? $res['return_msg'] : null);

$trials[] = array(
'body' => $body,
'http' => $http,
'return_code' => $rc,
'message' => $msg
);

if ($http < 400 && ($rc === '' || $rc === '0')) {
json_response(array(
'ok' => true,
'action' => 'theme_groups',
'matched_body' => $body,
'data' => $res,
'trials' => $trials
), 200);
}
}

fail_response(500, 'theme_groups_failed', array(
'trials' => $trials
));
}

/*
ka90002 theme_components 탐색 초안
전제:
- URI는 ka90001과 동일 계열로 /api/dostk/thme 우선 탐색
- theme_code 샘플은 ka90001 성공 응답의 thema_grp_cd 사용 (예: 200)
*/

if ($action === 'theme_components') {
    $themeCode = isset($_GET['theme_code']) ? trim((string)$_GET['theme_code']) : '';
    if ($themeCode === '') {
        fail_response(400, 'theme_code_required');
    }

    $candidates = array(
        array('thema_grp_cd' => $themeCode),
        array('thema_cd' => $themeCode),
        array('thema_code' => $themeCode),
        array('tm_cd' => $themeCode),
        array('grp_cd' => $themeCode),

        array('thema_grp_cd' => $themeCode, 'stex_tp' => '1'),
        array('thema_cd' => $themeCode, 'stex_tp' => '1'),
        array('thema_code' => $themeCode, 'stex_tp' => '1'),
        array('tm_cd' => $themeCode, 'stex_tp' => '1'),

        array('thema_grp_cd' => $themeCode, 'qry_tp' => '0', 'date_tp' => '0', 'flu_pl_amt_tp' => '0', 'stex_tp' => '1'),
        array('thema_cd' => $themeCode, 'qry_tp' => '0', 'date_tp' => '0', 'flu_pl_amt_tp' => '0', 'stex_tp' => '1'),
        array('thema_code' => $themeCode, 'qry_tp' => '0', 'date_tp' => '0', 'flu_pl_amt_tp' => '0', 'stex_tp' => '1')
    );

    $trials = array();
    foreach ($candidates as $body) {
        $res = http_json(
            'POST',
            kiwoom_base_url() . '/api/dostk/thme',
            auth_headers('ka90002', $token),
            $body
        );

        $http = isset($res['_http_code']) ? (int)$res['_http_code'] : 0;
        $rc = isset($res['return_code']) ? (string)$res['return_code'] : '0';
        $msg = isset($res['message']) ? $res['message'] : (isset($res['return_msg']) ? $res['return_msg'] : null);

        $trials[] = array(
            'body' => $body,
            'http' => $http,
            'return_code' => $rc,
            'message' => $msg
        );

        if ($http < 400 && ($rc === '' || $rc === '0')) {
            json_response(array(
                'ok' => true,
                'action' => 'theme_components',
                'matched_body' => $body,
                'data' => $res,
                'trials' => $trials,
            ), 200);
        }
    }

    fail_response(500, 'theme_components_failed', array(
        'trials' => $trials,
        'theme_code' => $themeCode,
    ));
}


fail_response(400, 'unknown_action');
} catch (Throwable $e) {
fail_response(500, 'server_error', array(
'detail' => $e->getMessage()
));
}
