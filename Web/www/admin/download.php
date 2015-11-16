<?
//ok
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/MySQL.php");
$db = new MySQL();

$dir =$_GET["dir"];
$no  =$_GET["no"];
//$file_name=$_GET["file_name"];

if(!$dir || !$no) exit;

$file_name = $db->one('tbldesign_backup_list', 'dbl_ftpnm_ftp', 'dbl_no='.$no);
$db->update('tbldesign_backup_list', 'dbl_downuse = dbl_downuse + 1', 'dbl_no='.$no);

$attachfileurl=getenv("DOCUMENT_ROOT")."/".RootPath.$dir.$file_name;

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