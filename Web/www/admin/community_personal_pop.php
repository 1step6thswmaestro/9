<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$idx=$_POST["idx"];
$mode=$_POST["mode"];
$re_content=$_POST["re_content"];

$sql = "SELECT * FROM tblpersonal WHERE idx='".$idx."' ";
$result=mysql_query($sql,get_db_conn());
$data=mysql_fetch_object($result);
mysql_free_result($result);
if(!$data) {
	echo "<script>alert(\"해당 게시물이 존재하지 않습니다.\");window.close();</script>";
	exit;
}
if(strlen($data->email)==0) $data->email="메일 입력이 안되었습니다.";
if(strlen($data->re_date)==14) $data->reply="<img src=\"images/icon_finish.gif\" width=\"74\" height=\"25\" border=\"0\">";
else $data->reply="<img src=\"images/icon_nofinish.gif\" width=\"74\" height=\"25\" border=\"0\">";

if($mode=="update" && strlen($re_content)>0) {
	$sql = "UPDATE tblpersonal SET ";
	$sql.= "re_date		= '".date("YmdHis")."', ";
	$sql.= "re_content	= '".$re_content."' ";
	$sql.= "WHERE idx='".$idx."' ";
	mysql_query($sql,get_db_conn());

	if(strlen($data->email)>0) {
		$info_email=$_shopdata->info_email;
		$shopname=$_shopdata->shopname;

		$content = "##### ".$data->name."님 ".$shopname." 1:1 고객 문의에 대한 답변입니다. #####";
		$content.= "<br>\n\n";
		$content.= "보낸이 - ".$shopname." (<a href=\"mailto:".$info_email."\">".$info_email."</a>)<br>\n";
		$content.= "#####################################################################\n";
		$content.= "<pre>".$re_content."</pre>\n";
		$header="From: ".$shopname." <".$info_email.">\r\nContent-Type: text/html; charset=euc-kr\r\n";

		sendmail($data->email, $shopname." 1:1 고객 문의에 대한 답변입니다.", $content, $header);
	}
	echo "<script>alert(\"해당 게시글에 대한 답변이 완료되었습니다.\");opener.location.reload();window.close();</script>";
	exit;
} else if ($mode=="delete") {
	$sql = "DELETE FROM tblpersonal WHERE idx='".$idx."' ";
	mysql_query($sql,get_db_conn());
	echo "<script>alert(\"해당 게시글을 삭제하였습니다.\");opener.location.reload();window.close();</script>";
	exit;
}
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>1:1 고객 게시판</title>
<link rel="stylesheet" href="style.css" type="text/css">
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
	var oWidth = 600;
	var oHeight = 550;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm(form) {
	if(form.re_content.length==0) {
		alert("답변 내용을 입력하세요.");
		form.re_content.focus();
		return;
	}
	form.mode.value="update";
	form.submit();
}

function CheckDelete() {
	if(confirm("해당 게시글을 삭제하시겠습니까?")) {
		document.form1.mode.value="delete";
		document.form1.submit();
	}
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow-x:hidden;" onLoad="PageResize();">

<TABLE WIDTH="328" BORDER=0 CELLPADDING=0 CELLSPACING=0>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><IMG SRC="images/com_personal_pop_t.gif" WIDTH="212" HEIGHT="31" ALT=""></td>
		<td width="100%" background="images/member_mailallsend_imgbg.gif">&nbsp;</td>
		<td align=right><img src="images/member_mailallsend_img2.gif" width="20" height="31" border="0"></td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD style="padding:6pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
	<input type=hidden name=mode>
	<input type=hidden name=idx value="<?=$idx?>">
	<tr>
		<td width="100%">
		<TABLE cellSpacing=0 cellPadding=0 width="584" border=0>
		<col width=130></col>
		<col width=></col>
		<TR>
			<TD colspan=2 background="images/table_top_line.gif"></TD>
		</TR>
		<TR>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">회원명</TD>
			<TD class="td_con1"><B><span class="font_blue"><?=$data->name?></B>(<?=$data->id?>)</span></TD>
		</TR>
		<TR>
			<TD colspan="2" width="584" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
		</TR>
		<TR>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">제목</TD>
			<TD class="td_con1"><?=$data->subject?></TD>
		</TR>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<TR>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">메일</TD>
			<TD class="td_con1"><a href="mailto:<?=$data->email?>"><?=$data->email?></a></TD>
		</TR>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">답변여부</TD>
			<TD class="td_con1"><?=$data->reply?></TD>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">질문</TD>
			<TD class="td_con1"><?=nl2br($data->content)?></TD>
		</tr>
		<TR>
			<TD colspan="2" background="images/table_con_line.gif"></TD>
		</TR>
		<tr>
			<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">답변</TD>
			<TD class="td_con1"><TEXTAREA style="width:400;height:205" name=re_content class="textarea"><?=$data->re_content?></TEXTAREA></TD>
		</tr>
		<TR>
			<TD colspan=2 background="images/table_top_line.gif"></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td width="100%" align="center">
		<a href="javascript:CheckForm(document.form1);"><img src="images/btn_write1.gif" width="69" height="24" border="0" vspace="10" border=0></a>
		<a href="javascript:CheckDelete();"><img src="images/btn_dela.gif" width="69" height="24" border="0" vspace="10" border=0 hspace="2"></a>
		<a href="javascript:window.close()"><img src="images/btn_closea.gif" width="69" height="24" border="0" vspace="10" border=0 hspace="0"></a>
		</td>
	</tr>
	</form>
	</table>
	</TD>
</TR>
</TABLE>
</body>
</html>