<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/func.php");
include_once($Dir."lib/ext/order_func.php");
include_once($Dir."lib/ext/coupon_func.php");


if(strlen($_ShopInfo->getMemid())==0) {
	exit;
}

if(true !== checkGroupUseCoupon($groupname)) _alert($groupname.' 회원 등급은 쿠폰 사용이 불가능합니다.','0');


if( $_REQUEST['offlinecoupon'] == "popup" ) {
	$onloadOfflinecouponAuthPop = " onload=\"offlinecoupon_auth();\"";
}


//쿠폰 발행이 있을 경우
if($_REQUEST['mode']=="coupon" && strlen($_REQUEST['coupon_code'])==8){
	$onload = '';
	$sql = "SELECT * FROM tblcouponinfo ";
	$sql.= "WHERE coupon_code = '".$_REQUEST['coupon_code']."'";

	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		if($row->issue_tot_no>0 && $row->issue_tot_no<$row->issue_no+1) {
			$onload="<script>alert(\"모든 쿠폰이 발급되었습니다.\");</script>";
		} else {
			$date=date("YmdHis");
			if($row->date_start>0) {
				$date_start=$row->date_start;
				$date_end=$row->date_end;
			} else {
				$date_start = substr($date,0,10);
				$date_end = date("Ymd",mktime(0,0,0,substr($date,4,2),substr($date,6,2)+abs($row->date_start),substr($date,0,4)))."23";
			}
			$sql = "INSERT tblcouponissue SET ";
			$sql.= "coupon_code	= '".$_REQUEST['coupon_code']."', ";
			$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
			$sql.= "date_start	= '".$date_start."', ";
			$sql.= "date_end	= '".$date_end."', ";
			$sql.= "date		= '".$date."' ";
			//echo $sql;
			mysql_query($sql,get_db_conn());
			if(!mysql_errno()) {
				$sql = "UPDATE tblcouponinfo SET issue_no = issue_no+1 ";
				$sql.= "WHERE coupon_code = '".$_REQUEST['coupon_code']."'";
				mysql_query($sql,get_db_conn());

				$onload="<script>alert(\"해당 쿠폰 발급이 완료되었습니다.\\n\\n상품 주문시 해당 쿠폰을 사용하실 수 있습니다.\");</script>";
			} else {
				if($row->repeat_id=="Y") {	//동일인 재발급이 가능하다면,,,,
					$sql = "UPDATE tblcouponissue SET ";
					if($row->date_start<=0) {
						$sql.= "date_start	= '".$date_start."', ";
						$sql.= "date_end	= '".$date_end."', ";
					}
					$sql.= "used		= 'N' ";
					$sql.= "WHERE coupon_code='".$_REQUEST['coupon_code']."' ";
					$sql.= "AND id='".$_ShopInfo->getMemid()."' ";
					//echo $sql;
					mysql_query($sql,get_db_conn());
					$onload="<script>alert(\"해당 쿠폰 발급이 완료되었습니다.\\n\\n상품 주문시 해당 쿠폰을 사용하실 수 있습니다.\");</script>";
				} else {
					$onload="<script>alert(\"이미 쿠폰을 발급받으셨습니다.\\n\\n해당 쿠폰은 재발급이 불가능합니다.\");</script>";
				}
			}
		}
	}
	mysql_free_result($result);

	if(_empty($onload)){
		echo $onload;
	}
	_alert('','/front/couponpop.php?otype='.$$_EQUEST['otype']);
	exit;
}


$productitems = array();
// 주문타입별 장바구니 테이블
$basket = basketTable($_REQUEST['ordertype']);

if($_REQUEST['otype'] == 'scheduled'){	
	include_once $Dir.'scheduled_delivery/config.php';
	$basket = getScheduledBasket();
	if(!_empty($basket['msg']) && $basket['msg'] != 'success'){
		_alert($basket['msg'],'0');
		exit;
	}
	foreach($basket['items'] as $idx=>$pd){
		if($pd['cateAuth']['coupon'] != 'Y') continue;
		$basket['items'][$idx]['realprice'] = $pd['sumprice'];
		$productitems[$idx] = &$basket['items'][$idx];
	}
	$basketItems['sumprice'] = $basket['info']['sumprice'];
}else{
	$basketItems = getBasketByArray($basket);
	//echo "<div style=\" height:500px; overflow:scroll;  border:2px solid #ff0000 ;  text-align:left;\">";
	//_pr($basketItems);
	//echo "</div>";
	
	
	foreach($basketItems['vender'] as $vd=>$val){
		foreach($val['products'] as $idx=>$pd){
			if($pd['cateAuth']['coupon'] != 'Y') continue;
			//if(!_array($productitems[$pd['productcode']])) $productitems[$pd['productcode']] = array();
			$productitems[] = &$basketItems['vender'][$vd]['products'][$idx];
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<title>할인쿠폰 조회 및 적용</title>

<style>
	table, th, td, caption, div, input, select, textarea{FONT-FAMILY:돋움,verdana; font-size: 12px;color: #666666;line-height:16px;}
</style>

<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>js/jquery-1.7.2.min.js"></script>

<script type="text/javascript">
<!--
var $j = jQuery.noConflict();

//window.moveTo(10,10);
window.resizeTo(750,600);

var orgSumPrice = parseInt("<?=$basketItems['sumprice']?>");
var totalpay = orgSumPrice;

var coupondata  = '<?=$coupon_json?>';
var coupon_limit = '<?=$_data->coupon_limit_ok?>';

var giftprice		= 0;
var discount		= 0;
var reserve		= 0;
var arrobj			= [];
var bank_only	= "N"; //현금 사용시 가능한 쿠폰이 선택된 경우 결제는 현금 및 가상계좌로만 가능해야 한다.
var giftUnUsed	= false; //사은품 불가 쿠폰 사용 여부
var GroupDisUnUsed = false; // 회원등급할인 불가 쿠폰 사용 여부

$j(document).ready(function(){

	// 중복사용쿠폰 선택
	$j('.unlimitcouponselect').change(function(){
		calprice();
	});

	// 단일사용쿠폰 선택
	$j('.limitcouponselect').change(function(){
		$this = $j(this);
		$j('.limitcouponselect').each(function(idx,el){
			if($j.trim($j($this).val()) != ''){
				if($j($this).attr('seq') != $j(el).attr('seq')){
					if($j(el).val() ==  $j($this).val()){
						alert('기존 동일 쿠폰 사용 항목이 초기화 됩니다.');
						$j(el).val('');
					}
				}
			}
		})
		calprice();
	});

	// 초기화 선택
	$j(".reset").click(function(){
		document.frm.reset();
		arrobj = [];
		//////////////////////
		$j("#total_discount_txt").html('0');
		$j("#total_payprice").html(number_format(orgSumPrice));
		basketTemp( 'default' );// 배송비 초기화
	});

	$j('#total_sumprice').html(number_format(totalpay));
	$j('#total_payprice').html(number_format(totalpay));
});

// 계산처리
function calprice(){
	// 초기화
	discount = 0;
	reserve =0;
	arrobj = [];
	giftUnUsed = false;
	GroupDisUnUsed = false;
	var deli_price = $j('#default_deli_sumprice_org').val();
	var unUsedGiftcouponList='';
	var unUsedGroupDisCouponList='';

	var basketTempList = ''; // 배송비 재 계산 리스트


	$j("#moreMsg").html(""); //사은품 불가 쿠폰 메세지
	var etcapply_gift_temp = ''; // 사은품 불가 쿠폰 메세지 적용쿠폰리스트 중복 체크

	$j("#moreMsg1").html(""); // 회원등급할인 불가 쿠폰 메세지
	var use_point_temp = ''; // 회원등급할인 불가 쿠폰 메세지 적용쿠폰리스트 중복 체크

	// 쿠폰선택 리스트
	$j('.unlimitcouponselect option:selected, .limitcouponselect option:selected').each(function(idx,el){
		if($j.trim($j(el).val()) != ''){
			var tmp = dr = dc = 0;
			var seq = $j(el).parent().attr('seq');
			var oripay = parseInt($j("#step3_"+seq+"_price").val()); // 상품 원래 가격
			var saletype = parseInt($j(el).attr('sale_type')); // 할인/적립 타입
			var salemoney = parseInt($j(el).attr('sale_money')); // 할인/적립 금액/%
			var amount_floor = parseInt($j(el).attr('amount_floor')); // 금액절사 1:일원/2:10원/3:백원

			/*
				saletype
				1 : + % : 적립 %
				2 : - % :  할인 %
				3 : + 원 : 적립 원
				4 : - 원 :  할인 원
			*/

			if(saletype < 3 && salemoney >= 100){
				alert('연산 오류 입니다 관리자에게 문의 하세요.');
				return false;
			}
			if(saletype < 3){
				// % 비율
				po = 0;
				if( !isNaN(amount_floor) && amount_floor > 0 && amount_floor < 4) po += amount_floor;
				tmp = Math.floor(oripay*(salemoney/ 100) / Math.pow(10,po))*Math.pow(10,po);
			}else {
				// 금액
				tmp = salemoney;
			}
			if(saletype%2 == 1){
				dr = tmp; // 적립
			}else{
				dc = tmp; // 할인
			}

			$j(el).data('dr',dr);
			$j(el).data('dc',dc);
			
			discount += dc; // 총할인
			reserve += dr; // 총적립


			//사은품 불가 쿠폰
			if($j(el).attr('etcapply_gift') == "A"){
				if ( etcapply_gift_temp != $j(el).val() ) {
					etcapply_gift_temp = $j(el).val();
					unUsedGiftcouponList += "["+$j(el).val()+"] ";
				}
				$j("#moreMsg").html("<br><font color='red'>"+unUsedGiftcouponList+" 쿠폰사용시 사은품을 받을 수 없습니다.</font>");
				giftUnUsed = true;
			}

			// 회원등급할인 불가 쿠폰
			if( $j(el).attr('use_point') == 'A' ) {
				if ( use_point_temp != $j(el).val() ) {
					use_point_temp = $j(el).val();
					unUsedGroupDisCouponList += "["+$j(el).val()+"] ";
				}
				$j("#moreMsg1").html("<br><font color='blue'>"+unUsedGroupDisCouponList+" 쿠폰사용시 등급할인 혜택을 받을 수 없습니다.</font>");
				GroupDisUnUsed = true;
			}

			$j(el).attr('product',$j("#step3_"+seq+"_product").val()); //상품코드
			$j(el).attr('opt1',$j("#step3_"+seq+"_product").attr('opt1')); //상품 옵션 1 인덱스 코드
			$j(el).attr('opt2',$j("#step3_"+seq+"_product").attr('opt2')); //상품 옵션 2 인덱스 코드
			$j(el).attr('optidxs',$j("#step3_"+seq+"_product").attr('optidxs')); //상품 옵션s 인덱스 코드
			arrobj.push($j(el));

			basketTempList += $j("#step3_"+seq+"_product").val()+"_"+$j("#step3_"+seq+"_product").attr('opt1')+"_"+$j("#step3_"+seq+"_product").attr('opt2')+'_'+$j(el).attr('optidxs')+"|"+dc+"-";
		}
	});

	// 배송비 재 계산
	basketTemp( basketTempList );

	// 쿠폰이 결제액 보다 클 경우
	if ( orgSumPrice < discount ) discount = orgSumPrice;

	$j("#basketTempList").val(basketTempList);

	$j("#total_discount_txt").html(number_format(discount));
	$j("#total_reserve_txt").html(number_format(reserve));
	$j("#total_payprice").html(number_format(orgSumPrice - discount));
}


// 쿠폰 적용 하기
function checkCoupon(){
	var couponlist = ""; // 쿠폰 리스트
	var dcpricelist = ""; // 할인액 리스트
	var drpricelist = ""; // 적립액 리스트
	var couponproduct = ""; // 쿠폰사용 상품 리스트 (쿠폰코드_상품코드_옵션1idx_옵션2idx)
	var couponBankOnly = ""; // if (현금 사용시 가능한 쿠폰이 선택된 경우 ) Y else N


	$j(arrobj).each(function(idx,el){
		couponlist += "|"+$j(el).val();
		dcpricelist += "|"+$j(el).data('dc');
		drpricelist += "|"+$j(el).data('dr');
		couponproduct += "|"+$j(el).val()+'_'+$j(el).attr('product')+'_'+$j(el).attr('opt1')+'_'+$j(el).attr('opt2')+'_'+$j(el).attr('optidxs');
		if($j(el).attr('bank_only') == "Y") couponBankOnly = "Y";

	});


	opener.document.getElementById("couponlist").value = couponlist;
	opener.document.getElementById("dcpricelist").value = dcpricelist;
	opener.document.getElementById("drpricelist").value = drpricelist;
	opener.document.getElementById("couponproduct").value = couponproduct;
	opener.document.getElementById("couponBankOnly").value = couponBankOnly;

	if(opener.document.getElementById("possible_gift_price_used")) opener.document.getElementById("possible_gift_price_used").value = ( giftUnUsed ) ? "N" : "Y"; // 사은품 불가 쿠폰사용
	if(opener.document.getElementById("possible_group_dis_used")) opener.document.getElementById("possible_group_dis_used").value = ( GroupDisUnUsed ) ? "N" : "Y";  // 회원등급 할인 중복 불가 쿠폰사용
	if(opener.document.getElementById("deliprice")) opener.document.getElementById("deliprice").value = $j('#basketTempReturn').val(); // 배송비

	opener.document.getElementById("coupon_price").value = discount; // 총할인
	opener.document.getElementById("coupon_reserve").value = reserve; // 총적립

	if(opener.document.getElementById("basketTempList")) opener.document.getElementById("basketTempList").value = $j("#basketTempList").val(); // 할인 정보


	opener.solvPrice();

	window.close();
}

// 취소
function cancelCoupon(){
	opener.resetCoupon();
	window.close();
}

// 쿠폰 다운로드
function issue_coupon(coupon_code,productcode){
	document.couponissueform.mode.value="coupon";
	document.couponissueform.coupon_code.value=coupon_code;
	document.couponissueform.submit();
}



// 오프라인 쿠폰 등록
function offlinecoupon_auth () {
	window.open('/front/offlinecoupon_auth.php?reloadchk=no','OffLineCoupon','width=300,height=200');
}


// 쿠폰 할인 적용 배송비 재계산
// ex ) basketTemp( '002001000000000003_0_0|5000-002002000000000002_2_3|5000' );
//	상품코드_옵션1인덱스_옵션2인덱스|할인가격-상품코드_옵션1인덱스_옵션2인덱스|할인가격
// "-" : 상품 리스트 구분 , "|" : 상품키|할인가격 구분
function basketTemp( code ) {
	if( code == 'default' ) {
		var result = <?=!_empty($basketItems['deli_price'])?$basketItems['deli_price']:0?>;
		$j('#basketTempReturn').val(result);
		$j('#total_deli_price').html(number_format(parseInt(result)));
	} else {
		$j.post(
			"basket.temp.php",
			{ code:code },
			function(result){
				$j('#basketTempReturn').val(result);
				$j('#total_deli_price').html(number_format(parseInt(result)));
				//alert("총 배송료 : "+result);
				//return result;
			}
		);
	}
}


//-->
</script>


<style>
	.tline {border-right:1px solid #e6e6e6;}
	#total_discount_txt {color:#ff6600;}
	#total_payprice{color:#ff6600; font-size:18px; font-family:verdana; letter-spacing:-0.5pt;}
</style>
</head>

<body topmargin="0" leftmargin="0" <?=$onloadOfflinecouponAuthPop?>>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td background="/images/common/order/T01/coupon_skin_bg.gif"><img src="/images/common/order/T01/coupon_skin_title.gif"></td>
	</tr>
</table>
<form name="frm" action="">
<table border="0" cellpadding="0" cellspacing="0" width="94%" align="center">
	<tr><td><img src="/images/common/order/T01/coupon_sstitle02.gif"></td></tr>
	<tr>
		<td style="font-size:11px; letter-spacing:-1px; padding-bottom:15px;">
			적용가능한 해당쿠폰을 선택하고 적용하기를 누르시면 쿠폰을 적용하여 상품구매가 가능합니다.<br />
			<!-- <span style="color:#ff6600">하나의 상품에 하나의 쿠폰만 사용가능합니다.</span><br /> //-->
			<span style="color:#0066ff;">다운로드 쿠폰은 다운로드 클릭 후 사용하실 수 있습니다.<br />[중복]쿠폰은 두 상품 이상에 사용 가능한 쿠폰입니다.</span><br />
			<span style="color:#ff6600">오프라인 쿠폰은 [<a href="javascript:offlinecoupon_auth();" style="font-weight:bold; color:#ff6600;">여기</a>] 클릭 하여 등록후 사용하실 수 있습니다.</span>
		</td>
	</tr>
	<tr><td height="1" bgcolor="#e6e6e6"></td></tr>
	<tr>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<col width="160"></col>
				<col width="15"></col>
				<col width="50"></col>
				<col width="70"></col>
				<col width="70"></col>
				<col width="140"></col>
				<col width=""></col>

				<tr height="40" align="center" style="background-color:#f5f5f5; letter-spacing:-0.5px; font-weight:bold;">
					<td colspan="2" class="tline">상품명/선택사항</td>
					<td class="tline">수량</td>
					<td class="tline">판매금액</td>
					<td class="tline">쿠폰종류</td>
					<!--<td class="tline">합계</td>-->
					<td class="tline">단일쿠폰</td>
					<td>중복쿠폰</td>
				</tr>
				<tr><td height="1" bgcolor="#e6e6e6" colspan="7"></td></tr>


<?
	$sumprice =$p_cnt = $reserveprice =0;
	$sumprice -= $usereserve;
	//_pr($productitems);

	$chkcouponcode = array();
	$ablecoupons = array();
	$mycoupons = array();

	foreach($productitems as $idx=>$product){
		//_pr($product);
		$coupons = array();
		$coupons = getMyCouponList($product['productcode']);

		//_pr($coupons);

		$p_cnt = $idx+1;


		$limitcoupons = array();
		$unlimitcoupons = array();

		if( $product['cateAuth']['coupon'] == "Y") {
			if(_array($coupons)){

				foreach($coupons as $coupon){

					//echo $coupon['etcapply_gift'].", ";
					if(!in_array($coupon['coupon_code'],$chkcouponcode)) {
						array_push($chkcouponcode,$coupon['coupon_code']);
						array_push($mycoupons,$coupon); // 적용가능 쿠폰 리스트
					}

					if($coupon['use_con_type2'] != "N"){
						if($coupon['mini_price'] > 0 && ($coupon['mini_price'] > $product['realprice'] || $product['realprice'] < 100)) continue;
						$coupon['etcapply_gift'] = ($coupon['etcapply_gift'] == "A" && $product['cateAuth']['gift'] == "Y")?'A':'';
					}
					//echo "[".$product['cateAuth']['gift']."], "."(".$coupon['etcapply_gift']."), ";

					if($coupon['vender'] > 0 && $product['vender']  != $coupon['vender']) continue;

					if($coupon['order_limit']=="N") {
						array_push($unlimitcoupons,$coupon);
					} else {
						array_push($limitcoupons,$coupon);
					}
				}
				unset($coupons);
			}
		}
?>
				<tr>
					<td align="center" width="60" height="60" style="padding:5px 0px;"><!--상품이미지--><img src="<?=$product['tinyimage']['src']?>" width="50" /></td>
					<td width="100" class="tline">
						<!--상품명/옵션-->
						<?=strip_tags($product['productname'])?>
						<font style="font-size:11px; color:#888888;"><?=$product['optvalue']?></font>
						<?=$product['package_str']?>
					</td>
					<td align="center" class="tline"><!--수량--><?=$product['quantity']?>개</td>
					<td align="center" class="tline"><!--판매가격--><b><?=number_format($product['sellprice'])?>원</b></td>
					<td align="center" class="tline" style="font-size:11px;">단일 <?=number_format(count($limitcoupons))?>장<br />중복 <?=number_format(count($unlimitcoupons))?>장</td>
					<!--<td align="center" class="tline">-->
						<!--합계-->
						<!--<font color="#ff6600"><b><?=number_format($product['realprice'])?>원</b></font>-->
						<input type="hidden" name="step3_<?=$p_cnt?>_price" id="step3_<?=$p_cnt?>_price" value="<?=$product['realprice']?>"/>
						<input type="hidden" name="step3_<?=$p_cnt?>_product" id="step3_<?=$p_cnt?>_product" opt1="<?=$product['opt1_idx']?>" opt2="<?=$product['opt2_idx']?>" optidxs="<?=$product['optidxs']?>" value="<?=$product['productcode']?>"/>
					<!--</td>-->
					<td align="center" class="tline">
						<select name="lim_coupon_<?=$p_cnt?>" class="limitcouponselect" seq="<?=$p_cnt?>">
							<option value="">단일사용쿠폰</option>
							<?
								if(_array($limitcoupons)){
									foreach($limitcoupons as $coupon){
							?>
							<option value="<?=$coupon['coupon_code']?>" sale_type="<?=$coupon['sale_type']?>" sale_money="<?=$coupon['sale_money']?>" amount_floor="<?=$coupon['amount_floor']?>"  discount="" etcapply_gift="<?=$coupon['etcapply_gift']?>" bank_only="<?=$coupon['bank_only']?>" order_limit="<?=$coupon['order_limit']?>" use_point="<?=$coupon['use_point']?>">[<?=$coupon['coupon_code']?>]<?=number_format(intval($coupon['sale_money'])).(($coupon['sale_type']<'3')?'%':'원').((intval($coupon['sale_type'])%2 == 1)?'적립':'할인')?></option>
							<?
									}
								}
							?>
						</select>
					</td>
					<td align="center">
						<select name="lim_coupon_<?=$p_cnt?>" class="unlimitcouponselect" seq="<?=$p_cnt?>">
							<option value="">중복사용쿠폰</option>
							<?
								if(_array($unlimitcoupons)){
									foreach($unlimitcoupons as $coupon){
							?>
							<option value="<?=$coupon['coupon_code']?>" sale_type="<?=$coupon['sale_type']?>" sale_money="<?=$coupon['sale_money']?>" amount_floor="<?=$coupon['amount_floor']?>" discount="" etcapply_gift="<?=$coupon['etcapply_gift']?>" bank_only="<?=$coupon['bank_only']?>" order_limit="<?=$coupon['order_limit']?>" use_point="<?=$coupon['use_point']?>">[<?=$coupon['coupon_code']?>]<?=number_format(intval($coupon['sale_money'])).(($coupon['sale_type']<'3')?'%':'원').((intval($coupon['sale_type'])%2 == 1)?'적립':'할인')?></option>
							<?
									}
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td height="1" bgcolor="#e6e6e6" colspan="7"></td></tr>
				<?
					$downcoupons = ableCouponOnProduct($product['productcode'],0,true);
					$newcode = array();


					for($qq=count($downcoupons) -1;$qq >=0;$qq--){
						if(in_array($downcoupons[$qq]['coupon_code'],$chkcouponcode)) unset($downcoupons[$qq]);
						else array_push($newcode,$downcoupons[$qq]['coupon_code']);
					}

					$chkcouponcode = array_merge($chkcouponcode,$newcode);
					$ablecoupons = array_merge($ablecoupons,$downcoupons);
					unset($downcoupons,$newcode);
	}
?>
			</table>
		</td>
	</tr>
	<tr>
		<td align="right" style="padding:10px 22px;">
			<table border="0" cellpadding="0" cellspacing="0">
				<input type="hidden" name="step3_orgprice" id="step3_orgprice" value="<?=$sumprice?>"/>
				<input type="hidden" name="step3_discount" id="step3_discount" value="0"/>
				<input type="hidden" name="total_discount" value="0" />
				<tr>
					<td colspan="5" style="color:#999;">
						쿠폰적립 금액 : <span id="total_reserve_txt" style=" font-weight:bold">0</span>원
						/
						배송비 : <b><span id="total_deli_price"><?=number_format($basketItems['deli_price'])?></span></b>원
						/
						상품금액 합계 : <b><span id="total_sumprice"><?=number_format($sumprice)?></span></b>원
						/
						<span style="color:#222;"><b>할인 쿠폰적용 :</span> <span id="total_discount_txt">0</span><span style="color:#222;">원</span></b>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td height="1" bgcolor="#e6e6e6"></td></tr>
	<tr>
		<td align="right">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td align="right" style="font-size:11px; color:#999; padding-top:10px;">
						적립쿠폰은 상품 배송완료 후 적립되는 쿠폰입니다.<span id="moreMsg"></span><span id="moreMsg1"></span>
					</td>
				</tr>
				<tr>
					<td style="color:#222; padding:10px 0px; font-weight:bold;">쿠폰적용 후 총 결제 금액 : <span id="total_payprice"><?=number_format($sumprice)?></span>원</font>(배송비 제외)</td>
				</tr>
			</table>
			<!-- 쿠폰적용 할인된 정보 -->
			<input type="hidden" name="basketTempReturn" id="basketTempReturn" value="<?=$basketItems['deli_price']?>">
			<input type="hidden" name="default_deli_sumprice_org" id="default_deli_sumprice_org" value="<?=$basketItems['deli_price']?>">
		</td>
	</tr>
	<tr><td height="1" bgcolor="#e6e6e6"></td></tr>
	<tr>
		<td align="center" style="padding-top:15px;">
			<table border="0" cellpadding="0" cellspacing="0">

				<tr>
					<td><a href="#btn_sc" onClick="checkCoupon();return false;"><img src="/images/common/order/T01/btn_send.gif" alt="적용하기" border="0" /></a></td>
					<td><img src="/images/common/order/T01/btn_reset.gif" alt="초기화" style="cursor:hand" class="reset"></td>
					<td><a href="#btn_sc" onClick="cancelCoupon();return false;"><img src="/images/common/order/T01/btn_cancel.gif" border="0" alt="취소하기" /></a></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?
//$mycoupons = getMyCouponList();

//_pr($mycoupons);

if(_array($ablecoupons) || _array($mycoupons)){ ?>
<style>
	.couponList {border:1px solid #e6e6e6; margin:0px 5px; border-left:hidden; border-right:hidden;}
	.couponList caption {overflow:hidden; margin-left:-9999px; width:1px; height:0px; font-size:0px; line-height:0px;}
	.couponList th {border-bottom:1px solid #e6e6e6; border-bottom:1px solid #e6e6e6; height:40px; font-size:12px; background-color:#f5f5f5; letter-spacing:-0.5px;}
	.couponList td {padding:3px 0px; font-size:11px;border-bottom:1px solid #e6e6e6;}
	.couponList td.endsell {border-bottom:0}
</style>

<table border="0" cellpadding="0" cellspacing="0" align="center" width="94%">
	<tr><td height="40"></td></tr>
	<tr><td><img src="/images/common/order/T01/coupon_sstitle03.gif"></td></tr>
	<tr>
		<td style="font-size:11px; letter-spacing:-1px; padding-bottom:4px;">
			<span style="color:#0066ff;">[중복]쿠폰은 두 상품 이상에 사용 가능한 쿠폰입니다.</span>
		</td>
	</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" class="couponList" align="center" width="94%">
	<caption>적용가능 쿠폰 목록</caption>
	<col width="75"></col>
	<col width="100"></col>
	<col width="90"></col>
	<col width=""></col>
	<col width="90"></col>
	<col width="90"></col>
	<tr>
		<th class="tline">쿠폰코드</th>
		<th class="tline">쿠폰명</th>
		<th class="tline">할인/적립</th>
		<th class="tline">적용대상/카테고리</th>
		<th class="tline">사용조건</th>
		<th>유효기간</th>
	</tr>
<?	if(_array($mycoupons)){
		foreach($mycoupons as $idx=>$coupon){
			$range = ($coupon['date_start']>0)?substr($coupon['date_start'],0,4)."/".substr($coupon['date_start'],4,2)."/".substr($coupon['date_start'],6,2)." ~ ".substr($coupon['date_end'],0,4)."/".substr($coupon['date_end'],4,2)."/".substr($coupon['date_end'],6,2):date("Y/m/d")." ~ ".date("Y/m/d",mktime(0,0,0,date("m"),date("d")+abs($coupon['date_start']),date("Y")));
			$coupon_desc = number_format($coupon['sale_money']).($coupon['sale_type']<=2?"%":"원").($coupon['sale_type']%2==0?"할인":"적립");
			$limit = (_isInt($coupon['mini_price']))?number_format($coupon['mini_price']).'원 이상<br />구매시':'&nbsp;';

			$productList = usableProductOnCoupon($coupon['productcode']);


			$target = '		적용대상 : ';
			if($coupon['vender'] > 0) $target .= '[입점사 : '.$coupon['venderid'].' 전용]';
			if($coupon['use_con_type2']=="N") $target .='['.$productList.'] 제외';
			else $target .= $productList;

			if($coupon['order_limit']=='N') {
				$coupon_order_limit_img = "order_unlimit.gif";
				$coupon_order_limit_alt = "중복적용쿠폰";
				$target .= "<img src=\"/images/common/order/".$coupon_order_limit_img."\" alt=\"".$coupon_order_limit_alt."\">";
			}

			/*
			else {
				$coupon_order_limit_img = "order_limit.gif";
				$coupon_order_limit_alt = "단일적용쿠폰";
			}
			*/



			$addclass = ($idx == count($mycoupons)-1)?' class="endsell"':'';
?>
	<tr>
		<td class="tline" align="center" <?=$addclass?>><? if(strlen($coupon['coupon_code'])>0){ ?><b><?=$coupon['coupon_code']?></b><?}else{?>&nbsp;<?}?></td>
		<td class="tline" style="padding-left:6px;" <?=$addclass?>><?=$coupon['coupon_name']?></td>
		<td class="tline" align="center" <?=$addclass?>><?=$coupon_desc?></td>
		<td class="tline" style="padding-left:6px;" <?=$addclass?>><?=$target?></td>
		<td class="tline" style="padding-left:6px;" <?=$addclass?>><?=$limit?></td>
		<td align="center" <?=$addclass?>><?=$range?></td>
	</tr>
<?		}
	}
?>

<?	if(_array($ablecoupons)){
		foreach($ablecoupons as $idx=>$coupon){
			$range = ($coupon['date_start']>0)?substr($coupon['date_start'],0,4)."/".substr($coupon['date_start'],4,2)."/".substr($coupon['date_start'],6,2)." ~ ".substr($coupon['date_end'],0,4)."/".substr($coupon['date_end'],4,2)."/".substr($coupon['date_end'],6,2):date("Y/m/d")." ~ ".date("Y/m/d",mktime(0,0,0,date("m"),date("d")+abs($coupon['date_start']),date("Y")));
			$coupon_desc = number_format($coupon['sale_money']).($coupon['sale_type']<=2?"%":"원").($coupon['sale_type']%2==0?"할인":"적립");
			$limit = (_isInt($coupon['mini_price']))?number_format($coupon['mini_price']).'원 이상<br />구매시':'&nbsp;';

			$productList = usableProductOnCoupon($coupon['productcode']);
			$target = '		적용대상 : ';
			if($coupon['vender'] > 0) $target .= '[입점사 : '.$coupon['venderid'].' 전용]';
			if($coupon['use_con_type2']=="N") $target.'['.$productList.'] 제외';
			else $target .= $productList;

			if($coupon['order_limit']=='N') {
				$coupon_order_limit_img = "order_unlimit.gif";
				$coupon_order_limit_alt = "중복적용쿠폰";
				$target .= "<img src=\"/images/common/order/".$coupon_order_limit_img."\" alt=\"".$coupon_order_limit_alt."\">";
			}

			/*
			else {
				$coupon_order_limit_img = "order_limit.gif";
				$coupon_order_limit_alt = "단일적용쿠폰";
			}
			*/

			$addclass = ($idx == count($mycoupons)-1)?' class="endsell"':'';
?>
	<tr>
		<td class="tline" align="center" <?=$addclass?>><? if(strlen($coupon['coupon_code'])>0){ ?><b><?=$coupon['coupon_code']?></b><?}else{?>&nbsp;<?}?></td>
		<td class="tline" style="padding-left:6px;"><?=$coupon['coupon_name']?></td>
		<td class="tline" align="center" <?=$addclass?>><?=$coupon_desc?><br /><a href="javascript:issue_coupon('<?=$coupon['coupon_code']?>')"><img src="/images/common/icon_download.gif" border="0" alt="쿠폰다운" /></a></td>
		<td class="tline" style="padding-left:6px;"><?=$target?></td>
		<td class="tline" style="padding-left:6px;"><?=$limit?></td>
		<td align="center" <?=$addclass?>><?=$range?></td>
	</tr>
<?		}
	}
?>
</table>
<div style="margin:0 auto; margin-top:20px; text-align:center;"><a href="javascript:window.close();"><img src="/images/common/bigview_btnclose.gif" border="0" alt="" /></a></div>
<br />

<? } ?>
</form>
<form name=couponissueform method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode value="">
<input type=hidden name=coupon_code value="">
</form>

<input type="hidden" name="basketTempList" id="basketTempList" value="">

<script language="javascript" type="text/javascript">
//orgSumPrice = parseInt('<?=$sumprice?>');
</script>
</body>
</html>