<?php
header("Content-type:text/html; charset=euc-kr");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$feedEmail = trim($_POST["feedEmail"]);
$feedSms = trim($_POST["feedSms"]);

if($feedEmail != "" || $feedSms != "" ){
	if($feedEmail != "" && $feedSms != ""){
		$sCondition = " email='".$feedEmail."' AND mobile='".$feedSms."' ";
	}else if($feedEmail != ""){
		$sCondition = " email='".$feedEmail."' ";
	}else if($feedSms != ""){
		$sCondition = " mobile='".$feedSms."' ";
	}
	$sql = "SELECT count(1) cnt, idx FROM tblsocial_mailing WHERE ".$sCondition;
	//echo $sql."<br><br>";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		$cnt = $row->cnt;
		$idx = $row->idx;
	}
	if($cnt >0){
		$sql = "UPDATE tblsocial_mailing SET ";
		$sql.= ($feedEmail != "")? "email	= '".$feedEmail."', ":"";
		$sql.= ($feedSms != "")? "mobile	= '".$feedSms."', ":"";
		$sql.= "regidate	= '".time()."', ";
		$sql.= "state	= 'Y' ";
		$sql.= "WHERE idx= '".$idx."' ";
	}else{
		$sql = "INSERT tblsocial_mailing SET ";
		$sql.= "email	= '".$feedEmail."', ";
		$sql.= "mobile	= '".$feedSms."', ";
		$sql.= "regidate	= '".time()."', ";
		$sql.= "state	= 'Y' ";
	}
	//echo $sql;
	if(mysql_query($sql,get_db_conn())) {
		echo "<html><head></head><body onload=\"alert('소셜상품 구독신청되었습니다.');\"></body></html>";exit;
	}
}
?>