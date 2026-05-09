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
            <ol class="on_4">
                <li><span class="blind">약관동의</span></li>
                <li><span class="blind">가입확인</span></li>
                <li><span class="blind">정보입력</span></li>
                <li><span class="blind">가입완료</span></li>
            </ol></div>

			<div class="email_auth" style="margin-top:80px">
                 <img src="/images/welcome.gif" alt="" class="img"><br><br>
                 <img src="/images/img_post.gif" alt="" class="img">
                    <h2>
                        <span class="ls0">
                            <span id="ctl00_ContentPlaceHolder1_lblEmail"><?=$this->session->userdata('kmc_name')?>님 </span></span></h2>
                    <p>
                        회원 가입이 성공적으로 완료되었습니다.<br>
                        <span class="txt_2">상단 아이디 / 비빌번호 란을 입력하신 후 로그인해 주시기 바랍니다.</span></p>
                </div>

        </td>
	</tr>
</table>

</div>
<!-- content end -->