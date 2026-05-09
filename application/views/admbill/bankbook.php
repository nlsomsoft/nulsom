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
        <h1>무통장관리</h1>
<div class="local_ov01 local_ov">전체목록 <?=number_format($total_rows)?> 건</div>

<?php
    $attributes = array(
        'name' => 'fsearch',
        'id' => 'fsearch',
        'method' => 'get',
        'class' => 'local_sch01 local_sch',
        'onsubmit' => 'return searchList();'
    );
    echo form_open('admbill/bankbook', $attributes);
    $g_placeholder = (!$g_placeholder ? '아이디/이름 검색' : $g_placeholder);
?>
<label for="sfl" class="sound_only">검색대상</label>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?=$stx?>" id="stx" style="width:200px;" required class="required frm_input" placeholder=" 아이디/이름 검색" />
<input type="submit" class="btn_submit" value="검색">
<script type="text/javascript">
var searchList = function() {
    if ($("input[name=stx]").val().trim() == '') return false;
    return true;
}
</script>
</form>

<?php
$attributes = array(
    'name' => 'frmadmin',
    'id' => 'frmadmin',
    'onsubmit' => 'return fmemberlist_submit(this);'
);
echo form_open('admbill/bank_auth', $attributes);
?>

<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="승인하기" onclick="document.pressed=this.value">
    <input type="submit" name="act_button" value="삭제하기" onclick="document.pressed=this.value">
</div>
<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    <tr>
        <th scope="col">선택</th>
        <th scope="col">아이디</th>
<?php if (0 && GROUP_USE_YN == 'Y') { ?>
    <?php if (0 && (int)$this->session->userdata('level') == 9) { ?>
        <th scope="col">상점</th>
    <?php } ?>
    <?php if ((int)$this->session->userdata('level') >= 5) { ?>
        <th scope="col">그룹</th>
    <?php } ?>
<?php } ?>
        <th scope="col">입금자명</th>
        <th scope="col">결제금액</th>
        <th scope="col">상태</th>
        <th scope="col">IP(승인)</th>
        <th scope="col">승인일시</th>
        <th scope="col">등록일시</th>
    </tr>
    </thead>
    <tbody>
<?php
    $i = 0;
    foreach ($result as $row) {
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td style="width:30px; text-align:center;"><?php if ($row->proc == 'N') { ?><input type="radio" name="rd_xid" value="<?=$row->xid?>" /><?php } ?></td>
        <td class="td_date"><a href="/admuser/detail/<?=$row->userno?>"><?=$row->userid?></a></td>
<?php if (0 && GROUP_USE_YN == 'Y') { ?>
    <?php if (0 && (int)$this->session->userdata('level') == 9) { ?>
        <td class="td_date"><?=$row->storename?></td>
    <?php } ?>
    <?php if ((int)$this->session->userdata('level') >= 5) { ?>
        <td class="td_date"><?=$row->groupid?></td>
    <?php } ?>
<?php } ?>
        <td class="td_date"><?=$row->deposit_name?></td>
        <td class="td_date"><?=number_format($row->amount)?> 원</td>
        <td class="td_date"><?=($row->proc == 'N' ? '<span style="color:#e8180c;">대기</span>' : '승인')?></td>
        <td class="td_date"><?=$row->ip?></td>
        <td class="td_date"><?=($row->proc == 'N' ? '' : $row->auth_time)?></td>
        <td class="td_date"><?=$row->reg_time?></td>
    </tr>
<?php
        $i ++;
    }
?>

            </tbody>
    </table>
</div>
<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="승인하기" onclick="document.pressed=this.value">
    <input type="submit" name="act_button" value="삭제하기" onclick="document.pressed=this.value">
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

<script type="text/javascript">
var fmemberlist_submit = function () {
    if ($('input:radio[name=rd_xid]').is(':checked') == false) {
        alert("목록을 선택하세요.");
        return false;
    }
    if (document.pressed == "삭제하기") {
        $("#frmadmin").attr("action", "/admbill/bank_delete");
    }
    return true;
}
</script>