<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

header("Content-Type: text/plain");
header("Content-Type: text/html; charset=euc-kr");

$groupCode=$_REQUEST["groupCode"];
$couponCode=$_REQUEST["couponCode"];
$chk=$_REQUEST["chk"];

// 그룹자동발급 쿠폰 등록 수정
if( strlen($groupCode) > 0 AND strlen($couponCode) > 0 ){

	if( $chk == "true" ){
		$SQL = "INSERT `group_coupon` SET `group_code`='".$groupCode."' , `coupon_code` = '".$couponCode."' ; ";
		echo "[".$couponCode."] <-> [".$groupCode."] CONNECT";
	} else{
		$SQL = "DELETE FROM `group_coupon` WHERE `group_code`='".$groupCode."' AND `coupon_code` = '".$couponCode."' LIMIT 1; ";
		echo "[".$couponCode."] -X- [".$groupCode."] UNPLUG";
	}
	if( mysql_query($SQL,get_db_conn()) ) {
		echo " SUCCESS!";
	} else{
		echo " FAIL! [".$SQL."]";
	}

}
?>