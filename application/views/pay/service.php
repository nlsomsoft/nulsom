<!-- datepicker -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/css/handsontable.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="/js/datepicker-ko.js"></script>
<!-- /datepicker -->

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
    <div class="body_title" style="position:relative;">서비스이용내역
	</div>
    <div style="margin-bottom:15px">
    <?php
        $attributes = array(
            'name' => 'frmPay',
            'id' => 'frmPay',
            'method' => 'get'
        );
        echo form_open('/pay/service', $attributes);
    ?>

    <?php
        $date_from = ($date_from == '0000-00-00' ? '' : $date_from);
        $date_to = ($date_to == '0000-00-00' ? '' : $date_to);
    ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="1">
			<tr>
            	<td align="right">기간선택 :&nbsp;</td>
		 		<td width="170" align="right">
                    <input type="text" id="date_from" name="date_from" style="float:left; width:120px; margin-right:3px;" class="input_261" readonly value="<?=$date_from?>" />&nbsp;~&nbsp;
                </td>
				<td width="150" align="right">
                    <input type="text" id="date_to" name="date_to" style="float:left; width:120px; margin-right:3px;" class="input_261" readonly value="<?=$date_to?>" />
                </td>
				<td width="60" align="right"><div class="style_btn" style="width:50px"><a href="#" onclick="doSearch();">조회</a></div></td>
			</tr>
		</table>
    </form>
    </div>
    <div class="board">
        <table border="0" class="basic" style="font-size:14px">
            <colgroup>
            <col style="width: 25%">
            <col style="width: 25%">
            <col style="width: 25%">
            <col style="width: 25%">
            </colgroup>
            <thead>
                <tr align="center">
                    <th scope="col">날짜</th>
                    <th scope="col">금액</th>
                    <th scope="col">내용</th>
                    <th scope="col">비고</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($result as $row) { ?>
                <tr bgcolor="#FFFFFF">
                    <td><?=mydate_format('Y-m-d H:i',$row->reg_time)?></td>
                    <td><?=number_format($row->amount,2)?></td>
                    <td><?=convert_billing_mode($row->mode)?></td>
                    <td><?=$row->memo?></td>
                </tr>
            <?php } ?>
            <?php /* ?>
                <tr bgcolor="#F4F4F4">
                    <td>합계</td>
                    <td>1,110,000</td>
                    <td>2건</td>
                    <td></td>
                </tr>
            <?php */ ?>
            </tbody>
        </table>
    </div>
</div>

<div><?=$this->pagination->create_links();?></div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td height="30"></td></tr>
<tr><td><img src="/images/tip.gif" width="145" height="34" alt="" /></td></tr>
<tr><td height="1" bgcolor="#CCCCCC"></td></tr>
<tr><td class="help_txt">
        * 세금계산서는 월 결제금액을 합산하여 매월 말일자로 익월초 발행됩니다.<br>
        * 분기 마감된 세금계산서는 발행이 불가능합니다.<br>
        * 세금계산서 발급은 신청한 회원님에 대해서만 발행되며, 신용카드 결제금액은 제외됩니다.
</td></tr>
<tr><td height="30px"></td></tr>
</table>
        </td>
</tr>
</table>

</div>
<!-- content end -->
<script type="text/javascript">
var doSearch = function () {
    if ($("#date_from").val() == "" || $("#date_to").val() == "") {
        alert("기간을 선택하세요.");
        return;
    }
    $("form#frmPay").attr("action", "/pay/service");
    $("form#frmPay").submit();
}
//<![CDATA[
$(function(){
    $("#date_from").datepicker({
        showOn: "button",
        buttonImage: "/images/ico_calendar.gif",
        buttonImageOnly: false,
        buttonText: "날자를 선택해 주세요."
    });
});

$(function(){
    $("#date_to").datepicker({
        showOn: "button",
        buttonImage: "/images/ico_calendar.gif",
        buttonImageOnly: false,
        buttonText: "날자를 선택해 주세요."
    });
});
//]]>
</script>