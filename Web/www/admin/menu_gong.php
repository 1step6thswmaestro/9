<?

switch(substr(strrchr(getenv("SCRIPT_NAME"),"/"),1)) {
	case "todaysale.php":
		$menuidx = "shop5"; 
		switch($_REQUEST['mode']){
			case 'topdesign':
				$idx[4][2] = 'YES'; break;		
			case 'modify':
			case 'new':
				$idx[4][1] = 'YES'; break;		
			default:
				$idx[4][0] = 'YES'; break;	
		}
		break;
	case "gong_displayset.php":
		$menuidx = "shop1"; $idx[0][0] = 'YES'; break;

	case "gong_auctionreg.php":
		$menuidx = "shop2"; $idx[1][0] = 'YES'; break;
	case "gong_auctionlist.php":
	
		$menuidx = "shop2"; $idx[1][1] = 'YES'; break;

	case "gong_gongchangereg.php":
		$menuidx = "shop3"; $idx[2][0] = 'YES'; break;
	case "gong_gongchangelist.php":
		$menuidx = "shop3"; $idx[2][1] = 'YES'; break;
	case "gong_gongfixset.php":
		$menuidx = "shop3"; $idx[2][2] = 'YES'; break;
	case "gong_gongfixreg.php":
		$menuidx = "shop3"; $idx[2][3] = 'YES'; break;

	case "social_shopping.php":
		$menuidx = "shop4"; $idx[3][0] = 'YES'; break;
	case "social_sell_result.php":
	case "social_order_list.php":
	case "social_encore_list.php":
		$menuidx = "shop4"; $idx[3][1] = 'YES'; break;
	case "social_request.php":
	case "social_request_list.php":
		$menuidx = "shop4"; $idx[3][2] = 'YES'; break;
	case "social_mailing.php":
		$menuidx = "shop4"; $idx[3][3] = 'YES'; break;
	case "social_mailing_result.php":
		$menuidx = "shop4"; $idx[3][4] = 'YES'; break;
	case "social_socialintro.php":
		$menuidx = "shop4"; $idx[3][5] = 'YES'; break;
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
layerlist = new Array ('shop1','shop2','shop3','shop4','shop5');
var thisshop="<?=$menuidx?>";
ino=5;

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
		shop = "shop5";
		tblashop = "tblashop5";
		tblbshop = "tblbshop5";
		document.all(shop).style.display="block";
		document.all(tblashop).style.display="none";
		document.all(tblbshop).style.display="block";
		num=shop.substring(4,5)-1;
	}
}
//-->
</SCRIPT>

<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0" >
<TR>
	<TD height="68" align="right" valign="top" background="images/gong_leftmenu_title.gif" ><a href="javascript:scrollMove(0);"><img src="images/leftmenu_stop.gif" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="images/leftmenu_trans.gif" border="0" hspace="2" id="menu_scroll"></a></TD>
</TR>
<TR>
	<TD  background="images/leftmenu_bg.gif">
	<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
	<col width="16"></col>
	<col></col>
	<col width="16"></col>
	<TR>
		<TD valign="top">		
		

<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop5">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop5');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">투데이세일</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop5" style="display:none">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop5');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">투데이세일</td>
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
			noselectmenu('투데이세일 상품목록','todaysale.php',$idx[4][0],0); 
			noselectmenu('투데이세일 상품등록','todaysale.php?mode=new',$idx[4][1],1);
			noselectmenu('투데이세일 상단디자인','todaysale.php?mode=topdesign',$idx[4][2],2);
			
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop4">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop4');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">소셜쇼핑</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop4" style="display:none">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop4');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">소셜쇼핑</td>
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
			noselectmenu('소셜쇼핑상품관리','social_shopping.php',$idx[3][0],0);
			noselectmenu('판매종료 소셜상품','social_sell_result.php',$idx[3][1],1);
			noselectmenu('공동구매신청관리','social_request.php',$idx[3][2],1);
			noselectmenu('공동구매구독관리','social_mailing.php',$idx[3][3],1);
			noselectmenu('구독메일전송목록','social_mailing_result.php',$idx[3][4],1);
			noselectmenu('소셜쇼핑 상단디자인','social_socialintro.php',$idx[3][5],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop1">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop1');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">경매/공동구매 화면설정</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop1" style="display:none">
<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop1');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">경매/공동구매 화면설정</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop1" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0">
<?
			if($menuidx != "shop1") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
			noselectmenu('경매/공동구매 화면설정','gong_displayset.php',$idx[0][0],3);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop2">
<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop2');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">쇼핑몰 경매 관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop2" style="display:none">
<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop2');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">쇼핑몰 경매 관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop2" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0">
<?
			if($menuidx != "shop2") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
			noselectmenu('경매상품 등록/수정','gong_auctionreg.php',$idx[1][0],0);
			noselectmenu('등록 경매 관리','gong_auctionlist.php',$idx[1][1],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop3">
<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop3');" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">공동구매 관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop3" style="display:none">
<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop3');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">공동구매 관리</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop3" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0">
<?
			if($menuidx != "shop3") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
			noselectmenu('가격변동형 공구 등록/수정','gong_gongchangereg.php',$idx[2][0],0);
			noselectmenu('가격변동형 등록공구 관리','gong_gongchangelist.php',$idx[2][1],1);
			noselectmenu('가격고정형 공동구매 설정','gong_gongfixset.php',$idx[2][2],1);
			noselectmenu('가격고정형 공구구매 등록','gong_gongfixreg.php',$idx[2][3],2);
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