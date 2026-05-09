<html>
<head>
<meta name="robots" content="noindex">
<script type="text/javascript">
	var move_page_url = "/kmcis/result_update";
	function end() {
	   	// 결과 페이지 경로 셋팅
    	document.kmcis_form.action = move_page_url;

   		var UserAgent = navigator.userAgent;
    	/* 모바일 접근 체크*/
    	// 모바일일 경우 (변동사항 있을경우 추가 필요)
    	if (UserAgent.match(/iPhone|iPod|Android|Windows CE|BlackBerry|Symbian|Windows Phone|webOS|Opera Mini|Opera Mobi|POLARIS|IEMobile|lgtelecom|nokia|SonyEricsson/i) != null || UserAgent.match(/LG|SAMSUNG|Samsung/) != null) {
		    document.kmcis_form.submit();
	  	}
	  	// 모바일이 아닐 경우
	  	else {
			document.kmcis_form.target = opener.window.name;
		  	document.kmcis_form.submit();
   		  	self.close();
	  	}
	}
</script>
</head>
<body onload="javascript:end()">

<?php
$attributes = array(
    'id' => 'kmcis_form',
    'name' => 'kmcis_form',
);
echo form_open('kmcis/result_update', $attributes);
?>
	<input type="hidden"	name="rec_cert"		id="rec_cert"	value="<?php echo $rec_cert ?>"/>
	<input type="hidden"	name="certNum"		id="certNum"	value="<?php echo $certNum ?>"/>
</form>
</body>
</html>