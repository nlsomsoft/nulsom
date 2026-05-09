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
<?php if (!$auth_key) { ?>
    <div style="border:1px solid #CCCCCC; border-radius:5px; margin-bottom:50px">
        <table width="100%">
            <tr><td height="50"></td></tr>
            <tr><td align="center" valign="middle"><span style="font-size:17px; color:#666"><b>조회결과가 없습니다.</b></span></td></tr>
            <tr><td align="center" valign="middle"><div class="bot_btn">
                        <span class="bc bc_org"><span id="next-button2"><a href="/signup/passwd_form">비밀번호 재설정</a></span></span>
                    </div></td></tr>
            <tr><td height="40"></td></tr>
        </table>
    </div>
<?php } else { ?>
	<div style="padding:10px 0px 10px 10px"><b>등록한 정보로 찾기</b></div>
    <div style="border:1px solid #CCCCCC; border-radius:5px; margin-bottom:50px">
    <?php
        $attributes = array(
            'name' => 'frmSignup',
            'id' => 'frmSignup',
            'onsubmit' => 'return authNumbers();'
        );
        echo form_open('/signup/auth_nums', $attributes);
    ?>
    <?php
    $data = array(
        'auth_key' => $auth_key,
    );
    echo form_hidden($data);
    ?>
		<table width="100%">
           	<tr><td height="50"></td></tr>
            <tr><td align="center" valign="middle"><span style="font-size:17px; color:#666"><b>가입 시 등록한 핸드폰으로 인증번호가 발송되었습니다.</b></span></td></tr>
           	<tr><td height="30"></td></tr>
			<tr><td align="center">
				<table width="40%">
                    <tr><td height="40"><span style="font-size:13px; color:#666">인증번호</span></td>
                    	<td><input name="ipt_auth_num" id="ipt_auth_num" class="input_36" style="width:100%; background:#efefef" minlength="6" maxlength="6" /></td></tr>
			    </table>
			</td></tr>
            <tr><td align="center" valign="middle"><div class="bot_btn"><input type="submit" id="signup-button" class="sowsms-inp-submit" value="인증하기" /></div></td></tr>
           	<tr><td height="40"></td></tr>
		</table>
		</form>
	</div>

<script type="text/javascript">
var authNumbers = function () {
    if ($("#ipt_auth_num").val() == "") {
        alert("인증번호를 입력하세요.");
        return false;
    }
    return true;
}
</script>


<?php } ?>

</div>


        </td>
</tr>
</table>

</div>
<!-- content end -->

