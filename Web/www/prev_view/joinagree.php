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
$sql="SELECT body,leftmenu FROM tbldesignnewpage_prev WHERE type='joinagree'";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$body=$row->body;
	$body=str_replace("[DIR]",$Dir,$body);
	$leftmenu=$row->leftmenu;
	$newdesign="Y";
}
mysql_free_result($result);

$sql="SELECT agreement,privercy FROM tbldesign ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$agreement=$row->agreement;
$privercy_exp=@explode("=", $row->privercy);
$privercy=$privercy_exp[1];
mysql_free_result($result);

if(strlen($agreement)==0) {
	$buffer="";
	$fp=fopen($Dir.AdminDir."agreement.txt","r");
	if($fp) {
		while (!feof($fp)) {
			$buffer.= fgets($fp, 1024);
		}
	}
	fclose($fp);
	$agreement=$buffer;
}

$pattern=array("(\[SHOP\])","(\[COMPANY\])");
$replace=array($_data->shopname, $_data->companyname);
$agreement = preg_replace($pattern,$replace,$agreement);

if(strlen($privercy)==0) {
	$buffer="";
	$fp=fopen($Dir.AdminDir."privercy2.txt","r");
	if($fp) {
		while (!feof($fp)) {
			$buffer.= fgets($fp, 1024);
		}
	}
	fclose($fp);
	$privercy=$buffer;
}

$pattern=array("(\[SHOP\])","(\[NAME\])","(\[EMAIL\])","(\[TEL\])");
$replace=array($_data->shopname,$_data->privercyname,"<a href=\"mailto:".$_data->privercyemail."\">".$_data->privercyemail."</a>",$_data->info_tel);
$privercy = preg_replace($pattern,$replace,$privercy);


$pattern=array("(\[CONTRACT\])","(\[PRIVERCY\])","(\[CHECK\])","(\[CHECKP\])","(\[OK\])","(\[REJECT\])");
$replace=array($agreement,$privercy,"<input type=checkbox id=\"idx_agree\" name=agree style=\"border:none;\"> <label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_agree>","<input type=checkbox id=\"idx_agreep\" name=agreep style=\"border:none;\"> <label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_agreep>","javascript:CheckForm()","javascript:history.go(-1)");
$body=preg_replace($pattern,$replace,$body);
echo "<tr>\n";
echo "	<td align=center>".$body."</td>";
echo "</tr>\n";
echo "<tr>\n";

?>
</table>
	
</BODY>
</HTML>