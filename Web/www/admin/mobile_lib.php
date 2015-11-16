<?
function gotoPage($url) 
{ echo"<meta http-equiv=\"refresh\" content=\"0; url=$url\">"; }

function alert($s)
{ echo "<script language=javaScript>alert('$s');</script>"; }

function selfClose()
{ echo "<script language=javaScript>alert();self.close();</script>"; }

function alertHistoryBack($s)
{	echo"	<script language=javaScript>alert('$s');	history.back();	</script>"; }

function historyBack()
{	echo"	<script language=javaScript>history.back();	</script>"; }

function locationHref($u)
{	echo"	<script language=javaScript>location.href='$u';</script>";  }

function alertLocationHref($s,$u)
{	echo"<script language=javaScript>alert('$s');location.href='$u';</script>"; }

function confirm($s)
{	echo"<script language=javaScript>	if(!confirm('$s')) {	history.back();	} </script>";  }

function openerReload()
{	echo"<script language=javaScript>opener.location.reload();</script>"; }

function parentReload()
{	echo"<script language=javaScript>parent.location.reload();</script>"; }

function parentOpenerReload()
{	echo"<script language=javaScript>parent.opener.location.reload();</script>"; }


function parentLocationHref($s)
{	echo"<script language=javaScript>parent.location.href='$s';</script>"; }


function mkThumbNail($srcImg, $savePath, $saveImgName, $maxX, $maxY)
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
	
imageJpeg($baseImg,$savePath.$saveImgName);

imageDestroy($img);
imageDestroy($baseImg);

}

function imgResize($img, $w, $h) 
{
	$lmWidth = $w;
	$lmHeight = $h;

	$imgInfo = @getImageSize($img);
	
	$imgWidth = $imgInfo[0];
	$imgHeight = $imgInfo[1];

	//경우1) 가로 세로 모두 클경우2) 가로만 클경우3)세로만 클경우	4) 모두 작을경우

	if($imgWidth > $lmWidth && $imgHeight > $lmHeight)
	{
		if(($lmWidth / $imgWidth) > ($lmHeight / $imgHeight))
		{
			//echo "가로 세로가 기준값보다 크고 가로가 더 축소 되었다.";
			$ratio = $lmHeight / $imgHeight;
		}
		else
		{
			//echo "가로 세로가 기준값보다 크고 세로가 더 축소 되었다.";
			
			$ratio = $lmWidth / $imgWidth;
		}
	}
	else if($imgWidth > $lmWidth)
	{
		//echo "가로만 기준값보다 크다.";
		$ratio = $lmWidth / $imgWidth;
	}
	else if($imgHeight > $lmHeight)
	{
		//echo "세로만 기준값보다 크다.";
		$ratio = $lmHeight / $imgHeight;
	}
	else //if($imgWidth <= $lmWidth && $imgHeight <= $lmHeight)
	{
		//echo "기준보다 모두 작다.";
		$ratio = 1;

	}
	
 $imgWidth = $imgWidth * $ratio;
 $imgHeight = $imgHeight * $ratio;

return array("$imgWidth","$imgHeight");

}



function getFileSize($fileSize)
{ 
	if($fileSize >= 1073741824) 
  { $fileSize = round($fileSize / 1073741824 * 100) / 100 . " GB";  } 
	elseif($fileSize >= 1048576) 
  { $fileSize = round($fileSize / 1048576 * 100) / 100 . " MB"; } 
	elseif($fileSize >= 1024) 
	{ $fileSize = round($fileSize / 1024 * 100) / 100 . " KB";  } 
	elseif($fileSize > 0) 
	{ $fileSize = $fileSize . " Byte"; }
	else{ $fileSize = ""; } 

	return $fileSize; 
} 

?>