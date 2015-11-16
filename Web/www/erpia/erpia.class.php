<?
/**
* ERPia 연동용 공용 클레스
* 2012.05.15 code by madmirr@gmail.com
*/

class erpia{
	var $charSet = 'euc-kr'; // 케릭터셋 - 소스 인코딩 및 사이트 소스 코드와 일치 해야 함.
	var $rn = "\r\n"; // 개행 문자
	var $saveLog = false;
	// 인증 및 서비스 상태 확인 관련
	var $MasterHost = "getmall.co.kr";
	var $SVStatusFile = "/front/onoff/checkservice.php";
	
	var $LogFileName = 'erpia.log'; // 디버깅 로그 저장용 파일
	var $isauth = false; // 정상 인증 여부
	var $shopurl='';

	var $svtype = '';
	var $pid = ''; // erpia 에서 접근 할때 인증 확인용 pid
	var $pwd = ''; // erpia 에서 접근 할때 인증 확인용 pwd
	var $admin_code = ''; // erpia 에 등록된 업체 코드 ( 실시간 호출 등 에서 사용)
	var $site_code = '001'; // Erpia 에 등록된 사이트 코드
	var $isinited = NULL; // 활성화 여부 판별
	var $Bcode = array('대한통운'=>'DEA','현대택배'=>'HLC','한진택배'=>'HAN','CJ HTH'=>'HTH','동부익스프레스'=>'DNG','로젠택배'=>'GEN','KGB택배'=>'KGB','옐로우캡'=>'YEL','우체국택배'=>'EPO','SC로지스'=>'SCL','하나로택배'=>'HNL','한진드림익스프레스'=>'HAD','일양택배'=>'YAN','네덱스'=>'NED','굿모닝택배'=>'GOD','이노지스택배'=>'INN','경동택배'=>'KDX','호남택배'=>'HON','양양택배'=>'YYT','기타'=>'OTH',''=>'REG','자체배송'=>'DIR','우편배송'=>'LET','직접수령'=>'FAC','대신택배'=>'DSI','천일택배'=>'CHN'); // erpia 택배사 코드 매핑용 배열
	
	var $BcodeInit = array(); // 자체몰과 erpia 간 택배사 매핑을 위한 배열

	function erpia(){
		$this->__construct();
	}
	// 인스턴스 방지
	function __construct(){
		$this->_init();	
	}
	
	// 설정 초기화 - 저장된 계정등 로드
	function _init($reinit=false){
		if($this->isinited === false){
			
		}else{
			if(is_null($this->isinited) || $reinit){		
				if(empty($this->shopurl) && !empty($GLOBALS['shopurl'])){
					$this->shopurl = $GLOBALS['shopurl'];						
				}
				if(substr($this->shopurl,-1,1) == '/') $this->shopurl = substr($this->shopurl,0,-1);
				$result = $this->_sendHTTP('get',$this->MasterHost,$this->SVStatusFile,array('shopurl'=>$this->shopurl));
				$status = explode('|',$result[1]);
				if($status[0] == '1'){
					if($status[1] == '7'){
						$this->svtype = $status[2];
						$this->admin_code = $status[3];
						$this->pid = $status[4];
						$this->pwd = $status[5];
						if($this->svtype !== 'Professional1') $this->isinited = true;
						else $this->isinited = false;							
					}else{
						$this->isinited = false;
					}
				}else{
					if($status[0] == '0') $this->isinited = false;
					else $this->_log('Sv CheckErr',$status[1]);
				}
			}			
		}
		return $this->isinited;
	}
	
	function _checkSvStatus(){
		$result = $this->_sendHTTP('get',$this->MasterHost,$this->SVStatusFile,array('shopurl'=>$this->shopurl));
		echo '<pre>';
		var_dump($result);
		echo '</pre>';
		exit;
	}
	
	/**
	* 변수 값 확인용
	*/
	function _val($key){
		$return = NULL;
		switch($key){
			case 'adminCode': $return = $this->admin_code; break;
			case 'isInit': $return = $this->isinited; break;
		}
		return $return;
	}
	
	/**
	* erpia 의 택배 코드와 자체 몰의 택배사 고유 번호 간 매핑 처리를 위한 초기화 함수
	*/
	function _delyInit(){
		$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
		$result=mysql_query($sql,get_db_conn());
		$this->BcodeInit = array();
		while($row=mysql_fetch_assoc($result)) {
			//$delicomlist[$row->code]=$row;			
			// 없는 택배 아주택배,KT로지스,건영택배,트라넷택배,양양택배,신세계쎄텍스택배
			$name = $row['company_name'];
			if(!empty($this->Bcode[$name])) $this->BcodeInit[$row['code']] = $this->Bcode[$name];
			else $this->BcodeInit[$row['code']] = $this->Bcode['기타'];			
		}
		mysql_free_result($result);
	}
	
	/**
	* 자체 택배 회사 코드를 입력받아서 erpia 연동 택배 코드를 반환
	* 목록에 없을 경우 기타 에 해당하는 코드 반환
	*/
	function _delyCodeErpia($code,$forceRefresh=false){
		if(!empty($code)){			
			if(count($this->BcodeInit) < 1 || $forceRefresh === true) $this->_delyInit();
			
			$return =  $this->BcodeInit[$code];
			if(empty($return)) $return = 'OTH';
			return $return;
		}else return '';		
	}
	
	/**
	* erpia 의 택배 코드를 자체 택배 회사 번호로 반환
	* 목록에 없을 경우 '없음' 에 해당하는 공백 반환
	*/
	function _deliComGetmall($Tcode){
		if(!empty($Tcode)){			
			if(count($this->BcodeInit) < 1 || $forceRefresh === true) $this->_delyInit();
			$deli_com = array_search($Tcode,$this->BcodeInit);
			if(!$deli_com) $deli_com = '';
			return $return;
		}else return '';	
	}
	
	
	
	/**
	* 인증 확인 함수
	* erpia 클라이언트에 url 연동 설정에 사용하는 id 와 비밀번호값 - 클라이언트설정에서는 페이지별 다른 계정 정보를 입력할수 있지만 연동을 위해서 통일 해야함.
	*/
	function _auth($pid,$pwd){
		if($this->_init() && !empty($pid) && !empty($pwd)){		
			if($pid === $this->pid && $pwd === $this->pwd){
				$this->isauth = true;
			}else{
				$this->isauth = false;
				$this->_log('accErr','연동 계정 정보가 올바르지 않습니다.');
			}
			return $this->isauth;
		}else{
			return false;
		}
	}
	
	/**
	* 디버깅용 테스트 로그 기록 함수
	* TODO :디버깅 완료후 해당 함수 처리 필요
	*/
	
	function _log($code,$msg){
		if(empty($code) && empty($msg)) return;
		if($this->saveLog && false){
			if(false !== $fp = fopen($_SERVER['DOCUMENT_ROOT'].'/'.$this->LogFileName,'a+')){
				$str = date('Y-m-d H:i:s').': ['.$code.']'.$msg.$this->rn;
				fwrite($fp,$str);
				fclose($fp);
			}
		}
	}
	
	// xml 출력 (head 포함) - 해당 함수를 호출 하는 페이지에서는 함수 결과 외 출력 내용이 있으면 안됨(해더 포함)
	function _xml($array_xml){
		$str = 
		$xml['root'] = '';
		if(is_array($array_xml) && count($array_xml) > 0){			
			 $xml['root'] = $array_xml;		
			 /*
			@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			@header("Last-Modified: " . gmdate("D, d M Y H:i ") . " GMT");
			@header("cache-contril: no-cache,must-revalidate");
			@header("pragma: no-cache");
			@header("Content-type: application/xml; charset=".$this->charSet);
	*/
			echo '<?xml version="1.0" encoding="'.$this->charSet.'" ?>';
			echo ($this->_xmlNode($xml));			
			//exit;					
		}else{
		//	exit;
		}
	
	}
	
	// _xml 함수에서 인자로 전달된 array 로 xml 문자열 생성을 위한 함수
	function _xmlNode($nodearr,$nName=NULL,$depth=0){
		$return = '';
		if(is_array($nodearr)){
			foreach($nodearr as $nodeKey=>$nodeVal){			
				if(is_array($nodeVal)){
					if(is_numeric($nodeKey) && !empty($nName)){
						$nodeName = $nName;
					}else{
						$nodeName = $nodeKey;
					}
				
					$keys= array_keys($nodeVal);
					$isNumeric = true;
					for($i=0;$i<count($keys);$i++){
						if(!is_numeric($keys[$i])){
							$isNumeric = false;
							break;
						}
					}					
					$tmpstr = $this->_xmlNode($nodeVal,$nodeName,$depth+1);
					if($isNumeric) $return .= $tmpstr;
					else $return .= $this->rn.'<'.$nodeName.'>'.$tmpstr.$this->rn.'</'.$nodeName.'>';				
				}else if(is_object($nodeVal)){
					$return .= '<'.$nodeKey.'>Object</'.$nodeKey.'>'; // 객체 파싱 은 향후 필요하면 추가
				}else{
					//if(preg_match_all("/[#\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/",$nodeVal)) $return .=  $this->rn.'<'.$nodeKey.'><!CDATA['.$nodeVal.']]></'.$nodeKey.'>';
					$nodeName = (is_numeric($nodeKey) && !empty($nName))?$nName:$nodeKey;
					if(preg_match("/[\\\'\"\^`~\_|$<>()=]/",$nodeVal)) $return .=  $this->rn.'<'.$nodeName.'><![CDATA['.$nodeVal.']]></'.$nodeName.'>';
					//if(preg_match("/[^ a-zA-Z0-9\-\@\.\,_]/",$nodeVal)) $return .=  $this->rn.'<'.$nodeKey.'><![CDATA['.$nodeVal.']]></'.$nodeKey.'>';
					else $return .=  $this->rn.'<'.$nodeName.'>'.$nodeVal.'</'.$nodeName.'>';
				}			
			}
		}	
		return $return;
	}
	
	// 결과 출력시 들여쓰기가 필요 할경우 사용
	function _tab($size){
		return str_repeat("\t",intval($size));
	}
	
	// 실시간 동기화 처리 함수 - 주문,상품 정보 등 변경 시에 해당 프로세스에서 호출
	function _realTimeSync($mode,$param){
		if($this->_init() && !empty($this->admin_code)){
			switch($mode){
				case 'goods':
					if(preg_match('/^[0-9]{18}$/',$param)) $this->_sendHTTP('get','www.erpia.net','/out/b2c.asp',array('mode'=>'goods','admin_code'=>$this->admin_code,'code'=>$param));
					else $this->_log('realtimesync','상품 코드가 올바르지 않습니다.');
					break;
				case 'order':
					if(preg_match('/^[0-9A-Z]+$/',$param)) $this->_sendHTTP('get','www.erpia.net','/out/b2c.asp',array('mode'=>'order','admin_code'=>$this->admin_code,'code'=>$param));
					else  $this->_log('realtimesync','주문 코드가 올바르지 않습니다.');
					break;
				default:
					break;
			}
		}else{
			//$this->_log('realtimesync','연동 설정 되지 않음');
		}
	}
	
	// _realTimeSync 호출시 실제 http 통신 담당 함수
	function _sendHTTP($method,$host,$uri='/',$param=array(),$port=80,$timeout=30){
		$method = (strtoupper($method) == 'POST')?'POST':'GET';
		$this->_log('sendHTTP','start '.$method);
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
		$req[] ="Connection: close".$this->rn;
				
		$req = implode($this->rn,$req).$this->rn;
		$this->_log('sendHTTP','req :: '.$req);
		if(false !== $fp = @fsockopen($host,$port,$errcode,$errmsg,$timeout)){			
			$this->_log('sendHTTP','open ');
			fputs($fp,$req);
			$this->_log('sendHTTP','put '.$method);
			
			$buff = array('','');
			$i=0;
			while(!feof($fp)){
				$cont = fgets($fp, 4096);
				if($i < 1 && $cont == $this->rn) $i++;
				$buff[$i] .= $cont;
			}
			//$temp_result.=fread($fp,1024);
			fclose($fp);
			$this->_log('sendHTTP','close');
			return $buff;
		}else{
			$this->_log('syncErr','['.$errcode.']'.$errmsg);
			return '';
		}
	}	
	
	/**
	* 일부 동기화 등 관련 기능 처리용 메서드
	*/
	function _syncBridge_Orders(){
		$query = "INSERT INTO tblerpiaorder (vender,ordercode,tempkey,productcode,opt1_name,opt2_name,package_idx,assemble_idx) select vender,ordercode,tempkey,productcode,opt1_name,opt2_name,package_idx,assemble_idx from tblorderproduct p left join tblerpiaorder e using (vender,ordercode,tempkey,productcode,opt1_name,opt2_name,package_idx,assemble_idx ) where substr(p.productcode,1,3) not in ('COU','999') and isnull( e.Gseq)";
		@mysql_query($query,get_db_conn());
	}
	
	/**
	* 주문 정보 변경시 erpia 연동 브리지 테이블 변경 시간 값 갱신을 위한 호출 함수
	*/
	function _syncOrderChangeTime($ordercode,$productcode=NULL,$modifydate=NULL){
		if(!empty($ordercode)){
			if(empty($modifydate)) $modifydate= 'NOW()';
			else $modifydate = "'".$modifydate."'";
			$where = array(" ordercode='".$ordercode."'");
			if(!empty($productcode)) array_push($where," productcode='".$productcode."'");
			$where = " where ".implode(' and ',$where);
			
			$query = "update tblerpiaorder set modifydate=".$modifydate.$where;
			mysql_query($query,get_db_conn());
		}
	}
	
	/** 그외 기타 공통 사용 가능한 함수 **/	
	function _limitstr($page,$pageCnt){
		$limit = '';
		if(is_null($page) || is_null($pageCnt)){
			$limit = ' limit 1';
		}else if(intval($page) > 0 && intval($pageCnt)){			
			$pageCnt = intval($_REQUEST['pageCnt']);
			$page = intval($_REQUEST['page']);		
			$limit = ' limit '.(($page-1)*$pageCnt).','.$pageCnt;
		}
		return $limit;
	}
	
	function _productQty($productcode){		
		if($this->_init()){			
			if(empty($productcode)){
				$param = array('Admin_Code'=>$this->admin_code,'Site_Code'=>$this->site_code);
			}else{
				$param = array('Admin_Code'=>$this->admin_code,'Site_Code'=>$this->site_code,'G_Code'=>$productcode);
			}		
			return $this->_sendHTTP('GET','www.erpia.net','/xml/Erpia_Goods_Stock.asp',$param,'80');
		}
		/*
		}catch(Exception $e){
			$this->_log('_productQty',$e->getMessage());
		}*/
	}

	function _syncProductQty($productcode){
		$standitems = array();				
		if($this->_init() && !empty($productcode)){
			$sqlitems = array();
			$xml = $this->_productQty($productcode);
			$xml = $xml[1];
			$items = array();
			$pos = 0;
			
			while(false !== $pos = strpos($xml,'<Good>',$pos)){
				$epos = strpos($xml,'</Good>',$pos+6);
				$tstr = substr($xml,$pos+6,$epos - $pos);
				if(preg_match_all('!<([a-zA-Z0-9_]+)>([^<]*)</\\1>!',$tstr,$mat)){
					
					$idx_code = array_search('G_Code',$mat[1]);
					$idx_name = array_search('G_Name',$mat[1]);
					$idx_stand = array_search('G_Stand',$mat[1]);
					$idx_qty  = array_search('Qty',$mat[1]);
					$idx_yqty = array_search('YQty',$mat[1]);
				
					$code = $mat[2][$idx_code];
					$name = $mat[2][$idx_name];
					$stand = $mat[2][$idx_stand];
					$qty = $mat[2][$idx_qty];
					$yqty = $mat[2][$idx_yqty];
					if(empty($stand)){
						$sql = "update tblproduct set quantity='".max(0,intval($qty)-intval($yqty))."' where productcode='".$code."'";
						array_push($sqlitems,$sql);
					}else{
						$standitems[$code][$stand] = max(0,intval($qty)-intval($yqty)); 
					}	
				}
				$pos = $epos+7;
			}

			if(count($standitems)){
				$sql = "select productcode,option_quantity,option1,option2 from tblproduct where productcode in ('".implode("','",array_keys($standitems))."')";								
				$result = @mysql_query($sql,get_db_conn()) or die(mysql_error());
				$old = array();
				while($row = mysql_fetch_assoc($result)){					
					$opt1 = explode(',',$row['option1']);
					$opt2 = explode(',',$row['option2']);
					$quantit = explode(',',$row['option_quantity']);
					$old[$row['productcode']]=array($opt1,$opt2,$quantit);						
				}			
				foreach($standitems as $productcode=>$node){
					$chk = $old[$productcode];
					for($i=1;$i<count($chk[0]);$i++){
						for($j=1;$j<count($chk[1]);$j++){
							$k = $i+($j-1)*10;
							$val = $node[$chk[0][$i].'||'.$chk[1][$j]];
							$chk[2][$k] = $val;
						}
					}	
					$sql = "update tblproduct set option_quantity='".implode(',',$chk[2])."' where productcode='".$productcode."'";
					array_push($sqlitems,$sql);
				}		
			}
			$rcnt = 0;
			if(count($sqlitems) > 0){
				foreach($sqlitems as $sql){									
					if(@mysql_query($sql,get_db_conn())) $rcnt++;
				}
				$sqlitems = array();
			}
		}
		return $rcnt;
	}
}


/*
(PHP 5, PECL pecl_http:0.1.0-0.9.0)
http_build_query -- URL 인코드한 쿼리 문자열을 생성합니다.
설명
string http_build_query ( array $formdata [, string $prefix][, string $arg_separator][, string $numeric_prefix] )
주어진 연관(혹은 인덱스) 배열에서 URL 인코드한 쿼리 문자열을 생성합니다. formdata는 배열이나 속성을 가지는 객체일 수 있습니다. formdata는 단순한 1차원 구조일수도, 혹은 (다른 배열을 포함한)배열의 배열일 수 있습니다. 기본 배열에 숫자 인덱스를 사용하고 numeric_prefix가 주어지면, 기본 배열 안의 숫자 인덱스 앞에 덧붙여집니다. 이는 PHP나 다른 CGI 어플리케이션에 적합한 변수명을 가지도록 합니다. 
*/
if (!function_exists('http_build_query')) {
	function http_build_query($data,$prefix=NULL,$arg_sep=NULL,$base=NULL){
		if(empty($arg_sep)) $arg_sep = ini_get('arg_separator.output');
		if (is_object($data)) $data = get_object_vars($data);
		
		$return = array();		
		foreach((array)$data as $_k => $_v){
			if(is_numeric($key) && !empty($prefix)) $_k = $prefix.$_k;
			$_k = urlencode($_k);
			if(!empty($base)) $_k = $base . '[' . $_k . ']';
			$return[] = (is_array($_v) || is_object($_v))?http_build_query($_v, $prefix, $arg_sep, $_k):$_k.'='.urlencode($_v);
		}
		return implode($arg_sep, $return);
	}
}
?>