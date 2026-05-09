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
				$g_left_menu_flag = 'elect_sms';
				include_once(VIEWPATH.'/templates/left_menu.php');
			?>
			</td>
			<!-- left menu end -->
			<td width="30"></td>
			<td width="960" valign="top">

				<div class="content_wrap">
					<div class="body_title" style="position:relative;">포토문자 <img src="/images/vote_icon.png">
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
	'onsubmit' => 'return registerMessages();'
);
echo form_open('send/send_sms', $attributes);
?>
<?php
$data = array(
	'send_type' => '3',
	'vote_flag' => ($cached_campaign_array['vote_flag'] != '' ? $cached_campaign_array['vote_flag'] : '1'),
	'send_list' => '',
	'trash_list' => '',
	'content' => '',
	'photo_image' => '',
	'phone_080' => $this->session->userdata('phone_080'),
	'phone_ext' => $this->session->userdata('phone_ext'),
	'svc' => 'elect',
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
													<div class="phone_bg_inner msg_sub_color" style="background-color:#ffeed9;">
														<div id="send_type_title_color" style="background-color:#ff7e30;">
															<span id="msgType"><strong>포토문자</strong></span>
															<span class="f-right">
																<span id="msgByte">0</span> / <span id="maxByte">2000</span> byte
															</span>
														</div>
											<?php $photo_image_path = ($cached_campaign_array['image_path_1'] != '' ? $cached_campaign_array['image_path_1'] : '/images/img_up.png'); ?>
													<div style="height:150px; line-height:150px; vertical-align:middle">
														<a href="/sms/photo_popup" onclick="winPop(this.href, {name:'inputPhotoPop',width:410,height:500}); return false;" class="inputPhotoPop" style="text-align:left;padding-left:14px;"><img src="<?=$photo_image_path?>" id="photo_image" style="width:auto; max-height:150px" /></a>
													</div>

														<div class="msg_sub_color" style="margin-top:10px;">
															<input id="votemsgtop" name="vote_msg_top" type="text" class="phone_bg_msg" style="display:none;font-weight:600;font-size:13px;color:#4576c7;" value="(선거운동정보)" readonly />
															<textarea style="background-color:#ffeed9;width:100%; height:219px;font-size:13px;color:#000;font-weight:600;" placeholder="메시지 내용을 입력하세요." id="msgbody" name="message_body" onkeyup="countMsgBody();"><?=$cached_campaign_array['message_body']?></textarea>
													<div id="votemsgbottom" class="phone_bg_msg2" style="font-weight:600;font-size:13px;color:#4576c7;display:none;line-height:120%">
													<?php
														if ($svc == 'elect') {
															if ($this->session->userdata('phone_080')) {
																$vote_bottom_msg2 = '무료수신거부 '.$this->session->userdata('phone_080');
																if ($this->session->userdata('phone_ext')) {
																	$vote_bottom_msg2 .= ' 인증'.$this->session->userdata('phone_ext');
																}
																$vote_bottom_msg1 = '불법수집정보신고번호 118';
																$vote_bottom_msg = $vote_bottom_msg1.'<br />'.$vote_bottom_msg2;
															} else {
																$vote_bottom_msg2 = '';
																$vote_bottom_msg1 = '불법수집정보신고번호 118';
																$vote_bottom_msg = $vote_bottom_msg1.$vote_bottom_msg2;
															}
														} else {
															$vote_bottom_msg2 = '무료수신거부 '.$this->session->userdata('phone_080');
															if ($this->session->userdata('phone_ext')) {
																$vote_bottom_msg2 .= ' 인증'.$this->session->userdata('phone_ext');
															}
															$vote_bottom_msg1 = '';
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
																<a href="#" style="color:#fff;" onclick="vote_msg_option(); return false;">선거필수문구</a>
															</div>
														</div>
														<div class="d-table w100pro table-border" style="background-color:#888;">
															<div class="d-table-cell w50pro v-middle">
																<a href="/sms/photo_saved" onclick="winPop(this.href, {name:'MsgDataPop',width:735,height:700}); return false;" style="color:#fff;">메시지 보관함</a>
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
														<td width="150" height="50" align="center" bgcolor="#f7f7f7"><strong>받는사람</strong></td>
														<td align="center" bgcolor="#FFFFFF">
															<table width="90%" border="0" cellspacing="0" cellpadding="0">
																<tr><td height="10"></td></tr>
																<tr>
																	<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
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
																				<select name="send_targets" multiple style="width:100%; height:310px; padding-top:5px; line-height:14px;" class="input_261">
																<?php
																	foreach ($cached_remain_array as $val) {
																			echo "<option value=\"P|{$val}|1||\">{$val}</option>";
																	}
																?>

																				</select>
																			</td>
																		</tr>
																		<tr>
																			<td height="5"></td>
																		</tr>
																		<tr>
																			<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
																				<tr>
																					<td>
																					<div style="margin-bottom:10px;">
																						<input type="submit" id="signup-button" class="sowsms-inp-submit" value="전송하기" />&nbsp;&nbsp;
																					</div>
																					</td>
																					<td align="right" width="120px">
																						<div style="margin-top:-30px"><strong>총 <span id="target_count" style="color:#ec4000;font-weight:bold;"><?=count($cached_remain_array)?></span> 명</strong></div>
																					</td>
																					<td align="right">
																						<div class="style_btn" style="width:50px;margin-top:-30px;"><a href="#" onclick="removeItem(); return false;">삭제</a></div>
																					</td>
																				</tr>
																				<tr>
																					<td colspan="3">
													              <div class="btn1-group" style="padding-top:4px;">
													                <div class="btn1-black font-13" style="border:0px;width:179px;text-align:center;">전체건수</div>
													                <input id="total_count" type="text" class="input_261 input-elect" title="전체건수" readonly value="<?=number_format($tcount)?>">
													              </div>
													              <div class="btn1-group" style="padding-top:4px;">
													                <div class="btn1-black font-13" style="border:0px;width:179px;text-align:center;">전송건수</div>
													                <input id="send_count" type="text" class="input_261 input-elect" title="전송건수" readonly value="<?=number_format($tcount - $rcount)?>">
													              </div>
													              <div class="btn1-group" style="padding-top:4px;">
													                <div class="btn1-black font-13" style="border:0px;width:179px;text-align:center;">잔여건수</div>
													                <input id="remain_count" type="text" class="input_261 input-elect" title="잔여건수" readonly value="<?=number_format($rcount)?>">
													              </div>
													              <div class="btn1-group" style="padding-top:4px;">
													                <a href="#" onclick="initElectList();"><div class="btn1-blue font-13" style="border:0px;width:179px;text-align:center;">발송목록초기화</div></a>
													              </div>
													              <div class="btn1-group" style="padding-top:4px;padding-left:2px;">
													                <a href="/sms/sms_excel"><div class="btn1-blue font-13" style="border:0px;width:179px;text-align:center;">잔여목록다운로드</div></a>
													              </div>
																					</td>
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
														</table>
													</td>
												</tr>
											</table>
											<!-- msg input e -->
										</td>
									</tr>

									<tr>
										<td align="center">
								<table width="100%" border="1" style="border: 1px solid #bbb; margin-top:20px" cellpadding="0" cellspacing="1" bgcolor="#fff" class="class_tbl">
									<tr border="1" style="border: 1px solid #bbb;">
										<td width="100" height="50" align="center" bgcolor="#f7f7f7">제목</td>
										<td width="300" bgcolor="#FFFFFF" align="center"><input type="text" name="subject" id="msg_subject" maxlength="30" style="width:90%;" class="input_261" placeholder="제목입력(최대 30byte 발송관리용)" value="<?=$cached_campaign_array['subject']?>" /></td>
										<td width="100" align="center" bgcolor="#f7f7f7">발신번호</td>
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
							</form>
						</div>

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:20px;">
<tr><td><img src="/images/tip.gif" width="145" height="34" alt="" /></td></tr>
<tr><td height="1" bgcolor="#CCCCCC"></td></tr>
<tr>
	<td class="help_txt">
        * 간편발송 TIP : 크롬(Chrome) 브라우저에서는 휴대폰번호를 다중 선택 후 엔터(Enter)키 누르면 전송 됩니다.</br>
        * 휴대폰 번호 추가 시 중복된 번호는 자동적으로 제거 됩니다. </br>
        * 주소록 메뉴의 직접등록 수신거부, 080 수신거부에 등록된 번호는 자동 제거 됩니다.</br>
        * 발송목록초기화를 클릭할 경우 중복번호 제거도 초기화 되어집니다. </br>
	</td>
</tr>
<tr><td height="30px"></td></tr>
</table>
<!-- main_body e -->
</td>
</tr>
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
		<div style="padding-left:15px"><strong>분할전송 안내</strong><span class="f-right" style="padding-right:15px; cursor:pointer" onclick="layerCloseNew( 'd_layer' ); return false;">닫기</span></div></div>
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
<div class="layer_bg" style="width:300px">
	<div class="layer_bg_inner">
		<div style="padding-left:15px"><strong>발송가능건수 확인</strong><span class="f-right" style="padding-right:15px; cursor:pointer" onclick="lookAtSendCount(); return false;">닫기</span></div></div>
		<div style="margin:10px 20px;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
        	<td height="20" align="left" width="22px"><img src="/images/ico_num2_1.gif" width="13" height="13" alt="" style="margin-top:-4px;" /> </td>
        	<td><div style="float:left;width:90px">선거단문문자</div><div style="float:left;width:90px">단가 <?=number_format($this->session->userdata('sms2'),2)?> 원</div><div style="float:left;"><?=number_format(floor($this->session->userdata('cash') / $this->session->userdata('sms2')))?> 건</div></td>
        </tr>
        <tr>
        	<td height="20" align="left"><img src="/images/ico_num2_2.gif" width="13" height="13" alt="" style="margin-top:-4px;" /></td>
        	<td><div style="float:left;width:90px">선거장문문자</div><div style="float:left;width:90px">단가 <?=number_format($this->session->userdata('lms2'),2)?> 원</div><div style="float:left;"><?=number_format(floor($this->session->userdata('cash') / $this->session->userdata('lms2')))?> 건</div></td>
        </tr>
        <tr>
        <tr>
        	<td height="20" align="left"><img src="/images/ico_num2_3.gif" width="13" height="13" alt="" style="margin-top:-4px;" /></td>
        	<td><div style="float:left;width:90px">선거포토문자</div><div style="float:left;width:90px">단가 <?=number_format($this->session->userdata('mms2'),2)?> 원</div><div style="float:left;"><?=number_format(floor($this->session->userdata('cash') / $this->session->userdata('mms2')))?> 건</div></td>
        </tr>
	 	</table>
		</div>
	</div>
</span>

<span id="result_layer" style="display:none; z-index:110;">
<div class="layer_bg" style="width:400px">
	<div class="layer_bg_inner">
		<div style="padding-left:15px"><strong>전송결과</strong><span class="f-right" style="padding-right:15px; cursor:pointer;" onclick="layerCloseNew( 'result_layer' ); return false;">닫기</span></div></div>
		<div style="margin:10px 20px;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
        	<td style="text-align:center;height:70px;"><span id="result_message" style="font-weight:bold;"></span></td>
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

<?php if ($this->session->userdata('callback') == false) { ?>
	var _location_base = document.getElementById("base_1");
	var _a = getOffsetPosition(_location_base);
	layerOpenLocationNew("c_layer", _a[0] + 427, _a[1] + 122);
<?php } ?>

	var change_byte = 90;
	var msg = document.getElementById("msgbody").value;
	var bytes = msg.bytes2();
	$("#msgByte").text(bytes);
	if ($("input[name=vote_flag]").val() == '2') {
		changeVoteForm('2');
		bytes += add_vote_byte;
	} else {
		changeVoteForm('1');
	}
	countMsgBody();
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
		if (phone_ext == 0) return (20 + phone_080);
		else return (25 + phone_080 + phone_ext);
	}
}
function vote_msg_option() {
	var svc = $.trim($("input[name=svc]").val());
	var phone_080 = $.trim($("input[name=phone_080]").val());
	if (svc == 'elect' && phone_080 == '') {
		phone080LayerOpen('phone_080_layer', 'base_1');
	}
	var vote_flag = $.trim($("input[name=vote_flag]").val());

  	if (vote_flag == '1') {
		document.getElementById("votemsgtop").style.display = "block";
		document.getElementById("votemsgbottom").style.display = "block";
		// document.getElementById("msgbody").style.height = "305px";
		document.getElementById("msgbody").style.height = "155px";
		$("input[name=vote_flag]").val("2");
	} else {
		document.getElementById("votemsgtop").style.display = "none";
		document.getElementById("votemsgbottom").style.display = "none";
		// document.getElementById("msgbody").style.height = "369px";
		document.getElementById("msgbody").style.height = "219px";
		$("input[name=vote_flag]").val("1");
	}
	countMsgBody();
}
function changeVoteForm(arv) {
	if (arv == '2') {
		document.getElementById("votemsgtop").style.display = "block";
		document.getElementById("votemsgbottom").style.display = "block";
		document.getElementById("msgbody").style.height = "155px";
	} else {
		document.getElementById("votemsgtop").style.display = "none";
		document.getElementById("votemsgbottom").style.display = "none";
		document.getElementById("msgbody").style.height = "219px";
	}
}
var resultProcess = function() {
	var _target = document.getElementById('result_layer');
	var _location_base = document.getElementById("base_1");
	var _a = getOffsetPosition(_location_base);
	layerOpenLocationNew("result_layer", _a[0] + 115, _a[1] - 500);
	setTimeout("layerCloseNew('result_layer')", 700);
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

function removeItem() {
	var send_targets = document.message.send_targets;
	if (send_targets.selectedIndex < 0) {
		alert( "삭제하실 주소록 또는 개별주소를 선택해 주세요." );
		return;
	}
	var trash_list = '';
	var trash_list = $("input[name=trash_list]").val();
	target_count = send_targets.options.length;
	for (var i = send_targets.options.length - 1; i >= 0; i--) {
		if (send_targets.options[i] == null || send_targets.options[i].selected != true) continue;
		var delobj = send_targets.options[i].value;
		var splobj = delobj.split("|");
		send_targets.options[i] = null;
		target_count = target_count + (splobj[2] * (-1));
		if (trash_list != '') trash_list += '|';
		trash_list += splobj[1];
		displayCount();
	}
	$("input[name=trash_list]").val(trash_list);
}
function displayCount() {
	document.getElementById("target_count").innerHTML = target_count;
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
var changeBodyForm1 = function (v1) {
	var bytes = document.getElementById("msgbody").value.bytes2();
	if ($("input[name=vote_flag]").val() == '2') bytes += add_vote_byte;
	document.getElementById("msgByte").innerHTML = bytes;
}
function countMsgBody() {
	var limit_byte = 2000;

	var msg = document.getElementById("msgbody").value;
	var bytes = msg.bytes2();
	if ($("input[name=vote_flag]").val() == '2') bytes += add_vote_byte;

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

var control_csrf = function (val) {
	console.log(val);
	$("input[name=csrf_sowkorea_name]").val(val);
}

var _send_lock = false;
function registerMessages() {
	if (_send_lock != false) {
		alert("발송 진행 중 입니다. 잠시만 기다려주세요.");
		return false;
	}

	if (!DoCheckLang($("#msgbody").val())) return false;

	var image = $("#photo_image").attr("src");
	if (image == "/images/img_up.png") {
		alert("포토 이미지를 등록하세요.");
		return false;
	}
	$("input[name=photo_image]").val(image);

	var send_list = "";
	var send_targets = document.message.send_targets;
	for (var i = send_targets.options.length - 1; i >= 0; i--) {
		if (send_targets.options[i] == null || send_targets.options[i].selected != true) continue;
		send_list += send_targets[i].value + ",";
	}
	if (send_list == "") {
		alert("받는사람을 선택하세요. 선택한 번호만 발송이 됩니다.");
		return false;
	}

	var form = document.message;
	var i = form.callback.options.selectedIndex;
	if (form.callback.options[i].value.bytes() < 2) {
		alert("발신번호를 선택해 주세요.");
		return false;
	}

	var vote_bottom_msg1 = $("input[name=vote_bottom_msg1]").val();
	var vote_bottom_msg2 = $("input[name=vote_bottom_msg2]").val();
	var content = '';
	if ($("input[name=vote_flag]").val() == '2') {
		content = $("#votemsgtop").val();
		content += "\n";
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
var save_message = function() {
	var csrf_sowkorea_name = $.trim($("input[name=csrf_sowkorea_name]").val());
	var send_type = $.trim($("input[name=send_type]").val());
	var msgByte = $.trim($("#msgByte").text());
	var msg_subject = $.trim($("#msg_subject").val());

	var msgbody = $("#msgbody").val();
	// if (msgbody == '') {
	// 	alert("저장할 내용이 없습니다.");
	// 	return;
	// }

	var image = $("#photo_image").attr("src");
	if (image == "/images/img_up.png") {
		alert("저장할 포토 이미지를 등록하세요.");
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
			"photo_image" : image,
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

var _look = 0;
var lookAtSendCount = function () {
	if (_look == 0) {
		var _location_base = document.getElementById("base_1");
		var _a = getOffsetPosition(_location_base);
		layerOpenLocationNew("b_layer", _a[0] + 578, _a[1] - 500); //left,top
		_look = 1;
	} else {
		layerCloseNew('b_layer');
		_look = 0;
	}
}
var initElectList = function() {
  var csrf_sowkorea_name = $.trim($("input[name=csrf_sowkorea_name]").val());
  $.ajax({
      type: "POST",
      url: "/append/init_elect",
      data: {
          "csrf_sowkorea_name" : csrf_sowkorea_name,
          "where" : "sms"
      },
      dataType: "json",
      async: false,
      success : function(data, status, xhr) {
      	alert(data.message);
      	$(location).attr('href', '/sms/newphoto');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR.responseText);
      }
  });
}
</script>

<?php
	$g_layer_popup_flag = 'elect_popup';
	include_once(VIEWPATH.'/templates/layer_popup.php');
?>