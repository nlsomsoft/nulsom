<!DOCTYPE html>
<html>
<head>
<title>SOWKOREA</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
    include_once(VIEWPATH.'/templates/head.php');
?>
</head>

<body>

<!-- content start -->
<div>

<table width="600px" border="0">
    <tr>
        <td style="padding-left:10px;background-color:#555;color:#fff;font-weight:bold;height:50px;">받는사람 추가하기</td>
    </tr>
    <tr>
        <td>
<?php
    $g_sms_header_flag = 'addr';
    include_once(VIEWPATH.'/templates/append_popup_header.php');
?>

        <div class="menu_tip">
        <p> -수정 및 변경은 주소록 메뉴에서 가능합니다.</p>
        </div>
        </td>
    </tr>
    <tr>
        <td align="center">
            <div class="body_title" style="padding-left:10px;position:relative;">
              <div class="btn2-group" style="padding-top:4px;padding-left:5px;">
                <a href="/append/group_popup" class="btn1-white btn1-space" onmouseover="this.style.backgroundColor='#F4F4F4';return true;" onmouseout="this.style.backgroundColor=''; return true;"><div style="width:80px;text-align:center;">그룹</div></a>
                <a href="/append/person_popup" class="btn1-black btn1-space" ><div style="width:80px;text-align:center;">개인</div></a>
              </div>

              <?php
                  $g_search_action = 'append/person_popup';
                  include_once(VIEWPATH.'/templates/div_search.php');
              ?>
            </div>
            <div class="board">
            <?php
                $attributes = array(
                    'name' => 'frmAddrPerson',
                    'id' => 'frmAddrPerson'
                );
                echo form_open('/append/person', $attributes);
            ?>
            <?php
            $data = array(
                'mobile_list' => '',
            );
            echo form_hidden($data);
            ?>
                <table border="0" class="basic">
                    <colgroup><col style="width: 20px">
                    <col style="width: 40px">
                    <col style="width: 150px">
                    <col style="width: 195px;">
                    <col style="width: 195px;">
                    <col>
                    </colgroup>
                    <thead>
                        <tr align="center">
                            <th scope="col"></th>
                            <th scope="col"><input type="checkbox" name="all_check_bt" /></th>
                            <th scope="col">휴대폰</th>
                            <th scope="col">그룹명</th>
                            <th scope="col">이름</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach ($result as $key => $row) {
                    ?>
                        <tr class="font-13" bgcolor="#FFFFFF" onMouseOver="this.style.backgroundColor='#F4F4F4';return true;" onMouseOut="this.style.backgroundColor=''; return true;">
                            <td></td>
                            <td><input type="checkbox" name="chk_seq_no" value="<?=("{$row->ano}")?>" /></td>
                            <td><?=format_phone($row->mobile)?></td>
                            <td><?=$row->group_name?></td>
                            <td><?=$row->name?></td>
                        </tr>
                    <?php
                        }
                    ?>
                    </tbody>
                </table>
        </form>
            </div>
        </div>
     </td>
    </tr>
    <tr>
        <td align="center">
            <?=$this->pagination->create_links();?>
        </td>
    </tr>
    <tr>
        <td align="center">
            <div class="btn1-group" style="text-align:center; margin-top:30px">
                <a id="export_string" class="btn1-orange btn1-space" onclick="addTargetMobile();"><div style="width:60px;text-align:center;">추가</div></a>
                <a id="close_popup" class="btn1-white btn1-space"><div style="width:60px;text-align:center;">닫기</div></a>
            </div>
        </td>
    </tr>
</table>
</div>
<div style="height:70px;">&nbsp;</div>
</td></tr></table>
<!-- content end -->

<script type="text/javascript">
var addTargetMobile = function () {
    var mobile_list = "";
    var successCount = 0;
    var errorCount = 0;

    $("input[name=chk_seq_no]:checked").each(function() {
        if (mobile_list != "") mobile_list += ",";
        mobile_list += $(this).val();
        successCount ++;
    });
    if (!successCount) {
        alert("추가할 목록을 선택하세요.");
        return;
    }
    // var alarm_message = "정상 : " + successCount + "건\n오류 : " + errorCount + "\n계속 진행하시겠습니까?";
    // if (!confirm(alarm_message)) {
    //     return;
    // }
    if (successCount < 1) {
        alert("등록할 유효한 데이타가 없습니다.");
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
        return;
    }

    var csrf_sowkorea_name = $.trim($("input[name=csrf_sowkorea_name]").val());
    $.ajax({
        type: "POST",
        url: "/append/<?=($this->session->userdata('elect') === true ? 'elect_person' : 'person')?>",
        data: {
            "csrf_sowkorea_name" : csrf_sowkorea_name,
            "mobile_list" : mobile_list,
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

<script src="/js/remote.js?v=<?=CSS_JS_INST?>"></script>
<link rel="stylesheet" href="/css/remote.css?v=<?=CSS_JS_INST?>" />
<div class="go-menu" style="display:inline; z-index: 120;">
<ul id="menu" class="mfb-component--br mfb-zoomin" data-mfb-toggle="click" style="margin:10px;">
    <li class="mfb-component__wrap">
        <a class="mfb-component__button--main" style="visibility: visible;" onclick="addTargetMobile();">
            <img class="mfb-component__main-icon--resting" src="/images/rm_add.png">
        </a>
    </li>
</ul>

<ul id="goTop" class="mfb-component--br" style="z-index: 10;bottom:75px; display:none;">
<a class=" mfb-component__button--child" style="opacity: 0.75;">
<img class="mfb-component__main-icon--active" style="opacity:1; padding: 11px" src="/images/rm_top.png">
</a>
</ul>
</div>

</body>
</html>