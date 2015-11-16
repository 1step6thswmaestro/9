<?
	if($i==0) {
		echo "<tr>\n";
		echo "	<td valign=\"top\" colspan=\"".$table_colcnt."\">\n";
		echo "	<TABLE cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" border=\"0\">\n";
		echo "	<tr><td height=\"15\"></td></tr>";
		echo "	<TR>\n";
	}

	if ($i!=0 && $i%4==0) {
		echo "	</tr><tr>\n";
	}
?>
	<!-- 목록 부분 시작 -->
	<TD width="25%" align="center" valign="top" style="padding:10px 2px;">
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td height="210" align="center" style="vertical-align:top;padding:4px; border:1px solid #ededed;"><?=$mini_file1?></td>
			</tr>
			<tr><td height="5"></td></tr>
			<tr>
				<td align="center" style="padding-top:5px;" nowrap><nobr><div style="white-space:nowrap; width:150px;overflow:hidden;text-overflow:ellipsis;font-size:8pt;"><?=$secret_img?> <B><?=$subject?></b><?=$commentnum?></div><?=$viewContent?></td>
			</tr>
			<!--
			<tr>
				<td align="center" nowrap><nobr><B><font color="#A48B00" style="font-size:8pt;"><?=$str_name?></font></b></td>
			</tr>
			-->
			<tr><td height="5"></td></tr>
			<tr>
				<td align="center" nowrap>
					<span style="font-size:11px; color:#bbbbbb;"><?=$str_name?>
					<?=$hide_date_start?>
					<nobr>, <?=$reg_date?>
					<?=$hide_date_end?>
					&nbsp;|&nbsp;<img src="/board/images/icon_vote.gif" alt="" /> <span style="color:#484848;">추천수 : <b><?=$vote?></b></span>
					</span>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
	</TD>
	<!-- 목록 부분 끝 -->

<?
	if(($total-1) == $i) {
		echo "	</tr>\n";
		//echo "	<TR><td height=\"1\" colspan=\"".$table_colcnt."\" bgcolor=\"".$list_divider."\"></td></tr>";
		echo "	<TR><td height=\"1\" colspan=\"".$table_colcnt."\" bgcolor=#eeeeee></td></tr>";
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
	}
?>