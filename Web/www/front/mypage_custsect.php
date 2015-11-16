<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
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

$mode=$_POST["mode"];
$venders=$_POST["venders"];
if($mode=="delete" && strlen($venders)>0) {
	$venders=substr($venders,0,-1);
	$venderlist=ereg_replace(',','\',\'',$venders);
	$sql = "DELETE FROM tblregiststore WHERE id='".$_ShopInfo->getMemid()."' AND vender IN ('".$venderlist."') ";
	if(mysql_query($sql,get_db_conn())) {
		$sql = "UPDATE tblvenderstorecount SET cust_cnt=cust_cnt-1 WHERE vender IN ('".$venderlist."') ";
		mysql_query($sql,get_db_conn());
	}
	header("Location:".$_SERVER[PHP_SELF]."?block=".$block."&gotopage=".$gotopage); exit;
} else if($mode=="agree" && strlen($venders)>0 && ($type=="Y" || $type=="N")) {
	$venders=substr($venders,0,-1);
	$venderlist=ereg_replace(',','\',\'',$venders);
	$sql = "UPDATE tblregiststore SET email_yn='".$type."' WHERE id='".$_ShopInfo->getMemid()."' AND vender IN ('".$venderlist."') ";
	mysql_query($sql,get_db_conn());
	header("Location:".$_SERVER[PHP_SELF]."?block=".$block."&gotopage=".$gotopage); exit;
}

?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 단골매장</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function miniMailAgree(gbn,vender) {
	if(gbn=="add") {
		if(confirm("메일을 수신하시겠습니까?")) {
			document.form2.venders.value=vender+",";
			document.form2.mode.value="agree";
			document.form2.type.value="Y";
			document.form2.submit();
		}
	} else if(gbn=="del") {
		if(confirm("메일을 거부하시겠습니까?")) {
			document.form2.venders.value=vender+",";
			document.form2.mode.value="agree";
			document.form2.type.value="N";
			document.form2.submit();
		}
	}
}
function addAgreeMailAll() {
	document.form2.venders.value="";
	for(i=1;i<document.form1.sels.length;i++) {
		if(document.form1.sels[i].checked==true) {
			document.form2.venders.value+=document.form1.sels[i].value+",";
		}
	}
	if(document.form2.venders.value.length==0) {
		alert("선택하신 미니샵이 없습니다.");
		return;
	}
	if(confirm("선택하신 미니샵의 메일을 수신하시겠습니까?")) {
		document.form2.mode.value="agree";
		document.form2.type.value="Y";
		document.form2.submit();
	}
}
function delAgreeMailAll() {
	document.form2.venders.value="";
	for(i=1;i<document.form1.sels.length;i++) {
		if(document.form1.sels[i].checked==true) {
			document.form2.venders.value+=document.form1.sels[i].value+",";
		}
	}
	if(document.form2.venders.value.length==0) {
		alert("선택하신 미니샵이 없습니다.");
		return;
	}
	if(confirm("선택하신 미니샵의 메일을 수신 거부하시겠습니까?")) {
		document.form2.mode.value="agree";
		document.form2.type.value="N";
		document.form2.submit();
	}
}

var chkval=false;
function CheckAll(){
	if(chkval==false) chkval=true;
	else if(chkval==true) chkval=false;
	cnt=document.form1.tot.value;
	for(i=1;i<=cnt;i++){
		document.form1.sels[i].checked=chkval;
	}
}

function goDeleteMinishop() {
	document.form2.venders.value="";
	for(i=1;i<document.form1.sels.length;i++) {
		if(document.form1.sels[i].checked==true) {
			document.form2.venders.value+=document.form1.sels[i].value+",";
		}
	}
	if(document.form2.venders.value.length==0) {
		alert("선택하신 미니샵이 없습니다.");
		return;
	}
	if(confirm("선택하신 미니샵을 삭제하시겠습니까?")) {
		document.form2.mode.value="delete";
		document.form2.submit();
	}
}

function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
	include ($Dir.MainDir.$_data->menu_type.".php");
	include_once("./mypage_groupinfo.php"); //회원등급 출력용
?>

<!-- 마이페이지-단골매장 상단 메뉴 -->
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
		<td class="leftline"><a href="/front/mypage_coupon.php">쿠폰내역</a></td>
		<? if($_data->recom_url_ok == "Y" || $_data->sns_ok == "Y"){ ?><td class="leftline"><a href="/front/mypage_promote.php">홍보관리</a></td><? } ?>
		<? if(getVenderUsed()==true) { ?><td class="nowMyage"><a href="/front/mypage_custsect.php">단골매장</a></td><? } ?>
		<td><a href="/front/mypage_usermodify.php">회원정보</a></td>
		<td><a href="/front/mypage_memberout.php">회원탈퇴</a></td>
	</tr>
</table>
<div class="currentTitle">
	<div class="titleimage">단골매장</div>
	<div class="current">홈 &gt; 마이페이지 &gt; <SPAN class="nowCurrent">단골매장</span></div>
</div>
<!-- 마이페이지-단골매장 상단 메뉴 -->



<table border=0 cellpadding=0 cellspacing=0 width=100%>
<?
$leftmenu="Y";
if($_data->design_mycustsect=="U") {
	$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='mycustsect'";
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
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/mycustsect_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/mycustsect_title.gif\" border=\"0\" alt=\"단골매장\"></td>\n";
	} else {
		echo "<td>\n";
		/*
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/mycustsect_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/mycustsect_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/mycustsect_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		*/
		echo "</td>\n";
	}
	echo "</tr>\n";
}

echo "<form name=form1 method=post action=\"".$_SERVER[PHP_SELF]."\">\n";
echo "<input type=hidden name=sels>\n";
echo "<tr>\n";
echo "	<td align=center>\n";
include ($Dir.TempletDir."mycustsect/mycustsect".$_data->design_mycustsect.".php");
echo "	</td>\n";
echo "</tr>\n";
echo "<input type=hidden name=tot value=\"".$cnt."\">\n";
echo "</form>\n";
?>

<form name=idxform method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=block>
<input type=hidden name=gotopage>
</form>

<form name=form2 method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode>
<input type=hidden name=type>
<input type=hidden name=venders>
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

</table>

<? include ($Dir."lib/bottom.php") ?>

<?=$onload?>

</BODY>
</HTML>