<?php   
//	ini_set("display_errors", 0);
    //set it to writable location, a place for temp generated PNG files
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'qrtemp'.DIRECTORY_SEPARATOR;    
    //html PNG location prefix
    $PNG_WEB_DIR = 'data/qrtemp/';
		
    include dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'phpqrcode'.DIRECTORY_SEPARATOR."qrlib.php";
    
	try{ 
	    if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
    	if(!preg_match('/^[0-9]{18}$/',$_REQUEST['productcode'])) throw new InvalidArgumentException('Not Productcode format');
		$filename = $PNG_TEMP_DIR.$_REQUEST['productcode'].'.png';	
		$link = 'http://'.$_SERVER['HTTP_HOST'].'/front/productdetail.php?productcode='.$_REQUEST['productcode'];
		
		//processing form input
		//remember to sanitize user input in real-life solution !!!
		$errorCorrectionLevel = 'L';
		if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H'))) $errorCorrectionLevel = $_REQUEST['level'];    
	
		$matrixPointSize = 2;
		if(isset($_REQUEST['size'])) $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);
		
		
		if(!file_exists($filename) || filesize($filename) < 1) 	QRcode::png($link, $filename, $errorCorrectionLevel, $matrixPointSize, 2); 		
		if(!file_exists($filename) || filesize($filename) < 1) throw new ErrorException('fileMakeErr');
			
		$size = filesize($filename);   
	    //$mime_type = (ereg('(Opera|MSIE)(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))?'application/octetstream' : 'application/octet-stream';
		if(!function_exists('mime_content_type')){
			//include dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'phpqrcode'.DIRECTORY_SEPARATOR."mime.php";
			$mime_type = 'image/png';
		}else{			
			$mime_type =  mime_content_type($filename);
		}
		
    
		@ob_end_clean(); // decrease cpu usage extreme		
		
		if($_REQUEST['isdown']){	
			$name ='QR_	'.$_REQUEST['productcode'].'.png';			
			header("Content-Disposition: attachment; filename=QR_".$_REQUEST['productcode'].".png");
			header("Content-type: image/png");
			readfile($filename);
		}else{
			header("Pragma: public");
			header("Expires: 0");		
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-control: public");
			header('Content-Type: ' . $mime_type);						
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header('Accept-Ranges: bytes');
		
			//  multipart-download and resume-download
			if(isset($_SERVER['HTTP_RANGE'])){
				list($a, $range) = explode("=",$_SERVER['HTTP_RANGE']);
				str_replace($range, "-", $range);
				$size2 = $size-1;
				$new_length = $size-$range;
				header("HTTP/1.1 206 Partial Content");
				header("Content-Length: $new_length");
				header("Content-Range: bytes $range$size2/$size");
			} else {
				$size2=$size-1;
				header("Content-Length: ".$size);
			}
			
			
			//$chunksize = 1*(1024*$speed); // 속도제한값
			$chunksize = 2048;
			
			$bytes_send = 0;
			if ($file = fopen($filename, 'rb'))
			{
				if(isset($_SERVER['HTTP_RANGE']))
					fseek($file, $range);
				while(!feof($file) and (connection_status()==0))
				{
					$buffer = fread($file, $chunksize);
					print($buffer);//echo($buffer); // is also possible
					flush();
					$this += strlen($buffer);
					if($limit) sleep(1); // 다운로드 속도제한
				}
				fclose($file);
			} else throw new ErrorException('Read Error');
			
			if(isset($new_length)) $size = $new_length;		
		}		
	}catch(Exception $e){
		echo $e->getMessage();
		//header("HTTP/1.0 404 Not Found");
		exit;
	}

  ?>