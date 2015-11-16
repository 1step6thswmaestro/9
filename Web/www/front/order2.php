<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");
include_once($Dir."lib/ext/order_func.php");
include_once($Dir."lib/ext/coupon_func.php");




$ordertype=$_GET["ordertype"];



//회원전용일 경우 로긴페이지로...
if($_data->member_buygrant=="Y" && strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}




//장바구니 인증키 확인
if(strlen($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
	$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
}

// 장바구니 데이터 (Array) ==================================================
$basketItems = getBasketByArray('tblbasket2');

/*
echo "<div style=\" height:500px; overflow:scroll;  border:2px solid #ff0000 ;  text-align:left;\">";
_pr($basketItems);
echo "</div>";
*/


/*
회원 등급 할인 메세지 ============
	RW : 금액 추가 적립
	RP  : % 추가 적립
	SW : 금액 추가 할인
	SP  : % 추가 할인
*/
$groupMemberSale = "";
if( $basketItems['groupMemberSale'] ) {
	$groupMemberSale .= "
		<font style=\"letter-spacing:0px;\"><b>".$basketItems['groupMemberSale']['name']."</b></font>님(".$basketItems['groupMemberSale']['group'].")은 회원 등급 할인
		<font color=\"#ee0a02\" style=\"letter-spacing:0px;\">".number_format($basketItems['groupMemberSale']['useMoney'])."</font>원 이상
		<font  color=\"#ee0a02\">".$basketItems['groupMemberSale']['payType']."</font> 결제시
	";
	if($basketItems['groupMemberSale']['groupCode']=="RW") {
		$groupMemberSale .= "<font color=#ee0a02 style=letter-spacing:0px;><b>".number_format($basketItems['groupMemberSale']['addMoney'])."</b>원</font>의 적립금을 추가로 적립해 드립니다.";
	} else if($basketItems['groupMemberSale']['groupCode']=="RP") {
		$groupMemberSale .= "<font color=#ee0a02 style=letter-spacing:0px;><b>구매금액의 ".number_format($basketItems['groupMemberSale']['addMoney'])."</b>%</font>를 적립해 드립니다.";
	} else if($basketItems['groupMemberSale']['groupCode']=="SW") {
		$groupMemberSale .= "<font color=#ee0a02 style=letter-spacing:0px;><b>구매금액 ".number_format($basketItems['groupMemberSale']['addMoney'])."</b>원</font>을 추가로 할인 됩니다.";
	} else if($basketItems['groupMemberSale']['groupCode']=="SP") {
		$groupMemberSale .= "<font color=#ee0a02 style=letter-spacing:0px;><b>구매금액의 ".number_format($basketItems['groupMemberSale']['addMoney'])."</b>%</font>를 추가로 할인 됩니다.";
	}
	$groupMemberSale .= "<span id=\"couponEventMsg\"></span>";
}






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
if( preg_match("/^(Y|N)$/", $_data->payment_type) && $escrow_info["onlycard"]!="Y" ) {
	$payType .= "<input type='radio' onclick=\"change_paymethod(1);\" name='sel_paymethod' value='B' id=\"sel_paymethod1\"><label for=\"sel_paymethod1\" style=\"cursor:pointer;\">무통장 입금</label>&nbsp;&nbsp;";
}

//2:신용카드: 현금결제시 비활성
/*if(preg_match("/^(Y|C)$/", $_data->payment_type) && strlen($_data->card_id)>0 AND $bankonlyCHK == "N" ) {
	$payType .= "<input type='radio' onclick=\"change_paymethod(2);\" name='sel_paymethod' value='C' id=\"sel_paymethod2\"><label for=\"sel_paymethod2\" style=\"cursor:pointer;\">신용카드</label>&nbsp;&nbsp;";
}*/

//2:실시간계좌이체
/*if($escrow_info["onlycard"]!="Y" ) {
	if(strlen($_data->trans_id)>0) {
		$payType .= "<input type='radio' onclick=\"change_paymethod(3);\" name='sel_paymethod' value='V' id=\"sel_paymethod3\"><label for=\"sel_paymethod3\" style=\"cursor:pointer;\">실시간계좌이체</label>&nbsp;&nbsp;";
	}
}

//3:가상계좌
if($escrow_info["onlycard"]!="Y" ) {
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
*/
//현금결제 전용 상품 포함시 메세지
if( $bankonlyCHK == "Y" ) {
	$payType .= "&nbsp;&nbsp;&nbsp;<font color='#FF0000'>(*주문 상품에 [현금결제] 상품이 포함되어 신용카드결제가 불가능합니다.)</font>";
}

//////  결제 수단 선택 end  ////////////////////////////////////////////////


// 오프라인 쿠폰 링크
$offlineCouponInputButton = "<img src='/images/common/order/T01/offlineCouponInputButton.gif' align='absmiddle' style='cursor:pointer;' alt='오프라인 쿠폰 등록' onclick=\" coupon_check( 'offlinecoupon' );\">";


// shopinfo 사은품 활성화 정보 호출
$giftInfoRow = @mysql_fetch_object( mysql_query("SELECT `gift_type` FROM `tblshopinfo` LIMIT 1;",get_db_conn()) );
$giftInfoSetArray = explode("|",$giftInfoRow->gift_type);


#수량재고파악
$errmsg="";
$sql = "SELECT a.quantity as sumquantity,b.productcode,b.productname,b.display,b.quantity, ";
$sql.= "b.option_quantity,b.etctype,b.group_check,b.assembleuse,a.assemble_list AS basketassemble_list ";
$sql.= ", c.assemble_list,a.package_idx ";
$sql.= "FROM tblbasket2 a, tblproduct b ";
$sql.= "LEFT OUTER JOIN tblassembleproduct c ON b.productcode=c.productcode ";
$sql.= "WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
$sql.= "AND a.productcode=b.productcode ";
$result=mysql_query($sql,get_db_conn());
$assemble_proquantity_cnt=0;
while($row=mysql_fetch_object($result)) {
	if($row->display!="Y") {
		$errmsg="[".ereg_replace("'","",$row->productname)."]상품은 판매가 되지 않는 상품입니다.\\n";
	}

	// today sale 판매 시간 관련 check
	if(preg_match('/^899[0-9]{15}$/',$row->productcode)){
		$tsql = "select unix_timestamp(t.end) -unix_timestamp() as remain, t.salecnt+t.addquantity as sellcnt from tblproduct a inner join todaysale t using(pridx) WHERE a.productcode='".$row->productcode."' limit 1";
		if(false === $tres = mysql_query($tsql,get_db_conn())){
			$errmsg="[".ereg_replace("'","",$row->productname)."]의 정보를 DB 에서 확인 하는중 오류가 발생했습니다..\\n";
		}else{
			if(mysql_num_rows($tres) < 1){
				$errmsg="[".ereg_replace("'","",$row->productname)."]의 정보를 찾을수 없습니다.\\n";
			}else{
				$trow = mysql_fetch_assoc($tres);
				if($trow['remain'] < 1){
					$errmsg="[".ereg_replace("'","",$row->productname)."]은 판매 마감된 상품 입니다.\\n";
					mysql_query("delete from tblbasket2 where a.tempkey='".$_ShopInfo->getTempkey()."' and productcode='".$row->productcode."'",get_db_conn()); // 삭제 처리
				}
			}
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
			$basketsql = "SELECT productcode,assemble_list,quantity,assemble_idx FROM tblbasket2 WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
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
			$sql = "SELECT opt1_idx, opt2_idx, quantity FROM tblbasket2 ";
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
			@mysql_free_result($result2);
		}
	}
}
@mysql_free_result($result);

if(strlen($errmsg)>0) {
	echo "<html></head><body onload=\"alert('".$errmsg."');location.href='".$Dir.FrontDir."basket.php';\"></body></html>";
	exit;
}







//쿠폰 발행이 있을 경우
if($_REQUEST['mode']=="coupon" && strlen($_REQUEST['coupon_code'])==8){
	$onload = '';
	if(strlen($_ShopInfo->getMemid())==0) {	//비회원
		echo "<html></head><body onload=\"alert('로그인 후 쿠폰 다운로드가 가능합니다.');location.href='".$Dir.FrontDir."login.php?chUrl=".getUrl()."';\"></body></html>";exit;
	}else{
		$sql = "SELECT * FROM tblcouponinfo where coupon_code = '".$_REQUEST['coupon_code']."'";


		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			if($row->issue_tot_no>0 && $row->issue_tot_no<$row->issue_no+1) {
				$onload="<script>alert(\"모든 쿠폰이 발급되었습니다.\");</script>";
			} else {
				$date=date("YmdHis");
				if($row->date_start>0) {
					$date_start=$row->date_start;
					$date_end=$row->date_end;
				} else {
					$date_start = substr($date,0,10);
					$date_end = date("Ymd",mktime(0,0,0,substr($date,4,2),substr($date,6,2)+abs($row->date_start),substr($date,0,4)))."23";
				}
				$sql = "INSERT tblcouponissue SET ";
				$sql.= "coupon_code	= '".$_REQUEST['coupon_code']."', ";
				$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
				$sql.= "date_start	= '".$date_start."', ";
				$sql.= "date_end	= '".$date_end."', ";
				$sql.= "date		= '".$date."' ";

				mysql_query($sql,get_db_conn());
				if(!mysql_errno()) {
					$sql = "UPDATE tblcouponinfo SET issue_no = issue_no+1 ";
					$sql.= "WHERE coupon_code = '".$_REQUEST['coupon_code']."'";
					mysql_query($sql,get_db_conn());

					$onload="<script>alert(\"해당 쿠폰 발급이 완료되었습니다.\\n\\n상품 주문시 해당 쿠폰을 사용하실 수 있습니다.\");</script>";
				} else {
					if($row->repeat_id=="Y") {	//동일인 재발급이 가능하다면,,,,
						$sql = "UPDATE tblcouponissue SET ";
						if($row->date_start<=0) {
							$sql.= "date_start	= '".$date_start."', ";
							$sql.= "date_end	= '".$date_end."', ";
						}
						$sql.= "used		= 'N' ";
						$sql.= "WHERE coupon_code='".$_REQUEST['coupon_code']."' ";
						$sql.= "AND id='".$_ShopInfo->getMemid()."' ";
						mysql_query($sql,get_db_conn());
						$onload="<script>alert(\"해당 쿠폰 발급이 완료되었습니다.\\n\\n상품 주문시 해당 쿠폰을 사용하실 수 있습니다.\");</script>";
					} else {
						$onload="<script>alert(\"이미 쿠폰을 발급받으셨습니다.\\n\\n해당 쿠폰은 재발급이 불가능합니다.\");</script>";
					}
				}
			}
		}
		mysql_free_result($result);

	}

	if(_empty($onload)){
		echo $onload;
	}
	?>
	<script language="javascript" type="text/javascript">
		document.location.replace('/front/order.php');
	</script>
	<?
	exit;

}


// 보유 쿠폰 리스트
$mycoupon_codes = getMyCouponList('',true);







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
		else if (strlen($row->home_tel)>0) $home_tel = $row->home_tel;
		else if (strlen($row->office_tel)>0) $office_tel = $row->office_tel;
		$mobile=explode("-",replace_tel(check_num($mobile)));
		$home_tel=explode("-",replace_tel(check_num($row->home_tel)));

		$group_code=$row->group_code;
		@mysql_free_result($result);
		if(strlen($group_code)>0 && $group_code!=NULL) {
			$sql = "SELECT * FROM tblmembergroup WHERE group_code='".$group_code."' AND MID(group_code,1,1)!='M' ";
			$result=mysql_query($sql);
			if($row=mysql_fetch_object($result)){

				//그룹 이미지 출력처리 20131025 J.Bum
				if(file_exists($Dir.DataDir."shopimages/etc/groupimg_".$row->group_code.".gif")) {
					$royal_img="<img src=\"".$Dir.DataDir."shopimages/etc/groupimg_".$row->group_code.".gif\" border=0>";
				} else {
					$royal_img="<img src=\"".$Dir."images/common/group_img.gif\" border=0>\n";
				}

				$group_code = $row->group_code;
				$org_group_name=$row->group_name;  //그룹정보로 인해 추가
				$group_name=$row->group_name;
				$group_type=substr($row->group_code,0,2); // 그룹 타입 					RW : 금액 추가 적립 / RP  : % 추가 적립 / SW : 금액 추가 할인 / SP  : % 추가 할인
				$group_usemoney=$row->group_usemoney; // 그룹할인 기준 금액
				$group_addmoney=$row->group_addmoney; // 그룹할인금액
				$group_payment=$row->group_payment; // 결제 방식					"B"=>"현금","C"=>"카드","N"=>"현금/카드"
					if($group_payment=="B") {
						$group_name.=" (현금결제시)";
					} else if($group_payment=="C") {
						$group_name.=" (카드결제시)";
					}
			}
			@mysql_free_result($result);
		}
	} else {
		$_ShopInfo->setMemid("");
	}
}




// 비회원 동의
if( strlen($_ShopInfo->getMemid()) == 0 ){
	$sql = "SELECT privercy FROM tbldesign ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$privercy_exp = @explode("=", $row->privercy);
		$privercybody=$privercy_exp[1];
	}
	@mysql_free_result($result);

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
}



$sumprice = $basketItems['sumprice'];

$cateAuth =array();
//_pr($basketItems['vender'][0]['products'][0]['cateAuth']);
//echo  $basketItems['cateauth']['reserve'];
if($basketItems['vender'][0]['products'][0]['cateAuth']['reserve'] == 'N') array_push($cateAuth,'<IMG SRC=\'/images/common/basket/001/basket_spe_icon001x.gif\' hspace=\'1\' alt=\'적립금 사용불가\' />');
if($basketItems['vender'][0]['products'][0]['cateAuth']['coupon'] == 'N') array_push($cateAuth,'<IMG SRC=\'/images/common/basket/001/basket_spe_icon002x.gif\' hspace=\'1\' alt=\'할인쿠폰 적용불가\' />');
if($basketItems['vender'][0]['products'][0]['cateAuth']['refund'] == 'N') array_push($cateAuth,'<img src=\'/images/common/basket/001/basket_spe_icon003x.gif\' hspace=\'1\' alt=\'교환/반품 불가\' />');
if($basketItems['vender'][0]['products'][0]['cateAuth']['gift'] == 'Y') array_push($cateAuth,'<img src=\'/images/common/basket/001/basket_spe_icon004o.gif\' hspace=\'1\' alt=\'사은품 지급\' />');
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
	document.orderpayform.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>orderpay.php';
<?}?>

	document.orderpayform.submit();
}

function ordercancel(gbn) {
	if(gbn=="cancel" && document.form1.process.value=="N") {
		document.location.href="productgift.php";
	} else {
		if (PROCESS_IFRAME.chargepop) {
			if (gbn=="cancel") alert("결제창과 연결중입니다. 취소하시려면 결제창에서 취소하기를 누르세요.");
			PROCESS_IFRAME.chargepop.focus();
		} else {
			PROCESS_IFRAME.PaymentOpen();
			//ProcessWait('visible');
		}
	}
}

/*function ProcessWait(display) {
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
}*/

function PaymentOpen() {
	PROCESS_IFRAME.PaymentOpen();
	//ProcessWait('visible');
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
<?
// PG모듈 호출
if( strlen($_data->card_id)>0 ) {
	$pgInfo=GetEscrowType($_data->card_id);
	if( $pgInfo["PG"] == "A" ) {
		// KCP
		echo "<script language='javascript' src='https://pay.kcp.co.kr/plugin/payplus.js'></script>";
		echo "<script language='javascript'> StartSmartUpdate(); </script>";
	}
	if( $pgInfo["PG"] == "B" ) {
		// LG U+
	}
	if( $pgInfo["PG"] == "C" ) {
		// 올더게이트
		echo "<script language=javascript src=\"http://www.allthegate.com/plugin/AGSWallet.js\"></script>";
		echo "<script language='javascript'> StartSmartUpdate(); </script>";
	}
	if( $pgInfo["PG"] == "D" ) {
		// 이니시스
		echo "<script language='javascript' src='http://plugin.inicis.com/pay40.js'></script>";
		echo "<script language='javascript'> StartSmartUpdate(); </script>";
	}
	if( $pgInfo["PG"] == "E" ) {
		// 나이스
		echo "<script src=\"https://web.nicepay.co.kr/flex/js/nicepay_tr.js\" language=\"javascript\"></script>";
		echo "<script language='javascript'> NicePayUpdate(); </script>";
		echo "<script src=\"https://www.vpay.co.kr/KVPplugin_ssl.js\" language=\"javascript\"></script>";
		echo "<script language='javascript'> StartSmartUpdate(); </script>";
	}
}
?>

	<style type="text/css">
		.itemListTbl{border-top:1px solid #222222; border-bottom:1px solid #222222; empty-cells:show}
		.itemListTbl thead .thstyle {height:35px; background:#f8f8f8; border:1px solid #e5e5e5; border-left:hidden; }
		.itemListTbl thead .thstyle2 {height:35px; background:#f8f8f8; border:1px solid #e5e5e5; border-left:hidden; border-right:hidden;}

		.itemListTbl tbody .tdstyle {border-right:1px solid #e5e5e5; border-bottom:1px solid #e5e5e5; padding:8px 0px;}
		.itemListTbl tbody .tdstyle2 {border-bottom:1px solid #e5e5e5; padding:5px 10px;}

		.orderTbl {border:1px solid #bbbbbb;}
		.orderTbl caption {text-align:left; padding:7px 0px;}
		.orderTbl th {width:150px; background:#f8f8f8; border-right:1px solid #eeeeee; border-bottom:1px solid #eeeeee; text-align:left; padding: 9px 0px 9px 15px;}
		.orderTbl td {border-bottom:1px solid #eeeeee; border-right:1px solid #eeeeee; text-align:left; padding: 5px 0px 5px 10px;}
		.orderTbl td.noCont {border:0px; font-size:1px; height:7px; line-height:1px;}
		.orderTbl td.payTbl {width:100%; border:0px;}
		.orderTbl .lastTh {width:150px; border-bottom:none;}
		.orderTbl .lastTd {border-bottom:none;}
		.orderTbl .input {border:1px solid #dddddd; height:22px; line-height:22px;}

		.couponDownArea{ display:block;}

		#addressSelDiv {height:24px; margin-right:10px; margin-bottom:8px; border-bottom:1px solid #ddd;}

		#giftOptionArea table {border:1px solid #fff; margin-top:5px;}
		#giftOptionArea td {border:1px solid #fff; margin-top:5px; font-size:12px; text-align:left;}
	</style>

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

<table cellpadding="0" cellspacing="0" width="100%" height="100%">
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%" height="100%">
				<form name=form1 action="<?=$Dir.FrontDir?>ordersend2.php" method=post>
				<input type=hidden name="addorder_msg" value="">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td>
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_stitle1.gif" border="0" vspace="3"></td>
											<td rowspan="2" align="right" valign="bottom"><font color="#A1A1A1">주문정보를 입력하신 후, <font color="#ee1a02">결제버튼</font>을 눌러주세요.</font></td>
										</tr>
										<tr>
											<td height="2"></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table cellpadding="0" cellspacing="0" width="100%" class="itemListTbl">
										<col width="60"></col>
										<col></col>
										<col width="60"></col>
										<col width="80"></col>
										<col width="60"></col>
										<col width="100"></col>
										<thead>
											<tr>
												<th class="thstyle" colspan="2">상품명</th>
												<th class="thstyle">적립금</th>
												<th class="thstyle">상품가격</th>
												<th class="thstyle">수량</th>
												<th class="thstyle2">주문금액</th>
											</tr>
										</thead>
										<tbody>
<?
	$sql = "SELECT b.vender FROM tblbasket2 a, tblproduct b WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
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
		//echo "<tr><td colspan=6 height=10></td></tr>\n";

		$sql = "SELECT a.gift, a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.productcode,b.productname,b.sellprice, ";
		$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
		$sql.= "b.etctype,b.deli_price,b.deli,b.sellprice*a.quantity as realprice,b.consumerprice,b.img_type, b.selfcode,a.assemble_list,a.assemble_idx,a.package_idx ";
		$sql.= "FROM tblbasket2 a, tblproduct b WHERE b.vender='".$vgrp->vender."' ";
		$sql.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
		$sql.= "AND a.productcode=b.productcode ";
		$sql.= "ORDER BY a.date DESC ";
		$result=mysql_query($sql,get_db_conn());

		$vender_sumprice = 0;
		$vender_delisumprice = 0;//해당 입점업체의 기본배송비 총 구매액
		$vender_deliprice = 0;
		$deli_productprice=0;
		$deli_init = false;

		$gift = "";

		while($row = mysql_fetch_object($result)) {
			if (strlen($row->option_price)>0 && $row->opt1_idx==0) {
				$sql = "DELETE FROM tblbasket2 WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
				$sql.= "AND productcode='".$row->productcode."' AND opt1_idx='".$row->opt1_idx."' ";
				$sql.= "AND opt2_idx='".$row->opt2_idx."' AND optidxs='".$row->optidxs."' ";
				mysql_query($sql,get_db_conn());

				echo "<script>alert('필수 선택 옵션 항목이 있습니다.\\n옵션을 선택하신후 장바구니에\\n담으시기 바랍니다.');location.href=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\";</script>";
				exit;
			}
			$gift = $row->gift;
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
								$delsql = "DELETE FROM tblbasket2 WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
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
								$row->realprice+=($row->quantity*$exop[1]);
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
				} else if (strlen($row->opt1_idx)>0) {
					$option_price = $row->option_price;
					$pricetok=explode(",",$option_price);
					$priceindex = count($pricetok);
					$sellprice=$pricetok[$row->opt1_idx-1]+$price_package_listtmp[$row->productcode][$row->package_idx];
					$price = $sellprice*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$sellprice,"N");
				}
			} else {

				//######### 옵션에 따른 가격 변동 체크 ###############
				if (strlen($row->option_price)==0) {
					$price = $row->realprice;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"N");
					$sellprice=$row->sellprice;
				} else if (strlen($row->opt1_idx)>0) {
					$option_price = $row->option_price;
					$pricetok=explode(",",$option_price);
					$priceindex = count($pricetok);
					$price = $pricetok[$row->opt1_idx-1]*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$pricetok[$row->opt1_idx-1],"N");
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
				<td class="tdstyle2" align="center" valign="middle">
<?
			if($row->img_type==1) {
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					echo "<div style=\"width:{$width[0]}px; height:{$width[1]}px;background:url(".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage).");text-align:center;padding-top:30px;\" ><b><font color=white>".number_format($row->consumerprice)."원</b></div>";
				}
				else {
					echo "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\" >";
				}
			}
			else {
				if(strlen($row->tinyimage)!=0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)){
					$file_size=getImageSize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					echo "<img src=\"".$Dir.DataDir."shopimages/product/".$row->tinyimage."\" style=\"width:{$width[0]}px; height:{$width[1]}px;\"";
					echo " border=\"0\" vspace=\"1\">";
				} else {
					echo "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" vspace=\"1\">";
				}
			}
?></td>
				<td class="tdstyle">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td style="padding-left:2px;word-break:break-all;"><a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode?>"><font color="#000000"><b><?=viewproductname($productname,$row->etctype,$row->selfcode,$row->addcode) ?></b>
					<? if(_array($cateAuth)){?>
					</br>
					<?=implode($cateAuth);?>
					<?}?>

					<?=$bankonly_html?><?=$setquota_html?><?=$deli_str?></font></td>
				</tr>
<?			if (strlen($row->option1)>0 || strlen($row->option2)>0 || strlen($optvalue)>0) {
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
				<? if ($_data->reserve_maxuse>=0 && strlen($_ShopInfo->getMemid())>0) { ?>
				<td class="tdstyle" align="center"><? echo number_format($tempreserve) ?>원</td>
				<? } else { ?>
				<td class="tdstyle" align="center">없음</td>
				<? } ?>
				<td class="tdstyle" align="center"><?=number_format($sellprice)?>원</td>
				<td class="tdstyle" align="center"><font color="#333333"><?=$row->quantity?>개</font></td>
				<td class="tdstyle2" align="center"><font color="#F02800"><? echo number_format($price) ?>원</font></td>
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
		}
		mysql_free_result($result);
?>
		</tbody>
<?
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
		echo "	<td colspan=\"6\" bgcolor=\"#f9f9f9\" style=\"padding:15px 10px; text-align:right;\"><b>합계 :</b> <span style=\"color:#ff6600; font-size:15px; font-family:tahoma; font-weight:bold;\">".number_format($vender_sumprice)."원</span></td>\n";
		echo "</tr>\n";
		echo "<tr><td colspan=6 height=1 bgcolor=\"#dddddd\"></td></tr>\n";
	}
	mysql_free_result($res);

	#무이자 상품과 일반 상품이 주문할 경우
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
		echo "	<td colspan=6 align=right style=\"padding:15px 0px;\">\n";
		echo "	<table border=0 cellpadding=0 cellspacing=0>\n";
		echo "	<tr>\n";
		echo "		<td align=right><B>상품 합계금액 : </B></td>\n";
		echo "		<td align=right style=\"padding-right:15\"><span class=\"basket_etc_price\"><B>".number_format($sumprice)."원</B></span></td>\n";
		if($_data->ETCTYPE["VATUSE"]=="Y") {
			$sumpricevat = return_vat($sumprice);
			echo "		<td align=right><B>부가세(VAT) 합계금액 :</B></td>\n";
			echo "		<td align=right style=\"padding-right:15\"><B>+ ".number_format($sumpricevat)."원</B></td>\n";
		}
		if($deli_price>0) {
			echo "		<td align=right><B>배송비 합계금액 : </B></td>\n";
			echo "		<td align=right style=\"padding-right:15\"><B>+ ".number_format($deli_price)."원</B></td>\n";
		}
		/*if($salemoney>0) {
			echo "	<tr>\n";
			echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15\"><img src=\"".$Dir."images/common/group_orderimg.gif\" align=absmiddle>&nbsp;&nbsp;<b><font color=#FF3C00>".$group_name." 추가 할인</FONT></b></td>\n";
			echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15\"><FONT COLOR=\"#FF3C00\"><B>- ".number_format($salemoney)."원</B></FONT></td>\n";
			echo "	</tr>\n";
		}*/
		echo "		<td align=right><B>총 결제금액 : </B></td>\n";
		//echo "		<td align=right bgcolor=#ffffff style=\"padding-right:15;font-size:17\"><FONT COLOR=\"#EE1A02\"><B>".number_format($sumprice+$deli_price+$sumpricevat-$salemoney)."원</B></FONT></td>\n";
			echo "		<td align=right style=\"padding-right:15;font-size:17\"><span class=\"basket_etc_price3\"><B>".number_format($sumprice+$deli_price+$sumpricevat)."원</B></span></td>\n";
		if($reserve>0 && $_data->reserve_maxuse>=0 && strlen($_ShopInfo->getMemid())>0) {
			echo "	<td align=right style=\"padding-right:15\"><B>적립금 :</B></td>\n";
			echo "	<td align=right style=\"padding-right:15\"><<span class=\"basket_etc_price\"><B>".number_format($reserve)."원</B></span></td>\n";
		}

		if($salereserve>0) {
			echo "		<td align=right style=\"padding-right:15\"><img src=\"".$Dir."images/common/group_orderimg.gif\" align=absmiddle>&nbsp;&nbsp;<b><font color=#0000FF>".$group_name." 추가 적립</FONT></b></td>\n";
			echo "		<td align=right style=\"padding-right:15\"><FONT COLOR=\"#0000FF\"><B>".number_format($salereserve)."원</B></FONT></td>\n";
		}
		//echo "<tr><td colspan=2 height=1 bgcolor=\"#dddddd\"></td></tr>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";

	} else {
		echo "<tr height=25><td colspan=6 align=center>쇼핑하신 상품이 없습니다.</td></tr>\n";
		//echo "<tr><td colspan=6 height=1 bgcolor=\"#dddddd\"></td></tr>\n";
	}
?>
			</table>
			</td>
		</tr>
<?
if(strlen($_ShopInfo->getMemid())>0 && strlen($group_code)>0 && substr($group_code,0,1)!="M") {
	$arr_dctype=array("B"=>"현금","C"=>"카드","N"=>"");
?>
		<tr>
			<td height="10"></td>
		</tr>
		<!-- <tr>
			<td>
			<table border="0" cellpadding="0" cellspacing="8" width="100%" bgcolor="#E8E8E8" style="table-layout:fixed">
			<tr>
				<td background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_tbg.gif" style="padding:15px;">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center">
					<?if(file_exists($Dir.DataDir."shopimages/etc/groupimg_".$group_code.".gif")){?>
					<img src="<?=$Dir.DataDir?>shopimages/etc/groupimg_<?=$group_code?>.gif" border="0">
					<?}else{?>
					<img src="<?=$Dir?>images/common/group_img.gif" border="0">
					<?}?>
					</td>
					<td>
					<B><?=$name?></B>님은 <B><FONT COLOR="#EE1A02">[<?=$org_group_name?>]</FONT></B> 회원입니다.<br>
					<B><?=$name?></B>님이 <FONT COLOR="#EE1A02"><B><?=number_format($group_usemoney)?>원</B></FONT> 이상 <?=$arr_dctype[$group_payment]?>구매시,
<?
				if($group_type=="RW") echo "적립금에 ".number_format($group_addmoney)."원을 <font color=\"#EE1A02\"><B>추가 적립</B></font>해 드립니다.";
				else if($group_type=="RP") echo "구매 적립금의 ".number_format($group_addmoney)."배를 <font color=\"#EE1A02\"><B>적립</B></font>해 드립니다.";
				else if($group_type=="SW") echo "구매금액 ".number_format($group_addmoney)."원을 <font color=\"#EE1A02\"><B>추가 할인</B></font>해 드립니다.";
				else if($group_type=="SP") echo "구매금액의 ".number_format($group_addmoney)."%를 <font color=\"#EE1A02\"><B>추가 할인</B></font>해 드립니다.";
?>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
			</td>
		</tr> -->
		</table>
		</td>
	</tr>
	<tr><td height="20" colspan="2"></td></tr>
<?
} else {
?>
		</table>
		</td>
	</tr>
	<tr><td height="20" colspan="2"></td></tr>
<?
}

$is_sms="N";
$sql = "SELECT * FROM tblsmsinfo WHERE (mem_order='Y' OR mem_delivery='Y') ";
$result=mysql_query($sql,get_db_conn());
if($rows=mysql_num_rows($result)) {
	$is_sms="Y";
}
mysql_free_result($result);
?>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_title_t04.gif" border="0" vspace="3"></td>
							</tr>
							<tr><td height="2"></td></tr>
							<tr>
								<td>
									<table cellpadding="0" cellspacing="0" width="100%" class="orderTbl">
										<tr>
											<th>주문자이름</th>
											<td>
												<?
													if(strlen($_ShopInfo->getMemid())>0) {
														echo "<font color=\"000000\"><B>".$name."</B></font>";
														echo "<input type=hidden name=sender_name value=\"".$name."\">\n";
													} else {
														echo "<input type=text name=sender_name size=15 maxlength=12 class=\"input\" style=\"BACKGROUND-COLOR:#F7F7F7;\">\n";
													}
												?>
										</td>
									</tr>
									<tr>
										<th>전화번호</th>
										<td><input type=text name="sender_tel1" value="<?=$mobile[0] ?>" size="5" maxlength="3" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="sender_tel2" value="<?=$mobile[1] ?>" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="sender_tel3" value="<?=$mobile[2] ?>" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
									</tr>
									<tr>
										<th class="lastTh">이메일</th>
										<td class="lastTd"><input type="text" name="sender_email" value="<?=$email?>" class="input" style="width:98%; BACKGROUND-COLOR:#F7F7F7;"></td>
									</tr>
									<?
										if($gift==2) {
											echo "
												<input type='hidden' name='receiver_name' value='{$name}'>
												<input type='hidden' name='receiver_tel11' value='{$mobile[0]}'>
												<input type='hidden' name='receiver_tel12' value='{$mobile[1]}'>
												<input type='hidden' name='receiver_tel13' value='{$mobile[2]}'>
												<input type='hidden' name='raddr1' value='{$email}'>
												<input type='hidden' name='order_msg' value=''>
												<input type=hidden name=msg_type value=\"1\">
												<input type=hidden name=order_prmsg value=''>
											";
										} else {
									?>
									</table>
								</td>
							</tr>
						</table>
					</td>
					<td align="right" valign="top">
						<table cellpadding="0" cellspacing="0" width="98%">
							<tr>
								<td valign="bottom"><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_title_t05.gif" border="0" vspace="3" align="absmiddle"></td>
							</tr>
							<tr><td height="4"></td></tr>
							<tr>
								<td>
									<table cellpadding="0" cellspacing="0" width="100%" class="orderTbl">
										<tr>
											<th>이름</th>
											<td><input type=text name="receiver_name" size="15" maxlength="12" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
										</tr>
										<tr>
											<th>휴대폰번호</th>
											<td><input type=text name="receiver_tel11" size="5" maxlength="3" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="receiver_tel12" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="receiver_tel13" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
										</tr>
										<tr>
											<th class="lastTh">이메일</th>
											<td class="lastTd"><input type=text name="raddr1" value="" class="input" style="width:98%; BACKGROUND-COLOR:#F7F7F7;"></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-top:20px;">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td>
									<table border="0" width="100%" cellpadding="0" cellspacing="0" class="orderTbl">
<?
	if(count($arr_prlist)==1) {
		echo "<input type=hidden name=msg_type value=\"1\">\n";
		echo "<tr>\n";
		echo "	<th>전달세지<br />(50자내외)</th>\n";
		echo "	<td>\n";
		echo "		<textarea name=\"order_prmsg\" style=\"WIDTH:98%; HEIGHT:70px; padding:5px; line-height:17px; border:solid 1px #DFDFDF;\"></textarea>\n";
		echo "	</td>\n";
		echo "</tr>\n";
	} else {
		echo "<input type=hidden name=msg_type value=\"2\">\n";
		echo "<tr>\n";
		echo "	<td colspan=2 id=\"msg_idx2\" style=\"padding:0\">\n";
		echo "	<table border=0 cellpadding=3 cellspacing=0 width=100%>\n";
		echo "	<col width=83></col>\n";
		echo "	<col width=5></col>\n";
		echo "	<col width=></col>\n";

		$yy=0;
		while(list($key,$val)=each($arr_prlist)) {
			echo "<tr>\n";
			echo "	<th>주문메세지<br>&nbsp;&nbsp;(50자내외)";
			if($yy==0) {
				echo "<div align=center style=\"padding-top:5px\"><A HREF=\"javascript:change_message(1)\"><font color=red>[통합 입력]</font></A></div>";
			}
			echo "	</th>\n";
			echo "	<td><table border=0 cellpadding=0 cellspacing=0 height=100%><tr><td width=2 bgcolor=#eeeeee><img width=2 height=0></td></tr></table></td>\n";
			echo "	<td style=\"padding-left:5;word-break:break-all;\">\n";
			echo "	<FONT COLOR=\"#000000\"><B>상품명 :</B></FONT> ".$val."<BR>\n";
			echo "	<textarea name=\"order_prmsg".$yy."\" style=\"WIDTH:100%;HEIGHT:70px;padding:5px;line-height:17px;border:solid 1;border-color:#DFDFDF;font-size:9pt;color:333333;\"></textarea>\n";
			echo "	</td>\n";
			echo "</tr>\n";
			$yy++;
		}
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "	<td colspan=2 id=\"msg_idx1\" style=\"padding:0;display:none\">\n";
		echo "	<table border=0 cellpadding=3 cellspacing=0 width=100%>\n";
		echo "	<col width=83></col>\n";
		echo "	<col width=5></col>\n";
		echo "	<col width=></col>\n";
		echo "	<tr>\n";
		echo "		<th>주문메세지<br>&nbsp;&nbsp;(50자내외)";
		echo "		<div align=center style=\"padding-top:5px\"><A HREF=\"javascript:change_message(2)\"><font color=red>[상품별 입력]</font></A></div>";
		echo "		</th>\n";
		echo "		<td><table border=0 cellpadding=0 cellspacing=0 height=100%><tr><td width=2 bgcolor=#eeeeee><img width=2 height=0></td></tr></table></td>\n";
		echo "		<td style=\"padding-left:5\">\n";
		echo "			<textarea name=\"order_prmsg\" style=\"WIDTH:100%;HEIGHT:70px;padding:5px;line-height:17px;border:solid 1;border-color:#DFDFDF;font-size:9pt;color:333333;\"></textarea>\n";
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
	}
}
?>
				<?if(strlen($etcmessage[0])>0 || strlen($etcmessage[1])>0 || $etcmessage[2]=="Y") {?>
				<tr>
					<th>안내메세지</th>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
<?
		$tempmess="";
		if(strlen($etcmessage[1])>0){
			$day1=substr($etcmessage[1],0,2);
			$time1=substr($etcmessage[1],2,2);
			$time2=substr($etcmessage[1],4,2);
			$delidate=date("Ymd",mktime(0,0,0,date("m"),date("d")+$day1,date("Y")));
			$deliyear=substr($delidate,0,4);
			$delimon=substr($delidate,4,2);
			$deliday=substr($delidate,6,2);

			$tempmess.="<col width=\"140\"></col><col></col>\n";
			$tempmess.="<tr>\n";
			$tempmess.="	<td><b>희망 배송일자</b></td>\n";
			$tempmess.="	<td><input type=checkbox name=\"nowdelivery\" value=\"Y\" style=\"border:none;\">&nbsp;가능한 빨리 배송요망</td>\n";
			$tempmess.="</tr>\n";
			$tempmess.="<tr>\n";
			$tempmess.="	<td></td>\n";
			$tempmess.="	<td>&nbsp;<select name=\"year\" style=\"font-size:11px;\">";
			for($i=$deliyear;$i<=($deliyear+1);$i++) {
				$tempmess.="<option value=".$i;
				if($i==$deliyear) $tempmess.=" selected";
				$tempmess.=" style=\"#444444;\">".$i."\n";
			}
			$tempmess.="	</select>년 <select name=\"mon\" style=\"font-size:11px;\">";
			for($i=1;$i<=12;$i++) {
				$tempmess.="<option value=".$i;
				if($i==$delimon) $tempmess.=" selected";
				$tempmess.=" style=\"#444444;\">".$i."\n";
			}
			$tempmess.="	</select>월 <select name=\"day\" style=\"font-size:11px;\">";
			for($i=1;$i<=31;$i++) {
				$tempmess.="<option value=".$i;
				if($i==$deliday) $tempmess.=" selected";
				$tempmess.=" style=\"#444444;\">".$i."\n";
			}
			if(strlen($etcmessage[1])==6) {
				$tempmess.="	</select>일 <select name=\"time\" style=\"font-size:11px;\">";
				for($i=$time1;$i<$time2;$i++) {
					$value=($i<=12?"오전":"오후").$i."시 ~ ".(($i+1)<=12?"오전":"오후").($i+1)."시";
					$tempmess.="<option value='".$value."' style=\"#444444;\">".$value."\n";
				}
				$tempmess.="	</select></td>\n";
				$tempmess.="</tr>\n";
			} else {
				$tempmess.="	</select>일</td>\n";
				$tempmess.="</tr>\n";
			}
			$tempmess.="<tr><td colspan=\"2\" height=\"5\"></td></tr>\n";
			$tempmess.="<tr>\n";
			$tempmess.="	<td></td>\n";
			$tempmess.="	<td>&nbsp;<b>".$deliyear."</b>년 <b>".$delimon."</b>월 <b>".$deliday."</b>일 <font color=darkred>이후 날짜</font>를 입력하셔야 합니다.</td>\n";
			$tempmess.="</tr>\n";
			$tempmess.="<tr><td colspan=\"2\" height=\"5\"></td></tr>\n";
		}
		if($etcmessage[2]=="Y") {
			$tempmess.="<tr>\n";
			$tempmess.="	<td><font color=\"#0099CC\"><b>무통장 입금시 입금자명</b></font></td>\n";
			$tempmess.="	<td>&nbsp;<input type=\"text\" name=\"bankname\" size=\"10\" maxlength=\"10\" style=\"BACKGROUND-COLOR:#F7F7F7;\" class=\"input\"> (주문자와 같을경우 생략 가능)</td>\n";
			$tempmess.="</tr>\n";
			$tempmess.="<tr><td colspan=\"2\" height=\"5\"></td></tr>\n";
		}
		$tempmess.="<tr><td colspan=\"2\">".$etcmessage[0]."</td></tr>\n";

		echo $tempmess;
?>
						</table>
					</td>
				</tr>
				<?}?>
		</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
<?
if ((strlen($_ShopInfo->getMemid())>0 && $_data->reserve_maxuse>=0 && $user_reserve!=0) || (strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y")) {
?>
	<tr>
		<td style="padding:10x 0px; color:#f00;" colspan="2">* 전용 이용권 구매시에는 적립금 및 쿠폰 사용이 불가능합니다.</td>
	</tr>
	<tr><td colspan="2" height="20"></td></tr>
<?
}
?>
	<tr>
		<td colspan="2">
		<!-- 결제수단 선택 START -->
		<div style="float:left; width:100%;" id="orderPaySel">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="orderTbl" style="margin-top:30px;">
				<caption><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_title_t03.gif"></caption>
				<tr>
					<th class="lastTh">결제 수단 선택</th>
					<td class="lastTd">
						<?
							// 결제수단 - order.php    결제 수단 선택
							echo $payType;
						?>
					</td>
				</tr>
			</table>
		</div>

		<style>
			.paytype {border:1px solid #ddd; border-bottom:hidden; width:100%; margin-top:10px;}
			.paytype caption {display:none;}
			.paytype th {height:25px; padding-left:10px; color:#666; text-align:left; border-bottom:1px solid #ddd;}
			.paytype td {padding:6px 0px 6px 10px; border-bottom:1px solid #ddd;}

			.payTotal {float:right; margin-top:10px; border:1px solid #ddd; border-bottom:hidden;}
			.payTotal th {width:120px; padding-left:15px; background-color:#f5f5f5; font-size:11px; font-family:돋움; color:#666; text-align:left; border-right:1px solid #ddd; border-bottom:1px solid #ddd;}
			.payTotal td {padding:3px 10px; border-bottom:1px solid #ddd; text-align:right;}
		</style>

		<!-- 기본 안내 페이지 -->
		<div id="simg0" class="paytype">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" height="<?=$payHeight?>">
				<caption>결제 수단 선택</caption>
				<tr>
					<th><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0">결제수단을 선택해 주세요.</th>
				</tr>
				<tr>
					<td height="100%" class="paytext">
						- 결제 수단을 선택하신 후 아래의 <b>결제하기</b> 버튼을 클릭해 주시기 바랍니다.<br />
						- 주문자와 배송지 정보를 정확하게 입력하였는지 다시한번 확인해 주시기 바랍니다.
					</td>
				</tr>
			</table>
		</div>

		<!-- 무통장 입금 -->
		<div id="simg1" style="display:none;" class="paytype">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" height="<?=$payHeight?>">
				<caption>무통장 입금</caption>
				<tr>
					<th colspan="2"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0">무통장 입금</th>
				</tr>
				<tr>
					<th><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0">입금계좌 선택</th>
					<td>
						<? $arrpayinfo=explode("=",$_data->bank_account); ?>
							<select name="sel_bankinfo" class="st51_1_5">
								<option value=""><?=_empty($arrpayinfo[1])?'입금 계좌번호 선택 (반드시 주문자 성함으로 입금)':$arrpayinfo[1]?></option>
						<? if(!_empty($arrpayinfo[0])){
								$count = 0;
								$tok = strtok($arrpayinfo[0],",");
								while($tok){ ?>
								<option value="<?=$tok?>" ><?=$tok?></option>
						<?			$tok = strtok(",");
									$count++;
								} // end while
							} // end if
						?>
					</select>
					</td>
				</tr>
				<tr>
					<th><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0">입금자명</th>
					<td><input type="text" name="bankname" value="" size="12" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> <font color="#999999" style="font-size:11px; letter-spacing:-0.5px;">(주문자와 같을경우 생략하셔도 됩니다.)</font></td>
				</tr>
				<tr>
					<td colspan="2" height="100%" class="paytext">
						- 무통장 입금의 경우 <FONT COLOR="#EE1A02">입금확인 후 </font> 배송처리가 진행되며, 안전하고 빠르게 상품을 배송합니다.
					</td>
				</tr>
			</table>
		</div>

		<!-- 카드결제 -->
		<div id="simg2" style="display:none;" class="paytype">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" height="<?=$payHeight?>">
				<caption>신용카드</caption>
				<tr>
					<th><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0">신용카드</th>
				</tr>
				<tr>
					<td height="100%" class="paytext">
						- 신용카드 정보가 상점에 남지 않으며, 128bit SSL로 암호화된 결제창이 새로 뜹니다.<br />
						- 결제 후, 카드명세서에 [<FONT COLOR="#EE1A02">결제대행사명</font>]으로 표시됩니다!
						</span>
					</td>
				</tr>
			</table>
		</div>
		</td>
	</tr>

<?if(strlen($_ShopInfo->getMemid())==0) {?>
	<tr>
		<td valign="top" style="padding-right:10px;padding-top:23px;">
		<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" height="100%">
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimg05.gif" border="0"></TD>
		</TR>
		<TR>
			<TD height="100%" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgbg.gif"></TD>
		</TR>
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgdown.gif" border="0"></TD>
		</TR>
		</TABLE>
		</td>
		<td valign="top">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="bottom"><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_stitle5.gif" border="0" vspace="3" align="absmiddle"></td>
		</tr>
		<tr>
			<td height="2"></td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_t1.gif" border="0"></td>
				<td width="100%" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_t1bg.gif"></td>
				<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_t4.gif" border="0"></td>
			</tr>
			<tr>
				<td background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_t2bg.gif"></td>
				<td style="padding:10px;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="150"></col>
				<col></col>
				<tr>
					<td valign="top"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>비회원<br><img width=12 height=0>정보수집 동의</b></font></td>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="BORDER-RIGHT: #dfdfdf 1px solid; BORDER-TOP: #dfdfdf 1px solid; BORDER-LEFT: #dfdfdf 1px solid; BORDER-BOTTOM: #dfdfdf 1px solid" bgColor="#ffffff"><DIV style="PADDING:5px;OVERFLOW-Y:auto;OVERFLOW-X:auto;HEIGHT:100px"><?=$privercybody?></DIV></td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>
					<tr>
						<td align="center"><b><?=$_data->shopname?>의 <font color="#FF4C00">개인정보취급방침</FONT>에 동의하겠습니까?</b></td>
					</tr>
					<tr>
						<td align="center" style="padding-top:5px;"><input type=radio id=idx_dongiY name=dongi value="Y" style="border:none"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_dongiY><b><font color="#0099CC">동의합니다.</font></b></label><img width=10 height=0><input type=radio id="idx_dongiN" name=dongi value="N" style="border:none"><label style='cursor:hand;' onMouseOver="style.textDecoration='underline'" onMouseOut="style.textDecoration='none'" for=idx_dongiN><b><font color="#0099CC">동의하지 않습니다.</font></b></label></td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
				<td background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_t4bg.gif"></td>
			</tr>
			<tr>
				<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_t2.gif" border="0"></td>
				<td width="100%" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_t3bg.gif"></td>
				<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_t3.gif" border="0"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="20"></td>
	</tr>
<?}?>

						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="20"></td>
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
if($sumprice<$_data->bank_miniprice) {
	echo "<script>alert('주문 가능한 최소 금액은 ".number_format($_data->bank_miniprice)."원 입니다.');location.href='".$Dir.FrontDir."basket2.php';</script>";
	exit;
} else if($sumprice<=0) {
	echo "<script>alert('상품 총 가격이 0원일 경우 상품 주문이 되지 않습니다.');location.href='".$Dir.FrontDir."basket2.php';</script>";
	exit;
}

//if(strlen($_ShopInfo->getMemid())>0) echo "<script>document.form1.addrtype[0].checked=true;addrchoice();</script>";
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
<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {?>
<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<?}?>
<input type=hidden name=gift value="<?=$gift?>">
</form>

<form name=couponform action="<?=$Dir.FrontDir?>coupon.php" method=post target=couponpopup>
<input type=hidden name=sumprice value="<?=$sumprice?>">
</form>

<form name=orderpayform method=post action="<?=$Dir.FrontDir?>orderpay2.php" target=orderpaypop>
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
	if(document.form1.sender_tel1.value.length==0) {
		alert("주문자 전화번호를 입력하세요.");
		document.form1.sender_tel1.focus();
		return;
	}
	if(document.form1.sender_tel2.value.length==0) {
		alert("주문자 전화번호를 입력하세요.");
		document.form1.sender_tel2.focus();
		return;
	}
	if(document.form1.sender_tel3.value.length==0) {
		alert("주문자 전화번호를 입력하세요.");
		document.form1.sender_tel3.focus();
		return;
	}
	if(!IsNumeric(document.form1.sender_tel1.value)) {
		alert("주문자 전화번호 입력은 숫자만 입력하세요.");
		document.form1.sender_tel1.focus();
		return;
	}
	if(!IsNumeric(document.form1.sender_tel2.value)) {
		alert("주문자 전화번호 입력은 숫자만 입력하세요.");
		document.form2.sender_tel2.focus();
		return;
	}
	if(!IsNumeric(document.form1.sender_tel3.value)) {
		alert("주문자 전화번호 입력은 숫자만 입력하세요.");
		document.form3.sender_tel3.focus();
		return;
	}
	document.form1.sender_tel.value=document.form1.sender_tel1.value+"-"+document.form1.sender_tel2.value+"-"+document.form1.sender_tel3.value;

	if(document.form1.sender_email.value.length>0) {
		if(!IsMailCheck(document.form1.sender_email.value)) {
			alert("주문자 이메일 형식이 잘못되었습니다.");
			document.form1.sender_email.focus();
			return;
		}
	}
<? if($gift!= 2) {?>
	if(document.form1.receiver_name.value.length==0) {
		alert("선물 받을실 분 성함을 입력하세요.");
		document.form1.receiver_name.focus();
		return;
	}
	if(!chkNoChar(document.form1.receiver_name.value)) {
		alert("선물 받을실 분 성함에 \\(역슬래쉬) ,  '(작은따옴표) , \"(큰따옴표)는 입력하실 수 없습니다.");
		document.form1.receiver_name.focus();
		return;
	}
	if(document.form1.receiver_tel11.value.length==0) {
		alert("선물 받을실 분 핸드폰번호를 입력하세요.");
		document.form1.receiver_tel11.focus();
		return;
	}
	if(document.form1.receiver_tel12.value.length==0) {
		alert("선물 받을실 분 핸드폰번호를 입력하세요.");
		document.form1.receiver_tel12.focus();
		return;
	}
	if(document.form1.receiver_tel13.value.length==0) {
		alert("선물 받을실 분 핸드폰번호를 입력하세요.");
		document.form1.receiver_tel13.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel11.value)) {
		alert("선물 받을실 분 핸드폰번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_tel11.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel12.value)) {
		alert("선물 받을실 분 핸드폰번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_tel12.focus();
		return;
	}
	if(!IsNumeric(document.form1.receiver_tel13.value)) {
		alert("선물 받을실 분 핸드폰번호 입력은 숫자만 입력하세요.");
		document.form1.receiver_tel13.focus();
		return;
	}
	if(document.form1.raddr1.value.length==0) {
		alert("선물 받을실 분 이메일을 입력하세요.");
		document.form1.raddr1.focus();
		return;
	}

<? }?>
	document.form1.receiver_tel1.value=document.form1.receiver_tel11.value+"-"+document.form1.receiver_tel12.value+"-"+document.form1.receiver_tel13.value;
	/*if(paymethod.length==0) {
		orderpaypop();
		return;
	}*/

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

		document.form1.receiver_addr.value = document.form1.raddr1.value;
		if(document.form1.addorder_msg=="[object]") {
			if(document.form1.order_msg.value.length>0) document.form1.order_msg.value+="\n";
			document.form1.order_msg.value+=document.form1.addorder_msg.value;
		}
		document.form1.process.value="Y";
		document.form1.target = "PROCESS_IFRAME";

<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {?>
		document.form1.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>order2.php';
<?}?>

document.form1.submit();

		document.all.paybuttonlayer.style.display="none";
		document.all.payinglayer.style.display="block";

		//if(paymethod!="B") ProcessWait("visible");

	} else {
		ordercancel();
	}
}
function change_paymethod(val){
		for(i=0;i<=6;i++){
			if(document.getElementById("simg"+i)){
				document.getElementById("simg"+i).style.display = "none";
			}
		}
		document.getElementById("simg"+val).style.display = "block";
	}
//-->
</SCRIPT>

<DIV id="PAYWAIT_LAYER" style='position:absolute; left:50px; top:120px; width:503; height: 255; z-index:1; visibility: hidden'><a href="JavaScript:PaymentOpen();"><img src="<?=$Dir?>images/paywait.gif" align=absmiddle border=0 name=paywait galleryimg=no></a></DIV>
<!-- <IFRAME id="PAYWAIT_IFRAME" name="PAYWAIT_IFRAME" style="left:50px; top:120px; width:503; height: 255; position:absolute; visibility:hidden"></IFRAME> -->
<!--IFRAME id=PROCESS_IFRAME name=PROCESS_IFRAME style="display:''" width=100% height=300></IFRAME-->
<IFRAME id=PROCESS_IFRAME name=PROCESS_IFRAME style="display:none"></IFRAME>
<IFRAME id="PAY_PROCESS_IFRAME" name="PAY_PROCESS_IFRAME" style="display:none;POSITION: absolute; z-index:9999; border:5px solid #222222;" width="100%" frameborder="0"></IFRAME>
<?=$onload?>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>