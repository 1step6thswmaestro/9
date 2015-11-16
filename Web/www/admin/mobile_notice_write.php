<?
$mode=$_REQUEST["mode"];
$exec=$_REQUEST["exec"];
$up_board=$_POST["up_board"];

if(strlen($up_board)==0) {
?>
	<BR>
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
	<input type=hidden name=exec value="<?=$_REQUEST["exec"]?>">
	<input type=hidden name=board value="<?=$_REQUEST["board"]?>">
	<tr>
		<td align=center>
		&nbsp;게시판 선택 : 
		<select name=up_board class="select">
		<option value="">게시판을 선택하세요</option>
<?
		$sql = "SELECT * FROM tblboardadmin ORDER BY date ASC ";
		$result=mysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=mysql_fetch_object($result)) {
			$cnt++;
			if($board==$row->board) {
				echo "<option value=\"".$row->board."\" selected>".$row->board_name."</option>\n";
				$one_notice=$row->notice;
			} else {
				echo "<option value=\"".$row->board."\">".$row->board_name."</option>\n";
			}
		}
		mysql_free_result($result);
?>
		</select>		
		</td>
	</tr>
	<tr><td height=10></td></tr>
	<tr>
		<td align=center>
		게시글 작성을 하기 위해서는 해당 게시판을 선택하셔야 합니다.
		</td>
	</tr>
	<tr><td height=10></td></tr>
	<tr>
		<td align=center>
		<A HREF="javascript:check_form();"><img src="<?=$imgdir?>/butt-ok.gif" border=0></A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<A HREF="javascript:history.go(-1)"><IMG SRC="<?=$imgdir?>/butt-cancel.gif" border=0></A>
		</td>
	</tr>
	</form>

	<script>
	function check_form() {
		if(document.form1.up_board.value.length==0) {
			alert("게시판을 선택하세요.");
			document.form1.up_board.focus();
			return;
		}
		document.form1.submit();
	}
	</script>

	</table>
<?
} else {
	$setup = @mysql_fetch_array(@mysql_query("SELECT * FROM tblboardadmin WHERE board ='".$up_board."'",get_db_conn()));
	$setup[max_filesize] = $setup[max_filesize]*(1024*100);
	$setup[btype]=substr($setup[board_skin],0,1);
	if(strlen($setup[board])==0) {
		echo "<html><head><title></title></head><body onload=\"alert('해당 게시판이 존재하지 않습니다.');history.go(-1);\"></body></html>";exit;
	}

	if($setup[use_lock]=="N") {
		$hide_secret_start="<!--";
		$hide_secret_end="-->";
	}

	if(($_POST[mode]=="up_result") && ($_POST[ins4e][mode]=="up_result") && ($_POST[up_subject]!="") && ($_POST[ins4e][up_subject]!="")) {
		if(!eregi($_SERVER[HTTP_HOST],$_SERVER[HTTP_REFERER])) {
			$errmsg="잘못된 경로로 접근하셨습니다.";
			echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
		}

		$thread = $setup[thread_no] - 1;
		if ($thread<=0) {
			$que2 = "SELECT MIN(thread) FROM tblboard ";
			$result = mysql_query($que2,get_db_conn());
			$row = mysql_fetch_array($result);
			if ($row[0]<=0) {
				$thread = 999999999;
			} else {
				$thread = $row[0] - 1;
			}
			mysql_free_result($result);
		}

		//해당 쇼핑몰 모든 게시판 thread값 동일하게 업데이트 (통합되어 보여질 때 유일thread값을 갖게하기 위하여)
		@mysql_query("UPDATE tblboardadmin SET thread_no='".$thread."' ",get_db_conn());

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
		$up_memo = addslashes($up_memo);
		$up_filename=$_POST["up_filename"];
		$up_is_secret=$_POST["up_is_secret"];
		if (!$up_is_secret) $up_is_secret = 0;
		$up_passwd=$_POST["up_passwd"];
		$up_email=$_POST["up_email"];
		$up_html=$_POST["up_html"];

		$next_no = $setup[max_num];

		if (!$next_no) {
			$que3 = "SELECT MAX(num) FROM tblboard WHERE board='".$up_board."' AND pos=0 AND deleted!='1'";
			$result3 = mysql_query($que3,get_db_conn());
			$row3 = mysql_fetch_array($result3);
			@mysql_free_result($result3);
			$next_no = $row3[0];

			if (!$next_no) $next_no = 0;
		}

		if(ProcessBoardFileIn($up_board,$up_filename)!="SUCCESS") {
			$up_filename="";
		}

		$sql  = "INSERT tblboard SET ";
		$sql .= "board				= '".$up_board."', ";
		$sql .= "num				= '', ";
		$sql .= "thread				= '".$thread."', ";
		$sql .= "pos				= '0', ";
		$sql .= "depth				= '0', ";
		$sql .= "prev_no			= '0', ";
		$sql .= "next_no			= '".$next_no."', ";
		$sql .= "name				= '".$up_name."', ";
		$sql .= "passwd				= '".$up_passwd."', ";
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

			if ($next_no) {
				$qry9 = "SELECT thread FROM tblboard WHERE board='".$up_board."' AND num='".$next_no."' ";
				$res9 = mysql_query($qry9,get_db_conn());
				$next_thread = mysql_fetch_row($res9);
				@mysql_free_result($res9);
				mysql_query("UPDATE tblboard SET prev_no='".$thisNum."' WHERE board='".$up_board."' AND thread = '".$next_thread[0]."'",get_db_conn());

				mysql_query("UPDATE tblboard SET prev_no='".$thisNum."' WHERE board='".$up_board."' AND num = '".$next_no."'",get_db_conn());
			}

			// ===== 관리테이블의 게시글수 update =====
			$sql3 = "UPDATE tblboardadmin SET total_article=total_article+1, max_num='".$thisNum."' ";
			$sql3.= "WHERE board='".$up_board."' ";
			$update = mysql_query($sql3,get_db_conn());

			echo("<meta http-equiv='Refresh' content='0; URL=".$_SERVER[PHP_SELF]."?board=$board'>");
			exit;
		} else {
			echo "
				<script>
				window.alert('글쓰기 입력중 오류가 발생하였습니다.');
				</script>
			";
			reWriteForm();
			exit;
		}
	} else {
		if ($mode == "reWrite") {
			$thisBoard=$_REQUEST["thisBoard"];
			$thisBoard[content]  = stripslashes(urldecode($thisBoard[content]));
			$thisBoard[title]  = stripslashes(urldecode($thisBoard[title]));
			$thisBoard[name]  = stripslashes(urldecode($thisBoard[name]));
		} else if (!$_REQUEST["mode"]) {
			$thisBoard[name] = $member[name];
			$thisBoard[email] = $member[email];
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
<input type=hidden name=num value=<?=$num?>>
<input type=hidden name=board value=<?=$board?>>
<input type=hidden name=up_board value=<?=$up_board?>>
<input type=hidden name=s_check value=<?=$s_check?>>
<input type=hidden name=search value=<?=$search?>>
<input type=hidden name=block value=<?=$block?>>
<input type=hidden name=gotopage value=<?=$gotopage?>>
<input type=hidden name=pos value="<?=$thisBoard[pos]?>">
<input type=hidden name=up_is_secret value="<?=$thisBoard[is_secret]?>">

<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
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
<TR>
	<TD class="board_cell1" width="111"><p align="center">글내용</p></TD>
	<TD class="td_con1" width="627">
	<?=$hide_html_start?>
	<B>HTML편집</B><INPUT style="BORDER-RIGHT: #dfdfdf 1px solid; BORDER-TOP: #dfdfdf 1px solid; BORDER-LEFT: #dfdfdf 1px solid; BORDER-BOTTOM: #dfdfdf 1px solid" type=checkbox name=up_html value="1" <?=$thisBoard[use_html]?>><br>
	<?=$hide_html_start?>
	<TEXTAREA style="WIDTH: 100%; HEIGHT: 280px" name=up_memo class="textarea" wrap=<?=$setup[wrap]?>><?=$thisBoard[content]?></TEXTAREA>
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
}
?>