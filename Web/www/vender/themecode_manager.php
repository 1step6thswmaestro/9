<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="tree_menu.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}
</script>
<table border=0 cellpadding=0 cellspacing=0 width=100% height="100%" style="table-layout:fixed">
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
					<td><img src="images/themecode_manager_title.gif"></td>
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
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">미니샵에 표시되는 기본 카테고리는 변경할 수 없습니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">기본 카테고리 외에 미니샵 내 카테고리를 미니샵의 특성에 맞게 자유롭게 설정하고 추가로 진열할 수 있습니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">테마 카테고리 작업 후 [저장하기] 버튼을 클릭하셔야 실제 반영됩니다.</td>
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
				





				<table width=100% border=0 cellspacing=0 cellpadding=0>
				<tr>
					<td valign=top>
					<table width="100%" border=0 cellspacing=0 cellpadding=0>
					<col width=12></col>
					<col width=></col>
					<tr valign=top>
						<td><img src="images/themecode_manager_stitle01.gif" border=0 align=absmiddle alt="카테고리 노출 설정"></td>
					</tr>
					<tr>
						<td bgcolor=#E1E1E1 style="padding:4">
						<table border=0 cellpadding=6 cellspacing=0 width=100% bgcolor=#FFFFFF>
						<tr>
							<td height="50">
							<input type="radio" name="code_disptype" value="YY" style='cursor:hand' <?if($_venderdata->code_distype=="YY")echo"checked";?>>기본 카테고리 + 테마 카테고리 노출
							&nbsp;&nbsp;
							<input type="radio" name="code_disptype" value="YN" style='cursor:hand' <?if($_venderdata->code_distype=="YN")echo"checked";?>>기본 카테고리만 노출
							&nbsp;&nbsp;
							<input type="radio" name="code_disptype" value="NY" style='cursor:hand' <?if($_venderdata->code_distype=="NY")echo"checked";?>>테마 카테고리만 노출</td>
							<td align="right"><img src="images/btn_save02.gif" border="0" style="cursor:hand" onClick="tcodelistifrm.SaveCodeDispType()"></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=30></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=230></col>
				<col width=10></col>
				<col width=></col>
				<tr>
					<td valign=top>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td><img src="images/icon_dot03.gif" border=0 align=absmiddle><b> 테마 카테고리 미리보기</b></td>
					</tr>
					<tr><td height=3></td></tr>
					<tr>
						<td bgcolor=E1E1E1 style=padding:4>
						<div id=menutree style="width:100%;height:215;overflow:auto">
						<table bgcolor=FFFFFF width=100% border=0 cellspacing=0 cellpadding=0 >
						<tr>
							<td height=215 valign=top style=padding-left:10>
							<script language="Javascript">
							foldersTree = genFolderRoot(" &nbsp;최상위 카테고리", "themecode_manager.list.php", 'tcodelistifrm');
<?
							$sql = "SELECT codeA,code_name FROM tblvenderthemecode ";
							$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' AND codeB='000' "; 
							$sql.= "ORDER BY sequence DESC ";
							$result2=mysql_query($sql,get_db_conn());
							while($row2=mysql_fetch_object($result2)) {
								echo "tcode".$row2->codeA." = insFolder(foldersTree, genFolder(\"".$row2->code_name."\", \"themecode_manager.list.php?codeA=".$row2->codeA."\", \"tcodelistifrm\"));\n";

								$sql = "SELECT codeA,code_name FROM tblvenderthemecode ";
								$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
								$sql.= "AND codeA='".$row2->codeA."' AND codeB!='000' ORDER BY sequence DESC ";
								$result=mysql_query($sql,get_db_conn());
								while($row=mysql_fetch_object($result)) {
									echo "insItem(tcode".$row->codeA.", genItem(\"".$row->code_name."\", \"themecode_manager.list.php?codeA=".$row->codeA."\", \"tcodelistifrm\"));\n";
								}
								mysql_free_result($result);
							}
							mysql_free_result($result2);

							echo "initializeDocument(foldersTree);\n";
?>
							</script>

							</td>
						</tr>
						</table>
						</div>
						</td>
					</tr>
					</table>
					</td>
					<td></td>
					<td valign=top>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td><img src="images/icon_dot03.gif" border=0 align=absmiddle><b> 테마 카테고리 수정/삭제</b></td>
					</tr>
					<tr><td height=3></td></tr>
					<tr>
						<td bgcolor=E1E1E1 valign=top style=padding:4>
						<iframe name="tcodelistifrm" src="themecode_manager.list.php" width="100%" height="215" scrolling=no frameborder=no style="background:FFFFFF"></iframe>
						</td>
					</tr>
					<tr><td height=5></td></tr>
					<tr><td height=1 bgcolor=D7D7D7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 style=padding:10,20>
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td>카테고리명 <input class=input type=text name=ctgrEdit value="" class=txt onKeydown="tcodelistifrm.f_editChange();" onChange="tcodelistifrm.f_editChange();"></td>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr><td height=1 bgcolor=D7D7D7></td></tr>
					<tr><td height=10></td></tr>
					<tr>
						<td align=center>
						<img src=images/btn_top01.gif border=0 align=absmiddle style="cursor:hand" onClick="tcodelistifrm.moveTop()">
						<img src=images/btn_up03.gif border=0 align=absmiddle style="cursor:hand" onClick="tcodelistifrm.moveUp()">
						<img src=images/btn_down13.gif border=0 align=absmiddle style="cursor:hand" onClick="tcodelistifrm.moveDown()">
						<img src=images/btn_bottom01.gif border=0 align=absmiddle style="cursor:hand" onClick="tcodelistifrm.moveBottom()">
						&nbsp; <img src=images/btn_add03.gif border=0 align=absmiddle style="cursor:hand" onClick="tcodelistifrm.addRow()">
						<img src=images/btn_delete08.gif border=0 align=absmiddle style="cursor:hand" onClick="tcodelistifrm.delRow()">
						<img src=images/btn_save02.gif border=0 align=absmiddle style="cursor:hand" onClick="tcodelistifrm.applyRow()">
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>

				<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

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

<?=$onload?>

<? INCLUDE "copyright.php"; ?>