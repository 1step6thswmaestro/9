<?php
header('Content-Type: text/html; charset=euc-kr'); 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$code=$_POST["code"];
$s_check=$_POST["s_check"];
$search_txt=iconv("utf-8","euc-kr",$_POST["search_txt"]);
$gotopage = $_POST["gotopage"];
$block=$_POST["block"];

$codeA=substr($code,0,3);
$codeB=substr($code,3,3);
$codeC=substr($code,6,3);
$codeD=substr($code,9,3);
if(strlen($codeA)!=3) $codeA="000";
if(strlen($codeB)!=3) $codeB="000";
if(strlen($codeC)!=3) $codeC="000";
if(strlen($codeD)!=3) $codeD="000";
$code=$codeA.$codeB.$codeC.$codeD;

$likecode=$codeA;
if($codeB!="000") $likecode.=$codeB;
if($codeC!="000") $likecode.=$codeC;
if($codeD!="000") $likecode.=$codeD;


$pageSize = 10;
$list_num = 4;

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $pageSize + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}

$sql = "SELECT codeA, codeB, codeC, codeD FROM tblproductcode ";
if(strlen($_ShopInfo->getMemid())==0) {
	$sql.= "WHERE group_code!='' ";
} else {
	$sql.= "WHERE group_code!='".$_ShopInfo->getMemgroup()."' AND group_code!='ALL' AND group_code!='' ";
}
$result=mysql_query($sql,get_db_conn());
$not_qry="";
while($row=mysql_fetch_object($result)) {
	$tmpcode=$row->codeA;
	if($row->codeB!="000") $tmpcode.=$row->codeB;
	if($row->codeC!="000") $tmpcode.=$row->codeC;
	if($row->codeD!="000") $tmpcode.=$row->codeD;
	$not_qry.= "AND a.productcode NOT LIKE '".$tmpcode."%' ";
}
mysql_free_result($result);


$qry = "WHERE 1=1 ";
if(strlen($likecode)>0) {
	$qry.= "AND a.productcode LIKE '".$likecode."%' ";
}
//검색조건 처리
if(strlen($s_check)>0 && strlen($search_txt)>0) {
	$skeys = explode(" ",$search_txt);
	@setlocale(LC_CTYPE , C);
	for($j=0;$j<count($skeys);$j++) {
		$skeys[$j]=strtoupper(trim($skeys[$j]));
		if(strlen($skeys[$j])>0) {
			if($s_check=="keyword") {
				$qry.= "AND (UPPER(a.productname) LIKE '%".$skeys[$j]."%' OR UPPER(a.keyword) LIKE '%".$skeys[$j]."%') ";
			} else if($s_check=="code") {
				$qry.= "AND a.productcode LIKE '".$skeys[$j]."%' ";
			} else {
				$qry.= "AND (UPPER(a.productname) LIKE '%".$skeys[$j]."%' OR UPPER(a.keyword) LIKE '%".$skeys[$j]."%' OR a.productcode LIKE '".$skeys[$j]."%' OR UPPER(a.production) LIKE '%".$skeys[$j]."%' OR UPPER(a.model) LIKE '%".$skeys[$j]."%' OR UPPER(a.selfcode) LIKE '%".$skeys[$j]."%' OR UPPER(a.content) LIKE '%".$skeys[$j]."%') ";
			}
		}
	}
}

$qry.= "AND a.display!='N' AND a.gonggu_product='Y' ";
if(strlen($not_qry)>0) $qry.= $not_qry;

$sql = "SELECT COUNT(*) as t_count ";
$sql.= "FROM tblproduct AS a ";
$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
$sql.= $qry;
$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$t_count = (int)$row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $list_num) + 1;
?>				
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
<?
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
		$sql.= "a.tinyimage, a.date, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= $qry." ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.= "ORDER BY a.productname ";
		$sql.= "LIMIT " . ($list_num * ($gotopage - 1)) . ", " . $list_num;
		$result=mysql_query($sql,get_db_conn());
		
		$i=0;
		while($row=mysql_fetch_object($result)) {
			if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
				$sellprice = $dicker;
			} else if(strlen($_data->proption_price)==0) {
				$sellprice = number_format($row->sellprice)."원";
				if (strlen($row->option_price)!=0) $sellprice .= "(기본가)";
			} else {
				if (strlen($row->option_price)==0) $sellprice = number_format($row->sellprice)."원";
				else $sellprice = ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
			}			

			if ($i!=0 && $i%4!=0) {
				echo "<td width=\"7\" valign=\"top\" align=\"center\">&nbsp;</td>";
			}
			echo "<td valign=\"top\" align=\"center\">\n";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
				echo "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" id=\"thumb_".$row->productcode."\"";
			} else {
				echo "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			echo " WIDTH=130 HEIGHT=100 alt=\"".strip_tags($row->productname)."\" class=\"img\" >";
			echo "		<TABLE cellSpacing=0 cellPadding=0 width=\"130\">\n";
			echo "			<TR><TD class=\"table_td\" align=\"center\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</TD></TR>\n";
			echo "			<TR><TD height=10></TD></TR>\n";
			if($row->consumerprice!=0) {
				echo "			<TR><TD class=table_td><IMG border=0 align=absMiddle src=\"../images/design/gonggu_end_price.gif\" width=34 height=17>".number_format($row->consumerprice)."원</TD></TR>\n";
			}
			echo "			<TR><TD class=table_td><IMG border=0 align=absMiddle src=\"../images/design/icon_price.gif\" width=34 height=17><B><FONT color=#3455de>".$row->sellprice."</FONT></B></TD></TR>\n";
			echo "			<TR><TD class=table_td><a href=\"javascript:;\" onclick=\"selectProduct('".$row->productcode."');\"><IMG SRC=\"../images/design/popgonggu_search_btn02.gif\" WIDTH=56 HEIGHT=15 ALT=\"\"></a></TD></TR>\n";
			echo "		</TABLE>\n";
			echo "	</td>\n";

			$i++;
		}
		if($i>0 && $i<4) {
			for($k=0; $k<(4-$i); $k++) {
				echo "<td width=\"7\">&nbsp;</td>\n<td width=\"24%\">&nbsp;</td>\n";
			}
		}
		mysql_free_result($result);
		if($i == 0){
			echo "<td colspan=\"7\ height=\"30\" align=\"center\">검색 결과가 없습니다.</td>";
		}
?>
</tr>
<tr>
	<td colspan="7" height="30"><img src="../images/design/con_line02.gif" width="615" height="1" border="0"></td>
</tr>
<tr>
	<td colspan="7" class="table01_con2" align="center">
<?
		$total_block = intval($pagecount / $pageSize);

		if (($pagecount % $pageSize) > 0) {
			$total_block = $total_block + 1;
		}

		$total_block = $total_block - 1;

		if (ceil($t_count/$list_num) > 0) {
			// 이전	x개 출력하는 부분-시작
			$a_first_block = "";
			if ($nowblock > 0) {
				$a_first_block .= "<a href='javascript:searchPList(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=\"../images/design/btn_first.gif\" border=\"0\" hspace=\"0\"></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='javascript:searchPList(".($nowblock-1).",".($pageSize*($block-1)+$pageSize).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$pageSize." 페이지';return true\"><img src=\"../images/design/btn_pre.gif\" border=\"0\" hspace=\"3\"></a>&nbsp;&nbsp;";

				$a_prev_page = $a_first_block.$a_prev_page;
			}

			// 일반 블럭에서의 페이지 표시부분-시작
			if (intval($total_block) <> intval($nowblock)) {
				$print_page = "";
				for ($gopage = 1; $gopage <= $pageSize; $gopage++) {
					if ((intval($nowblock*$pageSize) + $gopage) == intval($gotopage)) {
						$print_page .= "<b><font color=\"#FF511B\">".(intval($nowblock*$pageSize) + $gopage)."</font></b> ";
					} else {
						$print_page .= "<a href='javascript:searchPList(".$nowblock.",".(intval($nowblock*$pageSize) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$pageSize) + $gopage)."';return true\">".(intval($nowblock*$pageSize) + $gopage)."</a> ";
					}
				}
			} else {
				if (($pagecount % $pageSize) == 0) {
					$lastpage = $pageSize;
				} else {
					$lastpage = $pagecount % $pageSize;
				}

				for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
					if (intval($nowblock*$pageSize) + $gopage == intval($gotopage)) {
						$print_page .= "<b><font color=\"#FF511B\">".(intval($nowblock*$pageSize) + $gopage)."</font></b> ";
					} else {
						$print_page .= "<a href='javascript:searchPList(".$nowblock.",".(intval($nowblock*$pageSize) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$pageSize) + $gopage)."';return true\">".(intval($nowblock*$pageSize) + $gopage)."</a> ";
					}
				}
			}		// 마지막 블럭에서의 표시부분-끝

			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($list_num*$pageSize)) - 1;
				$last_gotopage = ceil($t_count/$list_num);

				$a_last_block .= "&nbsp;&nbsp;<a href='javascript:searchPList(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=\"../images/design/btn_end.gif\" border=\"0\" hspace=\"0\"></a>";

				$next_page_exists = true;
			}

			// 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='javascript:searchPList(".($nowblock+1).",".($pageSize*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$pageSize." 페이지';return true\"><img src=\"../images/design/btn_next.gif\" border=\"0\" hspace=\"3\"></a>";

				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "<b><font color=\"#FF511B\">1</font></b>";
		}
		echo $a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page
?>
	</td>
</tr>
</table>
<script type="text/javascript">
$j("#prdtSchCount").html("<?=$t_count?>");
</script>