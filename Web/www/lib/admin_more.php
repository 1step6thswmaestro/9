<?
/*
수수료 관련 추가 기능
*/ 

//입점업체 상품 개별 수수료 일괄 등록
function setProductCommissionAll($vender, $rate, $adminId) {

	$sql = "select * from tblproduct where vender = '".$vender."'";	
	$result = mysql_query($sql,get_db_conn());

	while($data= mysql_fetch_array($result)) {
		
		$productCode = $data['productcode'];
		insertCommission($vender, $productCode, $rate, "", "", "1", $adminId);
	}
	
	mysql_free_result($result);

	$ad_his = "개별 수수료 일괄 ".$rate."%로 지정 [운영본사]";

	$sql = "insert commission_history set ";
	$sql.= "vender	= '".$vender."', ";
	$sql.= "memo	= '".$ad_his."', ";
	$sql.= "`type`	= '1', ";
	$sql.= "admin_id	= '".$adminId."', ";
	$sql.= "reg_date	= now() ";

	mysql_query($sql,get_db_conn());
}

// 입점업체 수수료 형태 변경 히스토리
function getVenderCommissionHistory($vender, $adminchk, $p_type) {
	
	$setup[page_num] = 10;
	$setup[list_num] = 20;

	$block=$_REQUEST["block"];
	$gotopage=$_REQUEST["gotopage"];
	if ($block != "") {
		$nowblock = $block;
		$curpage  = $block * $setup[page_num] + $gotopage;
	} else {
		$nowblock = 0;
	}

	if (($gotopage == "") || ($gotopage == 0)) {
		$gotopage = 1;
	}

	$t_count=0;
	$sql = "select count(*) as t_count from commission_history where vender='".$vender."'";

	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$t_count = $row->t_count;

	mysql_free_result($result);
	$pagecount = (($t_count - 1) / $setup[list_num]) + 1;


	$sql = "select h.*, 
			(select productname from tblproduct where productcode=h.productcode) as p_name
			from commission_history h where  h.vender='".$vender."' order by h.reg_date desc, h.seq desc ";
	$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];


	$result=mysql_query($sql,get_db_conn());
	
	$data_lows = mysql_num_rows($result);

	$colspan=3;
	if ($adminchk=="1") { 
		$colspan=4;
	}

	if ($data_lows > 0) {
?>

		<table border=0 cellpadding=0 cellspacing=0 width="100%" style="table-layout:fixed">
			<col width=90></col>
			<col width=></col>
			<col width=90></col>
			<? if ($adminchk=="1") { ?>
			<col width=70></col>
			<? } ?>
			<TR>
				<TD colspan=<?= $colspan ?> background="images/table_top_line.gif"></TD>
			</TR>
			<tr>
				<td class="table_cell" align="center">변경날짜</td>
				<td class="table_cell1" align="center">처리내역</td>
				<td class="table_cell1" align="center">요청자</td>
				<?
				 if ($adminchk=="1") {
				?>
				<td class="table_cell1" align="center"><a href="javascript:history_del_all('<?= $vender ?>')">전체삭제</a></td>
				<?
				}	
				?>
			</tr>
			<TR>
				<TD colspan="<?= $colspan ?>" background="images/table_con_line.gif"></TD>
			</TR>
		<?
			
			$i=0;
			while($data= mysql_fetch_array($result)) {

				$idx = $data['seq'];
				$reg_date = substr($data['reg_date'], 0, 10);
				$meno = $data['memo'];
				$rq_name = $data['rq_name'];
				$h_type = $data['type'];
				$p_code = $data['productcode'];
				$p_name = $data['p_name'];
				
			?>
				<tr>
					<td class="td_con2" align="center"><?= $reg_date ?></td>
					<td class="td_con1" >
					<? if ($h_type==2 && $p_name!='') { ?>
					<a href="javascript:viewProduct('<?= $p_code ?>')" ><b style="color:#1139ac">[<?= $p_name ?>]</b></a>
					<? } ?>
					<?= $meno ?></td>
					<td class="td_con1" align="center">&nbsp;<?= $rq_name ?>&nbsp;</td>
					<?
					 if ($adminchk=="1") {
					?>
					<td class="td_con1" align="center"><a href="javascript:history_del('<?= $idx ?>')"><img src="images/btn_del.gif" border="0" alt="삭제" /></a></td>
					<?
					}	
					?>
				</tr>
				<tr><td background="images/table_con_line.gif" colSpan="<?= $colspan ?>"></tr>

		<?
				$i++;
			}
		?>
		</table>
		<?

			$cnt=$i;

			if($i>0) {

				$total_block = intval($pagecount / $setup[page_num]);
				if (($pagecount % $setup[page_num]) > 0) {
					$total_block = $total_block + 1;
				}
				
				$total_block = $total_block - 1;
				if (ceil($t_count/$setup[list_num]) > 0) {

					// 이전	x개 출력하는 부분-시작
					$a_first_block = "";
					if ($nowblock > 0) {
						$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=/images/minishop/btn_miniprev_end.gif border=0 align=absmiddle></a> ";
						$prev_page_exists = true;
					}
					$a_prev_page = "";
					if ($nowblock > 0) {
						$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><img src=/images/minishop/btn_miniprev.gif border=0 align=absmiddle></a> ";

						$a_prev_page = $a_first_block.$a_prev_page;
					}

					if (intval($total_block) <> intval($nowblock)) {
						$print_page = "";
						for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
							if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
								$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
							} else {
								$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
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

								$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
							} else {
								$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
							}
						}
					}

					$a_last_block = "";
					if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
						$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
						$last_gotopage = ceil($t_count/$setup[list_num]);
						$a_last_block .= " <a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=/images/minishop/btn_mininext_end.gif border=0 align=absmiddle></a>";
						$next_page_exists = true;
					}
					$a_next_page = "";
					if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
						$a_next_page .= " <a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><img src=/images/minishop/btn_mininext.gif border=0 align=absmiddle></a>";
						$a_next_page = $a_next_page.$a_last_block;
					}
				} else {
					$print_page = "<B>[1]</B>";
				}
				$pageing=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
			}

			echo "<div style=\"margin-top:12px;\">".$pageing."</div>";
		?>
			<form name="pageForm" method="post">
				<input type=hidden name='productcode' value='<?=$productcode?>'>
				<input type=hidden name='block' value='<?=$block?>'>
				<input type=hidden name='gotopage' value='<?=$gotopage?>'>
			</form>

			<script type="text/javascript">
		
			function GoPage(block,gotopage) {
				document.pageForm.block.value=block;
				document.pageForm.gotopage.value=gotopage;
				document.pageForm.submit();
			}

			function viewProduct(p_code) {
				
				window.open("/front/productdetail.php?productcode="+p_code);
			}

			</script>
		<?
		if ($adminchk=="1") {
		?>
			<iframe style="display:none" id="manageHistory"></iframe>
			<script type="text/javascript">
			<!--
				function history_del(idx) {

					if (confirm("해당내역을 삭제하시겠습니까? 삭제된 내역은 복구하실 수 없습니다.")) {
						mH = document.getElementById('manageHistory');
						mH.src="/admin/vender_ch_delete.php?type=one&p_type=<?= $p_type ?>&idx="+idx;
					}
				}

				function history_del_all(vender) {
			
					if (confirm("모든 내역을 삭제하시겠습니까? 삭제된 내역은 복구하실 수 없습니다.")) {
						mH = document.getElementById('manageHistory');
						mH.src="/admin/vender_ch_delete.php?type=all&p_type=<?= $p_type ?>&vender="+vender;
					}
				}
			// -->
			</script>
		<?
		}
	}else{
		?>
		<table border=0 cellpadding=0 cellspacing=0 width=500 style="table-layout:fixed">
			<tr>
				<td align="center">변경 내역이 없습니다.</td>
			</tr>
		</table>
		<?
	}
	mysql_free_result($result);
	
}


//업체 운영 기본관리 조회
function getShopMoreInfo() {

	$sql = "select * from shop_more_info";
	$result=mysql_query($sql,get_db_conn());

	$data=mysql_fetch_array($result);
	mysql_free_result($result);

	return $data;
}

//입점업체 추가 정보 조회
function getVenderMoreInfo($vender) {

	$sql = "select * from vender_more_info where vender='".$vender."'";
	$result=mysql_query($sql,get_db_conn());

	$data=mysql_fetch_array($result);
	mysql_free_result($result);

	return $data;
}

//상품 수수료 조회
function getProductCommission($productcode) {

	$sql = "select * from product_commission where productcode='".$productcode."'";
	$result=mysql_query($sql,get_db_conn());

	$data=mysql_fetch_array($result);
	mysql_free_result($result);

	return $data;

}

//상품 수수료 저장.
function insertCommission($vender, $productcode, $up_rq_com, $up_rq_cost, $up_rq_name, $admin_chk, $admin_id ) {
	
	if ($up_rq_com.length>0 || $up_rq_cost.length>0 ) {
		//정산 기준 조회 
		
		$shop_more_info = getShopMoreInfo();
		$account_rule = $shop_more_info['account_rule'];
		
		//입점업체 추가 정보
		$vender_more_info = getVenderMoreInfo($vender);
		$commission_type = $vender_more_info['commission_type'];


		//공급가로 운영되거나.. 수수료지만 개별 수수료일떄만 나타남.		
		if ($account_rule=="1" || $commission_type=="1") {

			$sql = "select * from product_commission where productcode='".$productcode."'";
			$result=mysql_query($sql,get_db_conn());
			
			$data_lows = mysql_num_rows($result);
			$data=mysql_fetch_array($result);
			mysql_free_result($result);

			$old_status = $data['status'];
			
			$chk = 0;
			if ($old_status == 1) {
				$chk_rq_com = $data['rq_com'];
				$chk_rq_cost = $data['rq_cost'];
				

			}else{
				$chk_rq_com = $data['cf_com'];
				$chk_rq_cost = $data['cf_cost'];
			}
			
			//공급가 일때
			if ($account_rule=="1") {
				if ($up_rq_cost != $chk_rq_cost) $chk=1;
			//수수료 일때
			}else{
				if ($up_rq_com != $chk_rq_com) $chk=1;
			}
			
			if ($chk==1) {
				$commission_sql = "";

				$status = 1;			
				$memo = "";
				if ($admin_chk == "1") {
				//관리자면 바로 승인처리
					$cf_com = $up_rq_com;
					$cf_cost = $up_rq_cost;
					$status = 2;		
					$up_vender = $vender;

					//공급가 일때
					if ($account_rule=="1") {
						$commission_sql .= "rq_cost = '".$up_rq_cost."', ";
						$commission_sql .= "cf_cost = '".$cf_cost."', ";

						$memo = "공급가 ".$cf_cost."원으로 승인 [운영본사]";
					//수수료 일때
					}else{
						$commission_sql .= "rq_com = '".$up_rq_com."', ";
						$commission_sql .= "cf_com = '".$cf_com."', ";
						
						$memo = "수수료 ".$cf_com."%로 승인 [운영본사]";
					}

					$commission_sql .= "`first_approval` = '1', ";
					$commission_sql .= "`update` = now(), ";

				}else{
						
					$admin_id = "";
					$up_vender = $vender;

					//공급가 일때
					if ($account_rule=="1") {
						$commission_sql .= "rq_cost = '".$up_rq_cost."', ";
						
						$memo = "공급가 ".$up_rq_cost."원으로";
					//수수료 일때
					}else{
						$commission_sql .= "rq_com = '".$up_rq_com."', ";

						$memo = "수수료 ".$up_rq_com."%로";
					}

					if ($data_lows>0) {						
						$memo .= " 변경요청 [입점]";
					}else{
						$memo .= " 승인요청 [입점]";
						$commission_sql .= "`first_approval` = '0', ";
					}

					$commission_sql .= "rq_date = now(), ";
				}
				
				$commission_sql .= "status = '".$status."' ";


				if ($data_lows>0) {
					
					$sql = "update product_commission set ";
					$sql .= $commission_sql;
					$sql .= " where productcode='".$productcode."'";

				}else{
					$sql = "insert product_commission set ";
					$sql .= "productcode = '".$productcode."', ";
					$sql .= $commission_sql;
				}
				mysql_query($sql,get_db_conn());

				if ($memo!='') {
					insertCommissionHistory($productcode, $memo, $admin_id, $up_vender, $up_rq_name);
				}

			}
		}
	}
}

//상품 수수료 변경 내역 저장
function insertCommissionHistory($productcode, $memo, $admin_id, $vender, $rq_name) {

	$sql = "insert commission_history set ";
	$sql .= "productcode = '".$productcode."', ";
	$sql .= "reg_date = now(), ";
	$sql .= "memo = '".$memo."', ";
	$sql .= "rq_name = '".$rq_name."', ";
	$sql .= "`type` = '2', ";
	$sql .= "admin_id = '".$admin_id."', ";
	$sql .= "vender = '".$vender."' ";

	mysql_query($sql,get_db_conn());

}


//상품 수수료 요청 승인상태 변경하기
function confirmCommission($productcode, $com_yn, $admin_id) {

	if ($productcode.length>0 ) {
		
		$shop_more_info = getShopMoreInfo();
		$account_rule = $shop_more_info['account_rule'];
		
		$sql = "select * from product_commission where productcode='".$productcode."'";
		$result=mysql_query($sql,get_db_conn());
		
		$data_lows = mysql_num_rows($result);
		$data=mysql_fetch_array($result);
		mysql_free_result($result);


		if ($data_lows>0) {
			$memo = "";

			if ($com_yn=="N") {
					
				$sql = "update product_commission set status=3, `update`=now() where productcode='".$productcode."'";
				$memo = "승인거부 [운영본부]";

			}else if ($com_yn=="Y") {

				//공급가 일때
				if ($account_rule=="1") {
							
					$sql = "update product_commission set cf_cost=rq_cost, status=2, first_approval=1, `update`=now() where productcode='".$productcode."'";					
					$memo = "공급가 ".$data['rq_cost']."원으로 변경승인 [운영본사]";

				//수수료 일때
				}else{

					$sql = "update product_commission set cf_com=rq_com, status=2, first_approval=1, `update`=now() where productcode='".$productcode."'";
					$memo = "수수료 ".$data['rq_com']."%로 변경승인 [운영본사]";
				}
			}
			
			mysql_query($sql,get_db_conn());

			if ($memo!='') {
				
				$sql = "select vender from tblproduct where productcode='$productcode'";
				$data=mysql_fetch_array($result);

				insertCommissionHistory($productcode, $memo, $admin_id, $data[0], "");
			}
		}
	}

}


// 상품 수수료 변경 히스토리
function getProductCommissionHistory($productcode, $adminchk) {
	
	
	$setup[page_num] = 10;
	$setup[list_num] = 20;

	$block=$_REQUEST["block"];
	$gotopage=$_REQUEST["gotopage"];
	if ($block != "") {
		$nowblock = $block;
		$curpage  = $block * $setup[page_num] + $gotopage;
	} else {
		$nowblock = 0;
	}

	if (($gotopage == "") || ($gotopage == 0)) {
		$gotopage = 1;
	}

	$t_count=0;
	$sql = "select count(*) as t_count from commission_history where productcode='".$productcode."'";

	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$t_count = $row->t_count;

	mysql_free_result($result);
	$pagecount = (($t_count - 1) / $setup[list_num]) + 1;


	$sql = "select * from commission_history where productcode='".$productcode."' order by reg_date desc ";
	$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];


	$result=mysql_query($sql,get_db_conn());
	
	$data_lows = mysql_num_rows($result);

	$colspan=3;
	if ($adminchk=="1") { 
		$colspan=4;
	}

	if ($data_lows > 0) {
?>

		<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=80></col>
				<col width=></col>
				<col width=80></col>
				<? if ($adminchk=="1") { ?>
				<col width=80></col>
				<? } ?>
				<TR>
					<TD colspan=<?= $colspan ?> background="images/table_top_line.gif"></TD>
				</TR>
				<tr>
					<td class="table_cell" align="center">변경날짜</td>
					<td class="table_cell1" align="center">처리 내역</td>
					<td class="table_cell1" align="center">요청자</td>
					<?
					 if ($adminchk=="1") {
					?>
					<td class="table_cell1" align="center"><a href="javascript:history_del_all('<?= $productcode ?>')">전체삭제</a></td>
					<?
					}	
					?>
				</tr>
				<TR>
					<TD colspan="<?= $colspan ?>" background="images/table_con_line.gif"></TD>
				</TR>
		<?
			
			$i=0;
			while($data= mysql_fetch_array($result)) {

				$idx = $data['seq'];
				$reg_date = substr($data['reg_date'], 0, 10);
				$meno = $data['memo'];
				$rq_name = $data['rq_name'];
				
			?>
				<tr>
					<td class="td_con2" align="center"><?= $reg_date ?></td>
					<td class="td_con1" ><?= $meno ?></td>
					<td class="td_con1" align="center">&nbsp;<?= $rq_name ?>&nbsp;</td>
					<?
					 if ($adminchk=="1") {
					?>
					<td class="td_con1" align="center"><a href="javascript:history_del('<?= $idx ?>')"><img src="images/btn_del.gif" border="0" alt="삭제" /></a></td>
					<?
					}	
					?>
				</tr>
				<TR>
					<TD colspan="<?= $colspan ?>" background="images/table_con_line.gif"></TD>
				</TR>
			<?
				$i++;
			}
		?>
		</table>
		<?

			$cnt=$i;

			if($i>0) {

				$total_block = intval($pagecount / $setup[page_num]);
				if (($pagecount % $setup[page_num]) > 0) {
					$total_block = $total_block + 1;
				}
				
				$total_block = $total_block - 1;
				if (ceil($t_count/$setup[list_num]) > 0) {

					// 이전	x개 출력하는 부분-시작
					$a_first_block = "";
					if ($nowblock > 0) {
						$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=/images/minishop/btn_miniprev_end.gif border=0 align=absmiddle></a> ";
						$prev_page_exists = true;
					}
					$a_prev_page = "";
					if ($nowblock > 0) {
						$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><img src=/images/minishop/btn_miniprev.gif border=0 align=absmiddle></a> ";

						$a_prev_page = $a_first_block.$a_prev_page;
					}

					if (intval($total_block) <> intval($nowblock)) {
						$print_page = "";
						for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
							if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
								$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
							} else {
								$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
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

								$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
							} else {
								$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
							}
						}
					}

					$a_last_block = "";
					if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
						$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
						$last_gotopage = ceil($t_count/$setup[list_num]);
						$a_last_block .= " <a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=/images/minishop/btn_mininext_end.gif border=0 align=absmiddle></a>";
						$next_page_exists = true;
					}
					$a_next_page = "";
					if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
						$a_next_page .= " <a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><img src=/images/minishop/btn_mininext.gif border=0 align=absmiddle></a>";
						$a_next_page = $a_next_page.$a_last_block;
					}
				} else {
					$print_page = "<B>[1]</B>";
				}
				$pageing=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
			}

			echo $pageing;
		?>
			<form name="pageForm" method="post">
				<input type=hidden name='productcode' value='<?=$productcode?>'>
				<input type=hidden name='block' value='<?=$block?>'>
				<input type=hidden name='gotopage' value='<?=$gotopage?>'>
			</form>

			<script type="text/javascript">
		
			function GoPage(block,gotopage) {
				document.pageForm.block.value=block;
				document.pageForm.gotopage.value=gotopage;
				document.pageForm.submit();
			}

			</script>
		<?
		if ($adminchk=="1") {
		?>
			<iframe style="display:none" id="manageHistory"></iframe>
			<script type="text/javascript">
			<!--
				function history_del(idx) {

					if (confirm("해당내역을 삭제하시겠습니까? 삭제된 내역은 복구하실 수 없습니다.")) {
						mH = document.getElementById('manageHistory');
						mH.src="/admin/vender_prdtcom_delete.php?type=one&idx="+idx;
					}
				}

				function history_del_all(productcode) {
			
					if (confirm("모든 내역을 삭제하시겠습니까? 삭제된 내역은 복구하실 수 없습니다.")) {
						mH = document.getElementById('manageHistory');
						mH.src="/admin/vender_prdtcom_delete.php?type=all&productcode="+productcode;
					}
				}
			// -->
			</script>
		<?
		}
	}else{
		?>
		<table border=0 cellpadding=0 cellspacing=0 width=500 style="table-layout:fixed">
			<tr>
				<td align="center">변경 내역이 없습니다.</td>
			</tr>
		</table>
		<?
	}
	mysql_free_result($result);
	
}

//정산금 수정 히스토리 목록 조회
function listOrderAdjustUpdateHistory($ordercode, $vender) {

	$sql = "select * from order_adjust_update_history where ordercode='".$ordercode."' and vender='".$vender."' order by reg_date desc";
	
	$result=mysql_query($sql,get_db_conn());
	return $result;
}

//정산금 수정(추가, 차감) 
function addOrderAdjustDetail($ordercode, $vender, $adjust, $memo) {

	$data = selectOrderAdjustDetail($ordercode, $vender);

	$old_adjust = $data['sumadjust'];
	$result_adjust = $old_adjust + $adjust;

	$deli_date = $data['deli_date'];

	$sql = "Insert order_adjust_detail set ";
	$sql .= "ordercode = '".$ordercode."', ";
	$sql .= "productcode = 'ADMIN0000XXXX', ";
	$sql .= "vender = '".$vender."', ";
	$sql .= "deli_date = '".$deli_date."', ";
	$sql .= "price = '0', ";
	$sql .= "deli_price = '0', ";
	$sql .= "reserve = '0', ";
	$sql .= "cou_price = '0', ";
	$sql .= "account_rule = '0', ";
	$sql .= "commission_type = '0', ";
	$sql .= "relay = '0', ";
	$sql .= "rate = '0', ";
	$sql .= "cost = '0', ";
	$sql .= "rate_price = '0', ";
	$sql .= "surtax = '0', ";
	$sql .= "adjust = '".$adjust."', ";
	$sql .= "status = '1' ";

	mysql_query($sql,get_db_conn());
	
	$sql = "Insert order_adjust_update_history set ";
	$sql .= "ordercode = '".$ordercode."', ";
	$sql .= "vender = '".$vender."', ";

	$sql .= "move_adjust = '".$adjust."', ";
	$sql .= "old_adjust = '".$old_adjust."', ";
	$sql .= "result_adjust = '".$result_adjust."', ";
	$sql .= "memo = '".$memo."', ";
	$sql .= "reg_date = now() ";
	
	mysql_query($sql,get_db_conn());
}


//정산금 조회
function selectOrderAdjustDetail($ordercode, $vender) {

	$sql.="SELECT SUM(IF((productcode!='99999999990X' AND NOT (productcode LIKE 'COU%')), price,NULL)) as sumprice, ";
	$sql.= "SUM(reserve) as sumreserve, ";
	$sql.= "SUM(deli_price) as sumdeliprice, ";
	$sql.= "SUM(cou_price) as sumcouprice, ";
	$sql.= "ordercode, deli_date, vender, sum(adjust) as sumadjust ";
	$sql.= " FROM `order_adjust_detail` o ";					
	$sql .= " where ordercode='".$ordercode."' and vender='".$vender."'";
	
	$result=mysql_query($sql,get_db_conn());

	$data=mysql_fetch_array($result);
	mysql_free_result($result);

	return $data;
	
}

// 개별 정산금 저장
function insertOrderAdjustDetail($ordercode) {
	
	$shop_more_info = getShopMoreInfo();
	$account_rule = $shop_more_info['account_rule'];
	$shop_relay = $shop_more_info['relay'];
	$status = "1";

	$sql = "	
		select p.*, c.cf_com, c.cf_cost, c.status from (
		SELECT 
		SUM(IF((b.productcode!='99999999990X' AND NOT (b.productcode LIKE 'COU%')), b.price*b.quantity,NULL)) as sumprice, 
		(select round(rate, 2) from tblvenderinfo where vender = b.vender) as rate, SUM(b.reserve*b.quantity) as sumreserve, 
		SUM(IF(b.productcode='99999999990X', b.price,NULL)) as sumdeliprice, 
		SUM(IF(b.productcode LIKE 'COU%', b.price,NULL)) as sumcouprice, a.ordercode,a.deli_date, b.productcode, b.vender, b.uid
		FROM tblorderinfo a, tblorderproduct b 
		WHERE a.ordercode=b.ordercode AND b.vender>0 AND NOT (b.productcode LIKE '999999%' AND b.productcode!='99999999990X') AND a.deli_gbn='Y' AND ((MID(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (MID(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000')) AND a.ordercode='".$ordercode."'
		and b.status !='RC'
		GROUP BY a.ordercode,b.productcode,b.vender, b.uid ) p
		left join product_commission c
		on p.productcode=c.productcode
	";
	
	$result=mysql_query($sql,get_db_conn());
	
	while($data=mysql_fetch_array($result)) {
	
		$productcode= $data['productcode'];
		$vender = $data['vender'];
		$deli_date = $data['deli_date'];

		$price = $data['sumprice'];
		$deli_price = $data['sumdeliprice'];
		$reserve = $data['sumreserve'];
		$cou_price = $data['sumcouprice'];
		$uid = $data['uid'];
		
		//쿠폰은 저장때부터 -로 저장됨.
		$r_cou_price = $cou_price * -1;

		
		$vender_more_info = getVenderMoreInfo($vender);
		$commission_type = $vender_more_info['commission_type'];
		
		//배송료가 아닐때 
		if ($productcode != '99999999990X') {
			/*
				중계업체가 아닐 경우 

					정산금액 = 상품판매금액-수수료+배송비-적립금-쿠폰할인
					정산금액 = 상품공급가+배송비-적립금-쿠폰할인

				중계업체인 경우 

					정산금액 = 상품판매금액-수수료-수수료의부가세+배송비-적립금-쿠폰할인
					정산금액 = 상품공급가-((상품판매금액-상품공급가)*0.1)+배송비-적립금-쿠폰할인

			*/
			//공급가
			if ($account_rule=="1") {
				
				$rate = 0;
				$cost = $data['cf_cost'];
				
				$rate_price = 0;
				$surtax = (int) ($price - $cost)*0.1;
				
				//중계업체
				if ($shop_relay=="1") {

					$adjust = $cost - $surtax + $deli_price - $reserve - $r_cou_price;

				//중계업체 아님
				}else{
					$adjust = $cost + $deli_price - $reserve - $r_cou_price;
					$surtax = 0;
				}

			//수수료
			}else{
				
				$cost = 0;
				
				//상품별
				if ($commission_type=="1") {
					
					$rate = $data['cf_com'];
					
				//전체
				}else{
					$rate = $data['rate'];
				}

				//수수료 환산
				$rate_price = (int) $price * ($rate / 100);
				$surtax = (int) $rate_price*0.1;

				//중계업체
				if ($shop_relay=="1") {

					$adjust = $price - $rate_price - $surtax + $deli_price - $reserve - $r_cou_price;

				//중계업체 아님
				}else{
					$adjust = $price - $rate_price + $deli_price - $reserve - $r_cou_price;					
					$surtax = 0;
				}

			}

		//배송료의 경우
		}else{

			$price = 0;
			$deli_price = $data['sumdeliprice'];
			$reserve = 0;
			$cou_price = 0;
			$account_rule = 0;
			$commission_type = 0;
			$rate = 0;
			$cost = 0;
			$rate_price = 0;
			$surtax = 0;
			$adjust = $deli_price;

		}
		
		$sql = "Insert order_adjust_detail set ";
		$sql .= "ordercode = '".$ordercode."', ";
		$sql .= "productcode = '".$productcode."', ";
		$sql .= "vender = '".$vender."', ";
		$sql .= "deli_date = '".$deli_date."', ";
		$sql .= "price = '".$price."', ";
		$sql .= "deli_price = '".$deli_price."', ";
		$sql .= "reserve = '".$reserve."', ";
		$sql .= "cou_price = '".$cou_price."', ";
		$sql .= "account_rule = '".$account_rule."', ";
		$sql .= "commission_type = '".$commission_type."', ";
		$sql .= "relay = '".$shop_relay."', ";
		$sql .= "rate = '".$rate."', ";
		$sql .= "cost = '".$cost."', ";
		$sql .= "rate_price = '".$rate_price."', ";
		$sql .= "surtax = '".$surtax."', ";
		$sql .= "adjust = '".$adjust."', ";
		$sql .= "status = '".$status."', ";
		$sql .= "uid = '".$uid."' ";
		
		mysql_query($sql,get_db_conn());
	}

	mysql_free_result($result);
}

// 개별 수수료 저장 취소
function redeliveryOrderAdjustDetail($ordercode) {

	$sql = "select * from order_adjust_detail where ordercode='".$ordercode."' ";
	$result=mysql_query($sql,get_db_conn());

	while($data=mysql_fetch_array($result)) {
		
		$productcode = $data['productcode'];
		$vender = $data['vender'];
		$deli_date = $data['deli_date'];
		$uid = $data['uid'];
		$adjust = $data['adjust'];
		
		insertRedeliveryData($ordercode, $productcode, $vender, $deli_date, $uid, $adjust);

	}
	mysql_free_result($result);
}

// 개별 수수료 저장 취소
function redeliveryOrderAdjustDetailByProduct($ordercode, $uid) {

	$sql = "select productcode from tblorderproduct where uid='".$uid."'";
	$result=mysql_query($sql,get_db_conn());
	$data=mysql_fetch_array($result);

	$productcode = $data[0];	
	mysql_free_result($result);

	if ($productcode) {

		$sql = "select * from order_adjust_detail where ordercode='".$ordercode."' and uid='".$uid."' ";
		$result=mysql_query($sql,get_db_conn());

		
		$data=mysql_fetch_array($result);
		
		$vender = $data['vender'];
		$deli_date = $data['deli_date'];
		$adjust = $data['adjust'];

		insertRedeliveryData($ordercode, $productcode, $vender, $deli_date, $uid, $adjust);

		mysql_free_result($result);
	}
}

function insertRedeliveryData($ordercode, $productcode, $vender, $deli_date, $uid, $adjust) {

	if ($adjust>0) {

		//정산전이면 정산삭제
		if ($data['status']==1) {
			$sql = "delete from order_adjust_detail where ordercode='".$ordercode."' and vender='".$vender."' and deli_date= '".$deli_date."' and uid='".$uid."'";
			mysql_query($sql,get_db_conn());

		//정산후면 같은액수의 -금 만큼 정산내역 추가
		}else{

			$new_deli_date =date("YmdHis");
			if (substr($deli_date, 0, 8)==date("Ymd")) {
				$new_deli_date = date("YmdHis", mktime(date("H"),date("i"),date("s"),date("m"), date("d")+1, date("Y"))); 
			}

			$sql = "insert into order_adjust_detail 
				select 
					ordercode,
					productcode,
					vender,
					'".$new_deli_date."',
					price*(-1),
					deli_price*(-1),
					reserve*(-1),
					cou_price*(-1),
					account_rule,
					commission_type,
					relay,
					rate,
					cost,
					rate_price*(-1),
					surtax*(-1),
					adjust*(-1),
					1,
					null,
					uid
				from order_adjust_detail 
					where ordercode='".$ordercode."' and vender='".$vender."' and deli_date= '".$deli_date."' and productcode='".$productcode."'";

			mysql_query($sql,get_db_conn());
		}
	}
}

///입점사 정보 조회
function getVenderInfo($vender) {
	
	$sql = "select * from tblvenderinfo where vender='".$vender."'";
	$result=mysql_query($sql,get_db_conn());

	$data=mysql_fetch_array($result);
	mysql_free_result($result);

	return $data;

}

//정산 비용 불러오기.
function getVenderOrderAdjust($vender, $year, $month, $day) {
	
	$_vdata = getVenderInfo($vender);
	$v_account_date = $_vdata['account_date'];
	$a_date = explode(",", $v_account_date);
	$a_date_count = count($a_date);
	
	//$chk = 0;
	$num = -1;
	for ($i=0;$i<$a_date_count;$i++) {
		if ((int) $a_date[$i] == (int) $day) {
			$num = $i;
	//		$chk++;
		}
	}
	
	//결산일 조회
	$vender_more_info = getVenderMoreInfo($vender);
	$add_date = $vender_more_info['close_date'];

	if (!$add_date || $add_date<0) {
		$add_date = 1;
	}

	//if ($chk>0) {
		
		$date = $year."-".$month."-".$day;
		
		$ad_date = selectAdjustDate($vender, $date, $day, $add_date, $a_date, $a_date_count, $num);
		$start_date = $ad_date['start_date'];
		$end_date = $ad_date['end_date'];
			
		$adjust = 0;
		$sql = "select ifnull(sum(adjust), 0) from order_adjust_detail where vender='".$vender."' and deli_date between '".$start_date."000000' and '".$end_date."235959' and status=1";
		$result=mysql_query($sql,get_db_conn());
		$data=mysql_fetch_array($result);
		mysql_free_result($result);

		$adjust = $data[0];
	//}
	$result = array();
	$result['start_date'] = $start_date;
	$result['end_date'] = $end_date;
	$result['adjust'] = $adjust;
	return $result;
}

//정산 결과(달력) 입력하기
function insertVenderOrderAccount($vender, $date, $price, $bank_account, $memo) {

	$adjust_data = getVenderOrderAdjust($vender, substr($date,0,4), substr($date,4,2), substr($date,6,2));
	$start_date = $adjust_data['start_date'];
	$end_date = $adjust_data['end_date'];
	$real_price = $adjust_data['adjust'];

	$sql = "INSERT order_account_new SET ";
	$sql.= "vender		= '".$vender."', ";
	$sql.= "date		= '".$date."', ";
	$sql.= "price		= '".$price."', ";
	$sql.= "bank_account= '".$bank_account."', ";
	$sql.= "memo		= '".$memo."', ";
	$sql.= "reg_date		= now(), ";
	$sql.= "start_date= '".$start_date."', ";
	$sql.= "end_date= '".$end_date."' ";
	mysql_query($sql,get_db_conn());

	$sql = "update order_adjust_detail set status=2, com_date='".$date."'
		where vender='".$vender."' and deli_date between '".$start_date."000000' and '".$end_date."235959' and status=1";
	mysql_query($sql,get_db_conn());

}

//정산 완료
function completeVenderOrderAccount($vender, $date) {

	$sql = "update order_adjust_detail set status=3
		where vender='".$vender."' and com_date='$date' and status=2";
	mysql_query($sql,get_db_conn());

}

//오늘이 정산 날짜인 입점업체 목록 가져오기
function getVenderToTodayOrderAccount($date) {

	$result_data = array();
	$date_s = explode("-",$date);
	$year = $date_s[0];
	$month = $date_s[1];
	$day = $date_s[2];

	$last_date = date("Y-m-t", strtotime($date));
	$last_date_s = explode("-",$last_date);
	$year_last = $last_date_s[0];
	$month_last = $last_date_s[1];
	$day_last = $last_date_s[2];

	$sql = "select v.vender, v.account_date from tblvenderinfo v left outer join vender_more_info vm on v.vender=vm.vender where  v.account_date like '%".(int) $day."%' and vm.adjust_lastday=0 ";

	if ($day=="15") {
		$sql .= " union ";
		$sql .= "select v.vender, '15,".$day_last."' as account_date from tblvenderinfo v left outer join vender_more_info vm on v.vender=vm.vender where vm.adjust_lastday=2 ";
	}

	if ($date == $last_date) {
		$sql .= " union ";
		$sql .= "select v.vender, '".$day_last."' as account_date from tblvenderinfo v left outer join vender_more_info vm on v.vender=vm.vender where vm.adjust_lastday=1 ";
	}
	
	$result=mysql_query($sql,get_db_conn());
	
	$i=0;
	while($data=mysql_fetch_array($result)) {
		
		$vender = $data[0];
		$account_date = $data[1];	
		
		$a_date = explode(",", $account_date);
		$a_date_count = count($a_date);

		for ($j=0;$j<$a_date_count;$j++) {
			if ((int) $a_date[$j] == (int) $day) {

				$result_data[$i] = $vender;
				$i++;
			}
		}
	}
	mysql_free_result($result);
	
	return $result_data;
}

//정산 상세 주문쿼리문 만들기.
function getVenderOrderAdjustList($vender, $date, $status) {
	
	$_vdata = getVenderInfo($vender);
	$v_account_date = $_vdata['account_date'];
	$a_date = explode(",", $v_account_date);
	$a_date_count = count($a_date);	
	
	$date_s = explode("-",$date);
	$year = $date_s[0];
	$month = $date_s[1];
	$day = $date_s[2];
	
	//결산일 조회
	$vender_more_info = getVenderMoreInfo($vender);
	$add_date = $vender_more_info['close_date'];

	if (!$add_date || $add_date<0) {
		$add_date = 1;
	}
	
//2015-04-29 무한루프로 인해 chk 주석 부분 제거
	$chk = 0;
	$num = -1;
	for ($i=0;$i<$a_date_count;$i++) {
		if ((int) $a_date[$i] == (int) $day) {
			$num = $i;
			$chk++;
		}
	}

	if ($chk>0) {

		$ad_date = selectAdjustDate($vender, $date, $day, $add_date, $a_date, $a_date_count, $num);
		$start_date = $ad_date['start_date'];
		$end_date = $ad_date['end_date'];
			
		$sql = " where vender='".$vender."' and deli_date between '".$start_date."000000' and '".$end_date."235959' ";
		
		if ($status) {	
			$sql .= "and status='".$status."'";
		}
		
	}else{
		
		$sql = " where 1=2";
	}
	

	return $sql;
}

//정산 상세 주문쿼리문 만들기.
function getVenderOrderAdjustListGoods($vender, $date) {
	
	$_vdata = getVenderInfo($vender);
	$v_account_date = $_vdata['account_date'];
	$a_date = explode(",", $v_account_date);
	$a_date_count = count($a_date);	
	
	$date_s = explode("-",$date);
	$year = $date_s[0];
	$month = $date_s[1];
	$day = $date_s[2];
	
	//결산일 조회
	$vender_more_info = getVenderMoreInfo($vender);
	$add_date = $vender_more_info['close_date'];

	if (!$add_date || $add_date<0) {
		$add_date = 1;
	}
	

	//$chk = 0;
	$num = -1;
	for ($i=0;$i<$a_date_count;$i++) {
		if ((int) $a_date[$i] == (int) $day) {
			$num = $i;
			//$chk++;
		}
	}

	//if ($chk>0) {
		
		$ad_date = selectAdjustDate($vender, $date, $day, $add_date, $a_date, $a_date_count, $num);
		$start_date = $ad_date['start_date'];
		$end_date = $ad_date['end_date'];
			
		$sql = "  and a.deli_date between '".$start_date."000000' and '".$end_date."235959' ";
	/*	
	}else{
		
		$sql = "";
	}
*/
	return $sql;
}


function changeOneMonthAgo($date) {


	$last_date = date("Y-m-t", strtotime($date));
	
	if ($date==$last_date) {
		
		$move_date = monthToDay($date)*(-1);
		$result_date = date("Y-m-d", strtotime($date." ".$move_date." day"));
		
	}else{

		$date_s = explode("-",$date);
		$year = $date_s[0];
		$month = $date_s[1];
		$day = $date_s[2];

		if ((int) $month == 1) {
			$year--;
			$month = "12";
		}else{
			$month = ((int) $month) - 1;

			if ($month < 10) {
				$month = "0".$month;
			}
		}

		while (!(checkdate($month,$day,$year))) {
			$day--;
		}

		$result_date = $year."-".$month."-".$day;

	}

	return $result_date;
}

function monthToDay($date) {
	
	$year = substr($date, 0, 4);
	$month = substr($date, 5, 2);

	for ($i=1; $i<=32; $i++) {
		$check = checkdate($month,$i,$year);
		if (!$check) {
			$day = $i - 1;
			break;
		}
	}

	return $day;
}


//상품 수수료 내용 복사.
function copyCommission($productcode, $new_productcode) {
	
	$sql = "insert into product_commission(productcode, rq_com, cf_com, rq_cost, cf_cost, status, first_approval, rq_date, `update`) ";
	$sql .= "select '".$new_productcode."', rq_com, cf_com, rq_cost, cf_cost, status, first_approval, now(), now() from product_commission where productcode='".$productcode."'";

	mysql_query($sql,get_db_conn());
}

//정산 시작일-끝일 조회
function selectAdjustDate($vender, $date, $day, $add_date, $a_date, $a_date_count, $num) {
	
	$adjustType = getVenderAdjustLastDay($vender);
	
	$date_s = explode("-",$date);
	$year = $date_s[0];
	$month = $date_s[1];
	$day = $date_s[2];
	
	$move_date = $add_date*(-1);

	if ($adjustType==0) {

		$yesterday = date("Y-m-d", strtotime($date." -1 day"));
		$end_date = date("Y-m-d", strtotime($date." ".$move_date." day"));

		if ($a_date_count == 1) {
			
			$start_date = changeOneMonthAgo($end_date);

		}else if($a_date_count > 1) {
			
			if ($num==0) {
				$num = $a_date_count-1;
			}else{
				$num--;
			}

			if ($a_date[$num] < 10) {
				$a_date[$num] = "0".$a_date[$num];
			}
			
			if ((int) $day< (int) $a_date[$num]){				
				
				$start_date = changeOneMonthAgo($date);
				$year = date("Y", strtotime($start_date));
				$month = date("m", strtotime($start_date));
			}

			$chk_num = $a_date[$num];
			while (!(checkdate($month,$chk_num,$year))) {
				if ($num==0) {

					$num = $a_date_count-1;

					if ((int) $month == 1) {
						$year--;
						$month = "12";
					}else{
						$month = ((int) $month) - 1;

						if ($month < 10) {
							$month = "0".$month;
						}
					}
				}else{
					$num--;
				}

				$chk_num = $a_date[$num];
			}
			$start_date = $year."-".$month."-".$chk_num;

			$start_date = date("Y-m-d", strtotime($start_date." ".$move_date." day"));

		}
		
		$start_date = date("Ymd", strtotime($start_date." 1 day"));
		$end_date = str_replace("-", "",$end_date);

	//말일기준일때
	}else if ($adjustType==1) {		

		$start_date =  date("Y-m-d", strtotime($year."-".$month."-01"));
		$start_date =  date("Ymd", strtotime($start_date." ".$move_date." day"));
		$end_date = date("Ymd", strtotime($date." ".$move_date." day"));
	
	//15일 말일 기준일때
	}else if ($adjustType==2) {		
		
		if ($day=="15") {

			$start_date =  date("Y-m-d", strtotime($year."-".$month."-01"));
			$start_date =  date("Ymd", strtotime($start_date." ".$move_date." day"));
			$end_date = date("Ymd", strtotime($date." ".$move_date." day"));

		}else{
			$start_date =  date("Ymd", strtotime($year."-".$month."-15"));
			$end_date = date("Ymd", strtotime($date." ".$move_date." day"));
		}

	}


	$result_data = Array();
	$result_data['start_date'] = $start_date;
	$result_data['end_date'] = $end_date;
	return $result_data;
}

//업체 정산형태 조회
function getVenderAdjustLastDay($vender) {
	
	$sql = "select adjust_lastday from vender_more_info where vender='".$vender."'";
	$result=mysql_query($sql,get_db_conn());

	$data=mysql_fetch_array($result);
	mysql_free_result($result);

	return $data[0];

}
?>