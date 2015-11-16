<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/admin_more.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$productcode=$_REQUEST["productcode"];
if(strlen($productcode)==0) {
	echo "<html><head></head><body onload=\"alert('해당 상품이 존재하지 않습니다.');window.close();\"></body></html>";exit;
}
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>상품 수수료 변경 내역</title>
<link rel="stylesheet" href="/admin/style.css" type="text/css">
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<table border=0 cellpadding=0 cellspacing=0 width=500 style="table-layout:fixed;" id=table_body>
<tr>
	<td width=100% align=center>
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
	<tr><td height=10></td></tr>
	<tr>
		<td align=center>
		<?
			getProductCommissionHistory($productcode, "0");
		?>
		</td>
	</tr>
	<tr><td height=10></td></tr>
	<tr>
		<td align=center><input type="image" src="images/btn_close.gif" width="36" height="18" border="0" vspace="0" hspace="2" onclick="window.close();">
		</td>
	</tr>
	<tr><td height=10></td></tr>
	</table>

	</td>
</tr>
</table>

</body>
</html>