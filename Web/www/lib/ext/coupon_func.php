<?
function usableProductOnCoupon($productcode){
	$prleng=strlen($productcode);
	if($productcode=="ALL") {
		$product="ÀüÃ¼»óÇ°";
	} else {
		$arrproduct=explode(",",$productcode);
		for($a=0;$a<count($arrproduct);$a++) {
			if($a>0) $product.=",";

			$prleng=strlen($arrproduct[$a]);
			if($prleng==12) {
				$sql2 = "SELECT code_name as product FROM tblproductcode WHERE codeA='".substr($arrproduct[$a],0,3)."' ";
				if(substr($arrproduct[$a],3,3)!="000") {
					$sql2.= "AND (codeB='".substr($arrproduct[$a],3,3)."' OR codeB='000') ";
					if(substr($arrproduct[$a],6,3)!="000") {
						$sql2.= "AND (codeC='".substr($arrproduct[$a],6,3)."' OR codeC='000') ";
						if(substr($arrproduct[$a],9,3)!="000") {
							$sql2.= "AND (codeD='".substr($arrproduct[$a],9,3)."' OR codeD='000') ";
						} else {
							$sql2.= "AND codeD='000' ";
						}
					} else {
						$sql2.= "AND codeC='000' ";
					}
				} else {
					$sql2.= "AND codeB='000' AND codeC='000' ";
				}
				$sql2.= "AND (type='L' or type='LX' or type='LM' or type='LMX') ";
				$sql2.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
				$result2 = mysql_query($sql2,get_db_conn());
				$i=0;
				while($row2=mysql_fetch_object($result2)) {
					if($i>0) $product.= " > ";
					$product.= $row2->product;
					$i++;
				}
				mysql_free_result($result2);
			}
			if($prleng==18) {
				$sql2 = "SELECT productname as product FROM tblproduct WHERE productcode='".$arrproduct[$a]."' ";
				$result2 = mysql_query($sql2,get_db_conn());
				if($row2 = mysql_fetch_object($result2)) {
					$product.= $row2->product;
				}
				mysql_free_result($result2);
			}
		}
	}
	return $product;
}

function checkGroupUseCoupon(&$groupname){
	global $_ShopInfo;
	if(!_empty($_ShopInfo->memgroup)){
		$r = "select group_name,group_apply_coupon from tblmembergroup where group_code = '".$_ShopInfo->memgroup."' ";
		if(false !== $rs = mysql_query($r,get_db_conn())){
			$rcount = mysql_num_rows($rs);
			if($rcount >0){
				$row = mysql_fetch_object($rs);
				$groupname = $row->group_name;
				return !($row->group_apply_coupon == "N");
			}else{
				return !_empty($_ShopInfo->getMemid());
			}
		}
	}
	return !_empty($_ShopInfo->getMemid());
}

function getAbleProductOnBasket($couponcode,$id,$key){
	global $_ShopInfo;

	$return = array();
	if(preg_match('/^[0-9a-zA-Z]+$/',$couponcode) && preg_match('/^[0-9a-zA-Z]+$/',$id) && preg_match('/^[0-9a-zA-Z]+$/',$key)){
		$sql = "SELECT a.coupon_code,if(c.vender=0,'¼¥',v.id) as venderid, a.coupon_name, a.sale_type, a.sale_money, a.bank_only,a.order_limit, a.productcode, a.mini_price, a.use_con_type1, a.use_con_type2, a.use_point, a.etcapply_gift, a.vender, b.date_start, b.date_end FROM tblcouponissue b inner join tblcouponinfo a on a.coupon_code=b.coupon_code left join tblvenderinfo v on a.vender = v.vender WHERE b.id='".$id."' AND b.date_start<='".date("YmdH")."'AND (b.date_end>='".date("YmdH")."' OR b.date_end='') and a.coupon_code = '".$couponcode."' and b.used != 'Y' limit 1 ";

		if(false !== $res = mysql_query($sql,get_db_conn())){
			if(mysql_num_rows($res)){
				$cinfo = mysql_fetch_assoc($res);
				$cproducts = ($cinfo['productcode'] != 'ALL')?explode(',',$cinfo['productcode']):true;
				if(_array($productcode)){
					for($j=count($cproducts) -1;$j<=0;$j--){
						if(_empty($cproducts[$j])) unset($cproducts[$j]);
						else{
							if(substr($cproducts[$j],12) == '000000'){
								$cproducts[$j] = substr($cproducts[$j],0,12);
								for($k=3;$k>=0;$k--){
									if(substr($cproducts[$j],$k*3,3) == '000') $cproducts[$j] = substr($cproducts[$j],0,$k*3);
									else break;
								}
							}
						}
					}
				}

				$sql = "select productcode from tblbasket where tempkey='".$key."'";

				$pcodes = array();
				if(false !== $res = mysql_query($sql,get_db_conn())){
					while($tmp = mysql_fetch_assoc($res)) array_push($pcodes,$tmp['productcode']);
				}

				if(_array($pcodes)){
					foreach($pcodes as $productcode){
						$chk = categoryAuth($productcode);
						if($chk['coupon'] != 'Y') continue;

						if($cproducts=== true) array_push($return,$productcode);
						else{
							foreach($cproducts as $ccode){
								if(substr($productcode,0,strlen($ccode)) == $ccode){
									if($cinfo['use_con_type2'] != 'N') array_push($return,$productcode);
									break;
								}
							}
							if($cinfo['use_con_type2'] == 'N' && !in_array($productcode,$return)) array_push($return,$productcode);
						}
					}
				}
			}
		}
	}
	return $return;
}

function checkCouponUasble($condition,$productcode,$contype2='Y'){
	if(preg_match('/^[0-9]{18}$/',$productcode)){
		if($condition == 'ALL') return true;
		$cproducts = explode(',',$condition);

		for($j=count($cproducts) -1;$j>=0;$j--){
			if(_empty($cproducts[$j])) unset($cproducts[$j]);
			else{
				if(strlen($cproducts[$j]) == 12 || substr($cproducts[$j],12) == '000000'){
					$cproducts[$j] = substr($cproducts[$j],0,12);

					for($k=3;$k>=0;$k--){
						if(substr($cproducts[$j],$k*3,3) == '000') $cproducts[$j] = substr($cproducts[$j],0,$k*3);
						else break;
					}
				}

			}
		}

		foreach($cproducts as $ccode){
			$chkarr = array($productcode);
			$chkarr = array_merge($chkarr,_getMultiCategory($productcode));
			foreach($chkarr as $chkcode){
				if(substr($chkcode,0,strlen($ccode)) == $ccode){
					//if($contype2 == 'N') return false;
					if($contype2 != 'N') return true;
					else return false;
				}
			}
		}
		if($contype2 == 'N') return true;
		else return false;
	}
	return false;
}

function ableCouponOnProduct($code,$vender=-1,$retarray=false,$excpcodes=array()){
	global $_ShopInfo;
	$couponItems = array();
	

	if(!_empty($_ShopInfo->getMemid())) $sql = "SELECT c.*,if(c.vender=0,'¼¥',v.id) as venderid FROM tblcouponinfo c left join tblcouponissue u on (u.coupon_code = c.coupon_code and u.id='".$_ShopInfo->getMemid()."') left join tblvenderinfo v on c.vender = v.vender ";
	else $sql = "SELECT c.*,if(c.vender =0,'¼¥',v.id) as venderid FROM tblcouponinfo c left join tblvenderinfo v on c.vender = v.vender ";

	if(preg_match('/^[0-9]{18}$/',$code)){
		$psql = "select vender from tblproduct where productcode='".$code."' limit 1";
		if(false !== $pres = mysql_query($psql,get_db_conn())){
			$vender = mysql_result($pres,0,0);
		}
	}

	if($vender == -1){
		$sql.= "WHERE 1=1 ";
	}else if($vender >0){
		$sql.= "WHERE (c.vender='0' OR c.vender='".$vender."') ";
	} else {
		$sql.= "WHERE c.vender='0' ";
	}

	$sql.= "AND c.display='Y' AND c.issue_type='Y' AND c.detail_auto='Y' ";
	$sql.= "AND (c.date_end>=".date("YmdH")." OR c.date_end='') ";
	$sql.= "AND (c.date_start<=".date("YmdH")." OR c.date_start='') ";

	$couponsearchcode = array();
	for($ci=0;$ci<4;$ci++){
		if(substr($code,3*$ci,3) == '000'){
			break;
		}else{
			array_push($couponsearchcode, str_pad(substr($code,0,3*($ci+1)),12,'0'));
		}
	}

	$sql.= "AND (
					(
						c.use_con_type2='Y'
						AND
						(
							c.productcode = 'ALL' ";
	foreach($couponsearchcode as $codeval){
		// $sql .= " or c.productcode like '%".$codeval."%' ";
		$sql .= " or c.productcode like '".$codeval.",%' or  c.productcode like '%,".$codeval.",%'";
	}
	if(preg_match('/^[0-9]{18}$/',$code)){
		// $sql .= " or c.productcode like '%".$code."%' ";
		$sql .= " or c.productcode like '".$code.",%' or  c.productcode like '%,".$code.",%' ";
	}
		$sql .= "		)
					)
					OR
					(
						c.use_con_type2='N' ";
	foreach($couponsearchcode as $codeval){
		//$sql .= " and c.productcode not like '%".$codeval."%' ";
		$sql .= " and c.productcode not like '".$codeval.",%' and c.productcode not like '%,".$codeval.",%' ";
	}
	if(preg_match('/^[0-9]{18}$/',$code)){
		//$sql .= "		and c.productcode not like '%".$code."%' ";
		$sql .= "		and c.productcode not like '".$code.",%' and c.productcode not like '%,".$code.",%' ";
	}
	$sql .= "		)
				)";
				
	
	if(_array($excpcodes)){
		$sql .= " and c.coupon_code not in ('".implode("','",$excpcodes)."')";
	}

	if(!_empty($_ShopInfo->getMemid())){
		$sql .= " and (isnull(u.id) or (c.repeat_id = 'Y' and u.used = 'Y'))";
	}

	$result=mysql_query($sql,get_db_conn());
	
	if($retarray !== false){
		while($row=mysql_fetch_assoc($result)){
			array_push($couponItems,$row);
		}
	}else{
		while($row=mysql_fetch_object($result)){
			array_push($couponItems,$row);
		}
	}
	return $couponItems;
}

function getMyCouponList($productcode='',$onlycode=false){
	global $_ShopInfo;
	$productcode = trim($productcode);
	$coupons = array();
	if(true === checkGroupUseCoupon($groupname)){
		$sql = "SELECT a.coupon_code,if(a.vender=0,'¼¥',v.id) as venderid,a.amount_floor, a.coupon_name, a.sale_type, a.sale_money, a.bank_only,a.order_limit, a.productcode, a.mini_price, a.use_con_type1, a.use_con_type2, a.use_point, a.etcapply_gift, a.vender, b.date_start, b.date_end FROM tblcouponissue b inner join tblcouponinfo a on a.coupon_code=b.coupon_code left join tblvenderinfo v on a.vender = v.vender WHERE b.id='".$_ShopInfo->getMemid()."' AND b.used !='Y' AND b.date_start<='".date("YmdH")."'AND (b.date_end>='".date("YmdH")."' OR b.date_end='') ";
		
		$couponsearchcode = array();
		if(!_empty($productcode)){
			for($ci=0;$ci<4;$ci++){
				if(substr($productcode,3*$ci,3) == '000'){
					break;
				}else{
					array_push($couponsearchcode, str_pad(substr($productcode,0,3*($ci+1)),12,'0'));
				}
			}
		
			$sql.= "AND (
							(
								a.use_con_type2='Y'
								AND
								(
									a.productcode = 'ALL' ";
			foreach($couponsearchcode as $codeval){
				$sql .= " or a.productcode like '".$codeval.",%' or  a.productcode like '%,".$codeval.",%'";
			}
			if(preg_match('/^[0-9]{18}$/',$productcode)){
				$sql .= " or a.productcode like '".$productcode.",%' or  a.productcode like '%,".$productcode.",%' ";
			}
				$sql .= "		)
							)
							OR
							(
								a.use_con_type2='N' ";
			foreach($couponsearchcode as $codeval){
				$sql .= " and a.productcode not like '".$codeval.",%' and a.productcode not like '%,".$codeval.",%' ";
			}
			if(preg_match('/^[0-9]{18}$/',$productcode)){
				$sql .= "		and a.productcode not like '".$productcode.",%' and a.productcode not like '%,".$productcode.",%' ";
			}
			$sql .= "		)
						)";
		}

		if(false !== $res = mysql_query($sql,get_db_conn())){
			while($coupon = mysql_fetch_assoc($res)){
				if($onlycode === true){
					array_push($coupons,$coupon['coupon_code']);
				}else{
					array_push($coupons,$coupon);
				}
			}
		}
		if(_array($coupons)){
			if($onlycode === true) return $coupons;
			foreach($coupons as $idx=>$coupon){
				$coupons[$idx]['productstr'] = array();
				if($coupon['productcode'] != 'ALL'){
					$tmp = explode(',',$coupon['productcode']);

					for($i=0;$i<count($tmp);$i++){
						if(!_empty($tmp[$i])){
							if(preg_match('/^[0-9]{12}$/',$tmp[$i])){ // Ä«Å×°í¸®¿¡ ÇÒ´ç
								$catesql = "SELECT code_name as product FROM tblproductcode WHERE ";
								$twhere = array(" type in ('L','LX','LM','LMX') ");

								for($j=0;$j<4;$j++){
									$code = substr($tmp[$i],$j*3,3);
									if($code != '000'){
										if($j>0) array_push($twhere,' (code'.chr(65+$j)."='".$code."' or code".chr(65+$j)."='000') ");
										else array_push($twhere,' code'.chr(65+$j)."='".$code."'");
									}else{
										array_push($twhere,' code'.chr(65+$j)."='000'");
									}
								}

								$catesql .= implode(' and ',$twhere);
								$catesql .= "ORDER BY codeA,codeB,codeC,codeD ASC ";

								if(false !== $res = mysql_query($catesql,get_db_conn())){
									$navi = array();
									while($row2 = mysql_fetch_assoc($res)) array_push($navi,$row2['product']);
									array_push($coupons[$idx]['productstr'],implode(' > ',$navi));
								}
							}else if(preg_match('/^[0-9]{18}$/',$tmp[$i])){ // »óÇ°¿¡ ÇÒ´ç
								$sql2 = "SELECT productname as product FROM tblproduct WHERE productcode='".$tmp[$i]."' limit 1 ";
								if(false !== $res = mysql_query($sql2,get_db_conn())){
									while($row = mysql_fetch_assoc($res)){
										array_push($coupons[$idx]['productstr'],$row['product']);
									}
								}
							}
						}
					}// end for
				}
			}// end foreach

		}
	}
	//_pr($coupons);
	return $coupons;
}
?>