<?
if(strlen($Dir)==0) {
	$Dir="../";
}
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_main.php");

Header("Pragma: no-cache");

include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/eventpopup.php");

$mainpagemark = "Y"; // 메인 페이지
$selfcodefont_start = "<font class=\"mainselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트 끝

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
<!-- ShoppingMall Version <?=_IncomuShopVersionNo?>(<?=_IncomuShopVersionDate?>) //-->
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?></TITLE>
<link rel="P3Pv1" href="http://<?=$_ShopInfo->getShopurl()?>w3c/p3p.xml">
<link rel="shortcut icon" href="<?=$Dir?>2010/favicon1.ico" >
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
<?=$onload?>
//-->
</SCRIPT>
<?include($Dir."lib/style.php")?>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<center><script src="../Scripts/common.js" type="text/javascript"></script>
<script type="text/javascript" src="../Scripts/rolling.js"></script>
<link href="../css/in_style.css" rel="stylesheet" type="text/css" />
<link href="../css/new_style.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="../2010/favicon1.ico" >

<style type="text/css">
<!--
.style1 {font-family: "돋움체", "돋움";font-size: 12px;}
a {selector-dummy : expression(this.hideFocus=true);}
a:link {color:#909090;text-decoration: none;}
a:visited {color:#909090;text-decoration: none;}	
a:hover {color:#ce0000;text-decoration: none;}
-->
</style>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<?
$sql="SELECT body FROM tbldesignnewpage_prev WHERE type='surveyview'";
$result=mysql_query($sql,get_db_conn());
$body="";
if($row=mysql_fetch_object($result)) {
	$body=$row->body;
	$body=str_replace("[DIR]",$Dir,$body);
}
mysql_free_result($result);

$pattern=array ("(\[SURVEYTITLE\])","(\[SURVEYDATE\])","(\[SURVEYTOT\])","(\[SURVEYRESULT\])","(\[SURVEYREVIEW\])","(\[SURVEYLIST\])","(\[SURVEYCLOSE\])");
$replace=array ($surveytitle,$surveydate,$NO_total,$surveyresult,$surveyreview,$_SERVER[PHP_SELF],"javascript:window.close()");
$body=preg_replace($pattern,$replace,$body);

echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"480\" style=\"table-layout:fixed\"><tr><td align=\"center\">".$body."</td></tr></table>";
?>
</table>
	
</BODY>
</HTML>