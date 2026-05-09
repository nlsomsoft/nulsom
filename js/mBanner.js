

        function msgTypeBan(type) {
            if(type == 'fax') return "팩스";
            if(type == 'sms') return "단문";
            if(type == 'lms') return "장문";
            if(type == 'mms') return "포토";
        }
    
        function viewAds(sno, style, box) { 
                    $(".no_" + sno).addClass("banStyle_" + style);
                    $(".no_" + sno).addClass("banSubject_" + box.css.layout[0]);
            
                    if(sno == 4) {
                        $(".no_4").removeClass("hide");
                        $(".new-banner3 > div.initBan3").removeClass("initBan3");
                        $(".new-banner3 > div.initBan1").removeClass("initBan1");
                        $(".new-banner3 > div").addClass("initBan2");
                    }
            
                    if( (style == 3 && sno == 8) || sno == 9) {
                        $(".nb3-2nd").removeClass("hide");
                        $(".new-banner3 > div.initBan3").removeClass("initBan3");
                        $(".new-banner3 > div.initBan1").removeClass("initBan1");
                        $(".new-banner3 > div").addClass("initBan2");
                    }

                    if(style == 3 && sno < 5) {
                        $(".no_4").addClass("hide");
                        $(".new-banner3 > div.initBan3").removeClass("initBan3");
                        $(".new-banner3 > div.initBan2").removeClass("initBan2");
                        $(".new-banner3 > div").addClass("initBan1");
                        
                        $(".banStyle_3").css("width", "441px");
                    }
            
                    if(style == 3 && sno >= 5) {
                        $(".no_9").addClass("hide");
                        $(".new-banner3 > div.initBan3").removeClass("initBan3");
                        $(".new-banner3 > div.initBan2").removeClass("initBan2");
                        $(".new-banner3 > div").addClass("initBan1");
                        
                        $(".banStyle_3").css("width", "441px");
                    }
            

                    switch (style) { // 스타일번호로 매칭
                        case 0:
                            break;
                        case 1:
                            $(".no_" + sno).append("<p class='subtitleBanN' onClick=javascript:goLocation('" + box.link + "');><span>" + box.subtitle[0] + "</span><span>" + box.subtitle[1] + "</span></p>");
                            $(".no_" + sno).append("<p class='titleBanN' onClick=javascript:goLocation('" + box.link + "');><span>" + box.title3[0] + "</span><span>" + box.title3[1] + "</span><span>" + box.title3[2] + "</span></p>");
                            $(".no_" + sno).append("<p class='contentBanN' onClick=javascript:goLocation('" + box.link + "');>" + box.content + "</p>");
                            $(".no_" + sno).append("<a href=javascript:goLocation('" + box.link + "');  class='banMore'>더보기</a>");
                            break;
                        case 2:
                            $(".no_" + sno).append("<p class='subtitleBanN' onClick=javascript:goLocation('" + box.link + "');><span>" + box.subtitle[0] + "</span></p>");
                            $(".no_" + sno).append("<p class='titleBanN' onClick=javascript:goLocation('" + box.link + "');><span>" + box.title2[0] + "</span><span>" + box.title2[1] + "</span></p>");
                            $(".no_" + sno).append("<p class='contentBanN' onClick=javascript:goLocation('" + box.link + "');>" + box.content + "</p>");
                            $(".no_" + sno).append("<a href=javascript:goLocation('" + box.link + "');  class='banMore'>더보기</a>");
                            break;
                        case 3:
                            $(".no_" + sno).append("<span class='banStyle_" + style + "-11' onClick=javascript:goLocation('" + box.link + "');></span>");
                            $(".no_" + sno).append("<span class='banStyle_" + style + "-22' onClick=javascript:goLocation('" + box.link + "');></span>");
                            
                            if( $('input[name=ban_charge1]').val() != "" )
                                $(".banStyle_" + style + "-11").append("<p><span class='" + $('input[name=ban_name1]').val() + "Ban_icon'><span>" + msgTypeBan($('input[name=ban_name1]').val()) + "</span><span>" + $('input[name=ban_charge1]').val() + "</span><span>~</span><span>원</span><</span></p>");
                            
                            if( $('input[name=ban_charge2]').val() != "" )
                                $(".banStyle_" + style + "-11").append("<p><span class='" + $('input[name=ban_name2]').val() + "Ban_icon'><span>" + msgTypeBan($('input[name=ban_name2]').val()) + "</span><span>" + $('input[name=ban_charge2]').val() + "</span><span>~</span><span>원</span></span></p>");
                            
                            if( $('input[name=ban_charge3]').val() != "" )
                                $(".banStyle_" + style + "-11").append("<p><span class='" + $('input[name=ban_name3]').val() + "Ban_icon'><span>" + msgTypeBan($('input[name=ban_name3]').val()) + "</span><span>" + $('input[name=ban_charge3]').val() + "</span><span>~</span><span>원</span></span></p>");
                            
                            if( $('input[name=ban_charge4]').val() != "" )
                                $(".banStyle_" + style + "-11").append("<p><span class='" + $('input[name=ban_name4]').val() + "Ban_icon'><span>" + msgTypeBan($('input[name=ban_name4]').val()) + "</span><span>" + $('input[name=ban_charge4]').val() + "</span><span>~</span><span>원</span></span></p>");
                            
                            $(".banStyle_" + style + "-22").append("<p class='contentBanN'><span>" + box.content + "</span></p>");
                            $(".banStyle_" + style + "-22").append("<p class='titleBanN'><span>" + box.title + "</span></p>");
                            $(".banStyle_" + style + "-22").append("<a href=javascript:goLocation('" + box.link + "');  class='banMore'>더보기</a>");
                            break;
                        case 4:
                            $(".no_" + sno).append("<p class='contentBanN' onClick=javascript:goLocation('" + box.link + "');>" + box.content + "</p>");
                            $(".no_" + sno).append("<p class='titleBanN' onClick=javascript:goLocation('" + box.link + "');><span>" + box.title[0] + "</span></p>");
                            $(".no_" + sno).append("<a href=javascript:goLocation('" + box.link + "');  class='banMore'>더보기</a>");
                            break;
                        case 5:

                            break;
                        case 20:

                            break;
                        case 21:
                            $(".no_" + sno).append("<div class='banStyle_" + style + "-1' style='background-color:" + box.content + ";' onClick=javascript:goLocation('" + box.link + "');></div>");
                            $(".banStyle_" + style + "-1").append("<p class='imgBanN'  style='background-image: url(" + box.image + ");' onClick=javascript:goLocation('" + box.link + "');></p>");
                            $(".banStyle_" + style + "-1").append("<p class='titleBanN' onClick=javascript:goLocation('" + box.link + "');><span>" + box.title[0] + "</span></p>");
                            $(".banStyle_" + style + "-1").append("<a href=javascript:goLocation('" + box.link + "');  class='banMore2'>바로가기</a>");
                            break;
                        case 22:
                            $(".no_" + sno).append("<p class='imgBanN' onClick=javascript:goLocation('" + box.link + "');><img src='" + box.image + "' /></p>");
                            break;
                    }

            
            
        }

        function viewAds2(idx, sno) { 
			$(".new-banner2").append("<div><a href='" + adBox2[sno].link + "' target='_self' title='" + adBox2[sno].title + "'><img src='" + adBox2[sno].image + "' /><p class='newb2-title'>" + adBox2[sno].title + "</p><p class='newb2-desc'>" + adBox2[sno].content + "</p></a></div>");
            
            if(idx < 4) $(".new-banner2 > div").addClass("hide");
            
            if(idx > 4) $(".new-banner2").append("<div class='hide'><a href='" + adBox2[sno].link + "' target='_self' title='" + adBox2[sno].title + "'><img src='" + adBox2[sno].image + "' /><p class='newb2-title'>" + adBox2[sno].title + "</p><p class='newb2-desc'>" + adBox2[sno].content + "</p></a></div>");
            
            if(idx == 4 || idx == 9) $(".new-banner2 > div").removeClass("hide");
        }

        function viewAds3(idx, sno) { 
            $(".new-banner4").append("<div><a href='" + adBox3[sno].link + "' target='_self' title='" + adBox3[sno].title + "'><div class='newb4-img'><img src='" + adBox3[sno].image + "' /></div><p>" + adBox3[sno].title + "</p></a></div>");
            
            if(idx < 7) $(".new-banner4 > div").addClass("hide");
            
            if(idx > 7) $(".new-banner4").append("<div class='hide'><a href='" + adBox3[sno].link + "' target='_self' title='" + adBox3[sno].title + "'><div class='newb4-img'><img src='" + adBox3[sno].image + "' /></div><p>" + adBox3[sno].title + "</p></a></div>");
            
            if(idx == 7 || idx == 15) $(".new-banner4 > div").removeClass("hide");
            
        }
        


/** 100%, 1920px mainbanner **/

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

            /** animate or fade
            $('#big_main_img .cts02').fadeOut(100);  
            $('#big_main_img .cts02').eq(n_main).fadeIn(500);
            **/

            if(n_main == ( cntLit() -1 ) ){
                $('#circle_main .cts01').css(activeOut);
                $('#circle_main .cts01').eq(0).css(activeOn);
            }

        }

	}	

