<?
	include_once("header.php");
	include_once($Dir."m/inc/paging_inc.php");

	$curpage=isset($_GET['page'])?trim($_GET['page']):1;
	$boardname = !_empty($_GET['board'])? trim($_GET['board']):"";
	$listnum = 10; // 페이지당 게시글 리스트 수

	if($boardname ==""){
		echo '<script>alert("잘못된 페이지 접근입니다.");history.go(-1);</script>';exit;
	}
	$boardsettingSQL = "SELECT grant_view, grant_write FROM tblboardadmin WHERE board = '".$boardname."' ";
	$boardsettingGrantView=$boardsettingGrantWrite="";
	if(false !== $boardsettingRes = mysql_query($boardsettingSQL,get_db_conn())){
		$boardsettingGrantView=$boardsettingGrantWrite="";
		$boardsettingGrantView = mysql_result($boardsettingRes,0,0);// 게시판 조회 권한 N: 회원비회원 목록,글보기 모두 가능, U: 비회원은 목록보기만 가능, Y: 회원만가능
		$boardsettingGrantWrite = mysql_result($boardsettingRes,0,0);// 게시판 쓰기 권한
	}

	if($boardsettingGrantView== "" || $boardsettingGrantView == "Y"){
		if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){
			echo '<script>alert("목록보기 권한이 없습니다.");history.go(-1);</script>';
			exit;
		}
	}

	$totallistSQL = "SELECT * FROM tblboard WHERE board = '".$boardname."' ";
	
	if(false !== $totallistRes = mysql_query($totallistSQL,get_db_conn())){
		$totallistrowcount = mysql_num_rows($totallistRes);
		mysql_free_result($totallistRes);
	}else{
		echo '<script>alert("게시판이 지정되지 않았습니다.");history.go(-1)</script>';exit;
	}
	
	$listSQL = "SELECT * FROM tblboard WHERE board ='".$boardname."' ORDER BY thread, pos ASC LIMIT ".($listnum * ($curpage - 1)) . ", " . $listnum;
?>
<div id="content">
	<div class="h_area2">
		<h2><?=$boardname?></h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<div class="boardwrap">
		<div class="rowcount">전체 <?=$totallistrowcount?>건의 게시물이 있습니다.</div>
		<?
			if($boardsettingGrantWrite == "Y" || $boardsettingGrantWrite == "A"){
				if($_ShopInfo->getMemid() != "" || $_ShopInfo->getMemid() != null){
		?>
		<div class="board_write_bt">
			<a class="button white medium" href="./customer_qna_write.php?board=qna" rel="external">글쓰기</a>
		</div>
		<?
				}
			}else if($boardsettingGrantWrite == "N"){
		?>
		<div class="board_write_bt">
			<a class="button white medium" href="./customer_qna_write.php?board=qna" rel="external">글쓰기</a>
		</div>
		<?}?>
		<div class="board_list">
			<?
				if(false !== $listRes = mysql_query($listSQL,get_db_conn())){
					$listrowcount = mysql_num_rows($listRes);
					if($listrowcount>0){
						while($listRow = mysql_fetch_object($listRes)){
							if(mb_strlen($listRow->title) > 21){
								$title = _strCut($listRow->title,21,5,$charset);
							}else{
								$title = $listRow->title;
							}
							if($listRow->is_secret == 1){
								$write_addr = "./passwd_confirm.php?type=view&num=".$listRow->num."&board=".$boardname;
							}else{
								$write_addr = "./board_view.php?num=".$listRow->num."&board=".$boardname;
							}

							unset($total_comment);
							/*if($listRow->total_comment >= 1){

								$total_comment = "<font color=\"#FF0000\">(".$listRow->total_comment.")</font>";
							}*/
			?>
						<a href="<?=$write_addr?>">
						<p class="title">
								<?if($listRow->pos >= 1){?>
									<img src="./images/re_mark.gif"/><?=$title?><?=$total_comment?>
								<?}else{
									if($listRow->is_secret == "0"){
										echo $title.$total_comment;
									}else{
								?>
									<img src="./images/lock.gif"/><?=$title?><?=$total_comment?>
								<?	}
								}?>
						</p>
						<p class="writer"><?=date("Y-m-d",$listRow->writetime)?> <span class="hline">|</span> <?=$listRow->name?> <span class="hline">|</span> <?=$listRow->access?></p>
						</a>
			<?
						}
					}else{
			?>
					<p class="err_td"><span>등록된 글이 없습니다.</span></p>
			<?
					}
				}
			?>
		</div>
	</div>

	<div id="page_wrap">
			<?
				$pageLink = $_SERVER['PHP_SELF']."?page=%u&board=".$boardname; // 링크
				$pagePerBlock = ceil($totallistrowcount/$listnum);
				$paging = new pages($pageparam);
				$paging->_init(array('page'=>$curpage,'total_page'=>$pagePerBlock,'links'=>$pageLink,'pageblocks'=>3))->_solv();
				echo $paging->_result('fulltext');
			?>
	</div>
</div>

<? include_once('footer.php'); ?>