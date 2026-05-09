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
        <h1>월별 결제통계</h1>
<div class="local_ov01 local_ov">전체목록 <?=number_format($total_rows)?> 건</div>

<?php
    $attributes = array(
        'name' => 'fsearch',
        'id' => 'fsearch',
        'method' => 'get',
        'class' => 'local_sch01 local_sch',
        'onsubmit' => 'return searchList();'
    );
    echo form_open('admstats/bank_mm', $attributes);

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
    if ((int)$this->session->userdata('level') > 5) {
        $options = array();
        $options[''] = '전체 (상점)';
        if (is_array($store_list)) {
            foreach ($store_list as $row) {
                $options[$row->storeno] = $row->storename;
            }
        }
        $js = 'class="frm_input" style="width:90px" onchange="getGroupList(this.value);"';
        echo form_dropdown('ipt_storeno', $options, $storeno, $js);
    }
    if ((int)$this->session->userdata('level') > 3) {
        $options = array();
        $options[''] = '전체 (그룹)';
        if (is_array($group_list)) {
            $options['9999'] = '그룹없음';
            foreach ($group_list as $row) {
                $options[$row->groupno] = $row->groupid;
            }
        }
        $js = 'class="frm_input" style="width:90px"';
        echo form_dropdown('ipt_groupno', $options, $groupno, $js);
    }
    $data = array(
        'name'  => 'ipt_userid',
        'id'    => 'ipt_userid',
        'class' => 'frm_input',
        'minlength' => '6',
        'maxlength' => '30',
        'size' => '15',
        'value' => $userid,
        'placeholder' => '아이디'
    );
    echo form_input($data);
    $options = array();
    $options[''] = '전체 (결제)';
    if (is_array($billing_list)) {
        foreach ($billing_list as $row) {
            $options[$row->mode] = $row->exp;
        }
    }
    $js = 'class="frm_input"';
    echo form_dropdown('ipt_billing_mode', $options, $billing_mode, $js);
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
    <table>
    <thead>
    <tr>
        <th scope="col">날짜</th>
        <th scope="col">건수</th>
        <th scope="col">금액</th>
    </tr>
    </thead>
    <tbody>
<?php
    $i = 0;
    foreach ($result as $row) {
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td class="td_num" style="letter-spacing: 0.1em;"><?=sprintf('%04d-%02d',$row->yyyy,$row->mm)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($row->cnt)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($row->amount)?></td>
    </tr>
<?php
        $i ++;
    }
?>
    <tr class="bg1">
        <td class="td_num">총 합계</td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($total_result->total_cnt)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($total_result->total_amount)?></td>
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