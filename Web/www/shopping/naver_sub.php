<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
@include_once($Dir."lib/venderlib.php");
include_once($Dir."lib/shopdata.php");
/**
* 2012.06.28 줄바꿈 상수 선언 by madmirr
*/
define("NL","\n");

//$engine_num = "naver"; //엔진페이지 번호
$filename = DirPath.DataDir."shopimages/etc/naver.db";
if(file_exists($filename)==true){
	if($fp=@fopen($filename, "r")){
		$szdata=fread($fp, filesize($filename));
		fclose($fp);
		$checkValue=unserialize($szdata);
	}
}
function _saveSync($status='0'){				
	$sendparam['shopurl'] = $GLOBALS['shopurl'];
	if(substr($GLOBALS['shopurl'],-1,1) == '/') $sendparam['shopurl'] = substr($sendparam['shopurl'],0,-1);
	$sendparam['type'] = 'S'; // 전체 수집 페이지 ( 요약 수집일 경우는 S)
	$sendparam['status'] = ($status != '1')?'0':'1';
	$sendparam['requri'] = $_SERVER['HTTP_REFERER'];
	/*
	$param_post = (is_array($sendparam) && count($sendparam) > 0)?http_build_query($sendparam):'';	
	
	$req = array();	
	$req[] = 'POST /API/naver_ep_sync.php HTTP/1.1';
	$req[] = 'Host: getmall.co.kr';
	$req[] = 'Content-Type: application/x-www-form-urlencoded';	
	$req[] = 'Content-Length: '.strlen($param_post);
	$req[] = $param_post;	
	$req[] ="Connection: close\r\n";
	
	$req = implode("\r\n",$req)."\r\n";	
	$fp = @fsockopen('getmall.co.kr',80,$errcode,$errmsg,10);		
	if(!$fp) return;	
	fputs($fp,$req);*/
	_sendHTTP('GET','getmall.co.kr','/API/naver_ep_sync.php',$sendparam,80,5);
	/*
	echo 'dd';
	$buff = array('','');
	$i=0;
	while(!feof($fp)){
		$cont = fgets($fp, 4096);
		if($i < 1 && $cont == "\r\n") $i++;
		$buff[$i] .= $cont;
	}
	echo '--';
	print_r($buff);	
	echo 'aaa';*/
	//fclose($fp);	
	//return $buff;
	return;
}	

function _log($code,$msg){
	return;
	if(empty($code) && empty($msg)) return;
	if(false !== $fp = fopen($_SERVER['DOCUMENT_ROOT'].'/erpia.log','a+')){
		$str = date('Y-m-d H:i:s').': ['.$code.']'.$msg.NL;
		fwrite($fp,$str);
		fclose($fp);
	}else{
	//	echo 'error';
	//	exit;
	}
}

// _realTimeSync 호출시 실제 http 통신 담당 함수
function _sendHTTP($method,$host,$uri='/',$param=array(),$port=80,$timeout=30){
	$method = (strtoupper($method) == 'POST')?'POST':'GET';
	_log('sendHTTP','start '.$method);
	if(is_array($param) && count($param) > 0){			
		$param_get = ($method == 'GET')?'?'.http_build_query($param):'';
		$param_post = ($method == 'POST')?http_build_query($param):'';
	}
	$req = array();
	
	$req[] = $method.' '.$uri.$param_get.' HTTP/1.1';
	$req[] = 'Host: '. $host; 
	$req[] = 'Content-Type: application/x-www-form-urlencoded';
	
	if($method == 'POST'){			
		$req[] = 'Content-Length: '.strlen($param_post);
		$req[] = $param_post;
	}		
	$req[] ="Connection: close".NL;
			
	$req = implode(NL,$req).NL;
	_log('sendHTTP','req :: '.$req);
	if(false === $fp = @fsockopen($host,$port,$errcode,$errmsg,$timeout)){
		//throw new ErrorException('syncErr','['.$errcode.']'.$errmsg);
		_log('syncErr','['.$errcode.']'.$errmsg);
		return;
	}
	_log('sendHTTP','open ');
	fputs($fp,$req);
	_log('sendHTTP','put '.$method);
	
	$buff = array('','');
	$i=0;
	while(!feof($fp)){
		$cont = fgets($fp, 4096);
		if($i < 1 && $cont == NL) $i++;
		$buff[$i] .= $cont;
	}
	//$temp_result.=fread($fp,1024);
	fclose($fp);
	_log('sendHTTP','close');
	return $buff;
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

/**
* 2012.06.28 기존 쿼리등 대량 수정 by madmirr
*/

if(!empty($checkValue['shopping'])){	
	_saveSync('1');
	$where = array();
	if($_REQUEST['isdemo'] == '1'){

	}else{
		switch(date('H')){		
			case '10':
			case '11':
				array_push($where," p.modifydate >= '".date('Y-m-d')." 08:00:00'");
				array_push($where," p.modifydate < '".date('Y-m-d')." 10:00:00'");
				break;
			case '12':
			case '13':
				array_push($where," p.modifydate >= '".date('Y-m-d')." 10:00:00'");
				array_push($where," p.modifydate < '".date('Y-m-d')." 12:00:00'");
				break;
			case '14':
			case '15':
				array_push($where," p.modifydate >= '".date('Y-m-d')." 12:00:00'");
				array_push($where," p.modifydate < '".date('Y-m-d')." 14:00:00'");
				break;
			case '16':
			case '17':
				array_push($where," p.modifydate >= '".date('Y-m-d')." 14:00:00'");
				array_push($where," p.modifydate < '".date('Y-m-d')." 16:00:00'");
				break;
			case '18':
			case '19':
				array_push($where," p.modifydate >= '".date('Y-m-d')." 16:00:00'");
				array_push($where," p.modifydate < '".date('Y-m-d')." 18:00:00'");
				break;
			case '20':
				array_push($where," p.modifydate >= '".date('Y-m-d')." 18:00:00'");
				array_push($where," p.modifydate < '".date('Y-m-d')." 20:00:00'");
				break;
			//case '08':
			//case '09':		
			default:
				array_push($where," p.modifydate >= '".date('Y-m-d')." 20:00:00'");
				array_push($where," p.modifydate < '".date('Y-m-d')." 08:00:00'");
				break;
		}
	}
	
	array_push($where," p.syncNaverEp='1'");
	
	$where = (is_array($where) && count($where) > 0)?' where '.implode(' and ',$where):'';
	$sql = "SELECT *,ifnull(p.quantity,'unlimit') quantitychk,b.brandname FROM tblproduct as p left join tblproductbrand as b on b.bridx=p.brand ".$where;
	//$sql = "SELECT *,ifnull(p.quantity,'unlimit') quantitychk,b.brandname FROM tblproduct as p left join tblproductbrand as b on b.bridx=p.brand limit 10";
	
	$presult=mysql_query($sql,get_db_conn());	
	if(mysql_num_rows($presult) > 0){
		/**
		* 2012.06.28 카테고리 검증용 by madmirr
		*/
		$cat_sql ="SELECT concat(codeA,codeB,codeC,codeD) as fullcode,code_name FROM `tblproductcode` WHERE type LIKE 'L%' order by codeA asc,codeB asc,codeC asc,codeD asc";
		$cat_result = mysql_query($cat_sql,get_db_conn());
		$categorys = array();
		if($cat_result && mysql_num_rows($cat_result) > 0){
			while($row = mysql_fetch_assoc($cat_result)) $categorys[$row['fullcode']] = $row['code_name'];
			mysql_free_result($cat_result);
		}
		
		
		// 상품명 치환 설정 적용 부분
		if(!_empty($checkValue['syncPname'])){
			if(!preg_match('/\[PNAME\]/',$checkValue['syncPname'])) $checkValue['syncPname'] .= '[PNAME]';			
			$namepattern = array('(\[BRAND\])','(\[SHOPNAME\])','(\[PNAME\])');
			$repname = true;
		}else{
			$repname = false;
		}
		
		while($row=mysql_fetch_object($presult)){
			if(empty($categorys[substr($row->productcode,0,12)])) continue;

			echo lineOut('begin','','');
			echo lineOut('mapid',_filterStr($row->productcode),false);
			
			// 상품명 치환 설정 적용 부분
			if($repname){
				$reparr = array($row->brandname,$_data->companyname,$row->productname);			
				$row->productname = preg_replace($namepattern,$reparr,$checkValue['syncPname']);
			}			
			
			echo lineOut('pname',_filterStr($row->productname).(!_empty(_filterStr($row->addcode))?'-'._filterStr($row->addcode):''),false);			
			echo lineOut('price',strval(intval($row->sellprice)),false);			
				
			if($row->regdate == $row->modifydate){
				/**
				* 신규 등록 상품일 경우
				*/
				$class = 'I';
				echo lineOut('pgurl',"http://".$shopurl."?productcode=".$row->productcode,false);
				echo lineOut('igurl',"http://".$shopurl.DataDir."shopimages/product/".$row->maximage,false);
				$catecode = str_split(substr($row->productcode,0,12),3);
				$catetmp = '';
				for($i=0;$i<4;$i++){
					$def = ($i < 1)?false:'';
					if($catecode[$i] != '000'){
						$catetmp .= $catecode[$i];
						echo lineOut('cate'.($i+1),$categorys[str_pad($catetmp,12,'0')],$def);				
					}else{
						echo lineOut('cate'.($i+1),'',$def);
					}
				}		
				$catetmp = '';
				for($i=0;$i<4;$i++){			
					$def = ($i < 1)?false:'';
					if($catecode[$i] != '000'){
						$catetmp .= $catecode[$i];				
						echo lineOut('caid'.($i+1),$catetmp,$def);
					}else{
						echo lineOut('cate'.($i+1),'',$def);
					}
				}		
				
				echo lineOut('model',_filterStr($row->model));
				echo lineOut('brand',_filterStr($row->brandname));
				echo lineOut('maker',_filterStr($row->production));
				echo lineOut('origi',_filterStr($row->madein));
				
				$BesongBi		= "0";
				if($row->deli=="N" && $row->deli_price == "0"){
					if($row->vender>0) {
						if($vender_deli_price[$row->vender]>0) {
							if($vender_deli_mini[$row->vender]>0) {
								// 지식쇼핑 정책 변경으로 수정 2014.06.10
								//$BesongBi	= "0/".$vender_deli_mini[$row->vender]."/".$vender_deli_price[$row->vender];
								if(intval($row->sellprice) >= $vender_deli_mini[$row->vender]) $BesongBi	= "0";
								else $BesongBi = $vender_deli_price[$row->vender];
							} else {
								$BesongBi	= $vender_deli_price[$row->vender];
							}
						} else if($vender_deli_price[$row->vender]=="-9") {
							$BesongBi	= "-1";
						}
					}else{
						if($_data->deli_basefee>0) {
							if($_data->deli_miniprice>0) {
								// 지식쇼핑 정책 변경으로 수정 2014.06.10
								//$BesongBi	= "0/".$_data->deli_miniprice."/".$_data->deli_basefee;
								if(intval($row->sellprice) >= $_data->deli_miniprice) $BesongBi	= "0";
								else $BesongBi = $_data->deli_basefee;
							} else {
								$BesongBi	= $_data->deli_basefee;
							}
						} else if($_data->deli_basefee == "-9") {
							$BesongBi	= "-1";
						}
					}
				} else if($row->deli_price>0) {
					$BesongBi	= $row->deli_price;
				} else if($row->deli=="G" && $row->deli_price == "0") {
					$BesongBi	= "-1";
				}
				
				echo lineOut('deliv',$BesongBi,false);
				
				if(intval($product['reserve']) >0) echo lineOut('point',trim(getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y")));
				echo lineOut('mvurl','','');
				echo lineOut('revct','0');
				echo lineOut('ecoyn','','');
			}else{
				/**
				*  상태 업데이트 상품일 경우
				*/
			  if((intval($row->quantity) < 1 && $row->quantitychk != 'unlimit') || $row->display != 'Y') $class ='D';
			  else $class = 'U';			 
			}
			echo lineOut('class',$class,false);
			echo lineOut('utime',$row->modifydate,false);
			echo lineOut('ftend','','');			
		}
		mysql_free_result($presult);		
	}
} else {
	_saveSync('0');
	echo "<html><head><title></title></head><body onload=\"alert('현재 페이지는 미사용 페이지 입니다.');window.close();\"></body></html>";exit;
}
?>