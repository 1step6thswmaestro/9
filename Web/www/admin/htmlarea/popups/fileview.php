<?
$Dir="../../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$rootlength=strlen($Dir)-1;

$type=$_POST["type"];
$dir=$_POST["dir"];

$path="";

if($_GET['vender']) $vender = $_GET['vender'];
if(strlen($vender) > 0 ) {
	$originalpath = $Dir.DataDir."vender/".$vender."/";
	if (!is_dir($originalpath)) {
		mkdir($originalpath, 0707, true);
		chmod($originalpath, 0707);
	}

}else{
	$originalpath = $Dir.DataDir."design/";
}

$originallength=strlen($originalpath);

if(strlen($dir)==0)
	$path=$originalpath;
else {
	$dir=ereg_replace("\.\.","",$dir); // 상위디렉토리로 이동 금지
	$dir=ereg_replace(" ","",$dir);  // 공백 제거
	$path=$originalpath.$dir."/";
	$dir="";
}
if(strlen($path)<strlen($originalpath)) $path=$originalpath;

$subpath=substr($path,$rootlength);
$subpath3=substr($path,$rootlength,strlen($originalpath)-$rootlength);
$subpath2=substr($path,strlen($originalpath));

?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>이미지 지정</title>
<link rel="stylesheet" href="<?=$Dir.AdminDir?>style.css" type="text/css">
<style>
#demo {
	border: 1px solid #E2E2E2;
	padding: 20px;
}
</style>
<script type="text/javascript" src="../../..//lib/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?=$Dir.AdminDir?>/uploadify/swfobject.js"></script>
<script type="text/javascript" src="<?=$Dir.AdminDir?>/uploadify/jquery.uploadify.js"></script>
<script type="text/javascript">
<!--
$j(document).ready(function() {
	$j('#file_upload').uploadify({
		'uploader'  : '../../../admin/uploadify/uploadify.swf',
		'script'    : '../../../admin/uploadify/uploadify.php',
		'cancelImg' : '../../../admin/uploadify/cancel.png',
		'folder'    : '<?=$originalpath?>',
		'multi'     : false,
		'auto'      : true,
		'buttonText' : 'UPLOAD',
		'fileExt'   : '*.jpg;*.gif;*.png',
		'fileDesc'  : 'Image Files',
		'onComplete' : function(e,ID,fObj,res,data) {
			var url = res.split("design/");
			document.form1.filename.value = "/<?=RootPath.DataDir?>design/"+url[1];
		}
	});
});

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 70;

	window.resizeTo(oWidth,oHeight);
}
/*
function change(temp){
	document.form1.filename.value="/<?=RootPath.DataDir?>design/"+temp;
	newImg = new Image();
	newImg.src="/<?=RootPath.DataDir?>design/"+temp;
	if(newImg.width>210) document.form1.images.width=210;
	else document.form1.images.width=newImg.width;
	if(newImg.height>100) document.form1.images.height=100;
	else document.form1.images.height=newImg.height;
	document.form1.images.src=newImg.src;
	document.form1.images.style.display="";
}
*/
function insertimage(){
	//if(document.form1.filename.value.length==0 || document.form1.filelist.value=="") {
	//	alert('이미지를 선택하지 않으셨습니다.');
	//	document.form1.filelist.focus();
	//	return;
	//}else {
		opener.document.all.txtFileName.value=document.form1.filename.value;
		window.close();
	//}
}

//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="550" BORDER=0 CELLPADDING=0 CELLSPACING=0 id=table_body>
<tr>
	<td>		
	<div id="demo">
	<input type="file" id="file_upload" name="file_upload" />
	</div>
	<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
	<input type=hidden name=filename>
	</form>
	</td>
</tr>
<TR>
	<TD align=center>
		<a href="javascript:insertimage()"><img src=<?=$Dir.AdminDir?>images/btn_ok.gif border="0"></a><a href="javascript:window.close()"><img src="<?=$Dir.AdminDir?>images/btn_close.gif" border="0" hspace="10"></a>
	</TD>
</TR>
<tr><td height=10></td></tr>
</TABLE>

</body>
</html>