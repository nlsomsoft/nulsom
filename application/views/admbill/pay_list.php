<!-- datepicker -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/css/handsontable.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="/js/datepicker-ko.js"></script>
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
        <h1>결제관리</h1>
<div class="local_ov01 local_ov">전체목록 <?=number_format($total_rows)?> 건</div>

<?php
    $attributes = array(
        'name' => 'fsearch',
        'id' => 'fsearch',
        'method' => 'get',
        'class' => 'local_sch01 local_sch',
        'onsubmit' => 'return searchList();'
    );
    echo form_open('admbill/pay_list', $attributes);

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

        $options = array();
        $options[''] = '전체';
        if (is_array($group_list)) {
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
    $options[''] = '전체';
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
    echo form_open('/admbill/pay_list', $attributes);
?>
<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    <tr>
        <th scope="col">아이디</th>
<?php if (GROUP_USE_YN == 'Y') { ?>
    <?php if (0 && (int)$this->session->userdata('level') == 9) { ?>
        <th scope="col">상점</th>
    <?php } ?>
    <?php if ((int)$this->session->userdata('level') >= 5) { ?>
        <th scope="col">그룹</th>
    <?php } ?>
<?php } ?>
        <th scope="col">결제</th>
        <th scope="col">날짜</th>
        <th scope="col">금액</th>
        <th scope="col">비고</th>
    </tr>
    </thead>
    <tbody>
<?php
    $i = 0;
    foreach ($result as $row) {
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td class="td_date"><a href="/admuser/detail/<?=$row->userno?>"><?=$row->userid?></a></td>
<?php if (GROUP_USE_YN == 'Y') { ?>
    <?php if (0 && (int)$this->session->userdata('level') == 9) { ?>
        <td class="td_date"><?=$row->storename?></td>
    <?php } ?>
    <?php if ((int)$this->session->userdata('level') >= 5) { ?>
        <td class="td_date"><?=$row->groupid?></td>
    <?php } ?>
<?php } ?>
        <td class="td_date"><?=convert_billing_mode($row->mode)?></td>
        <td class="td_date"><?=mydate_format('y-m-d H:i', $row->reg_time)?></td>
        <td class="td_date"><?=number_format($row->amount)?> 원</td>
        <td class="td_date"><?=$row->memo?></td>
    </tr>
<?php
        $i ++;
    }
?>
<?php
    if (GROUP_USE_YN == 'Y' && (int)$this->session->userdata('level') >= 5) {
        $colspan1 = '3';
    } else {
        $colspan1 = '2';
    }
?>
    <tr class="bg1">
        <td class="td_date" colspan="<?=$colspan1?>">총 합계</td>
        <td class="td_date" colspan="3"><?=number_format($total_result->total_amount,2)?> 원</td>
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