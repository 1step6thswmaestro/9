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
$code=$_POST["code"];
$top_body=$_POST["top_body"];
$top_height=(int)$_POST["top_height"];
if($top_height==0) $top_height=70;

if(strlen($code)==0) $code="ALL";

$insertKey = "topmenu";

$subject = '상단메뉴화면';
// 백업 / 복구
if ( $type=="store" OR $type=="restore" ) {
	if($code=="ALL") {
		$MSG = adminDesingBackup ( $type, $insertKey, $top_body, $subject, $code, '', '', 'tbldesign', 'body_top' );
		$MSG = adminDesingBackup ( $type, $insertKey, $top_height, $subject, $code, '', '', 'tbldesign', 'top_height' );
	} else {
		$MSG = adminDesingBackup ( $type, $insertKey, $top_body, $subject, $code );
	}
	$onload="<script>alert(\"".$MSG."\");</script>";
}



if($type=="update" && strlen($top_body)>0) {
	//$top_body = ereg_replace("\"\[","[",$top_body);
	//$top_body = ereg_replace("]\"","]",$top_body);
	if($code=="ALL") {
		$sql = "SELECT COUNT(*) as cnt FROM tbldesign ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		if($row->cnt==0) {
			$sql = "INSERT tbldesign SET ";
			$sql.= "body_top	= '".$top_body."', ";
			$sql.= "top_height	= '".$top_height."' ";
			mysql_query($sql,get_db_conn());
		} else {
			$sql = "UPDATE tbldesign SET ";
			$sql.= "body_top	= '".$top_body."', ";
			$sql.= "top_height	= '".$top_height."' ";
			mysql_query($sql,get_db_conn());
		}
		mysql_free_result($result);
	} else {
		$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage ";
		$sql.= "WHERE type='topmenu' AND code='".$code."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		if($row->cnt==0) {
			$sql = "INSERT tbldesignnewpage SET ";
			$sql.= "type		= 'topmenu', ";
			$sql.= "body		= '".$top_body."', ";
			$sql.= "code		= '".$code."' ";
			mysql_query($sql,get_db_conn());
		} else {
			$sql = "UPDATE tbldesignnewpage SET ";
			$sql.= "body		= '".$top_body."' ";
			$sql.= "WHERE type='topmenu' AND code='".$code."' ";
			mysql_query($sql,get_db_conn());
		}
		mysql_free_result($result);
	}
	$onload="<script>alert(\"상단메뉴화면 디자인 수정이 완료되었습니다.\");</script>";
} else if($type=="delete") {
	if($code=="ALL") {
		$sql = "UPDATE tbldesign SET body_top='' ";
		mysql_query($sql,get_db_conn());
	} else {
		$sql = "DELETE FROM tbldesignnewpage WHERE type='topmenu' AND code='".$code."' ";
		mysql_query($sql,get_db_conn());
	}
	$onload="<script>alert(\"상단메뉴화면 디자인 삭제가 완료되었습니다.\");</script>";
} else if($type=="clear") {
	$top_body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='topmenu' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$top_body=$row->body;
	}
	mysql_free_result($result);
}
if($type!="clear") {
	$top_body="";
	if($code=="ALL") {
		$sql = "SELECT * FROM tbldesign ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$top_body=$row->body_top;
			$top_height=$row->top_height;
		}
		mysql_free_result($result);
	} else {
		$sql = "SELECT * FROM tbldesignnewpage WHERE type='topmenu' AND code='".$code."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$top_body=$row->body;
		}
		mysql_free_result($result);
	}
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.top_body.value.length==0) {
			alert("상단메뉴화면 디자인 내용을 입력하세요.");
			document.form1.top_body.focus();
			return;
		}
		try {
			if(!IsNumeric(document.form1.top_height.value)) {
				alert("상단메뉴 높이는 숫자만 입력 가능합니다.");
				document.form1.top_height.focus();
				return;
			}
		} catch (e) {}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("상단메뉴화면 디자인을 삭제하시겠습니까?")) {
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
		if( document.form1.code.value != 'ALL' ) {
			if(document.form1.top_body.value.length==0) {
				alert("상단메뉴화면 디자인 내용을 입력하세요.");
				document.form1.top_body.focus();
				return;
			}
			document.form1.type.value='<?=$insertKey?>';
			document.form1.target="preview";
			document.form1.action="designPreview.php";
			document.form1.submit();
			document.form1.target="";
			document.form1.action="<?=$_SERVER[PHP_SELF]?>";
		} else {
			alert('기본페이지는 미리보기 할수 없습니다.');
		}
	}
}

function change_page(val) {
	document.form1.type.value="change";
	document.form1.code.value=val;
	document.form1.submit();
}

//매크로 보기(팝업)
function macroview(){
	window.open("http://www.getmall.co.kr/macro/pages/top_macro.html","top_macro","height=800,width=680,scrollbars=no");
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; 개별디자인-메인 및 상하단  &gt; <span class="2depth_select">상단메뉴 꾸미기</span></td>
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
							<TD><IMG SRC="images/design_eachtop_title.gif"  ALT=""></TD>
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
					<table cellpadding="0" cellspacing="0" width="686">
					<tr>
						<td width="172" align=center><IMG SRC="images/design_eachtop_img.gif" WIDTH="159" HEIGHT="100" ALT="" align="baseline"></td>
						<td  class="notice_blue" style="letter-spacing:-0.5pt;">1) 상단메뉴를 전체페이지(default), 또는 카테고리별, 메뉴별 자유롭게 디자인이 가능합니다.<br>2) 개별디자인 적용 후 <a href="javascript:parent.topframe.GoMenu(2,'design_option.php');"><span class="font_blue">디자인관리 > 웹FTP 및 개별적용 선택 > 개별디자인 적용선택</span></a> 을 해야 적용됩니다.
						<br><b>&nbsp;&nbsp;&nbsp;</b>상단+왼쪽 동시 적용
						<br><b>&nbsp;&nbsp;&nbsp;</b>상단만 적용
						<br>3) <a href="javascript:parent.topframe.GoMenu(2,'design_easytop.php');"><span class="font_blue">디자인관리 > Easy 디자인 관리 > Easy 상단 메뉴 관리</span></a> 에서 디자인을 변경할 수 있습니다.</p>
						</td>
					</tr>
					</table>
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
			<tr><td height="50"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/design_eachtop_stitle1.gif" WIDTH="174" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;&nbsp;<a href="javascript:macroview();"><img src="images/btn_macroview.gif" border="0" align="absmiddle" alt="" /></a>&nbsp;&nbsp;<a href="http://www.getmall.co.kr/macro/data/top_design.zip"><img src="images/btn_defaultcode.gif" border="0" align="absmiddle" alt="" /></a></TD>
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
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" alt="" /></TD>
					<TD width="100%" class="notice_blue">
						1) 매뉴얼의 <b>매크로 명령어</b>를 참조하여 디자인 하세요.<br />
						2) <span class="font_orange" style="font-size:11px;"><u>상단메뉴 매크로 명령어 관련 파일</u> : <b>/main/topp.php</b> (파일 수정시 기존 파일은 반드시 백업하시기 바랍니다.) </span><br />
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
			<tr>
				<td height=20></td>
			</tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=code value="<?=$code?>">
			<input type=hidden name="urls" value="<?=$urls?>">
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0  style="table-layout:fixed">
				<col width=139></col>
				<col width=></col>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">해당 페이지 선택</TD>
					<TD class="td_con1"><select name=plist onchange="change_page(options.value)" style="width:330" class="select">
						<option value="ALL" <?if($code=="A")echo"selected";?>>기본 페이지 (Default)</option>
<?
			$sql = "SELECT codeA, code_name FROM tblproductcode ";
			$sql.= "WHERE (type='L' OR type='T' OR type='LX' OR type='TX') ORDER BY sequence DESC ";
			$result=mysql_query($sql,get_db_conn());
			$i=0;
			while($row=mysql_fetch_object($result)) {
				$i++;
				echo "<option value=\"".$row->codeA."\" ";
				if($code==$row->codeA) echo "selected";
				echo ">대분류".$i." - ".$row->code_name."</option>\n";
			}
			mysql_free_result($result);

			$page_list=array("메인 페이지 상단메뉴","게시판 관련 상단메뉴","회원 관련 상단메뉴","마이페이지 관련 상단메뉴","주문서 관련 상단메뉴","검색 관련 상단메뉴","브랜드 상품 목록 관련 상단메뉴","브랜드맵 관련 상단메뉴");
			$page_code=array("MAI","BOA","MEM","MYP","ORD","SEA","BRL","BRM");

			for($i=0;$i<count($page_list);$i++) {
				echo "<option value=\"".$page_code[$i]."\" ";
				if($code==$page_code[$i]) echo "selected";
				echo ">".$page_list[$i]."</option>\n";
			}
?>
						</select>
						<?if($code!="ALL"){?>
						&nbsp; <span class="font_orange"><b>* 원프레임 타입에서만 적용됩니다.</b></span>
						<?}?>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<?if($code=="ALL"){?>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">상단메뉴 높이</TD>
					<TD class="td_con1"><input type=text name=top_height value="<?=$top_height?>" size=3 maxlength=3 onkeyup="strnumkeyup(this)" class="input">픽셀</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<?}?>
				<TR>
					<TD colspan="2"><textarea name=top_body style="WIDTH: 100%; HEIGHT: 300px" class="textarea"><?=htmlspecialchars($top_body)?></textarea></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('clear');"><img src="images/botteon_bok.gif" width="124" height="38" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" width="113" height="38" border="0" hspace="0"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('preview');"><img src="images/botteon_prev.gif" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('store');"><img src="images/botteon_store.gif" border="0" hspace="2" alt="백업하기"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('restore');"><img src="images/botteon_restore.gif" border="0" hspace="2" alt="백업복원하기"></a></td>
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
						<td ><p class="LIPoint"><B><span class="font_orange">상단메뉴 매크로명령어</span></B>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</p></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top">&nbsp;</td>
						<td   style="padding-top:3pt; padding-bottom:10pt;">

						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<col width=150></col>
						<col width=></col>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>
						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[VISIT]</td>
							<td class="td_con1" colspan="3" style="padding-left:5px;">방문자표시, 로그인시 로그아웃 표시</td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[VISIT2]</td>
							<td class="td_con1" colspan="3" style="padding-left:5px;">방문자표시, 로그아웃 표시안됨</td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[HOME]</td>
							<td class="td_con1" style="padding-left:5px;">HOME <FONT class=font_blue>(예:&lt;a href=[HOME]>HOME&lt;/a&gt;)</FONT></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/main/main.php <span class="font_blue">(예:&lt;a href="/main/main.php"&gt;HOME&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[USEINFO]</td>
							<td class="td_con1" style="padding-left:5px;">이용안내 <FONT class=font_blue>(예:&lt;a href=[USEINFO]>이용안내&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/useinfo.php <span class="font_blue">(예:&lt;a href="/front/useinfo.php"&gt;이용안내&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[MEMBER]</td>
							<td class="td_con1" style="padding-left:5px;">회원가입/수정 <FONT class=font_blue>(예:&lt;a href=[MEMBER]>회원가입/수정&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/member_agree.php <span class="font_blue">(예:&lt;a href="/front/member_agree.php"&gt;회원가입/수정&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[LOGIN_START]<br />[LOGIN_END]</td>
							<td class="td_con1" colspan="3" style="padding-left:5px;">
								<span style="line-height:30px;">로그아웃 상태일 때 보여줄 내용</span><br />
								<FONT class=font_blue>
									<b>[LOGIN_START]</b><br />
										&nbsp;&nbsp;&lt;a href=[LOGIN]&gt;로그인&lt/a&gt; | &lt;a href=[MEMBER]&gt;회원가입&lt;/a&gt;<br />
									<b>[LOGIN_END]</b>
								</FONT>
							</td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[LOGIN]</td>
							<td class="td_con1" style="padding-left:5px;">로그인 <FONT class=font_blue>(예:&lt;a href=[LOGIN]>로그인&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/login.php <span class="font_blue">(예:&lt;a href="/front/login.php"&gt;로그인&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>
						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[LOGOUT_START]<br />[LOGOUT_END]</td>
							<td class="td_con1" colspan="3" style="padding-left:5px;">
								<span style="line-height:30px;">로그인 상태일 때 보여줄 내용</span><br />
								<FONT class=font_blue>
									<b>[LOGOUT_START]</b><br />
										&nbsp;&nbsp;&lt;a href=[LOGOUT]&gt;로그아웃&lt/a&gt; | &lt;a href=[MEMBER]&gt;회원정보수정&lt;/a&gt;<br />
									<b>[LOGOUT_END]</b>
								</FONT>
							</td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[LOGOUT]</td>
							<td class="td_con1" style="padding-left:5px;">로그아웃 <FONT class=font_blue>(예:&lt;a href=[LOGOUT]>로그아웃&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">javascript:logout(); <span class="font_blue">(예:&lt;a href="javascript:logout();"&gt;로그아웃&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[MEMBEROUT]</td>
							<td class="td_con1" style="padding-left:5px;">회원탈퇴 <FONT class=font_blue>(예:&lt;a href=[MEMBEROUT]>회원탈퇴&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/mypage_memberout.php <span class="font_blue">(예:&lt;a href="/front/mypage_memberout.php"&gt;회원탈퇴&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[WELCOME]</td>
							<td class="td_con1" colspan="3" style="padding-left:5px;">회원 로그인 인사말 (예:Guest 님, 환영합니다.)</td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[LOGO]</td>
							<td class="td_con1" style="padding-left:5px;">로고이미지 <FONT class=font_blue>(예:&lt;a href=[HOME]>[LOGO]&lt;/a&gt;)</font></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[LOGINFORM]</td>
							<td class="td_con1" colspan="3" style="padding-left:5px;">로그인 폼</td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[LOGINFORMU]</td>
							<td class="td_con1" colspan="3" style="padding-left:5px;">로그인 폼 관리에서 등록한 내용 표시</td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[BASKET]</td>
							<td class="td_con1" style="padding-left:5px;">장바구니 <FONT class=font_blue>(예:&lt;a href=[BASKET]>장바구니&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/basket.php <span class="font_blue">(예:&lt;a href="/front/basket.php"&gt;장바구니&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[BASKETCOUNT]</td>
							<td class="td_con1" colspan="3" style="padding-left:5px;">장바구니 상품갯수</td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[ORDER]</td>
							<td class="td_con1" style="padding-left:5px;">주문조회 <FONT class=font_blue>(예:&lt;a href=[ORDER]>주문조회&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/mypage_orderlist.php <span class="font_blue">(예:&lt;a href="/front/mypage_orderlist.php"&gt;주문조회&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[RESERVEVIEW]</td>
							<td class="td_con1" style="padding-left:5px;">적립금조회 <FONT class=font_blue>(예:&lt;a href=[RESERVEVIEW]>적립금조회&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/mypage_reserve.php <span class="font_blue">(예:&lt;a href="/front/mypage_reserve.php"&gt;적립금조회&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[MYPAGE]</td>
							<td class="td_con1" style="padding-left:5px;">마이페이지 <FONT class=font_blue>(예:&lt;a href=[MYPAGE]>마이페이지&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/mypage.php <span class="font_blue">(예:&lt;a href="/front/mypage.php"&gt;마이페이지&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[REVIEW]</td>
							<td class="td_con1" style="padding-left:5px;">사용후기 모음 <FONT class=font_blue>(예:&lt;a href=[REVIEW]>사용후기 모음&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/reviewall.php <span class="font_blue">(예:&lt;a href="/front/reviewall.php"&gt;사용후기 모음&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[BOARD]</td>
							<td class="td_con1" style="padding-left:5px;">게시판 <FONT class=font_blue>(예:&lt;a href=[BOARD]>게시판&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/board/board.php?board=qna <span class="font_blue">(예:&lt;a href="/board/board.php?board=qna"&gt;게시판&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[AUCTION]</td>
							<td class="td_con1" style="padding-left:5px;">경매 <FONT class=font_blue>(예:&lt;a href=[AUCTION]>경매&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/auction/auction.php <span class="font_blue">(예:&lt;a href="/auction/auction.php"&gt;경매&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[TODAYSALE]</td>
							<td class="td_con1" style="padding-left:5px;">투데이세일 <FONT class=font_blue>(예:&lt;a href=[TODAYSALE]>투데이세일&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/todayshop/ <span class="font_blue">(예:&lt;a href="/todayshop/"&gt;투데이세일&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[GONGGU]</td>
							<td class="td_con1" style="padding-left:5px;">공동구매 <FONT class=font_blue>(예:&lt;a href=[GONGGU]>공동구매&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/gonggu_main.php <span class="font_blue">(예:&lt;a href="/front/gonggu_main.php"&gt;공동구매&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[USECOUPON_START]<br />[USECOUPON_END]</td>
							<td class="td_con1" style="padding-left:5px;">
								<span style="line-height:30px;">쿠폰모음 사용중일 때 보여줄 내용</span><br />
								<FONT class=font_blue>
									<b>[USECOUPON_START]</b><br />
										&nbsp; &lt;a href=[COUPONALL]&gt;쿠폰모음&lt;/a&gt;<br />
									<b>[USECOUPON_END]</b>
								</FONT>
							</td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[COUPONALL]</td>
							<td class="td_con1" style="padding-left:5px;">쿠폰모음 <FONT class=font_blue>(예:&lt;a href=[COUPONALL]>쿠폰모음&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/couponlist.php <span class="font_blue">(예:&lt;a href="/front/couponlist.php"&gt;공동구매&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[PRODUCTGIFT]</td>
							<td class="td_con1" style="padding-left:5px;">전용이용권 구매 <FONT class=font_blue>(예:&lt;a href=[PRODUCTGIFT]>전용이용권 구매&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/productgift.php <span class="font_blue">(예:&lt;a href="/front/productgift.php"&gt;전용이용권 구매&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[HONGBOURL]</td>
							<td class="td_con1" style="padding-left:5px;">홍보 적립금 <FONT class=font_blue>(예:&lt;a href=[HONGBOURL]>홍보 적립금&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">javascript:win_hongboUrl(); <span class="font_blue">(예:&lt;a href="javascript:win_hongboUrl();"&gt;홍보 적립금&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[ESTIMATE]</td>
							<td class="td_con1" style="padding-left:5px;">온라인견적서 <FONT class=font_blue>(예:&lt;a href=[ESTIMATE]>온라인견적서&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/estimate.php <span class="font_blue">(예:&lt;a href="/front/estimate.php"&gt;온라인견적서&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[COMPANY]</td>
							<td class="td_con1" style="padding-left:5px;">회사소개 <FONT class=font_blue>(예:&lt;a href=[COMPANY]>회사소개&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/estimate.php <span class="font_blue">(예:&lt;a href="/front/company.php"&gt;회사소개&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[EMAIL]</td>
							<td class="td_con1" style="padding-left:5px;">이메일 <FONT class=font_blue>(예:&lt;a href=[EMAIL]>고객센터&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">javascript:sendmail(); <span class="font_blue">(예:&lt;a href="javascript:sendmail();"&gt;고객센터&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[PRODUCTNEW]</td>
							<td class="td_con1" style="padding-left:5px;">신규상품 <FONT class=font_blue>(예:&lt;a href=[PRODUCTNEW]>신규상품&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/productnew.php <span class="font_blue">(예:&lt;a href="/front/productnew.php"&gt;신규상품&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[PRODUCTBEST]</td>
							<td class="td_con1" style="padding-left:5px;">인기상품 <FONT class=font_blue>(예:&lt;a href=[PRODUCTBEST]>인기상품&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/productbest.php <span class="font_blue">(예:&lt;a href="/front/productbest.php"&gt;인기상품&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[PRODUCTHOT]</td>
							<td class="td_con1" style="padding-left:5px;">추천상품 <FONT class=font_blue>(예:&lt;a href=[PRODUCTHOT]>추천상품&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/producthot.php <span class="font_blue">(예:&lt;a href="/front/producthot.php"&gt;추천상품&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[PRODUCTSPECIAL]</td>
							<td class="td_con1" style="padding-left:5px;">특별상품 <FONT class=font_blue>(예:&lt;a href=[PRODUCTSPECIAL]>특별상품&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/productspecial.php <span class="font_blue">(예:&lt;a href="/front/productspecial.php"&gt;특별상품&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[TAG]</td>
							<td class="td_con1" style="padding-left:5px;">태그 바로가기 <FONT class=font_blue>(예:&lt;a href=[TAG]>태그&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/tag.php <span class="font_blue">(예:&lt;a href="/front/tag.php"&gt;태그&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[RSS]</td>
							<td class="td_con1" style="padding-left:5px;">RSS 바로가기 <FONT class=font_blue>(예:&lt;a href=[RSS]>RSS&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">/front/rssinfo.php <span class="font_blue">(예:&lt;a href="/front/rssinfo.php"&gt;RSS&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[NOTICE1]</td>
							<td class="td_con1" colspan="3" style="padding-left:5px;">기본 공지사항 모습</td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[NOTICE2]</td>
							<td class="td_con1" colspan="3" style="padding-left:5px;">공지 날짜가 제목앞에 붙는 모습</td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[NOTICE3]</td>
							<td class="td_con1" colspan="3" style="padding-left:5px;">앞부분에 이미지 표시</td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[NOTICE4]</td>
							<td class="td_con1" colspan="3" style="padding-left:5px;">앞부분에 숫자나 날짜표기 안함</td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[NOTICE?????_000]</td>
							<td class="td_con1" colspan="3" style="padding-left:5px;">
								공지사항
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 위에 제공된 공지사항 타입</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 타이틀 표시여부(Y/N)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 공지사항 간격(1-9) 미입력시 4픽셀</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : NEW 아이콘 표시여부 (Y/N)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : NEW 아이콘 표시기간 (1-9)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>_000 : 표시될 공지사항 길이 (최대 숫자 200까지)</FONT>
								<br>
								<FONT class=font_blue>예) [NOTICE1N5Y1_80]</FONT>
							</td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>
						
						<tr>
							<td class=table_cell align=right bgcolor=#FBDED2 style="padding-right:15">[SEARCHFORMSTART]</td>
							<td class="td_con1" style="padding-left:5px;">검색폼 시작</td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1"><span class="font_blue">&lt;form name="search_tform" method="get" action="/front/productsearch.php"&gt; (name값 변경불가)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class=table_cell align=right bgcolor=#FBDED2 style="padding-right:15">[SEARCHKEYWORD_000]</td>
							<td class="td_con1" style="padding-left:5px;">검색폼 검색어 입력 텍스트폼 <FONT class=font_orange>(_000:텍스트폼 사이즈[픽셀단위])</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1"><span class="font_blue">&lt;input type="text" name="search" onkeydown="CheckKeyTopSearch()" style="width:200px"&gt; (name값 변경불가)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class=table_cell align=right bgcolor=#FBDED2 style="padding-right:15">[SEARCHOK]</td>
							<td class="td_con1" style="padding-left:5px;">검색확인 버튼 <FONT class=font_blue>(예:&lt;a href=[SEARCHOK]&gt;검색&lt;/a&gt;)</font></td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1">javascript:TopSearchCheck(); <span class="font_blue">(예:&lt;a href="javascript:TopSearchCheck();"&gt;검색&lt;/a&gt;)</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class=table_cell align=right bgcolor=#FBDED2 style="padding-right:15">[SEARCHFORMEND]</td>
							<td class="td_con1" style="padding-left:5px;">검색폼 끝</td>
							<td class="table_cell" align="right" style="padding-right:15px;">대체사용 가능</td>
							<td class="td_con1"><span class="font_blue">&lt;/form&gt;</span></td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>

						<tr>
							<td class="table_cell" align="right" style="padding-right:15px;">[BESTSKEY_000_인기검색어구분자_인기검색어텍스트스타일]</td>
							<td class="td_con1" colspan="3" style="padding-left:5px;">
							인기검색어 출력 (인기검색어 기능 설정이 되어있어야 가능)
										<br><img width=10 height=0>
										<FONT class=font_orange>_000 : 인기검색어 출력 텍스트 총 길이 (예: 100) - 100바이트 출력 후 "..." 출력</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>_인기검색어구분자 : 인기검색어 구분자 (예: "|" 또는 ",") "_"사용불가</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>_인기검색어텍스트스타일 : 인기검색어 텍스트 스타일 (예: color:#FFFFFF;font-size:9px) "_"사용불가</FONT>
										<br>
										<FONT class=font_blue>예) [BESTSKEY_100_|_color:#FFFFFF;font-size:9px]</FONT>
							</td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>
						<tr>
							<td colspan=4 style="padding:10px;">
									<B>[검색폼 예]</B><br /><br />

									<FONT class=font_blue><B>[SEARCHFORMSTART]</B><br />
									&lt;table border=0 cellpadding=0 cellspacing=0&gt;<br />
									&nbsp;&nbsp;&lt;tr&gt;<br />
									&nbsp;&nbsp;&nbsp;&nbsp;&lt;td&gt;<B>[SEARCHKEYWORD_120]</B>&lt;/td&gt;<br />
									&nbsp;&nbsp;&nbsp;&nbsp;&lt;td&gt;&lt;a href=<B>[SEARCHOK]</B>>검색&lt;/a&gt;&lt;/td&gt;<br />
									&nbsp;&nbsp;&lt;/tr&gt;<br />
									&lt;/table&gt;<br />
									<B>[SEARCHFORMEND]</B></FONT>
							</td>
						</tr>
						<tr><td colspan="4" height="1" bgcolor="#dddddd"></td></tr>
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