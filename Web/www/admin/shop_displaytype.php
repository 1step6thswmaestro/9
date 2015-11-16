<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-2";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$up_frame_type=$_POST["up_frame_type"];
$up_align_type=$_POST["up_align_type"];
$up_predit_type=$_POST["up_predit_type"];

if ($type=="up") {
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "frame_type		= '".$up_frame_type."', ";
	$sql.= "align_type		= '".$up_align_type."', ";
	$sql.= "predit_type		= '".$up_predit_type."' ";
	$result = mysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload = "<script> alert('설정이 완료되었습니다.'); </script>";
}

$sql = "SELECT frame_type, align_type, predit_type FROM tblshopinfo ";
$result = mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$frame_type= $row->frame_type;
	$align_type= $row->align_type;
	$predit_type = $row->predit_type;
}
mysql_free_result($result);

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script>
function CheckForm(){
	document.form1.type.value="up";
	document.form1.submit();
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
			<? include ("menu_shop.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 쇼핑몰 환경 설정 &gt; <span class="2depth_select">프레임/정렬 설정</span></td>
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
					<TD><IMG SRC="images/shop_displaytype_title.gif" ALT=""></TD>
				</TR>
				</TR>
					<TD width="100%" background="images/title_bg.gif" HEIGHT=21></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">프레임, 페이지 정렬설정, 상품상세입력의 웹편집기 사용을 일괄 적용할 수 있습니다.</TD>
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
					<TD><IMG SRC="images/shop_displaytype_stitle1.gif"  ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"><IMG SRC="images/distribute_04.gif" ></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">1) <B>투프레임 타입(상단+메인프레임)</B> : 쇼핑몰 페이지 주소 고정 및 상단메뉴 고정(새로고침 F5 - 쇼핑몰 메인으로 이동)<br>2) <B>원프레임 타입(주소고정)</B> : 쇼핑몰 페이지 주소가 항상 메인도메인명으로 고정(새로고침 F5 - 쇼핑몰 메인으로 이동)<br>3) <B>원프레임 타입(주소변동)</B> : 쇼핑몰의 각 페이지 주소를 그대로 노출하여 표시(새로고침 F5 - 현재 페이지 유지</b>)</TD>
					<TD background="images/distribute_07.gif"><IMG SRC="images/distribute_07.gif" ></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="6"></td>
			</tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" width="248"></TD>
					<TD background="images/table_top_line.gif" width="512" colspan="2" ></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="228" align="center"><input type=radio id="idx_frame_type1" name=up_frame_type value="N" <? if($frame_type == "N") echo "checked ";?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_frame_type1>투프레임 타입(상단+메인프레임)</label></TD>
					<TD class="table_cell1" width="248" align="center"><input type=radio id="idx_frame_type2" name=up_frame_type value="Y" <? if($frame_type == "Y") echo "checked ";?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_frame_type2>원프레임 타입(주소고정)</label></TD>
					<TD class="table_cell1" width="234" align="center"><input type=radio id="idx_frame_type3" name=up_frame_type value="A" <? if($frame_type == "A") echo "checked ";?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_frame_type3>원프레임 타입(주소변동)</label></TD>
				</TR>
				<TR>
					<TD colspan="3" width="760" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD width="248" align="center">&nbsp;<img src="images/shop_framepage.gif" border="0"></TD>
					<TD class="td_con1" width="256" align="center"><img src="images/shop_noframepage.gif" border="0"></TD>
					<TD class="td_con1" width="242" align="center"><img src="images/shop_noframepage.gif" border="0"></TD>
				</TR>
				<TR>
					<TD background="images/table_top_line.gif" width="248"></TD>
					<TD background="images/table_top_line.gif" width="512" colspan="2"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="30"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_displaytype_stitle2.gif"  ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"><IMG SRC="images/distribute_04.gif" ></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">1) 투프레임과 가운데 정렬을 선택할 경우 스크롤바 때문에 쇼핑몰의 형태가 어긋날 수 있습니다.<br>2) 원프레임에 가운데 정렬을 하실것을 권장합니다.</TD>
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
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" width="383"><img src="images/table_top_line.gif"></TD>
					<TD background="images/table_top_line.gif" width="377" ></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="363" align="center"><input type=radio id="idx_align_type1" name=up_align_type value="N" <? if($align_type == "N") echo "checked ";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_align_type1>좌측 정렬(좌측여백 X, 우측여백 O)</label></TD>
					<TD class="table_cell1" width="362" align="center"><input type=radio id="idx_align_type2" name=up_align_type value="Y" <? if($align_type == "Y") echo "checked ";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_align_type2>가운데 정렬(좌측여백 O, 우측여백 O)</label></TD>
				</TR>
				<TR>
					<TD colspan="2" width="760" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD width="383" align="center"><img src="images/shop_alignleft.gif" border="0"></TD>
					<TD class="td_con1" width="370" align="center"><img src="images/shop_aligncenter.gif" border="0"> </TD>
				</TR>
				<TR>
					<TD background="images/table_top_line.gif" width="383"></TD>
					<TD background="images/table_top_line.gif" width="377"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="30"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_displaytype_stitle3.gif"  ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">1) 상품등록시 상세정보 입력 타입을 편집기사용 또는 미사용을 일괄 적용할 수 있습니다.<br>2) 입력 타입을 변경할 경우 기존 입력모양이 달라질 수 있습니다.</TD>
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
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" width="383"></TD>
					<TD background="images/table_top_line.gif" width="377" ></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="363" align="center"><input type=radio id="idx_predit_type1" name=up_predit_type value="Y" <? if($predit_type == "Y") echo "checked ";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_predit_type1>웹편집기로 입력(<b>권장</b>)</label></TD>
					<TD class="table_cell1" width="362" align="center"><input type=radio id="idx_predit_type2" name=up_predit_type value="N" <? if($predit_type == "N") echo "checked ";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_predit_type2>단순 입력창에서 입력(개별 HTML 방식)</label></TD>
				</TR>
				<TR>
					<TD colspan="2" width="760" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD width="383" align="center">&nbsp;<img src="images/shop_detailediter.gif" border="0"></TD>
					<TD class="td_con1" width="370" align="center"> <img src="images/shop_detailhtml.gif" border="0"></TD>
				</TR>
				<TR>
					<TD background="images/table_top_line.gif" width="383"></TD>
					<TD background="images/table_top_line.gif" width="377"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">화면설정을 자유롭게 설정</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">
						- 프레임과 좌우정렬을 자유롭게 가능합니다.<br>
						- 원프레임에서 디자인 한 후 투프레임으로 사용할 경우 상하좌우 라인이 정확히 일치하지 않을 수 있습니다.<br>
						- 좌우정렬을 변경하면 기존 디자인에 변화가 있을 수 있습니다.<br>
						- 상품의 특성이나 쇼핑몰에 변화를 줄 때 좌우정렬 및 디자인을 변경하면서 사용하실 수 있습니다.<br>
						</td>
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