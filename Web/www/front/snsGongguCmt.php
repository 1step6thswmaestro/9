<?php
header('Content-Type: text/html; charset=euc-kr'); 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>
<table cellpadding="0" cellspacing="0" width="100%">
<?
$detail_type = $_POST["detail_type"];
$pcode = $_POST["pcode"];
$gotopage = $_POST["gotopage"];
$block=$_POST["block"];
$icolspan =7;
$pageSize = 10;
$list_num = 10;

if(strlen($pcode)>0){
	$sCondition = "AND pcode='".$pcode."' AND seq=c_seq ";
	$icolspan =5;
	$list_num = 5;
}

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $pageSize + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}

$sql = "SELECT COUNT(*) as t_count FROM tblsnsGongguCmt  WHERE c_order=1 ".$sCondition;
$result = mysql_query($sql,get_db_conn());

$row = mysql_fetch_object($result);
$t_count = $row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $list_num) + 1;
//echo $t_count;

$arIconImage = array("t"=>"twitter","f"=>"facebook","m"=>"me2day");
$sql = "SELECT A.* ";
$sql .=", (SELECT profile_img FROM tblmembersnsinfo B WHERE A.id=B.id ORDER BY B.regidate DESC limit 1) profile_img ";
if(strlen($pcode)==0){
	$sql .=", (SELECT tinyimage FROM tblproduct C WHERE C.productcode=A.pcode ) tinyimage ";
}
$sql .="FROM tblsnsGongguCmt A ";
$sql .="WHERE c_order=1 ".$sCondition;
$sql .="ORDER BY regidate DESC ";
$sql .= "LIMIT " . ($list_num * ($gotopage - 1)) . ", " . $list_num;
$result=mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)) {
	$icon="";
	$artype = explode(",",$row->sns_type);
	for($i=0;$i<sizeof($artype)-1;$i++){
		$icon .= "<img src=\"../images/design/icon_".$arIconImage[$artype[$i]]."_on.gif\" align=\"absmiddle\" WIDTH=\"17\" HEIGHT=\"17\"> ";
	}
	$id = $row->id;
	$comment = $row->comment;
	$cmt_count = $row->count;
	$sns_date = date("Y-m-d H:i:s", $row->regidate);
	$profile_img = $row->profile_img;
	if(strlen($profile_img) == 0){
		$profile_img="/images/design/sns_default.jpg";
	}
	$cmtsubList ="";
	$cmtState ="";
	if($cmt_count>1){
		$cmtsubList ="<IMG SRC=\"../images/design/gonggu_order_btn03.gif\" WIDTH=101 HEIGHT=22 ALT=\"\" class=\"cmtlistBtn\" style=\"cursor:pointer;margin-left:7px;\"><span style=\"display:none;\">".$row->seq."</span>";
	}
	$mem_id = ($_ShopInfo->getMemid() == $row->id)? "":$row->id;

	switch($row->rqt_state){
		case "1":
			$cmtState = "<a href=\"javascript:;\" onclick=\"checkWrite('".$row->seq."','".$row->pcode."','".$mem_id."');return false;\"><IMG SRC=\"../images/design/gonggu_order_btn02.gif\" WIDTH=101 HEIGHT=22 ALT=\"\"></a>";break;
		case "2":
			$cmtState = "<IMG SRC=\"../images/design/gonggu_order_btn02_n.gif\" WIDTH=101 HEIGHT=22 ALT=\"\">";break;//미진행
		case "3":
			$cmtState = "<IMG SRC=\"../images/design/gonggu_order_btn02_y.gif\" WIDTH=101 HEIGHT=22 ALT=\"\">";break;//진행
		case "4":
			$cmtState = "<IMG SRC=\"../images/design/gonggu_order_btn02_e.gif\" WIDTH=101 HEIGHT=22 ALT=\"\">";break;//완료
	}
	$sPdtThumb ="";
	if(strlen($pcode)==0){
		$sPdtThumb = "<td width=\"130\" valign=\"top\"><a href=\"../front/productdetail.php?productcode=".$row->pcode."\">";
		if(strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
			$width=GetImageSize($Dir.DataDir."shopimages/product/".$row->tinyimage);
			if($width[0]>=130) $width[0]=130;
			else if (strlen($width[0])==0) $width[0]=130;
			$sPdtThumb .= "<img src=\"".$Dir.DataDir."shopimages/product/".$row->tinyimage."\" border=\"0\" width=\"".$width[0]."\" class=\"img\">";
		} else {
			$sPdtThumb .= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" WIDTH=130 HEIGHT=100 class=\"img\">";
		}
		$sPdtThumb .= "</a></td>
		<td width=\"25\" valign=\"top\"><img src=\"../images/design/space_line.gif\" width=\"15\" height=\"1\" border=\"0\"></td>\n";
	}
	$delBtn = "";
	if($cmt_count==1){
		$delBtn=($_ShopInfo->getMemid() == $row->id)? "<a href=\"javascript:;\" onclick=\"delGongguCmt('".$row->seq."')\"><IMG SRC=\"../images/design/gonggu_order_del.gif\" ALT=\"\" style=\"cursor:pointer;\"></a>":"";
	}

?>
<tr>
	<?=$sPdtThumb?>
	<td width="48" valign="top"><IMG SRC="<?=$profile_img?>" WIDTH="48" HEIGHT="48" ALT="" class="img"></td>
	<td width="13" valign="top"><img src="../images/design/space_line.gif" width="10" height="1" border="0"></td>
	<td width="100%" valign="top">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><div style="float:right;"><?=$delBtn ?></div>
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td style="padding-right:10px;"><?=$icon?></td>
							<td style="padding-right:10px;" class="gongguing_order_id"><?=$id?></td>
							<td style="padding-right:10px;" class="gongguing_order_date"><?=$sns_date?></td>
						</tr>
					</table>
					
				</td>
			</tr>
			<tr>
				<td class="table_td"><?=$comment?></td>
			</tr>
		</table>
	</td>
	<td width="10">&nbsp;</td>
	<td align="center" width="230px">
		<table cellpadding="3" cellspacing="0">
			<tr>
				<td class="gongguing_order_order" align="center"><?=$cmt_count?></td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td><?=$cmtState?><?=$cmtsubList?></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan="<?=$icolspan?>" id="GongguCmtSubList<?=$row->seq?>"></td>
</tr>
<tr>
	<td colspan="<?=$icolspan?>" height="45"><img src="../images/design/con_line02.gif" width="100%" height="1" border="0"></td>
</tr>
<?}?>
<tr>
	<td colspan="<?=$icolspan?>" align="right" class="table01_con2">
<?
		$total_block = intval($pagecount / $pageSize);

		if (($pagecount % $pageSize) > 0) {
			$total_block = $total_block + 1;
		}

		$total_block = $total_block - 1;

		if (ceil($t_count/$list_num ) > 0) {
			// 이전	x개 출력하는 부분-시작
			$a_first_block = "";
			if ($nowblock > 0) {
				$a_first_block .= "<a href='#snsGongguList' onclick='showGongguCmt(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=\"../images/design/btn_first.gif\" height=\"22\" border=\"0\" align=\"absmiddle\"></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='#snsGongguList' onclick='showGongguCmt(".($nowblock-1).",".($pageSize*($block-1)+$pageSize).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$pageSize." 페이지';return true\"><FONT class=\"table01_con2\"><img src=\"../images/design/btn_pre.gif\" height=\"22\" border=\"0\" align=\"absmiddle\"></FONT></a>&nbsp;&nbsp;";

				$a_prev_page = $a_first_block.$a_prev_page;
			}

			// 일반 블럭에서의 페이지 표시부분-시작

			if (intval($total_block) <> intval($nowblock)) {
				$print_page = "";
				for ($gopage = 1; $gopage <= $pageSize; $gopage++) {
					if ((intval($nowblock*$pageSize) + $gopage) == intval($gotopage)) {
						$print_page .= "<b><font color=\"#FF511B\">".(intval($nowblock*$pageSize) + $gopage)."</font></b> ";
					} else {
						$print_page .= "<a href='#snsGongguList' onclick='showGongguCmt(".$nowblock.",".(intval($nowblock*$pageSize) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$pageSize) + $gopage)."';return true\"><FONT class=\"table01_con2\">".(intval($nowblock*$pageSize) + $gopage)."</FONT></a> ";
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
						$print_page .= "<a href='#snsGongguList' onclick='showGongguCmt(".$nowblock.",".(intval($nowblock*$pageSize) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$pageSize) + $gopage)."';return true\"><FONT class=\"table01_con2\">".(intval($nowblock*$pageSize) + $gopage)."</FONT></a> ";
					}
				}
			}		// 마지막 블럭에서의 표시부분-끝


			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($list_num *$pageSize)) - 1;
				$last_gotopage = ceil($t_count/$list_num );

				$a_last_block .= "&nbsp;&nbsp;<a href='#snsGongguList' onclick='showGongguCmt(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><FONT class=\"prlist\"><img src=\"../images/design/btn_end.gif\" height=\"22\" border=\"0\" align=\"absmiddle\"></FONT></a>";

				$next_page_exists = true;
			}

			// 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='#snsGongguList' onclick='showGongguCmt(".($nowblock+1).",".($pageSize*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$pageSize." 페이지';return true\"><FONT class=\"table01_con2\"><img src=\"../images/design/btn_next.gif\" height=\"22\" border=\"0\" align=\"absmiddle\"></FONT></a>";

				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "<FONT class=\"table01_con2\">1</FONT>";
		}
		echo $a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page
?>
	
	</td>
</tr>
<tr>
	<td colspan="<?=$icolspan?>" height="15"></td>
</tr>
</table>
<script type="text/javascript">
$j("#gongCmtTot").html("<?=$t_count?>");
$j('.cmtlistBtn').click(function(){
	showGongguCmtRe($j(this));
});
</script>
