				<TR>
					<TD colspan="<?=$table_colcnt?>">
						<TABLE border="0" cellpadding="3" cellspacing="0" width="100%">
							<TR>
								<TD align="right"><?=$hide_write_start?><A HREF="board.php?pagetype=write&board=<?=$board?>&exec=write&boardsep=<?=$boardsep?>"><IMG SRC="<?=$imgdir?>/butt-write.gif" border=0></A><?=$hide_write_end?></TD>
							</TR>
							<tr><td height="20"></td></tr>
							<TR>
								<TD align="center" width="100%">
									<div class="pageingarea" style="text-align:center;width:100%; margin-bottom:20px;"><?=$pobj->_result('fulltext')?></div>					
								</TD>
							</TR>
							<tr>
								<td height="26" align="center">
									<table cellpadding="0" cellspacing="0">
										<form method="get" name="frm" action="<?=$PHP_SELF?>" onSubmit="return schecked()">
										<input type="hidden" name="pagetype" value="list">
										<input type="hidden" name="board" value="<?=$board?>">
										<tr>
											<td style="font-size:11px;letter-spacing:-0.5pt;"><input type=radio name="s_check" value="c" <?=$check_c?> style="border:none">제목+내용<input type=radio name="s_check" value="n" <?=$check_n?> style="border:none">작성자</td>
											<td style="padding-left:5px;padding-right:5px;"><input type=text name="search" value="<?=$search?>" size="12" class="input"></td>
											<td><INPUT type="image" src="<?=$imgdir?>/butt-go.gif" border="0" align="absMiddle" style="border:none"></td>
										</tr>
										</FORM>
									</table>
								</td>
							</tr>
						</TABLE>
					</TD>
				</TR>
			</TABLE>
		</TD>
	</TR>
</TABLE>