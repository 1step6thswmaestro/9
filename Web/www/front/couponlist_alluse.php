<P>
<A name="3">
<!-- 전체사용 쿠폰 -->
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td background="<?=$Dir?>images/common/couponlist_tab_bg.gif"><a href="javascript:DisplayMenu(1);"><img src="<?=$Dir?>images/common/couponlist_tab00_off2.gif" border="0"></a></td>
			<td background="<?=$Dir?>images/common/couponlist_tab_bg.gif"><a href="javascript:DisplayMenu(2);"><img src="<?=$Dir?>images/common/couponlist_tab03_on.gif"></td>
			<td background="<?=$Dir?>images/common/couponlist_tab_bg.gif"><a href="javascript:DisplayMenu(3);"><img src="<?=$Dir?>images/common/couponlist_tab01_off.gif" border="0"></a></td>
			<td background="<?=$Dir?>images/common/couponlist_tab_bg.gif"><a href="javascript:DisplayMenu(4);"><img src="<?=$Dir?>images/common/couponlist_tab02_off.gif" border="0"></a></td>
			<td background="<?=$Dir?>images/common/couponlist_tab_bg.gif" width="100%" align="right">
				<a href="?tab=3&sort=new#couponlist"><font style="font-size:12px;"><?=$sortB['new'][0]?>신규등록 순<?=$sortB['new'][1]?></font></a>
				|
				<a href="?tab=3&sort=end#couponlist"><font style="font-size:12px;"><?=$sortB['end'][0]?>마감임박 순<?=$sortB['end'][1]?></font></a>
			</td>
		</tr>
	</table>

	<table cellpadding="0" cellspacing="0" width="100%" border="0" class="table_border">
		<tr>
			<td width="50%" valign="top" style="padding:28px; background-image:url(<?=$Dir?>images/common/couponlist_b_bg.gif); background-repeat:no-repeat; background-position:right bottom;">
			<?
					// 쿠폰 불러오기
					$i=1;
					$couponSQL = "
						SELECT
							`coupon_name`,
							`coupon_code`,
							`date_start`,
							`date_end`,
							`date`,
							`productcode`,
							`sale_money`,
							`sale_type`
						FROM
							`tblcouponinfo`
						WHERE
							`issue_type` = 'Y' ";
				
					if(count($limitcouponarray)>0){
						$couponSQL .= " AND coupon_code NOT IN(".implode(",",$limitcouponarray).") ";
					}
					$couponSQL .= " ORDER BY ".$SQL_SORT;

					$couponResult=mysql_query($couponSQL,get_db_conn());

					$couponCNT = mysql_num_rows($couponResult);

					while($couponRow=mysql_fetch_assoc($couponResult)) {

						// 쿠폰 기간 확인
						if( $couponRow['date_start'] < 0 ) {
							$startTime = strtotime($couponRow['date']);
							$endTime = strtotime( abs( $couponRow['date_start'] )." day", strtotime($couponRow['date']) );
						} else {
							$startTime = strtotime($couponRow['date_start']."00");
							$endTime = strtotime($couponRow['date_end']."00");
						}


						$saleType = ( $couponRow['sale_type']%2 ==0 )?"할인":"적립";
			?>
				<!-- 상품 정보 출력 START -->
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="table_nborder">
					<tr>
						<td valign="top" class="coupon_pimage"><img src="/data/shopimages/product/" onerror="this.src='/images/coupon_t_img.gif';"></td>
						<td width="100%" valign="top">
							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td>
										<font class="coupon_btext"><?=$couponRow['coupon_name']?></font><br>
										<font class="coupon_stext">쿠폰번호 : <?=$couponRow['coupon_code']?></font><br>
										<font class="coupon_stext"><b><?=$saleType.'쿠폰'?></b></font><br>
										<font class="coupon_ptext"><b><?=number_format($couponRow['sale_money'])?><font style="font-size:12px;"><?=($couponRow['sale_type']<3?'%':'원')?> <?=$saleType?></font></b></font>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td height="5"></td></tr>
					<tr><td colspan="2" height="1" bgcolor="#e6e6e6"></td></tr>
					<tr><td height="15"></td></tr>
					<tr>
						<td colspan="2">
							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="6"><img src="<?=$Dir?>images/common/dot_icon.gif"></td>
									<td width="95" class="coupon_btext">유효기간</td>
									<td>
										<?
											if($couponRow['date_start'] < 0) {
												echo "받은후 ".abs($couponRow['date_start'])." 일";
											} else{
												echo date("m/d",$startTime).' ~ '.date("m/d",$endTime);
											}
										?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr>
						<td colspan="2" align="center"><a href="javascript:issue_coupon('<?=$couponRow['coupon_code']?>');"><img src="<?=$Dir?>images/common/btn_coupon.gif" border="0"></a></td>
					</tr>
				</table>
				<!-- 상품 정보 출력 END -->
			<?
										if( fmod($i,2) ) {
											echo "</td><td width=\"50%\" valign=\"top\" style=\"padding:28px; background-image:url(".$Dir."images/common/couponlist_b_bg.gif); background-repeat:no-repeat; background-position:right bottom;\">";
										} else {
											if( $couponCNT > $i ) echo "</td></tr><tr><td width=\"50%\" valign=\"top\" style=\"padding:28px; background-image:url(".$Dir."images/common/couponlist_b_bg.gif); background-repeat:no-repeat; background-position:right bottom;\">";
										}

										$i++;
					}


			?>

			</td>
		</tr>
	</table>