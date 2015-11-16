<?
/*
allthegate 공통 통보 처리 (RESULT=OK, RESULT=NO)
*/
if(getenv("SERVER_ADDR")!=getenv("REMOTE_ADDR")) exit;

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

$ip=getenv("REMOTE_ADDR");

$ordercode=$_POST["ordercode"];
$trcode=$_POST["trcode"];
$price=$_POST["price"];
$ok=$_POST["ok"];

$paymethod="";
$istaxsave=false;
$date=date("YmdHis");
if(($trcode=="1" || $trcode=="2" || $trcode=="3" || $trcode=="4") && strlen($ordercode)>0 && strlen($price)>0 && strlen($ok)>0) {	//가상계좌 입금통보
	######### paymethod=>"O|Q", pay_flag=>"0000", pay_admin_proc=>"N", (deli_gbn=>"N" OR (deli_gbn=>"D" AND strlen(deli_date)==0))  ######
	$sql = "SELECT paymethod, pay_flag, pay_admin_proc, deli_gbn, deli_date FROM tblorderinfo ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
		$pay_flag=$row->pay_flag;
		$pay_admin_proc=$row->pay_admin_proc;
		$deli_gbn=$row->deli_gbn;
		$deli_date=$row->deli_date;
	} else {
		echo "RESULT=NO"; exit;
	}
	mysql_free_result($result);

	if($ok=="Y" && preg_match("/^(O|Q){1}/", $paymethod)) {
		$sql = "UPDATE tblorderinfo SET bank_date='".$date."', deli_gbn='N' ";
		$sql.= "WHERE ordercode='".$ordercode."' AND price='".$price."' ";
		if(mysql_query($sql,get_db_conn())) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='N' ";
			$sql.= "WHERE ordercode='".$ordercode."' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			mysql_query($sql,get_db_conn());
		}
	} else if($ok=="C" && preg_match("/^(O|Q){1}/", $paymethod)) {
		$sql = "UPDATE tblorderinfo SET bank_date=NULL ";
		$sql.= "WHERE ordercode='".$ordercode."' AND price='".$price."' ";
		mysql_query($sql,get_db_conn());
	} else {
		echo "RESULT=NO"; exit;
	}

	if(!mysql_error()) {
		echo "RESULT=OK";
		$istaxsave=true;
	} else {
		echo "RESULT=NO";
		if(strlen(AdminMail)>0) {
			@mail(AdminMail,"AllTheGate 가상계좌 입금처리 안됨","$sql<br>".mysql_error());
		}
	}

	if($istaxsave) {
		$sql = "SELECT tax_type FROM tblshopinfo ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$tax_type=$row->tax_type;
		mysql_free_result($result);

		if($tax_type=="Y") {
			if(preg_match("/^(O|Q){1}/", $paymethod)) {
				$sql = "SELECT bank_date, pay_admin_proc FROM tblorderinfo ";
				$sql.= "WHERE ordercode='".$ordercode."' ";
				$result=mysql_query($sql,get_db_conn());
				$row=mysql_fetch_object($result);
				mysql_free_result($result);
				if(strlen($row->bank_date)>=12 && $row->pay_admin_proc=="Y") {
					$sql = "SELECT COUNT(*) as cnt FROM tbltaxsavelist WHERE ordercode='".$ordercode."' AND type='N' ";
					$result=mysql_query($sql,get_db_conn());
					$row=mysql_fetch_object($result);
					mysql_free_result($result);
					if($row->cnt>0) {
						$flag="Y";
						include($Dir."lib/taxsave.inc.php");
					}
				} else {
					//현금영수증 취소처리를 해야될까?????
				}
			}
		}
	}
}
?>