<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($ordercode)==0) $ordercode=$_REQUEST["ordercode"];
$deli_gbn=$_REQUEST["deli_gbn"];

if(strlen($ordercode)>0) {

	mysql_query("INSERT INTO tblorderinfo SELECT * FROM tblorderinfotemp WHERE ordercode='".$ordercode."'",get_db_conn());
	if(!mysql_errno()) {
		mysql_query("DELETE FROM tblorderinfotemp WHERE ordercode='".$ordercode."'",get_db_conn());
	} else {
		$okmail="YES";
	}

	mysql_query("INSERT INTO tblorderproduct SELECT * FROM tblorderproducttemp WHERE ordercode='".$ordercode."'",get_db_conn());
	if(!mysql_errno()) {
		mysql_query("DELETE FROM tblorderproducttemp WHERE ordercode='".$ordercode."'",get_db_conn());
	}

	mysql_query("INSERT INTO tblorderoption SELECT * FROM tblorderoptiontemp WHERE ordercode='".$ordercode."'",get_db_conn());
	if(!mysql_errno()) {
		mysql_query("DELETE FROM tblorderoptiontemp WHERE ordercode='".$ordercode."'",get_db_conn());
	}
}



	//상품 구매 완료 시간 등록
	$orderproductSQL = "SELECT `productcode` FROM `tblorderproduct` WHERE `ordercode` = '".$ordercode."' ";
	$orderproductResult=mysql_query($orderproductSQL,get_db_conn());
	while($orderproductROW=mysql_fetch_object($orderproductResult)) {

		$selldateSQL= "UPDATE `tblproduct` SET `selldate` = NOW() WHERE `productcode` = '".$orderproductROW->productcode."' LIMIT 1; ";
		mysql_query($selldateSQL,get_db_conn());
	}




$sql="SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$paymethod=substr($row->paymethod,0,1);
	$pay_flag=$row->pay_flag;
	$pay_flag_check=$row->pay_flag;
	$pay_auth_no=$row->pay_auth_no;
	$bank_date=$row->bank_date;
	$deli_flag=$row->deli_gbn;
	$user_reserve=$row->reserve;
	$last_price=$row->price;
	$pay_data=$row->pay_data;
	$delflag=$row->del_gbn;
	$sender_name=$row->sender_name;
	$sender_email=$row->sender_email;
	$sender_tel=$row->sender_tel;
	$gift = $row->gift;
}
mysql_free_result($result);

if (preg_match("/^(V|O|Q|C|P|M)$/", $paymethod) && $deli_gbn=="C") {
	$pay_data = "결제 중 주문취소";
}

if(preg_match("/^(V|O|Q|C|P|M)$/", $paymethod) && $last_price>0) {
	if(strlen($_ShopInfo->getOkpayment())==0) {
		$_ShopInfo->setOkpayment("result");
		$_ShopInfo->Save();
		$_ShopInfo->setOkpayment("");
	}
}

//카드실패시 장바구니 복구
if (preg_match("/^(V|O|Q|C|P|M)$/", $paymethod) && $pay_flag!="0000") {
	mysql_query("UPDATE tblbasket2 SET tempkey='".$_ShopInfo->getTempkey()."' WHERE tempkey='".$_ShopInfo->getGifttempkey()."'",get_db_conn());
} else {
	mysql_query("DELETE FROM tblbasket2 WHERE tempkey='".$_ShopInfo->getGifttempkey()."'",get_db_conn());
}

//새로고침 방지
if ($paymethod!="B" && $_ShopInfo->getOkpayment()=="result") {
	echo "<html></head><body onload=\"location.href='".$Dir.MainDir."main.php'\"></body></html>";
	exit;
}


//결제성공 처리
if (($paymethod=="B" || (preg_match("/^(V|O|Q|C|P|M)$/", $paymethod) && strcmp($pay_flag,"0000")==0)) && $okmail!="YES") {
	$thankmsg="<hr size=1 width=100%>\n";
	if (strlen($_data->orderend_msg)>0) {
		$orderend_msg=ereg_replace("\n","<br>",$orderend_msg);
		$thankmsg.="<table cellpadding=0 cellspacing=0 border=0 width=100%>\n";
		$thankmsg.="<tr><td align=center>\n";
		$thankmsg.=ereg_replace("\"","",$orderend_msg);
		$thankmsg.="</td></tr>\n";
		$thankmsg.="</table>\n";
	} else {
		$thankmsg.="<br><h3>구매해주셔서 감사합니다!</h3><br>";
	}

	if (strlen($sender_email)>0) $oksendmail="Y"; //메일이 있으면 주문메일 발송
	if (strlen($_data->info_email)>0) $okadminmail="Y"; //쇼핑몰 메일이 있으면 해당 주문내역서를 발송

	//관리자/입점업체/고객 주문완료 메일 발송
	SendOrderMail($_data->shopname, $_ShopInfo->getShopurl(), $_data->design_mail, $_data->info_email, $ordercode, $okadminmail, $oksendmail, $thankmsg);
	$arpay=array("B"=>"현금","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");

	//주문완료시 회원/관리자/입금내역을 sms로 발송함
	$sqlsms = "SELECT * FROM tblsmsinfo WHERE (mem_order='Y' OR admin_order='Y' ";
	if($paymethod=="B") $sqlsms.="OR mem_bank='Y' ";
	$sqlsms.=")";
	$resultsms= mysql_query($sqlsms,get_db_conn());
	if($rowsms=mysql_fetch_object($resultsms)){
		if(strlen($ordercode)>0) {
			$sms_id=$rowsms->id;
			$sms_authkey=$rowsms->authkey;

			$admin_order=$rowsms->admin_order;
			$mem_order=$rowsms->mem_order;
			$mem_bank=$rowsms->mem_bank;
			$totellist=$rowsms->admin_tel;
			if(strlen($rowsms->subadmin1_tel)>8) $totellist.=",".$rowsms->subadmin1_tel;
			if(strlen($rowsms->subadmin2_tel)>8) $totellist.=",".$rowsms->subadmin2_tel;
			if(strlen($rowsms->subadmin3_tel)>8) $totellist.=",".$rowsms->subadmin3_tel;
			$fromtel=$rowsms->return_tel;

			$msg_mem_order=$rowsms->msg_mem_order;
			$msg_mem_bank=$rowsms->msg_mem_bank;
			if(strlen($msg_mem_bank)==0) $msg_mem_bank="[NAME]님! [PRICE]원 [ACCOUNT] 입금바랍니다. [".$_data->shopname."]";
			$patten=array("(\[NAME\])","(\[PRODUCT\])");
			$replace=array($sender_name,substr($smsproductname,1));
			$msg_mem_order=preg_replace($patten,$replace,$msg_mem_order);
			$msg_mem_order=AddSlashes($msg_mem_order);
			//$smsmsg=$sender_name."님이 ".substr($smsproductname,1)."를 ".$arpay[$paymethod]." 구입하셨습니다.";
			$patten=array("(\[NAME\])","(\[PRICE\])","(\[ACCOUNT\])");
			$replace=array($sender_name,number_format($last_price),$pay_data);
			$msg_mem_bank=preg_replace($patten,$replace,$msg_mem_bank);

			$patten=array("(\[NAME\])","(\[PRODUCT\])","(\[PAYTYPE\])");
			$replace=array($sender_name,substr($smsproductname,1), $arpay[$paymethod]);
			$smsmsg=preg_replace($patten,$replace,$pr_buy_msg);
			$smsmsg=addslashes($smsmsg);

			mysql_free_result($resultsms);
			$etcmsg="상품주문 안내메세지(회원)";
			$date="0";
			if($mem_order=="Y") {
				$temp=SendSMS($sms_id, $sms_authkey, $sender_tel, "", $fromtel, $date, $msg_mem_order, $etcmsg);
			}
			$etcmsg="무통장입금 안내메세지(회원)";
			if(preg_match("/^(B|O|Q)$/", $paymethod) && $mem_bank=="Y") {
				$temp=SendSMS($sms_id, $sms_authkey, $sender_tel, "", $fromtel, $date, $msg_mem_bank, $etcmsg);
			}
			$etcmsg="상품주문 안내메세지(관리자)";
			if($admin_order=="Y" && $rowsms->sleep_time1!=$rowsms->sleep_time2){
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

			if($admin_order=="Y") {
				$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, $smsmsg, $etcmsg);
			}
		}
	}

	if(preg_match("/^(O|Q|C|P|M)$/", $paymethod) && strcmp($pay_flag,"0000")==0){
		$sql = "SELECT productcode,productname FROM tblorderproduct WHERE ordercode='".$ordercode."'";
		$result = mysql_query($sql,get_db_conn());
		$tmps = mysql_fetch_array($result);
		mysql_free_result($result);

		$sql = "SELECT consumerprice FROM tblproduct WHERE productcode='{$tmps['productcode']}'";
		$result = mysql_query($sql,get_db_conn());
		$save_price = mysql_fetch_array($result);
		$save_price = $save_price['consumerprice'];
		mysql_free_result($result);

		if($gift=='2') {
			$sql="UPDATE tblorderinfo SET deli_gbn='Y' WHERE ordercode='".$ordercode."'";
			mysql_query($sql,get_db_conn());
			mysql_query("UPDATE tblorderproduct SET deli_gbn='Y' WHERE ordercode='".$ordercode."' ",get_db_conn());

			// 상품권 적립금 적립

			$sql = "INSERT tblreserve SET ";
			$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
			$sql.= "reserve		= {$save_price} ,";
			$sql.= "reserve_yn	= 'Y', ";
			$sql.= "content		= '상품권구입 적립금 전환', ";
			$sql.= "orderdata	= '".$ordercode."=".$last_price."', ";
			$sql.= "date		= '".date("YmdHis")."' ";
			mysql_query($sql,get_db_conn());

			$sql = "UPDATE tblmember SET reserve = reserve + ".abs($save_price)." ";
			$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
			mysql_query($sql,get_db_conn());

		}
		else if($gift=='1') {
			$sql="UPDATE tblorderinfo SET deli_gbn='X' WHERE ordercode='".$ordercode."'";
			mysql_query($sql,get_db_conn());
			mysql_query("UPDATE tblorderproduct SET deli_gbn='X' WHERE ordercode='".$ordercode."' ",get_db_conn());

			$SID = md5(uniqid(rand()));
			$authcode1 = substr($SID, 0, 6);
			$SID = md5(uniqid(rand()));
			$authcode2 = substr($SID, 0, 6);

			$sql = "INSERT tblgift_info SET ";
			$sql.= "ordercode	= '{$ordercode}', ";
			$sql.= "send_id 	= '".$_ShopInfo->getMemid()."', ";
			$sql.= "name	= '{$tmps['productname']}', ";
			$sql.= "productcode	= '{$tmps['productcode']}', ";
			$sql.= "price	= '{$save_price}', ";
			$sql.= "authcode1	= '{$authcode1}', ";
			$sql.= "authcode2	= '{$authcode2}', ";
			$sql.= "status	= 'A', ";
			$sql.= "signdate	= '".time()."' ";
			mysql_query($sql,get_db_conn());

			$authcode = $authcode1."-".$authcode2;
			SendGiftAuthMail($_data->shopname, $_ShopInfo->getShopurl(), $_data->design_mail, $_data->info_email, $ordercode, $authcode,$save_price);

			$sql="SELECT * FROM tblsmsinfo WHERE mem_gift='Y' ";
			$result=mysql_query($sql,get_db_conn());
			if($rowsms=mysql_fetch_object($result)) {
				$sms_id=$rowsms->id;
				$sms_authkey=$rowsms->authkey;

				$sname=$_ord->sender_name;

				if(strlen($rowsms->msg_mem_gift)==0) $rowsms->msg_mem_gift="[".strip_tags($_shopdata->shopname)."] [NAME]님이 상품권을 선물하셨습니다. 인증번호는 [AUTHCODE]입니다.";
				$patten=array("(\[NAME\])","(\[AUTHCODE\])","(\[URL\])");
				$replace=array($sname,$authcode,"http://".$_ShopInfo->getShopurl());

				$msg_mem_gift=preg_replace($patten,$replace,$rowsms->msg_mem_gift);
				$msg_mem_gift=addslashes($msg_mem_gift);

				$fromtel=$rowsms->return_tel;
				$date=0;
				$etcmsg="상품권 선물하기메세지(회원)";
				if($rowsms->use_mms=='Y') $use_mms = 'Y';
				else $use_mms = '';
				$temp=SendSMS2($sms_id, $sms_authkey, $_ord->receiver_tel1, "", $fromtel, $date, $msg_mem_gift, $etcmsg, $use_mms);
			}
			mysql_free_result($result);
		}
	}
}


//주문중 주문취소 데이터 처리
if ((preg_match("/^(O|Q|C|P|M)$/", $paymethod) && strcmp($pay_flag,"0000")!=0 && strlen($ordercode)>0 && strlen($pay_auth_no)==0 && $pay_flag_check=="N" && $deli_gbn=="C" && $deli_flag=="N") ||
($paymethod=="V" && strlen($ordercode)>0 && $deli_gbn=="C" && $deli_flag=="N" && strlen($bank_date)==0) && strlen($delflag)==0)
{
	//옵션별 상품의 수량복구를 위해서 해당 주문의 옵션을 찾는다.

	$sql = "SELECT a.option_quantity,b.productcode,b.opt1_idx,b.opt2_idx,b.quantity ";
	$sql.= "FROM tblproduct a, tblbasket2 b WHERE b.tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND a.productcode=b.productcode ";
	$result = mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		if(strlen($row->option_quantity)>0){
			if(strlen($option_quantity[$row->productcode])==0) {
				$option_quantity[$row->productcode]=$row->option_quantity;
			}
			$option1num=$row->opt1_idx;
			$option2num=($row->opt2_idx>0?$row->opt2_idx:1);
			$optioncnt2 = explode(",",substr($option_quantity[$row->productcode],1));
			if($optioncnt2[($option2num-1)*10+($option1num-1)]!="") {
				$optioncnt2[($option2num-1)*10+($option1num-1)]+=$row->quantity;
			}
			$tempoption_quantity="";
			for($j=0;$j<5;$j++){
				for($i=0;$i<10;$i++){
					$tempoption_quantity.=",".$optioncnt2[$j*10+$i];
				}
			}
			if(strlen($tempoption_quantity)>0){
				$option_quantity[$row->productcode]=$tempoption_quantity.",";
			}
		}
	}
	mysql_free_result($result);

	//상품 수량 복구
	$sql = "SELECT quantity, productcode, package_idx, assemble_idx, assemble_info FROM tblorderproduct ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		// 상품DB에서 수량을 더한다.
		if(strlen($option_quantity[$row->productcode])>0) {
			$sql2 = "UPDATE tblproduct SET ";
			$sql2.= "quantity		= quantity+".$row->quantity.", ";
			$sql2.= "option_quantity='".$option_quantity[$row->productcode]."' ";
			$sql2.= "WHERE productcode='".$row->productcode."' ";
		} else {
			$sql2 = "UPDATE tblproduct SET ";
			$sql2.= "quantity		= quantity+".$row->quantity." ";
			$sql2.= "WHERE productcode='".$row->productcode."' ";
		}
		mysql_query($sql2,get_db_conn());

		if(str_replace("","",str_replace(":","",str_replace("=","",$row->assemble_info)))) {
			$assemble_infoall_exp = explode("=",$row->assemble_info);

			if($row->package_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))>0) {
				$package_info_exp = explode(":",$assemble_infoall_exp[0]);
				if(strlen($package_info_exp[0])>0) {
					$package_productcode_exp = explode("",$package_info_exp[0]);
					for($k=0; $k<count($package_productcode_exp); $k++) {
						$sql2 = "UPDATE tblproduct SET ";
						$sql2.= "quantity		= quantity+".$row->quantity." ";
						$sql2.= "WHERE productcode='".$package_productcode_exp[$k]."' ";
						mysql_query($sql2,get_db_conn());
					}
				}
			}

			if($row->assemble_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[1])))>0) {
				$assemble_info_exp = explode(":",$assemble_infoall_exp[1]);
				if(strlen($assemble_info_exp[0])>0) {
					$assemble_productcode_exp = explode("",$assemble_info_exp[0]);
					for($k=0; $k<count($assemble_productcode_exp); $k++) {
						$sql2 = "UPDATE tblproduct SET ";
						$sql2.= "quantity		= quantity+".$row->quantity." ";
						$sql2.= "WHERE productcode='".$assemble_productcode_exp[$k]."' ";
						mysql_query($sql2,get_db_conn());
					}
				}
			}
		}
	}
	mysql_free_result($result);

	// 적립금 환원
	if ($_data->reserve_maxuse>=0 && strlen($user_reserve)>0 && $user_reserve>0 && strlen($delflag)==0){
		$sql = "UPDATE tblmember SET reserve = reserve + ".abs($user_reserve)." ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
		mysql_query($sql,get_db_conn());
	}
	// 주문서의 쿠폰을 다시 사용가능하게 변경함
	$sql = "SELECT productcode FROM tblorderproduct WHERE ordercode='".$ordercode."' AND productcode LIKE 'COU%' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)){
		$coupon_code=substr($row->productcode,3,-1);
		mysql_query("UPDATE tblcouponissue SET used='N' WHERE id='".$_ShopInfo->getMemid()."' AND coupon_code='".$coupon_code."'",get_db_conn());
	}

	// 주문서 주문취소,적립금환원상태로 돌려놓음.
	$sql = "UPDATE tblorderinfo SET ";
	$sql.= "pay_data		= '고객이 결제창에서 주문취소를 하였습니다.', ";
	$sql.= "deli_gbn	= 'C', ";
	$sql.= "del_gbn		= 'R' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	$sql.= "AND paymethod='".$paymethod."' AND pay_flag='N' ";
	if(mysql_query($sql,get_db_conn())) {
		$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
		mysql_query($sql,get_db_conn());
	}

} else if(((preg_match("/^(O|Q|C|P|M)$/", $paymethod) && strcmp($pay_flag,"0000")==0 && strlen($ordercode)>0 && $deli_gbn!="C" && $deli_flag=="N") || $paymethod=="B" ||
($paymethod=="V" && strlen($ordercode)>0 && $deli_gbn!="C" && $deli_flag=="N" && strlen($bank_date)>0)) && strlen($delflag)==0)
{
	// 주문성공시 적립금차감/쿠폰 차감.
	if($_data->reserve_maxuse>=0 && strlen($user_reserve)>0 && $user_reserve>0) {
		$sql = "INSERT tblreserve SET ";
		$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
		$sql.= "reserve		= -$user_reserve, ";
		$sql.= "reserve_yn	= 'N', ";
		$sql.= "content		= '물품 주문시 적립금 사용', ";
		$sql.= "orderdata	= '".$ordercode."=".$last_price."', ";
		$sql.= "date		= '".date("YmdHis")."' ";
		mysql_query($sql,get_db_conn());
	}

	$sql="UPDATE tblorderinfo SET del_gbn='N' WHERE ordercode='".$ordercode."'";
	mysql_query($sql,get_db_conn());


	//주문 상품 품절시 관리자에서 sms 통보
	$sqlsms="SELECT * FROM tblsmsinfo WHERE admin_soldout='Y' ";
	$resultsms= mysql_query($sqlsms,get_db_conn());
	if($rowsms=mysql_fetch_object($resultsms)) {
		$sms_id=$rowsms->id;
		$sms_authkey=$rowsms->authkey;

		$totellist=$rowsms->admin_tel;
		if(strlen($rowsms->subadmin1_tel)>8) $totellist.=",".$rowsms->subadmin1_tel;
		if(strlen($rowsms->subadmin2_tel)>8) $totellist.=",".$rowsms->subadmin2_tel;
		if(strlen($rowsms->subadmin3_tel)>8) $totellist.=",".$rowsms->subadmin3_tel;
		$fromtel=$rowsms->return_tel;
		mysql_free_result($resultsms);
		$etcmsg="상품품절 알림 메세지(관리자)";
		$date="0";
		//관리자가 sms를 원하지 않는 시간 체크하여 그외시간에 보내도록 한다.
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
		$sql = "SELECT a.productname FROM tblproduct a, tblorderproduct b ";
		$sql.= "WHERE b.ordercode='".$ordercode."' ";
		$sql.= "AND a.productcode=b.productcode ";
		$sql.= "AND (a.quantity<=0 && a.quantity is NOT NULL) ";
		$result = mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)) {
			$smsmsg="[".addslashes($row->productname)."]이 ".$sender_name."님 주문에 의해서 품절되었습니다.";
			$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, $smsmsg, $etcmsg);
		}
		mysql_free_result($result);
	}
}

?>

<html>
<head>
<title>결제</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 onload="document.form1.submit()">
<form name=form1 action="<?=$Dir.FrontDir?>orderend2.php" method=post target=_parent>
<input type=hidden name=ordercode value="<?=$ordercode?>">
</form>
</body>
</html>