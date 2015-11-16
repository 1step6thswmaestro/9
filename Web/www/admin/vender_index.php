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
			<? include ("menu_vender.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; <span class="2depth_select">입점관리 메인</span></td>
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









			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="99%" style="table-layout:fixed">
						<tr>
							<td background="images/main_titlebg.gif"><img src="images/vender_maintitle.gif" border="0"></td>							
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="50%"></col>
				<col width="50%"></col>
<?
	$shop_main_title[] = "vender_mainstitle1.gif";
	$shop_main_title[] = "vender_mainstitle2.gif";
	$shop_main_title[] = "vender_mainstitle3.gif";

	$shop_main_stext[0][] = "vender_mains0text01.gif";
	$shop_main_stext[0][] = "vender_mains0text02.gif";
	$shop_main_stext[0][] = "vender_mains0text03.gif";
	$shop_main_stext[0][] = "vender_mains0text04.gif";
	$shop_main_stext[0][] = "vender_mains0text05.gif";
	$shop_main_stext[0][] = "vender_mains0text06.gif";

	$shop_main_stext[1][] = "vender_mains1text01.gif";
	$shop_main_stext[1][] = "vender_mains1text02.gif";
	$shop_main_stext[1][] = "vender_mains1text03.gif";
	
	$shop_main_stext[2][] = "vender_mains2text01.gif";
	$shop_main_stext[2][] = "vender_mains2text02.gif";
	$shop_main_stext[2][] = "vender_mains2text03.gif";

	$shop_main_slink[0][] = "vender_new.php";
	$shop_main_slink[0][] = "vender_management.php";
	$shop_main_slink[0][] = "vender_notice.php";
	$shop_main_slink[0][] = "vender_counsel.php";
	$shop_main_slink[0][] = "vender_mailsend.php";
	$shop_main_slink[0][] = "vender_smssend.php";

	$shop_main_slink[1][] = "vender_prdtlist.php";
	$shop_main_slink[1][] = "vender_prdtallupdate.php";
	$shop_main_slink[1][] = "vender_prdtallsoldout.php";
	
	$shop_main_slink[2][] = "vender_orderlist.php";
	$shop_main_slink[2][] = "vender_orderadjust.php";
	$shop_main_slink[2][] = "vender_calendar.php";

	$shop_main_sinfo[0][] = "쇼핑몰에 입점할 업체를 신규로 등록하실 수 있습니다.";
	$shop_main_sinfo[0][] = "입점 업체의 정보를 수정/삭제 하실 수 있습니다.";
	$shop_main_sinfo[0][] = "입점업체의 공지사항을 등록/수정/삭제 하실 수 있습니다.";
	$shop_main_sinfo[0][] = "쇼핑몰 본사와 입점업체간의 1:1 문의에 대한 답변 및 관리를 하실 수 있습니다.";
	$shop_main_sinfo[0][] = "업체메일 전체 또는 특정 업체에게 메일을 발송 할 수 있습니다.";
	$shop_main_sinfo[0][] = "입점업체 전체 또는 특정 업체에게 SMS 문자 전송을 할 수 있습니다.";
	
	$shop_main_sinfo[1][] = "입점업체의 상품정보 및 관리를 하실 수 있습니다.";
	$shop_main_sinfo[1][] = "해당 입점업체 상품의 가격/적립금/수량 등을 일괄 관리 할 수 있습니다.";
	$shop_main_sinfo[1][] = "해당 입점업체의 품절된 상품을 전체적으로 삭제/등록 등 관리를 할 수 있습니다.";
	
	$shop_main_sinfo[2][] = "해당 입점업체의 일자별 모든 주문현황 및 주문내역을 확인/처리하실 수 있습니다.";
	$shop_main_sinfo[2][] = "입점업체별 모든 주문건에 대한 정산 예정 주문내역을 확인할 수 있습니다.";
	$shop_main_sinfo[2][] = "입점업체별 정산 내역을 관리하실 수 있습니다.";

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
		echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"product_fontcolor\">".$shop_main_sinfo[$i][$j]."</td>\n";
		echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"product_fontcolor\">".$shop_main_sinfo[$i][$k]."</td>\n";
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
			<tr>
				<td height="30"></td>
			</tr>
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