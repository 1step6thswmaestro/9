<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "go-4";
$MenuCode = "gong";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################
$CurrentTime = time();

$pgid_info=GetEscrowType($_shopdata->card_id);
$pg_type=trim($pgid_info["PG"]);
?>
<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function ProductMouseOver(Obj) {
	obj = event.srcElement;
	WinObj=document.getElementById(Obj);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.display = "";

	if(!WinObj.height)
		WinObj.height = WinObj.offsetTop;

	WinObjPY = WinObj.offsetParent.offsetHeight;
	WinObjST = WinObj.height-WinObj.offsetParent.scrollTop;
	WinObjSY = WinObjST+WinObj.offsetHeight;

	if(WinObjPY < WinObjSY)
		WinObj.style.top = WinObj.offsetParent.scrollTop-WinObj.offsetHeight+WinObjPY;
	else if(WinObjST < 0)
		WinObj.style.top = WinObj.offsetParent.scrollTop;
	else
		WinObj.style.top = WinObj.height;
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	WinObj = document.getElementById(Obj);
	WinObj.style.display = "none";
	clearTimeout(obj._tid);
}

function GoPage(block,gotopage,sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function GoSort(sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = "";
	document.form1.gotopage.value = "";
	document.form1.submit();
}

function orderlistView(prcode) {
	document.form2.action="social_order_list.php";
	document.form2.prcode.value = prcode;
	document.form2.submit();
}

function encoreView(prcode){
	window.open("","socialEncore","scrollbars=yes,width=320,height=300");
	document.encoreform.prcode.value = prcode;
	document.encoreform.submit();
}

function orderCancel(prcode) {
	if(confirm("선택하신 상품의 주문을 일괄 취소하시겠습니까?")) {
<?if($pg_type=="A"){?>
		if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
			document.kcpform.action="<?=$Dir?>paygate/A/social_cancel.php";
			document.kcpform.prcode.value = prcode;
			document.kcpform.submit();
		}
<?}else if($pg_type=="B"){?>
		if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
			document.dacomform.action="<?=$Dir?>paygate/B/social_cancel.php";
			document.dacomform.prcode.value = prcode;
			document.dacomform.submit();
		}
<?}else if($pg_type=="C"){?>
		if(caltype == "hp") {
			if(confirm("\n┏━━━━━━━━━━━━━━  【 주      의      사      항 】  ━━━━━━━━━━━━━━━━┓    \n┃                                                                                                                                    ┃    \n┃                                                                                                                                    ┃    \n┃       １. 휴대폰 결제 취소 처리는 쇼핑몰 DB에만 반영되며 올더게이트에 전달되지 않습니다.       ┃    \n┃                                                                                                                                    ┃    \n┃       ２. 올더게이트 휴대폰 결제 취소는 해당 ＰＧ사의 관리자페이지에서 처리 해 주세요.           ┃    \n┃                                                                                                                                    ┃    \n┃                                                                                                                                    ┃    \n┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛    \n\n                               결제취소처리는 쇼핑몰 DB에만 반영됩니다. 정말 하시겠습니까?")) {
				document.allthegateform.action="<?=$Dir?>paygate/C/social_cancel.php";
				document.allthegateform.prcode.value = prcode;
				document.allthegateform.submit();
			}
		} else {
			if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
				document.allthegateform.action="<?=$Dir?>paygate/C/social_cancel.php";
				document.allthegateform.prcode.value = prcode;
				document.allthegateform.submit();
			}
		}
<?} else if($pg_type=="D"){?>
		if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
			document.inicisform.action="<?=$Dir?>paygate/D/social_cancel.php";
			document.inicisform.prcode.value = prcode;
			document.inicisform.submit();
		}
<?}?>
	}
}

//-->
</SCRIPT>
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
			<? include ("menu_gong.php"); ?>
			</td>

			<td></td>
			<td valign="top">

<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 소셜·경매 &gt; 소셜쇼핑 &gt; <span class="2depth_select">판매종료 소셜상품</span></td>
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
		<?
		//리스트 세팅
		$setup[page_num] = 10;
		$setup[list_num] = 10;

		$sort=$_POST["sort"];
		$block=$_POST["block"];
		$gotopage=$_POST["gotopage"];

		if ($block != "") {
			$nowblock = $block;
			$curpage  = $block * $setup[page_num] + $gotopage;
		} else {
			$nowblock = 0;
		}

		if (($gotopage == "") || ($gotopage == 0)) {
			$gotopage = 1;
		}

		////////////////////////
		$imagepath=$Dir.DataDir."shopimages/product/";

		?>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr><td height="8"></td></tr>
		<tr>
			<td>
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
			<TR>
				<TD><IMG SRC="images/social_end_title.gif" ALT="판매종료 공동구매상품"></TD>
				</tr><tr>
				<TD width="100%" background="images/title_bg.gif" height="21"></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		<tr><td height=10></td></tr>
		<tr>
			<td>
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
			<TR>
				<TD><IMG SRC="images/social_end_stitle1.gif"  ALT="판매종료 상품"></TD>
				<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
				<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		<tr><td height=3></td></tr>
		<tr><td>
			<form name=form2 action="" method=post>
			<input type=hidden name=prcode>
			</form>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=prcode>
			<input type=hidden name=code value="<?=$code?>">
			<input type=hidden name=sort value="<?=$sort?>">
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=keyword value="<?=$keyword?>">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="100%" style="text-align:center">
				 <div style="border:1px solid #DBDBDB;width:100%;padding:10px 0;margin-top:10px;margin-bottom:10px;">상품명 <input type="text" name="keyword" value="<?=$keyword?>" style="vertical-align:middle;"class=input> <input type="image" src="images/icon_search.gif" alt="검색" style="vertical-align:middle;" ></div>
				</td>
			</tr>
			<tr>
				<td width="100%" style="text-align:right;font-size:15px;font-weight:bold;"><?=$btnWrite?></td>
			</tr>
			<tr>
				<td width="100%">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%">
					<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
					<col width=40></col>
					<col width=50></col>
					<col width=></col>
					<col width=130></col>
					<col width=50></col>
					<col width=50></col>
					<col width=60></col>
					<col width=60></col>
					<col width=60></col>
					<TR>
						<TD colspan="9" background="images/table_top_line.gif"></TD>
					</TR>
					<TR align="center">
						<TD class="table_cell">No</TD>
						<TD class="table_cell1" colspan="2">상품명</TD>
						<TD class="table_cell1">판매기간</TD>
						<TD class="table_cell1">구매달성</TD>
						<TD class="table_cell1">주문건수</TD>
						<TD class="table_cell1">주문서</TD>
						<TD class="table_cell1">앵콜요청</TD>
						<TD class="table_cell1">관리</TD>
					</TR>
					<TR>
						<TD height="1" colspan="9" background="images/table_top_line.gif"></TD>
					</TR>
		<?
			$sCondition.= "AND P.productcode = S.pcode ";//AND display='Y'
			$sCondition.= "AND ( (sell_enddate < '".$CurrentTime."' AND (quantity is null OR quantity <> 0 ) ) OR quantity = 0 )";
			if(strlen($keyword)>2) {
				$sCondition.= "AND productname LIKE '%".$keyword."%' ";
			}
			$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct P, tblproduct_social S WHERE 1=1 ";
			$sql0.= $sCondition;
			$result = mysql_query($sql0,get_db_conn());
			$row = mysql_fetch_object($result);
			mysql_free_result($result);
			$t_count = $row->t_count;
			$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

			$sql = "SELECT option_price, productcode,productname,production,sellprice,consumerprice, ";
			$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,selfcode,assembleuse, ";
			$sql.= "sell_startdate,sell_enddate,complete_quantity, ";
			$sql.= "(SELECT count(1)  FROM tblgongguencore WHERE productcode = P.productcode ) encoreCnt ";
			$sql.= "FROM tblproduct P, tblproduct_social S ";
			$sql.= "WHERE 1=1  ";
			$sql.= $sCondition;
			$sql.= "ORDER BY sell_enddate ASC ";
			/*
			//정렬
			if ($sort=="price")				$sql.= "ORDER BY sellprice ";
			else if ($sort=="productname")	$sql.= "ORDER BY productname ";
			else							$sql.= "ORDER BY date DESC ";
			*/
			$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
			$list_rs = mysql_query($sql,get_db_conn());

			$cnt=0;
			while($row=mysql_fetch_object($list_rs)) {
				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
				$cnt++;
				$start_date=date("Y-m-d H:i:s",$row->sell_startdate);
				$end_date=date("Y-m-d H:i:s",$row->sell_enddate);
				//구매자 수
				$sql = "SELECT IFNULL(sum(quantity), 0) as totcnt FROM tblorderproduct P, tblorderinfo O ";
				$sql.= "WHERE P.ordercode = O.ordercode ";
				$sql.= "AND pay_admin_proc!='C' ";
				$sql.= "AND O.deli_gbn IN ('S','Y','N','X') ";
				$sql.= "AND (del_gbn='N' OR del_gbn='A') ";
				$sql.= "AND P.productcode='".$row->productcode."' ";

				$result2=mysql_query($sql,get_db_conn());
				$row2=mysql_fetch_object($result2);
				$totcnt=$row2->totcnt;
				if($row->complete_quantity > $totcnt){
					$sell_result ="<font color=\"#FF0000\">실퍠</font>";
				}else{
					$sell_result ="<font color=\"#FF4C00\">성공</font>";
				}

				echo "<tr>\n";
				echo "	<TD colspan=\"8\" background=\"images/table_con_line.gif\"></TD>\n";
				echo "</tr>\n";
				echo "<tr align=\"center\">\n";
				echo "	<TD class=\"td_con2\">".$number."</td>\n";
				echo "	<TD class=\"td_con1\">";
				if (strlen($row->tinyimage)>0 && file_exists($imagepath.$row->tinyimage)==true){
					echo "<img src='".$imagepath.$row->tinyimage."' height=40 width=40 border=1 onMouseOver=\"ProductMouseOver('primage".$cnt."')\" onMouseOut=\"ProductMouseOut('primage".$cnt."');\">";
				} else {
					echo "<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage".$cnt."')\" onMouseOut=\"ProductMouseOut('primage".$cnt."');\">";
				}
				echo "<div id=\"primage".$cnt."\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
				echo "		<tr bgcolor=\"#FFFFFF\">\n";
				if (strlen($row->tinyimage)>0 && file_exists($imagepath.$row->tinyimage)==true){
					echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$imagepath.$row->tinyimage."\" border=\"0\"></td>\n";
				} else {
					echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$Dir."images/product_noimg.gif\" border=\"0\"></td>\n";
				}
				echo "		</tr>\n";
				echo "		</table>\n";
				echo "		</div>\n";
				echo "	</td>\n";
				echo "	<TD class=\"td_con1\" align=\"left\" style=\"word-break:break-all;\">".$row->productname."&nbsp;</td>\n";//.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")
				echo "	<TD class=\"td_con1\">".$start_date."<br/>".$end_date."</td>";
				echo "	<TD class=\"td_con1\">".$sell_result."</TD>\n";
				echo "	<TD class=\"td_con1\">".$totcnt."/".$row->complete_quantity."</TD>\n";
				echo "	<TD class=\"td_con1\"><a href=\"javascript:orderlistView('".$row->productcode."');\">주문서</a></td>\n";
				echo "	<TD class=\"td_con1\"><a href=\"javascript:encoreView('".$row->productcode."');\">".$row->encoreCnt."</a></td>\n";
				echo "	<TD class=\"td_con1\"><a href=\"javascript:orderCancel('".$row->productcode."');\"><font color=\"#FF0000\">결제일괄취소</font></a></td>\n";
				echo "</tr>\n";
			}
			mysql_free_result($list_rs);
		if ($cnt==0) {
			echo "<tr><td class=\"td_con2\" colspan=\"9\" align=\"center\">판매종료된 소셜상품이 없습니다.</td></tr>";
		}
		?>
					<TR>
						<TD height="1" colspan="9" background="images/table_top_line.gif"></TD>
					</TR>
					</TABLE>
					</td>
				</tr>
		<?
				if($t_count > 0) {
					$total_block = intval($pagecount / $setup[page_num]);

					if (($pagecount % $setup[page_num]) > 0) {
						$total_block = $total_block + 1;
					}

					$total_block = $total_block - 1;

					if (ceil($t_count/$setup[list_num]) > 0) {
						// 이전	x개 출력하는 부분-시작
						$a_first_block = "";
						if ($nowblock > 0) {
							$a_first_block .= "<a href=\"javascript:GoPage(0,1,'".$sort."');\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><IMG src=\"images/icon_first.gif\" border=0 align=\"absmiddle\"></a>&nbsp;&nbsp;";

							$prev_page_exists = true;
						}

						$a_prev_page = "";
						if ($nowblock > 0) {
							$a_prev_page .= "<a href=\"javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).",'".$sort."');\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\">[prev]</a>&nbsp;&nbsp;";

							$a_prev_page = $a_first_block.$a_prev_page;
						}

						// 일반 블럭에서의 페이지 표시부분-시작

						if (intval($total_block) <> intval($nowblock)) {
							$print_page = "";
							for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
								if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
									$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
								} else {
									$print_page .= "<a href=\"javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).",'".$sort."');\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
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
									$print_page .= "<a href=\"javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).",'".$sort."');\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
								}
							}
						}		// 마지막 블럭에서의 표시부분-끝


						$a_last_block = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
							$last_gotopage = ceil($t_count/$setup[list_num]);

							$a_last_block .= "&nbsp;&nbsp;<a href=\"javascript:GoPage(".$last_block.",".$last_gotopage.",'".$sort."');\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><IMG src=\"images/icon_last.gif\" border=0 align=\"absmiddle\" width=\"17\" height=\"14\"></a>";

							$next_page_exists = true;
						}

						// 다음 10개 처리부분...

						$a_next_page = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$a_next_page .= "&nbsp;&nbsp;<a href=\"javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).",'".$sort."');\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

							$a_next_page = $a_next_page.$a_last_block;
						}
					} else {
						$print_page = "<B>[1]</B>";
					}
					echo "<tr>\n";
					echo "	<td height=\"52\" align=center background=\"images/blueline_bg.gif\">\n";
					echo "	".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page."\n";
					echo "	</td>\n";
					echo "</tr>\n";
				}
		?>
				<tr>
					<td style="padding-top:12px;BORDER-top:#eeeeee 2px solid;"><img width="0" height="0"></td>
				</tr>
				</table>
			</table>
			</form>
<?
if($pg_type=="A") {	//KCP
	echo "<form name=kcpform method=post action=\"".$Dir."paygate/A/social_cancel.php\" target=\"_blank\">\n";
	echo "<input type=hidden name=sitecd value=\"".$pgid_info["ID"]."\">\n";
	echo "<input type=hidden name=sitekey value=\"".$pgid_info["KEY"]."\">\n";
	echo "<input type=hidden name=\"prcode\" value=\"\">\n";
	echo "<input type=hidden name=return_host value=\"".urlencode(getenv("HTTP_HOST"))."\">\n";
	echo "<input type=hidden name=return_script value=\"".str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl()).AdminDir."social_sell_result.php"."\">\n";
	echo "<input type=hidden name=return_data value=\"ordercode=".$ordercode."\">\n";
	echo "<input type=hidden name=return_type value=\"form\">\n";
	echo "</form>\n";
} else if($pg_type=="B") {	//LG데이콤
	echo "<form name=dacomform method=post action=\"".$Dir."paygate/B/social_cancel.php\" target=\"_blank\">\n";
	echo "<input type=hidden name=mid value=\"".$pgid_info["ID"]."\">\n";
	echo "<input type=hidden name=mertkey value=\"".$pgid_info["KEY"]."\">\n";
	echo "<input type=hidden name=\"prcode\" value=\"\">\n";
	echo "<input type=hidden name=return_host value=\"".urlencode(getenv("HTTP_HOST"))."\">\n";
	echo "<input type=hidden name=return_script value=\"".str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl()).AdminDir."social_sell_result.php"."\">\n";
	echo "<input type=hidden name=return_data value=\"ordercode=".$ordercode."\">\n";
	echo "<input type=hidden name=return_type value=\"form\">\n";
	echo "</form>\n";
} else if($pg_type=="C") {	//올더게이트
	echo "<form name=allthegateform method=post action=\"".$Dir."paygate/C/social_cancel.php\" target=\"_blank\">\n";
	echo "<input type=hidden name=\"storeid\" value=\"".$pgid_info["ID"]."\">\n";
	echo "<input type=hidden name=\"prcode\" value=\"\">\n";
	echo "<input type=hidden name=\"return_host\" value=\"".urlencode(getenv("HTTP_HOST"))."\">\n";
	echo "<input type=hidden name=\"return_script\" value=\"".str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl()).AdminDir."social_sell_result.php"."\">\n";
	echo "<input type=hidden name=\"return_data\" value=\"ordercode=".$ordercode."\">\n";
	echo "<input type=hidden name=\"return_type\" value=\"form\">\n";
	echo "</form>\n";
}else if($pg_type=="D") {	//이니시스
	echo "<form name=inicisform method=post action=\"".$Dir."paygate/D/social_cancel.php\" target=\"_blank\">\n";
	echo "<input type=hidden name=sitecd value=\"".$pgid_info["ID"]."\">\n";
	echo "<input type=hidden name=\"prcode\" value=\"\">\n";
	echo "<input type=hidden name=return_host value=\"".urlencode(getenv("HTTP_HOST"))."\">\n";
	echo "<input type=hidden name=return_script value=\"".str_replace(getenv("HTTP_HOST"),"",$_ShopInfo->getShopurl()).AdminDir."social_sell_result.php"."\">\n";
	echo "<input type=hidden name=return_data value=\"ordercode=".$ordercode."\">\n";
	echo "<input type=hidden name=return_type value=\"form\">\n";
	echo "</form>\n";
}

?>
		</td>
		</tr>
		<tr><td height="50"></td></tr>
		</table>

		<form name=encoreform method="post" action="social_encore_list.php" target="socialEncore">
		<input type=hidden name=prcode>
		</form>

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

<? INCLUDE "copyright.php"; ?>