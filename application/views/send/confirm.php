<!-- content start -->
<div class="body-inner-table" style="padding-top:20px; min-height:700px;">

<table width="1200" border="0" cellspacing="0" cellpadding="0">
	<tr>
<!-- left menu start -->
		<td width="210" valign="top">
			<?php
				if ($campaign_array['cate_flag'] == '2') $g_left_menu_flag = 'elect_sms';
				else if ($campaign_array['cate_flag'] == '3') $g_left_menu_flag = 'adsms';
				else $g_left_menu_flag = 'sms';

				include_once(VIEWPATH.'/templates/left_menu.php');
			?>
        </td>
<!-- left menu end -->
		<td width="30"></td>
		<td width="960" valign="top">


<div class="content_wrap">
    <div class="body_title" style="position:relative;">내용확인
	</div>
</div>

<!-- main_body s -->
<div style="margin-top:30px">
<?php
$attributes = array(
	'name' => 'frmConfirm',
	'id' => 'frmConfirm',
	'onsubmit' => 'return sendMessage();'
);
echo form_open('send/send', $attributes);
?>

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
            	<?=convert_product_code($campaign_array['productcode']);?>
        	</strong></span><span class="f-right"><span id="msgByte">0</span> / <span id="maxMsgByte">2000</span> byte</span></div>
            <div id="msgbody_sub">
			<?php if ($campaign_array['file_path_1'] != '') { ?>
				<div style="height:150px; line-height:150px; vertical-align:middle;">
					<a href="<?=$campaign_array['image_path_1']?>" target="_blank"><img src="<?=$campaign_array['image_path_1']?>" style="width:auto; max-height:150px;" /></a>
				</div>
			<?php } ?>
                <textarea style="background-color:#fff; font-weight:600;width:100%; <?=($campaign_array['file_path_1'] != '' ? 'height:219px;' : 'height:369px;')?>" id="msgbody" name="message_body" readonly><?=$campaign_array['msg']?></textarea>
            </div>
        </div>
    </div>

<!-- msg phone e -->
		</td>
		<td width="20" bgcolor="#FFFFFF"></td>
		<td width="560" valign="top" bgcolor="#FFFFFF">
<!-- msg input s -->
<table width="100%" border="1" style="border: 1px solid #bbb;" cellpadding="0" cellspacing="1" bgcolor="#fff" class="class_tbl">
<input type="hidden" name="send_type" value="<?=$campaign_array['send_type']?>" />
	<tr border="1" style="border: 1px solid #bbb;">
    	<td width="150" height="50" align="center" bgcolor="#f7f7f7">전송방법</td>
		<td width="410" bgcolor="#FFFFFF" style="padding-left:30px">
		<font color="#4576c7"><b>
			<?=convert_product_code($campaign_array['productcode']);?>
		</b></font>
		</td>
	</tr>
	<tr border="1" style="border: 1px solid #bbb;">
    	<td width="150" height="50" align="center" bgcolor="#f7f7f7">제목</td>
		<td width="410" bgcolor="#FFFFFF" style="padding-left:30px"><?=($campaign_array['subject'] != '' ? $campaign_array['subject'] : '제목없음')?></td>
	</tr>
	<tr border="1" style="border: 1px solid #bbb;">
    	<td width="150" height="50" align="center" bgcolor="#f7f7f7">발신번호</td>
		<td width="410" bgcolor="#FFFFFF" style="padding-left:30px"><?=format_phone($campaign_array['callback'])?></td>
	</tr>
	<tr border="1" style="border: 1px solid #bbb;">
    	<td width="150" height="50" align="center" bgcolor="#f7f7f7">전송시간</td>
		<td width="410" bgcolor="#FFFFFF" style="padding-left:30px">
			<?=($campaign_array['reserve_yn'] == 'N' ? '즉시전송' : $campaign_array['send_time'])?>
		</td>
	</tr>
	<tr border="1" style="border: 1px solid #bbb;">
    	<td width="150" height="50" align="center" bgcolor="#f7f7f7">분할전송</td>
		<td width="410" bgcolor="#FFFFFF" style="padding-left:30px">
		<?=($campaign_array['divide_yn'] == 'Y' ? "{$campaign_array['div_cnt']}건씩 {$campaign_array['div_min']}분 간격" : '해당없음')?>
		</td>
	</tr>
	<tr border="1" style="border: 1px solid #bbb;">
    	<td width="150" height="50" align="center" bgcolor="#f7f7f7">예상금액</td>
		<td width="410" bgcolor="#FFFFFF" style="padding-left:30px">
			<font color="#ff6600"><b><?=number_format($campaign_array['amount'], 2)?> 원 (VAT포함)</b></font>
		</td>
	</tr>
	<tr border="1" style="border: 1px solid #bbb;">
    	<td width="150" height="170" align="center" bgcolor="#f7f7f7">전송내역</td>
		<td width="410" bgcolor="#FFFFFF" style="padding-left:30px">
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
            	<td height="25" width="30%">전송시도건수</td>
				<td width="70%"><?=number_format($campaign_array['tcount'])?> 건</td>
            </tr>
			<tr>
            	<td height="25" width="30%">중복번호</td>
				<td width="70%"><?=number_format($campaign_array['dcount'])?> 건</td>
            </tr>
			<tr>
            	<td height="25" width="30%">수신거부</td>
				<td width="70%"><?=number_format($campaign_array['bcount'])?> 건</td>
            </tr>
<?php /* ?>
			<tr>
            	<td height="25" width="30%">오류번호</td>
				<td width="70%"><?=number_format($campaign_array['wcount'])?> 건</td>
            </tr>
<?php */ ?>
			<tr>
            	<td height="25" width="30%">전송될건수</td>
				<td width="70%"><font color="#ff6600"><b><?=number_format($campaign_array['scount'])?> 건</b></font></td>
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

<?php if ($channel->status == '1') { ?>
	<tr>
		<td colspan="3" align="center">
        	<div class="bot_btn" style="margin-bottom:50px;">
        	<?php if ($campaign_array['scount']) { ?>
        		<?php if ($campaign_array['amount'] < $this->session->userdata('cash')) { ?>
					<input type="submit" id="signup-button" class="sowsms-inp-submit" value="메시지 전송" />&nbsp;&nbsp;
				<?php } else { ?>
					<span class="bc bc_org"><span><a href="/pay/list">충전하기</a></span></span>
				<?php } ?>
			<?php } ?>
				<span class="bc bc_gry"><span><a href="<?=$campaign_array['http_referer']?>">취소</a></span></span>
			</div>
		</td>
    </tr>
<?php } else { ?>
	<script type="text/javascript">
	$(document).ready(function() {
		alert('회원님의 채널이 발송 제한이 되었습니다. 관리자에게 문의해 주시기 바랍니다.');
	});
	</script>
<?php } ?>


</table>
</form>
</div>
<!-- main_body e -->
        </td>
	</tr>
</table>

</div>
<!-- content end -->
<script type="text/javascript">
$(document).ready(function() {
	// var msg = $("#msgbody")val();
	var msg = document.getElementById("msgbody").value;
	var bytes = msg.bytes();
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

var _send_click = false;
function sendMessage() {
	if (_send_click == true) {
		alert("데이타 발송 중입니다.");
		return false;
	}
	_send_click = true;
	return true;
}
</script>
