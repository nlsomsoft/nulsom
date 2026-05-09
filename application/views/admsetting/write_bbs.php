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
        <h1>공지사항 작성</h1>
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
echo form_open('admsetting/add_bbs', $attributes);

$data = array(
    'xid' => $xid,
);
echo form_hidden($data);
?>

<div class="tbl_wrap tbl_head01">
    <table>
    <thead>
    </thead>
    <tbody>
        <tr>
            <td style="width:250px; text-align:center;">제목</td>
            <td><input type="text" name="subject" id="subject" value="<?=$result['subject']?>"  class="form-control" style="width:500px; font-size:12px;" minlength="1" maxlength="50" required="" /></td>
        </tr>
        <tr>
            <td style="width:250px; text-align:center;">내용</td>
            <td>
                <textarea style="width:500px; height:600px;font-size:12px;" id="body" name="body"><?=$result['body']?></textarea>
            </td>
        </tr>
        <tr><td colspan="2" style="text-align:center;"><input type="submit" class="btn btn-danger btn-sm" value="등록하기"></td></tr>
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
var fmemberlist_submit = function () {
    if ($('#subject').val() == '') {
        alert("제목을 입력하세요.");
        return false;
    }
    if ($('#body').val() == '') {
        alert("내용을 입력하세요.");
        return false;
    }
    return true;
}
</script>