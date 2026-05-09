var _send_lock = false;
var target_count = 0;
var last_clicked_form = null;
/*
function mouse_block()
{
    return false;
}
document.oncontextmenu = mouse_block;
document.ondragstart   = mouse_block;
document.onselectstart = mouse_block;
*/
function sendModeSet( num )
{
	_sendset = num;
	var stype = document.getElementsByName( "send_type" );
	stype[_sendset].checked = true;

	var vtype = document.getElementById( "votetype" ).value;
	if( vtype == "V" )
	{
		var vment = '선거운동정보';
		var vment2 = '\n불법수집정보신고 118';
	} else {
		var vment = '광고';
		var vment2 = '';
	}

	var bytes = document.getElementById( "_sms_bytes" ).value;
	if( bytes != '0')
	{
		if( confirm( "전송모드를 변경할 경우 기존 메시지 내용이 삭제됩니다.\n변경하시겠습니까?" ) == false ) { return; }
	}
	var num080 = document.getElementById( "num080" ).value;

	if( _sendset == 0 )
	{
		smode_0.style.background = "#fa7034";
		smode_1.style.background = "#eeeeee";
		sfont_0.className = "fs_ffffff";
		sfont_1.className = "fs_666666";
		document.getElementById( "_sms_msg" ).value = "";
//		layerCloseNew( '080_layer' );
	} else if( _sendset == 1 ) {
		smode_0.style.background = "#eeeeee";
		smode_1.style.background = "#fa7034";
		sfont_0.className = "fs_666666";
		sfont_1.className = "fs_ffffff";
		if(	vtype == "V" ) document.getElementById( "_sms_msg" ).value = "(" + vment + ") \n\n-- 전달 내용은 여기에 --\n\n무료거부 " + num080 + vment2;
		else document.getElementById( "_sms_msg" ).value = "(" + vment + ")업체명\n\n\n-- 전달 내용은 여기에 --\n\n\n무료거부 " + num080 + vment2;
		if( num080 == '080-000-0000' )
		{
			Layer080();
		}
	}
	countMsgBody();
}

function sendMessage()
{
	if( _send_lock != false )
	{
		alert( "발송 진행중입니다. 잠시만 기다려주세요." );
		return true;
	}

	var form = document.message;
	var _mtype = document.getElementById( 'msgtype' ).value;

	form.lms_subject.value = form.lms_subject.value.trim();
	if( _mtype != 0 && form.lms_subject.value.bytes() > 60 ) { alert( "메세지 제목은 60바이트(한글30자)를 초과하실 수 없습니다." ); form.lms_subject.focus(); return; }

	var _sms_msg = document.getElementById( "_sms_msg" );
	var _sum_msg = _sms_msg.value.bytes2();
	if( _sum_msg < 1 ) { alert( "발송하실 메세지가 없습니다." ); _sms_msg.focus(); return; }
	countMsgBody();

	var send_targets = document.message.send_targets;
	if( send_targets.length < 1 ) { alert( "수신자가 없습니다." ); return; }

	if( _mtype == 2 )
	{
		var _img_name1 = document.getElementById( "_img_name1" ).value;
		var _img_capa1 = parseInt(document.getElementById( "h_img_capa1" ).value);
		if( _img_name1 == '' || _img_capa1 == 0 ) { alert( "발송하실 이미지가 없습니다." ); return; }
		if( _img_capa1 >= 500000 ) { alert( "전송가능 이미지 크기를 초과합니다." ); return; }
	}

	if( form.divide.value == 'Y' )
	{
		var div_cnt = document.getElementById( "div_count" ).value;
		var div_count = parseInt(document.getElementById( "div_count" ).value);
		if( div_cnt == '' || div_count < 100 || div_count > 3000 ) { alert( "분할전송 단위 건수를\n100건 ~ 3000건 사이로 설정해 주세요." ); return; }
		var div_min = document.getElementById( "div_minute" ).value;
		var div_minute = parseInt(document.getElementById( "div_minute" ).value);
		if( div_min == '' || div_minute < 3 || div_minute > 60 ) { alert( "분할전송 시간 간격을\n3분 이상 60분 이내로 설정해 주세요." ); return; }
	}

	var i = form.callback.options.selectedIndex;
	if( form.callback.options[i].value.bytes() < 2 ) { alert( "발신번호를 선택해 주세요." ); return; }

	var send_queue = "";
	for( var i = 0; i < send_targets.length; i++ )
	{
		send_queue += send_targets[i].value + "\n";
	}
	_send_lock = true;

	form.msg_body.value = _sms_msg.value;
	form._send_targets.value = send_queue;
	form.mode.value = "confirm";
	form.submit();
}

function adde2( td )
{
	var str = td.innerHTML;
	insertAtCaret('_sms_msg', str);
	countMsgBody();
}

function addrwLayerOpen()
{
	var obj = document.getElementById("addrbak");
	obj.style.width = document.body.scrollWidth + 'px';
	obj.style.height = document.body.scrollHeight + 'px';
	obj.style.display = "block";
	var ef = document.getElementById( "i_addr_layer" );
	ef.src ="/SmartAddressM.php?mode=init";
}

// function msgLayerOpen()
// {
// 	var obj = document.getElementById("msgbak");
// 	obj.style.width = document.body.scrollWidth + 'px';
// 	obj.style.height = document.body.scrollHeight + 'px';
// 	obj.style.display = "block";
// 	var ef = document.getElementById( "i_msg_layer" );
// 	ef.src ="/SmartUtils.php?mode=savedmmsg";
// }

function imgLayerOpen( seq )
{
	var obj = document.getElementById("imgbak");
	obj.style.width = document.body.scrollWidth + 'px';
	obj.style.height = document.body.scrollHeight + 'px';
	obj.style.display = "block";
	var ef = document.getElementById( "i_img_layer" );
	ef.src ="/SmartUtils.php?mode=mmsimg&seq=" + seq;
}

function receiveEmoticon( msg )
{
	if( msg.trim().bytes() < 1 ) return false;
	var _sms_msg = document.getElementById( "_sms_msg" );
	_sms_msg.value = msg;

	countMsgBody();
	layerClosing('msgbak');
}

function insert_img_name( img_name, seq, capa )
{
	var _img_name_t = document.getElementById( "_img_name_t" );
	var _img_name1 = document.getElementById( "_img_name1" );
	var h_img_capa1 = document.getElementById( "h_img_capa1" );
	var ext = img_name.slice(img_name.lastIndexOf(".") + 1).toLowerCase();
    if (ext != "jpg") {
		alert( "웹발송의 경우 jpg 이미지만 가능합니다." );
        return false;
    }
	if( capa >= 500000 )
	{
		alert( "발송가능한 이미지 용량을 초과합니다." );
		return false;
	}
	_img_name_t.value = "업로드 완료 [VIEW]";
	_img_name1.value = img_name;
	h_img_capa1.value = capa;
	layerClosing('imgbak');
}

function layerClosing( layer )
{
	var obj = document.getElementById(layer);
	obj.style.display = "none";
}

function charLayerOpen( _layer, _base )
{
	var _target = document.getElementById( _layer );
	var _location_base = document.getElementById( _base );
	var _a = getOffsetPosition( _location_base );
	layerOpenLocationNew( "ext_char_layer", _a[0] - 160, _a[1] + 10 );
	displayCharSET( 0 );
}

function guideLayer(layer)
{
	var _location_base = document.getElementById( "base_9" );
	var _a = getOffsetPosition( _location_base );
	layerOpenLocationNew( layer, _a[0] + 80, _a[1] - 1 );
}

function layerCloseNew( _layer )
{
	var _target = document.getElementById( _layer );
	_target.style.display = "none";
	return _target;
}

function displayCharSET( num )
{
	_eset = num;
	var _ev = document.getElementById( "ech_panel_body_" + _eset );
	var _edisp = document.getElementById( "emoticonset" );
	if( _eset == 0 )
	{
		emenu_0.style.background = "#DDEAF9";
		emenu_1.style.background = "#F2F8FD";
		emenu_2.style.background = "#F2F8FD";
		emenu2_0.style.color = "#003399";
		emenu2_1.style.color = "#4B4B4B";
		emenu2_2.style.color = "#4B4B4B";
	} else if( _eset == 1 ) {
		emenu_0.style.background = "#F2F8FD";
		emenu_1.style.background = "#DDEAF9";
		emenu_2.style.background = "#F2F8FD";
		emenu2_0.style.color = "#4B4B4B";
		emenu2_1.style.color = "#003399";
		emenu2_2.style.color = "#4B4B4B";
	} else if( _eset ==2 ) {
		emenu_0.style.background = "#F2F8FD";
		emenu_1.style.background = "#F2F8FD";
		emenu_2.style.background = "#DDEAF9";
		emenu2_0.style.color = "#4B4B4B";
		emenu2_1.style.color = "#4B4B4B";
		emenu2_2.style.color = "#003399";
	}
	_edisp.innerHTML = _ev.innerHTML;
}

function layerOpenLocationNew( _layer, X, Y )
{
	var _target = document.getElementById( _layer );
	_target.style.display = "inline";
	_target.style.position = "absolute";
	_target.style.top = Y + "px";
	_target.style.left = X + "px";
	return _target;
}

function DivideCheck(div)
{
	var form = document.message;
	if ( div == "N" ) {
		form.div_min.disabled =true;
		form.div_cnt.disabled =true;
		form.div_min.value = '';
		form.div_cnt.value = '';
		layerCloseNew( 'd_layer' );
	} else {
		form.div_min.disabled =false;
		form.div_cnt.disabled =false;
		var _location_base = document.getElementById( "base_1" );
		var _a = getOffsetPosition( _location_base );
		layerOpenLocationNew( "d_layer", _a[0] + 555, _a[1] - 45 );
	}
}

function ReserveCheck(gb)
{
	var form = document.message;
	if ( gb == "N" ) {
		form.rsv_year.disabled =true;
		form.rsv_month.disabled =true;
		form.rsv_day.disabled =true;
		form.rsv_hour.disabled =true;
		form.rsv_minute.disabled =true;
	} else {
		form.rsv_year.disabled =false;
		form.rsv_month.disabled =false;
		form.rsv_day.disabled =false;
		form.rsv_hour.disabled =false;
		form.rsv_minute.disabled =false;
	}
}

function sendSET( num )
{
	var _sms_acc = document.getElementById( 'sms_acc' );
	var _lms_acc = document.getElementById( 'lms_acc' );
	var _mms_acc = document.getElementById( 'mms_acc' );
	var _title = document.getElementById( 'msg_title' );
	var _length = document.getElementById( 'max_length' );
	var _mms_col = document.getElementById( 'mms_col' );
	var _mmsimg = document.getElementById( 'mmsimg' );
	_eset = num;
	if( _eset == 0 )
	{
		smenu_0.style.background = "#FFFFFF";
		smenu_1.style.background = "#e3e7f1";
		smenu_2.style.background = "#e3e7f1";
		smenu2_0.style.color = "#ff6700";
		smenu2_1.style.color = "#4B4B4B";
		smenu2_2.style.color = "#4B4B4B";
		smenu3_0.style.background = "#ff6700";
		smenu3_1.style.background = "#E7E7E7";
		smenu3_2.style.background = "#E7E7E7";
		_sms_acc.style.display = "block";
		_lms_acc.style.display = "none";
		_mms_acc.style.display = "none";
		_title.style.display = "none";
		_length.innerHTML = '90';
		_mms_col.style.display = "none";
		_mmsimg.style.display = "none";
	} else if( _eset == 1 ) {
		smenu_0.style.background = "#e3e7f1";
		smenu_1.style.background = "#FFFFFF";
		smenu_2.style.background = "#e3e7f1";
		smenu2_0.style.color = "#4B4B4B";
		smenu2_1.style.color = "#ff6700";
		smenu2_2.style.color = "#4B4B4B";
		smenu3_0.style.background = "#E7E7E7";
		smenu3_1.style.background = "#ff6700";
		smenu3_2.style.background = "#E7E7E7";
		_sms_acc.style.display = "none";
		_lms_acc.style.display = "block";
		_mms_acc.style.display = "none";
		_title.style.display = "block";
		_length.innerHTML = '2000';
		_mms_col.style.display = "none";
		_mmsimg.style.display = "none";
	} else if( _eset == 2 ) {
		smenu_0.style.background = "#e3e7f1";
		smenu_1.style.background = "#e3e7f1";
		smenu_2.style.background = "#FFFFFF";
		smenu2_0.style.color = "#4B4B4B";
		smenu2_1.style.color = "#4B4B4B";
		smenu2_2.style.color = "#ff6700";
		smenu3_0.style.background = "#E7E7E7";
		smenu3_1.style.background = "#E7E7E7";
		smenu3_2.style.background = "#ff6700";
		_sms_acc.style.display = "none";
		_lms_acc.style.display = "none";
		_mms_acc.style.display = "block";
		_title.style.display = "block";
		_length.innerHTML = '2000';
		_mms_col.style.display = "block";
		_mms_col.innerHTML = '이미지선택';
		_mmsimg.style.display = "block";
	}
	var _mtype = document.getElementById( 'msgtype' );
	_mtype.value = _eset;
	countMsgBody();
}

function touchSMSform( lastform )
{
	last_clicked_form = lastform;
}

function displaycnt( count )
{
	target_count = parseInt( count );
	displayCount();
}

function displayCount()
{
	var tc = document.getElementById( "target_count" );
//	var send_targets = document.message.send_targets;
	tc.innerHTML = target_count;
}

function checkPhoneBox( obj, mode )
{
	if( mode == "F" )
	{
		if( obj.value != "<<핸드폰 번호 입력>>" ) return;
		obj.value = "";
		obj.style.color = "#000000";
	} else {
		if( obj.value.trim() == "" )
		{
			obj.style.color = "#ACACAC";
			obj.value = "<<핸드폰 번호 입력>>";
		}
	}
}

function addItem( type, no, text, count, extra )
{
	var send_targets = document.message.send_targets;
	if( count < 1 ) return false;
	if( type == "G" )
	{
		for( var i = 0; i < send_targets.options.length; i++ )
		{
			var spl = send_targets.options[i].value.split( "|" );
			if( spl[0] == "G" && spl[1] == no ) return false;
		}

		text += " (" + count + "명)";
	} else if( type == "M" ) {
		for( var i = 0; i < send_targets.options.length; i++ )
		{
			var spl = send_targets.options[i].value.split( "|" );
			if( spl[0] == "M" && spl[1] == no ) return false;
		}

		text += " (" + count + "명)";
	} else if( type == "P" ) {
		for( var i = 0; i < send_targets.options.length; i++ )
		{
			var spl = send_targets.options[i].value.split( "|" );
			if( spl[0] == "P" && spl[1] == no ) return false;  //중복제거
		}
	} else if( type == "S" ) {
		text += " (" + count + "명 선택)";
	}

	var newItem = new Option;
	newItem.text = text;
	if( type == "S" ) newItem.value = type + "|" + no + "|" + count + "|" + extra;
	else if( type == "P" ) newItem.value = type + "|" + no + "|" + count + "||" + extra;
	else newItem.value = type + "|" + no + "|" + count;

	send_targets.options[send_targets.length] = newItem;
	target_count += parseInt( count );
	displayCount();
}

function addTarget( type, no, text, count, extra )
{
	var send_targets = document.message.send_targets;
	if( count < 1 ) return false;
	for( var i = 0; i < send_targets.options.length; i++ )
	{
		var spl = send_targets.options[i].value.split( "|" );
		if( spl[1] == no ) 	return false; // 중복입력 차단
	}
	var newItem = new Option;
	newItem.text = text;
	newItem.value = type + "|" + no + "|" + count + "||" + extra;
	send_targets.options[send_targets.length] = newItem;
	target_count += parseInt( count );
	displayCount();
}

function addReceiveNumber2()
{
	var p_mobile = document.message.p_mobile;

	if( p_mobile.value != "<<핸드폰 번호 입력>>" )
	{
		if( isMobileNumber( p_mobile.value ) == false )
		{
			alert( "휴대폰 번호가 아닙니다. 다시 확인해주세요." );
			return false;
		}
	} else return true;

	addTarget( "P", p_mobile.value.trim(), p_mobile.value.trim(), 1, "" );

	p_mobile.value = "";
	p_mobile.focus();

	return true;
}

function removeItem()
{
	var send_targets = document.message.send_targets;
	if( send_targets.selectedIndex < 0 )
	{
		alert( "삭제하실 주소록 또는 개별주소를 선택해 주세요." );
		return;
	}

	for( var i = send_targets.options.length - 1; i >= 0; i-- )
	{
		if( send_targets.options[i] == null || send_targets.options[i].selected != true ) continue;

		var delobj = send_targets.options[i].value;
		var splobj = delobj.split( "|" );
		send_targets.options[i] = null;

		target_count = target_count + ( splobj[2] * -1 );
		displayCount();
	}
}

function addPersonalNumber()
{
	var p_mobile = document.message.p_mobile;
	if( p_mobile.value != "<<핸드폰 번호 입력>>" )
	{
		if( isMobileNumber( p_mobile.value ) == false )
		{
			alert( "휴대폰 번호가 아닙니다. 다시 확인해주세요." );
			return false;
		}
	} else return true;

	addItem( "P", p_mobile.value.trim(), p_mobile.value.trim(), 1, "" );

	p_mobile.value = "";
	p_mobile.focus();
	return true;
}

function countMsgBody()
{
	var _mtype = document.getElementById( 'msgtype' ).value;
	if( _mtype == 1 || _mtype == 2 ) var limit_byte = 2000;
	else var limit_byte = 90;

	var _sms_msg = document.getElementById( "_sms_msg" );
	var _sms_bytes = document.getElementById( "_sms_bytes" );
	var msg = _sms_msg.value;

	var bytes = msg.bytes2();
	if( bytes > limit_byte )
	{
		var l = 0;
		for( var i = 0; i < msg.length; i++ )
		{
			if( msg.charCodeAt( i ) == 13 ) continue;
			l += ( msg.charCodeAt( i ) > 128 ) ? 2 : 1;
			if( l > limit_byte )
			{
				msg = msg.substring( 0, i );
				alert( limit_byte + "바이트까지 입력하실 수 있습니다." );
				break;
			}
		}
		_sms_msg.value = msg;
		bytes = msg.bytes2();
	}
	 _sms_bytes.value = bytes;
}

function viewBigImg_fn()
{
	var img = document.getElementById("_img_name1").value;
	if( img == '' ) return false;
	var obj = document.getElementById("bak");
	obj.style.width = document.body.scrollWidth + 'px';
	obj.style.height = document.body.scrollHeight + 'px';
	obj.style.display = "block";
	document.all.bigimg.src=img;
}

function viewnoti(type)
{
	var obj = document.getElementById("bak");
	obj.style.width = document.body.scrollWidth + 'px';
	obj.style.height = document.body.scrollHeight + 'px';
	obj.style.display = "block";
	document.all.bigimg.src='../image/'+ type + '.jpg';
}

function hiddenBigImg_fn()
{
	var obj = document.getElementById("bak");
	obj.style.display = "none";
}

function Layer080()
{
	var _location_base = document.getElementById( "smode_1" );
	var _a = getOffsetPosition( _location_base );
	layerOpenLocationNew( "080_layer", _a[0] + 80, _a[1] - 1 );
}
