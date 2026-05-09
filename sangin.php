<?php
//phpinfo();
exit;


// 고도몰 API 접속 정보
$partner_key = 'JUQxNSVEOSU4M28lMTglN0IlRkE='; // 제휴사 인증키
$key = 'OFElQkMlQzMlMjUlMDQlQzQlQTglMEElODMlRTJXZUQtJURCTSU4NCVGMHQlMjclM0RFJUIzJTFCJTExJThEJTA1JTA0Ym53JUU5diU4RiVCM3MlQTAlMTglOEM='; // 쇼핑몰 인증키

// API 요청 URL
$url = 'https://openhub.godo.co.kr/godomall5/board/Board_List.php';

// 페이지 및 사이즈 처리
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$size = isset($_GET['size']) ? (int)$_GET['size'] : 20;

// 요청 파라미터 설정
$data = [
    'partner_key' => $partner_key,
    'key'         => $key,
    // 'bdId'        => 'goodsqa',  // 상품문의 게시판 ID
    'bdId'        => 'qa',  // 상품문의 게시판 ID
    'page'        => $page,
    'size'        => $size
];

// 선택적 파라미터 추가
$optionals = ['dateType', 'startDate', 'endDate', 'searchField', 'searchWord', 'notice', 'reply', 'coment', 'scmNo'];
foreach ($optionals as $o) {
    if (!empty($_GET[$o])) {
        $data[$o] = $_GET[$o];
    }
}


$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query($data),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    // 추가 헤더 설정 - Postman과 유사하게
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: */*',
        'User-Agent: PostmanRuntime/7.28.0' // Postman과 유사한 User-Agent
    ]
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    exit('네트워크 오류: ' . curl_error($ch));
}

curl_close($ch);

try {
    $xml = simplexml_load_string($response);


error_log(print_r($xml,1),0);
    
    if ($xml === false) throw new Exception('XML 파싱 오류');
    
    $code = (string)$xml->header->code;
    $msg  = (string)$xml->header->msg;
    
    echo "응답 코드: {$code}<br>응답 메시지: {$msg}<br>";
    
    if ($code !== '000') {
        $errors = [
            '999' => '인증키가 유효하지 않습니다.',
            '998' => '제휴사키가 유효하지 않습니다.',
            '997' => '사용기간이 맞지 않습니다.',
            '996' => '허용되지 않은 IP입니다.',
            '995' => '접속 권한이 없습니다.'
        ];
        $msg = $errors[$code] ?? $msg;
        exit("오류: {$msg}");
    }
    
    $total = isset($xml->header->total) ? (int)$xml->header->total : 0;
    $maxPage = isset($xml->header->max_page) ? (int)$xml->header->max_page : 1;
    $nowPage = isset($xml->header->now_page) ? (int)$xml->header->now_page : 1;



    echo "총 게시물 수: {$total}<br><br>";
    // 검색 폼 추가
    echo '<div style="margin-bottom:20px;">';
    echo '<form method="get">';
    echo '<table style="width:100%;">';
    echo '<tr>';
    echo '<td>날짜: <select name="dateType">';
    echo '<option value="regDt"' . (!empty($_GET['dateType']) && $_GET['dateType'] == 'regDt' ? ' selected' : '') . '>등록일</option>';
    echo '<option value="updDt"' . (!empty($_GET['dateType']) && $_GET['dateType'] == 'updDt' ? ' selected' : '') . '>수정일</option>';
    echo '</select></td>';
    echo '<td>시작일: <input type="date" name="startDate" value="' . (!empty($_GET['startDate']) ? $_GET['startDate'] : '') . '"></td>';
    echo '<td>종료일: <input type="date" name="endDate" value="' . (!empty($_GET['endDate']) ? $_GET['endDate'] : '') . '"></td>';
    echo '</tr>';
    
    echo '<tr>';
    echo '<td>검색: <select name="searchField">';
    echo '<option value="subject"' . (!empty($_GET['searchField']) && $_GET['searchField'] == 'subject' ? ' selected' : '') . '>제목</option>';
    echo '<option value="contents"' . (!empty($_GET['searchField']) && $_GET['searchField'] == 'contents' ? ' selected' : '') . '>내용</option>';
    echo '<option value="subject_contents"' . (empty($_GET['searchField']) || $_GET['searchField'] == 'subject_contents' ? ' selected' : '') . '>제목+내용</option>';
    echo '</select></td>';
    echo '<td colspan="2"><input type="text" name="searchWord" value="' . (!empty($_GET['searchWord']) ? $_GET['searchWord'] : '') . '" style="width:80%;"></td>';
    echo '</tr>';
    
    echo '<tr>';
    echo '<td>공급사: <input type="text" name="scmNo" value="' . (!empty($_GET['scmNo']) ? $_GET['scmNo'] : '') . '" style="width:50px;"></td>';
    echo '<td>공지글: <select name="notice">';
    echo '<option value=""' . (empty($_GET['notice']) ? ' selected' : '') . '>모두</option>';
    echo '<option value="y"' . (!empty($_GET['notice']) && $_GET['notice'] == 'y' ? ' selected' : '') . '>공지글만</option>';
    echo '<option value="n"' . (!empty($_GET['notice']) && $_GET['notice'] == 'n' ? ' selected' : '') . '>일반글만</option>';
    echo '</select></td>';
    echo '<td>';
    echo '답변포함: <input type="checkbox" name="reply" value="y"' . (!empty($_GET['reply']) ? ' checked' : '') . '>&nbsp;&nbsp;';
    echo '댓글포함: <input type="checkbox" name="coment" value="y"' . (!empty($_GET['coment']) ? ' checked' : '') . '>&nbsp;&nbsp;';
    echo '<input type="submit" value="검색">&nbsp;<input type="button" value="초기화" onclick="window.location.href=\'?\';">';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '</form>';
    echo '</div>';
    
    // 게시물 목록 출력
    echo '<h2>게시물 목록</h2>';
    echo '<table border="1" style="width:100%;border-collapse:collapse;">';
    echo '<tr style="background:#f0f0f0;"><th>번호</th><th>제목</th><th>내용</th><th>작성자</th><th>휴대폰</th><th>이메일</th><th>등록일</th></tr>';



    
    // board_data 사용
    $rows = [];
    if (isset($xml->return->board_data) && count($xml->return->board_data)) {
        $rows = $xml->return->board_data;
    }
    
    if (empty($rows)) {
        // echo '<tr><td colspan="7">조건에 맞는 게시물이 없습니다.</td></tr>';
    } else {

// error_log(print_r($rows,1),0);

        foreach ($rows as $item) {

// error_log(print_r($item,1),0);
// error_log($item->subject,0);
// error_log($item->content,0);


            $isSecret = isset($item->isSecret) && (string)$item->isSecret === 'y';
            $sno    = htmlspecialchars((string)$item->sno);
            $title  = htmlspecialchars((string)$item->subject);
            $title  = $isSecret ? $title . ' <span style="color:red;">[비밀글]</span>' : $title;
            
            // 내용 처리 - 비밀글이어도 내용 표시
            $detail = '';
            if (isset($item->content) && !empty((string)$item->content)) {
                // HTML 내용이 있을 수 있으므로 직접 표시
                $detail = (string)$item->content;
            } else {
                $detail = '-';
            }
            
            $writer = htmlspecialchars((string)$item->writerNm);
            $mobile = htmlspecialchars((string)$item->writerMobile);
            $email  = htmlspecialchars((string)$item->writerEmail);
            $date   = htmlspecialchars((string)$item->regDt);
            
            echo "<tr>";
            echo "<td>{$sno}</td>";
            echo "<td>{$title}</td>";
            echo "<td>{$detail}</td>"; // HTML 내용 직접 표시
            echo "<td>{$writer}</td>";
            echo "<td>{$mobile}</td>";
            echo "<td>{$email}</td>";
            echo "<td>{$date}</td>";
            echo "</tr>";
            
            // 답변 정보가 있는 경우 표시
            if (isset($item->answerSubject) && isset($item->answerContents) && 
                !empty((string)$item->answerSubject) && !empty((string)$item->answerContents)) {
                echo '<tr>';
                echo '<td colspan="7" style="background-color:#f0f8ff; padding:10px;">';
                echo '<strong>답변:</strong><br>';
                echo '<div style="margin-bottom:5px;">';
                echo '<strong>제목:</strong> ' . htmlspecialchars((string)$item->answerSubject) . '<br>';
                echo '<strong>내용:</strong> ' . (string)$item->answerContents; // HTML 내용 직접 표시
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }
            
            // 구분선 추가
            echo '<tr><td colspan="7" style="padding:0; height:1px; background-color:#ddd;"></td></tr>';


        }
    }
    
    echo '</table>';
    
    // 페이지네이션 추가
    if ($maxPage > 1) {
        echo '<div style="text-align:center; margin-top:20px;">';
        
        // 현재 URL에서 페이지 파라미터만 변경하기 위한 준비
        $queryParams = $_GET;
        
        // 페이지 번호 링크
        for ($i = 1; $i <= $maxPage; $i++) {
            $queryParams['page'] = $i;
            if ($i == $nowPage) {
                echo "<strong>[$i]</strong> ";
            } else {
                echo "<a href='?" . http_build_query($queryParams) . "'>$i</a> ";
            }
        }
        
        echo '</div>';
    }
    
} catch (Exception $e) {
    exit('오류 발생: ' . htmlspecialchars($e->getMessage()));
}








// phpinfo();


// $a = "/home/WEBCRM/data/xcally/20220407200213281830.jpg";

// $HTTP_HOST = "crm189.goodcomm.co.kr:8014";
// $DOCUMENT_ROOT = "/home/WEBCRM";


// $cc = str_replace ($DOCUMENT_ROOT, '', $a);
// // echo ($cc);


// //https://crm189.goodcomm.co.kr:8014/data/xcally/20220407195928795107.jpg

// $dd = 'https://'.$HTTP_HOST.$cc ;
// echo ($dd);

/*
$adult_date = date('Ymd', strtotime("-19 years"));
$birth_date = (int)'20040811';

echo "adult_date : {$adult_date} <br />";
echo "birth_date : {$birth_date} <br />";

if ($birth_date >= $adult_date) {
    echo "성인아님";
} else {
    echo "성인";
}
*/


/*
$img = "https://cdn2.ppomppu.co.kr/images/logo.png";
$fp = fopen("/data/sow/nulsom/uploads/logo.png", 'w+');

$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL, $img);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
$contents = curl_exec($ch);
curl_close($ch);

// 가져올 외부이미지 주소
fwrite($fp,$contents);
fclose($fp);
*/

/*
$url = 'http://dev.sowkorea.com/CRM/sample.wav';
$filename = end(explode("/", $url));

// $this->load->helper('file');
$this->load->helper('download');
// $data = file_get_contents($url);

$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
$contents = curl_exec($ch);
curl_close($ch);

error_log($contents, 0);
force_download($filename, $contents);
*/

