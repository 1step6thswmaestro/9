<?
$Dir="../";
include_once($Dir."lib/init.php");

$board=$_REQUEST["board"];
$file_name=$_REQUEST["file_name"];

$attachfileurl=getenv("DOCUMENT_ROOT")."/".RootPath.DataDir."shopimages/board/".$board."/".$file_name;
if(file_exists($attachfileurl)) {
	$file = $attachfileurl;

	if(strpos(" ".$file,"..")==true) exit;

	Header("Content-Disposition: attachment; filename=$file_name");
	Header("Content-Type: application/octet-stream;");
	Header("Pragma: no-cache");
	Header("Expires: 0");
	Header("Content-type: application/octet-stream");

	readfile($file);
}
?>