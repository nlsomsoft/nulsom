// -----------------------------------------------------------------------
// common vars
MOUSE_X = -1;  //버전 4 이하에서의 에러 방지를 위해
MOUSE_Y = -1;  //버전 4 이하에서의 에러 방지를 위해

CURRENT_MOVE_LAYER = null;
CURRENT_MOVE_LAYER_PD_X = 0;
CURRENT_MOVE_LAYER_PD_Y = 0;

SINGLE_LAYER = null;

// -----------------------------------------------------------------------
// common handlers

// 에러핸들러 설정
window.onerror = _ERROR_HANDLER;
function _ERROR_HANDLER( msg, url, line )
{
	// //alert( "msg : " + msg + "\nurl : " + url + "\nline : " + line );
	// var _url = "/NPlusErrorReport.php?mode=js_error&msg=" + msg + "&url=" + url + "&line=" + line + "&userid=" + current_userid + "&agent=" + window.navigator.userAgent;
	// var ew = window.open( _url, "ErrorReport", "scrollbars=no,status=no,width=1,height=1,top=0,left=0" );
	// return true;
	console.log("msg : " + msg + "\nurl : " + url + "\nline : " + line);
}

window.onresize = function()
{
	try
	{
		_init_ad();
	} catch( exception ) {
	}
}

document.ondragstart = function()
{
	if( CURRENT_MOVE_LAYER == null ) return true;
	else return false;
}

document.onselectstart = function()
{
	if( CURRENT_MOVE_LAYER == null ) return true;
	else return false;
}

// 좌표 마우스 핸들러 설정
if( document.layers ) document.captureEvents( Event.mousemove );
document.onmousemove = mouseStatusXY;  //마우스이동 이벤트 핸들러를 지정

//페이지 상의 좌표값을 구하기 위한 이벤트 핸들러
function mouseStatusXY( theEvent )
{
	//NetscapeNavigator 4.x 의 경우
	try
	{
		if( document.layers )
		{
			MOUSE_X = theEvent.pageX;  // X좌표 값을 구한다.
			MOUSE_Y = theEvent.pageY;  //Y좌표값을 구한다.
			document.routeEvent( theEvent );  //취득한 이벤트를 원상태로 되돌린다.
		}

		//Internet Explorer 4.0x 의 경우
		if( document.all )
		{
			MOUSE_X = document.body.scrollLeft + event.clientX;  //X좌표값을 구한다.
			MOUSE_Y = document.body.scrollTop + event.clientY;  //Y좌표값을 구한다.
		}

		// 레이어를 잡고 있을때 움직이기 위한 것.
		if( CURRENT_MOVE_LAYER != null )
		{
			CURRENT_MOVE_LAYER.style.position = "absolute";
			CURRENT_MOVE_LAYER.style.top = CURRENT_MOVE_LAYER_PD_Y + MOUSE_Y + "px";
			CURRENT_MOVE_LAYER.style.left = CURRENT_MOVE_LAYER_PD_X + MOUSE_X + "px";
		}
	} catch( exception ) {		// 다운로드 하면 발생한다. 에러... 이벤트 핸들러가 가출하나 ??
	}
}

// -----------------------------------------------------------------------
// initialize part
var _initialized = false;
function init()
{
	if( _initialized == true ) return;
	_initialized = true;

	// 특정 TAG 에 반응해서 그것이 설정된 TAG 만 기능 REPLACE
	a_tag = document.getElementsByTagName( "a" );
	for( var rp = 0; rp < a_tag.length; rp++ )
	{
		//var href2 = a_tag[rp].getAttribute( "href2" );
		//if( href2 != null ) a_tag[rp].setAttribute( "href", base_domain + "/" + a_tag[rp].getAttribute( "href2" ) );
		if( a_tag[rp].getAttribute( "href" ).indexOf( "#" ) > -1 )
		{
			a_tag[rp].setAttribute( "href", location.href + "#" );
		}
	}

	try
	{
		_init();
	} catch( exception ) {
	}

	try
	{
		_init_ad();
	} catch( exception ) {
	}
}

function _init_ad()
{
	var ap_right = document.getElementsByName( "ad_point_right" );
	var ac_right = document.getElementsByName( "ad_content_right" );

	if( ap_right.length > 0 && ac_right.length > 0 )
	{
		for( var i = 0; i < ap_right.length; i++ )
		{
			if( ac_right.length == i ) break;		// 더이상 없으므로 중지.
			var apr_loc = getOffsetPosition( ap_right[0] );
			ac_right[i].style.display = "inline";
			ac_right[i].style.left = ( apr_loc[0] + 10 ) + "px";
			ac_right[i].style.top = ( apr_loc[1] - 10 ) + "px";
			// alert( apr_loc[0] + " , " + apr_loc[1] );
		}
	}
}

// -----------------------------------------------------------------------
// prototype parts
/**
* string String::cut(int len)
* 글자를 앞에서부터 원하는 바이트만큼 잘라 리턴합니다.
* 한글의 경우 2바이트로 계산하며, 글자 중간에서 잘리지 않습니다.
*/
String.prototype.cut = function( len )
{
    var str = this;
    var l = 0;
    for( var i = 0; i < str.length; i++ )
	{
            l += ( str.charCodeAt( i ) > 128 ) ? 2 : 1;
            if( l > len ) return str.substring( 0, i ) + "..."; // <-- 줄임표 표시 (필요없으면 지우세용)
    }
    return str;
}

String.prototype.cut2 = function( len )
{
    var str = this;
    var l = 0;
    for( var i = 0; i < str.length; i++ )
	{
            l += ( str.charCodeAt( i ) > 128 ) ? 2 : 1;
            if( l > len ) return str.substring( 0, i );
    }
    return str;
}

/**
* bool String::bytes(void)
* 해당스트링의 바이트단위 길이를 리턴합니다. (기존의 length 속성은 2바이트 문자를 한글자로 간주합니다)
*/
String.prototype.bytes = function()
{
    var str = this;
    var l = 0;
    for( var i = 0; i < str.length; i++ ) l += ( str.charCodeAt( i ) > 128 ) ? 2 : 1;
    return l;
}

String.prototype.bytes2 = function()
{
    var str = this;
    var l = 0;
    for( var i = 0; i < str.length; i++ )
	{
		if( str.charCodeAt( i ) == 13 ) continue;
		l += ( str.charCodeAt( i ) > 128 ) ? 2 : 1;
	}
    return l;
}

// 앞뒤 공백 제거
String.prototype.trim = function()
{
	var str = this.replace( /(\s+$)/g, "" );
	return str.replace( /(^\s*)/g, "" );
}

// -----------------------------------------------------------------------
// layer and position utilities
function getOffsetPosition( _element )
{
	var _return = new Array()

	var _top = _element.offsetTop;
	var _left = _element.offsetLeft;
	while( ( _element = _element.offsetParent ) != null )
	{
		_top += _element.offsetTop;
		_left += _element.offsetLeft;
	}

	_return[0] = _left;			// X
	_return[1] = _top;			// Y
	//alert([top, left]);

	return _return;
}

function toggleLayer( layername, mode )
{
	var layer = document.getElementsByName( layername );
	if( layer.length < 1 ) return false;

	for( var i = 0; i < layer.length; i++ )
	{
		if( mode == null )
		{
			if( layer[i].style.display == "none" ) layer[i].style.display = "inline";
			else layer[i].style.display = "none";
		} else {
			layer[i].style.display = mode;
		}
	}
}

function layerOpenByElement( _layer, _layer_base, X, Y )
{
	var layerbase = document.getElementsByName( _layer_base );
	var r = getOffsetPosition( layerbase[0] );
	return layerOpenLocation( _layer, r[0] + X, r[1] + Y );
}

function layerOpenLocation( _layer, X, Y )
{
	var _target = document.getElementsByName( _layer );
	_target[0].style.display = "inline";
	_target[0].style.position = "absolute";
	_target[0].style.top = Y + "px";
	_target[0].style.left = X + "px";

	return _target[0];
}

function layerOpen( _layer )
{
	var _target = document.getElementsByName( _layer );
	_target[0].style.display = "inline";
	_target[0].style.position = "absolute";
	_target[0].style.top = ( MOUSE_Y - 10 ) + "px";
	_target[0].style.left = ( MOUSE_X + 15 ) + "px";

	return _target[0];
}

function layerClose( _layer )
{
	var _target = document.getElementsByName( _layer );
	_target[0].style.display = "none";

	return _target[0];
}

function closeSingleLayer()
{
	if( SINGLE_LAYER == null ) return;
	SINGLE_LAYER.style.display = "none";
}

function holdMoveLayer( el )
{
	var r = getOffsetPosition( el );
	CURRENT_MOVE_LAYER_PD_X = r[0] - MOUSE_X;
	CURRENT_MOVE_LAYER_PD_Y = r[1] - MOUSE_Y;

	CURRENT_MOVE_LAYER = el;

	return el;
}

function releaseMoveLayer( el )
{
	CURRENT_MOVE_LAYER = null;
	CURRENT_MOVE_LAYER_PD_X = 0;
	CURRENT_MOVE_LAYER_PD_Y = 0;

	return el;
}

// -----------------------------------------------------------------------
// etc utility parts
function redirect( url )
{
	top.location.href = url;
}

function evalReceiver( _eval_command )
{
	eval( _eval_command );
}

function clearForm( form, msg )
{
	if( form.value == msg ) form.value = "";
}

/**
 * 쿠키 읽기
 * @param name 키
 * @returns
 */
function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

/**
 * 쿠키 쓰기
 * @param name  키
 * @param value 값
 * @param days 날짜
 */
function writeCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

/**
 * 쿠키 삭제
 * @param name
 */
function deleteCookie(name) {
    createCookie(name,"",-1);
}

// -----------------------------------------------------------------------
// check parts
function countField( _source_field, _target_field, _count )
{
	if( _source_field.value.length >= _count )
	{
		_target_field.focus();
	}
}

// 번호 체크 (절대로 onKeyDown 이벤트에 사용하세요)
function onlyNumberCheck( _this_form, _next_form, _limit_len, _execute_func )
{
	//alert( event.keyCode );
	var ek = event.keyCode;
	if( ek != 8 && ek != 9 && ek != 35 && ek != 36 && ek != 18 && ek != 86 && ek != 67 && ek != 46
		&& ( ek < 37 || ek > 40 ) && ( ek < 48 || ek > 57 ) && ( ek < 95 || ek > 105 ) )
		//&& ( ek < 37 || ek > 40 ) && ( ek < 48 || ek > 57 ) ) //&& ( ek < 95 || ek > 105 ) )
	{
		if( _execute_func != null && ek == 13 )
		{
			eval( _execute_func );
		}
		event.returnValue = false;
	}

	if( ek != 8 && _this_form != null && _next_form != null && _limit_len != null )	 // 백스페이스 무시
	{
		countField( _this_form, _next_form, _limit_len );
	}
}

function onlyNumberCheck2( _execute_func )
{
	//alert( event.keyCode );
	var ek = event.keyCode;
	if( ek != 8 && ek != 9 && ek != 35 && ek != 36 && ek != 18 && ek != 86 && ek != 67 && ek != 46
		&& ( ek < 37 || ek > 40 ) && ( ek < 48 || ek > 57 ) && ( ek < 95 || ek > 105 ) )
		//&& ( ek < 37 || ek > 40 ) && ( ek < 48 || ek > 57 ) ) //&& ( ek < 95 || ek > 105 ) )
	{
		if( _execute_func != null && ek == 13 )
		{
			eval( _execute_func );
		}
		event.returnValue = false;
	}
}

function checkMobile(_form) {
	if (_form.value.bytes() < 1) return;

	if (isMobileNumber( _form.value ) == false) {
		alert ("전화번호 오류입니다. 전화번호를 확인하세요.");
		_form.value = '';
		_form.focus();
	}
}

function nextForm( _form_field )
{
	var ek = event.keyCode;
	if( _form_field != null && ek == 13 )
	{
		_form_field.focus();
		event.returnValue = false;
	}
}

function enterCheck( _execute_func )
{
	var ek = event.keyCode;
	if( _execute_func != null && ek == 13 )
	{
		eval( _execute_func );
		event.returnValue = false;
	}
}

function _check_email( email )
{
	var pattern = /^(.+)@(.+)$/;
	var atom = "\[^\\s\\(\\)<>#@,;:!\\\\\\\"\\.\\[\\]\]+";
	var word="(" + atom + "|(\"[^\"]*\"))";
	var user_pattern = new RegExp("^" + word + "(\\." + word + ")*$");
	var ip_pattern = /^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/;
	var domain_pattern = new RegExp("^" + atom + "(\\." + atom +")*$");

	var arr = email.match(pattern);
	if (!arr) { alert( "이메일 주소 형식이 틀립니다." ); return false; }
	if (!arr[1].match(user_pattern)) { alert( "이메일 계정형식이 틀립니다." ); return false; }

	var ip = arr[2].match(ip_pattern);
	if (ip) {
          for (var i=1; i<5; i++) if (ip[i] > 255) { alert( "IP 형식으로 된 메일 도메인은 사용할 수 없습니다." ); return false; }
	}
	else {
		if (!arr[2].match(domain_pattern)) { alert( "메일 도메인 형식이 틀립니다." ); return false; }
		var domain = arr[2].match(new RegExp(atom,"g"));
		if (domain.length<2) { alert( "메일 도메인을 입력해 주세요." ); return false; }
		if (domain[domain.length-1].length<2 || domain[domain.length-1].length>4)
		{ alert( "메일 도메인은 2~4글자로 끝나야 합니다." ); return false; }
	}
	return true;
}

function touchCheckbox()
{
	var _chkbox_root = document.getElementsByName( "_chkbox_root" );
	var _chkbox = document.getElementsByName( "_chkbox" );

	if( _chkbox_root[0].checked == true )
	{
		_chkbox_root[0].checked = true;
	} else {
		_chkbox_root[0].checked = false;
	}

	for( var i = 0; i < _chkbox.length; i++ )
	{
		_chkbox[i].checked = _chkbox_root[0].checked;
	}
}

function isNumeric(s) {
	for (var i = 0; i < s.length; i++) {
		var c = s.substr( i, 1 );
		if (c < "0" || c > "9") return false;
	}
	return true;
}

function isMobileNumber(s) {
	if (isNumeric(s) == false) return false;
	var str = s.substr(0, 3);
	if (str == "010" || str == "011" || str == "016" || str == "017" || str == "018" || str == "019") {
		if (s.length != 10 && s.length != 11) return false;
		else return true;
	}
	else if (str == "050") {
		if (s.length != 11 && s.length != 12) return false;
		else return true;
	}
	else return false;
	// if (str != "010" && str != "011" && str != "017" && str != "018" && str != "019" && str != "050") return false;
	// if (s.length != 11 ) return false;
	// return true;
}

// open window scroll no
function open_win( winName, URL, w, h, xp, yp )
{
	sx = ( screen.width - w ) / 2;
	if( xp != null ) sx += xp;
	sy = ( screen.height - h ) / 2 ;
	if( yp != null ) sy += yp;
	var ro = null;
	sub = ro = LAST_POPUP = window.open( URL, winName , "scrollbars=no,status=no,width=" + w + ",height=" + h + ",top=" + sy + ",left=" + sx );
	sub.owner = self;
	return ro;
}

// open window 2 scroll yes
function open_win2( winName , URL, w, h )
{
	sx = ( screen.width - w ) / 2;
	sy = ( screen.height - h ) / 2 ;
	var ro = null;
	sub = ro = LAST_POPUP = window.open( URL, winName, "scrollbars=yes,status=no,width=" + w + ",height=" + h + ",top=" + sy + ",left=" + sx );
	sub.owner = self;
	return ro;
}

// check box
function check_group(group_name, addr_num)
{
    flag = (document.address.elements[group_name+"_0"].checked) ? false : true;

    for (i=0; i<addr_num; i++) {
        checked_str = group_name + "_" + i;
        document.address.elements[checked_str].checked = flag;
    }
}

function Josa(txt, josa)
{
    var code = txt.charCodeAt(txt.length-1) - 44032;
    var cho = 19, jung = 21, jong=28;
    var i1, i2, code1, code2;

    // 원본 문구가 없을때는 빈 문자열 반환
    if (txt.length == 0) return '';

    // 한글이 아닐때
    if (code < 0 || code > 11171) return txt;

    if (code % 28 == 0) return txt + Josa.get(josa, false);
    else return txt + Josa.get(josa, true);
}
Josa.get = function (josa, jong) {
    // jong : true면 받침있음, false면 받침없음

    if (josa == '을' || josa == '를') return (jong?'을':'를');
    if (josa == '이' || josa == '가') return (jong?'이':'가');
    if (josa == '은' || josa == '는') return (jong?'은':'는');
    if (josa == '와' || josa == '과') return (jong?'와':'과');

    // 알 수 없는 조사
    return '**';
}

var _allcheck = 0;
function jsAllSelect(obj)
{
	if( !obj ) return;

	if( _allcheck == 1 ) _allcheck = 0;
	else _allcheck = 1;

	if( obj.name ){
		if( obj.disabled == true ) obj.checked = false;
		else if( _allcheck == 1 ) obj.checked = true;
		else obj.checked = false;
	} else {
		for( var i = 0; i < obj.length; i++ ){
			if( obj[i].disabled == true ) obj[i].checked = false;
			else if( _allcheck == 1 ) obj[i].checked = true;
			else obj[i].checked = false;
		}
	}
}

function jsCheckedVal(obj)
{
	var pass = 0;
	var checkedval = '';

	if( obj.name ){
		if( obj.checked ) checkedval = obj.value;
	} else {
		for( var i = 0; i < obj.length; i++ ){
			if( obj[i].checked ){
				if( pass ) checkedval += ",";
				checkedval += obj[i].value;
				pass++;
			}
		}
	}
	return checkedval;
}

<!-- new -->
function touchCheckboxNew()
{
	var _chkbox_root = document.getElementsByName( "_chkbox_root" );
	var _chkbox = document.getElementsByName( "_chkbox[]" );

	if( _chkbox_root[0].checked == true )
	{
		_chkbox_root[0].checked = true;
	} else {
		_chkbox_root[0].checked = false;
	}

	for( var i = 0; i < _chkbox.length; i++ )
	{
		_chkbox[i].checked = _chkbox_root[0].checked;
	}
}

function layerOpenByElementNew( _layer, _layer_base, X, Y )
{
	var layerbase = document.getElementById( _layer_base );
	var r = getOffsetPosition( layerbase );
	return layerOpenLocationNew( _layer, r[0] + X, r[1] + Y );
}

function layerOpenLocationNew(_layer, X, Y) {
	var _target = document.getElementById(_layer);
	_target.style.display = "inline";
	_target.style.position = "absolute";
	_target.style.top = Y + "px";
	_target.style.left = X + "px";
	return _target[0];
}

function layerCloseNew( _layer )
{
	var _target = document.getElementById( _layer );
	_target.style.display = "none";

	return _target;
}

function countMessageByte( num )
{
	var _sms_msg = document.getElementsByName( "_sms_msg[]" );
	var _sms_bytes = document.getElementsByName( "_sms_bytes[]" );
	var msg = _sms_msg[num].value;
	var bytes = msg.bytes2();
	 _sms_bytes[num].value = bytes;
}

function rsvLayer( layername )
{
	var layer = document.getElementById( layername );
	var view = document.getElementsByName( "reservation" );
	if( view[0].checked == true )
	{
		layer.style.display = "inline";
	} else {
		layer.style.display = "none";
	}
}

function chkCallbackFormat(callbackVal)
{
	if(callbackVal.length < 8 || callbackVal.length > 11)
	{
		if(callbackVal.length == 12 && callbackVal.substring(0,3) == "030")	 return true;
		else return false;
	} else {
		if(callbackVal.substring(0,3) == "021")
		{
			return false;
		} else {
			var cVal = callbackVal.substring(0,2);
			if(callbackVal.length == 8)
			{
				if(cVal == "15" || cVal == "16" || cVal == "18") return true;
				else return false;
			} else {
				if(cVal == "01")
				{
					var regMobile = /^(?:(010\d{4})|(01[1|6|7|8|9]\d{3,4}))(\d{4})$/;
					if(!regMobile.test(callbackVal))
					{
						return false;
					}
				} else if(cVal == "07") {
					if( callbackVal.length != 11 )
					{
						return false;
					}
				} else {
					var regPhone = /^(0(2|3[1-3]|4[1-4]|5[1-5]|6[1-4]))(\d{3,4})(\d{4})$/;
					if(!regPhone.test(callbackVal))
					{
						return false;
					}
				}
			}
		}
	}
	return true;
}

function insertAtCaret(areaId, text)
{
	var txtarea = document.getElementById(areaId);
	if (!txtarea) {
		return;
	}

	var scrollPos = txtarea.scrollTop;
	var strPos = 0;
	var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? "ff" : (document.selection ? "ie" : false));
	if (br == "ie") {
		txtarea.focus();
		var range = document.selection.createRange();
		range.moveStart('character', -txtarea.value.length);
		strPos = range.text.length;
	} else if (br == "ff") {
		strPos = txtarea.selectionStart;
	}

	var front = (txtarea.value).substring(0, strPos);
	var back = (txtarea.value).substring(strPos, txtarea.value.length);
	txtarea.value = front + text + back;
	strPos = strPos + text.length;
	if (br == "ie") {
		txtarea.focus();
		var ieRange = document.selection.createRange();
		ieRange.moveStart('character', -txtarea.value.length);
		ieRange.moveStart('character', strPos);
		ieRange.moveEnd('character', 0);
		ieRange.select();
	} else if (br == "ff") {
		txtarea.selectionStart = strPos;
		txtarea.selectionEnd = strPos;
		txtarea.focus();
	}
	txtarea.scrollTop = scrollPos;
}


function winPop(url, opts) {
   var popupName = opts.name || 'popup';
   var options = '';
   options += 'width=' + (opts.width ? opts.width : 200) + ', height=' + (opts.height ? opts.height : 200);
   options += opts.left && opts.width ? ', left=' + opts.left : ', left=' + ( (screen.availWidth - opts.width) / 2);
   options += opts.top && opts.height ? ', top=' + opts.top : ', top=' + ( (screen.availHeight - opts.height) / 2);
   options += opts.scrollbars ? ', scrollbars=' + opts.scrollbars : ', scrollbars=no';
   options += opts.resizable ? ', resizable=' + opts.resizable : ', resizable=no';
       console.log(options);
   window.open(url, popupName, options);
}

function numberWithCommas(x) {
    x = x.toString();
    var pattern = /(-?\d+)(\d{3})/;
    while (pattern.test(x))
        x = x.replace(pattern, "$1,$2");
    return x;
}

function DoCheckLang(str)
{
	return true;
	var CHECK_CHARS =  "가각간갇갈갉갊감갑값갓갔강갖갗같갚갛개객갠갤갬갭갯갰갱갸갹갼걀걋걍걔걘걜거걱건걷걸걺검겁것겄겅겆겉겊겋게겐겔겜겝겟겠겡겨격겪견겯결겸겹겻겼경곁계곈곌곕곗고곡곤곧골곪곬곯곰곱곳공곶과곽관괄괆괌괍괏광괘괜괠괩괬괭괴괵괸괼굄굅굇굉교굔굘굡굣구국군굳굴굵굶굻굼굽굿궁궂궈궉권궐궜궝궤궷귀귁귄귈귐귑귓규균귤그극근귿글긁금급긋긍긔기긱긴긷길긺김깁깃깅깆깊까깍깎깐깔깖깜깝깟깠깡깥깨깩깬깰깸깹깻깼깽꺄꺅꺌꺼꺽꺾껀껄껌껍껏껐껑께껙껜껨껫껭껴껸껼꼇꼈꼍꼐꼬꼭꼰꼲꼴꼼꼽꼿꽁꽂꽃꽈꽉꽐꽜꽝꽤꽥꽹꾀꾄꾈꾐꾑꾕꾜꾸꾹꾼꿀꿇꿈꿉꿋꿍꿎꿔꿜꿨꿩꿰꿱꿴꿸뀀뀁뀄뀌뀐뀔뀜뀝뀨끄끅끈끊끌끎끓끔끕끗끙끝끼끽낀낄낌낍낏낑나낙낚난낟날낡낢남납낫났낭낮낯낱낳내낵낸낼냄냅냇냈냉냐냑냔냘냠냥너넉넋넌널넒넓넘넙넛넜넝넣네넥넨넬넴넵넷넸넹녀녁년녈념녑녔녕녘녜녠노녹논놀놂놈놉놋농높놓놔놘놜놨뇌뇐뇔뇜뇝뇟뇨뇩뇬뇰뇹뇻뇽누눅눈눋눌눔눕눗눙눠눴눼뉘뉜뉠뉨뉩뉴뉵뉼늄늅늉느늑는늘늙늚늠늡늣능늦늪늬늰늴니닉닌닐닒님닙닛닝닢다닥닦단닫달닭닮닯닳담답닷닸당닺닻닿대댁댄댈댐댑댓댔댕댜더덕덖던덛덜덞덟덤덥덧덩덫덮데덱덴델뎀뎁뎃뎄뎅뎌뎐뎔뎠뎡뎨뎬도독돈돋돌돎돐돔돕돗동돛돝돠돤돨돼됐되된될됨됩됫됴두둑둔둘둠둡둣둥둬뒀뒈뒝뒤뒨뒬뒵뒷뒹듀듄듈듐듕드득든듣들듦듬듭듯등듸디딕딘딛딜딤딥딧딨딩딪따딱딴딸땀땁땃땄땅땋때땍땐땔땜땝땟땠땡떠떡떤떨떪떫떰떱떳떴떵떻떼떽뗀뗄뗌뗍뗏뗐뗑뗘뗬또똑똔똘똥똬똴뙈뙤뙨뚜뚝뚠뚤뚫뚬뚱뛔뛰뛴뛸뜀뜁뜅뜨뜩뜬뜯뜰뜸뜹뜻띄띈띌띔띕띠띤띨띰띱띳띵라락란랄람랍랏랐랑랒랖랗래랙랜랠램랩랫랬랭랴략랸럇량러럭런럴럼럽럿렀렁렇레렉렌렐렘렙렛렝려력련렬렴렵렷렸령례롄롑롓로록론롤롬롭롯롱롸롼뢍뢨뢰뢴뢸룀룁룃룅료룐룔룝룟룡루룩룬룰룸룹룻룽뤄뤘뤠뤼뤽륀륄륌륏륑류륙륜률륨륩륫륭르륵른를름릅릇릉릊릍릎리릭린릴림립릿링마막만많맏말맑맒맘맙맛망맞맡맣매맥맨맬맴맵맷맸맹맺먀먁먈먕머먹먼멀멂멈멉멋멍멎멓메멕멘멜멤멥멧멨멩며멱면멸몃몄명몇몌모목몫몬몰몲몸몹못몽뫄뫈뫘뫙뫼묀묄묍묏묑묘묜묠묩묫무묵묶문묻물묽묾뭄뭅뭇뭉뭍뭏뭐뭔뭘뭡뭣뭬뮈뮌뮐뮤뮨뮬뮴뮷므믄믈믐믓미믹민믿밀밂밈밉밋밌밍및밑바박밖밗반받발밝밞밟밤밥밧방밭배백밴밸뱀뱁뱃뱄뱅뱉뱌뱍뱐뱝버벅번벋벌벎범법벗벙벚베벡벤벧벨벰벱벳벴벵벼벽변별볍볏볐병볕볘볜보복볶본볼봄봅봇봉봐봔봤봬뵀뵈뵉뵌뵐뵘뵙뵤뵨부북분붇불붉붊붐붑붓붕붙붚붜붤붰붸뷔뷕뷘뷜뷩뷰뷴뷸븀븃븅브븍븐블븜븝븟비빅빈빌빎빔빕빗빙빚빛빠빡빤빨빪빰빱빳빴빵빻빼빽뺀뺄뺌뺍뺏뺐뺑뺘뺙뺨뻐뻑뻔뻗뻘뻠뻣뻤뻥뻬뼁뼈뼉뼘뼙뼛뼜뼝뽀뽁뽄뽈뽐뽑뽕뾔뾰뿅뿌뿍뿐뿔뿜뿟뿡쀼쁑쁘쁜쁠쁨쁩삐삑삔삘삠삡삣삥사삭삯산삳살삵삶삼삽삿샀상샅새색샌샐샘샙샛샜생샤샥샨샬샴샵샷샹섀섄섈섐섕서석섞섟선섣설섦섧섬섭섯섰성섶세섹센셀셈셉셋셌셍셔셕션셜셤셥셧셨셩셰셴셸솅소속솎손솔솖솜솝솟송솥솨솩솬솰솽쇄쇈쇌쇔쇗쇘쇠쇤쇨쇰쇱쇳쇼쇽숀숄숌숍숏숑수숙순숟술숨숩숫숭숯숱숲숴쉈쉐쉑쉔쉘쉠쉥쉬쉭쉰쉴쉼쉽쉿슁슈슉슐슘슛슝스슥슨슬슭슴습슷승시식신싣실싫심십싯싱싶싸싹싻싼쌀쌈쌉쌌쌍쌓쌔쌕쌘쌜쌤쌥쌨쌩썅써썩썬썰썲썸썹썼썽쎄쎈쎌쏀쏘쏙쏜쏟쏠쏢쏨쏩쏭쏴쏵쏸쐈쐐쐤쐬쐰쐴쐼쐽쑈쑤쑥쑨쑬쑴쑵쑹쒀쒔쒜쒸쒼쓩쓰쓱쓴쓸쓺쓿씀씁씌씐씔씜씨씩씬씰씸씹씻씽아악안앉않알앍앎앓암압앗았앙앝앞애액앤앨앰앱앳앴앵야약얀얄얇얌얍얏양얕얗얘얜얠얩어억언얹얻얼얽얾엄업없엇었엉엊엌엎에엑엔엘엠엡엣엥여역엮연열엶엷염엽엾엿였영옅옆옇예옌옐옘옙옛옜오옥온올옭옮옰옳옴옵옷옹옻와왁완왈왐왑왓왔왕왜왝왠왬왯왱외왹왼욀욈욉욋욍요욕욘욜욤욥욧용우욱운울욹욺움웁웃웅워웍원월웜웝웠웡웨웩웬웰웸웹웽위윅윈윌윔윕윗윙유육윤율윰윱윳융윷으윽은을읊음읍읏응읒읓읔읕읖읗의읜읠읨읫이익인일읽읾잃임입잇있잉잊잎자작잔잖잗잘잚잠잡잣잤장잦재잭잰잴잼잽잿쟀쟁쟈쟉쟌쟎쟐쟘쟝쟤쟨쟬저적전절젊점접젓정젖제젝젠젤젬젭젯젱져젼졀졈졉졌졍졔조족존졸졺좀좁좃종좆좇좋좌좍좔좝좟좡좨좼좽죄죈죌죔죕죗죙죠죡죤죵주죽준줄줅줆줌줍줏중줘줬줴쥐쥑쥔쥘쥠쥡쥣쥬쥰쥴쥼즈즉즌즐즘즙즛증지직진짇질짊짐집짓징짖짙짚짜짝짠짢짤짧짬짭짯짰짱째짹짼쨀쨈쨉쨋쨌쨍쨔쨘쨩쩌쩍쩐쩔쩜쩝쩟쩠쩡쩨쩽쪄쪘쪼쪽쫀쫄쫌쫍쫏쫑쫓쫘쫙쫠쫬쫴쬈쬐쬔쬘쬠쬡쭁쭈쭉쭌쭐쭘쭙쭝쭤쭸쭹쮜쮸쯔쯤쯧쯩찌찍찐찔찜찝찡찢찧차착찬찮찰참찹찻찼창찾채책챈챌챔챕챗챘챙챠챤챦챨챰챵처척천철첨첩첫첬청체첵첸첼쳄쳅쳇쳉쳐쳔쳤쳬쳰촁초촉촌촐촘촙촛총촤촨촬촹최쵠쵤쵬쵭쵯쵱쵸춈추축춘출춤춥춧충춰췄췌췐취췬췰췸췹췻췽츄츈츌츔츙츠측츤츨츰츱츳층치칙친칟칠칡침칩칫칭카칵칸칼캄캅캇캉캐캑캔캘캠캡캣캤캥캬캭컁커컥컨컫컬컴컵컷컸컹케켁켄켈켐켑켓켕켜켠켤켬켭켯켰켱켸코콕콘콜콤콥콧콩콰콱콴콸쾀쾅쾌쾡쾨쾰쿄쿠쿡쿤쿨쿰쿱쿳쿵쿼퀀퀄퀑퀘퀭퀴퀵퀸퀼큄큅큇큉큐큔큘큠크큭큰클큼큽킁키킥킨킬킴킵킷킹타탁탄탈탉탐탑탓탔탕태택탠탤탬탭탯탰탱탸턍터턱턴털턺텀텁텃텄텅테텍텐텔템텝텟텡텨텬텼톄톈토톡톤톨톰톱톳통톺톼퇀퇘퇴퇸툇툉툐투툭툰툴툼툽툿퉁퉈퉜퉤튀튁튄튈튐튑튕튜튠튤튬튱트특튼튿틀틂틈틉틋틔틘틜틤틥티틱틴틸팀팁팃팅파팍팎판팔팖팜팝팟팠팡팥패팩팬팰팸팹팻팼팽퍄퍅퍼퍽펀펄펌펍펏펐펑페펙펜펠펨펩펫펭펴편펼폄폅폈평폐폘폡폣포폭폰폴폼폽폿퐁퐈퐝푀푄표푠푤푭푯푸푹푼푿풀풂품풉풋풍풔풩퓌퓐퓔퓜퓟퓨퓬퓰퓸퓻퓽프픈플픔픕픗피픽핀필핌핍핏핑하학한할핥함합핫항해핵핸핼햄햅햇했행햐향허헉헌헐헒험헙헛헝헤헥헨헬헴헵헷헹혀혁현혈혐협혓혔형혜혠혤혭호혹혼홀홅홈홉홋홍홑화확환활홧황홰홱홴횃횅회획횐횔횝횟횡효횬횰횹횻후훅훈훌훑훔훗훙훠훤훨훰훵훼훽휀휄휑휘휙휜휠휨휩휫휭휴휵휸휼흄흇흉흐흑흔흖흗흘흙흠흡흣흥흩희흰흴흼흽힁히힉힌힐힘힙힛힝";
	CHECK_CHARS += "　！'，．／：；？＾＿｀｜￣、。·‥…¨〃­―∥＼～´?ˇ˘˝˚˙¸˛¡¿ː＂（）［］｛｝‘’“”〔〕〈〉《》「」『』【】+－＜=＞±×÷≠≤≥∞∴♂♀∠⊥⌒∂∇≡≒≪≫√∽∝∵∫∬∈∋⊆⊇⊂⊃∪∩∧∨￢⇒⇔∀∃∮∑∏＄％￦Ｆ′″℃Å￠￡￥¤℉‰㎕㎖㎗ℓ㎘㏄㎣㎤㎥㎦㎙㎚㎛㎜㎝㎞㎟㎠㎡㎢㏊㎍㎎㎏㏏㎈㎉㏈㎧㎨㎰㎱㎳㎴㎵㎶㎷㎸㎹㎀㎁㎂㎃㎄㎺㎻㎼㎽㎾㎿㎐㎑㎒㎓㎔Ω㏀㏁㎊㎋㎌㏖㏅㎭㎭㎮㎯㏛㎩㎪㎫㎬㏝㏐㏓㏃㏉㏜㏆＃＆＊＠■※☆★○●◎◇◆□■△▲▽▼→←↑↓↔〓◁◀▷▶♤♠♡♥♧♣⊙◈▣◐◑▒▤▥▨▧▦▩♨☏☎☜☞■†‡↕↗↙↖↘♭♩♪♬㉿㈜№㏇™㏂㏘℡■■─│┌┐┘└├┬┤┴┼━┃┏┓┛┗┣┳┫┻╋┠┯┨┷┿┝┰┥┸╂┒┑┚┙┖┕┎┍┞┟┡┢┦┧┩┪┭┮┱┲┵┶┹┺┽┾╀╁╃╄╅╆╇╈╉╊㉠㉡㉢㉣㉤㉥㉦㉧㉨㉩㉪㉫㉬㉭㉮㉯㉱㉲㉳㉴㉵㉶㉷㉸㉹㉺㉻㈀㈁㈂㈃㈄㈅㈆㈇㈈㈉㈊㈋㈌㈍㈎㈏㈐㈑㈒㈓㈔㈕㈖㈗㈘㈙㈚㈛ⓐⓑⓒⓓⓔⓕⓖⓗⓘⓙⓚⓛⓜⓝⓞⓟⓠⓡⓢⓣⓤⓥⓦⓧⓨⓩ①②③④⑤⑥⑦⑧⑨⑩⑪⑫⑬⑭⑮⒜⒝⒞⒟⒠⒡⒢⒣⒤⒥⒦⒧⒨⒩⒪⒫⒬⒭⒮⒯⒰⒱⒲⒳⑻⒵⑴⑵⑶⑷⑸⑹⑺⒴⑼⑽⑾⑿⒀⒁⒂ⅰⅱⅲⅳⅴⅵⅶⅷⅸⅹⅠⅡⅢⅣⅤⅥⅦⅧⅨⅩ½⅔⅔¼¾⅛⅜⅝⅞¹²³⁴ⁿ₁₂₃₄ㄱㄲㄳㄴㄵㄶㄷㄸㄹㄺㄻㄼㄽㄾㄿㅀㅁㅂㅃㅄㅅㅆㅇㅈㅉㅊㅋㅌㅍㅎㅏㅐㅑㅒㅓㅔㅕㅖㅗㅘㅙㅚㅛㅜㅝㅞㅟㅠㅡㅢㅣㅥㅦㅧㅨㅩㅪㅫㅬㅭㅮㅯㅰㅱㅲㅳㅴㅵㅶㅷㅸㅹㅺㅻㅼㅽㅾㅿㆀㆁㆂㆃㆄㆅㆆㆇㆈㆉㆊㆋㆌㆍㆎＡＢＣＤＥＦＧＨＩＪＫＬＭＮＯＰＱＲＳＴＵＶＷＸＹＺａｂｃｄｅｆｇｈｉｊｋｌｍｎｏｐｑｒｓｔｕｖｗｘｙｚㅍΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩαβγδεζηθικλμνξοπρστυφχψω";
	CHECK_CHARS += "ㄱㄲㄴㄷㄸㄹㅁㅂㅃㅅㅆㅇㅈㅉㅊㅋㅌㅍㅎ";
	CHECK_CHARS += "ㅏㅑㅓㅕㅗㅛㅜㅠㅡㅣ";
	CHECK_CHARS += "ァィゥェォアイウエオカキクケコガギグゲゴサシスセソザジズゼゾタチッツテトダヂヅデドナニヌネノハヒフヘホバビブベボパピプペポマミムメモャヤュユョヨラリルレロヮワヰヱヲンヴヵヶ";
	CHECK_CHARS += "ぁぃぅぇぉあいうえおかきくけこがぎぐげごさしすせそざじずぜぞたちっつてとだぢづでどなにぬねのはひふへほばびぶべぼぱぴぷぺぽまみむめもゃやゅゆょよらりるれろゎわゐゑをん";
	CHECK_CHARS += "🫵🫵🖕⚽⚾🥎🏀🏐🏈🏉🎾🥏🏅🎖🎫🎟🎁♥0⃣1⃣2⃣3⃣4⃣5⃣6⃣7⃣🆔🅿🇰🇷®©🔼▶◀🛜⏏⬅⬆ⓕⓚ";


	var inText = str;
	var ret;
	var chr;

	for (var i = 0; i < inText.length; i++)
	{
		ret = inText.charCodeAt(i);

		if (ret > 127)
		{
			chr = inText.charAt(i);

			if(CHECK_CHARS.indexOf(chr) < 0)
			{

				alert ( "휴대폰에서 지원하지 않는 문자가 있습니다.\n" + chr + "\n\n메시지 내용을 '붙여넣기'하는 경우 발생될 수 있으므로,\n직접입력 혹은 메모장에서 재편집 후 발송하기 바랍니다." );
				return false;
			}
		}
	}
	return true;
}
