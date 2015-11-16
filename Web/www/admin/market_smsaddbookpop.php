<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$type=$_POST["type"];
$mobile=$_POST["mobile"];

$mode=$_POST["mode"];
$up_name=$_POST["up_name"];
$up_mobile1=$_POST["up_mobile1"];
$up_mobile2=$_POST["up_mobile2"];
$up_mobile3=$_POST["up_mobile3"];
$up_group=$_POST["up_group"];
$up_new_group=$_POST["up_new_group"];
$up_memo=$_POST["up_memo"];

$_sms="";
if($type=="update" && strlen($mobile)>0) {
	$sql = "SELECT * FROM tblsmsaddress WHERE mobile='".$mobile."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$_sms=$row;
	} else {
		echo "<script>window.close();</script>";
		exit;
	}
	mysql_free_result($result);
} else if($mode=="update") {
	$up_mobile=$up_mobile1."-".$up_mobile2."-".$up_mobile3;
	if(strlen($up_new_group)>0) $up_group=$up_new_group;

	$sql = "UPDATE tblsmsaddress SET ";
	$sql.= "name		= '".$up_name."', ";
	$sql.= "mobile		= '".$up_mobile."', ";
	$sql.= "addr_group	= '".$up_group."', ";
	$sql.= "memo		= '".$up_memo."' ";
	$sql.= "WHERE mobile='".$mobile."' ";
	mysql_query($sql,get_db_conn());
	echo "</head><body onload=\"alert('수정되었습니다.');opener.location.reload();window.close();\"></body></html>";exit;

} else if($mode=="insert") {
	$up_mobile=$up_mobile1."-".$up_mobile2."-".$up_mobile3;
	if(strlen($up_new_group)>0) $up_group=$up_new_group;

	$sql = "INSERT tblsmsaddress SET ";
	$sql.= "name		= '".$up_name."', ";
	$sql.= "mobile		= '".$up_mobile."', ";
	$sql.= "addr_group	= '".$up_group."', ";
	$sql.= "memo		= '".$up_memo."', ";
	$sql.= "date		= '".date("YmdHis")."' ";
	mysql_query($sql,get_db_conn());
	echo "</head><body onload=\"alert('등록되었습니다.');opener.location.reload();window.close();\"></body></html>";exit;
} else {
	$type="insert";
}

$arrmobile=explode("-",$_sms->mobile);
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>SMS 주소 등록/수정</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 80;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm() {
	if(document.form1.up_name.value.length==0) {
		alert("이름을 입력하세요.");
		document.form1.up_name.focus();
		return;
	}
	if(document.form1.up_mobile1.value.length==0) {
		alert("휴대폰 번호를 입력하세요.");
		document.form1.up_mobile1.focus();
		return;
	}
	if(!IsNumeric(document.form1.up_mobile1.value)) {
		alert("휴대폰 번호는 숫자만 입력하세요.");
		document.form1.up_mobile1.focus();
		return;
	}
	if(document.form1.up_mobile2.value.length==0) {
		alert("휴대폰 번호를 입력하세요.");
		document.form1.up_mobile2.focus();
		return;
	}
	if(!IsNumeric(document.form1.up_mobile2.value)) {
		alert("휴대폰 번호는 숫자만 입력하세요.");
		document.form1.up_mobile2.focus();
		return;
	}
	if(document.form1.up_mobile3.value.length==0) {
		alert("휴대폰 번호를 입력하세요.");
		document.form1.up_mobile3.focus();
		return;
	}
	if(!IsNumeric(document.form1.up_mobile3.value)) {
		alert("휴대폰 번호는 숫자만 입력하세요.");
		document.form1.up_mobile3.focus();
		return;
	}
	if(document.form1.up_group.value.length==0 && document.form1.up_new_group.value.length==0) {
		alert("그룹을 선택하시거나 신규그룹을 입력하세요.");
		document.form1.up_group.focus();
		return;
	}
	document.form1.submit();
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" oncontextmenu="return false" onLoad="PageResize();">

<TABLE WIDTH="400" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode value="<?=$type?>">
<input type=hidden name=mobile value="<?=$mobile?>">
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><img src="images/market_smsaddressbk_title.gif" border="0" width="212" height="31"></td>
		<td width="100%" background="images/member_mailallsend_imgbg.gif"></td>
		<td align="right"><img src="images/member_mailallsend_img2.gif" width="20" height="31" border="0"></td>
	</tr>
	</table>
	</TD>
</TR>
<tr>
	<TD style="padding:10pt;">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TR>
		<TD background="images/table_top_line.gif" colspan="2"></TD>
	</TR>
	<TR>
		<TD class="table_cell" width="79"><img src="images/icon_point2.gif" width="8" height="11" border="0">이 름</TD>
		<TD class="td_con1" width="266"><INPUT maxLength=20 value="<?=$_sms->name?>" name=up_name class="input"></TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<TR>
		<TD class="table_cell" width="79"><img src="images/icon_point2.gif" width="8" height="11" border="0">휴대폰 번호</TD>
		<TD class="td_con1" width="266">
		<INPUT onkeyup=strnumkeyup(this) maxLength=3 size=4 value="<?=$arrmobile[0]?>" name=up_mobile1 class="input"> - 
		<INPUT onkeyup=strnumkeyup(this) maxLength=4 size=4 value="<?=$arrmobile[1]?>" name=up_mobile2 class="input"> - 
		<INPUT onkeyup=strnumkeyup(this) maxLength=4 size=4 value="<?=$arrmobile[2]?>" name=up_mobile3 class="input">
		</TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<TR>
		<TD class="table_cell" width="79"><img src="images/icon_point2.gif" width="8" height="11" border="0">그룹선택</TD>
		<TD class="td_con1" width="266">
		기존그룹: 
		<SELECT name=up_group class="input">
		<OPTION value="">그룹을 선택하세요.</OPTION>
<?
		$sql = "SELECT addr_group FROM tblsmsaddress GROUP BY addr_group ";
		$result=mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)) {
			echo "<option value=\"".$row->addr_group."\"";
			if($_sms->addr_group==$row->addr_group) echo " selected";
			echo ">".$row->addr_group."</option>\n";
		}
		mysql_free_result($result);
?>
		</SELECT>
		<br>신규생성: <INPUT maxLength=20 name=up_new_group class="input">
		</TD>
	</TR>
	<TR>
		<TD colspan="2" width="373" background="images/table_con_line.gif"></TD>
	</TR>
	<TR>
		<TD class="table_cell" width="79"><img src="images/icon_point2.gif" width="8" height="11" border="0">기타메모</TD>
		<TD class="td_con1" width="266"><TEXTAREA style="WIDTH: 100%; HEIGHT: 72px" name=up_memo maxlength="100" class="textarea"><?=$_sms->memo?></TEXTAREA></TD>
	</TR>
	<TR>
		<TD colspan="2" background="images/table_con_line.gif"></TD>
	</TR>
	<TR>
		<TD background="images/table_top_line.gif" colspan="2"></TD>
	</TR>
	</TABLE>
	</TD>
</tr>
<TR>
	<TD align="center"><a href="javascript:CheckForm()"><img src="images/btn_ok1.gif" width="36" height="18" border="0" vspace="0" border=0></a><a href="javascript:window.close();"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="0" border=0 hspace="2"></a></TD>
</TR>
</form>
</TABLE>
<?=$onload?>
</body>
</html>