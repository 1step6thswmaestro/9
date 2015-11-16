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

@set_time_limit(300);
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

var isupload=false;

function CheckForm() {
	alert("등록");

	return false;

	document.form1.submit();
}
</script>

<table cellpadding="0" cellspacing="0" width="980" style="table-layout:fixed">
<tr>
	<td width=10></td>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td height="29">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 모바일 &gt; 기본설정 &gt; <span class="2depth_select">기본설정</span></td>
		</tr>
		<tr>
			<td><img src="images/top_link_line.gif" width="100%" height="1" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=190></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top" background="images/left_bg.gif" style="padding-top:15">
			<? include ("menu_mobile.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/product_excelupload_title.gif" border="0"></TD>
					<TD width="100%" background="images/title_bg.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN="2" background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">다수 상품정보를 엑셀파일로 만들어 일괄 등록을 하는 기능입니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN="2" background="images/distribute_09.gif"></TD>
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
					<TD><IMG SRC="images/product_excelupload_stitle1.gif" border="0"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>

			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
			<input type=hidden name=mode>
			<input type="hidden" name="code" value="">
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" colspan=2></TD>
				</TR>

						<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">모바일용 쇼핑몰 사용 설정</TD>
					<TD class="td_con1" width="600">
					<input type="radio" value="Y"> 사용
					<input type="radio" value="N"> 사용안함
					<br /><span class="font_orange">* 사용: 'm.기존주소'로 접속  * 사용안함 : 모바일용 주소 접속시 pc용 화면으로 접속됨</span></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>



				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">모바일 접속시 자동 연결 설정</TD>
					<TD class="td_con1" width="600">
					<input type="radio" value="Y"> 사용
					<input type="radio" value="N"> 사용안함
					<br /><span class="font_orange">* 사용: 'www.기존주소' 입력시에도 모바일로 접속할 경우 모바일용 화면으로 자동으로 이동함<br />
					* 사용안함 :  모바일에서 'www.기존주소' 입력시 PC용 화면(기존 쇼핑몰 화면) 그대로 보여줌

					</span></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>


				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">모바일로 PC화면 접속시<br /> 모바일 화면 바로가기 설정</TD>
					<TD class="td_con1" width="600">
					<input type="radio" value="Y"> 사용
					<input type="radio" value="N"> 사용안함
					<br /><span class="font_orange">* 사용: 모바일에서 PC용 화면으로 이동할 경우 화면 상단에 [모바일 화면으로 이동]버튼이 노출됨<br />
					* 사용안함 :  PC용 화면 상단에 [모바일 화면으로 이동] 버튼이 노출안됨

					</span></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>


				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">결제수단 선택</TD>
					<TD class="td_con1" width="600">
					<input type="checkbox" value="Y"> 무통장입금
					<br /><span class="font_orange">* 신용카드, 휴대폰 결제의 경우 30만원 이상의 구매금액은 결제가 불가합니다.<br />(30만원 이상 구매시 반드시 공인인증 절차 필요)<br />

					</span></TD>
			  </TR>


			  <TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">스킨설정</TD>
					<TD class="td_con1" width="600">
					<select name="">
						<option value="" selected>스킨선택</option>
						<option value="">
					</select>

					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>

				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">로고등록</TD>
					<TD class="td_con1" width="600">
					<input type="file" name="" class="input">
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>

				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">아이콘 등록</TD>
					<TD class="td_con1" width="600">
					<input type="file" name="" class="input">
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>


				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">메인 베너이미지 #1</TD>
					<TD class="td_con1" width="600">
					<input type="file" name="" class="input">
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>

				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">메인 베너이미지 #2</TD>
					<TD class="td_con1" width="600">
					<input type="file" name="" class="input">
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>


				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">메인 베너이미지 #3</TD>
					<TD class="td_con1" width="600">
					<input type="file" name="" class="input">
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>

				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">메인 베너이미지 #4</TD>
					<TD class="td_con1" width="600">
					<input type="file" name="" class="input">
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>


				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">메인 베너이미지 #5</TD>
					<TD class="td_con1" width="600">
					<input type="file" name="" class="input">
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>

				<TR>
					<TD background="images/table_top_line.gif" colspan=2></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td align="center" height=10></td>
			</tr>
			<tr>
				<td align="center"><img src="images/btn_fileup.gif" id="uploadButton" width="113" height="38" border="0" style="cursor:hand" onclick="CheckForm(document.form1);"></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>

			<!-- <tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
					<col width=20></col>
					<col width=></col>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">매뉴얼</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">
						- 매뉴얼 내용
						<br>
						<FONT class=font_orange>- 매뉴얼 내용</font>
						</td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>

					</table>
					</TD>
					<TD background="images/manual_right1.gif"><IMG SRC="images/manual_right1.gif" WIDTH=18 HEIGHT="2" ALT=""></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr> -->
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



<? INCLUDE "copyright.php"; ?>