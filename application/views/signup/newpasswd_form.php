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
<div class="content_wrap">
	<div class="tab_menu2">
    	<ul>
    	<li><a href="/signup/id_form"><b>아이디 찾기</b></a></li>
    	<li class="on"><a href="/signup/passwd_form"><font color="#fff"><b>비밀번호 재설정</b></font></a></li>
    	</ul>
	</div>

    <div style="margin:20px 0px; border:1px solid #ddd; padding:8px 15px; background:#F1F1F1; font-size:13px">
    	<p> * 회원가입시 기재하셨던 정보를 입력해 주세요.<br>
			* 기억이 나지 않거나 정보가 일치하지 않는 경우에는 고객센터로 연락주시기 바랍니다.</p>
    </div>
	<div style="padding:10px 0px 10px 10px"><b>등록한 정보로 찾기</b></div>
    <div style="border:1px solid #CCCCCC; border-radius:5px; margin-bottom:50px">
    <?php
        $attributes = array(
            'name' => 'frmSignup',
            'id' => 'frmSignup',
            'onsubmit' => 'return authNewPasswd();'
        );
        echo form_open('/signup/newpasswd', $attributes);
    ?>
    <?php
    $data = array(
        'auth_key' => $auth_key,
        'ipt_user_id' => $userid,
    );
    echo form_hidden($data);
    ?>
		<table width="100%">
           	<tr><td height="50"></td></tr>
            <tr><td align="center" valign="middle"><span style="font-size:17px; color:#666"><b>새로운 비밀번호를 설정하세요.</b></span></td></tr>
           	<tr><td height="30"></td></tr>
			<tr><td align="center">
				<table width="40%">
                    <tr><td height="40"><span style="font-size:13px; color:#666">새로운 비밀번호</span></td>
                    	<td>
                    <?php
                        $data = array(
                            'name'  => 'ipt_new_passwd',
                            'id'    => 'ipt_new_passwd',
                            'class' => 'input_36',
                            'minlength' => '8',
                            'maxlength' => '20',
                            'style' => 'width:100%; background:#efefef;',
                            'title' => '비밀번호',
                            'required' => '',
                            'placeholder' => ''
                        );
                        echo form_password($data);
                    ?>
                        </td>
                    </tr>
                    <tr><td height="40"><span style="font-size:13px; color:#666">비밀번호 확인</span></td>
                        <td>
                    <?php
                        $data = array(
                            'name'  => 'ipt_re_passwd',
                            'id'    => 'ipt_re_passwd',
                            'class' => 'input_36',
                            'minlength' => '8',
                            'maxlength' => '20',
                            'style' => 'width:100%; background:#efefef;',
                            'title' => '비밀번호확인',
                            'required' => '',
                            'placeholder' => ''
                        );
                        echo form_password($data);
                    ?>
                        </td>
                    </tr>
			    </table>
			</td></tr>
            <tr><td align="center" valign="middle"><div class="bot_btn"><input type="submit" id="signup-button" class="sowsms-inp-submit" value="수정하기" /></div></td></tr>
           	<tr><td height="40"></td></tr>
		</table>
		</form>
	</div>

</div>


        </td>
</tr>
</table>

</div>
<!-- content end -->

<script type="text/javascript">
var authNewPasswd = function () {
    var ipt_new_password = $("#ipt_new_passwd").val();
    if (ipt_new_password == "") {
        alert("새로운 비밀번호를 입력하세요.");
        return false;
    }
    var ipt_re_password = $("#ipt_re_passwd").val();
    if (ipt_re_password == "") {
        alert("비밀번호 확인을 입력하세요.");
        return false;
    }
    if (ipt_new_password != ipt_re_password) {
        alert("새로운 비밀번호가 일치하지 않습니다. 다시 확인해 주세요.");
        return false;
    }

    var ipt_user_id = $("input[name=ipt_user_id]").val();
    if (checkPassword(ipt_user_id,ipt_new_password) == false) return false;
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
        $('#ipt_new_password').val('').focus();
        return false;
    }
    if(/(\w)\1\1\1/.test(password)){
        alert('같은 문자를 4번 이상 사용하실 수 없습니다.');
        $('#ipt_new_password').val('').focus();
        return false;
    }

    if(password.search(id) > -1){
        alert("비밀번호에 아이디가 포함되었습니다.");
        $('#ipt_new_password').val('').focus();
        return false;
    }
    return true;
}
</script>
