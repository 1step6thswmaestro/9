<TABLE cellSpacing="0" cellPadding="0" width="100%" bgcolor="#FFFFFF">
	<!--<tr onMouseOver="this.style.backgroundColor='<?=$list_mouse_over_color?>'" onMouseOut="this.style.backgroundColor='';">-->
	<tr>
		<td style="padding:0px 10px;">
			<TABLE cellSpacing="0" cellPadding="0" border="0" width="100%">
				<TR><TD height="10" colspan="2"></TD></TR>
				<TR>
					<TD height="22"><B><font color="#006699"><?=$c_name?><?=$c_id?></B></td>
					<td align="right">
						<font color="#0099CC"><?=$c_writetime?></font>
						<? if( $setup["onlyCmt"] == "N" OR strlen($_ShopInfo->id) > 0 ){ ?>&nbsp;<IMG src="<?=$imgdir?>/board_del.gif" align="absmiddle" border="0" style="CURSOR:hand;" onclick="javascript:comment_delete('<?=$this_num?>','<?=$c_num?>','<?=$frametype?>')"><? } ?>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" valign="top">
						<?=$c_comment_file?>
						<div style="float:left; font-size:11px; letter-spacing:-0.5px;"><?=$c_comment?></div>
						<div style="clear:both; margin-top:10px;"><?=$adminComment?></div>
					</TD>
				</TR>
				<TR><TD height="6" colspan="2"></TD></TR>
			</TABLE>
		</td>
	</tr>
	<TR><TD height="1" bgcolor="#EDEDED"></TD></TR>
</table>