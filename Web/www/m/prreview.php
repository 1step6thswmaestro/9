<?
if($reviewdate!="N") $colspan=4;
$qry = "WHERE productcode='".$productcode."' ";
if($_data->review_type=="A") $qry.= "AND display='Y' ";
$sql = "SELECT COUNT(*) as t_count, SUM(marks) as totmarks FROM tblproductreview ";
$sql.= $qry;

$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$totalRecord = (int)$row->t_count;
$totmarks = (int)$row->totmarks;
$marks=@ceil($totmarks/$t_count);
mysql_free_result($result);

$reviewimagedir = $Dir."data/shopimages/productreview/";


$reviewcounterSQL = "SELECT ";
$reviewcounterSQL .= "COUNT(num) AS total ";
$reviewcounterSQL .= ",SUM(IF(img IS NULL OR img ='',1,0)) AS basic ";
$reviewcounterSQL .= ",SUM(IF(img IS NOT NULL AND img !='',1,0)) AS photo ";
$reviewcounterSQL .= ",SUM(IF(best = 'Y',1,0)) AS best ";
$reviewcounterSQL .= "FROM tblproductreview ";
$reviewcounterSQL .= "WHERE productcode = '".$productcode."' ";
if($_data->review_type=="A") $reviewcounterSQL.= "AND display='Y' ";
if(false !== $reviewcountRes = mysql_query($reviewcounterSQL,get_db_conn())){
	$reviewcountRow = mysql_fetch_assoc($reviewcountRes);
	mysql_free_result($reviewcountRes);
}

$counttotal = ($reviewcountRow['total'])?trim($reviewcountRow['total']):"0";
$countbasic = ($reviewcountRow['basic'])?trim($reviewcountRow['basic']):"0";
$countphoto = ($reviewcountRow['photo'])?trim($reviewcountRow['photo']):"0";
$countbest = ($reviewcountRow['best'])?trim($reviewcountRow['best']):"0";

$reviewtype = !_empty($_GET['review'])?trim($_GET['review']):"all";
$sort = !_empty($_GET['sort'])?trim($_GET['sort']):"";
$locationlink = $_SERVER['PHP_SELF']."?productcode=".$productcode."&sort=".$sort;

$addsql = "";
$photoclass=$basicclass=$bestclass=$allclass="white";
switch($reviewtype){
	case "photo":
		$addsql = "AND img IS NOT NULL AND img !='' ";
		$photoclass = "black";
	break;
	case "basic":
		$addsql = "AND img IS NULL OR img ='' ";
		$basicclass = "black";
	break;
	case "best":
		$addsql = "AND best = 'Y' ";
		$bestclass = "black";
	break;
	case "all":
	default:
		$allclass = "black";
	break;

}
?>
<div class="w_and_c">
	<span>���Բ��� �ۼ��� �ֽô� ��ǰ���� �ٸ� ���е��� ������ ���������� �˴ϴ�.</span>
	<div class="write_btn"><button class="button blue small">��ǰ�� �ۼ��ϱ�</button></div>
	<div class="write_close"><button class="button white small">�ۼ�â �ݱ�</button></div>
</div>

<div class="review_container" id="review_form_box">
	<form name="reviewForm" action="./review_write_proc.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="code" value="<?=substr($productcode,0,12)?>">
	<input type="hidden" name="productcode" value="<?=$productcode?>" />
	<input type="hidden" name="page" value="<?=$currentPage?>">
	<input type="hidden" name="mode" value="write">
	<input type="hidden" name="sort" value="<?=$sort?>" />

	<table border="0" cellpadding="0" cellspacing="0" class="reviewForm">
		<caption>��ǰ�� ����</caption>
		<col width="70"></col>
		<col width=""></col>
		<tr>
			<th>�ۼ���</th>
			<td><input type="text" name="rname" maxlength="6" value=""></td>
		</tr>
		<tr>
			<th>ǰ��</th>
			<td>
				<select name="quality">
					<option value="1">��</option>
					<option value="2">�ڡ�</option>
					<option value="3">�ڡڡ�</option>
					<option value="4">�ڡڡڡ�</option>
					<option value="5" selected>�ڡڡڡڡ�</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>����</th>
			<td>
				<select name="price">
					<option value="1">��</option>
					<option value="2">�ڡ�</option>
					<option value="3">�ڡڡ�</option>
					<option value="4">�ڡڡڡ�</option>
					<option value="5" selected>�ڡڡڡڡ�</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>���</th>
			<td>
				<select name="delitime">
					<option value="1">��</option>
					<option value="2">�ڡ�</option>
					<option value="3">�ڡڡ�</option>
					<option value="4">�ڡڡڡ�</option>
					<option value="5" selected>�ڡڡڡڡ�</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>��õ</th>
			<td>
				<select name="recommend">
					<option value="1">��</option>
					<option value="2">�ڡ�</option>
					<option value="3">�ڡڡ�</option>
					<option value="4">�ڡڡڡ�</option>
					<option value="5" selected>�ڡڡڡڡ�</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>����</th>
			<td><textarea name="rcontent"></textarea></td>
		</tr>
		<tr>
			<th>÷��</th>
			<td>
				<input type="file" name="attech" id="attech" value=""/>
				<p class="addfileinfo"><strong><?=$_MSG_UNIT?>�̻�</strong>�� �̹����� ÷���Ͻ� �� �����ϴ�.</p>
			</td>
		</tr>
	</table>
	<div class="review_btn_box"><input type="button" class="button blue bigrounded" id="btn_submit" value="������"> <input type="button" class="button white bigrounded" id="btn_reset" value="�ٽþ���"></div>
	<input type="hidden" name="MAX_FILE_SIZE" value="<?=$_MAX_FILE_SIZE?>" />
	</form>
</div>
<a name="retypert" id="retypert"></a>
<section id="sec_reviewlsit_wrap">
	<div class="div_reviewtypebtn_wrap">
		<button class="btn_reviewtype button small <?=$allclass?>" onClick="reviewSelect('all');">��ü(<?=$counttotal?>)</button><button  class="btn_reviewtype button small <?=$bestclass?>" onClick="reviewSelect('best');">����Ʈ(<?=$countbest?>)</button><button class="btn_reviewtype button small <?=$photoclass?>" onClick="reviewSelect('photo');">����(<?=$countphoto?>)</button><button class="btn_reviewtype button small <?=$basicclass?>" onClick="reviewSelect('basic');">�Ϲ�(<?=$countbasic?>)</button>

	</div>
	<div class="div_reviewtypebtn_morebt" onClick="moreReview();">������</div>

	<?
		// ���� �ٷκ���
		include('reviewAjaxView.php');
	?>

	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="reviewTbl">
	<?
		$reviewlistSQL = "SELECT * FROM tblproductreview ";
		$reviewlistSQL .= "WHERE productcode='".$productcode."' ";
		$reviewlistSQL .=$addsql;
		if($_data->review_type=="A") $reviewlistSQL.= "AND display='Y' ";
		$reviewlistSQL .="ORDER BY date DESC LIMIT 0, 10";

		if(false !== $reviewlistRes = mysql_query($reviewlistSQL,get_db_conn())){
			$reviewrowcount = mysql_num_rows($reviewlistRes);
			if($reviewrowcount>0){
				while($reviewlistRow = mysql_fetch_assoc($reviewlistRes)){
					$attechfile=$contents=$num=$averstarcount=$writer=$viewstar=$regdate=$src=$imagearea=$size=$viewtype="";
					$attechfile = $reviewlistRow['img'];
					$contents = explode("=",$reviewlistRow['content']);//$reviewlistRow['content'];
					$num = $reviewlistRow['num'];
					$averstarcount = $reviewlistRow['marks'];
					$writer = $reviewlistRow['name'];
					$regdate = substr($reviewlistRow['date'],0,4)."-".substr($reviewlistRow['date'],4,2)."-".substr($reviewlistRow['date'],6,2);
					$src = $reviewimagedir.$attechfile;
					$size = _getImageRateSize($src,80);
					for($i=1;$i<=5;$i++){
						if($i <= $averstarcount){
							$viewstar.='<img src="/images/003/star_point1.gif" alt="" />';
						}else{
							$viewstar.='<img src="/images/003/star_point2.gif" alt="" />';
						}
					}
					#�̹��� ó���κ�
					if(strlen($attechfile)>0){
						$imagearea = '<img src="'.$src.'" '.$size.' />';
						$viewtype ="<img src=\"skin/default/img/icon_photo.png\" alt=\"\" /> ";
					}else{
						$imagearea = $viewstar;
					}

					if( $reviewlistRow['best'] == "Y"){
						$viewtype .="<img src=\"skin/default/img/icon_best.png\" alt=\"\" /> ";
					}



		?>
		<tr>
			<td width="90" align="center">
				<a href="javascript:zoomImage('<?=$src?>')"><?=$imagearea?></a>
			</td>
			<td onclick="reviewOpen('<?=$productcode?>','<?=$reviewlistRow['num']?>', event);">
				<p><?=(strlen($attechfile)>0)?$viewstar:""?></p>
				<p><?=$contents[0]?> <?=$viewtype?></p>
				<p class="writeinfo"><?=$writer?> / <?=$regdate?></p>
			</td>
		</tr>
<?
				}
			}else{
?>
		<tr><td colspan="2" align="center">��ϵ� �Խñ��� �����ϴ�.</td></tr>
<?
			}
		}
?>
	</table>
</section>

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
				if(confirm("��ǰ�� �ۼ��� ȸ�� �����Դϴ�.\�α��� �Ͻðڽ��ϱ�?")){
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
			alert("�̸��� �ۼ��ϼ���.");
			$p("input[name=rname]").focus();
			return false;
		}else if($p("textarea[name=rcontent]").val() == "" || $p("textarea[name=rcontent]").val() == null){
			alert("������ �ۼ��ϼ���.");
			$p("textarea[name=rcontent]").focus();
			return false;
		}else{

			var filestate = document.getElementById('attech');
			if(filestate.value != "" || filestate.value == "undefined" || filestate.value == null){

				var imageMaxSize = "<?=$_MAX_FILE_SIZE?>";
				var fileSize = filestate.files[0].size;
				if(fileSize > imageMaxSize){
					alert("÷���Ҽ� �ִ� �ִ� �뷮�� <?=$_MSG_UNIT?>�Դϴ�.");
					return false;
				}
			}

			if(confirm("�ı⸦ ����Ͻðڽ��ϱ�?")){
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
			alert("�������� ��η� �̿��� �ּ���");
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
<form name="moreReviewForm" action="./prreview_list.php" method="get">
	<input type="hidden" name="prcode" value="<?=$productcode?>">
	<input type="hidden" name="review" value="<?=$reviewtype?>">
</form>


