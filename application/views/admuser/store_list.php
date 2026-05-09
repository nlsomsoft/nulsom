<body class="responsive is-mobile">
<div id="to_content"><a href="#container">본문 바로가기</a></div>

<?php
    $g_menu_flag = 'user';
    include_once(VIEWPATH.'/templates/adm_menu.php');
?>

<div id="wrapper">
    <div id="container">
        <div id="text_size">
            <!-- font_resize('엘리먼트id', '제거할 class', '추가할 class'); -->
            <button onclick="font_resize('container', 'ts_up ts_up2', '');"><img src="/images/adm/ts01.gif" alt="기본"></button>
            <button onclick="font_resize('container', 'ts_up ts_up2', 'ts_up');"><img src="/images/adm/ts02.gif" alt="크게"></button>
            <button onclick="font_resize('container', 'ts_up ts_up2', 'ts_up2');"><img src="/images/adm/ts03.gif" alt="더크게"></button>
        </div>
        <h1>상점관리</h1>
<div class="local_ov01 local_ov">전체목록 <?=number_format($total_rows)?> 건</div>

<div class="btn_add01 btn_add">
    <a href="#accountModal" data-toggle="modal">상점등록하기</a>
</div>

<?php
$attributes = array(
    'name' => 'frmadmin',
    'id' => 'frmadmin',
    'onsubmit' => 'return fmemberlist_submit(this);'
);
echo form_open('admuser/store_auth', $attributes);
?>

<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    <tr>
        <th scope="col">PID</th>
        <th scope="col">상점아이디</th>
        <th scope="col">단문</th>
        <th scope="col">선거단문</th>
        <th scope="col">장문</th>
        <th scope="col">선거장문</th>
        <th scope="col">포토</th>
        <th scope="col">선거포토</th>
        <th scope="col">KAT</th>
        <th scope="col">KFT</th>
        <th scope="col">KFTM</th>
        <th scope="col">CH_SMS</th>
        <th scope="col">CH_LMS</th>
        <th scope="col">CH_MMS</th>
        <th scope="col">CH_KKO</th>
        <th scope="col">단문(계약)</th>
        <th scope="col">장문(계약)</th>
        <th scope="col">포토문(계약)</th>
        <th scope="col">카카오(계약)</th>
        <th scope="col">잔액부족시</th>
        <th scope="col">등록일시</th>
    </tr>
    </thead>
    <tbody>
<?php
    $i = 0;
    foreach ($result as $row) {
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td class="td_num"><?=$row->storeno?></td>
        <td class="td_id"><a href="<?=$row->url?>" target="_blank"><?=$row->storename?></a></td>
        <td class="td_num"><?=$row->sms1?></td>
        <td class="td_num"><?=$row->sms2?></td>
        <td class="td_num"><?=$row->lms1?></td>
        <td class="td_num"><?=$row->lms2?></td>
        <td class="td_num"><?=$row->mms1?></td>
        <td class="td_num"><?=$row->mms2?></td>
        <td class="td_num"><?=$row->kat?></td>
        <td class="td_num"><?=$row->kft?></td>
        <td class="td_num"><?=$row->kftm?></td>
        <td class="td_num"><?=$row->ch_sms?></td>
        <td class="td_num"><?=$row->ch_lms?></td>
        <td class="td_num"><?=$row->ch_mms?></td>
        <td class="td_num"><?=$row->ch_kko?></td>
        <td class="td_num" style="font-weight:bold;"><?=$row->contract_sms?></td>
        <td class="td_num" style="font-weight:bold;"><?=$row->contract_lms?></td>
        <td class="td_num" style="font-weight:bold;"><?=$row->contract_mms?></td>
        <td class="td_num" style="font-weight:bold;"><?=$row->contract_kko?></td>
        <td class="td_num"><?=($row->restrict_sending == '1' ? '<span style="color:#ff5959;">발송불가</span>' : '발송가능')?></td>
        <td class="td_id"><?=mydate_format('Y-m-d',$row->reg_time)?></td>
    </tr>
<?php
        $i ++;
    }
?>

            </tbody>
    </table>
</div>

<div><?=$this->pagination->create_links();?></div>

</form>
        <noscript>
            <p>
                귀하께서 사용하시는 브라우저는 현재 <strong>자바스크립트를 사용하지 않음</strong>으로 설정되어 있습니다.<br>
                <strong>자바스크립트를 사용하지 않음</strong>으로 설정하신 경우는 수정이나 삭제시 별도의 경고창이 나오지 않으므로 이점 주의하시기 바랍니다.
            </p>
        </noscript>

    </div>
</div>

<!-- <p>실행시간 : 0.00087904930114746 -->

</body>

<!-- The Modal Start -->
<div class="modal fade" id="accountModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">상점등록</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

    <?php
        $attributes = array(
            'name' => 'fadmin',
            'id' => 'fadmin',
            'onsubmit' => 'return admin_store_submit(this);'
        );
        echo form_open('admuser/store_auth', $attributes);
    ?>
                <div class="form-group">
    <?php
        $data = array(
            'name'  => 'ipt_storename',
            'id'    => 'ipt_storename',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;',
            'maxlength' => '20',
            'required' => '',
            'value' => '',
            'placeholder' => ' 상점아이디 '
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
    <?php
        $data = array(
            'name'  => 'ipt_url',
            'id'    => 'ipt_url',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;',
            'maxlength' => '50',
            'value' => '',
            'placeholder' => ' 상점URL Ex) http://www.google.com '
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                단문
    <?php
        $data = array(
            'name'  => 'ipt_sms1',
            'id'    => 'ipt_sms1',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '16.50',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                선거단문
    <?php
        $data = array(
            'name'  => 'ipt_sms2',
            'id'    => 'ipt_sms2',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '16.50',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                장문
    <?php
        $data = array(
            'name'  => 'ipt_lms1',
            'id'    => 'ipt_lms1',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '33.00',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                선거장문
    <?php
        $data = array(
            'name'  => 'ipt_lms2',
            'id'    => 'ipt_lms2',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '33.00',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                포토
    <?php
        $data = array(
            'name'  => 'ipt_mms1',
            'id'    => 'ipt_mms1',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '99.00',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                선거포토
    <?php
        $data = array(
            'name'  => 'ipt_mms2',
            'id'    => 'ipt_mms2',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '99.00',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                KAT
    <?php
        $data = array(
            'name'  => 'ipt_kat',
            'id'    => 'ipt_kat',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '11.00',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                KFT
    <?php
        $data = array(
            'name'  => 'ipt_kft',
            'id'    => 'ipt_kft',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '26.40',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                KFTM
    <?php
        $data = array(
            'name'  => 'ipt_kftm',
            'id'    => 'ipt_kftm',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '33.00',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                CH_SMS
    <?php
        $data = array(
            'name'  => 'ipt_ch_sms',
            'id'    => 'ipt_ch_sms',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '100',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                CH_LMS
    <?php
        $data = array(
            'name'  => 'ipt_ch_lms',
            'id'    => 'ipt_ch_lms',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '500',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                CH_MMS
    <?php
        $data = array(
            'name'  => 'ipt_ch_mms',
            'id'    => 'ipt_ch_mms',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '900',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                CH_KKO
    <?php
        $data = array(
            'name'  => 'ipt_ch_kko',
            'id'    => 'ipt_ch_kko',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '950',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                단문(계약금액)
    <?php
        $data = array(
            'name'  => 'ipt_contract_sms',
            'id'    => 'ipt_contract_sms',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '8.03',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                장문(계약금액)
    <?php
        $data = array(
            'name'  => 'ipt_contract_lms',
            'id'    => 'ipt_contract_.ms',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '26.07',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                포토(계약금액)
    <?php
        $data = array(
            'name'  => 'ipt_contract_mms',
            'id'    => 'ipt_contract_mms',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '52.80',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
                카카오(계약금액)
    <?php
        $data = array(
            'name'  => 'ipt_contract_kko',
            'id'    => 'ipt_contract_kko',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;float:right;width:78%;display:inline;',
            'maxlength' => '10',
            'required' => '',
            'value' => '0.00',
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group" style="padding-top:10px;padding-left:100px;">
    <?php
        echo form_radio('ipt_restrict_sending', '0', true);
        echo '충전금액 부족 시 발송 가능&nbsp;&nbsp;';
        echo form_radio('ipt_restrict_sending', '1', false);
        echo '충전금액 부족 시 발송 금지';
    ?>
                </div>
                <div class="form-group" style="padding-top:10px;padding-left:100px;">
    <?php
        echo form_radio('ipt_check_balance', '1', true);
        echo '정산업체&nbsp;&nbsp;';
        echo form_radio('ipt_check_balance', '0', false);
        echo '비정산업체';
    ?>
                </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-danger btn-sm" value="확인" ><span style="font-size:12px"></span>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><span style="font-size:12px">닫기</span></button>
            </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
var admin_store_submit = function () {
    if ($.trim($("input[name=ipt_storename]").val()) == '') {
        alert("상점아이디를 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_sms1]").val()) == '') {
        alert("단문을 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_sms2]").val()) == '') {
        alert("선거단문을 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_lms1]").val()) == '') {
        alert("장문을 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_lms2]").val()) == '') {
        alert("선거장문을 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_mms1]").val()) == '') {
        alert("포토를 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_mms2]").val()) == '') {
        alert("선거포토를 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_kat]").val()) == '') {
        alert("KAT 값을 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_kft]").val()) == '') {
        alert("KFT 값을 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_kftm]").val()) == '') {
        alert("KFTM 값을 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_ch_sms]").val()) == '') {
        alert("CH_SMS 값을 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_ch_lms]").val()) == '') {
        alert("CH_LMS 값을 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_ch_mms]").val()) == '') {
        alert("CH_MMS 값을 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_ch_kko]").val()) == '') {
        alert("CH_KKO 값을 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_contract_sms]").val()) == '') {
        alert("단문(계약금액) 값을 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_contract_lms]").val()) == '') {
        alert("장문(계약금액) 값을 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_contract_mms]").val()) == '') {
        alert("포토(계약금액) 값을 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_contract_kko]").val()) == '') {
        alert("카카오(계약금액) 값을 입력하세요.");
        return false;
    }
    return true;
}
</script>
