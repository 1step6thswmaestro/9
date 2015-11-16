<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");
include_once($Dir."lib/admin_more.php");

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
	echo "<html></head><body onload=\"alert('날짜 선택이 잘못되었습니다.');history.go(-1)\"></body></html>";exit;
}

function get_totaldays($year,$month) {
	$date = 1;
	while(checkdate($month,$date,$year)) {
		$date++;
	}

	$date--;

	return $date;
}

function showCalendar($year,$month,$total_days,$vender) {

	$_vdata = getVenderInfo($vender);
	$v_account_date = $_vdata['account_date'];
	$a_date = explode(",", $v_account_date);
	$a_date_count = count($a_date);
	if ($a_date_count<0) {
		echo "<html></head><body onload=\"alert('업체의 정산일을 지정해주세요.');location.href='vender_info.php'\"></body></html>";exit;
	}


	$first_day = date('w', mktime(0,0,0,$month,1,$year));

	unset($valueStr);
	$col = 0;
	for($i=0;$i<$first_day;$i++) {
		$valueStr .= "<td bgcolor=#FFFFFF>&nbsp;</td>";
		$col++;
	}

	$sql = "SELECT date,price,confirm,bank_account,memo FROM order_account_new ";
	$sql.= "WHERE vender='".$vender."' AND date LIKE '".$year.$month."%' ";
	$result = mysql_query($sql,get_db_conn());

	unset($data);
	while($row = mysql_fetch_object($result)) {
		$data[$row->date] = $row;
	}
	mysql_free_result($result);

	for($j=1;$j<=$total_days;$j++) {
		unset($dayname);
		$dayname = $j;

		$enum = $j;
		if ($j < 10) $enum = "0".$j;

		if ($col == 0) {
			$dayname = "<font color=red size=2>".$j."</font>";
		} else if ($col == 6) {
			$fontColor = "blue";
			$dayname = "<font color=".$fontColor." size=2>".$j."</font>";
		} else {
			$fontColor = "#000000";
			$dayname = "<font color=".$fontColor." size=2>".$j."</font>";
		}
		$valueStr .= "<td valign='top' bgcolor='#FFFFFF' height='55' width=14% valign=top ";

		if (count($data[$year.$month.$enum])>0 && $data[$year.$month.$enum]->confirm=="Y") {
			$valueStr .= "background=\"images/icon_signing.gif\" ";
		}


		$valueStr .= "style=\"background-repeat:no-repeat;background-position:right\" onMouseOver=\"this.style.backgroundColor='#fafafa'\" onMouseOut=\"this.style.backgroundColor=''\">\n";
		$valueStr .= "<table border=0 cellspacing=0 cellpadding=0 width=100%>\n";
		$valueStr .= "<tr>\n";
		$valueStr .= "	<td class=verdana style=\"padding:3px\">".$dayname."</td>\n";
		$valueStr .= "</tr>";
		$valueStr .= "<tr>\n";
		$valueStr .= "	<td align=right class=verdana style=\"padding:0,3,3,3; color:red\">\n";
		if (count($data[$year.$month.$enum])>0) {
			$valueStr .= "<A HREF=\"javascript:detailView(".$year.$month.$enum.")\"><FONT color=red size=2><B>".number_format($data[$year.$month.$enum]->price)."</B></FONT></A>";
		}
		$valueStr .= "	</td>\n";
		$valueStr .= "</tr>";
		$valueStr .= "</table>";
		$valueStr .= "</td>";
		$col++;

		if ($col == 7) {
			$valueStr .= "</tr>";
			if ($j != $total_days) {
				$valueStr .= "<tr>";
			}
			$col = 0;
		}
	}

	while($col > 0 && $col < 7) {
		$valueStr .= "<td bgcolor='#FFFFFF'>&nbsp;</td>";
		$col++;
	}
	$valueStr .= "</tr>";
	
	return $valueStr;
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function detailView(date) {
	owin=windowOpenScroll("about:blank","calendar_detailview",400,300);
	owin.focus();
	document.dForm.date.value=date;
	document.dForm.target="calendar_detailview";
	document.dForm.action="sellstat_calendar.detail.php";
	document.dForm.submit();
}
</script>
<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed"  height="100%" >
<col width=190></col>
<col width=20></col>
<col width=></col>
<col width=20></col>
<tr>
	<td width=190 valign=top nowrap background="images/minishop_leftbg.gif"><? include ("menu.php"); ?></td>
	<td width=20 nowrap></td>
	<td valign=top style="padding-top:20px">

	<table width="100%"  border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td><img src="images/sellstat_calendar_title.gif"></td>
				</tr>
				<tr>
					<td height=5 background="images/minishop_titlebg.gif">
				</tr>
				</table>
			</td>
		</tr>
		<tr><td height=10></td></tr>
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td colspan=3 >


						<table cellpadding="10" cellspacing="1" width="100%" bgcolor="#EFEFF2">
							<tr>
								<td  bgcolor="#F5F5F9" style="padding:20px">
									<table border=0 cellpadding=0 cellspacing=0 width=100%>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">본사에서 정산처리된 내역을 손쉽게 확인할 수 있습니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">정산금액 클릭시 상세정보를 확인할 수 있습니다.</td>
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

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=40></td></tr>
			<tr>
				<td>
				


				
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td style="padding-bottom:3">

					<table cellpadding='0' cellspacing='0'>
					<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method='get'>
					<TR>
						<TD>
						<select name='year'>
<?
						for($y=2006;$y<=date("Y");$y++) {
							unset($select);
							if ($y == $year) $select = "selected";
							echo "<option value='".$y."' ".$select.">".$y." 년</option>";
						}
?>
						</select>
						<select name='month'>
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
						</select>
						</tD>
						<td width=5></td>
						<tD><A HREF="javascript:document.form1.submit()"><img src='images/btn_inquery02.gif' border=0></A></TD>
					</TR>
					</form>
					</table>

					</td>
				</tr>
				<tr><td height=1 bgcolor=#cccccc></td></tr>
				<tr>
					<td>
					<table  border='0' cellspacing='1' cellpadding='3' bgcolor="#cccccc" width=100% style="table-layout:fixed">
					<tr bgcolor='f4f4f4' height='30'>
						<td align='center'><font color='red'>일(日)</font></td>
						<td align='center'>월(月)</td>
						<td align='center'>화(火)</td>
						<td align='center'>수(水)</td>
						<td align='center'>목(木)</td>
						<td align='center'>금(金)</td>
						<td align='center'><font color='blue'>토(土)</font></td>
					</tr>
					<TR><TD colspan='7' bgcolor='ffffff'></TD></TR>
					<tr>

					<?= showCalendar($inputY,$inputM,$totaldays,$_VenderInfo->getVidx()); ?>

					</table>
					</td>
				</tr>
				</table>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>

	</td>
</tr>

<form name=dForm method=post>
<input type=hidden name=date>
</form>

</table>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>