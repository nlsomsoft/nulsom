<body class="responsive is-mobile">
<div id="to_content"><a href="#container">본문 바로가기</a></div>

<?php
    $g_menu_flag = 'setting';
    include_once(VIEWPATH.'/templates/adm_menu.php');
?>

<div id="wrapper">
    <div id="container">
        <div id="text_size">
            <!-- font_resize('엘리먼트id', '제거할 class', '추가할 class'); -->
            <button onclick="font_resize('container', 'ts_up ts_up2', '');"><img src="/images/adm/ts01.gif" alt="기본"></button>
            <button onclick="font_resize('container', 'ts_up ts_up2', 'ts_up');"><img src="/images/adm/ts02.gif" alt="크게"></button>
            <button onclick="font_resize('container', 'ts_up ts_up2', 'ts_up2');"><img src="/images/adm/ts03.gif" alt="더크게"></button>
        </div>
        <h1>신고건 조회</h1>
<div class="local_ov01 local_ov">&nbsp;</div>

<?php
    $attributes = array(
        'name' => 'fsearch',
        'id' => 'fsearch',
        'method' => 'get',
        'class' => 'local_sch01 local_sch',
        'onsubmit' => 'return searchList();'
    );
    echo form_open('admsetting/report', $attributes);

?>
<label for="sfl" class="sound_only">검색대상</label>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>

<select name="sfl" id="sfl" class="frm_input" >
    <option value="callback" <?=($sfl == 'callback' ? 'selected' : '')?>>발신번호</option>
</select>

<input type="text" name="stx" value="<?=$stx?>" id="stx" style="width:200px;" class="required frm_input" />
<input type="submit" class="btn_submit" value="검색">
<script type="text/javascript">
var searchList = function() {
    if ($("input[name=stx]").val().trim() == '') return false;
    return true;
}
</script>
</form>

<?php /* ?>
<div class="btn_add01 btn_add">
    <button id="myBtn">발신번호등록하기</button>
</div>
<?php */ ?>

<?php
$attributes = array(
    'name' => 'frmadmin',
    'id' => 'frmadmin',
    'onsubmit' => 'return fmemberlist_submit(this);'
);
echo form_open('admuser/cb_auth', $attributes);
?>

<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    <tr>

        <th scope="col">아이디</th>
        <th scope="col">이름</th>
        <th scope="col">본인인증핸드폰</th>
        <th scope="col">가입일자</th>
        <th scope="col">마지막접속일자</th>
        <th scope="col">등록전화번호개수</th>
        <th scope="col">계정접속IP</th>
        <th scope="col">해당계정문자발신건수</th>
        <th scope="col">변작확인방법</th>
        <th scope="col">계정중지여부(O/X)</th>
    </tr>
    </thead>
    <tbody>
<?php
    $i = 0;
    for ($k = 0; $k < count($report_info); $k++) {
        $cert_type = (int)$report_info[$i]['cert_type'];
        if ($cert_type == '1') $info_cert_type = '가입자전화확인';
        else if ($cert_type == '2') $info_cert_type = '서류확인';
        else if ($cert_type == '3') $info_cert_type = '관리자등록';

        if ($report_info[$i]['state'] == '0') {
            $info_state = 'O';
        } else {
            $info_state = 'X';
        }
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td class="td_date"><?=$report_info[$k]['userid']?></td>
        <td class="td_date"><?=$report_info[$k]['username']?></td>
        <td class="td_date"><?=phone_format($report_info[$k]['phone_no'])?></td>
        <td class="td_date"><?=$report_info[$k]['add_date']?></td>
        <td class="td_date"><?=$report_info[$k]['login_date']?></td>
        <td class="td_date"><?=number_format($report_info[$k]['callback_cnt'])?></td>
        <td class="td_date"><?=($report_info[$k]['ip'] != '' ? $report_info[$k]['ip'] : $report_info[$k]['register_ip'])?></td>
        <td class="td_date"><?=number_format($report_info[$k]['total_sum'])?></td>
        <td class="td_date"><?=$info_cert_type?></td>
        <td class="td_date"><?=$info_state?></td>
    </tr>
<?php
    }
?>
            </tbody>
    </table>
</div>


</form>
        <noscript>
            <p>
                귀하께서 사용하시는 브라우저는 현재 <strong>자바스크립트를 사용하지 않음</strong>으로 설정되어 있습니다.<br>
                <strong>자바스크립트를 사용하지 않음</strong>으로 설정하신 경우는 수정이나 삭제시 별도의 경고창이 나오지 않으므로 이점 주의하시기 바랍니다.
            </p>
        </noscript>

    </div>
</div>

<!-- <p>실행시간 : 0.00087904930114746 -->

</body>
<script type="text/javascript">
var searchList = function() {
    if ($("input[name=ipt_date_from]").val().trim() == '') return false;
    if ($("input[name=ipt_date_to]").val().trim() == '') return false;
    return true;
}
</script>
