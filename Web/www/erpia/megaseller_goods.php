<?php
/**
* 메가 셀러 상품 연동
* 2012.05.15 code by madmirr@gmail.com
*/
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once('./erpia.class.php'); // 솔루션화 과정에서 라이브러 이동시는 경로 변경 해야 함.
$_REQUEST['page'] = 1;
$_REQUEST['pageCnt'] = 10;
$erpia = new erpia();
$erpia->_log('goods Params',print_r($_REQUEST,true));
$where = array();
/**
* 패키지 상품 등은 제외 함.
*/
array_push($where,"package_num < '1'");
array_push($where,"assembleuse = 'N'");

//if(empty($_REQUEST['dateFrom']) || !preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',$_REQUEST['dateFrom'])) throw new InvalidArgumentException('조회 시작일시 형식 또는 공백 오류');
//else array_push($where,'p.modifydate >="'.$_REQUEST['dateFrom'].'"');		
	
if(!empty($_REQUEST['DateTo']) && preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',$_REQUEST['DateTo'])){ // 변경일 조건 있을 경우		
	array_push($where,'p.modifydate <="'.$_REQUEST['DateTo'].'"');
}

$query = "select p.*,b.brandname from tblproduct p left join tblproductbrand b on (b.bridx = p.brand) ";
// 정렬을 사용 할 경우 해당 변수 내용 수정
$ordby = ' order by p.modifydate asc';

$where = (count($where) >0)?' where '.implode(' and ',$where):'';
$limit = $erpia->_limitstr($_REQUEST['page'],$_REQUEST['pageCnt']);

$query .= $where.$ordby.$limit; 

$result = mysql_query($query,get_db_conn());

$items = array('adminCode'=>$erpia->_val('adminCode'),'maxInDate'=>date('Y-m-d H:i:s'),'contentsCnt'=>mysql_num_rows($result),'contentsList'=>array('goodsMasterList'=>array()));

while($row = mysql_fetch_assoc($result)){
	$item = array();
	$item['gJcode'] = $row['productcode'];
	$item['gName'] 	= $row['productname'];		
	$item['goodsDetailList'] = array();
	$stts = ($row['display'] != 'Y')?'04':'01';
	$qty = (strlen(trim($row['quantity'])) > 0)?intval($row['quantity']):99999;
	
	if(strlen(trim($row['option1'])) > 0){
		$option1 = explode(',',$row['option1']);
		$option2 = explode(',',$row['option2']);
		$option_price = explode(',',$row['option_price']);
		$optqty = (!empty($row['option_quantity']))?explode(',',$row['option_quantity']):NULL;
		
		$option = array('b2cCode'=>$row['productcode'],'b2cStand'=>'','b2cPrice'=>'','erpia_barcode'=>'','erpiaStand'=>'','erpiaPrice'=>'','stts'=>$stts,'jegoQty'=>$qty);
		
		for($i=1;$i<count($option1);$i++){				
			$option['b2cPrice'] = preg_replace('[^0-9]','',$option_price[$i-1]);
			
			if(count($option2) > 0){				
				for($j=1;$j<count($option2);$j++){
					$option['b2cStand'] = trim($option1[$i]).'||'.trim($option2[$j]);						
					if(!is_null($optqty)) $option['jegoQty'] = $optqty[$i+($j-1)*10];
					array_push($item['goodsDetailList'],$option);
				}
			}else{
				$option['b2cStand'] = trim($option1[$i]);
				if(!is_null($optqty)) $option['jegoQty'] = $optqty[$i];
				array_push($item['option'],$option);
			}
		}
	}else{
		/*
		$option = array();
		$option['stand'] = '';
		$option['interAmt'] = $row['sellprice'];
		if(intval($row['consumerprice']) >0) $option['soAmt'] = intval($row['consumerprice']);
		if(intval($row['buyprice']) >0) $option['ipAmt'] = intval($row['buyprice']);
		if(intval($row['reserve'])>0){
			if($row['reservetype'] = 'Y') $option['point'] = intval($row['sellprice'])*intval($row['reserve'])/100;
			else if($row['reservetype'] == 'N') $option['point'] = intval($row['reserve']);
		}
		array_push($item['option'],$option);
		*/
	}
	/*
	*		
	$item['goodsDetailList']['b2cCode'] = $row['productcode'];
	*/		
	if(!empty($rwo['brandname'])) $item['gBrandname'] = $row['brandname'];           
	if(!empty($rwo['model'])) $item['gModel'] = $row['model'];           		
	$item['gOrigin'] = (!empty($row['madein']))?$row['madein']:' ';
	$item['gMfname'] = (!empty($row['production']))?$row['production']:' ';
	/*
	$item['gMkdate'] = 
	$item['gExpiredate'] = 
	*/
	$item['gKeyword'] = $row['keyword'];
	$item['gState'] = '01'; // 솔루션에 상품 상태 관련 값 없어서 전부 신품으로 처리 01 신품 , 02 중고
	$item['imgLarge'] = $_ShopInfo->getShopurl().DataDir.'shopimages/product/'.$row['maximage'];
	$item['imgList'] = $_ShopInfo->getShopurl().DataDir.'shopimages/product/'.$row['tinyimage'];
	$item['imgAdd1'] = $_ShopInfo->getShopurl().DataDir.'shopimages/product/'.$row['minimage'];
	/*
	$item['imgAdd2'] =
	$item['imgAdd3'] =
	$item['imgAdd4'] =
	*/
	$item['ginfo'] = htmlspecialchars($row['content']);
	$item['gSprice'] = 
	$item['gPrice'] = 
	$item['gGprice'] = 
	//$item['gWprice'] = 
	$item['gPqty'] = 
	$item['gTaxgu'] = 
	$item['paId'] = '';
	$item['paName'] = '';
	$item['jCategoricode'] =
	$item['jCategoriname'] = 
	$item['dCategoricode'] = '';
	$item['dCategoriname'] = '';
	$item['remark'] = '';
	$item['gStcode'] = '';
	$item['yearcode'] = '';
	$item['season'] = '';
	$item['prdmat'] = '';
	$item['prdkg'] = '';
	$item['sizeinfo'] = '';
	$item['laundrymethod'] = '';
	$item['gPreName'] = '';
	$item['gPostName'] = '';
	$item['modelItem'] = '';
	$item['hsCode'] = '';
	$item['certifyNo'] = '';
	$item['certifyDate'] = '';
	$item['safetyGubun'] = '';
	$item['safetyCenter'] = '';
	$item['imgChangeYn'] = 'N';
	$item['addText'] = $row['addcode'];
	$item['freeGift'] = '';
	$item['standType'] = 'E';
	$item['txtWishKeyword1'] = $row['keyword'];;
	$item['txtWishKeyword2'] = '';
	$item['txtWishKeyword3'] = '';
	$item['txtWishKeyword4'] = '';
	$item['txtWishKeyword5'] = '';		
	$item['addStandAdYN'] = 'N';
	$item['addStandAd'] = 'N';
	$item['addStandAlYN'] = 'N';
	$item['addStandAl'] = 'N';
	array_push($items['contentsList']['goodsMasterList'],$item);
}
$erpia->_xml($items);
?>