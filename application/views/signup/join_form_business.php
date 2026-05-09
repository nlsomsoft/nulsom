<!-- content start -->
<div class="body-inner-table" style="padding-top:20px; min-height:700px;">

<table width="1200" border="0" cellspacing="0" cellpadding="0">
    <tr>
<!-- left menu start -->
        <td width="210" valign="top">
<?php
    $g_left_menu_flag = 'signup';
    include_once(VIEWPATH.'/templates/left_menu.php');
?>
        </td>
<!-- left menu end -->
        <td width="30"></td>
        <td width="960" valign="top">
            <div class="mb_path">
            <ol class="on_3">
                <li><span class="blind">약관동의</span></li>
                <li><span class="blind">가입확인</span></li>
                <li><span class="blind">정보입력</span></li>
                <li><span class="blind">가입완료</span></li>
            </ol></div>



            <div class="mb_wrap">
                <div class="join_hd">
                    <h2>사업자회원 정보입력</h2>
                    <p>
<div style="padding-top:10px;">
<span style="color:#111;">
* 사업자 회원은 서비스 사용을 위하여 추가 서류를 담당자 메일 (<?=EMAIL?>) 로 보내 주시기 바랍니다. <br 
/>
- 사업자 (대표일 경우) : 사업자등록증 <br />
- 사업자 (직원일 경우) : 사업자등록증, 재직증명서 <br />
* 보내주신 서류는 관리자 확인 후 관리자 승인을 받아 서비스를 이용하실 수 있습니다. <br 
/>
</span>
</div>
                    </p>
                </div>
                <p class="indispens">
                    * 필수 입력 사항입니다.</p>
                    <div class="field_wrap w_2">
                    <fieldset class="field_1 noline">
                        <legend class="blind">정보입력</legend>

<?php
$attributes = array(
    'id' => 'frmSignup',
    'onsubmit' => 'return doSignup();'
);
echo form_open('signup/register', $attributes);
?>
<?php
$data = array(
    'user_checked' => 'N',
    'user_type' => $this->session->userdata('user_type'),
    'kmc_mobile' => $this->session->userdata('kmc_mobile'),
    'kmc_name' => $this->session->userdata('kmc_name'),
    'kmc_kno' => $this->session->userdata('kmc_kno'),
    'kmc_table' => $this->session->userdata('kmc_table'),
);
echo form_hidden($data);
?>

                        <table class="write_inp info" border="0">
                            <colgroup><col style="width: 155px;">
                            </colgroup><tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="corpname"><span>*</span> 사업자명</label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'ipt_com_name',
                                    'id'    => 'ipt_com_name',
                                    'class' => 'input_36',
                                    'maxlength' => '20',
                                    'style' => 'width:325px;',
                                    'title' => '사업자명',
                                    'required' => '',
                                    'placeholder' => ''
                                );
                                echo form_input($data);
                            ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="corpssn"><span>*</span> 사업자등록번호</label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'ipt_com_number',
                                    'id'    => 'ipt_com_number',
                                    'class' => 'input_36',
                                    'maxlength' => '20',
                                    'style' => 'width:325px;',
                                    'title' => '사업자등록번호',
                                    'required' => '',
                                    'placeholder' => ''
                                );
                                echo form_input($data);
                            ?>
                                     </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="realname"><span>*</span> 담당자명</label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'realname',
                                    'id'    => 'realname',
                                    'class' => 'input_36',
                                    'maxlength' => '20',
                                    'style' => 'width:325px;',
                                    'disabled' => 'disabled',
                                    'value' => $this->session->userdata('kmc_name'),
                                    'title' => '담당자명',
                                    'placeholder' => ''
                                );
                                echo form_input($data);
                            ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="mobile"><span>*</span> 핸드폰번호</label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'realmobile',
                                    'id'    => 'realmobile',
                                    'class' => 'input_36',
                                    'maxlength' => '20',
                                    'style' => 'width:325px;',
                                    'disabled' => 'disabled',
                                    'value' => $this->session->userdata('kmc_mobile'),
                                    'title' => '핸드폰번호',
                                    'placeholder' => ''
                                );
                                echo form_input($data);
                            ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="userid"><span>*</span> 아이디</label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'ipt_user_id',
                                    'id'    => 'ipt_user_id',
                                    'class' => 'input_36',
                                    'minlength' => '6',
                                    'maxlength' => '20',
                                    'style' => 'width:325px;',
                                    'title' => '아이디',
                                    'required' => '',
                                    'placeholder' => ''
                                );
                                echo form_input($data);
                            ?>
                        <input type="button" id="check-id-link" class="sowsms-button-link" title="중복확인" value="중복확인">
                        <p class="sml_txt">6~20자 이내의 영문, 숫자만 가능합니다.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="pwd"><span>*</span> 비밀번호</label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'ipt_password',
                                    'id'    => 'ipt_password',
                                    'class' => 'input_36',
                                    'minlength' => '8',
                                    'maxlength' => '20',
                                    'style' => 'width:325px;',
                                    'title' => '비밀번호',
                                    'required' => '',
                                    'placeholder' => ''
                                );
                                echo form_password($data);
                            ?>
                                        <p class="sml_txt">
                                            영문, 숫자, 특수문자를 조합하여 8~20자로 입력해 주세요.<br>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="pwd2"><span>*</span> 비밀번호 확인</label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'ipt_re_password',
                                    'id'    => 'ipt_re_password',
                                    'class' => 'input_36',
                                    'minlength' => '8',
                                    'maxlength' => '20',
                                    'style' => 'width:325px;',
                                    'title' => '비밀번호확인',
                                    'required' => '',
                                    'placeholder' => ''
                                );
                                echo form_password($data);
                            ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="email"><span>*</span> 이메일</label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'ipt_email',
                                    'id'    => 'ipt_email',
                                    'class' => 'input_36',
                                    'maxlength' => '50',
                                    'style' => 'width:325px;',
                                    'title' => '이메일',
                                    'required' => '',
                                    'placeholder' => ''
                                );
                                echo form_input($data);
                            ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="phone">전화번호</label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'ipt_phone',
                                    'id'    => 'ipt_phone',
                                    'class' => 'input_36',
                                    'maxlength' => '13',
                                    'style' => 'width:325px;',
                                    'title' => '전화번호',
                                    'placeholder' => ''
                                );
                                echo form_input($data);
                            ?>
                                    </td>
                                </tr>
                <?php if (is_array($group_list)) { ?>
                                <tr>
                                    <th scope="row">
                                        <label for="group_type"></label>
                                    </th>
                                    <td>
                        <?php
                            $options = array();
                            $options[0] = '(선택사항) 유입경로를 선택하세요.';
                            foreach ($group_list as $row) {
                                $options[$row->groupno] = $row->group_name;
                            }
                            $js = 'class="input_36" style="width:325px;"';
                            echo form_dropdown('sel_groupno', $options, '', $js);
                        ?>
                                    </td>
                                </tr>
                <?php } ?>
                            </tbody>
                        </table>
                    </fieldset>
                    <div class="bot_btn" style="margin-bottom:50px; margin-left:-70px">
                        <input type="submit" id="signup-button" class="sowsms-inp-submit" value="회원가입" />&nbsp;&nbsp;
                        <span class="bc bc_gry"><span><a href="javascript:">취소</a></span></span>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>
</form>
</div>
<!-- content end -->


<script type="text/javascript">
var doSignup = function () {
    var ipt_com_name = $.trim($('#ipt_com_name').val());
    var ipt_com_number = $.trim($('#ipt_com_number').val());
    var ipt_user_id = $.trim($('#ipt_user_id').val());
    var ipt_password = $.trim($('#ipt_password').val());
    var ipt_re_password = $.trim($('#ipt_re_password').val());
    var user_checked = $.trim($("input[name=user_checked]").val());

    if (ipt_com_name == '') {
        alert("사업자명을 입력하세요.");
        $('#ipt_com_name').focus();
        return false;
    }
    if (ipt_com_number == '') {
        alert("사업자등록번호를 입력하세요.");
        $('#ipt_com_number').focus();
        return false;
    }
    if (ipt_user_id == '') {
        alert("아이디를 입력하세요.");
        $('#ipt_user_id').focus();
        return false;
    }
    var regType1 = /^[A-Za-z0-9+]{6,20}$/;
    if (!regType1.test(ipt_user_id)) {
        alert("아이디는 6~20자의 영문,숫자만 가능합니다.");
        $('#ipt_user_id').focus();
        return false;
    }
    if (user_checked != "Y") {
        alert("아이디 중복확인을 하세요.");
        return false;
    }
    if (ipt_password == '') {
        alert("비밀번호를 입력하세요.");
        $('#ipt_password').focus();
        return false;
    }
    if (ipt_password != ipt_re_password) {
        alert("비밀번호가 일치하지 않습니다.");
        $('#ipt_re_password').focus();
        return false;
    }
    if (checkPassword(ipt_user_id,ipt_password) == false) return false;

    var ipt_email = $.trim($('#ipt_email').val());
    if (ipt_email == '') {
        alert("이메일 주소를 입력하세요.");
        $('#ipt_email').focus();
        return false;
    }
    if (ipt_email != '') {
        if (!validateEmail(ipt_email)) {
            alert("올바른 이메일 주소를 입력하세요.");
            $('#email').focus();
            return false;
        }
    }
    return true;
}
var checkPassword = function (id,password) {
    if(!/^(?=.*[a-zA-Z])(?=.*[!@#$%^*+=-])(?=.*[0-9]).{8,25}$/.test(password)){
        alert('숫자+영문자+특수문자 조합으로 8자리 이상 사용해야 합니다.');
        $('#password').val('').focus();
        return false;
    }
    var checkNumber = password.search(/[0-9]/g);
    var checkEnglish = password.search(/[a-z]/ig);
    if(checkNumber < 0 || checkEnglish < 0){
        alert("숫자와 영문자를 혼용하여야 합니다.");
        $('#password').val('').focus();
        return false;
    }
    if(/(\w)\1\1\1/.test(password)){
        alert('같은 문자를 4번 이상 사용하실 수 없습니다.');
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
$.fn.checkMultiUserid = function()
{
    $(this).click(function(){
        var ipt_user_id = $.trim($("#ipt_user_id").val());
        var csrf_sowkorea_name = $.trim($("input[name=csrf_sowkorea_name]").val());
        if (ipt_user_id == ''){
            alert("아이디를 입력하세요.");
            return false;
        }

        $("#check-id-link").prop("disabled",true);

        $.ajax({
            type: "POST",
            url: "/signup/check_id",
            data: {
                "csrf_sowkorea_name" : csrf_sowkorea_name,
                "ipt_user_id" : ipt_user_id,
                "where"       : "signup"
            },
            dataType: "json",
            async: false,
            success : function(data, status, xhr) {
                if (data.result == 'success') {
                    $("#check-id-link").prop("disabled",false);
                    alert(data.message);
                    $("input[name=user_checked]").val("Y");
                    $("input[name=csrf_sowkorea_name]").val(data.csrf_sowkorea_name);
                    // $("#mobile").attr("readonly",true);
                    // $("#div_auth_numbers").show();
                    // $("#send_auth_numbers").hide();
                    // $("#confirm_auth_numbers").show();
                    // alert_tooltip(data.message);
                    // $(location).attr('href', '/bbs/board.php');
                } else {
                    $("#check-id-link").prop("disabled",false);
                    alert(data.message);
                    $("input[name=user_checked]").val("N");
                    $("input[name=csrf_sowkorea_name]").val(data.csrf_sowkorea_name);
                    //console.log('pass(1)');
                    // alert_tooltip(data.message);
                    //$(location).attr('href', './signup.php');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#check-id-link").prop("disabled",false);
                $("input[name=user_checked]").val("N");
                console.log(jqXHR.responseText);
            }
        });
    });
}

$(function(){
    $("#check-id-link").checkMultiUserid();
});
</script>