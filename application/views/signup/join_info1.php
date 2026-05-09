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
    'name' => 'form_auth',
);
echo form_open('signup/kcp_req', $attributes);
?>

<?php
$data = array(
    'ordr_idxx' => '',
    'req_tx' => 'cert',                 // 요청종류
    'cert_method' => '01',              // 요청구분
    'web_siteid' => $g_conf_web_siteid, // 웹사이트아이디
    'site_cd' => $g_conf_site_cd,       // 사이트코드
    'Ret_URL' => $g_conf_Ret_URL,       // Ret_URL
    'cert_otp_use' => 'Y',              // cert_otp_use 필수
    'cert_enc_use' => 'Y',              // cert_enc_use 필수
    'cert_enc_use_ext' => 'Y',          // 리턴 암호화 고도화
    'res_cd' => '',
    'res_msg' => '',
    'veri_up_hash' => '',               // up_hash 검증 을 위한 필드
    'cert_able_yn' => '',               // 본인확인 input 비활성화
    'web_siteid_hashYN' => 'Y',         // web_siteid 을 위한 필드
    'param_opt_1' => 'opt1',            // 가맹점 사용 필드 (인증완료시 리턴)
    'param_opt_2' => 'opt2',            // 가맹점 사용 필드 (인증완료시 리턴)
    'param_opt_3' => 'opt3',            // 가맹점 사용 필드 (인증완료시 리턴)
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
      <td valign="middle">
        <div>
            <input type="image" src="/images/btn_cert.jpg" onclick="return auth_type_check();" alt="인증을 요청합니다" />

        </div>

    </td>
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
<script type="text/javascript">
    // 인증창 종료후 인증데이터 리턴 함수
    function auth_data(frm) {
        var auth_form     = document.form_auth;
        var nField        = frm.elements.length;
        var response_data = "";
        // up_hash 검증
        if (frm.up_hash.value != auth_form.veri_up_hash.value) {
            alert("up_hash 변조 위험있음");
        }
        if (frm.site_auth.value == '0000') {
            window.location.href = '/signup/join_form';
        } else {
            window.location.href = '/signup/join_agree';
        }
    }

    // 인증창 호출 함수
    function auth_type_check() {
        if (!$("input:radio[name='user_type']").is(":checked")) {
            alert("가입하실 회원유형을 선택하세요.");
            return false;
        }
        var auth_form = document.form_auth;
        if (auth_form.ordr_idxx.value == "") {
            alert( "요청번호는 필수 입니다." );
            return false;
        } else {
            var user_type = $(':radio[name="user_type"]:checked').val();
            $("input[name=param_opt_1").val(user_type);
            // writeCookie("user_type",user_type,1);
            if( ( navigator.userAgent.indexOf("Android") > - 1 || navigator.userAgent.indexOf("iPhone") > - 1 ) == false ) { // 스마트폰이 아닌경우
                var return_gubun;
                var width  = 410;
                var height = 500;

                var leftpos = screen.width  / 2 - ( width  / 2 );
                var toppos  = screen.height / 2 - ( height / 2 );

                var winopts  = "width=" + width   + ", height=" + height + ", toolbar=no,status=no,statusbar=no,menubar=no,scrollbars=no,resizable=no";
                var position = ",left=" + leftpos + ", top="    + toppos;
                var AUTH_POP = window.open('','auth_popup', winopts + position);
            }
            auth_form.method = "post";
            auth_form.target = "auth_popup"; // !!주의 고정값 ( 리턴받을때 사용되는 타겟명입니다.)
            auth_form.action = "/kcp/kcp_req"; // 인증창 호출 및 결과값 리턴 페이지 주소
            return true;
        }
    }

    /* 예제 */
    window.onload=function() {
        init_orderid(); // 요청번호 샘플 생성
    }

    // 요청번호 생성 예제 ( up_hash 생성시 필요 )
    function init_orderid() {
        var today = new Date();
        var year  = today.getFullYear();
        var month = today.getMonth()+ 1;
        var date  = today.getDate();
        var time  = today.getTime();
        if (parseInt(month) < 10) {
            month = "0" + month;
        }
        var vOrderID = year + "" + month + "" + date + "" + time;
        document.form_auth.ordr_idxx.value = vOrderID;
    }
</script>