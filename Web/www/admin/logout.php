<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$id = $_ShopInfo->getId();
$move_url = "http://".$_ShopInfo->getShopurl();

$_ShopInfo->setId("");
$_ShopInfo->setAuthkey("");
$_ShopInfo->Save();


$connect_ip=getenv("REMOTE_ADDR");

if(strlen($id)>0) {
	mysql_query("UPDATE tblsecurityadmin SET authkey='' WHERE id='".$id."'",get_db_conn());

	$log_content = "로그아웃 : $id";
	ShopManagerLog($id,$connect_ip,$log_content);
}

?>
<body onload="logout_gourl()">
<SCRIPT LANGUAGE="JavaScript">
<!--
function logout_gourl() {
	alert("로그아웃되었습니다.");
	try {
		top.location.href="<?=$move_url?>";
	} catch (e) {
		location.href="<?=$move_url?>";
	}
}
//-->
</SCRIPT>
</body>