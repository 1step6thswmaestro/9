<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once($Dir."lib/ext/product_func.php");
	include_once($Dir."lib/ext/member_func.php");
	include_once($Dir."lib/ext/order_func.php");


if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:login.php?chUrl=".getUrl());
	exit;
}
include "header.php";

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('ȸ�� ���̵� �������� �ʽ��ϴ�.');location.href='/.login.php';\"></body></html>";exit;
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		//echo "<html><head><title></title></head><body onload=\"alert('�ٸ���⿡�� �ߺ� �α��εǾ����ϴ�.�α׾ƿ� �� �� �α��� �Ͻñ� �ٶ��ϴ�.');location.href='./'\"></body></html>";exit;
		echo '<script>alert("�ٸ���⿡�� �ߺ� �α��� �Ǿ����ϴ�\n�α׾ƿ� �� ��α����Ͻñ� �ٶ��ϴ�.");location.href="./"</script>';exit;
	}
}
mysql_free_result($result);

$selfcodefont_start = "<font class=\"prselfcode\">"; //�����ڵ� ��Ʈ ����
$selfcodefont_end = "</font>"; //�����ڵ� ��Ʈ ��

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

?>


<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>

<SCRIPT LANGUAGE="JavaScript">
<!--
function OrderDetailPop(ordercode) {
	document.form2.ordercode.value=ordercode;
	window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
	document.form2.submit();
}
function DeliSearch(deli_url){
	window.open(deli_url,"�������","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizeble=yes,copyhistory=no,width=600,height=550");
}
function DeliveryPop(ordercode) {
	document.form3.ordercode.value=ordercode;
	window.open("about:blank","delipop","width=600,height=370,scrollbars=no");
	document.form3.submit();
}
function ViewPersonal(idx) {
	window.open("about:blank","mypersonalview","width=600,height=450,scrollbars=yes");
	document.form4.idx.value=idx;
	document.form4.submit();
}
//-->
</SCRIPT>


<?
include ($skinPATH."mypage.php");
?>

<form name=form2 method=post action="<?=$Dir?>m/orderdetailpop.php" target="orderpop">
<input type=hidden name=ordercode>
</form>
<form name=form3 method=post action="<?=$Dir.FrontDir?>deliverypop.php" target="delipop">
<input type=hidden name=ordercode>
</form>
<form name=form4 action="<?=$Dir?>m/mypage_personalview.php" method=post target="mypersonalview">
<input type=hidden name=idx>
</form>





<? include ("footer.php") ?>
