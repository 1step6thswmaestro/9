<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");
include_once($Dir."lib/ext/coupon_func.php");

if(_empty($_REQUEST['productcode'])){	
?>

<script language="javascript" type="text/javascript">
alert('상품 고유 번호가 올바르지 않습니다.');
window.close();
</script>
<? 
exit;
}else if(!_empty($_ShopInfo->getMemid())){ 
	$mycoupons = getMyCouponList($_REQUEST['productcode']);
}

$downcoupons = ableCouponOnProduct($_REQUEST['productcode'],0,true);
/*
$chkcode = array();
if(_array($mycoupons)){
	foreach($mycoupons as $cp){
		array_push($chkcode,$cp['coupon_code']);
	}
}

if(_array($downcoupons) && _array($chkcode)){
	for($j=count($downcoupons) -1;$j>=0;$j--){
		if(in_array($downcoupons[$j]['coupon_code']	,$chkcode)) unset($downcoupons[$j]);
	}
}
*/
?>

<?include($Dir."lib/style.php")?>

<style>
	.couponList {margin:0px; border-top:1px solid #e6e6e6;}
	.couponList caption {overflow:hidden; margin-left:-9999px; width:1px; height:0px; font-size:0px; line-height:0px;}
	.couponList th {border-bottom:1px solid #e6e6e6; border-right:1px solid #e6e6e6; height:42px; background-color:#f5f5f5; font-size:12px; letter-spacing:-1px;}
	.couponList td {padding:5px 0px; font-size:11px; line-height:16px; border-right:1px solid #e6e6e6;}
	.couponList .pleft {padding-left:6px;}
</style>

<body topmargin="0" leftmargin="0">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="104" background="<?=$Dir?>images/common/coupon_title5.gif"></td>
	</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" class="couponList" align="center" width="100%">
	<caption>적용가능 쿠폰 목록</caption>
	<col width="70"></col>
	<col width="100"></col>
	<col width="90"></col>
	<col width=""></col>
	<col width="80"></col>
	<col width="90"></col>
	<tr>
		<th>쿠폰코드</th>
		<th>쿠폰명</th>
		<th>할인/적립</th>
		<th>적용대상/카테고리</th>
		<th>사용조건</th>
		<th>유효기간</th>
	</tr>
<?	if(_array($mycoupons)){ 
		foreach($mycoupons as $coupon){
			$range = ($coupon['date_start']>0)?substr($coupon['date_start'],0,4)."/".substr($coupon['date_start'],4,2)."/".substr($coupon['date_start'],6,2)." ~ ".substr($coupon['date_end'],0,4)."/".substr($coupon['date_end'],4,2)."/".substr($coupon['date_end'],6,2):date("Y/m/d")." ~ ".date("Y/m/d",mktime(0,0,0,date("m"),date("d")+abs($coupon['date_start']),date("Y")));
			$coupon_desc = number_format($coupon['sale_money']).($coupon['sale_type']<=2?"%":"원").($coupon['sale_type']%2==0?"할인":"적립");
			$limit = (_isInt($coupon['mini_price']))?number_format($coupon['mini_price']).'원 이상 구매시':'&nbsp;';
			
			
			$productList = usableProductOnCoupon($coupon['productcode']);
			$target = '		적용대상 : ';
			if($coupon['vender'] > 0) $target .= '[입점사 : '.$coupon['venderid'].' 전용]';
			if($coupon['use_con_type2']=="N") $target .='['.$productList.'] 제외';
			else $target .= $productList;
?>
	<tr>
		<td align="center"><b><?=$coupon['coupon_code']?></b></td>
		<td class="pleft"><?=$coupon['coupon_name']?></td>
		<td align="center"><?=$coupon_desc?></td>
		<td class="pleft"><?=$target?></td>
		<td class="pleft"><?=$limit?></td>
		<td align="center"><?=$range?></td>		
	</tr>
	<tr><td colspan="6" height="1" bgcolor="#dddddd"></td></tr>
<?		}
	}
?>

<?	if(_array($downcoupons)){ 
		foreach($downcoupons as $coupon){
			$range = ($coupon['date_start']>0)?substr($coupon['date_start'],0,4)."/".substr($coupon['date_start'],4,2)."/".substr($coupon['date_start'],6,2)." ~ ".substr($coupon['date_end'],0,4)."/".substr($coupon['date_end'],4,2)."/".substr($coupon['date_end'],6,2):date("Y/m/d")." ~ ".date("Y/m/d",mktime(0,0,0,date("m"),date("d")+abs($coupon['date_start']),date("Y")));
			$coupon_desc = number_format($coupon['sale_money']).($coupon['sale_type']<=2?"%":"원").($coupon['sale_type']%2==0?"할인":"적립");
			$limit = (_isInt($coupon['mini_price']))?number_format($coupon['mini_price']).'원 이상<br />구매시':'&nbsp;';
			
			
			$productList = usableProductOnCoupon($coupon['productcode']);
			$target = '		적용대상 : ';
			if($coupon['vender'] > 0) $target .= '[입점사 : '.$coupon['venderid'].' 전용]';			
			if($coupon['use_con_type2']=="N") $target .='['.$productList.'] 제외';
			else $target .= $productList;
?>
	<tr>
		<td align="center"><b><?=$coupon['coupon_code']?></b></td>
		<td class="pleft"><?=$coupon['coupon_name']?></td>
		<td align="center"><?=$coupon_desc?><br /><a href="javascript:opener.issue_coupon('<?=$coupon['coupon_code']?>')"><img src="/images/common/icon_download.gif" border="0" alt="쿠폰다운" /></a></td>
		<td class="pleft"><?=$target?></td>
		<td class="pleft"><?=$limit?></td>
		<td align="center"><?=$range?></td>
	</tr>
	<tr><td colspan="6" height="1" bgcolor="#dddddd"></td></tr>
<?		}
	}
?>
</table>
<div style="height:40px; margin:10px; text-align:center;"><a href="javascript:window.close();"><img src="<?=$Dir?>images/common/bigview_btnclose.gif" style="border:0px;" alt="" /></div>
</body>