<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/order_func.php");

$ordercode=$_REQUEST["ordercode"];	//�ֹ���ȣ
$productcode=$_REQUEST["productcode"];	//��ǰ�ڵ�
$uid=$_REQUEST["uid"];	//��ǰ�ڵ�
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

	echo "<html><head><title></title><meta http-equiv=\"CONTENT-TYPE\" content=\"text/html;charset=EUC-KR\"></head><body onload=\"alert('��û �Ǿ����ϴ�.');opener.location.reload();window.close();\"></body></html>";
	exit();
}

if(strlen($ordercode)<=0 || strlen($productcode)<=0) {
	echo "<html><head><title></title><meta http-equiv=\"CONTENT-TYPE\" content=\"text/html;charset=EUC-KR\"></head><body onload=\"alert('�ٽ� �õ����ֽñ� �ٶ��ϴ�.');window.close();\"></body></html>";exit;
}



?>

<html>
<head>
<title>�κ� �ֹ� ���</title>
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
			alert("ȯ�Ұ��� ���� ���� �Է����ּ���.");
			frm.bank.focus();
			return;
		}
		if (frm.account_name.value =='') {
			alert("�����ָ� �Է����ּ���.");
			frm.account_name.focus();
			return;
		}
		if (frm.account_num.value =='') {
			alert("���¹�ȣ �Է����ּ���.");
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
			�κ� �ֹ���� ��û
		</h1>
		<?
	
			$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
			$result=mysql_query($sql,get_db_conn());
			if($orderInfo=mysql_fetch_object($result)) {
				
			} else {
			?>
				<div class="orderdetail_nodata">
					<p class="nodata_msg_top">��ȸ�Ͻ� �ֹ������� �����ϴ�.</p>
					<button class="nodata_close" type="button" onClick="window.close();">�ݱ�</button>
				</div>
			<?
				exit;
			}
			mysql_free_result($result);
		?>
		<!-- ��ǰ ���� START -->
		<div class="ordercancel_ct">
			<div class="ordercancel_prinfo_wrap">
				<h2>�ֹ���ǰ ����</h2>
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
												<th>��ǰ��</th>
												<td>
													<div><b><?=$row->productname?></b></div>
													<div><?=$row->opt1_name?><?=$row->opt2_name?></div>
												</td>
											</tr>
											<tr>
												<th>�ǸŰ�</th>
												<td><?=number_format($row->sumprice)?>��</td>
											</tr>
											<tr>
												<th>����</th>
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
					- �κ���ҷ� ���� �߻��Ǵ� ��ۺ� �� ���ݰ�� �����ϰ� ȯ��ó���� �� �ֽ��ϴ�.<br/>
					- �ֹ���Ұ� �Ϸ�Ǹ� ���� ������ ������ �� �ֹ��� ��������� ��� ��ҵǸ�, ��ҵ� �ֹ����� �ٽ� �ǵ��� �� �����ϴ�.
				</div>
				<!-- ��ǰ ���� END -->

				<!-- ȯ�Ұ��� �Է� START -->
				<h2>ȯ�Ұ��� ����</h2>
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
									<th>ȯ������</th>
									<td><input class="cancle_fieldtype="text" name="bank" value="<?= $row->bank ?>"/></td>
								</tr>
								<tr>
									<th>������</th>
									<td><input type="text" name="account_name" value="<?= $row->account_name ?>"/></td>
								</tr>
								<tr>
									<th>���¹�ȣ</th>
									<td><input type="text" name="account_num" value="<?= $row->account_num ?>"/></td>
								</tr>
							</tbody>
						</table>
						<?}?>
					</form>
				</div>
				<!-- ȯ�Ұ��� �Է� END -->

			</div>
		</div>
		<div class="ordercancel_button"><a href="#" class="button black" onClick="send_it();">ȯ�ҽ�û</a> <a href="#" class="button white" onClick="self.close();">���</a></div>
	</div>
</body>
</html>