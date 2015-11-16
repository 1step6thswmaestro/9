<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding-left:10px;padding-right:10px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><IMG SRC=[DIR]images/member/login_con_text_skin3.gif border="0"></td>
	</tr>
	<tr>
		<TD>




		<!--로그인-->
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td background=[DIR]images/member/login_con_tabletopl_skin3.gif height="28"></td>
			</tr>
			<tr>
				<td valign="top" style="padding:15px">


					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><IMG SRC=[DIR]images/member/login_con_text0_skin3.gif border="0"></td>
						</tr>
						<tr>
							<td align="center">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>
											<table cellpadding="0" cellspacing="0">
												<tr>
													<td><IMG SRC=[DIR]images/member/login_con_text1_skin3.gif border="0"></td>
													<td>[ID]</td>
												</tr>
												<tr>
													<td><IMG SRC=[DIR]images/member/login_con_text2_skin3.gif border="0"></td>
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
										<td valign=top><A HREF=[OK]><IMG SRC=[DIR]images/member/login_con_btn1_skin3.gif border="0" hspace="5"></a></td>
									</tr>
								</table>
							</td>
							<td width=20></td>
							<td align="center">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td>
											<table align="center" cellpadding="0" cellspacing="0">
												<tr>
													<td><IMG SRC=[DIR]images/member/login_con_text3_skin3.gif border="0"></td>
													<td><A HREF=[JOIN]><IMG SRC=[DIR]images/member/login_con_btn2a_skin3.gif border="0"></a></td>
												</tr>
												<tr>
													<td><IMG SRC=[DIR]images/member/login_con_text4_skin3.gif border="0"></td>
													<td><A HREF=[FINDPWD]><IMG SRC=[DIR]images/member/login_con_btn3_skin3.gif border="0"></a></td>
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
			<tr>
				<td background=[DIR]images/member/login_con_tabletopl_skin3.gif height="28" colspan=3></td>
			</tr>
		</table>
					
					
	</td>
</tr>
<tr>
	<td>


		<!--비회원 주문조회-->
		<table cellpadding="0" cellspacing="0" width="100%">
			[IFNOLOGIN]
			<tr>
				<td  valign="top" style="padding:15px">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td><A HREF=[NOLOGIN]><IMG SRC=[DIR]images/member/login_con_text5_skin3.gif border="0"></a></td>
										<td><A HREF=[NOLOGIN]><IMG SRC=[DIR]images/member/login_con_btn4_skin3.gif border="0"></A></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			[ENDNOLOGIN]
			[IFORDER]
			<tr>
				<td  valign="top" style="padding:15px">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td><IMG SRC=[DIR]images/member/login_con_text5a_skin3.gif border="0"></td>
						</tr>
						<tr>
							<td HEIGHT="10"></td>
						</tr>
						<tr>
							<td>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td STYLE="PADDING-LEFT:12PX">
											<table cellpadding="0" cellspacing="0">
												<tr>
													<td><img src=[DIR]images/member/login_con_text5a_1_skin3.gif border="0"></td>
													<td>[ORDERNAME]</td>
												</tr>
												<tr>
													<td><img src=[DIR]images/member/login_con_text5a_2_skin3.gif border="0"></td>
													<td>[ORDERCODE]</td>
												</tr>
											</table>
										</td>
										<td><a href=[ORDEROK]><IMG SRC=[DIR]images/member/login_con_btn2_skin3.gif border="0" hspace="5"></a></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td background=[DIR]images/member/login_con_tabletopl_skin3.gif height="28" colspan=3></td>
			</tr>
			[ENDORDER]
		</table>
		<!--비회원 주문조회-->



		</TD>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td  style="padding-left:10px;padding-right:10px;">[BANNER]</td>
</tr>
<tr>
	<td height="40"></td>
</tr>
</table>