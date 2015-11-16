<? 
// 본인 인증 확인 관련 선 처리 부분								
include_once './sci_ipin.class.php';

$sci_ipin = new sci_ipin();
$reqInfo = $sci_ipin->_reqInfo();
?>
<html>
    <head>
        <title>서울신용평가정보 본인확인서비스</title>
        <meta http-equiv="Content-Type" content="text/html; charset=euc-kr">

<script language="javascript" type="text/javascript">
function scipcc(){
<? if(empty($reqInfo)){?>
	alert('I-Pin 확인 요청 처리값 오류');
	//self.close();
<? }else{ ?>
	document.reqCBAForm.submit();
<? } ?>
}
//-->
</script>
</head>	
<body onLoad="javascript:scipcc()">
<form name="reqCBAForm" method="post" action = "https://ipin.siren24.com/i-PIN/jsp/ipin_j10.jsp">
    <input type="hidden" name="reqInfo"     value = "<?=$reqInfo?>">
    <input type="hidden" name="retUrl"      value = "23http://<?=$_SERVER['HTTP_HOST']?>/sci/ipin/result.php?mode=<?=$_REQUEST['mode']?>">
</form>
</body>
</html>
