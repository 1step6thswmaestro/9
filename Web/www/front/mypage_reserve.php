<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

if($_data->reserve_maxuse<0) {
	echo "<html><head><title></title></head><body onload=\"alert('본 쇼핑몰에서는 적립금 기능을 지원하지 않습니다.');location.href='".$Dir.FrontDir."mypage.php'\"></body></html>";exit;
}

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 10;

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}

$maxreserve=$_data->reserve_maxuse;

$reserve=0;
$sql = "SELECT id,name,reserve FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$id=$row->id;
	$name=$row->name;
	$reserve=$row->reserve;
} else {
	echo "<html><head><title></title></head><body onload=\"alert('회원정보가 존재하지 않습니다.');location.href='".$_SERVER[PHP_SELF]."?type=logout'\"></body></html>";exit;
}
mysql_free_result($result);


/* 6개월 까지만 조회하기 위해서 */
$e_year=(int)date("Y");
$e_month=(int)date("m");
$e_day=(int)date("d");
$stime=mktime(0,0,0,($e_month-6),$e_day,$e_year);
$s_year=(int)date("Y",$stime);
$s_month=(int)date("m",$stime);
$s_day=(int)date("d",$stime);
$s_curtime=mktime(0,0,0,$s_month,$s_day,$s_year);
$s_curdate=date("YmdHis",$s_curtime);
$e_curtime=mktime(24,59,59,$e_month,$e_day,$e_year);
$e_curdate=date("YmdHis",$e_curtime);

?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 적립금 내역</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.form1.block.value=block;
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}
function OrderDetailPop(ordercode) {
	document.form2.ordercode.value=ordercode;
	window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
	document.form2.submit();
}

//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
	include ($Dir.MainDir.$_data->menu_type.".php");
	include_once("./mypage_groupinfo.php"); //회원등급 출력용
?>

<!-- 마이페이지-적립금 상단 메뉴 -->
<div class="mypagemembergroup">
	<div class="groupinfotext">안녕하세요? <strong class="st1"><?=$_ShopInfo->getMemname()?></strong>님. 회원님의 등급은 <strong class="st2"><?=$groupname?></strong>입니다.</span></div>
	<div class="gruopinfogo"><a href="/front/newpage.php?code=1">회원정책보기 &gt;</a></div>
</div>
<table border="0" cellpadding="0" cellspacing="0" class="mypagetmenu">
	<tr>
		<td class="leftline"><a href="/front/mypage.php">마이페이지</a></td>
		<td class="leftline"><a href="/front/mypage_orderlist.php">주문내역</a></td>
		<td class="leftline"><a href="/front/mypage_personal.php">1:1 문의</a></td>
		<td class="nowMyage"><a href="/front/mypage_reserve.php">적립금</a></td>
		<td><a href="/front/wishlist.php">찜하기</a></td>
		<td><a href="/front/mypage_coupon.php">쿠폰내역</a></td>
		<? if($_data->recom_url_ok == "Y" || $_data->sns_ok == "Y"){ ?><td><a href="/front/mypage_promote.php">홍보관리</a></td><? } ?>
		<? if(getVenderUsed()==true) { ?><td><a href="/front/mypage_custsect.php">단골매장</a></td><? } ?>
		<td><a href="/front/mypage_usermodify.php">회원정보</a></td>
		<td><a href="/front/mypage_memberout.php">회원탈퇴</a></td>
	</tr>
</table>
<div class="currentTitle">
	<div class="titleimage">적립금</div>
	<div class="current">홈 &gt; 마이페이지 &gt; <SPAN class="nowCurrent">적립금</span></div>
</div>
<!-- 마이페이지-적립금 상단 메뉴 -->



<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
$leftmenu="Y";
if($_data->design_myreserve=="U") {
	$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='myreserve'";
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
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/myreserve_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/myreserve_title.gif\" border=\"0\" alt=\"적립금 내역\"></td>\n";
	} else {
		echo "<td>\n";
		/*
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/myreserve_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/myreserve_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/myreserve_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		*/
		echo "</td>\n";
	}
	echo "</tr>\n";
}

echo "<tr>\n";
echo "	<td align=\"center\">\n";
include ($Dir.TempletDir."myreserve/myreserve".$_data->design_myreserve.".php");
echo "	</td>\n";
echo "</tr>\n";
?>

<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=block>
<input type=hidden name=gotopage>
</form>
<form name=form2 method=post action="<?=$Dir.FrontDir?>orderdetailpop.php" target="orderpop">
<input type=hidden name=ordercode>
</form>

</table>

<? include ($Dir."lib/bottom.php") ?>

<?=$onload?>

</BODY>
</HTML>