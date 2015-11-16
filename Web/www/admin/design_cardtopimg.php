<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-3";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$cardimg=$_FILES["cardimg"];

$imagepath = $Dir.DataDir."shopimages/etc/";

if($type=="upload" && $cardimg[size]>0) {
	$ext = strtolower(substr($cardimg[name],-3));
	if($ext!="gif") {
		echo "<html></head><body onload=\"alert('카드결제창 상단이미지는 gif 이미지 파일만 업로드 가능합니다.');location.href='".$_SERVER[PHP_SELF]."'\"></body></html>";exit;
	}
	if($cardimg[size]>153600) {
		echo "<html></head><body onload=\"alert('올리실 이미지 용량은 150KB 이하의 파일만 가능합니다.');location.href='".$_SERVER[PHP_SELF]."'\"></body></html>";exit;
	}
	$size=getimageSize($cardimg[tmp_name]);
	if((435>$size[0] || $size[0]>445) || (54>$size[1] || $size[1]>64)){
		echo "<html></head><body onload=\"alert('카드결제창 상단이미지 사이즈는 440X59픽셀의 이미지로 업로드 하시기 바랍니다.');location.href='".$_SERVER[PHP_SELF]."'\"></body></html>";exit;
	}
	$filepath = $imagepath."cardimg_kcp.gif";
	move_uploaded_file($cardimg[tmp_name],$filepath);
	chmod($filepath,0666);
	$onload="<script>alert(\"카드결제창 상단이미지 등록이 완료되었습니다.\");</script>";
} else if($type=="delete") {
	unlink($imagepath."cardimg_kcp.gif");
	$onload="<script>alert(\"카드결제창 상단이미지 삭제가 완료되었습니다.\");</script>";
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if(document.form1.cardimg.value.length==0) {
		alert("업로드 이미지를 선택하세요.");
		return;
	}
	document.form1.type.value="upload";
	document.form1.submit();
}

function CheckDelete() {
	if(confirm("카드결제창 상단이미지를 삭제하시겠습니까?")) {
		document.form2.type.value="delete";
		document.form2.submit();
	}
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
			<? include ("menu_design.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; 템플릿-페이지 본문  &gt; <span class="2depth_select">카드결제창 로고</span></td>
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
					<TD><IMG SRC="images/design_cardtopimg_title.gif"  ALT=""></TD>
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
					<TD width="100%" class="notice_blue">카드결제창의 상단이미지를 쇼핑몰에 맞게 변경/관리하실 수 있습니다.</TD>
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
					<TD><IMG SRC="images/design_cardtopimg_stitle1.gif" WIDTH="181" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<tr>
				<td align=center width="764"><img src="images/cardimg_sample1.gif" width="270" height="230" border="0" class="imgline"></td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td height=3 style="width:764px; padding-top:2px;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" bgcolor="#ededed" style="padding:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD width="100%" height="30" align=center background="images/blueline_bg.gif"><b><font color="#555555">파일업로드 하기</font></b></TD>
						</TR>
						<TR>
							<TD width="100%" background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD width="100%" style="padding:10pt;">
							<table cellpadding="0" cellspacing="0" border="0">
<?
			$uploadbtn="add2";
			if(file_exists($imagepath."cardimg_kcp.gif")) {
				echo "<tr>\n";
				echo "	<td width=\"100%\">\n";
				echo "		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				echo "			<tr>\n";
				echo "				<td><img src=\"".$imagepath."cardimg_kcp.gif\" border=\"0\" width=\"440\" width=\"59\"></td>\n";
				echo "				<td width=\"101\" align=right><A HREF=\"javascript:CheckDelete()\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></A></td>\n";
				echo "			</tr>\n";
				echo "		</table>\n";
				echo "	</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td width=\"100%\"><hr size=\"1\" noshade color=\"#EBEBEB\"></td>\n";
				echo "</tr>\n";
				$uploadbtn="edit3";
			}
?>
							<tr>
								<td width="100%">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width=100%><input type=file name="cardimg" size="40" style="width:98%" class="input"></td>
									<td width="102" align=rught><A HREF="javascript:CheckForm()"><img src="images/btn_<?=$uploadbtn?>.gif" width="50" height="22" border="0"></a></td>
								</tr>
								<tr>
									<td colspan="2"><span class="font_orange">* 이미지 파일 종류는 GIF(gif) 파일만 등록 가능합니다.<br>
									* 업로드 가능 사이즈는 150KB 이하입니다.<br>* 이미지 사이즈는 가로 440 X 세로 59 픽셀로 등록 가능합니다.</span></td>
								</tr>
								</table>
								</td>
							</tr>
							</table>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</form>
			<form name=form2 method=post action="<?=$_SERVER[PHP_SELF]?>">
			<input type=hidden name=type>
			</form>
			<tr><td height=20></td></tr>
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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"  class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="100%"><span class="font_dotline">결제창 로고 변경</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 결제창 로고는 PG사마다 다를 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 결제창은 PG사의 페이지로 디자인 변경되지 않습니다.(템플릿 및 개별디자인 없음)</td>
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