<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$id=$_POST["id"];
if(strlen($id)==0) {
	echo "<script>window.close();</script>";
	exit;
}

$sql = "SELECT name FROM tblmember WHERE id = '".$id."' ";
$result = mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$name = $row->name;
}
mysql_free_result($result);

?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>회원 구매내역</title>
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 75;

	window.resizeTo(oWidth,oHeight);
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600");
	document.detailform.submit();
}

//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
<TABLE WIDTH="400" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD  height="31" background="images/member_mailallsend_imgbg.gif">
	<table cellpadding="0" cellspacing="0" width="400">
	<tr>
		<td  height="31" background="images/member_mailallsend_imgbg.gif" style="padding-left:20px"></td>
		<td width="372"><b><font color="white"><?=$name?> (<FONT COLOR="#FE8E4B"><?=$id?></FONT>)회원님의 구매내역</b></font></td>
	</tr>
	</table>
	</TD>
</TR>
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<TR>
	<TD style="padding-top:3pt; padding-bottom:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td width="390">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TR>
			<TD background="images/table_top_line.gif" colspan="3" height=1></TD>
		</TR>
		<TR align=center>
			<TD class="table_cell" width="21">NO</TD>
			<TD class="table_cell1" width="160">주문일</TD>
			<TD class="table_cell1" width="161">주문금액</TD>
		</TR>
		<TR>
			<TD colspan="3" background="images/table_con_line.gif"></TD>
		</TR>
<?
		$sql = "SELECT price,ordercode FROM tblorderinfo ";
		$sql.= "WHERE id = '".$id."'  AND `del_gbn` = 'N' AND `deli_gbn` != 'C' ORDER BY ordercode DESC ";
		$result = mysql_query($sql,get_db_conn());
		$count=1;$sumprice=0;
		while ($row=mysql_fetch_object($result)) {
			echo "<tr>\n";
			$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2);
			echo "<TD align=center class=\"td_con2\" width=\"35\">".$count++."</td>\n";
			echo "<TD align=center class=\"td_con1\" width=\"168\">".$date."</td>\n";
			echo "<TD align=right class=\"td_con1\" width=\"169\"><A HREF=\"javascript:OrderDetailView('".$row->ordercode."')\"><b><span class=\"font_orange\">".number_format($row->price)."원</span></b></a></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<TD colspan=\"3\" background=\"images/table_con_line.gif\"></TD>\n";
			echo "</tr>\n";
			$sumprice+=$row->price;
		}
		mysql_free_result($result);
		if ($count<=1) {
			echo "<tr><td class=\"td_con2\" colspan=3 align=center>주문내역이 없습니다.</td></tr>";
		} else {
?>
		<tr>
			<TD align=center class="td_con2" width="35" bgcolor="#E1F1FF" style="border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(0,153,204); border-bottom-color:rgb(0,153,204); border-top-style:solid; border-bottom-style:solid;">합계</td>
			<TD align=center class="td_con1" width="168" bgcolor="#E1F1FF" style="border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(0,153,204); border-bottom-color:rgb(0,153,204); border-top-style:solid; border-bottom-style:solid;"><?=number_format($count-1)?>건</td>
			<TD align=center class="td_con1" width="169" bgcolor="#E1F1FF" style="border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(0,153,204); border-bottom-color:rgb(0,153,204); border-top-style:solid; border-bottom-style:solid;"><b><span class="font_blue"><?=number_format($sumprice)?>원</span></b></TD>
		</tr>
<?
		}
?>
		<TR>
			<TD background="images/table_top_line.gif" colspan="3" height=1></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD align=center><a href="javascript:window.close()"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="10" border=0></a></TD>
</TR>
</form>

<form name=detailform method="post" action="order_detail.php" target="orderdetail">
			<input type=hidden name=ordercode>
			</form>

</TABLE>
</body>
</html>