<body class="responsive is-mobile">
<div id="to_content"><a href="#container">본문 바로가기</a></div>

<?php
    $g_menu_flag = 'setting';
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
        <h1>공지사항</h1>
<div class="local_ov01 local_ov">&nbsp;</div>

<?php /* ?>
<div class="btn_add01 btn_add">
    <button id="myBtn">발신번호등록하기</button>
</div>
<?php */ ?>

<?php
$attributes = array(
    'name' => 'frmadmin',
    'id' => 'frmadmin',
    'onsubmit' => 'return fmemberlist_submit(this);'
);
echo form_open('admsetting/delete_bbs', $attributes);
?>
<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="삭제하기" onclick="document.pressed=this.value">
    <?php if ((int)$this->session->userdata('level') >= 5) { ?>
    <span class="btn_add01" style="padding-left:20px;">
        <a href="/admsetting/write_bbs">공지사항 작성하기</a>
    </span>
    <?php } ?>
</div>
<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    <tr>
        <th scope="col" id="mb_list_chk">
            <label for="chkall" class="sound_only">회원 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">공지번호</th>
        <th scope="col">제목</th>
        <th scope="col">내용</th>
        <th scope="col">등록일</th>
        <th scope="col">공지여부</th>
        <th scope="col">삭제여부</th>
        <th scope="col">비고</th>
    </tr>
    </thead>
    <tbody>
<?php
    $i = 0;
    foreach ($result as $row) {
?>
    <tr class="bg<?=(int)($i%2)?>">
        <td style="width:10px;text-align:center;">
            <input type="checkbox" name="chk[]" value="<?=$row->xid?>" id="chk_<?=$row->xid?>" />
        </td>
        <td class="td_date"><?=$row->xid?></td>
        <td style="width:150px; text-align:left;"><?=$row->subject?></td>
        <td style="width:200px; text-align:left;"><?=$row->body?></td>
        <td class="td_date"><?=mydate_format('y-m-d H:i', $row->add_date)?></td>
        <td class="td_date">
            <?=($row->status == '1' ? '공지' : '')?>
        <?php if ($row->status == '1') { ?>
            <br /><br /><a href="/admsetting/hide_bbs/<?=$row->xid?>">[공지내리기]</a><br />
        <?php } ?>
        </td>
        <td class="td_date"><?=($row->del_flag == '1' ? '삭제' : '정상')?></td>
        <td class="td_date">
            <a href="/admsetting/notice_bbs/<?=$row->xid?>">[공지하기]</a><br /><br />
            <a href="/admsetting/write_bbs/<?=$row->xid?>">[수정하기]</a>
        </td>
    </tr>
<?php
    }
?>
            </tbody>
    </table>
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

<!-- <p>실행시간 : 0.00087904930114746 -->

</body>
<script type="text/javascript">
var searchList = function() {
    if ($("input[name=ipt_date_from]").val().trim() == '') return false;
    if ($("input[name=ipt_date_to]").val().trim() == '') return false;
    return true;
}
</script>
