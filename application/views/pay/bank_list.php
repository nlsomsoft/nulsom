<!-- content start -->
<div class="body-inner-table" style="padding-top:20px; min-height:700px;">

	<table width="1200" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<!-- left menu start -->
		<td width="210" valign="top">
            <?php
                $g_left_menu_flag = 'pay';
                include_once(VIEWPATH.'/templates/left_menu.php');
            ?>
        </td>
			<!-- left menu end -->
			<td width="30"></td>
			<td width="960" valign="top">

<div class="content_wrap">
    <div class="body_title" style="position:relative;">충전하기
	</div>

    <div style="margin-top:20px;">
            <p style="margin-bottom:15px; color:#333"><b>상품별 적용단가</b><span class="font_12"> (VAT별도)</span></p>
    </div>
        <table width="100%" style="font-size:14px; border: 1px solid #ccc;">
            <tr align="center" bgcolor="#F6F6F6" style="border: 1px solid #ccc; border-top: 1px solid #000">
                <td width="25%" height="45px" style="border-right: 1px solid #ccc; color:#222"><b>서비스상품</b></td>
                <td width="25%" style="border-right: 1px solid #ccc; color:#222"><b>SMS-단문</b></td>
                <td width="25%" style="border-right: 1px solid #ccc; color:#222"><b>LMS-장문</b></td>
                <td width="25%" style="color:#222"><b>MMS-포토</b></td>
            </tr>
            <tr align="center" bgcolor="#FFFFFF" style="border: 1px solid #ccc;">
                <td height="50px" style="border-right: 1px solid #ccc;">적용단가</td>
                <td style="border-right: 1px solid #ccc;"><?=number_format(($this->session->userdata('sms1') / 1.1),2)?>원</td>
                <td style="border-right: 1px solid #ccc;"><?=number_format(($this->session->userdata('lms1') / 1.1),2)?>원</td>
                <td><?=number_format(($this->session->userdata('mms1') / 1.1),2)?>원</td>
            </tr>
        </table>

	<div style="margin-top:30px;">
            <p style="margin-bottom:15px; color:#333"><b>입금계좌 안내</b></p>
	</div>


	<table width="100%" style="border: 1px solid #ccc;">
       	<tr><td height="20"></td></tr>

<?php if ((int)$this->session->userdata('sum_cash') >= 100000000) { ?>
		<tr><td height="40" align="center"><span style="color:#333; font-weight:bold; font-size:18px;"><?=BANK?>, <?=ACCOUNT?></span></td></tr>
		<tr><td height="40" align="center"><b>예금주 : </b><span style="color:#333; font-weight:bold; font-size:18px;"><?=ACCOUNT_NAME?></span></td></tr>
<?php } else { ?>
		<tr><td height="40" align="center"><span style="color:#333; font-weight:bold; font-size:18px;">입금계좌는 문의 부탁드립니다.</span></td></tr>
<?php } ?>

       	<tr><td height="20"></td></tr>
	</table>
<?php
    $attributes = array(
		'id' => 'frmPay',
		'id' => 'frmPay',
		'onsubmit' => 'return setBankbook();'
    );
    echo form_open('/pay/bankbook', $attributes);
?>
	<div style="margin-top:25px;">
            <p style="margin-bottom:15px; color:#333"><b>결제금액 : </b>&nbsp;&nbsp;<input name="ipt_amount" type="text" class="input_36" placeholder="결제금액" style="width:220px;" /><span style="padding-left:20px; font-size:12px">* 최저금액은 10,000원 입니다.</span></p>
	</div>

	<div style="margin-top:20px;">
            <p style="margin-bottom:15px; color:#333"><b>입금자명 : </b>&nbsp;&nbsp;<input name="ipt_name" type="text" class="input_36" value="" style="width:220px;" placeholder="입금자명" /><span style="padding-left:20px; font-size:12px">* 무통장 입금 시 입금자명에 아이디를 적으시면 충전과정이 빠르게 처리됩니다.</span></p>
	</div>

	<div class="bot_btn" style="margin-bottom:30px; text-align:center">
		<input type="submit" id="signup-button" class="sowsms-inp-submit" value="충전요청">
    </div>
</form>
</div>
        </td>
</tr>
</table>

</div>



<div class="alpha60" id="bak" style="width:100%; height:100%; left:0px; top:0px; position:absolute; z-index:9999; display:none;" align="center">
<table border="0" width="100%" height="100%">
<tr><td align="center" valign="middle" onClick="hiddenBigImg_fn()"><img id="bigimg" name="bigimg" alt="" style="border-width:5px; border-color:white; border-style:solid"  />
</td></tr></table>
</div>
<!-- content end -->

<script type="text/javascript">
var setBankbook = function () {
	var ipt_amount = $.trim($("input[name=ipt_amount]").val());
	if (ipt_amount == '') {
		alert("결제 금액을 입력하세요.");
		return false;
	}
    var regType = /^[0-9]*$/;
    if (!regType.test(ipt_amount)) {
        alert("결제 금액은 숫자만 입력하세요.");
        return false;
    }
    var int_amount = parseInt(ipt_amount);
    if (int_amount < 10000) {
        alert("결제 최저 금액은 10,000원 입니다.");
        return false;
    }

	var ipt_name = $.trim($("input[name=ipt_name]").val());
	if (ipt_name == '') {
		alert("입금자명을 입력하세요.");
		return false;
	}
	return true;
}
</script>