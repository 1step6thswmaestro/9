<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$sql = "SELECT shopname,info_tel,privercyname,privercyemail FROM tblshopinfo ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$shopname=$row->shopname;
	$privercytel=$row->info_tel;
	$privercyname=$row->privercyname;
	$privercyemail=$row->privercyemail;
	mysql_free_result($result);
} else {
	exit;
}

$sql = "SELECT privercy FROM tbldesign ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$privercy_exp = @explode("=", $row->privercy);
	$privercybody=$privercy_exp[0];
}
mysql_free_result($result);

if(strlen($privercybody)==0) {
	$fp=fopen($Dir.AdminDir."privercy.txt", "r");
	$privercybody=fread($fp,filesize($Dir.AdminDir."privercy.txt"));
	fclose($fp);
}

$pattern=array("(\[SHOP\])","(\[NAME\])","(\[EMAIL\])","(\[TEL\])");
$replace=array($shopname,$privercyname,"<a href=\"mailto:".$privercyemail."\">".$privercyemail."</a>",$privercytel);
$privercybody = preg_replace($pattern,$replace,$privercybody);
?>

<html>
<head>
<title>∞≥¿Œ¡§∫∏ ∫∏»£¡§√•</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td	{font-family:"±º∏≤,µ∏øÚ";color:#4B4B4B;font-size:12px;line-height:17px;}
BODY,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:#635C5A;text-decoration:none;}
A:visited {color:#545454;text-decoration:none;}
A:active  {color:#5A595A;text-decoration:none;}
A:hover  {color:#545454;text-decoration:underline;}
.input{font-size:12px;BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT: 18px; BACKGROUND-COLOR: #ffffff;padding-top:2pt; padding-bottom:1pt; height:19px}
.select{color:#444444;font-size:12px;}
.textarea {border:solid 1;border-color:#e3e3e3;font-family:µ∏¿Ω;font-size:9pt;color:333333;overflow:auto; background-color:transparent}
</style>
</head>

<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" onload="window.resizeTo(612,590);">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td><img src="<?=$Dir?>images/common/privercy_title.gif" border="0"></td>
</tr>
<tr>
	<td style="padding:10;padding-top:0px">
	<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td style="padding:10;border:1 solid #cacaca;"><pre><?=$privercybody?></pre></td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td align="center"><A HREF="javascript:window.close();"><img src="<?=$Dir?>images/common/bigview_btnclose.gif" border="0"></A></td>
	</tr>
	</table>
	</td>
</tr>
</table>
</body>
</html>