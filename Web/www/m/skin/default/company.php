<? include_once('header.php'); ?>

<div id="content">
	<div class="h_area2">
		<h2>ȸ��Ұ�</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>
	
	<!-- ȸ��Ұ� -->
	<div class="company">
		<!-- �����ڿ��� �Է��� ���� ����... -->		
		<pre><?=$row_company[content]?></pre>
	</div>
	<!-- //ȸ��Ұ� -->
	
</div>

<hr>

<? include_once('footer.php'); ?>