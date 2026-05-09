<body class="responsive is-mobile">
<div id="to_content"><a href="#container">본문 바로가기</a></div>

<?php
    $g_menu_flag = 'bill';
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
        <h1>사용료 (잔액)</h1>
<div class="local_ov01 local_ov">전체목록 <?=number_format($total_rows)?> 건</div>

<?php
$attributes = array(
    'name' => 'frmadmin',
    'id' => 'frmadmin',
    'onsubmit' => 'return fmemberlist_submit(this);'
);
echo form_open('admbill/adm_balance', $attributes);
?>

<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    <tr>
        <th scope="col">No</th>
        <th scope="col">상점아이디</th>
        <th scope="col">잔액금액</th>
        <th scope="col">기준일자</th>
<?php if ((int)$this->session->userdata('level') > 5) { ?>
        <th scope="col">잔액부족 시</th>
<?php } ?>
    </tr>
    </thead>
    <tbody>
<?php
    $seq = (int)($offset + 1);
    $i = 0;
    foreach ($result as $row) {
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td class="td_num"><?=($seq++)?></td>
        <td class="td_num"><?=$row->storename?></td>
        <td class="td_num"><?=number_format($row->balance,2)?></td>
        <td class="td_num"><?=mydate_format('Y-m-d H:i',$row->check_time).':00';?> 일자 기준</td>
<?php if ((int)$this->session->userdata('level') > 5) { ?>
        <td class="td_num"><?=($row->restrict_sending == '1' ? '<span style="color:#ff5959">발송불가</span>' : '발송가능')?></td>
<?php } ?>
    </tr>
<?php
        $i ++;
    }
?>

            </tbody>
    </table>
</div>

<div><?=$this->pagination->create_links();?></div>

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
