<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	INCLUDE ("access.php");

	####################### 페이지 접근권한 check ###############
	$PageCode = "mo-1";
	$MenuCode = "mobile";
	if (!$_usersession->isAllowedTask($PageCode)) {
		INCLUDE ("AccessDeny.inc.php");
		exit;
	}
	#########################################################

	$mode = $_GET[mode];

	if($mode=="show") {
		mysql_query("update tblproductcode set mobile_display ='Y' where codeA = '$codeA' and codeB = '$codeB' and codeC = '$codeC' and codeD = '$codeD'");
	} else if ($mode=="hidden")	{
		mysql_query("update tblproductcode set mobile_display ='N' where codeA = '$codeA' and codeB = '$codeB' and codeC = '$codeC' and codeD = '$codeD'");
	}

	$result = mysql_query("select use_same_product_code from tblmobileconfig");
	$row = mysql_fetch_array($result);
?>

<? INCLUDE "header.php"; ?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script language="JavaScript">
	var code="<?=$code?>";
	var allopen=false;
	var movecode=false;

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

		SetButton();
	}

	function ViewProperty() {
		if(selcode.length==0 || selcode=="000000000000") {
			return;
		}
		document.form1.code.value=selcode;
		document.form1.parentcode.value="";
		document.form1.mode.value="modify";
		document.form1.action="mobile_product_code.property.php";
		document.form1.target="PropertyFrame";
		document.form1.submit();
	}

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

	function SetButton() {
		document.all["btn_property"].disabled=false;
		document.all["btn_property"].className="btn";

		if(selcode.length==0 || selcode=="000000000000") {
			document.all["btn_property"].disabled=true;
			document.all["btn_property"].className="btnNA";
		}
	}

	var divLeft=0;
	var defaultLeft=0;
	var timeOffset=0;
	var setTObj;
	var divName="";
	var zValue=0;

	function divMove() {
		divLeft+=timeOffset;

		if(divLeft >= defaultLeft) {
			divLeft=defaultLeft;
			divName.style.left=divLeft;
			divName.style.zIndex = zValue;
			clearTimeout(setTObj);
			setTObj="";
		} else {
			timeOffset+=20;
			divName.style.left=divLeft;
			setTObj=setTimeout('divMove();',5);
		}
	}

	function divAction(arg1,arg2) {
		if(zValue != arg2 && !setTObj) {
			defaultLeft = arg1.offsetLeft;
			divLeft = defaultLeft;
			zValue = arg2;
			divName = arg1;
			timeOffset = -70;
			divMove();
		}
	}
</script>
<style type=text/css>
	#menuBar {}
	#contentDiv {width: 220;HEIGHT: 320;}
</style>

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
														<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 모바일샵 &gt; <span class="2depth_select">카테고리노출 설정</span>
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
												<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
													<tr>
														<td height="8"></td>
													</tr>
													<tr>
														<td>
															<table width="100%" border=0 cellpadding=0 cellspacing=0>
																<tr>
																	<td><img src="images/mobile_product_code_title.gif" alt=""></td>
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
																	<td width="100%" class="notice_blue">모바일 사이트의 카테고리 노출여부를 설정하실 수 있습니다.</td>
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
															<form name=form3 action="mobile_product_ctrl.php" method="get" target="ifrm_ctrl" style="padding-top:10px">
																<input type="hidden" name="mode" value="use_same_product_code">
																<table cellSpacing=0 cellPadding=0 width="100%" border=0>
																	<tr>
																		<td background="images/table_top_line.gif" colspan=2></td>
																	</tr>
																	<tr>
																		<td class="table_cell" width="170">
																			<img src="images/icon_point2.gif" width="8" height="11" border="0">모바일샵 카테고리 설정<?=$row[use_same_product_code]?>
																		</td>
																		<td class="td_con1" >
																			<table  border="0" cellpadding="0" cellspacing="0">
																				<tr>
																					<td class="td_con1b">
																						<input type="radio" name="use_same_product_code" value="Y" <? if($row[use_same_product_code]=="Y") {	echo "checked";}?> >쇼핑몰 설정과 같이&nbsp;&nbsp;
																						<input type="radio" name="use_same_product_code" value="N" <? if($row[use_same_product_code]=="N") {	echo "checked";}?>>모바일샵 별도 설정&nbsp;&nbsp;
																						<input type="submit" value="확인">
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																	<tr>
																		<td background="images/table_top_line.gif" colspan=2></td>
																	</tr>
																</table>
															</form>
														</td>
													</tr>
													<tr>
														<td>
															<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post >
																<input type=hidden name=mode value="<?=$mode?>">
																<input type=hidden name=code>
																<input type=hidden name=codes>
																<input type=hidden name=parentcode>
																<table cellpadding="0" cellspacing="0" width="100%" height="910" id="main_process" style="display:none">
																	<tr>
																		<td valign="top">
																			<div onmouseover="" id="cateidx" style="position:absolute;z-index:0;width:242px;bgcolor:#FFFFFF;">
																				<table cellpadding="0" cellspacing="0" width="100%" height="870">
																					<tr>
																						<td width="100%" height="100%" valign="top" background="images/category_boxbg.gif">
																							<table cellpadding="0" cellspacing="0" width="100%" height="100%">
																								<tr>
																									<td bgcolor="#FFFFFF"><img src="images/product_totoacategory_title.gif" width=85 HEIGHT=24 ALT=""></td>
																								</tr>
																								<tr>
																									<td><img src="images/category_box1.gif" border="0"></td>
																								</tr>
																								<tr>
																									<td bgcolor="#0F8FCB" style="padding-top:4pt; padding-bottom:6pt;padding-left:10px">
																										<table align="left" cellpadding="0" cellspacing="0" width="50">
																											<tr>
																												<td width="24">
																													<button title="전체 트리확장" id="btn_treeall" class="btn" onmouseover="if(this.className=='btn'){this.className='btnOver'}" onmouseout="if(this.className=='btnOver'){this.className='btn'}" unselectable="on" onclick="AllOpen();">
																														<img src="images/category_btn1.gif" width=22 HEIGHT=23 border="0">
																													</button>
																												</td>
																												<td width="24">
																													<button title="선택된 카테고리속성 보기" id="btn_property" class="btn" onmouseover="if(this.className=='btn'){this.className='btnOver'}" onmouseout="if(this.className=='btnOver'){this.className='btn'}" unselectable="on" onclick="ViewProperty();">
																														<img src="images/category_btn2.gif" width=22 HEIGHT=23 border="0">
																													</button>
																												</td>
																											</tr>
																										</table>
																									</td>
																								</tr>
																								<tr>
																									<td><img src="images/category_box2.gif" border="0"></td>
																								</tr>
																								<tr>
																									<td width="100%" height="100%" align=center valign=top style="padding-left:5px;padding-right:5px;">
																										<div class=MsgrScroller id=contentDiv style="width:99%;height:100%;OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
																											<div id=bodyList>
																												<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor=FFFFFF>
																													<tr>
																														<td height=18>
																															<img src="images/directory_root.gif" border=0 align=absmiddle><span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('out');">최상위 카테고리</span>
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
																		<td width="100%" valign="top" style="padding-left:165px" height="100%" onmouseover="divAction(document.getElementById('cateidx'),0);">
																			<div style="position:relative;z-index:1;width:100%;height:100%;bgcolor:#FFFFFF;">
																				<iframe name="PropertyFrame" src="mobile_product_code.property.php" width=100% height=840 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></iframe>
																			</div>
																		</td>
																	</tr>
																	<iframe name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></iframe>
																</table>
															</form>
														</td>
													</tr>
													<tr>
														<td height=20></td>
													</tr>
													<tr>
														<td>
															<table width="100%" border=0 cellpadding=0 cellspacing=0>
																<tr>
																	<td><img src="images/manual_top1.gif" width=15 HEIGHT=45 ALT=""></td>
																	<td><img src="images/manual_title.gif" width=113 HEIGHT=45 ALT=""></td>
																	<td width="100%" background="images/manual_bg.gif"></td>
																	<td background="images/manual_bg.gif"></td>
																	<td><img src="images/manual_top2.gif" width=18 HEIGHT=45 ALT=""></td>
																</tr>
																<tr>
																	<td background="images/manual_left1.gif"></td>
																	<td COLSPAN=3 width="100%" valign="top" class="menual_bg" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
																		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
																			<col width=20></col>
																			<col width=></col>
																			<tr>
																				<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																				<td><span class="font_dotline">카테고리 노출 설정</span></td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top" style="letter-spacing:-0.5pt;">
																					- "소셜 및 공동구매" 카테고리의 경우 모바일샵에서 지원되지 않습니다.<br/>
																					- "쇼핑몰 설정과 같이" 설정의 경우 PC버전과 동일하게 노출됩니다.<br/>
																					- "모바일샵 별도 설정" 설정의 경우 모바일샵과 따로 관리 할 수 있으며, 전체 카테고리에서 노출설정을 원하는 카테고리를 선택후 좌측 메뉴 상단의 톱늬바퀴 버튼을 클릭하시면 나타나는 좌측메뉴(카테고리 속성) 에서 설정 하실 수 있습니다.<br/>
																					
																					<!-- <font class=font_orange>- 매뉴얼 내용</font> -->
																				</td>
																			</tr>
																			<tr>
																				<td colspan="2" height="20"></td>
																			</tr>
																		</table>
																	</td>
																	<td background="images/manual_right1.gif"></td>
																</tr>
																<tr>
																	<td><img src="images/manual_left2.gif" width=15 height=8 alt=""></td>
																	<td colspan=3 background="images/manual_down.gif"></td>
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
<iframe src="" name="ifrm_ctrl" frameborder="0" width="0" height="0" marginwidth="0" marginheight="0" topmargin="0" scrolling="no"></iframe>
<?
if($row[use_same_product_code]=="Y")
{
?><script>

document.getElementById("main_process").style.display = 'none';</script><?
}
else
{
?><script>
document.getElementById("main_process").style.display = 'block';</script><?
}
?>


<?
$sql = "SELECT * FROM tblproductcode WHERE substr(type,1,1) != 'X' AND substr(type,1,1) != 'S' ORDER BY sequence DESC ";
include ("codeinit.php");
?>

<?=$onload?>
<? INCLUDE "copyright.php"; ?>