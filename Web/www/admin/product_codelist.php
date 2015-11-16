<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

?>

<? INCLUDE "header.php"; ?>

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
		document.form2.action="product_codelist.list.php";
		document.form2.submit();
		document.form2.target="MainPrdtFrame";
		document.form2.action="product_codelist.main.php";
		if(document.MainPrdtFrame.form1)
			document.form2.Scrolltype.value = document.MainPrdtFrame.form1.Scrolltype.value;
		document.form2.submit();
	} else {
		document.form2.mode.value="";
		document.form2.code.value="";
		document.form2.target="ListFrame";
		document.form2.action="product_codelist.list.php";
		document.form2.submit();
		document.form2.target="MainPrdtFrame";
		document.form2.action="product_codelist.main.php";
		if(document.MainPrdtFrame.form1)
			document.form2.Scrolltype.value = document.MainPrdtFrame.form1.Scrolltype.value;
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

function MainInsert() {
	max=50;
	if(document.MainPrdtFrame.form1.num.value>=max){
		alert("카테고리 진열상품은 최대 "+max+"개의 상품을 등록하실 수 있습니다.\n\n다른 상품을 삭제후 등록하세요.");
		return;
	}
	if (document.ListFrame.form1.prcode.value<=0) {
		alert("카테고리 진열상품으로 이동할 상품을 선택하세요.");
		document.location.href="#linkanchor";
		return;
	}
	var is_special = false;
	for(i=0;i<document.MainPrdtFrame.form1.special.length;i++) {
		if (document.MainPrdtFrame.form1.special[i].checked==true) {
			is_special=true;
			break;
		}
	}
	if(!is_special){
		alert("이동할 진열상품카테고리를 선택하세요.");
		document.MainPrdtFrame.form1.special[0].focus();
		return;
	}
	//if (confirm("해당 상품을 카테고리 진열상품으로 이동하시겠습니까?")){
		document.MainPrdtFrame.form1.prcode.value=document.ListFrame.form1.prcode.value;
		document.ListFrame.SelClear();
		document.MainPrdtFrame.InsertSpecial();
	//}
}

var divLeft=0;
var defaultLeft=0;
var timeOffset=0;
var setTObj;
var divName="";
var zValue=0;

function divMove()
{
	divLeft+=timeOffset;

	if(divLeft >= defaultLeft)
	{
		divLeft=defaultLeft;
		divName.style.left=divLeft;
		divName.style.zIndex = zValue;
		clearTimeout(setTObj);
		setTObj="";
	}
	else
	{
		timeOffset+=20;
		divName.style.left=divLeft;
		setTObj=setTimeout('divMove();',5);
	}
}

function divAction(arg1,arg2)
{
	if(zValue != arg2 && !setTObj)
	{
		defaultLeft = arg1.offsetLeft;
		divLeft = defaultLeft;
		zValue = arg2;
		divName = arg1;
		timeOffset = -70;
		divMove();
	}
}
</script>

<STYLE type=text/css>
	#menuBar {}
	#contentDiv {width: 200;height: 315;}
</STYLE>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%"  background="images/con_bg.gif">
							<col width="198"></col>
							<col width="10"></col>
							<col width=></col>
								<tr>
									<td valign="top"  background="images/leftmenu_bg.gif">
										<? include ("menu_product.php"); ?>
									</td>
									<td></td>
									<td valign="top">
										<table cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td height="29" colspan="3">
													<table cellpadding="0" cellspacing="0" width="100%">
														<tr>
															<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상품관리 &gt;카테고리/상품관리 &gt; <span class="2depth_select">카테고리 상품 진열관리</span>
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
														<tr><td height="8"></td></tr>
														<tr>
															<td>
																<table width="100%" border="0" cellpadding="0" cellspacing="0">
																	<tr>
																		<td><img src="images/product_codelist_title1.gif" border="0"></td>
																	</tr>
																	<tr>
																		<td width="100%" background="images/title_bg.gif" height="21"></td>
																	</tr>
																</table>
															</td>
														</tr>
														<tr><td height="3"></td></tr>
														<tr>
															<td style="padding-bottom:3pt;">
																<table width="100%" border="0" cellpadding="0" cellspacing="0">
																	<tr>
																		<td><img src="images/distribute_01.gif"></td>
																		<td colspan=2 background="images/distribute_02.gif"></td>
																		<td><img src="images/distribute_03.gif"></td>
																	</tr>
																	<tr>
																		<td background="images/distribute_04.gif"></td>
																		<td class="notice_blue"><img src="images/distribute_img.gif" ></td>
																		<td width="100%" class="notice_blue">쇼핑몰 카테고리페이지에 신상품, 인기상품, 추천상품에 진열할 상품을 등록할 수 있습니다.</td>
																		<td background="images/distribute_07.gif"></td>
																	</tr>
																	<tr>
																		<td><img src="images/distribute_08.gif"></td>
																		<td colspan=2 background="images/distribute_09.gif"></td>
																		<td><img src="images/distribute_10.gif"></td>
																	</tr>
																</table>
															</td>
														</tr>
													<tr>
														<td height="20"></td>
													</tr>
													<tr>
														<td>
															<form name="form1" action="<?=$_SERVER[PHP_SELF]?>" method="post">
																<input type="hidden" name="mode" value="<?=$mode?>">
																<input type="hidden" name="code">
																<table cellpadding="0" cellspacing="0" width="100%" height="551">
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
																										<button title="전체 트리확장" id="btn_treeall" class="btn" onmouseover="if(this.className=='btn'){this.className='btnOver'}" onmouseout="if(this.className=='btnOver'){this.className='btn'}" unselectable="on" onclick="AllOpen();"><img src="images/category_btn1.gif" width="22" height="23" border="0"></button>
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
																										<div class="MsgrScroller" id="contentDiv" style="width:99%;height:100%;OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
																											<div id="bodyList">
																												<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor="FFFFFF">
																													<tr>
																														<td height="18"><img src="images/directory_root.gif" border="0" align="absmiddle"> <span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('out');">최상위 카테고리</span></td>
																													</tr>
																													<tr>
																														<!-- 상품카테고리 목록 -->
																														<td id="code_list" nowrap valign="top"></td>
																														<!-- 상품카테고리 목록 끝 -->
																													</tr>
																												</table>
																											</div>
																										</div>
																									</td>
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
																		<td width="100%" valign="top" height="100%" onmouseover="document.getElementById('cateidx').style.zIndex=0;"><a name="linkanchor"><div style="position:relative;z-index:1;width:100%;height:100%;bgcolor:#FFFFFF;">
																		<iframe name="ListFrame" id="ListFrame" src="product_codelist.list.php" width="100%" height="551" frameborder="0" align="TOP" scrolling="no" marginheight="0" marginwidth="0"></iframe>
																		</div>
																		</td>
																	</tr>
																</table>
															</form>
														</td>
													</tr>
													<tr>
														<td height="10"></td>
													</tr>
													<tr>
														<td align="center" style="padding-left:30px"><a href="javascript:MainInsert();"><img src="images/botteon_array.gif" border="0"></a></td>
													</tr>
													<tr>
														<td height="20"></td>
													</tr>
													<tr>
														<td align="center" width="100%" valign="top" height="100%">
															<table cellpadding="0" cellspacing="0" width="100%" height="400">
																<tr>
																	<td><iframe name="MainPrdtFrame" id="MainPrdtFrame" src="product_codelist.main.php" width="100%" height="100%" frameborder="0" align="TOP" scrolling="no" marginheight="0" marginwidth="0"></iframe></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="20"></td>
													</tr>
													<tr>
														<td>
															<iframe name="HiddenFrame" src="<?=$Dir?>blank.php" width="0" height="0" frameborder="0" align="top" scrolling="no" marginheight="0" marginwidth="0"></iframe>
														</td>
													</tr>
													<tr>
														<td>
															<form name="form2" action="" method="post">
																<input type="hidden" name="mode">
																<input type="hidden" name="code">
																<input type="hidden" name="Scrolltype">
															</form>
														</td>
													</tr>
													<tr>
														<td height="20"></td>
													</tr>
													<tr>
														<td>
															<table width="100%" border=0 cellpadding=0 cellspacing=0>
																<tr>
																	<td><img src="images/manual_top1.gif" width="15" height="45" alt=""></td>
																	<td><img src="images/manual_title.gif" width="113" height="45" alt=""></td>
																	<td width="100%" background="images/manual_bg.gif"></td>
																	<td background="images/manual_bg.gif"></td>
																	<td><img src="images/manual_top2.gif" width="18" height="45" alt=""></td>
																</tr>
																<tr>
																	<td background="images/manual_left1.gif"></td>
																	<td colspan=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
																		<table cellpadding="0" cellspacing="0" width="100%">
																			<col width=20></col>
																			<col width=></col>
																			<tr>
																				<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																				<td><span class="font_dotline">카테고리 섹션별 진열상품 등록하기</span></td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top">① 카테고리 진열섹션에 등록할 카테고리 선택</td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top">② 상품목록에서 진열섹션에 등록할 상품 선택</td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top">③ 카테고리에 선택된 상품을 등록할 진열섹션 선택 후, [상품진열] 버튼 클릭</td>
																			</tr>
																			<tr>
																				<td colspan="2" height="20"></td>
																			</tr>
																			<tr>
																				<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																				<td><span class="font_dotline">카테고리 섹션별 진열시 주의사항</span></td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top">- 진열상품 순서조정을 위해 우측 버튼(삭제버튼제외)을 사용할 경우 [진열상품 순서 저장하기] 를 클릭해야만 적용됩니다.</td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top">- 진열상품 순서조정을 위해 "선택상품 순서변경"을 사용할 경우 [적용하기] 를 클릭해야만 적용됩니다.</td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top">- 카테고리 진열상품 삭제(우측삭제버튼)시 상품은 삭제되지 않으며 단지 해당 카테고리 상품진열 섹션에서 제외됩니다.</td>
																			</tr>
																			<tr>
																				<td colspan="2" height="20"></td>
																			</tr>
																			<tr>
																				<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																				<td><b><span class="font_orange">카테고리 상품진열 섹션 순서 입니다.</span></b></td>
																			</tr>
																			<tr>
																				<td align="right" valign="top">&nbsp;</td>
																				<td><img src="images/product_codelist_1_img.gif" border="0"></td>
																			</tr>
																		</table>
																	</td>
																	<td background="images/manual_right1.gif"></td>
																</tr>
																<tr>
																	<td><img src="images/manual_left2.gif" width="15" height="8" alt=""></td>
																	<td colspan=3 background="images/manual_down.gif"></td>
																	<td><img src="images/manual_right2.gif" width="18" height="8" alt=""></td>
																</tr>
															</table>
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
</table>
<?
$sql = "SELECT * FROM tblproductcode WHERE type not in('T','TX','TM','TMX','S','SX','SM','SMX') ";
include ("codeinit.php");
?>
<? INCLUDE "copyright.php"; ?>