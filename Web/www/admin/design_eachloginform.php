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
$login_body=$_POST["login_body"];
$logout_body=$_POST["logout_body"];

$subject = '로그인폼';

$insertKey = "loginform";

// 백업 / 복구
if ( $type=="store" OR $type=="restore" ) {
	$MSG = adminDesingBackup ( $type, 'loginform', $login_body, $subject );
	$MSG = adminDesingBackup ( $type, 'logoutform', $logout_body, $subject );
	$onload="<script>alert(\"".$MSG."\");</script>";
}


if($type=="update") {
	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage ";
	$sql.= "WHERE type='loginform' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT tbldesignnewpage SET ";
		$sql.= "type		= 'loginform', ";
		$sql.= "subject		= '로그인폼', ";
		$sql.= "body		= '".$login_body."' ";
		mysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET ";
		$sql.= "body		= '".$login_body."' ";
		$sql.= "WHERE type='loginform' ";
		mysql_query($sql,get_db_conn());
	}
	mysql_free_result($result);

	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage ";
	$sql.= "WHERE type='logoutform' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT tbldesignnewpage SET ";
		$sql.= "type		= 'logoutform', ";
		$sql.= "subject		= '로그아웃폼', ";
		$sql.= "body		= '".$logout_body."' ";
		mysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET ";
		$sql.= "body		= '".$logout_body."' ";
		$sql.= "WHERE type='logoutform' ";
		mysql_query($sql,get_db_conn());
	}
	mysql_free_result($result);

	$onload="<script>alert(\"로그인/로그아웃 디자인 수정이 완료되었습니다.\");</script>";
} else if($type=="delete") {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='loginform' ";
	mysql_query($sql,get_db_conn());
	$sql = "DELETE FROM tbldesignnewpage WHERE type='logoutform' ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert(\"로그인/로그아웃 디자인 삭제가 완료되었습니다.\");</script>";
} else if($type=="clear") {
	$login_body="";
	$logout_body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='loginform' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$login_body=$row->body;
	}
	mysql_free_result($result);

	$sql = "SELECT body FROM tbldesigndefault WHERE type='logoutform' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$logout_body=$row->body;
	}
	mysql_free_result($result);
}



if($type!="clear") {
	$sql = "SELECT body FROM tbldesignnewpage WHERE type='loginform' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$login_body=$row->body;
	mysql_free_result($result);

	$sql = "SELECT body FROM tbldesignnewpage WHERE type='logoutform' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$logout_body=$row->body;
	mysql_free_result($result);
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.login_body.value.length==0) {
			alert("로그인 디자인 내용을 입력하세요.");
			document.form1.login_body.focus();
			return;
		}
		if(document.form1.logout_body.value.length==0) {
			alert("로그아웃 디자인 내용을 입력하세요.");
			document.form1.logout_body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("로그인/로그아웃 디자인을 삭제하시겠습니까?")) {
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


}

//매크로 보기(팝업)
function macroview(){
	window.open("http://www.getmall.co.kr/macro/pages/loginform_macro.html","loginform_macro","height=800,width=680,scrollbars=no");
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; 개별디자인-메인 및 상하단  &gt; <span class="2depth_select">로그인폼 꾸미기</span></td>
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
					<TD><IMG SRC="images/design_eachloginform_title.gif" ALT=""></TD>
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
					<TD width="100%" class="notice_blue">
						1) 로그인/로그아웃 디자인을 자유롭게 관리하실 수 있습니다.&nbsp;&nbsp;&nbsp;<a href="javascript:macroview();"><img src="images/btn_macroview.gif" border="0" align="absmiddle" alt="" /></a>&nbsp;&nbsp;<a href="http://www.getmall.co.kr/macro/data/loginform_design.zip"><img src="images/btn_defaultcode.gif" border="0" align="absmiddle" alt="" /></a><br />
						2) <span class="font_orange" style="font-size:11px;"><u>메인 본문 매크로 관련 파일</u> : <b>/main/topp.php</b> (파일 수정시 기존 파일은 반드시 백업하시기 바랍니다.)</span><br />
						3) 매뉴얼의 <b>매크로명령어</b>를 참조하여 디자인 하세요.<br />
						4) [기본값복원]+[적용하기], [삭제하기]하면 기존 사용하던 매인 템플릿에 속한 디자인으로 변경됩니다.
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
			<tr><td height="20"></td></tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/design_eachlog_stitle1.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt;"><textarea name=login_body style="WIDTH: 100%; HEIGHT: 150px" class="textarea"><?=htmlspecialchars($login_body)?></textarea></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/design_eachlog_stitle2.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt;"><textarea name=logout_body style="WIDTH: 100%; HEIGHT: 150px" class="textarea"><?=htmlspecialchars($logout_body)?></textarea></td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
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
					<TD COLSPAN=3 width="100%" valign="top" class="menual_bg" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"  class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><p class="LIPoint"><B><span class="font_orange">로그인 매크로명령어</span></B>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</p></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"></td>
						<td   style="padding-top:3pt; padding-bottom:10pt;">
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>로그인 입력폼 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>

						<tr>
							<td class=table_cell align=right style="padding-right:15">[LOGINFORMSTART][LOGINFORMEND]</td>
							<td class=td_con1 style="padding-left:5;">
								회원 로그인폼<br /><br />
								<b>[로그인폼 예]</b><br />
								<FONT class=font_blue>
									<b>[LOGINFORMSTART]</b><br />
										&lt;table boder=0 cellpadding=0 cellsapcing=0&gt;<br />
										&nbsp;&nbsp;&lt;tr&gt;<br />
										&nbsp;&nbsp;&nbsp;&nbsp;&lt;td&gt;아이디 : [ID_110]&lt;/td&gt;<br />
										&nbsp;&nbsp;&nbsp;&nbsp;&lt;td&gt;비밀번호 : [PASSWD_100]&lt;/td&gt;<br />
										&nbsp;&nbsp;&nbsp;&nbsp;&lt;td&gt;&lt;a href=[OK]&gt;로그인&lt/a&gt;&lt;/td&gt;<br />
										&nbsp;&nbsp;&lt;/tr&gt;<br />
										&lt;/table&gt;<br />
									<b>[LOGINFORMEND]</b>
								</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>

						<tr>
							<td class=table_cell align=right style="padding-right:15">[ID_???]</td>
							<td class=td_con1 style="padding-left:5;">
							아이디 입력폼, <FONT class=font_orange>(_??? : 입력폼 사이즈(픽셀단위), 예)[ID_110]=>&lt;input type=text name=id style="width:110">)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PASSWD_???]</td>
							<td class=td_con1 style="padding-left:5;">
							패스워드 입력폼, <FONT class=font_orange>(_??? : 아이디 입력폼과 동일)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[OK]</td>
							<td class=td_con1 style="padding-left:5;">
							로그인 확인버튼 <FONT class=font_blue>(예:&lt;a href=[OK]>확인&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SSLCHECK]</td>
							<td class=td_con1 style="padding-left:5;">
							보안접속 체크박스
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SSLINFO]</td>
							<td class=td_con1 style="padding-left:5;">
							보안접속 안내페이지 링크 <FONT class=font_blue>(예:&lt;a href=[SSLINFO]>보안접속&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[JOIN]</td>
							<td class=td_con1 style="padding-left:5;">
							신규회원가입 <FONT class=font_blue>(예:&lt;a href=[JOIN]>회원가입&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[FINDPWD]</td>
							<td class=td_con1 style="padding-left:5;">
							패스워드분실 <FONT class=font_blue>(예:&lt;a href=[FINDPWD]>패스워드분실&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LOGIN]</td>
							<td class=td_con1 style="padding-left:5;">
							로그인 입력폼이 아닌 링크방식으로 할 경우 <FONT class=font_blue>(예:&lt;a href=[LOGIN]>로그인&lt;/a>) </font> : 로그인 페이지로 이동됩니다.
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TARGET]</td>
							<td class=td_con1 style="padding:5;line-height:15px">
							투프레임 방식의 쇼핑몰에서 상단에 로그인 폼을 보이게 할 경우 사용됨
							<br><FONT class=font_blue>(예:&lt;a href=/front/mypage.php [TARGET]>마이페이지&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>로그아웃 입력폼 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ID]</td>
							<td class=td_con1 style="padding-left:5;">
							회원아이디
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NAME]</td>
							<td class=td_con1 style="padding-left:5;">
							회원이름
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[RESERVE]</td>
							<td class=td_con1 style="padding-left:5;">
							회원적립금
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LOGOUT]</td>
							<td class=td_con1 style="padding-left:5;">
							로그아웃 버튼 <FONT class=font_blue>(예:&lt;a href=[LOGOUT]>로그아웃&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MEMBEROUT]</td>
							<td class=td_con1 style="padding-left:5;">
							회원탈퇴 버튼 <FONT class=font_blue>(예:&lt;a href=[MEMBEROUT]>회원탈퇴&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MEMBER]</td>
							<td class=td_con1 style="padding-left:5;">
							회원정보수정 버튼 <FONT class=font_blue>(예:&lt;a href=[MEMBER]>정보수정&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MYPAGE]</td>
							<td class=td_con1 style="padding-left:5;">
							마이페이지 버튼 <FONT class=font_blue>(예:&lt;a href=[MYPAGE]>마이페이지&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TARGET]</td>
							<td class=td_con1 style="padding:5;line-height:15px">
							투프레임 방식의 쇼핑몰에서 상단에 로그인 폼을 보이게 할 경우 사용됨
							<br><FONT class=font_blue>(예:&lt;a href=/front/mypage.php [TARGET]>마이페이지&lt;/a>)</font>
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