<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "go-1";
$MenuCode = "gong";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$code = $_POST['code'];
$mode = $_POST['mode'];
if(!$mode) $mode="modify";
?>
<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>

<?
switch($mode){
	case "write":
	case "modify":
		include("social_shopping_add.php");
		break;
	case "insert":
	case "update":
	case "delete":
	case "delprdtimg":
		include("social_shopping_proc.php");
		break;
	case "list":
	default:
		include("social_shopping_list.php");
		break;
}

?>
<? INCLUDE "copyright.php"; ?>