<?php
/**
* ERPia 상품 연동
* 2012.05.15 code by madmirr@gmail.com
*/
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once('./erpia.class.php'); // 솔루션화 과정에서 라이브러 이동시는 경로 변경 해야 함.
$erpia = new erpia();
	$erpia->_log('goods Params',print_r($_REQUEST,true));
if($erpia->_auth($_REQUEST['pid'],$_REQUEST['pwd'])){
	$where = array();
	
	if(!empty($_REQUEST['goodscode']) && preg_match('/^[0-9]{18}$/',$_REQUEST['goodscode'])){ // 상품 코드로 조회 (1개만)
		array_push($where,'p.productcode="'.$_REQUEST['goodscode'].'"');
		//$limit = ' limit 1';
		$_REQUEST['page'] = NULL;
	}else{
		/*
		if(!empty($_REQUEST['pageCnt']) && !empty($_REQUEST['page']) && is_numeric($_REQUEST['pageCnt']) && is_numeric($_REQUEST['page'])){ // 페이지 카운트 사용시
			$pageCnt = intval($_REQUEST['pageCnt']);
			$page = intval($_REQUEST['page']);		
			$limit = ' limit '.(($page-1)*$pageCnt).','.$pageCnt;
		}else{
			$limit = '';
		}
		*/
		if(empty($_REQUEST['page'])) $_REQUEST['page'] = 1;

		if(empty($_REQUEST['pageCnt'])) $_REQUEST['pageCnt'] = 100;
		if(!empty($_REQUEST['sdate']) && preg_match('/^[0-9]+$/',$_REQUEST['sdate'])){ // 변경일 조건 있을 경우		
			array_push($where,'p.modifydate >="'.substr($_REQUEST['sdate'],0,4).'-'.substr($_REQUEST['sdate'],4,2).'-'.substr($_REQUEST['sdate'],6,2).' '.substr($_REQUEST['sdate'],8,2).'"');		
		}		
	}
	
	$query = "select p.*,b.brandname from tblproduct p left join tblproductbrand b on (b.bridx = p.brand) ";	
	
	// 정렬을 사용 할 경우 해당 변수 내용 수정
	//$ordby = ' order by modifydate asc';
	$ordby = '';
	
	$where = (count($where) >0)?' where '.implode(' and ',$where):'';
	$limit = $erpia->_limitstr($_REQUEST['page'],$_REQUEST['pageCnt']);
	
	$query .= $where.$ordby.$limit; 	
	$result = mysql_query($query,get_db_conn());
	$items = array();
	
	while($row = mysql_fetch_assoc($result)){
		$item = array();
		$item['code'] = $row['productcode'];
		$item['name'] = $row['productname'];
		
		if(!empty($row['production'])) $item['making'] =$row['production'];
		if(!empty($row['brandname'])) $item['brand'] =$row['brandname'];
		$item['date'] = str_replace('-','',substr($row['modifydate'],0,10));
		$item['url'] = $_ShopInfo->getShopurl().FrontDir.'productdetail.php?productcode='.$row['productcode'];
		if(strlen(trim($row['tinyimage'])) > 0) $item['imgUrl'] = $_ShopInfo->getShopurl().DataDir.'shopimages/product/'.$row['tinyimage'];
		
		$item['option'] = array();
		if(strlen(trim($row['option1'])) > 0){
			$option1 = explode(',',$row['option1']);
			$option2 = explode(',',$row['option2']);
			$option_price = explode(',',$row['option_price']);
			
			for($i=1;$i<count($option1);$i++){
				$option = array('stand'=>'','interAmt'=>'');
				
				$option['interAmt'] = preg_replace('[^0-9]','',$option_price[$i-1]);
				
				if(intval($row['reserve'])>0){
					if($row['reservetype'] = 'Y') $option['point'] = intval($option_price[$i-1])*intval($row['reserve'])/100;
					else if($row['reservetype'] == 'N') $option['point'] = intval($row['reserve']);
				}
				
				if(count($option2) > 0){				
					for($j=1;$j<count($option2);$j++){
						$option['stand'] = trim($option1[$i]).'||'.trim($option2[$j]);
						array_push($item['option'],$option);
					}
				}else{
					$option['stand'] = trim($option1[$i]);
					array_push($item['option'],$option);
				}
			}
		}else{
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
		}
		array_push($items,$item);
	}	
	$erpia->_xml(array('info'=>$items));	
}
?>