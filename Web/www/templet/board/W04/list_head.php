<script language=javascript>
	function schecked(){
		if (frm.search.value == ''){
			alert('검색어를 입력해주세요.');
			frm.search.focus();
			return false;
		} 
		else {
			frm.submit();
		}
	}
</script>

<table cellpadding="0" cellspacing="0" width="<?=$setup[board_width]?>" style="table-layout:fixed">
	<tr>
		<td style="padding-top:6px;">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td style="font-size:11px; letter-spacing:-0.5pt;"><img src="<?=$imgdir?>/board_icon_8a.gif" border="0">전체 <font class="TD_TIT4_B"><B><?= $t_count ?></B></font>건 조회&nbsp;&nbsp;<img src="<?=$imgdir?>/board_icon_8a.gif" border="0">현재 <B><?=$gotopage?></B>/<B><?=ceil($t_count/$setup[list_num])?></B> 페이지</td>
					<td style="padding-left:5px;"><?=$strAdminLogin?></td>
				</tr>
				<tr><td height="5"></td></tr>
			</table>

			<table cellpadding="0" cellspacing="0" width="100%" border="0">
				<col width="40"></col>
				<col></col>
				<col width="30"></col>
				<col width="80"></col>
				<?=$hide_hit_start?>
				<col width="40"></col>
				<?=$hide_hit_end?>
				<?=$hide_date_start?>
				<col width="110"></col>
				<?=$hide_date_end?>
				<TR>
					<TD height="2" colspan="<?=$table_colcnt?>" bgcolor="<?=$setup[title_color]?>"></TD>
				</TR>