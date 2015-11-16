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



$receiver_name= "";
$receiver_email= "";
$receiver_tel11 = "";
$receiver_tel12 = "";
$receiver_tel13 = "";
$receiver_tel21 = "";
$receiver_tel22 = "";
$receiver_tel23 = "";
$receiver_zip1 = "";
$receiver_zip2 = "";
$receiver_addr1 = "";
$receiver_addr2 = "나머지 주소";

// 조르기 결제
$pstr=$_GET["pstr"];
if( strlen($pstr) > 3 ){

	$sql = "SELECT * FROM tblpesterinfo WHERE code='".$pstr."' AND state='0'";

	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {

		//_pr($row);

		$pester_code=$row->code;
		$pester_id=$row->id;
		$tempkey=$row->tempkey;
		$sender_name=$row->sender_name;
		$sender_tel=$row->sender_tel;
		$pester_state=$row->state;

		$receiver_name=$row->receiver_name;
		$receiver_email=$row->sender_email;
		$receiver_tel1 = explode("-",$row->receiver_tel1);
			$receiver_tel11 = $receiver_tel1[0];
			$receiver_tel12 = $receiver_tel1[1];
			$receiver_tel13 = $receiver_tel1[2];
		$receiver_tel2 = explode("-",$row->receiver_tel2);
			$receiver_tel21 = $receiver_tel2[0];
			$receiver_tel22 = $receiver_tel2[1];
			$receiver_tel23 = $receiver_tel2[2];
		$receiver_addr = explode("=",$row->receiver_addr);
			$receiver_zip = explode("-",$receiver_addr[0]);
				$receiver_zip1 = $receiver_zip[0];
				$receiver_zip2 = $receiver_zip[1];
			$receiver_addr1 = $receiver_addr[1];
			$receiver_addr2 = $receiver_addr[2];

		$sql2 = "SELECT group_code FROM tblmember WHERE id='".$pester_id."' ";
		$result2 = mysql_query($sql2);
		if($row2 = mysql_fetch_object($result2)) {
			$group_code=$row2->group_code;
		}
		mysql_free_result($result2);


		// 장바구니 복사 해오기
		$_COOKIE["basketauthkey"] = $tempkey;
		setcookie("basketauthkey", $tempkey, time()+3600, "/".RootPath, getCookieDomain());
		mysql_query("INSERT INTO tblbasket_pester_order SELECT * FROM tblbasket_pester_save WHERE tempkey='".$tempkey."'",get_db_conn());

		$ordertype = "pstr";
	}
	mysql_free_result($result);
	if(!$pester_code){
		echo "<script>alert('이미 구매하셨거나 판매 불가한 상품입니다.');if(parent){parent.location.href=\"/\";}else{location.href=\"/\";}</script>";
		exit;
	}


}


// 주문타입별 장바구니 테이블
$basket = basketTable($ordertype);



//회원전용일 경우 로긴페이지로...
if($_data->member_buygrant=="Y" && strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}




//장바구니 인증키 확인
if(strlen($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
	$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
}
if(strlen($_ShopInfo->getMemid()) > 0){
	$sql ="UPDATE tblbasket SET sell_memid ='' WHERE tempkey='".$_ShopInfo->getTempkey()."' AND sell_memid='".$_ShopInfo->getMemid()."'";
	mysql_query($sql,get_db_conn());
}



// 장바구니 데이터 (Array) ==================================================
$basketItems = getBasketByArray($basket);

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

	if(preg_match("/^(A|B|C|D|E|G)$/",$pg_type)) {
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
$sql.= "FROM tblbasket a, tblproduct b ";
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
					mysql_query("delete from tblbasket where a.tempkey='".$_ShopInfo->getTempkey()."' and productcode='".$row->productcode."'",get_db_conn()); // 삭제 처리
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
			$basketsql = "SELECT productcode,assemble_list,quantity,assemble_idx FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
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
			$sql = "SELECT opt1_idx, opt2_idx, quantity FROM tblbasket ";
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
		$home_post1=$row->home_post;
		$row->home_addr = ereg_replace("\"","",$row->home_addr);
		$home_addr = explode("=",$row->home_addr);
		$home_addr1 = $home_addr[0];
		$home_addr2 = $home_addr[1];

		$office_addr="";
		$office_post1=$row->office_post;
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


<SCRIPT LANGUAGE="JavaScript">
<!--
var coupon_limit = "<?=$_data->coupon_limit_ok?>";
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
		document.form1.raddr1.value='';
		document.form1.raddr2.value='';
	}
}


<?
	if(strlen($_ShopInfo->getMemid())>0){
?>
	// 회원일 경우 주소 선택
	function addrchoice() {
		if(document.form1.addrtype[0].checked==true) { // 자택
			document.form1.rpost1.value="<?=$home_post1?>";
			document.form1.raddr1.value="<?=$home_addr1?>";
			document.form1.raddr2.value="<?=$home_addr2?>";
		} else if(document.form1.addrtype[1].checked==true) { // 회사
			document.form1.rpost1.value="<?=$office_post1?>";
			document.form1.raddr1.value="<?=$office_addr1?>";
			document.form1.raddr2.value="<?=$office_addr2?>";
		} else if(document.form1.addrtype[2].checked==true) { // 최근배송지
			window.open("<?=$Dir.FrontDir?>addrbygone.php","addrbygone","width=100,height=100,toolbar=no,menubar=no,scrollbars=yes,status=no");
		}
	}

	// 적립금 체크
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
<?
	}
?>

// 주소 검색창
function get_post( t ) {
	window.open("<?=$Dir.FrontDir?>addr_search.php?form=form1&post="+t+"post&addr="+t+"addr1&gbn=2","f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");
}


// 쿠폰다운로드
function issue_coupon(coupon_code,productcode){
	location.href="?mode=coupon&coupon_code="+coupon_code+"&productcode="+productcode;
}


// 쿠폰선택 ( offlinecoupon : 오프라인쿠폰등록 )
function coupon_check( offlinecoupon ){
	resetCoupon();

	var offlinecouponURL = "";
	offlinecouponURL = "?ordertype=<?=$ordertype?>";		//2015-04-29 바로구매시 쿠폰적용이 불가능한 부분 수정
	if( offlinecoupon == "offlinecoupon" ) {
		offlinecouponURL = "?offlinecoupon=popup";
	}
	window.open("/front/couponpop.php"+offlinecouponURL,"couponpopup","width=720,height=750,toolbar=no,menubar=no,scrollbars=yes,status=no");
}


// 주문취소
function ordercancel(gbn) {
	if(gbn=="cancel" && document.form1.process.value=="N") {
		document.location.href="basket.php";
	} else {
		if (PROCESS_IFRAME.chargepop) {
			if (gbn=="cancel") alert("결제창과 연결중입니다. 취소하시려면 결제창에서 취소하기를 누르세요.");
		} else {
			PROCESS_IFRAME.PaymentOpen();
		}
	}
}


function PaymentOpen() {
	PROCESS_IFRAME.PaymentOpen();
}

//-->
</SCRIPT>

<?

$mingiftprice = 0;
if(false !== $gres = mysql_query("select min(gift_startprice) from tblgiftinfo",get_db_conn())){
	if(mysql_num_rows($gres)) $mingiftprice = mysql_result($gres,0,0);
}

?>





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
		
		//우편번호 갱신 유도
		$j('input[name="rpost1"]').val('');
		if($j('input[name="spost1"]').val().length >=6){
			if(confirm("우편번호가 갱신하지 않았습니다.\n우편번호를 변경하시겠습니까?")){
				document.location.replace("/front/mypage_usermodify.php");
			}
		}
		//우편번호 갱신 유도 끝
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
			} else if(groupCode == 'SP'){
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


<?
// PG모듈 호출
if( strlen($_data->card_id)>0 AND substr($ordertype,0,6) != "pester" ) {
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
	
	//_pr($pgInfo);
	if($pgInfo["PG"] == "G"){
		echo '<script language=JavaScript charset="euc-kr" src="https://tx.allatpay.com/common/AllatPayRE.js"></script>';
	}
}
?>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>


<!-- 주문 처리용 아이프레임 -->
<IFRAME id="PROCESS_IFRAME" name="PROCESS_IFRAME" style="display:none;" width="100%" height="700"></IFRAME>
<IFRAME id="PAY_PROCESS_IFRAME" name="PAY_PROCESS_IFRAME" style="display:none; POSITION: absolute; z-index:9999; border:5px solid #222222;" width="100%" frameborder="0"></IFRAME>


<?
	if(substr($_data->design_order,0,1)=="T") {
		$_data->menu_type="nomenu";
	}

	include ($Dir.MainDir.$_data->menu_type.".php");


	#무이자 상품과 일반 상품이 주문할 경우
	if($basketItems['productcnt']!=$basketItems['productcnt'] && $basketItems['productcnt']>0 && $_data->card_splittype=="O") {
		echo "<script> alert('[안내] 무이자적용상품과 일반상품을 같이 주문시 무이자할부적용이 안됩니다.');</script>";
	}

	if($basketItems['sumprice']<$_data->bank_miniprice) {
		echo "<script>alert('주문 가능한 최소 금액은 ".number_format($_data->bank_miniprice)."원 입니다.');location.href='".$Dir.FrontDir."basket.php';</script>";
		exit;
	} else if($basketItems['sumprice']<=0) {
		echo "<script>alert('상품 총 가격이 0원일 경우 상품 주문이 되지 않습니다.');location.href='".$Dir.FrontDir."basket.php';</script>";
		exit;
	}
?>


<form name=form1 action="<?=sprintf($Dir.FrontDir."%s.php", ((substr($ordertype,0,6)== "pester")? "pestersend":(($socialshopping == "social")? "ordersend3":"ordersend")) )?>" method=post>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<?
			if ($leftmenu!="N") {
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
	<tr><td height="20"></td></tr>
	<tr>
		<td align=center>
			<div id="paybuttonlayer" name="paybuttonlayer" style="height:43px; display:block;">
				<? if(substr($ordertype,0,6) == "pester"){?>
					<A HREF="javascript:CheckForm()" onMouseOver="window.status='조르기';return true;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/btn_pester.gif" border="0" align="absmiddle" alt="조르기" /></A>
				<?}else{?>
					<A HREF="javascript:CheckForm()" onMouseOver="window.status='결제';return true;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/btn_payment.gif" border="0" align="absmiddle" alt="결제" /></A>
				<?}?>
				<A HREF="javascript:ordercancel('cancel')" onMouseOver="window.status='취소';return true;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/btn_cancel.gif" border="0" align="absmiddle" /></A>
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
	<tr><td height="20"></td></tr>
</table>

<?
	if( strlen($_ShopInfo->getMemid())>0 && $ordertype !='present' && strlen($pstr) == 0 ) echo "<script>document.form1.addrtype[0].checked=true;addrchoice();</script>";
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

<input type=hidden name=ordertype value="<?=$ordertype?>">

<input type="hidden" name="tax_free" value="<?=$basketItems['tax_free']?>" />

<!-- 사은품 사용가능 금액 -->
<input type="hidden" name="possible_gift_price" id="possible_gift_price" value="<?=$basketItems['gift_price']?>" />
<!-- 사은품 사용가능 여부 (Y/N) -->
<input type="hidden" name="possible_gift_price_used" id="possible_gift_price_used" value="Y" />
<!-- 회원 등급 할인 혜택 여부 (Y/N) -->
<input type="hidden" name="possible_group_dis_used" id="possible_group_dis_used" value="Y" />


<!-- 주문메세지 타입 -->
<input type="hidden" name="msg_type" value="1" />

<input type="hidden" name="addorder_msg" value="">

<!-- 쿠폰 할인 정보 -->
<input type="hidden" name="basketTempList" id="basketTempList" value="">

<!-- 회원그룹(추가)할인 -->
<input type="hidden" name="groupdiscount" id="groupdiscount" value="0">




<input type=hidden name=process value="N">
<input type=hidden name=pay_data1>
<input type=hidden name=pay_data2>
<input type=hidden name=sender_resno>
<input type=hidden name=sender_tel>
<input type=hidden name=receiver_tel1>
<input type=hidden name=receiver_tel2>
<input type=hidden name=receiver_addr>
<input type=hidden name=order_msg>
<input type=hidden name=pester_tel>



<?
	if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ORDER"]=="Y") {
?>
	<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<?
	}
?>
</form>


<?

/*$replaceQueryString=$queiryStirng="";
parse_str($_SERVER['QUERY_STRING'],$queiryStirng);
if(_array($queiryStirng)) $replaceQueryString = '&'.http_build_query($queiryStirng);
*/
//$replaceURL = "/front/order.php".$replaceQueryString;
$replaceURL = "/front/basket.php";
?>




<SCRIPT LANGUAGE="JavaScript">
<!--
	function CheckData(){
		alert("원 거래건이 없거나, 위/변조된 거래로 거래가 취소되었습니다.");
		location.replace("<?=$replaceURL?>");
		return;
	}
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
		// 우편번호 갱신 관련
		if(document.form1.spost1.value.length <=0 || document.form1.spost1.value.length>=6) {
			alert("주문자 우편번호 입력이 옳바르지 않습니다.");
			get_post('s');
			return;
		}
		// 우편번호 갱신 관련 끝

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

		//우편번호 갱신 관련
		if(document.form1.rpost1.value.length <=0 || document.form1.rpost1.value.length>=6) {
			alert("우편번호 입력이 옳바르지 않습니다.");
			get_post('r');
			return;
		}
		//우편번호 갱신 관련 끝

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
		if($j('#gift01')){
			var checkGift = parseInt($j('#gift01').val());
			if($j("select[name=giftval_seq]") && $j("select[name=giftval_seq]").find('option').size() > 1){
				var $gift = $j("select[name=giftval_seq] option:selected");
				var index =$j("select[name=giftval_seq] option").index($gift);

				if(index < 1){
					alert('사은품을 선택하시기 바랍니다.');
					$j("select[name=giftval_seq]").focus();
					return;
				}
			}
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
			
			//우편번호 갱신
			document.form1.receiver_addr.value = "우편번호 : " + document.form1.rpost1.value +"\n주소 : " + document.form1.raddr1.value + "  " + document.form1.raddr2.value;
			//우편번호 갱신

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

					//지역별 추가배송료 확인
					<?
					/*
						echo "address = \" \"+document.form1.raddr1.value;\n";
						$array_deli = explode("|",$_data->deli_area);
						$cnt= floor(count($array_deli)/2);
						for($i=0;$i<$cnt;$i++){
							$subdeli=explode(",",$array_deli[$i*2]);
							$subcnt=count($subdeli);
							echo "if(";
							for($j=0;$j<$subcnt;$j++){
								if($j!=0) echo " || ";
								echo "address.indexOf(\"".$subdeli[$j]."\")>0";
							}
							echo "){ if(!confirm('";
							if($array_deli[$i*2+1]>0) echo "해당 지역은 배송료 ".number_format($array_deli[$i*2+1])."원이 추가됩니다.";
							else echo "해당 지역은 배송료 ".number_format(abs($array_deli[$i*2+1]))."원이 할인됩니다.";
							echo "')) return;}\n";
						}
					*/
					?>
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

	secGifts = "";
	function secGift(vls) {
		f = document.form1;

		$("gift_"+secGifts).style.display = "none";
		tmp = eval("f.img_"+secGifts);
		$("gift_img").src = tmp.value;

		$("gift_"+vls).style.display = "block";
		tmp = eval("f.img_"+vls);
		$("gift_img").src = tmp.value;

		secGifts = vls;

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
//-->
</SCRIPT>




<?=$onload?>


<? include ($Dir."lib/bottom.php") ?>







<Script>
	/******************************
		사은품
	******************************/
	$j(function(){
		$j('select[name=giftval_seq]').change( function(){ resetGiftOptions();});
	});

	// 적용 가능 사은품 가져 오기
	function giftchoices(gprice){
		var tempgprice = parseInt($j('input[name=gift01]').val()); // 적용전 (현) 사은품 지급가능 구매금액
		gprice = parseInt(gprice); // 적용될 사은품 지급가능 구매금액
		var noGift = ($j('input[name=possible_gift_price_used]').val() == 'N');
		if(!noGift){
			if(isNaN(gprice)) gprice = tempgprice;
			if(isNaN(gprice) || gprice < 1) gprice = 0;
		}else{
			gprice = 0;
		}

		// 사은품 지급가능 구매금액에 변동이 없고 사은품이 선택 되어 있을경우 사은품 변동 안함
		var index = $j("select[name=giftval_seq] option").index( $j("select[name=giftval_seq] option:selected") );
		if( tempgprice == gprice && index > 0 ) {
			return false;
		}

		$j('input[name=gift01]').val(gprice);
		if(gprice >= mingiftprice){
			if($j('#giftSelectArea')) $j('#giftSelectArea').css('display','');
			$j.post( '/json_order.php',{'act':'getGife','gift_price':gprice},function(data){
				if(data.err == 'ok'){
					giftReset(data.items);
				}else{
					alert(data.err);
				}
			},'json');
		}else{

			if($j('#giftSelectArea')) $j('#giftSelectArea').css('display','none');
			$j('#noGiftOptionArea').css('display','');
			$j('#giftOptionBox').css('display','none');
			$j('input[name=gift_msg]').attr('disabled','disabled');
		}
	}

	// 사은품 초기화
	function giftReset(items){
		$j('select[name=giftval_seq]').find('option:gt(0)').remove();
		resetGiftOptions();

		if($j(items).size() < 1){

			if($j('#giftSelectArea')) $j('#giftSelectArea').css('display','none');

			$j('#noGiftOptionArea').css('display','');
			$j('#giftOptionBox').css('display','none');
			$j('input[name=gift_msg]').attr('disabled','disabled');
		}else{
			if($j('#giftSelectArea')) $j('#giftSelectArea').css('display','');
			$j('#noGiftOptionArea').css('display','none');
			$j('#giftOptionBox').css('display','');
			if($j('input[name=gift_msg]').attr('disabled'))  $j('input[name=gift_msg]').removeAttr('disabled');
			if($j.isArray(items)){
				$j(items).each(function(idx,itm){
					addGiftSelSelect(itm);
				});
			}else{
				$j(items).each(function(idx,itm){
					for(p in itm) addGiftSelSelect(itm[p]);
				});
			}
		}
	}

	// 사은품 옵션 초기화
	function resetGiftOptions(){
		var $gift = $j("select[name=giftval_seq] option:selected");
		var index =$j("select[name=giftval_seq] option").index($gift);
		$j('#giftOptionArea').html('');

		if($j.trim($j($gift).data('imgsrc')).length < 1){
			$j('#gift_img').attr('src',"/images/no_img.gif");
		}else{
			$j('#gift_img').attr('src','<?=$Dir?>data/shopimages/etc/'+$j($gift).data('imgsrc'));
		}

		if(index > 0){
			$items = $j($gift).data('options');
			//alert($j($items).size());
			if($j($items).size() >0){
				var str = '<table border="0" cellpadding="0" cellspacing="0" style="width:100%">';
				if($j.isArray($items)){
					$j($items).each(function(idx,itm){
						str += '<tr><td style="width:50px;">옵션 '+(idx+1)+' :</td>';
						str += '<td><select name="giftOpt'+(idx+1)+'" style="width:90%">';
						$name = itm.name;

						$j(itm.items).each(function(idx,sitm){
							str += '<option value="'+sitm[0]+'">'+$name+' : '+sitm[0]+'</option>';
						});
						str += '</select></td></tr>';
					});
				}else{
					$j($items).each(function(idx,oitm){
						for(p in oitm){
							itm = oitm[p];
							str += '<tr><td style="width:50px;">옵션 '+(p)+' :</td>';
							str += '<td><select name="giftOpt'+(p)+'" style="width:90%">';
							$name = itm.name;
							$j(itm.items).each(function(idx,sitm){
								/*
								for(q in sitm){
									alert(q);
									str += '<option value="'+sitm[q]+'">'+$name+' : '+sitm[q]+'</option>';
								}*/
								str += '<option value="'+sitm[0]+'">'+$name+' : '+sitm[0]+'</option>';
							});
							str += '</select></td></tr>';
						}
					});
				}
				str += '</table>';
				$j('#giftOptionArea').html(str);
				//alert(str);
			}
		}
	}

	// 사은품 선택
	function addGiftSelSelect(itm){
		$j('<option value="'+itm.gift_regdate+'">'+itm.gift_name+'</option>').data('imgsrc',itm.gift_image).data('options',itm.options).appendTo("select[name=giftval_seq]");

	}
</script>
</BODY>
</HTML>