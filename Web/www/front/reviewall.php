<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if ($_data->ETCTYPE["REVIEW"]!="Y") {
	echo "<html><head><title></title></head><body onload=\"alert('사용후기 모음 게시판을 이용할 수 없습니다.');location.href='".$Dir."main/main.php';\"></body></html>";exit;
}

$reviewlist=$_data->ETCTYPE["REVIEWLIST"];
$reviewdate=$_data->ETCTYPE["REVIEWDATE"];
if(strlen($reviewlist)==0) $reviewlist="N";

$tmp_filter=explode("#",$_data->filter);
$filter_array=explode("REVIEWROW",$tmp_filter[1]);
$reviewrow=(int)$filter_array[1];
if($reviewrow<8) $reviewrow=8;

$code=$_REQUEST["code"];

$codeA=(substr($code,0,3)!=""?substr($code,0,3):"000");
$codeB=(substr($code,3,3)!=""?substr($code,3,3):"000");
$codeC=(substr($code,6,3)!=""?substr($code,6,3):"000");
$codeD=(substr($code,9,3)!=""?substr($code,9,3):"000");

$sort=(int)$_POST["sort"];
$listnum=(int)$_POST["listnum"];
$reviewtype= !_empty($_POST['reviewtype'])?trim($_POST['reviewtype']):"";
if($sort>1) $sort=0;	//0:최근등록순, 1:높은평점순
if($listnum<=0) $listnum=$reviewrow;

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

?>

<HTML>
<HEAD>
	<TITLE><?=$_data->shoptitle?> - 사용후기</TITLE>
	<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
	<META http-equiv="X-UA-Compatible" content="IE=5" />
	<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
	<META name="keywords" content="<?=$_data->shopkeyword?>">
	<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
	<?include($Dir."lib/style.php")?>
	<SCRIPT LANGUAGE="JavaScript">
		<!--
		function select_code(code) {
			document.form1.code.value=code;
			document.form1.submit();
		}

		function change_sort(val) {
			document.form2.sort.value=val;
			document.form2.block.value="";
			document.form2.gotopage.value="";
			document.form2.submit()
		}

		function change_listnum(val) {
			document.form2.listnum.value=val;
			document.form2.block.value="";
			document.form2.gotopage.value="";
			document.form2.submit()
		}

		function GoPage(block,gotopage) {
			document.form2.block.value = block;
			document.form2.gotopage.value = gotopage;
			document.form2.submit();
		}

		function CheckForm() {
			if(document.form1.prcode2.value.length==0) {
				alert("상품 선택을 하세요.");
				document.form1.prcode2.focus();
				return;
			}
			document.form1.code.value=document.form1.prcode2.value;
			document.form1.submit();
		}

		function view_review(cnt) {
			if(typeof(document.all.reviewspan)=="object" && typeof(document.all.reviewspan.length)!="undefined") {
				for(i=0;i<document.all.reviewspan.length;i++) {
					if(cnt==i) {
						if(document.all.reviewspan[i].style.display=="none") {
							document.all.reviewspan[i].style.display="block";
						} else {
							document.all.reviewspan[i].style.display="none";
						}
					} else {
						document.all.reviewspan[i].style.display="none";
					}
				}
			} else {
				if(document.all.reviewspan.style.display=="none") {
					document.all.reviewspan.style.display="block";
				} else {
					document.all.reviewspan.style.display="none";
				}
			}
		}

		function reviewSelect(type){
			var _form = document.form2;

			_form.reviewtype.value = type;
			_form.submit();

			return;
		}

		function review_open(prcode,num) {
			window.open("<?=$Dir.FrontDir?>review_popup.php?prcode="+prcode+"&num="+num,"","width=450,height=400,scrollbars=yes");
		}
		//-->
	</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<?
$colspan=6;
if($reviewdate!="N") $colspan=7;

$newdesign="";
$sql="SELECT body FROM ".$designnewpageTables." WHERE type='reviewall'";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$body=$row->body;
	$body=str_replace("[DIR]",$Dir,$body);
	$newdesign="Y";
} else {
	if(file_exists($Dir.TempletDir."review/review.php")) {
		$fp=fopen($Dir.TempletDir."review/review.php","r");
		if($fp) {
			while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
		}
		fclose($fp);
		$body=$buffer;
	}
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
if($num=strpos($body,"[PRCODE_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$prcode_style=$s_tmp[1];
}

if(strlen($codeA_style)==0) $codeA_style="width:150px";
if(strlen($codeB_style)==0) $codeB_style="width:150px";
if(strlen($codeC_style)==0) $codeC_style="width:150px";
if(strlen($codeD_style)==0) $codeD_style="width:150px";
if(strlen($prcode_style)==0)$prcode_style="width:312px";

if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/reviewall_title.gif")) {
	$review_title="<img src=\"".$Dir.DataDir."design/reviewall_title.gif\" border=0 alt=\"사용후기\">";
} else {
	/*
	$review_title.="<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
	$review_title.="<TR>\n";
	$review_title.="	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/reviewall_title_head.gif ALT=></TD>\n";
	$review_title.="	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/reviewall_title_bg.gif></TD>\n";
	$review_title.="	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/reviewall_title_tail.gif ALT=></TD>\n";
	$review_title.="</TR>\n";
	$review_title.="</TABLE>\n";
	*/
	$review_title.="<div class=\"subpageTitle\">사용후기 전체보기</div>";
}

$codeA_select="";
$codeA_select.="<select name=codeA2 onchange=\"select_code(options.value)\" style=\"".$codeA_style."\">\n";
$codeA_select.="<option value=\"\"> 1차분류 선택</option>\n";
if(strlen($_ShopInfo->getMemid())==0) {
	$add_qry="AND group_code='' ";
} else {
	$add_qry ="AND (group_code='".$_ShopInfo->getMemgroup()."' OR group_code='ALL' ";
	$add_qry.="OR group_code='') ";
}
$is_codeB=true;
$sql = "SELECT codeA, codeB, codeC, codeD, type, code_name FROM tblproductcode ";
$sql.= "WHERE codeB='000' AND codeC='000' ";
$sql.= "AND codeD='000' AND (type='L' OR type='LX') ".$add_qry;
$result=mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)) {
	$selA="";
	if($codeA==$row->codeA) {
		$selA="selected";
		if($row->type=="LX") $is_codeB=false;
	}
	$codeA_select.="<option value=\"".$row->codeA.$row->codeB.$row->codeC.$row->codeD."\" ".$selA.">".$row->code_name."</option>\n";
}
mysql_free_result($result);
$codeA_select.="</select>\n";

$codeB_select="";
$codeB_select.="<select name=codeB2 onchange=\"select_code(options.value)\" style=\"".$codeB_style."\">\n";
$is_codeC=true;
if($is_codeB==false) {
	$codeB_select.="<option value=\"\">단일분류</option>\n";
	$is_codeC=false;
} else {
	$codeB_select.="<option value=\"".$codeA."\"> 2차분류 선택</option>\n";
	if(strlen($codeA)==3 && $codeA!="000") {
		$sql = "SELECT codeA, codeB, codeC, codeD, type, code_name FROM tblproductcode ";
		$sql.= "WHERE codeA='".$codeA."' ";
		$sql.= "AND codeB!='000' AND codeC='000' ";
		$sql.= "AND codeD='000' AND (type='LM' OR type='LMX') ".$add_qry;
		$result=mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)) {
			$selB="";
			if($codeB==$row->codeB) {
				$selB="selected";
				if($row->type=="LMX") $is_codeC=false;
			}
			$codeB_select.="<option value=\"".$row->codeA.$row->codeB.$row->codeC.$row->codeD."\" ".$selB.">".$row->code_name."</option>\n";
		}
		mysql_free_result($result);
	}
}
$codeB_select.="</select>\n";

$codeC_select="";
$codeC_select.="<select name=codeC2 onchange=\"select_code(options.value)\" style=\"".$codeC_style."\">\n";
$is_codeD=true;
if($is_codeC==false) {
	$codeC_select.="<option value=\"\">단일분류</option>\n";
	$is_codeD=false;
} else {
	$codeC_select.="<option value=\"".$codeA.$codeB."\"> 3차분류 선택</option>\n";
	if(strlen($codeA)==3 && $codeA!="000" && strlen($codeB)==3 && $codeB!="000") {
		$sql = "SELECT codeA, codeB, codeC, codeD, type, code_name FROM tblproductcode ";
		$sql.= "WHERE codeA='".$codeA."' ";
		$sql.= "AND codeB='".$codeB."' AND codeC!='000' AND codeD='000' ";
		$sql.= "AND (type='LM' OR type='LMX') ".$add_qry;
		$result=mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)) {
			$selC="";
			if($codeC==$row->codeC) {
				$selC="selected";
				if($row->type=="LMX") $is_codeD=false;
			}
			$codeC_select.="<option value=\"".$row->codeA.$row->codeB.$row->codeC.$row->codeD."\" ".$selC.">".$row->code_name."</option>\n";
		}
		mysql_free_result($result);
	}
}
$codeC_select.="</select>\n";

$codeD_select="";
$codeD_select.="<select name=codeD2 onchange=\"select_code(options.value)\" style=\"".$codeD_style."\">\n";
if($is_codeD==false) {
	$codeD_select.="<option value=\"\">단일분류</option>\n";
} else {
	$codeD_select.="<option value=\"".$codeA.$codeB.$codeC."\"> 4차분류 선택</option>\n";
	if(strlen($codeA)==3 && $codeA!="000" && strlen($codeB)==3 && $codeB!="000" && strlen($codeC)==3 && $codeC!="000") {
		$sql = "SELECT codeA, codeB, codeC, codeD, type, code_name FROM tblproductcode ";
		$sql.= "WHERE codeA='".$codeA."' ";
		$sql.= "AND codeB='".$codeB."' AND codeC='".$codeC."' AND codeD!='000' ";
		$sql.= "AND (type='LM' OR type='LMX') ".$add_qry;
		$result=mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)) {
			$selD="";
			if($codeD==$row->codeD) {
				$selD="selected";
			}
			$codeD_select.="<option value=\"".$row->codeA.$row->codeB.$row->codeC.$row->codeD."\" ".$selD.">".$row->code_name."</option>\n";
		}
		mysql_free_result($result);
	}
}
$codeD_select.="</select>\n";

$prcode_select="";
$prcode_select.="<select name=prcode2 style=\"".$prcode_style."\">\n";
$prcode_select.="<option value=\"\"> 상품 선택</option>\n";
$product_likecode=$codeA.$codeB.$codeC.$codeD;
if($is_codeD==false || $codeD!="000") {
	$sql = "SELECT a.productcode, a.productname ";
	$sql.= "FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode LIKE '".$product_likecode."%' AND a.display!='N' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	$sql.= "ORDER BY a.date DESC ";

	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		$selP="";
		if($code==$row->productcode) {
			$selP="selected";
		}
		$prcode_select.="<option value=\"".$row->productcode."\" ".$selP.">".$row->productname."</option>\n";
	}
	mysql_free_result($result);
}
$prcode_select.="</select>\n";

$searchok="CheckForm()";




$review_list="";
if($num=strpos($body,"[LIST_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$imgwidth=(int)$s_tmp[1];
}
if($imgwidth<10) $imgwidth=55;


#상품평 타입 선택 탭
$addsql = "";
$sphotoreview = $sbestreview = $sbasicreview = $sallreview = "tabOff";

switch($reviewtype){
	case "photo":
		$sphotoreview = "tabOn";
		$addsql .= "AND img IS NOT NULL AND img != '' ";
	break;
	case "best":
		$sbestreview = "tabOn";
		$addsql .= "AND best = 'Y' ";
	break;
	case "basic":
		$sbasicreview = "tabOn";
		$addsql .= "AND img IS NULL OR img = '' ";
	break;
	case "all":
	default:
		$sallreview = "tabOn";
	break;
}


$review_list.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
$review_list.="<tr>\n";
$review_list.="	<td style=\"font-size:11px;letter-spacing:-0.5pt;\">\n";
$review_list.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
$review_list.="	<tr>\n";
$review_list.="		<td style=\"color:#FF4C00;font-size:11px;letter-spacing:-0.5pt;\">\n";


$review_list.="			<div class=\"button ".$sallreview."\"><a href=\"javascript:reviewSelect('all');\">전체상품평</a></div>";
$review_list.="			<div class=\"button ".$sbestreview."\"><a href=\"javascript:reviewSelect('best');\">베스트상품평</a></div>";
$review_list.="			<div class=\"button ".$sphotoreview."\"><a href=\"javascript:reviewSelect('photo');\">포토상품평</a></div>";
$review_list.="			<div class=\"button ".$sbasicreview."\"><a href=\"javascript:reviewSelect('basic');\">일반상품평</a></div>";

$review_list.="		</td>\n";
$review_list.="		<td align=right>\n";
$review_list.="		<select name=sort onchange=\"change_sort(options.value)\" class=\"select\">\n";
$review_list.="		<option value=0 ";if($sort=="0")$review_list.="selected";$review_list.=">최근등록순</option>\n";
$review_list.="		<option value=1 ";if($sort=="1")$review_list.="selected";$review_list.=">높은평점순</option>\n";
$review_list.="		</select>\n";
$review_list.="		&nbsp;\n";
$review_list.="		<select name=listnum onchange=\"change_listnum(options.value)\" class=\"select\">\n";
$review_list.="		<option value=10 ";if($listnum=="10")$review_list.="selected";$review_list.=">10개씩 정렬</option>\n";
$review_list.="		<option value=20 ";if($listnum=="20")$review_list.="selected";$review_list.=">20개씩 정렬</option>\n";
$review_list.="		<option value=30 ";if($listnum=="30")$review_list.="selected";$review_list.=">30개씩 정렬</option>\n";
$review_list.="		<option value=40 ";if($listnum=="40")$review_list.="selected";$review_list.=">40개씩 정렬</option>\n";
$review_list.="		</select>\n";
$review_list.="		</td>\n";
$review_list.="	</tr>\n";
$review_list.="	<tr><td height=5></td></tr>\n";
$review_list.="	<tr>\n";
$review_list.="		<td colspan=\"2\" style=\"font-size:11px;\">\n";
$review_list.="		* 상품리뷰를 클릭하시면 전체내용을 확인하실 수 있습니다.\n";
$review_list.="		</td>\n";
$review_list.="	</tr>\n";
$review_list.="	</table>\n";
$review_list.="	</td>\n";
$review_list.="</tr>\n";
$review_list.="<tr><td height=3></td></tr>\n";
$review_list.="<tr>\n";
$review_list.="	<td>\n";
$review_list.="	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
$review_list.="	<col width=40></col>\n";
$review_list.="	<col width=80></col>\n";
$review_list.="	<col width=1></col>\n";
$review_list.="	<col width=></col>\n";
$review_list.="	<col width=80></col>\n";
if($reviewdate!="N") {
	$review_list.="<col width=80></col>\n";
}
$review_list.="	<col width=80></col>\n";
$review_list.="	<tr><td height=\"2\" background=\"".$Dir."images/common/review/reviewall_line1.gif\" colspan=\"".$colspan."\"></td></tr>\n";
$review_list.="	<tr height=\"30\" align=\"center\" bgcolor=\"#F8F8F8\" style=\"letter-spacing:-0.5pt;\">\n";
$review_list.="		<td><font color=\"#333333\"><b>번호</b></font></td>\n";
$review_list.="		<td><font color=\"#333333\"><b>이미지</b></font></td>\n";
$review_list.="		<td></td>\n";
$review_list.="		<td><font color=\"#333333\"><b>상품명/사용후기</b></font></td>\n";
$review_list.="		<td><font color=\"#333333\"><b>작성자</b></font></td>\n";
if($reviewdate!="N") {
	$review_list.="	<td><font color=\"#333333\"><b>작성일</b></font></td>\n";
}
$review_list.="		<td><font color=\"#333333\"><b>평점</b></font></td>\n";
$review_list.="	</tr>\n";
$review_list.="	<tr><td height=\"1\" background=\"".$Dir."images/common/review/reviewall_line2.gif\" colspan=\"".$colspan."\"></td></tr>\n";

$likecode="";
if(strlen($code)==18) $likecode=$code;
else {
	if($codeA!="000") $likecode.=$codeA;
	if($codeB!="000") $likecode.=$codeB;
	if($codeC!="000") $likecode.=$codeC;
	if($codeD!="000") $likecode.=$codeD;
}


	$qry = "WHERE 1=1 ";
	if(strlen($likecode)>0) {
		$qry.= "AND a.productcode LIKE '".$likecode."%' ";
	}
	$qry.= "AND a.productcode=b.productcode ";
	if($_data->review_type=="A") $qry.= "AND a.display='Y' ";
	$qry.= "AND b.display='Y' ";
	
	
	$sql = "SELECT COUNT(*) as t_count ";
	$sql.= "FROM tblproductreview a, tblproduct b ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
	$sql.= $qry;
	$sql.= $addsql;
	$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$t_count = (int)$row->t_count;
	mysql_free_result($result);
	$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

	$sql = "SELECT a.num,a.id,a.name,a.marks,a.date,a.content,b.productcode,b.productname,b.tinyimage,b.quantity,b.selfcode ";
	$sql.= "FROM tblproductreview a, tblproduct b ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
	$sql.= $qry;
	$sql.= $addsql;
	$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
	if($sort==0) $sql.= "ORDER BY a.date DESC ";
	else if($sort==1) $sql.= "ORDER BY marks DESC ";
	$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
	$result=mysql_query($sql,get_db_conn());
	$cnt=0;
	while($row=mysql_fetch_object($result)) {
		$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);

		$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
		$content=explode("=",$row->content);
		$review_list.="<tr id=\"A".$row->productcode."\" onmouseover=\"quickfun_show(this,'A".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'A".$row->productcode."','none')\">\n";
		$review_list.="	<td style=\"color:#333333;padding-bottom:3pt;padding-top:3pt;line-height:18px;\" align=center>".$number."</td>\n";
		$review_list.="	<td style=\"color:#333333;padding-bottom:3pt;padding-top:3pt;line-height:18px;\" align=center>";
		if(strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
			$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
			$review_list.="<img src=\"".$Dir.DataDir."shopimages/product/".$row->tinyimage."\" border=0 ";
			if ($width[0]>=$width[1] && $width[0]>$imgwidth) $review_list.=" width=$imgwidth ";
			else if ($width[0]<$width[1] && $width[1]>$imgwidth) $review_list.=" height=$imgwidth ";
			$review_list.="></td>";
		} else {
			$review_list.="<img src=\"".$Dir."images/no_img.gif\" border=0 width=$imgwidth></td>";
		}
		$review_list.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','A','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
		$review_list.="	<td style=\"color:#333333;padding-bottom:3pt;padding-top:3pt;line-height:18px;\">\n";
		$review_list.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
		$linkpage="";
		if($reviewlist=="Y") {
			$linkpage="<A HREF=\"javascript:view_review(".$cnt.")\">";
		} else {
			$linkpage="<A HREF=\"javascript:review_open('".$row->productcode."',".$row->num.")\">";
		}
		$review_list.="	<tr><td style=\"padding-left:5\">".$linkpage."<FONT COLOR=\"#373737\"><B>".viewselfcode($row->productname,$row->selfcode)."</B></FONT></A> <A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/review/btn_reviewprview.gif\" border=0 align=absmiddle></A></td></tr>\n";
		$review_list.="	<tr><td style=\"padding-left:5;padding-top:5\">";
		if($reviewlist=="Y") {
			$review_list.="<A HREF=\"javascript:view_review(".$cnt.")\">".titleCut(45,$content[0])."</A>";
		} else {
			$review_list.="<A HREF=\"javascript:review_open('".$row->productcode."',".$row->num.")\">".titleCut(45,$content[0])."</A>";
		}
		if(strlen($content[1])>0) $review_list.="<img src=\"".$Dir."images/common/review/review_replyicn.gif\" border=0 align=absmiddle>";
		$review_list.="	</td></tr>\n";
		$review_list.="	</table>\n";
		$review_list.="	</td>\n";
		$review_list.="	<td style=\"color:#333333;padding-bottom:3pt;padding-top:3pt;line-height:18px;\" align=center>".$row->name."</td>";
		if($reviewdate!="N") {
			$review_list.="	<td style=\"color:#333333;padding-bottom:3pt;padding-top:3pt;line-height:18px;\" align=center>".$date."</td>";
		}
		$review_list.="	<td style=\"color:#333333;padding-bottom:3pt;padding-top:3pt;line-height:18px;\" align=center>";
		for($i=0;$i<$row->marks;$i++) $review_list.="<FONT color=#000000>★</FONT>";
		for($i=$row->marks;$i<5;$i++) {
			$review_list.="<FONT color=#DEDEDE>★</FONT>";
		}
		$review_list.="	</td>";
		$review_list.="</tr>\n";
		if($reviewlist=="Y") {
			$review_list.="<tr>\n";
			$review_list.="	<td></td>\n";
			$review_list.="	<td></td>\n";
			$review_list.="	<td></td>\n";
			$review_list.="	<td style=\"padding-left:10;\">\n";
			$review_list.="	<div id=reviewspan style=\"display:none;padding:7px 0px;background:#f9f9f9;border-top:1px solid #f2f2f2;\">\n";
			$review_list.="	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n";
			$review_list.="		<tr>\n";
			if(!empty($row->img) && file_exists($Dir.DataDir."shopimages/productreview/".$row->img)){
				$review_list.="<td><img src=\"".$Dir.DataDir."shopimages/productreview/".$row->img."\" border=\"0\" width=\"269\" /></td>\n";
			}
			$review_list.="<td width=\"100%\" valign=\"top\" style=\"padding:15px;\">".nl2br($content[0]);
			if(strlen($content[1])>0) {
				$review_list.="	<br/><br/><img src=\"".$Dir."images/common/review/review_replyicn2.gif\" align=absmiddle border=0> ".nl2br($content[1]);
			}
			$review_list.="</td>";
			$review_list.="	</table>\n";
			$review_list.="	</div>\n";
			$review_list.="	</td>\n";
			$review_list.="	<td></td>\n";
			$review_list.="	<td></td>\n";
			if($reviewdate!="N") {
				$review_list.="	<td></td>\n";
			}
			$review_list.="</tr>\n";
		}
		$review_list.="<tr><td height=\"1\" background=\"".$Dir."images/common/review/reviewall_line2.gif\" colspan=\"".$colspan."\"></td></tr>\n";

		$cnt++;
	}
	mysql_free_result($result);

	if ($cnt==0) {
		$review_list.="<tr height=30><td class=lineleft colspan=".$colspan." align=center>검색된 상품리뷰가 없습니다.</td></tr>";
		$review_list.="<tr><td height=\"1\" background=\"".$Dir."images/common/review/reviewall_line2.gif\" colspan=\"".$colspan."\"></td></tr>\n";
	}

	$total_block = intval($pagecount / $setup[page_num]);

	if (($pagecount % $setup[page_num]) > 0) {
		$total_block = $total_block + 1;
	}

	$total_block = $total_block - 1;

	if (ceil($t_count/$setup[list_num]) > 0) {
		// 이전	x개 출력하는 부분-시작
		$a_first_block = "";
		if ($nowblock > 0) {
			$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\">[1...]</a>&nbsp;&nbsp;";

			$prev_page_exists = true;
		}

		$a_prev_page = "";
		if ($nowblock > 0) {
			$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\">[prev]</a>&nbsp;&nbsp;";

			$a_prev_page = $a_first_block.$a_prev_page;
		}

		// 일반 블럭에서의 페이지 표시부분-시작

		if (intval($total_block) <> intval($nowblock)) {
			$print_page = "";
			for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
				if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
					$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></font> ";
				} else {
					$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
				}
			}
		} else {
			if (($pagecount % $setup[page_num]) == 0) {
				$lastpage = $setup[page_num];
			} else {
				$lastpage = $pagecount % $setup[page_num];
			}

			for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
				if (intval($nowblock*$setup[page_num]) + $gopage == intval($gotopage)) {
					$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></FONT> ";
				} else {
					$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
				}
			}
		}		// 마지막 블럭에서의 표시부분-끝


		$a_last_block = "";
		if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
			$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
			$last_gotopage = ceil($t_count/$setup[list_num]);

			$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\">[...".$last_gotopage."]</a>";

			$next_page_exists = true;
		}

		// 다음 10개 처리부분...

		$a_next_page = "";
		if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
			$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

			$a_next_page = $a_next_page.$a_last_block;
		}
	} else {
		$print_page = "<B>1</B>";
	}

	$review_page="";
	$review_page.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	$review_page.="<tr>\n";
	$review_page.="	<td align=center>\n";
	$review_page.="		".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
	$review_page.="	</td>\n";
	$review_page.="</tr>\n";
	$review_page.="</table>\n";

$review_list.="	</table>\n";
$review_list.="	</td>\n";
$review_list.="</tr>\n";
$review_list.="</table>\n";

$reviewbody ="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
$reviewbody.="<form name=form1 action=\"".$_SERVER[PHP_SELF]."\" method=post>\n";
$reviewbody.="<input type=hidden name=code value=\"".$code."\">\n";
$reviewbody.="<input type=hidden name=sort value=\"".$sort."\">\n";
$reviewbody.="<input type=hidden name=listnum value=\"".$listnum."\">\n";
$reviewbody.="<tr>\n";
$reviewbody.="	<td align=center>".$body."</td>\n";
$reviewbody.="</tr>\n";
$reviewbody.="</form>\n";
$reviewbody.="<form name=form2 method=post action=\"".$_SERVER[PHP_SELF]."\">\n";
$reviewbody.="<input type=hidden name=code value=\"".$code."\">\n";
$reviewbody.="<input type=hidden name=sort value=\"".$sort."\">\n";
$reviewbody.="<input type=hidden name=listnum value=\"".$listnum."\">\n";
$reviewbody.="<input type=hidden name=block value=\"".$block."\">\n";
$reviewbody.="<input type=hidden name=gotopage value=\"".$gotopage."\">\n";
$reviewbody.="<input type=hidden name=reviewtype value=\"".$reviewtype."\">\n";
$reviewbody.="</form>\n";
$reviewbody.="</table>\n";

$pattern=array("(\[DIR\])","(\[REVIEW_TITLE\])","(\[CODEA((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])","(\[CODEB((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])","(\[CODEC((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])","(\[CODED((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])","(\[PRCODE((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])","(\[SEARCHOK\])","(\[LIST((\_){0,1})([0-9]{2})\])","(\[PAGE\])");
$replace=array($Dir,$review_title,$codeA_select,$codeB_select,$codeC_select,$codeD_select,$prcode_select,$searchok,$review_list,$review_page);
$reviewbody = preg_replace($pattern,$replace,$reviewbody);

echo $reviewbody;
?>

<? include ($Dir."lib/bottom.php") ?>

<div id="create_openwin" style="display:none"></div>

</BODY>
</HTML>