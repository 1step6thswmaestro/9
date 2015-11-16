	<style>
		.w04Tbl h4{padding-bottom:10px;}
		.w04Tbl h4 a:link{font-size:14px;color:#333333;line-height:20px;letter-spacing:-1px;}
		.w04Tbl h4 a:hover{font-size:14px;color:#333333;line-height:20px;letter-spacing:-1px;}
	</style>

	<!-- 목록 부분 시작 -->
	<tr>
		<td colspan="<?=$table_colcnt?>" style="padding:15px 0px;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="w04Tbl">
				<tr>
					<? if (strlen($mini_file1)>0) { ?>
					<td valign="top" style="padding-right:25;">
						<div style="border:1px solid #dddddd; padding:5px; font-size:0px; overflow:hidden; text-align:center"><?=$mini_file1?></div>
					</td>
					<?}?>
					<td valign="top" style="padding-top:5px;">
						<h4><?=$subject?></h4>
						<p style="font-size:12px; color:#999999;">
							<?if ($deleted != "1") {?><a href="#" onclick="<?=$subjectURL?>"><?}?>
							<?=len_title(strip_tags($row[content]), 300)?>
							<?if ($deleted != "1") {?></a><?}?>
						</p>
						<div style="float:left; margin-top:10px;"><a href="board.php?pagetype=view&view=1&num=<?=$row[num]?>&board=<?=$board?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>&boardsep=<?=$boardsep?>"><img src="/board/images/btn_detail_view.gif" border="0" alt="자세히 보기" /></a></div>
						<div style="float:right; margin-top:15px; font-size:11px; color:#bbbbbb;"><?=$str_name?>, <?=$reg_date?> &nbsp;|&nbsp;&nbsp;<img src="/board/images/icon_vote.gif" alt="" /> <span style="color:#444444;">추천수 : <b><?=$vote?></b></span></div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<TR>
		<TD height="1" colspan="<?=$table_colcnt?>" bgcolor="<?=$list_divider?>"></TD>
	</TR>
	<!-- 목록 부분 끝 -->