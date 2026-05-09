<?php
// define filename
$file_name = $_SERVER['PHP_SELF'];
$log_name = $_SERVER['STORENAME'];

//error_log(print_r($_SERVER,1),0);
if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
    error_log('['.$log_name.'][E]...[REMOTE_ADDR]['.$file_name.']', 0);
    exit;
}

define('BASEPATH', $_SERVER['DOCUMENT_ROOT']);
define('ENVIRONMENT', '');
include_once(BASEPATH.'/application/config/database.php');
// $servername = 'localhost';
$servername = $db['default']['hostname'];
$username   = $db['default']['username'];
$password   = $db['default']['password'];
$database   = $db['default']['database'];

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
    error_log('['.$log_name.'][E]...[CONNECT DB]['.$file_name.']', 0);
    exit;
}
// error_log('Connected successfully', 0);
// error_log('['.$log_name.'][S]...['.$file_name.']', 0);


$dest = '';
// $dest = '?dest=2021-11-15';
$company['aone1000'] = 'http://cron.aone1000.com/crontab/put_sending_stat_daily.php';
$company['caca5'] = 'http://cron.caca5.com/crontab/put_sending_stat_daily.php';
$company['dasso'] = 'http://dasso.net/crontab/put_sending_stat_daily.php';
$company['unisms'] = 'http://cron.unisms.co.kr/crontab/put_sending_stat_daily.php';
//$company['mjmj82'] = 'http://cron.mjmj82.com/crontab/put_sending_stat_daily.php';
//$company['nowsms'] = 'http://cron.nowsms.co.kr/crontab/put_sending_stat_daily.php';
//$company['munjaplus'] = 'http://cron.munjaplus.com/crontab/put_sending_stat_daily.php';
//$company['kopo365'] = 'http://cron.kopo365.com/crontab/put_sending_stat_daily.php';
// $company['bonaego82'] = 'http://cron.bonaego82.com/crontab/put_sending_stat_daily.php';
// $company['holeinone'] = 'http://cron.xn--z92b15snoa8c476f.com/crontab/put_sending_stat_daily.php';
// $company['sms8279'] = 'http://cron.sms8279.com/crontab/put_sending_stat_daily.php';
// $company['tnt-sms'] = 'http://cron.tnt-sms.com/crontab/put_sending_stat_daily.php';
// $company['munjabest'] = 'http://www.munjabest.com/crontab/put_sending_stat_daily.php';
// $company['dam-sms'] = 'http://cron.dam-sms.com/crontab/put_sending_stat_daily.php';
// $company['soleyo'] = 'http://cron.xn--hj2bx6um5b.com/crontab/put_sending_stat_daily.php';
// $company['forreos'] = 'http://cron.forreos.com//crontab/put_sending_stat_daily.php';
// $company['rocket79'] = 'http://cron.rocket79.com/crontab/put_sending_stat_daily.php';


$rcv_data = array();
foreach ($company as $key => $val) {
    // error_log('val :'.$val, 0);
    if (!$val) continue;
    if ($dest) $val .= $dest;

    $res = getWebPage($val);
    // error_log($res, 0);
    // error_log('-----------------------', 0);
    $rcv_data[$key] = $res;
}

// error_log(print_r($rcv_data,1),0);
// exit;



mysqli_autocommit($conn, FALSE);

foreach ($rcv_data as $key => $val) {
    if (!$val) continue;

    $company = '';
    $yyyymmdd = date('Y-m-d');
    $total = 0;
    $succ = 0;
    $fail = 0;
    $remain = 0;
    $col_cnt = 0;

    $company = $key;
    $sval = explode('>', $val);
    $col_cnt = (int)$sval[0];

// error_log($company, 0);

    if (!$sval[1]) {
        $sql = "
            INSERT INTO company_stat
            SET company = '{$company}',
                yyyymmdd = '{$yyyymmdd}',
                total = '{$total}',
                succ = '{$succ}',
                fail = '{$fail}',
                remain = '{$remain}',
                col_cnt = '{$col_cnt}'
            ON DUPLICATE KEY
            UPDATE
                total = '{$total}',
                succ = '{$succ}',
                fail = '{$fail}',
                remain = '{$remain}',
                col_cnt = '{$col_cnt}'
        ";
        if (!mysqli_query($conn, $sql)) {
            error_log('['.$log_name.'][E1]...['.$sql.']['.$file_name.']', 0);
            mysqli_rollback($conn);
            mysqli_close($conn);
            exit;
        }
    }
    else
    {
        $data = array();
        $data = explode(':', $sval[1]);
// error_log(print_r($data,1),0);
        if (isset($data[0])) {
            $info1 = explode('|', $data[0]);
            $yyyymmdd = $info1[0];
            $total = (int)$info1[1];
            $succ = (int)$info1[2];
            $fail = (int)$info1[3];
            $remain = (int)$info1[4];

            $sql = "
                INSERT INTO company_stat
                SET company = '{$company}',
                    yyyymmdd = '{$yyyymmdd}',
                    total = '{$total}',
                    succ = '{$succ}',
                    fail = '{$fail}',
                    remain = '{$remain}',
                    col_cnt = '{$col_cnt}'
                ON DUPLICATE KEY
                UPDATE
                    total = '{$total}',
                    succ = '{$succ}',
                    fail = '{$fail}',
                    remain = '{$remain}',
                    col_cnt = '{$col_cnt}'
            ";
            if (!mysqli_query($conn, $sql)) {
                error_log('['.$log_name.'][E2]...['.$sql.']['.$file_name.']', 0);
                mysqli_rollback($conn);
                mysqli_close($conn);
                exit;
            }
        }
        if (isset($data[1])) {
            $info2 = explode('|', $data[1]);
            $yyyymmdd = $info2[0];
            $total = (int)$info2[1];
            $succ = (int)$info2[2];
            $fail = (int)$info2[3];
            $remain = (int)$info2[4];

            $sql = "
                INSERT INTO company_stat
                SET company = '{$company}',
                    yyyymmdd = '{$yyyymmdd}',
                    total = '{$total}',
                    succ = '{$succ}',
                    fail = '{$fail}',
                    remain = '{$remain}',
                    col_cnt = '{$col_cnt}'
                ON DUPLICATE KEY
                UPDATE
                    total = '{$total}',
                    succ = '{$succ}',
                    fail = '{$fail}',
                    remain = '{$remain}',
                    col_cnt = '{$col_cnt}'
            ";
            if (!mysqli_query($conn, $sql)) {
                error_log('['.$log_name.'][E3]...['.$sql.']['.$file_name.']', 0);
                mysqli_rollback($conn);
                mysqli_close($conn);
                exit;
            }
        }
    }
}

mysqli_commit($conn);
mysqli_close($conn);
// error_log('['.$log_name.'][F]...['.$file_name.']', 0);


function getWebPage($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);
     
    return $res;
}
exit;
