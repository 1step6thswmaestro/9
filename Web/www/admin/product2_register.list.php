<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

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

$mode=$_POST["mode"];
$code="999000000000";
$keyword=$_POST["keyword"];

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$vendercnt=$row->vendercnt;
mysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	mysql_free_result($result);
}

$imagepath=$Dir.DataDir."shopimages/product/";
?>

<? INCLUDE "header.php"; ?>
<style>td {line-height:18pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>
<SCRIPT LANGUAGE="JavaScript">
<!--
<?if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?}?>

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

function GoPageReload() {
	document.form1.mode.value = "";
	document.form1.submit();
}

function GoSort(sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = "";
	document.form1.gotopage.value = "";
	document.form1.submit();
}

function ProductSend(mode,prcode) {
	if(mode=="modify") {
		parent.ProductModify(prcode);
	}
}

function DivDefaultReset()
{
	if(!self.id)
	{
		self.id = self.name;
		parent.document.getElementById(self.id).style.height = parent.document.getElementById(self.id).height;
	}
}
DivDefaultReset();
//-->
</SCRIPT>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed">
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=keyword value="<?=$keyword?>">


<tr>
	<td width="100%" height="100%" valign="top">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><img src="images/product2_register_stitle.gif" alt="" /></td>
		<td align="right"><B><span class="font_orange">* 정렬방법 :</span></B> <A HREF="javascript:GoSort('date');">진열순</a> | <A HREF="javascript:GoSort('productname');">상품명순</a> | <A HREF="javascript:GoSort('price');">가격순</a></td>
	</tr>
	<tr><td height="5"></td></tr>
	<tr>
		<td colspan="2">
		<DIV style="width:100%; height:100%; overflow:hidden;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">
			<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%" style="table-layout:fixed">
<?
			$colspan=7;
			if($vendercnt>0) $colspan++;
?>
			<col width=40></col>
			<?if($vendercnt>0){?>
			<col width=70></col>
			<?}?>
			<col width=50></col>
			<col width=></col>
			<col width=70></col>
			<col width=45></col>
			<col width=45></col>
			<col width=45></col>
			<TR>
				<TD colspan="<?=$colspan?>" background="images/table_top_line.gif"></TD>
			</TR>
			<TR align="center">
				<TD class="table_cell">No</TD>
				<?if($vendercnt>0){?>
				<TD class="table_cell1">입점업체</TD>
				<?}?>
				<TD class="table_cell1" colspan="2">상품권명</TD>
				<TD class="table_cell1">판매가격</TD>
				<TD class="table_cell1">수량</TD>
				<TD class="table_cell1">상태</TD>
				<TD class="table_cell1">수정</TD>
			</TR>
<?
			if(strlen($code)==12) {
				$page_numberic_type=1;
				$codeA=substr($code,0,3);
				$codeB=substr($code,3,3);
				$codeC=substr($code,6,3);
				$codeD=substr($code,9,3);
				$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' ";
				$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";
				$result=mysql_query($sql,get_db_conn());
				$row=mysql_fetch_object($result);
				mysql_free_result($result);
				if($row && ereg("X",$row->type)) {
					$qry = "AND productcode LIKE '".$code."%' ";
					if(strlen($keyword)>2) {
						$qry.= "AND productname LIKE '%".$keyword."%' ";
					}
					$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct WHERE 1=1 ";
					$sql0.= $qry;
					$result = mysql_query($sql0,get_db_conn());
					$row = mysql_fetch_object($result);
					mysql_free_result($result);
					$t_count = $row->t_count;
					$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

					$sql = "SELECT option_price, productcode,productname,production,sellprice,consumerprice, ";
					$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,selfcode,assembleuse ";
					$sql.= "FROM tblproduct WHERE 1=1 ";
					$sql.= $qry." ";
					if ($sort=="price")				$sql.= "ORDER BY sellprice ";
					else if ($sort=="productname")	$sql.= "ORDER BY productname ";
					else							$sql.= "ORDER BY date DESC ";
					$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
					$result = mysql_query($sql,get_db_conn());
					$cnt=0;
					while($row=mysql_fetch_object($result)) {
						$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
						$cnt++;
						
						echo "<tr>\n";
						echo "	<TD colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></TD>\n";
						echo "</tr>\n";
						echo "<tr align=\"center\">\n";
						echo "	<TD class=\"td_con2\">".$number."</td>\n";
						if($vendercnt>0) {
							echo "	<TD class=\"td_con1\"><B>".(strlen($venderlist[$row->vender]->vender)>0?"<a href=\"javascript:viewVenderInfo(".$row->vender.")\">".$venderlist[$row->vender]->id."</a>":"-")."</B></td>\n";
						}
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
						echo "	<TD class=\"td_con1\" align=\"left\" style=\"word-break:break-all;\"><img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\"><A HREF=\"javascript:ProductSend('modify','".$row->productcode."');\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."</A>&nbsp;</td>\n";
						echo "	<TD align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($row->sellprice)."</span></TD>\n";
						echo "	<TD class=\"td_con1\">";
						if (strlen($row->quantity)==0) echo "무제한";
						else if ($row->quantity<=0) echo "<span class=\"font_orange\"><b>품절</b></span>";
						else echo $row->quantity;
						echo "	</TD>\n";
						echo "	<TD class=\"td_con1\">".($row->display=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
						echo "	<TD class=\"td_con1\"><a href=\"javascript:ProductSend('modify','".$row->productcode."');\"><img src=\"images/icon_edit2.gif\" border=\"0\"></a></td>\n";
						echo "</tr>\n";
					}
					mysql_free_result($result);
					if ($cnt==0) {
						$page_numberic_type="";
						echo "<tr><td class=\"td_con2\" colspan=\"".$colspan."\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
					}
				} else {
					$page_numberic_type="";
					echo "<tr><td class=\"td_con2\" colspan=\"".$colspan."\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
				}
			} else {
				$page_numberic_type="";
				echo "<tr><td class=\"td_con2\" colspan=\"".$colspan."\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
			}
?>
			<TR>
				<TD height="1" colspan="<?=$colspan?>" background="images/table_top_line.gif"></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
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
			<td style="padding-top:12px;BORDER-top:#ededed 2px solid;"><img width="0" height="0"></td>
		</tr>
		</table>
		</div>
	</table>
	</td>
</tr>
</form>
<?if($vendercnt>0){?>
<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>
<?}?>
</table>
</body>
</html>