<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
$qry = "WHERE 1=1 ";
if(strlen($board)>0) $qry.= "AND a.board='".$board."' ";
if(strlen($s_check)>0 && strlen($search)>0) {
	$orSearch = split(" ",$search);
	// 검색어가 있는경우 쿼리문에 조건추가...........
	switch ($s_check) {
		case "c":
			$qry .= "AND (";
			for($oo=0;$oo<count($orSearch);$oo++) {
				if ($oo > 0) {
					$qry .= " OR ";
				}
				$qry .= "a.title LIKE '%" . $orSearch[$oo] . "%' ";
				$qry .= "OR a.content LIKE '%" . $orSearch[$oo] . "%' ";
			}
			$qry .= ") ";
			break;
		case "n":
			$qry .= "AND (";
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

$sql = "SELECT COUNT(*) as t_count FROM tblboard AS a ".$qry;
$result = mysql_query($sql,get_db_conn());
$row = mysql_fetch_object($result);
$t_count = $row->t_count;
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
mysql_free_result($result);

$colspan=7;
?>
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=get>
<col width="6%"></col>
<col width="7%"></col>
<col></col>
<col width="6%"></col>
<col width="12%"></col>
<col width="10%"></col>
<col width="8%"></col>
<tr>
	<td colspan="<?=$colspan?>" width="100%" class="board_con1s">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="font_size">게시판 목록 : <select name=board onchange="this.form.submit();" class="select">
		<option value="">게시판 전체</option>
<?
		unset($badmin);
		$sql = "SELECT * FROM tblboardadmin ORDER BY date ASC ";
		$result=mysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=mysql_fetch_object($result)) {
			$cnt++;
			$badmin[$row->board]=$row;
			if($board==$row->board) {
				echo "<option value=\"".$row->board."\" selected>".$row->board_name."</option>\n";
				$one_notice=$row->notice;
			} else {
				echo "<option value=\"".$row->board."\">".$row->board_name."</option>\n";
			}
		}
		mysql_free_result($result);
?>
		</select></td>
		<td align="right" class="font_size"><img src="images/icon_8a.gif" border="0">전체 <FONT class="TD_TIT4_B"><B><?= $t_count ?></B></FONT>건 조회 <img src="images/icon_8a.gif" border="0">현재 <B><?=$gotopage?></B>/<B><?=ceil($t_count/$setup[list_num])?></B> 페이지</td>
	</tr>
	</table>
	</td>
</tr>
</form>
<TR>
	<TD background="images/table_top_line1.gif" colspan="<?=$colspan?>" width="762"><img src="images/table_top_line1.gif" height="2"></TD>
</TR>
<TR align="center">
	<TD class="board_cell1">선택</TD>
	<TD class="board_cell1">NO</TD>
	<TD class="board_cell1">글제목</TD>
	<TD class="board_cell1">파일</TD>
	<TD class="board_cell1">글쓴이</TD>
	<TD class="board_cell1">작성일</TD>
	<TD class="board_cell1">조회수</TD>
</TR>
<TR>
	<TD height="1" colspan="<?=$colspan?>" background="images/table_con_line.gif"></TD>
</TR>

<form name=changeForm method=post>
<?
$sql = "SELECT a.*, b.productcode,b.productname,b.etctype,b.sellprice,b.quantity,b.tinyimage ";
$sql.= "FROM tblboard a LEFT OUTER JOIN tblproduct b ";
$sql.= "ON a.pridx=b.pridx ";
$sql.= "".$qry;
$sql.= "ORDER BY a.thread ,a.pos LIMIT ".($setup[list_num]*($gotopage - 1)).",".$setup[list_num];
$result=mysql_query($sql,get_db_conn());
$cnt=0;
while($row=mysql_fetch_object($result)) {
	echo "<tr><td colspan=".$colspan." height=1 bgcolor=#F0F0F0></td></tr>\n";

	$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);

	$row->title = stripslashes($row->title);
	$row->title = strip_tags($row->title);
	$row->title=getTitle($row->title);
	$row->title=getStripHide($row->title);
	$row->name = stripslashes(strip_tags($row->name));
	$deleted = $row->deleted;

	unset($prview_img);
	if($prqnaboard==$row->board) {
		if(strlen($row->pridx)>0 && $row->pridx>0 && strlen($row->productcode)>0) {
			$prview_img="<A HREF=\"http://".$shopurl."?productcode=".$row->productcode."\" target=\"_blank\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=".$imgdir."/btn_prview.gif border=0 align=absmiddle></A>";
		}
	}

	unset($subject);
	$depth = $row->depth;
	$len_title = 55;
	$wid = 1;
	if ($depth > 0) {
		if ($depth == 1) {
			$wid = 6;
		} else {
			$wid = (6 * $depth) + (4 * ($depth-1));
		}
		$subject .= "<img src=\"".$imgdir."/x.gif\" width=\"".$wid."\" height=\"2\" border=\"0\">";
		$subject .= "<img src=\"".$imgdir."/re_mark.gif\" border=\"0\">";
		if ($len_title) {
			$len_title = $len_title - (3 * $depth);
		}
	}
	$subject .= "<a href=\"".$_SERVER[PHP_SELF]."?exec=view&num=".$row->num."&board=".$board."&block=".$nowblock."&gotopage=".$gotopage."&search=".$search."&s_check=".$s_check."\">";
	$title = $row->title;
	if ($len_title) {
		$title = len_title($title, $len_title);
	}
	$subject .=  $title;
	$subject .= "</a>";
	if ($row->writetime+(60*60*24)>time()) {
		$subject .= "&nbsp;<img src=\"".$imgdir."/icon_new.gif\" border=\"0\" align=\"absmiddle\">&nbsp;";
	}
	unset($secret_img);
	//공개/비공개
	if ($badmin[$row->board]->use_lock=="A" || $badmin[$row->board]->use_lock=="Y") {
		if ($row->is_secret == "1") {
			$secret_img = "<img src=\"".$imgdir."/lock.gif\" border=\"0\" align=\"absmiddle\">";
		} else {
			$secret_img = "";
		}
	}

	if ($badmin[$row->board]->use_comment=="Y" && $row->total_comment > 0) {
		$subject .= " <img src=\"".$imgdir."/icon_memo.gif\" border=\"0\">&nbsp;<font style=\"font-size:8pt;font-family:Tahoma;font-weight:normal\">(<font color=\"red\">".$row->total_comment."</font>)</font>";
	}

	$comment_tot = $row->total_comment;
	$user_name = $row->name;
	$str_name = $user_name;

	$reg_date = date("Y/m/d",$row->writetime);
	$hit = $row->access;

	if($row->filename && ($deleted != "1")) {
		$file_name = strtolower(substr(strrchr($row->filename,"."),1));
		if($file_name == zip || $file_name == arj || $file_name == arj || $file_name == gz || $file_name == tar) {
			$file_icon = "compressed.gif";
		} elseif ($file_name == rar) {
			$file_icon = "ra.gif";
		} elseif ($file_name == exe) {
			$file_icon = "exe.gif";
		} elseif($file_name == gif) {
			$file_icon = "gif.gif";
		} elseif($file_name == jpg || $file_name == jpeg) {
			$file_icon = "jpg.gif";
		} elseif($file_name == mpeg || $file_name == mpg || $file_name == asf || $file_name == avi || $file_name == swf) {
			$file_icon = "movie.gif";
		} elseif($file_name == mp3 || $file_name == rm || $file_name == ram) {
			$file_icon = "sound.gif";
		}elseif($file_name == pdf) {
			$file_icon = "pdf.gif";
		} elseif($file_name == ppt) {
			$file_icon = "ppt.gif";
		} elseif($file_name == doc) {
			$file_icon = "doc.gif";
		} elseif($file_name == hwp) {
			$file_icon = "hwp.gif";
		} else {
			$file_icon = "txt.gif";
		}
		$file_icon = "<IMG SRC=\"".$file_icon_path."/".$file_icon."\" border=0>";
	} else {
		$file_icon = "-";
	}

	$subCategoryView = "";

	if( $row->num > 0 ) {
		$boardSQL = "SELECT `subCategory`,`vote` FROM `tblboard` WHERE board='".$row->board."' AND num = ".$row->num;
		$boardResult = mysql_query($boardSQL,get_db_conn());
		$boardRow = mysql_fetch_assoc ($boardResult);
		if( strlen($boardRow['subCategory']) > 0 ) $subCategoryView = "[".$boardRow['subCategory']."]&nbsp;";
	}

	echo "<TR align=\"center\" height=\"30\">\n";
	echo "	<TD class=\"board_con1s\"><input type=checkbox name=cart[] value=\"".$row->num."".$row->thread."\"></td>\n";
	echo "	<TD class=\"board_con1s\">".$number."</TD>\n";
	echo "	<TD align=\"left\" class=\"board_con1\" style=\"word-break:break-all;padding-left:5px;\">".$subCategoryView."".$secret_img." ".$subject." ".$prview_img."</TD> \n";
	echo "	<TD class=\"board_con1s\">".$file_icon."</TD>\n";
	echo "	<TD class=\"board_con1\" nowrap>".$str_name."</TD>\n";
	echo "	<TD class=\"board_con1s\">".$reg_date."</TD>\n";
	echo "	<TD class=\"board_con1s\">".$hit."</TD>\n";
	echo "</TR>\n";

	$cnt++;
}
mysql_free_result($result);

if ($cnt==0) {
	echo "<tr><td height=\"30\" colspan=\"".$colspan."\" align=\"center\">조건에 맞는 내역이 존재하지 않습니다.</td></tr>";
}
echo "<tr><td height=\"1\" colspan=\"".$colspan."\" bgcolor=\"#F0F0F0\"></td></tr>\n";

$total_block = intval($pagecount / $setup[page_num]);

if (($pagecount % $setup[page_num]) > 0) {
	$total_block = $total_block + 1;
}

$total_block = $total_block - 1;

if (ceil($t_count/$setup[list_num]) > 0) {
	// 이전	x개 출력하는 부분-시작
	$a_first_block = "";
	if ($nowblock > 0) {
		$a_first_block .= "<a href=\"javascript:GoPage(0,1);\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><IMG src=\"images/icon_first.gif\" border=0 align=\"absmiddle\"></a>&nbsp;&nbsp;";

		$prev_page_exists = true;
	}

	$a_prev_page = "";
	if ($nowblock > 0) {
		$a_prev_page .= "<a href=\"javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\">[prev]</a>&nbsp;&nbsp;";

		$a_prev_page = $a_first_block.$a_prev_page;
	}

	// 일반 블럭에서의 페이지 표시부분-시작

	if (intval($total_block) <> intval($nowblock)) {
		$print_page = "";
		for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
			if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
				$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
			} else {
				$print_page .= "<a href=\"javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
			}
		}
	} else {
		if (($pagecount % $setup[page_num]) == 0) {
			$lastpage = $setup[page_num];
		} else {
			$lastpage = $pagecount % $setup[page_num];
		}

		for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
			if (intval($nowblock*$setup[page_num]) + $gopage == intval($gotopage)) {
				$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
			} else {
				$print_page .= "<a href=\"javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
			}
		}
	}		// 마지막 블럭에서의 표시부분-끝


	$a_last_block = "";
	if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
		$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
		$last_gotopage = ceil($t_count/$setup[list_num]);

		$a_last_block .= "&nbsp;&nbsp;<a href=\"javascript:GoPage(".$last_block.",".$last_gotopage.");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><IMG src=\"images/icon_last.gif\" border=0 align=\"absmiddle\" width=\"17\" height=\"14\"></a>";

		$next_page_exists = true;
	}

	// 다음 10개 처리부분...

	$a_next_page = "";
	if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
		$a_next_page .= "&nbsp;&nbsp;<a href=\"javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");\" onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

		$a_next_page = $a_next_page.$a_last_block;
	}
} else {
	$print_page = "<B>[1]</B>";
}
?>

<tr>
	<td colspan="<?=$colspan?>">
	<TABLE width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
	<TR>
		<TD>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<TD><input type="checkbox" id="allcheck" name="allcheck" value="0" onClick="allcheck2()"> 전체선택 <img src="<?=$imgdir?>/btn_del.gif" border="0" align="absmiddle" style="cursor:hand;" onClick="return changeListView('delete');"></TD>
			<TD align="right"><A HREF="<?=$_SERVER[PHP_SELF]?>?board=<?=$board?>&exec=write"><IMG SRC="<?=$imgdir?>/btn_write.gif" border="0" vspace="3"></A></TD>
		</tr>
		</table>
		</td>
	</tr>
	<TR>
		<TD align="center" class="font_size"><?=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page?></TD>
	</TR>
	<tr>
		<td height="20"></td>
	</TR>
	</TABLE>
	</td>
</tr>
</form>
<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=type>
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type="hidden" name="board" value="<?=$board?>">
<input type="hidden" name="s_check" value="<?=$s_check?>">
<input type="hidden" name="search" value="<?=$search?>">
</form>
<tr>
	<td colspan="<?=$colspan?>" align=center>
	<TABLE border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td class="main_sfont_non">
		<table cellpadding="10" cellspacing="1" bgcolor="#DBDBDB" width="100%">
		<form method=get name=frm action=<?=$PHP_SELF?>>
		<input type="hidden" name="board" value="<?=$board?>">
		<tr>
			<td bgcolor="#FFFFFF" align="center">
			<SELECT name="s_check" class="select">
			<OPTION value="">---- 검색종류 ----</OPTION>
			<OPTION value="c" <?=$check_c?>>제목+내용</OPTION>
			<OPTION value="n" <?=$check_n?>>작성자</OPTION>
			</SELECT>
			<INPUT class="input" size="30" name="search" value="<?=$search?>"> <a href="javascript:schecked();"><img src="images/icon_search.gif" alt="검색" align="absMiddle" border="0"></a><A href="javascript:search_default();"><IMG src="images/icon_search_clear.gif" align="absMiddle" border="0" hspace="2"></A></td>
		</tr>
		</FORM>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>

<SCRIPT LANGUAGE="JavaScript">
<!--
function schecked(){
	if (frm.search.value == ''){
		alert('검색어를 입력해주세요.');
		frm.search.focus();
	}
	else {
		frm.submit();
	}
}

function search_default(){
	frm.s_check.value = "";
	frm.search.value = "";
	frm.submit();
}

function allcheck2() {
	if (document.all.allcheck.value == 0) {
		for(var j=0; j < document.changeForm.elements.length; j++) {
			var checke = document.changeForm.elements[j];

				checke.checked = true;
		}
		document.all.allcheck.value = 1;
	} else {
		for(var j=0; j < document.changeForm.elements.length; j++) {
			var checke = document.changeForm.elements[j];

				checke.checked = false;
		}
		document.all.allcheck.value = 0;
	}
}

function changeListView(kind) {
	var isTrue = false;
	for(var i=0;i<changeForm.elements.length;i++) {
		if ((changeForm.elements[i].type == "checkbox") && (changeForm.elements[i].name == "cart[]")) {
			if (changeForm.elements[i].checked == true) {
				isTrue = true;
			}
		}
	}

	if (!isTrue) {
		alert('선택된 게시글이 없습니다.');
		return false;
	} else {
		if (kind == "change") {
			changeForm.action = "community_article_changepop.php?board=<?=$board?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>";
			OpenWindow("",400,250,"yes","changeWindow");
			changeForm.target = "changeWindow";
			changeForm.submit();
		} else if (kind == "delete") {
			var con = confirm("선택된 게시물을 삭제하시겠습니까?");
			if (con) {
				changeForm.action = "community_article_deletepop.php?board=<?=$board?>&block=<?=$nowblock?>&gotopage=<?=$gotopage?>&search=<?=$search?>&s_check=<?=$s_check?>";
				OpenWindow("",1,1,"no","deleteWindow");
				changeForm.target = "deleteWindow";
				changeForm.submit();
			} else {
				return false;
			}
		}
	}
}

function GoPage(block,gotopage) {
	document.form2.block.value = block;
	document.form2.gotopage.value = gotopage;
	document.form2.submit();
}
//-->
</SCRIPT>