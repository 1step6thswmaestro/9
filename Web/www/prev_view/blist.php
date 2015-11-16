<?
if(strlen($Dir)==0) {
	$Dir="../";
}
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_main.php");

Header("Pragma: no-cache");

include_once($Dir."lib/shopdata.php");

$brandcode=$_REQUEST["brandcode"];
$code=$_REQUEST["code"];

if($_data->ETCTYPE["BRANDPRO"]!="Y" || strlen($brandcode)==0) {
	Header("Location:".$Dir.MainDir."main.php");
	exit;
}

$brandpagemark = "Y"; // 브랜드 전용 페이지 
$selfcodefont_start = "<font class=\"prselfcode\">"; //진열코드 폰트 시작
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

$_bdata="";
$sql = "SELECT * FROM tblproductbrand ";
$sql.= "WHERE bridx='".$brandcode."' ";
$result=mysql_query($sql,get_db_conn());
$brow=mysql_fetch_object($result);
$_bdata=$brow;

if(strlen($code)>0) {
	$codeA=substr($code,0,3);
	$codeB=substr($code,3,3);
	$codeC=substr($code,6,3);
	$codeD=substr($code,9,3);
	if(strlen($codeA)!=3) $codeA="000";
	if(strlen($codeB)!=3) $codeB="000";
	if(strlen($codeC)!=3) $codeC="000";
	if(strlen($codeD)!=3) $codeD="000";
	$code=$codeA.$codeB.$codeC.$codeD;

	$likecode=$codeA;
	if($codeB!="000") $likecode.=$codeB;
	if($codeC!="000") $likecode.=$codeC;
	if($codeD!="000") $likecode.=$codeD;

	$_cdata="";
	$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' ";
	$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		//접근가능권한그룹 체크
		if($row->group_code=="NO") {
			echo "<html></head><body onload=\"location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
		}
		if(strlen($_ShopInfo->getMemid())==0) {
			if(strlen($row->group_code)>0) {
				echo "<html></head><body onload=\"location.href='".$Dir.FrontDir."login.php?chUrl=".getUrl()."'\"></body></html>";exit;
			}
		} else {
			if($row->group_code!="ALL" && strlen($row->group_code)>0 && $row->group_code!=$_ShopInfo->getMemgroup()) {
				echo "<html></head><body onload=\"alert('해당 카테고리 접근권한이 없습니다.');location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
			}
		}
		$_cdata=$row;
	} else {
		echo "<html></head><body onload=\"location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
	}
	mysql_free_result($result);

	$qry ="WHERE a.productcode LIKE '".$likecode."%' ";
	$qry.="AND a.display='Y' ";
	$qry.="AND a.brand='".$brandcode."' ";
} else {
	$qry.="WHERE a.brand='".$brandcode."' ";
	$qry.="AND a.display='Y' ";
}

$sort=$_REQUEST["sort"];
$listnum=(int)$_REQUEST["listnum"];

if($listnum<=0) $listnum=20;

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = $listnum;

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

$sql = "SELECT codeA, codeB, codeC, codeD FROM tblproductcode ";
if(strlen($_ShopInfo->getMemid())==0) {
	$sql.= "WHERE group_code!='' ";
} else {
	$sql.= "WHERE group_code!='".$_ShopInfo->getMemgroup()."' AND group_code!='ALL' AND group_code!='' ";
}
$result=mysql_query($sql,get_db_conn());
$not_qry="";
while($row=mysql_fetch_object($result)) {
	$tmpcode=$row->codeA;
	if($row->codeB!="000") $tmpcode.=$row->codeB;
	if($row->codeC!="000") $tmpcode.=$row->codeC;
	if($row->codeD!="000") $tmpcode.=$row->codeD;
	$not_qry.= "AND a.productcode NOT LIKE '".$tmpcode."%' ";
}
mysql_free_result($result);

//현재위치
$codenavi=getBCodeLoc($brandcode,$code);

$sql ="SELECT SUBSTRING(a.productcode, 1, 12) AS code ";
$sql.="FROM tblproduct AS a ";
$sql.="LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
$sql.="WHERE a.display='Y' AND a.brand='".$brandcode."' ";
$sql.="AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
$sql.="GROUP BY code ";
$result=mysql_query($sql,get_db_conn());
$brand_qry = "";
$brand_qryA = "";
$brand_qryB = "";
$brand_qryC = "";
$brand_qryD = "";
$leftcode = array();
$blistcodeA = array();
$blistcodeB = array();
$blistcodeC = array();
$blistcodeD = array();
$i=0;
while($row=mysql_fetch_object($result)) {
	$codetempA = substr($row->code,0,3);
	$leftcode[$codetempA] = $codetempA;
	$blistcodeA[$codetempA] = $codetempA;
	$codetempB = substr($row->code,3,3);
	if($codetempB>0) {
		$blistcodeB[$codetempA][$codetempB] = $codetempB;
		$codetempC = substr($row->code,6,3);
		if($codetempC>0) {
			$blistcodeC[$codetempA.$codetempB][$codetempC] = $codetempC;
			$codetempD = substr($row->code,9,3);
			if($codetempD>0) {
				$blistcodeD[$codetempA.$codetempB.$codetempC][$codetempD] = $codetempD;
			}
		}
	}
}
if(count($leftcode)>0) {
	$brand_qry = "AND codeA IN ('".implode("','",$leftcode)."') ";
}
if(count($blistcodeA)>0) {
	$brand_qryA = "AND codeA IN ('".implode("','",$blistcodeA)."') ";
}

$brand_link = "brandcode=".$brandcode."&";


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
$sql = "SELECT leftmenu,body,code FROM tbldesignnewpage ";
$sql.= "WHERE type='brlist' AND (code='".$brandcode."' OR code='ALL') AND leftmenu='Y' ";
$sql.= "ORDER BY code ASC LIMIT 1 ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$_ndata=$row;
mysql_free_result($result);
if($_ndata) {
	$body=$_ndata->body;
	$body=str_replace("[DIR]",$Dir,$body);
	include($Dir.TempletDir."brandproduct/blist_U.php");
} 
?>
</table>
	
</BODY>
</HTML>