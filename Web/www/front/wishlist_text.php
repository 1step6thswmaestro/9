<?
if(substr(getenv("SCRIPT_NAME"),-18)=="/wishlist_text.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}

$checkall="\"javascript:CheckBoxAll()\"";
$checkdel="\"javascript:GoDelete()\"";

$selsort ="<select name=sort onchange=\"ChangeSort(this.value)\"";
if(strlen($sort_style)>0) $selsort.=" style=\"".$sort_style."\"";
$selsort.=">\n";
$selsort.="<option value=\"date_desc\"";
if($sort=="date_desc") $selsort.=" selected";
$selsort.=">최근등록순</option>\n";
$selsort.="<option value=\"marks_desc\"";
if($sort=="marks_desc") $selsort.=" selected";
$selsort.=">구매우선순위순</option>\n";
$selsort.="<option value=\"price_desc\"";
if($sort=="price_desc") $selsort.=" selected";
$selsort.=">높은가격순</option>\n";
$selsort.="<option value=\"price\"";
if($sort=="price") $selsort.=" selected";
$selsort.=">낮은가격순</option>\n";
$selsort.="<option value=\"name\"";
if($sort=="name") $selsort.=" selected";
$selsort.=">상품명순</option>\n";
$selsort.="</select>\n";

$sellistnum ="<select name=listnum onchange=\"ChangeListnum(this.value)\"";
if(strlen($listnum_style)>0) $sellistnum.=" style=\"".$listnum_style."\"";
$sellistnum.=">\n";
$sellistnum.="<option value=\"10\"";
if($listnum==10) $sellistnum.=" selected";
$sellistnum.=">10개씩 정렬</option>\n";
$sellistnum.="<option value=\"20\"";
if($listnum==20) $sellistnum.=" selected";
$sellistnum.=">20개씩 정렬</option>\n";
$sellistnum.="<option value=\"30\"";
if($listnum==30) $sellistnum.=" selected";
$sellistnum.=">30개씩 정렬</option>\n";
$sellistnum.="<option value=\"40\"";
if($listnum==40) $sellistnum.=" selected";
$sellistnum.=">40개씩 정렬</option>\n";
$sellistnum.="<option value=\"50\"";
if($listnum==50) $sellistnum.=" selected";
$sellistnum.=">50개씩 정렬</option>\n";
$sellistnum.="</select>\n";


$qry = "WHERE a.id='".$_ShopInfo->getMemid()."' ";
$qry.= "AND a.productcode=b.productcode AND b.display='Y' ";
$qry.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";

$sql = "SELECT COUNT(*) as t_count ";
$sql.= "FROM tblwishlist a, tblproduct b ";
$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
$sql.= $qry;
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$t_count = (int)$row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

$total=$t_count;


$tmp_sort=explode("_",$sort);
$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,b.productcode,b.productname,b.sellprice,b.sellprice as realprice, ";
$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
$sql.= "b.etctype,a.wish_idx,a.marks,a.memo,b.selfcode,b.assembleuse,b.package_num ";
$sql.= "FROM tblwishlist a, tblproduct b ";
$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
$sql.= $qry." ";
if($tmp_sort[0]=="date") $sql.= "ORDER BY a.date ".$tmp_sort[1]." ";
else if($tmp_sort[0]=="marks") $sql.= "ORDER BY a.marks ".$tmp_sort[1]." ";
else if($tmp_sort[0]=="price") $sql.= "ORDER BY b.sellprice ".$tmp_sort[1]." ";
else if($tmp_sort[0]=="name") $sql.= "ORDER BY b.productname ".$tmp_sort[1]." ";
else $sql.= "ORDER BY a.date DESC ";
$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
$result=mysql_query($sql,get_db_conn());
$cnt=0;
while($row=mysql_fetch_object($result)) {
	$row->quantity=1;

	if(ereg("^(\[OPTG)([0-9]{4})(\])$",$row->option1)) {
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
						$delsql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
						$delsql.= "AND productcode='".$row->productcode."' ";
						$delsql.= "AND opt1_idx='".$row->opt1_idx."' AND opt2_idx='".$row->opt2_idx."' ";
						$delsql.= "AND optidxs='".$row->optidxs."' ";
						mysql_query($delsql,get_db_conn());
					}
					if($exoptcode[$opti]>0){
						$opval = str_replace('"','',explode("",$optionadd[$opti]));
						$optvalue.= ", ".$opval[0]." : ";
						$exop = str_replace('"','',explode(",",$opval[$exoptcode[$opti]]));
						if ($exop[1]>0) $optvalue.=$exop[0]."(<font color=#FF3C00>+".$exop[1]."원</font>)";
						else if($exop[1]==0) $optvalue.=$exop[0];
						else $optvalue.=$exop[0]."(<font color=#FF3C00>".$exop[1]."원</font>)";
						$row->realprice+=($row->quantity*$exop[1]);
					}
					$opti++;
				}
				$optvalue = substr($optvalue,1);
			}
		}
	} else {
		$optvalue="";
	}

	$tempwish.=$mainwish;

	//######### 옵션에 따른 가격 변동 체크 ###############
	if (strlen($row->option_price)==0) {
		$price = $row->realprice;
		$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"N");
		$sellprice=$row->sellprice;
	} else if (strlen($row->opt1_idx)>0) {
		$option_price = $row->option_price;
		$pricetok=explode(",",$option_price);
		$priceindex = count($pricetok);
		$price = $pricetok[$row->opt1_idx-1]*$row->quantity;
		$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$pricetok[$row->opt1_idx-1],"N");
		$sellprice=$pricetok[$row->opt1_idx-1];
	}
	//######### 옵션에 따른 가격 변동 체크 끝 ############

	//######## 특수값체크 : 현금결제상품//무이자상품 #####
	$bankonly_html = ""; $setquota_html = "";
	if (strlen($row->etctype)>0) {
		$etctemp = explode("",$row->etctype);
		for ($i=0;$i<count($etctemp);$i++) {
			switch ($etctemp[$i]) {
				case "BANKONLY": $bankonly = "Y";
					$bankonly_html = " <img src=".$Dir."images/common/bankonly.gif> ";
					break;
				case "SETQUOTA":
					if ($_data->card_splittype=="O" && $price>=$_data->card_splitprice) {
						$setquotacnt++;
						$setquota_html = " <img src=".$Dir."images/common/setquota.gif>";
						$setquota_html.= "</b><font color=black size=1>(";
						//if ($card_type=="IN" || $card_type=="BO") $setquota_html.="2~";
						//else                  $setquota_html.="3~";
						$setquota_html.="3~";
						$setquota_html.= $_data->card_splittype.")</font>";
					}
					break;
			}
		}
	} // $row_count 값과 setquotacnt값이 같으면 무이자결제가능하게 데이터를 보낸다.

	$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);

	$wish_checkbox="<input type=checkbox name=sels[] value=\"".$row->wish_idx."\" style=\"border:none;\">";
	$wish_primg ="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
	if(strlen($row->tinyimage)!=0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)){
		$file_size=getImageSize($Dir.DataDir."shopimages/product/".$row->tinyimage);
		$wish_primg.="<img src=\"".$Dir.DataDir."shopimages/product/".$row->tinyimage."\"";
		if($file_size[0]>=$file_size[1]) $wish_primg.=" width=60";
		else $wish_primg.=" height=60";
		$wish_primg.=" border=0>";
	} else {
		$wish_primg.="<img src=\"".$Dir."images/no_img.gif\" width=60 border=0>";
	}
	$wish_primg.="</a>";

	$wish_prname = "<a href=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\"><font color=#373737><b>".viewproductname($row->productname,$row->etctype,$row->selfcode)."</b>".$bankonly_html.$setquota_html."";

	if(strlen($row->addcode)==0) $wish_addcode1="";
	else $wish_addcode1="-".$row->addcode;
	$wish_addcode2=$row->addcode;

	$wish_reserve=number_format($tempreserve);
	$wish_price=number_format($price);

	$wish_option="";
	$tempoption="";
	if (strlen($row->option1)>0 || strlen($row->option2)>0 || strlen($optvalue)>0) {
		// ###### 특성 #########
		if (strlen($row->option1)>0 && $row->opt1_idx>0) {
			$temp = $row->option1;
			$tok = explode(",",$temp);
			$count=count($tok);
			$wish_option.=$tok[0]." : ".$tok[$row->opt1_idx]."\n";
		} 
		if (strlen($row->option2)>0 && $row->opt2_idx>0) {
			$temp = $row->option2;
			$tok = explode(",",$temp);
			$count=count($tok);
			$wish_option.=",&nbsp; ".$tok[0]." : ".$tok[$row->opt2_idx]."\n";
		}
		if(strlen($optvalue)>0) {
			$wish_option.=$optvalue."\n";
		}
		$tempoption=$optionwish;
		$tempoption=ereg_replace("\[WISH_OPTION\]",$wish_option,$tempoption);
	}

	$wish_basket="\"javascript:CheckForm('',".$row->wish_idx.")\"";
	$wish_baro="\"javascript:CheckForm('ordernow',".$row->wish_idx.")\"";

	$wish_marks ="<select name=up_marks_".$row->wish_idx." ";
	if(strlen($marks_style)>0) $wish_marks.=" style=\"".$marks_style."\"";
	$wish_marks.=">\n";

	$marks_0=$marks_1=$marks_2=$marks_3=$marks_4=$marks_5="";
	${"marks_".$row->marks}="selected";

	$wish_marks.="<option value=0 ".$marks_0.">구매우선순위</option>\n";
	$wish_marks.="<option value=1 ".$marks_1.">★</option>\n";
	$wish_marks.="<option value=2 ".$marks_2.">★★</option>\n";
	$wish_marks.="<option value=3 ".$marks_3.">★★★</option>\n";
	$wish_marks.="<option value=4 ".$marks_4.">★★★★</option>\n";
	$wish_marks.="<option value=5 ".$marks_5.">★★★★★★</option>\n";
	$wish_marks.="</select>\n";

	$wish_memotxt ="<input type=text name=up_memo_".$row->wish_idx." value=\"".$row->memo."\" size=60 maxlength=100";
	if(strlen($memo_style)>0) $wish_memotxt.=" style=\"".$memo_style."\"";
	$wish_memotxt.=">\n";

	$wish_memosave="\"javascript:SaveMemo(".$row->wish_idx.")\"";

	$miniq = 1; 
	if (strlen($row->etctype)>0) {
		$etctemp = explode("",$row->etctype);
		for ($i=0;$i<count($etctemp);$i++) {
			if (substr($etctemp[$i],0,6)=="MINIQ=") $miniq=substr($etctemp[$i],6);  // 최소주문수량
		}
	}
	$wish_start="";
	$wish_end ="<input type=hidden name=productcode_".$row->wish_idx." value=\"".$row->productcode."\">\n";
	$wish_end.="<input type=hidden name=option1_".$row->wish_idx." value=\"".$row->opt1_idx."\">\n";
	$wish_end.="<input type=hidden name=option2_".$row->wish_idx." value=\"".$row->opt2_idx."\">\n";
	$wish_end.="<input type=hidden name=opts_".$row->wish_idx." value=\"".$row->optidxs."\">\n";
	$wish_end.="<input type=hidden name=quantity_".$row->wish_idx." value=\"".$miniq."\">\n";
	$wish_end.="<input type=hidden name=assembleuse_".$row->wish_idx." value=\"".$row->assembleuse."\">\n";
	$wish_end.="<input type=hidden name=packagenum_".$row->wish_idx." value=\"".((int)$row->package_num>0?$row->package_num:"")."\">\n";
	$cnt++;

	$pattern=array("(\[WISH_CHECKBOX\])","(\[WISH_PRIMG\])","(\[WISH_PRNAME\])","(\[WISH_ADDCODE1\])","(\[WISH_ADDCODE2\])","(\[WISH_RESERVE\])","(\[WISH_PRICE\])","(\[WISH_BASKET\])","(\[WISH_BARO\])","(\[WISH_MARKS((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])","(\[WISH_MEMOTXT((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])","(\[WISH_MEMOSAVE\])","(\[FORWISH\])","(\[FORENDWISH\])","(\[OPTIONVALUE\])");
	$replace=array($wish_checkbox,$wish_primg,$wish_prname,$wish_addcode1,$wish_addcode2,$wish_reserve,$wish_price,$wish_basket,$wish_baro,$wish_marks,$wish_memotxt,$wish_memosave,$wish_start,$wish_end,$tempoption);

	$tempwish=preg_replace($pattern,$replace,$tempwish);
}
mysql_free_result($result);

if($cnt>0) {
	$originalwish=$ifwish;
	$pattern=array("(\[WISHVALUE\])");
	$replace=array($tempwish);
	$originalwish=preg_replace($pattern,$replace,$originalwish);
} else {
	$originalwish=$nowish;
}

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


$page=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;

?>