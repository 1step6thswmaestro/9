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

$type=$_POST["type"];
$list_body=$_POST["list_body"];
$list_xsize=$_POST["list_xsize"];
$list_ysize=$_POST["list_ysize"];
$view_body=$_POST["view_body"];
$view_xsize=$_POST["view_xsize"];
$view_ysize=$_POST["view_ysize"];

if($list_xsize==0) $list_xsize=630;
if($list_ysize==0) $list_ysize=500;
if($view_xsize==0) $view_xsize=630;
if($view_ysize==0) $view_ysize=500;


$list_filename=$list_xsize."".$list_ysize;
$view_filename=$view_xsize."".$view_ysize;




$subject = '정보 팝업';

// 백업 / 복구
if ( $type=="store" OR $type=="restore" ) {
	$MSG = adminDesingBackup ( $type, 'infolist', $list_body, $subject."목록창", '', $list_filename );
	$MSG = adminDesingBackup ( $type, 'infoview', $view_body, $subject."상세 페이지", '', $view_filename );
	$onload="<script>alert(\"".$MSG."\");</script>";
}



if($type=="update") {

	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='infolist' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT tbldesignnewpage SET ";
		$sql.= "type		= 'infolist', ";
		$sql.= "filename	= '".$list_filename."', ";
		$sql.= "subject		= '정보 팝업 목록창', ";
		$sql.= "body		= '".$list_body."' ";
		mysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET ";
		$sql.= "filename	= '".$list_filename."', ";
		$sql.= "body		= '".$list_body."' ";
		$sql.= "WHERE type='infolist' ";
		mysql_query($sql,get_db_conn());
	}
	mysql_free_result($result);


	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='infoview' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT tbldesignnewpage SET ";
		$sql.= "type		= 'infoview', ";
		$sql.= "filename	= '".$view_filename."', ";
		$sql.= "subject		= '정보 팝업 상세 페이지', ";
		$sql.= "body		= '".$view_body."' ";
		mysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET ";
		$sql.= "filename	= '".$view_filename."', ";
		$sql.= "body		= '".$view_body."' ";
		$sql.= "WHERE type='infoview' ";
		mysql_query($sql,get_db_conn());
	}
	mysql_free_result($result);

	$onload="<script>alert(\"정보 팝업창 디자인 수정이 완료되었습니다.\");</script>";
} else if($type=="delete") {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='infolist' ";
	mysql_query($sql,get_db_conn());
	$sql = "DELETE FROM tbldesignnewpage WHERE type='infoview' ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert(\"정보 팝업창 디자인 삭제가 완료되었습니다.\");</script>";
} else if($type=="clear") {
	$list_body="";
	$list_xsize=630;
	$list_ysize=500;
	$sql = "SELECT body FROM tbldesigndefault WHERE type='infolist' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$list_body=$row->body;
	}
	mysql_free_result($result);

	$view_body="";
	$view_xsize=630;
	$view_ysize=500;
	$sql = "SELECT body FROM tbldesigndefault WHERE type='infoview' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$view_body=$row->body;
	}
	mysql_free_result($result);
}


if($type!="clear") {
	$list_body="";
	$list_xsize=0;
	$list_ysize=0;

	$view_body="";
	$view_xsize=0;
	$view_ysize=0;

	$sql = "SELECT filename,body FROM tbldesignnewpage WHERE type='infolist' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$list_body=$row->body;
		$list_size=explode("",$row->filename);
		$list_xsize=(int)$list_size[0];
		$list_ysize=(int)$list_size[1];
		if($list_xsize==0) $list_xsize=630;
		if($list_ysize==0) $list_ysize=500;
	}
	mysql_free_result($result);
	if($list_xsize==0) $list_xsize=630;
	if($list_ysize==0) $list_ysize=500;

	$sql = "SELECT filename,body FROM tbldesignnewpage WHERE type='infoview' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$view_body=$row->body;
		$view_size=explode("",$row->filename);
		$view_xsize=(int)$view_size[0];
		$view_ysize=(int)$view_size[1];
		if($view_xsize==0) $view_xsize=630;
		if($view_ysize==0) $view_ysize=500;
	}
	mysql_free_result($result);
	if($view_xsize==0) $view_xsize=630;
	if($view_ysize==0) $view_ysize=500;
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.list_body.value.length==0) {
			alert("정보 팝업 목록 디자인 내용을 입력하세요.");
			document.form1.list_body.focus();
			return;
		}
		if(document.form1.view_body.value.length==0) {
			alert("정보 팝업 상세 페이지 디자인 내용을 입력하세요.");
			document.form1.view_body.focus();
			return;
		}
		if(!IsNumeric(document.form1.list_xsize.value)) {
			alert("팝업 목록창 가로폭은 숫자만 입력 가능합니다.");
			document.form1.list_xsize.focus();
			return;
		}
		if(!IsNumeric(document.form1.list_ysize.value)) {
			alert("팝업 목록창 높이는 숫자만 입력 가능합니다.");
			document.form1.list_ysize.focus();
			return;
		}
		if(!IsNumeric(document.form1.view_xsize.value)) {
			alert("팝업 상세 페이지 가로폭은 숫자만 입력 가능합니다.");
			document.form1.view_xsize.focus();
			return;
		}
		if(!IsNumeric(document.form1.view_ysize.value)) {
			alert("팝업 상세 페이지 높이는 숫자만 입력 가능합니다.");
			document.form1.view_ysize.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("정보 팝업창 디자인을 삭제하시겠습니까?")) {
			document.form1.type.value=type;
			document.form1.submit();
		}
	} else if(type=="clear") {
		alert("기본값 복원 후 [적용하기]를 클릭하세요. 클릭 후 페이지에 적용됩니다.");
		document.form1.type.value=type;
		document.form1.submit();
	}

	// 백업
	if(type=="store") {
		if(confirm("<?=$subject?> 디자인을 백업하시겠습니까?\n\n적용하지 않으셨다면 \"적용하기\"로 적용 하신후 백업하시기 바랍니다.\n기존 저장된 백업소스를 대체합니다.")) {
			document.form1.type.value=type;
			document.form1.submit();
			return;
		}
	}
	// 복구
	if(type=="restore") {
		if(confirm("<?=$subject?> 디자인을 백업복구 하시겠습니까?\n\n복구 하게 되면 바로 디자인 적용 됩니다.")) {
			document.form1.type.value=type;
			document.form1.submit();
			return;
		}
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt; <span class="2depth_select">정보 팝업창 꾸미기</span></td>
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
					<TD><IMG SRC="images/design_eachpopupinfo_title.gif" ALT=""></TD>
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
					<TD width="100%" class="notice_blue"><p>컨텐츠정보 팝업창 디자인을 자유롭게 관리하실 수 있습니다.</p></TD>
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
					<TD><IMG SRC="images/design_eachpop_stitle1.gif" WIDTH="250" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
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
					<TD width="100%" class="notice_blue">1) 매뉴얼의 <b>매크로명령어</b>를 참조하여 디자인 하세요.</span><br>2) [기본값복원]+[적용하기], [삭제하기]하면 기본템플릿으로 변경(개별디자인 소스 삭제)됨 -> 템플릿 메뉴에서 원하는 템플릿 선택.<br>3) 기본값 복원이나 삭제하기 없이도 템플릿 선택하면 개별디자인은 해제됩니다.(개별디자인 소스는 보관됨)</TD>
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
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td style="padding-top:3pt;">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				<col width=139></col>
				<col width=></col>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">팝업창 크기</TD>
					<TD class="td_con1"><input type=text name=list_xsize value="<?=$list_xsize?>" size=5 maxlength=3 onkeyup="strnumkeyup(this)" class="input"> &times; <input type=text name=list_ysize value="<?=$list_ysize?>" size=5 maxlength=3 onkeyup="strnumkeyup(this)" class="input"> 픽셀</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan="2"><textarea name=list_body style="WIDTH: 100%; HEIGHT: 300px" class="textarea"><?=htmlspecialchars($list_body)?></textarea></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td><p>&nbsp;</p></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/design_eachpop_stitle2.gif" WIDTH="250" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
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
					<TD width="100%" class="notice_blue">1) 매뉴얼의 <b>매크로명령어</b>를 참조하여 디자인 하세요.</span><br>2) [기본값복원]+[적용하기], [삭제하기]하면 기본템플릿으로 변경(개별디자인 소스 삭제)됨 -> 템플릿 메뉴에서 원하는 템플릿 선택.<br>3) 기본값 복원이나 삭제하기 없이도 템플릿 선택하면 개별디자인은 해제됩니다.(개별디자인 소스는 보관됨)</TD>
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
				<td style="padding-top:3pt;">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">팝업창 크기</TD>
					<TD class="td_con1" width="600"><input type=text name=view_xsize value="<?=$view_xsize?>" size=5 maxlength=3 onkeyup="strnumkeyup(this)" class="input"> &times; <input type=text name=view_ysize value="<?=$view_ysize?>" size=5 maxlength=3 onkeyup="strnumkeyup(this)" class="input"> 픽셀</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan="2"><textarea name=view_body style="WIDTH: 100%; HEIGHT: 200px" class="textarea"><?=htmlspecialchars($view_body)?></textarea></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('clear');"><img src="images/botteon_bok.gif" width="124" height="38" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" width="113" height="38" border="0" hspace="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('store');"><img src="images/botteon_store.gif" border="0" hspace="2" alt="백업하기"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('restore');"><img src="images/botteon_restore.gif" border="0" hspace="2" alt="백업복원하기"></a></td>
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
					<TD COLSPAN=3 width="100%" valign="top"  style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"  class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><p class="LIPoint"><B><span class="font_orange">정보 팝업창 매크로명령어</span></B>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</p></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"></td>
						<td >
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>정보 팝업 리스트 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LISTING]</td>
							<td class=td_con1 style="padding-left:5;">
							목록 표시
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PAGEING]</td>
							<td class=td_con1 style="padding-left:5;">
							페이지 표시
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[CLOSE]</td>
							<td class=td_con1 style="padding-left:5;">
							팝업창 닫기 <FONT class=font_blue>(예:&lt;a href=[CLOSE]>닫기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>정보 팝업 상세 페이지 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SUBJECT]</td>
							<td class=td_con1 style="padding-left:5;">
							컨텐츠정보 제목
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[CONTENT]</td>
							<td class=td_con1 style="padding-left:5;">
							컨텐츠정보 내용
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[DATE]</td>
							<td class=td_con1 style="padding-left:5;">
							게시 일자
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ACCESS]</td>
							<td class=td_con1 style="padding-left:5;">
							조회수
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PREV]</td>
							<td class=td_con1 style="padding-left:5;">
							이전 정보 <FONT class=font_blue>(예:&lt;a href=[PREV]>이전&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NEXT]</td>
							<td class=td_con1 style="padding-left:5;">
							다음 정보 <FONT class=font_blue>(예:&lt;a href=[NEXT]>다음&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LIST]</td>
							<td class=td_con1 style="padding-left:5;">
							목록보기 <FONT class=font_blue>(예:&lt;a href=[LIST]>목록보기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LIST]</td>
							<td class=td_con1 style="padding-left:5;">
							창 닫기 <FONT class=font_blue>(예:&lt;a href=[CLOSE]>닫기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						</table>
						</td>
					</tr>
					<tr>
						<td colspan="2"><p>&nbsp;</p></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><p class="LIPoint">나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</p></td>
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