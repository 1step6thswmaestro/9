<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%">
	<!--
	<tr>
		<td valign="bottom">
		<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu1.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_orderlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu2r.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_personal.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu3.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>wishlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu4.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_reserve.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu5.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_coupon.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu6.gif" BORDER="0"></A></TD>
			<?if($_data->recom_url_ok == "Y" || $_data->sns_ok == "Y"){?><TD><A HREF="<?=$Dir.FrontDir?>mypage_promote.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu10.gif" BORDER="0"></A></TD><?}?>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_gonggu.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu11.gif" BORDER="0"></A></TD>
			<? if(getVenderUsed()==true) { ?><TD><A HREF="<?=$Dir.FrontDir?>mypage_custsect.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu9.gif" BORDER="0"></A></TD><? } ?>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_usermodify.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu7.gif" BORDER="0"></A></TD>
			<TD><A HREF="<?=$Dir.FrontDir?>mypage_memberout.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin3_menu8.gif" BORDER="0"></A></TD>
			<TD width="100%" background="<?=$Dir?>images/common/mypersonal_skin3_menubg.gif"></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	-->
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td bgcolor="#EAEAEA" style="padding:6px;">


		<table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff">
		<tr>
			<td  style="padding:25px;">
			<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
				<td height="26"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin3_text01.gif" border="0" align="absmiddle"></td>
				<td><A HREF="javascript:GoSearch('TODAY')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin3_btn01.gif" border="0" align="absmiddle"></A>
				<A HREF="javascript:GoSearch('15DAY')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin3_btn02.gif" border="0" align="absmiddle"></A>
				<A HREF="javascript:GoSearch('1MONTH')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin3_btn03.gif" border="0" hspace="2" align="absmiddle"></A>
				<A HREF="javascript:GoSearch('3MONTH')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin3_btn04.gif" border="0" align="absmiddle"></A>
				<A HREF="javascript:GoSearch('6MONTH')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin3_btn05.gif" border="0" hspace="2" align="absmiddle"></A></td>
			</tr>
			<tr>
				<td><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin3_text02.gif" border="0" align="absmiddle"></td>
				<td><SELECT onchange="ChangeDate('s')" name="s_year" align="absmiddle" style="font-size:11px;">
				<?
				for($i=date("Y");$i>=(date("Y")-2);$i--) {
					echo "<option value=\"".$i."\"";
					if($s_year==$i) echo " selected";
					echo " style=\"color:#444444;\">".$i."</option>\n";
				}
				?>
				</SELECT> <SELECT onchange="ChangeDate('s')" name="s_month" style="font-size:11px;">
				<?
				for($i=1;$i<=12;$i++) {
					echo "<option value=\"".$i."\"";
					if($s_month==$i) echo " selected";
					echo " style=\"color:#444444;\">".$i."</option>\n";
				}
				?>
				</SELECT> <SELECT name="s_day" style="font-size:11px;">
				<?
				for($i=1;$i<=get_totaldays($s_year,$s_month);$i++) {
					echo "<option value=\"".$i."\"";
					if($s_day==$i) echo " selected";
					echo " style=\"color:#444444;\">".$i."</option>\n";
				}
				?>
				</SELECT><b> ~ </b> <SELECT onchange="ChangeDate('e')" name="e_year" style="font-size:11px;">
				<?
				for($i=date("Y");$i>=(date("Y")-2);$i--) {
					echo "<option value=\"".$i."\"";
					if($e_year==$i) echo " selected";
					echo " style=\"color:#444444;\">".$i."</option>\n";
				}
				?>
				</SELECT> <SELECT onchange="ChangeDate('e')" name="e_month" style="font-size:11px;">
				<?
				for($i=1;$i<=12;$i++) {
					echo "<option value=\"".$i."\"";
					if($e_month==$i) echo " selected";
					echo " style=\"color:#444444;\">".$i."</option>\n";
				}
				?>
				</SELECT> <SELECT name="e_day" style="font-size:11px;">
				<?
				for($i=1;$i<=get_totaldays($e_year,$e_month);$i++) {
					echo "<option value=\"".$i."\"";
					if($e_day==$i) echo " selected";
					echo " style=\"color:#444444;\">".$i."</option>\n";
				}
				?>
				</SELECT><a href="javascript:CheckForm();"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin3_btn06.gif" border="0" hspace="5" align="absmiddle"></a> </td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="padding:10px; font-size:11px; letter-spacing:-0.5pt; line-height:15px;">* ���� �ֱ� �ֹ� <font color="#F02800" style="font-size:11px;letter-spacing:-0.5pt;"><b>6���� �ڷ���� ����</b></font>�Ǹ�, <font color="#000000" style="font-size:11px;letter-spacing:-0.5pt;"><b>6���� ���� �ڷ�� ���ڸ� �����ؼ� ��ȸ</b></font>�Ͻñ� �ٶ��ϴ�.<br>
		&nbsp;&nbsp;&nbsp;(���ں��� ��ȸ�� �ִ� ���� 3�� ������ �ֹ����� ��ȸ�� �����մϴ�)<br>
		*&nbsp;�� ���� ��ȸ ������ �Ⱓ�� 6������ ���� ���ý� ��ȸ �Ⱓ�� 6���� �̳��� �����ϼž� �մϴ�.</td>
	</tr>
	<tr>
		<td height="30"></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%" border="0">
				<tr>
					<td><a href="<?=$Dir.FrontDir?>mypage_orderlist.php"><img src="../images/design/orderlist01<?=($type!="")? "":"on"?>.gif" align="absmiddle" ></a></td>
					<td><a href="<?=$Dir.FrontDir?>mypage_orderlist.php?type=2"><img src="../images/design/orderlist02<?=($type=="2")? "on":""?>.gif" align="absmiddle" ></a></td>
					<td><a href="<?=$Dir.FrontDir?>mypage_orderlist.php?type=3"><img src="../images/design/orderlist03<?=($type=="3")? "on":""?>.gif" align="absmiddle" ></a></td>
					<td width="100%" align="right" style="background:url(../images/design/orderlist_bg.gif);"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td valign="bottom"  style="background:url(<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/mypersonal_skin3_menubg.gif)">
		<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0">
		<TR>
			<TD><A HREF="javascript:GoOrdGbn('A')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/menu01<?=($ordgbn=="A"?"on":"off")?>.gif" border="0"></A></TD>
			<TD><A HREF="javascript:GoOrdGbn('S')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/menu02<?=($ordgbn=="S"?"on":"off")?>.gif" border="0"></TD>
			<TD><A HREF="javascript:GoOrdGbn('C')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/menu03<?=($ordgbn=="C"?"on":"off")?>.gif" border="0"></TD>
			<? if($type != 3){?>
			<TD><A HREF="javascript:GoOrdGbn('R')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/menu04<?=($ordgbn=="R"?"on":"off")?>.gif" border="0"></A></TD>
			<TD><A HREF="javascript:GoOrdGbn('P')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/menu05<?=($ordgbn=="P"?"on":"off")?>.gif" border="0" alt=""></A></TD>
			<? } ?>
			<? if($type != '2' && $type != 3){?>
			<TD><A HREF="javascript:GoOrdGbn('T')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/menu06<?=($ordgbn=="T"?"on":"off")?>.gif" border="0" alt="������ ��ǰ" /></a></TD>
			<TD><A HREF="javascript:GoOrdGbn('SC')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/menu07<?=($ordgbn=="SC"?"on":"off")?>.gif" border="0" alt="������ ��ǰ" /></a></TD>
			<? } ?>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" border="0" bgcolor="#F8F8F8" style="table-layout:fixed">
		<!-- �ֹ�����, �ֹ� ��ǰ��, ��ۻ���, �������, �������, �����ݾ�, ������  -->
		<col width="180"></col>
		<col></col>
		<col width="80"></col>
		<!--<col width="90"></col>-->
		<col width="80"></col>
		<col width="80"></col>
		<tr height="30" align="center" bgcolor="#F8F8F8">
			<td><font color="#333333"><b>�ֹ���(��������)</b></font></td>
			<td><font color="#333333"><b>��ǰ��/�ɼ�</b></font></td>
			<td><font color="#333333"><b><?=($type == 3)?"����ó������":"��ۻ���"?></b></font></td>
			<!--<td><font color="#333333"><b>��ȯ/ȯ��ó��</b></font></td>-->
			<td><font color="#333333"><b><?=($type == 3)?"������ȣ":"�������"?></b></font></td>
			<td><font color="#333333"><b>��ǰ��</b></font></td>
		</tr>
		<tr>
			<td height="1" colspan="5" bgcolor="#DDDDDD"></td>
		</tr>
<?
		$delicomlist=getDeliCompany();
		$returnableCnt = 0; // �ϴܿ��� ��ȯ,ȯ�� ���� ��ǰ Ȯ�� �� ����
		$s_curtime=mktime(0,0,0,$s_month,$s_day,$s_year);
		$s_curdate=date("Ymd",$s_curtime);
		$e_curtime=mktime(0,0,0,$e_month,$e_day,$e_year);
		$e_curdate=date("Ymd",$e_curtime)."999999999999";

		$orderlists = getOrderList($s_curdate,$e_curdate,$ordgbn,$type,$gotopage);

		if($orderlists['total'] < 1){ ?>
		<tr>
			<td colspan="5" style="padding:10px 0px; text-align:center; background:#FFFFFF">��ϵ� �ֹ� ������ �����ϴ�.</td>
		</tr>
		<tr><td colspan="5" height="1" bgcolor="#d1d1d1"></td></tr>
<?		}else{
			foreach($orderlists['orders'] as $row){
				$orderproducts = array();
				$orderproducts = getOrderProduct($row->ordercode);
?>

		<tr bgcolor="#FFFFFF" onmouseover="this.style.background='#ffffff';" onmouseout="this.style.background='#FFFFFF';">
			<td style="padding-top:10; padding-bottom:10;" class="mypage_order_line" valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr><td height="26" class="mypage_order_line2"><b><?=substr($row->ordercode,0,4)?>/<?=substr($row->ordercode,4,2)?>/<?=substr($row->ordercode,6,2)?></b></td></tr>
					<tr><td height=5></td></tr>
					<tr><td class="mypage_list_cont">������� : <?=getPaymethodStr($row->paymethod)?></td></tr>
					<tr><td class="mypage_list_cont">�����ݾ� : <b><font color="#000000"><?=number_format($row->price)?></font></b>��</td></tr>
					<tr><td height=5></td></tr>
					<tr><td class="mypage_list_cont"><A HREF="javascript:OrderDetailPop('<?=$row->ordercode?>')" onmouseover="window.status='�ֹ�������ȸ';return true;" onmouseout="window.status='';return true;"><img src="<?=$Dir?>images/common/mypage/<?=$_data->design_mypage?>/mypage_order_icon01.gif" alt="����" /></a>
					<?
					/*
					if (preg_match("/^(B){1}/", $row->paymethod) && strlen($row->bank_date)<12 && $row->deli_gbn=="N") {
						echo "<br/><a href=\"javascript:order_cancel('".$row->tempkey."', '".$row->ordercode."','".$row->bank_date."')\" onMouseOver=\"window.status='�ֹ����';return true;\"><img src=\"".$Dir."images/common/orderdetailpop_ordercancel.gif\" align=absmiddle border=0></a>\n";
					}
					*/
					?>
					</td></tr>
				</table>
			</td>
			<td colspan="4">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
				<col></col>
				<col width="80"></col>
				<!--<col width="80"></col>-->
				<col width="80"></col>
				<col width="80"></col>
<?				$chkbox_count = 0;
				$cnt = count($orderproducts);
				for($jj=0;$jj < $cnt;$jj++){
					$row2 = $orderproducts[$jj];
					if($jj>0) echo '<tr><td colspan="4" height="1" bgcolor="#E7E7E7"></tr>';
					$optvalue="";
					if(ereg("^(\[OPTG)([0-9]{3})(\])$",$row2->opt1_name)) {
						$optioncode=$row2->opt1_name;
						$row2->opt1_name="";
						$sql = "SELECT opt_name FROM tblorderoption WHERE ordercode='".$row->ordercode."' AND productcode='".$row2->productcode."' AND opt_idx='".$optioncode."' limit 1 ";
						$res=mysql_query($sql,get_db_conn());
						if($res && mysql_num_rows($res)){
							$optvalue= mysql_result($res,0,0);
						}
						mysql_free_result($res);
					}
	?>
					<tr>
						<td style="font-size:8pt; padding:10px; line-height:11pt;">
							<div style="width:25px;float:left;text-align:left;">
							<? if ($row->deli_gbn!="C" && !($row->pay_admin_proc=="C" && $row->pay_flag=="0000") && count($orderproducts)>1 && $row2->status=='') {?>
								<input type="checkbox" name="chk_<?= $row->ordercode ?>" id="chk_<?= $row->ordercode ?>_<?= $jj ?>" value="<?=$row2->productcode?>"/>
								<input type="hidden" name="chk_uid_<?= $row->ordercode ?>" id="chk_uid_<?= $row->ordercode ?>_<?= $jj ?>" value="<?=$row2->uid?>"/>
							<? $chkbox_count++;
							} ?>
							</div>
							<div>
								<?
									$reservation = "";
									if( $row2->reservation != "0000-00-00" && $row2->productcode!='99999990GIFT') {
										$reservation = "[�����ۻ�ǰ(��ۿ�����:".$row2->reservation.")]<br />";
									}
								?>
								<A HREF="javascript:OrderDetailProduct('<?=$row->ordercode?>','<?=$row2->productcode?>')" onmouseover="window.status='�ֹ�������ȸ';return true;" onmouseout="window.status='';return true;"><img src="<?=(strlen($row2->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row2->tinyimage)==true)?$Dir.DataDir.'shopimages/product/'.urlencode($row2->tinyimage):$Dir."images/no_img.gif"?>" border="0" width="50" style="float:left;margin-right:5px;"/><?=$reservation?><?=$row2->productname?></a>
								<?
								if(!_empty($optvalue)) 	echo "<br><img src=\"".$Dir."images/common/icn_option.gif\" border=0 align=absmiddle> ".$optvalue."";
								?>
								</td>
								<td align="center" style="font-size:8pt;"><font color="#000000"><? echo orderProductDeliStatusStr($row2,$row,$cnt); ?></font></td>
		<!--
								<td style="text-align:center"><font color="#3f77ca">
								<?
									if(getProductAbleInfo($row2->productcode,'return') == 'Y'){
										$pststr = orderProductStatusStr($row2->status);
										if(_empty($pststr)){
											if($row2->deli_gbn != 'Y') $pststr = '-';
											else if(strtotime('-15 day') > strtotime(substr($row->ordercode,0,8))) $pststr = '--';
											else{
												$pststr = '<input type="checkbox" value="'.$row2->uid.'" ordCode="'.$row->ordercode.'" name="Item[]" />';
												$returnableCnt++;
											}
										}
									}else{
										$pststr = '�Ұ�';
									}
									echo $pststr;
								?>
								</font></td>
								<td></td>
		-->
								<td align="center" style="font-size:8pt;padding-top:3;">
								<?
								$deli_link = '-';
								$deli_url="";
								$trans_num="";
								$company_name="";
								if($row2->deli_gbn=="Y" AND strlen($row2->deli_num) > 0 ) {
									if($row2->deli_com>0 && $delicomlist[$row2->deli_com]) {
										$deli_url=$delicomlist[$row2->deli_com]->deli_url;
										$trans_num=$delicomlist[$row2->deli_com]->trans_num;
										$company_name=$delicomlist[$row2->deli_com]->company_name;
										$deli_link .= $company_name."<br>".$row2->deli_num."<br>";
										if(strlen($row2->deli_num)>0 && strlen($deli_url)>0) {
											if(strlen($trans_num)>0) {
												$arrtransnum=explode(",",$trans_num);
												$pattern=array("(\[1\])","(\[2\])","(\[3\])","(\[4\])");
												$replace=array(substr($row2->deli_num,0,$arrtransnum[0]),substr($row2->deli_num,$arrtransnum[0],$arrtransnum[1]),substr($row2->deli_num,$arrtransnum[0]+$arrtransnum[1],$arrtransnum[2]),substr($row2->deli_num,$arrtransnum[0]+$arrtransnum[1]+$arrtransnum[2],$arrtransnum[3]));
												$deli_url=preg_replace($pattern,$replace,$deli_url);
											} else {
												$deli_url.=$row2->deli_num;
											}
											$deli_link .='<A HREF="javascript:DeliSearch(\''.$deli_url.'\')"><img src="'.$Dir.'images/common/btn_mypagedeliview.gif" border="0"></A>';
										}
									}
								}
								echo $deli_link;
								?>
							</div>
						</td>
						<td align=center><? if($row2->deli_gbn=="Y" && $_data->review_type !="N") { ?><!-- <A HREF="javascript:OrderReview('<?=$row->ordercode?>','<?=$row2->productcode?>')" onmouseover="window.status='��ǰ��';return true;" onmouseout="window.status='';return true;"> --><A HREF="javascript:OrderReview('<?=$row2->productcode?>')"><img src="<?=$Dir?>images/common/mypage_detailview.gif" border="0" alt="��ǰ���ۼ�" /></A><? }else{ ?><img src="<?=$Dir?>images/common/mypage_detailview_off.gif" alt="��ǰ���ۼ�" /><? } ?>
						</td>
					</tr>
				<? } // end for by jj ?>

				<? if ($row->deli_gbn!="C" && !($row->pay_admin_proc=="C" && $row->pay_flag=="0000") && $chkbox_count>0 && count($orderproducts)>1) {?>
				<tr><td colspan=4 height=1 bgcolor=#E5E5E5></tr>
				<tr>
					<td style="background-color:#f8f8f8;" style="padding:10px;" class="mypage_list_cont">
						<div style="width:25px;float:left;text-align:left;"><input type="checkbox" name="chk_<?= $row->ordercode ?>_all" id="chk_<?= $row->ordercode ?>_all" value="all" onclick="productAll('chk_<?= $row->ordercode ?>')"/></div>
						<div style="width:85px;float:left;padding-top:3px;"><b>��ü����</b></div>
						<div style="float:left;padding-top:3px;"><b> - ���û�ǰ �ֹ���� ��û</b></div>
					</td>
					<td style="background-color:#f8f8f8;" align="center"><span style="cursor:pointer"
					<? if (strlen($row->bank_date)<12 && preg_match("/^(B|O|Q){1}/", $row->paymethod)) { ?>
						onclick="order_one_cancel('<?= $row->ordercode ?>', '', 'NO', '<?= $row->tempkey ?>')"
					<? }else{ ?>
						onclick="order_multi_cancel('<?=$row->ordercode?>')"
					<? } ?>
					><img src="<?=$Dir?>images/common/mypage/<?=$_data->design_mypage?>/mypage_order_cancel_icon02.gif" alt="Ȯ��" /></span></td>
					<td style="background-color:#f8f8f8;">&nbsp;</td>
					<td style="background-color:#f8f8f8;">&nbsp;</td>
				</tr>
				<? } ?>
				</table>
			</td>
		</tr>
		<tr><td colspan="5" height="1" bgcolor="#d1d1d1"></td></tr>
<?		} // end foreach
	} // end if
?>
		</table>
		</td>
	</tr>
<!--
	<? if($returnableCnt > 0) { ?>
	<tr>
		<td>
			<div id="btn_sel" style="text-align:right; margin-top:10px; margin-right:10px">
				<a href="#" onclick="return refund1();"><img src="<?=$Dir?>images/common/mypage/<?=$_data->design_mypage?>/mypage_order_icon02.gif" alt="���ðǿ� ���� ��ȯ��û" /></a>
				<a href="#" onclick="return refund2();"><img src="<?=$Dir?>images/common/mypage/<?=$_data->design_mypage?>/mypage_order_icon03.gif" alt="���ðǿ� ���� ȯ�ҽ�û"/></a>
			</div>
		</td>
	</tr>
	<?}?>
-->
	<tr><td height="10"></td></tr>
	<tr>
<?
	$pages = new pages(array('total_page'=>$orderlists['total_page'],'page'=>$orderlists['page'],'pageblocks'=>$setup[page_num],'links'=>"javascript:newGoPage('%u')"));
?>
		<td align="center"><?=$pages->_solv()->_result('fulltext')?></td>
	</tr>
	<tr><td height="20"></td></tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin3_text03.gif" border="0"></td>
		</tr>
		<tr><td height="1" bgcolor="#E8E8E8"></td></tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="padding-top:10px;padding-bottom:10px">
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td><IMG SRC="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin3_table_img01.gif" border="0"></td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		<tr><td height="1" bgcolor="#E8E8E8"></td></tr>
		<tr>
			<td><IMG SRC="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/orderlist_skin3_table_im-04.gif" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	</table>
	</td>
</tr>
</table>