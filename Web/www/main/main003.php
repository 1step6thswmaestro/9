<?
if(substr(getenv("SCRIPT_NAME"),-12)=="/main002.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}

?>
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?></TITLE>
<link rel="P3Pv1" href="http://<?=$_ShopInfo->getShopurl()?>w3c/p3p.xml">
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
<?=$onload?>
//-->
</SCRIPT>

<? include($Dir."lib/style.php")?>

</HEAD>
<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
	include ($Dir.MainDir.$_data->menu_type.".php");
	//echo $_data->menu_type;
?>

<?
$main1_tag0_count = 2; // 신상품 이미지형 태그 출력 갯수
$main1_tag1_count = 2; // 인기상품 이미지A형 태그 출력 갯수
$main1_tag2_count = 2; // 추천상품 이미지A형 태그 출력 갯수
$main1_tag3_count = 2; // 특별상품 이미지A형 태그 출력 갯수

$main2_tag0_count = 3; // 신상품 리스트형 태그 출력 갯수
$main2_tag1_count = 3; // 인기상품 리스트형 태그 출력 갯수
$main2_tag2_count = 3; // 추천상품 리스트형 태그 출력 갯수
$main2_tag3_count = 3; // 특별상품 이미지A형 태그 출력 갯수

$main3_tag0_count = 2; // 신상품 이미지B형 태그 출력 갯수
$main3_tag1_count = 2; // 인기상품 이미지B형 태그 출력 갯수
$main3_tag2_count = 2; // 추천상품 이미지B형 태그 출력 갯수
$main3_tag3_count = 2; // 추천상품 이미지B형 태그 출력 갯수

########################## 인트로 #############################
$mainsec_strI="";

$imagepath=$Dir.DataDir."shopimages/etc/main_logo.gif";
$flashpath=$Dir.DataDir."shopimages/etc/main_logo.swf";

if (strlen($_data->shop_intro)==0) {
	$mainsec_strI.="<div align=center><img src='../images/003/main_visual_sample.gif'/></div>";
} else {
	if (file_exists($imagepath)) {
		$mainimg="<img src=\"".$imagepath."\" border=\"0\" align=\"absmiddle\">";
	} else {
		$mainimg="";
	}
	if (file_exists($flashpath)) {
		if (strpos($_data->shop_intro,"[MAINFLASH_")!==false) {
			$mainstart=strpos($_data->shop_intro,"[MAINFLASH_");
			$mainend=strpos($_data->shop_intro,"]",$mainstart);
			$swfsize=substr($_data->shop_intro,$mainstart+11,$mainend-$mainstart-11);
			$size=explode("X",$swfsize);
			$width=$size[0];
			$height=$size[1];
		}
		$mainflash="<script>flash_show('".$flashpath."','".$width."','".$height."');</script>";
	} else {
		$mainflash="";
	}
	$pattern=array("(\[DIR\])","(\[MAINIMG\])","/\[MAINFLASH_([0-9]{1,4})X([0-9]{1,4})\]/");
	$replace=array($Dir,$mainimg,$mainflash);
	$shop_intro=preg_replace($pattern,$replace,$_data->shop_intro);

	if (strpos(strtolower($shop_intro),"table")!=false || strlen($mainflash)>0)
		$mainsec_strI.=$shop_intro;
	else
		$mainsec_strI.=ereg_replace("\n","<br>",$shop_intro);
}


########################## 신상품 #############################
$mainsec_strN="";
$mainsec_strN.="<table border=0 cellpadding=0 cellspacing=0 align=center width=100% style=\"table-layout:fixed\">\n";
$mainsec_strN.="<tr>\n";
if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/main_new_title.gif")) {
	$mainsec_strN.="<td><img src=\"".$Dir.DataDir."design/main_new_title.gif\" border=\"0\" alt=\"신상품\"></td>\n";
} else {
	$mainsec_strN.="<td>\n";
	$mainsec_strN.="	<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
	$mainsec_strN.="		<TR>\n";
	$mainsec_strN.="			<TD></TD>\n";
	$mainsec_strN.="			<TD background=".$Dir."images/".$_data->icon_type."/main_new_title_bg.gif width=100%><IMG SRC=".$Dir."images/".$_data->icon_type."/main_new_title_img.gif ALT=></TD>\n";
	$mainsec_strN.="			<TD></TD>\n";
	$mainsec_strN.="		</TR>\n";
	$mainsec_strN.="	</TABLE>\n";
	$mainsec_strN.="</td>\n";
}
$mainsec_strN.="</tr>\n";
$mainsec_strN.="<tr>\n";
$mainsec_strN.="	<td>\n";
$mainsec_strN.="		<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
$mainsec_strN.="			<TR>\n";
$mainsec_strN.="				<TD></TD>\n";
$mainsec_strN.="				<TD width=100% align=center style='padding-top:15px'>\n";

$main_newprdt=explode("|",$_data->main_newprdt);
$main_new_num=$main_newprdt[0];
$main_new_cols=$main_newprdt[1];
$main_new_type=$main_newprdt[2];

$res = _getSpecialProducts('','1',$main_new_num);
if(count($res)){

	if($main_new_type=="I") {	//이미지A형
		$mainsec_strN.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" align=\"center\">\n";
		$table_width=floor(100/$main_new_cols);
		for($j=1;$j<=$main_new_cols;$j++){
			if($j>1) $mainsec_strN.="<col width=></col>\n";
			$mainsec_strN.="<col width=".$table_width."%></col>\n";
		}
		$mainsec_strN.="<tr>\n";

		//while($row=mysql_fetch_object($result)) {
		foreach($res as $i=>$row){

			// 예약상품 아이콘 추가
			$row->etctype = reservationEtcType($row->reservation,$row->etctype);

			// 도매 가격 적용 상품 아이콘
			$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";

			// 할인율 표시
			$discountRate = ( $row->discountRate > 0 ) ? "<strong>".$row->discountRate."</strong>%↓" : "";

			$memberpriceValue = $row->sellprice;
			$strikeStart = $strikeEnd = $memberprice = '';
			if($row->discountprices>0){
				$memberprice = number_format($row->sellprice - $row->discountprices);
				$strikeStart = "<strike>";
				$strikeEnd = "</strike>";
				$memberpriceValue = ($row->sellprice - $row->discountprices);
			}

			$tableSize = $_data->primg_minisize;
			if ($i!=0 && $i%$main_new_cols==0) {
				$mainsec_strN.= "</tr><tr><td colspan=\"".$main_new_cols."\" height=\"10\"></td></tr>\n";
			}
			if ($i!=0 && $i%$main_new_cols!=0) {
				$mainsec_strN.= "<td></td>";
			}

			$mainsec_strN.="<td valign=\"top\">\n";
			$mainsec_strN.="<TABLE cellSpacing=\"0\" cellPadding=\"0\" width=\"".$tableSize."\" border=\"0\" id=\"N".$row->productcode."\" onmouseover=\"quickfun_show(this,'N".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'N".$row->productcode."','none')\" class=\"prInfoBox\">\n";
			$mainsec_strN.="<TR>\n";
			$mainsec_strN.="	<TD class=\"prImage\" align=\"center\">";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
				$mainsec_strN.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";

				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $mainsec_strN.="height=\"".$_data->primg_minisize2."\" ";
					else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $mainsec_strN.="width=\"".$_data->primg_minisize."\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $mainsec_strN.="width=\"".$_data->primg_minisize."\" ";
					else if ($width[1]>=$_data->primg_minisize) $mainsec_strN.="height=\"".$_data->primg_minisize."\" ";
				}
			} else {
				$mainsec_strN.="<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			$mainsec_strN.="	></A></td>";
			$mainsec_strN.="</tr>\n";
			$mainsec_strN.="<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','N','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
			$mainsec_strN.="<tr>";
			$mainsec_strN.="	<td style=\"padding:5px 7px; word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>".(strlen($row->prmsg)?'<br /><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</td>\n";
			$mainsec_strN.="</tr>\n";

			//시중가 + 판매가 + 할인율 + 회원할인가
			$mainsec_strN.="<tr>
										<td style=\"padding:0px 7px 7px 7px; word-break:break-all;\">
											<table border=0 cellpadding=0 cellspacing=0 width=100%>
												<tr>
													<td>
			";
			if($row->consumerprice!=0) {
				//$mainsec_strN.="<li><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"mainconprice\"><strike>".number_format($row->consumerprice)."</strike>원</span></li>\n";
				$mainsec_strN.="<span class=\"mainconprice\"><strike>".number_format($row->consumerprice)."원</strike></span>\n";
			}

			// 회원 할인가가 있을 때 가격 class 변경
			if($memberprice > 0){
				$mainprpriceClass = "";
			}else{
				$mainprpriceClass = "mainprprice";
			}

			$mainsec_strN.="<span style=\"white-space:nowrap;\">".$strikeStart.dickerview($row->etctype,$wholeSaleIcon." <strong class=\"".$mainprpriceClass."\">".number_format($row->sellprice)."원</strong>".$strikeEnd)."</span>";
			$mainsec_strN.="</td>";

			if($row->discountRate > 0){
				$mainsec_strN.="<td align=\"right\" valign=\"bottom\" class=\"discount\">".$discountRate."</td>";
			}
			$mainsec_strN.="
					</tr>
				</table>
			";

			//회원할인가 적용
			if( $memberprice > 0 ) {
				//$mainsec_strN.="<tr>\n";
				$mainsec_strN.="	<div><span class=\"mainprprice\">".dickerview($row->etctype,$memberprice)."원</span> <img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" /></div>\n";
				//$mainsec_strN.="</tr>\n";
			}

			if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0"){
				$mainsec_strN.=soldout();
			}

			$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y");
			if($reserveconv>0) {
				//$mainsec_strN.="<tr>";
				$mainsec_strN.="	<div style=\"margin-top:5px;\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"vertical-ailgn:middle;\" /> <span class=\"mainreserve\">".number_format($reserveconv)."</span>원</div>\n";
				//$mainsec_strN.="</tr>\n";
			}

			$mainsec_strN.="	</td>\n";
			$mainsec_strN.="</tr>\n";

			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$main1_tag0_count;$ii++) {
					$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) {
							$mainsec_strN.="<tr>\n";
							$mainsec_strN.="	<td align=\"center\" style=\"padding-left:5px;padding-right:5px;word-break:break-all;\">\n";
							$mainsec_strN.="	<img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"letter-spacing:-0.5pt;\" class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						else {
							$mainsec_strN.="<FONT class=\"maintag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"letter-spacing:-0.5pt;\" class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						$jj++;
					}
				}
				if($jj!=0) {
					$mainsec_strN.="	</td>\n";
					$mainsec_strN.="</tr>\n";
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
				$mainsec_strN.="
					<tr>
						<td class=\"nameTagBox\">".$venderNameTag."</td>
					</tr>
				";
			}

			$mainsec_strN.="</table>\n";
			$mainsec_strN.="</td>\n";
			if ($i==$main_new_num) break;
		}
		if($i>0 && $i<$main_new_cols) {
			for($k=0; $k<($main_new_cols-$i); $k++) {
				$mainsec_strN.="<td></td>\n<td></td>\n";
			}
		}
		$mainsec_strN.="</tr>\n";
		$mainsec_strN.="</table>\n";

	} else if($main_new_type=="D") {	//이미지B형
		$mainsec_strN.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" align=\"center\">\n";
		$table_width=ceil(100/$main_new_cols);
		for($j=1;$j<=$main_new_cols;$j++) {
			if($j!=1)
				$mainsec_strN.="<col width=></col>\n";
			$mainsec_strN.="<col width=".$table_width."%></col>\n";
		}
		$mainsec_strN.="<tr>\n";
		//while($row=mysql_fetch_object($result)) {
		foreach($res as $i=>$row){
			$memberpriceValue = $row->sellprice;
			$strikeStart = $strikeEnd = $memberprice = '';
			if($row->discountprices>0){
				$memberprice = number_format($row->sellprice - $row->discountprices);
				$strikeStart = "<strike>";
				$strikeEnd = "</strike>";
				$memberpriceValue = ($row->sellprice - $row->discountprices);
			}

			$tableSize = $_data->primg_minisize;

			if ($i!=0) {
				if($i%$main_new_cols==0) {
					if($i>=$main_new_cols) {
						$mainsec_strN.="</tr><tr><td height=\"1\" colspan=\"".($main_new_cols*2-1)."\" bgcolor=\"#EDEDED\"></td></tr><tr>\n";
					} else {
						$mainsec_strN.="</tr><tr>\n";
					}
				} else {
					$mainsec_strN.="<td align=\"center\"><img src=\"".$Dir."images/common/main_product_lineb.gif\" border=\"0\"></td>\n";
				}
			}

			$mainsec_strN.="<td align=center style=\"padding:5px 0px;\">\n";
			$mainsec_strN.="<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" id=\"N".$row->productcode."\" onmouseover=\"quickfun_show(this,'N".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'N".$row->productcode."','none')\" class=\"prInfoBox2\">\n";
			$mainsec_strN.="<col width=\"".$tableSize."\"></col>\n";
			$mainsec_strN.="<col width=\"0\"></col>\n";
			$mainsec_strN.="<col width=\"100%\"></col>\n";
			$mainsec_strN.="<TR>\n";
			$mainsec_strN.="	<TD class=\"prImage\" align=\"center\" nowrap>";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
				$mainsec_strN.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $mainsec_strN.="height=\"".$_data->primg_minisize2."\" ";
					else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $mainsec_strN.="width=\"".$_data->primg_minisize."\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $mainsec_strN.="width=\"".$_data->primg_minisize."\" ";
					else if ($width[1]>=$_data->primg_minisize) $mainsec_strN.="height=\"".$_data->primg_minisize."\" ";
				}
			} else {
				$mainsec_strN.="<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			$mainsec_strN.="	></A></td>";
			$mainsec_strN.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','N','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
			$mainsec_strN.="	<TD style=\"padding-left:15px; word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br /><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</A><br />\n";

			if($row->consumerprice!=0) {
				//$mainsec_strN.="<img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><FONT class=\"mainconprice\"><strike>".number_format($row->consumerprice)."</strike>원</font>\n";
				$mainsec_strN.="<FONT class=\"mainconprice\"><strike>".number_format($row->consumerprice)."</strike>원</font>\n";
			}

			// 회원 할인가가 있을 때 가격 class 변경
			if($memberprice > 0){
				$mainprpriceClass = "";
			}else{
				$mainprpriceClass = "mainprprice";
			}
			// 할인율 표시
			$discountRate = ( $row->discountRate > 0 ) ? "<strong>".$row->discountRate."</strong>%↓" : "";

			$mainsec_strN.=$strikeStart.dickerview($row->etctype,$wholeSaleIcon."<strong class=\"".$mainprpriceClass."\">".number_format($row->sellprice)."원</strong>".$strikeEnd);
			$mainsec_strN.="<span class=\"discount\">".$discountRate."</span>";

			if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $mainsec_strN.=soldout();

			//회원할인가 적용
			if( $memberprice > 0 ) $mainsec_strN.="<br /><font class=\"mainprprice\">".dickerview($row->etctype,$memberprice."원")."</font> <img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" />";

			$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y");
			if($reserveconv>0) {
				$mainsec_strN.="<br /><font class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."원</font>\n";
			}
			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$main3_tag0_count;$ii++) {
					$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) {
							$mainsec_strN.="<br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						else {
							$mainsec_strN.="<FONT class=\"maintag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						$jj++;
					}
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

				// 네임텍 출력
				$mainsec_strN .= "	<div class=\"nameTagBox2\"><span class=\"name\">".$v_info['com_name']."</span> <span class=\"owner\">(".$v_info['com_owner'].")</span></div>";
				$mainsec_strN .= "	<div><a href=\"javascript:GoMinishop('/minishop.php?storeid=".$v_info['id']."')\"><img src=\"/images/common/icon_vender_go.gif\" border=\"0\" align=\"absmiddle\" alt=\"전체상품보기\" /></a></div>";
			}

			$mainsec_strN.="	</td>\n";
			$mainsec_strN.="</tr>\n";
			$mainsec_strN.="</table>\n";
			$mainsec_strN.="</td>\n";
			if ($i==$main_new_num) break;
		}
		if($i>0 && $i<$main_new_cols) {
			for($k=0; $k<($main_new_cols-$i); $k++) {
				$mainsec_strN.="<td></td>\n<td></td>\n";
			}
		}
		$mainsec_strN.="</tr>\n";
		$mainsec_strN.="</table>\n";

	} else if($main_new_type=="L") {	//리스트형
		$mainsec_strN.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" align=\"center\">\n";
		$mainsec_strN.="<col width=\"23%\"></col>\n";
		$mainsec_strN.="<col width=\"0\"></col>\n";
		$mainsec_strN.="<col width=\"32%\"></col>\n";
		$mainsec_strN.="<col width=\"16%\"></col>\n";
		$mainsec_strN.="<col width=\"16%\"></col>\n";
		$mainsec_strN.="<col width=\"13%\"></col>\n";
		//while($row=mysql_fetch_object($result)) {
		foreach($res as $i=>$row){
			$memberpriceValue = $row->sellprice;
			$strikeStart = $strikeEnd = $memberprice = '';
			if($row->discountprices>0){
				$memberprice = number_format($row->sellprice - $row->discountprices);
				$strikeStart = "<strike>";
				$strikeEnd = "</strike>";
				$memberpriceValue = ($row->sellprice - $row->discountprices);
			}

			if($i!=0) {
				$mainsec_strN.="<tr>\n";
				$mainsec_strN.="	<td height=\"1\" bgcolor=\"#EDEDED\" colspan=\"6\"></td>\n";
				$mainsec_strN.="</tr>\n";
			}
			$mainsec_strN.="<tr align=\"center\" id=\"N".$row->productcode."\" onmouseover=\"quickfun_show(this,'N".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'N".$row->productcode."','none')\">\n";
			$mainsec_strN.="	<td style=\"padding-top:1px;padding-bottom:1px;\">";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
				$mainsec_strN.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $mainsec_strN.="height=\"".$_data->primg_minisize2."\" ";
					else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $mainsec_strN.="width=\"".$_data->primg_minisize."\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $mainsec_strN.="width=\"".$_data->primg_minisize."\" ";
					else if ($width[1]>=$_data->primg_minisize) $mainsec_strN.="height=\"".$_data->primg_minisize."\" ";
				}
			} else {
				$mainsec_strN.="<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			$mainsec_strN.="	></A></td>";
			$mainsec_strN.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','N','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
			$mainsec_strN.="	<td style=\"padding-right:5px;word-break:break-all;\" align=\"left\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</A>";
			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$main2_tag0_count;$ii++) {
					$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) {
							$mainsec_strN.="<br><br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"letter-spacing:-0.5pt;\" class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						else {
							$mainsec_strN.="<FONT class=\"maintag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"letter-spacing:-0.5pt;\" class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						$jj++;
					}
				}
			}
			$mainsec_strN.="	</td>\n";
			$mainsec_strN.="	<TD style=\"word-break:break-all;\" class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
			$mainsec_strN.="	<td style=\"word-break:break-all;\" class=\"mainprprice\">".$strikeStart.dickerview($row->etctype,$wholeSaleIcon." <strong>".number_format($row->sellprice)."원</strong>".$strikeEnd).$discountRate;
			if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $mainsec_strN.=soldout();

			//회원할인가 적용
			if( $memberprice > 0 ) $mainsec_strN.="<img src=\"".$Dir."images/common/memsale_icon.gif\" style=\"position:relative; top:0.1em;\" alt=\"\" /> ".dickerview($row->etctype,$memberprice."원");

			$mainsec_strN.="	</td>\n";
			$mainsec_strN.="	<TD style=\"word-break:break-all;\" class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format(getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y"))."원</td>\n";
			$mainsec_strN.="</tr>\n";
			if ($i==$main_new_num) break;
		}
		$mainsec_strN.="</table>\n";
	}
	//mysql_free_result($result);
}
$mainsec_strN.="		</td>\n";
$mainsec_strN.="		<TD></TD>\n";
$mainsec_strN.="	</TR>\n";
$mainsec_strN.="	</TABLE>\n";
$mainsec_strN.="	</td>\n";
$mainsec_strN.="</tr>\n";
$mainsec_strN.="<tr>\n";
$mainsec_strN.="	<td>\n";
$mainsec_strN.="	<TABLE cellSpacing=0 cellPadding=0 width=100%>\n";
$mainsec_strN.="	<TR>\n";
$mainsec_strN.="		<TD></TD>\n";
$mainsec_strN.="		<TD width=100%></TD>\n";
$mainsec_strN.="		<TD></TD>\n";
$mainsec_strN.="	</TR>\n";
$mainsec_strN.="	</TABLE>\n";
$mainsec_strN.="	</td>\n";
$mainsec_strN.="</tr>\n";
$mainsec_strN.="</table>\n";


########################## 인기상품 #############################
$mainsec_strB="";
$mainsec_strB.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
$mainsec_strB.="<tr>\n";
if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/main_best_title.gif")) {
	$mainsec_strB.="<td><img src=\"".$Dir.DataDir."design/main_best_title.gif\" border=\"0\" alt=\"인기상품\"></td>\n";
} else {
	$mainsec_strB.="<td>\n";
	$mainsec_strB.="<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
	$mainsec_strB.="<TR>\n";
	$mainsec_strB.="	<TD height=20></TD>\n";
	$mainsec_strB.="</TR>\n";
	$mainsec_strB.="<TR>\n";
	$mainsec_strB.="	<TD></TD>\n";
	$mainsec_strB.="	<TD width=100% background=".$Dir."images/".$_data->icon_type."/main_best_title_bg.gif><IMG SRC=".$Dir."images/".$_data->icon_type."/main_best_title_img.gif ALT=></TD>\n";
	$mainsec_strB.="	<TD></TD>\n";
	$mainsec_strB.="</TR>\n";
	$mainsec_strB.="</TABLE>\n";
	$mainsec_strB.="</td>\n";
}
$mainsec_strB.="</tr>\n";
$mainsec_strB.="<tr>\n";
$mainsec_strB.="	<td>\n";
$mainsec_strB.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
$mainsec_strB.="	<TR>\n";
$mainsec_strB.="		<TD valign=top></TD>\n";
$mainsec_strB.="		<TD width=100% align=center style='padding-top:15px'>\n";

$main_bestprdt=explode("|",$_data->main_bestprdt);
$main_best_num=$main_bestprdt[0];
$main_best_cols=$main_bestprdt[1];
$main_best_type=$main_bestprdt[2];
$res = _getSpecialProducts('','2',$main_best_num);
if(count($res)) {
	if($main_best_type=="I") {	//이미지A형
		$mainsec_strB.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" align=\"center\">\n";
		$table_width=ceil(100/$main_best_cols);
		for($j=1;$j<=$main_best_cols;$j++) {
			if($j>1) $mainsec_strB.="<col width=></col>\n";
			$mainsec_strB.="<col width=".$table_width."%></col>\n";
		}
		$mainsec_strB.="<tr>\n";
		//while($row=mysql_fetch_object($result)) {
		foreach($res as $i=>$row){

			// 예약상품 아이콘 추가
			$row->etctype = reservationEtcType($row->reservation,$row->etctype);

			// 도매 가격 적용 상품 아이콘
			$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";

			// 할인율 표시
			$discountRate = ( $row->discountRate > 0 ) ? "<strong>".$row->discountRate."</strong>%↓" : "";

			$memberpriceValue = $row->sellprice;
			$strikeStart = $strikeEnd = $memberprice = '';
			if($row->discountprices>0){
				$memberprice = number_format($row->sellprice - $row->discountprices);
				$strikeStart = "<strike>";
				$strikeEnd = "</strike>";
				$memberpriceValue = ($row->sellprice - $row->discountprices);
			}
			#####################상품별 회원할인율 적용 끝 #######################################

			$tableSize = $_data->primg_minisize;
			if ($i!=0 && $i%$main_best_cols==0) {
				$mainsec_strB.= "</tr><tr><td colspan=\"".$main_best_cols."\" height=\"10\"></td></tr>\n";
			}
			if ($i!=0 && $i%$main_best_cols!=0) {
				$mainsec_strB.= "<td></td>";
			}

			$mainsec_strB.="<td valign=\"top\">\n";
			$mainsec_strB.="<TABLE cellSpacing=\"0\" cellPadding=\"0\" width=\"".$tableSize."\" border=\"0\" id=\"B".$row->productcode."\" onmouseover=\"quickfun_show(this,'B".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'B".$row->productcode."','none')\" class=\"prInfoBox\">\n";

			$mainsec_strB.="<TR>\n";
			$mainsec_strB.="	<TD class=\"prImage\" align=\"center\">";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
				$mainsec_strB.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $mainsec_strB.="height=\"".$_data->primg_minisize2."\" ";
					else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $mainsec_strB.="width=\"".$_data->primg_minisize."\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $mainsec_strB.="width=\"".$_data->primg_minisize."\" ";
					else if ($width[1]>=$_data->primg_minisize) $mainsec_strB.="height=\"".$_data->primg_minisize."\" ";
				}
			} else {
				$mainsec_strB.="<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			$mainsec_strB.="	></A></td>";
			$mainsec_strB.="</tr>\n";

			$mainsec_strB.="<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','B','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";

			$mainsec_strB.="<tr>";
			$mainsec_strB.="	<td style=\"padding:5px 7px; word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>".(strlen($row->prmsg)?'<br><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</td>\n";
			$mainsec_strB.="</tr>\n";

			//시중가 + 판매가 + 할인율 + 회원할인가
			$mainsec_strB.="<tr>
										<td style=\"padding:0px 7px 7px 7px; word-break:break-all;\">
											<table border=0 cellpadding=0 cellspacing=0 width=100%>
												<tr>
													<td>
			";
			if($row->consumerprice!=0) {
				$mainsec_strB.="			<span class=\"mainconprice\"><strike>".number_format($row->consumerprice)."원</strike></span>\n";
			}

			// 회원 할인가가 있을 때 가격 class 변경
			if($memberprice > 0){
				$mainprpriceClass = "";
			}else{
				$mainprpriceClass = "mainprprice";
			}

			//$mainsec_strB.="<tr>\n";
			$mainsec_strB.="<span style=\"white-space:nowrap;\">".$strikeStart.dickerview($row->etctype,$wholeSaleIcon." <strong class=\"".$mainprpriceClass."\">".number_format($row->sellprice)."원</strong>".$strikeEnd)."</span>";
			//$mainsec_strB.="	</td>\n";
			//$mainsec_strB.="</tr>\n";
			$mainsec_strB.="</td>";

			if($row->discountRate > 0){
				$mainsec_strB.="<td align=\"right\" valign=\"bottom\" class=\"discount\">".$discountRate."</td>";
			}
			$mainsec_strB.="
					</tr>
				</table>
			";

			//회원할인가 적용
			if( $memberprice > 0 ) {
				//$mainsec_strB.="<tr>\n";
				$mainsec_strB.="	<div><span class=\"mainprprice\">".dickerview($row->etctype,$memberprice)."</span><strong>원</strong> <img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" /></div>\n";
				//$mainsec_strB.="	</td>\n";
				//$mainsec_strB.="</tr>\n";
			}

			if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0"){
				$mainsec_strB.=soldout();
			}

			$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y");
			if($reserveconv>0) {
				//$mainsec_strB.="<tr>";
				$mainsec_strB.="	<div style=\"margin-top:5px;\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"vertical-ailgn:middle;\" /> <span class=\"mainreserve\">".number_format($reserveconv)."</span>원</div>\n";
				//$mainsec_strB.="</tr>\n";
			}

			$mainsec_strN.="	</td>\n";
			$mainsec_strN.="</tr>\n";

			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$main1_tag1_count;$ii++) {
					$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) {
							$mainsec_strB.="<tr>\n";
							$mainsec_strB.="	<td align=\"center\" style=\"padding-left:5px;padding-right:5px;word-break:break-all;\">\n";
							$mainsec_strB.="	<img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"letter-spacing:-0.5pt;\" class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						else {
							$mainsec_strB.="<FONT class=\"maintag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"letter-spacing:-0.5pt;\" class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						$jj++;
					}
				}
				if($jj!=0) {
					$mainsec_strB.="	</td>\n";
					$mainsec_strB.="</tr>\n";
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
				$mainsec_strB.="
					<tr>
						<td class=\"nameTagBox\">".$venderNameTag."</td>
					</tr>
				";
			}

			$mainsec_strB.="</table>\n";
			$mainsec_strB.="</td>\n";

			if ($i==$main_best_num) break;
		}
		if($i>0 && $i<$main_best_cols) {
			for($k=0; $k<($main_best_cols-$i); $k++) {
				$mainsec_strB.="<td></td>\n<td></td>\n";
			}
		}
		$mainsec_strB.="</tr>\n";
		$mainsec_strB.="</table>\n";

	} else if($main_best_type=="D") {	//이미지B형
		$mainsec_strB.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" align=\"center\">\n";
		$table_width=ceil(100/$main_best_cols);
		for($j=1;$j<=$main_best_cols;$j++) {
			if($j!=1) {$mainsec_strB.="<col width=></col>\n";}
			$mainsec_strB.="<col width=".$table_width."%></col>\n";
		}
		$mainsec_strB.="<tr>\n";
		//while($row=mysql_fetch_object($result)) {
		foreach($res as $i=>$row){
			$memberpriceValue = $row->sellprice;
			$strikeStart = $strikeEnd = $memberprice = '';
			if($row->discountprices>0){
				$memberprice = number_format($row->sellprice - $row->discountprices);
				$strikeStart = "<strike>";
				$strikeEnd = "</strike>";
				$memberpriceValue = ($row->sellprice - $row->discountprices);
			}

			$tableSize = $_data->primg_minisize;

			if ($i!=0) {
				if($i%$main_best_cols==0) {
					if($i>=$main_best_cols) {
						$mainsec_strB.="</tr><tr><td height=\"1\" colspan=\"".($main_best_cols*2-1)."\" bgcolor=\"#EDEDED\"></td></tr><tr>\n";
					} else {
						$mainsec_strB.="</tr><tr>\n";
					}
				} else {
					$mainsec_strB.="<td align=\"center\"><img src=\"".$Dir."images/common/main_product_lineb.gif\" border=\"0\"></td>\n";
				}
			}

			$mainsec_strB.="<td align=center style=\"padding:5px 0px;\">\n";
			$mainsec_strB.="<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" id=\"B".$row->productcode."\" onmouseover=\"quickfun_show(this,'B".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'B".$row->productcode."','none')\" class=\"prInfoBox2\">\n";
			$mainsec_strB.="<col width=\"".$tableSize."\"></col>\n";
			$mainsec_strB.="<col width=\"0\"></col>\n";
			$mainsec_strB.="<col width=></col>\n";

			$mainsec_strB.="<TR>\n";
			$mainsec_strB.="	<TD class=\"prImage\" align=\"center\" nowrap>";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
				$mainsec_strB.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $mainsec_strB.="height=\"".$_data->primg_minisize2."\" ";
					else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $mainsec_strB.="width=\"".$_data->primg_minisize."\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $mainsec_strB.="width=\"".$_data->primg_minisize."\" ";
					else if ($width[1]>=$_data->primg_minisize) $mainsec_strB.="height=\"".$_data->primg_minisize."\" ";
				}
			} else {
				$mainsec_strB.="<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			$mainsec_strB.="	></A></td>";
			$mainsec_strB.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','B','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
			$mainsec_strB.="	<TD style=\"padding-left:15px; word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br /><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</A><br />\n";

			if($row->consumerprice!=0) {
				$mainsec_strB.="<img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><FONT class=\"mainconprice\"><strike>".number_format($row->consumerprice)."</strike>원</font>&nbsp;\n";
			}

			// 회원 할인가가 있을 때 가격 class 변경
			if($memberprice > 0){
				$mainprpriceClass = "";
			}else{
				$mainprpriceClass = "mainprprice";
			}
			// 할인율 표시
			$discountRate = ( $row->discountRate > 0 ) ? "<strong>".$row->discountRate."</strong>%↓" : "";

			$mainsec_strB.=$strikeStart.dickerview($row->etctype,$wholeSaleIcon." <strong class=\"".$mainprpriceClass."\">".number_format($row->sellprice)."</strong><strong>원</strong>".$strikeEnd);
			$mainsec_strB.="<span class=\"discount\">".$discountRate."</span>";

			if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $mainsec_strB.=soldout();

			//회원할인가 적용
			if( $memberprice > 0 ) $mainsec_strB.="<br /><font class=\"mainprprice\">".dickerview($row->etctype,$memberprice."원")."</font> <img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" />";

			$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y");
			if($reserveconv>0) {
				$mainsec_strB.="<br /><font class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."원</font>\n";
			}

			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$main3_tag1_count;$ii++) {
					$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) {
							$mainsec_strB.="<br /><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						else {
							$mainsec_strB.="<FONT class=\"maintag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						$jj++;
					}
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

				// 네임텍 출력
				$mainsec_strB .= "	<div class=\"nameTagBox2\"><span class=\"name\">".$v_info['com_name']."</span> <span class=\"owner\">(".$v_info['com_owner'].")</span></div>";
				$mainsec_strB .= "	<div><a href=\"javascript:GoMinishop('/minishop.php?storeid=".$v_info['id']."')\"><img src=\"/images/common/icon_vender_go.gif\" border=\"0\" align=\"absmiddle\" alt=\"전체상품보기\" /></a></div>";
			}

			$mainsec_strB.="	</td>\n";
			$mainsec_strB.="</tr>\n";
			$mainsec_strB.="</table>\n";
			$mainsec_strB.="</td>\n";
			if ($i==$main_best_num) break;
		}
		if($i>0 && $i<$main_best_cols) {
			for($k=0; $k<($main_best_cols-$i); $k++) {
				$mainsec_strB.="<td></td>\n<td></td>\n";
			}
		}
		$mainsec_strB.="</tr>\n";
		$mainsec_strB.="</table>\n";

	} else if($main_best_type=="L") {	//리스트형
		$mainsec_strB.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" align=\"center\">\n";
		$mainsec_strB.="<col width=\"23%\"></col>\n";
		$mainsec_strB.="<col width=\"0\"></col>\n";
		$mainsec_strB.="<col width=\"32%\"></col>\n";
		$mainsec_strB.="<col width=\"16%\"></col>\n";
		$mainsec_strB.="<col width=\"16%\"></col>\n";
		$mainsec_strB.="<col width=\"13%\"></col>\n";
		//while($row=mysql_fetch_object($result)) {
		foreach($res as $i=>$row){
			$memberpriceValue = $row->sellprice;
			$strikeStart = $strikeEnd = $memberprice = '';
			if($row->discountprices>0){
				$memberprice = number_format($row->sellprice - $row->discountprices);
				$strikeStart = "<strike>";
				$strikeEnd = "</strike>";
				$memberpriceValue = ($row->sellprice - $row->discountprices);
			}
			if($i!=0) {
				$mainsec_strB.="<tr>\n";
				$mainsec_strB.="	<td height=\"1\" bgcolor=\"#EDEDED\" colspan=\"6\"></td>\n";
				$mainsec_strB.="</tr>\n";
			}
			$mainsec_strB.="<tr align=\"center\" id=\"B".$row->productcode."\" onmouseover=\"quickfun_show(this,'B".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'B".$row->productcode."','none')\">\n";
			$mainsec_strB.="	<td style=\"padding-top:1px;padding-bottom:1px;\">";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
				$mainsec_strB.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $mainsec_strB.="height=\"".$_data->primg_minisize2."\" ";
					else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $mainsec_strB.="width=\"".$_data->primg_minisize."\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $mainsec_strB.="width=\"".$_data->primg_minisize."\" ";
					else if ($width[1]>=$_data->primg_minisize) $mainsec_strB.="height=\"".$_data->primg_minisize."\" ";
				}
			} else {
				$mainsec_strB.="<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			$mainsec_strB.="	></A></td>";
			$mainsec_strB.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','B','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
			$mainsec_strB.="	<td style=\"padding-right:5px;word-break:break-all;\" align=\"left\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</A>";
			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$main2_tag1_count;$ii++) {
					$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) {
							$mainsec_strB.="<br><br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"letter-spacing:-0.5pt;\" class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						else {
							$mainsec_strB.="<FONT class=\"maintag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"letter-spacing:-0.5pt;\" class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						$jj++;
					}
				}
			}
			$mainsec_strB.="	</td>\n";
			$mainsec_strB.="	<TD style=\"word-break:break-all;\" class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
			$mainsec_strB.="	<td style=\"word-break:break-all;\" class=\"mainprprice\">".$strikeStart.dickerview($row->etctype,$wholeSaleIcon." <strong>".number_format($row->sellprice)."원</strong>".$strikeEnd).$discountRate;
			if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $mainsec_strB.=soldout();

			//회원할인가 적용
			if( $memberprice > 0 ) $mainsec_strB.="<img src=\"".$Dir."images/common/memsale_icon.gif\" style=\"position:relative; top:0.1em;\" alt=\"\" /> ".dickerview($row->etctype,$memberprice."원");

			$mainsec_strB.="	</td>\n";
			$mainsec_strB.="	<TD style=\"word-break:break-all;\" class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format(getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y"))."원</td>\n";
			$mainsec_strB.="</tr>\n";
			if ($i==$main_best_num) break;
		}
		$mainsec_strB.="</table>\n";
	}
	//mysql_free_result($result);
}
$mainsec_strB.="		</td>\n";
$mainsec_strB.="		<TD valign=top></TD>\n";
$mainsec_strB.="	</TR>\n";
$mainsec_strB.="	</TABLE>\n";
$mainsec_strB.="	</td>\n";
$mainsec_strB.="</tr>\n";
$mainsec_strB.="</table>\n";


########################## 추천상품 #############################
$mainsec_strH="";
$mainsec_strH.="<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
$mainsec_strH.="<tr>\n";
if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/main_hot_title.gif")) {
	$mainsec_strH.="<td><img src=\"".$Dir.DataDir."design/main_hot_title.gif\" border=\"0\" alt=\"추천상품\"></td>\n";
} else {
	$mainsec_strH.="<td>\n";
	$mainsec_strH.="<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
	$mainsec_strH.="<TR>\n";
	$mainsec_strH.="	<TD height=20></TD>\n";
	$mainsec_strH.="</TR>\n";
	$mainsec_strH.="<TR>\n";
	$mainsec_strH.="	<TD></TD>\n";
	$mainsec_strH.="	<TD width=100% background=".$Dir."images/".$_data->icon_type."/main_hot_title_bg.gif><IMG SRC=".$Dir."images/".$_data->icon_type."/main_hot_title_img.gif ALT=></TD>\n";
	$mainsec_strH.="	<TD></TD>\n";
	$mainsec_strH.="</TR>\n";
	$mainsec_strH.="</TABLE>\n";
	$mainsec_strH.="</td>\n";
}
$mainsec_strH.="</tr>\n";
$mainsec_strH.="<tr>\n";
$mainsec_strH.="	<td>\n";
$mainsec_strH.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
$mainsec_strH.="	<TR>\n";
$mainsec_strH.="		<TD></TD>\n";
$mainsec_strH.="		<TD width=100% align=center style='padding-top:15px'>\n";

$main_hotprdt=explode("|",$_data->main_hotprdt);
$main_hot_num=$main_hotprdt[0];
$main_hot_cols=$main_hotprdt[1];
$main_hot_type=$main_hotprdt[2];
$res = _getSpecialProducts('','3',$main_hot_num);
if(count($res)) {
	if($main_hot_type=="I") {	//이미지A형
		$mainsec_strH.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" align=\"center\">\n";
		$table_width=ceil(100/$main_hot_cols);
		for($j=1;$j<=$main_hot_cols;$j++) {
			if($j>1) $mainsec_strH.="<col width=></col>\n";
			$mainsec_strH.="<col width=".$table_width."%></col>\n";
		}
		$mainsec_strH.="<tr>\n";
		//while($row=mysql_fetch_object($result)) {
		foreach($res as $i=>$row){

			// 예약상품 아이콘 추가
			$row->etctype = reservationEtcType($row->reservation,$row->etctype);

			// 도매 가격 적용 상품 아이콘
			$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";

			// 할인율 표시
			$discountRate = ( $row->discountRate > 0 ) ? "<strong>".$row->discountRate."</strong>%↓" : "";

			$memberpriceValue = $row->sellprice;
			$strikeStart = $strikeEnd = $memberprice = '';
			if($row->discountprices>0){
				$memberprice = number_format($row->sellprice - $row->discountprices);
				$strikeStart = "<strike>";
				$strikeEnd = "</strike>";
				$memberpriceValue = ($row->sellprice - $row->discountprices);
			}

			$tableSize = $_data->primg_minisize;
			if ($i!=0 && $i%$main_hot_cols==0) {
				$mainsec_strH.= "</tr><tr><td colspan=\"".$main_hot_cols."\" height=\"10\"></td></tr>\n";
			}

			if ($i!=0 && $i%$main_hot_cols!=0) {
				$mainsec_strH.= "<td></td>";
			}

			$mainsec_strH.="<td valign=\"top\">\n";
			$mainsec_strH.="<TABLE cellSpacing=\"0\" cellPadding=\"0\" width=\"".$tableSize."\" border=\"0\" id=\"H".$row->productcode."\" onmouseover=\"quickfun_show(this,'H".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'H".$row->productcode."','none')\" class=\"prInfoBox\">\n";

			$mainsec_strH.="<TR>\n";
			$mainsec_strH.="	<TD class=\"prImage\" align=\"center\">";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
				$mainsec_strH.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $mainsec_strH.="height=\"".$_data->primg_minisize2."\" ";
					else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $mainsec_strH.="width=\"".$_data->primg_minisize."\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $mainsec_strH.="width=\"".$_data->primg_minisize."\" ";
					else if ($width[1]>=$_data->primg_minisize) $mainsec_strH.="height=\"".$_data->primg_minisize."\" ";
				}
			} else {
				$mainsec_strH.="<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			$mainsec_strH.="	></A></td>";
			$mainsec_strH.="</tr>\n";

			$mainsec_strH.="<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','H','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";

			$mainsec_strH.="<tr>";
			$mainsec_strH.="	<td style=\"padding:5px 7px; word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>".(strlen($row->prmsg)?'<br /><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</td>\n";
			$mainsec_strH.="</tr>\n";

			//시중가 + 판매가 + 할인율 + 회원할인가
			$mainsec_strH.="<tr>
										<td style=\"padding:0px 7px 7px 7px; word-break:break-all;\">
											<table border=0 cellpadding=0 cellspacing=0 width=100%>
												<tr>
													<td>
			";
			if($row->consumerprice!=0) {
				$mainsec_strH.="<span class=\"mainconprice\"><strike>".number_format($row->consumerprice)."원</strike></span>\n";
			}

			// 회원 할인가가 있을 때 가격 class 변경
			if($memberprice > 0){
				$mainprpriceClass = "";
			}else{
				$mainprpriceClass = "mainprprice";
			}

			$mainsec_strH.="<span style=\"white-space:nowrap;\">".$strikeStart.dickerview($row->etctype,$wholeSaleIcon." <strong class=\"".$mainprpriceClass."\">".number_format($row->sellprice)."원</strong>".$strikeEnd)."</span>";
			$mainsec_strH.="</td>";

			if($row->discountRate > 0){
				$mainsec_strH.="<td align=\"right\" valign=\"bottom\" class=\"discount\">".$discountRate."</td>";
			}
			$mainsec_strH.="
					</tr>
				</table>
			";

			//회원할인가 적용
			if( $memberprice > 0 ) {
				$mainsec_strH.="	<div><span class=\"mainprprice\">".dickerview($row->etctype,$memberprice)."</span><strong>원</strong> <img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" /></div>\n";
			}

			if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0"){
				$mainsec_strH.=soldout();
			}

			$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y");
			if($reserveconv>0) {
				$mainsec_strH.="	<div style=\"margin-top:5px;\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"vertical-ailgn:middle;\" /> <span class=\"mainreserve\">".number_format($reserveconv)."</span>원</div>\n";
			}

			$mainsec_strH.="	</td>\n";
			$mainsec_strH.="</tr>\n";

			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$main1_tag2_count;$ii++) {
					$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) {
							$mainsec_strH.="<tr>\n";
							$mainsec_strH.="	<td align=\"center\" style=\"padding-left:5px;padding-right:5px;word-break:break-all;\">\n";
							$mainsec_strH.="	<img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"letter-spacing:-0.5pt;\" class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						else {
							$mainsec_strH.="<FONT class=\"maintag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"letter-spacing:-0.5pt;\" class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						$jj++;
					}
				}
				if($jj!=0) {
					$mainsec_strH.="	</td>\n";
					$mainsec_strH.="</tr>\n";
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
				$mainsec_strH.="
					<tr>
						<td class=\"nameTagBox\">".$venderNameTag."</td>
					</tr>
				";
			}

			$mainsec_strH.="</table>\n";
			$mainsec_strH.="</td>\n";

			if ($i==$main_hot_num) break;
		}
		if($i>0 && $i<$main_hot_cols) {
			for($k=0; $k<($main_hot_cols-$i); $k++) {
				$mainsec_strH.="<td></td>\n<td></td>\n";
			}
		}
		$mainsec_strH.="</tr>\n";
		$mainsec_strH.="</table>\n";

	} else if($main_hot_type=="D") {	//이미지B형
		$mainsec_strH.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" align=\"center\">\n";
		$table_width=ceil(100/$main_hot_cols);
		for($j=1;$j<=$main_hot_cols;$j++) {
			if($j!=1) {$mainsec_strH.="<col width=></col>\n";}
			$mainsec_strH.="<col width=".$table_width."%></col>\n";
		}
		$mainsec_strH.="<tr>\n";
		//while($row=mysql_fetch_object($result)) {
		foreach($res as $i=>$row){
			$memberpriceValue = $row->sellprice;
			$strikeStart = $strikeEnd = $memberprice = '';
			if($row->discountprices>0){
				$memberprice = number_format($row->sellprice - $row->discountprices);
				$strikeStart = "<strike>";
				$strikeEnd = "</strike>";
				$memberpriceValue = ($row->sellprice - $row->discountprices);
			}

			$tableSize = $_data->primg_minisize;

			if ($i!=0) {
				if($i%$main_hot_cols==0) {
					if($i>=$main_hot_cols) {
						$mainsec_strH.="</tr><tr><td height=\"1\" colspan=\"".($main_hot_cols*2-1)."\" bgcolor=\"#EDEDED\"></td></tr><tr>\n";
					} else {
						$mainsec_strH.="</tr><tr>\n";
					}
				} else {
					$mainsec_strH.="<td align=\"center\"><img src=\"".$Dir."images/common/main_product_lineb.gif\" border=\"0\"></td>\n";
				}
			}

			$mainsec_strH.="<td align=center style=\"padding:5px 0px;\">\n";
			$mainsec_strH.="<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" id=\"H".$row->productcode."\" onmouseover=\"quickfun_show(this,'H".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'H".$row->productcode."','none')\" class=\"prInfoBox2\">\n";
			$mainsec_strH.="<col width=\"".$tableSize."\"></col>\n";
			$mainsec_strH.="<col width=\"0\"></col>\n";
			$mainsec_strH.="<col width=\"100%\"></col>\n";
			$mainsec_strH.="<TR>\n";
			$mainsec_strH.="	<TD class=\"prImage\" align=\"center\" nowrap>";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
				$mainsec_strH.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $mainsec_strH.="height=\"".$_data->primg_minisize2."\" ";
					else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $mainsec_strH.="width=\"".$_data->primg_minisize."\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $mainsec_strH.="width=\"".$_data->primg_minisize."\" ";
					else if ($width[1]>=$_data->primg_minisize) $mainsec_strH.="height=\"".$_data->primg_minisize."\" ";
				}
			} else {
				$mainsec_strH.="<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			$mainsec_strH.="	></A></td>";
			$mainsec_strH.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','H','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
			$mainsec_strH.="	<TD style=\"padding-left:15px;word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</A><br />\n";

			if($row->consumerprice!=0) {
				$mainsec_strH.="<img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><FONT class=\"mainconprice\"><strike>".number_format($row->consumerprice)."</strike>원</font>\n";
			}

			// 회원 할인가가 있을 때 가격 class 변경
			if($memberprice > 0){
				$mainprpriceClass = "";
			}else{
				$mainprpriceClass = "mainprprice";
			}
			// 할인율 표시
			$discountRate = ( $row->discountRate > 0 ) ? "<strong>".$row->discountRate."</strong>%↓" : "";

			$mainsec_strH.=$strikeStart.dickerview($row->etctype,$wholeSaleIcon."<strong class=\"".$mainprpriceClass."\">".number_format($row->sellprice)."원</strong>".$strikeEnd);
			$mainsec_strH.="<span class=\"discount\">".$discountRate."</span>";

			if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $mainsec_strH.=soldout();

			//회원할인가 적용
			if( $memberprice > 0 ) $mainsec_strH.="<br /><font class=\"mainprprice\">".dickerview($row->etctype,$memberprice."원")."</font> <img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" />";

			$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y");
			if($reserveconv>0) {
				$mainsec_strH.="<br><font class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."원</font>\n";
			}

			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$main3_tag2_count;$ii++) {
					$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) {
							$mainsec_strH.="<br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						else {
							$mainsec_strH.="<FONT class=\"maintag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						$jj++;
					}
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

				// 네임텍 출력
				$mainsec_strH .= "	<div class=\"nameTagBox2\"><span class=\"name\">".$v_info['com_name']."</span> <span class=\"owner\">(".$v_info['com_owner'].")</span></div>";
				$mainsec_strH .= "	<div><a href=\"javascript:GoMinishop('/minishop.php?storeid=".$v_info['id']."')\"><img src=\"/images/common/icon_vender_go.gif\" border=\"0\" align=\"absmiddle\" alt=\"전체상품보기\" /></a></div>";
			}

			$mainsec_strH.="	</td>\n";
			$mainsec_strH.="</tr>\n";
			$mainsec_strH.="</table>\n";
			$mainsec_strH.="</td>\n";
			if ($i==$main_hot_num) break;
		}
		if($i>0 && $i<$main_hot_cols) {
			for($k=0; $k<($main_hot_cols-$i); $k++) {
				$mainsec_strH.="<td></td>\n<td></td>\n";
			}
		}
		$mainsec_strH.="</tr>\n";
		$mainsec_strH.="</table>\n";

	} else if($main_hot_type=="L") {	//리스트형
		$mainsec_strH.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" align=\"center\">\n";
		$mainsec_strH.="<col width=\"23%\"></col>\n";
		$mainsec_strH.="<col width=\"0\"></col>\n";
		$mainsec_strH.="<col width=\"32%\"></col>\n";
		$mainsec_strH.="<col width=\"16%\"></col>\n";
		$mainsec_strH.="<col width=\"16%\"></col>\n";
		$mainsec_strH.="<col width=\"13%\"></col>\n";
		//while($row=mysql_fetch_object($result)) {
		foreach($res as $i=>$row){
			$memberpriceValue = $row->sellprice;
			$strikeStart = $strikeEnd = $memberprice = '';
			if($row->discountprices>0){
				$memberprice = number_format($row->sellprice - $row->discountprices);
				$strikeStart = "<strike>";
				$strikeEnd = "</strike>";
				$memberpriceValue = ($row->sellprice - $row->discountprices);
			}

			if($i!=0) {
			$mainsec_strH.="<tr>\n";
			$mainsec_strH.="	<td height=\"1\" bgcolor=\"#EDEDED\" colspan=\"6\"></td>\n";
			$mainsec_strH.="</tr>\n";
			}
			$mainsec_strH.="<tr align=\"center\" id=\"H".$row->productcode."\" onmouseover=\"quickfun_show(this,'H".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'H".$row->productcode."','none')\">\n";
			$mainsec_strH.="	<td style=\"padding-top:1px;padding-bottom:1px;\">";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
				$mainsec_strH.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $mainsec_strH.="height=\"".$_data->primg_minisize2."\" ";
					else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $mainsec_strH.="width=\"".$_data->primg_minisize."\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $mainsec_strH.="width=\"".$_data->primg_minisize."\" ";
					else if ($width[1]>=$_data->primg_minisize) $mainsec_strH.="height=\"".$_data->primg_minisize."\" ";
				}
			} else {
				$mainsec_strH.="<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			$mainsec_strH.="	></A></td>";
			$mainsec_strH.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','H','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
			$mainsec_strH.="	<td style=\"padding-right:5px;word-break:break-all;\" align=\"left\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</A>";
			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$main2_tag2_count;$ii++) {
					$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) {
							$mainsec_strH.="<br><br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"letter-spacing:-0.5pt;\" class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						else {
							$mainsec_strH.="<FONT class=\"maintag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"letter-spacing:-0.5pt;\" class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						$jj++;
					}
				}
			}
			$mainsec_strH.="	</td>\n";
			$mainsec_strH.="	<TD style=\"word-break:break-all;\" class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
			$mainsec_strH.="	<td style=\"word-break:break-all;\" class=\"mainprprice\">".$strikeStart.dickerview($row->etctype,$wholeSaleIcon."<strong>".number_format($row->sellprice)."원</strong>".$strikeEnd).$discountRate;

			//회원할인가 적용
			if( $memberprice > 0 ) $mainsec_strH.="<img src=\"".$Dir."images/common/memsale_icon.gif\" style=\"position:relative; top:0.1em;\" alt=\"\" /> ".dickerview($row->etctype,$memberprice."원");

			if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $mainsec_strH.=soldout();
			$mainsec_strH.="	</td>\n";
			$mainsec_strH.="	<TD style=\"word-break:break-all;\" class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format(getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y"))."원</td>\n";
			$mainsec_strH.="</tr>\n";

			if ($i==$main_hot_num) break;
		}
		$mainsec_strH.="</table>\n";
	}
	//mysql_free_result($result);
}
$mainsec_strH.="		</td>\n";
$mainsec_strH.="		<TD></TD>\n";
$mainsec_strH.="	</TR>\n";
$mainsec_strH.="	</TABLE>\n";
$mainsec_strH.="	</td>\n";
$mainsec_strH.="</tr>\n";
$mainsec_strH.="<tr>\n";
$mainsec_strH.="	<td>\n";
$mainsec_strH.="	<TABLE cellSpacing=0 cellPadding=0 width=100%>\n";
$mainsec_strH.="	<TR>\n";
$mainsec_strH.="		<TD></TD>\n";
$mainsec_strH.="		<TD></TD>\n";
$mainsec_strH.="		<TD></TD>\n";
$mainsec_strH.="	</TR>\n";
$mainsec_strH.="	</TABLE>\n";
$mainsec_strH.="	</td>\n";
$mainsec_strH.="</tr>\n";
$mainsec_strH.="</table>\n";

########################## 특별상품 #############################
$mainsec_strS="";
$mainsec_strS.="<table border=0 cellpadding=0 cellspacing=0 align=center width=100% style=\"table-layout:fixed\">\n";
$mainsec_strS.="<tr>\n";
if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/main_special_title.gif")) {
	$mainsec_strS.="<td><img src=\"".$Dir.DataDir."design/main_special_title.gif\" border=\"0\" alt=\"특별상품\"></td>\n";
} else {
	$mainsec_strS.="<td>\n";
	$mainsec_strS.="<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
	$mainsec_strS.="<TR>\n";
	$mainsec_strS.="	<TD height=20></TD>\n";
	$mainsec_strS.="</TR>\n";
	$mainsec_strS.="<TR>\n";
	$mainsec_strS.="	<TD></TD>\n";
	$mainsec_strS.="	<TD background=".$Dir."images/".$_data->icon_type."/main_hot_title_bg.gif><IMG SRC=".$Dir."images/".$_data->icon_type."/main_special_title.gif ALT=></TD>\n";
	$mainsec_strS.="	<TD></TD>\n";
	$mainsec_strS.="</TR>\n";
	$mainsec_strS.="</TABLE>\n";
	$mainsec_strS.="</td>\n";
}
$mainsec_strS.="</tr>\n";
$mainsec_strS.="<tr>\n";
$mainsec_strS.="	<td>\n";
$mainsec_strS.="	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
$mainsec_strS.="	<TR>\n";
$mainsec_strS.="		<TD></TD>\n";
$mainsec_strS.="		<TD width=100% align=center style='padding-top:15px'>\n";

$main_specprdt=explode("|",$_data->main_specialprdt);
$main_spec_num=$main_specprdt[0];
$main_spec_cols=$main_specprdt[1];
$main_spec_type=$main_specprdt[2];

$res = _getSpecialProducts('','4',$main_spec_num);
if(count($res)) {
	if($main_spec_type=="I") {	//이미지A형
		$mainsec_strS.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" align=\"center\">\n";
		$table_width=ceil(100/$main_spec_cols);
		for($j=1;$j<=$main_spec_cols;$j++) {
			if($j>1) $mainsec_strS.="<col width=></col>\n";
			$mainsec_strS.="<col width=".$table_width."%></col>\n";
		}
		$mainsec_strS.="<tr>\n";
		//while($row=mysql_fetch_object($result)) {
		foreach($res as $i=>$row){

			// 예약상품 아이콘 추가
			$row->etctype = reservationEtcType($row->reservation,$row->etctype);

			// 도매 가격 적용 상품 아이콘
			$wholeSaleIcon = ( $row->isdiscountprice == 1 ) ? $wholeSaleIconSet:"";

			// 할인율 표시
			$discountRate = ( $row->discountRate > 0 ) ? "<strong>".$row->discountRate."</strong>%↓" : "";

			$memberpriceValue = $row->sellprice;
			$strikeStart = $strikeEnd = $memberprice = '';
			if($row->discountprices>0){
				$memberprice = number_format($row->sellprice - $row->discountprices);
				$strikeStart = "<strike>";
				$strikeEnd = "</strike>";
				$memberpriceValue = ($row->sellprice - $row->discountprices);
			}

			$tableSize = $_data->primg_minisize;
			if ($i!=0 && $i%$main_spec_cols==0) {
				$mainsec_strS.= "</tr><tr><td colspan=\"".$main_spec_cols."\" height=\"10\"></td></tr>\n";
			}

			if ($i!=0 && $i%$main_spec_cols!=0) {
				$mainsec_strS.= "<td></td>";
			}

			$mainsec_strS.="<td valign=\"top\">\n";
			$mainsec_strS.="<TABLE cellSpacing=\"0\" cellPadding=\"0\" width=\"".$tableSize."\" border=\"0\" id=\"H".$row->productcode."\" onmouseover=\"quickfun_show(this,'H".$row->productcode."','')\" onmouseout=\"quickfun_show(this,'H".$row->productcode."','none')\" class=\"prInfoBox\">\n";
			$mainsec_strS.="<TR>\n";
			$mainsec_strS.="	<TD class=\"prImage\" align=\"center\">";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
				$mainsec_strS.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $mainsec_strS.="height=\"".$_data->primg_minisize2."\" ";
					else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $mainsec_strS.="width=\"".$_data->primg_minisize."\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $mainsec_strS.="width=\"".$_data->primg_minisize."\" ";
					else if ($width[1]>=$_data->primg_minisize) $mainsec_strS.="height=\"".$_data->primg_minisize."\" ";
				}
			} else {
				$mainsec_strS.="<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			$mainsec_strS.="	></A></td>";
			$mainsec_strS.="</tr>\n";
			$mainsec_strS.="<tr><td height=\"3\" style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','H','".$row->productcode."','".($row->quantity=="0"?"":"1")."')</script>":"")."</td></tr>\n";
			$mainsec_strS.="<tr>";
			$mainsec_strS.="	<td style=\"padding:5px 7px; word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT></A>".(strlen($row->prmsg)?'<br /><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</td>\n";
			$mainsec_strS.="</tr>\n";

			//시중가 + 판매가 + 할인율 + 회원할인가
			$mainsec_strS.="<tr>
										<td style=\"padding:0px 7px 7px 7px; word-break:break-all;\">
											<table border=0 cellpadding=0 cellspacing=0 width=100%>
												<tr>
													<td>
			";
			if($row->consumerprice!=0) {
				$mainsec_strS.="<span class=\"mainconprice\"><strike>".number_format($row->consumerprice)."원</strike></span>\n";
			}

			// 회원 할인가가 있을 때 가격 class 변경
			if($memberprice > 0){
				$mainprpriceClass = "";
			}else{
				$mainprpriceClass = "mainprprice";
			}

			$mainsec_strS.="<span style=\"white-space:nowrap;\">".$strikeStart.dickerview($row->etctype,$wholeSaleIcon." <strong class=\"".$mainprpriceClass."\">".number_format($row->sellprice)."원</strong>".$strikeEnd)."</span>";
			$mainsec_strS.="</td>";

			if($row->discountRate > 0){
				$mainsec_strS.="<td align=\"right\" valign=\"bottom\" class=\"discount\">".$discountRate."</td>";
			}
			$mainsec_strS.="
					</tr>
				</table>
			";

			//회원할인가 적용
			if( $memberprice > 0 ) {
				$mainsec_strS.="	<div><span class=\"mainprprice\">".dickerview($row->etctype,$memberprice)."원</span> <img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" /></div>\n";
			}

			if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0"){
				$mainsec_strS.=soldout();
			}

			$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y");
			if($reserveconv>0) {
				$mainsec_strS.="	<div style=\"margin-top:5px;\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"vertical-ailgn:middle;\" /> <span class=\"mainreserve\">".number_format($reserveconv)."</span>원</div>\n";
			}

			$mainsec_strS.="	</td>\n";
			$mainsec_strS.="</tr>\n";

			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$main1_tag2_count;$ii++) {
					$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) {
							$mainsec_strS.="<tr>\n";
							$mainsec_strS.="	<td align=\"center\" style=\"padding-left:5px;padding-right:5px;word-break:break-all;\">\n";
							$mainsec_strS.="	<img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"letter-spacing:-0.5pt;\" class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						else {
							$mainsec_strS.="<FONT class=\"maintag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"letter-spacing:-0.5pt;\" class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						$jj++;
					}
				}
				if($jj!=0) {
					$mainsec_strS.="	</td>\n";
					$mainsec_strS.="</tr>\n";
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
				$mainsec_strS.="
					<tr>
						<td class=\"nameTagBox\">".$venderNameTag."</td>
					</tr>
				";
			}

			$mainsec_strS.="</table>\n";
			$mainsec_strS.="</td>\n";
			if ($i==$main_spec_num) break;
		}
		if($i>0 && $i<$main_spec_cols) {
			for($k=0; $k<($main_spec_cols-$i); $k++) {
				$mainsec_strS.="<td></td>\n<td></td>\n";
			}
		}
		$mainsec_strS.="</tr>\n";
		$mainsec_strS.="</table>\n";

	} else if($main_spec_type=="D") {	//이미지B형
		$mainsec_strS.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" align=\"center\">\n";
		$table_width=ceil(100/$main_spec_cols);
		for($j=1;$j<=$main_spec_cols;$j++) {
			if($j!=1) {$mainsec_strS.="<col width=></col>\n";}
			$mainsec_strS.="<col width=".$table_width."%></col>\n";
		}
		$mainsec_strS.="<tr>\n";
		//while($row=mysql_fetch_object($result)) {
		foreach($res as $i=>$row){
			$memberpriceValue = $row->sellprice;
			$strikeStart = $strikeEnd = $memberprice = '';
			if($row->discountprices>0){
				$memberprice = number_format($row->sellprice - $row->discountprices);
				$strikeStart = "<strike>";
				$strikeEnd = "</strike>";
				$memberpriceValue = ($row->sellprice - $row->discountprices);
			}

			$tableSize = $_data->primg_minisize;

			if ($i!=0) {
				if($i%$main_spec_cols==0) {
					if($i>=$main_spec_cols) {
						$mainsec_strS.="</tr><tr><td height=\"1\" colspan=\"".($main_spec_cols*2-1)."\" bgcolor=\"#EDEDED\"></td></tr><tr>\n";
					} else {
						$mainsec_strS.="</tr><tr>\n";
					}
				} else {
					$mainsec_strS.="<td align=\"center\"><img src=\"".$Dir."images/common/main_product_lineb.gif\" border=\"0\"></td>\n";
				}
			}

			$mainsec_strS.="<td align=center style=\"padding:5px 0px;\">\n";
			$mainsec_strS.="<TABLE border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" id=\"H".$row->productcode."\" onmouseover=\"quickfun_show(this,'H".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'H".$row->productcode."','none')\" class=\"prInfoBox2\">\n";
			$mainsec_strS.="<col width=\"".$tableSize."\"></col>\n";
			$mainsec_strS.="<col width=\"0\"></col>\n";
			$mainsec_strS.="<col width=\"100%\"></col>\n";
			$mainsec_strS.="<TR>\n";
			$mainsec_strS.="	<TD class=\"prImage\" align=\"center\" nowrap>";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
				$mainsec_strS.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $mainsec_strS.="height=\"".$_data->primg_minisize2."\" ";
					else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $mainsec_strS.="width=\"".$_data->primg_minisize."\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $mainsec_strS.="width=\"".$_data->primg_minisize."\" ";
					else if ($width[1]>=$_data->primg_minisize) $mainsec_strS.="height=\"".$_data->primg_minisize."\" ";
				}
			} else {
				$mainsec_strS.="<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			$mainsec_strS.="	></A></td>";
			$mainsec_strS.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','H','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
			$mainsec_strS.="	<TD style=\"padding-left:15px;word-break:break-all;\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode.$add_query."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</A><br />\n";

			if($row->consumerprice!=0) {
				$mainsec_strS.="<img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><FONT class=\"mainconprice\"><strike>".number_format($row->consumerprice)."</strike>원</font>\n";
			}

			// 회원 할인가가 있을 때 가격 class 변경
			if($memberprice > 0){
				$mainprpriceClass = "";
			}else{
				$mainprpriceClass = "mainprprice";
			}
			// 할인율 표시
			$discountRate = ( $row->discountRate > 0 ) ? "<strong>".$row->discountRate."</strong>%↓" : "";

			$mainsec_strS.=$strikeStart.dickerview($row->etctype,$wholeSaleIcon."<strong class=\"".$mainprpriceClass."\">".number_format($row->sellprice)."원</strong>".$strikeEnd);
			$mainsec_strS.="<span class=\"discount\">".$discountRate."</span>";

			if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $mainsec_strS.=soldout();

			//회원할인가 적용
			if( $memberprice > 0 ) $mainsec_strS.="<br /><font class=\"mainprprice\">".dickerview($row->etctype,$memberprice."원")."</font> <img src=\"".$Dir."images/common/memsale_icon.gif\" align=\"absmiddle\" alt=\"\" />";

			$reserveconv=getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y");
			if($reserveconv>0) {
				$mainsec_strS.="<br><font class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format($reserveconv)."원</font>\n";
			}

			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$main3_tag2_count;$ii++) {
					$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) {
							$mainsec_strS.="<br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						else {
							$mainsec_strS.="<FONT class=\"maintag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						$jj++;
					}
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

				// 네임텍 출력
				$mainsec_strS .= "	<div class=\"nameTagBox2\"><span class=\"name\">".$v_info['com_name']."</span> <span class=\"owner\">(".$v_info['com_owner'].")</span></div>";
				$mainsec_strS .= "	<div><a href=\"javascript:GoMinishop('/minishop.php?storeid=".$v_info['id']."')\"><img src=\"/images/common/icon_vender_go.gif\" border=\"0\" align=\"absmiddle\" alt=\"전체상품보기\" /></a></div>";
			}

			$mainsec_strS.="	</td>\n";
			$mainsec_strS.="</tr>\n";
			$mainsec_strS.="</table>\n";
			$mainsec_strS.="</td>\n";
			if ($i==$main_spec_num) break;
		}
		if($i>0 && $i<$main_spec_cols) {
			for($k=0; $k<($main_spec_cols-$i); $k++) {
				$mainsec_strS.="<td></td>\n<td></td>\n";
			}
		}
		$mainsec_strS.="</tr>\n";
		$mainsec_strS.="</table>\n";

	} else if($main_spec_type=="L") {	//리스트형
		$mainsec_strS.="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" align=\"center\">\n";
		$mainsec_strS.="<col width=\"23%\"></col>\n";
		$mainsec_strS.="<col width=\"0\"></col>\n";
		$mainsec_strS.="<col width=\"32%\"></col>\n";
		$mainsec_strS.="<col width=\"16%\"></col>\n";
		$mainsec_strS.="<col width=\"16%\"></col>\n";
		$mainsec_strS.="<col width=\"13%\"></col>\n";
		//while($row=mysql_fetch_object($result)) {
		foreach($res as $i=>$row){
			$memberpriceValue = $row->sellprice;
			$strikeStart = $strikeEnd = $memberprice = '';
			if($row->discountprices>0){
				$memberprice = number_format($row->sellprice - $row->discountprices);
				$strikeStart = "<strike>";
				$strikeEnd = "</strike>";
				$memberpriceValue = ($row->sellprice - $row->discountprices);
			}

			if($i!=0) {
			$mainsec_strS.="<tr>\n";
			$mainsec_strS.="	<td height=\"1\" bgcolor=\"#EDEDED\" colspan=\"6\"></td>\n";
			$mainsec_strS.="</tr>\n";
			}
			$mainsec_strS.="<tr align=\"center\" id=\"H".$row->productcode."\" onmouseover=\"quickfun_show(this,'H".$row->productcode."','','row')\" onmouseout=\"quickfun_show(this,'H".$row->productcode."','none')\">\n";
			$mainsec_strS.="	<td style=\"padding-top:1px;padding-bottom:1px;\">";
			if (strlen($row->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)==true) {
				$mainsec_strS.="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=\"0\" ";
				$width = getimagesize($Dir.DataDir."shopimages/product/".$row->tinyimage);
				if($_data->ETCTYPE["IMGSERO"]=="Y") {
					if ($width[1]>$width[0] && $width[1]>$_data->primg_minisize2) $mainsec_strS.="height=\"".$_data->primg_minisize2."\" ";
					else if (($width[1]>=$width[0] && $width[0]>=$_data->primg_minisize) || $width[0]>=$_data->primg_minisize) $mainsec_strS.="width=\"".$_data->primg_minisize."\" ";
				} else {
					if ($width[0]>=$width[1] && $width[0]>=$_data->primg_minisize) $mainsec_strS.="width=\"".$_data->primg_minisize."\" ";
					else if ($width[1]>=$_data->primg_minisize) $mainsec_strS.="height=\"".$_data->primg_minisize."\" ";
				}
			} else {
				$mainsec_strS.="<img src=\"".$Dir."images/no_img.gif\" border=\"0\" align=\"center\"";
			}
			$mainsec_strS.="	></A></td>";
			$mainsec_strS.="	<td style=\"position:relative;\">".($_data->ETCTYPE["QUICKTOOLS"]!="Y"?"<script>quickfun_write('".$Dir."','H','".$row->productcode."','".($row->quantity=="0"?"":"1")."','row')</script>":"")."</td>";
			$mainsec_strS.="	<td style=\"padding-right:5px;word-break:break-all;\" align=\"left\"><A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row->productcode."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainprname\">".viewproductname($row->productname,$row->etctype,$row->selfcode)."</FONT>".(strlen($row->prmsg)?'<br><span class="prmsgArea">'.$row->prmsg.'</span>':'')."</A>";
			if($_data->ETCTYPE["TAGTYPE"]=="Y") {
				$taglist=explode(",",$row->tag);
				$jj=0;
				for($ii=0;$ii<$main2_tag2_count;$ii++) {
					$taglist[$ii]=ereg_replace("(<|>)","",$taglist[$ii]);
					if(strlen($taglist[$ii])>0) {
						if($jj==0) {
							$mainsec_strS.="<br><br><img src=\"".$Dir."images/common/tag_icon.gif\" border=\"0\" align=\"absmiddle\" style=\"margin-right:2px;\"><a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"letter-spacing:-0.5pt;\" class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						else {
							$mainsec_strS.="<FONT class=\"maintag\">,</font>&nbsp;<a href=\"".$Dir.FrontDir."tag.php?tagname=".urlencode($taglist[$ii])."\" onmouseover=\"window.status='".$taglist[$ii]."';return true;\" onmouseout=\"window.status='';return true;\"><FONT style=\"letter-spacing:-0.5pt;\" class=\"maintag\">".$taglist[$ii]."</font></a>";
						}
						$jj++;
					}
				}
			}
			$mainsec_strS.="	</td>\n";
			$mainsec_strS.="	<TD style=\"word-break:break-all;\" class=\"mainconprice\"><img src=\"".$Dir."images/common/won_icon2.gif\" border=\"0\" style=\"margin-right:2px;\"><strike>".number_format($row->consumerprice)."</strike>원</td>\n";
			$mainsec_strS.="	<td style=\"word-break:break-all;\" class=\"mainprprice\">".$strikeStart.dickerview($row->etctype,$wholeSaleIcon."<strong>".number_format($row->sellprice)."원</strong>".$strikeEnd).$discountRate;

			//회원할인가 적용
			if( $memberprice > 0 ) $mainsec_strS.="<img src=\"".$Dir."images/common/memsale_icon.gif\" style=\"position:relative; top:0.1em;\" alt=\"\" /> ".dickerview($row->etctype,$memberprice."원");

			if ($_data->ETCTYPE["MAINSOLD"]=="Y" && $row->quantity=="0") $mainsec_strS.=soldout();
			$mainsec_strS.="	</td>\n";
			$mainsec_strS.="	<TD style=\"word-break:break-all;\" class=\"mainreserve\"><img src=\"".$Dir."images/common/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".number_format(getReserveConversion($row->reserve,$row->reservetype,$memberpriceValue,"Y"))."원</td>\n";
			$mainsec_strS.="</tr>\n";

			if ($i==$main_spec_num) break;
		}
		$mainsec_strS.="</table>\n";
	}
	//mysql_free_result($result);
}
$mainsec_strS.="		</td>\n";
$mainsec_strS.="		<TD></TD>\n";
$mainsec_strS.="	</TR>\n";
$mainsec_strS.="	</TABLE>\n";
$mainsec_strS.="	</td>\n";
$mainsec_strS.="</tr>\n";
$mainsec_strS.="<tr>\n";
$mainsec_strS.="	<td>\n";
$mainsec_strS.="	<TABLE cellSpacing=0 cellPadding=0 width=100%>\n";
$mainsec_strS.="	<TR>\n";
$mainsec_strS.="		<TD></TD>\n";
$mainsec_strS.="		<TD></TD>\n";
$mainsec_strS.="		<TD></TD>\n";
$mainsec_strS.="	</TR>\n";
$mainsec_strS.="	</TABLE>\n";
$mainsec_strS.="	</td>\n";
$mainsec_strS.="</tr>\n";
$mainsec_strS.="</table>\n";
########################## 특별상품 끝 ###########################

########################## 공동구매 #############################
$mainsec_strG="";
$mainsec_strG.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
$mainsec_strG.="<col width=49%></col>\n";
$mainsec_strG.="<col width=2%></col>\n";
$mainsec_strG.="<col width=49%></col>\n";
$mainsec_strG.="<tr>\n";
if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/main_gonggu_title.gif")) {
	$mainsec_strG.="<td colspan=\"3\"><img src=\"".$Dir.DataDir."design/main_gonggu_title.gif\" border=\"0\" alt=\"공동구매\"></td>\n";
} else {
	$mainsec_strG.="<td colspan=\"3\">\n";
	$mainsec_strG.="<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
	$mainsec_strG.="<TR>\n";
	$mainsec_strG.="	<TD height=10></TD>\n";
	$mainsec_strG.="</TR>\n";
	$mainsec_strG.="<TR>\n";
	$mainsec_strG.="	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/main_gonggu_title_head.gif ALT=></TD>\n";
	$mainsec_strG.="	<TD width=100%  background=".$Dir."images/".$_data->icon_type."/main_gonggu_title_bg.gif></TD>\n";
	$mainsec_strG.="</TR>\n";
	$mainsec_strG.="<TR>\n";
	$mainsec_strG.="	<TD height=10></TD>\n";
	$mainsec_strG.="</TR>\n";
	$mainsec_strG.="</TABLE>\n";
	$mainsec_strG.="</td>\n";
}
$mainsec_strG.="</tr>\n";
$mainsec_strG.="<tr>\n";
$gongguimagepath=$Dir.DataDir."shopimages/gonggu/";
$sql = "SELECT * FROM tblgonginfo ";
$sql.= "WHERE start_date <= '".date("YmdHis")."' AND end_date > '".date("YmdHis")."' ";
$sql.= "ORDER BY gong_seq DESC LIMIT 2 ";
$result = mysql_query($sql,get_db_conn());
$i=0;
while($row=mysql_fetch_object($result)) {
	$num=intval($row->bid_cnt/$row->count);
	$price=$row->start_price-($num*$row->down_price);
	if($price<$row->mini_price) $price=$row->mini_price;

	$end_time=mktime((substr($row->end_date,8,2)*1),(substr($row->end_date,10,2)*1),0,(substr($row->end_date,4,2)*1),(substr($row->end_date,6,2)*1),(substr($row->end_date,0,4)*1));

	$i++;
	$mainsec_strG.="<td>\n";
	$mainsec_strG.="<table cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	$mainsec_strG.="<tr>\n";
	$mainsec_strG.="	<td><div style=\"padding-left:15px;white-space:nowrap;width:230px;overflow:hidden;text-overflow:ellipsis;\"><a href='".$Dir.GongguDir."gonggu_detail.php?seq=".$row->gong_seq."' onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><font color=\"#000000\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><b>".$row->gong_name."</b></font></div></a></td>\n";
	$mainsec_strG.="</tr>\n";
	$mainsec_strG.="<tr><td height=10></td></tr>\n";
	$mainsec_strG.="<tr>\n";
	$mainsec_strG.="	<td>\n";
	$mainsec_strG.="	<table cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	$mainsec_strG.="	<col width=42%></col>\n";
	$mainsec_strG.="	<col width=2%></col>\n";
	$mainsec_strG.="	<col width=56%></col>\n";
	$mainsec_strG.="	<tr>\n";
	$mainsec_strG.="		<td valign=\"top\">\n";
	$mainsec_strG.="		<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
	$mainsec_strG.="		<tr>\n";
	$mainsec_strG.="			<td align=center>\n";
	if(strlen($row->image3)>0 && file_exists($gongguimagepath.$row->image3)) {
		$mainsec_strG.="<a href=\"".$Dir.GongguDir."gonggu_detail.php?seq=".$row->gong_seq."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$gongguimagepath.$row->image3."\" border=\"0\" ";
		$size=GetImageSize($gongguimagepath.$row->image3);
		if(($size[0]>80 || $size[1]>80) && $size[0]>$size[1]) {
			$mainsec_strG.=" width=\"80\"";
		} else if($size[0]>80 || $size[1]>80) {
			$mainsec_strG.=" height=\"80\"";
		}
		$mainsec_strG.="></a></td>";
	} else {
		$mainsec_strG.="<a href=\"".$Dir.GongguDir."gonggu_detail.php?seq=".$row->gong_seq."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/no_img.gif\" width=\"80\" height=\"80\" border=\"0\"></a></td>";
	}
	$mainsec_strG.="		</tr>\n";
	$mainsec_strG.="		<tr>\n";
	$mainsec_strG.="			<td align=center><a href=\"".$Dir.GongguDir."gonggu_detail.php?seq=".$row->gong_seq."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/gong_view.gif\" border=0 vspace=3></a></td>\n";
	$mainsec_strG.="		</tr>\n";
	$mainsec_strG.="		</table>\n";
	$mainsec_strG.="		</td>\n";
	$mainsec_strG.="		<td></td>\n";
	$mainsec_strG.="		<td valign=\"top\">\n";
	$mainsec_strG.="		<table cellpadding=0 cellspacing=0 width=100%>\n";
	$mainsec_strG.="		<tr>\n";
	$mainsec_strG.="			<td style=\"font-size:11px;word-break:break-all;\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/communitynero.gif\" border=0> 시중가 : <s>".number_format($row->origin_price)."원</s></td>\n";
	$mainsec_strG.="		</tr>\n";
	$mainsec_strG.="		<tr>\n";
	$mainsec_strG.="			<td style=\"font-size:11px;word-break:break-all;\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/communitynero.gif\" border=0> 현재가 : <font color=\"#FF6A00\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><b>".number_format($price)."원</b></font></td>\n";
	$mainsec_strG.="		</tr>\n";
	$mainsec_strG.="		<tr>\n";
	$mainsec_strG.="			<td style=\"font-size:11px;word-break:break-all;\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/communitynero.gif\" border=0> 신청수량 : ".$row->bid_cnt."개</td>\n";
	$mainsec_strG.="		</tr>\n";
	$mainsec_strG.="		<tr>\n";
	$mainsec_strG.="			<td style=\"font-size:11px;word-break:break-all;\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/communitynero.gif\" border=0> 판매수량 : ".$row->quantity."개</td>\n";
	$mainsec_strG.="		</tr>\n";
	$mainsec_strG.="		<tr>\n";
	$mainsec_strG.="			<td height=5></td>\n";
	$mainsec_strG.="		</tr>\n";
	$mainsec_strG.="		<tr>\n";
	$mainsec_strG.="			<td>\n";
	$mainsec_strG.="			<table cellpadding=0 cellspacing=0 width=120>\n";
	$mainsec_strG.="			<tr>\n";
	$mainsec_strG.="				<td width=120 height=52 background=\"".$Dir."images/".$_data->icon_type."/listbox.gif\">\n";
	$mainsec_strG.="				<table cellpadding=0 cellspacing=0>\n";
	$mainsec_strG.="				<tr align=center>\n";
	$mainsec_strG.="					<td width=60 style=\"font-size:11px;\" valign=top height=42>".number_format($row->start_price)."</td>\n";
	$mainsec_strG.="					<td width=50 style=\"font-size:11px;\" valign=\"bottom\">".number_format($price)."</td>\n";
	$mainsec_strG.="				</tr>\n";
	$mainsec_strG.="				</table>\n";
	$mainsec_strG.="				</td>\n";
	$mainsec_strG.="			</tr>\n";
	$mainsec_strG.="			<tr>\n";
	$mainsec_strG.="				<td width=120>\n";
	$mainsec_strG.="				<table cellpadding=0 cellspacing=0>\n";
	$mainsec_strG.="				<tr align=center>\n";
	$mainsec_strG.="					<td width=60 style=\"font-size:11px;\">시작가</td>\n";
	$mainsec_strG.="					<td width=50 style=\"font-size:11px;\">현재가</td>\n";
	$mainsec_strG.="				</tr>\n";
	$mainsec_strG.="				</table>\n";
	$mainsec_strG.="				</td>\n";
	$mainsec_strG.="			</tr>\n";
	$mainsec_strG.="			</table>\n";
	$mainsec_strG.="			</td>\n";
	$mainsec_strG.="		</tr>\n";
	$mainsec_strG.="		</table>\n";
	$mainsec_strG.="		</td>\n";
	$mainsec_strG.="	</tr>\n";
	$mainsec_strG.="	</table>\n";
	$mainsec_strG.="	</td>\n";
	$mainsec_strG.="</tr>\n";
	$mainsec_strG.="</table>\n";
	$mainsec_strG.="</td>\n";
	if($i%2) {
		$mainsec_strG.="<td align=\"center\" width=\"2%\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/1_164.gif\" border=\"0\" hspace=\"10\"></td>\n";
	}
}
if($i==1) {
	$mainsec_strG.="<td></td>\n";
}
//mysql_free_result($result);
$mainsec_strG.="</tr>\n";
$mainsec_strG.="</table>\n";


########################## 경매 #############################
$mainsec_strA="";
$mainsec_strA.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
$mainsec_strA.="<col width=49%></col>\n";
$mainsec_strA.="<col width=2%></col>\n";
$mainsec_strA.="<col width=49%></col>\n";
$mainsec_strA.="<tr>\n";
if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/main_auction_title.gif")) {
	$mainsec_strA.="<td colspan=\"3\"><img src=\"".$Dir.DataDir."design/main_auction_title.gif\" border=\"0\" alt=\"경매\"></td>\n";
} else {
	$mainsec_strA.="<td colspan=\"3\">\n";
	$mainsec_strA.="<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
	$mainsec_strA.="<TR>\n";
	$mainsec_strA.="	<TD height=10></TD>\n";
	$mainsec_strA.="</TR>\n";
	$mainsec_strA.="<TR>\n";
	$mainsec_strA.="	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/main_auction_title_head.gif ALT=></TD>\n";
	$mainsec_strA.="	<TD width=100% background=".$Dir."images/".$_data->icon_type."/main_auction_title_bg.gif></TD>\n";
	$mainsec_strA.="</TR>\n";
	$mainsec_strA.="<TR>\n";
	$mainsec_strA.="	<TD height=10></TD>\n";
	$mainsec_strA.="</TR>\n";
	$mainsec_strA.="</TABLE>\n";
	$mainsec_strA.="</td>\n";
}
$mainsec_strA.="</tr>\n";
$mainsec_strA.="<tr>\n";
$auctionimagepath=$Dir.DataDir."shopimages/auction/";
$sql = "SELECT * FROM tblauctioninfo ";
$sql.= "WHERE start_date <= '".date("YmdHis")."' AND end_date > '".date("YmdHis")."' ";
$result=mysql_query($sql,get_db_conn());
$i=0;
while($row=mysql_fetch_object($result)) {
	$end_date=substr($row->end_date,4,2)."/".substr($row->end_date,6,2)." ".substr($row->end_date,8,2).":".substr($row->end_date,10,2);
	$i++;
	$mainsec_strA.="<td valign=top>\n";
	$mainsec_strA.="<table cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	$mainsec_strA.="<tr>\n";
	$mainsec_strA.="	<td><div style=\"padding-left:15px;white-space:nowrap;width:230px;overflow:hidden;text-overflow:ellipsis;\"><a href='".$Dir.AuctionDir."auction_detail.php?seq=".$row->auction_seq."&start_date=".$row->start_date."' onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><font color=\"#000000\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><b>".$row->auction_name."</b></font></a></div></td>\n";
	$mainsec_strA.="</tr>\n";
	$mainsec_strA.="<tr><td height=10></td></tr>\n";
	$mainsec_strA.="<tr>\n";
	$mainsec_strA.="	<td>\n";
	$mainsec_strA.="	<table cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
	$mainsec_strA.="	<col width=42%></col>\n";
	$mainsec_strA.="	<col width=2%></col>\n";
	$mainsec_strA.="	<col width=56%></col>\n";
	$mainsec_strA.="	<tr>\n";
	$mainsec_strA.="		<td valign=top>\n";
	$mainsec_strA.="		<table cellpadding=0 cellspacing=0 width=100%>\n";
	$mainsec_strA.="		<tr>\n";
	$mainsec_strA.="			<td align=center>\n";
	if(strlen($row->product_image)>0 && file_exists($auctionimagepath.$row->product_image)) {
		$mainsec_strA.="<a href=\"".$Dir.AuctionDir."auction_detail.php?seq=".$row->auction_seq."&start_date=".$row->start_date."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$auctionimagepath.$row->product_image."\" border=\"0\" ";
		$size=GetImageSize($auctionimagepath.$row->product_image);
		if(($size[0]>80 || $size[1]>80) && $size[0]>$size[1]) {
			$mainsec_strA.=" width=\"80\"";
		} else if($size[0]>80 || $size[1]>80) {
			$mainsec_strA.=" height=\"80\"";
		}
		$mainsec_strA.="></a></td>";
	} else {
		$mainsec_strA.="<a href=\"".$Dir.AuctionDir."auction_detail.php?seq=".$row->auction_seq."&start_date=".$row->start_date."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir."images/no_img.gif\" width=\"80\" height=\"80\" border=\"0\"></a></td>";
	}
	$mainsec_strA.="		</tr>\n";
	$mainsec_strA.="		</table>\n";
	$mainsec_strA.="		</td>\n";
	$mainsec_strA.="		<td></td>\n";
	$mainsec_strA.="		<td valign=top>\n";
	$mainsec_strA.="		<table cellpadding=0 cellspacing=0 width=100%>\n";
	$mainsec_strA.="		<tr>\n";
	$mainsec_strA.="			<td style=\"font-size:11px;word-break:break-all;\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/communitynero.gif\" border=0> 시작가 : ".number_format($row->start_price)."원</td>\n";
	$mainsec_strA.="		</tr>\n";
	$mainsec_strA.="		<tr>\n";
	$mainsec_strA.="			<td style=\"font-size:11px;word-break:break-all;\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/communitynero.gif\" border=0> 현재가 : <font color=\"#FF6A00\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><b>".number_format($row->last_price)."원</b></font></td>\n";
	$mainsec_strA.="		</tr>\n";
	$mainsec_strA.="		<tr>\n";
	$mainsec_strA.="			<td style=\"font-size:11px;word-break:break-all;\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/communitynero.gif\" border=0> 입찰수 : ".$row->bid_cnt."개</td>\n";
	$mainsec_strA.="		</tr>\n";
	$mainsec_strA.="		<tr>\n";
	$mainsec_strA.="			<td height=5></td>\n";
	$mainsec_strA.="		</tr>\n";
	$mainsec_strA.="		<tr>\n";
	$mainsec_strA.="			<td><a href=\"".$Dir.AuctionDir."auction_detail.php?seq=".$row->auction_seq."&start_date=".$row->start_date."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin3_gong_btn.gif\" border=0></a></td>\n";
	$mainsec_strA.="		</tr>\n";
	$mainsec_strA.="		</table>\n";
	$mainsec_strA.="		</td>\n";
	$mainsec_strA.="	</tr>\n";
	$mainsec_strA.="	</table>\n";
	$mainsec_strA.="	</td>\n";
	$mainsec_strA.="</tr>\n";
	$mainsec_strA.="</table>\n";
	$mainsec_strA.="</td>\n";
	if($i%2) {
		$mainsec_strA.="<td align=\"center\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/1_164.gif\" border=\"0\" hspace=\"10\"></td>\n";
	}
}
if($i==1) {
	$mainsec_strA.="<td></td>\n";
}
//mysql_free_result($result);
$mainsec_strA.="</tr>\n";
$mainsec_strA.="</table>\n";

?>


<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?if(strlen($_data->layoutdata["MAINTYPE"])==0 || $_data->layoutdata["MAINTYPE"]=="B"){?>
<tr><td height="20"></td></tr>
<tr>
	<td nowrap>
	<!-- main top start -->
	<?=$mainsec_strI?>
	<!-- main top end -->
	</td>
</tr>
<?}?>
<tr>
	<td>
	<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<?//if($_data->layoutdata["MAINTYPE"]!="C"){?>
		<!--
		<col></col>
		<col width="180"></col>
		-->
		<?//}?>
		<tr>
			<td valign="top">
				<!-- main center start -->
				<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
					<?if(strlen($_data->layoutdata["MAINTYPE"])>0 && $_data->layoutdata["MAINTYPE"]!="B"){?>
					<tr>
						<td nowrap>
						<?=$mainsec_strI?>
						</td>
					</tr>
					<tr><td height="10"></td></tr>
					<?}?>
					<?
						if(strlen($_data->layoutdata["MAINUSED"])==0) $_data->layoutdata["MAINUSED"]="INBHS";
						for($u=1;$u<strlen($_data->layoutdata["MAINUSED"]);$u++) {
							$temp=substr($_data->layoutdata["MAINUSED"],$u,1);
							if($u>1) echo "<tr><td height=10></td></tr>\n";
							echo "<tr>\n";
							echo "	<td>\n";
							echo ${"mainsec_str".$temp};
							echo "	</td>\n";
							echo "</tr>\n";
						}
					?>
				</table>
				<!-- main center end -->
			</td>
		</tr>




		<!--
		<?//if($_data->layoutdata["MAINTYPE"]!="C"){?>

		<td valign="top">
		<!-- main right start (180px) --//>
		<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<?//if($_data->layoutdata["MAINTYPE"]=="A"){?>
		<tr><td height=10></td></tr>
		<?//}?>
		<?
		/*
		###### 배너 ######
		if($_data->banner_loc=="R") {
			include($Dir."lib/banner.php");
			if(strlen($bannerbody)>0) {
				echo "<tr><td align=\"center\">".$bannerbody."</td></tr>";
				echo "<tr><td height=5></td></tr>\n";
			}
		}
		###### 배너 끝 ######
		*/
		?>
		<tr>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr>
				<td><A HREF="javascript:notice_view('list','')" onMouseOver="window.status='공지사항조회';return true;" onMouseOut="window.status='';return true;"><?
				if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/main_notice_title.gif")) {
					echo "<img src=\"".$Dir.DataDir."design/main_notice_title.gif\" border=\"0\" alt=\"공지사항조회\">";
				} else {
					echo "<img src=\"".$Dir."images/".$_data->icon_type."/main_notice_title.gif\" border=\"0\" alt=\"공지사항조회\">";
				}
			?></A></td>
			</tr>
			<tr>
				<td style="padding-top:20px;" valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
				$sql = "SELECT date,subject FROM tblnotice ORDER BY date DESC LIMIT ".$_data->main_notice_num;
				$result=mysql_query($sql,get_db_conn());
				$i=0;
				while($row=mysql_fetch_object($result)) {
					$i++;
					echo "<tr>\n";
					echo "	<td></td>\n";
					echo "	<td width=\"100%\"><div style=\"padding-left:3px;white-space:nowrap;width:157px;overflow:hidden;text-overflow:ellipsis;\"><A HREF=\"javascript:notice_view('view','".$row->date."')\" onmouseover=\"window.status='공지사항조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainnotice\">".$row->subject."</FONT></A></div></td>\n";
					echo "</tr>\n";
				}
				//mysql_free_result($result);
				if($i==0) {
					echo "<tr><td height=\"18\" align=\"center\" class=\"mainnotice\" colspan=\"2\">등록된 공지사항이 없습니다.</td></tr>";
				}
?>
				</table>
				</td>
			</tr>
			<tr>
				<td></td>
			</tr>
			</table>
			</td>
		</tr>
		-->
		<tr><td height="30"></td></tr>
		<tr>
			<td>
				<div class="mainNoticeQna">
					<div style="float:left; width:46%;">
						<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed;">
							<tr>
								<td>
									<?
										if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/main_notice_title.gif")) {
											echo "<div style=\"float:left;\"><img src=\"".$Dir.DataDir."design/main_notice_title.gif\" border=\"0\" alt=\"공지사항조회\" /></div>";
										} else {
											echo "<div style=\"float:left;\"><img src=\"".$Dir."images/".$_data->icon_type."/main_notice_title.gif\" border=\"0\" alt=\"공지사항조회\" /></div>";
										}
									?>
									<div style="float:right; margin-top:14px; text-align:right;"><a href="<?=$Dir.BoardDir?>board.php?board=notice"><img src="<?=$Dir?>images/<?=$_data->icon_type?>/main_board_more.gif" border="0" alt="더보기" /></div>
								</td>
							</tr>
							<tr>
								<td style="padding-top:12px; padding-left:4px;">
								<?
									$sql = "SELECT num, title, writetime FROM tblboard WHERE board='notice' ";
									$sql.= "AND deleted!='1' ";
									$sql.= "AND pos=0 ";
									$sql.= "ORDER BY thread ASC LIMIT 5";
									$result=@mysql_query($sql,get_db_conn());

									$j=0;
									while($row=mysql_fetch_object($result)) {
										$j++;
										$date="[".date("Y/m/d",$row->writetime)."]";

										echo "
											<table border=0 cellpadding=0 cellspacing=0 width=\"100%\">
												<tr>
													<td style=\"word-break:break-all;\"><A HREF=\"".$Dir.BoardDir."board.php?pagetype=view&view=1&board=notice&num=".$row->num."\" onmouseover=\"window.status='게시글항조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainboard\">".$date." ".titleCut(24,$row->title)."</FONT></A></td>
												</tr>
											</table>
										";
									}
									mysql_free_result($result);

									if($j==0) {
										echo "
											<table border=0 cellpadding=0 cellspacing=0>
												<tr><td align=center class=\"mainboard\">등록된 게시글이 없습니다.</td></tr>
											</table>
										";
									}
								?>
								</td>
							</tr>
						</table>
					</div>

					<div style="float:right; width:46%;">
						<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed;">
							<tr>
								<td>
									<?
										if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/customercenter_stitle2.gif")) {
											echo "<div style=\"float:left;\"><img src=\"".$Dir.DataDir."design/customercenter_stitle2.gif\" border=\"0\" alt=\"Shopping Q&A\" /></div>";
										} else {
											echo "<div style=\"float:left;\"><img src=\"".$Dir."images/".$_data->icon_type."/customercenter_stitle2.gif\" border=\"0\" alt=\"Shopping Q&A\" /></div>";
										}
									?>
									<div style="float:right; margin-top:14px; text-align:right;"><a href="<?=$Dir.BoardDir?>board.php?board=qna"><img src="<?=$Dir?>images/<?=$_data->icon_type?>/main_board_more.gif" border="0" alt="더보기" /></div>
								</td>
							</tr>
							<tr>
								<td style="padding-top:12px; padding-left:4px;">
								<?
									$sql = "SELECT num, title, writetime FROM tblboard WHERE board='qna' ";
									$sql.= "AND deleted!='1' ";
									$sql.= "AND pos=0 ";
									$sql.= "ORDER BY thread ASC LIMIT 5";
									$result=@mysql_query($sql,get_db_conn());

									$j=0;
									while($row=mysql_fetch_object($result)) {
										$j++;
										$date="[".date("Y/m/d",$row->writetime)."]";

										echo "
											<table border=0 cellpadding=0 cellspacing=0 width=\"100%\">
												<tr>
													<td style=\"word-break:break-all;\"><A HREF=\"".$Dir.BoardDir."board.php?pagetype=view&view=1&board=qna&num=".$row->num."\" onmouseover=\"window.status='게시글항조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainboard\">".$date." ".titleCut(24,$row->title)."</FONT></A></td>
												</tr>
											</table>
										";
									}
									mysql_free_result($result);

									if($j==0) {
										echo "
											<table border=0 cellpadding=0 cellspacing=0>
												<tr><td align=center class=\"mainboard\">등록된 게시글이 없습니다.</td></tr>
											</table>
										";
									}
								?>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<div class="mainCustomerCenter">
					<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top:5px; table-layout:fixed;">
						<tr>
							<td><img src="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin3_customertitle.gif" border="0" alt="Customer Center" /></td>
						</tr>
						<tr>
							<td style="padding-left:15px;">
								<?
									if(strlen($_data->info_tel)>0) {
										$tmp_tel=explode(",",$_data->info_tel);
										for($i=0;$i<count($tmp_tel);$i++) {
											echo "<div style=\"font-size:11px;\">TEL : <span class=\"telNum\">".trim($tmp_tel[$i])."</span></div>";
											if($i==2) break;
										}
									}else{
										echo "000-000-0000";
									}
								?>

								<? if(strlen($_data->info_email)>0){ ?>
								<div style="margin-top:2px; font-size:11px;">E-mail : <?=$_data->info_email?></div>
								<? } ?>

								<?
									if(strlen($_data->bank_account)>0){
										$bankinfo = explode(',',$_data->bank_account);
										$bankcount = count($bankinfo);
								?>
								<div style="margin-top:8px; font-size:11px;">
									<div style="color:#222222; font-size:10px; font-weight:bold;">ACCOUNT NUMBER.</div>
									<span style="letter-spacing:-1px; font-family:verdana,돋움;">
									<?
										for($i=0;$i<$bankcount;$i++){
											echo str_replace("=","",$bankinfo[$i])."<br />";
										}
									?>
									</span>
								</div>
								<? } ?>
							</td>
						</tr>
					</table>
				</div>

			</td>
		</tr>




		<!--
<?
		$sql = "SELECT date,subject FROM tblcontentinfo ORDER BY date DESC LIMIT ".$_data->main_info_num;
		$result=mysql_query($sql,get_db_conn());
		$i=0;
		$strinfo="";
		while($row=mysql_fetch_object($result)) {
			$i++;
			$strinfo.="<tr>\n";
			$strinfo.="	<td align=\"center\"><IMG SRC=\"".$Dir."images/".$_data->icon_type."/main_skin3_communitynero.gif\" border=\"0\"></td>\n";
			$strinfo.="	<td width=\"100%\" style=\"font-size:11px;letter-spacing:-0.5pt;\"><div style=\"padding-left:3px;white-space:nowrap;width:157px;overflow:hidden;text-overflow:ellipsis;\"><A HREF=\"javascript:information_view('view','".$row->date."');\" onmouseover=\"window.status='정보조회';return true;\" onmouseover=\"window.status='';return true;\"><FONT class=\"maininfo\">".$i.". ".$row->subject."</FONT></A></div></td>\n";
			$strinfo.="</tr>\n";
		}
		//mysql_free_result($result);
		if(strlen($strinfo)>0) {
			$strinfo = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n".$strinfo."</table>\n";
		}
?>
		<?if(strlen($strinfo)>0){?>
		<tr><td height="25"></td></tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
							<A HREF="javascript:information_view('list','')" onmouseover="window.status='정보조회';return true;" onmouseout="window.status='';return true;">
							<?
								if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/main_info_title.gif")) {
									echo "<img src=\"".$Dir.DataDir."design/main_info_title.gif\" border=\"0\" alt=\"정보조회\">";
								} else {
									echo "<img src=\"".$Dir."images/".$_data->icon_type."/main_info_title.gif\" border=\"0\" alt=\"정보조회\">";
								}
							?>
							</a>
						</td>
					</tr>
					<tr>
						<td style="padding:10px;padding-top:5px;padding-bottom:5px;"><?=$strinfo?></td>
					</tr>
				</table>
			</td>
		</tr>
		<?}?>
<?
		$sql = "SELECT COUNT(*) as pollcnt FROM tblsurveymain WHERE display='Y' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		$pollcnt=$row->pollcnt;
		//mysql_free_result($result);
?>
		<?//if($pollcnt>0) {?>
		<tr>
			<td height="25"></td>
		</tr>
		<tr>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_poll_title.gif" border="0"></td>
			</tr>
			<tr>
				<td style="padding:5px;padding-top:5px;padding-bottom:5px;" bgcolor="#F9F9F9"><?// include ($Dir."lib/poll.php"); ?></td>
			</tr>
			</table>
			</td>
		</tr>
		<?//}?>

		</table>
		<!-- main right end --//>
		</td>

		<?//}?>
		-->
	</tr>
	<tr><td height="20"></td></tr>
	</table>
	</td>
</tr>
</table>
<div id="create_openwin" style="display:none"></div>
<? include ($Dir."lib/bottom.php") ?>

<?//이벤트 팝업창 (main???.php에만 include)?>
<? include($Dir."lib/eventlayer.php") ?>

</BODY>
</HTML>