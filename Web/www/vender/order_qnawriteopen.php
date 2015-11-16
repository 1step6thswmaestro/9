<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

include ($Dir.BoardDir."file.inc.php");

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

function writeSecret($exec,$is_secret,$pos) {
	global $qnasetup;

	if ($exec == "reply") $disabled = "disabled";
	if ($exec == "modify" && $pos != "0") $disabled = "disabled";

	if($qnasetup->use_lock=="A") {
		echo "<select name=tmp_is_secret disabled>
			<option value=\"0\">사용안함</option>
			<option value=\"1\" selected>잠금사용</option>
			</select> &nbsp; <FONT COLOR=\"red\">자동잠금기능</FONT>
		";
	} else if($qnasetup->use_lock=="Y") {
		${"select".$is_secret} = "selected";
		echo "<select name=tmp_is_secret $disabled>
			<option value=\"0\" $select0>사용안함</option>
			<option value=\"1\" $select1>잠금사용</option>
			</select>
		";
	}
}



$exec=$_POST["exec"];
$num=$_POST["num"];
$board=$qnasetup->board;

if($exec=="modify" || $exec=="reply") {
	$qry = "WHERE a.board='".$qnasetup->board."' ";
	$qry.= "AND a.pridx=b.pridx AND b.vender='".$_VenderInfo->getVidx()."' ";

	$sql = "SELECT a.*, b.productcode,b.productname,b.tinyimage,b.sellprice,b.selfcode ";
	$sql.= "FROM tblboard a, tblproduct b ".$qry." ";
	$sql.= "AND a.num='".$num."' ";
	$result=mysql_query($sql,get_db_conn());
	if(!$qnadata=mysql_fetch_object($result)) {
		if($exec=="modify") $errmsg="수정할 게시글이 존재하지 않습니다.";
		else if($exec=="reply") $errmsg="답변할 게시글이 존재하지 않습니다.";
		echo "<html></head><body onload=\"alert('".$errmsg."');window.close();\"></body></html>";exit;
	}
	mysql_free_result($result);
}

if($exec=="modify") {
	if($qnasetup->use_article_care=="Y") {
		$errmsg="상품Q&A 게시판은 게시글 보호 기능을 사용중이므로 수정이 불가능합니다.\\n\\n쇼핑몰 운영자에게 문의하시기 바랍니다.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');window.close();\"></body></html>";exit;
	}

	if(($_POST[mode]=="up_result") && strlen($_POST[up_subject])>0) {
		if (strlen($_POST["up_passwd"])==0) {
			$errmsg="잘못된 경로로 접근하셨습니다.";
			echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');window.close();\"></body></html>";exit;
		}

		if ($qnadata->passwd!=$_POST["up_passwd"]) {
			$errmsg="비밀번호가 일치하지 않습니다.\\n\\n다시 확인 하십시오.";
			echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
		}

		$up_name = addslashes($up_name);
		$up_subject = str_replace("<!","&lt;!",$up_subject);
		$up_subject = addslashes($up_subject);
		$up_memo = str_replace("<!","&lt;!",$up_memo);
		$up_memo = addslashes($up_memo);

		if (!$up_is_secret) $up_is_secret = 0;

		if($setup[use_html]=="N") $up_html="";

		$sql  = "UPDATE tblboard SET ";
		$sql .= "name			= '".$up_name."', ";
		$sql .= "email			= '".$up_email."', ";
		$sql .= "is_secret		= '".$up_is_secret."', ";
		$sql .= "use_html		= '".$up_html."', ";
		$sql .= "title			= '".$up_subject."', ";
		if ($up_filename) {
			if(ProcessBoardFileModify($board,$up_filename,$qnadata->filename)=="SUCCESS") {
				$sql .= "filename	= '".$up_filename."', ";
			}
		}
		$sql .= "ip				= '".getenv("REMOTE_ADDR")."', ";
		$sql .= "content		= '".$up_memo."' ";
		$sql .= "WHERE board='".$board."' AND num = $num ";
		$insert = mysql_query($sql,get_db_conn());

		if($insert) {
			echo "<html><head><title></title></head><body onload=\"opener.viewArticle(".$num.");window.close();\"></body></html>";exit;
		} else {
			echo "<html><head><title></title></head><body onload=\"alert('글 수정중 오류가 발생하였습니다.');history.go(-1);\"></body></html>";exit;
		}
	} else {
		if (strlen($_POST["up_passwd"])==0) {
			$errmsg="잘못된 경로로 접근하셨습니다.";
			echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');window.close();\"></body></html>";exit;
		}

		if ($qnadata->passwd!=$_POST["up_passwd"]) {
			$errmsg="비밀번호가 일치하지 않습니다.\\n\\n다시 확인 하십시오.";
			echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
		}

		if (strlen($qnadata->filename)>0) {
			$thisBoard[filename] = "기존파일을 사용하려면 파일첨부 하지 마세요.";
		}

		$thisBoard[pos] = $qnadata->pos;
		$thisBoard[is_secret] = $qnadata->is_secret;
		$thisBoard[name] = stripslashes($qnadata->name);
		$thisBoard[passwd] = $qnadata->passwd;
		$thisBoard[email] = $qnadata->email;
		$thisBoard[title] = stripslashes($qnadata->title);
		$thisBoard[content] = stripslashes($qnadata->content);

		if ($qnadata->use_html == "1") $thisBoard[use_html] = "checked";

		//쓰기폼
		include ("order_qnawriteopen.inc.php");
	}
} else if($exec=="reply") {
	if($qnasetup->btype!="L") {
		$errmsg="본 게시판은 답변쓰기 기능이 지원되지 않습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');window.close();\"></body></html>";exit;
	}

	if($_POST[mode] == "up_result" && strlen($_POST[up_subject])>0) {		
		// ======== thread, pos, depth 정의 ========
		$sql = "UPDATE tblboard SET pos = pos+1 WHERE board='".$board."' AND thread='".$qnadata->thread."' ";
		$sql.= "AND pos>".$qnadata->pos." ";
		$update = mysql_query($sql,get_db_conn());

		if($qnasetup->use_html=="N") $up_html="";

		//메일용 변수
		$send_email = $up_email;
		$send_name = $up_name;
		$send_subject = $up_subject;
		$send_memo = stripslashes($up_memo);
		$send_filename= $up_filename;
		if (!$up_html) {
			$send_memo = nl2br(stripslashes($up_memo));
		}
		$send_date = date("Y-m-d H:i:s");

		$up_name = addslashes($up_name);
		$up_subject = str_replace("<!","&lt;!",$up_subject);
		$up_subject = addslashes($up_subject);
		$up_memo = str_replace("<!","&lt;!",$up_memo);
		$up_memo = addslashes($up_memo);

		if (!$up_is_secret) $up_is_secret = 0;

		if(ProcessBoardFileIn($board,$up_filename)!="SUCCESS") {
			$up_filename="";
		}

		$sql  = "INSERT tblboard SET ";
		$sql .= "board				= '".$board."', ";
		$sql .= "num				= '', ";
		$sql .= "thread				= '".$qnadata->thread."', ";
		$sql .= "pos				= '".($qnadata->pos+1)."', ";
		$sql .= "depth				= '".($qnadata->depth+1)."', ";
		$sql .= "prev_no			= '".$qnadata->prev_no."', ";
		$sql .= "next_no			= '".$qnadata->next_no."', ";
		$sql .= "pridx				= '".$qnadata->pridx."', ";
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

			// ===== 관리테이블의 게시글수 update =====
			$sql3 = "UPDATE tblboardadmin SET total_article=total_article+1 WHERE board='".$board."' ";
			$update = mysql_query($sql3,get_db_conn());

			if (strlen($row2[email])>0) {
				INCLUDE ($Dir.BoardDir."SendForm.inc.php");

				$title = $send_subject;
				$message = GetHeader() . GetContent($send_name, $send_email, $send_subject, $send_memo,$send_date,$send_filename,$setup[board_name]) . GetFooter();

				$tmp_admin_mail_list = split(",",$qnasetup->admin_mail);

				sendMailForm($send_name,$send_email,$message,$bodytext,$mailheaders);

				if (ismail($qnadata->email)) {
					mail($qnadata->email, $title, $bodytext, $mailheaders);
				}
			}

			echo "<html><head><title></title></head><body onload=\"opener.listArticle();window.close();\"></body></html>";exit;
		} else {
			echo "<html><head><title></title></head><body onload=\"alert('답변중 오류가 발생하였습니다.');history.go(-1);\"></body></html>";exit;
		}
	} else {
		$thisBoard[pos] = $qnadata->pos;
		$thisBoard[is_secret] = $qnadata->is_secret;
		$thisBoard[use_anonymouse] = $qnadata->use_anonymouse;
		$thisBoard[sitelink1] = $qnadata->sitelink1;
		$thisBoard[sitelink2] = $qnadata->sitelink2;
		$thisBoard[name] = "";
		$thisBoard[email] = "";
		$thisBoard[category] = $qnadata->category;

		$thisBoard[title] = stripslashes($qnadata->title);
		$thisBoard[summary] = stripslashes($qnadata->summary);

		$thisBoard[content] = stripslashes($qnadata->content);

		$thisBoard[title]    = "[답변]" . $thisBoard[title];

		$thisBoard[content]  = "\n\n\n'".stripslashes($qnadata->name)."'님이 쓰신글\n";
		$thisBoard[content] .= "------------------------------------\n";
		$thisBoard[content] .= ">" . str_replace(chr(10), chr(10).">", $qnadata->content) . "\n";
		$thisBoard[content] .= "------------------------------------\n";
		
		//쓰기폼
		include ("order_qnawriteopen.inc.php");
	}
} else {
	echo "<html><head><title></title></head><body onload=\"alert('잘못된 경로로 접근하셨습니다.');window.close();\"></body></html>";exit;
}