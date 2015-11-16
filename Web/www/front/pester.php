<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

//장바구니 인증키 확인
if(strlen($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
	$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
}

//sns 홍보 본인체크
if(strlen($_ShopInfo->getMemid()) > 0){
	$sql ="UPDATE tblbasket SET sell_memid ='' WHERE tempkey='".$_ShopInfo->getTempkey()."' AND sell_memid='".$_ShopInfo->getMemid()."'";
	mysql_query($sql,get_db_conn());
}

$ordertype=$_REQUEST["ordertype"];
if($ordertype =="pester"){
	$tblbasket = "tblbasket";
	$returnUrl = "productdetail.php?productcode=";
}else{
	$tblbasket = "tblbasket";
	$ordertype = "pester2";
	$returnUrl = "basket.php";
}

$basketsql2 = "SELECT a.productcode,a.package_idx,a.quantity,c.package_list,c.package_title,c.package_price ";
$basketsql2.= "FROM ".$tblbasket." AS a, tblproduct AS b, tblproductpackage AS c ";
$basketsql2.= "WHERE a.productcode=b.productcode ";
$basketsql2.= "AND b.package_num=c.num ";
$basketsql2.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
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
$sql.= "FROM ".$tblbasket." a, tblproduct b ";
$sql.= "LEFT OUTER JOIN tblassembleproduct c ON b.productcode=c.productcode ";
$sql.= "WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
$sql.= "AND a.productcode=b.productcode ";
$result=mysql_query($sql,get_db_conn());
$assemble_proquantity_cnt=0;
while($row=mysql_fetch_object($result)) {
	$returnUrl = ($ordertpe == "pester")? $returnUrl.$row->productcode:$returnUrl;
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
		if(strlen($_ShopInfo->getMemid())>0) {
			$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
			$sqlgc.= "WHERE productcode='".$row->productcode."' ";
			$sqlgc.= "AND group_code='".$_ShopInfo->getMemgroup()."' ";
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
			$basketsql = "SELECT productcode,assemble_list,quantity,assemble_idx FROM ".$tblbasket." WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
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
			$sql = "SELECT opt1_idx, opt2_idx, quantity FROM ".$tblbasket." ";
			$sql.= "WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
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
	echo "<html></head><body onload=\"alert('".$errmsg."');location.href='".$Dir.FrontDir.$returnUrl."';\"></body></html>";
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

$user_reserve=0;
if(strlen($_ShopInfo->getMemid())>0) {
	$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
	$result = mysql_query($sql);
	if($row = mysql_fetch_object($result)) {
		$user_reserve = $row->reserve;
		if($user_reserve>$reserve_limit) {
			$okreserve=$reserve_limit;
			$remainreserve=$user_reserve-$reserve_limit;
		} else {
			$okreserve=$user_reserve;
			$remainreserve=0;
		}
		$home_addr="";
		if(strlen($row->home_post)==6) {
			$home_post1=substr($row->home_post,0,3);
			$home_post2=substr($row->home_post,3,3);
		}
		$row->home_addr = ereg_replace("\"","",$row->home_addr);
		$home_addr = explode("=",$row->home_addr);
		$home_addr1 = $home_addr[0];
		$home_addr2 = $home_addr[1];

		$office_addr="";
		if(strlen($row->office_post)==6) {
			$office_post1=substr($row->office_post,0,3);
			$office_post2=substr($row->office_post,3,3);
		}
		$row->office_addr = ereg_replace("\"","",$row->office_addr);
		$office_addr = explode("=",$row->office_addr);
		$office_addr1 = $office_addr[0];
		$office_addr2 = $office_addr[1];

		$name = $row->name;
		$email = $row->email;
		if (strlen($row->mobile)>0) $mobile = $row->mobile;
		else if (strlen($row->home_tel)>0) $mobile = $row->home_tel;
		else if (strlen($row->office_tel)>0) $mobile = $row->office_tel;
		$mobile=explode("-",replace_tel(check_num($mobile)));
		$home_tel=explode("-",replace_tel(check_num($row->home_tel)));

		$group_code=$row->group_code;
		mysql_free_result($result);
		if(strlen($group_code)>0 && $group_code!=NULL) {
			$sql = "SELECT * FROM tblmembergroup WHERE group_code='".$group_code."' AND MID(group_code,1,1)!='M' ";
			$result=mysql_query($sql);
			if($row=mysql_fetch_object($result)){
				$group_code = $row->group_code;
				$org_group_name=$row->group_name;  //그룹정보로 인해 추가
				$group_name=$row->group_name;
				$group_type=substr($row->group_code,0,2);
				$group_usemoney=$row->group_usemoney;
				$group_addmoney=$row->group_addmoney;
				$group_payment=$row->group_payment;
				if($group_payment=="B") $group_name.=" (현금결제시)";
				else if($group_payment=="C") $group_name.=" (카드결제시)";
			}
			mysql_free_result($result);
		}
	} else {
		$_ShopInfo->setMemid("");
	}
}

$sql = "SELECT privercy FROM tbldesign ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$privercy_exp = @explode("=", $row->privercy);
	$privercybody=$privercy_exp[1];
}
mysql_free_result($result);

if(strlen($privercybody)==0) {
	$buffer="";
	$fp=fopen($Dir.AdminDir."privercy2.txt","r");
	if($fp) {
		while (!feof($fp)) {
			$buffer.= fgets($fp, 1024);
		}
	}
	fclose($fp);
	$privercybody=$buffer;
}

$pattern=array("(\[SHOP\])","(\[NAME\])","(\[EMAIL\])","(\[TEL\])");
$replace=array($_data->shopname,$_data->privercyname,"<a href=\"mailto:".$_data->privercyemail."\">".$_data->privercyemail."</a>",$_data->info_tel);
$privercybody = preg_replace($pattern,$replace,$privercybody);
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 주문서 작성</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function SameCheck(checked) {
	if(checked==true) {
		document.form1.receiver_name.value=document.form1.sender_name.value;
		document.form1.receiver_tel11.value="<?=$home_tel[0]?>";
		document.form1.receiver_tel12.value="<?=$home_tel[1]?>";
		document.form1.receiver_tel13.value="<?=$home_tel[2]?>";
		document.form1.receiver_tel21.value=document.form1.sender_hp1.value;
		document.form1.receiver_tel22.value=document.form1.sender_hp2.value;
		document.form1.receiver_tel23.value=document.form1.sender_hp3.value;
	} else {
		document.form1.receiver_name.value="";
		document.form1.receiver_tel11.value="";
		document.form1.receiver_tel12.value="";
		document.form1.receiver_tel13.value="";
		document.form1.receiver_tel21.value="";
		document.form1.receiver_tel22.value="";
		document.form1.receiver_tel23.value="";
	}
}
<?if(strlen($_ShopInfo->getMemid())>0){?>
function addrchoice() {
	if(document.form1.addrtype[0].checked==true) {
		document.form1.rpost1.value="<?=$home_post1?>";
		document.form1.rpost2.value="<?=$home_post2?>";
		document.form1.raddr1.value="<?=$home_addr1?>";
		document.form1.raddr2.value="<?=$home_addr2?>";
	} else if(document.form1.addrtype[1].checked==true) {
		document.form1.rpost1.value="<?=$office_post1?>";
		document.form1.rpost2.value="<?=$office_post2?>";
		document.form1.raddr1.value="<?=$office_addr1?>";
		document.form1.raddr2.value="<?=$office_addr2?>";
	} else if(document.form1.addrtype[2].checked==true) {
		window.open("<?=$Dir.FrontDir?>addrbygone.php","addrbygone","width=100,height=100,toolbar=no,menubar=no,scrollbars=yes,status=no");
	}
}
<?}?>
function get_post() {
	window.open("<?=$Dir.FrontDir?>addr_search.php?form=form1&post=rpost&addr=raddr1&gbn=2","f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");
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
if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/pester_title.gif")) {
	echo "<td><img src=\"".$Dir.DataDir."design/pester_title.gif\" border=\"0\" alt=\"조르기 요청\"></td>\n";
} else {
	echo "<td>\n";
	echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
	echo "<TR>\n";
	echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/pester_title_head.gif></TD>\n";
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
	<?
		include ($Dir.TempletDir."order/order".$_data->design_order.".php");
	?>
	</td>
</tr>
<tr>
	<td align=center>
	<div id="paybuttonlayer" name="paybuttonlayer" style="display:block;">
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td align=center><A HREF="javascript:CheckForm()" onmouseover="window.status='결제';return true;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/btn_pester.gif" border=0></A>&nbsp;&nbsp;&nbsp;&nbsp;<A HREF="javascript:ordercancel('cancel')" onmouseover="window.status='취소';return true;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/btn_cancel.gif" border=0></A></td>
	</tr>
	</table>
	</div>
	</td>
</tr>
</table>

<?
/*
if($sumprice<$_data->bank_miniprice) {
	echo "<script>alert('주문 가능한 최소 금액은 ".number_format($_data->bank_miniprice)."원 입니다.');location.href='".$Dir.FrontDir.$returnUrl."';</script>";
	exit;
} else if($sumprice<=0) {
	echo "<script>alert('상품 총 가격이 0원일 경우 상품 주문이 되지 않습니다.');location.href='".$Dir.FrontDir.$returnUrl."';</script>";
	exit;
}
*/

if(strlen($_ShopInfo->getMemid())>0) echo "<script>document.form1.addrtype[0].checked=true;addrchoice();</script>";
?>
<input type=hidden name=process value="N">
<input type=hidden name=paymethod>
<input type=hidden name=pay_data1>
<input type=hidden name=pay_data2>
<input type=hidden name=sender_resno>
<input type=hidden name=sender_tel>
<input type=hidden name=receiver_tel1>
<input type=hidden name=receiver_tel2>
<input type=hidden name=receiver_addr>
<input type=hidden name=order_msg>
<input type=hidden name=pester_tel>
<input type=hidden name=ordertype value="<?=$ordertype?>">
<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {?>
<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<?}?>
</form>


<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	<? if(strlen($_ShopInfo->getMemid())==0) { ?>
	if(document.form1.dongi[0].checked!=true) {
		alert("개인정보보호정책에 동의하셔야 비회원 주문이 가능합니다.");
		document.form1.dongi[0].focus();
		return;
	}
	if(document.form1.sender_name.type=="text") {
		if(document.form1.sender_name.value.length==0) {
			alert("주문자 성함을 입력하세요.");
			document.form1.sender_name.focus();
			return;
		}
		if(!chkNoChar(document.form1.sender_name.value)) {
			alert("주문자 성함에 \\(역슬래쉬) ,  '(작은따옴표) , \"(큰따옴표)는 입력하실 수 없습니다.");
			document.form1.sender_name.focus();
			return;
		}
	}
	<? } ?>
	if(document.form1.sender_hp1.value.length==0) {
		alert("주문자 휴대폰번호를 입력하세요.");
		document.form1.sender_hp1.focus();
		return;
	}
	if(document.form1.sender_hp2.value.length==0) {
		alert("주문자 휴대폰번호를 입력하세요.");
		document.form1.sender_hp2.focus();
		return;
	}
	if(document.form1.sender_hp3.value.length==0) {
		alert("주문자 휴대폰번호를 입력하세요.");
		document.form1.sender_hp3.focus();
		return;
	}
	if(!IsNumeric(document.form1.sender_hp1.value)) {
		alert("주문자 휴대폰번호 입력은 숫자만 입력하세요.");
		document.form1.sender_hp1.focus();
		return;
	}
	if(!IsNumeric(document.form1.sender_hp2.value)) {
		alert("주문자 휴대폰번호 입력은 숫자만 입력하세요.");
		document.form2.sender_hp2.focus();
		return;
	}
	if(!IsNumeric(document.form1.sender_hp3.value)) {
		alert("주문자 휴대폰번호 입력은 숫자만 입력하세요.");
		document.form3.sender_hp3.focus();
		return;
	}
	document.form1.sender_tel.value=document.form1.sender_hp1.value+"-"+document.form1.sender_hp2.value+"-"+document.form1.sender_hp3.value;

	if(document.form1.sender_email.value.length==0) {
		alert("주문자 이메일을 입력하세요.");
		document.form1.sender_email.focus();
		return;
	}
	if(document.form1.sender_email.value.length>0) {
		if(!IsMailCheck(document.form1.sender_email.value)) {
			alert("주문자 이메일 형식이 잘못되었습니다.");
			document.form1.sender_email.focus();
			return;
		}
	}

	if(document.form1.receiver_name.value.length==0) {
		alert("받는분 성함을 입력하세요.");
		document.form1.receiver_name.focus();
		return;
	}
	if(!chkNoChar(document.form1.receiver_name.value)) {
		alert("받는분 성함에 \\(역슬래쉬) ,  '(작은따옴표) , \"(큰따옴표)는 입력하실 수 없습니다.");
		document.form1.receiver_name.focus();
		return;
	}
<?if($ordertype  == "present"){?>
	if(document.form1.receiver_email.value.length==0) {
		alert("받는분 이메일을 입력하세요.");
		document.form1.receiver_email.focus();
		return;
	}
	if(document.form1.receiver_email.value.length > 0) {
		if(!IsMailCheck(document.form1.receiver_email.value)) {
			alert("받는분 이메일 형식이 잘못되었습니다.");
			document.form1.receiver_email.focus();
			return;
		}
	}
<?}?>
	if(document.form1.receiver_tel11.value.length==0) {
		alert("받는분 전화번호를 입력하세요.");
		document.form1.receiver_tel11.focus();
		return;
	}
	if(document.form1.receiver_tel12.value.length==0) {
		alert("받는분 전화번호를 입력하세요.");
		document.form1.receiver_tel12.focus();
		return;
	}
	if(document.form1.receiver_tel13.value.length==0) {
		alert("받는분 전화번호를 입력하세요.");
		document.form1.receiver_tel13.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel11.value)) {
		alert("받는분 전화번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_tel11.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel12.value)) {
		alert("받는분 전화번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_tel12.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel13.value)) {
		alert("받는분 전화번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_tel13.focus();
		return;
	}
	document.form1.receiver_tel1.value=document.form1.receiver_tel11.value+"-"+document.form1.receiver_tel12.value+"-"+document.form1.receiver_tel13.value;

	if(document.form1.receiver_tel21.value.length==0) {
		alert("받는분 비상전화번호를 입력하세요.");
		document.form1.receiver_tel21.focus();
		return;
	}
	if(document.form1.receiver_tel22.value.length==0) {
		alert("받는분 비상전화번호를 입력하세요.");
		document.form1.receiver_tel22.focus();
		return;
	}
	if(document.form1.receiver_tel23.value.length==0) {
		alert("받는분 비상전화번호를 입력하세요.");
		document.form1.receiver_tel23.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel21.value)) {
		alert("받는분 비상전화번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_tel21.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel22.value)) {
		alert("받는분 비상전화번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_tel22.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel23.value)) {
		alert("받는분 비상전화번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_tel23.focus();
		return;
	}
	document.form1.receiver_tel2.value=document.form1.receiver_tel21.value+"-"+document.form1.receiver_tel22.value+"-"+document.form1.receiver_tel23.value;

	if(document.form1.rpost1.value.length==0 || document.form1.rpost2.value.length==0) {
		alert("우편번호를 선택하세요.");
		get_post();
		return;
	}
	if(document.form1.raddr1.value.length==0) {
		alert("주소를 입력하세요.");
		document.form1.raddr1.focus();
		return;
	}
	if(document.form1.raddr2.value.length==0) {
		alert("상세주소를 입력하세요.");
		document.form1.raddr2.focus();
		return;
	}
	if(!chkNoChar(document.form1.raddr2.value)) {
		alert("상세주소에 \\(역슬래쉬) ,  '(작은따옴표) , \"(큰따옴표)는 입력하실 수 없습니다.");
		document.form1.raddr2.focus();
		return;
	}

	document.form1.receiver_addr.value = "우편번호 : " + document.form1.rpost1.value + "-" + document.form1.rpost2.value + "\n주소 : " + document.form1.raddr1.value + "  " + document.form1.raddr2.value;

	message_len = document.form1.order_prmsg.value.length;
	message_end = document.form1.order_prmsg.value.charCodeAt(message_len-1);
	if (message_len>0 && (message_end==39 || message_end==34 || message_end==92) ) {
		document.form1.order_prmsg.value += " ";
	}

	if(document.form1.pester_name.value.length==0) {
		alert("조르기 상대의 성함을 입력하세요.");
		document.form1.pester_name.focus();
		return;
	}
	if(!chkNoChar(document.form1.pester_name.value)) {
		alert("조르기 상대의 성함에 \\(역슬래쉬) ,  '(작은따옴표) , \"(큰따옴표)는 입력하실 수 없습니다.");
		document.form1.pester_name.focus();
		return;
	}

	if(document.form1.pester_tel1.value.length==0) {
		alert("조르기 상대의 전화번호를 입력하세요.");
		document.form1.pester_tel1.focus();
		return;
	}
	if(document.form1.pester_tel2.value.length==0) {
		alert("조르기 상대의 전화번호를 입력하세요.");
		document.form1.pester_tel2.focus();
		return;
	}
	if(document.form1.pester_tel3.value.length==0) {
		alert("조르기 상대의 전화번호를 입력하세요.");
		document.form1.pester_tel3.focus();
		return;
	}
	if(!IsNumeric(document.form1.pester_tel1.value)) {
		alert("조르기 상대의 전화번호 입력은 숫자만 입력하세요.");
		document.form1.pester_tel1.focus();
		return;
	}
	if(!IsNumeric(document.form1.pester_tel2.value)) {
		alert("조르기 상대의 전화번호 입력은 숫자만 입력하세요.");
		document.form2.pester_tel2.focus();
		return;
	}
	if(!IsNumeric(document.form1.pester_tel3.value)) {
		alert("조르기 상대의 전화번호 입력은 숫자만 입력하세요.");
		document.form3.pester_tel3.focus();
		return;
	}
	document.form1.pester_tel.value=document.form1.pester_tel1.value+"-"+document.form1.pester_tel2.value+"-"+document.form1.pester_tel3.value;

	if(document.form1.pester_email.value.length==0) {
		alert("조르기 상대의 이메일을 입력하세요.");
		document.form1.pester_email.focus();
		return;
	}
	if(document.form1.pester_email.value.length>0) {
		if(!IsMailCheck(document.form1.pester_email.value)) {
			alert("조르기 상대의 이메일 형식이 잘못되었습니다.");
			document.form1.pester_email.focus();
			return;
		}
	}

	if(document.form1.pester_smstxt.value.length==0) {
		alert("sms 전송메세지를 입력하세요.");
		document.form1.pester_smstxt.focus();
		return;
	}

	if(document.form1.pester_emailtxt.value.length==0) {
		alert("email 전송메세지를 입력하세요.");
		document.form1.pester_emailtxt.focus();
		return;
	}

	document.form1.submit();
}


function ordercancel(gbn) {
	if(gbn=="cancel" && document.form1.process.value=="N") {
		document.location.href='<?=$returnUrl?>';
	}
}
//-->
</SCRIPT>

<?=$onload?>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>