<input type="hidden" name="msg_type" value="1" />
<input type=hidden name="addorder_msg" value="">
<input type="hidden" name="sumprice" value="<?=$basketItems['sumprice']?>" />


<!-- �ֹ���ǰ ���̺� START -->
<div class="order_wrap">
	<table class="order_pr_container" cellpadding="0" cellspacing="0">
		<tbody>
	<?
		$couponable = false;
		$reserveuseable = false;
		if($basketItems['productcnt'] <1){ 
	?>
			<tr>
				<td style="height:30px;">��ϵ� ��ǰ�� �����ϴ�.</td>
			</tr>
	<?
		}else{
			$timgsize = 75;
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
				<td class="order_pr_wrap">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td class="order_pr_img_wrap">
								<img src="<?=$product['tinyimage']['src']?>" <? if($product['tinyimage'][$product['tinyimage']['big']] > $timgsize) echo $product['tinyimage']['big'].'="'.$timgsize.'"'; ?> />
							</td>
							<td>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td class="order_pr_title">
											��ǰ�� :
										</td>
										<td class="order_pr_infomsg">
											<a href="./productdetail_tab01.php?productcode=<?=$product['productcode']?>">
												<font color="#000000">
													<b>
														<?=cutStr($product['productname'],22)?>
													</b>
												</font>
											</a>
											<?
												if(_array($product['option1']) || _array($product['option2']) || !_empty($product['optvalue'])){
											?>
													<span class="order_option_wrap">
														<br/>
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
											<?
												}	
											?>
											<br />
											<span class="order_constraint">
											<? if($product['bankonly'] == 'Y'){ ?>���ݰ���,<? } ?>
											<? if($product['setquota'] == 'Y'){ ?>������,<? } ?>
											<?
												// ���� �� ���� ����
												$sptxt = array();
												if($product['cateAuth']['coupon'] == 'N') array_push($sptxt,'������������Ұ�');
												if($product['cateAuth']['reserve'] == 'N') array_push($sptxt,'����������Ұ�');
												if($product['cateAuth']['gift'] == 'N') array_push($sptxt,'����ǰ����Ұ�');
												if($product['cateAuth']['refund'] == 'N') array_push($sptxt,'��ȯ/��ǰ�Ұ�');
												if(_array($sptxt)){
													echo implode(',<br/>',$sptxt);
												}
											?>
											</span>
										</td>
									</tr>
									<tr>
										<td class="order_pr_title">
											��&nbsp;&nbsp;&nbsp;&nbsp;�� :
										</td>
										<td class="order_pr_infomsg">
										<?=$product['quantity']?>
										</td>
									</tr>
									<!--<tr>
										<td class="order_pr_title">
											�ǸŰ� :
										</td>
										<td class="order_pr_infomsg">
											<?=number_format($product['sellprice']-$product['group_discount'])?>��
										</td>
									</tr>
									-->
									<tr>
										<td class="order_pr_title">
											���Ű� :
										</td>
										<td class="order_pr_infomsg">
											<?=number_format($product['realprice'])?>��
										</td>
									</tr>
									<tr>
										<td class="order_pr_title">
											������ :
										</td>
										<td class="order_pr_infomsg">
											<?=number_format($product['reserve'])?>��
										</td>
									</tr>
									<tr>
										<td class="order_pr_title">
											��ۺ� :
										</td>
										<td class="order_pr_infomsg">
											<? if($product['deli_price']>0){
												if($row->deli=="Y"){ ?>������<br><?=number_format($product['deli_price']*$product['quantity'])?>��
												<?		}else if($row->deli=="N") { ?>������<br /><?=number_format($product['deli_price'])?>��<?		}
												}else if($product['deli']=="F" || $product['deli']=="G"){ echo ($product['deli']=="F"?'��������':'����')?><?
												}else{
												if($product['vender'] > 0) echo '������ �⺻���';
												else echo '�⺻��ۺ�';
												}
											?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		<?			}// end for
				} // end foreach
			} // end if
		?>	
		</tbody>
		<tfoot>
			<tr>
				<td class="order_pr_total_wrap">
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td>�����ݾ�&nbsp;:&nbsp;<span><?=number_format($basketItems['sumprice'])?></span>��</td>
							<td><span>(��ۺ� <?=number_format($basketItems['deli_price'])?>��)</span></td>
						</tr>
					</table>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
<!-- �ֹ���ǰ ���̺� END -->
<!-- �׷���å ���̺� START -->
<?
// �׷� ���� �Ǵ� ����
	if($sumprice>0 && !_empty($group_type)) {
		$salemoney=0;
		$salereserve=0;
		switch($group_type){
			case 'SW':
				$salemoney=$group_addmoney; break;
			case 'SP':
				$salemoney=substr(((int)($sumprice*($group_addmoney/100))),0,-2)."00"; break;
			case 'RW':
				$salereserve=$group_addmoney; break;
			case 'RP':
				$salereserve=$reserve*($group_addmoney-1); break;
			case 'RQ':
				$salereserve=substr(((int)($sumprice*($group_addmoney/100))),0,-2)."00"; break;
		}
		if(!_empty($_ShopInfo->getMemid()) && !_empty($group_code) && substr($group_code,0,1)!="M") $arr_dctype=array("B"=>"����","C"=>"ī��","N"=>"");
?>
<div class="order_wrap order_gap">
	<div class="order_group_wrap">
		<B><?=$name?></B>���� <B><FONT COLOR="#EE1A02">[<?=$org_group_name?>]</FONT></B>ȸ���Դϴ�.<br />
		<FONT COLOR="#EE1A02"><B><?=number_format($group_usemoney)?>��</B></FONT> �̻� <?=$arr_dctype[$group_payment]?>���Ž�,
		<?
			if($group_type=="RW") echo "�����ݿ� <font color=\"#EE1A02\"><B>".number_format($group_addmoney)."</B>��</font>�� �߰� ������ �帳�ϴ�.";
			else if($group_type=="RP") echo "���� �������� <font color=\"#EE1A02\"><B>".number_format($group_addmoney)."</B>��</font>�� ������ �帳�ϴ�.";
			else if($group_type=="SW") echo "���űݾ� <font color=\"#EE1A02\"><B>".number_format($group_addmoney)."</B>��</font>�� �߰� ������ �帳�ϴ�.";
			else if($group_type=="SP") echo "���űݾ��� <font color=\"#EE1A02\"><B>".number_format($group_addmoney)."</B>%</font>�� �߰� ������ �帳�ϴ�.";
		?>
	</div>
</div>
<?	
	} 
?>
<!-- �׷���å ���̺� END -->
<!-- �ֹ����� START -->
<div class="order_wrap">
	<!-- �ֹ��� ���� �Է� START -->
	<div class="order_title">
		<h4>�ֹ������� �Է�</h4>
	</div>
	<div class="order_info_table_wrap">
		<table cellpadding="0" cellspacing="0" border="0" class="order_table">
			<tr>
				<th>
					�̸�
				</th>
				<td>
					<input type="text" name="sender_name" class="mobile_input mobile_text" value="<?=$name?>" />
				</td>
			</tr>
			<tr>
				<th>
					����ó
				</th>
				<td>
					<input type="tel" name="sender_tel1" class="mobile_input mobile_number" value="<?=$mobile[0]?>" maxlength="4"/> -
					<input type="tel" name="sender_tel2" class="mobile_input mobile_number" value="<?=$mobile[1]?>" maxlength="4"/> -
					<input type="tel" name="sender_tel3" class="mobile_input mobile_number" value="<?=$mobile[2]?>" maxlength="4"/>
				</td>
			</tr>
			<tr>
				<th>
					�̸���
				</th>
				<td>
					<input type="email" name="sender_email" id="o_email" class="mobile_input mobile_text" maxlength="80" value="<?=$email?>" />
				</td>
			</tr>
		</table>
	</div>
	<!-- �ֹ��� ���� �Է� END -->
	<!-- ��� ���� �Է� START -->
	<div class="order_title delivery_title">
		<h4>������� �Է�</h4> <div class="btn_same"><button type="button" onClick="SameCheck();">�ֹ��ڿ� ����</button></div>
	</div>
	<div class="delivery_info_table_wrap">
		<table cellpadding="0" cellspacing="0" border="0" class="order_table">
			<tr>
				<th>
					�޴»��
				</th>
				<td>
					<input type="text" id="o_name" name="receiver_name" class="mobile_input mobile_text" />
				</td>
			</tr>
			<tr>
				<th>
					��ȭ��ȣ
				</th>
				<td>
					<input type="tel" name="receiver_tel11" id="o_number2" class="mobile_input mobile_number" maxlength="4" /> - 
					<input type="tel" name="receiver_tel12" id="o_number2" class="mobile_input mobile_number" maxlength="4" /> - 
					<input type="tel" name="receiver_tel13" id="o_number2" class="mobile_input mobile_number" maxlength="4" />
				</td>
			</tr>
			<tr>
				<th>�����ȭ
				</th>
				<td>
					<input type="tel" name="receiver_tel21" id="o_number2" class="mobile_input mobile_number" maxlength="4"> - 
					<input type="tel" name="receiver_tel22" id="o_number2" class="mobile_input mobile_number" maxlength="4"> - 
					<input type="tel" name="receiver_tel23" id="o_number2" class="mobile_input mobile_number" maxlength="4">
				</td>
			</tr>
			<tr>
				<th>
					�ּ�
				</th>
				<td class="order_deli_type1">
					<input type="text" name="rpost1" class="mobile_input post_field mobile_number" maxlength="4" /> - <input type="text" name="rpost2" class="mobile_input post_field mobile_number" maxlength="4"/>
					<button type="button" onClick="javascript:get_post();" class="btn_address_search"><span>�ּ�ã��</span></button><br/>
					<input type="text" name="raddr1" class="mobile_input address_field mobile_text" value="" /><br/>
					<input type="text" name="raddr2" class="mobile_input address_field mobile_text" value="" />
				</td>
			</tr>
			<tr>
				<th>
					���޻���
				</th>
				<td class="order_deli_type1">
					<textarea name="order_prmsg" id="o_text" class="order_textarea" rows="5"></textarea>
				</td>
			</tr>
		</table>
	</div>
	<!-- ��� ���� �Է� END -->
	<!-- ��ȸ�� �������� �������� START -->
	<?
		if(strlen($_ShopInfo->getMemid()) <= 0){
	?>
		<div class="order_title">
			<h4>��ȸ�� �������� ��������</h4>
		</div>
		<div class="persnal_info_wrap">
			<div class="persnal_clause">
				<?=strip_tags($privercybody, "<p>")?>
			</div>
			<div class="persnal_clause_btn_wrap">
				<input type="radio" id="idx_dongiY" name="dongi" value="Y" /><label for="idx_dongiY" class="clause_btn_true">������</label>&nbsp;&nbsp;&nbsp;
				<input type="radio" id="idx_dongiN" name="dongi" value="N" /><label for="idx_dongiN" class="clause_btn_false">���� ����</label>
			</div>
		</div>
	<?
		}
	?>
	<!-- ��ȸ�� �������� �������� END -->
	<!-- ���Ž� ���� ���� START -->
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
	<div class="order_title">
		<h4>���Ž� ���� ����</h4>
	</div>
	<div class="order_benefit_wrap">
		<table cellpadding="0" cellspacing="0" border="0" class="order_table">
				<!-- ���� ���� ���� START -->
			<tr>
				<th>
					����
				</th>
							<?
				if($_data->coupon_ok=="Y" && checkGroupUseCoupon() && $couponable) { //���� ��밡�� ���� üũ 
			?>
				<td class="order_benefit_type1">
					<input type="text" name="coupon_price" id="coupon_price" onclick="coupon_check()" class="st02_1 mobile_input" maxlength="8" value="0" readonly="readonly" /> ��
					 <!-- <a href="javascript:coupon_check()" onmouseover="window.status='��������';return true;" class>���� ����</a>
					 <a href="javascript:resetCoupon()">�������� ���</a><br> -->
					 <button type="button" onClick="coupon_check();">��������</button>
					 <button type="button" onClick="coupon_check();">�������</button><br/>
				</td>
			<?}else{?>
				<td>
					������� ���ǿ� �ش���� �ʽ��ϴ�.
				</td>
			<?}?>
			</tr>
			<!-- ���� ���� ���� END -->
			<!-- ������ ���� ���� START -->
		
			<tr>
				<th class="order_benefit_type2">
					������
				</th>
				<?
					if($reserveuseable or $okreserve > 0){
						if(($user_reserve -$_data->reserve_maxuse) > 0){
				?>
				<td class="order_benefit_type2" style="padding:2px 0px;">
					<input type="hidden" name="okreserve" value="<?=$okreserve?>" />
					���� ������ : <input type="text" name="oriuser_reserve" class="st02_1 mobile_input save_reserve" maxlength="8" value="<?=number_format($user_reserve)?>" readonly="readonly" /> ��
					<br/>��� ������ :
					<input type="text" name="usereserve" id="usereserve" class="st02_1 mobile_input" maxlength="8" value="0"  <?=($okreserve<1)?'disabled="disabled"':''?>  /> �� ���<br />
					<!--<span style="color:red"> <span style="font-weight:bold"><?=number_format($okreserve)?>��</span> ���� ���������� ����Ͽ� �����ϽǼ� �ֽ��ϴ�.</span><br />-->
				</td>
				<?
						}else{
				?>
					<td>
						���������� <span class="order_special_char"><?=number_format($_data->reserve_maxuse)?>��</span> �̻� �� ��� ��밡���մϴ�.
					</td>
				<?
						}
			}else{
					
				?>
				<td>
				������ ��� ���ǿ� �ش���� �ʽ��ϴ�.
				</td>
				<?}?>
			</tr>
	
			<!-- ������ ���� ���� END -->
		</table>
		
	</div>
	<?
		if($_data->coupon_ok !="Y" || !$couponable) { 
	?>
			<input type="hidden" name="coupon_price" id="coupon_price" value="0" />
	<?
		}

		if(!$reserveuseable ||  $okreserve <= 0 || ($user_reserve -$_data->reserve_maxuse) <= 0) {
	?>
			<input type="hidden" name="okreserve" value="0" />
			<input type="hidden" name="oriuser_reserve" class="st02_1" maxlength="8" value="<?=number_format($user_reserve)?>" />
			<input type="hidden" name="usereserve" id="coupon_price" value="0" />
	<?
		}
	}else{
	?>
		<input type="hidden" name="usereserve" id="usereserve" value="0" />
		<input type="hidden" name="coupon_price" id="coupon_price" value="0" />
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
	<!-- ���Ž� ���� ���� END -->
	<!-- ����ǰ ���� ���� START -->
	<?
		if( $giftInfoSetArray[0] == "C" OR ( $giftInfoSetArray[0] == "M" AND !_empty($_ShopInfo->getMemid()) ) ){
	?>
	<div id="giftSelectArea">
		<div class="order_title">
			<h4>���� ����ǰ </h4>
		</div>
		<div class="freegift_wrap">
			<input type="hidden" name="gift01" id="gift01" class="mobile_input" maxlength="8" readonly value="<?=$basketItems['gift_price']?>" />
			<table cellpadding="0" cellspacing="0" class="freegift_table">
				<tr>
					<th>����ǰ ����</th>
					<td>
						<div id="noGiftOptionArea">���� ������ ����ǰ�� �����ϴ�.</div>
						<table cellpadding="0" cellspacing="0" border="0" class="noborder" width="100%" id="giftOptionBox" style="display:none">
							<tr>
								<td class="freegift_image_area">
									<img src="/images/no_img.gif" id="gift_img" height="50"/>
								</td>
							</tr>
							<tr>
								<td class="freegift_choice">
									<div style="width:100%; text-align:left;">
										<select name="giftval_seq" class="st13_1_1">
											<option value="">:: ����ǰ���� ::</option>
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div id="giftOptionArea">
										<table class="noborder" cellpadding="0" cellspacing="0">
											<tr>
												<td>�ɼ�1</td>
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
					<th class="freegift_sendmsg_wrap">��û����</th>
					<td class="freegift_sendmsg_wrap">
						<textarea name="gift_msg" class="mobile_input freegift_sendmsg" maxlength="50" disabled="disabled" placeholder="����ǰ ���� ��û���� �Է�(50��)" /></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<?
		}
	?>
	<!-- ����ǰ ���� ���� END -->
	<!-- ���� ��� ���� START -->
	<?
		$arrpayinfo=explode("=",$_data->bank_account);
		//$arrcardcom=array("A"=>"[<font color=red>KCP.CO.KR</font>]","B"=>"[<font color=red>dacompay.net (���������ڻ�ŷ�)</font>]","C"=>"[<font color=red>allthegate.com (�ô�����Ʈ)</font>]","D"=>"[<font color=red>inicis.com (�̴Ͻý�)</font>]");
		//$cardid_info=GetEscrowType($_data->card_id);
	?>
	<div class="order_title">
		<h4>������� ����</h4>
	</div>
	<div class="payment_type_wrap">
		<table class="payment_wrap" cellpadding="0" cellspacing="0">
			<tr>
				<th>
				�������
				</th>
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
					<input type="radio" id="paytype_1" class="paytype" name="paymethod" value="B" onClick="showBankAccount('show')"><label for="paytype_1"><span class="btn_payment btn_paytype paytype_1" onClick="paymentControl('bankaccount');">&nbsp;������&nbsp;</span></label>
					<?
						}
						if(strlen($_data->card_id) > 0){
							if($paytype_row->use_creditcard == 'Y' && $usepg_row->pg_use == 'Y'){
					?>
					<input type="radio" id="paytype_2" class="paytype" name="paymethod" value="C" onClick="showBankAccount('hide')"><label for="paytype_2"><span class="btn_payment btn_paytype paytype_2" onClick="paymentControl('creditcard');">�ſ�ī��</span></label>
					<?
							}
						}
						if(strlen($_data->trans_id)>0) {
							if($paytype_row->use_transferaccount == 'Y' && $usepg_row->pg_use == 'Y'){
					?>
					<input type="radio" id="paytype_3" class="paytype" name="paymethod" value="V" onClick="showBankAccount('hide')"><label for="paytype_3"><span class="btn_payment btn_paytype paytype_3" onClick="paymentControl('transferaccount');">������ü</span></label>
					<?
							}
						}
						if(strlen($_data->virtual_id)>0) {
							if($paytype_row->use_virtualaccount == 'Y' && $usepg_row->pg_use == 'Y'){
					?>
					<input type="radio" id="paytype_4" class="paytype" name="paymethod" value="V" onClick="showBankAccount('hide')"><label for="paytype_4"><span class="btn_payment btn_paytype paytype_4" onClick="paymentControl('virtualaccount');">�������</span></label>
					<?
							}
						}
					?>
				</td>
			</tr>
			<tr id="pay_account_list" style="display:none">
				<th class="borderline">
					�Աݰ���
				</th>
				<td class="borderline">
					<table class="pay_account_table" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td class="account_select_wrap">
								<select name="pay_data1" onChange="accountControl(this.value);">
									<option value="dont">�����ϼ���</option>
									<?								
										//������
										if($escrow_info["onlycard"]!="Y" || (int)$banklast_price<100000) {
											if(preg_match("/^(Y|N)$/", $_data->payment_type)) {//��������� ������ OR �¶��ΰ����� ���õǾ��� ���
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
										//������
										if($escrow_info["onlycard"]!="Y" || (int)$banklast_price<100000) {
											if(preg_match("/^(Y|N)$/", $_data->payment_type)) {//��������� ������ OR �¶��ΰ����� ���õǾ��� ���
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
										<td class="account_info_menu">
										���¹�ȣ
										</td>
										<td class="separation">
										:
										</td>
										<td id="pay_account">
										</td>
									</tr>
									<tr>
										<td class="account_info_menu">
										��&nbsp;&nbsp;��&nbsp;&nbsp;��
										</td>
										<td class="separation">
										:
										</td>
										<td id="pay_holder">
										</td>
									</tr>
									<tr>
										<td class="account_info_menu">
											�Ա��ڸ�
										</td>
										<td class="separation">
										:
										</td>
										<td class="transfername_wrap">
											<input type="text" name="bankname" class="mobile_input transfetname" value="" placeholder="�Ա��ڸ��� �ٸ� ��� �Է�"/>
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

	<div class="totalpay_info_table">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<caption>�� ���� ����</caption>
			<tr>
				<th>�հ�</th>
				<td><?=number_format($sumprice+$sumpricevat)?></span>��</td>
			</tr>
			<? if(!_empty($_ShopInfo->getMemid())){ ?>
			<tr>
				<th>������ ���</th>
				<td><span id="disp_reserve">0</span>��</td>
			</tr>
			<? if($_data->coupon_ok =="Y" && $couponable){ ?>
			<tr>
				<th>��������</th>
				<td><span id="disp_coupon">0</span>��</td>
			</tr>
			<? } ?>
			<tr>
				<th>�������</th>
				<td><span id="disp_groupdiscount">0</span>��</td>
			</tr>
			<? } ?>
			<tr>
				<th>��ۺ�</th>
				<td>
					<span id="disp_deliprice"><?=number_format($basketItems['deli_price'])?></span>��
					<input type='hidden' name='disp_deliprice_temp' id='disp_deliprice_temp' value='0'>
				</td>
			</tr>
			<tr>
				<th>���� �����ݾ�</th>
				<td><span id="disp_last_price" style="font-size:18px; font-family:Tahoma;"><?=number_format($basketItems['sumprice']+$basketItems['deli_price']+$basketItems['sumpricevat'])?></span>��</td>
			</tr>
		</table>
	</div>
</div>
<!-- �ֹ����� END -->
<br />
<!-- //
<!-- ��ư -->
<section class="basic_btn_area btn_w1">

<? if($row_cfg[use_bank]!="Y" && $row_cfg[use_creditcard]!="Y" && $row_cfg[use_mobilephone]!="Y") { ?>
<a href="#" class="button black bigrounded" onClick="javascript:alert('��������� �����Ǿ����� �ʽ��ϴ�. �����ڿ��� �����ϼ���~')">�����ϱ�</a>
<? } else { ?>
<a href="#" class="button black bigrounded" onClick="javascript:CheckForm()">�����ϱ�</a>
<? }?>
<a href="#" class="button white bigrounded" onClick="javascript:ordercancel('cancel')">�ֹ����</a>
</section>
<!-- //��ư -->
<Script>
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
		break;
		case 'creditcard':
			$j('.btn_paytype').removeClass('selected');
			$j('.paytype_2').addClass('selected');			
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

// ���� ���� ����ǰ ���� ����
function giftchoices(gprice){
	gprice = parseInt(gprice);
	var noGift = ($j('input[name=apply_gift]').val() == 'N');
	if(!noGift){
		if(isNaN(gprice)) gprice = parseInt($j('input[name=gift01]').val());
		if(isNaN(gprice) || gprice < 1) gprice = 0;
	}else{
		gprice = 0;
	}


	$j('input[name=gift01]').val(gprice);
	/*
	var $gift = $j("select[name=giftval_seq] option:selected");
	var index =$j("select[name=giftval_seq] option").index($gift);
	if(index > 0) alert('����ǰ ������ �ʱ�ȭ �˴ϴ�.');
	*/
	if(gprice >= mingiftprice){
		//$j.post( '/json_order.php',{'act':'getGife','gift_price':gprice},function(response, textStatus, jqXHR){ console.log('data.length');},"json");
		if($j('#giftSelectArea')) $j('#giftSelectArea').css('display','');
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
					str += '<tr><td style="width:50px;">�ɼ� '+(idx+1)+' :</td>';
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
						str += '<tr><td style="width:50px;">�ɼ� '+(p)+' :</td>';
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


/*

$( "select" )
  .change(function () {
    var str = "";

    $( "div" ).text( str );
  })
  .change();

onchange="secGift(this.value);"
*/
function addGiftSelSelect(itm){
	$j('<option value="'+itm.gift_regdate+'">'+itm.gift_name+'</option>').data('imgsrc',itm.gift_image).data('options',itm.options).appendTo("select[name=giftval_seq]");

}
</script>
