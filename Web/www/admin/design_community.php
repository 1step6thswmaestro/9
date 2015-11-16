<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-6";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$maxpage=5;

$type=$_POST["type"];
$code=$_POST["code"];
$subject=$_POST["subject"];
$menu_type=$_POST["menu_type"];
$menu_code=$_POST["menu_code"];
$member_type=$_POST["member_type"];
$group_code=$_POST["group_code"];
$new_body=$_POST["new_body"];

if(strlen($menu_type)==0) $menu_type="Y";
if(strlen($member_type)==0) $member_type="Y";



// 백업 / 복구
if ( $type=="store" OR $type=="restore" ) {

	$leftmenu=$menu_type;
	$filename=$member_type;
	if($member_type=="G") {
		$filename=$group_code;
	}
	if($menu_type=="Y") {
		$filename.="".$menu_code;
	}

	$MSG = adminDesingBackup ( $type, 'community', $new_body, $subject, $code, $filename, $leftmenu );
	$onload="<script>alert(\"".$MSG."\");</script>";
}



if($type=="delete" && strlen($code)>0) {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='community' AND code='".$code."' ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert(\"커뮤니티 페이지 삭제가 완료되었습니다.\");</script>";
	$subject="";
	$menu_type="Y";
	$menu_code="";
	$member_type="Y";
	$group_code="";
	$new_body="";
} else if($type=="update" && strlen($new_body)>0) {
	$leftmenu=$menu_type;
	$filename=$member_type;
	if($member_type=="G") {
		$filename=$group_code;
	}
	if($menu_type=="Y") {
		$filename.="".$menu_code;
	}
	if(strlen($code)==0) {
		$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='community' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$cnt=(int)$row->cnt;
		mysql_free_result($result);
		if($cnt==$maxpage) {
			$onload="<script>alert(\"커뮤니티 페이지는 최대 ".$maxpage."페이지까지 지원됩니다.\\n\\n다른 페이지를 삭제 후 등록하시기 바랍니다.\");</script>";
		} else {
			$sql = "SELECT MAX(code*1) as maxcode FROM tbldesignnewpage ";
			$sql.= "WHERE type='community' ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			$code=(int)$row->maxcode+1;
			mysql_free_result($result);
			$sql = "INSERT tbldesignnewpage SET ";
			$sql.= "type			= 'community', ";
			$sql.= "subject			= '".$subject."', ";
			$sql.= "filename		= '".$filename."', ";
			$sql.= "leftmenu		= '".$leftmenu."', ";
			$sql.= "body			= '".$new_body."', ";
			$sql.= "code			= '".$code."' ";
			mysql_query($sql,get_db_conn());
			$onload="<script>alert(\"커뮤니티 페이지 등록이 완료되었습니다.\");</script>";
		}
	} else {
		$sql = "UPDATE tbldesignnewpage SET ";
		$sql.= "subject			= '".$subject."', ";
		$sql.= "filename		= '".$filename."', ";
		$sql.= "leftmenu		= '".$leftmenu."', ";
		$sql.= "body			= '".$new_body."' ";
		$sql.= "WHERE type='community' AND code='".$code."' ";
		mysql_query($sql,get_db_conn());
		$onload="<script>alert(\"커뮤니티 페이지 수정이 완료되었습니다.\");</script>";
	}
}

if(strlen($code)>0) {
	$sql = "SELECT * FROM tbldesignnewpage WHERE type='community' AND code='".$code."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$subject=$row->subject;
		$menu_type=$row->leftmenu;
		$filename=explode("",$row->filename);
		$member_type=$filename[0];
		$menu_code=$filename[1];
		$new_body=$row->body;
		if(strlen($member_type)>1) {
			$group_code=$member_type;
			$member_type="G";
		}
	}
	mysql_free_result($result);
} else {
	$subject="";
	$menu_type="Y";
	$menu_code="";
	$member_type="Y";
	$group_code="";
	$new_body="";
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(type=="delete") {
		if(document.form1.code.value.length==0) {
			alert("삭제할 페이지를 선택하세요.");
			document.form1.code.focus();
			return;
		} else {
			if(confirm("해당 페이지를 삭제하시겠습니까?")) {
				document.form1.type.value=type;
				document.form1.submit();
			}
		}
	} else if(type=="update") {
		if(document.form1.subject.value.length==0) {
			alert("HTML 문서명을 입력하세요.");
			document.form1.subject.focus();
			return;
		}
		member_type="";
		for(i=0;i<document.form1.member_type.length;i++) {
			if(document.form1.member_type[i].checked==true) {
				member_type=document.form1.member_type[i].value;
				break;
			}
		}
		if(member_type=="G") {
			if(document.form1.group_code.value=="") {
				alert("해당 등급을 선택하세요.");
				document.form1.group_code.focus();
				return;
			}
		}
		if(document.form1.new_body.value.length==0) {
			alert("페이지 내용을 입력하세요.");
			document.form1.new_body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	}


	// 백업
	if(type=="store") {

		if(document.form1.subject.value.length==0) {
			alert("HTML 문서명을 입력하세요.");
			document.form1.subject.focus();
			return;
		}
		member_type="";
		for(i=0;i<document.form1.member_type.length;i++) {
			if(document.form1.member_type[i].checked==true) {
				member_type=document.form1.member_type[i].value;
				break;
			}
		}
		if(member_type=="G") {
			if(document.form1.group_code.value=="") {
				alert("해당 등급을 선택하세요.");
				document.form1.group_code.focus();
				return;
			}
		}
		if(document.form1.new_body.value.length==0) {
			alert("페이지 내용을 입력하세요.");
			document.form1.new_body.focus();
			return;
		}

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

function change_page(val) {
	document.form1.type.value="change";
	document.form1.submit();
}

function check_menutype(val) {
	if(val=="Y") {
		document.form1.menu_code.disabled=false;
	} else {
		document.form1.menu_code.disabled=true;
	}
}
function check_membertype(val) {
	if(val=="G") {
		document.form1.group_code.disabled=false;
	} else {
		document.form1.group_code.disabled=true;
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; 개별추가페이지 관리 &gt; <span class="2depth_select">커뮤니티 페이지 꾸미기</span></td>
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
					<TD><IMG SRC="images/design_community_title.gif" ALT=""></TD>
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
					<TD width="100%" class="notice_blue">커뮤니티 페이지를 등록 및 관리하실 수 있습니다.</TD>
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
					<TD><IMG SRC="images/design_community_stitle1.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
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
					<TD width="100%" class="notice_blue">1) HTML 입력이 가능하므로 원하시는 디자인으로 작성하여 사용하시면 됩니다.(HTML만 지원, 부분HTML, TEXT 지원 안됨)<br>
					2) 최대 5페이지까지 제공됩니다.<br>
					3) 왼쪽메뉴를 개별 디자인한 경우 [개별디자인 적용선택]을 해야 합니다.(템플릿 사용시에는  템플릿의 디자인으로 출력)<br>
					<b>&nbsp;&nbsp;&nbsp;</b><a href="javascript:parent.topframe.GoMenu(2,'design_option.php');"><span class="font_blue">디자인관리 > 웹FTP 및 개별적용 선택 > 개별디자인 적용선택</span></a> [상단+왼쪽 동시 적용][왼쪽만 적용]<br>
					4) 프레임 설정 메뉴<br>
					&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:parent.topframe.GoMenu(1,'shop_displaytype.php');"><span class="font_blue">상점관리 > 쇼핑몰 환경 설정 > 프레임/정렬 설정</span></a></TD>
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
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">페이지 선택</TD>
					<TD class="td_con1"><select name=code onchange="change_page(options.value)" style="width:330px" class="select">
						<option value="">새로운 페이지 생성</option>
<?
			unset($arr_newpage);
			$sql = "SELECT subject,code FROM tbldesignnewpage WHERE type='community' ";
			$result=mysql_query($sql,get_db_conn());
			while($row=mysql_fetch_object($result)) {
				echo "<option value=\"".$row->code."\" ";
				if($code==$row->code) echo "selected";
				echo ">code : ".$row->code." - ".$row->subject."</option>\n";
				$arr_newpage[]=$row;
			}
			mysql_fetch_object($result);
?>
					</select></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">HTML 문서명</TD>
					<TD class="td_con1" >
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="90%"><input type=text name=subject value="<?=$subject?>" style="WIDTH:100%" class="input"></td>
						<td width="10%"><p align="center"><?if(strlen($code)>0) echo "코드 : ".$code;?></p></td>
					</tr>
					</table>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">문서 형태</TD>
					<TD class="td_con1" ><p><input type=radio id="idx_menutype0" name=menu_type value="Y" <?if($menu_type=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="check_menutype(this.value)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_menutype0>상단/왼쪽 메뉴 모두출력</label>(투 프레임은 상단 메뉴는 출력되지 않음)<br>
						<input type=radio id="idx_menutype1" name=menu_type value="T" <?if($menu_type=="T")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="check_menutype(this.value)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_menutype1>상단 메뉴만 출력</label>(투 프레임은 상/하단 메뉴 모두 출력되지 않음)<br>
						<input type=radio id="idx_menutype2" name=menu_type value="N" <?if($menu_type=="N")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="check_menutype(this.value)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_menutype2>상단/왼쪽 메뉴 모두 미출력</label>(원프레임/투프레임 모두 동일)</p></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<tr>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">왼쪽메뉴 선택</TD>
					<TD class="td_con1" ><select name="menu_code" style="width:150px" class="select">
<?
			//$page_list=array("메인 페이지 왼쪽메뉴","게시판 관련 왼쪽메뉴","회원 관련 왼쪽메뉴","마이페이지 관련 왼쪽메뉴","주문서 관련 왼쪽메뉴","검색 관련 왼쪽메뉴","개별 페이지 왼쪽메뉴1","개별 페이지 왼쪽메뉴2","개별 페이지 왼쪽메뉴3","개별 페이지 왼쪽메뉴4","개별 페이지 왼쪽메뉴5","개별 페이지 왼쪽메뉴6","개별 페이지 왼쪽메뉴7","개별 페이지 왼쪽메뉴8","개별 페이지 왼쪽메뉴9","개별 페이지 왼쪽메뉴10");
			//$page_code=array("MAI","BOA","MEM","MYP","ORD","SEA","NE0","NE1","NE2","NE3","NE4","NE5","NE6","NE7","NE8","NE9");
			
			$page_list=array("메인 페이지 왼쪽메뉴","게시판-고객센터 왼쪽메뉴","게시판-커뮤니티 왼쪽메뉴","게시판-개별 왼쪽메뉴","회원 관련 왼쪽메뉴","마이페이지 관련 왼쪽메뉴","주문서 관련 왼쪽메뉴","검색 관련 왼쪽메뉴","개별 페이지 왼쪽메뉴1","개별 페이지 왼쪽메뉴2","개별 페이지 왼쪽메뉴3","개별 페이지 왼쪽메뉴4","개별 페이지 왼쪽메뉴5","개별 페이지 왼쪽메뉴6","개별 페이지 왼쪽메뉴7","개별 페이지 왼쪽메뉴8","개별 페이지 왼쪽메뉴9","개별 페이지 왼쪽메뉴10");
			//$page_code=array("MAI","BOA","MEM","MYP","ORD","SEA","NE0","NE1","NE2","NE3","NE4","NE5","NE6","NE7","NE8","NE9");
			$page_code=array("MAI","BOU","BOM","BTC","MEM","MYP","ORD","SEA","NE0","NE1","NE2","NE3","NE4","NE5","NE6","NE7","NE8","NE9");
			for($i=0;$i<count($page_list);$i++) {
				echo "<option value=\"".$page_code[$i]."\" ";
				if($menu_code==$page_code[$i]) echo "selected";
				echo ">".$page_list[$i]."</option>\n";
			}
?>
					</select>&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* 상단/왼쪽 메뉴 모두 출력한 경우에만 적용됩니다.</span><script>check_menutype("<?=$menu_type?>")</script></TD>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<tr>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">회원제 선택</TD>
					<TD class="td_con1" ><input type=radio id="idx_membertype0" name=member_type value="Y" <?if($member_type=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="check_membertype(this.value)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_membertype0>회원</label>&nbsp;&nbsp;
						<input type=radio id="idx_membertype1" name=member_type value="N" <?if($member_type=="N")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="check_membertype(this.value)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_membertype1>회원+비회원</label>&nbsp;&nbsp;
						<input type=radio id="idx_membertype2" name=member_type value="G" <?if($member_type=="G")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="check_membertype(this.value)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_membertype2>특정 회원등급</label>&nbsp;<select name=group_code style="width:250px" class="select">
						<option value="">해당 등급을 선택하세요</option>
<?
			$sql = "SELECT group_code,group_name FROM tblmembergroup ";
			$result=mysql_query($sql,get_db_conn());
			while($row=mysql_fetch_object($result)) {
				echo "<option value=\"".$row->group_code."\" ";
				if($group_code==$row->group_code) echo "selected";
				echo ">".$row->group_name."</option>\n";
			}
			mysql_free_result($result);
?>
					</select><script>check_membertype("<?=$member_type?>")</script></TD>
				</tr>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="30"><textarea name=new_body style="WIDTH: 100%; HEIGHT: 350px" class="textarea"><?=htmlspecialchars($new_body)?></textarea></td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" width="113" height="38" border="0" hspace="2"></a><? if(strlen($code)>0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('store');"><img src="images/botteon_store.gif" border="0" hspace="2" alt="백업하기"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('restore');"><img src="images/botteon_restore.gif" border="0" hspace="2" alt="백업복원하기"></a><? } ?></td>
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
					<TD COLSPAN=3 width="100%" valign="top" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"  class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><p class="LIPoint"><B>커뮤니티 URL리스트</B></p></td>
					</tr>
					<tr>
						<td width="20" align="right"><p>&nbsp;</p></td>
						<td class="space_top">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
<?
		for($i=0;$i<count($arr_newpage);$i++) {
			if($i == count($arr_newpage)-1) {
?>
						<tr>
							<TD class="table_cell4" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=180 bgColor=#f0f0f0 height="27"><?=$arr_newpage[$i]->subject?></td>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%">&lt;a href="<?=RootPath.FrontDir?>community.php?code=<?=$arr_newpage[$i]->code?>"><?=$arr_newpage[$i]->subject?>&lt;/a></td>
						</tr>
<?
			} else {
?>
						<tr>
							<TD class="table_cell4" style="padding-right:15px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" noWrap align=right width=180 bgColor=#f0f0f0 height="27"><?=$arr_newpage[$i]->subject?></td>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" width="100%">&lt;a href="<?=RootPath.FrontDir?>community.php?code=<?=$arr_newpage[$i]->code?>"><?=$arr_newpage[$i]->subject?>&lt;/a></td>
						</tr>
<?
			}
		}
		if(count($arr_newpage)==0) {
			echo "<tr><TD class=\"td_con1\" style=\"padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;\" width=\"100%\" colspan=\"2\"><B>등록된 커뮤니티 페이지가 존재하지 않습니다.</B></td></tr>\n";
		}
?>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td width="20" colspan="2" height="20"><p>&nbsp;</p></td>
					</tr>
					<tr>
						<td width="20" align="right"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td class="LIPoint">나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</td>
					</tr>
					<tr>
						<td width="20" align="right"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td class="LIPoint"><B><span class="font_orange">커뮤니티 매크로명령어</span></B>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</td>
					</tr>
					<tr>
						<td width="20" align="right"><p>&nbsp;</p></td>
						<td class="space_top">
						<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
						<tr><td colspan="2" height="1" bgcolor="#dddddd"></td></tr>
						<tr>
							<td class="table_cell4" width="180" align="right" style="padding-right:15" noWrap>[FAQFORMSTART]</td>
							<td class="td_con1" width="100%" style="padding-left:5px;">
								FAQ 검색폼 시작 - 폼의 끝나는 부분에서는 [FAQFORMEND] 입력
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class="table_cell4" width="180" align="right" style="padding-right:15" noWrap>[FAQINPUTBOX]</td>
							<td class="td_con1" width="100%" style="padding-left:5px;">
								검색 키워드 입력박스
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class="table_cell4" width="180" align="right" style="padding-right:15" noWrap>[FAQSEARCH]</td>
							<td class="td_con1" width="100%" style="padding-left:5px;">
								검색하기 버튼 링크 <font class="font_blue">(예:&lt;a href=[FAQSEARCH]&gt;검색&lt;/a&gt;)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class="table_cell4" width="180" align="right" style="padding-right:15" noWrap>[BOARD_SUB_CATE:게시판명]</td>
							<td class="td_con1" width="100%" style="padding-left:5px;">
								게시판 말머리 바로가기(select 박스형)
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>

						<tr>
							<td class="table_cell4" width="180" align="right" style="padding-right:15" noWrap>[FAQFORMEND]</td>
							<td class="td_con1" width="100%" style="padding-left:5px;">
								FAQ 검색폼 끝
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<TR>
							<TD class="table_cell" align="right" style="padding-right:15">[BOARD?????_000_?]</TD>
							<TD class="td_con1" style="padding-left:5px;"><img src="images/icon_board.gif" width="60" height="14" border="0" vspace="3"><BR><FONT color=#ff6600>? : 1,2,3,4,5,6,7,8,9 아홉개의 게시판에 대해서 최근 게시물 출력</FONT><BR><FONT color=#ff6600>? : 게시일자 표시방법 (0:게시일자미표시, 1:월/일, 2:년/월/일)</FONT> <BR> <FONT color=#ff6600>? : 메인에 표시할 게시글 갯수(1-9)</FONT> <BR> <FONT color=#ff6600>? : 게시판 글 사이의 간격(0-9)</FONT> <BR> <FONT color=#ff6600>? : 답변글 추출 여부(Y/N)</FONT> <BR> <FONT color=#ff6600>000 : 표시될 게시글 길이 (최대 숫자 200까지)<br></FONT><FONT color=#ff6600>? : 게시판 코드 (해당 게시판에 부여된 고유코드)</FONT> <BR><span class="font_blue"><b>예)[BOARD1154N_80_soora25], [BOARD2154Y_50_soora25]</b></span></TD>
						</TR>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class="table_cell" align="right" style="padding-right:15">[REVIEW??????_000]</td>
							<td class=td_con1 style="padding-left:5;">
								상품평 표시
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 목록 정렬 방법 (0:최근등록순, 1:높은평점순)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 상품평 글 링크 방법(0:팝업으로 상품평 출력, 1:상품평 상품 상세페이지)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 작성일자 표시방법 (0:게시일자미표시, 1:월/일, 2:년/월/일)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 메인에 표시할 상품평글 갯수(1-9)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 상품평 글 사이의 간격(0-9)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 평점 표시 여부(Y/N)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>_000 : 표시될 게시글 길이 (최대 숫자 200까지)</FONT>
								<br>
								<FONT class=font_blue>예) [REVIEW10154Y_80], [REVIEW01254N_50]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						</TABLE>
						</TD>
					</TR>
					</TABLE>
					</td>
					<TD background="images/manual_right1.gif"></TD>
				</tr>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</table>
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