<?
	include_once("./header.php");
	include_once($Dir."m/inc/paging_inc.php");
	$_code = isset($_GET['prcode'])?trim($_GET['prcode']):"";
	$_review = isset($_GET['review'])?trim($_GET['review']):"";
	$curpage =isset($_GET['page'])?trim($_GET['page']):"1";

	$listitem =10;
	if(_empty($_review) || _empty($_code)){
		echo "<script>alert(\"�߸��� ������ �����Դϴ�.\");history.go(-1);</script>";exit;
	}
	$origloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/product/"; // �������� ���
	$saveloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/mobile/"; // �泻�� ���� ���
	$quality = 100;

	$reviewcounterSQL = "SELECT ";
	$reviewcounterSQL .= "COUNT(num) AS total ";
	$reviewcounterSQL .= ",SUM(IF(img IS NULL OR img ='',1,0)) AS basic ";
	$reviewcounterSQL .= ",SUM(IF(img IS NOT NULL AND img !='',1,0)) AS photo ";
	$reviewcounterSQL .= ",SUM(IF(best = 'Y',1,0)) AS best ";
	$reviewcounterSQL .= "FROM tblproductreview ";
	$reviewcounterSQL .= "WHERE productcode = '".$_code."' ";
	if($_data->review_type=="A") $reviewcounterSQL.= "AND display='Y' ";
	if(false !== $reviewcountRes = mysql_query($reviewcounterSQL,get_db_conn())){
		$reviewcountRow = mysql_fetch_assoc($reviewcountRes);
		mysql_free_result($reviewcountRes);
	}

	$counttotal = ($reviewcountRow['total'])?trim($reviewcountRow['total']):"0";
	$countbasic = ($reviewcountRow['basic'])?trim($reviewcountRow['basic']):"0";
	$countphoto = ($reviewcountRow['photo'])?trim($reviewcountRow['photo']):"0";
	$countbest = ($reviewcountRow['best'])?trim($reviewcountRow['best']):"0";

	$tearmsSQL="";
	$photoclass=$basicclass=$bestclass=$allclass="white";
	switch($_review){
		case 'photo':
			$tearmsSQL ="AND (img IS NOT null AND img != '') ";
			$photoclass = "black";
		break;
		case 'basic':
			$tearmsSQL ="AND (img IS null OR img = '') ";
			$basicclass = "black";
		break;
		case 'best':
			$tearmsSQL ="AND best = 'Y' ";
			$bestclass = "black";
		break;
		case 'all':
		default:
			$allclass = "black";
		break;

	}

	$currentPage = isset($_GET['page'])?$_GET['page']:1;
	$pageBlock = 5;
	$pageList = 5;

	$imgreview = $Dir."data/shopimages/productreview/";

?>


<?
	// ���� �ٷκ���
	include('reviewAjaxView.php');
?>


<div id="content">
	<div class="h_area2">
		<h2>��ǰ�ı�</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>

	<?
		$prInfoSQL = "SELECT p.productname, p.maximage, p.sellprice, p.consumerprice FROM tblproduct as p LEFT JOIN tblcategorycode as c ON(p.productcode = c.productcode) WHERE ";
		$prInfoSQL .="p.productcode = '".$_code."' ";

		if(false !== $prInfoRes = mysql_query($prInfoSQL,get_db_conn())){
			$prInfoNum = mysql_num_rows($prInfoRes);
			$imgfile = mysql_result($prInfoRes,0,1);
			$prname = mysql_result($prInfoRes,0,0);
			$sellprice = mysql_result($prInfoRes,0,2);
			$consumerprice = mysql_result($prInfoRes,0,3);
			if($prInfoNum > 0 ){
	?>
		<a href="productdetail_tab01.php?productcode=<?=$_code?>">
		<div class="img_container">
			<div class="img_box"><img src="<?=_getMobileThumbnail($origloc,$saveloc,$imgfile,140,140,$quality)?>" width="80" /></div>
			<div class="img_contents">
				<b><?=$prname?></b><br />
				�ǸŰ� : <span class="sellprice"><?=number_format($sellprice);?>��</span><br />
				���߰� : <strike><?=number_format($consumerprice);?>��</strike>
			</div>
		</div>
		</a>
	<?
			}else{
	?>
		<div>��ǰ�� �������� �ʰų�, ������ �����Ǿ����ϴ�.</div>
	<?
			}
		}else{
	?>
		<div>
			������ �����Ǿ����ϴ�.<br/>
			����� �ٽ� �õ� ���ֽñ� �ٶ��ϴ�.
		</div>
	<?
		}
	?>

	<div class="review_list_wrap">
		<div class="div_reviewtypebtn_wrap">
			<button class="btn_reviewtype button small <?=$allclass?>" onClick="typeChange('all');">��ü(<?=$counttotal?>)</button><button class="btn_reviewtype button small <?=$photoclass?>" onClick="typeChange('photo');">����(<?=$countphoto?>)</button><button class="btn_reviewtype button small <?=$basicclass?>" onClick="typeChange('basic');">�Ϲ�(<?=$countbasic?>)</button><button  class="btn_reviewtype button small <?=$bestclass?>" onClick="typeChange('best');">����Ʈ(<?=$countbest?>)</button>
		</div>


		<div class="review_list">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="reviewTbl">
			<?
				$totalSQL = "SELECT * FROM tblproductreview WHERE productcode='".$_code."' ";
				$totalSQL .= $tearmsSQL;
				if($_data->review_type=="A") $totalSQL.= "AND display='Y' ";
				$totalSQL .= "ORDER BY date DESC";

				if(false !== $totalRes = mysql_query($totalSQL,get_db_conn())){
					$totalNumRow = mysql_num_rows($totalRes);
					if($totalNumRow > 0){
						$reviewSQL = "SELECT * FROM tblproductreview WHERE productcode='".$_code."' ";
						$reviewSQL .= $tearmsSQL;
						if($_data->review_type=="A") $reviewSQL.= "AND display='Y' ";
						$reviewSQL .= "ORDER BY date DESC LIMIT ".($listitem * ($curpage - 1))." , ".$listitem;
						if(false !== $reviewRes = mysql_query($reviewSQL,get_db_conn())){

							while($reviewRow = mysql_fetch_assoc($reviewRes)){
								$attechfile=$contents=$num=$averstarcount=$writer=$viewstar=$regdate=$src=$imagearea=$size=$viewtype="";
								$attechfile = $reviewRow['img'];
								$contents = explode("=",$reviewRow['content']);
								$num = $reviewRow['num'];
								$averstarcount = $reviewRow['marks'];
								$writer = $reviewRow['name'];
								$regdate = substr($reviewRow['date'],0,4)."-".substr($reviewRow['date'],4,2)."-".substr($reviewRow['date'],6,2);
								$src = $imgreview.$attechfile;
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

								if( $reviewRow['best'] == "Y"){
									$viewtype ="<img src=\"skin/default/img/icon_best.png\" alt=\"\" /> ";
								}
			?>
				<tr onclick="reviewOpen('<?=$_code?>','<?=$num?>', event);">
					<td width="90" align="center">
						<!-- <a class="review_list_link" href="productdetail_tab03_view.php?productcode=<?=$_code?>&sort=<?=$sort?>&num=<?=$num?>" rel="external"> -->
						<?=$imagearea?>
						<!-- </a> -->
					</td>
					<td>
						<!-- <a class="review_list_link" href="productdetail_tab03_view.php?productcode=<?=$_code?>&sort=<?=$sort?>&num=<?=$num?>" rel="external"> -->
							<p><?=(strlen($attechfile)>0)?$viewstar:""?></p>
							<p><?=$contents[0]?> <?=$viewtype?></p>
							<p class="writeinfo"><?=$writer?> / <?=$regdate?></p>
						<!-- </a> -->
					</td>
				</tr>
			<?				}
						}
					}else{
			?>
				<tr><td>��ϵ� ���� �����ϴ�.</td></tr>
			<?
					}
				}else{
			?>
				<tr><td>������ �߻��Ͽ� ǥ���� �� �����ϴ�.</td></tr>
			<?
				}
			?>
			</table>
		</div>
	</div>

	<div id="page_wrap">
			<?
				$pageLink = $_SERVER['PHP_SELF']."?page=%u&prcode=".$_code; // ��ũ
				if(strlen($_review)>0)$pageLink .= "&review=".$_review;
				$pagePerBlock = ceil($totalNumRow/$listitem);
				$paging = new pages($pageparam);
				$paging->_init(array('page'=>$curpage,'total_page'=>$pagePerBlock,'links'=>$pageLink,'pageblocks'=>3))->_solv();
				echo $paging->_result('fulltext');
			?>
	</div>
</div>

<form name="typeForm" action="<?=$_SERVER['PHP_SELF']?>" method="GET">
	<input type="hidden" name="prcode" value="<?=$_code?>"/>
	<input type="hidden" name="review" value="" />
</form>

<script>
	function typeChange(mode){
		if(mode == 'undefined' || mode == null || mode == ""){
			alert("����Ʈ�� ���õ��� �ʾҽ��ϴ�.");
			return;
		}else{
			var _form = document.typeForm;

			if(_form.review.value=mode){
				_form.submit();
				return;
			}else{
				alert("�ʼ����� ���޵��� �ʾҽ��ϴ�.");
				return;
			}
		}
	}
</script>
<?
	include_once("./footer.php");
?>

