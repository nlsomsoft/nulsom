<!DOCTYPE html>
<html>
<head>
<title><?=TITLE?></title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="문자, 선거문자, 20건 선거문자, 단문, 장문, 포토문자, 대량문자">
<?php if ($_g_search_robots !== true) { ?>
<meta name="robots" content="noindex">
<?php } ?>

<?php
    include_once(VIEWPATH.'/templates/head.php');
?>
</head>

<body class="">
    <div class="body-inner">
<!-- header start -->
        <div id="header" class="header">
            <div class="bd-ccc">
                <div class="body-inner-table">
                    <div style="height:80px">
    <?php
        $common_path = FCPATH.'images/sowkorea_logo.jpg';
        $file_path = FCPATH.'images/'.$_SERVER['STORENAME'].'_logo.jpg';
        $logo_path = (file_exists($file_path) ? '/images/'.$_SERVER['STORENAME'].'_logo.jpg' : '/images/sowkorea_logo.jpg');
    ?>
                        <span style="position:absolute;top:10px;"><a href="/"><img src="<?=$logo_path?>" alt="logo" /></a></span>
<?php if ($this->session->userdata('logged_in') === true) { ?>
                                    <span>
                                        <div class="login-contents" style="position:absolute; top: 15px; left:420px;">
                                            <span><?=$this->session->userdata('realname')?> 님</span>
                                            <span>잔액</span>
                            <?php if ($this->session->userdata('pay_type') == '1') { ?>
                                            <span>후불사용자</span>
                            <?php } else { ?>
                                            <span><?=number_format($this->session->userdata('cash'))?> 원</span> <a class="charge" href="/pay/list" title="충전하기">충전하기</a>
                            <?php } ?>
                            <?php if ((int)$this->session->userdata('level') > 1 && $_SERVER['REQUEST_URI'] == '/') { ?>
                                <?php if ($this->session->userdata('authed_manager') === true) { ?>
                                    <span style="padding-left:10px;"></span><a class="charge" href="/admuser/users" title="충전하기">관리자</a>
                                <?php } else { ?>
                                    <span style="padding-left:10px;"></span>
                                    <a href="#" onclick="authManagerForm();" class="charge" title="관리자인증" id="authmanager_1">관리자 권한요청</a>
                                <?php } ?>
                            <?php } ?>
                                        </div>
                                    </span>
                                    <div class="f-right" style="margin-top:17px">
       <span class="bot_btn">
              <span class="bc bc_wht"><span><a href="/signup/logout">로그아웃</a></span></span>
        </span>
                                    </div>
<?php } else { ?>
                                    <div class="f-right" style="margin-top:17px">
<?=form_open('signup/register', 'id="formLogin"');?>
<?php
$data = array(
    'name'  => 'user_id',
    'id'    => 'user_id',
    'size'    => '20',
    'minlength' => '6',
    'maxlength' => '20',
    'class' => 'logpw u_login',
    'required' => '',
    'placeholder' => '아이디'
);
echo form_input($data);
?>
<?php
    $data = array(
        'name'  => 'password',
        'id'    => 'password',
        'minlength' => '8',
        'maxlength' => '20',
        'title' => '비밀번호',
        'class' => 'logpw u_pw',
        'required' => '',
        'placeholder' => '비밀번호'
    );
    echo form_password($data);
?>
        <a class="find_id_pw" href="/signup/id_form" title="아이디/비번찾기">아이디/비번찾기</a>
        <span class="bot_btn">
              <span class="bc bc_wht"><span name="login_submit" id="login_button"><a href="#" onclick="loginSubmit(); return false;">로그인</a></span></span>
              <span class="bc bc_wht"><span><a href="/signup/join_agree">회원가입</a></span></span>
        </span>
</form>
                                    </div>
<?php } ?>
                    </div>
                </div>
            </div>
            <div class="header">
                <div class="w100pro bd-ddd boxShadow">
                    <div class="body-inner-table">
                        <div class="d-table-cell po-u">
                            <div class="d-block ">
                                <?php /* ?><div class="voteicon_view"></div><?php */ ?>
                                <ul class="paste-here f-right hide">
                                    <li>
                                        <div class="open-menu-target open-target body-inner-table">
                                            <div class="all-menu">
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <ul class="main-menu copy-this">
                            <?php if ($this->session->userdata('logged_in') === true) { ?>
                                    <?php /* ?><li><a href="/sms/sms">문자</a></li><?php */ ?>
                                    <li><a href="/sms/newsms">선거문자</a></li>
                                    <li><a href="/sms/adsms">광고문자</a></li>
                                    <li><a href="/address/group">주소록</a></li>
                                    <li><a href="/result/list">결과통계</a></li>
                                    <li><a href="/pay/list">결제관리</a></li>
                                    <li><a href="/info/intro">환경설정</a></li>
                            <?php } else { ?>
                                    <?php /* ?><li><a href="/statics/sms">문자</a></li><?php */ ?>
                                    <li><a href="#" onclick="alert('로그인 후 이용하세요.');">선거문자</a></li>
                                    <li><a href="#" onclick="alert('로그인 후 이용하세요.');">광고문자</a></li>
                                    <li><a href="#" onclick="alert('로그인 후 이용하세요.');">주소록</a></li>
                                    <li><a href="#" onclick="alert('로그인 후 이용하세요.');">결과통계</a></li>
                                    <li><a href="#" onclick="alert('로그인 후 이용하세요.');">결제관리</a></li>
                                    <li><a href="#" onclick="alert('로그인 후 이용하세요.');">환경설정</a></li>
                            <?php } ?>
                                </ul>
                                <div class="main-bg"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php if ($this->session->userdata('logged_in') !== true) { ?>
<script type="text/javascript">
var loginSubmit = function() {
    if ($("#user_id").val() == "") {
        alert("아이디를 입력하세요.");
        $("#user_id").focus();
        return;
    }
    if ($("#password").val() == "") {
        alert("비밀번호를 입력하세요.");
        $('#password').focus();
        return;
    }
    $("form#formLogin").attr("action", "/signup/login");
    $("form#formLogin").submit();
}

$("#user_id").keydown(function(event){
    var keyCode = window.event.keyCode;
    if (keyCode != 13) return;
    loginSubmit();
});
$("#password").keydown(function(event){
    var keyCode = window.event.keyCode;
    if (keyCode != 13) return;
    loginSubmit();
});
</script>
<?php } ?>



<?php if ((int)$this->session->userdata('level') > 1 && $this->session->userdata('authed_manager') !== true) { ?>

<script type="text/javascript">
var _authmanager = 0;
function authManagerForm() {
    if (_authmanager == 0) {
        var _location_base = document.getElementById("authmanager_1");
        var _a = getOffsetPosition(_location_base);
        layerOpenLocationNew("authmanger_layer", _a[0] + 0, _a[1] + 25); //left,top
        _authmanager = 1;
    } else {
        layerCloseNew('authmanger_layer');
        _authmanager = 0;
    }
}
function sendSMSToManager() {
    var ipt_manager_mobile = $.trim($("input[name=ipt_manager_mobile]").val());
    if (ipt_manager_mobile == "") {
        alert("휴대폰번호를 입력하세요.");
        return;
    }

    var str = ipt_manager_mobile.substr(0, 3);
    var org_number = ipt_manager_mobile.replace("-","");
    var phone_flag = false;
    if (str == "010") {
        if (org_number.length != 11 && org_number.length != 12) {
            alert("휴대폰 번호 형식 오류입니다.");
            return;
        }
        phone_flag = true;
    }
    if (phone_flag == false) {
        alert("휴대폰 번호 형식 오류입니다.");
        return;
    }

    var csrf_sowkorea_name = $.trim($("input[name=csrf_sowkorea_name]").val());
    $.ajax({
        type: "POST",
        url: "/info/manager_auth",
        data: {
            "csrf_sowkorea_name" : csrf_sowkorea_name,
            "manager_mobile" : ipt_manager_mobile,
            "where" : "main"
        },
        dataType: "json",
        async: false,
        success : function(data, status, xhr) {
            $("input[name=csrf_sowkorea_name]").val(data.csrf_sowkorea_name);
            if (data.result == 'complete') {
                $(location).attr('href', '/');
            }
            else if (data.result == 'success') {
                $('#mmtable').css('display', 'none');
                $('#antable').css('display', 'block');
                alert(data.message);
            }
            else {
                alert(data.message);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.responseText);
        }
    });

}
function authConfirm() {
    var auth_number = $.trim($("input[name=ipt_auth_number]").val());
    if (auth_number == "") {
        alert("인증번호를 입력하세요.");
        return;
    }

    var regex= /^[0-9]*$/;
    if (!regex.test(auth_number)) {
        alert("인증번호 형식 오류입니다.");
        return;
    }

    var csrf_sowkorea_name = $.trim($("input[name=csrf_sowkorea_name]").val());
    $.ajax({
        type: "POST",
        url: "/info/confirm_manager",
        data: {
            "csrf_sowkorea_name" : csrf_sowkorea_name,
            "auth_number" : auth_number,
            "where" : "main"
            },
        dataType: "json",
        async: false,
        success : function(data, status, xhr) {
            $("input[name=csrf_sowkorea_name]").val(data.csrf_sowkorea_name);
            alert(data.message);
            if (data.result == "success") $(location).attr('href', '/');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.responseText);
        }
    });
}
</script>


<?php
    $attributes = array(
        'name' => 'frmAuthManager',
        'id' => 'frmAuthManager'
    );
    echo form_open('/info/manager_auth', $attributes);
?>
<span id="authmanger_layer" style="display:none; z-index:110;">
<div class="layer_bg" style="width:350px">
    <div class="layer_bg_inner">
        <div style="padding-left:15px"><strong>관리자 인증하기</strong><span class="f-right" style="padding-right:15px; cursor:pointer" onclick="authManagerForm(); return false;">닫기</span></div></div>
        <div style="margin:10px 20px;">
        <div id="mmtable">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width:80px;">휴대폰번호</td>
                <td><input type="text" name="ipt_manager_mobile" value="" class="input_36" minlength="8" maxlength="13" style="width:100%;" title="휴대폰번호" required="" placeholder="휴대폰번호"></td>
            </tr>
            <tr>
                <td height="60" align="center" colspan="2">
                    <div class="style_btn1" style="width:130px; margin-top:5px"><a href="#" onclick="sendSMSToManager(); return false;">문자로 인증요청</a></div>
                </td>
            </tr>
        </table>
        </div>
        <div id="antable" style="display:none;">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width:80px;">인증번호</td>
                <td><input type="text" name="ipt_auth_number" value="" class="input_36" minlength="4" maxlength="10" style="width:100%;" title="인증번호" required="" placeholder="인증번호"></td>
            </tr>
            <tr>
                <td height="60" align="center" colspan="2">
                    <div class="style_btn1" style="width:130px; margin-top:5px"><a href="#" onclick="authConfirm(); return false;">관리자 권한요청</a></div>
                </td>
            </tr>
        </table>
        </div>
    </div>
</div>
</span>
</form>


<?php } ?>

<!-- sowkorea header -->
