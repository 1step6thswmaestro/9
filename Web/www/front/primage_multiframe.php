<?
$Dir="../";
include_once($Dir."lib/init.php");

if (strpos(getenv("HTTP_REFERER"),"productdetail.php")==false) exit;

$productcode=$_REQUEST["productcode"];
$size=$_REQUEST["size"];
$thumbtype=$_REQUEST["thumbtype"];

if(strlen($size)==0) $size=350;
else $size+=20;

$size2=76;
if($thumbtype==2) $size2=140;
?>
<html>
<head>
<title>상품확대보기</title>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
</head>
<frameset rows="<?=($size+10)?>px,<?=$size2?>px" border=0>
<frame src="<?=$Dir.FrontDir?>primage_multiframemain.php?productcode=<?=$productcode?>&size=<?=$size-20?>" name=main noresize scrolling=no marginwidth=0 marginheight=3>
<frame src="<?=$Dir.FrontDir?>primage_multiframethumb.php?productcode=<?=$productcode?>&maxsize=<?=$size-20?>" name=top noresize scrolling=no marginwidth=0 marginheight=0>
</frameset>
</html>
