<div style="margin:0px 7px; padding:12px 7px; border-bottom:1px solid #e5e5e5;">
	<div style="margin-bottom:10px; overflow:hidden;">
		<p style="float:left; font-weight:bold; font-size:1.1em;"><?=$c_name?><?=$c_id?></p>
		<p style="float:right;">
			<?=$c_writetime?>
			<? if( $setup["onlyCmt"] == "N" OR strlen($_ShopInfo->id) > 0 ){ ?>&nbsp;<input type="button" class="button white small" value="»èÁ¦" onclick="javascript:comment_delete('<?=$view_num?>','<?=$c_num?>','<?=$actionurl?>')"><? } ?>
		</p>
	</div>
	<div style="width:100%; clear:both; overflow:hidden;">
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<? if($c_comment_file) { ?>
				<td style="width:90px;"><a href="javascript:zoomImage('<?=$filesname?>');"><?=$c_comment_file?></a></td>
				<? } ?>
				<td valign="top">
					<?=$c_comment?>
					<?=$adminComment?>
				</td>
			</tr>
		</table>
	</div>
</div>