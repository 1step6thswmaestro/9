<?
// 견적서

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


?>
<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
	<head>
		<meta http-equiv=Content-Type content="text/html; charset=ks_c_5601-1987">
		<?
			include "estimateStyle.php";
		?>
	</head>
	<body link=blue vlink=purple>
		<?
			include "estimateSheet.php";
		?>
		<table align="center" style="padding:10px;margin-top:40px;">
			<tr>
				<td><a href="/front/estimateExcel.php"><img src="/images/common/estimate/estimate_btn1.gif" border='0'></a></td>
				<td><a href="/front/estimateMail.php"><img src="/images/common/estimate/estimate_btn2.gif" border='0'></a></td>
				<td><a href="/front/estimatePrint.php"><img src="/images/common/estimate/estimate_btn3.gif" border='0'></a></td>
				<td><a href="javascript:self.close();"><img src="/images/common/estimate/estimate_btn4.gif" border='0'></a></td>
			</tr>
		</table>
	</body>
</html>
