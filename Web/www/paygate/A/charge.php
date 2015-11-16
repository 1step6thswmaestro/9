<?
$sitecd=$_REQUEST["sitecd"];
$sitekey=$_REQUEST["sitekey"];
$escrow=$_REQUEST["escrow"];
$paymethod=$_REQUEST["paymethod"];
$goodname=$_REQUEST["goodname"];
$price=$_REQUEST["price"];
$ordercode=$_REQUEST["ordercode"];
$buyername=$_REQUEST["buyername"];
$buyermail=$_REQUEST["buyermail"];
$buyertel1=$_REQUEST["buyertel1"];
$buyertel2=$_REQUEST["buyertel2"];
$rpost=$_REQUEST["rpost"];
$raddr1=$_REQUEST["raddr1"];
$raddr2=$_REQUEST["raddr2"];
$quotafree=$_REQUEST["quotafree"];
$quotamonth=$_REQUEST["quotamonth"];
$quotaprice=$_REQUEST["quotaprice"];
$sitelogo=$_REQUEST["sitelogo"];
$rtnpgtype=$_REQUEST["rtnpgtype"];

$quotaopt="";
if($quotafree=="Y" && $quotaprice>=50000) {
	$quotaopt=$quotamonth;
}

//$price=1004;

if (empty($price) || $price==0) {
	echo "<html><head><title></title></head><body onload=\"alert('결제금액이 없습니다.');history.go(-1);\"></body></html>";exit;
}
if (empty($sitecd)) {
	echo "<html><head><title></title></head><body onload=\"alert('KCP 고유ID가 없습니다.');history.go(-1);\"></body></html>";exit;
}
if (empty($sitekey)) {
	echo "<html><head><title></title></head><body onload=\"alert('KCP 고유KEY가 없습니다.');history.go(-1);\"></body></html>";exit;
}
$disp_tax_choose ="N";
switch($paymethod) {
	case "C":
		$pay_method="100000000000";
		break;
	case "P":
		$pay_method="100000000000";
		break;
	case "O":
		$pay_method="001000000000";
		$disp_tax_choose = "Y";
		break;
	case "Q":
		$pay_method="001000000000";
		$disp_tax_choose = "Y";
		break;
	case "M":
		$pay_method="000010000000";
		break;
	case "V":
		$pay_method="010000000000";
		$disp_tax_choose = "Y";
		break;
}
?>

<html>
<head>
<title>결제</title>
<link href="css/sample.css" rel="stylesheet" type="text/css">
<script language='javascript' src='https://pay.kcp.co.kr/plugin/payplus.js'></script>
<script language='javascript'>
	StartSmartUpdate();
	function  jsf__pay( form ){
		var RetVal = false;
		if ( MakePayMessage( form ) == true ){
			RetVal = true ;
		}else{
			res_cd  = document.form1.res_cd.value ;
			res_msg = document.form1.res_msg.value ;
			parent.location.href='/front/basket.php';
			return;
		}
		return RetVal ;
	}
	function init_orderid(){
		var today = new Date();
		var year  = today.getFullYear();
		var month = today.getMonth() + 1;
		var date  = today.getDate();
		var time  = today.getTime();

		if(parseInt(month) < 10){
			month = "0" + month;
		}

		if(parseInt(date) < 10) {
			date = "0" + date;
		}
		setTimeout("onload_pay();",300);
		
	}
        
	function onload_pay(){
		if( jsf__pay(document.form1) )
			document.form1.submit();
	}

	document.onkeydown = CheckKeyPress;
	document.onkeyup = CheckKeyPress;
	function CheckKeyPress() {
		ekey = event.keyCode;

		try {
			if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
				event.keyCode = 0;
				return false;
			}
		} catch (e) {}
	}

	function PageResize() {
		if(document.all.table_body) {
			var oWidth = document.all.table_body.clientWidth;
			var oHeight = document.all.table_body.clientHeight;

			window.resizeTo(oWidth,oHeight);
			init_orderid();
		}
	}

</script>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 onLoad="PageResize();">

<form name=form1 method=post action="charge_result.php">
<?@INCLUDE ("charge.inc.php");?>
<input type=hidden name=site_cd   value="<?=$sitecd?>">
<input type=hidden name=site_key  value="<?=$sitekey?>">
<input type=hidden name=pay_mod value="<?=$escrow?>">
<input type=hidden name=ordr_idxx value="<?=$ordercode?>">
<input type=hidden name=pay_method value="<?=$pay_method?>">
<input type=hidden name=good_name value="<?=$goodname?>">
<input type=hidden name=good_mny value="<?=$price?>">
<input type=hidden name=buyr_name value="<?=$buyername?>">
<input type=hidden name=buyr_mail value="<?=$buyermail?>">
<input type=hidden name=buyr_tel1 value="<?=$buyertel1?>">
<input type=hidden name=buyr_tel2 value="<?=$buyertel2?>">
<input type=hidden name=quotaopt value="<?=$quotaopt?>">
<!--input type=hidden name=quotaopt value="12"-->
<input type=hidden name=skin value="original">
<input type=hidden name=site_logo value="<?=$sitelogo?>">
<input type=hidden name=site_name value="<?=$return_host?>">
<input type=hidden name=rtnpgtype value="<?=$rtnpgtype?>">

<input type='hidden' name='req_tx'    value='pay'>
<input type='hidden' name='module_type' value='01'>
<input type='hidden' name='currency' value='WON'>
<input type='hidden' name='escw_used' value='Y'>
<input type='hidden' name='deli_term' value='03'>
<input type='hidden' name='bask_cntx' value='1'>
<input type='hidden' name='good_info' value='seq=1<?=chr(31)?>ordr_numb=<?=$ordercode.chr(31)?>good_name=<?=urlencode($goodname).chr(31)?>good_cntx=1<?=chr(31)?>good_amtx=<?=$price?>'>
<input type='hidden' name='rcvr_name' value='<?=$buyername?>'>
<input type='hidden' name='rcvr_tel1' value='<?=$buyertel1?>'>
<input type='hidden' name='rcvr_tel2' value='<?=$buyertel2?>'>
<input type='hidden' name='rcvr_mail' value='<?=$buyermail?>'>
<input type='hidden' name='rcvr_zipx' value='<?=$rpost?>'>
<input type='hidden' name='rcvr_add1' value='<?=$raddr1?>'>
<input type='hidden' name='rcvr_add2' value='<?=$raddr2?>'>

<!-- 필수 항목 : PLUGIN에서 값을 설정하는 부분으로 반드시 포함되어야 합니다. ※수정하지 마십시오.-->
<input type='hidden' name='res_cd'         value=''>
<input type='hidden' name='res_msg'        value=''>
<input type='hidden' name='tno'            value=''>
<input type='hidden' name='trace_no'       value=''>
<input type='hidden' name='enc_info'       value=''>
<input type='hidden' name='enc_data'       value=''>
<input type='hidden' name='ret_pay_method' value=''>
<input type='hidden' name='tran_cd'        value=''>
<input type='hidden' name='bank_name'      value=''>
<input type='hidden' name='bank_issu'      value=''>
<input type='hidden' name='use_pay_method' value=''>

<!-- 가상계좌, 계좌이체시 결제창에서 현금영수증 선택 창 발생 -->
<?if($disp_tax_choose == "Y"){?>
<input type="hidden" name="disp_tax_yn"     value="Y"/>
<?}?>

<!-- 현금영수증 관련 정보 : PLUGIN 에서 내려받는 정보입니다 -->
<input type='hidden' name='cash_tsdtime'   value=''>
<input type='hidden' name='cash_yn'        value=''>
<input type='hidden' name='cash_authno'    value=''>
<?@INCLUDE ("chargeform.inc.php");?>
</form>
</body>
</html>