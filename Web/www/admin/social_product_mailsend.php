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

$pcode = $_POST["pcode"];
$mode = $_POST["mode"];
if($mode =="send"){
	$send_prdt = $_POST["send_prdt"];
	$arSendprdt = explode(",",substr($send_prdt,0,-1));
	if(count($arSendprdt)>0){
		set_time_limit(7200);
		$cnt=0;
		$sql = "SELECT email FROM tblsocial_mailing  WHERE  state ='Y' AND email!='' ";
		$result = mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_array($result)) {
			//메일전송
			if(strlen($row["email"]) > 0){
				$sendMsg = SendGongguMail2($_data->shopname, $_data->shopurl, $arSendprdt, $_data->info_email, $row["email"]);
			}
			$email_list.=",".$row["email"];
			$cnt++;
		}
		if($cnt>0){
			$toemaillist=substr($email_list,1);
			$maildate = date("YmdHis");

			$sql = "INSERT tblgonggumail SET ";
			$sql.= "date		= '".$maildate."', ";
			$sql.= "sendcnt		= '".$cnt."', ";
			$sql.= "toemail		= '".$toemaillist."', ";
			$sql.= "body		= '".addslashes($sendMsg)."' ";
			mysql_query($sql,get_db_conn());

			echo "<html><head><title></title></head><body onload=\"alert('메일이 전송되었습니다.');window.close();\"></body></html>";exit;
		}else{
			echo "<html><head><title></title></head><body onload=\"alert('전송할 메일이 없습니다.');window.close();\"></body></html>";exit;
		}
	}
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>공동구매 메일알림</title>
<script type="text/javascript">
<!--
function sendNotice(){
	document.sendMsgFrm.send_prdt.value="";
	var numlength =document.sendMsgFrm.sel_prdt.length;
	if (typeof document.sendMsgFrm.sel_prdt.length == 'undefined') {
		if(document.sendMsgFrm.sel_prdt.checked==true) {
			document.sendMsgFrm.send_prdt.value+=document.sendMsgFrm.sel_prdt.value + ",";
		}
	}else{
		for(i=0;i<numlength;i++) {
			if(document.sendMsgFrm.sel_prdt[i].checked==true) {
				document.sendMsgFrm.send_prdt.value+=document.sendMsgFrm.sel_prdt[i].value + ",";
			}
		}
	}
	if(document.sendMsgFrm.send_prdt.value.length==0) {
		alert("선택하신 목록이 없습니다.");
	}else{
		document.sendMsgFrm.mode.value="send";
		document.sendMsgFrm.submit();
	}
}
//-->
</script>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 >
<form name="sendMsgFrm" method="post" action="">
<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<tr>
	<td height="35" background="images/blueline_bg.gif"><b><span class="font_blue" style="padding-left:10px;float:left">공동구매 메일 알림</span></b><span style="float:right"><a href="javascript:window.close();"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="0" border=0 hspace="2"></a></span></td>
</tr>
<TR>
	<TD style="padding:10px 5px;border:1px solid #ddd ;">
<?
	if(strlen($pcode)>0){
		$mailmsg = "";
		$sql = "SELECT * FROM tblproduct A, tblproduct_social B ";
		$sql .="WHERE A.productcode=B.pcode ";
		$sql .="AND productcode = '".$pcode."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=@mysql_fetch_array($result)){
			$productname= $row["productname"];
			$maximage= $row["maximage"];
			$productcode= $row["productcode"];
			if(strlen($maximage)>0 && file_exists($Dir.DataDir."shopimages/product/".$maximage)) {
				$maximage = "<img src='http://".$_data->shopurl.DataDir."shopimages/product/".$maximage."' width=488 height=\"294\" style=\"border-bottom:1px solid #e5e5e5; border-left:1px solid #e5e5e5; border-top:1px solid #e5e5e5;\">";
			} else {
				$maximage = "<img src=\"http://".$_data->shopurl."images/no_img.gif\" width=488 height=\"294\"  style=\"border-bottom:1px solid #e5e5e5; border-left:1px solid #e5e5e5; border-top:1px solid #e5e5e5;\" >";
			}
			$discountRate = ($row["consumerprice"] >0 )? 100-intval($row["sellprice"]/$row["consumerprice"]*100)."%":"";

			echo gongguPrdt($maximage,$row["sell_enddate"],$row["consumerprice"],$row["sellprice"],$discountRate,$productcode, $_data->shopurl);
			echo "<input type=\"checkbox\" name=\"sel_prdt\" value=\"".$productcode."\" checked style=\"display:none;\">";
			echo "<input type=hidden name=\"send_prdt\" value=\"".$pcode."\">";
		}
	}else{
		$today=time();
		$sCondition = "AND P.productcode = S.pcode ";
		$sCondition.= "AND display='Y' ";
		$sCondition.= "AND sell_startdate <= '".$today."' AND sell_enddate > '".$today."' AND (quantity is null OR quantity <> 0 ) ";

		$sql = "SELECT * FROM tblproduct P, tblproduct_social S ";
		$sql.= "WHERE 1=1 ".$sCondition;
		$sql.= "ORDER BY sell_enddate ASC LIMIT 10 ";

		$result = mysql_query($sql,get_db_conn());

		$i=0;
		echo "		  <table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		echo "<tr><td colspan=2 style=\"padding:10px 5px;color:#ff0000;font-weight:bold;\">* 메일로 전송할 상품을 선택해주세요.</td></tr>\n";
		while($row=mysql_fetch_array($result)) {
			$productname= $row["productname"];
			$maximage= $row["maximage"];
			$productcode= $row["productcode"];
			if(strlen($maximage)>0 && file_exists($Dir.DataDir."shopimages/product/".$maximage)) {
				$maximage = "<img src='http://".$_data->shopurl.DataDir."shopimages/product/".$maximage."' width=488 height=\"294\" style=\"border-bottom:1px solid #e5e5e5; border-left:1px solid #e5e5e5; border-top:1px solid #e5e5e5;\">";
			} else {
				$maximage = "<img src=\"http://".$_data->shopurl."images/no_img.gif\" width=488 height=\"294\"  style=\"border-bottom:1px solid #e5e5e5; border-left:1px solid #e5e5e5; border-top:1px solid #e5e5e5;\" >";
			}
			$discountRate = ($row["consumerprice"] >0 )? 100-intval($row["sellprice"]/$row["consumerprice"]*100)."%":"";

			echo "		  <tr>
			<td><input type=\"checkbox\" name=\"sel_prdt\" value=\"".$productcode."\"></td>
			<td align=\"right\">".gongguPrdt($maximage,$row["sell_enddate"],$row["consumerprice"],$row["sellprice"],$discountRate,$productcode, $_data->shopurl)."</td>
		  </tr>
		  <tr>
			<td colspan='2' height=\"30\">&nbsp;</td>
		  </tr>
			  ";
		}
		echo "		  </table>";
		echo "<input type=hidden name=\"send_prdt\" value=\"\">";
	}
?>

	</TD>
</TR>
<TR>
	<TD align=center style="border:none;padding-top:20px;" ><a href="javascript:sendNotice()"><img src="images/btn_mailsend.gif" width="124" height="38" border="0"></a></TD>
</TR>
</TABLE>
<input type=hidden name="mode" value="">
</form>
</body>
</html>