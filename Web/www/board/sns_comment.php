						<!--sns ÄÚ¸àÆ®--------------->
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td height=20></td>
							</tr>
							<tr>
								<td class="table_td"><img src="../images/design/board_text_snscomment.gif" align="absmiddle"><span style="font-family:verdana; font-size:10px; color:#FF7449;">(<span id="snsCmtTot">0</span>)</span>
							</tr>
							<tr>
								<td height=10></td>
							</tr>
							<tr>
								<td>
									<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#F4F4F4">
										<tr>
											<td style="padding:20px;" align="center">
												<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#F4F4F4">
													<tr>
														<td align="center" valign="top"></td>
														<td align="center" valign="top">
															<table cellpadding="0" cellspacing="0" width="98%" align="center">
																<tr>
																	<td width="145" valign="top" align="left">
																	<? if(TWITTER_ID !="TWITTER_ID"){?>
																	<a href="javascript:changeSnsInfo('t');"><IMG SRC="../images/design/icon_twitter_off.gif" WIDTH="25" HEIGHT="25" ALT="" border="0" id="tLoginBtn3"></a><input type="hidden" name="tLoginBtnChk" id="tLoginBtnChk">
																	<?}?>
																	<? if(FACEBOOK_ID!="FACEBOOK_ID"){?>
																	<a href="javascript:changeSnsInfo('f');"><IMG SRC="../images/design/icon_facebook_off.gif" WIDTH="25" HEIGHT="25" ALT="" hspace="4" border="0" id="fLoginBtn3"></a><input type="hidden" name="fLoginBtnChk" id="fLoginBtnChk">
																	<?}?>
																	<A HREF="#comment" onclick="CopyBodUrl();return false;"><IMG SRC="../images/design/gonggu_order_btn06.gif" WIDTH=28 HEIGHT=24 ALT="" hspace="4"></a></td>
																</tr>
															</table>
														</td>
														<td align="center" valign="top"></td>
													</tr>
													<tr><td height="5"></td></tr>
													<tr>
														<td width="48" align="center" valign="top"><IMG SRC="../images/design/sns_default.jpg" WIDTH="48" HEIGHT="48" ALT="" class="img" id="snsThumb" ></td>
														<td align="center" valign="top" width="100%"><textarea name="comment" id="comment" onChange="CheckStrLen('100',this);" onKeyUp="CheckStrLen('100',this);" rows="5" cols="50" style="width:98%" class="textarea_gonggu"></textarea></td>
														<td width="80" align="center" valign="top"><a href="#comment"><IMG SRC="../images/design/board_btn_comment.gif" ALT="" onclick="snsbodReg();return false;"></a></td>
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
								<td id="snsBoardList">
								</td>
							</tr>
						</table>
