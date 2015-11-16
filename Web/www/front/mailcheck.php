<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$email=$_REQUEST["email"];

if(strlen($email)<=0) {
    $message="<font color=#FF3300><b>이메일이 입력이 안되었습니다.</b></font>";
} else if(strtolower($email)=="admin") {
    $message="<font color=#FF3300><b>사용 불가능한 이메일 입니다.</b></font>";
} else {
	$sql = "SELECT email FROM tblmember WHERE email='".$email."' ";
	$result = mysql_query($sql,get_db_conn());

	if ($row=mysql_fetch_object($result)) {
		$message="<font color=#ff0000><b>이메일이 중복되었습니다.</b></font>";
	} else {
		$sql = "SELECT id FROM tblmemberout WHERE id='".$id."' ";
		$result2 = mysql_query($sql,get_db_conn());
		if($row2=mysql_fetch_object($result2)) {
			$message="<font color=#ff0000><b>이메일이 중복되었습니다.</b></font>";
		} else {
			$message="<font color=#0000ff><b>사용가능한 이메일 입니다.</b><br><a href=\"javascript:useEmail();\"><img src=\"".$Dir."images/btn_use.gif\" border=\"0\" alt=\"사용하기\"></a></font>";
		}
		mysql_free_result($result2);
	}
	mysql_free_result($result);
}


unset($body);
$sql="SELECT body FROM ".$designnewpageTables." WHERE type='iddup'";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$body=$row->body;
	$body=str_replace("[DIR]",$Dir,$body);
}
mysql_free_result($result);
?>

<html>
<head>
<title>아이디 중복 확인</title>
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
<script type="text/javascript">
<!--
	function useEmail () {
		opener.form1.mailChk.value="1";
		window.close();
	}
//-->
</script>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" onload="window.resizeTo(276,190);">
<?
if(strlen($body)>0) {
	$pattern=array("(\[MESSAGE\])","(\[OK\])");
	$replace=array($message,"JavaScript:window.close()");
	$body = preg_replace($pattern,$replace,$body);
	if (strpos(strtolower($body),"table")!=false) $body = "<pre>".$body."</pre>";
	else $body = ereg_replace("\n","<br>",$body);

	echo $body;
} else {
?>
<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
<TR>
	<TD><img src="<?=$Dir?>images/design_mailcheck_title.gif" border="0" alt="이메일 중복 체크"></TD>
</TR>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="50" align="center"><?=$message?></td>
	</tr>
	<tr>
		<td><hr size="1" noshade color="#F3F3F3"></td>
	</tr>
	<tr>
		<td align="center"><a href="javascript:window.close()"><img src="<?=$Dir?>images/btn_close.gif" border="0" alt="닫기"></a></td>
	</tr>
	</table>
	</TD>
</TR>
</TABLE>
<?}?>
</center>
</body>
</html>