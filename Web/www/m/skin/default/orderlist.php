<?
//include_once('header.php');
?>

<div id="content">
	<div class="h_area2">
		<h2>�ֹ�����</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>

	<!-- �ֹ����� -->
	<div class="orderlist">
		<div class="pr_navi">
			<h3>�Ⱓ�� ��ȸ</h3>
			<select class="basic_select" name="search_period" onChange="GoSearch(this.value)">
				<option value="">����</option>
				<option value="3MONTH" <? if($_POST[search_period]=="3MONTH") {echo "selected";}?>>3����</option>
				<option value="6MONTH" <? if($_POST[search_period]=="6MONTH") {echo "selected";}?>>6����</option>
				<option value="12MONTH" <? if($_POST[search_period]=="12MONTH") {echo "selected";}?>>1��</option>
			</select>
		</div>

		<div class="tab_area">
			<ul class="tab_type2">
			<? if($_POST[ordgbn]=="" || $_POST[ordgbn]=="A") {?>
				<li class="active"><a href="javascript:GoOrdGbn('A')" rel="external">��ü����</a></li>
			<? } else { ?>
				<li><a href="javascript:GoOrdGbn('A')" rel="external">��ü����</a></li>
			<? } ?>

			<? if($_POST[ordgbn]=="S") {?>
				<li class="active"><a href="javascript:GoOrdGbn('S')" rel="external">�ֹ�����</a></li>
			<? } else { ?>
				<li><a href="javascript:GoOrdGbn('S')" rel="external">�ֹ�����</a></li>
			<? } ?>

			<? if($_POST[ordgbn]=="C") {?>
				<li class="active"><a href="javascript:GoOrdGbn('C')" rel="external">��ҳ���</a></li>
			<? } else { ?>
				<li><a href="javascript:GoOrdGbn('C')" rel="external">��ҳ���</a></li>
			<? } ?>

			<? if($_POST[ordgbn]=="R") {?>
				<li class="active"><a href="javascript:GoOrdGbn('R')" rel="external">��ǰ,��ȯ</a></li>
			<? } else { ?>
				<li><a href="javascript:GoOrdGbn('R')" rel="external">��ǰ,��ȯ</a></li>
			<? } ?>
			</ul>
		</div>

		<div class="orderlist_list">
<?
		$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
		$result=mysql_query($sql,get_db_conn());
		$delicomlist=array();
		while($row=mysql_fetch_object($result)) {
			$delicomlist[$row->code]=$row;
		}
		mysql_free_result($result);

		$s_curtime=mktime(0,0,0,$s_month,$s_day,$s_year);
		$s_curdate=date("Ymd",$s_curtime);
		$e_curtime=mktime(0,0,0,$e_month,$e_day,$e_year);
		$e_curdate=date("Ymd",$e_curtime)."999999999999";

		$sql = "SELECT COUNT(*) as t_count FROM tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND ordercode >= '".$s_curdate."' AND ordercode <= '".$e_curdate."' ";
		if($ordgbn=="S") $sql.= "AND deli_gbn IN ('S','Y','N','X') ";
		else if($ordgbn=="C") $sql.= " AND deli_gbn IN ('C','D') ";
		else if($ordgbn=="R") $sql.= " AND deli_gbn IN ('R','E') ";
		$sql.= " AND (del_gbn='N' OR del_gbn='A') ";
		//echo $sql;
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$t_count = (int)$row->t_count;
		mysql_free_result($result);
		$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

		$sql = "SELECT ordercode, price, paymethod, pay_admin_proc, pay_flag, bank_date, deli_gbn ";
		$sql.= "FROM tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
		$sql.= "AND ordercode >= '".$s_curdate."' AND ordercode <= '".$e_curdate."' ";
		if($ordgbn=="S") $sql.= "AND deli_gbn IN ('S','Y','N','X') ";
		else if($ordgbn=="C") $sql.= "AND deli_gbn IN ('C','D') ";
		else if($ordgbn=="R") $sql.= "AND deli_gbn IN ('R','E') ";
		$sql.= "AND (del_gbn='N' OR del_gbn='A') ";
		$sql.= "ORDER BY ordercode DESC ";
		$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
		//echo $sql;
		$result=mysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=mysql_fetch_object($result)) {

			/*	$result_tmp = mysql_query("SELECT productname FROM tblorderproduct WHERE ordercode='$row->ordercode' AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%')");
				$a = 0;
				while($row_tmp = mysql_fetch_array($result_tmp))
				{
					if($a==0){	$first_productname = $row_tmp[productname];	}
					$a++;
				}
				if($a > 1) {	$str = "(��" .$a."��)";	} else {	$str = "";}*/

			?>
				<div class="orderlist_detail_top">
					�ֹ��� : <span class="orderlist_detail_date"><?=substr($row->ordercode,0,4)?>-<?=substr($row->ordercode,4,2)?>-<?=substr($row->ordercode,6,2)?></span>
					<a class="button white small" href="javascript:OrderDetailPop('<?=$row->ordercode?>')">�ֹ� ������</a><br />
					�����ݾ� : <?=number_format($row->price)?>�� / ������� : <?=getPaymethodStr($row->paymethod)?>
				</div>
				<div class="orderlist_detail_prlist">
<?
	$sql = "SELECT * FROM tblorderproduct WHERE ordercode='".$row->ordercode."' ";
	$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
	//echo $sql;
	$result2=mysql_query($sql,get_db_conn());
	$jj=0;
	$numrows = mysql_num_rows($result2);
	$chkbox_count = 0;
	while($row2=mysql_fetch_object($result2)) {
?>
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<col width="75%"></col>
						<col width=""></col>
						<tr>
							<td style="padding:5px 0px 5px 10px;">
							<?
								if ($row->deli_gbn!="C" && !($row->pay_admin_proc=="C" && $row->pay_flag=="0000") && $numrows>1 && $row2->status=='') {?>
								<input type="checkbox" name="chk_<?= $row->ordercode ?>" id="chk_<?= $row->ordercode ?>_<?= $jj ?>" value="<?=$row2->productcode?>"/>
								<input type="hidden" name="chk_uid_<?= $row->ordercode ?>" id="chk_uid_<?= $row->ordercode ?>_<?= $jj ?>" value="<?=$row2->uid?>"/>
							<?
								$chkbox_count++;
								}
								if(substr($row2->productcode,0,3) == "999"){
							?>
								<?=_strCut($row2->productname,20,5,$charset)?>
							<?
								}else{
							?>
								<a href="/m/productdetail_tab01.php?productcode=<?=$row2->productcode?>" rel="external"><?=_strCut($row2->productname,20,5,$charset)?></a>
							<?}?>
							</td>
							<td align="center" style="padding-bottom:5px;">
								<? echo orderProductDeliStatusStr($row2,$row); ?>
								<?
								$deli_link = '-';
								$deli_url="";
								$trans_num="";
								$company_name="";
								if($row2->deli_gbn=="Y" AND strlen($row2->deli_num) > 0 ) {
									$deli_link = '';
									if($row2->deli_com>0 && $delicomlist[$row2->deli_com]) {
										$deli_url=$delicomlist[$row2->deli_com]->deli_url;
										$trans_num=$delicomlist[$row2->deli_com]->trans_num;
										$company_name=$delicomlist[$row2->deli_com]->company_name;
										//$deli_link .= $company_name."<br>".$row2->deli_num."<br>";
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
								<br/>
								<?if(substr($row2->productcode,0,3) != "999"){?>
								<a class="button white small" href="javascript:reviewWrite('<?=$row2->productcode?>')">�ı� �ۼ�</a>
								<?}?>
							</td>
						</tr>
					</table>
<?
		$jj++;
	}
	mysql_free_result($result2);
?>

<? if ($row->deli_gbn!="C" && !($row->pay_admin_proc=="C" && $row->pay_flag=="0000") && $chkbox_count>0 && $numrows>1) {?>
					<div class="orderlist_select_del">
						<input type="checkbox" name="chk_<?= $row->ordercode ?>_all" id="chk_<?= $row->ordercode ?>_all" value="all" onclick="productAll('chk_<?= $row->ordercode ?>')"/>
						<span style="cursor:pointer"
						<? if (strlen($row->bank_date)<12 && preg_match("/^(B|O|Q){1}/", $row->paymethod)) { ?>
							onclick="order_one_cancel('<?= $row->ordercode ?>', '', 'NO', '<?= $row->tempkey ?>')"
						<? }else{ ?>
							onclick="order_multi_cancel('<?=$row->ordercode?>')"
						<? } ?>>���û�ǰ �ֹ����</span>
					</div>
<?}?>
				</div>
<?
			$cnt++;
		}
		mysql_free_result($result);
?>
		</div>


<?

		$total_block = intval($pagecount / $setup[page_num]);

		if (($pagecount % $setup[page_num]) > 0) {
			$total_block = $total_block + 1;
		}

		$total_block = $total_block - 1;

		if (ceil($t_count/$setup[list_num]) > 0) {
			// ����	x�� ����ϴ� �κ�-����
			$a_first_block = "";
			if ($nowblock > 0) {
			//	$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='ù ������';return true\"><FONT class=\"prlist\">[1...]</FONT></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {

				$a_prev_page = "<button type=\"button\" onClick='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' class=\"pg_btn pg_btn_prev\"><span></span></button>";
				//	$a_prev_page = $a_first_block.$a_prev_page;
				$a_prev_page = $a_prev_page;

			}
			else
			{
				$a_prev_page = "<button type=\"button\" onClick=\"\" class=\"pg_btn pg_btn_prev\"><span></span></button>";

			}


			// �Ϲ� �������� ������ ǥ�úκ�-����
			if (intval($total_block) <> intval($nowblock)) {
				$print_page .= "";
				for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
					if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
						$print_page .= "<span style=\"display:inline-block;width:28px;height:24px;line-height:24px; border:1px solid #000000;\">".(intval($nowblock*$setup[page_num]) + $gopage)."</span> "; //����������
					} else {
						$print_page .= " <a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' class=\"pg_num\">".(intval($nowblock*$setup[page_num]) + $gopage)."</a>";
					}
				}
			} else {
				if (($pagecount % $setup[page_num]) == 0) {
					$lastpage = $setup[page_num];
				} else {
					$lastpage = $pagecount % $setup[page_num];
				}

				for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
					if (intval($nowblock*$setup[page_num]) + $gopage == intval($gotopage)) {
						$print_page .= " <span class=\"pg_num pg_num_on\">".(intval($nowblock*$setup[page_num]) + $gopage)."</span>&nbsp;";
					} else {
						$print_page .= " <a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' class=\"pg_num\">".(intval($nowblock*$setup[page_num]) + $gopage)."</a>&nbsp;";
					}
				}
			}		// ������ �������� ǥ�úκ�-��

			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
				$last_gotopage = ceil($t_count/$setup[list_num]);

			//	$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='������ ������';return true\"><FONT class=\"prlist\">[...".$last_gotopage."]</FONT></a>";

				$next_page_exists = true;
			}
			// ���� 10�� ó���κ�...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {

				//$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='���� ".$setup[page_num]." ������';return true\"><FONT class=\"prlist\">[next]</FONT></a>";

				$a_next_page = "<button type=\"button\" onClick='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' class=\"pg_btn pg_btn_next\"><span></span></button>";

				//$a_next_page = $a_next_page.$a_last_block;
				$a_next_page = $a_next_page;
			}
			else
			{
				$a_next_page = "<button type=\"button\" onClick=\"\" class=\"pg_btn pg_btn_next\"><span></span></button>";
			}
		} else {
			$a_prev_page = "<button type=\"button\" onClick=\"\" class=\"pg_btn pg_btn_prev\"><span></span></button>";
			$print_page = "<span class=\"pg_num pg_num_on\">1</span>";
			$a_next_page = "<button type=\"button\" onClick=\"\" class=\"pg_btn pg_btn_next\"><span></span></button>";
		}
?>

		<div class="pg pg_num_area3">
		<?=$a_prev_page?>
		<span class="pg_area"><?=$print_page?></span>
		<?=$a_next_page?>
		</div>


		</div>
	</div>
	<!-- //�ֹ����� -->

</div>

<hr>
<?
//include_once('footer.php');
?>