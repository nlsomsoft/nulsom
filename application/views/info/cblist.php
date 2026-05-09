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
    <div class="body_title" style="position:relative;">발신번호관리
    </div>

    <div style="margin-bottom:15px">
        <table width="100%" border="0" cellspacing="0" cellpadding="1">
            <tr>
            <td align="right"></td>
            <td width="80">
                <div class="style_btn" style="width:100px"><a href="#" onclick="changeCallbackNum('3');">사용</a></div>
            </td>
            <td width="80">
                <div class="style_btn" style="width:100px"><a href="#" onclick="changeCallbackNum('4');">미사용</a></div>
            </td>
            </tr>
        </table>
    </div>

    <div class="board">
    <?php
        $attributes = array(
            'name' => 'frmInfo',
            'id' => 'frmInfo'
        );
        echo form_open('/info/cblist', $attributes);
    ?>
    <?php
        $data = array(
            'status' => '',
            'chk_nums' => '',
            'new_value' => '',
            'xid' => '',
        );
        echo form_hidden($data);
    ?>
        <table border="0" class="basic">
            <colgroup>
                <col style="width: 10px">
                <col>
                <col>
                <col>
                <col>
                <col style="width: 250px">
            <?php /* ?>
                <col style="width: 150px">
                <col style="width: 80px">
            <?php */ ?>
            </colgroup>
            <thead>
                <tr align="center">
                    <th scope="col"></th>
                    <th scope="col"><input type="checkbox" name="all_check_bt"></th>
                    <th scope="col">발신번호</th>
                    <th scope="col">메모</th>
                    <th scope="col">등록일</th>
                    <th scope="col">상태</th>
            <?php /* ?>
                    <th colspan="2" scope="col">메모변경</th>
            <?php */ ?>
                </tr>
            </thead>

            <tbody>
        <?php
            $c_cnt = 0;
            foreach ($result as $row) {
                if ($row['status'] == '3') $status = '사용';
                else if ($row['status'] == '2') $status = '등록차단';
                else if ($row['status'] == '4') $status = '미사용';
                else $status = '등록대기'; // status : 1
        ?>
                <tr bgcolor="#FFFFFF" onMouseOver="this.style.backgroundColor='#F4F4F4';return true;" onMouseOut="this.style.backgroundColor=''; return true;">
                    <td></td>
                    <td><?php if ($row['status'] == '3' || $row['status'] == '4') { ?><input type="checkbox" name="chk_seq_no" value="<?=$row['xid']?>" /><?php } ?></td>
                    <td><?=phone_format($row['callback'])?></td>
                    <td><?=$row['name']?></td>
                    <td><?=mydate_format('Y-m-d',$row['reg_time'])?></td>
                    <td><?=$status?></td>
            <?php /* ?>
                    <td align="right"><input type="text" name="new_value_<?=$row['xid']?>" class="input_26" style="width:150px" maxlength="20" /></td>
                    <td><div class="style_btn" style="width:60px"><a href="#" onclick="changeMemo('<?=$row['xid']?>');">변경</a></div></td>
            <?php */ ?>
                </tr>
        <?php
                $c_cnt++;
            }
        ?>
            </tbody>
        </table>
    </div>
<?php if (1 && CALLBACK_MOBILE_AUTH_YN == 'Y') { ?>
    <?php
        $data = array(
            'c_cnt' => $c_cnt,
        );
        echo form_hidden($data);
    ?>
    <div style="padding:20px 0px 10px 10px"><b>- 발신번호 추가등록 (SMS 인증)</b></div>
    <div class="board">
        <table border="0" class="basic" style="font-size:14px">
            <colgroup>
            <col style="width: 240px">
            <col style="width: 200px">
            <col style="width: 110px">
            <col style="width: 170px">
            <col style="width: 240px">
            </colgroup>
            <thead>
                <tr align="center">
                    <th scope="col">메모</th>
                    <th colspan="2" scope="col">발신번호</th>
                    <th scope="col">인증번호</th>
                    <th scope="col">등록</th>
                </tr>
            </thead>
            <tbody>
                <tr bgcolor="#FFFFFF" height="40">
                    <td><input type="text" name="cname" id="cname" class="input_26" style="width:200px" maxlength="20" /></td>
                    <td><input type="text" name="cnumber" id="cnumber" class="input_26" style="width:150px; margin-left:20px" maxlength="13" /></td>
                    <td><div class="style_btn" style="width:60px"><a href="#" onclick="authRequest();">인증요청</a></div></td>
                    <td><input type="text" name="auth_number" id="auth_number" class="input_26" style="width:140px;" maxlength="6"></td>
                    <td><div class="style_btn" style="margin-left:60px; margin-right:60px"><a href="#" onclick="authConfirm();">등록하기</a></div></td>
                </tr>
            </tbody>
        </table>
        <div style="font-size:13px; color:#888; margin-top:-10px;">* 인증 유효시간은 1분간 유지됩니다.</div>
    </form>
   </div>
</div>
<?php } ?>

<?php
    $attributes = array(
        'name' => 'frmaddfile',
        'id' => 'frmaddfile'
    );
    echo form_open_multipart('/info/add_file', $attributes);
?>
    <div style="padding:20px 0px 10px 10px"><b>- 통신가입증명원 등록 </b></div>
    <div class="board">
        <table border="0" class="basic" style="font-size:14px;">
            <tbody>
                <tr class="font-13">
                  <td width="430px">
                    <div class="filebox bs3-primary" style="text-align:left;padding-top:5px;height:40px;">
                        <input class="upload-name" id="upload_name" placeholder="파일을 등록하세요." disabled="disabled" style="width:300px;">&nbsp;&nbsp;
                        <label for="file_info">파일찾기</label>
                        <input type="file" name="file_info" id="file_info" class="upload-hidden">
                    </div>
                 </td>
                 <td>
                     <a id="export_string" class="btn1-orange btn1-space" onclick="addFileImage();"><div style="width:200px;text-align:center;">통신가입증명원 등록하기</div></a>
                 </td>
                </tr>
            </tbody>
        </table>
        <div style="font-size:13px; color:#888; margin-top:-10px;">* JPG, JPEG, GIF, PNG, PDF 파일만 가능합니다.</div>
   </div>
</div>
</form>

<div style="padding:30px 0px 10px 10px"><b>- 발신번호 추가등록 (서류 제출)</b></div>
<div class="board">
    <table border="0" class="basic" style="font-size:14px;">
        <colgroup>
            <col style="width: 30%">
            <col style="width: 70%">
        </colgroup>
        <thead>
            <tr align="left" height="50">
                <th scope="col">&nbsp;&nbsp;&nbsp;통신가입증명원 확인 후 등록</th>
                <th scope="col">
                    <div class="style_btn" style="width:160px;float: left; "><a href="#" onclick="telcoInfo();">통신사별 고객센터 확인</a></div>
                </th>
            </tr>
            <tr style="height:70px;border-top: 1px solid #e4e4e4;border-bottom: 1px solid #e4e4e4;">
                <td style="width:250px;text-align:center;">개인</td>
                <td style="text-align:left; padding-left:20px;">- 본인명의 통신가입증명원</td>
            </tr>
            <tr style="height:100px;border-top: 1px solid #e4e4e4;border-bottom: 1px solid #e4e4e4;">
                <td style="width:250px;text-align:center;">기업</td>
                <td style="text-align:left; padding-left:20px;">
                - 회원명의 재직증명서<br />
                - 사업자 등록증<br />
                - 기업명의 통신가입증명원
                </td>
            </tr>

        </thead>
    </table>
    <div style="font-size:14px; color:#666; margin-top:-10px;">* 이메일 (<?=EMAIL?>) <?php if (FAX_NO) { ?>또는 팩스 (<?=FAX_NO?>)<?php } ?>로 보내주세요.</div>
</div>
<br />
<br />
<br />



<!--
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td height="30"></td></tr>
<tr><td><img src="/images/tip.gif" width="145" height="34" alt="" /></td></tr>
<tr><td height="1" bgcolor="#CCCCCC"></td></tr>
<tr><td class="help_txt">
        * 발신번호는 10개까지 등록하실 수 있습니다.<br>
        * 2015년 10월 16일부터 발신번호 임의변경 발송을 차단하도록 규제하고 있습니다.<br>
        * 사용하실 발신번호는 인증을 통해서 등록하셔야 메세지 발송이 가능합니다.<br>
        <span style="color:red">(거짓으로 표시된 전화번호로 인한 이용자 피해 예방에 관한 고시 전기통신사업법 제84조)</span>
</td></tr>
<tr><td height="30px"></td></tr>
</table>
-->
</td>
</tr>
</table>

</div>

<div class="alpha60" id="bak" style="width:100%; height:100%; left:0px; top:0px; position:absolute; z-index:9999; display:none;" align="center">
<table border="0" width="100%" height="100%">
<tr><td align="center" valign="middle" onClick="hiddenBigImg_fn()"><img id="bigimg" name="bigimg" alt="" style="border-width:5px; border-color:white; border-style:solid"  />
</td></tr></table>
</div>
<!-- content end -->
<script type="text/javascript">
var addFileImage = function () {
    var file = $("#upload_name").val();
    if (file == "") {
        alert("GIF,JPG,JPEG,PNG,PDF 파일을 등록 하세요.");
        return;
    }
    // 정규식을 사용하여 jpg,png,gif,bmp|tiff등 이미지파일의 확장자를 가진것을 추려낸다.
    if (/(\.gif|\.jpg|\.jpeg|\.png|\.pdf)$/i.test(file) == false) {
        alert("GIF,JPG,JPEG,PNG,PDF 파일을 등록 하세요.");
        return;
    }
    $("form#frmaddfile").submit();
}
$(document).ready(function(){
    var fileTarget = $('.filebox .upload-hidden');
    fileTarget.on('change', function(){
        if(window.FileReader){
            var filename = $(this)[0].files[0].name;
        } else {
            var filename = $(this).val().split('/').pop().split('\\').pop();
        }

        $(this).siblings('.upload-name').val(filename);
    });
});
function telcoInfo() {
    var obj = document.getElementById("bak");
    obj.style.width = document.body.scrollWidth + 'px';
    obj.style.height = document.body.scrollHeight + 'px';
    obj.style.display = "block";
    document.all.bigimg.src='/images/telco_info.jpg';
}

function hiddenBigImg_fn() {
    var obj = document.getElementById("bak");
    obj.style.display = "none";
}

var changeCallbackNum = function (arv) {
    var k = 0;
    var chk_nums = [];

    if (arv == '') return;
    $("input[name=chk_seq_no]:checked").each(function() {
        chk_nums[k++] = $(this).val();
    });

    if (k == 0 || chk_nums.length == 0) {
        alert("목록을 선택하세요.");
        return;
    }
    $("input[name=status]").val(arv);
    $("input[name=chk_nums]").val(chk_nums);
    $("form#frmInfo").attr("action", "/info/change_nums");
    $("form#frmInfo").submit();
}
var changeMemo = function (xid) {
    return;
    var new_value = $("input[name=new_value_"+xid+"]").val();
    if (new_value == '') {
        alert("새로운 메모를 입력하세요.");
        return;
    }
    $("input[name=xid]").val(xid);
    $("input[name=new_value]").val(new_value);
    $("form#frmInfo").attr("action", "/info/callback_name");
    $("form#frmInfo").submit();
}
var authRequest = function () {
    var c_cnt = parseInt($("input[name=c_cnt]").val());
    if (c_cnt > 10) {
        alert("발신번호는 10개까지 등록할 수 있습니다.");
        return;
    }

    var cname = $.trim($("input[name=cname]").val());
    if (cname == "") {
        alert("메모를 입력하세요.");
        return;
    }
    var cnumber = $.trim($("input[name=cnumber]").val());
    if (cnumber == "") {
        alert("발신번호를 입력하세요.");
        return;
    }
    // var regExp = /(01[016789])-?(\d{4})-?\d{4}$/g;
    // if (!regExp.test(cnumber)) {
    //     alert("발신번호 형식 오류입니다.");
    //     return;
    // }

    var str = cnumber.substr(0, 3);
    var org_number = cnumber.replace("-","");
    var phone_flag = false;
    if (str == "010" || str == "011" || str == "016" || str == "017" || str == "018" || str == "019") {
        if (org_number.length != 11 && org_number.length != 12) {
            alert("휴대폰 번호 형식 오류입니다.");
            return;
        }
        phone_flag = true;
    }
    if (phone_flag == false) {
        alert("인증할 발신번호 형식 오류입니다.");
        return;
    }

    var csrf_sowkorea_name = $.trim($("input[name=csrf_sowkorea_name]").val());
    $.ajax({
        type: "POST",
        url: "/info/request_auth",
        data: {
            "csrf_sowkorea_name" : csrf_sowkorea_name,
            "cname" : cname,
            "cnumber" : cnumber,
            "where" : "info"
        },
        dataType: "json",
        async: false,
        success : function(data, status, xhr) {
            $("input[name=csrf_sowkorea_name]").val(data.csrf_sowkorea_name);
            alert(data.message);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.responseText);
        }
    });

}
var authConfirm = function () {
    var c_cnt = parseInt($("input[name=c_cnt]").val());
    if (c_cnt > 10) {
        alert("발신번호는 10개까지 등록할 수 있습니다.");
        return;
    }

    var auth_number = $.trim($("input[name=auth_number]").val());
    if (auth_number == "") {
        alert("인증번호를 입력하세요.");
        return;
    }

    var regex= /^[0-9]*$/;
    if (!regex.test(auth_number)) {
        alert("인증번호 형식 오류입니다.");
        return;
    }

    var csrf_sowkorea_name = $.trim($("input[name=csrf_sowkorea_name]").val());
    $.ajax({
        type: "POST",
        url: "/info/confirm_auth",
        data: {
            "csrf_sowkorea_name" : csrf_sowkorea_name,
            "auth_number" : auth_number,
            "where" : "info"
            },
        dataType: "json",
        async: false,
        success : function(data, status, xhr) {
            $("input[name=csrf_sowkorea_name]").val(data.csrf_sowkorea_name);
            alert(data.message);
            if (data.result == "success") $(location).attr('href', '/info/cblist');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.responseText);
        }
    });
}
</script>