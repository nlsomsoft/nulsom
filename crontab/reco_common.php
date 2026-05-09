<?php
declare(strict_types=1);

function rc_json_response($data, $statusCode)
{
http_response_code((int)$statusCode);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($data, JSON_UNESCAPED_UNICODE);
exit;
}

function rc_fail($statusCode, $message, $extra = array())
{
rc_json_response(array_merge(array(
'ok' => false,
'error' => $message
), $extra), $statusCode);
}

function rc_load_config()
{
$configPath = '/data/sow/private/kiwoom_secrets.php';
if (!is_file($configPath)) {
rc_fail(500, 'missing_secret_config');
}

$config = require $configPath;
if (!is_array($config)) {
rc_fail(500, 'invalid_secret_config');
}

return $config;
}

function rc_require_token($config)
{
$headerToken = isset($_SERVER['HTTP_X_BROKER_TOKEN']) ? (string)$_SERVER['HTTP_X_BROKER_TOKEN'] : '';
$expected = isset($config['shared_token']) ? (string)$config['shared_token'] : '';

if ($expected === '' || !hash_equals($expected, $headerToken)) {
rc_fail(401, 'invalid_token');
}
}

function rc_get_raw_body()
{
$raw = file_get_contents('php://input');
if ($raw === false) {
rc_fail(400, 'failed_to_read_body');
}
return $raw;
}

function rc_get_json_body()
{
$raw = rc_get_raw_body();
$json = json_decode($raw, true);

if (!is_array($json)) {
rc_fail(400, 'invalid_json');
}

return $json;
}

function rc_string($value)
{
if ($value === null) {
return '';
}
return trim((string)$value);
}

function rc_nullable_float($value)
{
if ($value === null || $value === '') {
return null;
}

$s = trim((string)$value);
$s = str_replace(',', '', $s);

if ($s === '') {
return null;
}

if ($s[0] === '+') {
$s = substr($s, 1);
}

if (!preg_match('/^-?\d+(?:\.\d+)?$/', $s)) {
return null;
}

return (float)$s;
}

function rc_validate_date($date)
{
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$date)) {
return false;
}
return true;
}
