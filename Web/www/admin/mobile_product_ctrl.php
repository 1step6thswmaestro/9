<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("mobile_lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "mo-1";
$MenuCode = "mobile";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################



switch($_GET[mode])
{
	case "mobile_display_y" :

		mysql_query("update tblproduct  set mobile_display = 'Y' where productcode = '$_GET[productcode]'");
		?>
		<script>
		//alert("추가하였습니다.");
		parent.location.reload();
		</script>
		<?
	break;

	case "mobile_display_n" :

		mysql_query("update tblproduct  set mobile_display = 'N' where productcode = '$_GET[productcode]'");
		?>
		<script>
			alert("목록에서 제거하였습니다.");
			parent.location.reload();
		</script>
		<?

	break;
	//메인구성상품 등록
	case "planning_write" :
		
		$result = mysql_query("select product_list from tblmobileplanningmain where pm_idx = '$_GET[pm_idx]'");
		$row = mysql_fetch_array($result);
		$state = "false";
		
		$mpr_list = explode("," , $row[product_list]);

		if(in_array(trim($productcode),$mpr_list)){
			$state="false";
		}else{
			
			if($row[product_list] == ""){
				$str_product_list = $productcode;
			}else{
				$str_product_list = $row[product_list].",".$productcode;
			}
			$state = "true";
			
		}

		if($state == "true"){
			mysql_query("update tblmobileplanningmain set product_list = '$str_product_list' where pm_idx = '$_GET[pm_idx]'");
		?>
		<script>
			alert("목록에 추가하였습니다.");
		//	parent.parent.opener.location.reload();
			parent.parent.opener.location.href="mobile_main_planning.php?pm_idx=<?=$_GET[pm_idx]?>";
		</script>
		<?
		}else{
			echo '<script>alert("이미 등록 된 상품입니다.");</script>';
		}
		?>

		<?

	break;

	//쇼핑몰의 이미지를  모바일에서도 그대로 사용할지 여부
	case "use_same_product_code" :
			mysql_query("update tblmobileconfig set use_same_product_code = '$_GET[use_same_product_code]'");
			alert("변경하였습니다.");
			parentReload();


	break;



	//쇼핑몰의 이미지를  모바일에서도 그대로 사용할지 여부
	case "use_same_product_image" :
			mysql_query("update tblmobileconfig set use_same_product_image = '$_GET[use_same_product_image]'");
			alert("변경하였습니다.");
			parentReload();


	break;


	case "resize_product_image" :

		//echo $chg_type;

		if($_GET[chg_type]=="all")
		{
			$query = "select * from tblproduct";
		}
		else
		{
			$query = "select maximage from tblproduct where mobile_display = 'Y'";
			$result = mysql_query($query);
			while($row = mysql_fetch_array($result))
			{
				$src_dir = "../data/shopimages/product/";
				$tg_dir = "../data/shopimages/mobile/product/";

				$src_image =  $src_dir.$row[maximage];
				$tg_image =  $tg_dir.$row[maximage];

				//$tmp_jpg = imagecreatefromjpeg($src_image);
				//imagejpeg($tmp_jpg,$tg_image);


				moveImage($src_image, $tg_image, 320, 10000);


			}
		}


	break;



}


function moveImage($srcImg, $saveImg, $maxX, $maxY)
{
	$imgInfo= @getImageSize($srcImg);

	$imgX	=	$imgInfo[0];
 	$imgY =	$imgInfo[1];
 	$imgType =	$imgInfo[2];

 	switch ($imgType)
	{
		case 1 :	$img = 	ImageCreateFromGif($srcImg); 	break;
		case 2 :	$img =	ImageCreateFromJPEG($srcImg);	break;
		case 3 :	$img =	ImageCreateFromPng($srcImg); break;
		default : return 0;
	}

	if($imgX > $maxX && $imgY > $maxY)
	{
		if(($maxX / $imgX) > ($maxY / $imgY))
		{	$ratio = $maxY / $imgY;}
		else
		{	$ratio = $maxX / $imgX;	}
	}
	else if($imgX > $maxX)
	{	$ratio = $maxX / $imgX;	}
	else if($imgY > $maxY)
	{	$ratio = $maxY / $imgY;	}
	else //if($imgX <= $maxX && $imgY <= $maxY)
	{	$ratio = 1;	}

	$imgX = $imgX * $ratio;
	$imgY = $imgY * $ratio;


	$positionX = ($maxX / 2) - ($imgX / 2);
	$positionY = ($maxY / 2) - ($imgY / 2);

	$baseImg =  @imageCreateTrueColor($maxX,$maxY) or imageCreate($maxX,$maxY);

	$bgColor = imageColorAllocate($baseImg, 255,255,255);
	ImageFilledRectangle($baseImg, 0, 0, $maxX, $maxY, $bgColor);
	imageCopyResampled($baseImg, $img, $positionX,$positionY,0,0, $imgX,$imgY,ImageSX($img),ImageSY($img));

imageJpeg($baseImg,$saveImg);

imageDestroy($img);
imageDestroy($baseImg);

}




?>