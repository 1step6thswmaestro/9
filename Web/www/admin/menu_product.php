<?
switch(substr(strrchr(getenv("SCRIPT_NAME"),"/"),1)) {
	case "product_code.php":
		$menuidx = "shop1"; $idx[0][0] = 'YES'; break;
	case "product_register.php":
		$menuidx = "shop1"; $idx[0][1] = 'YES'; break;
	/*case "product_assemble.php":
		$menuidx = "shop1"; $idx[0][2] = 'YES'; break;*/
	case "product_mainlist.php":
		$menuidx = "shop1"; $idx[0][2] = 'YES'; break;
	case "product_codelist.php":
		$menuidx = "shop1"; $idx[0][3] = 'YES'; break;
	case "product_sort.php":
		$menuidx = "shop1"; $idx[0][4] = 'YES'; break;
	case "product_copy.php":
		$menuidx = "shop1"; $idx[0][5] = 'YES'; break;
	case "product_theme.php":
		$menuidx = "shop1"; $idx[0][6] = 'YES'; break;
	case "product_detaillist.php":
		$menuidx = "shop1"; $idx[0][7] = 'YES'; break;
	case "product_deliinfo.php":
		$menuidx = "shop1"; $idx[0][8] = 'YES'; break;
	case "product_brand.php":
		$menuidx = "shop1"; $idx[0][9] = 'YES'; break;
	case "product_business.php":
		$menuidx = "shop1"; $idx[0][10] = 'YES'; break;
	case "product2_register.php":
		$menuidx = "shop1"; $idx[0][11] = 'YES'; break;
	case "product_latestup.php":
		$menuidx = "shop1"; $idx[0][12] = 'YES'; break;
	case "product_latestsell.php":
		$menuidx = "shop1"; $idx[0][13] = 'YES'; break;
	case "product_listsearch.php":
		$menuidx = "shop1"; $idx[0][14] = 'YES'; break;

	case "product_imgmulticonfig.php":
		$menuidx = "shop2"; $idx[1][0] = 'YES'; break;
	case "product_imgmultiset.php":
		$menuidx = "shop2"; $idx[1][1] = 'YES'; break;

	case "product_collectionconfig.php":
		$menuidx = "shop3"; $idx[2][0] = 'YES'; break;
	case "product_collectionlist.php":
		$menuidx = "shop3"; $idx[2][1] = 'YES'; break;

	case "product_allupdate.php":
		$menuidx = "shop4"; $idx[3][0] = 'YES'; break;
	case "product_reserve.php":
		$menuidx = "shop4"; $idx[3][1] = 'YES'; break;
	case "product_price.php":
		$menuidx = "shop4"; $idx[3][2] = 'YES'; break;
	case "product_allsoldout.php":
		$menuidx = "shop4"; $idx[3][3] = 'YES'; break;
	case "product_allquantity.php":
		$menuidx = "shop4"; $idx[3][4] = 'YES'; break;
	case "product_excelupload.php":
		$menuidx = "shop4"; $idx[3][5] = 'YES'; break;
	case "product_exceldownload.php":
		$menuidx = "shop4"; $idx[3][6] = 'YES'; break;

	case "product_giftlist.php":
		$menuidx = "shop5"; $idx[4][0] = 'YES'; break;
	case "product_estimate.php":
		$menuidx = "shop5"; $idx[4][1] = 'YES'; break;
	case "product_review.php":
		$menuidx = "shop5"; $idx[4][2] = 'YES'; break;
	case "product_wishlist.php":
		$menuidx = "shop5"; $idx[4][3] = 'YES'; break;
	case "product_keywordsearch.php":
		$menuidx = "shop5"; $idx[4][4] = 'YES'; break;
	case "product_detailfilter.php":
		$menuidx = "shop5"; $idx[4][5] = 'YES'; break;

	case "product_option.php":
		$menuidx = "shop6"; $idx[5][0] = 'YES'; break;

	case "product_package.php":
		$menuidx = "shop7"; $idx[6][0] = 'YES'; break;
		
	case "scheduled_delivery.php":
		$menuidx = "shop8"; 
		switch($_REQUEST['mode']){
			case 'config':
				$idx[7][2] = 'YES'; break;		
			case 'modify':
			case 'new':
				$idx[7][1] = 'YES'; break;		
			default:
				$idx[7][0] = 'YES'; break;	
		}
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
layerlist = new Array ('shop1','shop2','shop3','shop4','shop5','shop6','shop8');
var thisshop="<?=$menuidx?>";
ino=7;

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
	<TD height="68" align="right" valign="top" background="images/product_leftmenu_title.gif" ><a href="javascript:scrollMove(0);"><img src="images/leftmenu_stop.gif" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="images/leftmenu_trans.gif" border="0" hspace="2" id="menu_scroll"></a></TD>
</TR>
<TR>
	<TD background="images/leftmenu_bg.gif">
	<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
	<col width="16"></col>
	<col></col>
	<col width="16"></col>
	<TR>
		<TD valign="top">
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop1">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop1');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">카테고리/상품관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop1" style="display:none">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34"  style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop1');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">카테고리/상품관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop1" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0">
<?
			if($menuidx && $menuidx != "shop1") {
				echo "<tr><td height=\"1\"></td></tr>";
			}
			noselectmenu('카테고리 관리','product_code.php',$idx[0][0],0);
			noselectmenu('상품 등록/수정/삭제','product_register.php',$idx[0][1],1);
			//noselectmenu('코디/조립 상품 관리','product_assemble.php',$idx[0][2],1);
			noselectmenu('메인상품 진열관리','product_mainlist.php',$idx[0][2],1);
			noselectmenu('카테고리 상품 진열관리','product_codelist.php',$idx[0][3],1);
			noselectmenu('상품 진열순서 설정','product_sort.php',$idx[0][4],1);
			noselectmenu('상품 이동/복사/삭제','product_copy.php',$idx[0][5],1);
			noselectmenu('가상 카테고리 상품관리','product_theme.php',$idx[0][6],1);
			noselectmenu('상품 스펙 노출관리','product_detaillist.php',$idx[0][7],1);
			noselectmenu('교환/배송/환불정보 노출',"product_deliinfo.php",$idx[0][8],1);
			noselectmenu('상품 브랜드 설정 관리',"product_brand.php",$idx[0][9],1);
			noselectmenu('상품 거래처 관리',"product_business.php",$idx[0][10],1);
			noselectmenu('상품권 등록/수정/관리',"product2_register.php",$idx[0][11],1);
			noselectmenu('최근등록상품',"product_latestup.php",$idx[0][12],1);
			noselectmenu('최근판매상품',"product_latestsell.php",$idx[0][13],1);
			noselectmenu('등록된 상품 조회',"product_listsearch.php",$idx[0][14],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		
		
		
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop8">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop8');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">정기배송상품관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop8" style="display:none">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34"  style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop8');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">정기배송상품관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop8" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0">
<?
			if($menuidx != "shop8") {
				echo "<tr><td height=\"1\"></td></tr>";
			}
			noselectmenu('정기배송 상품목록','scheduled_delivery.php',$idx[7][0],0); 
			noselectmenu('정기배송 상품등록','scheduled_delivery.php?mode=new',$idx[7][1],1);
			noselectmenu('정기배송 설정','scheduled_delivery.php?mode=config',$idx[7][2],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		
		
		
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop2">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop2');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">다중이미지 관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop2" style="display:none">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34"  style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop2');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">다중이미지 관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop2" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0">
<?
			if($menuidx != "shop2") {
				echo "<tr><td height=\"1\"></td></tr>";
			}
			noselectmenu('상품 다중이미지 설정','product_imgmulticonfig.php',$idx[1][0],0);
			noselectmenu('상품 다중이미지 등록/관리','product_imgmultiset.php',$idx[1][1],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop3">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop3');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">관련상품 관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop3" style="display:none">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34"  style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop3');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">관련상품 관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop3" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0">
<?
			if($menuidx != "shop3") {
				echo "<tr><td height=\"1\"></td></tr>";
			}
			noselectmenu('관련상품 진열방식 설정','product_collectionconfig.php',$idx[2][0],0);
			noselectmenu('관련상품 검색/등록','product_collectionlist.php',$idx[2][1],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop4');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">상품 일괄관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop4" style="display:none">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34"  style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop4');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">상품 일괄관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop4" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0">
<?
			if($menuidx != "shop4") {
				echo "<tr><td height=\"1\"></td></tr>";
			}
			noselectmenu('상품 일괄 간편수정','product_allupdate.php',$idx[3][0],0);
			noselectmenu('상품 적립금 일괄수정','product_reserve.php',$idx[3][1],1);
			noselectmenu('판매상품 가격 일괄수정','product_price.php',$idx[3][2],1);
			noselectmenu('품절상품 일괄 삭제/관리','product_allsoldout.php',$idx[3][3],1);
			noselectmenu('재고상품 일괄관리','product_allquantity.php',$idx[3][4],1);
			noselectmenu('상품정보 일괄 등록','product_excelupload.php',$idx[3][5],1);
			noselectmenu('상품 엑셀 다운로드','product_exceldownload.php',$idx[3][6],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop5">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop5');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">사은품/견적/기타관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop5" style="display:none">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34"  style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop5');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">사은품/견적/기타관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop5" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0">
<?
			if($menuidx != "shop5") {
				echo "<tr><td height=\"1\"></td></tr>";
			}
			noselectmenu('사은품 제도 관리','product_giftlist.php',$idx[4][0],0);
			noselectmenu('견적서 상품 등록/관리','product_estimate.php',$idx[4][1],1);
			noselectmenu('상품 리뷰 관리','product_review.php',$idx[4][2],1);
			noselectmenu('Wishlist 상품 관리','product_wishlist.php',$idx[4][3],1);
			noselectmenu('상품 키워드 검색','product_keywordsearch.php',$idx[4][4],1);
			noselectmenu('상품상세내역 단어 필터링','product_detailfilter.php',$idx[4][5],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop6">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop6');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">옵션그룹 등록 관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop6" style="display:none">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34"  style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop6');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">옵션그룹 등록 관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop6" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0">
<?
			if($menuidx != "shop6") {
				echo "<tr><td height=\"1\"></td></tr>";
			}
			noselectmenu('옵션그룹 등록 관리','product_option.php',$idx[5][0],3);
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
