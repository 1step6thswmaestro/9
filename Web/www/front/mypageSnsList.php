<?php
header('Content-Type: text/html; charset=euc-kr'); 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="10" colspan="3"></td>
	</tr>

<?
$type = $_POST["type"];
$gotopage = $_POST["gotopage"];
$block=$_POST["block"];
//리스트 세팅
$pageSize = 10;
$list_num = $_POST["list_num"];

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $pageSize + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}
if($type !=1){
	$sql = "SELECT COUNT(*) as t_count FROM tblsnscomment A, tblproduct B WHERE 1=1 AND A.pcode=B.productcode  AND id='".$_ShopInfo->getMemid()."' ";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$t_count = $row->t_count;
	mysql_free_result($result);
	$pagecount = (($t_count - 1) / $list_num) + 1;
}

$sql = "SELECT A.* ,B.productname,B.tinyimage,B.sellprice,B.consumerprice ";
$sql .=" FROM tblsnscomment A, tblproduct B ";
$sql .= "WHERE 1=1 AND A.pcode=B.productcode ";
$sql .= "AND  id='".$_ShopInfo->getMemid()."' ";
$sql .= "ORDER BY regidate DESC ";
$sql .= "LIMIT " . ($list_num * ($gotopage - 1)) . ", " . $list_num;
$result=mysql_query($sql,get_db_conn());
$arIconImage = array("t"=>"twitter","f"=>"facebook","m"=>"me2day");
while($row=mysql_fetch_object($result)) {
	$icon="";$tinyimage="";
	$artype = explode(",",$row->sns_type);
	for($i=0;$i<sizeof($artype)-1;$i++){
		$icon .= "<img src=\"../images/design/icon_".$arIconImage[$artype[$i]]."_on.gif\" align=\"absmiddle\" width=17 height=17> ";
	}
	$id = $row->id;
	$comment = $row->comment;
	$sns_date = date("Y-m-d H:i:s", $row->regidate);
	
	if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
		$tinyimage = "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" class=\"img\" border=\"0\" ";
		$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
		if($_data->ETCTYPE["IMGSERO"]=="Y") {
			if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $tinyimage .= "height=\"72\" ";
			else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $tinyimage .= "width=\"94\" ";
		} else {
			if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $tinyimage .= "width=\"94\" ";
			else if ($width[1]>=$_data->primg_minisize) $tinyimage .= "height=\"72\" ";
		}
	} else {
		$tinyimage = "<img src=\"".$Dir."../images/design/no_img.gif\" border=\"0\" align=\"center\" width=\"94\"";
	}
	$tinyimage .=">";
?>

	<tr>
		<td valign="top">
			<TABLE cellSpacing=0 cellPadding=0 width="330">
				<TR>
					<TD>
						<TABLE cellSpacing=0 cellPadding=0>
							<TR>
								<TD style="PADDING-RIGHT: 10px"><?=$icon?></TD>
								<TD style="PADDING-RIGHT: 10px" class=gongguing_order_id><?=$id?></TD>
								<TD style="PADDING-RIGHT: 10px" class=gongguing_order_date><?=$sns_date?></TD>
							</TR>
						</TABLE>
					</TD>
				</TR>
				<TR>
					<TD class=table_td height="35"><?=$comment?></TD>
				</TR>
				<TR>
					<TD class="table_td"></TD>
				</TR>
			</TABLE>
		</td>
		<td width="25" valign="top"></td>
		<td valign="top">
			<TABLE cellSpacing=0 cellPadding=0 width="330">
				<TR>
					<TD vAlign=top width="94"><a href="../front/productdetail.php?productcode=<?=$row->pcode?>"><?=$tinyimage?></a></TD>
					<TD vAlign=top><IMG border=0 src="../images/design/space_line.gif" width=10 height=1></TD>
					<TD vAlign=top>
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td class="table_td"><a href="../front/productdetail.php?productcode=<?=$row->pcode?>"><?=$row->productname?></a></td>
							</tr>
							<tr>
								<td height="10"></td>
							</tr>
							<tr>
								<td class="table_td"><img src="../images/design/gonggu_end_price.gif" align="absmiddle" width="34" height="17" border="0"><?=$row->consumerprice?>원</td>
							</tr>
							<tr>
								<td class="table_td"><img src="../images/design/icon_price.gif" align="absmiddle" width="34" height="17" border="0"><b><font color="#3455DE"><?=$row->sellprice?>원</font></b></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3"><img src="../images/design/con_line02.gif" width="100%" height="1" border="0" vspace="13"></td>
	</tr>

<?}?>
<?
if($type !=1){
		$total_block = intval($pagecount / $pageSize);

		if (($pagecount % $pageSize) > 0) {
			$total_block = $total_block + 1;
		}

		$total_block = $total_block - 1;

		if (ceil($t_count/$list_num ) > 0) {
			// 이전	x개 출력하는 부분-시작
			$a_first_block = "";
			if ($nowblock > 0) {
				$a_first_block .= "<a href='javascript:getSnsList(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=\"../images/design/btn_first.gif\" height=\"22\" border=\"0\" align=\"absmiddle\"></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='javascript:getSnsList(".($nowblock-1).",".($pageSize*($block-1)+$pageSize).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$pageSize." 페이지';return true\"><img src=\"../images/design/btn_pre.gif\" height=\"22\" border=\"0\" align=\"absmiddle\"></a>&nbsp;&nbsp;";

				$a_prev_page = $a_first_block.$a_prev_page;
			}

			// 일반 블럭에서의 페이지 표시부분-시작

			if (intval($total_block) <> intval($nowblock)) {
				$print_page = "";
				for ($gopage = 1; $gopage <= $pageSize; $gopage++) {
					if ((intval($nowblock*$pageSize) + $gopage) == intval($gotopage)) {
						$print_page .= "<b><font color=\"#FF511B\">".(intval($nowblock*$pageSize) + $gopage)."</font></b> ";
					} else {
						$print_page .= "<a href='javascript:getSnsList(".$nowblock.",".(intval($nowblock*$pageSize) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$pageSize) + $gopage)."';return true\">".(intval($nowblock*$pageSize) + $gopage)."</a> ";
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
						$print_page .= "<a href='javascript:getSnsList(".$nowblock.",".(intval($nowblock*$pageSize) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$pageSize) + $gopage)."';return true\">".(intval($nowblock*$pageSize) + $gopage)."</a> ";
					}
				}
			}		// 마지막 블럭에서의 표시부분-끝


			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($list_num *$pageSize)) - 1;
				$last_gotopage = ceil($t_count/$list_num );

				$a_last_block .= "&nbsp;&nbsp;<a href='javascript:getSnsList(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=\"../images/design/btn_end.gif\" height=\"22\" border=\"0\" align=\"absmiddle\"></a>";

				$next_page_exists = true;
			}

			// 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='javascript:getSnsList(".($nowblock+1).",".($pageSize*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$pageSize." 페이지';return true\"><img src=\"../images/design/btn_next.gif\" height=\"22\" border=\"0\" align=\"absmiddle\"></a>";

				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "1";
		}
	echo "	<tr>\n";
	echo "		<td colspan=\"3\" height=\"55\" align=\"center\" class=\"table01_con2\">\n";
	echo $a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
	echo "		<td>\n";
	echo "	<tr>\n";
}
?>
	
</table>