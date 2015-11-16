<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$gong_seq=$_REQUEST["gong_seq"];

if(strlen($_ShopInfo->getId())==0 || strlen($gong_seq)==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

INCLUDE ("access.php");

function sendMailForm($sender_name,$sender_email,$message,$upfile,&$bodytext,&$mailheaders) {
	$boundary = "--------" . uniqid("part");

	$mailheaders  = "From: $sender_name <$sender_email>\r\n";
	$mailheaders .= "X-Mailer:SendMail\r\n";
	$mailheaders .= "MIME-Version: 1.0\r\n";

	if ($upfile && $upfile["size"]>0) {	// 첨부파일 있으면...
		$mailheaders .= "Content-Type: Multipart/mixed; boundary=\"$boundary\"";
		$bodytext  = "This is a multi-part message in MIME format.\r\n";
		$bodytext .= "\r\n--$boundary\r\n";
		$bodytext .= "Content-Type: text/html; charset=euc-kr\r\n";
		$bodytext .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
		$bodytext .= $message . "\r\n\r\n";

		$filename = basename($upfile["name"]);
		$result = fopen($upfile["tmp_name"], "r");
		$file = fread($result, $upfile["size"]);
		fclose($result);

		if ($upfile["type"]=="") {
			$upfile["type"] = "application/octet-stream";
		}

		$bodytext .= "\r\n--$boundary\r\n";
		$bodytext .= "Content-Type: $upfile[type]; name=\"$filename\"\r\n";
		$bodytext .= "Content-Transfer-Encoding: base64\r\n";
		$bodytext .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
		$bodytext .= chunk_split(base64_encode($file))."\r\n";
		$bodytext .= "\r\n--".$boundary."--\r\n";
	} else {
		$mailheaders .= "Content-Type: text/html;";
		$bodytext .= $message . "\r\n\r\n";
	}
}

$sql = "SELECT * FROM tblgonginfo WHERE gong_seq='".$gong_seq."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	if($row->end_date>date("YmdHis")) {
		echo "<html><head><title></title></head><body onload=\"alert('종료된 공동구매에 대해서만 메일을 발송할 수 있습니다.');window.close();\"></body></html>";exit;
	}
	$num=intval($row->bid_cnt/$row->count);
	$price=$row->start_price-($num*$row->down_price);
	if($price<$row->mini_price) $price=$row->mini_price;
	$receipt_date=date("Y년m월d일",mktime(0,0,0,substr($row->end_date,4,2),substr($row->end_date,6,2)+$row->receipt_end,substr($row->end_date,0,4)));
} else {
	echo "<html><head><title></title></head><body onload=\"alert('해당 공동구매가 존재하지 않습니다.');window.close();\"></body></html>";exit;
}
mysql_free_result($result);

$mode=$_POST["mode"];
if($mode=="send") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", "history.go(-1)");
	#######################################################################

	$sender_name=$_POST["sender_name"];
	$sender_email=$_POST["sender_email"];
	$subject=$_POST["subject"];
	$message=$_POST["message"];
	$upfile=$_FILES["upfile"];

	if(strlen($sender_email)==0) {echo "<script>alert(\"보내는 사람 이메일을 입력하세요.\");history.go(-1);</script>"; exit;}
	if (!ereg("^[^@ ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)$", $sender_email)) {
		echo "<script>alert(\"보내는 사람 이메일 형식이 맞지않습니다.\\n\\n확인하신 후 다시 입력하세요.\");history.go(-1);</script>"; exit;
	}
	if(strlen($subject)==0) {echo "<script>alert(\"제목을 입력하세요..\");history.go(-1);</script>"; exit;}
	if(strlen($message)==0) {echo "<script>alert(\"내용을 입력하세요..\");history.go(-1);</script>"; exit;}
	if($upfile["size"]>0) {
		$ext = strtolower(substr($upfile[name],strlen($upfile[name])-3,3));
	}
	if($upfile["size"]>204800) {
		echo "<script>alert(\"이미지는 200K이하로 첨부 가능합니다.\");history.go(-1);</script>"; exit;
	}

	sendMailForm($sender_name,$sender_email,nl2br($message),$upfile,$bodytext,$mailheaders);

	$sql = "SELECT email FROM tblgongresult WHERE gong_seq='".$gong_seq."' ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		if(ismail($row->email)) {
			sendmail($row->email, $subject, $bodytext, $mailheaders);
		}
	}
	mysql_free_result($result);

	echo "<html><head><title></title></head><body onload=\"alert('메일을 발송하였습니다.');window.close();\"></body></html>";exit;
}

?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>공동구매 참여자 메일 보내기</title>
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 75;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm() {
	isMailChk = /^[^@ ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)$/;
	if(document.email_form.sender_email.value.length==0) {
		alert("보내는 사람 이메일을 입력하세요.");
		document.email_form.sender_email.focus();
		return;
	}
	if(!isMailChk.test(document.email_form.sender_email.value)) {
		alert("보내는 사람 이메일 형식이 맞지않습니다.\n\n확인하신 후 다시 입력하세요.");
		document.email_form.sender_email.focus();
		return;
	}
	if(document.email_form.subject.value.length==0) {
		alert("제목을 입력하세요.");
		document.email_form.subject.focus();
		return;
	}
	if(document.email_form.message.value.length==0) {
		alert("내용을 입력하세요.");
		document.email_form.message.focus();
		return;
	}

	document.email_form.mode.value="send";
	document.email_form.submit();
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="430" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<form name=email_form method=post action="<?=$_SERVER[PHP_SELF]?>" enctype="multipart/form-data">
<input type=hidden name=mode>
<input type=hidden name=gong_seq value="<?=$gong_seq?>">
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><IMG SRC="images/gong_gongchangelist_mail_ti.gif" WIDTH="180" HEIGHT="31" ALT=""></td>
		<td width="100%" background="images/member_mailallsend_imgbg.gif">&nbsp;</td>
		<td align="right"><img src="images/member_mailallsend_img2.gif" width="20" height="31" border="0"></td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD style="padding:5pt;">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<col width=95></col>
	<col width=></col>
	<TR>
		<TD colspan=2 background="images/table_top_line.gif"></TD>
	</TR>
	<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">보내는이 메일</TD>
		<TD class="td_con1"><INPUT class=input onkeyup=strnumkeyup(this) style="width:100%" maxLength=50 name=sender_email value="<?=$_shopdata->info_email?>"></TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">보내는이 이름</TD>
		<TD class="td_con1"><INPUT class=input onkeyup=strnumkeyup(this) style="width:100%" maxLength=30 name=sender_name value="<?=$_shopdata->shopname?>"></TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<TR>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">제목</TD>
		<TD class="td_con1"><INPUT class=input onkeyup=strnumkeyup(this) style="width:100%" maxLength=100 name=subject value="<?=$row->gong_name?> 공구현황입니다."></TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<tr>
		<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">첨부파일</TD>
		<TD class="td_con1">
		<table cellpadding="0" cellspacing="0" width="98%">
		<col width=200></col>
		<col width=></col>
		<tr>
			<td><INPUT class=input style=width:100% type=file size=10 name=upfile></td>
			<td align=right><span class="font_orange">(*200kb이하)</span></td>
		</tr>
		</table>
		</TD>
	</tr>
	<TR>
		<TD colspan=2 background="images/table_top_line.gif"></TD>
	</TR>
	</TABLE>
	</TD>
</TR>
<TR>
	<TD width="100%" style="padding:5pt;">
	<TEXTAREA style="WIDTH: 100%; HEIGHT: 195px" name=message wrap=off class="textarea">
<?
	$tmp=explode("=",$_shopdata->bank_account);
	$bank_account=$tmp[0];
	$jiro="";
	if (strlen($bank_account)>0) {
		$tok = explode(",",$bank_account);
		$count = count($tok);
		for($i=0;$i<$count;$i++) if(strlen($tok[$i])>0) $jiro.=$tok[$i].", ";
	}
	$jiro=substr($jiro,0,(strlen($jiro)-2));

	echo "안녕하세요. ".$_shopdata->shopname." 입니다.\n\n";
	echo $row->gong_name."의 공구가 마감되었습니다.\n";
	echo "총 수량 ".$row->quantity."개에서 ".$row->bid_cnt."개가 접수되었습니다.\n";
	echo "공구 가격은 ".number_format($price)."원 입니다.\n";
	if(strlen($row->deli_money)==0) {
		echo "공구 배송료는 무료입니다.\n";
	} else if(strlen($row->deli_money)>0 && $row->deli_money==0) {
		echo "공구 배송료는 착불입니다.\n";
	} else if(strlen($row->deli_money)>0 && $row->deli_money>0) {
		echo "공구 배송료는 ".number_format($row->deli_money)."입니다.\n";
	}
	echo "공구 결제가격은 ".number_format($price+$row->deli_money)."원 입니다.\n";
	echo "입금 계좌는 ".$jiro." 입니다.\n";
	echo $receipt_date." 까지 입금 바랍니다.\n\n";
	echo "감사합니다.\n";
?>
	</TEXTAREA>
	</TD>
</TR>
<TR>
	<TD width="100%"><hr size="1" width="98%" color="#F3F3F3"></TD>
</TR>
<TR>
	<TD align=center><A HREF="javascript:CheckForm();"><img src="images/btn_transe.gif" width="76" height="28" border="0" vspace="3" border=0></a><a href="javascript:window.close()"><img src="images/btn_cancel.gif" width="76" height="28" border="0" vspace="3" border=0 hspace="2"></a></TD>
</TR>
</form>
</TABLE>

</body>
</html>