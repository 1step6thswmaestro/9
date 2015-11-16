<div id="content">

	<div class="h_area2">
		<h2>로그인</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<div class="login">
		<div class="login_wrap">
			<fieldset class="box1">
				<legend class="vc">로그인폼</legend>
				<label for="id">아이디</label><input type="text" name="id" title="아이디" placeholder="아이디" class="input_id" value="<?=$_COOKIE[save_id]?>">
				<label for="passwd">비밀번호</label><input type="password" name="passwd" title="비밀번호" placeholder="비밀번호" class="input_pw" value="<?=$save_pw?>">
				<button type="button" class="btn_login" onClick="CheckForm()"><span class="vc">로그인</span></button>
			</fieldset>

			<fieldset class="box2">
				<legend class="vc">아이디및 비밀번호 저장체크</legend>
				<input type="checkbox" id="id_check" name="id_check" class="input_check" value="Y" <? if(!empty($save_id)) echo "checked"; ?>><label for="id_check">아이디 저장</label>
				<!-- <input type="checkbox" id="pw_check"  name="pw_check" class="input_check" value="Y" <? if(!empty($save_pw)) echo "checked"; ?> ><label for="pw_check">비밀번호 저장</label> -->

				<?
					if(substr($chUrl,-9)=="order.php") {
						if($_data->member_buygrant=="U" && ( ereg("order.php",$chUrl) || ereg("order3.php",$chUrl) ) ) {
				?>
				<a href="order.php" rel="external" class="button blue bigrounded">비회원구매</a>
				<?
						}
					}
				?>

			</fieldset>

			<ul>
				<li>아이디/비밀번호 찾기는 PC버전에서 가능합니다.<!--<br /><a href="../front/findpwd.php" rel="external" class="button white medium">바로가기</a>--></li>
				<!-- <li>비회원이신가요? 회원이 되시면 빠른 신상품 정보와 각종 할인혜택을 받으실수 있습니다.<br />
				<a href="member_agree.php" rel="external" class="button white medium">회원가입</a>&nbsp;<?if(substr($chUrl,-9)=="order.php") {if($_data->member_buygrant=="U" && ( ereg("order.php",$chUrl) || ereg("order3.php",$chUrl) ) ) {echo '<a href="order.php" rel="external" class="button white medium"><img src="upload/btn_nomem_buy.png" /></a>';}}?></li> -->
			</ul>
			<div>
				<a href="member_agree.php" rel="external" class="button white">회원가입</a>
				<!-- <a href="../front/findpwd.php" rel="external" class="button white">아이디/비밀번호 찾기</a> -->
				<a href="./findpwd.php" rel="external" class="button white">아이디/비밀번호 찾기</a>
			</div>
		</div>
	</div>
</div>
