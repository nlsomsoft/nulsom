<!-- content start -->
<div class="body-inner-table" style="padding-top:20px; min-height:700px;">

    <table width="1200" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <!-- left menu start -->
        <td width="210" valign="top">
            <?php
                $g_left_menu_flag = 'pay';
                include_once(VIEWPATH.'/templates/left_menu.php');
            ?>
        </td>
            <!-- left menu end -->
            <td width="30"></td>
            <td width="960" valign="top">

<div class="content_wrap">
    <div class="body_title" style="position:relative;">결제처리 결과
	</div>
	<br>
    <div class="board">
        <table border="1" class="basic" style="font-size:14px;">
            <thead>
                <tr align="center">
                    <th style="background:#F4F4F4; height:50px" width="35%">결제방식</th>
                    <th style="background:#FFFFFF" width="75%">실시간이체, 신용카드, 가상계좌, 무통장입금</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="background:#F4F4F4; height:50px" >처리결과</td>
                    <td>신용카드, 실시간이체인 경우 : 성공/실패, 무통장입금,가상계좌 : 입금 대기</td>
                </tr>
                <tr>
                    <td style="background:#F4F4F4; height:50px" >결제금액</td>
                    <td>100,000원</td>
                </tr>
                <tr>
                    <td style="background:#F4F4F4; height:50px" >입금자명<br>(가상계좌인 경우에 표시)</td>
                    <td>입금자명</td>
                </tr>
                <tr>
                    <td style="background:#F4F4F4; height:50px" >입금계좌<br>(무통,가상계좌인 경우에 표시)</td>
                    <td>무통장입금인 경우 : <?=BANK?>, 계좌번호 : <?=ACCOUNT?>, 예금주 : <?=ACCOUNT_NAME?><br>
                    가상계좌인 경우 : 은행명, 체번된 계좌번호, 예금주</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td height="30"></td></tr>
<tr><td><img src="/images/tip.gif" width="145" height="34" alt="" /></td></tr>
<tr><td height="1" bgcolor="#CCCCCC"></td></tr>
<tr><td class="help_txt">
        * <span style="color:red"><b>무통장입금, 가상계좌</b> 방식을 선택하신 경우, <b>이체를 완료</b>하셔야  결제처리가 됩니다.</span><br>
        * 가상계좌의 경우 신청내역(입금자명, 금액)과 실제 입금내역이 동일하여야 처리됩니다.
</td></tr>
<tr><td height="100"></td></tr>
</table>
        </td>
</tr>
</table>

</div>
<!-- content end -->