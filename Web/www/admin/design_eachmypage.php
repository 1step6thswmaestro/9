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
$body=$_POST["body"];
$intitle=$_POST["intitle"];

if($intitle=="Y") {
	$leftmenu="Y";
} else {
	$leftmenu="N";
}


$subject = 'MyPage 화면';

$insertKey = 'mypage';

// 백업 / 복구
if ( $type=="store" OR $type=="restore" ) {
	$MSG = adminDesingBackup ( $type, $insertKey, $body, $subject, '', '', $leftmenu );
	$MSG .= adminDesingBackup ( $type, 'design_mypage', 'U', $subject, '', '', '', 'tblshopinfo', 'design_mypage' );
	$onload="<script>alert(\"".$MSG."\");</script>";
}


if($type=="update" && strlen($body)>0) {

	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='mypage' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT tbldesignnewpage SET ";
		$sql.= "type		= 'mypage', ";
		$sql.= "subject		= 'MyPage 화면', ";
		$sql.= "leftmenu	= '".$leftmenu."', ";
		$sql.= "body		= '".$body."' ";
		mysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET ";
		$sql.= "leftmenu	= '".$leftmenu."', ";
		$sql.= "body		= '".$body."' ";
		$sql.= "WHERE type='mypage' ";
		mysql_query($sql,get_db_conn());
	}
	mysql_free_result($result);

	$sql = "UPDATE tblshopinfo SET design_mypage='U' ";
	mysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>alert(\"MyPage 화면 디자인 수정이 완료되었습니다.\");</script>";
} else if($type=="delete") {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='mypage' ";
	mysql_query($sql,get_db_conn());

	$sql = "UPDATE tblshopinfo SET design_mypage='001' ";
	mysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>alert(\"MyPage 화면 디자인 삭제가 완료되었습니다.\");</script>";
} else if($type=="clear") {
	$intitle="";
	$body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='mypage' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$body=$row->body;
	}
	mysql_free_result($result);
}

if($type!="clear") {
	$body="";
	$intitle="";
	$sql = "SELECT leftmenu,body FROM tbldesignnewpage WHERE type='mypage' ";
	$result = mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$body=$row->body;
		$intitle=$row->leftmenu;
	} else {
		$intitle="Y";
	}
	mysql_free_result($result);
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.body.value.length==0) {
			alert("MyPage 화면 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("MyPage 화면 디자인을 삭제하시겠습니까?")) {
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

	// 미리보기
	if(type=="preview") {
		if(document.form1.body.value.length==0) {
			alert("MyPage 화면 디자인 내용을 입력하세요.");
			document.form1.body.focus();
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

//-->
</SCRIPT>
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt; <span class="2depth_select">MyPage 화면 꾸미기</span></td>
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
					<TD><IMG SRC="images/design_mypageview_title.gif" ALT=""></TD>
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
					<TD width="100%" class="notice_blue">MyPage 화면 디자인을 자유롭게 디자인 하실 수 있습니다.</TD>
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
					<TD><IMG SRC="images/design_mypageview_stitle.gif" WIDTH="250" HEIGHT=31 ALT=""></TD>
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
			<tr><td height="3"></td></tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td style="padding-top:2px;"><textarea name=body style="WIDTH: 100%; HEIGHT: 300px" class="textarea"><?=htmlspecialchars($body)?></textarea><br><input type=checkbox name=intitle value="Y" <?if($intitle=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"> <b><span style="letter-spacing:-0.5pt;"><span class="font_orange">기본 타이틀 이미지 유지 - 타이틀 이하 부분부터 디자인 변경</span>(미체크시 기존 타이틀 이미지 없어짐으로 직접 편집하여 사용)</b></span></td>
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
					<TD COLSPAN=3 width="100%" valign="top" class=menual_bg style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><p class="LIPoint"><b><span class="font_orange">MyPage 매크로명령어</span></b>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</p></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><p>&nbsp;</p></td>
						<td >
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>마이페이지 메뉴관련 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYHOME]</td>
							<td class=td_con1 style="padding-left:5;">
							마이페이지 홈 <FONT class=font_blue>(예:&lt;a href=[MENU_MYHOME]>마이페이지 홈&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYORDER]</td>
							<td class=td_con1 style="padding-left:5;">
							주문내역 <FONT class=font_blue>(예:&lt;a href=[MENU_MYORDER]>주문내역&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYPERSONAL]</td>
							<td class=td_con1 style="padding-left:5;">
							1:1고객게시판 <FONT class=font_blue>(예:&lt;a href=[MENU_MYPERSONAL]>1:1고객게시판&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYWISH]</td>
							<td class=td_con1 style="padding-left:5;">
							위시리스트 <FONT class=font_blue>(예:&lt;a href=[MENU_MYWISH]>위시리스트&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYRESERVE]</td>
							<td class=td_con1 style="padding-left:5;">
							적립금 <FONT class=font_blue>(예:&lt;a href=[MENU_MYRESERVE]>적립금&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYCOUPON]</td>
							<td class=td_con1 style="padding-left:5;">
							쿠폰 <FONT class=font_blue>(예:&lt;a href=[MENU_MYCOUPON]>쿠폰&lt;/a>)</font>
							</td>
						</tr>
						<? if($_shopdata->recom_url_ok == "Y" || $_shopdata->sns_ok == "Y"){ ?>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_PROMOTE]</td>
							<td class=td_con1 style="padding-left:5;">
							홍보내역 <FONT class=font_blue>(예:&lt;a href=[MENU_PROMOTE]>홍보내역&lt;/a>)</font>
							</td>
						</tr>
						<? } ?>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_GONGGU]</td>
							<td class=td_con1 style="padding-left:5;">
							공동구매 <FONT class=font_blue>(예:&lt;a href=[MENU_GONGGU]>공동구매&lt;/a>)</font>
							</td>
						</tr>
						<? if(getVenderUsed()==true) { ?>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYCUSTSECT]</td>
							<td class=td_con1 style="padding-left:5;">
							단골매장 <FONT class=font_blue>(예:&lt;a href=[MENU_MYCUSTSECT]>단골매장&lt;/a>)</font>
							</td>
						</tr>
						<? } ?>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYINFO]</td>
							<td class=td_con1 style="padding-left:5;">
							회원정보수정 <FONT class=font_blue>(예:&lt;a href=[MENU_MYINFO]>회원정보수정&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYOUT]</td>
							<td class=td_con1 style="padding-left:5;">
							회원탈퇴 <FONT class=font_blue>(예:&lt;a href=[MENU_MYOUT]>회원탈퇴&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=10></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>회원관련 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ID]</td>
							<td class=td_con1 style="padding-left:5;">
							회원 아이디
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NAME]</td>
							<td class=td_con1 style="padding-left:5;">
							회원 이름
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[EMAIL]</td>
							<td class=td_con1 style="padding-left:5;">
							회원 이메일 <FONT class=font_blue>(예:&lt;a href="mailto:[EMAIL]">[EMAIL]&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ADDRESS1]</td>
							<td class=td_con1 style="padding-left:5;">
							기본주소
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ADDRESS2]</td>
							<td class=td_con1 style="padding-left:5;">
							상세주소
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TEL]</td>
							<td class=td_con1 style="padding-left:5;">
							집전화
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MOBILE]</td>
							<td class=td_con1 style="padding-left:5;">
							휴대전화
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[RESERVE]</td>
							<td class=td_con1 style="padding-left:5;">
							현재 적립금 <FONT class=font_blue>(예:[RESERVE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[RESERVE_MORE]</td>
							<td class=td_con1 style="padding-left:5;">
							적립금 내역 보기 <FONT class=font_blue>(예:&lt;a href=[RESERVE_MORE]>적립금 내역 보기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[COUPON]</td>
							<td class=td_con1 style="padding-left:5;">
							현재 쿠폰수 <FONT class=font_blue>(예:[COUPON]장)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[COUPON_MORE]</td>
							<td class=td_con1 style="padding-left:5;">
							쿠폰 내역 조회 <FONT class=font_blue>(예:&lt';a href=[COUPON_MORE]>쿠폰 내역 보기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[GIFT_AUTH]</td>
							<td class=td_con1 style="padding-left:5;">
							상품권 인증 <FONT class=font_blue>(예:&lt';a href=[GIFT_AUTH]> 인증 &lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>

						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDERCOUNT]</td>
							<td class=td_con1 style="padding-left:5;">
							주문현황 건수 <FONT class=font_blue>(예:[ORDERCOUNT]건)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[DELIREADY]</td>
							<td class=td_con1 style="padding-left:5;">
							발송준비 건수 <FONT class=font_blue>(예:[DELIREADY]건)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[DELICOMPLATE]</td>
							<td class=td_con1 style="padding-left:5;">
							발송완료 건수 <FONT class=font_blue>(예:[DELICOMPLATE]건)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[REFUND]</td>
							<td class=td_con1 style="padding-left:5;">
							환불신청 건수 <FONT class=font_blue>(예:[REFUND]건)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[REPAYMENT]</td>
							<td class=td_con1 style="padding-left:5;">
							환불완료 건수 <FONT class=font_blue>(예:[REPAYMENT]건)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>


						<tr><td colspan=2 height=10></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>특별회원 관련 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15" nowrap>[IFROYAL]<br>[IFENDROYAL]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							특별회원에 대한 내용 기술 (특별회월일 경우에만 내용 출력)
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFROYAL]</B>
      내용
   <B>[IFENDROYAL]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ROYAL_IMG]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							특별회원 이미지 표시
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ROYAL_MSG1]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							특별회원 관련 메세지1 - 자동출력
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15" nowrap>[IFROYALMSG2]<br>[IFENDROYALMSG2]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							등급 속성이 없는 등급은 메세지2가 필요없기 때문에 사용해야함
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFROYALMSG2]</B>
      내용 (예:[ROYAL_MSG2])
   <B>[IFENDROYALMSG2]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ROYAL_MSG2]</td>
							<td class=td_con1 style="padding-left:5;">
							특별회원 관련 메세지2 <FONT class=font_blue>(예:[IFROYALMSG2] [ROYAL_MSG2] [IFENDROYALMSG2])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>

						<tr>
							<td class=table_cell align=right style="padding-right:15">[IF_MEMBER_S]<br />[IF_MEMBER_E]</td>
							<td class=td_con1 style="padding-left:5;">
								회원등급 추가정보 시작/끝<br />
								<FONT class=font_blue>
									사용 예:<br />
									<b>[IF_MEMBER_S]</b><br />
									&nbsp;&nbsp;[NEXT_GRP_NAME] 등급까지 남은 구매금액 : [NEXT_GRP_R_PRICE]원<br />
									<b>[IF_MEMBER_E]</b>
								</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NEXT_GRP_NAME]</td>
							<td class=td_con1 style="padding-left:5;">
								다음 높은 회원 등급 명칭 <FONT class=font_blue>(예: 다음 회원 등급 : [NEXT_GRP_NAME])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NEXT_GRP_R_PRICE]</td>
							<td class=td_con1 style="padding-left:5;">
								남은 구매금액 <FONT class=font_blue>(예: 남은 구매금액 : [NEXT_GRP_R_PRICE])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NEXT_GRP_R_CNT]</td>
							<td class=td_con1 style="padding-left:5;">
								남은 구매건수 <FONT class=font_blue>(예: 남은 구매건수 : [NEXT_GRP_R_CNT])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NEXT_GRP_DATE_S]</td>
							<td class=td_con1 style="padding-left:5;">
								구매기간(누적구매금액 산정기간) 시작
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NEXT_GRP_DATE_E]</td>
							<td class=td_con1 style="padding-left:5;">
								구매기간(누적구매금액 산정기간) 끝
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NEXT_GRP_CNT]</td>
							<td class=td_con1 style="padding-left:5;">
								구매건수
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NEXT_GRP_KEEP]</td>
							<td class=td_con1 style="padding-left:5;">
								등급유지기간
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NEXT_GRP_PRICE]</td>
							<td class=td_con1 style="padding-left:5;">
								구매금액
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>





						<tr><td colspan=2 height=10></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>SNS 채널 및 홍보URL관련 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15" nowrap>[IFHONGBO]<br>[IFENDHONGBO]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							홍보URL 관련 주소 및 적립금관련정보
							<pre style="line-height:15px">  <FONT class=font_blue><B>[IFHONGBO]</B>
내용(예:
	[NAME]님의 소개로 현재까지 &lt;B>[MEMBERCNT]명&lt;/B>의 친구가 가입하였습니다.
	[NAME]님만의 고유한 URL 주소는 &lt;B>[MEMHONGBOURL]&lt;/B>입니다. &lt;a href=[HONGBOPOPUP]>소문내기&lt;a>
	[MEMADDRESERVE]
	)
   <B>[IFENDHONGBO]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MEMBERCNT]</td>
							<td class=td_con1 width=100% style="padding-left:5;">나를 추천한 인원수 </td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MEMHONGBOURL]</td>
							<td class=td_con1 width=100% style="padding-left:5;">나의 홍보URL주소 </td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[HONGBOPOPUP]</td>
							<td class=td_con1 width=100% style="padding-left:5;">소문내기 새창링크 </td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MEMADDRESERVE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">추천을 통한 추가적립금 정보 </td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15" nowrap>[MEMSNSINFO]</td>
							<td class=td_con1 width=100% style="padding-left:5;"> SNS 채널 정보</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=10></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>최근 주문내역 관련 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDTAB1_기본이미지URL_선택된이미지URL]</td>
							<td class=td_con1 style="padding-left:5;">
							일반상품주문조회 이미지 버튼
							<br><FONT class=font_blue>(예:[ORDTAB1_/<?=RootPath.DataDir?>design/menutab01off.gif_/<?=RootPath.DataDir?>design/menutab01on.gif])</font>
							<br>
							이미지 URL에 "_" 사용불가
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDTAB2_기본이미지URL_선택된이미지URL]</td>
							<td class=td_con1 style="padding-left:5;">
							공동구매주문조회 이미지 버튼
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDTAB3_기본이미지URL_선택된이미지URL]</td>
							<td class=td_con1 style="padding-left:5;">
							상품권주문조회 이미지 버튼
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[IFGIFTCARD]<br>[IFELSEGIFTCARD]<br>[IFENDGIFTCARD]</td>
							<td class=td_con1 style="padding-left:5;">
							상품권주문 필드 출력
							<br><FONT class=font_blue>(예:
							<br>&lt;td>[IFGIFTCARD]인증처리상태[IFELSEGIFTCARD]배송상태[IFENDGIFTCARD]&lt;/td>
							<br>&lt;td>[IFGIFTCARD]인증번호[IFELSEGIFTCARD]배송추적[IFENDGIFTCARD]&lt;/td></font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_MORE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							최근 주문내역 전체보기 <FONT class=font_blue>(예:&lt;a href=[ORDER_ALL]>전체보기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15" nowrap>[IFORDER]<br>[IFELSEORDER]<br>[IFENDORDER]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							주문 내역이 있을 경우와 없을 경우
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFORDER]</B>
      주문 내역이 <FONT COLOR="red"><B>있을</B></FONT> 경우의 내용
   <B>[IFELSEORDER]</B>
      주문 내역이 <FONT COLOR="red"><B>없을</B></FONT> 경우의 내용
   <B>[IFENDORDER]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15" nowrap>[FORORDER]<br>[FORENDORDER]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							[FORORDER] 주문 내역 하나에 대한 내용 [FORENDORDER]
							<pre style="line-height:15px">
<FONT class=font_blue>   [IFORDER]
       <B>[FORORDER]</B>주문 내역 하나에 대한 내용 기술<B>[FORENDORDER]</B>
   [IFELSEORDER]
       주문내역이 없습니다.
   [IFENDORDER]</font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[FORPRODUCT]<br>[FORENDPRODUCT]</td>
							<td class=td_con1 style="padding-left:5;">
							[FORPRODUCT] 주문내역 하나에 대한 상품목록 및 배송정보 내용 [FORENDPRODUCT]
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_NAME]</td>
							<td class=td_con1 style="padding-left:5;">
							주문상품명 - [FORPRODUCT] [FORENDPRODUCT] 내용에 사용
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_DELISTAT]</td>
							<td class=td_con1 style="padding-left:5;">
							배송상태 - [FORPRODUCT] [FORENDPRODUCT] 내용에 사용
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[IFDELISEARCH]<br>[IFELSEDELISEARCH]<br>[IFENDDELISEARCH]</td>
							<td class=td_con1 style="padding-left:5;">
							배송 완료된 상품의 배송정보가 있을 경우와 없을 경우 - [FORPRODUCT] [FORENDPRODUCT]에 사용
							<pre style="line-height:15px">
<font class=font_blue>   <B>[IFDELISEARCH]</B>
	   배송 완료된 상품의 배송정보가 <FONT COLOR="red"><B>있을</B></FONT> 경우의 내용
   <B>[IFELSEDELISEARCH]</B>
	   배송 완료된 상품의 배송정보가 <FONT COLOR="red"><B>없을</B></FONT> 경우의 내용
   <B>[IFENDDELISEARCH]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_DELICOM]</td>
							<td class=td_con1 style="padding-left:5;">
							택배사명 - 위 [IFDELISEARCH]내 사용
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_DELISEARCH]</td>
							<td class=td_con1 style="padding-left:5;">
							배송추적 버튼 (예:&lt;a href=[ORDER_DELISEARCH]>배송추적&lt;/a>) - 위 [IFDELISEARCH]내 사용
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_DATE]</td>
							<td class=td_con1 style="padding-left:5;">
							주문일자
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_METHOD]</td>
							<td class=td_con1 style="padding-left:5;">
							결제방법
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_PRICE]</td>
							<td class=td_con1 style="padding-left:5;">
							결제금액 <FONT class=font_blue>(예:[ORDER_PRICE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_DETAIL]</td>
							<td class=td_con1 style="padding-left:5;">
							주문상세 버튼 함수 <FONT class=font_blue>(예:&lt;a href=[ORDER_DETAIL]>주문상세조회&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_DELI]</td>
							<td class=td_con1 style="padding-left:5;">
							배송현황 버튼 함수 <FONT class=font_blue>(예:&lt;a href=[ORDER_DELI]>배송현황조회&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[REVIEW_WRITE]</td>
							<td class=td_con1 style="padding-left:5;">상품평 작성하기 버튼</td>
						</tr>


						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=10></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>최근 문의내역 관련 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PERSONAL_MORE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							최근 문의내역 전체보기 <FONT class=font_blue>(예:&lt;a href=[PERSONAL_MORE]>전체보기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15" nowrap>[IFPERSONAL]<br>[IFELSEPERSONAL]<br>[IFENDPERSONAL]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							문의 내역이 있을 경우와 없을 경우
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFPERSONAL]</B>
      문의 내역이 <FONT COLOR="red"><B>있을</B></FONT> 경우의 내용
   <B>[IFELSEPERSONAL]</B>
      문의 내역이 <FONT COLOR="red"><B>없을</B></FONT> 경우의 내용
   <B>[IFENDPERSONAL]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15" nowrap>[FORPERSONAL]<br>[FORENDPERSONAL]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							[FORPERSONAL] 문의 내역 하나에 대한 내용 [FORENDPERSONAL]
							<pre style="line-height:15px">
<FONT class=font_blue>   [IFPERSONAL]
       <B>[FORPERSONAL]</B>문의 내역 하나에 대한 내용 기술<B>[FORENDPERSONAL]</B>
   [IFELSEPERSONAL]
       문의내역이 없습니다.
   [IFENDPERSONAL]</font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PERSONAL_DATE]</td>
							<td class=td_con1 style="padding-left:5;">
							문의날짜
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PERSONAL_SUBJECT]</td>
							<td class=td_con1 style="padding-left:5;">
							문의제목
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PERSONAL_REPLY]</td>
							<td class=td_con1 style="padding-left:5;">
							답변여부
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PERSONAL_REDATE]</td>
							<td class=td_con1 style="padding-left:5;">
							답변날짜
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>

						<tr><td colspan=2 height=10></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>위시리스트 관련 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[WISH_MORE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							위시리스트 전체보기 <FONT class=font_blue>(예:&lt;a href=[WISH_MORE]>전체보기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[WISH_LIST???]</td>
							<td class=td_con1 style="padding-left:5;">
							위시리스트 목록
									<br><img width=10 height=0>
									<FONT class=font_orange>? : 상품 표시 개수 (1~8)</FONT>
									<br><img width=10 height=0>
									<FONT class=font_orange>? : 상품 시중가격 표시여부(Y/N)</FONT>
									<br><img width=10 height=0>
									<FONT class=font_orange>? : 상품 적립금 표시여부(Y/N)</FONT>
									<br>
									<FONT class=font_blue>예) [WISH_LIST5NY]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>

						<tr><td colspan=2 height=10></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>정보수신여부 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[RECIVEMAIL]</td>
							<td class=td_con1 style="padding-left:5px;">이메일 수신여부</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[RECIVESMS]</td>
							<td class=td_con1 style="padding-left:5px;">SMS 수신여부</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LASTLOGIN]</td>
							<td class=td_con1 style="padding-left:5px;">마지막 로그인 시간</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>

						</table>
						</td>
					</tr>
					<tr>
						<td width="20" colspan="2"><p>&nbsp;</p></td>
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