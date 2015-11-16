
<SCRIPT LANGUAGE="JavaScript">
<!--
function chkCommentForm() {
	if (!comment_form.up_name.value) {
		alert('이름을 입력 하세요.');
		comment_form.up_name.focus();
		return false;
	}
	if (!comment_form.up_passwd.value) {
		alert('패스워드를 입력 하세요.');
		comment_form.up_passwd.focus();
		return false;
	}

	if (!comment_form.up_comment.value) {
		alert('내용을 입력 하세요.');
		comment_form.up_comment.focus();
		return false;
	}
}
//-->
</SCRIPT>


<!-- 간단한 답변글 쓰기 -->
<TABLE border=0 CELLSPACING=0 CELLPADDING=0 BGCOLOR="<?=$comment_header_bg_color?>" style="margin-bottom:25px; TABLE-LAYOUT:FIXED">
	<form method=post name=comment_form action="board.php" onSubmit="return chkCommentForm();" enctype="multipart/form-data">
	<input type=hidden name=pagetype value="comment_result">
	<input type=hidden name=board value="<?=$board?>">
	<input type=hidden name=num value="<?=$this_num?>">
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
	<input type=hidden name=search value="<?=$search?>">
	<input type=hidden name=s_check value="<?=$s_check?>">
	<input type=hidden name=mode value="up">
	<tr>
		<td>
			<TABLE border="0" cellSpacing="0" cellPadding="4" width="100%" style="table-layout:fixed">
				<TR>
					<TD style="font-size:11px; letter-spacing:-0.5pt; padding-top:5px; padding-left:10px; padding-right:5px;" bgColor="#fafafa">
						<? if (strlen($member[name])>0) { ?>
							<b>작성자</b> : <?= $member[name] ?><input type=hidden name="up_name" value="<?=$member[name]?>">
						<? } else { ?>
							<b>작성자</b> : <input type=text name="up_name" size="13" maxlength="10" value="" class="input" />
							<img width="20" height="0"><b>비밀번호</b> : <INPUT type=password name="up_passwd" value="" maxLength="20" size="10" class="input" />
						<? } ?>
					</TD>
				</TR>
				<TR bgColor="#fafafa" align="center">
					<TD style="padding:5px 0px;">
						<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
							<col width=></col>
							<col width="100"></col>
							<tr>
								<td style="padding-left:5px;"><textarea name=up_comment style="width:100%; height:70px; line-height:17px; border:solid 1px #dddddd; font-size:9pt; color:333333; background-color:white;"></textarea></td>
								<td align="center"><a href="javascript:document.comment_form.submit()"><IMG src="<?=$imgdir?>/board_comment.gif" border="0" hspace="5" align=absmiddle></A></TD>
							</tr>
						</table>
					</td>
				</TR>
			</TABLE>
		</td>
	</tr>
	<? if($setup[fileYN] == "Y"){ ?>
	<tr>
		<td style="font-size:11px; letter-spacing:-0.5pt; padding:5px 10px;" bgColor="#fafafa"><b>파일첨부</b> :&nbsp;<?=$cmtFile?></td>
	</tr>
	<? } ?>
	<TR><TD height="1" bgcolor="#EDEDED"></TD></TR>
	</FORM>
</TABLE>