<?	

	include "./inc/lib.php";


	$use_pay_method = "";
	$actionresult = "";
	$van_code = "";
	$returnPaymethod = "";
	switch($paymethod){
		case "C":  //�ſ�ī��
		$use_pay_method = "100000000000";
		$actionresult = "card";
		$van_code = "";
		$returnPaymethod = "CARD";
		break;
		case "V":  //������ü
		$use_pay_method="010000000000";
		$actionresult = "acnt";
		$van_code = "";
		$returnPaymethod = "BANK";
		break;
		case "O":  //�������
		$use_pay_method="001000000000";
		$actionresult = "vcnt";
		$van_code = "";
		$returnPaymethod = "VCNT";
		break;
	}
	
?>
<form name="frm" action="<?=$Dir?>m/paygate/A/order.php">
	<!-- �ֹ���ȣ -->
	<input type="hidden" name='ordr_idxx' value='<?=$ordercode?>'>
	<!-- good_name(��ǰ��) -->
	<input type="hidden" name='good_name' value='<?=$goodname?>'>
	<!-- hp_mny(�����ݾ�) -->
	<input type="hidden" name='good_mny' value='<?=$last_price?>' > 
	<!-- <input type="hidden" name='good_mny' size="9" maxlength="9" value='1004' >  -->
	<!-- buyr_name(�ֹ����̸�) -->
	<input type="hidden" name='buyr_name' value="<?=$sender_name?>">
	<!-- buyr_tel1(�ֹ��� ����ó) -->
	<input type="hidden" name='buyr_tel1' value='<?=$sender_tel?>'>
	<!-- buyr_tel2(�ֹ��� �ڵ��� ��ȣ) -->
	<input type="hidden" name='buyr_tel2' value='<?=$sender_tel?>'>
	<!-- buyr_mail(�ֹ��� E-mail) -->
	<input type="hidden" name='buyr_mail' value='<?=$sender_email?>'>
	<input type="hidden" name='van_code' value='<?=$van_code?>'>
	<input type="hidden" name='use_pay_method' value='<?=$use_pay_method?>'>
	<input type="hidden" name='actionresult' value='<?=$actionresult?>'>
	<input type="hidden" name='paymethod' value='<?=$returnPaymethod?>'>
</form>

<script>
	var f = document.frm;
	f.method = "post";
	f.submit();
</script>