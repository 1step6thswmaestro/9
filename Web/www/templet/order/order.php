<table cellpadding="0" cellspacing="0" width="100%" height="100%" border="0">
<tr>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%" height="100%">
	<form name=form1 action="<?=sprintf($Dir.FrontDir."%s.php", ((substr($ordertype,0,6)== "pester")? "pestersend":(($socialshopping == "social")? "ordersend3":"ordersend")) )?>" method=post>
	<input type=hidden name="addorder_msg" value="">
	<tr>
		<td>

		<!-- 주문 상세내역 START -->
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td align="right" valign="bottom" style="font-size:11px; letter-spacing:-0.5pt; padding-right:10"><?if(substr($ordertype,0,6)!= "pester") {?><font color="#A1A1A1">주문정보를 입력하신 후, <font color="#ee1a02">결제버튼</font>을 눌러주세요.</font><?}?></td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<col width="60"></col>
			<col></col>
			<col width="60"></col>
			<col width="75"></col>
			<col width="45"></col>
			<col width="80"></col>
			<tr>
				<td height="2" colspan="6" bgcolor="#000000"></td>
			</tr>
			<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
				<td colspan="2"><font color="#333333"><b>상품명/옵션</b></font></td>
				<td><font color="#333333"><b>적립금</b></font></td>
				<td><font color="#333333"><b>상품가격</b></font></td>
				<td><font color="#333333"><b>수량</b></font></td>
				<td><font color="#333333"><b>주문금액</b></font></td>
			</tr>
			<tr>
				<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
			</tr>
<?
	if(strlen($tblbasket)==0) {
		$tblbasket= "tblbasket";
	}
	$sql = "SELECT b.vender,(select IF(deli_super='S',NULL,b.vender) from tblvenderinfo where vender = b.vender) as deli_super FROM ".$tblbasket." a, tblproduct b WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND a.productcode=b.productcode GROUP BY deli_super ";
	$res=mysql_query($sql,get_db_conn());

	$cnt=0;
	$sumprice = 0;
	$deli_price = 0;
	$reserve = 0;
	$arr_prlist=array();
	while($vgrp=mysql_fetch_object($res)) {
		unset($_vender);
		if($vgrp->deli_super != NULL ) {
			$sql = "SELECT deli_price, deli_pricetype, deli_mini, deli_limit FROM tblvenderinfo WHERE vender='".$vgrp->deli_super."' ";
			$res2=mysql_query($sql,get_db_conn());
			if($_vender=mysql_fetch_object($res2)) {
				if($_vender->deli_price==-9) {
					$_vender->deli_price=0;
					$_vender->deli_after="Y";
				}
				if ($_vender->deli_mini==0) $_vender->deli_mini=1000000000;
			}
			mysql_free_result($res2);

		}
		echo "<tr><td colspan=6 height=10></td></tr>\n";
	/*
		$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.productcode,b.productname,b.sellprice, ";
		$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
		$sql.= "b.etctype,b.deli_price,b.deli,b.sellprice*a.quantity as realprice, b.selfcode,a.assemble_list,a.assemble_idx,a.package_idx ";
		$sql.= ", a.basketidx, b.sns_state,b.present_state,b.pester_state,b.sns_reserve2,b.sns_reserve2_type, a.sell_memid "; //sns 및 기타 추가기능 정보
		$sql.= "FROM ".$tblbasket." a, tblproduct b WHERE b.vender='".$vgrp->vender."' ";
		$sql.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
		$sql.= "AND a.productcode=b.productcode ";
		$sql.= "ORDER BY a.date DESC ";
		$result=mysql_query($sql,get_db_conn());
*/
		$result = getBasketByResource($tblbasket,$vgrp->deli_super);
		$vender_sumprice = 0;
		$vender_delisumprice = 0;//해당 입점업체의 기본배송비 총 구매액
		$vender_deliprice = 0;
		$deli_productprice=0;
		$reserve_price = 0;
		$deli_init = false;

		while($row = mysql_fetch_object($result)) {
			if (strlen($row->option_price)>0 && $row->opt1_idx==0) {
				$sql = "DELETE FROM ".$tblbasket." WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
				$sql.= "AND productcode='".$row->productcode."' AND opt1_idx='".$row->opt1_idx."' ";
				$sql.= "AND opt2_idx='".$row->opt2_idx."' AND optidxs='".$row->optidxs."' ";
				mysql_query($sql,get_db_conn());

				echo "<script>alert('필수 선택 옵션 항목이 있습니다.\\n옵션을 선택하신후 장바구니에\\n담으시기 바랍니다.');location.href=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\";</script>";
				exit;
			}
			if(ereg("^(\[OPTG)([0-9]{4})(\])$",$row->option1)){
				$optioncode = substr($row->option1,5,4);
				$row->option1="";
				$row->option_price="";
				if($row->optidxs!="") {
					$tempoptcode = substr($row->optidxs,0,-1);
					$exoptcode = explode(",",$tempoptcode);

					$sqlopt = "SELECT * FROM tblproductoption WHERE option_code='".$optioncode."' ";
					$resultopt = mysql_query($sqlopt,get_db_conn());
					if($rowopt = mysql_fetch_object($resultopt)){
						$optionadd = array (&$rowopt->option_value01,&$rowopt->option_value02,&$rowopt->option_value03,&$rowopt->option_value04,&$rowopt->option_value05,&$rowopt->option_value06,&$rowopt->option_value07,&$rowopt->option_value08,&$rowopt->option_value09,&$rowopt->option_value10);
						$opti=0;
						$optvalue="";
						$option_choice = $rowopt->option_choice;
						$exoption_choice = explode("",$option_choice);
						while(strlen($optionadd[$opti])>0){
							if($exoption_choice[$opti]==1 && $exoptcode[$opti]==0){
								$delsql = "DELETE FROM ".$tblbasket." WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
								$delsql.= "AND productcode='".$row->productcode."' ";
								$delsql.= "AND opt1_idx='".$row->opt1_idx."' AND opt2_idx='".$row->opt2_idx."' ";
								$delsql.= "AND optidxs='".$row->optidxs."' ";
								mysql_query($delsql,get_db_conn());
								echo "<script>alert('필수 선택 옵션 항목이 있습니다.\\n옵션을 선택하신후 장바구니에\\n담으시기 바랍니다.');location.href=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\";</script>";
								exit;
							}
							if($exoptcode[$opti]>0){
								$opval = explode("",str_replace('"','',$optionadd[$opti]));
								$optvalue.= ", ".$opval[0]." : ";
								$exop = explode(",",str_replace('"','',$opval[$exoptcode[$opti]]));
								if ($exop[1]>0) $optvalue.=$exop[0]."(<font color=#FF3C00>+".number_format($exop[1])."원</font>)";
								else if($exop[1]==0) $optvalue.=$exop[0];
								else $optvalue.=$exop[0]."(<font color=#FF3C00>".number_format($exop[1])."원</font>)";
								$row->sellprice+=$exop[1];
							}
							$opti++;
						}
						$optvalue = substr($optvalue,1);
					}
				}
			} else {
				$optvalue="";
			}

			$cnt++;

			$assemble_str="";
			$package_str="";

			#####################상품별 회원할인율 적용 시작#######################################
			$dSql = "SELECT discountrates,discountprices,over_discount FROM tblmemberdiscount ";
			$dSql .= "WHERE productcode='$row->productcode' AND group_code='".$_ShopInfo->getMemgroup()."'";
			$dResult = mysql_query($dSql,get_db_conn());
			$dRow = mysql_fetch_object($dResult);

			$old_sellprice = $row->sellprice;
			if($dRow->discountprices>0){
				$row->sellprice = $row->sellprice - $dRow->discountprices;
				$row->realprice = $row->sellprice*$row->quantity;
			}
			#####################상품별 회원할인율 적용 끝 #######################################

			if($row->assemble_idx>0 && strlen(str_replace("","",$row->assemble_list))>0) {
				$assemble_list_proexp = explode("",$row->assemble_list);
				//$alprosql = "SELECT productcode,productname,sellprice FROM tblproduct ";
				$alprosql = "SELECT productcode,productname,".((isSeller()=='Y')?'if(productdisprice>0,productdisprice,sellprice) as sellprice':'sellprice')." FROM tblproduct ";
				$alprosql.= "WHERE productcode IN ('".implode("','",$assemble_list_proexp)."') ";
				$alprosql.= "AND display = 'Y' ";
				$alprosql.= "ORDER BY FIELD(productcode,'".implode("','",$assemble_list_proexp)."') ";
				$alproresult=mysql_query($alprosql,get_db_conn());

				$assemble_str ="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
				$assemble_str.="		<td width=\"100%\">\n";
				$assemble_str.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";

				$assemble_sellerprice=0;
				while($alprorow=@mysql_fetch_object($alproresult)) {
					$assemble_str.="		<tr>\n";
					$assemble_str.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;\">\n";
					$assemble_str.="			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
					$assemble_str.="			<col width=\"\"></col>\n";
					$assemble_str.="			<col width=\"80\"></col>\n";
					$assemble_str.="			<col width=\"120\"></col>\n";
					$assemble_str.="			<tr>\n";
					$assemble_str.="				<td style=\"padding:4px;word-break:break-all;\"><font color=\"#000000\">".$alprorow->productname."</font>&nbsp;</td>\n";
					$assemble_str.="				<td align=\"right\" style=\"padding:4px;border-left:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\"><font color=\"#000000\">".number_format((int)$alprorow->sellprice)."원</font></td>\n";
					$assemble_str.="				<td align=\"center\" style=\"padding:4px;\">본 상품 1개당 수량1개</td>\n";
					$assemble_str.="			</tr>\n";
					$assemble_str.="			</table>\n";
					$assemble_str.="			</td>\n";
					$assemble_str.="		</tr>\n";
					$assemble_sellerprice+=$alprorow->sellprice;
				}
				@mysql_free_result($alproresult);
				$assemble_str.="		</table>\n";
				$assemble_str.="		</td>\n";

				//######### 코디/조립에 따른 가격 변동 체크 ###############
				$price = $assemble_sellerprice*$row->quantity;
				$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$assemble_sellerprice,"N");
				//sns홍보일 경우 적립금
				if($_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
					$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$assemble_sellerprice,"N");
				}
				$sellprice=$assemble_sellerprice;
			} else if($row->package_idx>0 && strlen($row->package_idx)>0) {
				$package_str ="<a href=\"javascript:setPackageShow('packageidx".$cnt."');\">".$title_package_listtmp[$row->productcode][$row->package_idx]."(<font color=#FF3C00>+".number_format($price_package_listtmp[$row->productcode][$row->package_idx])."원</font>)</a>";

				$productname_package_list_exp = $productname_package_list[$row->productcode][$row->package_idx];
				if(count($productname_package_list_exp)>0) {
					$packagelist_str ="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
					$packagelist_str.="		<td width=\"100%\">\n";
					$packagelist_str.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";

					for($i=0; $i<count($productname_package_list_exp); $i++) {
						$packagelist_str.="		<tr>\n";
						$packagelist_str.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;\">\n";
						$packagelist_str.="			<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
						$packagelist_str.="			<col width=\"\"></col>\n";
						$packagelist_str.="			<col width=\"120\"></col>\n";
						$packagelist_str.="			<tr>\n";
						$packagelist_str.="				<td style=\"padding:4px;word-break:break-all;\"><font color=\"#000000\">".$productname_package_list_exp[$i]."</font>&nbsp;</td>\n";
						$packagelist_str.="				<td align=\"center\" style=\"padding:4px;border-left:1px #DDDDDD solid;\">본 상품 1개당 수량1개</td>\n";
						$packagelist_str.="			</tr>\n";
						$packagelist_str.="			</table>\n";
						$packagelist_str.="			</td>\n";
						$packagelist_str.="		</tr>\n";
					}
					$packagelist_str.="		</table>\n";
					$packagelist_str.="		</td>\n";
				} else {
					$packagelist_str ="		<td width=\"50\" valign=\"top\" style=\"padding-left:12px;\" nowrap><font color=\"#FF7100\" style=\"line-height:10px;\">┃<br>┗━<b>▶</b></font></td>\n";
					$packagelist_str.="		<td width=\"100%\">\n";
					$packagelist_str.="		<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-left:1px #DDDDDD solid;border-top:1px #DDDDDD solid;border-right:1px #DDDDDD solid;\">\n";
					$packagelist_str.="		<tr>\n";
					$packagelist_str.="			<td bgcolor=\"#FFFFFF\" style=\"border-bottom:1px #DDDDDD solid;padding:4px;word-break:break-all;\"><font color=\"#000000\">구성상품이 존재하지 않는 패키지</font></td>\n";
					$packagelist_str.="		</tr>\n";
					$packagelist_str.="		</table>\n";
					$packagelist_str.="		</td>\n";
				}
				//######### 옵션에 따른 가격 변동 체크 ###############
				if (strlen($row->option_price)==0) {
					$sellprice=$row->sellprice+$price_package_listtmp[$row->productcode][$row->package_idx];
					$price = $sellprice*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$sellprice,"N");
					//sns홍보일 경우 적립금
					if($_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$sellprice,"N");
					}
				} else if (strlen($row->opt1_idx)>0) {
					$option_price = $row->option_price;
					$pricetok=explode(",",$option_price);
					$priceindex = count($pricetok);
					$sellprice=$pricetok[$row->opt1_idx-1]+$price_package_listtmp[$row->productcode][$row->package_idx];
					$price = $sellprice*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$sellprice,"N");
					//sns홍보일 경우 적립금
					if($_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$sellprice,"N");
					}
				}
			} else {
				//######### 옵션에 따른 가격 변동 체크 ###############
				if (strlen($row->option_price)==0) {
					$price = $row->sellprice*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"N");
					//sns홍보일 경우 적립금
					if($_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$row->sellprice,"N");
					}
					$sellprice=$row->sellprice;
				} else if (strlen($row->opt1_idx)>0) {
					$option_price = $row->option_price;
					$pricetok=explode(",",$option_price);
					$priceindex = count($pricetok);
					$price = $pricetok[$row->opt1_idx-1]*$row->quantity;
					$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$pricetok[$row->opt1_idx-1],"N");
					//sns홍보일 경우 적립금
					if($_data->sns_ok == "Y" && $row->sns_state == "Y" && $row->sell_memid !=""){
						$tempreserve = getReserveConversionSNS($tempreserve,$row->sns_reserve2,$row->sns_reserve2_type,$pricetok[$row->opt1_idx-1],"N");
					}
					$sellprice=$pricetok[$row->opt1_idx-1];
				}
			}

			$sumprice += $price;
			$vender_sumprice += $price;

			$deli_str = "";
			if (($row->deli=="Y" || $row->deli=="N") && $row->deli_price>0) {
				if($row->deli=="Y") {
					$deli_productprice += $row->deli_price*$row->quantity;
					$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#FF3C00>(구매수 대비 증가:".number_format($row->deli_price*$row->quantity)."원)</font></font>";
				} else {
					$deli_productprice += $row->deli_price;
					$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#FF3C00>(".number_format($row->deli_price)."원)</font></font>";
				}
			} else if($row->deli=="F" || $row->deli=="G") {
				$deli_productprice += 0;
				if($row->deli=="F") {
					$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#0000FF>(무료)</font></font>";
				} else {
					$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#38A422>(착불)</font></font>";
				}
			} else {
				$deli_init=true;
				$vender_delisumprice += $price;
			}

			$productname=$row->productname;

			$arr_prlist[$row->productcode]=$row->productname;

			$reserve += $tempreserve*$row->quantity;

			$bankonly_html = ""; $setquota_html = "";
			if (strlen($row->etctype)>0) {
				$etctemp = explode("",$row->etctype);
				for ($i=0;$i<count($etctemp);$i++) {
					switch ($etctemp[$i]) {
						case "BANKONLY": $bankonly = "Y";
							$bankonly_html = " <img src=".$Dir."images/common/bankonly.gif border=0 align=absmiddle> ";
							break;
						case "SETQUOTA":
							if ($_data->card_splittype=="O" && $price>=$_data->card_splitprice) {
								$setquotacnt++;
								$setquota_html = " <img src=".$Dir."images/common/setquota.gif border=0 align=absmiddle>";
								$setquota_html.= "</b><font color=black size=1>(";
								//$setquota_html.="3~";
								$setquota_html.= $_data->card_splitmonth.")</font>";
							}
							break;
					}
				}
			}
?>
			<tr>
				<td align="center" valign="middle" style="padding:2px;">
<?
			if(strlen($row->tinyimage)!=0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)){
				$file_size=getImageSize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				echo "<img src=\"".$Dir.DataDir."shopimages/product/".$row->tinyimage."\"";
				if($file_size[0]>=$file_size[1]) echo " width=\"50\"";
				else echo " height=\"50\"";
				echo " border=\"0\" vspace=\"1\">";
			} else {
				echo "<img src=\"".$Dir."images/no_img.gif\" width=\"50\" border=\"0\" vspace=\"1\">";
			}
?></td>
				<td style="padding:2,0,2,0">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td style="padding-left:2px;word-break:break-all;"><a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode?>"><font color="#000000"><b><?=viewproductname($productname,$row->etctype,$row->selfcode,$row->addcode) ?></b><?=$bankonly_html?><?=$setquota_html?><?=$deli_str?></font></td>
				</tr>
<?			if (strlen($row->option1)>0 || strlen($row->option2)>0 || strlen($optvalue)>0) {
?>
				<tr>
					<td style="padding:1,0,1,0;font-size:11px;letter-spacing:-0.5pt;word-break:break-all;">
					<img src="<?=$Dir?>images/common/icn_option.gif" border="0" align="absmiddle">
<?
				if (strlen($row->option1)>0 && $row->opt1_idx>0) {
					$temp = $row->option1;
					$tok = explode(",",$temp);
					$count=count($tok);
					echo $tok[0]." : ".$tok[$row->opt1_idx]."\n";
				}
				if (strlen($row->option2)>0 && $row->opt2_idx>0) {
					$temp = $row->option2;
					$tok = explode(",",$temp);
					$count=count($tok);
					echo ",&nbsp; ".$tok[0]." : ".$tok[$row->opt2_idx]."\n";
				}
				if(strlen($optvalue)>0) {
					echo $optvalue."\n";
				}
?>
					</td>
				</tr>
<?
			}
			if (strlen($package_str)>0) { // 패키지 정보
?>
				<tr>
					<td width="100%" style="padding-top:2px;font-size:11px;letter-spacing:-0.5pt;line-height:15px;word-break:break-all;"><img src="<?=$Dir?>images/common/icn_package.gif" border="0" align="absmiddle"> <?=(strlen($package_str)>0?$package_str:"")?></td>
				</tr>
<?
			}
?>
				</table>
				</td>
<? if($_data->reserve_maxuse>=0 && strlen($_ShopInfo->getMemid())>0){?>
				<td align="center"><img src="/images/common/reserve_icon.gif" style="margin-right:2px;"><font color="#333333"><? echo number_format($tempreserve) ?>원</font></td>
<?}else{?>
				<td align="center"><font color="#333333">없음</font></td>
<?}?>
				<td align="center"><font color="#333333"><B><?=number_format($sellprice)?>원</B></font></td>
				<td align="center"><font color="#333333"><?=$row->quantity?>개</font></td>
				<td align="center"><b><font color="#F02800"><? echo number_format($price) ?>원</font></b></td>
			</tr>
<?
	if (strlen($assemble_str)>0) { // 코디/조립 정보
?>
			<tr>
				<td colspan="6" style="padding:5px;padding-top:0px;padding-left:20px;">
					<table border=0 width="100%" cellpadding="0" cellspacing="0">
						<tr>
						<?=$assemble_str?>
						</tr>
					</table>
				</td>
			</tr>
<?
	}

	if (strlen($packagelist_str)>0) { // 패키지 정보
?>
			<tr id="<?="packageidx".$cnt?>" style="display:none;">
				<td colspan="6" style="padding:5px;padding-top:0px;padding-left:60px;">
					<table border=0 width="100%" cellpadding="0" cellspacing="0">
						<tr>
						<?=$packagelist_str?>
						</tr>
					</table>
				<td>
			</tr>
<?
			}

			$tapply = getProductAbleInfo($row->productcode);
			if($tapply['gift'] == 'Y') $gift_price += 	$price;	 /// ????
			if($tapply['reserve'] == 'Y') $reserve_price += $price;
			//$basket_etcimg = (count($tapply['img']) > 0)?'<li>'.implode('</li><li>',$tapply['img']).'</li>':'&nbsp;';
			/*
			$basket_etcimg = '&nbsp;';
			if(count($tapply['img']) > 0){
				unset($tapply['img']['return']); // 장바구니에서 교환환불 정보는 표시안하도록 처리
				$basket_etcimg = '<li>'.implode('</li><li>',$tapply['img']).'</li>';
			}*/
?>
			<tr><td colspan="6" height="1" bgcolor="#dddddd"></td></tr>
<?
		}
		mysql_free_result($result);

		$vender_deliprice=$deli_productprice;

		if($_vender) {
			if($_vender->deli_price>0) {
				if($_vender->deli_pricetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if ($vender_delisumprice<$_vender->deli_mini && $deli_init==true) {
					$vender_deliprice+=$_vender->deli_price;
				}
			} else if(strlen($_vender->deli_limit)>0) {
				if($_vender->deli_pricetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}
				if($deli_init==true) {
					$delilmitprice = setDeliLimit($vender_delisumprice,$_vender->deli_limit);
					$vender_deliprice+=$delilmitprice;
				}
			}
		} else {
			if($_data->deli_basefee>0) {
				if($_data->deli_basefeetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if ($vender_delisumprice<$_data->deli_miniprice && $deli_init==true) {
					$vender_deliprice+=$_data->deli_basefee;
				}
			} else if(strlen($_data->deli_limit)>0) {
				if($_data->deli_basefeetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if($deli_init==true) {
					$delilmitprice = setDeliLimit($vender_delisumprice,$_data->deli_limit);
					$vender_deliprice+=$delilmitprice;
				}
			}
		}
		$deli_price+=$vender_deliprice;
	}
	mysql_free_result($res);
	/*
		echo "<tr>\n";
		echo "	<td colspan=6 style=\"padding:3\">\n";
		echo "	<table border=0 cellpadding=5 cellspacing=1 bgcolor=#efefef width=100% style=\"table-layout:fixed\">\n";
		echo "	<col width=></col>\n";
		echo "	<col width=100></col>\n";
		echo "	<col width=120></col>\n";
		echo "	<col width=100></col>\n";
		echo "	<col width=130></col>\n";
		echo "	<tr>\n";
		echo "		<td bgcolor=#ffffff></td>\n";
		echo "		<td bgcolor=#f0f0f0 align=center><FONT COLOR=#000000>배송비</FONT></td>\n";
		echo "		<td bgcolor=#ffffff align=left style=\"padding-left:20\"><FONT COLOR=#000000>".number_format($vender_deliprice)."원</FONT></td>\n";
		echo "		<td bgcolor=#f0f0f0 align=center><FONT COLOR=#000000>합계</FONT></td>\n";
		echo "		<td bgcolor=#ffffff style=\"padding-left:20\"><FONT COLOR=#000000><B>".number_format($vender_sumprice)."원</B></FONT></td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
		echo "<tr><td colspan=6 height=1 bgcolor=\"#404040\"></td></tr>\n";
		echo "<tr><td colspan=6 height=10></td></tr>\n";
	*/

	#무이자 상품과 일반 상품이 주문할 경우
	if ($cnt!=$setquotacnt && $setquotacnt>0 && $_data->card_splittype=="O") {
		echo "<script> alert('[안내] 무이자적용상품과 일반상품을 같이 주문시 무이자할부적용이 안됩니다.');</script>";
	}

	if($sumprice>0) {
		if(strlen($group_type)>0 && $group_type!=NULL && $sumprice>=$group_usemoney) {
			$salemoney=0;
			$salereserve=0;
			if($group_type=="SW" || $group_type=="SP") {
				if($group_type=="SW") {
					$salemoney=$group_addmoney;
				} else if($group_type=="SP") {
					$salemoney=substr(((int)($sumprice*($group_addmoney/100))),0,-2)."00";
				}
			}
			if($group_type=="RW" || $group_type=="RP" || $group_type=="RQ") {
				if($group_type=="RW") {
					$salereserve=$group_addmoney;
				} else if($group_type=="RP") {
					$salereserve=$reserve*($group_addmoney-1);
				} else if($group_type=="RQ") {
					$salereserve=substr(((int)($sumprice*($group_addmoney/100))),0,-2)."00";
				}
			}
		}
?>


		<tr>
			<td colspan="6">
				<table border="0" cellpadding="0" cellspacing="0" width ="100%">
					<tr>
						<td style="padding-left:7"><a href="/front/basket.php"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/btn_basket.gif" border="0" alt="장바구니 돌아가기" /></a></td>
						<td align="right" style="padding:10px 0px 10px 0px;">
							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td>
										<table border="0" cellpadding="0" cellspacing="0" bgcolor="#fafafa" style="border:1px solid #f5f5f5;">


<?
		echo "<tr>\n";
		echo "	<td>\n";
		echo "	<table border=0 cellpadding=0 cellspacing=2 width=100%>\n";
		echo "	<col width=12></col>\n";
		echo "	<col width=></col>\n";
		echo "	<col width=130></col>\n";
		echo "	<tr>\n";
		echo "		<td><img src=".$Dir."images/common/order/".$_data->design_order."/order_skin_point.gif></td>";
		echo "		<td>상품 합계금액</td>\n";
		echo "		<td align=right><font color=#444444><b>".number_format($sumprice)."</b>원</font></td>\n";
		echo "	</tr>\n";
		if($_data->ETCTYPE["VATUSE"]=="Y") {
			$sumpricevat = return_vat($sumprice);
			echo "	<tr>\n";
			echo "		<td><img src=".$Dir."images/common/order/".$_data->design_order."/order_skin_point.gif></td>";
			echo "		<td>부가세(VAT) 합계금액</td>\n";
			echo "		<td align=right><font color=#444444><b>+ ".number_format($sumpricevat)."</b>원</font></td>\n";
			echo "	</tr>\n";
		}
		if($deli_price>0) {
			echo "	<tr>\n";
			echo "		<td><img src=".$Dir."images/common/order/".$_data->design_order."/order_skin_point.gif></td>";
			echo "		<td>배송비 합계금액</td>\n";
			echo "		<td align=right><font color=#444444><b>+ ".number_format($deli_price)."</b>원</font></td>\n";
			echo "	</tr>\n";
		}
		if($salemoney>0) {
			echo "	<tr>\n";
			echo "		<td><img src=".$Dir."images/common/order/".$_data->design_order."/order_skin_point.gif></td>";
			echo "		<td><img src=\"".$Dir."images/common/group_orderimg.gif\" align=absmiddle>&nbsp;&nbsp;<b><font color=#FF3C00>".$group_name." 추가 할인</FONT></b></td>\n";
			echo "		<td align=right>- ".number_format($salemoney)."원</td>\n";
			echo "	</tr>\n";
		}
		if($reserve>0 && $_data->reserve_maxuse>=0 && strlen($_ShopInfo->getMemid())>0) {
			echo "<tr>\n";
			echo "	<td><img src=".$Dir."images/common/order/".$_data->design_order."/order_skin_point.gif></td>";
			echo "	<td>적립금</td>\n";
			echo "	<td align=right><font color=#444444><b>".number_format($reserve)."</b>원</font></td>\n";
			echo "</tr>\n";
		}

		if($salereserve>0) {
			echo "	<tr>\n";
			echo "		<td><img src=".$Dir."images/common/order/".$_data->design_order."/order_skin_point.gif></td>";
			echo "		<td><img src=\"".$Dir."images/common/group_orderimg.gif\" align=absmiddle>&nbsp;&nbsp;<b><font color=#0000FF>".$group_name." 추가 적립</FONT></b></td>\n";
			echo "		<td align=right><FONT COLOR=\"#0000FF\"><B>".number_format($salereserve)."원</B></FONT></td>\n";
			echo "	</tr>\n";
		}
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
	} else {
		echo "<tr height=25><td colspan=6 align=center>쇼핑하신 상품이 없습니다.</td></tr>\n";
		echo "<tr><td colspan=6 height=1 bgcolor=\"#dddddd\"></td></tr>\n";
	}
?>
										</table>
									</td>
									<td width="50" align="center"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/icon_total_pay.gif" border="0" align="absmiddle"></td>
									<td align="right"><b>총 결제금액 : <font color="#F02800"><?=number_format($sumprice+$deli_price+$sumpricevat-$salemoney)?>원</font></b></td>
									<td width="10"></td>
								</tr>
							</table>
						</td>
					</tr>

<?
if(strlen($_ShopInfo->getMemid())>0 && strlen($group_code)>0 && substr($group_code,0,1)!="M") {
	$arr_dctype=array("B"=>"현금","C"=>"카드","N"=>"");
?>
					<tr>
						<td align="right" colspan="3" style="font-size:11px; padding-right:10">
							<B><?=$name?></B>님의 회원등급은 <B><FONT COLOR="#EE1A02">[<?=$org_group_name?>]</FONT></B>이며, <FONT COLOR="#EE1A02"><B><?=number_format($group_usemoney)?>원</B></FONT> 이상 <?=$arr_dctype[$group_payment]?>구매시,
<?
							if($group_type=="RW") echo "적립금에 <font color=\"#EE1A02\"><B>".number_format($group_addmoney)."</B>원</font>을 추가 적립해 드립니다.";
							else if($group_type=="RP") echo "구매 적립금의 <font color=\"#EE1A02\"><B>".number_format($group_addmoney)."</B>배</font>를 적립해 드립니다.";
							else if($group_type=="SW") echo "구매금액 <font color=\"#EE1A02\"><B>".number_format($group_addmoney)."</B>원</font>을 추가 할인해 드립니다.";
							else if($group_type=="SP") echo "구매금액의 <font color=\"#EE1A02\"><B>".number_format($group_addmoney)."</B>%</font>를 추가 할인해 드립니다.";
?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
		</td>
		</tr>
		<tr>
			<td height="1" bgcolor="#DDDDDD"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr><td height="40"></td></tr>
<?
} else {
?>
		</table>
		</td>
	</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="1" bgcolor="#DDDDDD"></td>
	</tr>
	<tr>
		<td height="40" colspan="2"></td>
	</tr>
<?}?>
<!-- 주문 상세 내역 END -->

<!-- 할인혜택 적용 START-->
<?
if (substr($ordertype,0,6)!= "pester" && $socialshopping != "social" && ((strlen($_ShopInfo->getMemid())>0 && $_data->reserve_maxuse>=0 && $user_reserve!=0) || (strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y"))) {
?>
	<tr>
		<td valign="top">
		<table cellpadding="0" cellspacing="0" width="100%" height="100%">
		<tr>
			<td valign="top" height="100%">
			<table cellpadding="0" cellspacing="0" width="100%" height="100%">
			<tr>
				<td height="38"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_title_t02.gif"></td>
			</tr>
			<tr>
				<td height="2" colspan="6" bgcolor="#000000"></td>
			</tr>
			<tr>
				<td height="30" bgcolor="#F8F8F8" style="padding-left:10px; letter-spacing:-0.5pt;" colspan="2">적립금 또는 할인/적립쿠폰을 사용하여 주문결제시 할인혜택을 적용받을 수 있습니다.</td>
			</tr>
			<tr>
				<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
			</tr>
			<tr>
				<td style="padding:15px 0px 15px 0px;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="150"></col>
				<col></col>
<?
	if (strlen($_ShopInfo->getMemid())>0 && $_data->reserve_maxuse>=0 && $user_reserve!=0) {
		if($okreserve<0){
			$okreserve=(int)($sumprice*abs($okreserve)/100);
			if($reserve_maxprice>$sumprice) {
				$okreserve=$user_reserve;
				$remainreserve=0;
			} else if($okreserve>$user_reserve) {
				$okreserve=$user_reserve;
				$remainreserve=0;
			} else {
				$remainreserve=$user_reserve-$okreserve;
			}
		}

		echo "		<tr>\n";
		echo "			<td><img src=\"".$Dir."images/common/order/".$_data->design_order."/order_skin_point.gif\" border=\"0\"><font color=\"#000000\"><b>적립금사용</b></font></td>\n";
		echo "			<td>\n";
		echo "			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
		if ($reserve_maxprice>$sumprice) {
			echo "		<tr>\n";
			echo '			<td>적립금 <input type="text" name="okreserve" class="st02_1" maxlength="8" value="'.number_format($okreserve).'" onfocus="blur();"/> 원 중에 <input type="text" name="usereserve" id="usereserve" class="st02_1" maxlength="8" value="0" onfocus="blur();" /> 원을 사용합니다.</td>';
			echo "		</tr>\n";
			echo "		<tr><td valign=bottom height=24 style=font-size:11px;>구매금액이 <font color=\"#FF5300\"><b>".number_format($reserve_maxprice)."원</b></font> 이상이면 사용가능합니다.</td></tr>";
		} else if ($user_reserve>=$_data->reserve_maxuse) {
			/// 추가
			if($reserve_price < 1){
			?>
						<tr>
							<td>적립금을 사용할 수 없습니다.<input type="hidden" name="userreserve" value="0" /></td>
						</tr>
			<?
			}else{
				?>
					<tr>
						<td><p class="st01_1"><b><?=number_format($reserve_price)?>원</b>까지 적립금으로 사용하여 구매하실 수 있습니다.</p></td>
					</tr>
					<tr>
						<td height="10"></td>
					</tr>
				<?
				// 요 밑으로 원래 코드
				echo "		<tr>\n";
				echo '			<td>사용가능 적립금 <br><input type="text" name="okreserve" class="st02_1" maxlength="8" value="'.number_format($okreserve).'" onfocus="blur();"/> 원 중에 <input type="text" name="usereserve" id="usereserve" class="st02_1" maxlength="8" value="0" /> 원을 사용합니다.';
				if($user_reserve>$reserve_limit) {
					echo ", <input type=text name=\"remainreserve\" value=\"".$remainreserve."\" size=\"10\" onfocus=\"blur();\" style=\"text-align:right;BACKGROUND-COLOR:#F7F7F7;\" class=\"input\">원 (사용준비 적립금)</td>\n";
					echo "	</tr>\n";
					echo "	<tr>\n";
					echo "		<td><font color=\"#FF5300\">* 사용준비적립금 : 1회 사용한도를 초과하는 적립금</font></td>\n";
					echo "	</tr>\n";
				} else {
					echo "		</td>\n";
					echo "	</tr>\n";
				}
			}
		} else {
			echo "		<tr>\n";
			echo '			<td>적립금 <input type="text" name="okreserve" class="st02_1" maxlength="8" value="'.number_format($okreserve).'" onfocus="blur();"/> 원 중에 <input type="text" name="usereserve" id="usereserve" class="st02_1" maxlength="8" value="0" onfocus="blur();" /> 원을 사용합니다.</td>';
			echo "		</tr>\n";
			echo "		<tr><td valign=bottom height=24 style=font-size:11px;>누적된 적립금이 <font color=\"#FF5300\"><b>".number_format($_data->reserve_maxuse)."원</b></font> 이상이면 사용가능합니다.</td></tr>";
		}
		echo "			</table>\n";
		echo "			</td>\n";
		echo "		</tr>\n";
	} else {
		echo "<input type=hidden name=\"usereserve\">";
	}

	if (strlen($_ShopInfo->getMemid())>0 && $_data->reserve_maxuse>=0 && $user_reserve!=0 && $_data->coupon_ok=="Y") {
		echo "
				<tr>
					<td HEIGHT=\"10\" colspan=\"2\" background=\"".$Dir."images/common/order/".$_data->design_order."/order_skin_line.gif\"></td>
				</tr>
		";
	}

	if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y") {
?>
				<tr>
						<td height="10"></td>
					</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>쿠폰선택</b></font></td>
					<td>
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
						<input type="text" name="dc_price" id="dc_price" class="st02_1" maxlength="8" value="0" readonly="readonly" /> 원
						<A HREF="javascript:coupon_check()" onmouseover="window.status='쿠폰선택';return true;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_btn1.gif" border="0" align="absmiddle"></A>
						<input type="hidden" name="couponlist" id="couponlist" value="" />
						<input type="hidden" name="dcpricelist" id="dcpricelist" value="" />
						<input type="hidden" name="couponproduct" id="couponproduct" value="" />
						<input type="hidden" name="coupon_price" id="coupon_price" value="0" />
						</td>
					</tr>
					<tr>
						<td valign="bottom" height="24" style="font-size:11px;">보유 쿠폰을 조회하신 후 선택적용하시면 할인(혹은 추가적립) 혜택을 받으실 수 있습니다.</td>
					</tr>
					<input type="hidden" name="bank_only" value="N">
					</table>
					</td>
				</tr>
<?
	}
?>
				</table>
				</td>
			</tr>
			<tr>
				<td bgcolor="#E9E9E9" height="1"></td>
			</tr>
			<tr>
				<td height="50" align="right" style="padding-right:10">
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td>총 결제금액 : <?=number_format($sumprice+$deli_price+$sumpricevat-$salemoney)?> 원</td>
							<td width="40" align="center"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/icon_minus_pay.gif" border="0" align="absmiddle"></td>
							<td>적립금 사용 : <b><span id="order_use_reserve">0</span></b>원</td>
							<td width="40" align="center"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/icon_minus_pay.gif" border="0" align="absmiddle"></td>
							<td>할인쿠폰 사용 : <b><span id="order_use_coupon">0</span></b>원</td>
							<td width="40" align="center"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/icon_total_pay.gif" border="0" align="absmiddle"></td>
							<td><b>최종 결제금액 :&nbsp;</b></td>
							<td><span id="order_last_price" class="basket_total_price" style="padding-bottom:3px;"><?=number_format($sumprice+$deli_price+$sumpricevat-$salemoney)?></span><b>원<input type="hidden" name="total_sumprice" id="total_sumprice" value="<?=$sumprice+$deli_price+$sumpricevat-$salemoney?>"></b></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td bgcolor="#E9E9E9" height="1"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="40"></td>
	</tr>
<?}?>
<!-- 할인혜택 적용 END -->


<!-- 구매 사은품 선택 START -->
	<tr>
		<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" height="100%">
				<tr>
					<td valign="top" height="100%">
						<table cellpadding="0" cellspacing="0" width="100%" height="100%">
							<tr>
								<td height="38"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_title_t06.gif"></td>
							</tr>
							<tr>
								<td height="2" colspan="6" bgcolor="#000000"></td>
							</tr>
							<tr>
								<td bgcolor="#F8F8F8">
									<table border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td width="150" height="30" style="padding-left:10px; letter-spacing:-0.5pt;" colspan="2">사은품 선택가능 금액 :</td>
											<td width="180"><input type="text" name="gift01" id="gift01" class="st02_1" style="background-color:#ffffff; padding-left:4px;" maxlength="8" readonly value="<?=$gift_price?>" /> 원</td>
											<td width=""><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/btn_product_list.gif"></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
							</tr>
							<tr>
								<td>
									<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr>
											<td width="150" align="center" rowspan="2" class="st13_3"><img src="/images/no_img.gif" id="gift_img" /></td>
											<td width="267" style="padding-top:25px;"><font color="#000000">
											<select name="giftval_seq" class="st13_1_1" onchange="secGift(this.value);">
												<option value="" style="font-weight:bold;">:: 사은품선택 ::</option></td>
											</select>
											</td>
											<td rowspan="2" style="padding:10px; border-left:1px solid #f2f2f2;">
												<img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>요청사항</b><br>
												<textarea name="gift_msg" style="width:100%; height:50" class="st14"></textarea>
											</td>
										</tr>
										<tr>
											<td height="40px" colspan="3">
												<div  class="st13_2" id="gift_"></div>
												<input type="hidden" name="img_" value="">
											</td>
										</tr>
										<tr>
											<td colspan="3" height="1" bgcolor="#d5d5d5"></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="40"></td>
	</tr>
<!-- 구매 사은품 선택 END -->

<!-- 주문자 정보 입력 START -->
<?
$is_sms="N";
$sql = "SELECT * FROM tblsmsinfo WHERE (mem_order='Y' OR mem_delivery='Y') ";
$result=mysql_query($sql,get_db_conn());
if($rows=mysql_num_rows($result)) {
	$is_sms="Y";
}
mysql_free_result($result);
?>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="38"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_title_t04.gif"></td>
			</tr>
			<tr>
				<td height="2" colspan="6" bgcolor="#000000"></td>
			</tr>
			<tr>
				<td height="30" bgcolor="#f8f8f8" style="padding-left:10px; letter-spacing:-0.5pt;" colspan="2">주문하시는 고객님의 정보를 입력해 주세요.</td>
			</tr>
			<tr>
				<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
			</tr>
			<tr>
				<td style="padding-top:15px;padding-bottom:15px;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="150"></col>
				<col></col>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>주문자이름</b></font></td>
					<td>
<?
		if(strlen($_ShopInfo->getMemid())>0) {
			echo "<font color=\"000000\"><B>".$name."</B></font>";
			echo "<input type=hidden name=sender_name value=\"".$name."\">\n";
		} else {
			echo "<input type=text name=sender_name size=15 maxlength=12 class=\"input\" style=\"BACKGROUND-COLOR:#F7F7F7;\">\n";
		}
?>
					</td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>휴대폰번호</b></font></td>
					<td><input type=text name="sender_hp1" value="<?=$mobile[0] ?>" size="5" maxlength="3" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="sender_hp2" value="<?=$mobile[1] ?>" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="sender_hp3" value="<?=$mobile[2] ?>" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>전화번호</b></font></td>
					<td><input type=text name="sender_tel1" value="<?=$home_tel[0] ?>" size="5" maxlength="3" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="sender_tel2" value="<?=$home_tel[1] ?>" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="sender_tel3" value="<?=$home_tel[2] ?>" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>이메일</b></font></td>
					<td><input type=text name="sender_email" value="<?=$email?>" size="30" class="input" style="width:80%;BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td bgcolor="#E9E9E9" height="1"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="40" colspan="2"></td>
	</tr>
<!-- 주문자 정보 입력 END -->


<!-- 배송지 정보 입력 START -->
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="38"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_title_t05.gif"></td>
			</tr>
			<tr>
				<td height="2" colspan="6" bgcolor="#000000"></td>
			</tr>
			<tr>
				<td height="30" bgcolor="#F8F8F8" style="padding-left:10px; letter-spacing:-0.5px;" colspan="2"><?if($ordertype  != "present"){?><input type=checkbox name="same" value="Y" onclick="SameCheck(this.checked)" style="border:none;"><B>주문자 정보와 동일합니다.</B><?}?></td>
			</tr>
			<tr>
				<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
			</tr>
			<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="padding-top:15px;padding-bottom:15px">
				<table cellpadding="0" cellspacing="0" width="100%" border="0">
				<col width="150"></col>
				<col></col>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>받는사람이름</b></font></td>
					<td><input type=text name="receiver_name" size="15" maxlength="12" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>전화번호</b></font></td>
					<td><input type=text name="receiver_tel11" size="5" maxlength="3" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="receiver_tel12" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="receiver_tel13" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>비상전화</b></font></td>
					<td><input type=text name="receiver_tel21" size="5" maxlength="3" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="receiver_tel22" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="receiver_tel23" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>이메일</b></font></td>
					<td><input type=text name="email" value="" size="30" class="input" style="width:80%;BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<?if(strlen($_ShopInfo->getMemid())>0 && $ordertype  != "present"){?>
				<TR>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>주소선택</b></font></td>
					<TD><input type=radio name="addrtype" value="H" onclick="addrchoice()" style="border:none;">자택&nbsp;<input type=radio name="addrtype" value="O" onclick="addrchoice()" style="border:none;">회사&nbsp;<input type=radio name="addrtype" value="B" onclick="addrchoice()" style="border:none;">과거 배송지&nbsp;<input type=radio name="addrtype" value="N" onclick="get_post()" style="border:none;">신규 배송지&nbsp;</TD>
				</TR>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<?}?>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>주 소</b></font></td>
					<td>
						<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
						<TR>
							<TD><input type=text name="rpost1" size="3" onclick="this.blur();get_post()" class="input" style="BACKGROUND-COLOR:#F7F7F7;">-<input type=text name="rpost2" size="3" onclick="this.blur();get_post()" class="input" style="BACKGROUND-COLOR:#F7F7F7;"><a href="javascript:get_post();"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_btn2.gif" border="0" align="absmiddle" hspace="3"></a></TD>
						</TR>
						<tr><td height="4"></td></tr>
						<TR>
							<TD><input type=text name="raddr1" size="50" readonly style="width:80%;BACKGROUND-COLOR:#F7F7F7;" class="input"> (기본주소)</TD>
						</TR>
						<tr><td height="4"></td></tr>
						<TR>
							<TD><input type=text name="raddr2" size="50" style="width:80%;BACKGROUND-COLOR:#F7F7F7;" class="input"> (상세주소)</TD>
						</TR>
						<tr><td height="4"></td></tr>
						</TABLE>
					</td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
<?
	if(count($arr_prlist)==1) {
		echo "<input type=hidden name=msg_type value=\"1\">\n";
		echo "<tr>\n";
		echo "	<td><img src=\"".$Dir."images/common/order/".$_data->design_order."/order_skin_point.gif\" border=\"0\"><font color=\"#000000\"><b>주문메세지<br>&nbsp;&nbsp;(50자내외)</b></font></td>\n";
		echo "	<td>\n";
		echo "	<textarea name=\"order_prmsg\" style=\"WIDTH:100%;HEIGHT:70px;padding:5px;line-height:17px;border:solid 1;border-color:#DFDFDF;font-size:9pt;color:333333;\"></textarea>\n";
		echo "	</td>\n";
		echo "</tr>\n";
	} else {
		echo "<input type=hidden name=msg_type value=\"2\">\n";
		echo "<tr>\n";
		echo "	<td colspan=2 id=\"msg_idx2\">\n";
		echo "	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
		echo "	<col width=150></col>\n";
		echo "	<col width=></col>\n";

		$yy=0;
		while(list($key,$val)=each($arr_prlist)) {
			echo "<tr><td colspan=2 height=3></td></tr>\n";
			echo "<tr><td colspan=2 height=1 bgcolor=#f0f0f0></td></tr>\n";
			echo "<tr><td colspan=2 height=3></td></tr>\n";
			echo "<tr>\n";
			echo "	<td><img src=\"".$Dir."images/common/order/".$_data->design_order."/order_skin_point.gif\" border=\"0\"><font color=\"#000000\"><b>주문메세지<br>&nbsp;&nbsp;(50자내외)</b></font>";
			if($yy==0) {
				echo "<br>&nbsp;&nbsp;&nbsp;<A HREF=\"javascript:change_message(1)\"><font color=red>(통합 입력)</font></A>";
			}
			echo "	</td>\n";
			echo "	<td style=\"padding:4px 0px 4px 0px; word-break:break-all;\">\n";
			echo "	<FONT COLOR=\"#000000\"><B>상품명 :</B></FONT> ".$val."<BR>\n";
			echo "	<textarea name=\"order_prmsg".$yy."\" style=\"WIDTH:100%;HEIGHT:70px;padding:5px;line-height:17px;border:solid 1;border-color:#DFDFDF;font-size:9pt;color:333333;\"></textarea>\n";
			echo "	</td>\n";
			echo "</tr>\n";
			$yy++;
		}
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "	<td colspan=2 id=\"msg_idx1\" style=\"padding:0;display:none\">\n";
		echo "	<table border=0 cellpadding=3 cellspacing=0 width=100%>\n";
		echo "	<col width=83></col>\n";
		echo "	<col width=5></col>\n";
		echo "	<col width=></col>\n";
		echo "	<tr><td colspan=3 height=3></td></tr>\n";
		echo "	<tr><td colspan=3 height=1 bgcolor=#f0f0f0></td></tr>\n";
		echo "	<tr><td colspan=3 height=3></td></tr>\n";
		echo "	<tr>\n";
		echo "		<td><img src=\"".$Dir."images/common/order/".$_data->design_order."/order_skin_point.gif\" border=\"0\"><font color=\"#000000\"><b>주문메세지<br>&nbsp;&nbsp;(50자내외)</b></font>";
		echo "		<div align=center style=\"padding-top:5px\"><A HREF=\"javascript:change_message(2)\"><font color=red>[상품별 입력]</font></A></div>";
		echo "		</td>\n";
		echo "		<td><table border=0 cellpadding=0 cellspacing=0 height=100%><tr><td width=2 bgcolor=#eeeeee><img width=2 height=0></td></tr></table></td>\n";
		echo "		<td style=\"padding-left:5\">\n";
		echo "		<textarea name=\"order_prmsg\" style=\"WIDTH:100%;HEIGHT:70px;padding:5px;line-height:17px;border:solid 1;border-color:#DFDFDF;font-size:9pt;color:333333;\"></textarea>\n";
		echo "		</td>\n";
		echo "	</tr>\n";
		echo "	</table>\n";
		echo "	</td>\n";
		echo "</tr>\n";
	}
?>
				<?// if(substr($ordertype,0,6)!= "pester" && $socialshopping != "social" && (strlen($etcmessage[0])>0 || strlen($etcmessage[1])>0 || $etcmessage[2]=="Y")) {?>
				<? if(substr($ordertype,0,6)!= "pester" && $socialshopping != "social" && (strlen($etcmessage[0])>0 || strlen($etcmessage[1])>0)) {?>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>안내메세지</b></font></td>
					<td>
					<table cellpadding="0" cellspacing="0" width="100%">
<?
		$tempmess="";
		if(strlen($etcmessage[1])>0){
			$day1=substr($etcmessage[1],0,2);
			$time1=substr($etcmessage[1],2,2);
			$time2=substr($etcmessage[1],4,2);
			$delidate=date("Ymd",mktime(0,0,0,date("m"),date("d")+$day1,date("Y")));
			$deliyear=substr($delidate,0,4);
			$delimon=substr($delidate,4,2);
			$deliday=substr($delidate,6,2);

			$tempmess.="<col width=\"140\"></col><col></col>\n";
			$tempmess.="<tr>\n";
			$tempmess.="	<td><b>희망 배송일자</b></td>\n";
			$tempmess.="	<td><input type=checkbox name=\"nowdelivery\" value=\"Y\" style=\"border:none;\">&nbsp;가능한 빨리 배송요망</td>\n";
			$tempmess.="</tr>\n";
			$tempmess.="<tr>\n";
			$tempmess.="	<td></td>\n";
			$tempmess.="	<td>&nbsp;<select name=\"year\" style=\"font-size:11px;\">";
			for($i=$deliyear;$i<=($deliyear+1);$i++) {
				$tempmess.="<option value=".$i;
				if($i==$deliyear) $tempmess.=" selected";
				$tempmess.=" style=\"#444444;\">".$i."\n";
			}
			$tempmess.="	</select>년 <select name=\"mon\" style=\"font-size:11px;\">";
			for($i=1;$i<=12;$i++) {
				$tempmess.="<option value=".$i;
				if($i==$delimon) $tempmess.=" selected";
				$tempmess.=" style=\"#444444;\">".$i."\n";
			}
			$tempmess.="	</select>월 <select name=\"day\" style=\"font-size:11px;\">";
			for($i=1;$i<=31;$i++) {
				$tempmess.="<option value=".$i;
				if($i==$deliday) $tempmess.=" selected";
				$tempmess.=" style=\"#444444;\">".$i."\n";
			}
			if(strlen($etcmessage[1])==6) {
				$tempmess.="	</select>일 <select name=\"time\" style=\"font-size:11px;\">";
				for($i=$time1;$i<$time2;$i++) {
					$value=($i<=12?"오전":"오후").$i."시 ~ ".(($i+1)<=12?"오전":"오후").($i+1)."시";
					$tempmess.="<option value='".$value."' style=\"#444444;\">".$value."\n";
				}
				$tempmess.="	</select></td>\n";
				$tempmess.="</tr>\n";
			} else {
				$tempmess.="	</select>일</td>\n";
				$tempmess.="</tr>\n";
			}
			$tempmess.="<tr><td colspan=\"2\" height=\"5\"></td></tr>\n";
			$tempmess.="<tr>\n";
			$tempmess.="	<td></td>\n";
			$tempmess.="	<td>&nbsp;<b>".$deliyear."</b>년 <b>".$delimon."</b>월 <b>".$deliday."</b>일 <font color=darkred>이후 날짜</font>를 입력하셔야 합니다.</td>\n";
			$tempmess.="</tr>\n";
			$tempmess.="<tr><td colspan=\"2\" height=\"5\"></td></tr>\n";
		}
		/*
		if($etcmessage[2]=="Y") {
			$tempmess.="<tr>\n";
			$tempmess.="	<td><font color=\"#0099CC\"><b>무통장 입금시 입금자명</b></font></td>\n";
			$tempmess.="	<td>&nbsp;<input type=\"text\" name=\"bankname\" size=\"10\" maxlength=\"10\" style=\"BACKGROUND-COLOR:#F7F7F7;\" class=\"input\"> (주문자와 같을경우 생략 가능)</td>\n";
			$tempmess.="</tr>\n";
			$tempmess.="<tr><td colspan=\"2\" height=\"5\"></td></tr>\n";
		}*/
		$tempmess.="<tr><td colspan=\"2\">".$etcmessage[0]."</td></tr>\n";

		echo $tempmess;
?>
					</table>
					</td>
				</tr>
				<?}?>



<?if(strlen($_ShopInfo->getMemid())==0) {?>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td valign="top"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>비회원<br><img SRC="<?=$Dir?>images/common/space_line.gif" width=12 height=0>정보수집 동의</b></font></td>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="BORDER-RIGHT: #dfdfdf 1px solid; BORDER-TOP: #dfdfdf 1px solid; BORDER-LEFT: #dfdfdf 1px solid; BORDER-BOTTOM: #dfdfdf 1px solid" bgColor="#ffffff"><DIV style="PADDING:5px;OVERFLOW-Y:auto;OVERFLOW-X:auto;HEIGHT:100px;WIDTH:100%;"><?=$privercybody?></DIV></td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr>
						<td align="left"><b><?=$_data->shopname?>의 <font color="#FF4C00">개인정보취급방침</FONT>에 동의하겠습니까?</b></td>
					</tr>
					<tr>
						<td align="left" style="padding-top:5px;"><input type=radio id=idx_dongiY name=dongi value="Y" style="border:none"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_dongiY><b><font color="#000000">동의합니다.</font></b></label><input type=radio id="idx_dongiN" name=dongi value="N" style="border:none"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_dongiN><b><font color="#000000">동의하지 않습니다.</font></b></label></td>
					</tr>
					</table>
					</td>
				</tr>
<?}?>


				</table>
				</td>
			</tr>
			<tr>
				<td bgcolor="#E9E9E9" height="1"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="40"></td>
	</tr>
<!-- 배송지 정보 입력 END -->


<!-- 결제수단 선택 START -->
	<tr>
		<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" height="100%">
				<tr>
					<td valign="top" height="100%">
						<table cellpadding="0" cellspacing="0" width="100%" height="100%">
							<tr>
								<td height="38"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_title_t03.gif"></td>
							</tr>
							<tr>
								<td height="2" colspan="6" bgcolor="#000000"></td>
							</tr>
							<tr>
								<td height="30" bgcolor="#F8F8F8" style="padding-left:10px; letter-spacing:-0.5pt;" colspan="2">결제방식을 선택하신 후 결제하기 버튼을 클릭해 주세요.</td>
							</tr>
							<tr>
								<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
							</tr>
							<tr>
								<td height="30" style="padding-left:10px;">
									<?
										// 결제수단 선택
										echo $payType;
									?>
								</td>
							</tr>
							<tr>
								<td height="1" colspan="6" bgcolor="#DDDDDD"></td>
							</tr>
							<tr>
								<td>


								<!-- 무통장 입금 -->
								<div id="simg1" style="display:none;">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr><td height="10"></td></tr>
										<tr>
											<td height="24" style="padding-left:26px;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000">결제금액 :</font> <FONT COLOR="#EE1A02"><b><span class="view_order_last_price"><?=number_format($sumprice+$deli_price+$sumpricevat-$salemoney)?></span>원</b></font></td>
										</tr>
										<tr>
											<td height="24" style="color:#666666; padding-left:26px;">
												<img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000">입금계좌 :</font>
												<select name="sel_bankinfo" class="st51_1_5">
												<?
												$arrpayinfo=explode("=",$_data->bank_account);
												if(strlen($arrpayinfo[1])==0) echo "<option value=\"\">입금 계좌번호 선택 (반드시 주문자 성함으로 입금)</option>\n";
												else echo "<option value=\"\" >".$arrpayinfo[1]."</option>\n";
												$count=0;
												if (strlen($arrpayinfo[0])>0) {
													$tok = strtok($arrpayinfo[0],",");
													$count = 0;
													while ($tok) {
														echo "<option value=\"".$tok."\" >".$tok."</option>\n";
														$tok = strtok(",");
														$count++;
													}
												}
												?>

												</select>
											</td>
										</tr>
										<tr>
											<td height="24" style="color:#666666; padding-left:26px;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000">입금자명 :</font> <input type="text" name="bankname" value="" size="12" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> <font color="#999999" style="font-size:11px; letter-spacing:-0.5px;">(주문자와 같을경우 생략하셔도 됩니다.)</font></td>
										</tr>
										<tr><td height="10"></td></tr>
										<tr>
											<td height="24" style="color:#666666; padding-left:26px; font-size:11px; letter-spacing:-0.5px;">- <FONT COLOR="#EE1A02">입금확인 후,</font> 배송처리가 진행되며, 안전하고 빠르게 상품을 배송합니다.</font></td>
										</tr>
										<tr><td height="10"></td></tr>
										<tr>
											<td height="1" bgcolor="#DDDDDD"></td>
										</tr>
									</table>
								</div>

								<!-- 카드결제 -->
								<div id="simg2" style="display:none;">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr><td height="10"></td></tr>
										<tr>
											<td height="24" style="padding-left:26px;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000">결제금액 :</font> <FONT COLOR="#EE1A02"><b><span class="view_order_last_price"><?=number_format($sumprice+$deli_price+$sumpricevat-$salemoney)?></span>원</b></font></td>
										</tr>
										<tr>
											<td style="color:#666666; padding-left:26px; font-size:11px; letter-spacing:-0.5px;">
												- 신용카드 정보가 상점에 남지 않으며, 128bit SSL로 암호화된 결제창이 새로 뜹니다.<br>
												- 결제 후, 카드명세서에 [<FONT COLOR="#EE1A02">결제대행사명</font>]으로 표시됩니다! </td>
										</tr>
										<tr><td height="10"></td></tr>
										<tr>
											<td height="1" bgcolor="#DDDDDD"></td>
										</tr>
									</table>
								</div>

								<!-- 실시간계좌이체 -->
								<div id="simg3" style="display:none;">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr><td height="10"></td></tr>
										<tr>
											<td height="24" style="padding-left:26px;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000">결제금액 :</font> <FONT COLOR="#EE1A02"><b><span class="view_order_last_price"><?=number_format($sumprice+$deli_price+$sumpricevat-$salemoney)?></span>원</b></font></td>
										</tr>
										<tr>
											<td style="color:#666666; padding-left:26px; font-size:11px; letter-spacing:-0.5px;">
												- 본인계좌 정보입력으로 결제금액이 이체되는 서비스 입니다.<br>
												- 인터넷뱅킹과 동일한 보안방식을 적용하므로 안전하며, 상점에 정보가 남지 않습니다.
											</td>
										</tr>
										<tr><td height="10"></td></tr>
										<tr>
											<td height="1" bgcolor="#DDDDDD"></td>
										</tr>
									</table>
								</div>

								<!-- 가상계좌 -->
								<div id="simg4" style="display:none;">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr><td height="10"></td></tr>
										<tr>
											<td height="24" style="padding-left:26px;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000">결제금액 :</font> <FONT COLOR="#EE1A02"><b><span class="view_order_last_price"><?=number_format($sumprice+$deli_price+$sumpricevat-$salemoney)?></span>원</b></font></td>
										</tr>
										<tr>
											<td style="color:#666666; padding-left:26px; font-size:11px; letter-spacing:-0.5px;">- 주의! 1회용 계좌(가상계좌) 입금시, 이름/금액이 반드시 일치되어야 입금확인이 가능합니다.</td>
										</tr>
										<tr><td height="10"></td></tr>
										<tr>
											<td height="1" bgcolor="#DDDDDD"></td>
										</tr>
									</table>
								</div>

								<!-- 결제대금예치제(에스크로) -->
								<div id="simg5" style="display:none;">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr><td height="10"></td></tr>
										<tr>
											<td height="24" style="padding-left:26px;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000">결제금액 :</font> <FONT COLOR="#EE1A02"><b><span class="view_order_last_price"><?=number_format($sumprice+$deli_price+$sumpricevat-$salemoney)?></span>원</b></font></td>
										</tr>
										<tr>
											<td style="color:#666666; padding-left:26px; font-size:11px; letter-spacing:-0.5px;">
												- 에스크로를 통해서 구매결정을 하실 수 있는 결제방식입니다.<br>
												- 주의! 1회용 계좌(가상계좌) 입금시, 이름/금액이 반드시 일치되어야 입금확인이 가능합니다.
											</td>
										</tr>
										<tr><td height="10"></td></tr>
										<tr>
											<td height="1" bgcolor="#DDDDDD"></td>
										</tr>
									</table>
								</div>

								<!-- 핸드폰 결제 -->
								<div id="simg6" style="display:none;">
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr><td height="10"></td></tr>
										<tr>
											<td height="24" style="padding-left:26px;"><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000">결제금액 :</font> <FONT COLOR="#EE1A02"><b><span class="view_order_last_price"><?=number_format($sumprice+$deli_price+$sumpricevat-$salemoney)?></span>원</b></font></td>
										</tr>
										<tr>
											<td style="color:#666666; padding-left:26px; font-size:11px; letter-spacing:-0.5px;">
												- 결제정보가 상점에 남지 않으며, 보안 적용된 결제창이 새로 뜹니다.<br>
												- 결제 후, 핸드폰 요금 청구서에 '(주)다날' 로 표시됩니다.
											</td>
										</tr>
										<tr><td height="10"></td></tr>
										<tr>
											<td height="1" bgcolor="#DDDDDD"></td>
										</tr>
									</table>
								</div>


								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="40"></td>
	</tr>
<!-- 결제수단 선택 END -->



<?if($ordertype  == "present"){?>
	<tr>
		<td style="padding-right:10px;">
		<!-- <TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" height="100%">
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimg06.gif" border="0"></TD>
		</TR>
		<TR>
			<TD height="100%" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgbg.gif"></TD>
		</TR>
		<TR>
			<TD><IMG SRC="<?=$Dir?>images/common/order/<?=$_data->design_order?>/design_order_leftimgdown.gif" border="0"></TD>
		</TR>
		</TABLE> -->
		</td>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td bgcolor="#E9E9E9" height="1"></td>
			</tr>
			<tr>
				<td style="padding-top:15px;padding-bottom:15px;">
				<div style="font-family:돋움; font-size:12px;color:#000000;font-weight:bold;">* 선물 받는 친구에게 메일을 보내세요.</div>
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="150"></col>
				<col></col>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>이메일</b></font></td>
					<td><input type=text name="receiver_email" value="" size="30" class="input" style="width:80%;BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>내용</b></font></td>
					<td><textarea name="receiver_message" rows="4" cols="70" style="WIDTH:100%;HEIGHT:70px;padding:5px;line-height:17px;border:solid 1;border-color:#DFDFDF;font-size:9pt;color:333333;"></textarea></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td bgcolor="#E9E9E9" height="1"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="40" colspan="2"></td>
	</tr>
<?}?>
<?if(substr($ordertype,0,6) == "pester"){?>
	<tr>
		<td style="padding-right:10px;"></td>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td bgcolor="#E9E9E9" height="3"></td>
			</tr>
			<tr>
				<td style="padding-top:15px;padding-bottom:15px;">
				<div style="font-family:돋움; font-size:12px;color:#000000;font-weight:bold;">* 조르기 요청 대상 정보 입력</div>
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="150"></col>
				<col></col>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>상대방 이름</b></font></td>
					<td><input type=text name="pester_name" size="15" maxlength="12" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>상대방 전화번호</b></font></td>
					<td><input type=text name="pester_tel1" value="" size="5" maxlength="3" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="pester_tel2" value="" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"> - <input type=text name="pester_tel3" value="" size="5" maxlength="4" onKeyUp="strnumkeyup(this)" class="input" style="BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>상대방 이메일</b></font></td>
					<td><input type=text name="pester_email" value="" size="30" class="input" style="width:80%;BACKGROUND-COLOR:#F7F7F7;"></td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>SMS 전송메세지</b></font></td>
					<td><textarea name="pester_smstxt" rows="4" cols="70" style="WIDTH:100%;HEIGHT:70px;padding:5px;line-height:17px;border:solid 1;border-color:#DFDFDF;font-size:9pt;color:333333;"></textarea></td>
				</tr>
				<tr>
					<td HEIGHT="10" colspan="2" background="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_line.gif"></td>
				</tr>
				<tr>
					<td><img src="<?=$Dir?>images/common/order/<?=$_data->design_order?>/order_skin_point.gif" border="0"><font color="#000000"><b>E-MAIL 전송메세지</b></font></td>
					<td><textarea name="pester_emailtxt" rows="4" cols="70" style="WIDTH:100%;HEIGHT:70px;padding:5px;line-height:17px;border:solid 1;border-color:#DFDFDF;font-size:9pt;color:333333;"></textarea></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td bgcolor="#E9E9E9" height="1"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="40" colspan="2"></td>
	</tr>
<?}?>
	</table>
	</td>
</tr>
</table>