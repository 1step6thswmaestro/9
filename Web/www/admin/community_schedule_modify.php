<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$CurrentTime = time();

if ($mode == "modify") {
	$subject = addslashes($subject);
	$comment = addslashes($comment);
	$duedate = $year.$month.$day;
	$duetime = $time;

	$sql = "UPDATE tblschedule SET ";
	$sql.= "import = '$import', ";
	$sql.= "rest = '$rest', ";
	$sql.= "subject = '$subject', ";
	$sql.= "comment = '$comment', ";
	$sql.= "duedate = '$duedate', ";
	$sql.= "duetime = '$duetime' ";
	$sql.= "WHERE idx = '".$sid."' ";
	$update = mysql_query($sql,get_db_conn());

	if ($update) {
		echo "<script>alert('일정 수정이 완료 되었습니다.');opener.location.reload();top.close();</script>";
		exit;
	} else {
		echo "<script>alert('일정 수정중 오류가 발생하였습니다.');history.go(-1);</script>";
		exit;
	}
}


$sql = "SELECT * FROM tblschedule WHERE idx = '".$sid."' ";
$result = mysql_query($sql,get_db_conn());
$rows = mysql_affected_rows();
if ($rows <= 0) {
	echo "<script>alert('수정할 일정이 없습니다.');top.close();</script>";
	exit;
}

$row = mysql_fetch_array($result);
$subject = stripslashes($row[subject]);
$comment = stripslashes($row[comment]);
$year = substr($row[duedate],0,4);
$month = substr($row[duedate],4,2);
$day = substr($row[duedate],6,2);
$time = $row[duetime];
${"select_".$time} = "selected";
${"import_".$row[import]} = "selected";
${"rest_".$row[rest]} = "selected";

$inputY = $year;
$inputM = $month;

$totaldays = get_totaldays($inputY,$inputM);

if ($totaldays <= 0) {
	echo "<script>alert('날짜 선택이 잘못되었습니다.');top.close();</script>";
	exit;
}

function get_totaldays($year,$month) {
	$date = 1;
	while(checkdate($month,$date,$year)) {
		$date++;
	}

	$date--;

	return $date;
}

?>

<html>
<head>
<title>스케쥴러</title>
<meta http-equiv='content-type' content='text/html; charset=euc-kr'>
<LINK rel="stylesheet" type="text/css" href="style.css">
<SCRIPT LANGUAGE="JavaScript">
<!--
function PageResize() {
	var oWidth = 410;
	var oHeight = 310;

	window.resizeTo(oWidth,oHeight);
}

function form_submit(thisform) {

	if (thisform.subject.value=='') {
		alert('제목을 입력하세요');
		thisform.subject.focus();
		return false;
	}

	if (thisform.comment.value=='') {
		alert('내용을 입력하세요');
		thisform.comment.focus();
		return false;
	}

	return true;

}
//-->
</SCRIPT>
</head >

<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" bgcolor="#F6F6F6" onLoad="PageResize();">
<form action='<?=$_SERVER[PHP_SELF]?>' method='post' onSubmit="return form_submit(this)">
<input type='hidden' name='mode' value='modify'>
<input type='hidden' name='sid' value="<?=$sid?>">

<TABLE WIDTH="400" BORDER=0 CELLPADDING=0 CELLSPACING=0>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><img src="images/calender_edit_title.gif" border="0" width="212" height="31"></td>
		<td width="100%" background="images/member_mailallsend_imgbg.gif">&nbsp;</td>
		<td align=right><img src="images/member_mailallsend_img2.gif" width="20" height="31" border="0"></td>
	</tr>
	</table>
	</TD>
</TR>
<tr>
<TD style="padding:10pt;">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TR>
		<TD class=lineleft style="padding-right:15px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" noWrap align=right width="63" bgColor=#f0f0f0 height="32">날짜</TD>
		<TD class=line style="padding-left:5px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" width="290" height="32">
		<SELECT name=year size="1" class="select">
<?
		for($y=2000;$y<=date("Y")+5;$y++) {
			unset($select);
			if ($y == $year) $select = "selected";
			echo "<option value='".$y."' ".$select.">".$y." 년</option>";
		}
?>
		</SELECT>
		<SELECT name=month class="select">
<?
		for($y=1;$y<=12;$y++) {
			unset($select);
			unset($yn);
			$yn = $y;
			if ($y<10) $yn = "0".$y;
			if ($yn == $month) $select = "selected";
			echo "<option value='".$yn."' ".$select.">".$yn." 월</option>";
		}
?>
		</SELECT>
		<SELECT name=day class="select">
<?
		for($y=1;$y<=$totaldays;$y++) {
			unset($select);
			unset($yn);
			$yn = $y;
			if ($y<10) $yn = "0".$y;
			if ($yn == $day) $select = "selected";
			echo "<option value='".$yn."' ".$select.">".$yn." 일</option>";
		}
?>
		</SELECT>
		<SELECT name=time class="select">
		<option value='25' <?=$select_25?>>시간미지정</option>
		<option value='6' <?=$select_6?>>6 시 AM</option>
		<option value='7' <?=$select_7?>>7 시 AM</option>
		<option value='8' <?=$select_8?>>8 시 AM</option>
		<option value='9' <?=$select_9?>>9 시 AM</option>
		<option value='10' <?=$select_10?>>10 시 AM</option>
		<option value='11' <?=$select_11?>>11 시 AM</option>
		<option value='12' <?=$select_12?>>12 시 AM</option>
		<option value='13' <?=$select_13?>>1 시 PM</option>
		<option value='14' <?=$select_14?>>2 시 PM</option>
		<option value='15' <?=$select_15?>>3 시 PM</option>
		<option value='16' <?=$select_16?>>4 시 PM</option>
		<option value='17' <?=$select_17?>>5 시 PM</option>
		<option value='18' <?=$select_18?>>6 시 PM</option>
		<option value='19' <?=$select_19?>>7 시 PM</option>
		<option value='20' <?=$select_20?>>8 시 PM</option>
		<option value='21' <?=$select_21?>>9 시 PM</option>
		<option value='22' <?=$select_22?>>10 시 PM</option>
		</SELECT>			 
		</TD>
	</TR>
	<TR>
		<TD class=lineleft style="padding-right:15px; border-top-width:1pt; border-top-color:rgb(222,222,222); border-top-style:solid;" noWrap align=right width="63" bgColor=#f0f0f0 height="32">제목</TD>
		<TD class=line style="padding-left:5px; border-top-width:1pt; border-top-color:rgb(222,222,222); border-top-style:solid;" width="290" height="32"><INPUT class="input" maxLength=12 size=15 name=subject value="<?=$subject?>" style=width:100%></TD>
	</TR>
	<TR>
		<TD class=lineleft style="padding-right:15px; border-top-width:1pt; border-top-color:rgb(222,222,222); border-top-style:solid;" noWrap align=right width="63" bgColor=#f0f0f0 height="32">내용</TD>
		<TD class=line style="padding-top:2pt; padding-bottom:2pt; padding-left:5px; border-top-width:1pt; border-top-color:rgb(222,222,222); border-top-style:solid;" height="32"><textarea rows="3" class="textarea" style=width:100% name="comment"><?=$comment?></textarea></TD>
	</TR>
	<TR>
		<TD class=lineleft style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=center width="358" bgColor=#f0f0f0 height="32" colspan="2">
		<SELECT name=import class="select">
		<option value='N' <?=$import_N?>>일반일정</option>
		<option value='Y' <?=$import_Y?>>중요일정</option>
		</SELECT>			 
		<SELECT name=rest class="select">
		<option value='N' <?=$rest_N?>>비공휴일</option>
		<option value='Y' <?=$rest_Y?>>공휴일지정</option>
		</SELECT>
		</TD  style="border-bottom-width:1pt; border-bottom-color:silver; border-bottom-style:solid;">
	</TR >
	</TABLE>					
	</TD>
	</tr>
	<TR>
		<TD align=center><input type="image" src="images/btn_ok1.gif" width="36" height="18" border="0" vspace="0" border=0><a href="javascript:window.close()"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="0" border=0 hspace="2"></a></TD>
	</TR>
</TABLE>

</form>
</body>
</html>