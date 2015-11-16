<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

INCLUDE ("access.php");
?>

<? INCLUDE ("header.php"); ?>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
		<col width=198></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top"  background="images/leftmenu_bg.gif">
			<? include ("menu_gong.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 공구/경매 &gt; <span class="2depth_select">공구/경매 메인</span></td>
			</tr>
			</table>
		</td>
	</tr>   
	<tr>
        <td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_01_bg.gif"></td>
        <td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr>
        <td width="16" background="images/con_t_04_bg1.gif"></td>
        <td bgcolor="#ffffff" style="padding:10px">





			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="99%" style="table-layout:fixed">
						<tr>
							<td background="images/main_titlebg.gif"><img src="images/gong_maintitle.gif" border="0"></td>							
						</tr>
					</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="99%" style="table-layout:fixed">
				<col width="50%"></col>
				<col width="50%"></col>
<?
				$shop_main_title[] = "gong_mainstitle4.gif";
				$shop_main_title[] = "gong_mainstitle1.gif";
				$shop_main_title[] = "gong_mainstitle2.gif";
				$shop_main_title[] = "gong_mainstitle3.gif";


				$shop_main_stext[0][] = "gong_mains00text01.gif";
				$shop_main_stext[0][] = "gong_mains00text02.gif";
				$shop_main_stext[0][] = "gong_mains00text03.gif";
				$shop_main_stext[0][] = "gong_mains00text04.gif";
				$shop_main_stext[0][] = "gong_mains00text05.gif";
				
				$shop_main_stext[1][] = "gong_mains0text01.gif";
				
				$shop_main_stext[2][] = "gong_mains1text01.gif";
				$shop_main_stext[2][] = "gong_mains1text02.gif";
				
				$shop_main_stext[3][] = "gong_mains2text01.gif";
				$shop_main_stext[3][] = "gong_mains2text02.gif";
				$shop_main_stext[3][] = "gong_mains2text03.gif";
				$shop_main_stext[3][] = "gong_mains2text04.gif";


				$shop_main_slink[0][] = "social_shopping.php";
				$shop_main_slink[0][] = "social_sell_result.php";
				$shop_main_slink[0][] = "social_request.php";
				$shop_main_slink[0][] = "social_mailing.php";
				$shop_main_slink[0][] = "social_mailing_result.php";
				
				$shop_main_slink[1][] = "gong_displayset.php";
				
				$shop_main_slink[2][] = "gong_auctionreg.php";
				$shop_main_slink[2][] = "gong_auctionlist.php";
				
				$shop_main_slink[3][] = "gong_gongchangereg.php";
				$shop_main_slink[3][] = "gong_gongchangelist.php";
				$shop_main_slink[3][] = "gong_gongfixset.php";
				$shop_main_slink[3][] = "gong_gongfixreg.php";




				$shop_main_sinfo[0][] = "";
				$shop_main_sinfo[0][] = "";
				$shop_main_sinfo[0][] = "공동구매 신청 가능 상품에 공동구매 요청된 목록을 관리할 수 있습니다..";
				$shop_main_sinfo[0][] = "공동구매 소식을 구독신청한 목록을 관리할 수 있습니다.";
				$shop_main_sinfo[0][] = "공동구매 소식을 구독신청한 사람들에게 메일을 발송한 내역을 관리할 수 있습니다.";
				
				$shop_main_sinfo[1][] = "경매 및 공동구매 페이지의 상품 디스플레이 설정을 하실 수 있습니다.";
				
				$shop_main_sinfo[2][] = "경매 상품의 등록 및 수정을 하실 수 있습니다.";
				$shop_main_sinfo[2][] = "등록된 경매를 관리할 수 있습니다.";
				
				$shop_main_sinfo[3][] = "공동구매 상품을 등록/수정하실 수 있습니다.";
				$shop_main_sinfo[3][] = "등록된 공동구매를 관리할 수 있습니다.";
				$shop_main_sinfo[3][] = "가격이 고정된 공동구매 설정 방법에 대해서 안내해드립니다.";
				$shop_main_sinfo[3][] = "가격이 고정된 공동구매 등록 방법에 대해서 안내해드립니다.";




				for($i=0; $i<count($shop_main_title); $i++) {
					echo "<tr>\n";
					echo "	<td colspan=\"3\" background=\"images/mainstitle_bg.gif\"><img src=\"images/".$shop_main_title[$i]."\" border=\"0\"></td>\n";
					echo "</tr>\n";
					
					$shop_main_stext_round = @round(count($shop_main_stext[$i])/2);
					$k = $shop_main_stext_round;
					for($j=0; $j<$shop_main_stext_round; $j++) {
					echo "<tr>\n";
					echo "	<td style=\"padding-left:15px\"><a href=\"".$shop_main_slink[$i][$j]."\"><img src=\"images/".$shop_main_stext[$i][$j]."\" border=\"0\"><img src=\"images/cmn_main_go.gif\" border=\"0\"></a></td>\n";
						if($shop_main_stext[$i][$k]) {
						echo "	<td style=\"padding-left:15px\"><a href=\"".$shop_main_slink[$i][$k]."\"><img src=\"images/".$shop_main_stext[$i][$k]."\" border=\"0\"><img src=\"images/cmn_main_go.gif\" border=\"0\"></a></td>\n";
						} else {
						echo "	<td style=\"padding-left:15px\"></td>\n";
						}
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"gong_fontcolor\">".$shop_main_sinfo[$i][$j]."</td>\n";
					echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"gong_fontcolor\">".$shop_main_sinfo[$i][$k]."</td>\n";
					echo "</tr>\n";
						$k++;
					}

					echo "<tr>\n";
					echo "	<td height=\"20\" colspan=\"3\"></td>\n";
					echo "</tr>\n";
				}
?>
				</table>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			</table>


</td>
        <td width="16" background="images/con_t_02_bg.gif"></td>
    </tr>
    <tr>
        <td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_04_bg.gif"></td>
        <td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr><td height="20"></td></tr>
</table>


			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<? INCLUDE ("copyright.php"); ?>