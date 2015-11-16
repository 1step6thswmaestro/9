<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-5";
$MenuCode = "product";
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

$yesbrand=$_POST["yesbrand"];
$keyword=$_POST["keyword"];
$type=$_POST["type"];

// 상품별 무이자설정때문에 넣음, card_splittype가 'O' 이면 상품별 무이자설정임.
$sql = "SELECT card_splittype,card_splitprice FROM tblshopinfo ";
$result = mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$card_splittype = $row->card_quotafree;
	$card_splitprice = $row->card_quotaprice;
}
mysql_free_result($result);

if($card_splittype!="O" && $type=="card"){
	echo "<script>alert('[개별상품 무이자 할부 서비스]가 셋팅되어있어야 검색이 가능합니다.');history.go(-1);</script>";
	exit;
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckSearch() {
	if (document.form1.keyword.value.length<2) {
		if(document.form1.keyword.value.length==0) alert("검색어를 입력하세요.");
		else alert("검색어는 2글자 이상 입력하셔야 합니다."); 
		document.form1.keyword.focus();
		return;
	} else {
		document.form1.submit();
	}
}

function CheckKeyPress(){
	ekey=event.keyCode;
	if (ekey==13) {
		CheckSearch()
	}
}

function ProductInfo(code,prcode,popup) {
	document.form2.code.value=code;
	document.form2.prcode.value=prcode;
	document.form2.popup.value=popup;
	if (popup=="YES") {
		document.form2.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
		document.form2.action="product_register.add.php";
	} else {
		document.form2.target="";
		document.form2.action="product_register.php";
	}
	document.form2.submit();
}
function ProductMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.primage"+cnt);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.visibility = "visible";
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	Obj = document.getElementById(Obj);
	Obj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}
function GoPage(block,gotopage) {
	document.form3.block.value = block;
	document.form3.gotopage.value = gotopage;
	document.form3.submit();
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
			<? include ("menu_product.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상품관리 &gt; 사은품/견적/기타관리 &gt; <span class="2depth_select">상품 키워드 검색</span></td>
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
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/product_keywords_title.gif"ALT=""></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height=21></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">쇼핑몰의 모든 상품을 상품명 및 키워드로 검색 하실 수 있습니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/product_keywords_stitle1.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139">
						<img src="images/icon_point2.gif" width="8" height="11" border="0">검색어 입력
					</TD>
					<TD class="td_con1" ><select size=1 name=yesbrand class="select">
							<option value="YES" <? if ($yesbrand=="YES") echo "selected"; ?>>상품명
							<option value="NO" <? if ($yesbrand=="NO") echo "selected"; ?>>키워드
						</select> <input type=text name=keyword value="<?=$keyword?>" onKeyDown="CheckKeyPress()" style="width:250" class="input"> <select size=1 name=type class="select">
							<option value="all" <? if ($type=="all" || empty($type)) echo "selected"; ?>>전체상품
							<option value="empty" <? if ($type=="empty") echo "selected"; ?>>품절된상품
							<option value="noview" <? if ($type=="noview") echo "selected"; ?>>미진열상품
							<option value="bank" <? if ($type=="bank") echo "selected"; ?>>현금결제상품
							<? if($card_splittype=="O") {?><option value="card" <? if ($type=="card") echo "selected"; ?>>개별무이자상품<?}?>
						</select> <a href="javascript:CheckSearch();"><img src="images/btn_search2.gif" align=absmiddle width="50" height="25" border="0"></a></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/product_review_stitle2.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width="30"></col>
				<col width=""></col>
				<col width="60"></col>
				<col width="40"></col>
				<col width="40"></col>
				<col width="20"></col>
				<col width="20"></col>
				<col width="20"></col>
				<col width="25"></col>
				<TR>
					<TD background="images/table_top_line.gif" colspan="9"></TD>
				</TR>
				<TR align=center>
					<TD class="table_cell">No</TD>
					<TD class="table_cell1">상품명</TD>
					<TD class="table_cell1">판매가</TD>
					<TD class="table_cell1">수량</TD>
					<TD class="table_cell1">적립금</TD>
					<TD class="table_cell1" colspan="3">이미지 대,중,소</TD>
					<TD class="table_cell1">진열</TD>
				</TR>
				<TR>
					<TD colspan="9" background="images/table_con_line.gif"></TD>
				</TR>
<?
				$keyword = ereg_replace("%","",$keyword);
				if (strlen(trim($keyword))>0 || !empty($type)) {
					$page_numberic_type=1;
					$qry = "FROM tblproduct a WHERE 1=1 ";

					if (strlen($keyword)>0 && $yesbrand=="YES")
						$qry.= "AND a.productname LIKE '%".$keyword."%' ";
					else if(strlen($keyword)>0)
						$qry.= "AND a.keyword LIKE '%".$keyword."%' ";

					if ($type=="empty")
						$qry.= "AND a.quantity<=0 ";
					else if ($type=="noview")
						$qry.= "AND a.display='N' ";
					else if ($type=="card")
						$qry.= "AND a.etctype LIKE '%SETQUOTA%' ";
					else if ($type=="bank")
						$qry.= "AND a.etctype LIKE '%BANKONLY%' ";
					else if ($type=="new")
						$qry.= "AND date LIKE '".date("Ymd")."%' ";

					$sql = "SELECT COUNT(*) as t_count ".$qry;
					$result = mysql_query($sql,get_db_conn());
					$row = mysql_fetch_object($result);
					mysql_free_result($result);
					$t_count = $row->t_count;
					$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

					$sql = "SELECT a.productcode, a.sellprice, a.productname, a.quantity, a.reserve, a.reservetype, ";
					$sql.= "a.addcode, a.maximage, a.minimage, a.tinyimage, a.display, a.selfcode, a.assembleuse ".$qry." ";
					$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
					$result = mysql_query($sql,get_db_conn());
					$cnt=0;
					while($row=mysql_fetch_object($result)) {
						$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
						echo "<tr>";
						echo "	<TD align=center class=\"td_con2\">".$number."</td>\n";
						echo "	<TD class=\"td_con1\"><NOBR>";
						echo "	<TABLE cellSpacing=0 cellPadding=0 border=0 width=\"100%\">\n";
						echo "	<tr>\n";
						echo "		<td style=\"word-break:break-all;\">\n";
						echo "		<span onMouseOver='ProductMouseOver($cnt)' onMouseOut=\"ProductMouseOut('primage".$cnt."');\">";
						echo "		<img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\"><a href=\"JavaScript:ProductInfo('".substr($row->productcode,0,12)."','".$row->productcode."','')\"><font color=#3D3D3D><u>".$row->productname.($row->selfcode?"-".$row->selfcode:"")."</u></font></a>";
						echo "		&nbsp;<a href=\"JavaScript:ProductInfo('".substr($row->productcode,0,12)."','".$row->productcode."','YES')\"><IMG src=\"images/icon_newwin.gif\" align=absMiddle border=0 width=\"42\" height=\"18\"></a>";
						echo "		</span>\n";
						echo "		<div id=primage".$cnt." style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
						echo "		<table border=0 cellspacing=0 cellpadding=0 width=170>\n";
						echo "		<tr bgcolor=#FFFFFF>\n";
						if (strlen($row->tinyimage)>0) {
							echo "			<td align=center width=100% height=150 style=\"BORDER-RIGHT: #000000 1px solid; BORDER-TOP: #000000 1px solid; BORDER-LEFT: #000000 1px solid; BORDER-BOTTOM: #000000 1px solid\"><img src=".$Dir.DataDir."shopimages/product/".$row->tinyimage."></td>\n";
						} else {
							echo "			<td align=center width=100% height=150 style=\"BORDER-RIGHT: #000000 1px solid; BORDER-TOP: #000000 1px solid; BORDER-LEFT: #000000 1px solid; BORDER-BOTTOM: #000000 1px solid\"><img src=".$Dir."images/product_noimg.gif></td>\n";
						}
						echo "		</tr>\n";
						echo "		</table>\n";
						echo "		</div>\n";
						echo "		</td>\n";
						echo "	</tr>\n";
						echo "	</table>\n";
						echo "	</td>\n";
						echo "	<TD align=center class=\"td_con1\"><b><span class=\"font_orange\">".number_format($row->sellprice)."</span></b></td>\n";
						echo "	<TD align=center class=\"td_con1\">";
						if (strlen($row->quantity)==0) echo "무제한";
						else if ($row->quantity<=0) echo "<font color=red>품절</font>";
						else echo $row->quantity;
						echo "	</td>\n";
						echo "	<TD align=center class=\"td_con1\">".($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")."</td>\n";
						echo "	<TD align=center class=\"td_con1\" width=\"30\">".(strlen($row->maximage)>0?"O":"X")."</td>\n";
						echo "	<TD align=center class=\"td_con1\" width=\"30\">".(strlen($row->minimage)>0?"O":"X")."</td>\n";
						echo "	<TD align=center class=\"td_con1\" width=\"30\">".(strlen($row->tinyimage)>0?"O":"X")."</td>\n";
						echo "	<TD align=center class=\"td_con1\">".$row->display."</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "	<TD colspan=\"9\" background=\"images/table_con_line.gif\"></TD>\n";
						echo "</tr>\n";
						$cnt++;
					}
					mysql_free_result($result);
					if ($cnt==0) {
						$page_numberic_type = "";
						echo "<tr><td class=td_con2 colspan=9 align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
					}
				} else {
					$page_numberic_type = "";
					echo "<tr><td class=td_con2 colspan=9 align=center>검색된 상품이 없습니다.</td></tr>";
				}
?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="9"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
<?
				if($page_numberic_type) {
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
					echo "	<td width=\"100%\" align=center class=\"font_size\">\n";
					echo "		".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
					echo "	</td>\n";
					echo "</tr>\n";
				}
?>
				</table>
				</td>
			</tr>
			</form>

			<form name=form2 action="product_register.add.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form>

			<form name=form3 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=block>
			<input type=hidden name=gotopage>
			<input type=hidden name=yesbrand value="<?=$yesbrand?>">
			<input type=hidden name=keyword value="<?=$keyword?>">
			<input type=hidden name=type value="<?=$type?>">
			</form>
			<tr>
				<td>&nbsp;</td>
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
					<TD background="images/manual_left1.gif"></td>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<col width=20></col>
					<col width=></col>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">상품 키워드 검색</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 검색어는 최소 2글자 이상부터 검색이 가능합니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 상품명을 클릭시 해당 상품 카테고리내 상품들의 정보를 확인하실 수 있습니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- [새창] 버튼 클릭시 해당 상품의 정보를 수정할 수 있습니다.</td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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