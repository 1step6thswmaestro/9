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

$type=(int)$_POST["type"];
$s_check=(int)$_POST["s_check"];
$search=$_POST["search"];
$productcode=$_POST["productcode"];

if(!$s_check) {
	$search="";
	$search_style="disabled style=\"background:#f4f4f4\"";
}
${"check_s_check".$s_check} = "checked";


?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckSearch() {
	check_val="";
	for(i=0;i<document.form1.s_check.length;i++) {
		if (document.form1.s_check[i].checked==true) {
			check_val=document.form1.s_check[i].value;
			break;
		}
	}
	if (check_val!="0") {
		if (document.form1.search.value.length<=2) {
			document.form1.search.focus();
			alert("검색어를 2자 이상 입력하세요.");
			return;
		}
	}
	document.form1.type.value="0";
	document.form1.submit();
}

function GoMemberView(prcode) {
	document.form3.type.value = "1";
	document.form3.block.value = "";
	document.form3.gotopage.value = "";
	document.form3.productcode.value = prcode;
	document.form3.submit();
}

function Searchid(id) {
	document.form1.type.value="up";
	document.form1.search.disabled=false;
	document.form1.search.style.background="#FFFFFF";
	document.form1.search.value=id;
	document.form1.s_check[2].checked=true;
	document.form1.submit();
}

function CheckScheck(val) {
	if (val==0) {
		document.form1.search.disabled=true;
		document.form1.search.style.background="#F4F4F4";
		alert("검색어를 입력하실 필요없이 조회하기 버튼을 누르시기 바랍니다.");
	} else {
		document.form1.search.disabled=false;
		document.form1.search.style.background="#FFFFFF";
		document.form1.search.focus();
	}
}

function ProductInfo(code,prcode,popup) {
	document.form2.code.value=code;
	document.form2.prcode.value=prcode;
	document.form2.popup.value=popup;
	if (popup=="YES") {
		document.form2.action="product_register.add.php";
		document.form2.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form2.action="product_register.php";
		document.form2.target="";
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
function CheckKeyPress(){
	ekey=event.keyCode;
	if (ekey==13) {
		CheckSearch();
	}
}
function MemberView(id){
	parent.topframe.ChangeMenuImg(4);
	document.form4.search.value=id;
	document.form4.submit();
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
					<TD><IMG SRC="images/product_wishlist_title.gif" border="0"></TD>
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
					<TD width="100%" class="notice_blue">Wishlist에 한 상품을 확인하실 수 있습니다.</TD>
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
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/product_wishlist_stitle1.gif" border="0"></TD>
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
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">검색조건 선택</TD>
					<TD class="td_con1" ><input type=radio name=s_check value="0" onClick="CheckScheck(this.value);" id=idx_s_check0 <?=$check_s_check0?>> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check0>상위 Wishlist 상품 100개</label>&nbsp;&nbsp;&nbsp;<input type=radio name=s_check value="1" onClick="CheckScheck(this.value);" id=idx_s_check1 <?=$check_s_check1?>> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check1>상품명으로 검색</label>&nbsp;&nbsp;&nbsp;<input type=radio name=s_check value="2" onClick="CheckScheck(this.value);" id=idx_s_check2 <?=$check_s_check2?>> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check2>회원아이디로 검색</label></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">검색어 입력</TD>
					<TD class="td_con1" ><input name=search size=40 value="<?=$search?>" onKeyDown="CheckKeyPress()" <?=$search_style?> class="input"> <a href="javascript:CheckSearch();"><img src="images/btn_search2.gif" align=absmiddle width="50" height="25" border="0"></a></TD>
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
				<?if($type=="0"){//일반검색?>
				<col width="30"></col>
				<col width=""></col>
				<col width="30"></col>
				<col width="40"></col>
				<col width="25"></col>
				<col width="80"></col>
				<TR>
					<TD background="images/table_top_line.gif" colspan="6"></TD>
				</TR>
				<TR align=center>
					<TD class="table_cell">No</TD>
					<TD class="table_cell1">상품명</TD>
					<TD class="table_cell1">수량</TD>
					<TD class="table_cell1">보이기</TD>
					<TD class="table_cell1">인원</TD>
					<TD class="table_cell1">회원보기</TD>
				</TR>
				<TR>
					<TD colspan="6" background="images/table_con_line.gif"></TD>
				</TR>
<?
				if ($s_check=="0") {
					$sql = "SELECT b.productname, b.productcode, COUNT(a.productcode) as totcnt, b.display, ";
					$sql.= "b.tinyimage, b.quantity, b.selfcode, b.assembleuse FROM tblwishlist a, tblproduct b ";
					$sql.= "WHERE a.productcode = b.productcode GROUP BY a.productcode ";
					$sql.= "ORDER BY totcnt DESC ";
				} else {
					$sql = "SELECT b.productname, b.productcode, COUNT(a.productcode) as totcnt, b.display, ";
					$sql.= "b.tinyimage, b.quantity, b.selfcode, b.assembleuse FROM tblwishlist a, tblproduct b ";
					$sql.= "WHERE a.productcode = b.productcode ";
					if ($s_check=="1") $sql.= "AND b.productname LIKE '%".$search."%' ";
					else if($s_check=="2") $sql.= "AND a.id LIKE '".$search."%' "; 
					$sql.= "GROUP BY a.productcode ";
					$sql.= "ORDER BY totcnt DESC ";
				}

				$result = mysql_query($sql,get_db_conn());
				$t_count = mysql_num_rows($result);
				mysql_free_result($result);
				$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

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
					echo "		<img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\"><a href=\"JavaScript:ProductInfo('".substr($row->productcode,0,12)."','".$row->productcode."','')\"><FONT class=mainprname>".$row->productname.($row->selfcode?"-".$row->selfcode:"")."</font></a>";
					echo "		&nbsp;<a href=\"JavaScript:ProductInfo('".substr($row->productcode,0,12)."','".$row->productcode."','YES')\"><img src=\"images/icon_newwin.gif\" width=\"42\" height=\"18\" border=\"0\" hspace=\"2\" align=absmiddle></a>";
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
					echo "	<TD align=center class=\"td_con1\"><NOBR>".($row->quantity==NULL?"<font color=blue>무제한</font>":($row->quantity>0?$row->quantity:"<font color=red>품절</font>"))."</td>\n";
					echo "	<TD align=center class=\"td_con1\"><b>".$row->display."</b></td>\n";
					echo "	<TD align=center class=\"td_con1\">".$row->totcnt."</td>\n";
					echo "	<TD align=center class=\"td_con1\"><A HREF=\"javascript:GoMemberView('".$row->productcode."');\"><img src=\"images/btn_memberview.gif\" width=\"77\" height=\"25\" border=\"0\"></A></td>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<TD colspan=\"6\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</tr>\n";
					$cnt++;
				}
				if ($cnt==0) {
					echo "<tr><td class=\"td_con2\" colspan=\"6\" align=\"center\">검색된 정보가 존재하지 않습니다.</td></tr>";
				}
				mysql_free_result($result);
?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="6"></TD>
				</TR>
				<?}else if($type=="1"){//등록회원 보기?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="5"></TD>
				</TR>
				<TR align=center>
					<TD class="table_cell">No</TD>
					<TD class="table_cell1">회원ID</TD>
					<TD class="table_cell1">회원정보 보기</TD>
					<TD class="table_cell1">총 주문건수</TD>
					<TD class="table_cell1">총 주문금액</TD>
				</TR>
				<TR>
					<TD colspan="5" background="images/table_con_line.gif"></TD>
				</TR>
<?
				$sql = "SELECT a.id, COUNT(b.price) as totcnt, SUM(b.price) as totprice ";
				$sql.= "FROM tblwishlist a LEFT OUTER JOIN tblorderinfo b ";
				$sql.= "ON (a.id = b.id AND b.deli_gbn = 'Y') ";
				$sql.= "WHERE a.productcode = '".$productcode."' ";
				$sql.= "GROUP BY a.id ";
				$result = mysql_query($sql,get_db_conn());
				$t_count = mysql_num_rows($result);
				mysql_free_result($result);
				$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

				$sql.= "ORDER BY a.id LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
				$result = mysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=mysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
					echo "<tr>";
					echo "	<TD align=center class=\"td_con2\">".$number."</td>\n";
					echo "	<TD align=center class=\"td_con1\"><img src=\"images/icon_id.gif\" border=\"0\" align=absMiddle> <A HREF=\"javascript:Searchid('".$row->id."');\"><FONT class=mainprname>".$row->id."</font></A></td>\n";
					echo "	<TD align=center class=\"td_con1\"><A HREF=\"javascript:MemberView('".$row->id."');\"><img src=\"images/bnt_memberview.gif\" width=\"74\" height=\"16\" border=\"0\"></A></td>\n";
					echo "	<TD align=center class=\"td_con1\">".number_format($row->totcnt)."건</td>\n";
					echo "	<TD align=center class=\"td_con1\"><b><span class=\"font_orange\">".number_format($row->totprice)."원</span></b></td>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<TD colspan=\"5\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</tr>\n";

					$cnt++;
				}
				if ($cnt==0) {
					echo "<tr><td class=\"td_con2\" colspan=\"5\" align=\"center\">검색된 정보가 존재하지 않습니다.</td></tr>";
				}
				mysql_free_result($result);
?>
				<?}?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="6"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td>
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
				echo "	<td width=\"100%\" align=center class=\"font_size\">\n";
				echo "		".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
				echo "	</td>\n";
				echo "</tr>\n";
?>
				</table>
				</td>
			</tr>
			</form>

			<form name=form2 action="product_register.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form>

			<form name=form3 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type value="<?=$type?>">
			<input type=hidden name=block>
			<input type=hidden name=gotopage>
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=productcode value="<?=$productcode?>">
			</form>

			<form name=form4 action="member_list.php" method=post>
			<input type=hidden name=search>
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
						<td><span class="font_dotline">Wishlist 상품 관리</span></td>
					</tr>
					<?if($type=="0"){?>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 상품명을 클릭시 해당 상품 카테고리내 상품들의 정보를 확인하실 수 있습니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- [새창] 버튼 클릭시 해당 상품의 정보를 수정할 수 있습니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- [회원보기] 버튼 클릭시 해당 상품을 Wishlist에 담은 회원리스트 및 총 구매건수, 총 구매금액을 확인할 수 있습니다.</td>
					</tr>
					<?}else if($type=="1"){?>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- [회원아이디] 클릭시 해당 회원아이디로 WishList 상품 검색이 이루어집니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- [회원정보보기] 클릭시 해당 회원의 정보를 확인할 수 있습니다.</td>
					</tr>
					<?}?>
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