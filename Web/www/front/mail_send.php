<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	echo "<html></head><body onload=\"alert('로그인이 필요한 서비스입니다.');window.close();\"></body></html>";exit;
}

$pcode = $_REQUEST["pcode"];
$mode = $_POST["mode"];
if($mode =="send"){

	//sns 설정
	$arSnsType = explode("", $_data->sns_reserve_type);

	$sql = "SELECT productname FROM tblproduct WHERE productcode='".$pcode."'";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$productname = strip_tags($row->productname);
	}
	mysql_free_result($result);
	if($arSnsType[0] != "N"){

		$sql = "SELECT code FROM tblsnsproduct ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' AND pcode='".$pcode."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$sns_url = "http://".$_ShopInfo->getShopurl()."?pk=".$row->code;
		}else{
			$cnt = 1;
			while($cnt > 0){
				$tmpid = rand(10000,999999);
				$sql = "SELECT count(1) cnt FROM tblsnsproduct WHERE code='".$tmpid."'";
				$result = mysql_query($sql,get_db_conn());
				if($row = mysql_fetch_object($result)) {
					$cnt = (int)$row->cnt;
				}
				mysql_free_result($result);
			}
			$sql = "INSERT tblsnsproduct SET ";
			$sql.= "code	= '".$tmpid."', ";
			$sql.= "id		= '".$_ShopInfo->getMemid()."', ";
			$sql.= "pcode	= '".$pcode."' ";
			$result=mysql_query($sql,get_db_conn());
			if($result) {
				$sns_url = "http://".$_ShopInfo->getShopurl()."?pk=".$tmpid;
			}
		}
	}else{
		$sns_url = "http://".$_ShopInfo->getShopurl()."?prdt=".$pcode;
	}

	$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
	$result = mysql_query($sql);
	if($row = mysql_fetch_object($result)) {
		$sendUrl_id = $row->url_id;
		$sendId = $row->id;
		$sendName = $row->name;
		$sendEmail = $row->email;
	}

	$arEmails=explode(",", $_POST["in_email"]);
	$message=$_POST["in_message"];

	$mess2=$row->email."로 메일을 ";
	for($i=0;$i<sizeof($arEmails);$i++) {
		SendProductMail($_data->shopname, $_data->shopurl, $_data->design_mail, $message, $sendEmail, $arEmails[$i], $sendName, $sns_url, $sendId, $pcode);
	}
	echo "<html><head><title></title></head><body onload=\"alert('메일이 전송되었습니다.');window.close();\"></body></html>";exit;

}else{
?>

<HTML>
<HEAD>
<TITLE>메일로 소문내기</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<script type="text/javascript">
function frmCheck(){
	if(document.form1.in_email.value.length==0) {
		alert("이메일 주소를 입력하세요.");
		document.form1.in_email.focus();
		return;
	}
	if(document.form1.in_message.value.length==0) {
		alert("내용을 입력하세요.");
		document.form1.in_message.focus();
		return;
	}
	document.form1.mode.value="send";
	document.form1.submit();
}
</script>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
<form name="form1" method="post" action="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="pcode" value="<?=$pcode?>">
<table cellpadding="0" cellspacing="0" width="420">
	<tr>
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="17" align="left"><IMG SRC="../images/design/pop_view_head.gif" WIDTH=17 HEIGHT=44 ALT=""></td>
					<td background="../images/design/pop_view_headbg.gif"><IMG SRC="../images/design/popemail_title.gif" WIDTH=112 HEIGHT=43 ALT=""></td>
					<td width="47" align="right"><a href="javascript:window.close();"><IMG SRC="../images/design/pop_view_exit.gif" WIDTH=47 HEIGHT=44 ALT=""></a></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td background="../images/design/pop_view_leftbg.gif" width="17" height="100%" align="center"></td>
		<td width="100%"  style="padding-top:13px">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td colspan="2"><IMG SRC="../images/design/popemail_text.gif" WIDTH=226 HEIGHT=41 ALT=""></td>
                </tr>
                <tr>
                    <td colspan="2" height="15"></td>
                </tr>
                <tr>
                    <td width="60" height="38"><IMG SRC="../images/design/popemail_text_email.gif" WIDTH=60 HEIGHT=19 ALT=""></td>
                    <td width="90%"><TEXTAREA rows="2" cols="40" name="in_email" class="textarea1"></TEXTAREA></td>
                </tr>
                <tr>
                    <td width="60"><IMG SRC="../images/design/popemail_text_memo.gif" WIDTH=60 HEIGHT=19 ALT=""></td>
                    <td><TEXTAREA rows=8 cols="40" name="in_message" class="textarea1"></TEXTAREA></td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><IMG SRC="../images/design/btn_rumor.gif" ALT="" vspace="5"  onclick="frmCheck()" style="cursor:pointer;"></td>
                </tr>
            </table>

		</td>
		<td background="../images/design/pop_view_rightbg.gif" width="17" height="100%"></td>
	</tr>
	<tr>
		<td height="9" width="10"><IMG SRC="../images/design/pop_view_bottomleft.gif" width="17" height="16" border="0"></td>
		<td background="../images/design/pop_view_bottombg.gif" height="9" width="729"></td>
		<td height="9" width="11"><IMG SRC="../images/design/pop_view_bottomright.gif" width="17" height="16" border="0"></td>
	</tr>
</table>
</form>
<!--메일로로 소문내기!-->
</body>
</html>
<?}?>