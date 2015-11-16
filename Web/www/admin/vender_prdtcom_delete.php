<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "vd-1";
$MenuCode = "vender";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_GET["type"];
$seq=$_GET["idx"];
$productcode=$_GET["productcode"];

if (!$type) {
	?>
	<script type="text/javascript">
		<!--
		alert("자료가 올바르지 않습니다.");
	   / -->
	</script>
	<?
	exit();
}

if ($type=="all") {
	
	if (!$productcode) {
		?>
		<script type="text/javascript">
			<!--
			alert("자료가 올바르지 않습니다.");
		   / -->
		</script>
		<?
		exit();
	}

	$sql = "delete from commission_history where productcode='".$productcode."'";
	mysql_query($sql,get_db_conn());

}else if($type=="one"){

	if (!$seq) {
		?>
		<script type="text/javascript">
			<!--
			alert("자료가 올바르지 않습니다.");
		   / -->
		</script>
		<?
		exit();
	}
	
	$sql = "delete from commission_history where seq='".$seq."'";
	mysql_query($sql,get_db_conn());

}

?>

<script type="text/javascript">
<!--
	alert("삭제되었습니다.");
	parent.location.reload();
// -->
</script>