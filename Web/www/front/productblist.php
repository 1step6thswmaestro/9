<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
//include_once($Dir."lib/cache_product.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");


$brandcode=$_REQUEST["brandcode"];
$rcode=$_REQUEST["code"];

if($_data->ETCTYPE["BRANDPRO"]!="Y" || strlen($brandcode)==0) {
	Header("Location:".$Dir.MainDir."main.php");
	exit;
}

$brandpagemark = "Y"; // 브랜드 전용 페이지
$selfcodefont_start = "<font class=\"prselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트 끝

$code = '';
$likecode='';
if(!empty($rcode)){
	for($i=0;$i<4;$i++){
		$tcode = substr($rcode,$i*3,3);
		if(strlen($tcode) != 3 || $tcode == '000'){
			$tcode = '000';
		}else{
			$likecode.=$tcode;
		}
		${'code'.chr(65+$i)} = $tcode;
		$code.=$tcode;
	}
}

/*
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
*/

function getBCodeLoc($brandcode,$color1="9E9E9E",$color2="9E9E9E") {
	global $_ShopInfo, $Dir,$code;
	$naviitem = array();

	array_push($naviitem,"<A HREF=\"".$Dir.FrontDir."productbmap.php\"><span style=\"font-size:12px; color:".$color1.";\">BRAND INDEX</span></A>&nbsp;");
	//<FONT COLOR=\"".$color1."\">></FONT>
	$sql = "SELECT brandname FROM tblproductbrand WHERE bridx='".$brandcode."' ";
	if(false === $result=mysql_query($sql,get_db_conn())) return '';
	if(mysql_num_rows($result) < 1)  return '';
	array_push($naviitem,"<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$brandcode."\"><span style=\"font-size:12px; color:".$color1.";\"> ".mysql_result($result,0,0)."</span></A>&nbsp;");


	for($i=0;$i<4;$i++){
		$tmp = array();

		$getsub = ($GLOBALS['code'.chr(65+$i)] == '000' || empty($GLOBALS['code'.chr(65+$i)]));
		$tmp = getCategoryItems(substr($code,0,$i*3),true);

		if(is_array($tmp) && count($tmp) > 0 && count($tmp['items']) > 0){
			$str = '&nbsp;<select name="code'.chr(65+$i).'"  id="code'.chr(65+$i).'" onChange="javascript:chgNaviCode('.$i.')">';
			if($tmp['depth'] != $i){
				exit('System Error');
			}
			$sel = '';
			if($getsub)  $str .= '<option value="">-----------------</option>';
			foreach($tmp['items'] as $item){
				if($sel != 'ok'){
					for($j=0;$j<=$i;$j++){
						if($j >0 && $sel != 'selected') break;
						if($item['code'.chr(65+$j)] == $GLOBALS['code'.chr(65+$j)]) $sel = 'selected';
						else $sel = '';
					}
				}

				if($sel == 'selected'){
					$str .= '<option value="'.$item['code'.chr(65+$i)].'" selected>'.$item['code_name'].'</option>';
					$sel = 'ok';
				}else{
					$str .= '<option value="'.$item['code'.chr(65+$i)].'" >'.$item['code_name'].'</option>';
				}
			}
			$str .= '</select>';
			array_push($naviitem,$str);
		}
		if($getsub) break;
	}
	return implode('&nbsp;<FONT COLOR="'.$color1.'">></FONT>',$naviitem);
}

$_bdata="";
$sql = "SELECT * FROM tblproductbrand ";
$sql.= "WHERE bridx='".$brandcode."' ";
$result=mysql_query($sql,get_db_conn());
$brow=mysql_fetch_object($result);
$_bdata=$brow;

if(strlen($code)>0) {
	/*
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
*/
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

<HTML>
<HEAD>
<TITLE><?=$_data->shopname." [".$_bdata->brandname."]"?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/drag.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function ClipCopy(url) {
	var tmp;
	tmp = window.clipboardData.setData('Text', url);
	if(tmp) {
		alert('주소가 복사되었습니다.');
	}
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
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}

function ChangeNum(obj) {
	document.form2.listnum.value=obj.value;
	document.form2.submit();
}

function chgNaviCode(dp){
	var code = '';
	dp = parseInt(dp);
	if(dp > 4) dp = 4
	for(i=0;i<=dp;i++){
		var el = document.getElementById('code'+String.fromCharCode(65+i));
		if(el){
			code += el.options[el.selectedIndex].value;
		}else{
			break;
		}
	}
	document.codeNaviForm.code.value = code;
	document.codeNaviForm.submit();
}
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td>
<?
	if(strlen($_bdata->list_type)==4) {
		include($Dir.TempletDir."brandproduct/blist_".$_bdata->list_type.".php");
	} else if (strlen($_bdata->list_type)==5 && substr($_bdata->list_type,4,5)=="U") {
		//leftmenu : 적용여부
		$sql = "SELECT leftmenu,body,code FROM ".$designnewpageTables." ";
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
		} else {
			include($Dir.TempletDir."brandproduct/blist_".substr($_bdata->list_type,0,5).".php");
		}
	}
?>
	</td>
</tr>
<form name=form2 method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=brandcode value="<?=$brandcode?>">
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<form name="codeNaviForm" id="codeNaviForm" action="<?=$_SERVER['PHP_SELF']?>">
<input type="hidden" name="brandcode" value="<?=$_REQUEST['brandcode']?>">
<input type="hidden" name="code" value="">
</form>

</table>

<? include ($Dir."lib/bottom.php") ?>

<div id="create_openwin" style="display:none"></div>

</BODY>
</HTML>

<? if($HTML_CACHE_EVENT=="OK") ob_end_flush(); ?>