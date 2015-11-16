<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$prcode=$_POST["prcode"];
if(strlen($prcode)==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$sql = "SELECT A.* ";
$sql .=", (SELECT name FROM tblmember B WHERE A.id=B.id) name ";
$sql .="FROM tblgongguencore A ";
$sql .="WHERE productcode = '".$prcode."' ";
$sql .="ORDER BY regidate DESC ";
$result=mysql_query($sql,get_db_conn());
$num_rows = mysql_num_rows($result);
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>공동구매 재신청 목록</title>
<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 >
<TABLE WIDTH="300" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD height="31" background="images/member_mailallsend_imgbg.gif" style="color:#fff;font-weight:bold;padding:5px 10px;"> 공동구매 재신청목록</TD>
</TR>

<TR>
	<TD>
		<div style="text-align:right;font-size:11px;">* 총 <?=$num_rows?> 건 신청</div>
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 >
		<col width=118></col>
		<col width=></col>
		<TR>
			<TD colspan="2" background="images/table_top_line.gif"></TD>
		</TR>
		<TR>
			<TD class="table_cell" align="center">신청인</TD>
			<TD class="table_cell" align="center">신청일시</TD>
		</TR>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
<?
while($row=mysql_fetch_array($result)) {
?>

		<TR>
			<TD class="td_con1" align=center><?=$row["id"]."(".$row["name"].")"?></TD>
			<TD class="td_con1" align=center><?=date("Y-m-d H:i:s",$row["regidate"])?></TD>
		</TR>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
<?}?>
		</TABLE>
	</TD>
</TR>
<TR>
	<TD align=center style="padding:5x" ><a href="javascript:window.close();"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="0" border=0 hspace="2"></a></TD>
</TR>
</form>
</TABLE>
</body>
</html>