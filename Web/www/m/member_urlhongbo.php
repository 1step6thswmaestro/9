<?
include_once("header.php");

$mode=$_POST["mode"];

if(!_empty($_ShopInfo->getMemid())){
	$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$_mdata=$row;
		$sendUrl_id = $row->url_id;
		$sendId = $row->id;
		$sendName = $row->name;
		$sendEmail = $row->email;
	}
	mysql_free_result($result);
}
if($_data->recom_url_ok != "Y"){
	echo "<html><head><title></title></head><body onload=\"alert('ȫ���������� �����Ǿ������ʽ��ϴ�.');window.close();\"></body></html>";exit;
}

if($mode=="send" && $sendUrl_id && $sendName) {
	$arEmails=explode(",", $_POST["in_email"]);
	$message=$_POST["in_message"];
	
	$mess2=$row->email."�� ������ ";
	for($i=0;$i<sizeof($arEmails);$i++) {
		SendUrlMail($_data->shopname, $_data->shopurl, $_data->design_mail, $message, $sendEmail, $arEmails[$i], $sendName, $sendUrl_id, $sendId, $_data->recom_memreserve);
	}
	echo "<html><head><title></title></head><body onload=\"alert('������ ���۵Ǿ����ϴ�.'); location.href='/m/member_urlhongbo.php'; \"></body></html>";exit;
}

$hongboUrl = "http://".$_data->shopurl."?token=".$sendUrl_id;
$hongboTle = sprintf("[%s]�� �����ϼ���.",$_data->shopname);

$sAddRecom = "";
if($_data->reserve_join >0){
	$sAddRecom = $_data->shopname." ������ �ǽø� <span style=\"color:#CC0035\">".$_data->reserve_join."��</span>�� �������� �帲�ϴ�.<br/>";
}
if($_data->recom_ok == "Y") {
	$arRecomType = explode("", $_data->recom_memreserve_type);

	if($arRecomType[0] == "A"){
		$sAddRecom.= "�Ұ� ���� ģ������ �ű�ȸ�����Խ� <span style=\"color:#CC0035\">".$_data->recom_memreserve."��</span>�� �������� ������ �� �ִ�ϴ�.</span>";
		$sAddRecom2 ="ȸ������ URL�� ���� �ű�ȸ�������� �� ��� <span style=\"color:#CC0035\">".number_format($_data->recom_memreserve)."��</span>�� �������� �帳�ϴ�.";
	}else if($arRecomType[0] == "B"){
		$sAddRecom .= "�Ұ� ���� ģ������ ù ���Ű� �Ϸ�� ������ <span style=\"color:#CC0035\">";
		$sAddRecom2 = "ȸ���Կ� URL�ּҷ� ������ ��� ȸ���Կ��� <span style=\"color:#CC0035\">";
		if($arRecomType[1] == "A"){
			if($arRecomType[2] == "N"){
				$sAddRecom .= $_data->recom_memreserve."����";
				$sAddRecom2 .= $_data->recom_memreserve."��</span>��";
			}else if($arRecomType[2] == "Y"){
				$sAddRecom .= "���űݾ��� ".$_data->recom_memreserve."%��";
				$sAddRecom2 .= "���űݾ��� ".$_data->recom_memreserve."%</span>��";
			}
		}else if($arRecomType[1] == "B"){
			$sAddRecom .= "���űݾ׿� ����";
			$sAddRecom2 .= "���űݾ׿� ����</span>";
		}
		$sAddRecom .= " ������</span>�� ������ �� �ִ�ϴ�.";
		$sAddRecom2 .=" �������� �帮��<br>��,ģ���е��� ù ���Ű� �Ϸ�ɶ� �������� �����ص帳�ϴ�.";
	}
}

// SMS ȫ�� �߼�
if( $mode == "sms_urlhongbo" ) {
	$sql="SELECT * FROM tblsmsinfo ";
	$result=mysql_query($sql,get_db_conn());
	if($rowsms=mysql_fetch_object($result)) {
		$sms_id=$rowsms->id;
		$sms_authkey=$rowsms->authkey;

		$sender = $_POST["send1"].$_POST["send2"].$_POST["send3"];
		$cell = $_POST["cel1"].$_POST["cel2"].$_POST["cel3"];

		$msg_hongbo = "[".$_data->shopname."]".$sendName."���� " .$_data->shopname. "(".$hongboUrl.")�� ��õ�ϼ̾��!!";

		$etcmsg = "������õ URL";

		$use_mms = $rowsms->use_mms;

		$temp=SendSMS2($sms_id, $sms_authkey, $cell, "", $sender, 0, $msg_hongbo, $etcmsg, $use_mms);
		$resmsg=explode("[SMS]",$temp);
		echo "<html></head><body onload=\"alert('".$resmsg[1]."'); location.href='/m/member_urlhongbo.php'; \"></body></html>";
		exit;
	}
}
$functionname = "";
$nologin ="";
if(strlen($_ShopInfo->getMemid()) > 0){
	$functionname = 'this.selectionStart=0; this.selectionEnd=this.value.length;';
}else{
	$functionname = 'nologin()';
	$nologin ='onClick="nologin();"';
}

$appname = $_data->shopname;
$appid =$_SERVER['HTTP_HOST'];
$cacaostorycontent = "";
$cacaostoryimgsrc = "http://".$_SERVER['HTTP_HOST']."/m/upload/logo.gif";
?>
<div id="content">
	<div class="h_area2">
		<h2>ȫ������������</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>
	<script type="text/javascript" src="../m/js/kakao.link.js"></script>
	<script>
		
		function IsMailCheck(email) {
			isMailChk = /^[^@ ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)$/;
			if(isMailChk.test(email)) {
				return true;
			} else {
				return false;
			}
		}
		function CheckForm() {
			if(document.form1.in_email.value.length==0) {
				alert("�̸����� �Է��ϼ���.");
				document.form1.in_email.focus();
				return;
			}
			var email = document.form1.in_email.value;
			if(email.indexOf(",") >0){
				arEmail = email.split(",");
				for(i=0;i<arEmail.length;i++){
					if(!IsMailCheck(arEmail[i].trim())) {
						alert("�̸��� ������ �����ʽ��ϴ�.\n\nȮ���Ͻ� �� �ٽ� �Է��ϼ���.");
						document.form1.in_email.focus(); return;
					}
				}
			}else{
				if(!IsMailCheck(email.trim())) {
					alert("�̸��� ������ �����ʽ��ϴ�.\n\nȮ���Ͻ� �� �ٽ� �Է��ϼ���.");
					document.form1.in_email.focus(); return;
				}
			}
			if(document.form1.in_message.value.length==0) {
				alert("�������� �Է��ϼ���.");
				document.form1.in_message.focus();
				return;
			}
			document.form1.mode.value="send";
			document.form1.submit();
		}

		function goFaceBook()
		{
			var href = "http://www.facebook.com/sharer.php?u=" + encodeURIComponent('<?=$hongboUrl?>') + "&t=" + encodeURIComponent('<?=$hongboTle?>');
			var a = window.open(href, 'Facebook', '');
			if (a) {
				a.focus();
			}
		}

		function goTwitter()
		{
			var href = "http://twitter.com/share?text=" + encodeURIComponent('<?=$hongboTle?>') + " " + encodeURIComponent('<?=$hongboUrl ?>');
			var a = window.open(href, 'Twitter', '');
			if (a) {
				a.focus();
			}
		}
		function nologin(){
			alert('���� ȫ��URL�� ȸ������ ����Դϴ�.\nȸ�� �α��� �� �̿��� �ּ���.');
			window.location='/m/login.php?chUrl='+"<?=getUrl()?>";
		}
		function setstate(){
			var _obj = document.getElementById('urlhongbo');
			_obj.readOnly= true;
			return;
		
		}
</SCRIPT>
	<section id="sec_urlprom_wrap">
		<div class="div_urltopmsg">
			<h2>�������� ��������!<br />ģ������ ���θ��� �Ұ��� �ּ���!</h2>
			<div style="margin-top:15px;">
				<ul style="padding-left:60px; background:url('/images/design/detail_pop_email_img01.gif') no-repeat; background-size:auto 50px;">
					<li>�Ʒ� ȸ������ <b>URL�ּ�</b>�� ������ �ּ���!</li>
					<li><b>���� ��URL�ּ�</b>�� �Բ� �ٸ� �е��� ���θ��� ������ �� �ֵ��� <b><font color="#E6B044">ī��, ��α�, ���� SNS</font></b> ���� ���� ���θ��� �Ұ����ּ���.</li>
				</ul>

				<ul style="height:50px; margin-top:10px; padding-top:8px; padding-left:60px; background:url('/images/design/detail_pop_email_img02.gif') no-repeat; background-size:auto 50px;">
					<li><?=$sAddRecom2?></li>
				</ul>
			</div>
		</div>

		<div class="div_urlarea">
			<h4>�� ȫ��URL</h4>
			<p><input type="text" name="urlhong" id="urlhongbo" value="<?=$hongboUrl?>" onClick="<?=$functionname?>;setstate();" /></p>
			<p class="p_urlareamsg">
			* �ּҸ� ��ġ �Ͻø� ��Ÿ���� �޴����� ������ �� �ֽ��ϴ�.<br/>
			* ����� ȯ�濡 ���� ���簡 ���� ���� ��� �ּҺκ��� ��� ��ġ�Ͻø�, ��ü ���� �� ���簡 �����մϴ�.<br/>
			
			</p>
		</div>
		<?
			$smsCount = smsCountValue();
			if( $smsCount > 0 AND strlen($_ShopInfo->getMemid())>0 AND $_ShopInfo->getMemid()!="deleted" ){
		?>
		<script>
			function sms_urlhongbo_send () {
				if(document.form2.send1.value.length==0) {
					alert("SMS �߽��� ��ȣ�� �Է��ϼ���.");
					document.form2.send1.focus();
					return false;
				}
				if(document.form2.send2.value.length==0) {
					alert("SMS �߽��� ��ȣ�� �Է��ϼ���.");
					document.form2.send2.focus();
					return false;
				}
				if(document.form2.send3.value.length==0) {
					alert("SMS �߽��� ��ȣ�� �Է��ϼ���.");
					document.form2.send3.focus();
					return false;
				}
				if(document.form2.cel1.value.length==0) {
					alert("SMS ������ ��ȣ�� �Է��ϼ���.");
					document.form2.cel1.focus();
					return false;
				}
				if(document.form2.cel2.value.length==0) {
					alert("SMS ������ ��ȣ�� �Է��ϼ���.");
					document.form2.cel2.focus();
					return false;
				}
				if(document.form2.cel3.value.length==0) {
					alert("SMS ������ ��ȣ�� �Է��ϼ���.");
					document.form2.cel3.focus();
					return false;
				}
				document.form2.submit();
			}
		</script>
		<div class="div_smsarea">
			<h4>�� SMS�� �Ұ��ϱ�</h4>
			<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
				<input type="hidden" name="mode" value="sms_urlhongbo">
				<div class="div_smsinfo">
					<p>
						<span>�߽��� ��ȣ</span> :
						<input type="number" name="send1" size="5" maxlength="4" <?=$nologin?>>-<input type="number" name="send2" size="5" maxlength="4" <?=$nologin?>>-<input type="number" name="send3" size="5" maxlength="4" <?=$nologin?>>
					</p>
					<br/>
					<p>
						<span>������ ��ȣ</span> :
						<input type="number" name="cel1" size="5" maxlength="4" <?=$nologin?>>-<input type="number" name="cel2" size="5" maxlength="4" <?=$nologin?>>-<input type="number" name="cel3" size="5" maxlength="4" <?=$nologin?>>
					</p>
					<br/>
					<p class="p_btnsmsarea">
						<button id="btn_sendsms" class="button white bigrounded" onClick="return sms_urlhongbo_send();">SMS�߼�</button>
					</p>
				</div>
			</form>
		</div>
		<?
			}
			if($_data->sns_ok == "Y"){
		?>
		<div class="div_snsarea sns_wrap">
			<h4>�� SNSȫ���ϱ�</h4>
			<?
				$kakaotalk_func=$kakaostory_func="";
				if($_ShopInfo->getMemid() ==""){
					$kakaotalk_func=$kakaostory_func='javascript:nologin();';
				}else{
					$kakaotalk_func="javascript:executeKakaoLink('".$hongboTle."','".$hongboUrl."','".$appid."','".$appname."');";
					$kakaostory_func="javascript:executeKakaoStoryLink('".$hongboUrl."','".$appid."','".$appname."','".$hongboTle."','','".$cacaostoryimgsrc."');";
				}
			?>
			<span>
				<a href="javascript:goTwitter();"><div class="snstwitter"></div></a>
				<a href="javascript:goFaceBook();"><div class="snsfacebook"></div></a>
				<a href="<?=$kakaotalk_func?>"><div class="snskakaotalk"></div></a>
				<a href="<?=$kakaostory_func?>"><div class="snskakaostory"></div></a>
			</span>
		</div>
		<?
			}
		?>
		<div class="div_mailarea">
			<h4>�� E-mail ȫ���ϱ�</h4>
			<div>
				<form name="form1" action="<?=$_SERVER[PHP_SELF]?>" method="post">
					<input type=hidden name=mode value="">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tbody>
							<tr>
								<th>�̸��� :</th>
								<td><input type="text" name="in_email" <?=$nologin?>></td>
							</tr>
							<tr>
								<th>��&nbsp;&nbsp;&nbsp;&nbsp;�� :</th>
								<td>
<textarea name="in_message" rows="3" noresize <?=$nologin?>>
<?=$sendName?>�Բ��� <?=$_data->shopname?>(<?=$hongboUrl?>)�� ��õ�ϼ̾��!!
</textarea>
<!--
<?//=$sendName?>�Բ��� ���ϲ� <?//=$_data->shopname?>�� ��õ�ϼ̽��ϴ�.
���� ������� <?//=$_data->shopname?>�� ������ ����������.
<?//=$hongboUrl?>
-->
								</td>
							</tr>
						</tbody>
					</table>
				</form>
				<div class="div_btnmailarea">
					<button class="button white bigrounded" onClick="<?=($_ShopInfo->getMemid() != "")? 'CheckForm()':'nologin()'?>;" id="btn_sendmail">��õ�ϱ�</button>
				</div>
			</div>
		</div>
	</section>

</div>
<?
include_once("footer.php");
?>