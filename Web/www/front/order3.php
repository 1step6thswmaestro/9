<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");
include_once($Dir."lib/ext/order_func.php");


if($_data->member_buygrant=="Y" && strlen($_ShopInfo->getMemid())==0) {	//회원전용일 경우 로긴페이지로...
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$socialshopping = "social";
$tblbasket ="tblbasket3";

$ordertype=$_GET["ordertype"];

//장바구니 인증키 확인
if(strlen($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
	$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
}

//판매종료상품삭제
$chksql = "SELECT pcode FROM tblbasket3 AS a, tblproduct_social AS b ";
$chksql.= "WHERE a.productcode=b.pcode ";
$chksql.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
$chksql.= "AND (sell_startdate > ".time()." OR sell_enddate < ".time().") ";
$chkresult = mysql_query($chksql,get_db_conn());
$i=0;
$delPcode = "";
while($row=@mysql_fetch_object($chkresult)) {
	$delPcode .= ($i>0)? ",":"";
	$delPcode .= "'".$row->pcode."'";
	$i++;
}
if($delPcode){
	$sql ="DELETE FROM tblbasket3 WHERE tempkey='".$_ShopInfo->getTempkey()."' AND productcode in (".$delPcode.")";
	mysql_query($sql,get_db_conn());
}

//sns 홍보 본인체크
if(strlen($_ShopInfo->getMemid()) > 0){
	$sql ="UPDATE tblbasket3 SET sell_memid ='' WHERE tempkey='".$_ShopInfo->getTempkey()."' AND sell_memid='".$_ShopInfo->getMemid()."'";
	mysql_query($sql,get_db_conn());
}

$basketsql2 = "SELECT a.productcode,a.package_idx,a.quantity,c.package_list,c.package_title,c.package_price ";
$basketsql2.= "FROM tblbasket3 AS a, tblproduct AS b, tblproductpackage AS c ";
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
$sql.= "FROM tblbasket3 a, tblproduct b ";
$sql.= "LEFT OUTER JOIN tblassembleproduct c ON b.productcode=c.productcode ";
$sql.= "WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
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
		//단일회원 중복 구매 허용 여부
		$sql2 = "SELECT member_check,sellcount_member FROM tblproduct_social WHERE pcode='".$row->productcode."'";
		$result2=mysql_query($sql2,get_db_conn());
		if($row2=mysql_fetch_object($result2)) {
			if($row->member_check == "N") {
				$sql3 =  "SELECT ordercode from tblorderproduct where productcode = '".$row->productcode."' and deli_gbn != 'C'";
				$res = mysql_query($sql,get_db_conn());
				$snsnum  = mysql_num_rows($res);

				if($snsnum > 0){
					$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 중복 구매가 되지 않습니다.\\n";
				}
			}else{
				if($row2->sellcount_member > 0){
					$sql3 =  "SELECT ordercode from tblorderproduct where productcode = '".$row->productcode."' and deli_gbn != 'C'";
					$res = mysql_query($sql,get_db_conn());
					$snsnum  = mysql_num_rows($res);

					if($snsnum >= $row2->sellcount_member){
						$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 ".$row2->sellcount_member."개 까지 중복 구매가 가능합니다.\\n";
					}
				}
			}
		}
		//////////////////////////////////////////////////////////////////////////////
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
			$basketsql = "SELECT productcode,assemble_list,quantity,assemble_idx FROM tblbasket3 WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
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
			$sql = "SELECT opt1_idx, opt2_idx, quantity FROM tblbasket3 ";
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
	echo "<html></head><body onload=\"alert('".$errmsg."');location.href='".$Dir.FrontDir."productdetail.php?".$row->productname."';\"></body></html>";
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



#### PG 데이타 세팅 ####
$_ShopInfo->getPgdata();
########################


//////  결제 수단 선택 start  ////////////////////////////////////////////////

// 결제 현금결제 전용 포함 체크
$bankonlyCHK = "N";
foreach ( $basketItems['vender'] as $venderKey => $venderValue ) {
	foreach ( $venderValue['products'] as $productsKey=> $productsValue ){
		if( $productsValue['bankonly'] ) {
			$bankonlyCHK = "Y";
		}
	}
}

$escrow_info = GetEscrowType($_data->escrow_info);

$payType = "";

//무통장
/*if( preg_match("/^(Y|N)$/", $_data->payment_type) && $escrow_info["onlycard"]!="Y" ) {
	$payType .= "<input type='radio' onclick=\"change_paymethod(1);\" name='sel_paymethod' value='B' id=\"sel_paymethod1\"><label for=\"sel_paymethod1\" style=\"cursor:pointer;\">무통장 입금</label>&nbsp;&nbsp;";
}*/

//2:신용카드: 현금결제시 비활성
if(preg_match("/^(Y|C)$/", $_data->payment_type) && strlen($_data->card_id)>0 AND $bankonlyCHK == "N" ) {
	$payType .= "<input type='radio' onclick=\"change_paymethod(2);\" name='sel_paymethod' value='C' id=\"sel_paymethod2\"><label for=\"sel_paymethod2\" style=\"cursor:pointer;\">신용카드</label>&nbsp;&nbsp;";
}

//2:실시간계좌이체
if($escrow_info["onlycard"]!="Y" ) {
	if(strlen($_data->trans_id)>0) {
		$payType .= "<input type='radio' onclick=\"change_paymethod(3);\" name='sel_paymethod' value='V' id=\"sel_paymethod3\"><label for=\"sel_paymethod3\" style=\"cursor:pointer;\">실시간계좌이체</label>&nbsp;&nbsp;";
	}
}

//3:가상계좌
/*if($escrow_info["onlycard"]!="Y" ) {
	if(strlen($_data->virtual_id)>0) {
		$payType .= "<input type='radio' onclick=\"change_paymethod(4);\" name='sel_paymethod' value='O' id=\"sel_paymethod4\"><label for=\"sel_paymethod4\" style=\"cursor:pointer;\">가상계좌</label>&nbsp;&nbsp;";
	}
}

//4:에스크로
if(($escrow_info["escrowcash"]=="A" || $escrow_info["escrowcash"]=="Y") && strlen($_data->escrow_id)>0) {
	$pgid_info="";
	$pg_type="";
	$pgid_info=GetEscrowType($_data->escrow_id);
	$pg_type=trim($pgid_info["PG"]);

	if(preg_match("/^(A|B|C|D|E)$/",$pg_type)) {
		//KCP/데이콤/올더게이트/이니시스/나이스페이 가상계좌 에스크로 코딩
		$payType .= "<input type='radio' onclick=\"change_paymethod(5);\" name='sel_paymethod' value='Q' id=\"sel_paymethod5\"><label for=\"sel_paymethod5\" style=\"cursor:pointer;\">".($pg_type=="E"?"에스크로결제(신용카드,실시간이체,가상계좌)":"결제대금예치제(에스크로)")."</label>&nbsp;&nbsp;";
	}
}

//5:핸드폰 : 현금결제시 비활성
if(strlen($_data->mobile_id)>0 AND $bankonlyCHK == "N" ) {
	$payType .= "<input type='radio' onclick=\"change_paymethod(6);\" name='sel_paymethod' value='M' id=\"sel_paymethod6\"><label for=\"sel_paymethod6\" style=\"cursor:pointer;\">핸드폰 결제</label>";
}

//현금결제 전용 상품 포함시 메세지
if( $bankonlyCHK == "Y" ) {
	$payType .= "&nbsp;&nbsp;&nbsp;<font color='#FF0000'>(*주문 상품에 [현금결제] 상품이 포함되어 신용카드결제가 불가능합니다.)</font>";
}
*/
//////  결제 수단 선택 end  ////////////////////////////////////////////////


$basketItems = getBasketByArray($tblbasket);
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 주문서 작성</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>js/jquery-1.8.3.min.js"></script>
<script>
	var $j = jQuery.noConflict();
</script>

<?include($Dir."lib/style.php")?>


<script>
	var deli_basefee	= parseInt(<?=$_data->deli_basefee?>); //쇼핑몰 설정 배송료
	var deli_miniprice	= parseInt(<?=$_data->deli_miniprice?>); //쇼핑몰 설정 배송무료 최소 상품가
	var deli_price = parseInt(<?=$basketItems['deli_price']?>);
	var excp_group_discount = parseInt(<?=$basketItems['excp_group_discount']?>);
	var mingiftprice = parseInt(<?=$giftprice?>);

	var setprice;

	//등급할인 정보
	var groupDiscMoney = parseInt("<?=$basketItems['groupMemberSale']['addMoney']?>"); // 적립/할인금액 또는 %
	var groupDiscUseMoney = parseInt("<?=$basketItems['groupMemberSale']['useMoney']?>"); // 기준 금액
	var groupDiscPayTypeCode = "<?=$basketItems['groupMemberSale']['payTypeCode']?>"; // 기준 결제 방법
	var groupCode = "<?=$basketItems['groupMemberSale']['groupCode']?>"; // 그룹코드



	if(isNaN(mingiftprice) || mingiftprice <1) mingiftprice = 0;

	$j(document).ready(function() {

		// 적립금
		$j("#usereserve").keyup(function(){
			var possibleMileage = parseInt($j("#okreserve").val());//해당 주문에서 사용가능한 적립금
			var defaultprice	= parseInt($j("#sumprice").val());	//기본 총 결제금액

			repstr = $j(this).val().replace(/[^0-9]/g,'');
			userMileage = parseInt(repstr);
			if(isNaN(userMileage)) userMileage = 0;
			$j(this).val(userMileage.toString());

			if(userMileage > possibleMileage){
				alert("해당 주문의 적립금 적용 가능한 금액은 "+possibleMileage + "원 입니다.");
				$j("#usereserve").val(possibleMileage.toString());
			}/*else if(userMileage > setprice){
				alert("해당 주문의 적립금 적용 가능한 금액은 "+setprice + "원 입니다");
				$j("#usereserve").val(setprice.toString());
			}*/else{

			}
			//resetCoupon();

			solvPrice();
		});

		$j("input[name=saddr2],input[name=raddr2]").focus(function(){
			if($j(this).val() == '나머지 주소') $j(this).val('');
		});

		$j("input[name=saddr2],input[name=raddr2]").blur(function(){
			if($j.trim($j(this).val()).length < 1) $j(this).val('나머지 주소');
		});

		solvPrice();
	});

	function reserdeli(total_price){
		if(total_price > 0 && deli_miniprice > total_price) {
			alert("최종 결제금액이 " + number_format(deli_miniprice) + " 원 이하인 경우 기본 배송료 " +number_format(deli_basefee)+ "원이 추가됩니다");
			$j("#disp_last_price").text(number_format(total_price+deli_basefee));	// 최종결제금액 UI 표시
		}
	}

	function resetCoupon(){
		var coupon = parseInt($j("#coupon_price").val()); //쿠폰 할인액
		if(!isNaN(coupon) && coupon > 0){
			alert('쿠폰 사용 설정이 초기화 됩니다.');
		}
		$j('#couponlist').val('');
		$j('#dcpricelist').val('');
		$j('#couponproduct').val('');
		$j('#coupon_price').val('0');
		$j('#bank_only').val('N');
		$j("#possible_gift_price_used").val("Y");
		$j("#possible_group_dis_used").val("Y");

		solvPrice();
	}



	// 재 계산기 ******************************************************************************************
	function solvPrice(){

		var possibleMileage = parseInt($j("#okreserve").val());//해당 주문에서 사용가능한 적립금
		var userMileage = parseInt($j("#usereserve").val()); // 사용한 적립금
		var gift = parseInt($j("#possible_gift_price").val()); // 사은품 지급가능 구매금액
		var coupon = parseInt($j("#coupon_price").val()); //쿠폰 사용한 값
		var defaultprice = parseInt($j("#sumprice").val()); //총 결제금액
		var deli_price = parseInt($j("#deliprice").val()); // 배송비

		if(isNaN(possibleMileage)) possibleMileage = 0;
		if(isNaN(userMileage)) userMileage = 0;
		if(isNaN(gift)) gift = 0;
		if(isNaN(coupon)) coupon = 0;
		if(isNaN(defaultprice)) defaultprice = 0;
		if(isNaN(deli_price)) deli_price = 0;
		var gdiscount = 0;

		setprice = parseInt(defaultprice-userMileage-coupon); // 결제 금액

		// 적립금 사용
		if( setprice < 0 ) {
			userMileage = parseInt( userMileage - ( 0 - setprice ) );
			alert("적립금사용은 "+userMileage+"까지 사용가능합니다.\n\n* 쿠폰 사용 및 할인정책에 의하여 변경 또는 원가 변동의 의한것입니다.");
			setprice = 0;
		}
		$j("#usereserve").val(userMileage);

		//등급 할인
		var gdiscount = 0;
		var ispaymentcheck=false;
		for(i=0;i<document.form1.sel_paymethod.length;i++) {
			if(document.form1.sel_paymethod[i].checked==true) {
				document.form1.paymethod.value=document.form1.sel_paymethod[i].value;
				ispaymentcheck=true;
				break;
			}
		}
		if( isNaN(groupCode) && ispaymentcheck==true && $j("#possible_group_dis_used").val() == "Y" && setprice >= groupDiscMoney && setprice >= groupDiscUseMoney ) {
			if ( groupCode == 'SW' ) {
				gdiscount=groupDiscMoney;
			} else {
				gdiscount= Math.floor((setprice*(groupDiscMoney/100))/100)*100;
			}
			// 결제 방식에 따른 처리
			// "B"=>"현금","C"=>"카드","N"=>"현금/카드"
			if( groupDiscPayTypeCode != "N" ) {
				var paymethodList = ( groupDiscPayTypeCode == "B" ) ? "B|V|O" : "C|M";
				var paymethod = $j("#paymethod").val();
				if( paymethodList.indexOf(paymethod) < 0 ) {
					gdiscount = 0;
				}
			}
		}

		// 등급할인 적용 안됨 쿠폰 사용 메세지
		if ($j("#possible_group_dis_used").val() == "N") {
			$j("#couponEventMsg").html("<br><font color='blue'>사용하신 쿠폰 중 등급할인 혜택을 받을 수 없는 쿠폰이 포함되었습니다.</font>");
		} else {
			$j("#couponEventMsg").html("");
		}

		setprice -= gdiscount;
		gdiscount = 0-gdiscount;
		$j("#groupdiscount").val(gdiscount);



		// 사은품 적용
		if(setprice < gift) gift = setprice; // 사은품 사용가능 금액
		giftchoices(gift);

		// 총결제금액
		var total_price =  parseInt( setprice + deli_price );

		// 디스플레이 ( UI 표시 )
		$j("#disp_coupon").text(number_format(0-coupon)); // 할인쿠폰 사용금액
		$j("#disp_reserve").text(number_format(0-userMileage)); // 적립금 사용액
		$j("#disp_groupdiscount").text(number_format(gdiscount));	// 등급할인

		$j("#disp_deliprice").text(number_format(deli_price));	// 배송금액

		$j("#disp_last_price").text(number_format(total_price));	// 최종결제금액

	}
	// 재 계산기 끝 **************************************************************************************


	// 창크기
	var trident = navigator.userAgent.match(/Trident\/(\d)/i);
	if(trident == null){
		var width = isNaN(window.innerWidth) ? window.clientWidth : window.innerWidth;
		var height = isNaN(window.innerHeight) ? window.clientHeight : window.innerHeight;
	} else {
		/*
		if( trident == "Trident/7,7" ) { // IE 11
			var width = 1200;
			var height = 1000;
		} else {
		*/
			var width = $j(document).width();
			var height = $j(window).height();
		//}
	}


	function windowSize() {
		$j("#PAY_PROCESS_IFRAME").css("width",width);
		$j("#PAY_PROCESS_IFRAME").css("height",height);
		document.getElementById("PAY_PROCESS_IFRAME").style.display = "block";
		document.getElementById("orderPaySel").disabled = "disabled";
		document.getElementById("PAY_PROCESS_IFRAME").focus();
	}


	$j(document).ready(function(){
		$j(window).bind("scroll", function(){
			$j("#PAY_PROCESS_IFRAME").css("top",$j(document).scrollTop());
		});
	});


	$j( window ).resize(function() {

		if(trident == null){
			var width2 = $j(window).width()+100;
			var height2 = isNaN(window.innerHeight) ? window.clientHeight : window.innerHeight;
		} else {
			//if( trident == "Trident/7,7" ) { // IE 11
			//	var width2 = 1200;
			//	var height2 = 1000;
			//} else {
				var width2 = $j(window).width()+100;
				var height2 = $j(window).height();
			//}
		}

		$j("#PAY_PROCESS_IFRAME").css("width",width2);
		$j("#PAY_PROCESS_IFRAME").css("height",height2);
	});
</script>


<SCRIPT LANGUAGE="JavaScript">
<!--
function change_message(gbn) {
	if(gbn==1) {
		document.all["msg_idx2"].style.display="none";
		document.all["msg_idx1"].style.display="";
		document.form1.msg_type.value=gbn;
	} else if(gbn==2) {
		document.all["msg_idx2"].style.display="";
		document.all["msg_idx1"].style.display="none";
		document.form1.msg_type.value=gbn;
	}
}
/*
function SameCheck(checked) {
	if(checked==true) {
		document.form1.receiver_name.value=document.form1.sender_name.value;
		document.form1.receiver_tel11.value="<?=$home_tel[0]?>";
		document.form1.receiver_tel12.value="<?=$home_tel[1]?>";
		document.form1.receiver_tel13.value="<?=$home_tel[2]?>";
		document.form1.receiver_tel21.value=document.form1.sender_tel1.value;
		document.form1.receiver_tel22.value=document.form1.sender_tel2.value;
		document.form1.receiver_tel23.value=document.form1.sender_tel3.value;
	} else {
		document.form1.receiver_name.value="";
		document.form1.receiver_tel11.value="";
		document.form1.receiver_tel12.value="";
		document.form1.receiver_tel13.value="";
		document.form1.receiver_tel21.value="";
		document.form1.receiver_tel22.value="";
		document.form1.receiver_tel23.value="";
	}
}*/

//주문자 정보와 동일함
function SameCheck(checked) {
	if(checked==true) {
		document.form1.receiver_name.value=document.form1.sender_name.value;
		document.form1.receiver_tel21.value=document.form1.sender_hp1.value;
		document.form1.receiver_tel22.value=document.form1.sender_hp2.value;
		document.form1.receiver_tel23.value=document.form1.sender_hp3.value;
		document.form1.receiver_tel11.value=document.form1.sender_tel1.value;
		document.form1.receiver_tel12.value=document.form1.sender_tel2.value;
		document.form1.receiver_tel13.value=document.form1.sender_tel3.value;
		document.form1.email.value			=document.form1.sender_email.value;

		document.form1.rpost1.value=document.form1.spost1.value;
		document.form1.rpost2.value=document.form1.spost2.value;
		document.form1.raddr1.value=document.form1.saddr1.value;
		document.form1.raddr2.value=document.form1.saddr2.value;
	} else {
		document.form1.receiver_name.value="";
		document.form1.receiver_tel11.value="";
		document.form1.receiver_tel12.value="";
		document.form1.receiver_tel13.value="";
		document.form1.receiver_tel21.value="";
		document.form1.receiver_tel22.value="";
		document.form1.receiver_tel23.value="";
		document.form1.email.value='';

		document.form1.rpost1.value='';
		document.form1.rpost2.value='';
		document.form1.raddr1.value='';
		document.form1.raddr2.value='';
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
function reserve_check(temp) {
	temp=parseInt(temp);
	if(isNaN(document.form1.usereserve.value)) {
		document.form1.usereserve.value=0;
		document.form1.okreserve.value=temp;
		document.form1.usereserve.focus();
		alert('숫자만 입력하셔야 합니다.');
		return;
	}
	if(parseInt(document.form1.usereserve.value)>temp) {
		document.form1.usereserve.value=0;
		document.form1.okreserve.value=temp;
		document.form1.usereserve.focus();
		alert('사용가능 적립금 보다 적거나 똑같이 입력하셔야 합니다.');
		return;
	}
	document.form1.okreserve.value=parseInt(temp-document.form1.usereserve.value);
	document.form1.usereserve.value=temp-document.form1.okreserve.value;
}
<?}?>
function get_post() {
	window.open("<?=$Dir.FrontDir?>addr_search.php?form=form1&post=rpost&addr=raddr1&gbn=2","f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");
}

<?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y"){?>
function coupon_check(){
	window.open("about:blank","couponpopup","width=650,height=650,toolbar=no,menubar=no,scrollbars=yes,status=no");
	document.couponform.submit();
}
<?}?>

function orderpaypop() {
	if(typeof(document.form1.usereserve)!="undefined") {
		document.orderpayform.usereserve.value=document.form1.usereserve.value;
	}
	if(typeof(document.form1.coupon_code)!="undefined") {
		document.orderpayform.coupon_code.value=document.form1.coupon_code.value;
	}
	document.orderpayform.email.value=document.form1.sender_email.value;
	document.orderpayform.address.value=document.form1.raddr1.value;
	document.orderpayform.mobile_num1.value=document.form1.sender_tel1.value;
	document.orderpayform.mobile_num.value=document.form1.sender_tel1.value+"-"+document.form1.sender_tel2.value+"-"+document.form1.sender_tel3.value

	var winpaypop=window.open("about:blank","orderpaypop","width=620,height=550,scrollbars=yes");
	winpaypop.focus();

<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {?>
	document.orderpayform.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>orderpay3.php';
<?}?>

	document.orderpayform.submit();
}

function ordercancel(gbn) {
	if(gbn=="cancel" && document.form1.process.value=="N") {
		document.location.href="gonggu_main.php";
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
<form name=form1 action="<?=sprintf($Dir.FrontDir."%s.php", ((substr($ordertype,0,6)== "pester")? "pestersend":(($socialshopping == "social")? "ordersend3":"ordersend")) )?>" method=post>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
<?
if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/order_title.gif")) {
	echo "<td><img src=\"".$Dir.DataDir."design/order_title.gif\" border=\"0\" alt=\"주문서작성\"></td>\n";
} else {
	echo "<td>\n";
	echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
	echo "<TR>\n";
	echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/order_title_head.gif></TD>\n";
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
	<? include ($Dir.TempletDir."order/order".$_data->design_order.".php"); ?>
	</td>
</tr>
<tr>
	<td align=center>
	<div id="paybuttonlayer" name="paybuttonlayer" style="display:block;">
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td align=center><A HREF="javascript:CheckForm()" onMouseOver="window.status='결제';return true;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/btn_payment.gif" border=0></A>&nbsp;&nbsp;&nbsp;&nbsp;<A HREF="javascript:ordercancel('cancel')" onMouseOver="window.status='취소';return true;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/btn_cancel.gif" border=0></A></td>
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

<?
if($basketItems['sumprice']<$_data->bank_miniprice) {
	echo "<script>alert('주문 가능한 최소 금액은 ".number_format($_data->bank_miniprice)."원 입니다.');location.href='".$Dir.FrontDir."basket.php';</script>";
	exit;
} else if($basketItems['sumprice']<=0) {
	echo "<script>alert('상품 총 가격이 0원일 경우 상품 주문이 되지 않습니다.');location.href='".$Dir.FrontDir."basket.php';</script>";
	exit;
}

if(strlen($_ShopInfo->getMemid())>0 && $ordertype !='present') echo "<script>document.form1.addrtype[0].checked=true;addrchoice();</script>";
?>
<!-- 총결제금액 원금 -->
<input type="hidden" name="sumprice" id="sumprice" value="<?=$basketItems['sumprice']?>" />

<!-- 쿠폰적용 값 ( 구분기호 : | )) -->
<!-- 사용쿠폰리스트 -->
<input type="hidden" name="couponlist" id="couponlist" value="" />
<!-- 사용쿠폰 할인액 리스트 -->
<input type="hidden" name="dcpricelist" id="dcpricelist" value="" />
<!-- 사용쿠폰 적립액 리스트 -->
<input type="hidden" name="drpricelist" id="drpricelist" value="" /><!-- -->
<!-- 사용쿠폰상품리스트 --><!--  (쿠폰코드_상품코드_옵션1idx_옵션2idx) -->
<input type="hidden" name="couponproduct" id="couponproduct" value="" />
<!-- 현금 사용시 가능한 쿠폰이 선택된 경우 --><!-- if (현금 사용시 가능한 쿠폰이 선택된 경우 ) Y else N -->
<input type="hidden" name="bank_only" id="couponBankOnly" value="N">
<!-- 배송비 -->
<input type='hidden' name='deliprice' id='deliprice' value='<?=$basketItems['deli_price']?>'>
<!-- 쿠폰적립총액 -->
<input type="hidden" name="coupon_reserve" id="coupon_reserve" value="0" />
<!-- 결제방식 -->
<input type="hidden" name="paymethod" id="paymethod" value="0" />
<!-- 적립금 적용 불가 상품 제외한 적용가능한 적립금 금액 , 사용 적립금이 okreserve 보다 작아야 함 -->
<input type="hidden" name="okreserve" id="okreserve" value="<?=$okreserve?>" />

<!-- 결제 타입(?) 선물하기 일경우 (?) -->
<input type=hidden name=ordertype value="<?=$ordertype?>">

<!-- 면세? 이건 어떻게 활용?? -->
<input type="hidden" name="tax_free" value="<?=$basketItems['tax_free']?>" />

<!-- 사은품 사용가능 금액 -->
<input type="hidden" name="possible_gift_price" id="possible_gift_price" value="<?=$basketItems['gift_price']?>" />
<!-- 사은품 사용가능 여부 (Y/N) -->
<input type="hidden" name="possible_gift_price_used" id="possible_gift_price_used" value="Y" />
<!-- 회원 등급 할인 혜택 여부 (Y/N) -->
<input type="hidden" name="possible_group_dis_used" id="possible_group_dis_used" value="Y" />


<!-- 주문메세지 타입 -->
<input type="hidden" name="msg_type" value="1" />
<!-- 지역별 추가 배송료..???? -->
<input type="hidden" name="addorder_msg" value="">

<!-- 쿠폰 할인 정보 -->
<input type="hidden" name="basketTempList" id="basketTempList" value="">

<!-- 회원그룹(추가)할인 -->
<input type="hidden" name="groupdiscount" id="groupdiscount" value="0">

<input type=hidden name=process value="N">
<!-- <input type=hidden name=paymethod> -->
<input type=hidden name=pay_data1>
<input type=hidden name=pay_data2>
<input type=hidden name=sender_resno>
<input type=hidden name=sender_tel>
<input type=hidden name=receiver_tel1>
<input type=hidden name=receiver_tel2>
<input type=hidden name=receiver_addr>
<input type=hidden name=order_msg>
<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {?>
<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<?}?>
<!-- <input type=hidden name=ordertype value="<?=$ordertype?>"> -->
</form>

<form name=couponform action="<?=$Dir.FrontDir?>coupon.php" method=post target=couponpopup>
<input type=hidden name=sumprice value="<?=$sumprice?>">
</form>

<form name=orderpayform method=post action="<?=$Dir.FrontDir?>orderpay3.php" target=orderpaypop>
<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {?>
<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<?}?>
<input type=hidden name=coupon_code>
<input type=hidden name=usereserve>
<input type=hidden name=email>
<input type=hidden name=mobile_num1>
<input type=hidden name=mobile_num>
<input type=hidden name=address>
</form>

<SCRIPT LANGUAGE="JavaScript">
<!--
// 주문하기
	// 주문하기
	function CheckForm() {

		paymethod=document.form1.paymethod.value.substring(0,1);
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

		<?if($ordertype == "p"){?>
			if(document.form1.in_email.value.length==0) {
				alert("이메일을 입력하세요.");
				document.form1.in_email.focus();
				return;
			}
			var email = document.form1.in_email.value;
			if(email.indexOf(",") >0){
				arEmail = email.split(",");
				for(i=0;i<arEmail.length;i++){
					if(!IsMailCheck(arEmail[i].trim())) {
						alert("이메일 형식이 맞지않습니다.\n\n확인하신 후 다시 입력하세요.");
						document.form1.in_email.focus(); return;
					}
				}
			}else{
				if(!IsMailCheck(email.trim())) {
					alert("이메일 형식이 맞지않습니다.\n\n확인하신 후 다시 입력하세요.");
					document.form1.in_email.focus(); return;
				}
			}
			if(document.form1.receiver_message.value.length==0) {
				alert("내용을 입력하세요.");
				document.form1.receiver_message.focus();
				return;
			}

		<?}?>




		<?
			if(substr($ordertype,0,6) == "pester"){
		?>

			document.form1.receiver_addr.value = document.form1.rpost1.value + "-" + document.form1.rpost2.value

			// 조르기 체크
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
		<?
			} else {
		?>



			//신규 결제수단 체크!!  ------ 20120430
			try {
				if (document.form1.sel_paymethod.length==null) {
					if(document.form1.sel_paymethod.checked==false) {
						alert("결제방법을 선택하세요.");
						document.form1.paymethod.value="";
						return;
					}
					document.form1.paymethod.value=document.form1.sel_paymethod.value;
				} else {
					var ispaymentcheck=false;
					for(i=0;i<document.form1.sel_paymethod.length;i++) {
						if(document.form1.sel_paymethod[i].checked==true) {
							document.form1.paymethod.value=document.form1.sel_paymethod[i].value;
							ispaymentcheck=true;
							break;
						}
					}
					if(ispaymentcheck==false) {
						alert("결제방법을 선택하세요.");
						document.form1.paymethod.value="";
						return;
					}
				}
			} catch(e) {
				return;
			}

			if(document.form1.paymethod.value=="B" && document.form1.sel_bankinfo.selectedIndex!=0) {
				document.form1.pay_data1.value=document.form1.sel_bankinfo.options[document.form1.sel_bankinfo.selectedIndex].value;
			} else if(document.form1.paymethod.value=="B" && document.form1.sel_bankinfo.selectedIndex<=0) {
				alert("입금계좌를 선택하세요.");
				document.form1.paymethod.value="";
				document.form1.sel_bankinfo.focus();
				return;
			}

			/* 결제수단체크 끝~~~~~~~~~~~~~~~~~~~~~*/

			<? if(strlen($_ShopInfo->getMemid())>0) { ?>

				if(document.form1.usereserve.value == '') {
					alert("적립금 입력란이 비었습니다.");
					document.form1.usereserve.value = 0;
					document.form1.usereserve.focus();
					return;
				}

				<? if($_data->reserve_maxuse>=0 && strlen($okreserve)>0 && $okreserve>0) { ?>
				if(document.form1.usereserve.value > <?=$okreserve?>) {
					alert("적립금 사용가능금액보다 큽니다.");
					document.form1.usereserve.focus();
					return;
				} else if(document.form1.usereserve.value < 0) {
					alert("적립금은 0원보다 크게 사용하셔야 합니다.");
					document.form1.usereserve.focus();
					return;
				}
				<? } ?>

				<? if($_data->reserve_maxuse>=0 && strlen($okreserve)>0 && $okreserve>0 && $_data->coupon_ok=="Y" && $rcall_type=="N") { ?>
				//if(document.form1.usereserve.value>0 && document.form1.coupon_code.value.length==8){
				if(document.form1.usereserve.value>0 && document.form1.couponlist.value.length>8){
					alert('적립금과 쿠폰을 동시에 사용이 불가능합니다.\n둘중에 하나만 사용하시기 바랍니다.');
					document.form1.usereserve.focus();
					return;
				}
				<? } ?>

				<? if($_data->reserve_maxuse>=0 && $bankreserve=="N") { ?>
				if (document.form1.usereserve.value>0) {
					if(paymethod!="B" && paymethod!="V" && paymethod!="O" && paymethod!="Q") {
						alert('적립금은 현금결제시에만 사용이 가능합니다.\n현금결제로 선택해 주세요');
						document.form1.paymethod.value="";
						return;
					}
				}
				<? } ?>
			<? } ?>


			prlistcnt="<?=$arr_prlist?>"+0;
			if(document.form1.msg_type.value=="1") {
				message_len = document.form1.order_prmsg.value.length;
				message_end = document.form1.order_prmsg.value.charCodeAt(message_len-1);
				if (message_len>0 && (message_end==39 || message_end==34 || message_end==92) ) {
					document.form1.order_prmsg.value += " ";
				}
			} else if(document.form1.msg_type.value=="2") {
				for(j=0;j<prlistcnt;j++) {
					message_len = document.form1["order_prmsg"+j].value.length;
					message_end = document.form1["order_prmsg"+j].value.charCodeAt(message_len-1);
					if (message_len>0 && (message_end==39 || message_end==34 || message_end==92) ) {
						document.form1["order_prmsg"+j].value += " ";
					}
				}
			}

			document.form1.receiver_addr.value = "우편번호 : " + document.form1.rpost1.value + "-" + document.form1.rpost2.value + "\n주소 : " + document.form1.raddr1.value + "  " + document.form1.raddr2.value;

			<? if($_data->coupon_ok=="Y" && strlen($_ShopInfo->getMemid())>0) { ?>
				if (document.form1.bank_only.value=="Y") {
					if(paymethod!="B" && paymethod!="V" && paymethod!="O" && paymethod!="Q") {
						alert("선택하신 쿠폰은 현금결제만 가능합니다.\n현금결제로 선택해 주세요");
						document.form1.paymethod.value="";
						return;
					}
				}
			<? } ?>
			document.form1.order_msg.value="";
			if(document.form1.process.value=="N") {
				<? if(strlen($etcmessage[1])>0) {?>
					if(document.form1.nowdelivery.checked==true) {
						document.form1.order_msg.value+="<font color=red>희망배송일 : 가능한 빨리배송</font>";
					} else {
						document.form1.order_msg.value+="<font color=red>희망배송일 : "+document.form1.year.value+"년 "+document.form1.mon.value+"월 "+document.form1.day.value+"일";
						<? if(strlen($etcmessage[1])==6) { ?>
						document.form1.order_msg.value+=" "+document.form1.time.value;
						<? } ?>
						document.form1.order_msg.value+="</font>";
					}
				<? } ?>

				<? if($etcmessage[2]=="Y") { ?>
					if(document.form1.bankname.value.length>1 && (document.form1.paymethod.length==null && paymethod=="B")) {
						if(document.form1.order_msg.value.length>0) document.form1.order_msg.value+="\n";
						document.form1.order_msg.value+="입금자 : "+document.form1.bankname.value;
					}
				<? } ?>
					
					if(document.form1.addorder_msg=="[object]") {
						if(document.form1.order_msg.value.length>0) document.form1.order_msg.value+="\n";
						document.form1.order_msg.value+=document.form1.addorder_msg.value;
					}

					document.form1.process.value="Y";
					document.form1.target = "PROCESS_IFRAME";

				<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {?>
						document.form1.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>order.php';
				<?}?>

					document.all.paybuttonlayer.style.display="none";
					document.all.payinglayer.style.display="block";

					document.form1.submit();

			} else {
				ordercancel();
			}

		<?
			}
		?>
	}
// 결제수단 변경
	function change_paymethod(val){
		for(i=0;i<=6;i++){
			if(document.getElementById("simg"+i)){
				document.getElementById("simg"+i).style.display = "none";
			}
		}
		document.getElementById("simg"+val).style.display = "block";

		solvPrice();
	}
	
	function giftchoices(){
	}
//-->
</SCRIPT>
<IFRAME id="PAY_PROCESS_IFRAME" name="PAY_PROCESS_IFRAME" style="display:none; POSITION: absolute; z-index:9999; border:5px solid #222222;" width="100%" frameborder="0"></IFRAME>
<DIV id="PAYWAIT_LAYER" style='position:absolute; left:50px; top:120px; width:503; height: 255; z-index:1; visibility: hidden'><a href="JavaScript:PaymentOpen();"><img src="<?=$Dir?>images/paywait.gif" align=absmiddle border=0 name=paywait galleryimg=no></a></DIV>
<IFRAME id="PAYWAIT_IFRAME" name="PAYWAIT_IFRAME" style="left:50px; top:120px; width:503; height: 255; position:absolute; visibility:hidden"></IFRAME>
<!--IFRAME id=PROCESS_IFRAME name=PROCESS_IFRAME style="display:''" width=100% height=300></IFRAME-->
<IFRAME id=PROCESS_IFRAME name=PROCESS_IFRAME style="display:none"></IFRAME>

<?=$onload?>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>