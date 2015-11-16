<?
	INCLUDE ($Dir."paygate/paylist.inc.php");
?>

<html>
<head>
<title>결제</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
// 카드결제창을 호출한다.
/*function PaymentOpen() {
	var cval = getCookie("okpayment");
	if (cval!="result") {
		chargepop=window.open("<?=$pgurl?><?=$rtnpgtype?>","pgopen","width=100,height=100,status=yes,menubar=no,toolbar=no,location=no,scrollbars=no,directories=no");
		if (!chargepop) parent.ProcessWaitPayment();
	} else if (cval=="result") {
		alert("결제시스템과의 연결이 이미 끝났습니다.");
		history.go(1);
	}
}*/

function PaymentOpen() {
	var cval = getCookie("okpayment");
	if (cval!="result") {
		parent.PAY_PROCESS_IFRAME.location.href='<?=$pgurl?><?=$rtnpgtype?>';
	} else if (cval=="result") {
		alert("결제시스템과의 연결이 이미 끝났습니다.");
	}
}
//-->
</SCRIPT>
</head>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 onload="PaymentOpen()">

</body>
</html>
