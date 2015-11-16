<?

$tblcategorycodeResult = mysql_query("SELECT * FROM `tblcategorycode` WHERE `productcode` = '".$productcode."' AND `categorycode` = '".$_REQUEST["code"]."' ",get_db_conn());
if( mysql_num_rows( $tblcategorycodeResult ) ){
	$rcode=$_REQUEST["code"];
}

//가상카테고리 검증 시작
$virtype = false;
if(strlen($rcode) > 0){
	$vcodeA = substr($rcode,0,3);
	$vcodeB = (substr($rcode,3,3))? substr($rcode,3,3):'000';
	$vcodeC = (substr($rcode,6,3))? substr($rcode,6,3):'000';
	$vcodeD = (substr($rcode,9,3))? substr($rcode,9,3):'000';
}


$virCateSql = "SELECT type FROM tblproductcode WHERE codeA = '".$vcodeA."' AND codeB = '".$vcodeB."' AND codeC = '".$vcodeC."' AND codeD = '".$vcodeD."' ";
$virCateResult = mysql_query($virCateSql,get_db_conn());
$virCateRows = mysql_num_rows($virCateResult);
$virCateRow = mysql_fetch_object($virCateResult);

if($virCateRows>0 && (substr($virCateRow->type,0,1) == 'T')){
	$virtype= true;

}
//가상카테고리 검증 끝

if(strlen($rcode)==0 || $virtype) {
	$rcode=substr($productcode,0,12);
}

$code = '';
$likecode='';
for($i=0;$i<4;$i++){
	$tcode = substr($rcode,$i*3,3);
	if(strlen($tcode) != 3){
		$tcode = '000';
	}else{
		$likecode.=$tcode;
	}
	${'code'.chr(65+$i)} = $tcode;
	$code.=$tcode;
}
//007001001001




$imgsize = 380;

if(!_empty($_pdata->maximage) && file_exists($Dir.DataDir."shopimages/product/".$_pdata->maximage)){
	$imgsrc = $Dir.DataDir."shopimages/product/".$_pdata->maximage;
	$size = getimagesize($imgsrc);
	if($size[1]>$size[0]){
		$imgstr =  ' height="'.(($size[1] > $imgsize)?$imgsize:$size[1]).'" ';
	}else{
		$imgstr =  ' width="'.(($size[0] > $imgsize)?$imgsize:$size[0]).'" ';
	}
}else{
	$imgsrc = $Dir."images/no_img.gif";
}

$icons = array();
$miniq = 1;
if(!_empty($_pdata->etctype)){
	if(preg_match('/ICON=([0-9]+)/',$_pdata->etctype,$itmp)){
		for($i=0;$i<strlen($itmp[1]);$i+=2){
			$num = substr($itmp[1],$i,2);
			$file = $Dir.'images/common/icon'.$num.'.gif';
			if(file_exists($file)) array_push($icons,$file);
		}
	}
	if(preg_match('/MINIQ=([0-9]+)/',$_pdata->etctype,$itmp)){
		$miniq = intval($itmp[1]);
	}

	if(preg_match('/DELIINFONO=([^]+)/',$_pdata->etctype,$itmp)){
		$deliinfono = intval($itmp[1]);
	}
}


if(preg_match("/^\[OPTG([0-9]{4})\]$/",$_pdata->option1,$mat)){
	$optcode = $mat[1];
}
$proption1="";
if(strlen($_pdata->option1)>0) {
	$temp = $_pdata->option1;
	$tok = explode(",",$temp);
	$count=count($tok);
	$proption1.="<table cellpadding=\"0\" cellspacing=\"0\">\n";
	$proption1.="<tr>\n";
	$proption1.="	<td align=\"right\">$tok[0]&nbsp;:&nbsp;</td>\n";
	$proption1.="	<td>";
	if ($priceindex!=0) {
		$proption1.="<select name=\"option1\" size=\"1\" style=\"font-size:11px;background-color:#404040;letter-spacing:-0.5pt;\" ";
		if($_data->proption_size>0) $proption1.="style=\"width : ".$_data->proption_size."px\" ";
		$proption1.="onchange=\"change_price(1,document.form1.option1.selectedIndex-1,";
		if(strlen($_pdata->option2)>0) $proption1.="document.form1.option2.selectedIndex-1";
		else $proption1.="''";
		$proption1.=")\">\n";
	} else {
		$proption1.="<select name=\"option1\" size=\"1\" style=\"font-size:11px;background-color:#404040;letter-spacing:-0.5pt;\" ";
		if($_data->proption_size>0) $proption1.="style=\"width : ".$_data->proption_size."px\" ";
		$proption1.="onchange=\"change_price(0,document.form1.option1.selectedIndex-1,";
		if(strlen($_pdata->option2)>0) $proption1.="document.form1.option2.selectedIndex-1";
		else $proption1.="''";
		$proption1.=")\">\n";
	}

	$optioncnt = explode(",",substr($_pdata->option_quantity,1));
	$proption1.="<option value=\"\" style=\"color:#ffffff;\">옵션을 선택하세요\n";
	$proption1.="<option value=\"\" style=\"color:#ffffff;\">-----------------\n";
	for($i=1;$i<$count;$i++) {
		if(strlen($tok[$i])>0) $proption1.="<option value=\"$i\" style=\"color:#ffffff;\">$tok[$i]\n";
		if(strlen($_pdata->option2)==0 && $optioncnt[$i-1]=="0") $proption1.=" (품절)";
	}
	$proption1.="</select>";
} else {
	//$proption1.="<input type=hidden name=option1>";
}

$proption2="";
if(strlen($_pdata->option2)>0) {
	$temp = $_pdata->option2;
	$tok = explode(",",$temp);
	$count2=count($tok);
	if(strlen($_pdata->option1)<=0) {
		$proption2.="<table cellpadding=\"0\" cellspacing=\"0\">\n";
	}
	$proption2.="<tr>\n";
	$proption2.="	<td align=\"right\">$tok[0]&nbsp;:&nbsp;</td>\n";
	$proption2.="	<td>";
	$proption2.="<select name=\"option2\" size=\"1\" style=\"font-size:11px;background-color:#404040;letter-spacing:-0.5pt;\" ";
	if($_data->proption_size>0) $proption2.="style=\"width : ".$_data->proption_size."px\" ";
	$proption2.="onchange=\"change_price(0,";
	if(strlen($_pdata->option1)>0) $proption2.="document.form1.option1.selectedIndex-1";
	else $proption2.="''";
	$proption2.=",document.form1.option2.selectedIndex-1)\">\n";
	$proption2.="<option value=\"\" style=\"color:#ffffff;\">옵션을 선택하세요\n";
	$proption2.="<option value=\"\" style=\"color:#ffffff;\">-----------------\n";
	for($i=1;$i<$count2;$i++) if(strlen($tok[$i])>0) $proption2.="<option value=\"$i\" style=\"color:#ffffff;\">$tok[$i]\n";
	$proption2.="</select>";
	$proption2.="	</td>\n";
	$proption2.="</tr>\n";
	$proption2.="</table>\n";
} else {
	//$proption2.="<input type=hidden name=option2>";
	if(strlen($_pdata->option1)>0) {
	$proption1.="	</td>\n";
	$proption1.="</tr>\n";
	$proption1.="</table>\n";
	}
}

if(strlen($optcode)>0) {
	$sql = "SELECT * FROM tblproductoption WHERE option_code='".$optcode."' ";
	echo $slq;
	$result = mysql_query($sql,get_db_conn());
	if($row = mysql_fetch_object($result)) {
		$optionadd = array (&$row->option_value01,&$row->option_value02,&$row->option_value03,&$row->option_value04,&$row->option_value05,&$row->option_value06,&$row->option_value07,&$row->option_value08,&$row->option_value09,&$row->option_value10);
		$opti=0;
		$option_choice = $row->option_choice;
		$exoption_choice = explode("",$option_choice);
		$proption3.="<TABLE cellSpacing=\"0\" cellPadding=\"0\" border=\"0\">\n";
		while(strlen($optionadd[$opti])>0) {
			$proption3.="[OPT]";
			$proption3.="<select name=\"mulopt\" style=\"font-size:11px;background-color:#404040;letter-spacing:-0.5pt;\" onchange=\"chopprice('$opti')\"";
			if($_data->proption_size>0) $proption3.=" style=\"width : ".$_data->proption_size."px\"";
			$proption3.=">";
			$opval = str_replace('"','',explode("",$optionadd[$opti]));
			$proption3.="<option value=\"0,0\" style=\"color:#ffffff;\">--- ".$opval[0].($exoption_choice[$opti]==1?"(필수)":"(선택)")." ---";
			$opcnt=count($opval);
			for($j=1;$j<$opcnt;$j++) {
				$exop = str_replace('"','',explode(",",$opval[$j]));
				$proption3.="<option value=\"".$opval[$j]."\" style=\"color:#ffffff;\">";
				if($exop[1]>0) $proption3.=$exop[0]."(+".$exop[1]."원)";
				else if($exop[1]==0) $proption3.=$exop[0];
				else $proption3.=$exop[0]."(".$exop[1]."원)";
			}
			$proption3.="</select><input type=hidden name=\"opttype\" value=\"0\"><input type=hidden name=\"optselect\" value=\"".$exoption_choice[$opti]."\">[OPTEND]";
			$opti++;
		}
		$proption3.="<input type=hidden name=\"mulopt\"><input type=hidden name=\"opttype\"><input type=hidden name=\"optselect\">";
		$proption3.="</TABLE>\n";
	}
	mysql_free_result($result);
}

if(strlen($proption1)>0 || strlen($proption2)>0 || strlen($proption3)>0) {
	$proption ="<tr height=\"22\">";
	$proption.="	<td>상품옵션</td>\n";
	$proption.="	<td>\n";
	//$proption.="	<TABLE cellSpacing=\"0\" cellPadding=\"0\" border=\"0\">\n";
	if(strlen($proption1)>0) {
		$proption.=$proption1;
	}
	if(strlen($proption2)>0) {
		$proption.=$proption2;
	}
	if(strlen($proption3)>0) {
		$pattern=array("[OPT]","[OPTEND]");
		$replace=array("<tr><td>","</td></tr>");
		$proption.=str_replace($pattern,$replace,$proption3);
	}
	//$proption.="	</table>\n";
	$proption.="	</td>\n";
	$proption.="</tr>\n";
	echo $arproduct[$arexcel[$i]];
} else {
	$proption ="<input type=hidden name=\"option1\">\n";
	$proption.="<input type=hidden name=\"option2\">\n";
}


if(!_empty($_pdata->option_price)){
	$pricetok=explode(",",$_pdata->option_price);
	$priceindex = count($pricetok);
}else{
	$priceindex = 0;
}


$discount = round(($_pdata->consumerprice-$_pdata->sellprice)/$_pdata->consumerprice*100); //할인율


#상품후기 카운트
$reviewcountSQL = "SELECT COUNT(productcode) FROM tblproductreview WHERE productcode = '".$_pdata->productcode."' ";
$reviewcount=0;
if(false !== $reviewcountRes = mysql_query($reviewcountSQL,get_db_conn())){
	$reviewcount = mysql_result($reviewcountRes,0,0);
	@mysql_free_result($reviewcountRes);
}
#상품후기 카운트 끝

$rsort = !_empty($_GET['sort'])?trim($_GET['sort']):"";
$rblock = !_empty($_GET['block'])?trim($_GET['block']):"";
$rgotopage = !_empty($_GET['gotopage'])?trim($_GET['gotopage']):"";
$rqnablock = !_empty($_GET['qnablock'])?trim($_GET['qnablock']):"";
$rqnagotopage = !_empty($_GET['qnagotopage'])?trim($_GET['qnagotopage']):"";
$rbrandcode = !_empty($_GET['brandcode'])?trim($_GET['brandcode']):"";
$rselectreview = !_empty($_GET['review'])?trim($_GET['review']):"";

$reviewlink = $_SERVER['PHP_SELF']."?productcode=".$productcode."&sort=".$rsort."&block=".$rblock."&gotopage=".$rgotopage."&qnablock=".$rqnablock."&qnagotopage=".$rqnagotopage."&brandcode=".$rbrandcode;

$reviewcounterSQL = "SELECT ";
$reviewcounterSQL .= "COUNT(num) AS total ";
$reviewcounterSQL .= ",SUM(IF(img IS NULL OR img ='',1,0)) AS basic ";
$reviewcounterSQL .= ",SUM(IF(img IS NOT NULL AND img !='',1,0)) AS photo ";
$reviewcounterSQL .= ",SUM(IF(best = 'Y',1,0)) AS best ";
$reviewcounterSQL .= ",SUM(quality) AS quailty ";
$reviewcounterSQL .= ",SUM(price) AS price ";
$reviewcounterSQL .= ",SUM(delitime) AS delitime ";
$reviewcounterSQL .= ",SUM(recommend) AS recommend ";
$reviewcounterSQL .= "FROM tblproductreview ";
$reviewcounterSQL .= "WHERE productcode = '".$productcode."' ";

if(false !== $reviewcountRes = mysql_query($reviewcounterSQL,get_db_conn())){
	$reviewcountRow = mysql_fetch_assoc($reviewcountRes);
	mysql_free_result($reviewcountRes);
}

$averqulity=$averprice=$averdelitime=$averrecommend=$avertotalscore=$startotalcount ="0";

$counttotal = ($reviewcountRow['total'])?trim($reviewcountRow['total']):"0";
$countbasic = ($reviewcountRow['basic'])?trim($reviewcountRow['basic']):"0";
$countphoto = ($reviewcountRow['photo'])?trim($reviewcountRow['photo']):"0";
$countbest = ($reviewcountRow['best'])?trim($reviewcountRow['best']):"0";
$sumquailty = ($reviewcountRow['quailty'])?trim($reviewcountRow['quailty']):"0";
$sumprice = ($reviewcountRow['price'])?trim($reviewcountRow['price']):"0";
$sumdelitime = ($reviewcountRow['delitime'])?trim($reviewcountRow['delitime']):"0";
$sumrecommend = ($reviewcountRow['recommend'])?trim($reviewcountRow['recommend']):"0";

$averquality = floor(($sumquailty * 20)/$counttotal);
$averprice = floor(($sumprice * 20)/$counttotal);
$averdelitime = floor(($sumdelitime * 20)/$counttotal);
$averrecommend = floor(($sumrecommend * 20)/$counttotal);

$countaverqulity = floor($sumquailty/$counttotal);
$countaverprice = floor($sumprice/$counttotal);
$countaverdelitime = floor($sumdelitime/$counttotal);
$countaverrecommend = floor($sumrecommend/$counttotal);

$avertotalscore = floor(($averquality+$averprice+$averdelitime+$averrecommend) /4);
$startotalcount = floor($avertotalscore / 20);

#사용후기 토탈 별점수
$reviewstarcount="";
for($i=1;$i<=5;$i++){
	if($i <= $startotalcount){
		$reviewstarcount.='<img src="/images/003/star_point1.gif" alt="" />';
	}else{
		$reviewstarcount.='<img src="/images/003/star_point2.gif" alt="" />';
	}
}

#사용후기 품질 별점수
$qualitystarcount="";
for($i=1;$i<=5;$i++){

	if($i <= $countaverqulity){
		$qualitystarcount.='<img src="/images/003/star_point1.gif" alt="" />';
	}else{
		$qualitystarcount.='<img src="/images/003/star_point2.gif" alt="" />';
	}
}

#사용후기 가격 별점수
$pricestarcount="";
for($i=1;$i<=5;$i++){
	if($i <= $countaverprice){
		$pricestarcount.='<img src="/images/003/star_point1.gif" alt="" />';
	}else{
		$pricestarcount.='<img src="/images/003/star_point2.gif" alt="" />';
	}
}

#사용후기 배송 별점수
$delitimestarcount="";
for($i=1;$i<=5;$i++){
	if($i <= $countaverdelitime){
		$delitimestarcount.='<img src="/images/003/star_point1.gif" alt="" />';
	}else{
		$delitimestarcount.='<img src="/images/003/star_point2.gif" alt="" />';
	}
}

#사용후기 추천 별점수
$recommendstarcount="";
for($i=1;$i<=5;$i++){
	if($i <= $countaverrecommend){
		$recommendstarcount.='<img src="/images/003/star_point1.gif" alt="" />';
	}else{
		$recommendstarcount.='<img src="/images/003/star_point2.gif" alt="" />';
	}
}

#상품평 타입 선택 탭
$sphotoreview=$sbestreview=$sbasicreview=$sallreview=" tabOff";
switch($rselectreview){
	case "photo":
		$sphotoreview = " tabOn";
	break;
	case "best":
		$sbestreview = " tabOn";
	break;
	case "basic":
		$sbasicreview = " tabOn";
	break;
	case "all":
	default:
		$sallreview = " tabOn";
	break;
}



//리뷰관련 환경 설정
$reviewlist=$_data->ETCTYPE["REVIEWLIST"];
$reviewdate=$_data->ETCTYPE["REVIEWDATE"];
if(strlen($reviewlist)==0) $reviewlist="N";

if($mode=="review_write") {
	function ReviewFilter($filter,$memo,&$findFilter) {
		$use_filter = split(",",$filter);
		$isFilter = false;
		for($i=0;$i<count($use_filter);$i++) {
			if (eregi($use_filter[$i],$memo)) {
				$findFilter = $use_filter[$i];
				$isFilter = true;
				break;
			}
		}
		return $isFilter;
	}

	$rname=$_POST["rname"];
	$rcontent=$_POST["rcontent"];
	$rmarks=$_POST["rmarks"];
	if((strlen($_ShopInfo->getMemid())==0) && $_data->review_memtype=="Y") {
		echo "<html></head><body onload=\"alert('로그인을 하셔야 사용후기 등록이 가능합니다.');location.href='".$Dir.FrontDir."login.php?chUrl=".getUrl()."'\"></body></html>";exit;
	}
	if(strlen($review_filter)>0) {	//사용후기 내용 필터링
		if(ReviewFilter($review_filter,$rcontent,$findFilter)) {
			echo "<html></head><body onload=\"alert('사용하실 수 없는 단어를 입력하셨습니다.(".$findFilter.")\\n\\n다시 입력하시기 바랍니다.');history.go(-1);\"></body></html>";exit;
		}
	}
	/** 첨부 이미지 추가 */
	$up_imd = '';

	if(is_array($_FILES['img']) && is_uploaded_file($_FILES['img']['tmp_name'])){
		if($_FILES['img']['error'] > 0){
			echo "<html></head><body onload=\"alert('파일 업로드중 오류가 발생했습니다.');history.go(-1);\"></body></html>";
			exit;
		}

		$save_dir=$Dir.DataDir."shopimages/productreview/";
		$numresult = mysql_query("select ifnull(max(num),1) as num from tblproductreview",get_db_conn());

		if($numresult){
			$file_name =  $productcode.(intval(mysql_result($numresult,0,0))+1);
		}

		$size=getimageSize($_FILES['img']['tmp_name']);
		$width=$size[0];
		$height=$size[1];
		$imgtype=$size[2];
		$_w = 650;
		$ratio = ($_w > 0 && $width > $_w)?(real)($_w / $width):1;

		if($imgtype==1)      $file_ext ='gif';
		else if($imgtype==2) $file_ext ='jpg';
		else if($imgtype==3) $file_ext ='png';
		else{
			 echo "<html></head><body onload=\"alert('올바른 형태의 이미지 파일이 아닙니다.');history.go(-1);\"></body></html>";
			 exit;
		}

		$index = 0;
		$up_name = $file_name.".".$file_ext;
		while(file_exists($save_dir."/".$up_name)){
			$up_name = $file_name."_".$index.".".$file_ext;
			$index++;
		}

		if(!move_uploaded_file($_FILES['img']['tmp_name'],$save_dir."/".$up_name)){
			echo "<html></head><body onload=\"alert('파일 저장 실패.');history.go(-1);\"></body></html>";
			exit;
		}
		if($ratio < 1){
			$source = $target = $save_dir."/".$up_name;
			$new_width = (int)($ratio*$width);
			$new_height = (int)($ratio*$height);

			$dest_img = imagecreatetruecolor($new_width,$new_height);
			$white = imagecolorallocate($dest_img,255,255,255);
			imagefill($dest_img,0,0,$white);

			if($file_ext == 'gif'){ //이미지 타입에 따라서 이미지 로드
				$src_img = imagecreatefromgif($source);
				imagecopyresampled($dest_img,$src_img,0,0,0,0,$new_width,$new_height,$width,$height);
				imagedestroy($src_img);
				imagegif($dest_img,$target);
			}else if($file_ext == 'jpg'){
				$src_img = @imagecreatefromjpeg($source);
				imagecopyresampled($dest_img,$src_img,0,0,0,0,$new_width,$new_height,$width,$height);
				imagedestroy($src_img);
				imagejpeg($dest_img,$target,75);
			}else if($file_ext == 'png'){
				$src_img = imagecreatefrompng($source);
				imagecopyresampled($dest_img,$src_img,0,0,0,0,$new_width,$new_height,$width,$height);
				imagedestroy($src_img);
				imagepng($dest_img,$target);
			}
			imagedestroy($dest_img);
		}
	}


	$sql = "INSERT tblproductreview SET ";
	$sql.= "productcode	= '".$productcode."', ";
	$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
	$sql.= "name		= '".$rname."', ";
	$sql.= "marks		= '".$rmarks."', ";
	$sql.= "date		= '".date("YmdHis")."', ";
	$sql.= "content		= '".$rcontent."', ";
	$sql.= "img		= '".$up_name."' ";
	mysql_query($sql,get_db_conn());

	if($_data->review_type=="A") $msg="관리자 인증후 등록됩니다.";
	else $msg="등록되었습니다.";
	$rqry="productcode=".$productcode;
	if(strlen($code)>0) $rqry.="&code=".$code;
	if(strlen($sort)>0) $rqry.="&sort=".$sort;
	if(strlen($brandcode)>0) $rqry.="&brandcode=".$brandcode;
	echo "<html></head><body onload=\"alert('".$msg."');location='".$_SERVER["PHP_SELF"]."?".$rqry."'\"></body></html>";exit;
}
?>

<script language="javascript" type="text/javascript">
<!--
function solvCountdown(timestamp){
	timestamp = parseInt(timestamp);
	var d = new Object;
	if(!isNaN(timestamp) && timestamp ){
		d.day = Math.floor(timestamp / (3600 * 24));
		mod = timestamp % (24 * 3600);
		d.hour = Math.floor(mod / 3600);
		mod = mod % 3600;
		d.min = Math.floor(mod / 60);
		d.sec = mod % 60;
		/*
		if (leftTime == 0){
			//document.getElementById("buyImg_"+k).src =""; //구매종료이미지
		}
		leftTime = leftTime-1;*/
		return d;
	}else{
		return false;
	}
}

function refCountdown(el){
	if($j(el) && $j(el).attr('endstamp')){
		var end = parseInt($j(el).attr('endstamp'));
		var curr = Math.round(new Date().getTime() / 1000);
		if(isNaN(end) || end < curr) remain = 0;
		else remain = end - curr;
		if(remain < 1){
			$j(el).find('.remainDay').html('0');
			$j(el).find('.remainHour1').html('0');
			$j(el).find('.remainHour2').html('0');
			$j(el).find('.remainMin1').html('0');
			$j(el).find('.remainMin2').html('0');
			$j(el).find('.remainSec1').html('0');
			$j(el).find('.remainSec2').html('0');
		}else{
			d = solvCountdown(remain);
			$j(el).find('.remainDay').html(d.day+'일');
			$j(el).find('.remainHour1').html(Math.floor(d.hour/10));
			$j(el).find('.remainHour2').html(Math.floor(d.hour %10));
			$j(el).find('.remainMin1').html(Math.floor(d.min/10));
			$j(el).find('.remainMin2').html(Math.floor(d.min%10));
			$j(el).find('.remainSec1').html(Math.floor(d.sec/10));
			$j(el).find('.remainSec2').html(Math.floor(d.sec%10));
		}
	}
}

function intCountdown(){
	$j('td.remainTimeBox').each(function(idx,el){
		refCountdown(el);
	});
	setTimeout("intCountdown()", 1000);
}

$j(function(){
	intCountdown();
});
//-->
</script>

<style>
	#topContent {width:100%; height:100%; overflow:hidden; border:1px solid #efefef;}

	.saleInfoTbl {width:100%; text-align:left;}
	.saleInfoTbl th {width:20%; height:24px; font-size:11px; font-weight:normal; letter-spacing:-0.5px;}
	.saleInfoTbl .todaySalePrice {line-height:22px; color:#333333; font-size:24px; font-family:arial; font-weight:bold;}

	.prSellprice {color:#333333;}


	.reviewBox {width:200px;}
	.markPoint {padding:10px 15px;}
	.markPoint h4 {padding:5px 0px; font-size:16px;}
	.reviewPoint {height:50px; line-height:50px; font-size:24px; font-family:verdana; font-weight:bold; color:#ffffff; letter-spacing:-1px; text-align:center; background:#cccccc;}
	.reviewPointNone {height:104px; line-height:104px; font-size:24px; font-family:verdana; font-weight:bold; color:#fff; text-align:center; background:url('/data/design/img/detail/bg_markpoint1.gif') repeat-x;}
	.reviewPoint2 {font-size:24px; font-family:verdana; font-weight:bold; color:#ffffff; margin-top:35px; margin-left:35px; float:left;width:46px; height:30px; padding-top:5px; text-align:center;}
	.reviewStar {float:left; width:80px; height:20px; margin-top:2px; font-size:0px;}
	.reviewStar2 {float:left; margin:0px; margin-top:34px; margin-left:22px; padding:0px; font-size:0px; text-align:left;}
	.reviewStar2 p {font-size:10px;}
	.reviewCount {float:right; font-size:11px; text-align:right;}
	.reviewList {clear:both; border-top:1px solid #e6e6e6; padding-top:8px;}
	.reviewList h4 {padding:5px 0px; font-size:16px;}
	.reviewAddPoint {text-align:left; margin:0px 15px; padding:12px 0px; font-size:11px; color:#919191; border-top:1px solid #e0e0e0; border-bottom:1px solid #e0e0e0;}
	.reviewAddPoint p {padding-bottom:10px; font-weight:bold;}
	.reviewAddPoint .redfonts {color:#ff3300;}

	.titAppraisal {clear:both; text-align:center;}
	.titAppraisal h4 {color:#e30707; line-height:24px;}
	.reviewPointBox {margin:20px 0px; padding:0px; width:100%; height:94px; border:1px solid #dddddd; background:#ffffff;}
	.reviewPointBox ul {list-style:none; margin:0px; padding:0px;}
	.reviewPointBox li {float:left; width:18%; padding:18px 0px; color:#333; font-weight:bold; text-align:center;}
	.reviewPointBox .reviewPartPoint {line-height:24px; border-right:1px solid #ededed;}
	.reviewPointBox .reviewPartPoint2 {line-height:24px;}
	.reviewPointNum {color:#d02127; font-size:17px; letter-spacing:-0.5px;}
	.customerReview {clear:both; padding-top:20px;}
	.delinfoBox {margin-top:20px;}
</style>

<!--
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td height="40">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td style="padding-right:5px;"><?=$codenavi?></td>
					<td align="right" style="padding-right:3px; background-repeat:no-repeat; background-position:right;"><A HREF="javascript:ClipCopy('http://<?=$_ShopInfo->getShopurl()?>?<?=getenv("QUERY_STRING")?>')"><img src="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/btn_addr_copy.gif" border="0"></A></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
-->

<div id="pInfowrapper" style="margin-top:20px;">
<? if($_pdata->remain > 1){  // 마감 된 상품이 아닐 경우만?>
	<form name=form1 method=post action="<?=$Dir.FrontDir?>basket.php">
	<input type=hidden name=price value="<?=number_format($_pdata->sellprice)?>">
	<input type=hidden name=code value="<?=$code?>">
	<input type=hidden name=productcode value="<?=$productcode?>">
	<input type=hidden name=ordertype>
	<input type=hidden name=opts>
	<input type=hidden name=sell_memid value="<?=$sell_memid?>">
	<?=($brandcode>0?"<input type=hidden name=brandcode value=\"".$brandcode."\">\n":"")?>
<? } ?>

	<table border="0" cellpadding="0" cellspacing="0" id="topContent">
		<tr>
			<td width="45%" height="100%" style="border-right:1px solid #efefef;">

			<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="border:0px solid #dddddd; background:#ffffff;">
				<caption style="display:none;">상품이미지</caption>
				<tr>
					<td align="center" height="100%">
						<div style="padding:10px 0px; font-size:0px;"><img src="<?=$imgsrc?>" <?=$imgstr?> alt="" /></div>
						<div>
							<!--SNS 버튼 출력-->
<?
//SNS BUTTON
$snsButton="";
if($_data->sns_ok == "Y" && $_pdata->sns_state == "Y") {
	$snsButton ="";
	if (strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted") {
		if(TWITTER_ID !="TWITTER_ID"){
			$snsButton .= "<td><INPUT type=\"checkbox\" name=\"send_chk\" id=\"send_chk_t\" value=\"t\" disabled><IMG SRC=\"../images/design/icon_twitter_off.gif\" width=\"17\"  border=\"0\" id=\"tLoginBtn0\" style=\"cursor:pointer\"><input type=\"hidden\" name=\"tLoginBtnChk\" id=\"tLoginBtnChk\"></td>\n";
		}
		if(FACEBOOK_ID !="FACEBOOK_ID"){
			$snsButton .= "<td><INPUT type=\"checkbox\" name=\"send_chk\" id=\"send_chk_f\" value=\"f\" disabled><IMG SRC=\"../images/design/icon_facebook_off.gif\" width=\"17\"  border=\"0\" id=\"fLoginBtn0\" style=\"cursor:pointer\"><input type=\"hidden\" name=\"fLoginBtnChk\" id=\"fLoginBtnChk\"></td>\n";
		}
		/*
		if(ME2DAY_ID !="ME2DAY_ID"){
			$snsButton .= "<td><INPUT type=\"checkbox\" name=\"send_chk\" id=\"send_chk_m\" value=\"m\" disabled><IMG SRC=\"../images/design/icon_me2day_off.gif\" width=\"17\"  border=\"0\" id=\"mLoginBtn0\" style=\"cursor:pointer\"><input type=\"hidden\" name=\"mLoginBtnChk\" id=\"mLoginBtnChk\"></td>\n";
		}
		*/
		if(strlen($snsButton)>0){
			$snsButton.="
<td><a href=\"#snsSepup\" onclick=\"showDiv('snsSepup');\"><img src=\"../images/design/icon_setup.gif\" alt=\"sns자동연결설정\" border=\"0\" align=\"absmiddle\"></a>
	<div id=\"snsSepup\" style=\"position:absolute;z-index:1000;background:#fff;left:35px;top:20px;visibility:hidden;\">
	<table cellpadding=\"0\" cellspacing=\"0\" width=\"150\">
	<tr>
		<td colspan=\"3\"><IMG src=\"../images/design/speech_bubble_top.gif\" width=\"150\" height=\"7\"></td>
	</tr>
	<tr>
		<td width=\"5\" background=\"../images/design/speech_bubble_leftbg.gif\"></td>
		<td width=\"140\" class=\"table01_con\">
			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
			<tr>
				<td class=\"speechbubble_title\"><b>sns자동연결 설정</b></td>
				<td align=\"right\" class=\"speechbubble_close\"><a href=\"#snsSepup\" onclick=\"showDiv('snsSepup');\"><img src=\"../images/design/speech_bubble_close.gif\"></a></td>
			</tr>
			<tr>
				<td colspan=\"2\" height=\"10\"><img src=\"../images/design/con_line02.gif\" width=\"140\" height=\"1\"></td>
			</tr>
			<tr>
				<td colspan=\"2\" class=\"speechbubble_con\">
					<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">".((FACEBOOK_ID !="FACEBOOK_ID")? "
						<tr>
							<td height=23><img src=\"../images/design/icon_sb_facebook_off.gif\" id=\"fLoginBtn1\"></td>
							<td><a href=\"javascript:changeSnsInfo('f');\"><img src=\"../images/design/btn_connection_off.gif\" alt=\"\" id=\"fLoginBtn2\"></td>
						</tr>":"").((TWITTER_ID !="TWITTER_ID")? "
						<tr>
							<td height=23><img src=\"../images/design/icon_sb_twitter_off.gif\"  id=\"tLoginBtn1\"></td>
							<td><a href=\"javascript:changeSnsInfo('t');\"><img src=\"../images/design/btn_connection_off.gif\" alt=\"\" id=\"tLoginBtn2\"></td>
						</tr>":"")."
					</table>
				</td>
			</tr>
			<tr>
				<td colspan=\"2\" height=\"10\"><img src=\"../images/design/con_line02.gif\" width=\"140\" height=\"1\"></td>
			</tr>
			<tr>
				<td colspan=\"2\" class=\"speechbubble_con\">버튼을 클릭하면 연결해제를 할수 있습니다.</td>
			</tr>
			</table>
		</td>
		<td width=\"5\" background=\"../images/design/speech_bubble_rightbg.gif\"></td>
	</tr>
	<tr>
		<td colspan=\"3\"><IMG src=\"../images/design/speech_bubble_bottom.gif\" width=\"150\" height=\"7\"></td>
	</tr>
	</table>
	</div>
</td>
<td><a href=\"#snsHelp\" onclick=\"showDiv('snsHelp');\"><img src=\"../images/design/icon_help.gif\" hspace=\"2\" alt=\"도움말\" border=\"0\" align=\"absmiddle\"></a>
	<div id=\"snsHelp\" style=\"position:absolute;z-index:1000;background:#fff;left:55px;top:20px;visibility:hidden;\">
	<table cellpadding=\"0\" cellspacing=\"0\" width=\"150\">
		<tr>
			<td colspan=\"3\"><IMG src=\"../images/design/speech_bubble_top.gif\" width=\"150\" height=\"7\"></td>
		</tr>
		<tr>
			<td width=\"5\" background=\"../images/design/speech_bubble_leftbg.gif\"></td>
			<td width=\"140\">
				<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
					<tr>
						<td class=\"speechbubble_title\"><b>도움말</b></td>
						<td align=\"right\" class=\"speechbubble_close\"><a href=\"#snsHelp\" onclick=\"showDiv('snsHelp');\"><img src=\"../images/design/speech_bubble_close.gif\"></a></td>
					</tr>
					<tr>
						<td colspan=\"2\" class=\"speechbubble_con\">해당 컨텐츠를 내 SNS로 보내 친구들과 공유해보세요.<br><font color=\"#F8752F\">SNS 자동연결 설정 시 한번에 여러개의 SNS로 글을 올릴수 있습니다.</font></td>
					</tr>
				</table>
			</td>
			<td width=\"5\" background=\"../images/design/speech_bubble_rightbg.gif\"></td>
		</tr>
		<tr>
			<td colspan=\"3\"><IMG src=\"../images/design/speech_bubble_bottom.gif\" width=\"150\" height=\"7\"></td>
		</tr>
	</table>
	</div>
</td>
<td><a href=\"#snsSend\" onclick=\"showDiv('snsSend');\"><img src=\"../images/design/icon_snssend.gif\" border=\"0\" align=\"absmiddle\"></a>
	<div id=\"snsSend\" style=\"position:absolute;z-index:1000;background:#fff;left:0;top:-130px;visibility:hidden;\">
	<table cellpadding=\"0\" cellspacing=\"0\" width=\"350\">
		<tr>
			<td colspan=\"3\"><IMG src=\"../images/design/speech_bubble_topa.gif\" width=\"352\" height=\"7\"></td>
		</tr>
		<tr>
			<td width=\"5\" background=\"../images/design/speech_bubble_leftbg.gif\"></td>
			<td width=\"342\" class=\"table01_con\">


				<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
					<tr>
						<td class=\"speechbubble_count\"><b><font color=\"#F8752F\" id=\"cmtByte\">0</font></b>/80자</td>
						<td align=\"right\" class=\"speechbubble_close\"><a href=\"#snsSend\" onclick=\"showDiv('snsSend');\"><img src=\"../images/design/speech_bubble_close.gif\"></a></td>
					</tr>
					<tr>
						<td class=\"speechbubble_con\" colspan=\"2\"><TEXTAREA rows=\"3\" cols=\"50\" name=\"comment0\" id=\"comment0\" class=\"textarea1\" onChange=\"CheckStrLen('80',this,'top');\" onKeyUp=\"CheckStrLen('80',this,'top');\" onfocus=\"if(this.value == '내용을 입력하세요.' ){this.value='';}\" onblur=\"if(this.value.length==0){this.value='내용을 입력하세요.'};\">내용을 입력하세요.</TEXTAREA></td>
					</tr>
					<tr>
						<td  align=\"center\" colspan=\"2\" style=\"padding-bottom:10px\"><a href=\"#snsSend\" onclick=\"snsReg_top();\"><img src=\"../images/design/icon_snssend.gif\"></a><a href=\"#snsSend\" onclick=\"showDiv('snsSend');\"><img src=\"../images/design/btn_cancel01.gif\" hspace=\"4\"></a></td>
					</tr>
				</table>

			</td>
			<td width=\"5\" background=\"../images/design/speech_bubble_rightbg.gif\"></td>
		</tr>
		<tr>
			<td colspan=\"3\"><IMG src=\"../images/design/speech_bubble_bottoma.gif\" width=\"352\" height=\"7\"></td>
		</tr>
	</table>
	</div>
</td>";

		}
	}else{
		$snsButton .= "<td><a href=\"javascript:snsSendCheck('t');\"><IMG SRC=\"../images/design/icon_twitter_on.gif\" width=\"17\" border=\"0\" id=\"tLoginBtn0\"></a>&nbsp;</td>\n";
		$snsButton .= "<td><a href=\"javascript:snsSendCheck('f');\"><IMG SRC=\"../images/design/icon_facebook_on.gif\" width=\"17\"  border=\"0\" id=\"fLoginBtn0\"></a>&nbsp;</td>\n";
		//$snsButton .= "<td><a href=\"javascript:snsSendCheck('m');\"><IMG SRC=\"../images/design/icon_me2day_on.gif\" width=\"17\"  border=\"0\" id=\"mLoginBtn0\"></a>&nbsp;</td>\n";
	}
	$snsButton .= "<td>&nbsp;</td>\n";
}
$sql="SELECT count(1) cnt FROM tblsmsinfo WHERE product_hongbo ='Y' limit 1 ";
$smsRs=mysql_query($sql,get_db_conn());
$rowsms=mysql_fetch_object($smsRs);
$smsChk = $rowsms->cnt;
mysql_free_result($smsRs);
$snsButton .= "<td align=\"right\" width='100%'>";
if (strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted") {
	$snsButton .= "<a href=\"../front/mail_send.php?pcode=".$productcode."\" onclick=\"window.open(this.href,'mailSend','width=420px,height=315px');return false;\">";
}else{
	$snsButton .= "<a href=\"javascript:check_login();\" >";
}
$snsButton .= "<IMG SRC=\"../images/design/icon_email.gif\" WIDTH=18 HEIGHT=18 ALT=\"\" border=\"0\"></a>";
if($smsChk >0){
	if (strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted") {
		$snsButton .= "<a href=\"../front/smssendFrm.php?pcode=".$productcode."\" onclick=\"window.open(this.href,'smsSendWin','width=420px,height=335px');return false;\">";
	}else{
		$snsButton .= "<a href=\"javascript:check_login();\" >";
	}
	$snsButton .= "<IMG SRC=\"../images/design/icon_phone.gif\" WIDTH=18 HEIGHT=18 ALT=\"\" border=\"0\" hspace=\"2\"></a>";
}
$snsButton .= "</td>";
$snsButton = "<table cellpadding=\"0\" cellspacing=\"0\" style=\"position:relative;widht:100%\">\n<tr>\n".$snsButton."</tr>\n</table>\n";

$pesterbtn =($_data->pester_state =="Y" && $_pdata->pester_state == "Y")? $pesterbtn:"";
$presentbtn =($_pdata->present_state == "Y")? $presentbtn:"";

$snscomment="";
if($_data->sns_ok == "Y" && $_pdata->sns_state == "Y"){
	INCLUDE ($Dir.TempletDir."product/sns_product_cmt.php");
	$snscomment = $sProductCmt;
}
$gonggucomment ="";
if($_data->sns_ok == "Y" && $_pdata->gonggu_product == "Y"){
	INCLUDE ($Dir.TempletDir."product/sns_gonggu_cmt.php");
	$gonggucomment = $sGongguCmt;
}


$discount = round(($_pdata->consumerprice-$_pdata->sellprice)/$_pdata->consumerprice*100);
//echo $snsButton;
?>
						</div>
					</td>
				</tr>
				<tr>
					<td style="padding:8px 10px; border-top:1px solid #efefef;"><?=$snsButton?></td>
				</tr>
			</table>

		</td>
		<td style="padding:15px 20px;">
			<!--
			<div>
				<!-- icon 영역 --//>
				<?// if($_pdata->remain < 1){ ?>
					<img src="/images/common/todaysale/sale_icon03.gif" alt="마감" />
				<?// }else{ ?>
				<?// if($_pdata->deli_price == 0 && $_pdata->deli == 'N'){	?>
					<img src="/images/common/todaysale/sale_icon01.gif" alt="무료배송" />
				<?// } ?>
				<?// if($_pdata->remain < 5*24*3600){	?>
					<img src="/images/common/todaysale/sale_icon02.gif" alt="마감임박" />
				<?// } ?>
				<?//  for($i=0;$i<count($icons);$i++){ ?><img src="<?//=$icons[$i]?>" border="0" style="margin-right:2px;" /><?// } ?>
				<?// } ?>
			</div>
			-->

			<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-bottom:2px solid #333333">
				<tr>
					<td>
						<div style="float:right; width:70px; height:70px; border:1px solid #eee; padding-top:5px; background:#f2f2f2; text-align:center;"><img src="/pqrcode.php?productcode=<?=$productcode?>" width="60" /></div>
					</td>
				</tr>
				<tr>
					<td style="padding:6px 0px; line-height:22px;"><span style="color:#333; font-size:15px; font-weight:bold; letter-spacing:-1px;word-break:break-all;"><?=$_pdata->productname?></span></td></td>
				</tr>
				<tr><td><?=$_pdata->addcode?></td></tr>
			</table>

			<div>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="saleInfoTbl">
					<tr><td height="10" colspan="2"></td></tr>
					<!--<tr>
						<th>상품번호</th>
						<td>[MODEL]</td>
					</tr>-->
					<tr>
						<th><span class="prSellprice">오늘만 특가</span></th>
						<td><span class="todaySalePrice"><?=number_format($_pdata->sellprice)?>원</span>(<?=$discount?>% 할인)</td>
					</tr>
					<tr>
						<th>기존 판매가</th>
						<td><span class="consumprice"><?=number_format($_pdata->consumerprice)?>원</span></td>
					</tr>
					<tr><td colspan="2" height="6"></td></tr>
					<tr>
						<td colspan="2">

							<table border="0" cellpadding="0" cellspacing="0" style="width:100%; border-top:1px solid #e9e9e9; border-bottom:1px solid #e9e9e9;">
								<tr>
									<td width="73" class="remainTimeBox" remain="<?=$_pdata->remain?>" endstamp='<?=strtotime($_pdata->end)?>'>

										<? if($_pdata->remain < 1){ ?>
											<div style="width:73px; height:73px; background:#2db400 url('/images/common/product/AD001/daybox_bg3.gif');">
												<div style="width:100%; padding-top:50px; color:#ffffff; font-weight:bold; text-align:center;">마감</div>
											</div>
										<? }else{ ?>

											<? if(($item['deli_price'] == 0 && $item['deli'] == 'F') || ($item['deli_price'] == 0 && $item['deli'] == 'N' && $item['sellprice'] >= $_data->deli_miniprice)){ ?>
												<!--[무료배송]-->
												<div style="float:left; width:73px; height:73px; background:#2db400 url('/images/common/product/AD001/daybox_bg1.gif');">
													<div style="padding-top:50px; color:#ffffff; font-weight:bold; text-align:center;">무료배송</div>
												</div>
											<? } ?>

											<? if($_pdata->remain < 7*24*3600){ ?>
												<!--[마감임박]-->
												<div style="width:73px; height:73px; background:#2db400 url('/images/common/product/AD001/daybox_bg2.gif');">
													<div style="width:100%; padding-top:50px; color:#ffffff; font-weight:bold; text-align:center;">임박 D-<span style="font-weight:bold;" class="remainDay">0</span></div>
												</div>
											<? }else{ ?>
												<div style="width:73px; height:73px; background:#2db400 url('/images/common/product/AD001/daybox_bg1.gif');">
													<div style="width:100%; padding-top:50px; color:#ffffff; font-weight:bold; text-align:center;">D-<span style="font-weight:bold;" class="remainDay">0</span></div>
												</div>
											<? } ?>
										<? } ?>

									</td>
									<td bgcolor="#f9f9f9" class="remainTimeBox" remain="<?=$_pdata->remain?>" endstamp='<?=strtotime($_pdata->end)?>'>
										<!--마감일-->
										<div style="margin-left:25px; width:100%; height:26px; text-align:left; font-size:11px;">마감 : <span style="font-size:12px; color:#444444; font-weight:bold;"><?=substr($_pdata->end,0,10)?> <?=date('D',strtotime($_pdata->end))?></span></div>
										<div style="margin-left:25px;">
											<div style="float:left; width:30px; height:30px; line-height:29px; font-family:verdana; color:#ffffff; font-size:24px; font-weight:bold; text-align:center; background:url('/images/common/todaysale/timebox_bg.gif') no-repeat; margin-right:3px; " class="remainHour1">0</div>
											<div style="float:left; width:30px; height:30px; line-height:29px; font-family:verdana; color:#ffffff; font-size:24px; font-weight:bold; text-align:center; background:url('/images/common/todaysale/timebox_bg.gif') no-repeat;" class="remainHour2">0</div>

											<div style="float:left;"><img src="/images/common/todaysale/timebox_dot_line.gif" alt="" /></div>

											<div style="float:left; width:30px; height:30px; line-height:29px; font-family:verdana; color:#ffffff; font-size:24px; font-weight:bold; text-align:center; background:url('/images/common/todaysale/timebox_bg.gif') no-repeat; margin-right:3px;" class="remainMin1">0</div>
											<div style="float:left; width:30px; height:30px; line-height:29px; font-family:verdana; color:#ffffff; font-size:24px; font-weight:bold; text-align:center; background:url('/images/common/todaysale/timebox_bg.gif') no-repeat;" class="remainMin2">0</div>

											<div style="float:left;"><img src="/images/common/todaysale/timebox_dot_line.gif" alt="" /></div>

											<div style="float:left; width:30px; height:30px; line-height:29px; font-family:verdana; color:#ffffff; font-size:24px; font-weight:bold; text-align:center; background:url('/images/common/todaysale/timebox_bg.gif') no-repeat; margin-right:3px;" class="remainSec1">0</div>
											<div style="float:left; width:30px; height:30px; line-height:29px; font-family:verdana; color:#ffffff; font-size:24px; font-weight:bold; text-align:center; background:url('/images/common/todaysale/timebox_bg.gif') no-repeat;" class="remainSec2">0</div>
										</div>
									</td>
								</tr>
								<tr>
									<td style="height:35px; padding-left:15px; color:#666666; font-size:11px; background:#f7f7f7; border-top:1px solid #eeeeee;">구매수량</td>
									<td style="padding-right:15px; color:#666666; font-size:11px; background:#f7f7f7; border-top:1px solid #eeeeee; text-align:right;">
										<span style="color:#ce0000; font-weight:bold; font-size:17px; font-family:arial;"><?=$_pdata->sellcnt?></span> 개 구매
									</td>
								</tr>
							</table>

						</td>
					</tr>
					<tr><td colspan="2" height="10"></td></tr>
					<tr>
						<th>배송비</th>
						<td>
							<?	if($_pdata->deli_price <= 0){
									switch($_pdata->deli){
										case 'N': echo '기본 배송비'; break;
										case 'F': echo '무료배송'; break;
										case 'G': echo '착불'; break;
											break;
									}
									//if($_pdata->deli == 'N')
								?>
							<? }else{ ?>
							<?=number_format($_pdata->deli_price)?>원
							<? if($_pdata->deli == 'Y') echo ' X 구매수량'; } ?>
						</td>
					</tr>
					<tr>
						<th>적립금</th>
						<td><span class="reservePoint"><?=number_format($_pdata->reserve)?>원</span></td>
					</tr>
					<!--
					<tr>
						<th><img src="/images/common/product/AD001/pdetail_skin_point.gif" alt="" /> 브랜드</th>
						<td>
							<span class="prBrand"><?=$_pdata->brand?></span>
						</td>
					</tr>
					<tr>
						<th><img src="/images/common/product/AD001/pdetail_skin_point.gif" alt="" /> 원산지</th>
						<td><span class="prMadein"><?=$_pdata->madein?></span></td>
					</tr>
					-->

					<!-- 사용자 정의 스팩 -->
					<?
						//echo $_pdata->userspec;

						if(strlen($_pdata->userspec)>0) {
							$specarray= explode("=",$_pdata->userspec);
							for($i=0; $i<count($specarray); $i++) {
								$specarray_exp = explode("", $specarray[$i]);
								if(strlen($specarray_exp[0])>0 || strlen($specarray_exp[1])>0) {
									${"pruserspec".$i} ="<td><IMG SRC=\"".$Dir."images/common/product/".$_cdata->detail_type."/pdetail_skin_point.gif\" border=\"0\"></td>\n";
									${"pruserspec".$i}.="<td>".$specarray_exp[0]."</td>\n";
									${"pruserspec".$i}.="<td></td>";
									${"pruserspec".$i}.="<td>".$specarray_exp[1]."</td>\n";
								} else {
									${"pruserspec".$i} = "";
								}
							}
						}
					?>

					<tr>
						<th>구매수량</th>
						<td>
							<div style="float:left;">
								<? if($_pdata->remain > 1){  // 마감 된 상품이 아닐 경우만?>
									<input type=text name="quantity" value="<?=($miniq>1?$miniq:"1")?>" style="font-size:11px; BORDER:#aaaaaa 1px solid; width:54px; HEIGHT:20px; line-height:20px; padding-left:10px;" onkeyup='strnumkeyup(this)' /> EA
								<? }else{ ?>
									마감된 상품 입니다.
								<? } ?>
							</div>
						</td>
					</tr>
					<? if(_array($option1) && count($option1) > 1){ ?>
					<tr>
						<th height="30"><?=$option1[0]?></td>
						<td><select name="option1" style="width:96%;">
							<? for($i=1;$i<count($option1);$i++){ ?>
								<option value="<?=$option1[$i]?>"><?=$option1[$i]?></option>
							<?	} ?>
							</select>
						</td>
					</tr>
					<? } ?>
					<? if(_array($option2) && count($option2) >1){ ?>
					<tr>
						<th><?=$option2[0]?></td>
						<td><select name="option2">
							<? for($i=1;$i<count($option2);$i++){ ?>
								<option value="<?=$option2[$i]?>"><?=$option2[$i]?></option>
							<?	} ?>
							</select>
						</td>
					</tr>
					<? } ?>
					<?=$proption?>

					<?
						// 사은품 , 적립금 , 쿠폰 사용 적용 확인
						$cateAuth = categoryAuth( $_pdata->productcode );
						$useableStr = '';
						foreach($cateAuth as $chkidx=>$etcchk){
							if($etcchk == 'Y') continue;
							switch($chkidx){
								case 'coupon': $etcname= '할인쿠폰'; break;
								case 'reserve': $etcname= '적립금'; break;
								case 'gift': $etcname= '구매사은품'; break;
								case 'return':case 'refund': $etcname= '교환및환불'; break;
							}
							$useableStr .="<tr>";
							$useableStr.="	<td>".$etcname."</td>\n";
							$useableStr.="	<td>".(($etcchk == 'Y')?'<span style="color:blue">적용가능</span>':'<span style="color:red">적용불가</span>').'</td></tr>';
						}
						//echo $useableStr;
					?>
					<tr><td colspan="2" height="10"></td></tr>
					<tr><td colspan="2" height="1" style="background:#dddddd;"></td></tr>
					<tr><td colspan="2" height="15"></tr>
				</table>
			</div>

			<? if($_pdata->remain > 1){  // 마감 된 상품이 아닐 경우만?>
			<div>
				<?
					if(_empty($dicker)){
						if(strlen($_pdata->quantity)>0 && $_pdata->quantity<=0){ ?>
							<FONT style=\"color:#F02800;\"><b>품 절</b></FONT>
				<? }else{ ?>
					<a href="javascript:CheckForm('ordernow','<?=$opti?>')" onMouseOver="window.status='바로구매';return true;"><IMG SRC="/data/design/img/detail/btn_baro.gif" border="0" /></a>
					<a href="javascript:CheckForm('','<?=$opti?>')" onMouseOver="window.status='장바구니담기';return true;"><IMG SRC="/data/design/img/detail/btn_cart.gif" border="0" /></a>
				<? } ?>
					<a href="javascript:<?=(strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted")?"CheckForm('wishlist','".$opti."')":"check_login();"?>"><IMG SRC="/data/design/img/detail/btn_wishlist.gif" border="0" /></a>
				<?	} ?>
			</div>
			<? } ?>
			</td>
		</tr>
	</table>


<script language="JavaScript">
	var miniq=<?=($miniq>1?$miniq:1)?>;
	var ardollar=new Array(3);
	ardollar[0]="<?=$ardollar[0]?>";
	ardollar[1]="<?=$ardollar[1]?>";
	ardollar[2]="<?=$ardollar[2]?>";
<?
	if(strlen($optcode)==0) {
		$maxnum=($count2-1)*10;
		if($optioncnt>0) {
			echo "num = new Array(";
			for($i=0;$i<$maxnum;$i++) {
				if ($i!=0) echo ",";
				if(strlen($optioncnt[$i])==0) echo "100000";
				else echo $optioncnt[$i];
			}
			echo ");\n";
		}
?>

function change_price(temp,temp2,temp3) {
<?=(strlen($dicker)>0)?"return;\n":"";?>
	if(temp3=="") temp3=1;
	price = new Array(<?if($priceindex>0) echo "'".number_format($_pdata->sellprice)."','".number_format($_pdata->sellprice)."',"; for($i=0;$i<$priceindex;$i++) { if ($i!=0) { echo ",";} echo "'".$pricetok[$i]."'"; } ?>);
	doprice = new Array(<?if($priceindex>0) echo "'".number_format($_pdata->sellprice/$ardollar[1],2)."','".number_format($_pdata->sellprice/$ardollar[1],2)."',"; for($i=0;$i<$priceindex;$i++) { if ($i!=0) { echo ",";} echo "'".$pricetokdo[$i]."'"; } ?>);
	if(temp==1) {
		if (document.form1.option1.selectedIndex><? echo $priceindex+2 ?>)
			temp = <?=$priceindex?>;
		else temp = document.form1.option1.selectedIndex;
		document.form1.price.value = price[temp];
		document.all["idx_price"].innerHTML = document.form1.price.value+"원";
<? if($_pdata->reservetype=="Y" && $_pdata->reserve>0) { ?>
		if(document.getElementById("idx_reserve")) {
			var reserveInnerValue="0";
			if(document.form1.price.value.length>0) {
				var ReservePer=<?=$_pdata->reserve?>;
				var ReservePriceValue=Number(document.form1.price.value.replace(/,/gi,""));
				if(ReservePriceValue>0) {
					reserveInnerValue = Math.round(ReservePer*ReservePriceValue*0.01)+"";
					var result = "";
					for(var i=0; i<reserveInnerValue.length; i++) {
						var tmp = reserveInnerValue.length-(i+1);
						if(i%3==0 && i!=0) result = "," + result;
						result = reserveInnerValue.charAt(tmp) + result;
					}
					reserveInnerValue = result;
				}
			}
			document.getElementById("idx_reserve").innerHTML = reserveInnerValue+"원";
		}
<? } ?>
		if(typeof(document.form1.dollarprice)=="object") {
			document.form1.dollarprice.value = doprice[temp];
			document.all["idx_dollarprice"].innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
		}
	}
	packagecal(); //패키지 상품 적용
	if(temp2>0 && temp3>0) {
		if(num[(temp3-1)*10+(temp2-1)]==0){
			alert('해당 상품의 옵션은 품절되었습니다. 다른 상품을 선택하세요');
			if(document.form1.option1.type!="hidden") document.form1.option1.focus();
			return;
		}
	} else {
		if(temp2<=0 && document.form1.option1.type!="hidden") document.form1.option1.focus();
		else document.form1.option2.focus();
		return;
	}
}

<? } else if(strlen($optcode)>0) { ?>

function chopprice(temp){
<?=(strlen($dicker)>0)?"return;\n":"";?>
	ind = document.form1.mulopt[temp];
	price = ind.options[ind.selectedIndex].value;
	originalprice = document.form1.price.value.replace(/,/g, "");
	document.form1.price.value=Number(originalprice)-Number(document.form1.opttype[temp].value);
	if(price.indexOf(",")>0) {
		optprice = price.substring(price.indexOf(",")+1);
	} else {
		optprice=0;
	}
	document.form1.price.value=Number(document.form1.price.value)+Number(optprice);
	if(typeof(document.form1.dollarprice)=="object") {
		document.form1.dollarprice.value=(Math.round(((Number(document.form1.price.value))/ardollar[1])*100)/100);
		document.all["idx_dollarprice"].innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
	}
	document.form1.opttype[temp].value=optprice;
	var num_str = document.form1.price.value.toString()
	var result = ''

	for(var i=0; i<num_str.length; i++) {
		var tmp = num_str.length-(i+1)
		if(i%3==0 && i!=0) result = ',' + result
		result = num_str.charAt(tmp) + result
	}
	document.form1.price.value = result;
	document.all["idx_price"].innerHTML=document.form1.price.value+"원";
	packagecal(); //패키지 상품 적용
}

<?}?>
<? if($_pdata->assembleuse=="Y") { ?>
function setTotalPrice(tmp) {
<?=(strlen($dicker)>0)?"return;\n":"";?>
	var i=true;
	var j=1;
	var totalprice=0;
	while(i) {
		if(document.getElementById("acassemble"+j)) {
			if(document.getElementById("acassemble"+j).value) {
				arracassemble = document.getElementById("acassemble"+j).value.split("|");
				if(arracassemble[2].length) {
					totalprice += arracassemble[2]*1;
				}
			}
		} else {
			i=false;
		}
		j++;
	}
	totalprice = totalprice*tmp;
	var num_str = totalprice.toString();
	var result = '';
	for(var i=0; i<num_str.length; i++) {
		var tmp = num_str.length-(i+1);
		if(i%3==0 && i!=0) result = ',' + result;
		result = num_str.charAt(tmp) + result;
	}
	if(typeof(document.form1.price)=="object") { document.form1.price.value=totalprice; }
	if(typeof(document.form1.dollarprice)=="object") {
		document.form1.dollarprice.value=(Math.round(((Number(document.form1.price.value))/ardollar[1])*100)/100);
		document.all["idx_dollarprice"].innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
	}
	if(document.getElementById("idx_assembleprice")) { document.getElementById("idx_assembleprice").value = result; }
	if(document.getElementById("idx_price")) { document.getElementById("idx_price").innerHTML = result+"원"; }
	if(document.getElementById("idx_price_graph")) { document.getElementById("idx_price_graph").innerHTML = result+"원"; }
	<?if($_pdata->reservetype=="Y" && $_pdata->reserve>0) { ?>
		if(document.getElementById("idx_reserve")) {
			var reserveInnerValue="0";
			if(document.form1.price.value.length>0) {
				var ReservePer=<?=$_pdata->reserve?>;
				var ReservePriceValue=Number(document.form1.price.value.replace(/,/gi,""));
				if(ReservePriceValue>0) {
					reserveInnerValue = Math.round(ReservePer*ReservePriceValue*0.01)+"";
					var result = "";
					for(var i=0; i<reserveInnerValue.length; i++) {
						var tmp = reserveInnerValue.length-(i+1);
						if(i%3==0 && i!=0) result = "," + result;
						result = reserveInnerValue.charAt(tmp) + result;
					}
					reserveInnerValue = result;
				}
			}
			document.getElementById("idx_reserve").innerHTML = reserveInnerValue+"원";
		}
	<? } ?>
}
<? } ?>

function packagecal() {
<?=(count($arrpackage_pricevalue)==0?"return;\n":"")?>
	pakageprice = new Array(<? for($i=0;$i<count($arrpackage_pricevalue);$i++) { if ($i!=0) { echo ",";} echo "'".$arrpackage_pricevalue[$i]."'"; }?>);
	var result = "";
	var intgetValue = document.form1.price.value.replace(/,/g, "");
	var temppricevalue = "0";
	for(var j=1; j<pakageprice.length; j++) {
		if(document.getElementById("idx_price"+j)) {
			temppricevalue = (Number(intgetValue)+Number(pakageprice[j])).toString();
			result="";
			for(var i=0; i<temppricevalue.length; i++) {
				var tmp = temppricevalue.length-(i+1);
				if(i%3==0 && i!=0) result = "," + result;
				result = temppricevalue.charAt(tmp) + result;
			}
			document.getElementById("idx_price"+j).innerHTML=result+"원";
		}
	}

	if(typeof(document.form1.package_idx)=="object") {
		var packagePriceValue = Number(intgetValue)+Number(pakageprice[Number(document.form1.package_idx.value)]);

		if(packagePriceValue>0) {
			result = "";
			packagePriceValue = packagePriceValue.toString();
			for(var i=0; i<packagePriceValue.length; i++) {
				var tmp = packagePriceValue.length-(i+1);
				if(i%3==0 && i!=0) result = "," + result;
				result = packagePriceValue.charAt(tmp) + result;
			}
			returnValue = result;
		} else {
			returnValue = "0";
		}
		if(document.getElementById("idx_price")) {
			document.getElementById("idx_price").innerHTML=returnValue+"원";
		}
		if(document.getElementById("idx_price_graph")) {
			document.getElementById("idx_price_graph").innerHTML=returnValue+"원";
		}
		if(typeof(document.form1.dollarprice)=="object") {
			document.form1.dollarprice.value=Math.round((packagePriceValue/ardollar[1])*100)/100;
			if(document.getElementById("idx_price_graph")) {
				document.getElementById("idx_price_graph").innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
			}
		}
	}
}
</script>
<? if($_pdata->remain > 1){  // 마감 된 상품이 아닐 경우만?>
	</form>
<? } ?>

	<div style="clear:both; margin-top:50px;">
		<!-- 중하단 상세 정보 영역 -->
		<div id="detailArea">
			<a name="1">
			<table cellpadding="0" cellspacing="0" width="100%" class="prDetailTab">
				<tr>
					<td class="prDetailTabOn"><a href="#1">상품상세정보</a></td>
					<? if(!_empty($deli_info)){ ?><td class="prDetailTabOff"><a href="#2">배송/AS/환불안내</a></td><? } ?>
					<td class="prDetailTabOff"><a href="#3">사용후기</a></td>
					<td class="prDetailTabOff"><a href="#4">상품Q&A</a></td>
				</tr>
			</table>

			<!-- 상품상세정보 -->
			<table cellpadding="0" cellspacing="0" align="center" style=" margin-left:auto; margin-right:auto">
				<tr>
					<td style="padding:20px 0px; text-align:center">
						<?
							if(strlen($detail_filter)>0) $_pdata->content = preg_replace($filterpattern,$filterreplace,$_pdata->content);

							if (strpos($_pdata->content,"table>")!=false || strpos($_pdata->content,"TABLE>")!=false)
								echo "<pre style=\"text-align:center\">".$_pdata->content."</pre>";
							else if(strpos($_pdata->content,"</")!=false)
								echo ereg_replace("\n","<br>",$_pdata->content);
							else if(strpos($_pdata->content,"img")!=false || strpos($_pdata->content,"IMG")!=false)
								echo ereg_replace("\n","<br>",$_pdata->content);
							else
								echo ereg_replace(" ","&nbsp;",ereg_replace("\n","<br>",$_pdata->content));
						?>
						<div id="gosiInfo">
							<?		// 상품정보고시
							$ditems = _getProductDetails($_pdata->pridx);
							if(_array($ditems) && count($ditems) > 0){
							?>
							<table border="0" cellpadding="0" cellspacing="0" class="productInfoGosi">
								<caption>전자상거래소비자보호법 시행규칙에 따른 상품정보제공 고시</caption>
							<?		foreach($ditems as $ditem){		?>
								<tr>
									<th><?=$ditem['dtitle']?></th>
									<td><?=nl2br($ditem['dcontent'])?></td>
								</tr>
							<?		}// end foreach	?>
							</table>
							<?	} // end if		?>
						</div>
					</td>
				</tr>
			</table>

		<!-- 배송/AS/환불 안내 -->
		<? if(!_empty($deli_info)){ ?>
			<a name="2">
			<table cellpadding="0" cellspacing="0" width="100%" class="prDetailTab">
				<tr>
					<td class="prDetailTabOff2"><a href="#1">상품상세정보</a></td>
					<? if(!_empty($deli_info)){ ?>
					<td class="prDetailTabOn"><a href="#2">배송/AS/환불안내</a></td>
					<? } ?>
					<td class="prDetailTabOff"><a href="#3">사용후기</a></td>
					<td class="prDetailTabOff"><a href="#4">상품Q&A</a></td>
				</tr>
			</table>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr><td style="padding:20px 0px;"><?=$deli_info?></td></tr>
			</table>
		<? } ?>

		<!-- 리뷰 -->
		<? if($_data->review_type!="N"){?>
			<a name="3">
			<table cellpadding="0" cellspacing="0" width="100%" class="prDetailTab">
				<tr>
					<td class="prDetailTabOff2"><a href="#1">상품상세정보</a></td>
					<? if(!_empty($deli_info)){ ?>
					<td class="prDetailTabOff2"><a href="#2">배송/AS/환불안내</a></td>
					<? } ?>
					<td class="prDetailTabOn"><a href="#3">사용후기</a></td>
					<td class="prDetailTabOff"><a href="#4">상품Q&A</a></td>
				</tr>
			</table>
			<div class="tblContents">
				<style>
					.reviewPoint {width:100%;margin:15px 0px;border-top:1px solid #eeeeee; border-bottom:1px solid #eeeeee;}
					.reviewPoint th {background:#f9f9f9; padding-left:25px;}
					.reviewPoint th strong {float:left; color:#ff4400; font-size:27px; font-weight:bold; font-family:tahoma; line-height:30px;}
					.reviewPoint th h4 {font-size:10px; font-weight:bold; font-family:tahoma;}

					.reviewPoint td {width:18%; padding:10px 0px; border-left:1px solid #eeeeee; text-align:center;background:#ffffff;}
					.reviewPoint td h4 {font-size:12px;}
					.reviewPoint td span {color:#222222; font-size:17px; font-family:tahoma; line-height:24px; font-weight:normal;}
				</style>

				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="reviewPoint">
					<tr>
						<th>
							<strong><?=$avertotalscore?></strong>
							<div style="float:left; margin-left:10px; text-align:left;">
								<h4>AVERAGE POINT</h4>
								<div><?=$reviewstarcount?></div>
							</div>
						</th>
						<td>
							<h4>품질</h4>
							<span><?=$averquality?></span>
							<div><?=$qualitystarcount?></div>
						</td>
						<td>
							<h4>가격</h4>
							<span><?=$averprice?></span>
							<div><?=$pricestarcount?></div>
						</td>
						<td>
							<h4>배송</h4>
							<span><?=$averdelitime?></span>
							<div><?=$delitimestarcount?></div>
						</td>
						<td>
							<h4>추천</h4>
							<span><?=$averrecommend?></span>
							<div><?=$recommendstarcount?></div>
						</td>
					</tr>
				</table>

				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td style="padding-bottom:20px;"><? include ($Dir.FrontDir."prreview.php"); ?></td>
					</tr>
				</table>
			</div>
		<? }?>

		<!-- 상품Q/A -->
		<? if(strlen($qnasetup->board)>0){ ?>
			<a name="4">
			<table cellpadding="0" cellspacing="0" width="100%" class="prDetailTab">
				<tr>
					<td class="prDetailTabOff2"><a href="#1">상품상세정보</a></td>
					<? if(!_empty($deli_info)){ ?>
					<td class="prDetailTabOff2"><a href="#2">배송/AS/환불안내</a></td>
					<? } ?>
					<td class="prDetailTabOff2"><a href="#3">사용후기</a></td>
					<td class="prDetailTabOn"><a href="#4">상품Q&A</a></td>
				</tr>
			</table>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td style="padding:5px 0px;">
					<? INCLUDE ($Dir.FrontDir."prqna.php"); ?>
					</td>
				</tr>
			</table>
		<? }?>
		</div>
	</div>

	<script language="javascript">
		<!--
		//리뷰 타입 선택
		function reviewSelect(type){
			var link ="<?=$reviewlink?>";
			location.href = link+"&review="+type+"#3";
			return;
		}


		//리뷰 작성 관련
		function review_write() {
			if(typeof(document.all["reviewwrite"])=="object") {
				if(document.all["reviewwrite"].style.display=="none") {
					document.all["reviewwrite"].style.display="";
				} else {
					document.all["reviewwrite"].style.display="none";
				}
			}
		}

		function write_review(){
			var userid = "<?=$_ShopInfo->getMemid()?>";
			var membergrant = "<?=$_data->review_memtype?>"; //회원 전용일경우
			var reviewgrant = "<?=$_data->review_type?>";
			var reviewetcgrant = "<?=$_data->ETCTYPE['REVIEW']?>";
			var _form = document.reviewWriteForm;
			if(reviewgrant == "N" || reviewetcgrant == "N"){
				alert("사용후기 설정이 되지 않아 사용 할 수 없습니다.");
				return;
			}else if(userid =="" && membergrant == "Y"){
				if(confirm("회원전용 기능입니다. 로그인 하시겠습니까?")){
					location.href="<?=$Dir.FrontDir?>login.php?chUrl=<?=getUrl()?>";
				}
				return;
			}else{

				if(_form.rname.value==""){
					alert("작성자를 입력해 주세요.");
					_form.rname.focus();
					return;
				}else if(_form.rname.rcontents){
					_form.rcontents.focus();
					return;
				}else{
					if(confirm("사용후기를 등록 하시겠습니까?")){
						_form.mode.value="write";
						_form.submit();
					}

					return;
				}
			}
		}

		function CheckReview() {
			if(document.reviewform.rname.value.length==0) {
				alert("작성자 이름을 입력하세요.");
				document.reviewform.rname.focus();
				return;
			}
			if(document.reviewform.rcontent.value.length==0) {
				alert("사용후기 내용을 입력하세요.");
				document.reviewform.rcontent.focus();
				return;
			}
			document.reviewform.mode.value="review_write";
			document.reviewform.submit();
		}
		//-->
	</script>