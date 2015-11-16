<?
// 견적서 프린트

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
	<script type="text/javascript">
	<!--
		print();
	//-->
	</script>
	<body link=blue vlink=purple>
		<?
			include "estimateSheet.php";
		?>
	</body>
</html>
