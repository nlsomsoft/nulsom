<!-- datepicker -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="/js/datepicker-ko.js?v=<?=CSS_JS_INST?>"></script>
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
				$g_left_menu_flag = 'adsms';
				include_once(VIEWPATH.'/templates/left_menu.php');
			?>
			</td>
			<!-- left menu end -->
			<td width="30"></td>
			<td width="960" valign="top">

				<div class="content_wrap">
					<div class="body_title" style="position:relative;">단문.장문
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
// error_log(SENDNAME_EXCEPT_LINE, 0);
// error_log($this->session->userdata('ch_sms'), 0);
// error_log($this->session->userdata('ch_lms'), 0);
// error_log($this->session->userdata('ch_mms'), 0);

$attributes = array(
	'name' => 'message',
	'id' => 'message',
	'onsubmit' => 'return registerMessages();'
);
echo form_open('send/sms', $attributes);

$data = array(
	'send_type' => ($cached_campaign_array['send_type'] != '' ? $cached_campaign_array['send_type'] : '1'),
	'vote_flag' => '1',
	'send_list' => '',
	'content' => '',
	'keyinfo' => $this->session->userdata('keyinfo'),
	'phone_080' => $this->session->userdata('phone_080'),
	'phone_ext' => $this->session->userdata('phone_ext'),
	'svc' => $svc,
	'ad_title_val' => ($this->session->userdata('ad_title') != '' ? '1' : '0'),
);
echo form_hidden($data);
?>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td>
									<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
										<tr>
											<td width="380" valign="top" align="center" bgcolor="#FFFFFF">
												<!-- msg phone s-->

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
															<textarea style="background-color:#deeaf5;width:100%; height:369px;font-size:13px;color:#000;font-weight:600;" placeholder="080번호 자동입력, 90byte 초과 시 자동 장문 전환됩니다." id="msgbody" name="message_body" onkeyup="countMsgBody();"><?=$cached_campaign_array['message_body']?></textarea>
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
																<a href="#" style="color:#fff;" onclick="vote_msg_option(); return false;"> <?=($svc == 'elect' ? '선거필수문구' : '광고.무료수신거부');?></a>
															</div>
														</div>
														<div class="d-table w100pro table-border" style="background-color:#888;">
															<div class="d-table-cell w50pro v-middle">
																<a href="/sms/msg_saved" onclick="winPop(this.href, {name:'MsgDataPop',width:735,height:700}); return false;" style="color:#fff;">메시지 보관함</a>
															</div>
															<div class="d-table-cell w50pro v-middle">
																<a href="#" style="color:#fff;" onclick="save_message();">문자내용 저장</a>
															</div>
														</div>
													</div>
												</div>

												<!-- msg phone e -->
											</td>
											<td width="20" bgcolor="#FFFFFF"></td>
											<td width="560" valign="top" bgcolor="#FFFFFF">
												<!-- msg input s -->

												<table width="100%" border="1" style="border: 1px solid #bbb;" cellpadding="0" cellspacing="1" bgcolor="#fff" class="class_tbl">
													<tr border="1" style="border: 1px solid #bbb;">
														<td width="150" height="50" align="center" bgcolor="#f7f7f7">제목</td>
														<td width="410" bgcolor="#FFFFFF" align="center"><input type="text" name="subject" id="msg_subject" maxlength="50" style="width:90%;" class="input_261" placeholder="제목입력(최대 50byte)" value="<?=$cached_campaign_array['subject']?>" /></td>
													</tr>
													<tr border="1" style="border: 1px solid #bbb;">
														<td align="center" bgcolor="#f7f7f7"><strong>받는사람</strong></td>
														<td align="center" bgcolor="#FFFFFF">
															<table width="90%" border="0" cellspacing="0" cellpadding="0">
																<tr><td height="10"></td></tr>
																<tr>
																	<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
																		<tr>
																			<td width="60%"><input type="text" name="p_mobile" class="input_261" style="width:100%;" placeholder="핸드폰번호 직접입력" onblur="checkMobile(this);" onkeydown="onlyNumberCheck2('addReceiveNumber2()');" /></td>
																			<td width="40%" align="right"><div class="style_btn" style="width:95%;"><a href="#" onclick="addReceiveNumber(); return false;">추가</a></div></td>
																		</tr>
																		<tr height="60px">
																			<td width="60%" colspan="2">
																				<div style="margin-top:10px">
																				<span class="bot_btn">
																					<span class="bc bc_wht">
																						<span style="width:115px;"><a href="/append/group_popup" onclick="winPop(this.href, {name:'inputDataPop',width:610,height:700}); return false;" class="inputDataPopup" style="text-align:left;padding-left:14px;">주소록불러오기</a></span>
																					</span>
																				</span>
																				<span class="bot_btn">
																					<span class="bc bc_wht">
																						<span style="width:115px;"><a href="/append/text_popup" onclick="winPop(this.href, {name:'inputDataPop',width:610,height:700}); return false;" class="inputDataPopup" style="text-align:left;padding-left:19px;">파일붙여넣기</a></span>
																					</span>
																				</span>
																				<span class="bot_btn">
																					<span class="bc bc_wht">
																						<span style="width:115px;"><a href="/append/excel_popup" onclick="winPop(this.href, {name:'inputDataPop',width:610,height:700}); return false;" class="inputDataPopup" style="text-align:left;padding-left:19px;">엑셀붙여넣기</a></span>
																					</span>
																				</span>

																				</div>

																			</td>
																		</tr>

																	</table></td>
																</tr>
																<tr><td height="10"></td></tr>
																<tr>
																	<td height="90"><table width="100%" border="0" cellspacing="0" cellpadding="0">
																		<tr>
																			<td>
																				<select name="send_targets" multiple style="width:100%; height:140px; padding-top:5px" class="input_261"></select>
																			</td>
																		</tr>
																		<tr>
																			<td height="5"></td>
																		</tr>
																		<tr>
																			<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
																				<tr>
																					<td><strong>총 <span id="target_count" style="color:#ec4000;font-weight:bold;">0</span> 명</strong></td>
																					<td align="right"><div class="style_btn" style="width:50px;"><a href="#" onclick="removeItem(); return false;">삭제</a></div></td>
																				</tr>
																			</table></td>
																		</tr>
																		<tr>
																			<td height="10"></td>
																		</tr>
																	</table></td>
																</tr>
															</table></td>
														</tr>
														<tr border="0" style="border: 1px solid #bbb;">
															<td align="center" bgcolor="#f7f7f7">분할전송</td>
															<td bgcolor="#FFFFFF" align="center">
																<table width="90%" border="0" cellspacing="0" cellpadding="0">
																	<tr>
																		<td width="35%" height="50">
																			<input type="radio" name="divide_yn" value="N" onclick="divide_check('0');" checked> 미사용&nbsp;
																			<input type="radio" name="divide_yn" value="Y" onclick="divide_check('1');"> 사용</td>
																		<td width="65%" align="center">
																			<input type="text" name="div_cnt" id="div_count" style="width:70px;text-align:center" class="input_261 disabled" />&nbsp;건씩&nbsp;&nbsp;
																			<input type="text" name="div_min" id="div_minute" style="width:70px; color:#F30; text-align:center" class="input_261 disabled" />&nbsp;분 간격
																		</td>
																		</tr>
																	</table>
																</td>
															</tr>
															<tr border="1" style="border: 1px solid #bbb;">
																<td height="70" align="center" bgcolor="#f7f7f7">전송시간</td>
																<td bgcolor="#FFFFFF" align="center">
																	<table width="90%" border="0" cellspacing="0" cellpadding="0">
																		<tr>
																			<td height="30">
																			<input type="radio" name="reserve_yn" value="N" onclick="reserve_check('0');" checked="checked"> 즉시&nbsp;
																			<input type="radio" name="reserve_yn" value="Y" onclick="reserve_check('1');"> 예약
																			</td>
																		</tr>
																		<tr>
															<?php
																	$rsv_hour = date('H',time() + 600);
																	if ($rsv_hour > 20) $rsv_date = date("Y-m-d",strtotime("+1 day", time()));
																	else $rsv_date = date('Y-m-d');;
															?>
																			<td height="30">
																				<input type="text" id="rsv_date" name="rsv_date" style="float:left;margin-right:3px;" class="input_261 disabled" readonly value="<?=$rsv_date?>" />&nbsp;&nbsp;
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
															</tr>
														</table>
													</td>
												</tr>
											</table>
											<!-- msg input e -->
										</td>
									</tr>

									<tr>
										<td colspan="3" align="center">
											<div class="bot_btn" style="margin-bottom:50px;">
												<input type="submit" id="signup-button" class="sowsms-inp-submit" value="전송하기" />&nbsp;&nbsp;
											</div>
										</td>
									</tr>
								</table>
							</form>
						</div>
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
		<div style="padding-left:15px"><strong>발신번호 사전 등록 안내</strong><span class="f-right" style="padding-right:15px; cursor:pointer" onclick="layerCloseNew('c_layer'); return false;">닫기</span></div></div>
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
		<div style="padding-left:15px"><strong>특수문자 입력</strong><span class="f-right" style="padding-right:15px; cursor:pointer" onclick="layerCloseNew('ext_char_layer'); return false;">닫기</span></div></div>
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
	layerOpenLocationNew("c_layer", _a[0] + 476, _a[1] - 5);
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
		// document.getElementById("votemsgtop").style.display = "none";
		// document.getElementById("votemsgbottom").style.display = "none";
		// document.getElementById("msgbody").style.height = "369px";
		// $("input[name=vote_flag]").val("1");
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
	var _target = document.getElementById( _layer );
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
	while( ( _element = _element.offsetParent ) != null )
	{
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
		layerOpenLocationNew("d_layer", _a[0] + 337, _a[1] - 88);
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

function addReceiveNumber() {
	var p_mobile = document.message.p_mobile;
	if (isMobileNumber(p_mobile.value ) == false) {
		alert("핸드폰 번호가 아닙니다. 확인해주세요.");
		return false;
	}
	addTarget("P", p_mobile.value.trim(), p_mobile.value.trim(), 1, "");
	p_mobile.value = "";
	return true;
}

function removeItem() {
	var send_targets = document.message.send_targets;
	if (send_targets.selectedIndex < 0) {
		alert( "삭제하실 주소록 또는 개별주소를 선택해 주세요." );
		return;
	}
	for (var i = send_targets.options.length - 1; i >= 0; i--) {
		if (send_targets.options[i] == null || send_targets.options[i].selected != true) continue;

		var delobj = send_targets.options[i].value;
		var splobj = delobj.split("|");
		send_targets.options[i] = null;

		target_count = target_count + (splobj[2] * (-1));
		displayCount();
	}
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

function displayCount() {
	document.getElementById("target_count").innerHTML = target_count;
}

function addItem (type, no, text, count, extra) {
	var send_targets = document.message.send_targets;
	if (count < 1) return false;
	if (type == "G") {
		for (var i = 0; i < send_targets.options.length; i++) {
			var spl = send_targets.options[i].value.split("|");
			if (spl[0] == "G" && spl[1] == no) return false;
		}

		text += " (" + count + "명)";
	}
	else if (type == "M") { //이름 포함
		for (var i = 0; i < send_targets.options.length; i++) {
			var spl = send_targets.options[i].value.split( "|" );
			if (spl[0] == "M" && spl[1] == no) return false;
		}

		text += " (" + count + "명)";
	}
	else if (type == "P") {
		for (var i = 0; i < send_targets.options.length; i++) {
			var spl = send_targets.options[i].value.split("|");
			if (spl[0] == "P" && spl[1] == no) return false;  //중복제거
		}
	}
	else if (type == "S") {
		text += " (" + count + "명 선택)";
	}

	var newItem = new Option;
	newItem.text = text;
	if (type == "S") newItem.value = type + "|" + no + "|" + count + "|" + extra;
	else if (type == "P") newItem.value = type + "|" + no + "|" + count + "||" + extra;
	else newItem.value = type + "|" + no + "|" + count;

	send_targets.options[send_targets.length] = newItem;
	target_count += parseInt(count);
	displayCount();
}

function addTarget(type, no, text, count, extra) {
	var send_targets = document.message.send_targets;
	if (count < 1) return false;

	for (var i = 0; i < send_targets.options.length; i++) {
		var spl = send_targets.options[i].value.split("|");
		if (spl[1] == no) return false; // 중복입력 차단
	}
	var newItem = new Option;
	newItem.text = text;
	newItem.value = type + "|" + no + "|" + count + "||" + extra;
	send_targets.options[send_targets.length] = newItem;
	target_count += parseInt(count);
	displayCount();
}

function countMsgBody() {
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

var changeBodyForm = function (v1) {
	if (v1 == '2') {
		$("#msgType").html("<strong>장문</strong>");
		$("#maxByte").html("2000");
		$("#msgbody").css("background-color","#ffeed9");
		$("#msg_subject").attr("placeholder", "제목입력(최대 50byte, 특수문자 제외)");
		$(".msg_sub_color").css("background-color","#ffeed9");
		$("#send_type_title_color").css("background-color","#ff7e30");
		$("input[name=send_type]").val("2");
	} else {
		$("#msgType").html("<strong>단문</strong>");
		$("#maxByte").html("90");
		$("#msgbody").css("background-color","#deeaf5");
		$("#msg_subject").attr("placeholder", "제목입력(최대 50byte, 특수문자 제외)");
		$("#send_type_title_color").css("background-color","#7395b8");
		$(".msg_sub_color").css("background-color","#deeaf5");
		$("input[name=send_type]").val("1");
	}
}
var changeBodyForm1 = function (v1) {
	var change_byte = 90;
	var limit_byte = 2000;
	var bytes = document.getElementById("msgbody").value.bytes2();
	if ($("input[name=vote_flag]").val() == '2') bytes += add_vote_byte;
	document.getElementById("msgByte").innerHTML = bytes;
	if (bytes > change_byte) {
		$("#msgType").html("<strong>장문</strong>");
		$("#maxByte").html("2000");
		$("#msgbody").css("background-color","#ffeed9");
		$("#msg_subject").attr("placeholder", "제목입력(최대 50byte, 특수문자 제외)");
		$(".msg_sub_color").css("background-color","#ffeed9");
		$("#send_type_title_color").css("background-color","#ff7e30");
		$("input[name=send_type]").val("2");
	} else {
		$("#msgType").html("<strong>단문</strong>");
		$("#maxByte").html("90");
		$("#msgbody").css("background-color","#deeaf5");
		$("#msg_subject").attr("placeholder", "제목입력(최대 50byte, 특수문자 제외)");
		$("#send_type_title_color").css("background-color","#7395b8");
		$(".msg_sub_color").css("background-color","#deeaf5");
		$("input[name=send_type]").val("1");
	}
}

var save_message = function() {
	var csrf_sowkorea_name = $.trim($("input[name=csrf_sowkorea_name]").val());
	var send_type = $.trim($("input[name=send_type]").val());
	var msgByte = $.trim($("#msgByte").text());
	var msg_subject = $.trim($("#msg_subject").val());

	var msgbody = $("#msgbody").val();
	if (msgbody == '') {
		alert("저장할 내용이 없습니다.");
		return;
	}
	$.ajax({
		type: "POST",
		url: "/sms/save_msg",
		data: {
			"csrf_sowkorea_name" : csrf_sowkorea_name,
			"message_body" : msgbody,
			"send_type" : send_type,
			"subject" : msg_subject,
			"bytes" : msgByte,
			"where" : "sms"
		},
		dataType: "json",
		async: false,
		success : function(data, status, xhr) {
		$("input[name=csrf_sowkorea_name]").val(data.csrf_sowkorea_name);
			alert(data.message);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(jqXHR.responseText);
		}
	});
}

var control_csrf = function (val) {
	console.log(val);
	$("input[name=csrf_sowkorea_name]").val(val);
}

var _send_lock = false;
function registerMessages() {
	countMsgBody();

	if (_send_lock != false) {
		alert("발송 진행 중 입니다. 잠시만 기다려주세요.");
		return false;
	}

	var form = document.message;
	// var _mtype = document.getElementById('msgtype').value;
	// form.lms_subject.value = form.lms_subject.value.trim();
	// if( _mtype != 0 && form.lms_subject.value.bytes() > 60 ) { alert( "메세지 제목은 60바이트(한글30자)를 초과하실 수 없습니다." ); form.lms_subject.focus(); return; }

	// var _sms_msg = document.getElementById( "_sms_msg" );
	// var _sum_msg = _sms_msg.value.bytes2();
	// if( _sum_msg < 1 ) { alert( "발송하실 메세지가 없습니다." ); _sms_msg.focus(); return; }
	// countMsgBody();

	if ($("input[name=ad_title_val]").val() != "1") {
		alert("발신자명을 등록하세요. \"환경설정\" > \"회원정보변경\" 으로 이동하세요.");
		return false;
	}

	if ($("#msgbody").val() == "") {
		alert("발송하실 내용을 입력하세요.");
		return false;
	}

	if (!DoCheckLang($("#msgbody").val())) return false;

	var send_targets = document.message.send_targets;
	if (send_targets.length < 1) {
		alert("받는사람이 없습니다.");
		return false;
	}

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

	var send_list = "";
	for (var i = 0; i < send_targets.length; i++) {
		send_list += send_targets[i].value + ",";
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

	_send_lock = true;
	form.send_list.value = send_list;
	return true;
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

<?php
	if ($svc == 'elect') {
		$g_layer_popup_flag = 'elect_popup';
		include_once(VIEWPATH.'/templates/layer_popup.php');
	}
?>