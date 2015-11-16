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

$erpia->_log('order Params',print_r($_REQUEST,true));
if($erpia->_auth($_REQUEST['pid'],$_REQUEST['pwd'])){
	$where = array();
	$where2 = array();
	// erpia 연동용 bridge 테이블 데이터 갱신 - 미수집 데이터 추가
	$erpia->_syncBridge_Orders();
	
	// 요청 파라메터중 orderno 의 우선 순위가 가장 높음
	if(!empty($_REQUEST['orderno']) && preg_match('/^[0-9A-Z]+$/',$_REQUEST['orderno'])){ // 상품 코드로 조회 (1개만)
		array_push($where,'o.ordercode="'.$_REQUEST['orderno'].'"');
		$_REQUEST['page'] = NULL;
		//$limit = ' limit 1';
	}else{	
		if(empty($_REQUEST['page'])) $_REQUEST['page'] = 1;
		if(empty($_REQUEST['pageCnt'])) $_REQUEST['pageCnt'] = 100;	
		if(!empty($_REQUEST['stage'])){// 상태별 요청				
			if($_REQUEST['stage'] == '10'){ // 입금전
				array_push($where,"((MID(o.paymethod,1,1) IN ('B','O','Q') AND (o.bank_date IS NULL OR o.bank_date='')) OR (MID(o.paymethod,1,1) IN ('C','P','M','V') AND o.pay_flag!='0000' AND o.pay_admin_proc='C'))");
			}else{
				array_push($where,"((MID(o.paymethod,1,1) IN ('B','O','Q') AND LENGTH(o.bank_date)=14) OR (MID(o.paymethod,1,1) IN ('C','P','M','V') AND o.pay_admin_proc!='C' AND o.pay_flag='0000'))");
				array_push($where,"o.deli_gbn not in ('D','E','C','R')"); // 취소요청,환불대기,주문취소,반송 정보는 정상주문이 아니기때문에 수집 하지 않음.
				switch($_REQUEST['stage']){				
					case '30': // 배송전
						array_push($where,"o.deli_gbn='S'");
						array_push($where,"p.dcnt < 0");
						break;
					case '40': // 배송중 - 현 겟몰 기능에 배송중에 관한 기능이 없음.
						array_push($where,"o.deli_gbn in ('S','H')");
						array_push($where2,"q.dcnt > 0"); // 배송 준비 에서 배송 완료 상태 이전에 송장 번호가 있을 경우를 배송중으로 처리하기로 함.
						break;
					case '50': // 배송완료(배송완료의 경우 기간 저정된 기간에 의해 표시
						array_push($where,"o.deli_gbn='Y'");
						break;
					case '60': // 주문완료(주문완료의 경우, 기간 지정된 기간에 의해 표시) - 겟몰 솔루션에서 별도 주문 완료 처리가 없음.
						break;
					case '20': // 입금완료(=발주확인 단계, 즉, 보내기로 확정된 데이터)
						array_push($where,"o.deli_gbn='N'");
						break;
					default:
						break;
				}
			}
		}
		// 요청 기간이 있을 경우
		if(!empty($_REQUEST['sdate']) && preg_match('/^[0-9]+$/',$_REQUEST['sdate'])){ // 변경일 조건 있을 경우		
			//array_push($where,'e.modifydate >="'.substr($_REQUEST['sdate'],0,4).'-'.substr($_REQUEST['sdate'],4,2).'-'.substr($_REQUEST['sdate'],6,2).' '.substr($_REQUEST['sdate'],8,2).'"');		
			//array_push($where,'substr(e.ordercode,1,10) >="'.substr($_REQUEST['sdate'],0,10).'"');		
			array_push($where,'(substr(e.ordercode,1,10) >="'.substr($_REQUEST['sdate'],0,10).'" || e.modifydate >="'.substr($_REQUEST['sdate'],0,4).'-'.substr($_REQUEST['sdate'],4,2).'-'.substr($_REQUEST['sdate'],6,2).' '.substr($_REQUEST['sdate'],8,2).'")');		
		}
	}
	
	$query = "select o.*,m.home_post,m.home_addr,m.memo,count(p.deli_num) from tblerpiaorder e left join tblorderproduct p on(p.vender = e.vender and p.ordercode = e.ordercode and e.tempkey=p.tempkey and e.productcode=p.productcode and e.opt1_name=p.opt1_name and e.opt2_name=p.opt2_name and e.package_idx=p.package_idx and e.assemble_idx=p.assemble_idx and p.deli_gbn in ('S','Y')  and length(p.deli_num) > 3 and SUBSTR(p.productcode,1,3) not in ('COU','999')) left join tblorderinfo o on (e.ordercode = o.ordercode) left join tblmember m on (m.id = o.id)";
			
	$ordby = '';	// 정렬을 사용 할 경우 해당 변수 내용 수정
	$where = (count($where) >0)?' where '.implode(' and ',$where):'';
	$limit = $erpia->_limitstr($_REQUEST['page'],$_REQUEST['pageCnt']);
	$groupby = ' group by ordercode';
	$query .= $where.$ordby.$groupby; 
	
	if(count($where2) >0) $query = 'select * from ('.$query	.') q where '.implode(' and ',$where2);	
	$query .= $limit;
	
	$result = mysql_query($query,get_db_conn());
	$items = array();
	
	while($row = mysql_fetch_assoc($result)){
		$item['orderNo'] = $row['ordercode'];		
		$item['Jstate'] = '정상';
		$item['Jprocess'] = '';	 // 실제 값 대입은 하위 주문 정보 처리 하면서 넣음.
		$item['Jdate'] = substr($row['ordercode'],0,4).'-'.substr($row['ordercode'],4,2).'-'.substr($row['ordercode'],6,2);		
//		$item['JmeachulDate'] = '';
		$item['Jname'] = $row['sender_name'];		
		$item['Jemail'] = $row['sender_email'];		
		if(substr($row['ordercode'],-1,1) != 'X' && substr($row['id'],0,1) != 'X'){ // 회원일 경우
			$item['Jid'] = $row['id'];
			$item['Jtel'] = $row['sender_tel'];		
			$item['Jpost'] = substr($row['home_post'],0,3).'-'.substr($row['home_post'],3,3);		
			$item['Jaddr'] = str_replace('=','',$row['home_addr']);
		}
				
		$item['Sname'] = $row['receiver_name'];
		if(preg_match('/^(010|011|016|017|019)/',$row['receiver_tel1'])){
			$item['Shp'] = $row['receiver_tel1'];
		}else if(!empty($row['receiver_tel1'])){
			$item['Stel'] = $row['receiver_tel1'];		
		}		
		if(empty($item['Shp']) && preg_match('/^(010|011|016|017|019)/',$row['receiver_tel2'])){
			$item['Shp'] = $row['receiver_tel2'];
		}else if(empty($item['Stel']) && !empty($row['receiver_tel2'])){
			$item['Stel'] = $row['receiver_tel2'];
		}		
		if(empty($item['Stel'])) $item['Stel'] = $item['Shp'];
		
		$address = eregi_replace(array("\r","\n")," ",trim($row['receiver_addr']));
		$pos=strpos($address,"주소");
		
		if ($pos>0) {
			$post = trim(substr($address,0,$pos));
			$address = substr($address,$pos+7);
		}
		$item['Spost'] = ereg_replace("우편번호 : ","",$post);
		$item['Saddr'] = $address;
		$item['Jpath'] = '';		
		$item['Jbigo'] = '';		
		$item['JmeaSano'] = '';
		
		/**
		* 상품 정보 정리
		*/
		$squery = "select * from tblorderproduct p left join tblerpiaorder e using (vender,ordercode,tempkey,productcode,opt1_name,opt2_name,package_idx,assemble_idx ) where ordercode='".$row['ordercode']."' and substr(productcode,1,3) not in ('COU','999') order by assemble_info asc, package_idx asc";
		$sresult = mysql_query($squery,get_db_conn());
		$resultCnt = mysql_num_rows($sresult);
		$item['GoodsInfo'] = array(); 
		$deli_com = '';
		$Gseq = 1;
		while($prow = mysql_fetch_assoc($sresult)){
			//$GoodsInfo = array('Gseq'=>$prow['Gseq']);	
			$GoodsInfo = array('Gseq'=>$Gseq++);				
			$GoodsInfo['Gtype'] = '';	 // 추가 상품일 경우 A
			
			$GoodsInfo['Gcode'] = $prow['productcode'];	
			// $item['GERPiaCode'] = '';
			$GoodsInfo['Gname'] = $prow['productname'];	
			$GoodsInfo['Gqty'] = $prow['quantity'];	
			$GoodsInfo['Gdan'] = $prow['price'];	
			// $item['ChangGoCode'] ='';	
			$GoodsInfo['Gstate'] = $item['Jstate'];	 // 현 솔루션에서는 상품 개별로 교환 또는 취소, 반품 등이 불가함.
			if(empty($deli_com) && !empty($prow['deli_com'])) $deli_com = $prow['deli_com'];
			
			if(strlen(str_replace("","",str_replace(array(":","="),"",$prow['assemble_info'])))>0){	
				
				$assemble_infoall_exp = explode('=',$prow['assemble_info']);
				if(intval($prow['package_idx']) && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))>0){
					$package_info_exp = explode(":", $assemble_infoall_exp[0]);
					//$package_name = $package_info_exp[3];
					//$package_price = $package_info_exp[2];
					
					$GoodsInfo['gpName'] = $package_info_exp[3];
					$GoodsInfo['gpSeq'] = intval($prow['package_idx']);
				
					array_push($item['GoodsInfo'],$GoodsInfo);
					
					$packageInfo = array_combine(explode('',$package_info_exp[0]),explode('',$package_info_exp[0]));
					$GoodsInfo['Gdan'] = 0;
					
					foreach($packageInfo as $product_code=>$productName){
						$GoodsInfo['Gseq'] = $Gseq++;
						$GoodsInfo['Gcode'] = $product_code;	
						$GoodsInfo['Gname'] = $productName;
					}			
				}
			}else{				
				// $item['Ggong'] = '';	
				//$GoodsInfo['Gstand'] = $prow['opt1_name'].' '.$prow['opt2_name']; 
				if(!empty($prow['opt1_name'])) $GoodsInfo['Gstand'] = trim(end(explode(':',$prow['opt1_name'])));
				if(!empty($prow['opt2_name'])) $GoodsInfo['Gstand'] .= '||'.trim(end(explode(':',$prow['opt2_name'])));
				/*
				$GoodsInfo['scmSano'] = '';	
				$GoodsInfo['scmAmt'] = 0;	
				$GoodsInfo['yDate'] = '';			
				$GoodsInfo['taxfree'] = '';	 // 비과세 - 현솔루션 상에는 없음
				$GoodsInfo['Sname'] = '';	
				$GoodsInfo['Shp'] = '';	
				$GoodsInfo['Stel'] = '';	ccc
				$GoodsInfo['Spost'] = '';
				$GoodsInfo['Saddr'] = '';
				$GoodsInfo['Bbigo'] = '';
				$GoodsInfo['gpName'] = '';
				$GoodsInfo['gpSeq'] = '';
				$GoodsInfo['gpQty'] = '';
				*/
				
				array_push($item['GoodsInfo'],$GoodsInfo);
			}
			
		}	
		
		// 입금 관련 정보 정리		
		$paymethod = substr($row['paymethod'],0,1);
		if(in_array($paymethod,array('B','O','Q'))){ // 무통장, 가상계좌, 가상계좌 에스크로
			$item['AcctInfo']['Atype'] = ($paymethod!='Q')?'통장':'에스크로';
			$item['AcctInfo']['Adate'] = (strlen($row['bank_date']) == 14)?substr($row['bank_date'],0,4).'-'.substr($row['bank_date'],4,2).'-'.substr($row['bank_date'],6,2):'';
		}else{
			switch(substr($row['paymethod'],0,1)){
				case 'M': // 핸드폰
					$item['AcctInfo']['Atype'] = '핸드폰';
					break;
				case 'P': // 매매보호 신용카드
				case 'C': //일반 신용카드
					$item['AcctInfo']['Atype'] = '카드';
					break;
				case 'V': //실시간 계좌 이체
					$item['AcctInfo']['Atype'] = '통장';
					break;
			}
			$item['AcctInfo']['Adate'] = ($row['pay_admin_proc'] != 'C' && $row['pay_flag'] != '0000')?$item['Jdate']:'';
		}
		
		
	//	입금전 / 입금완료 / 배송전 / 배송중 / 배송완료 / 주문완료
		
		if(empty($item['AcctInfo']['Adate'])) $item['Jprocess'] = '입금전';
		else{
			$item['AcctInfo']['Aamt'] = $row['price'];
			switch($row['deli_gbn']){				
					case 'S': // 배송전						
					case 'H': // 배송중 - 현 겟몰 기능에 배송중에 관한 기능이 없음.
						$item['Jprocess'] = ($row['dcnt'] > 0) ?'배송중':'배송전';
						break;
					case 'Y': // 배송완료(배송완료의 경우 기간 저정된 기간에 의해 표시
						$item['Jprocess'] ='배송완료';
						break;
					case 'N': // 입금완료(=발주확인 단계, 즉, 보내기로 확정된 데이터)
					default:
						$item['Jprocess'] = '입금완료';
						break;
				}
		}
		
		$item['Bcode'] = $erpia->_delyCodeErpia($deli_com);
//		$item['Btype'] = $row['Btype'];		
//		$item['Bsize'] = $row['Bsize'];		
//		$item['Bbox'] = $row['Bbox'];		

		$item['Bamt'] = $row['deli_price'];		
		$item['Bbigo'] = $row['receiver_message'];//$row['Bbigo'];

//		$item['Ybill'] = $row['Ybill'];		
//		$item['Yger'] = $row['Yger'];		
//		$item['Tstate'] = $row['Tstate'];		
//		$item['Tsano'] = $row['Tsano'];		
//		$item['Tname'] = $row['Tname'];		
//		$item['Tceo'] = $row['Tceo'];		
//		$item['Ttype'] = $row['Ttype'];		
//		$item['Titem'] = $row['Titem'];		
//		$item['Taddr'] = $row['Taddr'];		
//		$item['Tgoods'] = $row['Tgoods'];		
//		$item['Tbigo'] = $row['Tbigo'];		
//		$item['Tdate'] = $row['Tdate'];		
//		$item['Tamt'] = $row['Tamt'];		
//		$item['Htax'] = $row['Htax'];		
//		$item['Htype'] = $row['Htype'];		
//		$item['Hnum'] = $row['Hnum'];		
//		$item['Hname'] = $row['Hname'];		

		$item['disAmt'] = abs(intval($row['dc_price']));
		array_push($items,$item);
	}	
	$erpia->_xml(array('info'=>$items));		
}
?>