				<TR>
					<TD colspan="<?=$table_colcnt?>">
						<TABLE border="0" cellpadding="3" cellspacing="0" width="100%">
							<tr><td height="10"></td></tr>
							<TR>
								<TD align="right"><?=$hide_write_start?><A HREF="board.php?pagetype=write&board=<?=$board?>&exec=write&boardsep=<?=$boardsep?>"><IMG SRC="<?=$imgdir?>/butt-write.gif" border=0></A><?=$hide_write_end?></TD>
							</TR>
							<TR>
								<TD align="center" width="100%">
									<table border="0" cellpadding="0" cellspacing="0">
										<tr>
										<!-- 페이지 출력 ---------------------->
										<?=$a_prev_page?>
										<?=$print_page?>
										<?=$a_next_page?>
										<!-- 페이지 출력 끝 -->
											<td width=1 nowrap class=ndv></td>
										</tr>
										<tr><td height="20"></td></tr>
									</table>
								</TD>
							</TR>
						</TABLE>
					</TD>
				</TR>
			</TABLE>
		</TD>
	</TR>
</TABLE>

<form name=commform method=get action="board.php">
<input type=hidden name=pagetype value="comment_frame">
<input type=hidden name=board value="<?=$board?>">
<input type=hidden name=num>
</form>