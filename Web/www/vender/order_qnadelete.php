<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

//상품QNA 게시판 존재여부 확인 및 설정정보 확인
$prqnaboard=getEtcfield($_venderdata->etcfield,"PRQNA");
if(strlen($prqnaboard)>0) {
	$sql = "SELECT * FROM tblboardadmin WHERE board='".$prqnaboard."' ";
	$result=mysql_query($sql,get_db_conn());
	$qnasetup=mysql_fetch_object($result);
	mysql_free_result($result);

	$qnasetup->btype=substr($qnasetup->board_skin,0,1);
	$qnasetup->max_filesize=$qnasetup->max_filesize*(1024*100);
	if($qnasetup->use_hidden=="Y") unset($qnasetup);
}

if(strlen($qnasetup->board)<=0) {
	echo "<html></head><body onload=\"alert('쇼핑몰 Q&A게시판 오픈이 안되었습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.');window.close();\"></body></html>";exit;
}

$mode=$_POST["mode"];
$exec=$_POST["exec"];
$num=$_POST["num"];
$board=$qnasetup->board;

if ($exec != "delete")	{
	$errmsg="잘못된 경로로 접근하셨습니다.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');window.close();\"></body></html>";exit;
}

$qry = "WHERE a.board='".$qnasetup->board."' ";
$qry.= "AND a.pridx=b.pridx AND b.vender='".$_VenderInfo->getVidx()."' ";

$sql = "SELECT a.*, b.productcode,b.productname,b.tinyimage,b.sellprice ";
$sql.= "FROM tblboard a, tblproduct b ".$qry." ";
$sql.= "AND a.num='".$num."' ";
$result=mysql_query($sql,get_db_conn());
if(!$qnadata=mysql_fetch_object($result)) {
	echo "<html></head><body onload=\"alert('해당 게시글이 존재하지 않습니다.');window.close();\"></body></html>";exit;
}
mysql_free_result($result);

if (strlen($_POST["up_passwd"])==0) {
	$errmsg="잘못된 경로로 접근하셨습니다.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');window.close();\"></body></html>";exit;
}

if ($qnadata->passwd!=$_POST["up_passwd"]) {
	$errmsg="비밀번호가 일치하지 않습니다.\\n\\n다시 확인 하십시오.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
}

if ($qnadata->pos <> 0) {
	// 게시물을 삭제하자
	$sql  = "DELETE FROM tblboard WHERE board='".$board."' AND num=".$num." ";
	$isUpdate = true;
} else {
	$sql2  = "SELECT COUNT(*) FROM tblboard WHERE board='".$board."' AND thread=".$qnadata->thread." ";
	$result2 = mysql_query($sql2,get_db_conn());
	$deleteTotal = mysql_result($result2,0,0);
	mysql_free_result($result2);

	if ($deleteTotal == 1) {
		$sql  = "DELETE FROM tblboard WHERE board='".$board."' AND num = ".$num." ";
		$isUpdate = true;
	} else {
		$delMsg = "관리자 또는 작성자에 의해 삭제되었습니다.";
		$sql  = "UPDATE tblboard SET ";
		$sql .= "prev_no = 0, ";
		$sql .= "next_no = 0, ";
		$sql .= "use_html = '0', ";
		$sql .= "title = '".$delMsg."', ";
		$sql .= "filename = '', ";
		$sql .= "total_comment = 0, ";
		$sql .= "content = '".$delMsg."', ";
		$sql .= "notice = '0', ";
		$sql .= "deleted = '1' ";
		$sql .= "WHERE board='".$board."' AND num=".$num." ";
	}
}
$delete = mysql_query($sql,get_db_conn());

if($delete) {
	if($qnadata->prev_no) mysql_query("UPDATE tblboard SET next_no='".$qnadata->next_no."' WHERE board='".$board."' AND next_no='".$qnadata->num."'",get_db_conn());
	if($qnadata->next_no) mysql_query("UPDATE tblboard SET prev_no='".$qnadata->prev_no."' WHERE board='".$board."' AND prev_no='".$qnadata->num."'",get_db_conn());

	// ===== 관리테이블의 게시글수 update =====
	unset($in_max_qry);
	unset($in_total_qry);
	if ($qnadata->pos == 0) {
		if ($qnadata->prev_no == 0) {
			$in_max_qry = "max_num = '".$qnadata->next_no."' ";
		}
	}
	if ($isUpdate) {
		$in_total_qry = "total_article = total_article - 1 ";
	}

	$sql3 = "UPDATE tblboardadmin SET ";
	if ($in_max_qry) $sql3.= $in_max_qry;
	if ($in_max_qry && $in_total_qry) $sql3.= ",".$in_total_qry;
	else if (!$in_max_qry && $in_total_qry) $sql3.= $in_total_qry;
	$sql3.= "WHERE board='".$board."' ";

	if ($in_max_qry || $in_total_qry) $update = mysql_query($sql3,get_db_conn());

	if ($qnasetup->use_comment=="Y" && $qnadata->total_comment > 0) {
		@mysql_query("DELETE FROM tblboardcomment WHERE board='".$board."' AND parent = '".$qnadata->num."'",get_db_conn());
	}

	if(strlen($qnadata->filename)>0) {
		include ($Dir.BoardDir."file.inc.php");
		$filedel=ProcessBoardFileDel($board,$qnadata->filename);
	}

	echo "<html><head><title></title></head><body onload=\"opener.listArticle();window.close();\"></body></html>";exit;
} else {
	$errmsg="글삭제 중 오류가 발생했습니다.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');window.close();\"></body></html>";exit;
}
