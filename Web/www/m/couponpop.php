<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/func.php");
include_once($Dir."lib/ext/order_func.php");
include_once($Dir."lib/ext/coupon_func.php");
include_once($Dir."m/inc/function.php");
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
	?>
	<script language="javascript" type="text/javascript">
		document.location.replace('/m/couponpop.php');
	</script>
	<?
	exit;
}


$basketItems = getBasketByArray();
//echo "<div style=\" height:500px; overflow:scroll;  border:2px solid #ff0000 ;  text-align:left;\">";
//_pr($basketItems);
//echo "</div>";

$productitems = array();
foreach($basketItems['vender'] as $vd=>$val){
	foreach($val['products'] as $idx=>$pd){
		if($pd['cateAuth']['coupon'] != 'Y') continue;
		//if(!_array($productitems[$pd['productcode']])) $productitems[$pd['productcode']] = array();
		$productitems[] = &$basketItems['vender'][$vd]['products'][$idx];
	}
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-cache" />
<title>할인쿠폰 조회 및 적용</title>

<link rel="stylesheet" href="./css/common.css" />

<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="./js/jquery-1.10.2.min.js"></script>

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
				if(!isNaN(amount_floor) && amount_floor > 0 && amount_floor < 4) po += amount_floor;
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

	opener.document.getElementById("possible_gift_price_used").value = ( giftUnUsed ) ? "N" : "Y"; // 사은품 불가 쿠폰사용
	opener.document.getElementById("possible_group_dis_used").value = ( GroupDisUnUsed ) ? "N" : "Y";  // 회원등급 할인 중복 불가 쿠폰사용
	opener.document.getElementById("deliprice").value = $j('#basketTempReturn').val(); // 배송비

	opener.document.getElementById("coupon_price").value = discount; // 총할인
	opener.document.getElementById("coupon_reserve").value = reserve; // 총적립

	opener.document.getElementById("basketTempList").value = $j("#basketTempList").val(); // 할인 정보


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
		var result = <?=$basketItems['deli_price']?>;
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
</style>
</head>

<body class="coupon" <?=$onloadOfflinecouponAuthPop?>>
	<article class="coupon_wrap">
		<form name="frm" action="">
			<h1>쿠폰 즉시 할인 적용</h1>
			<section class="explan">
				<h2>● 쿠폰조회 및 적용</h2>
				<p>적용가능 한 쿠폰을 확인 후 쿠폰을 선택하고 적용하기를 누르시면 쿠폰을 적용하여 상품 구매가 가능합니다.</p>
				<p class="emphasis1">하나의 상품에 하나의 쿠폰만 사용가능합니다.</p>
				<p class="emphasis2">다운로드 쿠폰은 다운로드 후 사용하실 수 있습니다.</p>
				<p class="emphasis2">[중복]쿠폰은 두 상품 이상에 사용 가능한 쿠폰 입니다.</p>
			</section>
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
					$_size = _getImageSize($product['tinyimage']['src']);
					$_wdith = $_size[width];
					$_height = $_size[height];

					if($_wdith >= $_height){
						$set_size = "width=60";
					}else{
						$set_size = "height=60";
					}

			?>
					<section class="coupon_select_wrap">
						<div class="product_box">
							<div class="img_box">
								<div class="inner_box">
									<img <?=$set_size?> src="<?=$product['tinyimage']['src']?>"/>
								</div>
							</div>
							<div class="text_box">
								<div class="inner_box">
									<?
										if(strlen($product['productname']) >= 24){
											$prname = substr($product['productname'], 0 ,24).'..';
										}else{
											$prname = $product['productname'];
										}
									?>
									<span>상품명</span>:<?=strip_tags($prname)?><br>
									<?if($product['optvalue'] != "" || $product['package_str'] != ""){?>
									<span>상품옵션</span>:<?=$product['optvalue']?><?=$product['package_str']?><br>
									<?}?>
									<span>상품가격</span>:<?=number_format($product['sellprice']-$product['group_discount'])?>원
								</div>
								<input type="hidden" name="step3_<?=$p_cnt?>_price" id="step3_<?=$p_cnt?>_price" value="<?=$product['realprice']?>"/>
								<input type="hidden" name="step3_<?=$p_cnt?>_product" id="step3_<?=$p_cnt?>_product" value="<?=$product['productcode']?>"/>
							</div>
						</div>
						
						<div class="coupon_type_box">
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
						</div>
					</section>
					<section class="coupon_counter_wrap">
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
						<div class="coupon_counter">
							<input type="hidden" name="step3_orgprice" id="step3_orgprice" value="<?=$sumprice?>"/>
							<input type="hidden" name="step3_discount" id="step3_discount" value="0"/>
							<input type="hidden" name="total_discount" value="0" />
							<p class="counter_left"><span>쿠폰 적립 금액 : </span></p><p class="counter_right"><span id="total_reserve_txt">0</span> 원</p>
							<p class="counter_left"><span>상품 금액합계 : </span></p><p class="counter_right"><span id="total_sumprice"><?=number_format($sumprice)?></span> 원</p>
							<p class="counter_left"><span>할인 쿠폰적용 :</span></p><p class="counter_right"><span id="total_discount_txt">0</span> 원</p>
							<p class="counter_left emphasis1"><span>최종결제 금액 :</span></p><p class="counter_right emphasis1"><span id="total_payprice"><?=number_format($sumprice)?></span> 원</p>
						</div>

						<div class="coupon_button btn_basic">
							<a href="#btn_sc" onClick="checkCoupon();return false;">적용하기</a>
							<a href="#" class="reset">초기화</a>
							<a href="#btn_sc"  onClick="cancelCoupon();return false;">취소하기</a>
						</div>
				</section>
			<?
				//$mycoupons = getMyCouponList();

				if(_array($ablecoupons) || _array($mycoupons)){ 
			?>
			<section class="coupon_list_wrap">
				<h2>● 적용가능 쿠폰 목록</h2>
				<p class="emphasis2">[중복]쿠폰은 두 상품 이상에 사용 가능한 쿠폰 입니다.</p>
				
				<div class="coupon_list">
					<ul>
					<?
						if(_array($mycoupons)){ 
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

								$addclass = ($idx == count($mycoupons)-1)?' class="endsell"':'';
					?>

						<li>
							<div>
							<?=$coupon['coupon_code']?>
							</div>
							<div>
								<?
									if(strlen($coupon['coupon_name']) >= 28){
										$coupon_name = substr($coupon['coupon_name'], 0, 28).'..';
									}else{
										$coupon_name = $coupon['coupon_name'];
									}
								?>
								<p><span class="list_left">쿠폰명 : </span><span class="list_right <?=$coupon_limit_class?>">&nbsp;<?=$coupon_name?></span></p>
								<p><span class="list_left">할인적립 : </span><span class="list_right">&nbsp;<?=$coupon_desc?></span></p>
								<p><span class="list_left">적용대상/카테고리 : </span>
								<span class="list_right"><!-- &nbsp;<?//=$target?> -->
								
								<?
									if(substr(trim($target),-1) == ','){
										$save_target = substr(trim($target),0,-1);
									}else{
										$save_target = trim($target);
									}
								
									$print_target = explode(',',$save_target);
									$loop=count($print_target);

									for($i=0;$i<$loop;$i++){
										$echo_target = explode(":",$print_target[$i]);
										
										if(mb_strlen(trim($echo_target[1]))>8){
											echo mb_substr(trim($echo_target[1]),0,8).'..';
										}else{
											echo $echo_target[1];
										}
										if($i>=1){
										echo ',</br>';
										}
									}
									
								?>
								</span></p>
								<p><span class="list_left">사용조건 : </span><span class="list_right">&nbsp;<?=strip_tags($limit)?></span></p>
								<p><span class="list_left">유효기간 : </span><span class="list_right">&nbsp;<?=$range?></span></p>
							</div>
						</li>
					<?
							}
						}
						if(_array($ablecoupons)){ 
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
								$addclass = ($idx == count($mycoupons)-1)?' class="endsell"':'';
					?>
						<li>
							<div>
								<span>
									<?=$coupon['coupon_code']?>
									<a href="javascript:issue_coupon('<?=$coupon['coupon_code']?>')">다운로드</a>
								</span>
							</div>
							<div>
								<p><span class="list_left">쿠폰명 : </span><span class="list_right <?=$coupon_limit_class?>">&nbsp;<?=$coupon['coupon_name']?></span></p>
								<p><span class="list_left">할인적립 : </span><span class="list_right">&nbsp;<?=$coupon_desc?></span></p>
								<p><span class="list_left">적용대상/카테고리 : </span>
								<span class="list_right"><!-- <?//=$target?> --><!-- <a href="javascript:issue_coupon('<?=$coupon['coupon_code']?>')">다운로드</a> -->
																<?
									if(substr(trim($target),-1) == ','){
										$save_target = substr(trim($target),0,-1);
									}else{
										$save_target = trim($target);
									}
								
									$print_target = explode(',',$save_target);
									$loop=count($print_target);

									for($i=0;$i<$loop;$i++){
										
										$echo_target = explode(":",$print_target[$i]);
										
										if(mb_strlen(trim($echo_target[1]))>8){
											echo mb_substr(trim($echo_target[1]),0,8).'..';
										}else{
											echo $echo_target[1];
										}
										if($i>=1){
										echo ',</br>';
										}
									}
									
								?>
								</span></p>
								<p><span class="list_left">사용조건 : </span><span class="list_right">&nbsp;<?=strip_tags($limit)?></span></p>
								<p><span class="list_left">유효기간 : </span><span class="list_right">&nbsp;<?=$range?></span></p>
							</div>
						</li>
					<?
							}
						}
					?>
					</ul>
				</div>
			</section>
			<? } ?>
		</form>
	</article>

<div class="coupon_closebtn_wrap"><a href="javascript:window.close();" class="btn_coupon_close">닫기</a></div>

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