<?
$menu_myhome="".$Dir.FrontDir."mypage.php";
$menu_myorder="".$Dir.FrontDir."mypage_orderlist.php";
$menu_mypersonal="".$Dir.FrontDir."mypage_personal.php";
$menu_mywish="".$Dir.FrontDir."wishlist.php";
$menu_myreserve="".$Dir.FrontDir."mypage_reserve.php";
$menu_mycoupon="".$Dir.FrontDir."mypage_coupon.php";
$menu_myinfo="".$Dir.FrontDir."mypage_usermodify.php";
$menu_myout="".$Dir.FrontDir."mypage_memberout.php";
if(getVenderUsed()==true) {
	$menu_mycustsect=$Dir.FrontDir."mypage_custsect.php";
} 
if($_data->recom_url_ok == "Y" || $_data->sns_ok == "Y"){
	$menu_promote="".$Dir.FrontDir."mypage_promote.php";
}
$menu_gonggu="".$Dir.FrontDir."mypage_gonggu.php";


$checkall="javascript:CheckAll()";
$checkdelete="javascript:goDeleteMinishop()";
$checkmailyes="javascript:addAgreeMailAll()";
$checkmailno="javascript:delAgreeMailAll()";

$t_count=0;
$pagecount=0;
if(strpos($body,"[IFMINI]")!=0) {
	$ifmininum=strpos($body,"[IFMINI]");
	$endmininum=strpos($body,"[IFENDMINI]");
	$elsemininum=strpos($body,"[IFELSEMINI]");

	$ministartnum=strpos($body,"[FORMINI]");
	$ministopnum=strpos($body,"[FORENDMINI]");

	$ifmini=substr($body,$ifmininum+8,$ministartnum-($ifmininum+8))."[MINIVALUE]".substr($body,$ministopnum+12,$elsemininum-($ministopnum+12));

	$nomini=substr($body,$elsemininum+12,$endmininum-$elsemininum-12);

	$mainmini=substr($body,$ministartnum,$ministopnum-$ministartnum+12);

	$ifmailoknum=strpos($mainmini,"[IFMAILOK]");
	$endmailoknum=strpos($mainmini,"[IFENDMAILOK]");
	$elsemailoknum=strpos($mainmini,"[IFELSEMAILOK]");

	$ifmailok=substr($mainmini,$ifmailoknum+10,$ministartnum-($ifmininum+10))."[MINIVALUE]".substr($body,$ministopnum+14,$elsemininum-($ministopnum+14));

	$nomailok=substr($mainmini,$elsemailoknum+14,$endmailoknum-$elsemailoknum-14);
	$yesmailok=substr($mainmini,$ifmailoknum+10,$elsemailoknum-($ifmailoknum+10));

	$mainmini=substr($mainmini,0,$ifmailoknum)."[MAILOKVALUE]".substr($mainmini,$endmailoknum+13);
	
	$body=substr($body,0,$ifmininum)."[ORIGINALMINI]".substr($body,$endmininum+11);

	$qry = "WHERE a.id='".$_ShopInfo->getMemid()."' AND a.vender=b.vender ";

	$sql = "SELECT COUNT(*) as t_count FROM tblregiststore a, tblvenderstore b ".$qry;
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$t_count = $row->t_count;
	mysql_free_result($result);
	$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

	$sql = "SELECT a.vender, a.email_yn, b.id, b.brand_name, b.hot_used, b.hot_linktype ";
	$sql.= "FROM tblregiststore a, tblvenderstore b ".$qry." ";
	$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
	$result = mysql_query($sql,get_db_conn());
	$cnt=0;
	while($row=mysql_fetch_object($result)) {
		$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);

		$mini_checkbox="<input type=checkbox name=sels value=\"".$row->vender."\">";
		$mini_link="javascript:GoMinishop('".(MinishopType=="ON"?$Dir."minishop/".$row->id:$Dir."minishop.php?storeid=".$row->id)."')";
		$mini_logoimg="";
		if(file_exists($Dir.DataDir."shopimages/vender/logo_".$row->vender.".gif")) {
			$mini_logoimg="<img src=".$Dir.DataDir."shopimages/vender/logo_".$row->vender.".gif border=0>";
		} else {
			$mini_logoimg="<img src=".$Dir."images/minishop/logo.gif border=0>";
		}
		$mini_mail="";
		$mailok="";
		if($row->email_yn=="Y") {
			$mini_mail="수신";
			$mailok="javascript:miniMailAgree('del',".$row->vender.")";
			$mailokvalue=$yesmailok;
		} else {
			$mini_mail="거부";
			$mailok="javascript:miniMailAgree('add',".$row->vender.")";
			$mailokvalue=$nomailok;
		}
		$mini_name=$row->brand_name;

		$minihot="";
		if (preg_match("/\[MINIHOT([1-6]{0,1})\]/",$mainmini,$match)) {
			$minihotnum=$match[1];
			if(strlen($minihotnum)==0) $minihotnum=3;
			$minihot.="<table border=0 cellpadding=0 cellspacing=0 style=\"table-layout:fixed\">\n";
			$minihot.="<tr>\n";
			if($row->hot_used=="1") {
				unset($hot_prcode);
				unset($isnot_hotspecial);
				$sql = "SELECT a.productcode,a.productname,a.sellprice,a.consumerprice,a.reserve,a.production, ";
				$sql.= "a.option_price, a.tag, a.minimage, a.tinyimage, a.etctype, a.option_price FROM tblproduct AS a ";
				$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
				$sql.= "WHERE 1=1 ";
				if($row->hot_linktype=="2") {
					$sql2 = "SELECT special_list FROM tblvenderspecialmain WHERE vender='".$row->vender."' AND special='3' ";
					$result2=mysql_query($sql2,get_db_conn());
					if($row2=mysql_fetch_object($result2)) {
						$hot_prcode=ereg_replace(',','\',\'',$row2->special_list);
					}
					mysql_free_result($result2);
					if(strlen($hot_prcode)>0) {
						$sql.= "AND a.productcode IN ('".$hot_prcode."') ";
					} else {
						$isnot_hotspecial=true;
					}
				}
				$sql.= "AND a.vender='".$row->vender."' AND a.display='Y' ";
				$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
				if($row->hot_linktype=="1" || $isnot_hotspecial==true) {
					$sql.= "ORDER BY a.sellcount DESC ";
				} else if($_minidata->hot_linktype=="2") {
					$sql.= "ORDER BY FIELD(a.productcode,'".$hot_prcode."') ";
				}
				$sql.= "LIMIT 3 ";
				$result2=mysql_query($sql,get_db_conn());
				while($row2=mysql_fetch_object($result2)) {
					$minihot.="<td width=80 align=center style=\"padding:7,0\">\n";
					$minihot.="<table border=0 cellpadding=0 cellspacing=0 style=\"table-layout:fixed\">\n";
					$minihot.="<tr>\n";
					$minihot.="	<td width=62 height=62 style=\"border:1px #dddddd solid\">\n";
					$minihot.="	<A HREF=\"javascript:GoPrdtItem('".$row2->productcode."')\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
					if(strlen($row2->tinyimage)!=0 && file_exists($Dir.DataDir."shopimages/product/".$row2->tinyimage)){
						$file_size=getImageSize($Dir.DataDir."shopimages/product/".$row2->tinyimage);
						$minihot.="<img src=\"".$Dir.DataDir."shopimages/product/".$row2->tinyimage."\"";
						if($file_size[0]>=$file_size[1]) $minihot.=" width=60";
						else $minihot.=" height=60";
						$minihot.=" border=0></a>";
					} else {
						$minihot.="<img src=\"".$Dir."images/no_img.gif\" width=60 border=0></a>";
					}
					$minihot.="	</td>\n";
					$minihot.="</tr>\n";
					$minihot.="<tr>\n";
					$minihot.="	<td align=center style=\"font-size:8pt;padding-top:5\">\n";
					$minihot.="	<A HREF=\"javascript:GoPrdtItem('".$row2->productcode."')\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">".str_replace("...","..",titleCut(20,strip_tags($row2->productname)))."</A>";
					$minihot.="	</td>\n";
					$minihot.="</tr>\n";
					$minihot.="<tr><td align=center style=\"font-size:8pt;color:red;padding-top:5\"><B>".number_format($row2->sellprice)."</B></td></tr>\n";
					$minihot.="</table>\n";
					$minihot.="</td>\n";
				}
				mysql_free_result($result2);
			} else {
				$minihot.="<td align=center>HOT 추천상품이 없습니다.</td>\n";
			}
			$minihot.="</tr>\n";
			$minihot.="</table>\n";
		}

		$tempmini.=$mainmini;

		$cnt++;

		$pattern=array("(\[MAILOK\])");
		$replace=array($mailok);
		$mailokvalue=preg_replace($pattern,$replace,$mailokvalue);

		$pattern=array("(\[MINI_CHECKBOX\])","(\[MINI_LINK\])","(\[MINI_LOGOIMG\])","(\[MINI_MAIL\])","(\[MAILOK\])","(\[MINI_NAME\])","(\[MAILOKVALUE\])","(\[MINIHOT([1-6]{0,1})\])","(\[FORMINI\])","(\[FORENDMINI\])");
		$replace=array($mini_checkbox,$mini_link,$mini_logoimg,$mini_mail,$mailok,$mini_name,$mailokvalue,$minihot,"","");

		$tempmini=preg_replace($pattern,$replace,$tempmini);
	}
	mysql_free_result($result);
	if($cnt>0) {
		$originalmini=$ifmini;
		$pattern=array("(\[MINIVALUE\])");
		$replace=array($tempmini);
		$originalmini=preg_replace($pattern,$replace,$originalmini);
	} else {
		$originalmini=$nomini;
	}
}

if($cnt>0) {
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
}

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

	"(\[CHECKALL\])",
	"(\[CHECKDELETE\])",
	"(\[CHECKMAILYES\])",
	"(\[CHECKMAILNO\])",
	"(\[ORIGINALMINI\])",
	"(\[PAGE\])"
);

$replace=array($menu_myhome,$menu_myorder,$menu_mypersonal,$menu_mywish,$menu_myreserve,$menu_mycoupon,$menu_myinfo,$menu_myout,$menu_mycustsect,$menu_promote,$menu_gonggu,$checkall,$checkdelete,$checkmailyes,$checkmailno,$originalmini,$page);

$body=preg_replace($pattern,$replace,$body);

echo $body;

?>