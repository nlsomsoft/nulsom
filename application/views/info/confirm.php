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
    <div class="body_title" style="position:relative;">비밀번호 재확인</div>

    <div style="margin:50px 100px; border:1px solid #CCCCCC; border-radius:10px">
<?php
$attributes = array(
	'name' => 'frmIntro',
	'id' => 'frmIntro',
	'onsubmit' => 'return confirmAuth();'
);
echo form_open('info/confirm', $attributes);
?>
		<table width="100%">
           	<tr><td height="50"></td></tr>
            <tr><td align="center" valign="middle"><span style="font-size:17px; color:#666"><b>회원님의 정보보호를 위해 비밀번호를 확인합니다.</b></span></td></tr>
           	<tr><td height="30"></td></tr>
			<tr><td align="center">
				<table width="40%">
                    <tr><td width="23%"><span style="font-size:13px; color:#666">아이디</span></td>
	                    <td width="77%"><font color="#1f7cc8"><b><?=$this->session->userdata('userid')?></b></font></td></tr>
                    <tr><td colspan="2" height="15"></td></tr>
                    <tr><td><span style="font-size:13px; color:#666">비밀번호</span></td>
                    	<td>
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
			    </table>
			</td></tr>
            <tr><td align="center" valign="middle"><div class="bot_btn"><input type="submit" id="signup-button" class="sowsms-inp-submit" value="확인" /></div></td></tr>
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
        * 외부로부터 고객님의 정보를 안전하게 보호하기 위해 비밀번호를 다시 확인하고 있습니다.<br>
        * 비밀번호가 3회 이상 틀릴 경우 자동 로그아웃 됩니다.
</td></tr>
<tr><td height="30px"></td></tr>
</table>
        </td>
</tr>
</table>

</div>
<!-- content end -->
<script type="text/javascript">
function confirmAuth() {
	if ($("#passwd").val() == '') {
		alert("비밀번호를 입력하세요.");
		return false;
	}
	return true;
}
</script>