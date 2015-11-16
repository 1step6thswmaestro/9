<?
if(substr(getenv("SCRIPT_NAME"),-24)=="/productsection_text.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}

$pagecount = (($t_count - 1) / $setup["list_num"]) + 1;

//상품목록 ($prlist_type이 1:이미지A형,2:이미지B형,3:리스트형,4:공구형일 경우에만)
$prlist1=""; $prlist2=""; $prlist3=""; $prlist4="";
if(preg_match("/^(1|2|3|4)$/",$prlist_type)) {
	if($t_count<=0) {
		$prlist1 = "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td align=center valign=middle height=30>등록된 상품이 없습니다.</td></tr></table>";
		$prlist2 = "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td align=center valign=middle height=30>등록된 상품이 없습니다.</td></tr></table>";
		$prlist3 = "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td align=center valign=middle height=100>등록된 상품이 없습니다.</td></tr></table>";
		$prlist4 = "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td align=center valign=middle height=50>등록된 상품이 없습니다.</td></tr></table>";
	} else {
		$setup[list_num] = 10;
		$tmp_sort=explode("_",$sort);
		$sql = "SELECT a.productcode,a.productname,a.sellprice,a.quantity,a.consumerprice,a.reserve,a.reservetype,a.production, ";
		$sql.= "a.tag, a.tinyimage, a.etctype, a.option_price, a.madein, a.model, a.brand, a.selfcode ";
		$sql.= $addsortsql;
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		$sql.= "WHERE a.display='Y' ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
		else $sql.= "ORDER BY FIELD(a.productcode,'".$sp_prcode."') ";
		$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
		$result=mysql_query($sql,get_db_conn());
	
		$i=0;
		if($prlist_type=="1") {	####################################### 이미지A형 #############################
			$prlist1 = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			for($j=0;$j<$prlist1_cols;$j++) {
				if($j>0) $prlist1.= "<col width=10></col>\n";
				$prlist1.= "<col width=".floor(100/$prlist1_cols)."%></col>\n";
			}
			$prlist1.= "<tr>\n";
			while($row=mysql_fetch_object($result)) {
				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
				if ($i>0 && $i%$prlist1_cols==0) {
					if($prlist1_colline=="Y") {
						$prlist1.="<tr><td colspan=".$prlist1_colnum." ";
						if(eregi("#prlist_colline",$body)) {
							$prlist1.= "id=prlist_colline></td></tr>\n";
						} else {
							$prlist1.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$prlist1.="<tr><td colspan=".$prlist1_colnum." height=".$prlist1_gan."></td></tr><tr>\n";
					} else {
						$prlist1.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$prlist1_cols!=0) {
					$prlist1.="<td width=10 height=100% align=center nowrap>";
					if($prlist1_rowline=="N") $prlist1.="<img width=3 height=0>";
					else if($prlist1_rowline=="Y") {
						$prlist1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(eregi("#prlist_rowline",$body)) {
							$prlist1.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$prlist1.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($prlist1_rowline=="L") {
						$prlist1.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(eregi("#prlist_rowline",$body)) {
							$prlist1.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$prlist1.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$prlist1.="</td>";
				}
				$prlist1.="<td align=center valign=top>\n";
				$prlist1.= "<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"A".$row->productcode."\" onmouseover=\"quickfun_show(this,'A".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'A".$row->productcode."','none')\">\n";
				$prlist1.= "<tr height=100>\n";
				$prlist1.= "	<td align=center>";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
					$prlist1.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $prlist1.= "height=".$_data->primg_minisize2." ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $prlist1.= "width=".$_data->primg_minisize." ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $prlist1.= "width=".$_data->primg_minisize." ";
						else if ($width[1]>=$_data->primg_minisize) $prlist1.= "height=".$_data->primg_minisize." ";
					}
				} else {
					$prlist1.= "<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
				}
				$prlist1.= "	></A></td>";
				$prlist1.= "</tr>\n";
				$prlist1.= "<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','A','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
				$prlist1.= "<tr>";
				$prlist1.= "	<td align=center valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$prlist1.= "</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($prlist1_production=="Y" || $prlist1_madein=="Y" || $prlist1_model=="Y" || $prlist1_brand=="Y") {
					$prlist1.="<tr>\n";
					$prlist1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prproduction\">";
					if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
						unset($addspec);
						if($prlist1_production=="Y" && strlen($row->production)>0) {
							$addspec[]=$row->production;
						}
						if($prlist1_madein=="Y" && strlen($row->madein)>0) {
							$addspec[]=$row->madein;
						}
						if($prlist1_model=="Y" && strlen($row->model)>0) {
							$addspec[]=$row->model;
						}
						//if($prlist1_brand=="Y" && strlen($row->brand)>0) {
						//	$addspec[]=$row->brand;
						//}
						$prlist1.= implode("/", $addspec);
					}
					$prlist1.="	</td>\n";
					$prlist1.="</tr>\n";
				}
				if($prlist1_price=="Y" && $row->consumerprice>0) {	//소비자가
					$prlist1.="<tr>\n";
					$prlist1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <strike>".number_format($row->consumerprice)."</strike>원";
					$prlist1.="	</td>\n";
					$prlist1.="</tr>\n";
				}
				$prlist1.= "<tr>\n";
				$prlist1.= "	<td align=center valign=top style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$prlist1.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$prlist1.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $prlist1.= "(기본가)";
				} else {
					$prlist1.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (strlen($row->option_price)==0) $prlist1.= number_format($row->sellprice)."원";
					else $prlist1.= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $prlist1.= soldout();
				$prlist1.= "	</td>\n";
				$prlist1.= "</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($prlist1_reserve=="Y" && $reserveconv>0) {	//적립금
					$prlist1.="<tr>\n";
					$prlist1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$prlist1.="	</td>\n";
					$prlist1.="</tr>\n";
				}
				//태그관련
				if($prlist1_tag>0 && strlen($row->tag)>0) {
					$prlist1.="<tr>\n";
					$prlist1.="	<td align=center style=\"word-break:break-all;\" class=\"prtag\"><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=ereg_replace("(<|>)","",$arrtaglist[$ii]);
						if(strlen($arrtaglist[$ii])>0) {
							if($jj<$prlist1_tag) {
								if($jj>0) $prlist1.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $prlist1.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$prlist1.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$arrtaglist[$ii]."</FONT></a>";
							$jj++;
						}
					}
					$prlist1.="	</td>\n";
					$prlist1.="</tr>\n";
				}
				$prlist1.= "</table>\n";
				$prlist1.= "</td>\n";

				$i++;

				if ($i%$prlist1_cols==0) {
					$prlist1.="</tr><tr><td colspan=".$prlist1_colnum." height=".$prlist1_gan."></td></tr>\n";
				}
			}
			if($i>0 && $i<$prlist1_cols) {
				for($k=0; $k<($prlist1_cols-$i); $k++) {
					$prlist1.="<td></td>\n<td></td>\n";
				}
			}
			$prlist1.= "</tr>\n";
			$prlist1.= "</table>\n";
		} else if($prlist_type=="2") {	####################################### 이미지B형 #####################
			$prlist2 = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			for($j=0;$j<$prlist2_cols;$j++) {
				if($j>0) $prlist2.= "<col width=10></col>\n";
				$prlist2.= "<col width=".floor(100/$prlist2_cols)."%></col>\n";
			}
			$prlist2.= "<tr>\n";
			while($row=mysql_fetch_object($result)) {
				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
				if ($i>0 && $i%$prlist2_cols==0) {
					if($prlist2_colline=="Y") {
						$prlist2.="<tr><td colspan=".$prlist2_colnum." ";
						if(eregi("#prlist_colline",$body)) {
							$prlist2.= "id=prlist_colline></td></tr>\n";
						} else {
							$prlist2.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
						}
						$prlist2.="<tr><td colspan=".$prlist2_colnum." height=".$prlist2_gan."></td></tr><tr>\n";
					} else {
						$prlist2.="<tr>\n";
					}
				}
				if ($i!=0 && $i%$prlist2_cols!=0) {
					$prlist2.="<td width=10 height=100% align=center nowrap>";
					if($prlist2_rowline=="N") $prlist2.="<img width=3 height=0>";
					else if($prlist2_rowline=="Y") {
						$prlist2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100 style=\"table-layout:fixed\"><tr><td ";
						if(eregi("#prlist_rowline",$body)) {
							$prlist2.= "id=prlist_rowline height=100></td></tr></table>\n";
						} else {
							$prlist2.= "width=1 height=100 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					} else if($prlist2_rowline=="L") {
						$prlist2.="<table border=0 cellpadding=0 cellspacing=0 width=1 height=100% style=\"table-layout:fixed\"><tr><td ";
						if(eregi("#prlist_rowline",$body)) {
							$prlist2.= "id=prlist_rowline height=100%></td></tr></table>\n";
						} else {
							$prlist2.= "width=1 height=100% style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table>\n";
						}
					}
					$prlist2.="</td>";
				}
				$prlist2.="<td align=center>\n";
				$prlist2.= "<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"A".$row->productcode."\" onmouseover=\"quickfun_show(this,'A".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'A".$row->productcode."','none')\">\n";
				$prlist2.="<col width=\"100\"></col>\n";
				$prlist2.="<col width=\"0\"></col>\n";
				$prlist2.="<col width=\"100%\"></col>\n";
				$prlist2.= "<tr height=100>\n";
				$prlist2.= "	<td align=center>";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
					$prlist2.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if($_data->ETCTYPE["IMGSERO"]=="Y") {
						if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $prlist2.= "height=".$_data->primg_minisize2." ";
						else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $prlist2.= "width=".$_data->primg_minisize." ";
					} else {
						if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $prlist2.= "width=".$_data->primg_minisize." ";
						else if ($width[1]>=$_data->primg_minisize) $prlist2.= "height=".$_data->primg_minisize." ";
					}
				} else {
					$prlist2.= "<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
				}
				$prlist2.= "	></A></td>\n";
				$prlist2.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','A','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$prlist2.= "	<td valign=middle style=\"padding-left:5\">\n";
				$prlist2.= "	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				$prlist2.= "	<tr>";
				$prlist2.= "		<td align=left valign=top style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A></td>\n";
				$prlist2.= "</tr>\n";
				//모델명/브랜드/제조사/원산지
				if($prlist2_production=="Y" || $prlist2_madein=="Y" || $prlist2_model=="Y" || $prlist2_brand=="Y") {
					$prlist2.="<tr>\n";
					$prlist2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"prproduction\">";
					if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
						unset($addspec);
						if($prlist2_production=="Y" && strlen($row->production)>0) {
							$addspec[]=$row->production;
						}
						if($prlist2_madein=="Y" && strlen($row->madein)>0) {
							$addspec[]=$row->madein;
						}
						if($prlist2_model=="Y" && strlen($row->model)>0) {
							$addspec[]=$row->model;
						}
						//if($prlist2_brand=="Y" && strlen($row->brand)>0) {
						//	$addspec[]=$row->brand;
						//}
						$prlist2.= implode("/", $addspec);
					}
					$prlist2.="	</td>\n";
					$prlist2.="</tr>\n";
				}
				if($prlist2_price=="Y" && $row->consumerprice>0) {	//소비자가
					$prlist2.="<tr>\n";
					$prlist2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <strike>".number_format($row->consumerprice)."</strike>원";
					$prlist2.="	</td>\n";
					$prlist2.="</tr>\n";
				}
				$prlist2.= "<tr>\n";
				$prlist2.= "	<td align=left valign=top style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$prlist2.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$prlist2.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $prlist2.= "(기본가)";
				} else {
					$prlist2.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (strlen($row->option_price)==0) $prlist2.= number_format($row->sellprice)."원";
					else $prlist2.= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
				}
				if ($row->quantity=="0") $prlist2.= soldout();
				$prlist2.= "	</td>\n";
				$prlist2.= "</tr>\n";
				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($prlist2_reserve=="Y" && $reserveconv>0) {	//적립금
					$prlist2.="<tr>\n";
					$prlist2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원";
					$prlist2.="	</td>\n";
					$prlist2.="</tr>\n";
				}
				//태그관련
				if($prlist2_tag>0 && strlen($row->tag)>0) {
					$prlist2.="	<tr>\n";
					$prlist2.="		<td align=left style=\"word-break:break-all;\" class=\"prtag\"><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=ereg_replace("(<|>)","",$arrtaglist[$ii]);
						if(strlen($arrtaglist[$ii])>0) {
							if($jj<$prlist2_tag) {
								if($jj>0) $prlist2.="<img width=2 height=0>+<img width=2 height=0>";
							} else {
								if($jj>0) $prlist2.="<img width=2 height=0>+<img width=2 height=0>";
								break;
							}
							$prlist2.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$arrtaglist[$ii]."</FONT></a>";
							$jj++;
						}
					}
					$prlist2.="		</td>\n";
					$prlist2.="	</tr>\n";
				}
				$prlist2.= "	</table>\n";
				$prlist2.= "	</td>\n";
				$prlist2.= "</tr>\n";
				$prlist2.= "</table>\n";
				$prlist2.= "</td>\n";

				$i++;

				if ($i%$prlist2_cols==0) {
					$prlist2.="</tr><tr><td colspan=".$prlist2_colnum." height=".$prlist2_gan."></td></tr>\n";
				}
			}
			if($i>0 && $i<$prlist2_cols) {
				for($k=0; $k<($prlist2_cols-$i); $k++) {
					$prlist2.="<td></td>\n<td></td>\n";
				}
			}
			$prlist2.= "</tr>\n";
			$prlist2.= "</table>\n";
		} else if($prlist_type=="3") {	####################################### 리스트형 ######################
			$colspan=4;
			$image_height=27;
			$prlist3 = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
			if($prlist3_image_yn=="Y") {
				$image_height=60;
				$prlist3.= "<col width=70></col>\n";
			} else {
				$prlist3.= "<col width=40></col>\n";
			}
			$prlist3.= "<col width=\"0\"></col>\n";
			$prlist3.= "<col width=></col>\n";
			if($prlist3_production=="Y" || $prlist3_madein=="Y" || $prlist3_model=="Y" || $prlist3_brand=="Y") {
				$colspan++;
				$prlist3.= "<col width=120></col>\n";
			}
			if($prlist3_price=="Y") {
				$colspan++;
				$prlist3.= "<col width=90></col>\n";
			}
			$prlist3.= "<col width=120></col>\n";
			if($prlist3_reserve=="Y") {
				$colspan++;
				$prlist3.= "<col width=70></col>\n";
			}
			while($row=mysql_fetch_object($result)) {
				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
				if($i>0) {
					$prlist3.="<tr><td colspan=".$colspan." ";
					if(eregi("#prlist_colline",$body)) {
						$prlist3.= "id=prlist_colline></td></tr>\n";
					} else {
						$prlist3.= "height=1><table border=0 cellpadding=0 cellspacing=0 height=1 style=\"table-layout:fixed\"><tr><td height=1 style=\"border:1 dotted #DDDDDD\"><img width=1 height=0></td></tr></table></td></tr>\n";
					}
				}
				$prlist3.= "<tr height=".$image_height." id=\"A".$row->productcode."\" onmouseover=\"quickfun_show(this,'A".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'A".$row->productcode."','none')\">\n";
				if($prlist3_image_yn!="Y") {
					$prlist3.= "	<td align=center>".$number."</td>\n";
				}
				if($prlist3_image_yn=="Y") {
					$prlist3.= "	<td align=center>";
					if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
						$prlist3.= "<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
						$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
						if ($width[0]>=$width[1] && $width[0]>=60) $prlist3.= "width=60 ";
						else if ($width[1]>=60) $prlist3.= "height=60 ";
					} else {
						$prlist3.= "<img src=\"".$Dir."images/no_img.gif\" height=60 border=0 align=center";
					}
					$prlist3.= "	></A></td>\n";
				}
				$prlist3.= "	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','A','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$prlist3.= "	<td style=\"padding-left:5\" style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";
				if ($row->quantity=="0") $prlist3.= soldout();
				//태그관련
				if($prlist3_tag>0 && strlen($row->tag)>0) {
					$prlist3.="<br><img src=\"".$Dir."images/common/tag_icon.gif\" border=0 align=absmiddle><img width=2 height=0>";
					$arrtaglist=explode(",",$row->tag);
					$jj=0;
					for($ii=0;$ii<count($arrtaglist);$ii++) {
						$arrtaglist[$ii]=ereg_replace("(<|>)","",$arrtaglist[$ii]);
						if(strlen($arrtaglist[$ii])>0) {
							if($jj<$prlist3_tag) {
								if($jj>0) $prlist3.="<img width=2 height=0><FONT class=\"prtag\">+</FONT><img width=2 height=0>";
							} else {
								if($jj>0) $prlist3.="<img width=2 height=0><FONT class=\"prtag\">+</FONT><img width=2 height=0>";
								break;
							}
							$prlist3.="<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($arrtaglist[$ii])."\" onmouseover=\"window.status='".$arrtaglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prtag\">".$arrtaglist[$ii]."</FONT></a>";
							$jj++;
						}
					}
				}
				$prlist3.= "</td>\n";
				//모델명/브랜드/제조사/원산지
				if($prlist3_production=="Y" || $prlist3_madein=="Y" || $prlist3_model=="Y" || $prlist3_brand=="Y") {
					$prlist3.="	<td align=center style=\"word-break:break-all;\" class=\"prproduction\">";
					if(strlen($row->production)>0 || strlen($row->madein)>0 || strlen($row->model)>0 || strlen($row->brand)>0) {
						unset($addspec);
						if($prlist3_production=="Y" && strlen($row->production)>0) {
							$addspec[]=$row->production;
						}
						if($prlist3_madein=="Y" && strlen($row->madein)>0) {
							$addspec[]=$row->madein;
						}
						if($prlist3_model=="Y" && strlen($row->model)>0) {
							$addspec[]=$row->model;
						}
						//if($prlist3_brand=="Y" && strlen($row->brand)>0) {
						//	$addspec[]=$row->brand;
						//}
						$prlist3.= implode("/", $addspec);
					}
					$prlist3.="	</td>\n";
				}
				if($prlist3_price=="Y") {
					$prlist3.= "	<td align=center style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <strike>".number_format($row->consumerprice)."</strike>원</td>\n";
				}
				$prlist3.= "	<td align=center style=\"word-break:break-all;\" class=\"prprice\">";
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$prlist3.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$prlist3.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					if (strlen($row->option_price)!=0) $prlist3.= "(기본가)";
				} else {
					$prlist3.="<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (strlen($row->option_price)==0) $prlist3.= number_format($row->sellprice)."원";
					else $prlist3.= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
				}
				$prlist3.= "	</td>\n";
				if($prlist3_reserve=="Y") {
					$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
					$prlist3.= "	<td align=center style=\"word-break:break-all;\" class=prreserve><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원</td>\n";
				}
				$prlist3.= "</tr>\n";
				$i++;
			}
			$prlist3.= "</table>\n";
		} else if($prlist_type=="4") {	####################################### 공구형 #########################
			$prlist4 = "<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
			
			$prlist4.= "<tr>\n";
			while($row=mysql_fetch_object($result)) {
				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
				$prlist4.="<td align=center width=\"".(100/$prlist4_cols)."%\">\n";
				$prlist4.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"table-layout:fixed\" id=\"A".$row->productcode."\" onmouseover=\"quickfun_show(this,'A".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'A".$row->productcode."','none')\">\n";
				$prlist4.="<col width=100></col>\n";
				$prlist4.="<col width=></col>\n";
				$prlist4.="<tr>\n";
				$prlist4.="	<td height=\"35\" colspan=\"2\" style=\"padding-top:5\"><div style=\"padding-left:15px;white-space:nowrap;width:210px;overflow:hidden;text-overflow:ellipsis;\"><a href='".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."' onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><font color=\"#000000\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><b>".$row->productname."</b></fon></a></div></td>\n";
				$prlist4.="</tr>\n";
				$prlist4.="<tr>\n";
				$prlist4.="	<td align=center valign=\"top\">\n";
				$prlist4.="	<table width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" style=\"table-layout:fixed\">\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td align=\"center\" valign=\"middle\">\n";
				$prlist4.="		<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
				if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
					$prlist4.="<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
					$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
					if(($width[0]>80 || $width[1]>80) && $width[0]>$width[1]) {
						$prlist4.=" width=80";
					} else if($width[0]>80 || $width[1]>80) {
						$prlist4.=" height=80";
					}
				} else {
					$prlist4.="<img src=\"".$Dir."images/no_img.gif\" border=0 align=center width=80 height=80";
				}
				$prlist4.="	></A></td>\n";
				$prlist4.="	</tr>\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','A','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td>";
				$prlist4.="	</tr>\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td align=\"center\"><a href='".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."' onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><IMG SRC=\"".$Dir."images/common/btn_detail.gif\" border=\"0\"></a></td>\n";
				$prlist4.="	</tr>\n";
				$prlist4.="	</table>\n";
				$prlist4.="	</td>\n";
				$prlist4.="	<td valign=\"top\">\n";
				$prlist4.="	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
				$prlist4.="	<col width=52></col>\n";
				$prlist4.="	<col width=5></col>\n";
				$prlist4.="	<col width=></col>\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td style=\"font-size:11\">\n";
				$prlist4.="			<img src=\"".$Dir."images/common/cat_graybullet.gif\" border=\"0\" align=\"absmiddle\"> 시중가";
				$prlist4.="		</td>\n";
				$prlist4.="		<td>: </td>\n";
				$prlist4.="		<td align=\"right\" style=\"font-size:11\"> <s>".number_format($row->consumerprice)."원</s></td>\n";					
				$prlist4.="	</tr>\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td style=\"font-size:11\">\n";
				$prlist4.="			<img src=\"".$Dir."images/common/cat_graybullet.gif\" border=\"0\" align=\"absmiddle\"> 현재가"; 
				$prlist4.="		</td>\n";
				$prlist4.="		<td>: </td>\n";
				$prlist4.="		<td align=\"right\" style=\"font-size:11;color:#FE7F00\">".number_format($row->sellprice)."원</td>\n";
				$prlist4.="	</tr>\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td style=\"font-size:11\">\n";
				$prlist4.="			<img src=\"".$Dir."images/common/cat_graybullet.gif\" border=\"0\" align=\"absmiddle\"> 남은수량"; 
				$prlist4.="		</td>\n";
				$prlist4.="		<td>: </td>\n";
				$prlist4.="		<td align=\"right\" style=\"font-size:11\">\n";
				if(strlen($row->quantity)==0 || $row->quantity==NULL) {
					$prlist4.="무제한";
				} else {
					$prlist4.=$row->quantity."개";
				}
				$prlist4.="		</td>\n";
				$prlist4.="	</tr>\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td height=\"13\" colspan=\"3\"></td>\n";
				$prlist4.="	</tr>\n";
				$prlist4.="	</table>\n";
				$prlist4.="	<table width=\"102\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" background=\"".$Dir."images/common/list_box.gif\">\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td width=\"102\" height=\"52\" background=\"<?=$Dir?>images/common/product/<?=$_cdata->list_type?>/plist_skin_listbox.gif\">\n";
				$prlist4.="		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$prlist4.="		<tr align=\"center\">\n";
				$prlist4.="			<td width=\"43\" height=\"40\" align=\"center\" valign=\"top\" style=\"color:#696969;font-size:11px;\">".number_format($row->consumerprice)."</td>\n";
				$prlist4.="			<td width=\"43\" valign=\"middle\" style=\"color:#FE7F00;font-size:11px;\">".number_format($row->sellprice)."</td>\n";
				$prlist4.="		</tr>\n";
				$prlist4.="		</table>\n";
				$prlist4.="		</td>\n";
				$prlist4.="	</tr>\n";
				$prlist4.="	<tr>\n";
				$prlist4.="		<td width=\"102\">\n";
				$prlist4.="		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				$prlist4.="		<tr>\n";
				$prlist4.="			<td width=\"43\" align=\"right\" style=\"font-size:11px;\">시작가</td>\n";
				$prlist4.="			<td width=\"43\" align=\"right\" style=\"font-size:11px;\">공구가</td>\n";
				$prlist4.="		</tr>\n";
				$prlist4.="		</table>\n";
				$prlist4.="		</td>\n";
				$prlist4.="	</tr>\n";
				$prlist4.="	</table>\n";
				$prlist4.="	</td>\n";
				$prlist4.="</tr>\n";
				$prlist4.="</table>\n";
				$prlist4.="</td>\n";

				$i++;

				if ($i%$prlist4_cols==0) {
					$prlist4.="</tr><tr><td colspan=".$prlist4_colnum." height=".$prlist4_gan."></td></tr><tr>\n";
				}
			}
			if($i>0 && $i<$prlist4_cols) {
				for($k=0; $k<($prlist4_cols-$i); $k++) {
					$prlist4.="<td></td>\n";
				}
			}
			$prlist4.= "</tr>\n";
			$prlist4.= "</table>\n";
		}
		mysql_free_result($result);

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
		$list_page=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
	}
}
?>