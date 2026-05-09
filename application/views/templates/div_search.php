<?php
    $attributes = array(
        'name' => 'frmSearchList',
        'id' => 'frmSearchList',
        'method' => 'get',
        'onsubmit' => 'return searchList();'
    );
    echo form_open($g_search_action, $attributes);
    $g_placeholder = (!$g_placeholder ? '이름/번호로 검색' : $g_placeholder);
?>
	<div class="faq_sch">
	<label for="txtSearch"></label>
    <input name="sf" type="hidden" value="<?=$sf?>" />
    <input name="sg" type="hidden" value="<?=$gno?>" />
    <input name="sv" type="text" class="inp_text font-13" title="검색" placeholder="<?=$g_placeholder?>" value="<?=$sv?>" />
    <button type="submit" class="btn_sch"></button>
    </div>
</form>

<script type="text/javascript">
var searchList = function() {
    if ($("input[name=sv]").val().trim() == '') return false;
    return true;
}
</script>