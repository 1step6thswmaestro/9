<style>
	.memLoginBox {padding:35px 0px; text-align:center; border-top:1px solid #eeeeee; background:#fafafa; border-bottom:1px solid #cccccc;}
	.memLoginBox table {margin:0 auto;}
	.memLoginBox table thead th {text-align:left; padding-bottom:15px;}
	.memLoginBox table tbody th {width:120px; font-size:18px; font-weight:normal; line-height:30px; text-align:left;}
	.memLoginBox table tbody input {border:1px solid #dddddd; width:250px; height:32px; line-height:32px; padding-left:10px; color:#222222; font-size:17px; font-weight:bold;}
	.memLoginBox table tbody a:link {display:block; width:90px; height:66px; line-height:66px; text-align:center; color:#ffffff; font-size:14px; font-weight:bold; background:#222222;}
	.memLoginBox table tbody a:active {display:block; width:90px; height:66px; line-height:66px; text-align:center; color:#ffffff; font-size:14px; font-weight:bold; background:#222222;}
	.memLoginBox table tbody a:hover {display:block; width:90px; height:66px; line-height:66px; text-align:center; color:#ffffff; font-size:14px; font-weight:bold; background:#222222;}
	.memLoginBox table tbody a:visited {display:block; width:90px; height:66px; line-height:66px; text-align:center; color:#ffffff; font-size:14px; font-weight:bold; background:#222222;}

	.nomemOrderSearch {padding:35px 0px; text-align:center; border-bottom:1px solid #eeeeee;}
	.nomemOrderSearch table {margin:0 auto;}
	.nomemOrderSearch table thead th {text-align:left; padding-bottom:15px;}
	.nomemOrderSearch table tbody th {width:120px; font-size:18px; font-weight:normal; line-height:30px; text-align:left;}
	.nomemOrderSearch table tbody input {border:1px solid #dddddd; width:250px; height:32px; line-height:32px; padding-left:10px; color:#222222; font-size:17px; font-weight:bold;}
	.nomemOrderSearch table tbody a:link {display:block; width:90px; height:66px; line-height:66px; text-align:center; color:#ffffff; font-size:14px; font-weight:bold; background:#888888;}
	.nomemOrderSearch table tbody a:active {display:block; width:90px; height:66px; line-height:66px; text-align:center; color:#ffffff; font-size:14px; font-weight:bold; background:#888888;}
	.nomemOrderSearch table tbody a:hover {display:block; width:90px; height:66px; line-height:66px; text-align:center; color:#ffffff; font-size:14px; font-weight:bold; background:#888888;}
	.nomemOrderSearch table tbody a:visited {display:block; width:90px; height:66px; line-height:66px; text-align:center; color:#ffffff; font-size:14px; font-weight:bold; background:#888888;}

	.memJoin {padding:35px 0px; text-align:center; border:1px solid #eeeeee; border-top:hidden;}
</style>

<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><IMG SRC="[DIR]images/member/login_con_text_skin2.gif" border="0"></td>
	</tr>
	<tr>
		<td align="center">

			<!--�α���-->
			<div class="memLoginBox">
				<table border="0" cellpadding="0" cellspacing="0">
					<caption style="display:none;">ȸ�� �α���</caption>
					<thead>
						<th><IMG SRC="[DIR]images/member/login_con_text0_skin2.gif" border="0" /></th>
					</thead>
					<tbody>
					<tr>
						<td valign="top" style="padding-left:14px;">
							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
									<th>���̵�</th>
									<td><input type="text" name="id" maxlength="20" value="" /></td>
								</tr>
								<tr>
									<th>��й�ȣ</th>
									<td><input type="password" name="passwd" maxlength="20" value="" onkeydown="CheckKeyForm1()" /></td>
								</tr>

								[IFSSL]
								<tr>
									<th></th>
									<td style="padding-top:5px;">[SSLCHECK] <a href=[SSLINFO]>��������</a></td>
								</tr>
								[ENDSSL]
							</table>
						</td>
						<td width="10"></td>
						<td valign="top"><a href=[OK]>�α���</a></td>
					</tr>
					</tbody>
				</table>
			</div>
			<!--�α���-->

			<!--��ȸ�� �ֹ���ȸ-->
			[IFORDER]
			<div class="nomemOrderSearch">
				<table border="0" cellpadding="0" cellspacing="0">
					<caption style="display:none;">��ȸ�� �ֹ���ȸ</caption>
					<thead>
						<th><IMG SRC="[DIR]images/member/login_con_text5a_skin2.gif" border="0" /></th>
					</thead>
					<tbody>
					<tr>
						<td style="padding-left:14px;">
							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
									<th>�ֹ��ڸ�</th>
									<td><input type="text" name="ordername" maxlength="20" value="" /></td>
								</tr>
								<tr>
									<th>�ֹ���ȣ</th>
									<td><input type="text" name="ordercodeid" maxlength="20" value="" onkeydown="CheckKeyForm2()" /></td>
								</tr>
							</table>
						</td>
						<td width="10"></td>
						<td><a href=[ORDEROK]>�ֹ���ȸ</a></td>
					</tr>
					</tbody>
				</table>
			</div>
			[ENDORDER]
			<!--��ȸ�� �ֹ���ȸ-->

			<!--ȸ������/���̵��� ã��-->
			<div class="memJoin">
				<table cellpadding="0" cellspacing="0" width="480" style="text-align:left; margin-left:20px; margin:0 auto;">
					<tr>
						<th><IMG SRC="[DIR]images/member/login_con_text3_skin2.gif" border="0" /></th>
						<td align="right"><A HREF=[JOIN]><IMG SRC="[DIR]images/member/login_con_btn2a_skin2.gif" border="0"></a></td>
					</tr>
					<tr><td height="8" colspan="2"></td></tr>
					<tr>
						<th><IMG SRC="[DIR]images/member/login_con_text4_skin2.gif" border="0" /></th>
						<td align="right"><A HREF=[FINDPWD]><IMG SRC="[DIR]images/member/login_con_btn3_skin2.gif" border="0"></a></td>
					</tr>
				</table>
			</div>
			<!--ȸ������/���̵��� ã��-->

			<!--��ȸ������/�α���-->
			[IFNOLOGIN]
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align="center" style="padding-top:15px;">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<td width="390"><A HREF=[NOLOGIN]><IMG SRC="[DIR]images/member/login_con_text5_skin2.gif" border="0"></a></td>
								<td></td>
							</tr>
							<tr>
								<td><A HREF=[NOLOGIN]><IMG SRC="[DIR]images/member/login_con_text5_skin2_text01.gif" border="0"></a></td>
								<td><A HREF=[NOLOGIN]><IMG SRC="[DIR]images/member/login_con_btn4_skin2.gif" border="0"></A></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td background="[DIR]images/member/login_con_line_skine2.gif" height="28"></td>
				</tr>
			</table>
			[ENDNOLOGIN]
			<!--��ȸ������/�α���-->

		</td>
	</tr>
	<tr><td style="padding:10px;">[BANNER]</td></tr>
	<tr><td height="40"></td></tr>
</table>