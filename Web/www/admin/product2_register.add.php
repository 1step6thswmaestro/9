<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################
$userspec_cnt=5;
$maxfilesize="512000";
$mode=$_POST["mode"];

$code="999000000000";
$prcode=$_POST["prcode"];
$productcode=$_POST["productcode"];
$productname=$_POST["productname"];
$vimage=$_POST["vimage"];
$vimage2=$_POST["vimage2"];
$vimage3=$_POST["vimage3"];

if(strlen($code)==12) {
	$sql = "SELECT type, list_type FROM tblproductcode WHERE codeA='".substr($code,0,3)."' ";
	$sql.= "AND codeB='".substr($code,3,3)."' ";
	$sql.= "AND codeC='".substr($code,6,3)."' AND codeD='".substr($code,9,3)."' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);
	if(!$row) exit;
	if(!ereg("X",$row->type)) exit;

	$type = $row->type;
	if (substr($row->list_type,0,1)=="B")
		$gongtype="Y";
	else
		$gongtype="N";

	if($gongtype=="Y") {
		$bgcolor="#E9E7F1";
		$list_title = "<B>가격 고정형 공동구매 상품목록</B>";
	} else {
		$bgcolor="#F0F0F0";
		$list_title = "<B>판매상품 목록</B>";
	}

} else {
	exit;
}


if(strlen($_POST["setcolor"])==0){
	$setcolor=$_COOKIE["setcolor"];
} else if($_COOKIE["setcolor"]!=$_POST["setcolor"]){
	setcookie("setcolor",$setcolor,0,"/".RootPath.AdminDir);
	$setcolor=$_POST["setcolor"];
} else {
	$setcolor=$_COOKIE["setcolor"];
}

#상품이미지 태두리 쿠키 설정
if ($_POST["imgborder"]=="Y" && $_COOKIE["imgborder"]!="Y") {
	setcookie("imgborder","Y",0,"/".RootPath.AdminDir);
} else if ($_POST["imgborder"]!="Y" && $_COOKIE["imgborder"]=="Y" && ($mode=="insert" || $mode=="modify")) {
	setcookie("imgborder","",time()-3600,"/".RootPath.AdminDir);
	$imgborder="";
} else {
	$imgborder=$_COOKIE["imgborder"];
}

#상품등록날짜 고정 쿠키 설정
if ($_COOKIE["insertdate_cook"]=="Y" && $insertdate!="Y" && $mode=="modify") {
	setcookie("insertdate_cook","",time()-3600,"/".RootPath.AdminDir);
	$insertdate_cook="";
} else if ($_COOKIE["insertdate_cook"]!="Y" && $insertdate=="Y" && $mode=="modify") {
	setcookie("insertdate_cook","Y",time()+2592000,"/".RootPath.AdminDir);
	$insertdate_cook="Y";
}

if(strlen($setcolor)==0) $setcolor="000000";
$rcolor=HexDec(substr($setcolor,0,2));
$gcolor=HexDec(substr($setcolor,2,2));
$bcolor=HexDec(substr($setcolor,4,2));
$quality = "90";


$etcapply_coupon=$_POST["etcapply_coupon"];
$etcapply_reserve=$_POST["etcapply_reserve"];
$etcapply_gift=$_POST["etcapply_gift"];
$etcapply_return=$_POST["etcapply_return"];
if($etcapply_coupon!="Y") $etcapply_coupon="N";
if($etcapply_reserve!="Y") $etcapply_reserve="N";
if($etcapply_gift!="Y") $etcapply_gift="N";
if($etcapply_return!="Y") $etcapply_return="N";


$popup=$_POST["popup"];
$option1=$_POST["option1"];
$option1_name=$_POST["option1_name"];
$option2=$_POST["option2"];
$option2_name=$_POST["option2_name"];
$consumerprice=$_POST["consumerprice"];
$buyprice=$_POST["buyprice"];
$sellprice=$_POST["sellprice"];
$assembleuse=$_POST["assembleuse"];
$production=$_POST["production"];
$keyword=$_POST["keyword"];
$quantity=$_POST["quantity"];
$checkquantity=$_POST["checkquantity"];
$reserve=$_POST["reserve"];
$reservetype=$_POST["reservetype"];
$package_num=$_POST["package_num"];
//$deli=$_POST["deli"];
$deli="F"; //상품권배송비 0원

if($deli=="Y")
	$deli_price=(int)$_POST["deli_price_value2"];
else
	$deli_price=(int)$_POST["deli_price_value1"];

if($deli=="H" || $deli=="F" || $deli=="G") $deli_price=0;
if($deli!="Y" && $deli!="F" && $deli!="G") $deli="N";
$display=$_POST["display"];
$addcode=$_POST["addcode"];
$option_price=ereg_replace(" ","",$_POST["option_price"]);
$option_price=substr($option_price,0,-1);
$madein=$_POST["madein"];
$model=$_POST["model"];
$brandname=$_POST["brandname"];
$opendate=$_POST["opendate"];
$selfcode=$_POST["selfcode"];
$bisinesscode=$_POST["bisinesscode"];
$optiongroup=$_POST["optiongroup"];
$imgcheck=$_POST["imgcheck"];
$bankonly=$_POST["bankonly"];
$deliinfono=$_POST["deliinfono"];
$setquota=$_POST["setquota"];
$miniq=$_POST["miniq"];
$maxq=$_POST["maxq"];
$insertdate=$_POST["insertdate"];
$localsave=$_POST["localsave"];
$content=$_POST["content"];
$dicker=$_POST["dicker"];
$dicker_text=$_POST["dicker_text"];

$userspec=$_POST["userspec"];
$specname=$_POST["specname"];
$specvalue=$_POST["specvalue"];

$group_check=$_POST["group_check"];
$group_code=$_POST["group_code"];

if($group_check=="Y" && count($group_code)>0) {
	$group_check="Y";
} else {
	$group_check="N";
	$group_code="";
}

unset($specarray);
if($userspec == "Y") {
	for($i=0; $i<$userspec_cnt; $i++) {
		$specarray[$i]=$specname[$i]."".$specvalue[$i];
	}
	$userspec = implode("=",$specarray);
} else {
	$userspec = "";
}

if(strlen($display)==0) $display='Y';

if((int)$opendate<1) $opendate="";

$searchtype=$_POST["searchtype"];
if(strlen($searchtype)==0) $searchtype=0;

$userfile = $_FILES["userfile"];
$userfile2 = $_FILES["userfile2"];
$userfile3 = $_FILES["userfile3"];

$use_imgurl=$_POST["use_imgurl"];
$userfile_url=$_POST["userfile_url"];
$userfile2_url=$_POST["userfile2_url"];
$userfile3_url=$_POST["userfile3_url"];
if($use_imgurl!="Y") {
	$userfile_url="";
	$userfile2_url="";
	$userfile3_url="";
}
$img_type=$_POST["img_type"];

$maxsize=130;
$makesize=130;

$card_splittype = $_shopdata->card_splittype;
$makesize=$_shopdata->primg_minisize;
$predit_type=$_shopdata->predit_type;
$maxsize=$makesize+10;
if(strpos(" ".$_shopdata->etctype,"IMGSERO=Y")) {
	$imgsero="Y";
}

if(substr($prcode,0,12)!=$code && strlen($prcode)>0) {
	$prcode="";
	$maxq="";
}
if (strlen($mode)==0) $maxq="";

if ($mode=="insert" || $mode=="modify") {
	$etctype = "";
	if ($bankonly=="Y") $etctype .= "BANKONLY";
	if ($deliinfono=="Y") $etctype .= "DELIINFONO=Y";
	if ($setquota=="Y") $etctype .= "SETQUOTA";
	if (strlen(substr($iconvalue,0,3))>0)       $etctype .= "ICON=".$iconvalue."";
	if ($dicker=="Y" && strlen($dicker_text)>0) $etctype .= "DICKER=".$dicker_text."";

	if ($miniq>1)       $etctype .= "MINIQ=".$miniq."";
	else if ($miniq<1){
		echo "<script>alert('최소구매수량 수량은 1개 보다 커야 합니다.');history.go(-1);</script>";exit;
	}
	if ($checkmaxq=="B" && $maxq>=1)        $etctype .= "MAXQ=".$maxq."";
	else if ($checkmaxq=="B" && $maxq<1){
		echo "<script>alert('최대구매수량 수량은 1개 보다 커야 합니다.');history.go(-1);</script>";exit;
	}

	if ($bankonly=="Y" && $setquota=="Y") {
		echo "<script>alert('현금전용결제와 무이자상점부담을 동시에 체크하실 수 없습니다.(결제수단이 서로 틀림)');";
		echo "history.go(-1);</script>";exit;
	}
} else {
	$bankonly="";
	$deliinfono="";
	$setquota="";
	$miniq="";
	$freedeli="N";
}

$imagepath=$Dir.DataDir."shopimages/product/";

if($mode=="delprdtimg"){
	$delarray = array (&$vimage,&$vimage2,&$vimage3);
	$delname = array ("maximage","minimage","tinyimage");
	if(strlen($delarray[$delprdtimg])>0 && file_exists($imagepath.$delarray[$delprdtimg])) {
		unlink($imagepath.$delarray[$delprdtimg]);
	}
	$sql = "UPDATE tblproduct SET $delname='' WHERE productcode = '".$prcode."' ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert('해당 상품이미지를 삭제하였습니다.');</script>";
}

if (strlen($option1)>0 && strlen($option1_name)>0) {
	$option1 = $option1_name.",".substr($option1,0,-1);
} else {
	$option1="";
}
if (strlen($option2)>0 && strlen($option2_name)>0) {
	$option2 = $option2_name.",".substr($option2,0,-1);
} else {
	$option2="";
}

$optcnt="";
$tempcnt=0;
if ($searchtype=="1") {
	for($i=0;$i<5;$i++){
		for($j=0;$j<10;$j++){
			if(strlen(trim($optnumvalue[$i][$j]))>0) {
				$optnumvalue[$i][$j]=(int)$optnumvalue[$i][$j];
				$tempcnt++;
			}
			$optcnt.=",".$optnumvalue[$i][$j];
		}
	}
}
if($tempcnt>0) $optcnt.=",";
else $optcnt="";

if($mode=="insert") {
	$sql = "SELECT MAX(productcode) as maxproductcode FROM tblproduct ";
	$sql.= "WHERE productcode LIKE '".$code."%' ";
	$result = mysql_query($sql,get_db_conn());
	if ($rows = mysql_fetch_object($result)) {
		if (strlen($rows->maxproductcode)==18) {
			$productcode = ((int)substr($rows->maxproductcode,12))+1;
			$productcode = sprintf("%06d",$productcode);
		} else if($rows->maxproductcode==NULL){
			$productcode = "000001";
		} else {
			echo "<script>alert('상품코드를 생성하는데 실패했습니다. 잠시후 다시 시도하세요.');";
			echo "history.go(-1);</script>";
			exit;
		}
		mysql_free_result($result);
	} else {
		$productcode = "000001";
	}
}

if ($mode=="insert") {
	$image_name = $code.$productcode;
} elseif ($mode=="modify") {
	$image_name = $prcode;
}

if($use_imgurl!="Y") {
	$file_size = $userfile[size]+$userfile2[size]+$userfile3[size];
} else {
	$file_size=0;
}

if($file_size < $maxfilesize) {
	if (strlen($reserve)==0) {
		$reserve=0;
	} else {
		$reserve=$reserve*1;
	}

	if ($reservetype!="Y") {
		$reservetype=="N";
	}

	$curdate = date("YmdHis");

	$productname = ereg_replace("\\\\'","''",$productname);
	$addcode = ereg_replace("\\\\'","''",$addcode);
	$content = ereg_replace("\\\\'","''",$content);

	$message="";

	if($use_imgurl!="Y") {
		if($imgcheck=="Y") $filename = array (&$userfile[name],&$userfile[name],&$userfile[name]); 
		else $filename = array (&$userfile[name],&$userfile2[name],&$userfile3[name]);
		$file = array (&$userfile[tmp_name],&$userfile2[tmp_name],&$userfile3[tmp_name]);
	} else {
		if($imgcheck=="Y") $filename = array (&$userfile_url,&$userfile_url,&$userfile_url); 
		else $filename = array (&$userfile_url,&$userfile2_url,&$userfile3_url);
		$file = array (&$userfile_url,&$userfile2_url,&$userfile3_url);
	}

	$vimagear = array (&$vimage,&$vimage2,&$vimage3);
	$imgnum = array ("","2","3");

	if($mode=="insert" || $mode=="modify"){
		
		if($_POST['img_type']=='2') {
			$imagepath22=$Dir.DataDir."shopimages/giftbg/";

			if(file_exists($imagepath22."gift_bg_01.jpg")) $files11 = "gift_bg_01.jpg";
			else if(file_exists($imagepath22."gift_bg_01.gif")) $files11 = "gift_bg_01.gif";
			$files21 = $imagepath22.$files11;

			if(file_exists($imagepath22."gift_bg_02.jpg")) $files12 = "gift_bg_02.jpg";
			else if(file_exists($imagepath22."gift_bg_02.gif")) $files12 = "gift_bg_02.gif";
			$files22 = $imagepath22.$files12;

			if(file_exists($imagepath22."gift_bg_03.jpg")) $files13 = "gift_bg_03.jpg";
			else if(file_exists($imagepath22."gift_bg_03.gif")) $files13 = "gift_bg_03.gif";
			$files23 = $imagepath22.$files13;

			$filename = array ($files11,$files12,$files13);
			$file = array ($files21,$files22,$files23);
			$_POST['img_type']=1;
			$ckss=1;
		}

		for($i=0;$i<3;$i++){
			if($use_imgurl!="Y") {
				if ($mode=="modify" && strlen($vimagear[$i])>0 && strlen($filename[$i])>0 && file_exists($imagepath.$vimagear[$i])) {
					unlink($imagepath.$vimagear[$i]);
				}

				if (strlen($filename[$i])>0 && file_exists($file[$i])) {
					$ext = strtolower(substr($filename[$i],strlen($filename[$i])-3,3));
					if ($ext=="gif" || $ext=="jpg") {
						$image[$i] = $image_name.$imgnum[$i].".".$ext;
						
						if($ckss==1) copy($file[$i],$imagepath.$image[$i]);
						else move_uploaded_file($file[$i],$imagepath.$image[$i]);
						chmod($imagepath.$image[$i],0664);						
					} else {
						$image[$i]="";
					}
				} else if($imgcheck=="Y" && strlen($filename[$i])>0) {
					$image[$i]=$image_name.$imgnum[$i].".".$ext;
					copy($imagepath.$image[0],$imagepath.$image[$i]);
				} else {
					$image[$i] = $vimagear[$i];
				}
			} else {
				if(strlen($filename[$i])>0 && strlen($file[$i])>0) {
					$image_url=eregi_replace("http://","",$file[$i]);
					$temp=explode("/",$image_url);
					$host=$temp[0];
					$path=eregi_replace($host,"",$image_url);

					$ext=substr(strrchr($image_url,"."),1);

					$is_upimage=true;
					if($ext=="gif" || $ext=="jpg") {
						$image[$i] = $image_name.$imgnum[$i].".".$ext;
						$fdata=getRemoteImageData($host,$path,$ext);

						if(strlen($fdata)>0) {
							$fp2=fopen($imagepath.$image[$i],"w");
							fputs($fp2,$fdata);
							fclose($fp2);
							chmod($imagepath.$image[$i],0664);
							$tempsize=@getimagesize($imagepath.$image[$i]);
							if($tempsize[0]>0 && $tempsize[1]>0 && (preg_match("/^(1|2)$/",$tempsize[2]))) {

							} else {
								@unlink($imagepath.$image[$i]);
								$is_upimage=false;
							}
						} else {
							$is_upimage=false;
						}
					} else {
						$is_upimage=false;
					}

					if($is_upimage==false) {
						$image[$i]="";
						$filename[$i]="";
					}
				} else if($imgcheck=="Y" && strlen($filename[$i])>0) {
					$image[$i]=$image_name.$imgnum[$i].".".$ext;
					copy($imagepath.$image[0],$imagepath.$image[$i]);
					chmod($imagepath.$image[$i],0664);
				} else {
					$image[$i] = $vimagear[$i];
				}
			}
		}

		if ($imgcheck=="Y" && strlen($filename[1])>0 && file_exists($imagepath.$image[1])) {
			$imgname=$imagepath.$image[1];
			$size=getimageSize($imgname);
			$width=$size[0];
			$height=$size[1];
			$imgtype=$size[2];
			$makesize1=240;
			if ($width>$makesize1 || $height>$makesize1) {
				if($imgtype==1)      $im = ImageCreateFromGif($imgname);
				else if($imgtype==2) $im = ImageCreateFromJpeg($imgname);
				else if($imgtype==3) $im = ImageCreateFromPng($imgname);
				if ($width>=$height) {
					$small_width=$makesize1;
					$small_height=($height*$makesize1)/$width;
				} else if($width<$height) {
					$small_width=($width*$makesize1)/$height;
					$small_height=$makesize1;
				}

				if ($imgtype==1) {
					$im2=ImageCreate($small_width,$small_height); // GIF일경우
					$white = ImageColorAllocate($im2, 255,255,255);
					imagefill($im2,1,1,$white);
					ImageCopyResized($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					imageGIF($im2,$imgname);
				} else if ($imgtype==2) {
					$im2=ImageCreateTrueColor($small_width,$small_height); // JPG일경우
					$white = ImageColorAllocate($im2, 255,255,255);
					imagefill($im2,1,1,$white);
					imagecopyresampled($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					imageJPEG($im2,$imgname,$quality);
				} else {
					$im2=ImageCreateTrueColor($small_width,$small_height); // PNG일경우
					$white = ImageColorAllocate($im2, 255,255,255);
					imagefill($im2,1,1,$white);
					imagecopyresampled($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					imagePNG($im2,$imgname);
				}

				ImageDestroy($im);
				ImageDestroy($im2);
			}
		}
		if (strlen($filename[2])>0 && file_exists($imagepath.$image[2])) {
			$imgname=$imagepath.$image[2];
			$size=getimageSize($imgname);
			$width=$size[0];
			$height=$size[1];
			$imgtype=$size[2];
			$makesize2=120;
			$changefile="Y";
			if($imgsero=="Y") $leftmax=$makesize2;
			else $leftmax=$maxsize;
			if ($width>$maxsize || $height>$leftmax) {
				if($imgtype==1)      $im = ImageCreateFromGif($imgname);
				else if($imgtype==2) $im = ImageCreateFromJpeg($imgname);
				else if($imgtype==3) $im = ImageCreateFromPng($imgname);
				if ($width>=$height) {
					$small_width=$makesize;
					$small_height=($height*$makesize)/$width;
				} else if ($width<$height) {
					if ($imgsero=="Y") {
						$temwidth=$width;$temheight=$height;
						if ($temwidth>$makesize) {
							$temheight=($temheight*$makesize)/$temwidth;
							$temwidth=$makesize;
						}
						if ($temheight>$makesize2) {
							$temwidth=($temwidth*$makesize2)/$temheight;
							$temheight=$makesize2;
						}
						$small_width=$temwidth; $small_height=$temheight;
					} else {
						$small_width=($width*$makesize)/$height; $small_height=$makesize;
					}
				}

				if ($imgtype==1) {
					$im2=ImageCreate($small_width,$small_height); // GIF일경우
					// 홀수픽셀의 경우 검은줄을 흰색으로 바꾸기위해.
					$white = ImageColorAllocate($im2, 255,255,255); 
					imagefill($im2,1,1,$white);
					//$color = ImageColorAllocate ($im2, 0, 0, 0);
					$color =ImageColorAllocate($im2,$rcolor,$gcolor,$bcolor);
					ImageCopyResized($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					if($imgborder=="Y") imagerectangle ($im2, 0, 0, $small_width-1, $small_height-1,$color );
					imageGIF($im2,$imgname);
				} else if ($imgtype==2) {
					$im2=ImageCreateTrueColor($small_width,$small_height); // JPG일경우
					$white = ImageColorAllocate($im2, 255,255,255);
					imagefill($im2,1,1,$white);
					$color =ImageColorAllocate($im2,$rcolor,$gcolor,$bcolor);
					imagecopyresampled($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					if($imgborder=="Y") imagerectangle ($im2, 0, 0, $small_width-1, $small_height-1,$color );
					imageJPEG($im2,$imgname,$quality);
				} else {
					$im2=ImageCreateTrueColor($small_width,$small_height); // PNG일경우
					$white = ImageColorAllocate($im2, 255,255,255);
					imagefill($im2,1,1,$white);
					$color =ImageColorAllocate($im2,$rcolor,$gcolor,$bcolor);
					imagecopyresampled($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					if($imgborder=="Y") imagerectangle ($im2, 0, 0, $small_width-1, $small_height-1,$color );
					imagePNG($im2,$imgname);
				}

				ImageDestroy($im);
				ImageDestroy($im2);
			} else if($imgborder=="Y") {
				if($imgtype==1)      $im = ImageCreateFromGif($imgname);
				else if($imgtype==2) $im = ImageCreateFromJpeg($imgname);
				else if($imgtype==3) $im = ImageCreateFromPng($imgname);
				if ($imgtype==1) {
					$color = ImageColorAllocate($im,$rcolor,$gcolor,$bcolor);
					//$color = ImageColorAllocate ($im, 0, 0, 0);
					imagerectangle ($im, 0, 0, $width-1, $height-1,$color );
					imageGIF($im,$imgname);
				} else if ($imgtype==2) {
					$color = ImageColorAllocate($im,$rcolor,$gcolor,$bcolor);
					imagerectangle ($im, 0, 0, $width-1, $height-1,$color );
					imageJPEG($im,$imgname,$quality);
				} else {
					$color = ImageColorAllocate($im,$rcolor,$gcolor,$bcolor);
					imagerectangle ($im, 0, 0, $width-1, $height-1,$color );
					imagePNG($im,$imgname);
				}
				ImageDestroy($im);
			}
		}
		if($checkquantity=="F") $quantity="NULL";
		else if($checkquantity=="E") $quantity=0;
		else if($checkquantity=="A") $quantity=-9999;
		if ($searchtype=="3") {
			if($optiongroup>0) {
				$option1="[OPTG".$optiongroup."]";
				$option2="";
				$option_price="";
				$optcnt="";
			}
		}
	} else if($mode=="delete"){
		for($i=0;$i<3;$i++){
			if(strlen($vimagear[$i])>0 && file_exists($imagepath.$vimagear[$i]))
				unlink($imagepath.$vimagear[$i]);
		}
	}

	####################### 타서버 이미지 쇼핑몰에 저장 #############################
	unset($arrimgurl);
	unset($arrsavefilename);
	$in_content=$content;
	if($mode=="insert" || $mode=="modify") {
		if($localsave=="Y") {
			$imagesavepath=$Dir.DataDir."design/etc/";
			if(is_dir($imagesavepath)==false) {
				mkdir($imagesavepath);
				chmod($imagesavepath,0755);
			}
			$arrimgurl=array();
			$cln=explode("\n", $in_content);
			for($i=0;$i<count($cln);$i++) {
				while(eregi("[^= \"']*\.(gif|jpg|bmp|png)([\"|\'| |>]){1}", $cln[$i], $imgval)){
					$arrimgurl[substr($imgval[0],0,-1)]=substr($imgval[0],0,-1);
					$cln[$i]=str_replace(substr($imgval[0],0,-1),"",$cln[$i]);
				}
			}
			if(count($arrimgurl)>0) {
				while(list($key,$val)=each($arrimgurl)) {
					$file_url=urldecode($val);
					if(substr($file_url,0,7)=="http://") {
						$file_url=eregi_replace("http://","",$file_url);
						$temp=explode("/",$file_url);
						$host=$temp[0];
						$path=eregi_replace($host,"",$file_url);

						$filename=substr(strrchr($file_url,"/"),1);
						$j=0;
						while(file_exists($imagesavepath.$filename)){
							$file_ext=substr( strrchr($filename,"."),1);
							$file_name=substr($filename, 0, strlen($filename) - strlen(strrchr($filename,".")));
							$file_name=substr($file_name, 0, strlen($file_name) - strlen(strrchr($file_name,"[")));

							$filename=$file_name."[".$j."].".$file_ext;
							$j++ ;
						}

						$ext=substr(strrchr($filename,"."),1);

						$fdata=getRemoteImageData($host,$path,$ext);

						if(strlen($fdata)>0) {
							$filepath=$imagesavepath.$filename;
							$fp=@fopen($filepath,"w");
							@fputs($fp,$fdata);
							@fclose($fp);
							@chmod($filepath,0604);

							$size=@getimagesize($filepath);

							if($size[0]>0 && $size[1]>0 && (preg_match("/^(1|2|3|6)$/",$size[2]))) {
								$arrsavefilename[]=$filename;
								$in_content=@str_replace($val,"/".RootPath.DataDir."design/etc/".$filename,$in_content);
							} else {
								@unlink($filepath);
							}
						}
					}
				}
			}
		}
	}

	if ($mode=="insert") {
		if(strlen($buyprice) < 1 ) $buyprice = 0 ;
		$result = mysql_query("SELECT COUNT(*) as cnt FROM tblproduct ",get_db_conn());
		if ($row=mysql_fetch_object($result)) $cnt = $row->cnt;
		else $cnt=0;
		mysql_free_result($result);

		if($assembleuse=="Y") {
			$sellprice=0;
			$option1="";
			$option2="";
			$option_price="";
			$optcnt="";
			$package_num="0";
		}

		$sql = "INSERT tblproduct SET ";
		$sql.= "productcode		= '".$code.$productcode."', ";
		$sql.= "productname		= '".$productname."', ";
		$sql.= "assembleuse		= '".$assembleuse."', ";
		$sql.= "assembleproduct	= '', ";
		$sql.= "sellprice		= ".$sellprice.", ";
		$sql.= "consumerprice	= ".$consumerprice.", ";
		$sql.= "buyprice		= ".$buyprice.", ";
		$sql.= "reserve			= '".$reserve."', ";
		$sql.= "reservetype		= '".$reservetype."', ";
		$sql.= "production		= '".$production."', ";
		$sql.= "madein			= '".$madein."', ";
		$sql.= "model			= '".$model."', ";
		$sql.= "opendate		= '".$opendate."', ";
		$sql.= "selfcode		= '".$selfcode."', ";
		$sql.= "bisinesscode	= '".$bisinesscode."', ";
		$sql.= "quantity		= ".$quantity.", ";
		$sql.= "group_check		= '".$group_check."', ";
		$sql.= "keyword			= '".$keyword."', ";
		$sql.= "addcode			= '".$addcode."', ";
		$sql.= "userspec		= '".$userspec."', ";
		$sql.= "maximage		= '".$image[0]."', ";
		$sql.= "minimage		= '".$image[1]."', ";
		$sql.= "tinyimage		= '".$image[2]."', ";
		$sql.= "option_price	= '".$option_price."', ";
		$sql.= "option_quantity	= '".$optcnt."', ";
		$sql.= "option1			= '".$option1."', ";
		$sql.= "option2			= '".$option2."', ";
		$sql.= "etctype			= '".$etctype."', ";
		$sql.= "deli_price		= '".$deli_price."', ";
		$sql.= "deli			= '".$deli."', ";
		$sql.= "package_num		= '".(int)$package_num."', ";

		$sql.= "etcapply_coupon	= '".$etcapply_coupon."', ";
		$sql.= "etcapply_reserve= '".$etcapply_reserve."', ";
		$sql.= "etcapply_gift	= '".$etcapply_gift."', ";
		$sql.= "etcapply_return	= '".$etcapply_return."', ";

		$sql.= "display			= '".$display."', ";
		$sql.= "date			= '".$curdate."', ";
		$sql.= "regdate			= now(), ";
		$sql.= "modifydate		= now(), ";
		$sql.= "img_type		= '".$img_type."', ";
		$sql.= "content			= '".$in_content."' ";
		if($insert = mysql_query($sql,get_db_conn())) {
			if(strlen($brandname)>0) { // 브랜드 관련 처리
				$result = mysql_query("SELECT bridx FROM tblproductbrand WHERE brandname = '".$brandname."' ",get_db_conn());
				if ($row=mysql_fetch_object($result)) {
					@mysql_query("UPDATE tblproduct SET brand = '".$row->bridx."' WHERE productcode = '".$code.$productcode."'",get_db_conn());
				} else {
					$sql = "INSERT tblproductbrand SET brandname = '".$brandname."'";
					if($brandinsert = @mysql_query($sql,get_db_conn())) {
						$bridx = @mysql_insert_id(get_db_conn());
						if($bridx>0) {
							@mysql_query("UPDATE tblproduct SET brand = '".$bridx."' WHERE productcode = '".$code.$productcode."'",get_db_conn());
						}
					}
				}
				mysql_free_result($result);
			}

			if($group_check=="Y" && count($group_code)>0) {
				for($i=0; $i<count($group_code); $i++) {
					$sql = "INSERT tblproductgroupcode SET ";
					$sql.= "productcode = '".$code.$productcode."', ";
					$sql.= "group_code = '".$group_code[$i]."' ";
					mysql_query($sql,get_db_conn());
				}
			}

			$content=$in_content;
			$use_imgurl="";
			$userfile_url="";
			$userfile2_url="";
			$userfile3_url="";

			if($popup=="YES") {
				$onload="<script>alert(\"상품이 등록이 완료되었습니다.$message\");</script>";
			} else {
				$onload="<script>parent.ProductListReload('".$code."');parent.HiddenFrame.alert(\"상품이 등록이 완료되었습니다.$message\");</script>";
			}

			$log_content = "## 상품입력 ## - 코드 $code$productcode - 상품 : $productname 코디/조립 : $assembleuse 패키지그룹 : $package_num 가격 : $sellprice 수량 : $quantity 기타 : $etctype 적립금: $reserve $display";
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		} else {
			if($popup=="YES") {
				$onload="<script>alert(\"상품 등록중 오류가 발생하였습니다.\");</script>";
			} else {
				$onload="<script>parent.HiddenFrame.alert(\"상품 등록중 오류가 발생하였습니다.\");</script>";
			}
		}
		$prcode=$code.$productcode;
	} else if ($mode=="delete") {
		$sql = "SELECT vender,display,brand,pridx,assembleuse,assembleproduct FROM tblproduct WHERE productcode = '".$prcode."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		mysql_free_result($result);
		$vender=(int)$row->vender;
		$vdisp=$row->display;
		$brand=$row->brand;
		$vpridx=$row->pridx;
		$vassembleuse=$row->assembleuse;
		$vassembleproduct=$row->assembleproduct;

		#태그관련 지우기
		$sql = "DELETE FROM tbltagproduct WHERE productcode = '".$prcode."'";
		mysql_query($sql,get_db_conn());

		#리뷰 지우기
		$sql = "DELETE FROM tblproductreview WHERE productcode = '".$prcode."'";
		mysql_query($sql,get_db_conn());

		#위시리스트 지우기
		$sql = "DELETE FROM tblwishlist WHERE productcode = '".$prcode."'";
		mysql_query($sql,get_db_conn());

		#관련상품 지우기
		$sql = "DELETE FROM tblcollection WHERE productcode = '".$prcode."'";
		mysql_query($sql,get_db_conn());
		
		$sql = "DELETE FROM tblproducttheme WHERE productcode = '".$prcode."'";
		mysql_query($sql,get_db_conn());

		$sql = "DELETE FROM tblproduct WHERE productcode = '".$prcode."'";
		mysql_query($sql,get_db_conn());

		$sql = "DELETE FROM tblproductgroupcode WHERE productcode = '".$prcode."'";
		mysql_query($sql,get_db_conn());

		if($vassembleuse=="Y") {
			$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
			$sql.= "WHERE productcode = '".$prcode."' ";
			$result = mysql_query($sql,get_db_conn());
			if($row = @mysql_fetch_object($result)) {
				$sql = "DELETE FROM tblassembleproduct WHERE productcode = '".$prcode."' ";
				mysql_query($sql,get_db_conn());
				
				if(strlen(str_replace("","",$row->assemble_pridx))>0) {
					$sql = "UPDATE tblproduct SET ";
					$sql.= "assembleproduct = REPLACE(assembleproduct,',".$prcode."','') ";
					$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
					$sql.= "AND assembleuse != 'Y' ";
					mysql_query($sql,get_db_conn());
				}
			}
			mysql_free_result($result);
		} else {
			if(strlen($vassembleproduct)>0) {
				$sql = "SELECT productcode, assemble_pridx FROM tblassembleproduct ";
				$sql.= "WHERE productcode IN ('".str_replace(",","','",$vassembleproduct)."') ";
				$result = mysql_query($sql,get_db_conn());
				while($row = @mysql_fetch_object($result)) {
					$sql = "SELECT SUM(sellprice) as sumprice FROM tblproduct ";
					$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
					$sql.= "AND display ='Y' ";
					$sql.= "AND assembleuse!='Y' ";
					$result2 = mysql_query($sql,get_db_conn());
					if($row2 = @mysql_fetch_object($result2)) {
						$sql = "UPDATE tblproduct SET sellprice='".$row2->sumprice."' ";
						$sql.= "WHERE productcode = '".$row->productcode."' ";
						$sql.= "AND assembleuse='Y' ";
						mysql_query($sql,get_db_conn());
					}
					mysql_free_result($result2);
				}
			}

			$sql = "UPDATE tblassembleproduct SET ";
			$sql.= "assemble_pridx=REPLACE(assemble_pridx,'".$vpridx."',''), ";
			$sql.= "assemble_list=REPLACE(assemble_list,',".$vpridx."','') ";
			mysql_query($sql,get_db_conn());
		}

		if($vender>0) {
			//미니샵 테마코드에 등록된 상품 삭제
			setVenderThemeDeleteNor($prcode, $vender);
			setVenderCountUpdateMin($vender, $vdisp);

			$tmpcodeA=substr($prcode,0,3);
			$sql = "SELECT COUNT(*) as cnt FROM tblproduct ";
			$sql.= "WHERE productcode LIKE '".$tmpcodeA."%' AND vender='".$vender."' ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			$prcnt=$row->cnt;
			mysql_free_result($result);

			if($prcnt==0) {
				setVenderDesignDeleteNor($tmpcodeA, $vender);
				$imagename=$Dir.DataDir."shopimages/vender/".$vender."_CODE10_".$tmpcodeA.".gif";
				@unlink($imagename);
			}
		}

		$log_content = "## 상품삭제 ## - 상품코드 $prcode - 상품명 : ".urldecode($productname)." $display";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

		delProductMultiImg("prdelete","",$prcode);

		if ($popup=="YES") {
			$onload="<script>alert(\"상품 삭제가 완료되었습니다.\");window.close();opener.location.reload()</script>";
		} else {
			$onload="<script>parent.ProductListReload('".$code."');parent.HiddenFrame.alert(\"상품 삭제가 완료되었습니다.\");</script>";
		}
		$prcode="";
	} else if ($mode=="modify") {
		$sql = "SELECT vender,display,brand,pridx,assembleuse,sellprice,assembleproduct FROM tblproduct WHERE productcode = '".$prcode."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		mysql_free_result($result);
		$vender=(int)$row->vender;
		$vdisp=$row->display;
		$brand=$row->brand;
		$vassembleuse=$row->assembleuse;
		$vpridx=$row->pridx;
		$vsellprice=$row->sellprice;
		$vassembleproduct=$row->assembleproduct;

		if(strlen($buyprice) < 1 ) $buyprice = 0 ;

		$sql = "UPDATE tblproduct SET ";
		$sql.= "productname		= '".$productname."', ";
		$sql.= "consumerprice	= ".$consumerprice.", ";
		$sql.= "buyprice		= ".$buyprice.", ";
		$sql.= "reserve			= '".$reserve."', ";
		$sql.= "reservetype		= '".$reservetype."', ";
		$sql.= "production		= '".$production."', ";
		$sql.= "madein			= '".$madein."', ";
		$sql.= "model			= '".$model."', ";
		$sql.= "opendate		= '".$opendate."', ";
		$sql.= "selfcode		= '".$selfcode."', ";
		$sql.= "bisinesscode	= '".$bisinesscode."', ";
		$sql.= "quantity		= ".$quantity.", ";
		$sql.= "group_check		= '".$group_check."', ";
		$sql.= "keyword			= '".$keyword."', ";
		$sql.= "addcode			= '".$addcode."', ";
		$sql.= "userspec		= '".$userspec."', ";
		$sql.= "maximage		= '".$image[0]."', ";
		$sql.= "minimage		= '".$image[1]."', ";
		$sql.= "tinyimage		= '".$image[2]."', ";
		$sql.= "img_type		= '".$img_type."', ";
		
		$sql.= "assembleuse		= '".$assembleuse."', ";

		if($vassembleuse=="Y") {
			if($assembleuse=="Y") {
				$sql.= "assembleproduct	= '', ";
				$sql.= "option_price	= '', ";
				$sql.= "option_quantity	= '', ";
				$sql.= "option1			= '', ";
				$sql.= "option2			= '', ";
				$sql.= "package_num		= '0', ";
			} else {
				$sql.= "assembleproduct	= '', ";
				$sql.= "sellprice		= ".$sellprice.", ";
				$sql.= "option_price	= '".$option_price."', ";
				$sql.= "option_quantity	= '".$optcnt."', ";
				$sql.= "option1			= '".$option1."', ";
				$sql.= "option2			= '".$option2."', ";
				$sql.= "package_num		= '".(int)$package_num."', ";
			}
		} else {
			if($assembleuse=="Y") {
				$sql.= "assembleproduct	= '', ";
				$sql.= "sellprice		= 0, ";
				$sql.= "option_price	= '', ";
				$sql.= "option_quantity	= '', ";
				$sql.= "option1			= '', ";
				$sql.= "option2			= '', ";
				$sql.= "package_num		= '0', ";
			} else {
				$sql.= "sellprice		= ".$sellprice.", ";
				$sql.= "option_price	= '".$option_price."', ";
				$sql.= "option_quantity	= '".$optcnt."', ";
				$sql.= "option1			= '".$option1."', ";
				$sql.= "option2			= '".$option2."', ";
				$sql.= "package_num		= '".(int)$package_num."', ";
			}
		}

		$sql.= "etctype			= '".$etctype."', ";
		$sql.= "deli_price		= '".$deli_price."', ";
		$sql.= "deli			= '".$deli."', ";
		$sql.= "display			= '".$display."', ";
		
		$sql.= "etcapply_coupon	= '".$etcapply_coupon."', ";
		$sql.= "etcapply_reserve= '".$etcapply_reserve."', ";
		$sql.= "etcapply_gift	= '".$etcapply_gift."', ";
		$sql.= "etcapply_return	= '".$etcapply_return."', ";

		if($insertdate!="Y") {
			$sql.= "date			= '".$curdate."', ";
		}
		$sql.= "modifydate		= now(), ";
		$sql.= "content			= '".$in_content."' ";
		$sql.= "WHERE productcode = '".$prcode."' ";
		#echo $sql; exit;
		if($update = mysql_query($sql,get_db_conn())) {
			if(strlen($brandname)>0) { // 브랜드 관련 처리
				$result = mysql_query("SELECT bridx FROM tblproductbrand WHERE brandname = '".$brandname."' ",get_db_conn());
				if ($row=mysql_fetch_object($result)) {
					if($brand != $row->bridx) {
						@mysql_query("UPDATE tblproduct SET brand = '".$row->bridx."' WHERE productcode = '".$prcode."'",get_db_conn());
					}
				} else {
					$sql = "INSERT tblproductbrand SET brandname = '".$brandname."'";
					if($brandinsert = @mysql_query($sql,get_db_conn())) {
						$bridx = @mysql_insert_id(get_db_conn());
						if($bridx>0) {
							@mysql_query("UPDATE tblproduct SET brand = '".$bridx."' WHERE productcode = '".$prcode."'",get_db_conn());
						}
					}
				}
				mysql_free_result($result);
			} else {
				if($brand>0) {
					@mysql_query("UPDATE tblproduct SET brand = null WHERE productcode = '".$prcode."'",get_db_conn());
				}
			}
			
			$groupdelete = mysql_query("DELETE FROM tblproductgroupcode WHERE productcode = '".$prcode."' ",get_db_conn());
			if($groupdelete) {
				if($group_check=="Y" && count($group_code)>0) {
					for($i=0; $i<count($group_code); $i++) {
						$sql = "INSERT tblproductgroupcode SET ";
						$sql.= "productcode = '".$prcode."', ";
						$sql.= "group_code = '".$group_code[$i]."' ";
						@mysql_query($sql,get_db_conn());
					}
				}
			}

			if($vassembleuse=="Y") {
				if($assembleuse!="Y") {
					$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
					$sql.= "WHERE productcode = '".$prcode."' ";
					$result = mysql_query($sql,get_db_conn());
					if($row = @mysql_fetch_object($result)) {
						$sql = "DELETE FROM tblassembleproduct WHERE productcode = '".$prcode."' ";
						mysql_query($sql,get_db_conn());
						
						if(strlen(str_replace("","",$row->assemble_pridx))>0) {
							$sql = "UPDATE tblproduct SET ";
							$sql.= "assembleproduct = REPLACE(assembleproduct,',".$prcode."','') ";
							$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
							$sql.= "AND assembleuse != 'Y' ";
							mysql_query($sql,get_db_conn());
						}
					}
					mysql_free_result($result);
				}
			} else {
				if($assembleuse=="Y" || ($assembleuse!="Y" && $vsellprice!=$sellprice)) {
					if(strlen($vassembleproduct)>0) {
						$sql = "SELECT productcode, assemble_pridx FROM tblassembleproduct ";
						$sql.= "WHERE productcode IN ('".str_replace(",","','",$vassembleproduct)."') ";
						$result = mysql_query($sql,get_db_conn());
						while($row = @mysql_fetch_object($result)) {
							$sql = "SELECT SUM(sellprice) as sumprice FROM tblproduct ";
							$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
							$sql.= "AND display ='Y' ";
							$sql.= "AND assembleuse!='Y' ";
							$result2 = mysql_query($sql,get_db_conn());
							if($row2 = @mysql_fetch_object($result2)) {
								$sql = "UPDATE tblproduct SET sellprice='".$row2->sumprice."' ";
								$sql.= "WHERE productcode = '".$row->productcode."' ";
								$sql.= "AND assembleuse='Y' ";
								mysql_query($sql,get_db_conn());
							}
							mysql_free_result($result2);
						}
					}

					if($assembleuse=="Y") {
						$sql = "UPDATE tblassembleproduct SET ";
						$sql.= "assemble_pridx=REPLACE(assemble_pridx,'".$vpridx."',''), ";
						$sql.= "assemble_list=REPLACE(assemble_list,',".$vpridx."','') ";
						mysql_query($sql,get_db_conn());
					}
				}
			}

			if($vender>0) {
				if($vdisp!=$display) {
					setVenderCountUpdateRan($vender, $display);
				}
			}
			$content=$in_content;
			$use_imgurl="";
			$userfile_url="";
			$userfile2_url="";
			$userfile3_url="";
		}

		if($popup=="YES") {
			$onload="<script>alert(\"상품이 수정되었습니다.$message\");</script>";
		} else {
			$onload="<script>parent.ListFrame.GoPageReload();parent.HiddenFrame.alert(\"상품이 수정되었습니다.$message\");</script>";
		}

		$log_content = "## 상품수정 ## - 코드 $prcode - 상품 : $productname 가격 : $sellprice 수량 : $quantity 기타 : $etctype 적립금 : $reserve 날짜고정 : ".(($insertdate=="Y")?"Y":"N")." $display";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	}
} else {
	$onload="<script>alert(\"상품이미지의 총 용량이 ".ceil($file_size/1024)
	."Kbyte로 500K가 넘습니다.\\n\\n한번에 올릴 수 있는 최대 용량은 500K입니다.\\n\\n"
	."이미지가 gif가 아니면 이미지 포맷을 바꾸어 올리시면 용량이 줄어듭니다.\");history.go(-1);</script>\n";
}

//################# 500K가 넘는 이미지 체크
if ((strlen($userfile[name])>0 && $userfile[size]==0) || (strlen($userfile2[name])>0 && $userfile2[size]==0) || (strlen($userfile3[name])>0 && $userfile3[size]==0)) {
	 $onload="<script>alert(\"상품이미지중 용량이 500K가 넘는 이미지가 있습니다.\\n\\n500K가 넘는 이미지는 등록되지 않습니다..\\n\\n"
		."이미지가 gif가 아니면 이미지 포맷을 바꾸어 올리시면 용량이 줄어듭니다.\");</script>\n";
}
//###############################################

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('AddFrame')");</script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}

function PrdtDelete() {
	if (confirm("해당 상품을 삭제하시겠습니까?")) {
		document.cForm.mode.value="delete";
		document.cForm.submit();
	}
}

function NewPrdtInsert(){
	document.cForm.prcode.value="";
	document.cForm.submit();
}

function IconMy(){
	window.open("","icon","height=343,width=440,toolbar=no,menubar=no,scrollbars=no,status=no");
	document.icon.submit();
}

function IconList(){
	alert("서비스 준비중 입니다.");
	//window.open("","iconlist","height=343,width=440,toolbar=no,menubar=no,scrollbars=no,status=no");
	//document.iconlist.submit();
}

function DeletePrdtImg(temp){
	if(confirm('해당 이미지를 삭제하시겠습니까?')){
		document.cForm.mode.value="delprdtimg";
		document.cForm.delprdtimg.value=temp-1;
		document.cForm.submit();
	}
}

function CheckChoiceIcon(no){
	num = document.form1.iconnum.value;
	iconnum=0;
	for(i=0;i<num;i++){
		if(document.form1.icon[i].checked==true) iconnum++;
	}
	if(iconnum>3){
		alert('한 상품에 3개까지 아이콘을 사용할 수 있습니다.');
		document.form1.icon[no].checked=false;
	}
}

function PrdtAutoImgMsg(){
	if(document.form1.imgcheck.checked==true) alert('상품 중간/작은 이미지가 대 이미지에서 자동 생성됩니다.\n\n기존의 중간/작은 이미지는 삭제됩니다.');
}

var shop="layer0";
var ArrLayer = new Array ("layer0","layer1","layer2","layer3");
function ViewLayer(gbn){
	if(document.all){
		for(i=0;i<4;i++) {
			if (ArrLayer[i] == gbn)
				document.all[ArrLayer[i]].style.display="";
			else
				document.all[ArrLayer[i]].style.display="none";
		}
	} else if(document.getElementById){
		for(i=0;i<4;i++) {
			if (ArrLayer[i] == gbn)
				document.getElementByld[ArrLayer[i]].style.display="";
			else
				document.getElementByld[ArrLayer[i]].style.display="none";
		}
	} else if(document.layers){
		for(i=0;i<4;i++) {
			if (ArrLayer[i] == gbn)
				document.layers[ArrLayer[i]].display="";
			else
				document.layers[ArrLayer[i]].display="none";
		}
	}
	shop=gbn;
	parent_resizeIframe('AddFrame');
}

function SelectColor(){
	setcolor = document.form1.setcolor.value;
	var newcolor = showModalDialog("select_color.php?color="+setcolor, "oldcolor", "resizable: no; help: no; status: no; scroll: no;");
	if(newcolor){
		document.form1.setcolor.value=newcolor;
		document.all.ColorPreview.style.backgroundColor = '#' + newcolor;
	}
}

function optionhelp(){
	alert("서비스 준비중 입니다.");
}

function DateFixAll(obj) {
	if (obj.checked==true) {
		document.form1.insertdate.value="Y";
		document.form1.insertdate1.checked=true;
		document.form1.insertdate2.checked=true;
		document.form1.insertdate3.checked=true;
	} else {
		document.form1.insertdate.value="";
		document.form1.insertdate1.checked=false;
		document.form1.insertdate2.checked=false;
		document.form1.insertdate3.checked=false;
	}
}

function change_filetype(obj) {
	if(obj.checked==true) {	//이미지 링크 방식
		for(var jj=1;jj<=3;jj++) {
			idx=jj;
			if(idx==1) idx="";
			document.form1["userfile"+idx].style.display='none';
			document.form1["userfile"+idx+"_url"].style.display='';
			document.form1["userfile"+idx].disabled=true;
			document.form1["userfile"+idx+"_url"].disabled=false;
		}
	} else {				//첨부파일 방식
		for(var jj=1;jj<=3;jj++) {
			idx=jj;
			if(idx==1) idx="";
			document.form1["userfile"+idx].style.display='';
			document.form1["userfile"+idx+"_url"].style.display='none';
			document.form1["userfile"+idx].disabled=false;
			document.form1["userfile"+idx+"_url"].disabled=true;
		}
	}
}

function userspec_change(val) {
	if(document.getElementById("userspecidx")) {
		if(val == "Y") {
			document.getElementById("userspecidx").style.display ="";
		} else {
			document.getElementById("userspecidx").style.display ="none";
		}
	}
}

function GroupCode_Change(val) {
	if(document.getElementById("group_checkidx")) {
		if(val == "Y") {
			document.getElementById("group_checkidx").style.display ="";
		} else {
			document.getElementById("group_checkidx").style.display ="none";
		}
	}
}

function GroupCodeAll(checkval,checkcount) {
	for(var i=0; i<checkcount; i++) {
		if(document.getElementById("group_code_idx"+i)) {
			document.getElementById("group_code_idx"+i).checked = checkval;
		}
	}
}

/*################################### 태그관련 시작 #####################################*/
var IE = false ;
if (window.navigator.appName.indexOf("Explorer") !=-1) {
	IE = true;
}

function getXmlHttpRequest() {
	var xmlhttp = false
	if(window.XMLHttpRequest){//Mozila
		xmlhttp = new XMLHttpRequest()
	}else {//IE
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP")
	}
	return xmlhttp;
}
function loadData(path, successFunc, msg){ 
	var xmlhttp = getXmlHttpRequest();
	xmlhttp.open("GET",path,true);
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {
			if (xmlhttp.status == 200) {
				var data = xmlhttp.responseText;
				successFunc(data);
			}else{
				alert(msg);
			}
		}
	}
	xmlhttp.send(null);
	return false;
}


function loadProductTagList (prcode) {
	loadData("product_taglist.xml.php?prcode="+prcode, setProductTagList);
}
function setProductTagList(data) {
  	try {
  		var tagElem = document.getElementById("ProductTagList");
  		if(data=='') {
  			data = "일시적으로 태그 정보를 불러올 수 없습니다.\n\n태그 관리 기능은 잠시 후에 이용해 주십시오. \n\n상품수정은  정상적으로 수정하실  수 있습니다.";
  		}	
  		tagElem.innerHTML = data;
		tagElem.style.height = "68";
		tagElem.style.overflowY = "auto";  		
  	}catch(e) {}
}

function delTagName(prcode,tagname) {
<? if(_DEMOSHOP=="OK" && getenv("REMOTE_ADDR")!=_ALLOWIP) { ?>
	alert("데모버전에서는 삭제가 불가능 합니다.");
<? } else { ?>
	if(confirm("\""+tagname+"\" 태그를 삭제하시겠습니까?")) {
		loadData("product_taglist.xml.php?type=del&prcode="+prcode+"&tagname="+tagname, setProductTagList);
	}
<? } ?>
}

function BrandSelect() {
	window.open("product_brandselect.php","brandselect","height=400,width=420,scrollbars=no,resizable=no");
}

function FiledSelect(pagetype) {
	window.open("product_select.php?type="+pagetype,pagetype,"height=400,width=420,scrollbars=no,resizable=no");
}

/*################################### 태그관련 끝   #####################################*/


function deli_helpshow() {
	if(document.getElementById('deli_helpshow_idx')) {
		if(document.getElementById('deli_helpshow_idx').style.display=="none") {
			document.getElementById('deli_helpshow_idx').style.display="";
		} else {
			document.getElementById('deli_helpshow_idx').style.display="none";
		}
	}
}

function chkFieldMaxLenFunc(thisForm,reserveType) {
	if (reserveType=="Y") { max=5; addtext="/특수문자(소수점)";} else { max=6; }
	if (thisForm.reserve.value.bytes() > max) {
		alert("입력할 수 있는 허용 범위가 초과되었습니다.\n\n" + "숫자"+addtext+" " + max + "자 이내로 입력이 가능합니다.");
		thisForm.reserve.value = thisForm.reserve.value.cut(max);
		thisForm.reserve.focus();
	}
}

function getSplitCount(objValue,splitStr)
{
	var split_array = new Array();
	split_array = objValue.split(splitStr);
	return split_array.length;
}

function getPointCount(objValue,splitStr,falsecount)
{
	var split_array = new Array();
	split_array = objValue.split(splitStr);
	
	if(split_array.length!=2) {
		if(split_array.length==1) {
			return false;
		} else {
			return true;
		}
	} else {
		if(split_array[1].length>falsecount) {
			return true;
		} else {
			return false;
		}
	}
}

function isDigitSpecial(objValue,specialStr)
{
	if(specialStr.length>0) {
		var specialStr_code = parseInt(specialStr.charCodeAt(i));

		for(var i=0; i<objValue.length; i++) {
			var code = parseInt(objValue.charCodeAt(i));
			var ch = objValue.substr(i,1).toUpperCase();
			
			if((ch<"0" || ch>"9") && code!=specialStr_code) {
				return true;
				break;
			}
		}
	} else {
		for(var i=0; i<objValue.length; i++) {
			var ch = objValue.substr(i,1).toUpperCase();
			if(ch<"0" || ch>"9") {
				return true;
				break;
			}
		}
	}
}

<? if($_data->vender<=0) { ?>
function assembleuse_change() {
	if(document.form1.assembleuse[0].checked) {
		document.form1.sellprice.disabled=false;
		document.form1.sellprice.style.backgroundColor = "#FFFFFF";
		if(document.form1.searchtype) {
			if(document.form1.searchtype.length && document.form1.searchtype.length>0) {
				for(var i=0; i<document.form1.searchtype.length; i++) {
					document.form1.searchtype[i].disabled=false;
					if(document.form1.searchtype[i].checked) {
						ViewLayer("layer"+i);
					}
				}
			} else {
				document.form1.searchtype.disabled=false;
				if(document.form1.searchtype.checked) {
					ViewLayer("layer");
				}
			}
			if(document.getElementById("assemblealertidx")) {
				document.getElementById("assemblealertidx").style.display="none";
			}
		}

		if(document.getElementById("packagealertidx")) {
			document.getElementById("packagealertidx").style.display="none";
		}
		if(document.getElementById("packageselectidx")) {
			document.getElementById("packageselectidx").style.display="";
		}
		document.form1.package_num.disabled=false;
	} else {
		document.form1.sellprice.disabled=true;
		document.form1.sellprice.style.backgroundColor = "#C0C0C0";
		if(document.form1.searchtype) {
			ViewLayer("layer0");
			if(document.form1.searchtype.length && document.form1.searchtype.length>0) {
				for(var i=0; i<document.form1.searchtype.length; i++) {
					document.form1.searchtype[i].disabled=true;
				}
			} else {
				document.form1.searchtype.disabled=true;
			}
			if(document.getElementById("assemblealertidx")) {
				document.getElementById("assemblealertidx").style.display="";
			}
		}
		if(document.getElementById("packagealertidx")) {
			document.getElementById("packagealertidx").style.display="";
		}
		if(document.getElementById("packageselectidx")) {
			document.getElementById("packageselectidx").style.display="none";
		}
		document.form1.package_num.disabled=true;
	}
	parent_resizeIframe('AddFrame');
	alert("한번 등록된 상품 판매가격 타입은 변경이 불가능하므로 신중히 선택해 주세요.");
}
<? } ?>
//-->
</SCRIPT>
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td style="BORDER:#0F8FCB 2px solid; padding:10px;" bgcolor="#FFFFFF">
	<table cellpadding="0" cellspacing="0" width="100%">
	<?if ($popup== "YES") {?>
	<tr>
		<td align="center">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="29">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="right"><img src="images/top_link_house.gif" width="16" height="12" border="0" valign=absmiddle>현재위치 : 상품관리 &gt;카테고리/상품관리 &gt; <span class="2depth_select">상품 등록 및 수정</span></td>
			</tr>
			<tr>
				<td><img src="images/top_link_line.gif" width="100%" height="1" border="0"></td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr><td height="8"></td></tr>
				<tr>
					<td>
					<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
					<TR>
						<TD><IMG SRC="images/product_register_title.gif" WIDTH="208" HEIGHT=32 ALT=""></TD>
						<TD width="100%" background="images/title_bg.gif"></TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				<tr><td height="3"></td></tr>
				<tr>
					<td style="padding-bottom:3pt;">
					<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
					<TR>
						<TD><IMG SRC="images/distribute_01.gif"></TD>
						<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
						<TD><IMG SRC="images/distribute_03.gif"></TD>
					</TR>
					<TR>
						<TD background="images/distribute_04.gif"></TD>
						<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
						<TD width="100%" class="notice_blue">판매할 상품권을 등록,수정,삭제 합니다. </TD>
						<TD background="images/distribute_07.gif"></TD>
					</TR>
					<TR>
						<TD><IMG SRC="images/distribute_08.gif"></TD>
						<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
						<TD><IMG SRC="images/distribute_10.gif"></TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				<tr><td height="20"></td></tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<?}?>
	<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
	<input type=hidden name=mode>
	<input type=hidden name=code value="<?=$code?>">
	<input type=hidden name=prcode value="<?=$prcode?>">
	<input type=hidden name=htmlmode value='wysiwyg'>
	<input type=hidden name=delprdtimg>
	<input type=hidden name=option1>
	<input type=hidden name=option2>
	<input type=hidden name=option_price>
	<input type=hidden name=insertdate>
	<input type=hidden name=popup value="<?=$popup?>">
	<tr>
		<td align="center">
<?
	if ($code != substr($prcode,0,12)) $prcode = "";

	if (strlen($code)==12) {
		if (strlen($prcode)>0) {
			$sql = "SELECT * FROM tblproduct WHERE productcode = '".$prcode."' ";
			$result = mysql_query($sql,get_db_conn());
			if ($_data = mysql_fetch_object($result)) {
				$productname = $_data->productname;
				if(strlen($_data->option_quantity)>0) $searchtype=1;
				else if(ereg("^(\[OPTG)([0-9]{4})(\])$",$_data->option1)) $searchtype=3;
				
				unset($specname);
				unset($specvalue);
				unset($specarray);
				if(strlen($_data->userspec)>0) {
					$userspec = "Y";
					$specarray= explode("=",$_data->userspec);
					for($i=0; $i<$userspec_cnt; $i++) {
						$specarray_exp = explode("", $specarray[$i]);
						$specname[] = $specarray_exp[0];
						$specvalue[] = $specarray_exp[1];
					}
				} else {
					$userspec = "N";
				}

				// 특수옵션값을 체크한다.
				$dicker = $dicker_text="";
				if (strlen($_data->etctype)>0) {
					$etctemp = explode("",$_data->etctype); 
					$miniq = 1;          // 최소주문수량 기본값 넣는다.
					$maxq = "";
					for ($i=0;$i<count($etctemp);$i++) {
						if ($etctemp[$i]=="BANKONLY")                    $bankonly="Y";        // 현금전용
						else if (substr($etctemp[$i],0,11)=="DELIINFONO=")     $deliinfono=substr($etctemp[$i],11);  // 배송/교환/환불정보 노출안함 정보
						else if ($etctemp[$i]=="SETQUOTA")               $setquota="Y";        // 무이자상품
						else if (substr($etctemp[$i],0,6)=="MINIQ=")     $miniq=substr($etctemp[$i],6);  // 최소주문수량
						else if (substr($etctemp[$i],0,5)=="MAXQ=")      $maxq=substr($etctemp[$i],5);  // 최대주문수량
						else if (substr($etctemp[$i],0,5)=="ICON=")      $iconvalue=substr($etctemp[$i],5);  // 최대주문수량
						else if (substr($etctemp[$i],0,9)=="FREEDELI=")  $freedeli=substr($etctemp[$i],9);  // 무료배송상품
						else if (substr($etctemp[$i],0,7)=="DICKER=") {  $dicker=Y; $dicker_text=str_replace("DICKER=","",$etctemp[$i]); }  // 가격대체문구

						/*switch ($etctemp[$i]) {
							case "BANKONLY": $bankonly = "Y";break;
							case "SETQUOTA": $setquota = "Y";break;
						}*/
					}
				}
				if(strlen($iconvalue)>0) {
					for($i=0;$i<strlen($iconvalue);$i=$i+2) {
						$iconvalue2[substr($iconvalue,$i,2)]="Y";
						//echo "<br>>>>>".substr($iconvalue,$i,2);
					}
				}
				if($_data->brand>0) {
					$sql = "SELECT brandname FROM tblproductbrand WHERE bridx = '".$_data->brand."' ";
					$result = mysql_query($sql,get_db_conn());
					$_data2 = mysql_fetch_object($result);
					$_data->brandname = $_data2->brandname;
					mysql_free_result($result);
				}
				
				if($_data->group_check=="Y") {
					$sql = "SELECT group_code FROM tblproductgroupcode WHERE productcode = '".$prcode."' ";
					$result = mysql_query($sql,get_db_conn());
					while($row = mysql_fetch_object($result)) {
						$group_code[$row->group_code] = "Y";
					}
					mysql_free_result($result);
				}
			} else {
				echo "<script>alert('해당 상품이 존재하지 않습니다.');location='".$_SERVER[PHP_SELF]."';</script>";
				exit;
			}
		}

		if(ereg("^(\[OPTG)([0-9]{4})(\])$",$_data->option1)){
			$optcode = substr($_data->option1,5,4);
			$_data->option1="";
			$_data->option_price="";
		}
?>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
					<TR>
						<TD><IMG SRC="images/product_register_stitle1.gif" ALT=""></TD>
						<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
						<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
					</TR>
				</TABLE>
			</td>
		</tr>
		<tr><td height="3"></td></tr>
		<tr>
			<td class="notice_blue" style="padding-left:12px;"><img src="images/icon_point2.gif" width="8" height="11" border="0"> <span class="font_orange"><b>필수표시 항목</b></span></TD>
		</tr>
		<tr><td height="3"></td></tr>
		<tr>
			<td>
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
			<col width=160></col>
			<col width=></col>
			<col width=110></col>
			<col width=240></col>
			<TR>
				<TD colspan=4 background="images/table_top_line.gif"></TD>
			</TR>
			<?if($_data->vender>0){?>
			<tr>
				<td class="table_cell">등록업체</td>
				<td class="td_con1" colspan="3">
				<?
				$sql = "SELECT vender,id,brand_name FROM tblvenderstore WHERE vender='".$_data->vender."' ";
				$result=mysql_query($sql,get_db_conn());
				if($row=mysql_fetch_object($result)) {
					echo "<A HREF=\"javascript:viewVenderInfo(".$row->vender.")\"><B>".$row->brand_name." (".$row->id.")</B></A>";
				}
				mysql_free_result($result);
				?>
				</td>
			</tr>
			<?}?>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">현재 카테고리</TD>
				<TD class="td_con1" colspan="3" style="word-break:break-all;">
<?
				$code_loc = "";
				$sql = "SELECT code_name,type FROM tblproductcode WHERE codeA='".substr($code,0,3)."' ";
				if(substr($code,3,3)!="000") {
					$sql.= "AND (codeB='".substr($code,3,3)."' OR codeB='000') ";
					if(substr($code,6,3)!="000") {
						$sql.= "AND (codeC='".substr($code,6,3)."' OR codeC='000') ";
						if(substr($code,9,3)!="000") {
							$sql.= "AND (codeD='".substr($code,9,3)."' OR codeD='000') ";
						} else {
							$sql.= "AND codeD='000' ";
						}
					} else {
						$sql.= "AND codeC='000' ";
					}
				} else {
					$sql.= "AND codeB='000' AND codeC='000' ";
				}
				$sql.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
				//echo $sql; exit;
				$result=mysql_query($sql,get_db_conn());
				$i=0;
				while($row=mysql_fetch_object($result)) {
					if($i>0) $code_loc.= " > ";
					$code_loc.= $row->code_name;
					$i++;
				}
				mysql_free_result($result);

				if (strlen($prcode)>0) {
					echo $code_loc." > <B><span class=\"font_orange\">".$productname."</B></span>";
				} else {
					echo $code_loc." > <B><span class=\"font_orange\">".($gongtype=="Y"?"공동구매 신규입력":"신규입력")."</B></span>";
				}
?>
				</TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<? if($_data->productcode) { ?>
			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b><span class="font_orange">링크주소</span></b></TD>
				<TD class="td_con1" colspan="3"><a href="/front/productdetail.php?productcode=<?=$_data->productcode?>" target="_balnk">http://<?=$_SERVER["SERVER_NAME"]?>/front/productdetail.php?productcode=<?=$_data->productcode?></a></TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<? }?>
			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b><span class="font_orange">상품권명</span></b></TD>
				<TD class="td_con1" colspan="3"><input name=productname value="<?=ereg_replace("\"","&quot",$_data->productname)?>" size=80 maxlength=250 onKeyDown="chkFieldMaxLen(250)" class="input" style=width:100%></TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">등록/수정일</TD>
				<TD class="td_con1" colspan="3">
<?
			if (strlen($prcode)==0) {
				echo "자동입력";
			} else { 
				if ($_data) {
					echo " ".str_replace("-","/",substr($_data->modifydate,0,16))."\n";
					echo "(상품코드 : <span class=\"font_orange\">".$_data->productcode."</span>)";
					echo "&nbsp;&nbsp;&nbsp;<a href=\"http://".$shopurl."?productcode=".$_data->productcode."\" target=_blank><img src=\"images/productregister_goproduct.gif\" align=absmiddle border=0></font></a>";
				}
				echo "<input type=hidden name=productcode value=\"".$_data->productcode."\">\n";
			}
?>
				</TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">상품권 등록날짜</TD>
				<TD class="td_con1" colspan="3"><input type=checkbox id="idx_insertdate10" name=insertdate1 value="Y" onclick="DateFixAll(this)" <?=($insertdate_cook=="Y")?"checked":"";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_insertdate10>등록 일자 고정</label>&nbsp;<span class="font_orange">(* 상품수정시 등록날짜가 변경되지 않습니다.)</span></TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<!-- 혜택 및 구매제한 -->
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">혜택 및 구매제한</TD>
				<TD class="td_con1" colspan="3">
					<input type=checkbox id="idx_etcapply_coupon" name=etcapply_coupon value="Y" checked disabled><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_etcapply_coupon>할인쿠폰 적용불가</label>
					&nbsp;&nbsp;&nbsp;
					<input type=checkbox id="idx_etcapply_reserve" name=etcapply_reserve value="Y" checked disabled><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_etcapply_reserve>적립금 사용불가</label>
					&nbsp;&nbsp;&nbsp;
					<input type=checkbox id="idx_etcapply_gift" name=etcapply_gift value="Y"  checked disabled><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_etcapply_gift>구매사은품 적용불가</label>
					<input type=checkbox id="idx_etcapply_return" name=etcapply_return value="Y" <?=($_data->etcapply_return=="Y")?"checked":"";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_etcapply_return>교환 및 환불 불가</label>
					<input type=checkbox id="idx_bankonly1" name=bankonly value="Y" <? if ($_data) { if ($bankonly=="Y") echo "checked";}?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_bankonly1>현금결제만 사용하기</label> <span class="font_orange">(여러 상품과 함께 구매시 결제는 현금결제로만 진행됩니다.)</span>
				</TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<? if($_data->vender>0){ ?>
			<input type=hidden name="assembleuse" value="N">
			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0"><span class="font_orange"><b><?=($gongtype=="Y"?"공구가":"판매가격")?></b></span></TD>
				<TD class="td_con1"><input name=sellprice value="<?=$_data->sellprice?>" size=16 maxlength=10 class="input" style=width:98%></TD>
				<TD class="table_cell" style="border-left-width:1pt; border-color:rgb(227,227,227); border-top-style:none; border-right-style:none; border-bottom-style:none; border-left-style:solid;"><img src="images/icon_point2.gif" width="8" height="11" border="0"><span class="font_orange"><b><?=($gongtype=="Y"?"시작가":"시중가격")?></b></span></TD>
				<TD class="td_con1"><input name=consumerprice value="<?=(int)(strlen($_data->consumerprice)>0?$_data->consumerprice:"0")?>" size=16 maxlength=10 class="input" style=width:100%><br><span class="font_orange">* <strike>5,000</strike>로 표기됨, 0 입력시 표기안됨&nbsp;</span></TD>
			</tr>
			<? } else { ?>
			
			<TR>
				<?	if(strlen($prcode)==0) { ?>
				<TD class="table_cell">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<td rowspan="3"><img src="images/icon_point2.gif" width="8" height="11" border="0"></td>
					<td width="100%"><input type="radio" name="assembleuse" value="N" <?=($_data->assembleuse=="Y"?"":"checked")?> id="idx_assembleuseY" style="border:none" ><label style='cursor:hand;' for="idx_assembleuseY"><span class="font_orange"><b><?=($gongtype=="Y"?"단일 공구가":"판매가격")?></b></span></label></td>
				</tr>
				
				</table>
				</TD>
				<? } else { ?>
				<TD class="table_cell">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<input type=hidden name="assembleuse" value="<?=$_data->assembleuse?>">
				<tr>
					<td><img src="images/icon_point2.gif" width="8" height="11" border="0"></td>
					<td width="100%" height="30">
					<?	if($_data->assembleuse=="Y") { ?>
						<span class="font_orange"><b><?=($gongtype=="Y"?"코디/조립 판매가":"코디/조립 판매가")?></b></span>
					<? } else { ?>
						<span class="font_orange"><b><?=($gongtype=="Y"?"단일 공구가":"단일 판매가격")?></b></span>
					<? } ?>
					</td>
				</tr>
				</table>
				</TD>
				<? } ?>
				<TD class="td_con1"><input name=sellprice value="<?=$_data->sellprice?>" size=16 maxlength=10 class="input" style=width:98% ></TD>
				<TD class="table_cell" style="border-left-width:1pt; border-color:rgb(227,227,227); border-top-style:none; border-right-style:none; border-bottom-style:none; border-left-style:solid;"><img src="images/icon_point2.gif" width="8" height="11" border="0"><span class="font_orange"><b><?=($gongtype=="Y"?"시작가":"적립금액")?></b></span></TD>
				<TD class="td_con1"><input name=consumerprice value="<?=(int)(strlen($_data->consumerprice)>0?$_data->consumerprice:"0")?>" size=16 maxlength=10 class="input" style=width:100%></TD>
			</tr>
			<? } ?>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			
			<tr>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0"><b><span class="font_orange">수량</span></b></TD>
				<TD class="td_con1" colspan="3">
<?
			if ($gongtype=="Y") {
				if ($_data) {
					$quantity=$_data->quantity;
					if($_data->quantity<=0) $checkquantity="E";
					else $checkquantity="C";
				}else {
					$checkquantity="C";
				}

				$arrayname= array("마감","수량");
				$arrayprice=array("E","C");
				$arraydisable=array("true","false");
				$arraybg=array("silver","white");
				$arrayquantity=array("","$quantity");
				for($i=0;$i<2;$i++){
					echo "<input type=radio id=\"idx_checkquantity".$i."\" name=checkquantity value=\"".$arrayprice[$i]."\" ";
					if($checkquantity==$arrayprice[$i]) echo "checked "; echo "onClick=\"document.form1.quantity.disabled=".$arraydisable[$i].";document.form1.quantity.style.background='".$arraybg[$i]."';document.form1.quantity.value='".$arrayquantity[$i]."';\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_checkquantity".$i.">".$arrayname[$i]."</label>&nbsp;";
				}
				echo ": <input type=text name=quantity size=5 maxlength=5 value=\"".($quantity==0?"":$quantity)."\" class=\"input\">개";
			} else {
				if ($_data) {
					$quantity=$_data->quantity;
					if($_data->quantity==NULL) $checkquantity="F";
					else if($_data->quantity<=0) $checkquantity="E";
					else $checkquantity="C";
					if($quantity<0) $quantity="";
				} else {
					$checkquantity="F";
				}

				$arrayname= array("품절","무제한","수량");
				$arrayprice=array("E","F","C");
				$arraydisable=array("true","true","false");
				$arraybg=array("silver","silver","white");
				$arrayquantity=array("","","$quantity");
				$cnt = count($arrayprice);
				for($i=0;$i<$cnt;$i++){
					echo "<input type=radio id=\"idx_checkquantity".$i."\" name=checkquantity value=\"".$arrayprice[$i]."\" "; 
					if($checkquantity==$arrayprice[$i]) echo "checked "; echo "onClick=\"document.form1.quantity.disabled=".$arraydisable[$i].";document.form1.quantity.style.background='".$arraybg[$i]."';document.form1.quantity.value='".$arrayquantity[$i]."';\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_checkquantity".$i.">".$arrayname[$i]."</label>&nbsp;&nbsp;";
				}
				echo ": <input type=text name=quantity size=5 maxlength=5 value=\"".($quantity==0?"":$quantity)."\" class=\"input\">개";
			}
			if($checkquantity=="C"){
				echo "<script>document.form1.quantity.disabled=false;document.form1.quantity.style.background='white';</script>\n";
			}else{
				echo "<script>document.form1.quantity.disabled=true;document.form1.quantity.style.background='silver';document.form1.checkquantity.value='';</script>\n";
			}
?>
				</TD>
			</tr>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<tr>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">최소구매수량</TD>
				<TD class="td_con1"><input type=text name=miniq value="<?=($miniq>0?$miniq:"1")?>" size=5 maxlength=5 class="input"> 개 이상</TD>
				<TD class="table_cell" style="border-left-width:1pt; border-color:rgb(227,227,227); border-top-style:none; border-right-style:none; border-bottom-style:none; border-left-style:solid;"><img src="images/icon_point5.gif" width="8" height="11" border="0">최대구매수량</TD>
				<TD class="td_con1"><input type=radio id="idx_checkmaxq1" name=checkmaxq value="A" <? if (strlen($maxq)==0 || $maxq=="?") echo "checked ";?> onclick="document.form1.maxq.disabled=true;document.form1.maxq.style.background='silver';"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_checkmaxq1>무제한</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_checkmaxq2" name=checkmaxq value="B" <? if ($maxq!="?" && $maxq>0) echo "checked"; ?> onclick="document.form1.maxq.disabled=false;document.form1.maxq.style.background='white';"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_checkmaxq2>수량</label> : <input name=maxq size=5 maxlength=5 value="<?=$maxq?>" class="input"> 개 이하
				<script>
				if (document.form1.checkmaxq[0].checked==true) { document.form1.maxq.disabled=true;document.form1.maxq.style.background='silver'; }
				else if (document.form1.checkmaxq[1].checked==true) { document.form1.maxq.disabled=false;document.form1.maxq.style.background='white'; }
				</script>
				</TD>
			</tr>
			<!--
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			-->
			<TR>
				<TD class="td_con_orange" style="border-left:none; border-top:1px solid; border-top-color:rgb(255,153,51);"><b><span class="font_orange">상품 이미지 등록</span></b></TD>
				<TD class="td_con_orange" colspan="3" style="border-top-width:1pt; border-top-color:rgb(255,153,51); border-top-style:solid;">
					<?
						// if 전용상품권 이미지 설정값이 없다믄 배경 이미지 개별등록 방식을 기본으로 설정 j.bum
						// else 있으믄 있는걸로 설정 j.bum
						if($_data->img_type == '') {
							$CKDD1 = 'checked';
						}else{
							${"CKDD".$_data->img_type} = "checked";
						}
					?>
					<input type=hidden name=use_imgurl value="">
					<input type="radio" name="img_type" value="0" <?=$CKDD0?>> 이미지 방식&nbsp;&nbsp;
					<input type="radio" name="img_type" value="1" <?=$CKDD1?>> 배경이미지 별도 등록 (텍스트 출력)&nbsp;&nbsp;
					<? if(!$_data->productcode) { ?>
						<input type="radio" name="img_type" value="2"> 기본 배경이미지 이용 (텍스트 출력)
					<? } ?>
				</td>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">대 이미지</TD>
				<TD class="td_con1" colspan="3">
					<input type=file name="userfile" onchange="document.getElementById('size_checker').src=this.value;" style="WIDTH: 400px" class="input">
					<input type=text name="userfile_url" value="<?=$userfile_url?>" style="WIDTH: 400px; display:none" class="input">
					<span class="font_orange">(권장 이미지 : 222 X 105)</span><br />
					<input type=hidden name="vimage" value="<?=$_data->maximage?>">
<?
			if ($_data) {
				if (strlen($_data->maximage)>0 && file_exists($imagepath.$_data->maximage)==true) {
					echo "<img src='".$imagepath.$_data->maximage."' height=100 width=200 border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/".$_data->maximage."'>";
					echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('1')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a><br />";
				} else {
					echo "<img src=\"images/space01.gif\"><br />";
				}
			}
?>
					<input type="checkbox" id="idx_imgcheck1" name=imgcheck value="Y"<?if (strlen($_data->minimage)>0 || strlen($row->tinyimage)>0) echo "onclick=PrdtAutoImgMsg()"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_imgcheck1">대 이미지로 중, 소 이미지 자동생성(중, 소 권장 사이즈로 생성)</label>
				</TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">중 이미지</TD>
				<TD class="td_con1" colspan="3">
				<input type=file name="userfile2" style="WIDTH: 400px" onchange="document.getElementById('size_checker2').src = this.value;" class="input">
				<input type=text name="userfile2_url" value="<?=$userfile2_url?>" style="WIDTH: 400px; display:none" class="input">
				<span class="font_orange">(권장 이미지 : 222 X 105)</font>
				<input type=hidden name="vimage2" value="<?=$_data->minimage?>">
<?
			if ($_data) {
				if (strlen($_data->minimage)>0 && file_exists($imagepath.$_data->minimage)==true){
					echo "<br><img src='".$imagepath.$_data->minimage."' height=80 width=150 border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/".$_data->minimage."'>";
					echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('2')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
				} else {
					echo "<br><img src=images/space01.gif>";
				}
			}
?>
				</TD>
			</TR>
			<TR>
				<TD colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell" style="border-bottom-width:1pt; border-bottom-color:rgb(255,153,51); border-bottom-style:solid;"><img src="images/icon_point5.gif" width="8" height="11" border="0">소 이미지</TD>
				<TD class="td_con1" colspan="3" style="border-bottom-width:1pt; border-bottom-color:rgb(255,153,51); border-bottom-style:solid;">
					<input type=file name="userfile3" style="WIDTH: 400px" onchange="document.getElementById('size_checker3').src = this.value;" class="input">
					<input type=text name="userfile3_url" value="<?=$userfile3_url?>" style="WIDTH: 400px; display:none" class="input">
					<span class="font_orange">(권장 이미지 : 222 X 105)</font>
					<input type=hidden name=setcolor value="<?=$setcolor?>">
					<input type=hidden name="vimage3" value="<?=$_data->tinyimage?>">
<?
			if ($_data) {
				if (strlen($_data->tinyimage)>0 && file_exists($imagepath.$_data->tinyimage)==true){
					echo "<br><img src='".$imagepath.$_data->tinyimage."' height=70 width=120 border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/".$_data->tinyimage."'>";
					echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('3')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
				} else {
					echo "<br><img src=images/space01.gif>";
				}
			}
?>
				<BR />
					<input type=checkbox name=imgborder value="Y" <?=(($imgborder)=="Y"?"checked":"")?>>신규 상품등록시 외곽 테두리선 생성 &nbsp;
					( 테두리 색상 : <span id="ColorPreview" style="width:15px; height:15px; background: #<?=$setcolor?>;"></span> &nbsp;
					<a href="javascript:SelectColor();"><img src="images/btn_color.gif" width="111" height="16" border="0" align="absmiddle" /></a> )
				</TD>
			</TR>

			<tr>
				<TD class="td_con_orange" style="border-left:none;"><B><span class="font_orange">상품 상세내역 입력</span></B></td>
				<td class="td_con_orange" colspan="3"><? if($predit_type=="Y"){?><input type=radio id="idx_checkedit1" name=checkedit checked onclick="JavaScript:htmlsetmode('wysiwyg',this)"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_checkedit1>웹편집기로 입력하기(권장)</label> &nbsp;&nbsp; <input type=radio id="idx_checkedit2" name=checkedit onclick="JavaScript:htmlsetmode('textedit',this);"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_checkedit2>직접 HTML로 입력하기</label><? } ?>&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox id="idx_localsave" name=localsave value="Y" <?=($localsave=="Y"?"checked":"")?> onclick="alert('상품 상세내역에 링크된 타서버 이미지를 본 쇼핑몰에 저장 후 링크를 변경하는 기능입니다.')"> <label style='cursor:hand;' onmouseover="style.textDecoration='none'" onmouseout="style.textDecoration='none'" for=idx_localsave><span class="font_orange"><B>타서버 이미지 쇼핑몰에 저장</B></span></label></td>
			</tr>
			<tr>
				<TD colspan="4">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><textarea wrap=off style="WIDTH: 100%; HEIGHT: 300px" name=content><?=htmlspecialchars($_data->content)?></textarea></td>
						</tr>
					</table>
				</TD>
			</tr>
			<tr>
				<td colspan="4"><img id="size_checker" style="display:none;"><img id="size_checker2" style="display:none;"><img id="size_checker3" style="display:none;"></td>
			</tr>
			<TR>
				<TD colspan=4 background="images/table_top_line.gif"></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		<tr>
			<td>
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<tr>
				<td colspan="2"><input type=checkbox id="idx_insertdate20" name=insertdate2 value="Y" onclick="DateFixAll(this)" <?=($insertdate_cook=="Y")?"checked":"";?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_insertdate20><span class="font_orange">상품등록날짜 고정</span></label></td>
			</tr>
			<tr>
				<td align="center" width="100%">
				<? if (strlen($prcode)==0) { ?>
						<a href="javascript:CheckForm('insert');"><img src="images/btn_new.gif" align=absmiddle width="144" height="38" border="0" vspace="5"></a>
				<? } else {?>
						<a href="javascript:CheckForm('modify');"><B><img src="images/btn_infoedit.gif" align=absmiddle width="162" height="38" border="0" vspace="5"></B></a>
						&nbsp;
						<a href="javascript:PrdtDelete();"><B><img src="images/btn_infodelete.gif" align=absmiddle width="113" height="38" border="0" vspace="5"></B></a>
				<? }?>
							</td>
							<td align="right">
				<? if (strlen($prcode)>0) { ?>
						<a href="JavaScript:NewPrdtInsert()"  onMouseOver="window.status='신규입력';return true;"><img src="images/product_newregicn.gif" align=absmiddle border="0" width="142" height="38" vspace="5"></a>
				<? } ?>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="30"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
			<TR>
				<TD><IMG SRC="images/design_eachjoin_stitle2.gif"  ALT=""></TD>
				<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
				<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		<tr>
			<td height=3></td>
		</tr>
		<tr>
			<td>
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<col width=160></col>
			<col width=></col>
			<TR>
				<TD colspan=2 background="images/table_top_line.gif"></TD>
			</TR>
			
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">상품진열여부</TD>
				<TD class="td_con1"><input type=radio id="idx_display1" name=display value="Y" <? if ($_data) { if ($_data->display=="Y") echo "checked"; } else echo "checked";  ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_display1>진열함</label> &nbsp; <input type=radio id="idx_display2" name=display value="N" <? if ($_data) { if ($_data->display=="N") echo "checked"; } ?> onclick="JavaScript:alert('메인 화면의 상품 특징이 없음으로 변경됩니다.')"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_display2>진열안함</label></TD>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>
			
			<TR>
				<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">상품기타설정</TD>
				<TD class="td_con1">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				
				<? if ($card_splittype=="O") { ?>
				<tr>
					<td><input type=checkbox id="idx_setquota1" name=setquota value="Y" <? if ($_data) { if ($setquota=="Y") echo "checked";}?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_setquota1>상점부담 무이자</label> <span class="font_orange">(결제금액/무이자할부개월은 <a  href="shop_payment.php"><b>결제관련기능설정</b></a>과 동일)</span></td>
					<td></td>
				</tr>
				<? } ?>
				

				<TR>
					<TD style="PADDING-TOP: 5px"><input type=checkbox id="idx_deliinfono1" name=deliinfono value="Y" <? if ($_data) { if ($deliinfono=="Y") echo "checked";}?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfono1>배송/교환/환불정보 노출안함</label> <font color=#AA0000>(상품상세화면 하단에 배송/교환/환불정보가 노출안됨)</font></TD>
					<td></td>
				</TR>

				</TABLE>
				</TD>
			</TR>
			<TR>
				<TD colspan=2 background="images/table_top_line.gif"></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		<tr>
			<td>
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<tr>
				<td colspan="2"><input type=checkbox id="idx_insertdate30" name=insertdate3 value="Y" onclick="DateFixAll(this)" <?=($insertdate_cook=="Y")?"checked":"";?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_insertdate30><span class="font_orange">상품등록날짜 고정</span></label></td>
			</tr>
			<tr>
				<td align="center" width="100%">
				<? if (strlen($prcode)==0) { ?>
						<a href="javascript:CheckForm('insert');"><img src="images/btn_new.gif" align=absmiddle width="144" height="38" border="0" vspace="5"></a>
				<? } else {?>
						<a href="javascript:CheckForm('modify');"><B><img src="images/btn_infoedit.gif" align=absmiddle width="162" height="38" border="0" vspace="5"></B></a>
						&nbsp;
						<a href="javascript:PrdtDelete();"><B><img src="images/btn_infodelete.gif" align=absmiddle width="113" height="38" border="0" vspace="5"></B></a>
				<? }?>
							</td>
							<td align="right">
				<? if (strlen($prcode)>0) { ?>
						<a href="JavaScript:NewPrdtInsert()"  onMouseOver="window.status='신규입력';return true;"><img src="images/product_newregicn.gif" align=absmiddle border="0" width="142" height="38" vspace="5"></a>
				<? } ?>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
<?
	}
?>
		</td>
	</tr>
	<tr><td height=20 colspan=2></td></tr>
	<input type=hidden name=iconnum value='<?=$totaliconnum?>'>
	<input type=hidden name=iconvalue>
	<input type=hidden name=optnum1 value=<?=$optnum1?>>
	<input type=hidden name=optnum2 value=<?=$optnum2?>>
	</form>
	<form name=cForm action="<?=$_SERVER[PHP_SELF]?>" method=post>
	<input type=hidden name=mode>
	<input type=hidden name=popup value="<?=$popup?>">
	<input type=hidden name=code value=<?=$code?>>
	<input type=hidden name=prcode value=<?=$prcode?>>
	<input type=hidden name=delprdtimg>
	<input type=hidden name="vimage" value="<? if ($_data) echo $_data->maximage; ?>">
	<input type=hidden name="vimage2" value="<? if ($_data) echo $_data->minimage; ?>">
	<input type=hidden name="vimage3" value="<? if ($_data) echo $_data->tinyimage; ?>">
	</form>
	<form name=icon action="product_iconmy.php" method=post target=icon>
	</form>
	<form name=iconlist action="product_iconlist.php" method=post target=iconlist>
	</form>
	<form name=vForm action="vender_infopop.php" method=post>
	<input type=hidden name=vender>
	</form>
	</table>
	</td>
</tr>
</table>
<?=$onload?>

<?
if (strlen($code)==12 && $predit_type=="Y") {
?>
<script language="Javascript1.2" src="htmlarea/editor.js"></script>
<script language="JavaScript">
function htmlsetmode(mode,i){
	if(mode==document.form1.htmlmode.value) {
		return;
	} else {
		i.checked=true;
		editor_setmode('content',mode);
	}
	document.form1.htmlmode.value=mode;
} 
_editor_url = "htmlarea/";
editor_generate('content');
</script>
<?
}
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(mode) {
<?if ($gongtype=="Y") {?>
	gongname="시작가";
	gongname2="공구가";
<?} else {?>
     gongname="소비자가격";
     gongname2="판매가격";
<?}?>
	if (document.form1.productname.value.length==0) {
		alert("상품권명을 입력하세요.");
		document.form1.productname.focus();
		return;
	}
	if (CheckLength(document.form1.productname)>100) {
		alert('총 입력가능한 길이가 한글 50자까지입니다. 다시한번 확인하시기 바랍니다.');
		document.form1.productname.focus();
		return;
	}
	
	if (document.form1.consumerprice.value.length==0) {
		alert("적립금액을 입력하세요.");
		document.form1.consumerprice.focus();
		return;
	}
	
	if (isNaN(document.form1.consumerprice.value)) {
		alert("적립금액은 숫자로만 입력하세요.(콤마제외)");
		document.form1.consumerprice.focus();
		return;
	}
	
<?if($_data->vender<=0){?>
	if(document.form1.sellprice.disabled==false) {
		if (document.form1.sellprice.value.length==0) {
			alert(gongname2+"을 입력하세요.");
			document.form1.sellprice.focus();
			return;
		}
		if (isNaN(document.form1.sellprice.value)) {
			alert(gongname2+"을 숫자로만 입력하세요.(콤마제외)");
			document.form1.consumerprice.focus();
			return;
		}
	}
<?}?>
	/*
	if (document.form1.reserve.value.length>0) {
		if(document.form1.reservetype.value=="Y") {
			if(isDigitSpecial(document.form1.reserve.value,".")) {
				alert("적립률은 숫자와 특수문자 소수점\(.\)으로만 입력하세요.");
				document.form1.reserve.focus();
				return;
			}
			
			if(getSplitCount(document.form1.reserve.value,".")>2) {
				alert("적립률 소수점\(.\)은 한번만 사용가능합니다.");
				document.form1.reserve.focus();
				return;
			}

			if(getPointCount(document.form1.reserve.value,".",2)==true) {
				alert("적립률은 소수점 이하 둘째자리까지만 입력 가능합니다.");
				document.form1.reserve.focus();
				return;
			}

			if(Number(document.form1.reserve.value)>100 || Number(document.form1.reserve.value)<0) {
				alert("적립률은 0 보다 크고 100 보다 작은 수를 입력해 주세요.");
				document.form1.reserve.focus();
				return;
			}
		} else {
			if(isDigitSpecial(document.form1.reserve.value,"")) {
				alert("적립금은 숫자로만 입력하세요.");
				document.form1.reserve.focus();
				return;
			}
		}
	}
	*/
<?if ($gongtype=="N") {?>
	if (document.form1.checkquantity[2].checked==true) {
<?} else {?>
	if (document.form1.checkquantity[1].checked==true) {
<?}?>
		if (document.form1.quantity.value.length==0) {
			alert("수량을 입력하세요.");
			document.form1.quantity.focus();
			return;
		} else if (isNaN(document.form1.quantity.value)) {
			alert("수량을 숫자로만 입력하세요.");
			document.form1.quantity.focus();
			return;
		} else if (parseInt(document.form1.quantity.value)<=0) {
			alert("수량은 0개이상이여야 합니다.");
			document.form1.quantity.focus();
			return;
		}
	}
	miniq_obj=document.form1.miniq;
	maxq_obj=document.form1.maxq;
	if (miniq_obj.value.length>0) {
		if (isNaN(miniq_obj.value)) {
			alert ("최소주문한도는 숫자로만 입력해 주세요.");
			miniq_obj.focus();
			return;
		}
	}
	if (document.form1.checkmaxq[1].checked==true) {
		if (maxq_obj.value.length==0) {
			alert ("최대주문한도의 수량을 입력해 주세요.");
			maxq_obj.focus();
			return;
		} else if (isNaN(maxq_obj.value)) {
			alert ("최대주문한도의 수량을 숫자로만 입력해 주세요.");
			maxq_obj.focus();
			return;
		}
	}
	if (miniq_obj.value.length>0 && document.form1.checkmaxq[1].checked==true && maxq_obj.value.length>0) {
		if (parseInt(miniq_obj.value) > parseInt(maxq_obj.value)) {
			alert ("최소주문한도는 최대주문한도 보다 작아야 합니다.");
			miniq_obj.focus();
			return;
		}
	}
	/*
	if(document.form1.deli[3].checked==true || document.form1.deli[4].checked==true) {
		if(document.form1.deli[3].checked==true)
		{
			if (document.form1.deli_price_value1.value.length==0) {
				alert("개별배송비를 입력하세요.");
				document.form1.deli_price_value1.focus();
				return;
			} else if (isNaN(document.form1.deli_price_value1.value)) {
				alert("개별배송비는 숫자로만 입력하세요.");
				document.form1.deli_price_value1.focus();
				return;
			} else if (parseInt(document.form1.deli_price_value1.value)<=0) {
				alert("개별배송비는 0원 이상 입력하셔야 합니다.");
				document.form1.deli_price_value1.focus();
				return;
			}
		}
		else
		{
			if (document.form1.deli_price_value2.value.length==0) {
				alert("개별배송비를 입력하세요.");
				document.form1.deli_price_value2.focus();
				return;
			} else if (isNaN(document.form1.deli_price_value2.value)) {
				alert("개별배송비는 숫자로만 입력하세요.");
				document.form1.deli_price_value2.focus();
				return;
			} else if (parseInt(document.form1.deli_price_value2.value)<=0) {
				alert("개별배송비는 0원 이상 입력하셔야 합니다.");
				document.form1.deli_price_value2.focus();
				return;
			}
		}
	}
	

	searchtype=false;
	for(i=0;i<document.form1.searchtype.length;i++) {
		if(document.form1.searchtype[i].checked==true) {
			searchtype=true;
			shop="layer"+i;
			break;
		}
	}

	if(searchtype==false) {
		alert("옵션 타입을 선택하세요.\n\n공동구매 타입의 상품일 경우 옵션그룹 사용이 불가하오니\n잘 확인하시기 바랍니다.");
		document.form1.searchtype[0].focus();
		return;
	}
	
	if(document.form1.sellprice.disabled==false) {
		if(shop=="layer0") {

		} else if(shop=="layer1"){
			optnum1=0;
			optnum2=0;

			//옵션1 항목
			document.form1.option1.value="";
			for(i=0;i<10;i++){
				if(document.form1.optname1[i].value.length>0) {
					document.form1.option1.value+=document.form1.optname1[i].value+",";
					optnum1++;
				}
			}

			//옵션1 제목 검사 (옵션1 항목이 NULL이 아니면)
			if((document.form1.option1.value.length!=0 && document.form1.option1_name.value.length==0)
			|| (document.form1.option1.value.length==0 && document.form1.option1_name.value.length!=0)){
				alert('각 옵션별 조건입력과 [옵션제목]을 확인해주세요!');
				if(document.form1.option1_name.value.length==0) {
					document.form1.option1_name.focus();
				} else {
					document.form1.optname1[0].focus();
				}
				return;
			}

			//옵션2 항목
			document.form1.option2.value="";
			for(i=0;i<5;i++){
				if(document.form1.optname2[i].value.length>0) {
					document.form1.option2.value+=document.form1.optname2[i].value+",";
					optnum2++;
				}
			}

			//옵션2 제목 검사 (옵션2 항목이 NULL이 아니면)
			if((document.form1.option2.value.length!=0 && document.form1.option2_name.value.length==0)
			|| (document.form1.option2.value.length==0 && document.form1.option2_name.value.length!=0)){
				alert('각 옵션별 조건입력과 [옵션제목]을 확인해주세요!');
				if(document.form1.option2_name.value.length==0) {
					document.form1.option2_name.focus();
				} else {
					document.form1.optname2[0].focus();
				}
				return;
			}

			//옵션2만 입력했는지 검사
			if(document.form1.option1.value.length==0 && document.form1.option2.value.length>0) {
				alert('옵션2는 옵션1 입력후 입력가능합니다.');
				document.form1.option1_name.focus();
				return;
			}

			//옵션1에 따른 가격 검사
			document.form1.option_price.value="";
			pricecnt=0;
			for(i=0;i<optnum1;i++){
				if(document.form1.optprice[i].value.length==0){
					pricecnt++;
				}else{
					document.form1.option_price.value+=document.form1.optprice[i].value+",";
				}
			}
			if(optnum1>0 && pricecnt!=0 && pricecnt!=optnum1){
				alert('옵션별 가격은 모두 입력하거나 모두 입력하지 않아야 합니다.');
				document.form1.optprice[0].focus();
				return;
			}

			if(document.form1.option_price.value.length!=0) temp=0;
			else temp=-1;
			temp2=document.form1.option_price.value;
			while(temp!=-1){
				temp=temp2.indexOf(",");
				if(temp!=-1) temp3=(temp2.substring(0,temp));
				else temp3=temp2;
				if(isNaN(temp3)){
					alert("옵션 가격은 숫자만 입력을 하셔야 합니다.");
					document.form1.option_price.focus();
					return;
				}
				temp2=temp2.substring(temp+1);
			}

			//재고수량 및 숫자검사
			isquan=false;
			quanobj="";
			for(i=0;i<10;i++) {
				isgbn1=false;
				if(i<optnum1) isgbn1=true;

				for(j=0;j<5;j++) {
					isgbn2=false;
					if(optnum2>0) {
						if(j<optnum2 && isgbn1==true) isgbn2=true;
					} else {
						if(j==0 && isgbn1==true) isgbn2=true;
					}

					if(isgbn2==true) {
						if(isquan==false && document.form1["optnumvalue["+j+"]["+i+"]"].value.length==0) {
							isquan=true;
							quanobj=document.form1["optnumvalue["+j+"]["+i+"]"];
						}
					} else {
						if(document.form1["optnumvalue["+j+"]["+i+"]"].value.length>0) {
							alert("입력하신 수량이 옵션정보의 범위를 넘었습니다. ("+(i+1)+" 째줄 "+(j+1)+" 째칸)");
							document.form1["optnumvalue["+j+"]["+i+"]"]. focus();
							return;
						}
					}
				}
			}
			if(isquan==true) {
				if(!confirm("수량 입력이 안된 옵션정보는 무제한 수량으로 등록됩니다.\n\n계속 하시겠습니까?")) {
					quanobj.focus();
					return;
				}
			}

		} else if(shop=="layer2"){
			if (document.form1.toption_price.value.length!=0 && document.form1.toption1.value.length==0) {
				alert("특수코드별가격을 입력하면 반드시 특수코드입력1에도 내용을 입력해야 합니다.");
				document.form1.toption1.focus();
				return;
			}
			if(document.form1.toption_price.value.length!=0) temp=0;
			else temp=-1;
			temp2=document.form1.toption_price.value;
			while(temp!=-1){
				temp=temp2.indexOf(",");
				if(temp!=-1) temp3=(temp2.substring(0,temp));
				else temp3=temp2;
				temp4=" "+temp3;
				if(isNaN(temp3) || temp4.indexOf('.')>0){
					alert("옵션 가격은 숫자만 입력을 하셔야 합니다.");
					document.form1.toption_price.focus();
					return;
				}
				temp2=temp2.substring(temp+1);
			}
			document.form1.option_price.value=document.form1.toption_price.value+",";
			document.form1.option1_name.value=document.form1.toptname1.value;
			document.form1.option1.value=document.form1.toption1.value+",";
			document.form1.option2_name.value=document.form1.toptname2.value;
			document.form1.option2.value=document.form1.toption2.value+",";
<?	if($gongtype=="N" && (int)$_data->vender==0) { ?>
		} else if(shop=="layer3") {
			if(document.form1.optiongroup.selectedIndex==0) {
				alert("옵션그룹을 선택하세요.");
				document.form1.optiongroup.focus();
				return;
			}
<? } ?>
		}
	}
	*/

	//if(document.form1.use_imgurl.checked!=true) {
		filesize = Number(document.form1.size_checker.fileSize) + Number(document.form1.size_checker2.fileSize) + Number(document.form1.size_checker3.fileSize) ;
		if(filesize><?=$maxfilesize?>) { 
			alert('올리시려고 하는 파일용량이 500K이상입니다.\n파일용량을 체크하신후에 다시 이미지를 올려주세요');
			return;
		}
	//}
	tempcontent = document.form1.content.value;
<?if ($predit_type=="Y"){ ?>
	if(mode=="modify" && tempcontent.length>0 && tempcontent.indexOf("<")==-1 && tempcontent.indexOf(">")==-1 && !confirm("웹편집기 기능추가로 텍스트로만 입력하신 상세설명은\n줄바꾸기가 해제되어 쇼핑몰에서 다르게 보여질 수 있습니다.\n\n재입력하시거나 현재 쇼핑몰에서 해당 상품의 상세설명을\n그대로 마우스로 드래그하여 붙여넣기를 해서 재입력하셔야 합니다.\n\n위와 같이 수정하지 않고 저장하시려면 [확인]을 누르세요.")){
		return;
	}
<?}?>
	/*
	document.form1.iconvalue.value="";
	num = document.form1.iconnum.value;
	for(i=0;i<num;i++){
		if(document.form1.icon[i].checked==true) document.form1.iconvalue.value+=document.form1.icon[i].value;
	}
	*/
	if (document.form1.insertdate1.checked==true) document.form1.insertdate.value="Y";
	
	document.form1.mode.value=mode;
	document.form1.submit();
}

<?
if($popup=="YES"){
	echo "window.moveTo(10,10);\nwindow.resizeTo(820,700);";
}
?>
//-->
</SCRIPT>

<?
if($searchtype==2 || $optionover=="YES") {
	echo "<script>document.form1.searchtype[2].checked=true;\nViewLayer('layer2');</script>";
} else if($searchtype==1) {
	echo "<script>document.form1.searchtype[1].checked=true;\nViewLayer('layer1');</script>";
} else if($searchtype==3 && $gongtype=="N" && (int)$_data->vender==0) {
	echo "<script>document.form1.searchtype[3].checked=true;\nViewLayer('layer3');</script>";
}
?>
</body>
</html>
