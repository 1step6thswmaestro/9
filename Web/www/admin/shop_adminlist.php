<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-4";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_POST["mode"];
$type=$_POST["type"];
$up_id=$_POST["up_id"];
$adminname=$_POST["adminname"];
$passwd=$_POST["passwd"];
$adminemail=$_POST["adminemail"];
$adminmobile=$_POST["adminmobile"];
$oldips=$_POST["oldips"];
$newips=$_POST["newips"];
$roleidx=$_POST["roleidx"];
$disabled=(int)$_POST["disabled"];

function getipidx($ipaddress) {
	global $_ShopInfo;
	$sql = "SELECT idx FROM tblsecurityiplist WHERE ipaddress = '".$ipaddress."' ";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	mysql_free_result($result);
	$ipidx = (int)$row->idx;
	return $ipidx;
}

function isduplicatenewip($newip) {
	global $_ShopInfo;
	$sql = "SELECT ipaddress FROM tblsecurityiplist WHERE ipaddress = '".$newip."'";
	$result = mysql_query($sql,get_db_conn());
	$rows = mysql_num_rows($result);
	mysql_free_result($result);
	if($rows > 0)
		return true;
	return false;
}

function setnewip($newips,$oldips) {
	global $_ShopInfo;
	$ipidxs = array();
	if($newips) {
		$newipvalues = explode(",",$newips);
		for($i=0;$i<count($newipvalues);$i++) {
			if ($newipvalues[$i]) {
				$newipadd = $newipvalues[$i];
				if(!isDuplicateNewIP($newipadd)) {
					$sql = "INSERT INTO tblsecurityiplist (ipaddress,disabled) VALUES ('".$newipadd."',0)";
					mysql_query($sql,get_db_conn());
				}
			}
		}
	}

	if(count($oldips) > 0) {
		for($i = 0; $i < count($oldips); $i++)
			$ipidxs[] = $oldips[$i];
	}

	$newipidx = (int)-1;
	$ipexist = false;
	if($newips) {
		$newipvalues = explode(",",$newips);
		for($i=0;$i<count($newipvalues);$i++) {
			$newipidx = getIPIDX($newipvalues[$i]);
			$ipexist = false;
			if ($newipidx) {
				if(count($oldips) > 0) {
					for($j = 0; $j < count($ipidxs); $j++) {
						if($ipidxs[$j] == $newipidx) {
							$ipexist = true;
							$j = count($ipidxs);
						}
					}
					if(!$ipexist)
						$ipidxs[] = $newipidx;
				} else {
					$ipidxs[] = $newipidx;
				}
			}
		}
	}
	return (array)$ipidxs;
}

if ($mode=="edit") {
	$sql = "SELECT id, admintype, adminname, adminemail,adminmobile, disabled FROM tblsecurityadmin ";
	$sql.= "WHERE id = '".$up_id."' ";
	$result = mysql_query($sql,get_db_conn());
	if ($row = mysql_fetch_object($result)) {
		$up_id=$row->id;
		$admintype=$row->admintype;
		$adminname=$row->adminname;
		$adminemail=$row->adminemail;
		$adminmobile=$row->adminmobile;
		$disabled=$row->disabled;

		$id_readonly = "readonly";
		$id_style = "style=\"background:#eeeeee\"";

		$superadmin = false;
		if($admintype==1) {
			$superadmin = true;
			$submit = true;
			$disabled_disabled = "disabled";
			if ($up_id != $_ShopInfo->getId()) {
				$disabled_form = "disabled style=\"background:#eeeeee\"";
				$submit = false;
			}
		}

		if ($superadmin && $admintype!=1) {
			$disabled_form = "disabled style=\"background:#eeeeee\"";
		}
		${"check_".$disabled} = "checked";

	} else {
		echo "<script>alert('해당 운영자/부운영자 ID가 존재하지 않습니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
		exit;
	}
	mysql_free_result($result);
} else if ($mode=="del") {
	$sql = "SELECT id FROM tblsecurityadmin WHERE id = '".$up_id."' AND admintype=0 ";
	$result = mysql_query($sql,get_db_conn());
	if ($row = mysql_fetch_object($result)) {
		$sql = "DELETE FROM tblsecurityadmin WHERE id = '".$up_id."' ";
		mysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tblsecurityadminip WHERE id='".$up_id."' ";
		mysql_query($sql,get_db_conn());
		$sql = "DELETE FROM tblsecurityadminrole WHERE id = '".$up_id."' ";
		mysql_query($sql,get_db_conn());

		echo "<script>alert('해당 아이디 (".$up_id.")를 삭제하였습니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
		exit;
	} else {
		echo "<script>alert('해당 운영자/부운영자 ID가 존재하지 않습니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
		exit;
	}
	mysql_free_result($result);
}

if ($type=="insert") {
	if (!$up_id || !$passwd || !$roleidx) {
		echo "<script>alert('필수 입력 항목 등록이 잘못되었습니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
		exit;
	}
	if (!eregi("^[a-zA-Z0-9]*$", $up_id)) {
		echo "<script>alert('ID에는 영문/숫자만 입력하세요.');location='".$_SERVER[PHP_SELF]."';</script>";
		exit;
	}

	$sql = "SELECT id FROM tblsecurityadmin WHERE id = '".$up_id."' ";
	$result = mysql_query($sql,get_db_conn());
	$rows = (boolean)mysql_num_rows($result);
	mysql_free_result($result);
	if ($rows) {
		echo "<script>alert('(".$up_id.") 아이디는 현재 사용중입니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
		exit;
	}

	$ipidxs = (array)setnewip($newips,$oldips);
	$sql = "INSERT tblsecurityadmin SET ";
	$sql.= "id				= '".$up_id."', ";
	$sql.= "passwd			= '".md5($passwd)."', ";
	$sql.= "admintype		= 0, ";
	$sql.= "adminname		= '".$adminname."', ";
	$sql.= "adminemail		= '".$adminemail."', ";
	$sql.= "adminmobile		= '".$adminmobile."', ";
	$sql.= "expirydate		= '0', ";
	$sql.= "registerdate	= '".time()."', ";
	$sql.= "disabled		= '".$disabled."' ";
	$insert = mysql_query($sql,get_db_conn());
	if ($insert) {
		for($i = 0; $i < count($ipidxs); $i++) {
			$sql = "INSERT INTO tblsecurityadminip (id,ipidx) VALUES ('".$up_id."',".$ipidxs[$i].")";
			mysql_query($sql,get_db_conn());
		}

		$sql = "INSERT INTO tblsecurityadminrole (id,roleidx) VALUES ('".$up_id."','".$roleidx."')";
		mysql_query($sql,get_db_conn());
	}
	echo "<script>alert('(".$up_id.") 부운영자 추가가 완료되었습니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
	exit;
} else if ($type=="edit") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서 수정 테스트는 불가능 합니다.", $_SERVER[PHP_SELF]);
	#######################################################################

	$sql = "SELECT id,admintype FROM tblsecurityadmin ";
	$sql.= "WHERE id = '".$up_id."' ";
	$result = mysql_query($sql,get_db_conn());
	if (!$row = mysql_fetch_object($result)) {
		echo "<script>alert('해당 운영자/부운영자 ID가 존재하지 않습니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
		exit;
	}
	mysql_free_result($result);

	$disabled = 0;
	$superadmin = false;
	if($row->admintype==1)
		$superadmin = true;

	if (!$superadmin)
		$disabled = $_POST[disabled];

	$ipidxs = (array)setnewip($newips,$oldips);

	$sql = "UPDATE tblsecurityadmin SET ";
	$sql.= "adminname		= '".$adminname."', ";
	$sql.= "adminemail		= '".$adminemail."', ";
	$sql.= "adminmobile		= '".$adminmobile."', ";
	if ($passwd) {
		$sql.= "passwd		= '".md5($passwd)."', ";
	}
	$sql.= "disabled		= '".$disabled."' ";
	$sql.= "WHERE id = '".$up_id."' ";
	mysql_query($sql,get_db_conn());


	$sql = "DELETE FROM tblsecurityadminip WHERE id = '".$up_id."' ";
	mysql_query($sql,get_db_conn());
	for($i = 0; $i < count($ipidxs); $i++) {
		$sql = "INSERT INTO tblsecurityadminip (id,ipidx) VALUES ('".$up_id."','".$ipidxs[$i]."')";
		mysql_query($sql,get_db_conn());
	}

	if(!$superadmin) {
		$sql = "DELETE FROM tblsecurityadminrole WHERE id = '".$up_id."' ";
		mysql_query($sql,get_db_conn());
		$sql = "INSERT INTO tblsecurityadminrole (id,roleidx) VALUES ('".$up_id."',".$roleidx.")";
		mysql_query($sql,get_db_conn());
	}
	echo "<script>alert('운영자/부운영자 정보 수정이 완료되었습니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
	exit;
}

$mode = $mode ? $mode : "insert";
if ($mode=="edit") {
	if ($disabled_form) {
		$button_value = "images/btn_edit1.gif";
	} else {
		$button_value = "images/btn_edit2.gif";
	}
} else if ($mode=="insert") {
	if ($disabled_form) {
		$button_value = "images/btn_badd1.gif";
	} else {
		$button_value = "images/btn_badd2.gif";
	}
}
?>

<? INCLUDE ("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(type) {
	if (type=="insert") {
		if(form1.up_id.value.length<=0) {
			alert("부운영자 아이디를 입력하세요.");
			form1.up_id.focus();
			return;
		}
		if (form1.passwd.value.length<=0) {
			alert("패스워드를 입력하세요.");
			form1.passwd.focus();
			return;
		}
		if (form1.passwd.value != form1.passwd2.value) {
			alert("패스워드가 일치하지 않습니다.");
			form1.passwd2.focus();
			return;
		}
	}
	if (!form1.roleidx.value) {
		alert("권한그룹 선택을 하세요.");
		form1.roleidx.focus();
		return;
	}
	var ra = false;
	for(var i=0;i<form1.disabled.length;i++){
		if(form1.disabled[i].checked==true){
			ra=true;
			break;
		}
	}
	if(!ra){
		alert("로그인 허용여부를 선택하세요.");
		form1.disabled[0].focus();
		return;
	}
	form1.type.value=type;
	form1.submit();
}

function check_form(mode,id) {
	if (mode=="del") {
		var con=confirm("해당 운영자("+id+")를 삭제 하시겠습니까? (복구 불가능)");
		if (!con) {
			return ;
		}
	}
	form2.mode.value=mode;
	form2.up_id.value=id;
	form2.submit();
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
			<? include ("menu_shop.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 보안설정 &gt; <span class="2depth_select">운영자/부운영자 설정</span></td>
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
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_adminlist_title.gif"  ALT=""></TD>
					</tr>
<tr>
<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
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
					<TD width="100%" class="notice_blue"><p>운영자/부운영자 정보 관리 및 생성,	부운영자 별 메뉴 사용권한/접속제한 등을 설정할 수 있습니다.</p></TD>
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
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_rolelist_stitle1.gif" WIDTH="152" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" colspan="9"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="84" align="center">ID</TD>
					<TD class="table_cell1" width="84" align="center">권한그룹</TD>
					<TD class="table_cell1" width="44" align="center">로그인</TD>
					<TD class="table_cell1" width="39" align="center">이름</TD>
					<TD class="table_cell1" width="90" align="center">E-Mail</TD>
					<TD class="table_cell1" width="71" align="center">연락처</TD>
					<TD class="table_cell1" width="90" align="center">최근접속일</TD>
					<TD class="table_cell1" width="59" align="center">수정</TD>
					<TD class="table_cell1" width="61" align="center">삭제</TD>
				</TR>
				<TR>
					<TD colspan="9" background="images/table_con_line.gif"></TD>
				</TR>
<?
				$count = 0;
				$sql = "SELECT id, admintype, adminname, adminemail, adminmobile, lastlogintime, disabled ";
				$sql.= "FROM tblsecurityadmin ORDER BY id ASC ";
				$result = mysql_query($sql,get_db_conn());
				while($row=mysql_fetch_object($result)) {
					$count++;
					echo "<tr>\n";
					echo "	<TD class=\"td_con2\" width=\"100\" align=\"center\"><span class=font_orange><B>".$row->id."</B></span></td>\n";
					$allowedroles = "";
					$sql = "SELECT r.description as rdesc FROM tblsecurityadminrole a, tblsecurityrole r ";
					$sql.= "WHERE a.roleidx = r.idx AND a.id = '".$row->id."' ";
					$result2 = mysql_query($sql,get_db_conn());
					if ($row2 = mysql_fetch_object($result2)) {
						$allowedroles = $row2->rdesc;
						echo "	<TD class=\"td_con1\" width=\"91\" align=\"center\"><span class=font_orange><B>".$allowedroles."</B></span></td>\n";
					} else {
						echo "	<TD class=\"td_con1\" width=\"91\" align=\"center\">--/--/--</td>\n";
					}
					mysql_free_result($result2);

					if ($row->disabled == 0) {
						echo "	<TD class=\"td_con1\" width=\"51\" align=\"center\">가능</td>\n";
					} else {
						echo "	<TD class=\"td_con1\" width=\"51\" align=\"center\">불가능</td>\n";
					}
					$row->adminname = $row->adminname ? $row->adminname : "--/--/--";
					$row->adminemail = $row->adminemail ? $row->adminemail : "--/--/--";
					$row->adminmobile = $row->adminmobile ? $row->adminmobile : "--/--/--";
					echo "	<TD class=\"td_con1\" width=\"46\" align=\"center\">".$row->adminname."</td>\n";
					echo "	<TD class=\"td_con1\" width=\"97\" align=\"center\">".$row->adminemail."</td>\n";
					echo "	<TD class=\"td_con1\" width=\"78\" align=\"center\">".$row->adminmobile."</td>\n";
					if ($row->lastlogintime > 0) {
						echo "	<TD class=\"td_con1\" width=\"97\" align=\"center\">".date("Y/m/d H:i:s",$row->lastlogintime)."</td>\n";
					} else {
						echo "	<TD class=\"td_con1\" width=\"97\" align=\"center\">--/--/--</td>\n";
					}
					echo "	<TD class=\"td_con1\" width=\"66\" align=\"center\"><a href=\"javascript:check_form('edit','".$row->id."');\"><img src=\"images/btn_edit.gif\" width=\"50\" height=\"22\" border=\"0\"></a></td>\n";
					if ($row->admintype==1) {
						echo "	<TD class=\"td_con1\" width=\"68\" align=\"center\"><img src=\"images/btn_del1.gif\" width=\"50\" height=\"22\" border=\"0\"></td>\n";
					} else {
						echo "	<TD class=\"td_con1\" width=\"68\" align=\"center\"><a href=\"javascript:check_form('del','".$row->id."');\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></a></td>\n";
					}
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<TD colspan=\"9\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</tr>\n";
				}
				mysql_free_result($result);

				if ($count == 0) {
					echo "<tr>\n";
					echo "	<TD class=\"td_con1\" align=\"center\" colspan=\"9\">등록된 운영자/부운영자가 없습니다.</td>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<TD colspan=\"9\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</tr>\n";
				}
?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="9"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="30"></td>
			</tr>
			<tr>
				<td height=10>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_rolelist_stitle1.gif" WIDTH="152" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
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
					<TD width="100%" class="notice_blue">1) 운영자/부운영자 로그인은 회사 홈페이지에서만 가능합니다.<br>2) 비밀번호는 암호화 되어 알 수 없으며 변경만 가능합니다.(아이디는 영문, 숫자만 가능)</TD>
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
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=4 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">아이디</TD>
					<TD class="td_con1" width="30%"><input type=text name=up_id value="<?=$up_id?>" size=20 maxlength=20 <?=$id_readonly?> class="input" style=width:98%></TD>
					<TD class="table_cell2" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">이름</TD>
					<TD class="td_con1"  width="30%"><input type=text name=adminname value="<?=$adminname?>" size=20 maxlength=20 <?=$disabled_form?> class="input" style=width:99%></TD>
				</TR>
				<TR>
					<TD colspan="4" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">비밀번호</TD>
					<TD class="td_con1" ><input type=password name=passwd size=20 maxlength=20 <?=$disabled_form?> class="input" style=width:98%></TD>
					<TD class="table_cell2" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">비밀번호 확인</TD>
					<TD class="td_con1" ><input type=password name=passwd2 size=20 maxlength=20 <?=$disabled_form?> class="input" style=width:99%></TD>
				</TR>
				<TR>
					<TD colspan="4" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">E-Mail</TD>
					<TD class="td_con1" ><input type=text name=adminemail value="<?=$adminemail?>" size=30 maxlength=50 <?=$disabled_form?> class="input" style=width:98%></TD>
					<TD class="table_cell2" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">연락처</TD>
					<TD class="td_con1" ><input type=text name=adminmobile value="<?=$adminmobile?>" size=30 maxlength=50 <?=$disabled_form?> class="input" style=width:99%></TD>
				</TR>
				<TR>
					<TD colspan="4" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">접근 IP 선택</TD>
					<TD class="td_con1" " colspan="3">
<?
				if ($mode=="edit") {
					$ipsarray = array();

					$sql = "SELECT ipidx FROM tblsecurityadminip ";
					$sql.= "WHERE id = '".$up_id."' ";
					$result = mysql_query($sql,get_db_conn());
					while($row = mysql_fetch_object($result)) {
						$ipsarray[] = $row->ipidx;
						if ($row->ipidx == 0) {
							$isallip = true;
						}
					}
					mysql_free_result($result);

					$all = false;
					$sql = "SELECT idx, ipaddress FROM tblsecurityiplist ";
					$sql.= "WHERE disabled = 0 ORDER BY ipaddress";
					$result = mysql_query($sql,get_db_conn());

					echo "<select name='oldips[]' size='10' multiple style=\"WIDTH:100%;height:100px\" class=\"textarea\" ".$disabled_form.">";
					if($isallip) {
						echo chr(10).("<option value='0' selected>Any");
						$all = true;
					} else {
						echo chr(10).("<option value='0' >Any");
						$all = false;
					}
					$tempipidx = 0;
					$ipidx = 0;
					while($row = mysql_fetch_object($result)) {
						$ipidx = (int)$row->idx;
						$ipaddress = $row->ipaddress;
						$selected = false;
						//if(!$all) {
							for($i = 0; $i < count($ipsarray); $i++) {
								$tempipidx = (int)$ipsarray[$i];
								if($ipidx == $tempipidx) {
									$selected = true;
									$i = count($ipsarray);
								}
							}

						//}
						if($selected)
							echo chr(10).("<option value=" . $ipidx . " selected>" . $ipaddress . "");
						else
							echo chr(10).("<option value=" . $ipidx . " >" . $ipaddress . "");
					}
					mysql_free_result($result);

					echo "</select>";
				} else {
					$sql = "SELECT idx, ipaddress FROM tblsecurityiplist ";
					$sql.= "WHERE disabled = 0 ORDER BY ipaddress";
					$result = mysql_query($sql,get_db_conn());

					echo "<select name='oldips[]' size='10' multiple style=\"WIDTH:100%;height:100px\" class=\"textarea\" ".$disabled_form.">";
					echo "<option value='0' selected>Any";
					while($row = mysql_fetch_object($result)) {
						$ipidx = (int)$row->idx;
						$ipaddress = $row->ipaddress;
						echo chr(10).("<option value=".$ipidx.">".$ipaddress."");
					}
					mysql_free_result($result);
					echo "</select>";
				}
?>
					</TD>
				</TR>
				<TR>
					<TD colspan="4" background="images/table_con_line.gif"></TD>
				</TR>
				<tr>
					<TD class="table_cell" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">접근 가능한 새로운 IP</TD>
					<TD class="td_con1" colspan="3"><textarea name=newips rows=3 cols=70 class="textarea" style="width:100%;height:50px" <?=$disabled_form?>></textarea><br><span class="font_orange">* 여러개 등록시 콤마(,)로 구분하여 입력</span></TD>
				</tr>
				<TR>
					<TD colspan="4" background="images/table_con_line.gif"></TD>
				</TR>
<?
			if ($mode=="edit") {
				if(!$superadmin) {
					$roleid = 0;
					$sql = "SELECT roleidx FROM tblsecurityadminrole ";
					$sql.= "WHERE id = '".$up_id."' ";
					$result = mysql_query($sql,get_db_conn());
					$row = mysql_fetch_object($result);
					if($row->roleidx) {
						$roleidx = (int)$row->roleidx;
					}
?>
				<tr>
					<TD class="table_cell" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">권한그룹 선택</TD>
					<TD class="td_con1" " colspan="3">
<?
					$sql = "SELECT idx, description FROM tblsecurityrole ";
					$sql.= "WHERE disabled = 0 ORDER BY description";
					$result = mysql_query($sql,get_db_conn());
					$flag = (boolean)mysql_num_rows($result);
					if ($flag) {
						echo "<select name='roleidx' size='1' class=\"select\">\n";
						echo "<option value=''>권한그룹 선택</option>\n";
						$rno = 0;
						while($row = mysql_fetch_object($result)) {
							$rno++;
							$roleidx1 = (int)$row->idx;
							$description = $row->description;
							if ($roleidx == $roleidx1) {
								echo chr(10).("<option value=".$roleidx1." selected>".$description."");
							} else {
								echo chr(10).("<option value=".$roleidx1.">".$description."");
							}
						}
						echo "</select>\n";
						$submit = true;
					} else {
						echo "생성된 권한그룹이 없어 수정이 불가능합니다.";
						$submit = false;
					}
					mysql_free_result($result);
?>
					&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* 그룹 및 권한등록 메뉴에서 등록</span>
					</TD>
				</tr>
				<TR>
					<TD colspan="4" background="images/table_con_line.gif"></TD>
				</TR>
<?
				} else {
					echo chr(10).("<input type='hidden' name='roleidx' value='0'>");
				}
			} else {
?>
				<tr>
					<TD class="table_cell" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">권한그룹 선택</TD>
					<TD class="td_con1" " colspan="3">
<?
				$sql = "SELECT idx, description FROM tblsecurityrole ";
				$sql.= "WHERE disabled = 0 ORDER BY description";
				$result = mysql_query($sql,get_db_conn());
				$flag = (boolean)mysql_num_rows($result);
				if ($flag) {
					echo "<select name='roleidx' size='1' ".$disabled_form." class=\"select\">\n";
					echo "<option value=''>권한그룹 선택</option>\n";
					$rno = 0;
					while($row = mysql_fetch_object($result)) {
						$rno++;
						$roleidx = (int)$row->idx;
						$description = $row->description;
						echo chr(10).("<option value=".$roleidx.">".$description."");
					}
					echo "</select>\n";
					$submit = true;
				} else {
					echo "<font color=red>권한그룹을 먼저 생성한 후 관리자를 추가하시기 바랍니다.</font>";
					$submit = false;
				}
				mysql_free_result($result);
?>
					&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_orange">* 그룹 및 권한등록 메뉴에서 등록</span>
					</TD>
				</tr>
				<TR>
					<TD colspan="4" background="images/table_con_line.gif"></TD>
				</TR>
				<?
							}
				?>
				<tr>
					<TD class="table_cell" width="138"><img src="images/icon_point2.gif" width="8" height="11" border="0">로그인 허용여부</TD>
					<TD class="td_con1" colspan="3"><input type=radio id="idx_disabled1" name=disabled value="0" <?=$check_0?> <?=$disabled_disabled?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_disabled1>로그인 허용</label> &nbsp;&nbsp; <input type=radio id="idx_disabled2" name=disabled value="1" <?=$check_1?> <?=$disabled_disabled?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_disabled2>로그인 거부</label></TD>
				</tr>
				<TR>
					<TD colspan=4 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
<?
	if ($submit) {
?>
	<tr>
		<td align="center"><a href="javascript:CheckForm('<?=$mode?>');"><img src="<?=$button_value?>" width="113" height="38" border="0"></a></td>
	</tr>
<?
	} else {
?>
	<tr>
		<td align="center"><img src="<?=$button_value?>" width="113" height="38" border="0"></td>
	</tr>
<?
	}
?>
			</form>
			<form name=form2 method=post action="<?=$_SERVER[PHP_SELF]?>">
			<input type=hidden name=mode>
			<input type=hidden name=up_id>
			</form>
			<tr>
				<td height="50"></td>
			</tr>
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

<? INCLUDE ("copyright.php"); ?>