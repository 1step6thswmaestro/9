<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### ÆäÀÌÁö Á¢±Ù±ÇÇÑ check ###############
$PageCode = "de-4";
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
		document.form2.action="design_blist.list.php";
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">ÇöÀçÀ§Ä¡ : µğÀÚÀÎ°ü¸® &gt; ÅÛÇÃ¸´-ÆäÀÌÁö º»¹® &gt; <span class="2depth_select">»óÇ°ºê·£µå È­¸é ÅÚÇÃ¸´</span></td>
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
					<TD><IMG SRC="images/design_blist.list_title.gif" border="0"></TD>
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
					<TD width="100%" class="notice_blue">»óÇ° ºê·£µåº° È­¸é µğÀÚÀÎÀ» ¼±ÅÃÇÏ¿© »ç¿ëÇÏ½Ç ¼ö ÀÖ½À´Ï´Ù.</TD>
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
					<TD><IMG SRC="images/design_blist_stitle1.gif" border="0"></TD>
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
				<td style="width:764px; padding-top:3pt;">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width="350"></col>
				<col width="30"></col>
				<col></col>
				<TR>
					<TD background="images/table_top_line.gif" colspan="3"></TD>
				</TR>
				<TR>
					<TD class="table_cell" align="center"><b>ÀüÃ¼ ºê·£µå</b></td>
					<TD class="table_cell1" align="center">&nbsp;</TD>
					<TD class="table_cell1" align="center" background="images/blueline_bg.gif"><font color=#555555>ÇöÀç »óÇ° ºê·£µåº° ÅÛÇÃ¸´</span></TD>
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

						if(strlen($brandopt)>0 && $seachIdx != "ÀüÃ¼") {
							$brandopt = "<option value=\"".$seachIdx."\">--------------- ".$seachIdx." ºê·£µå ÀÏ°ı Àû¿ë ---------------</option>\n".$brandopt;
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
					<TD class="td_con1" align="center" style="padding:5pt;">&nbsp;<img id="preview_img" width="200" height="214" style="display:none" border="0" vspace="0" class="imgline"></TD>
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
				<td><IFRAME name="MainPrdtFrame" src="design_blist.list.php" width=100% height=350 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"  class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="100%"><span class="font_dotline">»óÇ°ºê·£µå ÅÛÇÃ¸´/°ü·Ã ÆÄÀÏ</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top">
							- <span class="font_orange">ÆÄÀÏ ¼öÁ¤½Ã ±âÁ¸ ÆÄÀÏÀ» ¹İµå½Ã ¹é¾÷ÇÏ½Ã±â ¹Ù¶ø´Ï´Ù.(ÆÄÀÏ ¼öÁ¤ ÈÄ ¹ß»ıµÈ ¹®Á¦¿¡ ´ëÇØ º¹±¸ ¼­ºñ½º¸¦ Áö¿øÇØ µå¸®Áö ¾Ê½À´Ï´Ù.)</span><br />
							- <span class="font_orange">ÅÛÇÃ¸´ ÆÄÀÏ : /templet/brandproduct/blist_L001.php</span><br />
							- <span class="font_orange">°ü·Ã ÆÄÀÏ : /front/productblist.php, /front/productblist_text.php</span>
						</td>
					</tr>
					<tr>
						<td height="20" colspan="2"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="100%"><span class="font_dotline">ÅÛÇÃ¸´ µğÀÚÀÎ</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- »óÇ° ºê·£µå ÆäÀÌÁö ³ëÃâ ¼³Á¤Àº <a href="javascript:parent.topframe.GoMenu(4,'product_brand.php');"><span class="font_blue">»óÇ°°ü¸® > Ä«Å×°í¸®/»óÇ°°ü¸® > »óÇ° ºê·£µå ¼³Á¤ °ü¸®</span></a> ¿¡¼­ ¼³Á¤ÇØ ÁÖ¼¼¿ä.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- ÅÛÇÃ¸´ µğÀÚÀÎ ÀÏ°ı Àû¿ëÀº ÇØ´ç ºê·£µåÀÇ ÅÛÇÃ¸´ÀÌ ÀÏ°ıÀûÀ¸·Î º¯°æ µË´Ï´Ù.</td>
					</tr>
					<tr>
						<td height="20" colspan="2"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="100%"><span class="font_dotline">°³º° µğÀÚÀÎ</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- <a href="javascript:parent.topframe.GoMenu(4,'design_eachblist.php');"><span class="font_blue">µğÀÚÀÎ°ü¸® > °³º°µğÀÚÀÎ - ÆäÀÌÁö º»¹® > »óÇ° ºê·£µå ²Ù¹Ì±â</span></a> ¿¡¼­ °³º° µğÀÚÀÎÀ» ÇÒ ¼ö ÀÖ½À´Ï´Ù.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- °³º° µğÀÚÀÎ »ç¿ë½Ã ÅÛÇÃ¸´Àº Àû¿ëµÇÁö ¾Ê½À´Ï´Ù.</td>
					</tr>
					<tr>
						<td height="20" colspan="2"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="100%"><span class="font_dotline">ÅÛÇÃ¸´ ÀçÀû¿ë</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- º» ¸Ş´º¿¡¼­ ¿øÇÏ´Â ÅÛÇÃ¸´À¸·Î Àç¼±ÅÃÇÏ¸é °³º°µğÀÚÀÎÀº ÇØÁ¦µÇ°í ¼±ÅÃÇÑ ÅÛÇÃ¸´À¸·Î Àû¿ëµË´Ï´Ù.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- °³º°µğÀÚÀÎ¿¡¼­ [±âº»°ªº¹¿ø] ¶Ç´Â [»èÁ¦ÇÏ±â] -> ±âº» ÅÛÇÃ¸´À¸·Î º¯°æµÊ -> ¿øÇÏ´Â  ÅÛÇÃ¸´À» ¼±ÅÃÇÏ½Ã¸é µË´Ï´Ù.</td>
					</tr>
					<tr>
						<td height="20" colspan="2"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="100%"><span class="font_dotline">ºê·£µåº° ÀÌº¥Æ® °ü¸®</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- <a href="javascript:parent.topframe.GoMenu(7,'market_eventbrand.php');"><span class="font_blue">¸¶ÄÉÆÃÁö¿ø > ÀÌº¥Æ®/»çÀºÇ° ±â´É ¼³Á¤ > ºê·£µåº° ÀÌº¥Æ® °ü¸®</span></a> ¿¡¼­ °¢°¢ ÀÌº¥Æ® °ü¸®¸¦ ÇÒ ¼ö ÀÖ½À´Ï´Ù.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- ºê·£µåº° ÀÌº¥Æ®´Â ÅÛÇÃ¸´ »ç¿ëÇÒ °æ¿ì¿¡¸¸ ÀÛµ¿ µË´Ï´Ù.</td>
					</tr>
					<tr>
						<td height="20" colspan="2"></td>
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