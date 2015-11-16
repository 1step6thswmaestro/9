<?
//include_once('header.php'); 
?>

<div id="content">
	<div class="h_area2">
		<h2>이용약관</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	
	<!-- 이용약관 -->
	<div class="agreement">
		<!-- 관리자에서 입력한 내용 들어가게... -->
		<p><?=$row_agreement[0]?></p>
	</div>
	<!-- //이용약관 -->
	
</div>

<hr>

<? 
include_once('footer.php'); 
?>