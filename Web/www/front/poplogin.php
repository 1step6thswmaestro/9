<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata2.php");

if(strlen($_ShopInfo->getMemid())>0) {
	$chUrl=trim(urldecode($_REQUEST["chUrl"]));
	if (strlen($chUrl)>0) $onload=$chUrl;
	else $onload=$Dir.MainDir."main.php";
	Header("Location:".$onload);
	exit;
}

unset($row);
$sql = "SELECT * FROM tbldesign ";
$result=mysql_query($sql,get_db_conn());
if($crow=mysql_fetch_object($result)) {
} else {
	$crow->introtype="C";
}
mysql_free_result($result);
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> 로그인</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript">
<!--
function login() {
	var f = document.loginf;
	if(!f.id.value) {
		alert("아이디를 입력하세요.");
		f.id.focus();
		return false;
	}

	if (!f.passwd.value) {
		alert('비밀번호를 입력하세요.');
		f.passwd.focus();
		return false;
	}
	f.submit();
}
//-->
</script>
<?include($Dir."lib/style.php")?>
<SCRIPT language=javascript>

<!--
	function GoLink(link_url)
	{
		opener.location.href = link_url;
	}
//-->

</SCRIPT>

</HEAD>
<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
<table cellpadding="0" cellspacing="0" width="420" align="center">
	<tr>
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="17" align="left"><IMG SRC="../images/design/pop_view_head.gif" WIDTH=17 HEIGHT=44 ALT=""></td>
					<td background="../images/design/pop_view_headbg.gif"><IMG SRC="../images/design/poplog_intitle.gif" ALT=""></td>
					<td width="47" align="right"><IMG SRC="../images/design/pop_view_exit.gif" WIDTH=47 HEIGHT=44 ALT="" onClick="self.close()" style="cursor:hand"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td background="../images/design/pop_view_leftbg.gif" width="17" height="100%" align="center"></td>
		<td width="100%"  style="padding-top:13px">
			<form method="post" action="/front/poploginproc.php" name="loginf">
			<input type="hidden" name="nexturl" value="<?=$_REQUEST['chUrl']?>">




			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<table align="center" cellpadding="0" cellspacing="0" width="340" style="margin-top:15px;">
						<tr>
							<td>
								<TABLE cellSpacing=0 cellPadding="2" align=center>
								<tr>
									<TD width="60px"><IMG SRC="../images/design/poplogin_text01.gif" ALT=""></TD>
									<TD><INPUT class="login_input" name="id" type="text" style="width:160px;height:20px;border-width:1px; border-color:rgb(194,197,204); border-style:solid; "></TD>
								</tr>
								<tr>
									<TD><IMG SRC="../images/design/poplogin_text02.gif"  ALT=""></TD>
									<TD><INPUT class="login_input" name="passwd" type="password"  style="width:160px;height:20px;border-width:1px; border-color:rgb(194,197,204); border-style:solid; "></TD>
								</tr>
								</table>
							</td>
							<td><input type="image" SRC="../images/design/poplogin_btn01.gif" ALT="" border=0  onclick="login();"></td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="10"></td>
				</tr>
				<tr>
					<td><hr size="1" color="#EAEAEA"></td>
				</tr>
				<tr>
					<td height="10"></td>
				</tr>
				<tr>
					<td align="center" valign="bottom"><IMG border=0 src="../images/001/main_skin1_top_loginicon1.gif"><A href="javascript:GoLink('../front/member_agree.php');"><FONT style="LETTER-SPACING: -0.5pt; FONT-SIZE: 8pt">신규회원가입하기</FONT></A><FONT style="LETTER-SPACING: -0.5pt; FONT-SIZE: 8pt"> &nbsp;</FONT><IMG border=0 src="../images/001/main_skin1_top_loginicon2.gif"><A href="javascript:GoLink('../front/findpwd.php');"><FONT style="LETTER-SPACING: -0.5pt; FONT-SIZE: 8pt">ID/PW를 분실했어요!</FONT></A></td>
				</tr>
			</table>





			</form>
		</td>
		<td background="../images/design/pop_view_rightbg.gif" width="17" height="100%"></td>
	</tr>
	<tr>
		<td height="9" width="10"><img src="../images/design/pop_view_bottomleft.gif" width="17" height="16" border="0"></td>
		<td background="../images/design/pop_view_bottombg.gif" height="9" width="729"></td>
		<td height="9" width="11"><img src="../images/design/pop_view_bottomright.gif" width="17" height="16" border="0"></td>
	</tr>
</table>
</BODY>
</HTML>