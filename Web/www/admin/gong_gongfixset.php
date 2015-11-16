<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "go-3";
$MenuCode = "gong";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}
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
			<? include ("menu_gong.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 공구/경매 &gt; 공동구매관리 &gt; <span class="2depth_select">가격고정형 공동구매 설정</span></td>
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
					<TD><IMG SRC="images/gong_gongfixset_title.gif" ALT=""></TD>
					</tr><tr>
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
					<TD width="100%" class="notice_blue">가격이 고정된 공동구매 설정 방법에 대해서 안내해드립니다.</TD>
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
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><img src="images/gong_gongfixset_st01.gif" border="0"></td>
				</tr>
				<tr>
					<td style="padding-left:18px;letter-spacing:-0.5pt;">가격 고정형 공동구매란 일반적인 가격변동형 공동구매(참여자수에 따라 가격변동)와는 달리 공구가가 확정되어진 공동구매로, 상품 진열시 <br>일반상품 진열방식 대신, 공동구매 타입으로 상품 진열 방식을 바꾸어 출력하는 방법입니다.</td>
				</tr>
				<tr><td height="10"></td></tr>
				<tr>
					<td style="padding-left:20px;letter-spacing:-0.5pt;"><img src="images/gong_gongfixset_icon.gif" border="0" align="absmiddle"><b>가격고정형 공동구매 상품</b></td>
				</tr>
				<tr>
					<td style="padding-left:14px;">
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td><img src="images/gong_gongfixset_img01_01.gif" height="283" border="0"></td>
						<td><img src="images/gong_gongfixset_img02_01.gif" height="283" border="0"></td>
					</tr>
					<tr>
						<td style="padding-top:10px;letter-spacing:-0.5pt;" align="center" background="images/gong_gongfixset_img01_02.gif"><b>[카테고리 리스트 진열 방식]</b></td>
						<td style="padding-top:10px;letter-spacing:-0.5pt;" align="center" background="images/gong_gongfixset_img02_02.gif"><b>[상품 상세설명 페이지 진열 방식]</b></td>
					</tr>
					<tr>
						<td colspan="2"><img src="images/gong_gongfixset_imgdown.gif" border="0"></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height="40"></td></tr>
				<tr>
					<td><img src="images/gong_gongfixset_st02.gif" border="0"></td>
				</tr>
				<tr>
					<td style="padding-left:8px;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td style="padding-left:12px;letter-spacing:-0.5pt;"><img src="images/gong_gongfixset_icon.gif" border="0" align="absmiddle"><a href="javascript:parent.topframe.GoMenu(4,'product_code.php');"><span class="font_blue">상품관리 > 카테고리/상품관리 > 카테고리 관리</span></a> 에서 상품진열 템플릿을 <b>가격고정형 공동구매 디자인(공구형)</b>으로 선택한다.</td>
					</tr>
					<tr>
						<td style="padding-left:8px;">
						<table cellpadding="0" cellspacing="0" width="720">
						<tr>
							<td><img src="images/gong_gongfixset_img03.gif" border="0"></td>
							<td width="100%" background="images/gong_gongfixset_imgbg.gif"></td>
							<td><img src="images/gong_gongfixset_img04.gif" border="0"></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td style="padding-left:12px;letter-spacing:-0.5pt;"><img src="images/gong_gongfixset_icon.gif" border="0" align="absmiddle"><a href="javascript:parent.topframe.GoMenu(4,'product_code.php');"><span class="font_blue">상품관리 > 카테고리/상품관리 > 카테고리 관리</span></a> 에서 상품진열 템플릿을 가격고정형 공동구매 디자인(공구형)으로 선택한다.</td>
					</tr>
					<tr>
						<td style="padding-left:8px;">
						<table cellpadding="0" cellspacing="0" width="720">
						<tr>
							<td><img src="images/gong_gongfixset_img05.gif" border="0"></td>
							<td width="100%" background="images/gong_gongfixset_imgbg1.gif"></td>
							<td><img src="images/gong_gongfixset_img06.gif" border="0"></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td style="padding-left:12px;letter-spacing:-0.5pt;"><img src="images/gong_gongfixset_icon.gif" border="0" align="absmiddle">가격고정형 공동구매 타입으로 설정된 카테고리에 상품을 등록합니다.(<a href="javascript:parent.topframe.GoMenu(4,'gong_gongfixreg.php');"><span class="font_blue">공구/경매 > 공동구매관리 > 가격고정형 공동구매 등록</a></span>)</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height="20"></td></tr>
				</table>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif">&nbsp;</TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<col width=20></col>
					<col width=></col>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">가격고정형 공동구매 설정</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 가격고정형 공동구매 타입으로 설정된 카테고리는 관리자 모드에서 "카테고리명(공구형)" 형식으로 문구가 추가 표기됩니다.</td>
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
</table>
<?=$onload?>
<? INCLUDE "copyright.php"; ?>