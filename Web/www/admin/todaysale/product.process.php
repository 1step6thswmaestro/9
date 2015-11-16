<?php
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include ("../access.php");
include_once($Dir."lib/ext/product_func.php");


$sql = "SELECT recom_memreserve_type, sns_ok, sns_reserve_type ";
$sql.= "FROM tblshopinfo ";
$result=mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$recom_memreserve_type=$row->recom_memreserve_type;
	$sns_ok=$row->sns_ok;
	$sns_reserve_type=$row->sns_reserve_type;
	$arRecomType = explode("",$recom_memreserve_type);
	$arSnsType = explode("",$sns_reserve_type);
}

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include ("../AccessDeny.inc.php");
	exit;
}
#########################################################
$userspec_cnt=5;
$maxfilesize="512000";
$mode=$_POST["mode"];

$code='899000000000';
$prcode=$_POST["prcode"];
$productcode=$_POST["productcode"];
$productname=$_POST["productname"];
$productdisprice=$_POST["productdisprice"];
$vimage=$_POST["vimage"];
$vimage2=$_POST["vimage2"];
$vimage3=$_POST["vimage3"];

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
$vender = $_POST['vender'];
$deli=$_POST["deli"];

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

$gonggu_product = $_POST["gonggu_product"];

$sns_state=$_POST["sns_state"];
$present_state=$_POST["present_state"];
$pester_state=$_POST["pester_state"];
if($sns_state =="Y" && $arSnsType[0] =="B"){
	$sns_reserve1=$_POST["sns_reserve1"];
	$sns_reserve1_type=$_POST["sns_reserve1_type"];
	$sns_reserve2=$_POST["sns_reserve2"];
	$sns_reserve2_type=$_POST["sns_reserve2_type"];
}else{
	$sns_reserve1="";
	$sns_reserve1_type="";
	$sns_reserve2="";
	$sns_reserve2_type="";
}
$first_reserve=$_POST["first_reserve"];
$first_reserve_type=$_POST["first_reserve_type"];

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

$etcapply_coupon=$_POST["etcapply_coupon"];
$etcapply_reserve=$_POST["etcapply_reserve"];
$etcapply_gift=$_POST["etcapply_gift"];
$etcapply_return=$_POST["etcapply_return"];

if($etcapply_coupon!="Y") $etcapply_coupon="N";
if($etcapply_reserve!="Y") $etcapply_reserve="N";
if($etcapply_gift!="Y") $etcapply_gift="N";
if($etcapply_return!="Y") $etcapply_return="N";

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

$maxsize=130;
$makesize=130;

$card_splittype = $_shopdata->card_splittype;
$makesize=$_shopdata->primg_minisize;
$predit_type=$_shopdata->predit_type;


$maxsize=$makesize+10;
if(substr($prcode,0,12)!=$code && strlen($prcode)>0) {
	$prcode="";
	$maxq="";
}

if (strlen($mode)==0) $maxq="";


################################상품별 회원할인율######################################
$group_code=(array)$_POST["group_code"];
$discount_rates=(array)$_POST["discount_rates"];
$discount_prices=(array)$_POST["discount_prices"];
$over_discount=$_POST["over_discount"];

if($mode=="discountprd"){
	for($i=0;$i<count($group_code);$i++) {
		$sql = "SELECT COUNT(dsidx) as cnt FROM tblmemberdiscount ";
		$sql.= "WHERE productcode ='".$prcode."' AND group_code='".$group_code[$i]."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);

		if($row->cnt>0){	//등록된 항목인 있는 경우는 update
			$sql = "UPDATE tblmemberdiscount SET ";
			$sql.= "discountrates		= '".$discount_rates[$i]."', ";
			$sql.= "discountprices		= ".$discount_prices[$i].", ";
			$sql.= "over_discount		= '".$over_discount."' ";
			$sql.= "WHERE productcode	= '".$prcode."' ";
			$sql.= "AND group_code		= '".$group_code[$i]."' ";

			mysql_query($sql,get_db_conn());
		}else{
			$sql = "INSERT INTO tblmemberdiscount (group_code,productcode,discountrates,discountprices,over_discount) VALUES( ";
			$sql.= "'".$group_code[$i]."', ";
			$sql.= "'".$prcode."', ";
			$sql.= "'".$discount_rates[$i]."', ";
			$sql.= $discount_prices[$i].", ";
			$sql.= "'".$over_discount."') ";
			mysql_query($sql,get_db_conn());
		}
	}

	$onload="<script>alert('상품별 회원할인율 적용하였습니다.');</script>";
}
################################상품별 회원할인율######################################

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
	for($i=0;$i<10;$i++){
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
		for($i=0;$i<3;$i++){
			if($use_imgurl!="Y") {
				if ($mode=="modify" && strlen($vimagear[$i])>0 && strlen($filename[$i])>0 && file_exists($imagepath.$vimagear[$i])) {
					unlink($imagepath.$vimagear[$i]);
				}
				if (strlen($filename[$i])>0 && file_exists($file[$i])) {
					$ext = strtolower(substr($filename[$i],strlen($filename[$i])-3,3));
					if ($ext=="gif" || $ext=="jpg") {
						$image[$i] = $image_name.$imgnum[$i].".".$ext;
						move_uploaded_file($file[$i],$imagepath.$image[$i]);
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
			$makesize1=300;
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
					imageJPEG($im2,$imgname,95);
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
			$makesize2=250;
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
					imageJPEG($im2,$imgname,95);
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
					imageJPEG($im,$imgname,95);
				} else {
					$color = ImageColorAllocate($im,$rcolor,$gcolor,$bcolor);
					imagerectangle ($im, 0, 0, $width-1, $height-1,$color );
					imagePNG($im,$imgname);
				}
				ImageDestroy($im);
			}
		}
		if($_POST['quantity_unlimit'] == '1') $quantity="NULL";
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

		/** 에디터 관련 파일 처리 추가 부분 */
		if(preg_match_all('/\/data\/editor_temp\/([a-zA-Z0-9\.]+)/',$in_content,$edimg)){
			foreach($edimg[1] as $timg){
				@rename($_SERVER['DOCUMENT_ROOT'].'/data/editor_temp/'.$timg,$_SERVER['DOCUMENT_ROOT'].'/data/editor/'.$timg);
			}
			$in_content = str_replace('/data/editor_temp/','/data/editor/',$in_content);
		}
		/** #에디터 관련 파일 처리 추가 부분 */

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

		
		if(!_isInt($vender)) $vender = "0";		
		$sql .= "vender		= '".$vender."', ";

		$sql.= "etcapply_coupon	= '".$etcapply_coupon."', ";
		$sql.= "etcapply_reserve= '".$etcapply_reserve."', ";
		$sql.= "etcapply_gift	= '".$etcapply_gift."', ";
		$sql.= "etcapply_return	= '".$etcapply_return."', ";

		$sql.= "display			= '".$display."', ";
		$sql.= "date			= '".$curdate."', ";
		$sql.= "regdate			= now(), ";
		$sql.= "modifydate		= now(), ";
		$sql.= "content			= '".$in_content."', ";
		$sql.= "sns_state		= '".$sns_state."', ";
		$sql.= "present_state	= '".$present_state."', ";
		$sql.= "pester_state	= '".$pester_state."', ";
		$sql.= "gonggu_product		= '".$gonggu_product."', ";
		$sql.= "sns_reserve1		= '".$sns_reserve1."', ";
		$sql.= "sns_reserve1_type	= '".$sns_reserve1_type."', ";
		$sql.= "sns_reserve2		= '".$sns_reserve2."', ";
		$sql.= "sns_reserve2_type	= '".$sns_reserve2_type."', ";
		$sql.= "first_reserve		= '".$first_reserve."', ";
		$sql.= "first_reserve_type	= '".$first_reserve_type."', ";
		$sql.= "productdisprice	= '".$productdisprice."' ";


		if($insert = mysql_query($sql,get_db_conn())){
			// 상품정보고시 저장
			$pridx = mysql_insert_id(get_db_conn());
			$ditems = array();
			if( count($_REQUEST['didx']) ) {
				foreach($_REQUEST['didx'] as $k=>$v){
					$item = array();
					$item['didx'] = $v;
					$item['dtitle'] = $_REQUEST['dtitle'][$k];
					$item['dcontent'] = $_REQUEST['dcontent'][$k];
					array_push($ditems,$item);
				}
			}
			_editProductDetails($pridx,$ditems);

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
			
			
			// todaysale 전용 처리
			$sql = "insert into todaysale set ";
			$sql .= "`pridx`='".$pridx."',";
			$sql .= "`start`='".($_POST['start_date'].' '.sprintf('%02d',$_POST['start_hour']).':'.sprintf('%02d',$_POST['start_minute']))."',";
			$sql .= "`end`='".($_POST['end_date'].' '.sprintf('%02d',$_POST['end_hour']).':'.sprintf('%02d',$_POST['end_minute']))."',";			
			$sql .= "`addquantity`='".$_POST['addquantity']."',";
			$sql .= "`salecnt`='0'";
			mysql_query($sql,get_db_conn());
			

			$content=$in_content;
			$use_imgurl="";
			$userfile_url="";
			$userfile2_url="";
			$userfile3_url="";
			$log_content = "## 상품입력 ## - 코드 $code$productcode - 상품 : $productname 코디/조립 : $assembleuse 패키지그룹 : $package_num 가격 : $sellprice 수량 : $quantity 기타 : $etctype 적립금: $reserve $display";
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
			_alert('상품 등록이 완료 되었습니다.','/admin/todaysale.php');
		} else {
			_alert('등록중 오류가 발생했습니다.','-1');
		}		
	} else if ($mode=="delete") {
		$sql = "SELECT vender,display,brand,pridx,assembleuse,assembleproduct FROM tblproduct WHERE productcode = '".$prcode."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		mysql_free_result($result);

		/** 에디터 관련 파일 처리 추가 부분 */
		if(preg_match_all('/\/data\/editor\/([a-zA-Z0-9\.]+)/',$row->content,$edimg)){
			foreach($edimg[1] as $timg){
				@unlink($_SERVER['DOCUMENT_ROOT'].'/data/editor/'.$timg);
			}
		}
		/** #에디터 관련 파일 처리 추가 부분 */

		$vender=(int)$row->vender;
		$vdisp=$row->display;
		$brand=$row->brand;
		$vpridx=$row->pridx;
		$vassembleuse=$row->assembleuse;
		$vassembleproduct=$row->assembleproduct;

		// 필수 정보 지우기
		$sql = "delete from tblproduct_detail WHERE pridx = '".$vpridx."'";
		mysql_query($sql,get_db_conn());

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

		#추가 카테고리 관련 지우기
		$sql = "DELETE FROM tblcategorycode WHERE productcode = '".$prcode."'";
		mysql_query($sql,get_db_conn());

		
		$sql = "delete from todaysale where pridx='".$pridx."',";
		mysql_query($sql,get_db_conn());
		
		$log_content = "## 상품삭제 ## - 상품코드 $prcode - 상품명 : ".urldecode($productname)." $display";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

		delProductMultiImg("prdelete","",$prcode);

		// 상품정보고지 삭제
		_deleteProductDetails($pridx);
		
		_alert('상품 삭제가 완료 되었습니다.','/admin/todaysale.php');
	} else if ($mode=="modify") {
		$sql = "SELECT vender,display,brand,pridx,assembleuse,sellprice,assembleproduct FROM tblproduct WHERE productcode = '".$prcode."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		mysql_free_result($result);

		/** 에디터 관련 파일 처리 추가 부분 */
		if(preg_match_all('/\/data\/editor\/([a-zA-Z0-9\.]+)/',$row->content,$edtimg)){
			if(!preg_match_all('/\/data\/editor\/([a-zA-Z0-9\.]+)/',$in_content,$edimg)) $edimg[1] =array();
			foreach($edtimg[1] as $cimg){
				if(!in_array($cimg,$edimg[1])) @unlink($_SERVER['DOCUMENT_ROOT'].'/data/editor/'.$cimg);
			}
		}
		/** #에디터 관련 파일 처리 추가 부분 */	
		$vvender=(int)$row->vender;
		$vdisp=$row->display;
		$brand=$row->brand;
		$vassembleuse=$row->assembleuse;
		$vpridx=$row->pridx;
		$vsellprice=$row->sellprice;
		$vassembleproduct=$row->assembleproduct;

		if(strlen($buyprice) < 1 ) $buyprice = 0 ;

		/** 에디터 관련 파일 처리 추가 부분 */
		if(preg_match_all('/\/data\/editor_temp\/([a-zA-Z0-9\.]+)/',$in_content,$edimg)){
			foreach($edimg[1] as $timg){
				@rename($_SERVER['DOCUMENT_ROOT'].'/data/editor_temp/'.$timg,$_SERVER['DOCUMENT_ROOT'].'/data/editor/'.$timg);
			}
			$in_content = str_replace('/data/editor_temp/','/data/editor/',$in_content);
		}
		/** #에디터 관련 파일 처리 추가 부분 */

		$sql = "UPDATE tblproduct SET ";
		$sql.= "productname		= '".$productname."', ";
		$sql.= "consumerprice	= '".$consumerprice."', ";
		$sql.= "sellprice		= '".$sellprice."', ";
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
		
		$sql.= "assembleuse		= '".$assembleuse."', ";

		// update 시 벤더 처리
		if(!_isInt($vender)) $vender = "0";		
		$sql .= "vender		= '".$vender."', ";
		
		$sql.= "etctype			= '".$etctype."', ";
		$sql.= "deli_price		= '".$deli_price."', ";
		$sql.= "deli			= '".$deli."', ";
		$sql.= "display			= '".$display."', ";

		$sql.= "etcapply_coupon	= '".$etcapply_coupon."', ";
		$sql.= "etcapply_reserve= '".$etcapply_reserve."', ";
		$sql.= "etcapply_gift	= '".$etcapply_gift."', ";
		$sql.= "etcapply_return	= '".$etcapply_return."', ";
		$sql.= "modifydate		= now(), ";
		$sql.= "content			= '".$in_content."', ";
		$sql.= "sns_state		= '".$sns_state."', ";
		$sql.= "present_state	= '".$present_state."', ";
		$sql.= "pester_state	= '".$pester_state."', ";
		$sql.= "gonggu_product		= '".$gonggu_product."', ";
		$sql.= "sns_reserve1		= '".$sns_reserve1."', ";
		$sql.= "sns_reserve1_type	= '".$sns_reserve1_type."', ";
		$sql.= "sns_reserve2		= '".$sns_reserve2."', ";
		$sql.= "sns_reserve2_type	= '".$sns_reserve2_type."', ";
		$sql.= "first_reserve		= '".$first_reserve."', ";
		$sql.= "first_reserve_type	= '".$first_reserve_type."', ";
		$sql.= "productdisprice	= '".$productdisprice."' ";
		$sql.= "WHERE productcode = '".$prcode."' ";
		if(mysql_query($sql,get_db_conn())) {
			// 상품정보고지 수정
			$sql = "select pridx from tblproduct WHERE productcode = '".$prcode."'  limit 1";
			if(false !== $res = mysql_query($sql,get_db_conn())){
				$pridx = mysql_result($res,0,0);
				$ditems = array();
				if( count($_REQUEST['didx']) ) {
					foreach($_REQUEST['didx'] as $k=>$v){
						$item = array();
						$item['didx'] = $v;
						$item['dtitle'] = $_REQUEST['dtitle'][$k];
						$item['dcontent'] = $_REQUEST['dcontent'][$k];
						array_push($ditems,$item);
					}
				}
				_editProductDetails($pridx,$ditems);
			}


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
			
			
			// todaysale 전용 처리
			$sql = "update todaysale set ";			
			$sql .= "`start`='".($_POST['start_date'].' '.sprintf('%02d',$_POST['start_hour']).':'.sprintf('%02d',$_POST['start_minute']))."',";
			$sql .= "`end`='".($_POST['end_date'].' '.sprintf('%02d',$_POST['end_hour']).':'.sprintf('%02d',$_POST['end_minute']))."',";			
			$sql .= "`addquantity`='".$_POST['addquantity']."'";
			$sql .= " where `pridx`='".$vpridx."'";
			mysql_query($sql,get_db_conn());

			
			$content=$in_content;
			$use_imgurl="";
			$userfile_url="";
			$userfile2_url="";
			$userfile3_url="";
			$log_content = "## 상품수정 ## - 코드 $prcode - 상품 : $productname 가격 : $sellprice 수량 : $quantity 기타 : $etctype 적립금 : $reserve 날짜고정 : ".(($insertdate=="Y")?"Y":"N")." $display";
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
			_alert('상품이 수정되었습니다'.$message,'/admin/todaysale.php?mode=modify&productcode='.$prcode);
		}else{
			exit(mysql_error());
		}
		
	}
} else {
	_alert("상품이미지의 총 용량이 ".ceil($file_size/1024)."Kbyte로 500K가 넘습니다.\\n\\n한번에 올릴 수 있는 최대 용량은 500K입니다.\\n\\n이미지가 gif가 아니면 이미지 포맷을 바꾸어 올리시면 용량이 줄어듭니다.",'-1');	
}

//################# 500K가 넘는 이미지 체크
if ((strlen($userfile[name])>0 && $userfile[size]==0) || (strlen($userfile2[name])>0 && $userfile2[size]==0) || (strlen($userfile3[name])>0 && $userfile3[size]==0)) {
	_alert("상품이미지중 용량이 500K가 넘는 이미지가 있습니다.\\n\\n500K가 넘는 이미지는 등록되지 않습니다..\\n\\n이미지가 gif가 아니면 이미지 포맷을 바꾸어 올리시면 용량이 줄어듭니다.",'-1');
}

?>