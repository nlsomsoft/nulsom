<!-- content start -->
<div class="body-inner-table" style="padding-top:20px; min-height:700px;">

<table width="1200" border="0" cellspacing="0" cellpadding="0">
    <tr>
<!-- left menu start -->
        <td width="210" valign="top">
<?php
    $g_left_menu_flag = 'signup';
    include_once(VIEWPATH.'/templates/left_menu.php');
?>
        </td>
<!-- left menu end -->
        <td width="30"></td>
        <td width="960" valign="top">
            <div class="mb_path">
            <ol class="on_2">
                <li><span class="blind">약관동의</span></li>
                <li><span class="blind">가입확인</span></li>
                <li><span class="blind">정보입력</span></li>
                <li><span class="blind">가입완료</span></li>
            </ol>
            </div>
            <br><br>
            <p>가입하실 회원유형을 선택하세요.</p>

            <div class="mb_path" style="margin-top:15px; width:100%; text-align:center">
                <div class="join_divi" style="display:inline-block; width:753px">
                    <dl class="personal">
                    <dt>일반회원(개인)</dt>
                    <dd>개인 사용자 및 국내, 해외에 거주하시는 외국인의 경우<br>일반회원으로 가입하시면 됩니다.<br>
                    <span id="uty_1" class="bc bc_gry"><span><a href="#" onclick="displayCharSET(1); return false;">일반회원 가입하기</a></span></span>
                    <input type="radio" name="user_type" value="1" style="display:none">
                    </dd>
                    </dl>
                    <dl class="enterprise">
                    <dt>사업자회원(회사/단체)</dt>
                    <dd>회사 혹은 단체로 가입하여 웹서비스를 하나의 사업자회원<br>아이디로 사용하실 수 있습니다.<br>
                    <span id="uty_2" class="bc bc_gry"><span><a href="#" onclick="displayCharSET(2); return false;">사업자회원 가입하기</a></span></span>
                    <input type="radio" name="user_type" value="2" style="display:none">
                    </dd>
                    </dl>
                </div>
           </div>
            <br><br>
            <p>휴대폰 본인인증 후 회원가입이 가능합니다.</p><br>

<?php
$attributes = array(
    'id' => 'reqKMCISForm',
    'name' => 'reqKMCISForm',
);
echo form_open('https://www.kmcert.com/kmcis/web/kmcisReq.jsp', $attributes);
?>

<?php
$data = array(
    'tr_cert' => $tr_cert,
    'tr_url' => $tr_url,
    'tr_add' => $tr_add,
);
echo form_hidden($data);
?>

<table width="100%" style="border: 1px solid #e0e2e2;">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
      <tr>
       <td colspan="3" height="20"></td>
     </tr>
      <tr>
        <td width="140" height="120" align="right" valign="middle"><img src="/images/L2.gif" width="128" height="89" /></td>
      <td  width="500" valign="middle">
      1. 본인명의로 된 휴대폰으로만 인증이 가능합니다.<br>
      2. 개인회원의 경우 만14세 미만은 가입할 수 없습니다.<br>
      3. 타인정보사용 시, 3년 이하의 징역 또는 1천만원 이하의 벌금이 부과될 수 있습니다.</td>
      <td valign="middle"><div class="bot_btn" style="margin-bottom:30px">
                        <span class="bc bc_org"><span id="certify-button"><a href="#" onclick="openKMCISWindow();">본인인증</a></span></span>
                    </div></td>
    </tr>
      <tr>
       <td colspan="3" height="20"></td>
     </tr>
    </table>
        </td>
  </tr>
</table>
</form>
        </td>
    </tr>
    <tr><td height="50"></td></tr>
</table>

</div>
<!-- content end -->

<script language="Javascript">
function displayCharSET(num) {
    // var pm = document.getElementsByName( "user_type" );
    // pm[_eset].checked = true;

    $("input:radio[name='user_type']:input[value=" + num + "]").prop('checked', true);

    if (num == 1) {
        $("#uty_1").attr("class", "bc bc_org");
        $("#uty_2").attr("class", "bc bc_gry");
    } else {
        $("#uty_1").attr("class", "bc bc_gry");
        $("#uty_2").attr("class", "bc bc_org");
    }
}
</script>
<script language="Javascript">
<!--
window.name = "kmcis_web_sowkorea";
var KMCIS_window;
function openKMCISWindow() {
    if (!$("input:radio[name='user_type']").is(":checked")) {
        alert("가입하실 회원유형을 선택하세요.");
        return;
    }

    var user_type = $(':radio[name="user_type"]:checked').val();
    writeCookie("user_type",user_type,1);

    var UserAgent = navigator.userAgent;
    /* 모바일 접근 체크*/
    // 모바일일 경우 (변동사항 있을경우 추가 필요)
    if (UserAgent.match(/iPhone|iPod|Android|Windows CE|BlackBerry|Symbian|Windows Phone|webOS|Opera Mini|Opera Mobi|POLARIS|IEMobile|lgtelecom|nokia|SonyEricsson/i) != null || UserAgent.match(/LG|SAMSUNG|Samsung/) != null) {
            document.reqKMCISForm.target = '';
    }
    // 모바일이 아닐 경우
    else {
        KMCIS_window = window.open('', 'KMCISWindow', 'width=425, height=550, resizable=0, scrollbars=no, status=0, titlebar=0, toolbar=0, left=435, top=250' );

        if(KMCIS_window == null){
            alert(" ※ 윈도우 XP SP2 또는 인터넷 익스플로러 7 사용자일 경우에는 \n    화면 상단에 있는 팝업 차단 알림줄을 클릭하여 팝업을 허용해 주시기 바랍니다. \n\n※ MSN,야후,구글 팝업 차단 툴바가 설치된 경우 팝업허용을 해주시기 바랍니다.");
        }
        document.reqKMCISForm.target = 'KMCISWindow';
    }
    document.reqKMCISForm.action = 'https://www.kmcert.com/kmcis/web/kmcisReq.jsp';
    document.reqKMCISForm.submit();
}
//-->
</script>