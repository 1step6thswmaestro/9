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

function getBCodeLoc($brandcode,$code="",$color1="9E9E9E",$color2="9E9E9E") {
	global $_ShopInfo, $Dir;
	$sql = "SELECT brandname FROM tblproductbrand ";
	$sql.= "WHERE bridx='".$brandcode."' ";
	$result=mysql_query($sql,get_db_conn());
	$brow=mysql_fetch_object($result);

	if(strlen($code)>0) {
		$code_loc = "<A HREF=\"".$Dir.MainDir."main.php\"><FONT COLOR=\"".$color1."\">홈</FONT></A> <FONT COLOR=\"".$color1."\">></FONT> <A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$brandcode."\"><FONT COLOR=\"".$color1."\">브랜드 : ".$brow->brandname."</FONT></A>";
		$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
		$sql.= "WHERE codeA='".substr($code,0,3)."' ";
		if(substr($code,3,3)!="000") {
			$sql.= "AND (codeB='".substr($code,3,3)."' OR codeB='000') ";
			if(substr($code,6,3)!="000") {
				$sql.= "AND (codeC='".substr($code,6,3)."' OR codeC='000') ";
				if(substr($code,9,3)!="000") {
					$sql.= "AND (codeD='".substr($code,9,3)."' OR codeD='000') ";
				} else {
					$sql.= "AND codeD='000' ";
				}
			} else {
				$sql.= "AND codeC='000' ";
			}
		} else {
			$sql.= "AND codeB='000' AND codeC='000' ";
		}
		$sql.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
		$result=mysql_query($sql,get_db_conn());
		$i=0;
		while($row=mysql_fetch_object($result)) {
			$tmpcode=$row->codeA.$row->codeB.$row->codeC.$row->codeD;
			$code_loc.= " <FONT COLOR=\"".$color1."\">></FONT> ";
			if($code==$tmpcode) {
				$code_loc.="<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$brandcode."&code=".$tmpcode."\"><FONT COLOR=\"".$color2."\"><B>".$row->code_name."</B></FONT></A>";
			} else {
				$code_loc.="<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$brandcode."&code=".$tmpcode."\"><FONT COLOR=\"".$color1."\">".$row->code_name."</FONT></A>";
			}
			$code_loc.= $_tmp;
			$i++;
		}
		mysql_free_result($result);
	} else {
		$code_loc = "<A HREF=\"".$Dir.MainDir."main.php\"><FONT COLOR=\"".$color1."\">홈</FONT></A> <FONT COLOR=\"".$color1."\">></FONT> <A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$brandcode."\"><FONT COLOR=\"".$color1."\"><B>브랜드 : ".$brow->brandname."</FONT></B></A>";
	}
	return $code_loc;
}

function getCodeLoc($code,$color1="9E9E9E",$color2="9E9E9E") {
	global $_ShopInfo, $Dir;
	$code_loc = "<A HREF=\"".$Dir.MainDir."main.php\"><FONT COLOR=\"".$color1."\">홈</FONT></A> <FONT COLOR=\"".$color1."\">></FONT> ";
	$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
	$sql.= "WHERE codeA='".substr($code,0,3)."' ";
	if(substr($code,3,3)!="000") {
		$sql.= "AND (codeB='".substr($code,3,3)."' OR codeB='000') ";
		if(substr($code,6,3)!="000") {
			$sql.= "AND (codeC='".substr($code,6,3)."' OR codeC='000') ";
			if(substr($code,9,3)!="000") {
				$sql.= "AND (codeD='".substr($code,9,3)."' OR codeD='000') ";
			} else {
				$sql.= "AND codeD='000' ";
			}
		} else {
			$sql.= "AND codeC='000' ";
		}
	} else {
		$sql.= "AND codeB='000' AND codeC='000' ";
	}
	$sql.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
	$result=mysql_query($sql,get_db_conn());
	$i=0;
	while($row=mysql_fetch_object($result)) {
		$tmpcode=$row->codeA.$row->codeB.$row->codeC.$row->codeD;
		if($i>0) $code_loc.= " <FONT COLOR=\"".$color1."\">></FONT> ";
		if($code==$tmpcode) {
			$code_loc.="<A HREF=\"".$Dir.FrontDir."productlist.php?code=".$tmpcode."\"><FONT COLOR=\"".$color2."\"><B>".$row->code_name."</B></FONT></A>";
		} else {
			$code_loc.="<A HREF=\"".$Dir.FrontDir."productlist.php?code=".$tmpcode."\"><FONT COLOR=\"".$color1."\">".$row->code_name."</FONT></A>";
		}
		$code_loc.= $_tmp;
		$i++;
	}
	mysql_free_result($result);
	return $code_loc;
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
<tr>
	<td>
<?
	$sql = "SELECT leftmenu,body,code FROM tbldesignnewpage_prev ";
	$sql.= "WHERE type='prdetail'";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$_ndata=$row;
	mysql_free_result($result);

	if($_ndata) {
		$body=$_ndata->body;
		$body=str_replace("[DIR]",$Dir,$body);
		include($Dir.TempletDir."product/detail_U.php");
	} 
?>
	</td>
</tr>
</table>
	
</BODY>
</HTML>