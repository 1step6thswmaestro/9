<?
include_once("header.php");
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/member_func.php");
include_once($Dir."m/inc/paging_inc.php");

# 썸네일 관련 파라메터 #
$origloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/product/"; // 원본파일 경로
$saveloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/mobile/"; // 썸내일 저장 경로
$quality = 100;
#썸네일 관련 파라메터 끝 #

$code=isset($_GET['code'])?trim($_GET['code']):"";

$displaymode = isset($_GET['display'])?trim($_GET['display']):"gallery";

#화면 모드 관련 파라메터#
$displaygallery=$displaywebzine=$displaylist="";
switch($displaymode){
	case "list":;
		$displaylist="active";
	break;
	case "webzine":
		$displaywebzine="active";
	break;
	case "gallery":
	default:
		$displaygallery="active";
	break;
}

if(strlen($code)==0) {
	Header("Location:./main.php");
	exit;
}

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

function getCodeLoc($code,$color1="9E9E9E",$color2="9E9E9E") {
	global $_ShopInfo, $Dir,$code;
	$naviitem = array();
	array_push($naviitem,"<A HREF=\"".$Dir.MainDir."main.php\"><span style=\"font-size:12px; color:".$color1.";\">홈</span></A>&nbsp;");

	for($i=0;$i<4;$i++){
		$tmp = array();

		$getsub = ($GLOBALS['code'.chr(65+$i)] == '000');
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

$_cdata="";
$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' ";
$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	//접근가능권한그룹 체크
	if($row->group_code=="NO") {
		echo "<html></head><body onload=\"location.href='./main.php'\"></body></html>";exit;
	}
	if(strlen($_ShopInfo->getMemid())==0) {
		if(strlen($row->group_code)>0) {
			echo "<html></head><body onload=\"location.href='./login.php?chUrl=".getUrl()."'\"></body></html>";exit;
		}
	} else {
		if(strlen($row->group_code)>0 && strpos($row->group_code,$_ShopInfo->getMemgroup())===false) {	//그룹회원만 접근
			echo "<html></head><body onload=\"alert('해당 카테고리 접근권한이 없습니다.');location.href='./main.php'\"></body></html>";exit;
		}
	}
	$_cdata=$row;

	// 미리보기
	if( @!preg_match( 'U', $_cdata->list_type ) AND $preview===true ) {
		$_cdata->list_type = $_cdata->list_type."U";
	}

} else {
	echo "<html></head><body onload=\"location.href='./main.php'\"></body></html>";exit;
}
mysql_free_result($result);

$currentPage = $_REQUEST["page"];
if(!$currentPage) $currentPage = 1; 

$sort=$_REQUEST["sort"];
$listnum=(int)$_REQUEST["listnum"];

if($listnum<=0) $listnum=$_data->prlist_num;

$sql = "SELECT codeA, codeB, codeC, codeD FROM tblproductcode ";
if(strlen($_ShopInfo->getMemid())==0) {
	$sql.= "WHERE group_code!='' ";
} else {
	//$sql.= "WHERE group_code!='".$_ShopInfo->getMemgroup()."' AND group_code!='ALL' AND group_code!='' ";
	$sql.= "WHERE group_code NOT LIKE '%".$_ShopInfo->getMemgroup()."%' AND group_code!='' ";
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
if(eregi("T",$_cdata->type)) {	//가상분류
	$sql = "SELECT productcode FROM tblproducttheme WHERE code LIKE '".$likecode."%' ";
	if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
		$sql.= "ORDER BY date DESC ";
	}
	$result=mysql_query($sql,get_db_conn());
	$t_prcode="";
	while($row=mysql_fetch_object($result)) {
		$t_prcode.=$row->productcode.",";
		$i++;
	}
	mysql_free_result($result);

	//추가 카테고리가 있는지 체크
	$sql = "SELECT productcode FROM tblcategorycode WHERE categorycode LIKE '".$likecode."%' ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		$t_prcode.=$row->productcode.",";
		$i++;
	}
	mysql_free_result($result);
	//# 추가 카테고리가 있는지 체크

	$t_prcode=substr($t_prcode,0,-1);
	$t_prcode=ereg_replace(',','\',\'',$t_prcode);
	$qry.= "AND a.productcode IN ('".$t_prcode."') ";

	$add_query="&code=".$code;
} else {	//일반분류
	$qry.= "AND cc.categorycode LIKE '".$likecode."%' ";
	$add_query="&code=".$code;
}
$qry.="AND a.display='Y' ";

//현재위치
$codenavi=getCodeLoc($code);

?>
<script type="text/javascript" src="./js/cate_ajax.js"></script>
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

function chgListType(str)
{
	location.href="productlist.php?code=<?=$code?>&codeA=<?=$codeA?>&codeB=<?=$codeB?>&codeC=<?=$codeC?>&codeD=<?=$codeD?>&sort=<?=$_GET[sort]?>&list_type="+str
}

function changeDisplayMode(displaymode,code){
	location.href="productlist.php?code="+code+"&display="+displaymode;
	return;
}

//-->
</SCRIPT>
<? include $skinPATH."productlist.php";?>
<form name=form2 method=get action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=list_type value="<?=$list_type?>">
</form>
<? include "footer.php"; ?>