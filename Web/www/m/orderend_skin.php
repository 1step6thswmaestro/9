<div class="orderend_container">
	<?
		if (preg_match("/^(B){1}/", $_ord->paymethod) || (preg_match("/^(V|O|Q|C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) {
	?>
	<div class="orderend_msg_wrap">주문이 완료 되었습니다.</div>
	<?
		}
	?>
	<div class="orderend_wrap">
		<h2>주문 내역</h2>
		<div class="orderend_info_wrap">
			<table cellpadding="0" cellspacing="0" class="orderend_table">
				<?
					$sql = "SELECT productcode,productname,price,reserve,opt1_name,opt2_name,tempkey,addcode,quantity,order_prmsg,selfcode,package_idx,assemble_idx,assemble_info ";
					$sql.= "FROM tblorderproduct WHERE ordercode='".$ordercode."' ORDER BY productcode ASC ";
					$result=mysql_query($sql,get_db_conn());
					$sumprice=0;
					$sumreserve=0;
					$totprice=0;
					$totreserve=0;
					$totquantity=0;
					$cnt=0;
					unset($etcdata);
					unset($prdata);
					while($row=mysql_fetch_object($result)) {
						$optvalue="";
						if(ereg("^(\[OPTG)([0-9]{3})(\])$",$row->opt1_name)) {
							$optioncode=$row->opt1_name;
							$row->opt1_name="";
							$sql = "SELECT opt_name FROM tblorderoption WHERE ordercode='".$ordercode."' AND productcode='".$row->productcode."' ";
							$sql.= "AND opt_idx='".$optioncode."' ";
							$result2=mysql_query($sql,get_db_conn());
							if($row2=mysql_fetch_object($result2)) {
								$optvalue=$row2->opt_name;
							}
							mysql_free_result($result2);
						}

						$isnot=false;
						if (substr($row->productcode,0,3)!="999") {
							if(substr($row->productcode,0,3)!="COU") {
								$no++;
								$isnot=true;
								$totquantity+=$row->quantity;
							}
							$sumreserve=$row->reserve*$row->quantity;
							$totreserve+=$sumreserve;
						}
						if(ereg("^(COU)([0-9]{8})(X)$",$row->productcode)) {				#쿠폰
							$etcdata[]=$row;
							continue;
						} else if(ereg("^(9999999999)([0-9]{1})(X)$",$row->productcode)) {
							#99999999999X : 현금결제시 결제금액에서 추가적립/할인
							#99999999998X : 에스크로 결제시 수수료
							#99999999997X : 부가세(VAT)
							#99999999990X : 상품배송비
							$etcdata[]=$row;
							continue;
						} else {															#진짜상품
							$prdata[]=$row;
						}
						$sumprice=$row->price*$row->quantity;
						$totprice+=$sumprice;
				?>
				<tr>
					<!--<th>상품명</th>-->
					<td colspan="2" style="padding:10px;">
						상품명 : <span class="point2"><?=cutStr($row->productname,32)?></span>
						<?
						if(strlen($row->opt1_name)>0 || strlen($row->opt2_name)>0 || strlen($optvalue)>0) {
							if(strlen($row->opt1_name)>0){
						?>
								<br/><?=$row->opt1_name?>,
						<?
							}
							if(strlen($row->opt2_name)>0) {
						?>
								<?=$row->opt2_name?>
						<?
							}
							if(strlen($optvalue)>0) {
						?>
								<?=$optvalue?>
						<?
							}
							$row->addcode="";
						}
						?>
					</td>
				</tr>
				<?if($isnot==true){?>
				<tr>
					<th>수량</th>
					<td><?=($isnot==true?$row->quantity:'')?></td>
				</tr>
				<?}?>
				<tr>
					<th>적립금</th>
					<td><?=number_format($sumreserve)?> 원</td>
				</tr>
				<tr>
					<th class="orderend_info_last">판매가</th>
					<td class="orderend_info_last"><?=number_format($sumprice)?> 원</td>
				</tr>
				<?
					}
					mysql_free_result($result);
				?>
			</table>
			<div class="orderend_total_info">
				주문 합계 : <span class="orderend_payinfo"><?=number_format($totprice)?></span>원
			</div>
		</div>
	</div>

	<div class="orderend_wrap">
		<h2>추가비용 내역</h2>
		<div class="orderend_info_wrap">
			<table cellpadding="0" cellspacing="0" class="orderend_table">
			<?
				$plus_etcprice=0;
				$etcreserve=0;
				$tot_etcdata=0;
				$pr_article = '';
				
				$tot_etcdata=count($etcdata);
				if($tot_etcdata > 0){
					for($i=0;$i<$tot_etcdata;$i++) {

						if(($etcdata[$i]->productcode == "99999999998X") || ($etcdata[$i]->productcode=="99999999990X") || ($etcdata[$i]->productcode=="99999999997X")){
							if($etcdata[$i]->productcode == "99999999998X"){
								$plus_etcprice+=$etcdata[$i]->price;
								$etcreserve+=$etcdata[$i]->reserve;
								$pr_article = '결제 수수료';
							}
							if($etcdata[$i]->productcode=="99999999990X"){
								$pr_article = '배송료';
							}
							if($etcdata[$i]->productcode=="99999999997X"){
								$pr_article = '부가세(VAT)';
							}
				?>
				<tr>
					<td colspan="2" style="padding:10px;"><b>항목 : <?=$pr_article?></b></td>
				</tr>
				<tr>
					<th>금액</th>
					<td><?=($etcdata[$i]->price!=0?number_format($etcdata[$i]->price).'원':'')?></td>
				</tr>
				<tr>
					<th class="orderend_info_last">적용대상</th>
					<td class="orderend_info_last"><?=$etcdata[$i]->order_prmsg?></td>
				</tr>
				<?
							}
						}
					}else{
				?>
				<tr>
					<td class="orderend_info_last" colspan="3" align="center">
						추가비용 내역이 없습니다.
					</td>
				</tr>

				<?
					}
					$plus_etcprice+=$_ord->deli_price;
				?>
			</table>
			<div class="orderend_total_info">
				추가비용 합계 : <span class="orderend_payinfo"><?=number_format($plus_etcprice)?></span>원
			</div>
		</div>
	</div>

	<div class="orderend_wrap">
		<h2>추가 할인 및 적립내역</h2>
		<div class="orderend_info_wrap">
			<table cellpadding="0" cellspacing="0" class="orderend_table">
				<?
					$dc_price=(int)$_ord->dc_price;
					$salemoney=0;
					$salereserve=0;
					$dc_article = '';
					$dc_state = false;
					if($dc_price<>0) {
						$dc_state = true;
						if($dc_price>0) $salereserve=$dc_price;
						else $salemoney=-$dc_price;
						if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
							$sql = "SELECT b.group_name FROM tblmember a, tblmembergroup b ";
							$sql.= "WHERE a.id='".$_ord->id."' AND b.group_code=a.group_code AND MID(b.group_code,1,1)!='M' ";
							$result=mysql_query($sql,get_db_conn());
							if($row=mysql_fetch_object($result)) {
								$group_name=$row->group_name;
							}
							mysql_free_result($result);
						}
						
						if($salemoney > 0){
							$dc_article = '그룹할인';
						}
						if($salereserve>0){
							$dc_article = '그룹적립';
						}
				?>
				<tr>
					<th>항목</th>
					<td><?=$dc_article?> (<?=$group_name?>)</td>
				</tr>
				<tr>
					<th>금액</th>
					<td>
						<?=($salemoney>0?"-".number_format($salemoney).'원':'')?>
						<?=($salereserve>0?"+ ".number_format($salereserve).'원':'')?>
					</td>
				</tr>
				<tr>
					<th <?if($_ord->reserve>0){?>class="orderend_info_last"<?}?>>적용대상</th>
					<td <?if($_ord->reserve>0){?>class="orderend_info_last"<?}?>>주문서 전체 적용</td>
				</tr>
				<?
					}
					if($_ord->reserve>0){
						$dc_state = true;
				?>
				<tr>
					<th>항목</th>
					<td>적립금 사용</td>
				</tr>
				<tr>
					<th>금액</th>
					<td>- <?=number_format($_ord->reserve)?> 원</td>
				</tr>
				<tr>
					<th>적용대상</th>
					<td>주문서 전체 적용</td>
				</tr>
				<?}?>
				<?if($dc_state == false){?>
				<tr>
					<td align="center">해당사항이 없습니다.</td>
				</tr>
				<?}?>
			</table>
		</div>
	</div>

	<div class="orderend_wrap">
		<h2>최종 결제내역</h2>
		<div class="orderend_info_wrap">
			<table cellpadding="0" cellspacing="0" class="orderend_total_table">
				<tr>
					<td>주문금액 : <?=number_format($totprice)?>원</td>
				</tr>
				<tr>
					<td>추가비용 : <?=number_format($plus_etcprice)?>원</td>
				</tr>
				<tr>
					<td>
						<?
							$tot_dc_price = 0;
							$tot_dc_price = $salemoney+$_ord->reserve;
						?>
						할인금액 : -<?=number_format($tot_dc_price)?>원
					</td>
				</tr>
				<tr>
					<td>
						<?
							$tot_price = 0;
							$tot_price = ($totprice+$plus_etcprice)-$tot_dc_price;
						?>
						결제금액 : <span class="point3"><?=number_format($tot_price)?>원</span>
					</td>
				</tr>
				<!-- <tr>
					<td>
						적립금액 : 배송후<?//=number_format($salereserve)?>원
					</td>
				</tr> -->
			</table>
		</div>
	</div>

	<div class="orderend_wrap">
		<h2>결제방법</h2>
		<div class="orderend_info_wrap">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<th class="orderend_info_last">결제방법</th>
					<td class="orderend_info_last">
						<?
							$orderend_paytype='';
							if(preg_match("/^(V|C|P|M|B|O|Q){1}/", $_ord->paymethod)){
								
								$arpm=array("V"=>"실시간계좌이체","C"=>"신용카드","P"=>"매매보호 - 신용카드", "M"=>"핸드폰", "B"=>"무통장 입금", "O"=>"가상계좌", "Q"=>"매매보호 - 가상계좌");

								$orderend_paytype = $arpm[substr($_ord->paymethod,0,1)];
							}else{
								$orderend_paytype = "거래실패";
							}
						?>
						<?=$orderend_paytype?>
					</td>
				</tr>
				<tr>
					<th>결제정보</th>
					<td style="padding:0px;">
						<?
							if(preg_match("/^(V|C|P|M){1}/", $_ord->paymethod)) {
								if ($_ord->pay_flag=="0000") {
									if(preg_match("/^(C|P){1}/", $_ord->paymethod)) {
										echo "결제성공 - 승인번호 : ".$_ord->pay_auth_no." ";
									} else {
										echo "결제가 <font color=blue>정상처리</font> 되었습니다.";
									}
								} else if(strlen($_ord->pay_flag)>0){
									echo "거래결과 : <font color=red><b><u>".$_ord->pay_data."</u></b></font>\n";
								}else{
									echo "\n<font color=red>(지불실패)</font>";
								}
								if (preg_match("/^(C|P|M){1}/", $_ord->paymethod) && $_data->card_payfee>0) echo "<br>&nbsp\n".$arpm[substr($_ord->paymethod,0,1)]." 결제시 현금 할인가 적용이 안됩니다.";
							} else if (preg_match("/^(B|O|Q){1}/", $_ord->paymethod)) {

								if(preg_match("/^(B|O|Q){1}/", $_ord->paymethod)){
									$account_info = explode(' ',$_ord->pay_data);
									$account_holder_temp = explode(':',$account_info[2]);
									$account_holder = '';
									if(substr($account_holder_temp[1],-1,1) == ')'){
										$account_holder = substr($account_holder_temp[1],0,-1);
									}else{
										$account_holder = $account_holder_temp[1];
									}
						?>
							<table cellpadding="0" cellspacing="0" style="width:100%; border:0px solid #dddddd;">
								<tr>
									<td width="60" align="left" bgcolor="#f2f2f2">은행명</td>
									<td>&nbsp;<?=$account_info[0]?></td>
								</tr>
								<tr>
									<td width="60" align="left" bgcolor="#f2f2f2">입금계좌</td>
									<td>&nbsp;<?=$account_info[1]?></td>
								</tr>
								<tr>
									<td width="60" align="left" bgcolor="#f2f2f2">예금주</td>
									<td>&nbsp;<?=$account_holder?></td>
								</tr>
								<tr>
									<td colspan="2" style="border-bottom:none;">입금확인 후 배송 됩니다.</td>
								</tr>
							</table>
						<?
								}
							}
						?>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="orderend_wrap">
		<h2>주문자정보</h2>
		<div class="orderend_info_wrap">
			<table>
				<tr>
					<th>이름</th>
					<td><?=$_ord->sender_name?></td>
				</tr>
				<tr>
					<th>전화번호</th>
					<td><?=$_ord->sender_tel?></td>
				</tr>
				<tr>
					<th>이메일</th>
					<td><?=$_ord->sender_email?></td>
				</tr>
			</table>
		</div>
	</div>

	<div class="orderend_wrap">
		<h2>배송정보</h2>
		<div class="orderend_info_wrap">
			<table>
				<tr>
					<th>이름</th>
					<td><?=$_ord->receiver_name?></td>
				</tr>
				<tr>
					<th>전화번호</th>
					<td><?=$_ord->receiver_tel1?></td>
				</tr>
				<tr>
					<th>비상전화</th>
					<td><?=$_ord->receiver_tel2?></td>
				</tr>
				<tr>
					<th>주소</th>
					<td><?=$_ord->receiver_addr?></td>
				</tr>
				<tr>
					<th>요청사항</th>
					<td><?=ereg_replace("\r\n","<br>",$_ord->order_msg)?></td>
				</tr>
			</table>
		</div>
	</div>

	<?$totreserve+=$salereserve+$etcreserve;?>
	<div class="orderend_wrap">
		<div class="orderend_title">
		</div>
		<div class="orderend_info_wrap" style="text-align:center;">
		<?
	if(preg_match("/^(B){1}/", $_ord->paymethod) || (preg_match("/^(V|O|Q|C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) {
		if(strlen($_ord->ordercode)==20 && substr($_ord->ordercode,-1)!="X") {
			echo "<font color=\"#FF6600\"><b>".$_ord->sender_name."님의 주문이 완료되었습니다.</b></font><br>\n";
			if ($totreserve>0) echo "귀하의 제품 구입에 따른 적립금 <font color=\"#FF6600\"><b>".number_format($totreserve)."원</b></font>은 배송과 함께 바로 적립됩니다.<br>\n";
		} else {
			echo "주문이 완료되었습니다.<br>\n";
			echo "귀하의 주문확인 번호는 <font color=0000a0><b>".substr($_ord->id,1,6)."</b></font>입니다.<br>\n";
		}
	} else if (preg_match("/^(V|O|Q|C|P|M)$/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")!=0 ) {
		echo "<font color=red size=3><b>주문이 실패되었습니다.</b></font><br>\n";
	}

	if(preg_match("/^(B){1}/", $_ord->paymethod) || (preg_match("/^(O|Q){1}/", $_ord->paymethod) && $_ord->pay_flag=="0000")) {
		echo "입금방법이 무통장입금의 경우 계좌번호를 메모하세요.<br>저희가 입금확인 후 바로 보내드립니다.<br><br>\n";
	} else if(preg_match("/^(C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0) {
		echo "저희가 확인 후 바로 보내드립니다.<br><br>\n";
	}

	if ((preg_match("/^(B){1}/", $_ord->paymethod) || (preg_match("/^(V|O|Q|C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) && strlen($_data->orderend_msg)>0) {
		echo ereg_replace("\n","<br>",$_data->orderend_msg);
		echo "<br>\n";
	}
?>	
	<img src="/lib/barcode.php?str=<?=$_ord->ordercode?>" >
		</div>
	</div>
	<div class="orderend_close_wrap">
		<button type="button" onClick="orderendClose();" class="button white bigrounded">확인</button>
	</div>
<div>
<script>
function orderendClose(){
	location.href="./";
}
</script>
