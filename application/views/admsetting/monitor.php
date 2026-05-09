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
        <h1>모니터링</h1>
<div class="local_ov01 local_ov">&nbsp;</div>

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
echo form_open('admsetting/monitor', $attributes);
?>

<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    <tr>
        <th scope="col">테이블명</th>
        <th scope="col">row 개수</th>
        <th scope="col">비고</th>
    </tr>
    </thead>
    <tbody>
<?php
    foreach ($monitor_info1 as $tname) {
        $detail_desc = '';
        if ($tname == 'sow_send_data') $detail_desc = '&nbsp;(Queue)';
        $rows = (int)$monitor_info2[$tname];

        // $bold_desc = 0;
        // if (strpos($tname, 'result') !== false || $tname == 'sow_send_data' || $tname == 'sow_pu_msgdata') {
        //     $bold_desc = 1;
        // }
        $bold_desc = 0;
        if ($tname == 'result_'.MAX_RESULT_CNT || $tname == 'result_0') {
            $bold_desc = 1;
        }
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td class="td_date"><?php if ($bold_desc) { ?><span style="font-weight:bold;"><?php } ?><?=$tname?> <?=$detail_desc?><?php if ($bold_desc) { ?></span><?php } ?></td>
        <td class="td_date"><?php if ($bold_desc) { ?><span style="font-weight:bold;"><?php } ?><?=number_format($rows)?><?php if ($bold_desc) { ?></span><?php } ?></td>
        <td class="td_date"><?=($rows > 1000000 ? '<span style="color:#e8180c">주의</span>' : '<span style="color:#396dba">양호</span>')?></td>
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
