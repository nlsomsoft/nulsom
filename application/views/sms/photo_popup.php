<!DOCTYPE html>
<html>
<head>
<title><?=BRAND?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
    include_once(VIEWPATH.'/templates/head.php');
?>
<link rel="stylesheet" href="/css/handsontable.css">
</head>

<body class="">

<!-- content start -->
<div>

<?php
    $attributes = array(
        'name' => 'frmPhotoPopup',
        'id' => 'frmPhotoPopup'
    );
    echo form_open_multipart('/sms/add_photo', $attributes);
?>

<table width="390px" border="0">
    <tr>
        <td style="padding-left:10px;background-color:#555;color:#fff;font-weight:bold;height:50px;">포토문자 이미지 등록하기</td>
    </tr>
    <tr>
        <td align="center">
        <div class="menu_tip" style="width:385px; text-align:left;">
        <p>
        - <span style="color:#FF6600">JPG 파일만 가능</span><br>
        - 이미지 크기 640 x 960 필셀에 최적화 됩니다.<br>
        - 640 x 960 초과 시 이미지가 손상될 수 있습니다.</p>
        </div>
        </td>
    </tr>
    <tr class="font-13">
      <td align="center">
        <div class="filebox bs3-primary" style="height:40px;">
            <input class="upload-name" id="upload_name" placeholder="파일을 등록하세요." disabled="disabled">
            <label for="photo_file">파일찾기</label>
            <input type="file" name="photo_file" id="photo_file" class="upload-hidden">
        </div>
     </td>
    </tr>

    <tr class="font-13">
      <td align="center">
            <div style="height:252px; line-height:252px; vertical-align:middle; border:1px solid #F1F1F1; width:385px;">
            <?php $image_path = ((is_array($photo_array) && $photo_array['image_path'] != '') ? $photo_array['image_path'] : '/images/img_up.png'); ?>
                <img src="<?=$image_path?>" style="width:auto;max-height:250px;" />
            </div>
     </td>
    </tr>
    <tr>
        <td align="center">
            <div class="btn1-group" style="text-align:center; margin-top:15px">
                <a id="export_string" class="btn1-orange btn1-space" onclick="addPhotoImage();"><div style="width:60px;text-align:center;">등록</div></a>
                <a id="close_popup" class="btn1-white btn1-space"><div style="width:60px;text-align:center;">닫기</div></a>
            </div>
        </td>
    </tr>
</table>
</form>
</div>

</td></tr></table>

<script type="text/javascript">
var addPhotoImage = function () {
    var file = $("#upload_name").val();
    if (file == "") {
        alert("JPG 이미지 파일을 등록 하세요.");
        return;
    }
    // 정규식을 사용하여 jpg,png,gif,bmp|tiff등 이미지파일의 확장자를 가진것을 추려낸다.
    var reg = /(.*?)\.(jpg)$/;
    if(!file.match(reg)) {
        alert("JPG 이미지 파일만 가능합니다.");
        return;
    }
    $("form#frmPhotoPopup").submit();
}
var loadImageAtPhoto = function (url) {
    $("#photo_image",opener.document).attr("src", url);
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

// 닫기
$("#close_popup").bind('click', function() {
    window.close();
});
</script>

<?php
    if ($this->session->userdata('csrf_sowkorea_name') != '') {
?>
<script type="text/javascript">
$(document).ready(function(){
    $(opener.location).attr("href","javascript:control_csrf('<?=$this->session->userdata('csrf_sowkorea_name')?>');");
});
</script>
<?php
        $this->session->set_userdata('csrf_sowkorea_name', '');
    }
?>

<?php if (is_array($photo_array) && $photo_array['image_path'] != '') { ?>
<script type="text/javascript">
$(document).ready(function(){
    loadImageAtPhoto('<?=$photo_array['image_path']?>');
});
</script>
<?php } ?>


<?php if ($this->session->flashdata('error_message')) { ?>
<script type="text/javascript">
$(document).ready(function(){
    alert("<?=$this->session->flashdata('error_message')?>");
});
</script>
<?php } ?>
<!-- content end -->
</body>
</html>

