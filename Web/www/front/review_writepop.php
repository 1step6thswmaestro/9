<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/func.php");

if($_POST['mode']=="review_write") {
	if($_data->review_type=="N") _alert('상품 리뷰 쓰기가 허용 되지 않습니다.','0');

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
		_alert('로그인 되어 있지 않습니다.','0');
	}
	if(strlen($review_filter)>0) {	//사용후기 내용 필터링
		if(ReviewFilter($review_filter,$rcontent,$findFilter)) {
			_alert("사용하실 수 없는 단어를 입력하셨습니다.('".$findFilter.")\\n\\n다시 입력하시기 바랍니다.');",'-1');
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
	$sql.= "productcode	= '".$_POST['productcode']."', ";
	$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
	$sql.= "name		= '".$rname."', ";
	$sql.= "marks		= '".$rmarks."', ";
	$sql.= "date		= '".date("YmdHis")."', ";
	$sql.= "content		= '".$rcontent."', ";
	$sql.= "img		= '".$up_name."' ";
	mysql_query($sql,get_db_conn());

	if($_data->review_type=="A") $msg="관리자 인증후 등록됩니다.";
	else $msg="등록되었습니다.";
	_alert($msg,'0');
}else if(!_empty($_REQUEST['ordercode']) && preg_match('/^[0-9]{18}$/',$_REQUEST['productcode'])){
	$sql = "SELECT * FROM tblproductcode WHERE codeA='".substr($_REQUEST['productcode'],0,3)."' AND codeB='".substr($_REQUEST['productcode'],3,3)."' AND codeC='".substr($_REQUEST['productcode'],6,3)."' AND codeD='".substr($_REQUEST['productcode'],9,3)."' ";
	$result = mysql_query($sql,get_db_conn());
	$_cdata = mysql_fetch_object($result);
}else{
	_alert('잘못된 접근 입니다.'.$_REQUEST['ordercode'].':'.$_REQUEST['productcode'],'0');
}


?>
<html>
<head>
<title>사용후기</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td {
	font-family:"굴림,돋움";
	color:#4B4B4B;
	font-size:12px;
	line-height:17px;
}
BODY, DIV, form, TEXTAREA, center, option, pre, blockquote {
	font-family:Tahoma;
	color:000000;
	font-size:9pt;
}
A:link {
	color:#635C5A;
	text-decoration:none;
}
A:visited {
	color:#545454;
	text-decoration:none;
}
A:active {
	color:#5A595A;
	text-decoration:none;
}
A:hover {
	color:#545454;
	text-decoration:underline;
}
.input {
	font-size:12px;
	BORDER-RIGHT: #DCDCDC 1px solid;
	BORDER-TOP: #C7C1C1 1px solid;
	BORDER-LEFT: #C7C1C1 1px solid;
	BORDER-BOTTOM: #DCDCDC 1px solid;
	HEIGHT: 18px;
	BACKGROUND-COLOR: #ffffff;
	padding-top:2pt;
	padding-bottom:1pt;
	height:19px
}
.select {
	color:#444444;
	font-size:12px;
}
.textarea {
	border:solid 1;
	border-color:#e3e3e3;
	font-family:돋음;
	font-size:9pt;
	color:333333;
	overflow:auto;
	background-color:transparent
}
</style>
<script language="javascript" type="text/javascript">
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
</script>
</head>
<form name=reviewform method=post action="<?=$_SERVER[PHP_SELF]?>" enctype="multipart/form-data">
<input type=hidden name=mode>
<input type=hidden name=productcode value="<?=$_REQUEST['productcode']?>">
<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td background="<?=$Dir?>images/common/review_tit_bg.gif"><IMG SRC="<?=$Dir?>images/common/review_tit.gif"></td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="96%" align="center">
	<col width="6"></col>
	<col></col>
	<col width="6"></col>
	<tr>
		<td height="6" colspan="3" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_t1.gif"></td>
	</tr>
	<tr>
		<td background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_t2.gif"></td>
		<td background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_tbg.gif" style="padding:7pt;">
			<table cellpadding="0" cellspacing="0" width="100%">
				<col width="40"></col>
				<col width="80"></col>
				<col width="40"></col>
				<col width=></col>
				<tr>
					<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_write_name.gif"></td>
					<td>
						<input type=text name=rname size="10" class="input">
					</td>
					<td align="right"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_write_point.gif" border="0"></td>
					<td style="font-size:11px; letter-spacing:-0.5pt; padding-bottom:2px;">
						<input type=radio name=rmarks value="1"><FONT color=#000000>★</FONT>
						<input type=radio name=rmarks value="2"><FONT color=#000000>★★</FONT>
						<input type=radio name=rmarks value="3"><FONT color=#000000>★★★</FONT>
						<input type=radio name=rmarks value="4"><FONT color=#000000>★★★★</FONT>
						<input type=radio name=rmarks value="5" checked><FONT color=#000000>★★★★★</FONT>
					</td>
				</tr>
				<tr><td height="8"></td></tr>
				<tr>
					<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_write_file.gif"></td>
					<td colspan="3">
						<input type="file" name="img" value="" /><br><font style="font-size:11px; letter-spacing:-0.5px;">(첨부 이미지가 있을경우 첨부해 주세요. 최대폭은 650px으로 강제조정 됩니다.)</font>
					</td>
				</tr>
				<tr><td height="5"></td></tr>
				<tr>
					<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_write_con.gif" border="0"></td>
					<td colspan="3">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="100%">
									<TEXTAREA name="rcontent" style="WIDTH: 99%; HEIGHT: 40pxpadding:3pt;line-height:17px;border:solid 1;border-color:#DFDFDF;font-size:9pt;color:333333;"></TEXTAREA>
								</td>
								<td align="right"><a href="javascript:CheckReview();"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_write_btn03.gif" border="0"></a></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
		<td background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_t5.gif"></td>
	</tr>
	<tr>
		<td height="6" colspan="3" background="<?=$Dir?>images/common/product/<?=$_cdata->detail_type?>/pdetail_skin_t3.gif"></td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
</table>
</form>
</body>
</html>