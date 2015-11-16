<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include ("access.php");

	####################### 페이지 접근권한 check ###############
	$PageCode = "mo-1";
	$MenuCode = "mobile";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include ("AccessDeny.inc.php");
		exit;
	}
	#########################################################

	$result = mysql_query("select * from tblmobileconfig");
	$row = mysql_fetch_array($result);
?>

<? include "header.php"; ?>
<style type=text/css>
	#menuBar {}
	#contentdiv {width: 200px;height: 315px;}
</style>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script language="JavaScript">
	var code="<?=$code?>";
	function CodeProcessFun(_code) {
	
		if(_code=="out" || _code.length==0 || _code=="000000000000") {
		
			document.all["code_top"].style.background="#dddddd";
			selcode="";
			seltype="";

			if(_code!="out") {
				BodyInit('');
			} else {
				_code="";
			}
		} else {
			document.all["code_top"].style.background="#ffffff";
			BodyInit(_code);
		}

		if(selcode.length==12 && selcode!="000000000000") {
			document.form2.mode.value="";
			document.form2.code.value=selcode;
			document.form2.target="ListFrame";
			document.form2.action="mobile_product_selected_list.php";
			document.form2.submit();
			document.form2.target="ifrm_ctrl";

			if(document.ifrm_ctrl.form1){
				document.form2.Scrolltype.value = document.ifrm_ctrl.form1.Scrolltype.value;
			}
			document.form2.submit();
		} else {
			document.form2.mode.value="";
			document.form2.code.value="";
			document.form2.target="ListFrame";
			document.form2.action="mobile_product_selected_list.php";
			document.form2.submit();
			document.form2.target="ifrm_ctrl";

			if(document.ifrm_ctrl.form1){
				document.form2.Scrolltype.value = document.ifrm_ctrl.form1.Scrolltype.value;
			}
			document.form2.submit();
		}
	}

	var allopen=false;

	function AllOpen() {
		display="show";
		open1="open";

		if(allopen) {
			display="none";
			open1="close";
			allopen=false;
		} else {
			allopen=true;
		}
		for(i=0;i<all_list.length;i++) {
			if(display=="none" && all_list[i].codeA==selcode.substring(0,3)) {
				all_list[i].selected=true;
				selcode=all_list[i].codeA+all_list[i].codeB+all_list[i].codeC+all_list[i].codeD;
				seltype=all_list[i].type;
			}
			all_list[i].display=display;
			all_list[i].open=open1;
			for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {

				if(display=="none") {
					all_list[i].ArrCodeB[ii].selected=false;
				}
				all_list[i].ArrCodeB[ii].display=display;
				all_list[i].ArrCodeB[ii].open=open1;

				for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
					if(display=="none") {
						all_list[i].ArrCodeB[ii].ArrCodeC[iii].selected=false;
					}

					all_list[i].ArrCodeB[ii].ArrCodeC[iii].display=display;
					all_list[i].ArrCodeB[ii].ArrCodeC[iii].open=open1;

					for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
						if(display=="none") {
							all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].selected=false;
						}
						all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].display=display;
						all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].open=open1;
					}
				}
			}
		}
		BodyInit('');
	}

	var divLeft=0;
	var defaultLeft=0;
	var timeOffset=0;
	var setTObj;
	var divName="";
	var zValue=0;

	function divMove(){
		divLeft+=timeOffset;

		if(divLeft >= defaultLeft){
			divLeft=defaultLeft;
			divName.style.left=divLeft;
			divName.style.zIndex = zValue;
			clearTimeout(setTObj);
			setTObj="";
		}else{
			timeOffset+=20;
			divName.style.left=divLeft;
			setTObj=setTimeout('divMove();',5);
		}
	}

	function divAction(arg1,arg2){
		if(zValue != arg2 && !setTObj){
			defaultLeft = arg1.offsetLeft;
			divLeft = defaultLeft;
			zValue = arg2;
			divName = arg1;
			timeOffset = -70;
			divMove();
		}
	}


</script>
<form name=form2 action="" method=get>
	<input type=hidden name=mode>
	<input type=hidden name=code>
	<input type=hidden name=Scrolltype>
</form>
<iframe name="ifrm_ctrl" width=0 height=0 frameborder=0 align=top scrolling="no" marginheight="0" marginwidth="0"></iframe>
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
														<td height="28" class="link" align="left" background="images/con_link_bg.gif">
															<img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 모바일샵 &gt;<span class="2depth_select">모바일 상품관리</span>
														</td>
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
													<tr>
														<td height="8"></td>
													</tr>
													<tr>
														<td>
															<table width="100%" border=0 cellpadding=0 cellspacing=0>
																<tr>
																	<td><img src="images/mobile_product_list_title.gif" border="0"></td>
																</tr>
																<tr>
																	<td width="100%" background="images/title_bg.gif" height="21"></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="3"></td>
													</tr>
													<tr>
														<td style="padding-bottom:3pt;">
															<table width="100%" border=0 cellpadding=0 cellspacing=0>
																<tr>
																	<td><img src="images/distribute_01.gif"></td>
																	<td COLSPAN=2 background="images/distribute_02.gif"></td>
																	<td><img src="images/distribute_03.gif"></td>
																</tr>
																<tr>
																	<td background="images/distribute_04.gif"></td>
																	<td class="notice_blue"><img src="images/distribute_img.gif" ></td>
																	<td width="100%" class="notice_blue">모바일쇼핑몰에 상품을 진열 하실수 있습니다.</td>
																	<td background="images/distribute_07.gif"></td>
																</tr>
																	<tr>
																	<td><img src="images/distribute_08.gif"></td>
																	<td COLSPAN=2 background="images/distribute_09.gif"></td>
																	<td><img src="images/distribute_10.gif"></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td>
															<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=get>
																<table cellpadding="0" cellspacing="0" width="100%" height="551" id="main_process" style="display:none">
																	<tr>
																		<td valign="top">
																			<div onmouseover="document.getElementById('cateidx').style.zIndex=2;" id="cateidx" style="position:absolute;z-index:0;width:242px;bgcolor:#FFFFFF;">
																				<table cellpadding="0" cellspacing="0" width="100%" height="511">
																					<tr>
																						<td width="232" height="100%" valign="top" background="images/category_boxbg.gif">
																							<table cellpadding="0" cellspacing="0" width="242" height="100%">
																								<tr>
																									<td bgcolor="#FFFFFF"><img src="images/product_totoacategory_title.gif" border="0"></td>
																								</tr>
																								<tr>
																									<td><img src="images/category_box1.gif" border="0"></td>
																								</tr>
																								<tr>
																									<td bgcolor="#0F8FCB" style="padding:2;padding-left:4">
																										<button title="전체 트리확장" id="btn_treeall" class="btn" onmouseover="if(this.className=='btn'){this.className='btnOver'}" onmouseout="if(this.className=='btnOver'){this.className='btn'}" unselectable="on" onclick="AllOpen();">
																											<img src="images/category_btn1.gif" width=22 height=23 border="0">
																										</button>
																									</td>
																								</tr>
																								<tr>
																									<td bgcolor="#0F8FCB" style="padding-top:4pt; padding-bottom:6pt;"></td>
																								</tr>
																								<tr>
																									<td><img src="images/category_box2.gif" border="0"></td>
																								</tr>
																								<tr>
																									<td width="100%" height="100%" align=center valign=top style="padding-left:5px;padding-right:5px;">
																										<div class=MsgrScroller id=contentdiv style="width:99%;height:100%;OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
																											<div id=bodyList>
																												<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor=FFFFFF>
																													<tr>
																														<td height=18>
																															<img src="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('out');">최상위 카테고리</span>
																														</td>
																													</tr>
																													<tr>
																														<!-- 상품카테고리 목록 -->
																														<td id="code_list" nowrap valign=top></td>
																														<!-- 상품카테고리 목록 끝 -->
																													</tr>
																												</table>
																											</div>
																										</div>
																									</td>
																								</tr>
																							</table>
																						</td>
																					</tr>
																					<tr>
																						<td><img src="images/category_boxdown.gif" border="0"></td>
																					</tr>
																				</table>
																			</div>
																		</td>
																		<td style="padding-left:84px;"></td>
																		<td width="100%" valign="top" height="100%" onmouseover="document.getElementById('cateidx').style.zIndex=0;">
																			<!--<A name="linkanchor">-->
																				<div style="position:relative;z-index:1;width:100%;height:100%;bgcolor:#FFFFFF;">
																					<iframe name="ListFrame" src="mobile_product_selected_list.php" width=100% height=551 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></iframe>
																				</div>
																		</td>
																	</tr>
																</table>
																<input type=hidden name=mode value="<?=$mode?>">
																<input type=hidden name=code>
															</form>
														</td>
													</tr>
													<tr>
														<td align="center" width="100%" valign="top" height="100%"></td>
													</tr>
													<tr>
														<td height="20"></td>
													</tr>
													<tr>
														<td height="20"></td>
													</tr>
													<tr>
														<td>
															<table width="100%" border=0 cellpadding=0 cellspacing=0>
																<tr>
																	<td><img src="images/manual_top1.gif" width=15 height=45 alt=""></td>
																	<td><img src="images/manual_title.gif" width=113 height=45 alt=""></td>
																	<td width="100%" background="images/manual_bg.gif"></td>
																	<td background="images/manual_bg.gif"></td>
																	<td><img src="images/manual_top2.gif" width=18 height=45 alt=""></td>
																</tr>
																<tr>
																	<td background="images/manual_left1.gif"></td>
																	<td COLSPAN=3 width="100%" valign="top" class="menual_bg" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
																		<table cellpadding="0" cellspacing="0" width="100%">
																			<col width=20></col>
																			<col width=></col>
																			<tr>
																				<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																				<td><span class="font_dotline">모바일샵 상품 진열 설정</span></td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top" style="letter-spacing:-0.5pt;">
																					- "소셜 및 공동구매" 상품 진열은 모바일샵에서 지원되지 않습니다. <br/>
																					- 기본적으로 모바일샵의 경우 등록된 상품을 단순 진열 설정만 가능합니다(별도 등록불가)<br/>
																					- 모바일샵의 경우 등록시 설정한 아이콘은 노출되지 않습니다 (예 : <img src="../images/common/icon01.gif"/>,<img src="../images/common/icon17.gif"/>  등 )<br/>
																					- 모바일샵의 경우 PC버전에서 지원되는 신상품,인기상품,추천상품,특별상품 기능은 제공되지 않습니다.<br/>
																				</td>
																				
																			</tr>
																			<tr>
																				<td width="20" align="right">&nbsp;</td>
																				<td  class="space_top">&nbsp; </td>
																			</tr>
																			<tr>
																				<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																				<td><span class="font_dotline">모바일샵 진열된 상품 보기</span></td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top" style="letter-spacing:-0.5pt;">
																					- "전체카테고리 목록에서 원하는 카테고리를 선택하시면 해당 카테고리에 진열된 상품을 확인 하실 수 있습니다.<br/>
																				</td>

																			</tr>
																			<tr>
																				<td width="20" align="right">&nbsp;</td>
																				<td  class="space_top">&nbsp; </td>
																			</tr>
																			<tr>
																				<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																				<td><span class="font_dotline">모바일샵 진열된 상품 진열</span></td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top" style="letter-spacing:-0.5pt;">
																					- 카테고리 노출 설정 메뉴에서 모바일샵 노출을 하지 않으신 경우 상품 진열 설정이 불가능합니다.<br/>
																					- "전체카테고리 목록" 에서 진열을 원하는 카테고리를 먼저 선택 하신 뒤 우측 "등록된 상품목록" 메뉴에서 "추가등록하기" 버튼을 클릭하시면 상품을 진열 할수 있는 팝업(이하 모바일 쇼핑몰 상품관리 창)이 나타납니다.<br/>
																					- "모바일 쇼핑몰 상품관리 창"에서 검색된 상품중 진열을 원하는 상품을 클릭하시면 진열 됩니다.<br/>
																					- "모바일 쇼핑몰 상품관리 창"에서 "[모바일샵에 출력중]"인 상품의 경우 이미 노출설정된 상품이므로 클릭 하시더라도 동일상품 하나만 진열됩니다</br>
																					- "모바일 쇼핑몰 상품관리 창"에서 다른 카테고리 상품을 검색하여 진열하실 경우 검색된 해당 카테고리에 상품이 진열됩니다.(기본적으로 해당 카테고리에 등록된 상품만 진열 가능합니다)</br>
																				</td>
																			</tr>
																			<tr>
																				<td width="20" align="right">&nbsp;</td>
																				<td  class="space_top">&nbsp; </td>
																			</tr>
																			<tr>
																				<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																				<td><span class="font_dotline">모바일샵 진열된 상품 제거</span></td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top" style="letter-spacing:-0.5pt;">
																					- "전체카테고리 목록"에서 설정을 원하는 카테고리 선택후 우측 "등록된 상품 목록" 메뉴에서 해당 상품의 "삭제" 버튼을 클릭하시면 되며 단순히 모바일 샵 진열상태만 제거되므로 등록상품이 삭제되지는 않습니다.
																				</td>
																				<tr>
																					<td width="20" align="right">&nbsp;</td>
																					<td  class="space_top">&nbsp; </td>
																				</tr>
																			</tr>
																			<tr>
																				<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																				<td><span class="font_dotline">모바일샵 진열된 상품정보 변경</span></td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top" style="letter-spacing:-0.5pt;">
																					- "전체카테고리 목록"에서 설정을 원하는 카테고리 선택후 우측 "등록된 상품 목록" 메뉴에서 해당 상품의 "새창" 버튼을 클릭하시면 나타나는 팝업(이하 상품 등록/수정/삭제 창)에서 상품 정보를 변경하실 수 있습니다.<br/>
																					- "상품 등록/수정/삭제 창" 에서 상품정보를 변경하실 경우 전체쇼핑몰에서 해당 상품이 수정되므로 주의 하시기 바랍니다.<br/>
																					- "상품 등록/수정/삭제 창" 에서 상품을 삭제하실 경우 전체쇼핑몰에서 해당 상품이 제거되므로 정보변경시 주의 하시기 바랍니다.<br/>
																				</td>
																			</tr>
																		</table>
																	</td>
																	<td background="images/manual_right1.gif"></td>
																</tr>
																<tr>
																	<td><img src="images/manual_left2.gif" width=15 height=8 alt=""></td>
																	<td COLSPAN=3 background="images/manual_down.gif"></td>
																	<td><img src="images/manual_right2.gif" width=18 height=8 alt=""></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="50"></td>
													</tr>
												</table>
											</td>
											<td width="16" background="images/con_t_02_bg.gif"></td>
										</tr>
										<tr>
											<td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
											<td background="images/con_t_04_bg.gif"></td>
											<td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
										</tr>
										<tr>
											<td height="20"></td>
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
<iframe name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></iframe>
<?
if($row[use_same_product_image]=="Y"){
?>
	<script>
		document.getElementById("main_process").style.display = 'none';</script>
<?
}else{
?>
	<script>
		document.getElementById("main_process").style.display = 'block';</script>
<?
}

if($row[use_same_product_code]=="Y"){
	$sql = "SELECT * FROM tblproductcode WHERE substr(type,1,1) != 'X' AND substr(type,1,1) != 'S' ORDER BY sequence DESC";
}else{
	$sql = "SELECT * FROM tblproductcode where substr(type,1,1) != 'X' AND substr(type,1,1) != 'S' AND mobile_display = 'Y' ORDER BY sequence DESC ";
}
include ("codeinit.php");
?>
<? include "copyright.php"; ?>