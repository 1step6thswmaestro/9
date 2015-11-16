<?
// 디자인 미리보기 적용
$preview=false;
foreach ( $_GET as $k=>$v ) {
	if( $k == 'preview' ) {
		$preview=true;
	}
}

if( $preview===true ) {

	// 미리보기 적용
	$designnewpageTables = "tbldesignnewpage_prev";
	$btbackgroundIMG = $Dir.$Dir.DataDir."shopimages/etc/btbackground_prev.gif";

	// <!----
	/*
	// 화면 컨트롤 안되게,,,
	$notControlPage = "
		<script type=\"text/javascript\">
		<!--
			onload = function () {
				var H = document.body.offsetHeight;
				H = H + 10;
				H = H + 'px';
				//alert(H);
				document.getElementById(\"previewIframe\").style.height = H;
			}
		//-->
		</script>
		<iframe id=\"previewIframe\" name=\"previewIframe\" allowtransparency='true' style=\"position:absolute; z-index:99999; width:100%; height:100px;\" frameborder='0'></iframe>
	";
	*/

	$notControlPage .= "<DIV style=\"width:100%; background-color:#f5f5f5; text-align:center; font-size:11px; padding:8px; border-bottom:1px solid #e5e5e5;\">본 페이지는 <span style=\"color:#ff3300;\"><b>디자인 미리보기</b></span> 페이지 입니다. (본 페이지에서만 해당 됨)</DIV>";

	echo $notControlPage;
	// --- >

	// 강제 디자인 활성
	$_data->main_type = "mainm";
	$_data->menu_type = "menup";
	$_data->frame_type = "Y";
	$_data->onetop_type = "topp";
	$_data->design_tag = "U";
	$_data->design_prnew = "U";
	$_data->design_prbest = "U";
	$_data->design_prhot = "U";
	$_data->design_prspecial = "U";
	$_data->design_search = "U";
	$_data->design_basket = "U";
	$_data->design_bmap = "U";
	$_data->design_tagsearch = "U";
	$_data->design_information = "U";
	$_data->design_notice = "U";
	$_data->design_mail = "U";
	$_data->design_mycustsect = "U";
	$_data->design_mypersonal = "U";
	$_data->design_myreserve = "U";
	$_data->design_mycoupon = "U";
	$_data->design_orderlist = "U";
	$_data->design_mypage = "U";
	$_data->design_order = "U";
	$_data->design_mbmodify = "U";
	$_data->design_mbjoin = "U";
	$_data->design_member = "U";
	$_data->design_useinfo = "U";
	$_data->design_wishlist = "U";
	$_data->design_basket = "U";
	$_data->design_intro = "U";




} else {

	// 기본 설정
	$designnewpageTables = "tbldesignnewpage";
	$btbackgroundIMG = $Dir.DataDir."shopimages/etc/btbackground.gif";

}

?>