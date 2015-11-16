<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

include_once("./inc/function.php");
if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir."m/login.php?chUrl=".getUrl());
	exit;
}

include "header.php";

if($_data->coupon_ok!="Y") {
	echo "<html><head><title></title></head><body onload=\"alert('본 쇼핑몰에서는 쿠폰 기능을 지원하지 않습니다.');location.href='".$Dir."m/mypage.php'\"></body></html>";exit;
}


$currentPage = $_REQUEST["page"];
if(!$currentPage) $currentPage = 1; 

$recordPerPage = 3; // 페이지당 게시글 리스트 수 
$pagePerBlock = 2; // 블록 갯수

$pagetype="board";
$cdate = date("YmdH");

if($_data->coupon_ok=="Y") {
	$sql = "SELECT COUNT(*) as cnt FROM tblcouponissue WHERE id='".$_ShopInfo->getMemid()."' AND used='N' AND (date_end>='".$cdate."' OR date_end='') ";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$coupon_cnt = $row->cnt;
	mysql_free_result($result);
} else {
	$coupon_cnt=0;
}

$totalRecord = ($row->cnt > 0)? $row->cnt:0;
?>


<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function addOffCoupon(){
	window.open('/front/offlinecoupon_auth.php','OffLineCoupon','width=300,height=200');
}
//-->
</SCRIPT>
<? include "./mypage_coupon_skin.php"; ?>
<?// include "skin/$skin_name/mypage_coupon.php"; ?>

<? include "footer.php";?>