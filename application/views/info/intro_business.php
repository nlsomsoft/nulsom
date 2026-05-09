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
            <div class="body_title" style="position:relative;">회원정보변경</div>
            <div style="margin:50px 100px; border:1px solid #CCCCCC; border-radius:10px">
                <p class="indispens" style="padding-right:20px;">
                    * 필수 입력 사항입니다.</p>
                <div style="padding-left:80px;">
                    <fieldset class="field_1 noline">
                        <legend class="blind">정보입력</legend>

<?php
$attributes = array(
    'id' => 'frmInfo',
    'onsubmit' => 'return doModifyInfo();'
);
echo form_open('info/register', $attributes);
?>
<?php
$data = array(
  'user_type' => '2',
);
echo form_hidden($data);
?>
                        <table class="write_inp info" border="0">
                            <colgroup><col style="width: 155px;">
                            </colgroup><tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="corpname">사업자명<span>*</span></label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'ipt_com_name',
                                    'id'    => 'ipt_com_name',
                                    'class' => 'input_36 disabled',
                                    'maxlength' => '20',
                                    'style' => 'width:325px;',
                                    'title' => '사업자명',
                                    'readonly' => 'readonly',
                                    'value' => $this->session->userdata('com_name'),
                                    'placeholder' => ''
                                );
                                echo form_input($data);
                            ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="corpssn">사업자등록번호<span>*</span></label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'ipt_com_number',
                                    'id'    => 'ipt_com_number',
                                    'class' => 'input_36 disabled',
                                    'maxlength' => '20',
                                    'style' => 'width:325px;',
                                    'readonly' => 'readonly',
                                    'value' => $this->session->userdata('com_number'),
                                    'title' => '사업자등록번호',
                                    'placeholder' => ''
                                );
                                echo form_input($data);
                            ?>
                                     </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="realname">담당자명<span>*</span></label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'ipt_realname',
                                    'id'    => 'ipt_realname',
                                    'class' => 'input_36 disabled',
                                    'maxlength' => '20',
                                    'style' => 'width:325px;',
                                    'readonly' => 'readonly',
                                    'value' => $this->session->userdata('realname'),
                                    'title' => '담당자명',
                                    'placeholder' => ''
                                );
                                echo form_input($data);
                            ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="mobile">핸드폰번호<span>*</span></label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'ipt_mobile',
                                    'id'    => 'ipt_mobile',
                                    'class' => 'input_36 disabled',
                                    'maxlength' => '20',
                                    'style' => 'width:325px;',
                                    'readonly' => 'readonly',
                                    'value' => $this->session->userdata('mobile'),
                                    'title' => '핸드폰번호',
                                    'placeholder' => ''
                                );
                                echo form_input($data);
                            ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="userid">아이디<span>*</span></label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'ipt_user_id',
                                    'id'    => 'ipt_user_id',
                                    'class' => 'input_36 disabled',
                                    'minlength' => '6',
                                    'maxlength' => '20',
                                    'style' => 'width:325px;',
                                    'title' => '아이디',
                                    'readonly' => 'readonly',
                                    'value' => $this->session->userdata('userid'),
                                    'placeholder' => ''
                                );
                                echo form_input($data);
                            ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="email">이메일<span>*</span></label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'ipt_email',
                                    'id'    => 'ipt_email',
                                    'class' => 'input_36',
                                    'maxlength' => '50',
                                    'style' => 'width:325px;',
                                    'title' => '이메일',
                                    'required' => '',
                                    'placeholder' => ''
                                );
                                echo form_input($data);
                            ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="phone">전화번호</label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'ipt_phone',
                                    'id'    => 'ipt_phone',
                                    'class' => 'input_36',
                                    'maxlength' => '13',
                                    'style' => 'width:325px;',
                                    'value' => $this->session->userdata('phone'),
                                    'title' => '전화번호',
                                    'placeholder' => ''
                                );
                                echo form_input($data);
                            ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="phone">발신자명<span>*</span></label>
                                    </th>
                                    <td>
                            <?php
                                $data = array(
                                    'name'  => 'ipt_ad_title',
                                    'id'    => 'ipt_ad_title',
                                    'class' => 'input_36',
                                    'maxlength' => '15',
                                    'style' => 'width:325px;',
                                    'value' => $this->session->userdata('ad_title'),
                                    'title' => '발신자명',
                                    'placeholder' => ''
                                );
                                echo form_input($data);
                            ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </fieldset>
                    <div class="bot_btn" style="margin-bottom:50px; margin-left:-70px">
                        <input type="submit" id="signup-button" class="sowsms-inp-submit" value="변경하기" />
                    </div>
                </div>
			</div>
            </div>
        </td>
	</tr>
</table>
</form>
</div>
<!-- content end -->


<script type="text/javascript">
var doModifyInfo = function () {
    var ipt_com_name = $('#ipt_com_name').val().trim();
    if (ipt_com_name == '') {
        alert("사업장명을 입력하세요.");
        $('#ipt_com_name').focus();
        return false;
    }
    var ipt_com_number = $('#ipt_com_number').val().trim();
    if (ipt_com_number == '') {
        alert("사업자등록번호를 입력하세요.");
        $('#ipt_com_number').focus();
        return false;
    }
    var ipt_realname = $('#ipt_realname').val().trim();
    if (ipt_realname == '') {
        alert("담당자명을 입력하세요.");
        $('#ipt_realname').focus();
        return false;
    }
    var ipt_mobile = $('#ipt_mobile').val().trim();
    if (ipt_mobile == '') {
        alert("핸드폰번호를 입력하세요.");
        $('#ipt_mobile').focus();
        return false;
    }
    var ipt_user_id = $('#ipt_user_id').val().trim();
    if (ipt_user_id == '') {
        alert("아이디를 입력하세요.");
        $('#ipt_user_id').focus();
        return false;
    }

    var ipt_email = $('#ipt_email').val().trim();
    if (ipt_email == '') {
        alert("이메일 주소를 입력하세요.");
        $('#ipt_email').focus();
        return false;
    }
    if (!validateEmail(ipt_email)) {
        alert("올바른 이메일 주소를 입력하세요.");
        $('#email').focus();
        return false;
    }
    var ipt_ad_title = $('#ipt_ad_title').val().trim();
    if (ipt_ad_title == '') {
        alert("발신자명을 입력하세요.");
        $('#ipt_ad_title').focus();
        return false;
    }
    return true;
}
var validateEmail = function(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}
</script>