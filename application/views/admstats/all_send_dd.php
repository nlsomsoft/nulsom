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
        <h1>일별 전송통계</h1>
<div class="local_ov01 local_ov">전체목록 <?=number_format($total_rows)?> 건</div>

<?php
    $attributes = array(
        'name' => 'fsearch',
        'id' => 'fsearch',
        'method' => 'get',
        'class' => 'local_sch01 local_sch',
        'onsubmit' => 'return searchList();'
    );
    echo form_open('admstats/all_send_dd', $attributes);

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
    echo form_open('/admstats/all_send_dd', $attributes);
?>
<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    <tr>
        <th scope="col">날짜</th>
        <th scope="col">업체</th>
        <th scope="col">전송시도</th>
        <th scope="col">전송성공</th>
        <th scope="col">전송실패</th>
        <th scope="col">잔여건수</th>
        <th scope="col">진행률</th>
    </tr>
    </thead>
    <tbody>
<?php
    $i = 0;
    foreach ($result as $row) {
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td class="td_num" style="letter-spacing: 0.1em;"><?=$row->yyyymmdd?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=$row->company?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($row->total)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($row->succ)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($row->fail)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($row->remain)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=(int)((($row->succ + $row->fail)/$row->total)*100).'%'?></td>
    </tr>
<?php
        $i ++;
    }
?>
    <tr class="bg1">
        <td class="td_num" colspan="2">총 합계</td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($total_result->total)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($total_result->succ)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($total_result->fail)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($total_result->remain)?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=(int)((($total_result->succ + $total_result->fail)/$total_result->total)*100).'%'?></td>
    </tr>
            </tbody>
    </table>
</div>
<div><?=$this->pagination->create_links();?></div>


<br />
<br />
<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    <tr>
        <th scope="col">업체</th>
        <th scope="col">결과 컬럼 수</th>
    </tr>
    </thead>
    <tbody>
<?php
// error_log(print_r($result1,1),0);
    $i = 0;
    foreach ($result1 as $row) {
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td class="td_num" style="letter-spacing: 0.1em;"><?=$row->company?></td>
        <td class="td_num" style="letter-spacing: 0.1em;"><?=number_format($row->col_cnt)?></td>
    </tr>
<?php
        $i ++;
    }
?>
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
