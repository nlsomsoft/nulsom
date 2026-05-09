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
    	<li class="on"><a href="/signup/id_form"><font color="#fff"><b>아이디 찾기</b></font></a></li>
    	<li><a href="/signup/passwd_form"><b>비밀번호 재설정</b></a></li>
    	</ul>
	</div>

    <div style="margin:20px 0px; border:1px solid #ddd; padding:8px 15px; background:#F1F1F1; font-size:13px">
    	<p> * 회원가입시 기재하셨던 정보를 입력해 주세요.<br>
			* 기억이 나지 않거나 정보가 일치하지 않는 경우에는 고객센터로 연락주시기 바랍니다.</p>
    </div>

<?php if ($result->userid != '') { ?>
    <div style="border:1px solid #CCCCCC; border-radius:5px; margin-bottom:50px">
		<table width="100%">
           	<tr><td height="50"></td></tr>
            <tr><td align="center" valign="middle"><span style="font-size:17px; color:#666"><b>아이디 찾기 결과입니다.</b></span></td></tr>
           	<tr><td height="30"></td></tr>
			<tr><td align="center">
				<table width="40%">
                    <tr><td width="23%" height="40"><span style="font-size:13px; color:#666">아이디</span></td>
                    	<td width="77%"><font color="#FF0000"><b><?=substr($result->userid, 0, -4).'****'?></b></font></td></tr>
                    <tr><td height="40"><span style="font-size:13px; color:#666">가입일</span></td>
                    	<td><b><?=mydate_format('Y-m-d', $result->add_date)?></b></td></tr>
			    </table>
			</td></tr>
            <tr><td align="center" valign="middle"><div class="bot_btn">
                        <span class="bc bc_org"><span id="next-button"><a href="/signup/passwd_form">비밀번호 재설정</a></span></span>
                    </div></td></tr>
           	<tr><td height="40"></td></tr>
		</table>
	</div>
<?php } else { ?>
    <div style="border:1px solid #CCCCCC; border-radius:5px; margin-bottom:50px">
		<table width="100%">
           	<tr><td height="50"></td></tr>
            <tr><td align="center" valign="middle"><span style="font-size:17px; color:#666"><b>조회결과가 없습니다.</b></span></td></tr>
            <tr><td align="center" valign="middle"><div class="bot_btn">
                        <span class="bc bc_org"><span id="next-button2"><a href="/signup/id_form">아이디 찾기</a></span></span>
                    </div></td></tr>
           	<tr><td height="40"></td></tr>
		</table>
	</div>
<?php } ?>
</div>


        </td>
</tr>
</table>

</div>
<!-- content end -->