<?
$num=$_REQUEST["num"];

$qry = "WHERE 1=1 ";
if(strlen($board)>0) $qry.= "AND board='".$board."' ";
if(strlen($s_check)>0 && strlen($search)>0) {
	$orSearch = split(" ",$search);
	// 검색어가 있는경우 쿼리문에 조건추가...........
	switch ($s_check) {
		case "c":
			$qry = "AND (";
			for($oo=0;$oo<count($orSearch);$oo++) {
				if ($oo > 0) {
					$qry .= " OR ";
				}
				$qry .= "title LIKE '%" . $orSearch[$oo] . "%' ";
				$qry .= "OR content LIKE '%" . $orSearch[$oo] . "%' ";
			}
			$qry .= ") ";
			break;
		case "n":
			$qry.= "AND (";
			for($oo=0;$oo<count($orSearch);$oo++) {
				if ($oo > 0) {
					$qry .= " OR ";
				}
				$qry .= "a.name LIKE '%" . $orSearch[$oo] . "%' ";
			}
			$qry .= ") ";
			break;
	}
}

$sql = "SELECT * FROM tblboard ";
$sql.= "WHERE num='".$num."' ";
if(strlen($board)>0) $sql.= "AND board='".$board."' ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_array($result);
mysql_free_result($result);

if(!$row) {
	echo "<script>alert('해당 게시글이 존재하지 않습니다.');history.go(-1);</script>";
	exit;
}

$setup = @mysql_fetch_array(@mysql_query("SELECT * FROM tblboardadmin WHERE board ='".$row[board]."'",get_db_conn()));
if($setup[board_width]<100) $setup[board_width]=$setup[board_width]."%";
if($setup[comment_width]<100) $setup[comment_width]=$setup[comment_width]."%";
if(strlen($setup[notice])>0) {
	$setup[notice]=getTitle($setup[notice]);
	$setup[notice]=getStripHide($setup[notice]);
}
if($setup[use_wrap]=="N") $setup[wrap]="off";
else if($setup[use_wrap]=="Y") $setup[wrap]="on";

$setup[max_filesize] = $setup[max_filesize]*(1024*100);
$setup[btype]=substr($setup[board_skin],0,1);
$setup[title_length]=65;

$setup[page_num] = 10;
$setup[list_num] = 20;

$filepath = $Dir.DataDir."shopimages/board/".$row[board];

if($setup[use_reply]=="N") {
	$reply_start="<!--";
	$reply_end="-->";
}

if($setup[use_lock]=="N") {
	$hide_secret_start="<!--";
	$hide_secret_end="-->";
}

$this_board=$row[board];
$this_num = $row[num];
$this_thread = $row[thread];
$this_pos = $row[pos];
$this_id = $row[id];
$this_comment = $row[total_comment];
$pridx=$row[pridx];

$row[title] = stripslashes($row[title]);
$row[title] = getTitle($row[title]);
$row[title] = getStripHide($row[title]);
$row[name] = getStripHide(stripslashes($row[name]));

if (strlen($row[email])>0) {
	$strName = "<a href='mailto:".$row[email]."' style=\"text-decoration:underline\">".$row[name]." [".$row[email]."]</a>";
} else {
	$strName = "<A style=\"cursor:point;text-decoration:underline\">".$row[name]."</A>";
}

$v_access = $row[access];
$v_vote = $row[vote];

if ($setup[use_lock]=="A" || $setup[use_lock]=="Y") {
	if ($row[is_secret] == "1") {
		$secret_img = "<img src=".$imgdir."/lock.gif border=0 align=absmiddle>";
	} else {
		$secret_img = "";
	}
}

if(strlen($row[filename])>0) {
	unset($file_name1);	//다운로드 링크
	unset($upload_file1);	//이미지 태그

	$attachfileurl=$filepath."/".$row[filename];
	if(file_exists($attachfileurl)) {
		$file_name1=FileDownload($this_board,$row[filename])." (".ProcessBoardFileSize($this_board,$row[filename]).")";

		$ext = strtolower(substr(strrchr($row[filename],"."),1));
		if($ext=="gif" || $ext=="jpg" || $ext=="png") {
			$imgmaxwidth=ProcessBoardFileWidth($this_board,$row[filename]);
			if($setup[img_maxwidth]<$imgmaxwidth) {
				$imgmaxwidth=$setup[img_maxwidth];
			}
			$upload_file1="<img src=\"".ImageAttachUrl($this_board,$row[filename])."\" border=0 width=\"".$imgmaxwidth."\">";
		}
	}
}

$strIp = "IP : ".$row[ip];

$strDate = date("Y/m/d (H:i)",$row[writetime]);
$strSubject = stripslashes($row[title]);
$strSubject = getStripHide($strSubject);
$strSubject = $secret_img.$strSubject;

if ($row[use_html] == "1") {
	$memo = stripslashes($row[content]);
} else {
	$memo = stripslashes(nl2br($row[content]));
}
$strCel = stripslashes($row[usercel]);

$url = $row[url];

$nowblock = $block;
$curpage  = $block * $setup[page_num] + $gotopage;

$sql = "SELECT COUNT(*) as t_count FROM tblboard ".$qry;
$result = mysql_query($sql,get_db_conn());
$row = mysql_fetch_object($result);
$t_count = $row->t_count;
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
mysql_free_result($result);

//comment
if ($setup[use_comment]=="Y" && $this_comment > 0) {
	$com_query = "SELECT * FROM tblboardcomment WHERE board='".$this_board."' ";
	$com_query.= "AND parent = $this_num ORDER BY num ASC ";
	$com_result = @mysql_query($com_query,get_db_conn());
	$com_rows = @mysql_num_rows($com_result);

	if ($com_rows <= 0) {
		@mysql_query("UPDATE tblboard SET total_comment='0' WHERE board='".$this_board."' AND num='".$this_num."'");
	} else {
		unset($com_list);
		while($com_row = mysql_fetch_array($com_result)) {
			$com_list[count($com_list)] = $com_row;
		}
		mysql_free_result($com_result);
	}
}

//윗글
$p_query  = "SELECT num,thread,title,name,email FROM tblboard ".$qry." ";
$p_query .= "AND pos = 0 AND thread < '".$this_thread."' AND deleted != '1' ";
$p_query .= "ORDER BY thread DESC limit 1" ;
$p_result = mysql_query($p_query,get_db_conn());
$p_row = mysql_fetch_array($p_result);
mysql_free_result($p_result);

if (!$p_row[num]) {
	$hide_prev_start = "<!--";
	$hide_prev_end = "-->";
} else {
	$p_row[name] = stripslashes($p_row[name]);
	$prevTitle = getTitle($p_row[title]);
	$prevTitle = getStripHide($prevTitle);

	if ($setup[title_length] > 0) {
		$len_title = $setup[title_length];

		$prevTitle = len_title($prevTitle,$len_title);
	}

	$prevTitle = "<a href='".$_SERVER[PHP_SELF]."?exec=view&num=".$p_row[num]."&board=".$board."&block=".$block."&gotopage=".$gotopage."&search=".$search."&s_check=".$s_check."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전글 : ".$prevTitle."';return true\">".$prevTitle."</a>";
	$prevName = $p_row[name];
	$prevEmail = $p_row[email];

	if (strlen($prevEmail)>0) {
		$prevName = "<a href=mailto:".$prevEmail." onmouseout=\"window.status=''\" onmouseover=\"window.status='".$prevEmail."'; return true\">".$prevName."</a>";
	}
}


//아랫글
$n_query  = "SELECT num,thread,title,name,email FROM tblboard ".$qry." ";
$n_query .= "AND pos = 0 AND thread > '".$this_thread."' AND deleted != '1' ";
$n_query .= "ORDER BY thread limit 1" ;
$n_result = mysql_query($n_query,get_db_conn());
$n_row = mysql_fetch_array($n_result);
mysql_free_result($n_result);

if (!$n_row[num]) {
	$hide_next_start = "<!--";
	$hide_next_end = "-->";
} else {
	$n_row[name] = stripslashes($n_row[name]);
	$nextTitle = getTitle($n_row[title]);
	$nextTitle = getStripHide($nextTitle);

	if ($setup[title_length] > 0) {
		$len_title = $setup[title_length];

		$nextTitle = len_title($nextTitle,$len_title);
	}

	$nextTitle = "<a href='".$_SERVER[PHP_SELF]."?exec=view&num=".$n_row[num]."&board=".$board."&block=".$block."&gotopage=".$gotopage. "&search=".$search."&s_check=".$s_check."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음글 : ".$nextTitle."';return true\">".$nextTitle."</a>";
	$nextName = $n_row[name];
	$nextEmail = $n_row[email];

	if (strlen($nextEmail)>0) {
		$nextEmail = "<a href=mailto:".$nextEmail." onmouseout=\"window.status=''\" onmouseover=\"window.status='".$nextEmail."'; return true\">".$nextName."</a>";
	}
}

//관련답변글 뽑아내는 루틴
if ($setup[use_reply] == "Y") {
	$query2  = "SELECT num, thread, pos, depth, name, email, deleted, title, writetime ";
	$query2 .= "FROM tblboard WHERE board='".$this_board."' ";
	$query2 .= "AND thread = ".$this_thread." ";
	$query2 .= "ORDER BY pos ";
	$result_re = mysql_query($query2, get_db_conn());
	$total_re = mysql_num_rows($result_re);
	if ($total_re == 1) {
		$hide_reply_start = "<!--";
		$hide_reply_end = "-->";
	} else {
		while ($row5 = mysql_fetch_array($result_re)) {
			unset($td_bgcolor);
			if ($num == $row5[num]) {
				$td_bgcolor = $list_mouse_over_color;
			}
			$row5[title] = getTitle($row5[title]);
			$row5[title] = getStripHide($row5[title]);
			$row5[name] = len_title($row5[name], $nameLength);
			$row5[name] = getStripHide($row5[name]);

			$tr_str1 .= "<TR><TD colspan=\"5\" background=\"images/table_con_line.gif\"><img src=\"images/table_con_line.gif\" width=\"4\" height=\"1\" border=\"0\"></TD></TR>";
			if ($row5[deleted] != "1") {
				$tr_str1 .= "<TR style=\"CURSOR:hand;\" onClick=\"location='".$_SERVER[PHP_SELF]."?exec=view&num=".$row5[num]."&board=".$board."&block=".$nowblock."&gotopage=".$gotopage."&search=".$search."&s_check=".$s_check."';\"><TD class=\"board_con1s\" width=30>&nbsp;</TD>";

				$tr_str1 .= "<TD class=\"board_con1s\"><a href='".$_SERVER[PHP_SELF]."?exec=view&num=".$row5[num]."&board=".$board."&search=".$search."&s_check=".$s_check."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='답변글 : ".$row5[title]."';return true\">";
			} else {
				$tr_str1 .= "<TR><TD class=\"board_con1s\" width=30>&nbsp;</TD>";
				$tr_str1 .= "<TD class=\"board_con1s\">";
			}

			$wid = 1;
			$depth = $row5[depth];

			if ($setup[title_length] > 0) {
				$len_title = $setup[title_length];
			}

			if ($depth > 0) {
				if ($depth == 1) {
					$wid = 6;
				} else {
					$wid = (6 * $depth) + (4 * ($depth-1));
				}

				$tr_str1 .= "<img src=".$imgdir."/x.gif width=".$wid." height=2 border=0>";
				$tr_str1 .= "<img src=".$imgdir."/re_mark.gif border=0 align=absmiddle>";

				if ($len_title) {
					$len_title = $len_title - (3 * $depth);
				}
			}

			$title = $row5[title];

			if ($len_title) {
				$title = len_title($title, $len_title);
			}

			$tr_str1 .=  $title;

			if ($row5[deleted] != "1") {
				$tr_str .= "</A>";
			}

			if ($row5[writetime]+(60*60*24)>time()) {
				$tr_str1 .= "&nbsp;<img src=".$imgdir."/icon_new.gif border=0>&nbsp;";
			}

			$tr_str1 .= "</TD>";
			$tr_str1 .= "<TD class=\"board_con1\" align=\"center\">".$row5[name]."</TD>";

			$tr_str1 .= "<TD class=\"board_con1s\" align=\"center\">".date("Y/m/d",$row5[writetime])."</TD>";
			$tr_str1 .= "<TD class=\"board_con1s\" align=\"center\"></TD></tr>";
		}
		mysql_free_result($result_re);
	}
} else {
	$hide_reply_start = "<!--";
	$hide_reply_end = "-->";
	$reply_start = "<!--";
	$reply_end = "-->";
}

if($setup[btype]=="L") {
	if(strlen($pridx)>0 && $pridx>0) {
		if($prqnaboard!=$this_board) $pridx="";
	}
	if(strlen($pridx)>0) {
		$sql = "SELECT productcode,productname,etctype,sellprice,quantity,tinyimage,selfcode FROM tblproduct ";
		$sql.= "WHERE pridx='".$pridx."' ";
		$result=mysql_query($sql,get_db_conn());
		if($_pdata=mysql_fetch_object($result)) {
			INCLUDE "community_article.prqna_top.inc.php";
		} else {
			$pridx="";
		}
		mysql_free_result($result);
	}
}




	if( $num > 0 ) {
		$boardSQL = "SELECT `subCategory`,`vote` FROM `tblboard` WHERE board='".$this_board."' AND num = ".$num;
		$boardResult = mysql_query($boardSQL,get_db_conn());
		$boardRow = mysql_fetch_assoc ($boardResult);
		if( strlen($boardRow['subCategory']) > 0 ) $subCategoryView = "[".$boardRow['subCategory']."]&nbsp;";
	}
?>

<STYLE type=text/css>

	#menuBar {
	}
	#contentDiv {
		WIDTH: 690;
	}
</STYLE>
<script>
function check_del(url) {
	if(confirm("삭제 하시겠습니까?")) {
		document.location.href=url;
	}
}


function saveAdminComm ( no ) {
	var f = eval("document.adminComentsForm_"+no);
	if( f.adminComm.value == '' ) {
		alert('내용을 입력하세요!');
		f.adminComm.focus();
		return false;
	}
	f.method="POST";
	f.submit();

}
</script>

<table border=0 cellpadding=0 cellspacing=1 width="100%">
<tr>
	<td height=15 style="padding-left:5"><B>[<?=$setup[board_name]?>]</B></td>
	<td align=right class="board_con1s"><?=$strIp?></td>
</tr>
</table>
<TABLE cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td width="100%">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TR>
		<TD background="images/table_top_line1.gif" colspan="4" width="762"><img src=img/table_top_line1.gif height=2></TD>
	</TR>
	<TR>
		<TD class="board_cell1" align="center" width="50"><p align="center">글제목</TD>
		<TD class="board_cell1" align="center" width="683" colspan="3"><p align="left"><B><span class="font_orange"><?=$subCategoryView?><?=$strSubject?></span></B></TD>
	</TR>
	<TR>
		<TD colspan="4" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
	</TR>
	<TR>
		<TD align="center" height="30" width="50" class="board_con1s"><p align="center">글쓴이</TD>
		<TD align="center" height="30" width="50%" class="board_con1"><p align="left"><A href="cooperation_board_view.php"><B><?=$strName?></B></A></TD>
		<TD align="center" height="30" class="board_con1s">작성일</TD>
		<TD align="center" height="30" width="231" class="board_con1"><?=$strDate?></TD>
	</TR>
	<TR>
		<TD colspan="4" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
	</TR>
	<TR>
		<TD class="board_con1s" align="center" width="50"><p align="center">첨부파일</TD>
		<TD class="board_con1" align="center" width="50%">
		<? if ($file_name1) { ?>
		<TABLE border=0 cellpadding=3 cellspacing=0 width=100%>
		<TR>
			<TD width=20></TD>
			<TD style='word-break:break-all;'>다운로드 : <?=$file_name1?></TD>
			<TD align=right></TD>
		</TR>
		</TABLE>
		<? } ?>
		</TD>
		<TD class="board_con1s" align="center">조회수</TD>
		<TD class="board_con1" align="center" width="231"><?=$v_access?></TD>
	</TR>
	<TR>
		<TD colspan="4" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
	</TR>
	<TR>
		<TD align="center" width="50" class="board_con1s">휴대전화</TD>
		<TD align="center" height="30" class="board_con1" colspan="3"><p align="left"><?=$strCel?></p></TD>
	</TR>
	<TR>
		<TD colspan="4" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
	</TR>



	<?
		if( $setup[linkboard] AND strlen($url) > 0 ) {
	?>
	<TR>
		<TD align="center" width="50" class="board_con1s">URL</TD>
		<TD align="center" height="30" class="board_con1" colspan="3"><p align="left"><a href="<?=$url?>" target="BLANK"><?=$url?></a></p></TD>
	</TR>
	<TR>
		<TD colspan="4" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
	</TR>


	<?
		}
	?>



	<TR>
		<TD class="board_con1" width="753" colspan="4">
		<table cellpadding="0" cellspacing="0" width="100%" height="300">
		<tr>
			<td valign="top">
			<DIV class=MsgrScroller id=contentDiv style="OVERFLOW-x: auto; OVERFLOW-y: hidden">
			<DIV id=bodyList>
			<TABLE border=0 cellspacing=0 cellpadding=10 style="table-layout:fixed">
			<TR>
				<TD style='word-break:break-all;' bgcolor=#ffffff valign=top>
				<?if ($upload_file1) {?>
				<span style="width:100%;line-height:160%;text-align:<?=$setup[img_align]?>">
				<?=$upload_file1?>
				</span>
				<?}?>

				<span style="width:100%;line-height:160%;">
				<?=$memo?>
				</span>
				</TD>
			</TR>
			</TABLE>
			</DIV>
			</DIV>
			</td>
		</tr>
		</table>
		</TD>
	</TR>
	<TR>
		<TD colspan="4" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
	</TR>
	</TABLE>
	</td>
</TR>





<TR>
	<TD bgcolor=#FFFFFF>
		<!-- 버튼 관련 출력 -->
		<TABLE border=0 cellspacing=0 cellpadding=0 width="100%">
			<TR>
				<TD WIDTH="100%" height="60"><p align="right">
						<?=$reply_start?><A HREF="<?=$_SERVER[PHP_SELF]?>?exec=reply&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>"><IMG SRC="<?=$imgdir?>/butt-reply.gif" border=0></A><?=$reply_end?>

						<A HREF="<?=$_SERVER[PHP_SELF]?>?exec=modify&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>"><IMG SRC="<?=$imgdir?>/butt-modify.gif" border=0></A>

						<A HREF="<?=$_SERVER[PHP_SELF]?>?exec=delete&board=<?=$board?>&num=<?=$num?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$block?>&gotopage=<?=$gotopage?>"><IMG SRC="<?=$imgdir?>/butt-delete.gif" border=0></A>

						<A HREF="<?=$_SERVER[PHP_SELF]?>?exec=write&board=<?=$board?>"><IMG SRC="<?=$imgdir?>/butt-write.gif" border=0></A>

						<A HREF="<?=$_SERVER[PHP_SELF]?>?board=<?=$board?>&s_check=<?=$s_check?>&search=<?=$search?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>"><IMG SRC="<?=$imgdir?>/butt-list.gif" border=0></A>

				</td>
			</TR>
		</TABLE>
	</TD>
</TR>








<TR>
	<TD width="100%">
<?
	if ($setup[use_comment] == "Y") {
		echo "<script>\n";
		echo "function chkCommentForm() {\n";
		echo "	if (!comment_form.up_name.value) {\n";
		echo "		alert('이름을 입력 하세요.');\n";
		echo "		comment_form.up_name.focus();\n";
		echo "		return;\n";
		echo "	}\n";
		echo "	if (!comment_form.up_comment.value) {\n";
		echo "		alert('내용을 입력 하세요.');\n";
		echo "		comment_form.up_comment.focus();\n";
		echo "		return;\n";
		echo "	}\n";
		echo "	comment_form.mode.value='comment_result';\n";
		echo "	comment_form.submit();\n";
		echo "}\n";
		echo "</script>\n";
		echo "<TABLE cellSpacing=0 cellPadding=0 width=\"100%\">\n";
		echo "<form method=post name=comment_form action=\"".$_SERVER[PHP_SELF]."\">\n";
		echo "<input type=hidden name=exec value=\"".$exec."\">\n";
		echo "<input type=hidden name=board value=\"".$board."\">\n";
		echo "<input type=hidden name=num value=\"".$this_num."\">\n";
		echo "<input type=hidden name=block value=\"".$block."\">\n";
		echo "<input type=hidden name=gotopage value=\"".$gotopage."\">\n";
		echo "<input type=hidden name=search value=\"".$search."\">\n";
		echo "<input type=hidden name=s_check value=\"".$s_check."\">\n";
		echo "<input type=hidden name=mode>\n";
		echo "<TR>\n";
		echo "	<TD>\n";
		echo "	<TABLE cellSpacing=0 cellPadding=4 width=\"100%\">\n";
		echo "	<TR>\n";
		echo "		<TD class=tk1 width=581 bgColor=#fafafa colSpan=2>	&nbsp;작성자 : <INPUT class=\"input\" maxLength=\"20\" size=\"15\" name=\"up_name\" /></TD>\n";
		echo "	</TR>\n";
		echo "	<TR align=middle>\n";
		echo "		<TD align=left width=\"100%\" bgColor=#fafafa><TEXTAREA class=input style=\"PADDING-RIGHT: 5pt; PADDING-LEFT: 5pt; PADDING-BOTTOM: 5pt; WIDTH: 100%; PADDING-TOP: 5pt; HEIGHT: 70px\" name=up_comment></TEXTAREA></TD>\n";
		echo "		<TD align=right width=\"72\" bgColor=#fafafa><A href=\"javascript:chkCommentForm();\"><IMG height=69 src=\"images/comment.gif\" width=72 border=0></A></TD>\n";
		echo "	</TR>\n";
		echo "	</TABLE>\n";
		echo "	</TD>\n";
		echo "</TR>\n";
		echo "</FORM>\n";

		/*
		if ($setup[use_comment] == "Y") {
			echo "<BR>\n";
			echo "<TABLE CELLSPACING=0 cellpadding=0 border=0 style=\"TABLE-LAYOUT:FIXED\">\n";
			echo "	<TR> \n";
			echo "		<TD HEIGHT=\"20\">▣ <b>댓글 쓰기</b> <현재 <b><font color=\"#4499DD\">".$this_comment."</font></b>건></TD>\n";
			echo "	</TR>\n";
			echo "</TABLE>\n";
			echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 STYLE=\"TABLE-LAYOUT:FIXED\">\n";
			echo "	<TR HEIGHT=1 BGCOLOR=".$setup[title_color]."><TD></TD></TR>\n";
			echo "</TABLE>\n";
		}
		*/

		for ($jjj=0;$jjj<count($com_list);$jjj++) {
			$c_num = $com_list[$jjj][num];
			$c_name = $com_list[$jjj][name];

			$c_uip=$com_list[$jjj][ip];

			unset($comUserId);

			$c_writetime = date("Y-m-d H:i:s",$com_list[$jjj][writetime]);
			$c_comment = nl2br(stripslashes($com_list[$jjj][comment]));
			$c_ip = $com_list[$jjj][ip];
			$c_comment = getStripHide($c_comment);

			echo "<TR>\n";
			echo "	<TD>\n";
			echo "	<TABLE cellSpacing=0 cellPadding=0 width=\"100%\">\n";
			echo "	<TR><TD background=\"images/bbs_line1.gif\"></TD></TR>\n";
			echo "	<TR><TD height=25></TD></TR>\n";
			echo "	<TR>\n";
			echo "		<TD class=tk1 width=\"760\" height=22><B><span class=\"font_blue\">".$c_name."</span></B> / <span class=\"board_con1s\">".$c_writetime." (".$c_ip.")</span> <A style=\"CURSOR:hand;\" onclick=\"check_del('".$_SERVER[PHP_SELF]."?mode=comment_del&board=".$board."&num=".$num."&c_num=".$c_num."&s_check=".$s_check."&search=".$search."&block=".$block."&gotopage=".$gotopage."')\"><IMG SRC=\"".$imgdir."/del_x.gif\" width=27 border=0 align=\"absmiddle\" vspace=\"4\" alt=\"삭제\"></A></TD>\n";
			echo "	</TR>\n";
			echo "	<TR>\n";
			echo "		<TD style='word-break:break-all;' class=tk1 width=\"760\" height=22>".$c_comment."</TD>\n";
			echo "	</TR>\n";
			echo "	<TR>\n";
			echo "		<TD width=\"760\" height=5></TD>\n";
			echo "	</TR>\n";


			// 관리자 코멘트의 댓글
			echo "
						<TR>
							<TD style=\"border:1px solid #f5f5f5; background:#f9f9f9;\" style=\"padding:5px 15px;\">
			";


			$adminCommSQL = "SELECT * FROM `tblboardcomment_admin` WHERE `board` = '".$setup[board]."' AND `board_no`= '".$num."' AND `comm_no`= '".$c_num."' ORDER BY `idx` ASC";
			$adminCommResult = mysql_query( $adminCommSQL );
			$adminCommNums = mysql_num_rows($adminCommResult);
			if($adminCommNums > 0) {
				echo "		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-bottom:1px solid #eeeeee;\">";
				while( $adminCommRow = mysql_fetch_assoc ( $adminCommResult ) ) {
					echo "
									<tr>
										<td>
											<div style=\"float:left; width:15px; margin-top:5px;\"><img src=\"".$imgdir."/icon_reply.gif\" /></div>
											<div style=\"float:left; font-size:11px;\"><strong>관리자</strong> / <span class=\"board_con1s\">".$adminCommRow['reg_date']."</span>
											<A style=\"CURSOR:hand;\" onclick=\"check_del('".$_SERVER[PHP_SELF]."?mode=comment_admin_del&delidx=".$adminCommRow['idx']."&board=".$setup[board]."&num=".$num."&c_num=".$c_num."&s_check=".$s_check."&search=".$search."&block=".$block."&gotopage=".$gotopage."')\"><IMG SRC=\"".$imgdir."/del_x.gif\" width=27 border=0 align=\"absmiddle\" vspace=\"4\" alt=\"삭제\"></A><br />
											<span style=\"font-size:11px;\">".$adminCommRow['comment']."</span></div>
										</td>
									</tr>
									<tr><td colspan=2 height=\"5\"></td></tr>
					";
				}
				echo "		</table>";
			}

			echo "
							<form style=\"margin:0px; padding:0px;\" name='adminComentsForm_".$c_num."'>
							<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\"\">
								<tr><td colspan=3 height=\"5\"></td></tr>
								<tr>
									<td width=\"100\" align=\"center\" style=\"font-size:11px;\">관리자 답변</td>
									<td><textarea name='adminComm' style=\"width:100%; height:69px;\"></textarea></td>
									<td width=\"100\" align=\"right\"><img src=\"".$imgdir."/adminComents.gif\" alt=\"관리자 답변 저장\" style=\"cursor:pointer;\" onclick=\"saveAdminComm( '".$c_num."' );\"></td>
								</tr>
							</table>
							<input type=\"hidden\" name=\"exec\" value=\"".$exec."\">
							<input type=\"hidden\" name=\"num\" value=\"".$num."\">
							<input type=\"hidden\" name=\"board\" value=\"".$setup[board]."\">
							<input type=\"hidden\" name=\"block\" value=\"".$block."\">
							<input type=\"hidden\" name=\"gotopage\" value=\"".$gotopage."\">
							<input type=\"hidden\" name=\"search\" value=\"".$search."\">
							<input type=\"hidden\" name=\"s_check\" value=\"".$s_check."\">
							<input type=\"hidden\" name=\"c_num\" value=\"".$c_num."\">
							<input type=\"hidden\" name=\"mode\" value=\"saveAdminComm\">
							</form>
						</TD>
					</TR>
			";


			echo "	<TR>\n";
			echo "		<TD width=\"760\" height=5></TD>\n";
			echo "	</TR>\n";
			echo "	</TABLE>\n";
			echo "	</TD>\n";
			echo "</TR>\n";
		}
		echo "<TR>\n";
		echo "	<TD width=\"760\" background=\"images/bbs_line1.gif\"></TD>\n";
		echo "</TR>\n";
		echo "<TR>\n";
		echo "	<td></td>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
	}
?>
	</TD>
</TR>



<TR>
	<td width="100%"><p>&nbsp;</p></td>
</TR>
<?=$hide_reply_start?>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><p><img src="images/community_article_reply.gif" width="129" height="28" border="0"></p></td>
	</tr>
	<tr>
		<td>
		<table border="0" cellspacing="2" width="100%" bgcolor="#0099CC">
		<tr>
			<td bgcolor="white">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<TR>
				<TD class="board_cell1" align="center"></TD>
				<TD class="board_cell1" align="center" width="465"><p align="center">글제목</TD>
				<TD class="board_cell1" align="center">글쓴이</TD>
				<TD class="board_cell1" align="center">작성일</TD>
				<TD class="board_cell1" align="center"></TD>
			</TR>
			<?=$tr_str1?>
			</TABLE>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</TD>
</TR>
<?=$hide_reply_end?>
<tr>
	<td width="100%"><p>&nbsp;</p></td>
</tr>
<tr>
	<td width="100%">
	<table cellpadding="0" cellspacing="0" width="100%">
	<TR>
		<TD>
		<TABLE cellSpacing=0 cellPadding=0 width="100%">
		<?if (!$hide_prev_start || !$hide_next_start) {?>
		<TR>
			<TD width=600 background="images/bbs_line1.gif" colSpan="2"></TD>
		</TR>
		<?}?>
		<?=$hide_prev_start?>
		<TR onClick="location='<?=$_SERVER[PHP_SELF]?>?exec=view&board=<?=$board?>&num=<?=$p_row[num]?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>';">
			<TD align=right width=71 height=27><IMG height=14 src="images/bbs_pre.gif" width=62 border=0></TD>
			<TD width="671"><?=$prevTitle?></TD>
		</TR>
		<?=$hide_prev_end?>
		<?=$hide_next_start?>
		<TR onClick="location='<?=$_SERVER[PHP_SELF]?>?exec=view&board=<?=$board?>&num=<?=$n_row[num]?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>';">
			<TD align=right width=71 height=27><IMG height=14 src="images/bbs_next.gif" width=62 border=0></TD>
			<TD width="688"><?=$nextTitle?></TD>
		</TR>
		<?=$hide_next_end?>
		<?if (!$hide_prev_start || !$hide_next_start) {?>
		<TR>
			<TD width=600 background="images/bbs_line1.gif" colSpan="2"></TD>
		</TR>
		<?}?>
		</TABLE>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<BR><BR>