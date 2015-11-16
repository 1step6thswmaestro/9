<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");

INCLUDE ("access.php");


// CS 관리 수정
if( $_POST['code'] AND $_POST['mode'] == "adminCS_save" ) {

	$SQL = "UPDATE `tbl_csManager` SET  ";
	if( $_POST['allComplete'] == "completeRegDateOK" ) {
		$SQL .= " `completeRegDate` = NOW() ";
		$msg = "저장 및 처리완료 등록!";
	}else {
		$SQL .= " `deli_com` = '".$_POST['deli_com']."'
					, `deli_num` = '".$_POST['deli_num']."'
					, `back_deli_com` = '".$_POST['back_deli_com']."'
					, `back_deli_num` = '".$_POST['back_deli_num']."'
		";
		$msg = "저장완료!";
	}
	$SQL .= "WHERE `idx` = ".$_POST['code']."; ";

	$RESULT=mysql_query($SQL,get_db_conn());
	echo "
		<script type=\"text/javascript\">
		<!--
			alert('".$msg."');
			location.href='vender_cs_view.php?code=".$_POST['code']."';
		//-->
		</script>
	";
}


// CS 관리 업체정산 수정
if( $_POST['code'] AND $_POST['mode'] == "adminCS_pay_save" ) {

	$SQL = "UPDATE `tbl_csManager` SET  ";
	$SQL .= "
					  `deliPay` = '".$_POST['deliPay']."'
					, `orderPay` = '".$_POST['orderPay']."'
					, `orderPayMemo` = '".$_POST['orderPayMemo']."'
	";
	echo $SQL .= "WHERE `idx` = ".$_POST['code']."; ";

	$RESULT=mysql_query($SQL,get_db_conn());

	$msg = "업체정산 저장완료!";

	echo "
		<script type=\"text/javascript\">
		<!--
			alert('".$msg."');
			location.href='vender_cs_view.php?code=".$_POST['code']."';
		//-->
		</script>
	";
}


?>