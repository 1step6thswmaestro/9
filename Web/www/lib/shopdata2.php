<?
if(substr(getenv("SCRIPT_NAME"),-13)=="/shopdata.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}

if(strlen(RootPath)>0) {
	$hostscript=getenv("HTTP_HOST").getenv("SCRIPT_NAME");
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=getenv("HTTP_HOST")."/";
}
/*
if(getenv("HTTPS")=="on") {
	//http로 리다이렉트한다.
	header("Location:http://".$shopurl);
	exit;
}
*/
$old_shopurl=$_ShopInfo->getShopurl();

$ref=$_REQUEST["ref"];
if (strlen($ref)==0 && strlen(getenv("HTTP_REFERER"))>0) {
	$ref=strtolower(ereg_replace("http://","",getenv("HTTP_REFERER")));
}
if (strlen($_ShopInfo->getShopurl())==0) {
	$sql = "SELECT * FROM tblshopinfo ";
	$result=mysql_query($sql,get_db_conn());
	if ($row=mysql_fetch_object($result)) {
		$_ShopInfo->setShopurl($shopurl);
		//$_ShopInfo->Save();	//save시키면 성인몰 또는 b2b쇼핑몰의 경우 바로 진입이 가능하기 때문.
	} else {
		error_msg("쇼핑몰 정보 등록이 안되었습니다.<br>쇼핑몰 설정을 먼저 하십시요", $Dir."install.php");
	}
	mysql_free_result($result);
}

$_ShopData=new _Shopdata($_ShopInfo);
$_data=$_ShopData->shopdata;
$_data->shopurl=$shopurl;

?>