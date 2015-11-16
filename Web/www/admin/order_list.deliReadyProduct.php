<?
session_start();
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");


$ordercode = $_GET['ordercode'];
$delitype = $_GET['delitype'];

$ordercodeList = explode(",",$ordercode);

if( strlen( $ordercode ) > 0 ) {
	foreach ( $ordercodeList as $var ) {
		//echo $var;
		//$orderProducts = explode("|",$var);
		// 주문 상품 상태 변경
		//if( !empty($orderProducts[0]) AND !empty($orderProducts[1]) ) {
		if( !empty($var) ) {
			$sql = "UPDATE tblorderproduct SET deli_gbn = '".$delitype."' WHERE uid = '".$var."';"; //  AND productcode = '".$orderProducts[1]."' LIMIT 1
			mysql_query($sql,get_db_conn());
		}
		// 주문 상태 변경
		//$sql = "UPDATE tblorderinfo SET deli_gbn = '".$delitype."' WHERE deli_gbn = 'N' AND ordercode = '".$orderProducts[0]."' LIMIT 1 ;";
		//mysql_query($sql,get_db_conn());
	}

		//if ( 1 ) {
			echo "
				<script type=\"text/javascript\">
				<!--
					parent.location.reload();
				//-->
				</script>
			";
		//}
}

?>