
<SCRIPT LANGUAGE="JavaScript">
	<!--
	function chkCommentForm() {
		if ( confirm("댓글 쓰기는 로그인이 필요 합니다! 로그인 하시겠습니까?") ) {
			location.href = "/front/login.php?chUrl=<?=getUrl()?>";
		}
	}
	//-->
</SCRIPT>


<!-- 간단한 답변글 쓰기 -->
<TABLE border=0 CELLSPACING=0 CELLPADDING=0 BGCOLOR="<?=$comment_header_bg_color?>" style="margin-bottom:25px; TABLE-LAYOUT:FIXED">
	<tr>
		<td>
			<TABLE border="0" cellSpacing="0" cellPadding="4" width="100%" style="table-layout:fixed;">
				<TR>
					<TD style="font-size:11px; letter-spacing:-0.5pt; padding:5px 10px;" bgColor="#fafafa">
						<b>작성자</b> : <input type=text name="up_name" size="13" maxlength="10" value="" class="input" onfocus="chkCommentForm();" readonly />
						<img width="20" height="0"><b>비밀번호</b> : <INPUT type=password name="up_passwd" value="" maxLength="20" size="10" class="input" onfocus="chkCommentForm();" readonly />
					</TD>
				</TR>
				<TR bgColor="#fafafa" align="center">
					<TD>
						<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
							<col width=></col>
							<col width="100"></col>
							<tr>
								<td style="padding:5px;"><textarea name=up_comment style="width:100%; height:70px;line-height:17px;border:solid 1;border-color:#BDBDBD;font-size:9pt;color:333333;background-color:white;" onfocus="chkCommentForm();" readonly >댓글을 작성하시려면 로그인이 필요합니다.</textarea></td>
								<td align="center"><a href="javascript:chkCommentForm();"><IMG src="<?=$imgdir?>/board_comment.gif" border="0" hspace="5" align=absmiddle></A></TD>
							</tr>
						</table>
					</td>
				</TR>
			</TABLE>
		</td>
	</tr>
	<TR><TD height="1" bgcolor="#EDEDED"></TD></TR>
</TABLE>