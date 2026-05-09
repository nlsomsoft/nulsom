<!-- content start -->
<div class="body-inner-table" style="padding-top:20px; min-height:700px;">

<table width="1200" border="0" cellspacing="0" cellpadding="0">
	<tr>
<!-- left menu start -->
		<td width="210" valign="top">
            <?php
                $g_left_menu_flag = 'result';
                include_once(VIEWPATH.'/templates/left_menu.php');
            ?>
        </td>
<!-- left menu end -->
		<td width="30"></td>
		<td width="960" valign="top">


<form>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td>
<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
        <tr>
          <td width="380" valign="top" align="center" bgcolor="#FFFFFF">
<!-- msg phone s-->

<div class="phone_bg">
                        <div class="phone_bg_inner">
                            <div id="send_type_title_color"><span><strong>
							<?=convert_product_code($result[0]->productcode)?>
                            </strong></span><span class="f-right"><span id="msgByte">0</span> / <span id="maxMsgByte">2000</span> bytes</span></div>
                            <div id="msgbody_sub">
			<?php
				if ($result[0]->file_path_1 != '') {
				$image_path = '/'.str_replace(FCPATH,'',$result[0]->file_path_1);
			?>
				<div style="height:150px;">
					<a href="<?=$image_path?>" target="_blank"><img src="<?=$image_path?>" style="width:157px;height:auto;" /></a>
				</div>
			<?php } ?>
                                <textarea style="background-color:#fff; width:100%; <?=($result[0]->file_path_1 != '' ? 'height:219px;' : 'height:369px;')?>" id="msgbody" name="message_body" readonly><?=$result[0]->msg?></textarea>
                            </div>
                        </div>
                    </div>

<!-- msg phone e -->
		</td>
		<td width="20" bgcolor="#FFFFFF"></td>
		<td width="560" valign="top" bgcolor="#FFFFFF">
<!-- msg input s -->
<input type="hidden" name="send_type" value="<?=convert_send_type($result[0]->productcode)?>" />
<table width="100%" border="1" style="border: 1px solid #bbb;" cellpadding="0" cellspacing="1" bgcolor="#fff" class="class_tbl">
	<tr border="1" style="border: 1px solid #bbb;">
    	<td width="150" height="55" align="center" bgcolor="#f7f7f7">발송방법</td>
		<td width="410" bgcolor="#FFFFFF" style="padding-left:30px">
			<font color="#669900"><b><?=convert_product_code($result[0]->productcode)?></b></font>
		</td>
	</tr>
	<tr border="1" style="border: 1px solid #bbb;">
    	<td width="150" height="55" align="center" bgcolor="#f7f7f7">발송제목</td>
		<td width="410" bgcolor="#FFFFFF" style="padding-left:30px"><?=($result[0]->subject != '' ? $result[0]->subject : '제목없음')?></td>
	</tr>
	<tr border="1" style="border: 1px solid #bbb;">
    	<td width="150" height="55" align="center" bgcolor="#f7f7f7">회신번호</td>
		<td width="410" bgcolor="#FFFFFF" style="padding-left:30px"><?=format_phone($result[0]->callback)?></td>
	</tr>
	<tr border="1" style="border: 1px solid #bbb;">
    	<td width="150" height="55" align="center" bgcolor="#f7f7f7">발송시간</td>
		<td width="410" bgcolor="#FFFFFF" style="padding-left:30px"><?=$result[0]->reserve_time?></td>
	</tr>
	<tr border="1" style="border: 1px solid #bbb;">
    	<td width="150" height="55" align="center" bgcolor="#f7f7f7">선차감금액</td>
		<td width="410" bgcolor="#FFFFFF" style="padding-left:30px"><b><?=number_format($result[0]->amount, 2)?> 원</b> <span style="font-size:11px; color:#666;">(VAT포함, 발송시점에 우선 차감된 금액)</span></td>
	</tr>
<?php
	$realamount = ($result[0]->status == '100' ? ($result[0]->success * $result[0]->price) : '0');
?>
	<tr border="1" style="border: 1px solid #bbb;">
    	<td width="150" height="55" align="center" bgcolor="#f7f7f7">실사용금액</td>
		<td width="410" bgcolor="#FFFFFF" style="padding-left:30px"><font color="#ff6600"><b><?=number_format($realamount, 2)?> 원</b></font> <span style="font-size:11px; color:#666;">(VAT포함, 발송완료 후 최종 확정된 금액)</span></td>
	</tr>
	<tr border="1" style="border: 1px solid #bbb;">
    	<td width="145" height="150" align="center" bgcolor="#f7f7f7">발송내역</td>
		<td width="410" bgcolor="#FFFFFF" style="padding-left:30px">
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
            	<td height="25" width="30%">발송시도건수 </td>
				<td width="70%"><?=number_format($result[0]->total_units)?> 건</td>
            </tr>
			<tr>
            	<td height="25">발송성공 </td>
				<td><font color="#ff6600"><b><?=number_format($result[0]->success)?> 건</b></font></td>
            </tr>
			<tr>
            	<td height="25">발송실패 </td>
				<td><?=number_format($result[0]->fail)?> 건</td>
            </tr>
			<tr>
            	<td height="25">결과 미수신 </td>
				<td><?=number_format($result[0]->remain_units)?> 건</td>
            </tr>
        </table>
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

<iframe src="/result/set_detail/<?=$result[0]->procid?>/<?=(int)$result[0]->tbl?>" id="the_iframe" onload="calcHeight();" name="WrittenPublic" title="detail list" frameborder="0" scrolling="no" style="overflow-x:hidden; overflow:auto; width:100%; min-height:300px;"></iframe>

</td>
</tr>

</table>

</form>
        </td>
	</tr>
</table>

</div>
<!-- content end -->

<script type="text/javascript">
function viewBigImg_fn(img) {
	var obj = document.getElementById("bak");
	obj.style.width = document.body.scrollWidth + 'px';
	obj.style.height = document.body.scrollHeight + 'px';
	obj.style.display = "block";
	document.all.bigimg.src=img;
}
function hiddenBigImg_fn() {
	var obj = document.getElementById("bak");
	obj.style.display = "none";
}
</script>
<script type="text/javascript">
//<![CDATA[
function calcHeight() {
	//find the height of the internal page
	var the_height=
	document.getElementById('the_iframe').contentWindow.
	document.body.scrollHeight;

	//change the height of the iframe
	document.getElementById('the_iframe').height=
	the_height;

	//document.getElementById('the_iframe').scrolling = "no";
	document.getElementById('the_iframe').style.overflow = "hidden";
}
//
$(document).ready(function() {
	// var msg = $("#msgbody")val();
	var msg = document.getElementById("msgbody").value;
	var bytes = msg.bytes2();
	// document.getElementById("msgByte").innerHTML = bytes;
	$("#msgByte").text(bytes);
	if ($("input[name=send_type]").val() == "1") {
		$("#maxMsgByte").text("90");
		$("#send_type_title_color").css("background-color","#7395b8");
		$("#msgbody").css("background-color","#deeaf5");
		$("#msgbody_sub").css("background-color","#deeaf5");
	} else {
		$("#maxMsgByte").text("2000");
		$("#send_type_title_color").css("background-color","#ff7e30");
		$("#msgbody").css("background-color","#ffeed9");
		$("#msgbody_sub").css("background-color","#ffeed9");
	}
});
</script>