<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

function getJuminData($host,$path,$query,$port=80) {
	$fp = @fsockopen($host, $port, &$errno, &$errstr, 3);
	if(!$fp) {
		@flush();
		@fclose($fp);
		return " result value=5";
	} else {
		$cmd = "POST $path HTTP/1.0\n";
		fputs($fp, $cmd);
		$cmd = "Host: $host\n";
		fputs($fp, $cmd);
		$cmd = "Content-type: application/x-www-form-urlencoded\n";
		fputs($fp, $cmd);
		$cmd = "Content-length: " . strlen($query) . "\n";
		fputs($fp, $cmd);
		$cmd = "Connection: close\n\n";
		fputs($fp, $cmd);
		fputs($fp, $query);
		flush();
		while($currentHeader = fgets($fp,4096)) {
			if($currentHeader == "\r\n") {
				break;
			}
		}
		$strLine = "";
		while(!feof($fp)) {
			$strLine .= fgets($fp, 4096);
		}
		fclose($fp);
		return $strLine;
	}
}


$id=$_POST["id"];
$pw=$_POST["pw"];

$name=$_POST["name"];
$jumin1=$_POST["jumin1"];
$jumin2=$_POST["jumin2"];

if(strlen($name)>0 && strlen($jumin1)>0 && strlen($jumin2)>0) {
	$host="name.siren24.com";
	$port=80;
	$path="/servlet/name_check";

	$query="id=".$id."&pw=".$pw."&name=".urlencode($name)."&jumin1=".$jumin1."&jumin2=".$jumin2."&ok_url=http://".getenv("HTTP_HOST");

	$resdata=getJuminData($host,$path,$query);
/*
	$fp = fopen($Dir."paygate/B/log/test.txt", "a+");
	fwrite($fp, $resdata);
	fclose($fp);
*/
	$name_result = substr($resdata,strpos($resdata,"result value=")+13,1);
	if($name_result=="\"") $name_result = substr($resdata,strpos($resdata,"result value=")+14,1);
}
?>
result value=<?=$name_result?>