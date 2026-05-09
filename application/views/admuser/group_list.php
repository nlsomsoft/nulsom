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
        <h1>그룹관리</h1>
<div class="local_ov01 local_ov">전체목록 <?=number_format($total_rows)?> 건</div>

<?php
$attributes = array(
    'name' => 'frmadmin',
    'id' => 'frmadmin',
    'onsubmit' => 'return fmemberlist_submit(this);'
);
echo form_open('admuser/group_auth', $attributes);
?>
<div class="btn_list01 btn_list">
    <?php if ((int)$this->session->userdata('level') >= 5) { ?>
    <span class="btn_add01">
        <a href="#accountModal" data-toggle="modal">그룹등록하기</a>
    </span>
    <?php } ?>
</div>
<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    <tr>
        <th scope="col">PID</th>
        <th scope="col">그룹아이디</th>
        <th scope="col">그룹이름</th>
        <th scope="col">상점</th>
        <th scope="col">담당자아이디</th>
        <th scope="col">연락처</th>
        <th scope="col">연동방식</th>
        <th scope="col">등록일시</th>
    </tr>
    </thead>
    <tbody>
<?php
    $i = 0;
    foreach ($result as $row) {
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td class="td_num"><?=$row->groupno?></td>
        <td class="td_id"><?=$row->groupid?></td>
        <td class="td_id"><?=$row->group_name?></td>
        <td class="td_id"><?=$row->storename?></td>
        <td class="td_id"><?=$row->userid?></td>
        <td class="td_id"><?=phone_format($row->phone)?></td>
        <td class="td_id"><?=($row->type == '2' ? '도메인연동' : '유입경로연동')?></td>
        <td class="td_id"><?=mydate_format('Y-m-d',$row->add_date)?></td>
    </tr>
<?php
        $i ++;
    }
?>

            </tbody>
    </table>
</div>

<div><?=$this->pagination->create_links();?></div>
<?php /* ?>
<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="차단하기" onclick="document.pressed=this.value">
    <input type="submit" name="act_button" value="승인하기" onclick="document.pressed=this.value">
</div>
<?php */ ?>
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
                <h5 class="modal-title" id="exampleModalLabel">그룹등록</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
    <?php
        $attributes = array(
            'name' => 'fadmin',
            'id' => 'fadmin',
            'onsubmit' => 'return admin_group_submit(this);'
        );
        echo form_open('admuser/group_auth', $attributes);
    ?>
                <div class="form-group">
        <?php
            $options = array();
            foreach ($store_list as $row) {
                $options[$row->storeno] = $row->storename;
            }
            $js = 'class="form-control" style="font-family:dotum; font-size:12px;"';
            echo form_dropdown('ipt_storeno', $options, '', $js);
        ?>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="ipt_groupid" placeholder="* 그룹아이디" maxlength="20" style="font-family:dotum; font-size:12px;">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="ipt_group_name" placeholder="* 그룹이름" maxlength="20" style="font-family:dotum; font-size:12px;">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="ipt_userid" placeholder="* 담당자아이디" maxlength="20" style="font-family:dotum; font-size:12px;">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="ipt_phone" placeholder="연락처" maxlength="20" style="font-family:dotum; font-size:12px;">
                </div>
                <div class="form-group">
                    <?php
                        $options = array(
                            '1' => '유입경로연동',
                            '2' => '도메인연동',
                        );
                        $js = 'class="form-control" style="font-family:dotum; font-size:12px;"';
                        echo form_dropdown('ipt_type', $options, '', $js);
                    ?>
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
<script type="text/javascript">
var admin_group_submit = function () {
    var ipt_groupid = $.trim($("input[name=ipt_groupid]").val());
    if (ipt_groupid == '') {
        alert("그룹아이디를 입력하세요.");
        return false;
    }
    var ipt_group_name = $.trim($("input[name=ipt_group_name]").val());
    if (ipt_group_name == '') {
        alert("그룹이름을 입력하세요.");
        return false;
    }
    var ipt_userid = $.trim($("input[name=ipt_userid]").val());
    if (ipt_userid == '') {
        alert("담당자 아이디를 입력하세요.");
        return false;
    }
    var ipt_phone = $.trim($("input[name=ipt_phone]").val());
    if (ipt_phone != '') {
        var regType = /^[0-9-]*$/;
        if (!regType.test(ipt_phone)) {
            alert("연락처는 숫자만 입력하세요.");
            return false;
        }
    }
    return true;
}
</script>