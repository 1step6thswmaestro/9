<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once $Dir.'lib/class/coupon.php';

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

if($_data->coupon_ok!="Y") {
	echo "<html><head><title></title></head><body onload=\"alert('본 쇼핑몰에서는 쿠폰 기능을 지원하지 않습니다.');location.href='".$Dir.FrontDir."mypage.php'\"></body></html>";exit;
}

if($_POST['act'] == 'auth'){
	$coupon = new coupon();
	$return = $coupon->_authIssue($_REQUEST['couponcode']);
	if(!$return['result']){ ?>
	<script language="javascript" type="text/javascript">
	alert('<?=$return['msg']?>');
	window.close();
	</script>
<?	}else{ ?>
	<script language="javascript" type="text/javascript">
	alert('등록되었습니다.');
	opener.document.location.reload();
	window.close();
	</script>
<?	}
	exit;
}

$coupon = new coupon();
?>
<script language="javascript" type="text/javascript">
function checkForm(){
	var f = document.authCouponForm;
	if(f.couponcode.value.length == 0){
		alert('쿠폰 번호를 입력하세요.');
		return false;
	}
	if(f.couponcode.value.length < 10){
		alert('쿠폰 번호가 맞지 않습니다. 정확히 입력해주세요!');
		return false;
	}

	return true;
}
</script>
<body topmargin="0" leftmargin="0">
	<form name="authCouponForm" method="post" action="<?=$_SERVER['PHP_SELF']?>" onSubmit="javascript:return checkForm()">
	<input type="hidden" name="act" value="auth">

	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="2"><img src="/images/common/coupon_title.gif" alt="오프라인 쿠폰등록" /></td>
		</tr>
		<tr>
			<td align="right" style="height:28px;"><img src="/images/common/coupon_text.gif" alt="쿠폰번호 :" /></td>
			<td><input type="text" name="couponcode" value="" /></td>
		</tr>
		<tr>
			<td colspan="2" height="40" style="text-align:center">
				<input type="image" src="/images/common/coupon_insert.gif">
				<a href="javascript:close();"><img src="/images/common/bigview_btnclose.gif" border="0"></a>
			</td>
		</tr>
	</table>
	</form>
</body>