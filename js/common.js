jQuery(document).ready(function($) {
	$( '.prevent' ).click(function(){
		if(!jQuery.browser.msie){
			event.preventDefault();
		}else{
			event.returnValue = false;
		}
	});
	function prevent(){
		if(!jQuery.browser.msie){
			event.preventDefault();
		}else{
			event.returnValue = false;
		}
	}

	/*IE*/
	var version = detectIE();
	if (version === false) {
	  $('body').addClass('no-ie');
	} else if (version >= 12) {
	  $('body').addClass('ie-' + version);
	} else {
	  $('body').addClass('ie-' + version);
	}
	function detectIE() {
		var ua = window.navigator.userAgent;
		var msie = ua.indexOf('MSIE ');
		if (msie > 0) {
			return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
		}
		var trident = ua.indexOf('Trident/');
		if (trident > 0) {
			var rv = ua.indexOf('rv:');
			return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
		}
		var edge = ua.indexOf('Edge/');
		if (edge > 0) {
			return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
		}
		return false;
	}

	var cookie_name;
	$('.popup-close').click( function(){
		var popupDIv = $(this).closest('.popup-div');
		var checkBox = $(this).siblings('label').find('input');
		$(popupDIv).addClass('hide');
		cookie_name = $(popupDIv).attr('id');
		if( $(checkBox).is(':checked') ){
			$.cookie('madeshot-' + cookie_name, 'no', { expires: 1 }); 
		}
	});

	var cookieNames = ['header-popup', 'main-popup'];
	$.each( cookieNames, function( index, cookieName ) {
		var madeshotCookie = 'madeshot-' + cookieName;
		if ( $.cookie( madeshotCookie ) ) {
			$('#' + cookieName).addClass('hide');
		}
	});
	/*cookie*/

	/* slider */
	var mainSliderWidth = 1920;
	var thisS = $('.main-slider');
	var animationTime = 600;
	$(thisS).each(function(){
		var conutLi = $(thisS).children('li').length;
		for( i = 1; i <= conutLi; i++ ){
			if(i == 1){
				$(thisS).siblings('.slider-btn').append('<li class="active"></li>');		
			} else {
				$(thisS).siblings('.slider-btn').append('<li></li>');
			}
		}
		$(thisS).css({ 'width' : mainSliderWidth * conutLi + 'px', 'margin-left' : '0px' });
	});
	$('.slider-btn > li').click(function(){
		var nth = $(this).index(); 
		$(this).closest('.slider-btn').siblings('.main-slider').animate( { 'margin-left' : nth * mainSliderWidth * -1 + 'px'}, animationTime );

		$(this).closest('.slider-btn').children('li').each(function(){
			$(this).removeClass('active');
		});
		$(this).addClass('active');
	});

	/* fadein */
	$('.fadein').each(function(){
		$(this).addClass('ready');
	});

	/* 각종 메뉴 open close */
	function removeShow(e){
		$('body').find('.show').each(function(){
			$(this).removeClass('show');
		});
	}

	$('body').click( function( e ){	
        if ($(e.target).hasClass('close') || $(e.target).hasClass('menu-close')) {
			removeShow();
		}else if( $(e.target).hasClass('open-popup') ){
			removeShow();
			var eventId = '';
			eventId = $(e.target).attr('id');
			eventId = eventId + '-popup';
			$('#footer-overlay').addClass('show');	
			$( '#'+ eventId ).addClass('show');
		}else if( $(e.target).hasClass('open-popup-target') || $(e.target).parents('.open-popup-target').length ){
			//console.log('ccc');
			var openPopupTarget;
			if( !$(e.target).parents('.open-popup-target').length ){
				openPopupTarget = $(e.target);
			}else{
                openPopupTarget = $(e.target).parents('.open-popup-target');
            }
            removeShow(); 
            $('#footer-overlay').addClass('show'); 
            $(openPopupTarget).addClass('show'); 

		}else if ( $(e.target).hasClass('open') && !$(e.target).siblings('.open-target').hasClass('show')  ) {
			//console.log('ddd');
			removeShow();

			$(e.target).siblings('.open-target').addClass('show');
			$(e.target).addClass('show');

		}else if( $(e.target).closest('a').hasClass('footer-popup') || $(e.target).parents('div#footer-popup').length ){
			//console.log('eee');
			removeShow();
			$('#footer-overlay').addClass('show');
            $('#footer-popup').addClass('show');
        } else if ($(e.target).closest('a').hasClass('charlist-popup') || $(e.target).parents('div#charlist-popup').length) {
            //console.log('eee');
            removeShow();
            $('#footer-overlay').addClass('show');
            $('#charlist-popup').addClass('show');
        } else if ($(e.target).closest('a').hasClass('msgboxlist-popup') || $(e.target).parents('div#msgboxlist-popup').length) {
            //console.log('eee');
            removeShow();
            $('#footer-overlay').addClass('show');
            $('#msgboxlist-popup').addClass('show');	
		}else if( $(e.target).parents('.open-target').hasClass('show') || $(e.target).hasClass('show') ){
			//console.log('fff');
			if( $(e.target).hasClass('menu-close') || $(e.target).siblings('.open-target').hasClass('quick-menu') ){
				removeShow();
			}
        } else {
            /*받는사람 추가하는 팝업은 팝업 바깥쪽을 클릭해도 사라지지 않도록 */
            if ($('#call-frequent-popup').hasClass('show') || $('#call-addr-popup').hasClass('show') || $('#clip-past-popup').hasClass('show') || $('#call-pc-popup').hasClass('show')) {

            } else if ($('#addr-del-popup').hasClass('show')) {

            } else {
			  removeShow();
            }
		}

		/*sms예약 선택의 경우 datepicker, timepicker 콘트롤*/
		if( $('input#send_reserve').is(':checked') ){
			$('.datepicker-div').addClass('show');
		}else{
			$('.datepicker-div').removeClass('show');
			//$('.datepicker').val('');
			//$('.timepicker').val('');
		}
		if( $('input#send_reserve_1').is(':checked') ){
			$('.datepicker-div-1').addClass('show');
		}else{
			$('.datepicker-div-1').removeClass('show');
			//$('.datepicker').val('');
			//$('.timepicker').val('');
		}

		/*광고용 선택의 경우 하단 div open*/
		if( $('input#send_for_ad').is(':checked') ){
			$('.send_for_ad_target').addClass('show');
		}else{
			$('.send_for_ad_target').removeClass('show');
			$('#ad_agree').prop('checked' , false);
		}
		if( $('input#send_for_ad_1').is(':checked') ){
			$('.send_for_ad_target_1').addClass('show');
		}else{
			$('.send_for_ad_target_1').removeClass('show');
			$('#ad_agree_1').prop('checked' , false);
		}



		/**/
		if( $('input#confirm_by_fax').is(':checked') ){
			$('.confirm_by_fax_target').addClass('show');
		}else{
			$('.confirm_by_fax_target').removeClass('show');
        }

		if( $('input#confirm_by_paper').is(':checked') ){
			$('.confirm_by_paper_target').addClass('show');
		}else{
			$('.confirm_by_paper_target').removeClass('show');
        }

        if ($('input#confirm_by_sms').is(':checked')) {
            $('.confirm_by_sms_target').addClass('show');
        } else {
            $('.confirm_by_sms_target').removeClass('show');
        }

        if ($('input#confirm_by_ars').is(':checked')) {
            $('.confirm_by_ars_target').addClass('show');
        } else {
            $('.confirm_by_ars_target').removeClass('show');
        }



		/*test sms 오른쪽 block*/
		if( $('input#test-sms').is(':checked') ){
			$('.disabled').addClass('block');
		}else{
			$('.disabled').removeClass('block');
		}
		$('input.send-option').each( function(){
			if ( $(this).is(':checked') ){
				$(this).closest('label').siblings('div.open-target').addClass('show');
			}
		});

		/*basic-fax-cover 콘트롤*/
		if( $('input#basic-fax-cover').is(':checked') ){
			$('.basic-fax-cover-target').addClass('show');
		}else{
			$('.basic-fax-cover-target').removeClass('show');
		}

		/*첨부파일*/
		$( 'input[type="file"]' ).change( function(){
			var strOriginal = $(this).val();
			var str = $(this).val();
			var n = str.lastIndexOf("\\");
            str = str.substring(n + 1);
			var fileSize;
            if ($(this).hasClass('file-size')) {
                if (this.files[0] != null) {
				fileSize = this.files[0].size / 1024;
				fileSize = ' ['+ Math.ceil(fileSize) + ' KB]';
                }
			}
			$(this).siblings('.input-val').text( str + fileSize );
			$(this).siblings('.input-val').attr('data', strOriginal);
		});

		$( '.send_freq_type' ).change( function(){
			var addNewClass = $(this).val();
			$(this).closest('div').removeClass('daily business_daily weekly monthly');
			$(this).closest('div').addClass(addNewClass);
		});



		/*동적계산*/
		$('.js100pro').each( function(){
			$(this).css({'height': $(this).width()+'px'});
		});
		$('.js60pro').each( function(){
			$(this).css({'height': $(this).width()*.6+'px'});
		});
	});	

	/*전체메뉴 클론*/
	$('.paste-here').find('.open-target > div.all-menu').append( $('.copy-this').clone() );


	/*cs-fax*/
	var csFax = $('a#csfax').html();
	$('#cs-fax').text( csFax );
	var csEmail = $('a#csemail').html();
	$('#cs-email').text( csEmail );
	$('#cs-email').attr( 'href', 'mailto:'+ csEmail );
	//console.log(csFax);



	/*휴대폰 목업에서 문자보내기 전화번호 입력란 추가 & 제거*/
	$('.cell-phone-inner2').on('click', 'a.more-input', function(e){
		var closestDiv = $(e.target).closest('div');
		var n =  $(closestDiv).siblings('div').length;
		if ( $(closestDiv).is('div:first-child') ){
			if( n <= 3 ){
				var clonedDiv = $(closestDiv).clone();
				//console.log(clonedDiv);
				$('.cell-phone-inner2').append( $(closestDiv).clone().find('input[type="number"]').val('').end() );		
			}
		}else{
			$(closestDiv).remove();
		}
	});
		
	/*대량 문자보내기 전화번호 입력란 추가 & 제거 등*/
	function countNumbers(){
		var n = $('select.sms_num_list > option').length;
		$('.count_number').text(n);
	}
	function addOption(){
		var newNumber = $('input#sms_num').val();
		var numberLength = newNumber.toString().length;
        if (numberLength >= 11 && numberLength <= 11) {
            var szVal = "";
            var szTxt = "";

            szTxt = newNumber;
            szVal = "0;" + newNumber + ";" + newNumber + ";" + "1";
            $('select.sms_num_list').append('<option value="' + szVal + '">' + szTxt +'</option>');
			$('input#sms_num').val('');
		}
	}

	$('input.sms_num').on('keypress', function(e){
		if( e.keyCode == 13 ){
			addOption();
			countNumbers();
			return false;
		}
	});

	/*fax보내기 문서 추가 & 제거 등*/
	function countNumbers1(){
		var n = $('select[name="fax_docs"] > option').length;
		$('.count_number1').text(n);
	}
	function addOption1(){
		var newNumber = $('span.input-val').text();
		var newNumberData = $('span.input-val').attr('data');
		var numberLength = newNumber.toString().length;
		if( numberLength > 1 ){
			$('select[name="fax_docs"]').append('<option value="'+ newNumberData +'">'+ newNumber +'</option>');
			$('input[name="fax_doc"]').val('');
			$('span.input-val').text('');
		}
	}
	$('select[name="fax_docs"]').on('click', 'option',function(e){
		$(e.target).remove();
		countNumbers1();
	});
	$('a.add_to_fax_doc').on('click', function(){
		addOption1();
		countNumbers1();
	});

	/*자주보내는 번호, 주소록 불러오기 등 콘트롤*/
	$('.tap-title > li').click( function(e){
		var nth = $(this).index();	//0123
		$(this).closest('.tap-title').find('li').each( function(){
			$(this).removeClass('active');
		});
		$(this).addClass('active');
		/*tap-title > li 클릭 후 해야하는 것 여기에서...*/


	});
	/*초기화*/
	$('.w62pro').on('click', '.clear-all', function(){
		$('.w62pro').find('input').each( function(){
			var inputType	= $(this).attr('type');
			var thisId		= $(this).attr('id');
			if( inputType == 'checkbox' || inputType == 'radio' ){
				if( thisId == 'send_now' || thisId == 'send_for_business' || thisId == 'send_option_03' ){
					//console.log(thisId);
					$(this).prop('checked' , true);
				}else{
					$(this).prop('checked' , false);
				}

			}else if(  inputType == 'text' || inputType == 'number'  ){
				$(this).val('');
			}
		});
        
		$('.datepicker-div').removeClass('show');
		$('.cell-phone-inner textarea#sms').val('');
		$('.cell-phone-inner2').find('input').each( function(){
			var inputType = $(this).attr('type');
			if(  inputType == 'text' || inputType == 'number'  ){
				var outerDiv	= $(this).closest('div');
				var outerDivNth	= $(this).closest('div').index();
				if( outerDivNth != 0 ){
					$(outerDiv).remove();
				}else{
					$(this).val('');
				}
			}
		});
		$('#test-sms').attr('checked' , false);
		$('span.count_number').text('0');
		$('.tap-title').find('> li').each( function(){
			var nth = $(this).index();
			if( nth == 0 ){
				$(this).addClass('active');
			}else{
				$(this).removeClass('active');
			}
		});

		/*초가화 하려는 내용을 여기에 추가*/
	});
	/*datepicker*/
	$('.datepicker').each( function(){
		$(this).datepicker({
			monthNames	: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			dayNamesMin	: ['일','월','화','수','목','금','토'],
			dateFormat	: "yy-mm-dd"
		});
	});
	/*timepicker*/
	$('.timepicker').each( function(){
		$(this).timepicker({
			timeFormat	: 'HH:mm',
			scrollbar	: true
		});
    });

    /*미리 설정된 문자 tap - db다시불러오기*/
    $('.sms-tap-db').on('click', 'li', function () {
        unCheckAll();
        var nth = $(this).index();
        var thisUl = $(this).closest('.sms-tap-db');
        $(thisUl).find('li').each(function () {
            $(this).removeClass('active');
        });
        $(this).addClass('active');
        msgFormlist(nth);
    });

	/*미리 설정된 문자 tap*/
	$('.sms-tap').on('click','li', function(){
		unCheckAll();
		var nth = $(this).index();
		var thisUl = $(this).closest('.sms-tap');
		$(thisUl).find('li').each(function(){
			$(this).removeClass('active');
		});
		$(this).addClass('active');
		$(thisUl).siblings('.sms-target').find('> li').each(function(){
			//console.log(this);
			var nth2 = $(this).index();
			if( nth == nth2 ){
				$(this).addClass('active');
				if($(this).hasClass('saved-photo-sms')){

					$('#saved-sms-edit').removeClass('show1');
					$('#saved-photo-sms-edit').addClass('show1');

				}else{

					$('#saved-sms-edit').addClass('show1');
					$('#saved-photo-sms-edit').removeClass('show1');

				}
			}else{
				$(this).removeClass('active');
			}
		});
	});
	/*미리 설정된 문자 tag 추가*/
	$('ul.sms-target > li > ul > li').find('> a').each( function(){
		$(this).wrap( "<div></div>" );
	});
	/*미리 설정된 문자 휴대폰에 넣기*/
    $('ul.sms-target > li > ul > li a').on('click', function () {
        if ($(this).find('img')) {
            var imgSrc = $(this).find('img').attr('src');
            //console.log(imgSrc);
            $('a#img-upload > img').attr('src', imgSrc);
        } else {
            $('.cell-phone-inner textarea#msData').val($(this).html().replace(/<br>/gi, "\n"));
        }
        var msg = $(this).html();
    });

	/*문자보관함*/
	function unCheckAll(){
		$('.saved-sms input[type="checkbox"]').each(function(){
			$(this).prop('checked', false);
			$('.po-a.cover').removeClass('none');
		});
	}
	$('.saved-sms').on('click','input[type="checkbox"]', function(){
		unCheckAll();		
		$(this).prop('checked', true);
		$('.po-a.cover').addClass('none');
		var existCheck = $('.saved-sms input[type="checkbox"]:checked').length;
		if( existCheck == 0 ){
			$('.po-a.cover').removeClass('none');
		}	
	});
		
	/*동적계산*/
	$('.js100pro').each( function(){
		$(this).css({'height': $(this).width()+'px'});
	});
	$('.js60pro').each( function(){
		$(this).css({'height': $(this).width()*.6+'px'});
	});
});