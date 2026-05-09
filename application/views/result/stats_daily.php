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
    <div class="body_title" style="position:relative;">발송통계 - 일별 </div>
    <div style="margin-bottom:15px">

<?php
    $attributes = array(
        'name' => 'frmSearchDate',
        'id' => 'frmSearchDate',
        'method' => 'get',
    );
    echo form_open('/result/stats_daily', $attributes);

    $data = array(
        'total_cnt' => '',
    );
    echo form_hidden($data);
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
            <col style="width: 12%">
            <col style="width: 22%">
            <col style="width: 22%">
            <col style="width: 22%">
            <col style="width: 22%">
            </colgroup>
            <thead>
                <tr align="center">
                    <th scope="col">선택</th>
                    <th scope="col">일자</th>
                    <th scope="col">전체건수</th>
                    <th scope="col">성공</th>
                    <th scope="col">실패</th>
                </tr>
            </thead>
            <tbody>
        <?php
            foreach ($result as $key => $row) {
        ?>
                <tr bgcolor="#FFFFFF">
                    <td><input type="radio" name="rd_date" value="<?=(sprintf('%04d-%02d-%02d', $row->yyyy, $row->mm, $row->dd))?>" onclick="divideResultPage('<?=(int)$row->total_units?>');" /></td>
                    <td><?=(sprintf('%04d-%02d-%02d', $row->yyyy, $row->mm, $row->dd))?></td>
                    <td><b><?=number_format($row->total_units)?></b></td>
                    <td><?=number_format($row->success)?></td>
                    <td><?=number_format($row->fail)?></td>
                </tr>
        <?php
                $total_t += $row->total_units;
                $total_s += $row->success;
                $total_f += $row->fail;
            }
        ?>
            </tbody>
        </table>
        <table border="0" class="basic" style="font-size:14px">
            <tbody>
                <tr bgcolor="#F4F4F4">
                    <td width="200px">
    <?php
        $options = array();
        $options['0'] = '다운로드 범위';
        $js = 'class="input_261" style="width:200px"';
        echo form_dropdown('ipt_page', $options, '', $js);
    ?>
                    </td>
                    <td>
                       <div class="style_btn1" style="width:130px;">
                        <a href="#" onclick="downloadExcel();">Excel ( 엑셀 ) 다운로드</a>
                       </div>
                   </td>
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
        <span style="color:#ff7e30;">* 엑셀 다운로드 범위는 최고 100,000 개를 나뉘어 다운 받을 수 있습니다.</span><br />
        * 현재 발송이 진행 중인 건도 포함되었습니다.<br />
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
var divideResultPage = function (arv) {
    if (arv == '') return;
    var result = parseInt(arv / 100000);
    var remainder = parseInt(arv % 100000);
    if (remainder > 0) result = result + 1;

    $("input[name=total_cnt]").val(arv);

    $("select[name=ipt_page]").find("option").remove().end().append("<option value='0'>다운로드 범위</option>");;

    if (arv <= 100000) {
        $("select[name=ipt_page]").append("<option value='"+1+"'>전체 ("+comma(arv)+")</option>");
    } else {
        var min_page = 0;
        var max_page = 0;
        for (var i = 1; i <= result; i ++) {
            min_page = ((i - 1) * 100000) + 1;
            max_page = (i * 100000);
            if (i == result) max_page = arv;
            $("select[name=ipt_page]").append("<option value='"+i+"'>" + comma(min_page) + " ~ "+ comma(max_page) +"</option>");
        }
    }
}
function comma(num){
    var len, point, str;

    num = num + "";
    point = num.length % 3 ;
    len = num.length;

    str = num.substring(0, point);
    while (point < len) {
        if (str != "") str += ",";
        str += num.substring(point, point + 3);
        point += 3;
    }
    return str;
}
var searchStats = function () {
    if ($("#date_from").val() == "" || $("#date_to").val() == "") {
        alert("일자를 선택하세요.");
        return;
    }
    $("form#frmSearchDate").submit();
}
</script>
<script type="text/javascript">
var downloadExcel = function() {
    if ($('input:radio[name=rd_date]').is(':checked') == false) {
        alert('일자를 선택하세요.');
        return;
    }

    var ipt_page = $("select[name=ipt_page]").val();
    if (ipt_page == '0') {
        alert("다운로드 범위를 선택하세요.");
        return;
    }
    var rd_date = $("input[name='rd_date']:checked").val();
    var total_cnt = $("input[name=total_cnt]").val();
    $(location).attr('href', '/result/daily_excel/'+rd_date+'/'+ipt_page+'/'+total_cnt);
}
</script>