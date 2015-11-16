<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language=javascript src="themeCtgrPrdt.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}
</script>
<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed" height="100%">
<col width=190></col>
<col width=20></col>
<col width=></col>
<col width=20></col>
<tr>
	<td width=190 valign=top nowrap background="images/minishop_leftbg.gif"><? include ("menu.php"); ?></td>
	<td width=20 nowrap></td>
	<td valign=top style="padding-top:20px">

	<table width="100%"  border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td><img src="images/themecode_prdtin_title.gif"></td>
				</tr>
				<tr>
					<td height=5 background="images/minishop_titlebg.gif">
				</tr>
				</table>
			</td>
		</tr>
		<tr><td height=10></td></tr>
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td colspan=3 >


						<table cellpadding="10" cellspacing="1" width="100%" bgcolor="#EFEFF2">
							<tr>
								<td  bgcolor="#F5F5F9" style="padding:20px">
									<table border=0 cellpadding=0 cellspacing=0 width=100%>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">미니샵 내 상품을 자유롭게 테마 카테고리에 진열할 수 있습니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">미니샵 특성에 맞게 테마를 설정하여 카테고리를 효과적으로 운영해 보세요.</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>



					</td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=40></td></tr>
			<tr>
				<td>
				


				
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td><img src="images/themecode_prdtin_stitle01.gif" border=0 align=absmiddle alt="내 상품 찾기"></td>
				</tr>
				<tr><td height=5></td></tr>
				<td bgcolor=E1E1E1 style=padding:4>
				<tr>
					<td align=center valign=top bgcolor=eeeeee style=padding:10>

					<table width=100% border=0 cellspacing=0 cellpadding=0>

					<form name="prdListFrm" method="post">
					<input type="hidden" name="sectcode" value="">

					<tr valign=top>
						<td>
						<table border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td width=45><img src=images/sub_text02.gif border=0></td>
							<td>
							<table border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td>
								<select name="code" style=width:100 onchange="ACodeSendIt(this.options[this.selectedIndex].value);">
								<option value="">--선택하세요--</option>
<?
								$sql = "SELECT SUBSTRING(productcode,1,3) as prcode FROM tblproduct ";
								$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
								$sql.= "AND display='Y' GROUP BY prcode ";
								$result=mysql_query($sql,get_db_conn());
								$codes="";
								while($row=mysql_fetch_object($result)) {
									$codes.=$row->prcode.",";
								}
								mysql_free_result($result);
								if(strlen($codes)>0) {
									$codes=substr($codes,0,-1);
									$prcodelist=ereg_replace(',','\',\'',$codes);
								}
								if(strlen($prcodelist)>0) {
									$sql = "SELECT codeA,codeB,codeC,codeD,code_name FROM tblproductcode ";
									$sql.= "WHERE codeA IN ('".$prcodelist."') AND codeB='000' AND codeC='000' ";
									$sql.= "AND codeD='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
									$result=mysql_query($sql,get_db_conn());
									while($row=mysql_fetch_object($result)) {
										echo "<option value=\"".$row->codeA."\">".$row->code_name."</option>\n";
									}
									mysql_free_result($result);
								}
?>
								</select>
								</td>
								<td><iframe name="BCodeCtgr" src="product_code.ctgr.php" width="100" height="23" scrolling=no frameborder=no></iframe></td>
								<td><iframe name="CCodeCtgr" src="product_code.ctgr.php" width="100" height="23" scrolling=no frameborder=no></iframe></td>
								<td><iframe name="DCodeCtgr" src="product_code.ctgr.php" width="100" height="23" scrolling=no frameborder=no></iframe></td>
							</tr>
							</table>
							</td>
						</tr>
						</table>
						</td>
						<td align=right>
						<table border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td width=45><img src=images/sub_text03.gif border=0></td>
							<td><input class="input" type=text name="goodNm" size=18 value="" class=txt onkeydown="if(event.keyCode == 13) return f_getData();"> <img src=images/btn_search01.gif border=0 align=absmiddle style="cursor:hand" onClick="f_getData()" ></td>
						</tr>
						</table>
						</td>
					</tr>

					</form>

					</table>

					</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height="220"><iframe name="PrdtListIfrm" src="product_prlist.select.php" width="100%" height="100%" scrolling=no frameborder=no style="background:FFFFFF"></iframe></td>
				</tr>



				<tr>
					<td valign=top>
					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr height=114 valign=top style=padding-top:10>
						<td width=50%> </td>
						<td width=204 nowrap align=center background=images/center_bg09.gif>
						<table border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td style=color:2A97A7 class=small>* 선택한 상품을<br>&nbsp; 테마 카테고리로<br>&nbsp; 복사합니다.</td>
						</tr>
						<tr>
							<td height=10></td>
						</tr>
						<tr>
							<td align=center><img src=images/btn_copy02.gif border=0 style="cursor:hand" onClick="copyPrdInfo();"></td>
						</tr>
						</table>
						</td>
						<td width=50%> </td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td>
					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td valign=top>
						<table border=0 cellspacing=0 cellpadding=0>
						<tr valign=top>
							<td><img src="images/themecode_prdtin_stitle02.gif" border=0 align=absmiddle alt="테마 카테고리"></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td height=4></td>
					</tr>
					<tr>
						<td height=1 bgcolor=eeeeee></td>
					</tr>

					<form name="ThemePrdtListFrm" method="post" >
					<input type="hidden" name="theme_sectcode" value="0">

					<tr>
						<td valign=top bgcolor=eeeeee style=padding:10>
						<table width=100% border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td width=45><img src=images/sub_text04.gif border=0></td>
							<td>
							<table border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td>
								<select name="ThemeACodeCtgr" style="width:170px" onchange="ThemeACodeIt(document.prdListFrm, this.options[this.selectedIndex]);SelCtgrPrdtList();">
								<option value="0">---대분류----</a>
<?
								$sql = "SELECT codeA,codeB,code_name FROM tblvenderthemecode ";
								$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' AND codeB='000' ";
								$sql.= "ORDER BY sequence DESC ";
								$result=mysql_query($sql,get_db_conn());
								while($row=mysql_fetch_object($result)) {
									echo "<option value=\"".$row->codeA."\">".$row->code_name."</option>\n";
								}
								mysql_free_result($result);
?>
								</select>
								</td>
								<td>
								<iframe id="ThemeBCodeCtgr" src="product_themecode.ctgr.php" width="170" height="23" scrolling=no frameborder=no></iframe>
								</td>
							</tr>
							</table>
							</td>
							<td align=right>
							<table border=0 cellspacing=0 cellpadding=0>
							<tr>
								<td width=45><img src=images/sub_text03.gif border=0></td>
								<td><input class="input" type=text name="themeGoodNm" size=18 value="" class=txt onkeydown="if(event.keyCode == 13) return ThemeSelCtgrPrdtList();" > <img src=images/btn_search01.gif border=0 align=absmiddle style="cursor:hand" onClick="ThemeSelCtgrPrdtList();"></td>
							</tr>
							</table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td height=5></td>
					</tr>
					</form>
					<tr>
						<td valign=top align=center>
						<iframe name="ThemePrdtListIfrm" src="product_themeprlist.select.php" width="100%" height="460" scrolling=no frameborder=no></iframe>
						</td>
					</tr>
					</table>
					</td>
				</tr>



				</table>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

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

<SCRIPT FOR=BCodeCtgr EVENT=onload LANGUAGE="JScript">
  loadedNum++;
  if(bodyOnLoad == 1 && loadedNum == 3) f_getData();
</SCRIPT>
<SCRIPT FOR=CCodeCtgr EVENT=onload LANGUAGE="JScript">
  loadedNum++;
  if(bodyOnLoad == 1 && loadedNum == 3) f_getData();
</SCRIPT>
<SCRIPT FOR=DCodeCtgr EVENT=onload LANGUAGE="JScript">
  loadedNum++;
  if(bodyOnLoad == 1 && loadedNum == 3) f_getData();
</SCRIPT>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>