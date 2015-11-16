<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%">
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
		<td style="padding:10px; font-size:11px; letter-spacing:-0.5pt; line-height:15px;">* 가장 최근 주문 <font color="#F02800" style="font-size:11px;letter-spacing:-0.5pt;"><b>6개월 자료까지 제공</b></font>되며, <font color="#000000" style="font-size:11px;letter-spacing:-0.5pt;"><b>6개월 이전 자료는 일자를 지정해서 조회</b></font>하시기 바랍니다.<br>
		&nbsp;&nbsp;&nbsp;(일자별로 조회시 최대 지난 3년 동안의 주문내역 조회가 가능합니다)<br>
		*&nbsp;한 번에 조회 가능한 기간은 6개월로 일자 선택시 조회 기간을 6개월 이내로 선택하셔야 합니다.</td>
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
			<TD><A HREF="javascript:GoOrdGbn('T')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/menu06<?=($ordgbn=="T"?"on":"off")?>.gif" border="0" alt="예약배송 상품" /></a></TD>
			<TD><A HREF="javascript:GoOrdGbn('SC')"><img src="<?=$Dir?>images/common/orderlist/<?=$_data->design_orderlist?>/menu07<?=($ordgbn=="SC"?"on":"off")?>.gif" border="0" alt="정기배송 상품" /></a></TD>
			<? } ?>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td>
		<script language="javascript" type="text/javascript">
		function sDeliveryPopUp(soidx){
			window.open("/front/sDeliveryPopUp.php?soidx="+soidx,"orderpop","width=610,height=500,scrollbars=yes");
		}
		</script>
		<table cellpadding="0" cellspacing="0" width="100%" border="0" bgcolor="#F8F8F8" style="table-layout:fixed">
		<col width="180"></col>		
		<col width="300"></col>
		<col width="80"></col>
		<col></col>
		<tr height="30" align="center" bgcolor="#F8F8F8">
			<td><font color="#333333"><b>주문일(결제정보)</b></font></td>
			<td><font color="#333333"><b>배송지</b></font></td>
			<td><font color="#333333"><b>상태</b></font></td>
			<td><font color="#333333"><b>상품/수량</b></font></td>
		</tr>
		<tr>
			<td height="1" colspan="4" bgcolor="#DDDDDD"></td>
		</tr>
<?
		$total = 0;
		$total_page = 1;
		$where = array("id='".$_ShopInfo->getMemid()."'");
		array_push($where,"orderstatus!='B'");
		
		$where = (_array($where))?' where '.implode(' and ',$where):'';
		
		$sql = "select count(*) from scheduled_delivery_order ".$where;
		if(false !== $res = mysql_query($sql,get_db_conn())){
			$total = mysql_result($res,0,0);
		}
		
		$orderlists = array();
		if($total>0){
			$ordby = ' order by soidx desc';
			$limit = ' limit '.(($gotopage-1)*$setup['list_num']).','.$setup['list_num'];
			$sql = "select * from scheduled_delivery_order ".$where.$ordby.$limit;
			
			if(false === $res = mysql_query($sql,get_db_conn())){
				
			}else if(mysql_num_rows($res) < 1){
				
			}else{
				while($prow = mysql_fetch_assoc($res)){
					array_push($orderlists,$prow);
				}
			}
		}
		if($total < 1){ ?>
		<tr>
			<td colspan="4" style="padding:10px 0px; text-align:center; background:#FFFFFF">등록된 주문 내역이 없습니다.</td>
		</tr>
		<tr><td colspan="4" height="1" bgcolor="#d1d1d1"></td></tr>
<?		}else{
			foreach($orderlists as $order){
				$orderproducts = array();
				$sql = "select i.*,p.productcode,p.tinyimage,p.option_price from scheduled_delivery_order_items i left join tblproduct p on p.pridx = i.pridx where i.soidx='".$order['soidx']."' and i.pridx > 0 ";
				if(false === $res = mysql_query($sql,get_db_conn())){
				}else if(mysql_num_rows($res) <1){
				}else{
					while($item = mysql_fetch_assoc($res)){
						if(!_empty($item['tinyimage']) && file_exists($Dir.DataDir."shopimages/product/".$item['tinyimage'])){
							$item['tinyimage'] = array('ori'=>$item['tinyimage'],'src'=>$Dir.DataDir."shopimages/product/".$item['tinyimage'],'width'=>'','height'=>'');
						}else{
							$item['tinyimage'] = array('ori'=>$item['tinyimage'],'src'=>$Dir."images/no_img.gif",'width'=>'','height'=>'');
						}
						list($item['tinyimage']['width'],$item['tinyimage']['height']) = getImageSize($item['tinyimage']['src']);
						
						$item['tinyimage']['big'] = ($item['tinyimage']['width'] > $item['tinyimage']['height'])?'width':'height';
						$item['tinyimage']['bigsize'] = $item['tinyimage'][$item['tinyimage']['big']];
						array_push($return['items'],$item);
						array_push($orderproducts,$item);
					}	
				}
				$statusstr = '';
				if($order['paystatus'] < '1'){
					if($order['paymethod'] == 'B') $statusstr = '입금대기';
					else $statusstr = '결제 오류';
					$dpoplink = '';
				}else{
					$dpoplink = 'javascript:sDeliveryPopUp(\''.$order['soidx'].'\')';
					switch($order['orderstatus']){
						case 'N':
						case 'S':
							$statusstr = '대기';
							break;
						case 'D':
							$statusstr = '배송중('.$order['deliveryCnt'].'/'.($order['period']+$order['bperiod']);
							break;
						case 'C':
							$dpoplink = '';
							$statusstr = '취소';
							break;
						case 'E':
							$statusstr = '마감';
							break;
					}					
				}
?>

		<tr bgcolor="#FFFFFF" onmouseover="this.style.background='#ffffff';" onmouseout="this.style.background='#FFFFFF';">
			<td style="padding-top:10; padding-bottom:10;" class="mypage_order_line" valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr><td height="26" class="mypage_order_line2"><b><?=date('Y/m/d',strtotime($order['ordertime']))?></b></td></tr>
					<tr><td height=5></td></tr>
					<tr><td class="mypage_list_cont">결제방법 : <?=getPaymethodStr($order['paymethod'])?></td></tr>
					<tr><td class="mypage_list_cont">결제금액 : <b><font color="#000000"><?=number_format($order['price'])?></font></b>원</td></tr>
					</td></tr>
				</table>
			</td>
			<td class="mypage_order_line" style="padding-left:5px;"><?=$order['zip']?><br /><?=$order['addr1']?><br /><?=$order['addr2']?></td>
			<td class="mypage_order_line" style="text-align:center">
			<?=$statusstr?>
			<? if(!_empty($dpoplink)){ ?>
			<br /><a href="<?=$dpoplink?>">일정확인</a>
			<? } ?>
			</td>
			<td>			
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<col></col>
<?				foreach($orderproducts as $jj=>$item){
					
					if($jj>0) echo '<tr><td colspan="4" height="1" bgcolor="#E7E7E7"></tr>';	?>
					<tr>
						<td style="font-size:8pt; padding:10px; line-height:11pt;"><img src="<?=$item['tinyimage']['src']?>" border="0" width="50" style="float:left;margin-right:5px;"/><?=$item['productname']?></a></td>
						<td style="width:40px;"><?=$item['quantity']?>개</td>
					</tr>
				<? } // end for by jj ?>				
				</table>		
			</td>			
		</tr>
		<tr><td colspan="4" height="1" bgcolor="#d1d1d1"></td></tr>
<?		} // end foreach
	} // end if
?>
		</table>
		</td>
	</tr>
	<tr><td height="10"></td></tr>
	<tr>
<?
	$pages = new pages(array('total_page'=>$total_page,'page'=>$gotopage,'pageblocks'=>$setup[page_num],'links'=>"javascript:newGoPage('%u')"));
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