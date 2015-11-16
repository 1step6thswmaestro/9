<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

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
}
mysql_free_result($result);

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

?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 1:1 고객문의</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}
function ViewPersonal(idx) {
	window.open("about:blank","mypersonalview","width=600,height=450,scrollbars=yes");
	document.form3.idx.value=idx;
	document.form3.submit();
}
function PersonalWrite() {
	window.open("<?=$Dir.FrontDir?>mypage_personalwrite.php","mypersonalwrite","width=580,height=450,scrollbars=no");
}
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
	include ($Dir.MainDir.$_data->menu_type.".php");
	include_once("./mypage_groupinfo.php"); //회원등급 출력용
?>

<!-- 마이페이지-1:1문의 상단 메뉴 -->
<div class="mypagemembergroup">
	<div class="groupinfotext">안녕하세요? <strong class="st1"><?=$_ShopInfo->getMemname()?></strong>님. 회원님의 등급은 <strong class="st2"><?=$groupname?></strong>입니다.</span></div>
	<div class="gruopinfogo"><a href="/front/newpage.php?code=1">회원정책보기 &gt;</a></div>
</div>
<table border="0" cellpadding="0" cellspacing="0" class="mypagetmenu">
	<tr>
		<td class="leftline"><a href="/front/mypage.php">마이페이지</a></td>
		<td class="leftline"><a href="/front/mypage_orderlist.php">주문내역</a></td>
		<td class="nowMyage"><a href="/front/mypage_personal.php">1:1 문의</a></td>
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
	<div class="titleimage">1:1 문의</div>
	<div class="current">홈 &gt; 마이페이지 &gt; <SPAN class="nowCurrent">1:1문의</span></div>
</div>
<!-- 마이페이지-1:1문의 상단 메뉴 -->



<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
$leftmenu="Y";
if($_data->design_mypersonal=="U") {
	$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='mypersonal'";
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
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/mypersonal_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/mypersonal_title.gif\" border=\"0\" alt=\"1:1고객문의\"></td>\n";
	} else {
		echo "<td>\n";
		/*
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/mypersonal_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/mypersonal_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/mypersonal_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		*/
		echo "</td>\n";
	}
	echo "</tr>\n";
}

echo "<tr>\n";
echo "	<td align=\"center\">\n";
include ($Dir.TempletDir."mypersonal/mypersonal".$_data->design_mypersonal.".php");
echo "	</td>\n";
echo "</tr>\n";
?>

<form name=idxform method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=block>
<input type=hidden name=gotopage>
</form>

<form name=form3 action="<?=$Dir.FrontDir?>mypage_personalview.php" method=post target="mypersonalview">
<input type=hidden name=idx>
</form>

</table>

<? include ($Dir."lib/bottom.php") ?>

<?=$onload?>

</BODY>
</HTML>