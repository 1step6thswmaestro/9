<?
setlocale(LC_CTYPE, 'ko_KR.eucKR');
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");


include_once($Dir."lib/ext/product_func.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-4";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

@set_time_limit(300);


setlocale(LC_CTYPE, 'ko_KR.eucKR');

###################################### 입점기능 사용권한 체크 #######################################
$usevender=setUseVender();

unset($venderlist);
if($usevender==true) {
	$sql = "SELECT vender,id,com_name FROM tblvenderinfo WHERE disabled=0 AND delflag='N' ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	mysql_free_result($result);
}
#####################################################################################################

function CutStringY($str, $start, $end)
{
	$result = substr($str, $start, $end); // 일단 문자열을 자릅니다.
	preg_match('/^([\x00-\x7e]|.{2})*/', $result, $string);	// 뒤에 오는 ?를 없애줍니다..
	return $string[0];
}

$imagepath=$Dir.DataDir."shopimages/product/";
$filename="prdtexcelupfile.csv";
@unlink($imagepath.$filename);

if(strlen($setcolor)==0) $setcolor="000000";
$rcolor=HexDec(substr($setcolor,0,2));
$gcolor=HexDec(substr($setcolor,2,2));
$bcolor=HexDec(substr($setcolor,4,2));
$quality = "90";

$maxsize=130;
$makesize=130;

$maxsize=$makesize+10;
if(strpos(" ".$_shopdata->etctype,"IMGSERO=Y")) {
	$imgsero="Y";
}

$mode=$_POST["mode"];
$vender=(int)$_POST["vender"];
$code=$_POST["code"];
$upfile=$_FILES["upfile"];

$date1=date("Ym");		// 등록순서데로 순서 저장 필요 변수
$date=date("dHis");		// 등록순서데로 순서 저장 필요 변수

if($mode=="upload" && strlen($code)==12 && strlen($upfile[name])>0 && $upfile[size]>0) {
	########################### TEST 쇼핑몰 확인 ##########################
	//DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER[PHP_SELF]);
	#######################################################################

	//입점업체 확인
	if($vender>0 && strlen($venderlist[$vender]->vender)<=0) {
		$vender=0;
	}

	//분류 확인
	$sql = "SELECT type FROM tblproductcode ";
	$sql.= "WHERE codeA='".substr($code,0,3)."' AND codeB='".substr($code,3,3)."' ";
	$sql.= "AND codeC='".substr($code,6,3)."' AND codeD='".substr($code,9,3)."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		if(substr($row->type,-1)!="X") {
			echo "<html><head></head><body onload=\"alert('상품을 등록할 분류 선택이 잘못되었습니다.');location='".$_SERVER["PHP_SELF"]."'\"></body></html>";exit;
		}
	} else {
		echo "<html><head></head><body onload=\"alert('상품을 등록할 분류 선택이 잘못되었습니다.');location='".$_SERVER["PHP_SELF"]."'\"></body></html>";exit;
	}
	mysql_free_result($result);

	$ext = strtolower(substr($upfile[name],strlen($upfile[name])-3,3));
	if($ext=="csv") {
		/*
		$tempfile=@file($upfile[tmp_name]);
		if(count($tempfile)>101) {
			echo "<html><head></head><body onload=\"alert('1회 등록 가능한 상품수는 100개 까지 입니다.\\n\\n100개 이상일 경우 나누어 등록하시기 바랍니다.');location='".$_SERVER["PHP_SELF"]."'\"></body></html>";exit;
		}
		*/

		copy($upfile[tmp_name],$imagepath.$filename);
		chmod($imagepath.$filename,0664);
	} else {
		echo "<html><head></head><body onload=\"alert('파일형식이 잘못되어 업로드가 실패하였습니다.\\n\\n등록 가능한 파일은 텍스트(TXT) 파일만 등록 가능합니다.');location='".$_SERVER["PHP_SELF"]."'\"></body></html>";exit;
	}

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
			echo "<html><head></head><body onload=\"alert('상품코드를 생성하는데 실패했습니다. 잠시후 다시 시도하세요.');location='".$_SERVER["PHP_SELF"]."'\"></body></html>";exit;
		}
		mysql_free_result($result);
	} else {
		$productcode = "000001";
	}

	$sql = "SELECT bridx, brandname FROM tblproductbrand ";
	$result = mysql_query($sql,get_db_conn());
	while ($rows = mysql_fetch_object($result)) {
		$bridx[$rows->brandname] = $rows->bridx;
	}
	mysql_free_result($result);

	$i=0;
	$filepath=$imagepath.$filename;
	$fp=fopen($filepath,"r");
	$yy=0;

	while($field=fgetcsv($fp, 10000)) {
		if($yy++==0) continue;
		if(strlen($field[0])==0) {
			continue;
		}

		$field[1]=(int)$field[1];
		$field[2]=(int)$field[2];
		$field[3]=(int)$field[3];

		$reservetype="N";
		$field_exp="";
		if(strlen($field[10])>0) {
			$field_type = substr($field[10],-1);
			if($field_type!="%") {
				$field[10]=(int)$field[10];
				if($field[10]<0 || $field[10]>999999) {
					$field[10]=0;
				}
			} else {
				$reservetype="Y";
				$field[10]=substr($field[10],0,-1)*1;
				$field_exp = explode(".", $field[10]);

				if($field[10]<0 || $field[10]>100 || strlen($field[10])>5) {
					$field[10]=0;
				} else if(count($field_exp)>1 && strlen($field_exp[1])>2) {
					$field[10]=$field_exp[0].".".substr($field_exp[1],0,2);
				}
			}
		} else {
			$field[10]=0;
		}

		if(strlen($field[11])==0) $field[11]="NULL";
		if($field[15]!="Y") $field[15]="N";
		if($field[19]!="Y") $field[19]="N";

		if($i++>0) {
			$productcode = ((int)$productcode)+1;
			$productcode = sprintf("%06d",$productcode);
		}

		//판매가 체크
		if($field[2]<=0) {
			//판매가를 입력하세요.
		}

		//옵션항목 체크
		if(strlen($field[13])>0 && strlen($field[12])==0) {
			//선택사항1 가격을 입력하면 반드시 선택사항1에도 내용을 입력해야 합니다.
			$field[13]="";
		}

		if(strlen($field[12])==0) {
			$field[14]="";
		}

		if(strlen($field[12])>0 && strlen($field[13])>0) {
			$tempopt1=explode("|",$field[12]);
			$tempopt1_price=explode("|",$field[13]);
			if((count($tempopt1)-1)!=(count($tempopt1_price))) {
				$field[12]="";
				$field[13]="";
				$field[14]="";
			}
		} else if(strlen($field[12])>0) {
			$tempopt1=explode("|",$field[12]);
			if(count($tempopt1)<=1) {
				$field[12]="";
				$field[13]="";
				$field[14]="";
			}
		}
		if(strlen($field[14])>0) {
			$tempopt2=explode("|",$field[14]);
			if(count($tempopt2)<=1) {
				$field[14]="";
			}
		}
		$field[12]=str_replace("|",",",$field[12]);
		$field[12]=str_replace(" ,",",",$field[12]);
		$field[12]=str_replace(", ",",",$field[12]);
		$field[13]=str_replace("|",",",$field[13]);
		$field[13]=str_replace(" ,",",",$field[13]);
		$field[13]=str_replace(", ",",",$field[13]);
		$field[14]=str_replace("|",",",$field[14]);
		$field[14]=str_replace(" ,",",",$field[14]);
		$field[14]=str_replace(", ",",",$field[14]);

		$image_name = $code.$productcode;

		$maximage="";
		$minimage="";
		$tinyimage="";

		$maximage_url=$field[16];
		$minimage_url=$field[17];
		$tinyimage_url=$field[18];

		if(strlen($maximage_url)>0) {
			$maximage_url=eregi_replace("http://","",$maximage_url);
			$temp=explode("/",$maximage_url);
			$host=$temp[0];
			$path=str_replace(" ","%20", eregi_replace($host,"",$maximage_url));

			$ext=strtolower(substr(strrchr($maximage_url,"."),1));
			if($ext=="gif" || $ext=="jpg") {
				$maximage=$image_name.".".$ext;
				$fdata=getRemoteImageData($host,$path,$ext);

				if(strlen($fdata)>0) {
					$filepath=$imagepath.$maximage;
					$fp2=fopen($filepath,"w");
					fputs($fp2,$fdata);
					fclose($fp2);
					chmod($filepath,0664);
					$tempsize=@getimagesize($filepath);
					if($tempsize[0]>0 && $tempsize[1]>0 && (preg_match("/^(1|2)$/",$tempsize[2]))) {
						if($field[19]=="Y") {
							$minimage=$image_name."2.".$ext;
							$tinyimage=$image_name."3.".$ext;
							copy($imagepath.$maximage, $imagepath.$minimage);
							chmod($imagepath.$minimage,0664);
							copy($imagepath.$maximage, $imagepath.$tinyimage);
							chmod($imagepath.$tinyimage,0664);
						}
					} else {
						@unlink($filepath);
						$maximage="";
					}
				} else {
					$maximage="";
				}
			}
		}

		if($field[19]!="Y") {
			if(strlen($minimage_url)>0) {
				$minimage_url=eregi_replace("http://","",$minimage_url);
				$temp=explode("/",$minimage_url);
				$host=$temp[0];
				$path=str_replace(" ","%20", eregi_replace($host,"",$minimage_url));

				$ext=strtolower(substr(strrchr($minimage_url,"."),1));
				if($ext=="gif" || $ext=="jpg") {
					$minimage=$image_name."2.".$ext;
					$fdata=getRemoteImageData($host,$path,$ext);

					if(strlen($fdata)>0) {
						$filepath=$imagepath.$minimage;
						$fp2=fopen($filepath,"w");
						fputs($fp2,$fdata);
						fclose($fp2);
						chmod($filepath,0664);
						$tempsize=@getimagesize($filepath);
						if($tempsize[0]==0 || $tempsize[1]==0 || (!preg_match("/^(1|2)$/",$tempsize[2]))) {
							@unlink($filepath);
							$minimage="";
						}
					} else {
						$minimage="";
					}
				}
			}

			if(strlen($tinyimage_url)>0) {
				$tinyimage_url=eregi_replace("http://","",$tinyimage_url);
				$temp=explode("/",$tinyimage_url);
				$host=$temp[0];
				$path=str_replace(" ","%20", eregi_replace($host,"",$tinyimage_url));

				$ext=strtolower(substr(strrchr($tinyimage_url,"."),1));
				if($ext=="gif" || $ext=="jpg") {
					$tinyimage=$image_name."3.".$ext;
					$fdata=getRemoteImageData($host,$path,$ext);

					if(strlen($fdata)>0) {
						$filepath=$imagepath.$tinyimage;
						$fp2=fopen($filepath,"w");
						fputs($fp2,$fdata);
						fclose($fp2);
						chmod($filepath,0664);
						$tempsize=@getimagesize($filepath);
						if($tempsize[0]==0 || $tempsize[1]==0 || (!preg_match("/^(1|2)$/",$tempsize[2]))) {
							@unlink($filepath);
							$tinyimage="";
						}
					} else {
						$tinyimage="";
					}
				}
			}
		}


		if($field[19]=="Y" && strlen($minimage)>0) {
			$imgname=$imagepath.$minimage;
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

		if(strlen($tinyimage)>0) {
			$imgname=$imagepath.$tinyimage;
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
					imageGIF($im2,$imgname);
				} else if ($imgtype==2) {
					$im2=ImageCreateTrueColor($small_width,$small_height); // JPG일경우
					$white = ImageColorAllocate($im2, 255,255,255);
					imagefill($im2,1,1,$white);
					$color =ImageColorAllocate($im2,$rcolor,$gcolor,$bcolor);
					imagecopyresampled($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					imageJPEG($im2,$imgname,$quality);
				} else {
					$im2=ImageCreateTrueColor($small_width,$small_height); // PNG일경우
					$white = ImageColorAllocate($im2, 255,255,255);
					imagefill($im2,1,1,$white);
					$color =ImageColorAllocate($im2,$rcolor,$gcolor,$bcolor);
					imagecopyresampled($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					imagePNG($im2,$imgname);
				}

				ImageDestroy($im);
				ImageDestroy($im2);
			}
		}


		############################### 상품 상세설명 이미지 경로 변경 #############################
		$etcimagepath=$Dir.DataDir."design/etc/";
		if(is_dir($etcimagepath)==false) {
			mkdir($etcimagepath);
			chmod($etcimagepath,0755);
		}

		$body=$field[20];
		$arrimgurl=array();
		$p=explode("\n", $body);
		for($u=0;$u<count($p);$u++) {
			while(eregi("[^=\"']*\.(gif|jpg|bmp|png)([\"|\'| |>]){1}", $p[$u], $val)){
				$arrimgurl[substr($val[0],0,-1)]=substr($val[0],0,-1);
				$p[$u]=str_replace(substr($val[0],0,-1),"",$p[$u]);
			}
		}

		while(list($key,$val)=each($arrimgurl)) {
			$file_url=urldecode($val);
			if(substr($file_url,0,7)=="http://") {
				$file_url=eregi_replace("http://","",$file_url);
				$temp=explode("/",$file_url);
				$host=$temp[0];
				$path=str_replace(" ","%20", eregi_replace($host,"",$file_url));

				$filename=substr(strrchr($file_url,"/"),1);
				$x=0;
				while(file_exists($etcimagepath.$filename)){
					$file_ext=substr( strrchr($filename,"."),1);
					$file_name=substr($filename, 0, strlen($filename) - strlen(strrchr($filename,".")));
					$file_name=substr($file_name, 0, strlen($file_name) - strlen(strrchr($file_name,"[")));

					$filename=$file_name."[".$x."].".$file_ext;
					$x++ ;
				}

				$ext=substr(strrchr($filename,"."),1);

				$fdata=getRemoteImageData($host,$path,$ext);

				if(strlen($fdata)>0) {
					$filepath=$etcimagepath.$filename;
					$fp2=fopen($filepath,"w");
					fputs($fp2,$fdata);
					fclose($fp2);
					chmod($filepath,0604);

					$size=getimagesize($filepath);

					if($size[0]>0 && $size[1]>0 && (preg_match("/^(1|2|3|6)$/",$size[2]))) {
						$body=str_replace($val,"/".RootPath.DataDir."design/etc/".$filename,$body);
					} else {
						@unlink($filepath);
					}
				}
			}
		}


		$productdisprice = (isset($field[21]) && intval($field[21]))?intval($field[21]):'';

		// 사용자 정의 스팩 처리
		$userspecarr = array();
		for($jj=22;$jj<31;$jj++){
			if(strlen(trim($field[$jj++])) >0 && strlen(trim($field[$jj])) > 0) array_push($userspecarr,$field[$jj-1]."".$field[$jj]);
		}
		$userspec = (count($userspecarr) > 0)?implode("=",$userspecarr):'';

		// 판매가격 대체 문구
		$etctype = '';
		if(strlen(trim($field[32]))>0) $etctype .= "DICKER=".$field[32]."";

		// 제한 설정
		$etcapply_coupon = (isset($field[33]) && $field[33]=='Y')?'Y':'N';
		$etcapply_reserve = (isset($field[34]) && $field[34]=='Y')?'Y':'N';
		$etcapply_gift = (isset($field[35]) && $field[35]=='Y')?'Y':'N';
		$etcapply_return = (isset($field[36]) && $field[36]=='Y')?'Y':'N';


		###################################################################################################
		# 0=>상품명, 1=>시중가격, 2=>판매가, 3=>구매가, 4=>제조사, 5=>원산지, 6=>브랜드, 7=>모델명, 8=>출시일, 9=>진열코드, 10=>적립금(률), 11=>재고
		# 12=>선택사항1, 13=>선택사항1가격, 14=>선택사항2, 15=>진열여부
		# 16=>큰이미지, 17=>보통이미지, 18=>작은이미지, 19=>중소이미지자동생성, 20=>상품상세설명
		# 21=>도매가,22=>사용자스팩명1,23=>사용자스팩1내용,24=>사용자스팩명2,25>사용자스팩2내용,26=>사용자스팩명3,27=>사용자스팩3내용,28=>사용자스팩명4,29=>사용자스팩4내용,30=>사용자스팩명5,31=>사용자스팩5내용,32->판매가격대체문구
		# 33=>할인쿠폰적용불가,34=>적립금적용불가,35=>구매사은품적용불가,36>교확및환불불가,37=>고시1제목,38=>고시2제목
		###################################################################################################

		//$curdate = date("YmdHis");
		$date=$date+1;
		if (strlen($date)==7) $date="0".$date;
		else if (strlen($date)==6) $date="00".$date;

		$curdate=$date1.$date;

		$field[0] = CutStringY($field[0], 0, 100);
		$field[4] = CutStringY($field[4], 0, 20);
		$field[5] = CutStringY($field[5], 0, 30);
		$field[6] = CutStringY($field[6], 0, 50);
		$field[7] = CutStringY($field[7], 0, 50);



		if(strlen($field[6])>0) {
			if($bridx[$field[6]]==0) {
				$sql = "INSERT tblproductbrand SET ";
				$sql.= "brandname = '".$field[6]."' ";
				if($brandinsert = @mysql_query($sql,get_db_conn())) {
					$bridx[$field[6]] = @mysql_insert_id(get_db_conn());
				}
			}
		}

		$sql = "INSERT tblproduct SET ";
		$sql.= "productcode		= '".$code.$productcode."', ";
		$sql.= "productname		= '".str_replace("'","\'",$field[0])."', ";
		$sql.= "sellprice		= ".$field[2].", ";
		$sql.= "consumerprice	= ".$field[1].", ";
		$sql.= "buyprice		= ".$field[3].", ";
		$sql.= "reserve			= '".$field[10]."', ";
		$sql.= "reservetype		= '".$reservetype."', ";
		$sql.= "production		= '".str_replace("'","\'",$field[4])."', ";
		$sql.= "madein			= '".str_replace("'","\'",$field[5])."', ";
		$sql.= "brand			= '".$bridx[$field[6]]."', ";
		$sql.= "model			= '".str_replace("'","\'",$field[7])."', ";
		$sql.= "opendate		= '".$field[8]."', ";
		$sql.= "selfcode		= '".$field[9]."', ";
		$sql.= "quantity		= ".$field[11].", ";
		$sql.= "keyword			= '', ";
		$sql.= "addcode			= '', ";
		$sql.= "maximage		= '".$maximage."', ";
		$sql.= "minimage		= '".$minimage."', ";
		$sql.= "tinyimage		= '".$tinyimage."', ";
		$sql.= "option_price	= '".$field[13]."', ";
		$sql.= "option_quantity	= '', ";
		$sql.= "option1			= '".$field[12]."', ";
		$sql.= "option2			= '".$field[14]."', ";
		//$sql.= "etctype			= '', ";
		$sql.= "etctype			= '".$etctype."', ";
		$sql.= "userspec		= '".$userspec."', ";
		$sql.= "deli_price		= '0', ";
		$sql.= "deli			= 'N', ";
		$sql.= "display			= '".$field[15]."', ";
		$sql.= "date			= '".$curdate."', ";
		if($vender>0) {
			$sql.= "vender		= '".$vender."', ";
		}
		$sql.= "regdate			= now(), ";
		$sql.= "modifydate		= now(), ";
		$sql.= "content			= '".str_replace("'","\'",$body)."' ";
		// 추가 필드 처리 부분
		if(!_empty($productdisprice)) $sql .= ",productdisprice='".$productdisprice."'";
		$sql .= ",etcapply_coupon='".$etcapply_coupon."'";
		$sql .= ",etcapply_reserve='".$etcapply_reserve."'";
		$sql .= ",etcapply_gift='".$etcapply_gift."'";
		$sql .= ",etcapply_return='".$etcapply_return."'";

		@mysql_query($sql,get_db_conn());
		$pridx = mysql_insert_id(get_db_conn());

		$sql = "insert into tblcategorycode set productcode='".$code.$productcode."',categorycode='".$code."'";
		@mysql_query($sql,get_db_conn());

		// 고시항목 추가 부분
		if(_isInt($pridx) && count($field) > 37){
			$getItem = array();
			for($kk=37;$kk < count($field);$kk++){
				$item = array('dtitle'=>$field[$kk++],'dcontent'=>$field[$kk]);
				if(!_empty($item['dtitle'])) array_push($getItem,$item);
			}

			if(_array($getItem)){
				_editProductDetails($pridx,$getItem);
			}
		}
	}
	@fclose($fp);

	if($vender>0 && $i>0) {
		$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(IF(display='Y',1,NULL)) as prdt_cnt FROM tblproduct ";
		$sql.= "WHERE vender='".$vender."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$prdt_allcnt=(int)$row->prdt_allcnt;
		$prdt_cnt=(int)$row->prdt_cnt;
		mysql_free_result($result);

		setVenderCountUpdate($prdt_allcnt, $prdt_cnt, $vender);
		setVenderDesignInsert($vender, $code);
	}

	echo "<html><head></head><body onload=\"alert('상품 등록이 완료되었습니다.');location.href='".$_SERVER["PHP_SELF"]."';\"></body></html>";exit;
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function ACodeSendIt(f,obj) {
	if(obj.ctype=="X") {
		f.code.value = obj.value+"000000000";
	} else {
		f.code.value = obj.value;
	}

	burl = "product_excelupload.ctgr.php?depth=2&code=" + obj.value;
	curl = "product_excelupload.ctgr.php?depth=3";
	durl = "product_excelupload.ctgr.php?depth=4";
	BCodeCtgr.location.href = burl;
	CCodeCtgr.location.href = curl;
	DCodeCtgr.location.href = durl;
}

var isupload=false;
function CheckForm() {
	if(isupload==true) {
		alert("######### 현재 상품정보 등록중입니다. #########");
		return;
	}
	if(document.form1.code.value.length!=12) {
		codelen=document.form1.code.value.length;
		if(codelen==0) {
			alert("상품을 등록할 대분류를 선택하세요.");
			document.form1.code1.focus();
		} else if(codelen==3) {
			alert("상품을 등록할 중분류를 선택하세요.");
			BCodeCtgr.form1.code.focus();
		} else if(codelen==6) {
			alert("상품을 등록할 소분류를 선택하세요.");
			CCodeCtgr.form1.code.focus();
		} else if(codelen==9) {
			alert("상품을 등록할 세부분류를 선택하세요.");
			DCodeCtgr.form1.code.focus();
		} else {
			alert("상품을 등록할 카테고리를 선택하세요.");
			DCodeCtgr.form1.code.focus();
		}
		return;
	}

	isupload=true;
	document.all.uploadButton.style.filter = "Alpha(Opacity=60) Gray";
	document.form1.mode.value="upload";
	document.form1.submit();
}
</script>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
		<col width=198></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top"  background="images/leftmenu_bg.gif">
			<? include ("menu_product.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상품관리 &gt; 상품 일괄관리 &gt; <span class="2depth_select">상품 엑셀 업로드</span></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
        <td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_01_bg.gif"></td>
        <td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr>
        <td width="16" background="images/con_t_04_bg1.gif"></td>
        <td bgcolor="#ffffff" style="padding:10px">





			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/product_excelupload_title.gif" border="0"></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height=21></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN="2" background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">다수 상품정보를 엑셀파일로 만들어 일괄 등록을 하는 기능입니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN="2" background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/product_excelupload_stitle1.gif" border="0"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>

			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
			<input type=hidden name=mode>
			<input type="hidden" name="code" value="">
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" colspan=2></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">엑셀 등록양식 다운로드</TD>
					<TD class="td_con1" ><A HREF="images/sample/product.csv"><img src="images/btn_down1.gif" border=0 align=absmiddle></A> <span class="font_orange">＊엑셀(CSV)파일을 내려받은 후 예제와 같이 작성합니다.</span></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>

				<?if($usevender==true) {?>

				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">등록상품 입점사 선택</TD>
					<TD class="td_con1" >
					<select name=vender>
						<option value="0">쇼핑몰 본사</option>
						<?
						while(list($key,$val)=each($venderlist)) {
							echo "<option value=\"".$val->vender."\">".$val->id." (".$val->com_name.")</option>\n";
						}
						?>
					</select>
					<span class="font_orange">＊상품이 등록될 입점사를 선택하세요.</span>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>

				<?}?>

				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품 카테고리 선택</TD>
					<TD class="td_con1" >
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<col width=145></col>
					<col width=3></col>
					<col width=145></col>
					<col width=3></col>
					<col width=145></col>
					<col width=3></col>
					<col width=></col>
					<tr>
						<td>
						<select name="code1" style=width:145 onchange="ACodeSendIt(document.form1,this.options[this.selectedIndex])">
						<option value="">---- 대 분 류 ----</option>
<?
						$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
						$sql.= "WHERE codeB='000' AND codeC='000' ";
						$sql.= "AND codeD='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
						$result=mysql_query($sql,get_db_conn());
						while($row=mysql_fetch_object($result)) {
							$ctype=substr($row->type,-1);
							if($ctype!="X") $ctype="";
							echo "<option value=\"".$row->codeA."\" ctype='".$ctype."'>".$row->code_name."";
							if($ctype=="X") echo " (단일분류)";
							echo "</option>\n";
						}
						mysql_free_result($result);
?>
						</select>
						</td>
						<td></td>
						<td>
						<iframe name="BCodeCtgr" src="product_excelupload.ctgr.php?depth=2" width="145" height="21" scrolling=no frameborder=no></iframe>
						</td>
						<td></td>
						<td><iframe name="CCodeCtgr" src="product_excelupload.ctgr.php?depth=3" width="145" height="21" scrolling=no frameborder=no></iframe></td>
						<td></td>
						<td><iframe name="DCodeCtgr" src="product_excelupload.ctgr.php?depth=4" width="145" height="21" scrolling=no frameborder=no></iframe></td>
					</tr>
					</table>
					</td>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">엑셀파일(CSV) 등록</TD>
					<TD class="td_con1" ><input type=file name=upfile style="width:54%" class="input"> <span class="font_orange">＊엑셀(CSV) 파일만 등록 가능합니다.</span></TD>
				</TR>
				<TR>
					<TD background="images/table_top_line.gif" colspan=2></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td align="center" height=10></td>
			</tr>
			<tr>
				<td align="center"><img src="images/btn_fileup.gif" id="uploadButton" width="113" height="38" border="0" style="cursor:hand" onclick="CheckForm(document.form1);"></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>

			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
					<col width=20></col>
					<col width=></col>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">상품정보 일괄 등록</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">
						- 상품 등록시 각 상품별 등록처리를 하지 않고, 다수의 상품정보를 엑셀(CSV)파일로 작성하여 일괄 등록하는 기능입니다.
						<br>
						<FONT class=font_orange>- 1회 등록 가능한 상품수는 <B>100개 까지 등록 가능</B>하오니 100개 이상일 경우에 나누어 등록하시기 바랍니다.</font>
						</td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">엑셀(CSV)파일 작성 순서</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">
						- 엑셀파일 작성시 두번 째 라인부터 데이터를 입력하시기 바랍니다. (첫 라인은 필드 설명부분)<br>
						- 아래 형식대로 <FONT class=font_orange><B>엑셀파일 작성 -> 다른이름으로 저장 -> CSV(쉼표로 분리)</B></font> 순으로 저장하시면 됩니다.</td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">상품정보 일괄등록 방법</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- ① 아래의 형식을 참고로 상품정보 엑셀파일을 작성합니다.<br>
						<span class="font_orange" style="padding-left:10px">----------------------------------------------------- 상품정보 엑셀 형식 -----------------------------------------------------</span><br>
						<span class="font_blue" style="padding-left:25px">상품명, 시중가, 판매가, 구매가, 제조사, 원산지, 브랜드, 모델명, 출시일, 진열코드, 적립금(률), 재고,</span>
						<br>
						<span class="font_blue" style="padding-left:25px">선택사항1, 선택사항1가격, 선택사항2, 진열여부, 큰이미지, 보통이미지, 작은이미지, 이미지자동생성, 상품상세설명<span><br>
						<span class="font_orange" style="padding-left:10px">------------------------------------------------------------------------------------------------------------------------------</span><br>

						<div style="padding-left:30">
						<table border=0 cellpadding=0 cellspacing=0 width=600>
						<col width=100></col>
						<col width=></col>
						<tr>
							<td colspan=2 align=center style="padding-bottom:5">
							<B>상품정보 엑셀 작성 예)</B>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">상품명<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							삼성 코어2 듀오 1.66G 14.1와이드 노트북
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">시중가<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							1580000 <img width=20 height=0><FONT class=font_orange>(<B>숫자</B>만 입력하세요.)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">판매가<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							1350000 <img width=20 height=0><FONT class=font_orange>(<B>숫자</B>만 입력하세요.)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">구매가</td>
							<td class=td_con1 style="padding-left:5;">
							1290000 <img width=20 height=0><FONT class=font_orange>(<B>숫자</B>만 입력하세요.)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">제조사</td>
							<td class=td_con1 style="padding-left:5;">
							삼성전자
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">원산지</td>
							<td class=td_con1 style="padding-left:5;">
							한국
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">모델명</td>
							<td class=td_con1 style="padding-left:5;">
							YP-S3A
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">브랜드</td>
							<td class=td_con1 style="padding-left:5;">
							YEPP
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">출시일</td>
							<td class=td_con1 style="padding-left:5;">
							<?=DATE("Ymd")?> <img width=10 height=0><FONT class=font_orange>(출시년월일, <B>숫자</B>만 입력하세요.)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">진열코드</td>
							<td class=td_con1 style="padding-left:5;">
							N123456789
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15" rowspan="3">적립금(률)</td>
							<td class=td_con1 style="padding-left:5;"><b>적립<FONT class=font_orange>금</font></b> : 11000<br>
							<FONT class=font_orange>* 적립금은 0보다 크고 999999 보다 작은 숫자로만 입력해 주세요.<br>
							* 적립금에 맞지 않는 형식일 경우는 숫자 또는 0으로 등록됩니다.</font></td>
						</tr>
						<tr><td height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=td_con1 style="padding-left:5;"><b>적립<FONT color="#0000FF">률</font></b> : 0.01%<br>
							<FONT class=font_orange>* <B>숫자</B>와 특수문자 <B>소수점(.)</B>, <B>페센트(%)</B>만 입력하세요.<br>
							* 적립률 입력시 <B>페센트(%)</B> 필히 입력해 주세요. 미입력시 <B>적립금</B>으로 등록됩니다.<br>
							* 적립률은 0보다 크고 100보다 작은 수로 입력해 주세요.<br>
							* 적립률은 소수점 둘째자리까지만 입력 가능합니다.<br>
							* 적립률에 형식에 일치 하지 않을 경우는 모두 0으로 등록됩니다.<br>
							</font></td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">재고<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							58 <img width=20 height=0><FONT class=font_orange>(<B>공란</B> : 무제한, <B>0</B> : 품절, <B>0이상</B> : 재고수)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">선택사항1</td>
							<td class=td_con1 style="padding-left:5;">
							옵션(RAM)추가 | 512M추가 | 1G추가 <img width=20 height=0><FONT class=font_orange>속성명 | 속성(속성은 "|"로 구분하여 등록)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">선택사항1 가격</td>
							<td class=td_con1 style="padding-left:5;">
							1380000 | 1410000 <img width=20 height=0><FONT class=font_orange>선택사항1 속성에 대한 가격 (판매가는 무시됩니다.)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">선택사항2</td>
							<td class=td_con1 style="padding-left:5;">
							색상 | 빨강 | 파랑 | 노랑 <img width=20 height=0><FONT class=font_orange>속성명 | 속성(속성은 "|"로 구분하여 등록)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">상품진열여부<FONT class=font_orange>(*)</font></td>
							<td class=td_con1 style="padding-left:5;">
							Y <img width=20 height=0><FONT class=font_orange>(<B>Y</B> : 상품진열, <B>N</B> : 진열대기)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">큰이미지</td>
							<td class=td_con1 style="padding-left:5;">
							http://www.abc.com/images/product/1000_1.jpg <FONT class=font_orange><B>(gif/jpg 이미지만 가능)</B></font>
							<br>
							<FONT class=font_orange>(상품 이미지가 존재하는 URL을 정확히 입력하시면 됩니다.)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">보통이미지</td>
							<td class=td_con1 style="padding-left:5;">
							http://www.abc.com/images/product/1000_2.jpg <FONT class=font_orange><B>(gif/jpg 이미지만 가능)</B></font>
							<br>
							<FONT class=font_orange>(<B>이미지자동생성</B>의 경우 입력하지 않아도 됩니다.)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">작은이미지</td>
							<td class=td_con1 style="padding-left:5;">
							http://www.abc.com/images/product/1000_3.jpg <FONT class=font_orange><B>(gif/jpg 이미지만 가능)</B></font>
							<br>
							<FONT class=font_orange>(<B>이미지자동생성</B>의 경우 입력하지 않아도 됩니다.)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">이미지자동생성</td>
							<td class=td_con1 style="padding-left:5;">
							Y <img width=20 height=0><FONT class=font_orange>(<B>Y</B> : 큰이미지로 보통/작은이미지 자동생성, <B>N</B> : 자동생성안함)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">상품상세설명</td>
							<td class=td_con1 style="padding-left:5;">
							상품에 대한 상세설명을 입력하시면 됩니다.
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">도매가</td>
							<td class=td_con1 style="padding-left:5;">
							숫자로 도매가 금액을 입력하시면 됩니다.
							(예 : 10000)</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">할인쿠폰적용불가</td>
							<td class=td_con1 style="padding-left:5;">
							할인쿠폰 적용 불가 상품일 경우 '<strong>Y</strong>' 를 입력하시면 됩니다. ( 허용일 경우 비워두시면 됩니다.)</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">적립금적용불가</td>
							<td class=td_con1 style="padding-left:5;">
							적립금 적용 불가 상품일 경우 '<strong>Y</strong>' 를 입력하시면 됩니다. ( 허용일 경우 비워두시면 됩니다.)</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">구매사은품적용불가</td>
							<td class=td_con1 style="padding-left:5;">
							구매사은품 적용 불가 상품 일 경우 '<strong>Y</strong>' 를 입력하시면 됩니다. ( 허용일 경우 비워두시면 됩니다.)</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">교환 및 환불불가</td>
							<td class=td_con1 style="padding-left:5;">
							교환 및 상품 불가 상품일 경우 '<strong>Y</strong>' 를 입력하시면 됩니다. ( 허용일 경우 비워두시면 됩니다.)</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">상품 고시1 제목</td>
							<td class=td_con1 style="padding-left:5;">상품 고시 정보 의 제목을 입력합니다.</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">상품 고시1 내용</td>
							<td class=td_con1 style="padding-left:5;">상품 고시 정보 의 내용을 입력합니다.</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						tr>
							<td class=table_cell align=right style="padding-right:15">상품 고시n 제목</td>
							<td class=td_con1 style="padding-left:5;" rowspan="3">상품 고시정보의 항목 만큼 재목, 내용 의 순서 으로 추가 하시면 됩니다.</td>
						</tr>
						<tr><td  height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">상품 고시n 내용</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						</table>
						</div>

						<span class="font_orange" style="padding-left:10px">------------------------------------------------------------------------------------------------------------------------------</span>
						</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- ② 상품 등록할 카테고리를 선택 후, 엑셀(CSV)파일을 업로드 합니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- ③ [파일등록] 버튼을 이용하여 업로드 완료 하면 선택된 카테고리에 상품이 등록됩니다.</td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"><IMG SRC="images/manual_right1.gif" WIDTH=18 HEIGHT="2" ALT=""></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</table>
</td>
        <td width="16" background="images/con_t_02_bg.gif"></td>
    </tr>
    <tr>
        <td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_04_bg.gif"></td>
        <td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr><td height="20"></td></tr>
</table>

			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>



<?=$onload?>

<? INCLUDE "copyright.php"; ?>