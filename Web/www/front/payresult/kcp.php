<?
/*
KCP 공통 통보 처리 (RESULT=OK, RESULT=NO)
*/
if(getenv("SERVER_ADDR")!=getenv("REMOTE_ADDR")) exit;

$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

$ip=getenv("REMOTE_ADDR");

$ordercode=$_POST["ordercode"];
$tx_cd=$_POST["tx_cd"];
$price=$_POST["price"];
$ok=$_POST["ok"];

$paymethod="";
$istaxsave=false;
$date=date("YmdHis");
if($tx_cd=="TX00" && strlen($ordercode)>0 && strlen($price)>0 && strlen($ok)>0) {	//가상계좌 입금통보
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
			@mail(AdminMail,"KCP 가상계좌 입금처리 안됨","$sql<br>".mysql_error());
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
} else if($tx_cd=="TX01" && strlen($ordercode)>0) {	//가상계좌 환불통보
	######### paymethod=>"Q", pay_flag=>"0000", pay_admin_proc=>"N", deli_gbn=>"E"  ######
	$sql = "SELECT paymethod, pay_flag, pay_admin_proc, deli_gbn, bank_date FROM tblorderinfo ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
		$pay_flag=$row->pay_flag;
		$pay_admin_proc=$row->pay_admin_proc;
		$deli_gbn=$row->deli_gbn;
		$bank_date=$row->bank_date;
	} else {
		echo "RESULT=NO"; exit;
	}
	mysql_free_result($result);

	if(preg_match("/^(Q){1}/", $paymethod)) {
		$sql = "UPDATE tblorderinfo ";
		$sql.= "SET pay_admin_proc='C', deli_gbn='C', bank_date='".substr($bank_date,0,8)."X' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		mysql_query($sql,get_db_conn());
		if(!mysql_error()) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
			$sql.= "WHERE ordercode='".$ordercode."' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			mysql_query($sql,get_db_conn());

			echo "RESULT=OK";
		} else {
			if(strlen(AdminMail)>0) {
				@mail(AdminMail,"KCP 가상계좌 환불완료처리 안됨","$sql<br>".mysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO";
	}
} else if($tx_cd=="TX02" && strlen($ordercode)>0 && strlen($ok)>0) {//구매확인/취소
	#### paymethod=>"Q|P", pay_flag=>"0000", pay_admin_proc=>"!C", deli_gbn=>"Y", escrow_result=>"N" #####
	$sql = "SELECT paymethod,pay_flag,pay_admin_proc,deli_gbn,deli_date,escrow_result FROM tblorderinfo ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
		$pay_flag=$row->pay_flag;
		$pay_admin_proc=$row->pay_admin_proc;
		$deli_gbn=$row->deli_gbn;
		$deli_date=$row->deli_date;
		$escrow_result=$row->escrow_result;
	} else {
		echo "RESULT=NO"; exit;
	}
	mysql_free_result($result);
	if($ok=="Y" && preg_match("/^(Q|P){1}/", $paymethod)) {			//구매확인
		$sql = "UPDATE tblorderinfo SET escrow_result='Y' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		mysql_query($sql,get_db_conn());
		if(!mysql_error()) {
			echo "RESULT=OK";
		} else {
			if(strlen(AdminMail)>0) {
				@mail(AdminMail,"KCP 에스크로 구매확인처리 안됨","$sql<br>".mysql_error());
			}
			echo "RESULT=NO";
		}
	} else if($ok=="C" && preg_match("/^(Q|P){1}/", $paymethod)) {	//구매취소
		$sql = "UPDATE tblorderinfo SET ";
		$sql.= "deli_gbn		= 'H' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		mysql_query($sql,get_db_conn());
		if(!mysql_error()) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='H' ";
			$sql.= "WHERE ordercode='".$ordercode."' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			mysql_query($sql,get_db_conn());

			echo "RESULT=OK";
		} else {
			if(strlen(AdminMail)>0) {
				@mail(AdminMail,"KCP 에스크로 구매취소처리 안됨","$sql<br>".mysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO"; exit;
	}
} else if($tx_cd=="TX03" && strlen($ordercode)>0) {//배송시작 통보
	######### paymethod=>"Q|P", pay_flag=>"0000", pay_admin_proc=>"N|Y", deli_gbn=>"N|S|X"  ######
	$deli_num=$_POST["deli_num"];
	$deli_name=$_POST["deli_name"];
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
	if(preg_match("/^(Q|P){1}/", $paymethod)) {
		$sql = "UPDATE tblorderinfo SET deli_gbn='Y', deli_date='".$date."' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		mysql_query($sql,get_db_conn());
		if(!mysql_error()) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='Y' ";
			$sql.= "WHERE ordercode='".$ordercode."' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			mysql_query($sql,get_db_conn());

			echo "RESULT=OK";
		} else {
			if(strlen(AdminMail)>0) {
				@mail(AdminMail,"KCP 에스크로 배송시작처리 안됨","$sql<br>".mysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO"; exit;
	}
} else if($tx_cd=="TX04" && strlen($ordercode)>0) {//정산보류 통보
	### paymethod=>"Q|P", pay_flag=>"0000", pay_admin_proc=>"N|Y", (deli_gbn=>"Y" OR (deli_gbn=>"D" AND strlen(deli_date)==14)), escrow_result=>"N"  ###
	$sql = "SELECT paymethod,pay_flag,pay_admin_proc,deli_gbn,deli_date,escrow_result FROM tblorderinfo ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
		$pay_flag=$row->pay_flag;
		$pay_admin_proc=$row->pay_admin_proc;
		$deli_gbn=$row->deli_gbn;
		$deli_date=$row->deli_date;
		$escrow_result=$row->escrow_result;
	} else {
		echo "RESULT=NO"; exit;
	}
	mysql_free_result($result);
	if(preg_match("/^(Q|P){1}/", $paymethod)) {
		$sql = "UPDATE tblorderinfo SET deli_gbn='H' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		mysql_query($sql,get_db_conn());
		if(!mysql_error()) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='H' ";
			$sql.= "WHERE ordercode='".$ordercode."' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			mysql_query($sql,get_db_conn());

			echo "RESULT=OK";
		} else {
			if(strlen(AdminMail)>0) {
				@mail(AdminMail,"KCP 에스크로 정산보류처리 안됨","$sql<br>".mysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO"; exit;
	}
} else if($tx_cd=="TX05" && strlen($ordercode)>0) {//즉시취소 통보
	######### paymethod=>"Q|P", pay_flag=>"0000", pay_admin_proc=>"N|Y", (deli_gbn=>"N|S|X" OR (deli_gbn=>"D" AND strlen(deli_date)==0))  ######
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
	if(preg_match("/^(Q|P){1}/", $paymethod)) {
		$sql = "UPDATE tblorderinfo SET ";
		if(preg_match("/^(P){1}/", $paymethod)) {	//신용카드는 정상 즉시취소처리
			$sql.= "pay_admin_proc	= 'C', ";
			$sql.= "deli_gbn		= 'C', ";
			$temp_deli_gbn="C";
		} else {	//가상계좌는 환불대기상태로
			$sql.= "deli_gbn		= 'E' ";
			$temp_deli_gbn="E";
		}
		$sql.= "WHERE ordercode='".$ordercode."' ";
		mysql_query($sql,get_db_conn());
		if(!mysql_error()) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='".$temp_deli_gbn."' ";
			$sql.= "WHERE ordercode='".$ordercode."' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			mysql_query($sql,get_db_conn());

			echo "RESULT=OK";
		} else {
			if(strlen(AdminMail)>0) {
				@mail(AdminMail,"KCP 에스크로 즉시취소처리 안됨","$sql<br>".mysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO"; exit;
	}
} else if($tx_cd=="TX06" && strlen($ordercode)>0) {//취소 통보 (정산보류 처리된 건)
	######### paymethod=>"Q|P", pay_flag=>"0000", pay_admin_proc=>"N|Y", deli_gbn=>"H"  ######
	$sql = "SELECT paymethod, pay_flag, pay_admin_proc, deli_gbn FROM tblorderinfo ";
	$sql.= "WHERE ordercode='".$ordercode."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$paymethod=$row->paymethod;
		$pay_flag=$row->pay_flag;
		$pay_admin_proc=$row->pay_admin_proc;
		$deli_gbn=$row->deli_gbn;
	} else {
		echo "RESULT=NO"; exit;
	}
	mysql_free_result($result);
	if(preg_match("/^(Q|P){1}/", $paymethod)) {
		$sql = "UPDATE tblorderinfo SET ";
		if(preg_match("/^(P){1}/", $paymethod)) {	//신용카드는 정상 취소처리
			$sql.= "pay_admin_proc	= 'C', ";
			$sql.= "escrow_result	= 'C', ";
			$sql.= "deli_gbn		= 'C', ";
			$temp_deli_gbn="C";
		} else {	//가상계좌는 환불대기상태로
			$sql.= "escrow_result	= 'C', ";
			$sql.= "deli_gbn		= 'E' ";
			$temp_deli_gbn="E";
		}
		$sql.= "WHERE ordercode='".$ordercode."' ";
		mysql_query($sql,get_db_conn());
		if(!mysql_error()) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='".$temp_deli_gbn."' ";
			$sql.= "WHERE ordercode='".$ordercode."' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			mysql_query($sql,get_db_conn());

			echo "RESULT=OK";
		} else {
			if(strlen(AdminMail)>0) {
				@mail(AdminMail,"KCP 에스크로 취소처리 안됨","$sql<br>".mysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO"; exit;
	}
} else if($tx_cd=="TX07" && strlen($ordercode)>0) {//발급계좌 해지 통보
	######### paymethod=>"Q", pay_flag=>"0000", pay_admin_proc=>"N", (deli_gbn=>"N" OR (deli_gbn=>"D" AND strlen(deli_date)==0))  ######
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
	if(preg_match("/^(Q|P){1}/", $paymethod)) {
		$sql = "UPDATE tblorderinfo SET deli_gbn='C' ";
		$sql.= "WHERE ordercode='".$ordercode."' ";
		mysql_query($sql,get_db_conn());
		if(!mysql_error()) {
			$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
			$sql.= "WHERE ordercode='".$ordercode."' ";
			$sql.= "AND NOT (productcode LIKE '999%' OR productcode LIKE 'COU%') ";
			mysql_query($sql,get_db_conn());

			echo "RESULT=OK";
		} else {
			if(strlen(AdminMail)>0) {
				@mail(AdminMail,"KCP 에스크로 발급계좌해지처리 안됨","$sql<br>".mysql_error());
			}
			echo "RESULT=NO";
		}
	} else {
		echo "RESULT=NO"; exit;
	}
} else {
	echo "RESULT=NO";
}
?>