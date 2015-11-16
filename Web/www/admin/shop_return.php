<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-3";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$up_return1_type=$_POST["up_return1_type"];
$up_return2_type=$_POST["up_return2_type"];
$up_ordercancel=$_POST["up_ordercancel"];
$up_nocancel_msg=$_POST["up_nocancel_msg"];
$up_okcancel_msg=$_POST["up_okcancel_msg"];

if ($type=="up") {
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "return1_type		= '".$up_return1_type."', ";
	$sql.= "return2_type		= '".$up_return2_type."', ";
	$sql.= "ordercancel			= '".$up_ordercancel."', ";
	$sql.= "nocancel_msg		= '".$up_nocancel_msg."', ";
	$sql.= "okcancel_msg		= '".$up_okcancel_msg."' ";
	$update = mysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload = "<script> alert('반품/환불 관련 설정이 완료되었습니다.'); </script>";
}

$sql = "SELECT return1_type, return2_type, ordercancel, nocancel_msg, okcancel_msg FROM tblshopinfo ";
$result = mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$return1_type = $row->return1_type;
	$return2_type = $row->return2_type;
	$ordercancel = $row->ordercancel;
	$nocancel_msg = $row->nocancel_msg;
	$okcancel_msg = $row->okcancel_msg;
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt; <span class="2depth_select">상품 반품/환불 기능설정</span></td>
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
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_return_title.gif"  border="0"></TD>
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
					<TD width="100%" class="notice_blue"><p>교환/반품/환불에 대한 설정을 하실 수 있습니다.</p></TD>
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
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_return_stitle1.gif" WIDTH="196" HEIGHT=31 ALT=""></TD>
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
					<TD width="100%" class="notice_blue">1) 쇼핑몰의 이용안내페이지에서 <b>교환/반품/환불</b> 안내 부분에 설정한 내용이 표기됩니다.<br>2) 이용안내를 개별 디자인할 경우는 제외됩니다.</TD>
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
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD width="146" class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">고객 변심의 경우</TD>
					<TD class="td_con1" >고객의 변심에 의한 교환 및 반품인 경우에 배송비는 <select name="up_return2_type" class="select" style="width:100px">
						<option value="2" <? if ($return2_type=="2") echo "selected"; ?>>소비자</option>
						<option value="1" <? if ($return2_type=="1") echo "selected"; ?>>판매자</option>
						</select> 부담입니다.</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD width="146" class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품 이상의 경우</TD>
					<TD class="td_con1" >상품의 이상에 의한 교환 및 반품인 경우에 배송비는 <select name="up_return1_type" class="select" style="width:100px">
						<option value="2" <? if ($return1_type=="2") echo "selected"; ?>>소비자</option>
						<option value="1" <? if ($return1_type=="1") echo "selected"; ?>>판매자</option>
						</select> 부담입니다.</TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
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
					<TD><IMG SRC="images/shop_return_stitle2.gif" WIDTH="196" HEIGHT=31 ALT=""></TD>
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
					<TD width="100%" class="notice_blue">1) 구매고객이 주문내역확인에서 주문취소가 가능한 단계를 설정합니다.<br>2) 설정 단계 이후에는 주문서 상세보기 하단에 <b>[주문취소]</b>라는 메뉴가 나타나지 않습니다.</TD>
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
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD width="146" class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">주문취소 가능 단계</TD>
					<TD class="td_con1" >고객의 주문취소는 쇼핑몰에서 <select name="up_ordercancel" class="select">
						<option value="0" <? if ($ordercancel=="0") echo "selected"; ?>>주문 배송 완료</option>
						<option value="2" <? if ($ordercancel=="2") echo "selected"; ?>>주문 발송 준비</option>
						<option value="1" <? if ($ordercancel=="1") echo "selected"; ?>>주문 결제 완료</option>
						</select> 전에만 가능합니다.</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD width="146" class="table_cell" valign="top"><img src="images/icon_point2.gif" width="8" height="11" border="0">주문취소시<br>&nbsp;&nbsp;고객메세지 설정</TD>
					<TD class="td_con1" >
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="108">주문취소 완료시</td>
						<td ><input type=text name=up_okcancel_msg value="<?=$okcancel_msg?>" size=65 maxlength=250 onKeyDown="chkFieldMaxLen(250)" style="width:100%" class="input"></td>
					</tr>
					<tr>
						<td width="108">주문취소 불가 단계</td>
						<td ><input type=text name=up_nocancel_msg value="<?=$nocancel_msg?>" size=65 maxlength=250 onKeyDown="chkFieldMaxLen(250)" style="width:100%" class="input"></td>
					</tr>
					</table>
					</TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
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
						<td ><b><span class="font_orange">주문리스트에 결제상태 및 처리단계의 확인 기준</b>으로 하며, <b>이미 발송한 경우는 자동주문취소 안됩니다.</b><br></span></td>
					</tr>
					<tr>
						<td width="20" align="right"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td  class="space_top">미입력시 기본 메시지 내용<br></td>
					</tr>
					<tr>
						<td width="20" align="right"><p>&nbsp;</p></td>
						<td class="space_top"><p><b>①발송전</b></p></td>
					</tr>
					<tr>
						<td width="20" align="right"><p>&nbsp;</p></td>
						<td  class="space_top">&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/icon_point3.gif" width="7" height="12" border="0">정상취소&nbsp;: 정상적으로 취소되었습니다. 주문변경 및 환불은 별도 안내해 드리겠습니다<br>&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/icon_point3.gif" width="7" height="12" border="0">취소불가&nbsp;: 주문변경 및 취소, 환불은 쇼핑몰로 별로 문의 바랍니다.<br></td>
					</tr>
					<tr>
						<td width="20" align="right"><p>&nbsp;</p></td>
						<td  class="space_top"><p><b>②발송준비전</b></p></td>
					</tr>
					<tr>
						<td width="20" align="right"><p>&nbsp;</p></td>
						<td class="space_top">&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/icon_point3.gif" width="7" height="12" border="0">정상취소&nbsp;: 정상적으로 취소되었습니다. 주문변경 및 환불은 별도 안내해 드리겠습니다.<br>&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/icon_point3.gif" width="7" height="12" border="0">취소불가&nbsp;: 주문변경 및 취소, 환불은 쇼핑몰로 별로 문의 바랍니다.&nbsp;<br></td>
					</tr>
					<tr>
						<td width="20" align="right"><p>&nbsp;</p></td>
						<td  class="space_top"><p><b>③입금전</b></p></td>
					</tr>
					<tr>
						<td width="20" align="right"><p>&nbsp;</p></td>
						<td  class="space_top">&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/icon_point3.gif" width="7" height="12" border="0">정상취소&nbsp;: 정상적으로 취소되었습니다. 주문변경은 별도 안내해 드리겠습니다.<br>&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/icon_point3.gif" width="7" height="12" border="0">취소불가&nbsp;: 주문변경 및 취소는 쇼핑몰로 별로 문의 바랍니다.</td>
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