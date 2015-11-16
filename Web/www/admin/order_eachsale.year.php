<?
$qry.= "WHERE ";
$qry.= " a.ordercode LIKE '".$date_year."%' AND b.ordercode LIKE '".$date_year."%' ";
if($paymethod!="ALL") $qry.= "AND a.paymethod LIKE '".$paymethod."%' ";
if($loc!="ALL") {
	if($loc=="기타") $qry.= "AND a.loc is NULL ";
	else $qry.= "AND a.loc = '".$loc."' ";
}
if(strlen($prcode)>0) {
	$qry.= "AND b.productcode = '".$prcode."' ";
} else if(strlen($code)==12) {
	$qry.= "AND b.productcode LIKE '".$likecode."%' ";
}

$sql = "SELECT /*SQL_CACHE*/ ";
$sql.= "SUM(IF(a.deli_gbn='Y' || a.deli_gbn='S' || a.deli_gbn='X' || a.deli_gbn='D' || a.deli_gbn='E' || a.deli_gbn='H',b.quantity,NULL)) as Ycnt, ";
$sql.= "SUM(IF(a.deli_gbn='N',b.quantity,NULL)) as Ncnt, ";
$sql.= "SUM(IF(a.deli_gbn='R',b.quantity,NULL)) as Rcnt, ";
$sql.= "SUM(IF(a.deli_gbn='Y' || a.deli_gbn='S' || a.deli_gbn='X' || a.deli_gbn='D' || a.deli_gbn='E' || a.deli_gbn='H',b.price*b.quantity,NULL)) as Ysum, ";
$sql.= "SUM(IF(a.deli_gbn='N',b.price*b.quantity,NULL)) as Nsum, ";
$sql.= "SUM(IF(a.deli_gbn='R',b.price*b.quantity,NULL)) as Rsum, ";

$sql.= "substring(a.ordercode,1,8) as day FROM tblorderinfo AS a LEFT JOIN tblorderproduct AS b ON(a.ordercode = b.ordercode) ";

if($age2>0 || $sex!="ALL") {
	$sql.= ", tblmember c ".$qry." AND a.id=c.id ";
	if($age1>0) {
		$start_year = (int)date("Y") - (int)$age2 +1;
		$end_year = (int)date("Y") - (int)$age1 +1;
		$s_year = substr((string)$start_year,2,2);
		$e_year = substr((string)$end_year,2,2);

		if ($start_year < 2000 && $end_year < 2000) {
			 $sql.= "AND (LEFT(c.birth,2) BETWEEN '".$s_year."' AND '".$e_year."') ";
		} else if ($start_year < 2000 && $end_year > 1999) {
			 $sql.= "AND (((LEFT(c.birth,2) BETWEEN '".$s_year."' AND '99') AND ";
			 $sql.= "OR ((LEFT(c.birth,2) BETWEEN '00' AND '".$e_year."') ";
		} else if ($start_year > 1999 && $end_year > 1999) {
			$sql.= "AND (LEFT(c.birth,2) BETWEEN '".$s_year."' AND '".$e_year."') ";
		}
	}
	if($sex=="M") {
		$sql.= "AND c.gender='1' ";
	} else if ($sex=="F") {
		$sql.= "AND c.gender='2' ";
	}
} else {
	$sql.= $qry." ";
	if($member=="Y") {
		$sql.= "AND MID(a.ordercode,21,1)!='X' ";
	} else if($member=="N") {
		$sql.= "AND MID(a.ordercode,21,1)='X' ";
	}
}
$sql.= "GROUP BY month ";

$MAX_barsize=470;
$Ysumtot=0;
$Rsumtot=0;
$Nsumtot=0;
$maxsum=0;

$Ycnttot=0;
$Rcnttot=0;
$Ncnttot=0;

$result = mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)) {
	$month=(int)substr($row->month,4,2);
	$Ysum[$month]=$row->Ysum;
	$Rsum[$month]=$row->Rsum;
	$Nsum[$month]=$row->Nsum;
	if($row->Ysum>$maxsum) $maxsum=$row->Ysum;
	if($row->Rsum>$maxsum) $maxsum=$row->Rsum;
	if($row->Nsum>$maxsum) $maxsum=$row->Nsum;

	$Ycnt[$month]=$row->Ycnt;
	$Rcnt[$month]=$row->Rcnt;
	$Ncnt[$month]=$row->Ncnt;

	$Ysumtot+=$row->Ysum;
	$Rsumtot+=$row->Rsum;
	$Nsumtot+=$row->Nsum;

	$Ycnttot+=$row->Ycnt;
	$Rcnttot+=$row->Rcnt;
	$Ncnttot+=$row->Ncnt;
}
mysql_free_result($result);
?>
<TABLE cellSpacing=0 cellPadding=0 width="760" border=0>
<TR>
	<TD background="images/table_top_line.gif" width="80"><img src="images/table_top_line.gif"></TD>
	<TD background="images/table_top_line.gif" width="680" colspan="2"></TD>
	<td></td>
</TR>
<TR>
	<TD class="table_cell" width="60"><p align="center">날짜</TD>
	<TD class="table_cell1" width="131"><p align="center">매출현황</p></TD>
	<TD class="table_cell1" width="519"><p align="center">해당건수 ( <b><font color="#00ACD9">입금■</font></b> &nbsp;<b><font color="#58BE0E">반송■</font></b> &nbsp;&nbsp;<font color="#FFA800"><b>미입금■ </b></font>)</p></TD>
	<td></td>
</TR>
<TR>
	<TD colspan="4" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<?
for($i=1;$i<=12;$i++) {
	if(($date_year.$i)==date("Yn")) {
		$tdclass="td_con_orange1";
		$tdclass2="td_con_orange";
	} else {
		$tdclass="td_con2";
		$tdclass2="td_con1a";
	}
	echo "<tr>\n";
	echo "	<TD class=\"$tdclass\" width=\"73\"><p align=\"center\">".$i."월</td>\n";
	echo "	<TD class=\"$tdclass2\" width=\"139\">\n";
	echo "	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
	echo "	<tr>\n";
	echo "		<td width=\"54\"><p style='font-size:11px;color:#00ACD9'>입금</p></td>\n";
	echo "		<td width=\"91\"><p align=\"right\">".number_format($Ysum[$i])."원</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td width=\"34\"><p style='font-size:11px;color:#58BE0E'>반송</p></td>\n";
	echo "		<td width=\"111\"><p align=\"right\">".number_format($Rsum[$i])."원</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td width=\"34\"><p style='font-size:11px;color:#FFA800'>미입금</p></td>\n";
	echo "		<td width=\"111\"><p align=\"right\">".number_format($Nsum[$i])."원</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "	</td>\n";
	echo "	<TD class=\"$tdclass2\" width=\"527\">\n";
	echo "	<table cellpadding=\"1\" cellspacing=\"0\" width=\"100%\">\n";
	echo "	<tr>\n";
	echo "		<td width=\"479\">\n";
	echo "		<div align=\"right\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"".(@round(($Ysum[$i] / $maxsum)*$MAX_barsize)>0?"@round(($Ysum[$i] / $maxsum)*$MAX_barsize)":"1")."%\">\n";
	echo "		<tr>\n";
	echo "			<td width=\"516\" height=\"15\" background=\"images/line_bg1.gif\"></td>\n";
	echo "		</tr>\n";
	echo "		</table></div>\n";
	echo "		</td>\n";
	echo "		<td width=\"45\"><p align=\"right\">(".number_format($Ycnt[$i])."건)</p></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td width=\"479\">\n";
	echo "		<div align=\"right\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"".(@round(($Rsum[$i] / $maxsum)*$MAX_barsize)>0?"@round(($Rsum[$i] / $maxsum)*$MAX_barsize)":"1")."%\">\n";
	echo "		<tr>\n";
	echo "			<td width=\"516\" height=\"15\" background=\"images/line_bg2.gif\"></td>\n";
	echo "		</tr>\n";
	echo "		</table></div>\n";
	echo "		</td>\n";
	echo "		<td width=\"45\"><p align=\"right\">(".number_format($Rcnt[$i])."건)</p></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td width=\"479\">\n";
	echo "		<div align=\"right\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"".(@round(($Nsum[$i] / $maxsum)*$MAX_barsize)>0?"@round(($Nsum[$i] / $maxsum)*$MAX_barsize)":"1")."%\">\n";
	echo "		<tr>\n";
	echo "			<td width=\"516\" height=\"15\" background=\"images/line_bg3.gif\"></td>\n";
	echo "		</tr>\n";
	echo "		</table></div>\n";
	echo "		</td>\n";
	echo "		<td width=\"45\"><p align=\"right\">(".number_format($Ncnt[$i])."건)</p></td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "	</td>\n";
	echo "	<td></td>\n";
	echo "</tr>\n";

	if($i != 12) {
		echo "<tr>\n";
		echo "	<TD colspan=\"4\" width=\"760\" background=\"images/table_con_line.gif\"><img src=\"images/table_con_line.gif\" width=\"4\" height=\"1\" border=\"0\"></TD>\n";
		echo "</tr>\n";
	}
}
?>
<TR>
	<TD background="images/table_con_line1.gif" width="80"><img src="images/table_con_line1.gif" width="4" height="1" border="0"></TD>
	<TD background="images/table_con_line1.gif" width="680" colspan="2"></TD>
	<td></td>
</TR>
<tr>
	<TD class="td_con2" width="73"><p align="center">합계</td>
	<TD class="td_con1a" width="139">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="34"><p><img src="images/icon_trans.gif" width="41" height="15" border="0"></p></td>
		<td width="111"><p align="right"><?=number_format($Ysumtot)?>원</td>
	</tr>
	<tr>
		<td width="34"><p><img src="images/icon_back.gif" width="41" height="15" border="0"></p></td>
		<td width="111"><p align="right"><?=number_format($Rsumtot)?>원</td>
	</tr>
	<tr>
		<td width="34"><p><img src="images/icon_not.gif" width="41" height="15" border="0"></p></td>
		<td width="111"><p align="right"><?=number_format($Nsumtot)?>원</td>
	</tr>
	</table>
	</td>
	<TD class="td_con1a" width="527">
	<table cellpadding="1" cellspacing="0" width="100%">
	<tr>
		<td width="479">
		<div align="right"><table cellpadding="0" cellspacing="0" width="<?=(@round(($Ysumtot / ($Ysumtot+$Rsumtot+$Nsumtot))*$MAX_barsize)>0?"@round(($Ysumtot / ($Ysumtot+$Rsumtot+$Nsumtot))*$MAX_barsize)":"1")?>%">
		<tr>
			<td width="516" height="15" background="images/line_bg1.gif"></td>
		</tr>
		</table></div>
		</td>
		<td width="45"><p align="right">(<?=number_format($Ycnttot)?>건)</p></td>
	</tr>
	<tr>
		<td width="479">
		<div align="right"><table cellpadding="0" cellspacing="0" width="<?=(@round(($Rsumtot / ($Ysumtot+$Rsumtot+$Nsumtot))*$MAX_barsize)>0?"@round(($Rsumtot / ($Ysumtot+$Rsumtot+$Nsumtot))*$MAX_barsize)":"1")?>%">
		<tr>
			<td width="516" height="15" background="images/line_bg2.gif"></td>
		</tr>
		</table></div>
		</td>
		<td width="45"><p align="right">(<?=number_format($Rcnttot)?>건)</p></td>
	</tr>
	<tr>
		<td width="479">
		<div align="right"><table cellpadding="0" cellspacing="0" width="<?=(@round(($Nsumtot / ($Ysumtot+$Rsumtot+$Nsumtot))*$MAX_barsize)>0?"@round(($Nsumtot / ($Ysumtot+$Rsumtot+$Nsumtot))*$MAX_barsize)":"1")?>%">
		<tr>
			<td width="516" height="15" background="images/line_bg3.gif"></td>
		</tr>
		</table></div>
		</td>
		<td width="45"><p align="right">(<?=number_format($Ncnttot)?>건)</p></td>
	</tr>
	</table>
	</td>
	<td></td>
</tr>
<TR>
	<TD background="images/table_con_line1.gif" width="80"><img src="images/table_con_line1.gif" width="4" height="1" border="0"></TD>
	<TD background="images/table_con_line1.gif" width="680" colspan="2"></TD>
	<td></td>
</TR>
</table>