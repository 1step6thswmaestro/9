<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-1";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

if (!$year) $year = date("Y");
if (!$month) $month = date("m");
if (!$day) $day = date("d");

$month = $month*1;
$day = $day*1;

if ($month < 10) $month = "0".$month;
if ($day < 10) $day = "0".$day;

$inputY = $year;
$inputM = $month;

$totaldays = get_totaldays($inputY,$inputM);

if ($totaldays <= 0) {
	echo "<script>alert('날짜 선택이 잘못되었습니다.');history.go(-1);</script>";
	exit;
}

function get_totaldays($year,$month) {
	$date = 1;
	while(checkdate($month,$date,$year)) {
		$date++;
	}

	$date--;

	return $date;
}

function showCalendar($year,$month,$total_days) {
	$first_day = date('w', mktime(0,0,0,$month,1,$year));

	unset($valueStr);

	$col = 0;
	for($i=0;$i<$first_day;$i++) {
		if($i == 0) {
			$month_class_str	= "td_con2";
		} else {
			$month_class_str = "td_con1";
		}

		$valueStr .= "<TD class=".$month_class_str." width=\"100\" height=\"90\" valign=\"top\">&nbsp;</td>";
		$col++;
	}

	$sql = "SELECT idx,import,rest,subject,duedate,duetime FROM tblschedule ";
	$sql.= "WHERE duedate LIKE '".$year.$month."%' ORDER BY duetime ASC ";
	$result = mysql_query($sql,get_db_conn());

	unset($data);
	while($row = mysql_fetch_object($result)) {
		if (count($data[$row->duedate]) == 3) {
			continue;
		}

		$data[$row->duedate][count($data[$row->duedate])] = $row;
		if ($row->rest == "Y") {
			$restDate[$row->duedate] = "Y";
		}
	}
	mysql_free_result($result);

	for($j=1;$j<=$total_days;$j++) {
		unset($dayname);
		unset($fontColor);		
		$dayname = $j;

		$enum = $j;
		if ($j < 10) $enum = "0".$j;

		if ($col == 0) {
			$fontColor = "font_orange";
		} else if ($col == 6) {
			$fontColor = "font_blue";
			if ($restDate[$year.$month.$enum] == "Y") {
				$fontColor = "font_orange";
			}
			$dayname = "$j";
		} else {
			if ($restDate[$year.$month.$enum] == "Y") {
				$fontColor = "font_orange";
			} else {
				$fontColor = "c_calender_text";
			}
			$dayname = "$j";
		}

		if($col == 0) {
			$month_class_str	= "td_con2";
		} else {
			$month_class_str = "td_con1";
		}

		$valueStr .= "<TD width=\"100\" height=\"90\" valign=\"top\" class=".$month_class_str." onMouseOver=\"this.style.backgroundColor='#8DDAF4'\" onMouseOut=\"this.style.backgroundColor=''\">";
		$valueStr .= "<TABLE cellSpacing=0 cellPadding=0 width=\"100%\" border=0><TR>";
		if (count($data[$year.$month.$enum]) > 0) {
			$valueStr .= "<TD class=".$fontColor."><b><a href=\"community_schedule_day.php?year=".$year."&month=".$month."&day=".$j."\">".$dayname."(".count($data[$year.$month.$enum]).")</b></a> <FONT class=smallfont><span class=\"font_orange\"><b><img src=\"images/icon_fdr.gif\" width=\"9\" height=\"9\" border=\"0\"></b></span></FONT>";
		} else {
			$valueStr .= "<TD class=".$fontColor."><a href=\"community_schedule_day.php?year=".$year."&month=".$month."&day=".$j."\">".$dayname."</a>";
		}
		$valueStr .= "</TD>";
		$valueStr .= "<TD align=right><B><a style=\"CURSOR:hand;\" onClick=\"OpenWindow('community_schedule_add.php?year=".$year."&month=".$month."&day=".$j."',350,130,'no','schedule')\"><img src=\"images/icon_date_add.gif\" width=\"16\" height=\"16\" border=\"0\" hspace=\"2\" align=absmiddle></a></B></td>";
		$valueStr .= "</TR>";
		$valueStr .= "<tr><TD class=verdana colspan=\"2\">";
		if (count($data[$year.$month.$enum]) > 0) {
			for($kk=0;$kk<count($data[$year.$month.$enum]);$kk++) {
				if ($kk == 0) {
					$valueStr .= "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
				} else {
					$valueStr .= "";
				}

				unset($scheduleSubject);
				$scheduleSubject = $data[$year.$month.$enum][$kk]->subject;
				if ($data[$year.$month.$enum][$kk]->import == "Y") {
					$scheduleSubject = "<B>".$scheduleSubject."</B>";
				}
				
				$valueStr .= "<tr><td width=\"100%\"><img src=\"images/icon_point1.gif\" width=\"6\" height=\"7\" border=\"0\">".$scheduleSubject."</td></tr>";
			}
			$valueStr .= "</table>";
		} else {
			$valueStr .= "</td></tr>";
		}
		$valueStr .= "</table>";
		$valueStr .= "</td>";
		$col++;

		if ($col == 7) {
			$valueStr .= "<TR><TD colspan=\"7\" width=\"760\" background=\"images/table_con_line.gif\"><img src=\"images/table_con_line.gif\" width=\"4\" height=\"1\" border=\"0\"></TD></TR>";
			if ($j != $total_days) {
				$valueStr .= "<tr>";
			}
			$col = 0;
		}
	}

	while($col > 0 && $col < 7) {
		if($i == 0) {
			$month_class_str	= "td_con2";
		} else {
			$month_class_str = "td_con1";
		}

		$valueStr .= "<TD class=".$month_class_str." width=\"100\" height=\"90\" valign=\"top\">&nbsp;</td>";
		$col++;
	}
	$valueStr .= "</tr>";
	
	return $valueStr;
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
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
			<? include ("menu_community.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 커뮤니티 &gt; 커뮤니티 관리  &gt; <span class="2depth_select">쇼핑몰 일정관리</span></td>
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
			<form action="community_schedule_month.php" method='get'>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/community_schedule_ytitle.gif"  ALT=""></TD>
					</tr><tr>
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
					<TD width="100%" class="notice_blue">쇼핑몰의 주요 일정을 관리하실 수 있습니다.</TD>
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
				<td background="images/community_schedule_tepbg.gif">
				<table cellpadding="0" cellspacing="0" width="100%">				
				<tr>
					<TD><a href="community_schedule_year.php?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>" onmouseover="document.m1.src='images/community_schedule_tep1.gif'" onmouseout="document.m1.src='images/community_schedule_tep1r.gif'"><img src='images/community_schedule_tep1r.gif' border='0' name='m1'></A></TD>
					<TD><a href='community_schedule_month.php?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>'><img src='images/community_schedule_tep2.gif' border='0' name='m2'></A></TD>
					<TD><a href='community_schedule_week.php?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>' onmouseover="document.m3.src='images/community_schedule_tep3.gif'" onmouseout="document.m3.src='images/community_schedule_tep3r.gif'"><img src='images/community_schedule_tep3r.gif' border='0' name='m3'></A></TD>
					<TD><a href='community_schedule_day.php?year=<?=$year?>&month=<?=$month?>&day=<?=$day?>' onmouseover="document.m4.src='images/community_schedule_tep4.gif'" onmouseout="document.m4.src='images/community_schedule_tep4r.gif'"><img src='images/community_schedule_tep4r.gif' border='0' name='m4'></A></TD>
					<td width="100%">
					<div align="right">
					<table cellpadding="0" cellspacing="0" width="170">
					<tr>
						<td width="73" align="right">
						<SELECT name=year size="1" class="select">
	<?
						for($y=2000;$y<=date("Y")+5;$y++) {
							unset($select);
							if ($y == $year) $select = "selected";
							echo "<option value='".$y."' ".$select.">".$y." 년</option>";
						}
	?>
						</SELECT>
						</td>
						<td width="73"align="right">
						<SELECT name=month class="select">
	<?
						for($y=1;$y<=12;$y++) {
							unset($select);
							unset($yn);
							$yn = $y;
							if ($y<10) $yn = "0".$y;
							if ($yn == $month) $select = "selected";
							echo "<option value='".$yn."' ".$select.">".$yn." 월</option>";
						}

	?>
						</SELECT>
						</td>
						<td width="207" align="right"><input type="image" style="MARGIN: 0px 2px 2px 2px" height="25" src="images/btn_search2.gif" width="50" border="0"></td>
					</tr>
					</table>
					</div>
					</td>
				</tr>
				</table>
				
			</tr>
			<tr>
				<td height=3>&nbsp;</td>
			</tr>
			<tr>
				<td>				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=7 background="images/table_top_line.gif"></TD>
				</TR>
				<TR align=center>
					<TD width="100" bgcolor="#F0FDFF" height="30" background="images/blueline_bg.gif"><b><span class="font_orange">일(日)</span></b></TD>
					<TD class="td_con1" width="100" bgcolor="#F0FDFF" background="images/blueline_bg.gif"><font color="#0099CC"><b>월(月)</b></font></TD>
					<TD class="td_con1" width="100" bgcolor="#F0FDFF" background="images/blueline_bg.gif"><font color="#0099CC"><b>화(火)</b></font></TD>
					<TD class="td_con1" width="100" bgcolor="#F0FDFF" background="images/blueline_bg.gif"><font color="#0099CC"><b>수(水)</b></font></TD>
					<TD class="td_con1" width="100" bgcolor="#F0FDFF" background="images/blueline_bg.gif"><font color="#0099CC"><b>목(木)</b></font></TD>
					<TD class="td_con1" width="100" bgcolor="#F0FDFF" background="images/blueline_bg.gif"><font color="#0099CC"><b>금(金)</b></font></TD>
					<TD class="td_con1" width="100" bgcolor="#F0FDFF" background="images/blueline_bg.gif"><font color="#8240A3"><b>토(土)</b></font></TD>
				</TR>
				<TR>
					<TD colspan="7" background="images/table_con_line.gif"></TD>
				</TR>
				<?= showCalendar($inputY,$inputM,$totaldays); ?>
				<TR>
					<TD colspan=7 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>				
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
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
					<col width=20></col>
					<col width=></col>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">쇼핑몰 일정관리(MONTH)</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 월(Month) 단위로 쇼핑몰 주요 일정이 출력됩니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 기록된 내용은 해당 일별로 관리자 페이지 메인에 출력됩니다.</td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">일정 기록 방법</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 날짜 : 해당 일정 날짜를 입력하세요. 년원일시 까지 지정 가능합니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 제목 : 일정표상에 출력되는 제목을 입력하세요.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 내용 : 일정 상세 내용을 입력하세요.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 일정 : 일반일정, 중요일정으로 구분되며 중요일정 지정시 일정제목이 두껍게 표기됩니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 휴일 : 비공휴일, 공휴일지정으로 구분되며 공휴일 지정시 해당 날짜의 색상이 붉은색으로 표기됩니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 반복/회수 : 해당 일정의 반복주기와 횟수를 입력하면 해당 주기에 맞춰 일정이 자동입력됩니다.<br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;예) 3월 12일 메모 주단위 2회 반복 -> 3월 12일, 3월 19일 두군데 기록</td>
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
			<tr><td height="50"></td></tr>
			</form>
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