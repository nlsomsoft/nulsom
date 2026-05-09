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
        <h1>차단/탈퇴관리</h1>

<div class="local_ov01 local_ov">
    총회원수 <?=number_format($total_rows)?>명 중 <a href="#">차단 <?=number_format($total_rows1)?></a>명, <a href="#">탈퇴 <?=number_format($total_rows2)?></a>명
</div>

<?php
    $attributes = array(
        'name' => 'fsearch',
        'id' => 'fsearch',
        'method' => 'get',
        'class' => 'local_sch01 local_sch',
        'onsubmit' => 'return searchList();'
    );
    echo form_open('admuser/block_users', $attributes);
    $g_placeholder = (!$g_placeholder ? '이름/번호로 검색' : $g_placeholder);
?>

<label for="sfl" class="sound_only">검색대상</label>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<select name="sfl" id="sfl" class="frm_input" >
    <option value="userid" <?=($sfl == 'userid' ? 'selected' : '')?>>아이디</option>
    <option value="realname" <?=($sfl == 'realname' ? 'selected' : '')?>>이름</option>
    <option value="mobile" <?=($sfl == 'mobile' ? 'selected' : '')?>>인증번호</option>
    <option value="birth_day" <?=($sfl == 'birth_day' ? 'selected' : '')?>>생년월일</option>
</select>
<input type="text" name="stx" value="<?=$stx?>" id="stx" style="width:200px;" required class="required frm_input" placeholder="" />
<input type="submit" class="btn_submit" value="검색">
</form>

<script type="text/javascript">
var searchList = function() {
    if ($("input[name=stx]").val().trim() == '') return false;
    return true;
}
</script>
<?php
$attributes = array(
    'name' => 'fmemberlist',
    'id' => 'fmemberlist',
    'onsubmit' => 'return fmemberlist_submit(this);'
);
echo form_open('admuser/block', $attributes);
global $PHONE_080_LIST;
?>
<?php /* ?>
<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="차단하기" onclick="document.pressed=this.value">
    <input type="submit" name="act_button" value="탈퇴처리" onclick="document.pressed=this.value">
    <?php if ((int)$this->session->userdata('level') >= 5) { ?>
    <span class="btn_add01 btn_add">
        <a href="#userModal" data-toggle="modal">회원등록하기</a>
    </span>
    <?php } ?>
</div>
<?php */ ?>
<div class="tbl_head02 tbl_wrap">
    <table>
    <caption>회원관리 목록</caption>
    <thead>
    <tr>
        <th scope="col" id="mb_list_chk" class="td_date">
            <label for="chkall" class="sound_only">회원 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col" class="td_date">아이디</th>
        <th scope="col" class="td_date">이름</th>
        <th scope="col" class="td_date">인증번호</th>
    <?php if (GROUP_USE_YN == 'Y') { ?>
        <?php if (0 && (int)$this->session->userdata('level') == 9) { ?>
            <th scope="col" class="td_date">상점</th>
        <?php } ?>
            <th scope="col" class="td_date">그룹</th>
    <?php } ?>
        <th scope="col" class="td_date">현금</th>
    <?php if ((int)$this->session->userdata('level') >= 5) { ?>
        <th scope="col" class="td_date">충전.차감</th>
    <?php } ?>
        <th scope="col" class="td_date">단문</th>
        <th scope="col" class="td_date">단문채널</th>
        <th scope="col" class="td_date">장문</th>
        <th scope="col" class="td_date">장문채널</th>
        <th scope="col" class="td_date">포토</th>
        <th scope="col" class="td_date">포토채널</th>
    <?php if (is_array($PHONE_080_LIST)) { ?>
        <th scope="col" class="td_date">수신번호</th>
    <?php } else { ?>
        <th scope="col" class="td_date">최종접속일</th>
    <?php } ?>
        <th scope="col" class="td_date">차단/탈퇴일</th>
        <th scope="col" class="td_date">타입</th>
        <th scope="col" class="td_date">메모</th>
        <th scope="col" class="td_date">상태</th>
        <th scope="col" class="td_date">관리</th>
    </tr>
    </thead>
    <tbody>
<?php
    $i = 0;
    foreach ($result as $row) {
        if ((int)$this->session->userdata('level') < (int)$row->level) continue;

        $state = '';
        if ($row->state == '0') $state = '정상';
        else if ($row->state == '1') $state = '<span style="color:#396dba">차단</span>';
        else if ($row->state == '2') $state = '<span style="color:#e8180c">탈퇴</span>';
        else if ($row->state == '3') $state = '<span style="color:#396dba">발송제한</span>';

        if ((int)$row->level == 3) $admin_flag = '<span style="color:#396dba;">(M)</span>';
        else if ((int)$row->level >= 5) $admin_flag = '<span style="color:#FF0000;">(A)</span>';
        else $admin_flag = '';
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td style="width:50px;text-align:center;">
            <input type="checkbox" name="chk[]" value="<?=$row->userno?>" id="chk_<?=$row->userno?>" />
        </td>
        <td style="width:110px;text-align:center;"><a href="/admuser/detail/<?=$row->userno?>"><?=$row->userid?> <?=$admin_flag?></a></td>
        <td headers="mb_list_name" class="td_date"><?=$row->realname?></td>
        <td style="width:110px;text-align:center;"><?=phone_format($row->auth_phoneno)?></td>
    <?php if (GROUP_USE_YN == 'Y') { ?>
        <?php if (0 && (int)$this->session->userdata('level') == 9) { ?>
            <td headers="mb_list_name" class="td_date"><?=$row->storename?></td>
        <?php } ?>
            <td headers="mb_list_name" class="td_date"><?=$row->groupid?></td>
    <?php } ?>
        <td headers="mb_list_name" class="td_date" style="width:80px;font-weight:bold;text-align:right;padding-right:5px;"><?=number_format($row->cash)?></td>
    <?php if ((int)$this->session->userdata('level') >= 5) { ?>
        <td style="width:80px;text-align:center;"><a href="#accountModal" data-toggle="modal" data-realname="<?=$row->realname?>" data-userid="<?=$row->userid?>" data-userno="<?=$row->userno?>" data-storeno="<?=$row->storeno?>" data-cash="<?=number_format($row->cash)?>"><span style="color:#396dba;">[충전.차감]</span></a></td>
    <?php } ?>
        <td headers="mb_list_name" class="td_date"><?=number_format($row->sms1,2)?></td>
        <td headers="mb_list_name" class="td_date"><?=$channel_info[$row->ch_sms]?></td>
        <td headers="mb_list_name" class="td_date"><?=number_format($row->lms1,2)?></td>
        <td headers="mb_list_name" class="td_date"><?=$channel_info[$row->ch_lms]?></td>
        <td headers="mb_list_name" class="td_date"><?=number_format($row->mms1,2)?></td>
        <td headers="mb_list_name" class="td_date"><?=$channel_info[$row->ch_mms]?></td>
    <?php if (is_array($PHONE_080_LIST)) { ?>
        <td style="width:100px;text-align:center;"><?=phone_format($row->phone_080)?></td>
    <?php } else { ?>
        <td style="width:100px;text-align:center;"><?=mydate_format('y-m-d H:i', $row->login_date)?></td>
    <?php } ?>
        <td headers="mb_list_name" class="td_date"><span class="txt_true"><?=mydate_format('y-m-d', $row->remove_date)?></span></td>
        <td headers="mb_list_name" class="td_date"><?=($row->ad_type == '1' ? '광고' : '일반')?></td>
    <?php if ((int)$this->session->userdata('level') >= 5) { ?>
        <td headers="mb_list_name" class="td_date"><input type="text" name="memo5" class="frm_input" style="width:200px;" value="<?=$row->memo5?>" /></td>
    <?php } else { ?>
        <td headers="mb_list_name" class="td_date"><input type="text" name="memo3" class="frm_input" style="width:200px;" value="<?=$row->memo3?>" /></td>
    <?php } ?>
        <td headers="mb_list_name" class="td_date"><?=$state?></td>
        <td headers="mb_list_name" class="td_date"><a href="/admuser/detail/<?=$row->userno?>">[수정]</a></td>
    </tr>
<?php
        $i ++;
    }
?>
        </tbody>
    </table>
</div>
<?php /* ?>
<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="차단하기" onclick="document.pressed=this.value">
    <input type="submit" name="act_button" value="탈퇴처리" onclick="document.pressed=this.value">
</div>
<?php */ ?>
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

<?php if ($admin_balance->storeno != '' && $admin_balance->check_balance == '1' && get_cookie('admin_bill_popup_yn') != 'N') { ?>
<script type="text/javascript">
function popupClose(arv) {
    set_cookie("admin_bill_popup_yn","N",24);
    $('#b_layer').hide();
}
</script>
<style>
.layer_bg {font-size: 12px; color:#000000; font-family: "굴림", "tahoma";background-color: #fff; border: 1px solid #999; border-radius: 4px;}
.layer_bg > div.layer_bg_inner{background-color: #fff;}
.layer_bg_inner{}
.layer_bg_inner > div{line-height: 2.5em;}
.layer_bg_inner > div:nth-child(1){color: #fff; background-color: #1f7cc8;}
.layer_bg_inner > div > textarea{overflow-y:none; border: none; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; resize: none;}
.layer_bg_inner > div.d-table{padding: 0;}
.f-right {
    float: right;
}
</style>
<span id="b_layer" style="z-index: 110; position: absolute; top:20%; left:30%;">
<div class="layer_bg" style="width:400px">
    <div class="layer_bg_inner">
        <div style="padding-left:15px"><strong>문자서비스 사용료 현황</strong>
            <span class="f-right" style="padding-right:15px; cursor:pointer;" onclick="popupClose(); return false;">1일간 닫기</span>
        </div>
    </div>
    <div style="margin:10px 20px;">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr style="height:30px;">
            <td style="width:160px;padding-left:50px;color:#000;">현재 잔액</td>
            <td style="padding-left:50px;color:#1f7cc8;"><?=number_format($admin_balance->balance,2);?> 원</td>
        </tr>
        <tr style="height:30px;">
            <td colspan="2" style="padding-left:50px;color:#000;"><?=mydate_format('Y-m-d H:i', $admin_balance->check_time).':00'?> 일자 기준</td>
        </tr>
    <?php // if ($admin_balance->restrict_sending == '1') { ?>
        <tr style="height:30px;">
            <td colspan="2" style="padding-left:40px;color:#ff5959;">* 잔액 부족 시 모든 회원의 발송이 제한됩니다.</td>
        </tr>
    <?php // } ?>
        </table>
    </div>
</div>
</span>
<?php } ?>
<!-- <p>실행시간 : 0.0017969608306885 -->

</body>

<!-- The Modal Start -->
<div class="modal fade" id="userModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5>회원등록</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

    <?php
        $attributes = array(
            'name' => 'fadminuser',
            'id' => 'fadminuser',
            'onsubmit' => 'return admin_add_user_submit(this);'
        );
        echo form_open('admuser/add_user', $attributes);
    ?>
                <div class="form-group">
    <?php
        $data = array(
            'name'  => 'ipt1_userid',
            'id'    => 'ipt1_userid',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;',
            'minlength' => '6',
            'maxlength' => '20',
            'required' => '',
            'value' => '',
            'placeholder' => '* 아이디[6~20자] (필수)'
        );
        echo form_input($data);
    ?>

                </div>
                <div class="form-group">
    <?php
        $data = array(
            'name'  => 'ipt1_realname',
            'id'    => 'ipt1_realname',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;',
            'minlength' => '2',
            'maxlength' => '50',
            'required' => '',
            'value' => '',
            'placeholder' => '* 이름 (필수)'
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
    <?php
        $data = array(
            'name'  => 'ipt1_password',
            'id'    => 'ipt1_password',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;',
            'minlength' => '8',
            'maxlength' => '20',
            'required' => '',
            'value' => '',
            'placeholder' => '* 비밀번호[8~20자] (필수)'
        );
        echo form_password($data);
    ?>
                </div>
                <div class="form-group">
    <?php
        $data = array(
            'name'  => 'ipt1_mobile',
            'id'    => 'ipt1_mobile',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;',
            'minlength' => '11',
            'maxlength' => '13',
            'value' => '',
            'placeholder' => ' 핸드폰 (선택)'
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
    <?php
        $data = array(
            'name'  => 'ipt1_email',
            'id'    => 'ipt1_email',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;',
            'maxlength' => '50',
            'value' => '',
            'placeholder' => ' 이메일 (선택)'
        );
        echo form_input($data);
    ?>
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
var admin_add_user_submit = function () {
    var ipt1_userid = $.trim($("input[name=ipt1_userid]").val());
    if (ipt1_userid == '') {
        alert("아이디를 입력하세요.");
        return false;
    }
    var ipt1_realname = $.trim($("input[name=ipt1_realname]").val());
    var reg_hanengnum = /^[ㄱ-ㅎ|가-힣|a-z|A-Z|0-9|\*]+$/;
    if (!reg_hanengnum.test(ipt1_realname)) {
        alert("이름은 한글/영문/숫자만 입력 가능합니다.");
        return false;
    }
    var ipt1_password = $.trim($("input[name=ipt1_password]").val());
    if (ipt1_userid == '') {
        alert("비밀번호를 입력하세요.");
        return false;
    }
    if (checkPassword(ipt1_userid,ipt1_password) == false) return false;

    var ipt1_mobile = $.trim($("input[name=ipt1_mobile]").val());
    if (ipt1_mobile != '') {
        var regType = /^[0-9-]*$/;
        if (!regType.test(ipt1_mobile)) {
            alert("핸드폰은 숫자만 입력하세요.");
            return false;
        }
    }
    var ipt1_email = $.trim($('#ipt1_email').val());
    if (ipt1_email != '') {
        if (!validateEmail(ipt1_email)) {
            alert("올바른 이메일 주소를 입력하세요.");
            $('#email').focus();
            return false;
        }
    }
    return true;
}
var checkPassword = function (id,password) {
    if(!/^(?=.*[a-zA-Z])(?=.*[!@#$%^*+=-])(?=.*[0-9]).{8,25}$/.test(password)){
        alert('비밀번호는 숫자+영문자+특수문자 조합으로 8자리 이상 사용해야 합니다.');
        $('#password').val('').focus();
        return false;
    }
    var checkNumber = password.search(/[0-9]/g);
    var checkEnglish = password.search(/[a-z]/ig);
    if(checkNumber < 0 || checkEnglish < 0){
        alert("비밀번호는 숫자와 영문자를 혼용하여야 합니다.");
        $('#password').val('').focus();
        return false;
    }
    if(/(\w)\1\1\1/.test(password)){
        alert('비밀번호는 같은 문자를 4번 이상 사용하실 수 없습니다.');
        $('#password').val('').focus();
        return false;
    }

    if(password.search(id) > -1){
        alert("비밀번호에 아이디가 포함되었습니다.");
        $('#password').val('').focus();
        return false;
    }
    return true;
}
// function validateEmail(email) {
var validateEmail = function(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}
</script>

<script type="text/javascript">
function fmemberlist_submit(f) {
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }
    // if(document.pressed == "선택삭제") {
    //     if(!confirm("선택회원의 기본정보만 삭제되며 아이디, 닉네임 기록은 남습니다.\n\n선택한 자료를 정말 삭제하시겠습니까?")) {
    //         return false;
    //     }
    // }

    // if(document.pressed == "완전삭제") {
    //     if(!confirm("선택회원의 회원정보 자체를 DB에서 완전히 삭제합니다.\n\n선택한 자료를 정말 삭제하시겠습니까?")) {
    //         return false;
    //     }
    // }
    return true;
}
</script>

<!-- The Modal Start -->
<div class="modal fade" id="accountModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">회원명</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

    <?php
        $attributes = array(
            'name' => 'fmemberlist',
            'id' => 'fmemberlist',
            'onsubmit' => 'return admin_billing_submit(this);'
        );
        echo form_open('admuser/bill', $attributes);
    ?>
    <?php
        $data = array(
            'ipt_userno' => '',
            'ipt_userid' => '',
            'ipt_storeno' => '',
        );
        echo form_hidden($data);
    ?>
                <div class="btn-group" data-toggle="buttons" style="margin-bottom:10px">
                <label class="btn btn-sm">
                <input type="radio" name="rd_bill_mode" value="PB" /> <span style="font-family:dotum; font-size:12px">무통장입금</span>
                </label>
                <label class="btn btn-sm">
                <input type="radio" name="rd_bill_mode" value="PA" /> <span style="font-family:dotum; font-size:12px">관리자적립</span>
                </label>
                <label class="btn btn-sm">
                <input type="radio" name="rd_bill_mode" value="MA" /> <span style="font-family:dotum; font-size:12px">관리자차감</span>
                </label>
                <label class="btn btn-sm">
                <input type="radio" name="rd_bill_mode" value="MB" /> <span style="font-family:dotum; font-size:12px">회원환불</span>
                </label>
                </div>
                <div class="form-group" id="current_cash"></div>
                <div class="form-group">
                <input type="text" class="form-control" name="ipt_amount" placeholder="금액입력" style="font-family:dotum; font-size:12px;">
                </div>
                <div class="form-group">
                <textarea class="form-control" name="ipt_memo" id="ipt_memo" placeholder="관리메모" style="font-family:dotum; font-size:12px;"></textarea>
                </div>
                <div>* 금액은 숫자만 입력하세요.</div>
                <div>* 무통장입금, 관리자적립은 입력한 금액이 충전되어집니다.</div>
                <div>* 관리자차감, 회원환불은 입력한 금액이 차감되어집니다.</div>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-danger btn-sm" value="확인" ><span style="font-size:12px"></span>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><span style="font-size:12px">닫기</span></button>
            </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
$('#accountModal').on('show.bs.modal', function (event) {
    var user = $(event.relatedTarget)
    var realname = user.data('realname')
    var userid = user.data('userid')
    var userno = user.data('userno')
    var storeno = user.data('storeno')
    var cash = user.data('cash')
    var modal = $(this)
    modal.find('.modal-title').text(realname + ' 회원 ( ID :  ' + userid + ' )');
    //modal.find('.modal-body input').val(recipient2)
    modal.find('#current_cash').html('<b>현재잔액 : ' + cash + ' 원</b>');
    $("input[name=ipt_userid]").val(userid);
    $("input[name=ipt_userno]").val(userno);
    $("input[name=ipt_storeno]").val(storeno);
})
var admin_billing_submit = function () {
    if ($("input:radio[name=rd_bill_mode]").is(':checked') == false) {
        alert("충전.차감 타입을 선택하세요.");
        return false;
    }
    var ipt_amount = $.trim($("input[name=ipt_amount]").val());
    if (ipt_amount == '') {
        alert("금액을 입력하세요.");
        return false;
    }
    var regType = /^[0-9,]*$/;
    if (!regType.test(ipt_amount)) {
        alert("금액은 숫자만 입력하세요.");
        return false;
    }
    var ipt_memo = $.trim($("#ipt_memo").val());
    if (ipt_memo == '') {
        alert("관리 메모를 입력하세요.");
        return false;
    }
    return true;
}
</script>
<!-- The Modal End-->

