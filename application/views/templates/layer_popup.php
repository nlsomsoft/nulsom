
<?php
    $notice['body'] = '공지사항<br /><br />해당 서비스는 2022년 12월 1일 이용이 종료되었음을 알립니다.<br /><br /><br />';
?>

<?php if ($notice['body'] && get_cookie('main_popup_yn') != 'N') { ?>
<style>
#mask {
    position: absolute;
    left: 0;
    top: 0;
    z-index: 999;
    background-color: #000000;
    display: none; }

.layerpop {
    display: none;
    z-index: 1000;
    border: 1px solid #ccc;
    background: #fff;
    cursor: move; }

.layerpop_area .content {
    width: 96%;
    margin: 2%;
    color: #828282; }
/*-- POPUP common style E --*/
</style>

<script src="https://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<script type="text/javascript">
function wrapWindowByMask() {
    //화면의 높이와 너비를 구한다.
    var maskHeight = $(document).height();
    var maskWidth = $(window).width();

    //마스크의 높이와 너비를 화면 것으로 만들어 전체 화면을 채운다.
    $('#mask').css({
        'width' : maskWidth,
        'height' : maskHeight
    });

    //애니메이션 효과
    //$('#mask').fadeIn(1000);
    $('#mask').fadeTo("slow", 0.5);
}

function popupOpen() {
    $('.layerpop').css("position", "absolute");
    //영역 가운에데 레이어를 뛰우기 위해 위치 계산
    $('.layerpop').css("top",(($(window).height() - $('.layerpop').outerHeight()) / 2) + $(window).scrollTop());
    $('.layerpop').css("left",(($(window).width() - $('.layerpop').outerWidth()) / 2) + $(window).scrollLeft());
    // $('.layerpop').draggable();
    $('#layerbox').show();
}

function popupClose(arv) {
    if (arv == '1') {
        writeCookie("main_popup_yn","N",1);
    }
    $('#layerbox').hide();
    $('#mask').hide();
}

function goDetail() {
    /*팝업 오픈전 별도의 작업이 있을경우 구현*/
    popupOpen(); //레이어 팝업창 오픈
    wrapWindowByMask(); //화면 마스크 효과
}

$( document ).ready(function() {
    goDetail();
});
</script>

<div id="layerbox" class="layerpop">
    <div class="content">

        <div class="layer_bg_inner">
            <div style="padding-left:15px"><strong><?=$notice['subject']?></strong></div>
            <div style="margin:10px 20px;">
            <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td><?=nl2br($notice['body'])?></td>
            </tr>
            </table>
            </div>
        </div>

    </div>
    <div>
        <table border="0" width="100%">
            <tr style="background-color: #e0e2e2;height:40px;">
            <td width="80%" style="padding-left:10px;"><a href="javascript:popupClose('1');"><div>[ 하루동안 열지 않기 ]</div></a></td>
            <td width="20%" style="text-align:right"><a href="javascript:popupClose('0');"><div style="padding-right:20px;">[ 닫기 ]</div></a></td>
            </tr>
        </table>
    </div>
</div>
<?php } ?>
<?php if ($g_layer_popup_flag == 'elect_popup' && get_cookie('elect_popup_yn') != 'N') { ?>
<style>
#mask {
    position: absolute;
    left: 0;
    top: 0;
    z-index: 999;
    background-color: #000000;
    display: none; }

.layerpop {
    display: none;
    z-index: 1000;
    border: 1px solid #ccc;
    background: #fff;
    cursor: move; }

.layerpop_area .content {
    width: 96%;
    margin: 2%;
    color: #828282; }
/*-- POPUP common style E --*/
</style>

<script src="http://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<script type="text/javascript">
function wrapWindowByMask() {
    //화면의 높이와 너비를 구한다.
    var maskHeight = $(document).height();
    var maskWidth = $(window).width();

    //마스크의 높이와 너비를 화면 것으로 만들어 전체 화면을 채운다.
    $('#mask').css({
        'width' : maskWidth,
        'height' : maskHeight
    });

    //애니메이션 효과
    //$('#mask').fadeIn(1000);
    $('#mask').fadeTo("slow", 0.5);
}

function popupOpen() {
    $('.layerpop').css("position", "absolute");
    //영역 가운에데 레이어를 뛰우기 위해 위치 계산
    $('.layerpop').css("top",(($(window).height() - $('.layerpop').outerHeight()) / 2) + $(window).scrollTop());
    $('.layerpop').css("left",(($(window).width() - $('.layerpop').outerWidth()) / 2) + $(window).scrollLeft());
    // $('.layerpop').draggable();
    $('#layerbox').show();
}

function popupClose(arv) {
    if (arv == '1') {
        writeCookie("elect_popup_yn","N",1);
    }
    $('#layerbox').hide();
    $('#mask').hide();
}

function goDetail() {
    /*팝업 오픈전 별도의 작업이 있을경우 구현*/
    popupOpen(); //레이어 팝업창 오픈
    wrapWindowByMask(); //화면 마스크 효과
}

$( document ).ready(function() {
    goDetail();
});
</script>

<div id="layerbox" class="layerpop">
    <div class="content"><img src="/images/elect_info1.jpg"></div>
    <div>
        <table border="0" width="100%">
            <tr style="background-color: #e0e2e2;height:40px;">
            <td width="80%" style="padding-left:10px;"><a href="javascript:popupClose('1');"><div>[ 하루동안 열지 않기 ]</div></a></td>
            <td width="20%" style="text-align:right"><a href="javascript:popupClose('0');"><div style="padding-right:20px;">[ 닫기 ]</div></a></td>
            </tr>
        </table>
    </div>
</div>
<?php } ?>