<?php
/* change log
2013.06.10 - 일부 사이트 이미지 리다이렉션 시 쿠키 확인 관련 부분 처리
			- Transfer-Encoding: chunked 관련 이미지 수집 오류 수정
*/

class thumbnail{
	var $errmsg='';
	var $source = array();
	var $remoteTimeout = 30;
	var $usePNM = false;
	var $isRemote = false;
	var $destoryOnSave = true; // 파일 생성후 이미지 리소스 삭제 여부

	function thumbnail($sourceFile=''){
		$this->__construct($sourceFile);
	}

	function __construct($sourceFile=''){
		if(!_empty($sourceFile)) $this->_read($sourceFile);
	}

	function _setAfterDestory($bool=true){ // 리소스 저장후 바로 삭제 여부 ( 때때로 연속작업 필요할 경우는 flase 로 전달 _read 시에 true 로 다시 초기화
		$this->destoryOnSave = ($bool === false)?false:true;
	}

	function returnFalse($msg){
		$this->errmsg = $msg;
		return false;
	}

	function _read($target=''){		// 타겟 정보를 입력하지 않을 경우 변수 초기화
		// 변수 초기화
		$this->isRemote = false;
		$this->_setAfterDestory(); // 리소스 삭제 여부 초기화
		if(is_resource($this->source['resource'])) imagedestroy($this->source['resource']);
		$this->source = array();
		$this->errmsg = 'defError';

		$target = trim($target);
		if(!_empty($target)) return (preg_match("`^http`i", $target))?$this->readUrl($target):$this->readFile($target);
	}

	function readFile($file_path,$url=''){
		$this->source = array('width'=>0,'height'=>0,'type'=>0,'resource'=>NULL);
		if($this->isRemote && !_empty($url)) $this->source['url'] = $url;

		if(empty($file_path) || !is_file($file_path)) return $this->returnFalse('파일이 아닙니다. ['.$file_path.']');

		$this->source['file'] = realpath($file_path);
		list($this->source['width'], $this->source['height'],$this->source['type'],$this->source['attr']) = @getimagesize($file_path);
		if(!in_array($this->source['type'],array('1','2','3'))) return $this->returnFalse('gif,jpg,png 형태의 이미지 파일이 아닙니다. ['.$file_path.']');

		if($this->usePNM !== true){
			switch($this->source['type']){
				case 1 : //gif
					$this->source['resource'] = @imagecreatefromgif($file_path);
					break;
				case 2 : //jpg
					$this->source['resource'] = @imagecreatefromjpeg($file_path);
					break;
				case 3 : //png
					$this->source['resource'] = @imagecreatefrompng($file_path);
					break;
			}
			if($this->source['resource'] === false) return $this->returnFalse('리소스 가져오기 실패. ['.$file_path.']');
		}else if($this->source['type'] == '3')	return $this->returnFalse('PNM 사용시 gif,jpg 타입의 이미지에 대한 기능만 제공합니다.. ['.$file_path.']');
		return true;
	}

	function readUrl($file_url,$referer='',$cookie=array()){
		$this->source = array('width'=>0,'height'=>0,'type'=>0,'resource'=>NULL);

		$info = parse_url($file_url);
		$info['scheme'] = strtolower($info['scheme']);
		if($info['scheme'] != 'http' && $info['scheme'] != 'https') return $this->returnFalse('정상적인 경로가 아닙니다.. ['.$file_url.']');
		if(empty($info['port'])) $info['port'] = ($info['scheme'] == 'https')?'443':'80';
		if(empty($info['path'])) $info['path'] = '/';
		if(!empty($info['query'])){
			if(!is_urlEncoded($info['query'])) $info['query'] = query_encode($info['query']);
			$info['query'] = '?'.($info['query']);
		}
		if(empty($referer)){
			$referer = $info['scheme'] . '://' . $info['host'];
			if($info['port'] != 80 && $info['port'] != 443) $referer .= ':'.$info['port'];
			$referer .= '/';
		}

		if($info['scheme'] == 'http') $fp = fsockopen($info['host'], $info['port'], $errno, $errstr, $this->remoteTimeout);
		else $fp = fsockopen('ssl://'.$info['host'], $info['port'], $errno, $errstr,$this->remoteTimeout);

		if($fp){
			$put = "GET " . $info['path'] . $info['query'] . " HTTP/1.1\r\n";
			$put .= "Host: " . $info['host'] . "\r\n";
			$put .= "User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:10.0.2) Gecko/20100101 Firefox/10.0.2\r\n";
			$put .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
			$put .= "Accept-Language: ko-kr,ko;q=0.8,en-us;q=0.5,en;q=0.3\r\n";
			if(_array($cookie)) $put .= "Cookie: ".cookie_build($cookie)."\r\n"; // 쿠키 관련 처리 추가
			$put .= "Referer: " . $referer . "\r\n";
			$put .= "Connection: Close\r\n\r\n";
			fwrite($fp, $put);
			$header = $status = $img_txt = '';

			$buff = array();
			$i =0;
			while(!feof($fp)){
				$cont = fgets($fp, 4096);
				if($i < 1 && $cont == "\r\n") $i++;
				$buff[$i] .= $cont;
			}
			fclose($fp);

			$header = explode("\r\n",$buff[0]);

			$cookie = '';
			$cookie = cookie_parse($header);

			if(preg_match("`^HTTP/[^\s]*\s+([0-9]+)\s`", $header[0], $status)) $status = $status[1]; // http status

			switch($status){
				case 301:
				case 302:
					for($i=1;$i<count($header);$i++){
						if(preg_match("`^(Location:|URI:)\s+(.*)`", $header[$i], $redirect)) return $this->readUrl($redirect[2], $referer,$cookie);
					}
					return $this->returnFalse('Redirection Error. ['.$file_url.']');
					break;
				case 200:
					$img_txt = trim($buff[1]);
					for($i=1;$i<count($header);$i++){
						if(preg_match("`^(Transfer-Encoding:)\s+chunked`", $header[$i])){
							$tmp = explode("\r\n",$img_txt);
							$img_txt = '';
							$temp_txt = array();
							$temp_size = '';
							foreach($tmp as $idx=>$val){
								if(preg_match("/^([a-fA-F0-9]+)$/",$val,$mat)){
									if(!_empty($temp_size) && _array($temp_txt)){
										$img_txt .= substr(implode("\r\n",$temp_txt),0,$temp_size);
										$temp_size = '';
										$temp_txt = array();
									}
									$temp_size = hexdec($mat[1]);
								}else{
									array_push($temp_txt,$val);
								}
							}
							if(!_empty($temp_size) && _array($temp_txt)) $img_txt .= substr(implode("\r\n",$temp_txt),0,$temp_size);
						}
					}
					break;
				default:
					return $this->returnFalse('응답이 200 이 아닙니다. ['.$file_url.']');
					break;
			}

			if(empty($img_txt)) return $this->returnFalse('파일을 정상적으로 읽지 못했습니다. ['.$file_url.']');

			$temp_dir = sys_get_temp_dir();
			$temp_file = tempnam($temp_dir, "COSMOS");

			$fp = @fopen($temp_file, "w");
			@fwrite($fp, $img_txt);
			@fclose($fp);

			$this->isRemote = true;
			$result = $this->readFile($temp_file,$file_url);
			if(!$result) @unlink($temp_file);
			if(!$this->usePNM) @unlink($temp_file);
			return $result;
		}else{
			return $this->returnFalse('소켓 접속에 실패 ['.$file_url.']');
		}
	}

	// $overwrite > true : 덮어쓰기
	function resourceSave($target, $quality=70, $overwrite='2',$flush=true){
		if(!is_resource($this->source['resource'])) return $this->returnFalse('저장 대상이 올바르지 않습니다.');

		$save_dir = dirname($target);//저장 파일 경로에서 상위 디렉토리 경로를 가져옴
		if(!is_dir($save_dir)) return $this->returnFalse('디렉토리가 아닙니다 ['.$save_dir.']');
		if(!is_writable($save_dir)) return $this->returnFalse('쓰기권한이 없습니다. ['.$save_dir.']');
		if(is_dir($target)) return $this->returnFalse('같은 이름의 디렉토리가 존재 ['.$target.']');
		if(is_file($target)){
			switch($overwrite){
				case '1':
					return true;
					break;
				case '2':
					if(false === @unlink($target)) return $this->returnFalse('삭제 실패 ['.$target.']');
					break;
				default:
					return $this->returnFalse('중복파일 ['.$target.']');
			}
		}
		if(substr($target,-4) == '.___'){
			switch($this->source['type']){
				case 1 :  $extension = 'gif'; break;
				case 2 :  $extension = 'jpg'; break;
				case 3 :  $extension = 'png'; break;
			}
			$target = substr_replace($target,$extension,-3);
		}else{
			$extension = strtolower(substr($target, strrpos($target, '.')+1));
		}

		switch($extension){
			case 'gif':
				$result_save = @imagegif($this->source['resource'],$target);
				break;
			case 'png':
				$result_save = @imagepng($this->source['resource'],$target,9); // round($quality/10)
				break;
			case 'jpg': case 'jpeg':
			default :
				$result_save = @imagejpeg($this->source['resource'],$target, $quality);
				break;
		}
		if($result_save === false) $this->returnFalse('저장 실패 ['.$save_file.']');
		if($this->destoryOnSave !== false) imagedestroy($this->source['resource']);
		return $target;
	}

	//썸내일 이미지 생성
	function _make($target,$_w=0,$_h=0,$quality=70){
		if(empty($this->source['file']) || (!$this->usePNM && !is_resource($this->source['resource']))) return $this->returnFalse('원본 파일이 올바르게 로드 되지 않았습니다.');

		$_s['name'] = basename($this->source['file']);
		$_s['dir'] = realpath(dirname($this->source['file']));

		if(substr($target,-4) == '.___'){
			$oribasename = ($this->isRemote)?$this->source['url']:$_s['name'];
			$ext =strtolower(substr($oribasename,strrpos($oribasename, '.')));
			if(!_empty($ext) && strlen($ext) < 5) $target = substr_replace($target,$ext,-4);
		}

		$_t['name'] = basename($target);
		$_t['dir'] = realpath(dirname($target));
		if(empty($_t['dir'])) return $this->returnFalse('저장 대상 디렉토리가 올바르지 않습니다.'.$_t['dir'].dirname($target));
		if(empty($_t['name'])) return $this->returnFalse('저장 대상 파일 명이 올바르지 않습니다.');

		$ratiow = ($_w > 0 && $this->source['width'] > $_w)?(real)($_w / $this->source['width']):1;
		$ratioh = ($_h > 0 && $this->source['height'] > $_h)?(real)($_h / $this->source['height']):1;
		$ratio = ($ratiow > $ratioh)?$ratioh:$ratiow;

		if($ratio == 1){
			/*
			if($this->isRemote) return $this->resourceSave($target,70,2);
			else if($_s['dir'].'/'.$_s['name'] === $_t['dir'].'/'.$_t['name'] || copy($_s['dir'].'/'.$_s['name'],$_t['dir'].'/'.$_t['name'])) return $target;
			else return $this->returnFalse("파일 복사중 에러가 발생했습니다.");
			*/
			if($this->isRemote);
			else if($_s['dir'].'/'.$_s['name'] === $_t['dir'].'/'.$_t['name'] || copy($_s['dir'].'/'.$_s['name'],$_t['dir'].'/'.$_t['name'])) return $target;
			else return $this->returnFalse("파일 복사중 에러가 발생했습니다.");
		}

		$new_width = (int)($ratio*$this->source['width']);
		$new_height = (int)($ratio*$this->source['height']);

		if($this->usePNM){
			switch ($this->source['type']){//이미지 타입에 따라서 이미지 로드
				case 1 : // gif
				case 2 : // jpeg
					$method = ($this->source['type'] == '1')?'giftopnm':'djpeg';
					break;
				default:
					return $this->returnFalse("PNM 사용시 이미지 형식은 gif 와 jpg 만 지원합니다.");
					break;
			}
			system($method.' '.$_s['dir'].'/'.$_s['name'].' > '.$_t['dir'].'/'.$_t['name'].'.pnm',$retsource);
			system("pnmscale -xy $new_width $new_height ".$_t['dir']."/".$_t['name'].".pnm | cjpeg -progressive -optimize -smooth 20 -outfile ".$_t['dir']."/".$_t['name'],$rettarget);
			@unlink($_t['dir']."/".$_t['name'].".pnm");
			if($this->isRemote) @unlink($this->source['file']);
		}else{
			$dest_img = imagecreatetruecolor($new_width,$new_height);

			$white = imagecolorallocate($dest_img,255,255,255);
			if($this->source['type'] == 3){ // png
				imagealphablending( $dest_img, false );
				imagesavealpha( $dest_img, true );
				imagefilledrectangle($dest_img, 0, 0, $new_width, $new_height, $white);
			}else{
				imagefill($dest_img,0,0,$white);
			}

			imagecopyresampled($dest_img, $this->source['resource'],0,0,0,0,$new_width,$new_height,$this->source['width'],$this->source['height']);
			imagedestroy($this->source['resource']);
			$this->source['resource'] = $dest_img;
			$this->source['width'] = $new_width;
			$this->source['height'] = $new_height;

			$target = $this->resourceSave($target,$quality,2);
			if(!is_string($target) || !is_file($target)) return $this->returnFalse('파일 생성중 오류가 발생했습니다.');
			return $target;
		}
	}
}


function cookie_parse( $header ) {
        $cookies = array();
        foreach( $header as $line ){
                if( preg_match( '/^Set-Cookie: /i', $line ) ) {
                        $line = preg_replace( '/^Set-Cookie: /i', '', trim( $line ) );
                        $csplit = explode( ';', $line );
                        $cdata = array();
                        foreach( $csplit as $data ) {
                                $cinfo = explode( '=', $data );
                                $cinfo[0] = trim( $cinfo[0] );
                                if( $cinfo[0] == 'expires' ) $cinfo[1] = strtotime( $cinfo[1] );
                                if( $cinfo[0] == 'secure' ) $cinfo[1] = "true";
                                if( in_array( $cinfo[0], array( 'domain', 'expires', 'path', 'secure', 'comment' ) ) ) {
                                        $cdata[trim( $cinfo[0] )] = $cinfo[1];
                                }
                                else {
                                        $cdata['value']['key'] = $cinfo[0];
                                        $cdata['value']['value'] = $cinfo[1];
                                }
                        }
                        $cookies[] = $cdata;
                }
        }
        return $cookies;
}

function cookie_build( $data ) {
        if( is_array( $data ) ) {
                $cookie = '';
                foreach( $data as $d ) {
                        $cookie[] = $d['value']['key'].'='.$d['value']['value'];
                }
                if( count( $cookie ) > 0 ) {
                        return trim( implode( '; ', $cookie ) );
                }
        }
        return false;
}


if(!function_exists('is_urlEncoded')){
	function is_urlEncoded($string){
		$test_string = $string;
		while(urldecode($test_string) != $test_string){
			$test_string = urldecode($test_string);
		}
		return (urlencode($test_string) == $string);
	}
}

function query_encode($str){
	$rematcharr 		= array('%2F','%2B','%25');
   	$rereplacearr 	= array('/','+','%');


	$queryarr = array();
	$expl = split("&", $str);

	foreach($expl as $sep) {
		$ret = split("=", $sep);
		$queryarr[] = $ret[0].'='.str_replace($rematcharr,$rereplacearr,urlencode($ret[1]));
	}
	return implode('&',$queryarr);
}


?>