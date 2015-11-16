<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/admin_more.php");

INCLUDE ("access.php");
// 추천인적립금설정정보
$recom_ok=$_data->recom_ok;
$recom_memreserve=$_data->recom_memreserve;
$recom_memreserve_type=$_data->recom_memreserve_type;
$recom_addreserve=$_data->recom_addreserve;
$arRecomType = explode("",$recom_memreserve_type);
$sns_ok=$_data->sns_ok;
$sns_reserve_type=$_data->sns_reserve_type;
$sns_recomreserve=$_data->sns_recomreserve;
$sns_memreserve=$_data->sns_memreserve;
$arSnsType = explode("",$sns_reserve_type);

function getDeligbn($ordercode,$deli_gbn) {
	//N:미처리, X:배송요청, S:발송준비, Y:발송완료, C:주문취소, R:반송, D:취소요청, E:환불대기[가상계좌일 경우만]
	$sql = "SELECT deli_gbn FROM tblorderproduct WHERE ordercode='".$ordercode."' AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
	$sql.= "GROUP BY deli_gbn ";
	$result=mysql_query($sql,get_db_conn());
	$arrdeli=array();
	while($row=mysql_fetch_object($result)) {
		$arrdeli[$row->deli_gbn]=true;
	}
	mysql_free_result($result);

	$res="";
	if($deli_gbn=="N" && count($arrdeli)>0) {
		$res="N";
	} else if($deli_gbn=="S" && count($arrdeli)>0) {
		if($arrdeli["N"]==true) $res="N";
		else $res="S";
	}

	if(preg_match("/^(N|S)$/",$res)) {
		//미처리, 발송준비 까지는 입점업체에서의 상태 변경시 바로 적용
		$sql = "UPDATE tblorderinfo SET deli_gbn='".$res."' WHERE ordercode='".$ordercode."' ";
		mysql_query($sql,get_db_conn());
	}
	return $res;
}

$ordercode=$_REQUEST["ordercode"];
$type=$_POST["type"];
$mode=$_POST["mode"];
$hidedisplay=$_POST["hidedisplay"];

$order_msg=$_POST["order_msg"];

$rescode=$_POST["rescode"];
$pay_admin_proc=$_POST["pay_admin_proc"];


if($type=="sort") {
	$sort=$_POST["sort"];
}


$totalRecom=$_POST["totalRecom"];
$sell_memid=$_POST["sell_memid"];
$sell_memid_reserve=$_POST["sell_memid_reserve"];
$arSell_id =array();
$arSell_rsv =array();
if($sell_memid){
	$arSell_id = explode("||", substr($sell_memid,0,-2));
	$arSell_rsv = explode("||", substr($sell_memid_reserve,0,-2));
}

if($ordercode==NULL) {
	echo "<script>alert('잘못된 접근입니다.');window.close();</script>";
	exit;
}

$sql="SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."'";
$result=mysql_query($sql,get_db_conn());
$_ord=mysql_fetch_object($result);
mysql_free_result($result);

	if(!$_ord) {
	echo "<script>alert(\"해당 주문내역이 존재하지 않습니다.\");window.close();</script>";
	exit;
}
unset($isupdate);

//상품권일때
if($_ord->gift =='1' || $_ord->gift =='2') {
	echo "<script>window.location.href='order_detail2.php?ordercode={$ordercode}';</script>";
	exit;
}

$pgid_info="";
$pg_type="";
switch (substr($_ord->paymethod,0,1)) {
	case "B":
		break;
	case "V":
		$pgid_info=GetEscrowType($_shopdata->trans_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "O":
		$pgid_info=GetEscrowType($_shopdata->virtual_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "Q":
		$pgid_info=GetEscrowType($_shopdata->escrow_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "C":
		$pgid_info=GetEscrowType($_shopdata->card_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "P":
		$pgid_info=GetEscrowType($_shopdata->card_id);
		$pg_type=$pgid_info["PG"];
		break;
	case "M":
		$pgid_info=GetEscrowType($_shopdata->mobile_id);
		$pg_type=$pgid_info["PG"];
		break;
}
$pg_type=trim($pg_type);

$tax_type=$_shopdata->tax_type;

//카드승인/취소,  핸드폰결제 취소 처리 (결제서버에서 호출)
if (strlen($rescode)>0 && strlen($ordercode)>0) {
	$confirm_data=SendSocketPost(getenv("HTTP_HOST"),str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl())."paygate/pgconfirm.php","ordercode=".$ordercode);
	if (strlen($rescode)==1 && $rescode==$confirm_data) {
		$sql = "UPDATE tblorderinfo SET pay_admin_proc='".$rescode."' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		mysql_query($sql,get_db_conn());
		$isupdate=true;
	}
	if (strlen($rescode)==1 && $rescode==$confirm_data && $rescode=="C" && substr($_ord->paymethod,0,1)=="P") {
		$sql = "UPDATE tblorderinfo SET deli_gbn='C' ";
		$sql.= "WHERE ordercode='".$ordercode."' AND MID(paymethod,1,1)='P'";
		if(mysql_query($sql,get_db_conn())) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
			$sql.= "WHERE ordercode='".$ordercode."' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			mysql_query($sql,get_db_conn());
		}
		$isupdate=true;
	}
}

//무통장입금확인
if($type=="bank" && strlen($ordercode)>0) {
	if($_ord->paymethod=="B" && $tax_type=="Y") {
		$sql = "SELECT COUNT(*) as cnt FROM tbltaxsavelist WHERE ordercode='".$ordercode."' AND type='N' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		mysql_free_result($result);
		if($row->cnt>0) {
			$flag="Y";
			include($Dir."lib/taxsave.inc.php");
		}
	}

	mysql_query("UPDATE tblorderinfo SET bank_date='".date("YmdHis")."' WHERE ordercode='".$ordercode."' ",get_db_conn());
	$isupdate=true;

	if(strlen($_ord->sender_email)>0) {
		//SendBankMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $_ord->sender_email, $ordercode);
	}

	$sql="SELECT * FROM tblsmsinfo ";//WHERE mem_bankok='Y'  mem_present
	$result=mysql_query($sql,get_db_conn());
	if($rowsms=mysql_fetch_object($result)) {
		$sms_id=$rowsms->id;
		$sms_authkey=$rowsms->authkey;
		$mem_bankok=$rowsms->mem_bankok;
		$msg_mem_bankok=$rowsms->msg_mem_bankok;
		$fromtel=$rowsms->return_tel;
		$mem_present=$rowsms->mem_present;
		$msg_mem_present=$rowsms->msg_mem_present;
		$use_mms =($rowsms->use_mms=='Y')? $rowsms->use_mms:"";
	}
	mysql_free_result($result);
	if($mem_bankok == "Y"){
		$bankprice=$_ord->price;
		$bankname=$_ord->sender_name;

		if(strlen($msg_mem_bankok)==0) $msg_mem_bankok="[".strip_tags($_shopdata->shopname)."] [DATE]의 주문이 입금확인 되었습니다. 빨리 발송해 드리겠습니다.";
		$patten=array("(\[DATE\])","(\[NAME\])","(\[PRICE\])");
		$replace=array(substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2),$bankname,$bankprice);

		$msg_mem_bankok=preg_replace($patten,$replace,$msg_mem_bankok);
		$msg_mem_bankok=addslashes($msg_mem_bankok);


		$date=0;
		$etcmsg="입금확인메세지(회원)";
		$temp=SendSMS($sms_id, $sms_authkey, $_ord->sender_tel, "", $fromtel, $date, $msg_mem_bankok, $etcmsg);
	}


	//선물하기 메일보내기
	if($_ord->order_type == "p"){
		$cnt = 1;
		while($cnt > 0){
			$tmpcode = rand(10000,999999);
			$sql = "SELECT count(1) cnt FROM tblpresentcode WHERE code='".$tmpcode."'";
			$result = mysql_query($sql,get_db_conn());
			if($row = mysql_fetch_object($result)) {
				$cnt = (int)$row->cnt;
			}
			mysql_free_result($result);
		}
		$sql = "INSERT tblpresentcode SET code = '".$tmpcode."', ordercode = '".$ordercode."'";
		$insert=mysql_query($sql,get_db_conn());
		if (mysql_errno()==0) {
			$receiver_name = $_ord->receiver_name;
			$receiver_email=$_ord->receiver_email;
			$receiver_tel1=$_ord->receiver_tel1;
			$receiver_message=$_ord->receiver_message;
			if(strlen($receiver_email)>0 && strlen($receiver_message)>0){
				SendPresentMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $receiver_message, $_ord->sender_email, $_ord->sender_name, $receiver_email, $receiver_name, $tmpcode);
			}
			if($mem_present == "Y"){
				if(strlen($msg_mem_present)==0) $msg_mem_present="[".strip_tags($_shopdata->shopname)."] [URL] [NAME]님이 선물하셨습니다.";
				$patten=array("(\[URL\])","(\[NAME\])");
				$replace=array("http://".$_ShopInfo->getShopurl()."?gft_cd=".$tmpcode,$sender_name);
				$msg_mem_present=preg_replace($patten,$replace,$msg_mem_present);
				$msg_mem_present=addslashes($msg_mem_present);

				$date=0;
				$etcmsg="선물하기메세지(회원)";
				$temp=SendSMS2($sms_id, $sms_authkey, $receiver_tel1, "", $fromtel, $date, $msg_mem_present, $etcmsg, $use_mms);
			}
		}
	}



//무통장입금 취소(환불처리)
} else if($type=="bankcancel" && strlen($ordercode)>0) {
	if($_ord->deli_gbn=="C" && $_ord->paymethod=="B") {
		$sql = "UPDATE tblorderinfo SET bank_date='".substr($_ord->bank_date,0,8)."X' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		mysql_query($sql,get_db_conn());
		$isupdate=true;
	}

//일반 가상계좌 취소(환불처리)
} else if($type=="virtualcancel" && strlen($ordercode)>0) {
	if($_ord->deli_gbn=="C" && preg_match("/^(O){1}/", $_ord->paymethod)) {
		$sql = "UPDATE tblorderinfo SET pay_admin_proc='C', bank_date='".substr($_ord->bank_date,0,8)."X' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		mysql_query($sql,get_db_conn());
		$isupdate=true;
	}

//실시간계좌이체 취소(환불처리)
} else if($type=="transcancel" && strlen($ordercode)>0) {
	if($_ord->deli_gbn=="C" && preg_match("/^(V){1}/", $_ord->paymethod)) {
		$sql = "UPDATE tblorderinfo SET pay_admin_proc='C', bank_date='".substr($_ord->bank_date,0,8)."X' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		mysql_query($sql,get_db_conn());
		$isupdate=true;
		if($pg_type == "E"){
		echo "
			<html>
				<body onload=\"niceform.submit();\">
					<form name=niceform method=post action=\"".$Dir."paygate/E/cancel.php\">
						<input type=hidden name=ordercode value=\"".$_ord->ordercode."\">
						<input type=hidden name=TID value=\"".$_ord->pay_auth_no."\">
						<input type=hidden name=CancelAmt value=\"".$_ord->price."\">
						<input type=hidden name=CancelMsg value=\"관리자 취소\">
						<input type=hidden name=PartialCancelCode value=\"0\">
					</form>
				</body>
			</html>
		";
		}
		exit;

	}

//배송준비 세팅
} else if($type=="readydeli" && strlen($ordercode)>0) {
	if($_ord->deli_gbn=="N") {
		$sql = "UPDATE tblorderinfo SET deli_gbn='S' WHERE ordercode='".$ordercode."' ";
		if(mysql_query($sql,get_db_conn())) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='S' WHERE ordercode='".$ordercode."' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			$sql.= "AND deli_gbn='N' ";
			mysql_query($sql,get_db_conn());
		}
	}
	echo "<script>if(opener) {opener.history.go(0);} window.close(); </script>";
	exit;

//발송완료 세팅
} else if($type=="delivery" && strlen($ordercode)>0) {
	$delimailok=$_POST["delimailtype"];	//발송완료에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)
	$in_reserve=$_POST["in_reserve"];

	if(preg_match("/^(N|X|S)$/",$_ord->deli_gbn)) {
		$deli_com=$_POST["deli_com"];
		$deli_num=$_POST["deli_num"];
		$deli_name=$_POST["deli_name"];

		$patterns = array("( )","(_)","(-)");
		$replace = array("","","");
		$deli_num = preg_replace($patterns,$replace,$deli_num);

		###에스크로 서버에 배송정보 전달 - 에스크로 결제일 경우에만.....

		if(strlen($deli_name)==0) {
			$deli_name="자가배송";
		}
		if(preg_match("/^(Q|P){1}/", $_ord->paymethod)) {

			if($pg_type=="A") {	//KCP
				$query="sitecd=".$pgid_info["ID"]."&sitekey=".$pgid_info["KEY"]."&ordercode=".$ordercode."&deli_num=".$deli_num."&deli_name=".urlencode($deli_name);

				$delivery_data=SendSocketPost(getenv("HTTP_HOST"),str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl())."paygate/".substr($_ord->paymethod,1,1)."/delivery.php",$query);

				$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
				if (substr($delivery_data,0,2)!="OK") {
					$tempdata=explode("|",$delivery_data);
					$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
					if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
					echo "<script> alert('".$errmsg."');history.go(-1);</script>";
					exit;
				} else {
					$tempdata=explode("|",$delivery_data);
					if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
					if(strlen($errmsg)>0) {
						echo "<script> alert('".$errmsg."');</script>";
					}
				}
			} else if($pg_type=="B") {	//LG데이콤
				$delicom_code="";
				if(strlen($deli_com)>0) {
					$sql = "SELECT dacom_code FROM tbldelicompany WHERE code='".$deli_com."' ";
					$result=mysql_query($sql,get_db_conn());
					if($row=mysql_fetch_object($result)) {
						$delicom_code=$row->dacom_code;
					}
					mysql_free_result($result);
				}
				$query="mid=".$pgid_info["ID"]."&mertkey=".$pgid_info["KEY"]."&ordercode=".$ordercode."&deli_num=".$deli_num."&delicom_code=".$delicom_code;

				$delivery_data=SendSocketPost(getenv("HTTP_HOST"),str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl())."paygate/".substr($_ord->paymethod,1,1)."/delivery.php",$query);

				$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
				if (substr($delivery_data,0,2)!="OK") {
					$tempdata=explode("|",$delivery_data);
					$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
					if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
					echo "<script> alert('".$errmsg."');history.go(-1);</script>";
					exit;
				} else {
					$tempdata=explode("|",$delivery_data);
					if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
					if(strlen($errmsg)>0) {
						echo "<script> alert('".$errmsg."');</script>";
					}
				}
			} else if($pg_type=="C") {	//올더게이트
				$query="storeid=".$pgid_info["ID"]."&ordercode=".$ordercode;

				$delivery_data=SendSocketPost(getenv("HTTP_HOST"),str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl())."paygate/".substr($_ord->paymethod,1,1)."/delivery.php",$query);

				$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
				if (substr($delivery_data,0,2)!="OK") {
					$tempdata=explode("|",$delivery_data);
					$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
					if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
					echo "<script> alert('".$errmsg."');history.go(-1);</script>";
					exit;
				} else {
					$tempdata=explode("|",$delivery_data);
					if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
					if(strlen($errmsg)>0) {
						echo "<script> alert('".$errmsg."');</script>";
					}
				}
			} else if($pg_type=="D") {	//INICIS
				$delicom_code="";
				if(strlen($deli_com)>0) {
					$sql = "SELECT inicis_code FROM tbldelicompany WHERE code='".$deli_com."' ";
					$result=mysql_query($sql,get_db_conn());
					if($row=mysql_fetch_object($result)) {
						$delicom_code=$row->inicis_code;
					}
					mysql_free_result($result);
				}
				$query="sitecd=".$pgid_info["EID"]."&ordercode=".$ordercode."&deli_num=".$deli_num."&delicom_code=".$delicom_code."&deli_name=".urlencode($deli_name);

				$delivery_data=SendSocketPost(getenv("HTTP_HOST"),str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl())."paygate/".substr($_ord->paymethod,1,1)."/delivery.php",$query);

				$delivery_data=substr($delivery_data,strpos($delivery_data,"RESULT=")+7);
				if (substr($delivery_data,0,2)!="OK") {
					$tempdata=explode("|",$delivery_data);
					$errmsg="배송정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
					if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
					echo "<script> alert('".$errmsg."');history.go(-1);</script>";
					exit;
				} else {
					$tempdata=explode("|",$delivery_data);
					if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
					if(strlen($errmsg)>0) {
						echo "<script> alert('".$errmsg."');</script>";
					}
				}
			}else if($pg_type == "G"){ // 올앳 에스크로
				
				$query="ordercode=".$ordercode."&deli_num=".$deli_num."&deli_code=".$deli_com;				
				if(_empty($_POST['allatprocess'])){
				?>
				<form name="allatDeliveryForm" action="/paygate/G/delivery.php" method="post">
				<?
					foreach($_POST as $key => $val){
				?>
					<input type="hidden" name="<?=$key?>" value="<?=$val?>"/>
				<?
					}
				?>
				</form>
				<script>
					document.allatDeliveryForm.submit();
				</script>
				<?
				exit;
				}
			}
		}

		$sql = "UPDATE tblorderinfo SET deli_gbn='Y', deli_date='".date("YmdHis")."' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		if(mysql_query($sql,get_db_conn())) {
			$sql = "UPDATE tblorderproduct SET";
			if( strlen($deli_com) > 0 ) $sql.= " deli_com='".$deli_com."', ";
			if( strlen($deli_num) > 0 ) $sql.= " deli_num='".$deli_num."', ";
			$sql.= " deli_gbn='Y',";
			$sql.= " deli_date='".date("YmdHis")."' ";
			$sql.= "WHERE ordercode='".$ordercode."' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			$sql.= "AND not status like 'R%' ";
			$sql.= "AND deli_gbn!='Y' ";
			mysql_query($sql,get_db_conn());

			/********* 개별 수수료 적용 jdy **********/
			insertOrderAdjustDetail($ordercode);
			/********* 개별 수수료 적용 jdy **********/
		}
		$isupdate=true;

		if($delimailok=="Y") {	//발송완료 메일을 발송할 경우
			$delimailtype="N";
			SendDeliMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $ordercode, $deli_com, $deli_num, $delimailtype);

			if(strlen($_ord->sender_tel)>0) {
				$sql ="SELECT * FROM tblsmsinfo WHERE (mem_delivery='Y' OR mem_delinum='Y') ";
				$result=mysql_query($sql,get_db_conn());
				if($rowsms=mysql_fetch_object($result)) {
					$sms_id=$rowsms->id;
					$sms_authkey=$rowsms->authkey;

					$deliprice=$_ord->price;
					$deliname=$_ord->sender_name;

					$msg_mem_delinum=$rowsms->msg_mem_delinum;
					if(strlen($msg_mem_delinum)==0) {
						$msg_mem_delinum="[".strip_tags($shopname)."] [DELICOM] 송장번호 : [DELINUM] 금일 발송처리 되었습니다.";
					}
					$patten=array("(\[DATE\])","(\[DELICOM\])","(\[DELINUM\])","(\[NAME\])","(\[PRICE\])");
					$replace=array(substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2),$deli_name,$deli_num,$deliname,$deliprice);
					$msg_mem_delinum=preg_replace($patten,$replace,$msg_mem_delinum);
					$msg_mem_delinum=addslashes($msg_mem_delinum);

					$msg_mem_delivery=$rowsms->msg_mem_delivery;
					if(strlen($msg_mem_delivery)==0) {
						$msg_mem_delivery="[".strip_tags($shopname)."]에서 [DATE]에 주문한 상품을 발송해 드렸습니다. 감사합니다.";
					}
					$patten=array("(\[DATE\])","(\[NAME\])","(\[PRICE\])");
					$replace=array(substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2),$deliname,$deliprice);
					$msg_mem_delivery=preg_replace($patten,$replace,$msg_mem_delivery);
					$msg_mem_delivery=addslashes($msg_mem_delivery);

					$fromtel=$rowsms->return_tel;
					$date=0;
					if($rowsms->mem_delinum=="Y" && strlen($deli_name)>0 && strlen($deli_num)>0) {	//송장안내메세지
						$etcmsg="송장안내메세지(회원)";
						$temp=SendSMS($sms_id, $sms_authkey, $_ord->sender_tel, "", $fromtel, $date, $msg_mem_delinum, $etcmsg);
					}
					if($rowsms->mem_delivery=="Y") {	//상품발송메세지
						$etcmsg="상품발송메세지(회원)";
						$temp=SendSMS($sms_id, $sms_authkey, $_ord->sender_tel, "", $fromtel, $date, $msg_mem_delivery, $etcmsg);
					}
				}
				mysql_free_result($result);
			}
		}


		/*
		$first_recomId ="";
		//추천인 첫구매시 여부 확인
		if($recom_ok =="Y" && $arRecomType[0] == "B"){
			$sql = "SELECT rec_id FROM tblmember WHERE id='".$_ord->id."' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				if(trim($row->rec_id) !=""){
					//첫구매여부체크
					$sql2 = "SELECT COUNT(ordercode) as t_count FROM tblorderinfo where  deli_gbn='Y' AND id='".$_ord->id."'";
					$result2 = mysql_query($sql2,get_db_conn());
					if($row2=mysql_fetch_object($result2)) {
						$firstOrderCnt=$row2->t_count;
					}
					if($firstOrderCnt == 1 ){
						if($totalRecom >0){
							SetReserve($row->rec_id,$totalRecom,$_ord->id."님이 추천하고 구매완료하셨습니다.");
							//적립금 정보 입력 - 취소시..
							$sql = "INSERT tblreservefirst SET ";
							$sql.= "id			= '".$_ord->id."', ";
							$sql.= "reserve		= '".$totalRecom."', ";
							$sql.= "ordercode	= '".$ordercode."', ";
							$sql.= "rec_id		= '".$row->rec_id."', ";
							$sql.= "date		= '".date("YmdHis")."', ";
							$sql.= "cancelchk	= 'N' ";
							mysql_query($sql,get_db_conn());
							$first_recomId = $_ord->id;
						}
					}
					mysql_free_result($result2);
				}
			}
			mysql_free_result($result);
		}

		//sns 홍보id
		if(sizeof($arSell_id)>0 && $arSnsType[0] != "N") {
			for($jj=0;$jj<sizeof($arSell_id);$jj++){
				//첫 구매시 제외
				if($first_recomId != $arSell_id[$jj]) {
					SetReserve($arSell_id[$jj],$arSell_rsv[$jj],$_ord->id."님이 sns홍보를 통해 상품을 구매하셨습니다.");
				}
			}
		}
		*/


		// 상품구매 완료시 추천인 적립금 처리
		recommandMemReserve ( $_ord->id );


		// 상품 홍보 URL 상품홍보 적립금 처리
		snsPromoteAccessOrderOK( $ordercode );



		// 주문 적립금
		if($in_reserve>0) {
			$sql = "SELECT reserve FROM tblmember WHERE id='".$_ord->id."' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				$reservemoney=$in_reserve + $row->reserve;
				$sql = "UPDATE tblmember SET reserve = ".abs($reservemoney)." ";
				$sql.= "WHERE id='".$_ord->id."' ";
				mysql_query($sql,get_db_conn());

				$sql = "INSERT tblreserve SET ";
				$sql.= "id			= '".$_ord->id."', ";
				$sql.= "reserve		= '".$in_reserve."', ";
				$sql.= "reserve_yn	= 'Y', ";
				$sql.= "content		= '물품 구입건에 대한 적립금 지급', ";
				$sql.= "orderdata	= '".$ordercode."' = '".$_ord->price."', ";
				$sql.= "date		= '".date("YmdHis")."' ";
				mysql_query($sql,get_db_conn());
				$in_reserve=0;
			}
			mysql_free_result($result);
		}

		if($pg_type == "G"){
			echo '<script>window.close();</script>';
		}else{
			echo "<script>if(opener) {opener.history.go(0);} window.close(); </script>";
		}
		exit;
	} else if(!preg_match("/^(N|X|S)$/",$_ord->deli_gbn)) {
		echo "<script>alert(\"이미 취소되거나 발송된 물품입니다. 다시 확인하시기 바랍니다.\");</script>";
	}

#반송처리
} else if($type=="redelivery" && strlen($ordercode)>0) {
	$sql = "UPDATE tblorderinfo SET deli_gbn='R' WHERE ordercode='".$ordercode."' ";
	if(mysql_query($sql,get_db_conn())) {
		$sql = "UPDATE tblorderproduct SET deli_gbn='R' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
		mysql_query($sql,get_db_conn());
	}

	/********* 개별 수수료 적용 jdy **********/
	redeliveryOrderAdjustDetail($ordercode);
	/********* 개별 수수료 적용 jdy **********/

	echo "<script>if(opener) {opener.history.go(0);} window.close(); </script>";
	exit;

//배송지 주소 업데이트
} else if($type=="addressupdate" && strlen($ordercode)>0) {
	$post1=$_POST["post1"];
	//$post2=$_POST["post2"];
	$address1=$_POST["address1"];
	//$receiver_addr="우편번호 : ".$post1."-".$post2."\\n주소 : ".$address1;
	$receiver_addr="우편번호 : ".$post1."\\n주소 : ".$address1;
	$sql = "UPDATE tblorderinfo SET receiver_addr='".$receiver_addr."' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	mysql_query($sql,get_db_conn());
	$isupdate=true;

//송장정보 업데이트
} else if($type=="deliupdate" && strlen($ordercode)>0) {
	$delimailtype=$_POST["delimailtype"];	//송장정보 업데이트에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)
	$deli_com=$_POST["deli_com"];
	$deli_num=$_POST["deli_num"];

	$patterns = array("( )","(_)","(-)");
	$replace = array("","","");
	$deli_num = preg_replace($patterns,$replace,$deli_num);

	/********
	에스크로 서버에 송장정보 전달 - 에스크로 결제일 경우에만.....
	********/
	if(preg_match("/^(Q|P){1}/", $_ord->paymethod)) {
		//KCP|올더게이트는 송장정보 업데이트 모듈이 없음. (추후 다른 PG사 추가시 작업.....)
	}

	$sql = "UPDATE tblorderproduct SET deli_num='".$deli_num."', deli_com='".$deli_com."' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
	mysql_query($sql,get_db_conn());

	if(strlen($_ord->sender_email)>0 && $delimailtype=="Y") {
		SendDeliMail($_shopdata->shopname, $shopurl, $_shopdata->design_mail, $_shopdata->info_email, $ordercode, $deli_com, $deli_num, $delimailtype);
	}
	if(strlen($_ord->sender_tel)>0 && $delimailtype=="Y") {
		if(strlen($deli_com)>0 && strlen($deli_name)==0) {
			$sql = "SELECT company_name FROM tbldelicompany WHERE code='".$deli_com."' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				$deli_name=$row->company_name;
			}
			mysql_free_result($result);
		}
		if(strlen($deli_name)==0) {
			$deli_name="자가배송";
		}
		$sql="SELECT * FROM tblsmsinfo WHERE mem_delinum='Y' ";
		$result=mysql_query($sql,get_db_conn());
		if($rowsms=mysql_fetch_object($result)) {
			$sms_id=$rowsms->id;
			$sms_authkey=$rowsms->authkey;

			$deliprice=$_ord->price;
			$deliname=$_ord->sender_name;
			$msg_mem_delinum=$rowsms->msg_mem_delinum;
			if(strlen($msg_mem_delinum)==0) {
				$msg_mem_delinum="[".strip_tags($shopname)."] [DELICOM] 송장번호 : [DELINUM] 금일 발송처리 되었습니다.";
			}
			$patten=array("(\[DATE\])","(\[DELICOM\])","(\[DELINUM\])","(\[NAME\])","(\[PRICE\])");
			$replace=array(substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2),$deli_name,$deli_num,$deliname,$deliprice);
			$msg_mem_delinum=preg_replace($patten,$replace,$msg_mem_delinum);
			$msg_mem_delinum=addslashes($msg_mem_delinum);

			$fromtel=$rowsms->return_tel;
			$date=0;
			$etcmsg="송장안내메세지(회원)";
			$temp=SendSMS($sms_id, $sms_authkey, $_ord->sender_tel, "", $fromtel, $date, $msg_mem_delinum, $etcmsg);
		}
		mysql_free_result($result);
	}

//KCP/올더게이트/이니시스 - 상품배송 후 취소요청이 있을 경우 우선 정산보류 상태로 돌린다. (반송 완료 후 취소처리 가능)
} else if($type=="okhold" && strlen($ordercode)>0 && ($pg_type=="A" || $pg_type=="C" || $pg_type=="D")) {
	if(preg_match("/^(Y|D)$/", $_ord->deli_gbn) && strlen($_ord->deli_date)==14) {
		if($pg_type=="A") {
			$query="sitecd=".$pgid_info["ID"]."&sitekey=".$pgid_info["KEY"]."&ordercode=".$ordercode;
			$hold_data=SendSocketPost(getenv("HTTP_HOST"),str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl())."paygate/".substr($_ord->paymethod,1,1)."/hold.php",$query);
			$hold_data=substr($hold_data,strpos($hold_data,"RESULT=")+7);
			if (substr($hold_data,0,2)!="OK") {
				$tempdata=explode("|",$hold_data);
				$errmsg="정산보류 정보를 에스크로 서버에 전달하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
				if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
				echo "<script> alert('".$errmsg."');history.go(-1);</script>";
				exit;
			} else {
				$tempdata=explode("|",$hold_data);
				if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
				if(strlen($errmsg)>0) {
					echo "<script> alert('".$errmsg."');</script>";
				}
			}

			$sql = "UPDATE tblorderinfo SET ";
			$sql.= "deli_gbn		= 'H' WHERE ordercode='".$ordercode."' ";
			if(mysql_query($sql,get_db_conn())) {
				$sql = "UPDATE tblorderproduct SET deli_gbn='H' ";
				$sql.= "WHERE ordercode='".$ordercode."' ";
				$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
				mysql_query($sql,get_db_conn());
			}
		} else if($pg_type=="C") {
			$query="sitecd=".$pgid_info["ID"]."&ordercode=".$ordercode;
			$hold_data=SendSocketPost(getenv("HTTP_HOST"),str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl())."paygate/".substr($_ord->paymethod,1,1)."/hold.php",$query);
			$hold_data=substr($hold_data,strpos($hold_data,"RESULT=")+7);
			if (substr($hold_data,0,2)!="OK") {
				$tempdata=explode("|",$hold_data);
				$errmsg="정산보류 처리가 정상 완료하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
				if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
				echo "<script> alert('".$errmsg."');history.go(-1);</script>";
				exit;
			} else {
				$tempdata=explode("|",$hold_data);
				if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
				if(strlen($errmsg)>0) {
					echo "<script> alert('".$errmsg."');</script>";
				}
			}

			$sql = "UPDATE tblorderinfo SET ";
			$sql.= "deli_gbn		= 'H' WHERE ordercode='".$ordercode."' ";
			if(mysql_query($sql,get_db_conn())) {
				$sql = "UPDATE tblorderproduct SET deli_gbn='H' ";
				$sql.= "WHERE ordercode='".$ordercode."' ";
				$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
				mysql_query($sql,get_db_conn());
			}
		} else if($pg_type=="D") {
			$query="sitecd=".$pgid_info["EID"]."&ordercode=".$ordercode."&curgetid=".$_ShopInfo->getId();
			$hold_data=SendSocketPost(getenv("HTTP_HOST"),str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl())."paygate/".substr($_ord->paymethod,1,1)."/hold.php",$query);
			$hold_data=substr($hold_data,strpos($hold_data,"RESULT=")+7);
			if (substr($hold_data,0,2)!="OK") {
				$tempdata=explode("|",$hold_data);
				$errmsg="정산보류 처리가 정상 완료하지 못했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
				if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
				echo "<script> alert('".$errmsg."');history.go(-1);</script>";
				exit;
			} else {
				$tempdata=explode("|",$hold_data);
				if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
				if(strlen($errmsg)>0) {
					echo "<script> alert('".$errmsg."');</script>";
				}
			}

			$sql = "UPDATE tblorderinfo SET ";
			$sql.= "deli_gbn		= 'H' WHERE ordercode='".$ordercode."' ";
			if(mysql_query($sql,get_db_conn())) {
				$sql = "UPDATE tblorderproduct SET deli_gbn='H' ";
				$sql.= "WHERE ordercode='".$ordercode."' ";
				$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
				mysql_query($sql,get_db_conn());
			}
		}
		$isupdate=true;
	}

//수량복구, 주문취소
} else if(($type=="recancel" || $type=="recoveryquan" || $type=="recoverycan") && strlen($ordercode)>0) {
	if($_ord->pay_admin_proc!="C" && $_ord->pay_flag=="0000" && preg_match("/^(C){1}/", $_ord->paymethod)) { // 일반 카드 주문은 주문취소전에 카드를 취소해야함
		echo "<script>alert('먼저 카드취소를 하셔야 합니다.');history.go(-1);</script>";
		exit;
	} else if($_ord->pay_admin_proc!="C" && $_ord->pay_flag=="0000" && preg_match("/^(M){1}/", $_ord->paymethod)) { // 핸드폰 주문은 주문취소전에 핸드폰 결제를 취소해야함
		echo "<script>alert('먼저 핸드폰결제 취소를 하셔야 합니다.');history.go(-1);</script>";
		exit;
	}

	/************* 에스크로 결제 환불(가상계좌) 또는 취소(신용카드) ***************/
	if(preg_match("/^(Q|P){1}/", $_ord->paymethod)) {
		//Q(가상계좌 매매보호)일 경우엔 우선 환불대기 후 환불되면 자동 취소처리된다.

		if($pg_type=="A") {			#KCP
			$query="sitecd=".$pgid_info["ID"]."&sitekey=".$pgid_info["KEY"]."&ordercode=".$ordercode;
		} else if($pg_type=="B") {	#LG데이콤
			$query="mid=".$pgid_info["ID"]."&mertkey=".$pgid_info["KEY"]."&ordercode=".$ordercode;
		} else if($pg_type=="C") {  #올더게이트
			$query="storeid=".$pgid_info["ID"]."&ordercode=".$ordercode;
		} else if($pg_type=="D") {  #이니시스
			$query="sitecd=".$pgid_info["EID"]."&ordercode=".$ordercode."&curgetid=".$_ShopInfo->getId();
		}

		$cancel_data=SendSocketPost(getenv("HTTP_HOST"),str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl())."paygate/".substr($_ord->paymethod,1,1)."/escrow_cancel.php",$query);

		$cancel_data=substr($cancel_data,strpos($cancel_data,"RESULT=")+7);
		if (substr($cancel_data,0,2)!="OK") {
			$tempdata=explode("|",$cancel_data);
			$errmsg="취소처리가 정상 완료 되지 못 했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
			if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
			echo "<script> alert('".$errmsg."');history.go(-1);</script>";
			exit;
		} else {
			$tempdata=explode("|",$cancel_data);
			if(strlen($tempdata[1])>0) $errmsg=$tempdata[1];
			if(strlen($errmsg)>0) {
				echo "<script> alert('".$errmsg."');</script>";
			}
		}
	}

	if($_ord->del_gbn!="R"){ //R일 경우 수량이 복구된 경우
		if($type=="recoveryquan"){ //수량복구 실행
			$sql = "SELECT a.productcode,a.productname,a.opt1_name,a.opt2_name,a.quantity, ";
			$sql.= "b.option_quantity,b.option1,b.option2,a.package_idx,a.assemble_idx,a.assemble_info FROM tblorderproduct a, tblproduct b ";
			$sql.= "WHERE a.productcode=b.productcode AND a.ordercode='".$ordercode."' ";
			$result=mysql_query($sql,get_db_conn());
			$message="";
			while ($row=mysql_fetch_object($result)) {
				$tmpoptq="";
				if(strlen($artmpoptq[$row->productcode])>0)
					$optq=$artmpoptq[$row->productcode];
				else
					$optq=$row->option_quantity;

				//if(strlen($optq)>51 && substr($row->opt1_name,0,5)!="[OPTG"){
				if(strlen($optq)>51){
					$tmpoptname1=explode(" : ",$row->opt1_name);
					$tmpoptname2=explode(" : ",$row->opt2_name);
					$tmpoption1=explode(",",$row->option1);
					$tmpoption2=explode(",",$row->option2);
					$cnt=1;
					$maxoptq = count($tmpoption1);
					while ($tmpoption1[$cnt]!=$tmpoptname1[1] && $cnt<$maxoptq) {
						$cnt++;
					}
					$opt_no1=$cnt;
					$cnt=1;
					$maxoptq2 = count($tmpoption2);
					while ($tmpoption2[$cnt]!=$tmpoptname2[1] && $cnt<$maxoptq2) {
						$cnt++;
					}
					$opt_no2=$cnt;
					$optioncnt = explode(",",substr($optq,1));

					/*
					echo "<pre>";
					print_r($tmpoption1);
					print_r($tmpoption2);
					print_r($optioncnt);
					echo "</pre>";
					*/

					$selNum = ($opt_no2-2)*10+($opt_no1-3);

					//echo "<br>".$optioncnt[$selNum]."<br>";
					if($optioncnt[$selNum]!="") $optioncnt[$selNum]+=$row->quantity;
					for($j=0;$j<10;$j++){
						for($i=0;$i<10;$i++){
							$tmpoptq.=",".$optioncnt[$j*10+$i];
						}
					}
					if(strlen($tmpoptq)>0 && $tmpoptq.","!=$optq){
						$artmpoptq[$row->productcode]=$tmpoptq;
						$tmpoptq=",option_quantity='".$tmpoptq.",'";
					}else{
						$tmpoptq="";
						$message .="[".$row->productname." - ".$row->opt1_name.$row->opt2_name."]\\n";
					}
				}
				$sql = "UPDATE tblproduct SET quantity=quantity+".$row->quantity.$tmpoptq." ";
				$sql.= "WHERE productcode='".$row->productcode."'";
				//exit;
				mysql_query($sql,get_db_conn());

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

			if(strlen($message)==0) $message="수량복구가 성공적으로 되었습니다. 확인바랍니다.";
			else $message="옵션별 수량복구가 아래의 원인으로 실패하였습니다.\\n\\n".$message."\\n위의 상품의 경우 현재 옵션값이 주문시의 옵션 값과 다릅니다.\\n개별 상품에서 직접 확인후 옵션별 수량 조정하세요\\n기본 수량은 복구된 상태입니다. 옵션별 수량만 조정하세요.";
			$canmess="<script>alert('".$message."');</script>";

			$log_content = "## 상품수량 복구 ## - 주문번호 : ".$ordercode;
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		} else {
			$canmess="<script>alert('해당 주문을 취소하였습니다.');</script>";

			$log_content = "## 주문취소 ## - 주문번호 : ".$ordercode;
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		}
	}

	if(preg_match("/^(B|O|Q){1}/", $_ord->paymethod) && $tax_type=="Y") {	//현금영수증 자동 발행
		$sql = "SELECT COUNT(*) as cnt FROM tbltaxsavelist WHERE ordercode='".$ordercode."' AND type='Y' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		mysql_free_result($result);
		if($row->cnt>0) {
			$flag="C";
			include($Dir."lib/taxsave.inc.php");
		}
	}

	if (preg_match("/^(Q){1}/", $_ord->paymethod) && strlen($_ord->bank_date)==14 && ($pg_type!="C" && $pg_type!="D")) {
		$deliupdate =" deli_gbn='E' ";	//환불대기
		$up_deli_gbn="E";
	} else if (preg_match("/^(P){1}/", $_ord->paymethod)) {
		$deliupdate = " deli_gbn='C', pay_admin_proc='C' ";
		$up_deli_gbn="C";
	} else {
		$deliupdate = " deli_gbn='C' ";
		$up_deli_gbn="C";
	}

	if($type=="recoveryquan") {
		if($_ord->del_gbn=="Y") $okdel="R";
		else $okdel="A";
	} else if($type=="recoverycan") $okdel=$_ord->del_gbn;

	$sql = "UPDATE tblorderinfo SET ".$deliupdate.", del_gbn='".$okdel."' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	if(mysql_query($sql,get_db_conn())) {
		$sql = "UPDATE tblorderproduct SET deli_gbn='".$up_deli_gbn."' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
		mysql_query($sql,get_db_conn());
	}
	$isupdate=true;
	echo $canmess;

//회원이 사용한 적립금 복구처리 후 주문서 취소처리
} else if($type=="recoveryres" && strlen($ordercode)>0) {
	if($_ord->deli_gbn!="C" && $_ord->reserve>0 && strlen($_ord->id)>0) {
		if($_ord->deli_gbn!="C" && strlen($_ord->bank_date)>0 && preg_match("/^(Q){1}/", $_ord->paymethod)) {
			//가상계좌 에스크로일 경우
			echo "<script>alert('가상계좌 에스크로 입금건은 취소 후 수기로 적립금을 처리하셔야 합니다.');history.go(-1);</script>";
			exit;
		}

		$sql = "UPDATE tblorderinfo SET deli_gbn='C' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		if(mysql_query($sql,get_db_conn())) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
			$sql.= "WHERE ordercode='".$ordercode."' ";
			$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
			mysql_query($sql,get_db_conn());
		}
		$isupdate=true;

		//적립금 조회
		$now_reserve = abs($_ord->reserve);

		//취소내용 조회
		$sql = "select sum(cancel_reserve) as cancel_reserve from part_cancel_reserve where ordercode='".$ordercode."'";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);

		$cancel_reserve = $row->cancel_reserve;
		mysql_free_result($result);

		$now_reserve = $now_reserve-$cancel_reserve;



		$sql = "UPDATE tblmember SET reserve=reserve+".$now_reserve." ";
		$sql.= "WHERE id='".$_ord->id."' ";
		mysql_query($sql,get_db_conn());

		$sql = "INSERT tblreserve SET ";
		$sql.= "id			= '".$_ord->id."', ";
		$sql.= "reserve		= '".$now_reserve."', ";
		$sql.= "reserve_yn	= 'Y', ";
		$sql.= "content		= '주문 취소건에 대한 적립금 환원', ";
		$sql.= "orderdata	= '".$ordercode."=".$_ord->price."', ";
		$sql.= "date		= '".date("YmdHis")."' ";
		mysql_query($sql,get_db_conn());

		$log_content="## 회원 적립금 환원 ## - 주문번호 : ".$ordercode." - 적립금 ".$now_reserve;
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	}

//주문취소로 인한 회원에게 지급했던 적립금 취소처리
} else if($type=="recoveryrecan" && strlen($ordercode)>0) {
	$canreserve=$_POST["canreserve"];
	if(preg_match("/^(Y|D|H)$/",$_ord->deli_gbn) && strlen($_ord->deli_date)==14 && $canreserve>0 && strlen($_ord->id)>0) {
		$sql = "UPDATE tblmember SET reserve=if(reserve<".abs($canreserve).",0,reserve-".abs($canreserve).") ";
		$sql.= "WHERE id='".$_ord->id."' ";
		mysql_query($sql,get_db_conn());

		$sql = "INSERT tblreserve SET ";
		$sql.= "id			= '".$_ord->id."', ";
		$sql.= "reserve		= '-$canreserve', ";
		$sql.= "reserve_yn	= 'Y', ";
		$sql.= "content		= '주문 취소건에 대한 적립금 지급취소', ";
		$sql.= "orderdata	= '".$ordercode."=".$_ord->price."', ";
		$sql.= "date		= '".date("YmdHis")."' ";
		mysql_query($sql,get_db_conn());

		$sql = "INSERT tblorderproduct SET ";
		$sql.= "ordercode	= '".$ordercode."', ";
		$sql.= "tempkey		= '".$_ord->tempkey."', ";
		$sql.= "productcode	= '99999999999R', ";
		$sql.= "productname	= '주문 취소건에 대한 적립금 지급취소', ";
		$sql.= "quantity	= '1', ";
		$sql.= "reserve		= '-$canreserve', ";
		$sql.= "date		= '".date("Ymd")."' ";
		mysql_query($sql,get_db_conn());

		$first_recomid = "";
		if($recom_ok =="Y" && $arRecomType[0] == "B"){
			$sql = "SELECT rec_id, reserve FROM tblreservefirst WHERE id='".$_ord->id."' AND ordercode='".$ordercode."' AND cancelchk	= 'N'";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				if(trim($row->rec_id) !=""){
					$sql = "INSERT tblreserve SET ";
					$sql.= "id			= '".$row->rec_id."', ";
					$sql.= "reserve		= '-".$row->reserve."', ";
					$sql.= "reserve_yn	= 'Y', ";
					$sql.= "content		= '".$_ord->id."님의 주문 취소건에 대한 적립금 지급취소', ";
					$sql.= "orderdata	= '', ";
					$sql.= "date		= '".date("YmdHis")."' ";
					mysql_query($sql,get_db_conn());

					$sql = "UPDATE tblmember SET reserve=if(reserve<".abs($row->reserve).",0,reserve-".abs($row->reserve).") ";
					$sql.= "WHERE id='".$row->rec_id."' ";
					mysql_query($sql,get_db_conn());
					$first_recomid = $_ord->id;

					$sql = "INSERT tblreservefirst SET ";
					$sql.= "id			= '".$_ord->id."', ";
					$sql.= "reserve		= '".$row->reserve."', ";
					$sql.= "ordercode	= '".$ordercode."', ";
					$sql.= "rec_id		= '".$row->rec_id."', ";
					$sql.= "date		= '".date("YmdHis")."', ";
					$sql.= "cancelchk	= 'Y' ";
					mysql_query($sql,get_db_conn());
				}
			}
			mysql_free_result($result);
		}

		//추천인 및 sns 홍보인
		if(sizeof($arSell_id)>0 && $arSnsType[0] != "N") {
			for($jj=0;$jj<sizeof($arSell_id);$jj++){
				if($first_recomid != $arSell_id[$jj]) {
					$sql = "INSERT tblreserve SET ";
					$sql.= "id			= '".$arSell_id[$jj]."', ";
					$sql.= "reserve		= '-".$arSell_rsv[$jj]."', ";
					$sql.= "reserve_yn	= 'Y', ";
					$sql.= "content		= '".$_ord->id."님의 주문 취소건에 대한 적립금 지급취소', ";
					$sql.= "orderdata	= '', ";
					$sql.= "date		= '".date("YmdHis")."' ";
					mysql_query($sql,get_db_conn());

					$sql = "UPDATE tblmember SET reserve=if(reserve<".abs($arSell_rsv[$jj]).",0,reserve-".abs($arSell_rsv[$jj]).") ";
					$sql.= "WHERE id='".$arSell_id[$jj]."' ";
					mysql_query($sql,get_db_conn());
				}
			}
		}


		$log_content="## 회원 적립금 지급취소 ## - 주문번호 : ".$ordercode." - 적립금 -".$canreserve;
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	}

//메모 업데이트
} else if($type=="memoupdate" && strlen($ordercode)>0) {
	$memo1=$_POST["memo1"];
	$memo2=$_POST["memo2"];

	$order_msg.="[MEMO]".$memo1;
	if(strlen(trim($memo2))!=0) $order_msg.="[MEMO]".$memo2;
	mysql_query("UPDATE tblorderinfo SET order_msg='".$order_msg."' WHERE ordercode='".$ordercode."'",get_db_conn());
	$isupdate=true;

//무통장입금 결제시 수량/적립금/가격 정보 변경
} else if($type=="orderupdate" && strlen($ordercode)>0) {
	$curdate = date("YmdHis");
	$vender=(int)$_POST["vender"];
	$productcode=$_POST["productcode"];
	$opt1_name=$_POST["opt1_name"];
	$opt2_name=$_POST["opt2_name"];
	$reserve=(int)$_POST["reserve"];
	$price=(int)$_POST["price"];
	$quantity=(int)$_POST["quantity"];
	$salereserve=(int)$_POST["salereserve"];
	$salemoney=(int)$_POST["salemoney"];
	$usereserve=(int)$_POST["usereserve"];
	$deli_price=(int)$_POST["deli_price"];
	$sumprice=(int)$_POST["sumprice"];

	if(strlen($productcode)==12 || strlen($productcode)==18) {
		if(strlen($productcode)==18 || substr($productcode,-4)=="GIFT") {
			if ($quantity<1) {
				echo "<script> alert ('수량은 1보다 큰 숫자로 입력해 주셔야 합니다.');history.back();</script>\n";
				exit;
			}
			$setprice=$price/$quantity;
		} else {
			$setprice=$price;
			$quantity=1;
		}
		$sql = "UPDATE tblorderproduct SET ";
		$sql.= "quantity	= '".$quantity."', ";
		$sql.= "price		= '".$setprice."', ";
		$sql.= "reserve		= '".$reserve."' ";
		$sql.= "WHERE vender='".$vender."' ";
		$sql.= "AND ordercode='".$ordercode."' AND productcode='".$productcode."' ";
		$sql.= "AND opt1_name='".$opt1_name."' AND opt2_name='".$opt2_name."' ";
	} else if($productcode==2) { //그룹회원 할인/적립
		if($salereserve>0) $tempdc_price=$salereserve;
		else $tempdc_price=$salemoney;
		$sql = "UPDATE tblorderinfo SET dc_price='".$tempdc_price."' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
	} else if($productcode==3) { //적립금 사용액
		$sql = "UPDATE tblorderinfo SET reserve='".$usereserve."' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
	} else if($productcode==4) { //배송료
		$sql = "UPDATE tblorderinfo SET deli_price='".$deli_price."' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
	} else if($productcode==5) { //전체금액
		$sql = "UPDATE tblorderinfo SET price='".$sumprice."' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
	}
	if(mysql_query($sql,get_db_conn())) {
		if($productcode=="99999999990X") {	//배송료일 경우
			$sql = "SELECT SUM(price) as in_deli_price FROM tblorderproduct ";
			$sql.= "WHERE ordercode='".$ordercode."' AND productcode='99999999990X' ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			mysql_free_result($result);

			$in_deli_price=$row->in_deli_price;
			$sql = "UPDATE tblorderinfo SET deli_price='".$in_deli_price."' ";
			$sql.= "WHERE ordercode='".$ordercode."' ";
			mysql_query($sql,get_db_conn());
		}
	}
	$isupdate=true;

	$log_content = "## 주문상품 변경 ## - 주문번호 : ".$ordercode." - 상품코드 : ".$productcode." - 수량 : ".$quantity." - 가격 : ".$setprice." - 적립금 : ".$reserve;
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

//무통장입금 결제시 상품/정보 삭제
} else if($type=="orderdelete" && strlen($ordercode)>0) {
	$curdate = date("YmdHis");
	$vender=(int)$_POST["vender"];
	$productcode=$_POST["productcode"];
	$opt1_name=$_POST["opt1_name"];
	$opt2_name=$_POST["opt2_name"];

	if(strlen($productcode)==12 || strlen($productcode)==18) {
		$sql = "DELETE FROM tblorderproduct WHERE vender='".$vender."' ";
		$sql.= "AND ordercode='".$ordercode."' AND productcode='".$productcode."' ";
		$sql.= "AND opt1_name='".$opt1_name."' AND opt2_name='".$opt2_name."' ";
	} else if($productcode==2) { //그룹회원 할인/적립
		$sql = "UPDATE tblorderinfo SET dc_price=0 WHERE ordercode='".$ordercode."' ";
	} else if($productcode==3) { //적립금 사용액
		$sql = "UPDATE tblorderinfo SET reserve=0 WHERE ordercode='".$ordercode."' ";
	} else if($productcode==4) { //배송료
		$sql = "UPDATE tblorderinfo SET deli_price=0 WHERE ordercode='".$ordercode."' ";
	}
	if(mysql_query($sql,get_db_conn())) {
		if($productcode=="99999999990X") {	//배송료일 경우
			$sql = "SELECT SUM(price) as in_deli_price FROM tblorderproduct ";
			$sql.= "WHERE ordercode='".$ordercode."' AND productcode='99999999990X' ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			mysql_free_result($result);

			$in_deli_price=$row->in_deli_price;
			$sql = "UPDATE tblorderinfo SET deli_price='".$in_deli_price."' ";
			$sql.= "WHERE ordercode='".$ordercode."' ";
			mysql_query($sql,get_db_conn());
		}
	}
	$isupdate=true;

	$log_content = "## 주문상품 삭제 ## - 주문번호 : ".$ordercode." - 상품코드 : ".$productcode;
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

} else if($type=="deligbnup" && strlen($ordercode)>0 && strlen($_POST["prcodes"])>0 && preg_match("/^(N|S|Y)$/",$deli_gbn) && preg_match("/^(N|X|S)$/",$_ord->deli_gbn)) {
	$prcodes=$_POST["prcodes"];
	$deli_gbn=$_POST["deli_gbn"];

	$prcodes=substr($prcodes,0,-1);
	$prlist=ereg_replace(',','\',\'',$prcodes);
	$sql = "UPDATE tblorderproduct SET deli_gbn='".$deli_gbn."', ";
	if($deli_gbn=="Y") $sql.= "deli_date='".date("YmdHis")."' ";
	else $sql.= "deli_date=NULL ";
	$sql.= "WHERE ordercode='".$ordercode."' AND productcode IN ('".$prlist."') ";
	$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
	if(mysql_query($sql,get_db_conn())) {
		if($_ord->deli_gbn!=$deli_gbn) {
			$rescode=getDeligbn($ordercode,$deli_gbn);
			if(strlen($rescode)>0) {
				$isupdate=true;
			}
		}
	}

} else if($type=="deliinfoup" && strlen($ordercode)>0 && strlen($_POST["prcodes"])>0) {
	$prcodes=$_POST["prcodes"];
	$deliprinfo = !_empty($_POST['deliprinfo'])?trim($_POST['deliprinfo']):"";
	if(strlen($deliprinfo)<=0){
		echo '<script>alert("잘못된 요청으로 처리가 거부되었습니다.");history.back();</script>';exit;
	}

	$deliprinfoarray = explode("|",$deliprinfo);

	if(_array($deliprinfoarray)){
		foreach($deliprinfoarray as $uidinfo){
			$uidinfoarray = explode("=",$uidinfo);
			$deliinfoupdateSQL="";
			if(_array($uidinfoarray)){

				$deliinfoupdateSQL = "UPDATE tblorderproduct SET deli_gbn='Y', deli_date='".date("YmdHis")."', deli_com='".$deli_com."', deli_num='".$deli_num."' ";
				$deliinfoupdateSQL.= "WHERE ordercode='".$ordercode."' AND productcode='".$uidinfoarray[0]."' AND uid = '".$uidinfoarray[1]."' ";
				$deliinfoupdateSQL.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
				mysql_query($deliinfoupdateSQL,get_db_conn());
			}
		}
	}
	$sql = "SELECT deli_gbn FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
	$tres = mysql_query($sql,get_db_conn());
	
	if($tres !== false && mysql_result($tres,0,0)!='Y') {
		$sql = "SELECT count(*) FROM tblorderproduct WHERE ordercode='".$ordercode."' && deli_gbn!='Y' AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%')";
		$tres = mysql_query($sql,get_db_conn());
		if($tres !== false && mysql_result($tres,0,0)==0) {
			$ck_y = "Y";
			$tmpdelicom = $deli_com;
			$tmpdelinum = $deli_num;
			$delicomlist=array();

			$sql="SELECT company_name FROM tbldelicompany WHERE code='{$deli_com}' ";
			if(false !== $tres = mysql_query($sql,get_db_conn())){
				$tmpdeliname = mysql_result($tres,0,0);
			}
		}
	}
}

if($isupdate) {
	$sql="SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."'";
	$result=mysql_query($sql,get_db_conn());
	$_ord=mysql_fetch_object($result);
	mysql_free_result($result);
	if(!$_ord) {
		echo "<script>alert(\"해당 주문내역이 존재하지 않습니다.\");window.close();</script>";
		exit;
	}
}

$prescd="N";
if(preg_match("/^(B){1}/", $_ord->paymethod)) {	//무통장
	if (strlen($_ord->bank_date)>0) $prescd="Y";
} else if(preg_match("/^(V){1}/", $_ord->paymethod)) {	//계좌이체
	if ($_ord->pay_flag=="0000") $prescd="Y";
} else if(preg_match("/^(M){1}/", $_ord->paymethod)) {	//핸드폰
	if ($_ord->pay_flag=="0000") $prescd="Y";
} else if(preg_match("/^(O|Q){1}/", $_ord->paymethod)) {	//가상계좌
	if ($_ord->pay_flag=="0000" && strlen($_ord->bank_date)>0) $prescd="Y";
} else {
	if ($_ord->pay_flag=="0000" && $_ord->pay_admin_proc=="Y") $prescd="Y";
}

$sql = "SELECT * FROM tblsmsinfo ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$smsok=true;
}
mysql_free_result($result);

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$vendercnt=$row->vendercnt;
mysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ";
	$sql.= "ORDER BY id ASC ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	mysql_free_result($result);
}

$delicomlist=array();
//$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
$sql="SELECT * FROM tbldelicompany ORDER BY code ";
$result=mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)) {
	$delicomlist[]=$row;
}
mysql_free_result($result);

$curdate = date("YmdHi",mktime(date("H"),date("i")-30,0,date("m"),date("d"),date("Y")));
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>주문상세내역 보기</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="lib.js.php"></script>
<STYLE TYPE="text/css">
<!--
body { font-size: 9pt}
td { font-size: 9pt; line-height: 15pt}
tr { font-size: 9pt}
.break {page-break-before: always;}
-->
</STYLE>
<SCRIPT LANGUAGE="javascript">
<!--



// CS 관리 팝업 - (주문코드, 상품코드, 벤더, 회원아이디)
function csManagerPop( order, product, vender ) {
	window.open( "cs_orderInsert.php?o="+order+"&p="+product+"&v="+vender , "csManagerInsert" , "width=620, height=500, menubar=no, status=no" );
}






//document.onkeydown = CheckKeyPress;
//document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
	   event.keyCode = 0;
	   return false;
	 }
}

function PageResize() {
	//var oWidth = document.all.table_body.clientWidth + 26;
	//var oHeight = document.all.table_body.clientHeight + 55;
	var oWidth = 750;
	var oHeight=650;

	window.resizeTo(oWidth,oHeight);
}

var countdeli=countdelinum=countdecan=countbank=countbacan=countvican=counttrcan=countokcan=countokhold=0;

function PagePrint(){
	if(confirm("주문상세내역을 프린트 하시겠습니까?")) {
		print();
	}
}

function Sort(key){
	document.form2.sort.value=key;
	document.form2.type.value="sort";
	document.form2.submit();
}

function ProductInfo(code,prcode,popup) {
	document.form_reg.code.value=code;
	document.form_reg.prcode.value=prcode;
	document.form_reg.popup.value=popup;
	if (popup=="YES") {
		document.form_reg.action="product_register.add.php";
		document.form_reg.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_reg.action="product_register.php";
		document.form_reg.target="";
	}
	document.form_reg.submit();
}
function ProductMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.primage"+cnt);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.visibility = "visible";
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	Obj = document.getElementById(Obj);
	Obj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

function ChangeUpdateMode(mode){
	document.form1.mode.value=mode;
	document.form1.type.value="";
	document.form1.submit();
}

function OrderUpdate(num,cnt,vender,productcode,opt1_name,opt2_name){
	if(confirm("해당 상품이나 내역을 수정하시겠습니까?")) {
		if(num==1) {
			document.form1.vender.value=vender;
			document.form1.productcode.value=productcode;
			document.form1.opt1_name.value=opt1_name;
			document.form1.opt2_name.value=opt2_name;
			document.form1.quantity.value=document.form1.arquantity[cnt].value;
			document.form1.reserve.value=document.form1.arreserve[cnt].value;
			document.form1.price.value=document.form1.arprice[cnt].value;
		} else {
			document.form1.productcode.value=num;
		}

		document.form1.mode.value="update";
		document.form1.type.value="orderupdate";
		document.form1.submit();
	}
}

function OrderDelete(num,vender,productcode,opt1_name,opt2_name){
	if(confirm("해당 상품이나 내역을 삭제하시겠습니까?")) {
		if(num==1) {
			document.form1.vender.value=vender;
			document.form1.productcode.value=productcode;
			document.form1.opt1_name.value=opt1_name;
			document.form1.opt2_name.value=opt2_name;
		} else {
			document.form1.productcode.value=num;
		}

		document.form1.mode.value="update";
		document.form1.type.value="orderdelete";
		document.form1.submit();
	}
}

function RestoreOrder(temp,delivery,reserve) {
	if(temp=="quan") mess="수량복구";
	else mess="주문취소";

	if(document.form2.recoveryquan.value=="Y") {
		alert(mess+"는 한 주문서에 대해서 한번만 가능합니다.");
		return;
	}
<?
	if ($_ord->deli_gbn!="C" && preg_match("/^(C|P){1}/",$_ord->paymethod) && substr($_ord->ordercode,0,12)>$curdate) {
		echo "	alert(\"카드주문의 경우 주문시점에서 30분 경과 후 \"+mess+\"가 가능합니다.\\n\\n고객이 카드정보 입력중 일 수 있습니다.\");";
	} else if($_ord->pay_admin_proc!="C" && $_ord->pay_flag=="0000" && preg_match("/^(C){1}/", $_ord->paymethod)) {
		echo "	alert(\"카드주문 취소는 먼저 카드취소 후 진행해 주세요.\");";
	} else if($_ord->pay_admin_proc!="C" && $_ord->pay_flag=="0000" && preg_match("/^(M){1}/", $_ord->paymethod)) {
		echo "	alert(\"휴대폰주문 취소는 먼저 휴대폰결제 취소 후 진행해 주세요.\");";
	} else {
?>
	if(delivery!="C" && reserve>0 && confirm("회원적립금을 먼저 취소하셔야 합니다.")) {
		RestoreReserve();
	} else if(temp=="quan" && confirm("상품수량이 자동으로 복귀되며,\n\n해당주문은 주문취소됩니다.<?=(preg_match("/^(P|Q){1}/",$_ord->paymethod)?"\\n\\n매매보호 결제의 경우는 자동으로 결제도 취소가 됩니다.":"")?>")) {
		document.form2.type.value="recoveryquan";
		document.form2.recoveryquan.value="Y";
		document.form2.submit();
	} else if(temp=="can" && confirm("상품수량은 복구되지 않습니다.\n\n해당 주문을 취소하시겠습니까?<?=(preg_match("/^(P|Q){1}/",$_ord->paymethod)?"\\n\\n매매보호 결제의 경우는 자동으로 결제도 취소가 됩니다.":"")?>")) {
		document.form2.type.value="recoverycan";
		document.form2.recoveryquan.value="Y";
		document.form2.submit();
	}
<?	} ?>
}

function RestoreReserve(){
	if(document.form2.recoveryrese.value=="Y"){
		alert("적립금 복구는 한 주문서에 대해서 한번만 가능합니다.");
		return;
	}
<?
	if ($_ord->deli_gbn!="C" && preg_match("/^(C|P){1}/",$_ord->paymethod) && substr($_ord->ordercode,0,12)>$curdate) {
		 echo "	alert(\"카드주문의 경우 주문시점에서 30분 경과 후 적립금 복구가 가능합니다.\\n\\n고객이 카드정보 입력중 일 수 있습니다.\");";
	} else {
?>
	if(confirm("회원 적립금이 자동으로 복구되며,\n\n해당주문은 주문최소됩니다.")){
		document.form2.type.value="recoveryres";
		document.form2.recoveryrese.value="Y";
		document.form2.submit();
		document.form2.submit();
	}
<?	} ?>
}

function RestoreReserveCancel(temp) {
	if(document.form2.recoveryrecan.value=="Y") {
		alert("적립금 취소는 한 주문서에 대해서 한번만 가능합니다.");
		return;
	}
	if(confirm("상품 배송으로 인한 지급 적립금이 자동으로 취소됩니다.\n\n적립금 취소후 주문서는 반드시 취소상태로 변경하셔야 합니다.")) {
		document.form2.type.value="recoveryrecan";
		document.form2.canreserve.value=temp;
		document.form2.recoveryrecan.value="Y";
		document.form2.submit();
	}
}

function SendSMS(tel1,tel2,tel3) {
	number=tel1+"|"+tel2+"|"+tel3;
	document.smsform.number.value=number;
	window.open("about:blank","sendsmspop","width=220,height=350,scrollbars=no");
	document.smsform.submit();
}

function SendMail(mail) {
	try {
		opener.parent.topframe.ChangeMenuImg(3);
		opener.document.mailform.rmail.value=mail;
		opener.document.mailform.submit();
	} catch(e) {}
}

function HideMemo() {
	try {
		membermemo_layer.style.visibility="hidden";
	} catch (e) {}
}

function MemberMemo(id) {
	window.open("about:blank","memopop","width=350,height=350,scrollbars=no");
	document.formmemo.target="memopop";
	document.formmemo.id.value=id;
	document.formmemo.action="member_memopop.php";
	document.formmemo.submit();
	document.formmemo.target="";
	document.formmemo.action="<?=$_SERVER[PHP_SELF]?>";
}

function HideDisplay() {
	document.formhide.submit();
}

/**************************************************************************************************/
//영수증 발행
function printtax(){
	document.taxprintform.submit();
}
//현금영수증 요청
function get_taxsave() {
	window.open("about:blank","taxsavepop","width=266,height=220,scrollbars=no");
	document.taxsaveform.submit();
}

//운송장 출력
function printaddress(){
	alert("서비스 준비중입니다.");
}
//반송처리
function delicancel(){
	if(!countdecan){
		if(!confirm("반송처리 하시겠습니까?")) return;
		countdecan++;
		document.form2.type.value="redelivery";
		document.form2.submit();
	}
}
//카드/핸드폰 취소
function card_ask(temp,caltype){
	//card_ask - 매입요청
	//card_cancel - 카드취소
	if(temp=="card_ask") { //매입요청
		if(confirm("신용카드 매입요청을 하시겠습니까?")) {
			<?if($pg_type=="A"){?>
				document.kcpform.action="<?=$Dir?>paygate/A/edi.php";
				document.kcpform.submit();
			<?}else if($pg_type=="B"){?>
			<?}else if($pg_type=="C"){?>
			<?}else if($pg_type=="D"){?>
			<?}?>
		}
	} else if(temp=="card_cancel") {//취소요청
		<?if($pg_type=="A"){?>
			if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
				document.kcpform.action="<?=$Dir?>paygate/A/cancel.php";
				document.kcpform.submit();
			}
		<?}else if($pg_type=="B"){?>
			if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
				document.dacomform.action="<?=$Dir?>paygate/B/cancel.php";
				document.dacomform.submit();
			}
		<?}else if($pg_type=="C"){?>
			if(caltype == "hp") {
			if(confirm("\n┏━━━━━━━━━━━━━━  【 주      의      사      항 】  ━━━━━━━━━━━━━━━━┓    \n┃                                                                                                                                    ┃    \n┃                                                                                                                                    ┃    \n┃       １. 휴대폰 결제 취소 처리는 쇼핑몰 DB에만 반영되며 올더게이트에 전달되지 않습니다.       ┃    \n┃                                                                                                                                    ┃    \n┃       ２. 올더게이트 휴대폰 결제 취소는 해당 ＰＧ사의 관리자페이지에서 처리 해 주세요.           ┃    \n┃                                                                                                                                    ┃    \n┃                                                                                                                                    ┃    \n┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛    \n\n                               결제취소처리는 쇼핑몰 DB에만 반영됩니다. 정말 하시겠습니까?")) {
				document.allthegateform.action="<?=$Dir?>paygate/C/cancel.php";
				document.allthegateform.submit();
			}
			} else {
				if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
					document.allthegateform.action="<?=$Dir?>paygate/C/cancel.php";
					document.allthegateform.submit();
				}
			}
		<?} else if($pg_type=="D"){?>
			if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
				document.inicisform.action="<?=$Dir?>paygate/D/cancel.php";
				document.inicisform.submit();
			}
		<?} else if($pg_type=="E"){?>
			if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
				document.niceform.action="<?=$Dir?>paygate/E/cancel.php";
				document.niceform.submit();
			}
		<?} else if($pg_type=="G"){?>
			if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
				document.allatform.action="<?=$Dir?>paygate/G/cancel.php";
				document.allatform.submit();
			}
		<?}?>
	}
}
//발송준비, 발송완료 처리
function delisend(temp){
	if(!countdeli){
		if(temp=="Y" && !confirm("입금확인이 안된 주문서입니다. 배송을 완료하시겠습니까?")) return;
		if(temp=="S" && !confirm("발송준비 지시를 하시겠습니까?")) return;
		if(temp=="S1" && !confirm("입금확인이 안된 주문서입니다. 발송준비 지시를 하시겠습니까?")) return;
		//else if(temp=="N" && !confirm("배송을 완료하시겠습니까?")) return;
		if(temp=="S" || temp=="S1") document.form2.type.value="readydeli";
		else {

			tmpdelicom=document.escrow_form1.escrow_deli_com.value;
			tmpdelinum=document.escrow_form1.escrow_deli_num.value;
			tmpdeliname=document.escrow_form1.escrow_deli_com.options[document.escrow_form1.escrow_deli_com.selectedIndex].text;

			if(document.getElementById("deliescrow")) {
				if(document.getElementById("deliescrow").style.display == "none") {
					document.getElementById("deliescrow").style.display="";
					return;
				}
				if(document.escrow_form1.escrowcaltype.value=="Y" && (!tmpdelicom || !tmpdelinum || !tmpdeliname)) {
					alert("에스크로 발송완료 처리는 송장정보를 입력해야만 처리가 가능합니다.");
					return;
				}
			}

			if(confirm("SMS 발송 확인!!\n\n1. 대표 송장정보 미입력할 경우 배송메일에서 송장정보가 출력되지 않습니다.\n"+"2. 대표 송장정보 미입력할 경우 송장번호 안내 SMS 는 발송되지 않습니다.\n\n          발송완료된 정보를 메일/SMS로 발송하시겠습니까?\n\n\n   * 배송업체 : "+tmpdeliname+"\n\n   * 송장번호 : "+tmpdelinum+"")) {
				document.form2.delimailtype.value="Y";
			} else {
				document.form2.delimailtype.value="N";
			}
			//if(!confirm("정말 배송을 완료하시겠습니까?")) return;


			document.form2.deli_com.value=tmpdelicom;
			document.form2.deli_num.value=tmpdelinum;
			document.form2.deli_name.value=tmpdeliname;
			document.form2.type.value="delivery";
		}
		countdeli++;

		document.form2.submit();
	}
}

function cgY(){
	document.form2.delimailtype.value="N";
	document.form2.deli_com.value='<?=$tmpdelicom?>';
	document.form2.deli_num.value='<?=$tmpdelinum?>';
	document.form2.deli_name.value='<?=$tmpdeliname?>';
	document.form2.type.value="delivery";
	document.form2.submit();
}

function escrow_deliclose()
{
	if(document.getElementById("deliescrow")) {
		if(document.getElementById("deliescrow").style.display == "") {
			document.escrow_form1.escrow_deli_com.selectedIndex=0;
			document.escrow_form1.escrow_deli_num.value="";
			document.getElementById("deliescrow").style.display = "none";
		}
	}
}

//무통장입금 완료처리
function banksend(){
	if(!countbank){
		if(!confirm("입금확인을 셋팅하시겠습니까?")) return;
		countbank++;
		document.form2.type.value="bank";
		document.form2.submit();
	}
}
//무통장 환불처리
function bankcancel(){
	if(!countbacan){
		if(!confirm("입금취소하시겠습니까?")) return;
		countbacan++;
		document.form2.type.value="bankcancel";
		document.form2.submit();
	}
}
//일반 가상계좌 환불처리
function virtualcancel() {
	if(!countvican){
		if(!confirm("가상계좌 입금건에 대해서 적립금 지급 또는 무통장으로 환불을 하셨습니까?")) return;
		countvican++;
		document.form2.type.value="virtualcancel";
		document.form2.submit();
	}
}
//실시간계좌이체 환불처리
function transcancel() {
	if(!counttrcan){
		if(!confirm("실시간계좌이체 결제건에 대해서 적립금 지급 또는 무통장으로 환불을 하셨습니까?")) return;
		counttrcan++;
		document.form2.type.value="transcancel";
		document.form2.submit();
	}
}

<?if($pg_type=="A" || $pg_type=="C" || $pg_type=="D"){?>
//매매보호 정산보류
function okhold() {
	if(!countokhold) {
		if(!confirm("이미 배송된 매매보호 결제건에 대해서 정산보류 처리를 하시겠습니까?\n\n정산보류 처리 후 상품이 반송완료되면 최종 취소처리가 가능합니다.")) return;
		countokhold++;
		document.form2.type.value="okhold";
		document.form2.submit();
	}
}
<?}?>

//매매보호 취소처리
function okcancel(temp,date) {
	if(!countokcan) {
		if(temp=="Q") {
			if(date.length>0) {
				<?if($pg_type=="A"){?>
				if(!confirm("매매보호 주문에 대해서 취소처리 하시겠습니까?\n\n환불대기 상태후 금액이 환불되면 자동 주문 취소됩니다.")) return;
				<?}else if($pg_type=="B"){?>
					<?if(strlen($_ord->deli_date)==14){?>
					alert("에스크로 환불처리는 LG데이콤 상관점관리에서 하시기 바랍니다.\n\n환불완료 후 쇼핑몰에 자동 반영됩니다."); return;
					<?}?>
				<?}else if($pg_type=="C"){?>

				<?}else if($pg_type=="D"){?>

				<?}?>
			} else {
				<?if($pg_type=="A"){?>
				if(!confirm("매매보호 주문에 대해서 취소처리 하시겠습니까?\n\n입금전이므로 발급계좌는 소멸됩니다.")) return;
				<?}else if($pg_type=="B"){?>
				//if(!confirm("매매보호 주문에 대해서 취소처리 하시겠습니까?")) return;
				<?}else if($pg_type=="C"){?>

				<?}else if($pg_type=="D"){?>

				<?}?>
			}
		}
		if(!confirm("매매보호 주문에 대해서 취소처리 하시겠습니까?")) return;
		countokcan++;
		document.form2.type.value="recancel";
		document.form2.submit();
	}
}
/**************************************************************************************************/


function f_addr_search(form,post,addr,gbn) {
	window.open("<?=$Dir.FrontDir?>addr_search.php?form="+form+"&post="+post+"&addr="+addr+"&gbn="+gbn,"f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");
}

function AddressUpdate(){
	if(confirm("배송지를 해당 주소로 수정하시겠습니까?")) {
		document.form2.type.value="addressupdate";
		document.form2.submit();
	}
}

function DeliSearch(deli_url){
	window.open(deli_url,"배송추적","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizeble=yes,copyhistory=no,width=600,height=550");
}

function DeliNumUpdate() {
	if(!countdelinum) {
		if(!confirm("모든 상품의 배송정보를 수정하시겠습니까?")) {
			document.form2.delimailtype.value="N";
			return;
		}

		if(confirm("모든 상품의 배송정보 변경내역을 메일/SMS로 발송하시겠습니까?"))
			document.form2.delimailtype.value="Y";
		else
			document.form2.delimailtype.value="N";

		document.form2.deli_com.value=document.form1.deli_com.value;
		document.form2.deli_num.value=document.form1.deli_num.value;
		document.form2.type.value="deliupdate";
		document.form2.submit();
	}
}

function MemoUpdate(){
	if(confirm("메모를 등록/수정하시겠습니까?")) {
		document.form2.type.value="memoupdate";
		document.form2.submit();
	}
}

<? if(preg_match("/^(Q){1}/",$_ord->paymethod) && strlen($_ord->bank_date)==14) { ?>
function escrow_bank_account() {
	alert("환불계좌 등록 후 매매보호 취소처리를 하시면\n\n등록된 환불계좌로 환불처리됩니다.");
	window.open("about:blank","baccountpop","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizeble=no,copyhistory=no,width=100,height=100");
	document.vform.submit();
}
<? } ?>


function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}

function EtcMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.etcdtl"+cnt);
	obj._tid = setTimeout("EtcView(WinObj)",200);
}
function EtcView(WinObj) {
	WinObj.style.visibility = "visible";
}
function EtcMouseOut(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.etcdtl"+cnt);
	WinObj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

function CheckAll(){
	chkval=document.form1.allcheck.checked;
	if(typeof(document.form1.chkprcode.length)=="number") {
		cnt=document.form1.chkprcode.length;
		for(i=1;i<cnt;i++){
			document.form1.chkprcode[i].checked=chkval;
		}
	}
}

function changeDeliinfo() {

	if(!document.form1.deli_com.value) {
		alert("배송회사를 선택 하시기 바랍니다.");
		return;
	}

	if(!document.form1.deli_num.value) {
		alert("송장번호를 입력 하시기 바랍니다.");
		return;
	}

	<?
		if(preg_match("/^(B){1}/",$_ord->paymethod) && strlen($_ord->bank_date)!=14) {
	?>
		if( !confirm("입금확인이 안된 주문서입니다. 송장번호 일괄 등록을 하시겠습니까?") ) {
			return;
		}
	<?
		}
	?>

	//if( document.orderDeliReadyProductobj.value == 'Y' ) { // 발송완료 + 송장 번호 일 경우

		if(typeof(document.form1.chkprcode.length)=="number") {
			document.form1.prcodes.value="";
			cnt=document.form1.chkprcode.length;
			for(i=1;i<cnt;i++){
				if(document.form1.chkprcode[i].checked==true) {
					document.form1.prcodes.value+="PRCODE="+document.form1.chkprcode[i].value+",DELI_COM="+document.form1.deli_com.value+",DELI_NUM="+document.form1.deli_num.value+"|";
					document.form1.deliprinfo.value+=document.form1.chkprcode[i].value+"="+document.form1.chkprcode[i].getAttribute('uid')+"|";
				}
			}
			document.form1.deliprinfo.value = document.form1.deliprinfo.value.slice(0,-1);
		} else {
			alert("배송 상품이 존재하지 않습니다.");
			return;
		}
		if(document.form1.prcodes.value.length==0) {
			alert("선택하신 상품이 없습니다.");
			return;
		}
		if(confirm("선택된 상품의 배송업체/송장번호를 수정(등록)합니다.\n\n정말로 적용하시겠습니까?")) {
			document.form1.type.value="deliinfoup";
			document.form1.submit();
		}
	//} else { // 발송준비
	//}
}

<?if(preg_match("/^(N|X|S)$/",$_ord->deli_gbn) && $_ord->pay_admin_proc!="C" && $prescd=="Y") {?>
function changeDeli(obj) {

	if(obj.value=='R') {
		rRefundNew(obj);
		return;
	}

	if(typeof(document.form1.chkprcode.length)=="number") {
		document.form1.prcodes.value="";
		cnt=document.form1.chkprcode.length;
		for(i=1;i<cnt;i++){
			if(document.form1.chkprcode[i].checked==true) {
				document.form1.prcodes.value+=document.form1.chkprcode[i].value+",";
			}
		}
	} else {
		alert("배송 상품이 존재하지 않습니다.");
		return;
	}
	deli_gbn=obj.value;
	document.form1.prcodes.value="";
	for(i=1;i<document.form1.chkprcode.length;i++) {
		if(document.form1.chkprcode[i].checked==true) {
			document.form1.prcodes.value+=document.form1.chkprcode[i].value+",";
		}
	}
	if(document.form1.prcodes.value.length==0) {
		alert("선택하신 상품이 없습니다.");
		obj.selectedIndex=0;
		return;
	}
	if(deli_gbn.length>0) {
		delistr="";
		if(deli_gbn=="N") delistr="[미처리]";
		else if(deli_gbn=="S") delistr="[발송준비]";
		else if(deli_gbn=="Y") delistr="[발송완료]";
		if(confirm("선택된 상품의 처리상태를 "+delistr+" 상태로 변경하시겠습니까?")) {
			document.form1.type.value="deligbnup";
			document.form1.submit();
		} else {
			document.form1.prcodes.value="";
			obj.selectedIndex=0;
		}
	} else {
		document.form1.prcodes.value="";
		obj.selectedIndex=0;
	}
}
<?}?>


function rDeliUpdate(cnt){
	f = eval("document.reForm_"+cnt);
	if(!f.deli_com.value) {
		alert("배송업체를 선택하세요");
		f.deli_com.focus();
		return false;
	}
	if(!f.deli_num.value) {
		alert("송장번호를  입력하세요");
		f.deli_num.focus();
		return false;
	}

	f.type.value = "deli";
	f.action = "order_ok.php";
	f.submit();

}

function rDeliUpdate2(cnt){
	f = eval("document.reForm2_"+cnt);
	if(!f.deli_com.value) {
		alert("배송업체를 선택하세요");
		f.deli_com.focus();
		return false;
	}
	if(!f.deli_num.value) {
		alert("송장번호를  입력하세요");
		f.deli_num.focus();
		return false;
	}

	f.type.value = "deli";
	f.action = "order_ok.php";
	f.submit();

}

function rBankUpdate(cnt){
	f = eval("document.reForm_"+cnt);

	f.type.value = "bank";
	f.action = "order_ok.php";
	f.submit();

}

function rMemoUpdate(cnt){
	f = eval("document.reForm_"+cnt);

	f.type.value = "memo";
	f.action = "order_ok.php";
	f.submit();

}

function rMemoUpdate2(cnt){
	f = eval("document.reForm2_"+cnt);

	f.type.value = "memo";
	f.action = "order_ok.php";
	f.submit();

}

function rStatusUpdate(str,cnt){
	f = eval("document.reForm_"+cnt);

	f.type.value = "status1";
	f.action = "order_ok.php";
	f.status.value = str;
	f.submit();

}

function rStatusUpdate2(str,cnt){
	f = eval("document.reForm2_"+cnt);

	f.type.value = "status2";
	f.action = "order_ok.php";
	f.status.value = str;
	f.submit();

}

function rChangeUpdateMode(mode){
	document.form1.mode.value=mode;
	document.form1.type.value="";
	document.form1.submit();
}

function rPriceUpdate(cnt){
	f = eval("document.reForm_"+cnt);

	f.type.value = "price1";
	f.action = "order_ok.php";
	f.submit();

}

function rPriceUpdate2(cnt){
	f = eval("document.reForm2_"+cnt);

	f.type.value = "price2";
	f.action = "order_ok.php";
	f.submit();

}

function rCancelUpdate(cnt){
	frm = eval("document.reForm_"+cnt);

	for (i = cnt = 0; i < frm.elements.length; i++) {
		if(frm.elements[i].name == 'Item[]' && frm.elements[i].checked == true) {
			cnt++;
		}
	}

	if ( cnt <= 0) {
		alert("환불취소할 상품을 선택하세요!");
		return;
	}

	if(confirm("정말로 취소하겠습니다. 취소가 되면 해당 정보가 삭제 됩니다.")){
		frm.type.value = "cancel1";
		frm.action = "order_ok.php";
		frm.submit();
	}

}


function rCancelUpdate2(cnt){
	frm = eval("document.reForm2_"+cnt);

	for (i = cnt = 0; i < frm.elements.length; i++) {
		if(frm.elements[i].name == 'Item[]' && frm.elements[i].checked == true) {
			cnt++;
		}
	}

	if ( cnt <= 0) {
		alert("교환취소할 상품을 선택하세요!");
		return;
	}

	if(confirm("정말로 취소하겠습니다. 취소가 되면 해당 정보가 삭제 됩니다.")){
		frm.type.value = "cancel2";
		frm.action = "order_ok.php";
		frm.submit();
	}

}

function rRefundUpdate(cnt){
	frm = eval("document.reForm_"+cnt);

	for (i = cnt = 0; i < frm.elements.length; i++) {
		if(frm.elements[i].name == 'Item[]' && frm.elements[i].checked == true) {
			cnt++;
		}
	}

	if ( cnt <= 0) {
		alert("환불처리할 상품을 선택하세요!");
		return;
	}

	frm.type.value = "refund3";
	frm.action = "order_ok.php";
	frm.submit();

}

function rRefundNew(obj){
	var goods = '';
	for(i=1;i<document.form1.chkprcode.length;i++) {
		if(document.form1.chkprcode[i].checked==true) {
			if(!document.form1.chkprcode[i].statuss) {
				if(!goods) goods = document.form1.chkprcode[i].uid;
				else goods += '|' + document.form1.chkprcode[i].uid;
			}
		}
	}
	if(!goods) {
		alert("선택하신 상품이 없습거나 이미 신청된 상품입니다");
		if(obj) obj.selectedIndex=0;
		return;
	}

	f = document.reTmpForm;

	f.type.value = "refund1";
	f.goods.value = goods;
	f.action = "order_ok.php";
	f.submit();
}

function rRefundNew2(obj){
	var goods = '';
	for(i=1;i<document.form1.chkprcode.length;i++) {
		if(document.form1.chkprcode[i].checked==true) {
			if(!document.form1.chkprcode[i].statuss) {
				if(!goods) goods = document.form1.chkprcode[i].uid;
				else goods += '|' + document.form1.chkprcode[i].uid;
			}
		}
	}
	if(!goods) {
		alert("선택하신 상품이 없습거나 이미 신청된 상품입니다");
		if(obj) obj.selectedIndex=0;
		return;
	}

	f = document.reTmpForm;

	f.type.value = "refund2";
	f.goods.value = goods;
	f.action = "order_ok.php";
	f.submit();
}

function rReserveUpdate(cnt){
	frm = eval("document.reForm_"+cnt);

	for (i = cnt = 0; i < frm.elements.length; i++) {
		if(frm.elements[i].name == 'Item[]' && frm.elements[i].checked == true) {
			cnt++;
		}
	}

	if ( cnt <= 0) {
		alert("적림금취소처리할 상품을 선택하세요!");
		return;
	}

	frm.type.value = "reserve";
	frm.action = "order_ok.php";
	frm.submit();

}

function changeDeliinfo2(cnt) {
	frm = eval("document.reForm2_"+cnt);

	if(!frm.deli_comss.value) {
		alert("배송회사를 선택 하시기 바랍니다.");
		return;
	}

	if(!frm.deli_numss.value) {
		alert("송장번호를 입력 하시기 바랍니다.");
		return;
	}

	for (i = cnt = 0; i < frm.elements.length; i++) {
		if(frm.elements[i].name == 'Item[]' && frm.elements[i].checked == true) {
			cnt++;
		}
	}

	if ( cnt <= 0) {
		alert("배송정보를 등록할 상품을 선택하세요!");
		return;
	}

	frm.type.value = "deliinfo";
	frm.action = "order_ok.php";
	frm.submit();
}




// CS 관리 팝업 - (주문코드, 상품코드, 벤더, 회원아이디)
function csManagerPop( order, product, vender ) {
	window.open( "cs_orderInsert.php?o="+order+"&p="+product+"&v="+vender , "csManagerInsert" , "width=620, height=500, menubar=no, status=no" );
}




// 상품별 발송준비 / 배송(송장번호) 처리
function orderDeliReadyProductChkAll( V ) {
	if( V == "Y" ) {
		document.form1.deli_com.style.display = 'block';
		document.form1.deli_num.style.display = 'block';
	} else {
		document.form1.deli_com.style.display = 'none';
		document.form1.deli_num.style.display = 'none';
	}
}

function refundsTo(str){
	var goods = '';
	var re_price = 0;
	var dcPriceTotal = 0;

	for(i=1;i<document.form1.chkprcode.length;i++) {

		if(document.form1.chkprcode[i].checked==true){
			var chkprcode_statuss_value = document.form1.chkprcode_statuss;
			if( chkprcode_statuss_value.length ) {
				chkprcode_statuss_value = chkprcode_statuss_value[i-1].value;
			} else {
				chkprcode_statuss_value = chkprcode_statuss_value.value;
			}
			if(chkprcode_statuss_value=='RC' ) {
				alert('이미 환불 처리된 건입니다.');
				return;
			}



			if( document.form1.chkprcode_uid.length > 1 ) {
					if( goods == '' ) {
						goods = document.form1.chkprcode_uid[i-1].value;
					} else {
						goods += '|' + document.form1.chkprcode_uid[i-1].value;
					}
				//}chkprcode_uid
				re_price = re_price + parseInt( document.form1.chkprcode_price[i-1].value ) * 1;
			} else {
				goods = document.form1.chkprcode_uid.value;
				re_price = re_price + parseInt( document.form1.chkprcode_price.value ) * 1;
			}



			// 쿠폰 사용 할인가
			var dcPrices = 0;
			if(typeof document.form1.chkprcode[i].dcprice == "undefined"){
				dcPrices = parseInt(document.form1.chkprcode[i].getAttribute('dcprice'));
			}else{
				dcPrices = parseInt( document.form1.chkprcode[i].dcprice );
			}
			if ( !isNaN(dcPrices) && dcPrices > 0){
				dcPriceTotal += dcPrices;
				re_price -= dcPrices;
			}
		}
	}
	if(!goods) {
		alert("선택하신 상품이 없습거나 이미 신청된 상품입니다");
		return;
	}

	f = document.reTmpForm;

	f.type.value = "order";
	f.status.value = str;
	f.goods.value = goods;
	f.bank.value = document.getElementById("bank").value;
	f.account_name.value = document.getElementById("account_name").value;
	f.account_num.value = document.getElementById("account_num").value;

	f.action = "order_new_ok.php";

	if (str=='RC') {

		rp = document.getElementById("refund_price");
		rp.value = re_price;

		document.getElementById("re_price_max").value = re_price;

		dcPriceTotalMsg.innerHTML = "<br />할인(쿠폰/등급할인) 제외 금액 : " + dcPriceTotal + " 원";
		f.dcPriceSend.value = dcPriceTotal;

		refundDivView('Y');

	}else{
		if ( confirm("선택한상품을 부분취소 신청하시겠습니까?") )
		{
			f.submit();
		}
	}
}

function refundsCom() {
	
	rp = document.getElementById("refund_price");
	
	if ( document.getElementById("re_reserve") != null ) {
		reserve = document.getElementById("re_reserve").value;
		if (!reReserveChk(reserve)) {
			alert("환원 가능한 적립금보다 큰액수를 입력하셨습니다.");
			return;
		}
	}

	re_price_max = document.getElementById("re_price_max");

	if (rp.value>re_price_max.value) {
		if( !confirm("상품 금액보다 큰 액수입니다.\n그래도 계속 진행 하시겠습니까?") ){
			rp.focus();
			return;
		}
	}

	if (rp.value < 0 ) {
		alert("0보다 작은값을 환불 금액으로 입력하실 수 없습니다.\n목록에서 쿠폰만을 선택하셨는지 확인해주세요.");
		rp.focus();
		return;
	}

	f = document.reTmpForm;

	f.re_price.value = rp.value;
	if ( document.getElementById("refund_free") != null ) {
		f.refund_free.value = document.getElementById("refund_free").value;
	}

	if (document.getElementById("re_reserve") != null ) {

		reserve = document.getElementById('re_reserve');
		f.reserve.value = reserve.value;
	}

	//f.dcPriceSend.value = document.getElementById("dcPriceSend").value;

	f.submit();
}

function refundDivView(v) {

	r_div = document.getElementById('refund_div');

	if (document.getElementById("re_reserve") != null ) {
		document.getElementById("re_reserve").value=0;
	}

	if (v=='N') {
		r_div.style.display="none";
	}else {
		if (r_div.style.display=="none") {
			r_div.style.display="block";
		}else{
			r_div.style.display="none";
		}
	}
}

function card_part_cancel(uid) {

<?if($pg_type=="A"){?>
		if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
			document.kcpform.uid.value = uid;
			document.kcpform.action="<?=$Dir?>paygate/A/part_cancel.php";
			document.kcpform.submit();
		}
<?}else if($pg_type=="B"){?>
		if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
			document.dacomform.uid.value = uid;
			document.dacomform.action="<?=$Dir?>paygate/B/part_cancel.php";
			document.dacomform.submit();
		}
<?}else if($pg_type=="C"){?>
		if(caltype == "hp") {
			if(confirm("\n┏━━━━━━━━━━━━━━  【 주      의      사      항 】  ━━━━━━━━━━━━━━━━┓    \n┃                                                                                                                                    ┃    \n┃                                                                                                                                    ┃    \n┃       １. 휴대폰 결제 취소 처리는 쇼핑몰 DB에만 반영되며 올더게이트에 전달되지 않습니다.       ┃    \n┃                                                                                                                                    ┃    \n┃       ２. 올더게이트 휴대폰 결제 취소는 해당 ＰＧ사의 관리자페이지에서 처리 해 주세요.           ┃    \n┃                                                                                                                                    ┃    \n┃                                                                                                                                    ┃    \n┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛    \n\n                               결제취소처리는 쇼핑몰 DB에만 반영됩니다. 정말 하시겠습니까?")) {
				document.allthegateform.uid.value = uid;
				document.allthegateform.action="<?=$Dir?>paygate/C/part_cancel.php";
				document.allthegateform.submit();
			}
		} else {
			if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
				document.allthegateform.action="<?=$Dir?>paygate/C/part_cancel.php";
				document.allthegateform.submit();
			}
		}
<?} else if($pg_type=="D"){?>
		if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
			document.inicisform.uid.value = uid;
			document.inicisform.action="<?=$Dir?>paygate/D/part_cancel.php";
			document.inicisform.submit();
		}
<?}?>

}

function reserveDivView(v) {

	r_div = document.getElementById('reserve_div');

	if (v=='N') {
		r_div.style.display="none";
	}else {
		if (r_div.style.display=="none") {
			r_div.style.display="";
		}else{
			r_div.style.display="none";
		}
	}
}

function reReserveChk(reserve) {

	if (reserve>0) {
		re_reserve_max = document.getElementById("re_reserve_max").value;

		if ((reserve*1)>(re_reserve_max*1)) {
			return false;
		}
	}

	return true;
}


function reReserveCom() {

	reserve = document.getElementById('re_reserve');

	if (reserve.value=='') {
		alert("환원활 적립금을 입력해주세요.");
		return;
	}

	f = document.reTmpForm;


	f.type.value = "reserve";
	f.re_price.value = reserve.value;
	f.action = "order_new_ok.php";

	f.submit();

}

function rePriceMinusReserve(obj) {

	reserve = obj.value;

	if (reserve=='') reserve = 0;

	re_price_max = document.getElementById("re_price_max").value;

	if (reserve>=0) {
		re_price = re_price_max-reserve;

		rp = document.getElementById("refund_price");

		if (re_price<0) {
			alert("적립금 취소금액이 너무 큽니다.");
			rp.value = re_price_max;
			obj.value = 0;
		}else{
			rp.value = re_price;
		}
	}

}

function ReserveInOut(id){
	window.open("about:blank","reserve_set","width=245,height=140,scrollbars=no");
	document.reserveform.target="reserve_set";
	document.reserveform.id.value=id;
	document.reserveform.type.value="reserve";
	document.reserveform.submit();
}


function ReserveInfo(id) {
	window.open("about:blank","reserve_info","width=500,height=400,scrollbars=yes");
	document.reserveform2.id.value=id;
	document.reserveform2.submit();
}

function reFuAccount() {
	f = document.reTmpForm;

	f.type.value = "bank";
	f.bank.value = document.getElementById("bank").value;
	f.account_name.value = document.getElementById("account_name").value;
	f.account_num.value = document.getElementById("account_num").value;

	f.action = "order_new_ok.php";
	f.submit();
}





// 수동취소 ( 2014-07-08 x2chi )
function popCancle ( o, t ) {
	window.open("orderCancle.php?o="+o+"&t="+t,"popCancle","width=600,height=600,scrollbars=no");
}



//-->
</SCRIPT>
</head>
<!--body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();"-->
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="PagePrint();return false;" style="overflow-x:hidden;overflow-y:auto;" onLoad="PageResize();" >

<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed;" id=table_body>
	<tr><td><img src="images/tit_orderdetail.gif" alt="" /></td></tr>
	<tr>
		<td height="70" style="border-bottom:1px solid #e0e0e0; padding:0px 17px;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><img src="images/tit_orderstep.gif" alt="" /></td>
					<td width="100%">
<?
		$createbutton="<table border=0 cellpadding=0 cellspacing=0>";
		//미처리 또는 배송요청건이고, 카드취소가 아니고, 무통장 입금건이고, 입금이 안됐다면,,,,,
		//if(preg_match("/^(N|X)$/",$_ord->deli_gbn) && $_ord->pay_admin_proc!="C" && preg_match("/^(B){1}/",$_ord->paymethod) && strlen($_ord->bank_date)!=14) {
		//	$createbutton.="<tr><td align=right style='padding-right:40px' height=15><img src='images/ordtl_arrow1.gif' align=absmiddle></td></tr>";
		//}
		$createbutton.="<tr><td>";

		if(preg_match("/^(N|X|S)$/",$_ord->deli_gbn) && $_ord->pay_admin_proc!="C") {
			//(미처리/배송요청/발송완료) 되고 카드결제 취소건이 아니면,,,,,
			//$createbutton.="<a href=\"javascript:printaddress()\"><img src=\"images/ordtl_btnprint.gif\" align=absmiddle border=0></a>\n";	//운송장 출력
			//$createbutton.="<font color=#C0C0C0>|</font>&nbsp;";

			if(preg_match("/^(B){1}/",$_ord->paymethod) && strlen($_ord->bank_date)!=14) {
				//무통장 입금 결제건이고, 입금이 안된 경우
				//$createbutton.="<a href=\"javascript:banksend()\"><img src=\"images/ordtl_btnbankok.gif\" align=absmiddle border=0></a><img src='images/ordtl_arrow2.gif' align=absmiddle>";	//입금확인
				$createbutton.="<a href=\"javascript:banksend()\"><img src=\"images/ordtl_btnbankok.gif\" align=absmiddle border=0></a>&nbsp;";	//입금확인

				//배송준비 단계가 아니면,,,,,
				if($_ord->deli_gbn!="S") {
					//$createbutton.="<a href=\"javascript:delisend('S')\"><img src=\"images/ordtl_btndeliready.gif\" align=absmiddle border=0></a><img src='images/ordtl_arrow2.gif' align=absmiddle>";	//발송준비
					$createbutton.="<a href=\"javascript:alert('입금 미확인 주문입니다.');\"><img src=\"images/ordtl_btndeliready.gif\" align=absmiddle border=0></a>&nbsp;";	//발송준비 // delisend('S1') : 메세지 띄우고 패스
					$createbutton.="<a href=\"javascript:alert('입금 미확인 주문입니다.')\"><img src=\"images/ordtl_btndeliok.gif\" align=absmiddle border=0></a>\n";	//발송완료 // delisend('Y')
				}

			} else if(!preg_match("/^(O|Q){1}/",$_ord->paymethod) || strlen($_ord->bank_date)>=12) {	//가상계좌 입금건에 대해서 입금이 된 경우

				//배송준비 단계가 아니면,,,,,
				if($_ord->deli_gbn!="S") $createbutton.="<td><a href=\"javascript:delisend('S')\"><img src=\"images/ordtl_btndeliready.gif\" align=absmiddle border=0></a>&nbsp;\n";	//발송준비

				$createbutton.="<div id=deliescrow style=\"position:absolute; z-index:100; display:none;\">\n";
				$createbutton.="<table border=0 cellspacing=1 cellpadding=0 bgcolor=#0099CC width=250>\n";
				$createbutton.="<tr>\n";
				$createbutton.="	<td style=\"padding:10px;\">\n";
				$createbutton.="	<table border=0 cellspacing=1 cellpadding=0 bgcolor=#B9B9B9 width=250>\n";
				$createbutton.="	<form name=escrow_form1>\n";
				if(preg_match("/^(Q|P){1}/", $_ord->paymethod)) {
					$createbutton.="	<input type=hidden name=\"escrowcaltype\" value=\"Y\">\n";
				} else {
					$createbutton.="	<input type=hidden name=\"escrowcaltype\" value=\"N\">\n";
				}
				$createbutton.="	<tr bgcolor=#FFFFFF>\n";
				$createbutton.="		<td class=\"table_cell\" colspan=\"2\" align=\"center\">대 표 송 장 정 보</td>\n";
				$createbutton.="	</tr>\n";
				$createbutton.="	<tr bgcolor=#FFFFFF>\n";
				$createbutton.="		<td class=\"table_cell\"><img src=\"images/icon_point5.gif\" width=\"8\" height=\"11\" border=\"0\">배송업체</td>\n";
				$createbutton.="		<td class=\"td_con1\"><select name=escrow_deli_com style=\"width:90; height:18; font-size:8pt;\">\n";
				$createbutton.="		<option value=\"\">없음</option>\n";

				for($yy=0;$yy<count($delicomlist);$yy++) {
					if($pg_type=="B" && preg_match("/^(Q|P){1}/", $_ord->paymethod)) {
						if(strlen($delicomlist[$yy]->dacom_code)>0) {
							$createbutton.="		<option value=\"".$delicomlist[$yy]->code."\">".$delicomlist[$yy]->company_name."</option>\n";
						}
					} else {
						$createbutton.="		<option value=\"".$delicomlist[$yy]->code."\">".$delicomlist[$yy]->company_name."</option>\n";
					}
				}
				$createbutton.="		</select>\n";
				$createbutton.="		</td>\n";
				$createbutton.="	</tr>\n";
				$createbutton.="	<tr bgcolor=#FFFFFF>\n";
				$createbutton.="		<td class=\"table_cell\"><img src=\"images/icon_point5.gif\" width=\"8\" height=\"11\" border=\"0\">송장번호</td>\n";
				$createbutton.="		<td class=\"td_con1\"><input type=text name=escrow_deli_num value=\"\" size=13 maxlength=20 style=\"height:19;font-size:8pt\"></td>\n";
				$createbutton.="	</tr>\n";
				$createbutton.="	<tr bgcolor=#FFFFFF>\n";
				$createbutton.="		<td height=40 colspan=\"2\" align=\"center\"><a href=\"javascript:delisend('N')\"><img src=\"images/btn_ok3.gif\" border=\"0\"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:escrow_deliclose();\"><img src=\"images/ordtl_close.gif\" border=\"0\"></a></td>\n";
				$createbutton.="	</tr>\n";
				$createbutton.="	</table>\n";
				$createbutton.="	</td>\n";
				$createbutton.="</tr>\n";
				$createbutton.="</form>\n";
				$createbutton.="</table>\n";
				$createbutton.="</div>\n";
				$createbutton.="</td>\n";

				$createbutton.="<td style=padding:0px 2px;><a href=\"javascript:delisend('N')\"><img src=\"images/ordtl_btndeliok.gif\" align=absmiddle border=0></a></td>\n";	//발송완료
			}
		} else if($_ord->deli_gbn=="Y") {	//발송완료된 건에 대해서,,,,,,
			//$createbutton.="<a href=\"javascript:printaddress()\"><img src=\"images/ordtl_btnprint.gif\" align=absmiddle border=0></a>\n";	//운송장 출력
			//$createbutton.="<font color=#C0C0C0>|</font>&nbsp;";
			if(!preg_match("/^(Q|P){1}/",$_ord->paymethod)) $createbutton.="<a href=\"javascript:delicancel()\"><img src=\"images/ordtl_btnreturn.gif\" align=absmiddle border=0></a>\n";	//반송처리
		}
		//무통장 입금완료건에 대해서 주문취소이고 환불이 안된 경우
		if(preg_match("/^(B){1}/",$_ord->paymethod) && $_ord->deli_gbn=="C" && strlen($_ord->bank_date)>=12) {
			$createbutton.="<a href=\"javascript:bankcancel()\"><img src=\"images/ordtl_btnrefund.gif\" align=absmiddle border=0></a>\n";	//환불처리
		} else if(preg_match("/^(O){1}/",$_ord->paymethod) && $_ord->deli_gbn=="C" && $_ord->pay_admin_proc!="C") {
			$createbutton.="<a href=\"javascript:virtualcancel()\"><img src=\"images/ordtl_btnrefund.gif\" align=absmiddle border=0></a>\n";	//환불처리
		} else if(preg_match("/^(V){1}/",$_ord->paymethod) && $_ord->deli_gbn=="C" && $_ord->pay_admin_proc!="C") {
			$createbutton.="<a href=\"javascript:transcancel()\"><img src=\"images/ordtl_btnrefund.gif\" align=absmiddle border=0></a>\n";	//환불처리
		}
		//매매보호 가상계좌/신용카드건에 대해서 (주문취소/환불대기) 상태가 아니면,,,,,
		if(preg_match("/^(Q|P){1}/",$_ord->paymethod) && !preg_match("/^(C|E)$/",$_ord->deli_gbn) && $_ord->price>0 && !preg_match("/^(Y|C)$/",$_ord->escrow_result)) {
			if(preg_match("/^(Y|D)$/",$_ord->deli_gbn) && strlen($_ord->deli_date)==14) {	//발송완료된 에스크로 결제건은 "정산보류" 버튼 활성화
				if($pg_type=="A" || $pg_type=="C" || $pg_type=="D") {
					$createbutton.="<a href=\"javascript:okhold()\"><img src=\"images/ordtl_btnescrowhold.gif\" align=absmiddle border=0></a>\n";	//정산보류
				}
			} else {
				$createbutton.="<a href=\"javascript:okcancel('".substr($_ord->paymethod,0,1)."','".$_ord->bank_date."')\"><img src=\"images/ordtl_btnescrowcancel.gif\" align=absmiddle border=0></a>\n";	//취소처리
			}
		}

		$createbutton.="</td>\n";
		$createbutton.="<td width=\"100%\"><img src=\"images/end_orderstep.gif\" alt=\"\" /></td>";





		//신용카드 정상 결제일 경우
		if(preg_match("/^(C){1}/",$_ord->paymethod) && $_ord->pay_flag=="0000") {
			if($_ord->pay_admin_proc=="N" && strcmp($_ord->pay_flag,"0000")==0) {	//매입요청이 안된 경우
				$createbutton.="<td><a href=\"javascript:card_ask('card_ask','card');\"><img src=\"images/ordtl_btncardok.gif\" align=absmiddle border=0 hspace=2></a></td>\n";	//카드결제요청
				$createbutton.="<td><a href=\"javascript:card_ask('card_cancel','card');\"><img src=\"images/ordtl_btncardcancel.gif\" align=absmiddle border=0 hspace=2></a></td>\n";	//카드취소
				//$createbutton.="&nbsp;&nbsp;&nbsp;<font color=#C0C0C0>|</font>&nbsp;";
			} else if($_ord->pay_admin_proc=="Y") {	//매입요청된 경우
				if(!preg_match("/^(Y|C)$/",$_ord->escrow_result)) {
					$createbutton.="<td><a href=\"javascript:card_ask('card_cancel','card');\"><img src=\"images/ordtl_btncardcancel.gif\" align=absmiddle border=0 hspace=2></a></td>\n";	//카드취소
					//$createbutton.="&nbsp;&nbsp;&nbsp;<font color=#C0C0C0>|</font>&nbsp;";
				}
			}
		//핸드폰 결제일 경우
		} else if (preg_match("/^(M){1}/",$_ord->paymethod) && $_ord->pay_flag=="0000") {

			if($_ord->pay_admin_proc=="N" && strcmp($_ord->pay_flag,"0000")==0) {
				$createbutton.="<td><a href=\"javascript:card_ask('card_cancel','hp')\"><img src=\"images/ordtl_btnpaycancel.gif\" align=absmiddle border=0 hspace=2></a></td>\n";		//결제취소
				//$createbutton.="&nbsp;&nbsp;&nbsp;<font color=#C0C0C0>|</font>&nbsp;";
			}

		//실시간계좌이체/일반가상계좌 환불안내
		} else if (preg_match("/^(V|O){1}/",$_ord->paymethod) && $_ord->pay_flag=="0000" && $_ord->pay_admin_proc!="C") {
			$createbutton.="<td><a href=\"javascript:card_ask('card_cancel','card');\"><img src=\"images/ordtl_btnVcancel.gif\" align=absmiddle border=0 hspace=2></a></td>\n";	//가상계좌취소
			//$createbutton.="<td><a href=\"javascript:alert('실시간계좌이체 및 가상계좌 결제건은 시스템적인 자동 환불처리가 불가하오니\\n\\n주문취소처리 후 적립금으로 지급 또는 무통장 환불처리를 하시기 바랍니다.')\"><img src=\"images/ordtl_refundinfo.gif\" border=0 align=absmiddle></a></td>\n";
			//$createbutton.="&nbsp;<font color=#C0C0C0>|</font>&nbsp;";
		}




		//(미처리/배송요청/발송완료) 되고 카드결제 취소건이 아니면,,,,,
		//if(preg_match("/^(N|X|S)$/",$_ord->deli_gbn) && $_ord->pay_admin_proc!="C") {
		//	$createbutton.="<td><a href=\"javascript:printaddress()\"><img src=\"images/ordtl_btnprint.gif\" align=absmiddle border=0 hspace=2></a></td>\n";	//운송장 출력
		//}
		//신용카드 결제가 승인되었거나 또는 (무통장입금이고, 주문취소가 아니고, 입금이 완료되었고) 또는 (실시간계좌이체이고 정상적으로 결제된건이라면,,,)
		if($_ord->pay_admin_proc=="Y" || (preg_match("/^(B){1}/",$_ord->paymethod) && $_ord->deli_gbn!="C" && strlen($_ord->bank_date)==14) || (preg_match("/^(V|O){1}/",$_ord->paymethod) && $_ord->pay_flag=="0000" && $_ord->deli_gbn!="C")) {
			$createbutton.="<td><a href=\"javascript:printtax()\"><img src=\"images/ordtl_btntax.gif\" align=absmiddle border=0 hspace=2></a></td>\n";	//영수증 발급
		}
		if(preg_match("/^(B|O|Q){1}/",$_ord->paymethod) && $tax_type!="N" && $_ord->deli_gbn!="C") {
			$createbutton.="<td><a href=\"javascript:get_taxsave()\"><img src=\"images/ordtl_btntaxsave.gif\" align=absmiddle border=0 hspace=2></a></td>\n";	//현금영수증 요청
		}



		$createbutton.="</tr>\n";
		$createbutton.="</table>\n";

		echo $createbutton;

?>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr><td height="10"></td></tr>
</table>
<?

	$deliTotSql = "SELECT * FROM tblorderproduct WHERE ordercode = '".$_ord->ordercode."' AND SUBSTR(productcode,1,3) NOT LIKE 'COU%' AND SUBSTR(productcode,1,3) NOT LIKE '999%' ";
	$deliTotResult = mysql_query($deliTotSql);
	$deliTotNums = mysql_num_rows($deliTotResult); //순수 상품 갯수
	mysql_free_result($deliTotResult);


	$deliYesSql = "SELECT * FROM tblorderproduct WHERE ordercode = '".$_ord->ordercode."' AND SUBSTR(productcode,1,3) NOT LIKE 'COU%' AND SUBSTR(productcode,1,3) NOT LIKE '999%' AND deli_gbn = 'Y' ";
	$deliYesResult = mysql_query($deliYesSql);
	$deliYesNums = mysql_num_rows($deliYesResult); // 배송된 상품 수

	mysql_free_result($deliYesResult);

	?>
<table cellpadding="0" cellspacing="0" border="0" width="96%" align="center">
<!-- 배송 처리에 따른 Msg -->
<?if($_ord->deli_gbn != "Y" && ($deliTotNums > 0 && ($deliTotNums == $deliYesNums))){?>
	<tr>
		<td style="padding:5px 17px; background-color:#f5f5f5;">
			<span style="font-size:11px; color:#ff5500; font-weight:bold; letter-spacing:-1px;">
			· 모든 상품이 배송되었으나 <u>처리완료 되지 않은 주문건</u> 입니다.<br />
			· <u>발송완료 처리를 하셔야만</u> 적립금 지급 및 매출확인, 정산처리가 이루어집니다.
			</span>
		</td>
	</tr>
	<tr><td height="20"></td></tr>
<?}?>
<tr>
	<td>
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
			<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
			<input type=hidden name=type>
			<input type=hidden name=mode>
			<input type=hidden name=ordercode value="<?=$_ord->ordercode?>">
			<input type=hidden name=vender>
			<input type=hidden name=productcode>
			<input type=hidden name=opt1_name>
			<input type=hidden name=opt2_name>
			<input type=hidden name=quantity>
			<input type=hidden name=reserve>
			<input type=hidden name=price>
			<input type=hidden name=arquantity>
			<input type=hidden name=arreserve>
			<input type=hidden name=arprice>
			<input type="hidden" name="deliprinfo" />
			<tr>
				<td>
				<?
						echo "	<table border=0 cellpadding=0 cellspacing=0>\n";
						echo "	<tr>\n";
						echo "		<td><img src=\"images/tit_endtrans.gif\" alt=\"\" /></td>";
						echo "		<td> \n";

						$deliStep = array(
							'S'=>"배송대기(발송준비)",
							'Y'=>"배송(송장번호)"
						);
						/*
						echo "			<select name='orderDeliReadyProductobj' id='orderDeliReadyProductobj' onchange=\"orderDeliReadyProductChkAll( this.value );\" style=\"height: 18px; font-size: 8pt;\">";
						//echo "				<option value=''>배송여부 표기 선택</option>";
						foreach( $deliStep as $key=>$var ) {
							echo "			<option value='".$key."'>".$var."</option>";
						}
						echo "			</select>";

						echo "		</td><td style=\"padding-bottom:2\"> \n";
						*/

						echo "			<select name=deli_com id=deli_com style=\"width:90;height:18;font-size:8pt; display:inline;\">\n";
						echo "				<option value=\"\">없음</option>\n";

						for($yy=0;$yy<count($delicomlist);$yy++) {
							if($pg_type=="B" && preg_match("/^(Q|P){1}/", $_ord->paymethod)) {
								if(strlen($delicomlist[$yy]->dacom_code)>0) {
									echo "		<option value=\"".$delicomlist[$yy]->code."\">".$delicomlist[$yy]->company_name."</option>\n";
								}
							} else {
								echo "		<option value=\"".$delicomlist[$yy]->code."\">".$delicomlist[$yy]->company_name."</option>\n";
							}
						}
						echo "			</select>\n";
						echo "		</td><td style=\"padding-left:2px\"> \n";
						echo "			<input type=text name=deli_num id=deli_num value=\"\" size=20 maxlength=20 style=\"height:19;font-size:8pt; display:inline;\" onkeyup=\"strnumkeyup(this)\">\n";
						echo "		</td><td style=\"padding-left:2px\"> \n";
						echo "			<input type=button value='등록' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:18px;width:50\" onclick=\"changeDeliinfo()\">\n";
						echo "		</td>\n";
						echo "	</tr>\n";
						echo "	</table>\n";

				?>
				</td>
				<td align="right">
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td><img src="images/tit_ordersort.gif" alt="" /></td>
							<td>
								<select name=sort style="width:90px; height:18px; font-size:8pt;" onChange="Sort(this.value);">
									<?
										if($_shopdata->ETCTYPE["SELFCODEVIEW"]=="Y" || $_shopdata->ETCTYPE["SELFCODEVIEW"]=="Z" || $_shopdata->ETCTYPE["SELFCODEVIEW"]=="M" || $_shopdata->ETCTYPE["SELFCODEVIEW"]=="N") {
											if(!isset($sort)) {
												$sort="selfcode";
											}
										echo "<option value=\"selfcode\" ".($sort=="selfcode"?"selected":"").">진열코드</option>\n";
										}
									?>
									<option value="" <?=(strlen($sort)==0?"selected":"")?>>장바구니</option>
									<option value="productname" <?=($sort=="productname"?"selected":"")?>>제품명</option>
									<option value="price desc" <?=($sort=="price desc"?"selected":"")?>>가격</option>
								</select>
							</td>
							<?if($_ord->paymethod=="B" && $mode!="update"){?>
							<!--
							<td style="padding-left:2px;"><a href="javascript:ChangeUpdateMode('update')"><img src="images/ordtl_btnpriceup.gif" align=absmiddle border=0></a></td>
							-->
							<?}?>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr><td height="10"></td></tr>
<tr>
	<td align="center">
	<!-- 주문내역 시작 -->
	<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor="#d8d8d8">
	<col width=25></col>
	<?if($vendercnt>0){?>
	<col width=55></col>
	<?}?>
	<col width=55></col>
	<col width=></col>
	<col width=95></col>
	<col width=28></col>
	<col width=45></col>
	<col width=55></col>
	<?=($_ord->paymethod=="B" && $mode=="update"?"<col width=30></col>\n":"")?>
	<tr bgcolor=#efefef>
		<td align=center class="page_print">No</td>
		<td align=center><input type=checkbox name=allcheck onClick="CheckAll()"></td>
		<?if($vendercnt>0){?>
		<td align=center style="font-size:11px; font-weight:bold;">입점업체</td>
		<?}?>
		<td align=center style="font-size:11px; font-weight:bold;">처리상태</td>
		<td align=center style="font-size:11px; font-weight:bold;">상품명</td>
		<td align=center style="font-size:11px; font-weight:bold;">선택사항</td>
		<td align=center style="font-size:11px; font-weight:bold;">수량</td>
		<td align=center style="font-size:11px; font-weight:bold;">적립금</td>
		<td align=center style="font-size:11px; font-weight:bold;">가격</td>
		<?=($_ord->paymethod=="B" && $mode=="update"?"<td>&nbsp;</td>\n":"")?>
	</tr>

	<input type=hidden name=chkprcode>
	<input type=hidden name=chkdeli_com>
	<input type=hidden name=chkdeli_num>
	<input type=hidden name=prcodes>

<?
	$colspan=7;
	if($vendercnt>0) $colspan++;

	$sql = "SELECT *, (select tax_yn from tblproduct where productcode = t.productcode limit 0, 1 ) as tax_yn FROM tblorderproduct t WHERE ordercode='".$_ord->ordercode."' ";
	if(strlen($sort)>0) $sql.="ORDER BY ".$sort.",SUBSTR( productcode, 1, 3 ) >  '888' ";
	else $sql.="ORDER BY SUBSTR( productcode, 1, 3 ) >  '888' , vender ASC";


	//
	$groupdiscount_Percent_result=mysql_query($sql,get_db_conn());
	while($groupdiscount_Percent_row=mysql_fetch_object($groupdiscount_Percent_result)) {
		if ($groupdiscount_Percent_row->productcode != '99999999995X') {
			$totalpriceTemp+=$groupdiscount_Percent_row->quantity*$groupdiscount_Percent_row->price;
		}
	}
	$groupdiscount_Percent = round ( 100 - ( 100 * ( $_ord->price / $totalpriceTemp ) ) ); //회원그룹(추가)할인 비율 %

	$result=mysql_query($sql,get_db_conn());
	$sumquantity=0;
	$totalprice=0;
	$in_reserve=0;
	$cnt=0;
	$taxsaveprname="";
	$arTotalSns = null;
	$totalRecom = 0;
	$sell_memid="";
	$sell_memid_reserve="";
	$ex_qty = array();
	$ex_reserve = array();
	$ex_price = array();
	while($row=mysql_fetch_object($result)) {


		$csManager = "";
		if($row->vender>0) {
			$csManager = "<input type=button value='CS관리' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma; height:18px; width:50px\" onclick=\"csManagerPop('".$_ord->ordercode."','".$row->productcode."','".$row->vender."'); return false;\">";
		}
		/*
		if(ereg("^(COU)([0-9]{10})(X)$",$row->productcode)) {				#쿠폰
			//if($row->price!=0 && $row->price!=NULL) {
				$etcdata[]=$row;
				continue;
			//}
		} else if(ereg("^(9999999999)([0-9]{1})(X|R)$",$row->productcode)) {
			$etcdata[]=$row;
			continue;
		*/
		if(!preg_match('/^[0-8]{1}[0-9]{2}$/',substr($row->productcode,0,3)) && substr($row->productcode,-4,4) != 'GIFT'){

			if ($row->productcode=='99999999995X') {
				$refund_data[]=$row;
			}else{
				$etcdata[]=$row;
			}
			continue;
		} else {															#진짜상품
			$prdata[]=$row;
		}

		$taxsaveprname.=$row->productname.",";
		$cnt++;
		$sumprice=$row->quantity*$row->price;
		$reserve=$row->quantity*$row->reserve;
		$tempopt1=$row->opt1_name;
		if($row->productcode=="99999999999R") $norecan="Y";
		if ($row->productcode!="99999999999X" && substr($row->productcode,0,3)!="COU" && $row->productcode!="99999999999R") {
			$sumquantity+=$row->quantity;
		}
		if($row->status!='RC') $in_reserve+=$reserve;
		$totalprice+=$sumprice;

		if(substr($row->status,0,1)=='E') {
			$ex_qty[$row->uid] = $row->quantity;
			$ex_reserve[$row->uid] = $reserve;
			$ex_price[$row->uid] = $sumprice;
		}

		$optvalue="";
		if(ereg("^(\[OPTG)([0-9]{3})(\])$",$row->opt1_name)) {
			$optioncode=$row->opt1_name;
			$row->opt1_name="";
			$sql = "SELECT opt_name FROM tblorderoption WHERE ordercode='".$_ord->ordercode."' AND productcode='".$row->productcode."' ";
			$sql.= "AND opt_idx='".$optioncode."' ";
			$result2=mysql_query($sql,get_db_conn());
			if($row2=mysql_fetch_object($result2)) {
				$optvalue=$row2->opt_name;
			}
			mysql_free_result($result2);
		}

		$assemblestr = "";
		$packagestr = "";
		if(($_ord->paymethod!="B" || $mode!="update") && strlen(str_replace("","",str_replace(":","",str_replace("=","",$row->assemble_info))))>0) {
			$assemble_infoall_exp = explode("=",$row->assemble_info);

			if($row->package_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))>0) {
				$package_info_exp = explode(":", $assemble_infoall_exp[0]);

				$package_productcode_exp = explode("", $package_info_exp[0]);
				$package_productname_exp = explode("", $package_info_exp[1]);
				$package_sellprice = $package_info_exp[2];
				$package_packagename = $package_info_exp[3];

				if(count($package_info_exp)>2 && strlen($package_packagename)>0) {
					$packagestr.="	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
					$packagestr.="	<tr>\n";
					$packagestr.="		<td colspan=\"2\" style=\"word-break:break-all;font-size:8pt;\"><font color=green><b>[</b>패키지선택 : ".$package_packagename."<b>]</b></font></td>\n";
					$packagestr.="	</tr>\n";
					if(strlen(str_replace("","",$package_info_exp[1]))>0) {
						$packagestr.="	<tr>\n";
						$packagestr.="		<td width=\"30\" valign=\"top\" nowrap><font color=\"#008000\" style=\"line-height:10px;\">│<br>└▶</font></td>\n";
						$packagestr.="		<td width=\"100%\" bgcolor=\"#DDDDDD\">\n";
						$packagestr.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\">\n";
						$packagestr.="		<col width=\"\"></col>\n";
						$packagestr.="		<col width=\"55\"></col>\n";
						for($k=0; $k<count($package_productname_exp); $k++) {
							if($k==0) {
								$packagestr.="		<tr bgcolor=\"#FFFFFF\">\n";
								$packagestr.="				<td style=\"padding-left:4px;padding-right:4px;word-break:break-all;font-size:8pt;\">".$package_productname_exp[$k]."&nbsp;<span class=\"page_screen\"><a href=\"javascript:ProductInfo('".substr($package_productcode_exp[$k],0,12)."','".$package_productcode_exp[$k]."','YES')\"><img src=images/ordtl_icnnewwin.gif align=absmiddle border=0 vspace=\"1\"></a></span></td>\n";
								$packagestr.="				<td rowspan=\"".count($package_productname_exp)."\" align=\"right\" style=\"padding-left:4px;padding-right:4px;font-size:8pt;\">".number_format((int)$package_sellprice)."</td>\n";
								$packagestr.="		</tr>\n";
							} else {
								$packagestr.="		<tr bgcolor=\"#FFFFFF\">\n";
								$packagestr.="				<td style=\"padding-left:4px;padding-right:4px;word-break:break-all;font-size:8pt;\">".$package_productname_exp[$k]."&nbsp;<span class=\"page_screen\"><a href=\"javascript:ProductInfo('".substr($package_productcode_exp[$k],0,12)."','".$package_productcode_exp[$k]."','YES')\"><img src=images/ordtl_icnnewwin.gif align=absmiddle border=0 vspace=\"1\"></a></span></td>\n";
								$packagestr.="		</tr>\n";
							}
						}

						$packagestr.="		</table>\n";
						$packagestr.="		</td>\n";
						$packagestr.="	</tr>\n";
					}
					$packagestr.="	</table>\n";
				}
				@mysql_free_result($alproresult);
			}

			if($row->assemble_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[1])))>0) {
				$assemblestr.="	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
				$assemblestr.="	<tr height=\"2\"><td></td></tr>\n";
				$assemblestr.="	<tr>\n";
				$assemblestr.="		<td width=\"30\" valign=\"top\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">│<br>└▶</font></td>\n";
				$assemblestr.="		<td width=\"100%\" bgcolor=\"#DDDDDD\">\n";
				$assemblestr.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\">\n";

				$assemble_info_exp = explode(":", $assemble_infoall_exp[1]);

				if(count($assemble_info_exp)>2) {
					$assemble_productcode_exp = explode("", $assemble_info_exp[0]);
					$assemble_productname_exp = explode("", $assemble_info_exp[1]);
					$assemble_sellprice_exp = explode("", $assemble_info_exp[2]);

					for($k=0; $k<count($assemble_productname_exp); $k++) {
						$assemblestr.="		<col width=\"\"></col>\n";
						$assemblestr.="		<col width=\"55\"></col>\n";
						$assemblestr.="		<tr bgcolor=\"#FFFFFF\">\n";
						$assemblestr.="				<td style=\"padding-left:4px;padding-right:4px;word-break:break-all;font-size:8pt;\">".$assemble_productname_exp[$k]."&nbsp;<span class=\"page_screen\"><a href=\"javascript:ProductInfo('".substr($assemble_productcode_exp[$k],0,12)."','".$assemble_productcode_exp[$k]."','YES')\"><img src=images/ordtl_icnnewwin.gif align=absmiddle border=0></a></span></td>\n";
						$assemblestr.="				<td align=\"right\" style=\"padding-left:4px;padding-right:4px;font-size:8pt;\">".number_format((int)$assemble_sellprice_exp[$k])."</td>\n";
						$assemblestr.="		</tr>\n";
					}
				}
				@mysql_free_result($alproresult);
				$assemblestr.="		</table>\n";
				$assemblestr.="		</td>\n";
				$assemblestr.="	</tr>\n";
				$assemblestr.="	</table>\n";
			}
		}



		// 사용 쿠폰 리스트 시작 -----------------------------------------------------
		$ordercouponView = "";
		$couponDcPrice = 0;
		$ordercouponSQL = "
			SELECT
				o.*,
				c.coupon_name,
				c.sale_money,
				c.sale_type
			FROM
				`tblordercoupon` AS o
				LEFT JOIN `tblcouponinfo` AS c ON o.couponcode = c.coupon_code
			WHERE `ordercode` = '".$_ord->ordercode."' AND `orderPuid`='".$row->uid."' ; ";
		$ordercouponResult = mysql_query($ordercouponSQL,get_db_conn());
		while( $ordercouponRow = mysql_fetch_assoc ( $ordercouponResult ) ){
			//_pr($ordercouponRow);
			$sale_typeA = ( $ordercouponRow['sale_type'] <= 2 ? "%" : "원" );
			$sale_typeB = ( $ordercouponRow['sale_type'] %2 == 0 ? "할인" : "적립" );
			$couponPrice = ( $ordercouponRow['sale_type'] %2 == 0 ? $ordercouponRow['dcPrice'] : $ordercouponRow['reserve'] );

			$couponDcPrice += $ordercouponRow['dcPrice'];

			// 출력용

			$ordercouponView .= "<DIV style=\"border-top:solid #DDDDDD 1px;  padding:5px;\">";
			$ordercouponView .= $ordercouponRow['coupon_name'];
			$ordercouponView .= " - ";
			$ordercouponView .= number_format($ordercouponRow['sale_money']).$sale_typeA .$sale_typeB."쿠폰 사용 : ".number_format($couponPrice)."원 ".$sale_typeB;
			$ordercouponView .= "</DIV>";

		}
		// 사용 쿠폰 리스트 끝 ----------------------------------------------------- //

		$couponDcPrice += $sumprice - ( round( ( $sumprice - ( $sumprice / 100 ) * $groupdiscount_Percent ) / 100 ) * 100 ) ;


		echo "<tr bgcolor=#FFFFFF>\n";

		echo "	<td align=center class=\"page_print\" style=\"font-size:8pt\"><font color=#878787>".$cnt."</td>\n";
		echo "	<td align=center class=\"page_screen\" style=\"font-size:8pt\">";
		if($row->productcode!='99999990GIFT') {
			echo "<input type=checkbox name=chkprcode value=\"".$row->productcode."\" uid=\"".$row->uid."\" statuss=\"".$row->status."\" price=\"".$sumprice."\" dcPrice=\"".$couponDcPrice."\">";

		echo "<input type=\"hidden\" name=\"chkprcode_uid\" value=\"".$row->uid."\" style=\"display:none\" />";
		echo "<input type=\"hidden\" name=\"chkprcode_statuss\" value=\"".$row->status."\" style=\"display:none\" />";
		echo "<input type=\"hidden\" name=\"chkprcode_price\" value=\"".$sumprice."\" style=\"display:none\" />";
		}

		echo "&nbsp;</td>\n";

		if($vendercnt>0) {
			$classred1=$classred2 = '';

			if($row->vender>0) {
				echo "	<td align=center style=\"font-size:8pt\"><a href=\"javascript:viewVenderInfo(".$row->vender.")\"><B>".$venderlist[$row->vender]->id."</B></a></td>\n";
			} else {
				echo "	<td align=center>&nbsp;</td>\n";
			}
		}

		if($row->status) {
				echo "	<td align=center style=\"color:red\">";
				switch($row->status) {
					case 'EA': echo "교환신청";  break;
					case 'EB': echo "교환접수";  break;
					case 'EC': echo "교환완료";  break;
					case 'RA':
						echo "환불신청";

						$sql = "select requestor from part_cancel_want where uid='".$row->uid."'";
						$result2=mysql_query($sql,get_db_conn());
						if($row2=mysql_fetch_object($result2)) {

							if ($row2->requestor ==1) {
								 echo "<br/><span style=\"color:blue\">(고객)</span>";
							}
						}
						mysql_free_result($result2);


						break;
					case 'RB': echo "환불접수";  break;
					case 'RC': echo "환불완료";  break;
				}
				echo "</td>";
				$classred1 = "<font style='color:red'>";
				$classred2 = "</font>";
		}else{
			echo "	<td align=center></td>";
		}

		if(file_exists($Dir.DataDir."shopimages/product/".$row->productcode."3.gif")) $file=$row->productcode."3.gif";
		else if(file_exists($Dir.DataDir."shopimages/product/".$row->productcode."3.jpg")) $file=$row->productcode."3.jpg";
		else $file="NO";

		// 체험 상품 표시를 위해
		if(substr($row->productcode,0,3) == '899') $row->productname = '[체험상품]'.$row->productname;

		if($file!="NO") {

			//미리보기 이미지
			$prImg = ( is_file($Dir.DataDir."shopimages/product/".$file) ) ? "<img name=bigimgs src=\"".$Dir.DataDir."shopimages/product/".$file."\" height=\"40\" height=\"40\" align=\"middle\" border=\"5\">" : "";

			echo "	<td style=\"font-size:8pt;padding:7,7,5,5;line-height:10pt\">\n";
			echo "	".(strlen($row->selfcode)?"진열코드 : ".$row->selfcode."<br>":"")."<span style=\"line-height:10pt\" onMouseOver='ProductMouseOver($cnt)' onMouseOut=\"ProductMouseOut('primage".$cnt."');\">".$prImg.$classred1.$row->productname.$classred2."";
			echo "	<span class=\"page_screen\"><a href=\"javascript:ProductInfo('".substr($row->productcode,0,12)."','".$row->productcode."','YES')\"><img src=images/ordtl_icnnewwin.gif align=absmiddle border=0></a></span>";
			echo "</span>\n";
			echo "	<div id=primage".$cnt." style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
			echo "	<table border=0 cellspacing=1 cellpadding=0 bgcolor=#000000 width=170>\n";
			echo "	<tr bgcolor=#FFFFFF>\n";
			echo "		<td align=center width=100% height=150><img name=bigimgs src=\"".$Dir.DataDir."shopimages/product/".$file."\"></td>\n";
			echo "	</tr>\n";
			echo "	<tr bgcolor=#FFFFFF>\n";
			echo "		<td height=54 bgcolor=#f5f5f5><table border=0><tr><td style=\"line-height:12pt\">예전 주문서,삭제/이동 상품은 이미지가 일치하지 않을수 있으니 <font color=red>주의하여 배송</font>바랍니다.</td></tr></table></td>\n";
			echo "	</tr>\n";
			echo "	</table>\n";
			echo "	</div>\n";
			if(strlen($row->addcode)>0) echo "<br><font color=blue><b>[</b>특수표시 : ".$row->addcode."<b>]</b></font>";
			if(strlen($optvalue)>0) echo "<br><font color=red><b>[</b>옵션사항 : ".$optvalue."<b>]</b></font>";
			echo $packagestr;
			echo $assemblestr;


			// 주문 상품 고유 코드 echo $row->uid;


			// 주문 상품에 사용한 상품권 정보
			echo "<div>".$ordercouponView."</div>";



			echo "	</td>\n";
		} else {
			echo "	<td style=\"font-size:8pt;padding:2,5;line-height:10pt\">";
			echo (strlen($row->selfcode)?"진열코드 : ".$row->selfcode."<br>":"").$classred1.$row->productname.$classred2;
			if(strlen($row->addcode)>0) echo "<br><font color=blue><b>[</b>특수표시 : ".$row->addcode."<b>]</b></font>";
			if(strlen($optvalue)>0) echo "<br><font color=red><b>[</b>옵션사항 : ".$optvalue."<b>]</b></font>";
			echo $packagestr;
			echo $assemblestr;

			if($row->productcode=='99999990GIFT'){
				echo "<br>고객요청사항 : {$row->assemble_info}";
			}
			echo "	</td>\n";
		}

		echo "	<td style=\"font-size:8pt;padding:2,5;line-height:11pt\">";
		echo (strlen($row->opt1_name)>0?$classred1.$row->opt1_name.$classred2."<br>":"&nbsp;");
		echo (strlen($row->opt2_name)>0?$classred1.$row->opt2_name.$classred2."<br>":"&nbsp;");
		echo (strlen($row->opt3_name)>0?$row->opt3_name."<br>":"&nbsp;");
		if(strlen($row->opt4_name)>0) echo $row->opt4_name;
		echo "	</td>\n";
		if ($row->productcode=="99999999999X" || substr($row->productcode,0,3)=="COU" || $row->productcode=="99999999999R") { // 현금결제면 수량표시안함
			echo "	<td><input type=hidden name=arquantity value=\"1\">&nbsp;</td>\n";
		} else {
			echo "	<td align=center style=\"font-size:8pt\" ".($_ord->paymethod!="B" || $mode!="update"?($row->quantity>1?" bgcolor=#FDE9D5><font color=#000000><b>":">").$classred1.$row->quantity.$classred2:"><input type=text style='text-align:right' name=arquantity value=\"".$row->quantity."\" style=\"font-size:8pt;width:100%\">")."</td>\n";
		}
		echo "	<td align=right style=\"font-size:8pt\">".(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X" && substr($row->productcode,-4)!="GIFT"?($_ord->paymethod!="B" || $mode!="update"?$classred1.number_format($reserve).$classred2."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=arreserve value=\"".$reserve."\">"):"<input type=hidden name=arreserve>&nbsp;")."</td>\n";
		echo "	<td align=right style=\"font-size:8pt\">".(substr($row->productcode,-4)!="GIFT"?$_ord->paymethod!="B" || $mode!="update"?$classred1.number_format($sumprice).$classred2."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=arprice value=\"".$sumprice."\">":"&nbsp;<input type=hidden name=arprice>");

		if ($row->tax_yn =="1") {
			echo "<br/><span style=\"color:red;\">(비과세)</span>";
		}

		echo "</td>\n";


		if($_ord->paymethod=="B" && $mode=="update") {
			echo "<td align=center><a href=\"javascript:OrderUpdate('1',".$cnt.",'".$row->vender."','".$row->productcode."','".$tempopt1."','".$row->opt2_name."')\"><img src='images/ordtl_miniup.gif' align=absmiddle border=0 alt='상품수정'></a><br><img width=0 height=2 border=0><br><a href=\"javascript:OrderDelete('1','".$row->vender."','".$row->productcode."','".$tempopt1."','".$row->opt2_name."')\"><img src='images/ordtl_minidel.gif' align=absmiddle border=0 alt='상품삭제'></a></td>\n";
		}
		echo "</tr>\n";

		if($_ord->paymethod!="B" || $mode!="update") {
			echo "<tr bgcolor=#FFFFFF style=\"font-size:8pt\">\n";
			echo "	<td align=right style=\"padding:2,5\" colspan=".($_ord->paymethod!="B" || $mode!="update"?$colspan:($colspan+1)).">\n";
			if($row->productcode!='99999990GIFT') {
				echo "	<table border=0 cellpadding=0 cellspacing=0>\n";
				echo "	<tr>\n";
				echo "		<td style=\"padding-left:20\" style=\"font-size:8pt;letter-spacing:-0.5pt;\">".$csManager." &nbsp;&nbsp;&nbsp; 배송업체 : \n";
				echo "		<span class=\"page_screen\">\n";
				$deli_url="";
				$trans_num="";
				$company_name="";
				for($yy=0;$yy<count($delicomlist);$yy++) {
					if($pg_type=="B" && preg_match("/^(Q|P){1}/", $_ord->paymethod)) {
						if(strlen($delicomlist[$yy]->dacom_code)>0) {
							//echo "		<option value=\"".$delicomlist[$yy]->code."\"";
							if($row->deli_com>0 && $row->deli_com==$delicomlist[$yy]->code) {
								//echo " selected";
								$deli_url=$delicomlist[$yy]->deli_url;
								$trans_num=$delicomlist[$yy]->trans_num;
								$company_name=$delicomlist[$yy]->company_name;
							}
							echo ">".$delicomlist[$yy]->company_name."</option>\n";
						}
					} else {
						//echo "		<option value=\"".$delicomlist[$yy]->code."\"";
						if($row->deli_com>0 && $row->deli_com==$delicomlist[$yy]->code) {
							//echo " selected";
							$deli_url=$delicomlist[$yy]->deli_url;
							$trans_num=$delicomlist[$yy]->trans_num;
							$company_name=$delicomlist[$yy]->company_name;
						}
						//echo ">".$delicomlist[$yy]->company_name."</option>\n";
					}
				}
				echo (strlen($company_name)>0?$company_name:"없음");
				echo "		</span>\n";

				echo "		<span class=\"page_print\">".(strlen($company_name)>0?$company_name:"없음")."</span>\n";
				echo "		</td>\n";
				echo "		<td style=\"font-size:8pt; letter-spacing:-0.5pt; padding-left:5px; padding-right:5px;\">송장번호 : \n";
				echo "		<span class=\"page_screen\">\n";
				echo "		".(strlen($row->deli_num)>0?$row->deli_num:"없음")."<img width=2 height=0>";
				if(strlen($row->deli_num)>0 && strlen($deli_url)>0) {
					if(strlen($trans_num)>0) {
						$arrtransnum=explode(",",$trans_num);
						$pattern=array("(\[1\])","(\[2\])","(\[3\])","(\[4\])");
						$replace=array(substr($row->deli_num,0,$arrtransnum[0]),substr($row->deli_num,$arrtransnum[0],$arrtransnum[1]),substr($row->deli_num,$arrtransnum[0]+$arrtransnum[1],$arrtransnum[2]),substr($row->deli_num,$arrtransnum[0]+$arrtransnum[1]+$arrtransnum[2],$arrtransnum[3]));
						$deli_url=preg_replace($pattern,$replace,$deli_url);
					} else {
						$deli_url.=$row->deli_num;
					}
					echo "</td><td><input type=button value='추적' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:18px;width:30\" onclick=\"DeliSearch('".$deli_url."')\">";
				} else {
					echo "</td><td><input type=button value='추적' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:18px;width:30\">";
				}
				echo "		</span></td>\n";
				echo "		<td><span class=\"page_print\">".(strlen($row->deli_num)>0?$row->deli_num:"없음")."</span></td>\n";
				echo "		<td style=\"padding-left:10px; font-size:8pt; letter-spacing:-0.5pt;\">\n";
				echo "	배송상태 : <B>";

				if($row->status == 'RC' ) {
					echo "<font color=red>환불완료</font>";
				}else{
					switch($row->deli_gbn) {
						case 'S': echo "발송준비";  break;
						case 'X': echo "배송요청";  break;
						case 'Y': echo "<font color=blue>배송</font>";  break;
						case 'D': echo "<font color=blue>취소요청</font>";  break;
						case 'N': echo "미처리";  break;
						case 'E': echo "<font color=red>환불대기</font>";  break;
						case 'C': echo "<font color=red>주문취소</font>";  break;
						case 'R': echo "반송";  break;
						case 'H': echo "배송(<font color=red>정산보류</font>)";  break;
					}
					if($row->deli_gbn=="D" && strlen($row->deli_date)==14) echo " (배송)";
				}

				echo "	</B>";
				echo "		</td>\n";
				echo "	</tr>\n";
				echo "	</table>\n";
			}
			echo "	</td>\n";
			echo "</tr>\n";
		}

		$sql = "SELECT sns_reserve1,sns_reserve1_type,first_reserve,first_reserve_type FROM tblproduct WHERE productcode='".$row->productcode."'";
		$result2=mysql_query($sql,get_db_conn());
		if($row2=mysql_fetch_object($result2)) {
			$sns_reserve1=$row2->sns_reserve1;
			$sns_reserve1_type=$row2->sns_reserve1_type;
			$first_reserve=$row2->first_reserve;
			$first_reserve_type=$row2->first_reserve_type;
		}
		mysql_free_result($result2);


		//추천인 회원가입 후 구매시 적립금
		if($recom_ok =="Y" && $arRecomType[0] == "B"){
			if($arRecomType[1] =="A"){
				if($arRecomType[2] == "N"){
					$totalRecom += $recom_memreserve;
				}else{
					$totalRecom += getReserveConversion($recom_memreserve,$arRecomType[2],$sumprice,"N");
				}
			}else{
				$totalRecom += getReserveConversion($first_reserve,$first_reserve_type,$sumprice,"N");
			}
		}

		//sns 홍보id
		if(strlen($row->sell_memid)>0 && $sns_ok =="Y" ){
			if($arSnsType[0] == "A"){
				$tempSns =getReserveConversion($sns_recomreserve,$arSnsType[1],$sumprice,"N");
			}else if($arSnsType[0] == "B"){
				$tempSns =getReserveConversion($sns_reserve1,$sns_reserve1_type,$sumprice,"N");
			}else if($arSnsType[0] == "N"){
				$tempSns =0;
			}
			//echo $sns_recomreserve."==".$sns_reserve1_type."==".$sumprice."==".$row->sell_memid."===========".$tempSns."<br>";
			$arTotalSns[$row->sell_memid] += $tempSns;
		}
	}
	mysql_free_result($result);
		echo "</table>\n";


	if($mode!="update") {
		echo "	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
		echo "	<tr>\n";
		if(preg_match("/^(N|X|S)$/",$_ord->deli_gbn) && $_ord->pay_admin_proc!="C" && $prescd=="Y") {
			echo "	<td><img src=images/tit_orderstepedit.gif border=0></td>\n";
			echo "	<td><select name=deli_gbn style=\"height:18; font-size:8pt;\" onchange=\"changeDeli(this)\">\n";
			echo "	<option value=\"\">처리상태 선택</option>\n";
			echo "	<option value=\"N\">미처리</option>\n";
			echo "	<option value=\"S\">발송준비</option>\n";
			echo "	<option value=\"Y\">발송완료</option>\n";
			//echo "	<option value=\"R\">환불신청</option>\n";
			echo "	</select></td>\n";
			echo "	<td width=100%><img src=\"images/end_orderstep.gif\" alt=\"\" /></td>";
		}
		//echo "		&nbsp;\n";
		//echo "		<A HREF=\"javascript:changeDeliinfo()\"><img src=images/btn_deliinfomodify.gif border=0 align=absmiddle></A>\n";

		if($_ord->deli_gbn=='Y' || $_ord->deli_gbn=='H') {
				/* 2013-02-12 교환 / 환불 - 주석
				echo "
					<a href='#' onclick='rRefundNew2();return false;'>동일건에대해 교환신청</a>
					<a href='#' onclick='rRefundNew();return false;'>환불신청</a>
				";
				*/
		}
		echo "	</tr>\n";
		echo "	</table>\n";
	}
?>
<!--배송 처리에 따른 Msg -->
<?if($_ord->deli_gbn != "Y" && ($deliTotNums > 0 && ($deliTotNums == $deliYesNums))){?>
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr><td height="20"></td></tr>
		<tr>
			<td style="padding:5px 17px; background-color:#f5f5f5;">
				<span style="font-size:11px; color:#ff5500; font-weight:bold; letter-spacing:-1px;">
				· 모든 상품이 배송되었으나 <u>처리완료 되지 않은 주문건</u> 입니다.<br />
				· <u>발송완료 처리를 하셔야만</u> 적립금 지급 및 매출확인, 정산처리가 이루어집니다.
				</span>
			</td>
		</tr>
		<tr><td height="20"></td></tr>
	<table>
<?}?>
<?

	//echo "<tr height=30 bgcolor=#efefef>\n";
	echo "<br/>";
	echo "<div style='width:100%;text-align:left;'><p style='font-weight:bold;font-size:10pt;'>* 최초 주문시 결제 정보(할인, 적립, 배송비 등 추가 내역 포함</p></div>";

	echo "<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=\"#e8e8e8\" style=\"margin-top:5px;\">";
	echo "<col width=25></col>";
	if($vendercnt>0){ echo "<col width=55></col>"; }
	echo"
		<col width=55></col>
		<col width=></col>
		<col width=95></col>
		<col width=28></col>
		<col width=45></col>
		<col width=55></col>
	";
	//echo "<tr><td bgcolor=#efefef align=center colspan=".($_ord->paymethod!="B" || $mode!="update"?$colspan:($colspan+1))."><span style=\"font-size:11px; font-weight:bold;\">추가비용/할인/적립내역</span></td></tr>\n";
	echo "<tr>
			<td bgcolor=#efefef align=center></td>
			<td bgcolor=#efefef align=center><span style=\"font-size:11px; font-weight:bold;\">입점업체</span></td>
			<td bgcolor=#efefef align=center><span style=\"font-size:11px; font-weight:bold;\">처리상태</span></td>
			<td bgcolor=#efefef align=center colspan=".($_ord->paymethod!="B" || $mode!="update"?$colspan-3:($colspan))."><span style=\"font-size:11px; font-weight:bold;\">쿠폰 및 적립 등 할인내역 / 배송비 등 추가 비용 내역</span></td>
		</tr>\n";

	if(count($etcdata)>0) {
		for($j=0;$j<count($etcdata);$j++) {

			/*if( substr($etcdata[$j]->productcode,0,3)=="COU" ){
				continue;
			}*/
			$cnt++;
			$sumprice=$etcdata[$j]->price;
			$reserve=$etcdata[$j]->reserve;
			$productcode=$etcdata[$j]->productcode;
			$in_reserve+=$reserve;
			$totalprice+=$sumprice;
			echo "<tr bgcolor=#ffffff>\n";

			if	($productcode=="99999999990X" ) { //|| substr($etcdata[$j]->productcode,0,3)=="COU"

				echo "	<td><input type=checkbox name=chkprcode value=\"".$etcdata[$j]->productcode."\" uid=\"".$etcdata[$j]->uid."\" statuss=\"".$etcdata[$j]->status."\" price=\"".$sumprice."\" /></td>\n";

				echo "<input type=\"hidden\" name=\"chkprcode_uid\" value=\"".$etcdata[$j]->uid."\" style=\"display:none\" />";
				echo "<input type=\"hidden\" name=\"chkprcode_statuss\" value=\"".$etcdata[$j]->status."\" style=\"display:none\" />";
				echo "<input type=\"hidden\" name=\"chkprcode_price\" value=\"".$sumprice."\" style=\"display:none\" />";

			}else{
				echo "	<td>&nbsp;</td>\n";
			}

			if($vendercnt>0) {
				if($etcdata[$j]->vender>0) {
					echo "	<td align=center style=\"font-size:8pt\"><a href=\"javascript:viewVenderInfo(".$etcdata[$j]->vender.")\"><B>".$venderlist[$etcdata[$j]->vender]->id."</B></a></td>\n";
				} else {
					echo "	<td align=center>&nbsp;</td>\n";
				}
			}
			echo "	<td bgcolor=#ffffff align=center style=\"color:red\">";

			if ($productcode=="99999999990X") {
				switch($etcdata[$j]->status) {
					case 'EA': echo "교환신청";  break;
					case 'EB': echo "교환접수";  break;
					case 'EC': echo "교환완료";  break;
					case 'RA':
						echo "환불신청";

							$sql = "select requestor from part_cancel_want where uid='".$etcdata[$j]->uid."'";
							$result2=mysql_query($sql,get_db_conn());
							if($row2=mysql_fetch_object($result2)) {

								if ($row2->requestor ==1) {
									 echo "<br/><span style=\"color:blue\">(고객)</span>";
								}
							}
							mysql_free_result($result2);
						break;
					case 'RB': echo "환불접수";  break;
					case 'RC': echo "환불완료";  break;
				}
			}else if(substr($etcdata[$j]->productcode,0,3)=="COU"){
				switch($etcdata[$j]->status) {
					case 'RC': echo "쿠폰취소";  break;
				}
			}


			$etcdata[$j]->order_prmsg = str_replace(", ","<br />",$etcdata[$j]->order_prmsg);

			echo " </td>\n";
			echo "	<td bgcolor=#ffffff style=\"font-size:8pt;padding:2,5;line-height:10pt\">".$etcdata[$j]->productname." <span class=\"page_screen\"><A style=\"cursor:hand\" onMouseOver='EtcMouseOver($cnt)' onMouseOut=\"EtcMouseOut($cnt);\"><img src=images/btn_more02.gif border=0 align=absmiddle></A>";
			echo "	<div id=etcdtl".$cnt." style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
			echo "	<table border=0 cellpadding=0 cellspacing=0 width=300 bgcolor=#A47917>\n";
			echo "	<tr><td align=center style=\"color:#FFFFFF;padding:5\"><B>###### 해당 상품명 ######</B></td></tr>\n";
			echo "	<tr><td style=\"font-size:8pt;color:#FFFFFF;padding:10;padding-top:0;line-height:11pt\">".$etcdata[$j]->order_prmsg."</td></tr>\n";
			echo "	</table>\n";
			echo "	</div>\n";
			echo "	</span>\n";
			echo "	</td>\n";
			echo "	<td bgcolor=#ffffff>&nbsp;</td>";


			if ($etcdata[$j]->productcode=="99999999999X" || $etcdata[$j]->productcode=="99999999990X" || $etcdata[$j]->productcode=="99999999997X" || substr($etcdata[$j]->productcode,0,3)=="COU" || $etcdata[$j]->productcode=="99999999999R") { // 현금결제면 수량표시안함
				echo "	<td><input type=hidden name=arquantity value=\"1\">&nbsp;</td>\n";
			} else {
				echo "	<td align=center".($_ord->paymethod!="B" || $mode!="update"?($etcdata[$j]->quantity>1?" bgcolor=#FDE9D5 style=\"font-size:8pt\"><font color=#000000><b>":">").$etcdata[$j]->quantity:"><input type=text style='font-size:8pt;text-align:right' name=arquantity value=\"".$etcdata[$j]->quantity."\" style=\"width:100%\">")."</td>\n";
			}
			echo "	<td align=right style=\"font-size:8pt\">";
			if($etcdata[$j]->vender>0) {
				if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X" && $etcdata[$j]->productcode!="99999999990X" && $etcdata[$j]->productcode!="99999999997X") {
					if($_ord->paymethod!="B" || $mode!="update") {
						echo ($reserve>0?number_format($reserve):"")."&nbsp;";
					} else {
						echo "<input type=hidden name=arreserve>&nbsp;";
					}
				} else {
					echo "<input type=hidden name=arreserve>&nbsp;";
				}
			} else {
				if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X" && $etcdata[$j]->productcode!="99999999990X" && $etcdata[$j]->productcode!="99999999997X") {
					if($_ord->paymethod!="B" || $mode!="update") {
						echo ($reserve>0?number_format($reserve):"")."&nbsp;";
					} else {
						echo "<input type=text style='font-size:8pt;text-align:right;width:100%' name=arreserve value=\"".$reserve."\">";
					}
				} else {
					echo "<input type=hidden name=arreserve>&nbsp;";
				}
			}
			echo "	</td>\n";

			echo "	<td align=right style=\"font-size:8pt\">".(substr($etcdata[$j]->productcode,-4)!="GIFT"?$_ord->paymethod!="B" || $mode!="update"?number_format($sumprice)."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=arprice value=\"".$sumprice."\">":"&nbsp;<input type=hidden name=arprice>")."</td>\n";

			if($_ord->paymethod=="B" && $mode=="update") {
				echo "<td align=center style=\"font-size:8pt\"><a href=\"javascript:OrderUpdate('1',".$cnt.",'".$etcdata[$j]->vender."','".$etcdata[$j]->productcode."','','')\"><img src='images/ordtl_miniup.gif' align=absmiddle border=0 alt='상품수정'></a><br><img width=0 height=2 border=0><br><a href=\"javascript:OrderDelete('1','".$etcdata[$j]->vender."','".$etcdata[$j]->productcode."','','')\"><img src='images/ordtl_minidel.gif' align=absmiddle border=0 alt='상품삭제'></a></td>\n";
			}
			echo "</tr>\n";
		}
	}

	if($_ord) {
		// 회원일경우 회원정보(회원메모)를 가져온다.
		if (strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
			$sql = "SELECT memo,group_code,rec_id FROM tblmember WHERE id='".$_ord->id."' ";
			$result=mysql_query($sql,get_db_conn());
			if ($row=mysql_fetch_object($result)) {
				$usermemo=$row->memo;
				$group_code=$row->group_code;
				$fist_rec_id=trim($row->rec_id);
			}
			mysql_free_result($result);

			if(strlen($group_code)>0) {
				$sql = "SELECT group_name FROM tblmembergroup WHERE group_code='".$group_code."' ";
				$result=mysql_query($sql,get_db_conn());
				if($row=mysql_fetch_object($result)) {
					$group_name = $row->group_name;
				}
				mysql_free_result($result);
			}
		}

		$dc_price=(int)$_ord->dc_price;
		$salemoney=0;
		$salereserve=0;
		if($dc_price<>0) {
			if($dc_price>0) $salereserve=$dc_price;
			else $salemoney=-$dc_price;
			if (strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
				$sql = "SELECT b.group_name FROM tblmember a, tblmembergroup b ";
				$sql.= "WHERE a.id='".$_ord->id."' AND b.group_code=a.group_code AND MID(b.group_code,1,1)!='M'";
				$result=mysql_query($sql,get_db_conn());
				if($row=mysql_fetch_object($result)) {
					$group_name=$row->group_name;
				}
				mysql_free_result($result);
			}
			echo "<tr bgcolor=#FFFFE6>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			if($vendercnt>0) {
				echo "	<td>&nbsp;</td>\n";
			}
			echo "	<td style=\"font-size:8pt;padding:2,5\"><font color=red>그룹회원 적립/할인 : ".$group_name."</font></td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td align=right style=\"font-size:8pt\">".($salereserve>0?($_ord->paymethod!="B" || $mode!="update"?number_format($salereserve)."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=salereserve value=\"".$salereserve."\">"):"&nbsp;")."</td>\n";
			echo "	<td align=right style=\"font-size:8pt\">".($salemoney>0?($_ord->paymethod!="B" || $mode!="update"?"-".number_format($salemoney)."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=salemoney value=\"-".$salemoney."\">"):"&nbsp;")."</td>\n";
			if($_ord->paymethod=="B" && $mode=="update") {
				echo "	<td align=center><a href=javascript:OrderUpdate('2','','','','','')><img src='images/ordtl_miniup.gif' align=absmiddle border=0 alt='수정'></a><br><img width=0 height=2 border=0><br><a href=javascript:OrderDelete('2','','','','')><img src='images/ordtl_minidel.gif' align=absmiddle border=0 alt='삭제'></a></td>\n";
			}
			echo "</tr>\n";
			$in_reserve+=$salereserve;
		}
		if($_ord->reserve>0) {
			echo "<tr bgcolor=#F5F5F5>\n";
			echo "	<td>&nbsp;</td>\n";
				echo "	<td>&nbsp;</td>\n";
			if($vendercnt>0) {
				echo "	<td>&nbsp;</td>\n";
			}
			echo "	<td style=\"font-size:8pt;padding:2,5\"><font color=#0054A6>적립금사용액</font></td>\n";
			//echo "	<td>&nbsp;</td>\n";
			echo "	<td></td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td align=right style=\"font-size:8pt\">".($_ord->paymethod!="B" || $mode!="update"?"- ".number_format($_ord->reserve)."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=usereserve value=\"".$_ord->reserve."\">")."</td>\n";
			if($_ord->paymethod=="B" && $mode=="update") {
				echo "	<td align=center><a href=javascript:OrderUpdate('3','','','','','')><img src='images/ordtl_miniup.gif' align=absmiddle border=0 alt='수정'></a><br><img width=0 height=2 border=0><br><a href=javascript:OrderDelete('3','','','','')><img src='images/ordtl_minidel.gif' align=absmiddle border=0 alt='삭제'></a></td>\n";
			}
			echo "</tr>\n";
		}
		$totalprice=$totalprice-$salemoney-$_ord->reserve;
		if($_shopdata->card_payfee>0 && preg_match("/^(C|P|M){1}/",$_ord->paymethod) && $_ord->price<>$totalprice) {
			echo "<tr bgcolor=#F5F5F5>\n";
			echo "	<td>&nbsp;</td>\n";
			if($vendercnt>0) {
				echo "	<td>&nbsp;</td>\n";
			}
			echo "	<td style=\"font-size:8pt;padding:2,5\"><font color=#F26622>카드수수료</font></td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td align=right style=\"font-size:8pt\">".number_format($_ord->price-$totalprice)."&nbsp;</td>\n";
			echo "</tr>\n";
		}
		$temp = substr($_ord->ordercode,0,4)."/".substr($_ord->ordercode,4,2)."/".substr($_ord->ordercode,6,2)." ".substr($_ord->ordercode,8,2).":".substr($_ord->ordercode,10,2).":".substr($_ord->ordercode,12,2);
		$message=explode("[MEMO]",$_ord->order_msg);
		$message[0]=ereg_replace("\"","&quot;",$message[0]);
		$message[0]=str_replace("\"","",$message[0]);

		$message[0]=ereg_replace("\r\n","<br>\n&nbsp;&nbsp;",$message[0]);
		/*
		$mes1 = explode("<br>",$message[0]);
		$mescnt = count($mes1);
		$message[0]="";
		for($i=0;$i<$mescnt;$i++) {
			//$message[0].=messageview2($mes1[$i],80)."<br>";
		}
		*/
		echo "<tr ";
		if($_ord->reserve==0) echo " bgcolor=#F5F5F5";
		echo ">\n";

		$account_type = "";
		$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드",/*"P"=>"신용카드(매매보호)",*/"M"=>"핸드폰");

		if($_ord->pay_data=="신용카드결제 - 카드작성중" && substr($_ord->ordercode,0,12)<=$pgdate) $_ord->pay_data=$arpm[substr($_ord->paymethod,0,1)]." 에러";

		if (preg_match("/^(B|O|Q){1}/",$_ord->paymethod)) {	//무통장, 가상계좌, 가상계좌 에스크로
			if($_ord->paymethod=="B") $account_type="무통장입금";
			else if(substr($_ord->paymethod,0,1)=="O") $account_type="가상계좌";
			else echo $account_type="매매보호 - 가상계좌";

		} else if(substr($_ord->paymethod,0,1)=="M") {	//핸드폰 결제
			$account_type = "핸드폰 결제";
		} else if(substr($_ord->paymethod,0,1)=="P") {	//매매보호 신용카드
			$account_type = "매매보호 - 신용카드";
		} else if (substr($_ord->paymethod,0,1)=="C") {	//일반신용카드
			$account_type = "신용카드";
		} else if (substr($_ord->paymethod,0,1)=="V") {
			$account_type = "실시간 계좌이체";
		}

		echo "	<td colspan=".($colspan-4)." style=\"font-size:8pt;padding:5,27\"><B>실 결제금액 (<span style='color:blue'>".$account_type."<span/>)</B> </td>\n";

		echo "	<td>&nbsp;</td>\n";
		echo "	<td align=center style=\"font-size:8pt\">".$sumquantity."</td>\n";
		echo "	<td align=right style=\"font-size:8pt\">".(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X"?number_format($in_reserve)."&nbsp;":"&nbsp")."</td>\n";
		echo "	<td align=right style=\"font-size:8pt;font-weight:bold\"> ".($_ord->paymethod!="B" || $mode!="update"?number_format($_ord->price)."&nbsp;":"<input type=text style='font-size:8pt;text-align:right;width:100%' name=sumprice value=\"".$_ord->price."\">")."</td>\n";
		if($_ord->paymethod=="B" && $mode=="update") {
			echo "	<td align=center><a href=javascript:OrderUpdate('5','','','','','')><img src='images/ordtl_miniup.gif' align=absmiddle border=0 alt='총금액수정'></a></td>";
		}

		echo "</form>\n";
		echo "</tr>\n";
		echo "</table>";


		$candate = date("Ymd",mktime(0,0,0,date("m"),date("d")-15,date("Y")));
		if((!preg_match("/^(R|A)$/", $_ord->del_gbn) && (!preg_match("/^(Q|P){1}/",$_ord->paymethod) || $_ord->price==0))
		|| (!preg_match("/^(R|A)$/", $_ord->del_gbn) && preg_match("/^(Q|P){1}/",$_ord->paymethod) && ($_ord->deli_gbn=="C" || substr($_ord->ordercode,0,8)<$candate) && $_ord->deli_gbn!="Y")) {

			echo "<br/>";
			echo "<div style='width:100%;text-align:left;'><p style='font-weight:bold;font-size:10pt;'>* 전체주문취소 관리</p></div>";
			echo "<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=\"#e8e8e8\" style=\"margin-top:5px;\">";
			echo "<tr bgcolor=#FFFFFF height=24 class=\"page_screen\">\n";

			echo "	<td align=left style=\"padding:10px 0;\">&nbsp;&nbsp;&nbsp;이 주문전체 취소에 따른 ";

			//적립금 취소
			if($norecan!="Y" && $_ord->deli_gbn!="C" && preg_match("/^(Y|D|H)$/",$_ord->deli_gbn) && strlen($_ord->deli_date)==14 && $in_reserve>0 && strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
				echo " <a href=\"javascript:RestoreReserveCancel('".$in_reserve."')\"><img src=\"images/ordtl_restorerescan.gif\" border=0 align=absmiddle></a>";
			}
			//수량복구
			echo " <a href=\"javascript:RestoreOrder('quan','".$_ord->deli_gbn."','".$_ord->reserve."')\"><img src=\"images/ordtl_restorequan.gif\" border=0 align=absmiddle></a>";
			if($_ord->deli_gbn!="C") {
				echo " <a href=\"javascript:RestoreOrder('can','".$_ord->deli_gbn."','".$_ord->reserve."')\"><img src=\"images/ordtl_restorecan.gif\" border=0 align=absmiddle></a>";
			}
			if($_ord->deli_gbn!="C" && $_ord->reserve>0) {
				echo " <a href=\"javascript:RestoreReserve()\"><img src=\"images/ordtl_restoreres.gif\" border=0 align=absmiddle></a>";
			}
			echo "&nbsp;</td>\n";
			echo "</tr>\n";
			echo "</table>";
		}

	//이미 환불된 금액
	$sum_re = 0;
	$sql = "SELECT sum(price) as sum_re FROM tblorderproduct WHERE ordercode='".$ordercode."' and productcode='99999999995X' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);

	$sum_re = $row->sum_re;

	mysql_free_result($result);

	$ord_price = $_ord->price;
	$now_price = $ord_price - $sum_re;


	//부분 취소 가능한 면세부분 조회
	$free_mny = 0;

	if (substr($_ord->paymethod,0,1) == "C") {
		$sql = "select * from card_orderinfo_tax where ordercode='".$ordercode."'";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);

		$free_mny = $row->free_mny;
		mysql_free_result($result);

		//취소 완료된건
		$sql = "select sum(cancel_free) as cancel_free from card_part_cancel_log  where ordercode='".$ordercode."'";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);

		$cancel_free = $row->cancel_free;
		mysql_free_result($result);

		//취소 신청중인건
		$sql = "select sum(free_mny) as want_cancel_free from card_part_cancel_tax_free  where ordercode='".$ordercode."' and status=0";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);

		$want_cancel_free = $row->want_cancel_free;
		mysql_free_result($result);

		$free_mny = $free_mny - $cancel_free - $want_cancel_free;

	}

	//적립금 환원
	$re_reserve = $_ord->reserve;

	if ($re_reserve>0) {

		$sql = "select sum(cancel_reserve) as cancel_reserve from part_cancel_reserve where ordercode='".$ordercode."'";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);

		$cancel_reserve = $row->cancel_reserve;
		mysql_free_result($result);

		$re_reserve = $re_reserve-$cancel_reserve;

	}


	//부분 환불 jdy
	if($_ord->deli_gbn != "Y") {

		echo "<br/>";
		echo "	<div style='width:100%;text-align:left; position:relative;'><p style='font-weight:bold;font-size:10pt;'>* 부분취소 관리</p></div>";

		if($prescd=="Y" && $_ord->deli_gbn!="C") {
			echo "<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=\"#e8e8e8\" style=\"margin-top:5px;\">";

			echo "<tr bgcolor=#FFFFFF height=24 class=\"page_screen\">\n";
			echo "	<td align=left colspan=".($_ord->paymethod!="B" || $mode!="update"?$colspan:($colspan+1))." style=\"padding-top:10px;\">";
			echo "	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			echo "	<tr>\n";
				echo "	<td>&nbsp;&nbsp;&nbsp;선택상품 고객 부분취소 신청에 따른 \n";
				echo "	<span style=\"cursor:pointer\"><img src=\"images/btn_norefund.gif\" align=\"absmiddle\" alt=\"환불거부\" onclick=\"refundsTo('')\" /></span>\n";
				echo "	<span style=\"cursor:pointer\"><img src=\"images/btn_refund.gif\" align=\"absmiddle\" alt=\"환불승인\" onclick=\"refundsTo('RC')\" /></span>\n";
				echo "	<span style=\"cursor:pointer\"><img src=\"images/btn_adminrefund.gif\" align=\"absmiddle\" alt=\"관리자환불신청\" onclick=\"refundsTo('RA')\" /></span>\n";
				/*
				if($_ord->reserve>0) {
				echo "	<span style=\"padding:3px 8px;height:15px;color:#ffffff;background-color:blue;cursor:pointer\" onclick=\"reserveDivView('Y')\">적립금환원</span>&nbsp;\n";
				}
				*/
				?>
				<?
				echo "<div style='width:100%; text-align:left; margin-top:4px;'><p style='color:blue;'>&nbsp;&nbsp;&nbsp;* 상단 주문리스트와 할인,적립,추가비용내역 리스트에서 취소요청 상품 및 내역을 선택하세요</p></div>";
				echo "	</td>";
			//echo "		&nbsp;\n";
			//echo "		<A HREF=\"javascript:changeDeliinfo()\"><img src=images/btn_deliinfomodify.gif border=0 align=absmiddle></A>\n";

			if($_ord->deli_gbn=='Y' || $_ord->deli_gbn=='H') {
					/* 2013-02-12 교환 / 환불 - 주석
					echo "
						<a href='#' onclick='rRefundNew2();return false;'>동일건에대해 교환신청</a>
						<a href='#' onclick='rRefundNew();return false;'>환불신청</a>
					";
					*/
			}
			echo "	</tr>\n";
			echo "	</table>\n";

				echo "&nbsp;</td>\n";
				echo "</tr>\n";
			echo "	</table>\n";
			?>

				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td>
							<div id="refund_div" style="position:absolute; width:670px; border:2px solid #acacac; background-color:#ffffff; z-index:999; padding:5px; margin:-80px 0 0 0; display:none;">
								<div style="width:100%; text-align:right"><span style="color:red;font-weight:bold;">* 부분주문취소에 따른 배송비가 발생할 경우 배송비를 공제한 금액을 처리금액란에 수동입력가능합니다.</span>&nbsp;<span style="border:1px solid gray;color:#ffffff;background-color:#000000;padding:2px 4px;cursor:pointer" onClick="refundDivView('N');" >X</span></div>
								<div style="width:100%;margin-top:5px;">
								<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
										<col width=200 />
										<col width= />
									<tr><td height=2 colspan="2" bgcolor=#808080></td></tr>
										<tr>
											<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>최초주문시 결제금액</td>
											<td style=padding:7,10>
												<?= number_format($ord_price) ?>
											</td>
										</tr>
										<tr>
											<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>이미 처리한 다른상품 환불금액</td>
											<td style=padding:7,10>
												<?= number_format($sum_re) ?>
											</td>
										</tr>
										<tr>
											<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>취소 가능 금액</td>
											<td style=padding:7,10>
												<?= number_format($now_price) ?>
											</td>
										</tr>
										<? if ($re_reserve>0) { ?>

										<tr>
											<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>적립금 환원</td>
											<td style=padding:7,10>
												취소 가능한 적립금 : <span style="color:red"><?= number_format($re_reserve) ?></span> 중
												<input type="text" size="8" name="re_reserve" id="re_reserve" onKeyUp="rePriceMinusReserve(this)" value=""/> 원 환원
											</td>
										</tr>
										<? } ?>
										<tr>
											<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>신청 환불 처리 금액</td>
											<td style=padding:7,10>
												<input type="text" size="8" name="refund_price" id="refund_price" value=""/> (수정 하실 수 있습니다.)<br/>
												<span style="color:red;">* 회원등급 할인등, 주문 시 기타할인내역이 있을 경우 실 취소요청 상품금액보다 최대 환불 처리금액이 적을 수 있습니다.</span>
												<span style="color:blue;" id="dcPriceTotalMsg"><span>
											</td>
										</tr>
										<? if (substr($_ord->paymethod,0,1) == "C" && ($pg_type=="A" || $pg_type == "G")) { ?>
										<tr>
											<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>취소가능 비과세</td>
											<td style=padding:7,10>
												<?= number_format($free_mny) ?>
											</td>
										</tr>
										<tr>
											<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>취소 비과세</td>
											<td style=padding:7,10>
												<input type="text" size="8" name="refund_free" id="refund_free" value=""/> <br/>(비과세 상품을 취소 하실 경우 해당상품의 금액을 입력해주세요.)
											</td>
										</tr>

										<? } ?>
										<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
										<tr>
											<td align="right" colspan="2">
												<span style="border:1px solid gray;color:#ffffff;background-color:#000000;padding:3px 8px;cursor:pointer" onClick="refundsCom()">환불처리</span>
											</td>
										</tr>
										<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
									</table>
								</div>
							</div>
						</td>
					</tr>
				</table>

					<!--
					<div id="reserve_div" style="position:absolute;width:400px;border:2px solid #acacac;background-color:#ffffff;z-index:999;padding:5px;margin:0 0 0 -350px;display:none">
						<div style="width:100%;text-align:right"><span style="color:red;font-weight:bold;">* 처리한 적립금은 회원에게 환원됩니다..</span>&nbsp;<span style="border:1px solid gray;color:#ffffff;background-color:#000000;padding:2px 4px;cursor:pointer" onclick="reserveDivView('N');" >X</span></div>
						<div style="width:100%;margin-top:5px;">
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
								<col width=150 />
								<col width= />
							<tr><td height=2 colspan="2" bgcolor=#808080></td></tr>
								<tr>
									<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>사용 적립금</td>
									<td style=padding:7,10>
										<?= number_format($_ord->reserve) ?>
									</td>
								</tr>
								<tr>
									<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>환원가능 적립금</td>
									<td style=padding:7,10>
										<?= number_format($re_reserve) ?>
									</td>
								</tr>
								<tr>
									<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>환원 적립금</td>
									<td style=padding:7,10>
										<input type="text" size="8" name="re_reserve" id="re_reserve" value=""/>
									</td>
								</tr>
								<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
								<tr>
									<td align="right" colspan="2">
										<span style="border:1px solid gray;color:#ffffff;background-color:blue;padding:3px 8px;cursor:pointer" onclick="reReserveCom()">환원처리</span>
									</td>
								</tr>
								<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
							</table>
						</div>
					</div>
					-->
			<?
		}
	}

	echo "<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=\"#e8e8e8\" style=\"margin-top:5px;\">";
	echo "

		<col></col>
		<col width=95></col>
		<col width=45></col>
		<col width=55></col>";

	if(count($refund_data)>0) {

		echo "<tr>
			<td bgcolor=#efefef align=center colspan=4><span style=\"font-size:11px; font-weight:bold;\">부분 취소상품내역 / 쿠폰 및 적립 등 할인 및 배송비 등 추가비용 부분취소 내역</span></td>
		</tr>\n";


		for($j=0;$j<count($refund_data);$j++) {
			$cnt++;
			$sumprice=$refund_data[$j]->price;
			$reserve=$refund_data[$j]->reserve;
			$in_reserve+=$reserve;
			$totalprice+=$sumprice;
			echo "<tr bgcolor=#ffffff>\n";
			echo "	<td bgcolor=#ffffff style=\"font-size:8pt;padding:2,5;line-height:10pt\">".$refund_data[$j]->productname." (".$refund_data[$j]->date.") <span class=\"page_screen\"></td>\n";

			if ($refund_data[$j]->status =="A" && $sumprice>0) {
				echo "	<td bgcolor=#ffffff><span style=\"padding:3px 8px;height:15px;color:#ffffff;background-color:#FF0000;cursor:pointer\" onclick=\"card_part_cancel('".$refund_data[$j]->uid."');\">카드부분취소</span></td>";
			}else{
				echo "	<td bgcolor=#ffffff>&nbsp;</td>";
			}

			echo "	<td align=right style=\"font-size:8pt\"></td>\n";
			echo "	<td align=right style=\"font-size:8pt\">- ".number_format($sumprice)."</td>\n";

			echo "</tr>\n";

		}

			echo "<tr ";
			if($_ord->reserve==0) echo " bgcolor=#F5F5F5";
			echo ">\n";
			echo "	<td style=\"font-size:8pt;padding:5,27\"><B>부분취소 후 실판매가</B> </td>\n";
			echo "	<td>&nbsp;</td>\n";
			echo "	<td align=center style=\"font-size:8pt\"></td>\n";
			echo "	<td align=right style=\"font-size:8pt;font-weight:bold\"> ".number_format($now_price)."</td>\n";

			echo "</tr>\n";
	}



	//환원된 적립금
	$sql = "SELECT * FROM part_cancel_reserve WHERE ordercode='".$ordercode."' order by reg_date asc";
	$result=mysql_query($sql,get_db_conn());

	$kk = 0;
	while($row=mysql_fetch_object($result)) {

		if ($kk==0) {
		echo "
		<tr>
			<td bgcolor=#ffffff align=center colspan=4 height=10></td>
		</tr>
		<tr>
			<td bgcolor=#efefef align=center colspan=4><span style=\"font-size:11px; font-weight:bold;\">적립금 환원</span></td>
		</tr>\n";

		}

		echo "<tr bgcolor=#ffffff>\n";

		echo "	<td bgcolor=#ffffff style=\"font-size:8pt;padding:2,5;line-height:10pt\">".$row->memo." (".substr($row->reg_date, 0, 10).")</td>\n";
		echo "	<td><input type=hidden name=arquantity value=\"1\">&nbsp;</td>\n";
		echo "	<td align=right style=\"font-size:8pt\">".number_format($row->cancel_reserve)."&nbsp;</td>\n";
		echo "	<td align=right style=\"font-size:8pt\"></td>\n";

		echo "</tr>\n";

		$kk++;
	}

?>
</table>
<br/>
<?


	//환불계좌 jdy
	$sql = "SELECT * FROM order_refund_account WHERE ordercode='".$ordercode."'";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
		$refund_account_bank = $row->bank;
		$refund_account_name = $row->account_name;
		$refund_account_num = $row->account_num;
		$refund_account_save = true;

	mysql_free_result($result);


	$_ord_pay_data = explode("<br>환불계좌정보 : ", $_ord->pay_data);
	if( strlen($_ord_pay_data[1]) > 0 ) {
		$_ord_pay_dataA = explode(" ", $_ord_pay_data[1]);
		$_ord_pay_dataB = explode("(예금주:", $_ord_pay_dataA[1]);
		$_ord_pay_dataC = explode(")", $_ord_pay_dataB[1]);
		$refund_account_bank = $_ord_pay_dataA[0];
		$refund_account_name = $_ord_pay_dataB[0];
		$refund_account_num = $_ord_pay_dataC[0];
		$refund_account_save = false;
	}
?>
<div style='width:100%;text-align:left;'><p style='font-weight:bold;font-size:10pt;'>* 취소에 따른 환불계좌정보(은행 /환불계좌정보/예금주)</p></div>
<table border=0 cellpadding=0 cellspacing=0 width=100% style=margin-top:10px;>
<tr>
	<td>
		<table border=0 cellpadding=3 cellspacing=1 width=100% bgcolor=#d8d8d8>
			<tr>
				<td bgcolor=#ffffff style="padding-bottom:10px;vertical-align:top;">
					부분취소 :
					<input type="text" name="bank" id="bank" size="10" value="<?=$refund_account_bank?>"/>
					/
					<input type="text" name="account_name" id="account_name" size="30" value="<?=$refund_account_name?>"/>
					/
					<input type="text" name="account_num" id="account_num" size="10" value="<?=$refund_account_num?>"/>
					<?
						if( $refund_account_save ) {
					?>
					<input type="button" value="저 장" style="cursor:hand; color:#FFFFFF; border-color:#666666; background-color:#666666; font-size:8pt; font-family:Tahoma; height:20px; width:45px;" onClick="reFuAccount();">
					<?
						}
					?>
				</td>
			</tr>
		</table>

	</td>
</tr>
</table>


<!--

 수동취소 ( 2014-07-08 x2chi )

<br/>
<div style='width:100%;text-align:left;'><p style='font-weight:bold;font-size:10pt;'>* 결제 수동 취소 처리</p></div>
<table border=0 cellpadding=0 cellspacing=0 width=100% style=margin-top:10px;>
<tr>
	<td>
		<table border=0 cellpadding=3 cellspacing=1 width=100% bgcolor=#d8d8d8>
			<col width=90 style=\"padding-left:3\"></col>
			<col width=></col>
			<tr>
				<td bgcolor=#ffffff style="padding-bottom:10px;vertical-align:top;">
					<input type="button" value="수동 취소" style="cursor:hand; color:#FFFFFF; border-color:#666666; background-color:#666666; font-size:8pt; font-family:Tahoma; height:20px; width:200px;" onClick="popCancle('<?=$ordercode?>','<?=$_ord->pay_auth_no?>');">
					<br>
					* 나이스페이 수동 처리 기능입니다. 관련 옵션을 정확히 빠짐없이 입력해 주시기 바랍니다.
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
-->




<?
	$promotInfo =  snsPromoteOrderOkInfo ( $ordercode );
?>
<div style='width:100%;text-align:left;'><p style='font-weight:bold;font-size:10pt;'>* 홍보 URL 적립 정보</p></div>
<table border=0 cellpadding=3 cellspacing=1 width=100% bgcolor=#d8d8d8>
	<tr>
		<td bgcolor=#ffffff style="padding-bottom:10px;vertical-align:top;">
			<?
				if( $promotInfo['rsvA'] ) {
					echo "[<strong>".$promotInfo['pkId']."</strong>]님의 홍보 URL을 통해 [<strong>".$promotInfo['memId']."</strong>]님이 구매한 주문입니다.<br />배송완료처리가 되면 홍보자인 [<strong>".$promotInfo['pkId']."</strong>]님께<strong>".$promotInfo['pkRsv']."</strong>원이 적립 됩니다.";
				}
			?>
		</td>
	</tr>
	<tr>
		<td bgcolor=#ffffff style="padding-bottom:10px;vertical-align:top;">
			<?
				if( $promotInfo['rsvB'] ) {
					echo "[<strong>".$promotInfo['pkId']."</strong>]님의 홍보 URL을 통해 [<strong>".$promotInfo['memId']."</strong>]님이 구매한 주문입니다.<br />배송완료처리가 되면 구매자인 [<strong>".$promotInfo['memId']."</strong>]님께<strong>".$promotInfo['memRsv']."</strong>원이 적립 됩니다.";
				}
			?>
		</td>
	</tr>
</table>
<?
		echo "<table border=0 cellpadding=0 cellspacing=0 width=100% style=margin-top:40px;>";
		echo "<tr>\n";
		echo "	<td colspan=".($_ord->paymethod!="B" || $mode!="update"?$colspan:($colspan+1))." align=center>\n";
		echo "	<table border=0 cellpadding=3 cellspacing=1 width=100% bgcolor=#d8d8d8>\n";
		echo "	<col width=90 style=\"padding-left:3\"></col>\n";
		echo "	<col width=></col>\n";
		//echo "	<tr><td colspan=2 height=5></td></tr>\n";
		echo "	<tr><td bgcolor=#efefef align=center colspan=2><span style=\"font-size:11px; font-weight:bold;\">주문자 정보</span></td></tr>\n";
		echo "	<form name=form2 method=post action=\"".$_SERVER[PHP_SELF]."\">\n";
		echo "	<input type=hidden name=type>\n";
		echo "	<input type=hidden name=ordercode value=\"".$_ord->ordercode."\">\n";
		echo "	<input type=hidden name=id value=\"".urlencode($_ord->id)."\">\n";
		echo "	<tr>\n";
		echo "		<td bgcolor=#f5f5f5 style=font-size:11px;>주문일자</td>\n";
		echo "		<td bgcolor=#ffffff>".$temp;
		if(($_ord->del_gbn=="Y" || $_ord->del_gbn=="R") && !preg_match("/^(Y)$/",$_ord->deli_gbn)) {
			echo " &nbsp;&nbsp;&nbsp;<font color=blue>[주문자가 내용삭제 버튼을 누른 주문서]</font>";
		}elseif($_ord->del_gbn=="Q"){
			echo " &nbsp;&nbsp;&nbsp;<font color=red>[결제금액 불일치로 인한 자동 취소된 주문서]</font>";
		}
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td bgcolor=#f5f5f5 style=font-size:11px;>주문자</td>\n";
		echo "		<td bgcolor=#ffffff>".$_ord->sender_name;
		if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
			echo "<A HREF=\"javascript:ReserveInfo('".$_ord->id."');\">(".$_ord->id.")</a> ";
			if(strlen($group_name)>0) echo " [ 그룹명 : ".$group_name." ] ";
			if($hidedisplay!="Y") {
				echo "<a href=\"javascript:MemberMemo('".$_ord->id."')\"><img src='images/ordtl_icnmemo.gif' align=absmiddle border=0 alt='메모 입력/수정하기'></a> ";
				if(strlen(trim($usermemo))>0) {
					echo "<div id=\"membermemo_layer\" style=\"position:absolute; z-index:20; width:300;\"><table border=0 cellspacing=0 cellpadding=1 bgcolor=#7F7F65><tr><td style=\"padding:3\"><font color=#ffffff>".$usermemo." <a href=\"javascript:HideMemo()\"><img src=\"images/x.gif\" align=absmiddle border=0 alt=\"숨기기\"></a>&nbsp;</td></tr></table></div>";
				}
			}

			echo "&nbsp;&nbsp;적립금 : <A HREF=\"javascript:ReserveInOut('".$_ord->id."');\"><img src=\"images/btn_pm.gif\" width=\"35\" height=\"29\" border=\"0\"></A>&nbsp;";
			echo "<A HREF=\"javascript:ReserveInfo('".$_ord->id."');\"><img src=\"images/btn_detail.gif\" width=\"35\" height=\"29\" border=\"0\"></A>";

		} else {
			echo "(비회원주문)";
		}
		echo "		</td>\n";
		echo "	</tr>\n";
		if (strlen($_ord->ip)>0) {
			$ip = $_ord->ip;
			echo "	<tr>\n";
			echo "		<td bgcolor=#f5f5f5 style=font-size:11px;>주문자IP</td>\n";
			echo "		<td bgcolor=#ffffff>".$ip."</td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr>\n";
		echo "		<td bgcolor=#f5f5f5 style=font-size:11px;>연락처</td>\n";
		echo "		<td bgcolor=#ffffff><img src=\"images/ordtl_icntel.gif\" align=absmiddle>전화 : ".$_ord->sender_tel."";
		if($smsok==true) {
			echo "<span class=\"page_screen\">&nbsp;<a href=\"javascript:SendSMS('".$_ord->sender_tel."','".$_ord->receiver_tel1."','".$_ord->receiver_tel1."')\"><img src=\"images/ordtl_icnsms.gif\" border=0 align=absmiddle alt='sms보내기'></a></span>";
		}
		echo "<img src=\"images/ordtl_icnemail.gif\" align=absmiddle>이메일 : <a href=\"javascript:SendMail('".$_ord->sender_email."')\"><font color=#AA0000>".$_ord->sender_email."</font></a>";
		echo "		</td>\n";
		echo "	</tr>\n";

		// 누적주문표시여부
		if ($hidedisplay!="Y") {
			if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
				$sql = "SELECT COUNT(*) as cnt, SUM(price) as money FROM tblorderinfo ";
				$sql.= "WHERE id='".$_ord->id."' AND deli_gbn='Y'";
				$result=mysql_query($sql,get_db_conn());
				if($row=mysql_fetch_object($result)) {
					$ordercnt=$row->cnt;
					$ordersum=$row->money;
				}
				mysql_free_result($result);
				echo "	<tr>\n";
				echo "		<td bgcolor=#7F7F65 style=font-size:11px;><font color=#ffffff>누적 주문</font></td>\n";
				echo "		<td bgcolor=#7F7F65 style=\"color:#ffffff\">";
				if($ordercnt!=0) {
					echo "주문횟수 ".$ordercnt."건, 총주문금액 ".number_format($ordersum)." (발송완료 기준) ";
				} else {
					echo "첫구매 고객입니다.".((strlen($fist_rec_id)>0)? "추천인(".$fist_rec_id.")에게 적립금".$totalRecom." 적립":"");
				}
				echo "		&nbsp;&nbsp;<A HREF=\"javascript:HideDisplay()\"><img src=\"images/x.gif\" align=absmiddle border=0 alt=\"숨기기\"></A>";
				echo "		</td>\n";
				echo "	</tr>\n";
			}
		}
		if(sizeof($arTotalSns)>0){
			echo "	<tr>\n";
			echo "		<td bgcolor=#f5f5f5>sns 홍보인</td>\n";
			echo "		<td>";
			foreach($arTotalSns as $key => $var) {
				if($fist_rec_id != $key ){
					if($arSnsType[0] != "N") {
						echo $key."에게 적립금 ".$var." 지급<br>";
						$sell_memid .=$key."||";
						$sell_memid_reserve .=$var."||";
					}
				}
			}
			echo "	</td></tr>\n";
		}
		echo "	<tr>\n";
		echo "		<td bgcolor=#f5f5f5 style=font-size:11px;>받는분</td>\n";
		echo "		<td bgcolor=#ffffff>".$_ord->receiver_name."</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td bgcolor=#f5f5f5 valign=top style=\"padding-top:5px; font-size:11px;\">받는 주소</td>\n";
		echo "		<td bgcolor=#ffffff>\n";
		echo "		<span class=\"page_screen\">\n";
		$address = eregi_replace("\n"," ",trim($_ord->receiver_addr));
		$address = eregi_replace("\r"," ",$address);
		$pos=strpos($address,"주소");
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+7);
		}
		$post = ereg_replace("우편번호 : ","",$post);
		//$arpost = explode("-",$post);
		$arpost = $post;
		//echo "		우편번호 : <input name=post1 size=3 value=\"".$arpost[0]."\" onclick=\"this.blur();f_addr_search('form2','post','address1',2);\">-<input name=post2 size=3 value=\"".$arpost[1]."\" onclick=\"this.blur();f_addr_search('form2','post','address1',2);\">\n";
		echo "		우편번호 : <input name=post1 size=5 value=\"".$arpost."\" onclick=\"this.blur();f_addr_search('form2','post','address1',2);\">\n";
		echo "		<input type=button value='우편번호검색' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:20px;width:80\" onclick=\"f_addr_search('form2','post','address1',2);\"><br />\n";
		echo "		주&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;소 : <input type=text name=address1 size=50 value=\"".$address."\"> <input type=button value='주소수정' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:20px;width:60\" onclick=\"AddressUpdate()\">\n";
		echo "		</span>\n";
		echo "		<span class=\"page_print\">\n";
		echo "		우편번호 : ".$arpost[0]."-".$arpost[1]."<br>\n";
		echo "		&nbsp;&nbsp;주&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;소: ".$address."\n";
		echo "		</span>\n";
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td bgcolor=#f5f5f5 style=font-size:11px;>연락처</td>\n";
		echo "		<td bgcolor=#ffffff><img src=\"images/ordtl_icntel.gif\" align=absmiddle>전화 : ".$_ord->receiver_tel1." , ".$_ord->receiver_tel2."</td>\n";
		echo "	</tr>\n";
		echo "	<tr>\n";
		echo "		<td bgcolor=#f5f5f5 style=font-size:11px;>결제 방법</td>\n";
		echo "		<td bgcolor=#ffffff>";

		$pgdate = date("YmdHi",mktime(date("H")-2,date("i"),0,date("m"),date("d"),date("Y")));
		$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드",/*"P"=>"신용카드(매매보호)",*/"M"=>"핸드폰");

		if($_ord->pay_data=="신용카드결제 - 카드작성중" && substr($_ord->ordercode,0,12)<=$pgdate) $_ord->pay_data=$arpm[substr($_ord->paymethod,0,1)]." 에러";

		if (preg_match("/^(B|O|Q){1}/",$_ord->paymethod)) {	//무통장, 가상계좌, 가상계좌 에스크로
			if($_ord->paymethod=="B") echo "<font color=#FF5D00>무통장입금</font>\n";
			else if(substr($_ord->paymethod,0,1)=="O") echo "<font color=#FF5D00>가상계좌</font>\n";
			else echo "매매보호 - 가상계좌";

			if(!preg_match("/^(C|D)$/",$_ord->deli_gbn) || $_ord->paymethod=="B"){
				echo "【 ".$_ord->pay_data." 】";
				if($_ord->paymethod=="B" && strlen(trim($_ord->bankname)) > 0) echo "</td>\n</tr>\n<tr>\n<td bgcolor=#f5f5f5 style=font-size:11px;>입금자명</td><td bgcolor=#ffffff>".$_ord->bankname."</td>";
			}else echo "【 계좌 취소 】";
			/*if(!preg_match("/^(C|D)$/",$_ord->deli_gbn) || $_ord->paymethod=="B") echo "【 ".$_ord->pay_data." 】";
			else echo "【 계좌 취소 】";
			*/
			if (strlen($_ord->bank_date)>=12) {
				echo "</td>\n</tr>\n";
				echo "<tr>\n";
				echo "	<td bgcolor=#f5f5f5 style=font-size:11px;><FONT COLOR=red><B>입금확인</B></FONT></td>\n";
				echo "	<td bgcolor=#ffffff><B><font color=red>".substr($_ord->bank_date,0,4)."/".substr($_ord->bank_date,4,2)."/".substr($_ord->bank_date,6,2)." (".substr($_ord->bank_date,8,2).":".substr($_ord->bank_date,10,2).")</font></B>";
			} else if(strlen($_ord->bank_date)==9) {
				echo "</td>\n</tr>\n";
				echo "<tr>\n";
				echo "	<td bgcolor=#f5f5f5><FONT COLOR=red><B>입금확인</B></FONT></td>\n";
				echo "	<td><B><font color=red>환불</font></B>";
			}
		} else if(substr($_ord->paymethod,0,1)=="M") {	//핸드폰 결제
			echo "핸드폰 결제【 ";
			if ($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=red>결제취소 완료</font> 】";
				else echo "<font color=red>결제가 성공적으로 이루어졌습니다.</font>";
			}
			else echo "결제가 실패되었습니다.";
			echo " 】";
		} else if(substr($_ord->paymethod,0,1)=="P") {	//매매보호 신용카드
			echo "매매보호 - 신용카드";
			if($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=red>카드결제 취소완료</font> 】";
				else if($_ord->pay_admin_proc=="Y") echo "【 카드 결제 완료 * 감사합니다. : 승인번호 ".$_ord->pay_auth_no." 】";
			}
			else echo "【 ".$_ord->pay_data." 】";
		} else if (substr($_ord->paymethod,0,1)=="C") {	//일반신용카드
			echo "<font color=#FF5D00>신용카드</font>\n";
			if($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=red>카드결제 취소완료</font> 】";
				else if($_ord->pay_admin_proc=="Y") echo "【 카드 결제 완료 * 감사합니다. : 승인번호 ".$_ord->pay_auth_no." 】";
			}
			else echo "【 ".$_ord->pay_data." 】";
		} else if (substr($_ord->paymethod,0,1)=="V") {
			echo "실시간 계좌이체 : ";
			if ($_ord->pay_flag=="0000") {
				if($_ord->pay_admin_proc=="C") echo "【 <font color=005000> [환불]</font> 】";
				else echo "<font color=red>".$_ord->pay_data."</font>";
			}
			else echo "결제가 실패되었습니다.";
		}

		if(preg_match("/^(Q|P){1}/",$_ord->paymethod) && preg_match("/^(Y)$/",$_ord->escrow_result) && $_ord->deli_gbn!="C") echo " - <font color=red><b>[구매확인]</b></font>";
		else if(preg_match("/^(Q|P){1}/",$_ord->paymethod) && preg_match("/^(C)$/",$_ord->escrow_result) && $_ord->deli_gbn=="C") echo " - <font color=red><b>[구매취소]</b></font>";
		echo "		</td>\n";
		echo "	</tr>\n";
		$ardelivery=array("Y"=>"발송중","N"=>"미발송","C"=>"주문취소","X"=>"배송요청","S"=>"발송준비","D"=>"취소요청","E"=>"환불대기","H"=>"배송(정산보류)");
		echo "	<tr>\n";
		echo "		<td bgcolor=#f5f5f5 style=font-size:11px;>발송 여부</td>\n";
		echo "		<td bgcolor=#ffffff><font color=#A00000>".$ardelivery[$_ord->deli_gbn]."</font>";
		if(strlen($_ord->deli_date)==14) {
			echo " - 발송셋팅일 : ".substr($_ord->deli_date,0,4)."/".substr($_ord->deli_date,4,2)."/".substr($_ord->deli_date,6,2)." (".substr($_ord->deli_date,8,2).":".substr($_ord->deli_date,10,2).")";
		}

		// KCP 매매보호 가상계좌이면서 취소요청의 경우 환불계좌 미리 등록
		if(preg_match("/^(Q){1}/",$_ord->paymethod) && strlen($_ord->bank_date)==14 && ($pg_type!="C" && $pg_type!="D")) {
			if(strlen($_ord->deli_date)!=14) {
				echo " <a href='javascript:escrow_bank_account()'><font color=red><U>[환불계좌수기입력]</U></font></a>";
			}
		}

		echo "		</td>\n";
		echo "	</tr>\n";
		if($in_reserve>0 && $_ord->deli_gbn=="N" && strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X"){
			echo "	<tr>\n";
			echo "		<td colspan=2 bgcolor=#ffffff style=padding-left:95px;><font color=#0000FF>&nbsp;&nbsp;* 발송완료 버튼을 누르면 회원에게 적립금</font> <font color=#A00000>".number_format($in_reserve)."원</font><font color=#0000FF>이 적립됩니다.</font></td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr height=22>\n";
		echo "		<td bgcolor=#f5f5f5 valign=top style=\"padding-top:5px; font-size:11px;\">주문요청사항</td>\n";
		echo "		<td bgcolor=#ffffff>".$message[0]."</td>\n";
		echo "	</tr>\n";

		for($j=0;$j<count($prdata);$j++) {
			if(strlen($prdata[$j]->order_prmsg)>0) {
				echo "	<tr height=22 class=\"page_screen\">\n";
				echo "		<td valign=middle>주문메세지</td>\n";
				echo "		<td style=\"padding-left:7;word-break:break-all\">";
				echo "	<FONT COLOR=\"#000000\"><B>상품명 :</B></FONT> ".$prdata[$j]->productname."<BR>\n";
				echo "<textarea style=\"width:95%;height:38;overflow-x:hidden;overflow-y:auto;\" readonly>".$prdata[$j]->order_prmsg."</textarea>\n";
				echo "		</td>\n";
				echo "	</tr>\n";
				echo "<tr><td colspan=2 height=5></td></tr>\n";

				echo "	<tr height=22 class=\"page_print\">\n";
				echo "		<td valign=middle>주문메세지</td>\n";
				echo "		<td style=\"padding-left:7\">";
				echo "		<FONT COLOR=\"#000000\"><B>상품명 :</B></FONT> ".$prdata[$j]->productname."<BR>\n";
				echo "		".$prdata[$j]->order_prmsg."";
				echo "		</td>\n";
				echo "	</tr>\n";
				echo "<tr><td colspan=2 height=3></td></tr>\n";
			}
		}

		echo "	<tr height=58 class=\"page_screen\">\n";
		echo "		<td bgcolor=#f5f5f5 valign=top style=\"padding-top:8px; font-size:11px;\">주문관련 메모</td>\n";
		echo "		<td bgcolor=#ffffff style=\"padding-top:3\">\n";
		echo "		<font style=\"line-height:20px\">&nbsp;&nbsp;<textarea name=memo1 cols=76 rows=3 style=\"font-size:9pt\">".$message[1]."</textarea>&nbsp;<input type=button value='입 력' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:20px;width:40\" onclick=\"MemoUpdate()\"><br>	&nbsp;&nbsp;<font color=#0000FF>*쇼핑몰 운영자만 확인할수 있는 주문관련 메모를 남길 수 있습니다.</font>";
		echo "		</td>\n";
		echo "	</tr>\n";
		if(strlen($message[1])>0) {
			echo "	<tr height=58 class=\"page_print\">\n";
			echo "		<td bgcolor=#f5f5f5 valign=top style=\"padding-top:8px; font-size:11px;\">주문관련 메모</td>\n";
			echo "		<td bgcolor=#ffffff style=\"padding-top:3\">".$message[1]."</td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr height=42 class=\"page_screen\">\n";
		echo "		<td bgcolor=#f5f5f5 valign=top style=\"padding-top:8px; font-size:11px;\">고객알리미</td>\n";
		echo "		<td bgcolor=#ffffff style=\"padding-top:3\">\n";
		echo "		<font style=\"line-height:20px\">&nbsp;&nbsp;<input type=text name=memo2 size=66 maxlength=100 value=\"".$message[2]."\">&nbsp;<input type=button value='입 력' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:20px;width:40\" onclick=\"MemoUpdate()\"><br> &nbsp;&nbsp;<font color=#0000FF>*입력을 하시면, 고객 주문조회 화면을 통해 고객에게 알려드립니다.</font>";
		echo "		</td>\n";
		echo "	</tr>\n";
		if(strlen($message[2])>0) {
			echo "	<tr height=58 class=\"page_print\">\n";
			echo "		<td bgcolor=#f5f5f5 valign=top style=\"padding-top:8px; font-size:11px;\">고객알리미</td>\n";
			echo "		<td bgcolor=#ffffff style=\"padding-top:3\">".$message[2]."</td>\n";
			echo "	</tr>\n";
		}
		echo "	<input type=hidden name=paymethod value=\"".$_ord->paymethod."\">\n";
		echo "	<input type=hidden name=in_reserve value=\"".$in_reserve."\">\n";
		echo "	<input type=hidden name=sender_email value=\"".$_ord->sender_email."\">\n";
		echo "	<input type=hidden name=sender_tel value=\"".$_ord->sender_tel."\">\n";
		echo "	<input type=hidden name=order_msg value=\"".$message[0]."\">\n";
		echo "	<input type=hidden name=sort>\n";
		echo "	<input type=hidden name=recoveryquan value=\"N\">\n";
		echo "	<input type=hidden name=recoveryrese value=\"N\">\n";
		echo "	<input type=hidden name=canreserve>\n";
		echo "	<input type=hidden name=recoveryrecan value=\"N\">\n";
		echo "	<input type=hidden name=deli_name>\n";
		echo "	<input type=hidden name=deli_com>\n";
		echo "	<input type=hidden name=deli_num>\n";
		echo "	<input type=hidden name=delimailtype value=\"N\">\n";
		echo "	<input type=hidden name=sell_memid_reserve value=\"".$sell_memid_reserve."\">\n";
		echo "	<input type=hidden name=totalRecom value=\"".$totalRecom."\">\n";
		echo "	<input type=hidden name=sell_memid value=\"".$sell_memid."\">\n";
		echo "	</form>\n";
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";

		if($pg_type=="A") {	//KCP
			echo "<form name=kcpform method=post action=\"".$Dir."paygate/A/cancel.php\">\n";
			echo "<input type=hidden name=sitecd value=\"".$pgid_info["ID"]."\">\n";
			echo "<input type=hidden name=sitekey value=\"".$pgid_info["KEY"]."\">\n";
			echo "<input type=hidden name=ordercode value=\"".$_ord->ordercode."\">\n";
			echo "<input type=hidden name=paymethod value=\"".substr($_ord->paymethod,0,1)."\">\n";
			echo "<input type=hidden name=return_host value=\"".urlencode(getenv("HTTP_HOST"))."\">\n";
			echo "<input type=hidden name=return_script value=\"".str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl()).AdminDir."order_detail.php"."\">\n";
			echo "<input type=hidden name=return_data value=\"ordercode=".$ordercode."\">\n";
			echo "<input type=hidden name=return_type value=\"form\">\n";
			echo "<input type=hidden name=uid>\n";
			echo "</form>\n";
		} else if($pg_type=="B") {	//LG데이콤
			echo "<form name=dacomform method=post action=\"".$Dir."paygate/B/cancel.php\">\n";
			echo "<input type=hidden name=mid value=\"".$pgid_info["ID"]."\">\n";
			echo "<input type=hidden name=mertkey value=\"".$pgid_info["KEY"]."\">\n";
			echo "<input type=hidden name=ordercode value=\"".$_ord->ordercode."\">\n";
			echo "<input type=hidden name=paymethod value=\"".substr($_ord->paymethod,0,1)."\">\n";
			echo "<input type=hidden name=return_host value=\"".urlencode(getenv("HTTP_HOST"))."\">\n";
			echo "<input type=hidden name=return_script value=\"".str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl()).AdminDir."order_detail.php"."\">\n";
			echo "<input type=hidden name=return_data value=\"ordercode=".$ordercode."\">\n";
			echo "<input type=hidden name=return_type value=\"form\">\n";
			echo "<input type=hidden name=uid>\n";
			echo "</form>\n";
		} else if($pg_type=="C") {	//올더게이트
			echo "<form name=allthegateform method=post action=\"".$Dir."paygate/C/cancel.php\">\n";
			echo "<input type=hidden name=\"storeid\" value=\"".$pgid_info["ID"]."\">\n";
			echo "<input type=hidden name=\"ordercode\" value=\"".$_ord->ordercode."\">\n";
			echo "<input type=hidden name=\"paymethod\" value=\"".substr($_ord->paymethod,0,1)."\">\n";
			echo "<input type=hidden name=\"return_host\" value=\"".urlencode(getenv("HTTP_HOST"))."\">\n";
			echo "<input type=hidden name=\"return_script\" value=\"".str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl()).AdminDir."order_detail.php"."\">\n";
			echo "<input type=hidden name=\"return_data\" value=\"ordercode=".$ordercode."\">\n";
			echo "<input type=hidden name=\"return_type\" value=\"form\">\n";
			echo "<input type=hidden name=uid>\n";
			echo "</form>\n";
		}else if($pg_type=="D") {	//이니시스
			echo "<form name=inicisform method=post action=\"".$Dir."paygate/D/cancel.php\">\n";
			echo "<input type=hidden name=sitecd value=\"".$pgid_info["ID"]."\">\n";
			echo "<input type=hidden name=ordercode value=\"".$_ord->ordercode."\">\n";
			echo "<input type=hidden name=paymethod value=\"".substr($_ord->paymethod,0,1)."\">\n";
			echo "<input type=hidden name=return_host value=\"".urlencode(getenv("HTTP_HOST"))."\">\n";
			echo "<input type=hidden name=return_script value=\"".str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl()).AdminDir."order_detail.php"."\">\n";
			echo "<input type=hidden name=return_data value=\"ordercode=".$ordercode."\">\n";
			echo "<input type=hidden name=return_type value=\"form\">\n";
			echo "<input type=hidden name=uid>\n";
			echo "</form>\n";
		}else if($pg_type=="E") {	//나이스
			echo "<form name=niceform method=post action=\"".$Dir."paygate/E/cancel.php\">\n";
			echo "<input type=hidden name=ordercode value=\"".$_ord->ordercode."\">\n";
			echo "<input type=hidden name=TID value=\"".$_ord->pay_auth_no."\">\n";
			echo "<input type=hidden name=CancelAmt value=\"".$_ord->price."\">\n";
			echo "<input type=hidden name=CancelMsg value=\"관리자 취소\">\n";
			echo "<input type=hidden name=PartialCancelCode value=\"0\">\n"; // 전체 취소 : 0 , 부분취소 : 1
			echo "</form>\n";
		} else if($pg_type == "G"){
			echo "<form name=allatform method=post action=\"".$Dir."paygate/G/cancel.php\">\n";
			echo "<input type=hidden name=sitecd value=\"".$pgid_info["ID"]."\">\n";
			echo "<input type=hidden name=sitekey value=\"".$pgid_info["KEY"]."\">\n";
			echo "<input type=hidden name=ordercode value=\"".$_ord->ordercode."\">\n";
			echo "<input type=hidden name=paymethod value=\"".substr($_ord->paymethod,0,1)."\">\n";
			echo "<input type=hidden name=return_host value=\"".urlencode(getenv("HTTP_HOST"))."\">\n";
			echo "<input type=hidden name=return_script value=\"".str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl()).AdminDir."order_detail.php"."\">\n";
			echo "<input type=hidden name=return_data value=\"ordercode=".$ordercode."\">\n";
			echo "<input type=hidden name=return_type value=\"form\">\n";
			echo "<input type=hidden name=type value=\"A\">\n";
			echo "<input type=hidden name=uid>\n";
			echo "</form>\n";
		}
	}
?>
	</table>
	<!-- 주문내역 끝 -->
	</td>
</tr>

<form name=form_reg action="product_register.php" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
</form>

<form name=smsform action="sendsms.php" method=post target="sendsmspop">
<input type=hidden name=number>
</form>

<form name=formmemo method=post>
<input type=hidden name=ordercode value="<?=$_ord->ordercode?>">
<input type=hidden name=id>
</form>

<form name=formhide action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=ordercode value="<?=$_ord->ordercode?>">
<input type=hidden name=hidedisplay value="Y">
</form>

<form name=taxsaveform method=post action="<?=$Dir.FrontDir?>taxsave.php" target=taxsavepop>
<input type=hidden name=ordercode value="<?=$_ord->ordercode?>">
<input type=hidden name=productname value="<?=urlencode(titleCut(htmlspecialchars(strip_tags($taxsaveprname),ENT_QUOTES),30))?>">
</form>

<form name=taxprintform method=post action="taxprint.php">
<input type=hidden name=ordercode value="<?=$_ord->ordercode?>">
</form>

<form name=vform action="<?=$Dir?>paygate/set_bank_account.php" method=post target="baccountpop">
<input type=hidden name=ordercode value="<?=$ordercode?>">
</form>

<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>


<form name="reTmpForm" method="post">
<input type="hidden" name="ordercode" value="<?=$ordercode?>">
<input type="hidden" name="type">
<input type="hidden" name="goods">
<input type="hidden" name="status">
<input type="hidden" name="re_price">
<input type="hidden" name="refund_free">
<input type="hidden" name="bank">
<input type="hidden" name="account_name">
<input type="hidden" name="account_num">
<input type="hidden" name="dcPriceSend" value="0"/>

<input type="hidden" name="reserve">
<input type="hidden" name="re_reserve_max" id="re_reserve_max" value="<?= $re_reserve ?>"/>
<input type="hidden" name="re_price_max" id="re_price_max">
</form>


<form name=reserveform action="reserve_money.php" method=post>
<input type=hidden name=type>
<input type=hidden name=id>
</form>

<form name=reserveform2 action="member_reservelist.php" method=post target=reserve_info>
<input type=hidden name=id>
<input type=hidden name=type>
</form>

<tr><td height=10></td></tr>

</table>








<!-- CS 관리 -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top:40px;">
	<tr><td align="right" style="padding-right:17px; padding-bottom:5px;"><a href="http://www.getmall.co.kr/data/cs_manual.zip"><img src="images/btn_csmanual.gif" border="0" align="absmiddle" alt="CS관리 매뉴얼" /></a></td></tr>
	<tr>
		<td style="padding:0px 17px;">
<table border="0" cellpadding="0" cellspacing="1" width="100%" bgcolor="#e0e0e0">
	<col width="35" align="center"></col>
	<col width="70" align="center"></col>
	<col width=""></col>
	<col width="80" align="center"></col>
	<col width="80" align="center"></col>
	<col width="80" align="center"></col>
	<col width="80" align="center"></col>
	<tr>
		<td colspan="7" height="32" bgcolor="#efefef"><span style="font-size:11px;"><b>CS 처리상태</b></span></td>
	</tr>
	<tr bgcolor="#efefef" align="center">
		<td style="height:28px; font-size:11px;">구분</td>
		<td style="font-size:11px;">벤더</td>
		<td style="font-size:11px;">상품명</td>
		<td style="font-size:11px;">제목</td>
		<td style="font-size:11px;">등록일</td>
		<td style="font-size:11px;">처리일</td>
		<td style="font-size:11px;">완료일</td>
	</tr>
	<?
		$csOrderListResult = mysql_query("SELECT * FROM `tbl_csManager` WHERE `order` = '".$_ord->ordercode."' ");
		$csOrderListCnt = mysql_num_rows ( $csOrderListResult );
		while ( $csOrderListRow = mysql_fetch_assoc ( $csOrderListResult ) ) {

			$productSQL = "SELECT * FROM `tblorderproduct` WHERE `ordercode`='".$csOrderListRow['order']."' AND `productcode`='".$csOrderListRow['product']."' LIMIT 1 ; ";
			$productResult=mysql_query($productSQL,get_db_conn());
			$productRow=mysql_fetch_assoc ($productResult);

			switch ( substr($csOrderListRow['type'],0,1) ) {
				case 1 : $csOrderType = "<font color='blue'>출고</font>"; break;
				case 2 : $csOrderType = "<font color='red'>반품</font>"; break;
				case 3 : $csOrderType = "기타"; break;
			}
	?>
	<tr bgcolor="#ffffff">
		<td><?=$csOrderType?></td>
		<td><?=$venderlist[$csOrderListRow['vender']]->id?></td>
		<td style="padding:5px;">
			<?=$productRow['productname']?>
			<?=($productRow['opt1_name'])?"(옵션1:".$productRow['opt1_name'].")":""?>
			<?=($productRow['opt2_name'])?"(옵션2:".$productRow['opt2_name'].")":""?>
		</td>
		<td style="padding:5px;"><?=$csOrderListRow['title']?></td>
		<td style="padding:5px; line-height:140%;"><?=$csOrderListRow['adminRegDate']?></td>
		<td style="padding:5px; line-height:140%;"><?=($csOrderListRow['venderRegDate'] > 0)?$csOrderListRow['venderRegDate']:"-"?></td>
		<td style="padding:5px; line-height:140%;"><?=($csOrderListRow['completeRegDate'] > 0)?$csOrderListRow['completeRegDate']:"-"?></td>
	</tr>
	<?
		}

		if( !$csOrderListCnt ){
			echo "<tr><td colspan=7 bgcolor=#ffffff height=24 align=center>등록된 CS 내용이 없습니다.</td></tr>";
		}
	?>
</table>
		</td>
	</tr>
	<tr><td height="20"></td></tr>
</table>

<div style="clear:both; height:40px; text-align:center;"><a href="javascript:window.close();"><img src="/images/common/bigview_btnclose.gif" border="0"/></a></div>

<?=$onload?>

<? if($ck_y=='Y') echo "<script>cgY();</script>"; ?>

</body>
</html>
