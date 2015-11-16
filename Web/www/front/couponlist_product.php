<P>
<A name="1">
<!-- 상품별 쿠폰 -->
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td background="<?=$Dir?>images/common/couponlist_tab_bg.gif"><a href="javascript:DisplayMenu(1);"><img src="<?=$Dir?>images/common/couponlist_tab00_off.gif" border="0"></a></td>
			<td background="<?=$Dir?>images/common/couponlist_tab_bg.gif"><a href="javascript:DisplayMenu(2);"><img src="<?=$Dir?>images/common/couponlist_tab03_off2.gif" border="0"></a></td>
			<td background="<?=$Dir?>images/common/couponlist_tab_bg.gif"><a href="javascript:DisplayMenu(3);"><img src="<?=$Dir?>images/common/couponlist_tab01_on.gif" border="0"></td>
			<td background="<?=$Dir?>images/common/couponlist_tab_bg.gif"><a href="javascript:DisplayMenu(4);"><img src="<?=$Dir?>images/common/couponlist_tab02_off.gif" border="0"></a></td>
			<td background="<?=$Dir?>images/common/couponlist_tab_bg.gif" width="100%" align="right">
				<a href="?tab=1&sort=new#couponlist"><font style="font-size:12px;"><?=$sortB['new'][0]?>신규등록 순<?=$sortB['new'][1]?></font></a>
				|
				<a href="?tab=1&sort=end#couponlist"><font style="font-size:12px;"><?=$sortB['end'][0]?>마감임박 순<?=$sortB['end'][1]?></font></a>
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
					while($couponRow=mysql_fetch_assoc($couponResult)) {

						// 쿠폰 기간 확인
						if( $couponRow['date_start'] < 0 ) {
							$startTime = strtotime($couponRow['date']);
							$endTime = strtotime( abs( $couponRow['date_start'] )." day", strtotime($couponRow['date']) );
						} else {
							$startTime = strtotime($couponRow['date_start']."00");
							$endTime = strtotime($couponRow['date_end']."00");
						}

						// 적용 상품
						$productcode = explode( ",", $couponRow['productcode'] );

						// 기간 만료 안된 쿠폰
						if( time() < $endTime ) {

							// 적용 상품 수대로
							$productcodeCNT = count($productcode);
							foreach ( $productcode as $var ) {
								$auth = categoryAuth($var);
								if($auth['coupon'] == 'N'){
									$productcodeCNT--;
									continue;
								}
								// 카테고리가 아닌 상품만
								if( strlen($var) > 12 ) {

									$productSQL = "SELECT * FROM `tblproduct` WHERE `productcode` = '".$var."' LIMIT 1 ; ";
									$productResult=mysql_query($productSQL,get_db_conn());
									$productRow=mysql_fetch_assoc($productResult);

									// 제품 코드가 있다면 출력
									if( $productRow['pridx'] ) {

										$sellPrice = $productRow['sellprice'];

										$saleType = ( $couponRow['sale_type']%2 ==0 )?"할인":"적립";

										// 쿠폰 적용 금액
										$couponPrice = abs( $sellPrice - $couponRow['sale_money'] );
										/*
										if( $sellPrice >= $couponRow['mini_price'] ) {
											$couponPrice = abs( $sellPrice - $couponRow['sale_money'] );
										} else {
											$couponPrice = $sellPrice;
										}
										*/

			?>
				<!-- 상품 정보 출력 START -->
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="table_nborder">
					<tr>
						<td valign="top" class="coupon_pimage"><img src="/data/shopimages/product/<?=$productRow['tinyimage']?>" width="82" onerror="this.src='/images/no_img.gif';" style="border:1px solid #e6e6e6;"></td>
						<td valign="top" width="100%">
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
									<td width="75" class="coupon_btext">상품명</td>
									<td class="coupon_ctext"><a href="/front/productdetail.php?productcode=<?=$productRow['productcode']?>"><?=$productRow['productname']?></a></td>
								</tr>
								<tr>
									<td><img src="<?=$Dir?>images/common/dot_icon.gif"></td>
									<td class="coupon_btext">유효기간</td>
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
						<td colspan="2" align="center"><a href="javascript:issue_coupon('<?=$couponRow['coupon_code']?>');"><img src="<?=$Dir?>images/common/btn_coupon.gif" border="0"></a> <a href="/front/productdetail.php?productcode=<?=$productRow['productcode']?>"><img src="<?=$Dir?>images/common/btn_buy.gif" border="0"></a></td>
					</tr>
				</table>
				<!-- 상품 정보 출력 END -->
			<?
										if( fmod($i,2) ) {
											echo "</td><td width=\"50%\" valign=\"top\" style=\"padding:28px; background-image:url(".$Dir."images/common/couponlist_b_bg.gif); background-repeat:no-repeat; background-position:right bottom;\">";
										} else {
											if( $productcodeCNT > $i ) echo "</td></tr><tr><td width=\"50%\" valign=\"top\" style=\"padding:28px; background-image:url(".$Dir."images/common/couponlist_b_bg.gif); background-repeat:no-repeat; background-position:right bottom;\">";
										}

										$i++;

									}
								}
							}
						}
					}

			?>
	</table>