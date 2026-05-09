<body class="responsive is-mobile">
<div id="to_content"><a href="#container">본문 바로가기</a></div>

<?php
    $g_menu_flag = 'bill';
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
        <h1>사용료 (입금)</h1>
<div class="local_ov01 local_ov">전체목록 <?=number_format($total_rows)?> 건</div>

<?php if ((int)$this->session->userdata('level') > 5) { ?>
<div class="btn_add01 btn_add">
    <a href="#accountModal" data-toggle="modal">사용료 등록하기</a>
</div>
<?php } ?>

<?php
$attributes = array(
    'name' => 'frmadmin',
    'id' => 'frmadmin',
    'onsubmit' => 'return fmemberlist_submit(this);'
);
echo form_open('admbill/adm_deposit', $attributes);
?>

<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    <tr>
        <th scope="col">No</th>
        <th scope="col">상점아이디</th>
        <th scope="col">입금금액</th>
        <th scope="col">입금일자</th>
        <th scope="col">메모</th>
    </tr>
    </thead>
    <tbody>
<?php
    $seq = (int)($offset + 1);
    $i = 0;
    foreach ($result as $row) {
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td class="td_num"><?=($seq++)?></td>
        <td class="td_num"><?=$row->storename?></td>
        <td class="td_num"><?=number_format($row->amount,2)?></td>
        <td class="td_num"><?=mydate_format('Y-m-d',$row->reg_time)?></td>
        <td class="td_num"><?=$row->memo?></td>
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
                <h5 class="modal-title" id="exampleModalLabel">사용료 등록하기</h5>
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
        echo form_open('admbill/add_deposit', $attributes);
    ?>
                <div class="form-group">
    <?php
        $options = array();
        foreach ($store_list as $val) {
            if (!$val->storeno) continue;
            $options[$val->storeno] = $val->storename;
        }
        $js = 'class="form-control" style="font-size:12px;"';
        echo form_dropdown('ipt_storeno', $options, '', $js);
    ?>
                </div>
                <div class="form-group">
    <?php
        $data = array(
            'name'  => 'ipt_amount',
            'id'    => 'ipt_amount',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;',
            'maxlength' => '20',
            'required' => '',
            'value' => '',
            'placeholder' => ' 입금금액 '
        );
        echo form_input($data);
    ?>
                </div>
                <div class="form-group">
    <?php
        $data = array(
            'name'  => 'ipt_memo',
            'id'    => 'ipt_memo',
            'class' => 'form-control',
            'style' => 'font-family:dotum; font-size:12px;',
            'maxlength' => '50',
            'required' => '',
            'value' => '',
            'placeholder' => ' 메모 '
        );
        echo form_input($data);
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
var admin_store_submit = function () {
    if ($.trim($("input[name=ipt_amount]").val()) == '') {
        alert("입금금액을 입력하세요.");
        return false;
    }
    if ($.trim($("input[name=ipt_memo]").val()) == '') {
        alert("메모를 입력하세요.");
        return false;
    }
    return true;
}
</script>
