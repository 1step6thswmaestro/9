<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$ordercode=$_POST["ordercode"];
include "header.php";

if(substr($ordercode,0,8)<=date("Ymd",strtotime("-3","day"))) {
	echo "<html><head></head><body onload=\"alert('잘못된 경로로 접근하셨습니다.'); location.href='./'\"></body></html>";
	exit;
}

$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$_ord=$row;
	$gift_price=$_ord->price-$row->deli_price;

	$receiver_addr = explode('주소 : ',$_ord->receiver_addr);
	$zipCode  = explode('우편번호 : ',$receiver_addr[0]);

	$sql = "select mobile from tblmember where id='".$_ord->id."'";
	$resultm=mysql_query($sql,get_db_conn());
	if($rowm=mysql_fetch_object($resultm)) {
		if (strlen($rowm->mobile)>0) $mobile = $rowm->mobile;
		$mobile=explode("-",replace_tel(check_num($mobile)));
	}

} else {
	echo "<html></head><body onload=\"alert('잘못된 경로로 접근하셨습니다.'); location.href='./'\"></body></html>";
	exit;
}
mysql_free_result($result);

if (preg_match("/^(V|O|Q|C|P|M)$/", $_ord->paymethod) && $_ord->deli_gbn=="C") {
	$_ord->pay_data = "결제 중 주문취소";
}

$gift_type=explode("|",$_data->gift_type);
$gift_cnt=0;
if (($_ord->paymethod=="B" || (preg_match("/^(V|O|Q|C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) && $_ord->deli_gbn=="N" && strlen($_ShopInfo->getGifttempkey())>0) {
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
		}
	}
}
$gift_cnt=0;

?>
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<? include "./orderend_skin.php"; ?>
<?
include "footer.php";
?>