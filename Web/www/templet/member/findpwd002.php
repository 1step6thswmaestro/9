<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding-left:23px"><img src="<?=$Dir?>images/member/idsearch_skin3_text01.gif" border="0"></td>
</tr>
<tr>
	<td align="center">
	<table cellpadding="0" cellspacing="0" width="95%">
	<tr>
		<td bgcolor="#EAEAEA" style="padding:6px;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF">
		<tr>
			<td style="padding:10px;">
			<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td style="padding:20px">
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td width="100"><b><font color="F02800"><img src="<?=$Dir?>images/member/idsearch_skin3_icon01.gif" border="0"></font><font color="000000">이름</font></b></td>
					<td width="140" style="padding:2px"><input type=text name=name value="" maxlength=20 style="WIDTH: 100%" class="input"></td>
				</tr>
				<tr>
					<td width="100"><b><font color="F02800"><img src="<?=$Dir?>images/member/idsearch_skin3_icon01.gif" border="0"></font><font color="000000"><?=($_data->resno_type!="N"?"주민등록번호":"가입 메일주소")?></font></b></td>
					<td width="140" style="padding:2px"><? if($_data->resno_type!="N"){?><input type=text name=jumin1 value="" maxlength=6 style="width:42%" onkeyup="strnumkeyup2(this);" class="input"> - <input type="password" name=jumin2 value="" maxlength=7 onkeyup="strnumkeyup2(this);" style="width:48%" class="input"><?}else{?><input type=text name=email value="" maxlength=50 style="width:100%" class="input"><?}?></td>
				</tr>
				</table>
				</td>
				<td><A HREF="javascript:CheckForm()"><img src="<?=$Dir?>images/member/idsearch_skin2_pwbtn.gif" border="0"></a><A HREF="<?=$Dir.FrontDir?>login.php"><img src="<?=$Dir?>images/member/idsearch_skin2_loginbtn.gif" border="0" hspace="5"></a></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>