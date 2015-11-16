<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cfg.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-1";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

########################### TEST 쇼핑몰 확인 ##########################
DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", "main.php");
#######################################################################

$sql = "SELECT * FROM tblshopbillinfo where bill_state ='Y' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$domain = $row->domain;
	$license_id = $row->license_id;
	$license_no = $row->license_no;
	$bill_state = $row->bill_state;
}
mysql_free_result($result);
if(strlen($domain)==0 || strlen($license_id)==0 || strlen($license_no)==0) {
	echo "<html></head><body onload=\"alert('전자세금계산서 설정에서 사용자 ID 및 연동키를 입력하시기 바랍니다.');location.href='shop_billinfo.php';\"></body></html>";exit;
}

$SBinfo = new Shop_Billinfo();
$document_id=$_POST["document_id"];
if($document_id){
	$link = "location.href='http://group.hiworks.co.kr/".$SBinfo->domain."/bill/manage/viewdoc/".$document_id."'";
}else{
	$link = "location.href='http://group.hiworks.co.kr/".$SBinfo->domain."/bill/manage/";
}
?>
<script>
<?=$link?>
</script>
<!-- http://office.hiworks.co.kr/getmall/bill/customer/customer_search -->