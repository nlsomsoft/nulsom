jQuery(document).ready(function($) {
    
	
    var jlength1 = $("ul.main-menu > li").length / 2;
    var jwidth1 = 1300 / jlength1;
    $("ul.main-menu > li").css('width',jwidth1+'px');
    var jwidth2 = $("ul.main-menu > li").width() + 2;
    $(".main-menu > li > ul > li").css('width',jwidth2+"px");
    
    $("ul.main-menu").mouseover(function(){
        $(".main-bg").css("display", "block");
        
        if($(".main-bg").css("display") == "block"){
            $(".main-menu > li > ul").css("display", "block");
        }
    }).mouseout(function(){
        $(".main-bg").css("display", "none");
        
        if($(".main-bg").css("display") == "none"){
            $(".main-menu > li > ul").css("display", "none");
        }
    });
    

    $(".main-bg").mouseover(function(){
        $(this).css("display", "block");
        if($(this).css("display") == "block"){
            $(".main-menu > li > ul").css("display", "block");
        }
        
    }).mouseout(function(){
        $(this).css("display", "none");
        if($(this).css("display") == "none"){
            $(".main-menu > li > ul").css("display", "none");
        }
    });
    
    
    $(".vote_icon").append("<div class='vote_icon_view'></div>");
    
    
    $(".scroll-menu").mouseover(function() {
        $(".menu3New").css("display","block");
    }).mouseout(function() {
        $(".menu3New").css("display","");
    });

    
    var newPhoto = 0;
    
    $(".new-photo").click(function() {
        $('.ph-pop').css('display','none');
        $(this).children('.ph-pop').css('display','block');
        newPhoto = 1;
    });
    
    
    
    /** 100%, 1920px mainbanner **/
    
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
		//$(thisS).css({ 'width' : mainSliderWidth * conutLi + 'px', 'margin-left' : '0px' });
	});

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


    $(".scroll-menu").mouseover(function () {
        $(".menu3New").css("display", "block");
    }).mouseout(function () {
        $(".menu3New").css("display", "");
    });
	
    
    $('#send_test').click(function(){
        
        var maskHeight = $("#send_content").height();
        $("#send_bg").css("height",maskHeight);

        if($('#send_test').prop('checked')){
            $('a.test-sms-submit').css("display","block");
            $('#send_bg').fadeTo("slow",0.7);
            $('#testNumInput').css("display","block");
        }else{
            $('a.test-sms-submit').css("display","none");
            $('#send_bg').hide();
            $('#testNumInput').css("display","none");
        }

	});

    $(".mopen").each(function(index) {
        
        $(this).mouseover(function() {
            $(".mopen-target").css("display", "block");
        });
        $(this).mouseout(function() {
            $(".mopen-target").css("display", "none");
        });
    });
    
    $(".mopenb").each(function(index) {
        
        $(this).mouseover(function() {
            $(".mopen-targetb").css("display", "block");
        });
        $(this).mouseout(function() {
            $(".mopen-targetb").css("display", "none");
        });
    });
    
    $(".mopen1").each(function (index) {
       
        $(this).mouseover(function() {
            $(".mopen-target1").css("display", "block");
        });
        $(this).mouseout(function() {
            $(".mopen-target1").css("display", "none");
        });
    });
    
    $(".mopen2").each(function(index) {
        
        $(this).mouseover(function() {
            $(".mopen-target2").css("display", "block");
        });
        $(this).mouseout(function() {
            $(".mopen-target2").css("display", "none");
        });
    });
    
    $(".mopen3").each(function(index) {
        
        $(this).mouseover(function() {
            $(".mopen-target3").css("display", "block");
        });
        $(this).mouseout(function() {
            $(".mopen-target3").css("display", "none");
        });
    });
    
    $(".mopen4").each(function(index) {
        
        $(this).mouseover(function() {
            $(".mopen-target4").css("display", "block");
        });
        $(this).mouseout(function() {
            $(".mopen-target4").css("display", "none");
        });
    });
    
    $(".mopen5").each(function(index) {
        
        $(this).mouseover(function() {
            $(".mopen-target5").css("display", "block");
        });
        $(this).mouseout(function() {
            $(".mopen-target5").css("display", "none");
        });
    });

    $(".mopen6").each(function (index) {

        $(this).mouseover(function () {
            $(".mopen-target6").css("display", "block");
        });
        $(this).mouseout(function () {
            $(".mopen-target6").css("display", "none");
        });
    });

    
	$('#radio-tog-sms').hide();
	$('#radio-tog-esms').hide();
	$('#radio-tog-mo').hide();
	$('#radio-tog-fax').show();
	
	$('#radio-tog-pw').hide();
	$('#radio-tog-id').show();
    
    $('#radio-tog-adm2').hide();
	$('#radio-tog-adm3').hide();
	$('#radio-tog-adm4').hide();
    $('#radio-tog-adm5').hide();
	$('#radio-tog-adm1').show();
    
    $('#radio-tog-adm22').hide();
	$('#radio-tog-adm11').show();
	
	
	$( '#idpw-close1' ).click(function(){
			$('#idpw-popup1').hide();
	});
	$( '#idpw-close11' ).click(function(){
			$('#idpw-popup1').hide();
	});
	
	$( '#idpw-close2' ).click(function(){
			$('#idpw-popup2').hide();
	});
	
 	$( '#long-win' ).click(function(){


	});
	
    
	/* /아이디 찾기 팝업창 부분 */
/*    
    // menu 클래스 바로 하위에 있는 a 태그를 클릭했을때
    $(".adm-side-menu > a").click(function(){
        var adm_all_submenu = $(".adm-side-menu .adm-hide");
        var adm_submenu = $(this).next("ul");
        var adm_span = $(this).find("span");

        //adm_submenu.toggle();
        adm_span.text( adm_span.text() == '>' ? '<' : '>' );
        
        if( adm_submenu.is(":visible") ){
            adm_submenu.slideUp();
            //adm_span.show();
        }else{
            adm_all_submenu.slideUp();
            adm_submenu.slideDown();
            //adm_span.hide();
        }
      });
	
	

	$( '#add_member_cell1' ).click(function(){
		$obn = $('#member_cell tbody tr').length;

		var tmpHtml;
		if($obn < 10)
			tmpHtml = "<tr id='mcell0"+$obn+"'><td><div class='small-text3 join-checkbox2'><input type='checkbox' id='checkm0"+$obn+"' name='userchkm0"+$obn+"' class='' /><label for='checkm0"+$obn+"'>&nbsp;</label></div></td><td><input type='text' class='userm-input2' value='' name='mmid0"+$obn+"'></td><td></td><td><input type='text' class='userm-input2' value='' name='mid0"+$obn+"'></td><td><input type='text' class='userm-input1' value='' name='mpart0"+$obn+"'></td><td><input type='email' class='userm-input3' value='' name='memail0"+$obn+"'></td><td><input type='text' class='userm-input2' value='' name='mtel0"+$obn+"'></td><td><input type='text' class='userm-input2 popup-fax-btns-01 open-popup' id='my-select-fax1' value='' name='mfax0"+$obn+"' readonly></td><td><input type='text' class='userm-input2 popup-tel-btn0"+$obn+" open-popup' id='my-select-tel1' value='' name='mno0"+$obn+"' readonly></td></tr>";
		else if($obn < 11)
			tmpHtml = "<tr id='mcell"+$obn+"'><td><div class='small-text3 join-checkbox2'><input type='checkbox' id='checkm"+$obn+"' name='userchkm"+$obn+"' class='' /><label for='checkm"+$obn+"'>&nbsp;</label></div></td><td><input type='text' class='userm-input2' value='' name='mmid"+$obn+"'></td><td></td><td><input type='text' class='userm-input2' value='' name='mid"+$obn+"'></td><td><input type='text' class='userm-input1' value='' name='mpart"+$obn+"'></td><td><input type='email' class='userm-input3' value='' name='memail"+$obn+"'></td><td><input type='text' class='userm-input2' value='' name='mtel"+$obn+"'></td><td><input type='text' class='userm-input2 popup-fax-btns-01 open-popup' id='my-select-fax1' value='' name='mfax"+$obn+"' readonly></td><td><input type='text' class='userm-input2 popup-tel-btn"+$obn+" open-popup' id='my-select-tel1' value='' name='mno"+$obn+"' readonly></td></tr>";
		else {
			alert("MAX 10");
			return;
		}
			
		$("#member_cell tbody").append(tmpHtml);
		//alert($obn);
		//10개를 초과시 alert 창 띄워주고 더이상 안 만들어지게 함.
	});
	
	$( '#add_member_cell2' ).click(function(){
		$obn = $('#member_cell tbody tr').length;

		var tmpHtml;
		if($obn < 10)
			tmpHtml = "<tr id='mcell0"+$obn+"'><td><div class='small-text3 join-checkbox2'><input type='checkbox' id='checkm0"+$obn+"' name='userchkm0"+$obn+"' class='' /><label for='checkm0"+$obn+"'>&nbsp;</label></div></td></td><td><td><input type='text' class='userm-input2' value='' name='muid0"+$obn+"'></td><td><input type='text' class='userm-input2' value='' name='mid0"+$obn+"'></td><td><input type='text' class='userm-input1' value='' name='mpart0"+$obn+"'></td><td><input type='email' class='userm-input3' value='' name='memail0"+$obn+"'></td><td><input type='text' class='userm-input2' value='' name='mtel0"+$obn+"'></td><td><input type='text' class='userm-input2 popup-fax-btns-01 open-popup' id='my-select-fax1' value='' name='mfax0"+$obn+"' readonly></td><td><input type='text' class='userm-input2 popup-tel-btn0"+$obn+" open-popup' id='my-select-tel1' value='' name='mno0"+$obn+"' readonly></td></tr>";
		else if($obn < 11)
			tmpHtml = "<tr id='mcell"+$obn+"'><td><div class='small-text3 join-checkbox2'><input type='checkbox' id='checkm"+$obn+"' name='userchkm"+$obn+"' class='' /><label for='checkm"+$obn+"'>&nbsp;</label></div></td><td></td><td><input type='text' class='userm-input2' value='' name='muid"+$obn+"'></td><td><input type='text' class='userm-input2' value='' name='mid"+$obn+"'></td><td><input type='text' class='userm-input1' value='' name='mpart"+$obn+"'></td><td><input type='email' class='userm-input3' value='' name='memail"+$obn+"'></td><td><input type='text' class='userm-input2' value='' name='mtel"+$obn+"'></td><td><input type='text' class='userm-input2 popup-fax-btns-01 open-popup' id='my-select-fax1' value='' name='mfax"+$obn+"' readonly></td><td><input type='text' class='userm-input2 popup-tel-btn"+$obn+" open-popup' id='my-select-tel1' value='' name='mno"+$obn+"' readonly></td></tr>";
		else {
			alert("MAX 10");
			return;
		}
			
		$("#member_cell tbody").append(tmpHtml);
		//alert($obn);
		//10개를 초과시 alert 창 띄워주고 더이상 안 만들어지게 함.
	});
	


	
	$("input[name='mfax00']").click(function(){
		
		var chkvv = $(this).attr('name');
		//alert(chkvv);
		
		$('#fax1-hidden').val(chkvv);
		//var chkvv = $('#fax1-hidden').val();
		//alert(chkvv);
		
	});
	
	$("input[name='mno00']").click(function(){
		
		var chkvv = $(this).attr('name');
		//alert(chkvv);
		
		$('#num1-hidden').val(chkvv);
		//var chkvv = $('#fax1-hidden').val();
		//alert(chkvv);
		
	});
	
	
	
	$( '#popup-fax-btn' ).click(function(){
		
		//$('#my-select-fax-popup');
		var chkval1 = $("input[name='fax1-radio-2']:checked").val();
		//alert(chkval);
		//$("input[name='mfax01']").val(chkval);
		var chkval2 = $('#fax1-hidden').val();
		//alert(chkval2);
		$("input[name='" + chkval2 + "']").val(chkval1);
	});
	
	$( '#popup-tel-btn' ).click(function(){
		
		//$('#my-select-fax-popup');
		var chkval1 = $("input[name='num1-radio-2']:checked").val();
		//alert(chkval);
		var chkval2 = $('#num1-hidden').val();
		//alert(chkval2);
		$("input[name='" + chkval2 + "']").val(chkval1);
	});
	
	
	$( '#popup-fax-btn1' ).click(function(){
		
		var chkval1 = $("input[name='fax1-radio-21']:checked").val();
		//alert(chkval);
		//$("input[name='mfax01']").val(chkval);
		var chkval2 = $('#fax1-hidden1').val();
		//alert(chkval2);
		$("input[name='mfax01']").val(chkval1);
	});
	
	$( '#popup-tel-btn2' ).click(function(){
		
		var chkval1 = $("input[name='num1-radio-21']:checked").val();
		//alert(chkval);
		//$("input[name='mfax01']").val(chkval);
		var chkval2 = $('#num1-hidden1').val();
		//alert(chkval2);
		$("input[name='mno01']").val(chkval1);
	});
	*/
	/***************    /개별 팝업창 만들고 함수 추가하면 됨     **************/

/*
	$( '#addr-grp-add10' ).click(function(){
		$obn = $('#addr-direct tbody tr').length + 1;

		var tmpHtml;
		if($obn < 10)
			tmpHtml = "<tr><td><input type='text' id='grps-name0"+$obn+"' name='grps-name0"+$obn+"'  maxlength='15' class='addr-inputs4' placeholder=''></td><td><input type='text' id='grps-company0"+$obn+"' name='grps-company0"+$obn+"'  maxlength='15' class='addr-inputs5' placeholder=''></td><td><input type='text' id='grps-fax0"+$obn+"' name='grps-fax0"+$obn+"'  maxlength='20' class='addr-inputs5' placeholder=''></td><td><input type='text' id='grps-mobile0"+$obn+"' name='grps-mobile0"+$obn+"'  maxlength='20' class='addr-inputs6' placeholder=''></td><td><input type='text' id='grps-tel0"+$obn+"' name='grps-tel0"+$obn+"'  maxlength='20' class='addr-inputs6' placeholder=''></td><td><input type='text' id='grps-email0"+$obn+"' name='grps-email0"+$obn+"'  maxlength='30' class='addr-inputs7' placeholder=''></td></tr>";
		else if($obn < 11)
			tmpHtml = "<tr><td><input type='text' id='grps-name"+$obn+"' name='grps-name"+$obn+"'  maxlength='15' class='addr-inputs4' placeholder=''></td><td><input type='text' id='grps-company"+$obn+"' name='grps-company"+$obn+"'  maxlength='15' class='addr-inputs5' placeholder=''></td><td><input type='text' id='grps-fax"+$obn+"' name='grps-fax"+$obn+"'  maxlength='20' class='addr-inputs5' placeholder=''></td><td><input type='text' id='grps-mobile"+$obn+"' name='grps-mobile"+$obn+"'  maxlength='20' class='addr-inputs6' placeholder=''></td><td><input type='text' id='grps-tel"+$obn+"' name='grps-tel"+$obn+"'  maxlength='20' class='addr-inputs6' placeholder=''></td><td><input type='text' id='grps-email"+$obn+"' name='grps-email"+$obn+"'  maxlength='30' class='addr-inputs7' placeholder=''></td></tr>";
		else {
			alert("MAX 10");
			return;
		}
			
		$("#addr-direct tbody").append(tmpHtml);
		//alert($obn);
		//10개를 초과시 alert 창 띄워주고 더이상 안 만들어지게 함.
	});
	
	//통계 라디오버튼 토글
	
	$( '#addr-radio-t1' ).click(function(){
		
		$('#radio-tog-sms').hide();
		$('#radio-tog-esms').hide();
		$('#radio-tog-mo').hide();

		$('#radio-tog-fax').show();
	});
	
	$( '#addr-radio-t2' ).click(function(){
		
		$('#radio-tog-fax').hide();
		$('#radio-tog-esms').hide();
		$('#radio-tog-mo').hide();
		
		$('#radio-tog-sms').show();
	});
	
	$( '#addr-radio-t3' ).click(function(){
		
		$('#radio-tog-fax').hide();
		$('#radio-tog-sms').hide();
		$('#radio-tog-mo').hide();
		
		$('#radio-tog-esms').show();
	});
	
	$( '#addr-radio-t4' ).click(function(){
		
		$('#radio-tog-fax').hide();
		$('#radio-tog-sms').hide();
		$('#radio-tog-esms').hide();
		
		$('#radio-tog-mo').show();
	});
	
	//아이디비번찾기 라디오버튼 토글
		
	$( '#idpw-radio-t1' ).click(function(){
		
		$('#radio-tog-pw').hide();

		$('#radio-tog-id').show();
	});
	
	$( '#idpw-radio-t2' ).click(function(){
		
		$('#radio-tog-id').hide();
		
		$('#radio-tog-pw').show();
	});
	
	$( '#adm-radio-t1' ).click(function(){
		
		$('#radio-tog-adm2').hide();
		$('#radio-tog-adm3').hide();
		$('#radio-tog-adm4').hide();
        $('#radio-tog-adm5').hide();

		$('#radio-tog-adm1').show();
	});
	
	$( '#adm-radio-t2' ).click(function(){
		
		$('#radio-tog-adm1').hide();
		$('#radio-tog-adm3').hide();
		$('#radio-tog-adm4').hide();
        $('#radio-tog-adm5').hide();

		$('#radio-tog-adm2').show();
	});
	
	$( '#adm-radio-t3' ).click(function(){
		
		$('#radio-tog-adm1').hide();
		$('#radio-tog-adm2').hide();
		$('#radio-tog-adm4').hide();
        $('#radio-tog-adm5').hide();

		$('#radio-tog-adm3').show();
	});
	
	$( '#adm-radio-t4' ).click(function(){
		
		$('#radio-tog-adm1').hide();
		$('#radio-tog-adm2').hide();
		$('#radio-tog-adm3').hide();
        $('#radio-tog-adm5').hide();

		$('#radio-tog-adm4').show();
	});    
    
	$( '#adm-radio-t5' ).click(function(){
		
		$('#radio-tog-adm1').hide();
		$('#radio-tog-adm2').hide();
		$('#radio-tog-adm3').hide();
        $('#radio-tog-adm4').hide();

		$('#radio-tog-adm5').show();
	});   
    
    
    
    $( '#adm-radio-t11' ).click(function(){
		
		$('#radio-tog-adm22').hide();

		$('#radio-tog-adm11').show();
	});    
    
	$( '#adm-radio-t22' ).click(function(){
		
		$('#radio-tog-adm11').hide();

		$('#radio-tog-adm22').show();
	});   
    
    
    
    $('.checkall2').click(function(){

        if($('.checkall2').prop('checked')){
            $('.chks2').prop('checked', true);
        }else{
            $('.chks2').prop('checked', false);
        }

	});
	
    $('.checkall3').click(function(){

        if($('.checkall3').prop('checked')){
            $('.chks3').prop('checked', true);
        }else{
            $('.chks3').prop('checked', false);
        }

	});
    
    $('.checkall4').click(function(){

        if($('.checkall4').prop('checked')){
            $('.chks4').prop('checked', true);
        }else{
            $('.chks4').prop('checked', false);
        }

	});
    
    $('.checkall5').click(function(){

        if($('.checkall5').prop('checked')){
            $('.chks5').prop('checked', true);
        }else{
            $('.chks5').prop('checked', false);
        }

	});
    
    $('.checkall6').click(function(){

        if($('.checkall6').prop('checked')){
            $('.chks6').prop('checked', true);
        }else{
            $('.chks6').prop('checked', false);
        }

	});
*/	

	/**** 기본적인 전체선택 체크박스 *****/
/*
	$('.checkall').click(function(){

        if($('.checkall').prop('checked')){
            $('.chks').prop('checked', true);
        }else{
            $('.chks').prop('checked', false);
        }

	});
*/	
	/**** 히튼버튼 넣고 버튼식 전체선택 체크박스 *****/
/*
	$('.env2-check-all').click( function() {
		if( $('#checkbox-chk').val() == '0' ) {
          $( '.env2-chk' ).prop( 'checked', true );
          $('#checkbox-chk').val(1);
    }
        else {
                    $( '.env2-chk' ).prop( 'checked', false );
                    $('#checkbox-chk').val(0);
        }
    } );
    
    
    $('.wxclose').click( function() {
		window.open('about:blank', '_self').close();

    } );
    
    
    $('#admBtn6-4').click(function () {
          // getter
        var radioVal = $('input[name="adm-radio-1"]:checked').val();
        
        var sNo = radioVal;
        var sWidth = 800;
        var sHeight = 820;
        var popupX = (window.screen.width / 2) - (sWidth / 2);
        var popupY= (window.screen.height /2) - (sHeight / 2) - 20;
        
        if($('input[name="adm-radio-1"]').is(':checked') == false) empty_cell();
        else {
            window.open('adm-popup-6-45.html?sno='+ sNo + '', 'adm-popup01', 'status=no, height='+ sHeight + ', width='+ sWidth + ', left='+ popupX + ', top='+ popupY + ', screenX='+ popupX + ', screenY= '+ popupY);
        }
        
    });
    
    
    $('#admBtn7-4').click(function () {
          // getter
        var radioVal = $('input[name="adm-radio-1"]:checked').val();
        
        var sNo = radioVal;
        var sWidth = 800;
        var sHeight = 820;
        var popupX = (window.screen.width / 2) - (sWidth / 2);
        var popupY= (window.screen.height /2) - (sHeight / 2) - 20;
        
        if($('input[name="adm-radio-1"]').is(':checked') == false) empty_cell();
        else {
            window.open('adm-popup-7-45.html?sno='+ sNo + '', 'adm-popup01', 'status=no, height='+ sHeight + ', width='+ sWidth + ', left='+ popupX + ', top='+ popupY + ', screenX='+ popupX + ', screenY= '+ popupY);
        }
        
    });
*/
});

	