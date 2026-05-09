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
        <h1>회원수정</h1>

<?php
$attributes = array(
    'name' => 'fmember',
    'id' => 'fmember',
    'onsubmit' => 'return fmember_submit(this);'
);
echo form_open('admuser/update', $attributes);
?>
<?php
$data = array(
    'ipt_storeno' => $result->storeno,
    'ipt_userno' => $result->userno,
    'ipt_user_type' => $result->user_type,
    'ipt_remove_date' => $result->remove_date,
);
echo form_hidden($data);
?>
<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption>회원수정</caption>
    <colgroup>
        <col class="grid_4">
        <col>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row"><label for="mb_id">아이디</label></th>
        <td style="font-weight:bold; color:#111;"><?=$result->userid?></td>
<?php
$data = array(
    'ipt_userid' => $result->userid,
);
echo form_hidden($data);
?>
        <th scope="row"><label for="birthday">생년월일</label></th>
        <td>
            <?=substr($auth_info['auth_birthday'], 0, 4)?>-<?=substr($auth_info['auth_birthday'], 4, 2)?>-<?=substr($auth_info['auth_birthday'], 6, 2)?> &nbsp;&nbsp;(<?=$auth_info['auth_agent']?>)
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="mb_name">이름(실명)</label></th>
        <td><?=$result->realname?></td>
        <th scope="row"><label for="mb_password">비밀번호</label></th>
        <td><div class="style_btn1" style="width:130px; margin:5px 0px;"><a href="#accountModal_pass" data-toggle="modal">비밀번호 변경하기</a></div></td>
    </tr>
<?php if ((int)$this->session->userdata('level') > 3) { ?>
    <tr>
        <th scope="row"><label for="store">상점</label></th>
        <td>
    <?php
        $options = array();
        foreach ($store_list as $row) {
            $options[$row->storeno] = $row->storename;
        }
        $js = 'style="width:110px;" class="frm_input"';
        echo form_dropdown('ipt_storeno', $options, $result->storeno, $js);
    ?>
        </td>
        <th scope="row">그룹</th>
        <td>
    <?php
        $options = array();
        $options['0'] = '그룹없음';
        foreach ($group_list as $row) {
            $options[$row->groupno] = $row->groupid;
        }
        $js = 'style="width:110px;" class="frm_input"';
        echo form_dropdown('ipt_groupno', $options, $result->groupno, $js);
    ?>
        </td>
    </tr>
<?php } else { ?>
    <input type="hidden" name="ipt_storeno" value="<?=$result->storeno?>" />
    <input type="hidden" name="ipt_groupno" value="<?=$result->groupno?>" />
<?php } ?>
    <tr>
        <th scope="row"><label for="mb_level">레벨</label></th>
        <td>
    <?php
        if ($this->session->userdata('level') == '9') {
            $options = array(
                '9' => '최고어드민',
                '5' => '어드민',
                '3' => '관리자',
                '1' => '회원'
            );
        } else if ($this->session->userdata('level') == '5') {
            $options = array(
                '5' => '어드민',
                '3' => '관리자',
                '1' => '회원',
            );
        } else if ($this->session->userdata('level') == '3') {
            $options = array(
                '3' => '관리자',
                '1' => '회원',
            );
        }
        $js = 'style="width:110px;" class="frm_input"';
        echo form_dropdown('ipt_level', $options, $result->level, $js);
    ?>
        </td>
        <th scope="row">회원유형</th>
        <td><?=($result->user_type == '1' ? '개인회원' : '사업자회원')?></td>
    </tr>
        <tr>
        <th scope="row"><label for="mb_email">E-mail<strong class="sound_only">필수</strong></label></th>
        <td>
    <?php
        $data = array(
            'name'  => 'ipt_email',
            'id'    => 'ipt_email',
            'class' => 'frm_input',
            'maxlength' => '50',
            'size' => '30',
            'value' => $result->email,
            'placeholder' => ''
        );
        echo form_input($data);
    ?>
        </td>
        <th scope="row"><label for="mb_homepage">보유현금</label></th>
        <td><?=number_format($result->cash).' 원'?></td>
    </tr>
    <tr>
        <th scope="row"><label for="mb_hp">휴대폰번호</label></th>
        <td>
    <?php
        $data = array(
            'name'  => 'ipt_mobile',
            'id'    => 'ipt_mobile',
            'class' => 'frm_input',
            'maxlength' => '13',
            'size' => '15',
            'value' => phone_format($result->mobile),
            'placeholder' => ''
        );
        echo form_input($data);
    ?>
        </td>
        <th scope="row"><label for="mb_tel">전화번호</label></th>
        <td>
    <?php
        $data = array(
            'name'  => 'ipt_phone',
            'id'    => 'ipt_phone',
            'class' => 'frm_input',
            'maxlength' => '13',
            'size' => '15',
            'value' => phone_format($result->phone),
            'placeholder' => ''
        );
        echo form_input($data);
    ?>
        </td>
    </tr>
    <tr>
        <th scope="row">080수신번호</th>
        <td colspan="3" style="font-weight:bold; color:#111;">
    <?php
        global $PHONE_080_LIST;
        if (is_array($PHONE_080_LIST)) {
            $js = 'style="width:110px;" class="frm_input"';
            echo form_dropdown('ipt_phone_080', $PHONE_080_LIST, $result->phone_080, $js);
        } else {
            $data = array(
                'name'  => 'ipt_phone_080',
                'id'    => 'ipt_phone_080',
                'class' => 'frm_input',
                'maxlength' => '13',
                'size' => '15',
                'value' => phone_format($result->phone_080),
                'placeholder' => ''
            );
            echo form_input($data);
        }
    ?>
        </td>
    </tr>
    <tr>
        <th scope="row">인증번호</th>
        <td><?=phone_format($auth_info['auth_phoneno'])?></td>
        <th scope="row">발신번호</th>
        <td>
            <div class="style_btn1" style="width:130px; margin:5px 0px;"><a href="#accountModal" data-toggle="modal">발신번호 등록하기</a></div>
    <?php
        $c_cnt = 0;
        foreach ($user_callback as $key => $val) {
            $cert_type = '';
            if ($val['cert_type'] == '1') $cert_type = '(휴대폰인증)';
            else if ($val['cert_type'] == '2') $cert_type = '(통신가입증명원)';
            else if ($val['cert_type'] == '3') $cert_type = '(관리자등록)';

            $status = '';
            if ($val['status'] == '4') $status = '(미사용)';
            else if ($val['status'] == '3') $status = '(승인)';
            else if ($val['status'] == '2') $status = '<span style="color:#396dba;">(차단)</span>';
            else if ($val['status'] == '1') $status = '<span style="color:#e8180c;">(대기)</span>';

            $callback = phone_format($val['callback']);
            if ($c_cnt) echo '<br />';
            echo ($callback.'&nbsp;&nbsp;'.$cert_type.'&nbsp;&nbsp;'.$status);
            $c_cnt ++;
        }
    ?>
        </td>
    </tr>
    <tr>
        <th scope="row">가입 IP</th>
        <td><?=$result->register_ip?>&nbsp;</td>
        <th scope="row">로그인 IP</th>
        <td><?=$result->ip?>&nbsp;</td>
    </tr>

    <tr>
        <th scope="row">회원가입일</th>
        <td><?=$result->add_date?></td>
        <th scope="row">최근접속일</th>
        <td><?=$result->login_date?></td>
    </tr>
    <tr>
        <th scope="row">결제방식</th>
        <td>
    <?php
        $data = array(
            'name' => 'rd_paytype_value',
            'id' => 'rd_paytype_value',
            'value' => '0',
            'checked' => ($result->pay_type == '0' ? true : ''),
        );
        echo form_radio($data);
        echo '선불&nbsp;&nbsp;';

        $data = array(
            'name' => 'rd_paytype_value',
            'id' => 'rd_paytype_value',
            'value' => '1',
            'checked' => ($result->pay_type == '1' ? true : ''),
        );
        echo form_radio($data);
        echo '후불';
    ?>
        </td>
        <th scope="row">PG사용</th>
        <td>
    <?php
        $data = array(
            'name' => 'rd_pg_value',
            'id' => 'rd_pg_value',
            'value' => 'Y',
            'checked' => ($result->pg == 'Y' ? true : ''),
        );
        echo form_radio($data);
        echo '사용&nbsp;&nbsp;';

        $data = array(
            'name' => 'rd_pg_value',
            'id' => 'rd_pg_value',
            'value' => 'N',
            'checked' => ($result->pg == 'N' ? true : ''),
        );
        echo form_radio($data);
        echo '미사용';
    ?>
        </td>
    </tr>
<?php /* ?>
    <tr>
        <th scope="row">블랙리스트</th>
        <td colspan="3">
    <?php
        $data = array(
            'name' => 'rd_blacklist_value',
            'id' => 'rd_blacklist_value',
            'value' => '0',
            'checked' => ($result->black_list == '0' ? true : ''),
        );
        echo form_radio($data);
        echo '수신거부 미적용&nbsp;&nbsp;';

        $data = array(
            'name' => 'rd_blacklist_value',
            'id' => 'rd_blacklist_value',
            'value' => '1',
            'checked' => ($result->black_list == '1' ? true : ''),
        );
        echo form_radio($data);
        echo '수신거부 적용';
    ?>
        <span style="padding-left:50px;">* 수신거부 적용 선택 시 블랙리스트 제외하고 발송</span>
        </td>
    </tr>
<?php */ ?>
    <tr>
        <th scope="row">발송방식</th>
        <td colspan="3">
    <?php
        $data = array(
            'name' => 'rd_adtype_value',
            'id' => 'rd_adtype_value',
            'value' => '0',
            'checked' => ($result->ad_type == '0' ? true : ''),
        );
        echo form_radio($data);
        echo '일반문자&nbsp;&nbsp;';

        $data = array(
            'name' => 'rd_adtype_value',
            'id' => 'rd_adtype_value',
            'value' => '1',
            'checked' => ($result->ad_type == '1' ? true : ''),
        );
        echo form_radio($data);
        echo '광고문자';
    ?>
        <span style="padding-left:118px;">* 광고문자 선택 시 광고문자 카테고리만 이용 가능 (광고문구 무조건 표기)</span>
        </td>
    </tr>
    <tr>
        <th scope="row">발신자명</th>
        <td colspan="3">
    <?php
        $data = array(
            'name'  => 'ipt_ad_title',
            'id'    => 'ipt_ad_title',
            'class' => 'frm_input',
            'maxlength' => '50',
            'size' => '30',
            'value' => $result->ad_title,
            'placeholder' => ''
        );
        echo form_input($data);
    ?>
        <span style="padding-left:52px;">* 발신자명 입력 시 문자발송 상단에 자동 표기</span>
        </td>
    </tr>
<?php if (MOBILE_COMPANY_TEST == 'Y') { ?>
    <tr>
        <th scope="row">이통사테스트전송</th>
        <td colspan="3">
    <?php
        $data = array(
            'name' => 'rd_telecomval_value',
            'id' => 'rd_telecomval_value',
            'value' => '0',
            'checked' => ($result->telecom_val == '0' ? true : ''),
        );
        echo form_radio($data);
        echo '사용 불가능&nbsp;&nbsp;';

        $data = array(
            'name' => 'rd_telecomval_value',
            'id' => 'rd_telecomval_value',
            'value' => '1',
            'checked' => ($result->telecom_val == '1' ? true : ''),
        );
        echo form_radio($data);
        echo '사용 가능';
    ?>
        <span style="padding-left:98px;">* 사용 불가능이면 이통사테스트 메뉴가 사라짐</span>
        </td>
    </tr>
<?php } ?>
<?php if (CONFIRM_SMS_YN == 'Y') { ?>
    <tr>
        <th scope="row">관리자인증</th>
        <td colspan="3">
    <?php
        $data = array(
            'name' => 'rd_confirmflag_value',
            'id' => 'rd_confirmflag_value',
            'value' => '0',
            'checked' => ($result->confirm_flag == '0' ? true : ''),
        );
        echo form_radio($data);
        echo '인증제외&nbsp;&nbsp;';

        $data = array(
            'name' => 'rd_confirmflag_value',
            'id' => 'rd_confirmflag_value',
            'value' => '1',
            'checked' => ($result->confirm_flag == '1' ? true : ''),
        );
        echo form_radio($data);
        echo '인증필요&nbsp;&nbsp;&nbsp;&nbsp;';
    ?>
        <span style="padding-left:95px;">* 인증필요 선택 시 발송관리 > 관리자인증, 어드민 확인 시 정상 발송</span>
        </td>
    </tr>
<?php } ?>
<?php if ($result->user_type == '2') { ?>
    <tr>
        <th scope="row">사업자명</th>
        <td>
    <?php
        $data = array(
            'name'  => 'ipt_com_name',
            'id'    => 'ipt_com_name',
            'class' => 'frm_input',
            'maxlength' => '20',
            'size' => '30',
            'required' => '',
            'readonly' => '',
            'value' => $result->com_name,
            'placeholder' => ''
        );
        echo form_input($data);
    ?>
        </td>
        <th scope="row">사업자등록번호</th>
        <td>
    <?php
        $data = array(
            'name'  => 'ipt_com_number',
            'id'    => 'ipt_com_number',
            'class' => 'frm_input',
            'maxlength' => '20',
            'size' => '30',
            'required' => '',
            'readonly' => '',
            'value' => $result->com_number,
            'placeholder' => ''
        );
        echo form_input($data);
    ?>
        </td>
    </tr>
<?php } ?>

<?php
    $s_options = array('0' => '채널없음');
    $l_options = array('0' => '채널없음');
    $m_options = array('0' => '채널없음');
    $k_options = array('0' => '채널없음');
    foreach ($channel as $key => $val) {
        foreach ($val as $channel_val => $channel_exp) {
            if ($key == 'sms') $s_options[$channel_val] = $channel_exp;
            else if ($key == 'lms') $l_options[$channel_val] = $channel_exp;
            else if ($key == 'mms') $m_options[$channel_val] = $channel_exp;
            else if ($key == 'kakao') $k_options[$channel_val] = $channel_exp;
        }
    }
    $js = 'style="width:110px;" class="frm_input"';
?>

    <tr>
        <th scope="row">단문</th>
        <td>
    <?php
        $data = array(
            'name'  => 'ipt_sms1',
            'id'    => 'ipt_sms1',
            'class' => 'frm_input',
            'maxlength' => '13',
            'size' => '15',
            'required' => '',
            'value' => $result->sms1,
            'placeholder' => ''
        );
        echo form_input($data);
    ?> 원
        </td>
        <th scope="row">단문채널</th>
        <td>
    <?php
        if ((int)$this->session->userdata('level') >= 5) {
            echo form_dropdown('ipt_ch_sms', $s_options, $result->ch_sms, $js);
        } else {
            $data = array(
                'ipt_ch_sms' => $result->ch_sms,
            );
            echo form_hidden($data);
            echo ($s_options[$result->ch_sms]);
        }
    ?>
        </td>
    </tr>
    <tr>
        <th scope="row">장문</th>
        <td>
    <?php
        $data = array(
            'name'  => 'ipt_lms1',
            'id'    => 'ipt_lms1',
            'class' => 'frm_input',
            'maxlength' => '13',
            'size' => '15',
            'required' => '',
            'value' => $result->lms1,
            'placeholder' => ''
        );
        echo form_input($data);
    ?> 원
        </td>
        <th scope="row">장문채널</th>
        <td>
    <?php
        if ((int)$this->session->userdata('level') >= 5) {
            echo form_dropdown('ipt_ch_lms', $l_options, $result->ch_lms, $js);
        } else {
            $data = array(
                'ipt_ch_lms' => $result->ch_lms,
            );
            echo form_hidden($data);
            echo ($l_options[$result->ch_lms]);
        }
    ?>
        </td>
    </tr>
    <tr>
        <th scope="row">포토</th>
        <td>
    <?php
        $data = array(
            'name'  => 'ipt_mms1',
            'id'    => 'ipt_mms1',
            'class' => 'frm_input',
            'maxlength' => '13',
            'size' => '15',
            'required' => '',
            'value' => $result->mms1,
            'placeholder' => ''
        );
        echo form_input($data);
    ?> 원
        </td>
        <th scope="row">포토채널</th>
        <td>
    <?php
        if ((int)$this->session->userdata('level') >= 5) {
            echo form_dropdown('ipt_ch_mms', $m_options, $result->ch_mms, $js);
        } else {
            $data = array(
                'ipt_ch_mms' => $result->ch_mms,
            );
            echo form_hidden($data);
            echo ($m_options[$result->ch_mms]);
        }
    ?>
        </td>
    </tr>

    <tr>
        <th scope="row">실패건환불</th>
        <td colspan="3">
<?php
    $data = array(
        'name' => 'chk_refund_value',
        'id' => 'chk_refund_value',
        'value' => '1',
        'checked' => ($result->refund_val == '1' ? true : ''),
    );
    echo form_checkbox($data);
?> 환불 미적용
        </td>
    </tr>

<?php if ($result->state != '2') { ?>
    <tr>
        <th scope="row">탈퇴</th>
        <td colspan="3">
<?php
    $data = array(
        'name' => 'chk_deny_value',
        'id' => 'chk_deny_value',
        'value' => '1',
        'checked' => '',
    );
    echo form_checkbox($data);
?> 탈퇴하기
        </td>
    </tr>

    <tr>
        <th scope="row">차단</th>
        <td>
<?php
    if ($result->state == '0') {
        $data = array(
            'name' => 'chk_block_value',
            'id' => 'chk_block_value',
            'value' => '1',
            'checked' => '',
        );
    } else {
        $data = array(
            'name' => 'chk_unblock_value',
            'id' => 'chk_unblock_value',
            'value' => '1',
            'checked' => '',
        );
    }
    echo form_checkbox($data);
?> <?=($result->state != '1' ? '차단하기' : '차단 해제하기')?>
        </td>
        <th scope="row"><label for="mb_leave_date">발송제한</label></th>
        <td>
<?php
if ($result->state != '1') {
    if ($result->state == '0') {
        $data = array(
            'name' => 'chk_sendblock_value',
            'id' => 'chk_sendblock_value',
            'value' => '1',
            'checked' => '',
        );
    } else {
        $data = array(
            'name' => 'chk_unsendblock_value',
            'id' => 'chk_unsendblock_value',
            'value' => '1',
            'checked' => '',
        );
    }
    echo form_checkbox($data);
?> <?=($result->state != '3' ? '제한하기' : '제한 해제하기')?>
<?php } ?>&nbsp;&nbsp;* 로그인 가능, 발송은 불가
        </td>
    </tr>
<?php } ?>
<?php
    $state = '';
    if ($result->state == '0') $state = '정상상태';
    else if ($result->state == '1') $state = '차단상태';
    else if ($result->state == '2') $state = '탈퇴상태';
    else if ($result->state == '3') $state = '정상상태 (발송제한)';
?>
    <tr>
        <th scope="row">상태</th>
        <td colspan="3" style="font-weight:bold; color:#111;"><?=$state?></td>
    </tr>
<?php if ((int)$this->session->userdata('level') > 3) { ?>
    <tr>
        <th scope="row">제재내역</th>
        <td colspan="3" style="font-weight:bold; color:#111;">
    <?php
        //type :1:차단, 2:탈퇴, 3:발송제한
        //ban  :1:제재 0:해제
        foreach ($ban_list as $val) {
            $ban_msg = '';
            if ($val->type == '1') $ban_msg .= '차단';
            else if ($val->type == '2') $ban_msg .= '탈퇴';
            else if ($val->type == '3') $ban_msg .= '발송제한';

            if ($val->ban == '0') $ban_msg .= ' (해제)';
            else if ($val->ban == '1') $ban_msg .= ' (제재)';

            $ban_msg .= ' '.$val->add_date;
            echo ($ban_msg).'<br />';
        }
    ?>
        &nbsp;</td>
    </tr>
<?php } ?>


<?php if ((int)$this->session->userdata('level') == 9 || (int)$this->session->userdata('level') == 5) { ?>
    <tr>
        <th scope="row">메모 (어드민)</th>
        <td colspan="3" style="font-weight:bold; color:#111;">
<?php
$data = array(
    'name'        => 'memo5',
    'id'          => 'memo5',
    'value'       => $result->memo5,
    'rows'        => '4',
    'cols'        => '10',
);
echo form_textarea($data);
?>
        </td>
    </tr>
<?php } ?>
<?php if ((int)$this->session->userdata('level') == 9 || (int)$this->session->userdata('level') == 3) { ?>
    <tr>
        <th scope="row">메모 (관리자)</th>
        <td colspan="3" style="font-weight:bold; color:#111;">
<?php
$data = array(
    'name'        => 'memo3',
    'id'          => 'memo3',
    'value'       => $result->memo3,
    'rows'        => '4',
    'cols'        => '10',
);
echo form_textarea($data);
?>
        </td>
    </tr>
<?php } ?>

    </tbody>
    </table>
</div>

<div class="btn_confirm01 btn_confirm">
<?php if ((int)$this->session->userdata('level') > 3) { ?>
    <input type="submit" value="확인" class="btn_submit" accesskey='s'>
<?php } ?>
    <a href="/admuser/users">목록</a>
</div>
</form>
        <noscript>
            <p>
                귀하께서 사용하시는 브라우저는 현재 <strong>자바스크립트를 사용하지 않음</strong>으로 설정되어 있습니다.<br>
                <strong>자바스크립트를 사용하지 않음</strong>으로 설정하신 경우는 수정이나 삭제시 별도의 경고창이 나오지 않으므로 이점 주의하시기 바랍니다.
            </p>
        </noscript>

    </div>
</div>


<!-- The Modal Start -->
<div class="modal fade" id="accountModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">발신번호등록</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

    <?php
        $attributes = array(
            'name' => 'fadmin',
            'id' => 'fadmin',
            'onsubmit' => 'return admin_callback_submit(this);'
        );
        echo form_open('admuser/callback', $attributes);
    ?>
                <label class="btn btn-sm">
                <input type="radio" name="rd_cert_type" value="3" /> <span style="font-family:dotum; font-size:12px">어드민등록</span>
                </label>
                <label class="btn btn-sm">
                <input type="radio" name="rd_cert_type" value="2" checked="checked" /> <span style="font-family:dotum; font-size:12px">통신가입증명원</span>
                </label>
                <div class="form-group">
                <input type="text" class="form-control" name="ipt_userid" placeholder="(필수) 아이디" maxlength="20" style="font-family:dotum; font-size:12px;" value="<?=$result->userid?>" readonly />
                </div>
                <div class="form-group">
                <input type="text" class="form-control" name="ipt_callback" placeholder="(필수) 발신번호" maxlength="13" style="font-family:dotum; font-size:12px;" />
                </div>
                <div class="form-group">
                <input type="text" class="form-control" name="ipt_memo" placeholder="(선택) 관리자 메모" maxlength="50" style="font-family:dotum; font-size:12px;" />
                </div>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-danger btn-sm" value="확인" ><span style="font-size:12px"></span>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><span style="font-size:12px">닫기</span></button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- The Modal Start -->
<div class="modal fade" id="accountModal_pass">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">비밀번호변경</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

    <?php
        $attributes = array(
            'name' => 'fadmin1',
            'id' => 'fadmin1',
            'onsubmit' => 'return admin_passwd_submit(this);'
        );
        echo form_open('admuser/newpasswd', $attributes);

        $data = array(
            'ipt_pass_storeno' => $result->storeno,
            'ipt_pass_groupno' => $result->groupno,
            'ipt_pass_userno' => $result->userno,
            'ipt_pass_userid' => $result->userid,
        );
        echo form_hidden($data);
    ?>
                <div class="form-group">
                    비밀번호 : <input type="password" class="form-control" name="ipt_password" id="ipt_password" maxlength="30" style="font-family:dotum; font-size:12px;" value="" /> </div>
                <div>
                    비밀번호 확인 : <input type="password" class="form-control" name="ipt_repassword" id="ipt_repassword" maxlength="30" style="font-family:dotum; font-size:12px;" value="" />
                </div>
                <div style="padding-top:10px;">* 비밀번호는 숫자 + 영문자 + 특수문자 조합으로 8자리 이상 사용</div>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-danger btn-sm" value="변경하기" ><span style="font-size:12px"></span>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><span style="font-size:12px">닫기</span></button>
            </div>
            </form>
        </div>
    </div>
</div>

</body>

<script type="text/javascript">
var fmember_submit = function (f) {
    if ($("input[name=ipt_user_type]").val() == '2') {
        var ipt_com_name = $.trim($('#ipt_com_name').val());
        var ipt_com_number = $.trim($('#ipt_com_number').val());
    }
    // var ipt_password = $.trim($('#ipt_password').val());
    // if (ipt_password != '') {
    //     if (checkPassword(ipt_userid,ipt_password) == false) return false;
    // }

    var ipt_mobile = $.trim($("input[name=ipt_mobile]").val());
    if (ipt_mobile != '') {
        var regType = /^[0-9-]*$/;
        if (!regType.test(ipt_mobile)) {
            alert("휴대폰은 숫자만 입력하세요.");
            return false;
        }
    }
    var ipt_phone = $.trim($("input[name=ipt_phone]").val());
    if (ipt_phone != '') {
        var regType = /^[0-9-]*$/;
        if (!regType.test(ipt_phone)) {
            alert("전화번호는 숫자만 입력하세요.");
            return false;
        }
    }
    var ipt_email = $.trim($('#ipt_email').val());
    if (ipt_email != '') {
        if (!validateEmail(ipt_email)) {
            alert("올바른 이메일 주소를 입력하세요.");
            $('#email').focus();
            return false;
        }
    }
    var ipt_phone_080 = $.trim($("input[name=ipt_phone_080]").val());
    if (ipt_phone_080 != '') {
        var regType = /^[0-9-]*$/;
        if (!regType.test(ipt_phone_080)) {
            alert("080 수신번호는 숫자만 입력하세요.");
            return false;
        }
    }

    var ipt_level = $("select[name=ipt_level]").val();
    if (ipt_level == "3") {
        var ipt_groupno = $("select[name=ipt_groupno]").val();
        if (ipt_groupno == "0") {
            alert("관리자 레벨은 그룹을 지정하셔야 합니다.");
            return false;
        }
    }
    return true;
}
var checkPassword = function (id,password) {
    if(!/^(?=.*[a-zA-Z])(?=.*[!@#$%^*+=-])(?=.*[0-9]).{8,25}$/.test(password)){
        alert('숫자+영문자+특수문자 조합으로 8자리 이상 사용해야 합니다.');
        $('#password').val('').focus();
        return false;
    }
    var checkNumber = password.search(/[0-9]/g);
    var checkEnglish = password.search(/[a-z]/ig);
    if(checkNumber < 0 || checkEnglish < 0){
        alert("숫자와 영문자를 혼용하여야 합니다.");
        $('#password').val('').focus();
        return false;
    }
    if(/(\w)\1\1\1/.test(password)){
        alert('같은 문자를 4번 이상 사용하실 수 없습니다.');
        $('#password').val('').focus();
        return false;
    }

    if(password.search(id) > -1){
        alert("비밀번호에 아이디가 포함되었습니다.");
        $('#password').val('').focus();
        return false;
    }
    return true;
}
// function validateEmail(email) {
var validateEmail = function(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}
var admin_callback_submit = function () {
    if ($("input:radio[name=rd_cert_type]").is(':checked') == false) {
        alert("등록 방식을 선택하세요.");
        return false;
    }
    var ipt_userid = $.trim($("input[name=ipt_userid]").val());
    if (ipt_userid == '') {
        alert("아이디를 입력하세요.");
        return false;
    }
    var ipt_callback = $.trim($("input[name=ipt_callback]").val());
    if (ipt_callback == '') {
        alert("발신번호를 입력하세요.");
        return false;
    }
    var regType = /^[0-9-]*$/;
    if (!regType.test(ipt_callback)) {
        alert("발신번호는 숫자만 입력하세요.");
        return false;
    }
    // var ipt_name = $.trim($("input[name=ipt_name]").val());
    // if (ipt_name == '') {
    //     alert("메모를 입력하세요.");
    //     return false;
    // }
    return true;
}
var admin_passwd_submit = function () {
    var ipt_userid = $.trim($("input[name=ipt_userid]").val());
    if (ipt_userid == '') {
        alert("아이디를 입력하세요.");
        return false;
    }
    var ipt_password = $.trim($('#ipt_password').val());
    if (ipt_password == '') {
        alert("비밀번호를 입력하세요.");
        return false;
    }
    var ipt_repassword = $.trim($('#ipt_repassword').val());
    if (ipt_repassword == '') {
        alert("비밀번호 확인을 입력하세요.");
        return false;
    }
    if (ipt_password != ipt_repassword) {
        alert("비밀번호가 일치하지 않습니다.");
        return false;
    }
    if (checkPassword(ipt_userid,ipt_password) == false) return false;
    return true;
}
</script>