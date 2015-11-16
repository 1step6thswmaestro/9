<?
$menu_myhome="".$Dir.FrontDir."mypage.php";
$menu_myorder="".$Dir.FrontDir."mypage_orderlist.php";
$menu_mypersonal="".$Dir.FrontDir."mypage_personal.php";
$menu_mywish="".$Dir.FrontDir."wishlist.php";
$menu_myreserve="".$Dir.FrontDir."mypage_reserve.php";
$menu_mycoupon="".$Dir.FrontDir."mypage_coupon.php";
$menu_myinfo="".$Dir.FrontDir."mypage_usermodify.php";
$menu_myout="".$Dir.FrontDir."mypage_memberout.php";
if(getVenderUsed()==true) { $menu_mycustsect=$Dir.FrontDir."mypage_custsect.php"; } 
if($_data->recom_url_ok == "Y" || $_data->sns_ok == "Y"){
	$menu_promote="".$Dir.FrontDir."mypage_promote.php";
}
$menu_gonggu="".$Dir.FrontDir."mypage_gonggu.php";
if(strpos($body,"[IFCOUPON]")!=0) {
	$ifcouponnum=strpos($body,"[IFCOUPON]");
	$endcouponnum=strpos($body,"[IFENDCOUPON]");
	$elsecouponnum=strpos($body,"[IFELSECOUPON]");

	$couponstartnum=strpos($body,"[FORCOUPON]");
	$couponstopnum=strpos($body,"[FORENDCOUPON]");

	$ifcoupon=substr($body,$ifcouponnum+10,$couponstartnum-($ifcouponnum+10))."[COUPONVALUE]".substr($body,$couponstopnum+14,$elsecouponnum-($couponstopnum+14));

	$nocoupon=substr($body,$elsecouponnum+14,$endcouponnum-$elsecouponnum-14);

	$maincoupon=substr($body,$couponstartnum,$couponstopnum-$couponstartnum+14);

	$body=substr($body,0,$ifcouponnum)."[ORIGINALCOUPON]".substr($body,$endcouponnum+13);
}

$sql = "SELECT a.coupon_code, a.coupon_name, a.sale_type, a.sale_money, a.bank_only, a.productcode, ";
$sql.= "a.mini_price, a.use_con_type1, a.use_con_type2, a.use_point, b.date_start, b.date_end ";
$sql.= "FROM tblcouponinfo a, tblcouponissue b ";
$sql.= "WHERE b.id='".$_ShopInfo->getMemid()."' ";
$sql.= "AND a.coupon_code=b.coupon_code AND b.date_start<='".date("YmdH")."' ";
$sql.= "AND (b.date_end>='".date("YmdH")."' OR b.date_end='') ";
$sql.= "AND b.used='N' ";
$result = mysql_query($sql,get_db_conn());
$cnt=0;
while($row=mysql_fetch_object($result)) {
	$tempcoupon.=$maincoupon;

	$codeA=substr($row->productcode,0,3);
	$codeB=substr($row->productcode,3,3);
	$codeC=substr($row->productcode,6,3);
	$codeD=substr($row->productcode,9,3);

	$prleng=strlen($row->productcode);

	$likecode=$codeA;
	if($codeB!="000") $likecode.=$codeB;
	if($codeC!="000") $likecode.=$codeC;
	if($codeD!="000") $likecode.=$codeD;

	if($prleng==18) $productcode[$cnt]=$row->productcode;
	else $productcode[$cnt]=$likecode;

	if($row->sale_type<=2) {
		$dan="%";
	} else {
		$dan="원";
	}
	if($row->sale_type%2==0) {
		$sale = "할인";
	} else {
		$sale = "적립";
	}
	
	if($row->productcode=="ALL") {
		$product="전체상품";
	} else {
		$product = "";
		$sql2 = "SELECT code_name FROM tblproductcode WHERE codeA='".substr($row->productcode,0,3)."' ";
		if(substr($row->productcode,3,3)!="000") {
			$sql2.= "AND (codeB='".substr($row->productcode,3,3)."' OR codeB='000') ";
			if(substr($row->productcode,6,3)!="000") {
				$sql2.= "AND (codeC='".substr($row->productcode,6,3)."' OR codeC='000') ";
				if(substr($row->productcode,9,3)!="000") {
					$sql2.= "AND (codeD='".substr($row->productcode,9,3)."' OR codeD='000') ";
				} else {
					$sql2.= "AND codeD='000' ";
				}
			} else {
				$sql2.= "AND codeC='000' ";
			}
		} else {
			$sql2.= "AND codeB='000' AND codeC='000' ";
		}
		$sql2.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
		$result2=mysql_query($sql2,get_db_conn());
		$i=0;
		while($row2=mysql_fetch_object($result2)) {
			if($i>0) $product.= " > ";
			$product.= $row2->code_name;
			$i++;
		}
		mysql_free_result($result2);

		if($prleng==18) {
			$sql2 = "SELECT productname as product FROM tblproduct ";
			$sql2.= "WHERE productcode='".$row->productcode."' ";
			$result2 = mysql_query($sql2,get_db_conn());
			if($row2 = mysql_fetch_object($result2)) {
				$product.= " > ".$row2->product;
			}
			mysql_free_result($result2);
		}
		if($row->use_con_type2=="N") $product="[".$product."] 제외";
	}
	$s_time=mktime((int)substr($row->date_start,8,2),0,0,(int)substr($row->date_start,4,2),(int)substr($row->date_start,6,2),(int)substr($row->date_start,0,4));
	$e_time=mktime((int)substr($row->date_end,8,2),0,0,(int)substr($row->date_end,4,2),(int)substr($row->date_end,6,2),(int)substr($row->date_end,0,4));

	$date=date("Y.m.d H",$s_time)."시 ~<br>".date("Y.m.d H",$e_time)."시";

	$coupon_code=$row->coupon_code;
	$coupon_sale="<font color=\"".($sale=="할인"?"#FF0000":"#0000FF")."\">".number_format($row->sale_money).$dan.$sale."</font>";
	$coupon_prname=$product;
	$coupon_name=$row->coupon_name;
	$coupon_price=($row->mini_price=="0"?"제한 없음":number_format($row->mini_price)."원 이상");
	$coupon_date1=date("Y.m.d H",$s_time)."시";
	$coupon_date2=date("Y.m.d H",$e_time)."시";
	$coupon_days=ceil(($e_time-$s_time)/(60*60*24))."일";

	$cnt++;

	$pattern=array("(\[COUPON_CODE\])","(\[COUPON_SALE\])","(\[COUPON_PRNAME\])","(\[COUPON_NAME\])","(\[COUPON_PRICE\])","(\[COUPON_DATE1\])","(\[COUPON_DATE2\])","(\[COUPON_DAYS\])","(\[FORCOUPON\])","(\[FORENDCOUPON\])");
	$replace=array($coupon_code,$coupon_sale,$coupon_prname,$coupon_name,$coupon_price,$coupon_date1,$coupon_date2,$coupon_days,"","");

	$tempcoupon=preg_replace($pattern,$replace,$tempcoupon);
}
mysql_free_result($result);


// 쿠폰 등록용 link
$authCoupon = '<a href="javascript:addOffCoupon();"><!-- 쿠폰등록 --><img src="/images/common/mycoupon/mycp_insert.gif" border="0" alt="쿠폰등록" /></a>';
array_push($pattern,'(\[AUTH_COUPONCODE\])');
array_push($replace,$authCoupon);




if($cnt>0) {
	$originalcoupon=$ifcoupon;
	$pattern=array("(\[COUPONVALUE\])");
	$replace=array($tempcoupon);
	$originalcoupon=preg_replace($pattern,$replace,$originalcoupon);
} else {
	$originalcoupon=$nocoupon;
}




$pattern=array(
	"(\[MENU_MYHOME\])",
	"(\[MENU_MYORDER\])",
	"(\[MENU_MYPERSONAL\])",
	"(\[MENU_MYWISH\])",
	"(\[MENU_MYRESERVE\])",
	"(\[MENU_MYCOUPON\])",
	"(\[MENU_MYINFO\])",
	"(\[MENU_MYOUT\])",
	"(\[MENU_MYCUSTSECT\])",
	"(\[MENU_PROMOTE\])",
	"(\[MENU_GONGGU\])",
	"(\[COUPON\])",
	"(\[ORIGINALCOUPON\])"
);

$replace=array($menu_myhome,$menu_myorder,$menu_mypersonal,$menu_mywish,$menu_myreserve,$menu_mycoupon,$menu_myinfo,$menu_myout,$menu_mycustsect,$menu_promote,$menu_gonggu,$coupon_cnt,$originalcoupon);

$body=preg_replace($pattern,$replace,$body);

echo $body;

?>