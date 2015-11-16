<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="padding:5px;padding-top:0px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu1.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_orderlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu2.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_personal.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu3r.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>wishlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu4.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_reserve.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu5.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_coupon.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu6.gif" BORDER="0"></A></TD>
			<?if($_data->recom_url_ok == "Y" || $_data->sns_ok == "Y"){?><TD><A HREF="<?=$Dir.FrontDir?>mypage_promote.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu10.gif" BORDER="0"></A></TD><?}?>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_gonggu.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu11.gif" BORDER="0"></A></TD>
			<? if(getVenderUsed()==true) { ?><TD><A HREF="<?=$Dir.FrontDir?>mypage_custsect.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin2_menu9.gif" BORDER="0"></A></TD><? } ?>
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
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%" valign="bottom" style="padding-left:10px;font-size:11px;letter-spacing:-0.5pt;"><font color="#666666">* 고객게시판과 1:1맞춤상담을 통한 문의내역과 답변을 보실 수 있습니다.</font></td>
			<td style="padding-bottom:3px;"><A HREF="javascript:PersonalWrite()"><IMG SRC="<?=$Dir?>images/common/mypersonal/<?=$_data->design_mypersonal?>/mypersonal_skin2_btn1.gif" BORDER="0"></A></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<col width="6%"></col>
		<col></col>
		<col width="17%"></col>
		<col width="13%"></col>
		<col width="17%"></col>
		<tr>
			<td height="2" colspan="5" bgcolor="#666666"></td>
		</tr>
		<tr align="center" height="30" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
			<td><font color="#333333"><b>NO</b></font></td>
			<td><font color="#333333"><b>글제목</b></font></td>
			<td><font color="#333333"><b>문의날짜</b></font></td>
			<td><font color="#333333"><b>답변여부</b></font></td>
			<td><font color="#333333"><b>답변날짜</b></font></td>
		</tr>
		<tr>
			<td height="1" colspan="5" bgcolor="#DDDDDD"></td>
		</tr>
<?
		$sql = "SELECT COUNT(*) as t_count FROM tblpersonal ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		$t_count = $row->t_count;
		mysql_free_result($result);
		$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

		$sql = "SELECT idx,subject,date,re_date FROM tblpersonal ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "ORDER BY idx DESC LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
		$result = mysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=mysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);

			$date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."(".substr($row->date,8,2).":".substr($row->date,10,2).")";
			$re_date="-";
			if(strlen($row->re_date)==14) {
				$re_date = substr($row->re_date,0,4)."/".substr($row->re_date,4,2)."/".substr($row->re_date,6,2)."(".substr($row->re_date,8,2).":".substr($row->re_date,10,2).")";
			}

			if($cnt>0) {
				echo "<tr>\n";
				echo "	<td height=\"1\" colspan=\"5\" bgcolor=\"#DDDDDD\"></td>\n";
				echo "</tr>\n";
			}

			echo "<tr height=\"28\" align=\"center\">\n";
			echo "	<td><font color=\"#333333\">".$number."</font></td>\n";
			echo "	<td>&nbsp;<A HREF=\"javascript:ViewPersonal('".$row->idx."')\"><font color=\"#333333\">".strip_tags($row->subject)."</font></A></td>\n";
			echo "	<td><font color=\"#333333\">".$date."</font></td>\n";
			echo "	<td>";
			if(strlen($row->re_date)==14) {
				echo "<img src=\"".$Dir."images/common/mypersonal_skin_icon1.gif\" border=\"0\">";
			} else {
				echo "<img src=\"".$Dir."images/common/mypersonal_skin_icon2.gif\" border=\"0\">";
			}
			echo "	</td>\n";
			echo "	<td><font color=\"#333333\">".$re_date."</font></td>\n";
			echo "</tr>\n";
			$cnt++;
		}
		mysql_free_result($result);
		if ($cnt==0) {
			echo "<tr height=\"30\"><td colspan=\"5\" align=\"center\">문의내역이 없습니다.</td></tr>";
		}
?>
		</table>
		</td>
	</tr>
	<tr>
		<td height="3" background="<?=$Dir?>images/common/mypersonal_skin_line3.gif"></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
<?
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
		<td align="center"><?=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page?></td>
	</tr>
	</table>
	</td>
</tr>
</table>