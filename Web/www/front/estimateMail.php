<?
// 견적서 메일링

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

	$InputMemName = $_ShopInfo->memname;
	$InputMemEmail = $_ShopInfo->mememail;

	if( $_POST['sendOK'] == "estimateSend" AND $_POST['EMAIL'] ) {

		$FromName = $_data->shopname;
		$FromEmail = $_data->privercyemail;

		$MemName = $_POST['NAME'];
		$MemEmail = $_POST['EMAIL'];

		$subject = $MemName."님의 ".$FromName." 견적서 [".date("Y/m/d H:s")."]";

		$header = "From:". $FromName . "<" . $FromEmail . ">\r\n";
		$header .= "Content-Type:text/html; charset=euc-kr\r\n";

		ob_start();
?>
		<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
			<head>
				<meta http-equiv=Content-Type content="text/html; charset=ks_c_5601-1987">
				<?
					include "estimateStyle.php";
				?>
			</head>
			<body link=blue vlink=purple>
				<?
					include "estimateSheet.php";
				?>
			</body>
		</html>
<?
		$mail_body = ob_get_contents();
		ob_end_clean();

		mail($MemEmail, $subject, $mail_body, $header);

?>
	<script type="text/javascript">
	<!--
		alert("<?=$MemName?>(<?=$MemEmail?>)님의 메일로 견적서 메일발송 완료");
		self.close();
	//-->
	</script>
<?
	} else {
?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<title>견적서 발송</title>
			<script type="text/javascript">
			<!--
				function sendMail ( f ) {

					if( f.NAME.value.length == 0 ) {
						alert("이름을 입력하세요.");
						f.NAME.focus();
						return false;
					}

					if( f.EMAIL.value.length == 0 ) {
						alert("이메일을 입력하세요.");
						f.EMAIL.focus();
						return false;
					}

					f.method="POST";
					f.submit();
				}
			//-->
			</script>
			
			<style>
				BODY,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}
				A:link    {color:#635C5A;text-decoration:none;}
				A:visited {color:#545454;text-decoration:none;}
				A:active  {color:#5A595A;text-decoration:none;}
				A:hover  {color:#545454;text-decoration:underline;}
				.input{font-size:12px; BORDER:1px solid #DCDCDC; HEIGHT:18px; line-height:18px;}

				.warpEstimateMail {width:100%; margin:0px; margin-top:10px; padding:0px;}
				.warpEstimateMail ul {list-style:none; margin:0px; padding-left:10px;}
				.warpEstimateMail .sendInfo1{float:left; width:24%; height:28px; line-height:28px; font-size:12px; font-family:돋움; font-weight:bold; border-bottom:1px solid #ddd;}
				.warpEstimateMail .sendInfo2 {float:left; width:4%; font-size:0px; height:28px; line-height:28px; border-bottom:1px solid #ddd;}
				.warpEstimateMail .sendInfo3 {float:left; width:70%; height:28px; line-height:28px; border-bottom:1px solid #ddd;}
			</style>
		</head>

		<body topmargin="0" leftmargin="0" onload="resizeTo(400,240);">
			<div style="background:url('../images/common/email/001/formmail_skin_titlebg.gif') repeat-x;"><img src="/images/common/email/001/formmail_skin_title2.gif" alt="" /></div>
			<DIV class="warpEstimateMail">
				<form name="sendMailForm">
				<ul>
					<li class="sendInfo1"><IMG SRC="../images/common/email/001/formmail_skin_nero.gif" border="0"> 이름</li>
					<li class="sendInfo2"><img src="/images/common/email/001/formmail_skin_line2.gif" vspace="6" alt="" /></li>
					<li class="sendInfo3"><input type="text" name="NAME" value="<?=$InputMemName?>" style="width:95%;" class="input"></li>
				</ul>
				<ul>
					<li style="clear:both;" class="sendInfo1"><IMG SRC="../images/common/email/001/formmail_skin_nero.gif" border="0"> 이메일</li>
					<li class="sendInfo2"><img src="/images/common/email/001/formmail_skin_line2.gif" vspace="6" alt="" /></li>
					<li class="sendInfo3"><input type="text" name="EMAIL" value="<?=$InputMemEmail?>" style="width:95%;" class="input"></li>
				</ul>
				<input type="hidden" name="sendOK" value="estimateSend">
				</form>
				</ul>
			</DIV>
			<div style="margin:0 auto; text-align:center;"><img src="/images/common/email/001/formmail_skin_send.gif" alt="메일보내기" style="cursor:pointer;" onclick="sendMail(sendMailForm);" /> <img src="/images/common/email/001/formmail_skin_close.gif" alt="닫기" style="cursor:pointer;" onclick="javascript:window.close();" />
			</div>
		</body>
	</html>
<?
	}
?>