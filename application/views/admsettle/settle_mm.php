<!-- datepicker -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="/js/datepicker-ym.js"></script>
<!-- /datepicker -->

<body class="responsive is-mobile">
<div id="to_content"><a href="#container">본문 바로가기</a></div>

<?php
    $g_menu_flag = 'bill';
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
        <h1>결산 (매출)</h1>
<div class="local_ov01 local_ov">전체목록 <?=number_format($total_rows)?> 건</div>

<?php
    $attributes = array(
        'name' => 'fsearch',
        'id' => 'fsearch',
        'method' => 'get',
        'class' => 'local_sch01 local_sch',
        'onsubmit' => 'return searchList();'
    );
    echo form_open('admsettle/settle_mm', $attributes);

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
    echo form_open('/admstats/send_mm', $attributes);
?>
<div class="tbl_wrap tbl_head01">
    <div><b>매입</b></div>
    <table>
    <thead>
    <tr>
        <th scope="col">상품</th>
        <th scope="col">채널원가</th>
        <th scope="col">건수</th>
        <th scope="col">금액</th>
    </tr>
    </thead>
    <tbody>
<?php
    $i = 0;
    $total_amount1 = 0;
    foreach ($purchase_array as $row) {
        $product = '';
        if ($row->productcode == 'SMS1') $product = '단문';
        else if ($row->productcode == 'LMS1') $product = '장문';
        else if ($row->productcode == 'MMS1') $product = '포토';
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td class="td_num" style="letter-spacing: 0.1em;"><?=$product?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($row->channel_price,2)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($row->success)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($row->amount,2)?></td>
    </tr>
<?php
        $total_amount1 += $row->amount;
        $i ++;
    }
?>
    <tr class="bg1">
        <td class="td_num" colspan="3"><b>총 합계</b></td>
        <td class="td_num" style="letter-spacing: 0.1em; font-weight:bold"><?=number_format($total_amount1,2)?></td>
    </tr>
            </tbody>
    </table>
    <br />
    <div><b>매출</b></div>
    <table>
    <thead>
    <tr>
        <th scope="col">상품</th>
        <th scope="col">유저단가</th>
        <th scope="col">건수</th>
        <th scope="col">금액</th>
    </tr>
    </thead>
    <tbody>
<?php
    $i = 0;
    $total_amount2 = 0;
    foreach ($sales_array as $row) {
        $product = '';
        if ($row->productcode == 'SMS1') $product = '단문';
        else if ($row->productcode == 'LMS1') $product = '장문';
        else if ($row->productcode == 'MMS1') $product = '포토';
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td class="td_num" style="letter-spacing: 0.1em;"><?=$product?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($row->price,2)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($row->success)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($row->amount,2)?></td>
    </tr>
<?php
        $total_amount2 += $row->amount;
        $i ++;
    }
?>
    <tr class="bg1">
        <td class="td_num" colspan="3"><b>총 합계</b></td>
        <td class="td_num" style="letter-spacing: 0.1em; font-weight:bold"><?=number_format($total_amount2,2)?></td>
    </tr>
            </tbody>
    </table>
    <br />
    <div><b>회선재판매</b></div>
    <table>
    <thead>
    <tr>
        <th scope="col">업체</th>
        <th scope="col">단문건수</th>
        <th scope="col">장문건수</th>
        <th scope="col">매입(단문+장문)</th>
        <th scope="col">매츨(단문+장문)</th>
    </tr>
    </thead>
    <tbody>
<?php
    $i = 0;

    $total_sms_cnt = 0;
    $total_lms_cnt = 0;
    $total_s_amount = 0;
    $total_p_amount = 0;


    $captain_per_sms_price = (float)(13 * 1.1);
    $captain_per_lms_price = (float)(32 * 1.1);
    $green_per_sms_price = (float)(12 * 1.1);
    $green_per_lms_price = (float)(32 * 1.1);

    $captain_org_sms_price = (float)(10.23);
    $captain_org_lms_price = (float)(30.80);
    $green_org_sms_price = (float)(10.23);
    $green_org_lms_price = (float)(30.80);

    foreach ($rental_array as $row) {
/*
        $product = '';
        if ($row->priority < 500) {
            $product = '단문';
            $s_amount = (float)($row->cnt * 14.3);
            $p_amount = (float)($row->cnt * 10.23);
        }
        else if ($row->priority >= 500 && $row->priority < 900) {
            $product = '장문';
            $s_amount = (float)($row->cnt * 35.2);
            $p_amount = (float)($row->cnt * 30.80);
        }
*/
        if ($row->company == 'captain') {
            $s_sms_amount = (float)($row->sms_cnt * $captain_org_sms_price);
            $s_lms_amount = (float)($row->lms_cnt * $captain_org_lms_price);
            $p_sms_amount = (float)($row->sms_cnt * $captain_per_sms_price);
            $p_lms_amount = (float)($row->lms_cnt * $captain_per_lms_price);
        } else {
            $s_sms_amount = (float)($row->sms_cnt * $green_org_sms_price);
            $s_lms_amount = (float)($row->lms_cnt * $green_org_lms_price);
            $p_sms_amount = (float)($row->sms_cnt * $green_per_sms_price);
            $p_lms_amount = (float)($row->lms_cnt * $green_per_lms_price);
        }
        $add_s_amount = (float)($s_sms_amount + $s_lms_amount);
        $add_p_amount = (float)($p_sms_amount + $p_lms_amount);

        $total_sms_cnt += $row->sms_cnt;
        $total_lms_cnt += $row->lms_cnt;
        $total_s_amount += $add_s_amount;
        $total_p_amount += $add_p_amount;
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td class="td_num" width="10%" style="letter-spacing: 0.1em;"><?=$row->company?></td>
        <td class="td_num" width="15%" style="letter-spacing: 0.1em;"><?=number_format($row->sms_cnt)?></td>
        <td class="td_num" width="15%" style="letter-spacing: 0.1em;"><?=number_format($row->lms_cnt)?></td>
        <td class="td_num" width="30%" style="letter-spacing: 0.1em;">
            <?=number_format($s_sms_amount,2)?> + <?=number_format($s_lms_amount,2)?> = <b><?=number_format($add_s_amount,2)?></b>
        </td>
        <td class="td_num" width="30%" style="letter-spacing: 0.1em;">
            <?=number_format($p_sms_amount,2)?> + <?=number_format($p_lms_amount,2)?> = <b><?=number_format($add_p_amount,2)?></b>
        </td>
    </tr>
<?php
        $s_total_amount += $s_amount;
        $p_total_amount += $p_amount;
        $i ++;
    }
?>
    <tr class="bg1">
        <td class="td_num"><b>총 합계</b></td>
        <td class="td_num" style="letter-spacing: 0.1em; font-weight:bold"><?=number_format($total_sms_cnt)?></td>
        <td class="td_num" style="letter-spacing: 0.1em; font-weight:bold"><?=number_format($total_lms_cnt)?></td>

        <td class="td_num" style="letter-spacing: 0.1em; font-weight:bold"><?=number_format($total_s_amount)?></td>
        <td class="td_num" style="letter-spacing: 0.1em; font-weight:bold"><?=number_format($total_p_amount)?></td>
    </tr>
            </tbody>
    </table>
    <br />
    <br />
    <div><b>총 결산 (VAT 포함)</b></div>
    <table>
    <thead>
    <tr>
        <th scope="col"></th>
        <th scope="col">합계금액</th>
        <th scope="col">레드빈8</th>
        <th scope="col">쏘우.늘솜</th>
    </tr>
    </thead>
    <tbody>

<?php
    $b_amount = (int)$total_amount2 - (int)$total_amount1;
    $c_amount = (int)($b_amount / 2);
?>

    <tr class="bg0">
        <td class="td_num" style="letter-spacing: 0.1em;border: 1px solid #d1dee2;background: #e5ecef;">매출.입</td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($b_amount)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($c_amount)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($c_amount)?></td>
    </tr>
<?php
    $d_amount = (int)$total_p_amount - (int)$total_s_amount;
    $e_amount = (int)($d_amount / 3);
    $f_amount = (int)(($d_amount * 2)/ 3);
?>
    <tr class="bg1">
        <td class="td_num" style="letter-spacing: 0.1em;border: 1px solid #d1dee2;background: #e5ecef;">회선재판매</td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($d_amount)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($e_amount)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($f_amount)?></td>
    </tr>
    <tr class="bg1">
        <td class="td_num" style="letter-spacing: 0.1em;border: 1px solid #d1dee2;background: #e5ecef;">최종 수익금액</td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format(($b_amount + $d_amount))?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($c_amount + $e_amount)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($c_amount + $f_amount)?></td>
    </tr>
    </tbody>
    </table>

</div>

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
        $("input[name=ipt_groupno]").find("option").remove().end().append("<option value=''>전체 (그룹)</option>");
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
            $("select[name=ipt_groupno]").find("option").remove().end().append("<option value=''>전체 (그룹)</option>");

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

<script type="text/javascript">
$("#sel_company").change( function(){
    // alert(this.val());
    var com_val = $("#sel_company").val();
    window.location.replace('/admsettle/settle_mm/'+com_val);
});
</script>