<?
include_once dirname(__FILE__).'/func.php';
include_once dirname(__FILE__).'/member_func.php';

// ������ ���� ���� �� ���� üũ�� �Լ�
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
	// shopinfo ����ǰ Ȱ��ȭ ���� ȣ��
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
	if(isSeller() == 'Y'){ // ���� ȸ���� ���
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

// ������ ���� Ư�� ��ǰ ( �Ż�ǰ, ��õ ��ǰ �� ) ��ǰ ���� ���� ����.
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

// ��ǰ ���� ������ ���� ����
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

////////// ī�װ� ��Ʈ��




// ī�װ� �ɼ� ���� Array ( [coupon] => Y/N, [reserve] => Y/N, [gift] => Y/N )
function categoryAuth ( $productcode ) {
	global $_ShopInfo;

	// ����ȸ�� ������ -----------------------------------------------------------------------
	if(isSeller() == 'Y'){
		$AUTH = array(
			'coupon' => "N", // ���� ��� ����
			'reserve' => "N", // ������ ��� ����
			'gift' => "N", // ����ǰ ���� ����
			'refund' => "N" // ��ȯ ȯ�� ���� ����
		);
		return $AUTH;
	}

	$AUTH = array(
		'coupon' => "Y", // ���� ��� ����
		'reserve' => "Y", // ������ ��� ����
		'gift' => "Y", // ����ǰ ���� ����
		'refund' => "Y" // ��ȯ ȯ�� ���� ����
	);

	// ī�װ��� ��� ���� ----------------------------------------------------------------------- 1

	$row = cateAuth ( $productcode );	
	$AUTH['coupon']		=	($row->coupon=="N")?"N":$AUTH['coupon'];
	$AUTH['reserve']	=	($row->reserve=="N")?"N":$AUTH['reserve'];
	$AUTH['gift']			=	($row->gift=="N")?"N":$AUTH['gift'];
	$AUTH['refund']			=	($row->refund=="N")?"N":$AUTH['refund'];


	// ��ǰ�� ��� ���� ----------------------------------------------------------------------- 2
	if(preg_match('/^[0-9]{18}$/',$productcode)){
		$row = productAuth ( $productcode );
		
		$AUTH['coupon']		=	($row->coupon=="Y")?"N":$AUTH['coupon'];
		$AUTH['reserve']	=	($row->reserve=="Y")?"N":$AUTH['reserve'];
		$AUTH['gift']			=	($row->gift=="Y")?"N":$AUTH['gift'];
		$AUTH['refund']			=	($row->refund=="Y")?"N":$AUTH['refund'];
	}



	// ȸ�� �׷캰 ��� ���� ----------------------------------------------------------------------- 3
	if ( strlen($_ShopInfo->getMemgroup()) > 0 ) {

		$row = memberGroupAuth( $_ShopInfo->getMemgroup() );

		$AUTH['coupon']		=	($row->coupon=="N")?"N":$AUTH['coupon'];
		$AUTH['reserve']	=	($row->reserve=="N")?"N":$AUTH['reserve'];
		$AUTH['gift']			=	($row->gift=="N")?"N":$AUTH['gift'];
		$AUTH['refund']			=	($row->refund=="N")?"N":$AUTH['refund'];
	}

	return $AUTH;
}


// ī�װ��� ��� ����
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


// ��ǰ�� ��� ����
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


// ��ǰ�� ��� ����
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
		'coupon' => "Y", // ���� ��� ����
		'reserve' => "Y", // ������ ��� ����
		'gift' => "Y", // ����ǰ ���� ����
		'refund' => "Y" // ��ȯ/ ȯ�� ���� ����
	);
	//return $row;

	$AUTH['coupon']		=	($row->coupon=="Y")?"N":$AUTH['coupon'];
	$AUTH['reserve']	=	($row->reserve=="Y")?"N":$AUTH['reserve'];
	$AUTH['gift']			=	($row->gift=="Y")?"N":$AUTH['gift'];
	$AUTH['refund']			=	($row->refund=="Y")?"N":$AUTH['refund'];
	return $AUTH;
}


// ȸ�� �׷캰 ��� ����
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


// ī�װ� �׷� ����
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



// ī�װ��� �׷� ���� ����
function categoryGroupAuth ( $A, $B, $C, $D ) {
	global $_ShopInfo;

	$_cdata="";
	$sql = "SELECT * FROM tblproductcode WHERE codeA='".$A."' AND codeB='".$B."' AND codeC='".$C."' AND codeD='".$D."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {

		//���ٰ��ɱ��ѱ׷� üũ
		if($row->group_code=="NO") {
			echo "<html></head><body onload=\"location.href='/main/main.php'\"></body></html>";exit;
		}
		if(strlen($_ShopInfo->getMemid())==0) {
			if(strlen($row->group_code)>0) {
				echo "<html></head><body onload=\"location.href='/front/login.php?chUrl=".getUrl()."'\"></body></html>";exit;
			}
		} else {
			//if($row->group_code!="ALL" && strlen($row->group_code)>0 && $row->group_code!=$_ShopInfo->getMemgroup()) {
			if(strlen($row->group_code)>0 && strpos($row->group_code,$_ShopInfo->getMemgroup())===false) {	//�׷�ȸ���� ����
				echo "<html></head><body onload=\"alert('�ش� ī�װ� ���ٱ����� �����ϴ�.');location.href='/main/main.php'\"></body></html>";exit;
			}
		}
		return $row;

	} else {
		echo "<html></head><body onload=\"location.href='/main/main.php'\"></body></html>";exit;
	}

}


// ī�װ� ������(category, codeA, codeB, codeC, codeD )
// �ɼ��� �ش� �ڵ尪�� �ٷ� ����
// �ɼ� ������� ��ü�� �迭�� ����
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
	// �ɼ��� ���� ���
	if( $opt != "ALL" ) {
		$categoryTab = $categoryTab[$opt];
	}
	return $categoryTab;
}



// ī�װ� ��ȣ�� ���ڸ� 000 �ڸ���
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











////////// ��ǰ ��Ʈ��



// ��ǰ ����Ʈ
function productListArray ( $category = '', $sort='production_desc', $limit = 4, $specialCode='' ) {
	global $_ShopInfo,$qry;
	if( !_empty($category) ) {

		$returnArray = "";


		// ī�װ� ������(category, codeA, codeB, codeC, codeD )
		foreach ( categorySubTab($category) as $key => $var ) {
			${$key}=$var;
		}

		// ȸ������ ������ ī�װ� ����Ʈ
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

			// ���� - ��ȣ, ����, ��ǰ��, ������, ����
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


		// ���� - ��ǰ�ڵ�,��ǰ��,�귣��,����,�ǸŰ�,������,��ǥ�̹���(��)
		return $returnArray;

	}
}



if(!function_exists('getCodeLoc')){
// ���� ī�װ� ��ġ
function getCodeLoc($code,$color1="9E9E9E",$color2="9E9E9E") {
	global $_ShopInfo, $Dir;
	$code_loc = "<A HREF=\"".$Dir.MainDir."main.php\"><FONT COLOR=\"".$color1."\">Ȩ</FONT></A> <FONT COLOR=\"".$color1."\">></FONT> ";
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

// ��ǰ ���� ��� �׸� ����
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



// ��ǰ ���� ��� �׸� ����
function _deleteProductDetails($pridx=0){
	if(_isInt($pridx)){
		$conn = get_db_conn();
		$sql = "delete from tblproduct_detail where pridx='".$pridx."'";
		mysql_query($sql,$conn);
	}
}

// $type = image > �̹��� �迭, val => �� ( N �� ��찡 �����ݵ��� ���� �����) , all => 2���� ���η� ��ȯ
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
��ǰ�� ���� ����ǰ �� ��ȯ ȯ�� � ���� ������ ��ȯ
���� �迭 ���·� ��ȯ array('coupon'=>'Y','reserve'=>'Y','gift'=>'Y','return'=>'Y');
coupon : ���� , reserve : ������ ��� , gift : ����ǰ , return : ��ȯ �� ȯ��
��Ű�� ���� Y �� ���� ��� N �� ���� �Ұ�
���� �ι�° ���ڷ� ���� Ű�� ���� �Ұ�� �ش� Ű�� �� ���� ��ȯ
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
#####################��ǰ�� ȸ��������#######################################
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
// ��ǰ ���� ��� ������ �迭�� ��ȯ
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


// ��ǰ ���� ��� ���ø� ���� ȣ���
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

// ��ǰ ���� ��� ���� ���� �׸� ó���� �迭 ��ȯ �Լ�
function _productGosiInfoArr($idx){
	$infoarr = array();
	$infoitem = array('title'=>'','items'=>array());
	//$infoDitem = array('title'=>'','desc'=>'');


	$tmp = $infoitem;
	$tmp['title']='�Ƿ�';
	array_push($tmp['items'],array('title'=>'��ǰ ����','desc'=>'������ ���� �Ǵ� ȥ����� ������� ǥ��, ��ɼ��� ��� ������ �Ǵ� �㰡��'));
	array_push($tmp['items'],array('title'=>'����','desc'=>''));
	array_push($tmp['items'],array('title'=>'ġ��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'��Ź��� �� ��޽� ���ǻ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'��������','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='����/�Ź�';
	array_push($tmp['items'],array('title'=>'��ǰ ����','desc'=>'�ȭ�� ��쿡�� �Ѱ�, �Ȱ��� �����Ͽ� ǥ��'));
	array_push($tmp['items'],array('title'=>'����','desc'=>''));
	array_push($tmp['items'],array('title'=>'ġ��','desc'=>'�߱���: �ؿܻ����� ǥ��� ���������� ���� ǥ��(mm)<br>������ ( �� ��Ḧ ����ϴ� ����ȭ�� ����,cm)'));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'��޽� ���ǻ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);



	$tmp = $infoitem;
	$tmp['title']='����';
	array_push($tmp['items'],array('title'=>'����','desc'=>''));
	array_push($tmp['items'],array('title'=>'����','desc'=>''));
	array_push($tmp['items'],array('title'=>'����','desc'=>''));
	array_push($tmp['items'],array('title'=>'ũ��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'��޽� ���ǻ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='�м���ȭ (����/��Ʈ/�׼�����)';
	array_push($tmp['items'],array('title'=>'����','desc'=>''));
	array_push($tmp['items'],array('title'=>'����','desc'=>''));
	array_push($tmp['items'],array('title'=>'ġ��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'��޽� ���ǻ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='ħ����/Ŀư';
	array_push($tmp['items'],array('title'=>'��ǰ ����','desc'=>'������ ���� �Ǵ� ȥ����� ������� ǥ��<br>�����縦 ����� ��ǰ�� �����縦 �Բ� ǥ��'));
	array_push($tmp['items'],array('title'=>'����','desc'=>''));
	array_push($tmp['items'],array('title'=>'ġ��','desc'=>''));
	array_push($tmp['items'],array('title'=>'��ǰ����','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'��Ź��� �� ��޽� ���ǻ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='����(ħ��/����/��ũ��/DIY��ǰ)';
	array_push($tmp['items'],array('title'=>'ǰ��','desc'=>''));
	array_push($tmp['items'],array('title'=>'KC ���� �� ����','desc'=>'ǰ���濵 �� ����ǰ���������� �� ����-ǰ��ǥ�ô�����ǰ�� ����'));
	array_push($tmp['items'],array('title'=>'����','desc'=>''));
	array_push($tmp['items'],array('title'=>'����ǰ','desc'=>''));
	array_push($tmp['items'],array('title'=>'�ֿ� ����','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)<br>* ����ǰ �� �����ڰ� �ٸ� ��� �� ����ǰ�� ������,������'));
	array_push($tmp['items'],array('title'=>'������','desc'=>'* ����ǰ �� �������� �ٸ� ��� �� ����ǰ�� ������'));
	array_push($tmp['items'],array('title'=>'ũ��','desc'=>''));
	array_push($tmp['items'],array('title'=>'���/��ġ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='������(TV��)';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'�����ǰ �������� �� ����','desc'=>'(�����ǰ���������� �� ����������������ǰ, ��������Ȯ�δ�������ǰ,���������ռ�Ȯ�δ�������ǰ�� ����)'));
	array_push($tmp['items'],array('title'=>'��������/�Һ�����/�������Һ�ȿ�����','desc'=>'(�������̿��ո�ȭ�� �� �ǹ�����ǰ�� ����)'));
	array_push($tmp['items'],array('title'=>'���ϸ��� ��ó��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'ũ��','desc'=>'��������'));
	array_push($tmp['items'],array('title'=>'ȭ����','desc'=>'ũ��,�ػ�,ȭ����� ��'));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='������ ������ǰ(�����/��Ź��/�ı⼼ô��/���ڷ�����)';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'�����ǰ �������� �� ����','desc'=>'(�����ǰ���������� �� ����������������ǰ, ��������Ȯ�δ�������ǰ,���������ռ�Ȯ�δ�������ǰ�� ����)'));
	array_push($tmp['items'],array('title'=>'��������/�Һ�����/�������Һ�ȿ�����','desc'=>'(�������̿��ո�ȭ�� �� �ǹ�����ǰ�� ����)'));
	array_push($tmp['items'],array('title'=>'���ϸ��� ��ó��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'ũ��','desc'=>'�뷮,��������'));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='��������(������/��ǳ��)';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'�����ǰ �������� �� ����','desc'=>'(�����ǰ���������� �� ����������������ǰ, ��������Ȯ�δ�������ǰ,���������ռ�Ȯ�δ�������ǰ�� ����)'));
	array_push($tmp['items'],array('title'=>'��������/�Һ�����/�������Һ�ȿ�����','desc'=>'(�������̿��ո�ȭ�� �� �ǹ�����ǰ�� ����)'));
	array_push($tmp['items'],array('title'=>'���ϸ��� ��ó��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'ũ��','desc'=>'���� �� �ǿܱ� ����'));
	array_push($tmp['items'],array('title'=>'�ó������','desc'=>''));
	array_push($tmp['items'],array('title'=>'�߰���ġ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='�繫����(��ǻ��/��Ʈ��/������)';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'KCC ���� �� ����','desc'=>'���Ĺ� �� ��������ǰ�� ����,MIC ���� �� ȥ�� ����'));
	array_push($tmp['items'],array('title'=>'��������/�Һ�����/�������Һ�ȿ�����','desc'=>'(�������̿��ո�ȭ�� �� �ǹ�����ǰ�� ����)'));
	array_push($tmp['items'],array('title'=>'���ϸ��� ��ó��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'ũ��/����','desc'=>'���Դ� ��Ʈ�Ͽ� ����'));
	array_push($tmp['items'],array('title'=>'�ֿ� ���','desc'=>'��ǻ�Ϳ� ��Ʈ���� ��� ����,�뷮,�ü�� ���Կ��� ��  / �������� ��� �μ� �ӵ� ��)'));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='���б��(������ī�޶�/ķ�ڴ�)';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'KCC ���� �� ����','desc'=>'���Ĺ� �� ��������ǰ�� ����,MIC ���� �� ȥ�� ����'));
	array_push($tmp['items'],array('title'=>'���ϸ��� ��ó��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'ũ��/����','desc'=>''));
	array_push($tmp['items'],array('title'=>'�ֿ� ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='��������(MP3/���ڻ��� ��)';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'KC ���� �� ����','desc'=>'���Ĺ� �� ��������ǰ�� ����,MIC ���� �� ȥ�� ����'));
	array_push($tmp['items'],array('title'=>'��������/�Һ�����','desc'=>''));
	array_push($tmp['items'],array('title'=>'���ϸ��� ��ó��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'ũ��/����','desc'=>''));
	array_push($tmp['items'],array('title'=>'�ֿ� ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='�޴���';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'KCC ���� �� ����','desc'=>'���Ĺ� �� ��������ǰ�� ����,MIC ���� �� ȥ�� ����'));
	array_push($tmp['items'],array('title'=>'���ϸ��� ��ó��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'ũ��/����','desc'=>''));
	array_push($tmp['items'],array('title'=>'�̵���� ��������','desc'=>'1.�̵���Ż�<br>2.��������<br>3.�Һ����� �߰����� �δ����( ���Ժ�,����ī�� ���Ժ� �� �߰��� �δ��Ͽ��� �� �ݾ�, �ΰ�����, �ǹ����Ⱓ, ����� ��)'));
	array_push($tmp['items'],array('title'=>'�ֿ� ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='�׺���̼�';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'KCC ���� �� ����','desc'=>'���Ĺ� �� ��������ǰ�� ����,MIC ���� �� ȥ�� ����'));
	array_push($tmp['items'],array('title'=>'��������/�Һ�����','desc'=>''));
	array_push($tmp['items'],array('title'=>'���ϸ��� ��ó��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'ũ��/����','desc'=>''));
	array_push($tmp['items'],array('title'=>'�ֿ� ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'�� ������Ʈ ��� �� ����Ⱓ','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='�ڵ�����ǰ(�ڵ�����ǰ/��Ÿ �ڵ�����ǰ)';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'���ϸ��� ��ó��','desc'=>''));
	array_push($tmp['items'],array('title'=>'�ڵ��� ��ǰ �ڱ����� ����','desc'=>'�ڵ����������� ���� ��� �ڵ�����ǰ�� ����'));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'ũ��','desc'=>''));
	array_push($tmp['items'],array('title'=>'��������','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='�Ƿ���';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'�Ƿ������ �㰡��ȣ','desc'=>'�㰡-�Ű� ��� �Ƿ��⿡ ����'));
	array_push($tmp['items'],array('title'=>'������������� ����','desc'=>''));
	array_push($tmp['items'],array('title'=>'KC ���� �� ����','desc'=>'�����ǰ������������ �������� �Ǵ� ��������Ȯ�� ��� ���� ��ǰ�� ����'));
	array_push($tmp['items'],array('title'=>'��������/�Һ�����','desc'=>'�����ǰ�� ����'));
	array_push($tmp['items'],array('title'=>'���ϸ��� ��ó��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'��ǰ�� ������ �� �����','desc'=>''));
	array_push($tmp['items'],array('title'=>'��޽� ���ǻ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='�ֹ��ǰ';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'����','desc'=>''));
	array_push($tmp['items'],array('title'=>'����ǰ','desc'=>''));
	array_push($tmp['items'],array('title'=>'ũ��','desc'=>''));
	array_push($tmp['items'],array('title'=>'���ϸ��� ��ó��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'���� �ⱸ/���','desc'=>'��ǰ�������� ���� ���� �ⱸ/����� ��� "��ǰ�������� ���� ���ԽŰ� ����"�� ������ �Է��ϼ���.'));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='ȭ��ǰ';
	array_push($tmp['items'],array('title'=>'�뷮/�߷�','desc'=>''));
	array_push($tmp['items'],array('title'=>'��ǰ �ֿ� ���','desc'=>'(�Ǻ�Ÿ��,����(ȣ,��) ��)'));
	array_push($tmp['items'],array('title'=>'������ �Ǵ� ���� �� ���Ⱓ','desc'=>''));
	array_push($tmp['items'],array('title'=>'�����','desc'=>''));
	array_push($tmp['items'],array('title'=>'������ �� �����Ǹž���','desc'=>''));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'�ֿ伺��','desc'=>'������ ȭ��ǰ�� ��� ����� ���� �Է� ����'));
	array_push($tmp['items'],array('title'=>'��ɼ� Ȯ��ǰ �ɻ��� ����','desc'=>'��ɼ� ȭ��ǰ�� ��� ȭ��ǰ���� ���� ��ǰ�Ǿ�ǰ����û �ɻ� �� ����(�̹�,�ָ�����,�ڿܼ����� ��)'));
	array_push($tmp['items'],array('title'=>'����� �� ���ǻ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'�Һ��ڻ����� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='�ͱݼ�/����/�ð��';
	array_push($tmp['items'],array('title'=>'����/����/�������(�ð��� ���)','desc'=>''));
	array_push($tmp['items'],array('title'=>'�߷�','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>'�������� ������ ���� �ٸ� ��� �Բ� ǥ��'));
	array_push($tmp['items'],array('title'=>'ġ��','desc'=>''));
	array_push($tmp['items'],array('title'=>'���� �� ���ǻ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'�ֿ� ���','desc'=>'1. �ͱݼ�,������ - ���<br>2.�ð� - ���,��� ��'));
	array_push($tmp['items'],array('title'=>'������ ��������','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='��ǰ(����깰)';
	array_push($tmp['items'],array('title'=>'��������� �뷮(�߷�)/����/ũ��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ� ǥ��'));
	array_push($tmp['items'],array('title'=>'������','desc'=>'����깰�� ������ ǥ�ÿ� ���� ������ ���� ������'));
	array_push($tmp['items'],array('title'=>'����������','desc'=>'������ �Ǵ� ���꿬��'));
	array_push($tmp['items'],array('title'=>'������� �Ǵ� ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'���ù��� ǥ�û���','desc'=>'1.��깰 - ��깰ǰ���������� �����ں�����깰 ǥ��, ������ǥ��<br>2.��깰 - ������ ���� ��� ǥ��, ������ ��� �̷°����� ���� ǥ�� ����<br>3.���깰 - ���깰ǰ���������� �����ں������깰 ǥ��, ������ǥ��<br>4.���Խ�ǰ�� �ش��ϴ� ��� "��ǰ�������� ���� ���ԽŰ� ����" �� ����'));
	array_push($tmp['items'],array('title'=>'��ǰ����','desc'=>''));
	array_push($tmp['items'],array('title'=>'������� �Ǵ� ��޹��','desc'=>''));
	array_push($tmp['items'],array('title'=>'�Һ��ڻ�� ���� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='������ǰ';
	array_push($tmp['items'],array('title'=>'��ǰ�� ����','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ� ǥ��'));
	array_push($tmp['items'],array('title'=>'����������','desc'=>'������ �Ǵ� ���꿬��'));
	array_push($tmp['items'],array('title'=>'������� �Ǵ� ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'��������� �뷮(�߷�)/����','desc'=>''));
	array_push($tmp['items'],array('title'=>'������ �� �Է�','desc'=>'����깰�� ������ ǥ�ÿ� ���� ������ ���� ������ ǥ�� ����'));
	array_push($tmp['items'],array('title'=>'���缺��','desc'=>'��ǰ�������� ���� ���缺�� ǥ�ô�� ��ǰ�� ����'));
	array_push($tmp['items'],array('title'=>'�����������ս�ǰ ����','desc'=>'�����������ս�ǰ�� �ش��ϴ� ����� ǥ��'));
	array_push($tmp['items'],array('title'=>'ǥ�ñ��� ����������','desc'=>'�����ƽ� �Ǵ� ü��������ǰ � �ش��ϴ� ���'));
	array_push($tmp['items'],array('title'=>'���Խ�ǰ ����','desc'=>'���Խ�ǰ�� �ش��ϴ� ��� "��ǰ�������� ���� ���ԽŰ� ����"�� ����'));
	array_push($tmp['items'],array('title'=>'�Һ��ڻ�� ���� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='�ǰ���ɽ�ǰ';
	array_push($tmp['items'],array('title'=>'��ǰ�� ����','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ� ǥ��'));
	array_push($tmp['items'],array('title'=>'����������','desc'=>'������ �Ǵ� ���꿬��'));
	array_push($tmp['items'],array('title'=>'������� �Ǵ� ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'��������� �뷮(�߷�)/����','desc'=>''));
	array_push($tmp['items'],array('title'=>'������ �� �Է�','desc'=>'����깰�� ������ ǥ�ÿ� ���� ������ ���� ������ ǥ�� ����'));
	array_push($tmp['items'],array('title'=>'��������','desc'=>''));
	array_push($tmp['items'],array('title'=>'�������','desc'=>''));
	array_push($tmp['items'],array('title'=>'���뷮/������ �� ����� ���ǻ���','desc'=>'* ������ ���� �� ġ�Ḧ ���� �Ǿ�ǰ�� �ƴ϶�� ������ ǥ���� ���� �մϴ�.<br>ex) �� ��ǰ�� ������ ���� �� ġ�Ḧ ���� �Ǿ�ǰ�� �ƴմϴ�.'));
	array_push($tmp['items'],array('title'=>'�����������ս�ǰ ����','desc'=>'�����������ս�ǰ�� �ش��ϴ� ����� ǥ��'));
	array_push($tmp['items'],array('title'=>'ǥ�ñ��� ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'���Խ�ǰ ����','desc'=>'���Խ�ǰ�� �ش��ϴ� ��� "��ǰ�������� ���� ���ԽŰ� ����"�� ����'));
	array_push($tmp['items'],array('title'=>'�Һ��ڻ�� ���� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='�����ƿ�ǰ';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'KC ���� ��','desc'=>'ǰ���濵 �� ����ǰ������������ ����������� �Ǵ� ��������Ȯ�δ�� ����ǰ�� ����'));
	array_push($tmp['items'],array('title'=>'ũ��/�߷�','desc'=>''));
	array_push($tmp['items'],array('title'=>'����','desc'=>''));
	array_push($tmp['items'],array('title'=>'����','desc'=>'������ ��� ȥ���'));
	array_push($tmp['items'],array('title'=>'��뿬��','desc'=>''));
	array_push($tmp['items'],array('title'=>'���ϸ��� ��ó��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'��޹�� �� ��޽� ���ǻ���/ ����ǥ��','desc'=>'����, ��� ��'));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='�Ǳ�';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'ũ��','desc'=>''));
	array_push($tmp['items'],array('title'=>'����','desc'=>''));
	array_push($tmp['items'],array('title'=>'����','desc'=>''));
	array_push($tmp['items'],array('title'=>'��ǰ ����','desc'=>''));
	array_push($tmp['items'],array('title'=>'���ϸ��� ��ó��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'��ǰ�� ���� ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='��������ǰ';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'ũ��/�߷�','desc'=>''));
	array_push($tmp['items'],array('title'=>'����','desc'=>''));
	array_push($tmp['items'],array('title'=>'����','desc'=>''));
	array_push($tmp['items'],array('title'=>'��ǰ ����','desc'=>''));
	array_push($tmp['items'],array('title'=>'���ϸ��� ��ó��','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>'����ǰ�� ��� �����ڸ� �Բ�ǥ�� (��������� ��� ���� ���Կ��η� ��ü ����)'));
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'��ǰ�� ���� ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'ǰ����������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='����';
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'����/���ǻ�','desc'=>''));
	array_push($tmp['items'],array('title'=>'ũ��','desc'=>'����å�� ��� ������ �뷮'));
	array_push($tmp['items'],array('title'=>'�ʼ�','desc'=>'����å�� ��� ����'));
	array_push($tmp['items'],array('title'=>'��ǰ ����','desc'=>'���� �Ǵ� ��Ʈ�� ��� ���� ����, CD ��'));
	array_push($tmp['items'],array('title'=>'�Ⱓ��','desc'=>''));
	array_push($tmp['items'],array('title'=>'����/å�Ұ�','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='ȣ��/��� ����';
	array_push($tmp['items'],array('title'=>'����/������','desc'=>''));
	array_push($tmp['items'],array('title'=>'��������','desc'=>''));
	array_push($tmp['items'],array('title'=>'���/����Ÿ��','desc'=>''));
	array_push($tmp['items'],array('title'=>'��밡�� �ο�, �ο��߰� �� ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'�δ�ü�,���� ����','desc'=>'���� ��'));
	array_push($tmp['items'],array('title'=>'��� ����','desc'=>'ȯ��, ����� ��'));
	array_push($tmp['items'],array('title'=>'������ ����ó','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='������Ű��';
	array_push($tmp['items'],array('title'=>'�����','desc'=>''));
	array_push($tmp['items'],array('title'=>'�̿��װ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'����Ⱓ/����','desc'=>''));
	array_push($tmp['items'],array('title'=>'�� ���� �ο�, ��� ���� �ο�','desc'=>''));
	array_push($tmp['items'],array('title'=>'��������','desc'=>''));
	array_push($tmp['items'],array('title'=>'���� ����','desc'=>'�Ļ�, �μ���, �������� ��'));
	array_push($tmp['items'],array('title'=>'�߰� ��� �׸�� �ݾ�','desc'=>'����������, �����̿��, ������ �����, �ȳ���������, �Ļ���, ���û��� ��'));
	array_push($tmp['items'],array('title'=>'��� ����','desc'=>'ȯ��, ����� ��'));
	array_push($tmp['items'],array('title'=>'����溸�ܰ�','desc'=>'�ؿܿ����� ��츸 �ܱ����ΰ� �����ϴ� ����溸�ܰ�'));
	array_push($tmp['items'],array('title'=>'������ ����ó','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='�װ���';
	array_push($tmp['items'],array('title'=>'�������, �պ�/�� ����','desc'=>''));
	array_push($tmp['items'],array('title'=>'��ȿ�Ⱓ','desc'=>''));
	array_push($tmp['items'],array('title'=>'���ѻ���','desc'=>'�����, �ͱ��� ���氡�� ���� ��'));
	array_push($tmp['items'],array('title'=>'Ƽ�ϼ��ɹ��','desc'=>''));
	array_push($tmp['items'],array('title'=>'�¼�����','desc'=>''));
	array_push($tmp['items'],array('title'=>'�߰� ��� �׸�� �ݾ�','desc'=>'����������, �����̿���'));
	array_push($tmp['items'],array('title'=>'��� ����','desc'=>'ȯ��, ����� ��'));
	array_push($tmp['items'],array('title'=>'������ ����ó','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='�ڵ��� �뿩 ����(����ī)';
	array_push($tmp['items'],array('title'=>'����','desc'=>''));
	array_push($tmp['items'],array('title'=>'������ ���� ����','desc'=>'�������� �����Ǵ� ��쿡 ����'));
	array_push($tmp['items'],array('title'=>'�߰� ���� �� ���','desc'=>'������å����, ������̼� ��'));
	array_push($tmp['items'],array('title'=>'���� ��ȯ �� ������ ���� ���','desc'=>''));
	array_push($tmp['items'],array('title'=>'������ ����/ȸ�� �� �Һ��� å��','desc'=>''));
	array_push($tmp['items'],array('title'=>'���� ��� �Ǵ� �ߵ� �ؾ� �� ȯ�� ����','desc'=>''));
	array_push($tmp['items'],array('title'=>'�Һ��ڻ�� ���� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='��ǰ�뿩 ����(������, ��, ����û���� ��)';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'������ ���� ����','desc'=>'�������� �����Ǵ� ��쿡 ����'));
	array_push($tmp['items'],array('title'=>'�������� ����','desc'=>'����/���ͱ�ȯ �ֱ�, �߰� ��� ��'));
	array_push($tmp['items'],array('title'=>'��ǰ�� ����/�н�/�Ѽ� �� �Һ��� å��','desc'=>''));
	array_push($tmp['items'],array('title'=>'�ߵ� �ؾ� �� ȯ�� ����','desc'=>''));
	array_push($tmp['items'],array('title'=>'��ǰ ���','desc'=>'�뷮,�Һ����µ�'));
	array_push($tmp['items'],array('title'=>'�Һ��ڻ�� ���� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='��ǰ�뿩 ����(����,���߿�ǰ,����ǰ ��)';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'������ ���� ����','desc'=>'�������� �����Ǵ� ��쿡 ����'));
	array_push($tmp['items'],array('title'=>'��ǰ�� ����/�н�/�Ѽ� �� �Һ��� å��','desc'=>''));
	array_push($tmp['items'],array('title'=>'�ߵ� �ؾ� �� ȯ�� ����','desc'=>''));
	array_push($tmp['items'],array('title'=>'�Һ��ڻ�� ���� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='������ ������ (����, ����, ���ͳݰ��� ��)';
	array_push($tmp['items'],array('title'=>'������/������','desc'=>''));
	array_push($tmp['items'],array('title'=>'�̿�����/�̿�Ⱓ','desc'=>''));
	array_push($tmp['items'],array('title'=>'��ǰ ���� ���','desc'=>'CD, �ٿ�ε�, �ǽð� ��Ʈ���� ��'));
	array_push($tmp['items'],array('title'=>'�ּ� �ý��� ���/ �ʼ� ����Ʈ����','desc'=>''));
	array_push($tmp['items'],array('title'=>'û��öȸ �Ǵ� ����� ����/������ ���� ȿ��','desc'=>''));
	array_push($tmp['items'],array('title'=>'�Һ��� ��� ���� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);


	$tmp = $infoitem;
	$tmp['title']='��ǰ��/����';
	array_push($tmp['items'],array('title'=>'������','desc'=>''));
	array_push($tmp['items'],array('title'=>'��ȿ�Ⱓ/�̿�����','desc'=>'��ȿ�Ⱓ ��� �� ���� ����, �������ǰ�� �� �Ⱓ ��'));
	array_push($tmp['items'],array('title'=>'�̿� ���� ����','desc'=>''));
	array_push($tmp['items'],array('title'=>'�ܾ� ȯ�� ����','desc'=>''));
	array_push($tmp['items'],array('title'=>'�Һ��ڻ�� ���� ��ȭ��ȣ','desc'=>''));
	array_push($infoarr,$tmp);

	$tmp = $infoitem;
	$tmp['title']='��Ÿ';
	array_push($tmp['items'],array('title'=>'ǰ�� �� �𵨸�','desc'=>''));
	array_push($tmp['items'],array('title'=>'����/�㰡 ����','desc'=>'���� ���� ����/�㰡 ���� �޾����� Ȯ���� �� �ִ� ��� �׿� ���� ����'));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>''));
	array_push($tmp['items'],array('title'=>'������/������','desc'=>''));
	array_push($tmp['items'],array('title'=>'A/S å���ڿ� ��ȭ��ȣ �Ǵ� �Һ��ڻ�� ���� ��ȭ��ȣ','desc'=>''));
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