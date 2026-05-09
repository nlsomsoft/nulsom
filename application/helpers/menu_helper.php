<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('get_category_list')) {
function get_category_list($cate_name, $level) {
    if ($cate_name == 'user') {
        if ($level == '9') {
            $menu_array = array(
                '회원관리' => '/admuser/users',
                '차단/탈퇴관리' => '/admuser/block_users',
                '상점관리' => '/admuser/store_list',
                '그룹관리' => '/admuser/group_list',
                '발신번호관리' => '/admuser/cblist',
                '발신번호관리(차단) <span style="color:red;font-weight:bold;font-size:10px;">NEW</span>' => '/admuser/cblist_ban',
                '통신가입증명원관리 <span style="color:red;font-weight:bold;font-size:10px;">NEW</span>' => '/admuser/cbflist',
                '080차단관리' => '/admuser/phone080',
                '서비스이용내역 <span style="color:red;font-weight:bold;font-size:10px;">NEW</span>' => '/admuser/user_bill',
            );
        }
        else if ($level == '5') {
            $menu_array = array(
                '회원관리' => '/admuser/users',
                '차단/탈퇴관리' => '/admuser/block_users',
                '그룹관리' => '/admuser/group_list',
                '발신번호관리' => '/admuser/cblist',
                '발신번호관리(차단) <span style="color:red;font-weight:bold;font-size:10px;">NEW</span>' => '/admuser/cblist_ban',
                '통신가입증명원관리 <span style="color:red;font-weight:bold;font-size:10px;">NEW</span>' => '/admuser/cbflist',  
                '080차단관리' => '/admuser/phone080',
                '서비스이용내역 <span style="color:red;font-weight:bold;font-size:10px;">NEW</span>' => '/admuser/user_bill',
            );
        }
        else {
            $menu_array = array(
                '회원관리' => '/admuser/users',
                '차단/탈퇴관리' => '/admuser/block_users',
                '서비스이용내역 <span style="color:red;font-weight:bold;font-size:10px;">NEW</span>' => '/admuser/user_bill',
            );
        }
    }
    else if ($cate_name == 'send') {
        if ($level == '9' || $level == '5') {
            $menu_array = array();
            $menu_array['발송관리'] = '/admsend/list';
            $menu_array['발송관리 (대량)'] = '/admsend/list1';
            $menu_array['예약관리'] = '/admsend/reserve';
            if (CONFIRM_SMS_YN == 'Y') $menu_array['발송관리 (인증)'] = '/admsend/confirm';
            $menu_array['필터링'] = '/admsend/filter';
        } else {
            $menu_array = array(
                '발송관리' => '/admsend/list',
                '발송관리 (대량)' => '/admsend/list1',
                '예약관리' => '/admsend/reserve',
            );
        }
    }
    else if ($cate_name == 'stats') {
        if ($level == '9') {
            $menu_array = array(
                '발송통계 (일별)' => '/admstats/send_dd',
                '발송통계 (월별)' => '/admstats/send_mm',
                '발송통계 (업체별) <span style="color:red;font-weight:bold;font-size:10px;">NEW</span>' => '/admstats/all_send_dd',
                '발송통계 (채널별)' => '/admstats/send_channel',
                '결제통계 (일별)' => '/admstats/bank_dd',
                '결제통계 (월별)' => '/admstats/bank_mm',
            );
        }
        else if ($level == '5') {
            $menu_array = array(
                '발송통계 (일별)' => '/admstats/send_dd',
                '발송통계 (월별)' => '/admstats/send_mm',
                '발송통계 (채널별) <span style="color:red;font-weight:bold;font-size:10px;">NEW</span>' => '/admstats/send_channel',
                '결제통계 (일별)' => '/admstats/bank_dd',
                '결제통계 (월별)' => '/admstats/bank_mm',
            );
        }
        else {
            $menu_array = array(
                '발송통계 (일별)' => '/admstats/send_dd',
                '발송통계 (월별)' => '/admstats/send_mm',
                '발송통계 (채널별) <span style="color:red;font-weight:bold;font-size:10px;">NEW</span>' => '/admstats/send_channel',
                '결제통계 (일별)' => '/admstats/bank_dd',
                '결제통계 (월별)' => '/admstats/bank_mm',
            );
        }
    }
    else if ($cate_name == 'bill') {
        if ($level == '9') {
            $menu_array = array(
            );
        }
        else if ($level == '5') {
            $menu_array = array(
                '무통장관리' => '/admbill/bankbook',
                '결제관리' => '/admbill/pay_list',
            );
        }
        else {
            $menu_array = array(
                '결제관리' => '/admbill/pay_list',
            );
        }
    }
    else if ($cate_name == 'setting') {
        if ($level == '9') {
            $menu_array = array();
            $menu_array['신고건조회'] = '/admsetting/report';
            if (CONFIRM_SMS_YN == 'Y') $menu_array['인증번호 (관리자)'] = '/admsetting/mobile';
            $menu_array['모니터링'] = '/admsetting/monitor';
            $menu_array['공지사항'] = '/admsetting/notice';
            $menu_array['관리자인증번호 <span style="color:red;font-weight:bold;font-size:10px;">NEW</span>'] = '/admsetting/auth_num';
        }
        else if ($level == '5') {
            $menu_array = array();
            $menu_array['신고건조회'] = '/admsetting/report';
            if (CONFIRM_SMS_YN == 'Y') $menu_array['인증번호 (관리자)'] = '/admsetting/mobile';
            $menu_array['공지사항 <span style="color:red;font-weight:bold;font-size:10px;">NEW</span>'] = '/admsetting/notice';
        }
        else {
            $menu_array = array(
                '신고건조회' => '/admsetting/report',
            );
        }
    }
    return $menu_array;
}
}
