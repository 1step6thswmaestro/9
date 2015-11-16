<?
$page="";
$taglink="\"javascript:void(0)\" onclick=\"tagCls.tagList()\"";

if($num=strpos($body,"[TAGSEARCHINPUT_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$input_style=$s_tmp[1];
}
if(strlen($input_style)==0) $input_style="width:300px";
$tagsearchinput = "<input type=text name=searchtagname value=\"".$tagname."\" style=\"".$input_style."\" maxlength=50 onkeydown=\"CheckKeyTagSearch()\" onkeyup=\"check_tagvalidate(event, this);\">";

$tagsearchok="\"javascript:void(0)\" onclick=\"tagCls.searchProc()\"";
$tagkeyword=$tagname;
$tagtotal=$t_count;

if(strpos($body,"[IFTAG]")!=0) {
	$iftagnum=strpos($body,"[IFTAG]");
	$endtagnum=strpos($body,"[IFENDTAG]");
	$elsetagnum=strpos($body,"[IFELSETAG]");

	$tagstartnum=strpos($body,"[FORTAG]");
	$tagstopnum=strpos($body,"[FORENDTAG]");

	$prtagstartnum=strpos($body,"[FORPRTAG_");
	$prtagstopnum=strpos($body,"[FORENDPRTAG]");

	$match=array();
	$prtagnum=3;
	if (preg_match("/\[FORPRTAG_([0-9]{1})\]/",$body,$match)) {
		$prtagnum=$match[1];
	}
	$iftag=substr($body,$iftagnum+7,$tagstartnum-($iftagnum+7))."[TAGVALUE]".substr($body,$tagstopnum+11,$elsetagnum-($tagstopnum+11));

	$notag=substr($body,$elsetagnum+11,$endtagnum-$elsetagnum-11);

	$maintag=substr($body,$tagstartnum,$prtagstartnum-$tagstartnum)."[PRTAGVALUE]".substr($body,$prtagstopnum+13,$tagstopnum-$prtagstopnum+1);

	$prtag=substr($body,$prtagstartnum+12,$prtagstopnum-$prtagstartnum-12);

	$tagetcstartnum=strpos($body,"[IFETC]");
	$tagetcstopnum=strpos($body,"[ENDETC]");
	$tagetc=substr($body,$tagetcstartnum+7,$tagetcstopnum-$tagetcstartnum-7);
	$prtag=substr($body,$prtagstartnum+12,$tagetcstartnum-$prtagstartnum-12);

	$body=substr($body,0,$iftagnum)."[ORIGINALTAG]".substr($body,$endtagnum+10);
}

if($t_count>0) {
	$sql = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.quantity, a.reserve, a.reservetype, a.production, ";
	$sql.= "a.addcode,a.tinyimage,a.date,a.etctype,a.option1,a.option2,a.option_price,a.tag,a.tagcount,a.selfcode ";
	$sql.= "FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= $qry." ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	$sql.= "ORDER BY a.tagcount DESC ";
	$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
	$result=mysql_query($sql,get_db_conn());
	$i=0;
	while($row=mysql_fetch_object($result)) {
		$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
		$tag_primg=$tag_prname=$tag_addcode=$tag_prtitle=$tag_consumprice=$tag_sellprice=$tag_reserve="";

		if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
			$tag_primg.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
			$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
			if ($width[1]>90) $tag_primg.="height=90 ";
		} else {
			$tag_primg.="<img src=\"".$Dir."images/no_img.gif\" border=0 align=center";
		}
		$tag_primg.="	></A>";

		$tag_prname="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";

		$tag_addcode="<font class=praddcode>".$row->addcode."</font>";

		$tag_prtitle="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\">";
		if(strlen($row->addcode)>0) $tag_prtitle.="<font class=praddcode>[".$row->addcode."]</font><br>";
		$tag_prtitle.="<FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>";

		$tag_consumprice=number_format($row->consumerprice);
		$tag_sellprice=number_format($row->sellprice);
		$tag_reserve=number_format(getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y"));

		$tag_production="<font class=prproduction>".$row->production."</font>";
		$tag_madein="<font class=prproduction>".$row->madein."</font>";
		$tag_model="<font class=prproduction>".$row->model."</font>";
		//$tag_brand="<font class=prproduction>".$row->brand."</font>";

		$wish_script="PrdtQuickCls.quickFun('".$row->productcode."','1')";

		if($row->quantity=="0")
			$basket_script="alert('재고가 없습니다.');";
		else
			$basket_script="PrdtQuickCls.quickFun('".$row->productcode."','2')";

		$tag_quickview="\"javascript:void(0)\" onclick=\"PrdtQuickCls.quickView('".$row->productcode."')\"";
		$tag_wish="\"javascript:void(0)\" onclick=\"".$wish_script."\"";
		$tag_basket="\"javascript:void(0)\" onclick=\"".$basket_script."\"";

		$tag_count=$row->tagcount;

		$tempmaintag.=$maintag;

		$prtagvalue="";
		$arrtag=explode(",",$row->tag);
		$jj=0;
		for($ii=0;$ii<count($arrtag);$ii++) {
			$temptag=ereg_replace("<|>","",$arrtag[$ii]);
			if(strlen($temptag)>0) {
				$jj++;
				$temptagname="<A HREF=\"javascript:void(0)\" onclick=\"tagCls.tagSearch('".$temptag."')\" onmouseover=\"window.status='".$temptag."';return true;\" onmouseout=\"window.status='';return true;\">".$temptag."</A>";
				if($jj>1) {
					$prtagvalue.=$tagetc;
				}
				$pattern=array("(\[TAGNAME\])");
				$replace=array($temptagname);
				$prtagvalue.=preg_replace($pattern,$replace,$prtag);

				if($jj>=$prtagnum) break;
			}
		}

		$pattern=array("(\[FORTAG\])","(\[FORENDTAG\])","(\[TAG_PRIMG\])","(\[TAG_PRNAME\])","(\[TAG_ADDCODE\])","(\[TAG_PRTITLE\])","(\[TAG_CONSUMPRICE\])","(\[TAG_SELLPRICE\])","(\[TAG_RESERVE\])","(\[TAG_COUNT\])","(\[TAG_QUICKVIEW\])","(\[TAG_WISH\])","(\[TAG_BASKET\])","(\[PRTAGVALUE\])","(\[TAG_PRODUCTION\])","(\[TAG_MADEIN\])","(\[TAG_MODEL\])","(\[TAG_BRAND\])");
		$replace=array("","",$tag_primg,$tag_prname,$tag_addcode,$tag_prtitle,$tag_consumprice,$tag_sellprice,$tag_reserve,$tag_count,$tag_quickview,$tag_wish,$tag_basket,$prtagvalue,$tag_production,$tag_madein,$tag_model,$tag_brand);
		$tempmaintag=preg_replace($pattern,$replace,$tempmaintag);

		$i++;
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
	$page=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;

	$originaltag=$iftag;
	$pattern=array("(\[TAGVALUE\])","(\[TAGKEYWORD\])","(\[TAGTOTAL\])","(\[PAGE\])");
	$replace=array($tempmaintag,$tagkeyword,$tagtotal,$page);
	$originaltag=preg_replace($pattern,$replace,$originaltag);
} else {
	$originaltag=$notag;
	$pattern=array("(\[TAGKEYWORD\])");
	$replace=array($tagkeyword);
	$originaltag=preg_replace($pattern,$replace,$originaltag);
}

$pattern=array("(\[TAGLINK\])","(\[TAGSEARCHINPUT((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])","(\[TAGSEARCHOK\])","(\[TAGKEYWORD\])","(\[TAGTOTAL\])","(\[PAGE\])","(\[ORIGINALTAG\])");
$replace=array($taglink,$tagsearchinput,$tagsearchok,$tagkeyword,$tagtotal,$page,$originaltag);
$body=preg_replace($pattern,$replace,$body);

echo $body;
?>