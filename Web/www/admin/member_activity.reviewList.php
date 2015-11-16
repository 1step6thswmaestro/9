<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");
INCLUDE "header.php";
?>
<script type="text/javascript" src="../lib/lib.js.php"></script>
<script type="text/javascript">
<!--
function review_open(prcode,num) {
	window.open("<?=$Dir.FrontDir?>review_popup.php?prcode="+prcode+"&num="+num,"","width=450,height=400,scrollbars=yes");
}
//-->
</script>
<?
//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 15;

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

$colspan=5;
$qry = "WHERE id='".$_GET['memberID']."' ";
$sql = "SELECT COUNT(*) as t_count, SUM(marks) as totmarks FROM tblproductreview ";
$sql.= $qry;
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$t_count = (int)$row->t_count;
$totmarks = (int)$row->totmarks;
$marks=@ceil($totmarks/$t_count);
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;



$reviewPsql = "
	SELECT
		( SELECT COUNT(*) FROM `tblproductreview` WHERE `id`=M.`id` ) AS reviewNo ,
		( SELECT COUNT(*) FROM `tblproductreview` WHERE `id`=M.`id` AND length(`content`) > 25 ) AS reviewMsgNo,
		( SELECT COUNT(*) FROM `tblproductreview` WHERE `id`=M.`id` AND length(`img`) > 0 ) AS reviewImgNo
	FROM
		`tblmember` M
	WHERE
		M.`id` = '".$_GET['memberID']."';
";
$reviewPresult = mysql_query($reviewPsql,get_db_conn());
$reviewProw=mysql_fetch_object($reviewPresult);
$reviewNo = $reviewProw->reviewNo * 5;
$reviewImgNo = $reviewProw->reviewImgNo * 3;
$reviewMsgNo = $reviewProw->reviewMsgNo * 2;
$reviewT = $reviewNo + $reviewImgNo + $reviewMsgNo;
?>

<style>
	.warp {width:100%; margin:0px; padding:0px;}
	.titleStyle {height:32px; padding-left:28px; line-height:32px; color:#fff; font-size:12px; font-family:돋움; font-weight:bold; background:url('images/member_mailallsend_imgbg.gif') repeat-x;}
	.infoText {width:100%; margin:4px 0px; padding:0px; padding-left:28px; color:#888; font-size:11px; font-family:돋움; letter-spacing:-1px;}

	.tblStyle {width:100%; border-top:1px solid #ddd;}
	.tblStyle caption {display:none;}
	.tblStyle th {height:30px; background-color:#f8f8f8; color:#444; font-size:12px; letter-spacing:-1px; border-left:1px solid eee; border-bottom:1px solid #eee;}
	.tblStyle td {border-left:1px solid eee; border-bottom:1px solid #eee;}
</style>

<div class="titleStyle"><?=$_GET['memberName']?>(<font color="#fe8e4b"><?=$_GET['memberID']?></font>)회원님의 리뷰점수</div>

<div class="infoText"><b>리뷰점수 안내</b> : 상품리뷰작성 : <?=$reviewNo?>점 (<?=$reviewProw->reviewNo?>건) + 이미지 첨부 추가 : <?=$reviewImgNo?>점 (<?=$reviewProw->reviewImgNo?>건) + 리뷰내용 25자 이상 추가 : <?=$reviewMsgNo?>점 (<?=$reviewProw->reviewMsgNo?>건)</div>

<table cellpadding="0" cellspacing="0" width="100%">
	<tr><td height="20"></td></tr>
	<tr>
		<td style="padding:4px 15px;">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td style="font-size:11px; letter-spacing:-0.5pt;">* <b><?=$_GET['memberID']?></b>님은 총 <font color="#F02800" style="font-size:11px;letter-spacing:-0.5pt;"><b><?=$t_count?>개</b></font>의  사용후기가 있습니다.</td>
					<td align="right" style="font-size:11px;letter-spacing:-0.5pt;">평균평점 :
					<?
						for($i=0;$i<$marks;$i++) echo "<FONT color=#000000><B>★</B></FONT>";
						for($i=$marks;$i<5;$i++) echo "<FONT color=#DEDEDE><B>★</B></FONT>";
					?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%" class="tblStyle">
				<caption>회원활동관리-리뷰점수</caption>
					<col width="80"></col>
					<col width=></col>
					<col width="80"></col>
					<col width="70"></col>
					<col width="80"></col>
					<col width="70"></col>
				<tr><td height="1" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_line2.gif" colspan="<?=$colspan?>"></td></tr>
				<tr>
					<th>작성자</th>
					<th>사용후기</th>
					<th>작성일</th>
					<th>평점</th>
					<th>적립금지급</th>
					<th>첨부파일</th>
				</tr>
				<tr>
					<td height="1" colspan="<?=$colspan?>" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_line4.gif"></td>
				</tr>
				<?
	$sql = "SELECT * FROM tblproductreview ".$qry." ";
	$sql.= "ORDER BY num DESC ";
	$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
	$result=mysql_query($sql,get_db_conn());
	$j=0;
	while($row=mysql_fetch_object($result)){
		$number = ($t_count-($setup[list_num] * ($gotopage-1))-$j);

		$imgYN = ( strlen($row->img) > 0 ) ? "첨부":"-";

		$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
		$content=explode("=",$row->content);
		echo "<tr align=\"center\" style=\"color:#333333;padding-bottom:3pt;padding-top:3pt;line-height:18px;\">\n";
		echo "	<td>".$row->name."</td>\n";
		echo "	<td align=\"left\" style=\"padding:4px 10px;\">";
		if($reviewlist=="Y") {
			echo "<A HREF=\"javascript:view_review(".$j.")\">".titleCut(60,$content[0])."</A>";
		} else {
			echo "<A HREF=\"javascript:review_open('".$row->productcode."',".$row->num.")\">".titleCut(60,$content[0])."</A>";
		}
		if(strlen($content[1])>0) echo "<img src=\"".$Dir."images/common/review/review_replyicn.gif\" border=0 align=absmiddle>";
		echo "	</td>\n";
		echo "	<td>".$date."</td>\n";
		echo "	<td style=\"font-size:11px;letter-spacing:-0.5pt;line-height:15px;\">";
		for($i=0;$i<$row->marks;$i++) {
			echo "<FONT color=#000000><B>★</B></FONT>";
		}
		for($i=$row->marks;$i<5;$i++) {
			echo "<FONT color=#DEDEDE><B>★</B></FONT>";
		}
		echo "	</td>\n";
		echo "	<td><a href='/admin/product_review.php?popup=OK&search=".$_GET['memberID']."&s_check=1&reviewtype=ALL&vperiod=0' target='_BLANK' title='리뷰 적립금 지급'>[적립금지급]</a></td>\n";
		echo "	<td>".$imgYN."</td>\n";
		echo "</tr>\n";
		if($reviewlist=="Y") {
			echo "<tr id=reviewspan style=\"display:none; xcursor:hand\">\n";
			echo "	<td colspan=".$colspan.">\n";
			echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#f0f0f0 style=\"table-layout:fixed\">\n";
			echo "	<tr>\n";
			echo "		<td style=\"border:#f0f0f0 solid 1px\">\n";
			echo "		<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#F1F1F1 style=\"table-layout:fixed\">\n";
			echo "		<tr>\n";
			echo "			<td align=center style=\"padding:8\">\n";
			echo "			<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			echo "			<tr>\n";
			echo "				<td bgcolor=#FFFFFF style=\"border:#f0f0f0 solid 1px; padding:8\">\n";
			echo "				<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			echo "				<tr><td>".nl2br($content[0])."</td></tr>\n";

			if(!empty($row->img) && file_exists($Dir.DataDir."shopimages/productreview/".$row->img)){
			echo "				<tr><td><img src=\"".$Dir.DataDir."shopimages/productreview/".$row->img."\" border='0' style='margin-bottom:5px' /></td></tr>\n";
			}

			if(strlen($content[1])>0) {
				echo "	<tr><td style=\"padding:5 5 5 10px\"><img src=\"".$Dir."images/common/review/review_replyicn2.gif\" align=absmiddle border=0> ".nl2br($content[1])."</td></tr>\n";
			}
			echo "				<tr>\n";
			echo "					<td align=right><a href=\"javascript:view_review(".$j.")\"><img src=\"".$Dir."images/common/review/review_close.gif\" border=0></a></td>\n";
			echo "				</tr>\n";
			echo "				</table>\n";
			echo "				</td>\n";
			echo "			</tr>\n";
			echo "			</table>\n";
			echo "			</td>\n";
			echo "		</tr>\n";
			echo "		</table>\n";
			echo "		</td>\n";
			echo "	</tr>\n";
			echo "	</table>\n";
			echo "	</td>\n";
			echo "</tr>\n";
		}
		echo "<tr><td height=\"1\" colspan=\"".$colspan."\" background=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_line4.gif\"></td></tr>\n";
		$j++;
	}
	mysql_free_result($result);
	if($j==0) {
		echo "<tr><td colspan=\"".$colspan."\" height=\"25\" align=\"center\">등록된 사용후기가 없습니다.</td></tr>\n";
		echo "<tr><td height=\"1\" colspan=\"".$colspan."\" background=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_line4.gif\"></td></tr>\n";
	}
?>
			</table>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%">
				<?
	 if($j != 0) {
		$total_block = intval($pagecount / $setup[page_num]);

		if (($pagecount % $setup[page_num]) > 0) {
			$total_block = $total_block + 1;
		}

		$total_block = $total_block - 1;

		if (ceil($t_count/$setup[list_num]) > 0) {
			// 이전	x개 출력하는 부분-시작
			$a_first_block = "";
			if ($nowblock > 0) {
				$a_first_block .= "<a href='javascript:GoPage(\"review\",0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><FONT class=\"prlist\">[1...]</FONT></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='javascript:GoPage(\"review\",".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><FONT class=\"prlist\">[prev]</FONT></a>&nbsp;&nbsp;";

				$a_prev_page = $a_first_block.$a_prev_page;
			}

			// 일반 블럭에서의 페이지 표시부분-시작

			if (intval($total_block) <> intval($nowblock)) {
				$print_page = "";
				for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
					if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
						$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$setup[page_num]) + $gopage)."</font> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(\"review\",".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</FONT></a> ";
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
						$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$setup[page_num]) + $gopage)."</font> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(\"review\",".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</FONT></a> ";
					}
				}
			}		// 마지막 블럭에서의 표시부분-끝


			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
				$last_gotopage = ceil($t_count/$setup[list_num]);

				$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(\"review\",".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><FONT class=\"prlist\">[...".$last_gotopage."]</FONT></a>";

				$next_page_exists = true;
			}

			// 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(\"review\",".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><FONT class=\"prlist\">[next]</FONT></a>";

				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "<FONT class=\"prlist\">1</FONT>";
		}
	}
?>
				<tr>
					<td align="center" style="font-size:11px;">
						<?=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td align="center"><a href="javascript:window.close();"><img src="images/btn_close.gif" vspace="10" border="0" alt="" /></a></td></tr>
</table>
