<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "go-4";
$MenuCode = "gong";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

function getStringCut($strValue,$lenValue)
{
	preg_match('/^([\x00-\x7e]|.{2})*/', substr($strValue,0,$lenValue), $retrunValue);
	return $retrunValue[0];
}

$c_seq = $_POST["c_seq"];
$mode = $_POST["mode"];

$sql = "SELECT A.* , B.name, B.email, B.mobile ";
$sql .=", (SELECT productname FROM tblproduct C WHERE C.productcode=A.pcode ) productname ";
$sql .="FROM tblsnsGongguCmt A, tblmember B ";
$sql .="WHERE A.id=B.id ";
$sql .="AND c_seq = '".$c_seq."' ";
$sql .="ORDER BY c_order, regidate DESC ";

$result=mysql_query($sql,get_db_conn());
$num_rows = mysql_num_rows($result);
if($mode =="send"){
	$mailmsg = "";
	$sql2 = "SELECT A.* , B.productname, B.tinyimage, B.sellprice, B.consumerprice, C.sell_startdate, C.sell_enddate ";
	$sql2 .="FROM tblsnsGongguCmt A, tblproduct B, tblproduct_social C ";
	$sql2 .="WHERE A.pcode  =B.productcode ";
	$sql2 .="AND A.pcode  =C.pcode ";
	$sql2 .="AND seq = '".$c_seq."' ";
	$result2=mysql_query($sql2,get_db_conn());
	if($rowpr=@mysql_fetch_array($result2)){
		$productname= $rowpr["productname"];
		$tinyimage= $rowpr["tinyimage"];
		$productcode= $rowpr["productcode"];
		$discountRate = ($rowpr["consumerprice"] >0 )? 100-intval($rowpr["sellprice"]/$rowpr["consumerprice"]*100)."%":"";
		$mailmsg = "<table cellpadding=0 cellspacing=0 width=100% style='border-top:2px solid #969696'>";
		$mailmsg .= "<tr bgcolor=\"#f9f9f9\" align=\"center\">";
		$mailmsg .= "<td style=\"height:30px;color:#474747;font-weight:bold;width:45%\" colspan=\"2\">상품</td>";
		$mailmsg .= "<td style=\"color:#474747;font-weight:bold;width:40%\">공동구매 판매기간</td>";
		$mailmsg .= "<td style=\"color:#474747;font-weight:bold;width:15%\">가격</td>";
		$mailmsg .= "</tr>";
		$mailmsg .= "<tr><td height=\"1\" bgcolor=\"#e4e4e4\" colspan='4'></td></tr>";
		$mailmsg .= "<tr align=\"center\">";
		$mailmsg .= "<td width=\"15%\" style='padding:15px 0 15px 10px'><a href=\"http://".$_data->shopurl."?productcode=".$rowpr["pcode"]."\">";
		if(strlen($tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$tinyimage)) {
			$width=GetImageSize($Dir.DataDir."shopimages/product/".$tinyimage);
			if($width[0]>=80) $width[0]=80;
			else if (strlen($width[0])==0) $width[0]=80;
			$mailmsg .= "<img src=\"http://".$_data->shopurl.DataDir."shopimages/product/".$tinyimage."\" border=\"0\" width=\"".$width[0]."\" class=\"img\">";
		} else {
			$mailmsg .= "<img src=\"http://".$_data->shopurl."images/no_img.gif\" border=\"0\" WIDTH=80 class=\"img\">";
		}
		$mailmsg .= "</a></td>\n";
		$mailmsg .= "<td width=\"30%\" align=\"left\" style='padding-left:10px;line-height:18px;'>".$productname."</td>";
		$mailmsg .= "<td width=\"40%\">".date("Y-m-d H:i",$rowpr["sell_startdate"])."~".date("Y-m-d H:i",$rowpr["sell_enddate"])."</td>";
		$mailmsg .= "<td width=\"15%\" style='line-height:18px;'><span style=\"color:#ff6c00; font-weight:bold;\">".number_format($rowpr["sellprice"])."</span>원<br>할인율 <span style=\"color:#ff6c00; font-weight:bold;\">".$discountRate."</span></td>";
		$mailmsg .= "</tr>";
		$mailmsg .= "<tr><td height=\"1\" bgcolor=\"#e4e4e4\" colspan='4'></td></tr>";
		$mailmsg .= "</table>";
		$gonggulink = "http://".$_data->shopurl."?productcode=".$rowpr["pcode"];


		$smsCheck = false;
		$sqlsms = "SELECT * FROM tblsmsinfo limit 1 ";
		$resultsms= mysql_query($sqlsms,get_db_conn());
		if($rowsms=@mysql_fetch_object($resultsms)){

			$sms_id=$rowsms->id;
			$sms_authkey=$rowsms->authkey;
			$fromtel=$rowsms->return_tel;

			if($rowsms->sleep_time1!=$rowsms->sleep_time2){
				$date="0";
				$time = date("Hi");
				if($rowsms->sleep_time2<"12" && $time<=substr("0".$rowsms->sleep_time2,-2)."59") $time+=2400;
				if($rowsms->sleep_time2<"12" && $rowsms->sleep_time1>$rowsms->sleep_time2) $rowsms->sleep_time2+=24;

				if($time<substr("0".$rowsms->sleep_time1,-2)."00" || $time>=substr("0".$rowsms->sleep_time2,-2)."59"){
					if($time<substr("0".$rowsms->sleep_time1,-2)."00") $day = date("d");
					else $day=date("d")+1;
					$date = date("Y-m-d H:i:s",mktime($rowsms->sleep_time1,0,0,date("m"),$day,date("Y")));
				}
			}
			$smsproductname=str_replace("\\n"," ",str_replace("\\r","",strip_tags(str_replace("&lt;!","<!",stripslashes($productname)))));
			$smsmsg= getStringCut($smsproductname,20)."의 공동구매가 진행됩니다.";
			$smsmsg= getStringCut($_data->shopname,80-strlen($smsmsg)).$smsmsg;
			$etcmsg = "공동구매진행 알림";
			mysql_free_result($resultsms);
			$smsCheck = true ;
		}
		$chk1=0;$chk2=0;
		while($row=mysql_fetch_array($result)) {
			$requsetdate= date("Y-m-d H:i:s",$row["regidate"]);
			//sms전송
			if($smsCheck){
				if(strlen($row["mobile"]) > 0 && substr($row["etc"],0,1)==1){
					SendSMS($sms_id, $sms_authkey, $row["mobile"], "", $fromtel, $date, $smsmsg, $etcmsg);
					$chk1++;
				}
			}
			//메일전송
			if(strlen($row["email"]) > 0 && substr($row["etc"],1,1)==1){
				SendGongguMail($_data->shopname, $_data->shopurl, $mailmsg, $_data->info_email,  $row["email"], $row["name"],$requsetdate,$gonggulink);
				$chk2++;
			}
		}
		if($chk1>0 || $chk2>0){
			$sql = "UPDATE tblsnsGongguCmt SET send_chk = 'Y' WHERE c_seq='".$c_seq."'";
			mysql_query($sql,get_db_conn());
			echo "<html><head><title></title></head><body onload=\"alert('전송되었습니다.');window.close();\"></body></html>";exit;
		}else{
			echo "<html><head><title></title></head><body onload=\"alert('전송되었습니다.');window.close();\"></body></html>";exit;
		}
	}else{
		echo "<html><head><title></title></head><body onload=\"alert('상품을 먼저 공동구매 상품으로 등록하세요.');window.close();\"></body></html>";exit;
	}
}
@mysql_data_seek($result,0);
$row=mysql_fetch_array($result);

?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>공동구매 신청인</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript">
<!--
function sendNotice(){
	document.sendMsgFrm.mode.value="send";
	document.sendMsgFrm.submit();
}
//-->
</script>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 >
<TABLE WIDTH="600" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD height="31" background="images/member_mailallsend_imgbg.gif" style="color:#fff;font-weight:bold;padding:5px 10px;"> 공동구매 신청인 목록</TD>
</TR>

<TR>
	<TD style="padding:10px 5px;">
		<?if($row["send_chk"]=="Y"){?><p style="color:#f00;font-weight:bold;">알림 메세지 발송 처리됨</p><?}?>
		<span style="float:left;"><input type="button" value=" 메일, 문자 발송 " onclick="sendNotice()"></span><span style="float:right;font-size:11px;">* 총 <?=$num_rows?> 건 신청</span>
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 >
		<col width=100></col>
		<col width=200></col>
		<col width=150></col>
		<col width=150></col>
		<TR>
			<TD colspan="4" background="images/table_top_line.gif"></TD>
		</TR>
		<TR>
			<TD class="table_cell" align="center">신청인</TD>
			<TD class="table_cell" align="center">email</TD>
			<TD class="table_cell" align="center">mobile</TD>
			<TD class="table_cell" align="center">신청일시</TD>
		</TR>
		<TR>
			<TD colspan="4" background="images/table_con_line.gif"></TD>
		</TR>
<?
@mysql_data_seek($result,0);
while($row=mysql_fetch_array($result)) {
	$rcv_mobile =(substr($row["etc"],0,1)==1)? "신청":"미신청";
	$rcv_email =(substr($row["etc"],1,1)==1)? "신청":"미신청";
?>

		<TR>
			<TD class="td_con1" align=center><?=$row["id"]."(".$row["name"].")"?></TD>
			<TD class="td_con1" align=center><?=$row["email"]."(".$rcv_email.")"?></TD>
			<TD class="td_con1" align=center><?=$row["mobile"]."(".$rcv_mobile.")"?></TD>
			<TD class="td_con1" align=center><?=date("Y-m-d H:i:s",$row["regidate"])?></TD>
		</TR>
		<TR>
			<TD colspan="4" background="images/table_con_line.gif"></TD>
		</TR>
<?}?>
		</TABLE>
	</TD>
</TR>
<TR>
	<TD align=center style="padding:5x" ><a href="javascript:window.close();"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="0" border=0 hspace="2"></a></TD>
</TR>
<form name="sendMsgFrm" method="post" action="<?=$_SERVER[PHP_SELF]?>">
<input type="hidden" name="mode" value="">
<input type="hidden" name="c_seq" value="<?=$c_seq?>">
</form>
</form>
</TABLE>
</body>
</html>