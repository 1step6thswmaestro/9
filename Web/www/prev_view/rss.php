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
$sql="SELECT body,leftmenu FROM tbldesignnewpage_prev WHERE type='rss'";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$body=$row->body;
	$body=str_replace("[DIR]",$Dir,$body);
	$leftmenu=$row->leftmenu;
	$newdesign="Y";
}
mysql_free_result($result);

if($num=strpos($body,"[CODEA_")) {
		$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
		$codeA_style=$s_tmp[1];
	}
	if($num=strpos($body,"[CODEB_")) {
		$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
		$codeB_style=$s_tmp[1];
	}
	if($num=strpos($body,"[CODEC_")) {
		$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
		$codeC_style=$s_tmp[1];
	}
	if($num=strpos($body,"[CODED_")) {
		$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
		$codeD_style=$s_tmp[1];
	}
	$sprice_style="";
	if($num=strpos($body,"[SPRICE_")) {
		$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
		$sprice_style=$s_tmp[1];
	}

	if(strlen($codeA_style)==0) $codeA_style="width:200px";
	if(strlen($codeB_style)==0) $codeB_style="width:200px";
	if(strlen($codeC_style)==0) $codeC_style="width:200px";
	if(strlen($codeD_style)==0) $codeD_style="width:200px";

	if($num=strpos($body,"[KEYWORD_")) {
		$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
		$keyword_style=$s_tmp[1];
	}
	if($num=strpos($body,"[RSSFEED_")) {
		$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
		$rssfeed_style=$s_tmp[1];
	}
	if(strlen($keyword_style)==0) $keyword_style="width:300px";
	if(strlen($rssfeed_style)==0) $rssfeed_style="width:300px";

	$codeA_select ="<select name=codeA style=\"".$codeA_style."\" onchange=\"SearchChangeCate(this,1)\">\n";
	$codeA_select.="<option value=\"\">--- 1차 카테고리 선택 ---</option>\n";
	$codeA_select.="</select>\n";

	$codeB_select ="<select name=codeB style=\"".$codeB_style."\" onchange=\"SearchChangeCate(this,2)\">\n";
	$codeB_select.="<option value=\"\">--- 2차 카테고리 선택 ---</option>\n";
	$codeB_select.="</select>\n";

	$codeC_select ="<select name=codeC style=\"".$codeC_style."\" onchange=\"SearchChangeCate(this,3)\">\n";
	$codeC_select.="<option value=\"\">--- 3차 카테고리 선택 ---</option>\n";
	$codeC_select.="</select>\n";

	$codeD_select ="<select name=codeD style=\"".$codeD_style."\">\n";
	$codeD_select.="<option value=\"\">--- 4차 카테고리 선택 ---</option>\n";
	$codeD_select.="</select>\n";

	$txt_keyword = "<input type=text name=search style=\"".$keyword_style."\">";

	$sprice_select = "<select name=sprice";
	if(strlen($sprice_style)>0) $sprice_select.= " style=\"".$sprice_style."\"";
	$sprice_select.= ">\n";
	$sprice_select.= "<option value=\"\">전체</option>\n";
	$sprice_select.= "<option value=\"20000\">2만원 이하</option>\n";
	$sprice_select.= "<option value=\"50000\">2~5만원</option>\n";
	$sprice_select.= "<option value=\"100000\">5~10만원</option>\n";
	$sprice_select.= "<option value=\"300000\">10~30만원</option>\n";
	$sprice_select.= "<option value=\"300001\">30만원 이상</option>\n";
	$sprice_select.= "</select>\n";

	$txt_rssfeed = "<input type=text name=rssfeed style=\"".$rssfeed_style."\">";

	$pattern=array(
		"(\[CODEA((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
		"(\[CODEB((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
		"(\[CODEC((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
		"(\[CODED((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
		"(\[KEYWORD((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
		"(\[SPRICE((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
		"(\[RSSFEED((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
		"(\[FEEDCREATE\])",
		"(\[FEEDCOPY\])"
	);

	$replace=array($codeA_select,$codeB_select,$codeC_select,$codeD_select,$txt_keyword,$sprice_select,$txt_rssfeed,"javascript:FeedCreate()","javascript:FeedCopy()");
	$body = preg_replace($pattern,$replace,$body);
	echo $body;
?>
</table>
	
</BODY>
</HTML>