<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "mo-1";
$MenuCode = "mobile";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################


if($_REQUEST[mode]=="write")
{
	if($display!="Y") {	$display = "N";}
	$query = "INSERT INTO tblmobileplanningmain ( pm_idx , title , display , display_type , product_cnt ) VALUES ('', '$title' , '$display' , '$display_type' , '$product_cnt')";
	mysql_query($query,get_db_conn());
	?>
	<script>
		alert("추가하였습니다.");
		parent.opener.location.reload();
		parent.close();
	</script>
	<?
}
else if($_REQUEST[mode]=="modify")
{
	if($display!="Y") {	$display = "N";}
	$query = "update tblmobileplanningmain set title = '$title', display = '$display', display_type = '$display_type', product_cnt = '$product_cnt' where pm_idx = '$_REQUEST[pm_idx]'";
	mysql_query($query,get_db_conn());

	?>
	<script>
		alert("수정하였습니다.");
		parent.opener.location.reload();
		parent.close();
	</script>
	<?
}
else if($_REQUEST[mode]=="del")
{
	mysql_query("delete from tblmobileplanningmain where pm_idx = '$_REQUEST[pm_idx]'");
//	mysql_query("delete from tblmobileplanningproduct where pm_idx = '$_GET[pm_idx]'");
	?>
	<script>
		alert("삭제하였습니다.");
		parent.location.reload();
	</script>
	<?
}
?>


