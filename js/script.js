jQuery(document).ready(function($) {
	$('body').click( function( e ){
		if ($(e.target).hasClass('close') || $(e.target).hasClass('menu-close')) {
			removeShow();
		}
	});	
	$('.paste-here').find('.open-target > div.all-menu').append( $('.copy-this').clone() );



	var jlength1 = $("ul.main-menu > li").length / 2;
	var jwidth1 = 1200 / jlength1;
	$("ul.main-menu > li").css('width',jwidth1+'px');
	var jwidth2 = $("ul.main-menu > li").width() + 2;
	$(".main-menu > li > ul > li").css('width',jwidth2+"px");

	var newPhoto = 0;
	
	$(".new-photo").click(function() {
		$('.ph-pop').css('display','none');
		$(this).children('.ph-pop').css('display','block');
		newPhoto = 1;
	});
	
	$('#big_main_img').append("<li  class='cts02'>" + $('#big_main_img').find("li:first").html() + "</li>");

	$('#big_main_img').each(function(){
		conutLi = $('#big_main_img > li').length;

		for( i = 1; i < conutLi; i++ ){
			if(i == 1){
				$('#circle_main').append('<li class="cts01"></li>');
				$('#circle_main .cts01').eq(0).css(activeOn);
			} else {
				$('#circle_main').append('<li class="cts01"></li>');
			}
		}
	});

	var interval_time = 5000; 
	shopimg_main = setInterval(function (interval_time) {
		isPasue = false;
		trans_main();
	}, interval_time);

	$('#main_banner_wrap').mouseenter(function(){
		clearInterval(shopimg_main);
		isPause = true;
	}).mouseleave(function(){ 
		isPause = false;
		shopimg_main = setInterval("trans_main()", interval_time); 
	});

	$('#circle_main .cts01').eq(0).css(activeOn);
	
	$('#circle_main .cts01').mouseenter(function(){
		n_main = $('#circle_main .cts01').index(this);

		$('#circle_main .cts01').css(activeOut);
		$('#circle_main .cts01').eq(n_main).css(activeOn);

		$('#big_main_img').stop().css({"left":-1920*n_main});
	});

	$('.btn_main_pre').click(function(){
		n_main--;
		if(n_main<0){
			
			$('#big_main_img').stop().css({"left":-19200});
			n_main=7;
		}
		$('#circle_main .cts01').css(activeOut);
		$('#circle_main .cts01').eq(n_main).css(activeOn);
		
		$('#big_main_img').stop().animate({"left":-1920*n_main});
	});
	
	$('.btn_main_next').click(function(){
		n_main++;

		if(n_main==9){
			$('#big_main_img').stop().css({"left":0});
			n_main=1;	
		}

		$('#circle_main .cts01').css({"background-color":"#999",border:"1px solid #999"})
		$('#circle_main .cts01').eq(n_main).css({"background-color":"#d50c0c",border:"1px solid #d50c0c"})
		
		$('#big_main_img').stop().animate({"left":-1920*n_main});
		
		if(n_main==8){
			$('#circle_main .cts01').eq(0).css({"background-color":"#d50c0c",border:"1px solid #d50c0c"})
		}
	});
});

var activeOn = {
	'background-color': '#d50c0c', 
	'border': '1px solid #d50c0c',
	'padding': '7px 16px'
};
var activeOut = { 
	'background-color': '#999', 
	'border': '1px solid #999',
	'padding': '7px'
};
var mainSliderWidth = 1920;
var animationTime = 600;
var isPause = false;
var n_main=0; 	

function cntLit() {
	return $('#big_main_img > li').length; 
}

function trans_main(){
	if(!isPause) {
		n_main++;
		if(n_main == cntLit() ){
			$('#big_main_img').stop().css({"left":0});
			n_main=1;
		}
		$('#circle_main .cts01').css(activeOut);
		$('#circle_main .cts01').eq(n_main).css(activeOn);
		$('#big_main_img').stop().animate({"left":-1920*n_main},animationTime);
		if(n_main == ( cntLit() -1 ) ){
			$('#circle_main .cts01').css(activeOut);
			$('#circle_main .cts01').eq(0).css(activeOn);
		}
	}
}


function location_href(url) {
	if (url == '') return;
	$(location).attr("href", url);	
}

$(document).ready(function() {
    $("input[name=all_check_bt]").click(function(){
        if($("input[name=all_check_bt]").prop("checked")) {
            $("input[name=chk_seq_no]").prop("checked",true);
        } else {
            $("input[name=chk_seq_no]").prop("checked",false);
        }
    })
});
