<?
	$url = $_GET['iurl'];
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
		<meta http-equiv="Cache-Control" content="no-cache" />
		<meta name="viewport" content="width=device-width" />
		<link rel="stylesheet" href="./css/common.css" />
		<style>
			body{margin:0px;padding:0px;border:0px;}
			img{width:100%;}
		</style>
	</head>
	<body>
		<img src="<?=$url?>" />

		<div style="text-align:center"><button onClick="self.close();" class="button white biggrounded">닫기</button></div>
	</body>
</html>