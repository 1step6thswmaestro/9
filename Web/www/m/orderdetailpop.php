<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/order_func.php");

#### PG ����Ÿ ���� ####
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

$ordercode=$_POST["ordercode"];	//�α����� ȸ���� ��ȸ��
$ordername=$_POST["ordername"]; //��ȸ�� ��ȸ�� �ֹ��ڸ�
$ordercodeid=$_POST["ordercodeid"];	//��ȸ�� ��ȸ�� �ֹ���ȣ 6�ڸ�
$print=$_POST["print"];	//OK�� ��� ����Ʈ

if(strlen($ordercodeid)>0 && strlen($ordercodeid)!=6) {
	echo "<html><head><title></title></head><body onload=\"alert('�ֹ���ȣ 6�ڸ��� ��Ȯ�� �Է��Ͻñ� �ٶ��ϴ�.');window.close();\"></body></html>";exit;
}

$sql = "SELECT gift FROM tblorderinfo WHERE ordercode='{$ordercode}'";
$result = mysql_query($sql,get_db_conn());
$row = mysql_fetch_array($result);

$row["gift"] = '0';

if($row["gift"]=='1'|| $row["gift"]=='2') {
	echo "<script>window.location.href='orderdetailpop2.php?ordercode={$ordercode}';</script>";
	exit;
}

$gift_type=explode("|",$_data->gift_type);

$type=$_POST["type"];
$tempkey=$_POST["tempkey"];
$rescode=$_POST["rescode"];

####### ����ũ�� ���Ű��� #######
if ($type=="okescrow" && strlen($ordercode)>0 && $rescode=="Y") {
	$sql = "UPDATE tblorderinfo SET escrow_result='Y' ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	$sql.= "AND (MID(paymethod,1,1)='Q' OR MID(paymethod,1,1)='P') ";
	$sql.= "AND deli_gbn='Y' ";
	$result = mysql_query($sql,get_db_conn());

	echo "<script>alert('���Ű��� �Ǿ����ϴ�.');self.close();</script>";
	exit;
}


####### �ֹ���� (����ũ�� ����) #######
if ($type=="cancel" || ($type=="okescrow" && $rescode=="C" && strlen($ordercode)>0)) { //�Ÿź�ȣ �ֹ�������
	$sql = "SELECT price,deli_gbn,reserve,sender_name,paymethod,bank_date FROM tblorderinfo ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	if($type=="cancel") $sql.= "AND tempkey='".$tempkey."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		if (
		(preg_match("/^(Q|P){1}/", $row->paymethod) && !preg_match("/^(C|D|E|H)$/", $row->deli_gbn) && getDeligbn("C|D|E|H",false))
		|| ($_data->ordercancel==0 && ($row->deli_gbn=="S" || $row->deli_gbn=="N") && getDeligbn("N|S",true)) //tblorderproduct�� deli_gbn�� "S|N"�� �ִ��� Ȯ���Ѵ�.
		|| ($_data->ordercancel==2 && $row->deli_gbn=="N" && getDeligbn("N",true)) //tblorderproduct�� deli_gbn�� "N"�� �ִ��� Ȯ���Ѵ�.
		|| ($_data->ordercancel=="1" && $row->paymethod=="B" && strlen($row->bank_date)<12 && $row->deli_gbn=="N" && getDeligbn("N",true))
		) {  // ��۱����� ��� ���� ����� ��������쿡�� �ֹ� ���, ���� �����ϰ�� �ԱݾȵȰǸ�
		
			if(preg_match("/^(Q|P){1}/", $row->paymethod)) $deliok="D";
			else $deliok="C";
			//printr($_POST);

			if($_POST['bank_name'] != "" ){
				$bankAccountInfo = "<br>ȯ�Ұ������� : ".$_POST['bank_name']." ".$_POST['bank_num']. "(������:". $_POST['bank_owner'].")";
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
					$sql.= "content		= '�ֹ� ��Ұǿ� ���� ������ ȯ��', ";
					$sql.= "orderdata	= '".$ordercode."=".$row->price."', ";
					$sql.= "date		= '".date("YmdHis")."' ";
					mysql_query($sql,get_db_conn());
				}

				/////////////// �ֹ���ҽ� �����ڿ��� ������ �߼�
				$maildata=$row->sender_name."������ <font color=blue>".date("Y")."�� ".date("m")."�� ".date("d")."��</font>�� �Ʒ��� ���� �ֹ��� ����ϼ̽��ϴ�.<br><br>";
				$maildata.="<li> ��ҵ� �ֹ��� ��ȣ : $ordercode<br><br>";
				$maildata.="��ҵ� �ֹ��� �����ڸ޴��� �ֹ���ȸ���� Ȯ���Ͻ� �� �ֽ��ϴ�.";

				if (strlen($_data->shopname)>0) $mailshopname = "=?ks_c_5601-1987?B?".base64_encode($_data->shopname)."?=";
				$header=getMailHeader($mailshopname,$_data->info_email);
				if(ismail($_data->info_email)) {
					sendmail($_data->info_email, $_data->shopname." �ֹ���� Ȯ�� �����Դϴ�.", $maildata, $header);
				}

				if(strlen($_data->okcancel_msg)==0)  $_data->okcancel_msg="���������� �ֹ��� ��ҵǾ����ϴ�!";
				if (preg_match("/^(Q){1}/", $row->paymethod) && strlen($row->bank_date)>=12) $_data->okcancel_msg.=" ���������� �������� ��� �� ȯ��ó���˴ϴ�.";
				if (preg_match("/^(P){1}/", $row->paymethod) && $row->pay_flag=="0000") $_data->okcancel_msg.=" ���������� �������� ��� �� ī�����ó���˴ϴ�.";

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

						//$smsmsg=$row->sender_name."�Բ��� ".substr($ordercode,0,4)."/".substr($ordercode,4,2)."/".substr($ordercode,6,2)."�� �ֹ��Ͻ� �ֹ��� ����ϼ̽��ϴ�.";
						$etcmsg="�ֹ���� �޼���(������)";
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
				$onload="<script>alert('��û�Ͻ� �۾��� ������ �߻��Ͽ����ϴ�.');</script>";
			}
		} else if (preg_match("/^(Q|P){1}/", $row->paymethod) && preg_match("/^(D)$/", $row->deli_gbn)) {
			
			$onload="<script>alert('���������� �������� ��� �� ȯ��ó���˴ϴ�.');</script>";
		} else if($_data->ordercancel==0) {
			
			if(strlen($_data->nocancel_msg)==0) $onload="<script>alert(\"�̹� ��۵� ��ǰ�� �ֽ��ϴ�. ���θ��� �����ֽñ� �ٶ��ϴ�.\");</script>";
			else $onload="<script>alert('$_data->nocancel_msg');</script>";
		} else if($_data->ordercancel==2) {
			
			if(strlen($_data->nocancel_msg)==0) $onload="<script>alert(\"�߼��غ� �Ϸ�Ǿ� �ù�ȸ�翡 ���޵� ��ǰ�� �ֽ��ϴ�. ���θ��� �����ֽñ� �ٶ��ϴ�.\");</script>";
			else $onload="<script>alert('$_data->nocancel_msg');</script>";
		} else {
			if(strlen($_data->nocancel_msg)==0) $onload="<script>alert(\"��������� ȯ��/��Ҵ� ���θ��� �����ֽñ� �ٶ��ϴ�.\");</script>";
			else $onload="<script>alert('$_data->nocancel_msg');</script>";
		}
	}
}

####### �ֹ��� ���� #######
if($type=="delete" && strlen($ordercode)>0 && strlen($tempkey)>0) {
	$sql = "SELECT del_gbn FROM tblorderinfo WHERE ordercode='".$ordercode."' AND tempkey='".$tempkey."' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);
	$del_gbn = $row->del_gbn;
	if($del_gbn=="N" || $del_gbn==NULL) $okdel="Y";
	else if($del_gbn=="A") $okdel="R";
	else {
		echo "<html><head><title></title></head><body onload=\"alert('�ش� �ֹ����� �̹� ����ó���� �Ǿ����ϴ�.');window.close();opener.location.reload();\"></body></html>";exit;
	}

	$sql = "UPDATE tblorderinfo SET del_gbn='".$okdel."' WHERE ordercode='".$ordercode."' AND tempkey='".$tempkey."' ";
	mysql_query($sql,get_db_conn());
	echo "<html><head><title></title></head><body onload=\"alert('�ش� �ֹ����� ����ó�� �Ͽ����ϴ�.');window.close();opener.location.reload();\"></body></html>";exit;
}

?>
<!DOCTYPE html>
<html>
<head>
<title>�ֹ����� �� ��ȸ</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta name="format-detection" content="telephone=no" />
<link rel="stylesheet" href="./css/common.css" />
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
	window.open(url,'�����ȸ','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=550,height=500');
}

function view_product(productcode) {
	opener.location.href="<?=$Dir?>m/productdetail.php?productcode="+productcode;
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

function order_cancel(tempkey,ordercode,bankdate) {	//�ֹ����
	//alert("�ֹ���Ұ� �Ϸ�Ǹ� ���޿����� ������ �� �ֹ��� ��������� ��� ��ҵǸ� ��ҵ� �ֹ����� �ٽ� �ǵ��� �� �����ϴ�");
	if (confirm("�ֹ���Ұ� �Ϸ�Ǹ� ���޿����� ������ �� �ֹ��� ��������� ��� ��ҵǸ� ��ҵ� �ֹ����� �ٽ� �ǵ��� �� �����ϴ�")) {
		if(bankdate != "") {
			//document.getElementById("refundAccount").style.display="block";
			if(document.refundAccountForm.bank_name.value == "" || document.refundAccountForm.bank_owner.value == "" || document.refundAccountForm.bank_num.value == "") {
				alert("ȯ�Ұ��� ������ �Է��ϼ���.");
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
function order_del(tempkey,ordercode) {	//�ֹ��� ����
	if(confirm("�ֹ��ǿ� ���ؼ� ��Ҵ� ���� �ʰ�, ��ȸ�� �Ұ����մϴ�.\n\n�ֹ��� ���븸 �����Ͻðڽ��ϱ�?")) {
		document.form1.tempkey.value=tempkey;
		document.form1.ordercode.value=ordercode;
		document.form1.type.value="delete";
		document.form1.submit();
	}
}

function get_taxsave(ordercode) {	//���ݿ����� ��û
	window.open("about:blank","taxsavepop","width=400,height=500,scrollbars=no");
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

// ���ڼ��ݰ�꼭 ����
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
		alert("��۾�ü�� �����ϼ���");
		f.deli_com.focus();
		return false;
	}
	if(!f.deli_num.value) {
		alert("�����ȣ��  �Է��ϼ���");
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
		alert("��۾�ü�� �����ϼ���");
		f.deli_com.focus();
		return false;
	}
	if(!f.deli_num.value) {
		alert("�����ȣ��  �Է��ϼ���");
		f.deli_num.focus();
		return false;
	}

	f.type.value = "deli";
	f.action = "order_oks.php";
	f.submit();

}


function order_one_cancel(ordercode, productcode, can, tempkey,uid) {
	
	if (can=="yes") {
		if (confirm("�ֹ���Ұ� �Ϸ�Ǹ� ���޿����� ������ �� �ֹ��� ��������� ��� ��ҵǸ� ��ҵ� �ֹ����� �ٽ� �ǵ��� �� �����ϴ�")) {
		window.open("<?=$Dir?>m/order_one_cancel_pop.php?ordercode="+ordercode+"&productcode="+productcode+"&uid="+uid,"one_cancel","width=610,height=500,scrollbars=yes");
		}
	}else{
		if (confirm("�Ա�Ȯ���� �ֹ��� '��ü���'�� �����մϴ�. \n��ü��Ҹ� ���Ͻô� ��� ���Ÿ� ���ϴ� ��ǰ�� �ٽ� �ֹ����ּ���.\n���ֹ��� ���� �ֹ� ��ü����Ͻðڽ��ϱ�?")) {
			if(bankdate != "") {
				document.getElementById("refundAccount").style.display="block";
				if(document.refundAccountForm.bank_name.value == "" || document.refundAccountForm.bank_owner.value == "" || document.refundAccountForm.bank_num.value == "") {
					alert("ȯ�Ұ��� ������ �Է��ϼ���.");
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
<div class="orderdetailwrap">
	<h1>
		�ֹ���ȸ
	</h1>
	<!-- �ֹ������� ���� ��� START -->
	<?
		if (strlen($ordercodeid)>0 && strlen($ordername)>0) {	//��ȸ�� �ֹ���ȸ
			$curdate = date("Ymd",mktime(0,0,0,date("m"),date("d")-90,date("Y")))."00000";
			$sql = "SELECT * FROM tblorderinfo WHERE ordercode > '".$curdate."' AND id LIKE 'X".$ordercodeid."%' ";
			$sql.= "AND sender_name='".$ordername."' ";
			$result=mysql_query($sql,get_db_conn());
			
			if($row=mysql_fetch_object($result)) {
				$_ord=$row;
				$ordercode=$row->ordercode;
				$gift_price=$row->price-$row->deli_price;
			} else {
	?>
				<div class="orderdetail_nodata">
					<p class="nodata_msg_top">��ȸ�Ͻ� �ֹ������� �����ϴ�.</p>
					<p class="nodata_msg_bottom">��ȸ�� �ֹ����� ��� 90�� ����� ������ ���ǹٶ��ϴ�.</p>
					<button type="button" onClick="window.close();">�ݱ�</button>
				</div>
	<?
			}
			mysql_free_result($result);
		} else {
			$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				$_ord=$row;
				$gift_price=$row->price-$row->deli_price;
			} else {
	?>
				<div class="orderdetail_nodata">
					<p class="nodata_msg_top">��ȸ�Ͻ� �ֹ������� �����ϴ�.</p>
					<!-- <button class="nodata_close" type="button" onClick="window.close();">�ݱ�</button> -->
				</div>
	<?
			}
		mysql_free_result($result);
		}
	?>
	<!-- �ֹ������� ���� ��� END -->

	<!-- �ֹ����� �ȳ��޽��� START -->
	<?if (strlen($ordercodeid)>0 && strlen($ordername)>0) {?>
	<div class="orderdetail_infomsg">
		<FONT COLOR="#EE1A02"><B><?=$_ord->sender_name?></B></FONT>�Բ��� <FONT COLOR="#111682"><?=substr($_ord->ordercode,0,4)?>�� <?=substr($_ord->ordercode,4,2)?>�� <?=substr($_ord->ordercode,6,2)?>��</FONT> �ֹ��� �����Դϴ�.
	</div>
	<?}?>
	<!-- �ֹ� ���� �ȳ��޽��� END -->
	<?
		if (strlen($ordercodeid)>0 && $_ord->deli_gbn=="Y") {
			/* ���ڼ��ݰ�꼭 ���� ���� üũ */
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
				if($billcnt>0){//��û
					$HB->set_document_id($document_id);
					$documet_result_array = $HB->check_document( HB_SOAPSERVER_URL );
					echo "<A HREF=\"javascript:viewBill('".$b_idx."')\">".$document_status[$documet_result_array[0]["now_state"]]."</a>";
				}else{
					if($companyinfo>0){
						echo "<input type='button' value='���ݰ�꼭 ��û' onclick=\"sendBill('".$_ord->ordercode."')\" onmouseover=\"window.status='���ݰ�꼭 ��û';return true;\" onmouseout=\"window.status='';return true;\" style='cursor:pointer;'>";
					}else{
						echo "<A HREF=\"javascript:sendBillPop('".$_ord->ordercode."')\" onmouseover=\"window.status='���ݰ�꼭 ��û';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/mypage_detailview.gif\" border=\"0\"></A>";
					}
				}
				echo "</span>";
			}
		}
	?>

	<div class="orderdetail_ct">
	<!-- �ֹ����� START -->
		<div class="orderdetail_prwrap">
			<h2>�ֹ�����</h2>
			<div class="orderdetail_pr_list">
				<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orderdetail_pr_table">
					<tbody>
						<?if(strlen($ordercode)==21 && substr($ordercode,-1)=="X"){?>
						<tr>
							<th>�ֹ�Ȯ�ι�ȣ</th>
							<td><?=substr($_ord->id,1,6)?></td>
						</tr>
						<?}?>
						<tr>
							<th>�ֹ�����</th>
							<td><?=substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2)?></td>
						</tr>
						<tr>
							<th>�޴»��</th>
							<td><?=$_ord->receiver_name?></td>
						</tr>
						<tr>
							<th>����ּ�</th>
							<td><?=ereg_replace("�ּ� :","<br>�ּ� :",$_ord->receiver_addr)?></td>
						</tr>
						<tr>
							<th>�������</th>
							<td>
								<?
									if (preg_match("/^(B|O|Q){1}/",$_ord->paymethod)) {	//������, �������, ������� ����ũ��
										if($_ord->paymethod=="B") echo "<font color=#FF5D00>�������Ա�</font>\n";
										else if(substr($_ord->paymethod,0,1)=="O") echo "<font color=#FF5D00>�������</font>\n";
										else echo "�Ÿź�ȣ - �������";

										if(!preg_match("/^(C|D)$/",$_ord->deli_gbn) || $_ord->paymethod=="B") echo "�� ".$_ord->pay_data." ��";
										else echo "�� ���� ��� ��";

										if (strlen($_ord->bank_date)>=12) {
											echo "</td>\n</tr>\n";
											echo "<tr>\n";
											echo "	<th>�Ա�Ȯ��</th>\n";
											echo "	<td>".substr($_ord->bank_date,0,4)."/".substr($_ord->bank_date,4,2)."/".substr($_ord->bank_date,6,2)." (".substr($_ord->bank_date,8,2).":".substr($_ord->bank_date,10,2).")";
										} else if(strlen($_ord->bank_date)==9) {
											echo "</td>\n</tr>\n";
											echo "<tr>\n";
											echo "	<th>�Ա�Ȯ��</th>\n";
											echo "	<td>ȯ��";
										}
									} else if(substr($_ord->paymethod,0,1)=="M") {	//�ڵ��� ����
										echo "�ڵ��� ������ ";
										if ($_ord->pay_flag=="0000") {
											if($_ord->pay_admin_proc=="C") echo "�� <font color=red>������� �Ϸ�</font> ��";
											else echo "<font color=red>������ ���������� �̷�������ϴ�.</font>";
										}
										else echo "������ ���еǾ����ϴ�.";
										echo " ��";
									} else if(substr($_ord->paymethod,0,1)=="P") {	//�Ÿź�ȣ �ſ�ī��
										echo "�Ÿź�ȣ - �ſ�ī��";
										if($_ord->pay_flag=="0000") {
											if($_ord->pay_admin_proc=="C") echo "�� <font color=red>ī����� ��ҿϷ�</font> ��";
											else if($_ord->pay_admin_proc=="Y") echo "�� ī�� ���� �Ϸ� * �����մϴ�. : ���ι�ȣ ".$_ord->pay_auth_no." ��";
										}
										else echo "�� ".$_ord->pay_data." ��";
									} else if (substr($_ord->paymethod,0,1)=="C") {	//�Ϲݽſ�ī��
										echo "<font color=#FF5D00>�ſ�ī��</font>\n";
										if($_ord->pay_flag=="0000") {
											if($_ord->pay_admin_proc=="C") echo "�� <font color=red>ī����� ��ҿϷ�</font> ��";
											else if($_ord->pay_admin_proc=="Y") echo "�� ī�� ���� �Ϸ� * �����մϴ�. : ���ι�ȣ ".$_ord->pay_auth_no." ��";
										}
										else echo "�� ".$_ord->pay_data." ��";
									} else if (substr($_ord->paymethod,0,1)=="V") {
										echo "�ǽð� ������ü : ";
										if ($_ord->pay_flag=="0000") {
											if($_ord->pay_admin_proc=="C") echo "�� <font color=005000> [ȯ��]</font> ��";
											else echo "<font color=red>".$_ord->pay_data."</font>";
										}
										else echo "������ ���еǾ����ϴ�.";
									}
								?>
							</td>
						</tr>
						<tr>
							<th>�����ݾ�</th>
							<td><font color=red><b><?=number_format($_ord->price)."��</b></font>".($_ord->reserve>0?"(������ ".number_format($_ord->reserve)."�� ����)":"")?></td>
						</tr>
						<?
							$order_msg=explode("[MEMO]",$_ord->order_msg);
							if(strlen($order_msg[0])>0) {
						?>
						<tr>
							<th>���޸�</th>
							<td><?=nl2br($order_msg[0])?></td>
						</tr>
						<?}?>
						<?if(strlen($order_msg[2])>0) {?>
						<tr>
							<th>�����޸�</th>
							<td><?=nl2br($order_msg[2])?></td>
						</tr>
						<?}?>
						<?if( preg_match("/^(B){1}/", $_ord->paymethod) && strlen($_ord->bank_date)==14 && $_ord->deli_gbn=="N" && getDeligbn("N",true)){//������ �Ա� �Ϸ� , ��ó�� ���� �϶� ���
						?>
						<tr>
							<th>ȯ�Ұ���</th>
							<td style="padding:0px;">
								<form name="refundAccountForm">
									<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orderdetail_pr_refund">
										<tr>
											<th>�����</th>
											<td><input type="text" name="bank_name" maxlength="30" style="width:90%; border:1px solid #dddddd;" /></td>
										</tr>
										<tr>
											<th>������</th>
											<td><input type="text" name="bank_owner" maxlength="4" style="width:90%; border:1px solid #dddddd;" /></td>
										</tr>
										<tr>
											<th>���¹�ȣ</th>
											<td><input type="text" name="bank_num" style="width:90%; border:1px solid #dddddd;" /></td>
										</tr>
									</table>
								</form>
							</td>
						</tr>
							<?}?>
					</tbody>
				</table>
			</div>
		</div>
	<!-- �ֹ����� END -->

	<!-- �ֹ� ���� SATART -->
		<div class="orderdetail_prwrap">
			<h2>�ֹ���ǰ ����</h2>
			<div class="orderdetail_pr_list">
				<ul>
					<?
						$delicomlist=getDeliCompany();
						$orderproducts = getOrderProduct($row->ordercode);

						$cnt=0;
						$gift_check="N";
						$taxsaveprname="";
						$etcdata=array();
						$giftdata=array();
						$in_reserve=0;

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
					?>
						<li>
							<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orderdetail_pr_table">
								<tbody>
									<tr>
										<td colspan="2" class="orderdetail_pr_name">
											<!--<div style="float:left; width:60px; text-align:center;"><img src="<?=$Dir.DataDir?>shopimages/product/<?=urlencode($row->minimage)?>" border=0 width=40 height=40 /></div>-->
											<b>��ǰ�� : <?=$row->productname?></b><br />
											<?=$row->opt1_name?><?=$row->opt2_name?>
										</td>
									</tr>
									<tr>
										<th>��ǰ�ݾ�</th>
										<td><span class="orderdetail_pr_price"><?=number_format($row->sumprice)?>��</span></td>
									</tr>
									<tr>
										<th>����</th>
										<td><?=$row->quantity?>��</td>
									</tr>
									<tr>
										<th>�ֹ�����</th>
										<td>
											<?if(strlen($row->order_prmsg)>0) {?>
											<p><?=nl2br(strip_tags($row->order_prmsg))?></p>
											<?}?>
											<p><?=orderProductDeliStatusStr($row,$_ord)?></p>
										</td>
									</tr>
									<tr>
										<th class="lastTH">��ۻ���</th>
										<td class="lastTD">
											<?
												$deli_url="";
												$trans_num="";
												$company_name="";
												if($row->deli_gbn=="Y") {
													if($row->deli_com>0 && $delicomlist[$row->deli_com]) {
														$deli_url=$delicomlist[$row->deli_com]->deli_url;
														$trans_num=$delicomlist[$row->deli_com]->trans_num;
														$company_name=$delicomlist[$row->deli_com]->company_name;
														//echo $company_name."<br>";
														echo "<div style=\"float:left; height:20px; line-height:20px;\">".$company_name."</div>";
														if(strlen($row->deli_num)>0 && strlen($deli_url)>0) {
															if(strlen($trans_num)>0) {
																$arrtransnum=explode(",",$trans_num);
																$pattern=array("(\[1\])","(\[2\])","(\[3\])","(\[4\])");
																$replace=array(substr($row->deli_num,0,$arrtransnum[0]),substr($row->deli_num,$arrtransnum[0],$arrtransnum[1]),substr($row->deli_num,$arrtransnum[0]+$arrtransnum[1],$arrtransnum[2]),substr($row->deli_num,$arrtransnum[0]+$arrtransnum[1]+$arrtransnum[2],$arrtransnum[3]));
																$deli_url=preg_replace($pattern,$replace,$deli_url);
															} else {
																$deli_url.=$row->deli_num;
															}
															echo "<div style=\"float:right; margin-right:6px;\"><A HREF=\"javascript:DeliSearch('".$deli_url."')\" class=\"button white small\">�������</A></div>";
														}
													} else {
														echo "-";
													}
												} else {
													echo "-";
												}
											?>
										</td>
									</tr>
								</tbody>
							</table>
						</li>
					<?
						}
					?>
				</ul>
			</div>
		</div>
	<!-- �ֹ� ���� END -->
	
	<!-- ����ǰ ���� START -->
	<?if(count($giftdata)>0){?>
		<div class="orderdetail_prwrap">
			<h2>����ǰ ����</h2>
			<div class="orderdetail_pr_list">
				<ul>
					<?for($i=0;$i<count($giftdata);$i++) {?>
					<li>
						<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orderdetail_pr_table">
							<thead>
								<tr>
									<td colspan="2" class="orderdetail_pr_name"><b>����ǰ�� : <?=$giftdata[$i]->productname?></b></td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th>���û���1</th>
									<td><?=$giftdata[$i]->opt1_name?></td>
								</tr>
								<tr>
									<th>���û���2</th>
									<td><?=$giftdata[$i]->opt2_name?></td>
								</tr>
								<tr>
									<th>���û���3</th>
									<td><?=$giftdata[$i]->opt3_name?></td>
								</tr>
								<tr>
									<th>���û���4</th>
									<td><?=$giftdata[$i]->opt4_name?></td>
								</tr>
								<tr>
									<th>����</th>
									<td><?=$giftdata[$i]->quantity?></td>
								</tr>
								<tr>
									<th>��û����</th>
									<td><?=$giftdata[$i]->assemble_info?></td>
								</tr>
							</tbody>
						</table>
					<li>
					<?}?>
				<ul>
			</div>
		</div>
	<?}?>
	<!-- ����ǰ ���� END -->

	<!-- �߰� ���� START -->
	<? $etcdata = getOrderAddtional($row->ordercode); ?>
		<div class="orderdetail_prwrap">
			<h2>�߰����/����/��������</h2>
			<div class="orderdetail_pr_list">
				<ul>
					<?for($i=0;$i<count($etcdata);$i++) {?>
					<li>
						<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orderdetail_pr_table">
							<tbody>
						<?
							$in_reserve+=$etcdata[$i]->reserve;
							if(ereg("^(COU)([0-9]{8,10})(X)$",$etcdata[$i]->productcode)) {
						?>
								<tr>
									<td colspan="2" class="orderdetail_pr_name"><b>�׸� : �������</b></td>
								</tr>
								<tr>
									<th>����</th>
									<td><?=$etcdata[$i]->productname?></td>
								</tr>
								<tr>
									<th>�ݾ�</th>
									<td><?=($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."��":"&nbsp;")?></td>
								</tr>
								<?if($etcdata[$i]->reserve!=0){?>
								<tr>
									<th>������</th>
									<td><?=number_format($etcdata[$i]->reserve)?>��</td>
								</tr>
								<?}?>
								<tr>
									<th>�ش� ��ǰ��</th>
									<td><?=$etcdata[$i]->order_prmsg?></td>
								</tr>
						<?
							}else if(ereg("^(9999999999)([0-9]{1})(X)$",$etcdata[$i]->productcode)){
								if($etcdata[$i]->productcode=="99999999999X") {
						?>
								<tr>
									<td colspan="2" class="orderdetail_pr_name"><b>�׸� : ��������</b></td>
								</tr>
								<tr>
									<th>����</th>
									<td><?=$etcdata[$i]->productname?></td>
								</tr>
								<tr>
									<th>�ݾ�</th>
									<td><?=($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."��":"&nbsp;")?></td>
								</tr>
								<?if($etcdata[$i]->reserve!=0){?>
								<tr>
									<th>������</th>
									<td><?=number_format($etcdata[$i]->reserve)?>��</td>
								</tr>
								<?}?>
								<tr>
									<th>�ش� ��ǰ��</th>
									<td>�ֹ��� ��ü ����</td>
								</tr>
						<?
								} else if($etcdata[$i]->productcode=="99999999998X") {
						?>
								<tr>
									<td colspan="2" class="orderdetail_pr_name"><b>�׸� : ���� ������</b></td>
								</tr>
								<tr>
									<th>����</th>
									<td><?=$etcdata[$i]->productname?></td>
								</tr>
								<tr>
									<th>�ݾ�</th>
									<td><?=($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."��":"&nbsp;")?></td>
								</tr>
								<?if($etcdata[$i]->reserve!=0){?>
								<tr>
									<th>������</th>
									<td><?=number_format($etcdata[$i]->reserve)?>��</td>
								</tr>
								<?}?>
								<tr>
									<th>�ش� ��ǰ��</th>
									<td>�ֹ��� ��ü ����</td>
								</tr>
						<?
								} else if($etcdata[$i]->productcode=="99999999990X") {
						?>
								<tr>
									<td colspan="2" class="orderdetail_pr_name"><b>�׸� : ��۷�</b></td>
								</tr>
								<tr>
									<th>����</th>
									<td><?=$etcdata[$i]->productname?></td>
								</tr>
								<tr>
									<th>�ݾ�</th>
									<td><?=($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."��":"&nbsp;")?></td>
								</tr>
								<?if($etcdata[$i]->reserve!=0){?>
								<tr>
									<th>������</th>
									<td><?=number_format($etcdata[$i]->reserve)?>��</td>
								</tr>
								<?}?>
								<tr>
									<th>�ش� ��ǰ��</th>
									<td><?=$etcdata[$i]->order_prmsg?></td>
								</tr>
						<?
								} else if($etcdata[$i]->productcode=="99999999997X") {
						?>
								<tr>
									<td colspan="2" class="orderdetail_pr_name"><b>�׸� : �ΰ���(VAT)</b></td>
								</tr>
								<tr>
									<th>����</th>
									<td><?=$etcdata[$i]->productname?></td>
								</tr>
								<tr>
									<th>�ݾ�</th>
									<td><?=($etcdata[$i]->price!=0?number_format($etcdata[$i]->price)."��":"&nbsp;")?></td>
								</tr>
								<?if($etcdata[$i]->reserve!=0){?>
								<tr>
									<th>������</th>
									<td><?=number_format($etcdata[$i]->reserve)?>��</td>
								</tr>
								<?}?>
								<tr>
									<th>�ش� ��ǰ��</th>
									<td>�ֹ��� ��ü����</td>
								</tr>
						<?
								}
							}
						?>
							</tbody>
						</table>
					</li>
					<?
						} //end for
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
					?>
					<li>
						<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orderdetail_pr_table">
							<tbody>
								<tr>
									<td colspan="2" class="orderdetail_pr_name"><b>�׸� : �׷����� / ����</b></td>
								</tr>
								<tr>
									<th>����</th>
									<td>�׷�ȸ�� ����/����<?=$group_name?></td>
								</tr>
								<tr>
									<th>�ݾ�</th>
									<td><?=($salemoney>0?"-".number_format($salemoney)."��":"&nbsp;")?></td>
								</tr>
								<?if($salereserve>0){?>
								<tr>
									<th>������</th>
									<td><?=($salereserve>0?"+ ".number_format($salereserve)."��":"&nbsp;")?></td>
								</tr>
								<?}?>
								<tr>
									<th>�ش� ��ǰ��</th>
									<td>�ֹ��� ��ü����</td>
								</tr>
							</tbody>
						</table>
					</li>

					<?
						}
						if($_ord->reserve>0) {
					?>
					<li>
						<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orderdetail_pr_table">
							<tbody>
								<tr>
									<td colspan="2" class="orderdetail_pr_name"><b>�׸� : ������ ���</b></td>
								</tr>
								<tr>
									<th>����</th>
									<td>������ ������ <?=number_format($_ord->reserve)?>�� ���</td>
								</tr>
								<tr>
									<th>�ݾ�</th>
									<td>-<?=number_format($_ord->reserve)?>��</td>
								</tr>
								<tr>
									<th>�ش� ��ǰ��</th>
									<td>�ֹ��� ��ü����</td>
								</tr>
								<?
									$sql = "SELECT * FROM part_cancel_reserve WHERE ordercode='".$ordercode."' order by reg_date asc";
									$result=mysql_query($sql,get_db_conn());

									while( $row=mysql_fetch_object($result)) {
								?>
									<tr>
										<th>�׸�</th>
										<td>������ ȯ��</td>
									</tr>
									<tr>
										<th>����</th>
										<td>������ <?=number_format($row->cancel_reserve)?>�� ȯ��</td>
									</tr>
									<tr>
										<th>������</th>
										<td><?=number_format($row->cancel_reserve)?>��</td>
									</tr>
								<? } ?>
							</tbody>
						</table>
					</li>
					<? } ?>
				</ul>
			</div>
		</div>
	<!-- �߰����� END -->

	<!-- �κ� ��� ���� -->
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
		<div class="orderdetail_prwrap">
			<h2>�κ� ��� ����</h2>
			<div class="orderdetail_pr_list">
				<table cellpadding="0" cellspacing="0" border="0" width="100%" class="orderdetail_pr_table">
					<tbody>
						<?
							$sumcancle = 0;
							for($i=0;$i<count($pcancleitems);$i++) {
								$citem = $pcancleitems[$i];
								$sumcancle +=abs($citem['price']);
						?>
						<tr>
							<td colspan="2" class="orderdetail_pr_name"><b>�׸� : <?=$citem['productname'].' #'.$citem['opt1_name']?></b></td>
						</tr>
						<tr>
							<th>�ݾ�</th>
							<td><?=number_format(abs($citem['price']))?>��</td>
						</tr>
						<tr>
							<th>����</th>
							<td><? echo substr($citem['date'],0,4).'-'.substr($citem['date'],5,2).'-'.substr($citem['date'],7,2); ?></td>
						</tr>
						<?}?>
					</tbody>
				</table>
				��� �ݾ� �հ� : <span><?=number_format($sumcancle)?></span>
			</div>
		</div>
	<?}?>
	</div>
	<!-- �κ� ��� ���� END -->

	<div style="text-align:center; margin:15px 0px;">
		<A HREF="javascript:window.close();" class="button black small">X â�ݱ�</A>
		<?
		if($print!="OK") {
			if (
			   ($_data->ordercancel==0 && ($_ord->deli_gbn=="S" || $_ord->deli_gbn=="N") && getDeligbn("S|N",true))/*�ֹ���ۿϷ� ���� ��Ұ� �����ϸ� �߼��غ�� �ֹ��� �Ǵ� ��ó���� �ֹ����� ��� ����*/
			|| ($_data->ordercancel==2 && $_ord->deli_gbn=="N" && getDeligbn("N",true))/*�ֹ�����غ� ������ ��Ұ� �����ϸ� ��ó���� �ֹ����� ��� ����*/
			|| ($_data->ordercancel==1 && preg_match("/^(B){1}/", $_ord->paymethod) && strlen($_ord->bank_date)<12 && $_ord->deli_gbn=="N" && getDeligbn("N",true)) /*�ֹ������Ϸ� ������ ��Ұ� �����ϸ� �������Ա����� �Ա��� ��ó���� �ֹ����� ��� ����*/
			) {
				if(!preg_match("/^(Q){1}/", $_ord->paymethod)) {
					echo "<a href=\"javascript:order_cancel('".$_ord->tempkey."', '".$_ord->ordercode."','".$_ord->bank_date."')\" onMouseOver=\"window.status='�ֹ����';return true;\" class=\"button white small\">��ü�ֹ����</a>\n";
				}
			} else if($_data->ordercancel==1 && (($_ord->paymethod=="B" && strlen($_ord->bank_date)>=12) || ( preg_match("/^(C|P){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) && $_ord->deli_gbn=="N" && getDeligbn("N",true)){
				if(strlen($_data->nocancel_msg)==0) $_data->nocancel_msg="�ֹ���Ұ� ���� �ʽ��ϴ�.\\n���θ��� �����ϼ���.";
				echo "<a href=\"javascript:alert('".$_data->nocancel_msg."')\"><img src=\"".$Dir."images/common/orderdetailpop_ordercancel.gif\" align=absmiddle border=0></a>\n";
			}

			if($_ord->del_gbn!="A" && $_ord->del_gbn!="Y" && getDeligbn("A|Y",false)
			&& !(substr($_ord->paymethod,0,1)=="Q" && strlen($_ord->bank_date)>=12 && $_ord->deli_gbn!="C")  //�Ÿź�ȣ ��������̰� �Ա�Ȯ�εǰ� �ֹ���Ұ� �ƴѰ��
			&& !(substr($_ord->paymethod,0,1)=="P" && $_ord->pay_flag=="0000" && $_ord->deli_gbn!="C")      //�Ÿź�ȣ �ſ�ī���̰� ī�强�� �ֹ���Ұ� �ƴѰ��
			&& strlen($_ShopInfo->getMemid())>0 /* ��ȸ���� ��������ȵǰ� */) {
				echo "<a href=\"javascript:order_del('".$_ord->tempkey."', '".$_ord->ordercode."')\" onMouseOver=\"window.status='�������';return true;\" class=\"button white small\">�������</a>\n";
			}

			/*
			if(preg_match("/^(B|O|Q){1}/", $_ord->paymethod) && $_ord->deli_gbn!="C") {
				if($_data->tax_type!="N" && $_ord->price>=1) {
					echo "<a href=\"javascript:get_taxsave('".$_ord->ordercode."')\" onMouseOver=\"window.status='���ݿ�����';return true;\"><img src=\"".$Dir."images/common/orderdetailpop_cashbill.gif\" align=absmiddle border=0></a>\n";
				}
			}
			*/


			// ���� ���� ������ ���� ���� ��ɿ� �߰� �Ͽ� ���� ��꼭 ���� ó��
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
						echo "<a href=\"javascript:get_taxsave('".$_ord->ordercode."')\" onMouseOver=\"window.status='���ݿ�����';return true;\" class=\"button white small\">���ݿ�������û</A>\n";
					}
					if($reqItem != 'taxsave'  && $_ord->deli_gbn == 'Y'){
						if(_isInt($bill_idx)){
							echo "<A HREF=\"javascript:viewBill('".$bill_idx."')\" onmouseover=\"window.status='���ݰ�꼭 ��û';return true;\" onmouseout=\"window.status='';return true;\" class=\"button white small\">���ݰ�꼭��û</A>";

						}else{
							echo "<A HREF=\"javascript:sendBillPop('".$_ord->ordercode."')\" onmouseover=\"window.status='���ݰ�꼭 ��û';return true;\" onmouseout=\"window.status='';return true;\" class=\"button white small\">���ݰ�꼭��û</A>";
						}
					}
				}
			}


			if(((substr($_ord->paymethod,0,1)=="P" && $_ord->pay_admin_proc=="Y") || (substr($_ord->paymethod,0,1)=="Q" && $_ord->pay_flag=="0000")) && !preg_match("/^(Y|C)$/",$_ord->escrow_result) && $_ord->deli_gbn!="C") {
				/*
				����ũ�� ������ ������ �´�.
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

				// ���ó���� �Ǿ�߸� �Ÿź�ȣ
				if ($_ord->deli_gbn=="Y") {
					echo "<a href=\"javascript:escrow_ok('".$_ord->ordercode."')\" onMouseOver=\"window.status='����Ȯ��';return true;\"><img src=\"".$Dir."images/common/orderdetailpop_okorder.gif\" align=absmiddle border=0></a>\n";
				} else if (substr($_ord->paymethod,0,1)=="Q" && !preg_match("/^(D|E|H)$/", $_ord->deli_gbn) && getDeligbn("D|E|H",false)) {
					#<!--- ��� ( ��� & ȯ�� �Ѳ����� ó��) -->
					echo "<a href=\"javascript:escrow_cancel('".$_ord->tempkey."','".$_ord->ordercode."','".$_ord->bank_date."')\" onMouseOver=\"window.status='�������';return true;\"><img src=\"".$Dir."images/common/orderdetailpop_ordercancel.gif\" align=absmiddle border=0></a>\n";
				}
			}

			// ######### ����ǰ�� �������� ���� �ֹ��� ��� ����ǰ�� ������ �� �ֵ��� ����
			if (($_ord->paymethod=="B" || (preg_match("/^(V|O|Q|C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) && $_ord->deli_gbn=="N" && getDeligbn("N",true) && $gift_check=="N" && $gift_type[3]=="Y") {
				if ($gift_type[2]=="A" || strlen($gift_type[2])==0 || ($gift_type[2]=="B" && $_ord->paymethod=="B")) {
					if (($gift_type[0]=="M" && strlen($_ShopInfo->getMemid())>0) || $gift_type[0]=="C") { // ȸ������, ��ȸ��+ȸ��
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
	</div>

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
<form name=taxsaveform method=post action="<?=$Dir?>m/taxsave.php" target=taxsavepop>
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
	<input type=hidden name=return_script value="<?=urlencode(str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl())."m/orderdetailpop.php")?>">
	<input type=hidden name=return_data value="<?=urlencode("type=okescrow&ordercode=".$ordercode)?>">
</form>

<form name=vform action="<?=$Dir?>paygate/set_bank_account.php" method=post target="baccountpop">
	<input type=hidden name=ordercode value="<?=$ordercode?>">
</form>

<form name=form3 method=post>
	<input type=hidden name=ordercode value="<?=$ordercode?>">
</form>

<form name=billform method=post action="<?=$Dir.FrontDir?>orderbillpop.php" target="billpop">
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

</body>
</html>