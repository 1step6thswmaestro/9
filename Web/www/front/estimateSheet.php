<?
	// 견적서 출력시트


	$basketItems = getBasketByArray();


	/*
	echo "<div style=\" height:500px; overflow:scroll;  border:2px solid #ff0000 ;  text-align:left;\">";
	_pr($basketItems);
	echo "</div>";
	*/


	//사업자 번호
	$arcompa=array("-"," ",".","_",",");
	$arcomre=array("", "", "", "", "");
	$companynum=str_replace($arcompa,$arcomre,$_data->companynum);

	if(strlen($companynum)==13) {
		$companynum=substr($companynum,0,6)."-*******";
	} else {
		$companynum=substr($companynum,0,3)."-".substr($companynum,3,2)."-".substr($companynum,5);
	}

?>


		<table border=0 cellpadding=0 cellspacing=0 width=633 style='border-collapse:collapse;table-layout:fixed;width:478pt' align="center">
			<col width=36 style='mso-width-source:userset;mso-width-alt:1152;width:27pt'>
			<col width=58 style='mso-width-source:userset;mso-width-alt:1856;width:44pt'>
			<col width=90 style='mso-width-source:userset;mso-width-alt:2880;width:68pt'>
			<col width=82 style='mso-width-source:userset;mso-width-alt:2624;width:62pt'>
			<col width=13 style='mso-width-source:userset;mso-width-alt:416;width:10pt'>
			<col width=23 style='mso-width-source:userset;mso-width-alt:736;width:17pt'>
			<col width=66 style='mso-width-source:userset;mso-width-alt:2112;width:50pt'>
			<col width=33 style='mso-width-source:userset;mso-width-alt:1056;width:25pt'>
			<col width=15 style='mso-width-source:userset;mso-width-alt:480;width:11pt'>
			<col width=69 style='mso-width-source:userset;mso-width-alt:2208;width:52pt'>
			<col width=13 style='mso-width-source:userset;mso-width-alt:416;width:10pt'>
			<col width=61 style='mso-width-source:userset;mso-width-alt:1952;width:46pt'>
			<col width=29 style='mso-width-source:userset;mso-width-alt:928;width:22pt'>
			<col width=45 style='mso-width-source:userset;mso-width-alt:1440;width:34pt'>
			<tr height=61 style='mso-height-source:userset;height:45.75pt'>
				<td colspan=14 height=61 class=xl89 width=633 style='height:45.75pt;width:478pt'>견　　　적　　　서</td>
			</tr>
			<tr height=22 style='height:16.5pt'>
				<td height=22 class=xl65 style='height:16.5pt'></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
			</tr>
			<tr height=22 style='height:16.5pt'>
				<td height=22 class=xl65 style='height:16.5pt'></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black'>사업자등록번호</td>
				<td colspan=6 class=xl84 style='border-right:.5pt solid black;border-left:none'><?=$companynum?></td>
			</tr>
			<tr height=22 style='height:16.5pt'>
				<td height=22 class=xl65 style='height:16.5pt'></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black'>회사명</td>
				<td colspan=6 class=xl84 style='border-right:.5pt solid black;border-left:none'><?=$_data->shopname?></td>
			</tr>
			<tr height=22 style='height:16.5pt'>
				<td height=22 class=xl65 style='height:16.5pt'></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black'>대표자 성명</td>
				<td colspan=6 class=xl84 style='border-right:.5pt solid black;border-left:none'><?=$_data->companyowner?></td>
			</tr>
			<tr>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black;'>견적일자</td>
				<td colspan=2 class=xl83 style='border-right:.5pt solid black;border-left:none'><?=date("Y년m월d일 H시i분");?></td>
				<td class=xl66></td>
				<td class=xl66></td>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black'>업태 / 종목</td>
				<td colspan=6 class=xl84 style='border-right:.5pt solid black;border-left:none'><?=$_data->companybiz?> / <?=$_data->companyitem?></td>
			</tr>
			<tr>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black;'>유효기간</td>
				<td colspan=2 class=xl83 style='border-right:.5pt solid black;border-left:none'>견적 후 일주일</td>
				<td class=xl66></td>
				<td class=xl66></td>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black'>사업장 주소</td>
				<td colspan=6 class=xl84 style='border-right:.5pt solid black;border-left:none'><?=$_data->companyaddr?></td>
			</tr>
			<tr>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black'>사업장 전화번호</td>
				<td colspan=6 class=xl84 style='border-right:.5pt solid black;border-left:none'><?=$_data->info_tel?></td>
			</tr>
			<tr height=22 style='height:16.5pt'>
				<td height=22 class=xl65 style='height:16.5pt'></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black'>팩스번호</td>
				<td colspan=6 class=xl84 style='border-right:.5pt solid black;border-left:none'>
				</td>
			</tr>
			<tr>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black'>이메일주소</td>
				<td colspan=6 class=xl84 style='border-right:.5pt solid black;border-left:none'><?=$_data->privercyemail?></td>
			</tr>
			<tr>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td class=xl65></td>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black'>홈페이지 주소</td>
				<td colspan=6 class=xl84 style='border-right:.5pt solid black;border-left:none'>http://<?=$_ShopInfo->getShopurl()?></td>
			</tr>
			<tr height=22 style='height:16.5pt'>
				<td height=22 style='height:16.5pt'></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>


			<tr height=22 style='height:16.5pt'>
				<td height=22 class=xl65 colspan=7 style='height:16.5pt;'>※  아래와 같이 견적합니다.</td>
				<td colspan=2 class=xl65></td>
				<td colspan=2 class=xl87><!-- 견적합계 :<span style='mso-spacerun:yes'>&nbsp;</span> --></td>
				<td class=xl88><!-- \ 939,000 원 --></td>
				<td class=xl67><!-- , VAT포함 --></td>
			</tr>



			<tr height=22 style='height:16.5pt'>
				<td height=22 class=xl68 style='height:16.5pt'>No</td>
				<td colspan=4 class=xl79 style='border-right:.5pt solid black;border-left:none'>상품명</td>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black;border-left:none'>제조사</td>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black;border-left:none'>수량</td>
				<td class=xl69>상품단가</td>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black;border-left:none'>상품금액</td>
				<td colspan=2 class=xl83 style='border-right:.5pt solid black;border-left:none'>세액</td>
			</tr>
			<?
				$NO = 0;

				foreach($basketItems['vender'] as $vender=>$vendervalue){
					for( $i = 0 ; $i < count($vendervalue['products']) ; $i++ ){

						$product = $vendervalue['products'][$i];

						$NO++;

						//옵션 1
						$opt1 = "";
						if (_array($product['option1'])) {
							$tok = $product['option1'];
							$opt1 = "\n (".$tok[0]." : ".$tok[$product['opt1_idx']].")";
						}

						// 옵션 2
						$opt2 = "";
						if (_array($product['option2'])) {
							$tok = $product['option2'];
							$opt2 = "\n (".$tok[0]." : ".$tok[$product['opt2_idx']].")";
						}

						//브랜드
						$brand=mysql_fetch_object(mysql_query("SELECT brandname FROM tblproductbrand WHERE bridx='".$product['brand']."' LIMIT 1; ",get_db_conn()));


						// 세액
						$taxAllPrice = 0;
						if( $product['tax_yn'] == 0 ) $taxAllPrice = round( $product['realprice'] / 11 ) ; // 단일 세액
						$taxPriceTotal += $taxAllPrice;

						$noneTaxPrice = ( $product['realprice'] - $taxAllPrice ) / $product['quantity'];

			?>
			<tr>
				<td class=xl70><?=$NO?></td>
				<td colspan=4 class=xl79Prname style='border-right:.5pt solid black;border-left:none' title="<?=viewproductname($product['productname'],$product['etctype'],$product['selfcode'],$product['addcode'])?><?=$opt1?><?=$opt2?>"><?=viewproductname($product['productname'],$product['etctype'],$product['selfcode'],$product['addcode'])?><?=$opt1?><?=$opt2?></td>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black;border-left:none'><?=$brand->brandname?></td>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black;border-left:none'><?=$product['quantity']?>개</td>
				<td class=xl69><?=number_format($noneTaxPrice)?>원</td>
				<td colspan=2 class=xl79 style='border-right:.5pt solid black;border-left:none'><?=number_format($product['realprice'])?>원</td>
				<td colspan=2 class=xl83 style='border-right:.5pt solid black;border-left:none'><?=number_format($taxAllPrice)?>원</td>
			</tr>
			<?
					}// end for
				} // end foreach
			?>



			<tr height=3 style='mso-height-source:userset;height:2.25pt'>
				<td colspan=14 height=3 class=xl74 style='height:2.25pt'>　</td>
			</tr>



			<tr height=22 style='height:16.5pt'>
				<td colspan=10 height=22 class=xl75 style='border-right:.5pt solid black;
				height:16.5pt'>상품금액</td>
				<td colspan=4 class=xl75 style='border-right:.5pt solid black;border-left:none'><?=number_format($basketItems['sumprice']-$taxPriceTotal)?>원</td>
			</tr>


			<tr height=22 style='height:16.5pt'>
				<td colspan=10 height=22 class=xl75 style='border-right:.5pt solid black;
				height:16.5pt'>부가세(10%)</td>
				<td colspan=4 class=xl75 style='border-right:.5pt solid black;border-left:none'><?=number_format($taxPriceTotal)?>원</td>
			</tr>


			<tr height=22 style='height:16.5pt'>
				<td colspan=10 height=22 class=xl75 style='border-right:.5pt solid black;
				height:16.5pt'>합 계</td>
				<td colspan=4 class=xl75 style='border-right:.5pt solid black;border-left:none'><?=number_format($basketItems['sumprice'])?>원</td>
			</tr>



			<tr height=22 style='height:16.5pt'>
				<td colspan=14 height=22 class=xl71 style='border-right:.5pt solid black;height:16.5pt'>비고</td>
			</tr>
			<tr height=34 style='mso-height-source:userset;height:25.5pt'>
				<td colspan=14 height=34 class=xl71 style='border-right:.5pt solid black;height:25.5pt'>　</td>
			</tr>
		</table>