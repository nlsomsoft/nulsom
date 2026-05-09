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
    <div class="body_title" style="position:relative;">주소록 리스트
        <?php
            $g_search_action = 'address/search_list';
            include_once(VIEWPATH.'/templates/div_search.php');
        ?>
	</div>
    <div style="margin-bottom:15px">
        <table width="100%" border="0" cellspacing="0" cellpadding="1">
			<tr>
			<td align="left">
                <select id="sel_group_no" class="input_26" style="width:250px;height:26px; background-color:#F2FFD7">
                <option value="0">그룹을 선택해 주세요</option>
            <?php foreach ($group_result as $row) { ?>
                <option value="<?=$row['gno']?>" <?=($row['gno'] == $gno ? 'selected="selected"' : '')?>><?=$row['name']?> (<?=number_format($row['total_cnt'])?>명)</option>
            <?php } ?>
                </select>
            </td>
<?php /* ?>
			<td width="80"><div class="style_btn" style="width:70px"><a href="#" onclick="send_group_addr();">메시지전송</a></div></td>
<?php */ ?>
<?php /* ?>
			<td width="80"><div class="style_btn" style="width:70px"><a href="#" onClick="addrwLayerOpen(); return false;">연락처추가</a></div></td>
<?php */ ?>
			<td width="80"><div class="style_btn" style="width:70px"><a href="#" onclick="deleteListAddress();">주소삭제</a></div></td>
			<td width="80"><div class="style_btn" style="width:70px"><a href="#" onclick="downloadExcel();">다운로드</a></div></td>
			</tr>
		</table>
    </div>
    <div class="board">
    <?php
        $attributes = array(
            'name' => 'frmList',
            'id' => 'frmList'
        );
        echo form_open('/address/list', $attributes);
    ?>
    <?php
        $data = array(
            'chk_nums' => '',
            'gno_nums' => '',
            'new_mobile' => '',
            'new_name' => '',
            'sel_column' => '',
            'gno' => $gno,
            'ano' => '',
            'append_addr' => '2',
        );
        echo form_hidden($data);
    ?>
        <table border="0" class="basic">
            <colgroup>
            <col style="width: 20px">
            <col style="width: 40px">
            <col style="width: 150px">
            <col style="width: 150px">
            <col>
            <col style="width: 265px">
            <col style="width: 100px">
            </colgroup>
            <thead>
                <tr align="center">
                    <th scope="col"></th>
                    <th scope="col"><input type="checkbox" name="all_check_bt" /></th>
                    <th scope="col">전화번호</th>
                    <th scope="col">이름</th>
                    <th scope="col">그룹명</th>
                    <th colspan="2" scope="col">내용변경</th>
                </tr>
            </thead>
            <tbody>
        <?php foreach ($result as $row) { ?>
                <tr bgcolor="#FFFFFF" onMouseOver="this.style.backgroundColor='#F4F4F4';return true;" onMouseOut="this.style.backgroundColor=''; return true;">
                    <td></td>
                    <td><input type="checkbox" name="chk_seq_no" value="<?=$row->ano?>,<?=$row->gno?>" /></td>
                    <td><?=format_phone($row->mobile)?></td>
                    <td><?=$row->name?></td>
                    <td style="font-size:14px"><?=$row->group_name?></td>
                    <td align="right">
                    <select class="input_26" name="address_column" id="sel_column_<?=$row->ano?>" style="width:90px;">
                        <option value="mobile" selected>전화번호</option>
                        <option value="name">이름</option>
                    </select>
                    	<input type="text" name="new_value_<?=$row->ano?>" class="input_26" style="width:150px" maxlength="13"></td>
                    <td><div class="style_btn" style="width:60px"><a href="#" onclick="changeCoentents('<?=$row->ano?>');">변경</a></div></td>
                </tr>
        <?php } ?>
            </tbody>
        </table>
        </form>
    </div>
</div>
<div><?=$this->pagination->create_links();?></div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td height="30"></td></tr>
<tr><td><img src="/images/tip.gif" width="145" height="34" alt="" /></td></tr>
<tr><td height="1" bgcolor="#CCCCCC"></td></tr>
<tr><td class="help_txt">
        * 한 그룹에 최대 50,000개의 연락처를 등록하실 수 있습니다.<br>
        * 페이지당 100건의 리스트가 보여집니다.
</td></tr>
</table>
        </td>
	</tr>
</table>

</div>
<!-- content end -->
<script type="text/javascript">
var deleteListAddress = function () {
    var k = 0;
    var i = 0;
    var chk_nums = [];
    var all_gno_nums = [];
    var gno_nums = [];

    $("input[name=chk_seq_no]:checked").each(function() {
        var chk_val = $(this).val();
        var array = chk_val.split(",");
        chk_nums[k++] = array[0];
        all_gno_nums[i++] = array[1];
    });

    $.each(all_gno_nums,function(i,value){
        if(gno_nums.indexOf(value) == -1 ) gno_nums.push(value);
    });

    if (k == 0 || chk_nums.length == 0) {
        alert("삭제할 목록을 선택하세요.");
        return;
    }
    $("input[name=chk_nums]").val(chk_nums);
    $("input[name=gno_nums]").val(gno_nums);
    $("form#frmList").attr("action", "/address/delete_list");
    $("form#frmList").submit();
    // document.getElementById("frmGroupList").submit();
}

$(function(){
    $("#sel_group_no").on('change',function() {
        if ($(this).val() == '0') return;
        // $(location).attr('href', '/address/list/'+$(this).val());
        changeSearchList($(this).val());
    });
});

var changeCoentents = function (ano) {
    var sel_column = $("#sel_column_"+ano+" option:selected").val();

    var ipt_new_value = $("input[name=new_value_"+ano+"]").val().trim();
    if (ipt_new_value == '') {
        alert("변경할 내용을 입력하세요.");
        return;
    }
    if (sel_column == 'mobile') {
        // var regex = /[^0123456789-]/g;
        // var regex = /^01([0|1|6|7|8|9]?)-?([0-9]{3,4})-?([0-9]{4})$/;
        var regex = /^0([1|5]0?)-?([0-9]{3,4})-?([0-9]{4})$/;
        if (!regex.test(ipt_new_value)) {
            alert("전화번호 오류입니다.");
            $("input[name=new_value_"+ano+"]").focus();
            return;
        }
        var new_mobile = ipt_new_value.replace(/-/gi, "");
    } else {
        var new_name = ipt_new_value;
    }

    $("input[name=ano]").val(ano);
    $("input[name=sel_column]").val(sel_column);
    $("input[name=new_mobile]").val(new_mobile);
    $("input[name=new_name]").val(new_name);
    $("form#frmList").attr("action", "/address/list_info");
    $("form#frmList").submit();
}
var changeSearchList = function(gno) {
    if ($("input[name=sf]").val().trim() == '') {
        $(location).attr('href', '/address/list/'+gno);
    } else {
       $("input[name=sg]").val(gno);
       $("form#frmSearchList").submit();
    }
}
var downloadExcel = function() {
    var sg = $("input[name=sg]").val();
    var sv = $("input[name=sv]").val();
    if (sg == '') alert('잘못된 접근입니다.');
    $(location).attr('href', '/address/list_excel/'+sg+'/'+sv);
}
var send_group_addr = function() {
    var k = 0;
    var chk_nums = [];

    $("input[name=chk_seq_no]:checked").each(function() {
        var chk_val = $(this).val();
        var array = chk_val.split(",");
        chk_nums[k++] = array[0];
    });

    if (k == 0 || chk_nums.length == 0) {
        alert("메시지 전송할 목록을 선택하세요.");
        return;
    }
    $("input[name=chk_nums]").val(chk_nums);
    $("form#frmList").attr("action", "/append/address");
    $("form#frmList").submit();
}
</script>
