<body class="responsive is-mobile">
<div id="to_content"><a href="#container">본문 바로가기</a></div>

<?php
    $g_menu_flag = 'user';
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
        <h1>통신가입증명원관리</h1>
<div class="local_ov01 local_ov">전체목록 <?=number_format($total_rows)?> 건</div>

<?php
    $attributes = array(
        'name' => 'fsearch',
        'id' => 'fsearch',
        'method' => 'get',
        'class' => 'local_sch01 local_sch',
        'onsubmit' => 'return searchList();'
    );
    echo form_open('admuser/cbflist', $attributes);
    $g_placeholder = (!$g_placeholder ? '이름/번호로 검색' : $g_placeholder);
?>
<label for="sfl" class="sound_only">검색대상</label>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>

<select name="sfl" id="sfl" class="frm_input" >
    <option value="userid" <?=($sfl == 'userid' ? 'selected' : '')?>>아이디</option>
</select>

<input type="text" name="stx" value="<?=$stx?>" id="stx" style="width:200px;" required class="required frm_input" />
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
        'name' => 'frmGroupList',
        'id' => 'frmGroupList'
    );
    echo form_open('/admuser/cbflist', $attributes);
?>
<?php
    $data = array(
        'xid' => '',
        'new_memo' => '',
    );
    echo form_hidden($data);
?>
<div class="btn_list01 btn_list">
<?php /* ?>
    <span class="btn_add01 btn_add"><a href="#accountModal" data-toggle="modal">통신가입증명원관리</a></span>
<?php */ ?>
</div>
<div class="tbl_wrap tbl_head01">
    <table>
    <colgroup>
    <col style="width: 10%">
    <col style="width: 20%">
    <col style="width: 20%">
    <col style="width: 20%">
    <col style="width: 30%">
    </colgroup>
    <thead>
    <tr>
        <th scope="col">No.</th>
        <th scope="col">아이디</th>
        <th scope="col">등록일시</th>
        <th scope="col">첨부파일</th>
        <th scope="col">메모</th>
    </tr>
    </thead>
    <tbody>
<?php
    $i = 0;
    foreach ($result as $row) {
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td style="width:30px;text-align:center;">
            <?php /* ?><input type="checkbox" name="chk[]" value="<?=$row->xid?>" id="chk_<?=$row->xid?>">
            <?php */ ?>
            <?=($total_rows--)?>
        </td>
        <td class="td_date"><a href="/admuser/detail/<?=$row->userno?>"><?=$row->userid?></a></td>
        <td class="td_date"><?=$row->reg_time?></td>
        <td class="td_date"><a href="<?=$row->image_path?>" target="_blank">[통신가입증명원 파일보기]</a></td>
        <td style="text-align:left;"><input type="text" name="memo_<?=$row->xid?>" class="frm_input" style="width:250px;" value="<?=$row->memo?>" /> &nbsp;<a href="#" onclick="addMemo('<?=$row->xid?>');"><b>[등록]</b></a></td>
    </tr>
<?php
        $i ++;
    }
?>

            </tbody>
    </table>
</div>
<div class="btn_list01 btn_list">
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

<!-- The Modal Start -->
<?php /* ?>
<div class="modal fade" id="accountModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">발신번호등록</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
    <?php
        $attributes = array(
            'name' => 'fadmin',
            'id' => 'fadmin',
            'onsubmit' => 'return admin_callback_submit(this);'
        );
        echo form_open('admuser/callback', $attributes);
    ?>
                <label class="btn btn-sm">
                <input type="radio" name="rd_cert_type" value="3" /> <span style="font-family:dotum; font-size:12px">어드민등록</span>
                </label>
                <label class="btn btn-sm">
                <input type="radio" name="rd_cert_type" value="2" /> <span style="font-family:dotum; font-size:12px">통신가입증명원</span>
                </label>
                <div class="form-group">
                <input type="text" class="form-control" name="ipt_userid" placeholder="아이디" maxlength="20" style="font-family:dotum; font-size:12px;">
                </div>
                <div class="form-group">
                <input type="text" class="form-control" name="ipt_callback" placeholder="발신번호" maxlength="13" style="font-family:dotum; font-size:12px;">
                </div>
                <div class="form-group">
                <input type="text" class="form-control" name="ipt_name" placeholder="메모" maxlength="50" style="font-family:dotum; font-size:12px;">
                </div>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-danger btn-sm" value="확인" ><span style="font-size:12px"></span>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><span style="font-size:12px">닫기</span></button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php */ ?>
<script type="text/javascript">
var addMemo = function (xid) {
    var new_memo = $("input[name=memo_"+xid+"]").val();
    if (new_memo == '') {
        alert("메모를 입력하세요.");
        return;
    }
    $("input[name=xid]").val(xid);
    $("input[name=new_memo]").val(new_memo);
    $("form#frmGroupList").attr("action", "/admuser/add_memo");
    $("form#frmGroupList").submit();
}
var fmemberlist_submit = function () {
    if (!is_checked("chk[]")) {
        alert("목록을 하나 이상 선택하세요.");
        return false;
    }
    return true;
}
var delete_calllback = function () {
    if (!is_checked("chk[]")) {
        alert("목록을 하나 이상 선택하세요.");
        return false;
    }
    if (!confirm('선택하신 목록을 삭제할가요?')) return false;
    $("#frmadmin").attr("action","/admuser/cb_delete").submit();
}
var admin_callback_submit = function () {
    if ($("input:radio[name=rd_cert_type]").is(':checked') == false) {
        alert("등록 방식을 선택하세요.");
        return false;
    }
    var ipt_userid = $.trim($("input[name=ipt_userid]").val());
    if (ipt_userid == '') {
        alert("아이디를 입력하세요.");
        return false;
    }
    var ipt_callback = $.trim($("input[name=ipt_callback]").val());
    if (ipt_callback == '') {
        alert("발신번호를 입력하세요.");
        return false;
    }
    var regType = /^[0-9-]*$/;
    if (!regType.test(ipt_callback)) {
        alert("발신번호는 숫자만 입력하세요.");
        return false;
    }
    var ipt_name = $.trim($("input[name=ipt_name]").val());
    if (ipt_name == '') {
        alert("메모를 입력하세요.");
        return false;
    }
    return true;
}
</script>