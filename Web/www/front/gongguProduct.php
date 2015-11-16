<?php
header('Content-Type: text/html; charset=euc-kr'); 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
$mnuTab = $_POST["mnuTab"];
$mnuTab =($mnuTab=="")? "1":$mnuTab;
?>
<table cellpadding="0" cellspacing="0" width="650" align="center">
<tr>
	<td colspan="3">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="17" align="left"><IMG SRC="../images/design/pop_view_head.gif" WIDTH=17 HEIGHT=44 ALT=""></td>
				<td background="../images/design/pop_view_headbg.gif"><IMG SRC="../images/design/popgonggu_search_title.gif" WIDTH=91 HEIGHT=43 ALT=""></td>
				<td width="47" align="right"><IMG SRC="../images/design/pop_view_exit.gif" WIDTH=47 HEIGHT=44 ALT="" id="gongPrdtClose" style="cursor:pointer;"></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td background="../images/design/pop_view_leftbg.gif" width="17" height="100%" align="center"></td>
	<td width="100%"  style="padding-top:13px">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="25"><a href="javascript:;" onclick="selProductTab(1);"><IMG SRC="../images/design/popgonggu_search_tap01<?=($mnuTab==1)?"r":""?>.gif" WIDTH=129 HEIGHT=30 ALT=""></a><!-- <a href="javascript:;" onclick="selProductTab(2);"><IMG SRC="../images/design/popgonggu_search_tap02<?=($mnuTab==2)?"r":""?>.gif" WIDTH=129 HEIGHT=30 ALT=""></a> --></td>
			</tr>
			<tr>
				<td><img src="../images/design/con_line01.gif" width="615" height="2" border="0"></td>
			</tr>
			<tr>
				<td height="15"></td>
			</tr>
		</table>
		<p class="table01_con">*총 <span id="prdtSchCount">0</span>건</p>
<?
if($mnuTab=="1"){
?>
		<!-- 상품검색하기(상품 상세정보) !-->
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
				<table cellpadding="15" cellspacing="0" width="100%" bgcolor="#F7F7F7">
				<tr>
					<td>
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="100"><IMG SRC="../images/design/popgonggu_search_text01.gif" WIDTH=100 HEIGHT=15 ALT=""></td>
							<td id="prdt_ctgr1"></td>
							<td id="prdt_ctgr2"></td>
							<td id="prdt_ctgr3"></td>
							<td id="prdt_ctgr4"></td>
							<td width="80" rowspan="2" align="right"><IMG SRC="../images/design/popgonggu_search_btn01.gif" WIDTH=77 HEIGHT=47 ALT="" style="cursor:pointer" onclick="searchCheck()"></td>
						</tr>
						<tr><!-- style="cursor:pointer" onclick="searchCheck()" -->
							<td width="100"><IMG SRC="../images/design/popgonggu_search_text02.gif" WIDTH=100 HEIGHT=15 ALT="" ></td>
							<td colspan="4">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="100">
										<SELECT style="BACKGROUND-COLOR: #ebebeb;width:100%"  class="select" name="s_check" id="s_check"> 
										<option value="keyword">상품명</option>
										<option value="code">상품코드</option>
										</SELECT>
									</td>
									<td align=left><input type="text" name="search_txt" id="search_txt" class="input" style="width:320px"></td>
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
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td id="prdtList"></td>
		</tr>
		</table>
		<!-- 상품검색하기(상품 상세정보) !-->
<?
}else if($mnuTab=="2"){
?>		
		<!-- 상품검색하기(관련상품)! -->
		<!-- <table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top" align="center"><IMG SRC="../img/specialtymall_build/ex_m_sample01.gif" WIDTH=130 HEIGHT=100 ALT="" class="img"></td>
					<td width="7" valign="top" align="center">&nbsp;</td>
					<td valign="top" align="center"><IMG SRC="../img/specialtymall_build/ex_m_sample02.gif" WIDTH=130 HEIGHT=100 ALT="" class="img"></td>
					<td width="7" valign="top" align="center">&nbsp;</td>
					<td valign="top" align="center"><IMG SRC="../img/specialtymall_build/ex_m_sample03.gif" WIDTH=130 HEIGHT=100 ALT="" class="img"></td>
					<td width="7" valign="top" align="center">&nbsp;</td>
					<td valign="top" align="center"><IMG SRC="../img/specialtymall_build/ex_m_sample04.gif" WIDTH=130 HEIGHT=100 ALT="" class="img"></td>
				</tr>
				<tr>
					<td valign="top" class="table_td" align="center">
						<TABLE cellSpacing=0 cellPadding=0 width="130">
							<TR>
								<TD class="table_td"><p align="center">얼터네이트 프린트 카라 블렉 티셔츠</TD>
							</TR>
							<TR>
								<TD height=10></TD>
							</TR>
							<TR>
								<TD class=table_td><IMG border=0 align=absMiddle src="../images/design/gonggu_end_price.gif" width=34 height=17>158,000원</TD>
							</TR>
							<TR>
								<TD class=table_td><IMG border=0 align=absMiddle src="../images/design/icon_price.gif" width=34 height=17><B><FONT color=#3455de>126,000원</FONT></B></TD>
							</TR>
							<TR>
								<TD class=table_td><IMG SRC="../images/design/popgonggu_search_btn02.gif" WIDTH=56 HEIGHT=15 ALT=""></TD>
							</TR>
						</TABLE>
					</td>
					<td width="7" valign="top" align="center"><b>&nbsp;</b></td>
					<td valign="top" class="table_td" align="center">
						<TABLE cellSpacing=0 cellPadding=0 width="130">
							<TR>
								<TD class=table_td><p align="center">얼터네이트 프린트 카라 블렉 티셔츠</TD>
							</TR>
							<TR>
								<TD height=10></TD>
							</TR>
							<TR>
								<TD class=table_td><IMG border=0 align=absMiddle src="../images/design/gonggu_end_price.gif" width=34 height=17>158,000원</TD>
							</TR>
							<TR>
								<TD class=table_td><IMG border=0 align=absMiddle src="../images/design/icon_price.gif" width=34 height=17><B><FONT color=#3455de>126,000원</FONT></B></TD>
							</TR>
							<TR>
								<TD class=table_td><IMG SRC="../images/design/popgonggu_search_btn02.gif" WIDTH=56 HEIGHT=15 ALT=""></TD>
							</TR>
						</TABLE>
					</td>
					<td width="7" valign="top" align="center"><b>&nbsp;</b></td>
					<td valign="top" class="table_td" align="center">
						<TABLE cellSpacing=0 cellPadding=0 width="130">
							<TR>
								<TD class=table_td><p align="center">얼터네이트 프린트 카라 블렉 티셔츠</TD>
							</TR>
							<TR>
								<TD height=10></TD>
							</TR>
							<TR>
								<TD class=table_td><IMG border=0 align=absMiddle src="../images/design/gonggu_end_price.gif" width=34 height=17>158,000원</TD>
							</TR>
							<TR>
								<TD class=table_td><IMG border=0 align=absMiddle src="../images/design/icon_price.gif" width=34 height=17><B><FONT color=#3455de>126,000원</FONT></B></TD>
							</TR>
							<TR>
								<TD class=table_td><IMG SRC="../images/design/popgonggu_search_btn02.gif" WIDTH=56 HEIGHT=15 ALT=""></TD>
							</TR>
						</TABLE>
					</td>
					<td width="7" valign="top" align="center"><b>&nbsp;</b></td>
					<td valign="top" class="table_td" align="center">
						<TABLE cellSpacing=0 cellPadding=0 width="130">
							<TR>
								<TD class=table_td align="center">얼터네이트 프린트 카라 블렉 티셔츠</TD>
							</TR>
							<TR>
								<TD height=10></TD>
							</TR>
							<TR>
								<TD class=table_td><IMG border=0 align=absMiddle src="../images/design/gonggu_end_price.gif" width=34 height=17>158,000원</TD>
							</TR>
							<TR>
								<TD class=table_td><IMG border=0 align=absMiddle src="../images/design/icon_price.gif" width=34 height=17><B><FONT color=#3455de>126,000원</FONT></B></TD>
							</TR>
							<TR>
								<TD class=table_td><IMG SRC="../images/design/popgonggu_search_btn02.gif" WIDTH=56 HEIGHT=15 ALT=""></TD>
							</TR>
						</TABLE>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="30"><img src="../images/design/con_line02.gif" width="615" height="1" border="0"></td>
		</tr>
		<tr>
			<td class="table01_con2" align="center">
				<table cellpadding="0" cellspacing="0"  align=center>
				<tr>
					<td><img src="../images/design/btn_first.gif" border="0" hspace="0"></td>
					<td><img src="../images/design/btn_pre.gif" border="0" hspace="3"></td>
					<td class="table01_con2"><b><font color="#FF511B">1 </font></b>2 3 4 5 6 7 8 9 10</td>
					<td><img src="../images/design/btn_next.gif" border="0" hspace="3"></td>
					<td><img src="../images/design/btn_end.gif" border="0" hspace="0"></td>
				</tr>
				</table>
			</td>
		</tr>
		</table> -->
		<!-- 상품검색하기(관련상품)! -->
<?}?>
	</td>
	<td background="../images/design/pop_view_rightbg.gif" width="17" height="100%"></td>
</tr>
<tr>
	<td height="9" width="10"><IMG SRC="../images/design/pop_view_bottomleft.gif" width="17" height="16" border="0"></td>
	<td background="../images/design/pop_view_bottombg.gif" height="9" width="729">&nbsp;</td>
	<td height="9" width="11"><IMG SRC="../images/design/pop_view_bottomright.gif" width="17" height="16" border="0"></td>
</tr>
</table>
