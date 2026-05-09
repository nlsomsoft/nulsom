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
            <p style="margin-bottom:15px; color:#333"><b>상품별 적용단가</b><span class="font_12"> (VAT포함)</span></p>
    </div>
        <table width="100%" style="font-size:14px; border: 1px solid #ccc;">
            <tr bgcolor="#F6F6F6" style="border: 1px solid #ccc; border-top: 1px solid #000">
                <td width="50%" height="40px" style="border-right: 1px solid #ccc; padding-left:25px; color:#222"><b>SMS-단문</b></td>
                <td width="50%" style="padding-left:25px"><b>LMS-장문</b></td>
            </tr>
            <tr bgcolor="#FFFFFF" style="border: 1px solid #ccc;">
                <td height="50px" style="border-right: 1px solid #ccc; padding-left:25px;">일반단문 <?=$this->session->userdata('sms1')?>원, 선거단문 <?=$this->session->userdata('sms2')?>원</td>
                <td style="padding-left:25px;">일반장문 <?=$this->session->userdata('lms1')?>원, 선거장문 <?=$this->session->userdata('lms2')?>원</td>
            </tr>
            <tr bgcolor="#F6F6F6" style="border: 1px solid #ccc;">
                <td height="40px" style="border-right: 1px solid #ccc; padding-left:25px; color:#222"><b>MMS-포토</b></td>
                <td style="padding-left:25px"><b>KAKAO</b></td>
            </tr>
            <tr bgcolor="#FFFFFF" style="border: 1px solid #ccc;">
                <td height="50px" style="border-right: 1px solid #ccc; padding-left:25px;">일반포토 <?=$this->session->userdata('mms1')?>원, 선거포토 <?=$this->session->userdata('mms2')?>원</td>
                <td style="padding-left:25px;">알림톡 <?=$this->session->userdata('kat')?>원, 친구톡 <?=$this->session->userdata('kft')?>원, 친구톡포토 <?=$this->session->userdata('kftm')?>원</td>
            </tr>
        </table>

    <div style="margin-top:30px;">
            <p style="margin-bottom:15px; color:#333"><b>결제방식을 선택해 주세요.</b></p>
    </div>
<?php
    $attributes = array(
        'name' => 'frmPay',
        'id' => 'frmPay',
    );
    echo form_open('/pay/deposit', $attributes);
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
    <td width="220" valign="middle" style="cursor:pointer">
        <table id="pt_0" width="100%" style="border: 1px solid #ccc;" onClick="selecPayType(0);">
            <tr><td height="40"></td></tr>
            <tr><td align="center"><img src="/images/bank_icon03.gif" alt="" /></td></tr>
            <tr><td height="40" align="center"><input type="radio" name="rd_pay_type" value="0"> 무통장입금</td></tr>
            <tr><td height="40"></td></tr>
        </table>
    </td>
    <td width="27"></td>
    <td width="220" align="center" valign="middle" style="cursor:pointer">
        <table id="pt_1" width="100%" style="border: 1px solid #ccc;" onClick="selecPayType(1);">
            <tr><td height="40"></td></tr>
            <tr><td align="center"><img src="/images/bank_icon05.gif" alt="" /></td></tr>
            <tr><td height="40" align="center"><input type="radio" name="rd_pay_type" value="1"> 실시간계좌이체</td></tr>
            <tr><td height="40"></td></tr>
        </table>
    </td>
    <td width="27"></td>
    <td width="220" align="center" valign="middle" style="cursor:pointer">
        <table id="pt_2" width="100%" style="border: 1px solid #ccc;" onClick="selecPayType(2);">
            <tr><td height="40"></td></tr>
            <tr><td align="center"><img src="/images/bank_icon03.gif" alt="" onClick="selecPayType(2);" /></td></tr>
            <tr><td height="40" align="center"><input type="radio" name="rd_pay_type" value="2"> 가상계좌입금</td></tr>
            <tr><td height="40"></td></tr>
        </table>
    </td>
    <td></td>
    <td width="220" valign="middle" style="cursor:pointer">
        <table id="pt_3" width="100%" style="border: 1px solid #ccc;" onClick="selecPayType(3);">
            <tr><td height="40"></td></tr>
            <tr><td align="center"><img src="/images/bank_icon02.gif" alt="" onClick="selecPayType(3);" /></td></tr>
            <tr><td height="40" align="center"><input type="radio" name="rd_pay_type" value="3"> 신용카드결제</td></tr>
            <tr><td height="40"></td></tr>
        </table>
    </td>
    </tr>
</table>

    <div style="margin-top:25px;">
            <p style="margin-bottom:15px; color:#333"><b>결제금액 : </b>&nbsp;&nbsp;<input name="ipt_amount" type="text" class="input_36" placeholder="결제금액" style="width:220px;" /><span style="padding-left:20px; font-size:12px">* 최저금액은 10,000원 입니다.</span></p>
    </div>

    <div style="margin-top:20px; display:none;" id="div_bankbook_name">
            <p style="margin-bottom:15px; color:#333"><b>입금자명 : </b>&nbsp;&nbsp;<input name="ipt_name" type="text" class="input_36" value="" style="width:220px;" placeholder="입금자명" /><span style="padding-left:20px; font-size:12px">* 무통장 입금 시 입금자명에 아이디를 적으시면 충전과정이 빠르게 처리됩니다.</span></p>
    </div>

    <div class="bot_btn" style="margin-bottom:30px; text-align:center">
        <img src="/images/proc_bill.gif" width="140" height="44" onclick="depositAmount();" style="cursor:pointer" />
    </div>
</form>

</div>


<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td height="30"></td></tr>
<tr><td><img src="/images/tip.gif" width="145" height="34" alt="" /></td></tr>
<tr><td height="1" bgcolor="#CCCCCC"></td></tr>
<tr><td class="help_txt">
        * 1회 결제하실 수 있는 최저금액은 10,000원입니다.<br>
        * 무통장입금의 경우 입금자명에 아이디를 적으시면 충전과정이 빠르게 처리됩니다.
</td></tr>
<tr><td height="30px"></td></tr>
</table>
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


<script language="Javascript">
function selecPayType(num) {
    $("input:radio[name='rd_pay_type']:input[value=" + num + "]").prop('checked', true);
    if (num == 0) {
        $("#pt_0").attr("style", "border: 3px solid #FF0000;");
        $("#pt_1").attr("style", "border: 1px solid #ccc;");
        $("#pt_2").attr("style", "border: 1px solid #ccc;");
        $("#pt_3").attr("style", "border: 1px solid #ccc;");
        $("#div_bankbook_name").show();
    } else if( num == 1) {
        $("#pt_0").attr("style", "border: 1px solid #ccc;");
        $("#pt_1").attr("style", "border: 3px solid #FF0000;");
        $("#pt_2").attr("style", "border: 1px solid #ccc;");
        $("#pt_3").attr("style", "border: 1px solid #ccc;");
        $("#div_bankbook_name").hide();
    } else if( num == 2) {
        $("#pt_0").attr("style", "border: 1px solid #ccc;");
        $("#pt_1").attr("style", "border: 1px solid #ccc;");
        $("#pt_2").attr("style", "border: 3px solid #FF0000;");
        $("#pt_3").attr("style", "border: 1px solid #ccc;");
        $("#div_bankbook_name").hide();
    } else if( num == 3) {
        $("#pt_0").attr("style", "border: 1px solid #ccc;");
        $("#pt_1").attr("style", "border: 1px solid #ccc;");
        $("#pt_2").attr("style", "border: 1px solid #ccc;");
        $("#pt_3").attr("style", "border: 3px solid #FF0000;");
        $("#div_bankbook_name").hide();
    }
}
var depositAmount = function () {
    if ($('input:radio[name=rd_pay_type]').is(':checked') == false) {
        alert("결제방식을 선택하세요.");
        return false;
    }

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

    var rd_pay_type = $(':radio[name="rd_pay_type"]:checked').val();
    if (rd_pay_type == '0') {
        var ipt_name = $.trim($("input[name=ipt_name]").val());
        if (ipt_name == '') {
            alert("입금자명을 입력하세요.");
            return false;
        }
        $("form#frmPay").attr("action", "/pay/bankbook");
        $("form#frmPay").submit();
    } else {
        $("form#frmPay").attr("action", "/inicis/request");
        $("form#frmPay").submit();
    }
}
</script>