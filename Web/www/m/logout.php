<?
$Dir = "../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
//include_once($Dir."lib/shopdata.php");  //원래 web에서 logout 처리가 포함되어있다

$sql = "UPDATE tblmember SET authidkey='logout' WHERE id='".$_ShopInfo->getMemid()."' ";
mysql_query($sql,get_db_conn());

$_ShopInfo->SetMemNULL();
$_ShopInfo->Save();
$url = "./";
echo "<script>location.href='".$url."';</script>";
?>