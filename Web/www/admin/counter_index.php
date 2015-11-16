<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "st-1";
$MenuCode = "counter";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$regdate=$_shopdata->regdate;

$today = date("Ymd");
$year=date("Y");
$month=date("m");
$day=date("d");
?>


<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
</script>
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
			<? include ("menu_counter.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 통계분석 &gt; <span class="2depth_select">통계분석 HOME</span></td>
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
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/counter_main_title.gif" ALT=""></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height=21></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="725" align="center">
				<tr>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="354">
					<tr>
						<td width="390"><IMG SRC="images/counter_main_img1.gif" WIDTH=354 HEIGHT=29 ALT=""></td>
					</tr>
					<tr>
						<td width="390" background="images/counter_main_imgbg.gif">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td height="15"></td>
						</tr>
						<tr>
							<td align=center><IMG SRC="graph/maingraph_1.php"></td>
						</tr>
						<tr>
							<td height="11"></td>
						</tr>
						<tr>
							<td>
							<table align="center" cellpadding="0" cellspacing="0" width="326" height="105">
							<tr>
								<td width="344" background="images/counter_main_bg.gif" style="padding:10pt;">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<TR>
									<TD background="images/table_top_line1.gif" width="760" colspan="3"><img src="images/table_top_line1.gif" width="13" height="2"></TD>
								</TR>
								<TR>
									<TD bgcolor="#F3F3F3" align=center><FONT color=#3d3d3d><b>&nbsp;</b></FONT></TD>
									<TD bgcolor="#F3F3F3" align=center><FONT color=#3d3d3d><b>오늘</b></FONT></TD>
									<TD bgcolor="#F3F3F3" align=center><b>전일대비</b></TD>
								</TR>
								<TR>
									<TD colspan="3" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
								</TR>
<?
								$arrayname=array ("시간(時)","당일(日)","월간(月)","주간(週)");
								$time[0]  = date("YmdH");
								$time1[0]  = date("YmdH",mktime(date("H"),0,0,date("m"),$day-1,$year));
								$time[1]  = date("Ymd");
								$time1[1]  = date("Ymd",mktime(0,0,0,date("m"),$day-1,$year));
								$time2[1]  = date("Ymd",mktime(0,0,0,date("m"),$day-1,$year))."00";
								$time[2]  = date("Ym");
								$time1[2]  = date("Ym",mktime(0,0,0,date("m")-1,1,$year));
								$sqlcou[0] ="SELECT cnt,date FROM tblcounter WHERE (date='".$time[0]."' OR date='".$time1[0]."') ";
								$sqlcou[1] ="SELECT SUM(cnt) as cnt,MID(date,1,8) as date FROM tblcounter ";
								$sqlcou[1].="WHERE (date LIKE '".$time[1]."%' OR (date>'".$time2[1]."' AND date<'".$time1[0]."')) ";
								$sqlcou[1].="GROUP BY date ";
								$sqlcou[2] ="SELECT SUM(cnt) as cnt,MID(date,1,6) as date FROM tblcounter ";
								$sqlcou[2].="WHERE (date LIKE '".$time[2]."%' OR date LIKE '".$time1[2]."%') GROUP BY date ";

								for($i=0;$i<3;$i++){
									$result=mysql_query($sqlcou[$i],get_db_conn());
									while($row=mysql_fetch_object($result)){
										$num[$row->date]=$row->cnt; 
									}
									$num2[$i]=$num[$time[$i]]-$num[$time1[$i]];
									mysql_free_result($result);
									if($i<>0) {
										echo "<TR><TD height=1 colspan=\"3\" background=\"images/table_con_line.gif\"><img src=\"images/table_con_line.gif\" border=\"0\"></TD></TR>\n";
									}
									echo "<TR>\n";
									echo "	<TD align=center>".$arrayname[$i]."</TD>\n";
									echo "	<TD align=\"right\" style=\"padding-right:5\">".number_format($num[$time[$i]])."</TD>\n";
									echo "	<TD>\n";
									echo "	<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" width=\"40\">\n";
									echo "	<tr>\n";
									echo "		<td width=\"10\" align=center>".($num2[$i]>0?"<font color=\"#0099CC\">▲</font>":($num2[$i]<0?"<font color=\"red\">▼</font>":""))."</td>\n";
									echo "		<td width=\"100%\" align=\"right\" style=\"padding-right:5\">".number_format(abs($num2[$i]))."</td>\n";
									echo "	</tr>\n";
									echo "	</table>\n";
									echo "	</TD>\n";
									echo "</TR>\n";
								}
?>
								</TABLE>
								</td>
							</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td width="344">&nbsp;</td>
						</tr>
						<tr>
							<td width="344">
							<table cellpadding="0" cellspacing="0" width="326" align="center">
							<tr>
								<td width="344"><IMG SRC="images/counter_main_stitle1.gif" WIDTH=326 HEIGHT=17 ALT=""></td>
							</tr>
<?
							$sql ="SELECT MID(date,9,2) as hour,cnt FROM tblcounter ";
							$sql.="WHERE date LIKE '".$time[1]."%' GROUP BY date ORDER BY cnt DESC LIMIT 3";
							$result=mysql_query($sql,get_db_conn());
							$count=0;
							while($row=mysql_fetch_object($result)) {
								$count++;
								echo "<tr>\n";
								echo "	<td width=\"344\" class=\"font_size\" style=\"padding-left:10pt;\">- ".$row->hour."시 ~ ".($row->hour+1)."시<b> <font class=\"font_orange2\">".number_format($row->cnt)."명</font></b></td>\n";
								echo "</tr>\n";
							}
							mysql_free_result($result);
?>
							<tr><td height=10></td></tr>
							<tr>
								<td width="344"><IMG SRC="images/counter_main_stitle2.gif" WIDTH=327 HEIGHT=18 ALT=""></td>
							</tr>
<?
							$sql ="SELECT MID(date,1,8) as mon,date,SUM(cnt) as cnt FROM tblcounter ";
							$sql.="GROUP BY mon UNION SELECT MID(date,1,8) as mon,date,cnt ";
							$sql.="FROM tblcountermonth GROUP BY mon ORDER BY cnt DESC LIMIT 2";
							$count=0;
							$result=mysql_query($sql,get_db_conn());
							while($row=mysql_fetch_object($result)) {
								$count++;
								$date=substr($row->mon,0,4)."년 ".substr($row->mon,4,2)."월 ".substr($row->mon,6,2)."일";
								$count++;
								echo "<tr>\n";
								echo "	<td width=\"344\" class=\"font_size\" style=\"padding-left:10pt;\">- ".$date."<b> <font class=\"font_orange2\">".number_format($row->cnt)."명</font></b></td>\n";
								echo "</tr>\n";
							}
							mysql_free_result($result);
?>
							<tr><td height=10></td></tr>
							<tr>
								<td width="344"><IMG SRC="images/counter_main_stitle3.gif" WIDTH=328 HEIGHT=18 ALT=""></td>
							</tr>
<?
							$sql ="SELECT MID(date,1,8) as mon,date,SUM(cnt) as cnt FROM tblcounter ";
							$sql.="WHERE MID(date,1,8)<>'".$today."' GROUP BY mon UNION SELECT MID(date,1,8) as mon,date,cnt FROM tblcountermonth WHERE date<>'".$regdate."' GROUP BY mon HAVING mon<>'".$regdate."' AND mon<>'".$today."' ORDER BY cnt LIMIT 2";
							$count=0;
							$result=mysql_query($sql,get_db_conn());
							while($row=mysql_fetch_object($result)) {
								$count++;
								$date=substr($row->mon,0,4)."년 ".substr($row->mon,4,2)."월 ".substr($row->mon,6,2)."일";
								$count++;
								echo "<tr>\n";
								echo "	<td width=\"344\" class=\"font_size\" style=\"padding-left:10pt;\">- ".$date."<b> <font class=\"font_orange2\">".number_format($row->cnt)."명</font></b></td>\n";
								echo "</tr>\n";
							}
							mysql_free_result($result);
?>
							</table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td width="390"><IMG SRC="images/counter_main_imgdown.gif" WIDTH=354 HEIGHT=24 ALT=""></td>
					</tr>
					</table>
					</td>
					<td valign="top" width="30">&nbsp;</td>
					<td valign="top">
					<table cellpadding="0" cellspacing="0" width="354">
					<tr>
						<td width="390"><IMG SRC="images/counter_main_img2.gif" WIDTH=354 HEIGHT=29 ALT=""></td>
					</tr>
					<tr>
						<td width="390" background="images/counter_main_imgbg.gif">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td height="15"></td>
						</tr>
						<tr>
							<td align=center><IMG SRC="graph/maingraph_2.php"></td>
						</tr>
						<tr>
							<td height="11"></td>
						</tr>
						<tr>
							<td>
							<table align="center" cellpadding="0" cellspacing="0" width="326" height="105">
							<tr>
								<td width="344" background="images/counter_main_bg.gif" style="padding:10pt;">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<TR>
									<TD background="images/table_top_line1.gif" colspan="3"></TD>
								</TR>
								<TR>
									<TD bgcolor="#F3F3F3" align=center></TD>
									<TD bgcolor="#F3F3F3" align=center><FONT color=#3d3d3d><b>오늘</b></FONT></TD>
									<TD bgcolor="#F3F3F3" align=center><b>전일대비</b></TD>
								</TR>
								<TR>
									<TD colspan="3" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
								</TR>
<?
								$arrayname=array ("시간(時)","당일(日)","월간(月)","주간(週)");
								$time[0]  = date("YmdH");
								$time1[0]  = date("YmdH",mktime(date("H"),0,0,date("m"),$day-1,$year));
								$time[1]  = date("Ymd");
								$time1[1]  = date("Ymd",mktime(0,0,0,date("m"),$day-1,$year));
								$time2[1]  = date("Ymd",mktime(0,0,0,date("m"),$day-1,$year))."00";
								$time[2]  = date("Ym");
								$time1[2]  = date("Ym",mktime(0,0,0,date("m")-1,1,$year));
								$sqlcou[0] ="SELECT cnt,date FROM tblcounterorder WHERE (date='".$time[0]."' OR date='".$time1[0]."') ";
								$sqlcou[1] ="SELECT SUM(cnt) as cnt,MID(date,1,8) as date FROM tblcounterorder ";
								$sqlcou[1].="WHERE (date LIKE '".$time[1]."%' OR (date>'".$time2[1]."' AND date<'".$time1[0]."')) ";
								$sqlcou[1].="GROUP BY date ";
								$sqlcou[2] ="SELECT SUM(cnt) as cnt,MID(date,1,6) as date FROM tblcounterorder ";
								$sqlcou[2].="WHERE (date LIKE '".$time[2]."%' OR date LIKE '".$time1[2]."%') GROUP BY date ";

								for($i=0;$i<3;$i++){
									$result=mysql_query($sqlcou[$i],get_db_conn());
									while($row=mysql_fetch_object($result)){
										$ornum[$row->date]=$row->cnt; 
									}
									$ornum2[$i]=$ornum[$time[$i]]-$ornum[$time1[$i]];
									mysql_free_result($result);
									if($i<>0) {
										echo "<TR><TD height=1 colspan=\"3\" background=\"images/table_con_line.gif\"><img src=\"images/table_con_line.gif\" border=\"0\"></TD></TR>\n";
									}
									echo "<TR>\n";
									echo "	<TD align=center>".$arrayname[$i]."</TD>\n";
									echo "	<TD align=\"right\" style=\"padding-right:5\">".number_format($ornum[$time[$i]])."</TD>\n";
									echo "	<TD>\n";
									echo "	<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" width=\"40\">\n";
									echo "	<tr>\n";
									echo "		<td width=\"10\" align=center>".($ornum2[$i]>0?"<font color=\"#0099CC\">▲</font>":($ornum2[$i]<0?"<font color=\"red\">▼</font>":""))."</td>\n";
									echo "		<td width=\"100%\" align=\"right\" style=\"padding-right:5\">".number_format(abs($ornum2[$i]))."</td>\n";
									echo "	</tr>\n";
									echo "	</table>\n";
									echo "	</TD>\n";
									echo "</TR>\n";
								}
?>
								</TABLE>
								</td>
							</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td width="344">&nbsp;</td>
						</tr>
						<tr>
							<td>
							<table cellpadding="0" cellspacing="0" width="326" align="center">
							<tr>
								<td width="344"><IMG SRC="images/counter_main_stitle4.gif" WIDTH=326 HEIGHT=17 ALT=""></td>
							</tr>
<?
							$sql ="SELECT MID(date,9,2) as hour,cnt FROM tblcounterorder ";
							$sql.="WHERE date LIKE '".$time[1]."%' GROUP BY date ORDER BY cnt DESC LIMIT 3";
							$result=mysql_query($sql,get_db_conn());
							$count=0;
							while($row=mysql_fetch_object($result)) {
								$count++;
								echo "<tr>\n";
								echo "	<td width=\"344\" class=\"font_size\" style=\"padding-left:10pt;\">- ".$row->hour."시 ~ ".($row->hour+1)."시<b> <font class=\"font_orange2\">".number_format($row->cnt)."건</font></b></td>\n";
								echo "</tr>\n";
							}
							mysql_free_result($result);
?>
							<tr><td height=10></td></tr>
							<tr>
								<td width="344"><IMG SRC="images/counter_main_stitle5.gif" WIDTH=327 HEIGHT=18 ALT=""></td>
							</tr>
<?
							$sql ="SELECT MID(date,1,8) as mon,date,SUM(cnt) as cnt FROM tblcounterorder ";
							$sql.="GROUP BY mon UNION SELECT MID(date,1,8) as mon,date,cnt ";
							$sql.="FROM tblcounterordermonth GROUP BY mon ORDER BY cnt DESC LIMIT 2";
							$count=0;
							$result=mysql_query($sql,get_db_conn());
							while($row=mysql_fetch_object($result)) {
								$count++;
								$date=substr($row->mon,0,4)."년 ".substr($row->mon,4,2)."월 ".substr($row->mon,6,2)."일";
								$count++;
								echo "<tr>\n";
								echo "	<td width=\"344\" class=\"font_size\" style=\"padding-left:10pt;\">- ".$date."<b> <font class=\"font_orange2\">".number_format($row->cnt)."건</font></b></td>\n";
								echo "</tr>\n";
							}
							mysql_free_result($result);
?>
							<tr><td height=10></td></tr>
							<tr>
								<td width="344"><IMG SRC="images/counter_main_stitle6.gif" WIDTH=328 HEIGHT=18 ALT=""></td>
							</tr>
<?
							$sql ="SELECT MID(date,1,8) as mon,date,SUM(cnt) as cnt FROM tblcounterorder ";
							$sql.="WHERE MID(date,1,8)<>'".$today."' GROUP BY mon UNION SELECT MID(date,1,8) as mon,date,cnt FROM tblcounterordermonth WHERE date<>'".$regdate."' GROUP BY mon HAVING mon<>'".$regdate."' AND mon<>'".$today."' ORDER BY cnt LIMIT 2";
							$count=0;
							$result=mysql_query($sql,get_db_conn());
							while($row=mysql_fetch_object($result)) {
								$count++;
								$date=substr($row->mon,0,4)."년 ".substr($row->mon,4,2)."월 ".substr($row->mon,6,2)."일";
								$count++;
								echo "<tr>\n";
								echo "	<td width=\"344\" class=\"font_size\" style=\"padding-left:10pt;\">- ".$date."<b> <font class=\"font_orange2\">".number_format($row->cnt)."건</font></b></td>\n";
								echo "</tr>\n";
							}
							mysql_free_result($result);
?>
							</table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td width="390"><IMG SRC="images/counter_main_imgdown.gif" WIDTH=354 HEIGHT=24 ALT=""></td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
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
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top"  style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><FONT color=#3d3d3d>시간 흐름(일/주간/월간)에 따른 순방문자/페이지뷰/주문시도건수를 그래프로 한눈에 볼 수 있습니다.</FONT></td>
					</tr>
					<tr><td colspan=2 height=5></td></tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>
							<FONT color=#3d3d3d>시간 흐름에 따른 쇼핑몰 중요 데이터를 그림으로 쉽게 분석할 수 있습니다.</FONT>
						</td>
					</tr>
					<tr><td colspan=2 height=5></td></tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>
							<FONT color=#3d3d3d>하루 하루 나타나는 데이터를 출력하여 모아 놓으면, 아주 소중한 쇼핑몰 운영가이드책이 될 수 있습니다.</FONT>
						</td>
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
			<tr><td height="30"></td></tr>
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
</table>
<?=$onload?>

<? INCLUDE "copyright.php"; ?>