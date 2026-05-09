<!-- datepicker -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/css/handsontable.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="/js/datepicker-ko.js"></script>
<!-- /datepicker -->

<!-- content start -->
<div class="body-inner-table" style="padding-top:20px; min-height:700px;">

	<table width="1200" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<!-- left menu start -->
		<td width="210" valign="top">
            <?php
                $g_left_menu_flag = 'info';
                include_once(VIEWPATH.'/templates/left_menu.php');
            ?>
        </td>
			<!-- left menu end -->
			<td width="30"></td>
			<td width="960" valign="top">


<div class="content_wrap">
    <div class="body_title" style="position:relative;">공지사항
	</div>
    <div class="board">
        <table border="0" class="basic" style="font-size:14px">
            <colgroup>
            <col style="width: 20%">
            <col style="width: 50%">
            <col style="width: 30%">
            </colgroup>
            <thead>
                <tr align="center">
                    <th scope="col">공지번호</th>
                    <th scope="col">내용</th>
                    <th scope="col">등록일</th>
                </tr>
            </thead>
            <tbody>
                <tr bgcolor="#FFFFFF">
                    <td><?=$result['xid']?></td>
                    <td style="padding-top:30px;padding-bottom:50px;text-align:left;"><?=nl2br($result['body'])?></td>
                    <td><?=mydate_format('Y-m-d H:i',$result['add_date'])?></td>

                </tr>
            </tbody>
        </table>
        <table>
            <tr>
                <td><div class="style_btn" style="width:160px"><a href="/info/notice">공지사항 목록</a></div></td>
            </tr>
        </table>
    </div>
</div>


        </td>
</tr>
</table>

</div>
<!-- content end -->
