<?php
/**
* ERPia 주문 누락분 검증 연동
* 2012.05.29 code by madmirr@gmail.com
*/
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once('./erpia.class.php'); // 솔루션화 과정에서 라이브러 이동시는 경로 변경 해야 함.
$erpia = new erpia();
$erpia->_log('orderNoList Params',print_r($_REQUEST,true));
if($erpia->_auth($_REQUEST['pid'],$_REQUEST['pwd'])){
	$where = array();

	// erpia 연동용 bridge 테이블 데이터 갱신 - 미수집 데이터 추가
	$erpia->_syncBridge_Orders();	

	// 요청 기간이 있을 경우
	if(!empty($_REQUEST['sdate']) && preg_match('/^[0-9]{8}$/',$_REQUEST['sdate'])){ // 변경일 조건 있을 경우		
		array_push($where,'e.modifydate >="'.substr($_REQUEST['sdate'],0,4).'-'.substr($_REQUEST['sdate'],4,2).'-'.substr($_REQUEST['sdate'],6,2).'"');
	}
	
	if(!empty($_REQUEST['edate']) && preg_match('/^[0-9]{8}$/',$_REQUEST['edate'])){ // 변경일 조건 있을 경우		
		array_push($where,'e.modifydate <="'.substr($_REQUEST['edate'],0,4).'-'.substr($_REQUEST['edate'],4,2).'-'.substr($_REQUEST['edate'],6,2).'"');			
	}
		
	$query = "select ordercode from tblerpiaorder ";
			
	$ordby = '';	// 정렬을 사용 할 경우 해당 변수 내용 수정
	$where = (count($where) >0)?' where '.implode(' and ',$where):'';	
	//$limit = $erpia->_limitstr($_REQUEST['page'],$_REQUEST['pageCnt']);
	$limit = '';
	$groupby = ' group by ordercode';
	$query .= $where.$groupby.$ordby.$limit;

	$result = mysql_query($query,get_db_conn());
	$items = array();
	
	while($row = mysql_fetch_assoc($result)){
		array_push($items,$row['ordercode']);
	}	
	$erpia->_xml(array('orderNo'=>$items));		
}
?>