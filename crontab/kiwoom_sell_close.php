<?php

declare(strict_types=1);

require_once __DIR__ . '/kiwoom_trade_lib.php';

$cfg = neulsom_require_secret_config();
neulsom_bootstrap_kiwoom_env($cfg);

$accountNo = isset($_ENV['KIWOOM_ACCOUNT_NO']) ? (string)$_ENV['KIWOOM_ACCOUNT_NO'] : '';
$accountProductCode = isset($_ENV['KIWOOM_ACCOUNT_PRODUCT_CODE']) ? (string)$_ENV['KIWOOM_ACCOUNT_PRODUCT_CODE'] : '';
$dryRun = isset($_ENV['KIWOOM_DRY_RUN']) ? ($_ENV['KIWOOM_DRY_RUN'] === '1') : true;

/*
 * ----------------------------------------------------------------
 * DB 조회부는 신대표님 서버 구조에 맞춰 직접 연결하실 영역입니다.
 *
 * 목적:
 * - 늘솜서버 DB에 저장된 '오늘 매수된 종목' 또는 '미청산 보유 종목'을 읽어서
 *   종목코드만으로 전량매도(sell_all=true) 하도록 연결하는 자리입니다.
 *
 * 기대 배열 구조 예시:
 * $positions = array(
 *   array('ticker' => '005930', 'sell_all' => true, 'name' => '삼성전자'),
 *   array('ticker' => '000660', 'sell_all' => true, 'name' => 'SK하이닉스'),
 * );
 *
 * 예시 쿼리 아이디어(주석 예시, 실제 실행은 신대표님 서버 DB 구조에 맞춰 구현):
 *
 * 1) 오늘 실제 매수 완료된 종목을 전량매도 대상으로 읽는 경우
 *    SELECT ticker, name
 *    FROM trade_execution_log
 *    WHERE trading_date = CURDATE()
 *      AND side = 'BUY'
 *      AND status IN ('SUBMIT_OK', 'FILLED', 'PARTIAL')
 *    GROUP BY ticker, name;
 *
 * 2) 아직 미청산인 보유 종목만 읽는 경우
 *    SELECT ticker, name
 *    FROM trade_execution_log
 *    WHERE sell_completed = 0
 *    GROUP BY ticker, name;
 *
 * 위 결과를 아래처럼 매핑:
 *   $positions[] = array(
 *     'ticker' => $row['ticker'],
 *     'name' => $row['name'],
 *     'sell_all' => true,
 *   );
 * ----------------------------------------------------------------
 */
$positions = array();

$positions = array(
    array('ticker' => '005930', 'sell_all' => false, 'qty' => 5, 'name' => '삼성전자'),
    array('ticker' => '000660', 'sell_all' => false, 'qty' => 5, 'name' => 'SK하이닉스'),
);


if (!$positions) {
    echo "[SELL] no positions (DB query area is intentionally commented out; fill ticker/qty manually)" . PHP_EOL;
    exit(0);
}

foreach ($positions as $row) {
    $ticker = isset($row['ticker']) ? trim((string)$row['ticker']) : '';
    $qty = isset($row['qty']) ? (int)$row['qty'] : 0;
    $sellAll = isset($row['sell_all']) ? (bool)$row['sell_all'] : false;
    $name = isset($row['name']) ? (string)$row['name'] : '';

    if ($ticker === '') {
        neulsom_log('SELL_SKIP', array(
            'ticker' => $ticker,
            'name' => $name,
            'reason' => 'missing_ticker',
        ));
        continue;
    }

    if ($sellAll && $qty <= 0) {
        try {
            $qty = neulsom_get_holding_qty($ticker, $accountNo, $accountProductCode);
            neulsom_log('SELL_HOLDING_LOOKUP', array(
                'ticker' => $ticker,
                'name' => $name,
                'sell_all' => true,
                'resolved_qty' => $qty,
            ));
        } catch (Throwable $e) {
            neulsom_log('SELL_SKIP', array(
                'ticker' => $ticker,
                'name' => $name,
                'reason' => 'holding_lookup_failed',
                'error' => $e->getMessage(),
            ));
            continue;
        }
    }

    if ($qty <= 0) {
        neulsom_log('SELL_SKIP', array(
            'ticker' => $ticker,
            'name' => $name,
            'reason' => 'missing_ticker_or_qty',
        ));
        continue;
    }

    // 시장가 전량매도
    $payload = neulsom_build_cash_order_payload($ticker, $qty, 0, 'SELL', true);
    neulsom_log('SELL_PLAN', array(
        'ticker' => $ticker,
        'name' => $name,
        'qty' => $qty,
        'sell_all' => $sellAll,
        'dry_run' => $dryRun,
        'account_no' => $accountNo,
        'account_product_code' => $accountProductCode,
        'payload' => $payload,
    ));

    /*
     * [늘솜 DB 반영 위치 #1 - 매도 계획 기준]
     * 필요하면 이 지점에서 '전량매도 계획 생성' 로그를 DB에 남길 수 있습니다.
     *
     * 저장 후보 컬럼 예시:
     * - trading_date : 오늘 날짜
     * - ticker       : $ticker
     * - name         : $name
     * - side         : 'SELL'
     * - order_qty    : $qty
     * - sell_all     : $sellAll ? 1 : 0
     * - status       : 'PLAN'
     * - created_at   : 현재시각
     */

    if ($dryRun) {
        continue;
    }
try {
        $result = neulsom_submit_order($payload);
        neulsom_log('SELL_SUBMIT_OK', array(
            'ticker' => $ticker,
            'name' => $name,
            'qty' => $qty,
            'sell_all' => $sellAll,
            'result' => $result,
        ));

        /*
         * [늘솜 DB 반영 위치 #2 - 매도 주문 접수 성공 기준]
         * 필요하면 이 지점에서 매도 주문 접수 성공을 DB에 INSERT/UPDATE 할 수 있습니다.
         *
         * 저장 후보 컬럼 예시:
         * - ticker            : $ticker
         * - side              : 'SELL'
         * - submit_qty        : $qty
         * - sell_all          : $sellAll ? 1 : 0
         * - status            : 'SUBMIT_OK'
         * - raw_response_json : json_encode($result, JSON_UNESCAPED_UNICODE)
         * - order_no / odno   : 키움 응답에 주문번호가 있으면 함께 저장
         * - updated_at        : 현재시각
         *
         * 중요:
         * - 현재는 시장가 매도 주문 '접수 성공' 기준입니다.
         * - 실제 매도 체결단가/체결금액/체결수량을 정확히 넣으려면
         *   주문체결조회/체결내역조회 API를 별도로 붙여 후속 UPDATE 하는 것이 맞습니다.
         */
    } catch (Throwable $e) {
        neulsom_log('SELL_SUBMIT_FAIL', array(
            'ticker' => $ticker,
            'name' => $name,
            'qty' => $qty,
            'sell_all' => $sellAll,
            'error' => $e->getMessage(),
        ));
    }
}
