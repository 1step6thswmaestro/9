<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td>
		<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu1.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_orderlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu2.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_personal.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu3.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>wishlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu4.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_reserve.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu5.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_coupon.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu6.gif" BORDER="0"></A></TD>
			<?if($_data->recom_url_ok == "Y" || $_data->sns_ok == "Y"){?><TD><A HREF="<?=$Dir.FrontDir?>mypage_promote.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu10.gif" BORDER="0"></A></TD><?}?>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_gonggu.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu11.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_custsect.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu9r.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_usermodify.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu7.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_memberout.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu8.gif" BORDER="0"></A></TD>
			<TD width="100%" background="<?=$Dir?>images/common/mypersonal_skin2_menubg.gif"></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td bgcolor=#F8F8FE style="padding:15,15,15,15">
		- 고객님께서 자주 방문하시는 단골 미니샵입니다.
		</td>
	</tr>
	<tr><td height=1 bgcolor=#f0f0f0></td></tr>
	<tr><td height=30></td></tr>
	<tr>
		<td>
		<A HREF="javascript:CheckAll()"><img src="<?=$Dir?>images/common/mycustsect/<?=$_data->design_mycustsect?>/btnSelectAll.gif" border=0></A>
		<A HREF="javascript:goDeleteMinishop()"><img src="<?=$Dir?>images/common/mycustsect/<?=$_data->design_mycustsect?>/btnDelMinishop.gif" border=0></A>
		<A HREF="javascript:addAgreeMailAll()"><img src="<?=$Dir?>images/common/mycustsect/<?=$_data->design_mycustsect?>/btnMailMinishop.gif" border=0></A>
		<A HREF="javascript:delAgreeMailAll()"><img src="<?=$Dir?>images/common/mycustsect/<?=$_data->design_mycustsect?>/btnMailNoMinishop.gif" border=0></A>
		</td>
	</tr>
	<tr><td height=5></td></tr>
	<tr>
		<td>
		<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
		<col width=30></col>
		<col width=200></col>
		<col width=180></col>
		<col width=></col>
		<tr><td colspan=4 height=1 bgcolor=#111E68></td></tr>
		<tr height=30 bgcolor=#E8EEFD>
			<td align=center style="color:#000000">&nbsp;</td>
			<td align=center style="color:#000000">미니샵 로고</td>
			<td align=center style="color:#000000">미니샵명</td>
			<td align=center style="color:#000000">미니샵 HOT 추천상품</td>
		</tr>
<?
		$qry = "WHERE a.id='".$_ShopInfo->getMemid()."' AND a.vender=b.vender ";

		$sql = "SELECT COUNT(*) as t_count FROM tblregiststore a, tblvenderstore b ".$qry;
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		$t_count = $row->t_count;
		mysql_free_result($result);
		$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

		$sql = "SELECT a.vender, a.email_yn, b.id, b.brand_name, b.hot_used, b.hot_linktype ";
		$sql.= "FROM tblregiststore a, tblvenderstore b ".$qry." ";
		$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
		$result = mysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=mysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);

			if($cnt>0) {
				echo "<tr><td colspan=4 height=1>\n";
				echo "<table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
				echo "</td></tr>\n";
			}

			echo "<tr>\n";
			echo "	<td align=center valign=top style=\"padding:7,0\"><input type=checkbox name=sels value=\"".$row->vender."\" style=\"BORDER:none;\"></td>\n";
			echo "	<td align=center style=\"padding:7,10\">\n";
			if(file_exists($Dir.DataDir."shopimages/vender/logo_".$row->vender.".gif")) {
				$logo="".$Dir.DataDir."shopimages/vender/logo_".$row->vender.".gif";
			} else {
				$logo="".$Dir."images/minishop/logo.gif";
			}
			echo "	<table border=0 cellpadding=0 cellspacing=0>\n";
			echo "	<tr>\n";
			echo "		<td align=center style=\"border:1px #dddddd solid\"><a href=\"javascript:GoMinishop('".(MinishopType=="ON"?$Dir."minishop/".$row->id:$Dir."minishop.php?storeid=".$row->id)."')\"> <img src=\"".$logo."\" width=185 height=80 border=0></a></td>\n";
			echo "	</tr>\n";
			echo "	<tr><td height=10></td></tr>\n";
			echo "	<tr>\n";
			echo "		<td>\n";
			echo "		메일수신 : ";
			if($row->email_yn=="Y") {
				echo "<B>수신</B>";
				echo " <A HREF=\"javascript:miniMailAgree('del',".$row->vender.")\"><img src=".$Dir."images/common/mycustsect/".$_data->design_mycustsect."/btsMailNo.gif border=0 align=absmiddle></A>";
			} else {
				echo "<B>거부</B>";
				echo " <A HREF=\"javascript:miniMailAgree('add',".$row->vender.")\"><img src=".$Dir."images/common/mycustsect/".$_data->design_mycustsect."/btsMailYes.gif border=0 align=absmiddle></A>";
			}
			echo "		</td>\n";
			echo "	</tr>\n";
			echo "	</table>\n";
			echo "	</td>\n";
			echo "	<td align=center valign=top style=\"padding:25,10\"><B>".$row->brand_name."</B></td>\n";
			if($row->hot_used=="1") {
				echo "	<td valign=top>\n";
				echo "	<table border=0 cellpadding=0 cellspacing=0 style=\"table-layout:fixed\">\n";
				echo "	<tr>\n";

				unset($hot_prcode);
				unset($isnot_hotspecial);
				$sql = "SELECT a.productcode,a.productname,a.sellprice,a.consumerprice,a.reserve,a.production, ";
				$sql.= "a.option_price, a.tag, a.minimage, a.tinyimage, a.etctype, a.option_price FROM tblproduct AS a ";
				$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
				$sql.= "WHERE 1=1 ";
				if($row->hot_linktype=="2") {
					$sql2 = "SELECT special_list FROM tblvenderspecialmain WHERE vender='".$row->vender."' AND special='3' ";
					$result2=mysql_query($sql2,get_db_conn());
					if($row2=mysql_fetch_object($result2)) {
						$hot_prcode=ereg_replace(',','\',\'',$row2->special_list);
					}
					mysql_free_result($result2);
					if(strlen($hot_prcode)>0) {
						$sql.= "AND a.productcode IN ('".$hot_prcode."') ";
					} else {
						$isnot_hotspecial=true;
					}
				}
				$sql.= "AND a.vender='".$row->vender."' AND a.display='Y' ";
				$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
				if($row->hot_linktype=="1" || $isnot_hotspecial==true) {
					$sql.= "ORDER BY a.sellcount DESC ";
				} else if($_minidata->hot_linktype=="2") {
					$sql.= "ORDER BY FIELD(a.productcode,'".$hot_prcode."') ";
				}
				$sql.= "LIMIT 3 ";
				$result2=mysql_query($sql,get_db_conn());
				while($row2=mysql_fetch_object($result2)) {
					echo "<td width=80 align=center valign=top style=\"padding:7,0\">\n";
					echo "<table border=0 cellpadding=0 cellspacing=0 style=\"table-layout:fixed\">\n";
					echo "<tr>\n";
					echo "	<td width=62 height=62 style=\"border:1px #dddddd solid\">\n";
					echo "	<A HREF=\"javascript:GoPrdtItem('".$row2->productcode."')\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
					if(strlen($row2->tinyimage)!=0 && file_exists($Dir.DataDir."shopimages/product/".$row2->tinyimage)){
						$file_size=getImageSize($Dir.DataDir."shopimages/product/".$row2->tinyimage);
						echo "<img src=\"".$Dir.DataDir."shopimages/product/".$row2->tinyimage."\"";
						if($file_size[0]>=$file_size[1]) echo " width=60";
						else echo " height=60";
						echo " border=0></a>";
					} else {
						echo "<img src=\"".$Dir."images/no_img.gif\" width=60 border=0></a>";
					}
					echo "	</td>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<td align=center style=\"font-size:8pt;padding-top:5\">\n";
					echo "	<A HREF=\"javascript:GoPrdtItem('".$row2->productcode."')\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">".str_replace("...","..",titleCut(20,strip_tags($row2->productname)))."</A>";
					echo "	</td>\n";
					echo "</tr>\n";
					echo "<tr><td align=center style=\"font-size:8pt;color:red;padding-top:5\"><B>".number_format($row2->sellprice)."</B></td></tr>\n";
					echo "</table>\n";
					echo "</td>\n";
				}
				mysql_free_result($result2);

				echo "	</tr>\n";
				echo "	</table>\n";
				echo "	</td>\n";
			} else {
				echo "	<td align=center valign=top style=\"padding:15,10\">HOT 추천상품이 없습니다.</td>\n";
			}
			echo "</tr>\n";
			$cnt++;
		}
		mysql_free_result($result);
		if ($cnt==0) {
			echo "<tr height=40><td colspan=4 align=center>등록된 단골매장이 없습니다.</td></tr>";
		}

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
?>
		<tr><td colspan=4 height=1 bgcolor=#111E68></td></tr>
		<tr><td colspan=4 height=10></td></tr>
		<tr>
			<td colspan=4 align=center>
			<?=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page?>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>