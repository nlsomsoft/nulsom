<?php
    $cache_key = 'sow_callback_cnt';
    $callback_cnt = $this->cache->redis->get($cache_key);

    $cache_key = 'sow_bankbook_cnt';
    $bank_cnt = $this->cache->redis->get($cache_key);

    $this->load->helper('menu');
    $g_menu_user = get_category_list('user', $this->session->userdata('level'));
    $g_menu_send = get_category_list('send', $this->session->userdata('level'));
    $g_menu_stats = get_category_list('stats', $this->session->userdata('level'));
    $g_menu_bill = get_category_list('bill', $this->session->userdata('level'));
    $g_menu_setting = get_category_list('setting', $this->session->userdata('level'));

    $g_memu_list = get_category_list($g_menu_flag, $this->session->userdata('level'));
?>


<header id="hd">
    <div id="hd_wrap">
        <div id="logo"><a href="#"><img src="/images/adm/logo.jpg" alt="ADMIN 관리자"></a></div>
        <ul id="tnb">
            <li><a href="/">문자사이트</a></li>
            <li id="tnb_logout"><a href="/signup/logout">로그아웃</a></li>
        </ul>

        <nav id="gnb">
            <h2>관리자 주메뉴</h2>
            <ul id="gnb_1dul">
                <li class="gnb_1dli <?=($g_menu_flag == 'user' ? 'gnb_1dli_air' : '')?>">
                <a href="/admuser/users" class="gnb_1da">회원관리 <?php if ($callback_cnt > 0) { ?><span style="color:#03c75a;font-size:10px;font-weight:bold;">N(<?=$callback_cnt?>)</span><?php } ?></a>
                <ul class="gnb_2dul">
                <?php foreach ($g_menu_user as $key => $val) { ?>
                        <li class="gnb_2dli"><a href="<?=$val?>" class="gnb_2da"><?=$key?></a></li>
                <?php } ?>
                </ul>
                </li>

                <li class="gnb_1dli <?=($g_menu_flag == 'send' ? 'gnb_1dli_air' : '')?>">
                <a href="/admsend/list" class="gnb_1da">발송관리</a>
                <ul class="gnb_2dul">
                <?php foreach ($g_menu_send as $key => $val) { ?>
                        <li class="gnb_2dli"><a href="<?=$val?>" class="gnb_2da"><?=$key?></a></li>
                <?php } ?>
                </ul>
                </li>

        <?php
            if ((int)$this->session->userdata('level') >= 5) {
                $billing_link = '/admbill/bankbook';
            } else {
                $billing_link = '/admbill/pay_list';
            }
        ?>
                <li class="gnb_1dli <?=($g_menu_flag == 'bill' ? 'gnb_1dli_air' : '')?>">
                <a href="<?=$billing_link?>" class="gnb_1da">빌링관리 <?php if ($bank_cnt > 0) { ?><span style="color:#03c75a;font-size:10px;font-weight:bold;">N(<?=$bank_cnt?>)</span><?php } ?></a>
                <ul class="gnb_2dul">
                <?php foreach ($g_menu_bill as $key => $val) { ?>
                        <li class="gnb_2dli"><a href="<?=$val?>" class="gnb_2da"><?=$key?></a></li>
                <?php } ?>
                </ul>
                </li>

                <li class="gnb_1dli <?=($g_menu_flag == 'stats' ? 'gnb_1dli_air' : '')?>">
                <a href="/admstats/send_dd" class="gnb_1da">통계관리</a>
                <ul class="gnb_2dul">
                <?php foreach ($g_menu_stats as $key => $val) { ?>
                        <li class="gnb_2dli"><a href="<?=$val?>" class="gnb_2da"><?=$key?></a></li>
                <?php } ?>
                </ul>
                </li>

                <li class="gnb_1dli <?=($g_menu_flag == 'setting' ? 'gnb_1dli_air' : '')?>">
                <a href="/admsetting/report" class="gnb_1da">환경설정</a>
                <ul class="gnb_2dul">
                <?php foreach ($g_menu_setting as $key => $val) { ?>
                        <li class="gnb_2dli"><a href="<?=$val?>" class="gnb_2da"><?=$key?></a></li>
                <?php } ?>
                </ul>
                </li>
            </ul>
        </nav>
    </div>
</header>

<ul id="lnb">
<?php
    foreach ($g_memu_list as $key => $val) {
        $new_flag = '';
        if ($callback_cnt && $key == '발신번호관리') {
            $new_flag = '&nbsp;<span style="color:#03c75a;font-weight:bold;">N</span>';
        }
        else if ($bank_cnt && $key == '무통장관리') {
            $new_flag = '&nbsp;<span style="color:#03c75a;font-weight:bold;">N</span>';
        }
?>
        <li><a href="<?=$val?>"><?=$key?><?=$new_flag?></a></li>
<?php
    }
?>
</ul>
