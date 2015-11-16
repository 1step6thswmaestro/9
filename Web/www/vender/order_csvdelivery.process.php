<?
session_start();
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");

INCLUDE ("access.php");

if($_GET["type"]=="init" && strlen($_GET["ordercode"])>0) {
	$sql = "SELECT SUM(reserve*quantity) as in_reserve FROM tblorderproduct ";
	$sql.= "WHERE ordercode='".$_GET["ordercode"]."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$in_reserve = $row->in_reserve;
	}
	echo "<html>\n";
	echo "<head>\n";
	echo "<SCRIPT LANGUAGE=\"javascript\">\n";
	echo "<!--\n";
	echo "function delivery() {\n";
	//echo "	if(confirm(\"배송완료된 정보를 메일/SMS로 발송하시겠습니까?\")) {\n";
	//echo "		document.form1.delimailtype.value=\"Y\";\n";
	//echo "	} else {\n";
	//echo "		document.form1.delimailtype.value=\"Y\";\n";
	//echo "	}\n";
	echo "	document.form1.submit();\n";
	echo "}\n";
	echo "//-->\n";
	echo "</SCRIPT>\n";
	echo "</head>\n";
	echo "<body bgcolor=#FFFFFF topmargin=1 leftmargin=0 marginwidth=0 marginheight=0>\n";
	echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	echo "<form name=form1 method=post action=\"".$_SERVER[PHP_SELF]."\">\n";
	echo "<input type=hidden name=type value=\"delivery\">\n";
	echo "<input type=hidden name=ordercode value=\"".$_GET["ordercode"]."\">\n";
	echo "<input type=hidden name=delimailtype value=\"N\">\n";
	echo "<input type=hidden name=deli_com value=\"".$_GET["deli_com"]."\">\n";
	echo "<input type=hidden name=deli_name value=\"".$_GET["deli_name"]."\">\n";
	echo "<input type=hidden name=deli_num value=\"".$_GET["deli_num"]."\">\n";
	echo "<input type=hidden name=in_reserve value=\"".$in_reserve."\">\n";
	echo "<tr>\n";
	echo "	<td align=center><a href='javascript:delivery();'><img src=images/order_csvdelivery_b3.gif align=absmiddle border=0></a></td>\n";
	echo "</tr>\n";
	echo "</form>\n";
	echo "</table>\n";
	echo "</body>\n";
	echo "</html>\n";
	exit;
}

$type=$_POST["type"];
$ordercode=$_POST["ordercode"];
$delimailok=$_POST["delimailtype"];	//배송완료에 따른 메일/SMS발송 여부 (Y:발송, N:발송안함)
$deli_com=$_POST["deli_com"];
$deli_name=$_POST["deli_name"];
$deli_num=$_POST["deli_num"];
$in_reserve=$_POST["in_reserve"];

//배송완료 세팅
if($type=="delivery" && strlen($ordercode)>0) {
	$errmsg="";
	$pgid_info="";
	$pg_type="";

	$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
	$result=mysql_query($sql,get_db_conn());
	if($_ord=mysql_fetch_object($result)) {
		if(preg_match("/^(N|S|X)$/", $_ord->deli_gbn)) {
			if(preg_match("/^(B|O|Q){1}/", $_ord->paymethod) && strlen($_ord->bank_date)<12) {
				$errmsg="입금확인이 않된 주문서입니다. 다시 확인하시기 바랍니다.";
			} else if(preg_match("/^(C|P){1}/", $_ord->paymethod) && $_ord->pay_admin_proc!="Y") {
				$errmsg="결제가 실패한 주문서입니다. 다시 확인하시기 바랍니다.";
			} else if(preg_match("/^(M){1}/", $_ord->paymethod) && $_ord->pay_admin_proc!="N" && $_ord->pay_flag!="0000") {
				$errmsg="결제가 실패한 주문서입니다. 다시 확인하시기 바랍니다.";
			} else if(preg_match("/^(V){1}/", $_ord->paymethod) && $_ord->pay_admin_proc!="N" && $_ord->pay_flag!="0000") {
				$errmsg="결제가 실패한 주문서입니다. 다시 확인하시기 바랍니다.";
			}
		} else if($_ord->deli_gbn=="Y") {
			$errmsg="이미 배송완료된 주문서입니다.";
		} else {
			$errmsg="이미 취소되거나 발송된 물품입니다. 다시 확인하시기 바랍니다.";
		}
	} else {
		$errmsg="해당 주문코드가 존재하지 않습니다.";
	}
	mysql_free_result($result);

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

	if(strlen($errmsg)==0) {
		$patterns = array("( )","(_)","(-)");
		$replace = array("","","");
		$deli_num = preg_replace($patterns,$replace,$deli_num);

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
			}
		}

		$sql = "UPDATE tblorderinfo SET deli_gbn='Y', deli_date='".date("YmdHis")."' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		if(mysql_query($sql,get_db_conn())) {
			$sql = "UPDATE tblorderproduct SET ";
			$sql.= "deli_gbn	= 'Y', ";
			$sql.= "deli_date	= '".date("YmdHis")."', ";
			$sql.= "deli_com	= '".$deli_com."', ";
			$sql.= "deli_num	= '".$deli_num."' ";
			$sql.= "WHERE ordercode='".$ordercode."' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			mysql_query($sql,get_db_conn());
		}

		if($delimailok=="Y") {	//배송완료 메일을 발송할 경우
			$delimailtype="N";
			SendDeliMail($_shopdata->shopname, $shopurl, $_shopdata->mail_type, $_shopdata->info_email, $ordercode, $deli_com, $deli_num, $delimailtype);

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
					if($rowsms->mem_delinum=="Y") {	//송장안내메세지
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

		$dc_price=(int)$_ord->dc_price;
		if($dc_price<>0) {
			if($dc_price>0) $in_reserve+=$dc_price;
		}

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
				$sql.= "orderdata	= '".$ordercode."=".$_ord->price."', ";
				$sql.= "date		= '".date("YmdHis")."' ";
				mysql_query($sql,get_db_conn());
				$in_reserve=0;
			}
			mysql_free_result($result);
		}
		echo "<html>\n";
		echo "<head></head>\n";
		echo "<body bgcolor=#FFFFFF topmargin=1 leftmargin=0 marginwidth=0 marginheight=0>\n";
		echo "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td align=center><A HREF=\"javascript:parent.OrderDetailView('".$ordercode."')\"><img src=\"images/order_csvdelivery_b1.gif\" border=0></A></td></tr></table>\n";
		echo "</body>\n";
		echo "</html>\n";
		exit;
	} else {
		echo "<html>\n";
		echo "<head></head>\n";
		echo "<script>\n";
		echo "function orderdetail(ordercode) {\n";
		if($_ord) {
			echo "	parent.OrderDetailView(ordercode);\n";
		} else {
			echo "	alert('".$errmsg."');\n";
		}
		echo "}\n";
		echo "</script>\n";
		echo "<body bgcolor=#FFFFFF topmargin=1 leftmargin=0 marginwidth=0 marginheight=0 onload=\"alert('".$errmsg."')\">\n";
		echo "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td align=center><A HREF=\"javascript:orderdetail('".$ordercode."')\"><img src=\"images/order_csvdelivery_b2.gif\" border=0></A></td></tr></table>\n";
		echo "</body>\n";
		echo "</html>\n";
		exit;
	}
}
?>