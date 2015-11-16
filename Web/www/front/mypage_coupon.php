<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

if($_data->coupon_ok!="Y") {
	echo "<html><head><title></title></head><body onload=\"alert('본 쇼핑몰에서는 쿠폰 기능을 지원하지 않습니다.');location.href='".$Dir.FrontDir."mypage.php'\"></body></html>";exit;
}

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

?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 쿠폰 보유내역</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function addOffCoupon(){
	window.open('/front/offlinecoupon_auth.php','OffLineCoupon','width=300,height=200');
}
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
	include ($Dir.MainDir.$_data->menu_type.".php");
	include_once("./mypage_groupinfo.php"); //회원등급 출력용
?>

<!-- 마이페이지-쿠폰내역 상단 메뉴 -->
<div class="mypagemembergroup">
	<div class="groupinfotext">안녕하세요? <strong class="st1"><?=$_ShopInfo->getMemname()?></strong>님. 회원님의 등급은 <strong class="st2"><?=$groupname?></strong>입니다.</span></div>
	<div class="gruopinfogo"><a href="/front/newpage.php?code=1">회원정책보기 &gt;</a></div>
</div>
<table border="0" cellpadding="0" cellspacing="0" class="mypagetmenu">
	<tr>
		<td class="leftline"><a href="/front/mypage.php">마이페이지</a></td>
		<td class="leftline"><a href="/front/mypage_orderlist.php">주문내역</a></td>
		<td class="leftline"><a href="/front/mypage_personal.php">1:1 문의</a></td>
		<td class="leftline"><a href="/front/mypage_reserve.php">적립금</a></td>
		<td class="leftline"><a href="/front/wishlist.php">찜하기</a></td>
		<td class="nowMyage"><a href="/front/mypage_coupon.php">쿠폰내역</a></td>
		<? if($_data->recom_url_ok == "Y" || $_data->sns_ok == "Y"){ ?><td><a href="/front/mypage_promote.php">홍보관리</a></td><? } ?>
		<? if(getVenderUsed()==true) { ?><td><a href="/front/mypage_custsect.php">단골매장</a></td><? } ?>
		<td><a href="/front/mypage_usermodify.php">회원정보</a></td>
		<td><a href="/front/mypage_memberout.php">회원탈퇴</a></td>
	</tr>
</table>
<div class="currentTitle">
	<div class="titleimage">쿠폰내역</div>
	<div class="current">홈 &gt; 마이페이지 &gt; <SPAN class="nowCurrent">쿠폰내역</span></div>
</div>
<!-- 마이페이지-쿠폰내역 상단 메뉴 -->



<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
$leftmenu="Y";
if($_data->design_mycoupon=="U") {
	$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='mycoupon'";
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
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/mycoupon_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/mycoupon_title.gif\" border=\"0\" alt=\"쿠폰 보유내역\"></td>\n";
	} else {
		echo "<td>\n";
		/*
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/mycoupon_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/mycoupon_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/mycoupon_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		*/
		echo "</td>\n";
	}
	echo "</tr>\n";
}

echo "<tr>\n";
echo "	<td align=\"center\">\n";
include ($Dir.TempletDir."mycoupon/mycoupon".$_data->design_mycoupon.".php");
echo "	</td>\n";
echo "</tr>\n";
?>

</table>

<? include ($Dir."lib/bottom.php") ?>

<?=$onload?>

</BODY>
</HTML>