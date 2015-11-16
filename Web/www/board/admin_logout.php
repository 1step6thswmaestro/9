<?
if(substr(getenv("SCRIPT_NAME"),-17)=="/admin_logout.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}

include ("head.php");

if($member[admin]!="SU") {
	header("Location:board.php?pagetype=list&board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage&boardsep=$boardsep");
	exit;
}

$cadname=$board."_ADMIN";
$cadnamrarray=getBoardCookieArray($_ShopInfo->getBoardadmin());
$cadnamrarray[$cadname]="";
$_ShopInfo->setBoardadmin(serialize($cadnamrarray));
$_ShopInfo->Save();

header("Location:board.php?pagetype=list&board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage&boardsep=$boardsep");
exit;
?>