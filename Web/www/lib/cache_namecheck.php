<?
if(substr(getenv("SCRIPT_NAME"),-20)=="/cache_namecheck.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}

$second = 900;		// 15분간 한IP에서 실명인증을
$maxvisit = 6;		// 6번이상할경우
$no_second = 600;	// 10분간 실명인증을 막음

if (file_exists($file_jumin)==true) {
	$filetime = @filemtime($file_jumin);
	$timegan=(time()-$filetime);
	if ($timegan>$second) {
		@unlink($file_jumin);
		$name_result=0;
	} else {
		$name_result=1;
		return;
	}
}


// $file_denyN 가 존재하면 차단임..
if (file_exists($file_denyN)==true) {
	$filetime = @filemtime($file_denyN);
	$timegan=(time()-$filetime);
	// 파일생성된지 no_seond초가 지나면 다시 오픈
	if ($timegan>=$no_second) {
		@unlink($file_denyN);
	} else {
		$name_result=119;
		return;
	}
}

if (file_exists($file_denyY)==false) {
	$fp = @fopen("$file_denyY","w");@fputs($fp,"1");@fclose($fp);
} else {
	$fp = @fopen("$file_denyY","r");
	$temp = @fgets($fp,1024);
	if (strlen($temp)==0) $temp=0;
	@fclose($fp);

	$filetime = @filemtime($file_denyY);
	$timegan=(time()-$filetime);
	if ($timegan>$second) {
		@unlink($file_denyY); // 파일생성된지  x 초 지나면 다시 오픈
	} else {
		if ($temp>=$maxvisit) { // x 초 안에 방문자가 x 명이상이면
			$fp = @fopen("$file_denyN","w");@fputs($fp,$temp);@fclose($fp);
			unlink($file_denyY);
			$name_result=119;
			return;
		}
	}
}
?>