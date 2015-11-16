<?
//상품리스트 (리스트,이미지,공구형)
$prlist_type="";
if($num=strpos($body,"[PRLIST")) {
	$prlist_type=substr($body,$num+7,1);
	if($prlist_type=="1") {	//이미지A형
		$prlist_type="";
		$match=array();
		$default_prlist1=array("5","2","N","Y","N","N","0","N","N","N","N");
		if (preg_match("/\[PRLIST1([0-9LNY_]{2,14})\]/",$body,$match)) {
			$match_array=explode("_",$match[1]);
			for ($i=0;$i<strlen($match_array[0]);$i++) {
				$default_prlist1[$i]=$match_array[0][$i];
			}
			$prlist_type="1";
		}

		$prlist1_cols=(int)$default_prlist1[0];
		$prlist1_rows=(int)$default_prlist1[1];
		$prlist1_rowline=$default_prlist1[2];		// 상품세로라인여부
		$prlist1_colline=$default_prlist1[3];		// 상품가로라인여부
		$prlist1_price=$default_prlist1[4];			// 소비자가 표시여부
		$prlist1_reserve=$default_prlist1[5];		// 적립금 표시여부
		$prlist1_tag=(int)$default_prlist1[6];		// 태그 표시갯수(0-9) 0일 경우 표시안함
		$prlist1_production=$default_prlist1[7];	// 제조사 표시여부
		$prlist1_madein=$default_prlist1[8];	// 원산지 표시여부
		$prlist1_model=$default_prlist1[9];	// 모델명 표시여부
		$prlist1_brand=$default_prlist1[10];	// 브랜드 표시여부
		if($prlist1_cols==0 || $prlist1_cols==9) $prlist1_cols=5;
		if($prlist1_rows==0 || $prlist1_rows==9) $prlist1_rows=2;
		$prlist1_gan=(($match_array[1]+0)>99)?"99":($match_array[1]+0);
		if($prlist1_gan==0) $prlist1_gan=5;

		$prlist1_colnum=$prlist1_cols*2-1;
		$prlist1_product_num=$prlist1_cols*$prlist1_rows;
		if($prlist1_cols==6)		$prlist1_imgsize=$_data->primg_minisize-5;
		else if($prlist1_cols==7)	$prlist1_imgsize=$_data->primg_minisize-10;
		else if($prlist1_cols==8)	$prlist1_imgsize=$_data->primg_minisize-20;
		else						$prlist1_imgsize=$_data->primg_minisize;

		if($_REQUEST["listnum"]){
			$listnum=(int)$_REQUEST["listnum"];
			if($listnum<=0) $listnum=$_data->prlist_num;

			//리스트 세팅
			$setup[list_num] = $listnum;
		}else{
			//리스트 세팅
			$setup[list_num]=$prlist1_product_num;
		}
		//$setup[list_num]=$prlist1_product_num;

	} else if($prlist_type=="2") {	//이미지B형
		$prlist_type="";
		$match=array();
		$default_prlist2=array("2","5","N","Y","N","N","0","N","N","N","N");
		if (preg_match("/\[PRLIST2([0-9LNY_]{2,14})\]/",$body,$match)) {
			$match_array=explode("_",$match[1]);
			for ($i=0;$i<strlen($match_array[0]);$i++) {
				$default_prlist2[$i]=$match_array[0][$i];
			}
			$prlist_type="2";
		}

		$prlist2_cols=(int)$default_prlist2[0];
		$prlist2_rows=(int)$default_prlist2[1];
		$prlist2_rowline=$default_prlist2[2];		// 상품세로라인여부
		$prlist2_colline=$default_prlist2[3];		// 상품가로라인여부
		$prlist2_price=$default_prlist2[4];			// 소비자가 표시여부
		$prlist2_reserve=$default_prlist2[5];		// 적립금 표시여부
		$prlist2_tag=(int)$default_prlist2[6];		// 태그 표시갯수(0-9) 0일 경우 표시안함
		$prlist2_production=$default_prlist2[7];	// 제조사 표시여부
		$prlist2_madein=$default_prlist2[8];	// 원산지 표시여부
		$prlist2_model=$default_prlist2[9];	// 모델명 표시여부
		$prlist2_brand=$default_prlist2[10];	// 브랜드 표시여부
		if($prlist2_cols==0 || $prlist2_cols==9) $prlist2_cols=5;
		if($prlist2_rows==0 || $prlist2_rows==9) $prlist2_rows=2;
		$prlist2_gan=(($match_array[1]+0)>99)?"99":($match_array[1]+0);
		if($prlist2_gan==0) $prlist2_gan=5;

		$prlist2_colnum=$prlist2_cols*2-1;
		$prlist2_product_num=$prlist2_cols*$prlist2_rows;
		if($prlist2_cols==6)		$prlist2_imgsize=$_data->primg_minisize-5;
		else if($prlist2_cols==7)	$prlist2_imgsize=$_data->primg_minisize-10;
		else if($prlist2_cols==8)	$prlist2_imgsize=$_data->primg_minisize-20;
		else						$prlist2_imgsize=$_data->primg_minisize;

		if($_REQUEST["listnum"]){
			$listnum=(int)$_REQUEST["listnum"];
			if($listnum<=0) $listnum=$_data->prlist_num;

			//리스트 세팅
			$setup[list_num] = $listnum;
		}else{
			//리스트 세팅
			$setup[list_num]=$prlist2_product_num;
		}
		//$setup[list_num]=$prlist2_product_num;

	} else if($prlist_type=="3") {	//리스트형
		$prlist_type="";
		$match=array();
		$default_prlist3=array("15","Y","N","Y","Y","0","N","N","N");
		if (preg_match("/\[PRLIST3([0-9NY]{2,8})\]/",$body,$match)) {
			$ii=0;
			for ($i=0;$i<strlen($match[1]);$i++) {
				if($i==0) {
					$default_prlist3[$ii]=$match[1][$i++].$match[1][$i];
				} else {
					$default_prlist3[$ii]=$match[1][$i];
				}
				$ii++;
			}
			$prlist_type="3";
		}

		$prlist3_product_num=(int)$default_prlist3[0];
		$prlist3_image_yn=$default_prlist3[1];
		$prlist3_production=$default_prlist3[2];	// 제조사 표시여부
		$prlist3_price=$default_prlist3[3];			// 소비자가 표시여부
		$prlist3_reserve=$default_prlist3[4];		// 적립금 표시여부
		$prlist3_tag=(int)$default_prlist3[5];		// 태그 표시갯수(0-9) 0일 경우 표시안함
		$prlist3_madein=$default_prlist3[6];	// 원산지 표시여부
		$prlist3_model=$default_prlist3[7];	// 모델명 표시여부
		$prlist3_brand=$default_prlist3[8];	// 브랜드 표시여부
		if($prlist3_product_num<10 || $prlist3_product_num>50) $prlist3_product_num=15;

		$setup[list_num]=$prlist3_product_num;
	} else if($prlist_type=="4") {	//공동구매형
		$prlist_type="";
		if (preg_match("/\[PRLIST4([1-8]{2}(\_){0,1}[0-9]{0,2})\]/",$body,$match)) {
			$prlist_type="4";
			$match_array=explode("_",$match[1]);
			$prlist4_cols=(int)$match_array[0][0];
			$prlist4_rows=(int)$match_array[0][1];

			if($prlist4_cols==0 || $prlist4_cols==9) $prlist4_cols=3;
			if($prlist4_rows==0 || $prlist4_rows==9) $prlist4_rows=3;
			$prlist4_colnum=$prlist4_cols*2-1;
			$prlist4_product_num=$prlist4_cols*$prlist4_rows;
			$prlist4_gan=(($match_array[1]+0)>99)?"99":($match_array[1]+0);
			if($prlist4_gan==0) $prlist4_gan=5;
		}
		$setup[list_num]=$prlist4_product_num;
	}
}

$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

if($num=strpos($body,"[CODEA_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$codeA_style=$s_tmp[1];
}
if($num=strpos($body,"[CODEB_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$codeB_style=$s_tmp[1];
}
if($num=strpos($body,"[CODEC_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$codeC_style=$s_tmp[1];
}
if($num=strpos($body,"[CODED_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$codeD_style=$s_tmp[1];
}
if($num=strpos($body,"[MINPRICE_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$minprice_style=$s_tmp[1];
}
if($num=strpos($body,"[MAXPRICE_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$maxprice_style=$s_tmp[1];
}
if($num=strpos($body,"[SCHECK_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$scheck_style=$s_tmp[1];
}
if($num=strpos($body,"[SCHECK1_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$scheck_style1=$s_tmp[1];
}
if($num=strpos($body,"[SCHECK2_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$scheck_style2=$s_tmp[1];
}
if($num=strpos($body,"[KEYWORD_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$keyword_style=$s_tmp[1];
}
if($num=strpos($body,"[KEYWORD1_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$keyword_style1=$s_tmp[1];
}
if($num=strpos($body,"[KEYWORD2_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$keyword_style2=$s_tmp[1];
}

if(strlen($codeA_style)==0) $codeA_style="width:170px";
if(strlen($codeB_style)==0) $codeB_style="width:170px";
if(strlen($codeC_style)==0) $codeC_style="width:170px";
if(strlen($codeD_style)==0) $codeD_style="width:170px";
if(strlen($minprice_style)==0) $minprice_style="width:143px";
if(strlen($maxprice_style)==0) $maxprice_style="width:143px";
if(strlen($scheck_style)==0) $scheck_style="width:90px";
if(strlen($keyword_style)==0) $keyword_style="width:209px";

$codeA_select ="<select name=codeA style=\"".$codeA_style."\" onchange=\"SearchChangeCate(this,1)\">\n";
$codeA_select.="<option value=\"\">--- 1차 카테고리 선택 ---</option>\n";
$codeA_select.="</select>\n";

$codeB_select ="<select name=codeB style=\"".$codeB_style."\" onchange=\"SearchChangeCate(this,2)\">\n";
$codeB_select.="<option value=\"\">--- 2차 카테고리 선택 ---</option>\n";
$codeB_select.="</select>\n";

$codeC_select ="<select name=codeC style=\"".$codeC_style."\" onchange=\"SearchChangeCate(this,3)\">\n";
$codeC_select.="<option value=\"\">--- 3차 카테고리 선택 ---</option>\n";
$codeC_select.="</select>\n";

$codeD_select ="<select name=codeD style=\"".$codeD_style."\">\n";
$codeD_select.="<option value=\"\">--- 4차 카테고리 선택 ---</option>\n";
$codeD_select.="</select>\n";

$txt_minprice = "<input type=text name=minprice value=\"".$minprice."\" style=\"".$minprice_style."\" onkeyup=\"strnumkeyup(this)\">";
$txt_maxprice = "<input type=text name=maxprice value=\"".$maxprice."\" style=\"".$maxprice_style."\" onkeyup=\"strnumkeyup(this)\">";

$sel_scheck = "<select name=s_check style=\"".$scheck_style."\">\n";
$sel_scheck.= "<option value=\"all\" ".($s_check=="all"?"selected":"").">통합검색</option>\n";
$sel_scheck.= "<option value=\"keyword\" ".($s_check=="keyword"?"selected":"").">상품명/키워드</option>\n";
$sel_scheck.= "<option value=\"code\" ".($s_check=="code"?"selected":"").">상품코드</option>\n";
$sel_scheck.= "<option value=\"selfcode\" ".($s_check=="selfcode"?"selected":"").">진열코드</option>\n";
$sel_scheck.= "<option value=\"production\" ".($s_check=="production"?"selected":"").">제조사</option>\n";
$sel_scheck.= "<option value=\"model\" ".($s_check=="model"?"selected":"").">모델명</option>\n";
$sel_scheck.= "<option value=\"content\" ".($s_check=="content"?"selected":"").">상세설명</option>\n";
$sel_scheck.= "<option value=\"prmsg\" ".($s_check=="prmsg"?"selected":"").">홍보문구</option>\n";
$sel_scheck.= "</select>\n";

$txt_keyword = "<input type=text name=search value=\"".$search."\" style=\"".$keyword_style."\" onkeyup=\"if(event.keyCode == '13'){ CheckForm(); }\">";


// 결과내 검색 1 출력 IF
$subSrchIf1S = "<!-- ";
$subSrchIf1E = " -->";

// 결과내 검색 1
$sel_scheck1 = "";
$txt_keyword1 = "";
if( strlen( $search ) > 0 ) {

	$subSrchIf1S = "";
	$subSrchIf1E = "";

	$sel_scheck1 = "<select name=s_check1 style=\"".$scheck_style1."\">\n";
	$sel_scheck1.= "<option value=\"all\" ".($s_check1=="all"?"selected":"").">통합검색</option>\n";
	$sel_scheck1.= "<option value=\"keyword\" ".($s_check1=="keyword"?"selected":"").">상품명/키워드</option>\n";
	$sel_scheck1.= "<option value=\"code\" ".($s_check1=="code"?"selected":"").">상품코드</option>\n";
	$sel_scheck1.= "<option value=\"selfcode\" ".($s_check1=="selfcode"?"selected":"").">진열코드</option>\n";
	$sel_scheck1.= "<option value=\"production\" ".($s_check1=="production"?"selected":"").">출판사</option>\n";
	$sel_scheck1.= "<option value=\"madein\" ".($s_check1=="madein"?"selected":"").">저자</option>\n";
	$sel_scheck1.= "<option value=\"model\" ".($s_check1=="model"?"selected":"").">모델명</option>\n";
	$sel_scheck1.= "<option value=\"content\" ".($s_check1=="content"?"selected":"").">상세설명</option>\n";
	$sel_scheck1.= "<option value=\"prmsg\" ".($s_check1=="prmsg"?"selected":"").">홍보문구</option>\n";
	$sel_scheck1.= "</select>\n";

	$txt_keyword1 = "<input type=text name=search1 value=\"".$search1."\" style=\"".$keyword_style1."\" onkeyup=\"if(event.keyCode == '13'){ CheckForm(); }\">";

	// 결과내 검색 2 출력 IF
	$subSrchIf2S = "<!-- ";
	$subSrchIf2E = " -->";

	// 결과내 검색 2
	$sel_scheck2 = "";
	$txt_keyword2 = "";
	if( strlen( $search1 ) > 0 ) {

		$subSrchIf2S = "";
		$subSrchIf2E = "";

		$sel_scheck2 = "<select name=s_check2 style=\"".$scheck_style2."\">\n";
		$sel_scheck2.= "<option value=\"all\" ".($s_check2=="all"?"selected":"").">통합검색</option>\n";
		$sel_scheck2.= "<option value=\"keyword\" ".($s_check2=="keyword"?"selected":"").">상품명/키워드</option>\n";
		$sel_scheck2.= "<option value=\"code\" ".($s_check2=="code"?"selected":"").">상품코드</option>\n";
		$sel_scheck2.= "<option value=\"selfcode\" ".($s_check2=="selfcode"?"selected":"").">진열코드</option>\n";
		$sel_scheck2.= "<option value=\"production\" ".($s_check2=="production"?"selected":"").">출판사</option>\n";
		$sel_scheck2.= "<option value=\"madein\" ".($s_check2=="madein"?"selected":"").">저자</option>\n";
		$sel_scheck2.= "<option value=\"model\" ".($s_check2=="model"?"selected":"").">모델명</option>\n";
		$sel_scheck2.= "<option value=\"content\" ".($s_check2=="content"?"selected":"").">상세설명</option>\n";
		$sel_scheck2.= "<option value=\"prmsg\" ".($s_check2=="prmsg"?"selected":"").">홍보문구</option>\n";
		$sel_scheck2.= "</select>\n";

		$txt_keyword2 = "<input type=text name=search2 value=\"".$search2."\" style=\"".$keyword_style2."\" onkeyup=\"if(event.keyCode == '13'){ CheckForm(); }\">";

	}
}


/* 선택된 정렬방식 활성화 */
$_new="";
$_best_desc="";
$_price="";
$_price_desc="";

switch(trim($sort)){
	case "best_desc":
		$_best_desc="class=\"sortOn\"";
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

	case "new_desc":
	default:
		$_new="class=\"sortOn\"";
	break;
}

if($listnum == 8) $sel8 = "selected";
if($listnum == 16) $sel16 = "selected";
if($listnum == 32) $sel32 = "selected";
if($listnum == 48) $sel48 = "selected";

$listselect = "
	<select name=\"listnum\" onchange=\"ChangeListnum(this.value)\">
		<option value='8' ".$sel16.">8</option>
		<option value='16' ".$sel16.">16</option>
		<option value='32' ".$sel32.">32</option>
		<option value='48' ".$sel48.">48</option>
	</select>개씩 보기
";

//검색상품목록 ($prlist_type이 1:이미지A형,2:이미지B형,3:리스트형,4:공구형일 경우에만)
$prlist1=""; $prlist2=""; $prlist3=""; $prlist4="";
if(preg_match("/^(1|2|3|4)$/",$prlist_type)) {
	if($t_count<=0) {
		$prlist1 = "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td align=center valign=middle height=30>검색된 상품이 없습니다.</td></tr></table>";
		$prlist2 = "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td align=center valign=middle height=30>검색된 상품이 없습니다.</td></tr></table>";
		$prlist3 = "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td align=center valign=middle height=100>검색된 상품이 없습니다.</td></tr></table>";
		$prlist4 = "<table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td align=center valign=middle height=50>검색된 상품이 없습니다.</td></tr></table>";
	} else {
		//번호, 사진, 상품명, 제조사, 가격
		$tmp_sort=explode("_",$sort);

		/*
		if($tmp_sort[0]=="reserve") {
			$addsortsql=",IF(a.reservetype='N',a.reserve*1,a.reserve*a.sellprice*0.01) AS reservesort ";
		}
		$sql = "SELECT a.productcode, a.productname, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, ";
		$sql.= "a.tinyimage, a.date, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode, a.prmsg, a.discountsellprice ";
		$sql.= $addsortsql;
		$sql.= "FROM tblproduct AS a ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
		*/

		$sql = productQuery();
		$sql.= $qry." ";
		$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
		if($tmp_sort[0]=="production") $sql.= "ORDER BY a.production ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="name") $sql.= "ORDER BY a.productname ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="price") $sql.= "ORDER BY a.sellprice ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="reserve") $sql.= "ORDER BY reservesort ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="new") $sql.= "ORDER BY a.regdate ".$tmp_sort[1]." ";
		else if($tmp_sort[0]=="best") $sql.= "ORDER BY a.sellcount ".$tmp_sort[1]." ";
		else $sql.= "ORDER BY a.productname ";
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

				// 예약상품 아이콘 추가
				$row->etctype = reservationEtcType($row->reservation,$row->etctype);

				// 할인율 표시
				$discountRate = ( $row->discountRate > 0 ) ? "<strong>".$row->discountRate."</strong>%↓" : "";

				#####################상품별 회원할인율 적용 시작#######################################
				$strikeStart = '';
				$strikeEnd = '';
				$memberprice = 0;
				$dSql = "SELECT discountYN,discountrates,discountprices FROM tblmemberdiscount ";
				$dSql .= "WHERE productcode='".$row->productcode."' AND group_code='".$_ShopInfo->getMemgroup()."'";
				$dResult = mysql_query($dSql,get_db_conn());
				$dRow = mysql_fetch_object($dResult);
				$discountprices = $dRow->discountprices;
				$discountYN = $dRow->discountYN;
				if($discountprices>0 && $discountYN == 'Y'){
					$memberprice = number_format($row->sellprice - $dRow->discountprices);
					$strikeStart = "<strike>";
					$strikeEnd = "</strike>";
				}
				#####################상품별 회원할인율 적용 끝 #######################################

				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
				$tableSize = $_data->primg_minisize+12;

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
				$prlist1.= "<table border=0 cellpadding=0 cellspacing=0 width=\"".$tableSize."\" id=\"A".$row->productcode."\" onmouseover=\"quickfun_show(this,'A".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'A".$row->productcode."','none')\" class=\"prInfoBox\">\n";
				$prlist1.= "<tr>\n";
				$prlist1.= "	<td align=\"center\" height=\"120\" style=\"padding:5px;\">";
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
				$prlist1.= "	<td style=\"padding:5px 7px; word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</A></td>\n";
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

				//시중가 + 판매가 + 할인율 + 회원할인가
				$prlist1.="
					<tr>
						<td style=\"padding:0px 7px 7px 7px; word-break:break-all;\">
							<table border=0 cellpadding=0 cellspacing=0 width=100%>
								<tr>
									<td>
				";

				if($prlist1_price=="Y" && $row->consumerprice>0) {	//소비자가
					$prlist1.="	<span class=\"prconsumerprice\" style=\"padding-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</span>\n";
					//$prlist1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <strike>".number_format($row->consumerprice)."</strike>원</td>\n";
				}

				// 회원 할인가가 있을 때 가격 class 변경
				if($discountprices > 0){
					$prpriceClass = "";
				}else{
					$prpriceClass = "prprice";
				}

				$prlist1.="<span style=\"white-space:nowrap;\">";
				$prlist1.=$strikeStart;
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$prlist1.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$prlist1.= "<strong class=\"".$prpriceClass."\">".number_format($row->sellprice)."</strong><strong>원</strong>";
					//$prlist1.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					//if (strlen($row->option_price)!=0) $prlist1.= "(기본가)";
				} else {
					//$prlist1.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (strlen($row->option_price)==0) $prlist1.= number_format($row->sellprice)."원";
					else $prlist1.= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
				}
				$prlist1.=$strikeEnd;
				$prlist1.="
								</span>
							</td>
				";
				if($row->discountRate > 0){
					$prlist1.="<td align=\"right\" valign=\"bottom\" class=\"discount\">".$discountRate."</td>";
				}
				$prlist1.="
						</tr>
					</table>
				";

				if ($row->quantity=="0") $prlist1.= soldout();

				//회원할인가 적용
				if($discountprices>0 && $discountYN == 'Y'){
					$prlist1.= "<div><span class=\"prprice\">".$memberprice."원</span> <img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" /></div>";
				}

				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($prlist1_reserve=="Y" && $reserveconv>0) {	//적립금
					$prlist1.="	<div style=\"margin-top:5px;\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\" align=\"absmiddle\" alt=\"\" /><span class=\"prreserve\">".number_format($reserveconv)."</span>원</div>";
					//$prlist1.="	<td align=center valign=top style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원</td>\n";
				}

				$prlist1.= "	</td>\n";
				$prlist1.= "</tr>\n";

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

				// 예약상품 아이콘 추가
				$row->etctype = reservationEtcType($row->reservation,$row->etctype);

				// 할인율 표시
				$discountRate = ( $row->discountRate > 0 ) ? "<strong>".$row->discountRate."</strong>%↓" : "";

				#####################상품별 회원할인율 적용 시작#######################################
				$strikeStart = '';
				$strikeEnd = '';
				$memberprice = 0;
				$dSql = "SELECT discountYN, discountrates,discountprices FROM tblmemberdiscount ";
				$dSql .= "WHERE productcode='".$row->productcode."' AND group_code='".$_ShopInfo->getMemgroup()."'";
				$dResult = mysql_query($dSql,get_db_conn());
				$dRow = mysql_fetch_object($dResult);
				$discountprices = $dRow->discountprices;
				$discountYN = $dRow->discountYN;
				if($discountprices>0 && $discountYN == 'Y'){
					$memberprice = number_format($row->sellprice - $dRow->discountprices);
					$strikeStart = "<strike>";
					$strikeEnd = "</strike>";
				}
				#####################상품별 회원할인율 적용 끝 #######################################

				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
				$tableSize = $_data->primg_minisize;

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
				$prlist2.= "<table border=0 cellpadding=0 cellspacing=0 width=100% id=\"A".$row->productcode."\" onmouseover=\"quickfun_show(this,'A".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'A".$row->productcode."','none')\" class=\"prInfoBox2\">\n";
				$prlist2.="<col width=\"".$tableSize."\"></col>\n";
				$prlist2.="<col width=\"0\"></col>\n";
				$prlist2.="<col width=\"\"></col>\n";
				$prlist2.= "<tr>\n";
				$prlist2.= "	<td align=center class=\"prImage\">";
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
				$prlist2.= "	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','A','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
				$prlist2.= "	<td valign=middle style=\"padding-left:15\">\n";
				$prlist2.= "	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				$prlist2.= "	<tr>";
				$prlist2.= "		<td align=left style=\"word-break:break-all;\" valign=top><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</A></td>\n";
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

				//시중가 + 판매가 + 할인율 + 회원할인가
				$prlist2.= "<tr>
									<td style=\"padding:0px 7px 7px 0px; word-break:break-all;\">
										<table border=0 cellpadding=0 cellspacing=0 width=100%>
											<tr>
												<td>
				";

				if($prlist2_price=="Y" && $row->consumerprice>0) {	//소비자가
					$prlist2.="	<span class=\"prconsumerprice\" style=\"padding-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</span>\n";
					//$prlist2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"prconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=0 align=absmiddle> <strike>".number_format($row->consumerprice)."</strike>원</td>\n";
				}

				// 회원 할인가가 있을 때 가격 class 변경
				if($discountprices>0){
					$prpriceClass = "";
				}else{
					$prpriceClass = "prprice";
				}

				$prlist2.="<span style=\"white-space:nowrap;\">";
				$prlist2.=$strikeStart;
				if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
					$prlist2.= $dicker;
				} else if(strlen($_data->proption_price)==0) {
					$prlist2.= "<strong class=\"".$prpriceClass."\">".number_format($row->sellprice)."</strong><strong>원</strong>";
					//$prlist2.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ".number_format($row->sellprice)."원";
					//if (strlen($row->option_price)!=0) $prlist2.= "(기본가)";
				} else {
					//$prlist2.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=0 align=absmiddle> ";
					if (strlen($row->option_price)==0) $prlist2.= number_format($row->sellprice)."원";
					else $prlist2.= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
				}
				$prlist2.=$strikeEnd;
				$prlist2.="</span>";

				if($row->discountRate > 0){
					$prlist2.="<span class=\"discount\">".$discountRate."</span>";
				}
				$prlist2.="
							</td>
						</tr>
					</table>
				";

				if ($row->quantity=="0") $prlist2.= soldout();

				//회원할인가 적용
				if($discountprices>0 && $discountYN == 'Y'){
					$prlist2 .= "<div><span class=\"prprice\">".$memberprice."원</span> <img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" /></div>";
				}

				$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
				if($prlist2_reserve=="Y" && $reserveconv>0) {	//적립금
					$prlist2.="	<div style=\"margin-top:5px;\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\" align=\"absmiddle\" alt=\"\" /><span class=\"prreserve\">".number_format($reserveconv)."</span>원</div>";
					//$prlist2.="	<td align=left valign=top style=\"word-break:break-all;\" class=\"prreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format($reserveconv)."원</td>\n";
				}
				$prlist2.= "	</td>\n";
				$prlist2.= "</tr>\n";

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

				// 입점사 네임택
				if( nameTechUse($row->vender) ) {
					$classList = array();
					$classResult=mysql_query("SELECT * FROM `tblVenderClassType` ",get_db_conn());
					while($classRow=mysql_fetch_object($classResult)) {
						$classList[$classRow->idx] = $classRow->name;
					}
					$v_info = mysql_fetch_assoc ( mysql_query( "SELECT * FROM `tblvenderinfo` WHERE `vender`=".$row->vender." LIMIT 1;" ,get_db_conn()) );

					// 네임텍 출력
					$prlist2.="
						<tr>
							<td>
								<div class=\"nameTagBox2\"><span class=\"name\">".$v_info['com_name']."</span> <span class=\"owner\">(".$v_info['com_owner'].")</span></div>
								<div><a href=\"javascript:GoMinishop('/minishop.php?storeid=".$v_info['id']."')\"><img src=\"/images/common/icon_vender_go.gif\" border=\"0\" align=\"absmiddle\" alt=\"전체상품보기\" /></a></div>
							</td>
						</tr>
					";
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
				$prlist3.= "	<td style=\"padding-left:5\" style=\"word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"prname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</A>";
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
					$prlist3.= "	<td align=center style=\"word-break:break-all;\" class=prreserve><img src=\"".$Dir."images/common/reserve_icon.gif\" border=0 align=absmiddle> ".number_format(getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y"))."원</td>\n";
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
				$prlist4.="	<td height=\"35\" colspan=\"2\" style=\"padding-top:5\"><div style=\"padding-left:15px;white-space:nowrap;width:210px;overflow:hidden;text-overflow:ellipsis;\"><a href='".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."' onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><font color=\"#000000\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><b>".$row->productname."</b></fonr></a></div></td>\n";
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
		$search_page=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
	}
}


$pattern=array(
	"(\[CODEA((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[CODEB((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[CODEC((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[CODED((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[MINPRICE((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[MAXPRICE((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[SCHECK((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[KEYWORD((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[SEARCHOK\])",
	"(\[TOTAL\])",
	"(\[SORTPRODUCTUP\])",
	"(\[SORTPRODUCTDN\])",
	"(\[SORTNAMEUP\])",
	"(\[SORTNAMEDN\])",
	"(\[SORTPRICEUP\])",
	"(\[SORTPRICEDN\])",
	"(\[SORTRESERVEUP\])",
	"(\[SORTRESERVEDN\])",
	"(\[SORTNEW\])",
	"(\[SORTBEST\])",
	"(\[ONNEW\])",
	"(\[ONBEST\])",
	"(\[ONPRICEUP\])",
	"(\[ONPRICEDN\])",
	"(\[ONRESERVEDN\])",
	"(\[LISTSELECT\])",
	"(\[PAGE\])",
	"(\[PRLIST1([0-9LNY_]{2,14})\])",
	"(\[PRLIST2([0-9LNY_]{2,14})\])",
	"(\[PRLIST3([0-9NY]{2,10})\])",
	"(\[PRLIST4([1-8]{2}(\_){0,1}[0-9]{0,2})\])"
);
$replace=array($codeA_select,$codeB_select,$codeC_select,$codeD_select,$txt_minprice,$txt_maxprice,$sel_scheck,$txt_keyword,"javascript:CheckForm()",$t_count,"javascript:ChangeSort('production')","javascript:ChangeSort('production_desc')","javascript:ChangeSort('name')","javascript:ChangeSort('name_desc')","javascript:ChangeSort('price')","javascript:ChangeSort('price_desc')","javascript:ChangeSort('reserve')","javascript:ChangeSort('reserve_desc')","javascript:ChangeSort('new')","javascript:ChangeSort('best_desc')",$_new,$_best_desc,$_price,$_price_desc,$_reserve_desc,$listselect,$search_page,$prlist1,$prlist2,$prlist3,$prlist4);




// 검색내 결과 검색 1 ------------------------------------------------------------------------------------------------------
array_push($pattern,"(\[SCHECK1((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])");
array_push($replace,$sel_scheck1);

array_push($pattern,"(\[KEYWORD1((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])");
array_push($replace,$txt_keyword1);

array_push($pattern,"(\[SUB_SEARCH1_IF_START\])");
array_push($replace,$subSrchIf1S);

array_push($pattern,"(\[SUB_SEARCH1_IF_END\])");
array_push($replace,$subSrchIf1E);
// ----------------------------------------------------------------------------------------------------------------------------


// 검색내 결과 검색 2 ------------------------------------------------------------------------------------------------------
array_push($pattern,"(\[SCHECK2((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])");
array_push($replace,$sel_scheck2);

array_push($pattern,"(\[KEYWORD2((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])");
array_push($replace,$txt_keyword2);

array_push($pattern,"(\[SUB_SEARCH2_IF_START\])");
array_push($replace,$subSrchIf2S);

array_push($pattern,"(\[SUB_SEARCH2_IF_END\])");
array_push($replace,$subSrchIf2E);
// ----------------------------------------------------------------------------------------------------------------------------




$body = preg_replace($pattern,$replace,$body);

$body.= "<script>SearchCodeInit(\"".$codeA."\",\"".$codeB."\",\"".$codeC."\",\"".$codeD."\");</script>";

echo $body;

?>