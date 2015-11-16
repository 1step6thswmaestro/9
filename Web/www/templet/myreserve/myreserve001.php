<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%">
	<!--
	<tr>
		<td>
		<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu1.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_orderlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu2.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_personal.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu3.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>wishlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu4.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_reserve.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu5r.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_coupon.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu6.gif" BORDER="0"></A></TD>
			<?if($_data->recom_url_ok == "Y" || $_data->sns_ok == "Y"){?><TD><A HREF="<?=$Dir.FrontDir?>mypage_promote.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu10.gif" BORDER="0"></A></TD><?}?>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_gonggu.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu11.gif" BORDER="0"></A></TD>
			<? if(getVenderUsed()==true) { ?><TD><A HREF="<?=$Dir.FrontDir?>mypage_custsect.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu9.gif" BORDER="0"></A></TD><? } ?>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_usermodify.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu7.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_memberout.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu8.gif" BORDER="0"></A></TD>
			<TD width="100%" background="<?=$Dir?>images/common/mypersonal_skin3_menubg.gif"></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	-->
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td style="padding:6px;" bgcolor="#EAEAEA">
		<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff">
		<tr>
			<td style="padding:25px;">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td><img src="<?=$Dir?>images/common/myreserve/<?=$_data->design_myreserve?>/mypersonal_skin3_t_text1.gif" border="0"></td>
				<td align="center" valign="bottom" style="padding-left:10px;padding-right:10px;font-size:30px;line-height:28px;letter-spacing:-0.5pt;"><font color="#FF6600"><b><?=number_format($reserve)?>원</b></font></td>
				<td><img src="<?=$Dir?>images/common/myreserve/<?=$_data->design_myreserve?>/mypersonal_skin3_t_text2.gif" border="0"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="padding:10px;padding-right:0px;font-size:11px;letter-spacing:-0.5pt;line-height:15px;">
		* 고객님께서 사용가능한 적립금은 <b><?=number_format($reserve)?>원</b> 입니다.<br>
		* 적립된 금액이 <b><?=number_format($maxreserve)?>원 이상 누적</b>되었을때, 사용하실 수 있습니다.결제시 적립금 사용여부를 확인하는 안내문이 나옵니다. <br>
		* 적립금 내역은 <b>최근 6개월분이 제공</b>되므로 이점 양지 바랍니다.<br>
		* 주문완료 후 부여된 적립 내역은 해당 내역을 클릭하시면 상세내역을 확인하실 수 있습니다.(단, 삭제하신 주문내역은 조회가 불가능합니다. )
		<? if($_data->cr_ok=='Y') { ?>
		<br>* 적립된 적립금은 <b><?=number_format($_data->cr_unit)?>원</b> 단위로 현금으로 전환 하실 수 있습니다.
		   <a href='<?=$Dir.FrontDir?>mypage_cash01.php'><IMG SRC="../images/design/promote3_btn01.gif" border="0" align="absmiddle"></a>
		<? } ?>	
		</td>
	</tr>
	<tr><td height=25></td></tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" border="0">
		<col width="100"></col>
		<col></col>
		<col width="80"></col>
		<col width="80"></col>
		<tr>
			<td height="2" colspan="4" bgcolor="#dddddd"></td>
		</tr>
		<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
			<td><font color="#333333"><b>발생일자</b></font></td>
			<td><font color="#333333"><b>발생내역</b></font></td>
			<td><font color="#333333"><b>결제금액</b></font></td>
			<td><font color="#333333"><b>적립금</b></font></td>
		</tr>
		<tr>
			<td height="1" colspan="4" bgcolor="#DDDDDD"></td>
		</tr>
<?
		$sql = "SELECT COUNT(*) as t_count FROM tblreserve ";
		$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND date >= '".$s_curdate."' AND date <= '".$e_curdate."' ";
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		$t_count = $row->t_count;
		mysql_free_result($result);
		$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

		$sql = "SELECT * FROM tblreserve WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND date >= '".$s_curdate."' AND date <= '".$e_curdate."' ";
		$sql.= "ORDER BY date DESC LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
		$result=mysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=mysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
			$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);

			if($cnt>0) {
				echo "<tr>\n";
				echo "	<td height=\"1\" colspan=\"4\" bgcolor=\"#DDDDDD\"></td>\n";
				echo "</tr>\n";
			}

			$ordercode="";
			$orderprice="";
			$orderdata=$row->orderdata;
			if(strlen($orderdata)>0) {
				$tmpstr=explode("=",$orderdata);
				$ordercode=$tmpstr[0];
				$orderprice=$tmpstr[1];
			}

			echo "<tr height=\"28\" align=\"center\">\n";
			echo "	<td><font color=\"#333333\">".$date."</font></td>\n";
			echo "	<td align=\"left\"><nobr><a";
			if(strlen($ordercode)>0) echo " style=\"cursor:hand;\" onclick=\"OrderDetailPop('".$ordercode."')\">";
			echo "<font color=\"#333333\">".$row->content."</font></a></td>\n";
			echo "	<td>";
			if(strlen($orderprice)>0 && $orderprice>0) {
				echo "<font color=\"#F02800\"><b>".number_format($orderprice)."원</b></font>";
			} else {
				echo "-";
			}
			echo "</td>\n";
			echo "	<td><font color=\"#333333\">".number_format($row->reserve)."원</font></td>\n";
			echo "</tr>\n";
			$cnt++;
		}
		mysql_free_result($result);
		if ($cnt==0) {
			echo "<tr height=\"28\"><td colspan=\"4\" align=\"center\">해당내역이 없습니다.</td></tr>";
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
	<tr>
		<td align="center"><?=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page?></td>
	</tr>
	</table>
	</td>
</tr>
</table>