<?
if(substr(getenv("SCRIPT_NAME"),-8)=="/top.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}
?>
<HTML>
	<HEAD>
		<TITLE><?=$_data->shoptitle?> - <?=$setup[board_name]?></TITLE>
		<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
		<META http-equiv="X-UA-Compatible" content="IE=5" />
		<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
		<META name="keywords" content="<?=$_data->shopkeyword?>">
		<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
		<?include($Dir."lib/style.php")?>

		<SCRIPT LANGUAGE="JavaScript">
			<!--
			function zoomImage(img,board) {
				if (img.length==0) {
					alert("확대보기 이미지가 없습니다.");
					return;
				}
				var tmp = "toolbar=no,menubar=no,resizable=no,status=no,scrollbars=yes";
				url = "/board/zoomimage.php?board="+board+"&image="+img;

				window.open(url,"zoomimage",tmp);
			}
			//-->
		</script>
	</HEAD>

	<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

	<?
		//echo $Dir.MainDir.$_data->menu_type.".php";
		include ($Dir.MainDir.$_data->menu_type.".php");
	?>

	<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<tr>
			<td align=center>
			<!-- 게시판 타이틀 및 바로가기 링크 -->
			<?=MakeBoardTop($setup, $designnewpageTables);?>