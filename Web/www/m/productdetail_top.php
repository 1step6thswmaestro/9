<?
	$src = "";
	$src = $primgsrc.$_pdata->minimage;
	$size = _getImageRateSize($src,298);
	$strapline = _currentCategoryName($code);
	//$productname =_strCut($_pdata->productname,20,5,"EUC-KR");
	$productname =$_pdata->productname;
	$reservation = $_pdata->reservation;
	if(strlen($reservation)>0 &&$reservation != "0000-00-00"){
		$msgreservation = "<font color=\"#2F9D27\"><b>[예약상품]</b></font>";
		$datareservation = $reservation;
	}else{
		$msgreservation = $datareservation = "";
	}
?>
<div id="content">

	<!--
	<div class="h_area2">
		<h2><?=$strapline?></h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	-->

	<div class="pr_detail">
		
		<!-- <hgroup class="pr_title"> -->
		<hgroup class="hgr_header_wrap">
			<h4 class="h4_categorylink"><?=_getCategoryList($code)?></h4>
			<h3 class="h3_productname"><?=$msgreservation?> <?=$productname?></h3>
		</hgroup>
	
		<section class="pr_pt">
			<div class="pr_pt_wrap">
				<div class="pt_pt_box">
					<img <?=$size?> src="<?=$src?>">
				</div>
			<div>
		</section>

		<div style="margin:0px 7px; padding:8px 16px; border-top:1px solid #eeeeee; background:#f9f9f9;">
			<h4>· SNS로 소문내기</h4>
			<? include_once("./sns.php"); ?>
			<!-- <span style="letter-spacing:-1px; font-size:0.9em; color:#777777;">카카오톡이나 카카오스토리로 소문내면 <strong style="color:#ff4400">지금 바로 사용가능한 적립금 1,000원을 지급</strong>해 드려요!</span> -->
		</div>

		<form name="form1" method="post" action="./basket.php">
		<section class="detail_01">
			<table class="basic_table" width="100%" style="border-top:1px solid #e0e0e0;">
				<?	if($_pdata->consumerprice>0) {	?>
				<tr>
					<th scope="row">시중가격</th>
					<td align="right"><em class="pr_price2"><?=number_format($_pdata->consumerprice)?>원</em></td>
				</tr>
				<?	}	?>
<?
				$SellpriceValue=0;
				if(strlen($dicker=dickerview($_pdata->etctype,number_format($_pdata->sellprice),1))>0) {
					$prsellprice ="<tr>\n";
					$prsellprice.="<th style='color:#4b99f0'>판매가격</th>\n";
					$prsellprice.="<td align='right'><em class=\"pr_price\" id=\"idx_price\">".$dicker."</em> </td>\n";
					$prdollarprice="";
					$priceindex=0;
				} else if(strlen($optcode)==0 && strlen($_pdata->option_price)>0) { 
					$option_price = $_pdata->option_price;
					$pricetok=explode(",",$option_price);
					$priceindex = count($pricetok);
					for($tmp=0;$tmp<=$priceindex;$tmp++) {
						$pricetokdo[$tmp]=number_format($pricetok[$tmp]/$ardollar[1],2);
						$pricetok[$tmp]=number_format($pricetok[$tmp]);
					}
					$prsellprice ="<tr>\n";
					$prsellprice.="<th style='color:#4b99f0'>판매가격</th>\n";
					$prsellprice.="<td align='right'><em class=\"pr_price\" id=\"idx_price\">".number_format($_pdata->sellprice)."</em>원</td>\n";
					$prsellprice.="<input type=hidden name=price value=\"".number_format($_pdata->sellprice)."\">\n";
					
					$prdollarprice.="<td>해외화폐</td>\n";
					$prdollarprice.="<td align=\"right\">".$ardollar[0]." ".number_format($_pdata->sellprice/$ardollar[1],2)." ".$ardollar[2]."</td>\n";
					$prdollarprice.="<input type=hidden name=dollarprice value=\"".number_format($_pdata->sellprice/$ardollar[1],2)."\">\n";
					$SellpriceValue=str_replace(",","",$pricetok[0]);
				} else if(strlen($optcode)>0) {
					$prsellprice ="<tr>\n";
					$prsellprice.="<th style='color:#4b99f0'>판매가격</th>\n";
					$prsellprice.="<td align='right'><em class=\"pr_price\" id=\"idx_price\">".number_format($_pdata->sellprice)."</em>원</td>\n";
					$prsellprice.="<input type=hidden name=price value=\"".number_format($_pdata->sellprice)."\">\n";
					
					$prdollarprice.="<td>해외화폐</td>\n";
					$prdollarprice.="<td align=\"right\"><em class=\"pr_price\">".$ardollar[0]." ".number_format($_pdata->sellprice/$ardollar[1],2)." ".$ardollar[2]."</em></td>\n";
					$prdollarprice.="<input type=hidden name=dollarprice value=\"".number_format($_pdata->sellprice/$ardollar[1],2)."\">\n";
					$SellpriceValue=$_pdata->sellprice;
				} else if(strlen($_pdata->option_price)==0) {
					if($_pdata->assembleuse=="Y") {
						$prsellprice ="<tr>\n";
						$prsellprice.="<th style='color:#4b99f0'>판매가격</th>\n";
						$prsellprice.="<td align='right'><em class=\"pr_price\" id=\"idx_price\">".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))."</em>원</td>\n";
						$prsellprice.="<input type=hidden name=price value=\"".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))."\">\n";

						$prdollarprice.="<td>해외화폐</td>\n";
						$prdollarprice.="<td>".$ardollar[0]." ".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice)/$ardollar[1],2)." ".$ardollar[2]."</em></td>\n";
						$prdollarprice.="<input type=hidden name=dollarprice value=\"".number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice)/$ardollar[1],2)."\">\n";
						$SellpriceValue=($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice);
					} else {
						$prsellprice ="<tr>\n";
						$prsellprice.="<th style='color:#4b99f0'>판매가격</th>\n";
						
						if($mempricestr > 0){
							$prsellprice.="<td align='right'><em class=\"pr_price\" id=\"idx_price\">(회원할인)".$mempricestr."</em>원</td>\n";
						}else{
							$prsellprice.="<td align='right'><em class=\"pr_price\" id=\"idx_price\">".number_format($_pdata->sellprice)."</em>원</td>\n";
						}
						//$prsellprice.="<td align='right'><em class=\"pr_price\" id=\"idx_price\">".number_format($_pdata->sellprice)."</em>원</td>\n";
						//$prsellprice.="<td><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" align=\"absmiddle\"><b>".$strikeStart."<FONT color=\"#F02800\" id=\"idx_price\">".number_format($_pdata->sellprice)."원</FONT>".$strikeEnd.$mempricestr."</b> <span style=\"font-size:11px;\"></span></td>\n";
						$prsellprice.="<input type=hidden name=price value=\"".number_format($_pdata->sellprice)."\">\n";

						$prdollarprice.="<td>해외화폐</td>\n";
						$prdollarprice.="<td><em class=\"pr_price\">".$ardollar[0]." ".number_format($_pdata->sellprice/$ardollar[1],2)." ".$ardollar[2]."</em></td>\n";
						$prdollarprice.="<input type=hidden name=dollarprice value=\"".number_format($_pdata->sellprice/$ardollar[1],2)."\">\n";
						$SellpriceValue=$_pdata->sellprice;
					}
					$priceindex=0;
				}
				if(isSeller() == 'Y' AND $_pdata->productdisprice > 0 ){
						$prsellprice ="<tr>\n";
						$prsellprice.="<th style='color:#4b99f0'>도매가격</th>\n";
						$prsellprice.="<td align='right'><em class=\"pr_price\" id=\"idx_price\">".number_format($_pdata->productdisprice)."</em>원</td>\n";
					}

			//판매가격 및 옵션 출력
				$proption1="";
				if(strlen($_pdata->option1)>0) {
					$temp = $_pdata->option1;
					$tok = explode(",",$temp);
					$count=count($tok);

					$proption1.="<tr>\n";
					$proption1.="	<th scope=\"row\">$tok[0]</th>\n";
					$proption1.="	<td align=\"right\">";
					if ($priceindex!=0) {
						$proption1.="<select name=\"option1\" class=\"basic_select\" ";
						if($_data->proption_size>0) $proption1.="style=\"width : ".$_data->proption_size."px\" ";
						$proption1.="onchange=\"change_price(1,document.form1.option1.selectedIndex-1,";
						if(strlen($_pdata->option2)>0) $proption1.="document.form1.option2.selectedIndex-1";
						else $proption1.="''";
						$proption1.=")\">\n";
					} else {
						$proption1.="<select name=\"option1\" class=\"basic_select\" ";
						if($_data->proption_size>0) $proption1.="style=\"width : ".$_data->proption_size."px\" ";
						$proption1.="onchange=\"change_price(0,document.form1.option1.selectedIndex-1,";
						if(strlen($_pdata->option2)>0) $proption1.="document.form1.option2.selectedIndex-1";
						else $proption1.="''";
						$proption1.=")\">\n";
					}

					$optioncnt = explode(",",substr($_pdata->option_quantity,1));
					$proption1.="<option value=\"\">옵션을 선택하세요\n";
					$proption1.="<option value=\"\">---------------\n";
					for($i=1;$i<$count;$i++) {
						if(strlen($tok[$i])>0) $proption1.="<option value=\"$i\">$tok[$i]\n";
						if(strlen($_pdata->option2)==0 && $optioncnt[$i-1]=="0") $proption1.=" (품절)";
					}
					$proption1.="</select>";
					$proption1.="	</td>\n";
					$proption1.="</tr>\n";
				} else {
					//$proption1.="<input type=hidden name=option1>";
				}
				$proption2="";
				if(strlen($_pdata->option2)>0) {
					$temp = $_pdata->option2;
					$tok = explode(",",$temp);
					$count2=count($tok);
					
					$proption2.="<tr>\n";
					$proption2.="	<th scope=\"row\">$tok[0]</th>\n";
					$proption2.="	<td align=\"right\">";
					$proption2.="<select name=\"option2\" class=\"basic_select\" ";
					if($_data->proption_size>0) $proption2.="style=\"width : ".$_data->proption_size."px\" ";
					$proption2.="onchange=\"change_price(0,";
					if(strlen($_pdata->option1)>0) $proption2.="document.form1.option1.selectedIndex-1";
					else $proption2.="''";
					$proption2.=",document.form1.option2.selectedIndex-1)\">\n";
					$proption2.="<option value=\"\">옵션을 선택하세요\n";
					$proption2.="<option value=\"\">---------------\n";
					for($i=1;$i<$count2;$i++) if(strlen($tok[$i])>0) $proption2.="<option value=\"$i\">$tok[$i]\n";
					$proption2.="</select>";
					$proption2.="	</td>\n";
					$proption2.="</tr>\n";

				} else {
					//$proption2.="<input type=hidden name=option2>";
				}

				if(strlen($optcode)>0) {
					$sql = "SELECT * FROM tblproductoption WHERE option_code='".$optcode."' ";
					$result = mysql_query($sql,get_db_conn());
					if($row = mysql_fetch_object($result)) {
						$optionadd = array (&$row->option_value01,&$row->option_value02,&$row->option_value03,&$row->option_value04,&$row->option_value05,&$row->option_value06,&$row->option_value07,&$row->option_value08,&$row->option_value09,&$row->option_value10);
						$opti=0;
						$option_choice = $row->option_choice;
						$exoption_choice = explode("",$option_choice);
						$proption3.="<tr>\n";
						$proption3.="	<th scope=\"row\">상품옵션</th>\n";
						$proption3.="	<td align=\"right\">";
					//	$proption3.="<TABLE class=\"basic_table\" cellSpacing=\"0\" cellPadding=\"0\" border=\"0\">\n";
						while(strlen($optionadd[$opti])>0) {
							$proption3.="[OPT]";
							$proption3.="<select name=\"mulopt\" class=\"basic_select\" onchange=\"chopprice('$opti')\"";
							if($_data->proption_size>0) $proption3.=" style=\"width : ".$_data->proption_size."px\"";
							$proption3.=">";
							$opval = str_replace('"','',explode("",$optionadd[$opti]));
							$proption3.="<option value=\"0,0\" style=\"color:#000000;\">--- ".$opval[0].($exoption_choice[$opti]==1?"(필수)":"(선택)")." ---";
							$opcnt=count($opval);
							for($j=1;$j<$opcnt;$j++) {
								$exop = str_replace('"','',explode(",",$opval[$j]));
								$proption3.="<option value=\"".$opval[$j]."\" style=\"color:#000000;\">";
								if($exop[1]>0) $proption3.=$exop[0]."(+".$exop[1]."원)";
								else if($exop[1]==0) $proption3.=$exop[0];
								else $proption3.=$exop[0]."(".$exop[1]."원)";
							}
							$proption3.="</select><br><br><input type=hidden name=\"opttype\" value=\"0\"><input type=hidden name=\"optselect\" value=\"".$exoption_choice[$opti]."\">[OPTEND]";
							$opti++;
						}
						$proption3.="<input type=hidden name=\"mulopt\"><input type=hidden name=\"opttype\"><input type=hidden name=\"optselect\">";
					//	$proption3.="</TABLE>\n";
						$proption3.="	</td>\n";
						$proption3.="</tr>\n";
					}
					mysql_free_result($result);
				}


				for($i=0;$i<$prcnt;$i++) {
					if(substr($arexcel[$i],0,1)=="O") {	//공백

					} else if ($arexcel[$i]=="7") {	//옵션
						if(strlen($proption1)>0 || strlen($proption2)>0 || strlen($proption3)>0) {
							if(strlen($proption1)>0) {
								$proption.=$proption1;
							}
							if(strlen($proption2)>0) {
								$proption.=$proption2;
							}
							if(strlen($proption3)>0) {
								$pattern=array("[OPT]","[OPTEND]");
								$replace=array("<tr><td>","</td></tr>");
								$proption.=str_replace($pattern,$replace,$proption3);
							}
					
							echo $arproduct[$arexcel[$i]];
						} else {
							$proption ="<input type=hidden name=\"option1\">\n";
							$proption.="<input type=hidden name=\"option2\">\n";
						}
					} else if(strlen($arproduct[$arexcel[$i]])>0) {	//
						echo $arproduct[$arexcel[$i]];
						if($arexcel[$i]=="9") $dollarok="Y";
					}
				}
				if(isSeller() == 'Y' AND $_pdata->productdisprice > 0 ){
					$_pdata->sellprice = $_pdata->productdisprice;
				}else{
				$_pdata->sellprice = ( $memberprice > 0 ) ? $memberprice : $_pdata->sellprice;
				}
					$reserveconv=getReserveConversion($_pdata->reserve,$_pdata->reservetype,$_pdata->sellprice,"Y");
					//sns홍보일 경우 적립금
					if($_data->sns_ok == "Y" && $_pdata->sns_state == "Y" && $sell_memid !=""){
						$reserveconv = getReserveConversionSNS($reserveconv,$_pdata->sns_reserve2,$_pdata->sns_reserve2_type,$_pdata->sellprice,"Y");
					}
					
						

				$reserveconv=getReserveConversion($_pdata->reserve,$_pdata->reservetype,$_pdata->sellprice,"Y");
				if($reserveconv>0) {
					
					$prreserve ="<tr>\n";
					$prreserve.="<th scope=\"row\">적립금</th>\n";
					$prreserve.="<td align='right' id=\"idx_reserve\">".number_format($reserveconv)."원</td>\n";
					$prreserve.="</tr>\n";

					echo $prreserve;
				}
?>

				
				<!-- <tr>
					<th scope="row">배송비</th>
					<td>무료</td>
				</tr> -->
			</table>
			
			<table class="basic_table" width="100%">
				<?
				if(strlen($_pdata->production)>0) {
				?>	
					<tr>
						<th scope="row">제조사</th>
						<td align="right"><?=$_pdata->production?></td>
					</tr>	
				<?
				}
				?>
				<?if(strlen($reservation)>0 &&$reservation != "0000-00-00"){?>
					<tr>
						<th scope="row">배송일</th>
						<td align="right"><?=$datareservation?></td>
					</tr>
				<?
				}
				if(strlen($_pdata->brand)>0) {
					if($_data->ETCTYPE["BRANDPRO"]=="Y") {
						$prbrand = $_pdata->brand;
					} else {
						$prbrand =$_pdata->brand;
					}
					?>
					<tr>
						<th scope="row">브랜드</th>
						<td align="right"><?=$prbrand?></td>
					</tr>
					<?
				}
				if(strlen($delipriceTxt)>0) {
				?>
					<tr>
						<th scope="row">배송비</th>
						<td align="right"><?=$delipriceTxt?></td>
					</tr>
					<?
				}
				?>
				
			</table>

			<?
				if(strlen($_pdata->addcode)>0) {
			?>
				<table class="basic_table">
				<tr>
					<th scope="row">특이사항</th>
					<td align="right"><?=$_pdata->addcode?></td>
				</tr>
				</table>
			<?
				}
			?>
			<table class="basic_table tbl2" border=0 width="100%">
				<tr>
					<th scope="row">수량</th>
					<td align="right">
						<span class="button white small" onClick="quantityControlGlobal('minus',document.form1);">-</span>
						<input type="number"style="width:60px; height:19px; line-height:19px; text-align:center; border:1px solid #dddddd;" required name="quantity" min="1" value="1" />
						<span class="button white small" onClick="quantityControlGlobal('plus',document.form1);">+</span>
					</td>
				</tr>
			</table>
		</section>
		<!-- //TAB1-기본정보 -->
		<input type=hidden name=code value="<?=$code?>">
		<input type=hidden name=productcode value="<?=$productcode?>">
		<input type=hidden name=ordertype>
		<input type=hidden name=opts>
		<?=($brandcode>0?"<input type=hidden name=brandcode value=\"".$brandcode."\">\n":"")?>

		</form>
	<!-- 버튼 영역-->
	<section class="basic_btn_area btn_w1">
		<?

			if(strlen($dicker)==0) {
				if(strlen($_pdata->quantity)>0 && $_pdata->quantity<=0)
					echo "<FONT style=\"color:#F02800;\"><b>품 절</b></FONT>";
				else {
		?>
					<!-- <a href="#" class="button blue bigrounded" onClick="CheckForm('ordernow','<?=$opti?>')"><span>바로구매</span></a>
					<a href="" class="button white bigrounded" onClick="CheckForm('','<?=$opti?>')"><span>장바구니</span></a> -->
					<button class="button blue bigrounded" onClick="CheckForm('ordernow','<?=$opti?>')">바로구매</button>
					<button class="button white bigrounded" onClick="CheckForm('','<?=$opti?>')">장바구니</button>
		<?
				}
		?>
					<!-- <a href="#" class="button white bigrounded" onClick="CheckForm('wishlist','<?=$opti?>')"><span>위시리스트</span></a> -->
				<?if($_ShopInfo->getMemid() != ""){?>
				<button class="button white bigrounded" onClick="CheckForm('wishlist','<?=$opti?>')">위시리스트</button>
				<?}else{?>
				<button class="button white bigrounded" onClick="check_login()">위시리스트</button>
				<?}?>
		<?
				}
		?>
	</section>
	</div>
	<!-- //상품 DETAIL -->
</div>
<a name="tapTop"></a>