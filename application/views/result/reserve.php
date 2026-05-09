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
    <div class="body_title" style="position:relative;">예약발송

        <?php
            $g_placeholder = '제목으로 검색';
            $g_search_action = 'result/reserve';
            include_once(VIEWPATH.'/templates/div_search.php');
        ?>
    </div>
    <div style="margin-bottom:15px">
        <table width="100%" border="0" cellspacing="0" cellpadding="1">
            <tr>
            <td align="right"></td>
            <td width="80">
                <div class="style_btn" style="width:70px"><a href="#" onclick="deleteCampaign();">예약취소</a></div>
            </td>
            </tr>
        </table>
    </div>
    <div class="board">
    <?php
        $attributes = array(
            'name' => 'frmCampaign',
            'id' => 'frmCampaign'
        );
        echo form_open('/result/list', $attributes);
    ?>
    <?php
        $data = array(
            'chk_nums' => '',
        );
        echo form_hidden($data);
    ?>
        <table border="0" class="basic" style="font-size:14px">
            <colgroup>
            <col style="width: 10px">
            <col style="width: 30px">
            <col style="width: 110px">
            <col style="width: 50px">
            <col style="width: 130px">
            <col style="width: 280px">
            <col style="width: 60px">
            <col style="width: 60px">
            <col style="width: 60px">
            <col style="width: 60px">
            <col>
            </colgroup>
            <thead>
                <tr align="center">
                    <th scope="col"></th>
                    <th scope="col"><input type="checkbox" name="all_check_bt"></th>
                    <th scope="col">등록일시</th>
                    <th scope="col">구분</th>
                    <th scope="col">발신번호</th>
                    <th scope="col">내용</th>
                    <th scope="col">총건수</th>
                    <th scope="col">성공</th>
                    <th scope="col">실패</th>
                    <th scope="col">잔여</th>
                    <th scope="col">비고</th>
                </tr>
            </thead>
            <tbody>
        <?php
            foreach ($result as $key => $row) {
        ?>
                <tr bgcolor="#FFFFFF" onMouseOver="this.style.backgroundColor='#F4F4F4';return true;" onMouseOut="this.style.backgroundColor=''; return true;">
                    <td></td>
                    <td><input type="checkbox" name="chk_seq_no" value="<?=$row->procid?>" /></td>
                    <td><?=convert_display_today_time($row->reserve_time)?></td>
                    <td><?=convert_product_code($row->productcode)?></td>
                    <td><?=phone_format($row->callback)?></td>
                    <td style="text-align:left;"><input type="text" name="msg_contents" style="width:280px;" value="<?=$row->msg?>" readonly="readonly" /></td>
                    <td><?=number_format($row->total_units)?></td>
                    <td><?=number_format($row->success)?></td>
                    <td><?=number_format($row->fail)?></td>
                    <td><?=number_format($row->remain_units)?></td>
                    <td><div class="style_btn" style="margin-left:20px; margin-right:20px"><a href="/result/detail/<?=$row->procid?>">상세 보기</a></div></td>
                </tr>
        <?php
            }
        ?>
            </tbody>
        </table>
    </form>
    </div>
</div>

<div><?=$this->pagination->create_links();?></div>
<p>&nbsp;</p>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td height="30"></td></tr>
<tr><td><img src="../images/tip.gif" width="145" height="34" alt="" /></td></tr>
<tr><td height="1" bgcolor="#CCCCCC"></td></tr>
<tr><td class="help_txt">
        * 발송실패건 환불은 발송시도가 완료된 시점에 처리됩니다.<br>
        * 발송결과는 3개월간 보관되며, 기간이 경과한 데이터는 정기적으로 삭제됩니다.<br>
        * 발송결과는 성공이지만 문자를 수신하지 못하는 경우 <span><img src="/images/btn_spam.png" alt="" onClick="viewnoti()" style="cursor:pointer" /></span>
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
<script type="text/javascript">
function viewnoti() {
    var obj = document.getElementById("bak");
    obj.style.width = document.body.scrollWidth + 'px';
    obj.style.height = document.body.scrollHeight + 'px';
    obj.style.display = "block";
    document.all.bigimg.src='/images/spam_guide.png';
}
function hiddenBigImg_fn() {
    var obj = document.getElementById("bak");
    obj.style.display = "none";
}
var deleteCampaign = function () {
    var k = 0;
    var chk_nums = [];

    $("input[name=chk_seq_no]:checked").each(function() {
        chk_nums[k++] = $(this).val();
    });

    if (k == 0 || chk_nums.length == 0) {
        alert("목록을 선택하세요.");
        return;
    }
    if (!confirm("계속 진행하시겠습니까?")) {
        return;
    }

    $("input[name=chk_nums]").val(chk_nums);
    $("form#frmCampaign").attr("action", "/result/delete_reserve");
    $("form#frmCampaign").submit();
}
</script>