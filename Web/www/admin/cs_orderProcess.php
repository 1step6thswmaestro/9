<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	INCLUDE ("access.php");

	// 등록
	if( $_POST['mode'] == "csInsert" ) {
		$SQL = "
			INSERT `tbl_csManager` SET
				`vender` = '".$_POST['vender']."',
				`order` = '".$_POST['order']."',
				`product` = '".$_POST['product']."',
				`member` = '".$_POST['member']."',
				`title` = '".$_POST['title']."',
				`type` = '".$_POST['type']."',
				`adminMemo` = '".$_POST['adminMemo']."',
				`adminRegDate` = NOW()
		";

		if( $_POST['delivery'] ) {
			$SQL .= ", `delivery` = '".$_POST['delivery']."' ";
		}
		if( $_POST['customer'] ) {
			$SQL .= ", `customer` = '".$_POST['customer']."' ";
		}
		//echo $SQL;
		mysql_query($SQL,get_db_conn());
		echo "
			<script>
				alert('CS등록 완료.');
				opener.history.go(0);
				window.close();
			</script>
		";
	}



	// 수정
	if( $_POST['mode'] == "csModify" AND $_POST['idx']) {
		$SQL = "
			UPDATE `tbl_csManager` SET
				`vender` = '".$_POST['vender']."',
				`order` = '".$_POST['order']."',
				`product` = '".$_POST['product']."',
				`member` = '".$_POST['member']."',
				`title` = '".$_POST['title']."',
				`type` = '".$_POST['type']."',
				`adminMemo` = '".$_POST['adminMemo']."',
				`adminRegDate` = NOW()
		";

		if( $_POST['delivery'] ) {
			$SQL .= ", `delivery` = '".$_POST['delivery']."' ";
		}
		if( $_POST['customer'] ) {
			$SQL .= ", `customer` = '".$_POST['customer']."' ";
		}
		$SQL .= " WHERE `idx` = '".$_POST['idx']."' ";
		//echo $SQL;
		mysql_query($SQL,get_db_conn());
		echo "
			<script>
				alert('CS 수정 완료.');
				opener.history.go(0);
				window.close();
			</script>
		";
	}
?>