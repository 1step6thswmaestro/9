<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-5";
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
		design=sel_detail_type;
		if(sel_detail_type.length==6 && sel_detail_type.substring(5,6)=="U") {
			design="DU";
		}
		document.all["preview_img"].src="images/product/"+design+".gif";
		document.all["preview_img"].style.display="";

		document.form2.mode.value="";
		document.form2.type.value=seltype;
		document.form2.code.value=selcode;
		document.form2.detail_type.value=sel_detail_type;
		document.form2.target="MainPrdtFrame";
		document.form2.action="design_eachpdetail.list.php";
		document.form2.submit();
	} else {	//카테고리 선택시 현재 사용중인 디자인 및 디자인 목록 초기화
		document.all["preview_img"].src="";
		document.all["preview_img"].style.display="none";

		document.form2.mode.value="";
		document.form2.type.value="";
		document.form2.code.value="";
		document.form2.detail_type.value="";
		document.form2.target="MainPrdtFrame";
		document.form2.action="design_eachpdetail.list.php";
		document.form2.submit();
	}
}

function ModifyCodeDesign(_code,detail_type,is_design) {
	codeA=_code.substring(0,3);
	codeB=_code.substring(3,6);
	codeC=_code.substring(6,9);
	codeD=_code.substring(9,12);
	for(i=0;i<all_list.length;i++) {
		if(codeA!="000" && codeB=="000" && codeC=="000" && codeD=="000") {
			if(all_list[i].code==_code) {
				all_list[i].detail_type=detail_type;
				if(is_design==1) {
					for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
						all_list[i].ArrCodeB[ii].detail_type=detail_type;
						for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
							all_list[i].ArrCodeB[ii].ArrCodeC[iii].detail_type=detail_type;
							for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].detail_type=detail_type;
							}
						}
					}
				}
				ChangeSelect(_code);
				return;
			}
		} else {
			for(ii=0;ii<all_list[i].ArrCodeB.length;ii++) {
				if(codeA!="000" && codeB!="000" && codeC=="000" && codeD=="000") {
					if(all_list[i].ArrCodeB[ii].code==_code) {
						all_list[i].ArrCodeB[ii].detail_type=detail_type;
						if(is_design==1) {
							for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].detail_type=detail_type;
								for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
									all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].detail_type=detail_type;
								}
							}
						}
						ChangeSelect(_code);
						return;
					}
				} else {
					for(iii=0;iii<all_list[i].ArrCodeB[ii].ArrCodeC.length;iii++) {
						if(codeA!="000" && codeB!="000" && codeC!="000" && codeD=="000") {
							if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].code==_code) {
								all_list[i].ArrCodeB[ii].ArrCodeC[iii].detail_type=detail_type;
								if(is_design==1) {
									for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].detail_type=detail_type;
									}
								}
								ChangeSelect(_code);
								return;
							}
						} else {
							for(iiii=0;iiii<all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD.length;iiii++) {
								if(codeA!="000" && codeB!="000" && codeC!="000" && codeD!="000") {
									if(all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].code==_code) {
										all_list[i].ArrCodeB[ii].ArrCodeC[iii].ArrCodeD[iiii].detail_type=detail_type;
										ChangeSelect(_code);
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt; <span class="2depth_select">상품상세 화면꾸미기</span></td>
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
					<TD><IMG SRC="images/design_productdetail_title.gif" ALT=""></TD>
					</tr>
<tr>
<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
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
					<TD width="100%" class="notice_blue"><p>상품상세 화면 디자인을 자유롭게 디자인 하실 수 있습니다.</p></TD>
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
					<TD><IMG SRC="images/design_productdetail_stitle.gif" WIDTH="250" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
			<tr>
				<td style="padding-top:3pt;">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" colspan="3"></TD>
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
					<TD class="table_cell1" align="center" background="images/blueline_bg.gif"><p><b><font color="#555555">현재 상품 카테고리별 템플릿</span></b></p></TD>
				</TR>
				<TR>
					<TD colspan="3" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD align="center" valign="top">
					<DIV class=MsgrScroller id=contentDiv style="OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
					<DIV id=bodyList>
					<table border=0 cellpadding=0 cellspacing=0 width="100%">
					<tr>
						<td height=18><IMG SRC="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('out');">모든 상품상세보기 일괄 개별디자인</span></td>
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
					<TD class="td_con1" align="center"  style="padding:5pt;" style="letter-spacing:-0.5pt;"><img id="preview_img" style="display:none" border="0" vspace="5" class="imgline"><br><p align="left"><b>&quot;모든 상품상세보기 일괄 개별디자인&quot; </b>을 적용할 경우 개별 디자인 사용중인 카테고리를 제외한 템플릿을 사용하는 모든 카테고리가 개별디자인으로 일괄 변경됩니다.</p></TD>
				</TR>
				<TR>
					<TD background="images/table_top_line.gif" colspan="3"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td><IFRAME name="MainPrdtFrame" src="design_eachpdetail.list.php" width=100% height=350 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
			</tr>
			</form>
			<form name=form2 action="" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
			<input type=hidden name=type>
			<input type=hidden name=detail_type>
			</form>
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

<?
$sql = "SELECT * FROM tblproductcode WHERE type not in('S','SX','SM','SMX') ORDER BY sequence DESC ";
include ("codeinit.php");
?>

<? INCLUDE "copyright.php"; ?>