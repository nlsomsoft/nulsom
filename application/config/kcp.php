<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$base_url  = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$base_url .= "://".$_SERVER['HTTP_HOST'].'/';

$config['g_conf_home_dir'] = $_SERVER['DOCUMENT_ROOT'].'/kcp';
$config['g_conf_site_cd'] = KCP_SITE_CD;
$config['g_conf_web_siteid'] = KCP_WEB_SITEID;
$config['g_conf_ENC_KEY'] = KCP_ENC_KEY;
$config['g_conf_Ret_URL'] = $base_url.'kcp/result';
$config['g_conf_gw_url'] = 'https://cert.kcp.co.kr/kcp_cert/cert_view.jsp';