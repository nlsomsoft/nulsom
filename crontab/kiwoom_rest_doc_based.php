<?php

declare(strict_types=1);

function loadEnvFile2($path) {
    if (!is_file($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') continue;
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($k, $v) = explode('=', $line, 2);
        $_ENV[trim($k)] = trim($v);
    }
}

function env2($k, $d = null) {
    if (isset($_ENV[$k]) && $_ENV[$k] !== '') return $_ENV[$k];
    $g = getenv($k);
    if ($g !== false && $g !== '') return $g;
    return $d === null ? '' : $d;
}

function requireEnv2($k) {
    $v = trim((string)env2($k));
    if ($v === '') throw new RuntimeException("Missing required env: {$k}");
    return $v;
}

function httpJson($method, $url, $headers = array(), $body = null) {
    $ch = curl_init($url);
    $payload = $body === null ? null : json_encode($body, JSON_UNESCAPED_UNICODE);
    curl_setopt_array($ch, array(
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array_merge(array('Content-Type: application/json;charset=UTF-8', 'Accept: application/json'), $headers),
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_TIMEOUT => 20,
    ));
    $raw = curl_exec($ch);
    $err = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($raw === false) {
        return array('ok' => false, 'error' => $err, '_http_code' => $code);
    }
    $json = json_decode((string)$raw, true);
    if (!is_array($json)) $json = array('raw' => $raw);
    $json['_http_code'] = $code;
    return $json;
}

function assertOk($res, $ctx) {
    $rc = isset($res['return_code']) ? (string)$res['return_code'] : '0';
    $msg = isset($res['return_msg']) ? (string)$res['return_msg'] : '';
    $http = isset($res['_http_code']) ? (int)$res['_http_code'] : 0;
    if ($http >= 400) throw new RuntimeException("{$ctx} HTTP {$http}: {$msg}");
    if ($rc !== '' && $rc !== '0') throw new RuntimeException("{$ctx} failed {$rc}: {$msg}");
}

function kiwoomBaseUrl() {
    $mode = strtolower((string)env2('KIWOOM_ENV', 'mock'));
    return $mode === 'real' ? 'https://api.kiwoom.com' : 'https://mockapi.kiwoom.com';
}

function issueToken() {
    $res = httpJson('POST', kiwoomBaseUrl() . '/oauth2/token', array('api-id: au10001'), array(
        'grant_type' => 'client_credentials',
        'appkey' => requireEnv2('KIWOOM_APP_KEY'),
        'secretkey' => requireEnv2('KIWOOM_APP_SECRET'),
    ));
    assertOk($res, 'token');
    return $res;
}

function authHeaders($apiId, $token) {
    return array(
        'api-id: ' . $apiId,
        'authorization: Bearer ' . $token,
    );
}

function getAccounts($token) {
    $res = httpJson('POST', kiwoomBaseUrl() . '/api/dostk/acnt', authHeaders('ka00001', $token), array());
    assertOk($res, 'accounts');
    return $res;
}

function getStockInfo($token, $ticker, $market) {
    if ($market === null || $market === '') $market = 'KRX';
    $code = normalizeTickerByMarket($ticker, $market);
    $res = httpJson('POST', kiwoomBaseUrl() . '/api/dostk/stkinfo', authHeaders('ka10001', $token), array(
        'stk_cd' => $code,
    ));
    assertOk($res, 'stock_info');
    return $res;
}

function getAccountEvaluation($token, $accountNo, $accountProductCode) {
    $res = httpJson('POST', kiwoomBaseUrl() . '/api/dostk/acnt_evl', authHeaders('kt00004', $token), array(
        'qry_tp' => '0',
        'dmst_stex_tp' => 'KRX',
        'stk_cd' => '',
        'pdno' => '',
        'cano' => (string)$accountNo,
        'acnt_prdt_cd' => (string)$accountProductCode,
    ));
    assertOk($res, 'account_evaluation');
    return $res;
}

function hasAccountHoldingList($res) {
    foreach (array('items', 'stocks', 'list', 'output', 'output1', 'output2', 'acnt_evlt_remn_indv_tot') as $key) {
        if (isset($res[$key]) && is_array($res[$key]) && count($res[$key]) > 0) {
            return true;
        }
    }
    return false;
}

function getAccountHoldings($token, $accountNo, $accountProductCode) {
    $bodyVariants = array(
        array(
            'qry_tp' => '0',
            'dmst_stex_tp' => 'KRX',
            'stk_cd' => '',
            'pdno' => '',
            'cano' => (string)$accountNo,
            'acnt_prdt_cd' => (string)$accountProductCode,
        ),
        array(
            'qry_tp' => '1',
            'dmst_stex_tp' => 'KRX',
            'stk_cd' => '',
            'pdno' => '',
            'cano' => (string)$accountNo,
            'acnt_prdt_cd' => (string)$accountProductCode,
        ),
        array(
            'inqr_dvsn' => '00',
            'dmst_stex_tp' => 'KRX',
            'stk_cd' => '',
            'pdno' => '',
            'cano' => (string)$accountNo,
            'acnt_prdt_cd' => (string)$accountProductCode,
        ),
        array(
            'qry_tp' => '0',
            'dmst_stex_tp' => 'KRX',
            'stk_cd' => '',
            'pdno' => '',
            'prcs_dvsn' => '00',
            'cano' => (string)$accountNo,
            'acnt_prdt_cd' => (string)$accountProductCode,
        ),
    );

    $candidates = array();
    foreach (array('kt00018', 'kt00005', 'kt00004') as $apiId) {
        foreach (array('/api/dostk/acnt_evl', '/api/dostk/acnt') as $uri) {
            foreach ($bodyVariants as $body) {
                $candidates[] = array(
                    'uri' => $uri,
                    'api_id' => $apiId,
                    'body' => $body,
                );
            }
        }
    }

    $lastErr = null;
    $lastSummarySuccess = null;
    foreach ($candidates as $candidate) {
        $res = httpJson('POST', kiwoomBaseUrl() . $candidate['uri'], authHeaders($candidate['api_id'], $token), $candidate['body']);
        $rc = isset($res['return_code']) ? (string)$res['return_code'] : '0';
        $msg = isset($res['return_msg']) ? (string)$res['return_msg'] : '';
        $http = isset($res['_http_code']) ? (int)$res['_http_code'] : 0;

        if ($http < 400 && ($rc === '' || $rc === '0')) {
            $res['_used_uri'] = $candidate['uri'];
            $res['_used_api_id'] = $candidate['api_id'];
            $res['_used_body'] = $candidate['body'];

            if (hasAccountHoldingList($res)) {
                return $res;
            }

            $lastSummarySuccess = $res;
            continue;
        }

        $lastErr = 'account_holdings failed ' . $rc . ': ' . $msg
            . ' [uri=' . $candidate['uri'] . ', api_id=' . $candidate['api_id'] . ', http=' . $http . ']';
    }

    if (is_array($lastSummarySuccess)) {
        $lastSummarySuccess['_summary_only'] = true;
        return $lastSummarySuccess;
    }

    throw new RuntimeException($lastErr === null ? 'account_holdings failed: no candidate matched' : $lastErr);
}

function normalizeTickerByMarket($ticker, $market) {
    $ticker = trim((string)$ticker);
    $market = strtoupper((string)$market);
    if ($market === 'NXT') {
        return strpos($ticker, '_NX') !== false ? $ticker : $ticker . '_NX';
    }
    if ($market === 'SOR') {
        return strpos($ticker, '_AL') !== false ? $ticker : $ticker . '_AL';
    }
    return preg_replace('/_(NX|AL)$/', '', $ticker);
}

function parsePreopenFromStockInfo($res) {
    return array(
        'ticker' => isset($res['stk_cd']) ? $res['stk_cd'] : null,
        'name' => isset($res['stk_nm']) ? $res['stk_nm'] : null,
        'current_price' => isset($res['cur_prc']) ? $res['cur_prc'] : null,
        'base_price' => isset($res['base_pric']) ? $res['base_pric'] : null,
        'expected_contract_price' => isset($res['exp_cntr_pric']) ? $res['exp_cntr_pric'] : null,
        'expected_contract_qty' => isset($res['exp_cntr_qty']) ? $res['exp_cntr_qty'] : null,
        'change_value' => isset($res['pred_pre']) ? $res['pred_pre'] : null,
        'change_pct' => isset($res['flu_rt']) ? $res['flu_rt'] : null,
        'return_code' => isset($res['return_code']) ? $res['return_code'] : null,
        'return_msg' => isset($res['return_msg']) ? $res['return_msg'] : null,
    );
}

function placeBuyOrder($token, $ticker, $qty, $market, $tradeType) {
    if ($market === null || $market === '') $market = 'KRX';
    if ($tradeType === null || $tradeType === '') $tradeType = '1';
    $res = httpJson('POST', kiwoomBaseUrl() . '/api/dostk/ordr', authHeaders('kt10000', $token), array(
        'dmst_stex_tp' => strtoupper($market),
        'stk_cd' => normalizeTickerByMarket($ticker, $market),
        'ord_qty' => (string)$qty,
        'ord_uv' => '',
        'trde_tp' => (string)$tradeType,
        'cond_uv' => '',
    ));
    assertOk($res, 'buy_order');
    return $res;
}

function placeSellOrder($token, $ticker, $qty, $market, $tradeType) {
    if ($market === null || $market === '') $market = 'KRX';
    if ($tradeType === null || $tradeType === '') $tradeType = '3';
    $res = httpJson('POST', kiwoomBaseUrl() . '/api/dostk/ordr', authHeaders('kt10001', $token), array(
        'dmst_stex_tp' => strtoupper($market),
        'stk_cd' => normalizeTickerByMarket($ticker, $market),
        'ord_qty' => (string)$qty,
        'ord_uv' => '',
        'trde_tp' => (string)$tradeType,
        'cond_uv' => '',
    ));
    assertOk($res, 'sell_order');
    return $res;
}

function getThemeGroups(string $token, string $market = 'KRX'): array {
    $bodyCandidates = [
        ['mrkt_tp' => strtoupper($market)],
        ['dmst_stex_tp' => strtoupper($market)],
        [],
    ];
    $last = null;
    foreach ($bodyCandidates as $body) {
        $res = httpJson('POST', kiwoomBaseUrl() . '/api/dostk/thema', authHeaders('ka90001', $token), $body);
        $last = $res;
        $http = (int)($res['_http_code'] ?? 0);
        $rc = (string)($res['return_code'] ?? '0');
        if ($http < 400 && ($rc === '' || $rc === '0')) {
            return $res;
        }
    }
    if (is_array($last)) {
        assertOk($last, 'theme_groups');
    }
    throw new RuntimeException('theme_groups failed');
}

function getThemeComponents(string $token, string $themeCode, string $market = 'KRX'): array {
    $bodyCandidates = [
        ['mrkt_tp' => strtoupper($market), 'thema_cd' => $themeCode],
        ['dmst_stex_tp' => strtoupper($market), 'thema_cd' => $themeCode],
        ['thema_cd' => $themeCode],
    ];

    $last = null;
    foreach ($bodyCandidates as $body) {
        $res = httpJson('POST', kiwoomBaseUrl() . '/api/dostk/thema', authHeaders('ka90002', $token), $body);
        $last = $res;
        $http = (int)($res['_http_code'] ?? 0);
        $rc = (string)($res['return_code'] ?? '0');
        if ($http < 400 && ($rc === '' || $rc === '0')) {
            return $res;
        }
    }
    if (is_array($last)) {
        assertOk($last, 'theme_components');
    }
    throw new RuntimeException('theme_components failed');
}

function getInvestorInstitutionChart(string $token, string $ticker, string $dateYmd, string $market = 'KRX'): array {
    $code = normalizeTickerByMarket($ticker, $market);
    $body = [
        'dt' => preg_replace('/[^0-9]/', '', $dateYmd),
        'stk_cd' => $code,
        'amt_qty_tp' => '2', // 1: amount, 2: quantity
        'trde_tp' => '0',    // 0: net buy
        'unit_tp' => '1',    // 1: single share
    ];
    $res = httpJson('POST', kiwoomBaseUrl() . '/api/dostk/chart', authHeaders('ka10060', $token), $body);
    assertOk($res, 'investor_institution_chart');
    return $res;
}

function parseInvestorInstitutionFlow(array $res, string $dateYmd): array {
    $target = preg_replace('/[^0-9]/', '', $dateYmd);
    $rows = $res['stk_invsr_orgn_chart'] ?? [];
    if (!is_array($rows)) {
        $rows = [];
    }

    $picked = null;
    foreach ($rows as $row) {
        if (!is_array($row)) continue;
        $rowDate = preg_replace('/[^0-9]/', '', (string)($row['dt'] ?? ''));
        if ($rowDate === $target) {
            $picked = $row;
            break;
        }
        if ($picked === null) {
            $picked = $row;
        }
    }

    return [
        'date' => $target,
        'foreign_net_buy' => $picked['frgnr_invsr'] ?? null,
        'inst_net_buy' => $picked['orgn'] ?? null,
        'current_price' => $picked['cur_prc'] ?? null,
        'turnover' => $picked['acc_trde_prica'] ?? null,
        'raw' => $picked,
    ];
}