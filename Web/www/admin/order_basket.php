<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-2";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 20;

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

$type=$_POST["type"];
$date=$_POST["date"];
$sort=$_POST["sort"];
if(strlen($date)==0) $date=date("Ymd");
if(strlen($sort)==0) $sort="date";
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function GoSort(sort) {
	document.form1.sort.value=sort;
	document.form1.submit();
}

function WindowOpen(sURL) {
	window.open(sURL);
}
</script>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
		<col width=198></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top"  background="images/leftmenu_bg.gif">
			<? include ("menu_order.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" width="16" height="12" border="0" valign=absmiddle>현재위치 : 주문/매출 &gt; 장바구니 및 매출 분석 &gt; <span class="2depth_select">장바구니 상품분석</span></td>
			</tr>
			</table>
		</td>
	</tr>   
	<tr>
        <td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_01_bg.gif"></td>
        <td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr>
        <td width="16" background="images/con_t_04_bg1.gif"></td>
        <td bgcolor="#ffffff" style="padding:10px">




			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/order_basket_title.gif" ALT=""></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height=21></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"><IMG SRC="images/distribute_04.gif" ></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue"><p>고객이 장바구니에 담은 상품을 확인할 수 있으며, 그에 따른 분석이 가능합니다.</p></TD>
					<TD background="images/distribute_07.gif"><IMG SRC="images/distribute_07.gif" ></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/order_basket_stitle1.gif" WIDTH="134" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=block>
			<input type=hidden name=gotopage>
			<input type=hidden name=sort value="<?=$sort?>">
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="410" border=0>
				<TR>
					<TD style="PADDING-RIGHT: 3px; PADDING-LEFT: 3px; PADDING-BOTTOM: 3px; PADDING-TOP: 3px"><p align="left"><img src="images/icon_8a.gif" width="13" height="13" border="0"><B>날짜선택 : </B></TD>
					<TD style="PADDING-RIGHT: 3px; PADDING-LEFT: 3px; PADDING-BOTTOM: 3px; PADDING-TOP: 3px"><p align="left"><select name=date onchange="this.form.submit();" class="select">
<?
				$weekday = array("일","월","화","수","목","금","토");
				$time=time();
				for($i=0;$i<7;$i++) {
					$timeval=$time-($i*86400);
					echo "<option value=\"".date("Ymd",$timeval)."\" ";
					if($date==date("Ymd",$timeval)) echo "selected";
					echo ">";
					if($i==0) echo "&nbsp;오늘&nbsp;";
					else echo $i."일전 ";
					echo "(".date("m/d",$timeval)." ".$weekday[date("w",$timeval)].")</option>\n";
				}
?>
					</select></TD>
					<TD style="PADDING-RIGHT: 1px; PADDING-LEFT: 1px; PADDING-BOTTOM: 1px; PADDING-TOP: 1px" align=right><p align="left"><img src="images/icon_8a.gif" width="13" height="13" border="0"><B>정렬방법:</B>&nbsp;<a href="javascript:GoSort('date');"><img src="images/icon_time<?=($sort=="date"?"r":"")?>.gif" width="34" height="15" border="0" align="absmiddle"></a><a href="javascript:GoSort('product');"><img src="images/icon_product<?=($sort=="product"?"r":"")?>.gif" width="34" height="15" border="0" hspace="2" align="absmiddle"></a><a href="javascript:GoSort('basket');"><img src="images/icon_bask<?=($sort=="basket"?"r":"")?>.gif" width="55" height="15" border="0" align="absmiddle"></a></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" width="760" colspan="5"><img src="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="111"><p align="center">날짜</TD>
					<TD class="table_cell1" width="339"><p align="center">상품명</TD>
					<TD class="table_cell1" width="57"><p align="center">수량</TD>
					<TD class="table_cell1" width="92"><p align="center">가격</TD>
					<TD class="table_cell1" width="113"><p align="center">상품보기</TD>
				</TR>
				<TR>
					<TD colspan="5" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
				</TR>
<?
		$sql = "SELECT COUNT(".($sort=="product"?"DISTINCT(a.productcode)":"*").") as t_count FROM tblbasket a, tblproduct b ";
		$sql.= "WHERE a.productcode=b.productcode ";
		$sql.= "AND a.date LIKE '".$date."%' ";
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		$t_count = $row->t_count;
		mysql_free_result($result);
		$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

		$sql = "SELECT a.tempkey, a.date, a.productcode, b.productname, b.sellprice, ";
		if($sort=="product") $sql.= "SUM(a.quantity) as quantity ";
		else $sql.= "a.quantity ";
		$sql.= "FROM tblbasket a, tblproduct b WHERE a.productcode = b.productcode ";
		$sql.= "AND a.date LIKE '".$date."%' ";
		if($sort=="date") $sql.= "ORDER BY a.date DESC ";
		else if ($sort=="product") $sql.= "GROUP BY a.productcode ORDER BY quantity DESC ";
		else if ($sort=="basket") $sql.= "ORDER BY a.tempkey ";
		$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
		$result = mysql_query($sql,get_db_conn());
		$cnt=0;
		$i=0;
		$bgcolor="#FFFFFF";
		$fontcolor="#000000";
		while($row=mysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
			$date1 = substr($row->date,4,2)."월 ".substr($row->date,6,2)."일 ".substr($row->date,8,2)."시 ".substr($row->date,10,2)."분";
			if($sort=="basket") {
				if($row->tempkey!=$tempkey) {
					if($i%2==0) {
						$bgcolor="#FFFFFF";
						$fontcolor="#000000";
					} else {
						$bgcolor="#F2FAFF";
						$fontcolor="#0099BF";
					}
					$i++;
				}
			}
			$tempkey=$row->tempkey;
			echo "<tr bgcolor=\"".$bgcolor."\">\n";
			echo "	<TD class=\"td_con2\" width=\"125\"><p align=\"center\">&nbsp;<font color=\"".$fontcolor."\">".$date1."</font>&nbsp;</td>\n";
			echo "	<TD class=\"td_con1\" width=\"339\"><p align=\"left\">&nbsp;<font color=\"".$fontcolor."\">".$row->productname."</font></td>\n";
			echo "	<TD class=\"td_con1\" width=\"57\"><p align=\"center\"><b><font color=\"".$fontcolor."\">".(int)$row->quantity."</font></b></p></TD>\n";
			echo "	<TD class=\"td_con1\" width=\"92\"><p align=\"right\"><b><span class=\"font_orange\">".number_format($row->sellprice)."원&nbsp;</span></b></TD>\n";
			echo "	<TD class=\"td_con1\" width=\"113\"><p align=\"center\"><a href=\"javascript:WindowOpen('http://$shopurl/?productcode=$row->productcode');\"><img src=\"images/btn_productview.gif\" width=\"88\" height=\"25\" border=\"0\"></a></p></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<TD colspan=\"5\" width=\"760\" background=\"images/table_con_line.gif\"><img src=\"images/table_con_line.gif\" width=\"4\" height=\"1\" border=\"0\"></TD>\n";
			echo "</tr>\n";
			$cnt++;
		}
		mysql_free_result($result);

		if ($cnt==0) {
			echo "<tr><td class=\"td_con2\" colspan=\"5\" align=\"center\">장바구니에 담긴 상품이 없습니다.</td></tr>";
		}
?>
				<TR>
					<TD background="images/table_top_line.gif" width="760" colspan="5"><img src="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td align="center"><p>&nbsp;</p></td>
			</tr>
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
<?
		$total_block = intval($pagecount / $setup[page_num]);

		if (($pagecount % $setup[page_num]) > 0) {
			$total_block = $total_block + 1;
		}

		$total_block = $total_block - 1;

		if (ceil($t_count/$setup[list_num]) > 0) {
			// 이전	x개 출력하는 부분-시작
			$a_first_block = "";
			if ($nowblock > 0) {
				$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><IMG src=\"images/icon_first.gif\" border=0 align=\"absmiddle\"></a>&nbsp;&nbsp;";

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
						$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
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
						$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
					}
				}
			}		// 마지막 블럭에서의 표시부분-끝


			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
				$last_gotopage = ceil($t_count/$setup[list_num]);

				$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><IMG src=\"images/icon_last.gif\" border=0 align=\"absmiddle\" width=\"17\" height=\"14\"></a>";

				$next_page_exists = true;
			}

			// 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "<B>[1]</B>";
		}
		echo "<tr>\n";
		echo "	<td width=\"100%\" class=\"font_size\"><p align=\"center\">\n";
		echo "		".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
		echo "	</td>\n";
		echo "</tr>\n";
?>
				</table>
				</td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"><IMG SRC="images/manual_left1.gif" WIDTH=15 HEIGHT="5" ALT=""></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">장바구니 상품분석</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;"><p>- 장바구니 상품분석은 고객의 구매성향 파악 및 매출 향상을 위한 이벤트 기획에 도움을 줍니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;"><p>- 해당 날짜별로 현재 장바구니에 담겨있는 상품 리스트만 출력됩니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;"><p>- 장바구니 상품 리스트는 일주일(7일)간 유지됩니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;"><p>- [시간]/[상품명]/[장바구니] 순으로 정렬 가능하며, [장바구니] 정렬시 한 고객의 장바구니 상품 리스트는 같은 색상으로 표시됩니다.</p></td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"><IMG SRC="images/manual_right1.gif" WIDTH=18 HEIGHT="2" ALT=""></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"><IMG SRC="images/manual_down.gif" WIDTH="4" HEIGHT=8 ALT=""></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</table>
</td>
        <td width="16" background="images/con_t_02_bg.gif"></td>
    </tr>
    <tr>
        <td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_04_bg.gif"></td>
        <td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr><td height="20"></td></tr>
</table>



			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>

<? INCLUDE "copyright.php"; ?>