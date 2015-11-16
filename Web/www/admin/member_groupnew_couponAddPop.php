<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

$groupCode=$_REQUEST["grp"];


$groupCodeSQL = "SELECT * FROM tblmembergroup WHERE group_code = '".$groupCode."' ";
$groupCodeResult = mysql_query($groupCodeSQL,get_db_conn());
$groupCodeRow=mysql_fetch_assoc($groupCodeResult);


INCLUDE "header.php";

?>

<script type="text/javascript">
	// <![CDATA[
		var xmlhttp = false;
		xmlhttp = new XMLHttpRequest ();
		//xmlhttp.overrideMimeType ('text/xml');

		function choiceCoupon ( groupCode, couponCode, checkeds ) {
			var url = 'member_groupnew_couponAddPop_process.php?groupCode=' + groupCode + '&couponCode=' + couponCode + '&chk=' + checkeds;
			xmlhttp.open('POST', url, true);
			xmlhttp.onreadystatechange = getCHK;
			xmlhttp.send(null);
		}

		function getCHK () {
			if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
				document.getElementById('chkMsg').innerHTML = xmlhttp.responseText;
			}
		}
	//]]>
</script>

<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td valign="top">


			<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
				<tr>
					<td>


					<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
						<col width=></col>
						<tr>
							<td valign="top">




								<table cellpadding="0" cellspacing="0" width="100%" >
									<tr>
										<td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
										<td background="images/con_t_01_bg.gif"></td>
										<td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
									</tr>
									<tr>
										<td width="16" background="images/con_t_04_bg1.gif"></td>
										<td bgcolor="#ffffff" style="padding:10px">

											<table cellpadding="0" cellspacing="0" width="100%">
												<tr><td height="8"></td></tr>
												<tr>
													<td>

														<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
															<TR>
																<TD><IMG SRC="images/member_groupnew_couponAddPop_title.gif" ALT="회원 등급 자동 발급 쿠폰"></TD>
															</tr>
															<tr>
																<TD width="100%" background="images/title_bg.gif" height="21"></TD>
															</TR>
														</TABLE>

													</td>
												</tr>
												<tr><td height="3"></td></tr>
												<tr>
													<td style="padding-bottom:3pt;">

														<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
															<TR>
																<TD><IMG SRC="images/distribute_01.gif"></TD>
																<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
																<TD><IMG SRC="images/distribute_03.gif"></TD>
															</TR>
															<TR>
																<TD background="images/distribute_04.gif"></TD>
																<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
																<TD width="100%" class="notice_blue"><?=$groupCodeRow['group_name']?> 등급.</TD>
																<TD background="images/distribute_07.gif"></TD>
															</TR>
															<TR>
																<TD><IMG SRC="images/distribute_08.gif"></TD>
																<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
																<TD><IMG SRC="images/distribute_10.gif"></TD>
															</TR>
														</TABLE>

													</td>
												</tr>
												<tr><td height="20"></td></tr>
												<tr>
													<td>

														<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
															<TR>
																<TD><IMG SRC="images/market_couponsy_stitle1.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
																<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
																<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
															</TR>
														</TABLE>

													</td>
												</tr>
												<tr><td height=3></td></tr>
												<tr>
													<td>

														<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
															<col width=30></col>
															<col width=70></col>
															<col width=></col>
															<col width=70></col>
															<col width=80></col>
															<col width=100></col>
															<col width=70></col>
															<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
															<input type=hidden name=type>
															<TR>
																<TD background="images/table_top_line.gif" colspan="7"></TD>
															</TR>
															<TR>
																<TD class="table_cell" align="center">선택</TD>
																<TD class="table_cell1" align="center">쿠폰코드</TD>
																<TD class="table_cell1" align="center">쿠폰명</TD>
																<TD class="table_cell1" align="center">생성일</TD>
																<TD class="table_cell1" align="center">할인/적립</TD>
																<TD class="table_cell1" align="center">유효기간</TD>
																<TD class="table_cell1" align="center">쿠폰종류</TD>
															</TR>
															<TR>
																<TD colspan="7" background="images/table_con_line.gif"></TD>
															</TR>
																<?
																	$sql = "SELECT * FROM tblcouponinfo WHERE vender='0' AND issue_type = 'N' AND member = '' ";
																	$result = mysql_query($sql,get_db_conn());
																	$cnt=0;
																	while($row=mysql_fetch_object($result)) {
																		$cnt++;
																		if($row->sale_type<=2) $dan="%";
																		else $dan="원";
																		if($row->sale_type%2==0) $sale = "할인";
																		else $sale = "적립";
																		if($row->date_start>0) {
																			$date = substr($row->date_start,2,2).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)." ~ ".substr($row->date_end,2,2).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2);
																		} else {
																			$date = abs($row->date_start)."일동안";
																		}

																		// 그룹자동발급 쿠폰 확인
																		$groupCouponResult = mysql_query("SELECT * FROM `group_coupon` WHERE `group_code`='".$groupCode."' AND `coupon_code` = '".$row->coupon_code."' LIMIT 1",get_db_conn());
																		$groupCouponRow = mysql_fetch_assoc ($groupCouponResult);

																		echo "<TR>\n";
																		echo "	<TD class=\"td_con2\" align=\"center\"><input type=checkbox name=ckbox id='ckbox_".$row->coupon_code."' ".($groupCouponRow['coupon_code']==$row->coupon_code?"checked":"")." onclick=\"choiceCoupon('".$groupCode."','".$row->coupon_code."', this.checked)\"></TD>\n";
																		echo "	<TD class=\"td_con1\" align=\"center\"><A HREF=\"javascript:CouponView('".$row->coupon_code."');\"><B>".$row->coupon_code."</B></A></TD>\n";
																		echo "	<TD class=\"td_con1\"><nobr>".$row->coupon_name."</TD>\n";
																		echo "	<TD class=\"td_con1\" align=\"center\">".substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."</TD>\n";
																		echo "	<TD class=\"td_con1\" align=\"center\"><span class=\"".($sale=="할인"?"font_orange":"font_blue")."\"><b>".number_format($row->sale_money).$dan." ".$sale."</b></span></TD>\n";
																		echo "	<TD class=\"td_con1\" align=\"center\">".$date."</TD>\n";
																		echo "	<TD class=\"td_con1\" align=\"center\"><img src=\"images/".($sale=="할인"?"icon_cupon1.gif":"icon_cupon2.gif")."\" width=\"61\" height=\"16\" border=\"0\"></TD>\n";
																		echo "</TR>\n";
																		echo "<TR>\n";
																		echo "	<TD colspan=\"7\" background=\"images/table_con_line.gif\"></TD>\n";
																		echo "</TR>\n";
																	}
																	mysql_free_result($result);
																	if($cnt==0) {
																		echo "<tr><td class=td_con2 colspan=7 align=center>발급된 쿠폰이 없습니다. 쿠폰을 생성하신 후 발급하시기 바랍니다.</td></tr>\n";
																	}
																?>
															<TR>
																<TD background="images/table_top_line.gif" colspan="7"></TD>
															</TR>
															</form>
														</TABLE>

													</td>
												</tr>
												<tr>
													<td height="25"><span id="chkMsg"></span></td>
												</tr>
												<tr>
													<td height=40 align="center">
														<img src="images/botteon_save.gif" alt="적용하기" style="cursor:pointer;" onclick="opener.couponListReload ( '<?=$groupCode?>' ); self.close();">
														<a href="market_couponnew.php?popup=OK&gubun=GROUP&grp=<?=$groupCode?>"><img src="images/btn_cupon_make2.gif" alt="신규쿠폰생성" style="cursor:pointer;"></a>
													</td>
												</tr>
												<tr>
													<td height=20></td>
												</tr>
												<tr>
													<td>

														<!-- <A href="javascript:document.location.reload()">새로고침</A> -->

														<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
															<TR>
																<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
																<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
																<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
																<TD background="images/manual_bg.gif"></TD>
																<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
															</TR>
															<TR>
																<TD background="images/manual_left1.gif"></TD>
																<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
																	<table cellpadding="0" cellspacing="0" width="100%">
																		<tr>
																			<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																			<td width="701"><span class="font_dotline">생성된 쿠폰 즉시발급</span></td>
																		</tr>
																		<tr>
																			<td width="20" align="right">&nbsp;</td>
																			<td width="701" class="space_top">- 체크 박스에 체크 하시면 해당 등급으로 승급시 자동 발급됩니다.</td>
																		</tr>
																		<tr>
																			<td width="20" align="right">&nbsp;</td>
																			<td width="701" class="space_top"><span class="font_orange">- 새로운 쿠폰 생성후 원하는 쿠폰을 목록에서 선택 적용 해주세요.</span></td>
																		</tr>
																	</table>
																</TD>
																<TD background="images/manual_right1.gif"></TD>
															</TR>
															<TR>
																<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
																<TD COLSPAN=3 background="images/manual_down.gif"></TD>
																<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
															</TR>
														</TABLE>

													</td>
												</tr>
												<tr>
													<td height="50"></td>
												</tr>
											</table>

										</td>
										<td width="16" background="images/con_t_02_bg.gif"></td>
									</tr>
									<tr>
										<td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
										<td background="images/con_t_04_bg.gif"></td>
										<td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
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
<?=$onload?>