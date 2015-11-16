<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-4";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$bottom_type=$_POST["bottom_type"];
$bottom_body=$_POST["bottom_body"];

$subject = '쇼핑몰 하단';

$insertKey = "bottom";

// 백업 / 복구
if ( $type=="store" OR $type=="restore" ) {
	$MSG = adminDesingBackup ( $type, $insertKey, $bottom_body, $subject, $bottom_type );
	$onload="<script>alert(\"".$MSG."\");</script>";
}



if($type=="update" && strlen($bottom_body)>0) {
	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage ";
	$sql.= "WHERE type='bottom' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT tbldesignnewpage SET ";
		$sql.= "type		= 'bottom', ";
		$sql.= "subject		= '쇼핑몰 하단', ";
		$sql.= "code		= '".$bottom_type."', ";
		$sql.= "body		= '".$bottom_body."' ";
		mysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET ";
		$sql.= "code	= '".$bottom_type."', ";
		$sql.= "body		= '".$bottom_body."' ";
		$sql.= "WHERE type='bottom' ";
		mysql_query($sql,get_db_conn());
	}
	mysql_free_result($result);
	$onload="<script>alert(\"하단화면 디자인 수정이 완료되었습니다.\");</script>";
} else if($type=="delete") {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='bottom' ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert(\"하단화면 삭제가 완료되었습니다.\");</script>";
} else if($type=="clear") {
	$bottom_body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='bottom' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$bottom_body=$row->body;
	}
	mysql_free_result($result);

	$bottom_type="2";
}
if($type!="clear") {
	$bottom_type="";
	$bottom_body="";
	$sql = "SELECT * FROM tbldesignnewpage ";
	$sql.= "WHERE type='bottom' ";
	$result = mysql_query($sql,get_db_conn());
	if($row = mysql_fetch_object($result)) {
		$bottom_type=$row->code;
		$bottom_body=$row->body;
	}
	mysql_free_result($result);
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.bottom_body.value.length==0) {
			alert("하단화면 내용을 입력하세요.");
			document.form1.bottom_body.focus();
			return;
		}
		if(document.form1.bottom_type[0].checked==false && document.form1.bottom_type[1].checked==false) {
			alert("쇼핑몰 하단 디자인 형태를 선택하세요.");
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("하단화면 디자인을 삭제하시겠습니까?")) {
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
		}
	}
	// 복구
	if(type=="restore") {
		if(confirm("<?=$subject?> 디자인을 백업복구 하시겠습니까?\n\n복구 하게 되면 바로 디자인 적용 됩니다.")) {
			document.form1.type.value=type;
			document.form1.submit();
		}
	}

	// 미리보기
	if(type=="preview") {
		if(document.form1.bottom_body.value.length==0) {
			alert("하단화면 내용을 입력하세요.");
			document.form1.bottom_body.focus();
			return;
		}
		if(document.form1.bottom_type[0].checked==false && document.form1.bottom_type[1].checked==false) {
			alert("쇼핑몰 하단 디자인 형태를 선택하세요.");
			return;
		}
		document.form1.type.value='<?=$insertKey?>';
		document.form1.target="preview";
		document.form1.action="designPreview.php";
		document.form1.submit();
		document.form1.target="";
		document.form1.action="<?=$_SERVER[PHP_SELF]?>";
	}

}

//매크로 보기(팝업)
function macroview(){
	window.open("http://www.getmall.co.kr/macro/pages/bottom_macro.html","top_macro","height=800,width=680,scrollbars=no");
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; 개별디자인-메인 및 상하단  &gt; <span class="2depth_select">하단화면 꾸미기</span></td>
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





			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/design_eachbottom_title.gif"  ALT=""></TD>
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
					<TD width="100%" class="notice_blue">쇼핑몰 하단을 자유롭게 디자인이 가능합니다.</TD>
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
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td  bgcolor="#ededed" style="padding:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD  height="35" align=center background="images/blueline_bg.gif"><b><font color="#555555">쇼핑몰 하단 디자인 형태 선택</font></b></TD>
						</TR>
						<TR>
							<TD background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD  style="padding:7pt;">
							<table align="center" cellpadding="0" cellspacing="0" width="80%">
							<tr>
								<td  align=center><img src="images/design_eachbottom_img1.gif" width="177" height="137" border="0"></td>
								<td  align=center><img src="images/design_eachbottom_img2.gif" width="177" height="137" border="0"></td>
							</tr>
							<tr>
								<td  align=center><input type=radio id="idx_bottom_type1" name="bottom_type" value="1" <?if($bottom_type=="1")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_bottom_type1>메인본문 사이즈 (왼쪽메뉴 제외)</label></td>
								<td  align=center><input type=radio id="idx_bottom_type2" name="bottom_type" value="2" <?if($bottom_type=="2")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_bottom_type2>쇼핑몰 전체 사이즈</label></td>
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
			<tr><td height="30"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/design_eachbottom_stitle1.gif" WIDTH="174" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;&nbsp;<a href="javascript:macroview();"><img src="images/btn_macroview.gif" border="0" align="absmiddle" alt="" /></a>&nbsp;&nbsp;<a href="http://www.getmall.co.kr/macro/data/bottom_design.zip"><img src="images/btn_defaultcode.gif" border="0" align="absmiddle" alt="" /></a></TD>
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
					<TD width="100%" class="notice_blue">
						1) 매뉴얼의 <b>매크로명령어</b>를 참조하여 디자인 하세요.<br />
						2) <span class="font_orange" style="font-size:11px;"><u>하단화면 매크로 관련 파일</u> : <b>/lib/bottom.php</b> (파일 수정시 기존 파일은 반드시 백업하시기 바랍니다.)</span><br />
						3) [기본값복원]+[적용하기], [삭제하기]하면 기본템플릿으로 변경(개별디자인 소스 삭제)됩니다. -> 템플릿 메뉴에서 원하는 템플릿 선택<br />
						4) 기본값 복원이나 삭제하기 없이도 템플릿 선택하면 개별디자인 해제됩니다.(개별디자인 소스는 보관됨)
					</TD>
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
				<td><textarea name=bottom_body style="WIDTH: 100%; HEIGHT: 300px" class="textarea"><?=htmlspecialchars($bottom_body)?></textarea></td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('clear');"><img src="images/botteon_bok.gif" width="124" height="38" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" width="113" height="38" border="0" hspace="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('preview');"><img src="images/botteon_prev.gif" width="113" height="38" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('store');"><img src="images/botteon_store.gif" border="0" hspace="2" alt="백업하기"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('restore');"><img src="images/botteon_restore.gif" border="0" hspace="2" alt="백업복원하기"></a></td>
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
					<TD COLSPAN=3 width="100%" valign="top" class=menual_bg style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"  class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><p class="LIPoint"><B><span class="font_orange">화면하단 매크로명령어</span></B>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</p></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"></td>
						<td   style="padding-top:3pt; padding-bottom:10pt;">
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[URL]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 URL <FONT class=font_blue>(예:&lt;a href=[URL]>쇼핑몰URL&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NAME]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 이름
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TEL]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 전화번호
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[INFOMAIL]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 운영자 메일
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[COMPANYNAME]</td>
							<td class=td_con1 style="padding-left:5;">
							상호명
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BIZNUM]</td>
							<td class=td_con1 style="padding-left:5;">
							사업자등록번호
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SALENUM]</td>
							<td class=td_con1 style="padding-left:5;">
							통신판매신고번호
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[OWNER]</td>
							<td class=td_con1 style="padding-left:5;">
							회사대표자명
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRIVERCY]</td>
							<td class=td_con1 style="padding-left:5;">
							개인정보담당자
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRIVERCYVIEW]</td>
							<td class=td_con1 style="padding-left:5;">
							개인정보보호정책 <FONT class=font_blue>(예:&lt;a href=[PRIVERCYVIEW]>개인정보보호정책&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ADDRESS]</td>
							<td class=td_con1 style="padding-left:5;">
							사업장 소재지(주소)
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[CONTRACT]</td>
							<td class=td_con1 style="padding-left:5;">
							이용약관 <FONT class=font_blue>(예:&lt;a href=[CONTRACT]>이용약관&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[HOME]</td>
							<td class=td_con1 style="padding-left:5;">
							HOME <FONT class=font_blue>(예:&lt;a href=[HOME]>HOME&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[USEINFO]</td>
							<td class=td_con1 style="padding-left:5;">
							이용안내 <FONT class=font_blue>(예:&lt;a href=[USEINFO]>이용안내&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[COMPANY]</td>
							<td class=td_con1 style="padding-left:5;">
							회사소개 <FONT class=font_blue>(예:&lt;a href=[COMPANY]>회사소개&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MEMBER]</td>
							<td class=td_con1 style="padding-left:5;">
							회원가입/수정 <FONT class=font_blue>(예:&lt;a href=[MEMBER]>회원가입/수정&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[VENDERPROPOSAL]</td>
							<td class=td_con1 style="padding-left:5;">
							제휴 및 입점문의 <FONT class=font_blue>(예:&lt;a href=[VENDERPROPOSAL]>제휴 및 입점문의&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LOGIN]</td>
							<td class=td_con1 style="padding-left:5;">
							로그인 <FONT class=font_blue>(예:&lt;a href=[LOGIN]>로그인&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LOGOUT]</td>
							<td class=td_con1 style="padding-left:5;">
							로그아웃 <FONT class=font_blue>(예:&lt;a href=[LOGOUT]>로그아웃&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER]</td>
							<td class=td_con1 style="padding-left:5;">
							주문조회 <FONT class=font_blue>(예:&lt;a href=[ORDER]>주문조회&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[RESERVEVIEW]</td>
							<td class=td_con1 style="padding-left:5;">
							적립금 <FONT class=font_blue>(예:&lt;a href=[RESERVEVIEW]>적립금&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BOARD]</td>
							<td class=td_con1 style="padding-left:5;">
							게시판 <FONT class=font_blue>(예:&lt;a href=[BOARD]>게시판&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[AUCTION]</td>
							<td class=td_con1 style="padding-left:5;">
							경매 <FONT class=font_blue>(예:&lt;a href=[AUCTION]>경매&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TODAYSALE]</td>
							<td class=td_con1 style="padding-left:5;">
							투데이세일 <FONT class=font_blue>(예:&lt;a href=[TODAYSALE]>투데이세일&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[GONGGU]</td>
							<td class=td_con1 style="padding-left:5;">
							공동구매 <FONT class=font_blue>(예:&lt;a href=[GONGGU]>공동구매&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ESTIMATE]</td>
							<td class=td_con1 style="padding-left:5;">
							온라인견적서 <FONT class=font_blue>(예:&lt;a href=[ESTIMATE]>온라인견적서&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[EMAIL]</td>
							<td class=td_con1 style="padding-left:5;">
							전자메일 <FONT class=font_blue>(예:&lt;a href=[EMAIL]>메일&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[RSS]</td>
							<td class=td_con1 style="padding-left:5;">
							RSS 바로가기 <FONT class=font_blue>(예:&lt;a href=[RSS]>RSS&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						</table>
						</td>
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