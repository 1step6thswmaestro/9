<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-2";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$modechk=$_POST["modechk"];
$up_bill_state=$_POST["up_bill_state"];
$up_domain=$_POST["up_domain"];
$up_license_id=$_POST["up_license_id"];
$up_license_no=$_POST["up_license_no"];

if($type=="up") {
	$sql =($modechk =="N") ? "INSERT":"UPDATE";
	$sql.=" tblshopbillinfo SET ";
	$sql.="domain='".$up_domain."', ";
	$sql.="license_id='".$up_license_id."', ";
	$sql.="license_no='".$up_license_no."', ";
	$sql.="bill_state='".$up_bill_state."' ";
	$sql.=",sc_name='".$_POST['sc_name']."' ";
	$sql.=",sc_email='".$_POST['sc_email']."' ";
	$sql.=",sc_cell='".$_POST['sc_cell']."' ";
	$sql.=",sc_phone='".$_POST['sc_phone']."' ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert('전자세금계산서 기능 설정이 완료되었습니다.');</script>";
}

$modechk ="N";
$sql = "SELECT * FROM tblshopbillinfo ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$domain = $row->domain;
	$license_id = $row->license_id;
	$license_no = $row->license_no;
	$bill_state = $row->bill_state;
	$sc_name = $row->sc_name;
	$sc_email = $row->sc_email;
	$sc_cell = $row->sc_cell;
	$sc_phone = $row->sc_phone;
	$modechk = "Y";
}
mysql_free_result($result);

if($bill_state =="Y"){
	$bill_stateY = "checked";
	$bill_stateN = "";
}else{
	$bill_stateY = "";
	$bill_stateN = "checked";
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
<!--
function CheckForm() {
	if(document.form1.up_bill_state.value == "Y"){
		if (document.form1.up_domain.value.length==0) {
			alert("하이웍스 개설 오피스주소를 입력하세요.");
			return;
		}
		if (document.form1.up_domain.value.length==0) {
			alert("사용자 ID를 입력하세요.");
			return;
		}
		if (document.form1.up_domain.value.length==0) {
			alert("연동 KEY를 입력하세요.");
			return;
		}
		
		if (document.form1.sc_name.value.length==0) {
			alert("담당자 성명을 입력하세요.");
			return;
		}
	}
	form1.type.value="up";
	form1.submit();
}

function bill_change(form) {
	if(form.up_bill_state[0].checked) {
		form.up_domain.disabled=false;
		form.up_license_id.disabled=false;
		form.up_license_no.disabled=false;
	} else {
		form.up_domain.disabled=true;
		form.up_license_id.disabled=true;
		form.up_license_no.disabled=true;
	}
}
//-->
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
			<? include ("menu_order.php"); ?>
			</td>

			<td></td>
			<td valign="top">

<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt; <span class="2depth_select">전자세금계산서 설정</span></td>
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
					<TD><IMG SRC="images/shop_bill_title.gif" border="0"></TD>
				</TR><TR>
					<TD width="100%" background="images/title_bg.gif">&nbsp;</TD>
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
					<TD width="100%" class="notice_blue"><p>전자세금계산서 발행기능을 설정하실 수 있습니다.</p></TD>
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
			<input type=hidden name="modechk" value="<?=$modechk?>">
			<!-- <tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_bill_title.gif" border="0"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr> -->
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">전자세금계산서 사용</TD>
					<TD class="td_con1"><input type=radio id="bill_stateY" name=up_bill_state value="Y" <?=$bill_stateY?> onclick="bill_change(this.form);"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=bill_stateY>전자세금계산서 사용</label>&nbsp;&nbsp;&nbsp;<input type=radio id="bill_stateN" name=up_bill_state value="N" <?=$bill_stateN?> onclick="bill_change(this.form);"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=bill_stateN>전자세금계산서 미사용</label>
					</td>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">하이웍스 개설 오피스주소</TD>
					<TD class="td_con1"><input type="text" name="up_domain" value="<?=$domain?>" style="width:150px;" class="input_selected">
					</td>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">사용자 ID</TD>
					<TD class="td_con1"><input type="text" name="up_license_id" value="<?=$license_id?>" style="width:150px;" class="input_selected">
					</td>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">연동 KEY</TD>
					<TD class="td_con1"><input type="text" name="up_license_no" value="<?=$license_no?>" style="width:300px;" class="input_selected">
					</td>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">담당자명</TD>
					<TD class="td_con1"><input type="text" name="sc_name" value="<?=$sc_name?>" style="width:150px;" class="input">
					</td>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">담당자 e-mail</TD>
					<TD class="td_con1"><input type="text" name="sc_email" value="<?=$sc_email?>" style="width:300px;" class="input">
					</td>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">담당자 휴대폰</TD>
					<TD class="td_con1"><input type="text" name="sc_cell" value="<?=$sc_cell?>" style="width:300px;" class="input">
					</td>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="180"><img src="images/icon_point2.gif" width="8" height="11" border="0">담당자 일반전화</TD>
					<TD class="td_con1"><input type="text" name="sc_phone" value="<?=$sc_phone?>" style="width:300px;" class="input">
					</td>
				</tr>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">전자세금계산서 설정 안내</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">전자세금계산서의 경우 가비아 하이웍스로 연동됩니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">연동처리 후 자동등록되며, 실제 발행처리는 가비아 하이웍스에서 발행하셔야 합니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">연동정보의 경우 http://www.hiworks.co.kr 에서 확인 가능합니다.(하이웍스 개설 오피스주소,사용자 ID,연동 KEY) </td>
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
<script>bill_change(document.form1);</script>
<?=$onload?>

<? INCLUDE "copyright.php"; ?>