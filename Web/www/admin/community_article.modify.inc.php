<?
$mode=$_REQUEST["mode"];
$exec=$_REQUEST["exec"];
$num=$_REQUEST["num"];

$sql = "SELECT * FROM tblboard WHERE num = ".$num." ";
$result = mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	mysql_free_result($result);

	$setup = @mysql_fetch_array(@mysql_query("SELECT * FROM tblboardadmin WHERE board ='".$row->board."'",get_db_conn()));
	$setup[max_filesize] = $setup[max_filesize]*(1024*100);
	$setup[btype]=substr($setup[board_skin],0,1);
	if(strlen($setup[board])==0) {
		echo "<html><head><title></title></head><body onload=\"alert('해당 게시판이 존재하지 않습니다.');history.go(-1);\"></body></html>";exit;
	}
} else {
	$errmsg="수정할 게시글이 없습니다.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
}

if($setup[use_lock]=="N") {
	$hide_secret_start="<!--";
	$hide_secret_end="-->";
}

$up_board=$row->board;

if(($_POST[mode]=="up_result") && ($_POST[ins4e][mode]=="up_result") && ($_POST[up_subject]!="") && ($_POST[ins4e][up_subject]!="")) {


	$up_name = addslashes($_POST["up_name"]);
	$up_subject = str_replace("<!","&lt;!",$_POST["up_subject"]);
	$up_subject = addslashes($up_subject);
	$up_memo = str_replace("<!","&lt;!",$_POST["up_memo"]);

	$subCategory = $_POST["subCategory"];

	$up_url = $_POST["up_url"];

	/** 에디터 관련 파일 처리 추가 부분 */
	if(preg_match_all('/\/data\/editor\/([a-zA-Z0-9\.]+)/',$row->content,$edtimg)){
		if(!preg_match_all('/\/data\/editor\/([a-zA-Z0-9\.]+)/',$up_memo,$edimg)) $edimg[1] = array();
		foreach($edtimg[1] as $cimg){
			if(!in_array($cimg,$edimg[1])) @unlink($_SERVER['DOCUMENT_ROOT'].'/data/editor/'.$cimg);
		}
	}

	if(preg_match_all('/\/data\/editor_temp\/([a-zA-Z0-9\.]+)/',$up_memo,$edimg)){
		foreach($edimg[1] as $timg){
			@rename($_SERVER['DOCUMENT_ROOT'].'/data/editor_temp/'.$timg,$_SERVER['DOCUMENT_ROOT'].'/data/editor/'.$timg);
		}
		$up_memo = str_replace('/data/editor_temp/','/data/editor/',$up_memo);
	}
	/** #에디터 관련 파일 처리 추가 부분 */

	$up_memo = addslashes($up_memo);
	$up_email=$_POST["up_email"];
	$up_filename=$_POST["up_filename"];

	$up_is_secret=$_POST["up_is_secret"];
	if (!$up_is_secret) $up_is_secret = 0;
	$up_html=$_POST["up_html"];


	$sql  = "UPDATE tblboard SET ";
	$sql .= "name			= '".$up_name."', ";
	$sql .= "email			= '".$up_email."', ";
	$sql .= "is_secret		= '".$up_is_secret."', ";
	$sql .= "use_html		= '".$up_html."', ";
	$sql .= "title			= '".$up_subject."', ";
	if ($up_filename) {
		if(ProcessBoardFileModify($up_board,$up_filename,$row->filename)=="SUCCESS") {
			$sql .= "filename	= '".$up_filename."', ";
		}
	}
	$sql .= "content		= '".$up_memo."', ";
	$sql .= "subCategory			= '".$subCategory."', ";
	$sql .= "url			= '".$up_url."' ";

	$sql .= "WHERE board='".$up_board."' AND num = ".$num." ";
	$insert = mysql_query($sql,get_db_conn());

	if($insert) {
		echo("<meta http-equiv='Refresh' content='0; URL=".$_SERVER[PHP_SELF]."?exec=view&board=$board&num=$num&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage'>");
		exit;
	} else {
		echo "
			<script>
			window.alert('글수정 중 오류가 발생했습니다.');
			</script>
		";
		reWriteForm();
		exit;
	}
} else {
	if (strlen($row->filename)>0) {
		$thisBoard[filename] = "기존파일을 사용하려면 파일첨부 하지 마세요.";
	}

	if ($mode == "reWrite") {
		$thisBoard[content]  = stripslashes(urldecode($thisBoard[content]));
		$thisBoard[title]  = stripslashes(urldecode($thisBoard[title]));
		$thisBoard[name]  = stripslashes(urldecode($thisBoard[name]));
	} else if (!$mode) {
		$thisBoard[pos] = $row->pos;
		$thisBoard[is_secret] = $row->is_secret;
		$thisBoard[name] = stripslashes($row->name);
		$thisBoard[passwd] = $row->passwd;
		$thisBoard[email] = $row->email;
		$thisBoard[url] = $row->url;
		$thisBoard[title] = stripslashes($row->title);
		$thisBoard[content] = stripslashes($row->content);

		if ($row->use_html == "1") $thisBoard[use_html] = "checked";
	}

	if(strlen($row->pridx)>0 && $row->pridx>0) {
		$sql = "SELECT productcode,productname,etctype,sellprice,quantity,tinyimage FROM tblproduct ";
		$sql.= "WHERE pridx='".$row->pridx."' ";
		$result=mysql_query($sql,get_db_conn());
		if($_pdata=mysql_fetch_object($result)) {
			INCLUDE "community_article.prqna_top.inc.php";
		} else {
			$pridx="";
		}
		mysql_free_result($result);
	}




// 말머리
$subCateSQL = "SELECT `subCategory` FROM `tblboardadmin` WHERE `board` = '".$up_board."' ;";
$subCateRes = mysql_query($subCateSQL,get_db_conn());
$subCateRow = mysql_fetch_assoc ($subCateRes);
$subCategoryArray = explode(",",$subCateRow[subCategory]);

$subCategoryList = "";
$subCategoryList_start="<!--";
$subCategoryList_end="-->";

if( count($subCategoryArray) > 0 AND strlen($subCategoryArray[0]) > 0 ) {
	$subCategoryList .= "<select name='subCategory'><option value=".">--- 없음 ---</option>";
	foreach ($subCategoryArray as $V) {
		if($num > 0) $sel = ( $row->subCategory == $V )?"selected":"";
		$subCategoryList .= "<option value=\"".$V."\" ".$sel.">[".$V."]</option>";
	}
	$subCategoryList .= "</select>";

	$subCategoryList_start="";
	$subCategoryList_end="";

}
?>


<SCRIPT LANGUAGE="JavaScript">
<!--
function chk_writeForm(form) {
	if (typeof(form.tmp_is_secret) == "object") {
		form.up_is_secret.value = form.tmp_is_secret.options[form.tmp_is_secret.selectedIndex].value;
	}

	if (!form.up_name.value) {
		alert('이름을 입력하십시오.');
		form.up_name.focus();
		return false;
	}

	if (!form.up_subject.value) {
		alert('제목을 입력하십시오.');
		form.up_subject.focus();
		return false;
	}

	if (!form.up_memo.value) {
		alert('내용을 입력하십시오.');
		form.up_memo.focus();
		return false;
	}

	form.mode.value = "up_result";
	reWriteName(form);
	form.submit();
}

function putSubject(subject) {
	document.writeForm.up_subject.value = subject;
}

function FileUp() {
	fileupwin = window.open("","fileupwin","width=50,height=50,toolbars=no,menubar=no,scrollbars=no,status=no");
	while (!fileupwin);
	document.fileform.action = "<?=$Dir.BoardDir?>ProcessBoardFileUpload.php"
	document.fileform.target = "fileupwin";
	document.fileform.submit();
	fileupwin.focus();
}
// -->
</SCRIPT>

<SCRIPT LANGUAGE="JavaScript" src="<?=$Dir.BoardDir?>chk_form.js.php"></SCRIPT>
<? if($setup['use_html'] !="N"){ ?>
<script type="text/javascript" src="/gmeditor/js/jquery.js"></script>
<script type="text/javascript" src="/gmeditor/js/jquery.event.drag-2.0.min.js"></script>
<script type="text/javascript" src="/gmeditor/js/jquery.resizable.js"></script>
<script type="text/javascript" src="/gmeditor/js/ajax_upload.3.6.js"></script>
<script type="text/javascript" src="/gmeditor/js/ej.h2xhtml.js"></script>
<script type="text/javascript" src="/gmeditor/editor.js"></script>
<style type="text/css">
  @import url("/gmeditor/common.css");
</style>
<script language="javascript" type="text/javascript">
$(document).ready(function() {
	ejEditor();
});
</script>
<? } ?>

<table border=0 cellpadding=0 cellspacing=1 width=<?=$setup[board_width]?>>
<tr>
	<td height=15 style="padding-left:5"><B>[<?=$setup[board_name]?>]</B></td>
	<td align=right class="td_con1"><?=$strIp?></td>
</tr>
</table>

<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>

<form name=fileform method=post>
<input type=hidden name=board value="<?=$up_board?>">
<input type=hidden name=max_filesize value="<?=$setup[max_filesize]?>">
<input type=hidden name=img_maxwidth value="<?=$setup[img_maxwidth]?>">
<input type=hidden name=use_imgresize value="<?=$setup[use_imgresize]?>">
<input type=hidden name=btype value="<?=$setup[btype]?>">
</form>

<form name=writeForm method='post' action='<?= $_SERVER[PHP_SELF]?>' enctype='multipart/form-data'>
<input type=hidden name=mode value=''>
<input type=hidden name=exec value='<?=$_REQUEST["exec"]?>'>
<? if($setup['use_html'] !="N"){ ?>
<input type="hidden" name="up_html" value="1" />
<? } ?>

<input type=hidden name=num value=<?=$num?>>
<input type=hidden name=board value=<?=$board?>>
<input type=hidden name=s_check value=<?=$s_check?>>
<input type=hidden name=search value=<?=$search?>>
<input type=hidden name=block value=<?=$block?>>
<input type=hidden name=gotopage value=<?=$gotopage?>>
<input type=hidden name=pos value="<?=$thisBoard[pos]?>">
<input type=hidden name=up_is_secret value="<?=$thisBoard[is_secret]?>">

<TR>
	<TD background="images/table_top_line1.gif" colspan="2" width="760"><img src=img/table_top_line1.gif height=2></TD>
</TR>
<?= $hide_secret_start ?>
<TR>
	<TD class="board_cell1" align="center" width="111"><p>잠금기능</p></TD>
	<TD class="td_con1" align="center" width="627"><p align="left"><?= writeSecret($exec,$thisBoard[is_secret],$thisBoard[pos]) ?></TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="760"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<?= $hide_secret_end ?>





<?=$subCategoryList_start?>
<TR>
	<TD class="board_cell1" align="center" width="111"><p align="center">말머리</TD>
	<TD class="td_con1" align="center"><p align="left"><?=$subCategoryList?></TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="760"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<?=$subCategoryList_end?>




<TR>
	<TD class="board_cell1" align="center" width="111"><p align="center">글제목</TD>
	<TD class="td_con1" align="center"><p align="left"><INPUT maxLength=200 size=70 name=up_subject value="<?=$thisBoard[title]?>" style="width:100%" class="input"></TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="760"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<TR>
	<TD align="center" height="30" class="board_cell1" width="111"><p align="center">글쓴이</TD>
	<TD align="center" height="30" class="td_con1" width="257"><p align="left"><INPUT maxLength=20 size=13 name=up_name value="<?=$thisBoard[name]?>" style="width:100%" class="input"></TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="760"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<TR>
	<TD align="center" height="30" class="board_cell1" width="111"><p align="center">이메일</TD>
	<TD align="center" height="30" class="td_con1" width="257"><p align="left"><INPUT maxLength=60 size=49 name=up_email value="<?=$thisBoard[email]?>" class="input" style="width:255px"></TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="760"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>


<?
	if( $setup[linkboard] ) {
?>
<TR>
	<TD align="center" height="30" class="board_cell1" width="111"><p align="center">URL</TD>
	<TD align="center" height="30" class="td_con1" width="257"><p align="left"><INPUT size=49 name=up_url value="<?=$thisBoard[url]?>" class="input" style="width:255px"></TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="760"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<?
	}
?>


<TR>
	<TD class="board_cell1" width="111"><p align="center">글내용</p></TD>
	<TD class="td_con1" width="627">
	<? /*
	<?=$hide_html_start?>
	<B>HTML편집</B><INPUT style="BORDER-RIGHT: #dfdfdf 1px solid; BORDER-TOP: #dfdfdf 1px solid; BORDER-LEFT: #dfdfdf 1px solid; BORDER-BOTTOM: #dfdfdf 1px solid" type=checkbox name=up_html value="1" <?=$thisBoard[use_html]?>><br>
	<?=$hide_html_start?>
	<TEXTAREA style="WIDTH: 100%; HEIGHT: 280px" name=up_memo wrap=off class="textarea"><?=$thisBoard[content]?></TEXTAREA>*/?>
	<TEXTAREA style="WIDTH: 100%; HEIGHT: 280px" name=up_memo wrap=off lang="ej-editor3" class="textarea"><?=$thisBoard[content]?></TEXTAREA>
	</TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="760"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<TR>
	<TD class="board_cell1" width="111"><p align="center">첨부파일</p></TD>
	<TD class="td_con1" width="627"><INPUT onfocus=this.blur(); size="50" name=up_filename class="input"> <INPUT style="BORDER-RIGHT: #cccccc 1px solid; BORDER-TOP: #cccccc 1px solid; BORDER-LEFT: #cccccc 1px solid; CURSOR: hand; BORDER-BOTTOM: #cccccc 1px solid" onclick=FileUp(); type=button value=파일첨부 class="submit1"> &nbsp;<span class="font_orange">*최대 <?=($setup[max_filesize]/1024)?>KB 까지 업로드 가능</span></TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="760"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<? if ($thisBoard[filename]) { ?>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="760">(<?=$thisBoard[filename]?>)</TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="760"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<? } ?>
</TABLE>

<img width=0 height=10><br>
<SCRIPT LANGUAGE="JavaScript">
<!--
field = "";
for(i=0;i<document.writeForm.elements.length;i++) {
	if(document.writeForm.elements[i].name.length>0) {
		field += "<input type=hidden name=ins4eField["+document.writeForm.elements[i].name+"]>\n";
	}
}
document.write(field);
//-->
</SCRIPT>

</form>

<div align=center>
	<img src="<?=$imgdir?>/butt-ok.gif" border=0 style="cursor:hand;" onclick="chk_writeForm(document.writeForm);"> &nbsp;&nbsp;
	<IMG SRC="<?=$imgdir?>/butt-cancel.gif" border=0 style="CURSOR:hand" onClick="history.go(-1);">
</div>


<?
}
?>