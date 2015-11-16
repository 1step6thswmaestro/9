<?
if(substr(getenv("SCRIPT_NAME"),-9)=="/list.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}

include ("head.php");

if($member[grant_list]!="Y") {
	if(strlen($setup[group_code])==4 && $setup[group_code]!=$member[group_code]) {
		$errmsg="이용 권한이 없습니다.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
	} else {
		$errmsg="쇼핑몰 회원만 이용 가능합니다.\\n\\n로그인 후 이용하시기 바랍니다.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
	}
}

if($setup[btype]=="B") {
	if($member[grant_view]!="Y") {
		if(strlen($setup[group_code])==4 && $setup[group_code]!=$member[group_code]) {
			$errmsg="이용 권한이 없습니다.";
			echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
		} else {
			$errmsg="쇼핑몰 회원만 이용 가능합니다.\\n\\n로그인 후 이용하시기 바랍니다.";
			echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
		}
	}
}

$prqnaboard="";
if($setup[btype]=="L") {
	$prqnaboard=getEtcfield($_data->etcfield,"PRQNA");
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

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}

$t_count = $setup[total_article];

if (strlen($s_check)>0 || $setup[use_reply] != "Y") {
	$sql2  = "SELECT COUNT(*) FROM tblboard a WHERE a.board='".$board."' ";

	$orSearch = split(" ",$search);
	// 검색어가 있는경우 쿼리문에 조건추가...........
	switch ($s_check) {
		case "c":
			$w_que = "AND (";
			for($oo=0;$oo<count($orSearch);$oo++) {
				if ($oo > 0) {
					$w_que .= " OR ";
				}
				$w_que .= "a.title LIKE '%" . $orSearch[$oo] . "%' ";
				$w_que .= "OR a.content LIKE '%" . $orSearch[$oo] . "%' ";
			}
			$w_que .= ") ";
			break;
		case "n":
			$w_que = "AND (";
			for($oo=0;$oo<count($orSearch);$oo++) {
				if ($oo > 0) {
					$w_que .= " OR ";
				}
				$w_que .= "a.name LIKE '%" . $orSearch[$oo] . "%' ";
			}
			$w_que .= ") ";
			break;
	}

	if ($setup[use_reply] != "Y") {
		$w_que.= "AND a.pos = 0 AND a.depth = 0 ";
	}

	if ( strlen($_GET['subCategory']) > 0 ) {
		$w_que.= " AND a.subCategory = '".$_GET['subCategory']."' ";
	}


	$sql2 = $sql2.$w_que;
	$result2 = mysql_query($sql2,get_db_conn());

	$row2 = mysql_fetch_row($result2);

	$t_count = $row2[0];
	mysql_free_result($result2);
}

$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

if($prqnaboard==$board) {
	$sql = "SELECT a.*, b.productcode,b.productname,b.etctype,b.sellprice,b.quantity,b.tinyimage ";
	$sql.= "FROM tblboard a LEFT OUTER JOIN tblproduct b ";
	$sql.= "ON a.pridx=b.pridx ";
	$sql.= "WHERE a.board='".$board."' ";
} else {
	$sql = "SELECT a.* FROM tblboard a WHERE a.board='".$board."' ";
}



// 정열
if( strlen($_GET['sort']) > 1 ) {
	$sorting = explode ("_",$_GET['sort']);
	$orderBy = "ORDER BY ".$sorting[0]." ".$sorting[1];
	${$sorting[0]."_sortIcon"} = ($sorting[1]=="desc")?"▼":"▲";
} else {
	$orderBy = "ORDER BY thread , pos";
}

$sql = $sql.$w_que.$orderBy." LIMIT ".($setup[list_num] * ($gotopage - 1)).", ".$setup[list_num];
//echo $sql;
$res = mysql_query($sql,get_db_conn());
$total = mysql_num_rows($res);

include ("top.php");

/********************************************
관리자 아이콘 출력 유뮤
*********************************************/
if($setup['admin_icon'] == "N") $strAdminLogin = "";


include ($dir."/list_head.php");
if(strlen($setup[notice])>0) {
	include ($dir."/oneline_notice.php");
}
$nSql = "SELECT num, title, writetime, total_comment FROM tblboard WHERE board='".$board."' ";
$nSql.= "AND notice='1' ORDER BY thread ASC ";
$nResult = mysql_query($nSql,get_db_conn());
while($nRow = mysql_fetch_array($nResult)) {
	$nRow[title] = stripslashes($nRow[title]);
	$nRow[title]=getTitle($nRow[title]);
	$nRow[title]=getStripHide($nRow[title]);
	$nRow[title] = len_title($nRow[title], 100);
	$nRow[writetime] = getTimeFormat($nRow[writetime]);
	INCLUDE $dir."/list_notice.php";
}
mysql_free_result($nResult);
if ($total == 0) {
	if ($s_check == "") {
		$nosearch = "등록된 게시물이 없습니다.";
		INCLUDE $dir."/list_no_main.php";
	} else {
		$nosearch = "검색된 게시물이 없습니다.";
		INCLUDE $dir."/list_no_main.php";
	}
} else {
	$i = 0;
	while($row = mysql_fetch_array($res)) {

		$row[title] = stripslashes($row[title]);
		if($setup[use_html]!="Y") {
			$row[title] = strip_tags($row[title]);
			$row[content] = strip_tags($row[content]);
		}
		$row[title]=getTitle($row[title]);
		$row[title]=getStripHide($row[title]);
		$row[content]=getStripHide(stripslashes($row[content]));
		if($row[use_html]=="0") {
			$row[content]=nl2br($row[content]);
		}
		$row[name] = stripslashes(strip_tags($row[name]));
		$deleted = $row[deleted];
		$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);

		$vote = $row[vote];

		unset($prview_img);
		if($prqnaboard==$board) {
			if(strlen($row[pridx])>0 && $row[pridx]>0 && strlen($row[productcode])>0) {
				$prview_img="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row[productcode]."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=".$imgdir."/btn_prview.gif border=0 align=absmiddle></A>";
			}
		}
		unset($subject);
		$viewContent = '';
		if ($deleted != "1" && $setup[btype]!="B") {
			$subjectURL = "";			
			if( $setup[linkboard] AND strlen($row[url]) > 0 ) {
				$subjectURL = "window.open('".$row[url]."','_blank');";
				$viewContent = '<a href="/board/board.php?pagetype=view&view=1&num='.$row[num].'&board='.$board.'&block='.$nowblock.'&gotopage='.$gotopage.'&search='.$search.'&s_check='.$s_check.'&boardsep='.$boardsep.'"><img src="/data/design/img/sub/btn_viewcontent.gif" alt="본문보기" border="0" /></a>';
			} else{
				$subjectURL = "location.href='board.php?pagetype=view&view=1&num=".$row[num]."&board=".$board."&block=".$nowblock."&gotopage=".$gotopage. "&search=".$search."&s_check=".$s_check."&boardsep=".$boardsep."'";
			}
			$subject = "<a href=\"#\" onclick=\"".$subjectURL." \">";
		}
		$depth = $row[depth];
		if($setup[title_length]>0) {
			$len_title = $setup[title_length];
		}
		$wid = 1;
		if ($depth > 0) {
			if ($depth == 1) {
				$wid = 2;
			} else {
				$wid = (2 * $depth) + (12 * ($depth-1));
			}
			$subject .= "<img src=\"".$imgdir."/x.gif\" width=".$wid."\" height=\"2\" border=\"0\">";
			$subject .= "<img src=\"".$imgdir."/re_mark.gif\" border=\"0\" align=\"absmiddle\">";
			if ($len_title) {
				$len_title = $len_title - (3 * $depth);
			}
		}
		$title = $row[title];
		if( strlen($row[subCategory]) > 0 ) {
			$title = "<strong>[".$row[subCategory]."]</strong>&nbsp;".$title;
		}
		if ($len_title) {
			$title = len_title($title, $len_title);
		}
		$subject .=  $title;
		if ($deleted != "1" && $setup[btype]!="B") {
			$subject .= "</a>";
		}
		unset($new_img);
		if (getNewImage($row[writetime])) {
			$subject .= "&nbsp;<img src=\"".$imgdir."/icon_new.gif\" border=\"0\" align=\"absmiddle\">";
			$new_img .= "<img src=\"".$imgdir."/icon_new.gif\" border=\"0\" align=\"absmiddle\">&nbsp;";
		}
		unset($secret_img);
		//if ($row[pos] == 0) {
			//공개/비공개
			if ($setup[use_lock]=="A" || $setup[use_lock]=="Y") {
				if ($row[is_secret] == "1") {
					$secret_img = "<img src=\"".$imgdir."/lock.gif\" border=\"0\" align=\"absmiddle\">";
				} else {
					$secret_img = "";
				}
			}
		//}

		$commentnum="";
		if ($setup[use_comment]=="Y" && $row[total_comment] > 0) {
			//$subject .= "&nbsp;<img src=\"".$imgdir."/icon_memo.gif\" border=\"0\" align=\"absmiddle\">&nbsp;<font style=\"font-size:8pt;\">(<font color=\"#FF0000\">".$row[total_comment]."</font>)</font>";
			$commentnum = "&nbsp;<font style=\"font-size:11px; color:#FF0000;\">(".$row[total_comment].")</font>";
		}

		//$comment_tot = $row[total_comment];
		//$user_name = len_title($row[name], $nameLength);
		$user_name = $row[name];
		$str_name = $user_name;

		$reg_date = getTimeFormat($row[writetime]);
		$hit = $row[access];

		if($row[filename] && ($deleted != "1")) {
			$file_name = strtolower(substr(strrchr($row[filename],"."),1));
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

		if($setup[btype]=="B") {	//블로그형
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

			if ($setup[use_hide_ip]=="N" || $member[admin]=="SU") {
				$strIp = "IP : ".$row[ip];
			}

			unset($str_content); //글내용
			unset($file_name_str1);	//파일명
			unset($file_name1);	//다운로드 링크
			unset($upload_file1);	//이미지 태그

			if ($row[use_html] == "1") {
				$str_content = stripslashes($row[content]);
			} else {
				$str_content = stripslashes(nl2br($row[content]));
			}

			$attachfileurl=$filepath."/".$row[filename];
			if(file_exists($attachfileurl)) {
				$file_name1=FileDownload($board,$row[filename])." (".ProcessBoardFileSize($board,$row[filename]).")";
				$file_name_str1 = $row[filename];

				$ext = strtolower(substr(strrchr($row[filename],"."),1));
				if($ext=="gif" || $ext=="jpg" || $ext=="png") {
					$imgmaxwidth=ProcessBoardFileWidth($board,$row[filename]);
					if($setup[img_maxwidth]<$imgmaxwidth) {
						$imgmaxwidth=$setup[img_maxwidth];
					}
					$upload_file1="<a href=\"javascript:zoomImage('".$row[filename]."','".$board."');\"><img src=\"".ImageAttachUrl($board,$row[filename])."\" border=0 width=\"".$imgmaxwidth."\"></a>";
				}
			}
		} else if($setup[btype]=="I" || $setup[btype]=="W") {	//앨범형 또는 웹진형
			//썸네일 이미지 링크
			unset($mini_file1);
			$attachfileurl=$filepath."/thumbnail.".$row[filename];
			if(file_exists($attachfileurl)) {
				$ext = strtolower(substr(strrchr($row[filename],"."),1));

				if($ext=="gif" || $ext=="jpg" || $ext=="png") {

					$mini_file1 = "<a href=\"#\" onclick=\"".$subjectURL." \">";

					//페스티벌 게시판 첨부파일 이미지 가로 사이즈 고정처리
					if( $board == 'festival' OR $board == "share"){
						$mini_file1.="<img src=\"".ImageMiniUrl($board,$row[filename])."\" width=\"185\" border=0 style=\"border-width:1pt; border-color:rgb(235,235,235); border-style:solid;\"></a>";
					}else{
						$mini_file1.="<img src=\"".ImageMiniUrl($board,$row[filename])."\" border=0 style=\"border-width:1pt; border-color:rgb(235,235,235); border-style:solid;\"></a>";
					}
				} else {
					if($setup[btype]=="I") {
						$mini_file1="<img src=\"images/no_img.gif\" border=0 width=100 style=\"border-width:1pt; border-color:rgb(235,235,235); border-style:solid;\">";
					}
				}
			} else {
				if($setup[btype]=="I") {
					$mini_file1="<img src=\"images/no_img.gif\" border=0 width=100 style=\"border-width:1pt; border-color:rgb(235,235,235); border-style:solid;\">";
				}
			}
		}
		INCLUDE $dir."/list_main.php";
		$i++;
	}
	mysql_free_result($res);
}

$total_block = intval($pagecount / $setup[page_num]);

if (($pagecount % $setup[page_num]) > 0) {
	$total_block = $total_block + 1;
}

$total_block = $total_block - 1;

if (ceil($t_count/$setup[list_num]) > 0) {
	$a_first_block = "";
	if ($nowblock > 0) {
		$a_first_block .= "<td width=1 nowrap class=bdv></td>\n<td align=center style='padding:2 6 0 5;cursor:hand' onMouseOver=\"this.className='blover'\" OnMouseOut=\"this.className='blout'\" class=verdana2 onClick='location.href=\"board.php?pagetype=list&board=".$board."&s_check=".$s_check."&search=".$search."&block=0&gotopage=1&boardsep=".$boardsep."\"'>1..</td>\n";
	}

	$a_prev_page = "";
	if ($nowblock > 0) {
		$a_prev_page .= "<td width=1 nowrap class=bdv></td>\n<td align=center style='padding:2 6 0 5;cursor:hand' onMouseOver=\"this.className='blover'\" OnMouseOut=\"this.className='blout'\" class=verdana2 onClick='location.href=\"board.php?pagetype=list&board=".$board."&s_check=".$s_check."&search=".$search."&block=".($nowblock-1)."&gotopage=".($setup[page_num]*($block-1)+$setup[page_num])."&boardsep=".$boardsep."\"'>prev</td>\n";

		$a_prev_page = $a_first_block.$a_prev_page;
	}

	if (intval($total_block) <> intval($nowblock)) {
		$print_page = "";
		for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
			if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
				$print_page .= "<td width=1 nowrap class=bdv></td>\n<td align=center style='padding:2 6 0 5;' onMouseOver=\"this.className='blover'\" OnMouseOut=\"this.className='blout'\" class=verdana2><FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></font></TD>\n";
			} else {
				$print_page .= "<td width=1 nowrap class=bdv></td>\n<td align=center style='padding:2 6 0 5;cursor:hand' onMouseOver=\"this.className='blover'\" OnMouseOut=\"this.className='blout'\" class=verdana2 onClick='location.href=\"board.php?pagetype=list&board=".$board."&s_check=".$s_check."&search=".$search."&block=".$nowblock."&gotopage=". (intval($nowblock*$setup[page_num]) + $gopage)."&boardsep=".$boardsep."\"'>".(intval($nowblock*$setup[page_num]) + $gopage)."</TD>\n";
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
				$print_page .= "<td width=1 nowrap class=bdv></td>\n<td align=center style='padding:2 6 0 5;' onMouseOver=\"this.className='blover'\" OnMouseOut=\"this.className='blout'\" class=verdana2><FONT color=red>".(intval($nowblock*$setup[page_num]) + $gopage)."</FONT></TD>\n";
			} else {
				$print_page .= "<td width=1 nowrap class=bdv></td>\n<td align=center style='padding:2 6 0 5;cursor:hand' onMouseOver=\"this.className='blover'\" OnMouseOut=\"this.className='blout'\" class=verdana2 onClick='location.href=\"board.php?pagetype=list&board=".$board."&s_check=".$s_check."&search=".$search."&block=".$nowblock."&gotopage=".(intval($nowblock*$setup[page_num]) + $gopage)."&boardsep=".$boardsep."\"'>".(intval($nowblock*$setup[page_num]) + $gopage)."</TD>\n";
			}
		}
	}


	$a_last_block = "";
	if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
		$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
		$last_gotopage = ceil($t_count/$setup[list_num]);

		$a_last_block .= "<td width=1 nowrap class=bdv></td>\n<td align=center style='padding:2 6 0 5;cursor:hand' onMouseOver=\"this.className='blover'\" OnMouseOut=\"this.className='blout'\" class=verdana2 onClick='location.href=\"board.php?pagetype=list&board=".$board."&s_check=".$s_check."&search=".$search."&block=".$last_block."&gotopage=".$last_gotopage."&boardsep=".$boardsep."\"'>..".$last_gotopage."</TD>\n";
	}

	$a_next_page = "";
	if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
		$a_next_page .= "<td width=1 nowrap class=bdv></td>\n<td align=center style='padding:2 6 0 5;cursor:hand' onMouseOver=\"this.className='blover'\" OnMouseOut=\"this.className='blout'\" class=verdana2 onClick='location.href=\"board.php?pagetype=list&board=".$board."&s_check=".$s_check."&search=".$search."&block=".($nowblock+1)."&gotopage=".($setup[page_num]*($nowblock+1)+1)."&boardsep=".$boardsep."\"'>next</TD>\n";

		$a_next_page = $a_next_page.$a_last_block;
	}
}

// 페이징 유닛 기본 정의
$link = 'board.php?pagetype=list&board='.$board.'&s_check='.$s_check.'&search='.$search.'&subCategory='.$subCategory.'&block='.$last_block.'&boardsep='.$boardsep.'&gotopage=%u';
$pobj = array('page'=>$gotopage,'total_page'=>ceil($t_count/$setup[list_num]),'links'=>$link,'pageblocks'=>10,
						'style_first'=>'<img src="/images/common/btn_page_start.gif" border="0" alt="처음으로" class="blockPageBtn" />',
						'style_prev'=>'<img src="/images/common/btn_page_prev.gif" border="0" alt="이전 10 페이지" class="blockPageBtn" />',
						'style_page'=>'<span class="currpageitem">%u</span>', // 현재 페이지
						'style_next'=>'<img src="/images/common/btn_page_next.gif" border="0" alt="다음 10 페이지" class="blockPageBtn" />',
						'style_end'=>'<img src="/images/common/btn_page_end.gif" border="0" alt="마지막" class="blockPageBtn"  />',
						'style_pages'=>'<span class="pageitem">%u</span>', // 일반 페이지
						'style_page_sep'=>'');
$pobj = new pages($pobj);
$pobj->_solv();

include ($dir."/list_foot.php");
include ("bottom.php");

?>