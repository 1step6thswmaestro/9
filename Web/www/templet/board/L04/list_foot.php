	<TR>
		<TD colspan="<?=$table_colcnt?>">
		<TABLE border="0" cellpadding="3" cellspacing="0" width="100%">
			<tr><td height="20"></td></tr>
			<TR>
				<TD align="right"><?=$hide_write_start?><A HREF="board.php?pagetype=write&board=<?=$board?>&exec=write&boardsep=<?=$boardsep?>"><IMG SRC="<?=$imgdir?>/butt-write.gif" border=0></A><?=$hide_write_end?></TD>
			</TR>
			<tr><td height="10"></td></tr>
			<TR>
				<TD align="center">


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

					// 정열
					function boardSort ( t ) {
						var v = ( sort.value == "" || sort.value == t+"_asc" ) ? "desc":"asc";
						location.href="?board=<?=$board?>&sort="+t+"_"+v;
					}
					</script>
					<input type="hidden" name="sort" id="sort" value="<?=$sort?>">
				</TD>
			</TR>
			<TR>
				<TD align="center" width="100%">
					<div class="pageingarea" style="text-align:center;width:100%; margin-bottom:20px;"><?=$pobj->_result('fulltext')?></div>
				</TD>
			</TR>
		</TABLE>
		</TD>
	</TR>
	</TABLE>
	</TD>
</TR>
</TABLE>