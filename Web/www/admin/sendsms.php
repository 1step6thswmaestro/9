<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>window.close();</script>";
	exit;
}

$sql = "SELECT id, authkey, return_tel FROM tblsmsinfo ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)){
	$sms_id=$row->id;
	$sms_authkey=$row->authkey;
	$return_tel = explode("-",$row->return_tel);
} else {
	echo "</head><body onload=\"alert('SMS 기본환경 설정 후 이용하실 수 있습니다.');window.close();\"></body></html>";exit;
}
mysql_free_result($result);


$type=$_POST["type"];
$number=$_POST["number"];
$message=$_POST["message"];
$ordercodes=$_POST["ordercodes"];

$up_message=$_POST["up_message"];	//메세지
$tel=$_POST["tel"];		//전화번호
$split_gbn=$_POST["split_gbn"];		//메세지가 길 경우 나누어 보내는지 구분 (N/Y)
$totellist=$_POST["totellist"];
$tonamelist=$_POST["tonamelist"];

$isdisabled="1";
$maxcount=0;
if(strlen($sms_id)==0 || strlen($sms_authkey)==0) {
	echo "</head><body onload=\"alert('SMS머니 충전을 하셔야 이용이 가능합니다.');window.close();\"></body></html>";exit;
	$isdisabled="0";
} else {
	$smscountdata=getSmscount($sms_id, $sms_authkey);
	if(substr($smscountdata,0,2)=="OK") {
		$maxcount=substr($smscountdata,3);
	} else if(substr($smscountdata,0,2)=="NO") {
		echo "</head><body onload=\"alert('SMS 회원 아이디가 존재하지 않습니다.\\n\\nSMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하신 후 이용하시기 바랍니다.');window.close();\"></body></html>";exit;
		$isdisabled="2";
	} else if(substr($smscountdata,0,2)=="AK") {
		echo "</head><body onload=\"alert('SMS 회원 인증키가 일치하지 않습니다.\\n\\nSMS 기본환경 설정에서 인증키를 정확히 입력하신 후 이용하시기 바랍니다.');window.close();\"></body></html>";exit;
		$isdisabled="3";
	} else {
		echo "</head><body onload=\"alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.');window.close();\"></body></html>";exit;
		$isdisabled="4";
	}
}

if($maxcount<=0) {
	echo "</head><body onload=\"alert('SMS머니 충전을 하셔야 이용이 가능합니다.');window.close();\"></body></html>";exit;
}

$fromtel=$return_tel[0]."-".$return_tel[1]."-".$return_tel[2];
$date=0;
if($type=="send") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER[PHP_SELF]);
	#######################################################################

	if(($tel=check_mobile_head($tel))!=0) {
		$etcmsg="개별 메세지 전송";
		if(strlen($up_message)>80 && $split_gbn=="Y"){
			while($up_message!=$tmpmsg && strlen($up_message)>0){
				$tmpmsg=msg_cut(80,$up_message);
				$temp=SendSMS($sms_id, $sms_authkey, $tel, "", $fromtel, $date, $tmpmsg, $etcmsg);
				$up_message=substr($up_message,strlen($tmpmsg));
			}
		} else {
			$temp=SendSMS($sms_id, $sms_authkey, $tel, "", $fromtel, $date, $up_message, $etcmsg);
		}
		$resmsg=explode("[SMS]",$temp);
		$onload = "<script>alert('".$resmsg[1]."');</script>";
	} else {
		echo "</head><body onload=\"alert('잘못된 전화번호입니다.');window.close();\"></body></html>";exit;
	}
} else if($type=="allsend") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER[PHP_SELF]);
	#######################################################################

	$etcmsg="주문서 개별 메세지 전송";
	if(strlen($up_message)>80 && $split_gbn=="Y"){
		while($up_message!=$tmpmsg && strlen($up_message)>0){
			$tmpmsg=msg_cut(80,$up_message);
			$temp=SendSMS($sms_id, $sms_authkey, $totellist, $tonamelist, $fromtel, 0, $tmpmsg, $etcmsg);
			$up_message=substr($up_message,strlen($tmpmsg));
		}
	} else {
		$temp=SendSMS($sms_id, $sms_authkey, $totellist, $tonamelist, $fromtel, 0, $up_message, $etcmsg);
	}
	$resmsg=explode("[SMS]",$temp);
	$onload = "<script>alert('".$resmsg[1]."');</script>";
}

unset($arrtel);
if(strlen($number)!=0) {
	$arrtel=explode("|",$number);
	$telcnt=0;
	for($i=0;$i<count($arrtel);$i++){
		$arrtel[$i]=check_mobile_head($arrtel[$i]);
		if(strlen($arrtel[$i])!=0) $telcnt++;
	}
} else if($type=="order") {
	$telcnt=0;
	$ordercodes=ereg_replace("\\\\","",substr($ordercodes,0,-1));
	$sql = "SELECT sender_tel,sender_name FROM tblorderinfo WHERE ordercode IN (".$ordercodes.")";
	$result = mysql_query($sql,get_db_conn());
	while($row = mysql_fetch_object($result)){
		$sender_tel=check_mobile_head($row->sender_tel);
		if(strlen($sender_tel)!=0){
			$ok="no";
			for($i=0;$i<$telcnt;$i++){
				if($sender_tel==$arrtel[$i]) $ok="yes";
			}
			if($ok=="no"){
				$sender[$telcnt]=$row->sender_name;
				$arrtel[$telcnt++]=$sender_tel;
			}
		}
	}
}

function msg_cut($len_title,$title) {
	$trim_len=strlen(substr($title,0,$len_title));
	if (strlen($title) > $trim_len){
		for($jj=0;$jj < $trim_len;$jj++) {
			$uu=ord(substr($title, $jj, 1));
			if( $uu > 127 ){
				$jj++;
			}
		}
		$n_title=substr($title,0,$jj);
	} else {
		$n_title = $title;
	}
	return $n_title;
}

?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>SMS 발송</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="calendar.js.php"></script>
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

function cal_pre2() {
	var strcnt,obj_msg,obj_len;
	var reserve=0;

	obj_msg = document.form1.up_message;
	obj_len = document.form1.text_length;

	strcnt = cal_byte2(obj_msg.value);
	obj_len.value=strcnt;
}

function cal_byte2(aquery) {
	var tmpStr;
	var temp = 0;
	var onechar;
	var tcount = 0;
	var reserve = 0;

	tmpStr = new String(aquery);
	temp = tmpStr.length;

	for(k=0; k<temp; k++) {
		onechar = tmpStr.charAt(k);
		if(escape(onechar).length > 4) {
			tcount += 2;
		} else {
			tcount ++;
		}
	}
	return tcount;
}


function CheckForm() {
	if(document.form1.up_message.value.length==0) {
		alert("메세지를 입력하세요.");
		document.form1.up_message.focus();
		return;
	}
	if(document.form1.tel.value.length==0) {
		alert("전화번호를 입력하세요.");
		document.form1.tel.focus();
		return;
	}
	msglen=document.form1.text_length.value;
	if(msglen>80 && confirm("해당 메세지를 "+Math.ceil(msglen/80)+"번에 걸쳐 나누어 발송하시겠습니까?")) {
		document.form1.split_gbn.value="Y";
	} else if(msglen>80) {
 		reserve = msglen - 80;
		alert('메시지 내용은 80바이트를 넘을수 없습니다.\n\n작성하신 메세지 내용은 '+ reserve +'byte가 초과되었습니다.');
		document.form1.up_message.focus();
		return;
	}
	if(confirm("메세지를 발송하시겠습니까?")) {
<?if($type=="order"){?>
		document.form1.type.value="allsend";
		document.form1.submit();
<?}else {?>
		document.form1.type.value="send";
		document.form1.submit();
<?}?>
	}
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="220" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD height="31" background="images/win_titlebg1.gif">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="525">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="28">&nbsp;</td>
			<td><b><font color="white">SMS발송</b></font></td>
		</tr>
		</table>
		</td>
		<td width="9"><img src="images/win_titlebg1_end.gif" width="12" height="31" border="0"></td>
	</tr>
	</table>
	</TD>
</TR>
<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=type>
<input type=hidden name=split_gbn value="N">
<TR>
	<TD style="padding:3pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="232">
		<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
		<TR>
			<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
		</TR>
		<TR>
			<TD height="90" background="images/sms_bg.gif" valign="top" align=center><TEXTAREA class="textarea_hide" onkeyup="cal_pre2();" onchange="cal_pre2();" name=up_message rows=5 cols=26><? if($type=="order") echo "[NAME]고객님"; else if($type=="sendfail") echo stripslashes($message);?></TEXTAREA></td>
		</TR>
		<TR>
			<TD height="26" background="images/sms_down_01.gif" align=center><input type="text" name="text_length" value="0" style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus="this.blur();" class="input_hide"> bytes (최대 80 Bytes)<script>cal_pre2()</script></TD>
		</TR>
		<TR>
			<TD HEIGHT=6></TD>
		</TR>
		<TR>
			<TD>
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<TR>
				<TD colspan=2 background="images/table_top_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell" width="35"><img src="images/icon_point2.gif" width="8" height="11" border="0">달력</TD>
				<TD class="td_con1">
					<input type="button" value="날자 입력" onclick="Calendar(up_message);">
				</TD>
			</TR>
			<TR>
				<TD colspan=2 background="images/table_top_line.gif"></TD>
			</TR>
<? if($type=="order"){?>
			<TR>
				<TD class="table_cell" width="35"><img src="images/icon_point2.gif" width="8" height="11" border="0">번호</TD>
				<TD class="td_con1">
					<select name=tel width="141" class=select>
<?
			for($i=0;$i<count($arrtel);$i++) {
				if(strlen($arrtel[$i])!=0 && $arrtel[$i]!=0) {
					echo "<option value=".$arrtel[$i].">".$arrtel[$i]."</option>";
					$totellist.=",".ereg_replace(",","",$arrtel[$i]);
					$tonamelist.=",".ereg_replace(",","",$sender[$i]);
				}
			}
?>
					</select>
				</TD>
			</TR>
			<input type=hidden name=totellist value="<?=$totellist?>">
			<input type=hidden name=tonamelist value="<?=$tonamelist?>">
<? } else { ?>
			<TR>
				<TD class="table_cell" width="35"><img src="images/icon_point2.gif" width="8" height="11" border="0">번호</TD>
				<TD class="td_con1">
			<?if($telcnt==0) {?>
					<input type=text name=tel size=15 maxlength=15 class=input>
			<?} else {?>
					<select name=tel width="141" class=select>
<?
			for($i=0;$i<count($arrtel);$i++) {
				if(strlen($arrtel[$i])!=0 && $arrtel[$i]!=0) {
					echo "<option value=".$arrtel[$i].">".$arrtel[$i]."</option>";
					$totellist.=",".ereg_replace(",","",$arrtel[$i]);
					$tonamelist.=",".ereg_replace(",","",$sender[$i]);
				}
			}
?>
					</select>
			<?}?>
				</TD>
			</TR>
<? } ?>
			<TR>
				<TD colspan=2 background="images/table_top_line.gif"></TD>
			</TR>
			</TABLE>
			</TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD align=center><a href="javascript:CheckForm();"><img src="images/btn_send1.gif" width="36" height="18" border="0" vspace="2" border=0></a>&nbsp;&nbsp;<a href="javascript:window.close();"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="2" border=0 hspace="1"></a></TD>
</TR>
</form>
</TABLE>
<?=$onload?>

</body>
</html>