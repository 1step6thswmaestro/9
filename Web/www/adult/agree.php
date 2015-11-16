<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$sql="SELECT agreement FROM tbldesign ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$agreement=$row->agreement;
mysql_free_result($result);

if(strlen($agreement)==0) {
	$fp=fopen($Dir.AdminDir."/agreement.txt","r");
	if($fp) {
		while (!feof($fp)) {
			$buffer.= fgets($fp, 1024);
		}
	}
	fclose($fp);
	$agreement=$buffer;
}

$sql="SELECT * FROM tblshopinfo ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
mysql_free_result($result);

$pattern=array("(\[SHOP\])","(\[COMPANY\])");
$replace=array($row->shopname, $row->companyname);

$agreement = preg_replace($pattern,$replace,$agreement);
?>

<html>
<head>
<title>회원가입</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<style>
td	{font-family:"굴림,돋움";color:#4B4B4B;font-size:12px;line-height:17px;}
BODY,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:#635C5A;text-decoration:none;}
A:visited {color:#545454;text-decoration:none;}
A:active  {color:#5A595A;text-decoration:none;}
A:hover  {color:#545454;text-decoration:underline;}
.input{font-size:12px;BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT: 18px; BACKGROUND-COLOR: #ffffff;padding-top:2pt; padding-bottom:1pt; height:19px}
.select{color:#444444;font-size:12px;}
.textarea {border:solid 1;border-color:#e3e3e3;font-family:돋음;font-size:9pt;color:333333;overflow:auto; background-color:transparent}
</style>
</head>
<BODY LEFTMARGIN="0" TOPMARGIN="0" rightmargin="0" MARGINWIDTH="0" MARGINHEIGHT="0" background="<?=$Dir.AdultDir?>images/adultintro_join_bg.gif" style="overflow-x:hidden">
<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
<TR>
	<TD><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_join_title.gif" border="0"></TD>
</TR>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td background="<?=$Dir.AdultDir?>images/adultintro_join_left01.gif"><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_join_left01.gif" border="0"></td>
		<td width="100%" bgcolor="FFFFFF" style="PADDING:5px"><DIV style="OVERFLOW-Y:auto;OVERFLOW-X:auto;WIDTH:100%;HEIGHT:340px"><?=$agreement?></DIV></td>
		<td background="<?=$Dir.AdultDir?>images/adultintro_join_left02.gif"><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_join_left02.gif" border="0"></td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_join_left03.gif" border="0"></TD>
</TR>
<TR>
	<TD height="10"></TD>
</TR>
<tr>
	<TD background="<?=$Dir.AdultDir?>images/adultintro_join_bg.gif" align="center"><A HREF="javascript:document.form1.submit();"><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_join_agree.gif" border="0"></a><A HREF="javascript:window.close();"><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_join_no.gif" border="0" hspace="5"></a></TD>
</tr>
<form name="form1" action="<?=$Dir.AdultDir?>adult_join.php" method="post">
</form>
</TABLE>
</body>
</html>