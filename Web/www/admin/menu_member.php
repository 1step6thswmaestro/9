<?
switch(substr(strrchr(getenv("SCRIPT_NAME"),"/"),1)) {
	case "member_list.php":
		$menuidx = "shop1"; $idx[0][0] = 'YES'; break;
	case "member_wholesale.php":
		$menuidx = "shop1"; $idx[0][1] = 'YES'; break;
	case "member_outlist.php":
		$menuidx = "shop1"; $idx[0][2] = 'YES'; break;
	case "member_activity.php":
		$menuidx = "shop1"; $idx[0][3] = 'YES'; break;
	case "member_excelupload.php":
		$menuidx = "shop1"; $idx[0][4] = 'YES'; break;

	case "member_groupnew.php":
		$menuidx = "shop2"; $idx[1][0] = 'YES'; break;
	case "member_groupmemreg.php":
		$menuidx = "shop2"; $idx[1][1] = 'YES'; break;
	case "member_groupmemberview.php":
		$menuidx = "shop2"; $idx[1][2] = 'YES'; break;

	case "member_mailsend.php":
		$menuidx = "shop3"; $idx[2][0] = 'YES'; break;
	//case "member_mailallsend.php":
	case "bulkmail.php":
		$menuidx = "shop3"; $idx[2][1] = 'YES'; break;
	//case "member_mailallsendinfo.php":
	//	$menuidx = "shop3"; $idx[2][2] = 'YES'; break;
	case "member_smssend.php":
		$menuidx = "shop3"; $idx[2][2] = 'YES'; break;
	case "member_smsallsend.php":
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
	echo "	<td height=\"19\"  style=\"padding-left:33px;\" class=\"".$str_style_class."\"><img src=\"images/icon_leftmenu1.gif\" border=\"0\"><a href=\"".$url."\">".$name."</a></td>\n";
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

<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
<TR>
	<TD height="68" align="right" valign="top" background="images/member_leftmenu_title.gif" style="padding-top:14px;padding-right:10px;"><a href="javascript:scrollMove(0);"><img src="images/leftmenu_stop.gif" border="0" id="menu_pix"></a><a href="javascript:scrollMove(1);"><img src="images/leftmenu_trans.gif" border="0" hspace="2" id="menu_scroll"></a></TD>
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
			<td height="34" onClick="ChangeMenu('shop1');" class="depth1_noselect"style="padding-left:20px;cursor:hand;" ><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">ȸ������ ����</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop1" style="display:none">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop1');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">ȸ������ ����</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
			<div id="shop1" style="display:none;">
			<table WIDTH="100%" cellpadding="0" cellspacing="0" >
<?
			if($menuidx && $menuidx != "shop1") {
				echo "<tr><td height=\"1\"></td></tr>";
			}
			noselectmenu('ȸ������ ����','member_list.php',$idx[0][0],0);
			if( isWholesale() == "Y" ) noselectmenu('����ȸ����û����','member_wholesale.php',$idx[0][1],1);
			noselectmenu('ȸ�� Ż���û ����','member_outlist.php',$idx[0][2],1);
			noselectmenu('ȸ�� Ȱ�� ����',"member_activity.php",$idx[0][3],1);
			noselectmenu('ȸ������ �ϰ� ���','member_excelupload.php',$idx[0][4],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop2">
<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34"  onClick="ChangeMenu('shop2');" style="padding-left:20px;cursor:hand;"class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">ȸ����� ����</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop2" style="display:none">
<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop2');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">ȸ����� ����</td>
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
			noselectmenu('ȸ����� ���/����/����','member_groupnew.php',$idx[1][0],0);
			noselectmenu('ȸ����� ���� ����','member_groupmemreg.php',$idx[1][1],1);
			noselectmenu('��޺� ȸ�� ����','member_groupmemberview.php',$idx[1][2],2);
?>
			</table>
			</div>
			</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblashop3">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" onClick="ChangeMenu('shop3');"class="depth1_noselect"><img src="images/icon_leftmenu.gif" border="0" align="absmiddle"  style="margin-right:4px;margin-bottom:2px">ȸ������ �ΰ����</td>
		</tr>
		</table>
		<table WIDTH="100%" cellpadding="0" cellspacing="0" id="tblbshop3" style="display:none">
		<tr><td height="3" background="images/leftmenu_line.gif"></td></tr>
		<tr>
			<td height="34" style="padding-left:20px;cursor:hand;" class="depth1_select" onClick="ChangeMenu('shop3');"><img src="images/icon_leftmenu_select.gif"  align="absmiddle" style="margin-right:4px;margin-bottom:2px">ȸ������ �ΰ����</td>
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
			noselectmenu('�������� �߼�','member_mailsend.php',$idx[2][0],0);
			noselectmenu('��ü���� �߼�','bulkmail.php',$idx[2][1],1);
			//noselectmenu('��ü���� �߼�','member_mailallsend.php',$idx[2][1],1);
			//noselectmenu('��ü���� �߼۳��� ����','member_mailallsendinfo.php',$idx[2][2],1);
			noselectmenu('���� SMS �߼�',"javascript:parent.topframe.GoMenu(7,'market_smssinglesend.php');",$idx[2][2],1);
			noselectmenu('��ü SMS �߼�',"javascript:parent.topframe.GoMenu(7,'market_smsgroupsend.php');",$idx[2][3],2);
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
