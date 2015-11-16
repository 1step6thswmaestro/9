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
			alert("회원 아이디를 입력하세요.");
			document.form1.id.focus();
			return;
		}
		if(document.form1.passwd.value.length==0) {
			alert("비밀번호를 입력하세요.");
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
		alert("로그인 페이지에 문제가 있습니다.\n\n쇼핑몰 운영자에게 문의하시기 바랍니다.");
	}
}

function CheckOrder() {
	if(document.form1.ordername.value.length==0) {
		alert("주문자 이름을 입력하세요.");
		document.form1.ordername.focus();
		return;
	}
	if(document.form1.ordercodeid.value.length==0) {
		alert("주문번호 6자리를 입력하세요.");
		document.form1.ordercodeid.focus();
		return;
	}
	if(document.form1.ordercodeid.value.length!=6) {
		alert("주문번호는 6자리입니다.\n\n다시 입력하세요.");
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
		<h2>회원 로그인</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
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
						<legend class="vc">로그인폼</legend>
						<label for="id">아이디</label><input type="text" name="id" title="아이디" placeholder="아이디" class="input_id" value="<?=$_COOKIE[save_id]?>">
						<label for="passwd">비밀번호</label><input type="password" name="passwd" title="비밀번호" placeholder="비밀번호" class="input_pw" value="<?=$save_pw?>">
						<button type="button" class="btn_login" onClick="CheckForm()"><span class="vc">로그인</span></button>
					</fieldset>

					<fieldset class="box2">
						<legend class="vc">아이디및 비밀번호 저장체크</legend>
						<input type="checkbox" id="id_check" name="id_check" class="input_check" value="Y" <? if(!empty($save_id)) echo "checked"; ?>><label for="id_check">아이디 저장</label>
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
					</form>

					<?if(substr($chUrl,-10) == "mypage.php"){?>
					<div style="padding-top:80px; background:url(/m/skin/default/img/ordersearch_bg_x2.png) no-repeat 0px 25px; background-size:auto 37px;">
					<fieldset class="box3">
						<form name="orderForm" action="./orderdetailpop.php" target="orderpop" method="post">
							<legend class="vc">비회원주문조회</legend>
							<label for="ordername">주문자명</label><input type="text" name="ordername" class="input_id" value=""/>
							<label for="ordercodeid">주문번호</label><input type="text" name="ordercodeid" class="input_pw" value=""/>
						</form>
						<button type="button" class="btn_search" onClick="javascript:order();"><span class="vc">비회원 주문조회</span></a>
					</fieldset>
					</div>
					<?}?>
					<div style="margin-top:10px;">
						<a href="member_agree.php" rel="external" class="button blue">회원가입</a>
						<a href="./findpwd.php" rel="external" class="button white">아이디/비밀번호 찾기</a>
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
			alert("주문자명을 입력해주세요.");
			_form.ordername.focus();
			return;
		}else if(_form.ordercodeid.value == ""){
			alert("주문번호를 입력해주세요.");
			_form.ordercodeid.focus();
			return;

		}
		window.open("about:blank","orderpop");
		document.orderForm.submit();
	}
</script>

<? include "footer.php"; ?>