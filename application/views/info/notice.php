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
                    <th scope="col">제목</th>
                    <th scope="col">등록일</th>
                </tr>
            </thead>
            <tbody>
            <?php
                foreach($result as $row) {
            ?>
                <tr bgcolor="#FFFFFF">
                    <td><?=$row->xid?></td>
                    <td style="text-align:left;"><a href="/info/notice_view/<?=$row->xid?>"><?=$row->subject?></a></td>
                    <td><?=mydate_format('Y-m-d H:i',$row->add_date)?></td>

                </tr>
            <?php
                }
            ?>
            </tbody>
        </table>
    </div>
</div>

<div><?=$this->pagination->create_links();?></div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td height="30"></td></tr>
<tr><td><img src="/images/tip.gif" width="145" height="34" alt="" /></td></tr>
<tr><td height="1" bgcolor="#CCCCCC"></td></tr>
<tr><td class="help_txt">
        * 제목을 클릭하시면 자세한 공지내용을 확인하실 수 있습니다.
</td></tr>
<tr><td height="30px"></td></tr>
</table>
        </td>
</tr>
</table>

</div>
<!-- content end -->
