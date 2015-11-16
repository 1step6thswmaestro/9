<?
//상품마지막 카테고리 구하기
$code=$_REQUEST["code"];

$_GET[codeA]=substr($code,0,3);
$_GET[codeB]=substr($code,3,3);
$_GET[codeC]=substr($code,6,3);
$_GET[codeD]=substr($code,9,3);

$strapline = _currentCategoryName($code);
?>
<div id="content">
	<?// include_once("strapline.php"); ?>
	<div class="pr_current">
		<?=_getCategoryList($code)?>
	</div>
	<!-- 상품리스트 상단 -->
	<div class="pr_navi">
		<select class="basic_select" onChange="ChangeSort(this.value)">
			<option value="">최근등록순</option>
			<option value="price_desc" <?if($_GET[sort]=="price_desc") {echo "selected";}?>>높은가격순</option>
			<option value="price" <?if($_GET[sort]=="price") {echo "selected";}?>>낮은가격순</option>
			<option value="name" <?if($_GET[sort]=="name") {echo "selected";}?>>상품명 순</option>
			<option value="name_desc" <?if($_GET[sort]=="name_desc") {echo "selected";}?>>상품명 역순</option>
			<option value="reserve_desc" <?if($_GET[sort]=="reserve_desc") {echo "selected";}?>>적립금 높은순</option>
			<option value="reserve" <?if($_GET[sort]=="reserve") {echo "selected";}?>>적립금 낮은순</option>
			<option value="production_desc" <?if($_GET[sort]=="production_desc") {echo "selected";}?>>제조사 이름순</option>
			<option value="production" <?if($_GET[sort]=="production") {echo "selected";}?>>제조사 이름역순</option>
		</select>
		
		<div class="btn_display_wrap">
			<button type="button" class="btn_display_gallery <?=$displaygallery?>" onClick="changeDisplayMode('gallery','<?=$code?>')" ><span class="vc">갤러리형</span></button>
			<button type="button" class="btn_display_webzine <?=$displaywebzine?>" onClick="changeDisplayMode('webzine','<?=$code?>')"><span class="vc">웹진형</span></button>
			<!-- <button type="button" class="btn_display_list <?=$displaylist?>" onClick="changeDisplayMode('list','<?=$code?>')"><span class="vc">리스트형</span></button> -->
		</div>
	</div>
	<!-- //상품리스트 상단 -->
	<!-- 카테고리 리스트 -->
	<div>
		<?include_once('./prcategory.php');?>
	</div>
	<!-- //카테고리 리스트 -->
<!-- 상품목록 시작 -->
<?
	$sql = "SELECT COUNT(distinct a.productcode) as t_count ";
	$sql.= "FROM tblproduct AS a left join tblcategorycode as cc on cc.productcode = a.productcode ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= $qry." ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	if(strlen($not_qry)>0) {
		$sql.= $not_qry." ";
	}

	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$rowcount = (int)$row->t_count;
	mysql_free_result($result);

	$tmp_sort=explode("_",$sort);
	if($tmp_sort[0]=="reserve") {
		$addsortsql=",IF(a.reservetype='N',a.reserve*1,a.reserve*a.sellprice*0.01) AS reservesort ";
	}
	$sql = "SELECT distinct a.productcode,a.productname,a.sellprice,a.quantity,a.consumerprice,a.reserve,a.reservetype,a.production, ";
	if($_cdata->sort=="date2") $sql.="IF(a.quantity<=0,'11111111111111',a.date) as date, ";
	$sql.= "a.tag, a.tinyimage, a.maximage, a.etctype, a.option_price, a.madein, a.model, a.brand, a.selfcode,a.prmsg ";
	$sql.= $addsortsql;

	$sql.= ", v.com_name, a.vender, a.reservation ";
	$sql.= "FROM tblproduct AS a LEFT JOIN tblcategorycode as cc on cc.productcode = a.productcode ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "LEFT OUTER JOIN tblvenderinfo AS v ON(a.vender = v.vender) "; 

	$sql.= $qry." ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	if(strlen($not_qry)>0) {
		$sql.= $not_qry." ";
	}
	if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
	else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
	else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
	else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
	else {
		if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
			if(eregi("T",$_cdata->type) && strlen($t_prcode)>0) {
				$sql.= "ORDER BY FIELD(a.productcode,'".$t_prcode."'),a.date DESC ";
			} else {
				$sql.= "ORDER BY a.date DESC ";
			}
		} else if($_cdata->sort=="productname") {
			$sql.= "ORDER BY a.productname ";
		} else if($_cdata->sort=="production") {
			$sql.= "ORDER BY a.production ";
		} else if($_cdata->sort=="price") {
			$sql.= "ORDER BY a.sellprice ";
		}
	}

	$pagePerBlock = 5; // 블록 갯수

?>
	<?// if($_SESSION[list_type]=="" || $_SESSION[list_type]=="gallery") { ?>
	<?
	switch($displaymode){
		case "gallery":
	?>
	<!-- 상품리스트-타입1 -->
	<div class="">
		<ul class="pr_list pr_type1">		
		<?
		//번호, 사진, 상품명, 제조사, 가격
		//리스트 세팅
		$itemcount = 12; // 페이지당 게시글 리스트 수 
		//$pagePerBlock = 5; // 블록 갯수
		
		$sql.= "LIMIT " . ($itemcount * ($currentPage - 1)) . ", " . $itemcount;

		if(false !== $gelleryRes = mysql_query($sql,get_db_conn())){
			$gelleryNumRows = mysql_num_rows($gelleryRes);
			
			if($gelleryNumRows > 0){
				while($gelleryRow = mysql_fetch_assoc($gelleryRes)){
					$maximage = $gelleryRow['maximage'];
					$wholeSaleIcon = ( $gelleryRow['isdiscountprice'] == 1 ) ? '<img src="/images/common/wholeSaleIcon.gif"/>':"";
					$memberpriceValue = $gelleryRow['sellprice'];
					$strikeStart = $strikeEnd = '';
					$memberprice = 0;
					$reservation = $gelleryRow['reservation'];

					if(strlen($reservation)>0 &&$reservation != "0000-00-00"){
						$msgreservation = "<font color=\"#2F9D27\"><b>[예약상품]</b></font><br/>";
						$datareservation = "발송예정일 : ".$reservation;
					}else{
						$msgreservation = $datareservation = "";
					}
					if($gelleryRow['discountprices']>0 AND isSeller() != 'Y' ){
						$memberprice = $gelleryRow['sellprice'] - $gelleryRow['discountprices'];
						$strikeStart = "<strike>";
						$strikeEnd = "</strike>";
						$memberpriceValue = ($gelleryRow['sellprice'] - $gelleryRow['discountprices']);
					}
		?>
			<li>
				<a href="productdetail_tab01.php?productcode=<?=$gelleryRow['productcode']?><?=$add_query?>&sort=<?=$sort?>" rel="external">
					<div class="wrap_img">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td class="pr_img_box"><img src="<?=_getMobileThumbnail($origloc,$saveloc,$maximage,140,140,$quality)?>" alt="상품명 이미지" class="pr_pt" /></td>
							</tr>
							<tr>
								<td class="pr_txt">
									<strong class="pr_name"><?=cutStr($gelleryRow['productname'],14)?></strong>
									<?if($gelleryRow['consumerprice'] > 0){?>
										<em class="pr_price2"><?=number_format($gelleryRow['consumerprice'])?></em>
									<?}?>
									<em class="pr_price">
									<?if( $memberprice > 0 ) {?>
										<img src="/images/common/memsale_icon.gif"/><?=dickerview($gelleryRow['etctype'],number_format($memberprice)."원")?>
									<?
										}else{
											if($dicker=dickerview($gelleryRow['etctype'],$wholeSaleIcon.number_format($gelleryRow['sellprice'])."원",1)) {
												echo $dicker;
											} else if(strlen($_data->proption_price)==0) {
												echo "<img src=\"/images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">".$wholeSaleIcon.number_format($gelleryRow['sellprice'])."원";
												//if (strlen($gelleryRow['option_price'])!=0) echo "(기본가)";
											} else {
												echo "<img src=\"/images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
												if (strlen($gelleryRow['option_price'])==0) echo $wholeSaleIcon.number_format($gelleryRow['sellprice'])."원";
												else echo ereg_replace("\[PRICE\]",number_format($gelleryRow['sellprice']),$_data->proption_price);
											}
										}
										if ($gelleryRow['quantity']=="0") echo soldout();
									?>
									</em>
									
								</td>
							</tr>
							<?if(strlen($msgreservation)>0){?>
							<tr>
								<td>
								<?=$msgreservation?><?=$datareservation?>
								</td>
							</tr>
							<?}?>
						</table>
					</div>
				</a>
		<?
					if($memberprice>0){
						$gelleryRow['sellprice'] = ( $memberprice > 0 ) ? $memberprice : $_pdata->sellprice;
					}
					$reserveconv=getReserveConversion($gelleryRow['reserve'],$gelleryRow['reservetype'],$gelleryRow['sellprice'],"Y");
					if($reserveconv>0) {
						echo "<img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."원</td>\n";
					}

		?>
				<?if(strlen($gelleryRow['com_name'])){?>
					<div>
						<a href="javascript:venderInfo('<?=$gelleryRow['vender']?>');" rel="external"><?=$gelleryRow['com_name']?></a>
					</div>
				<?}?>
			</li>
		<?
				}
			}else{
		?>
			<li style="margin:0px;padding:0px;text-align:center;width:100%;">
				진열된 상품이 없습니다.
			</li>
		<?
			}
			mysql_free_result($gelleryRes);
		}else{
		?>
			<li style="margin:0px;padding:0px;text-align:center;width:100%;">
				연결이 지연되었습니다 다시 시도해주세요.
			</li>
		<?
		}
	?>
		</ul>
	</div>
	<!-- //상품리스트-타입1 -->
	<?
		break;
		case "webzine":
	?>

	<!-- 상품리스트-타입2 -->
	<div class="">
		<ul class="">
		<?
			$itemcount = 12; // 페이지당 게시글 리스트 수 
			$sql.= "LIMIT " . ($itemcount * ($currentPage - 1)) . ", " . $itemcount;
			if(false !== $listRes = mysql_query($sql,get_db_conn())){
				$listNumRows = mysql_num_rows($listRes);
				if($listNumRows > 0){
					while($listRow = mysql_fetch_assoc($listRes)){
						$maximage=$listRow['maximage'];
						$wholeSaleIcon = ( $listRow['isdiscountprice'] == 1 ) ? '<img src="/images/common/wholeSaleIcon.gif"/>':"";
						$memberpriceValue = $listRow['sellprice'];
						$strikeStart = $strikeEnd = '';
						$memberprice = 0;
						$reservation = $listRow['reservation'];
						if(strlen($reservation)>0 && $reservation != "0000-00-00"){
							$msgreservation = "<font color=\"#2F9D27\"><b>[예약상품]</b></font>";
							$datareservation = $reservation;
						}else{
							$msgreservation = $datareservation = "";
						}
						if($listRow['discountprices']>0 AND isSeller() != 'Y' ){
							$memberprice = $listRow['sellprice'] - $listRow['discountprices'];
							$strikeStart = "<strike>";
							$strikeEnd = "</strike>";
							$memberpriceValue = ($listRow['sellprice'] - $listRow['discountprices']);
						}
		?>
			<li class="pr_type_list_wrap">
				<a href="productdetail_tab01.php?productcode=<?=$listRow['productcode']?><?=$add_query?>&sort=<?=$sort?>" rel="external">
				<table cellpadding="0" cellspacing="0" width="100%" class="pr_type_list_table">
					<tr>
						<td class="typelist_image_wrap">
							<div class="typelist_image_div">
								<img src="<?=_getMobileThumbnail($origloc,$saveloc,$maximage,140,140,$quality)?>" alt="상품명 이미지" class="pr_pt" />
							</div>
						</td>
						<td class="typelist_text_wrap">
							<div class="pr_txt">
								<strong class="pr_name"><?=cutStr($listRow['productname'],26)?></strong>
								<?if($listRow['consumerprice'] > 0){?>
									<br/><em class="pr_consumer_price"><?=number_format($listRow['consumerprice'])?>원</em><em class="pr_price">
								<?}?>
								<?
									if( $memberprice > 0 ) {
										echo '<br/><img src="/images/common/memsale_icon.gif"/>'.dickerview($listRow['etctype'],number_format($memberprice)."원");
									}else{
										if($dicker=dickerview($listRow['etctype'],$wholeSaleIcon.number_format($listRow['sellprice'])."원",1)) {
											echo $dicker;
										} else if(strlen($_data->proption_price)==0) {
											echo "<br/>".$wholeSaleIcon.number_format($listRow['sellprice'])."원";
										} else {
											echo "<img src=\"/images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
											if (strlen($listRow['option_price'])==0) echo $wholeSaleIcon.number_format($listRow['sellprice'])."원";
											else echo ereg_replace("\[PRICE\]",number_format($listRow['sellprice']),$_data->proption_price);
										}
									}
									if ($listRow['quantity']=="0") echo soldout();
			
									if($memberprice>0){
										$listRow['sellprice'] = ( $memberprice > 0 ) ? $memberprice : $_pdata->sellprice;
									}
									echo "</em>";
									
									$reserveconv=getReserveConversion($listRow['reserve'],$listRow['reservetype'],$listRow['sellprice'],"Y");
									
									if($reserveconv>0) {
									?>
										<br/><img src="/images/common/reserve_icon.gif" style="margin-right:2px;"><?=number_format($reserveconv)?>원
									<?}?>
									<?if(strlen($listRow['com_name'])){?>
										<div>
											<a href="javascript:venderInfo('<?=$listRow['vender']?>');" rel="external"><?=$listRow['com_name']?></a>
										</div>
									<?}?>
									<?=$msgreservation?><?=$datareservation?>
							</div>
						</td>
					</tr>
				</table>
				</a>
			</li>
		<?
					}
				}else{
		?>
			<li>
				진열된 상품이 없습니다.
			</li>
		<?
				}
				mysql_free_result($listRes);
			}else{
		?>
			<li>
				연결이 지연되었습니다 다시 시도해주세요.
			</li>
		<?
			}
		?>
			
		</ul>
	</div>
	<!-- //상품리스트-타입2 -->
	<?
		break;
		}
	?>
	<div id="page_wrap">
			<?
				$listtype = isset($_GET['list_type'])?trim($_GET['list_type']):"gallery";
				$pageLink =$_SERVER['PHP_SELF']."?code=".$code."&list_type=".$listtype."&page=%u";
				$pagePerBlock = ceil($rowcount/$itemcount);
				$paging = new pages($pageparam);
				$paging->_init(array('page'=>$currentPage,'total_page'=>$pagePerBlock,'links'=>$pageLink,'pageblocks'=>3))->_solv();
				echo $paging->_result('fulltext');
			?>
	</div>
</div>

<? 
//include_once('footer.php'); 
?>