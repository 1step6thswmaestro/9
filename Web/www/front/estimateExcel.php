<?

// 견적서 엑셀

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");



$CurrentTime = date("YmdHis");

$export_file = "xlsfile://order_excel_".$CurrentTime.".xls";//다운로드할 파일 이름
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/x-msexcel");
header ("Content-Disposition: attachment; filename=\"" . basename($export_file) . "\"" );
header ("Content-Description: PHP/INTERBASE Generated Data" );

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

	</body>

</html>
