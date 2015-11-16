<?
switch(substr(strrchr(getenv("SCRIPT_NAME"),"/"),1)) {	
	case "shop_moreinfo.php":
		$menuidx = "shop1"; $idx[0][0] = 'YES'; break;
	case "vender_new.php":
		$menuidx = "shop1"; $idx[0][1] = 'YES'; break;
	case "vender_management.php":
	case "vender_infomodify.php":
		$menuidx = "shop1"; $idx[0][2] = 'YES'; break;
	case "vender_notice.php":
		$menuidx = "shop1"; $idx[0][3] = 'YES'; break;
	case "vender_counsel.php":
		$menuidx = "shop1"; $idx[0][4] = 'YES'; break;
	case "vender_mailsend.php":
		$menuidx = "shop1"; $idx[0][5] = 'YES'; break;
	case "vender_smssend.php":
		$menuidx = "shop1"; $idx[0][6] = 'YES'; break;
	case "vender_cs.php":
	case "vender_cs_view.php":
		$menuidx = "shop1"; $idx[0][7] = 'YES'; break;
	case "vender_proposal.list.php":
		$menuidx = "shop1"; $idx[0][8] = 'YES'; break;


	case "vender_prdtlist.php":
		$menuidx = "shop2"; $idx[1][0] = 'YES'; break;
	case "vender_prdtallupdate.php":
		$menuidx = "shop2"; $idx[1][1] = 'YES'; break;
	case "vender_prdtallsoldout.php":
		$menuidx = "shop2"; $idx[1][2] = 'YES'; break;

	case "vender_orderlist.php":
		$menuidx = "shop3"; $idx[2][0] = 'YES'; break;
	case "vender_calendar.php":
		$menuidx = "shop3"; $idx[2][1] = 'YES'; break;
	case "vender_orderadjust.php":
		$menuidx = "shop3"; $idx[2][2] = 'YES'; break;
	case "vender_order_adjust_result.php":
		$menuidx = "shop3"; $idx[2][3] = 'YES'; break;
	
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
	echo "	<td  style=\"padding-left:33pt;\"  class=\"".$str_style_class."\" height=\"19\"><img src=\"images/icon_leftmenu1.gif\" width=\"8\" height=\"10\" border=\"0\"><a href=\"".$url."\">".$name."</a></td>\n";
	echo "</tr>\n";
	if($end==2 || $end==3){
		echo "<tr><td height=\"25\" ></td></tr>";
	}
}
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
layerlist = new Array ('shop1','shop2','shop3');
var thisshop="<?=$menuidx?>";
ino=3;

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

<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>
<TR>
	<TD height="68" align="right" valign="top" background="images/vender_leftmenu_title.gif"><a href="javascript:scrollMove(0);"><img src="images/leftmenu_stop.gif" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="images/leftmenu_trans.gif" border="0"" id="menu_scroll"></a></TD>
</TR>
<TR>
	<TD  background="images/leftmenu_bg.gif">
	<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<col width="16"></col>
	<col></col>
	<col width="16"></col>
	<TR>
		<TD  valign="top" >
		<table cellpadding="0" cellspacing="0"  id=tblashop1>
		<tr><td height="3" background="images/leftmenu_line.gif"></td>
		<tr>
			<td height="34" onClick="ChangeMenu('shop1');" style="padding-left:20px;cursor:hand;"class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">입점업체 관리</td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0"  id=tblbshop1 style="display:none">
		<tr><td height="3" background="images/leftmenu_line.gif"></td>
		<tr>
			<td  height="34"  class="depth1_select" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop1');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">입점업체 관리</td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td >
			<div id=shop1 style="display:none;">
			<table width="100%" cellpadding="0" cellspacing="0">
<?
			if($menuidx && $menuidx != "shop1") {
				echo "<tr><td width=\"158\"></td></tr>";
			}
			noselectmenu('입점운영기본관리','shop_moreinfo.php',$idx[0][0],0);
			noselectmenu('입점업체 신규등록','vender_new.php',$idx[0][1],1);
			noselectmenu('입점업체 정보관리','vender_management.php',$idx[0][2],1);
			noselectmenu('입점업체 공지사항','vender_notice.php',$idx[0][3],1);
			noselectmenu('입점업체 상담게시판','vender_counsel.php',$idx[0][4],1);
			noselectmenu('E-mail 발송','vender_mailsend.php',$idx[0][5],1);
			noselectmenu('SMS 문자전송','vender_smssend.php',$idx[0][6],1);
			noselectmenu('CS 관리','vender_cs.php',$idx[0][7],1);
			noselectmenu('제휴 및 입점문의','vender_proposal.list.php',$idx[0][8],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0"  id=tblashop2>
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td  height="34" onClick="ChangeMenu('shop2');" style="padding-left:20px;cursor:hand;" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">입점상품 관리</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0"  id=tblbshop2 style="display:none">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td  height="34" class="depth1_select" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop2');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">입점상품 관리</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td >
			<div id=shop2 style="display:none;">
			<table width="100%" cellpadding="0" cellspacing="0">
<?
			if($menuidx != "shop2") {
				echo "<tr><td width=\"158\"></td></tr>";
			}
			noselectmenu('입점업체 상품목록','vender_prdtlist.php',$idx[1][0],0);
			noselectmenu('상품 일괄 간편수정','vender_prdtallupdate.php',$idx[1][1],1);
			noselectmenu('품절상품 일괄 삭제/관리','vender_prdtallsoldout.php',$idx[1][2],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0"  id=tblashop3 width="100%">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td  height="34" onClick="ChangeMenu('shop3');" style="padding-left:20px;cursor:hand;" class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">주문/정산 관리</td>
		</tr>
		</table>
		<table cellpadding="0" cellspacing="0"  id=tblbshop3 style="display:none" width="100%">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td  height="34" class="depth1_select" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop3');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">주문/정산 관리</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td >
			<div id=shop3 style="margin-left:0;display:hide; display:none ;border-style:solid; border-width:0; border-color:black;padding:0;">
			<table width="100%" cellpadding="0" cellspacing="0">
<?
			if($menuidx != "shop3") {
				echo "<tr><td width=\"158\"></td></tr>";
			}
			noselectmenu('입점업체 주문조회','vender_orderlist.php',$idx[2][0],0);
			noselectmenu('입점업체 정산 캘린더','vender_calendar.php',$idx[2][1],1);
			noselectmenu('정산 세부내역 관리','vender_orderadjust.php',$idx[2][2],1);
			noselectmenu('정산완료 이력보기','vender_order_adjust_result.php',$idx[2][3],2);
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
