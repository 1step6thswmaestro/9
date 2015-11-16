<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");
include_once($Dir."lib/ext/order_func.php");

$socialshopping = "social";

$mode=$_POST["mode"];
$code=$_POST["code"];
$ordertype=$_POST["ordertype"];	//바로구매 구분 (바로구매시 => ordernow)
$opts=$_POST["opts"];	//옵션그룹 선택된 항목 (예:1,1,2,)
$option1=$_POST["option1"];	//옵션1
$option2=$_POST["option2"];	//옵션2
$quantity=(int)$_REQUEST["quantity"];	//구매수량
if($quantity==0) $quantity=1;
$productcode=$_REQUEST["productcode"];

$orgquantity=$_POST["orgquantity"];
$orgoption1=$_POST["orgoption1"];
$orgoption2=$_POST["orgoption2"];

$assemble_type=$_POST["assemble_type"];
$assemble_list=@str_replace("|","",$_POST["assemble_list"]);
$assembleuse=$_POST["assembleuse"];
$assemble_idx=(int)$_POST["assemble_idx"];

$package_idx=(int)$_POST["package_idx"];

$sell_memid = $_POST["sell_memid"];

if($assemble_idx==0) {
	if($assembleuse=="Y") {
		$assemble_idx="99999";
	}
} else {
	$assembleuse="Y";
}

//장바구니 인증키 확인
if(strlen($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
	$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
}
$tblbasket = "tblbasket3";
$sql = "DELETE FROM tblbasket3 WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
mysql_query($sql,get_db_conn());

//sns 홍보 본인체크
if(strlen($_ShopInfo->getMemid()) > 0){
	$sql ="UPDATE tblbasket3 SET sell_memid ='' WHERE tempkey='".$_ShopInfo->getTempkey()."' AND sell_memid='".$_ShopInfo->getMemid()."'";
	mysql_query($sql,get_db_conn());
}

if($mode!="del" && $mode!="clear" && strlen($productcode)==18) {
	if(strlen($code)==0) {
		$code=substr($productcode,0,12);
	}
	$codeA=substr($code,0,3);
	$codeB=substr($code,3,3);
	$codeC=substr($code,6,3);
	$codeD=substr($code,9,3);
	if(strlen($codeA)!=3) $codeA="000";
	if(strlen($codeB)!=3) $codeB="000";
	if(strlen($codeC)!=3) $codeC="000";
	if(strlen($codeD)!=3) $codeD="000";

	$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' AND codeC='".$codeC."' AND codeD='".$codeD."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		if($row->group_code=="NO") {	//숨김 분류
			echo "<html></head><body onload=\"alert('판매가 종료된 상품입니다.');location.href='".$Dir.FrontDir."productdetail.php?productcode=".$productcode."';\"></body></html>";exit;
		} else if($row->group_code=="ALL" && strlen($_ShopInfo->getMemid())==0) {	//회원만 접근가능
			echo "<html></head><body onload=\"alert('로그인 하셔야 장바구니에 담으실 수 있습니다.');location.href='".$Dir.FrontDir."productdetail.php?productcode=".$productcode."';\"></body></html>";exit;
		} else if(strlen($row->group_code)>0 && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
			echo "<html></head><body onload=\"alert('해당 분류의 접근 권한이 없습니다.');location.href='".$Dir.FrontDir."productdetail.php?productcode=".$productcode."';\"></body></html>";exit;
		}
		if(eregi("S",$row->type)) {	//소셜상품
			$sql2 = "SELECT count(1) as cnt FROM tblproduct_social WHERE pcode='".$productcode."' AND '".time()."' between sell_startdate and sell_enddate ";
			$result2=mysql_query($sql2,get_db_conn());
			if($row2=mysql_fetch_object($result2)) {
				if($row2->cnt == 0){
					echo "<html></head><body onload=\"alert('판매가 종료된 상품입니다.');location.href='".$Dir.FrontDir."productdetail.php?productcode=".$productcode."';\"></body></html>";exit;
				}
			}
		}

		//Wishlist 담기
		if($mode=="wishlist") {
			if(strlen($_ShopInfo->getMemid())==0) {	//비회원
				echo "<html></head><body onload=\"alert('로그인을 하셔야 본 서비스를 이용하실 수 있습니다.');location.href='".$Dir.FrontDir."login.php?chUrl=".getUrl()."';\"></body></html>";exit;
			}
			$sql = "SELECT COUNT(*) as totcnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
			$result2=mysql_query($sql,get_db_conn());
			$row2=mysql_fetch_object($result2);
			$totcnt=$row2->totcnt;
			mysql_free_result($result2);
			$maxcnt=20;
			if($totcnt>=$maxcnt) {
				$sql = "SELECT b.productcode FROM tblwishlist a, tblproduct b ";
				$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
				$sql.= "WHERE a.id='".$_ShopInfo->getMemid()."' AND a.productcode=b.productcode ";
				$sql.= "AND b.display='Y' ";
				$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
				$sql.= "GROUP BY b.productcode ";
				$result2=mysql_query($sql,get_db_conn());
				$i=0;
				$wishprcode="";
				while($row2=mysql_fetch_object($result2)) {
					$wishprcode.="'".$row2->productcode."',";
					$i++;
				}
				mysql_free_result($result2);
				$totcnt=$i;
				$wishprcode=substr($wishprcode,0,-1);
				if(strlen($wishprcode)>0) {
					$sql = "DELETE FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' AND productcode NOT IN (".$wishprcode.") ";
					mysql_query($sql,get_db_conn());
				}
			}
			if($totcnt<$maxcnt) {
				$sql = "SELECT COUNT(*) as cnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' AND productcode='".$productcode."' ";
				$result2=mysql_query($sql,get_db_conn());
				$row2=mysql_fetch_object($result2);
				$cnt=$row2->cnt;
				mysql_free_result($result2);
				if($cnt>0) {
					$sql = "UPDATE tblwishlist SET date='".date("YmdHis")."' ";
					$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
					$sql.= "AND productcode='".$productcode."' ";
					$sql.= "AND opt1_idx='".$option1."' AND opt2_idx='".$option2."' AND optidxs='".$opts."' ";
					mysql_query($sql,get_db_conn());

					echo "<html></head><body onload=\"alert('WishList에 이미 등록된 상품입니다.');history.go(-1);\"></body></html>";exit;
				} else {
					$sql = "INSERT tblwishlist SET ";
					$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
					$sql.= "productcode	= '".$productcode."' ";
					mysql_query($sql,get_db_conn());
					echo "<html></head><body onload=\"alert('WishList에 해당 상품을 등록하였습니다.');history.go(-1);\"></body></html>";exit;
				}
			} else {
				echo "<html></head><body onload=\"alert('WishList에는 ".$maxcnt."개 까지만 등록이 가능합니다.\\n\\nWishList에서 다른 상품을 삭제하신 후 등록하시기 바랍니다.');history.go(-1);\"></body></html>";exit;
			}
		}
	} else {
		echo "<html></head><body onload=\"alert('해당 분류가 존재하지 않습니다.');location.href='".$Dir.FrontDir."productdetail.php?productcode=".$productcode."';\"></body></html>";exit;
	}
	mysql_free_result($result);
} else if($mode=="clear") {	//장바구니 비우기
	$sql = "DELETE FROM tblbasket3 WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
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
			$basketsql3 = "SELECT productcode,quantity,productname,sellprice FROM tblproduct ";
			$basketsql3.= "WHERE pridx IN ('".str_replace(",","','",$package_list_exp[$basketrow2->package_idx])."') ";
			$basketsql3.= "AND display = 'Y' ";

			$basketresult3 = mysql_query($basketsql3,get_db_conn());
			$sellprice_package_listtmp=0;
			while($basketrow3=@mysql_fetch_object($basketresult3)) {
				$assemble_proquantity[$basketrow3->productcode]+=$basketrow2->quantity;
				$productcode_package_listtmp[] = $basketrow3->productcode;
				$quantity_package_listtmp[] = $basketrow3->quantity;
				$productname_package_listtmp[] = $basketrow3->productname;
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
				$productname_package_list[$basketrow2->productcode][$basketrow2->package_idx] = $productname_package_listtmp;
			}

			if(strlen($package_productcode_tmp)==0) { // 수량 및 옵션을 변경항 상품의 패키지 구성 상품 정보(재고 체크에서 필요)
				if($mode!="clear" && $mode!="wishlist" && $mode!="del" && strlen($quantity)>0 && strlen($productcode)==18) {
					if(count($productcode_package_listtmp)>0 && $basketrow2->productcode==$productcode && $basketrow2->package_idx==$package_idx && strlen($package_idx)>0 && (int)$package_idx>0) {
						$package_productcode_tmp = implode("",$productcode_package_listtmp);
						$package_quantity_tmp = implode("",$quantity_package_listtmp);
						$package_productname_tmp = implode("",$productname_package_listtmp);
					}
				}
			}
			unset($productcode_package_listtmp);
			unset($quantity_package_listtmp);
			unset($productname_package_listtmp);
		}
	}
}
@mysql_free_result($basketresult2);

$errmsg="";
if($mode!="clear" && $mode!="wishlist" && strlen($productcode)==18) {
	//해당상품삭제, 장바구니담기, 바로구매, 수량 업데이트, 원샷구매시에...
	if($mode!="del" && strlen($quantity)>0 && $quantity<=0 && strlen($productcode)==18) {
		echo "<html></head><body onload=\"alert('구매수량이 잘못되었습니다.');history.go(-1);\"></body></html>";
		exit;
	}

	//장바구니 담기 또는 수량/옵션 업데이트
	if($mode!="del" && strlen($quantity)>0 && strlen($productcode)==18) {
		$sql = "SELECT productname,quantity,display,option1,option2,option_quantity,etctype,group_check,assembleuse,package_num FROM tblproduct ";
		$sql.= "WHERE productcode='".$productcode."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			if($row->display!="Y") {
				$errmsg="해당 상품은 판매가 되지 않는 상품입니다.\\n";
			}

			$proassembleuse = $row->assembleuse;

			if($mode=="upd") {
				$sql2 = "SELECT SUM(quantity) as quantity FROM tblbasket3 WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
				$sql2.= "AND productcode='".$productcode."' ";
				$sql2.= "GROUP BY productcode ";
				$result2 = mysql_query($sql2,get_db_conn());
				if($row2 = mysql_fetch_object($result2)) {
					$rowcnt=$row2->quantity;
				} else {
					$rowcnt=0;
				}
				mysql_free_result($result2);

				$charge_quantity = -($orgquantity-$quantity);
				$rowcnt=$rowcnt+$charge_quantity;

				if($proassembleuse=="Y") { // 조립/코디 상품 등록에 따른 구성상품 체크
					$assemsql = "SELECT * FROM tblassembleproduct ";
					$assemsql.= "WHERE productcode='".$productcode."' ";
					$assemresult=mysql_query($assemsql,get_db_conn());
					if(!$assemrow=@mysql_fetch_object($assemresult)) {
						$errmsg="현재 구성상품이 미등록된 상품입니다. 구매가 불가능합니다.\\n";
					} else {
						$assemble_type_exp = explode("",$assemrow->assemble_type);
						$assemble_list_exp = explode("",$assemble_list);
					}
				}
			} else {
				$rowcnt=$quantity;
				$charge_quantity=$quantity;

				if($proassembleuse=="Y") { // 조립/코디 상품 등록에 따른 구성상품 체크
					if(strlen($assemble_type)>0) {
						$assemsql = "SELECT * FROM tblassembleproduct ";
						$assemsql.= "WHERE productcode='".$productcode."' ";
						$assemresult=mysql_query($assemsql,get_db_conn());
						if(!$assemrow=@mysql_fetch_object($assemresult)) {
							echo "<html></head><body onload=\"alert('현재 구성상품이 미등록된 상품입니다. 구매가 불가능합니다.');history.go(-1);\"></body></html>";
							exit;
						} else {
							$assemble_type_exp = explode("",$assemrow->assemble_type);
							$assemble_list_exp = explode("",$assemble_list);
							$assemble_list_count=0;
							for($i=1; $i<count($assemble_type_exp); $i++) {
								if(strlen($assemble_list_exp[$i])>0) {
									$assemble_list_count++;
								} else {
									if($assemble_type_exp[$i]=="Y") {
										echo "<html></head><body onload=\"alert('필수 구성 상품을 선택해 주세요.');history.go(-1);\"></body></html>";
										exit;
									}
								}
							}

							if($assemble_list_count>0) {
								$assemprosql = "SELECT COUNT(productcode) AS productcode_cnt FROM tblproduct ";
								$assemprosql.= "WHERE productcode IN ('".implode("','",$assemble_list_exp)."') ";
								$assemprosql.= "AND display = 'Y' ";
								$assemproresult=mysql_query($assemprosql,get_db_conn());
								if($assemprorow=@mysql_fetch_object($assemproresult)) {
									if($assemble_list_count!=$assemprorow->productcode_cnt) {
										echo "<html></head><body onload=\"alert('선택하신 구성 상품중 판매가 되지 않는 상품이 있습니다. 새로고침 후 다시 등록해 주세요.');history.go(-1);\"></body></html>";
										exit;
									}
								}
								@mysql_free_result($assemproresult);
							} else {
								echo "<html></head><body onload=\"alert('구성 상품을 하나 이상은 선택해야만 구매할 수 있습니다.');history.go(-1);\"></body></html>";
								exit;
							}
						}
					} else {
						echo "<html></head><body onload=\"alert('현재 구성상품이 미등록된 상품입니다. 구매가 불가능합니다.');history.go(-1);\"></body></html>";
						exit;
					}
				}

				if(strlen($package_productcode_tmp)==0 && strlen($package_idx)>0 && (int)$package_idx>0) { // 장바구니 담길 상품의 패키지 정보
					$basketsql2 = "SELECT b.package_list,b.package_title,b.package_price ";
					$basketsql2.= "FROM tblproduct AS a, tblproductpackage AS b ";
					$basketsql2.= "WHERE a.package_num=b.num ";
					$basketsql2.= "AND a.productcode='".$productcode."' ";
					$basketsql2.= "AND a.display = 'Y' ";
					$basketresult2 = mysql_query($basketsql2,get_db_conn());
					if($basketrow2=@mysql_fetch_object($basketresult2)) {
						if(strlen($basketrow2->package_title)>0 && strlen($package_idx)>0 && $package_idx>0) {
							$package_title_exp = explode("",$basketrow2->package_title);
							$package_price_exp = explode("",$basketrow2->package_price);
							$package_list_exp = explode("", $basketrow2->package_list);

							$title_package_listtmp[$productcode][$package_idx] = $package_title_exp[$package_idx];

							if(strlen($package_list_exp[$package_idx])>1) {
								$basketsql3 = "SELECT productcode,quantity,productname,sellprice FROM tblproduct ";
								$basketsql3.= "WHERE pridx IN ('".str_replace(",","','",$package_list_exp[$package_idx])."') ";
								$basketsql3.= "AND display = 'Y' ";

								$basketresult3 = mysql_query($basketsql3,get_db_conn());
								$sellprice_package_listtmp=0;
								while($basketrow3=@mysql_fetch_object($basketresult3)) {
									$assemble_proquantity[$basketrow3->productcode]+=$basketrow2->quantity;
									$productcode_package_listtmp[] = $basketrow3->productcode;
									$quantity_package_listtmp[] = $basketrow3->quantity;
									$productname_package_listtmp[] = $basketrow3->productname;
									$sellprice_package_listtmp+= $basketrow3->sellprice;
								}
								@mysql_free_result($basketresult3);

								if(count($productcode_package_listtmp)>0) {  //장바구니 패키지 상품 정보 출력시 필요한 정보
									$price_package_listtmp[$productcode][$package_idx]=0;
									if((int)$sellprice_package_listtmp>0) {
										$price_package_listtmp[$productcode][$package_idx]=(int)$sellprice_package_listtmp;
										if(strlen($package_price_exp[$package_idx])>0) {
											$package_price_expexp = explode(",",$package_price_exp[$package_idx]);
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
														$price_package_listtmp[$productcode][$package_idx]=$sumsellpricecal;
													}
												}
											}
										}
									}

									$productcode_package_list[$productcode][$package_idx] = $productcode_package_listtmp;
									$productname_package_list[$productcode][$package_idx] = $productname_package_listtmp;

									$package_productcode_tmp = implode("",$productcode_package_listtmp);
									$package_quantity_tmp = implode("",$quantity_package_listtmp);
									$package_productname_tmp = implode("",$productname_package_listtmp);

									unset($productcode_package_listtmp);
									unset($quantity_package_listtmp);
									unset($productname_package_listtmp);
								}
							}
						}
					}
					@mysql_free_result($basketresult2);
				}
			}

			if($row->group_check!="N") {
				if(strlen($_ShopInfo->getMemid())>0) {
					$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
					$sqlgc.= "WHERE productcode='".$productcode."' ";
					$sqlgc.= "AND group_code='".$_ShopInfo->getMemgroup()."' ";
					$resultgc=mysql_query($sqlgc,get_db_conn());
					if($rowgc=@mysql_fetch_object($resultgc)) {
						if($rowgc->groupcheck_count<1) {
							$errmsg="해당 상품은 지정 등급 전용 상품입니다.\\n";
						}
						@mysql_free_result($resultgc);
					} else {
						$errmsg="해당 상품은 지정 등급 전용 상품입니다.\\n";
					}
				} else {
					$errmsg="해당 상품은 회원 전용 상품입니다.\\n";
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
					$errmsg="해당 상품은 판매가 되지 않습니다. 다른 상품을 주문해 주세요.\\n";
				}
			}
			if(strlen($errmsg)==0) {
				if ($miniq!=1 && $miniq>1 && $rowcnt<$miniq)
					$errmsg="해당 상품은 최소 ".$miniq."개 이상 주문하셔야 합니다.\\n";
				if ($maxq!="?" && $maxq>0 && $rowcnt>$maxq)
					$errmsg.="해당 상품은 최대 ".$maxq."개 이하로 주문하셔야 합니다.\\n";

				if(empty($option1) && strlen($row->option1)>0)  $option1=1;
				if(empty($option2) && strlen($row->option2)>0)  $option2=1;
				if(strlen($row->quantity)>0) {
					if ($rowcnt>$row->quantity) {
						if ($row->quantity>0)
							$errmsg.="해당 상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 ".$row->quantity." 개 입니다.")."\\n";
						else
							$errmsg.= "해당 상품이 다른 고객의 주문으로 품절되었습니다.\\n";
					}
				}

				///////////////////////////////// 코디/조립 기능으로 인한 재고량 체크 ///////////////////////////////////////////////
				$basketsql = "SELECT productcode,assemble_list,quantity,assemble_idx FROM tblbasket3 ";
				$basketsql.= "WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
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

				if(count($assemble_list_exp)>0) {
					for($i=0; $i<count($assemble_list_exp); $i++) {
						if(strlen($assemble_list_exp[$i])>0) {
							$assemble_proquantity[$assemble_list_exp[$i]]+=$charge_quantity;
						}
					}
					$assemprosql = "SELECT productcode,quantity,productname FROM tblproduct ";
					$assemprosql.= "WHERE productcode IN ('".implode("','",$assemble_list_exp)."') ";
					$assemprosql.= "AND display = 'Y' ";
					$assemproresult=mysql_query($assemprosql,get_db_conn());
					while($assemprorow=@mysql_fetch_object($assemproresult)) {
						if(strlen($assemprorow->quantity)>0) {
							if($assemble_proquantity[$assemprorow->productcode] > $assemprorow->quantity) {
								if($assemprorow->quantity>0) {
									$errmsg.="해당 상품의 구성상품 [".ereg_replace("'","",$assemprorow->productname)."] 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 ".$assemprorow->quantity." 개 입니다.")."\\n";
								} else {
									$errmsg.="해당 상품의 구성상품 [".ereg_replace("'","",$assemprorow->productname)."] 다른 고객의 주문으로 품절되었습니다.\\n";
								}
							}
						}
					}
					@mysql_free_result($assemproresult);
				} else if(strlen($package_productcode_tmp)>0) {
					$assemble_proquantity[$productcode]+=$charge_quantity;
					$package_productcode_tmpexp = explode("",$package_productcode_tmp);
					$package_quantity_tmpexp = explode("",$package_quantity_tmp);
					$package_productname_tmpexp = explode("",$package_productname_tmp);
					for($i=0; $i<count($package_productcode_tmpexp); $i++) {
						if(strlen($package_productcode_tmpexp[$i])>0) {
							$assemble_proquantity[$package_productcode_tmpexp[$i]]+=$charge_quantity;

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
				} else {
					$assemble_proquantity[$productcode]+=$charge_quantity;
					if(strlen($row->quantity)>0) {
						if ($assemble_proquantity[$productcode] > $row->quantity) {
							if ($row->quantity>0)
								$errmsg.="해당 상품의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"현재 ".$row->quantity." 개 입니다.")."\\n";
							else
								$errmsg.= "해당 상품이 다른 고객의 주문으로 품절되었습니다.\\n";
						}
					}
				}

				if(strlen($row->option_quantity)>0) {
					$optioncnt = explode(",",substr($row->option_quantity,1));
					if($option2==0) $tmoption2=1;
					else $tmoption2=$option2;
					$optionvalue=$optioncnt[(($tmoption2-1)*10)+($option1-1)];
					if($optionvalue<=0 && $optionvalue!="") {
						$errmsg.="해당 상품의 선택된 옵션은 다른 고객의 주문으로 품절되었습니다.\\n";
					} else if($optionvalue<$quantity && $optionvalue!="") {
						$errmsg.="해당 상품의 선택된 옵션의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"$optionvalue 개 입니다.")."\\n";
					} else {
						if($mode=="upd") {
							if (empty($option1))  $option1=0;
							if (empty($option2))  $option2=0;
							if (empty($opts))  $opts="0";
							if (empty($assemble_idx))  $assemble_idx=0;

							$samesql = "SELECT * FROM tblbasket3 WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
							$samesql.= "AND productcode='".$productcode."' ";
							$samesql.= "AND opt1_idx='".$option1."' AND opt2_idx='".$option2."' AND optidxs='".$opts."' ";
							$samesql.= "AND assemble_idx = '".$assemble_idx."' ";
							$sameresult = mysql_query($samesql,get_db_conn());
							$samerow=mysql_fetch_object($sameresult);
							mysql_free_result($sameresult);
							if($samerow && ($option1!=$orgoption1 || $option2!=$orgoption2)) {
								if($optionvalue<($samerow->quantity + $quantity) && $optionvalue!="") {
									$errmsg.="해당 상품의 선택된 옵션과 중복상품의 옵션의 재고가 ".($_data->ETCTYPE["STOCK"]=="N"?"부족합니다.":"$optionvalue 개 입니다.")."\\n";
								}
							}
						}
					}
				}
			}
		} else {
			$errmsg="해당 상품이 존재하지 않습니다.\\n";
		}
		mysql_free_result($result);

		if(strlen($errmsg)>0) {
			echo "<html></head><body onload=\"alert('".$errmsg."');location.href='".$Dir.FrontDir."productdetail.php?productcode=".$productcode."'\"></body></html>";
			exit;
		}
	}

	// 이미 장바구니에 담긴 상품인지 검사하여 있으면 카운트만 증가.
	if (empty($option1))  $option1=0;
	if (empty($option2))  $option2=0;
	if (empty($opts))  $opts="0";
	if (empty($assemble_idx))  $assemble_idx=0;

	if($proassembleuse=="Y") {
		$assemaxsql = "SELECT MAX(assemble_idx) AS assemble_idx_max FROM tblbasket3 WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
		$assemaxsql.= "AND productcode='".$productcode."' ";
		$assemaxsql.= "AND opt1_idx='".$option1."' AND opt2_idx='".$option2."' AND optidxs='".$opts."' ";
		$assemaxsql.= "AND assemble_idx > 0 ";
		$assemaxresult = mysql_query($assemaxsql,get_db_conn());
		$assemaxrow=@mysql_fetch_object($assemaxresult);
		@mysql_free_result($assemaxresult);
		$assemble_idx_max = $assemaxrow->assemble_idx_max+1;
	} else {
		$assemble_idx_max = 0;
	}

	$sql = "SELECT * FROM tblbasket3 WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND productcode='".$productcode."' ";
	$sql.= "AND opt1_idx='".$option1."' AND opt2_idx='".$option2."' AND optidxs='".$opts."' ";
	$sql.= "AND assemble_idx = '".$assemble_idx."' ";
	$sql.= "AND package_idx = '".$package_idx."' ";
	$result = mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);

	if ($mode=="del") {
		$sql = "DELETE FROM tblbasket3 WHERE tempkey='".$_ShopInfo->getTempkey()."' AND productcode='".$productcode."' ";
		$sql.= "AND opt1_idx='".$orgoption1."' AND opt2_idx='".$orgoption2."' AND optidxs='".$opts."' ";
		$sql.= "AND assemble_idx = '".$assemble_idx."' ";
		$sql.= "AND package_idx = '".$package_idx."' ";
		mysql_query($sql,get_db_conn());
	} else if ($mode=="upd") {
		if (($option1==$orgoption1 && $option2==$orgoption2) || !($row)) {
			$sql = "UPDATE tblbasket3 SET ";
			$sql.= "quantity		= '".$quantity."', ";
			$sql.= "opt1_idx		= '".$option1."', ";
			$sql.= "opt2_idx		= '".$option2."' ";
			$sql.= "WHERE tempkey	='".$_ShopInfo->getTempkey()."' ";
			$sql.= "AND productcode	='".$productcode."' AND opt1_idx='".$orgoption1."' ";
			$sql.= "AND opt2_idx	='".$orgoption2."' AND optidxs='".$opts."' ";
			$sql.= "AND assemble_idx = '".$assemble_idx."' ";
			$sql.= "AND package_idx = '".$package_idx."' ";
			mysql_query($sql,get_db_conn());
		} else {
			$c = $row->quantity + $quantity;
			$sql = "UPDATE tblbasket3 SET quantity='".$c."', opt1_idx='".$option1."' ";
			$sql.= "WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
			$sql.= "AND productcode='".$productcode."' AND opt1_idx='".$option1."' ";
			$sql.= "AND opt2_idx='".$option2."' AND optidxs='".$opts."' ";
			$sql.= "AND assemble_idx = '".$assemble_idx."' ";
			$sql.= "AND package_idx = '".$package_idx."' ";
			mysql_query($sql,get_db_conn());

			$sql = "DELETE FROM tblbasket3 WHERE tempkey='".$_ShopInfo->getTempkey()."' AND productcode='".$productcode."' ";
			$sql.= "AND opt1_idx='".$orgoption1."' AND opt2_idx='".$orgoption2."' AND optidxs='".$opts."' ";
			$sql.= "AND assemble_idx = '".$assemble_idx."' ";
			$sql.= "AND package_idx = '".$package_idx."' ";
			mysql_query($sql,get_db_conn());
		}
	} else if ($row) {
		$onload="<script>alert('이미 장바구니에 상품이 담겨있습니다. 수량을 조절하시려면 수량입력후 수정하세요.');</script>";
	} else {
		if (strlen($productcode)==18) {
			$vdate = date("YmdHis");
			$sql = "SELECT COUNT(*) as cnt FROM tblbasket3 WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
			$result = mysql_query($sql,get_db_conn());
			$row = mysql_fetch_object($result);
			mysql_free_result($result);
			if($row->cnt>=200) {
				echo "<script>alert('장바구니에는 총 200개까지만 담을수 있습니다.');</script>";
			} else {
				$sql = "INSERT tblbasket3 SET ";
				$sql.= "tempkey			= '".$_ShopInfo->getTempkey()."', ";
				$sql.= "productcode		= '".$productcode."', ";
				$sql.= "opt1_idx		= '".$option1."', ";
				$sql.= "opt2_idx		= '".$option2."', ";
				$sql.= "optidxs			= '".$opts."', ";
				$sql.= "quantity		= '".$quantity."', ";
				$sql.= "package_idx	= '".$package_idx."', ";
				$sql.= "assemble_idx	= '".$assemble_idx_max."', ";
				$sql.= "assemble_list	= '".$assemble_list."', ";
				$sql.= "date			= '".$vdate."', ";
				$sql.= "sell_memid		= '".$sell_memid."' ";
				mysql_query($sql,get_db_conn());
			}
		}
	}
}


$basketItems = getBasketByArray($tblbasket);

?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 장바구니</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(mode,idx) {
	if(mode=="del") {
		if(confirm("해당 상품을 장바구니에서 삭제하시겠습니까?")) {
			document["form_"+idx].mode.value=mode;
			document["form_"+idx].submit();
		}
	} else if(mode=="upd") {
		if(document["form_"+idx].quantity.value.length==0 || document["form_"+idx].quantity.value==0) {
			alert("수량을 입력하세요.");
			document["form_"+idx].quantity.focus();
			return;
		}
		if(!IsNumeric(document["form_"+idx].quantity.value)) {
			alert("수량은 숫자만 입력하세요.");
			document["form_"+idx].quantity.focus();
			return;
		}
		document["form_"+idx].mode.value=mode;
		document["form_"+idx].submit();
	}
}
function change_quantity(gbn,idx) {
	tmp=document["form_"+idx].quantity.value;
	if(gbn=="up") {
		tmp++;
	} else if(gbn=="dn") {
		if(tmp>1) tmp--;
	}
	document["form_"+idx].quantity.value=tmp;
}
function go_wishlist(idx) {
	document.wishform.productcode.value=document["form_"+idx].productcode.value;
	document.wishform.opts.value=document["form_"+idx].opts.value;
	document.wishform.option1.value=document["form_"+idx].orgoption1.value;
	document.wishform.option2.value=document["form_"+idx].orgoption2.value;
	window.open("about:blank","confirmwishlist","width=500,height=300,scrollbars=no");
	document.wishform.submit();
}
function basket_clear() {
	if(confirm("장바구니를 비우시겠습니까?")) {
		document.delform.mode.value="clear";
		document.delform.submit();
	}
}
function check_login() {
	if(confirm("로그인이 필요한 서비스입니다. 로그인을 하시겠습니까?")) {
		document.location.href="<?=$Dir.FrontDir?>login.php?chUrl=<?=getUrl()?>";
	}
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

<?if($_data->oneshot_ok=="Y" || $_data->design_basket=="U") {?>
var imagepath="<?=$Dir.DataDir?>shopimages/product/";
var default_primage="oneshot_primage<?=$_data->design_basket?>.gif";
var prall=new Array();
function pralllist() {
	var argv = pralllist.arguments;
	var argc = pralllist.arguments.length;

	//Property 선언
	this.classname		= "pralllist"								//classname
	this.debug			= false;									//디버깅여부.
	this.productcode	= new String((argc > 0) ? argv[0] : "");
	this.tinyimage		= new String((argc > 1) ? argv[1] : "");
	this.option1		= ToInt((argc > 2) ? argv[2] : 1);
	this.option2		= ToInt((argc > 3) ? argv[3] : 1);
	this.quantity		= ToInt((argc > 4) ? argv[4] : 1);
	this.miniq			= ToInt((argc > 5) ? argv[5] : 1);
	this.assembleuse	= new String((argc > 6) ? argv[6] : "N");
	this.package_num	= new String((argc > 7) ? argv[7] : "");
}

function CheckCode() {
	form=document.form1;
	if(form.codeA.value.length==3 && form.codeB.value.length==3 && form.codeC.value.length==3 && form.codeD.value.length==3) {
		form.submit();
	} else {
		form.tmpprcode.options.length=1;
		var d = new Option("상품 선택");
		form.tmpprcode.options[0] = d;
		form.tmpprcode.options[0].value = "";

		document.all["oneshot_primage"].src="<?=$Dir?>images/common/basket/"+default_primage;
		form.productcode.value="";
		form.quantity.value="";
		form.option1.value="";
		form.option2.value="";
	}
}

function CheckProduct() {
	form=document.form1;
	if(form.tmpprcode.value.length==0) {
		document.all["oneshot_primage"].src="<?=$Dir?>images/common/basket/"+default_primage;
		form.productcode.value="";
		form.quantity.value="";
		form.option1.value="";
		form.option2.value="";
		form.assembleuse.value="";
		form.package_num.value="";
	} else {
		productcode=prall[form.tmpprcode.value].productcode;
		tinyimage=prall[form.tmpprcode.value].tinyimage;
		option1=prall[form.tmpprcode.value].option1;
		option2=prall[form.tmpprcode.value].option2;
		quantity=prall[form.tmpprcode.value].miniq;
		assembleuse=prall[form.tmpprcode.value].assembleuse;
		package_num=prall[form.tmpprcode.value].package_num;
		if(tinyimage.length>0) {
			document.all["oneshot_primage"].src=imagepath+tinyimage;
		} else {
			document.all["oneshot_primage"].src="<?=$Dir?>images/common/basket/"+default_primage;
		}
		form.productcode.value=productcode;
		form.quantity.value=quantity;
		form.option1.value=option1;
		form.option2.value=option2;
		form.assembleuse.value=assembleuse;
		form.package_num.value=package_num;
	}
}

function OneshotBasketIn() {
	if(document.form1.productcode.value.length!=18) {
		alert("상품을 선택하세요.");
		document.form1.tmpprcode.focus();
		return;
	}
	if(document.form1.assembleuse.value=="Y") {
		if(confirm("해당 상품은 구성상품을 구성해야만 구매가 가능한 상품입니다.\n\n         상품 상세페이지에서 구성를 하겠습니까?")) {
			location.href="<?=$Dir.FrontDir?>productdetail.php?productcode="+document.form1.productcode.value;
		}
	} else if(document.form1.package_num.value.length>0) {
		if(confirm("해당 상품은 패키지 선택 상품으로써 상품상세페이지에서 패키지 정보를 확인 해 주세요.\n\n                              상품상세페이지로 이동 하겠습니까?")) {
			location.href="<?=$Dir.FrontDir?>productdetail.php?productcode="+document.form1.productcode.value;
		}
	} else {
		document.form1.submit();
	}
}
<?}?>

//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
$leftmenu="Y";
if($_data->design_basket=="U") {
	$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='basket'";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);
		$leftmenu=$row->leftmenu;
		$newdesign="Y";
	}
	mysql_free_result($result);
}

if ($leftmenu!="N") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/basket_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/basket_title.gif\" border=\"0\" alt=\"장바구니\"></td>\n";
	} else {
		echo "<td>\n";
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/basket_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/basket_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/basket_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		echo "</td>\n";
	}
	echo "</tr>\n";
}

echo "<tr>\n";
echo "	<td align=\"center\">\n";
include ($Dir.TempletDir."basket/basket".$_data->design_basket.".php");
echo "	</td>\n";
echo "</tr>\n";

if($basketItems['sumprice']>=$_data->bank_miniprice) {
	$param="";
	if($ordertype=="pester"){
		echo "<script>location.href='".$Dir.FrontDir."login.php?chUrl=".urlencode($Dir.FrontDir."pester.php?ordertype=pester")."';</script>";
		exit;
	}else{
		if($ordertype=="present"){
			$param = "?ordertype=present";
		}
		echo "<script>location.href='".$Dir.FrontDir."login.php?chUrl=".urlencode($Dir.FrontDir."order3.php".$param)."';</script>";
		exit;
	}
} else {
	echo "<script>alert('".number_format($_data->bank_miniprice)."원 이상 구매가 가능합니다.');history.back();</script>";exit;
}


?>

<form name=wishform method=post action="<?=$Dir.FrontDir?>confirm_wishlist.php" target="confirmwishlist">
<input type=hidden name=productcode>
<input type=hidden name=opts>
<input type=hidden name=option1>
<input type=hidden name=option2>
</form>
<form name=delform method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=productcode>
</form>
</table>

<? include ($Dir."lib/bottom.php") ?>

<?=$onload?>

</BODY>
</HTML>