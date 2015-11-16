
<style type="text/css">
	.itemListTbl{border-top:1px solid #222222; border-bottom:1px solid #222222; empty-cells:show}
	.itemListTbl thead .thstyle {height:35px; background:#f8f8f8; border:1px solid #e5e5e5; border-left:hidden; }
	.itemListTbl thead .thstyle2 {height:35px; background:#f8f8f8; border:1px solid #e5e5e5; border-left:hidden; border-right:hidden;}

	.itemListTbl tbody .tdstyle {border-right:1px solid #e5e5e5; border-bottom:1px solid #e5e5e5; padding:8px 0px;}
	.itemListTbl tbody .tdstyle2 {border-bottom:1px solid #e5e5e5; padding:5px 10px;}

	.noBodertbl{ border:1px solid #ccc;}
	.noBodertbl tbody .thStyle {color:#000;}
	.noBodertbl tbody th {text-align:right;}
	.noBodertbl tbody td {text-align:right; padding-right:10px;}

	.orderTbl {border:1px solid #bbbbbb;}
	.orderTbl caption {text-align:left; padding:7px 0px;}
	.orderTbl th {width:110px; background:#f8f8f8; border-right:1px solid #eeeeee; border-bottom:1px solid #eeeeee; text-align:left; padding: 5px 0px 5px 15px;}
	.orderTbl td {border-bottom:1px solid #eeeeee; text-align:left; padding: 5px 0px 5px 10px;}
	.orderTbl td.noCont {border:0px; font-size:1px; height:7px; line-height:1px;}
	.orderTbl td.payTbl {width:100%; border:0px;}
	.orderTbl .lastTh {width:110px; border-bottom:none;}
	.orderTbl .lastTd {border-bottom:none;}
	.orderTbl .input {border:1px solid #dddddd; height:22px; line-height:22px;}

	.couponDownArea{ display:block;}

	#addressSelDiv {height:24px; margin-right:10px; margin-bottom:8px; border-bottom:1px solid #ddd;}

	#giftOptionArea table {border:1px solid #fff; margin-top:5px;}
	#giftOptionArea td {border:1px solid #fff; margin-top:5px; font-size:12px; text-align:left;}
</style>

<div style="height:20px; text-align:right; color:#999; font-size:11px; font-family:돋움; letter-spacing:-1px;">주문정보를 입력하신 후, <span style="color:red;">결제버튼</span>을 눌러주세요.</div>


<table border="0" cellpadding="0" cellspacing="0" width="100%" class="itemListTbl">
	<caption style="text-align:left; display:none;">주문서 작성</caption>
	<col width=""></col>
	<col width=""></col>
	<col width="50"></col>
	<col width="80"></col>
	<col width="80"></col>
	<col width="80"></col>
	<col width="80"></col>
	<col width="120"></col>
	<thead>
		<tr>
			<th class="thstyle" colspan="2">상품명/옵션</th>
			<th class="thstyle">수량</th>
			<th class="thstyle">판매가</th>
			<th class="thstyle">적립금</th>
			<th class="thstyle">합계</th>
			<th class="thstyle">배송비</th>
			<th class="thstyle2">쿠폰</th>
		</tr>
	</thead>
	<tbody>
		<?
			$formcount = 0;
			$couponable = false;
			$reserveuseable = false;
			$productRealPrice = 0;
			if($basketItems['productcnt'] <1){
		?>
		<tr>
			<td colspan="8" style="text-align:center; height:30px;">등록된 상품이 없습니다.</td>
		</tr>
		<?
			}else{
					$timgsize = 50;
					foreach($basketItems['vender'] as $vender=>$vendervalue){
						for( $i = 0 ; $i < count($vendervalue['products']) ; $i++ ){
							$product = $vendervalue['products'][$i];

							if(!$couponable && $product['cateAuth']['coupon'] == 'Y'){
								$chkcoupons = array();
								$chkcoupons = getMyCouponList($product['productcode']);
								if(_array($chkcoupons)) {
									$couponable = true;
								}
							}
							if(!$reserveuseable && $product['cateAuth']['reserve'] == 'Y') {
								$reserveuseable = true;
							}

							if($product['tinyimage'][$product['tinyimage']['big']] > $timgsize) {
								$imageSize = $product['tinyimage']['big'].'="'.$timgsize.'"';
							} else{
								$imageSize = "";
							}


							$formcount++;


							//
							$arPresent[$formcount] = $product['present_state'];
							$arPester[$formcount] = $product['pester_state'];
							$sellChk = true;
							if($product['sell_startdate'] && $product['sell_enddate']){
								$sellChk = false;
								if($product['sell_startdate']<time() && time()<$product['sell_enddate']){
									$sellChk = true;
								}
							}



							// 배송비 무료 혜택 정보 출력
							$venderDeliPrint = "";
							$venderDeliPrintCHK = false;
							if( strlen($vendervalue['deli_after']) == 0 AND $vendervalue['conf']['deli_mini'] < 1000000000 AND $vendervalue['delisumprice'] > 0 ) { // 착불이 아닐경우
								$venderDeliPrint .= "<b>무료배송 혜택</b>". ( $vendervalue['conf']['groupDeli'] > 1 ? "(회원등급 배송정책 적용)" : "" );

								if( $vendervalue['delisumprice'] >= $vendervalue['conf']['deli_mini'] ){
									$venderDeliPrint .= "<font color='#ff6600'><strong>[적용됨]</strong></font>";
									$venderDeliPrintCHK = true;
								}

								//$venderDeliPrint .= "&nbsp;:&nbsp;입점사별 구매금액(<b>".number_format($vendervalue['delisumprice'])."원</b>, 개별배송상품 ".( $vendervalue['conf']['deli_pricetype'] == "Y" ? "포함" : "제외" ).")이 <b>".number_format($vendervalue['conf']['deli_mini'])."원</b> 이상일 경우";
								$venderDeliPrint .= "&nbsp;:&nbsp;구매금액이 <b>".number_format($vendervalue['conf']['deli_mini'])."원</b> 이상일 경우 (개별배송상품 ".( $vendervalue['conf']['deli_pricetype'] == "Y" ? "포함" : "제외" ).")";
							}



							// 배송료
							$deliPrtChk="";
							$deliPrtRowspan = "";
							if($product['deli_price']>0){
								if($product['deli']=="Y"){
									$deliPrt = "유료배송<br>(". ( ($product['deli_price']*$product['quantity']) > 0 ? number_format($product['deli_price']*$product['quantity'])."원" : "무료" ) .")";
								}else if($product['deli']=="N") {
									$deliPrt = "유료배송<br />(". ( $product['deli_price'] > 0 ? number_format($product['deli_price'])."원" : "무료" ) .")";
								}
							}else if($product['deli']=="F" || $product['deli']=="G"){
								$deliPrt = ($product['deli']=="F"?'개별무료':'착불');
							}else{
								if($vender == 0) {
									$deliPrt = "기본배송비<br />(". ( $vendervalue['conf']['deli_price'] > 0 && $venderDeliPrintCHK == false ? number_format($vendervalue['conf']['deli_price'])."원" : "무료" ) .")";
									$productRealPrice += $product['realprice'];
								} else {
									//$deliPrt = "입점사<br />기본배송<br />(". ( $vendervalue['conf']['deli_price'] > 0 ? number_format($vendervalue['conf']['deli_price'])."원" : "무료" ) .")";
									$deliPrt = "기본배송<br />(". ( $vendervalue['conf']['deli_price'] > 0 ? number_format($vendervalue['conf']['deli_price'])."원" : "무료" ) .")";
								}
								$deliPrtChk = $vender."D";
							}

							// 배송비 테이블 병합
							if( strlen($deliPrtChk) > 0 ) {
								$deliPrtArr[$deliPrtChk]++;
								if( $deliPrtArr[$deliPrtChk] > 1 ) {
									$deliPrt = "";
								} else{
									$deliCount = $basketItems['vender'][$vender]['deliCount'][$product['deli']][($product['deli_price']>0?"1":"0")];
									if( $deliCount > 1 ) {
										$deliPrtRowspan = " rowspan = '".$deliCount."'";
									}
								}
							}



		?>
		<tr>
			<td class="tdstyle2" style="width:60px; text-align:center"><img src="<?=$product['tinyimage']['src']?>" <? if($product['tinyimage'][$product['tinyimage']['big']] > $timgsize) echo $product['tinyimage']['big'].'="'.$timgsize.'"'; ?> /></td>
			<td class="tdstyle">
				<a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$product['productcode']?>"><font color="#000000" style="font-size:12px;"><b><?=viewproductname($product['productname'],$product['etctype'],$product['selfcode'],$product['addcode'])?></b></font></a>

			<? if(_array($product['option1']) || _array($product['option2']) || !_empty($product['optvalue'])){ ?>
					<br /><IMG border=0 align=absMiddle src="../images/common/basket/001/basket_skin3_icon002.gif"> <?=$product['option1'][$product['opt1_idx']]?> <? if(_array($product['option2'])) echo ' / '.$product['option2'][$product['opt2_idx']];

					if(!_empty($product['optvalue'])) {
						echo $product['optvalue']."\n";
					}
			}
			?>

			<? if($product['bankonly'] == 'Y'){ ?><img src="<?=$Dir?>images/common/bankonly.gif" border=0 align=absmiddle><? } ?>
			<? if($product['setquota'] == 'Y'){ ?><img src="<?=$Dir?>images/common/setquota.gif" border=0 align=absmiddle><? } ?>
			<?
				// 혜택 및 제한 사항
				$sptxt = array();
				if($product['cateAuth']['reserve'] == 'N') array_push($sptxt,'<IMG SRC=\'/images/common/basket/001/basket_spe_icon001x.gif\' hspace=\'1\' alt=\'적립금 사용불가\' />');
				if($product['cateAuth']['coupon'] == 'N') array_push($sptxt,'<IMG SRC=\'/images/common/basket/001/basket_spe_icon002x.gif\' hspace=\'1\' alt=\'할인쿠폰 적용불가\' />');
				if($product['cateAuth']['refund'] == 'N') array_push($sptxt,'<img src=\'/images/common/basket/001/basket_spe_icon003x.gif\' hspace=\'1\' alt=\'교환/반품 불가\' />');
				if($product['cateAuth']['gift'] == 'Y' && checkGiftSet()) array_push($sptxt,'<img src=\'/images/common/basket/001/basket_spe_icon004o.gif\' hspace=\'1\' alt=\'사은품 지급\' />');
				if(_array($sptxt)){
					//echo '<br />'.implode(' / ',$sptxt);
					echo '<div style=\'margin-top:5px; font-size:0px;\'>'.implode('',$sptxt).'</div>';
				}
			?>
			</td>
			<td class="tdstyle" align="center"><?=$product['quantity']?>개</td>
			<td class="tdstyle" align="center"><?=number_format($product['sellprice'])?>원</td>
			<td class="tdstyle" align="center"><?=number_format($product['reserve'])?>원</td>
			<td class="tdstyle" align="center"><b><?=number_format($product['realprice'])?>원</b></td>
			<?
				if( strlen($deliPrt) > 0 ) {
			?>
			<td class="tdstyle" align="center" <?=$deliPrtRowspan?>><?=$deliPrt?></td>
			<?
				}
			?>



			<td class="tdstyle2">
			<?
				$mycoupons = $ablecoupons =array();
				if($_data->coupon_ok=="Y" && $product['cateAuth']['coupon'] == 'Y' && checkGroupUseCoupon()){
					$mycoupons = getMyCouponList($product['productcode']);
					$ablecoupons = ableCouponOnProduct($product['productcode'],$product['vender'],true);
					if(_array($mycoupons)){
						foreach($mycoupons as $abcoupon){
							echo "<span class=\"couponDownArea\">";
							echo "<b>·</b>&nbsp;".number_format(intval($abcoupon['sale_money'])).(($abcoupon['sale_type']<'3')?'%':'원').((intval($abcoupon['sale_type'])%2 == 1)?"적립":"할인");

							if(_array($mycoupon_codes) && in_array($abcoupon['coupon_code'],$mycoupon_codes)){
								echo "<img src=\"".$Dir."images/common/order/".$_data->design_order."/icon_get.gif\" border=\"0\" style=\"position:relative; top:0.2em;\" alt=\"보유중\" /><br />";
							}else{
								echo "<a href=\"javascript:issue_coupon('".$abcoupon['coupon_code']."','".$product['productcode']."');\"><img src=\"".$Dir."images/common/order/".$_data->design_order."/icon_download.gif\" style=\"position:relative; top:0.2em;\" border=\"0\" alt=\"쿠폰다운\" /><br /></a>";
							}
							echo '</span>';
						}
					}

					if(_array($ablecoupons)){
						foreach($ablecoupons as $abcoupon){
							echo '<span class="couponDownArea">';
							echo '<b>·</b>&nbsp;'.number_format(intval($abcoupon['sale_money'])).(($abcoupon['sale_type']<'3')?'%':'원').((intval($abcoupon['sale_type'])%2 == 1)?'적립':'할인');

							if(_array($mycoupon_codes) && in_array($abcoupon['coupon_code'],$mycoupon_codes)){
								echo "<img src=\"".$Dir."images/common/order/".$_data->design_order."/icon_get.gif\" border=\"0\" style=\"position:relative; top:0.2em;\" alt=\"보유중\" /><br />";
							}else{
								echo "<a href=\"javascript:issue_coupon('".$abcoupon['coupon_code']."','".$product['productcode']."');\"><img src=\"".$Dir."images/common/order/".$_data->design_order."/icon_download.gif\" style=\"position:relative; top:0.2em;\" border=\"0\" alt=\"쿠폰다운\" /><br /></a>";
							}
							echo '</span>';
						}
					}
				}

				if(!$ablecoupons){
					echo "&nbsp;";
				}
			?>
			</td>
		</tr>
		<?
			}// end for
		?>
		<tr>
			<td colspan="8" bgcolor="#f9f9f9" style="padding:15px 10px; text-align:right;">
				<div style="font-size:11px; margin-bottom:5px;">
				<?=$venderDeliPrint?>
				</div>
				배송비 : <b><?=number_format($vendervalue['deliprice'])?></b>원 / <b>합계 : </b><span style="color:#ff6600; font-size:15px; font-family:tahoma; font-weight:bold;"><?=number_format($vendervalue['sumprice'])?>원</span>
			</td>
		</tr>
		<tr><td colspan=8 height=1 bgcolor="#DDDDDD"></td></tr>
	<?
				} // end foreach
			} // end if
	?>
	</tbody>
	<tfoot>
		<tr><td colspan=8 height=2 bgcolor="#DDDDDD"></td></tr>
		<tr>
			<td colspan="3" bgcolor="#eeeeee" style="padding-left:20px;">
				<? if( $ordertype == "" ) { ?><a href="/front/basket.php?ordertype=<?=$ordertype?>"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/btn_basket.gif" border="0" alt="장바구니 돌아가기" /></a><? } ?>
			</td>
			<td colspan="5" bgcolor="#eeeeee" style="padding:10px 0px;">
				<div style="float:right;">
					<div style="font-size:11px; color:#777777; text-align:right; letter-spacing:-0.5pt; margin-bottom:5px; padding-right:10px;"><?=$groupMemberSale?></div>
					<table border="0" cellpadding="0" cellspacing="0" align="right">
						<tr>
							<td><!--<img src="<?=$Dir?>images/common/basket/<?=$_data->design_basket?>/basket_skin3_point.gif" alt="적립금" />-->적립금 : <span class="basket_etc_price"><?=number_format($basketItems['reserve'])?></span><b>원</b></td>
							<td width="20"></td>

							<?
								// !--배송비 합계
								if($basketItems['deli_price']>0){
							?>
							<td><!--<img src="<?=$Dir?>images/common/basket/<?=$_data->design_basket?>/basket_skin3_trans.gif">-->배송비 : <span class="basket_etc_price2"><?=number_format($basketItems['deli_price'])?></span><b>원</b></td>
							<td width="20"></td>
							<?
								}
							?>

							<?
								//VAT 합계금액
								if($_data->ETCTYPE["VATUSE"]=="Y") {
									$sumpricevat = return_vat($basketItems['sumprice']);
							?>
							<td><!--<img src="<?=$Dir?>images/common/basket/<?=$_data->design_basket?>/basket_skin3_vat.gif">-->VAT합계금액 : <span class="basket_etc_price"><?=number_format($sumpricevat)?></span><B>원</B></td>
							<td width="20"></td>
							<?
								}
							?>

							<td><!--<img src="<?=$Dir?>images/common/basket/<?=$_data->design_basket?>/basket_skin3_tprice.gif" alt="총 결제금액" />--><b>총 결제금액 : <span class="basket_etc_price3"><?=number_format($basketItems['sumprice'])?>원</span></b></td>
							<td width="10"></td>
						</tr>
					</table>
				</div>

			</td>
		</tr>
		<tr><td colspan=7 height=1 bgcolor="#DDDDDD"></td></tr>
	</tfoot>
</table>






<!--
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="itemListTbl">
	<caption style="text-align:left; display:none;">주문서 작성</caption>
	<thead>
		<tr>
			<th class="thstyle" colspan="2">상품명/옵션</th>
			<th class="thstyle" style="width:50px;">수량</th>
			<th class="thstyle" style="width:100px;">판매가</th>
			<th class="thstyle" style="width:70px;">적립금</th>
			<th class="thstyle" style="width:100px;">합계</th>
			<th class="thstyle" style="width:70px;">배송비</th>
			<th class="thstyle2" style="width:120px;">쿠폰</th>
		</tr>
	</thead>
	<tbody>
<?
$couponable = false;
$reserveuseable = false;
$productRealPrice = 0;
if($basketItems['productcnt'] <1){ ?>
		<tr>
			<td colspan="8" style="height:30px;">등록된 상품이 없습니다.</td>
		</tr>
<? }else{
		$timgsize = 50;
		foreach($basketItems['vender'] as $vender=>$vendervalue){

			for($i=0;$i<count($vendervalue['products']);$i++){
				$product = $vendervalue['products'][$i];

				if(!$couponable && $product['cateAuth']['coupon'] == 'Y'){
					$chkcoupons = array();
					$chkcoupons = getMyCouponList($product['productcode']);
					if(_array($chkcoupons)) $couponable = true;
				}

				if(!$reserveuseable && $product['cateAuth']['reserve'] == 'Y') $reserveuseable = true;
?>
		<tr>
			<td class="tdstyle2" style="width:60px; text-align:center"><img src="<?=$product['tinyimage']['src']?>" <? if($product['tinyimage'][$product['tinyimage']['big']] > $timgsize) echo $product['tinyimage']['big'].'="'.$timgsize.'"'; ?> /></td>
			<td class="tdstyle">
				<a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$product['productcode']?>"><font color="#000000" style="font-size:12px;"><b><?=viewproductname($product['productname'],$product['etctype'],$product['selfcode'],$product['addcode'])?></b></font></a>

			<? if(_array($product['option1']) || _array($product['option2']) || !_empty($product['optvalue'])){ ?>
					<br /><IMG border=0 align=absMiddle src="../images/common/basket/001/basket_skin3_icon002.gif"> <?=$product['option1'][$product['opt1_idx']]?> <? if(_array($product['option2'])) echo ' / '.$product['option2'][$product['opt2_idx']];

					if(!_empty($product['optvalue'])) {
						echo $product['optvalue']."\n";
					}
			}
			?>

			<? if($product['bankonly'] == 'Y'){ ?><img src="<?=$Dir?>images/common/bankonly.gif" border=0 align=absmiddle><? } ?>
			<? if($product['setquota'] == 'Y'){ ?><img src="<?=$Dir?>images/common/setquota.gif" border=0 align=absmiddle><? } ?>
			<?
				// 혜택 및 제한 사항
				$sptxt = array();
				if($product['cateAuth']['reserve'] == 'N') array_push($sptxt,'<IMG SRC=\'/images/common/basket/001/basket_spe_icon001x.gif\' hspace=\'1\' alt=\'적립금 사용불가\' />');
				if($product['cateAuth']['coupon'] == 'N') array_push($sptxt,'<IMG SRC=\'/images/common/basket/001/basket_spe_icon002x.gif\' hspace=\'1\' alt=\'할인쿠폰 적용불가\' />');
				if($product['cateAuth']['refund'] == 'N') array_push($sptxt,'<img src=\'/images/common/basket/001/basket_spe_icon003x.gif\' hspace=\'1\' alt=\'교환/반품 불가\' />');
				if($product['cateAuth']['gift'] == 'Y') array_push($sptxt,'<img src=\'/images/common/basket/001/basket_spe_icon004o.gif\' hspace=\'1\' alt=\'사은품 지급\' />');
				if(_array($sptxt)){
					//echo '<br />'.implode(' / ',$sptxt);
					echo '<div style=\'margin-top:5px; font-size:0px;\'>'.implode('',$sptxt).'</div>';
				}
			?>
			</td>
			<td class="tdstyle" align="center"><?=$product['quantity']?>개</td>
			<td class="tdstyle" align="center"><?=number_format($product['sellprice'])?>원</td>
			<td class="tdstyle" align="center"><?=number_format($product['reserve'])?>원</td>
			<td class="tdstyle" align="center"><b><?=number_format($product['realprice'])?>원</b></td>
			<td class="tdstyle" align="center" style="font-size:11px; letter-spacing:-1px;">
				<? if($product['deli_price']>0){
					if($product['deli']=="Y"){ ?>유료배송<br><?=number_format($product['deli_price']*$product['quantity'])?>원
			<?		}else if($product['deli']=="N") { ?>유료배송<br /><?=number_format($product['deli_price'])?>원<?		}
				}else if($product['deli']=="F" || $product['deli']=="G"){
					echo ($product['deli']=="F"?'개별무료':'착불');
				}else{
					if($vender > 0) {
						echo '입점사<br />기본배송';
					} else {
						echo '기본배송비';
						$productRealPrice += $product['realprice'];
					}
				}
			?>
			</td>
			<td class="tdstyle2" style="font-size:11px;">
			<?


			$mycoupons = $ablecoupons =array();
			if($_data->coupon_ok=="Y" && $product['cateAuth']['coupon'] == 'Y' && checkGroupUseCoupon()){
				$mycoupons = getMyCouponList($product['productcode']);
				$ablecoupons = ableCouponOnProduct($product['productcode'],$product['vender'],true);
				if(_array($mycoupons)){
					foreach($mycoupons as $abcoupon){
						echo '<span class="couponDownArea">';
						echo '<b>·</b>&nbsp;'.number_format(intval($abcoupon['sale_money'])).(($abcoupon['sale_type']<'3')?'%':'원').((intval($abcoupon['sale_type'])%2 == 1)?'적립':'할인');

						if(_array($mycoupon_codes) && in_array($abcoupon['coupon_code'],$mycoupon_codes)){ ?>
							<img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/icon_get.gif" border="0" style="position:relative; top:0.2em;" alt="보유중" /><br />
			<?			}else{ ?>
						<a href="javascript:issue_coupon('<?=$abcoupon['coupon_code']?>','<?=$product['productcode']?>')"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/icon_download.gif" style="position:relative; top:0.2em;" border="0" alt="쿠폰다운" /><br /></a>
			<?			}
						echo '</span>';
					}
				}

				if(_array($ablecoupons)){
					foreach($ablecoupons as $abcoupon){
						echo '<span class="couponDownArea">';
						echo '<b>·</b>&nbsp;'.number_format(intval($abcoupon['sale_money'])).(($abcoupon['sale_type']<'3')?'%':'원').((intval($abcoupon['sale_type'])%2 == 1)?'적립':'할인');

						if(_array($mycoupon_codes) && in_array($abcoupon['coupon_code'],$mycoupon_codes)){ ?>
							<img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/icon_get.gif" border="0" style="position:relative; top:0.2em;" alt="보유중" /><br />
			<?			}else{ ?>
						<a href="javascript:issue_coupon('<?=$abcoupon['coupon_code']?>','<?=$product['productcode']?>')"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/icon_download.gif" style="position:relative; top:0.2em;" border="0" alt="쿠폰다운" /><br /></a>
			<?			}
						echo '</span>';
					}
				}
			}else{
				echo '&nbsp;';
			}
			?>
			</td>
		</tr>

<?			}// end for
		} // end foreach
	} // end if
?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3" bgcolor="#eeeeee" style="padding-left:20px;"><a href="/front/basket.php"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/btn_basket.gif" border="0" alt="장바구니 돌아가기" /></a></td>
			<td colspan="5" bgcolor="#eeeeee" style="padding:15px 10px;" align="right">
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						< !--
						<th class="thstyle">적립금 :&nbsp;</th>
						<th style="height:20px; line-height:20px; padding-right:20px;"><span style="font-size:24px; font-family:Tahoma;"><?//=number_format($basketItems['reserve'])?></span>원</th>
						<th class="thstyle">배송비 :&nbsp;</th>
						<th style="height:20px; line-height:20px; padding-right:20px;"><span style="font-size:24px; font-family:Tahoma;"><?//=number_format($basketItems['deli_price'])?></span>원</th>
						-- >
						<td valign="bottom"><b>총 주문금액</b> :&nbsp;</td>
						<td style="color:#0097da; height:20px; line-height:20px;"><span style="font-size:20px; font-family:Tahoma; font-weight:bold;"><?=number_format($basketItems['sumprice'])?></span>원&nbsp;</td>
						<td valign="bottom">(배송비 <?=number_format($basketItems['deli_price'])?>원)</td>
					</tr>
				</table>
			</td>
		</tr>
	</tfoot>
</table>
-->




<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top:40px;">
	<caption style="display:none;">주문자 정보 입력</caption>
	<tr>
		<td width="50%" height="100%" valign="top" style="padding-right:15px;">

<!-- 주문자 정보 입력 START -->
<?
$is_sms="N";
$sql = "SELECT * FROM tblsmsinfo WHERE (mem_order='Y' OR mem_delivery='Y') ";
$result=mysql_query($sql,get_db_conn());
if($rows=mysql_num_rows($result)) $is_sms="Y";
mysql_free_result($result);

$memberName = '';
$addrHight = '80';
$payHeight = '70';
if( !_empty($_ShopInfo->getMemid()) ) {
	$memberName = ' readonly';
	$addrHight = '110';
	$payHeight = '144';
}

?>


<table border="0" cellpadding="0" cellspacing="0" width="100%" class="orderTbl">
	<caption><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_title_t04.gif" /></caption>
	<tr>
		<th>주문자이름</th>
		<td><input type="text" name="sender_name" value="<?=$name?>" size="15" maxlength="12" class="input" style="BACKGROUND-COLOR:#F7F7F7;" <?=$memberName?> /></td>
	</tr>
	<tr>
		<th>전화번호</th>
		<td><input type=text name="sender_tel1" value="<?=$home_tel[0] ?>" size="5" maxlength="3" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="sender_tel2" value="<?=$home_tel[1] ?>" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="sender_tel3" value="<?=$home_tel[2] ?>" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
	</tr>
	<tr>
		<th>휴대폰번호</th>
		<td><input type=text name="sender_hp1" value="<?=$mobile[0] ?>" size="5" maxlength="3" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="sender_hp2" value="<?=$mobile[1] ?>" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="sender_hp3" value="<?=$mobile[2] ?>" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
	</tr>
	<tr>
		<th>이메일</th>
		<td><input type=text name="sender_email" value="<?=$email?>" class="input" style="width:96%; BACKGROUND-COLOR:#F7F7F7;"></td>
	</tr>
	<!-- <tr>
		<th class="lastTh">주소</th>
		<td class="lastTd" height="<?=$addrHight?>">
			<input type=text name="spost1" size="3" onclick="this.blur();get_post('s')" value="<?=$home_post1?>" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="spost2" size="3" onclick="this.blur();get_post('s')" value="<?=$home_post2?>" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> <a href="javascript:get_post('s');"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_btn2.gif" border="0" align="absmiddle" /></a><br />
			<input type=text name="saddr1" style="width:96%; BACKGROUND-COLOR:#F7F7F7;" value="<?=$home_addr1?>" class="input" readonly><br />
			<input type=text name="saddr2" style="width:96%; BACKGROUND-COLOR:#F7F7F7;" class="input" value="<?=!_empty($home_addr2)?$home_addr2:'나머지 주소'?>">
		</td>
	</tr> -->
	<tr>
		<th class="lastTh">주소</th>
		<td class="lastTd" height="<?=$addrHight?>">
			<input type=text name="spost1" size="5" onclick="this.blur();get_post('s')" value="<?=$home_post1?>" class="input" style="width:50px;BACKGROUND-COLOR:#F7F7F7;"> <a href="javascript:get_post('s');"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_btn2.gif" border="0" align="absmiddle" /></a><br />
			<input type=text name="saddr1" style="width:96%; BACKGROUND-COLOR:#F7F7F7;" value="<?=$home_addr1?>" class="input" readonly><br />
			<input type=text name="saddr2" style="width:96%; BACKGROUND-COLOR:#F7F7F7;" class="input" value="<?=!_empty($home_addr2)?$home_addr2:'나머지 주소'?>">
		</td>
	</tr>
</table>
<!-- 주문자 정보 입력 END -->

		</td>
		<td valign="top">
			<!-- 배송지 정보 입력 START -->
			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="orderTbl">
				<caption>
				<div style="position:relative; margin:0px; padding:0px; font-size:0px;">
					<img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_title_t05.gif">
					<div style="position:absolute; top:0px; left:135px;"><? if($ordertype  != "present"){?><span style="font-size:11px; font-weight:bold"> <input type=checkbox name="same" value="Y" onclick="SameCheck(this.checked)">주문자 정보와 동일함</span><? }?></div>
				</div>
				</caption>
				<tr>
					<th>수령인이름</th>
					<td><input type=text name="receiver_name" size="15" maxlength="12" class="input" style="BACKGROUND-COLOR:#F7F7F7;" value="<?=$receiver_name?>"></td>
				</tr>
				<tr>
					<th>전화번호</th>
					<td><input type=text name="receiver_tel11" size="5" maxlength="3" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;" value="<?=$receiver_tel11?>"> - <input type=text name="receiver_tel12" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;" value="<?=$receiver_tel12?>"> - <input type=text name="receiver_tel13" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;" value="<?=$receiver_tel13?>"></td>
				</tr>
				<tr>
					<th>핸드폰번호</th>
					<td><input type=text name="receiver_tel21" size="5" maxlength="3" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;" value="<?=$receiver_tel21?>"> - <input type=text name="receiver_tel22" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;" value="<?=$receiver_tel22?>"> - <input type=text name="receiver_tel23" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;" value="<?=$receiver_tel23?>"></td>
				</tr>
				<tr>
					<th>이메일</th>
					<td><input type=text name="email" value="<?=$receiver_email?>" size="30" class="input" style="width:96%; BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				<tr>
					<th class="lastTh">주소</th>
					<td class="lastTd" height="<?=$addrHight?>">
						<? if(!_empty($_ShopInfo->getMemid()) && $ordertype  != "present"){ ?>
						<div id="addressSelDiv"><input type=radio name="addrtype" value="H" onclick="addrchoice()" style="border:none;">자택&nbsp;<input type=radio name="addrtype" value="O" onclick="addrchoice()" style="border:none;">회사&nbsp;<input type=radio name="addrtype" value="B" onclick="addrchoice()" style="border:none;">최근 배송지&nbsp;<input type=radio name="addrtype" value="N" onclick="get_post('r')" style="border:none;">신규 배송지</div>
						<? } ?>
						<!-- <input type=text name="rpost1" size="3" onclick="this.blur();get_post('r')" class="input" style="BACKGROUND-COLOR:#F7F7F7;" value="<?=$receiver_zip1?>"> - <input type=text name="rpost2" size="3" onclick="this.blur();get_post('r')" class="input" style="BACKGROUND-COLOR:#F7F7F7;" value="<?=$receiver_zip2?>"> <a href="javascript:get_post('r');"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_btn2.gif" border="0" align="absmiddle" /></a><br /> -->
						<input type=text name="rpost1" size="5" onclick="this.blur();get_post('r')" class="input" style="width:50px;BACKGROUND-COLOR:#F7F7F7;" value="<?=$receiver_zip1?>"><a href="javascript:get_post('r');"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_btn2.gif" border="0" align="absmiddle" /></a><br />
						<input type=text name="raddr1" size="50" style="width:96%; BACKGROUND-COLOR:#F7F7F7;" class="input" readonly value="<?=$receiver_addr1?>"><br />
						<input type=text name="raddr2" size="50" style="width:96%; BACKGROUND-COLOR:#F7F7F7;" class="input"  value="<?=$receiver_addr2?>">
					</td>
				</tr>
			</table>
			<!-- 배송지 정보 입력 END -->
		</td>
	</tr>
</table>

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="orderTbl" style="margin-top:15px;">
	<tr>
		<th>주문메시지<br />(50자 내외)</th>
		<td>
			<input type="text" name="order_prmsg" value="" class="input" style="width:99%; BACKGROUND-COLOR:#F7F7F7;" /><br />
			택배 송장에 들어가는 메시지 입니다. 예) 부재시 경비실에 맡겨주세요.
		</td>
	</tr>
<? if(_empty($_ShopInfo->getMemid())){?>
	<tr>
		<th class="lastTh">비회원<br />정보수집 동의</th>
		<td class="lastTd">
			<div style="border:#dfdfdf 1px solid; padding:5px; overflow:auto; HEIGHT:100px; WIDTH:98%; font-size:11px;"><?=$privercybody?></DIV>
			<div style="text-align:left; padding-top:7px;">
				<?=$_data->shopname?>의 <font color="#FF4C00"><b>개인정보취급방침</b></FONT>에 동의하겠습니까?
				<input type=radio id=idx_dongiY name=dongi value="Y" style="border:none"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_dongiY><b><font color="#000000">동의합니다.</font></b></label><input type=radio id="idx_dongiN" name=dongi value="N" style="border:none"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_dongiN><b><font color="#000000">동의하지 않습니다.</font></b></label>
			</div>
		</td>
	</tr>
<? }?>
</table>



<?
	if(substr($ordertype,0,6) != "pester"){
?>


	<!-- 할인혜택 적용 START-->
	<?
	if(substr($ordertype,0,6)!= "pester" && $socialshopping != "social" && !_empty($_ShopInfo->getMemid()) && (($reserveuseable && $okreserve > 0 && ($user_reserve -$_data->reserve_maxuse) > 0) || (($_data->coupon_ok=="Y" && checkGroupUseCoupon()) || $couponable))){
		if ($_data->reserve_maxuse>=0 && $user_reserve!=0) {
			if($okreserve<0){
				$okreserve=(int)($sumprice*abs($okreserve)/100);
				if($reserve_maxprice>$sumprice) $okreserve=0;
				else if($okreserve>$user_reserve) $okreserve=$user_reserve;
			}
		}
		if($_data->reserve_maxuse > $user_reserve) $okreserve = 0;
		else $okreserve = min($okreserve,$basketItems['reserve_price']);
	?>

	<?
		//echo $reserveuseable.'<br>'.$okreserve.'<br>'.$user_reserve.'<br>'.$_data->reserve_maxuse.'<br>';
		if( ($_data->coupon_ok=="Y" && checkGroupUseCoupon() && $couponable) OR ($reserveuseable && ( $okreserve > 0 OR ($user_reserve -$_data->reserve_maxuse) > 0 ) && $_data->reserve_maxprice <= $basketItems['sumprice']+$basketItems['sumpricevat'] ) ) {
	?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="orderTbl" style="margin-top:30px;">
		<caption><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_title_t02.gif"></caption>
	<?	if($_data->coupon_ok=="Y" && checkGroupUseCoupon() && $couponable) { // 쿠폰 사용 가능?>
		<tr>
			<th>할인쿠폰 적용</th>
			<td>
				<li>할인금액 : <input type="text" name="coupon_price" id="coupon_price" class="st02_1" maxlength="8" value="0" readonly="readonly" /> 원 </li>
				<li><A HREF="javascript:coupon_check()" onmouseover="window.status='쿠폰선택';return true;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_btn1.gif" border="0" align="absmiddle"></A> <a href="javascript:resetCoupon()"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_btn3.gif" border="0" align="absmiddle"></a> <?=$offlineCouponInputButton?></li><br />
			보유 쿠폰을 조회하신 후 선택적용하시면 할인(혹은 추가적립) 혜택을 받으실 수 있습니다.</td>
		</tr>
	<? } ?>

	<?
		if($_data->reserve_maxprice <= $basketItems['sumprice']+$basketItems['sumpricevat']){		//20150318 적립금 기준금액 측정 조건식 수정
			/*
				차후 적립금 사용 - 배송비 관련 적립금 제외금액으로  배송비 기준 측정???
			*/
	?>
		<tr>
			<th class="lastTh">적립금사용</th>
			<td class="lastTd">
				<?
					if($okreserve > 0 && $user_reserve - $_data->reserve_maxuse >= 0){
				?>
				<input type="text" name="oriuser_reserve" class="st02_1" maxlength="8" value="<?=number_format($user_reserve)?>" readonly="readonly" /> 원 중에
				<input type="text" name="usereserve" id="usereserve" class="st02_1" maxlength="8" value="0"  <?=($okreserve<1)?'disabled="disabled"':''?>  /> 원을 사용<br /><span style="color:red"> <span style="font-weight:bold"><?=number_format($okreserve)?>원</span> 까지 적립금으로 사용하여 구매하실수 있습니다.(적립금사용불가 상품 제외금액)</span><br />
				<?
					}else{
				?>
					<input type="hidden1" name="usereserve" id="usereserve" class="st02_1" maxlength="8" value="0" />
					<strong>[보유적립금 : <?=number_format($user_reserve)?>]</strong>
				<?
					}
				?>
				(보유하신 적립금이 <span style="font-weight:bold"><?=number_format($_data->reserve_maxuse)?>원 이상</span>일 경우 사용 가능합니다.)
			</td>
		</tr>
	<? } ?>
	</table>
	<?
		}
	?>


	<?	if(!$reserveuseable ||  $okreserve <= 0 || ($user_reserve -$_data->reserve_maxuse) < 0) { ?>
	<input type="hidden" name="oriuser_reserve" class="st02_1" maxlength="8" value="<?=number_format($user_reserve)?>" />
	<input type="hidden" name="usereserve" value="0" />
	<? } ?>
	<?
	}else{ ?>
	<input type="hidden" name="usereserve" id="usereserve" value="0" />
	<? } ?>
	<!-- 할인혜택 적용 END -->

	<?	if(!_empty($_ShopInfo->getMemid()) && $_data->coupon_ok !="Y" || !$couponable) { ?><span id="disp_coupon" style="display:none">0</span><? } ?>



	<!-- 구매 사은품 선택 START -->
	<?
		if( $giftInfoSetArray[0] == "C" OR ( $giftInfoSetArray[0] == "M" AND !_empty($_ShopInfo->getMemid()) ) ){
	?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="orderTbl" style="margin-top:30px; display:none" id="giftSelectArea">
		<caption><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_title_t06.gif"></caption>
		<tr>
			<th>사은품 지급가능 구매금액</th>
			<td><input type="text" name="gift01" id="gift01" class="input" style="background-color:#ffffff; padding-left:4px;" maxlength="8" readonly value="<?=$basketItems['gift_price']?>" /> 원</td>
		</tr>
		<tr>
			<th>사은품 선택하기</th>
			<td style="padding:10px;">
				<div id="noGiftOptionArea">선택 가능한 사은품이 없습니다.</div>
				<table cellpadding="0" cellspacing="0" border="0" width="100%" class="noBodertbl" id="giftOptionBox" style="display:none">
					<tr>
						<td rowspan="2" style="border:1px solid #efefef; width:120px; height:120px; text-align:center"><img src="/images/no_img.gif" id="gift_img" /></td>
						<td style="padding:10px;" valign="top">
							<div style="width:100%; text-align:left;">
								<select name="giftval_seq" class="st13_1_1">
									<option value="" style="font-weight:bold;">:: 사은품선택 ::</option>
								</select>
							</div>
							<div id="giftOptionArea">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td>옵션1</td>
										<td><select name="giftOpt1" style="width:90%"></select></td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th class="lastTh">사은품 요청사항<br />(50자 내외)</th>
			<td class="lastTd">
				사은품 관련하여 요청사항이 있으실 경우 기재해 주세요.<br />
				<input type="tel" name="gift_msg" class="input" style="width:99%;" maxlength="50" disabled="disabled" /></td>
		</tr>
	</table>
	<?
		}
	?>
	<!-- 구매 사은품 선택 END -->






	<?
		// 그룹 할인 또는 적립
		if($sumprice>0 && !_empty($group_type)) {
	?>

	<div style="height:94px; text-align:left; line-height:18px; border:6px solid #eee; background:#fff; margin-top:20px; padding:10px;">
		<ul style="list-style:none;">
			<li style="width:150px; float:left; text-align:center; font-size:0px;"><?=$royal_img?></li>
			<li style="float:left; padding:16px;">
				<?=$groupMemberSale?>
				<span id="groupDeliFree"></span>
			</li>
		</ul>
	</div>
	<?
		}
	?>










	<!-- 선물하기 -->
	<? if($ordertype  == "present"){?>
		<table cellpadding="0" cellspacing="0" width="100%" class="orderTbl" style="margin-top:30px;">
			<caption>* 선물 받는 친구에게 메일을 보내세요.</caption>
			<tr>
				<th>이메일</th>
				<td><input type=text name="receiver_email" value="" class="input" style="width:99%; BACKGROUND-COLOR:#F7F7F7;"></td>
			</tr>
			<tr>
				<th>내용</th>
				<td><textarea name="receiver_message" style="WIDTH:99%; HEIGHT:70px; padding:5px; line-height:17px; border:solid 1px #DFDFDF;"></textarea></td>
			</tr>
		</table>
	<? }?>






	<!-- 결제수단 선택 START -->
	<div style="float:left; width:100%;" id="orderPaySel">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="orderTbl" style="margin-top:30px;">
			<caption><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_title_t03.gif"></caption>
			<tr>
				<th class="lastTh">결제 수단 선택</th>
				<td class="lastTd">
					<?
						// 결제수단 - order.php    결제 수단 선택
						echo $payType;
					?>
				</td>
			</tr>
		</table>
	</div>


	<style>
		.paytype {border:1px solid #ddd; border-bottom:hidden; float:left; width:65%; margin-top:10px;}
		.paytype caption {display:none;}
		.paytype th {height:25px; padding-left:10px; color:#666; text-align:left; border-bottom:1px solid #ddd;}
		.paytype td {padding:6px 0px 6px 10px; border-bottom:1px solid #ddd;}
		.paytext {font-size:11px;}

		.payTotal {float:right; margin-top:10px; border:1px solid #ddd; border-bottom:hidden;}
		.payTotal th {width:120px; padding-left:15px; background-color:#f5f5f5; font-size:11px; font-family:돋움; color:#666; text-align:left; border-right:1px solid #ddd; border-bottom:1px solid #ddd;}
		.payTotal td {padding:3px 10px; border-bottom:1px solid #ddd; text-align:right;}
	</style>

	<!-- 기본 안내 페이지 -->
	<div id="simg0" class="paytype">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" height="<?=$payHeight?>">
			<caption>결제 수단 선택</caption>
			<tr>
				<th><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0">결제수단을 선택해 주세요.</th>
			</tr>
			<tr>
				<td height="100%" class="paytext">
					- 결제 수단을 선택하신 후 아래의 <b>결제하기</b> 버튼을 클릭해 주시기 바랍니다.<br />
					- 주문자와 배송지 정보를 정확하게 입력하였는지 다시한번 확인해 주시기 바랍니다.
				</td>
			</tr>
		</table>
	</div>

	<!-- 무통장 입금 -->
	<div id="simg1" style="display:none;" class="paytype">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" height="<?=$payHeight?>">
			<caption>무통장 입금</caption>
			<tr>
				<th colspan="2"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0">무통장 입금</th>
			</tr>
			<tr>
				<th><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0">입금계좌 선택</th>
				<td>
				<? $arrpayinfo=explode("=",$_data->bank_account); ?>
					<select name="sel_bankinfo" class="st51_1_5">
						<option value=""><?=_empty($arrpayinfo[1])?'입금 계좌번호 선택 (반드시 주문자 성함으로 입금)':$arrpayinfo[1]?></option>
				<? if(!_empty($arrpayinfo[0])){
						$count = 0;
						$tok = strtok($arrpayinfo[0],",");
						while($tok){ ?>
						<option value="<?=$tok?>" ><?=$tok?></option>
				<?			$tok = strtok(",");
							$count++;
						} // end while
					} // end if
					?>
					</select>
				</td>
			</tr>
			<tr>
				<th><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0">입금자명</th>
				<td><input type="text" name="bankname" value="" size="12" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> <span style="font-size:11px;color:red;letter-spacing:-0.5px;">(※ 주문자와 입금자가 다를경우 반드시 입력해 주시기 바랍니다.)</span></td>
			</tr>
			<tr>
				<td colspan="2" height="100%" class="paytext">
					- 무통장 입금의 경우 <FONT COLOR="#EE1A02">입금확인 후 </font> 배송처리가 진행되며, 안전하고 빠르게 상품을 배송합니다.
				</td>
			</tr>
		</table>
	</div>

	<!-- 카드결제 -->
	<div id="simg2" style="display:none;" class="paytype">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" height="<?=$payHeight?>">
			<caption>신용카드</caption>
			<tr>
				<th><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0">신용카드</th>
			</tr>
			<tr>
				<td height="100%" class="paytext">
					- 신용카드 정보가 상점에 남지 않으며, 128bit SSL로 암호화된 결제창이 새로 뜹니다.<br />
					- 결제 후, 카드명세서에 [<FONT COLOR="#EE1A02">결제대행사명</font>]으로 표시됩니다!
					</span>
				</td>
			</tr>
		</table>
	</div>

	<!-- 실시간계좌이체 -->
	<div id="simg3" style="display:none;" class="paytype">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" height="<?=$payHeight?>">
			<caption>실시간 계좌이체</caption>
			<tr>
				<th><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0">실시간 계좌이체</th>
			</tr>
			<tr>
				<td height="100%" class="paytext">
					- 본인계좌 정보입력으로 결제금액이 이체되는 서비스 입니다.<br />
					- 인터넷뱅킹과 동일한 보안방식을 적용하므로 안전하며, 상점에 정보가 남지 않습니다.
				</td>
			</tr>
		</table>
	</div>

	<!-- 가상계좌 -->
	<div id="simg4" style="display:none;" class="paytype">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" height="<?=$payHeight?>">
			<caption>가상계좌</caption>
			<tr>
				<th><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0">가상계좌</th>
			</tr>
			<tr>
				<td height="100%" class="paytext">- 주의! 1회용 계좌(가상계좌) 입금시, 이름/금액이 반드시 일치되어야 입금확인이 가능합니다.</td>
			</tr>
		</table>
	</div>

	<!-- 결제대금예치제(에스크로) -->
	<div id="simg5" style="display:none;" class="paytype">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" height="<?=$payHeight?>">
			<caption>결제대금예치제(에스크로)</caption>
			<tr>
				<th><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0">결제대금예치제(에스크로)</th>
			</tr>
			<tr>
				<td height="100%" class="paytext">
					- 에스크로를 통해서 구매결정을 하실 수 있는 결제방식입니다.<br>
					- 주의! 1회용 계좌(가상계좌) 입금시, 이름/금액이 반드시 일치되어야 입금확인이 가능합니다.
				</td>
			</tr>
		</table>
	</div>

	<!-- 핸드폰 결제 -->
	<div id="simg6" style="display:none;" class="paytype">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" height="<?=$payHeight?>">
			<tr>
				<th><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0">핸드폰 결제</th>
			</tr>
			<tr>
				<td colspan="2" height="100%" class="paytext">
					- 결제정보가 상점에 남지 않으며, 보안 적용된 결제창이 새로 뜹니다.<br>
					- 결제 후, 핸드폰 요금 청구서에 '(주)다날' 로 표시됩니다.
				</td>
			</tr>
		</table>
	</div>
	<!-- 결제수단 선택 END -->



	<div class="payTotal">
		<table border="0" cellpadding="0" cellspacing="0" width="300">
			<caption style="display:none;">총 결제 내역</caption>
			<tr>
				<th>합계</th>
				<td><span style="font-size:13px;"><?=number_format($sumprice+$sumpricevat)?></span> 원</td>
			</tr>
			<? if(!_empty($_ShopInfo->getMemid())){ ?>
			<tr>
				<th>적립금 사용</th>
				<td><span id="disp_reserve">0</span> 원</td>
			</tr>
			<?	if($_data->coupon_ok =="Y" && $couponable) { ?>
			<tr>
				<th>할인쿠폰</th>
				<td><span id="disp_coupon">0</span> 원</td>
			</tr>
			<? } ?>
			<tr>
				<th>회원그룹(추가)할인</th>
				<td><span id="disp_groupdiscount">0</span> 원</td>
			</tr>
			<? } ?>
			<tr>
				<th>배송비</th>
				<td><span id="disp_deliprice"><!-- <?=number_format($basketItems['deli_price'])?> -->0</span> 원</td>
			</tr>
			<tr>
				<th>최종 결제금액</th>
				<td style="color:red; font-weight:bold;"><span id="disp_last_price" style="font-size:18px; font-family:Tahoma;"><?=number_format($basketItems['sumprice']+$basketItems['deli_price']+$basketItems['sumpricevat'])?></span> 원</td>
			</tr>
		</table>
	</div>

<?
	}
?>






	<!-- 조르기 -->
	<? if(substr($ordertype,0,6) == "pester"){?>
		<table cellpadding="0" cellspacing="0" width="100%" class="orderTbl" style="margin-top:30px;">
			<caption><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_title_t07.gif" alt="" /></caption>
			<tr>
				<th>상대방 이름</th>
				<td><input type=text name="pester_name" size="15" maxlength="12" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
			</tr>
			<tr>
				<th>상대방 전화번호</th>
				<td><input type=text name="pester_tel1" value="" size="5" maxlength="3" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="pester_tel2" value="" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="pester_tel3" value="" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
			</tr>
			<tr>
				<th>상대방 이메일</th>
				<td><input type=text name="pester_email" value="" size="30" class="input" style="width:80%;BACKGROUND-COLOR:#F7F7F7;"></td>
			</tr>
			<tr>
				<th>SMS 전송메세지</th>
				<td><textarea name="pester_smstxt" style="WIDTH:98%; HEIGHT:70px; padding:5px; line-height:17px; border:solid 1px #DFDFDF;"></textarea></td>
			</tr>
			<tr>
				<th>E-MAIL<br />전송메세지</th>
				<td><textarea name="pester_emailtxt" style="WIDTH:98%; HEIGHT:70px; padding:5px; line-height:17px; border:solid 1px #DFDFDF;"></textarea></td>
			</tr>
		</table>
	<? }?>