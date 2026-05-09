<?php
// define filename
$file_name = $_SERVER['PHP_SELF'];
$log_name = $_SERVER['STORENAME'];

//error_log(print_r($_SERVER,1),0);
if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
    error_log('['.$log_name.'][E]...[REMOTE_ADDR]['.$file_name.']', 0);
    exit;
}

// define database
define('BASEPATH', $_SERVER['DOCUMENT_ROOT']);
define('ENVIRONMENT', '');
include_once(BASEPATH.'/application/config/database.php');
// $servername = 'localhost';
$servername = $db['default']['hostname'];
$username	= $db['default']['username'];
$password	= $db['default']['password'];
$database	= $db['default']['database'];
// define database

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
	error_log('['.$log_name.'][E]...[CONNECT DB]['.$file_name.']', 0);
	exit;
}
// error_log('['.$log_name.'][S]...['.$file_name.']', 0);


include_once(BASEPATH.'/application/helpers/lotto_helper.php');
$cur_divide = (int)lotto_current_divide_num();
$divide = (int)($cur_divide - 1);

$sql = "SELECT * FROM lotto_winning_num WHERE divide = '{$divide}' ORDER BY xid DESC LIMIT 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
mysqli_free_result($result);
$xid = (int)$row['xid'];
$num = trim($row['num']);
$add_date = trim($row['add_date']);
if ($xid) exit;


$url = "https://www.dhlottery.co.kr/common.do?method=getLottoNumber&drwNo={$divide}";

$ch = curl_init();                                 //curl 초기화
curl_setopt($ch, CURLOPT_URL, $url);               //URL 지정하기
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환 
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);      //connection timeout 10초 
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
 
$response = curl_exec($ch);
curl_close($ch);


$lotto_object = '';
$lotto_object = json_decode($response);

// error_log(print_r($lotto_object,1),0);

if ($lotto_object->returnValue != 'success') exit;


$lotto_object->drwNoDate; //"2024-08-17",
$lotto_object->drwNo;     //1133,
$lotto_object->drwtNo1;   //13
$lotto_object->drwtNo2;   //14,
$lotto_object->drwtNo3;   //20,
$lotto_object->drwtNo4;   //28,
$lotto_object->drwtNo5;   //29,
$lotto_object->drwtNo6;   //34,
$lotto_object->bnusNo;    //23,

$drawing_date = trim($lotto_object->drwNoDate);
$divide = (int)$lotto_object->drwNo;
$num = "{$lotto_object->drwtNo1},{$lotto_object->drwtNo2},{$lotto_object->drwtNo3},{$lotto_object->drwtNo4},{$lotto_object->drwtNo5},{$lotto_object->drwtNo6}|{$lotto_object->bnusNo}";

$sql = "
	INSERT INTO lotto_winning_num
	SET num = '{$num}',
		divide = {$divide},
		drawing_date = '{$drawing_date}'
";
if (!mysqli_query($conn, $sql)) {
    error_log('['.$log_name.'][E]...['.$sql.']['.$file_name.']', 0);
	mysqli_rollback($conn);
	mysqli_close($conn);
	exit;
}

mysqli_close($conn);
exit;
