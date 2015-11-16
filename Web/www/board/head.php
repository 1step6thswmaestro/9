<?
if(substr(getenv("SCRIPT_NAME"),-9)=="/head.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/class/pages.php");

include ("file.inc.php");
include ("lib.inc.php");
include ($Dir.TempletDir."board/".$setup[board_skin]."/color.php");

$dir=$Dir.TempletDir."board/".$setup[board_skin];
$imgdir=$Dir.BoardDir."images/skin/".$setup[board_skin];
$boardsep =!_empty($_REQUEST['boardsep'])?trim($_REQUEST['boardsep']):"";
$table_colcnt=6;
if ( $setup[board_skin] == "L04" ){$table_colcnt=7;}

if($setup[datedisplay]=="N") {
	$hide_date_start="<!--";
	$hide_date_end="-->";
	$table_colcnt=$table_colcnt-1;

	if($setup[hitdisplay]=="N" || ($setup[hitdisplay]=="M" && strlen($member[id])<=0 && strlen($member[authidkey])<=0 && $member[admin]!="SU")) {
		$hide_hit_start="<!--";
		$hide_hit_end="-->";
		$table_colcnt=$table_colcnt-1;
	}
} else {
	if($setup[hitdisplay]=="N" || ($setup[hitdisplay]=="M" && strlen($member[id])<=0 && strlen($member[authidkey])<=0 && $member[admin]!="SU")) {
		$hide_hit_start="<!--";
		$hide_hit_end="-->";
		$table_colcnt=$table_colcnt-1;
	}
}

//차단 IP
$avoid_ip = split(",",$setup[avoid_ip]);
for($i=0;$i<count($avoid_ip);$i++) {
	if (getenv("REMOTE_ADDR") == trim($avoid_ip[$i])) {
		echo "<html><head><title></title></head><body onload=\"alert('접속중인 IP는 해당 게시판 접근이 제한되었습니다.\\n\\n쇼핑몰 운영자에게 문의하시기 바랍니다.');history.go(-1)\"></body></html>";exit;
	}
}


//글쓰기 권한 체크
if($member[grant_write]!="Y") {
	if($setup[grant_write]=="A") {	// 관리자
		$hide_write_start="<!--";
		$hide_write_end="-->";
	} else if($setup[grant_write]=="Y") {
		if(strlen($setup[group_code])==4) {
			$hide_write_start="<!--";
			$hide_write_end="-->";
		} else {
			$hide_write_start="<!--";
			$hide_write_end="-->";
		}
	}
}


if($setup[use_hide_button]=="Y" && $member[admin]!="SU") {
	$hide_write_start="<!--";
	$hide_write_end="-->";
	$reply_start="<!--";
	$reply_end="-->";
	$hide_delete_start="<!--";
	$hide_delete_end="-->";
}

if($setup[use_reply]=="N" OR $setup[grant_reply]=="A") {
	$reply_start="<!--";
	$reply_end="-->";
}

if($setup[use_lock]=="N") {
	$hide_secret_start="<!--";
	$hide_secret_end="-->";
}

if($setup[reply_sms]=="N" || $member[admin]== "SU") {
	$hide_replysms_start="<!--";
	$hide_replysms_end="-->";
}

if($setup[btype]!="L") {
	$reply_start="<!--";
	$reply_end="-->";
}



// 말머리
$subCateSQL = "SELECT `subCategory` FROM `tblboardadmin` WHERE `board` = '".$board."' ;";
$subCateRes = mysql_query($subCateSQL,get_db_conn());
$subCateRow = mysql_fetch_assoc ($subCateRes);
$subCategoryArray = explode(",",$subCateRow[subCategory]);

$subCategoryList = "";
$subCategoryList_start="<!--";
$subCategoryList_end="-->";

$subCategoryView = "";

if( $num > 0 ) {
	$boardSQL = "SELECT `subCategory`,`vote` FROM `tblboard` WHERE board='".$board."' AND num = ".$num;
	$boardResult = mysql_query($boardSQL,get_db_conn());
	$boardRow = mysql_fetch_assoc ($boardResult);
	if( strlen($boardRow['subCategory']) > 0 ) $subCategoryView = "[".$boardRow['subCategory']."]&nbsp;";
}

if( count($subCategoryArray) > 0 AND strlen($subCategoryArray[0]) > 0 ) {

	if( $boardRow['subCategory'] AND $num > 0 ) $selSubCategory = $boardRow['subCategory'];
	if( $_GET['subCategory'] ) $selSubCategory = $_GET['subCategory'];
	$subCategoryList .= "<select name='subCategory'><option value=".">--- 없음 ---</option>";
	foreach ($subCategoryArray as $V) {
		$V = trim($V);
		$sel = ( $selSubCategory == $V )?"selected":"";
		$subCategoryList .= "<option value=\"".$V."\" ".$sel.">".$V."</option>";
	}
	$subCategoryList .= "</select>";

	$subCategoryList_start="";
	$subCategoryList_end="";

}


// 글 추전 vote
$voteLink = "alert('회원 로그인을 하셔야 글을 추천하실 수 있습니다.');";
if( strlen($member[id]) > 0 ) {
	$voteLink = "if(confirm('이 글을 추천 합니까?')){location.href='/board/board.php?pagetype=view&num=".$num."&board=".$board."&block=".$block."&gotopage=".$gotopage."&search=".$search."&s_check=".$s_check."&voteup=up&boardsep=".$boardsep."'};";
}
//$voteButton = "<img src='' alt='글 추천' onclick=\"".$voteLink."\" style=\"cursor:pointer;\"> ".$boardRow['vote'];
$voteButton = "<img src=\"".$imgdir."/butt-vote.gif\" alt='추천하기' onclick=\"".$voteLink."\" style=\"cursor:pointer;\">";

if( $_GET[voteup] == 'up' ) {
	$voteSQL = "UPDATE `tblboard` SET `vote`=`vote`+1 WHERE board='".$board."' AND num = ".$num;
	mysql_query($voteSQL,get_db_conn());
	header("Location:http://".$_SERVER['SERVER_NAME']."/board/board.php?pagetype=view&num=".$num."&board=".$board."&block=".$block."&gotopage=".$gotopage."&search=".$search."&s_check=".$s_check."&boardsep=".$boardsep."");
}

//print_r($setup);


// 글 작성자 아이디
if( strlen($num) > 0 ) {
	$useridSQL = "SELECT userid FROM tblboard WHERE num = ".$num;
	$useridResult = mysql_query( $useridSQL, get_db_conn());
	$useridRow = mysql_fetch_assoc( $useridResult );
	$boardUserid = $useridRow[userid];
}
?>