<?
session_start();
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");


$ordercode = $_GET['ordercode'];

$ordercodeList = explode(",",$ordercode);

if( strlen( $ordercode ) > 0 ) {
	$i=0;
	foreach ( $ordercodeList as $var ) {

		$sel_sql = "SELECT * FROM tblorderinfo WHERE deli_gbn = 'N' AND ( paymethod != 'B' OR ( paymethod = 'B' AND bank_date IS NOT NULL ) ) AND ordercode = '".$var."' LIMIT 1 ;";
		$sel_result = mysql_query($sel_sql,get_db_conn());
		if( mysql_num_rows($sel_result) ) {
			// 주문 상태 변경
			$sql = "UPDATE tblorderinfo SET deli_gbn = 'S' WHERE ordercode = '".$var."' LIMIT 1 ;";
			mysql_query($sql,get_db_conn());
			// 주문 상품 상태 변경
			$sql = "UPDATE tblorderproduct SET deli_gbn = 'S' WHERE ordercode = '".$var."';";
			mysql_query($sql,get_db_conn());
			$i++;
		}
	}

	if($i==0){
		echo "
			<script type=\"text/javascript\">
				<!--
					alert(\"일괄처리가능한 주문건이 없습니다.\");
				//-->
			</script>
		";
	}else{
		echo "
			<script type=\"text/javascript\">
				<!--
					parent.location.reload();
					alert(\"선택하신 주문건 중 발송준비 가능한 주문만 발송준비처리했습니다.\");
				//-->
			</script>
		";
	}

}

?>