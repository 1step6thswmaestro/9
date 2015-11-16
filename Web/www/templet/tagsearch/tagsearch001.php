<TABLE cellSpacing=0 cellPadding=0 width="95%" align="center">
<TR>
	<TD align="right"><A HREF="javascript:void(0)" onclick="tagCls.tagList()" onmouseover="window.status='최근인기태그';return true;" onmouseout="window.status='';return true;"><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_icon.gif" width="89" height="18" border="0" vspace="4"></a></TD>
</TR>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img02_head.gif" ALT=""></td>
		<td width="100%" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img03_bg.gif">
		<table cellpadding="0" cellspacing="0" width="40%" align="center">
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img02.gif" ALT=""></td>
			<td width="100%" valign="top" align="left">
			<table cellpadding="0" cellspacing="0" width="300">
			<tr>
				<td width="100%" height="19" align="center"><INPUT type=text name=searchtagname value="<?=$tagname?>" class=input style="WIDTH: 300px; BACKGROUND-COLOR: #f7f7f7" size="42" maxlength=50 onkeydown="CheckKeyTagSearch()" onkeyup="check_tagvalidate(event, this);"></td>
				<td></td>
				<td style="padding-left:5"><A HREF="javascript:void(0)" onclick="tagCls.searchProc()" onmouseover="window.status='태그검색';return true;" onmouseout="window.status='';return true;"><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_btn03.gif" width="74" height="23" border="0"></a></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
		<td align="right"><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img03_tail.gif"  ALT=""></td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD>&nbsp;</TD>
</TR>
<TR>
	<TD align="center">
	<?if($t_count>0){?>
	<FONT style="font-size:16px;color:#000000"><B>"<FONT COLOR="#FF5400"><?=$tagname?></FONT>"(으)로 태그가 등록되어 있는 상품입니다.</B></FONT>
	<?}else{?>
	<FONT style="font-size:16px;color:#000000"><B>"<FONT COLOR="#FF5400"><?=$tagname?></FONT>"(으)로 태그가 등록된 상품이 <FONT COLOR="#FF5400">없습니다.</FONT></B></FONT>
	<?}?>
	</TD>
</TR>
<tr><td height=20></td></tr>

<?if($t_count<=0){?>

<TR>
	<TD>
	<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t01_head.gif" WIDTH="14" HEIGHT=8 ALT=""></TD>
		<TD width="100%" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t01_bg.gif"></TD>
		<TD><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t01_tail.gif" width="14" height="8" border="0"></TD>
	</TR>
	<TR>
		<TD height="31" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t01bg_head.gif"></TD>
		<TD height="31" style="padding-top:6pt; padding-right:15pt; padding-bottom:3pt; padding-left:15pt;" width="100%">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="5%" align="center"><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_icon1.gif" width="55" height="55" border="0"></td>
			<td width="90%" style="line-height:120%;"><span style="font-size:8pt; letter-spacing:-0.5pt;">- 태그어가 <b>바르게</b> 입력되어 있는지 확인해 보세요.<br>- 보다 일반적인태그어 등 <b>다른 태그어</b>로 검색해 보세요.<br>- <b>띄어쓰기 없이</b> 검색해 보세요.<br>- 태그어에 특수기호가 포함되어 있다면 <b>특수기호를 빼고</b> 검색해 보세요.</span></td>
		</tr>
		</table>
		</TD>
		<TD height="31" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t01bg_tail.gif"></TD>
	</TR>
	<TR>
		<TD><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t02_head.gif" WIDTH="14" HEIGHT=16 ALT=""></TD>
		<TD width="100%" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t02_bg.gif">&nbsp;</TD>
		<TD><img src="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_t02_tail.gif" width="14" height="16" border="0"></TD>
	</TR>
	</TABLE>
	</TD>
</TR>

<?}else{?>

<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img01bg.gif" ALT=""></TD>
			<TD width="100%" background="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_img01bg1.gif" style="padding-top:3pt;"><font color="#FFFFFF"><span style="font-size:8pt;">*총 <b><?=$t_count?>건</b>의 상품이 존재합니다.</span></font></TD>
			<TD><IMG SRC="<?=$Dir?>images/common/tagsearch/<?=$_data->design_tagsearch?>/tag_btn02bg.gif" ALT=""></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td style="padding:5pt;">
		<table cellpadding="0" cellspacing="0" width="100%">
<?
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.quantity, a.reserve, a.reservetype, a.production, ";
		$sql.= "a.addcode,a.tinyimage,a.date,a.etctype,a.option1,a.option2,a.option_price,a.tag,a.tagcount,a.selfcode ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= $qry." ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.= "ORDER BY a.tagcount DESC ";
		$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
		$result=mysql_query($sql,get_db_conn());
		$i=0;
		while($row=mysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
			echo "<tr>\n";
			echo "	<td width=15% align=center>";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
				echo "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if ($width[1]>90) echo "height=90 ";
			} else {
				echo "<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
			}
			echo "	></A></td>\n";
			echo "	<td width=85%>\n";
			echo "	<table cellpadding=0 cellspacing=0 width=100%>\n";
			echo "	<tr>\n";
			echo "		<td height=47>\n";
			echo "		<table cellpadding=0 cellspacing=0>\n";
			echo "		<tr>\n";
			echo "			<td>";
			echo "			<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
			if(strlen($row->addcode)>0) echo "<font class=praddcode>[".$row->addcode."]</font><br>";
			echo "		<FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>";
			echo "			</a>\n";
			echo "			</td>\n";
			echo "		</tr>\n";
			echo "		</table>\n";
			echo "		</td>\n";
			echo "		<td height=47 width=140>\n";
			echo "		<table cellpadding=0 cellspacing=0 width=100%>\n";
			echo "		<tr>\n";
			echo "			<td class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> ".number_format($row->consumerprice)."원<br></td>\n";
			echo "		</tr>\n";
			echo "		<tr>\n";
			echo "			<td class=prprice>";
			if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
				echo $dicker;
			} else if(strlen($_data->proption_price)==0) {
				echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
				if (strlen($row->option_price)!=0) echo "(기본가)";
			} else {
				echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ";
				if (strlen($row->option_price)==0) echo number_format($row->sellprice)."원";
				else echo ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
			}
			if ($row->quantity=="0") echo soldout();
			echo "			<br></td>\n";
			echo "		</tr>\n";
			echo "		<tr>\n";
			echo "			<td class=prreserve><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format(getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y"))."원</td>\n";
			echo "		</tr>\n";
			echo "		</table>\n";
			echo "		</td>\n";
			echo "		<td height=47 width=180>\n";
			echo "		<table cellpadding=0 cellspacing=0 width=100%>\n";
			echo "		<tr>\n";
			echo "			<td width=39 align=center><A HREF=\"javascript:void(0)\" onclick=\"PrdtQuickCls.quickView('".$row->productcode."')\" onmouseover=\"window.status='미리보기';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_listquic.gif\" width=42 height=30 border=0></a></td>\n";
			
			$wish_script="PrdtQuickCls.quickFun('".$row->productcode."','1')";

			if($row->quantity=="0")
				$basket_script="alert('재고가 없습니다.');";
			else
				$basket_script="PrdtQuickCls.quickFun('".$row->productcode."','2')";
			
			echo "			<td width=39 align=center><A HREF=\"javascript:void(0)\" onclick=\"".$wish_script."\" onmouseover=\"window.status='위시리스트';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_listzim.gif\" width=43 height=30 border=0 hspace=2></a></td>\n";
			echo "			<td width=39 align=center><A HREF=\"javascript:void(0)\" onclick=\"".$basket_script."\" onmouseover=\"window.status='장바구니';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_listba.gif\" width=52 height=30 border=0></a></td>\n";
			echo "		</tr>\n";
			echo "		</table>\n";
			echo "		</td>\n";
			echo "	</tr>\n";
			echo "	<tr>\n";
			echo "		<td height=23 colspan=3 style=\"padding-top:4pt;\">\n";
			echo "		<table cellpadding=0 cellspacing=0 width=100%>\n";
			echo "		<tr>\n";
			echo "			<td align=left><img src=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_hitbg1.gif\" width=10 height=28 border=0></td>\n";
			echo "			<td width=100% background=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_hitbg2.gif\"><b><font color=\"#AF8C63\"><span style=\"font-size:8pt;\"><img src=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_ticon.gif\" width=31 height=15 border=0 align=absmiddle></span></font><font color=\"#F08A30\"><span style=\"font-size:8pt;\">(".$row->tagcount.")</span></font></b><span style=\"font-size:8pt;\"> ";
			$arrtag=explode(",",$row->tag);
			$jj=0;
			for($ii=0;$ii<count($arrtag);$ii++) {
				$temptag=ereg_replace("<|>","",$arrtag[$ii]);
				if(strlen($temptag)>0) {
					$jj++;
					if($jj>1) echo ", ";
					echo " <A HREF=\"javascript:void(0)\" onclick=\"tagCls.tagSearch('".$temptag."')\" onmouseover=\"window.status='".$temptag."';return true;\" onmouseout=\"window.status='';return true;\">".$temptag."</A>";
					if($jj>=5) break;
				}
			}
			echo "			</span></td>\n";
			echo "			<td align=right><img src=\"".$Dir."images/common/tagsearch/".$_data->design_tagsearch."/tag_hitbg3.gif\" width=8 height=28 border=0></td>\n";
			echo "		</tr>\n";
			echo "		</table>\n";
			echo "		</td>\n";
			echo "	</tr>\n";
			echo "	</table>\n";
			echo "	</td>\n";
			echo "</tr>\n";
			echo "<tr><td height=15></td></tr>\n";
			echo "<tr>\n";
			echo "	<td width=651 colspan=2 height=1 bgcolor=#F1F1F1></td>\n";
			echo "</tr>\n";
			echo "<tr><td height=15></td></tr>\n";
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
?>
		</table>
		</td>
	</tr>
	</table>
	</TD>
</TR>
<tr>
	<TD>
	<table cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td width=100% class="new_font_size" align="center"><?=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page?></td>
	</tr>
	</table>
	</TD>
</tr>
<?}?>
</TABLE>
