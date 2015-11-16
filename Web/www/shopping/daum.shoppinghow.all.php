<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/func.php");
define('NL',"\r\n");
//$engine_num = "naver"; //엔진페이지 번호
$filename = DirPath.DataDir."shopimages/etc/daum.db";
if(file_exists($filename)==true){	
	if($fp=@fopen($filename, "r")){
		$szdata=fread($fp, filesize($filename));		
		fclose($fp);
		$checkValue=unserialize($szdata);
	}
}

/**
* 2012.06.28 단위 라인 출력용 함수 by madmirr
*/
function lineOut($key,$val='',$def=true){
	$str = '';
	if(preg_match('/[a-zA-Z0-9]+$/',$key)){
		$str .= '<<<'.$key.'>>>';
		if(!_empty($val)) $str .= $val;
		else if($def === false) exit('필수 입력 필드 값 누락'.$key);
		else if($def === true) $str .= '';
		else if(!_empty($def))  $str .= $def;
		
		if(!_empty($str)) $str .= NL;
	}
	return $str;
}

/**
* 2012.06.28 공백 문자열 확인용 함수 by madmirr
*/
if(!function_exists('_empty')){
	function _empty($val){
		return !(strlen(trim($val)) > 0);
	}
}

function _filterStr($str){
	return	preg_replace("(\\t|\\n|\\r|\^|\||%|※|☆|★|○|●|◎|◇|◆|□|■|△|▲|▽|▼|◁|◀|▷|▶|♤|♠|♡|♥|♧|♣|⊙|◈|▣|◐|◑|◐|◑|▒|▤|▥|▨|▧|▦|▩|♨|☏|☎|☜|☞|¶|†|‡|↕|↗|↙|↖|↘|♭|♩|♪|♬|㉿|＃)", "", strip_tags($str));
}

if(empty($checkValue['shopping'])){
	echo "<html><head><title></title></head><body onload=\"alert('현재 페이지는 미사용 페이지 입니다.');window.close();\"></body></html>";exit;
}
	//$sql = "SELECT productcode, productname, sellprice, quantity, production, deli, deli_price, vender, tinyimage, minimage, maximage, madein, reserve, reservetype, regdate, opendate FROM tblproduct WHERE display='Y' AND (quantity IS NULL OR quantity > 0) and syncDaumSh='1'";
$sql = "SELECT productcode, productname, sellprice, quantity, production, deli, deli_price, vender, tinyimage, minimage, maximage, madein, reserve, reservetype, regdate, opendate FROM tblproduct WHERE display='Y' AND (quantity IS NULL OR quantity > 0) ";

$presult=mysql_query($sql,get_db_conn());

if(mysql_num_rows($presult)>0){
	$deli_sql = 'SELECT deli_type,deli_basefee,deli_basefeetype,deli_miniprice,deli_oneprprice	,deli_setperiod,deli_limit FROM `tblshopinfo` limit 1';
	$deli_result = mysql_query($deli_sql,get_db_conn());
	$deli = mysql_fetch_assoc($deli_result);

	/**
	* 카테고리 불러오기 by madmirr
	*/
	$cat_sql ="SELECT concat(codeA,codeB,codeC,codeD) as fullcode,code_name FROM `tblproductcode` WHERE type LIKE 'L%' order by codeA asc,codeB asc,codeC asc,codeD asc";
	$cat_result = mysql_query($cat_sql,get_db_conn());
	$categorys = array();
	if($cat_result && mysql_num_rows($cat_result) > 0){
		while($row = mysql_fetch_assoc($cat_result)) $categorys[$row['fullcode']] = $row['code_name'];
		mysql_free_result($cat_result);
	}	
	if(getVenderUsed()==true) {
		$sql = "SELECT vender, deli_price, deli_mini FROM tblvenderinfo ";
		$result=mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)) {
			$vender_deli_price[$row->vender] = $row->deli_price;
			$vender_deli_mini[$row->vender] = $row->deli_mini;
		}
		mysql_free_result($result);
	}
	
	// 상품명 치환 설정 적용 부분
	if(!_empty($checkValue['syncPname'])){
		if(!preg_match('/\[PNAME\]/',$checkValue['syncPname'])) $checkValue['syncPname'] .= '[PNAME]';			
		$namepattern = array('(\[BRAND\])','(\[SHOPNAME\])','(\[PNAME\])');
		$repname = true;
	}else{
		$repname = false;
	}

	while($row=mysql_fetch_object($presult)) {
		$catcode = str_split(substr($row->productcode,0,12),3);
		if(_empty($categorys[str_pad(substr($row->productcode,0,3),12,'0')])) continue;
		
		
		echo lineOut('begin','','');
		echo lineOut('pid',_filterStr($row->productcode),false);		
		echo lineOut('price',strval(intval($row->sellprice)),false);
		
		// 상품명 치환 설정 적용 부분
		if($repname){
			$reparr = array($row->brandname,$_data->companyname,$row->productname);			
			$row->productname = preg_replace($namepattern,$reparr,$checkValue['syncPname']);
		}			
		
		echo lineOut('pname',_filterStr($row->productname).(!_empty(_filterStr($row->addcode))?'-'._filterStr($row->addcode):''),false);			
		
		echo lineOut('pgurl',"http://".$shopurl."?productcode=".$row->productcode,false);
		echo lineOut('igurl',"http://".$shopurl.DataDir."shopimages/product/".$row->maximage,false);
		
		$catecode = str_split(substr($row->productcode,0,12),3);
		$catetmp = '';
		for($i=0;$i<4;$i++){			
			$def = ($i < 1)?false:'';
			if($catecode[$i] != '000'){
				$catetmp .= $catecode[$i];				
				echo lineOut('cate'.($i+1),$catetmp,$def);
			}else{
				echo lineOut('cate'.($i+1),'',$def);
			}
		}		

		$catetmp = '';
		for($i=0;$i<4;$i++){
			$def = ($i < 1)?false:'';
			if($catecode[$i] != '000'){
				$catetmp .= $catecode[$i];
				echo lineOut('catename'.($i+1),$categorys[str_pad($catetmp,12,'0')],$def);
			}else{
				echo lineOut('catename'.($i+1),'',$def);
			}
		}		
					
		echo lineOut('model',_filterStr($row->model));
		echo lineOut('brand',_filterStr($row->brandname));
		echo lineOut('maker',_filterStr($row->production));
		
		$deliv2	= "";
		$deliv 	= '0';
		
		if($row->deli=="N" && $row->deli_price == "0"){
			if($row->vender>0) {
				if($vender_deli_price[$row->vender]>0) {
					if($vender_deli_mini[$row->vender]>0){ // 조건부 무료							
						if(intval($row->sellprice) >= $vender_deli_mini[$row->vender]){
							$deliv	= "2";
							$deliv2 = $vender_deli_mini[$row->vender].'원이상무료or'.$vender_deli_price[$row->vender].'원';
						}else{
							$deliv	= "1";
							$deliv2 = $vender_deli_price[$row->vender];
						}
					} else { // 유료
						$deliv	= "1";
						$deliv2 = $vender_deli_price[$row->vender];
					}
				} else if($vender_deli_price[$row->vender]=="-9") { // 착불
					$BesongBi	= "1";
				}
			}else{
				if($_data->deli_basefee>0) {
					if($_data->deli_miniprice>0) {							
						$deliv	= "2";
						$deliv2 = $_data->deli_miniprice.'원이상무료or'.$_data->deli_miniprice.'원';
					} else {
						$deliv	= "1";
						$deliv2	= $_data->deli_basefee;
					}
				} else if($_data->deli_basefee == "-9") {
					$deliv	= "1";					
				}
			}
		} else if($row->deli_price>0) {
			$deliv	= "1";					
			$deliv2	= $row->deli_price;
		} else if($row->deli=="G" && $row->deli_price == "0") {
			$deliv	= "1";					
			$deliv2	= '';
		}
		
		echo lineOut('deliv',$deliv);
		echo lineOut('deliv2',$deliv2);
		
		if(intval($product['reserve']) >0) echo lineOut('point',trim(getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y")));		
		echo lineOut('end');
	}
	mysql_free_result($presult);
}

?>