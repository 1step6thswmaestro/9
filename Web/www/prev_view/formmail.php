<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getShopurl())==0) {
	echo "<script>window.close();</script>";
	exit;
}

$emailsendcnt=(int)$_COOKIE["emailsendcnt"];
if($emailsendcnt>5) {
	echo "<script>alert(\"5회 이상 연속 발송이 불가능합니다.\");window.close();</script>"; exit;
}

$sql = "SELECT info_email FROM tblshopinfo ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
mysql_free_result($result);
if(strlen($row->info_email)>0) {
	$info_email=$row->info_email;
}
if(strlen($info_email)==0) {
	echo "<script>alert('관리자 이메일 등록이 안되어 메일발송이 안됩니다.');window.close();</script>";
	exit;
}

$mode=$_POST["mode"];
$sender_name=$_POST["sender_name"];
$sender_email=$_POST["sender_email"];
$subject=$_POST["subject"];
$message=$_POST["message"];
$upfile=$_FILES["upfile"];

if($mode=="send") {
	if(strlen($sender_email)==0) {echo "<script>alert(\"보내는 사람 이메일을 입력하세요.\");history.go(-1);</script>"; exit;}
	if (!ereg("^[^@ ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)$", $sender_email)) {
		echo "<script>alert(\"보내는 사람 이메일 형식이 맞지않습니다.\\n\\n확인하신 후 다시 입력하세요.\");history.go(-1);</script>"; exit;
	}
	if(strlen($subject)==0) {echo "<script>alert(\"제목을 입력하세요..\");history.go(-1);</script>"; exit;}
	if(strlen($message)==0) {echo "<script>alert(\"내용을 입력하세요..\");history.go(-1);</script>"; exit;}
	if($upfile["size"]>0) {
		$ext = strtolower(substr($upfile[name],strlen($upfile[name])-3,3));
		if($ext!="gif" && $ext!="jpg" && $ext!="bmp") {
			echo "<script>alert(\"첨부파일은 이미지만 가능합니다.\");history.go(-1);</script>"; exit;
		}
	}
	if($upfile["size"]>204800) {
		echo "<script>alert(\"이미지는 200K이하로 첨부 가능합니다.\");history.go(-1);</script>"; exit;
	}

	$emailsendcnt++;
	setcookie("emailsendcnt",$emailsendcnt,time()+3600,"/");

	sendMailForm($sender_name,$sender_email,$message,$upfile,$bodytext,$mailheaders);
	sendmail($info_email, $subject, $bodytext, $mailheaders);

	echo "<script>alert(\"메일을 발송하였습니다.\"); window.close();</script>"; exit;
}

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

?>

<html>
<head>
<title>쇼핑몰 운영자에게 메일보내기</title>
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
var stateFlag=1;
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

	if(typeof(document.email_form.upfile)!="undefined") {
		if(document.email_form.upfile.value.length>0) {
			if(stateFlag==0) {
				alert("첨부파일은 이미지만 첨부 가능합니다.");
				return;
			}
			filesize = Number(document.all["addfile"].fileSize);	//maxsize:204800
			if(filesize>204800) {
				alert("이미지는 200K이하로만 첨부 가능합니다.");
				return;
			}
		}
	}

	document.email_form.mode.value="send";
	document.email_form.submit();
}

function checkImgFormat(imgPath) {
	if(imgPath.length==0) {
		stateFlag = 1;
	} else {
		if ( imgPath.toLowerCase().indexOf(".gif") != -1 || imgPath.toLowerCase().indexOf(".jpg") != -1 || imgPath.toLowerCase().indexOf(".bmp") != -1 )
		{
			stateFlag = 1;
			document.getElementById('addfile').src=imgPath;
		} else {
			stateFlag = 0;
			document.getElementById('addfile').src="";
			alert("이미지 파일만 첨부 가능합니다.");
		}
	}
}

var g_fIsSP2 = false;
g_fIsSP2 = (window.navigator.userAgent.indexOf("SV1") != -1);
//-->
</SCRIPT>
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0>

<?
$sql = "SELECT * FROM tbldesignnewpage_prev WHERE type='formmail' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$email_type=$row->code;
	$content=$row->body;
	$content=str_replace("[DIR]",$Dir,$content);
	if($email_type=="U" && strlen($content)==0) {
		$email_type="001";
	}
	$size=$row->filename;
} else {
	$email_type="001";
}
$size = "1024768";
mysql_free_result($result);

include($Dir.TempletDir."email/emailU.php");
?>

</body>
</html>