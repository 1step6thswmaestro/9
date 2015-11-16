<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$code=$_REQUEST["code"];
if(strlen($code)>0) {
	$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='community' AND code='".$code."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$isnew=true;
		unset($newobj);
		$newobj->subject=$row->subject;
		$newobj->menu_type=$row->leftmenu;
		$filename=explode("",$row->filename);
		$newobj->member_type=$filename[0];
		$newobj->menu_code=$filename[1];
		$newobj->body=$row->body;
		$newobj->body=str_replace("[DIR]",$Dir,$newobj->body);
		if(strlen($newobj->member_type)>1) {
			$newobj->group_code=$newobj->member_type;
			$newobj->member_type="G";
		}
	}
	mysql_free_result($result);
}
if($isnew!=true) {
	echo "<html><head><title></title></head><body onload=\"alert('해당 페이지가 존재하지 않습니다.');history.go(-1);\"></body></html>";exit;
}
if (strlen($newobj->menu_code)>0 && $newobj->menu_code!="MAI"){
	$boardsep=$newobj->menu_code;
}else{
	switch($code){
		case "1":
			$boardsep="BOU";
		break;
		case "2":
			$boardsep="BOM";
		break;
		default:
			$boardsep="BTC";
		break;
	}
}
if($newobj->member_type=="Y") {
	if(strlen($_ShopInfo->getMemid())==0) {
		Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
		exit;
	}
} else if($newobj->member_type=="G") {
	if(strlen($_ShopInfo->getMemid())==0 || $newobj->group_code!=$_ShopInfo->getMemgroup()) {
		if(strlen($_ShopInfo->getMemid())==0) {
			Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
			exit;
		} else {
			echo "<html><head><title></title></head><body onload=\"alert('해당 페이지 접근권한이 없습니다.');location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
		}
	}
}
?>

<HTML>
<HEAD>
	<TITLE><?=$_data->shoptitle?></TITLE>
	<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
	<META http-equiv="X-UA-Compatible" content="IE=5" />
	<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
	<META name="keywords" content="<?=$_data->shopkeyword?>">
	<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
	<?include($Dir."lib/style.php")?>

	<SCRIPT LANGUAGE="JavaScript">
		<!-- FAQ검색 관련
		function schecked(){
			if(frm.search.value == ''){
				alert('검색어를 입력해 주세요.');
				frm.search.focus();
			}else{
				frm.submit();
			}
			return;
		}
		//-->
	</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
unset($boardval);
for($i=1;$i<=9;$i++) {
	if($num=strpos($newobj->body,"[BOARD".$i)) {
		$boardval[$i]->board_type="Y";
		$boardval[$i]->board_datetype=substr($newobj->body,$num+7,1);
		$boardval[$i]->board_num=(int)substr($newobj->body,$num+8,1);
		$boardval[$i]->board_gan=(int)substr($newobj->body,$num+9,1);
		$boardval[$i]->board_reply=substr($newobj->body,$num+10,1);

		$board_tmp=explode("_",substr($newobj->body,$num+1,strpos($newobj->body,"]",$num)-$num-1));

		$boardval[$i]->board_titlelen=$board_tmp[1];
		$boardval[$i]->board_code=substr($newobj->body,$num+13+strlen($boardval[$i]->board_titlelen),strpos($newobj->body,"]",$num)-$num-13-strlen($boardval[$i]->board_titlelen));

		$boardval[$i]->board_titlelen=(int)$boardval[$i]->board_titlelen;
		if($boardval[$i]->board_num==0) $boardval[$i]->board_num=5;
		if(strlen($boardval[$i]->board_code)==0) $boardval[$i]->board_type="";
	}
}

################## 게시판 #################
$board1=""; $board2=""; $board3=""; $board4=""; $board5=""; $board6=""; $board7=""; $board8=""; $board9="";
for($i=1;$i<=9;$i++) {
	if($boardval[$i]->board_type=="Y") {
		${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
		${"board".$i}.="<tr>\n";
		${"board".$i}.="	<td style=\"padding:5\">\n";

		$sql = "SELECT num, title, writetime FROM tblboard WHERE board='".$boardval[$i]->board_code."' ";
		$sql.= "AND deleted!='1' ";
		if($boardval[$i]->board_reply=="N") $sql.= "AND pos=0 ";
		$sql.= "ORDER BY thread ASC LIMIT ".$boardval[$i]->board_num;
		$result=@mysql_query($sql,get_db_conn());
		$j=0;
		while($row=mysql_fetch_object($result)) {
			$j++;
			$date="";
			if($boardval[$i]->board_datetype=="1") {
				$date="[".date("m/d",$row->writetime)."] ";
			} else if($boardval[$i]->board_datetype=="2") {
				$date="[".date("Y/m/d",$row->writetime)."] ";
			}
			${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0>\n";
			${"board".$i}.="<tr><td>";
			${"board".$i}.="<A HREF=\"".$Dir.BoardDir."board.php?pagetype=view&view=1&board=".$boardval[$i]->board_code."&num=".$row->num."&boardsep=".$boardsep."\" onmouseover=\"window.status='게시글항조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainboard\">".$date.($boardval[$i]->board_titlelen>0?titleCut($boardval[$i]->board_titlelen,$row->title):$row->title)."</FONT></A>";
			${"board".$i}.="</td></tr>\n";
			${"board".$i}.="<tr><td height=".$boardval[$i]->board_gan."></td></tr>\n";
			${"board".$i}.="</table>\n";
		}
		mysql_free_result($result);
		if($j==0) {
			${"board".$i}.="<table border=0 cellpadding=0 cellspacing=0>\n";
			${"board".$i}.="<tr><td align=center class=\"mainboard\">등록된 게시글이 없습니다.</td></tr>";
			${"board".$i}.="</table>";
		}
		${"board".$i}.="	</td>\n";
		${"board".$i}.="</tr>\n";
		${"board".$i}.="</table>\n";
	}
}



################## 상품평 #################
if($_data->review_type!="N") {
	//////////////////////////////////////////////////////////////////////////////////////////
	if($num=strpos($newobj->body,"[REVIEW")) {
		$review_ordertype=(int)substr($newobj->body,$num+7,1);
		$review_displaytype=(int)substr($newobj->body,$num+8,1);
		$review_datetype=(int)substr($newobj->body,$num+9,1);
		$review_num=(int)substr($newobj->body,$num+10,1);
		$review_gan=(int)substr($newobj->body,$num+11,1);
		$review_marks=substr($newobj->body,$num+12,1);

		$review_tmp=explode("_",substr($newobj->body,$num+1,strpos($newobj->body,"]",$num)-$num-1));
		$review_titlelen=(int)$review_tmp[1];

		if($review_num==0) $review_num=5;
		if($review_titlelen==0) $review_titlelen=40;
		if($review_reply!="Y") $review_reply=="N";
	}

	$review ="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	$review.="<tr>\n";
	$review.="	<td>\n";

	$qry = "WHERE 1=1 ";
	if($_data->review_type=="A") $qry.= "WHERE display='Y' ";

	//$sql = "SELECT * FROM tblproductreview ";
	$sql = "SELECT a.num, a.id, a.name, a.marks, a.date, a.content, b.productcode, b.productname, b.tinyimage, b.quantity, b.selfcode ";
	$sql.= "FROM tblproductreview a ";
	$sql.= "LEFT OUTER JOIN tblproduct b ON a.productcode=b.productcode ";
	$sql.= $qry;
	if($review_ordertype=="1") {
		$sql.= "ORDER BY marks DESC ";
	} else {
		$sql.= "ORDER BY date DESC ";
	}
	$sql.= "LIMIT " . $review_num;
	$result=mysql_query($sql,get_db_conn());

	$j=0;
	while($row=@mysql_fetch_object($result)) {
		$date="";
		if($review_datetype=="1") {
			$date=substr($row->date,4,2)."/".substr($row->date,6,2);
		} else if($review_datetype=="2") {
			$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
		}

		$marks="";
		if($review_marks =="Y") {
			for($i=0;$i<$row->marks;$i++) {
				//$marks.="<FONT color=#000000>★</FONT>";
				$marks.="<img src=\"/images/003/star_point1.gif\" alt=\"\" />";
			}
			for($i=$row->marks;$i<5;$i++) {
				//$marks.="<FONT color=#CACACA>★</FONT>";
				$marks.="<img src=\"/images/003/star_point2.gif\" alt=\"\" />";
			}
			$marks = " ".$marks;
		}

		$reviewlink="";
		if($review_displaytype == "1") {
			$reviewlink = $Dir.FrontDir."productdetail.php?productcode=".$row->productcode;
			$reviewonclick = "";
		} else {
			$reviewlink = "javascript:;";
			$reviewonclick = "onclick=\"window.open('".$Dir.FrontDir."review_popup.php?prcode=".$row->productcode."&num=".$row->num."','','width=450,height=400,scrollbars=yes');\"";
		}

		$content=explode("=",$row->content);
		$titlestr = titleCut($review_titlelen, $content[0]);

		$review.="<table border=0 cellpadding=0 cellspacing=0 width=\"100%\">\n";
		$review.="
			<tr>
				<td width=\"70\" style=\"text-align:center; border-bottom:1px solid #eeeeee;\"><A HREF=\"".$reviewlink."\" ".$reviewonclick." onmouseover=\"window.status='상품평조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"/data/shopimages/product/".$row->tinyimage."\" width=\"60\" border=\"0\" alt=\"\" /></a></td>
				<td style=\"border-bottom:1px solid #eeeeee; padding-bottom:".$review_gan."px; word-break:break-all;\">
					<A HREF=\"".$reviewlink."\" ".$reviewonclick." onmouseover=\"window.status='상품평조회';return true;\" onmouseout=\"window.status='';return true;\"><span style=\"color:#222222; font-weight:bold;\">".$row->productname."</span><br />
					<span>".$titlestr."</span></A>
					<div style=\"color:#999999; padding-top:5px;\">".$row->name.", ".$date."</div>
				</td>
				<td style=\"border-bottom:1px solid #eeeeee;\" align=\"right\">".$marks."</td>
			</tr>\n
		";
		$review.="<tr><td colspan=\"3\" height=".$review_gan."></td></tr>\n";
		$review.="</table>\n";
		$j++;
	}

	if($j==0) {
		$review.="<table border=0 cellpadding=0 cellspacing=0>\n";
		$review.="<tr><td align=center class=\"mainboard\">등록된 상품평이 없습니다.</td></tr>";
		$review.="</table>";
	}

	$review.="	</td>\n";
	$review.="</tr>\n";
	$review.="</table>\n";
}

/* FAQ 검색폼 */
$faqformstart = "
	<FORM method=\"GET\" name=\"frm\" action=\"/board/board.php\">
		<input type=\"hidden\" name=\"pagetype\" value=\"list\" />
		<input type=\"hidden\" name=\"board\" value=\"faq\" />
		<input type=\"hidden\" name=\"s_check\" value=\"c\" />
";
$faqinputbox="<input type=\"text\" name=\"search\" value=\"\" />";
$faqsearch="javascript:schecked();";
$faqformend = "</FORM>";

$pattern=array(
	"(\[BOARD1([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
	"(\[BOARD2([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
	"(\[BOARD3([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
	"(\[BOARD4([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
	"(\[BOARD5([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
	"(\[BOARD6([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
	"(\[BOARD7([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
	"(\[BOARD8([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
	"(\[BOARD9([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})_([_a-zA-Z0-9-]{0,})\])",
	"(\[REVIEW([0-1]{1})([0-1]{1})([0-2]{1})([1-9]{1})([0-9]{1})([YN]{1})_([0-9]{0,3})\])",
	"(\[FAQFORMSTART\])",
	"(\[FAQINPUTBOX\])",
	"(\[FAQSEARCH\])",
	"(\[FAQFORMEND\])"
);
$replace=array($board1,$board2,$board3,$board4,$board5,$board6,$board7,$board8,$board9,$review,$faqformstart,$faqinputbox,$faqsearch,$faqformend);






// 말머리
if(strpos($newobj->body,"[BOARD_SUB_CATE:")!=0) {

	$boardSubCateKey = explode( "]", substr( $newobj->body, strpos($newobj->body,"[BOARD_SUB_CATE:")+16 ) );
	$boardSubCateKey = $boardSubCateKey[0];

	$subCateSQL = "SELECT `subCategory` FROM `tblboardadmin` WHERE `board` = '".$boardSubCateKey."' ;";
	$subCateRes = mysql_query($subCateSQL,get_db_conn());
	$subCateRow = mysql_fetch_assoc ($subCateRes);
	$subCategoryArray = explode(",",$subCateRow[subCategory]);

	$subCategoryList = "";

	if( count($subCategoryArray) > 0 AND strlen($subCategoryArray[0]) > 0 ) {
		if( $boardRow['subCategory'] AND $num > 0 ) $selSubCategory = $boardRow['subCategory'];
		if( $_GET['subCategory'] ) $selSubCategory = $_GET['subCategory'];
		$subCategoryList .= "<select name='subCategory' onChange=\"if(this.value){location.href='/board/board.php?pagetype=list&board=".$boardSubCateKey."&subCategory='+this.value;}\">";
		$subCategoryList .= "<option value=''>카테고리 선택</option>";
		foreach ($subCategoryArray as $V) {
			$V = trim($V);
			$subCategoryList .= "<option value=\"".$V."\" ".$sel.">".$V."</option>";
		}
		$subCategoryList .= "</select>";
	}

}
array_push($pattern,"(\[BOARD_SUB_CATE\:([a-zA-Z0-9-]{0,})\])");
array_push($replace,$subCategoryList);




$newobj->body=preg_replace($pattern,$replace,$newobj->body);

if($newobj->menu_type=="Y") {
	include ($Dir.MainDir.$_data->menu_type.".php");
	echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	echo "<tr>\n";
	echo "	<td valign=top>\n";
	echo $newobj->body;
	echo "	</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	include ($Dir."lib/bottom.php");
} else if($newobj->menu_type=="T" && $_data->frame_type!="N") {
	include ($Dir.MainDir."nomenu.php");
	echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	echo "<tr>\n";
	echo "	<td valign=top>\n";
	echo $newobj->body;
	echo "	</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	include ($Dir."lib/bottom.php");
} else {
	echo $newobj->body;
}
?>

</BODY>
</HTML>