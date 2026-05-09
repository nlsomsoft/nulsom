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
                $g_left_menu_flag = 'result';
                include_once(VIEWPATH.'/templates/left_menu.php');
            ?>
        </td>
			<!-- left menu end -->
			<td width="30"></td>
			<td width="960" valign="top">


<div class="content_wrap">
    <div class="body_title" style="position:relative;">발송통계 - 상품
	</div>
    <div style="margin-bottom:15px">

<?php
    $attributes = array(
        'name' => 'frmSearchDate',
        'id' => 'frmSearchDate',
        'method' => 'get',
    );
    echo form_open('/result/stats', $attributes);
?>
        <table width="100%" border="0" cellspacing="0" cellpadding="1">
			<tr>
            	<td align="right">기간선택 :&nbsp;</td>
		 		<td width="170" align="right"><input type="text" id="date_from" name="date_from" style="float:left; width:120px; margin-right:3px;" class="input_261" readonly value="<?=$date_from?>" />&nbsp;~&nbsp;</td>
				<td width="150" align="right"><input type="text" id="date_to" name="date_to" style="float:left; width:120px; margin-right:3px;" class="input_261" readonly value="<?=$date_to?>" /></td>
				<td width="60" align="right"><div class="style_btn" style="width:50px"><a href="#" onclick="searchStats();">조회</a></div></td>
			</tr>
		</table>
</form>
    </div>
    <div class="board">
        <table border="0" class="basic" style="font-size:14px">
            <colgroup>
            <col style="width: 20%">
            <col style="width: 20%">
            <col style="width: 20%">
            <col style="width: 20%">
            <col style="width: 20%">
            </colgroup>
            <thead>
                <tr align="center">
                    <th scope="col">상품구분</th>
                    <th scope="col">전체건수</th>
                    <th scope="col">성공</th>
                    <th scope="col">실패</th>
                    <th scope="col">사용금액</th>
                </tr>
            </thead>
            <tbody>
        <?php
            $total_t = 0;
            $total_s = 0;
            $total_f = 0;
            $total_r = 0;
            foreach ($result as $key => $row) {
        ?>
                <tr bgcolor="#FFFFFF">
                    <td><?=convert_product_code($row->productcode)?></td>
                    <td><?=number_format($row->total_units)?></td>
                    <td><?=number_format($row->success)?></td>
                    <td><?=number_format($row->fail)?></td>
                    <td><?=number_format($row->realamount)?></td>
                </tr>
        <?php
                $total_t += $row->total_units;
                $total_s += $row->success;
                $total_f += $row->fail;
                $total_r += $row->realamount;
            }
        ?>
                <tr bgcolor="#F4F4F4">
                    <td>합계</td>
                    <td><?=number_format($total_t)?></td>
                    <td><?=number_format($total_s)?></td>
                    <td><?=number_format($total_f)?></td>
                    <td><?=number_format($total_r)?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td height="30"></td></tr>
<tr><td><img src="/images/tip.gif" width="145" height="34" alt="" /></td></tr>
<tr><td height="1" bgcolor="#CCCCCC"></td></tr>
<tr><td class="help_txt">
        <span style="color:#ff7e30;">* 현재 발송이 진행 중이거나 예약발송은 포함되어 있지 않습니다.</span><br>
        * 발송통계는 최근 6개월까지 조회 가능합니다.
</td></tr>
<tr><td height="30px"></td></tr>
</table>
        </td>
</tr>
</table>

</div>

<!-- content end -->
<script type="text/javascript">
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
//<![CDATA[
$(function(){
    $("#rsv_date").datepicker({
        showOn: "button",
        buttonImage: "/images/ico_calendar.gif",
        buttonImageOnly: false,
        buttonText: "Select date"
    });
});
//]]>
var searchStats = function () {
    if ($("#date_from").val() == "" || $("#date_to").val() == "") {
        alert("기간을 선택하세요.");
        return;
    }
    $("form#frmSearchDate").submit();
}
</script>