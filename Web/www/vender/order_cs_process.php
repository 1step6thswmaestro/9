<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");

$isaccesspass=true;
INCLUDE ("access.php");

if( $_POST['code'] AND $_POST['mode'] == "venderCS_save" ) {

	$msg = "저장완료!";

	$SQL = "UPDATE `tbl_csManager` SET `venderMemo` = '".$_POST['venderMemo']."' ";
	if( $_POST['deli_com'] ) $SQL .= ", `deli_com` = '".$_POST['deli_com']."' ";
	if( $_POST['deli_num'] ) $SQL .= ", `deli_num` = '".$_POST['deli_num']."' ";
	if( $_POST['venderBackMemo'] ) $SQL .= ", `venderBackMemo` = '".$_POST['venderBackMemo']."' ";
	if( $_POST['back_deli_com'] ) $SQL .= ", `back_deli_com` = '".$_POST['back_deli_com']."' ";
	if( $_POST['back_deli_num'] ) $SQL .= ", `back_deli_num` = '".$_POST['back_deli_num']."' ";

	$SQL .= ", `backCHK` = '".$_POST['venderBackCHK']."' ";
	if( $_POST['venderComplete'] == "venderRegDateOK" ) {
		$SQL .= ", `venderRegDate` = NOW() ";
		$msg = "저장 및 처리완료 등록!";
	}
	$SQL .= "WHERE `idx` = ".$_POST['code']."; ";

	$RESULT=mysql_query($SQL,get_db_conn());
	echo "
		<script type=\"text/javascript\">
		<!--
			alert('".$msg."');
			location.href='order_cs_view.php?code=".$_POST['code']."';
		//-->
		</script>
	";
}

?>