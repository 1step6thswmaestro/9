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

if(true !== checkGroupUseCoupon($groupname)) _alert($groupname.' ȸ�� ����� ���� ����� �Ұ����մϴ�.','0');


if( $_REQUEST['offlinecoupon'] == "popup" ) {
	$onloadOfflinecouponAuthPop = " onload=\"offlinecoupon_auth();\"";
}


//���� ������ ���� ���
if($_REQUEST['mode']=="coupon" && strlen($_REQUEST['coupon_code'])==8){
	$onload = '';
	$sql = "SELECT * FROM tblcouponinfo ";
	$sql.= "WHERE coupon_code = '".$_REQUEST['coupon_code']."'";

	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		if($row->issue_tot_no>0 && $row->issue_tot_no<$row->issue_no+1) {
			$onload="<script>alert(\"��� ������ �߱޵Ǿ����ϴ�.\");</script>";
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

				$onload="<script>alert(\"�ش� ���� �߱��� �Ϸ�Ǿ����ϴ�.\\n\\n��ǰ �ֹ��� �ش� ������ ����Ͻ� �� �ֽ��ϴ�.\");</script>";
			} else {
				if($row->repeat_id=="Y") {	//������ ��߱��� �����ϴٸ�,,,,
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
					$onload="<script>alert(\"�ش� ���� �߱��� �Ϸ�Ǿ����ϴ�.\\n\\n��ǰ �ֹ��� �ش� ������ ����Ͻ� �� �ֽ��ϴ�.\");</script>";
				} else {
					$onload="<script>alert(\"�̹� ������ �߱޹����̽��ϴ�.\\n\\n�ش� ������ ��߱��� �Ұ����մϴ�.\");</script>";
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
<title>�������� ��ȸ �� ����</title>

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
var bank_only	= "N"; //���� ���� ������ ������ ���õ� ��� ������ ���� �� ������·θ� �����ؾ� �Ѵ�.
var giftUnUsed	= false; //����ǰ �Ұ� ���� ��� ����
var GroupDisUnUsed = false; // ȸ��������� �Ұ� ���� ��� ����

$j(document).ready(function(){

	// �ߺ�������� ����
	$j('.unlimitcouponselect').change(function(){
		calprice();
	});

	// ���ϻ������ ����
	$j('.limitcouponselect').change(function(){
		$this = $j(this);
		$j('.limitcouponselect').each(function(idx,el){
			if($j.trim($j($this).val()) != ''){
				if($j($this).attr('seq') != $j(el).attr('seq')){
					if($j(el).val() ==  $j($this).val()){
						alert('���� ���� ���� ��� �׸��� �ʱ�ȭ �˴ϴ�.');
						$j(el).val('');
					}
				}
			}
		})
		calprice();
	});

	// �ʱ�ȭ ����
	$j(".reset").click(function(){
		document.frm.reset();
		arrobj = [];
		//////////////////////
		$j("#total_discount_txt").html('0');
		$j("#total_payprice").html(number_format(orgSumPrice));
		basketTemp( 'default' );// ��ۺ� �ʱ�ȭ
	});

	$j('#total_sumprice').html(number_format(totalpay));
	$j('#total_payprice').html(number_format(totalpay));
});

// ���ó��
function calprice(){
	// �ʱ�ȭ
	discount = 0;
	reserve =0;
	arrobj = [];
	giftUnUsed = false;
	GroupDisUnUsed = false;
	var deli_price = $j('#default_deli_sumprice_org').val();
	var unUsedGiftcouponList='';
	var unUsedGroupDisCouponList='';

	var basketTempList = ''; // ��ۺ� �� ��� ����Ʈ


	$j("#moreMsg").html(""); //����ǰ �Ұ� ���� �޼���
	var etcapply_gift_temp = ''; // ����ǰ �Ұ� ���� �޼��� ������������Ʈ �ߺ� üũ

	$j("#moreMsg1").html(""); // ȸ��������� �Ұ� ���� �޼���
	var use_point_temp = ''; // ȸ��������� �Ұ� ���� �޼��� ������������Ʈ �ߺ� üũ

	// �������� ����Ʈ
	$j('.unlimitcouponselect option:selected, .limitcouponselect option:selected').each(function(idx,el){
		if($j.trim($j(el).val()) != ''){
			var tmp = dr = dc = 0;
			var seq = $j(el).parent().attr('seq');
			var oripay = parseInt($j("#step3_"+seq+"_price").val()); // ��ǰ ���� ����
			var saletype = parseInt($j(el).attr('sale_type')); // ����/���� Ÿ��
			var salemoney = parseInt($j(el).attr('sale_money')); // ����/���� �ݾ�/%
			var amount_floor = parseInt($j(el).attr('amount_floor')); // �ݾ����� 1:�Ͽ�/2:10��/3:���

			/*
				saletype
				1 : + % : ���� %
				2 : - % :  ���� %
				3 : + �� : ���� ��
				4 : - �� :  ���� ��
			*/

			if(saletype < 3 && salemoney >= 100){
				alert('���� ���� �Դϴ� �����ڿ��� ���� �ϼ���.');
				return false;
			}
			if(saletype < 3){
				// % ����
				po = 0;
				if(!isNaN(amount_floor) && amount_floor > 0 && amount_floor < 4) po += amount_floor;
				tmp = Math.floor(oripay*(salemoney/ 100) / Math.pow(10,po))*Math.pow(10,po);
			}else {
				// �ݾ�
				tmp = salemoney;
			}
			if(saletype%2 == 1){
				dr = tmp; // ����
			}else{
				dc = tmp; // ����
			}

			$j(el).data('dr',dr);
			$j(el).data('dc',dc);
			discount += dc; // ������
			reserve += dr; // ������


			//����ǰ �Ұ� ����
			if($j(el).attr('etcapply_gift') == "A"){
				if ( etcapply_gift_temp != $j(el).val() ) {
					etcapply_gift_temp = $j(el).val();
					unUsedGiftcouponList += "["+$j(el).val()+"] ";
				}
				$j("#moreMsg").html("<br><font color='red'>"+unUsedGiftcouponList+" �������� ����ǰ�� ���� �� �����ϴ�.</font>");
				giftUnUsed = true;
			}

			// ȸ��������� �Ұ� ����
			if( $j(el).attr('use_point') == 'A' ) {
				if ( use_point_temp != $j(el).val() ) {
					use_point_temp = $j(el).val();
					unUsedGroupDisCouponList += "["+$j(el).val()+"] ";
				}
				$j("#moreMsg1").html("<br><font color='blue'>"+unUsedGroupDisCouponList+" �������� ������� ������ ���� �� �����ϴ�.</font>");
				GroupDisUnUsed = true;
			}

			$j(el).attr('product',$j("#step3_"+seq+"_product").val()); //��ǰ�ڵ�
			$j(el).attr('opt1',$j("#step3_"+seq+"_product").attr('opt1')); //��ǰ �ɼ� 1 �ε��� �ڵ�
			$j(el).attr('opt2',$j("#step3_"+seq+"_product").attr('opt2')); //��ǰ �ɼ� 2 �ε��� �ڵ�
			$j(el).attr('optidxs',$j("#step3_"+seq+"_product").attr('optidxs')); //��ǰ �ɼ�s �ε��� �ڵ�
			arrobj.push($j(el));

			basketTempList += $j("#step3_"+seq+"_product").val()+"_"+$j("#step3_"+seq+"_product").attr('opt1')+"_"+$j("#step3_"+seq+"_product").attr('opt2')+'_'+$j(el).attr('optidxs')+"|"+dc+"-";
		}
	});

	// ��ۺ� �� ���
	basketTemp( basketTempList );

	$j("#basketTempList").val(basketTempList);

	$j("#total_discount_txt").html(number_format(discount));
	$j("#total_reserve_txt").html(number_format(reserve));
	$j("#total_payprice").html(number_format(orgSumPrice - discount));
}


// ���� ���� �ϱ�
function checkCoupon(){
	var couponlist = ""; // ���� ����Ʈ
	var dcpricelist = ""; // ���ξ� ����Ʈ
	var drpricelist = ""; // ������ ����Ʈ
	var couponproduct = ""; // ������� ��ǰ ����Ʈ (�����ڵ�_��ǰ�ڵ�_�ɼ�1idx_�ɼ�2idx)
	var couponBankOnly = ""; // if (���� ���� ������ ������ ���õ� ��� ) Y else N


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

	opener.document.getElementById("possible_gift_price_used").value = ( giftUnUsed ) ? "N" : "Y"; // ����ǰ �Ұ� �������
	opener.document.getElementById("possible_group_dis_used").value = ( GroupDisUnUsed ) ? "N" : "Y";  // ȸ����� ���� �ߺ� �Ұ� �������
	opener.document.getElementById("deliprice").value = $j('#basketTempReturn').val(); // ��ۺ�

	opener.document.getElementById("coupon_price").value = discount; // ������
	opener.document.getElementById("coupon_reserve").value = reserve; // ������

	opener.document.getElementById("basketTempList").value = $j("#basketTempList").val(); // ���� ����


	opener.solvPrice();

	window.close();
}

// ���
function cancelCoupon(){
	opener.resetCoupon();
	window.close();
}

// ���� �ٿ�ε�
function issue_coupon(coupon_code,productcode){
	document.couponissueform.mode.value="coupon";
	document.couponissueform.coupon_code.value=coupon_code;
	document.couponissueform.submit();
}



// �������� ���� ���
function offlinecoupon_auth () {
	window.open('/front/offlinecoupon_auth.php?reloadchk=no','OffLineCoupon','width=300,height=200');
}


// ���� ���� ���� ��ۺ� ����
// ex ) basketTemp( '002001000000000003_0_0|5000-002002000000000002_2_3|5000' );
//	��ǰ�ڵ�_�ɼ�1�ε���_�ɼ�2�ε���|���ΰ���-��ǰ�ڵ�_�ɼ�1�ε���_�ɼ�2�ε���|���ΰ���
// "-" : ��ǰ ����Ʈ ���� , "|" : ��ǰŰ|���ΰ��� ����
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
				//alert("�� ��۷� : "+result);
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
			<h1>���� ��� ���� ����</h1>
			<section class="explan">
				<h2>�� ������ȸ �� ����</h2>
				<p>���밡�� �� ������ Ȯ�� �� ������ �����ϰ� �����ϱ⸦ �����ø� ������ �����Ͽ� ��ǰ ���Ű� �����մϴ�.</p>
				<p class="emphasis1">�ϳ��� ��ǰ�� �ϳ��� ������ ��밡���մϴ�.</p>
				<p class="emphasis2">�ٿ�ε� ������ �ٿ�ε� �� ����Ͻ� �� �ֽ��ϴ�.</p>
				<p class="emphasis2">[�ߺ�]������ �� ��ǰ �̻� ��� ������ ���� �Դϴ�.</p>
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
									array_push($mycoupons,$coupon); // ���밡�� ���� ����Ʈ
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
									<span>��ǰ��</span>:<?=strip_tags($prname)?><br>
									<?if($product['optvalue'] != "" || $product['package_str'] != ""){?>
									<span>��ǰ�ɼ�</span>:<?=$product['optvalue']?><?=$product['package_str']?><br>
									<?}?>
									<span>��ǰ����</span>:<?=number_format($product['sellprice']-$product['group_discount'])?>��
								</div>
								<input type="hidden" name="step3_<?=$p_cnt?>_price" id="step3_<?=$p_cnt?>_price" value="<?=$product['realprice']?>"/>
								<input type="hidden" name="step3_<?=$p_cnt?>_product" id="step3_<?=$p_cnt?>_product" value="<?=$product['productcode']?>"/>
							</div>
						</div>
						
						<div class="coupon_type_box">
							<select name="lim_coupon_<?=$p_cnt?>" class="limitcouponselect" seq="<?=$p_cnt?>">
								<option value="">���ϻ������</option>
								<?
									if(_array($limitcoupons)){
										foreach($limitcoupons as $coupon){
								?>
											<option value="<?=$coupon['coupon_code']?>" sale_type="<?=$coupon['sale_type']?>" sale_money="<?=$coupon['sale_money']?>" amount_floor="<?=$coupon['amount_floor']?>"  discount="" etcapply_gift="<?=$coupon['etcapply_gift']?>" bank_only="<?=$coupon['bank_only']?>" order_limit="<?=$coupon['order_limit']?>" use_point="<?=$coupon['use_point']?>">[<?=$coupon['coupon_code']?>]<?=number_format(intval($coupon['sale_money'])).(($coupon['sale_type']<'3')?'%':'��').((intval($coupon['sale_type'])%2 == 1)?'����':'����')?></option>
								<?
										}
									} 
								?>
							</select>
							<select name="lim_coupon_<?=$p_cnt?>" class="unlimitcouponselect" seq="<?=$p_cnt?>">
								<option value="">�ߺ��������</option>
								<?	
									if(_array($unlimitcoupons)){
										foreach($unlimitcoupons as $coupon){
								?>
											<option value="<?=$coupon['coupon_code']?>" sale_type="<?=$coupon['sale_type']?>" sale_money="<?=$coupon['sale_money']?>" amount_floor="<?=$coupon['amount_floor']?>" discount="" etcapply_gift="<?=$coupon['etcapply_gift']?>" bank_only="<?=$coupon['bank_only']?>" order_limit="<?=$coupon['order_limit']?>" use_point="<?=$coupon['use_point']?>">[<?=$coupon['coupon_code']?>]<?=number_format(intval($coupon['sale_money'])).(($coupon['sale_type']<'3')?'%':'��').((intval($coupon['sale_type'])%2 == 1)?'����':'����')?></option>
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
							<p class="counter_left"><span>���� ���� �ݾ� : </span></p><p class="counter_right"><span id="total_reserve_txt">0</span> ��</p>
							<p class="counter_left"><span>��ǰ �ݾ��հ� : </span></p><p class="counter_right"><span id="total_sumprice"><?=number_format($sumprice)?></span> ��</p>
							<p class="counter_left"><span>���� �������� :</span></p><p class="counter_right"><span id="total_discount_txt">0</span> ��</p>
							<p class="counter_left emphasis1"><span>�������� �ݾ� :</span></p><p class="counter_right emphasis1"><span id="total_payprice"><?=number_format($sumprice)?></span> ��</p>
						</div>

						<div class="coupon_button btn_basic">
							<a href="#btn_sc" onClick="checkCoupon();return false;">�����ϱ�</a>
							<a href="#" class="reset">�ʱ�ȭ</a>
							<a href="#btn_sc"  onClick="cancelCoupon();return false;">����ϱ�</a>
						</div>
				</section>
			<?
				//$mycoupons = getMyCouponList();

				if(_array($ablecoupons) || _array($mycoupons)){ 
			?>
			<section class="coupon_list_wrap">
				<h2>�� ���밡�� ���� ���</h2>
				<p class="emphasis2">[�ߺ�]������ �� ��ǰ �̻� ��� ������ ���� �Դϴ�.</p>
				
				<div class="coupon_list">
					<ul>
					<?
						if(_array($mycoupons)){ 
							foreach($mycoupons as $idx=>$coupon){
								$range = ($coupon['date_start']>0)?substr($coupon['date_start'],0,4)."/".substr($coupon['date_start'],4,2)."/".substr($coupon['date_start'],6,2)." ~ ".substr($coupon['date_end'],0,4)."/".substr($coupon['date_end'],4,2)."/".substr($coupon['date_end'],6,2):date("Y/m/d")." ~ ".date("Y/m/d",mktime(0,0,0,date("m"),date("d")+abs($coupon['date_start']),date("Y")));
								$coupon_desc = number_format($coupon['sale_money']).($coupon['sale_type']<=2?"%":"��").($coupon['sale_type']%2==0?"����":"����");
								$limit = (_isInt($coupon['mini_price']))?number_format($coupon['mini_price']).'�� �̻�<br />���Ž�':'&nbsp;';

								$productList = usableProductOnCoupon($coupon['productcode']);


								$target = '		������ : ';
								if($coupon['vender'] > 0) $target .= '[������ : '.$coupon['venderid'].' ����]';
								if($coupon['use_con_type2']=="N") $target .='['.$productList.'] ����';
								else $target .= $productList;

								if($coupon['order_limit']=='N') {
									$coupon_order_limit_img = "order_unlimit.gif";
									$coupon_order_limit_alt = "�ߺ���������";
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
								<p><span class="list_left">������ : </span><span class="list_right <?=$coupon_limit_class?>">&nbsp;<?=$coupon_name?></span></p>
								<p><span class="list_left">�������� : </span><span class="list_right">&nbsp;<?=$coupon_desc?></span></p>
								<p><span class="list_left">������/ī�װ� : </span>
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
								<p><span class="list_left">������� : </span><span class="list_right">&nbsp;<?=strip_tags($limit)?></span></p>
								<p><span class="list_left">��ȿ�Ⱓ : </span><span class="list_right">&nbsp;<?=$range?></span></p>
							</div>
						</li>
					<?
							}
						}
						if(_array($ablecoupons)){ 
							foreach($ablecoupons as $idx=>$coupon){
								$range = ($coupon['date_start']>0)?substr($coupon['date_start'],0,4)."/".substr($coupon['date_start'],4,2)."/".substr($coupon['date_start'],6,2)." ~ ".substr($coupon['date_end'],0,4)."/".substr($coupon['date_end'],4,2)."/".substr($coupon['date_end'],6,2):date("Y/m/d")." ~ ".date("Y/m/d",mktime(0,0,0,date("m"),date("d")+abs($coupon['date_start']),date("Y")));
								$coupon_desc = number_format($coupon['sale_money']).($coupon['sale_type']<=2?"%":"��").($coupon['sale_type']%2==0?"����":"����");
								$limit = (_isInt($coupon['mini_price']))?number_format($coupon['mini_price']).'�� �̻�<br />���Ž�':'&nbsp;';

								$productList = usableProductOnCoupon($coupon['productcode']);
								$target = '		������ : ';
								if($coupon['vender'] > 0) $target .= '[������ : '.$coupon['venderid'].' ����]';
								if($coupon['use_con_type2']=="N") $target.'['.$productList.'] ����';
								else $target .= $productList;

								if($coupon['order_limit']=='N') {
									$coupon_order_limit_img = "order_unlimit.gif";
									$coupon_order_limit_alt = "�ߺ���������";
									$target .= "<img src=\"/images/common/order/".$coupon_order_limit_img."\" alt=\"".$coupon_order_limit_alt."\">";
								}
								$addclass = ($idx == count($mycoupons)-1)?' class="endsell"':'';
					?>
						<li>
							<div>
								<span>
									<?=$coupon['coupon_code']?>
									<a href="javascript:issue_coupon('<?=$coupon['coupon_code']?>')">�ٿ�ε�</a>
								</span>
							</div>
							<div>
								<p><span class="list_left">������ : </span><span class="list_right <?=$coupon_limit_class?>">&nbsp;<?=$coupon['coupon_name']?></span></p>
								<p><span class="list_left">�������� : </span><span class="list_right">&nbsp;<?=$coupon_desc?></span></p>
								<p><span class="list_left">������/ī�װ� : </span>
								<span class="list_right"><!-- <?//=$target?> --><!-- <a href="javascript:issue_coupon('<?=$coupon['coupon_code']?>')">�ٿ�ε�</a> -->
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
								<p><span class="list_left">������� : </span><span class="list_right">&nbsp;<?=strip_tags($limit)?></span></p>
								<p><span class="list_left">��ȿ�Ⱓ : </span><span class="list_right">&nbsp;<?=$range?></span></p>
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

<div class="coupon_closebtn_wrap"><a href="javascript:window.close();" class="btn_coupon_close">�ݱ�</a></div>

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