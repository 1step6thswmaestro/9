<!DOCTYPE html>
<html>
	<head>
		<title><?=$shopname?> ���θ� - �����</title>
		<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />		
		<meta http-equiv="Cache-Control" content="no-cache" />
		<meta name="format-detection" content="telephone=no" />
		<!-- �ٷΰ��� ������ -->
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<link rel="apple-touch-icon-precomposed" href="./upload/<?=$icon?>" />
		<link rel="stylesheet" href="./css/common.css" />
		<? if($skinfile=="") { ?>
		<link rel="stylesheet" href="./css/skin/default.css" />
		<? } else { ?>
		<link rel="stylesheet" href="./css/skin/<?=$skinfile?>" />
		<? } ?>
		<link rel="stylesheet" href="./css/style.css" />
		<script type="text/javascript" src="/m/js/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="/m/js/common.js"></script>
		</head>
		<body>

			<div class="wrap">
				<!-- ��� -->
				<header id="header">
					<h1><? if (file_exists($logo)==true) {?>
					<a href="./" rel="external"><div class="tlogo" style="background:url(<?=$logo?>); background-position:0% 50%; background-repeat:no-repeat; background-size:auto 35px;"></div></a><? } ?></h1>
					<div class="search">
						<div class="topMenu">
							<a class="button white searchBT" href="productsearch.php" rel="external"><div class="searchButton"></div></a>
						</div>
					</div>
					<!-- <nav class="gnb">
						<ul>
							<li class="m1"><a href="category.php" rel="external"><span class="vc">ī�װ�</span></a></li>
							<li class="m2"><a href="mypage.php" rel="external"><span class="vc">����������</span></a></li>
							<li class="m3"><a href="basket.php" rel="external"><span class="vc">��ٱ���</span></a></li>
							<li class="m4"><a href="community.php" rel="external"><span class="vc">Ŀ�´�Ƽ</span></a></li>
						</ul>
					</nav> -->
					<nav id="nav_menu">
						<ul>
							<li><a href="category.php" rel="external"><span>ī�װ�</span></a></li>
							<li><a href="mypage.php" rel="external"><span>���̼���</span></a></li>
							<li><a href="basket.php" rel="external"><span>��ٱ���</span></a></li>
							<li><a href="community.php" rel="external"><span>Ŀ�´�Ƽ</span></a></li>
						</ul>
					</nav>
				</header>
			
				<!-- ��� �� -->