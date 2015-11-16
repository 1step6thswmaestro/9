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
$sql="SELECT body,leftmenu FROM tbldesignnewpage_prev WHERE type='login'";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$body=$row->body;
	$body=str_replace("[DIR]",$Dir,$body);
	$leftmenu=$row->leftmenu;
	$newdesign="Y";
}
mysql_free_result($result);

$banner_body="";
$sql = "SELECT * FROM tblaffiliatebanner WHERE used='Y' ORDER BY rand() LIMIT 1 ";
$result=@mysql_query($sql,get_db_conn());
if($row=@mysql_fetch_object($result)) {
	$tempcontent=explode("=",$row->content);
	$banner_type=$tempcontent[0];
	if($banner_type=="Y") {
		$banner_target=$tempcontent[1];
		$banner_url=$tempcontent[2];
		$banner_image=$tempcontent[3];
		if(strlen($banner_image)>0 && file_exists($Dir.DataDir."shopimages/banner/".$banner_image)==true) {
			$banner_body="<A HREF=\"".$banner_url."\" target=\"".$banner_target."\"><img src=\"".$Dir.DataDir."shopimages/banner/".$banner_image."\" border=0></A>";
		}
	} else if($banner_type=="N") {
		$banner_body=$tempcontent[1];
	}
}
@mysql_free_result($result);

//주문조회시 로그인
if(substr($chUrl,-20)=="mypage_orderlist.php") {
	$body=str_replace("[IFORDER]","",$body);
	$body=str_replace("[ENDORDER]","",$body);
} else {
	if(strlen(strpos($body,"[IFORDER]"))>0){
		$iforder=strpos($body,"[IFORDER]");
		$endorder=strpos($body,"[ENDORDER]");
		$body=substr($body,0,$iforder).substr($body,$endorder+10);
	}
}
//바로구매시 로그인
if(substr($chUrl,-9)=="order.php") {
	$body=str_replace("[IFNOLOGIN]","",$body);
	$body=str_replace("[ENDNOLOGIN]","",$body);
} else {
	if(strlen(strpos($body,"[IFNOLOGIN]"))>0){
		$iforder=strpos($body,"[IFNOLOGIN]");
		$endorder=strpos($body,"[ENDNOLOGIN]");
		$body=substr($body,0,$iforder).substr($body,$endorder+12);
	}
}
// SSL 체크박스 출력
if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {
	$body=str_replace("[IFSSL]","",$body);
	$body=str_replace("[ENDSSL]","",$body);
} else {
	if(strlen(strpos($body,"[IFSSL]"))>0){
		$ifssl=strpos($body,"[IFSSL]");
		$endssl=strpos($body,"[ENDSSL]");
		$body=substr($body,0,$ifssl).substr($body,$endssl+8);
	}
}

$pattern=array("(\[ID\])","(\[PASSWD\])","(\[SSLCHECK\])","(\[SSLINFO\])","(\[OK\])","(\[JOIN\])","(\[FINDPWD\])","(\[NOLOGIN\])","(\[ORDERNAME\])","(\[ORDERCODE\])","(\[ORDEROK\])","(\[BANNER\])");
$replace=array("<input type=text name=id value=\"\" maxlength=20 style=\"width:120\">","<input type=password name=passwd value=\"\" maxlength=20 style=\"width:120\">","<input type=checkbox name=ssllogin value=Y>","javascript:sslinfo()","\"JavaScript:CheckForm()\"",$Dir.FrontDir."member_agree.php",$Dir.FrontDir."findpwd.php",$Dir.FrontDir."order.php","<input type=text name=ordername value=\"\" maxlength=20 style=\"width:80\">","<input type=text name=ordercodeid value=\"\" maxlength=20 style=\"width:80\">","\"javascript:CheckOrder()\"",$banner_body);
$body=preg_replace($pattern,$replace,$body);
echo $body;

?>
</table>
	
</BODY>
</HTML>