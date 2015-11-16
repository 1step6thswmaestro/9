<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-2";
$MenuCode = "design";
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
		sel_list_type="";
		sel_detail_type="";

		if(_code!="out") {
			BodyInit('');
		} else {
			_code="";
		}
	} else {
		document.all["code_top"].style.background="#ffffff";
		BodyInit(_code);
	}

	if(selcode.length==12 && selcode!="000000000000") {	//카테고리 선택시 현재 사용중인 디자인 및 디자인 목록 새로고침
		design=sel_list_type;
		if(sel_list_type.length==6 && sel_list_type.substring(5,6)=="U") {
			design="LU";
		}
		document.all["preview_img"].src="images/product/"+design+".gif";
		document.all["preview_img"].style.display="";

		document.form2.mode.value="";
		document.form2.type.value=seltype;
		document.form2.code.value=selcode;
		document.form2.list_type.value=sel_list_type;
		document.form2.target="MainPrdtFrame";
		document.form2.action="design_plist.list.php";
		document.form2.submit();
	} else {	//카테고리 선택시 현재 사용중인 디자인 및 디자인 목록 초기화
		document.all["preview_img"].src="";
		document.all["preview_img"].style.display="none";

		document.form2.mode.value="";
		document.form2.type.value="";
		document.form2.code.value="";
		document.form2.list_type.value="";
		document.form2.target="MainPrdtFrame";
		document.form2.action="design_plist.list.php";
		document.form2.submit();
	}
}

function ModifyCodeDesign(_code,list_type,is_design) {
	codeA=_code.substring(0,3);
	codeB=_code.substring(3,6);
	codeC=_code.substring(6,9);
	codeD=_code.substring(9,12);
	for(i=0;i<all_list.length;i++) {
		if(codeA!="000" && codeB=="000" && codeC=="000" && codeD=="000") {
			if(all_list[i].code==_code) {
				all_list[i].list_type=list_type;
				if(list_type.substring(0,1)=="B") { gonggustr = "(공구형)"; }
				else { gonggustr = ""; }
				document.getElementById("span_"+all_list[i].code).innerHTML=all_list[i].code_name+gonggustr;
				if(is_design==1) {
					for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
						all_list[i].ArrCodeB[ii].list_type=list_type;
						document.getElementById("span_"+all_list[i].ArrCodeB[ii].code).innerHTML=all_list[i].ArrCodeB[ii].code_name+gonggustr;
						for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
							all_list[i].ArrCodeB[ii].ArrCodeC[iii].list_type=list_type;
							document.getElementById("span_"+all_list[i].ArrCodeB[ii].ArrCodeC[iii].code).innerHTML=all_list[i].ArrCodeB[ii].ArrCodeC[iii].code_name+gonggustr;
							for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].list_type=list_type;
								document.getElementById("span_"+all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code).innerHTML=all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code_name+gonggustr;
							}
						}
					}
				}
				return;
			}
		} else {
			for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
				if(codeA!="000" && codeB!="000" && codeC=="000" && codeD=="000") {
					if(all_list[i].ArrCodeB[ii].code==_code) {
						all_list[i].ArrCodeB[ii].list_type=list_type;
						if(list_type.substring(0,1)=="B") { gonggustr = "(공구형)"; }
						else { gonggustr = ""; }
						document.getElementById("span_"+all_list[i].ArrCodeB[ii].code).innerHTML=all_list[i].ArrCodeB[ii].code_name+gonggustr;
						if(is_design==1) {
							for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].list_type=list_type;
								document.getElementById("span_"+all_list[i].ArrCodeB[ii].ArrCodeC[iii].code).innerHTML=all_list[i].ArrCodeB[ii].ArrCodeC[iii].code_name+gonggustr;
								for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
									all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].list_type=list_type;
									document.getElementById("span_"+all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code).innerHTML=all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code_name+gonggustr;
								}
							}
						}
						return;
					}
				} else {
					for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
						if(codeA!="000" && codeB!="000" && codeC!="000" && codeD=="000") {
							if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].code==_code) {
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].list_type=list_type;
								if(list_type.substring(0,1)=="B") { gonggustr = "(공구형)"; }
								else { gonggustr = ""; }
								document.getElementById("span_"+all_list[i].ArrCodeB[ii].ArrCodeC[iii].code).innerHTML=all_list[i].ArrCodeB[ii].ArrCodeC[iii].code_name+gonggustr;
								if(is_design==1) {
									for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].list_type=list_type;
										document.getElementById("span_"+all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code).innerHTML=all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code_name+gonggustr;
									}
								}
								return;
							}
						} else {
							for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
								if(codeA!="000" && codeB!="000" && codeC!="000" && codeD!="000") {
									if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code==_code) {
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].list_type=list_type;
										if(list_type.substring(0,1)=="B") { gonggustr = "(공구형)"; }
										else { gonggustr = ""; }
										document.getElementById("span_"+all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code).innerHTML=all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code_name+gonggustr;
										return;
									}
								}
							}
						}
					}
				}
			}
		}
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

function CheckForm() {
	if(confirm("선택하신 디자인으로 변경하시겠습니까?")) {
		document.form1.type.value="update";
		document.form1.submit();
	}
}

function design_preview(design) {
	document.all["preview_img"].src="images/product/"+design+".gif";
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; 템플릿-메인 및 카테고리 &gt; <span class="2depth_select">상품 카테고리 텔플릿</span></td>
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
					<TD><IMG SRC="images/design_plist.list_title.gif"  ALT=""></TD>
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
					<TD width="100%" class="notice_blue"><p>쇼핑몰 카테고리 화면 디자인을 선택하여 사용하실 수 있습니다.</p></TD>
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
					<TD><IMG SRC="images/design_plist_stitle1.gif" WIDTH="250" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
			<tr>
				<td style="padding-top:3pt;">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" width="760" colspan="3"></TD>
				</TR>
				<TR>
					<TD class="table_cell" align="center">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<col width=30></col>
					<col width=></col>
					<tr>
						<td><button title="전체 트리확장" id="btn_treeall" class="btn" onmouseover="if(this.className=='btn'){this.className='btnOver'}" onmouseout="if(this.className=='btnOver'){this.className='btn'}" unselectable="on" onclick="AllOpen();"><IMG SRC="images/category_btn1.gif" WIDTH=22 HEIGHT=23 border="0"></button></td>
						<td align=center style="padding-right:30"><b>전체 카테고리</b></td>
					</tr>
					</table>
					</TD>
					<TD class="table_cell1" align="center" width="50">&nbsp;</TD>
					<TD class="table_cell1" align="center" background="images/blueline_bg.gif"><p><span class="font_blue">현재 상품 카테고리별 템플릿</span></p></TD>
				</TR>
				<TR>
					<TD colspan="3" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD align="center" valign="top" style="padding:3pt;">
					<DIV class=MsgrScroller id=contentDiv style="OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
					<DIV id=bodyList>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td height=18><IMG SRC="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('out');">상품 카테고리를 선택하세요</span></td>
					</tr>
					<tr>
						<!-- 상품카테고리 목록 -->
						<td id="code_list" nowrap></td>
						<!-- 상품카테고리 목록 끝 -->
					</tr>
					</table>
					</DIV>
					</DIV>
					</TD>
					<TD class="td_con1" align="center" width="50"><img src="images/btn_next1.gif" width="25" height="25" border="0" hspace="5"></TD>
					<TD class="td_con1" align="center"  style="padding:5pt;" width="48%">&nbsp;<img id="preview_img" width="150" height="184" style="display:none" border="0" vspace="0" class="imgline"></TD>
				</TR>
				<TR>
					<TD background="images/table_top_line.gif" width="760" colspan="3"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td><IFRAME name="MainPrdtFrame" src="design_plist.list.php" width=100% height=350 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="100%"><span class="font_dotline">상품 카테고리 템플릿/관련 파일</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top">
							- <span class="font_orange">파일 수정시 기존 파일을 반드시 백업하시기 바랍니다.(파일 수정 후 발생된 문제에 대해 복구 서비스를 지원해 드리지 않습니다.)</span><br />
							- <span class="font_orange">템플릿 파일 : /templet/product/list_AL001.php</span><br />
							- <span class="font_orange">관련 파일 : /front/productlist.php, /front/productlist_text.php</span>
						</td>
					</tr>
					<tr>
						<td height="20" colspan="2"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="100%"><span class="font_dotline">템플릿 선택</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- <a href="javascript:parent.topframe.GoMenu(1,'product_code.php');"><span class="font_blue">상품관리 > 카테고리/상품관리 > 카테고리 관리</span></a> 에서 각각 템플릿을 선택할 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 두 개의 메뉴에서 동시에 템플릿 선택이 가능하며 최종 적용한 메뉴의 설정이 적용됩니다.</td>
					</tr>
					<tr>
						<td height="20" colspan="2"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="100%"><span class="font_dotline">개별 디자인</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- <a href="javascript:parent.topframe.GoMenu(2,'design_eachplist.php');"><span class="font_blue">디자인관리 > 개별디자인 - 페이지 본문 > 상품 카테고리 꾸미기</span></a> 에서 개별 디자인을 할 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 개별 디자인 사용시 템플릿은 적용되지 않습니다.</td>
					</tr>
					<tr>
						<td height="20" colspan="2"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="100%"><span class="font_dotline">템플릿 재적용</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 본 메뉴에서 원하는 템플릿으로 재선택하면 개별디자인은 해제되고 선택한 템플릿으로 적용됩니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 개별디자인에서 [기본값복원] 또는 [삭제하기] -> 기본 템플릿으로 변경됨 -> 원하는  템플릿을 선택하시면 됩니다.</td>
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
<input type=hidden name=type>
<input type=hidden name=list_type>
</form>
</table>

<?=$onload?>

<?
$sql = "SELECT * FROM tblproductcode WHERE type not in('S','SX','SM','SMX') ORDER BY sequence DESC ";
include ("codeinit.php");
?>

<? INCLUDE "copyright.php"; ?>