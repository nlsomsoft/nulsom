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
        <h1>결산 (입금)</h1>
<div class="local_ov01 local_ov">&nbsp;</div>

<?php
$attributes = array(
    'name' => 'frmadmin',
    'id' => 'frmadmin',
    'onsubmit' => 'return fmemberlist_submit(this);'
);
echo form_open('admsettle/deposit', $attributes);
?>

<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    </thead>
    <tbody>
        <tr>
            <td style="width:200px; text-align:center;background: #e5ecef;font-weight:bold;">승인할 금액</td>
            <td><input type="text" name="auth_amount" id="auth_amount" value=""  class="form-control" style="font-family:dotum; font-size:12px;" minlength="2" maxlength="8" required="" placeholder="* 승인할 금액[숫자만] (필수)" size="3" /></td>
            <td style="text-align:left;"><input type="submit" class="btn btn-danger btn-sm" value="등록하기"></td>
        </tr>
    </tbody>
    </table>
</div>
</form>


<?php
    $attributes = array(
        'name' => 'frmAdmsettle',
        'id' => 'frmAdmsettle'
    );
    echo form_open('/admsettle/deposit_form', $attributes);
?>
<div class="tbl_wrap tbl_head01">
    <div>입금내역</div>
    <table>
    <thead>
    <tr>
        <th scope="col">No</th>
        <th scope="col">금액</th>
        <th scope="col">승인날짜</th>
    </tr>
    </thead>
    <tbody>
<?php
    $i = 0;
    $total_amount = 0;
    foreach ($result as $row) {
        $total_amount += $row->amount;
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td class="td_num"><?=number_format($i+1)?></td>
        <td class="td_num"><?=number_format($row->amount)?> 원</td>
        <td class="td_num"><?=$row->reg_time?></td>
    </tr>
<?php
        $i ++;
    }
?>
    <tr class="bg1">
        <td class="td_num">총 합계</td>
        <td class="td_num"><b><?=number_format($total_amount)?> 원</b></td>
        <td class="td_num">&nbsp;</td>
    </tr>
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
var fmemberlist_submit = function () {
    if ($('#noti_mobile').val() == '') {
        alert("핸드폰 번호를 입력하세요.");
        return false;
    }
    return true;
}
</script>