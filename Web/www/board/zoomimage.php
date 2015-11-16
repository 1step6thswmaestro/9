<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

	$image=$_REQUEST["image"];
	$board=$_REQUEST["board"];

	$imagepath=$Dir.DataDir."shopimages/board/".$board."/".$image;

	if(!file_exists($imagepath)) {
		echo "<script>window.close();</script>"; exit;
	}

	$size=GetImageSize($imagepath);
?>

<html>
	<head>
		<title>이미지 확대보기</title>
		<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
		<META http-equiv="X-UA-Compatible" content="IE=5" >
		<meta http-equiv="imagetoolbar" content="no">
		<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>

		<style>
			BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}
			tr {font-family:돋움; color:666666; font-size:9pt;}
			td {font-family:돋움; color:666666; font-size:9pt;}
		</style>

		<SCRIPT LANGUAGE="JavaScript">
			<!--
			var g_fIsSP2 = false;
			g_fIsSP2 = (window.navigator.userAgent.indexOf("SV1") != -1);

			window.moveTo(10,10);
			if (g_fIsSP2)
				window.resizeTo(<?=($size[0]+50)?>,<?=($size[1]+130)?>);
			else
				window.resizeTo(<?=($size[0]+50)?>,<?=($size[1]+120)?>);

			function WindowClose(){
				window.close();
			}
			//-->
		</SCRIPT>
	</head>

	<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" oncontextmenu="return false;">
		<?
			echo "<center>";
			echo "<table border=0 cellpadding=0 cellspacing=0 width=".$size[0]." height=".$size[1]." background=\"".$Dir.DataDir."shopimages/board/".$board."/".$image."\"><tr><td><a href=\"javascript:WindowClose()\"><img src=".$Dir."images/common/trans.gif border=0 width=".$size[0]." height=".$size[1]." alt=\"클릭하시면 창이 닫힙니다\"></a></td></tr></table>";
			echo "<br><a href=\"javascript:WindowClose()\"><img src=\"".$Dir."images/common/imageview_close.gif\" border=0></a>";
		?>
	</body>
</html>