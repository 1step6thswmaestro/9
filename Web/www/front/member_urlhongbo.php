<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

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
	//echo "<html><head><title></title></head><body onload=\"alert('홍보적립금이 설정되어있지않습니다.');window.close();\"></body></html>";exit;
	echo "<html><head><title></title></head><body onload=\"alert('홍보적립금이 설정되어있지않습니다.');history.back(-1);\"></body></html>";exit;
}

if($mode=="send" && $sendUrl_id && $sendName) {
	$arEmails=explode(",", $_POST["in_email"]);
	$message=$_POST["in_message"];

	$mess2=$row->email."로 메일을 ";
	for($i=0;$i<sizeof($arEmails);$i++) {
		SendUrlMail($_data->shopname, $_data->shopurl, $_data->design_mail, $message, $sendEmail, $arEmails[$i], $sendName, $sendUrl_id, $sendId, $_data->recom_memreserve);
	}
	echo "<html><head><title></title></head><body onload=\"alert('메일이 전송되었습니다.'); location.href='/front/member_urlhongbo.php'; \"></body></html>";exit;
}

$hongboUrl = "http://".$_data->shopurl."?token=".$sendUrl_id;
$hongboTle = sprintf("[%s]에 가입하세요.",$_data->shopname);

$sAddRecom = "";
if($_data->reserve_join >0){
	$sAddRecom = $_data->shopname."을 추천하여 가입한 지인이 첫 구매할때 마다 적립금<span style=\"color:#CC0035\">".$_data->reserve_join."원</span>을 드립니다.<br/>";
}
if($_data->recom_ok == "Y") {
	$arRecomType = explode("", $_data->recom_memreserve_type);

	if($arRecomType[0] == "A"){
		$sAddRecom.= "소개 받은 친구들의 신규회원가입시 <span style=\"color:#CC0035\">".$_data->recom_memreserve."원</span>의 적립금을 받으실 수 있답니다.</span>";
		$sAddRecom2 ="회원님에 URL주소로 들어오실 경우 신규회원가입시 <span style=\"color:#CC0035\">".$_data->recom_memreserve."원</span>의 적립금을 드립니다.";
	}else if($arRecomType[0] == "B"){
		$sAddRecom .= "추천 받은 지인들에게도 첫 구매 시 마다 적립금<span style=\"color:#CC0035\">";
		$sAddRecom2 = "회원님의 전용 홍보URL 추천을 통해 지인들이 회원가입할때마다 회원님에게 적립금<span style=\"color:#CC0035\">";
		if($arRecomType[1] == "A"){
			if($arRecomType[2] == "N"){
				$sAddRecom .= $_data->recom_memreserve."원";
				$sAddRecom2 .= $_data->recom_memreserve."원</span>";
			}else if($arRecomType[2] == "Y"){
				$sAddRecom .= "구매금액의 ".$_data->recom_memreserve."%의";
				$sAddRecom2 .= "구매금액의 ".$_data->recom_memreserve."%</span>의";
			}
		}else if($arRecomType[1] == "B"){
			$sAddRecom .= "구매금액에 따른";
			$sAddRecom2 .= "구매금액에 따른</span>";
		}
		$sAddRecom .= "</span>을 지급해드립니다.";
		$sAddRecom2 .="을 <br />드립니다. 추천받은 지인도 첫 구매 시 마다 추가적립금을 지급해드립니다.";
	}
}








// SMS 홍보 발송
if( $mode == "sms_urlhongbo" ) {
	$sql="SELECT * FROM tblsmsinfo ";
	$result=mysql_query($sql,get_db_conn());
	if($rowsms=mysql_fetch_object($result)) {
		$sms_id=$rowsms->id;
		$sms_authkey=$rowsms->authkey;

		$sender = $_POST["send1"].$_POST["send2"].$_POST["send3"];
		$cell = $_POST["cel1"].$_POST["cel2"].$_POST["cel3"];

		$msg_hongbo = "[".$_data->shopname."]가입추천 바로가기 : ".$hongboUrl;

		$etcmsg = "가입추천 URL";

		$use_mms = $rowsms->use_mms;

		$temp=SendSMS2($sms_id, $sms_authkey, $cell, "", $sender, 0, $msg_hongbo, $etcmsg, $use_mms);
		$resmsg=explode("[SMS]",$temp);
		echo "<html></head><body onload=\"alert('".$resmsg[1]."'); location.href='/front/member_urlhongbo.php'; \"></body></html>";
		exit;
	}
}


?>
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 소문내기</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function ClipCopy(url) {
	var tmp;
	tmp = window.clipboardData.setData('Text', url);
	if(tmp) {
		alert('주소가 복사되었습니다.');
	}
}


function CheckForm() {
	if(document.form1.in_email.value.length==0) {
		alert("이메일을 입력하세요.");
		document.form1.in_email.focus();
		return;
	}
	var email = document.form1.in_email.value;
	if(email.indexOf(",") >0){
		arEmail = email.split(",");
		for(i=0;i<arEmail.length;i++){
			if(!IsMailCheck(arEmail[i].trim())) {
				alert("이메일 형식이 맞지않습니다.\n\n확인하신 후 다시 입력하세요.");
				document.form1.in_email.focus(); return;
			}
		}
	}else{
		if(!IsMailCheck(email.trim())) {
			alert("이메일 형식이 맞지않습니다.\n\n확인하신 후 다시 입력하세요.");
			document.form1.in_email.focus(); return;
		}
	}
	if(document.form1.in_message.value.length==0) {
		alert("내용을을 입력하세요.");
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

function goMe2Day()
{
	var href = "http://me2day.net/posts/new?new_post[body]=" + encodeURIComponent('<?=$hongboTle?>') + " " + encodeURIComponent('<?=$hongboUrl ?>') + "&new_post[tags]=" + encodeURIComponent('<?=$_data->shopname?>');
	var a = window.open(href, 'Me2Day', '');
	if (a) {
		a.focus();
	}
}

function goCyworld(){
	var href = "http://csp.cyworld.com/bi/bi_recommend_pop.php?url=" + encodeURIComponent('<?=$hongboUrl ?>') + "&title_nobase64=" + encodeURIComponent('<?=$hongboTle?>') + "&thumbnail=" +  encodeURIComponent("http://<?=$_ShopInfo->getShopurl()?>images/winywill.jpg") + "&write=" + encodeURIComponent('http://<?=$_data->shopurl?>');
	var a = window.open(href, 'Cyworld', 'width=466, height=356');
	if (a) {
		a.focus();
	}
}

function goYozmDaum()
{
	var href = "http://yozm.daum.net/api/popup/prePost?sourceid=54&link=" + encodeURIComponent('<?=$hongboUrl ?>') + "&prefix=" + encodeURIComponent('<?=$_data->shopname ?> > <?=$hongboTle?>\'') + "&parameter=" + encodeURIComponent('<?=$hongboTle?>');
	var a = window.open(href, 'yozmSend', 'width=466, height=356');
	if (a) {
		a.focus();
	}
}

function nologin(){
	alert('전용 홍보URL은 회원전용 기능입니다.\n회원 로그인 후 이용해 주세요.');
	window.location='/front/login.php';
}

//window.resizeTo(730,765);
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
	include ($Dir.MainDir.$_data->menu_type.".php");
?>

<table cellpadding="0" cellspacing="0" width="100%" align="center">
	<tr>
		<td>

			<div class="memberbenefit">
				<h2>MUST HAVE! 쇼핑혜택</h2>
				<div><img src="/images/003/benefit_top.jpg" alt="" /></div>
				<div class="benefitmenu">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><a href="/front/newpage.php?code=1">회원혜택</a></td>
							<td><a href="/front/newpage.php?code=2">상품평혜택</a></td>
							<td><a href="/front/couponlist.php">쿠폰모음</a></td>
							<td><a href="/front/productgift.php">전용이용권</a></td>
							<td><a href="/front/attendance.php">출석체크</a></td>
							<td class="nowon"><a href="/front/member_urlhongbo.php">홍보적립금혜택</a></td>
							<td><a href="/board/board.php?board=storytalk">스토리톡</a></td>
						</tr>
					</table>
				</div>

				<script language="javascript">
					function prgift(){
						window.open("/data/design/popup/productgift.php","offlinecoupon_pop","height=570,width=590,scrollbars=yes");
					}
				</script>
				<div class="urlhongbo">
					<h3>홍보적립금혜택</h3>
					<h4>전용 홍보URL로 알리기</h4>
				</div>
			</div>

		</td>
	</tr>
	<!--
	<tr>
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="17" align="left"><IMG SRC="../images/design/pop_view_head.gif" WIDTH=17 HEIGHT=44 ALT=""></td>
					<td background="../images/design/pop_view_headbg.gif"><IMG SRC="../images/design/detail_pop_email_title.gif" WIDTH="164" HEIGHT=44 ALT=""></td>
					<td width="47" align="right"><a href="javascript:window.close();"><IMG SRC="../images/design/pop_view_exit.gif" WIDTH=47 HEIGHT=44 ALT=""></a></td>
				</tr>
			</table>
		</td>
	</tr>
	-->
	<tr>
		<td style="border:5px solid #f3f4f6; padding:40px 0px;">
			<table cellpadding="0" cellspacing="0" align="center" width="85%" border="0" style="margin:0 auto;">
				<tr>
					<td><IMG SRC="../images/design/detail_pop_email_text.gif" ALT=""></td>
				</tr>
				<tr><td height="20"></td></tr>
				<tr>
					<td class="table01_con"><?=$sAddRecom?></td>
				</tr>
				<tr><td height="30"></td></tr>
				<tr>
					<td><IMG SRC="../images/design/detail_pop_email_line.gif" WIDTH=100% HEIGHT=1 ALT=""></td>
				</tr>
				<tr><td height="20"></td></tr>
				<tr>
					<td>
						<table cellpadding="2" cellspacing="0" width="100%">
							<tr>
								<td width="120"><IMG SRC="../images/design/detail_pop_email_img01.gif" WIDTH=95 HEIGHT=95 ALT=""></td>
								<td class="table01_con">아래 회원님의 전용 홍보<b>URL주소</b>부분을 클릭하여 복사해 주세요!<br>복사된 전용<b>URL</b>을 통해 지인이 쇼핑몰 회원이 될 수 있도록 <b><font color="#E6B044">페이스북, 트위터, 카카오톡, 카카오스토리, 카페, 블로그 및 지인의 메일과 휴대폰</font></b>등으로 쇼핑몰을 추천해주세요.</td>
							</tr>
							<tr>
								<td width="120"><IMG SRC="../images/design/detail_pop_email_img02.gif" WIDTH=95 HEIGHT=95 ALT=""></td>
								<td class="table01_con"><?=$sAddRecom2?><br />(회원님의 <b>URL주소</b>는 마이페이지에서도 확인 가능하십니다.)</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td height="20"></td></tr>

				<? if(_empty($_ShopInfo->getMemid())){ ?>
				<tr>
					<td><a href="javascript:nologin();"><img src="/images/design/urlhongbo_image.gif" border="0" alt="" /></a></td>
				</tr>

				<? }else{ ?>
				<tr>
					<td>
						<table cellpadding="0" cellspacing="1" width="100%" bgcolor="#ECECEC">
							<tr>
								<td bgcolor="#F3F3F3" class="table01_con" align="center" style="padding:10px 0px;">
									<b><font color="black"><?=$sendName?></font></b> 회원님의 전용 홍보URL주소는 <b><span style="background-color:black;"><font color="white"><?=$hongboUrl?></font> </span></b>입니다.
									<div style="margin-top:5px;"><A HREF="javascript:ClipCopy('<?=$hongboUrl?>')"><IMG SRC="../images/design/detail_pop_email_btn01.gif" WIDTH=86 HEIGHT=27 ALT="" align="absmiddle" /></a></div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td height="30"></td></tr>

				<?
					$smsCount = smsCountValue ();
					if( $smsCount > 0 AND strlen($_ShopInfo->getMemid())>0 AND $_ShopInfo->getMemid()!="deleted" ){
				?>
				<tr>
					<td>
						<script type="text/javascript">
						<!--
							function sms_urlhongbo_send () {
								if(document.form2.send1.value.length==0) {
									alert("SMS 발신자 번호를 입력하세요.");
									document.form2.send1.focus();
									return;
								}
								if(document.form2.send2.value.length==0) {
									alert("SMS 발신자 번호를 입력하세요.");
									document.form2.send2.focus();
									return;
								}
								if(document.form2.send3.value.length==0) {
									alert("SMS 발신자 번호를 입력하세요.");
									document.form2.send3.focus();
									return;
								}
								if(document.form2.cel1.value.length==0) {
									alert("SMS 수신자 번호를 입력하세요.");
									document.form2.cel1.focus();
									return;
								}
								if(document.form2.cel2.value.length==0) {
									alert("SMS 수신자 번호를 입력하세요.");
									document.form2.cel2.focus();
									return;
								}
								if(document.form2.cel3.value.length==0) {
									alert("SMS 수신자 번호를 입력하세요.");
									document.form2.cel3.focus();
									return;
								}
								document.form2.submit();
							}
						//-->
						</script>
						<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
						<input type="hidden" name="mode" value="sms_urlhongbo">
						<table cellpadding="0" cellspacing="1" width="100%" bgcolor="#ECECEC">
							<caption style="font-size:15px; font-weight:bold; letter-spacing:-1px; color:#333333; text-align:left; padding:5px 10px;">SMS로 소개하기</caption>
							<tr>
								<td bgcolor="#F3F3F3" class="table01_con" align="center" style="padding:10px 0px;">
									<b><font color="black">SMS 발신자 번호</font></b> :
									<input type="text" name="send1" size="5" maxlength="3" class="input">
									-
									<input type="text" name="send2" size="5" maxlength="4" class="input">
									-
									<input type="text" name="send3" size="5" maxlength="4" class="input">
								</td>
							</tr>
							<tr>
								<td bgcolor="#F3F3F3" class="table01_con" align="center" style="padding:10px 0px;">
									<b><font color="black">SMS 수신자 번호</font></b> :
									<input type="text" name="cel1" size="5" maxlength="3" class="input">
									-
									<input type="text" name="cel2" size="5" maxlength="4" class="input">
									-
									<input type="text" name="cel3" size="5" maxlength="4" class="input">
								</td>
							</tr>
							<tr>
								<td>
									<div style="margin:5px; text-align:center;"><A HREF="javascript:sms_urlhongbo_send();"><IMG SRC="../images/design/sms_urlhongbo_btn.gif" ALT="SMS 발송" /></a></div>
								</td>
							</tr>
						</table>
						</form>
					</td>
				</tr>
				<tr><td height="30"></td></tr>
				<? } ?>

				<? if($_data->sns_ok == "Y"){ ?>
				<tr>
					<td style="padding-bottom:20px;">
						<table cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #eeeeee;">
							<caption style="font-size:15px; font-weight:bold; letter-spacing:-1px; color:#333333; text-align:left; padding:5px 10px;">SNS(소셜 네트워크 서비스)로 소개하기</caption>
							<tr>
								<td width="89" height="20"><IMG SRC="../images/design/detail_pop_email_text01.gif" WIDTH=89 HEIGHT=24 ALT=""></td>
								<td style="padding:15px 0px;">
										<a href="javascript:goTwitter();"><img src="../images/design/icon_twitter_on.gif" width="25" height="25" border="0"></a>
									<a href="javascript:goFaceBook();"><img src="../images/design/icon_facebook_on.gif" width="25" height="25" border="0" hspace="3"></a>
									<!--<a href="javascript:goMe2Day();"><img src="../images/design/icon_me2day_on.gif" width="25" height="25" border="0"></a>
									<a href="javascript:goCyworld();"><img src="../images/design/icon_cywold_on.gif" width="25" height="25" border="0" hspace="3"></a>-->
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<? } ?>
				<tr>
					<td>
						<div>
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td width="89" height="35"><IMG SRC="../images/design/detail_pop_email_text02.gif" WIDTH=89 HEIGHT=24 ALT=""></td>
									<td><IMG SRC="../images/design/detail_pop_email_text03.gif" WIDTH=405 HEIGHT=24 ALT=""></td>
								</tr>
							</table>
						</div>
						<table cellpadding="0" width="100%" cellspacing="1" bgcolor="#ECECEC">
							<tr>
								<td bgcolor="#F3F3F3">
								<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
								<input type=hidden name=mode value="">
									<table cellpadding="0" cellspacing="10" border="0" width="100%">
										<tr>
											<td width="80"><IMG SRC="../images/design/detail_pop_email_text04.gif" WIDTH=49 HEIGHT=20 ALT=""></td>
											<td><input type="text" name="in_email" class="input" maxlength="30" size="67" style="width:98%;"></td>
											<td width="80"></td>
										</tr>
										<tr>
											<td width="80" valign="top"><IMG SRC="../images/design/detail_pop_email_text05.gif" WIDTH=49 HEIGHT=20 ALT=""></td>
											<td>
<textarea name="in_message" rows="5" class="textarea_gonggu" style="width:98%; padding:10px;">
<?=$sendName?>님께서 <?=$_data->shopname?>(<?=$hongboUrl?>)을 추천하셨어요!!
</textarea>
<!--
<?//=$sendName?>님께서 귀하께 <?//=$_data->shopname?>을 추천하셨습니다.
매일 쏟아지는 <?//=$_data->shopname?>의 혜택을 만나보세요.
<?//=$hongboUrl?>
-->
											</td>
											<td width="80"><A HREF="javascript:CheckForm()"><img src="../images/design/detail_pop_email_btn02.gif" width="80" height="80" border="0"></a></td>
										</tr>
									</table>
								</form>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<?}?>

			</table>
		</td>
	</tr>

	<tr>
		<td style="padding-top:80px;">
			<div class="snshongboinfo">
				<h4>각 상품별 SNS 홍보 적립금 혜택</h4>
				<div class="addpoint">
					상품을 소셜로 홍보하면 적립금이 차곡차곡!<br />
					<?=$sAddRecom2?>
				</div>
			</div>

			<div class="snschannel">
				<h4>홍보가능 SNS채널</h4>
				<p style="padding-bottom:40px;">
					- 페이스북, 트위터, 카카오톡(모바일전용), 카카오스토리(모바일전용)<br />
					- 나의 SNS채널 전용 홍보URL을 통해 제품이 판매되면 결제금액의 일정 %를 판매시마다 지속적으로 적립해 드리며, 구매한 지인에게도 추가적립금을 적립해 드립니다.
				</p>

				<h4>혜택절차</h4>
				<div style="padding:20px 0px;background:#f9f9f9;text-align:center;font-size:0px;"><img src="/images/common/snshongbo_image1.gif" alt="" /></div>
			</div>
		</td>
	</tr>
	<tr>
		<td style="padding-top:80px;">
			<div class="pointfaq">
				<h3>적립금관련 자주하는 질문</h3>
				<h4>SNS홍보 적립금이란</h4>
				<p>나의 SNS채널로 추천한 상품정보를 통해 지인이 상품 구매하거나 나의 홍보 URL을 통해 상품을 구입한 경우 적립되는 포인트입니다.</p>

				<h4>SNS버튼을 선택하지 않으면 홍보적립금이 주어지지 않나요?</h4>
				<p>
					URL복사하기 기능을 통해 추천이 가능합니다.<br />
					URL을 복사하신 후 사용하시는 메신저나 블로그, 카페 등 방문자들이 많은 게시판 등을 통해 상품을 추천하셔도 적립금을 지급해 드릴 수 있습니다.
				</p>

				<h4>나의 적립금 내역은 어디서 확인하나요?</h4>
				<p>
					로그인 후 상단메뉴의 '마이페이지 &gt; 적립금 메뉴에서 적립금 상세 내역을 확인하실 수 있습니다. SNS홍보적립금 외 일반 상품구매를 통한 포인트 적립 현황 및 상품평 작성을 통한 포인트 적립내역 등 형태 적립금 발생내역을 확인하실 수 있습니다.<br />
					- 추천 URL을 통해 구입 및 판매된 주문건의 취소시 기 적립되었던 포인트가 차감됩니다.
				</p>
			</div>
		</td>
	</tr>
	<tr><td height="40"></td></tr>
	<!--
	<tr>
		<td height="9" width="10"><img src="../images/design/pop_view_bottomleft.gif" width="17" height="16" border="0"></td>
		<td background="../images/design/pop_view_bottombg.gif" height="9" width="729"></td>
		<td height="9" width="11"><img src="../images/design/pop_view_bottomright.gif" width="17" height="16" border="0"></td>
	</tr>
	-->
</table>

<? include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>