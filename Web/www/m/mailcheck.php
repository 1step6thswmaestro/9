<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$email=$_REQUEST["email"];

if(strlen($email)<=0) {
    $message="<font color=#FF3300><b>�̸����� �Է��� �ȵǾ����ϴ�.</b></font>";
} else if(strtolower($email)=="admin") {
    $message="<font color=#FF3300><b>��� �Ұ����� �̸��� �Դϴ�.</b></font>";
} else {
	$sql = "SELECT email FROM tblmember WHERE email='".$email."' ";
	$result = mysql_query($sql,get_db_conn());

	if ($row=mysql_fetch_object($result)) {
		$message="<font color=#ff0000><b>�̸����� �ߺ��Ǿ����ϴ�.</b></font>";
	} else {
		$sql = "SELECT id FROM tblmemberout WHERE id='".$id."' ";
		$result2 = mysql_query($sql,get_db_conn());
		if($row2=mysql_fetch_object($result2)) {
			$message="<font color=#ff0000><b>�̸����� �ߺ��Ǿ����ϴ�.</b></font>";
		} else {
			$message="<font color=#0000ff><b>��밡���� �̸��� �Դϴ�.</b><br><a class=\"button black small\" href=\"javascript:useEmail();\">����ϱ�</a></font>";
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
<title>���̵� �ߺ� Ȯ��</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<link rel="stylesheet" href="./css/common.css" />
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
	<TD style="width:100%;height:40px;background-color:#888;text-align:center;font-weight:bold;font-size:1.2em;color:#FFF">�̸��� �ߺ�Ȯ��</TD>
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
		<td align="center"><a href="javascript:window.close()"><img src="<?=$Dir?>images/btn_ok4.gif" border="0"></a></td>
	</tr>
	</table>
	</TD>
</TR>
</TABLE>
<?}?>
</center>
</body>
</html>