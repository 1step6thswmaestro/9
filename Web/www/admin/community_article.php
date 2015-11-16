<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-1";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

include ($Dir.BoardDir."file.inc.php");

$prqnaboard=getEtcfield($_shopdata->etcfield,"PRQNA");

unset($setup);
$file_icon_path = "images/board/file_icon";
$imgdir = "images/board";
$nameLength=20;

function writeSecret($exec,$is_secret,$pos) {
	global $setup;

	if ($exec == "reply") $disabled = "disabled";
	if ($exec == "modify" && $pos != "0") $disabled = "disabled";

	if($setup[use_lock]=="A") {
		echo "<select name=tmp_is_secret disabled class=select>
			<option value=\"0\">사용안함</option>
			<option value=\"1\" selected>잠금사용</option>
			</select> &nbsp; <FONT COLOR=\"red\">자동잠금기능</FONT>
		";
	} else if($setup[use_lock]=="Y") {
		${"select".$is_secret} = "selected";
		echo "<select name=tmp_is_secret $disabled class=select>
			<option value=\"0\" $select0>사용안함</option>
			<option value=\"1\" $select1>잠금사용</option>
			</select>
		";
	}
}

function reWriteForm() {
	global $exec, $_POST;
	if ($_POST[up_html]) $up_html = "checked";
	$up_subject = urlencode(stripslashes($_POST[up_subject]));
	$up_memo = urlencode(stripslashes($_POST[up_memo]));
	$up_name = urlencode(stripslashes($_POST[up_name]));

	echo "<form name=reWriteForm method=post action=".$_SERVER[PHP_SELF]."?exec=".$exec.">\n";
	echo "<input type=hidden name=\"mode\" value=\"reWrite\">\n";
	echo "<input type=hidden name=\"thisBoard[is_secret]\" value=\"$_POST[up_is_secret]\">\n";
	echo "<input type=hidden name=\"thisBoard[name]\" value=\"$up_name\">\n";
	echo "<input type=hidden name=\"thisBoard[passwd]\" value=\"$_POST[up_passwd]\">\n";
	echo "<input type=hidden name=\"thisBoard[email]\" value=\"$_POST[up_email]\">\n";
	echo "<input type=hidden name=\"thisBoard[use_html]\" value=\"$up_html\">\n";
	echo "<input type=hidden name=\"thisBoard[title]\" value=\"$up_subject\">\n";
	echo "<input type=hidden name=\"thisBoard[content]\" value=\"$up_memo\">\n";
	echo "<input type=hidden name=\"thisBoard[pos]\" value=\"$_POST[pos]\">\n";

	echo "<input type=hidden name=num value=\"$_POST[num]\">\n";
	echo "<input type=hidden name=board value=\"$_POST[board]\">\n";
	echo "<input type=hidden name=up_board value=\"$_POST[up_board]\">\n";
	echo "<input type=hidden name=s_check value=\"$_POST[s_check]\">\n";
	echo "<input type=hidden name=search value=\"$_POST[search]\">\n";
	echo "<input type=hidden name=block value=\"$_POST[block]\">\n";
	echo "<input type=hidden name=gotopage value=\"$_POST[gotopage]\">\n";
	echo "</form>\n";
	echo "<script>document.reWriteForm.submit();</script>";
	exit;
}

function sendMailForm($send_name,$send_email,$message,&$bodytext,&$mailheaders) {
	$mailheaders  = "From: $send_name <$send_email>\r\n";
	$mailheaders .= "X-Mailer:SendMail\r\n";
	$boundary = "--------" . uniqid("part");
	$mailheaders .= "MIME-Version: 1.0\r\n";
	$mailheaders .= "Content-Type: text/html;";
	$bodytext .= $message . "\r\n\r\n";
}

function len_title($title,$len_title) {
	if (strlen($title) > $len_title) {
		for($jj=0;$jj < $len_title;$jj++) {
			$uu=ord(substr($title, $jj, 1));
			if( $uu > 127 ){
				$jj++;
			}
		}
		$title=substr($title,0,$jj);
		$title=$title."...";
	}
	return $title;
}

$list_header_bg_color = "#F6F6F6";
$list_header_dark0 = "#DFDFDF";
$list_header_dark1 = "#FFFFFF";
$list_header_back = "#EAF4F6";

$list_mouse_over_color = "#F6F6F6";

$list_divider = "#DFDFDF";

$list_footer_bg_color = "#D6D6D6";

$list_notice_bg_color = "#FEFEFE";
$list_bg_color = "white";

$view_divider = "#cfcfcf";
$view_left_header_color = "#F6F6F6";
$view_body_color = "#FFFFFF";

$comment_header_bg_color = "#CCCCCC";



// 관리자 코멘트 답변
if($_REQUEST["mode"]=="saveAdminComm") {

	$exec=$_REQUEST["exec"];
	$board=$_REQUEST["board"];
	$num=$_REQUEST["num"];
	$c_num=$_REQUEST["c_num"];
	$block=$_REQUEST["block"];
	$gotopage=$_REQUEST["gotopage"];
	$search=$_REQUEST["search"];
	$s_check=$_REQUEST["s_check"];

	$adminComm=$_REQUEST["adminComm"];

	$commAdminSQL = "
		INSERT
			`tblboardcomment_admin`
		SET
			`board` = '".$board."',
			`board_no` = '".$num."',
			`comm_no` = '".$c_num."',
			`comment` = '".$adminComm."',
			`reg_date` = NOW()
	";
	mysql_query($commAdminSQL,get_db_conn());

	header("Location:".$_SERVER[PHP_SELF]."?exec=view&board=$board&num=$num&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check");
	exit;
}



// 관리자 코멘트 답변
if($_REQUEST["mode"]=="comment_admin_del") {

	$exec=$_REQUEST["exec"];
	$board=$_REQUEST["board"];
	$num=$_REQUEST["num"];
	$c_num=$_REQUEST["c_num"];
	$block=$_REQUEST["block"];
	$gotopage=$_REQUEST["gotopage"];
	$search=$_REQUEST["search"];
	$s_check=$_REQUEST["s_check"];

	$delidx=$_REQUEST["delidx"];

	$commAdminSQL = "
		DELETE FROM
			`tblboardcomment_admin`
		WHERE
			`idx` = '".$delidx."'
		LIMIT 1
	";
	mysql_query($commAdminSQL,get_db_conn());

	header("Location:".$_SERVER[PHP_SELF]."?exec=view&board=$board&num=$num&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check");
	exit;
}



//코멘트 달기
if($_REQUEST["mode"]=="comment_result") {
	$exec=$_POST["exec"];
	$board=$_POST["board"];
	$num=$_POST["num"];
	$block=$_POST["block"];
	$gotopage=$_POST["gotopage"];
	$search=$_POST["search"];
	$s_check=$_POST["s_check"];

	$up_name=$_POST["up_name"];
	$up_comment=$_POST["up_comment"];


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
		$errmsg="댓글 달 게시글이 없습니다.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
	}

	if ($setup[use_comment] != "Y") {
		$errmsg="해당 게시판은 댓글 기능을 지원하지 않습니다.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
	}

	if(!eregi($_SERVER[HTTP_HOST],$_SERVER[HTTP_REFERER])) {
		$errmsg="잘못된 경로로 접근하셨습니다.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
	}

	if(isNull($up_comment)) {
		$errmsg="내용을 입력하셔야 합니다.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
	}

	if(isNull($up_name)) {
		$errmsg="이름을 입력하셔야 합니다.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
	}


	$up_name = addslashes($up_name);
	$up_comment = autoLink($up_comment);
	$up_comment = addslashes($up_comment);

	$sql  = "INSERT tblboardcomment SET ";
	$sql.= "board		= '".$row->board."', ";
	$sql.= "parent		= '".$row->num."', ";
	$sql.= "name		= '".$up_name."', ";
	$sql.= "passwd		= '".$setup[passwd]."', ";
	$sql.= "ip			= '".$_SERVER[REMOTE_ADDR]."', ";
	$sql.= "writetime	= '".time()."', ";
	$sql.= "comment		= '".$up_comment."' ";
	$insert = mysql_query($sql,get_db_conn());

	// 코멘트 갯수를 구해서 정리
	$total=mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM tblboardcomment WHERE board='".$row->board."' AND parent='".$row->num."'",get_db_conn()));
	mysql_query("UPDATE tblboard SET total_comment='".$total[0]."' WHERE board='".$row->board."' AND num='".$row->num."'",get_db_conn());

	header("Location:".$_SERVER[PHP_SELF]."?exec=view&board=$board&num=$num&block=$block&gotopage=$gotopage&search=$search&s_check=$s_check");
	exit;
} else if($_REQUEST["mode"]=="comment_del") {
	$exec=$_REQUEST["exec"];
	$board=$_REQUEST["board"];
	$num=$_REQUEST["num"];
	$c_num=$_REQUEST["c_num"];
	$block=$_REQUEST["block"];
	$gotopage=$_REQUEST["gotopage"];
	$search=$_REQUEST["search"];
	$s_check=$_REQUEST["s_check"];

	$sql = "SELECT * FROM tblboardcomment WHERE parent='".$num."' AND num = ".$c_num." ";
	$result = mysql_query($sql,get_db_conn());
	if ($row=mysql_fetch_object($result)) {
		$sql = "DELETE FROM tblboardcomment WHERE board='".$row->board."' AND parent='".$num."' AND num = '".$c_num."'";
		$delete = mysql_query($sql,get_db_conn());

		if ($delete) {
			@mysql_query("UPDATE tblboard SET total_comment = total_comment - 1 WHERE board='".$row->board."' AND num='".$num."'",get_db_conn());
		}
	}
	header("Location:".$_SERVER[PHP_SELF]."?exec=view&board=$board&num=$num&s_check=$s_check&search=$search&block=$block&gotopage=$gotopage");
	exit;
}

$exec=$_REQUEST["exec"];
if(strlen($exec)==0) $exec="list";


$board=$_REQUEST["board"];

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 20;

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}

$s_check=$_REQUEST["s_check"];
$search=$_REQUEST["search"];

switch ($s_check) {
	case "c":
		$check_c = "selected";
		break;
	case "n":
		$check_n = "selected";
		break;
	default:
		$check_c = "selected";
		break;
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
		<col width=198></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top"  background="images/leftmenu_bg.gif">
			<? include ("menu_community.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 커뮤니티 &gt; 커뮤니티 관리  &gt; <span class="2depth_select">게시판 게시물 관리</span></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
        <td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_01_bg.gif"></td>
        <td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr>
        <td width="16" background="images/con_t_04_bg1.gif"></td>
        <td bgcolor="#ffffff" style="padding:10px">






			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
				<TR>
					<TD><IMG SRC="images/community_article_title.gif" border="0"></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN="2" background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">등록된 게시판의 모든 게시물을 관리할 수 있습니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN="2" background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td><?include ("community_article.".$exec.".inc.php")?></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif">&nbsp;</TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<col width=20></col>
					<col width=></col>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">게시판 게시물 관리</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;"><p>- 쇼핑몰에 등록된 게시판의 모든 글을 수정/삭제 및 작성하실 수 있습니다.</p></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;"><p>- 회원 게시판에 별도의 로그인 없이 비밀글 열람 및 게시물 관리가 가능합니다.</p></td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
</td>
        <td width="16" background="images/con_t_02_bg.gif"></td>
    </tr>
    <tr>
        <td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_04_bg.gif"></td>
        <td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr><td height="20"></td></tr>
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

<?=$onload?>

<? INCLUDE "copyright.php"; ?>