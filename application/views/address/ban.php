<!-- content start -->
<div class="body-inner-table" style="padding-top:20px; min-height:700px;">

	<table width="1200" border="0" cellspacing="0" cellpadding="0">
	 <tr>
		<!-- left menu start -->
		<td width="210" valign="top">
			<?php
			$g_left_menu_flag = 'address';
			include_once(VIEWPATH.'/templates/left_menu.php');
			?>
		</td>
		<!-- left menu end -->
		<td width="30"></td>
		<td width="960" valign="top">

		<?php
			$attributes = array(
				'name' => 'searchBanList',
				'id' => 'searchBanList',
				'method' => 'get',
				'onsubmit' => 'return searchBanList();'
			);
			echo form_open('address/search_ban', $attributes);
		?>
			<div class="content_wrap">
				<div class="body_title" style="position:relative;">수신거부 목록 (직접관리)
					<div class="faq_sch">
					 <label for="txtSearch"></label>
					 <input name="sv" type="text" class="inp_text" title="검색" placeholder="이름/번호로 검색">
					 <button type="submit" class="btn_sch"></button>
					 </div>
				 </div>
			 </form>
			 <div style="margin-bottom:15px">
				<table width="100%" border="0" cellspacing="0" cellpadding="1">
				 <tr>
					 <td align="left"></td>
					 <td width="110">
					 	<div class="style_btn" style="width:100px"><a href="#" onClick="addrwLayerOpen('전송차단리스트','ban_type','<?=(int)$total_rows?>','0'); return false;">차단리스트 추가</a></div>
					 </td>
					 <td width="80"><div class="style_btn" style="width:70px"><a href="#" onclick="deleteBanAddress();">삭제하기</a></div></td>
					 <td width="80">
					 	<div class="style_btn" style="width:70px"><a href="/address/ban_excel" >다운로드</a></div>
					 </td>
				 </tr>
			 </table>
		 </div>
	<?php
		$attributes = array(
			'name' => 'frmBanList',
			'id' => 'frmBanList'
		);
		echo form_open('/address/delete_ban', $attributes);
	?>

	<?php
		$data = array(
			'chk_nums' => '',
		);
		echo form_hidden($data);
	?>
		 <div class="board">
			<table border="0" class="basic">
				<colgroup><col style="width: 20px">
					<col style="width: 40px">
					<col style="width: 300px">
					<col style="width: 300px">
					<col>
				</colgroup>
				<thead>
					<tr align="center">
						<th scope="col"></th>
						<th scope="col"><input type="checkbox" name="all_check_bt" /></th>
						<th scope="col">차단번호</th>
						<th scope="col">이름</th>
						<th scope="col">등록일</th>
					</tr>
				</thead>
				<tbody>

				<?php
					foreach ($result as $key => $row) {
				?>
						<tr bgcolor="#FFFFFF" onMouseOver="this.style.backgroundColor='#F4F4F4';return true;" onMouseOut="this.style.backgroundColor=''; return true;">
							<td></td>
							<td><input type="checkbox" name="chk_seq_no" id="chk_seq_no" value="<?=$row->abno?>" /></td>
							<td><?=format_phone($row->mobile)?></td>
							<td><?=$row->name?></td>
							<td><?=$row->add_date?></td>
						</tr>
				<?php
					}
				?>

				</tbody>
			</table>
		</div>
	</form>
</div>

<div><?=$this->pagination->create_links();?></div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr><td height="30"></td></tr>
	<tr><td><img src="/images/tip.gif" width="145" height="34" alt="" /></td></tr>
	<tr><td height="1" bgcolor="#CCCCCC"></td></tr>
	<tr><td class="help_txt">
		* 등록된 번호는 문자 발송 시 자동으로 차단됩니다.</br>
		* 최대 50,000개의 차단번호를 등록하실 수 있습니다.<br>
		* 페이지당 100건의 리스트가 보여집니다.
	</td></tr>
</table>
</td>
</tr>
</table>
</div>

<?php
include_once(VIEWPATH.'/templates/layer_bulk_addition.php');
?>

<script type="text/javascript">
var searchBanList = function() {
	var sv = $("input[name=sv]").val().trim();

	if (sv == '') return false;
	return true;
}
var deleteBanAddress = function() {
	var k = 0;
	var chk_nums = [];
	var check_flg = false;

	$("input[name=chk_seq_no]:checked").each(function() {
		chk_nums[k++] = $(this).val();
		check_flg = true;
	});

	if (check_flg == false || chk_nums.length == 0) {
		alert("삭제할 목록을 선택하세요.");
		return;
	}
	$("input[name=chk_nums]").val(chk_nums);
	$("form#frmBanList").attr("action", "/address/delete_ban");
	$("form#frmBanList")[0].submit();
	// document.getElementById("frmBanList").submit();
}
</script>

<!-- content end -->

