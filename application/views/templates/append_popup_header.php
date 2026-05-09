<div class="tab_menu2_3">
    <ul>
    <li <?=($g_sms_header_flag == 'addr' ? 'class="on"' : '')?>><a href="/append/group_popup"><?=($g_sms_header_flag == 'addr' ? '<font color="#fff">' : '')?>주소록불러오기<?=($g_sms_header_flag == 'addr' ? '</font>' : '')?></a></li>
    <li <?=($g_sms_header_flag == 'text' ? 'class="on"' : '')?>><a href="/append/text_popup"><?=($g_sms_header_flag == 'text' ? '<font color="#fff">' : '')?>직접붙여넣기<?=($g_sms_header_flag == 'text' ? '</font>' : '')?></a></li>
    <li <?=($g_sms_header_flag == 'excel' ? 'class="on"' : '')?>><a href="/append/excel_popup"><?=($g_sms_header_flag == 'excel' ? '<font color="#fff">' : '')?>엑셀붙여넣기<?=($g_sms_header_flag == 'excel' ? '</font>' : '')?></a></li>
    </ul>
</div>