<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
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
					<td height="30">
					<?
						$_date="";
						$_best_desc="";
						$_price="";
						$_price_desc="";

						switch(trim($sort)){
							case "best_desc":
								$_best_desc="class=\"sortOn\"";
							break;

							case "price":
								$_price="class=\"sortOn\"";
							break;

							case "price_desc":
								$_price_desc="class=\"sortOn\"";
							break;

							case "reserve_desc":
								$_reserve_desc="class=\"sortOn\"";
							break;

							case "new_desc":
							default:
								$_date="class=\"sortOn\"";
							break;
						}
					?>
						<ul class="prSortType">
							<li><a href="javascript:ChangeSort('new_desc');" <?=$_date?>>신규등록순</a></li>
							<li><a href="javascript:ChangeSort('best_desc');" <?=$_best_desc?>>인기상품순</a></li>
							<li><a href="javascript:ChangeSort('price');" <?=$_price?>>낮은가격순</a></li>
							<li><a href="javascript:ChangeSort('price_desc');" <?=$_price_desc?>>높은가격순</a></li>
							<li class="last"><a href="javascript:ChangeSort('reserve_desc');" <?=$_reserve_desc?>>적립금순</a></li>
						</ul>
						<div style="float:right; text-align:right;">
						<?
							if($listnum == 8) $sel8 = "selected";
							if($listnum == 16) $sel16 = "selected";
							if($listnum == 32) $sel32 = "selected";

							$listselect = "
								<select name=\"listnum2\" onchange=\"ChangeNum(this)\">
									<option value='8' ".$sel8.">8</option>
									<option value='16' ".$sel16.">16</option>
									<option value='32' ".$sel32.">32</option>
								</select>
							";
							echo $listselect."개씩 보기";
						?>
						</div>
					</td>

					<!--
					<td height="28" style="padding-left:10px;"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text10.gif" border="0"><a href="javascript:ChangeSort('new');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="new")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('new_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="new_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text11.gif" border="0"><a href="javascript:ChangeSort('best');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="best")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('best_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="best_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text01.gif" border="0"><a href="javascript:ChangeSort('production');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="production")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('production_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="production_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text02.gif" border="0"><a href="javascript:ChangeSort('name');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="name")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('name_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="name_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text03.gif" border="0"><a href="javascript:ChangeSort('price');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="price")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('price_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="price_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text04.gif" border="0"><a href="javascript:ChangeSort('reserve');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="reserve")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('reserve_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="reserve_desc")echo"_on";?>.gif" border="0"></a></td>
					-->
				</tr>
				<tr><td height="1" bgcolor="#EDEDED"></td></tr>
				<tr><td height="15"></td></tr>
				<tr>
					<td>
						<table cellpadding="2" cellspacing="0" width="100%">
							<tr>
<?
			$print_page="";
			$a_first_block="";
			$a_prev_page="";
			$a_next_page="";

			if($t_count<=0) {
				echo "<td align=\"center\" height=\"30\">등록된 상품이 없습니다.</td>";
			} else {
				$tag_0_count = 2; //전체상품 태그 출력 갯수

				//번호, 사진, 상품명, 제조사, 가격
				/*
				$tmp_sort=explode("_",$sort);
				if($tmp_sort[0]=="reserve") {
					$addsortsql=",IF(a.reservetype='N',a.reserve*1,a.reserve*a.sellprice*0.01) AS reservesort ";
				}
				$sql = "SELECT a.productcode, a.productname, a.quantity, a.reserve, a.reservetype, a.production, ".( (isSeller()=="Y") ? "if(a.productdisprice>0,a.productdisprice,a.sellprice) as sellprice, if(a.productdisprice>0,1,0)":"a.sellprice, 0" )." as isdiscountprice, ";
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
				$sql.= "LIMIT " . ($setup["list_num"] * ($gotopage - 1)) . ", " . $setup["list_num"];

				$result=mysql_query($sql,get_db_conn());
				$i=0;
				while($row=mysql_fetch_object($result)) {
				*/

				$res = _getPrsectionProductList('',$sptype,$gotopage,$setup["list_num"],$sort);

				foreach($res as $i=>$row){

					// 예약상품 아이콘 추가
					$row->etctype = reservationEtcType($row->reservation,$row->etctype);

					// 도매 가격 적용 상품 아이콘
					$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";

					// 할인율 표시
					$discountRate = ( $row->discountRate > 0 ) ? "<strong>".$row->discountRate."%</strong>↓" : "";

					$memberpriceValue = $row->sellprice;
					$strikeStart = $strikeEnd = '';
					$memberprice = 0;
					if($row->discountprices>0 AND isSeller() != 'Y' ){
						$memberprice = number_format($row->sellprice - $row->discountprices);
						$strikeStart = "<strike>";
						$strikeEnd = "</strike>";
						$memberpriceValue = ($row->sellprice - $row->discountprices);
					}

					$number = ($t_count-($setup["list_num"] * ($gotopage-1))-$i);
					$tableSize = $_data->primg_minisize;


					if($i>0 && $i%4==0)
						echo "</tr><tr><td colspan=\"9\" height=\"10\"></td></tr>\n";
					if ($i!=0 && $i%4!=0) {
						echo "<td width=\"1\" nowrap></td>";
					}

					echo "<td align=\"center\" valign=\"top\">\n";
					echo "<TABLE cellSpacing=\"0\" cellPadding=\"0\" width=\"".$tableSize."\" border=\"0\" id=\"A".$row->productcode."\" onmouseover=\"quickfun_show(this,'A".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'A".$row->productcode."','none')\" class=\"prInfoBox\">\n";
					echo "<TR>\n";
					echo "	<TD align=\"center\" height=\"120\" style=\"padding:5px;\">";
					echo "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
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
					echo "</tr>\n";

					echo "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','A','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";

					echo "<tr>";
					echo "	<td style=\"padding:5px 7px; word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>".(strlen($row->prmsg)?'<br /><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</td>\n";
					echo "</tr>\n";

					//시중가 + 판매가 + 할인율 + 회원할인가
					echo "<tr>";
					echo "	<td style=\"padding:0px 7px 7px 7px; word-break:break-all;\">
									<table border=0 cellpadding=0 cellspacing=0 width=100%>
										<tr>
											<td>
					";
					if($row->consumerprice!=0) {
						//echo "<tr>\n";
						//echo "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
						echo "	<span class=\"prconsumerprice\" style=\"padding-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</span>\n";
						//echo "</tr>\n";
					}

					// 회원 할인가가 있을 때 가격 class 변경
					if($memberprice > 0){
						$mainprpriceClass = "";
					}else{
						$mainprpriceClass = "mainprprice";
					}

					//echo "<tr>\n";
					//echo "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prprice\">";

					echo $strikeStart;

					echo "<span style=\"white-space:nowrap;\">";
					if($dicker=dickerview($row->etctype,$wholeSaleIcon."<strong>".number_format($row->sellprice)."원</strong>",1)) {
						echo $dicker;
					} else if(strlen($_data->proption_price)==0) {
						echo "<strong class=\"".$mainprpriceClass."\">".$wholeSaleIcon.number_format($row->sellprice)."원</strong>";
						//if (strlen($row->option_price)!=0) echo "(기본가)";
					} else {
						//echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">";
						if (strlen($row->option_price)==0) echo $wholeSaleIcon."<strong>".number_format($row->sellprice)."원</strong>";
						else echo ereg_replace("\[PRICE\]",$wholeSaleIcon."<strong>".number_format($row->sellprice)."원</strong>",$_data->proption_price);
					}

					echo $strikeEnd;
					echo "
									</span>
								</td>
					";
					if($row->discountRate > 0){
						echo "<td align=\"right\" valign=\"bottom\" class=\"discount\">".$discountRate."</td>";
					}
					echo "
							</tr>
						</table>
					";

					if ($row->quantity=="0") echo soldout();
					//echo "	</td>\n";
					//echo "</tr>\n";


					//회원할인가 적용
					if( $memberprice > 0 ) {
						//echo "<tr>\n";
						//echo "	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainprprice\"><img src=\"".$Dir."images/common/memsale_icon.gif\" style=\"position:relative; top:0.1em;\" alt=\"\" />".dickerview($row->etctype,$memberprice."원")."</td>\n";
						echo "	<div><span class=\"mainprprice\">".dickerview($row->etctype,$memberprice."원")."</span> <img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" /></div>\n";
						//echo "</tr>\n";
					}

					$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y");
					if($reserveconv>0) {
						//echo "<tr>\n";
						//echo "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."원</td>\n";
						echo "	<div style=\"margin-top:5px;\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\" align=\"absmiddle\" alt=\"\" /><span class=\"prreserve\">".number_format($reserveconv)."</span>원</div>\n";
						//echo "</tr>\n";
					}
					echo "	</td>\n";
					echo "</tr>\n";

					if($_data->ETCTYPE["TAGTYPE"]=="Y") {
						$taglist=explode(",",$row->tag);
						$jj=0;
						for($ii=0;$ii<$tag_0_count;$ii++) {
							$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
							if(strlen($taglist[$ii])>0) {
								if($jj==0) {
									echo "<tr>\n";
									echo "	<td align=\"center\" style=\"word-break:break-all;\">\n";
									echo "	<img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
								}
								else {
									echo "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
								}
								$jj++;
							}
						}
						if($jj!=0) {
							echo "	</td>\n";
							echo "</tr>\n";
						}
					}

					// 입점사 네임택
					if( nameTechUse($row->vender) ) {
						$classList = array();
						$classResult=mysql_query("SELECT * FROM `tblVenderClassType` ",get_db_conn());
						while($classRow=mysql_fetch_object($classResult)) {
							$classList[$classRow->idx] = $classRow->name;
						}
						$v_info = mysql_fetch_assoc ( mysql_query( "SELECT * FROM `tblvenderinfo` WHERE `vender`=".$row->vender." LIMIT 1;" ,get_db_conn()) );

						$venderNameTag = "<div style=\"float:left; width:60px;\"><img src=\"".$com_image_url.$v_info['com_image']."\" onerror=\"this.src='/images/no_img.gif';\" width=\"48\" style=\"border:1px solid #dddddd;\" /></div>";
						$venderNameTag .= "<div style=\"float:left; width:65%; font-size:11px;\">";
						$venderNameTag .= "	<span class=\"name\">".$v_info['com_name']."</span> <span class=\"owner\">(".$v_info['com_owner'].")</span><br />";
						$venderNameTag .= "	<a href=\"javascript:GoMinishop('/minishop.php?storeid=".$v_info['id']."')\"><img src=\"/images/common/icon_vender_go.gif\" border=\"0\" alt=\"전체상품보기\" /></a>";
						$venderNameTag .= "</div>";

						// 네임텍 출력
						echo "
							<tr>
								<td class=\"nameTagBox\">".$venderNameTag."</td>
							</tr>
						";
					}

					echo "</table>\n";
					echo "</td>";

					$i++;
				}
				if($i>0 && $i<4) {
					for($k=0; $k<(4-$i); $k++) {
						echo "<td width=\"10\" nowrap></td>\n<td width=\"20%\"></td>\n";
					}
				}
				mysql_free_result($result);

				$total_block = intval($pagecount / $setup["page_num"]);

				if (($pagecount % $setup["page_num"]) > 0) {
					$total_block = $total_block + 1;
				}

				$total_block = $total_block - 1;

				if (ceil($t_count/$setup["list_num"]) > 0) {
					// 이전	x개 출력하는 부분-시작
					$a_first_block = "";
					if ($nowblock > 0) {
						$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><FONT class=\"prlist\">[1...]</FONT></a>&nbsp;&nbsp;";

						$prev_page_exists = true;
					}

					if ($nowblock > 0) {
						$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup["page_num"]*($block-1)+$setup["page_num"]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup["page_num"]." 페이지';return true\"><FONT class=\"prlist\">[prev]</FONT></a>&nbsp;&nbsp;";

						$a_prev_page = $a_first_block.$a_prev_page;
					}

					// 일반 블럭에서의 페이지 표시부분-시작

					if (intval($total_block) <> intval($nowblock)) {
						$print_page = "";
						for ($gopage = 1; $gopage <= $setup["page_num"]; $gopage++) {
							if ((intval($nowblock*$setup["page_num"]) + $gopage) == intval($gotopage)) {
								$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$setup["page_num"]) + $gopage)."</font> ";
							} else {
								$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup["page_num"]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup["page_num"]) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$setup["page_num"]) + $gopage)."]</FONT></a> ";
							}
						}
					} else {
						if (($pagecount % $setup["page_num"]) == 0) {
							$lastpage = $setup["page_num"];
						} else {
							$lastpage = $pagecount % $setup["page_num"];
						}

						for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
							if (intval($nowblock*$setup["page_num"]) + $gopage == intval($gotopage)) {
								$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$setup["page_num"]) + $gopage)."</FONT> ";
							} else {
								$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup["page_num"]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup["page_num"]) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$setup["page_num"]) + $gopage)."]</FONT></a> ";
							}
						}
					}		// 마지막 블럭에서의 표시부분-끝


					$a_last_block = "";
					if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
						$last_block = ceil($t_count/($setup["list_num"]*$setup["page_num"])) - 1;
						$last_gotopage = ceil($t_count/$setup["list_num"]);

						$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><FONT class=\"prlist\">[...".$last_gotopage."]</FONT></a>";

						$next_page_exists = true;
					}

					// 다음 10개 처리부분...

					if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
						$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup["page_num"]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup["page_num"]." 페이지';return true\"><FONT class=\"prlist\">[next]</FONT></a>";

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
				<tr><td height="15"></td></tr>
				<!--
				<tr><td height="1" bgcolor="#EDEDED"></td></tr>
				<tr>
					<td height="28" style="padding-left:10px;"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text01.gif" border="0"><a href="javascript:ChangeSort('production');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="production")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('production_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="production_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text02.gif" border="0"><a href="javascript:ChangeSort('name');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="name")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('name_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="name_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text03.gif" border="0"><a href="javascript:ChangeSort('price');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="price")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('price_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="price_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_text04.gif" border="0"><a href="javascript:ChangeSort('reserve');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerotop<?if($sort=="reserve")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('reserve_desc');"><IMG SRC="<?=$Dir?>images/common/prsection/<?=$prsection_type?>/plist_skin_nerodow<?if($sort=="reserve_desc")echo"_on";?>.gif" border="0"></a></td>
				</tr>
				-->
				<tr><td height="1" bgcolor="#EDEDED"></td></tr>
				<tr><td height="20"></td></tr>
				<tr>
					<td style="font-size:11px;" align="center"><?=$a_prev_page.$print_page.$a_next_page?></td>
				</tr>
				<tr><td height="20"></td></tr>
			</table>
		</td>
	</tr>
</table>