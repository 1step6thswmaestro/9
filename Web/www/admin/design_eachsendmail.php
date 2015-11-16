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
$mail_type=$_POST["mail_type"];
$subject=$_POST["subject"];
$body=$_POST["body"];

$insertKey = $mail_type;

// 백업 / 복구
if ( $type=="store" OR $type=="restore" ) {
	$MSG = adminDesingBackup ( $type, $mail_type, $body, $subject );
	$onload="<script>alert(\"".$MSG."\");</script>";
}


if($type=="update" && strlen($mail_type)>0 && strlen($body)>0 && strlen($subject)>0) {
	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='".$mail_type."' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT tbldesignnewpage SET ";
		$sql.= "type		= '".$mail_type."', ";
		$sql.= "subject		= '".$subject."', ";
		$sql.= "body		= '".$body."' ";
		mysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET ";
		$sql.= "subject		= '".$subject."', ";
		$sql.= "body		= '".$body."' ";
		$sql.= "WHERE type='".$mail_type."' ";
		mysql_query($sql,get_db_conn());
	}
	mysql_free_result($result);
	$onload="<script>alert(\"해당 메일화면 디자인 수정이 완료되었습니다.\");</script>";
} else if($type=="delete" && strlen($mail_type)>0) {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='".$mail_type."' ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert(\"해당 메일화면 디자인 삭제가 완료되었습니다.\");</script>";
} else if($type=="clear" && strlen($mail_type)>0) {
	if($mail_type=="joinmail") {
		$subject="[SHOP] 가입 축하 메일입니다.";
	} else if($mail_type=="ordermail") {
		$subject="[SHOP] 주문내역서 확인 메일입니다.";
	} else if($mail_type=="delimail") {
		$subject="[SHOP] 상품 발송 메일입니다.";
	} else if($mail_type=="bankmail") {
		$subject="[SHOP] 입금 확인 메일입니다.";
	} else if($mail_type=="passmail") {
		$subject="[SHOP] 패스워드 안내메일입니다.";
	} else if($mail_type=="authmail") {
		$subject="[SHOP] 회원 인증 메일입니다.";
	}
	$body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='".$mail_type."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$body=$row->body;
	}
	mysql_free_result($result);
}

if($type!="clear") {
	$subject="";
	$body="";
	if(strlen($mail_type)>0) {
		$sql = "SELECT subject,body FROM tbldesignnewpage WHERE type='".$mail_type."' ";
		$result = mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$subject=$row->subject;
			$body=$row->body;
		}
		mysql_free_result($result);
	}
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.mail_type.value.length==0) {
			alert("해당 메일화면을 선택하세요.");
			document.form1.mail_type.focus();
			return;
		}
		if(document.form1.subject.value.length==0) {
			alert("해당 메일제목을 입력하세요.");
			document.form1.subject.focus();
			return;
		}
		if(document.form1.body.value.length==0) {
			alert("해당 메일화면 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("해당 메일화면 디자인을 삭제하시겠습니까?")) {
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
		if(document.form1.mail_type.value.length==0) {
			alert("해당 메일화면을 선택하세요.");
			document.form1.mail_type.focus();
			return;
		}
		if(document.form1.subject.value.length==0) {
			alert("해당 메일제목을 입력하세요.");
			document.form1.subject.focus();
			return;
		}
		if(document.form1.body.value.length==0) {
			alert("해당 메일화면 디자인 내용을 입력하세요.");
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

function change_page(val) {
	document.form1.type.value="change";
	document.form1.submit();
}

//매크로 보기(팝업)
function macroview(){
	window.open("http://www.getmall.co.kr/macro/pages/sendmail_macro.html","sendmail_macro","height=800,width=680,scrollbars=no");
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt; <span class="2depth_select">메일 화면 꾸미기</span></td>
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
					<TD><IMG SRC="images/design_eachsendmail_title.gif" ALT=""></TD>
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
					<TD width="100%" class="notice_blue"><p>메일 화면 디자인을 자유롭게 디자인 하실 수 있습니다.</p></TD>
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
					<TD><IMG SRC="images/design_eachsendmail_stitle1.gif" WIDTH="190" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">
						&nbsp;&nbsp;<a href="javascript:macroview();"><img src="images/btn_macroview.gif" border="0" align="absmiddle" alt="" /></a>&nbsp;&nbsp;<a href="http://www.getmall.co.kr/macro/data/sendmail_design.zip"><img src="images/btn_defaultcode.gif" border="0" align="absmiddle" alt="" />
					</TD>
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
					<TD width="100%" class="notice_blue">1) 매뉴얼의 <b>매크로명령어</b>를 참조하여 디자인 하세요. - 메일본분  내용만 변경 가능합니다.<br>2) [기본값복원]+[적용하기] 하면 기본 템플릿의 디자인으로 변경됩니다.<br>3) [삭제하기] -> 기존 사용하던 메인 템플릿에 속한 디자인으로 변경됩니다.(메일화면은 별도의 템플릿이 제공되지 않습니다.)<br>4) <b>메일 제목 필수, 이미지 경로 사용시 쇼핑몰 주소 반드시 입력</b><br>&nbsp;&nbsp;&nbsp;&nbsp;(예 : http://www.abc.co.kr/design/상점ID/이미지명.gif 또는 http://[URL]/design/상점ID/이미지명.gif)</TD>
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
				<td style="padding-top:3pt;">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				<col width=139></col>
				<col width=></col>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">메일 화면 선택</TD>
					<TD class="td_con1"><select name=mail_type onchange="change_page(options.value)" style="width:330px;" class="select">
						<option value="">메일 화면을 선택하세요.</option>
<?
			$mail_list=array("신규 회원가입 축하 메일","주문 신청 확인 메일","주문 발송 메일","주문 입금 확인 메일","아이디/패스워드 안내 메일","회원인증 메일 (B2B 인증시에만)");
			$mail_code=array("joinmail","ordermail","delimail","bankmail","passmail","authmail");
			for($i=0;$i<count($mail_list);$i++) {
				echo "<option value=\"".$mail_code[$i]."\" ";
				if($mail_type==$mail_code[$i]) echo "selected";
				echo ">".$mail_list[$i]."</option>\n";
			}
?>
						</select></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">메일 제목</TD>
					<TD class="td_con1"><input type=text name=subject value="<?=$subject?>" size=70 class="input" style="width:98%"></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan="2"><textarea name=body style="WIDTH: 100%; HEIGHT: 300px" class="textarea"><?=htmlspecialchars($body)?></textarea></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('clear');"><img src="images/botteon_bok.gif" width="124" height="38" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" width="113" height="38" border="0" hspace="0"></a><!-- &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('preview');"><img src="images/botteon_prev.gif" width="113" height="38" border="0" hspace="2"></a> -->&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('store');"><img src="images/botteon_store.gif" border="0" hspace="2" alt="백업하기"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('restore');"><img src="images/botteon_restore.gif" border="0" hspace="2" alt="백업복원하기"></a></td>
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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"  class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top">&nbsp;</td>
						<td  style="padding-top:3pt; padding-bottom:10pt;">
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>신규 회원가입 축하 메일</B>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SHOP]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NAME]</td>
							<td class=td_con1 style="padding-left:5;">
							가입 회원 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MESSAGE]</td>
							<td class=td_con1 style="padding-left:5;">
							신규 회원가입 축하 메세지 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[URL]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 URL - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>주문 신청 확인 메일</B>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SHOP]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NAME]</td>
							<td class=td_con1 style="padding-left:5;">
							가입 회원 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[DATE]</td>
							<td class=td_con1 style="padding-left:5;">
							주문일자 - 메일 제목에만 사용가능 예)2006년 05월 03일
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[CURDATE]</td>
							<td class=td_con1 style="padding-left:5;">
							주문일자 - 메일 내용에만 사용가능 예)2006년 05월 03일
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MAILDATA]</td>
							<td class=td_con1 style="padding-left:5;">
							주문서 내역 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MESSAGE]</td>
							<td class=td_con1 style="padding-left:5;">
							주문 메세지 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[URL]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 URL - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>주문 발송 메일</B>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SHOP]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[DELIVERYURL]</td>
							<td class=td_con1 style="padding-left:5;">
							송장추적 URL - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[DELIVERYNUM]</td>
							<td class=td_con1 style="padding-left:5;">
							택배 송장번호 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[DELIVERYCOMPANY]</td>
							<td class=td_con1 style="padding-left:5;">
							택배 회사명 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[URL]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 URL - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[DELIVERYDATE]</td>
							<td class=td_con1 style="padding-left:5;">
							배송날짜 - 메일 내용에만 사용가능 예)2006/05/03
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDERDATE]</td>
							<td class=td_con1 style="padding-left:5;">
							주문날짜 - 메일 내용에만 사용가능 예)2006/05/03
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15;line-height:17px">[IFDELICHANGE][ELSEDELICHANGE]   [ENDDELICHANGE]</td>
							<td class=td_con1 style="padding-left:5;line-height:17px">
							[IFDELICHANGE]물품 발송 후 송장정보만 변경된 경우 메세지[ELSEDELICHANGE]
							<br>물품발송 메세지[ENDDELICHANGE]
							<br>- 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15;line-height:17px">[IFDELINUM] [ENDDELINUM]</td>
							<td class=td_con1 style="padding-left:5;">
							송장번호가 존재할경우 메세지 입력 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15;line-height:17px">[IFDELIURL][ELSEDELIURL]   [ENDDELIURL]</td>
							<td class=td_con1 style="padding-left:5;line-height:17px">
							[IFDELIURL]배송추적시스템을 제공할경우 메세지[ELSEDELIURL]
							<br>배송추적시스템을 제공하지 않을경우 메세지[ENDDELIURL]
							<br>- 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>주문 입금 확인 메일</B>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SHOP]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BANKDATE]</td>
							<td class=td_con1 style="padding-left:5;">
							입금확인 일짜 - 메일 내용에만 사용가능 예)2006/05/03
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDERDATE]</td>
							<td class=td_con1 style="padding-left:5;">
							주문일자 - 메일 내용에만 사용가능 예)2006년 05월 03일
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[URL]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 URL - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>아이디/패스워드 안내 메일</B>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SHOP]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NAME]</td>
							<td class=td_con1 style="padding-left:5;">
							회원 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ID]</td>
							<td class=td_con1 style="padding-left:5;">
							회원 아이디 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PASSWORD]</td>
							<td class=td_con1 style="padding-left:5;">
							회원 비밀번호 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[URL]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 URL - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>회원인증 메일 (B2B에서 관리자가 회원인증시 발송)</B>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SHOP]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ID]</td>
							<td class=td_con1 style="padding-left:5;">
							회원 아이디 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[OKDATE]</td>
							<td class=td_con1 style="padding-left:5;">
							회원 인증일짜 - 메일 내용에만 사용가능 예)2006/05/03
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[URL]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 URL - 메일 내용에만 사용가능
							</td>
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