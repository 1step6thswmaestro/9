<?
	$Dir = "../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once($Dir."m/inc/function.php");
	$_FileInfo = _uploadMaxFileSize();

	$_MAX_FILE_SIZE = $_FileInfo['maxsize'];
	$_MSG_UNIT = $_FileInfo['unit'];
	$productcode = !_empty($_POST['productcode'])?trim($_POST['productcode']):"";
	
	$mode = !_empty($_POST['mode'])?trim($_POST['mode']):"";  //모드
	$categorycode = !_empty($_POST['code'])?trim($_POST['code']):""; // 카테고리코드

	$sort = !_empty($_POST['sort'])?trim($_POST['sort']):""; // 정렬
	$quality = !_empty($_POST['quality'])?trim($_POST['quality']):"";//품질
	$price = !_empty($_POST['price'])?trim($_POST['price']):"";//가격
	$delitime = !_empty($_POST['delitime'])?trim($_POST['delitime']):"";//배송시간
	$recommend = !_empty($_POST['recommend'])?trim($_POST['recommend']):"";//추천
	$writer = !_empty($_POST['rname'])?trim($_POST['rname']):"";//작성자
	$contents= !_empty($_POST['rcontent'])?trim($_POST['rcontent']):"";//내용

	if(strlen($productcode)<=0){
		echo '<script>alert("잘못된 페이지 접근입니다.");self.close();</script>';exit;
	}
	
	$productSQL ="SELECT * FROM tblproduct WHERE productcode = '".$productcode."' ";
	$imagesrc = $Dir."data/shopimages/product/";
	if(false !== $productRes = mysql_query($productSQL,get_db_conn())){
		$productrowcount = mysql_num_rows($productRes);

		if($productrowcount>0){
			$productRow = mysql_fetch_assoc($productRes);
			$productname = $productRow['productname'];
			$productimage =$productRow['minimage'];
			$productprice = number_format($productRow['sellprice']);
			$src = $imagesrc.$productimage;
			$size = _getImageRateSize($src,80);
		}
	}


	if($_data->review_type =="N" || $_data->ETCTYPE["REVIEW"]=="N") {
		echo '<script>alert("사용후기 기능 설정이 되지 않아 사용할 수 없습니다.");location.replace("/m/productdetail_tab03.php?productcode='.$productcode.'");</script>';exit;
	}
	if(strlen($_ShopInfo->getMemid())==0 && $_data->review_memtype=="Y"){
		echo '<script>alert("회원전용 기능입니다.");location.replace("/m/productdetail_tab03.php?productcode='.$productcode.'");</script>';exit;
	}
	
	if($mode == "write"){
		if(strlen($code) <= 0 || strlen($code)>12){
			echo '<script>alert("정상적인 방법이 아니므로 접근할 수 없습니다2.");location.replace("/m/productdetail_tab03.php?productcode='.$productcode.'");</script>';exit;
		}else if(strlen($productcode) <= 0 || strlen($productcode)>18){
			echo '<script>alert("정상적인 방법이 아니므로 접근할 수 없습니다3.");location.replace("/m/productdetail_tab03.php?productcode='.$productcode.'");</script>';exit;
		}else if(strlen($quality) <= 0 || ($quality < 1 || $quality > 5)){
			echo '<script>alert("품질 점수를 선택해 주세요.");location.replace("/m/productdetail_tab03.php?productcode='.$productcode.'");</script>';exit;
		}else if(strlen($price) <= 0 || ($price < 1 || $price > 5)){
			echo '<script>alert("가격 점수를 선택해 주세요.");location.replace("/m/productdetail_tab03.php?productcode='.$productcode.'");</script>';exit;
		}else if(strlen($delitime) <= 0 || ($delitime < 1 || $delitime > 5)){
			echo '<script>alert("배송 점수를 선택해 주세요.");location.replace("/m/productdetail_tab03.php?productcode='.$productcode.'");</script>';exit;
		}else if(strlen($recommend) <= 0 || ($recommend < 1 || $recommend > 5)){
			echo '<script>alert("추천 점수 선택해 주세요.");location.replace("/m/productdetail_tab03.php?productcode='.$productcode.'");</script>';exit;
		}else if(strlen($writer) <= 0){
			echo '<script>alert("작성자를 입력해 주세요.");location.replace("/m/productdetail_tab03.php?productcode='.$productcode.'");</script>';exit;
		}else if(strlen($contents) <= 0){
			echo '<script>alert("내용을 입력해 주세요.");location.replace("/m/productdetail_tab03.php?productcode='.$productcode.'");</script>';exit;
		}
	
	
		$allowfile = array('image/pjpeg','image/jpeg','image/JPG','image/X-PNG','image/PNG','image/png','image/x-png','image/gif');
		$saveattechfile = $Dir."data/shopimages/productreview/";
		$getmaxfilesize = _uploadMaxFileSize();
		
		$maxfilesize = $getmaxfilesize['maxsize'];//실제사이즈
		$maxfilesize_unit = $getmaxfilesize['unit'];//컨버팅사이즈
		$filename="";
		$queryattechname="";
		$attechfilename = !_empty($_FILES['attech']['name'])?trim($_FILES['attech']['name']):"";
		if(strlen($attechfilename)>0){
			$attechfiletype = !_empty($_FILES['attech']['type'])?trim($_FILES['attech']['type']):"";
			$attechfilesize = !_empty($_FILES['attech']['size'])?trim($_FILES['attech']['size']):"";
			$attechtempfilename = !_empty($_FILES['attech']['tmp_name'])?trim($_FILES['attech']['tmp_name']):"";
			if(!in_array($attechfiletype,$allowfile)){
				echo '<script>alert("첨부 가능한 파일이 아닙니다.\n첨부가능한 파일은 jpg, gif, png입니다.");location.replace("/m/productdetail_tab03.php?productcode='.$productcode.'");</script>';exit;
			}else{
				if($attechfilesize >$maxfilesize){
					echo '<script>alert("첨부 가능한 파일 용량이 초과 되었습니다.\n최대 첨부가능한 파일용량은 '.$maxfilesize_unit.'입니다.");location.replace("/m/productdetail_tab03.php?productcode='.$productcode.'");</script>';exit;
				}else{
					$filename = date("YmdHis").$attechfilename;

					if(move_uploaded_file($attechtempfilename,$saveattechfile.$filename)){
						$queryattechname = $filename;
					}
				}
			}
		}
		
		$reviewwriteSQL ="INSERT tblproductreview SET ";
		$reviewwriteSQL.= "productcode	= '".$productcode."'";
		$reviewwriteSQL.= ", id			= '".$_ShopInfo->getMemid()."'";
		$reviewwriteSQL.= ", name		= '".$writer."'";
		$reviewwriteSQL.= ", marks		= '".$avermark."'";
		$reviewwriteSQL.= ", date		= '".date("YmdHis")."'";
		$reviewwriteSQL.= ", content		= '".$contents."'";
		$reviewwriteSQL.= ", device		= 'P'";
		$reviewwriteSQL.= ", quality		= '".$quality."'";
		$reviewwriteSQL.= ", price		= '".$price."'";
		$reviewwriteSQL.= ", delitime		= '".$delitime."'";
		$reviewwriteSQL.= ", recommend = '".$recommend."' ";
		if(strlen($queryattechname)>0){
			$reviewwriteSQL.= ", img = '".$queryattechname."' ";
		}
		
		$returnmsg="";

		if(false !== mysql_query($reviewwriteSQL,get_db_conn())){
			if($_data->review_type=="A") {
				$returnmsg="관리자 인증후 등록됩니다.";
			}else{
				$returnmsg="상품평이 등록되었습니다.";
			}
		}else{
			$returnmsg="상품평이 등록되지 않았습니다.";
		}

		echo '<script>alert("'.$returnmsg.'");self.close();</script>';
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
		<meta http-equiv="Cache-Control" content="no-cache" />
		<link rel="stylesheet" href="./css/common.css" />
		<style>
			body,img,div{margin:0px;padding:0px;border:0px;}
			.review_wrap{padding:5px;}
			.reviewForm{margin-top:5px;width:100%;border-top:2px solid #777;border-bottom:1px solid #777}
			.reviewForm th, .reviewForm td{border-bottom:1px solid #777;padding:3px 0px;}
			.reviewForm td input {border:1px solid #9e9e9e;height:22px;}
			.reviewForm td textarea{display:block;width:100%;box-sizing:border-box;height:80px;resize:none;border:1px solid #9e9e9e}
			.addfileinfo{font-size:0.9em;color:#FF5E00}
			.review_btn_box{text-align:center;margin-top:5px;border:0px;}
		</style>
		<script type="text/javascript" src="/m/js/jquery-1.10.2.min.js"></script>
	</head>
	<body>
		
		<div style="height:40px;line-height:40px;text-align:center;background-color:#177edb;color:#FFF;font-wieght:bold;font-size:1.2em">
			상품 후기 작성
		</div>
		<div class="review_wrap">	
			<table cellpadding="0" cellspacing="0" border="0" class="prwrap">
				<tr>
					<td>
						<img src="<?=$src?>" <?=$size?>/>
					</td>
					<td style="padding-left:5px;">
						<p><?=$productname?></p>
						<p><?=$productprice?>원</p>
					</td>
				</tr>
			</table>
			<form name="reviewForm" action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="code" value="<?=substr($productcode,0,12)?>">
			<input type="hidden" name="productcode" value="<?=$productcode?>" />
			<input type="hidden" name="page" value="<?=$currentPage?>">
			<input type="hidden" name="mode" value="write">
			<input type="hidden" name="sort" value="<?=$sort?>" />

			<table border="0" cellpadding="0" cellspacing="0" class="reviewForm">
				<col width="80"></col>
				<col width=""></col>
				<tr>
					<th>작성자</th>
					<td><input type="text" name="rname" maxlength="6" value=""></td>
				</tr>
				<tr>
					<th>품질</th>
					<td>
						<select name="quality">
							<option value="1">★</option>
							<option value="2">★★</option>
							<option value="3">★★★</option>
							<option value="4">★★★★</option>
							<option value="5" selected>★★★★★</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>가격</th>
					<td>
						<select name="price">
							<option value="1">★</option>
							<option value="2">★★</option>
							<option value="3">★★★</option>
							<option value="4">★★★★</option>
							<option value="5" selected>★★★★★</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>배송</th>
					<td>
						<select name="delitime">
							<option value="1">★</option>
							<option value="2">★★</option>
							<option value="3">★★★</option>
							<option value="4">★★★★</option>
							<option value="5" selected>★★★★★</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>추천</th>
					<td>
						<select name="recommend">
							<option value="1">★</option>
							<option value="2">★★</option>
							<option value="3">★★★</option>
							<option value="4">★★★★</option>
							<option value="5" selected>★★★★★</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>내용</th>
					<td><textarea name="rcontent"></textarea></td>
				</tr>
				<tr>
					<th>첨부</th>
					<td>
						<input type="file" name="attech" id="attech" value=""/>
						<p class="addfileinfo">
							<strong><?=$_MSG_UNIT?>이상</strong>의 이미지는 첨부하실 수 없습니다.<br/>
							이미지(jpg,gif,png) 파일만 업로드 가능합니다.<br/>
						</p>
					</td>
				</tr>
			</table>
			<div class="review_btn_box"><input type="button" class="button blue bigrounded" id="btn_submit" value="리뷰등록"> <input type="button" class="button white bigrounded" id="btn_reset" value="다시쓰기"> <button class="button white bigrounded" onClick="self.close();">닫기</button></div>
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=$_MAX_FILE_SIZE?>" />
			</form>
		</div>
		<script>
			var $p = jQuery.noConflict();
			$p(".write_btn").click(function(){
				var loginid = "<?=$_ShopInfo->getMemid()?>";
				var writetype = "<?=$_data->review_memtype?>";

				if(writetype =="Y"){
					if(loginid.length > 0 && loginid !=""){
						$p(".review_container").css("display", "block");
						$p(".write_btn").css("display","none");
						$p(".write_close").css("display", "block");
					}else{
						if(confirm("상품평 작성은 회원 전용입니다.\로그인 하시겠습니까?")){
							window.location='/m/login.php?chUrl='+"<?=getUrl()?>";
						}
					}
				}else{
					$p(".review_container").css("display", "block");
					$p(".write_btn").css("display","none");
					$p(".write_close").css("display", "block");
				}
				return;
			});

			$p(".write_close").click(function(){
				$p(".review_container").css("display", "none");
				$p(".write_close").css("display", "none");
				$p(".write_btn").css("display","block");
			});

			var form = document.reviewForm;
			$p("#btn_submit").click(function(){
				if($p("input[name=rname]").val() == "" || $p("input[name=rname]").val() == null){
					alert("이름을 작성하세요.");
					$p("input[name=rname]").focus();
					return false;
				}else if($p("textarea[name=rcontent]").val() == "" || $p("textarea[name=rcontent]").val() == null){
					alert("내용을 작성하세요.");
					$p("textarea[name=rcontent]").focus();
					return false;
				}else{

					var filestate = document.getElementById('attech');
					if(filestate.value != "" || filestate.value == "undefined" || filestate.value == null){

						var imageMaxSize = "<?=$_MAX_FILE_SIZE?>";
						var fileSize = filestate.files[0].size;
						if(fileSize > imageMaxSize){
							alert("첨부할수 있는 최대 용량은 <?=$_MSG_UNIT?>입니다.");
							return false;
						}
					}

					if(confirm("후기를 등록하시겠습니까?")){
						$p("#btn_submit").css("display", "none");
						form.submit();
						return;
					}else{
						return false;
					}
				}
			});

			$p("#btn_reset").click(function(){
				form.reset();
				return;
			});

			function moreReview(){
				var _form = document.moreReviewForm;

				if(_form.prcode.value == "" || _form.review.value == ""){
					alert("정상적인 경로로 이용해 주세요");
					return;
				}
				_form.submit();
				return;
			}

			function reviewSelect(type){
				var rlink ="<?=$locationlink?>";
				location.href=rlink+"&review="+type+"#retypert";
				return;
			}
		</script>
	</body>
</html>