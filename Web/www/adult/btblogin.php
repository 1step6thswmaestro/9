<?
if(substr(getenv("SCRIPT_NAME"),-12)=="btblogin.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}

if($ssl_type=="Y" && $num=strpos(" ".$ssl_page,"LOGIN=")) {
	$is_ssl=substr($ssl_page,$num+5,1);
}

?>

<html>
<head>
<title><?=$shoptitle?></title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
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
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	if (!document.form1.id.value) {
		alert("아이디를 입력하세요.");
		document.form1.id.focus();
		return;
	}
	if (!document.form1.passwd.value) {
		alert("패스워드를 입력하세요.");
		document.form1.passwd.focus();
		return;
	}
	document.form1.target="";

<?if($ssl_type=="Y" && strlen($ssl_domain)>0 && strlen($ssl_port)>0 && $is_ssl=="Y") {?>
	if(typeof document.form1.ssllogin!="undefined") {
		if(document.form1.ssllogin.checked==true) {
			document.form1.action='https://<?=$ssl_domain?><?=($ssl_port!="443"?":".$ssl_port:"")?>/<?=RootPath.SecureDir?>login.php';
		}
	}
<?}?>
	document.form1.submit();
}

function CheckKey() {
	key=event.keyCode;
	if (key==13) {
		CheckForm();
	}
}

function member_join() {
	window.open("","btb_join","width=521,height=554,scrollbars=yes");
	document.form2.submit();
}

function sslinfo() {
	window.open("<?=$Dir.FrontDir?>sslinfo.php","sslinfo","width=100,height=100,scrollbars=no");
}

//-->
</SCRIPT>
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 background="<?=$Dir.AdultDir?>images/b2b_bg1.gif">
<table align="center" cellpadding="0" cellspacing="0" width="100%">
<form name=form1 method=post action="<?=$Dir?>index.php">
<input type=hidden name=type value="btblogin">
<?if($ssl_type=="Y" && strlen($ssl_domain)>0 && strlen($ssl_port)>0 && $is_ssl=="Y") {?>
<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<?}?>
<tr>
<?
	if ($member_confirm=="Y") {
		echo "<td align=\"center\"><IMG SRC=\"".$Dir.AdultDir."images/b2b_title1.gif\" border=\"0\"></td>";
	} else { 
		echo "<td align=\"center\"><IMG SRC=\"".$Dir.AdultDir."images/b2b_title1r.gif\" border=\"0\"></td>";
	}
?>
</tr>
<tr>
	<td height="183" background="<?=$Dir.AdultDir?>images/b2b_bg2.gif" valign="top" align="center">
	<table cellpadding="0" cellspacing="0">
	<tr>
		<td height="43" colspan="5"></td>
	</tr>
	<tr>
		<td width="50%" rowspan="3"></td>
		<td><IMG SRC="<?=$Dir.AdultDir?>images/b2b_id.gif" border="0" hspace="5"></td>
		<td style="padding-left:3"><input type=text name=id tabindex="1" style="width:100px;" class="input"></td>
		<td rowspan="3" valign=top><A HREF="javascript:CheckForm()"><img SRC="<?=$Dir.AdultDir?>images/b2b_login.gif" tabindex="3" border=0 hspace="10"></A></td>
		<td width="50%" rowspan="3"></td>
	</tr>
	<tr>
		<td><IMG SRC="<?=$Dir.AdultDir?>images/b2b_pw.gif" border="0" hspace="5"></td>
		<td style="padding-left:3"><input type=password name="passwd" tabindex="2" style="width:100px;" class="input"></td>
	</tr>
	<tr>
		<td></td>
		<td>
		<?if($ssl_type=="Y" && strlen($ssl_domain)>0 && strlen($ssl_port)>0 && $is_ssl=="Y") {?>
		<input type=checkbox name=ssllogin value="Y"> <A HREF="javascript:sslinfo()"><FONT COLOR="#FFFFFF">보안 접속</FONT></A>
		<?}?>
		</td>
	</tr>
	<tr>
		<td colspan="5" height="20"></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td colspan="2"><IMG SRC="<?=$Dir.AdultDir?>images/b2b_btn.gif" border="0" style="CURSOR:hand;" onClick="member_join();"></td>
		<td></td>
	</tr>
	</table>
	</td>
</tr>
</form>
<tr>
	<td height="20"></td>
</tr>
<tr>
	<td align="center">
	<table cellpadding="0" cellspacing="0">
	<tr>
		<td><font style="font-size:8pt;letter-spacing:-0.5pt;">비밀번호를 잊으셨거나, 로그인에 문제가 있는 분들은 관리자(<? echo $info_email ?>)에게 연락하여 주십시요.</font><br><font style="font-size:8pt;"><? echo $shopname ?>(<? echo $info_tel ?>), <a href="mailto:<? echo $info_email ?>" style="background-color:#fe9602;color:#ffffff"><? echo $info_email ?></a></font></td>
	</tr>
	</table>
	</td>
</tr>
<form name=form2 method=post action="<?=$Dir.AdultDir?>btbagree.php" target="btb_join">
</form>
</table>
</body>
</html>