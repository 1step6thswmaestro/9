<?
if($num=strpos($body,"[ONE_CODEA_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$codeA_style=$s_tmp[2];
}
if($num=strpos($body,"[ONE_CODEB_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$codeB_style=$s_tmp[2];
}
if($num=strpos($body,"[ONE_CODEC_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$codeC_style=$s_tmp[2];
}
if($num=strpos($body,"[ONE_CODED_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$codeD_style=$s_tmp[2];
}

if($num=strpos($body,"[ONE_PRLIST_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$prlist_style=$s_tmp[2];
}

if(strlen($codeA_style)==0) $codeA_style="width:150px";
if(strlen($codeB_style)==0) $codeB_style="width:150px";
if(strlen($codeC_style)==0) $codeC_style="width:150px";
if(strlen($codeD_style)==0) $codeD_style="width:150px";
if(strlen($prlist_style)==0) $prlist_style="width:300px";





if(strpos($body,"[IFROYAL]")!=0) {
	$ifroyalnum=strpos($body,"[IFROYAL]");
	$endroyalnum=strpos($body,"[IFENDROYAL]");
	$mainroyal=substr($body,$ifroyalnum+9,$endroyalnum-$ifroyalnum-9);
	$body=substr($body,0,$ifroyalnum)."[ROYALVALUE]".substr($body,$endroyalnum+12);
}

if(strpos($body,"[IFBASKET]")!=0) {

	$ifbasketnum=strpos($body,"[IFBASKET]");
	$endbasketnum=strpos($body,"[IFENDBASKET]");
	$elsebasketnum=strpos($body,"[IFELSEBASKET]");

	$basketstartnum=strpos($body,"[FORBASKET]");
	$basketstopnum=strpos($body,"[FORENDBASKET]");
	$optionstartnum=strpos($body,"[IFOPTION]");
	$optionstopnum=strpos($body,"[IFENDOPTION]");

	// 장바구니 리스트
	$ifbasket=substr($body,$ifbasketnum+10,$basketstartnum-($ifbasketnum+10))."[BASKETVALUE]".substr($body,$basketstopnum+14,$elsebasketnum-($basketstopnum+14));

	// 장바구니 상품합계 0원
	$nobasket=substr($body,$elsebasketnum+14,$endbasketnum-$elsebasketnum-14);

	$optionbasket=substr($body,$optionstartnum+10,$optionstopnum-$optionstartnum-10);
	$mainbasket=substr($body,$basketstartnum,$optionstartnum-$basketstartnum)."[OPTIONVALUE]".substr($body,$optionstopnum+13,$basketstopnum-$optionstopnum+1);

	$assemblestartnum=strpos($mainbasket,"[IFASSEMBLE]");
	$assemblestopnum=strpos($mainbasket,"[IFENDASSEMBLE]");
	if($assemblestartnum>0) {
		$assemblebasket=substr($mainbasket,$assemblestartnum+12,$assemblestopnum-$assemblestartnum-12);
		$mainbasket=substr($mainbasket,0,$assemblestartnum)."[ASSEMBLEVALUE]".substr($mainbasket,$assemblestopnum+15);
	} else {
		$assemblebasket="";
	}

	$packageliststartnum=strpos($mainbasket,"[IFPACKAGELIST]");
	$packageliststopnum=strpos($mainbasket,"[IFENDPACKAGELIST]");
	if($packageliststartnum>0) {
		$packagelistbasket=substr($mainbasket,$packageliststartnum+15,$packageliststopnum-$packageliststartnum-15);
		$mainbasket=substr($mainbasket,0,$packageliststartnum)."[PACKAGELISTVALUE]".substr($mainbasket,$packageliststopnum+18);
	} else {
		$packagelistbasket="";
	}

	$packagestartnum=strpos($mainbasket,"[IFPACKAGE]");
	$packagestopnum=strpos($mainbasket,"[IFENDPACKAGE]");
	if($packagestartnum>0) {
		$packagebasket=substr($mainbasket,$packagestartnum+11,$packagestopnum-$packagestartnum-11);
		$mainbasket=substr($mainbasket,0,$packagestartnum)."[PACKAGEVALUE]".substr($mainbasket,$packagestopnum+14);
	} else {
		$packagebasket="";
	}



	$groupstartnum=strpos($mainbasket,"[BASKET_GROUPSTART]");
	$groupstopnum=strpos($mainbasket,"[BASKET_GROUPEND]");
	if($groupstartnum>0) {
		$groupbasket=substr($mainbasket,$groupstartnum,$groupstopnum-$groupstartnum+17);
		$mainbasket=substr($mainbasket,0,$groupstartnum)."[GROUPBASKETVALUE]".substr($mainbasket,$groupstopnum+17);
	} else {
		$groupbasket="";
	}

	$body=substr($body,0,$ifbasketnum)."[ORIGINALBASKET]".substr($body,$endbasketnum+13);
}

include("basket_text.php");


$pattern=array(
	"(\[ONE_START\])",
	"(\[ONE_CODEA((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[ONE_CODEB((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[ONE_CODEC((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[ONE_CODED((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[ONE_PRLIST((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[ONE_PRIMG\])",
	"(\[ONE_BASKET\])",
	"(\[ONE_END\])",
	"(\[BASKET_MSG\])",
	"(\[ORIGINALBASKET\])",
	"(\[BASKET_SHOPPING\])",
	"(\[BASKET_CLEAR\])",
	"(\[ROYALVALUE\])"
);
$replace=array($one_start,$one_codeA,$one_codeB,$one_codeC,$one_codeD,$one_prlist,$one_primg,"\"javascript:OneshotBasketIn()\"",$one_end,$basket_msg,$originalbasket,$basket_shopping,$basket_clear,$royalvalue);


	// 장바구니 선택상품 기능 =====================================
	if($basketItems['sumprice']==0) {
		$basket_order="\"javascript:alert('장바구니에 담긴 상품이 없습니다.(1)')\"";
		$basket_pester="\"javascript:alert('장바구니에 담긴 상품이 없습니다.(2)')\"";
		$basket_present="\"javascript:alert('장바구니에 담긴 상품이 없습니다.(3)')\"";
	} else if ($basketItems['sumprice']>=$_data->bank_miniprice) {
		$basket_order=$Dir.FrontDir."login.php?chUrl=".urlencode($Dir.FrontDir."order.php");
		if (strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted") {
			$basket_pester="\"javascript:chkPester();\"";
		}else{
			$basket_pester="\"javascript:check_login();\"";
		}
		$basket_present="\"javascript:chkPresent();\"";
	} else {
		$basket_order="\"javascript:alert('주문가능한 최소 금액은 ".number_format($_data->bank_miniprice)."원 입니다')\"";
		$basket_pester="\"javascript:alert('주문가능한 최소 금액은 ".number_format($_data->bank_miniprice)."원 입니다')\"";
		$basket_present="\"javascript:alert('주문가능한 최소 금액은 ".number_format($_data->bank_miniprice)."원 입니다')\"";
	}

	// 주문하기
	
	if( $nowOrdertype == "ordernow" OR $nowOrdertype == "" ) $basket_order="";
	array_push($pattern,'(\[BASKET_ORDER\])');
	array_push($replace,$basket_order);

	// 조르기
	if( $nowOrdertype == "pester" ) $basket_pester="";
	array_push($pattern,'(\[BASKET_PESTER\])');
	array_push($replace,$basket_pester);

	// 선물하기
	if( $nowOrdertype == "present" ) $basket_present="";
	array_push($pattern,'(\[BASKET_PRESENT\])');
	array_push($replace,$basket_present);



	// 장바구니 전체 합계 금액 =====================================
	array_push($pattern,'(\[BASKET_TOTPRICE\])');
	array_push($replace,number_format($basketItems['sumprice']));


	// 장바구니 상품 합계 금액 ???? =====================================
	array_push($pattern,'(\[BASKET_PRODUCTPRICE\])');
	array_push($replace,$basket_productprice);


	// 장바구니 합계 금액 =====================================
	array_push($pattern,'(\[BASKET_DELIPRICE\])');
	array_push($replace,number_format($basketItems['deli_price']));


	// 장바구니 적립금 합계 금액 =====================================
	array_push($pattern,'(\[BASKET_TOTRESERVE\])');
	array_push($replace,number_format($basketItems['reserve']));


	// 장바구니 VAT 합계 금액 =====================================
	array_push($pattern,'(\[BASKET_PRODUCTPRICEVAT\])');
	array_push($replace,number_format($basket_productpricevat));


	// 선택 삭제 버튼 ===========================================
	array_push($pattern,'(\[BASKET_SEL_DEL\])');
	array_push($replace,"javascript:CheckForm('del','sel');");


	// 등급 할인 정보 ===========================================
	// 회원그룹별 추가적립/추가할인 정책
	array_push($pattern,'(\[GROUP_DISCOUNT\])');
	array_push($replace,$groupMemberSale);


$body=preg_replace($pattern,$replace,$body);

echo $body;

?>

