<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$type=$_POST["type"];
$delicon=$_POST["delicon"];
$file0=$_FILES["file0"];
$file1=$_FILES["file1"];
$file2=$_FILES["file2"];
$file3=$_FILES["file3"];
$file4=$_FILES["file4"];
$file5=$_FILES["file5"];

$imagepath=$Dir.DataDir."shopimages/etc/";
if($type=="insert"){
	$filename = array (&$file0[name],&$file1[name],&$file2[name],&$file3[name],&$file4[name],&$file5[name]);
	$file = array (&$file0[tmp_name],&$file1[tmp_name],&$file2[tmp_name],&$file3[tmp_name],&$file4[tmp_name],&$file5[tmp_name]);
	$image = array("iconU1.gif","iconU2.gif","iconU3.gif","iconU4.gif","iconU5.gif","iconU6.gif");
	$cnt = count($image);

	for($i=0;$i<$cnt;$i++){
		if(strlen($filename[$i])>0){
			$ext = strtolower(substr($filename[$i],-3));
			if ($ext!="gif" && $ext!="jpg") {
				echo "<script>alert('이미지타입을 GIF나 JPG로 작성하여 주세요.');history.go(-1);</script>";
				exit;
			}
			if (file_exists($file[$i])==false) {
				echo "<script>alert('파일크기는 20Kb이하로 올려주세요.');history.go(-1);</script>";
				exit;
			}
			if (filesize($file[$i])>10000) {
				echo "<script>alert('파일크기는 10Kb이하로 올려주세요.');history.go(-1);</script>";
				exit;
			}
		}
	}

	for($i=0;$i<$cnt;$i++){
		if (strlen($filename[$i])>0 && file_exists($file[$i])) {
			$ext = strtolower(substr($filename[$i],strlen($filename[$i])-3,3));
			if($ext=="gif" || $ext=="jpg"){
				move_uploaded_file($file[$i],$imagepath.$image[$i]);
				chmod($imagepath.$image[$i],0666);
			}
		}
	}
	$onload="해당 파일을 등록하였습니다.";
}
if($type=="delete"){
	if (file_exists($imagepath.$delicon)) {
		unlink($imagepath.$delicon);
		$onload="해당 파일을 삭제하였습니다.";
	}
}
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>내 아이콘 등록/수정</title>
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
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 75;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm(){
	var ok=0;
	if(document.form1.file0.value.length!=0) ok++;
	if(document.form1.file1.value.length!=0) ok++;
	if(document.form1.file2.value.length!=0) ok++;
	if(document.form1.file3.value.length!=0) ok++;
	if(document.form1.file4.value.length!=0) ok++;
	if(document.form1.file5.value.length!=0) ok++;
	if(ok==0){
		alert('등록을 원하시는 파일을 선택하세요');
		document.form1.file0.focus();
		return;
	} 
	if(confirm("해당 파일을 등록하시겠습니까?")) document.form1.submit();
}

function DeleteIcon(icn){
	if(confirm("해당 파일을 삭제하시겠습니까?\n\n삭제하시면 이미 등록하셨던 다른 상품에 아이콘이\n안보일수 있으니 참고하시기 바랍니다.")){
		document.form1.delicon.value="icon"+icn+".gif";
		document.form1.type.value="delete";
		document.form1.submit();
	}
}

//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="450" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><img src="images/myicon_upload_title.gif" border="0" width="212" height="31"></td>
		<td width="100%" background="images/member_find_titlebg.gif">&nbsp;</td>
		<td align=right><img src="images/member_find_titleimg.gif" width="20" height="31" border="0"></td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD background="images/member_zipsearch_bg.gif">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="18">&nbsp;</td>
		<td>&nbsp;</td>
		<td width="18">&nbsp;</td>
	</tr>
	<tr>
		<td width="18">&nbsp;</td>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
		<input type=hidden name=type value="insert">
		<input type=hidden name=delicon>
		<tr>
			<td>
			<table cellpadding="5" cellspacing="0" width="100%" bgcolor="#F3F3F3">
			<tr align=center>
				<td bgcolor="#F3F3F3" align="center" style="border-top-width:1pt; border-top-color:silver; border-top-style:solid;" width="23"><b>NO</b></td>
				<td bgcolor="#F3F3F3" align="center" style="border-top-width:1pt; border-top-color:silver; border-top-style:solid;" width="47"><b>아이콘</b></td>
				<td bgcolor="#F3F3F3" align="center" style="border-top-width:1pt; border-top-color:silver; border-top-style:solid;" width="36"><b>삭제</b></td>
				<td bgcolor="#F3F3F3" align="center" style="border-top-width:1pt; border-top-color:silver; border-top-style:solid;" width="266"><b>등록 및 수정</b></td>
			</tr>
<?
			$usericon = array("U1","U2","U3","U4","U5","U6");
			$num = count($usericon); 
			for($i=0;$i<$num;$i++){
				if(file_exists($imagepath."icon".$usericon[$i].".gif")) $ok="Y";
				else $ok="N";
				echo "<tr>\n";
				echo "	<td bgcolor=\"white\" align=\"center\" width=\"23\">".($i+1)."</td>\n";
				if ($ok=="Y") {
					echo "	<td bgcolor=\"white\" align=\"center\" width=\"47\"><img src=\"".$imagepath."icon".$usericon[$i].".gif\" align=absmiddle border=0></td>\n";
					echo "	<td bgcolor=\"white\" align=\"center\" width=\"36\"><a href=\"javascript:DeleteIcon('".$usericon[$i]."');\"><img src=\"images/myicon_upload_del.gif\" width=\"35\" height=\"14\" border=\"0\"></a></td>\n";
				} else {
					echo "	<td bgcolor=\"white\" align=\"center\" width=\"47\">--</td>\n";
					echo "	<td bgcolor=\"white\" align=\"center\" width=\"36\">--</td>\n";
				}
				echo "	<td bgcolor=\"white\" width=\"266\"><input type=file name=file".$i." style=\"width:100%\" class=\"input\"></td>\n";
				echo "<tr>\n";
				echo "	<td bgcolor=\"white\" align=\"center\" colspan=\"4\" background=\"images/table_con_line.gif\" width=\"402\"></td>\n";
				echo "</tr>\n";
			}
?>
			</table>
			</td>
		</tr>
		<tr>
			<td height=10></td>
		</tr>
		<tr>
			<td class="font_size"><p style="line-height:140%;">* 아이콘은 <b>&quot;gif&quot;</b> 또는<b>&quot;jpg&quot;</b>파일만 등록 가능합니다.<br>* 아이콘 파일용량은 <b>10KB이하</b>로 등록하시기 바랍니다.</td>
		</tr>
		</table>
		</td>
		<td width="18">&nbsp;</td>
	</tr>
	<tr>
		<td width="18">&nbsp;</td>
		<td align="center"><a href="javascript:CheckForm();"><img src="images/myicon_upload_btn.gif" width="85" height="24" border="0" vspace="5" border=0></a>&nbsp;&nbsp;<a href="javascript:window.close();"><img src="images/btn_closea.gif" width="69" height="24" border="0" vspace="5" border=0></a></td>
		<td width="18">&nbsp;</td>
	</tr>
	</form>
	</table>
	</TD>
</TR>
</TABLE>
<? if(strlen($onload)>0) echo "<script>alert('$onload');opener.location.reload();</script>"; ?>
</body>
</html>