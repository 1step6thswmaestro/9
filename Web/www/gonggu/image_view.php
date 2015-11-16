<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
?>

<html>
<head><title>확대 이미지 보기</title></head>
<body>
<?
$image=$_REQUEST["image"];
$gongguimagepath=$Dir.DataDir."shopimages/gonggu/";


$size=GetImageSize($gongguimagepath.$image);
if($size[0]>=800) $size[0]=750;
if($size[1]>=600) $size[1]=550;
echo "<script>window.moveTo(10,10);window.resizeTo($size[0]+50,$size[1]+100);</script>";
echo "<center>";
echo "<a href=\"JavaScript:window.close()\"><img src=\"".$gongguimagepath.$image."\" border=0 width=$size[0] alt=\"클릭하시면 창이 닫힙니다\"></a>";
?>
</body>
</html>
