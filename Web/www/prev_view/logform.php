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
<?
$companynum="";

$sql = "SELECT body FROM tbldesignnewpage_prev WHERE type='logform' ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$top_loginformu=$row->body;
$top_loginformu=str_replace("[DIR]",$Dir,$top_loginformu);
mysql_free_result($result);
$idfield="";
if($posnum=strpos($top_loginformu,"[ID")) {
	$s_tmp=explode("_",substr($top_loginformu,$posnum+1,strpos($top_loginformu,"]",$posnum)-$posnum-1));
	$idflength=(int)$s_tmp[1];
	if($idflength==0) $idflength=80;

	$idfield="<input type=text name=id maxlength=20 style=\"width:$idflength\">";
}
$pwfield="";
if($posnum=strpos($top_loginformu,"[PASSWD")) {
	$s_tmp=explode("_",substr($top_loginformu,$posnum+1,strpos($top_loginformu,"]",$posnum)-$posnum-1));
	$pwflength=(int)$s_tmp[1];
	if($pwflength==0) $pwflength=80;

	$pwfield="<input type=password name=passwd maxlength=20 onkeydown=\"TopCheckKeyLogin()\" style=\"width:$pwflength\">";
}
$pattern_login=array("(\[ID(\_){0,1}([0-9]{0,3})\])","(\[PASSWD(\_){0,1}([0-9]{0,3})\])","(\[SSLCHECK\])","(\[SSLINFO\])","(\[OK\])","(\[JOIN\])","(\[FINDPWD\])","(\[LOGIN\])","(\[TARGET\])","(\[ID\])","(\[NAME\])","(\[RESERVE\])","(\[LOGOUT\])","(\[MEMBEROUT\])","(\[MEMBER\])","(\[MYPAGE\])");
$replace_login=array($idfield,$pwfield,"<input type=checkbox name=ssllogin value=Y>","javascript:sslinfo()","javascript:top_login_check()",$Dir.FrontDir."member_agree.php $main_target",$Dir.FrontDir."findpwd.php $main_target",$Dir.FrontDir."login.php $main_target",$main_target,$_ShopInfo->getMemid(),$_ShopInfo->getMemname(),number_format($_ShopInfo->getMemreserve()),$Dir.MainDir."main.php?type=logout","javascript:memberout()",$Dir.FrontDir."mypage_usermodify.php",$Dir.FrontDir."mypage.php");
$top_loginformu = preg_replace($pattern_login,$replace_login,$top_loginformu);

echo $top_loginformu;
?>
		