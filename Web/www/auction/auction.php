<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$auctionimagepath=$Dir.DataDir."shopimages/auction/";


$auct_num=(int)$_data->auct_num; #경매상품 진열개수
$auct_sort=(int)$_data->auct_sort; #경매목록 화면의 상품정렬 방법 (0:경매마감일역순, 1:경매가격순, 2:경매등록일역순, 3:경매마감일순)
$auct_moveday=(int)$_data->auct_moveday; #마감된 경매, 마감경매 목록으로 이동되는 기간

if($auct_num==0) $auct_num=10;

if($auct_moveday==0) $curdate = date("YmdHis");
else $curdate = date("YmdHis",mktime(0,0,0,date("m"),date("d")-$auct_moveday,date("Y")));

$sort=$_REQUEST["sort"];
if(strlen($sort)==0) $sort=$auct_sort;

$type=$_REQUEST["type"];
if(strlen($type)==0 || $type!="complete") $type="";

$tname0="auction_menu1.gif";
$tname1="auction_menu2.gif";
if($type=="complete") {
	$tname1="auction_menu1r.gif";
} else {
	$tname0="auction_menu1r.gif";
}
?>
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 경매</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<?
if ($_data->title_type=="Y") {
	echo "<td><img src=\"".$Dir.DataDir."design/auction_title.gif\" border=\"0\" alt=\"경매\"></td>\n";
} else {
	echo "<td>\n";
	echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
	echo "<TR>\n";
	echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/auction_title_head.gif ALT=></TD>\n";
	echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/auction_title_bg.gif></TD>\n";
	echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/auction_title_tail.gif ALT=></TD>\n";
	echo "</TR>\n";
	echo "</TABLE>\n";
	echo "</td>\n";
}
?>
</tr>
<tr>
	<td style="padding-left:10px;padding-right:10px;">
	<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td align="right">
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td><a href="auction.php"><img src="images/<?=$tname0?>" border="0"></a></td>
			<td><a href="auction.php?type=complete"><img src="images/<?=$tname1?>" border="0"></a></td>
			<td><a href="myauction.php"><img src="images/auction_menu3.gif" border="0"></a></td>
			<td><a href="myauction.php?type=complete"><img src="images/auction_menu4.gif" border="0"></a></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="2" bgcolor="#000000"></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
<?
		if($type=="complete") echo "<img src=\"images/icon1.gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">마감된 경매물품입니다.";
		else echo "<img src=\"images/icon1.gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">현재 진행중인 경매물품입니다.";
?>
			</td>
			<td align="right"><FONT color="#000000">정렬방식 : </FONT><select name="sort" onChange="location.href='<?=$PHP_SELF?>?type=<?=$type?>&sort='+this.options[this.selectedIndex].value;" style="font-size:11px;letter-spacing:-0.5pt;background-color:#404040;">
				<?if($type!="complete"){?>
				<option value="0" <?if($sort=="0")echo"selected";?> style="color:#ffffff;">마감 임박순</option>
				<?} else {?>
				<option value="0" <?if($sort=="0")echo"selected";?> style="color:#ffffff;">마감일순</option>
				<?}?>
				<option value="1" <?if($sort=="1")echo"selected";?> style="color:#ffffff;">마감일 역순</option>
				<option value="2" <?if($sort=="2")echo"selected";?> style="color:#ffffff;">낮은 가격순</option>
				<option value="3" <?if($sort=="3")echo"selected";?> style="color:#ffffff;">높은 가격순</option>
				<option value="4" <?if($sort=="4")echo"selected";?> style="color:#ffffff;">많은 입찰자순</option>
			</select></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="2"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%" style="padding-top:0pt; padding-right:0; padding-bottom:0; padding-left:0pt;">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<col width="80"></col>
			<col></col>
			<col width="80"></col>
			<col width="120"></col>
			<col width="60"></col>
			<tr>
				<td height="2" bgcolor="#000000" colspan="5"></td>
			</tr>
			<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
				<td><font color="#000000"><b>물품사진</b></font></td>
				<td><font color="#000000"><b>물품명</b></font></td>
				<td><font color="#000000"><b><?=($type=="complete"?"낙찰가":"현재가")?></b></font></td>
				<td><font color="#000000"><b>경매종료일</b></font></td>
				<td><font color="#000000"><b>입찰수</b></font></td>
			</tr>
			<tr>
				<td height="1" colspan="5" bgcolor="#DDDDDD"></td>
			</tr>
<?
	//리스트 세팅
	$setup[page_num] = 10;
	$setup[list_num] = $auct_num;

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

	$today=date("YmdHis");
	$sql = "SELECT COUNT(*) as t_count FROM tblauctioninfo ";
	if ($type=="complete") $sql.= "WHERE end_date < '".$curdate."' ";
	else $sql.= "WHERE start_date <= '".$today."' AND end_date > '".$curdate."' ";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$t_count = $row->t_count;
	mysql_free_result($result);
	$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

	$sql = "SELECT * FROM tblauctioninfo ";
	if ($type=="complete") $sql.= "WHERE end_date < '".$curdate."' ";
	else $sql.= "WHERE start_date <= '".$today."' AND end_date > '".$curdate."' ";
	if($sort=="0") $sql.= "ORDER BY end_date ASC ";
	else if($sort=="1") $sql.= "ORDER BY end_date DESC ";
	else if($sort=="2") $sql.= "ORDER BY last_price ASC ";
	else if($sort=="3") $sql.= "ORDER BY last_price DESC ";
	else if($sort=="4") $sql.= "ORDER BY bid_cnt DESC ";
	$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
	$result = mysql_query($sql,get_db_conn());
	$i=0;
	while($row=mysql_fetch_object($result)) {
		$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
		$i++;
		$auct_bgcolor = "#FFFFFF";
		if ($i % 2 == 0) $auct_bgcolor = "#FAFAFA";
		$end_date=substr($row->end_date,4,2)."/".substr($row->end_date,6,2)." ".substr($row->end_date,8,2).":".substr($row->end_date,10,2);

		$time=mktime((substr($row->end_date,8,2)*1),(substr($row->end_date,10,2)*1),0,(substr($row->end_date,4,2)*1),(substr($row->end_date,6,2)*1),(substr($row->end_date,0,4)*1));

		echo "<tr align=\"center\">\n";
		echo "	<td style=\"padding-bottom:3px;padding-top:3px;\">";
		if(strlen($row->product_image)>0 && file_exists($auctionimagepath.$row->product_image)) {
			echo "<img src=\"".$auctionimagepath.$row->product_image."\" border=\"0\" ";
			$size=GetImageSize($auctionimagepath.$row->product_image);
			if(($size[0]>70 || $size[1]>60) && $size[0]>$size[1]) {
				echo " width=\"70\"";
			} else if($size[0]>70 || $size[1]>60) {
				echo " height=\"60\"";
			}
			echo "></td>\n";
		} else {
			echo "<img src=\"images/product_no_img.gif\" width=70 height=60 border=0></td>\n";
		}
		echo "	<td align=\"left\" style=\"padding-bottom:3pt;padding-top:3pt;\"><A HREF=\"auction_detail.php?seq=".$row->auction_seq."&start_date=".$row->start_date."&view=1&type=".$type."&sort=".$sort."&block=".$block."&gotopage=".$gotopage."\"><font color=\"#333333\">&nbsp;".$row->auction_name."&nbsp;</font></A></td>\n";
		echo "	<td style=\"padding-right:5\" style=\"padding-bottom:3pt;padding-top:3pt;\"><font color=\"#333333\"><B>".number_format($row->last_price)."원</B></font></td>\n";
		if (intval($time - 60*60*23) < time()) {
			echo "	<td align=\"center\" style=\"padding-bottom:3pt;padding-top:3pt;\"><FONT COLOR=\"#FF0000\">".$end_date."</FONT></td>\n";
		} else {
			echo "	<td align=\"center\" style=\"padding-bottom:3pt;padding-top:3pt;\"><font color=\"#333333\">".$end_date."</font></td>\n";
		}
		echo "	<td style=\"padding-bottom:3pt;padding-top:3pt;\"><font color=\"#333333\">".$row->bid_cnt."</font></td>\n";
		echo "</tr>\n";
		echo "<tr><td height=\"1\" colspan=\"5\" bgcolor=\"#DDDDDD\"></td></tr>";
	}
	mysql_free_result($result);
	if($i==0) {
		if($type=="complete") $msg="마감된 경매물품이 없습니다.";
		else $msg="진행중인 경매물품이 없습니다.";
		echo "<tr><td height=\"30\" colspan=\"5\" align=\"center\"><font color=\"#333333\">".$msg."</font></td></tr>\n";
		echo "<tr><td height=\"1\" colspan=\"5\" bgcolor=\"#DDDDDD\"></td></tr>";
	}
?>
			</table>
			</td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
<?
	$total_block = intval($pagecount / $setup[page_num]);
	if (($pagecount % $setup[page_num]) > 0) {
		$total_block = $total_block + 1;
	}
	$total_block = $total_block - 1;
	if (ceil($t_count/$setup[list_num]) > 0) {
		$a_first_block = "";
		if ($nowblock > 0) {
			$a_first_block .= "<a href='".$_SERVER[PHP_SELF]."?block=0&gotopage=1&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\">[1...]</a>&nbsp;&nbsp;";
			$prev_page_exists = true;
		}
		$a_prev_page = "";
		if ($nowblock > 0) {
			$a_prev_page .= "<a href='".$_SERVER[PHP_SELF]."?block=".($nowblock-1)."&gotopage=".($setup[page_num]*($block-1)+$setup[page_num])."&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\">[prev]</a>&nbsp;&nbsp;";
			$a_prev_page = $a_first_block.$a_prev_page;
		}
		if (intval($total_block) <> intval($nowblock)) {
			$print_page = "";
			for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
				if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
					$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></font> ";
				} else {
					$print_page .= "<a href='".$_SERVER[PHP_SELF]."?block=".$nowblock."&gotopage=".(intval($nowblock*$setup[page_num]) + $gopage)."&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
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
					$print_page .= "<a href='".$_SERVER[PHP_SELF]."?block=".$nowblock."&gotopage=".(intval($nowblock*$setup[page_num]) + $gopage)."&type=".$type."&sort=".$sort."' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
				}
			}
		}
		$a_last_block = "";
		if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
			$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
			$last_gotopage = ceil($t_count/$setup[list_num]);

			$a_last_block .= "&nbsp;&nbsp;<a href='".$_SERVER[PHP_SELF]."?block=".$last_block."&gotopage=".$last_gotopage."&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\">[...".$last_gotopage."]</a>";
			$next_page_exists = true;
		}
		$a_next_page = "";
		if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
			$a_next_page .= "&nbsp;&nbsp;<a href='".$_SERVER[PHP_SELF]."?block=".($nowblock+1)."&gotopage=".($setup[page_num]*($nowblock+1)+1)."&type=".$type."&sort=".$sort."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

			$a_next_page = $a_next_page.$a_last_block;
		}
	} else {
		$print_page = "<B>1</B>";
	}
	echo "<tr>\n";
	echo "	<td width=\"100%\" style=\"font-size:11px;\" align=\"center\">\n";
	echo "	".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
	echo "	</td>\n";
	echo "</tr>\n";
?>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height="20"></td>
</tr>
</table>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>