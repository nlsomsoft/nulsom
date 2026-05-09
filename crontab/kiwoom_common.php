<?php
declare(strict_types=1);

function envv($key, $default = '')
{
if (isset($_ENV[$key])) {
return (string)$_ENV[$key];
}

$v = getenv($key);
if ($v === false) {
return (string)$default;
}

return (string)$v;
}

function require_envv($key)
{
$v = trim(envv($key));
if ($v === '') {
throw new RuntimeException("Missing required env: {$key}");
}
return $v;
}

function json_response($data, $statusCode)
{
http_response_code((int)$statusCode);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($data, JSON_UNESCAPED_UNICODE);
exit;
}

function fail_response($statusCode, $message, $extra = array())
{
json_response(array_merge(array(
'ok' => false,
'error' => $message
), $extra), $statusCode);
}

function load_kiwoom_config($config)
{
$_ENV['KIWOOM_ENV'] = isset($config['kiwoom_env']) ? (string)$config['kiwoom_env'] : 'real';
$_ENV['KIWOOM_APP_KEY'] = isset($config['appkey']) ? (string)$config['appkey'] : '';
$_ENV['KIWOOM_APP_SECRET'] = isset($config['secretkey']) ? (string)$config['secretkey'] : '';
}

function kiwoom_base_url()
{
$mode = strtolower(envv('KIWOOM_ENV', 'real'));
if ($mode === 'mock') {
return 'https://mockapi.kiwoom.com';
}
return 'https://api.kiwoom.com';
}

function http_json($method, $url, $headers = array(), $body = null)
{
$ch = curl_init($url);
$payload = $body === null ? null : json_encode($body, JSON_UNESCAPED_UNICODE);

curl_setopt_array($ch, array(
CURLOPT_CUSTOMREQUEST => $method,
CURLOPT_RETURNTRANSFER => true,
CURLOPT_HTTPHEADER => array_merge(array(
'Content-Type: application/json;charset=UTF-8',
'Accept: application/json'
), $headers),
CURLOPT_POSTFIELDS => $payload,
CURLOPT_TIMEOUT => 20,
));

$raw = curl_exec($ch);
$err = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($raw === false) {
return array(
'ok' => false,
'error' => $err,
'_http_code' => $code
);
}

$json = json_decode((string)$raw, true);
if (!is_array($json)) {
$json = array('raw' => $raw);
}
$json['_http_code'] = $code;
return $json;
}

function assert_kiwoom_ok($res, $context)
{
$http = isset($res['_http_code']) ? (int)$res['_http_code'] : 0;
$rc = isset($res['return_code']) ? (string)$res['return_code'] : '0';
$msg = isset($res['return_msg']) ? (string)$res['return_msg'] : '';

if ($http >= 400) {
throw new RuntimeException($context . ' HTTP ' . $http . ': ' . $msg);
}
if ($rc !== '' && $rc !== '0') {
throw new RuntimeException($context . ' failed ' . $rc . ': ' . $msg);
}
}

function issue_token()
{
$res = http_json(
'POST',
kiwoom_base_url() . '/oauth2/token',
array('api-id: au10001'),
array(
'grant_type' => 'client_credentials',
'appkey' => require_envv('KIWOOM_APP_KEY'),
'secretkey' => require_envv('KIWOOM_APP_SECRET')
)
);

assert_kiwoom_ok($res, 'token');
return $res;
}

function auth_headers($apiId, $token)
{
return array(
'api-id: ' . $apiId,
'authorization: Bearer ' . $token
);
}

function normalize_ticker($ticker, $market)
{
$ticker = trim((string)$ticker);
$market = strtoupper(trim((string)$market));

if ($market === 'NXT') {
return strpos($ticker, '_NX') !== false ? $ticker : $ticker . '_NX';
}
if ($market === 'SOR') {
return strpos($ticker, '_AL') !== false ? $ticker : $ticker . '_AL';
}
return preg_replace('/_(NX|AL)$/', '', $ticker);
}

/*
|--------------------------------------------------------------------------
| 숫자 파서
|--------------------------------------------------------------------------
| 키움은 가격 앞에 + / - 부호를 붙여 보낼 수 있음.
| 가격은 절대값으로, 등락률/등락폭은 signed 값으로 처리.
*/
function to_float_signed($value)
{
if ($value === null) {
return null;
}

$s = trim((string)$value);
if ($s === '') {
return null;
}

$sign = 1.0;
if (substr($s, 0, 1) === '+') {
$s = substr($s, 1);
} elseif (substr($s, 0, 1) === '-') {
$sign = -1.0;
$s = substr($s, 1);
}

$s = str_replace(',', '', $s);

if (!preg_match('/^\d+(?:\.\d+)?$/', $s)) {
return null;
}
return ((float)$s) * $sign;
}

function to_price_abs($value)
{
$v = to_float_signed($value);
if ($v === null) {
return null;
}
return abs($v);
}

function require_broker_token($config)
{
$headerToken = isset($_SERVER['HTTP_X_BROKER_TOKEN']) ? (string)$_SERVER['HTTP_X_BROKER_TOKEN'] : '';
$expected = isset($config['shared_token']) ? (string)$config['shared_token'] : '';

if ($expected === '' || !hash_equals($expected, $headerToken)) {
fail_response(401, 'invalid_token');
}
}

function mask_account($acct)
{
if ($acct === null || $acct === '') {
return null;
}
return '****-**-**' . substr((string)$acct, -2);
}

function first_present_value(array $row, array $keys) {
    foreach ($keys as $key) {
        if (array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
            return $row[$key];
        }
    }
    return null;
}

function is_chart_row_candidate(array $row) {
    foreach (array_keys($row) as $key) {
        if (preg_match('/(open|high|low|cur|close|qty|volume|trde|price|dt)/i', (string)$key)) {
            return true;
        }
    }
    return false;
}

function find_chart_rows_recursive($value, array &$rows) {
    if (!is_array($value)) {
        return;
    }

    $isList = array_keys($value) === range(0, count($value) - 1);
    if ($isList && !empty($value)) {
        $allRows = true;
        foreach ($value as $item) {
            if (!is_array($item) || !is_chart_row_candidate($item)) {
                $allRows = false;
                break;
            }
        }
        if ($allRows) {
            foreach ($value as $item) {
                $rows[] = $item;
            }
            return;
        }
    }

    foreach ($value as $item) {
        find_chart_rows_recursive($item, $rows);
    }
}

function extract_first_chart_rows(array $res) {
    $rows = array();
    find_chart_rows_recursive($res, $rows);
    return $rows;
}

function row_date_ymd(array $row) {
    $raw = first_present_value($row, array('dt', 'date', 'trde_dt', 'base_dt', 'stck_bsop_date'));
    return preg_replace('/[^0-9]/', '', (string)($raw !== null ? $raw : ''));
}

function parse_daily_ohlcv_from_chart(array $res, $dateYmd = null) {
    $target = preg_replace('/[^0-9]/', '', (string)($dateYmd !== null ? $dateYmd : ''));
    $rows = extract_first_chart_rows($res);
    if (!$rows) {
        throw new RuntimeException('daily_chart_rows_missing');
    }

    $picked = null;
    $nextRow = null;
    foreach ($rows as $idx => $row) {
        if (!is_array($row)) {
            continue;
        }
        $rowDate = row_date_ymd($row);
        if ($target !== '' && $rowDate === $target) {
            $picked = $row;
            $nextRow = isset($rows[$idx + 1]) ? $rows[$idx + 1] : null;
            break;
        }
        if ($picked === null) {
            $picked = $row;
            $nextRow = isset($rows[$idx + 1]) ? $rows[$idx + 1] : null;
        }
    }

    if (!is_array($picked)) {
        throw new RuntimeException('daily_chart_pick_failed');
    }

    $prevClose = first_present_value($picked, array('prev_close', 'base_pric', 'base_price', 'prev_cls_pric'));
    if (($prevClose === null || $prevClose === '') && is_array($nextRow)) {
        $prevClose = first_present_value($nextRow, array('close_price', 'cur_prc', 'close_pric', 'last_pric', 'stck_prpr'));
    }
	return array(
        'date' => row_date_ymd($picked),
        'open_price' => first_present_value($picked, array('open_price', 'open_pric', 'open', 'start_pric', 'stck_oprc')),
        'high_price' => first_present_value($picked, array('high_price', 'high_pric', 'high', 'highpric', 'stck_hgpr')),
        'low_price' => first_present_value($picked, array('low_price', 'low_pric', 'low', 'lowpric', 'stck_lwpr')),
        'close_price' => first_present_value($picked, array('close_price', 'cur_prc', 'close_pric', 'last_pric', 'close', 'stck_prpr')),
        'current_price' => first_present_value($picked, array('current_price', 'cur_prc', 'close_price', 'close_pric', 'last_pric', 'stck_prpr')),
        'prev_close' => $prevClose,
        'volume' => first_present_value($picked, array('volume', 'trade_volume', 'trde_qty', 'acc_trde_qty', 'acml_vol', 'cntg_vol')),
        'turnover' => first_present_value($picked, array('turnover', 'trade_value', 'trde_prica', 'acc_trde_prica', 'acml_tr_pbmn')),
        'change_pct' => first_present_value($picked, array('change_pct', 'flu_rt', 'prdy_ctrt', 'rate')),
        'raw' => $picked
    );
}

function get_daily_ohlcv_chart($token, $ticker, $market, $dateYmd) {
    $date = preg_replace('/[^0-9]/', '', (string)$dateYmd);
    $bodyCandidates = array(
        array('stk_cd' => $ticker, 'base_dt' => $date, 'upd_stkpc_tp' => '1'),
        array('stk_cd' => $ticker, 'dt' => $date, 'upd_stkpc_tp' => '1'),
        array('stk_cd' => $ticker, 'base_dt' => $date),
        array('stk_cd' => $ticker, 'dt' => $date),
        array('stk_cd' => $ticker)
    );

    $last = null;
    foreach ($bodyCandidates as $body) {
        $res = http_json(
            'POST',
            kiwoom_base_url() . '/api/dostk/chart',
            auth_headers('ka10081', $token),
            $body
        );
        $last = $res;
        $http = isset($res['_http_code']) ? (int)$res['_http_code'] : 0;
        $rc = isset($res['return_code']) ? (string)$res['return_code'] : '0';
        if ($http >= 400 || ($rc !== '' && $rc !== '0')) {
            continue;
        }
        $parsed = parse_daily_ohlcv_from_chart($res, $date);
        $parsed['return_code'] = isset($res['return_code']) ? $res['return_code'] : null;
        $parsed['return_msg'] = isset($res['return_msg']) ? $res['return_msg'] : null;
        return $parsed;
    }

    if (is_array($last)) {
        assert_kiwoom_ok($last, 'daily_ohlcv_chart');
    }
    throw new RuntimeException('daily_ohlcv_chart_failed');
}

function brokerNormalizeDailyTurnover($turnover, $volume, $closePrice, array $raw = null) {
    $turnoverVal = brokerToFloat($turnover);
    $volumeVal = brokerToFloat($volume);
    $closeVal = brokerToFloat($closePrice);

    if ($turnoverVal === null) {
        return null;
    }

    // 1순위: raw row에 백만원 단위 누적거래대금 필드가 있으면 KRW 환산
    if (is_array($raw)) {
        $rawPbmn = null;

        if (array_key_exists('acml_tr_pbmn', $raw) && $raw['acml_tr_pbmn'] !== null && $raw['acml_tr_pbmn'] !== '') {
            $rawPbmn = brokerToFloat($raw['acml_tr_pbmn']);
        } elseif (array_key_exists('acc_trde_prica', $raw) && $raw['acc_trde_prica'] !== null && $raw['acc_trde_prica'] !== '') {
            $rawPbmn = brokerToFloat($raw['acc_trde_prica']);
        }

        if ($rawPbmn !== null) {
            return (int) round($rawPbmn * 1000000);
        }
    }

    // 2순위: heuristic fallback
    // turnover가 가격*거래량 대비 너무 작고, 백만원 스케일이면 합리적일 때만 보정
    if ($volumeVal !== null && $closeVal !== null && $volumeVal > 0 && $closeVal > 0) {
        $expectedKrw = $closeVal * $volumeVal;
        $scaled = $turnoverVal * 1000000;

        if ($turnoverVal > 0) {
            $ratioRaw = $turnoverVal / $expectedKrw;
            $ratioScaled = $scaled / $expectedKrw;

            if ($ratioRaw < 0.01 && $ratioScaled > 0.5 && $ratioScaled < 1.5) {
                return (int) round($scaled);
            }
        }
    }

    return (int) round($turnoverVal);
}
