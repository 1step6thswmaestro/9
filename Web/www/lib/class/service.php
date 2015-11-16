<?php
/** 
- 템플릿 관리 관련 메서드 추가
**/
require_once $Dir.'lib/ext/func.php';

class service{
	var $isdebug 	= false;
	var $isinited = NULL;
	// 인증 및 서비스 상태 확인 관련
	var $apiHost = "getmall.co.kr";
	var $apiUrl = "/API/service.php";
	
	var $shopurl='';
	var $authkey='';
	
	//var $sender	= array('email'=>'jhj@objet.co.kr','name'=>'오브제'
	//var $sepStr = 'æ'; // 연결 구분자
	var $users = array();
	var $rn = "\r\n";
	
	function service(){
		$this->__construct();
	}
	
	function __construct(){		
	}
	
	// 설정 초기화 - 저장된 계정등 로드
	function _usedList($reinit=false){
		if($this->isinited === false){
			
		}else{
			if(is_null($this->isinited) || $reinit){		
				if(empty($this->shopurl) && !empty($GLOBALS['shopurl'])){
					$this->shopurl = $GLOBALS['shopurl'];						
				}
				if(empty($this->shopurl)) $this->shopurl =  $_SERVER['HTTP_HOST'];
				
				if(substr($this->shopurl,-1,1) == '/') $this->shopurl = substr($this->shopurl,0,-1);
				$this->getAuthKey();
				
				$apireq = array('act'=>'usedlist','shopurl'=>$this->shopurl,'authkey'=>$this->authkey);
				if(strlen(trim($apireq['authkey'])) < 1) exit('인증키 확인 오류');
				
				$result = $this->_sendHTTP('POST',$this->apiHost,$this->apiUrl,$apireq);
				$result = json_decode($result[1]);
				if(is_object($result)) $result = objectToArray($result);				
				return $this->_decode($result);
			}			
		}
		return $this->isinited;
	}
	
	
	function getAuthKey(){	
		$this->authkey = '';
		if($f=file($GLOBALS['Dir'].AuthkeyDir.".shopaccess")) $this->authkey =trim($f[0]);			
		return $this->authkey;
	}

	function _sendHTTP($method,$host,$uri='/',$param=array(),$port=80,$timeout=30){
		$method = (strtoupper($method) == 'POST')?'POST':'GET';
		if(is_array($param) && count($param) > 0){			
			$param_get = ($method == 'GET')?'?'.http_build_query($param):'';
			$param_post = ($method == 'POST')?http_build_query($param):'';
		}
		$req = array();
		$req[] = $method.' '.$uri.$param_get.' HTTP/1.1';
	    $req[] = 'Host: '. $host; 
 		$req[] = 'Content-Type: application/x-www-form-urlencoded';
		
		if($method == 'POST') $req[] = 'Content-Length: '.strlen($param_post);			
		$req[] ="Connection: close".$this->rn;
		if($method == 'POST') $req[] = $param_post.$this->rn;
		
		$req = implode($this->rn,$req).$this->rn;
		
		if(false !== $fp = @fsockopen($host,$port,$errcode,$errmsg,$timeout)){					
			fputs($fp,$req);
			$buff = array('','');
			$i=0;
			while(!feof($fp)){
				$cont = fgets($fp, 1024);
				if($i < 1 && $cont == $this->rn) $i++;
				$buff[$i] .= $cont;			
			}			
			//$temp_result.=fread($fp,1024);
			fclose($fp);
			if(!empty($buff[1])) $buff[1] = $this->unchunk($buff[1]);			
			return $buff;
		}else{
			echo 'syncErr'.'['.$errcode.']'.$errmsg;
			return '';
		}
	}
	
	function unchunk($result) {
		return preg_replace_callback(
			'/(?:(?:\r\n|\n)|^)([0-9A-F]+)(?:\r\n|\n){1,2}(.*?)'
			.'((?:\r\n|\n)(?:[0-9A-F]+(?:\r\n|\n))|$)/si',
			create_function(
				'$matches',
				'return hexdec($matches[1]) == strlen($matches[2]) ?
					 $matches[2] :
					 $matches[0];'
			),
			$result
		);
	}
	
	function _decode($res){
		array_walk($res,'_decode');
		return $res;
	}	

}

if(!function_exists('_decode')){
	function _decode(&$value,$key){
		if(is_object($value)){
			$value = objectToArray($value);
		}
		
		if(is_array($value)){
			array_walk($value,'_decode');
		}else if(is_string($value)){
			$value = preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))",$value);
			//$value = iconv('UTF-8','EUC-KR',stripslashes($value));
			$value = iconv('UTF-8','EUC-KR',$value);
			//$value = urlencode($value);
			
		}
	}
}
?>