<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### ÆäÀÌÁö Á¢±Ù±ÇÇÑ check ###############
$PageCode = "de-5";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

if(strlen($seachIdx)==0) {
	$seachIdx = "ÀüÃ¼";
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function SearchSubmit(seachIdxval) {
	form = document.form1;
	form.mode.value="";
	form.seachIdx.value = seachIdxval;
	form.submit();
}

function design_preview(design) {
	document.all["preview_img"].src="images/sample/brand"+design+".gif";
}

function CodeProcessFun(brandselectedIndex,brandcode) {
	if(brandselectedIndex>-1) {
		document.form2.mode.value="";
		document.form2.code.value=brandcode;
		document.form2.target="MainPrdtFrame";
		document.form2.action="design_eachblist.list.php";
		document.form2.submit();
	}
}
</script>
<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 300;HEIGHT: 250;}
</STYLE>

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
			<? include ("menu_design.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">ÇöÀçÀ§Ä¡ : µðÀÚÀÎ°ü¸® &gt; °³º°µðÀÚÀÎ-ÆäÀÌÁö º»¹® &gt; <span class="2depth_select">»óÇ°ºê·£µå È­¸é ²Ù¹Ì±â</span></td>
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
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/design_productbrand_title.gif" border="0"></TD>
					</tr>
<tr>
<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">»óÇ° ºê·£µåº° È­¸é µðÀÚÀÎÀ» ÀÚÀ¯·Ó°Ô µðÀÚÀÎ ÇÏ½Ç ¼ö ÀÖ½À´Ï´Ù.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/design_productbrand_stitle.gif" border="0"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=seachIdx value="">
			<tr>
				<td style="padding-top:3pt;">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width="400"></col>
				<col width="30"></col>
				<col></col>
				<TR>
					<TD background="images/table_top_line.gif" colspan="3"></TD>
				</TR>
				<TR>
					<TD class="table_cell" align="center"><b>ÀüÃ¼ ºê·£µå</b></td>
					<TD class="table_cell1" align="center">&nbsp;</TD>
					<TD class="table_cell1" align="center" background="images/blueline_bg.gif"><span class="font_blue">ÇöÀç »óÇ° ºê·£µåº° ÅÛÇÃ¸´</span></TD>
				</TR>
				<TR>
					<TD colspan="3" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD valign="top" style="padding:3pt;">
					<table border=0 cellpadding=0 cellspacing=0 width="100%">
					<tr>
						<td style="padding:5px;padding-left:2px;padding-right:2px;">
						<table border=0 cellpadding=0 cellspacing=0 width="100%">
						<tr align="center">
							<td><b><a href="javascript:SearchSubmit('A');"><span id="A">A</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('B');"><span id="B">B</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('C');"><span id="C">C</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('D');"><span id="D">D</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('E');"><span id="E">E</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('F');"><span id="F">F</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('G');"><span id="G">G</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('H');"><span id="H">H</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('I');"><span id="I">I</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('J');"><span id="J">J</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('K');"><span id="K">K</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('L');"><span id="L">L</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('M');"><span id="M">M</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('N');"><span id="N">N</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('O');"><span id="O">O</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('P');"><span id="P">P</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('Q');"><span id="Q">Q</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('R');"><span id="R">R</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('S');"><span id="S">S</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('T');"><span id="T">T</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('U');"><span id="U">U</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('V');"><span id="V">V</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('W');"><span id="W">W</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('X');"><span id="X">X</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('Y');"><span id="Y">Y</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('Z');"><span id="Z">Z</span></a></b></td>
						</TR>
						</table>
						</td>
						<td width="40" align="center" nowrap><b><a href="javascript:SearchSubmit('ÀüÃ¼');"><span id="ÀüÃ¼">ÀüÃ¼</span></a></b></td>
					</tr>
					<tr>
						<!-- »óÇ°ºê·£µå ¸ñ·Ï -->
						<td width="100%"><select name="up_brandlist" size="16" style="width:100%;" onchange="CodeProcessFun(this.selectedIndex,this.value);">
					<?
						$sql = "SELECT * FROM tblproductbrand ";
						if(ereg("^[¤¡-¤¾]", $seachIdx)) {
							if($seachIdx == "¤¡") $sql.= "WHERE (brandname >= '¤¡' AND brandname < '¤¤') OR (brandname >= '°¡' AND brandname < '³ª') ";
							if($seachIdx == "¤¤") $sql.= "WHERE (brandname >= '¤¤' AND brandname < '¤§') OR (brandname >= '³ª' AND brandname < '´Ù') ";
							if($seachIdx == "¤§") $sql.= "WHERE (brandname >= '¤§' AND brandname < '¤©') OR (brandname >= '´Ù' AND brandname < '¶ó') ";
							if($seachIdx == "¤©") $sql.= "WHERE (brandname >= '¤©' AND brandname < '¤±') OR (brandname >= '¶ó' AND brandname < '¸¶') ";
							if($seachIdx == "¤±") $sql.= "WHERE (brandname >= '¤±' AND brandname < '¤²') OR (brandname >= '¸¶' AND brandname < '¹Ù') ";
							if($seachIdx == "¤²") $sql.= "WHERE (brandname >= '¤²' AND brandname < '¤µ') OR (brandname >= '¹Ù' AND brandname < '»ç') ";
							if($seachIdx == "¤µ") $sql.= "WHERE (brandname >= '¤µ' AND brandname < '¤·') OR (brandname >= '»ç' AND brandname < '¾Æ') ";
							if($seachIdx == "¤·") $sql.= "WHERE (brandname >= '¤·' AND brandname < '¤¸') OR (brandname >= '¾Æ' AND brandname < 'ÀÚ') ";
							if($seachIdx == "¤¸") $sql.= "WHERE (brandname >= '¤¸' AND brandname < '¤º') OR (brandname >= 'ÀÚ' AND brandname < 'Â÷') ";
							if($seachIdx == "¤º") $sql.= "WHERE (brandname >= '¤º' AND brandname < '¤»') OR (brandname >= 'Â÷' AND brandname < 'Ä«') ";
							if($seachIdx == "¤»") $sql.= "WHERE (brandname >= '¤»' AND brandname < '¤¼') OR (brandname >= 'Ä«' AND brandname < 'Å¸') ";
							if($seachIdx == "¤¼") $sql.= "WHERE (brandname >= '¤¼' AND brandname < '¤½') OR (brandname >= 'Å¸' AND brandname < 'ÆÄ') ";
							if($seachIdx == "¤½") $sql.= "WHERE (brandname >= '¤½' AND brandname < '¤¾') OR (brandname >= 'ÆÄ' AND brandname < 'ÇÏ') ";
							if($seachIdx == "¤¾") $sql.= "WHERE (brandname >= '¤¾' AND brandname < '¤¿') OR (brandname >= 'ÇÏ' AND brandname < 'É¡') ";
							$sql.= "ORDER BY brandname ";
						} else if($seachIdx == "±âÅ¸") {
							$sql.= "WHERE (brandname < '¤¡' OR brandname >= '¤¿') AND (brandname < '°¡' OR brandname >= 'É¡') AND (brandname < 'a' OR brandname >= '{') AND (brandname < 'A' OR brandname >= '[') ";
							$sql.= "ORDER BY brandname ";
						} else if(ereg("^[A-Z]", $seachIdx)) {
							$sql.= "WHERE brandname LIKE '".$seachIdx."%' OR brandname LIKE '".strtolower($seachIdx)."%' ";	
							$sql.= "ORDER BY brandname ";
						} else {
							$sql.= "ORDER BY brandname ";
						}
						$result=mysql_query($sql,get_db_conn());
						while($row=mysql_fetch_object($result)) {
							$brandopt .= "<option value=\"".$row->bridx."\">".$row->brandname."</option>\n";
						}

						if(strlen($brandopt)>0 && $seachIdx == "ÀüÃ¼") {
							$brandopt = "<option value=\"".$seachIdx."\">------------ ".$seachIdx." ºê·£µå ÀÏ°ý °³º°µðÀÚÀÎ ------------</option>\n".$brandopt;
						}
						echo $brandopt;
					?>
						</select></td>
						<td width="40" align="center" nowrap valign="top">
						<table border=0 cellpadding=0 cellspacing=0 width="100%">
						<tr><td align="center"><b><a href="javascript:SearchSubmit('¤¡');"><span id="¤¡">¤¡</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('¤¤');"><span id="¤¤">¤¤</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('¤§');"><span id="¤§">¤§</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('¤©');"><span id="¤©">¤©</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('¤±');"><span id="¤±">¤±</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('¤²');"><span id="¤²">¤²</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('¤µ');"><span id="¤µ">¤µ</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('¤·');"><span id="¤·">¤·</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('¤¸');"><span id="¤¸">¤¸</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('¤º');"><span id="¤º">¤º</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('¤»');"><span id="¤»">¤»</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('¤¼');"><span id="¤¼">¤¼</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('¤½');"><span id="¤½">¤½</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('¤¾');"><span id="¤¾">¤¾</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('±âÅ¸');"><span id="±âÅ¸">±âÅ¸</span></a></b></td></tr>
						</table>
						</td>
					</tr>
					</table>
					</TD>
					<TD class="td_con1" align="center"><img src="images/btn_next1.gif" border="0" hspace="5"></TD>
					<TD class="td_con1" align="center" style="padding:5pt;">&nbsp;<img id="preview_img" width="200" height="214" style="display:none" border="0" vspace="0" class="imgline"><br><p align="left"><b>&quot;¸ðµç ºê·£µå ÀÏ°ý °³º°µðÀÚÀÎ&quot; </b>À» Àû¿ëÇÒ °æ¿ì °³º° µðÀÚÀÎ »ç¿ëÁßÀÎ ºê·£µå¸¦ Á¦¿ÜÇÑ ÅÛÇÃ¸´À» »ç¿ëÇÏ´Â ¸ðµç ºê·£µå°¡ °³º°µðÀÚÀÎÀ¸·Î ÀÏ°ý º¯°æµË´Ï´Ù.</TD>
				</TR>
				<TR>
					<TD background="images/table_top_line.gif" colspan="3"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td><IFRAME name="MainPrdtFrame" src="design_eachblist.list.php" width=100% height=350 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 HEIGHT=45 ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 HEIGHT=45 ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<TD><IMG SRC="images/manual_top2.gif" WIDTH=18 HEIGHT=45 ALT=""></TD>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" class=menual_bg style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"  class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><p class="LIPoint"><B><span class="font_orange">»óÇ°ºê·£µå È­¸é ¸ÅÅ©·Î¸í·É¾î</span></B>(ÇØ´ç ¸ÅÅ©·Î¸í·É¾î´Â ´Ù¸¥ ÆäÀÌÁö µðÀÚÀÎ ÀÛ¾÷½Ã »ç¿ëÀÌ ºÒ°¡´ÉÇÔ)</p></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><p>&nbsp;</p></td>
						<td >
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BRANDNAME]</td>
							<td class=td_con1 style="padding-left:5;">
							ÇöÀç ºê·£µå/Ä«Å×°í¸®¸í
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BRANDNAVI??????_??????]</td>
							<td class=td_con1 style="padding-left:5;">
							ºê·£µå ³×ºñ°ÔÀÌ¼Ç 
									<br><img width=10 height=0>
									<FONT class=font_orange>¾Õ?????? : È¨ ¶Ç´Â ÇöÀç ºê·£µå »ö»ó</FONT> - <FONT COLOR="red">"#"Á¦¿Ü</FONT>
									<br><img width=10 height=0>
									<FONT class=font_orange>µÚ?????? : ÇöÀç ºê·£µå ¶Ç´Â ÇöÀç ºê·£µå°¡ ¼ÓÇÑ Ä«Å×°í¸® »ö»ó</FONT> - <FONT COLOR="red">"#"Á¦¿Ü</FONT>
									<br>
									<FONT class=font_blue>¿¹) [BRANDNAVI] or [BRANDNAVI000000_FF0000]</FONT>
							</td>
						</tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[CLIPCOPY]</td>
							<td class=td_con1 style="padding-left:5;">
							ÇöÀçÁÖ¼Ò º¹»ç ¹öÆ° <FONT class=font_blue>(¿¹:&lt;a href=[CLIPCOPY]>ÁÖ¼Òº¹»ç&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BRANDEVENT]</td>
							<td class=td_con1 style="padding-left:5;">
							ºê·£µåº° ÀÌº¥Æ® ÀÌ¹ÌÁö/html
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BRANDGROUP]</td>
							<td class=td_con1 style="padding-left:5;">
							»óÇ° ºê·£µå Ä«Å×°í¸® ±×·ì
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right bgcolor=#E9A74E style="padding-right:15">»óÇ° ºê·£µå Ä«Å×°í¸® ±×·ì °ü·Ã ½ºÅ¸ÀÏ Á¤ÀÇ</td>
							<td class=td_con1 bgcolor=#FEEEE2 style="padding-left:5;">
										<img width=10 height=0>
										<FONT class=font_orange>#group1_td - »óÀ§Ä«Å×°í¸® TD ½ºÅ¸ÀÏ Á¤ÀÇ (»çÀÌÁî ¹× ¹é±×¶ó¿îµåÄÃ·¯)</FONT>
										<br><img width=100 height=0>
										<FONT class=font_blue>¿¹) #group1_td { background-color:#E6E6E6;width:25%; }</FONT>
										<br><img width=0 height=7><br><img width=10 height=0>
										<FONT class=font_orange>#group2_td - ÇÏÀ§Ä«Å×°í¸® TD ½ºÅ¸ÀÏ Á¤ÀÇ (»çÀÌÁî ¹× ¹é±×¶ó¿îµåÄÃ·¯)</FONT>
										<br><img width=100 height=0>
										<FONT class=font_blue>¿¹) #group2_td { background-color:#EFEFEF; }</FONT>
										<br><img width=0 height=7><br><img width=10 height=0>
										<FONT class=font_orange>#group_line - »óÀ§±×·ì°ú »óÀ§±×·ì »çÀÌÀÇ °¡·Î¶óÀÎ ¼¿ ½ºÅ¸ÀÏ Á¤ÀÇ</FONT>
										<br><img width=100 height=0>
										<FONT class=font_blue>¿¹) #group_line { background-color:#FFFFFF;height:1px; }</FONT>
				<pre style="line-height:15px">
<B>[»ç¿ë ¿¹]</B> - ³»¿ë º»¹®¿¡ ¾Æ·¡¿Í °°ÀÌ Á¤ÀÇÇÏ½Ã¸é µË´Ï´Ù.

<FONT class=font_blue>&lt;style>
  #group1_td { background-color:#E6E6E6;width:25%; }
  #group2_td { background-color:#EFEFEF; }
  #group_line { background-color:#FFFFFF;height:1px; }
&lt;/style></FONT></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TOTAL]</td>
							<td class=td_con1 style="padding-left:5;">
							ÃÑ »óÇ°¼ö <FONT class=font_blue>(¿¹:ÃÑ [TOTAL]°Ç)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SORTPRODUCTUP]</td>
							<td class=td_con1 style="padding-left:5;">
							Á¦Á¶»ç ¤¡¤¤¤§¼ø Á¤·Ä  <FONT class=font_blue>(¿¹:&lt;a href=[SORTPRODUCTUP]>Á¦Á¶»ç¼ø¡ã&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SORTPRODUCTDN]</td>
							<td class=td_con1 style="padding-left:5;">
							Á¦Á¶»ç ¤§¤¤¤¡¼ø Á¤·Ä <FONT class=font_blue>(¿¹:&lt;a href=[SORTPRODUCTDN]>Á¦Á¶»ç¼ø¡å&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SORTNAMEUP]</td>
							<td class=td_con1 style="padding-left:5;">
							»óÇ°¸í ¤¡¤¤¤§¼ø Á¤·Ä <FONT class=font_blue>(¿¹:&lt;a href=[SORTNAMEUP]>»óÇ°¸í¼ø¡ã&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SORTNAMEDN]</td>
							<td class=td_con1 style="padding-left:5;">
							»óÇ°¸í ¤§¤¤¤¡¼ø Á¤·Ä <FONT class=font_blue>(¿¹:&lt;a href=[SORTNAMEDN]>»óÇ°¸í¼ø¡å&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SORTPRICEUP]</td>
							<td class=td_con1 style="padding-left:5;">
							³·Àº »óÇ°°¡°Ý¼ø <FONT class=font_blue>(¿¹:&lt;a href=[SORTPRICEUP]>°¡°Ý¼ø¡ã&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SORTPRICEDN]</td>
							<td class=td_con1 style="padding-left:5;">
							³ôÀº »óÇ°°¡°Ý¼ø <FONT class=font_blue>(¿¹:&lt;a href=[SORTPRICEDN]>°¡°Ý¼ø¡å&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SORTRESERVEUP]</td>
							<td class=td_con1 style="padding-left:5;">
							³·Àº Àû¸³±Ý¼ø <FONT class=font_blue>(¿¹:&lt;a href=[SORTRESERVEUP]>Àû¸³±Ý¼ø¡ã&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SORTRESERVEDN]</td>
							<td class=td_con1 style="padding-left:5;">
							³ôÀº Àû¸³±Ý¼ø <FONT class=font_blue>(¿¹:&lt;a href=[SORTRESERVEDN]>Àû¸³±Ý¼ø¡å&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>

						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONNEW]</td>
							<td class=td_con1 style="padding-left:5;">
								½Å±Ôµî·Ï »óÇ°¼ø ¼±ÅÃ Ç¥½Ã <FONT class=font_blue>(class="sortOn", /lib/style.php ÆÄÀÏ¿¡¼­ css Á¤ÀÇ)</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONBEST]</td>
							<td class=td_con1 style="padding-left:5;">
								ÀÎ±â»óÇ°¼ø ¼±ÅÃ Ç¥½Ã <FONT class=font_blue>(class="sortOn", /lib/style.php ÆÄÀÏ¿¡¼­ css Á¤ÀÇ)</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONPRICEUP]</td>
							<td class=td_con1 style="padding-left:5;">
								³·Àº °¡°Ý¼ø ¼±ÅÃ Ç¥½Ã <FONT class=font_blue>(class="sortOn", /lib/style.php ÆÄÀÏ¿¡¼­ css Á¤ÀÇ)</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONPRICEDN]</td>
							<td class=td_con1 style="padding-left:5;">
								³ôÀº °¡°Ý¼ø ¼±ÅÃ Ç¥½Ã <FONT class=font_blue>(class="sortOn", /lib/style.php ÆÄÀÏ¿¡¼­ css Á¤ÀÇ)</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONRESERVEDN]</td>
							<td class=td_con1 style="padding-left:5;">
								Àû¸³±Ý¼ø ¼±ÅÃ Ç¥½Ã <FONT class=font_blue>(class="sortOn", /lib/style.php ÆÄÀÏ¿¡¼­ css Á¤ÀÇ)</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LISTSELECT]</td>
							<td class=td_con1 style="padding-left:5;">
								»óÇ°Ãâ·Â°¹¼ö ¼±ÅÃ
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>

						<tr>
							<td class=table_cell align=right style="padding-right:15">[PAGE]</td>
							<td class=td_con1 style="padding-left:5;">
							ÆäÀÌÁö Ç¥½Ã
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRLIST1??]</td>
							<td class=td_con1 style="padding-left:5;">
							»óÇ°¸ñ·Ï - ÀÌ¹ÌÁöAÇü
										<br><img width=10 height=0>
										<FONT class=font_orange>? : ¶óÀÎº° »óÇ°°¹¼ö(1~8)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : ¸î¶óÀÎÀ¸·Î Áø¿­À» ÇÒ°ÇÁö ¼ýÀÚÀÔ·Â(1-8)</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRLIST2??]</td>
							<td class=td_con1 style="padding-left:5;">
							»óÇ°¸ñ·Ï - ÀÌ¹ÌÁöBÇü
										<br><img width=10 height=0>
										<FONT class=font_orange>? : ¶óÀÎº° »óÇ°°¹¼ö(1~8)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : ¸î¶óÀÎÀ¸·Î Áø¿­À» ÇÒ°ÇÁö ¼ýÀÚÀÔ·Â(1-8)</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRLIST????????_??]</td>
							<td class=td_con1 style="padding-left:5;">
							»óÇ°¸ñ·Ï - ÀÌ¹ÌÁöAÇü/ÀÌ¹ÌÁöBÇü
										<br><img width=10 height=0>
										<FONT class=font_orange>? : À§¿¡ Á¦°øµÈ »óÇ°¸ñ·Ï ÇüÅÂ (1:ÀÌ¹ÌÁöAÇü, 2:ÀÌ¹ÌÁöBÇü)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : ¶óÀÎº° »óÇ°°¹¼ö(1~8)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : ¸î¶óÀÎÀ¸·Î Áø¿­À» ÇÒ°ÇÁö ¼ýÀÚÀÔ·Â(1-8)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : »óÇ° »çÀÌÀÇ ¼¼·Î¶óÀÎ Ç¥½Ã¿©ºÎ(Y/N/L)</FONT> (LÀº »óÇ°¿¡ ¸ÂÃß¾î ±æ°Ô Ç¥½ÃµÊ)
										<br><img width=10 height=0>
										<FONT class=font_orange>? : »óÇ° »çÀÌÀÇ °¡·Î¶óÀÎ Ç¥½Ã¿©ºÎ(Y/N)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : »óÇ° ½ÃÁß°¡°Ý Ç¥½Ã¿©ºÎ(Y/N)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : »óÇ° Àû¸³±Ý Ç¥½Ã¿©ºÎ(Y/N)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : »óÇ° ÅÂ±× Ç¥½Ã°¹¼ö(0-9) : 0ÀÏ °æ¿ì Ç¥½Ã¾ÈÇÔ</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>_?? : »óÇ°»çÀÌ(À§¾Æ·¡) °£°Ý ÃÖ´ë 99ÇÈ¼¿ (¹ÌÀÔ·Â½Ã 5ÇÈ¼¿)</FONT>
										<br>
										<FONT class=font_blue>¿¹) [PRLIST142NNYN2_10], [PRLIST222LYYY2_5]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRLIST3??]</td>
							<td class=td_con1 style="padding-left:5;">
							»óÇ°¸ñ·Ï - ¸®½ºÆ®Çü
										<br><img width=10 height=0>
										<FONT class=font_orange>?? : »óÇ°¸ñ·Ï Áø¿­°¹¼ö (01~20)</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRLIST3???????]</td>
							<td class=td_con1 style="padding-left:5;">
							»óÇ°¸ñ·Ï - ¸®½ºÆ®Çü
										<br><img width=10 height=0>
										<FONT class=font_orange>?? : »óÇ° Áø¿­°¹¼ö (01~20)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : »óÇ° ÀÌ¹ÌÁö Ç¥½Ã¿©ºÎ (Y/N)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : »óÇ° Á¦Á¶»ç Ç¥½Ã¿©ºÎ (Y/N)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : »óÇ° ½ÃÁß°¡°Ý Ç¥½Ã¿©ºÎ(Y/N)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : »óÇ° Àû¸³±Ý Ç¥½Ã¿©ºÎ(Y/N)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : »óÇ° ÅÂ±× Ç¥½Ã°¹¼ö(0-9) : 0ÀÏ °æ¿ì Ç¥½Ã¾ÈÇÔ</FONT>
										<br>
										<FONT class=font_blue>¿¹) [PRLIST304YYYY4]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRLIST4??_??]</td>
							<td class=td_con1 style="padding-left:5;">
							»óÇ°¸ñ·Ï - °øµ¿±¸¸ÅÇü
										<br><img width=10 height=0>
										<FONT class=font_orange>? : ¶óÀÎº° »óÇ°°¹¼ö(2~4)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : ¸î¶óÀÎÀ¸·Î Áø¿­À» ÇÒ°ÇÁö ¼ýÀÚÀÔ·Â(1~8)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>_?? : »óÇ°»çÀÌ(À§¾Æ·¡) °£°Ý ÃÖ´ë 99ÇÈ¼¿ (¹ÌÀÔ·Â½Ã 5ÇÈ¼¿)</FONT>
										<br>
										<FONT class=font_blue>¿¹) [PRLIST423_5]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right bgcolor=#E9A74E style="padding-right:15">»óÇ°¸ñ·Ï ½ºÅ¸ÀÏ Á¤ÀÇ</td>
							<td class=td_con1 bgcolor=#FEEEE2 style="padding-left:5;">
										<img width=15 height=0><FONT class=font_orange>#prlist_colline - ÀÌ¹ÌÁö/¸®½ºÆ®ÇüÀÇ °¡·Î¶óÀÎ ¼¿ ½ºÅ¸ÀÏ Á¤ÀÇ</FONT>
										<br><img width=100 height=0>
										<FONT class=font_blue>¿¹) #prlist_colline { background-color:#f4f4f4;height:1px; }</FONT>
										<br><img width=0 height=7><br><img width=10 height=0>
										<FONT class=font_orange>#prlist_colline - ÀÌ¹ÌÁö/¸®½ºÆ®ÇüÀÇ °¡·Î¶óÀÎ ¼¿ ½ºÅ¸ÀÏ Á¤ÀÇ</FONT>
										<br><img width=100 height=0>
										<FONT class=font_blue>¿¹) #prlist_rowline { background-color:#f4f4f4;width:1px; }</FONT>
							<pre style="line-height:15px">
<B>[»ç¿ë ¿¹]</B> - ³»¿ë º»¹®¿¡ ¾Æ·¡¿Í °°ÀÌ Á¤ÀÇÇÏ½Ã¸é µË´Ï´Ù.
<FONT class=font_blue>&lt;style>
  #prlist_colline { background-color:#f4f4f4;height:1px; }
  #prlist_rowline { background-color:#f4f4f4;width:1px; }
&lt;/style></FONT></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						</table>
						</td>
					</tr>
					<tr>
						<td width="20" colspan="2"><p>&nbsp;</p></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><p class="LIPoint">³ª¸ð,µå¸²À§¹öµîÀÇ ¿¡µðÅÍ·Î ÀÛ¼º½Ã ÀÌ¹ÌÁö°æ·Îµî ÀÛ¾÷³»¿ëÀÌ Æ²·ÁÁú ¼ö ÀÖÀ¸´Ï ÁÖÀÇÇÏ¼¼¿ä!</p></td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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
<form name=form2 action="" method=post>
<input type=hidden name=mode>
<input type=hidden name=code>
</form>
</table>
<script language="javascript">
<!--
<?
	if(strlen($seachIdx)>0) {
		echo "document.getElementById(\"$seachIdx\").style.color=\"#FF4C00\";";
	} else {
		echo "document.getElementById(\"TTL\").style.color=\"#FF4C00\";";
	}
?>
//-->
</script>
<?=$onload?>

<? INCLUDE "copyright.php"; ?>