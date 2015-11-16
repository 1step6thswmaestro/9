<?
switch(substr(strrchr(getenv("SCRIPT_NAME"),"/"),1)) {
	case "mobile_config.php":
		$menuidx = "mobile"; $idx[0][0] = 'YES'; break;
	case "mobile_board_display_set.php":
		$menuidx = "mobile"; $idx[0][1] = 'YES'; break;
	case "mobile_product_code.php":
		$menuidx = "mobile"; $idx[0][2] = 'YES'; break;
	case "mobile_product_code_old.php":
		$menuidx = "mobile"; $idx[0][3] = 'YES'; break;
	case "mobile_product_list.php":
		$menuidx = "mobile"; $idx[0][4] = 'YES'; break;
	case "mobile_category_list_set.php":
		$menuidx = "mobile"; $idx[0][5] = 'YES'; break;
	case "mobile_main_direct_menu.php":
		$menuidx = "mobile"; $idx[0][6] = 'YES'; break;
	case "mobile_main_planning.php":
		$menuidx = "mobile"; $idx[0][7] = 'YES'; break;
	case "mobile_main_design.php":
		$menuidx = "mobile"; $idx[0][8] = 'YES'; break;
	case "mobile_payment.php":
		$menuidx = "mobile"; $idx[0][9] = 'YES'; break;
	//case "mobile_notice.php":
		//$menuidx = "mobile"; $idx[0][8] = 'YES'; break;
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
layerlist = new Array ('shop1');
var thisshop="<?=$menuidx?>";
ino=1;

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
<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0" >
<TR>
	<TD height="68" align="right" valign="top" background="images/mobile_leftmenu_title.gif" style="padding-top:14px;padding-right:10px;"><a href="javascript:scrollMove(0);"><img src="images/leftmenu_stop.gif" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="images/leftmenu_trans.gif" border="0" hspace="2" id="menu_scroll"></a></TD>
</TR>
<TR>
	<TD  background="images/leftmenu_bg.gif">
	<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
	<col width="16"></col>
	<col></col>
	<col width="16"></col>
	<TR>
		<TD valign="top">
		<table width="100%" cellpadding="0" cellspacing="0" id="tblashop1">
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop1');"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle">모바일샵</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" id="tblbshop1" style="display:none">
		<tr>
			<td height="34"  style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop1');">모바일샵</td>
		</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop1" style="display:none;">
			<table width="100%" cellpadding="0" cellspacing="0">
<?
			if($menuidx && $menuidx != "shop1") {
				echo "<tr><td height=\"1\" ></td></tr>";
			}
		
			noselectmenu('기본설정','mobile_config.php',$idx[0][0],0);
			noselectmenu('게시판 설정','mobile_board_display_set.php',$idx[0][1],1);
			noselectmenu('카테고리노출 설정','mobile_product_code.php',$idx[0][2],1);
			noselectmenu('카테고리노출 설정(오픈형)','mobile_product_code_old.php',$idx[0][3],1);
			noselectmenu('상품진열 관리','mobile_product_list.php',$idx[0][4],1);
			noselectmenu('카테고리별 상품진열 관리','mobile_category_list_set.php',$idx[0][5],1);
			noselectmenu('바로가기메뉴 설정','mobile_main_direct_menu.php',$idx[0][6],1);
			noselectmenu('메인 상품 진열설정','mobile_main_planning.php',$idx[0][7],1);		
			noselectmenu('메인구성 출력위치설정','mobile_main_design.php',$idx[0][8],1);
			noselectmenu('전자결제연동','mobile_payment.php',$idx[0][9],2);

?>

			<tr><td height="10"></td></tr>
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
<tr><td height="100"></td></tr>
</TABLE>
</div>
</div>
<script>
InitMenu('<?=$menuidx?>');
</script>
<script type="text/javascript" src="move_menu.js.php"></script>