<!-- 공지사항 -->
<section class="main_news">
	<div class="h_area"><h3>공지사항</h3><a href="notice_list.php" class="btn_more" rel="external"><span>더보기</span></a></div>
	<div  align="center">
	<ul class="list_type01">
	<?
	
	$noticelistSQL = "SELECT writetime,title,num,thread, writetime FROM tblboard WHERE board = 'notice' ORDER BY writetime DESC LIMIT 5 ";
	
	if(false !== $noticelistRes = mysql_query($noticelistSQL,get_db_conn())){
		$noticelistrowcount = mysql_num_rows($noticelistRes);

		if($noticelistrowcount>0){
			while($noticelistRow = mysql_fetch_assoc($noticelistRes)){
				$writetime = date('y.m.d',$noticelistRow['writetime']);
				$subject = _strCut($noticelistRow['title'],24,6,$charset);
				$num = $noticelistRow['num'];
				$thread = $noticelistRow['thread'];
	?>
		<li>
				<a href="javascript:noticeView('<?=$num?>','<?=$thread?>','notice');"><strong class="title"><?=$subject?></strong><span class="date"><?=$writetime?></span></a>
		</li>
	<?
			}

		}else{
	?>
		<li><a href="" rel="external"><strong class="title">등록된 공지사항이 없습니다.</strong><span class="date"></span></a></li>
	<?
		}

		mysql_free_result($noticelistRes);
	}

	?>
	</ul>
	</div>
</section>
<!-- //공지사항 -->