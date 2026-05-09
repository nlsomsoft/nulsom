<!-- 이니시스 표준결제 js -->
<?pho /* ?><script language="javascript" type="text/javascript" src="https://stgstdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script><?pho */ ?>
<script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script>
<script type="text/javascript">
function pay() {
    INIStdPay.pay('SendPayForm_id');
}
</script>

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
    <div class="body_title" style="position:relative;">결제내역확인</div>

        <table width="100%" style="font-size:14px; border: 1px solid #ccc;">
            <tr bgcolor="#F6F6F6" style="border: 1px solid #ccc; border-top: 1px solid #000">
                <td width="33%" height="40px" style="border-right: 1px solid #ccc; padding-left:25px; color:#222"><b>결제상품</b></td>
                <td width="33%" style="border-right: 1px solid #ccc; padding-left:25px; color:#222"><b>결제방식</b></td>
                <td width="33%" style="padding-left:25px"><b>결제금액 (VAT포함)</b></td>
            </tr>
            <tr bgcolor="#FFFFFF" style="border: 1px solid #ccc;">
                <td height="50px" style="border-right: 1px solid #ccc; padding-left:25px;">문자서비스이용료</td>
        <?php
            if ($gopaymethod == 'DirectBank') $goods_name = '실시간계좌이체';
            else if ($gopaymethod == 'VBank') $goods_name = '가상계좌입금';
            else if ($gopaymethod == 'Card') $goods_name = '신용카드';
        ?>
                <td height="50px" style="border-right: 1px solid #ccc; padding-left:25px;"><?=$goods_name?></td>
                <td style="padding-left:25px;"><?=number_format($price)?></td>
            </tr>
        </table>

<?php
/*
    $attributes = array(
        'name' => 'SendPayForm_id',
        'id' => 'SendPayForm_id',
    );
    echo form_open('', $attributes);
*/
?>
<form id="SendPayForm_id" name="SendPayForm_name" method="POST">
<input type="hidden" name="version" value="1.0" />
<input type="hidden" name="mid" value="<?=$mid?>" />
<input type="hidden" name="goodname" value="문자서비스이용료" />
<input type="hidden" name="oid" value="<?=$orderNumber?>" />
<input type="hidden" name="price" value="<?=$price?>" />
<input type="hidden" name="currency" value="WON" />
<input type="hidden" name="buyername" value="<?=$this->session->userdata('realname')?>" />
<input type="hidden" name="buyertel" value="<?=$this->session->userdata('mobile')?>" />
<input type="hidden" name="buyeremail" value="<?=$this->session->userdata('email')?>" />
<input type="hidden" name="timestamp" value="<?=$timestamp?>" />
<input type="hidden" name="signature" value="<?=$sign?>" />
<input type="hidden" name="returnUrl" value="<?=$siteDomain?>inicis/result" />
<input type="hidden" name="mKey" value="<?=$mKey?>" />
<input type="hidden" name="gopaymethod" value="<?=$gopaymethod?>" />
<input type="hidden" name="offerPeriod" value="<?=date('Ymd')?>-<?=date('Ymd')?>" />
<input type="hidden" name="acceptmethod" value="va_receipt:vbanknoreg(0):vbank(<?=date('Ymd')?>):below1000" />
<input type="hidden" name="languageView" value="ko" />
<input type="hidden" name="charset" value="UTF-8" />
<input type="hidden" name="payViewType" value="overlay" />
<input type="hidden" name="closeUrl" value="<?=$siteDomain?>inicis/close" />
<input type="hidden" name="popupUrl" value="<?=$siteDomain?>inicis/popup" />
<input type="hidden" name="nointerest" value="<?=$cardNoInterestQuota ?>" />
<input type="hidden" name="quotabase" value="<?=$cardQuotaBase ?>" />
<input type="hidden" name="merchantData" value="" />

    <div class="bot_btn" style="margin-bottom:30px; text-align:center">
        <img src="/images/proc_bill.gif" width="140" height="44" onclick="pay()" style="cursor:pointer">
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
