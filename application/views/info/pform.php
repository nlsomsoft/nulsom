<!-- content start -->
<div class="body-inner-table" style="padding-top:20px; min-height:700px;">

	<table width="1200" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<!-- left menu start -->
		<td width="210" valign="top">
			<?php
				$g_left_menu_flag = 'info';
				include_once(VIEWPATH.'/templates/left_menu.php');
			?>
        </td>
			<!-- left menu end -->
			<td width="30"></td>
			<td width="960" valign="top">


<div class="content_wrap">
    <div class="body_title" style="position:relative;">비밀번호변경</div>

    <div style="margin:50px 100px; border:1px solid #CCCCCC; border-radius:10px">
<?php
$attributes = array(
    'id' => 'frmInfo',
    'onsubmit' => 'return doPassword();'
);
echo form_open('info/password', $attributes);
?>
<?php
$data = array(
    'userid'  => $this->session->userdata('userid'),
);
echo form_hidden($data);
?>
		<table width="100%">
           	<tr><td height="50"></td></tr>
            <tr><td align="center" valign="middle"><span style="font-size:17px; color:#666"><b>회원님의 개인정보 보호를 위해 쉬운 비밀번호는 자제해 주세요.</b></span></td></tr>
           	<tr><td height="30"></td></tr>
			<tr><td align="center">
				<table width="60%">
                    <tr><td width="23%"><span style="font-size:13px; color:#666">현재 비밀번호</span></td>
	                    <td width="77%">
                        <?php
                            $data = array(
                                'name'  => 'ipt_password',
                                'id'    => 'ipt_password',
                                'class' => 'input_36',
                                'minlength' => '8',
                                'maxlength' => '20',
                                'style' => 'width:100%; background:#efefef',
                                'title' => '비밀번호',
                                'required' => '',
                                'placeholder' => ''
                            );
                            echo form_password($data);
                        ?>
	                    </td>
	                </tr>
                    <tr><td colspan="2" height="10"></td></tr>
                    <tr><td><span style="font-size:13px; color:#666">새 비밀번호</span></td>
                    	<td>
                        <?php
                            $data = array(
                                'name'  => 'new_password',
                                'id'    => 'new_password',
                                'class' => 'input_36',
                                'minlength' => '8',
                                'maxlength' => '20',
                                'style' => 'width:100%; background:#efefef',
                                'title' => '비밀번호',
                                'required' => '',
                                'placeholder' => ''
                            );
                            echo form_password($data);
                        ?>
                    	</td>
                    </tr>
                    <tr><td></td>
                    	<td height="30"><span style="font-size:11px; color:#666">* 영문, 숫자, 특수문자를 조합하여 8~20자로 입력해 주세요.</span></td></tr>
                    <tr><td><span style="font-size:13px; color:#666">비밀번호 확인</span></td>
                    	<td>
                        <?php
                            $data = array(
                                'name'  => 're_password',
                                'id'    => 're_password',
                                'class' => 'input_36',
                                'minlength' => '8',
                                'maxlength' => '20',
                                'style' => 'width:100%; background:#efefef',
                                'title' => '비밀번호',
                                'required' => '',
                                'placeholder' => ''
                            );
                            echo form_password($data);
                        ?>
                    	</td>
                    </tr>
			    </table>
			</td></tr>
            <tr><td align="center" valign="middle"><div class="bot_btn"><input type="submit" id="signup-button" class="sowsms-inp-submit" value="변경하기" /></div></td></tr>
           	<tr><td height="40"></td></tr>
		</table>
</form>
	</div>
</div>


<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td height="30"></td></tr>
<tr><td><img src="/images/tip.gif" width="145" height="34" alt="" /></td></tr>
<tr><td height="1" bgcolor="#CCCCCC"></td></tr>
<tr><td class="help_txt">
        * 현재 비밀번호가 3회 이상 틀릴 경우 자동 로그아웃 됩니다.<br>
        * 주기적인 비밀번호 변경을 권장합니다.
</td></tr>
<tr><td height="30px"></td></tr>
</table>
        </td>
</tr>
</table>

</div>
<!-- content end -->
<script type="text/javascript">
var doPassword = function() {
	if ($("#ipt_password").val() == "") {
		alert("현재 비밀번호를 입력하세요.");
		return false;
	}
	if ($("#new_password").val() == "") {
		alert("새 비밀번호를 입력하세요.");
		return false;
	}
	if ($("#new_password").val() != $("#re_password").val()) {
		alert("새 비밀번호가 일치하지 않습니다.");
		return false;
	}
	if (checkPassword($("input[name=userid]").val(),$("#new_password").val()) == false) return false;
	return true;
}
var checkPassword = function (id,password) {
    if(!/^(?=.*[a-zA-Z])(?=.*[!@#$%^*+=-])(?=.*[0-9]).{8,25}$/.test(password)){
        alert('숫자+영문자+특수문자 조합으로 8자리 이상 사용해야 합니다.');
        return false;
    }
    var checkNumber = password.search(/[0-9]/g);
    var checkEnglish = password.search(/[a-z]/ig);
    if(checkNumber < 0 || checkEnglish < 0){
        alert("숫자와 영문자를 혼용하여야 합니다.");
        return false;
    }
    if(/(\w)\1\1\1/.test(password)){
        alert('같은 문자를 4번 이상 사용하실 수 없습니다.');
        return false;
    }

    if(password.search(id) > -1){
        alert("비밀번호에 아이디가 포함되었습니다.");
        return false;
    }
    return true;
}
</script>