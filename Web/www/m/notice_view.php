<?
	include_once("header.php");

	$num = isset($_GET['num'])?trim($_GET['num']):"";
	$thread = isset($_GET['thread'])?trim($_GET['thread']):"";
	$board = isset($_GET['board'])?trim($_GET['board']):"";

	if(empty($num) || empty($thread) || empty($board)){
		echo '<script>alert("잘못된 페이지 접근 입니다.");history.go(-1);</script>';exit;
	}

	//print_r($_GET);
	
	$noticeSQL = "SELECT *";
	$noticeSQL .= " FROM tblboard";
	$noticeSQL .= " WHERE board= '".$board."'";
	$noticeSQL .= " AND num = ".$num;
	$noticeSQL .= " AND thread = ".$thread;
	$noticeSQL .= " LIMIT 0, 1";
	
	//echo $noticeSQL;
	if(false !== $noticeRes = mysql_query($noticeSQL, get_db_conn())){
		$rowcount = mysql_num_rows($noticeRes);
		if($rowcount > 0){
			$noticeRow = mysql_fetch_assoc($noticeRes);
			//print_R($noticeRow);
			
			$subject = $noticeRow['title'];
			$contents = stripslashes($noticeRow['content']);
			$writetime = date('Y-m-d',$noticeRow['writetime']);
		}
		mysql_free_result($noticeRes);
	}

?>
<div id="content">
	<div class="h_area2">
		<h2>공지사항</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<style>
		.contentarea img {width:100%;}
	</style>

	<div id="board_view">
		<p class="title"><?=$subject?></p>
		<p class="writer"><?=$writetime?> <span class="hline">|</span> <?=$noticeRow['name']?> <span class="hline">|</span> <?=$noticeRow['access']?></p>

		<div class="snsbutton"><?//include_once('board_sns.php')?></div>
		<div class="bigview"><button class="button white medium" onClick="contentsView();">게시물 확대보기</button></div>

		<div id="contents_area" class="contentarea"><?=$contents?></div>
	</div>
	<div class="qna_view_bt">
		<a class="button black bigrounded" href="notice_list.php" rel="external">목록보기</a>
		<!-- <a class="button white bigrounded" href="./passwd_confirm.php?type=modify&num=<?=$view_num?>&board=<?=$view_board?>" rel="external">수정하기</a> -->
	</div>
</div>

<SCRIPT LANGUAGE="JavaScript">
<!--
	function contentsView(){
		var board = "<?=$board?>";
		var num = "<?=$num?>";
		var contenturl = "./board_view_contents.php?board="+board+"&num="+num;
		window.open(contenturl,"boardview","");
		return;
	}
//-->
</SCRIPT>

<?
	include_once("footer.php");
?>