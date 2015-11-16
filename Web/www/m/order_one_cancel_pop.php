<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/order_func.php");

$ordercode=$_REQUEST["ordercode"];	//주문번호
$productcode=$_REQUEST["productcode"];	//상품코드
$uid=$_REQUEST["uid"];	//상품코드
$type = $_POST["type"];

$productcodes = explode("$$", $productcode);
$uids = explode("$$", $uid);

if ($type=='insert') {

	$bank=$_POST["bank"];
	$account_name=$_POST["account_name"];
	$account_num=$_POST["account_num"];
	$deli_chk=$_POST["deli_chk"];
	$deli_vender=$_POST["vender"];
	
	$i=0;
	foreach($productcodes as $pc) {

		$sql = "update tblorderproduct set status='RA' where ordercode='".$ordercode."' and productcode='".$pc."' and uid='".$uids[$i]."' and status='' ";
		mysql_query($sql,get_db_conn());

		$sql = "delete from part_cancel_want where uid='".$uids[$i]."'";
		mysql_query($sql,get_db_conn());
		
		$sql = "insert into part_cancel_want(uid, requestor, reg_date) values('".$uids[$i]."','1',now()) ";
		mysql_query($sql,get_db_conn());

		$i++;
	}

	if (strlen($deli_vender)>0) {
		
		$venders = explode("$$", $deli_vender);
		
		foreach($venders as $vender) {
			
			$sql = "update tblorderproduct set status='RA' where ordercode='".$ordercode."' and vender='".$vender."' and productcode='99999999990X' and status='' ";
			mysql_query($sql,get_db_conn());
			
			$sql = "select uid from tblorderproduct where ordercode='".$ordercode."' and vender='".$vender."' and productcode='99999999990X' and status='RA' ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);

			if ($row->uid) {
				$sql = "delete from part_cancel_want where uid='".$row->uid."'";
				mysql_query($sql,get_db_conn());
				
				$sql = "insert into part_cancel_want(uid, requestor, reg_date) values('".$row->uid."','1',now()) ";
				mysql_query($sql,get_db_conn());
			}

		}

	}
	
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

	echo "<html><head><title></title><meta http-equiv=\"CONTENT-TYPE\" content=\"text/html;charset=EUC-KR\"></head><body onload=\"alert('신청 되었습니다.');opener.location.reload();window.close();\"></body></html>";
	exit();
}

if(strlen($ordercode)<=0 || strlen($productcode)<=0) {
	echo "<html><head><title></title><meta http-equiv=\"CONTENT-TYPE\" content=\"text/html;charset=EUC-KR\"></head><body onload=\"alert('다시 시도해주시기 바랍니다.');window.close();\"></body></html>";exit;
}



?>

<html>
<head>
<title>부분 주문 취소</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta name="format-detection" content="telephone=no" />
<link rel="stylesheet" href="./css/common.css" />
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>

<SCRIPT LANGUAGE="JavaScript">
<!--
//window.moveTo(10,10);
//window.resizeTo(800,450);
//window.name="order_cancel_pop";

function view_product(productcode) {
	opener.location.href="./productdetail_tab01.php?productcode="+productcode;
}

function ProductMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.primage"+cnt);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.visibility = "visible";
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	Obj = document.getElementById(Obj);
	Obj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

function send_it() {

	frm = document.form1;

	if (typeof  frm.bank != "undefined") {
		
		if (frm.bank.value =='') {			
			alert("환불계좌 은행 명을 입력해주세요.");
			frm.bank.focus();
			return;
		}
		if (frm.account_name.value =='') {
			alert("예금주를 입력해주세요.");
			frm.account_name.focus();
			return;
		}
		if (frm.account_num.value =='') {
			alert("계좌번호 입력해주세요.");
			frm.account_num.focus();
			return;
		}
	}
	
	document.form1.type.value="insert";
	frm.submit();
	
}


//-->
</SCRIPT>
</head>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0>
	<div class="ordercancel_wrap">
		<h1>
			부분 주문취소 요청
		</h1>
		<?
	
			$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
			$result=mysql_query($sql,get_db_conn());
			if($orderInfo=mysql_fetch_object($result)) {
				
			} else {
			?>
				<div class="orderdetail_nodata">
					<p class="nodata_msg_top">조회하신 주문내역이 없습니다.</p>
					<button class="nodata_close" type="button" onClick="window.close();">닫기</button>
				</div>
			<?
				exit;
			}
			mysql_free_result($result);
		?>
		<!-- 상품 정보 START -->
		<div class="ordercancel_ct">
			<div class="ordercancel_prinfo_wrap">
				<h2>주문상품 정보</h2>
				<div class="ordercancel_prlist">
					<?
						$delicomlist=getDeliCompany();
						$sql_product = "";
						foreach($productcodes as $pc) {
							if ($sql_product=="") {
								$sql_product = "'".$pc."'";
							}else{
								$sql_product .= ",'".$pc."'";
							}
						}

						$sql = "SELECT op.*,op.quantity*op.price as sumprice,p.tinyimage,p.minimage FROM tblorderproduct op left join tblproduct p on (op.productcode = p.productcode) WHERE op.ordercode='".$ordercode."' and op.productcode in(".$sql_product.") AND NOT (op.productcode LIKE 'COU%' OR op.productcode LIKE '999999%') order by  op.vender ";

						$result = mysql_query($sql,get_db_conn());

							$cnt=0;

							$is_reRefunds = 0;
							$deli_vender = array();
							$dd = 0;

							while($row=mysql_fetch_object($result)) {
							//foreach($orderproducts as $row) {
								
								if ($row->status) {
									$is_reRefunds++;
								}
								
								$deli_chk = 0;
								$deli_view_chk = 0;
								for ($i=0;$i<$dd;$i++) {
									if ($deli_vender[$i]==$row->vender) {
										$deli_chk++;
									}
								}
								
								if ($deli_chk==0) {
									$sql = "select * from tblorderproduct where ordercode='".$ordercode."' and vender='".$row->vender."' AND status='' AND productcode='99999999990X' ";
									$result2 = mysql_query($sql,get_db_conn());
									$deli_data = mysql_fetch_assoc($result2);
									mysql_free_result($result2);
									
									if ($deli_data['price']>0) {

										
										$sql = "select count(*) as cnt from tblorderproduct where ordercode='".$ordercode."' and vender='".$row->vender."' and deli_gbn in ('Y', 'N') and status='' AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%')  ";
										$result2 = mysql_query($sql,get_db_conn());
										$goods_count = mysql_fetch_assoc($result2);
										mysql_free_result($result2);
								
										$sql = "select count(*) as cnt from tblorderproduct where ordercode='".$ordercode."' and vender='".$row->vender."' and deli_gbn in ('Y', 'N') and status='' AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') and productcode in(".$sql_product.")";
										$result2 = mysql_query($sql,get_db_conn());
										$want_count = mysql_fetch_assoc($result2);

										if ($goods_count==$want_count) {
											$deli_vender[$dd] = $deli_data['vender'];
											$deli_view_chk = 1;
											$dd++;
										}
									}
								}


								if (substr($row->productcode,0,3)=="999" || substr($row->productcode,0,3)=="COU") {
									if ($gift_check=="N" && strpos($row->productcode,"GIFT")!==false) $gift_check="Y";
									$etcdata[]=$row;

									if(strpos($row->productcode,"GIFT")!==false) {
										$giftdata[]=$row;
									}

									continue;
								}
								$gift_tempkey=$row->tempkey;
								$taxsaveprname.=$row->productname.",";

								$optvalue="";
								if(ereg("^(\[OPTG)([0-9]{3})(\])$",$row->opt1_name)) {
									$optioncode=$row->opt1_name;
									$row->opt1_name="";
									$sql = "SELECT opt_name FROM tblorderoption WHERE ordercode='".$ordercode."' AND productcode='".$row->productcode."' ";
									$sql.= "AND opt_idx='".$optioncode."' ";
									$result2=mysql_query($sql,get_db_conn());
									if($row2=mysql_fetch_object($result2)) {
										$optvalue=$row2->opt_name;
									}
									mysql_free_result($result2);
								}

								if($row->status!='RC') $in_reserve+=$row->quantity*$row->reserve;
					?>
								<span class="ordercancel_pr_image"><img src="<?=$Dir.DataDir?>shopimages/product/<?=urlencode($row->minimage)?>" border=0 width=40 height=40></span>
								<div class="ordercancel_pr_info">
									<table cellpadding="0" cellspacing="0" border="0">
										<tbody>
											<tr>
												<th>상품명</th>
												<td>
													<div><b><?=$row->productname?></b></div>
													<div><?=$row->opt1_name?><?=$row->opt2_name?></div>
												</td>
											</tr>
											<tr>
												<th>판매가</th>
												<td><?=number_format($row->sumprice)?>원</td>
											</tr>
											<tr>
												<th>수량</th>
												<td><?=$row->quantity?></td>
											</tr>
										</tbody>
									</table>
								</div>
					<?
						}
					?>

				</div>
				<div style="clear:both; margin:5px 10px;">
					- 부분취소로 인해 발생되는 배송비 및 제반경비를 제외하고 환불처리될 수 있습니다.<br/>
					- 주문취소가 완료되면 지급 예정된 적립금 및 주문시 사용쿠폰이 모두 취소되며, 취소된 주문건은 다시 되돌릴 수 없습니다.
				</div>
				<!-- 상품 정보 END -->

				<!-- 환불계좌 입력 START -->
				<h2>환불계좌 정보</h2>
				<div class="refund">
					<form name=form1 action="order_one_cancel_pop.php" method=post>
						<input type=hidden name=type>
						<input type=hidden name=ordercode value="<?= $ordercode ?>">
						<input type=hidden name=productcode value="<?= $productcode ?>">
						<input type=hidden name=vender value="<?= $deli_value ?>">
						<? if (preg_match("/^(B){1}/",$orderInfo->paymethod)) {
							$sql = "SELECT * FROM order_refund_account WHERE ordercode='".$ordercode."'";
							$result=mysql_query($sql,get_db_conn());
							$row=mysql_fetch_object($result);

							mysql_free_result($result);
				
						?>
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tbody>
								<tr>
									<th>환불은행</th>
									<td><input class="cancle_fieldtype="text" name="bank" value="<?= $row->bank ?>"/></td>
								</tr>
								<tr>
									<th>예금주</th>
									<td><input type="text" name="account_name" value="<?= $row->account_name ?>"/></td>
								</tr>
								<tr>
									<th>계좌번호</th>
									<td><input type="text" name="account_num" value="<?= $row->account_num ?>"/></td>
								</tr>
							</tbody>
						</table>
						<?}?>
					</form>
				</div>
				<!-- 환불계좌 입력 END -->

			</div>
		</div>
		<div class="ordercancel_button"><a href="#" class="button black" onClick="send_it();">환불신청</a> <a href="#" class="button white" onClick="self.close();">취소</a></div>
	</div>
</body>
</html>