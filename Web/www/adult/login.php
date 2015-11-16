<?
if(substr(getenv("SCRIPT_NAME"),-9)=="login.php"){
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
	window.open("","adult_join","width=521,height=550,scrollbars=yes");
	document.form2.submit();
}

function sslinfo() {
	window.open("<?=$Dir.FrontDir?>sslinfo.php","sslinfo","width=100,height=100,scrollbars=no");
}

//-->
</SCRIPT>
</head>
<body bgcolor="white" text="black" link="blue" vlink="purple" alink="red" leftmargin="0" marginwidth="0" rightmargin="0" topmargin="0" marginheight="0" onload="document.form1.id.focus()">
<table cellpadding="0" cellspacing="0" width="100%">
<form name=form1 method=post action="<?=$Dir?>index.php">
<input type=hidden name=type value="adultlogin">
<?if($ssl_type=="Y" && strlen($ssl_domain)>0 && strlen($ssl_port)>0 && $is_ssl=="Y") {?>
<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<?}?>
<tr>
	<td background="<?=$Dir.AdultDir?>images/adultintro_skin_top.gif" HEIGHT="76"><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_skin_top.gif" WIDTH="1"></td>
</tr>
<tr>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%">
	<col width="25%"></col>
	<col width="50%"></col>
	<col width="25%"></col>
	<tr>
		<td background="<?=$Dir.AdultDir?>images/adultintro_skin_linebg.gif"></td>
		<td background="<?=$Dir.AdultDir?>images/adultintro_skin_linebg.gif">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_skin_img1.gif" border="0"></td>
			<td><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_skin_text1.gif" border="0"></td>
		</tr>
		<tr>
			<td><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_skin_img2.gif" border="0"></td>
			<td background="<?=$Dir.AdultDir?>images/adultintro_skin_linebg1.gif">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="padding-left:10pt;">
				<table cellpadding="0" cellspacing="0" width="100%" align="center">
				<tr>
					<td><? echo $shopname ?>을 방문해 주셔서 감사합니다.<br>
					저희 사이트는 만 19세 이상의 성인만이 회원가입 후 이용하실 수 있습니다.<BR>
					<FONT color="#000000"><B>회원가입을 하신 분은 로그인을 하신 후 이용하여 주십시요.<br>
					<FONT style="COLOR:#ffffff;BACKGROUND-COLOR:#FF3300">회원가입은 무료</FONT> 입니다.</B></FONT></td>
				</tr>
				<tr>
					<td height="20"></td>
				</tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" width="100%" align="center">
					<tr>
						<td><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_skin_id.gif" border="0"></td>
						<td style="padding-left:3"><input type=text name="id" size="15" tabindex="1" style="width:130px;font-size:11px;BORDER-RIGHT:#EDEDED 1px solid;BORDER-TOP:#C2C2C2 1px solid;BORDER-LEFT:#C2C2C2 1px solid;BORDER-BOTTOM: #EDEDED 1px solid;BACKGROUND-COLOR:#ffffff;padding-top:2pt;padding-bottom:1pt;height:18px"></td>
						<td rowspan="2">
						<table cellpadding="0" cellspacing="0">
						<tr>
							<td style="padding-left:3pt;"><A HREF="javascript:CheckForm()"><img SRC="<?=$Dir.AdultDir?>images/adultintro_skin_btnlogin.gif" border="0" tabindex="3"></A></td>
							<td><img src="<?=$Dir.AdultDir?>images/adultintro_skin_btnjoin.gif" style="CURSOR:hand;" onClick="member_join();" hspace="2"></td>
							<td><a href="javascript:history.go(-1);"><img src="<?=$Dir.AdultDir?>images/adultintro_skin_btn2.gif" border="0"></a></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td><IMG SRC="<?=$Dir.AdultDir?>images/adultintro_skin_pw.gif" border="0"></td>
						<td style="padding-left:3"><input type=password name="passwd" size="15" tabindex="2" style="width:130px;font-size:11px;BORDER-RIGHT:#EDEDED 1px solid;BORDER-TOP:#C2C2C2 1px solid;BORDER-LEFT:#C2C2C2 1px solid;BORDER-BOTTOM:#EDEDED 1px solid;BACKGROUND-COLOR:#ffffff;padding-top:2pt;padding-bottom:1pt;height:18px"></td>
					</tr>
					<tr>
						<td></td>
						<td>
						<?if($ssl_type=="Y" && strlen($ssl_domain)>0 && strlen($ssl_port)>0 && $is_ssl=="Y") {?>
						<input type=checkbox name=ssllogin value="Y"> <A HREF="javascript:sslinfo()">보안 접속</A>
						<?}?>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
		<td background="<?=$Dir.AdultDir?>images/adultintro_skin_linebg.gif"></td>
	</tr>
	<tr>
		<td colspan="3" height="20"></td>
	</tr>
	<tr>
		<td colspan="3" style="padding-left:300px">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><font color="#000000" style="font-size:20px;"><b><?=$url?></b></font></td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td><font color="#000000" style="font-size:11px;">비밀번호를 잊으셨거나, 로그인에 문제가 있는 분들은 관리자(<?=$info_email?>)에게 연락하여 주십시요.<br>
				<? if ($member_confirm=="Y") { ?>
				성인몰이므로 회원가입 후 관리자의 인증을 거친 후에만 구매가 가능합니다.
				<? } else { ?>
				성인몰이므로 회원가입 후 이용이 가능합니다.
				<? } ?><br>
				<?=$shopname?>(<?=$info_tel?>),&nbsp;<a href="mailto:<?=$info_email?>"><font color="#ffffff" style="background-color:#fe9602;"><?=$info_email?></font></a><br>
				사업자번호:<?=$companynum?>&nbsp;&nbsp;대표자:<?=$companyowner?>
				<? if (strlen($companyaddr)>0) { ?>
				&nbsp;&nbsp;&nbsp;주소:<?=$companyaddr?>
				<? } ?></font></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</form>
<form name=form2 method=post action="<?=$Dir.AdultDir?>agree.php" target="adult_join">
</form>
</table>
</body>
</html>