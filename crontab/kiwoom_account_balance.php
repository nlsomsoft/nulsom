<?php

function loadSecretsCompat($path) {
    if (!is_file($path)) {
        return array();
    }
    $cfg = require $path;
    return is_array($cfg) ? $cfg : array();
}

function cfg70($cfg, $path, $default = '') {
    $node = $cfg;
    foreach (explode('.', $path) as $part) {
        if (!is_array($node) || !array_key_exists($part, $node)) {
            return $default;
        }
        $node = $node[$part];
    }
    if ($node === null || $node === '') {
        return $default;
    }
    return $node;
}

function requireCfg70($cfg, $path) {
    $v = trim((string)cfg70($cfg, $path, ''));
    if ($v === '') {
        throw new RuntimeException('Missing required config: ' . $path);
    }
    return $v;
}

function maskAccount70($acct) {
    $acct = (string)$acct;
    $len = strlen($acct);
    if ($len <= 4) {
        return str_repeat('*', max(0, $len - 2)) . substr($acct, -2);
    }
    return substr($acct, 0, 2) . str_repeat('*', max(0, $len - 4)) . substr($acct, -2);
}

function pdo70($cfg) {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        cfg70($cfg, 'db.host', '127.0.0.1'),
        cfg70($cfg, 'db.port', '3306'),
        cfg70($cfg, 'db.name', 'trading')
    );

    return new PDO($dsn, cfg70($cfg, 'db.user', 'trader'), cfg70($cfg, 'db.password', ''), array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ));
}

function httpJson70($method, $url, $headers, $body) {
    $ch = curl_init($url);
    $httpHeaders = array('Content-Type: application/json', 'Accept: application/json');
    foreach ($headers as $k => $v) {
        $httpHeaders[] = $k . ': ' . $v;
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    if (strtoupper($method) !== 'GET') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
    $raw = curl_exec($ch);
    $err = curl_error($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($raw === false) {
        throw new RuntimeException('curl error: ' . $err);
    }
    $json = json_decode($raw, true);
    if (!is_array($json)) {
        throw new RuntimeException('invalid json response: ' . $raw);
    }
    $json['_http_code'] = $http;
    return $json;
}

function issueAccessToken70($cfg) {
    $appKey = requireCfg70($cfg, 'appkey');
    $secretKey = requireCfg70($cfg, 'secretkey');
    $res = httpJson70('POST', kiwoomBaseUrl70($cfg) . '/oauth2/token', array(
        'api-id' => 'au10001',
    ), array(
        'grant_type' => 'client_credentials',
        'appkey' => $appKey,
        'secretkey' => $secretKey,
    ));
    if (empty($res['token']) && empty($res['access_token'])) {
        throw new RuntimeException('kiwoom access token issue failed');
    }
    return isset($res['token']) ? (string)$res['token'] : (string)$res['access_token'];
}

function kiwoomBaseUrl70($cfg) {
    $env = strtolower((string)cfg70($cfg, 'kiwoom_env', 'real'));
    if ($env === 'mock' || $env === 'paper') {
        return rtrim((string)cfg70($cfg, 'kiwoom_base_url_mock', 'https://mockapi.kiwoom.com'), '/');
    }
    return rtrim((string)cfg70($cfg, 'kiwoom_base_url_real', 'https://api.kiwoom.com'), '/');
}

function authHeaders70($apiId, $token) {
    return array(
        'authorization' => 'Bearer ' . $token,
        'api-id' => $apiId,
    );
}

function assertOk70($res, $label) {
    $http = isset($res['_http_code']) ? (int)$res['_http_code'] : 0;
    $rc = isset($res['return_code']) ? (string)$res['return_code'] : '0';
    if ($http >= 400 || ($rc !== '' && $rc !== '0')) {
        $msg = isset($res['return_msg']) ? $res['return_msg'] : 'unknown error';
        throw new RuntimeException($label . ' failed: ' . $msg);
    }
}

function getAccountEvaluation70($cfg, $token, $accountNo, $accountProductCode) {
    $res = httpJson70('POST', kiwoomBaseUrl70($cfg) . '/api/dostk/acnt', authHeaders70('kt00004', $token), array(
        'qry_tp' => '0',
        'dmst_stex_tp' => 'KRX',
        'stk_cd' => '',
        'pdno' => '',
        'cano' => (string)$accountNo,
        'acnt_prdt_cd' => (string)$accountProductCode,
    ));
    assertOk70($res, 'account_evaluation');
    return $res;
}

function pickNumber70($res, $keys) {
    foreach ($keys as $key) {
        if (isset($res[$key]) && $res[$key] !== null && $res[$key] !== '') {
            return (string)$res[$key];
        }
    }
    foreach ($res as $value) {
        if (is_array($value)) {
            $picked = pickNumber70($value, $keys);
            if ($picked !== null) {
                return $picked;
            }
        }
    }
    return null;
}

function ensureBalanceTable70(PDO $pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS kiwoom_account_balance_logs (
        id BIGINT NOT NULL AUTO_INCREMENT,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        account_masked VARCHAR(32) NOT NULL,
        account_product_code VARCHAR(8) NOT NULL,
        estimated_assets DECIMAL(18,2) NULL,
        deposit DECIMAL(18,2) NULL,
        withdrawable DECIMAL(18,2) NULL,
        orderable DECIMAL(18,2) NULL,
        d2_expected_deposit DECIMAL(18,2) NULL,
        raw_json LONGTEXT NULL,
        PRIMARY KEY (id),
        KEY idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sql);
}

function insertBalanceLog70(PDO $pdo, $row) {
    $sql = "INSERT INTO kiwoom_account_balance_logs
        (account_masked, account_product_code, estimated_assets, deposit, withdrawable, orderable, d2_expected_deposit, raw_json)
        VALUES
        (:account_masked, :account_product_code, :estimated_assets, :deposit, :withdrawable, :orderable, :d2_expected_deposit, :raw_json)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':account_masked' => $row['account_masked'],
        ':account_product_code' => $row['account_product_code'],
        ':estimated_assets' => $row['estimated_assets'],
        ':deposit' => $row['deposit'],
        ':withdrawable' => $row['withdrawable'],
        ':orderable' => $row['orderable'],
        ':d2_expected_deposit' => $row['d2_expected_deposit'],
        ':raw_json' => $row['raw_json'],
    ));
}

try {
    $cfg = loadSecretsCompat('/data/sow/private/kiwoom_secrets.php');
    if (!is_array($cfg) || empty($cfg)) {
        throw new RuntimeException('kiwoom_secrets.php load failed');
    }

    $accountNo = requireCfg70($cfg, 'trade.account_no');
    $accountProductCode = cfg70($cfg, 'trade.account_product_code', '01');
    $accessToken = issueAccessToken70($cfg);
    $res = getAccountEvaluation70($cfg, $accessToken, $accountNo, $accountProductCode);

    $row = array(
        'account_masked' => maskAccount70($accountNo),
        'account_product_code' => $accountProductCode,
        'estimated_assets' => pickNumber70($res, array('tot_est_amt', 'aset_evlt_amt', 'tot_asst_evlu_amt', 'tot_evlt_amt', 'tot_evlt_prft_amt')),
        'deposit' => pickNumber70($res, array('entr', 'dnca_tot_amt', 'ord_psbl_cash', 'cash', 'deposit')),
        'withdrawable' => pickNumber70($res, array('prsm_dpst_aset_amt', 'wdrw_psbl_amt')),
        'orderable' => pickNumber70($res, array('prsm_dpst_aset_amt', 'ord_psbl_amt', 'ord_psbl_cash', 'buy_psbl_amt')),
        'd2_expected_deposit' => pickNumber70($res, array('d2_entra', 'nxdy_excc_amt', 'd2_dnca_tot_amt')),
        'raw_json' => json_encode($res, JSON_UNESCAPED_UNICODE),
    );


// DB insert 예시 (요청대로 주석 처리)
    // $pdo = pdo70($cfg);
    // ensureBalanceTable70($pdo);
    // insertBalanceLog70($pdo, $row);
    // INSERT INTO kiwoom_account_balance_logs
    // (account_masked, account_product_code, estimated_assets, deposit, withdrawable, orderable, d2_expected_deposit, raw_json)
    // VALUES
    // (:account_masked, :account_product_code, :estimated_assets, :deposit, :withdrawable, :orderable, :d2_expected_deposit, :raw_json)

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'ok' => true,
        'saved' => false,
        'balance' => $row,
    ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'ok' => false,
        'error' => $e->getMessage(),
    ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
