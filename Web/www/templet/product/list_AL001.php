<table cellpadding="0" cellspacing="0"  width="100%">
	<tr><td height="13"></td></tr>
	<tr>
		<td>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td style="padding-right:5px;"><?=$codenavi?></td>
					<td><A HREF="javascript:ClipCopy('http://<?=$_ShopInfo->getShopurl()?>?<?=getenv("QUERY_STRING")?>')"><img src="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/btn_addr_copy.gif" border="0"></A></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td height="25"></td></tr>

<?
	if($_data->ETCTYPE["CODEYES"]!="N") {

		$iscode=false;
		$citemsOnParent = false;
		$category_listitem = array();

		$citems = getCategoryItems($code,true);


		if(is_array($citems['items'])){
			if($citems['depth'] > 0 && count($citems['items']) <1){
				$chkLinkCode = $citems['pcode'];
				$citems = getCategoryItems(substr($code,0,($citems['depth']-1)*3),true);
				$citemsOnParent = true;
			}else if($citems['pcode'] == $code){
				$citems = getCategoryItems(substr($code,0,$citems['depth']*3),true);
			}
		}

		if(is_array($citems['items']) && count($citems['items']) > 0 ){
			$tcnt = 0;
			$codeitems = array(array('text'=>'전체','link'=>'/front/productlist.php?code='.$citems['pcode'],'cnt'=>0,'issel'=>true));

			foreach($citems['items'] as $cidx=>$citem){
				$sql = "select count(*) from tblcategorycode c left join tblproduct p on c.productcode=p.productcode where c.categorycode like '".$citem['linkcode']."%' and p.display='Y'";
				if(false !== $cntres = mysql_query($sql,get_db_conn())){
					$citemspcnt = mysql_result($cntres,0,0);
					$codeitems[0]['cnt']+=$citemspcnt;
					if(true === $issel = ($citemsOnParent && $chkLinkCode == $citem['linkcode'])){
						$codeitems[0]['issel'] = false;
					}
					array_push($codeitems,array('text'=>$citem['code_name'],'link'=>'/front/productlist.php?code='.$citem['linkcode'],'cnt'=>$citemspcnt,'issel'=>$issel));
				}
			}
			$citemloop = ceil((count($citems['items'])+1) / 5);

			$iscode = true;

			$category_list = '<table border="0" cellpadding="0" cellspacing="1" class="subCategoryBox"><caption>카테고리 그룹</caption>';

			for($ci=0;$ci<$citemloop;$ci++){
				$category_list .= '<tr>';
				for($j=0;$j<5;$j++){
					if(_array($codeitems[$ci*5+$j])){
						$citemobj = $codeitems[$ci*5+$j];
						$category_list .= '<td class="'.($citemobj['issel']?'selCategory':'').'">';
						if(!_empty($citemobj['link'])){
							$category_list .= '<a href="'.$citemobj['link'].'">'.$citemobj['text'].'<span class="citemproductcnt">('.number_format($citemobj['cnt']).')</span></a></td>';
						}else{
							$category_list .= $citemobj['text'].'<span class="citemproductcnt">('.number_format($citemobj['cnt']).')</span></td>';
						}
					}else{
						$category_list .= '<td>&nbsp;</td>';
					}
				}
				$category_list .= '</tr>';
			}

			$category_list .= '</table>';

		}

		/*
		$iscode=false;
		$category_listitem = array();
		$citems = getCategoryItems($code,true);
		if(is_array($citems['items'])){
			if($citems['depth'] > 0 && count($citems['items']) <1){
				$citems = getCategoryItems(substr($code,0,($citems['depth']-1)*3),true);
			}else if($citems['pcode'] == $code){
				$citems = getCategoryItems(substr($code,0,$citems['depth']*3),true);
			}
		}
		if(is_array($citems['items']) && count($citems['items']) > 0){
			foreach($citems['items'] as $citem){
				if( $citems['depth'] > 0 AND $citem['type'] != "LX" ) {
					if($codeA == $citem['codeA'] && $codeB == $citem['codeB'] && $codeC == $citem['codeC'] && $codeD == $citem['codeD']){
						array_push($category_listitem,"<a href=\"".$Dir.FrontDir."productlist.php?code=".$citem['codeA'].$citem['codeB'].$citem['codeC'].$citem['codeD']."\"><FONT class=subcodename style=\"font-weight:bold\">".$citem['code_name']."</FONT></a>");
					}else{
						array_push($category_listitem,"<a href=\"".$Dir.FrontDir."productlist.php?code=".$citem['codeA'].$citem['codeB'].$citem['codeC'].$citem['codeD']."\"><FONT class=subcodename>".$citem['code_name']."</FONT></a>");
					}
				}
			}

			$iscode = true;

			$category_list ="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
			$category_list .="<tr><td style=\"padding:10px;\" class=subcodename>";
			$category_list .= implode('&nbsp;|&nbsp;',$category_listitem);
			$category_list .='</td></tr>';
			$category_list .='</table>';
		}
		*/

		/*
		$sql =

			if($_cdata->type!="LX" && $_cdata->type!="TX") {	//하위분류가 있을 경우에만
				$sql = "SELECT COUNT(*) as cnt FROM tblproductcode ";
				$sql.= "WHERE codeA='".$codeA."' AND codeB!='000' AND codeC!='000' AND group_code!='NO' ";

				$result=mysql_query($sql,get_db_conn());
				$row=mysql_fetch_object($result);
				$cnt=$row->cnt;
				$iscode=true;
				mysql_free_result($result);

				$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
				$sql.= "WHERE codeA='".$codeA."' AND codeB!='000' AND codeC='000' AND codeD='000' AND group_code!='NO' ";
				$sql.= "AND (type='LM' || type='TM' || type='LMX' || type='TMX') ";
				$sql.= "ORDER BY sequence DESC ";
				$result=mysql_query($sql,get_db_conn());
				$category_list ="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";

				if($cnt>0) {
					$i=0;
					while($row=mysql_fetch_object($result)) {
						//if($i>0) $category_list.="<tr><td height=1 colspan=2 bgcolor=FFFFFF></td></tr>\n";
						$category_list.="<tr>";
						$category_list.="	<td width=\"25%\" bgcolor=\"#F3F8FF\" style=\"padding:10px;\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_iconaa.gif\" border=\"0\" align=\"absmiddle\" hspace=\"5\"><a href=\"".$Dir.FrontDir."productlist.php?code=".$row->codeA.$row->codeB.$row->codeC.$row->codeD."\"><FONT class=upcodename>".$row->code_name."</font></a></td>\n";
						$category_list.="	<td width=\"75%\" style=\"padding:10px;\" class=subcodename>";
						if(!eregi("X",$row->type)) {
							$sql = "SELECT codeA,codeB,codeC,codeD,code_name,type FROM tblproductcode ";
							$sql.= "WHERE codeA='".$row->codeA."' AND codeB='".$row->codeB."' AND codeC!='000' AND codeD='000' AND group_code!='NO' ";
							$sql.= "AND (type='LM' || type='TM' || type='LMX' || type='TMX') ";
							$sql.= "ORDER BY sequence DESC ";
							$result2=mysql_query($sql,get_db_conn());
							$j=0;
							while($row2=mysql_fetch_object($result2)) {
								if($j>0) $category_list.=" | ";
								$category_list.="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row2->codeA.$row2->codeB.$row2->codeC.$row2->codeD."\"><FONT class=subcodename>".$row2->code_name."</font></a>";
								$j++;
							}
							mysql_free_result($result2);
						}

						$category_list.="	</td>\n";
						$category_list.="</tr>\n";
						$i++;
					}
				} else {
					$category_list.="<tr>";
					$category_list.="	<td style=\"padding:10px;\" class=subcodename>";
					$i=0;
					while($row=mysql_fetch_object($result)) {
						if($i>0) $category_list.=" | ";
						$category_list.="<a href=\"".$Dir.FrontDir."productlist.php?code=".$row->codeA.$row->codeB.$row->codeC.$row->codeD."\"><FONT class=subcodename>".$row->code_name."</FONT></a>";
						$i++;
					}
					$category_list.="	</td>\n";
					$category_list.="</tr>\n";
				}
				$category_list.="</table>\n";
				mysql_free_result($result);
			}
		*/
?>
<? if($iscode==true){ ?>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="cateName"><?=$_cdata->code_name?></td>
				</tr>
				<? if($category_list){ ?>
				<tr>
					<td><?=$category_list?></td>
				</tr>
				<? } ?>
			</table>
		</td>
	</tr>
<?
	}
}
?>

<!-- 신규/인기/추천 시작 -->
<?
$special_show_cnt=0;
$special_show_list ="<tr><td height=\"15\"></td></tr>";
$special_show_list.="<tr>\n";
$special_show_list.="	<td>\n";
$special_show_list.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";

$arrspecialcnt=explode(",",$_cdata->special_cnt);
for ($i=0;$i<count($arrspecialcnt);$i++) {
	if (substr($arrspecialcnt[$i],0,2)=="1:") {
		$tmpsp1=substr($arrspecialcnt[$i],2);
	} else if (substr($arrspecialcnt[$i],0,2)=="2:") {
		$tmpsp2=substr($arrspecialcnt[$i],2);
	} else if (substr($arrspecialcnt[$i],0,2)=="3:") {
		$tmpsp3=substr($arrspecialcnt[$i],2);
	}
}
if(strlen($tmpsp1)>0) {
	$special_1=explode("X",$tmpsp1);
	$special_1_cols=(int)$special_1[0];
	$special_1_rows=(int)$special_1[1];
	$special_1_type=$special_1[2];
}
if(strlen($tmpsp2)>0) {
	$special_2=explode("X",$tmpsp2);
	$special_2_cols=(int)$special_2[0];
	$special_2_rows=(int)$special_2[1];
	$special_2_type=$special_2[2];
}
if(strlen($tmpsp3)>0) {
	$special_3=explode("X",$tmpsp3);
	$special_3_cols=(int)$special_3[0];
	$special_3_rows=(int)$special_3[1];
	$special_3_type=$special_3[2];
}

$plist0_tag_0_count = 2; //전체상품 태그 출력 갯수

$plist1_tag_1_count = 2; //신규상품 태그 출력 갯수(이미지A형)
$plist2_tag_1_count = 5; //신규상품 태그 출력 갯수(리스트형)
$plist3_tag_1_count = 2; //신규상품 태그 출력 갯수(이미지B형)

$plist1_tag_2_count = 2; //인기상품 태그 출력 갯수(이미지A형)
$plist2_tag_2_count = 5; //인기상품 태그 출력 갯수(리스트형)
$plist3_tag_2_count = 2; //인기상품 태그 출력 갯수(이미지B형)

$plist1_tag_3_count = 2; //추천상품 태그 출력 갯수(이미지A형)
$plist2_tag_3_count = 5; //추천상품 태그 출력 갯수(리스트형)
$plist3_tag_3_count = 2; //추천상품 태그 출력 갯수(이미지B형)

//신규
$special_1_num=$special_1_cols*$special_1_rows;
if(eregi("1",$_cdata->special)) {
	$sql = "SELECT special_list FROM tblspecialcode ";
	$sql.= "WHERE code='".$code."' AND special='1' ";
	$result=mysql_query($sql,get_db_conn());
	$sp_prcode="";
	$sp_list="";
	if($row=mysql_fetch_object($result)) {
		$sp_prcode=ereg_replace(',','\',\'',$row->special_list);
	}
	mysql_free_result($result);

	if(strlen($sp_prcode)>0) {

		$sql = productQuery ();
		$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if(strlen($not_qry)>0) {
			$sql.= $not_qry." ";
		}
		$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
		$sql.= "LIMIT ".$special_1_num;
		$result=mysql_query($sql,get_db_conn());
		$i=0;
		//$special_1_type => I:이미지A형, D:이미지B형, L:리스트형
		if($special_1_type == "I") {
			$sp_list.= "<table cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n";
			$table_width=ceil(100/$special_1_cols);
			for($j=1;$j<=$special_1_cols;$j++) {
				if($j>1)
					$sp_list.="<col width=10></col>\n";
					$sp_list.="<col width=></col>\n";
					//$sp_list.="<col width=".$table_width."%></col>\n";
			}
			$sp_list.= "<tr><td height=\"15\"></td></tr>\n";
			$sp_list.= "<tr>\n";
			while($row=mysql_fetch_object($result)) {

				// 예약상품 아이콘 추가
				$row->etctype = reservationEtcType($row->reservation,$row->etctype);

				// 도매 가격 적용 상품 아이콘
				$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";

				// 할인율 표시
				$discountRate = ( $row->discountRate > 0 ) ? "<strong>".$row->discountRate."</strong>%↓" : "";

				$memberpriceValue = $row->sellprice;
				$strikeStart = $strikeEnd = '';
				$memberprice = 0;
				if($row->discountprices>0 AND isSeller() != 'Y' ){
					$memberprice = number_format($row->sellprice - $row->discountprices);
					$strikeStart = "<strike>";
					$strikeEnd = "</strike>";
					$memberpriceValue = ($row->sellprice - $row->discountprices);
				}

				$tableSize = $_data->primg_minisize + 12;
				if ($i!=0 && $i%$special_1_cols==0) {
					$sp_list.= "</tr><tr><td colspan=\"9\" height=\"10\"></td></tr>\n";
				}
				if ($i!=0 && $i%$special_1_cols!=0) {
					$sp_list.= "<td width=\"10\" nowrap></td>";
				}

				$sp_list.= "<td align=\"center\" valign=\"top\">\n";
				$sp_list.= "<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"".$tableSize."\" border=\"0\" id=\"N".$row->productcode."\" onmouseover=\"quickfun_show(this,'N".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'N".$row->productcode."','none')\" class=\"prInfoBox\">\n";

				$sp_list.= "<TR>\n";
				$sp_list.= "<TD align=\"center\" height=\"120\" style=\"padding:5px;\">";
				$sp_list.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>";
				$sp_list.= "</tr>\n";

				$sp_list.= "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','N','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";

				$sp_list.= "<tr>";
				$sp_list.= "	<TD style=\"padding:5px 7px; word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br /><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</A></td>\n";
				$sp_list.= "</tr>\n";

				//시중가 + 판매가 + 할인율 + 회원할인가
				$sp_list.= "<tr>
									<td style=\"padding:0px 7px 7px 7px; word-break:break-all;\">
										<table border=0 cellpadding=0 cellspacing=0 width=100%>
											<tr>
												<td>
				";
				if($row->consumerprice!=0) {
					//$sp_list.= "<tr>\n";
					//$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
					$sp_list.= "<span class=\"prconsumerprice\" style=\"padding-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</span>\n";
					//$sp_list.= "</tr>\n";
				}

				// 회원 할인가가 있을 때 가격 class 변경
				if($memberprice > 0){
					$mainprpriceClass = "";
				}else{
					$mainprpriceClass = "mainprprice";
				}

				//$sp_list.= "<tr>\n";
				//$sp_list.= "	<TD align=\"center\" style=\"word-break:break-all;\" class=\"prprice\">";

				$sp_list.="<span style=\"white-space:nowrap;\">";
				$sp_list.= $strikeStart;
				if($dicker=dickerview($row->etctype,$wholeSaleIcon.number_format($row->sellprice)."원",1)) {
					$sp_list.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<strong class=\"".$mainprpriceClass."\">".$wholeSaleIcon.number_format($row->sellprice)."원</strong>";
					//if (strlen($row->option_price)!=0) $sp_list.= "(기본가)";
				} else {
					//$sp_list.="<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0){
						$sp_list.= $wholeSaleIcon.number_format($row->sellprice)."원";
					}else{
						$sp_list.= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
					}
				}
				$sp_list.= $strikeEnd;
				$sp_list.="
								</span>
							</td>
				";
				if($row->discountRate > 0){
					$sp_list.="<td align=\"right\" valign=\"bottom\" class=\"discount\">".$discountRate."</td>";
				}
				$sp_list.="
						</tr>
					</table>
				";

				if ($row->quantity=="0") $sp_list.= soldout();
				//$sp_list.= "	</td>\n";
				//$sp_list.= "</tr>\n";

				//회원할인가 적용
				if( $memberprice > 0 ) {
					//$sp_list.="<tr>\n";
					//$sp_list.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainprprice\"><img src=\"".$Dir."images/common/memsale_icon.gif\" style=\"position:relative; top:0.1em;\" alt=\"\" />".dickerview($row->etctype,$memberprice."원")."</td>\n";
					$sp_list.="	<div><span class=\"mainprprice\">".dickerview($row->etctype,$memberprice."원")."</span> <img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" /></div>\n";
					//$sp_list.="</tr>\n";
				}

				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y");
				if($reserveconv>0) {
					//$sp_list.= "<tr>\n";
					//$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\" align=\"absmiddle\" alt=\"\" />".number_format($reserveconv)."원</td>\n";
					$sp_list.= "	<div style=\"margin-top:5px;\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\" align=\"absmiddle\" alt=\"\" /><span class=\"prreserve\">".number_format($reserveconv)."</span>원</div>";
					//$sp_list.= "</tr>\n";
				}

				$sp_list.="	</td>\n";
				$sp_list.="</tr>\n";

				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist1_tag_1_count;$ii++) {
						$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<tr>\n";
								$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\">\n";
								$sp_list.= "	<img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
					if($jj!=0) {
						$sp_list.= "	</td>\n";
						$sp_list.= "</tr>\n";
					}
				}

				// 입점사 네임택
				if( nameTechUse($row->vender) ) {
					$classList = array();
					$classResult=mysql_query("SELECT * FROM `tblVenderClassType` ",get_db_conn());
					while($classRow=mysql_fetch_object($classResult)) {
						$classList[$classRow->idx] = $classRow->name;
					}
					$v_info = mysql_fetch_assoc ( mysql_query( "SELECT * FROM `tblvenderinfo` WHERE `vender`=".$row->vender." LIMIT 1;" ,get_db_conn()) );

					$venderNameTag = "<div style=\"float:left; width:60px;\"><img src=\"".$com_image_url.$v_info['com_image']."\" onerror=\"this.src='/images/no_img.gif';\" width=\"48\" style=\"border:1px solid #dddddd;\" /></div>";
					$venderNameTag .= "<div style=\"float:left; width:65%; font-size:11px;\">";
					$venderNameTag .= "	<span class=\"name\">".$v_info['com_name']."</span> <span class=\"owner\">(".$v_info['com_owner'].")</span><br />";
					$venderNameTag .= "	<a href=\"javascript:GoMinishop('/minishop.php?storeid=".$v_info['id']."')\"><img src=\"/images/common/icon_vender_go.gif\" border=\"0\" alt=\"전체상품보기\" /></a>";
					$venderNameTag .= "</div>";

					// 네임텍 출력
					$sp_list.="
						<tr>
							<td class=\"nameTagBox\">".$venderNameTag."</td>
						</tr>
					";
				}

				$sp_list.= "</table>\n";
				$sp_list.= "</td>";
				$i++;

				if ($i==$special_1_num) break;
				if ($i%$special_1_cols==0) {
					$sp_list.= "</tr><tr><td colspan=\"".($special_1_cols*2-1)."\" height=\"5\"></td><tr>\n";
				}
			}
			if($i>0 && $i<$special_1_cols) {
				for($k=0; $k<($special_1_cols-$i); $k++) {
					$sp_list.="<td width=\"10\" nowrap></td><td width=\"".$tableSize."\"></td>\n";
				}
			}

		} else if($special_1_type == "L") {
			$colspan="6";
			$sp_list.= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$sp_list.= "<col width=\"15%\"></col>\n";
			$sp_list.= "<col width=\"0\"></col>\n";
			$sp_list.= "<col width=\"50%\"></col>\n";
			$sp_list.= "<col width=\"12%\"></col>\n";
			$sp_list.= "<col width=\"12%\"></col>\n";
			$sp_list.= "<col width=\"11%\"></col>\n";
			$sp_list.= "<tr height=\"30\" align=\"center\" bgcolor=\"#F8F8F8\">\n";
			$sp_list.= "	<td colspan=\"2\"><b><font color=\"#000000\">제품사진</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">제품명</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">시중가격</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">판매가격</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">적립금</font></b></td>\n";
			$sp_list.= "</tr>\n";
			$sp_list.= "<tr>\n";
			$sp_list.= "	<td height=\"1\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\" colspan=\"".$colspan."\"></td>";
			$sp_list.= "</tr>\n";
			while($row=mysql_fetch_object($result)) {

				// 예약상품 아이콘 추가
				$row->etctype = reservationEtcType($row->reservation,$row->etctype);

				// 도매 가격 적용 상품 아이콘
				$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";

				// 할인율 표시
				$discountRate = ( $row->discountRate > 0 ) ? "(".$row->discountRate."%)" : "";

				$memberpriceValue = $row->sellprice;
				$strikeStart = $strikeEnd = '';
				$memberprice = 0;
				if($row->discountprices>0 AND isSeller() != 'Y' ){
					$memberprice = number_format($row->sellprice - $row->discountprices);
					$strikeStart = "<strike>";
					$strikeEnd = "</strike>";
					$memberpriceValue = ($row->sellprice - $row->discountprices);
				}


				$sp_list.= "<tr align=\"center\" id=\"N".$row->productcode."\" onmouseover=\"quickfun_show(this,'N".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'N".$row->productcode."','none')\">\n";
				$sp_list.= "	<td style=\"padding-top:1px;padding-bottom:1px;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>\n";
				$sp_list.="		<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','N','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$sp_list.= "	<td style=\"padding-left:5px;padding-right:5px;word-break:break-all;\" align=\"left\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span style="color:#bcbcbc; font-size:11px; font-family:"돋움">'.$row->prmsg.'</span>':'')."</A>";
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist2_tag_1_count;$ii++) {
						$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<br><br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
				}
				$sp_list.= "	</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prprice\">";

				$sp_list.= $strikeStart;

				if($dicker=dickerview($row->etctype,$wholeSaleIcon.number_format($row->sellprice)."원",1)) {
					$sp_list.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">".$wholeSaleIcon.number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $sp_list.= "(기본가)";
				} else {
					$sp_list.="<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) $sp_list.= $wholeSaleIcon.number_format($row->sellprice)."원";
					else $sp_list.= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
				}

				$sp_list.= $strikeEnd;

				$sp_list.= $discountRate;

				if ($row->quantity=="0") $sp_list.= soldout();

				//회원할인가 적용
				if( $memberprice > 0 ) {
					$sp_list.= "<br /><img src=\"".$Dir."images/common/memsale_icon.gif\" style=\"position:relative; top:0.1em;\" alt=\"\" />".dickerview($row->etctype,$memberprice."원");
				}

				$sp_list.= "	</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\" align=\"absmiddle\" alt=\"\" />".number_format(getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y"))."원</td>\n";
				$sp_list.= "</tr>\n";
				$sp_list.= "<tr>\n";
				$sp_list.= "	<td height=\"1\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\" colspan=\"".$colspan."\"></td>";
				$sp_list.= "</tr>\n";
				$i++;
			}
		} else if($special_1_type == "D") {
			$sp_list.= "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$sp_list.= "<tr>\n";
			$sp_list.= "	<td height=\"5\"></td>\n";
			$sp_list.= "</tr>\n";
			$sp_list.= "<tr>\n";
			while($row=mysql_fetch_object($result)) {

				// 예약상품 아이콘 추가
				$row->etctype = reservationEtcType($row->reservation,$row->etctype);

				// 도매 가격 적용 상품 아이콘
				$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";


				// 할인율 표시
				$discountRate = ( $row->discountRate > 0 ) ? "(".$row->discountRate."%)" : "";

				$memberpriceValue = $row->sellprice;
				$strikeStart = $strikeEnd = '';
				$memberprice = 0;
				if($row->discountprices>0 AND isSeller() != 'Y' ){
					$memberprice = number_format($row->sellprice - $row->discountprices);
					$strikeStart = "<strike>";
					$strikeEnd = "</strike>";
					$memberpriceValue = ($row->sellprice - $row->discountprices);
				}


				if ($i!=0 && $i%$special_1_cols!=0) {
					$sp_list.= "<td align=\"center\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_lineb.gif\" border=\"0\"></td>\n";
				}
				$sp_list.= "<td width=\"".(100/$special_1_cols)."%\">\n";
				$sp_list.= "<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" id=\"N".$row->productcode."\" onmouseover=\"quickfun_show(this,'N".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'N".$row->productcode."','none')\">\n";
				$sp_list.= "<col width=\"100\"></col>\n";
				$sp_list.= "<col width=\"0\"></col>\n";
				$sp_list.= "<col width=\"100%\"></col>\n";
				$sp_list.= "<TR>\n";
				$sp_list.= "	<TD align=\"center\" style=\"padding-top:1px;padding-bottom:1px;\" nowrap>";
				$sp_list.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>";
				$sp_list.="		<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','N','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$sp_list.= "	<TD style=\"padding-left:5px;padding-right:5px;word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span style="color:#bcbcbc; font-size:11px; font-family:"돋움">'.$row->prmsg.'</span>':'')."</A>\n";

				$sp_list.= $strikeStart;

				if($row->consumerprice!=0) {
					$sp_list.= "<br><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><FONT class=\"prconsumerprice\"><strike>".number_format($row->consumerprice)."</strike>원</font>\n";
				}
				if($dicker=dickerview($row->etctype,$wholeSaleIcon.number_format($row->sellprice)."원",1)) {
					$sp_list.= "<br><font class=\"prprice\">".$dicker."</font>";
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<br><font class=\"prprice\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".$wholeSaleIcon.number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $sp_list.= "(기본가)";
					$sp_list.= "</font>";
				} else {
					$sp_list.="<br><font class=\"prprice\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) $sp_list.= $wholeSaleIcon.number_format($row->sellprice)."원";
					else $sp_list.= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
					$sp_list.= "</font>";
				}

				$sp_list.= $strikeEnd;

				$sp_list.= $discountRate;

				if ($row->quantity=="0") $sp_list.= soldout();


				//회원할인가 적용
				if( $memberprice > 0 ) {
					$sp_list.="<br />▼<br /><img src=\"".$Dir."images/common/memsale_icon.gif\" style=\"position:relative; top:0.1em;\" alt=\"\" />".dickerview($row->etctype,$memberprice."원");
				}

				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y");
				if($reserveconv>0) {
					$sp_list.= "<br><font class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\" align=\"absmiddle\" alt=\"\" />".number_format($reserveconv)."원</font>\n";
				}
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist3_tag_1_count;$ii++) {
						$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
				}
				$sp_list.= "	</td>\n";
				$sp_list.= "</tr>\n";
				$sp_list.= "</table>\n";
				$sp_list.= "</td>\n";
				$i++;
				if ($i%$special_1_cols==0) {
					$sp_list.= "</tr><tr><td height=\"1\" colspan=\"".($special_1_cols*2-1)."\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\"></td></tr><tr>\n";
				}
				if ($i==$special_1_num) break;
			}
			if($i>0 && $i<$special_1_cols) {
				for($k=0; $k<($special_1_cols-$i); $k++) {
					$sp_list.="<td></td>\n<td width=\"".(100/$special_1_cols)."%\"></td>\n";
				}
			}
			if ($i!=0 && $i%$special_1_cols) {
				$sp_list.= "</tr><tr><td height=\"1\" colspan=\"".($special_1_cols*2-1)."\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\"></td>\n";
			}
		}
		mysql_free_result($result);
		$sp_list.= "</tr>\n";
		$sp_list.= "</table>\n";

		if($i>0) {
			if($special_show_cnt) {
				$special_show_list.="</tr><td height=\"20\"></td></tr>\n";
			}
			$special_show_list.="<tr>\n";
			$special_show_list.="	<td>\n";
			$special_show_list.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
			$special_show_list.="	<tr>\n";
			$special_show_list.="		<td background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_titlebg3.gif\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_newtitle.gif\" border=\"0\"></td>\n";
			$special_show_list.="	</tr>\n";
			$special_show_list.="	</table>\n";
			$special_show_list.="	</td>\n";
			$special_show_list.="</tr>\n";
			$special_show_list.="<tr>\n";
			$special_show_list.="	<td>\n";
			$special_show_list.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
			$special_show_list.="	<tr>\n";
			$special_show_list.="		<td>\n";
			$special_show_list.="		".$sp_list."\n";
			$special_show_list.="		</td>\n";
			$special_show_list.="	</tr>\n";
			$special_show_list.="	</table>\n";
			$special_show_list.="	</td>\n";
			$special_show_list.="</tr>\n";
			$special_show_cnt++;
		}
	}
}

//인기
$special_2_num=$special_2_cols*$special_2_rows;
if(eregi("2",$_cdata->special)) {
	$sql = "SELECT special_list FROM tblspecialcode ";
	$sql.= "WHERE code='".$code."' AND special='2' ";
	$result=mysql_query($sql,get_db_conn());
	$sp_prcode="";
	$sp_list="";
	if($row=mysql_fetch_object($result)) {
		$sp_prcode=ereg_replace(',','\',\'',$row->special_list);
	}
	mysql_free_result($result);

	if(strlen($sp_prcode)>0) {

		$sql = productQuery ();
		$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if(strlen($not_qry)>0) {
			$sql.= $not_qry." ";
		}
		$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
		$sql.= "LIMIT ".$special_2_num;
		$result=mysql_query($sql,get_db_conn());
		$i=0;
		//$special_2_type => I:이미지A형, D:이미지B형, L:리스트형
		if($special_2_type == "I") {
			$sp_list.= "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n";
			$table_width=ceil(100/$special_2_cols);
			for($j=1;$j<=$special_2_cols;$j++) {
				if($j>1)
					$sp_list.="<col width=10></col>\n";
					$sp_list.="<col width=></col>\n";
				//$sp_list.="<col width=".$table_width."%></col>\n";
			}
			$sp_list.= "<tr><td height=\"15\"></td></tr>\n";
			$sp_list.= "<tr>\n";
			while($row=mysql_fetch_object($result)) {

				// 예약상품 아이콘 추가
				$row->etctype = reservationEtcType($row->reservation,$row->etctype);

				// 도매 가격 적용 상품 아이콘
				$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";

				// 할인율 표시
				$discountRate = ( $row->discountRate > 0 ) ? "<strong>".$row->discountRate."</strong>%↓" : "";

				$memberpriceValue = $row->sellprice;
				$strikeStart = $strikeEnd = '';
				$memberprice = 0;
				if($row->discountprices>0 AND isSeller() != 'Y' ){
					$memberprice = number_format($row->sellprice - $row->discountprices);
					$strikeStart = "<strike>";
					$strikeEnd = "</strike>";
					$memberpriceValue = ($row->sellprice - $row->discountprices);
				}

				$tableSize = $_data->primg_minisize + 12;
				if ($i!=0 && $i%$special_2_cols==0) {
					$sp_list.= "</tr><tr><td colspan=\"9\" height=\"10\"></td></tr>\n";
				}
				if ($i!=0 && $i%$special_2_cols!=0) {
					$sp_list.= "<td width=\"10\" nowrap></td>";
				}

				$sp_list.= "<td align=\"center\" valign=\"top\">\n";
				$sp_list.= "<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"".$tableSize."\" border=\"0\" id=\"B".$row->productcode."\" onmouseover=\"quickfun_show(this,'B".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'B".$row->productcode."','none')\" class=\"prInfoBox\">\n";

				$sp_list.= "<TR>\n";
				$sp_list.= "	<TD align=\"center\" height=\"120\" style=\"padding:5px;\">";
				$sp_list.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>";
				$sp_list.= "</tr>\n";

				$sp_list.= "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','B','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";

				$sp_list.= "<tr>";
				$sp_list.= "	<TD style=\"padding:5px 7px; word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span style="color:#bcbcbc; font-size:11px; font-family:"돋움">'.$row->prmsg.'</span>':'')."</A></td>\n";
				$sp_list.= "</tr>\n";

				//시중가 + 판매가 + 할인율 + 회원할인가
				$sp_list.= "<tr>
									<td style=\"padding:0px 7px 7px 7px; word-break:break-all;\">
										<table border=0 cellpadding=0 cellspacing=0 width=100%>
											<tr>
												<td>
				";
				if($row->consumerprice!=0) {
					//$sp_list.= "<tr>\n";
					//$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
					$sp_list.= "	<span class=\"prconsumerprice\" style=\"padding-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</span>\n";
					//$sp_list.= "</tr>\n";
				}

				// 회원 할인가가 있을 때 가격 class 변경
				if($memberprice > 0){
					$mainprpriceClass = "";
				}else{
					$mainprpriceClass = "mainprprice";
				}

				//$sp_list.= "<tr>\n";
				//$sp_list.= "	<TD align=\"center\" style=\"word-break:break-all;\" class=\"prprice\">";

				$sp_list.="<span style=\"white-space:nowrap;\">";
				$sp_list.= $strikeStart;
				if($dicker=dickerview($row->etctype,$wholeSaleIcon.number_format($row->sellprice)."원",1)) {
					$sp_list.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<strong class=\"".$mainprpriceClass."\">".$wholeSaleIcon.number_format($row->sellprice)."원</strong>";
					//if (strlen($row->option_price)!=0) $sp_list.= "(기본가)";
				} else {
					//$sp_list.="<img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0){
						$sp_list.= $wholeSaleIcon.number_format($row->sellprice)."원";
					}else{
						$sp_list.= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
					}
				}
				$sp_list.= $strikeEnd;
				$sp_list.="
								</span>
							</td>
				";
				if($row->discountRate > 0){
					$sp_list.="<td align=\"right\" valign=\"bottom\" class=\"discount\">".$discountRate."</td>";
				}
				$sp_list.="
						</tr>
					</table>
				";

				if ($row->quantity=="0") $sp_list.= soldout();
				//$sp_list.= "	</td>\n";
				//$sp_list.= "</tr>\n";


				//회원할인가 적용
				if( $memberprice > 0 ) {
					//$sp_list.="<tr>\n";
					//$sp_list.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainprprice\"><img src=\"".$Dir."images/common/memsale_icon.gif\" style=\"position:relative; top:0.1em;\" alt=\"\" />".dickerview($row->etctype,$memberprice."원")."</td>\n";
					$sp_list.="	<div><span class=\"mainprprice\">".dickerview($row->etctype,$memberprice."원")."</span> <img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" /></div>\n";
					//$sp_list.="</tr>\n";
				}

				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y");
				if($reserveconv>0) {
					$sp_list.= "	<p style=\"margin-top:5px;\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"position:relative;top:2px;\" align=\"absmiddle\" alt=\"\" /><span class=\"prreserve\">".number_format($reserveconv)."원</span></p>";
				}

				$sp_list.="	</td>\n";
				$sp_list.="</tr>\n";

				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist1_tag_2_count;$ii++) {
						$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<tr>\n";
								$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\">\n";
								$sp_list.= "	<img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
					if($jj!=0) {
						$sp_list.= "	</td>\n";
						$sp_list.= "</tr>\n";
					}
				}

				// 입점사 네임택
				if( nameTechUse($row->vender) ) {
					$classList = array();
					$classResult=mysql_query("SELECT * FROM `tblVenderClassType` ",get_db_conn());
					while($classRow=mysql_fetch_object($classResult)) {
						$classList[$classRow->idx] = $classRow->name;
					}
					$v_info = mysql_fetch_assoc ( mysql_query( "SELECT * FROM `tblvenderinfo` WHERE `vender`=".$row->vender." LIMIT 1;" ,get_db_conn()) );

					$venderNameTag = "<div style=\"float:left; width:60px;\"><img src=\"".$com_image_url.$v_info['com_image']."\" onerror=\"this.src='/images/no_img.gif';\" width=\"48\" style=\"border:1px solid #dddddd;\" /></div>";
					$venderNameTag .= "<div style=\"float:left; width:65%; font-size:11px;\">";
					$venderNameTag .= "	<span class=\"name\">".$v_info['com_name']."</span> <span class=\"owner\">(".$v_info['com_owner'].")</span><br />";
					$venderNameTag .= "	<a href=\"javascript:GoMinishop('/minishop.php?storeid=".$v_info['id']."')\"><img src=\"/images/common/icon_vender_go.gif\" border=\"0\" alt=\"전체상품보기\" /></a>";
					$venderNameTag .= "</div>";

					// 네임텍 출력
					$sp_list.="
						<tr>
							<td class=\"nameTagBox\">".$venderNameTag."</td>
						</tr>
					";
				}

				$sp_list.= "</table>\n";
				$sp_list.= "</td>";
				$i++;

				if ($i==$special_2_num) break;
				if ($i%$special_2_cols==0) {
					$sp_list.= "</tr><tr><td colspan=\"".($special_2_cols*2-1)."\" height=\"5\"></td><tr>\n";
				}
			}
			if($i>0 && $i<$special_2_cols) {
				for($k=0; $k<($special_2_cols-$i); $k++) {
					$sp_list.="<td width=\"10\" nowrap></td><td width=\"".$tableSize."\"></td>\n";
				}
			}
		} else if($special_2_type == "L") {
			$colspan="6";
			$sp_list.= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$sp_list.= "<col width=\"15%\"></col>\n";
			$sp_list.= "<col width=\"0\"></col>\n";
			$sp_list.= "<col width=\"50%\"></col>\n";
			$sp_list.= "<col width=\"12%\"></col>\n";
			$sp_list.= "<col width=\"12%\"></col>\n";
			$sp_list.= "<col width=\"11%\"></col>\n";
			$sp_list.= "<tr height=\"30\" align=\"center\" bgcolor=\"#F8F8F8\">\n";
			$sp_list.= "	<td colspan=\"2\"><b><font color=\"#000000\">제품사진</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">제품명</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">시중가격</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">판매가격</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">적립금</font></b></td>\n";
			$sp_list.= "</tr>\n";
			$sp_list.= "<tr>\n";
			$sp_list.= "	<td height=\"1\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\" colspan=\"".$colspan."\"></td>";
			$sp_list.= "</tr>\n";
			while($row=mysql_fetch_object($result)) {

				// 예약상품 아이콘 추가
				$row->etctype = reservationEtcType($row->reservation,$row->etctype);

				// 도매 가격 적용 상품 아이콘
				$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";

				// 할인율 표시
				$discountRate = ( $row->discountRate > 0 ) ? "(".$row->discountRate."%)" : "";

				$memberpriceValue = $row->sellprice;
				$strikeStart = $strikeEnd = '';
				$memberprice = 0;
				if($row->discountprices>0 AND isSeller() != 'Y' ){
					$memberprice = number_format($row->sellprice - $row->discountprices);
					$strikeStart = "<strike>";
					$strikeEnd = "</strike>";
					$memberpriceValue = ($row->sellprice - $row->discountprices);
				}

				$sp_list.= "<tr align=\"center\" id=\"B".$row->productcode."\" onmouseover=\"quickfun_show(this,'B".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'B".$row->productcode."','none')\">\n";
				$sp_list.= "	<td style=\"padding-top:1px;padding-bottom:1px;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>\n";
				$sp_list.="		<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','B','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$sp_list.= "	<td style=\"padding-left:5px;padding-right:5px;word-break:break-all;\" align=\"left\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span style="color:#bcbcbc; font-size:11px; font-family:"돋움">'.$row->prmsg.'</span>':'')."</A>";
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist2_tag_2_count;$ii++) {
						$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<br><br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
				}
				$sp_list.= "	</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prprice\">";

				$sp_list.= $strikeStart;

				if($dicker=dickerview($row->etctype,$wholeSaleIcon.number_format($row->sellprice)."원",1)) {
					$sp_list.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">".$wholeSaleIcon.number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $sp_list.= "(기본가)";
				} else {
					$sp_list.="<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) $sp_list.= $wholeSaleIcon.number_format($row->sellprice)."원";
					else $sp_list.= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
				}
				$sp_list.= $strikeEnd;

				$sp_list.= $discountRate;

				//회원할인가 적용
				if( $memberprice > 0 ) {
					$sp_list.= "<br /><img src=\"".$Dir."images/common/memsale_icon.gif\" style=\"position:relative; top:0.1em;\" alt=\"\" />".dickerview($row->etctype,$memberprice."원");
				}

				if ($row->quantity=="0") $sp_list.= soldout();
				$sp_list.= "	</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\" align=\"absmiddle\" alt=\"\" />".number_format(getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y"))."원</td>\n";
				$sp_list.= "</tr>\n";
				$sp_list.= "<tr>\n";
				$sp_list.= "	<td height=\"1\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\" colspan=\"".$colspan."\"></td>";
				$sp_list.= "</tr>\n";
				$i++;
			}
		} else if($special_2_type == "D") {
			$sp_list.= "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$sp_list.= "<tr>\n";
			$sp_list.= "	<td height=\"5\"></td>\n";
			$sp_list.= "</tr>\n";
			$sp_list.= "<tr>\n";
			while($row=mysql_fetch_object($result)) {

				// 예약상품 아이콘 추가
				$row->etctype = reservationEtcType($row->reservation,$row->etctype);

				// 도매 가격 적용 상품 아이콘
				$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";

				// 할인율 표시
				$discountRate = ( $row->discountRate > 0 ) ? "(".$row->discountRate."%)" : "";

				$memberpriceValue = $row->sellprice;
				$strikeStart = $strikeEnd = '';
				$memberprice = 0;
				if($row->discountprices>0 AND isSeller() != 'Y' ){
					$memberprice = number_format($row->sellprice - $row->discountprices);
					$strikeStart = "<strike>";
					$strikeEnd = "</strike>";
					$memberpriceValue = ($row->sellprice - $row->discountprices);
				}

				if ($i!=0 && $i%$special_2_cols!=0) {
					$sp_list.= "<td align=\"center\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_lineb.gif\" border=\"0\"></td>\n";
				}
				$sp_list.= "<td width=\"".(100/$special_2_cols)."%\">\n";
				$sp_list.= "<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" id=\"B".$row->productcode."\" onmouseover=\"quickfun_show(this,'B".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'B".$row->productcode."','none')\">\n";
				$sp_list.= "<col width=\"100\"></col>\n";
				$sp_list.= "<col width=\"0\"></col>\n";
				$sp_list.= "<col width=\"100%\"></col>\n";
				$sp_list.= "<TR>\n";
				$sp_list.= "	<TD align=\"center\" style=\"padding-top:1px;padding-bottom:1px;\" nowrap>";
				$sp_list.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>";
				$sp_list.="		<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','B','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$sp_list.= "	<TD style=\"padding-left:5px;padding-right:5px;word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span style="color:#bcbcbc; font-size:11px; font-family:"돋움">'.$row->prmsg.'</span>':'')."</A>\n";

				$sp_list.= $strikeStart;

				$sp_list.= $discountRate;

				if($row->consumerprice!=0) {
					$sp_list.= "<br><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><FONT class=\"prconsumerprice\"><strike>".number_format($row->consumerprice)."</strike>원</font>\n";
				}
				if($dicker=dickerview($row->etctype,$wholeSaleIcon.number_format($row->sellprice)."원",1)) {
					$sp_list.= "<br><font class=\"prprice\">".$dicker."</font>";
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<br><font class=\"prprice\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".$wholeSaleIcon.number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $sp_list.= "(기본가)";
					$sp_list.= "</font>";
				} else {
					$sp_list.="<br><font class=\"prprice\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) $sp_list.= $wholeSaleIcon.number_format($row->sellprice)."원";
					else $sp_list.= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
					$sp_list.= "</font>";
				}

				$sp_list.= $strikeEnd;

				if ($row->quantity=="0") $sp_list.= soldout();

				//회원할인가 적용
				if( $memberprice > 0 ) {
					$sp_list.="<br />▼<br /><img src=\"".$Dir."images/common/memsale_icon.gif\" style=\"position:relative; top:0.1em;\" alt=\"\" />".dickerview($row->etctype,$memberprice."원");
				}

				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y");
				if($reserveconv>0) {
					$sp_list.= "<br><font class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\" align=\"absmiddle\" alt=\"\" />".number_format($reserveconv)."원</font>\n";
				}
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist3_tag_2_count;$ii++) {
						$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
				}
				$sp_list.= "	</td>\n";
				$sp_list.= "</tr>\n";
				$sp_list.= "</table>\n";
				$sp_list.= "</td>\n";
				$i++;
				if ($i%$special_2_cols==0) {
					$sp_list.= "</tr><tr><td height=\"1\" colspan=\"".($special_2_cols*2-1)."\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\"></td></tr><tr>\n";
				}
				if ($i==$special_2_num) break;
			}
			if($i>0 && $i<$special_2_cols) {
				for($k=0; $k<($special_2_cols-$i); $k++) {
					$sp_list.="<td></td>\n<td width=\"".(100/$special_2_cols)."%\"></td>\n";
				}
			}
			if ($i!=0 && $i%$special_2_cols) {
				$sp_list.= "</tr><tr><td height=\"1\" colspan=\"".($special_2_cols*2-1)."\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\"></td>\n";
			}
		}
		mysql_free_result($result);
		$sp_list.= "</tr>\n";
		$sp_list.= "</table>\n";

		if($i>0) {
			if($special_show_cnt) {
				$special_show_list.="</tr><td height=\"20\"></td></tr>\n";
			}
			$special_show_list.="<tr>\n";
			$special_show_list.="	<td>\n";
			$special_show_list.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
			$special_show_list.="	<tr>\n";
			$special_show_list.="		<td background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_titlebg3.gif\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_besttitle.gif\" border=\"0\"></td>\n";
			$special_show_list.="	</tr>\n";
			$special_show_list.="	</table>\n";
			$special_show_list.="	</td>\n";
			$special_show_list.="</tr>\n";
			$special_show_list.="<tr>\n";
			$special_show_list.="	<td>\n";
			$special_show_list.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
			$special_show_list.="	<tr>\n";
			$special_show_list.="		<td>\n";
			$special_show_list.="		".$sp_list."\n";
			$special_show_list.="		</td>\n";
			$special_show_list.="	</tr>\n";
			$special_show_list.="	</table>\n";
			$special_show_list.="	</td>\n";
			$special_show_list.="</tr>\n";
			$special_show_cnt++;
		}
	}
}



//추천
$special_3_num=$special_3_cols*$special_3_rows;
if(eregi("3",$_cdata->special)) {
	$sql = "SELECT special_list FROM tblspecialcode ";
	$sql.= "WHERE code='".$code."' AND special='3' ";
	$result=mysql_query($sql,get_db_conn());
	$sp_prcode="";
	$sp_list="";
	if($row=mysql_fetch_object($result)) {
		$sp_prcode=ereg_replace(',','\',\'',$row->special_list);
	}
	mysql_free_result($result);

	if(strlen($sp_prcode)>0) {
		$sql = productQuery ();
		$sql.= "WHERE a.productcode IN ('".$sp_prcode."') AND a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if(strlen($not_qry)>0) {
			$sql.= $not_qry." ";
		}
		$sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
		$sql.= "LIMIT ".$special_3_num;
		$result=mysql_query($sql,get_db_conn());
		$i=0;
		//$special_3_type => I:이미지A형, D:이미지B형, L:리스트형
		if($special_3_type == "I") {
			$sp_list.= "<table cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n";
			$table_width=ceil(100/$special_3_cols);
			for($j=1;$j<=$special_3_cols;$j++) {
				if($j>1)
					$sp_list.="<col width=10></col>\n";
				$sp_list.="<col width=></col>\n";
				//$sp_list.="<col width=".$table_width."%></col>\n";
			}
			$sp_list.= "<tr><td height=\"15\"></td></tr>\n";
			$sp_list.= "<tr>\n";
			while($row=mysql_fetch_object($result)) {

				// 예약상품 아이콘 추가
				$row->etctype = reservationEtcType($row->reservation,$row->etctype);

				// 도매 가격 적용 상품 아이콘
				$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";

				// 할인율 표시
				$discountRate = ( $row->discountRate > 0 ) ? "<strong>".$row->discountRate."</strong>%↓" : "";

				$memberpriceValue = $row->sellprice;
				$strikeStart = $strikeEnd = '';
				$memberprice = 0;
				if($row->discountprices>0 AND isSeller() != 'Y' ){
					$memberprice = number_format($row->sellprice - $row->discountprices);
					$strikeStart = "<strike>";
					$strikeEnd = "</strike>";
					$memberpriceValue = ($row->sellprice - $row->discountprices);
				}

				$tableSize = $_data->primg_minisize + 12;
				if ($i!=0 && $i%$special_3_cols==0) {
					$sp_list.= "</tr><tr><td colspan=\"9\" height=\"10\"></td></tr>\n";
				}
				if ($i!=0 && $i%$special_3_cols!=0) {
					$sp_list.= "<td width=\"10\" nowrap></td>";
				}

				$sp_list.= "<td align=\"center\" valign=\"top\">\n";
				$sp_list.= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"".$tableSize."\" border=\"0\" id=\"H".$row->productcode."\" onmouseover=\"quickfun_show(this,'H".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'H".$row->productcode."','none')\" class=\"prInfoBox\">\n";
				$sp_list.= "<TR>\n";
				$sp_list.= "	<TD align=\"center\" height=\"120\" style=\"padding:5px;\">";
				$sp_list.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>";
				$sp_list.= "</tr>\n";

				$sp_list.= "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','H','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";

				$sp_list.= "<tr>";
				$sp_list.= "	<TD style=\"padding:5px 7px; word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span style="color:#bcbcbc; font-size:11px; font-family:"돋움">'.$row->prmsg.'</span>':'')."</A></td>\n";
				$sp_list.= "</tr>\n";

				//시중가 + 판매가 + 할인율 + 회원할인가
				$sp_list.= "<tr>
									<td style=\"padding:0px 7px 7px 7px; word-break:break-all;\">
										<table border=0 cellpadding=0 cellspacing=0 width=100%>
											<tr>
												<td>
				";
				if($row->consumerprice!=0) {
					//$sp_list.= "<tr>\n";
					//$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
					$sp_list.= "	<span class=\"prconsumerprice\" style=\"padding-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</span>\n";
					//$sp_list.= "</tr>\n";
				}

				// 회원 할인가가 있을 때 가격 class 변경
				if($memberprice > 0){
					$mainprpriceClass = "";
				}else{
					$mainprpriceClass = "mainprprice";
				}

				//$sp_list.= "<tr>\n";
				//$sp_list.= "	<TD align=\"center\" style=\"word-break:break-all;\" class=\"prprice\">";

				$sp_list.="<span style=\"white-space:nowrap;\">";
				$sp_list.= $strikeStart;
				if($dicker=dickerview($row->etctype,$wholeSaleIcon.number_format($row->sellprice)."원",1)) {
					$sp_list.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<strong class=\"".$mainprpriceClass."\">".$wholeSaleIcon.number_format($row->sellprice)."</strong><strong>원</strong>";
					//if (strlen($row->option_price)!=0) $sp_list.= "(기본가)";
				} else {
					//$sp_list.="<img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0){
						$sp_list.= $wholeSaleIcon.number_format($row->sellprice)."원";
					}else{
						$sp_list.= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
					}
				}
				$sp_list.= $strikeEnd;
				$sp_list.="
								</span>
							</td>
				";
				if($row->discountRate > 0){
					$sp_list.="<td align=\"right\" valign=\"bottom\" class=\"discount\">".$discountRate."</td>";
				}
				$sp_list.="
						</tr>
					</table>
				";

				if ($row->quantity=="0") $sp_list.= soldout();
				//$sp_list.= "	</td>\n";
				//$sp_list.= "</tr>\n";

				//회원할인가 적용
				if( $memberprice > 0 ) {
					//$sp_list.="<tr>\n";
					//$sp_list.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainprprice\"><img src=\"".$Dir."images/common/memsale_icon.gif\" style=\"position:relative; top:0.1em;\" alt=\"\" />".dickerview($row->etctype,$memberprice."원")."</td>\n";
					$sp_list.="	<div><span class=\"mainprprice\">".dickerview($row->etctype,$memberprice."원")."</span> <img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" /></div>\n";
					//$sp_list.="</tr>\n";
				}

				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y");
				if($reserveconv>0) {
					//$sp_list.= "<tr>\n";
					//$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\" align=\"absmiddle\" alt=\"\" />".number_format($reserveconv)."원</td>\n";
					$sp_list.= "	<div style=\"margin-top:5px;\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\" align=\"absmiddle\" alt=\"\" /><span class=\"prreserve\">".number_format($reserveconv)."</span>원</div>\n";
					//$sp_list.= "</tr>\n";
				}

				$sp_list.="	</td>\n";
				$sp_list.="</tr>\n";

				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist1_tag_3_count;$ii++) {
						$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<tr>\n";
								$sp_list.= "	<td align=\"center\" style=\"word-break:break-all;\">\n";
								$sp_list.= "	<img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
					if($jj!=0) {
						$sp_list.= "	</td>\n";
						$sp_list.= "</tr>\n";
					}
				}

				// 입점사 네임택
				if( nameTechUse($row->vender) ) {
					$classList = array();
					$classResult=mysql_query("SELECT * FROM `tblVenderClassType` ",get_db_conn());
					while($classRow=mysql_fetch_object($classResult)) {
						$classList[$classRow->idx] = $classRow->name;
					}
					$v_info = mysql_fetch_assoc ( mysql_query( "SELECT * FROM `tblvenderinfo` WHERE `vender`=".$row->vender." LIMIT 1;" ,get_db_conn()) );

					$venderNameTag = "<div style=\"float:left; width:60px;\"><img src=\"".$com_image_url.$v_info['com_image']."\" onerror=\"this.src='/images/no_img.gif';\" width=\"48\" style=\"border:1px solid #dddddd;\" /></div>";
					$venderNameTag .= "<div style=\"float:left; width:65%; font-size:11px;\">";
					$venderNameTag .= "	<span class=\"name\">".$v_info['com_name']."</span> <span class=\"owner\">(".$v_info['com_owner'].")</span><br />";
					$venderNameTag .= "	<a href=\"javascript:GoMinishop('/minishop.php?storeid=".$v_info['id']."')\"><img src=\"/images/common/icon_vender_go.gif\" border=\"0\" alt=\"전체상품보기\" /></a>";
					$venderNameTag .= "</div>";

					// 네임텍 출력
					$sp_list.="
						<tr>
							<td class=\"nameTagBox\">".$venderNameTag."</td>
						</tr>
					";
				}

				$sp_list.= "</table>\n";
				$sp_list.= "</td>";
				$i++;

				if ($i==$special_3_num) break;
				if ($i%$special_3_cols==0) {
					$sp_list.= "</tr><tr><td colspan=\"".($special_3_cols*2-1)."\" height=\"5\"></td><tr>\n";
				}
			}
			if($i>0 && $i<$special_3_cols) {
				for($k=0; $k<($special_3_cols-$i); $k++) {
					$sp_list.="<td width=\"10\" nowrap></td><td width=\"".$tableSize."\"></td>\n";
				}
			}

		} else if($special_3_type == "L") {
			$colspan="6";
			$sp_list.= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$sp_list.= "<col width=\"15%\"></col>\n";
			$sp_list.= "<col width=\"0\"></col>\n";
			$sp_list.= "<col width=\"50%\"></col>\n";
			$sp_list.= "<col width=\"12%\"></col>\n";
			$sp_list.= "<col width=\"12%\"></col>\n";
			$sp_list.= "<col width=\"11%\"></col>\n";
			$sp_list.= "<tr height=\"30\" align=\"center\" bgcolor=\"#F8F8F8\">\n";
			$sp_list.= "	<td colspan=\"2\"><b><font color=\"#000000\">제품사진</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">제품명</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">시중가격</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">판매가격</font></b></td>\n";
			$sp_list.= "	<td><b><font color=\"#000000\">적립금</font></b></td>\n";
			$sp_list.= "</tr>\n";
			$sp_list.= "<tr>\n";
			$sp_list.= "	<td height=\"1\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\" colspan=\"".$colspan."\"></td>";
			$sp_list.= "</tr>\n";
			while($row=mysql_fetch_object($result)) {

				// 예약상품 아이콘 추가
				$row->etctype = reservationEtcType($row->reservation,$row->etctype);

				// 도매 가격 적용 상품 아이콘
				$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";

				// 할인율 표시
				$discountRate = ( $row->discountRate > 0 ) ? "(".$row->discountRate."%)" : "";

				$memberpriceValue = $row->sellprice;
				$strikeStart = $strikeEnd = '';
				$memberprice = 0;
				if($row->discountprices>0 AND isSeller() != 'Y' ){
					$memberprice = number_format($row->sellprice - $row->discountprices);
					$strikeStart = "<strike>";
					$strikeEnd = "</strike>";
					$memberpriceValue = ($row->sellprice - $row->discountprices);
				}

				$sp_list.= "<tr align=\"center\" id=\"H".$row->productcode."\" onmouseover=\"quickfun_show(this,'H".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'H".$row->productcode."','none')\">\n";
				$sp_list.= "	<td style=\"padding-top:1px;padding-bottom:1px;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>\n";
				$sp_list.="		<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','H','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$sp_list.= "	<td style=\"padding-left:5px;padding-right:5px;word-break:break-all;\" align=\"left\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span style="color:#bcbcbc; font-size:11px; font-family:"돋움">'.$row->prmsg.'</span>':'')."</A>";
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist2_tag_3_count;$ii++) {
						$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<br><br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
				}
				$sp_list.= "	</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prprice\">";

				$sp_list.= $strikeStart;

				$sp_list.= $discountRate;

				if($dicker=dickerview($row->etctype,$wholeSaleIcon.number_format($row->sellprice)."원",1)) {
					$sp_list.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">".$wholeSaleIcon.number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $sp_list.= "(기본가)";
				} else {
					$sp_list.="<img src=\"".$Dir."images/common/won_icon.gif\" border=0 style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) $sp_list.= $wholeSaleIcon.number_format($row->sellprice)."원";
					else $sp_list.= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
				}

				$sp_list.= $strikeEnd;

				if( $memberprice > 0 ) $sp_list.="<br><font class=\"mainprprice\">".dickerview($row->etctype,number_format($memberprice)."원")."</font>";

				if ($row->quantity=="0") $sp_list.= soldout();
				$sp_list.= "	</td>\n";
				$sp_list.= "	<TD style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\" align=\"absmiddle\" alt=\"\" />".number_format(getReserveConversion($row->reserve,$wholeSaleIcon.$row->reservetype,$memberpriceValue,"Y"))."원</td>\n";
				$sp_list.= "</tr>\n";
				$sp_list.= "<tr>\n";
				$sp_list.= "	<td height=\"1\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\" colspan=\"".$colspan."\"></td>";
				$sp_list.= "</tr>\n";
				$i++;
			}
		} else if($special_3_type == "D") {
			$sp_list.= "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$sp_list.= "<tr>\n";
			$sp_list.= "	<td height=\"5\"></td>\n";
			$sp_list.= "</tr>\n";
			$sp_list.= "<tr>\n";
			while($row=mysql_fetch_object($result)) {

				// 예약상품 아이콘 추가
				$row->etctype = reservationEtcType($row->reservation,$row->etctype);

				// 도매 가격 적용 상품 아이콘
				$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";

				$discountRate = ( $row->discountRate > 0 ) ? "(".$row->discountRate."%)" : "";

				$memberpriceValue = $row->sellprice;
				$strikeStart = $strikeEnd = '';
				$memberprice = 0;
				if($row->discountprices>0 AND isSeller() != 'Y' ){
					$memberprice = number_format($row->sellprice - $row->discountprices);
					$strikeStart = "<strike>";
					$strikeEnd = "</strike>";
					$memberpriceValue = ($row->sellprice - $row->discountprices);
				}

				if ($i!=0 && $i%$special_3_cols!=0) {
					$sp_list.= "<td align=\"center\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_lineb.gif\" border=\"0\"></td>\n";
				}
				$sp_list.= "<td width=\"".(100/$special_3_cols)."%\">\n";
				$sp_list.= "<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" id=\"H".$row->productcode."\" onmouseover=\"quickfun_show(this,'H".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'H".$row->productcode."','none')\">\n";
				$sp_list.= "<col width=\"100\"></col>\n";
				$sp_list.= "<col width=\"0\"></col>\n";
				$sp_list.= "<col width=\"100%\"></col>\n";
				$sp_list.= "<TR>\n";
				$sp_list.= "	<TD align=\"center\" style=\"padding-top:1px;padding-bottom:1px;\" nowrap>";
				$sp_list.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
					$sp_list.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $sp_list.= "height=\"".$_data->primg_minisize2."\" ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $sp_list.= "width=\"".$_data->primg_minisize."\" ";
						else if ($width[1]>=$_data->primg_minisize) $sp_list.= "height=\"".$_data->primg_minisize."\" ";
					}
				} else {
					$sp_list.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
				}
				$sp_list.= "	></A></td>";
				$sp_list.="		<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','H','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$sp_list.= "	<TD style=\"padding-left:5px;padding-right:5px;word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span style="color:#bcbcbc; font-size:11px; font-family:"돋움">'.$row->prmsg.'</span>':'')."</A>\n";

				$sp_list.= $strikeStart;

				$sp_list.= $discountRate;

				if($row->consumerprice!=0) {
					$sp_list.= "<br><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><FONT class=\"prconsumerprice\"><strike>".number_format($row->consumerprice)."</strike>원</font>\n";
				}
				if($dicker=dickerview($row->etctype,$wholeSaleIcon.number_format($row->sellprice)."원",1)) {
					$sp_list.= "<br><font class=\"prprice\">".$dicker."</font>";
				} else if(strlen($_data->proption_price)==0) {
					$sp_list.= "<br><font class=\"prprice\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".$wholeSaleIcon.number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $sp_list.= "(기본가)";
					$sp_list.= "</font>";
				} else {
					$sp_list.="<br><font class=\"prprice\"><img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
					if (strlen($row->option_price)==0) $sp_list.= $wholeSaleIcon.number_format($row->sellprice)."원";
					else $sp_list.= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
					$sp_list.= "</font>";
				}

				$sp_list.= $strikeEnd;

				//회원할인가 적용
				if( $memberprice > 0 ) $sp_list.="<img src=\"".$Dir."images/common/memsale_icon.gif\" style=\"position:relative; top:0.1em;\" alt=\"\" />".dickerview($row->etctype,$memberprice."원");

				if ($row->quantity=="0") $sp_list.= soldout();
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y");
				if($reserveconv>0) {
					$sp_list.= "<br><font class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\" align=\"absmiddle\" alt=\"\" />".number_format($reserveconv)."원</font>\n";
				}
				if($_data->ETCTYPE["TAGTYPE"]=="Y") {
					$taglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<$plist3_tag_3_count;$ii++) {
						$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
						if(strlen($taglist[$ii])>0) {
							if($jj==0) {
								$sp_list.= "<br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							else {
								$sp_list.= "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
							}
							$jj++;
						}
					}
				}
				$sp_list.= "	</td>\n";
				$sp_list.= "</tr>\n";
				$sp_list.= "</table>\n";
				$sp_list.= "</td>\n";
				$i++;
				if ($i%$special_3_cols==0) {
					$sp_list.= "</tr><tr><td height=\"1\" colspan=\"".($special_3_cols*2-1)."\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\"></td></tr><tr>\n";
				}
				if ($i==$special_3_num) break;
			}
			if($i>0 && $i<$special_3_cols) {
				for($k=0; $k<($special_3_cols-$i); $k++) {
					$sp_list.="<td></td>\n<td width=\"".(100/$special_3_cols)."%\"></td>\n";
				}
			}
			if ($i!=0 && $i%$special_3_cols) {
				$sp_list.= "</tr><tr><td height=\"1\" colspan=\"".($special_3_cols*2-1)."\" background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_line3.gif\"></td>\n";
			}
		}
		mysql_free_result($result);
		$sp_list.= "</tr>\n";
		$sp_list.= "</table>\n";

		if($i>0) {
			if($special_show_cnt) {
				$special_show_list.="</tr><td height=\"20\"></td></tr>\n";
			}
			$special_show_list.="<tr>\n";
			$special_show_list.="	<td>\n";
			$special_show_list.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
			$special_show_list.="	<tr>\n";
			$special_show_list.="		<td background=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_titlebg3.gif\"><img src=\"".$Dir."images/common/product/".$_cdata->list_type."/plist_skin_hotitem.gif\" border=\"0\"></td>\n";
			$special_show_list.="	</tr>\n";
			$special_show_list.="	</table>\n";
			$special_show_list.="	</td>\n";
			$special_show_list.="</tr>\n";
			$special_show_list.="<tr>\n";
			$special_show_list.="	<td>\n";
			$special_show_list.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
			$special_show_list.="	<tr>\n";
			$special_show_list.="		<td>\n";
			$special_show_list.="		".$sp_list."\n";
			$special_show_list.="		</td>\n";
			$special_show_list.="	</tr>\n";
			$special_show_list.="	</table>\n";
			$special_show_list.="	</td>\n";
			$special_show_list.="</tr>\n";
			$special_show_cnt++;
		}
	}
}

$special_show_list.="	</table>\n";
$special_show_list.="	</td>\n";
$special_show_list.="</tr>\n";
$special_show_list.="<tr>\n";
$special_show_list.="	<td height=\"10\"></td>\n";
$special_show_list.="</tr>\n";

if($special_show_cnt)
	echo $special_show_list;
?>
<!-- 신규/인기/추천 끝 -->


<!--카테고리 이벤트배너-->
<?
	if($_cdata->title_type=="image") {
		if(file_exists($Dir.DataDir."shopimages/etc/CODE".$code.".gif")) {
			//echo "<tr><td height=\"40\"></td></tr><tr>\n";
			echo "<tr>\n";
			echo "	<td align=center><img src=\"".$Dir.DataDir."shopimages/etc/CODE".$code.".gif\" border=0 align=absmiddle></td>\n";
			echo "</tr>\n";
		}
	} else if($_cdata->title_type=="html") {
		if(strlen($_cdata->title_body)>0) {
			//echo "<tr><td height=\"40\"></td></tr><tr>\n";
			echo "<tr>\n";
			echo "	<td align=center>";
			if (strpos(strtolower($_cdata->title_body),"<table")!==false)
				echo $_cdata->title_body;
			else
				echo ereg_replace("\n","<br>",$_cdata->title_body);
			echo "	</td>\n";
			echo "</tr><tr><td height=\"40\"></td></tr>\n";
		}
	}
?>
<!--카테고리 이벤트 배너-->

<!-- 상품목록 시작 -->
<?
	if($_cdata->islist=="Y"){
		$sql = "SELECT COUNT(*) as t_count FROM tblproduct AS a left join tblcategorycode as cc on cc.productcode = a.productcode ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= $qry." ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if(strlen($not_qry)>0) {
			$sql.= $not_qry." ";
		}
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$t_count = (int)$row->t_count;
		mysql_free_result($result);
		$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
?>
	<tr><td height="20"></td></tr>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_sticon.gif" border="0"></td>
					<td width="100%" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_stibg.gif" style="color:#ffffff;font-size:11px;"><B><?=$_cdata->code_name?></B> 총 등록상품 : <b><?=$t_count?>건</b></td>
					<td><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_stimg.gif" border="0"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td height="30">
		<?
			$_date="";
			$_sellcount_desc="";
			$_price="";
			$_price_desc="";

			switch(trim($sort)){
				case "sellcount_desc":
					$_sellcount_desc="class=\"sortOn\"";
				break;

				case "price":
					$_price="class=\"sortOn\"";
				break;

				case "price_desc":
					$_price_desc="class=\"sortOn\"";
				break;

				case "reserve_desc":
					$_reserve_desc="class=\"sortOn\"";
				break;

				case "date":
				default:
					$_date="class=\"sortOn\"";
				break;
			}
		?>
			<ul class="prSortType">
				<li><a href="javascript:ChangeSort('');" <?=$_date?>>신규등록순</a></li>
				<li><a href="javascript:ChangeSort('sellcount_desc');" <?=$_sellcount_desc?>>인기상품순</a></li>
				<li><a href="javascript:ChangeSort('price');" <?=$_price?>>낮은가격순</a></li>
				<li><a href="javascript:ChangeSort('price_desc');" <?=$_price_desc?>>높은가격순</a></li>
				<li class="last"><a href="javascript:ChangeSort('reserve_desc');" <?=$_reserve_desc?>>적립금순</a></li>
			</ul>
			<div style="float:right; text-align:right;">
			<?
				if($listnum == 8) $sel8 = "selected";
				if($listnum == 16) $sel16 = "selected";
				if($listnum == 32) $sel32 = "selected";

				$listselect = "
					<select name=\"listnum2\" onchange=\"ChangeNum(this)\">
						<option value='8' ".$sel8.">8</option>
						<option value='16' ".$sel16.">16</option>
						<option value='32' ".$sel32.">32</option>
					</select>
				";
				echo $listselect."개씩 보기";
			?>
			</div>
		</td>
		<!--
		<td height="28" style="padding-left:10px;"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text01.gif" border="0"><a href="javascript:ChangeSort('production');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="production")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('production_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="production_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text02.gif" border="0"><a href="javascript:ChangeSort('name');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="name")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('name_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="name_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text03.gif" border="0"><a href="javascript:ChangeSort('price');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="price")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('price_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="price_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text04.gif" border="0"><a href="javascript:ChangeSort('reserve');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="reserve")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('reserve_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="reserve_desc")echo"_on";?>.gif" border="0"></a></td>
		-->
	</tr>
	<tr>
		<td height="1" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_line3.gif"></td>
	</tr>
	<tr><td height="15"></td></tr>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<table cellpadding="2" cellspacing="0" width="100%">
							<tr>
<?
		//번호, 사진, 상품명, 제조사, 가격
		$tmp_sort=explode("_",$sort);
		if($tmp_sort[0]=="reserve") {
			$addsortsql=",IF(a.reservetype='N',a.reserve*1,a.reserve*a.sellprice*0.01) AS reservesort ";
		}
		$sql = "SELECT distinct a.productcode,a.productname,a.sellprice,a.quantity,a.consumerprice,a.reserve,a.reservetype,a.production, ";
		if($_cdata->sort=="date2") $sql.="IF(a.quantity<=0,'11111111111111',a.date) as date, ";
		$sql.= "a.tag, a.tinyimage, a.etctype, a.option_price, a.madein, a.model, a.brand, a.selfcode,a.prmsg, a.discountRate, a.vender, a.sellcount, a.reservation ";
		$sql.= $addsortsql;
		$sql.= "FROM tblproduct AS a  left join tblcategorycode as cc on cc.productcode = a.productcode ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= $qry." ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if(strlen($not_qry)>0) {
			$sql.= $not_qry." ";
		}
		if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="sellcount") $sql.= "ORDER BY sellcount ".$tmp_sort[1]." ";
		else {
			if(strlen($_cdata->sort)==0 || $_cdata->sort=="date" || $_cdata->sort=="date2") {
				if(eregi("T",$_cdata->type) && strlen($t_prcode)>0) {
					$sql.= "ORDER BY FIELD(a.productcode,'".$t_prcode."'),date DESC ";
				} else {
					$sql.= "ORDER BY date DESC ";
				}
			} else if($_cdata->sort=="productname") {
				$sql.= "ORDER BY a.productname ";
			} else if($_cdata->sort=="production") {
				$sql.= "ORDER BY a.production ";
			} else if($_cdata->sort=="price") {
				$sql.= "ORDER BY a.sellprice ";
			}
		}
		$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
		$result=mysql_query($sql,get_db_conn());

		$nums=mysql_num_rows($result);
		if($nums == 0){
			echo "<td align=\"center\">등록된 상품이 없습니다.</td></tr>";
		}

		$i=0;
		while($row=mysql_fetch_object($result)) {

			// 예약상품 아이콘 추가
			$row->etctype = reservationEtcType($row->reservation,$row->etctype);

			// 도매 가격 적용 상품 아이콘
			$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";

			// 할인율 표시
			$discountRate = ( $row->discountRate > 0 ) ? "<strong>".$row->discountRate."</strong>%↓" : "";

			$memberpriceValue = $row->sellprice;
			$strikeStart = $strikeEnd = '';
			$memberprice = 0;
			if($row->discountprices>0 AND isSeller() != 'Y' ){
				$memberprice = number_format($row->sellprice - $row->discountprices);
				$strikeStart = "<strike>";
				$strikeEnd = "</strike>";
				$memberpriceValue = ($row->sellprice - $row->discountprices);
			}

			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
			$tableSize = $_data->primg_minisize + 12;

			if ($i!=0 && $i%4==0) {
				echo "</tr><tr><td colspan=\"9\" height=\"10\"></td></tr>\n";
			}
			if ($i!=0 && $i%4!=0) {
				echo "<td width=\"1\" nowrap></td>";
			}

			echo "<td align=\"center\" valign=\"top\">\n";
			echo "<TABLE cellSpacing=\"0\" cellPadding=\"0\" width=\"".$tableSize."\" border=\"0\" id=\"G".$row->productcode."\" onmouseover=\"quickfun_show(this,'G".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'G".$row->productcode."','none')\" class=\"prInfoBox\">\n";
			echo "<TR>\n";
			echo "	<TD align=\"center\" height=\"120\" style=\"padding:5px;\">";
			echo "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."&sort=".$sort."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
				echo "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) echo "height=\"".$_data->primg_minisize2."\" ";
					else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) echo "width=\"".$_data->primg_minisize."\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) echo "width=\"".$_data->primg_minisize."\" ";
					else if ($width[1]>=$_data->primg_minisize) echo "height=\"".$_data->primg_minisize."\" ";
				}
			} else {
				echo "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			echo "	></A></td>";
			echo "</tr>\n";

			echo "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','G','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";

			echo "<tr>";
			echo "	<TD style=\"padding:5px 7px; word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."&sort=".$sort."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>".(strlen($row->prmsg)?'<br /><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</td>\n";
			echo "</tr>\n";

			//시중가 + 판매가 + 할인율 + 회원할인가
			echo "<tr>";
			echo "	<td style=\"padding:0px 7px 7px 7px; word-break:break-all;\">
							<table border=0 cellpadding=0 cellspacing=0 width=100%>
								<tr>
									<td>
			";
			if($row->consumerprice!=0) {
				//echo "<tr>\n";
				//echo "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
				echo "	<span class=\"prconsumerprice\" style=\"padding-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</span>\n";
				//echo "</tr>\n";
			}

			// 회원 할인가가 있을 때 가격 class 변경
			if($memberprice > 0){
				$mainprpriceClass = "";
			}else{
				$mainprpriceClass = "mainprprice";
			}

			//echo "<tr>\n";
			//echo "	<TD align=\"center\" style=\"word-break:break-all;\" class=\"prprice\">";

			echo "<span style=\"white-space:nowrap;\">";
			echo $strikeStart;
			if($dicker=dickerview($row->etctype,$wholeSaleIcon.number_format($row->sellprice)."원",1)) {
				echo $dicker;
			} else if(strlen($_data->proption_price)==0) {
				echo "<strong class=\"".$mainprpriceClass."\">".$wholeSaleIcon.number_format($row->sellprice)."원</strong>";
				//if (strlen($row->option_price)!=0) echo "(기본가)";
			} else {
				//echo "<img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
				if (strlen($row->option_price)==0) echo $wholeSaleIcon.number_format($row->sellprice)."원";
				else echo ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
			}
			echo $strikeEnd;
			echo "
							</span>
						</td>
			";
			if($row->discountRate > 0){
				echo "<td align=\"right\" valign=\"bottom\" class=\"discount\">".$discountRate."</td>";
			}
			echo "
					</tr>
				</table>
			";

			if ($row->quantity=="0") echo soldout();
			//echo "	</td>\n";
			//echo "</tr>\n";


			//회원할인가 적용
			if( $memberprice > 0 ) {
				//echo "<tr>\n";
				//echo "	<td align=center valign=top style=\"word-break:break-all;\" class=\"mainprprice\"><img src=\"".$Dir."images/common/memsale_icon.gif\" style=\"position:relative; top:0.1em;\" alt=\"\" />".dickerview($row->etctype,$memberprice."원")."</td>\n";
				echo "	<div><span class=\"mainprprice\">".dickerview($row->etctype,$memberprice."원")."</span> <img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" /></div>\n";
				//echo "</tr>\n";
			}

			$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y");
			if($reserveconv>0) {
				//echo "<tr>\n";
				//echo "	<td align=\"center\" style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\" align=\"absmiddle\" alt=\"\" />".number_format($reserveconv)."원</td>\n";
				echo "	<div style=\"margin-top:5px;\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\" align=\"absmiddle\" alt=\"\" /><span class=\"prreserve\">".number_format($reserveconv)."원</span></div>\n";
				//echo "</tr>\n";
			}

			echo "	</td>\n";
			echo "</tr>\n";

			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$plist0_tag_0_count;$ii++) {
					$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) {
							echo "<tr>\n";
							echo "	<td align=\"center\" style=\"word-break:break-all;\">\n";
							echo "	<img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
						}
						else {
							echo "<FONT class=\"prtag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$taglist[$ii]."</font></a>";
						}
						$jj++;
					}
				}
				if($jj!=0) {
					echo "	</td>\n";
					echo "</tr>\n";
				}
			}

			// 입점사 네임택
			if( nameTechUse($row->vender) ) {
				$classList = array();
				$classResult=mysql_query("SELECT * FROM `tblVenderClassType` ",get_db_conn());
				while($classRow=mysql_fetch_object($classResult)) {
					$classList[$classRow->idx] = $classRow->name;
				}
				$v_info = mysql_fetch_assoc ( mysql_query( "SELECT * FROM `tblvenderinfo` WHERE `vender`=".$row->vender." LIMIT 1;" ,get_db_conn()) );

				$venderNameTag = "<div style=\"float:left; width:60px;\"><img src=\"".$com_image_url.$v_info['com_image']."\" onerror=\"this.src='/images/no_img.gif';\" width=\"48\" style=\"border:1px solid #dddddd;\" /></div>";
				$venderNameTag .= "<div style=\"float:left; width:65%; font-size:11px;\">";
				$venderNameTag .= "	<span class=\"name\">".$v_info['com_name']."</span> <span class=\"owner\">(".$v_info['com_owner'].")</span><br />";
				$venderNameTag .= "	<a href=\"javascript:GoMinishop('/minishop.php?storeid=".$v_info['id']."')\"><img src=\"/images/common/icon_vender_go.gif\" border=\"0\" alt=\"전체상품보기\" /></a>";
				$venderNameTag .= "</div>";

				// 네임텍 출력
				echo "
					<tr>
						<td class=\"nameTagBox\">".$venderNameTag."</td>
					</tr>
				";
			}

			echo "</table>\n";
			echo "</td>";

			$i++;
		}
		if($i>0 && $i<4) {
			for($k=0; $k<(4-$i); $k++) {
				echo "<td width=\"10\" nowrap></td>\n<td width=\"".$tableSize."\"></td>\n";
			}
		}
		mysql_free_result($result);
?>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr><td height="15"></td></tr>
	<tr>
		<td height="1" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_line3.gif"></td>
	</tr>
	<!--
	<tr>
		<td height="28" style="padding-left:10px;"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text01.gif" border="0"><a href="javascript:ChangeSort('production');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="production")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('production_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="production_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text02.gif" border="0"><a href="javascript:ChangeSort('name');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="name")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('name_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="name_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text03.gif" border="0"><a href="javascript:ChangeSort('price');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="price")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('price_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="price_desc")echo"_on";?>.gif" border="0"></a><img src="../images/common/space_line.gif" width="8" height="1" border="0"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_text04.gif" border="0"><a href="javascript:ChangeSort('reserve');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerotop<?if($sort=="reserve")echo"_on";?>.gif" border="0"></a><a href="javascript:ChangeSort('reserve_desc');"><IMG SRC="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_nerodow<?if($sort=="reserve_desc")echo"_on";?>.gif" border="0"></a></td>
	</tr>
	<tr>
		<td height="1" background="<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_line3.gif"></td>
	</tr>
	-->
	<tr><td height="20"></td></tr>
<?
		$total_block = intval($pagecount / $setup[page_num]);

		if (($pagecount % $setup[page_num]) > 0) {
			$total_block = $total_block + 1;
		}

		$total_block = $total_block - 1;

		if (ceil($t_count/$setup[list_num]) > 0) {
			// 이전	x개 출력하는 부분-시작
			$a_first_block = "";
			if ($nowblock > 0) {
				$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><FONT class=\"prlist\">[1...]</FONT></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><FONT class=\"prlist\">[prev]</FONT></a>&nbsp;&nbsp;";

				$a_prev_page = $a_first_block.$a_prev_page;
			}

			// 일반 블럭에서의 페이지 표시부분-시작

			if (intval($total_block) <> intval($nowblock)) {
				$print_page = "";
				for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
					if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
						$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$setup[page_num]) + $gopage)."</font> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</FONT></a> ";
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
						$print_page .= "<FONT class=\"choiceprlist\">".(intval($nowblock*$setup[page_num]) + $gopage)."</FONT> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\"><FONT class=\"prlist\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</FONT></a> ";
					}
				}
			}		// 마지막 블럭에서의 표시부분-끝


			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
				$last_gotopage = ceil($t_count/$setup[list_num]);

				$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><FONT class=\"prlist\">[...".$last_gotopage."]</FONT></a>";

				$next_page_exists = true;
			}

			// 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><FONT class=\"prlist\">[next]</FONT></a>";

				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "<FONT class=\"prlist\">1</FONT>";
		}
?>
	<tr>
		<td style="font-size:11px;" align="center"><?=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page?></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<?}?>
</table>