<?
include_once dirname(__FILE__).'/func.php';
include_once dirname(__FILE__).'/member_func.php';

// 투데이 세일 마감 등 관련 체크용 함수
function _checkTodaySale($productcode=''){
	global $_ShopInfo;
	if(preg_match('/^899[0-9]{15}$/',$productcode)){
		$sql = "select a.*,t.*,unix_timestamp(t.end) -unix_timestamp() as remain, t.salecnt+t.addquantity as sellcnt from tblproduct a inner join todaysale t using(pridx) LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode WHERE a.productcode='".$productcode."' AND a.display='Y' AND (a.group_check='N' OR b.group_code LIKE '%".$_ShopInfo->getMemgroup()."%') limit 1";
		if(false === $res = mysql_query($sql,get_db_conn())){
			if(mysql_num_rows($res)){
				$row = mysql_fetch_assoc($res);
				if($remain < 1) return 'timeout';
			}
		}
	}
	return false;
}

function _saleTotdaySale($productcode,$cnt_opt=1){

}


function checkGiftSet(){
	global $_ShopInfo;
	// shopinfo 사은품 활성화 정보 호출
	$giftInfoRow = @mysql_fetch_object( mysql_query("SELECT `gift_type` FROM `tblshopinfo` LIMIT 1;",get_db_conn()) );
	$giftInfoSetArray = explode("|",$giftInfoRow->gift_type);
	if( $giftInfoSetArray[0] == "C" OR ( $giftInfoSetArray[0] == "M" AND !_empty($_ShopInfo->getMemid()) ) ){
		$sql = "select count(*) from tblgiftinfo";
		if(false === $res = mysql_query($sql,get_db_conn())){
		}else{
			if(mysql_result($res,0,0) > 0) return true;
		}
	}
	return false;
}




// product query
function productQuery () {
	global $_ShopInfo;
	if(isSeller() == 'Y'){ // 도매 회원일 경우
		$sql = "
			SELECT
				a.productcode,
				a.productname,
				if(a.productdisprice>0,a.productdisprice,a.sellprice) as sellprice,
				a.quantity,
				if(a.productdisprice>0,1,0) as isdiscountprice,
				IF(a.reservetype='N',a.reserve*1,a.reserve*a.sellprice*0.01) AS reservesort,
				a.prmsg,
				a.minimage,
				a.tinyimage,
				a.date,
				a.etctype,
				a.consumerprice,
				a.reserve,
				a.reservetype,
				a.tag,
				a.selfcode,
				a.prmsg,
				a.option1,
				a.option2,
				a.discountRate,
				a.vender,
				a.sellcount,
				a.reservation
			FROM
				tblproduct AS a
				LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode
		";
	}else{
		$sql = "
			SELECT
				a.productcode,
				a.productname,
				a.sellprice,
				a.quantity,
				IF(d.discountYN='Y',d.discountprices,0) AS discountprices,
				0 as isdiscountprice,
				IF(a.reservetype='N',a.reserve*1,a.reserve*a.sellprice*0.01) AS reservesort,
				a.prmsg,
				a.minimage,
				a.tinyimage,
				a.date,
				a.etctype,
				a.consumerprice,
				a.reserve,
				a.reservetype,
				a.tag,
				a.selfcode,
				a.prmsg,
				a.option1,
				a.option2,
				a.discountRate,
				a.vender,
				a.sellcount,
				a.reservation
			FROM
				tblproduct AS a
				LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode
				LEFT JOIN tblmemberdiscount d on (d.productcode = a.productcode and d.group_code='".$_ShopInfo->getMemgroup()."')
		";
	}

	return $sql;
}

function getCategoryItems($code,$getsub=false){
	$where = array();
	$result = array('depth'=>0,'items'=>array(),'pcode'=>'');

	for($i=0;$i<4;$i++){
		$tcode = substr($code,$i*3,3);
		if(strlen($tcode) == 3 && $tcode != '000'){
			array_push($where," code".chr(65+$i)."='".$tcode."'");
			$result['depth'] = $i;
			$result['pcode'] .=$tcode;
		}else{
			if($getsub === true || ($i == 0 && _empty($code))){
				array_push($where," code".chr(65+$i)."!='000'");
				$result['depth'] = $i;
				$getsub = false;
			}else{
				array_push($where," code".chr(65+$i)."='000'");
			}
		}
	}
	array_push($where,"(type like 'L%' || type like 'T%')","group_code!='NO' ");
	$where = ' where '.implode(' and ',$where);

	$sql = "select * from tblproductcode ".$where." ORDER BY sequence DESC ; ";
	if(false !== $res = mysql_query($sql,get_db_conn())){
		while($row = mysql_fetch_assoc($res)){
			if(strlen($row['group_code']) > 0 && (strlen($GLOBALS['_ShopInfo']->getMemid())< 1 || strpos($row['group_code'],$GLOBALS['_ShopInfo']->getMemgroup())===false)) continue;
			$row['linkcode'] = '';
			for($i=0;$i<4;$i++){
				$ckey = 'code'.chr(65+$i);
				if(!_empty($row[$ckey]) && $row[$ckey] != '000' && preg_match('/^[0-9]{3}$/',$row[$ckey])) $row['linkcode'] .= $row[$ckey];
				else break;				
			}
			array_push($result['items'],$row);
		}
	}
	return $result;
}

// 관리자 설정 특별 상품 ( 신상품, 추천 상품 등 ) 상품 정보 가져 오기.
function _getSpecialProducts($code='',$special=0,$limit=10,$sort=''){
	global $_ShopInfo;

	$isspecial = true;
	$return = array();
	$where = array();
	if(_isInt($special)){
		if(!_empty($code) && preg_match("/^[0-9]{12}$/",$code)){
			$sql = "SELECT special_list FROM tblspecialcode WHERE code='".$code."' AND special='".$special."' ";
		}else{
			$sql = "SELECT special_list FROM tblspecialmain WHERE special='".$special."' ";
		}
		$res=mysql_query($sql,get_db_conn());
		if($res && mysql_num_rows($res)){
			$sp_prcode="";
			if($row=mysql_fetch_object($res)){
				$sp_prcode=ereg_replace(',','\',\'',$row->special_list);
			}
		}
		array_push($where,"a.productcode IN ('".$sp_prcode."')");
	}else{
		$isspecial = false;
		if(!_empty($code) && preg_match("/^[0-9]{3,12}$/",$code)){
			while(substr($code,-3,3) == '000') $code = substr($code,0,-3);
			array_push($where,"a.productcode like '".$code."%'");
		}
	}

	array_push($where,"a.display='Y'");
	array_push($where,"(a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."')");

	if(!_empty($sort)){// && preg_match('/^([a-zA-Z0-9_]+)_(asc|desc)$/',$sort,$mat)){
		$mat = explode('_',$sort);
		switch($mat[0]){
			case 'new':
				$mat[0] = 'regdate';
				break;
			case 'best':
				$mat[0] = 'sellcount';
				break;
			case 'name':
				$mat[0] = 'productname';
				break;
			case 'price':
				$mat[0] = 'sellprice';
				break;
			case 'reserve':
				$mat[0] = 'reservesort';
				break;
		}
		$ordby = ' order by '.$mat[0].' '.$mat[1];
	}else{
		$ordby = ($isspecial)?" ORDER BY FIELD(a.productcode,'".$sp_prcode."') ":' order by date desc';
	}
	if(_empty($limit)) $limit = 10;
	$limit = " LIMIT ".$limit;


	$where = (_array($where))?' where '.implode(' and ',$where):'';

	$sql = productQuery();


	$sql.= $where.$ordby.$limit;

	$res = mysql_query($sql,get_db_conn());
	if($res && mysql_num_rows($res)){
		while($row=mysql_fetch_object($res)) array_push($return,$row);
	}
	return $return;
}

// PR SECTION PRODUCT LIST
function _getPrsectionProductList($code='',$special=00,$pagenum=1,$limit=10,$sort=''){
	global $_ShopInfo;
	$isspecial = true;
	$return = array();
	$where = array();
	if(_isInt($special)){
		if(!_empty($code) && preg_match("/^[0-9]{12}$/",$code)){
			$sql = "SELECT special_list FROM tblspecialcode WHERE code='".$code."' AND special='".$special."' ";
		}else{
			$sql = "SELECT special_list FROM tblspecialmain WHERE special='".$special."' ";
		}
		$res=mysql_query($sql,get_db_conn());
		if($res && mysql_num_rows($res)){
			$sp_prcode="";
			if($row=mysql_fetch_object($res)){
				$sp_prcode=ereg_replace(',','\',\'',$row->special_list);
			}
		}
		array_push($where,"a.productcode IN ('".$sp_prcode."')");
	}else{
		$isspecial = false;
		if(!_empty($code) && preg_match("/^[0-9]{3,12}$/",$code)){
			while(substr($code,-3,3) == '000') $code = substr($code,0,-3);
			array_push($where,"a.productcode like '".$code."%'");
		}
	}

	array_push($where,"a.display='Y'");
	array_push($where,"(a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."')");

	if(!_empty($sort)){// && preg_match('/^([a-zA-Z0-9_]+)_(asc|desc)$/',$sort,$mat)){
		$mat = explode('_',$sort);
		switch($mat[0]){
			case 'new':
				$mat[0] = 'regdate';
				break;
			case 'best':
				$mat[0] = 'sellcount';
				break;
			case 'name':
				$mat[0] = 'productname';
				break;
			case 'price':
				$mat[0] = 'sellprice';
				break;
			case 'reserve':
				$mat[0] = 'reservesort';
				break;
		}
		$ordby = ' order by '.$mat[0].' '.$mat[1];
	}else{
		$ordby = ($isspecial)?" ORDER BY FIELD(a.productcode,'".$sp_prcode."') ":' order by date desc';
	}
	if(_empty($limit)) $limit = 10;
	$limit = " LIMIT ".($limit * ($pagenum -1)).",".$limit;
	//$limit = " LIMIT ".$limit;


	$where = (_array($where))?' where '.implode(' and ',$where):'';

	$sql = productQuery();

	$sql.= $where.$ordby.$limit;

	$res = mysql_query($sql,get_db_conn());
	if($res && mysql_num_rows($res)){
		while($row=mysql_fetch_object($res)) array_push($return,$row);
	}

	//echo $sql;
	return $return;
}

// 상품 개별 할인율 가져 오기
function getProductDiscount($productcode){
	global $_ShopInfo;
	$discountprice = 0;
	if(!_empty($productcode) && preg_match('/^[0-9]{18}$/',$productcode)){
		$sql = "SELECT discountYN,discountrates,discountprices FROM tblmemberdiscount WHERE productcode='".$productcode."' AND group_code='".$_ShopInfo->getMemgroup()."' limit 1";
		$res = mysql_query($sql,get_db_conn());
		if($res && mysql_num_rows($res) == 1){
			$dRow = mysql_fetch_object($res);
			if( $dRow->discountYN == "Y" ) $discountprice = intval($dRow->discountprices);
		}
	}
	return ($discountprice > 0)?$discountprice:0;

}


function _getMultiCategory($productcode){
	$result = array();
	if(preg_match('/^[0-9]{18}$/',$productcode)){
		$sql = "select categorycode from tblcategorycode where productcode='".$productcode."'";
		if(false !== $res = mysql_query($sql,get_db_conn())){
			while($row = mysql_fetch_assoc($res)){
				array_push($result,$row['categorycode']);
			}
			//array_unique($result);
		}
	}
	return $result;
}

////////// 카테고리 컨트롤




// 카테고리 옵션 권한 Array ( [coupon] => Y/N, [reserve] => Y/N, [gift] => Y/N )
function categoryAuth ( $productcode ) {
	global $_ShopInfo;

	// 도매회원 미적용 -----------------------------------------------------------------------
	if(isSeller() == 'Y'){
		$AUTH = array(
			'coupon' => "N", // 쿠폰 사용 권한
			'reserve' => "N", // 적립금 사용 권한
			'gift' => "N", // 사은품 적용 권한
			'refund' => "N" // 교환 환불 적용 권한
		);
		return $AUTH;
	}

	$AUTH = array(
		'coupon' => "Y", // 쿠폰 사용 권한
		'reserve' => "Y", // 적립금 사용 권한
		'gift' => "Y", // 사은품 적용 권한
		'refund' => "Y" // 교환 환불 적용 권한
	);

	// 카테고리별 사용 권한 ----------------------------------------------------------------------- 1

	$row = cateAuth ( $productcode );	
	$AUTH['coupon']		=	($row->coupon=="N")?"N":$AUTH['coupon'];
	$AUTH['reserve']	=	($row->reserve=="N")?"N":$AUTH['reserve'];
	$AUTH['gift']			=	($row->gift=="N")?"N":$AUTH['gift'];
	$AUTH['refund']			=	($row->refund=="N")?"N":$AUTH['refund'];


	// 상품별 사용 권한 ----------------------------------------------------------------------- 2
	if(preg_match('/^[0-9]{18}$/',$productcode)){
		$row = productAuth ( $productcode );
		
		$AUTH['coupon']		=	($row->coupon=="Y")?"N":$AUTH['coupon'];
		$AUTH['reserve']	=	($row->reserve=="Y")?"N":$AUTH['reserve'];
		$AUTH['gift']			=	($row->gift=="Y")?"N":$AUTH['gift'];
		$AUTH['refund']			=	($row->refund=="Y")?"N":$AUTH['refund'];
	}



	// 회원 그룹별 사용 권한 ----------------------------------------------------------------------- 3
	if ( strlen($_ShopInfo->getMemgroup()) > 0 ) {

		$row = memberGroupAuth( $_ShopInfo->getMemgroup() );

		$AUTH['coupon']		=	($row->coupon=="N")?"N":$AUTH['coupon'];
		$AUTH['reserve']	=	($row->reserve=="N")?"N":$AUTH['reserve'];
		$AUTH['gift']			=	($row->gift=="N")?"N":$AUTH['gift'];
		$AUTH['refund']			=	($row->refund=="N")?"N":$AUTH['refund'];
	}

	return $AUTH;
}


// 카테고리별 사용 권한
function cateAuth ( $productCode ) {

	$cate = categorySubTab ( $productCode );

	$sql = "
		SELECT
			`iscoupon` as coupon, `isreserve` as reserve, `isgift` as gift,`isrefund` as refund
		FROM
			`tblproductcode`
		WHERE
			`codeA`='".$cate['codeA']."'
			AND
			`codeB`='".$cate['codeB']."'
			AND
			`codeC`='".$cate['codeC']."'
			AND
			`codeD`='".$cate['codeD']."'
		LIMIT 1;
	";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);

	return $row;
}


// 상품별 사용 권한
function productAuth ( $productCode ) {
	$sql = "
		SELECT
			`etcapply_coupon` as coupon, `etcapply_reserve` as reserve, `etcapply_gift` as gift,`etcapply_return` as refund
		FROM
			`tblproduct`
		WHERE
			`productcode`='".$productCode."'
		LIMIT 1;
	";
	
//	echo $sql;
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);

	return $row;
}


// 상품별 사용 권한
function productAuth2($productCode) {
	$sql = "
		SELECT
			`etcapply_coupon` as coupon, `etcapply_reserve` as reserve, `etcapply_gift` as gift,`etcapply_return` as refund
		FROM
			`tblproduct`
		WHERE
			`productcode`='".$productCode."'
		LIMIT 1;
	";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);

	$AUTH = array(
		'coupon' => "Y", // 쿠폰 사용 권한
		'reserve' => "Y", // 적립금 사용 권한
		'gift' => "Y", // 사은품 적용 권한
		'refund' => "Y" // 교환/ 환불 적용 권한
	);
	//return $row;

	$AUTH['coupon']		=	($row->coupon=="Y")?"N":$AUTH['coupon'];
	$AUTH['reserve']	=	($row->reserve=="Y")?"N":$AUTH['reserve'];
	$AUTH['gift']			=	($row->gift=="Y")?"N":$AUTH['gift'];
	$AUTH['refund']			=	($row->refund=="Y")?"N":$AUTH['refund'];
	return $AUTH;
}


// 회원 그룹별 사용 권한
function memberGroupAuth ( $groupCode ) {
	$sql = "
		SELECT
			`group_apply_coupon` as coupon, `group_apply_reserve` as reserve, `group_apply_gift` as gift
		FROM
			`tblmembergroup`
		WHERE
			`group_code` = '".$groupCode."'
		LIMIT 1;
	";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);

	return $row;
}


// 카테고리 그룹 권한
function categoryMemberGroup () {
	global $_ShopInfo;
	$sql = "SELECT codeA, codeB, codeC, codeD FROM tblproductcode ";
	if(strlen($_ShopInfo->getMemid())==0) {
		$sql.= "WHERE group_code!='' ";
	} else {
		$sql.= "WHERE group_code NOT LIKE '%".$_ShopInfo->getMemgroup()."%' AND group_code!='' ";
	}
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		$tmpcode=$row->codeA;
		if($row->codeB!="000") $tmpcode.=$row->codeB;
		if($row->codeC!="000") $tmpcode.=$row->codeC;
		if($row->codeD!="000") $tmpcode.=$row->codeD;
		$codeArray=array_push($tmpcode);
	}
	mysql_free_result($result);

	return $codeArray;
}



// 카테고리의 그룹 접근 권한
function categoryGroupAuth ( $A, $B, $C, $D ) {
	global $_ShopInfo;

	$_cdata="";
	$sql = "SELECT * FROM tblproductcode WHERE codeA='".$A."' AND codeB='".$B."' AND codeC='".$C."' AND codeD='".$D."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {

		//접근가능권한그룹 체크
		if($row->group_code=="NO") {
			echo "<html></head><body onload=\"location.href='/main/main.php'\"></body></html>";exit;
		}
		if(strlen($_ShopInfo->getMemid())==0) {
			if(strlen($row->group_code)>0) {
				echo "<html></head><body onload=\"location.href='/front/login.php?chUrl=".getUrl()."'\"></body></html>";exit;
			}
		} else {
			//if($row->group_code!="ALL" && strlen($row->group_code)>0 && $row->group_code!=$_ShopInfo->getMemgroup()) {
			if(strlen($row->group_code)>0 && strpos($row->group_code,$_ShopInfo->getMemgroup())===false) {	//그룹회원만 접근
				echo "<html></head><body onload=\"alert('해당 카테고리 접근권한이 없습니다.');location.href='/main/main.php'\"></body></html>";exit;
			}
		}
		return $row;

	} else {
		echo "<html></head><body onload=\"location.href='/main/main.php'\"></body></html>";exit;
	}

}


// 카테고리 나누기(category, codeA, codeB, codeC, codeD )
// 옵션은 해당 코드값을 바로 리턴
// 옵션 없을경우 전체를 배열로 리턴
function categorySubTab ( $category, $opt = "ALL" ){
	$categoryTab = array();
	$category = trim($category);
	$categoryTab['depth'] = 4;

	for($i=0;$i<4;$i++){
		$tmp = '000';
		if($i < $categoryTab['depth']){
			if(preg_match('/^[0-9]{3}$/',substr($category,$i*3,3),$mat)) $tmp = $mat[0];
			if($tmp == '000') $categoryTab['depth'] = $i;
		}
		$categoryTab['code'.chr(65+$i)] = $tmp;
		$categoryTab['category'] .= $tmp;
	}
	// 옵션이 있을 경우
	if( $opt != "ALL" ) {
		$categoryTab = $categoryTab[$opt];
	}
	return $categoryTab;
}



// 카테고리 번호에 뒷자리 000 자르기
function categorySubTabShort ( $category) {

	foreach ( categorySubTab($category) as $key => $var ) {
		${$key}=$var;
	}

	$categoryTab=$codeA;
	if($codeB!="000") $categoryTab.=$codeB;
	if($codeC!="000") $categoryTab.=$codeC;
	if($codeD!="000") $categoryTab.=$codeD;

	return $categoryTab;
}











////////// 상품 컨트롤



// 상품 리스트
function productListArray ( $category = '', $sort='production_desc', $limit = 4, $specialCode='' ) {
	global $_ShopInfo,$qry;
	if( !_empty($category) ) {

		$returnArray = "";


		// 카테고리 나누기(category, codeA, codeB, codeC, codeD )
		foreach ( categorySubTab($category) as $key => $var ) {
			${$key}=$var;
		}

		// 회원접근 가능한 카테고리 리스트
		$tmpcode = categoryMemberGroup();

		$not_qry = "";
		foreach ( $tmpcode as $var ) {
			$not_qry .= "AND a.productcode NOT LIKE '".$var."%' ";
		}

		if( !_empty($specialCode) ) {
			$sql = "SELECT special_list FROM tblspecialcode ";
			$sql.= "WHERE code='".$category."' AND special='".$specialCode."' ";
			$result=mysql_query($sql,get_db_conn());
			$sp_prcode="";
			$sp_list="";
			if($row=mysql_fetch_object($result)) {
				$sp_prcode=ereg_replace(',','\',\'',$row->special_list);
			}
			mysql_free_result($result);
		}

		if(strlen($sp_prcode)>0) {
			$sql = "SELECT a.productcode, a.productname, ";
			$sql.= (isSeller() == 'Y')?"a.productdisprice as sellprice,":"a.sellprice, a.productdisprice,";
			$sql.= " a.quantity, ";
			$sql.= "a.tinyimage, a.date, a.etctype, a.reserve, a.reservetype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
			$sql.= "FROM tblproduct AS a ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
			$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
			if(strlen($not_qry)>0) {
				$sql.= $not_qry." ";
			}
			$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
			$sql.= "LIMIT ".$limit.";       ";

			$returnArray = $sql;
		} else {



			$_cdata = categoryGroupAuth ( $codeA, $codeB, $codeC, $codeD );

			// 정열 - 번호, 사진, 상품명, 제조사, 가격
			$tmp_sort=explode("_",$sort);

			if(isSeller() == 'Y'){

				if($tmp_sort[0]=="reserve") {
					$addsortsql=",IF(a.reservetype='N',a.reserve*1,a.reserve*a.productdisprice*0.01) AS reservesort ";
				}
				$sql = "SELECT a.productcode, a.productname, a.productdisprice as sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
				if($_cdata->sort=="date2") $sql.="IF(a.quantity<=0,'11111111111111',a.date) as date, ";
				$sql.= "a.tinyimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
				$sql.= $addsortsql;
				$sql.= "FROM tblproduct AS a ";
				$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
				$sql.= $qry." ";
				$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
				if(strlen($not_qry)>0) {
					$sql.= $not_qry." ";
				}
				if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
				else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
				else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.productdisprice ".$tmp_sort[1]." ";
				else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
				else if($tmp_sort[0]=="regdate") $sql.= "ORDER BY regdate ".$tmp_sort[1]." ";
				else {
					if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
						if(eregi("T",$_cdata->type) && strlen($t_prcode)>0) {
							$sql.= "ORDER BY FIELD(a.productcode,'".$t_prcode."'),date DESC ";
						} else {
							$sql.= "ORDER BY date DESC ";
						}
					} else if($_cdata->sort=="productname") {
						$sql.= "ORDER BY a.productname ";
					} else if($_cdata->sort=="production") {
						$sql.= "ORDER BY a.production ";
					} else if($_cdata->sort=="price") {
						$sql.= "ORDER BY a.productdisprice ";
					}
				}
			} else {

				if($tmp_sort[0]=="reserve") {
					$addsortsql=",IF(a.reservetype='N',a.reserve*1,a.reserve*a.sellprice*0.01) AS reservesort ";
				}
				$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
				if($_cdata->sort=="date2") $sql.="IF(a.quantity<=0,'11111111111111',a.date) as date, ";
				$sql.= "a.tinyimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
				$sql.= $addsortsql;
				$sql.= "FROM tblproduct AS a ";
				$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
				$sql.= $qry." ";
				$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
				if(strlen($not_qry)>0) {
					$sql.= $not_qry." ";
				}
				if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
				else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
				else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
				else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
				else if($tmp_sort[0]=="regdate") $sql.= "ORDER BY regdate ".$tmp_sort[1]." ";
				else {
					if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
						if(eregi("T",$_cdata->type) && strlen($t_prcode)>0) {
							$sql.= "ORDER BY FIELD(a.productcode,'".$t_prcode."'),date DESC ";
						} else {
							$sql.= "ORDER BY date DESC ";
						}
					} else if($_cdata->sort=="productname") {
						$sql.= "ORDER BY a.productname ";
					} else if($_cdata->sort=="production") {
						$sql.= "ORDER BY a.production ";
					} else if($_cdata->sort=="price") {
						$sql.= "ORDER BY a.sellprice ";
					}
				}
			}

			//$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
			//$result=mysql_query($sql,get_db_conn());
			$returnArray = $sql;
		}


		// 리턴 - 상품코드,상품명,브랜드,원가,판매가,적립금,대표이미지(소)
		return $returnArray;

	}
}



if(!function_exists('getCodeLoc')){
// 현재 카테고리 위치
function getCodeLoc($code,$color1="9E9E9E",$color2="9E9E9E") {
	global $_ShopInfo, $Dir;
	$code_loc = "<A HREF=\"".$Dir.MainDir."main.php\"><FONT COLOR=\"".$color1."\">홈</FONT></A> <FONT COLOR=\"".$color1."\">></FONT> ";
	$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
	$sql.= "WHERE codeA='".substr($code,0,3)."' ";
	if(substr($code,3,3)!="000") {
		$sql.= "AND (codeB='".substr($code,3,3)."' OR codeB='000') ";
		if(substr($code,6,3)!="000") {
			$sql.= "AND (codeC='".substr($code,6,3)."' OR codeC='000') ";
			if(substr($code,9,3)!="000") {
				$sql.= "AND (codeD='".substr($code,9,3)."' OR codeD='000') ";
			} else {
				$sql.= "AND codeD='000' ";
			}
		} else {
			$sql.= "AND codeC='000' ";
		}
	} else {
		$sql.= "AND codeB='000' AND codeC='000' ";
	}
	$sql.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
	$result=mysql_query($sql,get_db_conn());
	$i=0;
	while($row=mysql_fetch_object($result)) {
		$tmpcode=$row->codeA.$row->codeB.$row->codeC.$row->codeD;
		if($i>0) $code_loc.= " <FONT COLOR=\"".$color1."\">></FONT> ";
		if($code==$tmpcode) {
			$code_loc.="<A HREF=\"".$Dir.FrontDir."productlist.php?code=".$tmpcode."\"><FONT COLOR=\"".$color2."\"><B>".$row->code_name."</B></FONT></A>";
		} else {
			$code_loc.="<A HREF=\"".$Dir.FrontDir."productlist.php?code=".$tmpcode."\"><FONT COLOR=\"".$color1."\">".$row->code_name."</FONT></A>";
		}
		$code_loc.= $_tmp;
		$i++;
	}
	mysql_free_result($result);
	return $code_loc;
}
}

// 상품 정보 고시 항목 수정
function _editProductDetails($pridx=0,$param=array()){
	if(_isInt($pridx)){
		$conn = get_db_conn();
		$sql = "select didx from tblproduct_detail where pridx='".$pridx."'";
		if(false !== $res = mysql_query($sql,$conn)){
			$didxs = array();
			while($itm = mysql_fetch_assoc($res)) if(_isInt($itm['didx']))  array_push($didxs,$itm['didx']);
		}
		foreach($param as $item){
			if(isset($item['didx']) && _isInt($item['didx']) && false !== $pt = array_search($item['didx'],$didxs)){
				$sql = "update tblproduct_detail set dtitle="._escape($item['dtitle']).",dcontent="._escape($item['dcontent'])." where pridx='".$pridx."' and didx='".$item['didx']."'";
				unset($didxs[$pt]);
			}else{
				$sql = "insert into tblproduct_detail set pridx='".$pridx."', dtitle="._escape($item['dtitle']).",dcontent="._escape($item['dcontent']);
			}
			mysql_query($sql,$conn);
		}
		if(_array($didxs)){
			$sql = "delete from tblproduct_detail where pridx='".$pridx."' and didx in ('".implode("','",$didxs)."')";
			mysql_query($sql,$conn);
		}
	}
}



// 상품 정보 고시 항목 삭제
function _deleteProductDetails($pridx=0){
	if(_isInt($pridx)){
		$conn = get_db_conn();
		$sql = "delete from tblproduct_detail where pridx='".$pridx."'";
		mysql_query($sql,$conn);
	}
}

// $type = image > 이미지 배열, val => 값 ( N 일 경우가 적립금등이 없는 경우임) , all => 2가지 전부로 반환
function _getEtcImg($productcode,$type='image'){
	global $_ShopInfo;
	$return = array();
	$chkItems = array('coupon'=>'Y','reserve'=>'Y','gift'=>'Y','return'=>'Y');
	if(preg_match('/^[0-9]{18}$/',$productcode)){
		$sql ="SELECT * FROM tblproduct WHERE productcode='".$productcode."' limit 1";
		if(false !== $res =mysql_query($sql,get_db_conn())){
			if(mysql_num_rows($res) == 1){
				$tmp = mysql_fetch_assoc($res);
				foreach($chkItems as $key=>$val) if($tmp['etcapply_'.$key] == 'Y') $chkItems[$key] = 'N';
			}
		}

		if(strlen($_ShopInfo->getMemid())>0) {
			$sql = "SELECT g.* from tblmembergroup g left join tblmember m on (m.group_code = g.group_code) WHERE m.id='".$_ShopInfo->getMemid()."'";
			if(false !== $res =mysql_query($sql,get_db_conn())){
				if(mysql_num_rows($res) == 1){
					$tmp = mysql_fetch_assoc($res);
					foreach($chkItems as $key=>$val){
						if($key == 'return') continue;
						else if($val != 'N' && $tmp['group_apply_'.$key] == 'N') $chkItems[$key] = 'N';
					}
				}
			}
		}
	}
	$i=1;
	if($type != 'val'){
		foreach($chkItems as $key=>$val){
			$imgname = 'btn_'.(($val == 'N')?'no':'yes').sprintf('%02d',$i++).'.jpg';
			if($val == 'N') $return[$key] = '<img src="/images/newbasket/'.$imgname.'" />';
		}
	}
	if($type == 'val'){
		return $chkItems;
	}else if($type == 'all'){
		 return array('img'=>$return,'val'=>$chkItems);
	}else {
		return $return;
	}
}

/*
상품에 대한 사은품 및 교환 환불 등에 관한 정보를 반환
연관 배열 형태로 반환 array('coupon'=>'Y','reserve'=>'Y','gift'=>'Y','return'=>'Y');
coupon : 쿠폰 , reserve : 적립금 사용 , gift : 사은품 , return : 교환 및 환불
각키의 값이 Y 일 경우는 허용 N 일 경우는 불가
만약 두번째 인자로 연관 키를 전달 할경우 해당 키의 값 만을 반환
*/
function getProductAbleInfo($productcode,$checkVal=""){
	global $_ShopInfo;
	if(isSeller() == 'Y'){
		$return = array('coupon'=>'N','reserve'=>'N','gift'=>'N','return'=>'Y');
		return $return;
	}
	$return = array('coupon'=>'Y','reserve'=>'Y','gift'=>'Y','return'=>'Y');

	if(preg_match('/^[0-9]{18}$/',$productcode)){
		$sql ="SELECT * FROM tblproduct WHERE productcode='".$productcode."' limit 1";
		if(false !== $res =mysql_query($sql,get_db_conn())){
			if(mysql_num_rows($res) == 1){
				$tmp = mysql_fetch_assoc($res);
				foreach($return as $key=>$val) if($tmp['etcapply_'.$key] == 'Y') $return[$key] = 'N';
			}
		}
		if(strlen($_ShopInfo->getMemid())>0) {
			$sql = "SELECT g.* from tblmembergroup g left join tblmember m on (m.group_code = g.group_code) WHERE m.id='".$_ShopInfo->getMemid()."'";
			if(false !== $res =mysql_query($sql,get_db_conn())){
				if(mysql_num_rows($res) == 1){
					$tmp = mysql_fetch_assoc($res);
					foreach($return as $key=>$val){
						if($key == 'return') continue;
						else if($val != 'N' && $tmp['group_apply_'.$key] == 'N') $return[$key] = 'N';
					}
				}
			}
		}
	}else{
		$return = array('coupon'=>'N','reserve'=>'N','gift'=>'N','return'=>'N');
	}
	if(!_empty($checkVal) && isset($return[$checkVal])) return $return[$checkVal];
	else return $return;
}

/*
#####################상품별 회원할인율#######################################
function _getGroupDiscountPrice($productcode){
	$dSql = "SELECT discountrates,discountprices,over_discount FROM tblmemberdiscount ";
	$dSql .= "WHERE productcode='".$row->productcode."' AND group_code='".$_ShopInfo->getMemgroup()."'";
	$dResult = mysql_query($dSql,get_db_conn());
	$dRow = mysql_fetch_object($dResult);

	$discountprices = $dRow->discountprices;
	if($discountprices>0){
		$row->sellprice = $row->sellprice - $dRow->discountprices;
	}
}
*/
// 상품 정보 고시 내용을 배열로 반환
function _getProductDetails($pridx=0){
	$result = array();
	if(_isInt($pridx)){
		$sql = "select * from tblproduct_detail where pridx='".$pridx."' order by didx";
		if(false !== $res = mysql_query($sql,get_db_conn())){
			while($row = mysql_fetch_assoc($res)) array_push($result,$row);
		}
	}
	return $result;
}


// 상품 정보 고시 템플릿 정보 호출용
function _productGosiInfo($idx){
	$tplarr = _productGosiInfoArr();
	$return = array();
	if(_isInt($idx,true)){
		$return = $tplarr[$idx]['items'];
	}else{
		foreach($tplarr as $idx=>$val){
			array_push($return,array('idx'=>$idx,'title'=>$val['title']));
		}
	}
	return $return;
}

// 상품 정보 고시 관련 세부 항목 처리용 배열 반환 함수
function _productGosiInfoArr($idx){
	$infoarr = array();
	$infoitem = array('title'=>'','items'=>array());
	//$infoDitem = array('title'=>'','desc'=>'');


	$tmp = $infoitem;
	$tmp['title']='의류';
	array_push($tmp['items'],array('title'=>'제품 소재','desc'=>'섬유의 조성 또는 혼용률을 백분율로 표시, 기능성인 경우 성적서 또는 허가서'));
	array_push($tmp['items'],array('title'=>'색상','desc'=>''));
	array_push($tmp['items'],array('title'=>'치수','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'세탁방법 및 취급시 주의사항','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조연월','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='구두/신발';
	array_push($tmp['items'],array('title'=>'제품 소재','desc'=>'운동화인 경우에는 겉감, 안감을 구분하여 표시'));
	array_push($tmp['items'],array('title'=>'색상','desc'=>''));
	array_push($tmp['items'],array('title'=>'치수','desc'=>'발길이: 해외사이즈 표기시 국내사이즈 병행 표기(mm)<br>굽높이 ( 굽 재료를 사용하는 여성화에 한함,cm)'));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'취급시 주의사항','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);



	$tmp = $infoitem;
	$tmp['title']='가방';
	array_push($tmp['items'],array('title'=>'종류','desc'=>''));
	array_push($tmp['items'],array('title'=>'소재','desc'=>''));
	array_push($tmp['items'],array('title'=>'색상','desc'=>''));
	array_push($tmp['items'],array('title'=>'크기','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'취급시 주의사항','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='패션잡화 (모자/벨트/액세서리)';
	array_push($tmp['items'],array('title'=>'종류','desc'=>''));
	array_push($tmp['items'],array('title'=>'소재','desc'=>''));
	array_push($tmp['items'],array('title'=>'치수','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'취급시 주의사항','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='침구류/커튼';
	array_push($tmp['items'],array('title'=>'제품 소재','desc'=>'섬유의 조성 또는 혼용률을 백분율로 표시<br>충전재를 사용한 제품은 충전재를 함께 표기'));
	array_push($tmp['items'],array('title'=>'색상','desc'=>''));
	array_push($tmp['items'],array('title'=>'치수','desc'=>''));
	array_push($tmp['items'],array('title'=>'제품구성','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'세탁방법 및 취급시 주의사항','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='가구(침대/소파/싱크대/DIY제품)';
	array_push($tmp['items'],array('title'=>'품명','desc'=>''));
	array_push($tmp['items'],array('title'=>'KC 인증 필 유무','desc'=>'품질경영 및 공산품안전관리법 상 안전-품질표시대상공산품에 한함'));
	array_push($tmp['items'],array('title'=>'색상','desc'=>''));
	array_push($tmp['items'],array('title'=>'구성품','desc'=>''));
	array_push($tmp['items'],array('title'=>'주요 소재','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)<br>* 구성품 별 제조자가 다른 경우 각 구성품의 제조자,수입자'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>'* 구성품 별 제조국이 다른 경우 각 구선품의 제조국'));
	array_push($tmp['items'],array('title'=>'크기','desc'=>''));
	array_push($tmp['items'],array('title'=>'배송/설치비용','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='영상가전(TV류)';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'전기용품 안전인증 필 유무','desc'=>'(전기용품안전관리법 상 안전인증대상전기용품, 자율안전확인대상전기용품,공급자적합성확인대상전기용품에 한함)'));
	array_push($tmp['items'],array('title'=>'정격전압/소비전력/에너지소비효율등급','desc'=>'(에너지이용합리화법 상 의무대상상품에 한함)'));
	array_push($tmp['items'],array('title'=>'동일모델의 출시년월','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'크기','desc'=>'형태포함'));
	array_push($tmp['items'],array('title'=>'화면사양','desc'=>'크기,해상도,화면비율 등'));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='가정용 전기제품(냉장고/세탁기/식기세척기/전자레인지)';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'전기용품 안전인증 필 유무','desc'=>'(전기용품안전관리법 상 안전인증대상전기용품, 자율안전확인대상전기용품,공급자적합성확인대상전기용품에 한함)'));
	array_push($tmp['items'],array('title'=>'정격전압/소비전력/에너지소비효율등급','desc'=>'(에너지이용합리화법 상 의무대상상품에 한함)'));
	array_push($tmp['items'],array('title'=>'동일모델의 출시년월','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'크기','desc'=>'용량,형태포함'));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='계절가전(에어컨/온풍기)';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'전기용품 안전인증 필 유무','desc'=>'(전기용품안전관리법 상 안전인증대상전기용품, 자율안전확인대상전기용품,공급자적합성확인대상전기용품에 한함)'));
	array_push($tmp['items'],array('title'=>'정격전압/소비전력/에너지소비효율등급','desc'=>'(에너지이용합리화법 상 의무대상상품에 한함)'));
	array_push($tmp['items'],array('title'=>'동일모델의 출시년월','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'크기','desc'=>'형태 및 실외기 포함'));
	array_push($tmp['items'],array('title'=>'냉난방면적','desc'=>''));
	array_push($tmp['items'],array('title'=>'추가설치비용','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='사무용기기(컴퓨터/노트북/프린터)';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'KCC 인증 필 유무','desc'=>'전파법 상 인증대상상품에 한함,MIC 인증 필 혼용 가능'));
	array_push($tmp['items'],array('title'=>'정격전압/소비전력/에너지소비효율등급','desc'=>'(에너지이용합리화법 상 의무대상상품에 한함)'));
	array_push($tmp['items'],array('title'=>'동일모델의 출시년월','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'크기/무게','desc'=>'무게는 노트북에 한함'));
	array_push($tmp['items'],array('title'=>'주요 사양','desc'=>'컴퓨터와 노트북의 경우 성능,용량,운영체제 포함여부 등  / 프린터의 경우 인쇄 속도 등)'));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='광학기기(디지털카메라/캠코더)';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'KCC 인증 필 유무','desc'=>'전파법 상 인증대상상품에 한함,MIC 인증 필 혼용 가능'));
	array_push($tmp['items'],array('title'=>'동일모델의 출시년월','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'크기/무게','desc'=>''));
	array_push($tmp['items'],array('title'=>'주요 사양','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='소형전자(MP3/전자사전 등)';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'KC 인증 필 유무','desc'=>'전파법 상 인증대상상품에 한함,MIC 인증 필 혼용 가능'));
	array_push($tmp['items'],array('title'=>'정격전압/소비전력','desc'=>''));
	array_push($tmp['items'],array('title'=>'동일모델의 출시년월','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'크기/무게','desc'=>''));
	array_push($tmp['items'],array('title'=>'주요 사양','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='휴대폰';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'KCC 인증 필 유무','desc'=>'전파법 상 인증대상상품에 한함,MIC 인증 필 혼용 가능'));
	array_push($tmp['items'],array('title'=>'동일모델의 출시년월','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'크기/무게','desc'=>''));
	array_push($tmp['items'],array('title'=>'이동통신 가입조건','desc'=>'1.이동통신사<br>2.가입절차<br>3.소비자의 추가적인 부담사항( 가입비,유심카드 구입비 등 추가로 부담하여야 할 금액, 부가서비스, 의무사용기간, 위약금 등)'));
	array_push($tmp['items'],array('title'=>'주요 사양','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='네비게이션';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'KCC 인증 필 유무','desc'=>'전파법 상 인증대상상품에 한함,MIC 인증 필 혼용 가능'));
	array_push($tmp['items'],array('title'=>'정격전압/소비전력','desc'=>''));
	array_push($tmp['items'],array('title'=>'동일모델의 출시년월','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'크기/무게','desc'=>''));
	array_push($tmp['items'],array('title'=>'주요 사양','desc'=>''));
	array_push($tmp['items'],array('title'=>'맵 업데이트 비용 및 무상기간','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='자동차용품(자동차부품/기타 자동차용품)';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'동일모델의 출시년월','desc'=>''));
	array_push($tmp['items'],array('title'=>'자동차 부품 자기인증 유무','desc'=>'자동차관리법에 따른 대상 자동차부품에 한함'));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'크기','desc'=>''));
	array_push($tmp['items'],array('title'=>'적용차종','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='의료기기';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'의료기기법상 허가번호','desc'=>'허가-신고 대상 의료기기에 한함'));
	array_push($tmp['items'],array('title'=>'광고사전심의필 유무','desc'=>''));
	array_push($tmp['items'],array('title'=>'KC 인증 필 유무','desc'=>'전기용품안전관리법상 안전인증 또는 자율안전확인 대상 전기 용품에 한함'));
	array_push($tmp['items'],array('title'=>'정격전압/소비전력','desc'=>'전기용품에 한함'));
	array_push($tmp['items'],array('title'=>'동일모델의 출시년월','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'제품의 사용목적 및 사용방법','desc'=>''));
	array_push($tmp['items'],array('title'=>'취급시 주의사항','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='주방용품';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'재질','desc'=>''));
	array_push($tmp['items'],array('title'=>'구성품','desc'=>''));
	array_push($tmp['items'],array('title'=>'크기','desc'=>''));
	array_push($tmp['items'],array('title'=>'동일모델의 출시년월','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'수입 기구/용기','desc'=>'식품위생법에 따른 수입 기구/용기의 경우 "식품위생법에 따른 수입신고를 필함"의 문구를 입력하세요.'));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='화장품';
	array_push($tmp['items'],array('title'=>'용량/중량','desc'=>''));
	array_push($tmp['items'],array('title'=>'제품 주요 사양','desc'=>'(피부타입,색상(호,번) 등)'));
	array_push($tmp['items'],array('title'=>'사용기한 또는 개봉 후 사용기간','desc'=>''));
	array_push($tmp['items'],array('title'=>'사용방법','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자 및 제조판매업자','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'주요성분','desc'=>'유지농 화장품의 경우 유기농 원료 함량 포함'));
	array_push($tmp['items'],array('title'=>'기능성 확장품 심사필 유무','desc'=>'기능성 화장품의 경우 화장품법에 따른 식품의약품안전청 심사 필 유무(미백,주름개선,자외선차단 등)'));
	array_push($tmp['items'],array('title'=>'사용할 때 주의사항','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'소비자상담관련 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='귀금속/보석/시계류';
	array_push($tmp['items'],array('title'=>'소재/순도/밴드재질(시계의 경우)','desc'=>''));
	array_push($tmp['items'],array('title'=>'중량','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>'원산지와 가공지 등이 다를 경우 함께 표기'));
	array_push($tmp['items'],array('title'=>'치수','desc'=>''));
	array_push($tmp['items'],array('title'=>'착용 시 주의사항','desc'=>''));
	array_push($tmp['items'],array('title'=>'주요 사양','desc'=>'1. 귀금속,보석류 - 등급<br>2.시계 - 기능,방수 등'));
	array_push($tmp['items'],array('title'=>'보증서 제공여부','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='식품(농수산물)';
	array_push($tmp['items'],array('title'=>'포장단위별 용량(중량)/수량/크기','desc'=>''));
	array_push($tmp['items'],array('title'=>'생산자/수입자','desc'=>'수입품의 경우 수입자를 함께 표기'));
	array_push($tmp['items'],array('title'=>'원산지','desc'=>'농수산물의 원산지 표시에 관한 법률에 따른 원산지'));
	array_push($tmp['items'],array('title'=>'제조연월일','desc'=>'포장일 또는 생산연도'));
	array_push($tmp['items'],array('title'=>'유통기한 또는 품질유지기한','desc'=>''));
	array_push($tmp['items'],array('title'=>'관련법상 표시사항','desc'=>'1.농산물 - 농산물품질관리법상 유전자변형농산물 표시, 지리적표시<br>2.축산물 - 축산법에 따른 등급 표시, 쇠고기의 경우 이력관리에 따른 표시 유무<br>3.수산물 - 수산물품질관리법상 유전자변형수산물 표시, 지리적표시<br>4.수입식품에 해당하는 경우 "식품위생법에 따른 수입신고를 필함" 의 문구'));
	array_push($tmp['items'],array('title'=>'상품구성','desc'=>''));
	array_push($tmp['items'],array('title'=>'보관방법 또는 취급방법','desc'=>''));
	array_push($tmp['items'],array('title'=>'소비자상담 관련 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='가공식품';
	array_push($tmp['items'],array('title'=>'식품의 유형','desc'=>''));
	array_push($tmp['items'],array('title'=>'생산자/소재지/수입자','desc'=>'수입품의 경우 수입자를 함께 표기'));
	array_push($tmp['items'],array('title'=>'제조연월일','desc'=>'포장일 또는 생산연도'));
	array_push($tmp['items'],array('title'=>'유통기한 또는 품질유지기한','desc'=>''));
	array_push($tmp['items'],array('title'=>'포장단위별 용량(중량)/수량','desc'=>''));
	array_push($tmp['items'],array('title'=>'원재료명 및 함량','desc'=>'농수산물의 원산지 표시에 관한 법률에 따른 원산지 표시 포함'));
	array_push($tmp['items'],array('title'=>'영양성분','desc'=>'식품위생법에 따른 영양성분 표시대상 식품에 한함'));
	array_push($tmp['items'],array('title'=>'유전자재조합식품 유무','desc'=>'유전자재조합식품에 해당하는 경우의 표시'));
	array_push($tmp['items'],array('title'=>'표시광고 사전심의필','desc'=>'영유아식 또는 체중조절식품 등에 해당하는 경우'));
	array_push($tmp['items'],array('title'=>'수입식품 여부','desc'=>'수입식품에 해당하는 경우 "식품위생법에 따른 수입신고를 필함"의 문구'));
	array_push($tmp['items'],array('title'=>'소비자상담 관련 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='건강기능식품';
	array_push($tmp['items'],array('title'=>'식품의 유형','desc'=>''));
	array_push($tmp['items'],array('title'=>'생산자/소재지/수입자','desc'=>'수입품의 경우 수입자를 함께 표기'));
	array_push($tmp['items'],array('title'=>'제조연월일','desc'=>'포장일 또는 생산연도'));
	array_push($tmp['items'],array('title'=>'유통기한 또는 품질유지기한','desc'=>''));
	array_push($tmp['items'],array('title'=>'포장단위별 용량(중량)/수량','desc'=>''));
	array_push($tmp['items'],array('title'=>'원재료명 및 함량','desc'=>'농수산물의 원산지 표시에 관한 법률에 따른 원산지 표시 포함'));
	array_push($tmp['items'],array('title'=>'영양정보','desc'=>''));
	array_push($tmp['items'],array('title'=>'기능정보','desc'=>''));
	array_push($tmp['items'],array('title'=>'섭취량/섭취방법 및 섭취시 주의사항','desc'=>'* 질병의 예방 및 치료를 위한 의약품이 아니라는 내용의 표현이 들어가야 합니다.<br>ex) 본 제품은 질병의 예방 및 치료를 위한 의약품이 아닙니다.'));
	array_push($tmp['items'],array('title'=>'유전자재조합식품 유무','desc'=>'유전자재조합식품에 해당하는 경우의 표시'));
	array_push($tmp['items'],array('title'=>'표시광고 사전심의필','desc'=>''));
	array_push($tmp['items'],array('title'=>'수입식품 여부','desc'=>'수입식품에 해당하는 경우 "식품위생법에 따른 수입신고를 필함"의 문구'));
	array_push($tmp['items'],array('title'=>'소비자상담 관련 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='영유아용품';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'KC 인증 필','desc'=>'품질경영 및 공산품안전관리법상 안전인증대상 또는 자율안전확인대상 공산품에 한함'));
	array_push($tmp['items'],array('title'=>'크기/중량','desc'=>''));
	array_push($tmp['items'],array('title'=>'색상','desc'=>''));
	array_push($tmp['items'],array('title'=>'재질','desc'=>'섬유의 경우 혼용률'));
	array_push($tmp['items'],array('title'=>'사용연령','desc'=>''));
	array_push($tmp['items'],array('title'=>'동일모델의 출시년월','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'취급방법 및 취급시 주의사항/ 안전표시','desc'=>'주의, 경고 등'));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='악기';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'크기','desc'=>''));
	array_push($tmp['items'],array('title'=>'색상','desc'=>''));
	array_push($tmp['items'],array('title'=>'재질','desc'=>''));
	array_push($tmp['items'],array('title'=>'제품 구성','desc'=>''));
	array_push($tmp['items'],array('title'=>'동일모델의 출시년월','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'상품별 세부 사양','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='스포츠용품';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'크기/중량','desc'=>''));
	array_push($tmp['items'],array('title'=>'색상','desc'=>''));
	array_push($tmp['items'],array('title'=>'재질','desc'=>''));
	array_push($tmp['items'],array('title'=>'제품 구성','desc'=>''));
	array_push($tmp['items'],array('title'=>'동일모델의 출시년월','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>'수입품의 경우 수입자를 함께표기 (병행수입의 경우 병행 수입여부로 대체 가능)'));
	array_push($tmp['items'],array('title'=>'제조국','desc'=>''));
	array_push($tmp['items'],array('title'=>'상품별 세부 사양','desc'=>''));
	array_push($tmp['items'],array('title'=>'품질보증기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='서적';
	array_push($tmp['items'],array('title'=>'도서명','desc'=>''));
	array_push($tmp['items'],array('title'=>'저자/출판사','desc'=>''));
	array_push($tmp['items'],array('title'=>'크기','desc'=>'전자책의 경우 파일의 용량'));
	array_push($tmp['items'],array('title'=>'쪽수','desc'=>'전자책의 경우 제외'));
	array_push($tmp['items'],array('title'=>'제품 구성','desc'=>'전집 또는 세트일 경우 낱권 구성, CD 등'));
	array_push($tmp['items'],array('title'=>'출간일','desc'=>''));
	array_push($tmp['items'],array('title'=>'목차/책소개','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='호텔/펜션 예약';
	array_push($tmp['items'],array('title'=>'국가/지역명','desc'=>''));
	array_push($tmp['items'],array('title'=>'숙소형태','desc'=>''));
	array_push($tmp['items'],array('title'=>'등급/객실타입','desc'=>''));
	array_push($tmp['items'],array('title'=>'사용가능 인원, 인원추가 시 비용','desc'=>''));
	array_push($tmp['items'],array('title'=>'부대시설,제공 서비스','desc'=>'조식 등'));
	array_push($tmp['items'],array('title'=>'취소 규정','desc'=>'환불, 위약금 등'));
	array_push($tmp['items'],array('title'=>'예약담당 연락처','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='여행패키지';
	array_push($tmp['items'],array('title'=>'여행사','desc'=>''));
	array_push($tmp['items'],array('title'=>'이용항공편','desc'=>''));
	array_push($tmp['items'],array('title'=>'여행기간/일정','desc'=>''));
	array_push($tmp['items'],array('title'=>'총 예정 인원, 출발 가능 인원','desc'=>''));
	array_push($tmp['items'],array('title'=>'숙박정보','desc'=>''));
	array_push($tmp['items'],array('title'=>'포함 내역','desc'=>'식사, 인솔자, 공연관람 등'));
	array_push($tmp['items'],array('title'=>'추가 경비 항목과 금액','desc'=>'유류할증료, 공항이용료, 관광지 입장료, 안내원수수료, 식사비용, 선택사항 등'));
	array_push($tmp['items'],array('title'=>'취소 규정','desc'=>'환불, 위약금 등'));
	array_push($tmp['items'],array('title'=>'여행경보단계','desc'=>'해외여행의 경우만 외교통상부가 지정하는 여행경보단계'));
	array_push($tmp['items'],array('title'=>'예약담당 연락처','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='항공권';
	array_push($tmp['items'],array('title'=>'요금조건, 왕복/편도 여부','desc'=>''));
	array_push($tmp['items'],array('title'=>'유효기간','desc'=>''));
	array_push($tmp['items'],array('title'=>'제한사항','desc'=>'출발일, 귀국일 변경가능 여부 등'));
	array_push($tmp['items'],array('title'=>'티켓수령방법','desc'=>''));
	array_push($tmp['items'],array('title'=>'좌석종류','desc'=>''));
	array_push($tmp['items'],array('title'=>'추가 경비 항목과 금액','desc'=>'유류할증료, 공항이용료등'));
	array_push($tmp['items'],array('title'=>'취소 규정','desc'=>'환불, 위약금 등'));
	array_push($tmp['items'],array('title'=>'예약담당 연락처','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='자동차 대여 서비스(렌터카)';
	array_push($tmp['items'],array('title'=>'차종','desc'=>''));
	array_push($tmp['items'],array('title'=>'소유권 이전 조건','desc'=>'소유권이 이전되는 경우에 한함'));
	array_push($tmp['items'],array('title'=>'추가 선택 시 비용','desc'=>'자차면책제도, 내비게이션 등'));
	array_push($tmp['items'],array('title'=>'차량 반환 시 연료대금 정산 방법','desc'=>''));
	array_push($tmp['items'],array('title'=>'차량의 고장/회손 시 소비자 책임','desc'=>''));
	array_push($tmp['items'],array('title'=>'예약 취소 또는 중도 해약 시 환불 기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'소비자상담 관련 전화번호','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='물품대여 서비스(정수기, 비데, 공기청정기 등)';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'소유권 이전 조건','desc'=>'소유권이 이전되는 경우에 한함'));
	array_push($tmp['items'],array('title'=>'유지보수 조건','desc'=>'점검/필터교환 주기, 추가 비용 등'));
	array_push($tmp['items'],array('title'=>'상품의 고장/분실/훼손 시 소비자 책임','desc'=>''));
	array_push($tmp['items'],array('title'=>'중도 해약 시 환불 기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'제품 사양','desc'=>'용량,소비전력등'));
	array_push($tmp['items'],array('title'=>'소비자상담 관련 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='물품대여 서비스(서적,유야용품,행사용품 등)';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'소유권 이전 조건','desc'=>'소유권이 이전되는 경우에 한함'));
	array_push($tmp['items'],array('title'=>'상품의 고장/분실/훼손 시 소비자 책임','desc'=>''));
	array_push($tmp['items'],array('title'=>'중도 해약 시 환불 기준','desc'=>''));
	array_push($tmp['items'],array('title'=>'소비자상담 관련 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='디지털 콘텐츠 (음원, 게임, 인터넷강의 등)';
	array_push($tmp['items'],array('title'=>'제작자/공급자','desc'=>''));
	array_push($tmp['items'],array('title'=>'이용조건/이용기간','desc'=>''));
	array_push($tmp['items'],array('title'=>'상품 제공 방식','desc'=>'CD, 다운로드, 실시간 스트리밍 등'));
	array_push($tmp['items'],array('title'=>'최소 시스템 사양/ 필수 소프트웨어','desc'=>''));
	array_push($tmp['items'],array('title'=>'청약철회 또는 계약의 해제/해지에 따른 효과','desc'=>''));
	array_push($tmp['items'],array('title'=>'소비자 상담 관련 전화번호','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='상품권/쿠폰';
	array_push($tmp['items'],array('title'=>'발행자','desc'=>''));
	array_push($tmp['items'],array('title'=>'유효기간/이용조건','desc'=>'유효기간 경과 시 보상 기준, 사용제한품목 및 기간 등'));
	array_push($tmp['items'],array('title'=>'이용 가능 매장','desc'=>''));
	array_push($tmp['items'],array('title'=>'잔액 환급 조건','desc'=>''));
	array_push($tmp['items'],array('title'=>'소비자상담 관련 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='기타';
	array_push($tmp['items'],array('title'=>'품명 및 모델명','desc'=>''));
	array_push($tmp['items'],array('title'=>'인증/허가 사항','desc'=>'법에 의한 인증/허가 등을 받았음을 확인할 수 있는 경우 그에 대한 사항'));
	array_push($tmp['items'],array('title'=>'제조국/원산지','desc'=>''));
	array_push($tmp['items'],array('title'=>'제조자/수입자','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S 책임자와 전화번호 또는 소비자상담 관련 전화번호','desc'=>''));
	array_push($infoarr,$tmp);

	return $infoarr;
}
function setOptQuantityReg($opt,$option1,$option2){
	$returndata=false;
	if(strlen($opt)>0){
		$opt1_array = (strlen($option1)>0)?explode(",",$option1):array();
		$opt2_array = (strlen($option2)>0)?explode(",",$option2):array();
		$firstloop = (count($opt2_array) >0)?count($opt2_array)-1:1;
		$quantity_array = (strlen($opt)>0)?explode(",",$opt):array();
		$sumoptquantity=0;
		$exceptioncheck= false;
		for($i=0;$i<$firstloop;$i++){
			for($j=1;$j<count($opt1_array);$j++){
				if($quantity_array[($i*10) + $j] ==""){
					$exceptioncheck = true;
				}
				$sumoptquantity +=$quantity_array[($i*10) + $j];
			}
		}
		if($exceptioncheck === false){
			$returndata=$sumoptquantity;
		}else{
			$returndata="NULL";
		}
	}
	return $returndata;
}


function setOptQuantityCheck($param = array()){
	$proccount=0;
	if(_array($param)){
		foreach($param as $prcode){
			$procSQL = "SELECT option_quantity, option1, option2 FROM tblproduct WHERE productcode = '".$prcode."' ";
			if(false !== $procRes = mysql_query($procSQL,get_db_conn())){
				$proccount = mysql_num_rows($procRes);
				if($proccount>0){
					$opt_quantity = mysql_result($procRes,0,0);
					$opt1 = mysql_result($procRes,0,1);
					$opt2 = mysql_result($procRes,0,2);
					$opt1_array = (strlen($opt1)>0)?explode(",",$opt1):array();
					$opt2_array = (strlen($opt2)>0)?explode(",",$opt2):array();
					$quantity_array = (strlen($opt_quantity)>0)?explode(",",$opt_quantity):array();
					$firstloop = (count($opt2_array) >0)?count($opt2_array)-1:1;
					$sumoptquantity = 0;
					$exceptioncheck = false;
					if(_array($opt1_array) && _array($quantity_array)){
						for($i=0;$i<$firstloop;$i++){
							for($j=1;$j<count($opt1_array);$j++){
								if($quantity_array[($i*10) + $j] ==""){
									$exceptioncheck = true;
								}
								$sumoptquantity +=$quantity_array[($i*10) + $j];
							}
						}
						if($exceptioncheck === false){
							$soldoutSQL = "UPDATE tblproduct SET quantity = '".$sumoptquantity."' WHERE productcode = '".$prcode."' ";
							@mysql_query($soldoutSQL,get_db_conn());
						}
					}
				}
				@mysql_free_result($procRes);
			}
		}
	}
}
?>