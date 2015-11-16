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
if(strpos($body,"[IFRESERVE]")!=0) {
	$ifreservenum=strpos($body,"[IFRESERVE]");
	$endreservenum=strpos($body,"[IFENDRESERVE]");
	$elsereservenum=strpos($body,"[IFELSERESERVE]");

	$reservestartnum=strpos($body,"[FORRESERVE]");
	$reservestopnum=strpos($body,"[FORENDRESERVE]");
	$ifreserve=substr($body,$ifreservenum+11,$reservestartnum-($ifreservenum+11))."[RESERVEVALUE]".substr($body,$reservestopnum+15,$elsereservenum-($reservestopnum+15));

	$reserve_useadd=substr($body,$elsereservenum+15,$endreservenum-$elsereservenum-15);

	$mainreserve=substr($body,$reservestartnum,$reservestopnum-$reservestartnum+15);

	$body=substr($body,0,$ifreservenum)."[ORIGINALRESERVE]".substr($body,$endreservenum+14);
}

if(strpos($body,"[IFRESERVECASH]")!=0) {
	$ifreservecashnum=strpos($body,"[IFRESERVECASH]");
	$endreservecashnum=strpos($body,"[IFENDRESERVECASH]");

	$originalreservecash=substr($body,$ifreservecashnum+15,$endreservecashnum-($ifreservecashnum+15));
	$body=substr($body,0,$ifreservecashnum)."[ORIGINALRESERVECASH]".substr($body,$endreservecashnum+18);
	if($_data->cr_ok=='Y') {
		$pattern=array("(\[RESERVECASH\])","(\[RESERVECASHLINK\])");
		$replace=array(number_format($_data->cr_unit),$Dir.FrontDir."mypage_cash01.php");
		$originalreservecash=preg_replace($pattern,$replace,$originalreservecash);
	}
}

$reserve=number_format($reserve);
$maxreserve=number_format($maxreserve);


$sql = "SELECT COUNT(*) as t_count FROM tblreserve ";
$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
$sql.= "AND date >= '".$s_curdate."' AND date <= '".$e_curdate."' ";
$result = mysql_query($sql,get_db_conn());
$row = mysql_fetch_object($result);
$t_count = $row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

$sql = "SELECT * FROM tblreserve WHERE id='".$_ShopInfo->getMemid()."' ";
$sql.= "AND date >= '".$s_curdate."' AND date <= '".$e_curdate."' ";
$sql.= "ORDER BY date DESC LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
$result=mysql_query($sql,get_db_conn());
$cnt=0;
while($row=mysql_fetch_object($result)) {
	$tempreserve.=$mainreserve;

	$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
	$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);

	$ordercode="";
	$orderprice="";
	$orderdata=$row->orderdata;
	if(strlen($orderdata)>0) {
		$tmpstr=explode("=",$orderdata);
		$ordercode=$tmpstr[0];
		$orderprice=$tmpstr[1];
	}

	$content="";
	if(strlen($ordercode)>0) {
		$content="<A HREF=\"javascript:OrderDetailPop('".$ordercode."')\">".$row->content."</A>";
	} else {
		$content=$row->content;
	}
	if(strlen($orderprice)>0 && $orderprice>0) {
		$ordprice=number_format($orderprice);
	} else {
		$ordprice="-";
	}

	if(strpos($tempreserve,"[IFWON]")!=0){
		if(strlen($orderprice)>0 && $orderprice>0) {
			$pattern=array("(\[IFWON\])","(\[IFENDWON\])");
			$replace=array("","");
			$tempreserve=preg_replace($pattern,$replace,$tempreserve);
		} else {
			$ifnum=strpos($tempreserve,"[IFWON]");
			$endnum=strpos($tempreserve,"[IFENDWON]")+10;
			$tempreserve=substr($tempreserve,0,$ifnum).substr($tempreserve,$endnum);
		}
	}

	$price=number_format($row->reserve);

	$cnt++;

	$pattern=array("(\[DATE\])","(\[CONTENT\])","(\[ORDPRICE\])","(\[PRICE\])","(\[FORRESERVE\])","(\[FORENDRESERVE\])");
	$replace=array($date,$content,$ordprice,$price,"","");

	$tempreserve=preg_replace($pattern,$replace,$tempreserve);
}
mysql_free_result($result);

if($cnt>0) {
	$originalreserve=$ifreserve;
	$pattern=array("(\[RESERVEVALUE\])");
	$replace=array($tempreserve);
	$originalreserve=preg_replace($pattern,$replace,$originalreserve);
} else {
	$originalreserve=$reserve_useadd;
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

	"(\[ID\])",
	"(\[NAME\])",
	"(\[RESERVE\])",
	"(\[MAXRESERVE\])",
	"(\[ORIGINALRESERVE\])",
	"(\[ORIGINALRESERVECASH\])",
	"(\[PAGE\])"
);

$replace=array($menu_myhome,$menu_myorder,$menu_mypersonal,$menu_mywish,$menu_myreserve,$menu_mycoupon,$menu_myinfo,$menu_myout,$menu_mycustsect,$menu_promote,$menu_gonggu,$id,$name,$reserve,$maxreserve,$originalreserve,$originalreservecash, $page);

$body=preg_replace($pattern,$replace,$body);

echo $body;

?>