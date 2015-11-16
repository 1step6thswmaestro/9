<?
$_cdata->detail_type = 'AD001';

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 10;

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

$colspan=3;
$addsql="";
if(strlen($rselectreview)>0){
	switch($rselectreview){
		case "photo":
			$addsql .= "AND img IS NOT NULL AND img != '' ";
		break;
		case "best":
			$addsql .= "AND best = 'Y' ";
		break;
		case "basic":
			$addsql .= "AND img IS NULL OR img = '' ";
		break;
		case "all":
		default:
		break;
	}
}
if($reviewdate!="N") $colspan=4;
$qry = "WHERE productcode='".$productcode."' ";
if($_data->review_type=="A") $qry.= "AND display='Y' ";
$sql = "SELECT COUNT(*) as t_count, SUM(marks) as totmarks FROM tblproductreview ";
$sql.= $qry;
$sql.= $addsql;
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$t_count = (int)$row->t_count;
$totmarks = (int)$row->totmarks;
$marks=@ceil($totmarks/$t_count);
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
?>

<table cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:4px;">
	<tr>
		<td width="140"><span style="color: #000; font-size: 14px; font-weight: bold;">고객상품평</span> (<?=$counttotal?>)</td>
		<td>
			<div class="button <?=$sallreview?>"><a href="javascript:reviewSelect('all');">전체상품평(<?=$counttotal?>)</a></div>
			<div class="button <?=$sbestreview?>"><a href="javascript:reviewSelect('best');">베스트상품평(<?=$countbest?>)</a></div>
			<div class="button <?=$sphotoreview?>"><a href="javascript:reviewSelect('photo');">포토상품평(<?=$countphoto?>)</a></div>
			<div class="button <?=$sbasicreview?>"><a href="javascript:reviewSelect('basic');">일반상품평(<?=$countbasic?>)</a></div>
			<!--* 총 <font color="#F02800" style="font-size:11px; letter-spacing:-0.5pt;"><b><?=$counttotal?>개</b></font>의 사용후기가 등록되어 있습니다.-->
			<!--&nbsp;&nbsp;평균별점 : <?//=$reviewstarcount?>-->
		</td>
		<td align="right">
			<?
				if((strlen($_ShopInfo->getMemid())==0) && $_data->review_memtype=="Y") {
					echo "<A HREF=\"javascript:check_login()\"><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_write_btn02.gif\" border=\"0\" hspace=\"2\" /></A>";
				} else {
					echo "<A HREF=\"javascript:review_write()\"><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_write_btn02.gif\" border=\"0\" hspace=\"2\" /></A>";
				}
			?>
			<?if ($_data->ETCTYPE["REVIEW"]=="Y") {?>
				<A HREF="<?=$Dir.FrontDir?>reviewall.php"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_write_btn01.gif" border="0" hspace="2" /></a>
			<?}?>
		</td>
	</tr>
</table>

<div id="reviewwrite" style="display:none;">

	<div id="div_reviewwrite_container">
		<form name="reviewWriteForm" action="/front/reviewwrite_proc.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="mode" />
			<input type="hidden" name="code" value="<?=$code?>" />
			<input type="hidden" name="productcode" value="<?=$productcode?>" />
			<input type="hidden" name="sort" value="<?=$sort?>" />
			<?=($brandcode>0?"<input type=\"hidden\" name=\"brandcode\" value=\"".$brandcode."\" />\n":"")?>

			<table cellpadding="0" cellspacing="0" class="reviewMarkTbl">
				<tr>
					<th>* 품질</th>
					<td>
						<select name="quality">
							<option value="1">★</option>
							<option value="2">★★</option>
							<option value="3">★★★</option>
							<option value="4">★★★★</option>
							<option value="5" selected>★★★★★</option>
						</select>
					</td>
					<th>* 가격</th>
					<td>
						<select name="price">
							<option value="1">★</option>
							<option value="2">★★</option>
							<option value="3">★★★</option>
							<option value="4">★★★★</option>
							<option value="5" selected>★★★★★</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>* 배송</th>
					<td>
						<select name="delitime">
							<option value="1">★</option>
							<option value="2">★★</option>
							<option value="3">★★★</option>
							<option value="4">★★★★</option>
							<option value="5" selected>★★★★★</option>
						</select>
					</td>
					<th>* 추천</th>
					<td>
						<select name="recommend">
							<option value="1">★</option>
							<option value="2">★★</option>
							<option value="3">★★★</option>
							<option value="4">★★★★</option>
							<option value="5" selected>★★★★★</option>
						</select>
					</td>
				</tr>
			</table>

			<table cellpadding="0" cellspacing="0" class="reviewWriteTbl">
				<tr>
					<th>작성자</th>
					<td><input type="text" name="rname" maxlength="10" class="input" value=""/></td>
				</tr>
				<tr>
					<th>내용</th>
					<td><textarea name="rcontent" style="WIDTH:100%; HEIGHT:40px; padding:3pt; line-height:17px; border:solid 1px; border-color:#DFDFDF; font-size:9pt; color:333333;"></textarea></td>
				</tr>
				<tr>
					<th>첨부파일</th>
					<td><input type="file" name="attech" class="input" value=""/></td>
				</tr>
			</table>
			<div class="reviewInfoDiv">
				<b>·</b> 작성된 상품평은 수정/삭제가 불가하니 참고해 주시기 바랍니다.<br />
				<b>·</b> 취지와 상관없는 광고성 글은 관리자에 의해 임의로 삭제될 수 있습니다.<br />
				<b>·</b> 첨부파일은 이미지 파일(GIF, JPG, PNG)만 등록 가능합니다.
			</div>
		</form>
	</div>
	<div style="padding:10px 0px 20px 0px; text-align:center;">
		<A HREF="javascript:write_review();"><img src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/btn_reviewok.gif" border="0" alt="" /></a>
		<A HREF="javascript:review_write();"><img src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/btn_reviewcancel.gif" border="0" alt="" /></a>
	</div>
</div>

<div>
	<table cellpadding="0" cellspacing="0" width="100%">
		<col width="80"></col>
		<col width=></col>
		<?if($reviewdate!="N"){?>
		<col width="80"></col>
		<?}?>
		<col width="90"></col>
		<tr><td height="1" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_line2.gif" colspan="<?=$colspan?>"></td></tr>
		<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
			<td><b>작성자</b></td>
			<td><b>사용후기</b></td>
			<?if($reviewdate!="N"){?>
			<td><b>작성일</b></td>
			<?}?>
			<td><b>평점</b></td>
		</tr>
		<tr>
			<td height="1" colspan="<?=$colspan?>" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_line4.gif"></td>
		</tr>
<?
	$sql = "SELECT * FROM tblproductreview ".$qry." ";
	$sql.= $addsql;
	$sql.= "ORDER BY date DESC ";
	$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
	$result=mysql_query($sql,get_db_conn());
	$j=0;
	while($row=mysql_fetch_object($result)){
		$number = ($t_count-($setup[list_num] * ($gotopage-1))-$j);

		$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
		$content=explode("=",$row->content);
		echo "<tr align=\"center\" height=\"26\" style=\"color:#333333; line-height:18px;\">\n";
		echo "	<td>".$row->name."</td>\n";
		echo "	<td align=\"left\">";
		if($reviewlist=="Y") {
			echo "<A HREF=\"javascript:view_review(".$j.")\">".titleCut(60,$content[0])."</A>";
		} else {
			echo "<A HREF=\"javascript:review_open('".$row->productcode."',".$row->num.")\">".titleCut(60,$content[0])."</A>";
		}
		if(strlen($content[1])>0) echo "<img src=\"".$Dir."images/common/review/review_replyicn.gif\" border=0 align=absmiddle>";
		echo "	</td>\n";
		if($reviewdate!="N") {
			echo "	<td>".$date."</td>\n";
		}
		echo "	<td style=\"font-size:11px; letter-spacing:-0.5pt; line-height:15px;\">";
		for($i=0;$i<$row->marks;$i++) {
			//echo "<FONT color=#000000><B>★</B></FONT>";
			echo "<img src=\"/images/003/star_point1.gif\" alt=\"\" />";
		}
		for($i=$row->marks;$i<5;$i++) {
			//echo "<FONT color=#DEDEDE><B>★</B></FONT>";
			echo "<img src=\"/images/003/star_point2.gif\" alt=\"\" />";
		}
		echo "	</td>\n";
		echo "</tr>\n";

		if($reviewlist=="Y") {
			echo "<tr class=\"reviewspan\" style=\"display:none;\">\n";
			echo "	<td colspan=".$colspan.">\n";
			echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#f0f0f0 style=\"table-layout:fixed;\">\n";
			echo "	<tr>\n";
			echo "		<td style=\"border:#f0f0f0 solid 0px;\">\n";
			echo "		<table border=0 cellpadding=0 cellspacing=0 width=100% bgcolor=#F1F1F1 style=\"table-layout:fixed;\">\n";
			echo "		<tr>\n";
			echo "			<td align=center style=\"padding:0\">\n";
			echo "			<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			echo "			<tr>\n";
			echo "				<td bgcolor=\"#FFFFFF\" style=\"border:#f0f0f0 solid 1px; border-left:hidden; border-right:hidden; padding:12px; background-color:#F8F8F8;\">\n";
			echo "				<table border=0 cellpadding=0 cellspacing=0 width=100% background=#f5f5f5>\n";
			
			if(!empty($row->img) && file_exists($Dir.DataDir."shopimages/productreview/".$row->img)){
				echo "				<tr><td><img src=\"".$Dir.DataDir."shopimages/productreview/".$row->img."\" border=\"0\" width=\"260\" /></td>\n";
			}
			echo "					<td width=100% height=100% valign=\"top\" style=\"padding:20px 0px 0px 20px;\"><table border=0 cellpadding=0 cellspacing=0 width=100% height=100%><tr><td valign=top height=100%>".nl2br($content[0]);
			if(strlen($content[1])>0) {
				echo "				<br/><br/><img src=\"".$Dir."images/common/review/review_replyicn2.gif\" align=absmiddle border=0> ".nl2br($content[1]);
			}
			echo "					</td></tr><tr><td align=right><a href=\"javascript:view_review(".$j.")\"><img src=\"".$Dir."images/common/review/review_close.gif\" border=0></a></td></tr></table></td></tr>";
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
</div>