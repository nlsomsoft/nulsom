<!-- content start -->
<div class="body-inner-table" style="padding-top:20px; min-height:700px;">

<table width="1200" border="0" cellspacing="0" cellpadding="0">
	<tr>
<!-- left menu start -->
		<td width="210" valign="top">
        <?php
            $g_left_menu_flag = 'address';
            include_once(VIEWPATH.'/templates/left_menu.php');
        ?>
        </td>
<!-- left menu end -->
		<td width="30"></td>
		<td width="960" valign="top">


<div class="content_wrap">
    <div class="body_title" style="position:relative;">그룹별 주소록
        <?php
            $g_search_action = 'address/search_list';
            include_once(VIEWPATH.'/templates/div_search.php');
        ?>
	</div>
    <div style="margin-bottom:15px">
        <table width="100%" border="0" cellspacing="0" cellpadding="1">
			<tr>
			<td align="right">그룹추가 : <input type="text" name="group_name" class="input_26" style="width:200px; background-color:#F2FFD7"></td>
			<td width="80">
                <div class="style_btn" style="width:70px"><a href="#" id="button-add-group">그룹추가</a></div>
            </td>
        <?php /* ?>
			<td width="80">
                <div class="style_btn" style="width:70px"><a href="#" id="button-send-message">메시지전송</a></div>
            </td>
        <?php */ ?>
			<td width="80">
                <div class="style_btn" style="width:70px"><a href="#" onclick="deleteGroupAddress();">그룹삭제</a></div>
            </td>
			</tr>
		</table>
    </div>
    <div class="board">
    <?php
        $attributes = array(
            'name' => 'frmGroupList',
            'id' => 'frmGroupList'
        );
        echo form_open('/address/group', $attributes);
    ?>
    <?php
        $data = array(
            'chk_nums' => '',
            'new_value' => '',
            'gno' => '',
            'append_addr' => '1',
        );
        echo form_hidden($data);
    ?>
        <table border="0" class="basic">
            <colgroup><col style="width: 20px">
            <col style="width: 40px">
            <col style="width: 300px">
            <col style="width: 200px">
            <col style="width: 150px">
            <col style="width: 60px">
            <col>
            </colgroup>
            <thead>
                <tr align="center">
                    <th scope="col"></th>
                    <th scope="col"><input type="checkbox" name="all_check_bt" /></th>
                    <th scope="col">그룹명</th>
                    <th scope="col">소속인원</th>
                    <th colspan="2" scope="col">그룹명 변경</th>
                    <th scope="col">추가</th>
                </tr>
            </thead>
            <tbody>
            <?php
                foreach ($result as $key => $row) {
            ?>
                <tr bgcolor="#FFFFFF" onMouseOver="this.style.backgroundColor='#F4F4F4';return true;" onMouseOut="this.style.backgroundColor=''; return true;">
                    <td></td>
                    <td><input type="checkbox" name="chk_seq_no" value="<?=$row['gno']?>" /></td>
                    <td><a href="/address/list/<?=$row['gno']?>"><?=$row['name']?></a></td>
                    <td><?=number_format($row['total_cnt'])?>명</td>
                    <td>
                        <input type="text" name="new_value_<?=$row['gno']?>" class="input_26" style="width:150px">
                    </td>
                    <td><div class="style_btn" style="width:60px"><a href="#" onclick="changeContents('<?=$row['gno']?>');">변경</a></div></td>
                    <td><div class="style_btn" style="margin-left:50px; margin-right:50px"><a href="#" onClick="addrwLayerOpen('<?=$row['name']?>','address_type','<?=(int)$row['total_cnt']?>','<?=$row['gno']?>'); return false;">연락처추가</a></div></td>
                </tr>
            <?php
                }
            ?>
            </tbody>
        </table>
</form>
    </div>
</div>


<?php /* ?>
<div style="text-align:center">PAGER</div>
<?php */ ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td height="30"></td></tr>
<tr><td><img src="/images/tip.gif" width="145" height="34" alt="" /></td></tr>
<tr><td height="1" bgcolor="#CCCCCC"></td></tr>
<tr><td class="help_txt">
        * 한 그룹에 최대 50,000개의 연락처를 등록하실 수 있습니다.<br>
        * 그룹명을 클릭하시면 개별주소 리스트를 확인하실 수 있습니다.
</td></tr>
</table>
        </td>
	</tr>
</table>

</div>
<!-- content end -->
<?php
include_once(VIEWPATH.'/templates/layer_bulk_addition.php');
?>

<script type="text/javascript">
$.fn.add_group_addr = function()
{
    $(this).click(function(){
        var csrf_sowkorea_name = $.trim($("#frmGroupList [name=csrf_sowkorea_name]").val());
        var group_name = $("input[name=group_name]").val();
        if (group_name == ''){
            alert("그룹명을 입력하세요.");
            return;
        }

        $.ajax({
            type: "POST",
            url: "/address/add_group",
            data: {
                "csrf_sowkorea_name" : csrf_sowkorea_name,
                "group_name" : group_name,
                "where" : "address"
            },
            dataType: "json",
            async: false,
            success : function(data, status, xhr) {
                if (data.result == 'success') {
                    $("input[name=csrf_sowkorea_name]").val(data.csrf_sowkorea_name);
                    // $('#mytable > tbody:last').append('<tr><td>안녕ㅋ 친구들ㅋ </td><td>' + time + '</td></tr>');
                    $(location).attr('href', '/address/group');
                } else {
                    $("input[name=csrf_sowkorea_name]").val(data.csrf_sowkorea_name);
                    //console.log('pass(1)');
                    // alert_tooltip(data.message);
                    //$(location).attr('href', './signup.php');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText);
            }
        });
    });
}
$.fn.send_group_addr = function()
{
    $(this).click(function(){
        var k = 0;
        var chk_nums = [];

        $("input[name=chk_seq_no]:checked").each(function() {
            chk_nums[k++] = $(this).val();
        });

        if (k == 0 || chk_nums.length == 0) {
            alert("메시지 전송할 목록을 선택하세요.");
            return;
        }
        $("input[name=chk_nums]").val(chk_nums);
        $("form#frmGroupList").attr("action", "/append/address");
        $("form#frmGroupList").submit();
    });
}
$(function(){
    $("#button-add-group").add_group_addr();
    $("#button-send-message").send_group_addr();
});

var deleteGroupAddress = function () {
    var k = 0;
    var chk_nums = [];

    $("input[name=chk_seq_no]:checked").each(function() {
        chk_nums[k++] = $(this).val();
    });

    if (k == 0 || chk_nums.length == 0) {
        alert("삭제할 목록을 선택하세요.");
        return;
    }
    $("input[name=chk_nums]").val(chk_nums);
    $("form#frmGroupList").attr("action", "/address/delete_group");
    $("form#frmGroupList").submit();
    // document.getElementById("frmGroupList").submit();
}
var changeContents = function (gno) {
    var new_value = $("input[name=new_value_"+gno+"]").val();
    if (new_value == '') {
        alert("새로운 이름을 입력하세요.");
        return;
    }
    $("input[name=gno]").val(gno);
    $("input[name=new_value]").val(new_value);
    $("form#frmGroupList").attr("action", "/address/group_info");
    $("form#frmGroupList").submit();
}
</script>