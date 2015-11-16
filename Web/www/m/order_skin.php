<input type="hidden" name="msg_type" value="1" />
<input type=hidden name="addorder_msg" value="">
<input type="hidden" name="sumprice" value="<?=$basketItems['sumprice']?>" />
<div class="order_wrap">
	<!-- 주문상품 출력 START -->
	<h2>주문상품 확인</h2>
	<div class="order_pr_info">
	<?
		$couponable = false;
		$reserveuseable = false;
		$productRealPrice = 0;

		
		if($basketItems['productcnt'] <1){ ?> 
	?>
		<div style="height:30px;">등록된 상품이 없습니다.</div>
	<?
		}else{
			$timgsize = 80;
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

		<div class="order_pr_loop">
			<div class="order_pr_image"><!-- <img src="<?=$product['tinyimage']['src']?>" <? if($product['tinyimage'][$product['tinyimage']['big']] > $timgsize) echo $product['tinyimage']['big'].'="'.$timgsize.'"'; ?> /> -->
			<img src="<?=_getMobileThumbnail($origloc,$saveloc,$product['tinyimage']['ori'],80,80,$quality)?>" />			
			</div>
			<table cellpadding="0" cellspacing="0" border="0" class="order_pr_table">
				<tr>
					<td colspan="2" style="text-align:left; padding:0px; padding-bottom:4px; border-bottom:1px dashed #e5e5e5;">
						<a href="./productdetail_tab01.php?productcode=<?=$product['productcode']?>">
							<b><?=cutStr($product['productname'],30)?></b>
						</a>
						<? if(_array($product['option1']) || _array($product['option2']) || !_empty($product['optvalue'])){ ?>
						<br/>

						<span>
							<img border=0 src="../images/common/basket/001/basket_skin3_icon002.gif">
							<?=$product['option1'][$product['opt1_idx']]?>
							<? 
								if(_array($product['option2'])) {
									echo ' / '.$product['option2'][$product['opt2_idx']]; 
								}

								if(!_empty($product['optvalue'])) {
									echo $product['optvalue']."\n";
								}
							?>
						</span>
						<? } ?>
						<br />

						<span style="font-size:0.8em; color:#999999;">
							<? if($product['bankonly'] == 'Y'){ ?>현금결제/<? } ?>
							<? if($product['setquota'] == 'Y'){ ?>무이자/<? } ?>
							<?
								$sptxt = array();
								if($product['cateAuth']['reserve'] == 'N') array_push($sptxt,'적립금 사용불가/');
								if($product['cateAuth']['coupon'] == 'N') array_push($sptxt,'할인쿠폰 적용불가/');
								if($product['cateAuth']['refund'] == 'N') array_push($sptxt,'교환/반품 불가/');
								if($product['cateAuth']['gift'] == 'Y') array_push($sptxt,'사은품 지급');
								if(_array($sptxt)){
									//echo '<br />'.implode(' / ',$sptxt);
									echo implode('',$sptxt);
								}
							?>
						</span>
					</td>
				</tr>
				<tr>
					<th>수량 :</th>
					<td><?=$product['quantity']?>개</td>
				</tr>
				<tr>
					<th>구매가 :</th>
					<td><span class="point3"><?=number_format($product['realprice'])?>원</span></td>
				</tr>
				<tr>
					<th>적립금 :</th>
					<td><?=number_format($product['reserve'])?>원</td>
				</tr>
				<tr>
					<th>배송비 :</th>
					<td>
					<? 
						if($product['deli_price']>0){
							if($product['deli']=="Y"){
					?>
						유료배송<br />
					<?=number_format($product['deli_price']*$product['quantity'])?>원
					<?		
						}else if($product['deli']=="N") { 
					?>
						유료배송<br />
					<?=number_format($product['deli_price'])
					?>
						원
					<?		
						}
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
				</tr>
			</table>
		</div>
	<?			}// end for
			} // end foreach
		} // end if
	?>

	</div>
	<!-- 주문상품 출력 END -->
	<!-- 주문정보 START -->
	<!-- 주문자 정보 입력 START -->
	<h2>주문자 정보 입력</h2>
	<div class="order_info_table_wrap">
		<table cellpadding="0" cellspacing="0" border="0" class="order_table">
			<tr>
				<th>이름</th>
				<td><input type="text" name="sender_name" class="mobile_input mobile_text" value="<?=$name?>" /></td>
			</tr>
			<tr>
				<th>전화번호</th>
				<td>
					<input type="tel" name="sender_tel1" value="<?=$home_tel[0] ?>" size="5" maxlength="3" class="mobile_input mobile_number" /> - 
					<input type="tel" name="sender_tel2" value="<?=$home_tel[1] ?>" size="5" maxlength="4" class="mobile_input mobile_number" /> - 
					<input type="tel" name="sender_tel3" value="<?=$home_tel[2] ?>" size="5" maxlength="4" class="mobile_input mobile_number" />
				</td>
			</tr>
			<tr>
				<th>연락처</th>
				<td>
					<input type="tel" name="sender_hp1" class="mobile_input mobile_number" value="<?=$mobile[0]?>" maxlength="4"/> -
					<input type="tel" name="sender_hp2" class="mobile_input mobile_number" value="<?=$mobile[1]?>" maxlength="4"/> -
					<input type="tel" name="sender_hp3" class="mobile_input mobile_number" value="<?=$mobile[2]?>" maxlength="4"/>
				</td>
			</tr>
			<tr>
				<th class="lastTH">이메일</th>
				<td class="lastTD"><input type="email" name="sender_email" id="o_email" class="mobile_input mobile_text" maxlength="80" value="<?=$email?>" /></td>
			</tr>
		</table>
	</div>
	<!-- 주문자 정보 입력 END -->

	<!-- 배송 정보 입력 START -->
	<div class="delivery_title">
		<h2>배송정보 입력</h2>
		<div class="btn_same"><a class="button black small" onClick="SameCheck();">주문자와 동일</a></div>
	</div>
	<div class="order_info_table_wrap">
		<table cellpadding="0" cellspacing="0" border="0" class="order_table">
			<tr>
				<th>받는사람</th>
				<td><input type="text" id="o_name" name="receiver_name" class="mobile_input mobile_text" /></td>
			</tr>
			<tr>
				<th>전화번호</th>
				<td>
					<input type="tel" name="receiver_tel11" id="o_number2" class="mobile_input mobile_number" maxlength="4" /> - 
					<input type="tel" name="receiver_tel12" id="o_number2" class="mobile_input mobile_number" maxlength="4" /> - 
					<input type="tel" name="receiver_tel13" id="o_number2" class="mobile_input mobile_number" maxlength="4" />
				</td>
			</tr>
			<tr>
				<th>비상전화</th>
				<td>
					<input type="tel" name="receiver_tel21" id="o_number2" class="mobile_input mobile_number" maxlength="4"> - 
					<input type="tel" name="receiver_tel22" id="o_number2" class="mobile_input mobile_number" maxlength="4"> - 
					<input type="tel" name="receiver_tel23" id="o_number2" class="mobile_input mobile_number" maxlength="4">
				</td>
			</tr>
			<tr>
				<th>주소</th>
				<td class="order_deli_type1">
					<input type="text" name="rpost1" class="mobile_input post_field mobile_number" maxlength="5" style="width:50px;"/> 
					<button type="button" onClick="javascript:get_post();" class="button white small"><span>주소찾기</span></button><br/>
					<input type="text" name="raddr1" class="mobile_input address_field mobile_text" value="" /><br/>
					<input type="text" name="raddr2" class="mobile_input address_field mobile_text" value="" />
				</td>
			</tr>
			<tr>
				<th class="lastTH">전달사항</th>
				<td class="lastTD">
					<textarea name="order_prmsg" id="o_text" class="order_textarea" rows="5"></textarea>
				</td>
			</tr>
		</table>
	</div>
	<!-- 배송 정보 입력 END -->

	<!-- 비회원 개인정보 수집동의 START -->
	<?
		if(strlen($_ShopInfo->getMemid()) <= 0){
	?>
		<h2>비회원 개인정보 수집동의</h2>
		<div class="persnal_info_wrap">
			<div class="persnal_clause">
				<?=strip_tags($privercybody, "<p>")?>
			</div>
			<div class="persnal_clause_btn_wrap">
				<input type="radio" id="idx_dongiY" name="dongi" value="Y" /><label for="idx_dongiY" class="clause_btn_true">동의함</label>&nbsp;&nbsp;&nbsp;
				<input type="radio" id="idx_dongiN" name="dongi" value="N" /><label for="idx_dongiN" class="clause_btn_false">동의 안함</label>
			</div>
		</div>
	<?
		}
	?>
	<!-- 비회원 개인정보 수집동의 END -->

	<!-- 구매시 혜택 사항 START -->
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
		if( ($_data->coupon_ok=="Y" && checkGroupUseCoupon() && $couponable) OR ($reserveuseable && $okreserve > 0 && ($user_reserve -$_data->reserve_maxuse) > 0) ) {
	?>
	<h2>구매시 혜택 적용</h2>
	<div class="order_benefit_wrap">
		<table cellpadding="0" cellspacing="0" border="0" class="order_table">
			<!-- 쿠폰 적용 사항 START -->
			<tr>
				<th>쿠폰</th>
			<?
				if($_data->coupon_ok=="Y" && checkGroupUseCoupon() && $couponable) { //쿠폰 사용가능 여부 체크 
			?>
				<td class="order_benefit_type1">
					<input type="text" name="coupon_price" id="coupon_price" onclick="coupon_check()" class="st02_1 mobile_input" maxlength="8" value="0" readonly="readonly" /> 원
					<a class="button white small" onClick="coupon_check();">쿠폰적용</a>
					<a class="button white small" onClick="coupon_check();">적용취소</a>
				</td>
			<?}else{?>
				<td>쿠폰사용 조건에 해당되지 않습니다.</td>
			<?}?>
			</tr>
			<!-- 쿠폰 적용 사항 END -->

			<!-- 적립금 적용 사항 START -->
			<tr>
				<th class="lastTH">적립금</th>
				<?
					if($reserveuseable){
						if($okreserve > 0 && $user_reserve - $_data->reserve_maxuse >= 0){
				?>
					<td class="order_benefit_type2">
						<input type="hidden" name="okreserve" value="<?=$okreserve?>" />
						보유 적립금 : <input type="text" name="oriuser_reserve" class="st02_1 mobile_input save_reserve" maxlength="8" value="<?=number_format($user_reserve)?>" readonly="readonly" /> 원
						<br/>사용 적립금 :
						<input type="text" name="usereserve" id="usereserve" class="st02_1 mobile_input" maxlength="8" value="0"  <?=($okreserve<1)?'disabled="disabled"':''?>  /> 원 사용<br />
						<!--<span style="color:red"> <span style="font-weight:bold"><?=number_format($okreserve)?>원</span> 까지 적립금으로 사용하여 구매하실수 있습니다.</span><br />-->
					</td>
				<?
						}else{
				?>
				<td>보유적립금 <span class="order_special_char"><?=number_format($_data->reserve_maxuse)?>원</span> 이상 일 경우 사용가능합니다.</td>
				<?
						}
					}else{
				?>
				<td>적립금 사용 조건에 해당되지 않습니다.</td>
				<?}?>
			</tr>
			<!-- 적립금 적용 사항 END -->
		</table>
	</div>
	<?}?>

	<?
		if(!$reserveuseable ||  $okreserve <= 0 || ($user_reserve -$_data->reserve_maxuse) < 0) {
	?>
		<input type="hidden" name="oriuser_reserve" class="st02_1" maxlength="8" value="<?=number_format($user_reserve)?>" />
		<input type="hidden" name="usereserve" value="0" />
	<?
		}
	}else{
	?>
		<input type="hidden" name="usereserve" id="usereserve" value="0" />
	<?
		}
	?>
	<?
		if(!_empty($_ShopInfo->getMemid()) && $_data->coupon_ok !="Y" || !$couponable) {
	?>
		<span id="disp_coupon" style="display:none">0</span>
	<?
		}
	?>
	<!-- 구매시 혜택 사항 END -->

	<!-- 사은품 적용 사항 START -->
	<?
		if( $giftInfoSetArray[0] == "C" OR ( $giftInfoSetArray[0] == "M" AND !_empty($_ShopInfo->getMemid()) ) ){
	?>
	<div id="giftSelectArea">
		<h2>구매 사은품 </h2>
		<div class="freegift_wrap">
			<input type="hidden" name="gift01" id="gift01" class="mobile_input" maxlength="8" readonly value="<?=$basketItems['gift_price']?>" />
			<table cellpadding="0" cellspacing="0" class="freegift_table">
				<tr>
					<th>사은품<br />선택</th>
					<td>
						<div id="noGiftOptionArea">선택 가능한 사은품이 없습니다.</div>
						<table cellpadding="0" cellspacing="0" border="0" width="100%" id="giftOptionBox" style="display:none">
							<tr>
								<td class="freegift_image_area">
									<img src="/images/no_img.gif" id="gift_img" height="50"/>
								</td>
							</tr>
							<tr>
								<td class="freegift_choice">
									<div style="text-align:left;">
										<select name="giftval_seq" class="st13_1_1">
											<option value="">:: 사은품 선택 ::</option>
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div id="giftOptionArea">
										<table class="noborder" cellpadding="0" cellspacing="0">
											<tr>
												<td>옵션1</td>
												<td><select name="giftOpt1"></select></td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<th class="lastTH">요청사항</th>
					<td class="lastTD"><input name="gift_msg" class="mobile_input freegift_sendmsg" maxlength="50" disabled="disabled" placeholder="사은품 관련 요청사항 입력(50자)" style="margin-bottom:4px;"/></td>
				</tr>
			</table>
		</div>
	</div>
	<?
		}
	?>
	<!-- 사은품 적용 사항 END -->
	<?if($sumprice>0 && !_empty($group_type)) {?>
	<h2>회원등급 정책</h2>
	<div class="order_group_wrap">
		<?=$groupMemberSale?>
	</div>
	<?}?>
	<!-- 결제 방법 선택 START -->
	<?
		$arrpayinfo=explode("=",$_data->bank_account);
	?>
	<h2>결제방법 선택</h2>
	<div class="payment_type_wrap">
		<table class="payment_wrap" cellpadding="0" cellspacing="0">
			<tr>
				<th>결제방법</th>
				<td class="paytype_list">
					<?
						$paytype_sql = "SELECT use_bank, use_creditcard, use_transferaccount, use_virtualaccount FROM tblmobileconfig";
						$paytype_result = mysql_query($paytype_sql, get_db_conn());
						$paytype_row = mysql_fetch_object($paytype_result);
						
						$usepg_sql = "SELECT pg_use FROM tblmobilepg WHERE pg_section = 'mobile' ";
						$usepg_result = mysql_query($usepg_sql, get_db_conn());
						$usepg_row = mysql_fetch_object($usepg_result);
						
						if($paytype_row->use_bank == 'Y'){
					?>
					<input type="radio" id="paytype_1" class="paytype" name="sel_paymethod" value="B" onClick="change_paymethod(1);"><label for="paytype_1"><span class="button white small btn_paytype paytype_1" onClick="paymentControl('bankaccount');">&nbsp;무통장&nbsp;</span></label>
					<?
						}
						if(strlen($_data->card_id) > 0){
							if($paytype_row->use_creditcard == 'Y' && $usepg_row->pg_use == 'Y'){
	
					?>
					<input type="radio" id="paytype_2" class="paytype" name="sel_paymethod" value="C" onClick="change_paymethod(2);"><label for="paytype_2"><span class="button white small btn_paytype paytype_2" onClick="paymentControl('creditcard');">신용카드</span></label>
					<?
							}
						}
						if(strlen($_data->trans_id)>0) {
							if($paytype_row->use_transferaccount == 'Y' && $usepg_row->pg_use == 'Y'){
					?>
					<input type="radio" id="paytype_3" class="paytype" name="sel_paymethod" value="V" onClick="showBankAccount('hide')"><label for="paytype_3"><span class="button white small btn_paytype paytype_3" onClick="paymentControl('transferaccount');">계좌이체</span></label>
					<?
							}
						}
						if(strlen($_data->virtual_id)>0) {
							if($paytype_row->use_virtualaccount == 'Y' && $usepg_row->pg_use == 'Y'){
					?>
					<input type="radio" id="paytype_4" class="paytype" name="sel_paymethod" value="V" onClick="showBankAccount('hide')"><label for="paytype_4"><span class="button white small btn_paytype paytype_4" onClick="paymentControl('virtualaccount');">가상계좌</span></label>
					<?
							}
						}
					?>
				</td>
			</tr>
			<tr id="pay_account_list" style="display:none">
				<th class="borderline">입금계좌</th>
				<td class="borderline">
					<table class="pay_account_table" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td class="account_select_wrap">
								<select name="pay_data1" id="pay_data1" onChange="accountControl(this.value);">
									<option value="dont">선택하세요</option>
									<?
										//무통장
										if($escrow_info["onlycard"]!="Y" || (int)$banklast_price<100000) {
											if(preg_match("/^(Y|N)$/", $_data->payment_type)) {//결제방법이 모든결제 OR 온라인결제가 선택되었을 경우
												if (strlen($arrpayinfo[0])>0) {
											
												$tok = strtok($arrpayinfo[0],",");
												$count = 1;
													while ($tok) {
														$account_info = explode(" ",$tok);
														$account_division = explode(":", $account_info[2]);
														$account_holder = substr($account_division[1],0,-1);
									?>
														<option value="<?=$tok?>"><?=$account_info[0]?></option>
									<?
														$tok = strtok(",");
														$count++;
													}
												}
											}
										}
										//무통장
										if($escrow_info["onlycard"]!="Y" || (int)$banklast_price<100000) {
											if(preg_match("/^(Y|N)$/", $_data->payment_type)) {//결제방법이 모든결제 OR 온라인결제가 선택되었을 경우
												echo $pmethodlist[0];
											}
										}
									?>
								</select>
							</td>
						</tr>
						<tr id="account_info_list" style="display:none;">
							<td>
								<table cellpadding="0" width="100%" cellspacing="0" border="0" class="account_info_table">
									<tr>
										<td class="account_info_menu">계좌번호</td>
										<td class="separation">:</td>
										<td id="pay_account"></td>
									</tr>
									<tr>
										<td class="account_info_menu">예&nbsp;&nbsp;금&nbsp;&nbsp;주</td>
										<td class="separation">:</td>
										<td id="pay_holder"></td>
									</tr>
									<tr>
										<td class="lastTD">입금자명</td>
										<td class="lastTD">:</td>
										<td class="lastTD">
											<input type="text" name="bankname" class="mobile_input transfetname" value="" placeholder="입금자명이 다를 경우 입력"/>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>

	<h2 class="totalpay_h2">총 결제 내역</h2>
	<div class="totalpay_info_table">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<th>합계</th>
				<td><?=number_format($sumprice+$sumpricevat)?></span>원</td>
			</tr>
			<? if(!_empty($_ShopInfo->getMemid())){ ?>
			<tr>
				<th>적립금 사용</th>
				<td><span id="disp_reserve">0</span>원</td>
			</tr>
			<? if($_data->coupon_ok =="Y" && $couponable){ ?>
			<tr>
				<th>할인쿠폰</th>
				<td><span id="disp_coupon">0</span>원</td>
			</tr>
			<? } ?>
			<tr>
				<th>등급할인</th>
				<td><span id="disp_groupdiscount">0</span>원</td>
			</tr>
			<? } ?>
			<tr>
				<th>배송비</th>
				<td>
					<span id="disp_deliprice"><!-- <?//=number_format($basketItems['deli_price'])?> -->0</span>원
					<!-- <input type='hidden' name='disp_deliprice_temp' id='disp_deliprice_temp' value='0'> -->
				</td>
			</tr>
			<tr>
				<th class="lastTH">최종 결제금액</th>
				<td class="lastTD"><span id="disp_last_price" class="point3"><?=number_format($basketItems['sumprice']+$basketItems['deli_price']+$basketItems['sumpricevat'])?></span>원</td>
			</tr>
		</table>
	</div>

	<!-- 버튼 -->
	<section class="basic_btn_area btn_w1">

	<!-- <? if($row_cfg[use_bank]!="Y" && $row_cfg[use_creditcard]!="Y" && $row_cfg[use_mobilephone]!="Y") { ?>
	<a href="javascript:alert('결제방식이 설정되어있지 않습니다. 관리자에게 문의하세요~')" class="button black bigrounded">결제하기</a>
	<? } else { ?>
	<a href="javascript:CheckForm();" class="button black bigrounded">결제하기</a>
	<? }?>
	<a href="javascript:ordercancel('cancel');" class="button white bigrounded">주문취소</a> -->

	<a href="javascript:CheckForm();" class="button blue bigrounded">결제하기</a>
	<a href="javascript:ordercancel('cancel');" class="button white bigrounded">주문취소</a>
	</section>
	<!-- //버튼 -->
</div>
<!-- 주문정보 END -->

	<Script>
		$j(function(){
			$j('select[name=giftval_seq]').change( function(){ resetGiftOptions();});
		});
	secGifts = "";
	function secGift(vls) {
		f = document.form1;

		$("gift_"+secGifts).style.display = "none";
		tmp = eval("f.img_"+secGifts);
		$("gift_img").src = tmp.value;

		$("gift_"+vls).style.display = "block";
		tmp = eval("f.img_"+vls);
		$("gift_img").src = tmp.value;

		secGifts = vls;

	}

	function accountControl(idx){
		var cainfo = false;
		if(idx != 'dont'){
			var account_info = idx.split(' ');

			if((account_info[2].charAt(0) == '(') ||((account_info[2].charAt(account_info[2].length - 1)) == ')')){
				var account_holder = account_info[2].substr(0,account_info[2].lastIndexOf(')')).slice(1).split(':');
				var holder = account_holder[1];
				cainfo = true;
			}
			if(cainfo != false){
				$j('#account_info_list').removeAttr('style');
				$j('#pay_account').text(account_info[1]);
				$j('#pay_holder').text(holder);
			}
		}else{
			$j('#account_info_list').css('display','none');
		}
	}
	function paymentControl(idx){
		switch(idx){
			case 'bankaccount':
				$j('.btn_paytype').removeClass('selected');
				$j('.paytype_1').addClass('selected');
				showBankAccount('show');
			break;
			case 'creditcard':
				$j('.btn_paytype').removeClass('selected');
				$j('.paytype_2').addClass('selected');
				showBankAccount('hide');
			break;
			case 'transferaccount':
				$j('.btn_paytype').removeClass('selected');
				$j('.paytype_3').addClass('selected');
				$j('#pay_account_list').css('display','none');
			break;
			case 'virtualaccount':
				$j('.btn_paytype').removeClass('selected');
				$j('.paytype_4').addClass('selected');
				$j('#pay_account_list').css('display','none');
			break;
		}
	}
	$j('.clause_btn_true').click(function(){
		$j('.clause_btn_false').removeClass('selected');
		$j('.clause_btn_true').addClass('selected');
	});
	$j('.clause_btn_false').click(function(){
		$j('.clause_btn_true').removeClass('selected');
		$j('.clause_btn_false').addClass('selected');
	});
	$j(function(){
		$j('select[name=giftval_seq]').change( function(){	resetGiftOptions();});
	//	$j( "<div></div>" ).after( "<p></p>" ).addClass( "foo" ).filter( "p" ).attr( "id", "bar" ).html( "hello" ).end().appendTo( "body" );
	});

// 적용 가능 사은품 가져 오기
	function giftchoices(gprice){
		var tempgprice = parseInt($j('input[name=gift01]').val()); // 적용전 (현) 사은품 지급가능 구매금액
		gprice = parseInt(gprice); // 적용될 사은품 지급가능 구매금액
		var noGift = ($j('input[name=possible_gift_price_used]').val() == 'N');
		if(!noGift){
			if(isNaN(gprice)) gprice = tempgprice;
			if(isNaN(gprice) || gprice < 1) gprice = 0;
		}else{
			gprice = 0;
		}

		// 사은품 지급가능 구매금액에 변동이 없고 사은품이 선택 되어 있을경우 사은품 변동 안함
		var index = $j("select[name=giftval_seq] option").index( $j("select[name=giftval_seq] option:selected") );
		if( tempgprice == gprice && index > 0 ) {
			return false;
		}

		$j('input[name=gift01]').val(gprice);
		if(gprice >= mingiftprice){
			//if($j('#giftSelectArea')) $j('#giftSelectArea').css('display','');
			$j.post( '/json_order.php',{'act':'getGife','gift_price':gprice},function(data){
				if(data.err == 'ok'){
					giftReset(data.items);
				}else{
					alert(data.err);
				}
			},'json');
		}else{
			if($j('#giftSelectArea')) $j('#giftSelectArea').css('display','none');
			$j('#noGiftOptionArea').css('display','');
			$j('#giftOptionBox').css('display','none');
			$j('input[name=gift_msg]').attr('disabled','disabled');
		}
	}

	// 사은품 초기화
	function giftReset(items){
		$j('select[name=giftval_seq]').find('option:gt(0)').remove();
		resetGiftOptions();

		if($j(items).size() < 1){
			
			if($j('#giftSelectArea')) $j('#giftSelectArea').css('display','none');

			$j('#noGiftOptionArea').css('display','');
			$j('#giftOptionBox').css('display','none');
			$j('input[name=gift_msg]').attr('disabled','disabled');
		}else{
			
			if($j('#giftSelectArea')) $j('#giftSelectArea').css('display','');
			$j('#noGiftOptionArea').css('display','none');
			$j('#giftOptionBox').css('display','');

			if($j('input[name=gift_msg]').attr('disabled'))   $j('input[name=gift_msg]').removeAttr('disabled');
			if($j.isArray(items)){
				$j(items).each(function(idx,itm){
					addGiftSelSelect(itm);
				});
			}else{
				$j(items).each(function(idx,itm){
					for(p in itm) addGiftSelSelect(itm[p]);
				});
			}
		}
	}

	// 사은품 옵션 초기화
	function resetGiftOptions(){
		var $gift = $j("select[name=giftval_seq] option:selected");
		var index =$j("select[name=giftval_seq] option").index($gift);
		$j('#giftOptionArea').html('');

		if($j.trim($j($gift).data('imgsrc')).length < 1){
			$j('#gift_img').attr('src',"/images/no_img.gif");
		}else{
			$j('#gift_img').attr('src','<?=$Dir?>data/shopimages/etc/'+$j($gift).data('imgsrc'));
		}

		if(index > 0){
			$items = $j($gift).data('options');
			//alert($j($items).size());
			if($j($items).size() >0){
				var str = '<table border="0" cellpadding="0" cellspacing="0" style="width:100%">';
				if($j.isArray($items)){
					$j($items).each(function(idx,itm){
						str += '<tr><td style="width:50px;">옵션 '+(idx+1)+' :</td>';
						str += '<td><select name="giftOpt'+(idx+1)+'" style="width:90%">';
						$name = itm.name;

						$j(itm.items).each(function(idx,sitm){
							str += '<option value="'+sitm[0]+'">'+$name+' : '+sitm[0]+'</option>';
						});
						str += '</select></td></tr>';
					});
				}else{
					$j($items).each(function(idx,oitm){
						for(p in oitm){
							itm = oitm[p];
							str += '<tr><td style="width:50px;">옵션 '+(p)+' :</td>';
							str += '<td><select name="giftOpt'+(p)+'" style="width:90%">';
							$name = itm.name;
							$j(itm.items).each(function(idx,sitm){
								/*
								for(q in sitm){
									alert(q);
									str += '<option value="'+sitm[q]+'">'+$name+' : '+sitm[q]+'</option>';
								}*/
								str += '<option value="'+sitm[0]+'">'+$name+' : '+sitm[0]+'</option>';
							});
							str += '</select></td></tr>';
						}
					});
				}
				str += '</table>';
				$j('#giftOptionArea').html(str);
				//alert(str);
			}
		}
	}

function addGiftSelSelect(itm){
	$j('<option value="'+itm.gift_regdate+'">'+itm.gift_name+'</option>').data('imgsrc',itm.gift_image).data('options',itm.options).appendTo("select[name=giftval_seq]");

}
</script>