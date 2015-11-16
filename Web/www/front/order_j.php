<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$sql = "SELECT * FROM tblpesterinfo WHERE code='".$pstr."' AND state='0'";

$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$pester_code=$row->code;
	$pester_id=$row->id;
	$tempkey=$row->tempkey;
	$sender_name=$row->sender_name;
	$sender_tel=$row->sender_tel;
	$pester_state=$row->state;

	$sql2 = "SELECT group_code FROM tblmember WHERE id='".$pester_id."' ";
	$result2 = mysql_query($sql2);
	if($row2 = mysql_fetch_object($result2)) {
		$group_code=$row2->group_code;
	}
	mysql_free_result($result2);
}
mysql_free_result($result);
if(!$pester_code){
	echo "<script>alert('이미 구매하셨거나 판매 불가한 상품입니다.');if(parent){parent.location.href=\"/\";}else{location.href=\"/\";}</script>";
	exit;
}


$basketsql2 = "SELECT a.productcode,a.package_idx,a.quantity,c.package_list,c.package_title,c.package_price ";
$basketsql2.= "FROM tblbasket_pester_save AS a, tblproduct AS b, tblproductpackage AS c ";
$basketsql2.= "WHERE a.productcode=b.productcode ";
$basketsql2.= "AND b.package_num=c.num ";
$basketsql2.= "AND a.tempkey='".$tempkey."' ";
$basketsql2.= "AND a.package_idx>0 ";
$basketsql2.= "AND b.display = 'Y' ";

$basketresult2 = mysql_query($basketsql2,get_db_conn());
while($basketrow2=@mysql_fetch_object($basketresult2)) {
	if(strlen($basketrow2->package_title)>0 && strlen($basketrow2->package_idx)>0 && $basketrow2->package_idx>0) {
		$package_title_exp = explode("",$basketrow2->package_title);
		$package_price_exp = explode("",$basketrow2->package_price);
		$package_list_exp = explode("", $basketrow2->package_list);

		$title_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx] = $package_title_exp[$basketrow2->package_idx];

		if(strlen($package_list_exp[$basketrow2->package_idx])>1) {
			$basketsql3 = "SELECT productcode,quantity,productname,tinyimage,sellprice FROM tblproduct ";
			$basketsql3.= "WHERE pridx IN ('".str_replace(",","','",$package_list_exp[$basketrow2->package_idx])."') ";
			$basketsql3.= "AND display = 'Y' ";

			$basketresult3 = mysql_query($basketsql3,get_db_conn());
			$sellprice_package_listtmp=0;
			while($basketrow3=@mysql_fetch_object($basketresult3)) {
				$assemble_proquantity[$basketrow3->productcode]+=$basketrow2->quantity;
				$productcode_package_listtmp[] = $basketrow3->productcode;
				$quantity_package_listtmp[] = $basketrow3->quantity;
				$productname_package_listtmp[] = $basketrow3->productname;
				$tinyimage_package_listtmp[] = $basketrow3->tinyimage;
				$sellprice_package_listtmp+= $basketrow3->sellprice;
			}
			@mysql_free_result($basketresult3);

			if(count($productcode_package_listtmp)>0) {  //장바구니 패키지 상품 정보 출력시 필요한 정보
				$price_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx]=0;
				if((int)$sellprice_package_listtmp>0) {
					$price_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx]=(int)$sellprice_package_listtmp;
					if(strlen($package_price_exp[$basketrow2->package_idx])>0) {
						$package_price_expexp = explode(",",$package_price_exp[$basketrow2->package_idx]);
						if(strlen($package_price_expexp[0])>0 && $package_price_expexp[0]>0) {
							$sumsellpricecal=0;
							if($package_price_expexp[1]=="Y") {
								$sumsellpricecal = ((int)$sellprice_package_listtmp*$package_price_expexp[0])/100;
							} else {
								$sumsellpricecal = $package_price_expexp[0];
							}
							if($sumsellpricecal>0) {
								if($package_price_expexp[2]=="Y") {
									$sumsellpricecal = $sellprice_package_listtmp-$sumsellpricecal;
								} else {
									$sumsellpricecal = $sellprice_package_listtmp+$sumsellpricecal;
								}
								if($sumsellpricecal>0) {
									if($package_price_expexp[4]=="F") {
										$sumsellpricecal = floor($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
									} else if($package_price_expexp[4]=="R") {
										$sumsellpricecal = round($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
									} else {
										$sumsellpricecal = ceil($sumsellpricecal/($package_price_expexp[3]*10))*($package_price_expexp[3]*10);
									}
									$price_package_listtmp[$basketrow2->productcode][$basketrow2->package_idx]=$sumsellpricecal;
								}
							}
						}
					}
				}

				$productcode_package_list[$basketrow2->productcode][$basketrow2->package_idx] = $productcode_package_listtmp;
				$quantity_package_list[$basketrow2->productcode][$basketrow2->package_idx] = $quantity_package_listtmp;
				$productname_package_list[$basketrow2->productcode][$basketrow2->package_idx] = $productname_package_listtmp;
				$tinyimage_package_list[$basketrow2->productcode][$basketrow2->package_idx] = $tinyimage_package_listtmp;
			}

			unset($productcode_package_listtmp);
			unset($quantity_package_listtmp);
			unset($productname_package_listtmp);
		}
	}
}
@mysql_free_result($basketresult2);

#수량재고파악
$errmsg="";
$sql = "SELECT a.quantity as sumquantity,b.productcode,b.productname,b.display,b.quantity,b.social_chk, ";
$sql.= "b.option_quantity,b.etctype,b.group_check,b.assembleuse,a.assemble_list AS basketassemble_list ";
$sql.= ", c.assemble_list,a.package_idx ";
$sql.= "FROM tblbasket_pester_save a, tblproduct b ";
$sql.= "LEFT OUTER JOIN tblassembleproduct c ON b.productcode=c.productcode ";
$sql.= "WHERE a.tempkey='".$tempkey."' ";
$sql.= "AND a.productcode=b.productcode ";
$result=mysql_query($sql,get_db_conn());
$assemble_proquantity_cnt=0;
while($row=mysql_fetch_object($result)) {
	if($row->display!="Y") {
		$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 판매가 되지 않는 상품입니다.\\n";
	}
	if($row->social_chk =="Y") {	//소셜상품
		$sql2 = "SELECT count(1) as cnt FROM tblproduct_social WHERE pcode='".$row->productcode."' AND '".time()."' between sell_startdate and sell_enddate ";
		$result2=mysql_query($sql2,get_db_conn());
		if($row2=mysql_fetch_object($result2)) {
			if(strlen($errmsg)==0 && $row2->cnt == 0){
				$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 판매가 종료된 상품입니다.\\n";
			}
		}
	}
	$assemble_list_exp = array();
	if(strlen($errmsg)==0 && $row->assembleuse=="Y") { // 조립/코디 상품 등록에 따른 구성상품 체크
		if(strlen($row->assemble_list)==0) {
			$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 구성상품이 미등록된 상품입니다. 다른 상품을 주문해 주세요.\\n";
		} else {
			$assemble_list_exp = explode("",$row->basketassemble_list);
		}
	}

	if($row->group_check!="N") {
		if(strlen($pester_id)>0) {
			$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
			$sqlgc.= "WHERE productcode='".$row->productcode."' ";
			$sqlgc.= "AND group_code='".$group_code."' ";
			$resultgc=mysql_query($sqlgc,get_db_conn());
			if($rowgc=@mysql_fetch_object($resultgc)) {
				if($rowgc->groupcheck_count<1) {
					$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 지정 등급 전용 상품입니다.\\n";
				}
				@mysql_free_result($resultgc);
			} else {
				$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 지정 등급 전용 상품입니다.\\n";
			}
		} else {
			$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 회원 전용 상품입니다.\\n";
		}
	}

	$package_productcode_tmp = array();
	$package_quantity_tmp = array();
	$package_productname_tmp = array();
	if(strlen($errmsg)==0 && $row->package_idx>0) { // 패키지 상품 등록에 따른 구성상품 체크
		if(count($productcode_package_list[$row->productcode][$row->package_idx])>0) {
			$package_productcode_tmp = $productcode_package_list[$row->productcode][$row->package_idx];
			$package_quantity_tmp = $quantity_package_list[$row->productcode][$row->package_idx];
			$package_productname_tmp = $productname_package_list[$row->productcode][$row->package_idx];
		}
	}

	if(strlen($errmsg)==0) {
		$miniq=1;
		$maxq="?";
		if(strlen($row->etctype)>0) {
			$etctemp = explode("",$row->etctype);
			for($i=0;$i<count($etctemp);$i++) {
				if(substr($etctemp[$i],0,6)=="MINIQ=")     $miniq=substr($etctemp[$i],6);
				if(substr($etctemp[$i],0,5)=="MAXQ=")      $maxq=substr($etctemp[$i],5);
			}
		}

		if(strlen(dickerview($row->etctype,0,1))>0) {
			$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 판매가 되지 않습니다. 다른 상품을 주문해 주세요.\\n";
		}
	}

	if(strlen($errmsg)==0) {
		if ($miniq!=1 && $miniq>1 && $row->sumquantity<$miniq)
			$errmsg.="[".ereg_replace("'","",$row->productname)."]상품은 최소 ".$miniq."개 이상 주문하셔야 합니다.\\n";

		if ($maxq!="?" && $maxq>0 && $row->sumquantity>$maxq)
			$errmsg.="[".ereg_replace("'","",$row->productname)."]상품은 최대 ".$maxq."개 이하로 주문하셔야 합니다.\\n";

		if(strlen($row->quantity)>0) {
			if ($row->sumquantity>$row->quantity) {
				if ($row->quantity>0)
					$errmsg.="[".ereg_replace("'","",$row->productname)."]상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 ".$row->quantity." 개 입니다.")."\\n";
				else
					$errmsg.= "[".ereg_replace("'","",$row->productname)."]상품의 재고가 다른고객 주문등의 이유로 장바구니 수량보다 작습니다.\\n";
			}
		}
		if($assemble_proquantity_cnt==0) { //일반 및 구성상품들의 재고량 가져오기
			///////////////////////////////// 코디/조립 기능으로 인한 재고량 체크 ///////////////////////////////////////////////
			$basketsql = "SELECT productcode,assemble_list,quantity,assemble_idx FROM tblbasket_pester_save WHERE tempkey='".$tempkey."' ";
			$basketresult = mysql_query($basketsql,get_db_conn());
			while($basketrow=@mysql_fetch_object($basketresult)) {
				if($basketrow->assemble_idx>0) {
					if(strlen($basketrow->assemble_list)>0) {
						$assembleprolistexp = explode("",$basketrow->assemble_list);
						for($i=0; $i<count($assembleprolistexp); $i++) {
							if(strlen($assembleprolistexp[$i])>0) {
								$assemble_proquantity[$assembleprolistexp[$i]]+=$basketrow->quantity;
							}
						}
					}
				} else {
					$assemble_proquantity[$basketrow->productcode]+=$basketrow->quantity;
				}
			}
			@mysql_free_result($basketresult);
			$assemble_proquantity_cnt++;
		}
		if(count($assemble_list_exp)>0) { // 구성상품의 재고 체크
			$assemprosql = "SELECT productcode,quantity,productname FROM tblproduct ";
			$assemprosql.= "WHERE productcode IN ('".implode("','",$assemble_list_exp)."') ";
			$assemprosql.= "AND display = 'Y' ";
			$assemproresult=mysql_query($assemprosql,get_db_conn());
			while($assemprorow=@mysql_fetch_object($assemproresult)) {
				if(strlen($assemprorow->quantity)>0) {
					if($assemble_proquantity[$assemprorow->productcode]>$assemprorow->quantity) {
						if($assemprorow->quantity>0) {
							$errmsg.="[".ereg_replace("'","",$row->productname)."]상품의 구성상품 [".ereg_replace("'","",$assemprorow->productname)."] 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 ".$assemprorow->quantity." 개 입니다.")."\\n";
						} else {
							$errmsg.="[".ereg_replace("'","",$row->productname)."]상품의 구성상품 [".ereg_replace("'","",$assemprorow->productname)."] 다른 고객의 주문으로 품절되었습니다.\\n";
						}
					}
				}
			}
			@mysql_free_result($assemproresult);
		} else if(strlen($package_productcode_tmp)>0) {
			$package_productcode_tmpexp = explode("",$package_productcode_tmp);
			$package_quantity_tmpexp = explode("",$package_quantity_tmp);
			$package_productname_tmpexp = explode("",$package_productname_tmp);
			for($i=0; $i<count($package_productcode_tmpexp); $i++) {
				if(strlen($package_productcode_tmpexp[$i])>0) {
					if(strlen($package_quantity_tmpexp[$i])>0) {
						if($assemble_proquantity[$package_productcode_tmpexp[$i]] > $package_quantity_tmpexp[$i]) {
							if($package_quantity_tmpexp[$i]>0) {
								$errmsg.="해당 상품의 패키지 [".ereg_replace("'","",$package_productname_tmpexp[$i])."] 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 ".$package_quantity_tmpexp[$i]." 개 입니다.")."\\n";
							} else {
								$errmsg.="해당 상품의 패키지 [".ereg_replace("'","",$package_productname_tmpexp[$i])."] 다른 고객의 주문으로 품절되었습니다.\\n";
							}
						}
					}
				}
			}
		} else { // 일반상품의 재고 체크
			if(strlen($row->quantity)>0) {
				if($assemble_proquantity[$assemprorow->productcode]>$row->quantity) {
					if ($row->quantity>0) {
						$errmsg.="[".ereg_replace("'","",$row->productname)."]상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 ".$row->quantity." 개 입니다.")."\\n";
					} else {
						$errmsg.= "[".ereg_replace("'","",$row->productname)."]상품의 재고가 다른고객 주문등의 이유로 장바구니 수량보다 작습니다.\\n";
					}
				}
			}
		}
		if(strlen($row->option_quantity)>0) {
			$sql = "SELECT opt1_idx, opt2_idx, quantity FROM tblbasket_pester_save ";
			$sql.= "WHERE tempkey='".$tempkey."' ";
			$sql.= "AND productcode='".$row->productcode."' ";
			$result2=mysql_query($sql,get_db_conn());
			while($row2=mysql_fetch_object($result2)) {
				$optioncnt = explode(",",substr($row->option_quantity,1));
				$optionvalue=$optioncnt[($row2->opt2_idx==0?0:($row2->opt2_idx-1))*10+($row2->opt1_idx-1)];

				if($optionvalue<=0 && $optionvalue!="") {
					$errmsg.="[".ereg_replace("'","",$row->productname)."]상품의 옵션은 다른 고객의 주문으로 품절되었습니다.\\n";
				} else if($optionvalue<$row2->quantity && $optionvalue!="") {
					$errmsg.="[".ereg_replace("'","",$row->productname)."]상품의 선택된 옵션의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"$optionvalue 개 입니다.")."\\n";
				}
			}
			mysql_free_result($result2);
		}
	}
}
mysql_free_result($result);

if(strlen($errmsg)>0) {
	mysql_query("UPDATE tblpesterinfo SET state='3' WHERE code='".$pester_code."'",get_db_conn());
	echo "<html></head><body onload=\"alert('".$errmsg."');location.href='".$Dir.FrontDir."basket.php';\"></body></html>";
	exit;
}

$card_miniprice=$_data->card_miniprice;
$deli_area=$_data->deli_area;
$admin_message = $_data->order_msg;
$reserve_limit = $_data->reserve_limit;
$reserve_maxprice = $_data->reserve_maxprice;
if($reserve_limit==0) $reserve_limit=1000000000000;
if($_data->rcall_type=="Y") {
	$rcall_type = $_data->rcall_type;
	$bankreserve="Y";
} else if($_data->rcall_type=="N") {
	$rcall_type = $_data->rcall_type;
	$bankreserve="Y";
} else if($_data->rcall_type=="M") {
	$rcall_type="Y";
	$bankreserve="N";
} else {
	$rcall_type="N";
	$bankreserve="N";
}
$etcmessage=explode("=",$admin_message);

?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 조르기 결제</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function orderpaypop() {

	var winpaypop=window.open("about:blank","orderpaypop","width=620,height=550,scrollbars=yes");
	winpaypop.focus();

<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {?>
	document.orderpayform.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>orderpay_j.php';
<?}?>

	document.orderpayform.submit();
}

function ordercancel(gbn) {
	if(gbn=="cancel" && document.form1.process.value=="N") {
		document.location.href="../main/main.php";
	} else {
		if (PROCESS_IFRAME.chargepop) {
			if (gbn=="cancel") alert("결제창과 연결중입니다. 취소하시려면 결제창에서 취소하기를 누르세요.");
			PROCESS_IFRAME.chargepop.focus();
		} else {
			PROCESS_IFRAME.PaymentOpen();
			ProcessWait('visible');
		}
	}
}

function ProcessWait(display) {
	var PAYWAIT_IFRAME = document.all.PAYWAIT_IFRAME;

	document.paywait.src = "<?=$Dir?>images/paywait.gif";
	var _x = document.body.clientWidth/2 + document.body.scrollLeft - 250;
	var _y = document.body.clientHeight/2 + document.body.scrollTop - 120;

	PAYWAIT_IFRAME.style.visibility=display;
	PAYWAIT_IFRAME.style.posLeft=_x;
	PAYWAIT_IFRAME.style.posTop=_y;

	PAYWAIT_LAYER.style.posLeft=_x;
	PAYWAIT_LAYER.style.posTop=_y;
	PAYWAIT_LAYER.style.visibility=display;
}

function ProcessWaitPayment() {
	var PAYWAIT_IFRAME = document.all.PAYWAIT_IFRAME;

	document.paywait.src = "<?=$Dir?>images/paywait2.gif";
	var _x = document.body.clientWidth/2 + document.body.scrollLeft - 250;
	var _y = document.body.clientHeight/2 + document.body.scrollTop - 120;

	PAYWAIT_IFRAME.style.visibility='visible';
	PAYWAIT_IFRAME.style.posLeft=_x;
	PAYWAIT_IFRAME.style.posTop=_y;

	PAYWAIT_LAYER.style.visibility='visible';
	PAYWAIT_LAYER.style.posLeft=_x;
	PAYWAIT_LAYER.style.posTop=_y;
}

function PaymentOpen() {
	PROCESS_IFRAME.PaymentOpen();
	ProcessWait('visible');
}

function setPackageShow(packageid) {
	if(packageid.length>0 && document.getElementById(packageid)) {
		if(document.getElementById(packageid).style.display=="none") {
			document.getElementById(packageid).style.display="";
		} else {
			document.getElementById(packageid).style.display="none";
		}
	}
}
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
if(substr($_data->design_order,0,1)=="T") {
	$_data->menu_type="nomenu";
}
include ($Dir.MainDir.$_data->menu_type.".php");
?>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
<?
if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/pester_accept_title.gif")) {
	echo "<td><img src=\"".$Dir.DataDir."design/pester_accept_title.gif\" border=\"0\" alt=\"조르기 수락\"></td>\n";
} else {
	echo "<td>\n";
	echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
	echo "<TR>\n";
	echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/pester_accept_title_head.gif></TD>\n";
	echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/order_title_bg.gif></TD>\n";
	echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/order_title_tail.gif ALT=></TD>\n";
	echo "</TR>\n";
	echo "</TABLE>\n";
	echo "</td>\n";
}
?>
</tr>
<tr>
	<td align=center>

<table cellpadding="0" cellspacing="0" width="90%" align=center>
<tr>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%">
		<form name=form1 action="<?=$Dir.FrontDir?>ordersend_j.php" method=post>
		<input type=hidden name="addorder_msg" value="">
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_stitle1.gif" border="0" vspace="2"></td>
				<td rowspan="2" align="right" valign="bottom" style="font-size:11px;letter-spacing:-0.5pt;"></td>
			</tr>
			<tr>
				<td height="2"></td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<col width="60"></col>
			<col></col>
			<col width="60"></col>
			<col width="75"></col>
			<col width="45"></col>
			<col width="80"></col>
			<tr>
				<td height="2" colspan="6" bgcolor="#000000"></td>
			</tr>
			<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
				<td colspan="2"><font color="#333333"><b>상품명</b></font></td>
				<td><font color="#333333"><b>적립금</b></font></td>
				<td><font color="#333333"><b>상품가격</b></font></td>
				<td><font color="#333333"><b>수량</b></font></td>
				<td><font color="#333333"><b>주문금액</b></font></td>
			</tr>
			<tr>
				<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
			</tr>
<?
	$sql = "SELECT b.vender FROM tblbasket_pester_save a, tblproduct b WHERE a.tempkey='".$tempkey."' ";
	$sql.= "AND a.productcode=b.productcode GROUP BY b.vender ";
	$res=mysql_query($sql,get_db_conn());

	$cnt=0;
	$sumprice = 0;
	$deli_price = 0;
	$reserve = 0;
	$arr_prlist=array();
	while($vgrp=mysql_fetch_object($res)) {
		unset($_vender);
		if($vgrp->vender>0) {
			$sql = "SELECT deli_price, deli_pricetype, deli_mini, deli_limit FROM tblvenderinfo WHERE vender='".$vgrp->vender."' ";
			$res2=mysql_query($sql,get_db_conn());
			if($_vender=mysql_fetch_object($res2)) {
				if($_vender->deli_price==-9) {
					$_vender->deli_price=0;
					$_vender->deli_after="Y";
				}
				if ($_vender->deli_mini==0) $_vender->deli_mini=1000000000;
			}
			mysql_free_result($res2);

		}
		echo "<tr><td colspan=6 height=10></td></tr>\n";

		$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.productcode,b.productname,b.sellprice, ";
		$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
		$sql.= "b.etctype,b.deli_price,b.deli,b.sellprice*a.quantity as realprice, b.selfcode,a.assemble_list,a.assemble_idx,a.package_idx ";
		$sql.= ", a.basketidx, b.sns_state,b.present_state,b.pester_state,b.sns_reserve2,b.sns_reserve2_type, a.sell_memid "; //sns 및 기타 추가기능 정보
		$sql.= "FROM tblbasket_pester_save a, tblproduct b WHERE b.vender='".$vgrp->vender."' ";
		$sql.= "AND a.tempkey='".$tempkey."' ";
		$sql.= "AND a.productcode=b.productcode ";
		$sql.= "ORDER BY a.date DESC ";

		$result=mysql_query($sql,get_db_conn());

		$vender_sumprice = 0;
		$vender_delisumprice = 0;//해당 입점업체의 기본배송비 총 구매액
		$vender_deliprice = 0;
		$deli_productprice=0;
		$deli_init = false;

		while($row = mysql_fetch_object($result)) {
			if (strlen($row->option_price)>0 && $row->opt1_idx==0) {
				$sql = "DELETE FROM tblbasket_pester_save WHERE tempkey='".$tempkey."' ";
				$sql.= "AND productcode='".$row->productcode."' AND opt1_idx='".$row->opt1_idx."' ";
				$sql.= "AND opt2_idx='".$row->opt2_idx."' AND optidxs='".$row->optidxs."' ";
				mysql_query($sql,get_db_conn());

				echo "<script>alert('필수 선택 옵션 항목이 있습니다.\\n옵션을 선택하신후 장바구니에\\n담으시기 바랍니다.');location.href=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\";</script>";
				exit;
			}
			if(ereg("^(\[OPTG)([0-9]{4})(\])$",$row->option1)){
				$optioncode = substr($row->option1,5,4);
				$row->option1="";
				$row->option_price="";
				if($row->optidxs!="") {
					$tempoptcode = substr($row->optidxs,0,-1);
					$exoptcode = explode(",",$tempoptcode);

					$sqlopt = "SELECT * FROM tblproductoption WHERE option_code='".$optioncode."' ";
					$resultopt = mysql_query($sqlopt,get_db_conn());
					if($rowopt = mysql_fetch_object($resultopt)){
						$optionadd = array (&$rowopt->option_value01,&$rowopt->option_value02,&$rowopt->option_value03,&$rowopt->option_value04,&$rowopt->option_value05,&$rowopt->option_value06,&$rowopt->option_value07,&$rowopt->option_value08,&$rowopt->option_value09,&$rowopt->option_value10);
						$opti=0;
						$optvalue="";
						$option_choice = $rowopt->option_choice;
						$exoption_choice = explode("",$option_choice);
						while(strlen($optionadd[$opti])>0){
							if($exoption_choice[$opti]==1 && $exoptcode[$opti]==0){
								$delsql = "DELETE FROM tblbasket_pester_save WHERE tempkey='".$tempkey."' ";
								$delsql.= "AND productcode='".$row->productcode."' ";
								$delsql.= "AND opt1_idx='".$row->opt1_idx."' AND opt2_idx='".$row->opt2_idx."' ";
								$delsql.= "AND optidxs='".$row->optidxs."' ";
								mysql_query($delsql,get_db_conn());
								echo "<script>alert('필수 선택 옵션 항목이 있습니다.\\n옵션을 선택하신후 장바구니에\\n담으시기 바랍니다.');location.href=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\";</script>";
								exit;
							}
							if($exoptcode[$opti]>0){
								$opval = explode("",str_replace('"','',$optionadd[$opti]));
								$optvalue.= ", ".$opval[0]." : ";
								$exop = explode(",",str_replace('"','',$opval[$exoptcode[$opti]]));
								if ($exop[1]>0) $optvalue.=$exop[0]."(<font color=#FF3C00>+".number_format($exop[1])."원</font>)";
								else if($exop[1]==0) $optvalue.=$exop[0];
								else $optvalue.=$exop[0]."(<font color=#FF3C00>".number_format($exop[1])."원</font>)";
								$row->sellprice+=($row->quantity*$exop[1]);
							}
							$opti++;
						}
						$optvalue = substr($optvalue,1);
					}
				}
			} else {
				$optvalue="";
			}

			$cnt++;

			$assemble_str="";
			$package_str="";
			if($row->assemble_idx>0 && strlen(str_replace("","",$row->assemble_list))>0) {
				$assemble_list_proexp = explode("",$row->assemble_list);
				$alprosql = "SELECT productcode,productname,sellprice FROM tblproduct ";
				$alprosql.= "WHERE productcode IN ('".implode("','",$assemble_list_proexp)."') ";
				$alprosql.= "AND display = 'Y' ";
				$alprosql.= "ORDER BY FIELD(productcode,'".implode("','",$assemble_list_proexp)."') ";
				$alproresult=mysql_query($alprosql,get_db_conn());

				$assemble_str ="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
				$assemble_str.="		<td width=\"100%\">\n";
				$assemble_str.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";

				$assemble_sellerprice=0;
				while($alprorow=@mysql_fetch_object($alproresult)) {
					$assemble_str.="		<tr>\n";
					$assemble_str.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;\">\n";
					$assemble_str.="			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
					$assemble_str.="			<col width=\"\"></col>\n";
					$assemble_str.="			<col width=\"80\"></col>\n";
					$assemble_str.="			<col width=\"120\"></col>\n";
					$assemble_str.="			<tr>\n";
					$assemble_str.="				<td style=\"padding:4px;word-break:break-all;\"><font color=\"#000000\">".$alprorow->productname."</font>&nbsp;</td>\n";
					$assemble_str.="				<td align=\"right\" style=\"padding:4px;border-left:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\"><font color=\"#000000\">".number_format((int)$alprorow->sellprice)."원</font></td>\n";
					$assemble_str.="				<td align=\"center\" style=\"padding:4px;\">본 상품 1개당 수량1개</td>\n";
					$assemble_str.="			</tr>\n";
					$assemble_str.="			</table>\n";
					$assemble_str.="			</td>\n";
					$assemble_str.="		</tr>\n";
					$assemble_sellerprice+=$alprorow->sellprice;
				}
				@mysql_free_result($alproresult);
				$assemble_str.="		</table>\n";
				$assemble_str.="		</td>\n";

				//######### 코디/조립에 따른 가격 변동 체크 ###############
				$price = $assemble_sellerprice*$row->quantity;
				$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$assemble_sellerprice,"N");
				//sns홍보일 경우 적립금
				if($_data->recom_ok == "Y" && $_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
					$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$assemble_sellerprice,"N");
				}
				$sellprice=$assemble_sellerprice;
			} else if($row->package_idx>0 && strlen($row->package_idx)>0) {
				$package_str ="<a href=\"javascript:setPackageShow('packageidx".$cnt."');\">".$title_package_listtmp[$row->productcode][$row->package_idx]."(<font color=#FF3C00>+".number_format($price_package_listtmp[$row->productcode][$row->package_idx])."원</font>)</a>";

				$productname_package_list_exp = $productname_package_list[$row->productcode][$row->package_idx];
				if(count($productname_package_list_exp)>0) {
					$packagelist_str ="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
					$packagelist_str.="		<td width=\"100%\">\n";
					$packagelist_str.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";

					for($i=0; $i<count($productname_package_list_exp); $i++) {
						$packagelist_str.="		<tr>\n";
						$packagelist_str.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;\">\n";
						$packagelist_str.="			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
						$packagelist_str.="			<col width=\"\"></col>\n";
						$packagelist_str.="			<col width=\"120\"></col>\n";
						$packagelist_str.="			<tr>\n";
						$packagelist_str.="				<td style=\"padding:4px;word-break:break-all;\"><font color=\"#000000\">".$productname_package_list_exp[$i]."</font>&nbsp;</td>\n";
						$packagelist_str.="				<td align=\"center\" style=\"padding:4px;border-left:1px #DDDDDD solid;\">본 상품 1개당 수량1개</td>\n";
						$packagelist_str.="			</tr>\n";
						$packagelist_str.="			</table>\n";
						$packagelist_str.="			</td>\n";
						$packagelist_str.="		</tr>\n";
					}
					$packagelist_str.="		</table>\n";
					$packagelist_str.="		</td>\n";
				} else {
					$packagelist_str ="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
					$packagelist_str.="		<td width=\"100%\">\n";
					$packagelist_str.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";
					$packagelist_str.="		<tr>\n";
					$packagelist_str.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;padding:4px;word-break:break-all;\"><font color=\"#000000\">구성상품이 존재하지 않는 패키지</font></td>\n";
					$packagelist_str.="		</tr>\n";
					$packagelist_str.="		</table>\n";
					$packagelist_str.="		</td>\n";
				}
				//######### 옵션에 따른 가격 변동 체크 ###############
				if (strlen($row->option_price)==0) {
					$sellprice=$row->sellprice+$price_package_listtmp[$row->productcode][$row->package_idx];
					$price = $sellprice*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$sellprice,"N");
					//sns홍보일 경우 적립금
					if($_data->recom_ok == "Y" && $_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$sellprice,"N");
					}
				} else if (strlen($row->opt1_idx)>0) {
					$option_price = $row->option_price;
					$pricetok=explode(",",$option_price);
					$priceindex = count($pricetok);
					$sellprice=$pricetok[$row->opt1_idx-1]+$price_package_listtmp[$row->productcode][$row->package_idx];
					$price = $sellprice*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$sellprice,"N");
					//sns홍보일 경우 적립금
					if($_data->recom_ok == "Y" && $_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$sellprice,"N");
					}
				}
			} else {
				//######### 옵션에 따른 가격 변동 체크 ###############
				if (strlen($row->option_price)==0) {
					$price = $row->realprice;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"N");
					//sns홍보일 경우 적립금
					if($_data->recom_ok == "Y" && $_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$row->sellprice,"N");
					}
					$sellprice=$row->sellprice;
				} else if (strlen($row->opt1_idx)>0) {
					$option_price = $row->option_price;
					$pricetok=explode(",",$option_price);
					$priceindex = count($pricetok);
					$price = $pricetok[$row->opt1_idx-1]*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$pricetok[$row->opt1_idx-1],"N");
					//sns홍보일 경우 적립금
					if($_data->recom_ok == "Y" && $_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$pricetok[$row->opt1_idx-1],"N");
					}
					$sellprice=$pricetok[$row->opt1_idx-1];
				}
			}

			$sumprice += $price;
			$vender_sumprice += $price;

			$deli_str = "";
			if (($row->deli=="Y" || $row->deli=="N") && $row->deli_price>0) {
				if($row->deli=="Y") {
					$deli_productprice += $row->deli_price*$row->quantity;
					$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#FF3C00>(구매수 대비 증가:".number_format($row->deli_price*$row->quantity)."원)</font></font>";
				} else {
					$deli_productprice += $row->deli_price;
					$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#FF3C00>(".number_format($row->deli_price)."원)</font></font>";
				}
			} else if($row->deli=="F" || $row->deli=="G") {
				$deli_productprice += 0;
				if($row->deli=="F") {
					$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#0000FF>(무료)</font></font>";
				} else {
					$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#38A422>(착불)</font></font>";
				}
			} else {
				$deli_init=true;
				$vender_delisumprice += $price;
			}

			$productname=$row->productname;

			$arr_prlist[$row->productcode]=$row->productname;

			$reserve += $tempreserve*$row->quantity;

			$bankonly_html = ""; $setquota_html = "";
			if (strlen($row->etctype)>0) {
				$etctemp = explode("",$row->etctype);
				for ($i=0;$i<count($etctemp);$i++) {
					switch ($etctemp[$i]) {
						case "BANKONLY": $bankonly = "Y";
							$bankonly_html = " <img src=".$Dir."images/common/bankonly.gif border=0 align=absmiddle> ";
							break;
						case "SETQUOTA":
							if ($_data->card_splittype=="O" && $price>=$_data->card_splitprice) {
								$setquotacnt++;
								$setquota_html = " <img src=".$Dir."images/common/setquota.gif border=0 align=absmiddle>";
								$setquota_html.= "</b><font color=black size=1>(";
								//$setquota_html.="3~";
								$setquota_html.= $_data->card_splitmonth.")</font>";
							}
							break;
					}
				}
			}
?>
			<tr>
				<td align="center" valign="middle" style="padding:2px;">
<?
				if(strlen($row->tinyimage)!=0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)){
					$file_size=getImageSize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					echo "<img src=\"".$Dir.DataDir."shopimages/product/".$row->tinyimage."\"";
					if($file_size[0]>=$file_size[1]) echo " width=\"50\"";
					else echo " height=\"50\"";
					echo " border=\"0\" vspace=\"1\">";
				} else {
					echo "<img src=\"".$Dir."images/no_img.gif\" width=\"50\" border=\"0\" vspace=\"1\">";
				}
?></td>
				<td style="padding:2,0,2,0">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td style="padding-left:2px;word-break:break-all;"><a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode?>"><font color="#000000"><b><?=viewproductname($productname,$row->etctype,$row->selfcode,$row->addcode) ?></b><?=$bankonly_html?><?=$setquota_html?><?=$deli_str?></font></td>
				</tr>

<?				if (strlen($row->option1)>0 || strlen($row->option2)>0 || strlen($optvalue)>0) {
?>
				<tr>
					<td style="padding:1,0,1,0;font-size:11px;letter-spacing:-0.5pt;word-break:break-all;">
					<img src="<?=$Dir?>images/common/icn_option.gif" border="0" align="absmiddle">
<?
					if (strlen($row->option1)>0 && $row->opt1_idx>0) {
						$temp = $row->option1;
						$tok = explode(",",$temp);
						$count=count($tok);
						echo $tok[0]." : ".$tok[$row->opt1_idx]."\n";
					}
					if (strlen($row->option2)>0 && $row->opt2_idx>0) {
						$temp = $row->option2;
						$tok = explode(",",$temp);
						$count=count($tok);
						echo ",&nbsp; ".$tok[0]." : ".$tok[$row->opt2_idx]."\n";
					}
					if(strlen($optvalue)>0) {
						echo $optvalue."\n";
					}
?>
					</td>
				</tr>
<?
			}
			if (strlen($package_str)>0) { // 패키지 정보
?>
				<tr>
					<td width="100%" style="padding-top:2px;font-size:11px;letter-spacing:-0.5pt;line-height:15px;word-break:break-all;"><img src="<?=$Dir?>images/common/icn_package.gif" border="0" align="absmiddle"> <?=(strlen($package_str)>0?$package_str:"")?></td>
				</tr>
<?
			}
?>
				</table>
				</td>
				<? if ($_data->reserve_maxuse>=0 && strlen($pester_id)>0) { ?>
				<td align="right" style="padding-right:5px;"><font color="#333333"><? echo number_format($tempreserve) ?>원</font></td>
				<? } else { ?>
				<td align="center"><font color="#333333">없음</font></td>
				<? } ?>
				<td align="right" style="padding-right:5px;"><font color="#333333"><B><?=number_format($sellprice)?>원</B></font></td>
				<td align="center"><font color="#333333"><?=$row->quantity?>개</font></td>
				<td align="right" style="padding-right:5px;"><b><font color="#000000"><? echo number_format($price) ?>원</font></b></td>
			</tr>
<?
			if (strlen($assemble_str)>0) { // 코디/조립 정보
?>
			<tr>
				<td colspan="6" style="padding:5px;padding-top:0px;padding-left:20px;">
				<table border=0 width="100%" cellpadding="0" cellspacing="0">
				<tr>
				<?=$assemble_str?>
				</tr>
				</table>
				</td>
			</tR>
<?
			}

			if (strlen($packagelist_str)>0) { // 패키지 정보
?>
			<tr id="<?="packageidx".$cnt?>" style="display:none;">
				<td colspan="6" style="padding:5px;padding-top:0px;padding-left:60px;">
				<table border=0 width="100%" cellpadding="0" cellspacing="0">
				<tr>
				<?=$packagelist_str?>
				</tr>
				</table>
				<td>
			</tr>
<?
			}
?>
			<tr><td colspan="6" height="1" bgcolor="#dddddd"></td></tr>
<?
		}
		mysql_free_result($result);

		$vender_deliprice=$deli_productprice;

		if($_vender) {
			if($_vender->deli_price>0) {
				if($_vender->deli_pricetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if ($vender_delisumprice<$_vender->deli_mini && $deli_init==true) {
					$vender_deliprice+=$_vender->deli_price;
				}
			} else if(strlen($_vender->deli_limit)>0) {
				if($_vender->deli_pricetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}
				if($deli_init==true) {
					$delilmitprice = setDeliLimit($vender_delisumprice,$_vender->deli_limit);
					$vender_deliprice+=$delilmitprice;
				}
			}
		} else {
			if($_data->deli_basefee>0) {
				if($_data->deli_basefeetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if ($vender_delisumprice<$_data->deli_miniprice && $deli_init==true) {
					$vender_deliprice+=$_data->deli_basefee;
				}
			} else if(strlen($_data->deli_limit)>0) {
				if($_data->deli_basefeetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if($deli_init==true) {
					$delilmitprice = setDeliLimit($vender_delisumprice,$_data->deli_limit);
					$vender_deliprice+=$delilmitprice;
				}
			}
		}
		$deli_price+=$vender_deliprice;

		echo "<tr>\n";
		echo "	<td colspan=6 style=\"padding:3\">\n";
		echo "	<table border=0 cellpadding=5 cellspacing=0 bgcolor=#efefef width=100% style=\"table-layout:fixed\">\n";
		echo "	<col width=></col>\n";
		echo "	<col width=100></col>\n";
		echo "	<col width=120></col>\n";
		echo "	<col width=100></col>\n";
		echo "	<col width=130></col>\n";
		echo "	<tr>\n";
		echo "		<td bgcolor=#ffffff colspan=5 align=right><FONT COLOR=#000000>배송비</FONT> : <FONT COLOR=#000000>".number_format($vender_deliprice)."원</FONT> / <FONT COLOR=#000000>합계</FONT> : <FONT COLOR=#000000><B>".number_format($vender_sumprice)."원</B></FONT></td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
		echo "<tr><td colspan=6 height=1 bgcolor=\"#404040\"></td></tr>\n";
	}
	mysql_free_result($res);

	if ($cnt!=$setquotacnt && $setquotacnt>0 && $_data->card_splittype=="O") {
		echo "<script> alert('[안내] 무이자적용상품과 일반상품을 같이 주문시 무이자할부적용이 안됩니다.');</script>";
	}

	if($sumprice>0) {
		if(strlen($group_type)>0 && $group_type!=NULL && $sumprice>=$group_usemoney) {
			$salemoney=0;
			$salereserve=0;
			if($group_type=="SW" || $group_type=="SP") {
				if($group_type=="SW") {
					$salemoney=$group_addmoney;
				} else if($group_type=="SP") {
					$salemoney=substr(((int)($sumprice*($group_addmoney/100))),0,-2)."00";
				}
			}
			if($group_type=="RW" || $group_type=="RP" || $group_type=="RQ") {
				if($group_type=="RW") {
					$salereserve=$group_addmoney;
				} else if($group_type=="RP") {
					$salereserve=$reserve*($group_addmoney-1);
				} else if($group_type=="RQ") {
					$salereserve=substr(((int)($sumprice*($group_addmoney/100))),0,-2)."00";
				}
			}
		}

		echo "<tr>\n";
		echo "	<td colspan=6 bgcolor=#ffffff align=right>\n";
		echo "	<table border=0 cellpadding=5 cellspacing=0 bgcolor=#FAFAFA width=100%>\n";
		echo "	<col></col>\n";
		echo "	<col width=120></col>\n";
		echo "	<tr>\n";
		echo "		<td align=right bgcolor=#FAFAFA style=\"padding-right:15\"><FONT COLOR=\"#000000\"><B>상품 합계금액</B></FONT></td>\n";
		echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15\"><FONT COLOR=\"#000000\"><B>".number_format($sumprice)."원</B></FONT></td>\n";
		echo "	</tr>\n";
		if($_data->ETCTYPE["VATUSE"]=="Y") {
			$sumpricevat = return_vat($sumprice);
			echo "	<tr>\n";
			echo "		<td align=right bgcolor=#FAFAFA style=\"padding-right:15\"><FONT COLOR=\"#000000\"><B>부가세(VAT) 합계금액</B></FONT></td>\n";
			echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15\"><FONT COLOR=\"#000000\"><B>+ ".number_format($sumpricevat)."원</B></FONT></td>\n";
			echo "	</tr>\n";
		}
		if($deli_price>0) {
			echo "	<tr>\n";
			echo "		<td align=right bgcolor=#FAFAFA style=\"padding-right:15\"><FONT COLOR=\"#000000\"><B>배송비 합계금액</B></FONT></td>\n";
			echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15\"><FONT COLOR=\"#000000\"><B>+ ".number_format($deli_price)."원</B></FONT></td>\n";
			echo "	</tr>\n";
		}
		if($salemoney>0) {
			echo "	<tr>\n";
			echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15\"><img src=\"".$Dir."images/common/group_orderimg.gif\" align=absmiddle>&nbsp;&nbsp;<b><font color=#FF3C00>".$group_name." 추가 할인</FONT></b></td>\n";
			echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15\"><FONT COLOR=\"#FF3C00\"><B>- ".number_format($salemoney)."원</B></FONT></td>\n";
			echo "	</tr>\n";
		}
		echo "	<tr>\n";
		echo "		<td align=right bgcolor=#FAFAFA style=\"padding-right:15;font-size:17\"><FONT COLOR=\"#000000\"><B>총 결제금액</B></FONT></td>\n";
		echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15;font-size:17\"><FONT COLOR=\"#EE1A02\"><B>".number_format($sumprice+$deli_price+$sumpricevat-$salemoney)."원</B></FONT></td>\n";
		echo "	</tr>\n";
		if($reserve>0 && $_data->reserve_maxuse>=0 && strlen($pester_id)>0) {
			echo "<tr>\n";
			echo "	<td align=right bgcolor=#FAFAFA style=\"padding-right:15\"><FONT COLOR=#0099CC><B>적립금</B></FONT></td>\n";
			echo "	<td align=right bgcolor=#ffffff style=\"padding-right:15\"><FONT COLOR=#0099CC><B>".number_format($reserve)."원</B></FONT></td>\n";
			echo "</tr>\n";
		}

		if($salereserve>0) {
			echo "	<tr>\n";
			echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15\"><img src=\"".$Dir."images/common/group_orderimg.gif\" align=absmiddle>&nbsp;&nbsp;<b><font color=#0000FF>".$group_name." 추가 적립</FONT></b></td>\n";
			echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15\"><FONT COLOR=\"#0000FF\"><B>".number_format($salereserve)."원</B></FONT></td>\n";
			echo "	</tr>\n";
		}
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
		echo "<tr><td colspan=6 height=1 bgcolor=\"#404040\"></td></tr>\n";

	} else {
		echo "<tr height=25><td colspan=6 align=center>쇼핑하신 상품이 없습니다.</td></tr>\n";
		echo "<tr><td colspan=6 height=1 bgcolor=\"#dddddd\"></td></tr>\n";
	}

?>
			</table>
			</td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td align=center>

				<table cellpadding="0" cellspacing="1" width="718" height="70" bgcolor="#E582A3">
					<tr>
						<td bgcolor="#F196B5" style="padding:20px;color:#000;" align="center"><b><font style="color:#ffffff;font-size:16px;">&quot;<?=$sender_name."[".substr($sender_tel,0,-2)."**]" ?>님의 조르기요청에 수락하셨습니다.&quot;</font></b><br><br><b><?=$sender_name ?></b>님께서 배송정보를 모두 기재하셨으므로 결제만 완료하시면 상품이 배송됩니다.</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="30"></td>
		</tr>
	</table>
	</td>
</tr>
</table>

	</td>
</tr>
<tr>
	<td align=center>
	<div id="paybuttonlayer" name="paybuttonlayer" style="display:block;">
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td align=center>
			<!-- <A HREF="javascript:CheckForm()" onmouseover="window.status='결제';return true;"> -->
			<A HREF="/front/order.php?pstr=<?=$pstr?>" onmouseover="window.status='결제';return true;">
			<img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/btn_payment.gif" border=0></A>&nbsp;&nbsp;&nbsp;&nbsp;<A HREF="javascript:ordercancel('cancel')" onmouseover="window.status='취소';return true;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/btn_cancel.gif" border=0></A></td>
	</tr>
	</table>
	</div>
	<div id="payinglayer" name="payinglayer" style="display:none;">
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td align=center><img src="<?=$Dir?>images/common/paying_wait.gif" border=0></td>
	</tr>
	</table>
	</div>
	</td>
</tr>
</table>

<input type=hidden name=process value="N">
<input type=hidden name=paymethod>
<input type=hidden name=pay_data1>
<input type=hidden name=pay_data2>
<input type=hidden name=sender_resno>
<input type=hidden name=pester_code  value="<?=$pester_code?>">
<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {?>
<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<?}?>
<input type=hidden name=ordertype value="<?=$ordertype?>">
</form>


<form name=orderpayform method=post action="<?=$Dir.FrontDir?>orderpay_j.php" target=orderpaypop>
<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {?>
<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<?}?>
<input type=hidden name=coupon_code>
<input type=hidden name=usereserve>
<input type=hidden name=pester_code  value="<?=$pester_code?>">
</form>

<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	paymethod=document.form1.paymethod.value.substring(0,1);

	if(paymethod.length==0) {
		orderpaypop();
		return;
	}

<? if ($_data->payment_type=="Y" || $_data->payment_type=="N") { ?>
	if(paymethod=="B" && document.form1.pay_data1.value.length==0) {
		if(typeof(document.form1.usereserve)!="undefined") {
			if(document.form1.usereserve.value<<?=$sumprice-$salemoney?>) {
				alert("은행을 선택하세요.");
				orderpaypop();
				return;
			}
		} else {
			alert("은행을 선택하세요.");
			orderpaypop();
			return;
		}
	}
<? } ?>

	if(document.form1.process.value=="N") {

		document.form1.process.value="Y";
		document.form1.target = "PROCESS_IFRAME";

<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {?>
		document.form1.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>ordersend_j.php';
<?}?>

document.form1.submit();

		document.all.paybuttonlayer.style.display="none";
		document.all.payinglayer.style.display="block";

		if(paymethod!="B") ProcessWait("visible");

	} else {
		ordercancel();
	}
}

//-->
</SCRIPT>

<DIV id="PAYWAIT_LAYER" style='position:absolute; left:50px; top:120px; width:503; height: 255; z-index:1; visibility: hidden'><a href="JavaScript:PaymentOpen();"><img src="<?=$Dir?>images/paywait.gif" align=absmiddle border=0 name=paywait galleryimg=no></a></DIV>
<IFRAME id="PAYWAIT_IFRAME" name="PAYWAIT_IFRAME" style="left:50px; top:120px; width:503; height: 255; position:absolute; visibility:hidden"></IFRAME>
<!--IFRAME id=PROCESS_IFRAME name=PROCESS_IFRAME style="display:''" width=100% height=300></IFRAME-->
<IFRAME id=PROCESS_IFRAME name=PROCESS_IFRAME style="display:none"></IFRAME>

<?=$onload?>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>