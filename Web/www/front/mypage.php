<?
$Dir="../";

include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");
include_once($Dir."lib/ext/order_func.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('회원 아이디가 존재하지 않습니다.');location.href='".$Dir.FrontDir."login.php';\"></body></html>";exit;
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('처음부터 다시 시작하시기 바랍니다.');location.href='".$Dir.FrontDir."login.php';\"></body></html>";exit;
	}
	$url_id = $row->url_id;
}
mysql_free_result($result);



$selfcodefont_start = "<font class=\"prselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트 끝


// 쿠폰 정보
$cdate = date("YmdH");
if($_data->coupon_ok=="Y") {
	$sql = "SELECT COUNT(*) as cnt FROM tblcouponissue WHERE id='".$_ShopInfo->getMemid()."' AND used='N' AND (date_end>='".$cdate."' OR date_end='') ";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$coupon_cnt = $row->cnt;
	mysql_free_result($result);
} else {
	$coupon_cnt=0;
}



//주문현황 시작
$orderid = isset($_ShopInfo->memid)?trim($_ShopInfo->memid):"";

if(strlen($orderid) > 0){
	$oiSQL = "SELECT ";
	$oiSQL .= "COUNT(DISTINCT oi.ordercode) AS ordercount, "; // 주문현황 수
	$oiSQL .= "SUM(IF((oi.pay_admin_proc = 'Y' OR oi.pay_admin_proc = 'N') AND oi.deli_gbn = 'N',1,0)) AS delireadycount, "; // 발송준비 수
	$oiSQL .= "SUM(IF(oi.deli_gbn = 'Y', 1,0)) AS delicomplatecount, "; // 발송완료 수
	$oiSQL .= "SUM(IF(op.status = 'RA',1,0)) AS refundcount, "; //환불요청수
	$oiSQL .= "SUM(IF(op.status = 'RC',1,0)) AS repaymentcount "; //환불완료수
	$oiSQL .= "FROM tblorderinfo AS oi LEFT OUTER JOIN tblorderproduct AS op ON(oi.ordercode = op.ordercode) ";
	$oiSQL .= "WHERE oi.id = '".$orderid."' ";
	
	$ordercount = "";
	$deliready = "";
	$delicomplate = "";
	$refund = "";
	$repayment = "";

	if(false !== $oiRes = mysql_query($oiSQL, get_db_conn())){
		$oiNumRow = mysql_num_rows($oiRes);
		if($oiNumRow > 0){
			$oiRow = mysql_fetch_assoc($oiRes);

			// 주문현황,발송준비,발송완료는 전체주문수로 카운터 잡음
			// 환불신청,환불완료는 상품단위로 카운터 잡음
			$ordercount = $oiRow['ordercount']; // 주문현황 수
			$deliready = _empty($oiRow['delireadycount'])?0:$oiRow['delireadycount']; // 발송준비 수
			$delicomplate = _empty($oiRow['delicomplatecount'])?0:$oiRow['delicomplatecount']; // 발송완료 수
			$refund = _empty($oiRow['refundcount'])?0:$oiRow['refundcount']; // 환불요청수
			$repayment = _empty($oiRow['repaymentcount'])?0:$oiRow['repaymentcount']; // 환불요청수

			/*
			$delicomplate = $oiRow['delicomplatecount']; //발송완료 수
			$refund = $oiRow['refundcount']; //환불요청수
			$repayment = $oiRow['repaymentcount']; //환불요청수*/
		}

		mysql_free_result($oiRes);
	}
}
//주문현황 끝
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 마이페이지</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/jquery-1.4.2.min.js"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function OrderDetailPop(ordercode) {
	document.form2.ordercode.value=ordercode;
	window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
	document.form2.submit();
}

/*function OrderReview(ordercode,productcode){
	window.open("/front/review_writepop.php?ordercode="+ordercode+'&productcode='+productcode,"Review","height=200,width=500,scrollbars=no,resizable=no");
}*/

function OrderReview(productcode){

	var _form = document.reviewForm;
	var writepop = "reviewritepop";
	var url = "./prreview_write_pop.php";
	window.open(url,writepop,"height=550,width=500,scrollbars=no,resizable=no");
	_form.productcode.value=productcode;
	_form.target = writepop;
	_form.action = url;
	_form.submit();

}

function OrderDetailProduct(ordercode,productcode){
	document.location.href="/front/productdetail.php?productcode="+productcode;
}


function DeliSearch(deli_url){
	window.open(deli_url,"배송추적","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizeble=yes,copyhistory=no,width=800,height=550");
}
function DeliveryPop(ordercode) {
	document.form3.ordercode.value=ordercode;
	window.open("about:blank","delipop","width=600,height=370,scrollbars=no");
	document.form3.submit();
}
function ViewPersonal(idx) {
	window.open("about:blank","mypersonalview","width=600,height=450,scrollbars=yes");
	document.form4.idx.value=idx;
	document.form4.submit();
}

function addGiftcard(){
	window.open('/front/mypage_auth.php','addgiftcard','width=300,height=200');
	return;
}


function order_one_cancel(ordercode, productcode, can, tempkey,uid) {

	if (can=="yes") {
		if (confirm("주문취소가 완료되면 지급예정된 적립금 및 주문시 사용쿠폰이 모두 취소되며 취소된 주문건은 다시 되돌릴 수 없습니다")) {
		window.open("<?=$Dir.FrontDir?>order_one_cancel_pop.php?ordercode="+ordercode+"&productcode="+productcode+"&uid="+uid,"one_cancel","width=610,height=500,scrollbars=yes");
		}
	}else{
		if (confirm("입금확인중 주문은 '전체취소'만 가능합니다. \n전체취소를 원하시는 경우 구매를 원하는 상품을 다시 주문해주세요.\n이주문을 지금 주문 전체취소하시겠습니까?")) {

			document.form2.tempkey.value=tempkey;
			document.form2.type.value="cancel";

			document.form2.ordercode.value=ordercode;
			window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
			document.form2.submit();

			document.form2.tempkey.value="";
			document.form2.type.value="";

		}
		//alert("입금확인중 주문은 '전체취소'만 가능합니다. \n전체 취소 후 구매를 원하는 상품을 다시 주문하여 주십시오.");
	}
}

/*
function order_cancel(tempkey,ordercode,bankdate) {	//주문취소
	if (confirm("주문을 취소하시겠습니까?")) {

		document.form2.tempkey.value=tempkey;
		document.form2.type.value="cancel";

		document.form2.ordercode.value=ordercode;
		window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
		document.form2.submit();

		document.form2.tempkey.value="";
		document.form2.type.value="";
	}
}
*/

function order_cancel(tempkey,ordercode,bankdate) {	//주문취소
	if (confirm("주문을 취소하시겠습니까?")) {

		document.detailform.tempkey.value=tempkey;
		document.detailform.type.value="cancel_one";

		document.detailform.ordercode.value=ordercode;
		window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
		document.detailform.submit();

		document.detailform.tempkey.value="";
		document.detailform.type.value="";
	}
 }

function productAll(chk_name) {

	chk_all = document.getElementById(chk_name+"_all");

	chk = document.getElementsByName(chk_name);
	for(i=0;i<chk.length;i++) {
		chk[i].checked=chk_all.checked;
	}
}

function order_multi_cancel(ordercode, cnt, tempkey, bankdate) {

	chk_name= "chk_"+ordercode;
	chk_uid_name= "chk_uid_"+ordercode;

	productcode = "";
	uid = "";
	product_chk = 0;

	chk = document.getElementsByName(chk_name);
	chk_uid = document.getElementsByName(chk_uid_name);
	for(i=0;i<chk.length;i++) {
		if (chk[i].checked) {


			if (productcode=="") {
				productcode = chk[i].value;
			}else{
				productcode = productcode+"$$"+chk[i].value;
			}

			if (uid=="") {
				uid = chk_uid[i].value;
			}else{
				uid = uid+"$$"+chk_uid[i].value;
			}

			product_chk++
		}
	}

	if (product_chk==0) {
		alert("선택된 상품이 없습니다.");
	}else{

		if( chk.length == product_chk ) {
			order_cancel(tempkey,ordercode,bankdate);
		} else {
			if (confirm("주문취소가 완료되면 지급예정된 적립금 및 주문시 사용쿠폰이 모두 취소되며 취소된 주문건은 다시 되돌릴 수 없습니다")) {
				window.open("<?=$Dir.FrontDir?>order_one_cancel_pop.php?ordercode="+ordercode+"&productcode="+productcode+"&uid="+uid,"one_cancel","width=610,height=500,scrollbars=yes");
			}
		}
	}
}

//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
	include ($Dir.MainDir.$_data->menu_type.".php");
	include_once("./mypage_groupinfo.php"); //회원등급 출력용
?>

<!-- 마이페이지 상단 메뉴 START -->
<div class="mypagemembergroup">
	<div class="groupinfotext">안녕하세요? <strong class="st1"><?=$_ShopInfo->getMemname()?></strong>님. 회원님의 등급은 <strong class="st2"><?=$groupname?></strong>입니다.</span></div>
	<div class="gruopinfogo"><a href="/front/newpage.php?code=1">회원정책보기 &gt;</a></div>
</div>
<table border="0" cellpadding="0" cellspacing="0" class="mypagetmenu">
	<tr>
		<td class="nowMyage"><a href="/front/mypage.php">마이페이지</a></td>
		<td><a href="/front/mypage_orderlist.php">주문내역</a></td>
		<td><a href="/front/mypage_personal.php">1:1 문의</a></td>
		<td><a href="/front/mypage_reserve.php">적립금</a></td>
		<td><a href="/front/wishlist.php">찜하기</a></td>
		<td><a href="/front/mypage_coupon.php">쿠폰내역</a></td>
		<? if($_data->recom_url_ok == "Y" || $_data->sns_ok == "Y"){ ?><td><a href="/front/mypage_promote.php">홍보관리</a></td><? } ?>
		<? if(getVenderUsed()==true) { ?><td><a href="/front/mypage_custsect.php">단골매장</a></td><? } ?>
		<td><a href="/front/mypage_usermodify.php">회원정보</a></td>
		<td><a href="/front/mypage_memberout.php">회원탈퇴</a></td>
	</tr>
</table>
<div class="currentTitle">
	<div class="titleimage">마이페이지</div>
	<div class="current">홈 &gt; 마이페이지 &gt; <SPAN class="nowCurrent">마이페이지</span></div>
</div>
<!-- 마이페이지 상단 메뉴 END -->

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
$leftmenu="Y";
if($_data->design_mypage=="U") {
	$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='mypage'";
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
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/mypage_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/mypage_title.gif\" border=\"0\" alt=\"마이페이지\"></td>\n";
	} else {
		echo "<td>\n";
		/*
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/mypage_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/mypage_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/mypage_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		*/
		echo "</td>\n";
	}
	echo "</tr>\n";
}

echo "<tr>\n";
echo "	<td align=\"center\">\n";
include ($Dir.TempletDir."mypage/mypage".$_data->design_mypage.".php");
echo "<form name=form1 method=post action=\"".$_SERVER[PHP_SELF]."\">\n";
echo "	<input type=hidden name=ordgbn value=\"".$ordgbn."\">\n";
echo "	<input type=hidden name=type value=\"".$type."\">\n";
echo '</form>';
echo "	</td>\n";
echo "</tr>\n";
?>


<form name=form2 method=post action="<?=$Dir.FrontDir?>orderdetailpop.php" target="orderpop">
<input type=hidden name=ordercode>
<input type=hidden name=tempkey>
<input type=hidden name=type>
</form>
<form name=form3 method=post action="<?=$Dir.FrontDir?>deliverypop.php" target="delipop">
<input type=hidden name=ordercode>
</form>
<form name=form4 action="<?=$Dir.FrontDir?>mypage_personalview.php" method=post target="mypersonalview">
<input type=hidden name=idx>
</form>


	<form name=detailform method=post action="<?=$Dir.FrontDir?>orderdetailpop.php" target="orderpop">
		<input type=hidden name=ordercode>
		<input type=hidden name=tempkey>
		<input type=hidden name=type>
	</form>

</table>
<form name="reviewForm" method="post">
	<input type="hidden" name="productcode" value=""/>
</form>
<?=$onload?>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>
