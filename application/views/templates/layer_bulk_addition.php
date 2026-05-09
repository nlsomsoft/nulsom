<div class="alpha60" id="addrbak" style="width:100%; height:100%; left:0px; top:0px; position:absolute; z-index:9999; display:none;" align="center">

	<?php
	$attributes = array(
		'name' => 'frmBulk',
		'id' => 'frmBulk'
	);
	echo form_open('address/bulk_add', $attributes);
	?>

	<?php
	$data = array(
		'bulk_gno' => '',
		'bulk_current_count' => '',
		'bulk_count' => '',
		'bulk_type' => '',
		'bulk_list' => '',
	);
	echo form_hidden($data);
	?>

	<table border="0" width="100%" height="100%">
		<tr><td align="center" valign="top" style="padding-top:130px">
			<table width="570" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="10" height="10" background="/images/box/t1.gif"></td>
					<td width="550" background="/images/box/t2.gif"></td>
					<td width="10" background="/images/box/t3.gif"></td>
				</tr>
				<tr>
					<td background="/images/box/t4.gif"></td>
					<td bgcolor="#F7F7F7" align="center" valign="top">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td height="25">&nbsp;</td>
								<td align="right" width="50"><img src="/images/box/l_close.gif" width="50" height="25" onClick="layerClosing(); return false;" style="cursor:pointer" alt=""></td>
							</tr>
						</table>
					</td>
					<td background="/images/box/t5.gif">&nbsp;</td>
				</tr>
				<tr>
					<td background="/images/box/t4.gif"></td>
					<td bgcolor="#FFFFFF" align="center" valign="top">
					 <table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td height="10"></td>
						</tr>
						<tr>
							<td align="center">
								<table width="540" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
									<tr>
										<td width="250" align="right" valign="top">
											<table width="230" border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td height="20" class="font_12">그룹명 : <span id="layer_group_name"></span></td>
												</tr>
												<tr>
													<td height="10"></td>
												</tr>
												<tr>
													<td align="center"><textarea id="bulklist_telnumlist" name="bulklist_telnumlist" style="width:230px; height:190px; resize: none;" wrap="VIRTUAL" class="inputbulkbody"></textarea></td>
												</tr>
												<tr>
													<td height="10"></td>
												</tr>
												<tr>
													<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
														<tr>
															<td width="80"><input onclick="bulklist_check_list( this.form.bulklist_telnumlist );" type="button" class="addr_button"></td>
															<td><input type="text" class="input_21" name="bulklist_listcount" id="bulklist_listcount2" value="[총 0명]" style="background-color:transparent;border:0;width:100px;" readonly></td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td height="15"></td>
											</tr>
											<tr>
											  <td height="24" align="center"><span id="button-bulk-address"><a href="#" onclick="add_bulk_address();"><img src="/images/regi2.gif" alt=""></a></span></td>
											</tr>
										</table>
									</td>
									<td>
									</td>
									<td width="254" align="left" valign="top">
										<table width="240" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td width="12" height="12" background="/images/box1/boxtop1.gif"></td>
												<td background="/images/box1/boxtop2.gif"></td>
												<td width="12" background="/images/box1/boxtop3.gif"></td>
											</tr>
											<tr>
												<td height="266" background="/images/box1/boxleft.gif"></td>
												<td valign="top">

													<table width="100%" border="0" cellpadding="0" cellspacing="0">
														<tr><td height="5"></td></tr>
														<tr><td class="font_12"><b>주소록 등록 안내</b></td></tr>
														<tr><td height="10"></td></tr>
														<tr>
															<td height="25" class="font_12"><img src="/images/ico_num2_1.gif" align="absmiddle" alt="" /> 최대 50,000개까지 등록가능</td></tr>
															<tr>
																<td height="25" class="font_12"><img src="/images/ico_num2_2.gif" align="absmiddle" alt="" /> 문서파일 [복사], [붙여넣기] 가능</td></tr>
																<tr><td height="22" class="font_12"><img src="/images/ico_num2_3.gif" align="absmiddle" alt="" /> 핸드폰번호, 이름 순으로 입력</td></tr>
																<tr>
																	<td height="25" class="font_12"><img src="/images/ico_num2_4.gif" align="absmiddle" alt="" /> 입력 예시</td></tr>
																	<tr><td height="5"></td></tr>
																	<tr>
																		<td align="center"><img src="/images/guide_img.jpg" width="200" height="80" alt="" /></td>
																	</tr>
																	<tr><td height="10"></td></tr>
																	<tr>
																		<td height="22" class="font_12"><img src="/images/ico_num2_5.gif" align="absmiddle" alt="" /> 문의사항 또는 등록대행을 원하시면</td></tr>
																		<tr><td style="padding-left:17px" class="font_12">고객센터로 연락주세요.</td></tr>
																	</table>

																</td>
																<td background="/images/box1/boxright.gif"></td>
															</tr>
															<tr>
																<td height="12" background="/images/box1/boxbot1.gif"></td>
																<td background="/images/box1/boxbot2.gif"></td>
																<td background="/images/box1/boxbot3.gif"></td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td height="15"></td>
									</tr>
								</table>
							</td>
							<td background="/images/box/t5.gif">&nbsp;</td>
						</tr>
						<tr>
							<td height="10" background="/images/box/t6.gif"></td>
							<td background="/images/box/t7.gif"></td>
							<td background="/images/box/t8.gif"></td>
						</tr>
					</table>
				</td></tr>
			</table>
		</form>
	</div>
<script type="text/javascript">
var bulklist_telnumlist_enabled = false;
function addrwLayerOpen(name,type,totcnt,gno) {
	var obj = document.getElementById("addrbak");
	obj.style.width = document.body.scrollWidth + 'px';
	obj.style.height = document.body.scrollHeight + 'px';
	obj.style.display = "block";

	$("#layer_group_name").text(name);
	$("input[name=bulk_type]").val(type);
	$("input[name=bulk_current_count]").val(totcnt);
	$("input[name=bulk_gno]").val(gno);
}

function layerClosing() {
	var obj = document.getElementById("addrbak");
	obj.style.display = "none";
}
function bulklist_check_list( obj ) {
	var listcount = bulklist_order_list( obj );
	obj.form.bulklist_listcount.value = "[총 " + listcount + "명]";
	obj.form.bulk_count.value = listcount;
	if (listcount > 0) obj.enabled = true;
	bulklist_telnumlist_enabled = true;
	return listcount;
}
function bulklist_order_list( obj ) {
	if( obj.value == "" ) return 0;

	var bulk_max_cnt = 50000;
	var reVal = obj.value;
	var rePhone = '';
	var reName = '';
	var countList = 0;
	var HTML = '';
	var bulk_value = '';

	var form = document.frmBulk;
	var bulk_current_count = parseInt(form.bulk_current_count.value, 10);
	var able_cnt = bulk_max_cnt - bulk_current_count;

	var arrayList = reVal.split("\n");
	var length_List = arrayList.length;
	if (length_List + bulk_current_count > bulk_max_cnt) {
		alert( "선택하신 그룹에는 추가로 " + able_cnt + "개 까지 등록하실 수 있습니다." );
		lengthList = bulk_max_cnt - bulk_current_count;
	} else {
		lengthList = length_List;
	}

	var pattern = /([0-9-() ]{8,15})[ ,\t]*([\W\w]*)/;
	for (var i = 0; i < lengthList; i++) {
		var strLine = '';
		row = pattern.exec(arrayList[i]);
		if (!row) continue;

		rePhone = row[1].replace(/[-\(\) ]/g, "");
		if (isMobileNumber(rePhone) == false) continue;

		reName = row[2].replace(/\|/g, "");
		strLine = rePhone + "|" + reName + "\n";

		HTML += strLine;
		bulk_value += rePhone + "|" + reName + ",";
		countList++;
	}
	obj.value = HTML;
	$("input[name=bulk_list]").val(bulk_value);
	return countList;
}

var isRun = false;
var add_bulk_address = function() {

	if(isRun == true) {
		return;
	}
	isRun = true;

	if (bulklist_telnumlist_enabled == false) {
		isRun  = false;
		alert("인원수 확인을 누르세요.");
		return;
	}

	var bulklist_telnumlist = $("#bulklist_telnumlist").val();
	if (bulklist_telnumlist == '') {
		isRun  = false;
		alert("등록할 데이타가 없습니다.");
		return;
	}

  // var listcount = document.bulk.elements["bulk_count"].value;
	if (!parseInt($("input[name=bulk_count]").val())) {
		isRun  = false;
		alert("입력하신 주소록이 없습니다.\n주소록입력 후, 인원수 확인을 해주세요.");
		return;
	}

  $("form#frmBulk").submit();
}
</script>
