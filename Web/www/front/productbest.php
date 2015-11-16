<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_product.php");
include_once($Dir."lib/shopdata.php");

$prsection_type=$_data->design_prbest;

$sort=(isset($_REQUEST["sort"])?$_REQUEST["sort"]:"");
$listnum=(int)(isset($_REQUEST["listnum"])?$_REQUEST["listnum"]:0);

//if($listnum<=0) $listnum=16;
$listnum=(int)$_REQUEST["listnum"];
if($listnum<=0) $listnum=$_data->prlist_num;

//리스트 세팅
$setup["page_num"] = 10;
$setup["list_num"] = $listnum;

$block=(isset($_REQUEST["block"])?$_REQUEST["block"]:"");
$gotopage=(isset($_REQUEST["gotopage"])?$_REQUEST["gotopage"]:"");

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup["page_num"] + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}

$sql = "SELECT special_list FROM tblspecialmain WHERE special='2' ";
$result=mysql_query($sql,get_db_conn());
$sp_prcode="";
if($row=mysql_fetch_object($result)) {
	$sp_prcode=ereg_replace(',','\',\'',$row->special_list);
}
mysql_free_result($result);

$t_count=0;
if(strlen($sp_prcode)>0) {
	$sql = "SELECT COUNT(*) as t_count ";
	$sql.= "FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$t_count = (int)$row->t_count;
	mysql_free_result($result);
	$pagecount = (($t_count - 1) / $setup["list_num"]) + 1;
}
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shopname." [인기상품]"?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function ChangeSort(val) {
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.sort.value=val;
	document.form2.submit();
}

function ChangeListnum(val) {
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.listnum.value=val;
	document.form2.submit();
}

function ChangeNum(obj) {
	document.form2.listnum.value=obj.value;
	document.form2.submit();
}

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
$leftmenu="Y";
if($_data->design_prbest=="U") {
	$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='prbest'";
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
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/productbest_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/productbest_title.gif\" border=\"0\" alt=\"인기상품\"></td>\n";
	} else {
		echo "<td>\n";
		/*
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/productbest_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/productbest_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/productbest_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		*/
		echo "<div class=\"subpageTitle\">인기상품</div>";
		echo "</td>\n";
	}
	echo "</tr>\n";
}

$sptype = '2';

echo "<tr>\n";
echo "	<td align=\"center\">\n";
include ($Dir.TempletDir."prsection/prsection".$_data->design_prbest.".php");
echo "	</td>\n";
echo "</tr>\n";
?>
<form name=form2 method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>
</table>

<? include ($Dir."lib/bottom.php") ?>
<div id="create_openwin" style="display:none"></div>
</BODY>
</HTML>

<? if($HTML_CACHE_EVENT=="OK") ob_end_flush(); ?>