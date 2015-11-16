<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/admin_more.php");
include_once($Dir."lib/ext/func.php");
INCLUDE ("access.php");


$_ShopInfo->getPgdata();

$date = date('Ymd');

$ordercode = $_POST[ordercode];
$type = $_POST[type];
$goods = $_POST[goods];
$status = $_POST[status];
$bank = $_POST[bank];
$account_name = $_POST[account_name];
$account_num = $_POST[account_num];
$reserve = $_POST[reserve];
$re_reserve_max = $_POST[re_reserve_max];
$re_price_max = $_POST[re_price_max];

// 할인 대비액
$dcPriceSend = ( empty($_POST[dcPriceSend]) ? 0 : $_POST[dcPriceSend] );
// 환불액
$re_price = ( empty($_POST[re_price]) ? 0 : $_POST[re_price] );
// 비과세
$refund_free = ( empty($_POST[refund_free]) ? 0 : $_POST[refund_free] );
// 과세적용 금액 ( 할인액에서 면세액제외, 할인 대비액 제외 )
$price_tax = $re_price - $refund_free; // - $dcPriceSend;
//부과세 ( 과세적용 금액의 세액 )
$refund_vat = floor($price_tax / 11);
//공급가 ( 과세금액 : 과세적용 금액에서 세액제외  )
$refund_tax = $price_tax-$refund_vat;


$pginfo=array();
if(strlen($_data->trans_id)>0){
	$pginfo = GetEscrowType($_data->trans_id);
	$pgcorpinfo = trim($pginfo['PG']);
}

if ($type=="order") {

	$status	= $_POST['status'];
	$goods = $_POST['goods'];

	$goodsOrg = $goods;
	$goods = explode("|",$goods);

	if ($status=='RC') {

		// 취소 금액
		$re_price = $_POST['re_price'];

		//현재금액
		$now_price = 0;
		$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."'";
		$result=mysql_query($sql,get_db_conn());
		$_ord=mysql_fetch_object($result);
		$now_price = $_ord->price;
		mysql_free_result($result);



		//현재까지 환불금
		$sum_re = 0;
		$sql = "SELECT sum(price) as sum_re FROM tblorderproduct WHERE ordercode='".$ordercode."' and productcode='99999999995X' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$sum_re = $row->sum_re;
		mysql_free_result($result);


		if ($re_price > ($now_price-$sum_re)) {
			echo "<script type=\"text/javascript\"><!-- alert(\"현재 잔액보다 많이 환불할 수는 없습니다.\"); history.back(); //--></script>";
			exit;
		}

		/*if ($re_price > $now_price ) {
			?>
			<script type="text/javascript">
			<!--
				alert("현재 잔액보다 많이 환불할 수는 없습니다.<?=$re_price?>-<?=$now_price?>");
				history.back();
		   //-->
			</script>
			<?
			exit();
		}*/


		//임시테이블에도 같이 저장 확인. uid 충돌 안나게.
		$re_count = 0;
		$sql = "SELECT count(*) as cnt FROM tblorderproducttemp WHERE ordercode='".$ordercode."' and productcode='99999999995X' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$re_count = ($row->cnt) + 1;
		mysql_free_result($result);








		//카드결제면 부분취소 모듈후 상태확인
		$c_status='C';
		if (preg_match("/^(C){1}/",$_ord->paymethod) && $_ord->pay_flag=="0000") {
			$c_status='A';

			if ($refund_free) {

				if ($refund_free > $re_price) {
					?>
					<script type="text/javascript">
					<!--
						alert("취소요청 비과세가 전체 금액보다 큽니다.");
						history.back();
					//-->
					</script>
					<?
					exit();
				}

				$sql = "select * from card_orderinfo_tax where ordercode='".$ordercode."'";
				$result=mysql_query($sql,get_db_conn());
				$row=mysql_fetch_object($result);
				$free_mny = $row->free_mny;
				mysql_free_result($result);


				//취소 완료된건
				$sql = "select sum(cancel_free) as cancel_free from card_part_cancel_log  where ordercode='".$ordercode."'";
				$result=mysql_query($sql,get_db_conn());
				$row=mysql_fetch_object($result);
				$cancel_free = $row->cancel_free;
				mysql_free_result($result);


				//취소 신청중인건
				$sql = "select sum(free_mny) as want_cancel_free from card_part_cancel_tax_free  where ordercode='".$ordercode."' and status=0";
				$result=mysql_query($sql,get_db_conn());
				$row=mysql_fetch_object($result);
				$want_cancel_free = $row->want_cancel_free;
				mysql_free_result($result);


				$free_mny = $free_mny - $cancel_free - $want_cancel_free;

				if ($refund_free > $free_mny) {
					?>
					<script type="text/javascript">
					<!--
						alert("취소요청 비과세가 취소가능 비과세 금액보다 큽니다.");
						history.back();
				   //-->
					</script>
					<?
					exit();
				}

			}
		}





		// 카드 결제 정보
		if (preg_match("/^(C){1}/",$_ord->paymethod)) {

			$remain_price = 0; // 결제금액
			$remain_tax = 0; // 공급가
			$remain_vat = 0; // 부과세
			$remain_free = 0; // 비과세

			$sql = "SELECT * FROM card_orderinfo_tax WHERE ordercode='".$ordercode."' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				$remain_price = $row->amount;
				$remain_tax = $row->tax_mny;
				$remain_vat = $row->vat_mny;
				$remain_free = $row->free_mny;
			}else{
				echo "<script>alert('".get_message("결제 자료가 손상되었습니다.")."');history.go(-1);</script>\n";
				exit;
			}

			$sql = "SELECT * FROM card_part_cancel_log WHERE ordercode='".$ordercode."' order by reg_date desc limit 0, 1";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {

				$remain_price = $row->remain_price;
				$remain_tax = $row->remain_tax;
				$remain_vat = $row->remain_vat;
				$remain_free = $row->remain_free;

			}

			if ($refund_tax>$remain_tax) {
				?>
				<script type="text/javascript">
				<!--
					alert("PG사에 취소 신청 할 수 있는 과세금액보다 큰 액수를 입력하셨습니다. \n비과세 부분을 검토하여 다시 입력해주십시오.\n취소 가능 금액 : <?= $remain_price ?>\n취소 가능 비과세 : <?= $remain_free ?>");
					history.back();
			   //-->
				</script>
				<?
				exit();
			}

			if ($refund_free>$remain_free) {
				?>
				<script type="text/javascript">
				<!--
					alert("PG사에 취소 신청 할 수 있는 비과세보다 큰 액수를 입력하셨습니다. 다시 시도해주세요.\n취소 가능 금액 : <?= $remain_price ?>\n취소 가능 비과세 : <?= $remain_free ?>");
					history.back();
			   //-->
				</script>
				<?
				exit();
			}

		}


		if ($reserve>0) {
			//적립금 조회
			$now_reserve = 0;
			$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."'";
			$result=mysql_query($sql,get_db_conn());
			$_ord=mysql_fetch_object($result);

			$now_reserve = $_ord->reserve;
			mysql_free_result($result);

			//취소내용 조회
			$sql = "select sum(cancel_reserve) as cancel_reserve from part_cancel_reserve where ordercode='".$ordercode."'";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);

			$cancel_reserve = $row->cancel_reserve;
			mysql_free_result($result);

			$now_reserve = $now_reserve-$cancel_reserve;

			if ($now_reserve<$reserve) {
				?>
				<script type="text/javascript">
				<!--
					alert("환원 가능한 액수보다 큰액수를 입력하셨습니다.");
					history.back();
			   //-->
				</script>
				<?
				exit();
			}
		}

	}

	//부분 취소 상품명
	$cancel_product_name = "";

	$p_count = 0;
	$p_name = array();
	$b_count = 0;
	$b_name = array();

	$cou_chk = 0;

	for($i=0;$i<count($goods);$i++) {

		//부분 취소 상품명 만들기
		$sql = "select productcode, productname from tblorderproduct where uid='".$goods[$i]."'";
		$result=mysql_query($sql,get_db_conn());
		$data=mysql_fetch_array($result);

		$productcode = $data['productcode'];
		$productname = $data['productname'];

		mysql_free_result($result);

		if (substr($productcode)!="COU") {

			//정산 수수료 정리
			redeliveryOrderAdjustDetailByProduct($ordercode, $goods[$i]);

			if	($productcode=="99999999990X") {
				$b_name[$b_count]=$productname;

				$b_count++;
			}else{
				$p_name[$p_count]=$productname;
				$p_count++;
			}

			/** 부분취소 요청 거부**/
			$sql = "UPDATE tblorderproduct SET status = '".$status."' WHERE uid='".$goods[$i]."'";
			mysql_query($sql,get_db_conn());
			/** 부분취소 요청 거부**/

			if ($status=='' || $status=='RA') {
			$sql = "delete from part_cancel_want where uid='".$goods[$i]."'";
			mysql_query($sql,get_db_conn());
			}

		}else{
			$cou_chk++;
		}
	}

	if (count($goods)==$cou_chk) {
		?>
		<script type="text/javascript">
		<!--
			alert("쿠폰만 환불처리 할 수 없습니다.");
			history.back();
	   //-->
		</script>
		<?
		exit();
	}

	if ($p_count>0) {
		$cancel_product_name = "부분 취소 : ".$p_name[0];
		if ($p_count>1) {
			$p_count--;
			$cancel_product_name .= " 외 ".$p_count."건";
		}

		if ($b_count>0) {
			$cancel_product_name .= ", 배송료 ".$b_count."건";
		}

	}else{
		$cancel_product_name = "부분 취소 : ".$b_name[0];
		if ($b_count>1) {
			$b_count--;
			$cancel_product_name .= " 외 ".$b_count."건";
		}
	}

	if ($status=='RC') {

		//임시테이블에도 같이 저장. uid 충돌 안나게.
		$sql = "insert into tblorderproducttemp(vender, ordercode, tempkey, productcode, productname, quantity, price, deli_gbn, status, `date`, opt1_name) values (0, '".$ordercode."', '".$_ord->tempkey."', '99999999995X', '".$cancel_product_name."', 1, '".$re_price."', 'N', '".$c_status."','".$date."', '".$re_count."')";
		mysql_query($sql,get_db_conn());

		$cancel_uid = mysql_insert_id();

		$sql = "insert into tblorderproduct ";
		$sql .= " select * from tblorderproducttemp where uid='".$cancel_uid."'";
		mysql_query($sql,get_db_conn());



		if (preg_match("/^(C){1}/",$_ord->paymethod)) {

			$sql = "insert into card_part_cancel_tax_free(ordercode, cancel_uid, amount, tax_mny, vat_mny, free_mny, status, reg_date) ";
			$sql .= " values ('".$ordercode."', '".$cancel_uid."', '".$re_price."', '".$refund_tax."', '".$refund_vat."', '".$refund_free."', 0, now())";
			mysql_query($sql,get_db_conn());
		}

		//적립금

		if ($reserve>0) {
			$sql = "UPDATE tblmember SET reserve=reserve+".abs($reserve)." ";
			$sql.= "WHERE id='".$_ord->id."' ";
			mysql_query($sql,get_db_conn());

			$sql = "INSERT tblreserve SET ";
			$sql.= "id			= '".$_ord->id."', ";
			$sql.= "reserve		= '".$reserve."', ";
			$sql.= "reserve_yn	= 'Y', ";
			$sql.= "content		= '주문 부분 취소건에 대한 적립금 환원', ";
			$sql.= "orderdata	= '".$ordercode."=".$_ord->price."', ";
			$sql.= "date		= '".date("YmdHis")."' ";
			mysql_query($sql,get_db_conn());

			$log_content="## 회원 적립금 환원 ## - 주문번호 : ".$ordercode." - 적립금 ".$reserve;
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

			$remain_reserve = $now_reserve - $reserve;

			$sql = "insert into part_cancel_reserve(ordercode, cancel_reserve, org_reserve, remain_reserve, memo,reg_date) values ('".$ordercode."', '".$reserve."', '".$now_reserve."', '".$remain_reserve."', '".$cancel_product_name."',now()) ";
			mysql_query($sql,get_db_conn());
		}

		// 환불후 잔액이 0-이면 전체 취소 처리
		if( ($now_price - $re_price) <= 0 ) {
			$sql = "UPDATE tblorderinfo SET deli_gbn = 'C', bank_date = '".date("Ymd")."X' WHERE ordercode = '".$ordercode."' LIMIT 1 ; ";
			mysql_query($sql,get_db_conn());
		}
		switch($pgcorpinfo){
			case "G": // 올앳
				?>
					<form name="partcancelForm" method="post" action="<?=$Dir?>paygate/G/cancel.php"/>
						<input type="hidden" name="ordercode" value="<?=$ordercode?>"/>
						<input type="hidden" name="CancelAmt" value="<?=$re_price?>"/>
						<input type="hidden" name="SupplyAmt" value="<?=$refund_tax?>"/> <!--  공급가 -->
						<input type="hidden" name="GoodsVat" value="<?=$refund_vat?>"/> <!-- 부가세 -->
						<input type="hidden" name="ServiceAmt" value="0"/>
						<input type="hidden" name="TaxFreeAmt" value="<?=$refund_free?>"/> <!-- 면세금액 -->
						<input type="hidden" name="cancel_uid" value="<?=$cancel_uid?>"/>
						<input type="hidden" name="goodsOrg" value="<?=$goodsOrg?>"/>
						<input type="hidden" name="type" value="P"/>
					</form>
					<script> document.partcancelForm.submit();</script>
				<?
				exit;
			break;
			case "E": // 나이스
				echo "<form name=niceform method=post action=\"".$Dir."paygate/E/cancel.php\">\n";
				echo "<input type=hidden name=ordercode value=\"".$ordercode."\">\n";
				echo "<input type=hidden name=TID value=\"".$_ord->pay_auth_no."\">\n";
				echo "<input type=hidden name=CancelAmt value=\"".$re_price."\">\n";
				echo "<input type=hidden name=CancelMsg value=\"관리자 부분취소\">\n";
				echo "<input type=hidden name=PartialCancelCode value=\"1\">\n"; // 전체 취소 : 0 , 부분취소 : 1

				// 과세/비과세
				echo "<input type=hidden name=SupplyAmt value=\"".$refund_tax."\">\n"; //공급가
				echo "<input type=hidden name=GoodsVat value=\"".$refund_vat."\">\n"; //부과세
				echo "<input type=hidden name=ServiceAmt value=\"0\">\n"; //봉사료
				echo "<input type=hidden name=TaxFreeAmt value=\"".$refund_free."\">\n"; //면세 금액

				echo "<input type=hidden name=cancel_uid value=\"".$cancel_uid."\">\n"; // 부분 취소 기록 코드
				echo "<input type=hidden name=goodsOrg value=\"".$goodsOrg."\">\n"; // 부분 취소 상품 목록

				echo "</form>\n";
				echo "<script> document.niceform.submit(); </script>";
				exit;
			break;
		}
	}

}else if ($type=="bank") {

	$sql = "SELECT count(*) as cnt FROM order_refund_account WHERE ordercode='".$ordercode."'";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);

	if ($row->cnt > 0 ) {
		$sql = "update order_refund_account set bank='".$bank."', account_name='".$account_name."', account_num='".$account_num."' WHERE ordercode='".$ordercode."'";

	}else{
		$sql = "insert into order_refund_account(ordercode, bank, account_name, account_num, reg_date)";
		$sql .= " values ('".$ordercode."', '".$bank."', '".$account_name."', '".$account_num."', now())";
	}

	mysql_query($sql,get_db_conn());



	//적립금

	if ($reserve>0) {
		$sql = "UPDATE tblmember SET reserve=reserve+".abs($reserve)." ";
		$sql.= "WHERE id='".$_ord->id."' ";
		mysql_query($sql,get_db_conn());

		$sql = "INSERT tblreserve SET ";
		$sql.= "id			= '".$_ord->id."', ";
		$sql.= "reserve		= '".$reserve."', ";
		$sql.= "reserve_yn	= 'Y', ";
		$sql.= "content		= '주문 부분 취소건에 대한 적립금 환원', ";
		$sql.= "orderdata	= '".$ordercode."=".$_ord->price."', ";
		$sql.= "date		= '".date("YmdHis")."' ";
		mysql_query($sql,get_db_conn());

		$log_content="## 회원 적립금 환원 ## - 주문번호 : ".$ordercode." - 적립금 ".$reserve;
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

		$remain_reserve = $now_reserve - $reserve;

		$sql = "insert into part_cancel_reserve(ordercode, cancel_reserve, org_reserve, remain_reserve, memo,reg_date) values ('".$ordercode."', '".$reserve."', '".$now_reserve."', '".$remain_reserve."', '".$cancel_product_name."',now()) ";
		mysql_query($sql,get_db_conn());
	}


	/*$sql = "SELECT count(*) as cnt FROM order_refund_account WHERE ordercode='".$ordercode."'";
	$result=mysql_query($sql,get_db_conn());
	if (mysql_num_rows($result) && mysql_result($result,0,0) > 0) {
		$sql = "update order_refund_account set bank='".$bank."', account_name='".$account_name."', account_num='".$account_num."' WHERE ordercode='".$ordercode."'";

	}else{
		$sql = "insert into order_refund_account(ordercode, bank, account_name, account_num, reg_date)";
		$sql .= " values ('".$ordercode."', '".$bank."', '".$account_name."', '".$account_num."', now())";
	}

	mysql_query($sql,get_db_conn());
	*/

}
?>
<form name="f" action="order_detail.php" method="post">
<input type="hidden" name="ordercode" value="<?=$ordercode?>">
</form>
<script>
document.f.submit();
</script>