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

	echo "<html><head><title></title></head><body onload=\"alert('신청 되었습니다.');opener.location.reload();window.close();\"></body></html>";
	exit();
}

if(strlen($ordercode)<=0 || strlen($productcode)<=0) {
	echo "<html><head><title></title></head><body onload=\"alert('다시 시도해주시기 바랍니다.');window.close();\"></body></html>";exit;
}



?>

<html>
<head>
<title>부분 주문 취소</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
#refundAccount {display:none}
td {font-family:돋음;color:333333;font-size:9pt;}

tr {font-family:돋음;color:333333;font-size:9pt;}
BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:돋음;color:333333;font-size:9pt;}

A:link    {color:333333;text-decoration:none;}

A:visited {color:333333;text-decoration:none;}

A:active  {color:333333;text-decoration:none;}

A:hover  {color:#CC0000;text-decoration:none;}
</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
window.moveTo(10,10);
window.resizeTo(800,450);
window.name="order_cancel_pop";


function view_product(productcode) {
	opener.location.href="<?=$Dir.FrontDir?>productdetail.php?productcode="+productcode;
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
<center>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td align=center style="padding:10,10,10,10">
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
	<tr><td align=center height=30 bgcolor=#454545><FONT COLOR="#FFFFFF"><B>부분 주문 취소 요청</B></FONT></td></tr>
<?
	
		$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
		$result=mysql_query($sql,get_db_conn());
		if($orderInfo=mysql_fetch_object($result)) {
			


		} else {
			echo "<tr height=200><td align=center>조회하신 주문내역이 없습니다.</td></tr>\n";
			echo "<tr><td align=center><input type=button value='닫 기' style=\"cursor:hand;color:#FFFFFF;border-color:#666666;background-color:#666666;font-size:8pt;font-family:Tahoma;height:20px;width:70\" onclick=\"window.close()\"></td></tr>\n";
			echo "</table>";
			exit;
		}
		mysql_free_result($result);
	
?>
	<tr><td height="10"></td></tr>
	<tr>
		<td><span style="float:left"><img src=<?=$Dir?>images/icon_dot.gif border=0 align=absmiddle> <B>주문상품 정보</B></span>
		</td>
	</tr>
	<tr>
		<td>
		<table border=0 cellpadding=0 cellspacing=1 bgcolor=E7E7E7 width=100% style="table-layout:fixed">
		<col width=45></col>
		<col width=></col>
		<col width=75></col>
		<col width=75></col>
		<col width=30></col>
		<col width=70></col>
		<col width="70"></col>
		<tr height=28 bgcolor=#F5F5F5>
			<td align=center>이미지</td>
			<td align=center>상품명</td>
			<td align=center>선택사항1</td>
			<td align=center>선택사항2</td>
			<td align=center>수량</td>
			<td align=center>가격</td>
			<td align=center>배송료</td>
		</tr>
<?
		//$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
		$delicomlist=getDeliCompany();
		
		$sql_product = "";
		foreach($productcodes as $pc) {

			if ($sql_product=="") {
				$sql_product = "'".$pc."'";
			}else{
				$sql_product .= ",'".$pc."'";
			}
		}
		
		$sql_uid = "";
		foreach($uids as $us) {

			if ($sql_uid=="") {
				$sql_uid = "'".$us."'";
			}else{
				$sql_uid .= ",'".$us."'";
			}
		}

		$sql = "SELECT op.*,op.quantity*op.price as sumprice,p.tinyimage,p.minimage FROM tblorderproduct op left join tblproduct p on (op.productcode = p.productcode) WHERE op.ordercode='".$ordercode."' and op.productcode in(".$sql_product.") AND op.uid in(".$sql_uid.") AND NOT (op.productcode LIKE 'COU%' OR op.productcode LIKE '999999%') order by  op.vender ";

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


			$packagestr = "";
			$packageliststr = "";
			$assemblestr = "";
			$rowspanstr = 1;
			if(strlen(str_replace("","",str_replace(":","",str_replace("=","",$row->assemble_info))))>0) {
				$assemble_infoall_exp = explode("=",$row->assemble_info);

				if($row->package_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))>0) {
					$rowspanstr++;
					$package_info_exp = explode(":", $assemble_infoall_exp[0]);
					$packagestr.="<br><img src=\"".$Dir."images/common/icn_package.gif\" border=0 align=absmiddle> ".$package_info_exp[3]."(<font color=#FF3C00>+".number_format($package_info_exp[2])."원</font>)";
					$productname_package_list_exp = explode("",$package_info_exp[1]);
					$packageliststr.="<tr bgcolor=\"#FFFFFF\">\n";
					$packageliststr.="	<td colspan=\"6\" style=\"padding-left:5px;\">\n";
					$packageliststr.= "	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
					$packageliststr.= "	<tr>\n";
					if(count($productname_package_list_exp)>0 && strlen($productname_package_list_exp[0])>0) {
						$packageliststr.= "		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
						$packageliststr.= "		<td width=\"100%\">\n";
						$packageliststr.= "		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;\">\n";

						for($k=0; $k<count($productname_package_list_exp); $k++) {
							$packageliststr.= "		<tr>\n";
							$packageliststr.= "			<td bgcolor=\"#FFFFFF\"".($k>0?"style=\"border-top:1px #DDDDDD solid;\"":"").">\n";
							$packageliststr.= "			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
							$packageliststr.= "			<col width=\"\"></col>\n";
							$packageliststr.= "			<col width=\"124\"></col>\n";
							$packageliststr.= "			<tr>\n";
							$packageliststr.= "				<td style=\"padding:4px;word-break:break-all;font-size:8pt;\"><font color=\"#000000\">".$productname_package_list_exp[$k]."</font>&nbsp;</td>\n";
							$packageliststr.= "				<td align=\"center\" style=\"padding:4px;border-left:1px #DDDDDD solid;font-size:8pt;\">본 상품 1개당 수량1개</td>\n";
							$packageliststr.= "			</tr>\n";
							$packageliststr.= "			</table>\n";
							$packageliststr.= "			</td>\n";
							$packageliststr.= "		</tr>\n";
						}
						$packageliststr.= "		</table>\n";
						$packageliststr.= "		</td>\n";
					} else {
						$packageliststr.= "		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
						$packageliststr.= "		<td width=\"100%\">\n";
						$packageliststr.= "		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;\">\n";
						$packageliststr.= "		<tr>\n";
						$packageliststr.= "			<td bgcolor=\"#FFFFFF\" style=\"padding:4px;word-break:break-all;font-size:8pt;\"><font color=\"#000000\">구성상품이 존재하지 않는 패키지</font></td>\n";
						$packageliststr.= "		</tr>\n";
						$packageliststr.= "		</table>\n";
						$packageliststr.= "		</td>\n";
					}
					$packageliststr.= "	</tr>\n";
					$packageliststr.= "	</table>\n";
					$packageliststr.="	</td>\n";
					$packageliststr.="</tr>\n";
				}

				if($row->assemble_idx>0 && strlen(str_replace("","",str_replace(":","",$assemble_infoall_exp[1])))>0) {
					$rowspanstr++;
					$assemblestr.="<tr bgcolor=\"#FFFFFF\">\n";
					$assemblestr.="	<td colspan=\"6\" style=\"padding-left:5px;\">\n";
					$assemblestr.="	<table border=0 width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n";
					$assemblestr.="	<tr>\n";
					$assemblestr.="		<td width=\"50\" valign=\"top\" style=\"padding-left:5px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
					$assemblestr.="		<td width=\"100%\">\n";
					$assemblestr.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;\">\n";

					$assemble_info_exp = explode(":", $assemble_infoall_exp[1]);

					if(count($assemble_info_exp)>2) {
						$assemble_productname_exp = explode("", $assemble_info_exp[1]);
						$assemble_sellprice_exp = explode("", $assemble_info_exp[2]);

						for($k=0; $k<count($assemble_productname_exp); $k++) {
							$assemblestr.="		<tr>\n";
							$assemblestr.="			<td bgcolor=\"#FFFFFF\"".($k>0?"style=\"border-top:1px #DDDDDD solid;\"":"").">\n";
							$assemblestr.="			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
							$assemblestr.="			<col width=\"\"></col>\n";
							$assemblestr.="			<col width=\"67\"></col>\n";
							$assemblestr.="			<col width=\"124\"></col>\n";
							$assemblestr.="			<tr>\n";
							$assemblestr.="				<td style=\"padding:4px;word-break:break-all;font-size:8pt;\">".$assemble_productname_exp[$k]."&nbsp;</td>\n";
							$assemblestr.="				<td align=\"right\" style=\"padding:4px;border-left:1px #DDDDDD solid;border-right:1px #DDDDDD solid;font-size:8pt\">".number_format((int)$assemble_sellprice_exp[$k])."</td>\n";
							$assemblestr.="				<td align=\"center\" style=\"padding:4px;font-size:8pt\">본 상품 1개당 수량1개</td>\n";
							$assemblestr.="			</tr>\n";
							$assemblestr.="			</table>\n";
							$assemblestr.="			</td>\n";
							$assemblestr.="		</tr>\n";
						}
					}
					@mysql_free_result($alproresult);
					$assemblestr.="		</table>\n";
					$assemblestr.="		</td>\n";
					$assemblestr.="	</tr>\n";
					$assemblestr.="	</table>\n";
					$assemblestr.="	</td>\n";
					$assemblestr.="</tr>\n";
				}
			}

			echo "<tr bgcolor=#FFFFFF>\n";
			echo "	<td align=center rowspan=\"".$rowspanstr."\" style=\"padding:2px;\">\n";

			if(strlen($row->minimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->minimage)==true){
				echo "		<span onMouseOver='ProductMouseOver($cnt)' onMouseOut=\"ProductMouseOut('primage".$cnt."');\">";
				echo "		<img src=".$Dir.DataDir."shopimages/product/".urlencode($row->minimage)." border=0 width=40 height=40>";
				echo "		</span>\n";
				echo "		<div id=primage".$cnt." style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
				echo "		<table border=0 cellspacing=0 cellpadding=0 width=170>\n";
				echo "		<tr bgcolor=#FFFFFF>\n";
				echo "			<td align=center width=100% height=150 style=\"border:#000000 solid 1px\"><img src=".$Dir.DataDir."shopimages/product/".urlencode($row->minimage)."></td>\n";
				echo "		</tr>\n";
				echo "		</table>\n";
				echo "		</div>\n";
			} else {
				echo '<img src="'.$Dir.'images/no_img.gif" border=0 width=40 height=40>';
			}
			echo "	</td>\n";
			echo "	<td style=\"font-size:8pt;padding:5,5,5,5\">";
			if (substr($row->productcode,0,3)!="999" && substr($row->productcode,0,3)!="COU") {
				echo "<a href=\"javascript:view_product('".$row->productcode."')\">";
			}
			echo ($row->sumprice<0?"<font color=#0000FF>":"").$row->productname.(strlen($row->addcode)>0?" - $row->addcode":"")."</a>";
			if(strlen($optvalue)>0) {
				echo "<br><img src=\"".$Dir."images/common/icn_option.gif\" border=0 align=absmiddle> ".$optvalue."";
			}
			if(strlen($packagestr)>0) {
				echo $packagestr;
			}
			echo "	</td>\n";
			echo "	<td align=center style=\"font-size:8pt\">".$row->opt1_name."</td>\n";
			echo "	<td align=center style=\"font-size:8pt\">".$row->opt2_name."</td>\n";
			echo "	<td align=center style=\"font-size:8pt\">".$row->quantity."</td>\n";
			echo "	<td align=right style=\"font-size:8pt;padding-right:5\"><FONT COLOR=\"#EE1A02\"><B>".number_format($row->sumprice)."</B></FONT></td>\n";
			echo "  <td align=right style=\"font-size:8pt;padding-right:5\">";

			if ($deli_view_chk>0) {
				echo number_format($deli_data['price']);
			}
			echo "</td>\n";

			echo "</tr>\n";
			echo $assemblestr;
			echo $packageliststr;
			$cnt++;
		}
		@mysql_free_result($result);

	 if ($is_reRefunds>0) { ?>
	<script type="text/javascript">
	<!--
		alert("이미 취소 되었거나 취소할 수 없는 건 입니다.");
		self.close();
	//-->
	</script>
	<? 
		exit();	
	} 
	
	$deli_value = "";
	foreach($deli_vender as $dv) {

		if ($deli_value=="") {
			$deli_value = $dv;
		}else{
			$deli_value .= "$$".$dv;
		}
	}

?>

		</table>
		</td>
	</tr>
	<tr><td height=8></td></tr>
	<tr>
	<tr>
		<td style="font-size:11px; font-family:돋움; color:#666666; padding-left:5px;">
			<span style="float:left;">- 부분취소로 인해 발생되는 배송비 및 제반경비를 제외하고 환불처리될 수 있습니다.</span><br/>
			<span style="float:left;">- 주문취소가 완료되면 지급예정된 적립금 및 주문시 사용쿠폰이 모두 취소되며,<br/>&nbsp;&nbsp;취소된 주문건은 다시 되돌릴 수 없습니다.</span>
		</td>
	</tr>
	<form name=form1 action="order_one_cancel_pop.php" method=post>
	<input type=hidden name=type>
	<input type=hidden name=ordercode value="<?= $ordercode ?>">
	<input type=hidden name=productcode value="<?= $productcode ?>">
	<input type=hidden name=uid value="<?= $uid ?>">
	<input type=hidden name=vender value="<?= $deli_value ?>">

	<? if (preg_match("/^(B){1}/",$orderInfo->paymethod)) {
			$sql = "SELECT * FROM order_refund_account WHERE ordercode='".$ordercode."'";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);

			mysql_free_result($result);
		?>
	<tr><td height=18></td></tr>
	<tr>
	<tr>
		<td><span style="float:left"><img src=<?=$Dir?>images/icon_dot.gif border=0 align=absmiddle> <B>환불계좌 </B></span>
		</td>
	</tr>
	<tr>
		<td>
		<table border=0 cellpadding=0 cellspacing=1 bgcolor=E7E7E7 width=100% style="table-layout:fixed">
			<tr height=28 bgcolor=#F5F5F5>
				<th width="100">환불은행</th>
				<td bgcolor=#FFFFFF>&nbsp;&nbsp;<input type="text" name="bank" size="10" value="<?= $row->bank ?>"/></td>
			</tr>
			<tr height=28 bgcolor=#F5F5F5>
				<th width="100">예금주</th>
				<td bgcolor=#FFFFFF>&nbsp;&nbsp;<input type="text" name="account_name" size="10" value="<?= $row->account_name ?>"/></td>
			</tr>
			<tr height=28 bgcolor=#F5F5F5>
				<th width="100">계좌번호</th>
				<td bgcolor=#FFFFFF>&nbsp;&nbsp;<input type="text" name="account_num" size="30" value="<?= $row->account_num ?>"/></td>
			</tr>
		</table>
		</td>
	</tr>
	<? } ?>
	<tr><td height=10></td></tr>
	<tr>
	<tr>
		<td align="center">
			<span style="cursor:pointer" onclick="send_it();"><img src="/images/common/ordercancel_icon1.gif" alt="환불신청" /></span>&nbsp;
			<span style="cursor:pointer" onclick="self.close();"><img src="/images/common/ordercancel_icon2.gif" alt="취소" /></span>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>

</form>
</body>
</html>
