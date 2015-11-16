<?
	$sGongguCmt =
		"<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
			<tr>
				<td height=\"16\">&nbsp;</td>
			</tr>
			<tr>
				<td class=\"table_td\">- 공동구매는 1회당 <b><font color=\"#FE8854\">최소 30개 이상 최대 100개 이하의 상품으로 진행 가능</font></b>하며, 판매 성사시 <b><font color=\"#FE8854\">최초 30~50%</font></b>의 할인된 가격으로 상품을 구매하실 수 있습니다.<br>- 특가상품,기획상품은 공동구매 제안이 불가능합니다.<br>- 본인이 등록한 글은 상품구매 희망자가 없을 경우에만 삭제가 가능합니다.<br>- 신청 및 희망한 상품의 공동구매가 진행되면 등록하신 이메일과 SMS로 알람이 발송됩니다.</td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
					<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
						<tr>
							<td class=\"table_td\">*총 <span id=\"gongCmtTot\">0</span>건 신청</td>
							<td class=\"table_td\" align=\"right\"><a href=\"../front/gonggu_guide.php\"><IMG SRC=\"../images/design/detail_btn_gongguguide.gif\" ALT=\"\"></a><a href=\"../front/gonggu_main.php\"><IMG SRC=\"../images/design/detail_btn_gonggugo.gif\"  ALT=\"\"></a></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td height=5></td></tr>
			<tr>
				<td>
					<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" bgcolor=\"#F4F4F4\">
						<tr>
							<td style=\"padding:20px;\" align=\"center\">
								<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" bgcolor=\"#F4F4F4\">
									<tr>
										<td width=\"812\" align=\"center\" valign=\"top\" align=\"left\"><textarea name=\"gonggu_cmt\" id=\"gonggu_cmt\" onChange=\"CheckStrLen('100',this);\" onKeyUp=\"CheckStrLen('100',this);\" rows=\"5\" cols=\"50\" style=\"width:98%\" class=\"textarea_gonggu\"></textarea></td>
										<td width=\"80\" align=\"center\" valign=\"top\"><a href=\"#gonggu_cmt\"><IMG SRC=\"../images/design/detail_btn_gonggu_pro.gif\" WIDTH=80 HEIGHT=80 ALT=\"\" hspace=5 onclick=\"snsGongguReg();return false;\"></a></td>
									</tr>
									<tr>
										<td align=\"center\" valign=\"top\" colspan=\"2\" height=10></td>
									</tr>
									<tr>
										<td align=\"center\" valign=\"top\">
											<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" align=\"center\">
												<tr>
													<td width=\"145\" valign=\"top\" align=\"left\">
													".((TWITTER_ID !="TWITTER_ID")? "<a href=\"javascript:changeSnsInfo('t');\"><IMG SRC=\"../images/design/icon_twitter_off.gif\" WIDTH=\"25\" HEIGHT=\"25\" ALT=\"\" border=\"0\" id=\"tLoginBtn4\"></a>":"").
													((FACEBOOK_ID !="FACEBOOK_ID")? "<a href=\"javascript:changeSnsInfo('f');\"><IMG SRC=\"../images/design/icon_facebook_off.gif\" WIDTH=\"25\" HEIGHT=\"25\" ALT=\"\" hspace=\"4\" border=\"0\" id=\"fLoginBtn4\"></a>":"").
													"<A HREF=\"#commen\" onclick=\"CopyUrl2();return false;\"><IMG SRC=\"../images/design/gonggu_order_btn06.gif\" WIDTH=28 HEIGHT=24 ALT=\"\" hspace=\"4\"></a></td>
													<td valign=\"top\"><IMG SRC=\"../images/design/detail_text_gonggu02.gif\" WIDTH=246 HEIGHT=26 ALT=\"\"></td>
												</tr>
											</table>
										</td>
										<td align=\"center\" valign=\"top\">&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td><IMG SRC=\"../images/design/detail_text_gonggu01.gif\" ALT=\"\"></td>
			</tr>
			<tr>
				<td><img src=\"../images/design/con_line01.gif\" width=\"100%\" height=\"1\" border=\"0\"></td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td id=\"snsGongguList\">
				</td>
			</tr>
		</table>
	";
?>