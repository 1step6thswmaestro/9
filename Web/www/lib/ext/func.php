<?php
if(!defined('NL')) define('NL',"\r\n");

// 순차적으로 값을 확인해서 최초로 정의 된 값을 반환
function pick(){
	$args = func_get_args();
	$i=0;
	while($i < count($args)){
		if(!_empty($args[$i])) return $args[$i];
		$i++;
	}
	return;
}

// 변수가 존재하지않거나 값이 없을 경우
// empty 는 0 의 경우 true 를 반환
function _empty($val){
	if(strlen(trim($val)) > 0) return false;
	else return true;
}



// 자연수형
// _isInt(확인할 값[,0을 허용할지의 여부
// 숫자형이고 1이상 (0 허용 옵션이 true 이면 0 이상) 일경우 treu, 그외는 false
function _isInt($val,$allowzero=false){
	if(is_numeric($val) && (($allowzero && $val >= 0) || $val > 0)) return true;
	else return false;
}

//타입이 배열이며 요소가 1개 이상일경우 참
function _array($arr){
	return (is_array($arr) && !empty($arr))?true:false;
}

//변수 내용 출력
function _pr($obj){
	@header("Content-type: text/html; charset=euc-kr");
	echo '<pre>'.print_r($obj,true).'</pre>';
}


// 메직 쿼터스에 따른 addslsahes
function _addslashes($str){
	if(_array($str)){
		foreach($str as $k=>$v) $str[$k] = _addslashes($v);
	}else{
		if(!get_magic_quotes_gpc()) $str = addslashes($str);
	}
	return $str;
}

// Sql injection 방지용 mysql_real_escape_string 은 % 와 _ 를 escape 하지 않는다.
// _escape(값[,문자열 따옴표 처리 여부]);
// 옵션이 true 이면 문자열의 경우 작은 따옴표로 감싼 형태로 출력 (default)
function _escape($str,$usesql=true){
	if(_array($str)){
		foreach($str as $k=>$v) $str[$k] = _escape($v,$usesql);
	}else{
		if(get_magic_quotes_gpc()) $str = stripslashes($str);
		$str = mysql_real_escape_string($str);
		if($usesql) $str = "'".$str."'";
	}
	return $str;
}

// 배열의 재귀적으로 trim 처리 그외는 trim 과 동일
function _trim(&$val){
	if(is_array($val)) $val = array_map("trim",$val);
	else $val = trim($val);
	return $val;
}

//값에 공백문자가 있으면 참
function inspace($val){
	return eregi("[[:space:]]",$val);
}

// 문자열 끊기 (이상의 길이일때는 . 로 표시)
function strcut($str,$len,$suffix="."){
	if($len >= strlen($str)) return $str;
	$klen = $len - 1;
	while(ord($str[$klen]) & 0x80) $klen--;
	return substr($str, 0, $len - (($len + $klen + 1) % 2)).$suffix;
}


function sizebyte($size,$range=2,$humansize=false,$shotunity=false){
	$unity = array('B','KB','MB','GB','TB');
	if(!_isInt($size,true)) return '0';
	if(!_isInt($range,true)) $range = 0;
	$base = ($humansize !== false)?1000:1024;
	$e = floor(log($size,$base));
	if($e > count($unity)) return 'Out of Range';
	$rsize = round($size/pow($base,$e),$range);
	return $rsize.' '.$unity[$e];
}


//경고창 띄우고 페이지 이동
// _alert(메시지[,이동할 주소 [,페이지 교체 여부]]);
// 페이지 교체 여부가 false 이면 페이지 이동으로 처리 (history 상에 주소 추가형태)
// 이외의 경우는 페이지 치환으로 처리( history 상에서 페이지 주소가 대체됨 default)
function _alert($msg,$url=NULL,$replace=true){	//페이지 전환
	@header("Content-type: text/html; charset=euc-kr");
	ob_get_clean();
	ob_start();

	echo '<script type="text/javascript" language="javascript">'.NL;
	if(!empty($msg)) echo "window.alert('".addslashes($msg)."');".NL;
	if(!_empty($url)){
		if(is_numeric($url)){
			if($url == 0) echo 'window.close();'.NL;
			else echo "window.history.go(-".abs($url).");".NL;
		}else{
			if($replace) echo "document.location.replace('".trim($url)."');".NL;
			else echo "document.location.href('".trim($url)."');".NL;
		}
	}
	echo '</script>'.NL;
	ob_end_flush();
}

// 문자열 출력 하고 종료
function _exit($msg){
	@header("Content-type: text/html; charset=euc-kr");
	exit($msg);
}


function _encode(&$value,$key){
	if(is_array($value)){
		array_walk($value,'_encode');
	}else{
		//$value = urlencode($value);
		$value = iconv('EUC-KR','UTF-8',$value);
	}
}

function _iconvFromUtf8(&$value,$key){
	if(is_array($value)){
		@array_walk($value,'_iconvFromUtf8');
	}else{
		//$value = urlencode($value);
		$value = iconv('UTF-8','EUC-KR',$value);
	}
}


/* imagecolorallocate 과 기능은 동일 하나 뒤의 RGB 코드를 웹상의 16진수 문자열로 전달 받아 처리함
    EX)  $white = _imagecolorallocate($im,'#ffffff');

*/
function _imagecolorallocate($im,$colorstr){
	$colorstr = str_replace('#','',$colorstr);
	$colorstr = str_split($colorstr,2);
	for($i=0;$i<3;$i++) $colorstr[$i] = hexdec($colorstr[$i]);
	return imagecolorallocate($im,$colorstr[0],$colorstr[1],$colorstr[2]);
}
/*
if (!function_exists('json_encode')) {
    function json_encode($data) {
        switch ($type = gettype($data)) {
            case 'NULL':
                return 'null';
            case 'boolean':
                return ($data ? 'true' : 'false');
            case 'integer':
            case 'double':
            case 'float':
                return $data;
            case 'string':
                return '"' . strtr(addslashes($data), array("\\'"  => "'")) . '"';
            case 'object':
                $data = get_object_vars($data);
            case 'array':
                $output_index_count = 0;
                $output_indexed = array();
                $output_associative = array();
                foreach ($data as $key => $value) {
                    $output_indexed[] = json_encode($value);
                    $output_associative[] = json_encode($key) . ':' . json_encode($value);
                    if ($output_index_count !== NULL && $output_index_count++ !== $key) {
                        $output_index_count = NULL;
                    }
                }
                if ($output_index_count !== NULL) {
                    return '[' . implode(',', $output_indexed) . ']';
                } else {
                    return '{' . implode(',', $output_associative) . '}';
                }
            default:
                return ''; // Not supported
        }
    }
} */


function readAuthKey(){
	global $Dir;
	$authkey = '';
	if($f=@file($Dir.AuthkeyDir.".shopaccess")) {
		$authkey=trim($f[0]);
	}
	return $authkey;
}



function objectToArray($object){
	if(!is_object($object) && !is_array($object)){
		return $object;
	}
	if(is_object($object)){
		$object = get_object_vars($object);
	}
	return array_map('objectToArray',$object);
}

// 시간 관련 추가
function datediff($fdate,$sdate=''){
	$stamp1 = strtotime($fdate);
	$stamp2 = !_empty($sdate)?strtotime($sdate):time();
	return solvTimestamp($stamp1-$stamp2);
}


function solvTimestamp($diff){
	$result = array();
	$result['diff']    = $diff;
	$result['isold']  = $diff < 0;
	$result['day']    = intval($diff / (24*60*60));	
	$tmp = $diff%(24*60*60);
	$result['hour']   = intval($tmp/3600);
	$tmp = $tmp%3600;
	$result['minute'] = intval($tmp/60);	
	$result['second'] = $tmp%60;
	return $result;	
}

?>