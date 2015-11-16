<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
@header("Content-type: text/xml;charset=euc-kr");
@header("Cache-Control: no-cache, must-revalidate");
@header("Pragma: no-cache");

require_once dirname(__FILE__).'/class/getmallapi.php';
$param = $_REQUEST;
$api = new getmallApi($param['apiname']);
unset($param['apiname']);
if(false === $result = $api->_call($param)){
	echo "<getmallapi>\r\n";
	echo "<apiname>getmallapi</apiname>\r\n";
	echo "<version>1</version>\r\n";
	echo "<code>9999</code>\r\n";
	echo "<msg>".$api->errmsg."</msg>\r\n";
	echo "</getmallapi>";
}else{
	echo $result;
}
exit;
?>