<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_sticon.gif" border="0"></td>
			<td width="100%" background="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_stibg.gif" style="color:#ffffff;font-size:11px;"> 총 등록상품 : <b><?=$t_count?>건</b></td>
			<td><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_stimg.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="28" style="padding-left:10px;"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text10.gif" border="0"><a href="javascript:ChangeSort('new');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="new")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('new_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="new_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text11.gif" border="0"><a href="javascript:ChangeSort('best');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="best")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('best_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="best_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text01.gif" border="0"><a href="javascript:ChangeSort('production');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="production")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('production_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="production_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text02.gif" border="0"><a href="javascript:ChangeSort('name');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="name")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('name_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="name_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text03.gif" border="0"><a href="javascript:ChangeSort('price');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="price")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('price_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="price_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text04.gif" border="0"><a href="javascript:ChangeSort('reserve');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="reserve")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('reserve_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="reserve_desc")echo"_on";?>.gif" border="0"></a></td>
	</tr>
	<tr>
		<td valign="top">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<col width="15%"></col>
		<col width="0"></col>
		<col width="50%"></col>
		<col width="12%"></col>
		<col width="12%"></col>
		<col width="11%"></col>
		<tr>
			<td height="1" bgcolor="#EDEDED" colspan="6"></td>
		</tr>
		<tr height="30" align="center" bgcolor="#F8F8F8">
			<td colspan="2"><b><font color="#000000">제품사진</font></b></td>
			<td><b><font color="#000000">제품명</font></b></td>
			<td><b><font color="#000000">시중가격</font></b></td>
			<td><b><font color="#000000">판매가격</font></b></td>
			<td><b><font color="#000000">적립금</font></b></td>
		</tr>
		<tr>
			<td height="1" bgcolor="#EDEDED" colspan="6"></td>
		</tr>
		<tr>
			<td height="5" colspan="6"></td>
		</tr>
<?
		if($t_count<=0) {
			echo "<tr><td align=\"center\" height=\"30\" colspan=\"6\">등록된 상품이 없습니다.</td></tr>";
		} else {
			$tag_0_count = 5; //전체상품 태그 출력 갯수
			//번호, 사진, 상품명, 제조사, 가격
			$tmp_sort=explode("_",$sort);
			if($tmp_sort[0]=="reserve") {
				$addsortsql=",IF(a.reservetype='N',a.reserve*1,a.reserve*a.sellprice*0.01) AS reservesort ";
			}
			$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
			$sql.= "a.tinyimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode ";
			$sql.= $addsortsql;
			$sql.= "FROM tblproduct AS a ";
			$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
			$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
			$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
			if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
			else if($tmp_sort[0]=="new") $sql.= "ORDER BY a.regdate ".$tmp_sort[1]." ";
			else if($tmp_sort[0]=="best") $sql.= "ORDER BY a.sellcount ".$tmp_sort[1]." ";
			else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
			else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
			else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
			else $sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
			$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];

			$result=mysql_query($sql,get_db_conn());
			$i=0;
			while($row=mysql_fetch_object($result)) {
				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
				

				echo "<tr align=\"center\" id=\"A".$row->productcode."\" onmouseover=\"quickfun_show(this,'A".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'A".$row->productcode."','none')\">\n";
				echo "	<td style=\"padding-top:1px;padding-bottom:1px;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
					echo "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) echo "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) echo "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) echo "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) echo "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					echo "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				echo "	></A></td>";
				echo "	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','A','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				echo "	<td style=\"padding-left:5px;padding-right:5px;word-break:break-all;\" align=\"left\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$tag_0_count;$ii++) {
						$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								echo "<br><br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								echo "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
				}
				echo "	</td>\n";
				echo "	<TD style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
				echo "	<TD style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					echo $dicker;
				} else if(strlen($_data->proption_price)==0) {
					echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) echo "(기본가)";
				} else {
					echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) echo number_format($row->sellprice)."원";
					else echo ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") echo soldout();
				echo "	</td>\n";
				echo "	<TD style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format(getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y"))."원</td>\n";
				echo "</tr>\n";
				echo "<tr><td height=\"5\"></td></tr><tr>\n";
				echo "	<td height=\"1\" bgcolor=\"#EDEDED\" colspan=\"6\"></td>";
				echo "</tr><tr><td height=\"5\"></td></tr>\n";
				$i++;
			}
			mysql_free_result($result);

			$total_block = intval($pagecount / $setup[page_num]);

			if (($pagecount % $setup[page_num]) > 0) {
				$total_block = $total_block + 1;
			}

			$total_block = $total_block - 1;

			if (ceil($t_count/$setup[list_num]) > 0) {
				// 이전	x개 출력하는 부분-시작
				$a_first_block = "";
				if ($nowblock > 0) {
					$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><FONT class=\"prlist\">[1...]</FONT></a>&nbsp;&nbsp;";

					$prev_page_exists = true;
				}

				$a_prev_page = "";
				if ($nowblock > 0) {
					$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><FONT class=\"prlist\">[prev]</FONT></a>&nbsp;&nbsp;";

					$a_prev_page = $a_first_block.$a_prev_page;
				}

				// 일반 블럭에서의 페이지 표시부분-시작

				if (intval($total_block) <> intval($nowblock)) {
					$print_page = "";
					for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
						if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
							$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$setup[page_num]) + $gopage)."</font> ";
						} else {
							$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</FONT></a> ";
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
							$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$setup[page_num]) + $gopage)."</FONT> ";
						} else {
							$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</FONT></a> ";
						}
					}
				}		// 마지막 블럭에서의 표시부분-끝


				$a_last_block = "";
				if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
					$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
					$last_gotopage = ceil($t_count/$setup[list_num]);

					$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><FONT class=\"prlist\">[...".$last_gotopage."]</FONT></a>";

					$next_page_exists = true;
				}

				// 다음 10개 처리부분...

				$a_next_page = "";
				if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
					$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><FONT class=\"prlist\">[next]</FONT></a>";

					$a_next_page = $a_next_page.$a_last_block;
				}
			} else {
				$print_page = "<FONT class=\"prlist\">1</FONT>";
			}
		}
?>
		</tr>
		</table>
		</td>
	</tr>
<tr><td height="5"></td></tr>
	<tr>
		<td height="1" bgcolor="#EDEDED"></td>
	</tr>
<tr><td height="5"></td></tr>
	<tr>
		<td height="28" style="padding-left:10px;"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text01.gif" border="0"><a href="javascript:ChangeSort('production');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="production")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('production_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="production_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text02.gif" border="0"><a href="javascript:ChangeSort('name');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="name")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('name_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="name_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text03.gif" border="0"><a href="javascript:ChangeSort('price');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="price")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('price_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="price_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text04.gif" border="0"><a href="javascript:ChangeSort('reserve');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="reserve")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('reserve_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="reserve_desc")echo"_on";?>.gif" border="0"></a></td>
	</tr>
<tr><td height="5"></td></tr>
	<tr>
		<td height="1" bgcolor="#EDEDED"></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td style="font-size:11px;" align="center"><?=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page?></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	</table>
	</td>
</tr>
</table>