<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/order_func.php");

#### PG 데이타 세팅 ####
$_ShopInfo->getPgdata();
########################

function getDeligbn($strdeli,$true=true) {
	global $_ShopInfo, $ordercode, $arrdeli;
	
	if(!is_array($arrdeli)) {
		$sql = "SELECT deli_gbn FROM tblorderproduct WHERE ordercode='".$ordercode."' AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
		$sql.= "GROUP BY deli_gbn ";
		$result=mysql_query($sql,get_db_conn());
		$arrdeli=array();
		while($row=mysql_fetch_object($result)) {
			$arrdeli[]=$row->deli_gbn;
		}
		mysql_free_result($result);
	}

	$res=true;
	for($i=0;$i<count($arrdeli);$i++) {
		if($true==true) {
			if(!preg_match("/^(".$strdeli.")$/", $arrdeli[$i])) {
				$res=false;
				break;
			}
		} else {
			if(preg_match("/^(".$strdeli.")$/", $arrdeli[$i])) {
				$res=false;
				break;
			}
		}
	}
	return $res;
}


$ordercode=$_POST["ordercode"];	//로그인한 회원이 조회시
$ordername=$_POST["ordername"]; //비회원 조회시 주문자명
$ordercodeid=$_POST["ordercodeid"];	//비회원 조회시 주문번호 6자리
$print=$_POST["print"];	//OK일 경우 프린트

if(strlen($ordercodeid)>0 && strlen($ordercodeid)!=6) {
	echo "<html><head><title></title></head><body onload=\"alert('주문번호 6자리를 정확히 입력하시기 바랍니다.');window.close();\"></body></html>";exit;
}

$sql = "SELECT gift FROM tblorderinfo WHERE ordercode='{$ordercode}'";
$result = mysql_query($sql,get_db_conn());
$row = mysql_fetch_array($result);

if($row["gift"]=='1'|| $row["gift"]=='2') {
	echo "<script>window.location.href='orderdetailpop2.php?ordercode={$ordercode}';</script>";
	exit;
}

$gift_type=explode("|",$_data->gift_type);

$type=$_POST["type"];
$tempkey=$_POST["tempkey"];
$rescode=$_POST["rescode"];

####### 에스크로 구매결정 #######
if ($type=="okescrow" && strlen($ordercode)>0 && $rescode=="Y") {
	$sql = "UPDATE tblorderinfo SET escrow_result='Y' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	$sql.= "AND (MID(paymethod,1,1)='Q' OR MID(paymethod,1,1)='P') ";
	$sql.= "AND deli_gbn='Y' ";
	$result = mysql_query($sql,get_db_conn());

	echo "<script>alert('구매결정 되었습니다.');self.close();</script>";
	exit;
}

####### 주문취소 (에스크로 포함) #######
if ($type=="cancel" || ($type=="okescrow" && $rescode=="C" && strlen($ordercode)>0)) { //매매보호 주문거절시
	$sql = "SELECT price,deli_gbn,reserve,sender_name,paymethod,bank_date FROM tblorderinfo ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	if($type=="cancel") $sql.= "AND tempkey='".$tempkey."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		if (
		(preg_match("/^(Q|P){1}/", $row->paymethod) && !preg_match("/^(C|D|E|H)$/", $row->deli_gbn) && getDeligbn("C|D|E|H",false))
		|| ($_data->ordercancel==0 && ($row->deli_gbn=="S" || $row->deli_gbn=="N") && getDeligbn("N|S",true)) //tblorderproduct에 deli_gbn이 "S|N"만 있는지 확인한다.
		|| ($_data->ordercancel==2 && $row->deli_gbn=="N" && getDeligbn("N",true)) //tblorderproduct에 deli_gbn이 "N"만 있는지 확인한다.
		|| ($_data->ordercancel=="1" && $row->paymethod=="B" && strlen($row->bank_date)<12 && $row->deli_gbn=="N" && getDeligbn("N",true))
		) {  // 배송기준일 경우 아직 배달을 안했을경우에만 주문 취소, 결제 기준일경우 입금안된건만

			//if(preg_match("/^(Q|P){1}/", $row->paymethod))
			$deliok="D";
			//else $deliok="C";
			//printr($_POST);

			if($_POST['bank_name'] != "" ){//환불 계좌 정보가 있을때
				$bankAccountInfo = "<br>환불계좌정보 : ".$_POST['bank_name']." ".$_POST['bank_num']. "(예금주:". $_POST['bank_owner'].")";
				$banksql = ", pay_data = CONCAT(pay_data,'".$bankAccountInfo."') ";
			}

			$sql = "UPDATE tblorderinfo SET deli_gbn='".$deliok."'".$banksql." WHERE ordercode='".$ordercode."' ";
			if($type=="cancel") $sql.= "AND tempkey='".$tempkey."' ";
			//echo $sql;exit;
			if(mysql_query($sql,get_db_conn())) {
				$sql = "UPDATE tblorderproduct SET deli_gbn='".$deliok."' ";
				$sql.= "WHERE ordercode='".$ordercode."' ";
				$sql.= "AND NOT (productcode LIKE 'COU%' AND productcode LIKE '999999%') ";
				mysql_query($sql,get_db_conn());

				if(empty($ordercodeid) && strlen($_ShopInfo->getMemid())>0 && $row->reserve>0) {
					$sql = "UPDATE tblmember SET reserve=reserve+".abs($row->reserve)." ";
					$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
					mysql_query($sql,get_db_conn());

					$sql = "INSERT tblreserve SET ";
					$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
					$sql.= "reserve		= ".$row->reserve.", ";
					$sql.= "reserve_yn	= 'Y', ";
					$sql.= "content		= '주문 취소건에 대한 적립금 환원', ";
					$sql.= "orderdata	= '".$ordercode."=".$row->price."', ";
					$sql.= "date		= '".date("YmdHis")."' ";
					mysql_query($sql,get_db_conn());
				}

				/////////////// 주문취소시 관리자에게 메일을 발송
				$maildata=$row->sender_name."고객님이 <font color=blue>".date("Y")."년 ".date("m")."월 ".date("d")."일</font>에 아래와 같이 주문을 취소하셨습니다.<br><br>";
				$maildata.="<li> 취소된 주문의 번호 : $ordercode<br><br>";
				$maildata.="취소된 주문은 관리자메뉴의 주문조회에서 확인하실 수 있습니다.";

				if (strlen($_data->shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($_data->shopname)."?=";
				$header=getMailHeader($mailshopname,$_data->info_email);
				if(ismail($_data->info_email)) {
					sendmail($_data->info_email, $_data->shopname." 주문취소 확인 메일입니다.", $maildata, $header);
				}

				if(strlen($_data->okcancel_msg)==0)  $_data->okcancel_msg="정상적으로 주문이 취소요청 되었습니다!";
				if (preg_match("/^(Q){1}/", $row->paymethod) && strlen($row->bank_date)>=12) $_data->okcancel_msg.=" 최종적으로 상점에서 취소 후 환불처리됩니다.";
				if (preg_match("/^(P){1}/", $row->paymethod) && $row->pay_flag=="0000") $_data->okcancel_msg.=" 최종적으로 상점에서 취소 후 카드취소처리됩니다.";

				$sqlsms = "SELECT * FROM tblsmsinfo WHERE admin_cancel='Y' ";
				$resultsms= mysql_query($sqlsms,get_db_conn());
				if($rowsms=mysql_fetch_object($resultsms)) {
					if(strlen($ordercode)>0) {
						$sms_id=$rowsms->id;
						$sms_authkey=$rowsms->authkey;

						$pr_cancel_msg = $rowsms->pr_cancel_msg;
						$pattern = array("(\[NAME\])","(\[DATE\])");
						$replace = array($row->sender_name, substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2));
						$pr_cancel_msg=preg_replace($pattern, $replace, $pr_cancel_msg);
						$pr_cancel_msg=addslashes($pr_cancel_msg);

						$totellist=$rowsms->admin_tel;
						if(strlen($rowsms->subadmin1_tel)>8) $totellist.=",".$rowsms->subadmin1_tel;
						if(strlen($rowsms->subadmin2_tel)>8) $totellist.=",".$rowsms->subadmin2_tel;
						if(strlen($rowsms->subadmin3_tel)>8) $totellist.=",".$rowsms->subadmin3_tel;
						$fromtel=$rowsms->return_tel;

						//$smsmsg=$row->sender_name."님께서 ".substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2)."에 주문하신 주문을 취소하셨습니다.";
						$etcmsg="주문취소 메세지(관리자)";
						if($rowsms->sleep_time1!=$rowsms->sleep_time2) {
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
						$temp=SendSMS($sms_id, $sms_authkey, $totellist, "", $fromtel, $date, $pr_cancel_msg, $etcmsg);
					}
				}
				mysql_free_result($resultsms);
				$onload="<script>alert('".$_data->okcancel_msg."');</script>";
			} else {
				$onload="<script>alert('요청하신 작업중 오류가 발생하였습니다.');</script>";
			}
		} else if (preg_match("/^(Q|P){1}/", $row->paymethod) && preg_match("/^(D)$/", $row->deli_gbn)) {
			$onload="<script>alert('최종적으로 상점에서 취소 후 환불처리됩니다.');</script>";
		} else if($_data->ordercancel==0) {
			if(strlen($_data->nocancel_msg)==0) $onload="<script>alert(\"이미 배송된 상품이 있습니다. 쇼핑몰로 연락주시기 바랍니다.\");</script>";
			else $onload="<script>alert('$_data->nocancel_msg');</script>";
		} else if($_data->ordercancel==2) {
			if(strlen($_data->nocancel_msg)==0) $onload="<script>alert(\"발송준비가 완료되어 택배회사에 전달된 상품이 있습니다. 쇼핑몰로 연락주시기 바랍니다.\");</script>";
			else $onload="<script>alert('$_data->nocancel_msg');</script>";
		} else {
			if(strlen($_data->nocancel_msg)==0) $onload="<script>alert(\"결제대금의 환불/취소는 쇼핑몰로 연락주시기 바랍니다.\");</script>";
			else $onload="<script>alert('$_data->nocancel_msg');</script>";
		}
	}
}

####### 주문서 삭제 #######
if($type=="delete" && strlen($ordercode)>0 && strlen($tempkey)>0) {
	$sql = "SELECT del_gbn FROM tblorderinfo WHERE ordercode='".$ordercode."' AND tempkey='".$tempkey."' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);
	$del_gbn = $row->del_gbn;
	if($del_gbn=="N" || $del_gbn==NULL) $okdel="Y";
	else if($del_gbn=="A") $okdel="R";
	else {
		echo "<html><head><title></title></head><body onload=\"alert('해당 주문서는 이미 삭제처리가 되었습니다.');window.close();opener.location.reload();\"></body></html>";exit;
	}

	$sql = "UPDATE tblorderinfo SET del_gbn='".$okdel."' WHERE ordercode='".$ordercode."' AND tempkey='".$tempkey."' ";
	mysql_query($sql,get_db_conn());
	echo "<html><head><title></title></head><body onload=\"alert('해당 주문서를 삭제처리 하였습니다.');window.close();opener.location.reload();\"></body></html>";exit;
}

?>

<html>
<head>
<title>주문내역 조회</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
#refundAccount {display:none}
td {font-family:돋음;color:333333;font-size:9pt;}

tr {font-family:돋음;color:333333;font-size:9pt;}
BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:돋음;color:333333;font-size:9pt;}

A:link    {color:333333;text-decoration:none;}

A:visited {color:333333;text-decoration:none;}

A:active  {color:333333;text-decoration:none;}

A:hover  {color:#CC0000;text-decoration:none;}
</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
window.moveTo(10,10);
window.resizeTo(800,650);
window.name="orderpop";

function MemoMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	obj._tid = setTimeout("MemoView(WinObj)",200);
}
function MemoView(WinObj) {
	WinObj.style.visibility = "visible";
}
function MemoMouseOut(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	WinObj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

function DeliSearch(url){
	window.open(url,'배송조회','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=550,height=500');
}

function view_product(productcode) {
	opener.location.href="<?=$Dir.FrontDir?>productdetail.php?productcode="+productcode;
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


function order_cancel(tempkey,ordercode,bankdate) {	//주문취소
	if (confirm("주문취소가 완료되면 지급예정된 적립금 및 주문시 사용쿠폰이 모두 취소되며 취소된 주문건은 다시 되돌릴 수 없습니다")) {
		if(bankdate != "") {
			document.getElementById("refundAccount").style.display="";
			if(document.refundAccountForm.bank_name.value == "" || document.refundAccountForm.bank_owner.value == "" || document.refundAccountForm.bank_num.value == "") {
				alert("환불계좌 정보를 입력하세요.");
				document.refundAccountForm.bank_name.focus();
				return;
			}
			document.form1.bank_name.value=document.refundAccountForm.bank_name.value;
			document.form1.bank_owner.value=document.refundAccountForm.bank_owner.value;
			document.form1.bank_num.value=document.refundAccountForm.bank_num.value;
		}
		document.form1.tempkey.value=tempkey;
		document.form1.ordercode.value=ordercode;
		document.form1.type.value="cancel";
		document.form1.submit();
	}
}




function order_del(tempkey,ordercode) {	//주문서 삭제
	if(confirm("주문건에 대해서 취소는 되지 않고, 조회만 불가능합니다.\n\n주문서 내용만 삭제하시겠습니까?")) {
		document.form1.tempkey.value=tempkey;
		document.form1.ordercode.value=ordercode;
		document.form1.type.value="delete";
		document.form1.submit();
	}
}

function get_taxsave(ordercode) {	//현금영수증 요청
	window.open("about:blank","taxsavepop","width=266,height=220,scrollbars=no");
	document.taxsaveform.ordercode.value=ordercode;
	document.taxsaveform.submit();
}

function setPackageShow(packageid) {
	if(packageid.length>0 && document.getElementById(packageid)) {
		if(document.getElementById(packageid).style.display=="none") {
			document.getElementById(packageid).style.display="";
		} else {
			document.getElementById(packageid).style.display="none";
		}
	}
}

// 전자세금계산서 관련
function sendBillPop(ordercode) {
	document.billform.ordercode.value=ordercode;
	window.open("about:blank","billpop","width=610,height=500,scrollbars=yes");
	document.billform.submit();
}
function sendBill(ordercode) {
	document.billsendfrm.ordercode.value=ordercode;
	document.billsendfrm.submit();
}
function viewBill(bidx){
	document.billviewFrm.b_idx.value= bidx;
	window.open("","winBill","scrollbars=yes,width=700,height=600");
	document.billviewFrm.submit();
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
	f.action = "order_oks.php";
	f.submit();

}

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
	f.action = "order_oks.php";
	f.submit();

}


function order_one_cancel(ordercode, productcode, can, tempkey,uid) {

	if (can=="yes") {
		if (confirm("주문취소가 완료되면 지급예정된 적립금 및 주문시 사용쿠폰이 모두 취소되며 취소된 주문건은 다시 되돌릴 수 없습니다")) {
		window.open("<?=$Dir.FrontDir?>order_one_cancel_pop.php?ordercode="+ordercode+"&productcode="+productcode+"&uid="+uid,"one_cancel","width=610,height=500,scrollbars=yes");
		}
	}else{
		if (confirm("입금확인중 주문은 '전체취소'만 가능합니다. \n전체취소를 원하시는 경우 구매를 원하는 상품을 다시 주문해주세요.\n이주문을 지금 주문 전체취소하시겠습니까?")) {
			if(bankdate != "") {
				document.getElementById("refundAccount").style.display="block";
				if(document.refundAccountForm.bank_name.value == "" || document.refundAccountForm.bank_owner.value == "" || document.refundAccountForm.bank_num.value == "") {
					alert("환불계좌 정보를 입력하세요.");
					document.refundAccountForm.bank_name.focus();
					return;
				}
				document.form1.bank_name.value=document.refundAccountForm.bank_name.value;
				document.form1.bank_owner.value=document.refundAccountForm.bank_owner.value;
				document.form1.bank_num.value=document.refundAccountForm.bank_num.value;
			}
			document.form1.tempkey.value=tempkey;
			document.form1.ordercode.value=ordercode;
			document.form1.type.value="cancel";
			document.form1.submit();
		}
	}
}

//-->
</SCRIPT>
</head>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0>
<center>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td align=center style="padding:10,10,10,10">
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
	<tr><td align=center height=30 bgcolor=#454545><FONT COLOR="#FFFFFF"><B>주문조회</B></FONT></td></tr>
<?
	if (strlen($ordercodeid)>0 && strlen($ordername)>0) {	//비회원 주문조회
		$curdate = date("Ymd",mktime(0,0,0,date("m"),date("d")-90,date("Y")))."00000";
		$sql = "SELECT * FROM tblorderinfo WHERE ordercode > '".$curdate."' AND id LIKE 'X".$ordercodeid."%' ";
		$sql.= "AND sender_name='".$ordername."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$_ord=$row;
			$ordercode=$row->ordercode;
			$gift_price=$row->price-$row->deli_price;
		} else {
			echo "<tr height=200><td align=center>조회하신 주문내역이 없습니다.<br><br>회원주문이 아닌경우 주문후 90일이 경과하였다면 상점에 문의바랍니다.</td></tr>\n";
			echo "<tr><td align=center><input type=button value='닫 기' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:20px;width:70\" onclick=\"window.close()\"></td></tr>\n";
			echo "</table>";
			exit;
		}
		mysql_free_result($result);
	} else {
		$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$_ord=$row;
			$gift_price=$row->price-$row->deli_price;
		} else {
			echo "<tr height=200><td align=center>조회하신 주문내역이 없습니다.</td></tr>\n";
			echo "<tr><td align=center><input type=button value='닫 기' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:20px;width:70\" onclick=\"window.close()\"></td></tr>\n";
			echo "</table>";
			exit;
		}
		mysql_free_result($result);
	}
?>
	<tr><td height=10></td></tr>
	<tr>
		<td style="padding-left:20">
		<img src="<?=$Dir?>images/common/orderdetailpop_img.gif" border=0 align=absmiddle>
		&nbsp;&nbsp;&nbsp;
		<img src="<?=$Dir?>images/common/orderdetailpop_arrow.gif" border=0 align=absmiddle>
		<FONT COLOR="#EE1A02"><B><?=$_ord->sender_name?></B></FONT>님께서 <FONT COLOR="#111682"><?=substr($_ord->ordercode,0,4)?>년 <?=substr($_ord->ordercode,4,2)?>월 <?=substr($_ord->ordercode,6,2)?>일</FONT> 주문하신 내역입니다.
		</td>
	</tr>
	<tr><td height=10></td></tr>
	<tr>
		<td><span style="float:left"><img src=<?=$Dir?>images/icon_dot.gif border=0 align=absmiddle> <B>주문상품 정보</B></span>
<?
if (strlen($ordercodeid)>0 && $_ord->deli_gbn=="Y") {
	/* 전자세금계산서 발행 설정 체크 */
	$sql = "SELECT COUNT(*) as cnt FROM tblshopbillinfo where bill_state ='Y' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$shopBill = (int)$row->cnt;
	mysql_free_result($result);

	if($shopBill>0){

		include_once($Dir."lib/cfg.php");
		$SBinfo = new Shop_Billinfo();
		$HB = new Hiworks_Bill( $SBinfo->domain, $SBinfo->license_id, $SBinfo->license_no, $SBinfo->partner_id );
		$sql = "SELECT COUNT(*) as cnt FROM tblmemcompany WHERE memid='".$_ord->ordercode."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$companyinfo = (int)$row->cnt;
		mysql_free_result($result);

		$sql3 = "SELECT COUNT(*) as cnt, document_id, b_idx  FROM tblorderbill WHERE ordercode='".$_ord->ordercode."' ";
		$result3=mysql_query($sql3,get_db_conn());
		$row3=mysql_fetch_object($result3);
		$billcnt = (int)$row3->cnt;
		$document_id = $row3->document_id ;
		$b_idx = $row3->b_idx ;
		mysql_free_result($result3);
		echo "<span style=\"float:right\">";
		if($billcnt>0){//신청
			$HB->set_document_id($document_id);
			$documet_result_array = $HB->check_document( HB_SOAPSERVER_URL );
			echo "<A HREF=\"javascript:viewBill('".$b_idx."')\">".$document_status[$documet_result_array[0]["now_state"]]."</a>";
		}else{
			if($companyinfo>0){
				echo "<input type='button' value='세금계산서 신청' onclick=\"sendBill('".$_ord->ordercode."')\" onmouseover=\"window.status='세금계산서 신청';return true;\" onmouseout=\"window.status='';return true;\" style='cursor:pointer;'>";
			}else{
				echo "<A HREF=\"javascript:sendBillPop('".$_ord->ordercode."')\" onmouseover=\"window.status='세금계산서 신청';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/mypage_detailview.gif\" border=\"0\"></A>";
			}
		}
		echo "</span>";
	}

}
?>
		</td>
	</tr>
	<tr>
		<td>
		<table border=0 cellpadding=0 cellspacing=1 bgcolor=E7E7E7 width=100% style="table-layout:fixed">
		<col width=45></col>
		<col width=></col>
		<col width=75></col>
		<col width=75></col>
		<col width=30></col>
		<col width=70></col>
		<col width=30></col>
		<col width=70></col>
		<col width=100></col>
		<tr height=28 bgcolor=#F5F5F5>
			<td align=center>이미지</td>
			<td align=center>상품명</td>
			<td align=center>선택사항1</td>
			<td align=center>선택사항2</td>
			<td align=center>수량</td>
			<td align=center>가격</td>
			<td align=center>메모</td>
			<td align=center>처리상태</td>
			<td align=center>배송조회</td>
		</tr>
<?
		//$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
		$delicomlist=getDeliCompany();
		$orderproducts = getOrderProduct($row->ordercode);

		$cnt=0;
		$gift_check="N";
		$taxsaveprname="";
		$etcdata=array();
		$giftdata=array();
		$in_reserve=0;
		//while($row=mysql_fetch_object($result)) {
		$orderproductsCnt = count($orderproducts);
		foreach($orderproducts as $row) {

			if (substr($row->productcode,0,3)=="999" || substr($row->productcode,0,3)=="COU") {

				if ($gift_check=="N" && strpos($row->productcode,"GIFT")!==false) $gift_check="Y";

				$etcdata[]=$row;

				if(strpos($row->productcode,"GIFT")!==false) {
					$giftdata[]=$row;
				}

				continue;
			}
			$gift_tempkey=$row->tempkey;
			$taxsaveprname.=$row->productname.",";

			$optvalue="";
			if(ereg("^(\[OPTG)([0-9]{3})(\])$",$row->opt1_name)) {
				$optioncode=$row->opt1_name;
				$row->opt1_name="";
				$sql = "SELECT opt_name FROM tblorderoption WHERE ordercode='".$ordercode."' AND productcode='".$row->productcode."' ";
				$sql.= "AND opt_idx='".$optioncode."' ";
				$result2=mysql_query($sql,get_db_conn());
				if($row2=mysql_fetch_object($result2)) {
					$optvalue=$row2->opt_name;
				}
				mysql_free_result($result2);
			}

			if($row->status!='RC') $in_reserve+=$row->quantity*$row->reserve;


			$packagestr = "";
			$packageliststr = "";
			$assemblestr = "";
			$rowspanstr = 1;
			if(strlen(str_replace("","",str_replace(":","",str_replace("=","",$row->assemble_info))))>0) {
				$assemble_infoall_exp = explode("=",$row->assemble_info);

				if($row->package_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))>0) {
					$rowspanstr++;
					$package_info_exp = explode(":", $assemble_infoall_exp[0]);
					$packagestr.="<br><img src=\"".$Dir."images/common/icn_package.gif\" border=0 align=absmiddle> ".$package_info_exp[3]."(<font color=#FF3C00>+".number_format($package_info_exp[2])."원</font>)";
					$productname_package_list_exp = explode("",$package_info_exp[1]);
					$packageliststr.="<tr bgcolor=\"#FFFFFF\">\n";
					$packageliststr.="	<td colspan=\"6\" style=\"padding-left:5px;\">\n";
					$packageliststr.= "	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
					$packageliststr.= "	<tr>\n";
					if(count($productname_package_list_exp)>0 && strlen($productname_package_list_exp[0])>0) {
						$packageliststr.= "		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
						$packageliststr.= "		<td width=\"100%\">\n";
						$packageliststr.= "		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;\">\n";

						for($k=0; $k<count($productname_package_list_exp); $k++) {
							$packageliststr.= "		<tr>\n";
							$packageliststr.= "			<td bgcolor=\"#FFFFFF\"".($k>0?"style=\"border-top:1px #DDDDDD solid;\"":"").">\n";
							$packageliststr.= "			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
							$packageliststr.= "			<col width=\"\"></col>\n";
							$packageliststr.= "			<col width=\"124\"></col>\n";
							$packageliststr.= "			<tr>\n";
							$packageliststr.= "				<td style=\"padding:4px;word-break:break-all;font-size:8pt;\"><font color=\"#000000\">".$productname_package_list_exp[$k]."</font>&nbsp;</td>\n";
							$packageliststr.= "				<td align=\"center\" style=\"padding:4px;border-left:1px #DDDDDD solid;font-size:8pt;\">본 상품 1개당 수량1개</td>\n";
							$packageliststr.= "			</tr>\n";
							$packageliststr.= "			</table>\n";
							$packageliststr.= "			</td>\n";
							$packageliststr.= "		</tr>\n";
						}
						$packageliststr.= "		</table>\n";
						$packageliststr.= "		</td>\n";
					} else {
						$packageliststr.= "		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
						$packageliststr.= "		<td width=\"100%\">\n";
						$packageliststr.= "		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;\">\n";
						$packageliststr.= "		<tr>\n";
						$packageliststr.= "			<td bgcolor=\"#FFFFFF\" style=\"padding:4px;word-break:break-all;font-size:8pt;\"><font color=\"#000000\">구성상품이 존재하지 않는 패키지</font></td>\n";
						$packageliststr.= "		</tr>\n";
						$packageliststr.= "		</table>\n";
						$packageliststr.= "		</td>\n";
					}
					$packageliststr.= "	</tr>\n";
					$packageliststr.= "	</table>\n";
					$packageliststr.="	</td>\n";
					$packageliststr.="</tr>\n";
				}

				if($row->assemble_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[1])))>0) {
					$rowspanstr++;
					$assemblestr.="<tr bgcolor=\"#FFFFFF\">\n";
					$assemblestr.="	<td colspan=\"6\" style=\"padding-left:5px;\">\n";
					$assemblestr.="	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
					$assemblestr.="	<tr>\n";
					$assemblestr.="		<td width=\"50\" valign=\"top\" style=\"padding-left:5px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
					$assemblestr.="		<td width=\"100%\">\n";
					$assemblestr.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;\">\n";

					$assemble_info_exp = explode(":", $assemble_infoall_exp[1]);

					if(count($assemble_info_exp)>2) {
						$assemble_productname_exp = explode("", $assemble_info_exp[1]);
						$assemble_sellprice_exp = explode("", $assemble_info_exp[2]);

						for($k=0; $k<count($assemble_productname_exp); $k++) {
							$assemblestr.="		<tr>\n";
							$assemblestr.="			<td bgcolor=\"#FFFFFF\"".($k>0?"style=\"border-top:1px #DDDDDD solid;\"":"").">\n";
							$assemblestr.="			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
							$assemblestr.="			<col width=\"\"></col>\n";
							$assemblestr.="			<col width=\"67\"></col>\n";
							$assemblestr.="			<col width=\"124\"></col>\n";
							$assemblestr.="			<tr>\n";
							$assemblestr.="				<td style=\"padding:4px;word-break:break-all;font-size:8pt;\">".$assemble_productname_exp[$k]."&nbsp;</td>\n";
							$assemblestr.="				<td align=\"right\" style=\"padding:4px;border-left:1px #DDDDDD solid;border-right:1px #DDDDDD solid;font-size:8pt\">".number_format((int)$assemble_sellprice_exp[$k])."</td>\n";
							$assemblestr.="				<td align=\"center\" style=\"padding:4px;font-size:8pt\">본 상품 1개당 수량1개</td>\n";
							$assemblestr.="			</tr>\n";
							$assemblestr.="			</table>\n";
							$assemblestr.="			</td>\n";
							$assemblestr.="		</tr>\n";
						}
					}
					@mysql_free_result($alproresult);
					$assemblestr.="		</table>\n";
					$assemblestr.="		</td>\n";
					$assemblestr.="	</tr>\n";
					$assemblestr.="	</table>\n";
					$assemblestr.="	</td>\n";
					$assemblestr.="</tr>\n";
				}
			}

			echo "<tr bgcolor=#FFFFFF>\n";
			echo "	<td align=center rowspan=\"".$rowspanstr."\" style=\"padding:2px;\">\n";

			if(strlen($row->minimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->minimage)==true){
				echo "		<span onMouseOver='ProductMouseOver($cnt)' onMouseOut=\"ProductMouseOut('primage".$cnt."');\">";
				echo "		<img src=".$Dir.DataDir."shopimages/product/".urlencode($row->minimage)." border=0 width=40 height=40>";
				echo "		</span>\n";
				echo "		<div id=primage".$cnt." style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
				echo "		<table border=0 cellspacing=0 cellpadding=0 width=170>\n";
				echo "		<tr bgcolor=#FFFFFF>\n";
				echo "			<td align=center width=100% height=150 style=\"border:#000000 solid 1px\"><img src=".$Dir.DataDir."shopimages/product/".urlencode($row->minimage)."></td>\n";
				echo "		</tr>\n";
				echo "		</table>\n";
				echo "		</div>\n";
			} else {
				echo '<img src="'.$Dir.'images/no_img.gif" border=0 width=40 height=40>';
			}
			echo "	</td>\n";
			echo "	<td style=\"font-size:8pt;padding:5,5,5,5\">";
			if (substr($row->productcode,0,3)!="999" && substr($row->productcode,0,3)!="COU") {
				echo "<a href=\"javascript:view_product('".$row->productcode."')\">";
			}
			echo ($row->sumprice<0?"<font color=#0000FF>":"").$row->productname.(strlen($row->addcode)>0?" - $row->addcode":"")."</a>";
			if(strlen($optvalue)>0) {
				echo "<br><img src=\"".$Dir."images/common/icn_option.gif\" border=0 align=absmiddle> ".$optvalue."";
			}
			if(strlen($packagestr)>0) {
				echo $packagestr;
			}
			echo "	</td>\n";
			echo "	<td align=center style=\"font-size:8pt\">".$row->opt1_name."</td>\n";
			echo "	<td align=center style=\"font-size:8pt\">".$row->opt2_name."</td>\n";
			echo "	<td align=center style=\"font-size:8pt\">".$row->quantity."</td>\n";
			echo "	<td align=right style=\"font-size:8pt;padding-right:5\"><FONT COLOR=\"#EE1A02\"><B>".number_format($row->sumprice)."</B></FONT></td>\n";
			if(strlen($row->order_prmsg)>0) {
				echo "	<td align=center style=\"font-size:8pt;color:red\"><a style=\"cursor:hand;\" onMouseOver='MemoMouseOver($cnt)' onMouseOut=\"MemoMouseOut($cnt);\">메모</a>";
				echo "	<div id=memo".$cnt." style=\"left:160px;position:absolute; z-index:100; visibility:hidden;\">\n";
				echo "	<table width=400 border=0 cellspacing=0 cellpadding=0 bgcolor=#A47917>\n";
				echo "	<tr>\n";
				echo "		<td style=\"padding:5;line-height:12pt\"><font color=#FFFFFF>".nl2br(strip_tags($row->order_prmsg))."</td>\n";
				echo "	</tr>";
				echo "	</table>\n";
				echo "	</div>\n";
				echo "	</td>\n";
			} else {
				echo "	<td align=center style=\"font-size:8pt\">-</td>\n";
			}
			echo "	<td align=center style=\"font-size:8pt\" rowspan=\"".$rowspanstr."\">";

			echo orderProductDeliStatusStr($row,$_ord, $orderproductsCnt);

			
			echo "	</td>\n";
			echo "	<td align=center style=\"font-size:8pt\" rowspan=\"".$rowspanstr."\">";
			$deli_url="";
			$trans_num="";
			$company_name="";
			if($row->deli_gbn=="Y") {
				if($row->deli_com>0 && $delicomlist[$row->deli_com]) {
					$deli_url=$delicomlist[$row->deli_com]->deli_url;
					$trans_num=$delicomlist[$row->deli_com]->trans_num;
					$company_name=$delicomlist[$row->deli_com]->company_name;
					echo $company_name."<br>";
					if(strlen($row->deli_num)>0 && strlen($deli_url)>0) {
						if(strlen($trans_num)>0) {
							$arrtransnum=explode(",",$trans_num);
							$pattern=array("(\[1\])","(\[2\])","(\[3\])","(\[4\])");
							$replace=array(substr($row->deli_num,0,$arrtransnum[0]),substr($row->deli_num,$arrtransnum[0],$arrtransnum[1]),substr($row->deli_num,$arrtransnum[0]+$arrtransnum[1],$arrtransnum[2]),substr($row->deli_num,$arrtransnum[0]+$arrtransnum[1]+$arrtransnum[2],$arrtransnum[3]));
							$deli_url=preg_replace($pattern,$replace,$deli_url);
						} else {
							$deli_url.=$row->deli_num;
						}
						echo "<A HREF=\"javascript:DeliSearch('".$deli_url."')\"><img src=".$Dir."images/common/btn_mypagedeliview.gif border=0></A>";
					}
				} else {
					echo "-";
				}
			} else {
				echo "-";
			}
			echo "	</td>\n";
			echo "</tr>\n";
			echo $assemblestr;
			echo $packageliststr;
			$cnt++;
		}
		@mysql_free_result($result);
?>
		</table>
		</td>
	</tr>
	<?if(count($giftdata)>0){?>
	<tr><td height=10></td></tr>
	<tr>
		<td><img src=<?=$Dir?>images/icon_dot.gif border=0 align=absmiddle> <B>사은품 내역</B></td>
	</tr>
	<tr>
		<td>
		<table border=0 cellpadding=0 cellspacing=1 bgcolor=E7E7E7 width=100% style="table-layout:fixed">
		<col width=></col>
		<col width=100></col>
		<col width=100></col>
		<col width=100></col>
		<col width=100></col>
		<col width=70></col>
		<tr height=28 bgcolor=#F5F5F5>
			<td align=center>사은품명</td>
			<td align=center>선택사항1</td>
			<td align=center>선택사항2</td>
			<td align=center>선택사항3</td>
			<td align=center>선택사항4</td>
			<td align=center>수량</td>
		</tr>
<?
		for($i=0;$i<count($giftdata);$i++) {
			echo "<tr bgcolor=#FFFFFF height=22>\n";
			echo "	<td align=center style=\"font-size:8pt;padding:5,5,5,5\">".$giftdata[$i]->productname."</td>\n";
			echo "	<td align=center style=\"font-size:8pt\">".$giftdata[$i]->opt1_name."</td>\n";
			echo "	<td align=center style=\"font-size:8pt\">".$giftdata[$i]->opt2_name."</td>\n";
			echo "	<td align=center style=\"font-size:8pt\">".$giftdata[$i]->opt3_name."</td>\n";
			echo "	<td align=center style=\"font-size:8pt\">".$giftdata[$i]->opt4_name."</td>\n";
			echo "	<td align=center style=\"font-size:8pt\">".$giftdata[$i]->quantity."</td>\n";

			echo "</tr>\n";
			echo "<tr bgcolor=#FFFFFF height=22><td colspan=6> 고객요청사항 : ".$giftdata[$i]->assemble_info."</td></tr>";
		}
?>
		</table>
		</td>
	</tr>
	<?}?>
	<tr><td height=20></td></tr>
	<tr>
		<td><img src=<?=$Dir?>images/icon_dot.gif border=0 align=absmiddle> <B>추가비용/할인/적립내역</B></td>
	</tr>
	<tr>
		<td>
		<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
		<col width=90></col>
		<col width=220></col>
		<col width=70></col>
		<col width=70></col>
		<col width=></col>
		<tr height=28 align=center bgcolor=F5F5F5>
			<td>항목</td>
			<td>내용</td>
			<td>금액</td>
			<td>적립액</td>
			<td>해당 상품명</td>
		</tr>
<?
		$etcdata = getOrderAddtional($row->ordercode);
		
		for($i=0;$i<count($etcdata);$i++) {
			$in_reserve+=$etcdata[$i]->reserve;
			if(ereg("^(COU)([0-9]{8,10})(X)$",$etcdata[$i]->productcode)) {				#쿠폰
				echo "<tr bgcolor=#FFFFFF>\n";
				echo "	<td align=center style=\"padding:7,5;font-size:8pt;line-height:10pt\">쿠폰 사용</td>\n";
				echo "	<td style=\"padding:7,5;font-size:8pt;line-height:10pt\">".$etcdata[$i]->productname."</td>\n";
				echo "	<td align=right style=\"padding:7,5;font-size:8pt;line-height:10pt\">".($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")."</td>\n";
				echo "	<td align=right style=\"padding:7,5;font-size:8pt;line-height:10pt\">".($etcdata[$i]->reserve!=0?number_format($etcdata[$i]->reserve)."원":"&nbsp;")."</td>\n";
				echo "	<td style=\"padding:7,5;font-size:8pt;line-height:10pt\">".$etcdata[$i]->order_prmsg."</td>\n";
				echo "</tr>\n";
			} else if(ereg("^(9999999999)([0-9]{1})(X)$",$etcdata[$i]->productcode)) {
				if($etcdata[$i]->productcode=="99999999999X") {
					echo "<tr bgcolor=#FFFFFF>\n";
					echo "	<td align=center style=\"padding:7,5;font-size:8pt;line-height:10pt\">결제 할인</td>\n";
					echo "	<td style=\"padding:7,5;font-size:8pt;line-height:10pt\">".$etcdata[$i]->productname."</td>\n";
					echo "	<td align=right style=\"padding:7,5;font-size:8pt;line-height:10pt\">".($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")."</td>\n";
					echo "	<td align=right style=\"padding:7,5;font-size:8pt;line-height:10pt\">".($etcdata[$i]->reserve!=0?number_format($etcdata[$i]->reserve)."원":"&nbsp;")."</td>\n";
					echo "	<td style=\"padding:7,5;font-size:8pt;line-height:10pt\" align=center>주문서 전체적용</td>\n";
					echo "</tr>\n";
				} else if($etcdata[$i]->productcode=="99999999998X") {
					echo "<tr bgcolor=#FFFFFF>\n";
					echo "	<td align=center style=\"padding:7,5;font-size:8pt;line-height:10pt\">결제 수수료</td>\n";
					echo "	<td style=\"padding:7,5;font-size:8pt;line-height:10pt\">".$etcdata[$i]->productname."</td>\n";
					echo "	<td align=right style=\"padding:7,5;font-size:8pt;line-height:10pt\">".($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")."</td>\n";
					echo "	<td align=right style=\"padding:7,5;font-size:8pt;line-height:10pt\">".($etcdata[$i]->reserve!=0?number_format($etcdata[$i]->reserve)."원":"&nbsp;")."</td>\n";
					echo "	<td style=\"padding:7,5;font-size:8pt;line-height:10pt\" align=center>주문서 전체적용</td>\n";
					echo "</tr>\n";
				} else if($etcdata[$i]->productcode=="99999999990X") {
					echo "<tr bgcolor=#FFFFFF>\n";
					echo "	<td align=center style=\"padding:7,5;font-size:8pt;line-height:10pt\">배송료</td>\n";
					echo "	<td style=\"padding:7,5;font-size:8pt;line-height:10pt\">".$etcdata[$i]->productname."</td>\n";
					echo "	<td align=right style=\"padding:7,5;font-size:8pt;line-height:10pt\">".($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")."</td>\n";
					echo "	<td align=right style=\"padding:7,5;font-size:8pt;line-height:10pt\">".($etcdata[$i]->reserve!=0?number_format($etcdata[$i]->reserve)."원":"&nbsp;")."</td>\n";
					echo "	<td style=\"padding:7,5;font-size:8pt;line-height:10pt\">".$etcdata[$i]->order_prmsg."</td>\n";
					echo "</tr>\n";
				} else if($etcdata[$i]->productcode=="99999999997X") {
					echo "<tr bgcolor=#FFFFFF>\n";
					echo "	<td align=center style=\"padding:7,5;font-size:8pt;line-height:10pt\">부가세(VAT)</td>\n";
					echo "	<td style=\"padding:7,5;font-size:8pt;line-height:10pt\">".$etcdata[$i]->productname."</td>\n";
					echo "	<td align=right style=\"padding:7,5;font-size:8pt;line-height:10pt\">".($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."원":"&nbsp;")."</td>\n";
					echo "	<td align=right style=\"padding:7,5;font-size:8pt;line-height:10pt\"></td>\n";
					echo "	<td style=\"padding:7,5;font-size:8pt;line-height:10pt\" align=center>주문서 전체적용</td>\n";
					echo "</tr>\n";
				}
			}
		}
		$dc_price=(int)$_ord->dc_price;
		$salemoney=0;
		$salereserve=0;
		if($dc_price<>0) {
			if($dc_price>0) $salereserve=$dc_price;
			else $salemoney=-$dc_price;
			if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
				$sql = "SELECT b.group_name FROM tblmember a, tblmembergroup b ";
				$sql.= "WHERE a.id='".$_ord->id."' AND b.group_code=a.group_code AND MID(b.group_code,1,1)!='M' ";
				$result=mysql_query($sql,get_db_conn());
				if($row=mysql_fetch_object($result)) {
					$group_name=$row->group_name;
				}
				mysql_free_result($result);
			}
			echo "<tr bgcolor=#FFFFFF>\n";
			echo "	<td align=center style=\"padding:7,5;font-size:8pt;line-height:10pt\">그룹적립/할인</td>\n";
			echo "	<td style=\"padding:7,5;font-size:8pt;line-height:10pt\">그룹회원 적립/할인 : ".$group_name."</td>\n";
			echo "	<td align=right style=\"padding:7,5;font-size:8pt;line-height:10pt\">".($salemoney>0?"-".number_format($salemoney)."원":"&nbsp;")."</td>\n";
			echo "	<td align=right style=\"padding:7,5;font-size:8pt;line-height:10pt\">".($salereserve>0?"+ ".number_format($salereserve)."원":"&nbsp;")."</td>\n";
			echo "	<td align=center style=\"padding:7,5;font-size:8pt;line-height:10pt\">주문서 전체 적용</td>\n";
			echo "</tr>\n";
			$in_reserve+=$salereserve;
		}

		if($_ord->reserve>0) {
			echo "<tr bgcolor=#FFFFFF>\n";
			echo "	<td align=center style=\"padding:7,5;font-size:8pt;line-height:10pt\">적립금 사용</td>\n";
			echo "	<td style=\"padding:7,5;font-size:8pt;line-height:10pt\">결제시 적립금 ".number_format($_ord->reserve)."원 사용</td>\n";
			echo "	<td align=right style=\"padding:7,5;font-size:8pt;line-height:10pt\">-".number_format($_ord->reserve)."원</td>\n";
			echo "	<td align=right style=\"padding:7,5;font-size:8pt;line-height:10pt\">&nbsp;</td>\n";
			echo "	<td align=center style=\"padding:7,5;font-size:8pt;line-height:10pt\">주문서 전체 적용</td>\n";
			echo "</tr>\n";

			//환원된 적립금
			$sql = "SELECT * FROM part_cancel_reserve WHERE ordercode='".$ordercode."' order by reg_date asc";
			$result=mysql_query($sql,get_db_conn());

			while( $row=mysql_fetch_object($result)) {

			echo "<tr bgcolor=#FFFFFF>\n";
			echo "	<td align=center style=\"padding:7,5;font-size:8pt;line-height:10pt\">적립금 환원</td>\n";
			echo "	<td style=\"padding:7,5;font-size:8pt;line-height:10pt\">적립금 ".number_format($row->cancel_reserve)."원 환원</td>\n";
			echo "	<td align=right style=\"padding:7,5;font-size:8pt;line-height:10pt\">&nbsp;</td>\n";
			echo "	<td align=right style=\"padding:7,5;font-size:8pt;line-height:10pt\">".number_format($row->cancel_reserve)."원</td>\n";
			echo "	<td align=center style=\"padding:7,5;font-size:8pt;line-height:10pt\">&nbsp;</td>\n";
			echo "</tr>\n";

			}

		}
?>
		</table>
		</td>
	</tr>
	<tr><td height=10></td></tr>
<?
	if($_ord->price>0) {
?>
	<tr>
		<td align=center style="font-size:10pt;color:#EE1A02">
		<B>결제 합계금액 : <?=number_format($_ord->price)?>원</B>
<?
		if($in_reserve>0) {
			echo " &nbsp; <font style=\"color:blue;font-size:9pt\">(적립금액 : <B>".number_format($in_reserve)."</B>원)</font>";
		}
?>
		</td>
	</tr>
	<tr><td height=20></td></tr>

<?
$sql = "select * from tblorderproduct where ordercode='".$_ord->ordercode."' and tempkey='".$_ord->tempkey."' and productcode='99999999995X' order by opt1_name asc";
//echo $sql;
$pcancleitems = array();
if(false !== $cres = mysql_query($sql,get_db_conn())){
	while($citem = mysql_fetch_assoc($cres)){
		array_push($pcancleitems, $citem);
	}
}
if(count($pcancleitems)){
?>
	<tr>
		<td><img src=<?=$Dir?>images/icon_dot.gif border=0 align=absmiddle> <B>부분 취소 내역</B></td>
	</tr>
	<tr>
		<td>
		<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
		<tr height=28 align=center bgcolor=F5F5F5>
			<td>항목</td>
			<td>금액</td>
			<td>일자</td>
		</tr>
<?
		$sumcancle = 0;
		for($i=0;$i<count($pcancleitems);$i++) {
			$citem = $pcancleitems[$i];
			$sumcancle +=abs($citem['price']);
?>
		<tr height=28 style=" background:#ffffff">
			<td style="padding-left:10px;"><?=$citem['productname'].' #'.$citem['opt1_name']?></td>
			<td style="text-align:right; padding-right:10px;"><?=number_format(abs($citem['price']))?>원</td>
			<td style="text-align:center"><? echo substr($citem['date'],0,4).'-'.substr($citem['date'],5,2).'-'.substr($citem['date'],7,2); ?></td>
		</tr>
<? 		} ?>

		</table>
		<div style="text-align:right; padding:10px; font-size:14px;">취소 금액 합계 : <span style="font-weight:bold; color:red"><?=number_format($sumcancle)?></span></div>
		</td>
	</tr>
	<tr><td height=10></td></tr>
<? } ?>

	<tr>
		<td align=center>
		<table border=0 cellpadding=5 cellspacing=1 width=100% bgcolor=#E7E7E7 style="table-layout:fixed">
		<col width=110></col>
		<col width=></col>
		<?if(strlen($ordercode)==21 && substr($ordercode,-1)=="X"){?>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">주문확인번호</td>
			<td bgcolor=#ffffff style="padding:7,10"><b><?=substr($_ord->id,1,6)?></td>
		</tr>
		<?}?>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">주문일자</td>
			<td bgcolor=#ffffff style="padding:7,10"><?=substr($ordercode,0,4).".".substr($ordercode,4,2).".".substr($ordercode,6,2)?></td>
		</tr>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">받는사람</td>
			<td bgcolor=#ffffff style="padding:7,10"><?=$_ord->receiver_name?></td>
		</tr>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">배달주소</td>
			<td bgcolor=#ffffff style="padding:7,10"><?=ereg_replace("주소 :","<br>주소 :",$_ord->receiver_addr)?></td>
		</tr>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">결제방법</td>
			<td bgcolor=#ffffff style="padding:7,10">
<?
			if (preg_match("/^(B|O|Q){1}/",$_ord->paymethod)) {	//무통장, 가상계좌, 가상계좌 에스크로
				if($_ord->paymethod=="B") echo "<font color=#FF5D00>무통장입금</font>\n";
				else if(substr($_ord->paymethod,0,1)=="O") echo "<font color=#FF5D00>가상계좌</font>\n";
				else echo "매매보호 - 가상계좌";

				if(!preg_match("/^(C|D)$/",$_ord->deli_gbn) || $_ord->paymethod=="B") echo "【 ".$_ord->pay_data." 】";
				else echo "【 계좌 취소 】";

				if (strlen($_ord->bank_date)>=12) {
					echo "</td>\n</tr>\n";
					echo "<tr>\n";
					echo "	<td align=center bgcolor=#F5F5F5 style=\"padding:7,10\">입금확인</td>\n";
					echo "	<td bgcolor=#ffffff style=\"padding:7,10\"><font color=red>".substr($_ord->bank_date,0,4)."/".substr($_ord->bank_date,4,2)."/".substr($_ord->bank_date,6,2)." (".substr($_ord->bank_date,8,2).":".substr($_ord->bank_date,10,2).")</font>";
				} else if(strlen($_ord->bank_date)==9) {
					echo "</td>\n</tr>\n";
					echo "<tr>\n";
					echo "	<td align=center bgcolor=#F5F5F5 style=\"padding:7,10\">입금확인</td>\n";
					echo "	<td bgcolor=#ffffff style=\"padding:7,10\">환불";
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
?>
			</td>
		</tr>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">결제금액</td>
			<td bgcolor=#ffffff style="padding:7,10"><font color=#0000FF><b><?=number_format($_ord->price)."원</b>".($_ord->reserve>0?"(적립금 ".number_format($_ord->reserve)."원 공제)":"")?></font></td>
		</tr>
<?
		$order_msg=explode("[MEMO]",$_ord->order_msg);
		if(strlen($order_msg[0])>0) {
?>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">고객메모</td>
			<td bgcolor=#ffffff style="padding:7,10">
			<?=nl2br($order_msg[0])?>
			</td>
		</tr>
		<?}?>
		<?if(strlen($order_msg[2])>0) {?>
		<tr>
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">상점메모</td>
			<td bgcolor=#ffffff style="padding:7,10">
			<?=nl2br($order_msg[2])?>
			</td>
		</tr>
		<? } ?>
		<?
		if( preg_match("/^(B){1}/", $_ord->paymethod) && strlen($_ord->bank_date)==14 && $_ord->deli_gbn=="N" && getDeligbn("N",true)){//무통장 입금 완료 , 미처리 상태 일때 출력

		?>
		<tr id="refundAccount">
			<td align=center bgcolor=#F5F5F5 style="padding:7,10">환불계좌</td>
			<td bgcolor=#ffffff style="padding:7,10">
				<form name="refundAccountForm">
                <table>
				
					<tr>
						<td width="100">
							은행명 : </td><td><input type="text" size="15" name="bank_name" maxlength="30" style="text-align:center;" />
						</td>
					<tr>
						<td width="100">
							예금주명 : </td><td><input type="text" size="15" name="bank_owner" maxlength="4" style="text-align:center;" />
						</td>
					</tr>
					<tr>
						<td width="100">
							계좌번호 입력 : </td><td><input type="text" size="30" name="bank_num" style="text-align:center;" />
						</td>
					</tr>				
				</table>
                </form>
			</td>
		</tr>
		<?}?>
		</table>
		</td>
	</tr>
<?
	}
?>
	<tr><td height=10></td></tr>
	<tr>
		<td align=center>
		<A HREF="javascript:window.close()"><img src="<?=$Dir?>images/common/orderdetailpop_close.gif" align=absmiddle border=0></A>
<?
		if($print!="OK") {
			if (
			   ($_data->ordercancel==0 && ($_ord->deli_gbn=="S" || $_ord->deli_gbn=="N") && getDeligbn("S|N",true))/*주문배송완료 전에 취소가 가능하며 발송준비된 주문서 또는 미처리된 주문서일 경우 가능*/
			|| ($_data->ordercancel==2 && $_ord->deli_gbn=="N" && getDeligbn("N",true))/*주문배송준비 전에만 취소가 가능하며 미처리된 주문서일 경우 가능*/
			|| ($_data->ordercancel==1 && preg_match("/^(B){1}/", $_ord->paymethod) && strlen($_ord->bank_date)<12 && $_ord->deli_gbn=="N" && getDeligbn("N",true)) /*주문결제완료 전에만 취소가 가능하며 무통장입금으로 입금전 미처리된 주문서일 경우 가능*/
			) {
				if(!preg_match("/^(Q){1}/", $_ord->paymethod)) {
					echo "<a href=\"javascript:order_cancel('".$_ord->tempkey."', '".$_ord->ordercode."','".$_ord->bank_date."')\" onMouseOver=\"window.status='주문취소';return true;\"><img src=\"".$Dir."images/common/orderdetailpop_ordercancel.gif\" align=absmiddle border=0></a>\n";
				}
			} else if($_data->ordercancel==1 && (($_ord->paymethod=="B" && strlen($_ord->bank_date)>=12) || ( preg_match("/^(C|P){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) && $_ord->deli_gbn=="N" && getDeligbn("N",true)){
				if(strlen($_data->nocancel_msg)==0) $_data->nocancel_msg="주문취소가 되지 않습니다.\\n쇼핑몰에 문의하세요.";
				echo "<a href=\"javascript:alert('".$_data->nocancel_msg."')\"><img src=\"".$Dir."images/common/orderdetailpop_ordercancel.gif\" align=absmiddle border=0></a>\n";
			}

			if($_ord->del_gbn!="A" && $_ord->del_gbn!="Y" && getDeligbn("A|Y",false)
			&& !(substr($_ord->paymethod,0,1)=="Q" && strlen($_ord->bank_date)>=12 && $_ord->deli_gbn!="C")  //매매보호 가상계좌이고 입금확인되고 주문취소가 아닌경우
			&& !(substr($_ord->paymethod,0,1)=="P" && $_ord->pay_flag=="0000" && $_ord->deli_gbn!="C")      //매매보호 신용카드이고 카드성공 주문취소가 아닌경우
			&& strlen($_ShopInfo->getMemid())>0 /* 비회원은 내용삭제안되게 */) {
				echo "<a href=\"javascript:order_del('".$_ord->tempkey."', '".$_ord->ordercode."')\" onMouseOver=\"window.status='내용삭제';return true;\"><img src=\"".$Dir."images/common/orderdetailpop_del.gif\" align=absmiddle border=0></a>\n";
			}

			/*
			if(preg_match("/^(B|O|Q){1}/", $_ord->paymethod) && $_ord->deli_gbn!="C") {
				if($_data->tax_type!="N" && $_ord->price>=1) {
					echo "<a href=\"javascript:get_taxsave('".$_ord->ordercode."')\" onMouseOver=\"window.status='현금영수증';return true;\"><img src=\"".$Dir."images/common/orderdetailpop_cashbill.gif\" align=absmiddle border=0></a>\n";
				}
			}
			*/


			// 기존 현금 영수증 발행 관련 기능에 추가 하여 세금 계산서 영역 처리
			if($_data->tax_type!="N" && $_ord->price>=1) {
				if(preg_match("/^(B|O|Q){1}/", $_ord->paymethod) && $_ord->deli_gbn!="C"){
					$reqItem = '';
					if(false !== $cres = mysql_query("select count(*) from tbltaxsavelist WHERE ordercode='".$ordercode."'",get_db_conn())){
						if(mysql_result($cres,0,0)) $reqItem = 'taxsave';
					}
					if( !_empty($reqItem) && $_ord->deli_gbn == 'Y'){
						if(false !== $cres = mysql_query("select bill_idx from bill_basic WHERE ordercode='".$ordercode."'",get_db_conn())){
							if(mysql_num_rows($cres)){
								$reqItem = 'bill';
								$bill_idx = mysql_result($cres,0,0);
							}
						}
					}
					if($reqItem != 'bill'){
						echo "<a href=\"javascript:get_taxsave('".$_ord->ordercode."')\" onMouseOver=\"window.status='현금영수증';return true;\"><img src=\"".$Dir."images/common/orderdetailpop_cashbill.gif\" align=\"absmiddle\" border=0></A>\n";
					}
					if($reqItem != 'taxsave'  && $_ord->deli_gbn == 'Y'){
						if(_isInt($bill_idx)){
							echo "<A HREF=\"javascript:viewBill('".$bill_idx."')\" onmouseover=\"window.status='세금계산서 신청';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/mypage_reqbill.gif\" alt=\"세금계산서 신청\" border=\"0\" align=\"absmiddle\"></A>";

						}else{
							echo "<A HREF=\"javascript:sendBillPop('".$_ord->ordercode."')\" onmouseover=\"window.status='세금계산서 신청';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/mypage_reqbill.gif\" alt=\"세금계산서 신청\" border=\"0\" align=absmiddle></A>";
						}
					}
				}
			}


			if(((substr($_ord->paymethod,0,1)=="P" && $_ord->pay_admin_proc=="Y") || (substr($_ord->paymethod,0,1)=="Q" && $_ord->pay_flag=="0000")) && !preg_match("/^(Y|C)$/",$_ord->escrow_result) && $_ord->deli_gbn!="C") {
				/*
				에스크로 정보를 가지고 온다.
				*/
				$pgid_info="";
				$pg_type="";
				switch (substr($_ord->paymethod,0,1)) {
					case "B":
						break;
					case "V":
						$pgid_info=GetEscrowType($_data->trans_id);
						$pg_type=$pgid_info["PG"];
						break;
					case "O":
						$pgid_info=GetEscrowType($_data->virtual_id);
						$pg_type=$pgid_info["PG"];
						break;
					case "Q":
						$pgid_info=GetEscrowType($_data->escrow_id);
						$pg_type=$pgid_info["PG"];
						break;
					case "C":
						$pgid_info=GetEscrowType($_data->card_id);
						$pg_type=$pgid_info["PG"];
						break;
					case "P":
						$pgid_info=GetEscrowType($_data->card_id);
						$pg_type=$pgid_info["PG"];
						break;
					case "M":
						$pgid_info=GetEscrowType($_data->mobile_id);
						$pg_type=$pgid_info["PG"];
						break;
				}
				$pg_type=trim($pg_type);

				// 배송처리가 되어야만 매매보호
				if ($_ord->deli_gbn=="Y") {
					echo "<a href=\"javascript:escrow_ok('".$_ord->ordercode."')\" onMouseOver=\"window.status='구매확인';return true;\"><img src=\"".$Dir."images/common/orderdetailpop_okorder.gif\" align=absmiddle border=0></a>\n";
				} else if (substr($_ord->paymethod,0,1)=="Q" && !preg_match("/^(D|E|H)$/", $_ord->deli_gbn) && getDeligbn("D|E|H",false)) {
					#<!--- 취소 ( 취소 & 환불 한꺼번에 처리) -->
					echo "<a href=\"javascript:escrow_cancel('".$_ord->tempkey."','".$_ord->ordercode."','".$_ord->bank_date."')\" onMouseOver=\"window.status='구매취소';return true;\"><img src=\"".$Dir."images/common/orderdetailpop_ordercancel.gif\" align=absmiddle border=0></a>\n";
				}
			}

			// ######### 사은품을 선택하지 않은 주문의 경우 사은품을 선택할 수 있도록 해줌
			if (($_ord->paymethod=="B" || (preg_match("/^(V|O|Q|C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) && $_ord->deli_gbn=="N" && getDeligbn("N",true) && $gift_check=="N" && $gift_type[3]=="Y") {
				if ($gift_type[2]=="A" || strlen($gift_type[2])==0 || ($gift_type[2]=="B" && $_ord->paymethod=="B")) {
					if (($gift_type[0]=="M" && strlen($_ShopInfo->getMemid())>0) || $gift_type[0]=="C") { // 회원전용, 비회원+회원
						$sql = "SELECT COUNT(*) as gift_cnt FROM tblgiftinfo ";
						if($gift_type[1]=="N") {
							$sql.= "WHERE gift_startprice<=".$gift_price." AND gift_endprice>".$gift_price." ";
						} else  {
							$sql.= "WHERE gift_startprice<=".$gift_price." ";
						}
						$sql.= "AND (gift_quantity is NULL OR gift_quantity>0) ";
						$result=mysql_query($sql,get_db_conn());
						$row=mysql_fetch_object($result);
						$gift_cnt=$row->gift_cnt;
						mysql_free_result($result);
						if ($gift_cnt>0) {
							$gift_body = "<a href=\"javascript:getGift()\"><img src='".$Dir."images/common/orderdetailpop_gift.gif' border=0 align=absmiddle></a>\n";
							$gift_body.= "<form name=giftform method=post action=\"".$Dir.FrontDir."gift_choice.php\" target=\"gift_popwin\">\n";
							$gift_body.= "<input type=hidden name=gift_price value=\"".$gift_price."\">\n";
							$gift_body.= "<input type=hidden name=ordercode value=\"".$_ord->ordercode."\">\n";
							$gift_body.= "<input type=hidden name=gift_mode value=\"orderdetailpop\">\n";
							$gift_body.= "<input type=hidden name=gift_tempkey value=\"".$gift_tempkey."\">\n";
							$gift_body.= "</form>\n";
							$gift_body.= "<script language='javascript'>\n";
							$gift_body.= "function getGift() {\n";
							$gift_body.= "	gift_popwin = window.open('about:blank','gift_popwin','width=700,height=600,scrollbars=yes');\n";
							$gift_body.= "	document.giftform.target='gift_popwin';\n";
							$gift_body.= "	document.giftform.submit();\n";
							$gift_body.= "	gift_popwin.focus();\n";
							$gift_body.= "}\n";
							$gift_body.= "</script>\n";
							echo $gift_body;
						}
					}
				}
			}
		}
?>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<form name=form1 action="orderdetailpop.php" method=post>
<input type=hidden name=tempkey>
<input type=hidden name=ordercode>
<input type=hidden name=type>
<input type=hidden name=ordercodeid value="<?=$ordercodeid?>">
<input type=hidden name=ordername value="<?=$ordername?>">
<input type=hidden name=bank_name value="">
<input type=hidden name=bank_owner value="">
<input type=hidden name=bank_num value="">
</form>
<form name=taxsaveform method=post action="<?=$Dir.FrontDir?>taxsave.php" target=taxsavepop>
<input type=hidden name=ordercode>
<input type=hidden name=productname value="<?=urlencode(titleCut(30,htmlspecialchars(strip_tags($taxsaveprname),ENT_QUOTES)))?>">
</form>
<form name=escrowform action="<?=$Dir?>paygate/okescrow.php" method=post>
<input type=hidden name=ordercode value="">
<?if($pg_type=="D") {?>
<input type=hidden name=sendtype value="">
<? } else { ?>
<input type=hidden name=sitecd value="<?=urlencode($pgid_info["ID"])?>">
<input type=hidden name=sitekey value="<?=urlencode($pgid_info["KEY"])?>">
<? } ?>
<input type=hidden name=return_host value="<?=urlencode(getenv("HTTP_HOST"))?>">
<input type=hidden name=return_script value="<?=urlencode(str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl()).FrontDir."orderdetailpop.php")?>">
<input type=hidden name=return_data value="<?=urlencode("type=okescrow&ordercode=".$ordercode)?>">
</form>

<form name=vform action="<?=$Dir?>paygate/set_bank_account.php" method=post target="baccountpop">
<input type=hidden name=ordercode value="<?=$ordercode?>">
</form>

<form name=form3 method=post>
<input type=hidden name=ordercode value="<?=$ordercode?>">
</form>

<!-- <form name=billform method=post action="<?=$Dir.FrontDir?>orderbillpop.php" target="billpop">
<input type=hidden name=ordercode>
</form> -->
<form name=billform method=post action="<?=$Dir?>bill/reqbill.php" target="billpop">
<input type=hidden name=ordercode>
</form>
<form name=billsendfrm action="orderbillsend.php" method=post target="hiddenFrame">
<input type=hidden name="ordercode">
<input type=hidden name="member" value="<?=(strlen($_ShopInfo->getMemid())==0)? "guest":$_ShopInfo->getMemid()?>">
</form>
<iframe id="hiddenFrame" name="hiddenFrame" style="width:0;height:0; position:absolute; visibility:hidden;"></iframe>
<form name=billviewFrm method=post action="orderbillview.php" target="winBill">
<input type=hidden name=b_idx value="">
</form>

<?
	switch($pg_type){
		case "B":
?>
		<SCRIPT language=JavaScript src="http://pgweb.dacom.net/js/DACOMEscrow.js"></SCRIPT>
<?
		break;
		case "G":
?>
		<script language=JavaScript charset='euc-kr' src="https://tx.allatpay.com/common/AllatCommon.js"></script>
		<script language=javascript>
			function ftn_escrowconfirm(dfm) {
			  var ret;
			  ret = invisible_EscrowConfirm(dfm);//Function 내부에서 submit을 하게 되어있음.
			  if( ret.substring(0,4)!="0000" && ret.substring(0,4)!="9999"){
				// 오류 코드 : 0001~9998 의 오류에 대해서 적절한 처리를 해주시기 바랍니다.
				alert(ret.substring(4,ret.length));		// Message 가져오기
			  }
			  if( ret.substring(0,4)=="9999" ){
				// 오류 코드 : 9999 의 오류에 대해서 적절한 처리를 해주시기 바랍니다.
				alert(ret.substring(4,ret.length));	    // Message 가져오기	  	
			  }
			}
		</script>
<?
		break;
	}
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function escrow_ok(ordercode) {	//에스크로 구매결정
	if(confirm("배송 완료된 매매보호 결제건에 대해서 구매결정/구매거절 처리 하시겠습니까?")) {
<?if($pg_type=="B"){?>
		var resdata=checkDacomESC('<?=$pgid_info["ID"]?>',ordercode,'');
		if(resdata=="0000") {
			document.form3.submit();
		}
<?}else if($pg_type=="D"){?>
		document.escrowform.sendtype.value="";
		document.escrowform.ordercode.value=ordercode;
		window.open("about:blank","okescrowpop","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizeble=no,copyhistory=no,width=100,height=100");
		document.escrowform.target="okescrowpop";
		document.escrowform.submit();
<?}else if($pg_type=="G"){?>
		document.fm.allat_order_no.value=ordercode;
		if(document.fm.allat_seq_no.value==""){
			alert("정상적인 에스크로 주문건이 아닙니다.");
			return;
		}else if(document.fm.allat_order_no.value ==""){
			alert("잘못된 주문번호로 요청이 거부되었습니다.");
			return;
		}else{
			ftn_escrowconfirm(document.fm);
		}
<?}else{?>
		document.escrowform.ordercode.value=ordercode;
		window.open("about:blank","okescrowpop","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizeble=no,copyhistory=no,width=100,height=100");
		document.escrowform.target="okescrowpop";
		document.escrowform.submit();
<?}?>
	}
}
function escrow_cancel(tempkey,ordercode,bank_date) {	//에스크로 구매취소 (가상계좌)
	if(bank_date.length>=12) {
<?if($pg_type=="D"){?>
		if(confirm("매매보호 주문서에 대해서 환불처리 요청을 하시겠습니까?")) {
			document.escrowform.sendtype.value="CNCL";
			document.escrowform.ordercode.value=ordercode;
			window.open("about:blank","okescrowpop","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizeble=no,copyhistory=no,width=100,height=100");
			document.escrowform.target="okescrowpop";
			document.escrowform.submit();
			return;
		}
<?}else{?>
		if(!confirm("환불계좌정보를 입력하시려면 [확인]을 클릭하시고,\n\n환불계좌정보를 입력하셨다면 [취소]를 클릭하세요.")) {
			if(confirm("매매보호 주문서에 대해서 환불처리 요청을 하시겠습니까?")) {
				document.form1.tempkey.value=tempkey;
				document.form1.ordercode.value=ordercode;
				document.form1.type.value="cancel";
				document.form1.submit();
			}
			return;
		}
<?} ?>
	} else {
		if(confirm("미입금 주문에 대해서 주문취소 하시겠습니까?")) {
			document.form1.tempkey.value=tempkey;
			document.form1.ordercode.value=ordercode;
			document.form1.type.value="cancel";
			document.form1.submit();
		}
		return;
	}
	window.open("about:blank","baccountpop","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizeble=no,copyhistory=no,width=100,height=100");
	document.vform.submit();
}

//-->
</SCRIPT>
</center>

<? if($print=="OK") echo "<script>print();</script>";?>
<?
	if($type == "cancel_one" ) {
		$onload="<script>order_cancel('".$_ord->tempkey."', '".$_ord->ordercode."','".$_ord->bank_date."');</script>";
	}
?>
<?
if($pg_type=="G" && substr($_ord->paymethod,0,1) == "Q"){
	$seqnoSQL = "SELECT trans_code FROM tblpvirtuallog WHERE ordercode = '".$_ord->ordercode."' ";
	if(false !== $seqnoRes = mysql_query($seqnoSQL,get_db_conn())){
		$seqnocount = mysql_num_rows($seqnoRes);
		if($seqnocount>0){
			$allat_seq_no = mysql_result($seqnoRes,0,0);
		}
		mysql_free_result($seqnoRes);
	}
}
?>
<form name="fm"  method="POST" action="/paygate/G/escrowconfirm.php"> <!--승인요청 및 결과수신페이지 지정 //-->
	<input type="hidden" name="allat_shop_id" value="<?=$pgid_info["ID"]?>" size="26" maxlength="20"><!-- 상점아이디 //-->
	<input type="hidden" name="allat_order_no" value="" size="26" maxlength="80"><!-- 주문번호 //-->
	<input type="hidden" name="allat_pay_type" value="VBANK" size="26" maxlength="6"><!-- 결제방식 CARD(카드),계좌이체(ABANK),가상계좌(VBANK),HP(휴대폰결제),TICKET(상품권 결제) -->
	<input type="hidden" name="allat_enc_data" value=""><!-- 주문정보암호화필드 -->
	<input type="hidden" name="allat_seq_no" value="<?=$allat_seq_no?>" size="26" maxlength="10"><!-- 거래일련번호 -->
	<input type="hidden" name="allat_test_yn" value="N" size="26" maxlength="1"><!-- 테스트여부 Y:테스트 N:서비스 -->
</form>
<?=$onload?>
</body>
</html>
