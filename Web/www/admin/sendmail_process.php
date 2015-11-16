<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');</script>";
	exit;
}

function sendMailForm($sender_name,$sender_email,$message,$upfile,&$bodytext,&$mailheaders) {
	$boundary = "--------" . uniqid("part");

	$mailheaders  = "From: $sender_name <$sender_email>\r\n";
	//$mailheaders .= "X-Mailer:SendMail\r\n";
	//$mailheaders .= "MIME-Version: 1.0\r\n";

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


$bodytext .= "
	<style type=\"text/css\">
		body, td {font-family:굴림; font-size:12px; color:#666666}
		img {margin:0; border:0;}
		table a:link,
		table a:active,
		table a:visited {color:#666666; text-decoration:none;}
		table a:hover {text-decoration:none;}
	</style>

	<table width=\"690\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">
		<tr><td height=\"50\"></td></tr>
		<tr>
			<td height=\"35\" valign=\"bottom\" style=\"font-size:12px; padding-bottom:5px; padding-left:50px; background-image:url(".$shopurl."/images/mail/solution/logo_1.gif); background-position:0 bottom; background-repeat:no-repeat;\"><strong>".$shopname."</strong></td>
		</tr>
		<tr>
			<td>
				<table width=\"690\" height=\"232\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"background:url(".$shopurl."/images/mail/solution/top_bg.jpg) 0 0 no-repeat;\">
					<tr><td height=\"4\" style=\"padding-top:60px; padding-left:24px; color:#222222; font-size:30px; font-family:verdana; font-weight:bold; letter-spacing:-1px;\"><span style=\"color:#fd6b00;\">".$shopname."</span></td></tr>
					<tr><td height=\"23\" style=\"padding-left:25px;\"><img src=\"".$shopurl."/images/mail/solution/top_img_2.gif\" /></td></tr>
					<tr>
						<td valign=\"top\" style=\"padding-left:26px; padding-top:30px; line-height:18px;\">
							안녕하세요. <span style=\"color:#fd6b00;\">".$shopname."</span> 입니다.<br />
							<span style=\"font-size:16px; color:#222222; font-family:돋움;\">".$subject."</span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height=\"10\"></td></tr>
		<tr><td style=\"background-color:#f5f5f5; padding:15px 24px; text-align:left; vertical-align:text-top; color:#666666; line-height:18px;\">".$body."</td></tr>
		<tr><td height=\"20\"></td></tr>
		<tr><td align=\"center\"><a href=\"".$shopurl."\" target=\"_blank\"><img src=\"".$shopurl."/images/mail/solution/btn_1.gif\" border=\"0\" alt=\"\" /></a></td></tr>
		<tr><td height=\"20\"></td></tr>
		<tr>
			<td>
				<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
					<tr>
						<td width=\"20\"></td>
						<td><a href=\"/\" target=\"_blank\"><img src=\"".$shopurl."/images/mail/solution/copy_logo.gif\" border=\"0\" alt=\"\" /></a></td>
						<td style=\"padding-left:30px; font-size:11px; color:#8f8f8f; line-height:18px;\">
							본 메일은 정보통신망률 등 관련규정에 의거하여 수신동의하신 회원에게 발송되었습니다. <br />
							본 메일은 발신전용메일입니다. 메일 수신을 원치 않으시면 <b><a href=\"/front/mypage_usermodify.php\" target=\"_blank\">[수신거부]</a></b> 클릭하십시오. <br />
							COPYRIGHT (C) <b>".$shopname."</b> ALL RESERVED.
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height=\"40\"></td></tr>
	</table>
";


if (strlen($to)>0 && strlen($from)>0 && strlen($subject)>0 && strlen($body)>0) {
	sendMailForm($rname,$from,$body,$upfile,$bodytext,$mailheaders);
	$tolist=explode(",",$to);
	for($i=0;$i<count($tolist);$i++) {
		$tomail=trim($tolist[$i]);
		if(ismail($tomail)) {
			mail($tomail, $subject, $bodytext, $mailheaders);
		}
	}
	echo "<html></head><body onload=\"alert('메일 발송이 완료되었습니다.');\"></body></html>";
	exit;
}
?>