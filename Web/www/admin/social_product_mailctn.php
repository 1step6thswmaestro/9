<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "go-4";
$MenuCode = "gong";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$date = $_POST["date"];
$sql = "SELECT * FROM tblgonggumail ";
$sql .="WHERE date = '".$date."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)){
	$mailmsg = $row->body;
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>공동구매 메일</title>
<link rel="stylesheet" href="style.css" type="text/css">
<style>
.gongguTbl td {font-family:"verdana", "돋움";font-weight:bold;}
</style>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 >
<TABLE WIDTH="600" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<tr>
	<td height="35" background="images/blueline_bg.gif"><b><span class="font_blue" style="padding-left:10px;float:left">공동구매 구독메일 내용</span></b><span style="float:right"><a href="javascript:window.close();"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="0" border=0 hspace="2"></a></span></td>
</tr>
<TR>
	<TD style="padding:10px 5px;border:1px solid #ddd ;">
	<?=$mailmsg?>
	</TD>
</TR>
</TABLE>
</body>
</html>