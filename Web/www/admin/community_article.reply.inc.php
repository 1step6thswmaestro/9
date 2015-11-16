<?
$mode=$_REQUEST["mode"];
$exec=$_REQUEST["exec"];
$num=$_REQUEST["num"];

// 상점정보 - 관리자 이메일
$sql = "SELECT info_email FROM tblshopinfo ";
$result = mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$info_email = $row->info_email;
}
mysql_free_result($result);

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
	$errmsg="답변할 게시글이 없습니다.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
}

if($setup[use_lock]=="N") {
	$hide_secret_start="<!--";
	$hide_secret_end="-->";
}

$up_board=$row->board;

//웹진형과 앨범형 게시판은 답변쓰기가 안된다.
if($setup[btype]!="L") {
	$errmsg="본 게시판은 답변쓰기 기능이 지원되지 않습니다.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
}

if(($_POST[mode] == "up_result") && ($_POST[ins4e][mode] == "up_result") && ($_POST[up_subject] != "") && ($_POST[ins4e][up_subject] != "")) {
	// ======== thread, pos, depth 정의 ========
	$sql = "UPDATE tblboard SET pos = pos+1 WHERE board='".$up_board."' AND thread=".$row->thread." AND pos>".$row->pos." ";
	$update = mysql_query($sql,get_db_conn());

	//메일용 변수
	$send_email = $_POST["up_email"];
	$send_name = $_POST["up_name"];
	$send_subject = $_POST["up_subject"];
	$send_memo = stripslashes($_POST["up_memo"]);
	$send_filename= $_POST["up_filename"];
	if (!$_POST["up_html"]) {
		$send_memo = nl2br(stripslashes($_POST["up_memo"]));
	}
	$send_date = date("Y-m-d H:i:s");

	$up_name = addslashes($_POST["up_name"]);
	$up_subject = str_replace("<!","&lt;!",$_POST["up_subject"]);
	$up_subject = addslashes($up_subject);
	$up_memo = str_replace("<!","&lt;!",$_POST["up_memo"]);

	/** 에디터 관련 파일 처리 추가 부분 */
	if(preg_match_all('/\/data\/editor_temp\/([a-zA-Z0-9\.]+)/',$up_memo,$edimg)){
		foreach($edimg[1] as $timg){
			@rename($_SERVER['DOCUMENT_ROOT'].'/data/editor_temp/'.$timg,$_SERVER['DOCUMENT_ROOT'].'/data/editor/'.$timg);
		}
		$up_memo = str_replace('/data/editor_temp/','/data/editor/',$up_memo);
		$send_memo = str_replace('/data/editor_temp/','/data/editor/',$send_memo);
	}
	/** #에디터 관련 파일 처리 추가 부분 */

	$up_memo = addslashes($up_memo);
	$up_email=$_POST["up_email"];
	$up_filename=$_POST["up_filename"];





	$up_is_secret=$_POST["up_is_secret"];
	if (!$up_is_secret) $up_is_secret = 0;
	$up_html=$_POST["up_html"];

	if(ProcessBoardFileIn($up_board,$up_filename)!="SUCCESS") {
		$up_filename="";
	}

	$sql  = "INSERT tblboard SET ";
	$sql .= "board				= '".$up_board."', ";
	$sql .= "num				= '', ";
	$sql .= "thread				= ".$row->thread.", ";
	$sql .= "pos				= ".($row->pos+1).", ";
	$sql .= "depth				= ".($row->depth+1).", ";
	$sql .= "prev_no			= '".$row->prev_no."', ";
	$sql .= "next_no			= '".$row->next_no."', ";
	$sql .= "pridx				= '".$row->pridx."', ";
	$sql .= "name				= '".$up_name."', ";
	$sql .= "passwd				= '".$setup["passwd"]."', ";
	$sql .= "email				= '".$up_email."', ";
	$sql .= "is_secret			= '".$up_is_secret."', ";
	$sql .= "use_html			= '".$up_html."', ";
	$sql .= "title				= '".$up_subject."', ";
	$sql .= "filename			= '".$up_filename."', ";
	$sql .= "writetime			= '".time()."', ";
	$sql .= "ip					= '".getenv("REMOTE_ADDR")."', ";
	$sql .= "access				= '0', ";
	$sql .= "total_comment		= '0', ";
	$sql .= "content			= '".$up_memo."', ";
	$sql .= "notice				= '0', ";
	$sql .= "deleted			= '0' ";
	$insert = mysql_query($sql,get_db_conn());

	if($insert) {
		$qry = "SELECT LAST_INSERT_ID() ";
		$res = mysql_fetch_row(mysql_query($qry,get_db_conn()));
		$thisNum = $res[0];

		// ===== 관리테이블의 게시글수 update =====
		$sql3 = "UPDATE tblboardadmin SET total_article=total_article+1 WHERE board='".$up_board."' ";
		$update = mysql_query($sql3,get_db_conn());

		if (strlen($row->email)>0) {
			INCLUDE ($Dir.BoardDir."SendForm.inc.php");

			$title = $send_subject;
			$message = GetHeader() . GetContent($send_name, $send_email, $send_subject, $send_memo,$send_date,$send_filename,$setup[board_name], "re", $row->name) . GetFooter();

			sendMailForm($send_name,$send_email,$message,$bodytext,$mailheaders);

			if (ismail($row->email)) {
				mail($row->email, $title, $bodytext, $mailheaders);
			}
		}

		if($setup[reply_sms] == "Y" && strlen(str_replace("-","",$row->usercel))>0) {
			$sqlsms = "SELECT * FROM tblsmsinfo limit 1 ";
			$resultsms= mysql_query($sqlsms,get_db_conn());
			if($rowsms=@mysql_fetch_object($resultsms)){

				$sms_id=$rowsms->id;
				$sms_authkey=$rowsms->authkey;

				$fromtel=$rowsms->return_tel;

				$smsboardname=str_replace("\\n"," ",str_replace("\\r","",strip_tags($setup[board_name])));
				$smsboardsubject=str_replace("\\n"," ",str_replace("\\r","",strip_tags(str_replace("&lt;!","<!",stripslashes($up_subject)))));

				$smsmsg="[".$_shopdata->shopname."] ".$setup[board_name]."에 올리신 글에 답변이 등록되었습니다.";

				$etcmsg="게시판 답변등록 메세지";
				if($rowsms->sleep_time1!=$rowsms->sleep_time2){
					$date="0";
					$time = date("Hi");
					if($rowsms->sleep_time2<"12" && $time<=substr("0".$rowsms->sleep_time2,-2)."59") $time+=2400;
					if($rowsms->sleep_time2<"12" && $rowsms->sleep_time1>$rowsms->sleep_time2) $rowsms->sleep_time2+=24;

					if($time<substr("0".$rowsms->sleep_time1,-2)."00" || $time>=substr("0".$rowsms->sleep_time2,-2)."59"){
						if($time<substr("0".$rowsms->sleep_time1,-2)."00") $day = date("d");
						else $day=date("d")+1;
						$date = date("Y-m-d H:i:s",mktime($rowsms->sleep_time1,0,0,date("m"),$day,date("Y")));
					}
				}
				$temp=SendSMS($sms_id, $sms_authkey, $row->usercel, "", $fromtel, $date, $smsmsg, $etcmsg);
				mysql_free_result($resultsms);
			}
		}

		echo("<meta http-equiv='Refresh' content='0; URL=".$_SERVER[PHP_SELF]."?board=$board&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage'>");
		exit;
	} else {
		echo "
			<script>
			window.alert('글답변 중 오류가 발생했습니다.');
			</script>
		";
		reWriteForm();
		exit;
	}
} else {

	if ($mode == "reWrite") {
		$thisBoard[content]  = stripslashes(urldecode($thisBoard[content]));
		$thisBoard[title]  = stripslashes(urldecode($thisBoard[title]));
		$thisBoard[summary]  = stripslashes(urldecode($thisBoard[summary]));
		$thisBoard[name]  = stripslashes(urldecode($thisBoard[name]));
	} else if (!$mode) {

		$thisBoard[pos] = $row->pos;
		$thisBoard[is_secret] = $row->is_secret;
		$thisBoard[use_anonymouse] = $row->use_anonymouse;
		$thisBoard[sitelink1] = $row->sitelink1;
		$thisBoard[sitelink2] = $row->sitelink2;
		$thisBoard[name] = "관리자";
		$thisBoard[email] = $info_email;

		$thisBoard[title] = stripslashes($row->title);

		$thisBoard[content] = stripslashes($row->content);

		$thisBoard[title]    = "[답변]" . $thisBoard[title];

		$thisBoard[content]  = "<BR><BR><BR>'".stripslashes($row->name)."'님이 쓰신글<BR>";
		$thisBoard[content] .= "------------------------------------<BR>";
		$thisBoard[content] .= ">" . str_replace(chr(10), chr(10).">", $row->content) . "<BR>";
		$thisBoard[content] .= "------------------------------------<BR>";
	}

	if(strlen($row->pridx)>0 && $row->pridx>0) {
		$sql = "SELECT productcode,productname,etctype,sellprice,quantity,tinyimage  FROM tblproduct ";
		$sql.= "WHERE pridx='".$row->pridx."' ";
		$result=mysql_query($sql,get_db_conn());
		if($_pdata=mysql_fetch_object($result)) {
			INCLUDE "community_article.prqna_top.inc.php";
		} else {
			$pridx="";
		}
		mysql_free_result($result);
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
	<TD background="images/table_top_line1.gif" colspan="2" width="<?=$setup[board_width]?>"><img src=img/table_top_line1.gif height=2></TD>
</TR>
<?= $hide_secret_start ?>
<TR>
	<TD class="board_cell1" align="center" width="111"><p>잠금기능</p></TD>
	<TD class="td_con1" align="center" width="627"><p align="left"><?= writeSecret($exec,$thisBoard[is_secret],$thisBoard[pos]) ?></TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup[board_width]?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<?= $hide_secret_end ?>
<TR>
	<TD class="board_cell1" align="center" width="111"><p align="center">글제목</TD>
	<TD class="td_con1" align="center"><p align="left"><INPUT maxLength=200 size=70 name=up_subject value="<?=$thisBoard[title]?>" style="width:100%" class="input"></TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup[board_width]?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<TR>
	<TD align="center" height="30" class="board_cell1" width="111"><p align="center">글쓴이</TD>
	<TD align="center" height="30" class="td_con1" width="257"><p align="left"><INPUT maxLength=20 size=13 name=up_name value="<?=$thisBoard[name]?>" style="width:100%" class="input"></TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup[board_width]?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<TR>
	<TD align="center" height="30" class="board_cell1" width="111"><p align="center">이메일</TD>
	<TD align="center" height="30" class="td_con1" width="257"><p align="left"><INPUT maxLength=60 size=49 name=up_email value="<?=$thisBoard[email]?>" class="input" style="width:255px"></TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup[board_width]?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<script>putSubject("<?=addslashes($thisBoard[title])?>");</script>
<TR>
	<TD class="board_cell1" width="111"><p align="center">글내용</p></TD>
	<TD class="td_con1" width="627">
	<? /*
	<?=$hide_html_start?>
	<B>HTML편집</B><INPUT style="BORDER-RIGHT: #dfdfdf 1px solid; BORDER-TOP: #dfdfdf 1px solid; BORDER-LEFT: #dfdfdf 1px solid; BORDER-BOTTOM: #dfdfdf 1px solid" type=checkbox name=up_html value="1" <?=$thisBoard[use_html]?>><br>
	<?=$hide_html_start?>
	<TEXTAREA style="WIDTH: 100%; HEIGHT: 280px" name=up_memo wrap=off class="textarea"><?=$thisBoard[content]?></TEXTAREA> */ ?>
	<TEXTAREA style="WIDTH: 100%; HEIGHT: 280px" name=up_memo wrap=off lang="ej-editor3" class="textarea"><?=stripslashes($thisBoard[content])?></TEXTAREA>
	</TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup[board_width]?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<TR>
	<TD class="board_cell1" width="111"><p align="center">첨부파일</p></TD>
	<TD class="td_con1" width="627"><INPUT onfocus=this.blur(); size="50" name=up_filename class="input"> <INPUT style="BORDER-RIGHT: #cccccc 1px solid; BORDER-TOP: #cccccc 1px solid; BORDER-LEFT: #cccccc 1px solid; CURSOR: hand; BORDER-BOTTOM: #cccccc 1px solid" onclick=FileUp(); type=button value=파일첨부 class="submit1"> &nbsp;<span class="font_orange">*최대 <?=($setup[max_filesize]/1024)?>KB 까지 업로드 가능</span></TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup[board_width]?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
</TR>
<? if ($thisBoard[filename]) { ?>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup[board_width]?>">(<?=$thisBoard[filename]?>)</TD>
</TR>
<TR>
	<TD colspan="2" background="images/table_con_line.gif" width="<?=$setup[board_width]?>"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
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
	<img src="<?=$imgdir?>/butt-ok.gif" bo2rder=0 style="cursor:hand;" onclick="chk_writeForm(document.writeForm);"> &nbsp;&nbsp;
	<IMG SRC="<?=$imgdir?>/butt-cancel.gif" border=0 style="CURSOR:hand" onClick="history.go(-1);">
</div>

<?
}
?>