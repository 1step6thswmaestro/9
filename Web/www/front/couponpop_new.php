<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/func.php");
include_once($Dir."lib/ext/order_func.php");
include_once($Dir."lib/ext/coupon_func.php");

if(strlen($_ShopInfo->getMemid())==0) {
	_alert('비정상적인 접근 입니다.','0');
	exit;
}


if(true !== checkGroupUseCoupon($groupname)) _alert($groupname.' 회원 등급은 쿠폰 사용이 불가능합니다.','0');	


$basketItems = getBasketByArray();
/*
$org_sumprice	= $basketItems["sumprice"];
$usereserve		= $_POST["usereserve"];
$gprice			= $_POST['giftprice'];
*/

$productitems = array();
foreach($basketItems['vender'] as $vd=>$val){	
	foreach($val['products'] as $idx=>$pd){
		if(!_array($productitems[$pd['productcode']])) $productitems[$pd['productcode']] = array();
		$productitems[$pd['productcode']][] = &$basketItems['vender'][$vd]['products'][$idx];
	}
}
/*
_pr($basketItems); 
_pr($productitems);
*/
$coupons = getMyCouponList();

//$_data->coupon_limit_ok=="Y";  //하나의 주문에 여러 쿠폰 사용 가능
$coupon_json = array();

foreach($coupons as $idx=>$coupon){
//	_pr($productitems);
	$tmp = array();
	//$tsumprice = -1*abs(intval($_POST["usereserve"]));
	$tsumprice = $fixmoney = 0;
	$coupon_json[$coupon['coupon_code']] = array();
	
	$tobj = &$coupon_json[$coupon['coupon_code']];
	
	foreach($productitems as $pcode=>$pvals){
		foreach($pvals as $pvalidx=>$pval){
			if($pval['cateAuth']['coupon'] != 'Y') continue;
			if(checkCouponUasble($coupon['productcode'],$pcode,$coupon['use_con_type2'])){			
				$tsumprice += intval($pval['realprice']);
				if($pval['cateAuth']['reserve'] != 'Y') $fixmoney += intval($pval['realprice']);
				array_push($tmp,$pcode);
			}else{

			}
		}
	}
	//echo '<br>-----------------<br>'.$coupon['coupon_code'].":  $tsumprice < ".$coupon['mini_price'].'<br>';
	$tobj['able'] = $coupons[$idx]['able'] = (intval($coupon['mini_price']) > 1 && $tsumprice < $coupon['mini_price'])?'0':'1';
	
	$tobj['mini_price'] = (intval($coupon['mini_price']) > 1)?intval($coupon['mini_price']):0;
	$tobj['reserve'] = $tobj['discount'] = 0;
	
	$tobj['use_point'] = $coupon['use_point'];
	$tobj['etcapply_gift'] = $coupon['etcapply_gift'];
	
	if($tobj['able'] == '1'){
		$tobj['pcodes'] = implode(',',$tmp);
		$tobj['sumprice'] = $tsumprice;
		
		$tmpmoney = intval($coupon['sale_money']);		
		
		if($coupon['sale_type']<'3' && intval($tmpmoney) < 100){
			//$tmpmoney = $tobj['sumprice'] * $tmpmoney /100;
			$tmpmoney = $tmpmoney /100;
		}
		$tobj['type'] = (intval($coupon['sale_type'])%2 == 1)?'reserve':'discount';
		$tobj['sale_money'] = $tmpmoney;
		
	}
}

// php  5.2.0 이상은 추가
$phpVer = str_replace(".","",phpversion());
if( $phpVer >= 520 ){
	array_walk($productitems,'_encode');
	array_walk($coupon_json,'_encode');
}
$productitems = json_encode($productitems);
$coupon_json = json_encode($coupon_json);

//checkCouponUasble
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<title>할인쿠폰 조회 및 적용</title>

<style>
	table, th, td, caption, div, input, select, textarea{FONT-FAMILY:돋움,verdana; font-size: 12px;color: #666666;line-height:16px;}

.itemListTbl{border-top:2px solid #ccc;  empty-cells:show}

.itemListTbl thead th{background:#efefef; border-right:1px solid #ccc;font-size:11px;}
.itemListTbl thead td{background:#efefef; text-align:center; font-weight:bold;font-size:11px;}
.itemListTbl thead th.noCont{ border:0px; font-size:1px; height:10px; line-height:0px;}
.itemListTbl thead th.botLine{ border-bottom:1px solid #ccc;}

.itemListTbl tbody td{ border-bottom:1px solid #ccc; padding-bottom:5px; padding-top:5px;font-size:11px;}

.itemListTbl tfoot td{ border-bottom:1px solid #ccc;font-size:11px;}

.noBodertbl{ border:0px;}
.noBodertbl tbody th{ border:0px; text-align:right;font-size:11px;}
.noBodertbl tbody td{ border:0px; text-align:right; padding-right:10px;font-size:11px;}

/*

.orderTbl{}
.orderTbl caption{ text-align:left; border-bottom:1px solid #00F }
.orderTbl th{background:#efefef; border:1px solid #ccc; text-align:left; padding: 5px 0px 5px 15px; height:30px;font-size:11px;}
.orderTbl td{border:1px solid #ccc; text-align:left; padding: 5px 0px 5px 15px; border-left:0px;font-size:11px;}
.orderTbl td.noCont{border:0px; font-size:1px; height:7px; line-height:1px;}
*/
</style>

<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>js/jquery-1.8.3.min.js"></script>

<script type="text/javascript">
<!--
var $j = jQuery.noConflict();

window.moveTo(10,10);
window.resizeTo(750,650);
</script>

</head>
<style type="text/css">
.couponName{ font-weight:bold; color:blue;}
</style>
<body topmargin="0" leftmargin="0">
<script language="javascript" type="text/javascript">
var thumbimgsize = 60; // 썸네일 이미지 폭
var totalpay = parseInt('<?=$basketItems['sumprice']?>');
var productdata = <?=$productitems?>;
var coupondata  = <?=$coupon_json?>;
var usemileage = parseInt($j('form[name=form1]',opener.document).find('input[name=usereserve]').val());
/*
$j(tones).each(function(idx,val){
	for(p in val) alert(p);
});

*/

function number_format(input){
    var input = String(input);
    var reg = /(\-?\d+)(\d{3})($|\.\d+)/;
    if(reg.test(input)){
        return input.replace(reg, function(str, p1,p2,p3){
                return number_format(p1) + "," + p2 + "" + p3;
            }
        );
    }else{
        return input;
    }
}



$j(function(){	
	$j('input[name=selcoupon]').click(function(){
			 dispProduct($j(this).val());
	});
	
	
	$j('input[name=selcoupon]').each(function(idx,el){
		ccode = $j(el).val();
		tobj = coupondata[ccode];
		if(tobj.mini_price > 0 && parseInt(tobj.sumprice) - parseInt(usemileage) < 1){
			$j(el).attr('disabled','disabled');
		}
	});	
	
	
});
	
function getProductList(coupon_code){
	/*
	$j.post('/json_order.php',{'act':'couponProduct','coupon_code':coupon_code,'id':'<?=$_ShopInfo->getMemid()?>','key':'<?=$_ShopInfo->getTempkey()?>'},function(data){
		dispProduct(data);
	},'json');	*/
	
}


function dispProduct(coupon_code){	
	$j('#productListTbl').find('tbody:eq(0)').html('');
	var cobj = coupondata[coupon_code];
	var pcodes = cobj.pcodes.split(',');	
	var pcode = '';
	var pdata = null;
	var html = '';
	usemileage = parseInt($j('form[name=form1]',opener.document).find('input[name=usereserve]').val());
	var finalmoney= distmp = reserve = discount =0;
	var addstr = reservestr= discountstr = '';
	if(isNaN(usemileage)){
		alert('주문서의 적립금 사용의 값이 올바르지 않습니다.');
		window.close();
	}
	
	if(cobj.able != '1' || pcodes.length < 1){
		html = '<tr><td style="height:30px; text-align:center" colspan="5">적용 가능한 상품이 없습니다.</td></tr>';
		$j('#total_discount').html('0 원');
		$j('#total_payprice').html(number_format(totalpay-usemileage)+'원');
		$j('#couponUseForm').find('input[name=coupon_code]').val('');
		$j('#couponUseForm').find('input[name=productcodes]').val('');
		$j('#couponUseForm').find('input[name=discount]').val(0);
		$j('#couponUseForm').find('input[name=reserve]').val(0);		
		$j('#couponUseForm').find('input[name=etcapply_gift]').val('');		
		$j('#couponUseForm').find('input[name=use_point]').val('');		
	}else{		
		
		finalmoney = parseInt(cobj.sumprice) - usemileage;
		if(finalmoney <= parseInt(cobj.fixmoney)) finalmoney = parseInt(fixmoney);
		if(cobj.sale_money < 1) distmp = parseInt(finalmoney*cobj.sale_money);
		else distmp = parseInt(cobj.sale_money);
		
		if(cobj.type == 'discount'){
			reserve = 0;
			reservestr = '-';					
			discount = distmp;
			discountstr = number_format(discount)+'원';
		}else{
			reserve = distmp;
			reservestr = number_format(reserve)+'원';
			discount = 0;
			discountstr = '-';
		}				
		
		for(i=0;i<pcodes.length;i++){
			pcode = pcodes[i];
			for(pdidx in productdata[pcode]){
				pdata = productdata[pcode][pdidx];
				bsize = parseInt(pdata.tinyimage.bigsize);
				if(!isNaN(bsize) || bsize > thumbimgsize) addstr = pdata.tinyimage.big+'="'+thumbimgsize+'"';
				else addstr = addstr = pdata.tinyimage.big+'="'+bsize+'"';
				
				html += '<tr><td><img src="'+pdata.tinyimage.src+'"  '+addstr+' /></td>';
				html += '<td>'+pdata.productname+'</td>';
				
				if(i < 1){				
					html +='<td rowspan="'+pcodes.length+'" style="text-align:center">'+number_format(finalmoney)+'원</td><td rowspan="'+pcodes.length+'" style="text-align:center">'+discountstr+'</td><td rowspan="'+pcodes.length+'" style="text-align:center">'+reservestr+'</td>';
				}
				html += '</tr>';
			}
		}
		
		$j('#total_discount').html(number_format(discount)+'원');
		$j('#total_payprice').html(number_format(totalpay-discount-usemileage)+'원');		
		
		$j('#couponUseForm').find('input[name=coupon_code]').val(coupon_code);
		$j('#couponUseForm').find('input[name=productcodes]').val(cobj.pcodes);
		$j('#couponUseForm').find('input[name=discount]').val(discount);
		$j('#couponUseForm').find('input[name=reserve]').val(reserve);
		$j('#couponUseForm').find('input[name=etcapply_gift]').val(cobj.etcapply_gift);
		$j('#couponUseForm').find('input[name=use_point]').val(cobj.use_point);
	}
	$j('#productListTbl').find('tbody:eq(0)').html(html);
	
}
</script>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="itemListTbl">
	<caption style="text-align:left">
		<div style="clear:both; border-bottom:1px solid #00F; margin-bottom:10px;">보유 구폰 내역</div>
		<div style="width:420px; float:left">
			고객님 께서 보유하고 계신 할인쿠폰 리스트 입니다.<br />
			할인쿠폰이 없으실 경우 쿠폰 페이지에서 다운 받으신 후 사용 가능합니다.<br />
			<span style="color:red">* 쿠폰은 1회 주문시 한개의 쿠폰만 사용 가능합니다.(중복 불가)</span>
		</div>
		<div style="width:200px; float:right; background:#efefef; height:50px;">쿠폰 영역</div>
		<div style="height:1px; clear:both; width:800px;"></div>
	</caption>
	<thead>
		<tr>
			<th colspan="3" class="noCont"></th>
		</tr>
		<tr>
			<th  style="width:20%;">쿠폰번호</th>
			<th  style="width:60%;">쿠폰명</th>
			<td  style="width:20%;">쿠폰선택</td>
		</tr>
		<tr>
			<th colspan="3" class="noCont botLine"></th>
		</tr>
	</thead>
	<tbody>
	<? 
	if(count($coupons)){
		foreach($coupons as $coupon){ 	
			$coupon['type'] = (intval($coupon['sale_type'])%2 == 1)?1:-1;
			$coupon['money'] = intval($coupon['sale_money'])*($coupon['sale_type']<'3')?0.01:1;
	?>
		<tr>
			<td style="text-align:center;"><?=$coupon['coupon_code']?></td>
			<td style="  padding:10px 0px;">
				<span class="couponName"><?=$coupon['coupon_name']?></span>
				<ul style="padding:0px 0px 0px 15px;">
					<li>쿠폰 <?=($coupon['use_con_type2']=='N'?'제외':'적용')?> 상품 : <?=($coupon['productcode'] == 'ALL'?'전체상품':implode(', ',$coupon['productstr']))?></li>
					<li>제한사항 : <?=(intval($coupon['mini_price']) < 1)?'없음':number_format(intval($coupon['mini_price'])).'원 이상'?></li>
					<li style="color:red">혜택 : <?=number_format(intval($coupon['sale_money']))?><?=($coupon['sale_type']<'3')?'%':'원'?> <?=(intval($coupon['sale_type'])%2 == 1)?'적립':'할인'?></li>
					<li style="font-weight:bold">기간 : <?=substr($coupon['date_start'],0,4).'.'.substr($coupon['date_start'],4,2).'.'.substr($coupon['date_start'],6,2).' '.substr($coupon['date_start'],8,2).'시'?>
								<? if(!_empty($coupon['date_end'])) echo ' ~ '.substr($coupon['date_end'],0,4).'.'.substr($coupon['date_end'],4,2).'.'.substr($coupon['date_end'],6,2).' '.substr($coupon['date_end'],8,2).'시'?></li>
				</ul>
			</td>
			<td style="text-align:center;"><input type="radio" name="selcoupon" value="<?=$coupon['coupon_code']?>" <? if($coupon['able'] != '1') echo 'disabled="disabled"';?>  /></td>
		</tr>
	<? 	}// end foreach
	}else{	 ?>
		<tr>
			<td colspan="3" style="height:30px; text-align:center">사용가능한 쿠폰이 없습니다.</td>
		</tr>
	<? } ?>
	</tbody>
</table>

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="itemListTbl" style="margin-top:30px;" id="productListTbl">
	<caption style="text-align:left; border-bottom: 1px solid #00F; margin-bottom:10px;">적용 가능 리스트</caption>
	<thead>
		<tr>
			<th colspan="5" class="noCont"></th>
		</tr>
		<tr>
			<th colspan="2">상품명</th>
			<th  style="width:20%;">적용금액</th>
			<th  style="width:20%;">할인금액</th>
			<td  style="width:20%;">적립금액</td>
		</tr>
		<tr>
			<th colspan="5" class="noCont botLine"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="height:30px; text-align:center" colspan="5">선택된 쿠폰이 없습니다.</td>
		</tr>
	</tbody>
</table>
<form name="couponUseForm" id="couponUseForm">
<input type="hidden" name="coupon_code" value="" />
<input type="hidden" name="productcodes" value=""/>
<input type="hidden" name="discount" value="0" />
<input type="hidden" name="reserve" value="0" />
<input type="hidden" name="etcapply_gift" value="" />
<input type="hidden" name="use_point" value="" />
<div style="width:100%; border-top:1px solid #efefef; border-bottom:1px solid #efefef; text-align:right; margin-top:10px; margin-bottom:10px; padding:20px 0px;">
	<table style="width:50%; float:right; display:block">
		<tr>
			<td>할인 총액 : </td><td id="total_discount">0 원</td>
		</tr>
		<tr>				
			<td>총결제 금액 : </td><td id="total_payprice"><?=number_format($sumprice)?> 원</td>
		</tr>
	</table>
</div>
<div style="width:100%; padding:15px 0px; text-align:center">
	<a href="#btn_sc" onclick="checkCoupon();return false;"><img src="/images/common/order/T01/btn_send.gif" alt="적용하기" border="0" /></a>
	<a href="#btn_sc" onclick="cancelCoupon();return false;"><img src="/images/common/order/T01/btn_cancel.gif" border="0" alt="취소하기" /></a>
</div>
</form>
<script language="javascript" type="text/javascript">
function checkCoupon(){
	var of = $j('form[name=form1]',opener.document);
	var mf = $j('#couponUseForm');
	
	var coupon_code = $j(mf).find('input[name=coupon_code]').val();
	var couponproduct = $j(mf).find('input[name=productcodes]').val()
	var coupon_price = $j(mf).find('input[name=discount]').val();
	var reserveprice = $j(mf).find('input[name=reserve]').val();
	
	
	var apply_gift = $j(mf).find('input[name=etcapply_gift]').val();
	var apply_group_disc = $j(mf).find('input[name=use_point]').val();
	
	if($j.trim(coupon_code).length < 1){
		alert('선택된 쿠폰이 없습니다.');
		return;
	}else{
		$j(of).find('input[name=coupon_code]').val(coupon_code);
		$j(of).find('input[name=coupon_price]').val(coupon_price);
		$j(of).find('input[name=coupon_reserve]').val(reserveprice);
		$j(of).find('input[name=couponproduct]').val(couponproduct);
		
		if(apply_gift == 'A') $j(of).find('input[name=apply_gift]').val('N');
		else $j(of).find('input[name=apply_gift]').val('');
		
		if(apply_group_disc == 'A') $j(of).find('input[name=apply_group_disc]').val('N');
		else $j(of).find('input[name=apply_group_disc]').val('');
				
		/*
		$j(of).find('input[name=etcapply_gift]').val(couponproduct);
		$j(of).find('input[name=couponproduct]').val(couponproduct);*/
		//$j(of).find('input[name=order_use_coupon]').val(couponproduct);		
		opener.solvPrice();
		window.close();
	}

}

function cancelCoupon(){
	opener.coupon_default();
	window.close();
}

</script>
</body>
</html>