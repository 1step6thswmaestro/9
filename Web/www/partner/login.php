<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata2.php");
?>
<html>
<head>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<link rel="stylesheet" href="style.css">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script language="JavaScript">
function CheckLogin() {
	if (!document.loginform.id.value) {
		alert("아이디를 입력하세요.");
		document.loginform.id.focus();
		return false;
	}
	if (!document.loginform.passwd.value) {
		alert("패스워드를 입력하세요.");
		document.loginform.passwd.focus();
		return false;
	}
	<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["PLOGN"]=="Y") {?>
	if(typeof document.loginform.ssllogin!="undefined") {
		if(document.loginform.ssllogin.checked==true) {
			document.loginform.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>partnerlogin.php';
		}
	}
	<?}?>
}

function sslinfo() {
	window.open("<?=$Dir.FrontDir?>sslinfo.php","sslinfo","width=100,height=100,scrollbars=no");
}

</script>
</head>
<body onload="document.loginform.id.focus()">
<center>
<BR><BR><BR><BR>
<table border=0 cellpadding=0 cellspacing=0 width=550>
<tr>
	<td>
	<table border=0 cellpadding=0 cellspacing=3 width=200 bgcolor=#eeeeee>
	<tr>
		<td align=center height=25>
		<B>파트너 회원 실적조회</B>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td>
	<table border=0 cellpadding=0 cellspacing=3 bgcolor=#eeeeee width=550>
	<form method=post name=loginform action="<?=$Dir.PartnerDir?>order_search.php" onsubmit="return CheckLogin();">
	<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["PLOGN"]=="Y") {?>
	<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
	<?}?>
	<tr>
		<td style="padding:5" bgcolor=#FFFFFF>
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<tr>
			<td>
			<table border=0 cellpadding=0 cellspacing=0 width=100%>
			<tr>
				<td width=100% style="padding-top:10">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td align=right width=150 style="padding-right:20" nowrap>아이디</td>
					<td style="padding:2;padding-left:10"><input type="text" name="id" value="" size="20" maxlength="20" style="background-color:ffffff;height:25px;font-size:17px;"></td>
				</tr>
				<tr>
					<td align=right width=150 style="padding-right:20" nowrap>비밀번호</td>
					<td style="padding:2;padding-left:10"><input type="password" name="passwd" value="" size="20" maxlength="20" style="background-color:ffffff;height:25px;font-size:17px;"></td>
				</tr>
				<tr>
					<td align=right width=150 style="padding-right:20" nowrap></td>
					<td style="padding:2;padding-left:7">
					<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["PLOGN"]=="Y") {?>
					<input type=checkbox name=ssllogin value="Y" style="border:none"> <A HREF="javascript:sslinfo()">보안 접속</A>
					<?}?>
					</td>
				</tr>
				</table>
				</td>
				<td width=20 nowrap></td>
				<td width=100 valign=top nowrap style="padding-top:14">
				<input type=submit value="로그인" style="cursor:hand;width:56;height:56">
				</td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td height=5 colspan=2></td>
		</tr>
		<tr>
			<td height=1 bgcolor=#FFFFFF></td>
		</tr>
		<tr>
			<td height=20 bgcolor=#F4F4F4></td>
		</tr>
		<tr height=20 bgcolor=#F4F4F4>
			<td style="padding-left:20"><nobr>
			※ <font color=#0000a0><b><? echo getenv("HTTP_HOST") ?></b></font>에서 부여받은 <font color=red>파트너 회원</font>의 ID와 Password를 입력하세요.
			</td>
		</tr>
		<tr height=20 bgcolor=#F4F4F4>
			<td style="padding-left:20"><nobr>
			※ <font color=red><u>비밀번호 관리에 주의하세요.</u></font>
			</td>
		</tr>
		<tr>
			<td height=10 bgcolor=#F4F4F4>&nbsp;</td>
		</tr>
		</table>
		</td>
	</tr>
	</form>
	</table>
	</td>
</tr>
</table>
</center>
</body>
</html>