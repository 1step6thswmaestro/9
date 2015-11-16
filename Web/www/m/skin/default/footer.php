		<!-- 하단 -->
		<div class="footerMenu">
			<? if($_ShopInfo->getMemid() == ""){ ?>
				<a href="login.php" rel="external" class="button white bigroundedSE">로그인</a>
			<? }else{ ?>
				<a href="logout.php" rel="external" class="button white bigroundedSE">로그아웃</a>
			<? } ?>
			<? if($configRow['use_cross_link']=="Y") {?>
			<a href="../main/main.php?pc=ON" rel="external" class="button white bigroundedSE">PC버전</a>
			<? } ?>
			<a href="#header" class="button white bigroundedSE">TOP으로</a>
		</div>

		<footer id="footer">
			<div>
				<ul class="ft_menu">
					<li><a href="notice_list.php" rel="external">공지사항</a></li>
					<li><a href="customer.php" rel="external">고객센터</a></li>
					<li><a href="agreement.php" rel="external">이용약관</a></li>
				</ul>
				<p class="copy"><?=$copyright?></p>
			</div>
		</footer>
	<!-- //하단 -->
	</div>
</body>
</html>