<?php
/**
* ERPia 주문 연동
* 2012.05.23 code by madmirr@gmail.com
*/
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once('./erpia.class.php'); // 솔루션화 과정에서 라이브러 이동시는 경로 변경 해야 함.
$erpia = new erpia();
$erpia->_log('cancel Params',print_r($_REQUEST,true));
if($erpia->_auth($_REQUEST['pid'],$_REQUEST['pwd'])){
	$where = array();	
	// erpia 연동용 bridge 테이블 데이터 갱신 - 미수집 데이터 추가
	$erpia->_syncBridge_Orders();
	
	// 요청 파라메터중 orderno 의 우선 순위가 가장 높음
	if(!empty($_REQUEST['orderno']) && preg_match('/^[0-9A-Z]+$/',$_REQUEST['orderno'])){ // 상품 코드로 조회 (1개만)
		array_push($where,'p.ordercode="'.$_REQUEST['orderno'].'"');
		$_REQUEST['page'] = NULL;
		//$limit = ' limit 1';
	}else{	
		// 요청 기간이 있을 경우
		if(!empty($_REQUEST['sdate']) && preg_match('/^[0-9]{8}$/',$_REQUEST['sdate'])){ // 변경일 조건 있을 경우		
			array_push($where,'e.modifydate >="'.substr($_REQUEST['sdate'],0,4).'-'.substr($_REQUEST['sdate'],4,2).'-'.substr($_REQUEST['sdate'],6,2).'"');
		}
		
		if(!empty($_REQUEST['edate']) && preg_match('/^[0-9]{8}$/',$_REQUEST['edate'])){ // 변경일 조건 있을 경우		
			array_push($where,'e.modifydate <="'.substr($_REQUEST['edate'],0,4).'-'.substr($_REQUEST['edate'],4,2).'-'.substr($_REQUEST['edate'],6,2).'"');			
		}		
		array_push($where,"p.deli_gbn='C'");
	}
	
	array_push($where," substr(e.productcode,1,3) not in ('COU','999')");
	$where = " where ".implode(' and ',$where);
	
	$query = "select p.*,e.modifydate,o.*,substr(o.paymethod,1,1) as paymethod from tblorderproduct p left join tblerpiaorder e using (vender,ordercode,tempkey,productcode,opt1_name,opt2_name,package_idx,assemble_idx ) left join tblorderinfo o on o.ordercode=e.ordercode ".$where;
	
	$result = mysql_query($query,get_db_conn());
	
	$items = array();
	while($row = mysql_fetch_assoc($result)){
		$item = array('gubun'=>'C'); // 구분은 취소만 처리 - 솔루션에서 미지원
		
		if(in_array($row['paymethod'],array('B','O','Q'))){
			$item['marketStts'] = (preg_match('/^[0-9]{8}X$/',$row['bank_date']))?'2':'0';
		}else{
			$item['marketStts'] = ($row['pay_admin_proc'] == 'C')?'2':'0';
		}
		$item['Jumun_No'] = $row['ordercode'];
		$item['Jumun_Seq'] = $row['Gseq'];
		$item['outCode'] = $row['productcode'];
		$item['outName'] = $row['productname'];		
		if(!empty($row['opt1_name'])) $item['outStand'] = trim(end(explode(':',$row['opt1_name'])));
		if(!empty($row['opt2_name'])) $item['outStand'] .= '::'.trim(end(explode(':',$row['opt2_name'])));
		// 현 솔루션에서는 일괄 취소만 가능하기때문에 원수량 과 취소 요청 수량은 동일
		$item['oQty'] = $item['Qty'] = $row['quantity'];
		$item['panAmt'] = $row['price'];
		// $item['sDate'] = '';
		// $item['eDate'] = '';
		// $item['yDate'] = '';
		 $item['jName'] = $row['sender_name'];
		// $item['jHp'] = '';
		// $item['jTel'] = '';
		// $item['jPost'] = '';
		// $item['jAddr'] = '';
		
		$item['sName'] = $row['receiver_name'];
		
		if(preg_match('/^(010|011|016|017|019)/',$row['receiver_tel1'])){
			$item['sHp'] = $row['receiver_tel1'];
		}else if(!empty($row['receiver_tel1'])){
			$item['sTel'] = $row['receiver_tel1'];		
		}		
		if(empty($item['sHp']) && preg_match('/^(010|011|016|017|019)/',$row['receiver_tel2'])){
			$item['sHp'] = $row['receiver_tel2'];
		}else if(empty($item['sTel']) && !empty($row['receiver_tel2'])){
			$item['sTel'] = $row['receiver_tel2'];
		}		
		
		$address = eregi_replace(array("\r","\n")," ",trim($row['receiver_addr']));
		$pos=strpos($address,"주소");		
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+7);
		}
		$item['sPost'] = ereg_replace("우편번호 : ","",$post);
		$item['sAddr'] = $address;
		$item['jId'] = (substr($row['ordercode'],-1,1) != 'X')?$row['id']:'';
		$item['email'] = $row['sender_email'];
	//	$item['Remk'] = '';
	//	$item['RemkDetail']='';
	//	$item['RemkSpecial'] = '';
		array_push($items,$item);
	}	
	$erpia->_log('export st',$_SERVER['SCRIPT_FILENAME']);
	$erpia->_xml(array('info'=>$items));
	$erpia->_log('export ed',$_SERVER['SCRIPT_FILENAME']);
	exit;
}
?>