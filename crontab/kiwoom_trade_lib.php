<?php

declare(strict_types=1);

require_once __DIR__ . '/kiwoom_rest_doc_based.php';

function neulsom_require_secret_config() {
    $path = '/data/sow/private/kiwoom_secrets.php';
    if (!is_file($path)) {
        throw new RuntimeException('missing secret file: ' . $path);
    }
    $cfg = require $path;
    if (!is_array($cfg)) {
        throw new RuntimeException('invalid secret config');
    }
    return $cfg;
}

function neulsom_bootstrap_kiwoom_env(array $cfg) {
    $_ENV['KIWOOM_ENV'] = isset($cfg['kiwoom_env']) ? (string)$cfg['kiwoom_env'] : 'real';
    $_ENV['KIWOOM_APP_KEY'] = isset($cfg['appkey']) ? (string)$cfg['appkey'] : '';
    $_ENV['KIWOOM_APP_SECRET'] = isset($cfg['secretkey']) ? (string)$cfg['secretkey'] : '';

    if (isset($cfg['trade']) && is_array($cfg['trade'])) {
        if (isset($cfg['trade']['account_no'])) {
            $_ENV['KIWOOM_ACCOUNT_NO'] = (string)$cfg['trade']['account_no'];
        }
        if (isset($cfg['trade']['account_product_code'])) {
            $_ENV['KIWOOM_ACCOUNT_PRODUCT_CODE'] = (string)$cfg['trade']['account_product_code'];
        }
        if (array_key_exists('dry_run', $cfg['trade'])) {
            $_ENV['KIWOOM_DRY_RUN'] = $cfg['trade']['dry_run'] ? '1' : '0';
        }
    }
}

function neulsom_mask_value($value) {
    if ($value === null) return null;
    $s = (string)$value;
    $len = strlen($s);
    if ($len <= 4) return str_repeat('*', $len);
    return str_repeat('*', $len - 4) . substr($s, -4);
}

function neulsom_log($prefix, array $context) {
    $masked = $context;
    foreach (array('token','authorization','appkey','appsecret','secretkey','password','account_no','cano') as $k) {
        if (isset($masked[$k])) {
            $masked[$k] = neulsom_mask_value($masked[$k]);
        }
    }
    echo '[' . $prefix . '] ' . json_encode($masked, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}

function neulsom_tick_size($price) {
    if ($price < 2000) return 1;
    if ($price < 5000) return 5;
    if ($price < 20000) return 10;
    if ($price < 50000) return 50;
    if ($price < 200000) return 100;
    if ($price < 500000) return 500;
    return 1000;
}

function neulsom_floor_to_tick($price) {
    $tick = neulsom_tick_size($price);
    if ($tick <= 0) return (int)$price;
    return (int)(floor($price / $tick) * $tick);
}

function neulsom_build_buy_price_at_least_5ticks_up($basePrice) {
    if ($basePrice <= 0) return 0;
    $baseTickPrice = neulsom_floor_to_tick($basePrice);
    $tick = neulsom_tick_size($baseTickPrice > 0 ? $baseTickPrice : $basePrice);
    return (int)($baseTickPrice + ($tick * 5));
}

function neulsom_to_float($value) {
    if ($value === null) return null;
    $s = trim((string)$value);
    if ($s === '') return null;
    $s = str_replace(',', '', $s);
    if ($s !== '' && $s[0] === '+') $s = substr($s, 1);
    if (!preg_match('/^-?\d+(?:\.\d+)?$/', $s)) return null;
    return (float)$s;
}

function neulsom_abs_price($value) {
    $v = neulsom_to_float($value);
    if ($v === null) return null;
    return abs($v);
}

function neulsom_pick_first_price(array $raw, array $keys) {
    foreach ($keys as $key) {
        if (array_key_exists($key, $raw)) {
            $v = neulsom_abs_price($raw[$key]);
            if ($v !== null && $v > 0) {
                return $v;
            }
        }
    }
    return null;
}

function neulsom_fetch_quote($ticker, $market) {
    $tokenRes = issueToken();
    $token = isset($tokenRes['token']) ? (string)$tokenRes['token'] : '';
    if ($token === '') {
        throw new RuntimeException('token missing from kiwoom response');
    }

    $raw = getStockInfo($token, $ticker, $market);

    return array(
        'token' => $token,
        'current_price' => neulsom_pick_first_price($raw, array('current_price', 'cur_prc', 'stck_prpr')),
        'expected_price' => neulsom_pick_first_price($raw, array('expected_contract_price', 'exp_cntr_pric', 'exp_prc')),
        'prev_close' => neulsom_pick_first_price($raw, array('base_price', 'base_pric')),
        'raw' => $raw,
    );
}

function neulsom_pick_base_price(array $quote, array $row) {
    $candidates = array(
        isset($quote['current_price']) ? $quote['current_price'] : null,
        isset($quote['expected_price']) ? $quote['expected_price'] : null,
        isset($quote['prev_close']) ? $quote['prev_close'] : null,
        isset($row['reference_price']) ? $row['reference_price'] : null,
    );
    foreach ($candidates as $v) {
        if ($v !== null && (float)$v > 0) {
            return (float)$v;
        }
    }
    return 0.0;
}

function neulsom_build_cash_order_payload($ticker, $qty, $price, $side, $isMarket) {
    return array(
        'dmst_stex_tp' => 'KRX',
        'stk_cd' => $ticker,
        'ord_qty' => (string)$qty,
        'ord_uv' => $isMarket ? '' : (string)$price,
        'trde_tp' => $isMarket ? '3' : '1',
        'side' => $side,
    );
}

function neulsom_get_holding_qty($ticker, $accountNo, $accountProductCode) {
    $tokenRes = issueToken();
    $token = isset($tokenRes['token']) ? (string)$tokenRes['token'] : '';
    if ($token === '') {
        throw new RuntimeException('token missing from kiwoom response');
    }

    $res = getAccountHoldings($token, $accountNo, $accountProductCode);
    $target = preg_replace('/^A/', '', preg_replace('/_(NX|AL)$/', '', trim((string)$ticker)));

    $candidateLists = array();
    if (isset($res['items']) && is_array($res['items'])) $candidateLists['items'] = $res['items'];
    if (isset($res['stocks']) && is_array($res['stocks'])) $candidateLists['stocks'] = $res['stocks'];
    if (isset($res['list']) && is_array($res['list'])) $candidateLists['list'] = $res['list'];
    if (isset($res['output']) && is_array($res['output'])) $candidateLists['output'] = $res['output'];
    if (isset($res['output1']) && is_array($res['output1'])) $candidateLists['output1'] = $res['output1'];
    if (isset($res['output2']) && is_array($res['output2'])) $candidateLists['output2'] = $res['output2'];
    if (isset($res['acnt_evlt_remn_indv_tot']) && is_array($res['acnt_evlt_remn_indv_tot'])) $candidateLists['acnt_evlt_remn_indv_tot'] = $res['acnt_evlt_remn_indv_tot'];

    $debugSummary = array(
        'ticker' => $target,
        'used_uri' => isset($res['_used_uri']) ? $res['_used_uri'] : '',
        'used_api_id' => isset($res['_used_api_id']) ? $res['_used_api_id'] : '',
        'used_body' => isset($res['_used_body']) ? $res['_used_body'] : array(),
        'summary_only' => !empty($res['_summary_only']),
        'top_level_keys' => array_keys($res),
        'list_sizes' => array(),
        'samples' => array(),
    );

    foreach ($candidateLists as $listName => $list) {
        $debugSummary['list_sizes'][$listName] = is_array($list) ? count($list) : 0;
        if (is_array($list) && isset($list[0]) && is_array($list[0])) {
            $sample = $list[0];
            $debugSummary['samples'][$listName] = array_slice($sample, 0, 12, true);
        }
    }

    neulsom_log('SELL_HOLDING_DEBUG', $debugSummary);
    foreach ($candidateLists as $listName => $list) {
        foreach ($list as $row) {
            if (!is_array($row)) continue;
            $code = '';
            foreach (array('stk_cd','pdno','item_code','code','shtn_pdno','jongmok_cd','isu_no','item_no') as $k) {
                if (isset($row[$k]) && trim((string)$row[$k]) !== '') {
                    $code = preg_replace('/^A/', '', preg_replace('/_(NX|AL)$/', '', trim((string)$row[$k])));
                    break;
                }
            }
            if ($code !== $target) continue;
            foreach (array('hold_qty','rmnd_qty','qty','hldg_qty','ord_psbl_qty','cblc_qty','bal_qty','own_qty','crd_qty','rmnd_psbl_qty','sell_psbl_qty') as $qk) {
                if (isset($row[$qk])) {
                    $qty = (int)str_replace(',', '', (string)$row[$qk]);
                    if ($qty > 0) {
                        neulsom_log('SELL_HOLDING_MATCH', array(
                            'ticker' => $target,
                            'list_name' => $listName,
                            'code_field' => $code,
                            'qty_field' => $qk,
                            'qty' => $qty,
                        ));
                        return $qty;
                    }
                }
            }
        }
    }

    $usedUri = isset($res['_used_uri']) ? (string)$res['_used_uri'] : '';
    $usedApiId = isset($res['_used_api_id']) ? (string)$res['_used_api_id'] : '';
    throw new RuntimeException('holding qty not found for ticker=' . $target . ' [uri=' . $usedUri . ', api_id=' . $usedApiId . ']');
}

function neulsom_submit_order(array $payload) {
    $tokenRes = issueToken();
    $token = isset($tokenRes['token']) ? (string)$tokenRes['token'] : '';
    if ($token === '') {
        throw new RuntimeException('token missing from kiwoom response');
    }

    $ticker = isset($payload['stk_cd']) ? (string)$payload['stk_cd'] : '';
    $qty = isset($payload['ord_qty']) ? (int)$payload['ord_qty'] : 0;
    $market = isset($payload['dmst_stex_tp']) ? (string)$payload['dmst_stex_tp'] : 'KRX';
    $tradeType = isset($payload['trde_tp']) ? (string)$payload['trde_tp'] : '1';
    $side = isset($payload['side']) ? strtoupper((string)$payload['side']) : 'BUY';

    if ($ticker === '' || $qty <= 0) {
        throw new RuntimeException('invalid payload for order submit');
    }

    if ($side === 'SELL') {
        return placeSellOrder($token, $ticker, $qty, $market, $tradeType);
    }
    return placeBuyOrder($token, $ticker, $qty, $market, $tradeType);
}
