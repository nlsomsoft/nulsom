<!DOCTYPE html>
<html>
<head>
<title>SOWKOREA</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
    include_once(VIEWPATH.'/templates/head.php');
?>
</head>

<?php
$data = array(
    'procid' => $procid,
    'table' => $table,
);
echo form_hidden($data);
?>

<div class="content_wrap" style="margin-top:30px; margin-bottom:30px">
    <div class="body_title" style="position:relative; text-align:left">발송상세 리스트
        <?php
            $g_placeholder = '전화번호로 검색';
            $g_search_action = 'result/detail_list';
            include_once(VIEWPATH.'/templates/div_search.php');
        ?>
    </div>
    <div style="margin-bottom:15px">
        <table width="100%" border="0" cellspacing="0" cellpadding="1">
            <tbody><tr>
            <td align="left">&nbsp;</td>
            <td width="155"><div class="style_btn" style="width:150px"><a href="#" onclick="downloadExcel('1000');">발송결과 성공 다운로드</a></div></td>
            <td width="155"><div class="style_btn" style="width:150px"><a href="#" onclick="downloadExcel('2000');">발송결과 실패 다운로드</a></div></td>
            </tr>
        </tbody></table>
    </div>
    <div class="board">
        <table border="0" class="basic" style="font-size:14px">
            <colgroup>
            <col style="width: 15%">
            <col style="width: 15%">
            <col style="width: 15%">
            <col style="width: 15%">
            <col style="width: 25%">
            <col style="width: 15%">
            </colgroup>
            <thead>
                <tr align="center">
                    <th scope="col">전화번호</th>
                    <th scope="col">수신자명</th>
                    <th scope="col">통신사</th>
                    <th scope="col">발송결과</th>
                    <th scope="col">발송시간</th>
                    <th scope="col">비고</th>
                </tr>
            </thead>
            <tbody>
    <?php
        foreach ($result as $key => $row) {
    ?>
                <tr bgcolor="#FFFFFF" onMouseOver="this.style.backgroundColor='#F4F4F4';return true;" onMouseOut="this.style.backgroundColor=''; return true;">
                    <td><?=format_phone($row->targetno)?></td>
                    <td><?=$row->targetname?></td>
                <?php if ($row->result == '0' || $row->result == '1000') { //성공일 때만 ?>
                    <td><?=convert_result_telecom($row->telecom)?></td>
                <?php } else { ?>
                    <td>&nbsp;</td>
                <?php } ?>
                    <td><?=convert_campaign_result($row->result)?></td>
                    <td><?=$row->result_time?></td>
                    <td></td>
                </tr>
    <?php
        }
    ?>
            </tbody>
        </table>
    </div>
    <div><?=$this->pagination->create_links();?></div>
</div>
<script type="text/javascript">
var downloadExcel = function(arv) {
    var procid = $("input[name=procid]").val();
    var table = $("input[name=table]").val();
    $(location).attr('href', '/result/result_excel/'+table+'/'+procid+'/'+arv);
}
</script>
