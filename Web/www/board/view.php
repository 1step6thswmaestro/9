<?
if(substr(getenv("SCRIPT_NAME"),-9)=="/view.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}


include ("head.php");

/*
if($setup[btype]=="B") {	//블로그형 게시판은 view페이지가 없다.
	header("Location:board.php?pagetype=list&board=".$board."&block=".$block."&gotopage=".$gotopage."&search=".$search."&s_check=".$s_check);
	exit;
}
*/

if($member[grant_view]!="Y") {
	if(strlen($setup[group_code])==4 && $setup[group_code]!=$member[group_code]) {
		$errmsg="이용 권한이 없습니다.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
	} else {
		$errmsg="쇼핑몰 회원만 이용 가능합니다.\\n\\n로그인 후 이용하시기 바랍니다.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
	}
}

switch ($s_check) {
	case "c":
		$check_c = "checked";
		break;
	case "n":
		$check_n = "checked";
		break;
	default:
		$check_c = "checked";
		break;
}

$orSearch = split(" ",$search);
switch ($s_check) {
	case "c":
		$sql_search = "AND (";
		for($oo=0;$oo<count($orSearch);$oo++) {
			if ($oo > 0) {
				$sql_search .= " OR ";
			}
			$sql_search .= "title LIKE '%" . $orSearch[$oo] . "%' ";
			$sql_search .= "OR content LIKE '%" . $orSearch[$oo] . "%' ";
		}
		$sql_search .= ") ";
		break;
	case "n":
		$sql_search = "AND (";
		for($oo=0;$oo<count($orSearch);$oo++) {
			if ($oo > 0) {
				$sql_search .= " OR ";
			}
			$sql_search .= "name LIKE '%" . $orSearch[$oo] . "%' ";
		}
		$sql_search .= ") ";
		break;
}



if ( strlen($subCategory) > 0 ) {
	$sql_subCategory_search.= " AND a.subCategory = '".$subCategory."' ";
}



$query  = "SELECT * FROM tblboard WHERE board='".$board."' ";
$query .= "AND num = '".$num."' ";

getSecret($query,$row);

$this_num = $row[num];
$this_thread = $row[thread];
$this_pos = $row[pos];
$this_prev = $row[prev_no];
$this_next = $row[next_no];
$this_id = $row[id];
$this_comment = $row[total_comment];
$pridx=$row[pridx];

$row[title] = $subCategoryView.stripslashes($row[title]);
$row[title] = getTitle($row[title]);
$row[title] = getStripHide($row[title]);

//$row[name] = getStripHide(stripslashes($row[name]));
if($setup[btype]=="L") { // 아래서 처리 하는 부분 위에도 복사함.
	$prqnaboard=getEtcfield($_data->etcfield,"PRQNA");
	if($prqnaboard==$board) {
		$row['name'] = !_empty($row['userid'])?$row['userid']:$row['name'];
	}
}


if (strlen($row[email])>0 && $member[admin]=="SU") {
	$strName = "<a href='mailto:".$row[email]."' style=\"text-decoration:underline\">".$row[name]." [".$row[email]."]</a>";
} else {
	if($setup[use_hide_email]=="Y") {
		$strName = "<A style=\"cursor:point;text-decoration:underline\">".$row[name]."</A>";
	} else {
		if(strlen($row[email])>0) {
			$strName = "<a href='mailto:".$row[email]."' style=\"text-decoration:underline\">".$row[name]." [".$row[email]."]</a>";
		} else {
			$strName = "<A style=\"cursor:point;text-decoration:underline\">".$row[name]."</A>";
		}
	}
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
		$file_name1=FileDownload($board,$row[filename])." (".ProcessBoardFileSize($board,$row[filename]).")";

		$ext = strtolower(substr(strrchr($row[filename],"."),1));
		if($ext=="gif" || $ext=="jpg" || $ext=="png") {
			$imgmaxwidth=ProcessBoardFileWidth($board,$row[filename]);
			if($setup[img_maxwidth]<$imgmaxwidth) {
				$imgmaxwidth=$setup[img_maxwidth];
			}
			$upload_file1="<a href=\"javascript:zoomImage('".$row[filename]."','".$board."');\"><img src=\"".ImageAttachUrl($board,$row[filename])."\" border=0 width=\"".$imgmaxwidth."\" /></a>";
		}
	}
}

if ($setup[use_hide_ip]=="N" || $member[admin]=="SU") {
	$strIp = "IP : ".$row[ip];
}

$strDate = getTimeFormat($row[writetime]);
$strSubject = stripslashes($row[title]);
$strSubject = getStripHide($strSubject);
$strSubject = $secret_img.$strSubject;



if ($row[use_html] == "1") {
	$memo = stripslashes($row[content]);
} else {
	$memo = stripslashes(nl2br($row[content]));
}
$nowblock = $block;
$curpage  = $block * $setup[page_num] + $gotopage;

$t_count = $setup[total_article];

if ($s_check) {
	$sql2  = "SELECT COUNT(*) FROM tblboard WHERE board='".$board."' ";
	$sql2 = $sql2.$sql_search;
	$result2 = mysql_query($sql2,get_db_conn());
	$row2 = mysql_fetch_row($result2);

	$t_count = $row2[0];

	mysql_free_result($result2);
}

$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

include ("top.php");

//comment
if ($setup[use_comment]=="Y" && $this_comment > 0) {
	$com_query = "SELECT * FROM tblboardcomment WHERE board='".$board."' ";
	$com_query.= "AND parent = $this_num ORDER BY num DESC ";
	$com_result = @mysql_query($com_query,get_db_conn());
	$com_rows = @mysql_num_rows($com_result);

	if ($com_rows <= 0) {
		@mysql_query("UPDATE tblboard SET total_comment='0' WHERE board='$board' AND num='$this_num'");
	} else {
		unset($com_list);
		while($com_row = mysql_fetch_array($com_result)) {
			$com_list[count($com_list)] = $com_row;
		}
		mysql_free_result($com_result);
	}
}

//윗글
if ($s_check) {
	$p_query  = "SELECT num,thread,title,name,email,subCategory FROM tblboard WHERE board='$board' ";
	$p_query .= "AND pos = 0 AND thread < '$this_thread' AND deleted != '1' ";
	if ($sql_search) $p_query .= $sql_search." ";
	$p_query .= "ORDER BY thread DESC limit 1" ;
} else {
	$p_query  = "SELECT num,thread,title,name,email,subCategory FROM tblboard WHERE board='$board' ";
	$p_query .= "AND num = $this_prev ";
}
$p_query .= $sql_subCategory_search;
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
	if( strlen($p_row[subCategory]) > 0 ) $prevTitle = "<strong>[".$p_row[subCategory]."]</strong>&nbsp;".$prevTitle;

	if ($setup[title_length] > 0) {
		$len_title = $setup[title_length];

		$prevTitle = len_title($prevTitle,$len_title);
	}

	$prevTitle = "<a href='board.php?pagetype=view&view=1&num=".$p_row[num]."&board=".$board."&block=".$block."&gotopage=".$gotopage."&search=".$search."&subCategory=".$subCategory."&s_check=".$s_check."&boardsep=".$boardsep." ' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전글 : ".$prevTitle."';return true\">".$prevTitle."</a>";
	$prevName = $p_row[name];
	$prevEmail = $p_row[email];

	if ($prevEmail && $member[admin] == "SU") {
		$prevName = "<a href=mailto:".$prevEmail." onmouseout=\"window.status=''\" onmouseover=\"window.status='".$prevEmail."'; return true\">".$prevName."</a>";
	}
}


//아랫글
if ($s_check) {
	$n_query  = "SELECT num,thread,title,name,email,subCategory FROM tblboard WHERE board='$board' ";
	$n_query .= "AND pos = 0 AND thread > '$this_thread' AND deleted != '1' ";
	if ($sql_search) $n_query .= $sql_search." ";
	$n_query .= "ORDER BY thread limit 1" ;
} else {
	$n_query  = "SELECT num,thread,title,name,email,subCategory FROM tblboard WHERE board='$board' ";
	$n_query .= "AND num = $this_next ";
}
$n_query .= $sql_subCategory_search;
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
	if( strlen($n_row[subCategory]) > 0 ) $nextTitle = "<strong>[".$n_row[subCategory]."]</strong>&nbsp;".$nextTitle;

	if ($setup[title_length] > 0) {
		$len_title = $setup[title_length];

		$nextTitle = len_title($nextTitle,$len_title);
	}

	$nextTitle = "<a href='board.php?pagetype=view&view=1&num=".$n_row[num]."&board=".$board."&block=".$block."&gotopage=".$gotopage. "&search=".$search."&subCategory=".$subCategory."&s_check=".$s_check."&boardsep=".$boardsep." ' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음글 : ".$nextTitle."';return true\">".$nextTitle."</a>";
	$nextName = $n_row[name];
	$nextEmail = $n_row[email];

	if ($nextEmail && $member[admin] == "SU") {
		$nextEmail = "<a href=mailto:".$nextEmail." onmouseout=\"window.status=''\" onmouseover=\"window.status='".$nextEmail."'; return true\">".$nextName."</a>";
	}
}

//관련답변글 뽑아내는 루틴
if ($setup[use_reply] == "Y") {
	$query2  = "SELECT num, thread, pos, depth, name, email, deleted, title, writetime ";
	$query2 .= "FROM tblboard WHERE board='".$board."' ";
	$query2 .= "AND thread = ".$this_thread." ";
	$query2 .= "ORDER BY pos ";
	$result_re = mysql_query($query2, get_db_conn());
	$total_re = mysql_num_rows($result_re);

	if ($total_re == 1) {
		$hide_reply_start = "<!--";
		$hide_reply_end = "-->";
	} else {
		if($total_re>1) {
			$tr_str1 .= "<TR height=\"30\" align=\"center\" bgcolor=\"#F8F8F8\" style=\"letter-spacing:-0.5pt;\">\n";
			$tr_str1 .= "	<TD><font color=\"#333333\"><b>글제목</b></TD>\n";
			$tr_str1 .= "	<TD><font color=\"#333333\"><b>글쓴이</b></TD>\n";
			$tr_str1 .= "	<TD><font color=\"#333333\"><b>작성일</b></TD>\n";
			$tr_str1 .= "</TR>\n";
			while ($row5 = mysql_fetch_array($result_re)) {
				unset($td_style);
				if ($num == $row5[num]) {
					$td_style = "color:#FF6600;font-size:11px;letter-spacing:0pt;font-weight:bold;";
				}
				$row5[title] = getTitle($row5[title]);
				$row5[title] = getStripHide($row5[title]);
				$row5[name] = len_title($row5[name], $nameLength);
				$row5[name] = getStripHide($row5[name]);

				$tr_str1 .= "<tr><td height=\"1\" bgcolor=\"$list_divider\" colspan=\"3\"></td></tr>";
				if ($row5[deleted] != "1") {
					$tr_str1 .= "<tr height=\"30\" style=\"CURSOR:hand;\" onClick=\"location='board.php?pagetype=view&view=1&num=".$row5[num]."&board=".$board."&block=".$nowblock."&gotopage=".$gotopage."&search=".$search."&subCategory=".$subCategory."&s_check=".$s_check."&boardsep=".$boardsep."';\">";
					$tr_str1 .= "<td style=\"padding-left:3pt;padding-right:3pt;BORDER-LEFT:#E3E3E3 0pt solid;\"><a href='board.php?pagetype=view&view=1&num=".$row5[num]."&board=".$board."&search=".$search."&subCategory=".$subCategory."&s_check=".$s_check."&boardsep=".$boardsep."' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='답변글 : ".$row5[title]."';return true\"><span style=\"$td_style\">";
				} else {
					$tr_str1 .= "<tr height=\"30\">";
					$tr_str1 .= "<td style=\"padding-left:3pt;padding-right:3pt;BORDER-LEFT:#E3E3E3 0pt solid;\"><span style=\"$td_style\">\"";
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

					$tr_str1 .= "<img src=\"".$imgdir."/x.gif\" width=\"".$wid."\" height=\"2\" border=\"0\">";
					$tr_str1 .= "<img src=\"".$imgdir."/re_mark.gif\" border=\"0\" align=\"absmiddle\">";

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

				if (getNewImage($row5[writetime])) {
					$tr_str1 .= "&nbsp;<img src=\"".$imgdir."/icon_new.gif\" border=\"0\">&nbsp;";
				}

				$tr_str1 .= "</td>";
				$tr_str1 .= "<td align=\"center\" style=\"padding-left:3pt;padding-right:3pt;BORDER-LEFT:#E3E3E3 0pt solid;\">".$row5[name]."</td>";

				$tr_str1 .= "<td align=\"center\" class=\"list_text\" style=\"padding-left:3pt;padding-right:3pt;BORDER-LEFT:#E3E3E3 0pt solid;\">".getTimeFormat($row5[writetime])."</td>";
				$tr_str1 .= "</tr>";
			}
		}
		mysql_free_result($result_re);
	}
}

if($setup[btype]=="L") {
	if(strlen($pridx)>0 && $pridx>0) {
		$prqnaboard=getEtcfield($_data->etcfield,"PRQNA");
		if($prqnaboard!=$board) $pridx="";
	}
	if(strlen($pridx)>0) {
		$sql = "SELECT a.productcode,a.productname,a.etctype,a.sellprice,a.quantity,a.tinyimage ";
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE pridx='".$pridx."' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		$result=mysql_query($sql,get_db_conn());
		if($_pdata=mysql_fetch_object($result)) {
			INCLUDE "prqna_top.php";
		} else {
			$pridx="";
		}
		mysql_free_result($result);
	}
}
include ("./view_sns.php");
include ($dir."/view.php");
include ($dir."/view_foot.php");

if($_data->sns_ok == "Y" && $setup[sns_state] == "Y" && strlen($_ShopInfo->getMemid())>0){ //
	if ($setup[use_comment] == "Y" && $member[grant_comment]=="Y") {
		@include ("./sns_comment.php");
	}
}else{

	if ($setup[use_comment] == "Y" && $member[grant_comment]=="Y") {
		$secuCmtMsg = "";
		if( $setup["secuCmt"] == "Y" ){
			$secuCmtMsg .= "비밀 댓글 게시판으로 자신의 댓글만 조회 가능합니다. ";
		}
		if( $setup["onlyCmt"] == "Y" ){
			$secuCmtMsg .= "회원당 1건만 등록 가능하며, 삭제는 불가합니다. ";
		}

		$secuCmtMsg = ( strlen($secuCmtMsg) > 0 ) ? "(".$secuCmtMsg.")" : "";
		@include ($dir."/comment_head.php");
	}


	$secuCmtViewCnt=0;

	for ($jjj=0;$jjj<count($com_list);$jjj++) {
		// 단일댓글
		if( $_ShopInfo->getMemid() == $com_list[$jjj][id] AND $setup[onlyCmt] == "Y" ) $secuCmtViewCnt++;
	}


	if ($setup[use_comment] == "Y" && $member[grant_comment]=="Y") {
		if( $secuCmtViewCnt == 0 OR strlen($_ShopInfo->id) > 0 ){
			$cmtFile = ($setup[fileYN] == "Y") ? "<input type=\"file\" name=\"img\" class=\"input\">" : ""; // 파일첨부
			@include ($dir."/comment_write.php");
		}
	} else {
		@include ($dir."/comment_write_login.php");
	}



	for ($jjj=0;$jjj<count($com_list);$jjj++) {

		$c_num = $com_list[$jjj][num];
		$c_name = $com_list[$jjj][name];

		if($setup[use_comip]!="Y") {
			$c_uip=$com_list[$jjj][ip];
		}

		unset($comUserId);

		$c_writetime = getTimeFormat($com_list[$jjj][writetime]);
		$c_comment = nl2br(stripslashes($com_list[$jjj][comment]));
		$c_ip = $com_list[$jjj][ip];
		$c_comment = getStripHide($c_comment);

		// 비밀댓글
		$secuCmtView = true;
		if( $setup["secuCmt"] == "Y" ){
			$secuCmtView = false;
			if( $_ShopInfo->getMemid() == $com_list[$jjj][id] OR strlen($_ShopInfo->id) > 0 ){
				$secuCmtView = true;
			}
		}


		// 관리자 댓글의 댓글
		$adminComment = "";
		$adminCommSQL = "SELECT * FROM `tblboardcomment_admin` WHERE `board` = '".$board."' AND `board_no`= '".$num."' AND `comm_no`= '".$c_num."' ORDER BY `idx` ASC";
		$adminCommResult = mysql_query( $adminCommSQL );
		$adminCommNums = mysql_num_rows($adminCommResult);

		if($adminCommNums > 0) {
			$adminComment .= "<div style=\" background:#f9f9f9; border:1px solid #f5f5f5; padding:5px 10px 3px 10px;\">";
			while( $adminCommRow = mysql_fetch_assoc ( $adminCommResult ) ) {
				$adminComment .= "
					<p style=\"font-size:11px; padding:3px 0px;\">
						<img src=\"".$imgdir."/icon_reply.gif\" alt=\"\" /> <strong>관리자</strong> : ".$adminCommRow['comment']."
					</p>
				"; //(".$adminCommRow['reg_date'].")
			}
			$adminComment .= "</div>";
		}

		// 파일
		$filesname = DirPath.DataDir."shopimages/board/".$board."/".$com_list[$jjj]['file'];
		$filessize = @getimagesize($filesname);
		$c_comment_file_max_width = $setup[comment_width];
		$c_comment_file_width = ( $c_comment_file_max_width < $filessize[0] ) ? ($c_comment_file_max_width) : $filessize[0];
		$c_comment_file = ( strlen($com_list[$jjj]['file']) > 0 ) ? "<div style='float:left; margin-right:20px;'><a href=\"javascript:zoomImage('".$com_list[$jjj]['file']."','".$board."');\"><img src='".$filesname."' width='".$c_comment_file_width."'></a></div>" : "";

		if( $secuCmtView ) @include ($dir."/comment_list.php");

	}





}

include ("bottom.php");
?>
<!-- sns 코멘트 / 게시판 SNS 홍보 ------------- -->
<script type="text/javascript">
<!--
var board = "<?=$board ?>";
var bod_uid = "<?=$this_num ?>";
var memId = "<?=$_ShopInfo->getMemid() ?>";
var snsType = "";
var preShowID = "";

$j(document).ready( function () {
	if(memId != ""){
		snsImg();
		snsInfo();
	}
	showbodComment();
});
//-->
</script>