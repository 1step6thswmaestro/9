<div class="orderend_container">
	<?
		if (preg_match("/^(B){1}/", $_ord->paymethod) || (preg_match("/^(V|O|Q|C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) {
	?>
	<div class="orderend_msg_wrap">�ֹ��� �Ϸ� �Ǿ����ϴ�.</div>
	<?
		}
	?>
	<div class="orderend_wrap">
		<h2>�ֹ� ����</h2>
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
						if(ereg("^(COU)([0-9]{8})(X)$",$row->productcode)) {				#����
							$etcdata[]=$row;
							continue;
						} else if(ereg("^(9999999999)([0-9]{1})(X)$",$row->productcode)) {
							#99999999999X : ���ݰ����� �����ݾ׿��� �߰�����/����
							#99999999998X : ����ũ�� ������ ������
							#99999999997X : �ΰ���(VAT)
							#99999999990X : ��ǰ��ۺ�
							$etcdata[]=$row;
							continue;
						} else {															#��¥��ǰ
							$prdata[]=$row;
						}
						$sumprice=$row->price*$row->quantity;
						$totprice+=$sumprice;
				?>
				<tr>
					<!--<th>��ǰ��</th>-->
					<td colspan="2" style="padding:10px;">
						��ǰ�� : <span class="point2"><?=cutStr($row->productname,32)?></span>
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
					<th>����</th>
					<td><?=($isnot==true?$row->quantity:'')?></td>
				</tr>
				<?}?>
				<tr>
					<th>������</th>
					<td><?=number_format($sumreserve)?> ��</td>
				</tr>
				<tr>
					<th class="orderend_info_last">�ǸŰ�</th>
					<td class="orderend_info_last"><?=number_format($sumprice)?> ��</td>
				</tr>
				<?
					}
					mysql_free_result($result);
				?>
			</table>
			<div class="orderend_total_info">
				�ֹ� �հ� : <span class="orderend_payinfo"><?=number_format($totprice)?></span>��
			</div>
		</div>
	</div>

	<div class="orderend_wrap">
		<h2>�߰���� ����</h2>
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
								$pr_article = '���� ������';
							}
							if($etcdata[$i]->productcode=="99999999990X"){
								$pr_article = '��۷�';
							}
							if($etcdata[$i]->productcode=="99999999997X"){
								$pr_article = '�ΰ���(VAT)';
							}
				?>
				<tr>
					<td colspan="2" style="padding:10px;"><b>�׸� : <?=$pr_article?></b></td>
				</tr>
				<tr>
					<th>�ݾ�</th>
					<td><?=($etcdata[$i]->price!=0?number_format($etcdata[$i]->price).'��':'')?></td>
				</tr>
				<tr>
					<th class="orderend_info_last">������</th>
					<td class="orderend_info_last"><?=$etcdata[$i]->order_prmsg?></td>
				</tr>
				<?
							}
						}
					}else{
				?>
				<tr>
					<td class="orderend_info_last" colspan="3" align="center">
						�߰���� ������ �����ϴ�.
					</td>
				</tr>

				<?
					}
					$plus_etcprice+=$_ord->deli_price;
				?>
			</table>
			<div class="orderend_total_info">
				�߰���� �հ� : <span class="orderend_payinfo"><?=number_format($plus_etcprice)?></span>��
			</div>
		</div>
	</div>

	<div class="orderend_wrap">
		<h2>�߰� ���� �� ��������</h2>
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
							$dc_article = '�׷�����';
						}
						if($salereserve>0){
							$dc_article = '�׷�����';
						}
				?>
				<tr>
					<th>�׸�</th>
					<td><?=$dc_article?> (<?=$group_name?>)</td>
				</tr>
				<tr>
					<th>�ݾ�</th>
					<td>
						<?=($salemoney>0?"-".number_format($salemoney).'��':'')?>
						<?=($salereserve>0?"+ ".number_format($salereserve).'��':'')?>
					</td>
				</tr>
				<tr>
					<th <?if($_ord->reserve>0){?>class="orderend_info_last"<?}?>>������</th>
					<td <?if($_ord->reserve>0){?>class="orderend_info_last"<?}?>>�ֹ��� ��ü ����</td>
				</tr>
				<?
					}
					if($_ord->reserve>0){
						$dc_state = true;
				?>
				<tr>
					<th>�׸�</th>
					<td>������ ���</td>
				</tr>
				<tr>
					<th>�ݾ�</th>
					<td>- <?=number_format($_ord->reserve)?> ��</td>
				</tr>
				<tr>
					<th>������</th>
					<td>�ֹ��� ��ü ����</td>
				</tr>
				<?}?>
				<?if($dc_state == false){?>
				<tr>
					<td align="center">�ش������ �����ϴ�.</td>
				</tr>
				<?}?>
			</table>
		</div>
	</div>

	<div class="orderend_wrap">
		<h2>���� ��������</h2>
		<div class="orderend_info_wrap">
			<table cellpadding="0" cellspacing="0" class="orderend_total_table">
				<tr>
					<td>�ֹ��ݾ� : <?=number_format($totprice)?>��</td>
				</tr>
				<tr>
					<td>�߰���� : <?=number_format($plus_etcprice)?>��</td>
				</tr>
				<tr>
					<td>
						<?
							$tot_dc_price = 0;
							$tot_dc_price = $salemoney+$_ord->reserve;
						?>
						���αݾ� : -<?=number_format($tot_dc_price)?>��
					</td>
				</tr>
				<tr>
					<td>
						<?
							$tot_price = 0;
							$tot_price = ($totprice+$plus_etcprice)-$tot_dc_price;
						?>
						�����ݾ� : <span class="point3"><?=number_format($tot_price)?>��</span>
					</td>
				</tr>
				<!-- <tr>
					<td>
						�����ݾ� : �����<?//=number_format($salereserve)?>��
					</td>
				</tr> -->
			</table>
		</div>
	</div>

	<div class="orderend_wrap">
		<h2>�������</h2>
		<div class="orderend_info_wrap">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<th class="orderend_info_last">�������</th>
					<td class="orderend_info_last">
						<?
							$orderend_paytype='';
							if(preg_match("/^(V|C|P|M|B|O|Q){1}/", $_ord->paymethod)){
								
								$arpm=array("V"=>"�ǽð�������ü","C"=>"�ſ�ī��","P"=>"�Ÿź�ȣ - �ſ�ī��", "M"=>"�ڵ���", "B"=>"������ �Ա�", "O"=>"�������", "Q"=>"�Ÿź�ȣ - �������");

								$orderend_paytype = $arpm[substr($_ord->paymethod,0,1)];
							}else{
								$orderend_paytype = "�ŷ�����";
							}
						?>
						<?=$orderend_paytype?>
					</td>
				</tr>
				<tr>
					<th>��������</th>
					<td style="padding:0px;">
						<?
							if(preg_match("/^(V|C|P|M){1}/", $_ord->paymethod)) {
								if ($_ord->pay_flag=="0000") {
									if(preg_match("/^(C|P){1}/", $_ord->paymethod)) {
										echo "�������� - ���ι�ȣ : ".$_ord->pay_auth_no." ";
									} else {
										echo "������ <font color=blue>����ó��</font> �Ǿ����ϴ�.";
									}
								} else if(strlen($_ord->pay_flag)>0){
									echo "�ŷ���� : <font color=red><b><u>".$_ord->pay_data."</u></b></font>\n";
								}else{
									echo "\n<font color=red>(���ҽ���)</font>";
								}
								if (preg_match("/^(C|P|M){1}/", $_ord->paymethod) && $_data->card_payfee>0) echo "<br>&nbsp\n".$arpm[substr($_ord->paymethod,0,1)]." ������ ���� ���ΰ� ������ �ȵ˴ϴ�.";
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
									<td width="60" align="left" bgcolor="#f2f2f2">�����</td>
									<td>&nbsp;<?=$account_info[0]?></td>
								</tr>
								<tr>
									<td width="60" align="left" bgcolor="#f2f2f2">�Աݰ���</td>
									<td>&nbsp;<?=$account_info[1]?></td>
								</tr>
								<tr>
									<td width="60" align="left" bgcolor="#f2f2f2">������</td>
									<td>&nbsp;<?=$account_holder?></td>
								</tr>
								<tr>
									<td colspan="2" style="border-bottom:none;">�Ա�Ȯ�� �� ��� �˴ϴ�.</td>
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
		<h2>�ֹ�������</h2>
		<div class="orderend_info_wrap">
			<table>
				<tr>
					<th>�̸�</th>
					<td><?=$_ord->sender_name?></td>
				</tr>
				<tr>
					<th>��ȭ��ȣ</th>
					<td><?=$_ord->sender_tel?></td>
				</tr>
				<tr>
					<th>�̸���</th>
					<td><?=$_ord->sender_email?></td>
				</tr>
			</table>
		</div>
	</div>

	<div class="orderend_wrap">
		<h2>�������</h2>
		<div class="orderend_info_wrap">
			<table>
				<tr>
					<th>�̸�</th>
					<td><?=$_ord->receiver_name?></td>
				</tr>
				<tr>
					<th>��ȭ��ȣ</th>
					<td><?=$_ord->receiver_tel1?></td>
				</tr>
				<tr>
					<th>�����ȭ</th>
					<td><?=$_ord->receiver_tel2?></td>
				</tr>
				<tr>
					<th>�ּ�</th>
					<td><?=$_ord->receiver_addr?></td>
				</tr>
				<tr>
					<th>��û����</th>
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
			echo "<font color=\"#FF6600\"><b>".$_ord->sender_name."���� �ֹ��� �Ϸ�Ǿ����ϴ�.</b></font><br>\n";
			if ($totreserve>0) echo "������ ��ǰ ���Կ� ���� ������ <font color=\"#FF6600\"><b>".number_format($totreserve)."��</b></font>�� ��۰� �Բ� �ٷ� �����˴ϴ�.<br>\n";
		} else {
			echo "�ֹ��� �Ϸ�Ǿ����ϴ�.<br>\n";
			echo "������ �ֹ�Ȯ�� ��ȣ�� <font color=0000a0><b>".substr($_ord->id,1,6)."</b></font>�Դϴ�.<br>\n";
		}
	} else if (preg_match("/^(V|O|Q|C|P|M)$/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")!=0 ) {
		echo "<font color=red size=3><b>�ֹ��� ���еǾ����ϴ�.</b></font><br>\n";
	}

	if(preg_match("/^(B){1}/", $_ord->paymethod) || (preg_match("/^(O|Q){1}/", $_ord->paymethod) && $_ord->pay_flag=="0000")) {
		echo "�Աݹ���� �������Ա��� ��� ���¹�ȣ�� �޸��ϼ���.<br>���� �Ա�Ȯ�� �� �ٷ� �����帳�ϴ�.<br><br>\n";
	} else if(preg_match("/^(C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0) {
		echo "���� Ȯ�� �� �ٷ� �����帳�ϴ�.<br><br>\n";
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
		<button type="button" onClick="orderendClose();" class="button white bigrounded">Ȯ��</button>
	</div>
<div>
<script>
function orderendClose(){
	location.href="./";
}
</script>
