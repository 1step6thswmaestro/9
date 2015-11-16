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

$useinfo=$body;

if($intitle=="Y") {
	$useinfo=$body."Y";
} else {
	$useinfo=$body."N";
}

$subject = '이용안내화면';

$insertKey = "useinfo";

// 백업 / 복구
if ( $type=="store" OR $type=="restore" ) {
	$MSG = adminDesingBackup ( $type, $insertKey, $useinfo, $subject, '', '', '', 'tbldesign', 'useinfo' );
	$onload="<script>alert(\"".$MSG."\");</script>";
}

if($type=="update" && strlen($body)>0) {

	$sql = "SELECT COUNT(*) as cnt FROM tbldesign ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT tbldesign SET ";
		$sql.= "useinfo		= '".$useinfo."' ";
		mysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesign SET ";
		$sql.= "useinfo		= '".$useinfo."' ";
		mysql_query($sql,get_db_conn());
	}
	mysql_free_result($result);
	$onload="<script>alert(\"이용안내화면 디자인 수정이 완료되었습니다.\");</script>";
} else if($type=="delete") {
	$sql = "UPDATE tbldesign SET ";
	$sql.= "useinfo		= '' ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert(\"이용안내화면 디자인 삭제가 완료되었습니다.\");</script>";
}

$body="";
$sql = "SELECT useinfo FROM tbldesign ";
$result = mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$body=$row->useinfo;
	if (substr($body,-2,1)=="") {
		$intitle = substr($body,-1);
		$body = substr($body,0,-2);
	} else {
		$intitle = "Y";	//N:상단 타이틀 이미지 기본 사용, Y:상단 타이틀 이미지 내용속에 포함
	}
}
mysql_free_result($result);

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.body.value.length==0) {
			alert("이용안내화면 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("이용안내화면 디자인을 삭제하시겠습니까?")) {
			document.form1.type.value=type;
			document.form1.submit();
		}
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
			alert("이용안내화면 디자인 내용을 입력하세요.");
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt; <span class="2depth_select">이용안내 화면 꾸미기</a></td>
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
					<TD><IMG SRC="images/design_eachuseinfo_title.gif" ALT=""></TD>
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
					<TD width="100%" class="notice_blue">쇼핑몰 이용안내 화면 디자인을 자유롭게 디자인 하실 수 있습니다.</TD>
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
					<TD><IMG SRC="images/design_eachuseinfo_stitle1.gif" WIDTH="250" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td style="padding-bottom:3pt;" >
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">1) HTML 입력이 가능하므로 원하시는 디자인으로 변경하여 사용하시면 됩니다.(부분HTML, TEXT 모두 지원됨)<br>2) [삭제하기] -> 기존 사용하던 템플릿으로 변경됨 -> 템플릿 메뉴에서 원하는 템플릿 선택<br>
					&nbsp;&nbsp;&nbsp;&nbsp;* 기본템플릿이 아닌 기존 템플릿으로 변경됩니다.<br>
					&nbsp;&nbsp;&nbsp;&nbsp;* 삭제하지 않고 템플릿메뉴에서 템플릿을 재선택해도  개별디자인은 해제되지 않습니다.</TD>
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
				<td style="padding-top:2px;"><textarea name=body style="WIDTH: 100%; HEIGHT: 300px" class="textarea"><?=htmlspecialchars($body)?></textarea><br><input type=checkbox name=intitle value="Y" <?if($intitle=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\"> <b><span style="letter-spacing:-0.5pt;"><span class="font_orange">기본 타이틀 이미지 유지 - 타이틀 이하 부분부터 디자인 변경</span>(미체크시 기존 타이틀 이미지 없어짐으로 직접 편집하여 사용)</b></span></td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" width="113" height="38" border="0" hspace="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('preview');"><img src="images/botteon_prev.gif" width="113" height="38" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('store');"><img src="images/botteon_store.gif" border="0" hspace="2" alt="백업하기"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('restore');"><img src="images/botteon_restore.gif" border="0" hspace="2" alt="백업복원하기"></a></td>
			</tr>
			</form>
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