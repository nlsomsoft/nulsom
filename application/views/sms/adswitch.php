<!-- datepicker -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="/js/datepicker-ko.js?v=<?=CSS_JS_INST?>"></script>
<script src="/js/handsontable.full.min.js"></script>
<link rel="stylesheet" href="/css/handsontable.css">
<!-- /datepicker -->
<script type="text/javascript">
//<![CDATA[
$(function(){
	$("#rsv_date").datepicker({
		showOn: "button",
        buttonImage: "/images/ico_calendar.gif",
	    buttonImageOnly: true,
		buttonText: "Select date"
	});
});
//]]>
</script>

<!-- content start -->
<div class="body-inner-table" style="padding-top:20px; min-height:700px;">

	<table width="1200" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<!-- left menu start -->
		<td width="210" valign="top">
			<?php
				$g_left_menu_flag = ($svc == 'elect' ? 'elect_sms' : 'adsms');
				include_once(VIEWPATH.'/templates/left_menu.php');
			?>
        </td>
			<!-- left menu end -->
			<td width="30"></td>
			<td width="960" valign="top">

				<div class="content_wrap">
					<div class="body_title" style="position:relative;">내용바꿔
						<div class="faq_sch">
						<?php if ($this->session->userdata('pay_type') != '1') { ?>
							<div class="style_btn1" style="width:130px; margin-top:5px"><a href="#" onclick="lookAtSendCount();">발송가능건수 확인</a></div>
						<?php } ?>
						</div>
					</div>
				</div>

				<!-- main_body s -->
				<div style="margin-top:30px">
<?php
$attributes = array(
	'name' => 'message',
	'id' => 'message',
);
echo form_open('send/switch', $attributes);

$_vote_flag = '1';
if (SENT_MEMORY_YN == 'Y') {
    if ($cached_campaign_array['vote_flag'] == '' || $cached_campaign_array['vote_flag'] == '1') $_vote_flag = '2';
}

//send_type :1(단문) 2(장문) 3(포토)
$data = array(
	'send_type' => ($cached_campaign_array['send_type'] != '' ? $cached_campaign_array['send_type'] : '1'),
	'vote_flag' => $_vote_flag,
	'send_list' => '',
	'content' => '',
	'phone_080' => $this->session->userdata('phone_080'),
	'phone_ext' => $this->session->userdata('phone_ext'),
	'svc' => $svc,
	'ad_title_val' => ($this->session->userdata('ad_title') != '' ? '1' : '0'),
	'cate_flag' => '3', //1(문자) 2(선거) 3(광고)
);
echo form_hidden($data);

$data = array(
	'type'  => 'hidden',
	'name'  => 'send_data',
	'id'    => 'cvs_data',
	'value' => '',
);
echo form_input($data);
?>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td>
									<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
										<tr>
											<td width="380" valign="top" bgcolor="#FFFFFF" style="padding-left:15px">
												<div class="phone_bg">
													<div class="phone_bg_inner msg_sub_color" style="background-color:#deeaf5;">
														<div id="send_type_title_color" style="background-color:#7395b8;">
															<span id="msgType"><strong>단문</strong></span>
															<span class="f-right">
																<span id="msgByte">0</span> / <span id="maxByte">90</span> byte
															</span>
														</div>
										<?php
											$ad_title = $this->session->userdata('ad_title');
											if ($ad_title != '') $disp_ad_title = '(광고)'.$ad_title;
											else $disp_ad_title = '(광고)';
										?>
														<div class="msg_sub_color" style="margin-top:10px;">
															<input id="votemsgtop" name="vote_msg_top" type="text" class="phone_bg_msg" style="display:none;font-weight:600;font-size:13px;color:#4576c7;outline:none;" value="<?=($svc == 'elect' ? '(선거운동정보)' : $disp_ad_title)?>" readonly />
															<textarea style="background-color:#deeaf5;width:100%; height:369px;font-size:13px;color:#000;font-weight:600;" placeholder="90byte 초과 시 장문문자로 자동 전환됩니다." id="msgbody" name="message_body[]" onkeyup="countMsgBody();"><?=$cached_campaign_array['message_body']?></textarea>
													<div id="votemsgbottom" class="phone_bg_msg2" style="font-weight:600;font-size:13px;color:#4576c7;display:none;line-height:120%">
													<?php
														if ($svc == 'elect') {
															if ($this->session->userdata('phone_080')) {
																$vote_bottom_msg1 = '무료수신거부 '.$this->session->userdata('phone_080');
																if ($this->session->userdata('phone_ext')) {
																	$vote_bottom_msg1 .= ' 인증'.$this->session->userdata('phone_ext');
																}
																$vote_bottom_msg2 = '불법수집정보신고번호 118';
																$vote_bottom_msg = $vote_bottom_msg1.'<br />'.$vote_bottom_msg2;
															} else {
																$vote_bottom_msg1 = '';
																$vote_bottom_msg2 = '불법수집정보신고번호 118';
																$vote_bottom_msg = $vote_bottom_msg1.$vote_bottom_msg2;
															}
														} else {
															$vote_bottom_msg1 = '무료수신거부 '.$this->session->userdata('phone_080');
															if ($this->session->userdata('phone_ext')) {
																$vote_bottom_msg1 .= ' 인증'.$this->session->userdata('phone_ext');
															}
															$vote_bottom_msg2 = '';
															$vote_bottom_msg = $vote_bottom_msg1.$vote_bottom_msg2;
														}

														$data = array(
															'vote_bottom_msg1' => $vote_bottom_msg1,
															'vote_bottom_msg2' => $vote_bottom_msg2,
														);
														echo form_hidden($data);
														echo $vote_bottom_msg;
													?>
													</div>
														</div>
														<div class="d-table w100pro table-border" style="background-color:#888;">
															<div class="d-table-cell w50pro v-middle">
																<a id="base_1" href="#" style="color:#fff;" onclick="charLayerOpen('ext_char_layer', 'base_1'); return false;">특수문자 입력</a>
															</div>
															<div class="d-table-cell w50pro v-middle">
																<a href="#" style="color:#fff;" onclick="vote_msg_option(); return false;"><?=($svc == 'elect' ? '선거필수문구' : '광고.무료수신거부');?></a>
															</div>
														</div>
													</div>
												</div>
											</td>
											<td align="center" valign="middle" bgcolor="#FFFFFF">
											<table width="100%" border="0" cellpadding="0" cellspacing="0">
  													<tr><td><div class="style_btn" style="margin:5px 30px;"><a href="#" onClick="adde1('[*n*]');">이름변경</a></div></td></tr>
  													<tr><td><div class="style_btn" style="margin:5px 30px;"><a href="#" onClick="adde1('[*1*]');">내용변경 1</a></div></td></tr>
  													<tr><td><div class="style_btn" style="margin:5px 30px;"><a href="#" onClick="adde1('[*2*]');">내용변경 2</a></div></td></tr>
  													<tr><td><div class="style_btn" style="margin:5px 30px;"><a href="#" onClick="adde1('[*3*]');">내용변경 3</a></div></td></tr>
  													<tr><td><div class="style_btn" style="margin:5px 30px;"><a href="#" onClick="adde1('[*4*]');">내용변경 4</a></div></td></tr>
												</table>
                                            </td>
											<td width="380"  valign="top" bgcolor="#FFFFFF" style="padding-left:15px">
												<div class="phone_bg">
													<div class="phone_bg_inner msg_sub_color2" style="background-color:#deeaf5;">
														<div id="send_type_title_color2" style="background-color:#7395b8;">
															<span id="msgType2"><strong>단문</strong></span>
															<span class="f-right">
																<span id="msgByte2">0</span> / <span id="maxByte2">90</span> byte
															</span>
														</div>
														<div class="msg_sub_color2" style="margin-top:10px;">
															<input id="votemsgtop2" name="vote_msg_top" type="text" class="phone_bg_msg" style="display:none;font-weight:600;font-size:13px;color:#4576c7;outline:none;" value="<?=($svc == 'elect' ? '(선거운동정보)' : $disp_ad_title)?>" readonly />
															<textarea style="background-color:#deeaf5;width:100%; height:369px;font-size:13px;color:#000;font-weight:600;" placeholder="미리보기 확인창" id="msgbody2" name="msgbody2" readonly onkeyup="countMsgBody();"></textarea>
													<div id="votemsgbottom2" class="phone_bg_msg2" style="font-weight:600;font-size:13px;color:#4576c7;display:none;line-height:120%">
													<?php
														if ($svc == 'elect') {
															if ($this->session->userdata('phone_080')) {
																$vote_bottom_msg1 = '무료수신거부 '.$this->session->userdata('phone_080');
																if ($this->session->userdata('phone_ext')) {
																	$vote_bottom_msg1 .= ' 인증'.$this->session->userdata('phone_ext');
																}
																$vote_bottom_msg2 = '불법수집정보신고번호 118';
																$vote_bottom_msg = $vote_bottom_msg1.'<br />'.$vote_bottom_msg2;
															} else {
																$vote_bottom_msg1 = '';
																$vote_bottom_msg2 = '불법수집정보신고번호 118';
																$vote_bottom_msg = $vote_bottom_msg1.$vote_bottom_msg2;
															}
														} else {
															$vote_bottom_msg1 = '무료수신거부 '.$this->session->userdata('phone_080');
															if ($this->session->userdata('phone_ext')) {
																$vote_bottom_msg1 .= ' 인증'.$this->session->userdata('phone_ext');
															}
															$vote_bottom_msg2 = '';
															$vote_bottom_msg = $vote_bottom_msg1.$vote_bottom_msg2;
														}
														echo $vote_bottom_msg;
													?>
													</div>
														</div>
														<div class="d-table w100pro table-border" style="background-color:#888;">
															<div class="d-table-cell w50pro v-middle">
																<a href="#" id="export-string" style="color:#fff;">내용 미리보기</a>
															</div>
														</div>
													</div>
												</div>
											</td>
												</tr>
											</table>
										</td>
									</tr>

									<tr>
										<td align="center">
<table width="100%" border="1" style="border: 1px solid #bbb; margin-top:30px" cellpadding="0" cellspacing="1" bgcolor="#fff" class="class_tbl">
													<tr border="1" style="border: 1px solid #bbb;">
														<td width="100" height="50" align="center" bgcolor="#f7f7f7">제목</td>
														<td width="300" bgcolor="#FFFFFF" align="center"><input type="text" name="subject" id="msg_subject" maxlength="50" style="width:90%;" class="input_261" placeholder="제목입력(최대 50byte, 특수문자 제외)" value="<?=$cached_campaign_array['subject']?>" /></td>
														<td width="100" align="center" bgcolor="#f7f7f7">분할전송</td>
														<td bgcolor="#FFFFFF" align="center">
															<table width="90%" border="0" cellspacing="0" cellpadding="0">
																<tr>
																	<td width="35%" height="50">
																		<input type="radio" name="divide_yn" value="N" onclick="divide_check('0');" checked> 미사용&nbsp;
																		<input type="radio" name="divide_yn" value="Y" onclick="divide_check('1');"> 사용</td>
																	<td width="65%" align="left">
																		<input type="text" name="div_cnt" id="div_count" style="width:70px;text-align:center" class="input_261 disabled" />&nbsp;건씩&nbsp;&nbsp;
																		<input type="text" name="div_min" id="div_minute" style="width:70px; color:#F30; text-align:center" class="input_261 disabled" />&nbsp;분 간격
																	</td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr border="1" style="border: 1px solid #bbb;">
															<td height="50" align="center" bgcolor="#f7f7f7">발신번호</td>
															<td bgcolor="#FFFFFF" align="center">
																<select name="callback" class="input_261" style="width:90%;">
													<?php if ($this->session->userdata('callback') == false) { ?>
																	<option value="">발신번호를 등록하세요.</option>
													<?php } else { ?>
													<?php
														if (count($this->session->userdata('callback')) > 1) {
															echo "<option value=''>발신번호를 선택하세요.</option>";
														}
													?>
															<?php foreach ($this->session->userdata('callback') as $row) { ?>
																		<option value="<?=$row['callback']?>" <?=($cached_campaign_array['callback'] == $row['callback'] ? 'selected="selected"' : '')?>><?=phone_format($row['callback'])?><?php if ($row['name']) { ?>&nbsp;&nbsp;[<?=$row['name']?>]<?php } ?></option>
															<?php } ?>
													<?php } ?>
																</select>
															</td>
															<td align="center" bgcolor="#f7f7f7">전송시간</td>
															<td bgcolor="#FFFFFF" align="center">
																<table width="90%" border="0" cellspacing="0" cellpadding="0">
																<tr>
																	<td width="35%" height="50">
																		<input type="radio" name="reserve_yn" value="N" onclick="reserve_check('0');" checked="checked"> 즉시&nbsp;
																		<input type="radio" name="reserve_yn" value="Y" onclick="reserve_check('1');"> 예약</td>
															<?php
																	$rsv_hour = date('H',time() + 600);
																	if ($rsv_hour > 20) $rsv_date = date("Y-m-d",strtotime("+1 day", time()));
																	else $rsv_date = date('Y-m-d');;
															?>
																	<td width="65%" align="left">
                                  	<input type="text" id="rsv_date" name="rsv_date" style="float:left; width:100px; margin-right:3px;" class="input_261 disabled" readonly value="<?=$rsv_date?>" />&nbsp;&nbsp;

																	<select name="rsv_hour" id="rsv_hour" class="input disabled" style="height:24px;">
																<?php
																	// $rsv_hour = date('H',time() + 600);
																	for ($i = 8; $i < 20; $i++) {
																		// if ($i == 0) $name = '자정';
																		// else if ($i == 12) $name = '정오';
																		if ($i > 12) {
																			$name = '오후 '.($i - 12);
																		}
																		else $name = '오전 '.$i;
																		$selected = ($rsv_hour == $i ? 'selected="selected"' : '');
																		echo "<option value=\"{$i}\" {$selected}>{$name}</option>";
																	}
																?>
																	</select>시
																	<select name="rsv_min" id="rsv_min" class="input disabled" style="height:24px;" >
																<?php
																	$rsv_min = (int)(date('i',time() + 600) / 10);
																	for ($i = 0; $i < 6; $i++) {
																		$name = $i.'0';
																		$selected = ($rsv_min == $i ? 'selected="selected"' : '');
																		echo "<option value=\"{$i}0\" {$selected}>{$name}</option>";
																	}
																?>
																	</select>분
																	</td>
																	</tr>
																</table>
															</td>
														</tr>
														</table>

											<div class="bot_btn" style="margin-bottom:40px;">
												<a id="send_merge" style="color:#fff;" class="sowsms-inp-submit"/>전송하기</a>
											</div>
										</td>
									</tr>
								</table>
							</form>
						</div>
 <div style="font-size:12px; font-family:'굴림','굴림체','Arial'; margin-bottom:30px"><div class="handsontable" id="hot"></div></div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td><img src="/images/tip.gif" width="145" height="34" alt="" /></td></tr>
<tr><td height="1" bgcolor="#CCCCCC"></td></tr>
<tr>
	<td class="help_txt">
        * 발송되는 모든 번호는 중복 체크되며, 중복된 번호는 자동적으로 제거 됩니다. </br>
        * 주소록 메뉴의 직접 등록 '수신거부', '080수신거부'에 등록된 번호는 자동 제거 됩니다. </br>
	</td>
</tr>
<tr><td height="30px"></td></tr>
</table>

<!-- main_body e -->
</td>
</tr>
</table>

</div>
<!-- content end -->


<!-- 가이드 레이어 -->
<span id="d_layer" style="display:none; z-index:110;">
<div class="layer_bg" style="width:260px">
	<div class="layer_bg_inner">
		<div style="padding-left:15px"><strong>분할전송 안내</strong><span class="f-right" style="padding-right:15px; cursor:pointer" onclick="layerCloseNew('d_layer'); return false;">닫기</span></div></div>
		<div style="margin:10px 20px;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
        	<td height="20" align="left" width="22px"><img src="/images/ico_num2_1.gif" width="13" height="13" alt="" style="margin-top:-25px;" /> </td>
        	<td>분할전송 단위 건수는 최소 100건, 최대 3000건 입니다.</td>
        </tr>
        <tr>
        	<td height="20" align="left"><img src="/images/ico_num2_2.gif" width="13" height="13" alt="" style="margin-top:-25px;" /></td>
        	<td>분할전송 시간 간격은 최소 3분, 최대 60분 입니다.</td>
        </tr>
	 	</table>
		</div>
	</div>
</span>

<span id="b_layer" style="display:none; z-index:110;">
<div class="layer_bg" style="width:350px">
	<div class="layer_bg_inner">
		<div style="padding-left:15px"><strong>발송가능건수 확인</strong><span class="f-right" style="padding-right:15px; cursor:pointer" onclick="lookAtSendCount(); return false;">닫기</span></div></div>
		<div style="margin:10px 20px;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
        	<td height="20" align="left" width="22px"><img src="/images/ico_num2_1.gif" width="13" height="13" alt="" style="margin-top:-4px;" /> </td>
        	<td><div style="float:left;width:70px">단문문자</div><div style="float:left;width:90px">단가 <?=number_format($this->session->userdata('sms1'),2)?> 원</div><div style="float:left;color:#3253B2;">가능건수 <?=number_format(floor($this->session->userdata('cash') / $this->session->userdata('sms1')))?> 건</div></td>
        </tr>
        <tr>
        	<td height="20" align="left"><img src="/images/ico_num2_2.gif" width="13" height="13" alt="" style="margin-top:-4px;" /></td>
        	<td><div style="float:left;width:70px">장문문자</div><div style="float:left;width:90px">단가 <?=number_format($this->session->userdata('lms1'),2)?> 원</div><div style="float:left;color:#3253B2;">가능건수 <?=number_format(floor($this->session->userdata('cash') / $this->session->userdata('lms1')))?> 건</div></td>
        </tr>
        <tr>
        <tr>
        	<td height="20" align="left"><img src="/images/ico_num2_3.gif" width="13" height="13" alt="" style="margin-top:-4px;" /></td>
        	<td><div style="float:left;width:70px">포토문자</div><div style="float:left;width:90px">단가 <?=number_format($this->session->userdata('mms1'),2)?> 원</div><div style="float:left;color:#3253B2;">가능건수 <?=number_format(floor($this->session->userdata('cash') / $this->session->userdata('mms1')))?> 건</div></td>
        </tr>
	 	</table>
	</div>
</div>
</span>

<span id="c_layer" style="display:none; z-index:110;">
<div class="layer_bg" style="width:370px">
	<div class="layer_bg_inner">
		<div style="padding-left:15px"><strong>발신번호 사전 등록 안내</strong><span class="f-right" style="padding-right:15px; cursor:pointer" onclick="layerCloseNew( 'c_layer' ); return false;">닫기</span></div></div>
		<div style="margin:10px 20px;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
        	<td height="20" align="left" width="22px"><img src="/images/ico_num2_1.gif" width="13" height="13" alt="" style="margin-top:-4px;" /> </td>
        	<td>서비스 이용을 위해서 발신번호 사전 등록이 필요합니다.</td>
        </tr>
        <tr>
        	<td height="20" align="left"><img src="/images/ico_num2_2.gif" width="13" height="13" alt="" style="margin-top:-4px;" /></td>
        	<td><a href="/info/cblist">'환경설정 > 발신번호관리'</a>로 이동 후 등록하세요.</td>
        </tr>
        <tr>
        	<td height="20" align="left"><img src="/images/ico_num2_3.gif" width="13" height="13" alt="" style="margin-top:-4px;" /></td>
        	<td><a href="/info/cblist"><a href="/info/cblist">발신번호관리 서비스 바로가기</a></td>
        </tr>
	 	</table>
		</div>
	</div>
</span>
<span id="ad_info_layer" style="display:none; z-index:110;">
<div class="layer_bg" style="width:370px">
	<div class="layer_bg_inner">
		<div style="padding-left:15px"><strong>발신자명 사전 등록 안내</strong><span class="f-right" style="padding-right:15px; cursor:pointer" onclick="layerCloseNew('ad_info_layer'); return false;">닫기</span></div></div>
		<div style="margin:10px 20px;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
        	<td height="20" align="left" width="22px"><img src="/images/ico_num2_1.gif" width="13" height="13" alt="" style="margin-top:-4px;" /> </td>
        	<td>서비스 이용을 위해서 발신자명 등록이 필요합니다.</td>
        </tr>
        <tr>
        	<td height="20" align="left"><img src="/images/ico_num2_2.gif" width="13" height="13" alt="" style="margin-top:-4px;" /></td>
        	<td><a href="/info/intro">'환경설정 > 회원정보변경'</a>으로 이동 후 등록하세요.</td>
        </tr>
        <tr>
        	<td height="20" align="left"><img src="/images/ico_num2_3.gif" width="13" height="13" alt="" style="margin-top:-4px;" /></td>
        	<td><a href="/info/intro">발신자명 등록 서비스 바로가기</a></td>
        </tr>
	 	</table>
		</div>
	</div>
</span>

<!-- 080 레이어 -->
<span id="phone_080_layer" style="display:none; z-index:110;">
<div class="layer_bg" style="width:370px">
	<div class="layer_bg_inner">
		<div style="padding-left:15px"><strong>080 수신 거부 번호 신청 안내</strong><span class="f-right" style="padding-right:15px; cursor:pointer" onclick="layerCloseNew('phone_080_layer'); return false;">닫기</span></div></div>
		<div style="margin:10px 20px;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
        	<td height="20" align="left" width="22px"><img src="/images/ico_num2_1.gif" width="13" height="13" alt="" style="margin-top:-4px;" /> </td>
        	<td>080 수신 거부 번호를 신청하지 않았습니다.</td>
        </tr>
        <tr>
        	<td height="20" align="left"><img src="/images/ico_num2_2.gif" width="13" height="13" alt="" style="margin-top:-4px;" /></td>
        	<td>고객센터 <?=CALLCENTER?> 에 문의하시면 신청 가능합니다.</td>
        </tr>
	 	</table>
		</div>
	</div>
</span>
<!-- 특수문자 레이어 -->
<span id="ext_char_layer" style="display:none; z-index:100;">
<div class="layer_bg" style="width:300px">
	<div class="layer_bg_inner">
		<div style="padding-left:15px"><strong>특수문자 입력</strong><span class="f-right" style="padding-right:15px; cursor:pointer" onclick="layerCloseNew( 'ext_char_layer' ); return false;">닫기</span></div></div>
		<div style="margin:10px 20px;">
<table width="250" border="1" style="border: 1px solid #bbb;" cellspacing="0" cellpadding="0" bgcolor="#D7D7D7" class="class_tbl">
<tr align="center" border="1" style="border: 1px solid #bbb;" bgcolor="#FFFFFF">
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">♥</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">♡</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">★</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">☆</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">▶</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">▷</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">●</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">■</td>
</tr>
<tr align="center" border="1" style="border: 1px solid #bbb;" bgcolor="#FFFFFF">
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">▲</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">▒</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">♨</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">™</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">♪</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">♬</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">☜</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">☞</td>
</tr>
<tr align="center" border="1" style="border: 1px solid #bbb;" bgcolor="#FFFFFF">
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">♂</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">♀</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">◆</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">◇</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">♣</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">♧</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">☎</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">◀</td>
</tr>
<tr align="center" border="1" style="border: 1px solid #bbb;" bgcolor="#FFFFFF">
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">◁</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">○</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">□</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">▼</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">∑</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">㉿</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">◈</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">▣</td>
</tr>
<tr align="center" border="1" style="border: 1px solid #bbb;" bgcolor="#FFFFFF">
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">『</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">』</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">☜</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">♬</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">⌒</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">¸</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">˛</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">∽</td>
</tr>
<tr align="center" border="1" style="border: 1px solid #bbb;" bgcolor="#FFFFFF">
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">з</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">§</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">⊙</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">※</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">∴</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">¤</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">∂</td>
  <td width="30" onclick="adde2( this );" style="cursor:pointer;">▩</td>
</tr>
</table>
		</div>
	</div>
</span>

<script type="text/javascript">
$(document).ready(function() {
	reserve_check('0');
	divide_check('0');
	vote_msg_option();
<?php if ($this->session->userdata('callback') == false) { ?>
	var _location_base = document.getElementById("base_1");
	var _a = getOffsetPosition(_location_base);
	layerOpenLocationNew("c_layer", _a[0] + 31, _a[1] + 132);
<?php } ?>
<?php if ($this->session->userdata('ad_title') == '') { ?>
	var _location_base = document.getElementById("base_1");
	var _a = getOffsetPosition(_location_base);
	layerOpenLocationNew("ad_info_layer", _a[0] + 20, _a[1] - 395);
<?php } ?>
});

var add_vote_byte = vote_byte();
function vote_byte() {
	var svc = $.trim($("input[name=svc]").val());
	var phone_080 = $.trim($("input[name=phone_080]").val()).length;
	var phone_ext = $.trim($("input[name=phone_ext]").val()).length;
	if (svc == 'elect') {
		if (phone_080 == 0) return 40;
		if (phone_ext == 0) return (54 + phone_080);
		else return (59 + phone_080 + phone_ext);
	} else {
		var ad_title_val = $.trim($("input[name=ad_title_val]").val());
		var votemsgtop_leng = $("#votemsgtop").val().bytes2();
		var added_type = 14;
		if (ad_title_val == '1') var added_type = 15;
		return (votemsgtop_leng + added_type + phone_080);
		// if (phone_ext == 0) return (16 + phone_080);
		// else return (21 + phone_080 + phone_ext);
	}
}
function vote_msg_option() {
	var svc = $.trim($("input[name=svc]").val());
	var phone_080 = $.trim($("input[name=phone_080]").val());
	if (svc != 'elect' && phone_080 == '') {
		phone080LayerOpen('phone_080_layer', 'base_1');
		return false;
	}
	else if (svc == 'elect' && phone_080 == '') {
		phone080LayerOpen('phone_080_layer', 'base_1');
	}
	var vote_flag = $.trim($("input[name=vote_flag]").val());
	if (vote_flag == '1') {
		document.getElementById("votemsgtop").style.display = "block";
		document.getElementById("votemsgbottom").style.display = "block";
		document.getElementById("msgbody").style.height = "305px";
		$("input[name=vote_flag]").val("2");
	} else {
		document.getElementById("votemsgtop").style.display = "block";
		document.getElementById("votemsgbottom").style.display = "block";
		document.getElementById("msgbody").style.height = "305px";
		$("input[name=vote_flag]").val("2");
	}
	countMsgBody();
}
function phone080LayerOpen(_layer, _base) {
	var _target = document.getElementById(_layer);
	var _location_base = document.getElementById(_base);
	var _a = getOffsetPosition(_location_base);
	layerOpenLocationNew("phone_080_layer", _a[0] - 94, _a[1] + 22);
}
function charLayerOpen(_layer, _base) {
	var _target = document.getElementById(_layer);
	var _location_base = document.getElementById(_base);
	var _a = getOffsetPosition(_location_base);
	layerOpenLocationNew("ext_char_layer", _a[0] - 43, _a[1] + 22);
}
function layerCloseNew(_layer) {
	var _target = document.getElementById(_layer);
	_target.style.display = "none";
	return _target;
}
function layerOpenLocationNew(_layer, X, Y) {
	var _target = document.getElementById( _layer );
	_target.style.display = "inline";
	_target.style.position = "absolute";
	_target.style.top = Y + "px";
	_target.style.left = X + "px";
	return _target;
}

function getOffsetPosition(_element) {
	var _return = new Array()

	var _top = _element.offsetTop;
	var _left = _element.offsetLeft;
	while ((_element = _element.offsetParent) != null) {
		_top += _element.offsetTop;
		_left += _element.offsetLeft;
	}
	_return[0] = _left;			// X
	_return[1] = _top;			// Y
	return _return;
}

function divide_check(div) {
	if (div == "0") {
		$("input[name=div_min").disabled = true;
		$("input[name=div_cnt").disabled = true;
		$("input[name=div_min").value = '';
		$("input[name=div_cnt").value = '';

		$("input[name=div_min").addClass("disabled");
		$("input[name=div_cnt").addClass("disabled");
		layerCloseNew('d_layer');
	} else {
		$("input[name=div_cnt").disabled = false;
		$("input[name=div_cnt").disabled = false;
		$("input[name=div_min").removeClass("disabled");
		$("input[name=div_cnt").removeClass("disabled");
		var _location_base = document.getElementById("base_1");
		var _a = getOffsetPosition(_location_base);
		layerOpenLocationNew("d_layer", _a[0] + 290, _a[1] + 115);
	}
}

function reserve_check(v1) {
	if (v1 == "0") {
		$("#rsv_date").disabled = true;
		$("#rsv_hour").disabled = true;
		$("#rsv_min").disabled = true;
  	$("#rsv_date").addClass("disabled");
		$("#rsv_hour").addClass("disabled");
		$("#rsv_min").addClass("disabled");
		$("input[name=rsv_date]").datepicker("disable").removeAttr("disabled");
	} else {
		$("#rsv_date").disabled = false;
		$("#rsv_hour").disabled = false;
		$("#rsv_min").disabled = false;
		$("#rsv_date").removeClass("disabled");
		$("#rsv_hour").removeClass("disabled");
		$("#rsv_min").removeClass("disabled");
		$("input[name=rsv_date]").datepicker("enable").removeAttr("enabled");
	}
}
function adde1( txt ) {
	insertAtCaret('msgbody', txt);
	countMsgBody();
}

function adde2(td) {
	var str = td.innerHTML;
	insertAtCaret('msgbody', str);
	countMsgBody();
}

function insertAtCaret(areaId, text) {
	var txtarea = document.getElementById(areaId);
	if (!txtarea) {
		return;
	}

	var scrollPos = txtarea.scrollTop;
	var strPos = 0;
	var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? "ff" : (document.selection ? "ie" : false));
	if (br == "ie") {
		txtarea.focus();
		var range = document.selection.createRange();
		range.moveStart('character', -txtarea.value.length);
		strPos = range.text.length;
	} else if (br == "ff") {
		strPos = txtarea.selectionStart;
	}

	var front = (txtarea.value).substring(0, strPos);
	var back = (txtarea.value).substring(strPos, txtarea.value.length);
	txtarea.value = front + text + back;
	strPos = strPos + text.length;
	if (br == "ie") {
		txtarea.focus();
		var ieRange = document.selection.createRange();
		ieRange.moveStart('character', -txtarea.value.length);
		ieRange.moveStart('character', strPos);
		ieRange.moveEnd('character', 0);
		ieRange.select();
	} else if (br == "ff") {
		txtarea.selectionStart = strPos;
		txtarea.selectionEnd = strPos;
		txtarea.focus();
	}
	txtarea.scrollTop = scrollPos;
}

function countMsgBody() {
	// var v = typeof v !== 'undefined' ? v : '1';
	var change_byte = 90;
	var limit_byte = 2000;

	var msg = document.getElementById("msgbody").value;
	var bytes = msg.bytes2();
	if ($("input[name=vote_flag]").val() == '2') bytes += add_vote_byte;

	if (bytes > change_byte) {
		changeBodyForm('2');
	} else {
		changeBodyForm('1');
	}

	if (bytes > limit_byte) {
		if ($("input[name=vote_flag]").val() == '2') var check_byte = limit_byte - add_vote_byte;
		else var check_byte = limit_byte;
		var l = 0;
		for (var i = 0; i < msg.length; i++) {
			if (msg.charCodeAt(i) == 13 ) continue;
			l += (msg.charCodeAt(i) > 128) ? 2 : 1;
			if (l > check_byte) {
				msg = msg.substring(0, i);
				alert(limit_byte + "byte 까지 입력하실 수 있습니다.");
				break;
			}
		}
		document.getElementById("msgbody").value = msg;
		bytes = msg.bytes2();
		if ($("input[name=vote_flag]").val() == '2') bytes += add_vote_byte;
	}
	document.getElementById("msgByte").innerHTML = bytes;
}
function countPreMsgBody() {
	// var v = typeof v !== 'undefined' ? v : '1';
	var change_byte = 90;
	var limit_byte = 2000;

	var msg = document.getElementById("msgbody2").value;
	var bytes = msg.bytes2();
	if ($("input[name=vote_flag]").val() == '2') bytes += add_vote_byte;

	if (bytes > change_byte) {
		changePreBodyForm('2');
	} else {
		changePreBodyForm('1');
	}

	if (bytes > limit_byte) {
		if ($("input[name=vote_flag]").val() == '2') var check_byte = limit_byte - add_vote_byte;
		else var check_byte = limit_byte;
		var l = 0;
		for (var i = 0; i < msg.length; i++) {
			if (msg.charCodeAt(i) == 13 ) continue;
			l += (msg.charCodeAt(i) > 128) ? 2 : 1;
			if (l > check_byte) {
				msg = msg.substring(0, i);
				alert(limit_byte + "byte 까지 입력하실 수 있습니다.");
				break;
			}
		}
		document.getElementById("msgbody2").value = msg;
		bytes = msg.bytes2();
		if ($("input[name=vote_flag]").val() == '2') bytes += add_vote_byte;
	}
	document.getElementById("msgByte2").innerHTML = bytes;
}
var changeBodyForm = function (v1) {
	if (v1 == '2') {
		$("#msgType").html("<strong>장문</strong>");
		$("#maxByte").html("2000");
		$("#send_type_title_color").css("background-color","#ff7e30");
		$("#msgbody").css("background-color","#ffeed9");
		$(".msg_sub_color").css("background-color","#ffeed9");
		$("#msg_subject").attr("placeholder", "제목입력(최대 50byte, 특수문자 제외)");
		$("input[name=send_type]").val("2");
	} else {
		$("#msgType").html("<strong>단문</strong>");
		$("#maxByte").html("90");
		$("#send_type_title_color").css("background-color","#7395b8");
		$("#msgbody").css("background-color","#deeaf5");
		$(".msg_sub_color").css("background-color","#deeaf5");
		$("#msg_subject").attr("placeholder", "제목입력(최대 50byte, 특수문자 제외)");
		$("input[name=send_type]").val("1");
	}
}
var changePreBodyForm = function (v1) {
	if (v1 == '2') {
		$("#msgType2").html("<strong>장문</strong>");
		$("#maxByte2").html("2000");
		$("#send_type_title_color2").css("background-color","#ff7e30");
		$(".msg_sub_color2").css("background-color","#ffeed9");
		$("#msgbody2").css("background-color","#ffeed9");
		$("#msg_subject").attr("placeholder", "제목입력(최대 50byte, 특수문자 제외)");
		$("input[name=send_type]").val("2");
	} else {
		$("#msgType2").html("<strong>단문</strong>");
		$("#maxByte2").html("90");
		$("#send_type_title_color2").css("background-color","#7395b8");
		$(".msg_sub_color2").css("background-color","#deeaf5");
		$("#msgbody2").css("background-color","#deeaf5");
		$("#msg_subject").attr("placeholder", "제목입력(최대 50byte, 특수문자 제외)");
		$("input[name=send_type]").val("1");
	}
}
</script>
<script type="text/javascript">
var preview = false;
var dataObject = [];
var hotElement = document.querySelector('#hot');
var hotElementContainer = hotElement.parentNode;
var hotSettings = {
	data: dataObject,
	columns: [{data:'var1',type:'text'},{data:'var2',type:'text'},{data:'var3',type:'text'},{data:'var4',type:'text'},{data:'var5',type:'text'},{data:'var6',type:'text'}],
	stretchH: 'all',
  width: 960,
  height: 395,
  minSpareRows: 10000,
  maxRows: 10000,
	autoWrapRow: true,
  rowHeaders: true,
	contextMenu:true,
	currentRowClassName : 'currentRow',
  colHeaders: ['핸드폰','이름','[*1*]','[*2*]','[*3*]','[*4*]'],
	colWidths: [50,50],
	manualColumnResize: true,
	outsideClickDeselects : true,
	afterLoadData : function() {
	},
	contextMenu: {
		items: {
		"row_above": {
			name: '행 추가(위)'
			},
		"row_below": {
			name: '행 추가(아래)'
			},
		"remove_row": {
			name: '행 삭제'
			},
		}
	}
};

var hot = new Handsontable(hotElement, hotSettings);
var buttons = {
	string: document.getElementById('export-string'),
	string2: document.getElementById('send_merge')
};
var exportPlugin = hot.getPlugin('exportFile');
var send_data = document.getElementById('cvs_data');

buttons.string.addEventListener('click', function() {
	var reVal = exportPlugin.exportAsString('csv', {
		columnDelimiter: '|^|'
	});

	var arrayList = reVal.split("\n");
	var firstrow = arrayList[0];
	if (firstrow.bytes2() == 17)  {
		if (document.getElementById("msgbody").value == '') {
			alert("문자 내용을 입력하세요..");
		} else {
			alert("첫째줄을 입력하셔야 합니다.");
		}
		document.getElementById("msgbody2").value = '';
		preview = false;
		return true;
	}
	var repVal = firstrow.replace(/\"/g, "");
	var repVal = repVal.replace( /(\s+$)/g, "" );
	var repVal = repVal.replace( /(^\s*)/g, "" );
	var lines = repVal.split("|^|");
	// var _sm = document.getElementsByName( "sms_msg[]" );
	var _sm = document.getElementsByName("message_body[]");
	var org_msg = _sm[0].value;
	for (var i = 0; i < 10; i++) {
		org_msg = org_msg.replace( /\[\*n\*\]/, lines[1] );
		org_msg = org_msg.replace( /\[\*1\*\]/, lines[2] );
		org_msg = org_msg.replace( /\[\*2\*\]/, lines[3] );
		org_msg = org_msg.replace( /\[\*3\*\]/, lines[4] );
		org_msg = org_msg.replace( /\[\*4\*\]/, lines[5] );
	}

	if ($("input[name=vote_flag]").val() == '2') {
		document.getElementById("votemsgtop2").style.display = "block";
		document.getElementById("votemsgbottom2").style.display = "block";
		document.getElementById("msgbody2").style.height = "305px";
	} else {
		document.getElementById("votemsgtop2").style.display = "none";
		document.getElementById("votemsgbottom2").style.display = "none";
		document.getElementById("msgbody2").style.height = "369px";
	}

	document.getElementById("msgbody2").value = org_msg;
	countPreMsgBody();
	preview = true;
	return false;
});

buttons.string2.addEventListener('click', function() {
	var reVal = exportPlugin.exportAsString('csv', {
		columnDelimiter: '|^|'
	});

	send_data.value = reVal;
	var arrayList = reVal.split("\n");
	var firstrow = arrayList[0];

	// var limit_byte = 0;
	// var kind = parseInt(document.getElementById("msgtype").value);
	// if (kind > 0) limit_byte = 2000;
	// else limit_byte = 90;

	if (preview == false ||
			firstrow.bytes2() == 17 || document.getElementById("msgbody").value == "")
	{
		alert( "미리보기 확인을 해주세요." );
		document.getElementById("msgbody2").value = "";
		return false;
	}

	var send_list = '';
	var is_send = true;
	var lms_notice = false;
	for (var i = 0; i < arrayList.length; i++) {
		var repVal = '';
		repVal = arrayList[i].replace(/\"/g, "");
		repVal = repVal.replace(/(\s+$)/g, "");
		repVal = repVal.replace(/(^\s*)/g, "");

		var lines = repVal.split("|^|");
		if (lines[0] == '') continue;

		if (send_list != '') send_list += "|,|";
		send_list += repVal;

		// var _sm = document.getElementsByName( "sms_msg[]" );
		var _sm = document.getElementsByName("message_body[]");
		var org_msg = _sm[0].value;
		for (var p = 0; p < 10; p++) {
			org_msg = org_msg.replace( /\[\*n\*\]/, lines[1] );
			org_msg = org_msg.replace( /\[\*1\*\]/, lines[2] );
			org_msg = org_msg.replace( /\[\*2\*\]/, lines[3] );
			org_msg = org_msg.replace( /\[\*3\*\]/, lines[4] );
			org_msg = org_msg.replace( /\[\*4\*\]/, lines[5] );
		}
		var msgbyte = org_msg.bytes2();
		if ($("input[name=vote_flag]").val() == '2') msgbyte += add_vote_byte;

		if (lms_notice == false && msgbyte > 90) {
			alert ("90 Byte를 초과하는 행이 있습니다. 모든 메시지는 장문으로 발송 됩니다.\n[" + ( i + 1 ) + " 번째 라인]");
			lms_notice = true;
		}

		if (msgbyte > 2000) {
			alert ("2000 Byte를 초과하는 행이 있습니다.\n[" + ( i + 1 ) + " 번째 라인]");
			is_send = false;
			break;
		}
	}

	if (is_send == false) return false;
	registerMessages(send_list,lms_notice);
});

var _send_lock = false;
function registerMessages(send_list,lms_notice) {
	countMsgBody();

	if (_send_lock != false) {
		alert("발송 진행 중 입니다. 잠시만 기다려주세요.");
		return false;
	}

	if ($("input[name=ad_title_val]").val() != "1") {
		alert("발신자명을 등록하세요. \"환경설정\" > \"회원정보변경\" 으로 이동하세요.");
		return false;
	}

	var form = document.message;
	if ($("#msgbody").val() == "") {
		alert("발송하실 내용을 입력하세요.");
		return false;
	}

	if (!DoCheckLang($("#msgbody").val())) return false;

	if (form.divide_yn.value == 'Y') {
		var div_cnt = document.getElementById("div_count").value;
		var div_count = parseInt(document.getElementById("div_count").value);
		if (div_cnt == '' || div_count < 100 || div_count > 3000) {
			alert("분할전송 단위 건수를\n100건 ~ 3000건 사이로 설정해 주세요.");
			return false;
		}
		var div_min = document.getElementById("div_minute").value;
		var div_minute = parseInt(document.getElementById("div_minute").value);
		if (div_min == '' || div_minute < 3 || div_minute > 60) {
			alert("분할전송 시간 간격을\n3분 이상 60분 이내로 설정해 주세요.");
			return false;
		}
	}
	if (form.reserve_yn.value == 'Y') {
		if (document.getElementById("rsv_date").value == "") {
			alert("전송시간을 선택하세요.");
			return false;
		}
	}

	var i = form.callback.options.selectedIndex;
	if (form.callback.options[i].value.bytes() < 2) {
		alert("발신번호를 선택해 주세요.");
		return false;
	}

	var svc = $.trim($("input[name=svc]").val());
	var vote_bottom_msg1 = $("input[name=vote_bottom_msg1]").val();
	var vote_bottom_msg2 = $("input[name=vote_bottom_msg2]").val();
	var content = '';
	if ($("input[name=vote_flag]").val() == '2') {
		content = $("#votemsgtop").val();
		var ad_title_val = $.trim($("input[name=ad_title_val]").val());
		if (svc == 'elect' || ad_title_val == '1') content += "\n";
		content += $("#msgbody").val();
		content += "\n";
		if (vote_bottom_msg1 != '' && vote_bottom_msg2 != '') {
			content += $("input[name=vote_bottom_msg1]").val();
			content += "\n";
			content += $("input[name=vote_bottom_msg2]").val();
		} else {
			if (vote_bottom_msg1 != '') content += $("input[name=vote_bottom_msg1]").val();
			if (vote_bottom_msg2 != '') content += $("input[name=vote_bottom_msg2]").val();
		}
	} else {
		content = $("#msgbody").val();
	}
	$("input[name=content]").val(content);

	if (lms_notice == true) $("input[name=send_type]").val("2");

	_send_lock = true;
	form.send_list.value = send_list;
	// form.mode.value = "confirm";
	$("input[name=send_data]").val("");
	form.submit();
}

var _look = 0;
var lookAtSendCount = function () {
	if (_look == 0) {
		var _location_base = document.getElementById("base_1");
		var _a = getOffsetPosition(_location_base);
		layerOpenLocationNew("b_layer", _a[0] + 528, _a[1] - 500); //left,top
		_look = 1;
	} else {
		layerCloseNew('b_layer');
		_look = 0;
	}
}
</script>
