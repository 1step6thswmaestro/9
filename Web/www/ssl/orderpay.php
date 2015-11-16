<?
if(getenv("HTTPS")!="on") {
	header("HTTP/1.0 404 Not Found");
	exit;
}

#林巩 (搬力规过 芒)
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$shopurl=$_POST["shopurl"];

if(!eregi($shopurl,getenv("HTTP_REFERER"))) {
	exit;
}

$procdata=array();
$sessid=md5(uniqid(rand(),1)).md5(uniqid(rand(),1));
foreach($_POST as $key=>$val) {
	$procdata[$key]=$val;
}
$fp=fopen($Dir.DataDir."ssl/".$sessid.".temp","w");
fputs($fp, serialize($procdata));
fclose($fp);
touch($Dir.DataDir."ssl/".$sessid.".temp",time()+600);

echo "<html><head><title></title></head><body>\n";
echo "<form name=form1 method=post action=\"http://".$shopurl."/".RootPath.FrontDir."orderpay.php\">\n";
echo "<input type=hidden name=ssltype value=\"ssl\">\n";
echo "<input type=hidden name=sessid value=\"".$sessid."\">\n";
echo "</form>\n";
echo "<script>document.form1.submit();</script>\n";
echo "</body></html>\n";
exit;

?>