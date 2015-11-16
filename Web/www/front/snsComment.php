<?php
header('Content-Type: text/html; charset=euc-kr');
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>
<table cellpadding="0" cellspacing="0" width="100%">
<?
$gbn = $_POST["gbn"];
$pcode = $_POST["pcode"];
$gotopage = $_POST["gotopage"];
$block=$_POST["block"];
$list_num=$_POST["list_num"];

//리스트 세팅
$pageSize = 10;
if (($list_num == "") || ($list_num == 0)) {
	$list_num = 5;
}
$arIconImage = array("t"=>"twitter","f"=>"facebook","m"=>"me2day");

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $pageSize + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}
if($gbn == "main"){	$sql = "SELECT A.*, B.profile_img, B.user_id ";
	$sql .=",(SELECT tinyimage FROM tblproduct C	WHERE A.pcode=C.productcode) tinyimage ";
	$sql .=" FROM tblsnscomment A, tblmembersnsinfo B ";
	$sql .=" WHERE A.id=B.id ";
	$sql .=" AND substring(A.sns_type, 1, 1)= B.type ";
	$sql .=" AND sns_type <> '' ";
	$sql .=" ORDER BY A.regidate DESC ";
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
		$sns_date = date("Y-m-d H:i:s", $row->regidate);
		$profile_img = $row->profile_img;
		if(strlen($profile_img) == 0){
			$profile_img="/images/design/sns_default.jpg";
		}
		if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
			$tinyimage = $Dir.DataDir."shopimages/product/".urlencode($row->tinyimage);
		} else {
			$tinyimage = $Dir."images/no_img.gif";
		}
		if($artype[0] == "f"){
			$snsLink ="http://www.facebook.com/profile.php?id=".$row->user_id;
		}else if($artype[0] == "m"){
			$snsLink ="http://me2day.net/".$row->user_id;
		}else if($artype[0] == "t"){
			$snsLink ="http://twitter.com/@".$row->user_id;
		}
?>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="200px" style="TABLE-LAYOUT: fixed;word-wrap:break-word;">
				<tr>
					<td width="42" valign="top"><IMG src="<?=$profile_img?>" width=36 height=36><br><a href="productdetail.php?productcode=<?=$row->pcode?>"><IMG class=img border=0 alt="" vspace=5 src="<?=$tinyimage?>" width="36" height="27"></a></td>
					<td valign="top" class="table01_con"><A href="<?=$snsLink?>" target=_blank><font color="#219AA6"><?=$id?></A></font> <?=$comment?><br><font color="#AAAAAA"><?=$sns_date?></font><br><?=$icon?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="20" align=center><img src="../images/design/con_line02.gif" width="200" height="1" border="0"></td>
	</tr>
<?
	}
}else{
	$sql = "SELECT COUNT(*) as t_count FROM tblsnscomment  WHERE 1=1 AND pcode='".$pcode."'";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$t_count = $row->t_count;
	mysql_free_result($result);
	$pagecount = (($t_count - 1) / $list_num) + 1;
	//echo $t_count;

	$sql = "SELECT A.*";
	$sql .="FROM tblsnscomment A ";
	$sql .= "WHERE 1=1 AND  pcode='".$pcode."' ORDER BY regidate DESC ";
	$sql .= "LIMIT " . ($list_num * ($gotopage - 1)) . ", " . $list_num;



	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {

		$id = $row->id;
		$comment = $row->comment;
		$sns_date = date("Y-m-d H:i:s", $row->regidate);

		$icon="";
		$artype = explode(",",$row->sns_type);
		for($i=0;$i<sizeof($artype)-1;$i++){

			$snsSql = "SELECT `profile_img`, `link` FROM `tblmembersnsinfo` WHERE `id` = '".$id."' AND `type` = '".$artype[$i]."' ";
			$snsResult=mysql_query($snsSql,get_db_conn());
			$snsRow=mysql_fetch_object($snsResult);

			$snsUserPop = "";
			if( strlen( $snsRow->link ) > 0 ){
				$snsUserPop = "style=\"cursor:pointer;\" onclick=\" window.open('".$snsRow->link."', '".$artype[$i]."_pop'); \" ";
			}

			$icon .= "<img src=\"../images/design/icon_".$arIconImage[$artype[$i]]."_on.gif\" align=\"absmiddle\" WIDTH=\"17\" HEIGHT=\"17\" ".$snsUserPop."> ";

			if( strlen($snsRow->profile_img) > 0 ) $profile_img = $snsRow->profile_img;
			if(strlen($profile_img) == 0){
				$profile_img="/images/design/sns_default.jpg";
			}
			mysql_free_result($snsResult);

		}


?>
	<tr>
		<td width="48" valign="top"><IMG SRC="<?=$profile_img?>" WIDTH="48" HEIGHT="48" ALT="" class="img"></td>
		<td width="13" valign="top"><img src="../images/design/space_line.gif" width="10" height="1" border="0"></td>
		<td width="100%" valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
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
	</tr>
	<tr>
		<td colspan="3" height="25"><img src="../images/design/con_line02.gif" width="100%" height="1" border="0"></td>
	</tr>
<?	}?>
	<tr>
		<td colspan="3" height="35" align="center" class="table01_con2">
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
				$a_first_block .= "<a href='javascript:showSnsComment(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=\"../images/design/btn_first.gif\" border=\"0\" hspace=\"0\"></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='javascript:showSnsComment(".($nowblock-1).",".($pageSize*($block-1)+$pageSize).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$pageSize." 페이지';return true\"><img src=\"../images/design/btn_pre.gif\" border=\"0\" hspace=\"3\"></a>&nbsp;&nbsp;";

				$a_prev_page = $a_first_block.$a_prev_page;
			}

			// 일반 블럭에서의 페이지 표시부분-시작
			if (intval($total_block) <> intval($nowblock)) {
				$print_page = "";
				for ($gopage = 1; $gopage <= $pageSize; $gopage++) {
					if ((intval($nowblock*$pageSize) + $gopage) == intval($gotopage)) {
						$print_page .= "<b><font color=\"#FF511B\">".(intval($nowblock*$pageSize) + $gopage)."</font></b> ";
					} else {
						$print_page .= "<a href='javascript:showSnsComment(".$nowblock.",".(intval($nowblock*$pageSize) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$pageSize) + $gopage)."';return true\">".(intval($nowblock*$pageSize) + $gopage)."</a> ";
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
						$print_page .= "<a href='javascript:showSnsComment(".$nowblock.",".(intval($nowblock*$pageSize) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$pageSize) + $gopage)."';return true\">".(intval($nowblock*$pageSize) + $gopage)."</a> ";
					}
				}
			}		// 마지막 블럭에서의 표시부분-끝

			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($list_num*$pageSize)) - 1;
				$last_gotopage = ceil($t_count/$list_num);

				$a_last_block .= "&nbsp;&nbsp;<a href='javascript:showSnsComment(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=\"../images/design/btn_end.gif\" border=\"0\" hspace=\"0\"></a>";

				$next_page_exists = true;
			}

			// 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='javascript:showSnsComment(".($nowblock+1).",".($pageSize*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$pageSize." 페이지';return true\"><img src=\"../images/design/btn_next.gif\" border=\"0\" hspace=\"3\"></a>";

				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "<b><font color=\"#FF511B\">1</font></b>";
		}
		echo $a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page
?>
		</td>
	</tr>
	<script type="text/javascript">
		$j("#snsCmtTot").html("<?=$t_count?>");
		$j("#detail_btn_snsch").attr("src","/images/design/detail_btn_snsch.gif");
	</script>
<?}?>
</table>
