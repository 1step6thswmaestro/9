<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$selfcodefont_start = "<font class=\"prselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트 끝

$search=$_REQUEST["search"];
$search1=$_REQUEST["search1"];
$search2=$_REQUEST["search2"];

$codeA=$_REQUEST["codeA"];
$codeB=$_REQUEST["codeB"];
$codeC=$_REQUEST["codeC"];
$codeD=$_REQUEST["codeD"];
$minprice=(int)$_REQUEST["minprice"];
$maxprice=(int)$_REQUEST["maxprice"];
$s_check=$_REQUEST["s_check"];
$search=$_REQUEST["search"];
if(strlen($s_check)==0) $s_check="all";
if($s_check!="all" && $s_check!="keyword" && $s_check!="code" && $s_check!="production" && $s_check!="content" && $s_check!="model" && $s_check!="selfcode") {
	$s_check="all";
}

$likecode="";
if($codeA!="000") $likecode.=$codeA;
if($codeB!="000") $likecode.=$codeB;
if($codeC!="000") $likecode.=$codeC;
if($codeD!="000") $likecode.=$codeD;

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


$qry = "WHERE 1=1 ";
if(strlen($likecode)>0) {
	$qry.= "AND a.productcode LIKE '".$likecode."%' ";
}
if($minprice>0) {
	$qry.= "AND a.sellprice >= ".$minprice." ";
}
if($maxprice>0) {
	$qry.= "AND a.sellprice <= ".$maxprice." ";
}
//검색조건 처리
if(strlen($s_check)>0 && strlen($search)>0) {
	$skeys = explode(" ",$search);
	@setlocale(LC_CTYPE , C);
	for($j=0;$j<count($skeys);$j++) {
		$skeys[$j]=strtoupper(trim($skeys[$j]));
		$skeys[$j] = mysql_real_escape_string($skeys[$j]);
		if(strlen($skeys[$j])>0) {
			if($s_check=="keyword") {
				$qry.= "AND (UPPER(a.productname) LIKE '%".$skeys[$j]."%' OR UPPER(a.keyword) LIKE '%".$skeys[$j]."%') ";
			} else if($s_check=="code") {
				$qry.= "AND a.productcode LIKE '".$skeys[$j]."%' ";
			} else if($s_check=="production") {
				$qry.= "AND UPPER(a.production) LIKE '%".$skeys[$j]."%' ";
			} else if($s_check=="model") {
				$qry.= "AND UPPER(a.model) LIKE '%".$skeys[$j]."%' ";
			} else if($s_check=="selfcode") {
				$qry.= "AND UPPER(a.selfcode) LIKE '%".$skeys[$j]."%' ";
			} else if($s_check=="content") {
				$qry.= "AND UPPER(a.content) LIKE '%".$skeys[$j]."%' ";
			} else if($s_check=="prmsg") {
				$qry.= " AND UPPER(a.prmsg) LIKE '%".$skeys[$j]."%' ";
			} else {
				$qry.= "
					AND
					(
						UPPER(a.productname) LIKE '%".$skeys[$j]."%'
						OR
						UPPER(a.keyword) LIKE '%".$skeys[$j]."%'
						OR
						a.productcode LIKE '".$skeys[$j]."%'
						OR
						UPPER(a.production) LIKE '%".$skeys[$j]."%'
						OR
						UPPER(a.model) LIKE '%".$skeys[$j]."%'
						OR
						UPPER(a.selfcode) LIKE '%".$skeys[$j]."%'
						OR
						UPPER(a.prmsg) LIKE '%".$skeys[$j]."%'
					)
				";
				/*
					// 통합 검색에서 내용은 검색 미포함
						OR
						UPPER(a.content) LIKE '%".$skeys[$j]."%'
				*/
			}
		}
	}


	// 결과내 검색 1
	if( strlen($search1)>0 ){
		$search1 = mysql_real_escape_string($search1);
		$qry.= " AND ( a.productname LIKE '%".$search1."%' OR a.production LIKE '%".$search1."%' OR a.madein LIKE '%".$search1."%' OR a.prmsg LIKE '%".$search1."%' )";

		// 결과내 검색 2
		if( strlen($search2)>0 ){
			$search2 = mysql_real_escape_string($search2);
			$qry.= " AND ( a.productname LIKE '%".$search2."%' OR a.production LIKE '%".$search2."%' OR a.madein LIKE '%".$search2."%' OR a.prmsg LIKE '%".$search2."%' )";
		}
	}

}
$qry.= "AND a.display!='N' ";
if(strlen($not_qry)>0) $qry.= $not_qry;

//$sql = productQuery();

$sql = "SELECT COUNT(*) as t_count ";
$sql.= "FROM tblproduct AS a ";
$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";

$sql.= $qry;
$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";

$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$t_count = (int)$row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

if($minprice==0) $minprice="";
if($maxprice==0) $maxprice="";


$search=str_replace ( "\\", "", $_REQUEST["search"] );
$search1=str_replace ( "\\", "", $_REQUEST["search1"] );
$search2=str_replace ( "\\", "", $_REQUEST["search2"] );
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle." [상품검색]"?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	if(document.form1.search.value.length==0) {
		alert("검색어를 입력하세요.");
		document.form1.search.focus();
		return;
	}
	if(document.form1.search.value.replace(" ","").length==0) {
		alert("검색어를 입력하세요.");
		document.form1.search.value="";
		document.form1.search.focus();
		return;
	}
	if(document.form1.minprice.value.length>0 && !IsNumeric(document.form1.minprice.value)) {
		alert("상품가격은 숫자만 입력 가능합니다.");
		document.form1.minprice.focus();
		return;
	}
	if(document.form1.maxprice.value.length>0 && !IsNumeric(document.form1.maxprice.value)) {
		alert("상품가격은 숫자만 입력 가능합니다.");
		document.form1.maxprice.focus();
		return;
	}
	document.form1.block.value="";
	document.form1.gotopage.value="";
	document.form1.submit();
}

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

function GoPage(block,gotopage) {
	document.form1.block.value=block;
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}
//-->
</SCRIPT>

<?
$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY sequence DESC ";
$i=0;
$ii=0;
$iii=0;
$iiii=0;
$strcodelist = "";
$strcodelist.= "<script>\n";
$result = mysql_query($sql,get_db_conn());
$selcode_name="";
while($row=mysql_fetch_object($result)) {
	$strcodelist.= "var clist=new CodeList();\n";
	$strcodelist.= "clist.codeA='".$row->codeA."';\n";
	$strcodelist.= "clist.codeB='".$row->codeB."';\n";
	$strcodelist.= "clist.codeC='".$row->codeC."';\n";
	$strcodelist.= "clist.codeD='".$row->codeD."';\n";
	$strcodelist.= "clist.type='".$row->type."';\n";
	$strcodelist.= "clist.code_name='".$row->code_name."';\n";
	if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
		$strcodelist.= "lista[".$i."]=clist;\n";
		$i++;
	}
	if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
		if ($row->codeC=="000" && $row->codeD=="000") {
			$strcodelist.= "listb[".$ii."]=clist;\n";
			$ii++;
		} else if ($row->codeD=="000") {
			$strcodelist.= "listc[".$iii."]=clist;\n";
			$iii++;
		} else if ($row->codeD!="000") {
			$strcodelist.= "listd[".$iiii."]=clist;\n";
			$iiii++;
		}
	}
	$strcodelist.= "clist=null;\n\n";
}
mysql_free_result($result);
$strcodelist.= "CodeInit();\n";
$strcodelist.= "</script>\n";

echo $strcodelist;

?>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<form name=form1 method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=sort value="<?=$sort?>">
<?
$leftmenu="Y";
if($_data->design_search=="U") {
	$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='search'";
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
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/search_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/search_title.gif\" border=\"0\" alt=\"상품검색\"></td>\n";
	} else {
		echo "<td>\n";
		/*
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/search_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/search_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/search_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		*/
		echo "<div class=\"subpageTitle\">상품검색</div>";
		echo "</td>\n";
	}
	echo "</tr>\n";
}

echo "<tr>\n";
echo "	<td align=\"center\">\n";
include ($Dir.TempletDir."search/search".$_data->design_search.".php");
echo "	</td>\n";
echo "</tr>\n";
?>
</form>

<form name=form2 method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=codeA value="<?=$codeA?>">
<input type=hidden name=codeB value="<?=$codeB?>">
<input type=hidden name=codeC value="<?=$codeC?>">
<input type=hidden name=codeD value="<?=$codeD?>">
<input type=hidden name=minprice value="<?=$minprice?>">
<input type=hidden name=maxprice value="<?=$maxprice?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=s_check1 value="<?=$s_check1?>">
<input type=hidden name=s_check2 value="<?=$s_check2?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=search1 value="<?=$search1?>">
<input type=hidden name=search2 value="<?=$search2?>">
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