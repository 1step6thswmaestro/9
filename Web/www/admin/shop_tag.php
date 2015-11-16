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
$sql = "SELECT etctype FROM tblshopinfo ";
$result=mysql_query($sql,get_db_conn());
$tagtype="";
if($row=mysql_fetch_object($result)) {
	if (strlen($row->etctype)>0) {
		$etctemp = explode("TAGTYPE=",$row->etctype);
		
		if (strlen($etctemp[1])>0) {
			if(strlen(substr($etctemp[1],0,1))>0 && substr($etctemp[1],1,1) == "") {
				$tagtype=substr($etctemp[1],0,1);
				$etctempvalue = substr($etctemp[1],2);
			} else {
				$etctempvalue = $etctemp[1];
			}
		}

		$etctype = $etctemp[0].$etctempvalue;
	}
}
mysql_free_result($result);

$type=$_POST["type"];
$up_tag=$_POST["up_tag"];
$up_listtag=$_POST["up_listtag"];

if($type=="up") {
	if(strlen($up_tag)==0) $up_tag="Y";

	if($up_tag == "Y" && $up_listtag == "N") {
		$up_tag = "L";
	}

	$tag_info.="TAGTYPE=".$up_tag."";
	$tagtype = $up_tag;

	$sql="UPDATE tblshopinfo SET etctype='".$etctype.$tag_info."' ";
	mysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>alert('상품태그 관련 기능 설정이 완료되었습니다.');</script>";
}

if(strlen($tagtype)==0) {
	$tagtype = "Y";
}

if($tagtype == "Y") {
	$check_tagY="checked";
	$check_listtagY="checked";
	$listdisabled="";
} else if($tagtype == "L") {
	$check_tagY="checked";
	$check_listtagN="checked";
	$listdisabled="";
} else {
	$check_tagN="checked";
	$check_listtagY="checked";
	$listdisabled="disabled";
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
<!--
function CheckForm() {
	if (!confirm("태그 정보를 저장하겠습니까?")) {
		return;
	}
	form1.type.value="up";
	form1.submit();
}

function tag_change(form) {
	if(form.up_tag[0].checked) {
		form.up_listtag[0].disabled=false;
		form.up_listtag[1].disabled=false;
	} else {
		form.up_listtag[0].disabled=true;
		form.up_listtag[1].disabled=true;
	}
}
//-->
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt; <span class="2depth_select">상품태그 관련 기능설정</span></td>
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
					<TD><IMG SRC="images/shop_producttag_title.gif" border="0"></TD>
					</tr>
<tr>
<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
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
					<TD width="100%" class="notice_blue"><p>상품의 태그(Tag) 관련 기능을 설정하실 수 있습니다.</p></TD>
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
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_producttag_stitle2.gif" border="0"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품태그 사용여부 선택</TD>
					<TD class="td_con1">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td><input type=radio id="idx_tag1" name=up_tag value="Y" <?=$check_tagY?> onclick="tag_change(this.form);"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_tag1>상품태그 기능 사용</label>&nbsp;&nbsp;&nbsp;<input type=radio id="idx_tag2" name=up_tag value="N" <?=$check_tagN?> onclick="tag_change(this.form);"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_tag2>상품태그 기능 미사용</label></td>
					</tr>
					<tr>
						<td height="4"></td>
					</tr>
					<tr>
						<td>&nbsp;<span class=font_blue>* 상품태그(Tag)는 상품에 고객들이 직접 Tag를 입력 및 공유하는 Web2.0 기반의 기능입니다.</span></TD>
					</tr>
					</table>
					</td>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품 목록 상품태그 출력여부</TD>
					<TD class="td_con1">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td><input type=radio id="idx_listtag1" name=up_listtag value="Y" <?=$check_listtagY?> <?=$listdisabled?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_listtag1>상품태그 출력함</label>&nbsp;&nbsp;&nbsp;<input type=radio id="idx_listtag2" name=up_listtag value="N" <?=$check_listtagN?> <?=$listdisabled?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_listtag2>상품태그 미출력</label></td>
					</tr>
					<tr>
						<td height="4"></td>
					</tr>
					<tr>
						<td>&nbsp;<span class=font_blue>* 상품목록 출력페이지에서 최근 등록된 태그를 출력할 수 있습니다.</span></TD>
					</tr>
					</table>
					</td>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>
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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td class="space_top">- <b>상품태그(Tag)란?</b><br>
						<b>&nbsp;&nbsp;</b>참여, 개방, 공유의 web2.0 기능으로 고객이 직접 상품에 대한 느낌이나 특징을 단어 꼬리표(tag)를 입력하면<br>
						<b>&nbsp;&nbsp;</b>동일한 tag를 가진 모든 상품을 검색해줍니다.
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