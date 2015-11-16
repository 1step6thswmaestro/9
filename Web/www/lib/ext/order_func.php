<?php
include_once dirname(__FILE__).'/func.php';
include_once dirname(__FILE__).'/base_func.php';





// 장바구니 정보 가져오기 : 반환값은 mysql query 결과 resource type  : 벤더별
function getBasketByResource($tblbasket='tblbasket',$vender){
	global  $_ShopInfo;
	if(isSeller()=='Y') {
		$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.vender,b.productcode,b.productname,if(b.productdisprice>0,b.productdisprice,b.sellprice) as sellprice, ";
		$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, b.brand, ";
		$sql.= "b.etctype,b.deli_price, b.deli,if(b.productdisprice > 0,b.productdisprice*a.quantity,b.sellprice*a.quantity) as realprice, b.selfcode,a.assemble_list,a.assemble_idx ";
	} else {
		$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.vender,b.productcode,b.productname,b.sellprice, ";
		$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, b.brand, ";
		$sql.= "b.etctype,b.deli_price, b.deli,b.sellprice*a.quantity as realprice, b.selfcode,a.assemble_list,a.assemble_idx ";

	}
	$sql.= ", a.basketidx, b.sns_state,b.present_state,b.pester_state,b.sns_reserve2,b.sns_reserve2_type,d.sell_startdate,d.sell_enddate "; //sns 및 기타 추가기능 정보

	//상품권구입
	if($tblbasket !="tblbasket2"){
		$sql.= ",v.deli_super, v.deli_price AS venderDeliprice ,b.pridx, b.date, b.tax_yn";
		$sql.=", a.sell_memid ";
	}
	//$sql.= ", c.assemble_type, c.assemble_title ";
	$sql.= " FROM ".$tblbasket." a, tblproduct b ";
	$sql.= "LEFT OUTER JOIN tblproduct_social d ON b.productcode=d.pcode ";

	if($tblbasket !="tblbasket2"){
		$sql.= "LEFT OUTER JOIN tblvenderinfo v ON b.vender=v.vender ";
	}
	//$sql.= "LEFT OUTER JOIN tblassembleproduct c ON b.productcode=c.productcode ";
	//$sql .= " LEFT JOIN tblmemberdiscount dis on (dis.productcode = a.productcode and dis.group_code='".$_ShopInfo->getMemgroup()."') ";
	$sql.= "WHERE 1 ";
	if($tblbasket !="tblbasket2"){
		if($vender > 0){
			$sql.= "AND b.vender = '".$vender."' ";
		}else{
			$sql.= "AND (b.vender = '".$vender."' OR v.deli_super = 'S' )";
		}
	}
	$sql.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND a.productcode=b.productcode ";
	$sql.= "ORDER BY deli ASC, deli_price ASC ";
	return mysql_query($sql,get_db_conn());
}





// 장바구니 정보 가져오기 : 반환값은 mysql query 결과 resource type
function getBasketByResourceAll($tblbasket='tblbasket'){
	global  $_ShopInfo;
	if(isSeller()=='Y') {
		$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.vender,b.productcode,b.productname,if(b.productdisprice>0,b.productdisprice,b.sellprice) as sellprice, ";
		$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, b.brand, ";
		$sql.= "b.etctype,b.deli_price, b.deli,if(b.productdisprice > 0,b.productdisprice*a.quantity,b.sellprice*a.quantity) as realprice, b.selfcode,a.assemble_list,a.assemble_idx ";
		$sql.= ", a.basketidx, b.sns_state,b.present_state,b.pester_state,b.sns_reserve2,b.sns_reserve2_type,d.sell_startdate,d.sell_enddate "; //sns 및 기타 추가기능 정보
		$sql.= ",v.deli_super";
	} else {
		$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.vender,b.productcode,b.productname,b.sellprice, ";
		$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, b.brand, ";
		$sql.= "b.etctype,b.deli_price, b.deli,b.sellprice*a.quantity as realprice, b.selfcode,a.assemble_list,a.assemble_idx ";
		$sql.= ", a.basketidx, b.sns_state,b.present_state,b.pester_state,b.sns_reserve2,b.sns_reserve2_type,d.sell_startdate,d.sell_enddate "; //sns 및 기타 추가기능 정보
		$sql.= ",v.deli_super";
	}

	//상품권구입
	if($tblbasket !="tblbasket2") $sql.=", a.sell_memid ";
	$sql.= "FROM ".$tblbasket." a, tblproduct b ";
	$sql.= "LEFT OUTER JOIN tblproduct_social d ON b.productcode=d.pcode ";

	$sql.= "LEFT OUTER JOIN tblvenderinfo v ON b.vender=v.vender ";
	$sql.= "WHERE 1 ";
	$sql.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND a.productcode=b.productcode ";
	$sql.= "ORDER BY a.date DESC,b.vender desc ";
	return mysql_query($sql,get_db_conn());
}




// 장바구니 리스트
// 상품 추가 할인가격 임시 적용 : $test['002002000000000002_2_3']="5000";  배열['상품코드_옵션1idx_옵션2idx']='상품 할인가'
function getBasketByArray($tblbasket='tblbasket', $raddr, $productTest = array() ){
	global $_ShopInfo,$Dir,$_data;

	$basketItems = array();
	if(_empty($tblbasket)) $tblbasket='tblbasket';

	$basketItems['productTest'] = "";

	// 벤더 리스트 SQL
	// venderidx 0 은 본사
	if($tblbasket == 'tblbasket2'){
		$sql = "SELECT 0 as venderidx FROM ".$tblbasket." a inner join tblproduct b on a.productcode=b.productcode  WHERE a.tempkey='".$_ShopInfo->getTempkey()."'";

	}else{
		$sql = "SELECT IF(v.deli_super='S',0,b.vender) as venderidx FROM ".$tblbasket." a inner join tblproduct b on a.productcode=b.productcode left join tblvenderinfo v on v.vender = b.vender WHERE a.tempkey='".$_ShopInfo->getTempkey()."' GROUP BY venderidx ";
	}

	if(false === $res = mysql_query($sql,get_db_conn())) {
		$basketItems['errmsg'] = mysql_error();
	} else{
		$basketItems['sumprice'] = // 주문 총 합계
		$basketItems['deli_price'] = // 주문 총 배송비
		$basketItems['excp_group_discount'] = //
		$basketItems['reserve'] = // 총 적립 예정금
		$basketItems['gift_price'] = //
		$basketItems['reserve_price'] = //
		$basketItems['setquotacnt'] =
		$basketItems['productcnt'] = 0; // 상품 개수
		$basketItems['arr_prlist'] = array();

		// 벤더 리스트
		while($vender = mysql_fetch_assoc($res)){
			if(!_isInt($vender['venderidx']))  $vender['venderidx']=0;

			// 벤더 배송정보 초기화
			$basketItems['vender'][$vender['venderidx']]['conf'] = array('deli_price'=>0,'deli_pricetype'=>'','deli_after'=>'','deli_mini'=>0,'deli_area_limit'=>0, 'deli_area'=>0);

			if(_isInt($vender['venderidx'])){
				// 벤더사 일경우 벤더사 정보
				$sql = "SELECT deli_price, deli_pricetype, deli_mini, deli_area_limit, deli_area FROM tblvenderinfo WHERE vender='".$vender['venderidx']."' ";
				$res2=mysql_query($sql,get_db_conn());
				if($basketItems['vender'][$vender['venderidx']]['conf'] =mysql_fetch_assoc($res2)){
					if($basketItems['vender'][$vender['venderidx']]['conf']['deli_price']==-9) { // 착불
						$basketItems['vender'][$vender['venderidx']]['conf']['deli_price'] = 0;
						$basketItems['vender'][$vender['venderidx']]['conf']['deli_after'] = 'Y';
					}
					// if($_vender->deli_mini==0) $_vender->deli_mini=1000000000;??
				}
				mysql_free_result($res2);
			}else{
				// 기본 배송정보
				$basketItems['vender'][$vender['venderidx']]['conf']['deli_pricetype'] = $_data->deli_basefeetype;
				$basketItems['vender'][$vender['venderidx']]['conf']['deli_area_limit'] = $_data->deli_area_limit;
				$basketItems['vender'][$vender['venderidx']]['conf']['deli_area'] = $_data->deli_area;
				$basketItems['vender'][$vender['venderidx']]['conf']['deli_mini'] = $_data->deli_miniprice;
				$basketItems['vender'][$vender['venderidx']]['conf']['deli_price'] = $_data->deli_basefee;
			}
		}



		// 배송타입통합 카운트
		$deliCount = array();

		// 밴더별 장바구니 정보 (벤더 0 은 본사) ****************************************************
		foreach($basketItems['vender'] as $vender=>&$venderval){

			$result = getBasketByResource($tblbasket,$vender);
			$venderval['sumprice'] = 0; // 벤더 총합계
			$venderval['delisumprice'] = 0; // 벤더 배송비 적용 총 구매액
			$venderval['deliprice'] = 0; // 벤더 배송비
			$venderval['deli_productprice']=0; // 그룹 상품 개별 배송비
			$venderval['reserve_price'] = 0; // 벤더 상품 예정 적립금
			$venderval['products'] = array(); // 상품정보

			// 밴더별 > 상품별 장바구니 정보
			while($product = mysql_fetch_assoc($result)){

				// 사은품 , 적립금 , 쿠폰 사용 적용 확인
				$product['cateAuth'] = categoryAuth($product['productcode']);

				// 상품수
				$basketItems['productcnt']++;

				// 상품명 리스트
				$basketItems['arr_prlist'][$product['productcode']]=$product['productname'];


				// 옵션 ==============================================================================================
				if(!_empty($product['option1']) && substr($product['option1'],0,5) != '[OPTG' ) $product['option1'] = explode(',',$product['option1']);
				if(!_empty($product['option2'])) $product['option2'] = explode(',',$product['option2']);
				if(!_empty($product['option_quantity'])) $product['option_quantity'] = explode(',',$product['option_quantity']);
				if(!_empty($product['option_price'])) $product['option_price'] = explode(',',$product['option_price']);
				if($product['cateAuth']['reserve'] == "N") $product['reserve'] = 0;
				if(_array($product['option_price']) && $product['opt1_idx']==0){
					$sql = "DELETE FROM ".$tblbasket." WHERE tempkey='".$_ShopInfo->getTempkey()."' AND productcode='".$product['productcode']."' AND opt1_idx='".$product['opt1_idx']."' AND opt2_idx='".$product['opt2_idx']."' AND optidxs='".$product['optidxs']."' ";
					mysql_query($sql,get_db_conn());
					_alert("필수 선택 옵션 항목이 있습니다.\\n옵션을 선택하신후 장바구니에\\n담으시기 바랍니다.",$Dir.FrontDir."productdetail.php?productcode=".$product['productcode']);
					exit;
				}
				// 옵션그룹
				$product['optvalue'] = "";
				$optvalue = "";
				if(ereg("^(\[OPTG)([0-9]{4})(\])$",$product['option1'])){
					$product['optionGroup'] = $product['option1'];
					$optioncode = substr($product['option1'],5,4);
					//$product['option1']="";
					$product['option_price']="";

					if(!_empty($product['optidxs'])){
						$tempoptcode = substr($product['optidxs'],0,-1);
						$exoptcode = explode(",",$tempoptcode);
						$resultopt = mysql_query("SELECT * FROM tblproductoption WHERE option_code='".$optioncode."' ",get_db_conn());
						if($rowopt = mysql_fetch_object($resultopt)){
							$optionadd = array (&$rowopt->option_value01,&$rowopt->option_value02,&$rowopt->option_value03,&$rowopt->option_value04,&$rowopt->option_value05,&$rowopt->option_value06,&$rowopt->option_value07,&$rowopt->option_value08,&$rowopt->option_value09,&$rowopt->option_value10);

							$option_choice = $rowopt->option_choice;
							$exoption_choice = explode("",$option_choice);
							foreach($optionadd as $opti=>$optv){
								if(_empty($optv)) continue;
								if($exoption_choice[$opti]==1 && $exoptcode[$opti]==0){
									$delsql = "DELETE FROM ".$tblbasket." WHERE tempkey='".$_ShopInfo->getTempkey()."' AND productcode='".$product['productcode']."' AND opt1_idx='".$product['opt1_idx']."' AND opt2_idx='".$product['opt2_idx']."' AND optidxs='".$product['optidxs']."' ";
									mysql_query($delsql,get_db_conn());
									_alert("필수 선택 옵션 항목이 있습니다.\\n옵션을 선택하신후 장바구니에\\n담으시기 바랍니다.",$Dir.FrontDir."productdetail.php?productcode=".$product['productcode']);
									exit;
								}
								if($exoptcode[$opti]>0){
									$opval = explode("",str_replace('"','',$optv));
									$optvalue.= ", ".$opval[0]." : ";
									$exop = explode(",",str_replace('"','',$opval[$exoptcode[$opti]]));
									if ($exop[1]>0) $optvalue.=$exop[0]."(<font color=#FF3C00>+".number_format($exop[1])."원</font>)";
									else if($exop[1]==0) $optvalue.=$exop[0];
									else $optvalue.=$exop[0]."(<font color=#FF3C00>".number_format($exop[1])."원</font>)";
									$product['sellprice']+=$exop[1];
								}
							} // end foreach
							$product['optvalue'] = substr($optvalue,1);
						}
					}
				}

				// 옵션에 따른 가격 변동 체크
				if (_array($product['option_price']) && !_empty($product['opt1_idx'])) {
					$product['sellprice'] = $product['option_price'][$product['opt1_idx']-1];
				}



				// 상품별 회원할인율 적용 시작 : 상품마다 적용된 회원 그룹별 할인
				// 셀러 회원이거나 비회원의경우 미적용
				// over_discount : Y 그룹별 중복할인가능 / N 중복불가(상품별 할인만 적용)
				$product['group_discount'] = 0;
				if( isSeller() != 'Y' AND strlen($_ShopInfo->getMemgroup()) > 0 ) {
					$dSql = "SELECT discountrates,discountprices,over_discount FROM tblmemberdiscount WHERE productcode='".$product['productcode']."' AND group_code='".$_ShopInfo->getMemgroup()."' AND discountYN='Y'";
					if( false !== $dResult = mysql_query($dSql,get_db_conn()) ) {
						if(mysql_num_rows($dResult)){
							list(,$product['group_discount'],$product['group_over_discount']) = mysql_fetch_row($dResult);
						}
					}
				}

				// 그룹 할인 적용
				$product['sellprice'] -= $product['group_discount'];


				// 임시 가격 변경 처리
				$key = $product['productcode']."_".$product['opt1_idx']."_".$product['opt2_idx']."_".$product['optidxs'];
				if ( _array($productTest) ) {
					if( strlen($productTest[$key]) > 0 ) {
						$product['sellprice'] -= $productTest[$key];
						$basketItems['productTest'] = "TEST_MODE";
					}
				}

				// 상품 판매가 (구매수적용)
				$product['realprice'] = $product['sellprice']*$product['quantity'];

				// 그룹별 중복할인 안되는 상품 총 금액
				if($product['group_over_discount'] == 'N') {
					$basketItems['excp_group_discount'] += $product['realprice'] ;
				}


				// 적립금 ===========================================================================

				$tempreserve = getReserveConversion($product['reserve'],$product['reservetype'],$product['sellprice'],"N");

				//sns홍보일 경우 적립금
				if($_data->sns_ok == "Y" && $product['sns_state'] == "Y" && !_empty($product['sell_memid'])) {
					$tempreserve = getReserveConversionSNS($tempreserve,$product['sns_reserve2'],$product['sns_reserve2_type'],$product['sellprice'],"N");
				}

				// 벤더 상품 총 금액
				$venderval['sumprice'] += $product['realprice'];

				// 사은품 적용 된 상품 총액
				if($product['cateAuth']['gift'] == 'Y') $basketItems['gift_price'] += $product['realprice'];

				// 적립금 적용 된 상품 총액
				if($product['cateAuth']['reserve'] == 'Y') $basketItems['reserve_price'] += $product['realprice'];


				// 상품 적립예정금
				$product['reserve'] =$tempreserve*$product['quantity'];

				// 총적립 예정금
				$basketItems['reserve'] += $product['reserve'];

				// 기타 설정 (?)
				if(!_empty($product['etctype'])) {
					$etctemp = explode("",$product['etctype']);
					for($i=0;$i<count($etctemp);$i++){
						switch ($etctemp[$i]) {
							case "BANKONLY": $product['bankonly'] = "Y"; break;
							case "SETQUOTA":
								if($_data->card_splittype=="O" && $product['realprice'] >=$_data->card_splitprice){
									$product['setquota'] = 'Y';
									$basketItems['setquotacnt']++;
								}
								break;
						}
					}// end for
				}


				// 이미지 ===================================================================================
				if(!_empty($product['tinyimage']) && file_exists($Dir.DataDir."shopimages/product/".$product['tinyimage'])){
					$product['tinyimage'] = array('ori'=>$product['tinyimage'],'src'=>$Dir.DataDir."shopimages/product/".$product['tinyimage'],'width'=>'','height'=>'');
				}else{
					$product['tinyimage'] = array('ori'=>$product['tinyimage'],'src'=>$Dir."images/no_img.gif",'width'=>'','height'=>'');
				}
				list($product['tinyimage']['width'],$product['tinyimage']['height']) = getImageSize($product['tinyimage']['src']);
				$product['tinyimage']['big'] = ($product['tinyimage']['width'] > $product['tinyimage']['height'])?'width':'height';
				$product['tinyimage']['bigsize'] = $product['tinyimage'][$product['tinyimage']['big']];
				// $product['tinyimage']['src'] = str_replace($_SERVER['DOCUMENT_ROOT'],'/',realpath($product['tinyimage']['src']));





				// 배송비 =====================================================================================

				// 상품의 배송비 설정
				if ( ($product['deli']=="Y" || $product['deli']=="N") && $product['deli_price'] > 0 ) {
					// 개별배송비 유료 이면서 배송비 설정 되어 있음 (Y/0초과) 또는 기본 배송비 (N)
					// deli =>  Y : 낱개 배송비 (배송비*개수) / N : 상품별 배송비(개수 상관없음)
					// 그룹 상품 개별 배송비 : deli_productprice
					$venderval['deli_productprice'] += $product['deli_price']*(($product['deli']=="Y")?$product['quantity']:1);
				} else if($product['deli'] !="F" && $product['deli'] !="G"){
					// 개별배송비 무료가 아니거나 착불이 아니면.....(개별상품 무료와 착불은 패스)
					// 배송비에 적용된 상품금액
					$venderval['delisumprice'] += $product['realprice'];
				}

				// 배송 타입 통합 카운터
				if( $product['deli_price'] == 0 AND $product['deli'] == "N" ) { // 기본배송비 일 경우만
					$basketItems['vender'][$vender]['deliCount'][$product['deli']][($product['deli_price']>0?"1":"0")]++;
				}

				// 상품정보 리스트 등록
				array_push($venderval['products'],$product);

			}// end while product

			$deli_init = intval($venderval['delisumprice'])> 0;


			if($deli_init){
				if($venderval['conf']['deli_price'] > 0 && ( $venderval['delisumprice']<$venderval['conf']['deli_mini'] OR $venderval['conf']['deli_mini']==0 ) ) {

					$venderval['deliprice'] += $venderval['conf']['deli_price'];

				} 
			}
			if(!_empty($venderval['conf']['deli_area_limit'])) {
				// 차등 배송료
				$deliLimitList = explode("=",$venderval['conf']['deli_area_limit']);
				$check_addr = "N";

				for($deliLimitList_i=0; $deliLimitList_i<count($deliLimitList); $deliLimitList_i++) {
					/*if ($_ShopInfo->getMemid()=="getmall") {
					echo "<script>alert('" .$deliLimitList_i. "------" .count($deliLimitList). "');</script>";
				}*/
					$deliLimitValues=explode("",$deliLimitList[$deliLimitList_i]);
						if(false !== $pos = strpos($raddr, $deliLimitValues[0])){
						if($pos==0 || strlen(trim(substr($raddr, $pos-1, 1)))<1){
							$check_addr = "Y";
							continue;
						}
					}
					if($check_addr == "Y"){
						if(strlen($deliLimitValues[1])>0) {
							// a 이상 ~ b 미만
							if( $deliLimitValues[0] <= $venderval['sumprice'] AND $venderval['sumprice'] < $deliLimitValues[1] ) {
								$venderval['conf']['deli_price'] = $deliLimitValues[2];
								$venderval['deliprice'] = $venderval['conf']['deli_price'];
								echo "<script>alert('해당 지역의 배송료는 " .$deliLimitValues[2]. "원 입니다.');</script>";
								break;
							}
							// a 이상 ~
							if( $deliLimitValues[0] <= $venderval['sumprice'] && strlen($deliLimitValues[1])==0 ) {
								$venderval['conf']['deli_price'] = $deliLimitValues[2];
								$venderval['deliprice'] = $venderval['conf']['deli_price'];
								break;
							}
						}
					}
				}
				// 배송료 0원 (무료) 적용
				for($deliLimitList_i=0; $deliLimitList_i<count($deliLimitList); $deliLimitList_i++) {
					$deliLimitValues=explode("",$deliLimitList[$deliLimitList_i]);
					if( $venderval['conf']['deli_mini'] == 1000000000 AND $deliLimitValues[2]==0 ){ //AND strlen($deliLimitValues[1])==0
						$venderval['conf']['deli_mini'] = $deliLimitValues[0];
					}
				}
			}

			if(!_empty($venderval['conf']['deli_area'])){

				$array_deli = explode("|", $venderval['conf']['deli_area']);
				$cnt = floor(count($array_deli)/2);
	
				for($i=0; $i<$cnt; $i++){
					$subdeli = explode(",", $array_deli[$i*2]);

					$subcnt = count($subdeli);

					for($j=0; $j<$subcnt; $j++)
						if(false !== $pos = strpos($raddr, $subdeli[$j]))
							if($pos==0 || strlen(trim(substr($raddr, $pos-1, 1)))<1)
								if($array_deli[$i*2+1]>0){
									$venderval['deliprice'] += $array_deli[$i*2+1];
									echo "<script>alert('해당 지역의 배송료 " .$array_deli[$i*2+1]. "원이 추가됩니다.');</script>";
								}else{
									$venderval['deliprice'] += $array_deli[$i*2+1];
									echo "<script>alert('해당 지역의 배송료 " .$array_deli[$i*2+1]. "원이 활인됩니다.');</script>";
								}
				}
			}
		

			// 그룹 회원 배송비 무료 정책 ( 타입 - 1:기본, 2:무료, 3:금액이상 )
			if( strlen($_ShopInfo->getMemgroup()) > 0 AND $vender == 0 ) {
				$groupDeli = memberGroupDelivery ( $_ShopInfo->getMemgroup() );
				//// 타입 "1" 기본 배송 정책 패스~
				//// 타입 "2" 무료
				if( $groupDeli['type'] == "2" ) {
					$venderval['conf']['deli_price'] = 0;
					$venderval['conf']['deli_mini'] = 0;
					$venderval['deliprice'] = 0;
					$venderval['conf']['deli_pricetype'] = $groupDeli['sumtype'];
					$venderval['conf']['groupDeli'] = $groupDeli['type'];
				}
				//// 타입 "3"금액이상 무료
				$chk_delisumprice = ( $groupDeli['type'] == "Y" ) ? $venderval['delisumprice'] : $venderval['sumprice'] ;
				if( $groupDeli['type'] == "3" AND $groupDeli['money'] < $chk_delisumprice AND $venderval['conf']['deli_mini'] >= $groupDeli['money'] ) {
					$venderval['conf']['deli_price'] = 0;
					$venderval['conf']['deli_mini'] = $groupDeli['money'];
					$venderval['deliprice'] = 0;
					$venderval['conf']['deli_pricetype'] = $groupDeli['sumtype'];
					$venderval['conf']['groupDeli'] = $groupDeli['type'];
				}
			}


			// 개별배송비 추가
			$venderval['deliprice']+=$venderval['deli_productprice'];

			// 개별배송상품 포함(Y) / 제외(N)
			if($venderval['conf']['deli_pricetype']=="Y") $venderval['delisumprice'] = $venderval['sumprice'];


			// 총 배송비
			$basketItems['deli_price']+=$venderval['deliprice'];

			//총 주문금액
			$basketItems['sumprice'] += $venderval['sumprice'];


		}//end foreach vender





		// 회원그룹별 추가적립/추가할인 정책
		if( strlen($_ShopInfo->getMemid())>0 AND strlen($_ShopInfo->getMemgroup())>0 AND substr($_ShopInfo->getMemgroup(),0,1)!="M" AND isSeller() != 'Y' ) {

			$basketItems['groupMemberSale'] = array();

			$arr_dctype=array("B"=>"현금","C"=>"카드","N"=>"현금/카드");

			$memGroupSQL = "
				SELECT
					a.name, b.group_code, b.group_name, b.group_payment, b.group_usemoney, b.group_addmoney
				FROM
					tblmember a,
					tblmembergroup b
				WHERE
					a.id='".$_ShopInfo->getMemid()."'
					AND
					b.group_code=a.group_code
					AND
					MID(b.group_code,1,1)!='M'
			";
			$memGroupResult=mysql_query($memGroupSQL,get_db_conn());
			if($memGroupRow=mysql_fetch_object($memGroupResult)) {
				$basketItems['groupMemberSale']['name'] = $memGroupRow->name; // 회원명
				$basketItems['groupMemberSale']['group'] = $memGroupRow->group_name; // 그룹명
				$basketItems['groupMemberSale']['useMoney'] = $memGroupRow->group_usemoney; // 기준금액
				$basketItems['groupMemberSale']['payType'] = $arr_dctype[$memGroupRow->group_payment]; // 결제방식
				$basketItems['groupMemberSale']['payTypeCode'] = $memGroupRow->group_payment; // 결제방식
				$basketItems['groupMemberSale']['groupCode'] = $type=substr($memGroupRow->group_code,0,2); // 그룹코드
				$basketItems['groupMemberSale']['addMoney'] = $memGroupRow->group_addmoney; // 적용금액 or 적용%
				/*
					RW : 금액 추가 적립
					RP  : % 추가 적립
					SW : 금액 추가 할인
					SP  : % 추가 할인
				*/
			}
			mysql_free_result($memGroupResult);
		}






		// 비과세
		$tax_free = 0;
		foreach ( $basketItems['arr_prlist'] as $p_code=> $p_name) {

			$chkTaxFree = checkTaxFree($p_code);
			if ($chkTaxFree['tax_yn']) {

				$add_tax_free=0;

				foreach ($basketItems['vender'][$chkTaxFree['vender']]['products'] as $pro_array) {
					if ($pro_array['productcode'] == $p_code) {
						$add_tax_free += $pro_array['realprice'];
					}

				}
				$tax_free = $tax_free + $add_tax_free;
			}

		}
		$basketItems['tax_free'] = $tax_free;








	}// end if

	return $basketItems;
}





// 비과세 여부
function checkTaxFree($p_code) {

	$result = array();
	$result['tax_yn'] = 0;
	$result['vender'] = 0;

	$sql = "select tax_yn, vender from tblproduct where productcode='".$p_code."'";
	$resultTax = mysql_query($sql,get_db_conn());
	$rowTax = mysql_fetch_array($resultTax);

	if ($rowTax[0]==1) {
		$result['tax_yn'] = 1;
		$result['vender'] = $rowTax[1];
	}
	return $result;
}







// 조건에 따른 주문 목록 반환
function getOrderList($range_start='',$range_end='',$ordergbn='',$type='',$page='1',$itemtype='object',$limit=''){
	global $_ShopInfo,$setup;
	$where = array();
	$result = array('total'=>0,'total_page'=>0,'page'=>1,'orders'=>array());
	if(!_empty($_ShopInfo->getMemid())){
		array_push($where,"id="._escape($_ShopInfo->getMemid()));
		if(!_empty($range_start) && preg_match('/^[0-9]{6,}$/',$range_start)) array_push($where,"ordercode >= '".$range_start."'");
		if(!_empty($range_end) && preg_match('/^[0-9]{6,}$/',$range_end)) array_push($where,"ordercode <= '".$range_end."'");
		switch($ordergbn){
			case 'S':
				array_push($where,"deli_gbn IN ('S','Y','N','X')");
				break;
			case 'C':
				array_push($where,"deli_gbn IN ('C','D')");
				break;
			case 'R':
				//array_push($where,"(deli_gbn IN ('R','E') OR SUBSTRING(status,1,1)='Y')");
				array_push($where," ( select count(*) from tblorderproduct where ordercode=o.ordercode and status in ('RA', 'RC'))>0 ");
				break;
			case 'P':
				array_push($where,"order_type ='P'");
				break;
			case 'T':
				array_push($where,"( SELECT count(*) FROM `tblproduct` P INNER JOIN `tblorderproduct` OP ON OP.`productcode` = P.`productcode` WHERE OP.`ordercode` = o.ordercode AND P.reservation !='0000-00-00' ) > 0 " );
				break;
		}

		array_push($where,"(del_gbn='N' OR del_gbn='A')");

		switch($type){
			case '2':
				array_push($where,"gift='3'");
				break;
			case '3':
				array_push($where,"gift in('1','2')");
				break;
			default:
				array_push($where,"gift='0'");
				break;
		}
		$where = "where ".implode(" AND ",$where);

		$sql = "SELECT COUNT(*) as t_count FROM tblorderinfo o ".$where;
		$res=mysql_query($sql,get_db_conn());
		if($res) $result['total'] = intval(mysql_result($res,0,0));

		if($result['total'] > 0){
			@$result['total_page'] = ceil($result['total']/$setup['list_num']);
			@$result['page'] = min((intval($page) < 0)?1:intval($page),$result['total_page']);
			$ordby = " ORDER BY ordercode DESC ";
			@$limit = " LIMIT ".($setup['list_num']*($result['page']-1)).", ".$setup['list_num'];
			if(_isInt($limit)) $limit = " LIMIT ".$limit;
			else if(_isInt($setup['list_num']))  @$limit = " LIMIT ".($setup['list_num']*($result['page']-1)).", ".$setup['list_num'];
			else $limit = " LIMIT 10";
			$sql = "SELECT ordercode, price, paymethod, pay_admin_proc, pay_flag, bank_date, deli_gbn, gift, tempkey FROM tblorderinfo o ".$where.$ordby.$limit;
			$res = mysql_query($sql,get_db_conn());
			if($res){
				if($itemtype == 'array'){
					while($item = mysql_fetch_assoc($res)) 	array_push($result['orders'],$item);
				}else{
					while($item = mysql_fetch_object($res)) 	array_push($result['orders'],$item);
				}
			}
		}
	}
	return $result;
}

// mypage 상에서 1개월 내 주문 조회
function getMyOrderList($limit,$type='',$itemtype='object'){
	$curdate=date("Ymd",mktime(0,0,0,(int)date("m")-1,(int)date("d"),date("Y")));
	return getOrderList($curdate,'','',$type,'1',$itemtype,$limit);
}



// 주문 코드내의 주문상품 정보를 반환
function getOrderProduct($ordercode,$itemtype='object'){
	$result = array();
	$sql = "SELECT op.*,op.quantity*op.price as sumprice,p.tinyimage,p.minimage, p.reservation FROM tblorderproduct op left join tblproduct p on (op.productcode = p.productcode) WHERE op.ordercode='".$ordercode."' AND (NOT (op.productcode LIKE 'COU%' OR op.productcode LIKE '999999%') or  op.productcode = '99999990GIFT') order by productcode ";
	$res = mysql_query($sql,get_db_conn());
	if($res){
		if($itemtype == 'array'){
			while($item = mysql_fetch_assoc($res)) array_push($result,$item);
		}else{
			while($item = mysql_fetch_object($res)) array_push($result,$item);
		}
	}
	return $result;
}


// 주문 코드내의 주문상품 외 정보를 반환
function getOrderAddtional($ordercode,$itemtype='object') {
	$result = array();
	$sql = "SELECT op.* FROM tblorderproduct op  WHERE op.ordercode='".$ordercode."' AND  (op.productcode LIKE 'COU%' OR op.productcode LIKE '999999%') and op.productcode != '99999990GIFT' ";
	$res = mysql_query($sql,get_db_conn());
	if($res){
		if($itemtype == 'array'){
			while($item = mysql_fetch_assoc($res)) array_push($result,$item);
		}else{
			while($item = mysql_fetch_object($res)) array_push($result,$item);
		}
	}
	return $result;
}





// 객체 형태로 전달 받은 주문내 상품 정보와, 주문 정보로 해당 상품의 주문 상태 메시지를 반환
function orderProductDeliStatusStr($orderproduct,$orderinfo, $cnt){
	$str = '';

	switch($orderproduct->deli_gbn){
		case "C": $str ="주문취소"; break;
		case "D": $str ="취소요청"; break;
		case "E": $str = "환불대기"; break;
		case "S":
			$str = "발송준비";
			//환불 관련이 있을경우 jdy
			$str = getRefundsText($str, $orderproduct, "yes", $orderinfo, $cnt);

			break;
		case "R": $str = "반송처리"; break;
		case "H": $str = "발송완료 [정산보류]"; break;
		case "X": $str = ($orderproduct->gift=='1')?"인증번호발송":"발송준비"; break;

		//현재는 Y와 N, C 만 사용?? 취소 상태(C)가 아니라면 주문 취소가 들어감. jdy
		case "Y":
			if($orderinfo->gift=='1') $str = "인증후적립완료";
			else if($orderinfo->gift=='2') $str = '적립완료';
			else $str = '발송완료';

			if ($orderinfo->pay_admin_proc=="C" && $orderinfo->pay_flag=="0000") {
				$str = "결제취소";
			}else  {

				//환불 관련이 있을경우 jdy
				//$str = getRefundsText($str, $orderproduct, "yes", $orderinfo, $cnt);
			}

			break;

		case"N":
			if (strlen($orderinfo->bank_date)<12 && preg_match("/^(B|O|Q){1}/", $orderinfo->paymethod)) {
				$str = "입금확인중";
				//입금확인중 일? 전체를 이용한 취소만 가능하도록
				$str = getRefundsText($str, $orderproduct, "no", $orderinfo, $cnt);

			}else if ($orderinfo->pay_admin_proc=="C" && $orderinfo->pay_flag=="0000") {
				$str = "결제취소";
			}else if (strlen($orderinfo->bank_date)>=12 || $orderinfo->pay_flag=="0000")  {

				$str = "발송준비";
				$str = getRefundsText($str, $orderproduct, "yes", $orderinfo, $cnt);

			}else {
				$str = "결제확인중";
			}
			break;
		default:
			$str = "오류"; break;
	}

	return $str;
}

// 주문 별 상품에 관한 상태 출력
function orderProductStatusStr($status){
	$status_arr = array('EA'=>'교환신청','EB'=>'교환접수','EC'=>'교환완료','RA'=>'환불신청','RB'=>'환불접수','RC'=>'환불완료'); // 주문별 상태 상세 메시지
	$str = '';
	if(!_empty($status) && !_empty($status_arr[$status])) $str = $status_arr[$status];
	return $str;
}

// 결제 방법 text 출력
function getPaymethodStr($paymethod){
	$str = '';
	if (preg_match("/^(B){1}/",$paymethod)) $str = '무통장입금';
	else if (preg_match("/^(V){1}/",$paymethod)) $str = '실시간계좌이체';
	else if (preg_match("/^(O){1}/",$paymethod)) $str = '가상계좌';
	else if (preg_match("/^(Q){1}/",$paymethod)) $str = '가상계좌-<FONT COLOR="#FF0000">매매보호</FONT>';
	else if (preg_match("/^(C){1}/",$paymethod)) $str = '신용카드';
	else if (preg_match("/^(P){1}/",$paymethod)) $str = '신용카드-<FONT COLOR="#FF0000">매매보호</FONT>';
	else if (preg_match("/^(M){1}/",$paymethod)) $str = '휴대폰';

	return $str;
}


//밴더 번호
function getVenderIdx($code){
	$sql = "SELECT vender FROM tblproduct WHERE productcode='".$code."'";
	$res = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($res);
	return $row;
}

// 회원등급별 배송비 - tblmembergroup - group_carr_free
function memberGroupDelivery ( $group_code ) {
	$sql = "SELECT group_carr_free FROM tblmembergroup WHERE group_code='".$group_code."' LIMIT 1;";
	$res = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($res);
	$set = explode("|",$row->group_carr_free);
	$return = array();
	$return['type'] = $set[0];
	$return['money'] = $set[1];
	if( $set[0] == '3' ) $return['sumtype'] = $set[2];
	return $return;
}

//취소관련 버튼 jdy
function getRefundsText($str, $orderproduct, $can="yes", $orderinfo, $cnt ) {

	if ($orderproduct->status) {
		switch($orderproduct->status){
			case 'EA': $str="교환신청";  break;
			case 'EB': $str= "교환접수";  break;
			case 'EC': $str= "교환완료";  break;
			case 'RA': $str= "환불신청";  break;
			case 'RB': $str= "환불접수";  break;
			case 'RC': $str= "환불완료";  break;
		}
	}else {

		//결제 취소 버튼 추가 jdy
		if( $cnt == 1 ) {
			$str .= "<div style=\"margin-top:3px\"><span style=\"cursor:pointer\" onclick=\"order_cancel('".$orderproduct->tempkey."', '".$orderproduct->ordercode."','".$orderinfo->bank_date."')\"><img src=\"/images/common/mypage/001/mypage_order_cancel_icon01.gif\" alt=\"주문취소\" /></span></div>";
		}else{
			$str .= "<div style=\"margin-top:3px\"><span style=\"cursor:pointer\" onclick=\"order_one_cancel('".$orderproduct->ordercode."', '".$orderproduct->productcode."', '".$can."', '".$orderproduct->tempkey."', '".$orderproduct->uid."')\"><img src=\"/images/common/mypage/001/mypage_order_cancel_icon01.gif\" alt=\"주문취소\" /></span></div>";
		}
	}

	return $str;
}





// 주문타입별 장바구니 테이블
function basketTable($ordertype) {
	$basketList = array(
		'' => 'tblbasket', // 일반 장바구니
		'pester' => "tblbasket_pester", // 조르기
		'pstr' => "tblbasket_pester_order", // 조르기 결제
		'pestersave' => "tblbasket_pester_save", // 조르기 저장
		'present' => "tblbasket_present", // 선물하기
		'ordernow' => "tblbasket_ordernow" // 바로구매
	);
	return $basketList[$ordertype];
}
?>