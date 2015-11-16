<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-1";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################
?>

<? INCLUDE "header.php"; ?>
<SCRIPT TYPE="text/javascript">
	function webftp_popup() {
		window.open("design_webftp.popup.php","webftppopup","height=10,width=10");
}
</SCRIPT>

<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td valign="top">
			<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
							<col width=198></col>
							<col width=10></col>
							<col width=></col>
							<tr>
								<td valign="top"  background="images/leftmenu_bg.gif">
									<? include ("menu_design.php"); ?>
								</td>
								<td></td>
								<td valign="top">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td height="29" colspan="3">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" width="16" height="12" border="0" valign=absmiddle>현재위치 : 디자인관리 &gt; 웹FTP, 디자인 옵션 설정  &gt; <span class="2depth_select">웹FTP/웹FTP팝업</span></td>
													</tr>
												</table>
											</td>
										</tr>   
										<tr>
											<td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
											<td background="images/con_t_01_bg.gif"></td>
											<td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
										</tr>
										<tr>
											<td width="16" background="images/con_t_04_bg1.gif"></td>
											<td bgcolor="#ffffff" style="padding:10px">		
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr>
														<td height="8"></td>
													</tr>
													<tr>
														<td>
															<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																<TR>
																	<TD><IMG SRC="images/design_webftp_title.gif"		 ALT=""></TD>
																</TR>
																<TR>
																	<TD width="100%" background="images/title_bg.gif" HEIGHT="21"></TD>
																</TR>
															</TABLE>
														</td>
													</tr>
													<tr>
														<td height="3"></td>
													</tr>
													<tr>
														<td style="padding-bottom:3pt;">
															<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																<TR>
																	<TD><IMG SRC="images/distribute_01.gif"></TD>
																	<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
																	<TD><IMG SRC="images/distribute_03.gif"></TD>
																</TR>
																<TR>
																	<TD background="images/distribute_04.gif"><IMG SRC="images/distribute_04.gif" ></TD>
																	<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
																	<TD width="100%" class="notice_blue">쇼핑몰에 사용될 파일들을 웹상에서 쉽게 관리하실 수 있습니다.</TD>
																	<TD background="images/distribute_07.gif"><IMG SRC="images/distribute_07.gif" ></TD>
																</TR>
																<TR>
																	<TD><IMG SRC="images/distribute_08.gif"></TD>
																	<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
																	<TD><IMG SRC="images/distribute_10.gif"></TD>
																</TR>
															</TABLE>
														</td>
													</tr>
													<tr>
														<td height="10"></td>
													</tr>
													<tr>
														<td align=right style="padding:0,2,5,0">
															<A HREF="javascript:webftp_popup()"><IMG src="images/webftp_button.gif" align=absmiddle border=0></A>
														</td>
													</tr>
													<tr>
														<td>
															<?include("design_webftp_display.php");?>
														</td>
													</tr>
													<tr>
														<td height=20></td>
													</tr>
													<tr>
														<td>
															<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																<TR>
																	<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 HEIGHT=45 ALT=""></TD>
																	<TD><IMG SRC="images/manual_title.gif" WIDTH=113 HEIGHT=45 ALT=""></TD>
																	<TD width="100%" background="images/manual_bg.gif"></TD>
																	<TD background="images/manual_bg.gif"></TD>
																	<TD><IMG SRC="images/manual_top2.gif" WIDTH=18 HEIGHT=45 ALT=""></TD>
																</TR>
																<TR>
																	<TD background="images/manual_left1.gif"><IMG SRC="images/manual_left1.gif" WIDTH=15 HEIGHT="5" ALT=""></TD>
																	<TD COLSPAN=3 width="100%" valign="top" class="menual_bg" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
																		<table cellpadding="0" cellspacing="0" width="100%">
																			<tr>
																				<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																				<td width="100%"><span class="font_dotline">웹FTP/웹FTP팝업</span></td>
																			</tr>
																			<tr>
																				<td width="20" align="right">&nbsp;</td>
																				<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 각종 문서, 이미지 파일 등을 간편하게 웹상에서 업로드 및 삭제할 수 있습니다.</td>
																			</tr>
																			<tr>
																				<td width="20" align="right">&nbsp;</td>
																				<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- html, css 파일은 웹FTP로 편집이 가능합니다.</td>
																			</tr>
																		</table>
																	</TD>
																	<TD background="images/manual_right1.gif"><IMG SRC="images/manual_right1.gif" WIDTH=18 HEIGHT="2" ALT=""></TD>
																</TR>
																<TR>
																	<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
																	<TD COLSPAN=3 background="images/manual_down.gif"><IMG SRC="images/manual_down.gif" WIDTH="4" HEIGHT=8 ALT=""></TD>
																	<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
																</TR>
															</TABLE>
														</td>
													</tr>
													<tr><td height=40></td></tr>
												</table>
											</td>
											<td width="16" background="images/con_t_02_bg.gif"></td>
										</tr>
										<tr>
        											<td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
        											<td background="images/con_t_04_bg.gif"></td>
        											<td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
										</tr>
										<tr><td height="20"></td></tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<form name=form3 method=post target="webftpetcpop">
		<input type=hidden name=val>
	</form>
</table>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>