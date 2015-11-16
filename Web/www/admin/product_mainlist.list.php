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
$code=$_POST["code"];
$keyword=$_POST["keyword"];
$prcode=$_POST["prcode"];

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
<script>//LH.add("parent_resizeIframe('ListFrame')");</script>
<SCRIPT LANGUAGE="JavaScript">
<!--
var ProductInfoStop="";

<?if($vendercnt>0){?>
function viewVenderInfo(vender) {
	ProductInfoStop = "1";
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

function GoSort(sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = "";
	document.form1.gotopage.value = "";
	document.form1.submit();
}

/*
function SelectList(idx)
{
	if(ProductInfoStop)
		ProductInfoStop = "";
	else
	{
		if(document.getElementById("pidx_"+idx))
		{
			var layer_id = document.getElementById("pidx_"+idx);
			var prcode_value = document.form1.prcode;

			if(idx == prcode_value.value)
			{
				prcode_value.value = "";
				layer_id.style.backgroundColor = "";
			}
			else
			{
				if(prcode_value.value>0)
				{
					if(document.getElementById("pidx_"+prcode_value.value))
					{
						var layer_pid = document.getElementById("pidx_"+prcode_value.value);
						prcode_value.value = idx;
						layer_pid.style.backgroundColor = "";
						layer_id.style.backgroundColor = "#EFEFEF";
					}
					else
					{
						prcode_value.value = idx;
						layer_id.style.backgroundColor = "#EFEFEF";
					}
				}
				else
				{
					prcode_value.value = idx;
					layer_id.style.backgroundColor = "#EFEFEF";
				}
			}
		}
	}
}
*/

var prcodearr = new Array();
function SelectList(idx){
	if(ProductInfoStop) ProductInfoStop = "";
	else{
		//var prcodearr = new Array();
		var pidx = -1;
		var prcode_value = document.form1.prcode;
		prcodestr = prcode_value.value;
		prcodestr.replace(/[^0-9,]/,'');

		if(prcodestr.length > 1){
			prcodearr = prcodestr.split(',');
			pidx = prcodearr.indexOf(idx);
		}else{
			var prcodearr = new Array();
		}
		if(pidx != -1){
			prcodearr.splice(pidx,1);
			if(document.getElementById("pidx_"+idx)) document.getElementById("pidx_"+idx).style.backgroundColor = '';
		}else{
			prcodearr.push(idx);
			//if(document.getElementById("pidx_"+idx)) document.getElementById("pidx_"+idx).style.backgroundColor = "#EFEFEF";
		}
		for(i=0;i<prcodearr.length;i++){
			if(document.getElementById("pidx_"+prcodearr[i])) document.getElementById("pidx_"+prcodearr[i]).style.backgroundColor = "#EFEFEF";
			else alert('d');
		}
		prcode_value.value = prcodearr.join(',');
	}
}

function SelClear(){
	var prcode_value = document.form1.prcode;
	prcodestr = prcode_value.value;
	prcodestr.replace(/[^0-9,]/,'');

	if(prcodestr.length > 1){
		prcodearr = prcodestr.split(',');
		for(i=0;i<prcodearr.length;i++){
			if(document.getElementById("pidx_"+prcodearr[i])) document.getElementById("pidx_"+prcodearr[i]).style.backgroundColor = "";
		}
	}
	prcode_value.value = '';
	prcodearr = new Array();
}

function onMouseColor(argValue){
	//if(document.form1.prcode.value != argValue)
	if(prcodearr.indexOf(argValue) != -1)
		return true;
	else
		return false;
}

function ProductInfo(prcode) {
	ProductInfoStop = "1";
	code=prcode.substring(0,12);
	popup="YES";
	document.form_reg.code.value=code;
	document.form_reg.prcode.value=prcode;
	document.form_reg.popup.value=popup;
	if (popup=="YES") {
		document.form_reg.action="product_register.add.php";
		document.form_reg.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_reg.action="product_register.php";
		document.form_reg.target="";
	}
	document.form_reg.submit();
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
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" bgcolor="#ffffff">
<tr>
	<td width="100%" bgcolor="#FFFFFF"><IMG SRC="images/product_mainlist_text.gif" border="0"></td>
</tr>
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=keyword value="<?=$keyword?>">
<input type=hidden name=prcode value="">
<tr>
	<td width="100%" height="100%" valign="top" style="BORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" style="padding-top:2pt; padding-bottom:2pt;" height="30"><B><span class="font_orange">* 정렬방법 :</span></B> <A HREF="javascript:GoSort('date');">진열순</a> | <A HREF="javascript:GoSort('productname');">상품명순</a> | <A HREF="javascript:GoSort('price');">가격순</a></td>
	</tr>
	<tr>
		<td width="100%">
		<DIV style="width:100%;height:100%;overflow:hidden;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">
			<TABLE border="0" cellSpacing="0" cellPadding="0" width="100%">
<?
			$colspan=7;
			if($vendercnt>0) $colspan++;
?>
			<col width=50></col>
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
				<TD class="table_cell1" colspan="2">상품명/진열코드/특이사항</TD>
				<TD class="table_cell1">판매가격</TD>
				<TD class="table_cell1">수량</TD>
				<TD class="table_cell1">상태</TD>
				<TD class="table_cell1">수정</TD>
			</TR>
<?
			if(strlen($code)==12) {
				$page_numberic_type=1;
				$likecode=substr($code,0,3);
				if(substr($code,3,3)!="000") {
					$likecode.=substr($code,3,3);
					if(substr($code,6,3)!="000") {
						$likecode.=substr($code,6,3);
						if(substr($code,9,3)!="000") {
							$likecode.=substr($code,9,3);
						}
					}
				}

				$qry = "WHERE productcode LIKE '".$likecode."%' ";
				if(strlen($keyword)>2) {
					$qry.= "AND productname LIKE '%".$keyword."%' ";
				}
				$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct ";
				$sql0.= $qry;
				$result = mysql_query($sql0,get_db_conn());
				$row = mysql_fetch_object($result);
				mysql_free_result($result);
				$t_count = $row->t_count;
				$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

				$sql = "SELECT option_price, productcode,productname,production,sellprice,consumerprice, ";
				$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,selfcode,assembleuse ";
				$sql.= "FROM tblproduct ";
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
					echo "<tr align=\"center\" id=\"pidx_".$row->productcode."\" onclick=\"SelectList('".$row->productcode."')\" onmouseover=\"if(onMouseColor('".$row->productcode."'))this.style.backgroundColor='#F4F7FC';\" onmouseout=\"if(onMouseColor('".$row->productcode."'))this.style.backgroundColor='';\" style=\"cursor:hand;\">\n";
					echo "	<TD class=\"td_con2\">".$number."<br><img src=\"images/btn_select1a.gif\" border=\"0\"></td>\n";
					if($vendercnt>0) {
						echo "	<TD class=\"td_con1\"><B>".(strlen($venderlist[$row->vender]->vender)>0?"<span onclick=\"viewVenderInfo(".$row->vender.")\">".$venderlist[$row->vender]->id."</span>":"-")."</B></td>\n";
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
					echo "	<TD class=\"td_con1\" align=\"left\" style=\"word-break:break-all;\"><img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."&nbsp;</td>\n";
					echo "	<TD align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($row->sellprice)."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")."</TD>\n";
					echo "	<TD class=\"td_con1\">";
					if (strlen($row->quantity)==0) echo "무제한";
					else if ($row->quantity<=0) echo "<span class=\"font_orange\"><b>품절</b></span>";
					else echo $row->quantity;
					echo "	</TD>\n";
					echo "	<TD class=\"td_con1\">".($row->display=="Y"?"<font color=\"#0000FF\">판매중</font>":"<font color=\"#FF4C00\">보류중</font>")."</td>";
					echo "	<TD class=\"td_con1\"><a href=\"javascript:ProductInfo('".$row->productcode."');\"><img src=\"images/icon_newwin1.gif\" border=\"0\"></a></td>\n";
					echo "</tr>\n";
				}
				mysql_free_result($result);
				if ($cnt==0) {
					$page_numberic_type="";
					echo "<tr><TD colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan=\"".$colspan."\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
				}
			} else {
				$page_numberic_type="";
				echo "<tr><TD colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan=\"".$colspan."\" align=\"center\">등록된 상품이 없습니다.</td></tr>";
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
			<td style="padding-top:12px;BORDER-top:#eeeeee 2px solid;"><img width="0" height="0"></td>
		</tr>
		</table>
		</div>
		</td>
	</tr>
	</table>
	</td>
</tr>
</form>
<form name=form_reg action="product_register.php" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
</form>
<?if($vendercnt>0){?>
<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>
<?}?>
</table>
<script type="text/javascript">
<!--
	parent.autoResize('ListFrame');
//-->
</script>
</body>
</html>