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
        <h1>080 차단번호 관리</h1>
<div class="local_ov01 local_ov">전체목록 <?=number_format($total_rows)?> 건</div>

<?php
    $attributes = array(
        'name' => 'fsearch',
        'id' => 'fsearch',
        'method' => 'get',
        'class' => 'local_sch01 local_sch',
        'onsubmit' => 'return searchList();'
    );
    echo form_open('admuser/phone080', $attributes);
?>
<label for="sfl" class="sound_only">검색대상</label>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?=$stx?>" id="stx" style="width:200px;" required class="required frm_input" placeholder="차단번호 검색" />
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
echo form_open('admuser/phone080_delete', $attributes);
?>
<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="삭제하기" onclick="document.pressed=this.value">
    <?php if ((int)$this->session->userdata('level') >= 5) { ?>
    <span class="btn_add01 btn_add">
        <a href="#userModal" data-toggle="modal">차단번호 등록하기</a>
    </span>
    <?php } ?>
</div>
<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    <tr>
        <th scope="col"><input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)"></th>
        <th scope="col">차단번호</th>
        <th scope="col">080번호</th>
        <th scope="col">등록일시</th>
        <th scope="col" style="width:15%;">비고</th>
    </tr>
    </thead>
    <tbody>
<?php
    $i = 0;
    foreach ($result as $row) {
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td style="width:20px;text-align:center;">
        <?php if ($row->state != '2') { ?>
            <input type="checkbox" name="chk[]" value="<?=$row->xid?>" id="chk_<?=$row->xid?>">
        <?php } ?>
        </td>
        <td class="td_date"><?=phone_format($row->mobile)?></td>
        <td class="td_date"><?=phone_format($row->phone_080)?></td>
        <td class="td_date">
        <?php if ($row->reg_time == '0000-00-00 00:00:00') { ?>
            <?='관리자 등록'?>
        <?php } else { ?>
            <?=mydate_format('Y-m-d H:i',$row->reg_time)?>
        <?php } ?>
        </td>
        <td class="td_date"><?=($row->state == '2' ? '삭제' : '')?></td>
    </tr>
<?php
        $i ++;
    }
?>

            </tbody>
    </table>
</div>
<div class="btn_list01 btn_list">
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

<!-- The Modal Start -->
<div class="modal fade" id="userModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">차단번호 등록</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

    <?php
        $attributes = array(
            'name' => 'fadminphone',
            'id' => 'fadminphone',
            'onsubmit' => 'return admin_add_phone080_submit(this);'
        );
        echo form_open('admuser/add_phone080', $attributes);
    ?>
                <div class="form-group">
    <?php
        $data = array(
            'name'  => 'ipt_mobile',
            'id'    => 'ipt_mobile',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;',
            'minlength' => '9',
            'maxlength' => '15',
            'value' => '',
            'placeholder' => ' 차단번호 (필수)'
        );
        echo form_input($data);
    ?>
        <select name="ipt_phone_080" id="ipt_phone_080" style="height:35px;width:100%;margin-top:5px;">
            <option value="-1">080 번호를 선택하세요.</option>
        <?php
            global $PHONE_080_LIST;
            foreach ($PHONE_080_LIST as $key => $val) {
                echo "<option value='{$key}'>{$val}</option>";
            }
        ?>
        </select>
                </div>

            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-danger btn-sm" value="등록하기" ><span style="font-size:12px"></span>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><span style="font-size:12px">닫기</span></button>
            </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
var admin_add_phone080_submit = function () {
    var ipt_mobile = $.trim($("input[name=ipt_mobile]").val());
    if (ipt_mobile == '') {
        alert("차단번호를 입력하세요.");
        return false;
    }
    var regType = /^[0-9-]*$/;
    if (!regType.test(ipt_mobile)) {
        alert("차단번호는 숫자만 입력하세요.");
        return false;
    }
    var sel_val = $("#ipt_phone_080 option:selected").val();
    if (sel_val == '-1') {
        alert("080번호를 선택하세요.");
        return false;
    }
    return true;
}
var fmemberlist_submit = function () {
    if (!is_checked("chk[]")) {
        alert("목록을 하나 이상 선택하세요.");
        return false;
    }
    return true;
}
</script>