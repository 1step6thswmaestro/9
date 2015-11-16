<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
//INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-1";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$rootlength=strlen($Dir)-1;	### design/ 전 까지의 string length
$max=7;		### 전체경로의 디렉토리 "/" 갯수
$dirmax=20;		### 생성 가능한 디렉토리 갯수

$type=$_POST["type"];
$dir=$_POST["dir"];
$delfile=$_POST["delfile"];
$selectdir=$_POST["selectdir"];
$newdir=$_POST["newdir"];

$path="";

$upfile1=$_FILES[upfile1];
$upfile2=$_FILES[upfile2];
$upfile3=$_FILES[upfile3];
$upfile4=$_FILES[upfile4];
$upfile5=$_FILES[upfile5];
$upfile6=$_FILES[upfile6];
$upfile7=$_FILES[upfile7];
$upfile8=$_FILES[upfile8];
$upfile9=$_FILES[upfile9];
$upfile10=$_FILES[upfile10];

$originalpath = $Dir.DataDir."design/";
//$originalpath=ereg_replace("\.\.","",$originalpath);

$originallength=strlen($originalpath);

$total=0;
getDirList(substr($originalpath,0,-1));
@sort($dirlist);
$temp=$dirlist;
$number = sizeof($temp);
for($i=0;$i<$number;$i++) {
	$tempdir=$temp[$i];
	if(strlen($tempdir)>$originallength && is_dir($tempdir)) {
		$total++;
	}
}

if(strlen($dir)==0)
	$path=$originalpath;
else {
	$dir=ereg_replace("\.\.","",$dir); // 상위디렉토리로 이동 금지
	$dir=ereg_replace(" ","",$dir);  // 공백 제거
	$path=$originalpath.$dir."/";
	$dir="";
}
if(strlen($path)<strlen($originalpath)) $path=$originalpath;

$countslash = explode("/", $path);

if($type=="ins") $max=$max-1;
if(count($countslash)>$max || ($type=="ins" && $total>=$dirmax)) {
	if($type=="ins" && $total>=$dirmax) {
		echo "<html><head><title></title></head><body onload=\"alert('디렉토리는 총 ".$dirmax."개가 등록됩니다. 더이상 등록이 불가능합니다.');\"></body></html>";
	} else {
		echo "<html><head><title></title></head><body onload=\"alert('더이상 하위 디렉토리 등록이되지 않습니다.');\"></body></html>";
	}
	$type="";
	$temppath=substr($path,0,-1);
	$path = substr($temppath,0,strrpos($temppath,"/"))."/";
}
if($type=="del") {
	$temppath=substr($path,0,-1);
	proc_rmdir($temppath);
	$path = substr($temppath,0,strrpos($temppath,"/"))."/";
} else if($type=="mv") {
	$temppath=substr($path,0,-1);
	$path2 = substr($temppath,0,strrpos($temppath,"/"))."/";
	$path2 = $path2.$selectdir."/";

	if(is_dir($path2)) {
		echo "<html><head><title></title></head><body onload=\"alert('같은 이름의 디렉토리가 있습니다.');\"></body></html>";
	} else {
		rename($path,$path2);
		$path=$path2;
		$dir=substr($path,strlen($originalpath));
		$dir=substr($dir,0,-1);
	}
}

if(!is_dir($path) && $total<$dirmax) {
	mkdir($path);
	chmod($path, 0755);
} else if($type=="ins" && $total<$dirmax) {
	echo "<html><head><title></title></head><body onload=\"alert('같은 이름의 디렉토리가 있습니다.');\"></body></html>";
} else if(!is_dir($path)) {
	echo "<html><head><title></title></head><body onload=\"alert('디렉토리는 총 ".$dirmax."개가 등록됩니다. 더이상 등록이 불가능합니다.');history.go(-1);\"></body></html>";
	exit;
}

if ($type=="filedelete" && $delfile) {
	$delfile=substr($delfile,1);
	$ardelfile = explode("|",$delfile);
	$arnum = count($ardelfile);
	for($i=0;$i<$arnum;$i++)
		if(file_exists($originalpath.$ardelfile[$i])) unlink($originalpath.$ardelfile[$i]);
}

$dir2=substr($path,0,-1);
$dir2=substr($dir2,strrpos($dir2,"/")+1);

if($type=="fileupload") {
	$filearray = array (&$upfile1,&$upfile2,&$upfile3,&$upfile4,&$upfile5,&$upfile6,&$upfile7,&$upfile8,&$upfile9,&$upfile10);
	$filesize=(int)$filearray[0]["size"]+(int)$filearray[1]["size"]+(int)$filearray[2]["size"]+(int)$filearray[3]["size"]+(int)$filearray[4]["size"]+(int)$filearray[5]["size"]+(int)$filearray[6]["size"]+(int)$filearray[7]["size"]+(int)$filearray[8]["size"]+(int)$filearray[9]["size"];
	if($filesize>2097152) {
		echo "<html><head><title></title></head><body onload=\"alert('파일용량이 300KByte를 초과되었습니다.');\"></body></html>";
	} else {
		$cnt= count($filearray);
		for($i=0;$i<$cnt;$i++){
			if (strlen($filearray[$i]["name"])>0 && file_exists($filearray[$i]["tmp_name"])==true) {
				$filearray[$i]["name"]=ereg_replace(" ","",$filearray[$i]["name"]);
				$ext = substr($filearray[$i]["name"],-4);
				if($ext =="html") {
					$filearray[$i]["name"] = substr($filearray[$i]["name"],0,(strlen($filearray[$i]["name"]))-1);
				}
				else if (substr($ext,-3)=="php") $filearray[$i]["name"]=$filearray[$i]["name"]."s";

				if($filearray[$i]["size"] > 2097152) {
					echo "<html><head><title></title></head><body onload=\"alert('파일용량이 2MByte를 초과되었습니다.');\"></body></html>";
				} else {
					if(move_uploaded_file($filearray[$i]["tmp_name"],$path.$filearray[$i]["name"])) {
						chmod($path.$filearray[$i]["name"],0604);
					}
				}
			}
		}
	}
}

$subpath=substr($path,$rootlength);
$subpath3=substr($path,$rootlength,strlen($originalpath)-$rootlength);
$subpath2=substr($path,strlen($originalpath));

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function htmledit() {
	if (document.form1.filelist.selectedIndex==-1) {
		alert("파일목록에서 파일을 선택하세요.");
		return;
	}
	filename = document.form1.filelist.options[document.form1.filelist.selectedIndex].value;
	ext = filename.substring(filename.length-3,filename.length);
	ext = ext.toLowerCase();
	if (ext!="htm" && ext!="css") {
		alert("htm(html),css파일만 편집하실 수 있습니다.");
		return;
	}
	window.open("about:blank","webftpetcpop","height=10,width=10");
	document.form3.action="design_webftp.edit.php";
	document.form3.val.value=filename;
	document.form3.submit();
}

function imageview() {
	if(document.form1.filelist.selectedIndex==-1) {
		alert("파일목록에서 이미지 파일을 선택하세요.");
		return;
	}
	filename = document.form1.filelist.options[document.form1.filelist.selectedIndex].value;
	ext = filename.substring(filename.length-3,filename.length);
	ext = ext.toLowerCase();
	if (ext!="gif" && ext!="jpg" && ext!="png") {
		alert("PNG, GIF, JPG파일만 보실 수 있습니다.");return;
	}
	window.open("about:blank","webftpetcpop","height=10,width=10,scrollbars=1");
	document.form3.action="design_webftp.imgview.php";
	document.form3.val.value=filename;
	document.form3.submit();
}

function delete_file() {
	val="";
	if(document.form1.filelist.selectedIndex==-1) {
		alert("삭제할 파일을 선택하세요.");
		return;
	}
	for(i=0;i<document.form1.filelist.options.length;i++) {
		if(document.form1.filelist.options[i].selected==true) {
			if(document.form1.filelist.options[i].value.length>0) {
				val+="|"+document.form1.filelist.options[i].value;
			}
		}
	}
	if(val.length==0) {
		alert("선택하신 폴더에 등록된 파일이 없습니다.");
		return;
	}
	if(confirm("선택된 파일 삭제하시겠습니까?")) {
		document.form2.type.value="filedelete";
		document.form2.delfile.value=val;
		document.form2.submit();
	}
}

function upfile_plus() {
	for(i=4;i<=10;i++) {
		if(document.all) {
			if(document.all["hideupfile"+i].style.display=="none") {
				document.all["hideupfile"+i].style.display="block";
				break;
			}
		} else if(document.getElementById) {
			if(document.getElementById("hideupfile"+i).style.display=="none") {
				document.getElementById("hideupfile"+i).style.display="block";
				break;
			}
		}
	}
}

function select_file(filepath) {
	if(filepath.length>0) {
		document.all["fileurlidx"].innerHTML="/<?=RootPath.substr($subpath3,1)?>"+filepath;
	}
}

function dir_delete() {
	if(document.form1.dir.value.length==0) {
		alert('기본 폴더는 삭제하실수 없습니다.');
		return;
	}
	if(!confirm("선택한 디렉토리를 삭제하시겠습니까?")) return;
	if(document.form1.count.value>0) {
		if(!confirm("선택한 디렉토리에 파일이나 디렉토리가 존재합니다.\n모두 삭제하시겠습니까?")) return;
	}
	document.form1.type.value="del";
	document.form1.submit();
}

function dir_modify() {
	if(document.form1.dir.value.length==0) {
		alert('기본 폴더는 수정이 불가능합니다.');
		return;
	}
	temp=document.form1.selectdir.value;
	count=0;
	for(i=0;i<temp.length;i++) {
		temp2=temp.substr(i,1);
		if((temp2>="0" && temp2<="9") || (temp2>="a" && temp2<="z") || (temp2>="A" && temp2<="Z") || temp2=="_" || temp2 =="-") {
			count++;
		} else {
			alert('디렉토리명에 올수 없는 이름입니다. 다시 입력하세요');
			document.form1.selectdir.focus();
			return;
		}
	}
	if (count>0) {
		if (!confirm("선택한 디렉토리명을 변경하시겠습니까?")) return;
		document.form1.type.value="mv";
		document.form1.submit();
	}
}

function dir_new() {
	if(document.form1.newdir.value == ""){
		alert("디렉토리명이 입력되지 않았습니다.");
		return;
	}
<?
$countslash2 = explode("/", $subpath);
if(count($countslash2)>$max) {
?>
	alert('하위 폴더를 등록하실수가 없습니다.');
	return;
<?}else{?>
	temp=document.form1.newdir.value;
	count=0;
	for(i=0;i<temp.length;i++) {
		temp2=temp.substr(i,1);
		if((temp2>="0" && temp2<="9") || (temp2>="a" && temp2<="z") || (temp2>="A" && temp2<="Z") || temp2=="_" || temp2=="-") {
			count++;
		} else{
			alert('디렉토리명에 올수 없는 이름입니다. 다시 입력하세요');
			document.form1.newdir.focus();
			return;
		}
	}
	if (count>0) {
		if(!confirm("디렉토리를 등록하시겠습니가?")) return;
	}
	if(document.form1.dir.value.length==0) document.form1.dir.value=temp;
	else document.form1.dir.value=document.form1.dir.value+"/"+temp;
	document.form1.type.value="ins";
	document.form1.submit();
<?}?>
}

function upload_file() {
	<?for($i=1; $i<=10; $i++){?>
		filename = document.form1.upfile<?=$i?>.value;
		if(filename.length >0){
			ext = filename.substring(filename.indexOf('.')+1,filename.length).toLowerCase();
			if(ext!="gif" && ext!="jpg" && ext!="png" && ext!="html" && ext!="htm"){
				alert("<?=$i?>번째 파일의 확장자명이 잘못되었습니다.");
				return;
			}
		}
	<?}?>
	
	if(confirm("파일을 업로드 하시겠습니까?")) {
		document.form1.type.value="fileupload";
		document.form1.submit();
	}
}

function download_file(path){
	window.open("about:blank","webftpetcpop","height=10,width=10");
	document.form3.action="design_webftp.down.php";
	document.form3.val.value=path;
	document.form3.submit();
}

function setPopup(type){
	if(type=="popup")
		document.all["dir_frame"].src = "design_webftp.directory.php?dir=<?=substr($subpath2,0,-1)?>&popup=ok";
}
</script>

<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>" enctype="multipart/form-data">
	<input type=hidden name=type>
	<input type=hidden name=dir value="<?=substr($subpath2,0,-1)?>">
	<input type=hidden name=count value="<?=$count2?>">
	<input type=hidden name=filesize>
	<input type=hidden name=popup value="">
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
		<col width=310></col>
		<col width=50></col>
		<col width=></col>
		<tr><td colspan=3 height=1 background="images/table_top_line.gif"></td></tr>
		<tr>
			<td class="table_cell" align="center"><FONT color=#3d3d3d><B>디렉토리</B></FONT></td>
			<td class="table_cell1" align="center">&nbsp;</td>
			<td class="table_cell1" align="center" background="images/blueline_bg.gif"><b><font color="#555555">파일목록</font></b></td>
		</tr>
		<TR>
			<TD colspan="3" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
		</TR>
		<tr>
			<td align=center valign=top style="padding:4">
				<!-- 디렉토리 목록 시작 -->
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td>
							<table cellpadding="8" cellspacing="0" width="100%" bgcolor="#EBEBEB">
								<tr>
									<td align=center>
										<IFRAME name="dir_frame" style="WIDTH:100%;HEIGHT:262px" src="design_webftp.directory.php?dir=<?=substr($subpath2,0,-1)?>" scrolling=yes size="6" marginwidth=5 marginheight=5></IFRAME>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td height=10></td></tr>
					<tr>
						<td>
							<table border=0 cellpadding=0 cellspacing=0 width=100%>
								<col width=65></col>
								<col width=></col>
								<col width=38></col>
								<col width=38></col>
								<tr>
									<td>
										<FONT color=#3d3d3d><img src="images/design_webftp_text01.gif" border="0"></FONT>
									</td>
									<td>
										<FONT color=#3d3d3d><INPUT style="width:99%" size=32 name=selectdir class="input"
										<?
											if($originalpath!=$path) {
												echo "value=\"$dir2\"";
											} else {
												echo "disabled style=\"background='silver'\"";
											}
										?>
										></FONT>
									</td>
									<td>
										<FONT color=#3d3d3d><A href="javascript:dir_modify()"><IMG src="images/icon_edit2.gif" align=absmiddle border=0></A> </FONT>
									</td>
									<td>
										<FONT color=#3d3d3d><A href="javascript:dir_delete()"><IMG src="images/icon_del1.gif" align=absmiddle border=0></A></FONT>
									</td>
								</tr>
								<tr>
									<td>
										<FONT color=#3d3d3d><img src="images/design_webftp_text02.gif" border="0"></FONT>
									</td>
									<td>
										<FONT color=#3d3d3d><INPUT size=32 name=newdir class="input" style=width:98%></FONT>
									</td>
									<td colspan=2>
										<FONT color=#3d3d3d><A href="javascript:dir_new()"><IMG src="images/icon_newfolder.gif" align=absmiddle border=0></A></FONT>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td height=1></td></tr>
					<tr>
						<td><hr size="1" color="#EBEBEB"></td>
					</tr>
					<tr>
						<td>* <FONT color=#0054a6>현재 경로 : /<?=RootPath.substr($subpath,1)?></FONT></td>
					</tr>
					<tr>
						<td><hr size="1" color="#EBEBEB"></td>
					</tr>
				</table>
				<!-- 디렉토리 목록 끝 -->
			</td>
			<!-- -->
			<TD class="td_con1" align="center" valign=top style="padding-top:130"><img src="images/icon_nero.gif" border="0"></TD>
			<td class="td_con1" align=center valign=top style="padding:5">
				<!-- 파일목록 시작 -->
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td>
							<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#EDEDED">
								<tr>
									<td style="padding:8,8,0,8">
										<SELECT style="width:100%;" name=filelist size=16 multiple onchange="select_file(options.value)" class="font_size1">
										<?
											unset($temp);
											$temp=getFileList(substr($path,0,-1));
											sort($temp);
											for ($i=0;$i<sizeof($temp);$i++) {
												$filename=ereg_replace("\*","",$temp[$i]);
												echo "<option value=\"$subpath2$filename\">$filename\n";
												$tok = strtok("\n");
											}
											if ($i==0) echo "<option value=\"\">등록된 파일이 없습니다.";
										?>
										</SELECT>
									</td>
								</tr>
								<tr>
									<td align=center>
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td align=center>
													<a href="javascript:htmledit()"><IMG SRC="images/design_webftp_icon1.gif" border="0"></a>
												</td>
												<td align=center>
													<a href="javascript:imageview();"><IMG SRC="images/design_webftp_icon2.gif" border="0"></a>
												</td>
												<td align=center>
													<a href="javascript:delete_file()"><IMG SRC="images/design_webftp_icon3.gif" border="0"></a>
												</td>
											</tr>
										</table>	
									</td>	
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td height="30">
							<table border=0 cellpadding=0 cellspacing=0 width=100%>
								<col width=90></col>
								<col width=></col>
								<tr>
									<td>&nbsp;* 이미지경로 : </td>
									<td id = 'fileurlidx'>선택안됨</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="padding-top:2pt; padding-bottom:2pt;">
							<table cellpadding="1" cellspacing="0" align="center" width="150">
								<tr>
									<td><A HREF="javascript:upload_file()"><img src="images/btn_upload.gif" border="0"></A></td>
									<td><a HREF="javascript:download_file('<?=substr($subpath2,0,-1)?>')"><img src="images/btn_download.gif" border="0"></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td align=center>
							<table cellpadding="20" cellspacing="4" width="100%" bgcolor="#EDEDED">
	    							<tr>
        									<td  bgcolor="white"height="60">
										<table cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td><a href="javascript:upfile_plus()"><img src="images/design_webftp_btnfileup.gif" width="242" height="22" border="0"></a></td>
											</tr>
											<tr><td height=3></td></tr>
											<tr>
												<td>
													<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
														<tr>
															<TD>
																<INPUT class="input" type=file name=upfile1 style=width:100%>
															</td>
														</tr>
														<tr>
															<TD>
																<INPUT class="input" type=file name=upfile2 style=width:100%>
														</td>	
														</tr>
														<tr>
															<TD>
																<INPUT class="input" type=file name=upfile3 style=width:100%>
															</td>
														</tr>
														<?
															for($i=4;$i<=10;$i++) {
																echo "<tr id=\"hideupfile".$i."\" style=\"display:none\">\n";
																echo "	<td>\n";
																echo "		<INPUT class=\"input\" type=file name=upfile".$i." style=width:100%>\n";
																echo "	</td>\n";
																echo "</tr>\n";
															}
														?>
														<tr>
															<td><img src="images/design_webftp_btntext.gif" width="281" height="14" border="0" vspace="2"></td>
														</tr>
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
				<!-- 파일목록 끝 -->
			</td>
		</tr>
		<tr><td colspan=3 height=1 background="images/table_top_line.gif"></td></tr>
	</table>
</form>	
<form name=form2 method=post action="<?=$_SERVER[PHP_SELF]?>">
	<input type=hidden name=type>
	<input type=hidden name=dir value="<?=substr($subpath2,0,-1)?>">
	<input type=hidden name=delfile>
</form>

<?=$onload?>