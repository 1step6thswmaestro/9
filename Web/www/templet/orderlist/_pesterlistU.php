<?
$menu_myhome="".$Dir.FrontDir."mypage.php";
$menu_myorder="".$Dir.FrontDir."mypage_orderlist.php";
$menu_mypersonal="".$Dir.FrontDir."mypage_personal.php";
$menu_mywish="".$Dir.FrontDir."wishlist.php";
$menu_myreserve="".$Dir.FrontDir."mypage_reserve.php";
$menu_mycoupon="".$Dir.FrontDir."mypage_coupon.php";
$menu_myinfo="".$Dir.FrontDir."mypage_usermodify.php";
$menu_myout="".$Dir.FrontDir."mypage_memberout.php";
if(getVenderUsed()==true) { $menu_mycustsect=$Dir.FrontDir."mypage_custsect.php"; } 
if($_data->recom_url_ok == "Y" || $_data->sns_ok == "Y"){
	$menu_promote="".$Dir.FrontDir."mypage_promote.php";
}
$menu_gonggu="".$Dir.FrontDir."mypage_gonggu.php";

if(strpos($body,"[IFORDER]")!=0) {
	$ifordernum=strpos($body,"[IFORDER]");
	$endordernum=strpos($body,"[IFENDORDER]");
	$elseordernum=strpos($body,"[IFELSEORDER]");

	$orderstartnum=strpos($body,"[FORORDER]");
	$orderstopnum=strpos($body,"[FORENDORDER]");

	$iforder=substr($body,$ifordernum+9,$orderstartnum-($ifordernum+9))."[ORDERVALUE]".substr($body,$orderstopnum+13,$elseordernum-($orderstopnum+13));

	$noorder=substr($body,$elseordernum+13,$endordernum-$elseordernum-13);

	$mainorder=substr($body,$orderstartnum,$orderstopnum-$orderstartnum+13);

	$productstartnum=strpos($mainorder,"[FORPRODUCT]");
	$productstopnum=strpos($mainorder,"[FORENDPRODUCT]");

	$mainproduct=substr($mainorder,$productstartnum,$productstopnum-$productstartnum+15);

	$ifdelisearchnum=strpos($mainproduct,"[IFDELISEARCH]");
	$enddelisearchnum=strpos($mainproduct,"[IFENDDELISEARCH]");
	$elsedelisearchnum=strpos($mainproduct,"[IFELSEDELISEARCH]");

	$ifdelisearch=substr($mainproduct,$ifdelisearchnum+14,$elsedelisearchnum-($ifdelisearchnum+14));
	$nodelisearch=substr($mainproduct,$elsedelisearchnum+18,$enddelisearchnum-$elsedelisearchnum-18);
	$mainproduct=substr($mainproduct,0,$ifdelisearchnum)."[DELISEARCHVALUE]".substr($mainproduct,$enddelisearchnum+17);

	$mainorder=substr($mainorder,0,$productstartnum)."[ORIGINALPRODUCT]".substr($mainorder,$productstopnum+15);

	$body=substr($body,0,$ifordernum)."[ORIGINALORDER]".substr($body,$endordernum+12);
}

$ordtab1="";
$ordtab2="";
$ordtab3="";
$ordtab4="";
if (preg_match("/\[ORDTAB1([a-zA-Z0-9_?\/\-.]+)\]/",$body,$match)) {
	$ordtab1_tmp=substr($match[1],1);
	$ordtab1_val=explode("_",$ordtab1_tmp);
	$ordtab1_off=$ordtab1_val[0];
	$ordtab1_on=$ordtab1_val[1];
	if(strlen($ordtab1_on)==0) $ordtab1_on=$ordtab1_off;
	$ordtab1="<A HREF=\"".$Dir.FrontDir."mypage_orderlist.php\"><img src=\"".$ordtab1_off."\" border=0></A>";
}
if (preg_match("/\[ORDTAB2([a-zA-Z0-9_?\/\-.]+)\]/",$body,$match)) {
	$ordtab2_tmp=substr($match[1],1);
	$ordtab2_val=explode("_",$ordtab2_tmp);
	$ordtab2_off=$ordtab2_val[0];
	$ordtab2_on=$ordtab2_val[1];
	if(strlen($ordtab2_on)==0) $ordtab2_on=$ordtab2_off;
	$ordtab2="<A HREF=\"".$Dir.FrontDir."mypage_orderlist.php?type=2\"><img src=\"".$ordtab2_off."\" border=0></A>";
}
if (preg_match("/\[ORDTAB3([a-zA-Z0-9_?\/\-.]+)\]/",$body,$match)) {
	$ordtab3_tmp=substr($match[1],1);
	$ordtab3_val=explode("_",$ordtab3_tmp);
	$ordtab3_off=$ordtab3_val[0];
	$ordtab3_on=$ordtab3_val[1];
	if(strlen($ordtab3_on)==0) $ordtab3_on=$ordtab3_off;
	$ordtab3="<A HREF=\"".$Dir.FrontDir."mypage_orderlist.php?type=3\"><img src=\"".$ordtab3_off."\" border=0></A>";
}
if($_data->pester_state =="Y"){
	if (preg_match("/\[ORDTAB4([a-zA-Z0-9_?\/\-.]+)\]/",$body,$match)) {
		$ordtab4_tmp=substr($match[1],1);
		$ordtab4_val=explode("_",$ordtab4_tmp);
		$ordtab4_off=$ordtab4_val[0];
		$ordtab4_on=$ordtab4_val[1];
		if(strlen($ordtab4_on)==0) $ordtab4_on=$ordtab4_off;
		$ordtab4="<A HREF=\"".$Dir.FrontDir."mypage_pesterlist.php\"><img src=\"".$ordtab4_off."\" border=0></A>";
	}
}

$search_btn1="\"javascript:GoSearch('TODAY')\"";
$search_btn2="\"javascript:GoSearch('15DAY')\"";
$search_btn3="\"javascript:GoSearch('1MONTH')\"";
$search_btn4="\"javascript:GoSearch('3MONTH')\"";
$search_btn5="\"javascript:GoSearch('6MONTH')\"";

$search_date ="<select name=\"s_year\" onchange=\"ChangeDate('s')\" style=\"font-size:11px\">\n";
for($i=date("Y");$i>=(date("Y")-2);$i--) {
	$search_date.="<option value=\"".$i."\"";
	if($s_year==$i) $search_date.=" selected";
	$search_date.=" style=\"color:#444444\">".$i."</option>\n";
}
$search_date.="</select> 년\n";
$search_date.="<select name=\"s_month\" onchange=\"ChangeDate('s')\" style=\"font-size:11px\">\n";
for($i=1;$i<=12;$i++) {
	$search_date.="<option value=\"".$i."\"";
	if($s_month==$i) $search_date.=" selected";
	$search_date.=" style=\"color:#444444\">".$i."</option>\n";
}
$search_date.="</select> 월\n";
$search_date.="<select name=\"s_day\" style=\"font-size:11px\">\n";
for($i=1;$i<=get_totaldays($s_year,$s_month);$i++) {
	$search_date.="<option value=\"".$i."\"";
	if($s_day==$i) $search_date.=" selected";
	$search_date.=" style=\"color:#444444\">".$i."</option>\n";
}
$search_date.="</select> 일\n";
$search_date.="~ \n";
$search_date.="<select name=\"e_year\" onchange=\"ChangeDate('e')\" style=\"font-size:11px\">\n";
for($i=date("Y");$i>=(date("Y")-2);$i--) {
	$search_date.="<option value=\"".$i."\"";
	if($e_year==$i) $search_date.=" selected";
	$search_date.=" style=\"color:#444444\">".$i."</option>\n";
}
$search_date.="</select> 년\n";
$search_date.="<select name=\"e_month\" onchange=\"ChangeDate('e')\" style=\"font-size:11px\">\n";
for($i=1;$i<=12;$i++) {
	$search_date.="<option value=\"".$i."\"";
	if($e_month==$i) $search_date.=" selected";
	$search_date.=" style=\"color:#444444\">".$i."</option>\n";
}
$search_date.="</select> 월\n";
$search_date.="<select name=\"e_day\" style=\"font-size:11px\">\n";
for($i=1;$i<=get_totaldays($e_year,$e_month);$i++) {
	$search_date.="<option value=\"".$i."\"";
	if($e_day==$i) $search_date.=" selected";
	$search_date.=" style=\"color:#444444\">".$i."</option>\n";
}
$search_date.="</select> 일\n";

$search_ok="\"javascript:CheckForm()\"";


$s_curtime=mktime(0,0,0,$s_month,$s_day,$s_year);
$e_curtime=mktime(23,59,59,$e_month,$e_day,$e_year);

$sql = "SELECT COUNT(*) as t_count FROM tblpesterinfo WHERE id='".$_ShopInfo->getMemid()."' ";
$sql.= "AND regdate >= '".$s_curtime."' AND regdate <= '".$e_curtime."' ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$t_count = (int)$row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

$sql = "SELECT tempkey, pester_name,pester_tel, state, ordercode, regdate ";
$sql.= "FROM tblpesterinfo WHERE id='".$_ShopInfo->getMemid()."' ";
$sql.= "AND regdate >= '".$s_curtime."' AND regdate <= '".$e_curtime."' ";
$sql.= "ORDER BY regdate DESC ";
$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
$result=mysql_query($sql,get_db_conn());
$cnt=0;
while($row=mysql_fetch_object($result)) {
	$pester_date=date("Y-m-d", $row->regdate);

	$sql = "SELECT productname FROM tblbasket4 a, tblproduct b WHERE a.productcode=b.productcode AND tempkey='".$row->tempkey."' ";
	$result2=mysql_query($sql,get_db_conn());
	while($row2=mysql_fetch_object($result2)) {
		$productname = "<A HREF=\"javascript:PesterDetailPop('".$row->tempkey."')\" onmouseover=\"window.status='주문내역조회';return true;\" onmouseout=\"window.status='';return true;\">".$row2->productname."</a>";
		$pattern=array("(\[PRODUCTNAME\])","(\[FORPRODUCT\])","(\[FORENDPRODUCT\])");
		$replace=array($productname,"","");
		$originalproduct.=preg_replace($pattern,$replace,$tempproduct);
	}
	mysql_free_result($result2);
	$pester_name =.$row->pester_name."(".$row->pester_tel.")";
	if($row->state == "0"){
		$pester_state ="조르기 수락 대기";
	}else if($row->state == "1"){
		$pester_state ="조르기 수락 <A HREF=\"javascript:OrderDetailPop('".$row->ordercode."')\" onmouseover=\"window.status='주문내역조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/mypage_detailview.gif\" border=\"0\" align=\"absmiddle\"></A>\n";
	}else if($row->state == "0"){
		$pester_state ="조르기 수락 <A HREF=\"javascript:OrderDetailPop('".$row->ordercode."')\" onmouseover=\"window.status='주문내역조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/mypage_detailview.gif\" border=\"0\" align=\"absmiddle\"></A>\n";
	}else{
		$pester_state ="조르기 요청한 상품의 품절 및 판매종료";
	}
	$cnt++;

	$pattern=array("(\[PESTER_DATE\])","(\[PESTER_NAME\])","(\[PESTER_STATE\])""(\[ORIGINALPRODUCT\])","(\[FORORDER\])","(\[FORENDORDER\])");
	$replace=array($pester_date,$pester_name,$pester_state,$originalproduct,"","");
	$temporder=preg_replace($pattern,$replace,$temporder);
}
mysql_free_result($result);

if($cnt>0) {
	$originalorder=$iforder;
	$pattern=array("(\[ORDERVALUE\])");
	$replace=array($temporder);
	$originalorder=preg_replace($pattern,$replace,$originalorder);
} else {
	$originalorder=$noorder;
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
		$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><FONT class=\"prlist\">[1...]</FONT></a>&nbsp;&nbsp;";

		$prev_page_exists = true;
	}

	$a_prev_page = "";
	if ($nowblock > 0) {
		$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><FONT class=\"prlist\">[prev]</FONT></a>&nbsp;&nbsp;";

		$a_prev_page = $a_first_block.$a_prev_page;
	}

	// 일반 블럭에서의 페이지 표시부분-시작

	if (intval($total_block) <> intval($nowblock)) {
		$print_page = "";
		for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
			if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
				$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$setup[page_num]) + $gopage)."</font> ";
			} else {
				$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</FONT></a> ";
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
				$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$setup[page_num]) + $gopage)."</FONT> ";
			} else {
				$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</FONT></a> ";
			}
		}
	}		// 마지막 블럭에서의 표시부분-끝


	$a_last_block = "";
	if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
		$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
		$last_gotopage = ceil($t_count/$setup[list_num]);

		$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><FONT class=\"prlist\">[...".$last_gotopage."]</FONT></a>";

		$next_page_exists = true;
	}

	// 다음 10개 처리부분...

	$a_next_page = "";
	if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
		$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><FONT class=\"prlist\">[next]</FONT></a>";

		$a_next_page = $a_next_page.$a_last_block;
	}
} else {
	$print_page = "<FONT class=\"prlist\">1</FONT>";
}

$page=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;


$pattern=array(
	"(\[MENU_MYHOME\])",
	"(\[MENU_MYORDER\])",
	"(\[MENU_MYPERSONAL\])",
	"(\[MENU_MYWISH\])",
	"(\[MENU_MYRESERVE\])",
	"(\[MENU_MYCOUPON\])",
	"(\[MENU_MYINFO\])",
	"(\[MENU_MYOUT\])",
	"(\[MENU_MYCUSTSECT\])",
	"(\[MENU_PROMOTE\])",
	"(\[MENU_GONGGU\])",

	"(\[SEARCH_BTN1\])",
	"(\[SEARCH_BTN2\])",
	"(\[SEARCH_BTN3\])",
	"(\[SEARCH_BTN4\])",
	"(\[SEARCH_BTN5\])",
	"(\[SEARCH_DATE\])",
	"(\[SEARCH_OK\])",

	"(\[ORDTAB1([a-zA-Z0-9_?\/\-.]+)\])",
	"(\[ORDTAB2([a-zA-Z0-9_?\/\-.]+)\])",
	"(\[ORDTAB3([a-zA-Z0-9_?\/\-.]+)\])",
	"(\[ORDTAB4([a-zA-Z0-9_?\/\-.]+)\])",

	"(\[ORIGINALORDER\])",
	"(\[PAGE\])"
);

$replace=array($menu_myhome,$menu_myorder,$menu_mypersonal,$menu_mywish,$menu_myreserve,$menu_mycoupon,$menu_myinfo,$menu_myout,$menu_mycustsect,$menu_promote,$menu_gonggu,$search_btn1,$search_btn2,$search_btn3,$search_btn4,$search_btn5,$search_date,$search_ok,$ordtab1,$ordtab2,$ordtab3,$ordtab4,$originalorder,$page);

$body=preg_replace($pattern,$replace,$body);

echo $body;
?>