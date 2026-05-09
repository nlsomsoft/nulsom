<?php

declare(strict_types=1);

require_once __DIR__ . '/kiwoom_trade_lib.php';

$cfg = neulsom_require_secret_config();
neulsom_bootstrap_kiwoom_env($cfg);

$market = 'KRX';
$accountNo = isset($_ENV['KIWOOM_ACCOUNT_NO']) ? (string)$_ENV['KIWOOM_ACCOUNT_NO'] : '';
$accountProductCode = isset($_ENV['KIWOOM_ACCOUNT_PRODUCT_CODE']) ? (string)$_ENV['KIWOOM_ACCOUNT_PRODUCT_CODE'] : '';
$dryRun = isset($_ENV['KIWOOM_DRY_RUN']) ? ($_ENV['KIWOOM_DRY_RUN'] === '1') : true;

/*
 * ----------------------------------------------------------------
 * DB 조회부는 신대표님 서버 구조에 맞춰 직접 연결하실 영역입니다.
 * /data/sow/private/kiwoom_secrets.php 에는 DB 정보를 넣지 않습니다.
 *
 * 기대 배열 구조 예시:
 * $orders = array(
 *   array('ticker' => '005930', 'ticker_amount' => 2500000, 'name' => '삼성전자'),
 *   array('ticker' => '000660', 'ticker_amount' => 2500000, 'name' => 'SK하이닉스'),
 * );
 * ----------------------------------------------------------------
 */
$orders = array();

$orders = array(
  array('ticker' => '005930', 'ticker_amount' => 2500000, 'name' => '삼성전자'),
  array('ticker' => '000660', 'ticker_amount' => 2500000, 'name' => 'SK하이닉스'),
);

if (!$orders) {
    echo "[BUY] no orders (DB query area is intentionally commented out; fill ticker/ticker_amount manually)" . PHP_EOL;
    exit(0);
}

foreach ($orders as $row) {
    $ticker = isset($row['ticker']) ? trim((string)$row['ticker']) : '';
    $tickerAmount = isset($row['ticker_amount']) ? (int)$row['ticker_amount'] : 0;
    $name = isset($row['name']) ? (string)$row['name'] : '';

    if ($ticker === '' || $tickerAmount <= 0) {
        neulsom_log('BUY_SKIP', array(
            'ticker' => $ticker,
            'name' => $name,
            'reason' => 'missing_ticker_or_ticker_amount',
        ));
        continue;
    }

    try {
        $quote = neulsom_fetch_quote($ticker, $market);
    } catch (Throwable $e) {
        neulsom_log('BUY_QUOTE_WARN', array(
            'ticker' => $ticker,
            'name' => $name,
            'error' => $e->getMessage(),
        ));
        continue;
    }

    $basePrice = neulsom_pick_base_price($quote, $row);
    if ($basePrice <= 0) {
        neulsom_log('BUY_SKIP', array(
            'ticker' => $ticker,
            'name' => $name,
            'reason' => 'missing_base_price',
        ));
        continue;
    }

    // 현재가(또는 기준가)보다 최소 +5틱 이상 지정가
    $buyPrice = neulsom_build_buy_price_at_least_5ticks_up($basePrice);
    if ($buyPrice <= 0) {
        neulsom_log('BUY_SKIP', array(
            'ticker' => $ticker,
            'name' => $name,
            'reason' => 'invalid_buy_price',
        ));
        continue;
    }

    $qty = (int)floor($tickerAmount / $buyPrice);
    if ($qty <= 0) {
        neulsom_log('BUY_SKIP', array(
            'ticker' => $ticker,
            'name' => $name,
            'reason' => 'ticker_amount_too_small',
            'ticker_amount' => $tickerAmount,
            'buy_price' => $buyPrice,
        ));
        continue;
    }

    $payload = neulsom_build_cash_order_payload($ticker, $qty, $buyPrice, 'BUY', false);
    $orderAmount = (int)$qty * (int)$buyPrice;
    neulsom_log('BUY_PLAN', array(
        'ticker' => $ticker,
        'name' => $name,
        'ticker_amount' => $tickerAmount,
        'base_price' => $basePrice,
        'buy_price' => $buyPrice,
        'qty' => $qty,
        'order_amount' => $orderAmount,
        'dry_run' => $dryRun,
        'account_no' => $accountNo,
        'account_product_code' => $accountProductCode,
        'payload' => $payload,
    ));
/*
     * [늘솜 DB 저장 위치 #1 - 주문 계획/주문 기준]
     * 여기서 늘솜 DB에 INSERT 할 수 있습니다.
     *
     * 저장 후보 컬럼 예시:
     * - trading_date   : 오늘 날짜
     * - ticker         : $ticker
     * - name           : $name
     * - side           : 'BUY'
     * - planned_amount : $tickerAmount   // 종목별 목표 매수금액
     * - order_price    : $buyPrice       // 주문 지정가(+5틱)
     * - order_qty      : $qty            // 주문 수량
     * - order_amount   : $orderAmount    // 주문 기준 금액(qty * buyPrice)
     * - dry_run        : $dryRun ? 1 : 0
     * - status         : 'PLAN'
     * - created_at     : 현재시각
     *
     * 주의:
     * - 이 값은 '실제 체결값'이 아니라 '주문 계획/주문 기준값'입니다.
     * - 실제 체결가격/체결수량/체결금액은 아래 BUY_SUBMIT_OK 이후 응답 또는
     *   별도 체결조회 API로 후속 보강하는 것이 정확합니다.
     */

    if ($dryRun) {
        continue;
    }

    try {
        $result = neulsom_submit_order($payload);
        neulsom_log('BUY_SUBMIT_OK', array(
            'ticker' => $ticker,
            'name' => $name,
            'qty' => $qty,
            'buy_price' => $buyPrice,
            'order_amount' => $orderAmount,
            'result' => $result,
        ));

        /*
         * [늘솜 DB 저장 위치 #2 - 주문 접수 성공 기준]
         * 이 지점에서 늘솜 DB에 INSERT 또는 UPDATE 할 수 있습니다.
         *
         * 저장 후보 컬럼 예시:
         * - ticker            : $ticker
         * - side              : 'BUY'
         * - submit_qty        : $qty
         * - submit_price      : $buyPrice
         * - submit_amount     : $orderAmount
         * - status            : 'SUBMIT_OK'
         * - raw_response_json : json_encode($result, JSON_UNESCAPED_UNICODE)
         * - order_no / odno   : 키움 응답에 주문번호가 있으면 함께 저장
         * - updated_at        : 현재시각
         *
         * 중요:
         * - 현재 neulsom_submit_order() 결과는 '주문 접수 성공'일 수 있으며,
         *   이것만으로 '실제 체결 완료'를 100% 의미하진 않습니다.
         * - 실제 체결가격, 실제 체결수량, 실제 체결금액을 정확히 넣으려면
         *   주문체결조회/체결내역조회 API를 별도로 붙여 후속 UPDATE 하는 것을 권장합니다.
         */
    } catch (Throwable $e) {
        neulsom_log('BUY_SUBMIT_FAIL', array(
            'ticker' => $ticker,
            'name' => $name,
            'qty' => $qty,
            'buy_price' => $buyPrice,
            'error' => $e->getMessage(),
        ));
    }
}
