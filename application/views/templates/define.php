<?php
//css, js version 관리
defined('CSS_JS_INST')      OR define('CSS_JS_INST', 2407024);

if ($_SERVER['STORENAME'] == 'nulsom') {
	defined('MAIN_BANNER_CNT') OR define('MAIN_BANNER_CNT', '1');
	defined('STORE_080EXT_USEAGE_YN') OR define('STORE_080EXT_USEAGE_YN', 'N');
	defined('PG_USEAGE_YN') OR define('PG_USEAGE_YN', 'N');
	defined('TITLE') OR define('TITLE', '늘솜');
	defined('BRAND') OR define('BRAND', 'NULSOM');
	defined('DOMAIN') OR define('DOMAIN', 'www.zzonga.com');
	// defined('CALLCENTER') OR define('CALLCENTER', '02-2688-0035');
	defined('CALLCENTER') OR define('CALLCENTER', '02-542-8528');
	defined('COMPANY_NAME') OR define('COMPANY_NAME', '주식회사 늘솜소프트');
	defined('CEO_NAME') OR define('CEO_NAME', '신상인');
	defined('COPYRIGHT') OR define('COPYRIGHT', 'ZZONGA.COM');
	defined('BUSINESS_NO') OR define('BUSINESS_NO', '405-86-01979');
	defined('BUSINESS_TYPE') OR define('BUSINESS_TYPE', '2021-서울마포-1261');
	defined('ADDRESS') OR define('ADDRESS', '서울시 마포구 마포대로 173 (공덕동, 마포현대하이엘 1516호)');
	defined('FAX_NO') OR define('FAX_NO', '');
	defined('EMAIL') OR define('EMAIL', 'zzong_helper@naver.com');
	defined('BANK') OR define('BANK', 'IBK기업은행');
	defined('ACCOUNT') OR define('ACCOUNT', '674-030476-04-012');
	defined('ACCOUNT_NAME') OR define('ACCOUNT_NAME', '주식회사 늘솜소프트');
	// defined('KCP_SITE_CD') OR define('KCP_SITE_CD', 'A8X5W');
	// defined('KCP_WEB_SITEID') OR define('KCP_WEB_SITEID', 'J20022804173');
	defined('KCP_SITE_CD') OR define('KCP_SITE_CD', 'ABKNZ');
	defined('KCP_WEB_SITEID') OR define('KCP_WEB_SITEID', 'J21032206594');
	defined('KCP_ENC_KEY') OR define('KCP_ENC_KEY', 'd7c9da8e43390c8d3d266c305b40a947cdf608f9158311d8b23074daad5ef571');
	defined('SESS_COOKIE_NAME') OR define('SESS_COOKIE_NAME', 'nulsom4session');
	//주의: 결과 테이블 추가 시 수정할 것
	defined('MIN_RESULT_CNT') OR define('MIN_RESULT_CNT', '1');
	defined('MAX_RESULT_CNT') OR define('MAX_RESULT_CNT', '1');
	defined('SENT_MEMORY_YN') OR define('SENT_MEMORY_YN', 'Y');
	defined('CALLBACK_KCP_AUTH_YN') OR define('CALLBACK_KCP_AUTH_YN', 'Y');
	defined('CALLBACK_MOBILE_AUTH_YN') OR define('CALLBACK_MOBILE_AUTH_YN', 'Y');
	defined('GROUP_USE_YN') OR define('GROUP_USE_YN', 'Y');
	defined('DATABASE_NAME') OR define('DATABASE_NAME', 'nulsom');
	defined('RESTRICT_SENDING_TIME_YN') OR define('RESTRICT_SENDING_TIME_YN', 'Y');
	defined('SENDING_TIME_START_HOUR') OR define('SENDING_TIME_START_HOUR', '8');
	defined('SENDING_TIME_END_HOUR') OR define('SENDING_TIME_END_HOUR', '21');
	defined('EXCEPT_SENDNAME_YN') OR define('EXCEPT_SENDNAME_YN', 'N');

	$PHONE_080_LIST = array(
		'0801361110' => '080-136-1110',
	);

	$LOTTO_CONFIRM_LIST = array(
		'01087183637',
		'01063022064',
		'01099087161',
	);

}
