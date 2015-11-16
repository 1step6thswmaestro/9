<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");
?>
<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
</script>


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
			<? include ("menu_mobile.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 모바일 &gt; <span class="2depth_select">모바일 HOME</span></td>
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
				<td> <!-- <img src="images/product_maintitle.gif" border="0"> --> <b>모바일 사이트 이미지로</b></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td valign="top">
				<table cellpadding="0" cellspacing="0" width="99%" style="table-layout:fixed">
				<col width="50%"></col>
				<col width="50%"></col>
<?
				$shop_main_title[] = "product_mainstitle1.gif";
				$shop_main_title[] = "product_mainstitle2.gif";				

				$shop_main_stext[0][] = "product_mains0text01.gif";
				$shop_main_stext[0][] = "product_mains0text02.gif";
				$shop_main_stext[0][] = "product_mains0text03.gif";
				$shop_main_stext[0][] = "product_mains0text04.gif";
		
				
				$shop_main_stext[1][] = "product_mains1text01.gif";
				$shop_main_stext[1][] = "product_mains1text02.gif";
				
				$shop_main_slink[0][] = "product_code.php";
				$shop_main_slink[0][] = "product_register.php";
				$shop_main_slink[0][] = "product_assemble.php";
				$shop_main_slink[0][] = "product_mainlist.php";
				$shop_main_slink[0][] = "product_codelist.php";				

				$shop_main_slink[1][] = "product_imgmulticonfig.php";
				$shop_main_slink[1][] = "product_imgmultiset.php";
				
				$shop_main_sinfo[0][] = "내용입력1";
				$shop_main_sinfo[0][] = "내용입력2";
				$shop_main_sinfo[0][] = "내용입력3";
				$shop_main_sinfo[0][] = "내용입력4";
				
								
				$shop_main_sinfo[1][] = "내용입력";
				$shop_main_sinfo[1][] = "내용입력";

				

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
			<tr><td height="30"></td></tr>
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
					<TD COLSPAN=3 width="100%" valign="top" class="menual_bg" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><FONT color=#3d3d3d>매뉴얼 내용</FONT></td>
					</tr>
					<tr><td colspan=2 height=5></td></tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>
							<FONT color=#3d3d3d>매뉴얼 내용</FONT>
						</td>
					</tr>
					<tr><td colspan=2 height=5></td></tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>
							<FONT color=#3d3d3d>매뉴얼 내용</FONT>
						</td>
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
<?=$onload?>

<? INCLUDE "copyright.php"; ?>