<div id="content">

	<div class="h_area2">
		<h2>�α���</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>

	<div class="login">
		<div class="login_wrap">
			<fieldset class="box1">
				<legend class="vc">�α�����</legend>
				<label for="id">���̵�</label><input type="text" name="id" title="���̵�" placeholder="���̵�" class="input_id" value="<?=$_COOKIE[save_id]?>">
				<label for="passwd">��й�ȣ</label><input type="password" name="passwd" title="��й�ȣ" placeholder="��й�ȣ" class="input_pw" value="<?=$save_pw?>">
				<button type="button" class="btn_login" onClick="CheckForm()"><span class="vc">�α���</span></button>
			</fieldset>

			<fieldset class="box2">
				<legend class="vc">���̵�� ��й�ȣ ����üũ</legend>
				<input type="checkbox" id="id_check" name="id_check" class="input_check" value="Y" <? if(!empty($save_id)) echo "checked"; ?>><label for="id_check">���̵� ����</label>
				<!-- <input type="checkbox" id="pw_check"  name="pw_check" class="input_check" value="Y" <? if(!empty($save_pw)) echo "checked"; ?> ><label for="pw_check">��й�ȣ ����</label> -->

				<?
					if(substr($chUrl,-9)=="order.php") {
						if($_data->member_buygrant=="U" && ( ereg("order.php",$chUrl) || ereg("order3.php",$chUrl) ) ) {
				?>
				<a href="order.php" rel="external" class="button blue bigrounded">��ȸ������</a>
				<?
						}
					}
				?>

			</fieldset>

			<ul>
				<li>���̵�/��й�ȣ ã��� PC�������� �����մϴ�.<!--<br /><a href="../front/findpwd.php" rel="external" class="button white medium">�ٷΰ���</a>--></li>
				<!-- <li>��ȸ���̽Ű���? ȸ���� �ǽø� ���� �Ż�ǰ ������ ���� ���������� �����Ǽ� �ֽ��ϴ�.<br />
				<a href="member_agree.php" rel="external" class="button white medium">ȸ������</a>&nbsp;<?if(substr($chUrl,-9)=="order.php") {if($_data->member_buygrant=="U" && ( ereg("order.php",$chUrl) || ereg("order3.php",$chUrl) ) ) {echo '<a href="order.php" rel="external" class="button white medium"><img src="upload/btn_nomem_buy.png" /></a>';}}?></li> -->
			</ul>
			<div>
				<a href="member_agree.php" rel="external" class="button white">ȸ������</a>
				<!-- <a href="../front/findpwd.php" rel="external" class="button white">���̵�/��й�ȣ ã��</a> -->
				<a href="./findpwd.php" rel="external" class="button white">���̵�/��й�ȣ ã��</a>
			</div>
		</div>
	</div>
</div>
