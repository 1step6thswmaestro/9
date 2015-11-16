<?
// 본인 인증 확인 관련 선 처리 부분
include_once './sci_pcc.class.php';

$sci_pcc = new sci_pcc();

$reqInfo = $sci_pcc->_reqInfo($_REQUEST['mode']);
?>
<html>
    <head>
        <title>서울신용평가정보 본인확인서비스</title>
        <meta http-equiv="Content-Type" content="text/html; charset=euc-kr">

<script language="javascript" type="text/javascript">
function scipcc(){
<? if(empty($reqInfo)){?>
	alert('본인 확인 요청 처리값 오류');
	//self.close();
<? }else{ ?>
	document.reqCBAForm.submit();
<? } ?>
}
//-->
</script>
</head>
<body onLoad="javascript:scipcc()">
<form name="reqCBAForm" method="post" action = "https://pcc.siren24.com/pcc_V3/jsp/pcc_V3_j10.jsp">
    <input type="hidden" name="reqInfo"     value = "<?=$reqInfo?>">
    <input type="hidden" name="retUrl"      value = "32http://<?=$_SERVER['HTTP_HOST']?>/sci/pcc/result.php">
</form>
</body>
</html>
