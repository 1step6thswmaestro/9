<?
//���� ��ȹ��ǰ
$imagesrc = $Dir."data/shopimages/product/";

$mainPRSQL = "SELECT * ";
$mainPRSQL .= "FROM tblmobileplanningmain ";
$mainPRSQL .= "WHERE display = 'Y' ";
$mainPRSQL .= "ORDER BY pm_idx ASC ";
$webzinepage=0;
$origloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/product/"; // �������� ���
$saveloc = $_SERVER['DOCUMENT_ROOT']."/data/shopimages/mobile/"; // �泻�� ���� ���
$quality = 100;
$savewideloc = $Dir.DataDir."shopimages/wideimage/";
if(false !== $mainPRRes = mysql_query($mainPRSQL,get_db_conn())){
	$rowcount = mysql_num_rows($mainPRRes);
	if($rowcount>0){
		$origPRList=array();
		$mainPRList=$title=$dplimitcount=$limitcount=$dpPRcount=$maximage=$src=$productcode=	$sellprice=$vendername=$venderidx=$wideimage="";
		$banner = 1;
		$prSQL = "SELECT p.productcode, p.productname, p.sellprice, p.consumerprice, p.discountRate, p.brand, p.prmsg, p.maximage,p.wideimage, p.vender, p.reservation, v.com_name "; 
		$prSQL .= "FROM tblproduct AS p LEFT OUTER JOIN tblvenderinfo AS v ";
		$prSQL .= "ON(p.vender = v.vender) ";
		$prSQL .= "WHERE (p.pridx != '' OR p.pridx IS NOT NULL) ";
		
		while($mainPRRow = mysql_fetch_assoc($mainPRRes)){

			$origPRList = $mainPRRow['product_list']; //���Ǻ� ������ ��ǰ ����Ʈ
			$mainPRList = explode(",",$origPRList);
			//$dpPRcount = count($mainPRList); // ������ ��ǰ ��
			//print_r($mainPRList);
			$title = $mainPRRow['title']; // ���Ǹ� �⺻ HIT PRODUCT, MD PRODUCT, NEW PRODUCT, SPECIAL PRODUCT
			$dplimitcount = $mainPRRow['product_cnt']; //���÷��� ���� ī����
			$realprSQL = "SELECT productcode FROM tblproduct WHERE productcode IN ('".implode("','",$mainPRList)."') AND display = 'Y' AND mobile_display = 'Y' ORDER BY FIELD(productcode,'".implode("','",$mainPRList)."') ";
	
			if(false !== $realprRes = mysql_query($realprSQL,get_db_conn())){
				$realprList = array();
				while($realrow = mysql_fetch_assoc($realprRes)){
					array_push($realprList,$realrow['productcode']);
				}
				@mysql_free_result($realprRes);
			}
			$dpPRcount = count($realprList); // ���������� ��ǰ ��
			if($dpPRcount>=$dplimitcount){
				$limitcount = $dplimitcount;
			}else{
				$limitcount = $dpPRcount;
			}
			switch($mainPRRow['display_type']){
				case "gallery": //������ Ÿ��
?>
				<section>
					<div class="h_area"><h3><?=$title?></h3></div>
<?
					if(!empty($origPRList)){
?>
					<div id='slider<?=$banner?>' align="center" class='swipe div_main_gallery_container'>
						<ul>
<?
						if($dpPRcount >0){
							for($i=0;$i<$limitcount;$i++){
								$gallerySQL = $prSQL."AND p.productcode = '".$realprList[$i]."' ";
								if(false !== $galleryRes = mysql_query($gallerySQL, get_db_conn())){
									$galleryRow = mysql_fetch_assoc($galleryRes);
									$maximage=$galleryRow['maximage'];
									$productcode = $galleryRow['productcode'];
									$productname = _strCut($galleryRow['productname'],11,2,$charset);
									$sellprice = number_format($galleryRow['sellprice']);
									$vendername = $galleryRow['com_name'];
									$venderidx = $galleryRow['vender'];
									$reservation = $galleryRow['reservation'];
									if(strlen($reservation)>0 && $reservation != "0000-00-00"){
										$msgreservation = "<font color=\"#2F9D27\"><b>[�����ǰ]</b></font>";
										$datareservation = $reservation;
									}else{
										$msgreservation = $datareservation = "";
									}
									if($i % 4 ==0){
?>
							<li style='display:block;' class="li_main_gallery_list">
<?
									}
?>								
								<div class="div_main_gallery_wrap" style="padding-bottom:10px">
									<a href="productdetail_tab01.php?productcode=<?=$productcode?>" rel="external">
										<table cellpadding="0" cellspacing="0" class="td_main_gallery_contents">
											<tbody>
												<tr>
													<td class="td_gallery_image_wrap">
														<img src="<?=_getMobileThumbnail($origloc,$saveloc,$maximage,140,140,$quality)?>" alt="��ǰ�� �̹���" class="pr_pt">
													</td>
												</tr>
												<tr>
													<td class="td_gallery_info_wrap">
														<p class="p_productname"><?=$productname?></strong></p>
														<p class="p_sellprice"><em class="pr_price"><?=$sellprice?></em>��</p>
														<p><?=$msgreservation?> <?=$datareservation?></p>
													</td>
												</tr>
											<tbody>
										</table>
									</a>
								</div>
<?
									if( ($i % 4 == 3 ) || ($i == $limitcount-1)) {
?>
							</li>	
<?
									}
								}
								mysql_free_result($galleryRes);
							}
						}else{
?>
							<li style="text-align:center;padding:5px 0px;"><?=$title?>�� ������ ��ǰ�� �����ϴ�.</li>
<?
						}
?>
						</ul>
					</div>
					<div class="gallery_btn_wrap">
						<ul class="gallery_btn_ul">
							<li><div><a href='#' onclick='slider<?=$banner?>.prev();return false;'><span>&lt; ����</span></a></div></li>
							<li><div><a href='#' onclick='slider<?=$banner?>.next();return false;'><span>���� &gt;</span></a></div></li>
						</ul>
					</div>
<?
	
					}else{
?>
					<div style="text-align:center;padding:5px 0px;">
						<?=$title?>�� ������ ��ǰ�� �����ϴ�.
					</div>
<?
					}
?>
				</section>
<?
					$banner++;
				break;

				case "webzine": // ���� Ÿ��
					$pagecount = ceil($limitcount / 3);
?>
					<section id="webzine">
						<div class="h_area"><h3><?=$title?></h3></div>
<?
						if(!empty($realprList)){
							for($i=0;$i<$pagecount;$i++){
								$startnum = $i * 3;
?>
							<ul class="ul_main_webzine_wrap" id="<?=$webzinepage?>_main_product_<?=$i?>" <? if($i!=0) {echo "style=\"display:none\"";}?>>
<?
								for($j=$startnum;$j <$startnum + 3;$j++){
									if($realprList[$j]=="") {	continue;	}
										$webzineSQL = $prSQL."AND p.productcode = '".$realprList[$j]."' ";
										
										if(false !== $webzineRes = mysql_query($webzineSQL,get_db_conn())){
											$webzineRow = mysql_fetch_assoc($webzineRes);
											$maximage=$webzineRow['maximage'];
											$productcode=$webzineRow['productcode'];
											$productname=$webzineRow['productname'];
											$sellprice=number_format($webzineRow['sellprice']);
											$vendername=$webzineRow['com_name'];
											$venderidx = $webzineRow['vender'];
											$reservation = $webzineRow['reservation'];
											if(strlen($reservation)>0 && $reservation != "0000-00-00"){
												$msgreservation = "<font color=\"#2F9D27\"><b>[�����ǰ]</b></font>";
												$datareservation = $reservation;
											}else{
												$msgreservation = $datareservation = "";
											}
?>							
								<li>
									<a href="productdetail_tab01.php?productcode=<?=$productcode?>" rel="external">
										<table cellpadding="0" cellspacing="0" width="100%" border="0" class="tb_main_webzine_contents">
											<tr>
												<td class="td_webzine_image_wrap">
													<img src="<?=_getMobileThumbnail($origloc,$saveloc,$maximage,140,140,$quality)?>" alt="��ǰ�� �̹���" class="pr_pt">
												</td>
												<td class="td_webzine_info_wrap">
													<p class="p_productname"><?=$productname?></p>
													<p class="p_sellprice"><em><?=$sellprice?></em>��</p>
													<p><?=$msgreservation?></p>
													<p><?=$datareservation?></p>
<?
													if(strlen($vendername) > 0){
?>
													<p class="p_vendername"><a href="javascript:venderInfo('<?=$venderidx?>');"><span><?=$vendername?></span></a></p>
<?
													}
?>
												</td>
											</tr>
										</table>
									</a>
								</li>
<?
											if($j>=$limitcount-1){
												break;
											}
										}
									}
?>
							</ul>
<?
									mysql_free_result($webzineRes);
								}

?>

						<?	for($i=0;$i<$pagecount;$i++)	{ ?>
						<div class="div_main_webzine_page_container" id="<?=$webzinepage?>_page_<?=$i?>" <? if($i!=0) {echo "style=\"display:none\"";}?>>
							<span class="span_main_webzine_wrap">
							<? for($k = 1;$k<=$pagecount;$k++) {?>
								<? if(($k-1)==$i) { ?>
								<a href="javascript:displayUL('<?=$webzinepage?>','<?=$pagecount?>','<?=$k?>')" class="page currentpage" rel="external"><?=$k?></a>
								<? }  else { ?>
								<a href="javascript:displayUL('<?=$webzinepage?>','<?=$pagecount?>','<?=$k?>')" class="page" rel="external"><?=$k?></a>
								<? } ?>
							<? }?>
							</span>
						</div>
						<? } ?>
<?
							}else{
?>
							<div style="text-align:center;padding:5px 0px;">
								<?=$title?>�� ������ ��ǰ�� �����ϴ�.
							</div>
<?
							}
					$webzinepage++;
?>
					</section>
<?
				break;

				case "list": //����Ʈ Ÿ��
?>
					<section class="main_prlist_list">
						<div class="h_area"><h3><?=$title?></h3></div>
<?
						if(!empty($realprList)){
?>
					<ul class="ul_main_list_wrap">
<?
						for($i=0;$i<$limitcount;$i++){
							$listSQL = $prSQL."AND p.productcode = '".$realprList[$i]."' ";
							if(false !== $listRes = mysql_query($listSQL, get_db_conn())){
								$listRow = mysql_fetch_assoc($listRes);
								$maximage=$listRow['maximage'];
								$wideimage=$listRow['wideimage'];
								$productcode = $listRow['productcode'];
								
								$productname = _strCut($listRow['productname'],24,4,$charset);
								$prmsg = $listRow['prmsg'];
								$consumerprice = number_format($listRow['consumerprice']);
								$sellprice = number_format($listRow['sellprice']);
								$discountRate = number_format($listRow['discountRate']);
								$vendername = $listRow['com_name'];
								$venderidx = $listRow['vender'];

								$reservation = $listRow['reservation'];
								if(strlen($reservation)>0 && $reservation != "0000-00-00"){
									$msgreservation = "<font color=\"#2F9D27\"><b>[�����ǰ]</b></font>";
									$datareservation = $reservation;
								}else{
									$msgreservation = $datareservation = "";
								}
								$src="";
								if(is_file($savewideloc.$wideimage)>0){
									$src = $savewideloc.$wideimage;
									$size = "100%";
								}else{
									$src="/images/no_img.gif";
									$size = "";
								}
?>
						<li>
							<a href="productdetail_tab01.php?productcode=<?=$productcode?>" rel="external">
							<table cellpadding="0" cellspacing="0" width="100%" border="0" class="tb_main_list_contents">
								<tr>
									<td colspan="2" class="td_list_image_wrap"><img src="<?=$src?>" width="<?=$size?>" style="border:0px;" /></td>
								</tr>
								<tr>
									<td colspan="2" class="td_list_info_wrap">
										<p class="p_productname"><?=$productname?></p>
										<p class="p_prmsg"><?=$prmsg?></p>
									</td>
								</tr>
								<tr>
									<td class="td_list_price_wrap">
										<? if($discountRate > 0){ ?><span class="p_discountrate"><?=$discountRate?>%</span><? } ?>
										<span class="p_sellprice"><?=$sellprice?>��</span>
										<? if($consumerprice > 0){ ?><span class="p_consumerprice"><strike><?=$consumerprice?>��</strike></span><? } ?>
										<?=$msgreservation?> <?=$datareservation?>
									</td>
									<td class="td_list_vender_wrap">
<? 
										if(strlen($vendername) > 0){ 
?>
											<p class="p_vendername"><a href="javascript:venderInfo('<?=$venderidx?>');"><?=$vendername?></span></a></p>
<? 
										} 
?>
									</td>
								<tr>
							</table>
							</a>
						</li>
<?	
								}
}
?>
					</ul>
				</section>
<?
						}else{
?>
					<div style="text-align:center;padding:5px 0px;">
					<?=$title?>�� ������ ��ǰ�� �����ϴ�.
					</div>
<?
						}
				break;
			}
		}
	}else{
?>
	<div style="text-align:center;padding:5px 0px;">
		���������� ���� ������ �����ϴ�.
	</div>
<?

	}
	mysql_free_result($mainPRRes);
}
?>
<script src='./js/swipe.js'></script>

<script>
	var loopcount =document.getElementsByClassName('swipe').length;
	
	for(i=1;i<=loopcount;i++){
		document["slider"+i] = new Swipe(document.getElementById('slider'+i));
	}

	//var slider1 = new Swipe(document.getElementById('slider1'));
	//var slider2 = new Swipe(document.getElementById('slider2'));
	//var slider3 = new Swipe(document.getElementById('slider3'));
	//var slider4 = new Swipe(document.getElementById('slider4'));
</script>
<script>
function displayUL(target,page_cnt,k)
{
	kk = k -1;
	for(i=0;i<page_cnt;i++)
	{
		document.getElementById(target+"_main_product_"+i).style.display = 'none';
		document.getElementById(target+"_page_"+i).style.display = 'none';
	}
	document.getElementById(target+"_main_product_"+kk).style.display = '';
	document.getElementById(target+"_page_"+kk).style.display = '';
}
</script>

