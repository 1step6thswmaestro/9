<?

include_once("header.php");

$chUrl=trim(urldecode($_REQUEST["chUrl"]));

if(strlen($_ShopInfo->getMemid())>0) {	

	if (strlen($chUrl)>0) $onload=$chUrl;
	else		
		$onload="./main.php";
	//header("Location:".$onload);

	echo '<script>location.href="'.$onload.'";</script>';exit;
	
}


if(strpos($chUrl,"?") && (ereg("order.php",$chUrl) || ereg("order3.php",$chUrl))){
	$orderParm =  substr($chUrl, strpos($chUrl,"?"));
	$chUrl = substr($chUrl,0,strpos($chUrl,"?"));
}
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {

	try {
		if(document.form1.id.value.length==0) {
			alert("ȸ�� ���̵� �Է��ϼ���.");
			document.form1.id.focus();
			return;
		}
		if(document.form1.passwd.value.length==0) {
			alert("��й�ȣ�� �Է��ϼ���.");
			document.form1.passwd.focus();
			return;
		}
		document.form1.target = "";
		<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {?>
		if(typeof document.form1.ssllogin!="undefined"){
			if(document.form1.ssllogin.checked==true) {
				document.form1.target = "loginiframe";
				document.form1.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>login.php';
			}
		}
		<?}?>
		document.form1.submit();
	} catch (e) {
		alert("�α��� �������� ������ �ֽ��ϴ�.\n\n���θ� ��ڿ��� �����Ͻñ� �ٶ��ϴ�.");
	}
}

function CheckOrder() {
	if(document.form1.ordername.value.length==0) {
		alert("�ֹ��� �̸��� �Է��ϼ���.");
		document.form1.ordername.focus();
		return;
	}
	if(document.form1.ordercodeid.value.length==0) {
		alert("�ֹ���ȣ 6�ڸ��� �Է��ϼ���.");
		document.form1.ordercodeid.focus();
		return;
	}
	if(document.form1.ordercodeid.value.length!=6) {
		alert("�ֹ���ȣ�� 6�ڸ��Դϴ�.\n\n�ٽ� �Է��ϼ���.");
		document.form1.ordercodeid.focus();
		return;
	}
	document.form2.ordername.value=document.form1.ordername.value;
	document.form2.ordercodeid.value=document.form1.ordercodeid.value;
	window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
	document.form2.submit();
}
//-->
</SCRIPT>
<div id="content">
	<div class="h_area2">
		<h2>ȸ�� �α���</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>
	<section>
		<div class="loginTap" id="loginTap_1">
			<div class="login">
				<div class="login_wrap" style="position:relative;">
					<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
					<input type=hidden name=type value="">
					<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {?>
					<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
					<IFRAME id=loginiframe name=loginiframe style="display:none"></IFRAME>
					<?}?>
					<fieldset class="box1">
						<legend class="vc">�α�����</legend>
						<label for="id">���̵�</label><input type="text" name="id" title="���̵�" placeholder="���̵�" class="input_id" value="<?=$_COOKIE[save_id]?>">
						<label for="passwd">��й�ȣ</label><input type="password" name="passwd" title="��й�ȣ" placeholder="��й�ȣ" class="input_pw" value="<?=$save_pw?>">
						<button type="button" class="btn_login" onClick="CheckForm()"><span class="vc">�α���</span></button>
					</fieldset>

					<fieldset class="box2">
						<legend class="vc">���̵�� ��й�ȣ ����üũ</legend>
						<input type="checkbox" id="id_check" name="id_check" class="input_check" value="Y" <? if(!empty($save_id)) echo "checked"; ?>><label for="id_check">���̵� ����</label>
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
					</form>

					<?if(substr($chUrl,-10) == "mypage.php"){?>
					<div style="padding-top:80px; background:url(/m/skin/default/img/ordersearch_bg_x2.png) no-repeat 0px 25px; background-size:auto 37px;">
					<fieldset class="box3">
						<form name="orderForm" action="./orderdetailpop.php" target="orderpop" method="post">
							<legend class="vc">��ȸ���ֹ���ȸ</legend>
							<label for="ordername">�ֹ��ڸ�</label><input type="text" name="ordername" class="input_id" value=""/>
							<label for="ordercodeid">�ֹ���ȣ</label><input type="text" name="ordercodeid" class="input_pw" value=""/>
						</form>
						<button type="button" class="btn_search" onClick="javascript:order();"><span class="vc">��ȸ�� �ֹ���ȸ</span></a>
					</fieldset>
					</div>
					<?}?>
					<div style="margin-top:10px;">
						<a href="member_agree.php" rel="external" class="button blue">ȸ������</a>
						<a href="./findpwd.php" rel="external" class="button white">���̵�/��й�ȣ ã��</a>
					</div>
				</div>
			</div>
		</div>
	</section>
<div>
<script>document.form1.id.focus();</script>
<script>
	$(".input_pw").keydown(function(e){
		if(e.keyCode == 13){
			CheckForm();
		}
	});

	function order(){
		var _form = document.orderForm;
		if(_form.ordername.value == ""){
			alert("�ֹ��ڸ��� �Է����ּ���.");
			_form.ordername.focus();
			return;
		}else if(_form.ordercodeid.value == ""){
			alert("�ֹ���ȣ�� �Է����ּ���.");
			_form.ordercodeid.focus();
			return;

		}
		window.open("about:blank","orderpop");
		document.orderForm.submit();
	}
</script>

<? include "footer.php"; ?>