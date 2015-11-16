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

$type=$_POST["type"];
$mode=$_POST["mode"];
$ipidx=$_POST["ipidx"];
$ipaddress=$_POST["ipaddress"];
$description=$_POST["description"];
$disabled=(int)$_POST["disabled"];

if ($mode=="edit") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER[PHP_SELF]);
	#######################################################################

	$sql = "SELECT idx as ipidx , ipaddress, disabled, description ";
	$sql.= "FROM tblsecurityiplist WHERE idx = '".$ipidx."' ";
	$result = mysql_query($sql,get_db_conn());
	if ($row = mysql_fetch_object($result)) {
		$ipaddress=$row->ipaddress;
		$description=$row->description;
		$disabled=$row->disabled;

		${"check_".$disabled} = "checked";
	} else {
		echo "<script>alert('선택하신 IP 정보가 존재하지 않습니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
		exit;
	}
	mysql_free_result($result);
} else if ($mode=="del") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER[PHP_SELF]);
	#######################################################################

	$sql = "SELECT idx as ipidx, ipaddress FROM tblsecurityiplist ";
	$sql.= "WHERE idx = '".$ipidx."' ";
	$result = mysql_query($sql,get_db_conn());
	if ($row = mysql_fetch_object($result)) {
		$ipaddress = $row->ipaddress;
		mysql_free_result($result);
		$sql = "SELECT ipidx FROM tblsecurityadminip WHERE ipidx = '".$ipidx."' ";
		$result = mysql_query($sql,get_db_conn());
		$flag = (boolean)mysql_num_rows($result);
		mysql_free_result($result);
		if ($flag) {
			echo "<script>alert('해당 IP (".$ipaddress.")는 현재 사용중인 IP입니다.\\n\\n운영자/부운영자 설정에서 해당 IP선택을 해지 하신 후 삭제하시기 바랍니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
			exit;
		}
		$sql = "DELETE FROM tblsecurityiplist WHERE idx = '".$ipidx."' ";
		mysql_query($sql,get_db_conn());
		echo "<script>alert('해당 IP (".$ipaddress.")를 삭제하였습니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
		exit;
	} else {
		echo "<script>alert('선택하신 IP 정보가 존재하지 않습니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
		exit;
	}
}

if ($type=="insert") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER[PHP_SELF]);
	#######################################################################

	if (!$ipaddress || !$description) {
		echo "<script>alert('필수 입력 항목 등록이 잘못되었습니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
		exit;
	}
	$sql = "SELECT ipaddress FROM tblsecurityiplist WHERE ipaddress = '".$ipaddress."'";
	$result = mysql_query($sql,get_db_conn());
	$flag = mysql_num_rows($result);
	mysql_free_result($result);
	if ($flag) {
		echo "<script>alert('입력하신 IP 정보가 현재 사용중입니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
		exit;
	}
	$sql = "INSERT INTO tblsecurityiplist (ipaddress,description,disabled) VALUES ";
	$sql.= "('".$ipaddress."','".$description."','".$disabled."')";
	$insert = mysql_query($sql,get_db_conn());

	echo "<script>alert('접근 IP 추가 프로세싱이 정상적으로 처리되었습니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
	exit;
} else if ($type=="edit") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER[PHP_SELF]);
	#######################################################################

	$sql = "SELECT idx FROM tblsecurityiplist WHERE idx = '".$ipidx."' ";
	$result = mysql_query($sql,get_db_conn());
	if (!$row = mysql_fetch_object($result)) {
		echo "<script>alert('선택하신 IP 정보가 존재하지 않습니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
		exit;
	}
	mysql_free_result($result);

	$sql = "SELECT ipaddress FROM tblsecurityiplist WHERE ipaddress = '".$ipaddress."' AND idx != '".$ipidx."'";
	$result = mysql_query($sql,get_db_conn());
	$flag = mysql_num_rows($result);
	mysql_free_result($result);
	if ($flag) {
		echo "<script>alert('입력하신 IP 정보가 현재 사용중입니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
		exit;
	}

	$sql = "UPDATE tblsecurityiplist SET ";
	$sql.= "ipaddress	= '".$ipaddress."', ";
	$sql.= "description	= '".$description."', ";
	$sql.= "disabled	= '".$disabled."' ";
	$sql.= "WHERE idx = '".$ipidx."' ";
	mysql_query($sql,get_db_conn());

	echo "<script>alert('접근 IP 정보 수정이 완료되었습니다.'); location='".$_SERVER[PHP_SELF]."';</script>";
	exit;
}

$mode = $mode ? $mode : "insert";
if ($mode=="edit") {
	$button_value = "images/btn_edit2.gif";
} else if ($mode=="insert") {
	$button_value = "images/btn_badd2.gif";
}
?>

<? INCLUDE ("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(type) {
	if(form1.ipaddress.value.length<=0) {
		alert("IP Address를 입력하세요.");
		form1.ipaddress.focus();
		return;
	}
	if (form1.description.value.length<=0) {
		alert("IP Address에 대한 설명을 입력하세요.");
		form1.description.focus();
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
		alert("사용여부를 선택하세요.");
		form1.disabled[0].focus();
		return;
	}
	form1.type.value=type;
	form1.submit();
}

function check_form(mode,ipidx) {
	if (mode=="del") {
		var con=confirm("해당 IP 정보를 삭제 하시겠습니까?");
		if (!con) {
			return;
		}
	}
	form2.mode.value=mode;
	form2.ipidx.value=ipidx;
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 보안설정 &gt; <span class="2depth_select">접근IP 설정</span></td>
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
					<TD><IMG SRC="images/shop_iplist_title.gif"ALT=""></TD>
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
					<TD width="100%" class="notice_blue"><p>관리페이지에 접근할 수 있는 운영자/부운영자 IP를 관리합니다.</p></TD>
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
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_iplist_stitle1.gif" WIDTH="152" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="6"></td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=5 background="images/table_top_line.gif"></TD>
				</TR>
<?
			$count = 0;
			$sql = "SELECT idx as ipidx , ipaddress, disabled, description ";
			$sql.= "FROM tblsecurityiplist ORDER BY idx DESC ";
			$result = mysql_query($sql,get_db_conn());
			while($row=mysql_fetch_object($result)) {
				$count++;

				echo "<tr>\n";
				echo "	<TD width=\"180\" class=\"td_con2\" style=\"padding-left:5px\"><span class=font_orange>".$count." &nbsp;-&nbsp; <B>".$row->ipaddress."</B></span></td>\n";
				if ($row->disabled == 0) {
					echo "	<TD class=\"td_con1\" width=\"62\" align=\"center\">사용함</td>\n";
				} else {
					echo "	<TD class=\"td_con1\" width=\"62\" align=\"center\">사용안함</td>\n";
				}

				$allowedadmins = "";
				$sql = "SELECT a.id FROM tblsecurityadmin a, tblsecurityadminip p ";
				$sql.= "WHERE a.id = p.id AND p.ipidx = ".$row->ipidx."";
				$result2 = mysql_query($sql,get_db_conn());
				while($row2 = mysql_fetch_object($result2)) {
					$aname = $row2->id;
					if($aname)
						$allowedadmins .= "" . $aname . ", ";
				}
				mysql_free_result($result2);

				if ($allowedadmins) {
					$allowedadmins = substr($allowedadmins,0,(strlen($allowedadmins)-2));
					echo "	<TD class=\"td_con1\" width=\"410\" align=\"center\">".$allowedadmins."</td>\n";
				} else {
					echo "	<TD class=\"td_con1\" width=\"410\" align=\"center\">해당 아이피를 적용중인 운영자/부운영자가 없습니다.</td>\n";
				}
				echo "	<TD class=\"td_con1\" width=\"54\" align=\"center\"><a href=\"javascript:check_form('edit','".$row->ipidx."');\"><img src=\"images/btn_edit.gif\" width=\"50\" height=\"22\" border=\"0\"></a></td>\n";
				echo "	<TD class=\"td_con1\" width=\"56\" align=\"center\"><a href=\"javascript:check_form('del','".$row->ipidx."');\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></a></td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<TD colspan=\"5\" background=\"images/table_con_line.gif\"></TD>\n";
				echo "</tr>\n";
			}
			if ($count == 0) {
				echo "<tr>\n";
				echo "	<TD class=\"td_con1\" align=\"center\" colspan=\"5\">등록된 접근 IP 정보가 없습니다.</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<TD colspan=\"5\" background=\"images/table_con_line.gif\"></TD>\n";
				echo "</tr>\n";
			}
?>
				<TR>
					<TD colspan=5 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="30"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_iplist_stitle2.gif" WIDTH="152" HEIGHT=31 ALT=""></TD>
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
			<input type=hidden name=ipidx value="<?=$ipidx?>">
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
					<TD width="100%" class="notice_blue">1) 접근 IP 추가 후 <span class=font_orange><B>운영자/부운영자 설정</B></span>에서 운영자별 접근 IP 선택을 하실 수 있습니다.<br>2) 유동IP의 경우 잦은 IP 변경으로 관리페이지에 접근이 차단될 수 있사오니 이점 유의하시기 바랍니다.</TD>
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
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">IP Address</TD>
					<TD class="td_con1"><input type=text name=ipaddress value="<?=$ipaddress?>" size=25 class="input"> <span class=font_orange>예)211.235.123.120</span></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">설명</TD>
					<TD class="td_con1" ><textarea cols=60 rows=2 name=description style="width:100%;height=85px;" class="textarea"><?=$description?></textarea></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">사용여부</TD>
					<TD class="td_con1"><input type=radio id="idx_disabled1" name=disabled value="0" <?=$check_0?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_disabled1>사용함</label> &nbsp;&nbsp;<input type=radio id="idx_disabled2" name=disabled value="1" <?=$check_1?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_disabled2>사용하지 않음</label></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('<?=$mode?>');"><img src="<?=$button_value?>" width="113" height="38" border="0"></a></td>
			</tr>
			</form>
			<form name=form2 method=post action="<?=$_SERVER[PHP_SELF]?>">
			<input type=hidden name=mode>
			<input type=hidden name=ipidx>
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