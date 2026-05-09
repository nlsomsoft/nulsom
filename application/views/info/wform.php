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
    <div class="body_title" style="position:relative;">회원탈퇴</div>

    <div style="margin:50px 100px; border:1px solid #CCCCCC; border-radius:10px">
<?php
$attributes = array(
    'id' => 'frmInfo',
    'onsubmit' => 'return dowithdrawal();'
);
echo form_open('info/withdrawal', $attributes);
?>
		<table width="100%">
           	<tr><td height="50"></td></tr>
            <tr><td align="center" valign="middle"><span style="font-size:17px; color:#666"><b>회원탈퇴를 원하시면 아래 내용을 확인해 주세요.</b></span></td></tr>
           	<tr><td height="30"></td></tr>
			<tr><td align="center">
				<table width="40%">
                    <tr><td height="40" width="23%"><span style="font-size:13px; color:#666">회원명</span></td>
	                    <td width="77%"><b><?=$this->session->userdata('realname')?></b></td></tr>
                    <tr><td height="40" width="23%"><span style="font-size:13px; color:#666">아이디</span></td>
	                    <td width="77%"><font color="#1f7cc8"><b><?=$this->session->userdata('userid')?></b></font></td></tr>
                    <tr><td height="40"><span style="font-size:13px; color:#666">비밀번호</span></td>
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
            <tr><td align="center" valign="middle"><div class="bot_btn"><input type="submit" id="signup-button" class="sowsms-inp-submit" value="회원탈퇴" /></div></td></tr>
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
        * 탈퇴 후, 기존 정보로는 재가입하실 수 없습니다.<br>
        * 전송내역 등의 데이터는 탈퇴일로부터 6개월간 보관됩니다.
</td></tr>
<tr><td height="30px"></td></tr>
</table>
        </td>
</tr>
</table>

</div>
<!-- content end -->
<script type="text/javascript">
var dowithdrawal = function() {
	if (!confirm("탈퇴 후, 기존 정보로는 재가입하실 수 없습니다.\n전송내역 등의 데이터는 탈퇴일로부터 6개월간 보관됩니다.\n삭제를 진행할가요?")) {
		return false;
	}
	if ($.trim($("#ipt_password").val()) == "") {
		alert("비밀번호를 입력하세요.");
		return false;
	}
	return true;
}
</script>