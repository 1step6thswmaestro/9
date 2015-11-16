<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	echo "<script>window.close();</script>"; exit;
}

if($_data->personal_ok!="Y") {
	echo "<html></head><body onload=\"alert('본 쇼핑몰에서는 1:1고객문의 게시판 기능을 사용하지 않습니다.\\n\\n쇼핑몰 운영자에게 문의하시기 바랍니다.');window.close();\"></body></html>";exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('잘못된 접근입니다.');window.close()\"></body></html>";exit;
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('잘못된 접근입니다.');window.close()\"></body></html>";exit;
	}
}
mysql_free_result($result);

$smsCount = smsCountValue ();
$boardsmsSQL = "SELECT smsused, leavenumber FROM personalboard_admin WHERE type='SMS' LIMIT 0, 1";
$smsused=$numberlist="";
if(false !== $boardsmsRes = mysql_query($boardsmsSQL,get_db_conn())){
	$boardsmsrowcount = mysql_num_rows($boardsmsRes);

	if($boardsmsrowcount >0){
		$smsused = mysql_result($boardsmsRes,0,0);
		$numberlist = mysql_result($boardsmsRes,0,1);
	}
	mysql_free_result($boardsmsRes);
}

$smsinfoSQL = "SELECT id, authkey, return_tel FROM tblsmsinfo";

if(false !== $smsinfoRes = mysql_query($smsinfoSQL,get_db_conn())){
	$smsinforowcount = mysql_num_rows($smsinfoRes);
	
	if($smsinforowcount>0){
		$smsinfo_id = mysql_result($smsinfoRes,0,0);
		$smsinfo_authkey = mysql_result($smsinfoRes,0,1);
		$smsinfo_returntel = mysql_result($smsinfoRes,0,2);
	}
	mysql_free_result($smsinfoRes);
}




$mode=$_POST["mode"];
$up_subject=$_POST["up_subject"];
$up_email=$_POST["up_email"];
$up_content=$_POST["up_content"];

if($mode=="write") {
	$ip=$_SERVER["REMOTE_ADDR"];
	$date=date("YmdHis");

	$sql = "INSERT tblpersonal SET ";
	$sql.= "id			= '".$_mdata->id."', ";
	$sql.= "name		= '".$_mdata->name."', ";
	$sql.= "email		= '".$up_email."', ";
	$sql.= "ip			= '".$ip."', ";
	$sql.= "subject		= '".$up_subject."', ";
	$sql.= "date		= '".$date."', ";
	$sql.= "content		= '".$up_content."' ";
	if(mysql_query($sql,get_db_conn())) {
		
		if($smsCount>0 && strlen($smsinfo_id)>0 && strlen($smsinfo_authkey)>0 && strlen($smsinfo_returntel)>0 && $smsused = "Y" && strlen($numberlist)>0){
			$senddate ="0"; //실시간 반영
			$sendetcmsg="1:1문의 글등록 알림";
			$smssendmsg = "1:1 게시판에 신규문의가 등록되었습니다.";
			$temp=SendSMS($smsinfo_id,$smsinfo_authkey,$numberlist , "", $smsinfo_returntel, 0, $smssendmsg, $sendetcmsg);
		}
				
		echo "<html><head><title></title></head><body onload=\"alert('정상적으로 등록되었습니다.');try{opener.location.reload();}catch(e){}window.close()\"></body></html>";exit;
	} else {
		$onload="<script>alert('문의글 등록중 오류가 발생하였습니다.');</script>";
	}
}


?>

<html>
<head>
<title>1:1고객문의</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
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
window.moveTo(10,10);

function CheckForm() {
	if(document.form1.up_subject.value.length==0) {
		alert("문의제목을 입력하세요.");
		document.form1.up_subject.focus();
		return;
	}
	if(document.form1.up_email.value.length>0) {
		if(!IsMailCheck(document.form1.up_email.value)) {
			alert("이메일 입력이 잘못되었습니다.");
			document.form1.up_email.focus();
			return;
		}
	}
	if(document.form1.up_content.value.length==0) {
		alert("문의내용을 입력하세요.");
		document.form1.up_content.focus();
		return;
	}
	document.form1.mode.value="write";
	document.form1.submit();
}
//-->
</SCRIPT>
</head>

<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" onload="window.resizeTo(560,430);">
<table cellpadding="0" cellspacing="0" width="550">
<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode>
<tr>
	<td><IMG src="<?=$Dir?>images/common/mypersonal_popup_title.gif" border="0"></td>
</tr>
<tr>
	<td>
	<table align="center" cellpadding="0" cellspacing="0" width="96%" bordercolordark="black" bordercolorlight="black">
	<tr>
		<td>
		<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
		<col width="100" style="PADDING-RIGHT: 4.5pt; PADDING-LEFT: 4.5pt; FONT-WEIGHT: bold; PADDING-BOTTOM: 4.5pt; LINE-HEIGHT: 18px; PADDING-TOP: 4.5pt; LETTER-SPACING: -0.5pt; BACKGROUND-COLOR: #f8f8f8"></col>
		<col style="PADDING-RIGHT: 2pt; PADDING-LEFT: 3pt; PADDING-BOTTOM: 3pt; BORDER-LEFT: #e3e3e3 1pt solid; LINE-HEIGHT: 18px; PADDING-TOP: 3pt" width=></col>
		<TR>
			<TD bgcolor="#B9B9B9" colSpan="2" height="1"></TD>
		</TR>
		<TR>
			<TD><IMG src="<?=$Dir?>images/common/mypersonal_popup_point2.gif" border="0">문의제목</TD>
			<TD style="BORDER-LEFT: #e3e3e3 1pt solid;"><INPUT type=text name="up_subject" size="40" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></TD>
		</TR>
		<TR>
			<TD bgcolor="#EDEDED" colSpan="2" height="1"></TD>
		</TR>
		<TR>
			<TD><IMG src="<?=$Dir?>images/common/mypersonal_popup_point2.gif" border="0">이메일</TD>
			<TD style="BORDER-LEFT: #e3e3e3 1pt solid;"><input type=text name="up_email" value="<?=$_mdata->email?>" size="40" class="input" style="BACKGROUND-COLOR:#F7F7F7;"><BR><FONT color="#FF6600">*입력하신 메일주소로 답변내용이 발송되오니 정확히 입력하세요.</FONT></TD>
		</TR>
		<TR>
			<TD bgcolor="#EDEDED" colSpan="2" height="1"></TD>
		</TR>
		<TR>
			<TD><IMG src="<?=$Dir?>images/common/mypersonal_popup_point2.gif" border="0">문의내용</TD>
			<TD style="BORDER-LEFT: #e3e3e3 1pt solid;"><TEXTAREA style="WIDTH: 100%; HEIGHT: 160px" name="up_content" class="textarea"></TEXTAREA></TD>
		</TR>
		<TR>
			<TD bgcolor="#B9B9B9" colSpan="2" height="1"></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height="10"></td>
</tr>
<tr>
	<td align="center"><a href="javascript:CheckForm();"><img src="<?=$Dir?>images/common/mypage_personalwrite_btn1.gif" border="0"></a><a href="javascript:window.close();"><img src="<?=$Dir?>images/common/mypage_personalwrite_btn2.gif" border="0" hspace="5"></a></td>
</tr>
<tr>
	<td height="10"></td>
</tr>
</form>
</table>
<?=$onload?>

</body>
</html>