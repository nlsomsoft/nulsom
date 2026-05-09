<?php if ($g_left_menu_flag == 'signup') { ?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td height="93" align="center" class="submenuTitle">회원관리</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
	<?php if ($this->session->userdata('logged_in') !== true) { ?>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/signup/join_agree');">회원가입</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/signup/id_form');">아이디/비밀번호찾기</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
	<?php } ?>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/signup/terms');">서비스 제약관</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
	</table>
<?php } else if ($g_left_menu_flag == 'address') { ?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr><td height="93" align="center" class="submenuTitle">주소록</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/address/group');">그룹별주소록</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/address/ban');">수신거부목록</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
	<?php if (STORE_080EXT_USEAGE_YN == 'Y') { ?>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/address/phone080');
		">080수신거부목록</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
	<?php } ?>
	</table>
<?php } else if ($g_left_menu_flag == 'sms') { ?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr><td height="93" align="center" class="submenuTitle">문자</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/sms/sms');">단문.장문</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/sms/photo');">포토문자</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/sms/switch');">내용바꿔</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
	</table>
<?php } else if ($g_left_menu_flag == 'adsms') { ?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr><td height="93" align="center" class="submenuTitle">광고문자</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/sms/adsms');">단문.장문</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/sms/adphoto');">포토문자</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/sms/adswitch');">내용바꿔</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/sms/adtext');">단문.장문 -텍스트</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/sms/adfile');">단문.장문 -파일 (Text)</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/sms/adexcel');">단문.장문 -파일 (Excel)</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
	</table>
<?php } else if ($g_left_menu_flag == 'elect_sms') { ?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr><td height="93" align="center" class="submenuTitle">선거문자</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/sms/newsms');">단문.장문 <img src="/images/vote_icon.png"></td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/sms/newphoto');">포토문자 &nbsp;<img src="/images/vote_icon.png"></td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
<?php /* ?>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/sms/newswitch');">내용바꿔 &nbsp;<img src="/images/vote_icon.png"></td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
<?php */ ?>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/sms/sms/elect');">단문.장문</div></td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/sms/photo/elect');">포토문자</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/sms/switch/elect');">내용바꿔</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
	</table>
<?php } else if ($g_left_menu_flag == 'result') { ?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr><td height="93" align="center" class="submenuTitle">결과통계</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/result/list');">발송결과</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
<?php /* ?>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/result/list1');">발송결과 (대량)</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
<?php */ ?>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/result/reserve');">예약발송</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/result/stats');">발송통계 - 상품</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/result/stats_daily');">발송통계 - 일별</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
	</table>
<?php } else if ($g_left_menu_flag == 'pay') { ?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td height="93" align="center" class="submenuTitle">결제관리</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/pay/list');">충전하기</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/pay/bill');">충전내역</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/pay/service');">서비스이용내역</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
	</table>
<?php } else if ($g_left_menu_flag == 'info') { ?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td height="93" align="center" class="submenuTitle">환경설정</td></tr>
		<tr><td height="10"></td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/info/intro');">회원정보변경</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/info/pform');">비밀번호변경</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/info/cblist');">발신번호관리</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/info/notice');">공지사항</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
		<tr><td height="45" align="left" class="submenuLink" onclick="location_href('/info/wform');">회원탈퇴</td></tr>
		<tr><td height="1" class="dot_line"></td></tr>
	</table>
<?php } ?>
