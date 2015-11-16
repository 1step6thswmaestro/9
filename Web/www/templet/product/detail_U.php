<?
$codename=$_cdata->code_name;
$clipcopy="\"javascript:ClipCopy('http://".getenv("HTTP_HOST")."/?".getenv("QUERY_STRING")."')\"";

$codenavi="";
if($num=strpos($body,"[CODENAVI")) {
	$s_tmp=explode("_",substr($body,$num+9,13));
	$codenavi=($brandcode>0?getBCodeLoc($brandcode,$code,$s_tmp[0],$s_tmp[1]):getCodeLoc($code,$s_tmp[0],$s_tmp[1]));
}

if(substr($_pdata->productcode,0,3) == '898'){ // 정기 배송 상품 처리용
	$body = preg_replace('/(\[IFSCHEDULED\](.*?)\[ELSESCHEDULED\](.*?)\[ENDSCHEDULED\])/s','\2',$body);
}else{
	$body = preg_replace('/(\[IFSCHEDULED\](.*?)\[ELSESCHEDULED\](.*?)\[ENDSCHEDULED\])/s','\3',$body);
}

$coupon1="";
$coupon2="";
if(strpos($body,"[COUPON1]")==true) {
	$coupon1=$couponbody1;
} else if(strpos($body,"[COUPON2]")==true) {
	$coupon2=$couponbody2;
}

if(strpos($body,"[IFOPTION]")!=0) {
	$ifoptionnum=strpos($body,"[IFOPTION]");
	$endoptionnum=strpos($body,"[IFENDOPTION]");
	$bodyoption=substr($body,$ifoptionnum+10,$endoptionnum-$ifoptionnum-10);
	$body=substr($body,0,$ifoptionnum)."[OPTIONVALUE]".substr($body,$endoptionnum+13);
}

if(strpos($body,"[IFPACKAGE]")!=0) {
	$ifpackagenum=strpos($body,"[IFPACKAGE]");
	$endpackagenum=strpos($body,"[IFENDPACKAGE]");
	$bodypackage=substr($body,$ifpackagenum+11,$endpackagenum-$ifpackagenum-11);
	$body=substr($body,0,$ifpackagenum)."[PACKAGEVALUE]".substr($body,$endpackagenum+14);
}

/*
if(strpos($body,"[IFVENDER]")!=0) {
	$ifvendernum=strpos($body,"[IFVENDER]");
	$endvendernum=strpos($body,"[IFENDVENDER]");
	$bodyvender=substr($body,$ifvendernum+10,$endvendernum-$ifvendernum-10);
	$body=substr($body,0,$ifvendernum)."[VENDERVALUE]".substr($body,$endvendernum+13);
}
*/

$review_average_color1="CACACA";
$review_average_color2="000000";
if($num=strpos($body,"[REVIEW_AVERAGE")) {
	$s_tmp=explode("_",substr($body,$num+15,13));
	if(strlen($s_tmp[0])==6) $review_average_color1=$s_tmp[0];
	if(strlen($s_tmp[1])==6) $review_average_color2=$s_tmp[1];
}

$reviewname_style="width:60";
$reviewarea_style="width:95%;height:40";
if($num=strpos($body,"[REVIEW_NAME_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$reviewname_style=$s_tmp[2];
}
if($num=strpos($body,"[REVIEW_AREA_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$reviewarea_style=$s_tmp[2];
}

$review_marks_color="000000";
if($num=strpos($body,"[REVIEW_MARKS")) {
	$s_tmp=substr($body,$num+13,6);
	if(strlen($s_tmp)==6) $review_marks_color=$s_tmp;
}

include("productdetail_text.php");

if((strlen($uspecname1)==0 && strlen($uspecvalue1)==0) && strpos($body,"[IFUSPEC1]")!=0) {
	$ifuspecnum1=strpos($body,"[IFUSPEC1]");
	$enduspecnum1=strpos($body,"[IFENDUSPEC1]");
	$body=substr($body,0,$ifuspecnum1).substr($body,$enduspecnum1+13);
}

if((strlen($uspecname2)==0 && strlen($uspecvalue12)==0) && strpos($body,"[IFUSPEC2]")!=0) {
	$ifuspecnum2=strpos($body,"[IFUSPEC2]");
	$enduspecnum2=strpos($body,"[IFENDUSPEC2]");
	$body=substr($body,0,$ifuspecnum2).substr($body,$enduspecnum2+13);
}

if((strlen($uspecname3)==0 && strlen($uspecvalue3)==0) && strpos($body,"[IFUSPEC3]")!=0) {
	$ifuspecnum3=strpos($body,"[IFUSPEC3]");
	$enduspecnum3=strpos($body,"[IFENDUSPEC3]");
	$body=substr($body,0,$ifuspecnum3).substr($body,$enduspecnum3+13);
}

if((strlen($uspecname4)==0 && strlen($uspecvalue4)==0) && strpos($body,"[IFUSPEC4]")!=0) {
	$ifuspecnum4=strpos($body,"[IFUSPEC4]");
	$enduspecnum4=strpos($body,"[IFENDUSPEC4]");
	$body=substr($body,0,$ifuspecnum4).substr($body,$enduspecnum4+13);
}

if((strlen($uspecname5)==0 && strlen($uspecvalue5)==0) && strpos($body,"[IFUSPEC5]")!=0) {
	$ifuspecnum5=strpos($body,"[IFUSPEC5]");
	$enduspecnum5=strpos($body,"[IFENDUSPEC5]");
	$body=substr($body,0,$ifuspecnum5).substr($body,$enduspecnum5+13);
}

if((strlen($pesterbtn)==0 && strpos($body,"[IFPESTER]")!=0)) {
	$ifpester=strpos($body,"[IFPESTER]");
	$ifendpester=strpos($body,"[IFENDPESTER]");
	$body=substr($body,0,$ifpester).substr($body,$ifendpester+13);
}
if((strlen($presentbtn)==0 && strpos($body,"[IFPRESENT]")!=0)) {
	$ifpresent=strpos($body,"[IFPRESENT]");
	$ifendpresent=strpos($body,"[IFENDPRESENT]");
	$body=substr($body,0,$ifpresent).substr($body,$ifendpresent+14);
}

$pattern=array(
	"(\[STARTFORM\])",
	"(\[ENDFORM\])",
	"(\[PRNAME\])",
	"(\[CODENAME\])",
	"(\[CODENAVI([0-9a-fA-F_]{0,13})\])",
	"(\[CLIPCOPY\])",
	"(\[COUPON1\])",
	"(\[COUPON2\])",
	"(\[PREV\])",
	"(\[NEXT\])",
	"(\[PRINFO\])",
	"(\[GONGTABLE\])",
	"(\[GONGINFO\])",
	"(\[PRIMAGE\])",
	"(\[SELLPRICE\])",
	"(\[GONGPRICE\])",
	"(\[DOLLAR\])",
	"(\[PRODUCTION\])",
	"(\[MADEIN\])",
	"(\[MODEL\])",
	"(\[BRAND\])",
	"(\[BRANDLINK\])",
	"(\[OPENDATE\])",
	"(\[SELFCODE\])",
	"(\[ADDCODE\])",
	"(\[DELIPRICE\])",
	"(\[USPECNAME1\])",
	"(\[USPECNAME2\])",
	"(\[USPECNAME3\])",
	"(\[USPECNAME4\])",
	"(\[USPECNAME5\])",
	"(\[USPECVALUE1\])",
	"(\[USPECVALUE2\])",
	"(\[USPECVALUE3\])",
	"(\[USPECVALUE4\])",
	"(\[USPECVALUE5\])",
	"(\[IFUSPEC1\])",
	"(\[IFENDUSPEC1\])",
	"(\[IFUSPEC2\])",
	"(\[IFENDUSPEC2\])",
	"(\[IFUSPEC3\])",
	"(\[IFENDUSPEC3\])",
	"(\[IFUSPEC4\])",
	"(\[IFENDUSPEC4\])",
	"(\[IFUSPEC5\])",
	"(\[IFENDUSPEC5\])",
	"(\[CONSUMPRICE\])",
	"(\[RESERVE\])",
	"(\[QUANTITY\])",
	"(\[QUANTITY_UP\])",
	"(\[QUANTITY_DN\])",
	"(\[OPTIONVALUE\])",
	"(\[VENDERVALUE\])",
	"(\[DETAIL\])",
	"(\[BASKETIN\])",
	"(\[WISHIN\])",
	"(\[BARO\])",
	"(\[TAGLIST\])",
	"(\[TAGREGINPUT((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[TAGREGOK\])",
	"(\[COLLECTION\])",
	"(\[DELIINFO\])",
	"(\[REVIEW_STARTFORM\])",
	"(\[REVIEWALL\])",
	"(\[REVIEW_WRITE\])",
	"(\[REVIEW_HIDE_START\])",
	"(\[REVIEW_SHOW_START\])",
	"(\[REVIEW_NAME((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[REVIEW_AREA((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[REVIEW_MARKS([0-9a-fA-F]{0,6})\])",
	"(\[REVIEW_MARK1\])",
	"(\[REVIEW_MARK2\])",
	"(\[REVIEW_MARK3\])",
	"(\[REVIEW_MARK4\])",
	"(\[REVIEW_MARK5\])",
	"(\[REVIEW_RESULT\])",
	"(\[REVIEW_HIDE_END\])",
	"(\[REVIEW_SHOW_END\])",
	"(\[REVIEW_TOTAL\])",
	"(\[REVIEW_AVERAGE([0-9a-fA-F_]{0,13})\])",
	"(\[REVIEW_LIST\])",
	"(\[REVIEW_SLIST\])",
	"(\[REVIEW_ENDFORM\])",
	"(\[QNA_ALL\])",
	"(\[QNA_WRITE\])",
	"(\[QNA_LIST\])",
	"(\[ASSEMBLETABLE\])",
	"(\[PACKAGETABLE\])",
	"(\[PACKAGEVALUE\])",
	"(\[SNSBUTTON\])",
	"(\[PESTERBUTTON\])",
	"(\[PRESENTBUTTON\])",
	"(\[PESTER\])",
	"(\[PRESENT\])",
	"(\[IFPESTER\])",
	"(\[IFENDPESTER\])",
	"(\[IFPRESENT\])",
	"(\[IFENDPRESENT\])",
	"(\[SNSCOMMENT\])",
	"(\[GONGGUCOMMENT\])",
	"(\[REVIEW_MEAN\])",
	"(\[REVIEWTOP_LIST\])",
	"(\[QNATOP_LIST\])",
	"(\[AVAILABLE\])",
	"(\[QNA_TOTAL\])",
	"(\[BASKET_ETCIMG\])",
	"(\[SOLDOUT_START\])",
	"(\[SOLDOUT_END\])"
);

$startform="<form name=form1 method=post action=\"".$Dir.FrontDir."basket.php\">";

$endform = $detailhidden;
$endform.="<input type=hidden name=code value=\"".$code."\">\n";
$endform.="<input type=hidden name=productcode value=\"".$productcode."\">\n";
$endform.="<input type=hidden name=ordertype>\n";
$endform.="<input type=hidden name=opts>\n";
$endform.=($brandcode>0?"<input type=hidden name=brandcode value=\"".$brandcode."\">\n":"");
$endform.="</form>\n";

/*$review_startform ="<form name=reviewform method=post action=\"".$_SERVER[PHP_SELF]."\" enctype=\"multipart/form-data\">";
$review_startform.="<input type=hidden name=mode>\n";
$review_startform.="<input type=hidden name=code value=\"".$code."\">\n";
$review_startform.="<input type=hidden name=productcode value=\"".$productcode."\">\n";
$review_startform.="<input type=hidden name=sort value=\"".$sort."\">\n";
$review_startform.=($brandcode>0?"<input type=hidden name=brandcode value=\"".$brandcode."\">\n":"");
$review_endform="</form>\n";*/

// 상품 정보 고시 관련 자동 생성 내용 출력
//$detail .= $gositable;
//#상품 정보 고시 관련 자동 생성 내용 출력


if(substr($_pdata->productcode,0,3) == '898'){ // 정기배송상품
	include_once $Dir.'scheduled_delivery/config.php';
	$parseprice = parseSellprice($_pdata->option_price);
	$sellprice = '';
	
	for($q=0;$q<count($sdconfig['periods']);$q++){
		$period = $sdconfig['periods'][$q];
		$price = $parseprice[$period];
		$bonus = $sdconfig['period_bonus'][$q];
		if($q >0) $sellprice .= '<br>';
		$sellprice .='<span class="pdPeriodstyle" style="width:70px;">'.$period.($bonus>0?'+'.$bonus:'').$sdconfig['dtail'].'</span>';
		$sellprice .='<span class="sellprice" style="color: red; font-weight: bold;">'.number_format($price).'원</span>';
	/*
		if(intval($_pdata->consumerprice) >0){
			$disper = round(($_pdata->consumerprice*$period-$price)/$_pdata->consumerprice*$period)*100;
			$sellprice .= $disper;
			if($disper > 0) $sellprice .= '<div class="scheduled_discountrate">'.$disper.'<span style="color:#ffffff; font-size:13px; font-weight:700;">%</span></div>';
		}*/
		
	}
	$baro="\"/scheduled_delivery/\" onmouseover=\"window.status='바로구매';return true;\" onmouseout=\"window.status='';return true;\"";	
}



$replace=array($startform,$endform,$prname,$codename,$codenavi,$clipcopy,$coupon1,$coupon2,$prev,$next,$prinfo,$gongtable,$gonginfo,$primage,$sellprice,$gongprice,$dollar,$production,$madein,$model,$brand,$brandlink,$opendate,$selfcode,$addcode,$delipriceTxt,$uspecname1,$uspecname2,$uspecname3,$uspecname4,$uspecname5,$uspecvalue1,$uspecvalue2,$uspecvalue3,$uspecvalue4,$uspecvalue5,"","","","","","","","","","",$consumprice,$reserve,$quantity,$quantity_up,$quantity_dn,$optionvalue,$vendervalue,$detail,$basketin,$wishin,$baro,$taglist,$tagreginput,$tagregok,$collection,$deli_info,$review_startform,$reviewall,$review_write,$review_hide_start,$review_show_start,$review_name,$review_area,$review_marks,$review_mark1,$review_mark2,$review_mark3,$review_mark4,$review_mark5,$review_result,$review_hide_end,$review_show_end,$review_total,$review_average,$review_list,$review_slist,$review_endform,$qna_all,$qna_write,$qna_list,$assembletable,$packagetable,$packagevalue,$snsButton,$pesterButton,$presentButton,$pesterbtn,$presentbtn,"","","","",$snscomment,$gonggucomment,$review_mean,$reviewtop_list,$qnatop_list,$available,$qna_total,$basket_etcimg,$sold_out_start,$sold_out_end);

// 상품 정보 고시 관련 자동 생성 내용 출력
array_push($pattern,'(\[PRODUCTINFOGOSI\])');
array_push($replace,$gositable);


// 상품 상세 밴더 정보
$vendervalue="";
$isVenderStart="<!-- ";
$isVenderEnd=" -->";
if($_pdata->vender>0) {

	$sql = "SELECT a.vender, a.id, a.brand_name, a.deli_info, b.prdt_cnt , i.com_name,i.com_image ";
	$sql.= "FROM tblvenderstore a left join tblvenderstorecount b on b.vender = a.vender left join tblvenderinfo i on i.vender = a.vender ";
	$sql.= "WHERE a.vender='".$_pdata->vender."' ";

	$result=mysql_query($sql,get_db_conn());
	if(!$_vdata=mysql_fetch_object($result)) {
		$_pdata->vender=0;
	}
	mysql_free_result($result);

	array_push($pattern,'(\[VENDER_NAME\])');
	array_push($replace,$_vdata->brand_name);

	array_push($pattern,'(\[VENDER_DESCRIPTION\])');
	array_push($replace,$_vdata->brand_description);

	array_push($pattern,'(\[VENDER_MINISHOP\])');
	array_push($replace,"javascript:GoMinishop('".$Dir.(MinishopType=="ON"?"minishop/":"minishop.php?storeid=").$_vdata->id."')");

	array_push($pattern,'(\[VENDER_PRDTCNT\])');
	array_push($replace,$_vdata->prdt_cnt);

	array_push($pattern,'(\[VENDER_REGIST\])');
	array_push($replace,"javascript:custRegistMinishop()");

	$isVenderStart="";
	$isVenderEnd="";

	$v_info = mysql_fetch_assoc ( mysql_query( "SELECT * FROM `tblvenderinfo` WHERE `vender`=".$_pdata->vender." LIMIT 1;" ,get_db_conn()) );
	array_push($pattern,'(\[VENDER_IMAGE\])');
	array_push($replace,"<img src=\"/data/shopimages/vender/".$v_info[com_image]."\" width=\"150\">");

	array_push($pattern,'(\[VENDER_OWNER\])');
	array_push($replace,$v_info[com_owner]);

}
array_push($pattern,'(\[IFVENDER\])');
array_push($replace,$isVenderStart);

array_push($pattern,'(\[IFENDVENDER\])');
array_push($replace,$isVenderEnd);

array_push($pattern,'(\[VENDERPRODUCT\])');
array_push($replace,$venderproduct);


// 관련상품 사용 유무체크
$collectionStart = '<!--';
$collectionEnd = '-->';

//if($_data->coll_loc != '0' && strlen($collection_list) > 0) {
if($_data->coll_loc != '0') {
	$collectionStart = '';
	$collectionEnd = '';
}
array_push($pattern,'(\[COLLECTIONSTART\])');
array_push($replace,$collectionStart);

array_push($pattern,'(\[COLLECTIONEND\])');
array_push($replace,$collectionEnd);


// 상품 적용가능 쿠폰 리스트
array_push($pattern,'(\[COUPON_LIST\])');
array_push($replace,$coupon_body);


// 사은품 정책보기
array_push($pattern,'(\[GIFT_PRICE\])');
array_push($replace,number_format($giftprice));


// 적용가능쿠폰
array_push($pattern,'(\[ABLE_COUPON_POP\])');
array_push($replace,"javascript:ableCouponPOP('".$productcode."');");



// 사용불가체크 관련
//$chkarr = array('coupon','reserve','gift','return');
$i=1;
foreach($_pdata->checkAbles as $chkidx=>$etcchk){
	switch($chkidx){
		case 'coupon': $etcname= '할인쿠폰'; break;
		case 'reserve': $etcname= '적립금'; break;
		case 'gift': $etcname= '구매사은품'; break;
		case 'return': $etcname= '교환및환불'; break;
	}

	array_push($pattern,"(\[ETCAPPLYNAME".($i)."\])","(\[ETCAPPLYVALUE".($i)."\])");
	$etcval = ($etcchk == 'Y')?'<span style="color:blue">적용가능</span>':'적용불가';
	array_push($replace,$etcname,$etcval);


	array_push($pattern,"(\[IFETCAPPLY".($i)."\])","(\[IFENDAPPLY".($i++)."\])");
	array_push($replace,"","");
}
//#사용불가체크 관련

// 도매가 관련 추가
if(isSeller() == 'Y'){
	$ifwholesale = $ifendwholesale = '';
	$wholesaleprice = number_format($_pdata->productdisprice);
}else{
	$ifwholesale = '<!-- ';
	$ifendwholesale = ' -->';
}
// #도매가 관련 추가


//
$viewproductsstr = '';
if(!empty($_COOKIE['ViewProduct'])){
	$tmpitems = explode(',',$_COOKIE['ViewProduct']);
	$viewProductcodes = array();
	foreach($tmpitems as $tpcode){
		if(preg_match('/^[0-9]{18}$/',$tpcode)) array_push($viewProductcodes,$tpcode);
	}
	if(count($viewProductcodes) > 0){
		$sql = "SELECT * FROM tblproduct WHERE productcode IN ('".implode("','",$viewProductcodes)."') ORDER BY FIELD(productcode,".implode(',',$viewProductcodes).") ";
		if(false !== $res = mysql_query($sql,get_db_conn())){
			if(mysql_num_rows($res)){
				$viewproductsstr = '<DIV style="SCROLLBAR-ARROW-COLOR: #999999; SCROLLBAR-FACE-COLOR: #ffffff; PADDING-BOTTOM: 5px; MARGIN: 0px; PADDING-LEFT: 5px; WIDTH: 100%; PADDING-RIGHT: 5px; SCROLLBAR-DARKSHADOW-COLOR: #ffffff; HEIGHT: 300px; SCROLLBAR-HIGHLIGHT-COLOR: #cccccc; SCROLLBAR-SHADOW-COLOR: #cccccc; OVERFLOW: auto; SCROLLBAR-TRACK-COLOR: #ffffff; SCROLLBAR-3DLIGHT-COLOR: #ffffff; PADDING-TOP: 3px;margin-top:15px;">';
				$viewproductsstr .= '<TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%"><TBODY><TR><TD vAlign=top><TABLE cellSpacing=0 cellPadding=0><TBODY>';

				$loop = ceil(mysql_num_rows($res)/2)*2;
				for($i=0;$i<$loop;$i++){
					if($i < 1 || $i%2 ==0)  $viewproductsstr .= '<tr>';

					if($row = mysql_fetch_object($res)){
						$viewproductsstr.= "				<td align=\"center\" valign=\"top\" style=\"padding:2px;\">\n";
						$viewproductsstr.= "				<table cellpadding=\"0\" cellspacing=\"0\" width=\"100\" id=\"U".$row->productcode."\" ";
						if(substr($row->productcode,0,3)!='999' && $row->social_chk !="Y") {
							$viewproductsstr.= " onmouseover=\"quickfun_show(this,'U".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'U".$row->productcode."','none')\"";
						}
						$viewproductsstr.= ">\n";
						$viewproductsstr.= "				<tr>\n";
					//	$prdlistbody.= "					<td align=\"center\" valign=\"top\" style=\"padding:5px;padding-bottom:2px;\"><input type=\"checkbox\" name=\"idxToday\" id=\"idxToday".$cnt."\" value=\"".$row->productcode."\" style=\"padding:0;margin:0;position:absolute;\">";
						$viewproductsstr.= "					<td align=\"center\" valign=\"top\" style=\"padding:5px;padding-bottom:2px;\">";
						if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
							$viewproductsstr.= "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 ";
							$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
							if($_data->ETCTYPE["IMGSERO"]=="Y") {
								if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $viewproductsstr.= "height=\"".$_data->primg_minisize2."\" ";
								else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $viewproductsstr.= "width=\"".$_data->primg_minisize."\" ";
							} else {
								if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $viewproductsstr.= "width=\"".$_data->primg_minisize."\" ";
								else if ($width[1]>=$_data->primg_minisize) $viewproductsstr.= "height=\"".$_data->primg_minisize."\" ";
							}
						} else {
							$viewproductsstr.= "<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
						}
						$viewproductsstr.= " id=\"xtprimage\"></td>\n";
						$viewproductsstr.= "				</tr>\n";
						$viewproductsstr.= "				<tr>\n";
						$viewproductsstr.= "					<td height=\"3\" style=\"position:relative;\">\n";
						$viewproductsstr.= "					</td>\n";
						$viewproductsstr.= "				</tr>\n";
						$viewproductsstr.= "				<tr>\n";
						$viewproductsstr.= "					<td style=\"padding:5px;\">\n";
						$viewproductsstr.= "					<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
						$viewproductsstr.= "					<tr>\n";
						$viewproductsstr.= "						<td align=\"center\" style=\"padding-left:2px;word-break:break-all;\"><a href=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\"><span color=\"#000000\" id=\"xtprname\">".viewproductname($row->productname,($btproiconok=="Y"?$row->etctype:""),($btselfcodeok=="Y"?$row->selfcode:""))."</span></a></td>\n";
						$viewproductsstr.= "					</tr>\n";

						if ($btconsumerpriceok=="Y" && $row->consumerprice>0) {
							$viewproductsstr.= "					<tr>\n";
							$viewproductsstr.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xtprconsumerprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
							$viewproductsstr.= "					</tr>\n";
						}
						if ($row->sellprice>0) {
							$viewproductsstr.= "					<tr>\n";
							$viewproductsstr.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xtprsellprice\">\n";
							if($dicker=dickerview($row->etctype,number_format($row->sellprice)."원",1)) {
								$viewproductsstr.= $dicker;
							} else if(strlen($_data->proption_price)==0) {
								$viewproductsstr.= "<img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($row->sellprice)."원";
							} else {
								$viewproductsstr.="<img src=\"".$Dir."images/common/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\">";
								if (strlen($row->option_price)==0) $viewproductsstr.= number_format($row->sellprice)."원";
								else $viewproductsstr.= ereg_replace("\[PRICE\]",number_format($row->sellprice),$_data->proption_price);
							}
							if ($row->quantity=="0") $viewproductsstr.= soldout();
							$viewproductsstr.= "						</td>\n";
							$viewproductsstr.= "					</tr>\n";
						}
						$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"Y");
						if($btreserveok=="Y" && $reserveconv>0) {
							$viewproductsstr.= "					<tr>\n";
							$viewproductsstr.= "						<td align=\"center\" style=\"word-break:break-all;\" id=\"xtprreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."원</td>\n";

							$viewproductsstr.= "					</tr>\n";
						}
						$viewproductsstr.= "					</table>\n";
						$viewproductsstr.= "					</td>\n";
						$viewproductsstr.= "				</tr>\n";
						$viewproductsstr.= "				</table>\n";
						$viewproductsstr.= "				</td>\n";
					}else{
						$viewproductsstr = '&nbsp;';
					}

					if($i > 0 && $i%2) $viewproductsstr .= '</tr>';
				}

				$viewproductsstr .= '</tbody></table></td></tr></tbody></table>';
				$viewproductsstr .= '</div>';
			}
		}
	}
}

//echo htmlspecialchars($viewproductsstr);
//exit;
$reviewwrite ="";
$reviewwrite = '
					<div id="div_reviewwrite_container">
						<form name="reviewWriteForm" action="/front/reviewwrite_proc.php" method="post" enctype="multipart/form-data">
							<input type="hidden" name="mode"/>
							<input type="hidden" name="code" value="'.$code.'"/>
							<input type="hidden" name="productcode" value="'.$productcode.'"/>
							<input type="hidden" name="sort" value="'.$sort.'"/>
							'.($brandcode>0?'<input type="hidden" name="brandcode" value="'.$brandcode.'"/>':'').'
							<table cellpadding="0" cellspacing="0" class="reviewMarkTbl">
								<tr>
									<th>* 품질</th>
									<td>
										<select name="quality">
											<option value="1">★</option>
											<option value="2">★★</option>
											<option value="3">★★★</option>
											<option value="4">★★★★</option>
											<option value="5" selected>★★★★★</option>
										</select>
									</td>
									<th>* 가격</th>
									<td>
										<select name="price">
											<option value="1">★</option>
											<option value="2">★★</option>
											<option value="3">★★★</option>
											<option value="4">★★★★</option>
											<option value="5" selected>★★★★★</option>
										</select>
									</td>
								</tr>
								<tr>
									<th>* 배송</th>
									<td>
										<select name="delitime">
											<option value="1">★</option>
											<option value="2">★★</option>
											<option value="3">★★★</option>
											<option value="4">★★★★</option>
											<option value="5" selected>★★★★★</option>
										</select>
									</td>
									<th>* 추천</th>
									<td>
										<select name="recommend">
											<option value="1">★</option>
											<option value="2">★★</option>
											<option value="3">★★★</option>
											<option value="4">★★★★</option>
											<option value="5" selected>★★★★★</option>
										</select>
									</td>
								</tr>
							</table>

							<table cellpadding="0" cellspacing="0" class="reviewWriteTbl">
								<tr>
									<th>작성자</th>
									<td><input type="text" name="rname" maxlength="10" class="input" value=""/></td>
								</tr>
								<tr>
									<th>내용</th>
									<td><textarea name="rcontent" style="WIDTH:100%; HEIGHT:40px; padding:3pt; line-height:17px; border:solid 1px; border-color:#DFDFDF; font-size:9pt; color:333333;"></textarea></td>
								</tr>
								<tr>
									<th>첨부파일</th>
									<td><input type="file" name="attech" class="input" value=""/></td>
								</tr>
							</table>
							<div class="reviewInfoDiv">
								<b>·</b> 작성된 상품평은 수정/삭제가 불가하니 참고해 주시기 바랍니다.<br />
								<b>·</b> 취지와 상관없는 광고성 글은 관리자에 의해 임의로 삭제될 수 있습니다.<br />
								<b>·</b> 첨부파일은 이미지 파일(GIF, JPG, PNG)만 등록 가능합니다.
							</div>
						</form>
					</div>
					';

array_push($pattern,'(\[REVIEW_WRITE_FORM\])');
array_push($replace,$reviewwrite);

$btnreviewwrite = '"javascript:write_review();"';
array_push($pattern,'(\[BTN_REVIEW_WRITE\])');
array_push($replace,$btnreviewwrite);



#사용후기 전체 평균 점수
array_push($pattern,'(\[REVIEW_AVERSCORE_TOTAL\])');
array_push($replace,$avertotalscore);

#사용후기 품질 평균 점수
array_push($pattern,'(\[REVIEW_AVERSCORE_QULITY\])');
array_push($replace,$averquality);

#사용후기 가격 평균 점수
array_push($pattern,'(\[REVIEW_AVERSCORE_PRICE\])');
array_push($replace,$averprice);

#사용후기 배송 평균 점수
array_push($pattern,'(\[REVIEW_AVERSCORE_DELITIME\])');
array_push($replace,$averdelitime);

#사용후기 추천 평균 점수
array_push($pattern,'(\[REVIEW_AVERSCORE_RECOMMEND\])');
array_push($replace,$averrecommend);


#사용후기 토탈 별점수
array_push($pattern,'(\[REVIEW_VIEW_STARTOTAL\])');
array_push($replace,$reviewstarcount);


#사용후기 품질 별점수
array_push($pattern,'(\[REVIEW_VIEW_STARQUALITY\])');
array_push($replace,$qualitystarcount);


#사용후기 가격 별점수
array_push($pattern,'(\[REVIEW_VIEW_STARPRICE\])');
array_push($replace,$pricestarcount);


#사용후기 배송 별점수
array_push($pattern,'(\[REVIEW_VIEW_STARDELITIME\])');
array_push($replace,$delitimestarcount);


#사용후기 추천 별점수
array_push($pattern,'(\[REVIEW_VIEW_STARRECOMMEND\])');
array_push($replace,$recommendstarcount);

array_push($pattern,'(\[RIVIEW_COUNT_TOTAL\])');
array_push($replace,$counttotal);

array_push($pattern,'(\[VIEW_PROTUCTS\])');
array_push($replace,$viewproductsstr);

array_push($pattern,'(\[PRMSG\])');
array_push($replace,$_pdata->prmsg);

// 할인율
array_push($pattern,'(\[DISC_RATE\])');
array_push($replace,$_pdata->discountRate);

#상품후기 카운트 메크로
array_push($pattern,'(\[RIVIEW_COUNT\])');
array_push($replace,$rowcount);
#상품후기 카운트 메크로 끝

#QNA 카운트 메크로
array_push($pattern,'(\[QNA_COUNT\])');
array_push($replace,$qnacount);
#QNA 카운트 메크로 끝

/*$reviewselect ="";
$reviewselect = '<select name="reviewselect" onchange="reviewSelect(this.value);">';
$reviewselect .='<option value="all"'.$sallreview.'>전체상품평('.$counttotal.')</option>';
$reviewselect .='<option value="photo"'.$sphotoreview.'>포토상품평('.$countphoto.')</option>';
$reviewselect .='<option value="basic"'.$sbasicreview.'>일반상품평('.$countbasic.')</option>';
$reviewselect .='<option value="best"'.$sbestreview.'>베스트상품평('.$countbest.')</option>';
$reviewselect .='</select>';*/

$reviewselect ="";
$reviewselect .='<div class="button '.$sallreview.'"><a href="javascript:reviewSelect(\'all\');">전체상품평('.$counttotal.')</a></div>';
$reviewselect .='<div class="button '.$sbestreview.'"><a href="javascript:reviewSelect(\'best\');">베스트상품평('.$countbest.')</a></div>';
$reviewselect .='<div class="button '.$sphotoreview.'"><a href="javascript:reviewSelect(\'photo\');">포토상품평('.$countphoto.')</a></div>';
$reviewselect .='<div class="button '.$sbasicreview.'"><a href="javascript:reviewSelect(\'basic\');">일반상품평('.$countbasic.')</a></div>';

#상품후기 셀렉트 메크로
array_push($pattern,'(\[REVIEW_SELECT\])');
array_push($replace,$reviewselect);
#상품후기 셀렉트 메크로 끝


















// 벤더 네임택
$venderNameTag = "";
if( nameTechUse($_pdata->vender) ) {

	$classList = array();
	$classResult=mysql_query("SELECT * FROM `tblVenderClassType` ",get_db_conn());
	while($classRow=mysql_fetch_object($classResult)) {
		$classList[$classRow->idx] = $classRow->name;
	}
	$v_info = mysql_fetch_assoc ( mysql_query( "SELECT * FROM `tblvenderinfo` WHERE `vender`=".$_pdata->vender." LIMIT 1;" ,get_db_conn()) );

	/*
	$venderNameTag .= "<img src=\"".$com_image_url.$v_info['com_image']."\" width=\"100\">";
	$venderNameTag .= "<br><strong>".$v_info['com_name']."</strong>";
	$venderNameTag .= "<br>대표 : ".$v_info['com_owner']."";
	if( $v_info['class'] > 0 ) $venderNameTag .= "<br>등급:".$classList[$v_info['class']]."";
	$venderNameTag .= "<br>사업자번호 : ".$v_info['com_num']."";
	$venderNameTag .= "<br>사업구분 : ".$v_info['com_type']."";
	$venderNameTag .= "<br>연락처 : ".$v_info['com_tel']."";
	$venderNameTag .= "<br>통신판매신고 : ".$v_info['ec_num']."";
	$venderNameTag .= "<br>사업장소재지 : ".$v_info['com_addr']."";
	$venderNameTag .= "<br>e-mail : ".$v_info['p_email']."";
	*/

	$venderNameTag .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"border:1px solid #dddddd; margin-top:20px;\">
									<tr>
										<td style=\"width:130px; text-align:center; background:#f9f9f9;\"><img src=\"".$com_image_url.$v_info['com_image']."\" width=\"100\" /></td>
										<td style=\"padding:10px 15px;\">
											<div style=\"height:30px; border-bottom:1px solid #dddddd;\">
												<div style=\"float:left; height:27px; line-height:27px;\">".$v_info['com_name']."&nbsp;&nbsp;<span style=\"color:#dddddd;\">|</span>&nbsp;&nbsp;대표 : ".$v_info['com_owner'];
	if( $v_info['class'] > 0 ){
		$venderNameTag .= "				&nbsp;&nbsp;<span style=\"color:#dddddd;\">|</span>&nbsp;&nbsp;등급 : ".$classList[$v_info['class']];
	}
	$venderNameTag .= "				</div>
												<div style=\"float:right; margin:0px; padding:0px;\">
													<a href=\"javascript:GoMinishop('../minishop.php?storeid=".$v_info['id']."')\"><img src=\"/images/common/btn_vender_allpr.gif\" border=\"0\" alt=\"전체상품보기\" /></a>
													<a href=\"javascript:custRegistMinishop();\"><img src=\"/images/common/btn_vender_addstor.gif\" border=\"0\" alt=\"단골매장등록\" /></a>
												</div>
											</div>

											<div>
												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" class=\"venderInfoTbl\">
													<caption>판매자 정보</caption>
													<tr>
														<th>사업자번호</th>
														<td>: ".$v_info['com_num']."</td>
														<th>통신판매업신고번호</th>
														<td>: ".$v_info['ec_num']."</td>
													</tr>
													<tr>
														<th>연락처</th>
														<td>: ".$v_info['com_tel']."</td>
														<th>E-mail</th>
														<td>: ".$v_info['p_email']."</td>
													</tr>
													<tr>
														<th>사업장소재지</th>
														<td>: ".$v_info['com_addr']."</td>
														<th>사업자구분</th>
														<td>: ".$v_info['com_type']."</td>
													</tr>
												</table>
											</div>
										</td>
									</tr>
								</table>";
}
array_push($pattern,'(\[VENDER_NAME_TAG\])');
array_push($replace,$venderNameTag);

// 상품 SNS 카운터 ( /front/productdetail.php )
array_push($pattern,'(\[PROD_SNS_CNT\])');
array_push($replace,$product_SNS_Count);

// 상품 공구 카운터  ( /front/productdetail.php )
array_push($pattern,'(\[PROD_GONGGU_CNT\])');
array_push($replace,$product_Gonggu_Count);

// 상품 공구 사용유무 체크
array_push($pattern,'(\[PROD_GONGGU_USED_START\])');
array_push($replace,$product_Gonggu_used_start);

array_push($pattern,'(\[PROD_GONGGU_USED_END\])');
array_push($replace,$product_Gonggu_used_end);


//상품상세 공통이벤트 관리
array_push($pattern,'(\[PRODUCTDETAIL_EVENT\])');
array_push($replace,$detailimg_body);


//예약판매 상품
array_push($pattern,'(\[IFRESERVATION\])');
array_push($replace,$reservationstart);

array_push($pattern,'(\[RESERVATION\])');
array_push($replace,$reservation);

array_push($pattern,'(\[ENDRESERVATION\])');
array_push($replace,$reservationend);



$body=preg_replace($pattern,$replace,$body);

echo $body;

?>