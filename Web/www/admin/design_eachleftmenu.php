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
$left_body=$_POST["left_body"];

if(strlen($code)==0) $code="ALL";


$insertKey = "leftmenu";

$subject = '왼쪽메뉴화면';
// 백업 / 복구
if ( $type=="store" OR $type=="restore" ) {
	if($code=="ALL") {
		$MSG = adminDesingBackup ( $type, $insertKey, $left_body, $subject, $code, '', '', 'tbldesign', 'body_left' );
	} else {
		$MSG = adminDesingBackup ( $type, $insertKey, $left_body, $subject, $code );
	}
	$onload="<script>alert(\"".$MSG."\");</script>";
}



if($type=="update" && strlen($left_body)>0) {
	//$left_body = ereg_replace("\"\[","[",$left_body);
	//$left_body = ereg_replace("]\"","]",$left_body);
	if($code=="ALL") {
		$sql = "SELECT COUNT(*) as cnt FROM tbldesign ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		if($row->cnt==0) {
			$sql = "INSERT tbldesign SET ";
			$sql.= "body_left	= '".$left_body."' ";
			mysql_query($sql,get_db_conn());
		} else {
			$sql = "UPDATE tbldesign SET ";
			$sql.= "body_left	= '".$left_body."' ";
			mysql_query($sql,get_db_conn());
		}
		mysql_free_result($result);
	} else {
		$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage ";
		$sql.= "WHERE type='leftmenu' AND code='".$code."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		if($row->cnt==0) {
			$sql = "INSERT tbldesignnewpage SET ";
			$sql.= "type		= 'leftmenu', ";
			$sql.= "body		= '".$left_body."', ";
			$sql.= "code		= '".$code."' ";
			mysql_query($sql,get_db_conn());
		} else {
			$sql = "UPDATE tbldesignnewpage SET ";
			$sql.= "body		= '".$left_body."' ";
			$sql.= "WHERE type='leftmenu' AND code='".$code."' ";
			mysql_query($sql,get_db_conn());
		}
		mysql_free_result($result);
	}
	$onload="<script>alert(\"왼쪽메뉴화면 디자인 수정이 완료되었습니다.\");</script>";
} else if($type=="delete") {
	if($code=="ALL") {
		$sql = "UPDATE tbldesign SET body_left='' ";
		mysql_query($sql,get_db_conn());
	} else {
		$sql = "DELETE FROM tbldesignnewpage WHERE type='leftmenu' AND code='".$code."' ";
		mysql_query($sql,get_db_conn());
	}
	$onload="<script>alert(\"왼쪽메뉴화면 디자인 삭제가 완료되었습니다.\");</script>";

}
if($type!="clear") {
	$left_body="";
	if($code=="ALL") {
		$sql = "SELECT * FROM tbldesign ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$left_body=$row->body_left;
		}
		mysql_free_result($result);
	} else {
		$sql = "SELECT * FROM tbldesignnewpage WHERE type='leftmenu' AND code='".$code."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$left_body=$row->body;
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
		if(document.form1.left_body.value.length==0) {
			alert("왼쪽메뉴화면 디자인 내용을 입력하세요.");
			document.form1.left_body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("왼쪽메뉴화면 디자인을 삭제하시겠습니까?")) {
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
			if(document.form1.left_body.value.length==0) {
				alert("왼쪽메뉴화면 디자인 내용을 입력하세요.");
				document.form1.left_body.focus();
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
	window.open("http://www.getmall.co.kr/macro/pages/left_macro.html","top_macro","height=800,width=680,scrollbars=no");
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; 개별디자인-메인 및 상하단  &gt; <span class="2depth_select">왼쪽메뉴 꾸미기</span></td>
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
						<TD><IMG SRC="images/design_eachleftmenu_title.gif"  ALT=""></TD>
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
						<td width="172" align=center><IMG SRC="images/design_eachleftmenu_img.gif" WIDTH="159" HEIGHT="100" ALT="" align="baseline"></td>
						<td  class="notice_blue" style="letter-spacing:-0.5pt;">1) 왼쪽메뉴를 전체페이지(default), 또는 카테고리별, 메뉴별 자유롭게 디자인이 가능합니다.<br>2) 개별디자인 적용 후 <a href="javascript:parent.topframe.GoMenu(2,'design_option.php');"><span class="font_blue">디자인관리 > 웹FTP 및 개별적용 선택 > 개별디자인 적용선택</span></a> 을 해야 적용됩니다.
						<br><b>&nbsp;&nbsp;&nbsp;</b>상단+왼쪽 동시 적용
						<br><b>&nbsp;&nbsp;&nbsp;</b>왼쪽만 적용
						<br>3) <a href="javascript:parent.topframe.GoMenu(2,'design_easyleft.php');"><span class="font_blue">디자인관리 > Easy 디자인 관리 > Easy 왼쪽 메뉴 관리</span></a> 에서 디자인을 변경할 수 있습니다.</p></td>

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
					<TD><IMG SRC="images/design_eachleft_stitle1.gif" WIDTH="174" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;&nbsp;<a href="javascript:macroview();"><img src="images/btn_macroview.gif" border="0" align="absmiddle" alt="" /></a>&nbsp;&nbsp;<a href="http://www.getmall.co.kr/macro/data/left_design.zip"><img src="images/btn_defaultcode.gif" border="0" align="absmiddle" alt="" /></a></TD>
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
						1) 매뉴얼의 <b>매크로명령어</b>를 참조하여 디자인 하세요.<br /><br />
						2) <span class="font_orange" style="font-size:11px;"><u>왼쪽메뉴 매크로 명령어 관련 파일</u> : <b>/main/menup.php (왼쪽메뉴 사용시), /main/nomenu.php (왼쪽메뉴 미사용시), /main/menu_text.php</b> (파일 수정시 기존 파일은 반드시 백업하시기 바랍니다.)</span><br />
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
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				<col width=139></col>
				<col width=></col>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
				<input type=hidden name=type>
				<input type=hidden name=code value="<?=$code?>">
				<input type=hidden name="urls" value="<?=$urls?>">
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">해당 페이지 선택</TD>
					<TD class="td_con1"><select name=plist onchange="change_page(options.value)" style="width:330" class="select">
						<option value="ALL" <?if($code=="ALL")echo"selected";?>>기본 페이지 (Default)</option>
<?
			$sql = "SELECT codeA, code_name FROM tblproductcode WHERE (type='L' OR type='T' OR type='LX' OR type='TX') ORDER BY sequence DESC ";
			$result=mysql_query($sql,get_db_conn());
			$i=0;
			while($row=mysql_fetch_object($result)) {
				$i++;
				echo "<option value=\"".$row->codeA."\" ";
				if($code==$row->codeA) echo "selected";
				echo ">대분류".$i." - ".$row->code_name."</option>\n";
			}
			mysql_free_result($result);

			//$page_list=array("메인 페이지 왼쪽메뉴","게시판 관련 왼쪽메뉴","회원 관련 왼쪽메뉴","마이페이지 관련 왼쪽메뉴","주문서 관련 왼쪽메뉴","검색 관련 왼쪽메뉴","브랜드 상품 목록 관련 왼쪽메뉴","브랜드맵 관련 왼쪽메뉴","개별 페이지 왼쪽메뉴1","개별 페이지 왼쪽메뉴2","개별 페이지 왼쪽메뉴3","개별 페이지 왼쪽메뉴4","개별 페이지 왼쪽메뉴5","개별 페이지 왼쪽메뉴6","개별 페이지 왼쪽메뉴7","개별 페이지 왼쪽메뉴8","개별 페이지 왼쪽메뉴9","개별 페이지 왼쪽메뉴10");
			//$page_code=array("MAI","BOA","MEM","MYP","ORD","SEA","BRL","BRM","NE0","NE1","NE2","NE3","NE4","NE5","NE6","NE7","NE8","NE9");
			$page_list=array("메인 페이지 왼쪽메뉴","게시판-고객센터 왼쪽메뉴","게시판-커뮤니티 왼쪽메뉴","게시판-개별 왼쪽메뉴","회원 관련 왼쪽메뉴","마이페이지 관련 왼쪽메뉴","주문서 관련 왼쪽메뉴","검색 관련 왼쪽메뉴","브랜드 상품 목록 관련 왼쪽메뉴","브랜드맵 관련 왼쪽메뉴","개별 페이지 왼쪽메뉴1","개별 페이지 왼쪽메뉴2","개별 페이지 왼쪽메뉴3","개별 페이지 왼쪽메뉴4","개별 페이지 왼쪽메뉴5","개별 페이지 왼쪽메뉴6","개별 페이지 왼쪽메뉴7","개별 페이지 왼쪽메뉴8","개별 페이지 왼쪽메뉴9","개별 페이지 왼쪽메뉴10");

			$page_code=array("MAI","BOU","BOM","BTC","MEM","MYP","ORD","SEA","BRL","BRM","NE0","NE1","NE2","NE3","NE4","NE5","NE6","NE7","NE8","NE9");
			for($i=0;$i<count($page_list);$i++) {
				echo "<option value=\"".$page_code[$i]."\" ";
				if($code==$page_code[$i]) echo "selected";
				echo ">".$page_list[$i]."</option>\n";
			}
?>
						</select></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan="2"><textarea name=left_body style="WIDTH: 100%; HEIGHT: 300px" class="textarea"><?=htmlspecialchars($left_body)?></textarea></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10>&nbsp;</td></tr>
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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"  class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><p class="LIPoint"><B><span class="font_orange">왼쪽메뉴 매크로명령어</span></B>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</p></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"></td>
						<td  style="padding-top:3pt; padding-bottom:10pt;">
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[VISIT]</td>
							<td class=td_con1 style="padding-left:5;">
							방문자표시, 로그인시 로그아웃 표시
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[VISIT2]</td>
							<td class=td_con1 style="padding-left:5;">
							방문자표시, 로그아웃 표시안됨
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[EMAIL]</td>
							<td class=td_con1 style="padding-left:5;">
							이메일 <FONT class=font_blue>(예:&lt;a href=[EMAIL]>메일 또는 고객센터&lt;/a>)</font>
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
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRODUCTNEW]</td>
							<td class=td_con1 style="padding-left:5;">
							신규상품 <FONT class=font_blue>(예:&lt;a href=[PRODUCTNEW]>신규상품&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRODUCTBEST]</td>
							<td class=td_con1 style="padding-left:5;">
							인기상품 <FONT class=font_blue>(예:&lt;a href=[PRODUCTBEST]>인기상품&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRODUCTHOT]</td>
							<td class=td_con1 style="padding-left:5;">
							추천상품 <FONT class=font_blue>(예:&lt;a href=[PRODUCTHOT]>추천상품&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRODUCTSPECIAL]</td>
							<td class=td_con1 style="padding-left:5;">
							특별상품 <FONT class=font_blue>(예:&lt;a href=[PRODUCTSPECIAL]>특별상품&lt;/a>)</font>
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
							<td class=table_cell align=right style="padding-right:15">[MEMBEROUT]</td>
							<td class=td_con1 style="padding-left:5;">
							회원탈퇴 <FONT class=font_blue>(예:&lt;a href=[MEMBEROUT]>회원탈퇴&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LOGINFORM]</td>
							<td class=td_con1 style="padding-left:5;">
							로그인 폼
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LOGINFORMU]</td>
							<td class=td_con1 style="padding-left:5;">
							로그인 폼 관리에서 등록한 내용 표시
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BRANDMAP]</td>
							<td class=td_con1 style="padding-left:5;">
							브랜드맵 <FONT class=font_blue>(예:&lt;a href=[BRANDMAP]>브랜드맵&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[REVIEW]</td>
							<td class=td_con1 style="padding-left:5;">
							사용후기 모음 <FONT class=font_blue>(예:&lt;a href=[REVIEW]>사용후기 모음&lt;/a>)</font>
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
							적립금조회 <FONT class=font_blue>(예:&lt;a href=[RESERVEVIEW]>적립금조회&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MYPAGE]</td>
							<td class=td_con1 style="padding-left:5;">
							마이페이지 <FONT class=font_blue>(예:&lt;a href=[MYPAGE]>마이페이지&lt;/a>)</font>
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
							<td class=table_cell align=right style="padding-right:15">[SHOPTEL_아이콘URL]</td>
							<td class=td_con1 style="padding-left:5;">
							상점 전화번호 - <FONT class=font_orange>_아이콘URL : 전화번호 앞에 붙는 아이콘 URL ("_"사용불가)</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BRANDLIST_000]</td>
							<td class=td_con1 style="padding-left:5;">
							브랜드 목록
							<br><img width=10 height=0>
							<FONT class=font_orange>_000 : 브랜드 목록 높이</FONT>
							<br>
							<FONT class=font_blue>예) [BRANDLIST_200]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right bgcolor=#E9A74E style="padding-right:15">브랜드 목록 관련 스타일 정의</td>
							<td class=td_con1 bgcolor=#FEEEE2 style="padding-left:5;">
										<img width=10 height=0>
										<FONT class=font_orange>#brandlist_div - 브랜드 목록 DIV 스타일 정의 (백그라운드컬러 및 스크롤바)</FONT>
										<br><img width=100 height=0>
										<FONT class=font_blue>예) #brandlist_div { background-color:#E6E6E6;<br><img width=100 height=0>
										scrollbar-face-color:#FFFFFF;<br><img width=100 height=0>
										scrollbar-arrow-color:#999999;<br><img width=100 height=0>
										scrollbar-track-color:#FFFFFF;<br><img width=100 height=0>
										scrollbar-highlight-color:#CCCCCC;<br><img width=100 height=0>
										scrollbar-3dlight-color:#FFFFFF;<br><img width=100 height=0>
										scrollbar-shadow-color:#CCCCCC;<br><img width=100 height=0>
										scrollbar-darkshadow-color:#FFFFFF; }</FONT>
										<br><img width=0 height=7><br><img width=10 height=0>
										<FONT class=font_orange>#brandlist_ul - 브랜드 목록 UI 스타일 정의 (백그라운드컬러)</FONT>
										<br><img width=100 height=0>
										<FONT class=font_blue>예) #brandlist_ul { background-color:#EFEFEF; }</FONT>
										<br><img width=0 height=7><br><img width=10 height=0>
										<FONT class=font_orange>#brandlist_li - 브랜드 목록 LI 스타일 정의 (백그라운드컬러)</FONT>
										<br><img width=100 height=0>
										<FONT class=font_blue>예) #brandlist_li { background-color:#FFFFFF;}</FONT>
				<pre style="line-height:15px">
<B>[사용 예]</B> - 내용 본문에 아래와 같이 정의하시면 됩니다.

<FONT class=font_blue>&lt;style>
  #brandlist_div { background-color:#E6E6E6;
  scrollbar-face-color:#FFFFFF;
  scrollbar-arrow-color:#999999;
  scrollbar-track-color:#FFFFFF;
  scrollbar-highlight-color:#CCCCCC;
  scrollbar-3dlight-color:#FFFFFF;
  scrollbar-shadow-color:#CCCCCC;
  scrollbar-darkshadow-color:#FFFFFF; }
  #brandlist_ul { background-color:#EFEFEF; }
  #brandlist_li { background-color:#FFFFFF; }
&lt;/style></FONT></pre>
							</td>
						</tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BANNER]</td>
							<td class=td_con1 style="padding-left:5;">
							배너표시 (배너가 왼쪽에 위치해 있을 경우에만)
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LEFTEVENT]</td>
							<td class=td_con1 style="padding-left:5;">
							왼쪽 이벤트/고객알림영역
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NOTICE1]</td>
							<td class=td_con1 style="padding-left:5;">
							기본 공지사항 모습
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NOTICE2]</td>
							<td class=td_con1 style="padding-left:5;">
							공지날짜가가 제목앞에 붙는 모습
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NOTICE3]</td>
							<td class=td_con1 style="padding-left:5;">
							앞부분에 이미지 표시
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NOTICE4]</td>
							<td class=td_con1 style="padding-left:5;">
							앞부분에 숫자나 날짜표기 안함
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NOTICE?????_000]</td>
							<td class=td_con1 style="padding-left:5;">
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
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[INFO1]</td>
							<td class=td_con1 style="padding-left:5;">
							기본 컨텐츠정보 모습
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[INFO2]</td>
							<td class=td_con1 style="padding-left:5;">
							게시날짜가가 제목앞에 붙는 모습
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[INFO3]</td>
							<td class=td_con1 style="padding-left:5;">
							앞부분에 이미지 표시
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[INFO4]</td>
							<td class=td_con1 style="padding-left:5;">
							앞부분에 숫자나 날짜표기 안함
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[INFO???_000]</td>
							<td class=td_con1 style="padding-left:5;">
							컨텐츠정보
										<br><img width=10 height=0>
										<FONT class=font_orange>? : 위에 제공된 컨텐츠정보 타입</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : 타이틀 표시여부(Y/N)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : 컨텐츠정보 간격(1-9) 미입력시 4픽셀</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>_000 : 표시될 컨텐츠정보 길이 (최대 숫자 200까지)</FONT>
										<br>
										<FONT class=font_blue>예) [INFO1N5_80]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SPEITEM]</td>
							<td class=td_con1 style="padding-left:5;">
							타이틀 이미지가 있는 특별상품
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SPEITEM_N]</td>
							<td class=td_con1 style="padding-left:5;">
							타이틀 이미지가 없는 특별상품
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[POLL]</td>
							<td class=td_con1 style="padding-left:5;">
							타이틀 이미지가 있는 투표
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[POLL_N]</td>
							<td class=td_con1 style="padding-left:5;">
							타이틀 이미지가 없는 투표
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ESTIMATE]</td>
							<td class=td_con1 style="padding-left:5;">
							온라인견적서 - &lt;a href=[ESTIMATE]>온라인견적서&lt;/a>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRLIST_??_???_?_가로라인백그라운드URL_아이콘URL]</td>
							<td class=td_con1 style="padding-left:5;">
							상품대분류 자동표시 - "_"사용불가
										<br><img width=10 height=0>
										<FONT class=font_orange>_?? : 대분류 사이의 높이(픽셀) - 지정하지 않을 경우 "?" 입력</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>_??? : 대분류 테이블의 가로 넓이(픽셀) - 지정하지 않을 경우 "?" 입력</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>_? : 대분류 사이에 가로라인 표시여부(Y/N) - 지정하지 않을 경우 "?" 입력</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>가로라인백그라운드URL : 지정하지 않을 경우 "?" 입력</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>아이콘URL : 지정하지 않을 경우 "?" 등록</FONT>
										<br>
										<FONT class=font_blue>예) [PRLIST_5_190_Y_가로라인백그라운드URL_아이콘URL]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BOARDLIST_??_???_?_가로라인백그라운드URL_아이콘URL]</td>
							<td class=td_con1 style="padding-left:5;">
							게시판리스트 자동표시 - "_"사용불가
										<br><img width=10 height=0>
										<FONT class=font_orange>_?? : 게시판리스트 사이의 높이(픽셀) - 지정하지 않을 경우 "?" 입력</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>_??? : 게시판리스트 테이블의 가로 넓이(픽셀) - 지정하지 않을 경우 "?" 입력</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>_? : 게시판리스트 사이에 가로라인 표시여부(Y/N) - 지정하지 않을 경우 "?" 입력</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>가로라인백그라운드URL : 지정하지 않을 경우 "?" 입력</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>아이콘URL : 지정하지 않을 경우 "?" 등록</FONT>
										<br>
										<FONT class=font_blue>예) [BOARDLIST_5_190_Y_가로라인백그라운드URL_아이콘URL]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BOARD_LEFT_LIST]</td>
							<td class=td_con1 style="padding-left:5;">
								게시판 타입별 리스트 출력<br>
								출력타입 : 고객센터, 커뮤니티, 개별<br>
								게시판 생성 또는 수정시 체크한 노출영역에 따라 자동 출력됩니다.
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right bgcolor=#FBDED2 style="padding-right:15">[SEARCHFORMSTART]</td>
							<td class=td_con1 style="padding-left:5;">
							검색폼 시작
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right bgcolor=#FBDED2 style="padding-right:15">[SEARCHKEYWORD_000]</td>
							<td class=td_con1 style="padding-left:5;">
							검색폼 검색어 입력 텍스트폼 <FONT class=font_orange>(_000:텍스트폼 사이즈[픽셀단위])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right bgcolor=#FBDED2 style="padding-right:15">[SEARCHOK]</td>
							<td class=td_con1 style="padding-left:5;">
							검색확인 버튼 <FONT class=font_blue>(예:&lt;a href=[SEARCHOK]>검색&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right bgcolor=#FBDED2 style="padding-right:15">[SEARCHFORMEND]</td>
							<td class=td_con1 style="padding-left:5;">
							검색폼 끝
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td colspan=2 style="padding:10">
							<pre style="line-height:15px">
<B>[검색폼 예]</B>

	<FONT class=font_blue>&lt;table border=0 cellpadding=0 cellspacing=0>
	<B>[SEARCHFORMSTART]</B>
	&lt;tr>
	   &lt;td><B>[SEARCHKEYWORD_120]</B>&lt;/td>
	   &lt;td>&lt;a href=<B>[SEARCHOK]</B>>검색&lt;/a>&lt;/td>
	&lt;/tr>
	<B>[SEARCHFORMEND]</B>
	&lt;/table></FONT></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						</table>
						</td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><p class="LIPoint">나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</p></td>
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