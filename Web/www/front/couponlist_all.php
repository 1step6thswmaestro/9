<P>
<A name="4">
<!-- 전체 쿠폰 -->
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td background="<?=$Dir?>images/common/couponlist_tab_bg.gif"><a href="javascript:DisplayMenu(1);"><img src="<?=$Dir?>images/common/couponlist_tab00_on.gif"></td>
			<td background="<?=$Dir?>images/common/couponlist_tab_bg.gif"><a href="javascript:DisplayMenu(2);"><img src="<?=$Dir?>images/common/couponlist_tab03_off.gif" border="0"></a></td>
			<td background="<?=$Dir?>images/common/couponlist_tab_bg.gif"><a href="javascript:DisplayMenu(3)"><img src="<?=$Dir?>images/common/couponlist_tab01_off.gif" border="0"></a></td>
			<td background="<?=$Dir?>images/common/couponlist_tab_bg.gif"><a href="javascript:DisplayMenu(4)"><img src="<?=$Dir?>images/common/couponlist_tab02_off.gif" border="0"></a></td>
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


						// 적용 상품
						$productcode = explode( ",", $couponRow['productcode'] );


						$saleType = ( $couponRow['sale_type']%2 ==0 )?"할인":"적립";

						$productCoupon = false;


						// 적용 카테고리 수대로
						$cate_i = 0;
						$CategoryList = "";
						foreach ( $productcode as $var ) {
							$auth = categoryAuth($var);
							if($auth['coupon'] == 'N'){
								$productcodeCNT--;
								continue;
							}
							$CategoryListName = "";

							// 카테고리
							if( strlen($var) == 12 ) {

								$codeA = substr($var,0,3);
								$codeA_SQL = "SELECT `code_name` FROM `tblproductcode` WHERE `codeA` = '".$codeA."' AND `type`='L' ";
								$codeA_Result=mysql_query($codeA_SQL,get_db_conn());
								$codeA_Row=mysql_fetch_assoc($codeA_Result);
								$CategoryListName .= $codeA_Row['code_name'];

								$codeB = substr($var,3,3);
								if( $codeB > 0 ) {
									$codeB_SQL = "SELECT `code_name` FROM `tblproductcode` WHERE `codeA` = '".$codeA."' AND `codeB` = '".$codeB."' AND `type`!='L' ";
									$codeB_Result=mysql_query($codeB_SQL,get_db_conn());
									$codeB_Row=mysql_fetch_assoc($codeB_Result);
									$CategoryListName .= ">".$codeB_Row['code_name'];
								}

								$codeC = substr($var,6,3);
								if( $codeC > 0 ) {
									$codeC_SQL = "SELECT `code_name` FROM `tblproductcode` WHERE `codeA` = '".$codeA."' AND `codeB` = '".$codeB."' AND `codeC` = '".$codeC."' AND `type`!='L' ";
									$codeC_Result=mysql_query($codeC_SQL,get_db_conn());
									$codeC_Row=mysql_fetch_assoc($codeC_Result);
									$CategoryListName .= ">".$codeC_Row['code_name'];
								}

								$codeD = substr($var,9,3);
								if( $codeD > 0 ) {
									$codeD_SQL = "SELECT `code_name` FROM `tblproductcode` WHERE `codeA` = '".$codeA."' AND `codeB` = '".$codeB."' AND `codeC` = '".$codeC."' AND `codeD` = '".$codeD."' AND `type`!='L' ";
									$codeD_Result=mysql_query($codeD_SQL,get_db_conn());
									$codeD_Row=mysql_fetch_assoc($codeD_Result);
									$CategoryListName .= ">".$codeD_Row['code_name'];
								}

								$CategoryList .="<a href='/front/productlist.php?code=".$var."'>".$CategoryListName."</a>";

								$cate_i++;
								if( $cate_i > 0 ) $CategoryList .= ", ";

							}


							// 상품
							if( strlen($var) > 12 ) {

								$productSQL = "SELECT * FROM `tblproduct` WHERE `productcode` = '".$var."' LIMIT 1 ; ";
								$productResult=mysql_query($productSQL,get_db_conn());
								$productRow=mysql_fetch_assoc($productResult);

								// 제품 코드가 있다면 출력
								if( $productRow['pridx'] ) {

									$sellPrice = $productRow['sellprice'];

									// 쿠폰 적용 금액
									$couponPrice = abs( $sellPrice - $couponRow['sale_money'] );
									$productCoupon = true;
								}
							}
						}


						//상품
						if( $productCoupon == true ) {

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
						}


						// 카테고리
						if( $cate_i > 0 ) {
			?>
				<!-- 상품 정보 출력 START -->
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="table_nborder">
					<tr>
						<td valign="top" class="coupon_pimage"><img src="/data/shopimages/product/" onerror="this.src='/images/coupon_c_img.gif';"></td>
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
									<td width="95" class="coupon_btext">적용카테고리</td>
									<td class="coupon_ctext"><?=$CategoryList?></td>
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
						<td colspan="2" align="center"><a href="javascript:issue_coupon('<?=$couponRow['coupon_code']?>');"><img src="<?=$Dir?>images/common/btn_coupon.gif" border="0"></a></td>
					</tr>
				</table>
				<!-- 상품 정보 출력 END -->
			<?

						}

						//전체 사용
						if( $couponRow['productcode'] == "ALL" ){
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
												echo "발행후 ".abs($couponRow['date_start'])." 일";
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
						}



						// 줄바꿈
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