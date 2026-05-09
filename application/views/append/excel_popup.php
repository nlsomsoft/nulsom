<!DOCTYPE html>
<html>
<head>
<title>SOWKOREA</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
    $g_head_flag = 'handsontable';
    include_once(VIEWPATH.'/templates/head.php');
?>
<link rel="stylesheet" href="/css/handsontable.css">
</head>

<body class="">

<!-- content start -->
<div>

<?php
    $attributes = array(
        'name' => 'frmExcelPopup',
        'id' => 'frmExcelPopup'
    );
    echo form_open('/append/string/excel', $attributes);
?>
<?php
$data = array(
    'mobile_list' => '',
);
echo form_hidden($data);
?>
<table width="600px" border="0">
    <tr>
        <td style="padding-left:10px;background-color:#555;color:#fff;font-weight:bold;height:50px;">받는사람 추가하기</td>
    </tr>
    <tr>
        <td>
<?php
    $g_sms_header_flag = 'excel';
    include_once(VIEWPATH.'/templates/append_popup_header.php');
?>
        <div class="menu_tip">
        <p>- 최대 <span style="color:#FF6600">50,000개</span>까지 등록할 수 있습니다.<br>
        - 이름, 전화번호 순으로 등록해 주세요.</p>
        </div>

        <div style="padding-left:2px; font-size:12px; font-family:'굴림','굴림체','Arial'"><div class="handsontable" id="hot"></div></div>

        </td>
    </tr>
    <tr>
        <td align="center">
            <div class="btn1-group" style="text-align:center; margin-top:30px">
                <a id="export_string" class="btn1-orange btn1-space"><div style="width:60px;text-align:center;">추가</div></a>
                <a id="close_popup" class="btn1-white btn1-space"><div style="width:60px;text-align:center;">닫기</div></a>
            </div>
        </td>
    </tr>
</table>
</form>
</div>

</td></tr></table>

<script type="text/javascript">
var dataObject = [];
var hotElement = document.querySelector('#hot');
var hotElementContainer = hotElement.parentNode;
var hotSettings = {
    data: dataObject,
    columns: [{data:'var1',type:'text'},{data:'var2',type:'text'}],
    stretchH: 'all',
    width: 594,
    height: 395,
    minSpareRows: 50000,
    maxRows: 50000,
    autoWrapRow: true,
    rowHeaders: true,
    contextMenu:true,
    currentRowClassName : 'currentRow',
    colHeaders: ['이름 (선택)','전화번호 (*필수)'],
    colWidths: [50,50],
    manualColumnResize: true,
    outsideClickDeselects : true,
    afterLoadData : function() {
    },
    contextMenu: {
        items: {
        "row_above": {
            name: '행 추가(위)'
            },
        "row_below": {
            name: '행 추가(아래)'
            },
        "remove_row": {
            name: '행 삭제'
            },
        }
    }
};

var hot = new Handsontable(hotElement, hotSettings);
var buttons = {
    string: document.getElementById('export_string')
};
var exportPlugin = hot.getPlugin('exportFile');
var send_data = document.getElementById('cvs_data');
var isRun = false;
buttons.string.addEventListener('click', function() {
    if (isRun == true) {
        return;
    }

    var reVal = exportPlugin.exportAsString('csv', {
        columnDelimiter: '|'
    });

    var arrayList = reVal.split("\n");
    var successCount = 0;
    var errorCount = 0;
    var mobile_list = '';
    var loopCnt = arrayList.length;
    for (var j = 0; j < loopCnt; j++) {
        var arrayList1 = arrayList[j].split("|");
        if ($.trim(arrayList1[1]) == '') continue;

        var destname = "";
        destname = $.trim(arrayList1[0].replace(/[-\(\) ]/g, ""));

        var mobile = "";
        mobile = $.trim(arrayList1[1].replace(/[-\(\) ]/g, ""));
        if (mobile == '') continue;

        var f_str = mobile.substr(0, 1);
        if (f_str != "0") {
            mobile = "0" + mobile;
        }

        if (!isMobileNumber(mobile)) {
            errorCount ++;
            continue;
        }
        if (mobile_list != "") mobile_list += ",";
        mobile_list += mobile + "|" + destname;
        successCount ++;
    }
    if (successCount < 1) {
        var isRun = false;
        alert("등록할 유효한 데이타가 없습니다.");
        return;
    }
    var bulk_max_cnt = 50000;
    if (successCount > bulk_max_cnt) {
        var isRun = false;
        alert("엑셀붙여넣기에서 1회 최대 "+bulk_max_cnt+"개까지 등록할 수 있습니다.");
        return;
    }

    <?php if ($this->session->userdata('elect') === true) { ?>
        var opener_total_count = $("#total_count", opener.document).val();
    <?php } else { ?>
        var opener_total_count = $("#target_count", opener.document).text();
    <?php } ?>
    var total_count = opener_total_count.replace(/,/i, '');
    total_count = parseInt(total_count);
    var max_limit_count = 100000;
    if ((total_count + successCount) > max_limit_count) {
        var isRun = false;
        alert("받는사람 목록의 등록 제한 개수 "+max_limit_count+"개를 초과하셨습니다.");
        return;
    }

    var csrf_sowkorea_name = $.trim($("input[name=csrf_sowkorea_name]").val());
    $.ajax({
        type: "POST",
        url: "/append/<?=($this->session->userdata('elect') === true ? 'elect_string' : 'string')?>/excel",
        data: {
            "csrf_sowkorea_name" : csrf_sowkorea_name,
            "mobile_list" : mobile_list,
            "type" : "excel",
            "where" : "sms"
        },
        dataType: "json",
        async: false,
        success : function(data, status, xhr) {
            // isRun  = false;
            $("input[name=csrf_sowkorea_name]").val(data.csrf_sowkorea_name);
            $(opener.location).attr("href","javascript:control_csrf('"+data.csrf_sowkorea_name+"');");
            $("#bulklist_telnumlist").val('');
            if (data.result == "success") {
                if (data.elect == '1') {
                    var total_count = parseInt(data.tcount);
                    var remain_count = parseInt(data.rcount);
                    var send_count = total_count - remain_count;
                    $("#total_count",opener.document).val(numberWithCommas(total_count));
                    $("#send_count",opener.document).val(numberWithCommas(send_count));
                    $("#remain_count",opener.document).val(numberWithCommas(remain_count));

                    var arrayList = data.keyinfo.split("|,|");
                    var length_List = arrayList.length;
                    for (var i = 0; i < length_List; i++) {
                        if (arrayList[i] == '') continue;
                        var prefix = '';
                        var key = '';
                        var type = '';
                        var count = '';
                        var arrayList1 = '';
                        arrayList1 = arrayList[i].split("|:|");
                        var loopCnt = arrayList1.length;
                        for (var j = 0; j < loopCnt; j++) {
                            if (j == 0) prefix = arrayList1[j];
                            else if (j == 1) key = arrayList1[j];
                            else if (j == 2) type = arrayList1[j];
                            else if (j == 3) count = arrayList1[j];
                        }

                        $(opener.location).attr("href","javascript:addTarget('"+prefix+"','"+key+"','"+type+"','"+count+"','');");
                    }
                } else {
                    //"{prefix}|:|{key}|:|{type}|:|{count}|,|";
                    // parent.addItem( "G", spl[0], spl[1], spl[2] );
                    var arrayList = data.keyinfo.split("|,|");
                    var length_List = arrayList.length;
                    for (var i = 0; i < length_List; i++) {
                        if (arrayList[i] == '') continue;
                        var prefix = '';
                        var key = '';
                        var type = '';
                        var count = '';
                        var arrayList1 = '';
                        arrayList1 = arrayList[i].split("|:|");
                        var loopCnt = arrayList1.length;
                        for (var j = 0; j < loopCnt; j++) {
                            if (j == 0) prefix = arrayList1[j];
                            else if (j == 1) key = arrayList1[j];
                            else if (j == 2) type = arrayList1[j];
                            else if (j == 3) count = arrayList1[j];
                        }
                        $(opener.location).attr("href","javascript:addItem('"+prefix+"','"+key+"','"+type+"','"+count+"');");
                    }
                }
            }
            if (data.message != '') alert(data.message);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.responseText);
        }
    });

});
</script>
<script type="text/javascript">
// 닫기
$("#close_popup").bind('click', function() {
    window.close();
});
</script>

<?php if ($this->session->flashdata('error_message')) { ?>
<script type="text/javascript">
$(document).ready(function(){
    alert("<?=$this->session->flashdata('error_message')?>");
});
</script>
<?php } ?>
<!-- content end -->
</body>
</html>

