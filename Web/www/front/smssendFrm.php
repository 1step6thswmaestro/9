<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	echo "<html></head><body onload=\"alert('로그인이 필요한 서비스입니다.');window.close();\"></body></html>";exit;
}
$sql = "SELECT id, authkey, return_tel, product_hongbo FROM tblsmsinfo ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)){
	$return_tel = explode("-",$row->return_tel);
	$sms_id=$row->id;
	$sms_authkey=$row->authkey;
	$product_hongbo=$row->product_hongbo;
	$use_mms=$row->use_mms;
}
mysql_free_result($result);
if(strlen($sms_id)==0 || strlen($sms_authkey)==0) {
	echo "<html><head><head><body><script>alert('SMS 설정이 되어있지않습니다.');window.close();</script></body>";
	exit;
} else {
	$smscountdata=getSmscount($sms_id, $sms_authkey);
	if(substr($smscountdata,0,2)=="OK") {
	} else {
		echo "<html><head><head><body><script>alert('SMS 설정이 되어있지않습니다.');window.close();</script></body>";
		exit;
	}
}
if(	$product_hongbo != "Y"){
	echo "<html><head><head><body><script>alert('SMS 설정이 되어있지않습니다.');window.close();</script></body>";
	exit;
}

$pcode = $_REQUEST["pcode"];
$mode = $_POST["mode"];

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

if($mode =="send"){

	$sname = $_POST["sender_name"];
	$sender_cel1 = $_POST["sender_cel1"].$_POST["sender_cel2"].$_POST["sender_cel3"];
	$receiver_cell = $_POST["receiver_cel1"].$_POST["receiver_cel2"].$_POST["receiver_cel3"];
	$message = $_POST["message"];

	$sql="SELECT * FROM tblsmsinfo WHERE product_hongbo='Y' ";
	$result=mysql_query($sql,get_db_conn());
	if($rowsms=mysql_fetch_object($result)) {
		$sms_id=$rowsms->id;
		$sms_authkey=$rowsms->authkey;
/*
		$msg_hongbo="[".strip_tags($_data->shopname)."] [NAME]님께서 [PRODUCT]를 알립니다. [URL] \n";
		$patten=array("(\[NAME\])","(\[PRODUCT\])","(\[URL\])");
		$replace=array($_ShopInfo->getMemname(), $productname,$sns_url);
		$msg_hongbo=preg_replace($patten,$replace,$msg_hongbo)." ".$message;
*/
		$msg_hongbo= strip_tags($_data->shopname)."] ".$sns_url." ".$message;
		$msg_hongbo=addslashes($msg_hongbo);
		$date=0;
		$etcmsg=$_ShopInfo->getMemid()."님의 상품홍보";
		if($rowsms->use_mms=='Y') $use_mms = 'Y';
		else $use_mms = '';
		//echo $msg_hongbo;
		$temp=SendSMS2($sms_id, $sms_authkey, $receiver_cell, "", $sender_cel1, $date, $msg_hongbo, $etcmsg, $use_mms);
	}
	mysql_free_result($result);
	$resmsg=explode("[SMS]",$temp);
	echo "<html></head><body onload=\"alert('".$resmsg[1]."');window.close();\"></body></html>";exit;

}else{
	if(strlen($_ShopInfo->getMemid())>0) {
		$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
		$result = mysql_query($sql);
		if($row = mysql_fetch_object($result)) {
			$name = $row->name;
			if (strlen($row->mobile)>0) $mobile = $row->mobile;
			$mobile=explode("-",replace_tel(check_num($mobile)));
		}
	}
?>
<HTML>
<HEAD>
<TITLE><?=$_data->shopname?> - SMS 발송</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<script type="text/javascript">
function frmCheck(){
	if(document.form1.sender_name.value.length==0) {
		alert("보내는 사람 이름을 입력하세요.");
		document.form1.sender_name.focus();
		return;
	}
	if(document.form1.sender_cel1.value.length==0) {
		alert("보내는 사람 전화번호를 입력하세요.");
		document.form1.sender_cel1.focus();
		return;
	}
	if(document.form1.sender_cel2.value.length==0) {
		alert("보내는 사람 전화번호를 입력하세요.");
		document.form1.sender_cel2.focus();
		return;
	}
	if(document.form1.sender_cel3.value.length==0) {
		alert("보내는 사람 전화번호를 입력하세요.");
		document.form1.sender_cel3.focus();
		return;
	}
	if(!IsNumeric(document.form1.sender_cel1.value)) {
		alert("보내는 사람 전화번호 입력은 숫자만 입력하세요.");
		document.form1.sender_cel1.focus();
		return;
	}
	if(!IsNumeric(document.form1.sender_cel2.value)) {
		alert("보내는 사람 전화번호 입력은 숫자만 입력하세요.");
		document.form2.sender_cel2.focus();
		return;
	}
	if(!IsNumeric(document.form1.sender_cel3.value)) {
		alert("보내는 사람 전화번호 입력은 숫자만 입력하세요.");
		document.form3.sender_cel3.focus();
		return;
	}
	if(document.form1.receiver_cel1.value.length==0) {
		alert("받는분 전화번호를 입력하세요.");
		document.form1.receiver_cel1.focus();
		return;
	}
	if(document.form1.receiver_cel2.value.length==0) {
		alert("받는분 전화번호를 입력하세요.");
		document.form1.receiver_cel2.focus();
		return;
	}
	if(document.form1.receiver_cel3.value.length==0) {
		alert("받는분 전화번호를 입력하세요.");
		document.form1.receiver_cel3.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_cel1.value)) {
		alert("받는분 전화번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_cel1.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_cel2.value)) {
		alert("받는분 전화번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_cel2.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_cel3.value)) {
		alert("받는분 전화번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_cel3.focus();
		return;
	}
	if(document.form1.message.value.length==0) {
		alert("내용을 입력하세요.");
		document.form1.message.focus();
		return;
	}
	document.form1.mode.value ="send";
	document.form1.submit();

}

function CheckStrLen(maxlen,field,pos) {
	var fil_str = field.value;
	var fil_len = 0;
	fil_len =  field.value.length;
	//alert(this.value);
	if(pos =='top')
		$j("#cmtByte").html(fil_len);
	if (fil_len > maxlen ) {
	   alert("총 " + maxlen + "자 까지 저장 가능합니다.");
	   field.value = fil_str.substr(0,maxlen);
	   return;
	}
}
</script>
</head>
<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
<table cellpadding="0" cellspacing="0" width="420">
<form name="form1" method="post" action="" >
<input type="hidden" name="mode" value="">
<input type="hidden" name="pcode" value="<?=$pcode?>">
	<tr>
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="17" align="left"><IMG SRC="../images/design/pop_view_head.gif" WIDTH=17 HEIGHT=44 ALT=""></td>
					<td background="../images/design/pop_view_headbg.gif"><IMG SRC="../images/design/popphone_title.gif" ALT=""></td>
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
                    <td width="106" height="25"><IMG SRC="../images/design/popphone_text_name.gif" WIDTH=106 HEIGHT=19 ALT=""></td>
                    <td width="26" height="24"><IMG SRC="../images/design/line_02.gif" WIDTH=26 HEIGHT=19 ALT=""></td>
                    <td width="60%" height="24"><input type="text" name="sender_name" value="<?=$_ShopInfo->getMemname()?>" maxlength="15" size="20" class="input"></td>
                </tr>
                <tr>
                    <td width="106" height="25"><IMG SRC="../images/design/popphone_text_phone1.gif" WIDTH=106 HEIGHT=19 ALT=""></td>
                    <td width="26" height="24"><IMG SRC="../images/design/line_02.gif" WIDTH=26 HEIGHT=19 ALT=""></td>
                    <td height="24"><input type="text" name="sender_cel1" size="5"  maxlength="3" value="<?=$mobile[0]?>" class="input">-<input type="text" name="sender_cel2" size="5" maxlength="4" value="<?=$mobile[1]?>" class="input">-<input type="text" name="sender_cel3" size="5"  maxlength="4" value="<?=$mobile[2]?>" class="input"></td>
                </tr>
                <tr>
                    <td width="106" height="25"><IMG SRC="../images/design/popphone_text_phone2.gif" WIDTH=106 HEIGHT=19 ALT=""></td>
                    <td width="26" height="24"><IMG SRC="../images/design/line_02.gif" WIDTH=26 HEIGHT=19 ALT=""></td>
                    <td height="24"><input type="text" name="receiver_cel1" size="5" maxlength="3" class="input">-<input type="text" name="receiver_cel2" size="5" maxlength="4" class="input">-<input type="text" name="receiver_cel3" size="5" maxlength="4" class="input"></td>
                </tr>
                <tr>
                    <td height="15" colspan="3"></td>
                </tr>
                <tr>
                    <td colspan="3" height="25"><IMG SRC="../images/design/popphone_text_memo.gif" WIDTH=106 HEIGHT=19 ALT=""></td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-top:10px;"><span style="font-size:11px;"><strong>자동첨부내용</strong> : <?=strip_tags($_data->shopname)?>]<a href='<?=$sns_url?>' target='_BLANK'><?=$sns_url?></a></span></td>
                </tr>
                <tr>
                    <td colspan="3"><TEXTAREA rows=3 cols="50" name="message" class="textarea1" <?=($use_mms !="Y")? "onChange=\"CheckStrLen('20',this);\" onKeyUp=\"CheckStrLen('20',this);\"":""?> ></TEXTAREA></td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-top:10px;"><span style="font-size:11px;">SMS 발송내용은 쇼핑몰명과 URL이 포함되어 최대 20자까지 가능합니다.</span></td>
                </tr>
                <tr>
                    <td colspan="3" align="center" style="padding-top:10px;"><IMG SRC="../images/design/btn_rumor.gif" ALT="" vspace="5" onclick="frmCheck()" style="cursor:pointer;"></td>
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
</body>
</html>
<?}?>