
<!-- footer start -->
            <div id="copyright" style="background-image:url(/images/footer_bg.jpg);background-size: auto;background-position: center center; background-repeat: no-repeat; min-width:1200px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="35"></td>
  </tr>
  <tr>
    <td align="center">
    <table width="1200" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="font_white" width="800" height="20">
            <a href="/"><font style="color:#ffffff;" onMouseOver="this.style.color='#ffffff'; return true;" onMouseOut="this.style.color='#ffffff'; return true;">홈으로</font></a>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href="/statics/sms"><font style="color:#ffffff;" onMouseOver="this.style.color='#ffffff'; return true;" onMouseOut="this.style.color='#ffffff'; return true;">서비스 안내</font></a>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href="/signup/terms"><font style="color:#ffffff;" onMouseOver="this.style.color='#ffffff'; return true;" onMouseOut="this.style.color='#ffffff'; return true;">서비스 이용약관</font></a>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href="/signup/terms/person"><font style="color:#ffffff;" onMouseOver="this.style.color='#ffffff'; return true;" onMouseOut="this.style.color='#ffffff'; return true;"><b>개인정보취급방침</b></font></a>
        </td>
    <td width="400"></td>
    </tr>
    <tr>
    <td height="20"></td>
    <td></td>
    </tr>
    <tr>
    <td height="20" class="font_white">Copyrightⓒ <font color="#ff9900"><b><?=COPYRIGHT?></b></font>&nbsp;All Rights Reserved.</td>
    <td rowspan="5" valign="top">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="70" rowspan="3" valign="top"><img src="/images/cs_info_icon.png" alt="" /></td>
      <td class="font_white" width="350"><span style="font-size:20px; font-weight:bold">고객만족센터 | 가격상담</span></td>
    </tr>
<?php if ($_SERVER['STORENAME'] == 'fn070') { ?>
    <tr>
      <td class="font_white"><span style="font-size:20px; font-weight:bold">통합콜센터 <?=CALLCENTER?></span></td>
    </tr>
<?php } else { ?>
    <tr>
      <td class="font_white"><span style="font-size:20px; font-weight:bold"><?=CALLCENTER?></span></td>
    </tr>
    <tr>
      <td class="font_white">AM 10시 ~ PM 6시  (토,일,공휴일 휴무)</td>
    </tr>
<?php } ?>
</table>

    </td>
    </tr>
    <tr>
    <td height="20" class="font_white"><?=COMPANY_NAME?>&nbsp;대표이사 <?=CEO_NAME?></td>
    </tr>
    <tr>
    <td height="20" class="font_white">사업자등록번호 : <?=BUSINESS_NO?>&nbsp;|&nbsp;통신판매업신고 : <?=BUSINESS_TYPE?></td>
    </tr>
    <tr>
    <td height="20" class="font_white">주소 : <?=ADDRESS?></td>
    </tr>
    <tr>
    <td height="20" class="font_white">
        Tel : <?=CALLCENTER?>&nbsp;
        <?php if (FAX_NO != '') { ?>|&nbsp;Fax : <?=FAX_NO?>&nbsp;<?php } ?>
        |&nbsp;Email : <?=EMAIL?>
    </td>
    </tr>
    <tr>
    <td height="25"></td>
    <td></td>
    </tr>
    </table>
    </td>
  </tr>
</table><!-- sowkorea footer end -->
    </div>
</div>

<?php if ($this->session->flashdata('notice')) { ?>
<script type="text/javascript">
$(document).ready(function(){
    alert("<?=$this->session->flashdata('notice')?>");
});
</script>
<?php } else if ($this->session->flashdata('layer_notice')) { ?>
<script type="text/javascript">
$(document).ready(function(){
    $("#result_message").text("<?=$this->session->flashdata('layer_notice')?>");
    resultProcess();
});
</script>
<?php } ?>

</body>
</html>
