<?

switch(substr(strrchr(getenv("SCRIPT_NAME"),"/"),1)) {
	case "order_list.php":
		$menuidx = "shop1"; $idx[0][0] = 'YES'; break;
	case "order_delay.php":
		$menuidx = "shop1"; $idx[0][1] = 'YES'; break;
	case "order_delisearch.php":
		$menuidx = "shop1"; $idx[0][2] = 'YES'; break;
	case "order_namesearch.php":
		$menuidx = "shop1"; $idx[0][3] = 'YES'; break;
	case "order_monthsearch.php":
		$menuidx = "shop1"; $idx[0][4] = 'YES'; break;
	case "order_tempinfo.php":
		$menuidx = "shop1"; $idx[0][5] = 'YES'; break;
	case "order_excelinfo.php":
		$menuidx = "shop1"; $idx[0][6] = 'YES'; break;
	case "order_csvdelivery.php":
		$menuidx = "shop1"; $idx[0][7] = 'YES'; break;
	case "order_reservation.php":
		$menuidx = "shop7"; $idx[6][0] = 'YES'; break;

	case "order_basket.php":
		$menuidx = "shop2"; $idx[1][0] = 'YES'; break;
	case "order_allsale.php":
		$menuidx = "shop2"; $idx[1][1] = 'YES'; break;
	case "order_eachsale.php":
		$menuidx = "shop2"; $idx[1][2] = 'YES'; break;
	case "order_profit.php":
		$menuidx = "shop2"; $idx[1][3] = 'YES'; break;

	case "order_billuse.php":
		$menuidx = "shop3"; $idx[2][0] = 'YES'; break;
	case "order_billinfo.php":
		$menuidx = "shop3"; $idx[2][1] = 'YES'; break;
	case "order_billing.php":
		$menuidx = "shop1"; $idx[0][8] = 'YES'; break;

	case "order_taxsaveabout.php":
		$menuidx = "shop4"; $idx[3][0] = 'YES'; break;
	case "order_taxsaveconfig.php":
		$menuidx = "shop4"; $idx[3][1] = 'YES'; break;
	case "order_taxsavelist.php":
		$menuidx = "shop4"; $idx[3][2] = 'YES'; break;
	case "order_taxsaveissue.php":
		$menuidx = "shop4"; $idx[3][3] = 'YES'; break;

	case "order_bank.php":
		$menuidx = "shop5";
		if($_REQUEST['act'] == 'bankm')  $idx[4][0] = 'YES';
		else if($_REQUEST['act'] == 'bankadd')  $idx[4][1] = 'YES';
		break;
}


function noselectmenu($name,$url,$idx,$end){
	if($end==0 || $end==3){
		echo "<tr><td  height=\"8\"></td></tr>";
	}
	$str_style_class="depth2_default";
	if ($idx == "YES") {
		$str_style_class = "depth2_select";
	}
	echo "<tr>\n";
	echo "	<td height=\"19\"  style=\"padding-left:33px;\" class=\"".$str_style_class."\"><img src=\"images/icon_leftmenu1.gif\" border=\"0\"><a href=\"".$url."\">".$name."</a></td>\n";
	echo "</tr>\n";
	if($end==2 || $end==3){
echo "<tr><td height=\"25\" ></td></tr>";
	}
}
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
layerlist = new Array ('shop1','shop2','shop3','shop4','shop5','shop7');
var thisshop="<?=$menuidx?>";
ino=6;

function Change(){
	if(document.all){
		for(i=0;i<ino;i++) {
			document.all(layerlist[i]).style.display="none";
		}
		stobj="document.all(shop).style";
	} else if(document.getElementById){
		for(i=0;i<ino;i++) {
			document.getElementById(layerlist[i]).style.display="none";
		}
		stobj="document.getElementById(shop).style";
	} else if(document.layers){
		for(i=0;i<ino;i++) {
			document.layers[layerlist[i]].display=none;
		}
		stobj="document.layers[shop]";
	}
}

function ChangeMenu(shop){
	if ( thisshop !== shop){
		Change();
		eval(stobj).display="block";
		thisshop=shop;
	} else{
		Change();
		//eval(stobj).display="block";
		thisshop=stobj;
	}
}

function InitMenu(shop) {
	try {
		tblashop = "tbla".concat(shop);
		tblbshop = "tblb".concat(shop);
		document.all(shop).style.display="block";
		document.all(tblashop).style.display="none";
		document.all(tblbshop).style.display="block";
		num=shop.substring(4,5)-1;
	} catch (e) {
		shop = "shop1";
		tblashop = "tblashop1";
		tblbshop = "tblbshop1";
		document.all(shop).style.display="block";
		document.all(tblashop).style.display="none";
		document.all(tblbshop).style.display="block";
		num=shop.substring(4,5)-1;
	}
}
//-->
</SCRIPT>

<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
<TR>
	<TD height="68" align="right" valign="top" background="images/order_leftmenu_title.gif" style="padding-top:14px;padding-right:10px;"><a href="javascript:scrollMove(0);"><img src="images/leftmenu_stop.gif" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="images/leftmenu_trans.gif" border="0" hspace="2" id="menu_scroll"></a></TD>
</TR>
<TR>
	<TD  background="images/leftmenu_bg.gif">
	<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
	<col width="16"></col>
	<col></col>
	<col width="16"></col>
	<TR>
		<TD valign="top">
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop1">
<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop1');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">주문조회 및 배송관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop1" style="display:none">
<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34"  style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop1');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">주문조회 및 배송관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop1" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0" >
<?
			if($menuidx && $menuidx != "shop1") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
			noselectmenu('일자별 주문조회/배송','order_list.php',$idx[0][0],0);
			noselectmenu('미배송/미입금 주문관리','order_delay.php',$idx[0][1],1);
			noselectmenu('배송/입금일별 주문조회','order_delisearch.php',$idx[0][2],1);
			noselectmenu('이름/가격별 외 주문조회','order_namesearch.php',$idx[0][3],1);
			noselectmenu('개월별 상품명 주문조회','order_monthsearch.php',$idx[0][4],1);
			noselectmenu('결제시도 주문서 관리','order_tempinfo.php',$idx[0][5],1);
			noselectmenu('주문리스트 엑셀파일 관리','order_excelinfo.php',$idx[0][6],1);
			noselectmenu('주문리스트 일괄배송 관리','order_csvdelivery.php',$idx[0][7],2);
		//	noselectmenu('예약 판매 주문 관리','order_reservation.php',$idx[0][8],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		
		
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop7">
<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop7');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">예약 판매 주문</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop7" style="display:none">
<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34"  style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop7');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">예약 판매 주문</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop7" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0" >
<?
			if($menuidx != "shop7") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
			noselectmenu('배송 관리','order_reservation.php?mode=orders',$idx[6][0],2);			
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		
		

		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop2">
<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop2');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">장바구니 및 매출 분석</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop2" style="display:none">
<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34"  style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop2');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">장바구니 및 매출 분석</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop2" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0" >
<?
			if($menuidx != "shop2") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
			noselectmenu('장바구니 상품분석','order_basket.php',$idx[1][0],0);
			noselectmenu('전체상품 매출분석','order_allsale.php',$idx[1][1],1);
			noselectmenu('개별상품 매출분석','order_eachsale.php',$idx[1][2],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>





		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop3">
			<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
			<tr>
				<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop3');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">전자세금계산서 관리</td>
			</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop3" style="display:none">
			<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
			<tr>
				<td height="34"  style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop3');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">전자세금계산서 관리</td>
			</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<div id="shop3" style="display:none;">
						<table WIDTH="100%" cellpadding="0" cellspacing="0" >
<?
				if($menuidx != "shop3") {
					echo "<tr><td height=\"1\" ></td></tr>";
				}
				noselectmenu('서비스 신청방법','order_billuse.php',$idx[2][0],0);
				noselectmenu('전자세금계산서 설정','order_billinfo.php',$idx[2][1],1);
				noselectmenu('전자세금계산서 발행관리','order_billing.php',$idx[2][2],2);
?>
						</table>
					</div>
				</td>
			</tr>
		</table>





		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop4">
			<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
			<tr>
				<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop4');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">현금영수증 관리</td>
			</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop4" style="display:none">
			<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
			<tr>
				<td height="34"  style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop4');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">현금영수증 관리</td>
			</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<div id="shop4" style="display:none;">
						<table WIDTH="100%" cellpadding="0" cellspacing="0" >
<?
				if($menuidx != "shop4") {
					echo "<tr><td height=\"1\" ></td></tr>";
				}
				noselectmenu('현금영수증 제도란?','order_taxsaveabout.php',$idx[3][0],0);
				noselectmenu('현금영수증 환경설정','order_taxsaveconfig.php',$idx[3][1],1);
				noselectmenu('현금영수증 발급/조회','order_taxsavelist.php',$idx[3][2],1);
				noselectmenu('현금영수증 개별발급','order_taxsaveissue.php',$idx[3][3],2);
?>
						</table>
					</div>
				</td>
			</tr>
		</table>

		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop5">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop5');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">무통장 입금확인</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop5" style="display:none">
			<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
			<tr>
				<td height="34"  style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop5');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">무통장 입금확인</td>
			</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td>
				<div id="shop5" style="display:none;">

				<table WIDTH="100%" cellpadding="0" cellspacing="0" >
<?
			if($menuidx != "shop5") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
			noselectmenu('무통장 입금학인','order_bank.php?act=bankm',$idx[4][0],0);
			noselectmenu('입금계좌 등록','order_bank.php?act=bankadd',$idx[4][1],1);
?>
				</table>
				</div>
				</td>
			</tr>
		</table>
		</TD>
	</TR>
	</TABLE>
	</TD>
</TR>
</TABLE>

<script>
InitMenu('<?=$menuidx?>');
</script>
<script type="text/javascript" src="move_menu.js.php"></script>