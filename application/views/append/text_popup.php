<!DOCTYPE html>
<html>
<head>
<title>SOWKOREA</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
    include_once(VIEWPATH.'/templates/head.php');
?>
<link rel="stylesheet" href="/css/handsontable.css">
</head>

<body class="">

<!-- content start -->
<div>

<?php
    $attributes = array(
        'name' => 'frmTextPopup',
        'id' => 'frmTextPopup'
    );
    echo form_open('/append/string/text', $attributes);
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
    $g_sms_header_flag = 'text';
    include_once(VIEWPATH.'/templates/append_popup_header.php');
?>
        <div class="menu_tip">
        <p>- 최대 <span style="color:#FF6600">50,000개</span>까지 등록할 수 있습니다.<br>
        - 핸드폰 번호는 엔터(Enter) 또는 콤마(,)로 구분하여 입력해야 합니다.</p>
        </div>
        </td>
    </tr>

    <tr class="font-13">
      <td align="left">
            <div>
                <textarea style="width:590px;height:350px;" name="bulklist_telnumlist" id="bulklist_telnumlist" placeholder="입력 방법: 010XXXXXXX1, 010XXXXXXX2" cols="70" rows="20"></textarea>
            </div>
     </td>
    </tr>
    <tr>
        <td align="center">
            <div class="btn1-group" style="text-align:center; margin-top:30px">
                <a id="export_string" class="btn1-orange btn1-space" onclick="bulklist_check_list(); return false;"><div style="width:60px;text-align:center;">추가</div></a>
                <a id="close_popup" class="btn1-white btn1-space"><div style="width:60px;text-align:center;">닫기</div></a>
            </div>
        </td>
    </tr>
</table>
</form>
</div>

</td></tr></table>

<script type="text/javascript">
var bulklist_telnumlist_enabled = false;
var isRun = false;
function bulklist_check_list() {
    if (isRun == true) {
        return;
    }

    isRun = true;
    var obj = document.getElementById("bulklist_telnumlist");
    if (obj.value == "") {
        alert("등록할 데이타가 없습니다.");
        var isRun = false;
        return;
    }
    var mobile_list = '';
    var bulk_max_cnt = 50000;
    var errorCount = 0;
    var successCount = 0;
    var arrayList = obj.value.split("\n");
    var length_List = arrayList.length;

    for (var i = 0; i < length_List; i++) {
        var arrayList1 = '';
        arrayList1 = arrayList[i].split(",");
        var loopCnt = arrayList1.length;

        for (var j = 0; j < loopCnt; j++) {
            var mobile = '';
            mobile = $.trim(arrayList1[j].replace(/[-\(\) ]/g, ""));
            if (mobile == '') continue;

            var f_str = mobile.substr(0, 1);
            if (f_str != "0") {
                mobile = "0" + mobile;
            }

            if (!isMobileNumber(mobile)) {
                errorCount ++;
                continue;
            }
            if (mobile_list != '') mobile_list += ',';
            mobile_list += mobile;
            successCount ++;
        }
    }
    if (successCount < 1) {
        alert("등록할 유효한 데이타가 없습니다.");
        var isRun = false;
        return;
    }

    if (successCount > bulk_max_cnt) {
        alert("직접붙여넣기에서 1회 최대 "+bulk_max_cnt+"개까지 등록할 수 있습니다.");
        var isRun = false;
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
        alert("받는사람 목록의 등록 제한 개수 "+max_limit_count+"개를 초과하셨습니다.");
        var isRun = false;
        return;
    }

    var csrf_sowkorea_name = $.trim($("input[name=csrf_sowkorea_name]").val());
    $.ajax({
        type: "POST",
        url: "/append/<?=($this->session->userdata('elect') === true ? 'elect_string' : 'string')?>/text",
        data: {
            "csrf_sowkorea_name" : csrf_sowkorea_name,
            "mobile_list" : mobile_list,
            "type" : "text",
            "where" : "sms"
        },
        dataType: "json",
        async: false,
        success : function(data, status, xhr) {
            isRun  = false;
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
}

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

