<?
$imagepath=$Dir.DataDir."shopimages/product/";
$maxfilesize="512000";

$code=$_POST["code"];
$prcode=$_POST["prcode"];
$keyword=$_POST["keyword"];

$productcode=$_POST["productcode"];
$productname=$_POST["productname"];

$start_date1=explode("-",$_POST["start_date1"]);
$end_date1=explode("-",$_POST["end_date1"]);
$start_date=mktime($_POST["start_date2"],$_POST["start_date3"],0,$start_date1[1],$start_date1[2],$start_date1[0]);
$end_date=mktime($_POST["end_date2"],$_POST["end_date3"],59,$end_date1[1],$end_date1[2],$end_date1[0]);

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
$member_check = $_POST["member_check"]; //회원 중복 구매 허용 여부
$sellcount_member = $_POST["sellcount_member"];
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
$bankonly=$_POST["bankonly"];
$deliinfono=$_POST["deliinfono"];
$setquota=$_POST["setquota"];
$miniq=$_POST["miniq"];
$maxq=$_POST["maxq"];
$content=$_POST["content"];

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

$complete_quantity=$_POST["complete_quantity"];
$sellcount_type=$_POST["sellcount_type"];
$sellcount_add=$_POST["sellcount_add_".$sellcount_type];
$stock_state=$_POST["stock_state"];
$discount_state=$_POST["discount_state"];

$sample_url=$_POST["sample_url"];
if(strlen($sample_url)>0 && !eregi("http://",$sample_url) && !eregi("https://",$sample_url)){
	$sample_url = "http://".$sample_url;
}
$download_file=$_POST["download_file"];

$vimage=$_POST["vimage"];
$vimage2=$_POST["vimage2"];
$vimage3=$_POST["vimage3"];

$dicker ="";
$dicker_text ="";
$group_check="N";
$group_code="";
$userspec = "";
$opendate="";

if(strlen($display)==0) $display='Y';

$searchtype=$_POST["searchtype"];
if(strlen($searchtype)==0) $searchtype=0;

$userfile = $_FILES["userfile"];
$userfile2 = $_FILES["userfile2"];
$userfile3 = $_FILES["userfile3"];

$delprdtimg= $_POST["delprdtimg"];
?>
<form name=form_proc action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=mode value="<?=($mode =="delprdtimg" || $mode =="insert"||$mode=="update")? "modify":""?>">
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=prcode value="<?=$prcode?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=keyword value="<?=$keyword?>">
</form>
<?
//이미지 삭제
if($mode=="delprdtimg"){
	$delarray = array (&$vimage,&$vimage2,&$vimage3);
	$delname = array ("maximage","minimage","tinyimage");
	if(strlen($delarray[$delprdtimg])>0 && file_exists($imagepath.$delarray[$delprdtimg])) {
		unlink($imagepath.$delarray[$delprdtimg]);
	}
	$sql = "UPDATE tblproduct SET ".$delname[$delprdtimg]."='' WHERE productcode = '".$prcode."' ";
	echo $sql;
	mysql_query($sql,get_db_conn());
	echo "<script>alert('해당 상품이미지를 삭제하였습니다.');document.form_proc.submit();</script>";
}

if ($mode=="insert" || $mode=="update") {
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

//상품코드생성
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
} elseif ($mode=="update") {
	$image_name = $prcode;
}

if($use_imgurl!="Y") {
	$file_size = $userfile[size]+$userfile2[size]+$userfile3[size];
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

	if($use_imgurl!="Y") {
		$filename = array (&$userfile[name],&$userfile2[name],&$userfile3[name]);
		$file = array (&$userfile[tmp_name],&$userfile2[tmp_name],&$userfile3[tmp_name]);
	}

	$vimagear = array (&$vimage,&$vimage2,&$vimage3);
	$imgnum = array ("","2","3");

	if($mode=="insert" || $mode=="update"){
		for($i=0;$i<3;$i++){
			if ($mode=="update" && strlen($vimagear[$i])>0 && strlen($filename[$i])>0 && file_exists($imagepath.$vimagear[$i])) {
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
			} else {
				$image[$i] = $vimagear[$i];
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
		$sql.= "display			= '".$display."', ";
		$sql.= "date			= '".$curdate."', ";
		$sql.= "regdate			= now(), ";
		$sql.= "modifydate		= now(), ";
		$sql.= "content			= '".$content."', ";
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
		$sql.= "social_chk		= 'Y' ";
		if($insert = mysql_query($sql,get_db_conn())) {
			$sql2 ="INSERT tblproduct_social SET ";
			$sql2.= "pcode		= '".$code.$productcode."', ";
			$sql2.= "sell_startdate		= '".$start_date."', ";
			$sql2.= "sell_enddate		= '".$end_date."', ";
			$sql2.= "complete_quantity	= '".$complete_quantity."', ";
			$sql2.= "sellcount_type		= '".$sellcount_type."', ";
			$sql2.= "sellcount_add		= '".$sellcount_add."', ";
			$sql2.= "stock_state		= '".$stock_state."', ";
			$sql2.= "discount_state		= '".$discount_state."', ";
			$sql2.= "member_check		= '".$member_check."', ";
			$sql2.= "sellcount_member	= '".$sellcount_member."' ";
			
			@mysql_query($sql2,get_db_conn());

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

			$use_imgurl="";
			$userfile_url="";
			$userfile2_url="";
			$userfile3_url="";

			echo "<script>alert(\"상품이 등록이 완료되었습니다.\");document.form_proc.prcode.value='".$code.$productcode."';document.form_proc.submit();</script>";

			$log_content = "## 소셜 상품입력 ## - 코드 $code$productcode - 상품 : $productname 코디/조립 : $assembleuse 패키지그룹 : $package_num 가격 : $sellprice 수량 : $quantity 기타 : $etctype 적립금: $reserve $display";
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
		} else {
			echo "<script>alert(\"상품 등록중 오류가 발생하였습니다.\");histroy.go(-1);</script>";
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

		$sql = "DELETE FROM tblproduct_social WHERE pcode = '".$prcode."'";
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

		$log_content = "## 소셜상품삭제 ## - 상품코드 $prcode - 상품명 : ".urldecode($productname)." $display";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

		delProductMultiImg("prdelete","",$prcode);

		if ($popup=="YES") {
			echo "<script>alert(\"상품 삭제가 완료되었습니다.\");window.close();opener.location.reload()</script>";
		} else {
			echo "<<script>alert(\"상품 삭제가 완료되었습니다.\");document.form_proc.submit();</script>";
		}
		$prcode="";
	} else if ($mode=="update") {
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
		$sql.= "sellprice		= ".$sellprice.", ";
		$sql.= "option_price	= '".$option_price."', ";
		$sql.= "option_quantity	= '".$optcnt."', ";
		$sql.= "option1			= '".$option1."', ";
		$sql.= "option2			= '".$option2."', ";
		$sql.= "package_num		= '".(int)$package_num."', ";
		$sql.= "etctype			= '".$etctype."', ";
		$sql.= "deli_price		= '".$deli_price."', ";
		$sql.= "deli			= '".$deli."', ";
		$sql.= "display			= '".$display."', ";
		if($insertdate!="Y") {
			$sql.= "date			= '".$curdate."', ";
		}
		$sql.= "modifydate		= now(), ";
		$sql.= "content			= '".$content."', ";
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
		$sql.= "social_chk		= 'Y' ";
		$sql.= "WHERE productcode = '".$prcode."' ";
		if($update = mysql_query($sql,get_db_conn())) {
			//상품 복사의 경우 
			$result=mysql_query("SELECT COUNT(*) as cnt FROM tblproduct_social WHERE pcode='".$prcode."' ",get_db_conn());
			$row=mysql_fetch_object($result);
			$dataCheck=$row->cnt;
			mysql_free_result($result);

			if($dataCheck==0) {
				$sql2 ="INSERT tblproduct_social SET ";
				$sql2.= "pcode		= '".$prcode."', ";
			}else{
				$sql2 ="UPDATE tblproduct_social SET ";
			}
			$sql2.= "sell_startdate		= '".$start_date."', ";
			$sql2.= "sell_enddate		= '".$end_date."', ";
			$sql2.= "complete_quantity	= '".$complete_quantity."', ";
			$sql2.= "sellcount_type		= '".$sellcount_type."', ";
			$sql2.= "sellcount_add		= '".$sellcount_add."', ";
			$sql2.= "stock_state		= '".$stock_state."', ";
			$sql2.= "discount_state		= '".$discount_state."', ";
			$sql2.= "member_check		= '".$member_check."', ";
			$sql2.= "sellcount_member	= '".$sellcount_member."' ";
			if($dataCheck > 0) {
				$sql2.= "WHERE pcode		= '".$prcode."' ";
			}
			@mysql_query($sql2,get_db_conn());
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
			$use_imgurl="";
			$userfile_url="";
			$userfile2_url="";
			$userfile3_url="";
		}

		echo "<script>alert(\"상품이 수정되었습니다.\");document.form_proc.submit();</script>";

		$log_content = "## 소셜상품수정 ## - 코드 $prcode - 상품 : $productname 가격 : $sellprice 수량 : $quantity 기타 : $etctype 적립금 : $reserve 날짜고정 : ".(($insertdate=="Y")?"Y":"N")." $display";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	}
} else {
	echo "<script>alert(\"상품이미지의 총 용량이 ".ceil($file_size/1024)
	."Kbyte로 500K가 넘습니다.\\n\\n한번에 올릴 수 있는 최대 용량은 500K입니다.\\n\\n"
	."이미지가 gif가 아니면 이미지 포맷을 바꾸어 올리시면 용량이 줄어듭니다.\");history.go(-1);</script>\n";
}

//################# 500K가 넘는 이미지 체크
if ((strlen($userfile[name])>0 && $userfile[size]==0) || (strlen($userfile2[name])>0 && $userfile2[size]==0) || (strlen($userfile3[name])>0 && $userfile3[size]==0)) {
	 echo "<script>alert(\"상품이미지중 용량이 500K가 넘는 이미지가 있습니다.\\n\\n500K가 넘는 이미지는 등록되지 않습니다..\\n\\n"
		."이미지가 gif가 아니면 이미지 포맷을 바꾸어 올리시면 용량이 줄어듭니다.\");history.go(-1);</script>\n";
}
//###############################################

?>
