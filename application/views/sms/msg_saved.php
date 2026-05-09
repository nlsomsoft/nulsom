<!DOCTYPE html>
<html>
<head>
<title><?=BRAND?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
    include_once(VIEWPATH.'/templates/head.php');
?>
</head>

<body class="">
<!-- content start -->
<div>

<table width="720px" border="0">
    <tr>
        <td style="padding-left:10px;background-color:#555;color:#fff;font-weight:bold;height:50px;">메시지 보관함</td>
    </tr>
    <tr>
        <td>
<div class="tab_menu1">
    <ul>
    <li class="on"><a href="/sms/msg_saved"><font color="#fff">단문.장문</font></a></li>
    <li><a href="#" onclick="alert('포토문자 보내기에서 이용 가능합니다.'); return false;">포토</a></li>
    </ul>
</div>

        <div class="menu_tip">
        <p> - 저장된 메시지를 클릭하면 메시지 입력창에 제목과 내용이 입력됩니다.</p>
        </div>
        </td>
    </tr>
    <tr>
        <td align="center">
            <div class="body_title" style="padding-left:10px;position:relative;">
            <div class="btn2-group" style="padding-top:4px;padding-left:5px;">
                <a href="#" class="btn1-white btn1-space" id="all_check_button"><div style="width:80px;text-align:center;">전체선택</div></a>
                <a href="#" class="btn1-white btn1-space" onclick="deleteSavedMessage();"><div style="width:80px;text-align:center;">삭제</div></a>
              </div>

              <?php
                    $g_placeholder = '제목/내용으로 검색';
                    $g_search_action = 'sms/msg_saved';
                    include_once(VIEWPATH.'/templates/div_search.php');
              ?>
            </div>
            <div class="board">
            <?php
                $attributes = array(
                    'name' => 'frmSavedMsg',
                    'id' => 'frmSavedMsg'
                );
                echo form_open('/sms/del_smsg', $attributes);
            ?>
            <?php
                $data = array(
                    'all_check_val' => '0',
                    'chk_nums' => '',
                );
                echo form_hidden($data);
            ?>
                <table border="0" class="basic">
                    <tbody>
                <?php
                    $i = 0;
                    foreach ($result as $key => $row) {
                        $ii = (int)($i % 3);
                ?>
                <?php if (!$ii) { ?>
                    <tr class="font-13">
                    <td align="center">
                        <div class="my_smms_list" style="margin-left:15px;">
                <?php } ?>
                            <dl class="<?=($row->send_type == '1' ? 'sms' : 'mms')?> <?=($ii == 2 ? 'last' : '')?>">
                                <dt>
                                    <p>
                                        <input type="hidden" name="send_type_<?=$row->smno?>" id="send_type_<?=$row->smno?>" value="<?=$row->send_type?>" />
                                        <input class="chk" name="chk_seq_no" id="smno_<?=$row->smno?>" type="checkbox" value="<?=$row->smno?>" />
                                        <label class="subject font-13" id="subject_<?=$row->smno?>"><?=$row->subject?></label>
                                    </p>
                                </dt>
                                <dd class="textarea">
                                    <textarea class="text" rows="5" cols="8" id="msg_<?=$row->smno?>" readonly="" onclick="setSavedMessage('<?=$row->smno?>');"><?=$row->msg?></textarea>
                                </dd>
                                <!--<dd class="byte"><p>97byte</p></dd>-->
                            <dd class="option">
                                <p class="date font-12"><?=mydate_format('Y-m-d',$row->add_date)?></p>
                                <p class="byte font-12"><span id="bytes_<?=$row->smno?>"><?=(int)$row->bytes?></span>byte</p>
                            </dd>
                            </dl>
                <?php
                        $i ++;
                    }
                ?>
                <?php if ($i) { ?>
                        </div>
                    </td>
                    </tr>
                <?php } ?>

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
var setSavedMessage = function (smno) {
    var send_type = $.trim($("#send_type_"+smno).val());
    $("#msgbody",opener.document).val($("#msg_"+smno).val());
    $("#msg_subject",opener.document).val($("#subject_"+smno).text());
    // $("#msgByte",opener.document).html($("#bytes_"+smno).text());
    $(opener.location).attr("href","javascript:changeBodyForm1('"+send_type+"');");
    alert("메시지 입력창에 제목과 내용이 입력되었습니다.");
}
$(document).ready(function() {
    $("#all_check_button").click(function(){
        if($("input[name=all_check_val]").val() == '0') {
            $("input[name=chk_seq_no]").prop("checked",true);
            $("input[name=all_check_val]").val('1');
        } else {
            $("input[name=chk_seq_no]").prop("checked",false);
            $("input[name=all_check_val]").val('0');
        }
    })
});


var isRun = false;
var deleteSavedMessage = function () {
    var k = 0;
    var chk_nums = "";

    if (isRun == true) return;
    $("input[name=chk_seq_no]:checked").each(function() {
        if (chk_nums != "") chk_nums += ",";
        chk_nums += $(this).val();
    });
    if (chk_nums == "") {
        alert("삭제할 목록을 선택하세요.");
        return;
    }

    isRun = true;
    var csrf_sowkorea_name = $.trim($("input[name=csrf_sowkorea_name]").val());
    $.ajax({
        type: "POST",
        url: "/sms/delete_smsg",
        data: {
            "csrf_sowkorea_name" : csrf_sowkorea_name,
            "chk_nums" : chk_nums,
            "where" : "sms"
        },
        dataType: "json",
        async: false,
        success : function(data, status, xhr) {
            isRun = false;
            $("input[name=csrf_sowkorea_name]").val(data.csrf_sowkorea_name);
            $(opener.location).attr("href","javascript:control_csrf('"+data.csrf_sowkorea_name+"');");
            if (data.message != "") alert(data.message);
            $(location).attr('href', '/sms/msg_saved');
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

</body>
</html>