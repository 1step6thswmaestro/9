		<!-- �ϴ� -->
		<div class="footerMenu">
			<? if($_ShopInfo->getMemid() == ""){ ?>
				<a href="login.php" rel="external" class="button white bigroundedSE">�α���</a>
			<? }else{ ?>
				<a href="logout.php" rel="external" class="button white bigroundedSE">�α׾ƿ�</a>
			<? } ?>
			<? if($configRow['use_cross_link']=="Y") {?>
			<a href="../main/main.php?pc=ON" rel="external" class="button white bigroundedSE">PC����</a>
			<? } ?>
			<a href="#header" class="button white bigroundedSE">TOP����</a>
		</div>

		<footer id="footer">
			<div>
				<ul class="ft_menu">
					<li><a href="notice_list.php" rel="external">��������</a></li>
					<li><a href="customer.php" rel="external">������</a></li>
					<li><a href="agreement.php" rel="external">�̿���</a></li>
				</ul>
				<p class="copy"><?=$copyright?></p>
			</div>
		</footer>
	<!-- //�ϴ� -->
	</div>
</body>
</html>