<?php

declare(strict_types=1);

/*
 * Deploy on Neulsom server (211.174.61.140) behind HTTPS.
 * Secrets file must live OUTSIDE web root.
 */

require DIR . '/kiwoom_rest_doc_based.php';

$configPath = getenv('KIWOOM_BROKER_SECRET_FILE') ?: '/var/www/private/kiwoom_broker_secrets.php';
if (!is_file($configPath)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'missing_secret_config'], JSON_UNESCAPED_UNICODE);
    exit;
}

$cfg = require $configPath;
header('Content-Type: application/json; charset=utf-8');

function brokerDeny(int $code, string $msg, array $extra = []): void {
    http_response_code($code);
    echo json_encode(array_merge(['ok' => false, 'error' => $msg], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

function brokerToken(array $cfg): void {
    $headerToken = $_SERVER['HTTP_X_BROKER_TOKEN'] ?? '';
    $expected = (string)($cfg['shared_token'] ?? '');
    if ($expected === '' || !hash_equals($expected, $headerToken)) {
        brokerDeny(401, 'invalid_token');
    }
}

function brokerIpAllow(array $cfg): void {
    $allowed = $cfg['allowed_ips'] ?? [];
    if (!$allowed) return;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!in_array($ip, $allowed, true)) {
        brokerDeny(403, 'forbidden_ip');
    }
}

function brokerLoadEnv(array $cfg): void {
    $_ENV['KIWOOM_ENV'] = (string)($cfg['kiwoom_env'] ?? 'real');
    $_ENV['KIWOOM_APP_KEY'] = (string)($cfg['appkey'] ?? '');
    $_ENV['KIWOOM_APP_SECRET'] = (string)($cfg['secretkey'] ?? '');
}

function brokerMaskAccount(?string $acct): ?string {
    if ($acct === null || $acct === '') return null;
    $tail = substr($acct, -2);
    return '****-**-**' . $tail;
}

function brokerToFloat($value): ?float {
    if ($value === null) return null;
    $s = trim((string)$value);
    if ($s === '') return null;
    $sign = 1.0;
    if ($s[0] === '+') $s = substr($s, 1);
    if ($s[0] === '-') { $sign = -1.0; $s = substr($s, 1); }
    $s = str_replace(',', '', $s);
    if (!preg_match('/^\d+(?:\.\d+)?$/', $s)) return null;
    return ((float)$s) * $sign;
}

function brokerParsePreopen(array $raw): array {
    $parsed = parsePreopenFromStockInfo($raw);
    $expected = brokerToFloat($parsed['expected_contract_price'] ?? null);
    $current = brokerToFloat($parsed['current_price'] ?? null);
    $base = brokerToFloat($parsed['base_price'] ?? null);
    $gap = brokerToFloat($parsed['change_pct'] ?? null);
    $effective = $expected ?? $current;
    if ($gap === null && $effective !== null && $base !== null && $base > 0) {
        $gap = (($effective - $base) / $base) * 100.0;
    }
    return [
        'expected_price' => $expected,
        'current_price' => $current,
        'effective_price' => $effective,
        'prev_close' => $base,
        'expected_change_pct' => $gap,
        'expected_contract_qty' => brokerToFloat($parsed['expected_contract_qty'] ?? null),
        'raw' => $raw,
    ];
}

function brokerNormalizeDailyTurnover(?float $turnover, ?float $volume, ?float $refPrice, $rawRow): ?float {
    if ($turnover === null) return null;
    if (is_array($rawRow)) {
        $pbmn = brokerToFloat($rawRow['acml_tr_pbmn'] ?? null);
        if ($pbmn !== null) {
            return $pbmn * 1000000.0;
        }
    }
    if ($turnover > 0 && $volume !== null && $volume > 0 && $refPrice !== null && $refPrice > 0) {
        $notional = $volume * $refPrice;
        $scaled = $turnover * 1000000.0;
        if ($turnover < 100000000 && $scaled >= ($notional * 0.2) && $scaled <= ($notional * 5.0)) {
            return $scaled;
        }
    }
    return $turnover;
}
function brokerParseDailyQuote(array $quote): array {
    $open = brokerToFloat($quote['open_price'] ?? null);
    $high = brokerToFloat($quote['high_price'] ?? null);
    $low = brokerToFloat($quote['low_price'] ?? null);
    $close = brokerToFloat($quote['close_price'] ?? ($quote['current_price'] ?? null));
    $current = brokerToFloat($quote['current_price'] ?? ($quote['close_price'] ?? null));
    $prevClose = brokerToFloat($quote['prev_close'] ?? null);
    $changePct = brokerToFloat($quote['change_pct'] ?? null);
    $volume = brokerToFloat($quote['volume'] ?? ($quote['trade_volume'] ?? null));
    $turnover = brokerToFloat($quote['turnover'] ?? ($quote['trade_value'] ?? null));
    $refPrice = $close ?? $current ?? $open ?? $prevClose;
    $turnover = brokerNormalizeDailyTurnover($turnover, $volume, $refPrice, $quote['raw'] ?? null);
    if ($changePct === null && $prevClose !== null && $prevClose > 0 && $current !== null) {
        $changePct = (($current - $prevClose) / $prevClose) * 100.0;
    }
    return [
        'open_price' => $open,
        'high_price' => $high,
        'low_price' => $low,
        'close_price' => $close,
        'current_price' => $current,
        'prev_close' => $prevClose,
        'change_pct' => $changePct,
        'volume' => $volume !== null ? (int)round($volume) : null,
        'turnover' => $turnover !== null ? (int)round($turnover) : null,
        'raw' => $quote['raw'] ?? null,
    ];
}

try {
    brokerToken($cfg);
    brokerIpAllow($cfg);
    brokerLoadEnv($cfg);

    $action = trim((string)($_GET['action'] ?? ''));
    $market = trim((string)($_GET['market'] ?? 'KRX')) ?: 'KRX';

    $tokenRes = issueToken();
    $token = (string)($tokenRes['token'] ?? '');
    if ($token === '') {
        brokerDeny(500, 'token_missing');
    }

    switch ($action) {
        case 'auth_check':
            echo json_encode([
                'ok' => true,
                'message' => 'token_issued',
                'expires_in' => null,
                'env' => env2('KIWOOM_ENV', 'real'),
            ], JSON_UNESCAPED_UNICODE);
            exit;

        case 'account_info':
            $acct = getAccounts($token);
            echo json_encode([
                'ok' => true,
                'account_masked' => brokerMaskAccount($acct['acctNo'] ?? null),
                'env' => env2('KIWOOM_ENV', 'real'),
            ], JSON_UNESCAPED_UNICODE);
            exit;

        case 'preopen_quote':
        case 'quote':
            $ticker = trim((string)($_GET['ticker'] ?? ''));
            if ($ticker === '') brokerDeny(400, 'missing_ticker');
            $raw = getStockInfo($token, $ticker, $market);
            $quote = brokerParsePreopen($raw);
            echo json_encode([
                'ok' => true,
                'ticker' => normalizeTickerByMarket($ticker, $market),
                'market' => strtoupper($market),
                'source' => 'kiwoom_broker',
                'quote' => $quote,
            ], JSON_UNESCAPED_UNICODE);
            exit;

        case 'intraday_ohlc':
            $ticker = trim((string)($_GET['ticker'] ?? ''));
            if ($ticker === '') brokerDeny(400, 'missing_ticker');
            $date = trim((string)($_GET['date'] ?? ''));
            if ($date === '') {
                $date = date('Ymd');
            }
            $chart = getDailyOhlcvChart($token, $ticker, $market, $date);
            $quote = brokerParseDailyQuote($chart);
            echo json_encode([
                'ok' => true,
                'ticker' => normalizeTickerByMarket($ticker, $market),
                'market' => strtoupper($market),
                'date' => preg_replace('/[^0-9]/', '', $date),
                'source' => 'kiwoom_broker',
                'quote' => $quote,
            ], JSON_UNESCAPED_UNICODE);
            exit;
        case 'investor_flow':
            $ticker = trim((string)($_GET['ticker'] ?? ''));
            if ($ticker === '') brokerDeny(400, 'missing_ticker');
            $date = trim((string)($_GET['date'] ?? ''));
            if ($date === '') {
                $date = date('Ymd');
            }
            $raw = getInvestorInstitutionChart($token, $ticker, $date, $market);
            $flow = parseInvestorInstitutionFlow($raw, $date);
            echo json_encode([
                'ok' => true,
                'ticker' => normalizeTickerByMarket($ticker, $market),
                'market' => strtoupper($market),
                'date' => preg_replace('/[^0-9]/', '', $date),
                'source' => 'kiwoom_broker',
                'investor_flow' => $flow,
            ], JSON_UNESCAPED_UNICODE);
            exit;

        default:
            brokerDeny(400, 'unknown_action');
    }
} catch (Throwable $e) {
    brokerDeny(500, 'server_error', ['detail' => $e->getMessage()]);
}