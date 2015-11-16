<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	INCLUDE ("access.php");


	if($_GET["pm_idx"] == "" || $_GET["pm_idx"] == null ){
		$cnt_sql = "select * from tblmobileplanningmain where 1=1";
		$cnt_result = mysql_query($cnt_sql, get_db_conn());
		$cnt_row = mysql_num_rows($cnt_result); 
		if($cnt_row >= 4){
			echo '<script>alert("최대 4개의 섹션만 생성가능 합니다.");window.close();</script>';
			exit;
		}
	}else{
		$query = "select * from tblmobileplanningmain where pm_idx = '$_GET[pm_idx]'";
		$row = mysql_fetch_array(mysql_query($query));
	}
	$gallery=$webzine=$list="";
	switch($row[display_type]){
		case "webzine" :
			$webzine = "checked";
		break;
		case "list" :
			$list = "checked";
		break;
		case "gallery" :
		default:
			$gallery = "checked";
		break;

	}
?>

<? INCLUDE "header.php"; ?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(f){
	if(f.title.value == "")	{	alert("설명을 입력하세요~");	f.title.focus();	return;	}
	if(f.product_cnt.value == "")	{	alert("메인에 출력할 상품수를 입력하세요~");	return;	}
	f.submit();
}
</script>

<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0">
	<iframe name="ifrm_ctrl" width=0 height=0 frameborder=0 align=top scrolling="no" marginheight="0" marginwidth="0"></iframe>
	<table cellpadding="0" cellspacing="0" width="100%" >
		<tr>
			<td valign="top">
				<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
					<tr>
						<td>
							<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
								<tr>
									<td valign="top">
										<table cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td>
													<table cellpadding="0" cellspacing="0" width="100%">
														<tr>
															<td><img src="images/mobile_main_planning_popt.gif" border="0"></td>
															<td width="100%" background="images/member_mailallsend_imgbg.gif"></td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td height="20"></td>
											</tr>
											<tr>
												<td align="center">
													<table WIDTH="95%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
														<tr>
															<td><img src="images/mobile_main_planing_stitle1.gif" border="0"></td>
															<td width="100%" background="images/shop_basicinfo_stitle_bg.gif"></td>
															<td><img src="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td height=3></td>
											</tr>

											<form name=form1 method=post action="mobile_main_planning_ctrl.php" target="ifrm_ctrl">
											<? if($_GET[pm_idx]=="") { ?>
											<input type="hidden" name="pm_idx" value="<?=$_GET[pm_idx]?>">
											<input type="hidden" name="mode" value="write">

											<? } else if($_GET[pm_idx]!="") { ?>
											<input type="hidden" name="pm_idx" value="<?=$row[pm_idx]?>">
											<input type="hidden" name="mode" value="modify">
											<? } ?>
											<tr>
												<td align="center">
													<table cellSpacing=0 cellPadding=0 width="95%" border=0>
														<tr>
															<td background="images/table_top_line.gif" colspan=2></td>
														</tr>

														<tr>
															<td class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">설명</td>
															<td class="td_con1" width="">
																<input type="text" name="title" style="width:54%" class="input" value="<?=$row[title]?>">
															</td>
														</tr>
														<tr>
															<td colspan="2" background="images/table_con_line.gif"></td>
														</tr>

														<tr>
															<td class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">사용유무</td>
															<td class="td_con1" width="">
																<input type="checkbox" name="display" value="Y" <? if($row[display]=="Y"){ echo "checked";}?>> 체크시 모바일 샵 메인페이지에 출력됩니다.
															</td>
														</tr>
														<tr>
															<td colspan="2" background="images/table_con_line.gif"></td>
														</tr>

														<tr>
															<td class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">디스플레이유형</td>
															<td class="td_con1" width="">

																<table width="300" border="0" cellpadding="0" cellspacing="0">
																	<tr align="center">
																		<td>
																			<!-- <img src="../admin/images/ex1.gif" /><br /> -->
																			<img src="../admin/images/mobile_main_display_gallery.gif" alt="갤러리형" />
																		</td>
																		<td><img src="../admin/images/mobile_main_display_webzine.gif" alt="웹진형" /></td>
																		<td><img src="../admin/images/mobile_main_display_list.gif" alt="리스트형" /></td>
																	</tr>
																	<tr align="center">
																		<td><input type="radio" name="display_type" value="gallery" <?=$gallery?>>갤러리형</td>
																		<td><input type="radio" name="display_type" value="webzine" <?=$webzine?>>웹진형</td>
																		<td><input type="radio" name="display_type" value="list" <?=$list?>>리스트형</td>
																	</tr>
																</table>
																<div style="margin-top:5px;padding-left:23px;color:#ff4c00;font-size:0.9em">
																	* 메인 디스플레이 타입을 <b>"리스트형"</b>으로 설정하실 경우, 상품 추가(수정)시 <br/>
																	<b>"모바일샵 이미지"</b>를 첨부하셔야 이미지가 노출 됩니다.
																</div>
															</td>
														</tr>
														<tr>
															<td colspan="2" background="images/table_con_line.gif"></td>
														</tr>

														<tr>
															<td class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">메인출력상품수</td>
															<td class="td_con1" width="600">
																<input type="text" name="product_cnt" size="5" value="<?=$row[product_cnt]?>">개
															</td>
														</tr>
														<tr>
															<td colspan="2" background="images/table_con_line.gif"></td>
														</tr>
														<td background="images/table_top_line.gif" colspan=2></td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td align="center" height=10></td>
										</tr>
										<tr>
											<td align="center"><img src="images/botteon_save.gif" border="0" style="cursor:hand" onclick="CheckForm(document.form1);" ></td>
										</tr>
										</form>
										<tr>
											<td height=20></td>
										</tr>
										<tr>
											<td height="50"></td>
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
	</table>
</body>
</html>