<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$type=$_POST["type"];
$filename=$_POST["val"];
$up_body=$_POST["up_body"];

if(strlen($_ShopInfo->getId())==0 || strlen($filename)==0){
	echo "<script>window.close();</script>";
	exit;
}

$ext=substr(strrchr($filename,"."),1);
if(!preg_match("/^(htm|css)$/",$ext)) {
	echo "<script>window.close();</script>";
	exit;
}

$filepath = $Dir.DataDir."design/".$filename;

if(file_exists($filepath)==false) {
	echo "<html><head><title></title></head><body onload=\"alert('해당 파일이 존재하지 않습니다.');window.close();\"></body></html>";exit;
}

if($type=="update" && strlen($up_body)>0) {
	$up_body=stripslashes($up_body);
	$fp=fopen($filepath,"w");
	fwrite($fp, $up_body);
	fclose($fp);
	$onload="<script>alert(\"HTML편집이 완료되었습니다.\");</script>";
}

$fp = fopen($filepath,"r");
$body="";
while ($str=fgets($fp,1024)) $body.=$str;
fclose($fp);

?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>HTML편집</title>
<style>td {font-size:9pt; font-family: 굴림;}</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 65;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm() {
	if(document.form1.up_body.value.length==0) {
		alert("내용을 입력하세요.");
		document.form1.up_body.focus();
		return;
	}
	document.form1.type.value="update";
	document.form1.submit();
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="750" BORDER=0 CELLPADDING=0 CELLSPACING=0 id=table_body>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<col width=212></col>
	<col width=></col>
	<col width=20></col>
	<tr>
		<td><img src="images/design_html_title.gif" border="0"></td>
		<td background="images/member_mailallsend_imgbg.gif">&nbsp;</td>
		<td align=right><img src="images/member_mailallsend_img2.gif" border="0"></td>
	</tr>
	</table>
	</TD>
</TR>
<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=type>
<input type=hidden name=val value="<?=$filename?>">
<tr>
	<TD style="padding:10pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align=center>
			<font color="#0066FF">파일명 : <?=$filename?></font>
		</td>
	</tr>
	<tr>
		<td>
			<TEXTAREA name=up_body rows=30 wrap=off cols=100 style=width:100%><?=htmlspecialchars($body);?></TEXTAREA>
		</td>
	</tr>
	</table>
	</TD>
</tr>
<TR>
	<TD align=center>
		<a href="javascript:CheckForm()"><img src="images/btn_ok1.gif" border="0" border=0></a>
		&nbsp;
		<a href="javascript:window.close()"><img src="images/btn_close.gif" border=0></a>
	</TD>
</TR>
<tr><td height=10></td></tr>
</form>
</TABLE>

<?=$onload?>

</body>
</html>
