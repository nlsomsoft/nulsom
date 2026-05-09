<body class="responsive is-mobile">
<div id="to_content"><a href="#container">본문 바로가기</a></div>

<?php
    $g_menu_flag = 'send';
    include_once(VIEWPATH.'/templates/adm_menu.php');
?>

<div id="wrapper">
    <div id="container">
        <div id="text_size">
            <!-- font_resize('엘리먼트id', '제거할 class', '추가할 class'); -->
            <button onclick="font_resize('container', 'ts_up ts_up2', '');"><img src="/images/adm/ts01.gif" alt="기본"></button>
            <button onclick="font_resize('container', 'ts_up ts_up2', 'ts_up');"><img src="/images/adm/ts02.gif" alt="크게"></button>
            <button onclick="font_resize('container', 'ts_up ts_up2', 'ts_up2');"><img src="/images/adm/ts03.gif" alt="더크게"></button>
        </div>
        <h1>예약관리</h1>
<div class="local_ov01 local_ov">전체목록 <?=number_format($total_rows)?> 건</div>

<?php
    $attributes = array(
        'name' => 'fsearch',
        'id' => 'fsearch',
        'method' => 'get',
        'class' => 'local_sch01 local_sch',
        'onsubmit' => 'return searchList();'
    );
    echo form_open('admsend/reserve', $attributes);
?>
<label for="sfl" class="sound_only">검색대상</label>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?=$stx?>" id="stx" style="width:200px;" required class="required frm_input" placeholder=" 아이디 검색" />
<input type="submit" class="btn_submit" value="검색">
<script type="text/javascript">
var searchList = function() {
    if ($("input[name=stx]").val().trim() == '') return false;
    return true;
}
</script>
</form>

<?php /* ?>
<div class="btn_add01 btn_add">
    <button id="myBtn">발신번호등록하기</button>
</div>
<?php */ ?>

<?php
$attributes = array(
    'name' => 'frmadmin',
    'id' => 'frmadmin',
    'onsubmit' => 'return fmemberlist_submit(this);'
);
echo form_open('admsend/change', $attributes);
?>
<div class="btn_list01 btn_list">
<?php if ((int)$this->session->userdata('level') >= 5) { ?>
    <input type="submit" name="act_button" value="예약취소" onclick="document.pressed=this.value">
<?php } ?>
<?php if (0 && (int)$this->session->userdata('level') >= 5) { ?>
    <input type="submit" name="act_button" value="채널변경" onclick="document.pressed=this.value">
<?php } ?>
</div>

<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    <tr>
        <th scope="col"><input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)"></th>
        <th scope="col">PID</th>
        <th scope="col">아이디</th>
<?php if (0 && (int)$this->session->userdata('level') == 9) { ?>
        <th scope="col">상점</th>
<?php } ?>
        <th scope="col">상품</th>
        <th scope="col">채널</th>
        <th scope="col">분할</th>
        <th scope="col">발송시간</th>
        <th scope="col">발신번호</th>
        <th scope="col">IP</th>
    <?php if ((int)$this->session->userdata('level') == 5) { ?>
        <th scope="col" width="300px">내용</th>
    <?php } ?>
        <th scope="col">선차감금액</th>
        <th scope="col">발송건수</th>
        <th scope="col">상태</th>
    </tr>
    </thead>
    <tbody>
<?php
    // $sms_options = array();
    // $lms_options = array();
    // $mms_options = array();
    // $kakao_options = array();
    // foreach ($channel as $key => $val) {
    //     foreach ($val as $channel_val => $channel_exp) {
    //         if ($key == 'sms') $sms_options[$channel_val] = $channel_exp;
    //         else if ($key == 'lms') $lms_options[$channel_val] = $channel_exp;
    //         else if ($key == 'mms') $mms_options[$channel_val] = $channel_exp;
    //         else if ($key == 'kakao') $kakao_options[$channel_val] = $channel_exp;
    //     }
    // }
    // $js = 'class="frm_input"';
    $i = 0;
    foreach ($result as $row) {
        if ($row->productcode == 'SMS1') {
            $send_contents = $row->msg;
        } else {
            $send_contents = character_limiter($row->msg, 90);
            if (strlen($send_contents) > 250) $send_contents = substr($row->msg, 0, 90);
        }

        $div_info = '-';
        if ($row->div_count && $row->div_minute) {
            $div_info = number_format($row->div_count).'건/'.$row->div_minute.'분';
        }
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td class="td_num">
    <?php if ($row->deleteflag != 'Y') { ?>
            <input type="checkbox" name="chk[]" value="<?=$row->procid?>" id="chk_<?=$row->procid?>">
    <?php } ?>
        </td>
        <td class="td_num"><?=$row->procid?></td>
        <td class="td_date"><a href="/admuser/detail/<?=$row->userno?>"><?=$row->userid?></a></td>
<?php if (0 && (int)$this->session->userdata('level') == 9) { ?>
        <td class="td_date"><?=$row->storename?></td>
<?php } ?>
        <td class="td_date"><?=convert_product_code($row->productcode)?></td>
        <td class="td_date"><?=convert_campaign_priority($row->priority)?></td>
        <td class="td_date"><?=$div_info?></td>
        <td class="td_date"><?=convert_display_today_time($row->reserve_time)?></td>
        <td class="td_date"><?=phone_format($row->callback)?></td>
        <td class="td_date"><?=$row->ip?></td>
    <?php if ((int)$this->session->userdata('level') == 5) { ?>
        <td class="td_date" style="text-align:left;letter-spacing:-0.07em;">
        <?php
            if ($row->file_path_1) {
                $image_path = str_replace(FCPATH, '', $row->file_path_1);
        ?>
            <a href="<?=('/'.$image_path)?>" target="_blank"><img src="<?=('/'.$image_path)?>" style="width:auto; max-height:30px;" /></a>
        <?php
            }
        ?>
        <?php if ($row->productcode == 'LMS1') { ?>
            <span id="org_msg_<?=$row->procid?>" style="display:none;"><?=$row->msg?></span>
        <?php } ?>
            <span id="disp_msg_<?=$row->procid?>" style="display:block;"><?=$send_contents?><?php if ($row->productcode == 'LMS1') { ?><a href='javascript:void(0);' onclick="showHiddenContents('<?=$row->procid?>');"><span style="color:#dc143c;">...[ 더보기 ]</span></a><?php } ?></span>
        </td>
    <?php } ?>
        <td class="td_date"><?=number_format($row->amount)?></td>
        <td class="td_date"><?=number_format($row->total_units)?></td>
        <td class="td_num"><?=($row->deleteflag == 'Y' ? '삭제' : '정상')?></td>
    </tr>
<?php
        $i ++;
    }
?>

            </tbody>
    </table>
</div>
<div class="btn_list01 btn_list">
<?php if ((int)$this->session->userdata('level') >= 5) { ?>
    <input type="submit" name="act_button" value="예약취소" onclick="document.pressed=this.value">
<?php } ?>
<?php if (0 && (int)$this->session->userdata('level') >= 5) { ?>
    <input type="submit" name="act_button" value="채널변경" onclick="document.pressed=this.value">
<?php } ?>
</div>
<div><?=$this->pagination->create_links();?></div>

</form>
        <noscript>
            <p>
                귀하께서 사용하시는 브라우저는 현재 <strong>자바스크립트를 사용하지 않음</strong>으로 설정되어 있습니다.<br>
                <strong>자바스크립트를 사용하지 않음</strong>으로 설정하신 경우는 수정이나 삭제시 별도의 경고창이 나오지 않으므로 이점 주의하시기 바랍니다.
            </p>
        </noscript>

    </div>
</div>

<!-- <p>실행시간 : 0.00087904930114746 -->

</body>

<script type="text/javascript">
var showHiddenContents = function(arv) {
    if (arv == '') return;
    $("#org_msg_"+arv).css("display", "block");
    $("#disp_msg_"+arv).css("display", "none");
    return;
}
var fmemberlist_submit = function () {
    if (!is_checked("chk[]")) {
        alert("수정할 목록을 선택하세요.");
        return false;
    }
    return true;
}
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})
</script>