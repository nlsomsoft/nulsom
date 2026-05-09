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
<?php if ($params[0] == 'succ') { ?>
        <table border="1" class="basic" style="font-size:14px;">
            <thead>
        <?php
            if ($params[1] == 'bank') $deposit_type = '무통장 입금';
            else if ($params[1] == 'card') $deposit_type = '신용카드 결제';
            else if ($params[1] == 'vbank') $deposit_type = '가상계좌 입금';
            else if ($params[1] == 'directbank') $deposit_type = '실시간 계좌이체';
        ?>
                <tr align="center">
                    <th style="background:#F4F4F4; height:50px" width="35%">결제방식</th>
                    <th style="background:#FFFFFF" width="75%"><?=$deposit_type?></th>
                </tr>
            </thead>
            <tbody>
        <?php
            if ($params[1] == 'bank' || $params[1] == 'vbank') $deposit_result = '입금 대기';
            else if ($params[1] == 'card' || $params[1] == 'directbank') $deposit_result = '입금';
        ?>
                <tr>
                    <td style="background:#F4F4F4; height:50px" >처리결과</td>
                    <td><?=$deposit_result?></td>
                </tr>
                <tr>
                    <td style="background:#F4F4F4; height:50px" >결제금액</td>
                    <td><?=number_format($params[2]);?>원</td>
                </tr>
                <tr>
                    <td style="background:#F4F4F4; height:50px" >입금자명</td>
                    <td><?=$params[3]?></td>
                </tr>
        <?php
            if ($params[1] == 'bank') $deposit_bank = BANK.', 계좌번호 : '.ACCOUNT.', 예금주 : '.ACCOUNT_NAME;
            else $deposit_bank = $params[4];
        ?>
        <?php /* ?>
                <tr>
                    <td style="background:#F4F4F4; height:50px" >입금계좌</td>
                    <td><?=$deposit_bank?></td>
                </tr>
        <?php */ ?>
            </tbody>
        </table>
<?php } else if ($params[0] == 'error') { ?>
        <table border="1" class="basic" style="font-size:14px;">
            <thead>
                <tr align="center">
                    <th style="background:#F4F4F4; height:50px" width="35%">결제방식</th>
                    <th style="background:#FFFFFF; text-align:left; padding-left:30px;" width="75%">결제는 성공하였으나 시스템 오류가 발생했습니다. <br />고객센터에 문의하십시오. T.<?=CALLCENTER?></th>
                </tr>
            </thead>
        </table>
<?php } else { // fail ?>
        <table border="1" class="basic" style="font-size:14px;">
            <thead>
                <tr align="center">
                    <th style="background:#F4F4F4; height:50px" width="35%">결제방식</th>
                    <th style="background:#FFFFFF; text-align:left; padding-left:30px;" width="75%">요청하신 결제는 실패했습니다.</th>
                </tr>
            </thead>
        </table>
<?php } ?>
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