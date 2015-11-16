<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding-left:10px;padding-right:10px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><IMG SRC=[DIR]images/member/login_con_text_skin1.gif border="0"></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<TD align="center">
			
			
			
			
		<!--로그인-->
		<table cellpadding="0" cellspacing="0" width="100%">
		<col width="10"></col>
		<col width=""></col>
		<col width="10"></col>
		<tr>
			<td><IMG SRC=[DIR]images/member/login_con_tabletopl_skin1.gif border="0"></td>
			<td background=[DIR]images/member/login_con_tabletopc_skin1.gif></td>
			<td><IMG SRC=[DIR]images/member/login_con_tabletopr_skin1.gif border="0"></td>
		</tr>
		<tr>
			<td background=[DIR]images/member/login_con_tableleft_skin1.gif></td>
			<td valign="top" bgcolor="#ffffff"  style="padding:20px">


				<table cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td height=25><IMG SRC=[DIR]images/member/login_con_text0_skin1.gif border="0"></td>
								</tr>
								<tr>
									<td>
										<table cellpadding="0" cellspacing="0">
										<tr>
											<td><IMG SRC=[DIR]images/member/login_con_text1_skin1.gif border="0"></td>
											<td>[ID]</td>
										</tr>
										<tr>
											<td><IMG SRC=[DIR]images/member/login_con_text2_skin1.gif border="0"></td>
											<td>[PASSWD]</td>
										</tr>
										[IFSSL]
										<tr>
											<td></td>
											<td>[SSLCHECK] <a href=[SSLINFO]>보안 접속</a></td>
										</tr>
										[ENDSSL]
										</table>
									<td>
									<td valign=top><A HREF=[OK]><IMG SRC=[DIR]images/member/login_con_btn1_skin1.gif border="0" hspace="5"></a></td>
								</tr>
							</table>
						</td>
						<td width="20"></td>
						<td style="padding-top:20px">
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td>
										<table align="center" cellpadding="0" cellspacing="0">
											<tr>
												<td><IMG SRC=[DIR]images/member/login_con_text3_skin1.gif border="0"></td>
												<td><A HREF=[JOIN]><IMG SRC=[DIR]images/member/login_con_btn2a_skin1.gif border="0"></a></td>
											</tr>
											<tr>
												<td><IMG SRC=[DIR]images/member/login_con_text4_skin1.gif border="0"></td>
												<td><A HREF=[FINDPWD]><IMG SRC=[DIR]images/member/login_con_btn3_skin1.gif border="0"></a></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>


			</td>
			<td background=[DIR]images/member/login_con_tableright_skin1.gif></td>
		</tr>
		<tr>
			<td><IMG SRC=[DIR]images/member/login_con_tabledownl_skin1.gif border="0"></td>
			<td background=[DIR]images/member/login_con_tabledownc_skin1.gif></td>
			<td><IMG SRC=[DIR]images/member/login_con_tabledownr_skin1.gif border="0"></td>
		</tr>

		</table>
	<!--로그인-->


		





		<!--비회원로그인-->
		[IFORDER]
		<table cellpadding="0" cellspacing="0" width="100%">
		<col width="10"></col>
		<col width=""></col>
		<col width="10"></col>
		<tr>
			<td><IMG SRC=[DIR]images/member/login_con_tabletopl_skin1.gif border="0"></td>
			<td background=[DIR]images/member/login_con_tabletopc_skin1.gif></td>
			<td><IMG SRC=[DIR]images/member/login_con_tabletopr_skin1.gif border="0"></td>
		</tr>
		<tr>
			<td background=[DIR]images/member/login_con_tableleft_skin1.gif></td>
			<td valign="top" bgcolor="#ffffff" style="padding:20px">
			<table cellpadding="0" cellspacing="0">

					<tr>
						<td>
						<table cellpadding="0" cellspacing="0">
						<tr>
							<td><IMG SRC=[DIR]images/member/login_con_text5a_skin1.gif border="0"></td>
						</tr>
						<tr>
							<td height="10"></td>
						</tr>
						<tr>
							<td>
							<table cellpadding="0" cellspacing="0">
							<tr>
								<td>
								<table cellpadding="0" cellspacing="0">
								<tr>
									<td><img src=[DIR]images/member/login_con_text5a_1_skin1.gif border="0"></td>
									<td>[ORDERNAME]</td>
								</tr>
								<tr>
									<td><img src=[DIR]images/member/login_con_text5a_2_skin1.gif border="0"></td>
									<td>[ORDERCODE]</td>
								</tr>
								</table>
								</td>
								<td><a href=[ORDEROK]><IMG SRC=[DIR]images/member/login_con_btn2_skin1.gif border="0" hspace="5"></a></td>
							</tr>
							</table>
							</td>
						</tr>
						</table>
						</td>
						<td width="20"></td>
						<td></td>
					</tr>
					</table>

			</td>
			<td background=[DIR]images/member/login_con_tableright_skin1.gif></td>
		</tr>
		<tr>
			<td><IMG SRC=[DIR]images/member/login_con_tabledownl_skin1.gif border="0"></td>
			<td background=[DIR]images/member/login_con_tabledownc_skin1.gif></td>
			<td><IMG SRC=[DIR]images/member/login_con_tabledownr_skin1.gif border="0"></td>
		</tr>
		</table>
		[ENDORDER]
		<!--비회원로그인-->



		<!--비회원구매-->
		[IFNOLOGIN]
		<table cellpadding="0" cellspacing="0" width="100%">
		<col width="10"></col>
		<col width=""></col>
		<col width="10"></col>
		<tr>
			<td><IMG SRC=[DIR]images/member/login_con_tabletopl_skin1.gif border="0"></td>
			<td background=[DIR]images/member/login_con_tabletopc_skin1.gif></td>
			<td><IMG SRC=[DIR]images/member/login_con_tabletopr_skin1.gif border="0"></td>
		</tr>
		<tr>
			<td background=[DIR]images/member/login_con_tableleft_skin1.gif></td>
			<td valign="top" bgcolor="#ffffff" style="padding:20px">
			<table cellpadding="0" cellspacing="0">

				<tr>
					<td>
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td><A HREF=[NOLOGIN]><IMG SRC=[DIR]images/member/login_con_text5_skin1.gif border="0"></a></td>
						<td valign="bottom"><A HREF=[NOLOGIN]><IMG SRC=[DIR]images/member/login_con_btn4_skin1.gif border="0"></A></td>
					</tr>
					</table>
					</td>
	
					</tr>

				</table>

			</td>
			<td background=[DIR]images/member/login_con_tableright_skin1.gif></td>
		</tr>
		<tr>
			<td><IMG SRC=[DIR]images/member/login_con_tabledownl_skin1.gif border="0"></td>
			<td background=[DIR]images/member/login_con_tabledownc_skin1.gif></td>
			<td><IMG SRC=[DIR]images/member/login_con_tabledownr_skin1.gif border="0"></td>
		</tr>
		</table>
		[ENDNOLOGIN]
		<!--비회원구매-->




				

				</TD>
			</tr>
		</table>

	</td>
</tr>
<tr>
	<td style="padding-top:10px;padding-bottom:10px;padding-left:10px">[BANNER]</td>
</tr>
<tr>
	<td height="40"></td>
</tr>
</table>