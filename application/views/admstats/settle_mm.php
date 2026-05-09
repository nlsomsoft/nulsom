<!-- datepicker -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/css/handsontable.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="/js/datepicker-ym.js"></script>
<!-- /datepicker -->

<body class="responsive is-mobile">
<div id="to_content"><a href="#container">본문 바로가기</a></div>

<?php
    $g_menu_flag = 'stats';
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
        <h1>매출관리</h1>
<div class="local_ov01 local_ov">전체목록 <?=number_format($total_rows)?> 건</div>

<?php
    $attributes = array(
        'name' => 'fsearch',
        'id' => 'fsearch',
        'method' => 'get',
        'class' => 'local_sch01 local_sch',
        'onsubmit' => 'return searchList();'
    );
    echo form_open('admstats/settle_mm', $attributes);

    $data = array(
        'name'  => 'ipt_date_from',
        'id'    => 'ipt_date_from',
        'class' => 'frm_input',
        'minlength' => '10',
        'maxlength' => '10',
        'size' => '15',
        'readonly' => '',
        'required' => '',
        'value' => $date_from,
        'placeholder' => ''
    );
    echo form_input($data);
    echo ' ~ ';
    $data = array(
        'name'  => 'ipt_date_to',
        'id'    => 'ipt_date_to',
        'class' => 'frm_input',
        'minlength' => '10',
        'maxlength' => '10',
        'size' => '15',
        'readonly' => '',
        'required' => '',
        'value' => $date_to,
        'placeholder' => ''
    );
    echo form_input($data);
    echo '&nbsp;&nbsp;&nbsp;';
    if ((int)$this->session->userdata('level') > 3) {
        $options = array();
        $options[''] = '전체';
        if (is_array($store_list)) {
            foreach ($store_list as $row) {
                $options[$row->storeno] = $row->storename;
            }
        }
        $js = 'class="frm_input" style="width:90px" onchange="getGroupList(this.value);"';
        echo form_dropdown('ipt_storeno', $options, $storeno, $js);
    }
?>
<input type="submit" class="btn_submit" value="조회">

<script type="text/javascript">
var searchList = function() {
    if ($("input[name=ipt_date_from]").val().trim() == '') return false;
    if ($("input[name=ipt_date_to]").val().trim() == '') return false;
    return true;
}
//<![CDATA[
$(function(){
    $("#ipt_date_from").datepicker({
        showOn: "button",
        buttonImage: "/images/ico_calendar.gif",
        buttonImageOnly: false,
        buttonText: "날짜를 선택해 주세요."
    });
});

$(function(){
    $("#ipt_date_to").datepicker({
        showOn: "button",
        buttonImage: "/images/ico_calendar.gif",
        buttonImageOnly: false,
        buttonText: "날짜를 선택해 주세요."
    });
});
//]]>
</script>
</form>

<?php
    $attributes = array(
        'name' => 'frmAdmstats',
        'id' => 'frmAdmstats'
    );
    echo form_open('/admstats/settle_mm', $attributes);
?>
<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    <tr>
        <th scope="col">날짜</th>
        <th scope="col">상점</th>
        <th scope="col">그룹</th>
        <th scope="col">단문건수</th>
        <th scope="col">단문(원가)</th>
        <th scope="col">단문(실사용)</th>
        <th scope="col">장문건수</th>
        <th scope="col">장문(원가)</th>
        <th scope="col">장문(실사용)</th>
        <th scope="col">포토건수</th>
        <th scope="col">포토(원가)</th>
        <th scope="col">포토(실사용)</th>
        <th scope="col">카카오건수</th>
        <th scope="col">카카오(원가)</th>
        <th scope="col">카카오(실사용)</th>
        <th scope="col">비고</th>
    </tr>
    </thead>
    <tbody>
<?php
    $sms_cnt = 0;
    $sms_basic_amount = 0;
    $sms_real_amount = 0;
    $lms_cnt = 0;
    $lms_basic_amount = 0;
    $lms_real_amount = 0;
    $mms_cnt = 0;
    $mms_basic_amount = 0;
    $mms_real_amount = 0;
    $kko_cnt = 0;
    $kko_basic_amount = 0;
    $kko_real_amount = 0;
    $total_basic_amount = 0;
    $total_real_amount = 0;

    $i = 0;
    foreach ($result as $row) {
        $sms_cnt += $row->sms_cnt;
        $sms_basic_amount += $row->sms_basic_amount;
        $sms_real_amount += $row->sms_real_amount;
        $lms_cnt += $row->lms_cnt;
        $lms_basic_amount += $row->lms_basic_amount;
        $lms_real_amount += $row->lms_real_amount;
        $mms_cnt += $row->mms_cnt;
        $mms_basic_amount += $row->mms_basic_amount;
        $mms_real_amount += $row->mms_real_amount;
        $kko_cnt += $row->kko_cnt;
        $kko_basic_amount += $row->kko_basic_amount;
        $kko_real_amount += $row->kko_real_amount;
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td class="td_num"><?=sprintf('%04d-%02d',$row->yyyy,$row->mm)?></td>
        <td class="td_num"><?=$row->storename?></td>
        <td class="td_num"><?=$row->groupid?></td>
        <td class="td_num"><?=number_format($row->sms_cnt)?></td>
        <td class="td_num"><?=number_format($row->sms_basic_amount,2)?></td>
        <td class="td_num"><?=number_format($row->sms_real_amount,2)?></td>
        <td class="td_num"><?=number_format($row->lms_cnt)?></td>
        <td class="td_num"><?=number_format($row->lms_basic_amount,2)?></td>
        <td class="td_num"><?=number_format($row->lms_real_amount,2)?></td>
        <td class="td_num"><?=number_format($row->mms_cnt)?></td>
        <td class="td_num"><?=number_format($row->mms_basic_amount,2)?></td>
        <td class="td_num"><?=number_format($row->mms_real_amount,2)?></td>
        <td class="td_num"><?=number_format($row->kko_cnt)?></td>
        <td class="td_num"><?=number_format($row->kko_basic_amount,2)?></td>
        <td class="td_num"><?=number_format($row->kko_real_amount,2)?></td>
        <td class="td_num"><?=($row->fix_flag == '1' ? '확정' : '미확정')?></td>
    </tr>
<?php
        $i ++;
    }
    $total_basic_amount = $sms_basic_amount + $lms_basic_amount + $mms_basic_amount + $kko_basic_amount;
    $total_real_amount = $sms_real_amount + $lms_real_amount + $mms_real_amount + $kko_real_amount;
?>
    <tr class="bg1">
        <td class="td_num" colspan="3">합계</td>
        <td class="td_num"><?=number_format($sms_cnt)?></td>
        <td class="td_num"><?=number_format($sms_basic_amount,2)?></td>
        <td class="td_num"><?=number_format($sms_real_amount,2)?></td>
        <td class="td_num"><?=number_format($lms_cnt)?></td>
        <td class="td_num"><?=number_format($lms_basic_amount,2)?></td>
        <td class="td_num"><?=number_format($lms_real_amount,2)?></td>
        <td class="td_num"><?=number_format($mms_cnt)?></td>
        <td class="td_num"><?=number_format($mms_basic_amount,2)?></td>
        <td class="td_num"><?=number_format($mms_real_amount,2)?></td>
        <td class="td_num"><?=number_format($kko_cnt)?></td>
        <td class="td_num"><?=number_format($kko_basic_amount,2)?></td>
        <td class="td_num"><?=number_format($kko_real_amount,2)?></td>
        <td class="td_num">&nbsp;</td>
    </tr>
    <tr class="bg1">
        <td class="td_num" colspan="3">총 원가 합계</td>
        <td class="td_num" colspan="3"><?=number_format($total_basic_amount,2)?></td>
        <td colspan="10" style="padding-left:50px;">단문(원가) + 장문(원가) + 포토(원가) + 카카오(원가)</td>
    </tr>
    <tr class="bg1">
        <td class="td_num" colspan="3">총 실사용 합계</td>
        <td class="td_num" colspan="3"><?=number_format($total_real_amount,2)?></td>
        <td colspan="10" style="padding-left:50px;">단문(실사용) + 장문(실사용) + 포토(실사용) + 카카오(실사용)</
    </tr>
            </tbody>
    </table>
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

<?php if ((int)$this->session->userdata('level') > 3) { ?>
<script type="text/javascript">
var getGroupList = function (storeno) {
    if (!storeno) {
        $("input[name=ipt_groupno]").find("option").remove().end().append("<option value=''>전체</option>");
        return;
    }

    var csrf_sowkorea_name = $.trim($("input[name=csrf_sowkorea_name]").val());
    $.ajax({
        type: "POST",
        url: "/admstats/get_group",
        data: {
            "csrf_sowkorea_name" : csrf_sowkorea_name,
            "storeno" : storeno,
            "where" : "admstats"
        },
        dataType: "json",
        async: false,
        success : function(data, status, xhr) {
            $("input[name=csrf_sowkorea_name]").val(data.csrf_sowkorea_name);

            //SELECT BOX 초기화
            $("select[name=ipt_groupno]").find("option").remove().end().append("<option value=''>전체</option>");

            var added_option = '';
            //배열 개수 만큼 option 추가
            $.each(data.group, function(key,val){
                added_option = val.split('|');
                $("select[name=ipt_groupno]").append("<option value='"+added_option[0]+"'>"+added_option[1]+"</option>");
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.responseText);
        }
    });
}
</script>
<?php } ?>