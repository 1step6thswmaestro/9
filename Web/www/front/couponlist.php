<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");


	// 쿠폰디자인
	$couponTopDesign = '';
	$sql="SELECT body FROM `tbldesigndefault` WHERE `type`='cpnlisttop' LIMIT 1;";
	if(false === $result=mysql_query($sql,get_db_conn())){
	}else if(mysql_num_rows($result)){
		$couponTopDesign = mysql_result($result,0,0);
	}
	
	// 정열
	$sort = ( empty($_GET['sort']) )?"new":$_GET['sort'];
	if( $sort == 'end' ) {
		$SQL_SORT = "if(date_start<0,DATE_FORMAT(SYSDATE(date)+INTERVAL ABS(date_start) DAY,'%Y%m%d%H'),date_end) ASC";
	}
	if( $sort == 'new' ) {
		$SQL_SORT = "date DESC";
	}
	$sortB[$sort][0] = "<b>";
	$sortB[$sort][1] = "</b>";
	

	$limitcouponarray = array();

	$limitcpSQL = "SELECT co.coupon_code FROM tblcouponinfo AS co LEFT JOIN tblcouponissue AS ce ON co.coupon_code = ce.coupon_code WHERE ce.id = '".$_ShopInfo->getMemid()."' AND co.repeat_id = 'N' AND ce.coupon_code IS NOT NULL ORDER BY co.date DESC";
	if(false !== $limitcpRes = mysql_query($limitcpSQL,get_db_conn())){
		$limitcprowcount= mysql_num_rows($limitcpRes);

		if($limitcprowcount>0){
			while($limitcprow = mysql_fetch_assoc($limitcpRes)){
				array_push($limitcouponarray,$limitcprow['coupon_code']);
			}
		}
	}
	
	$reusedcpSQL = "SELECT co.coupon_code FROM tblcouponinfo AS co LEFT JOIN tblcouponissue AS ce ON co.coupon_code = ce.coupon_code WHERE ce.id = '".$_ShopInfo->getMemid()."' AND co.repeat_id = 'Y' AND ce.used='N' ORDER BY co.date DESC";

	if(false !== $recpRes = mysql_query($reusedcpSQL,get_db_conn())){
		$recprowcount= mysql_num_rows($recpRes);

		if($recprowcount>0){
			while($recprow = mysql_fetch_assoc($recpRes)){
				array_push($limitcouponarray,$recprow['coupon_code']);
			}
		}
	}

	
?>

<HTML>
<HEAD>
<TITLE>쿠폰모음</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>

<style>
	.table_border { border-collapse:collapse; border-right:2px dashed #c4c4c4; border-bottom:2px dashed #c4c4c4; border-left:2px dashed #c4c4c4; }
	.table_border td { border-right:2px dashed #c4c4c4; border-bottom:2px dashed #c4c4c4; border-left:2px dashed #c4c4c4; }
	.table_border_total { border-top:2px dashed #c4c4c4; border-right:2px dashed #c4c4c4; }
	.table_border_total td { border-bottom:2px dashed #c4c4c4; border-left:2px dashed #c4c4c4; }

	.table_nborder { border:0px; }
	.table_nborder td { border:0px; }

	.coupon_pimage { padding-right:20px; }
	.coupon_btext { color:#222222; font-size:12px; font-weight:bold; letter-spacing:-0.08em; line-height:16px; }
	.coupon_stext { color:#666666; font-size:11px; line-height:18px; }
	.coupon_ptext { color:#ff3300; font-size:20px; font-family:verdana; font-weight:bold; letter-spacing:-1px; line-height:18px; }
	.coupon_ctext { font-size:11px; line-height:120%; }
</style>

<SCRIPT LANGUAGE="JavaScript">
	<!--
	//탭 처리
	function DisplayMenu(index) {
		for (i=1; i<=8; i++)
		if (index == i) {
			thisMenu = eval("themeShopTab" + index + ".style");
			thisMenu.display = "";
		}else{
			otherMenu = eval("themeShopTab" + i + ".style"); 
			otherMenu.display = "none"; 
		}
	}


	// 쿠폰 발급
	<?if($_data->coupon_ok=="Y") {?>
	function issue_coupon(coupon_code){
		document.couponform.mode.value="coupon";
		document.couponform.coupon_code.value=coupon_code;
		document.couponform.submit();
	}
	<?}?>
	//-->
</script>
</HEAD>

<body <?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
	include ($Dir.MainDir.$_data->menu_type.".php");
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
	if ($leftmenu!="N") {
		echo "<tr>\n";
		echo "	<td>\n";
		/*
		echo "	<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "		<TR>\n";
		echo "			<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/couponlist_title_head.gif ALT=></TD>\n";
		echo "			<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/couponlist_title_bg.gif></TD>\n";
		echo "			<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/couponlist_title_tail.gif ALT=></TD>\n";
		echo "		</TR>\n";
		echo "	</TABLE>\n";
		*/
		echo "	</td>\n";
		echo "</tr>\n";
	}
?>
	<tr>
		<td>
			<!-- 관리자 페이지에서 html모드로 내용을 등록할 수 있도록 처리 -->
			<?=$couponTopDesign?>
			<!-- <IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/couponlist_t_banner.jpg"> -->
		</td>
	</tr>
	<tr><td height="10"></td></tr>
	<tr>
		<td style="padding:30px 40px; border:1px solid #dddddd;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr><td><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/couponlist_text.gif"></td></tr>
				<tr><td height="10"></td></tr>
				<tr><td height="35"><a name="couponlist"></td></tr>
				<tr>
					<td valign="top">

						<div class="themeShop">
							<div id="themeShopTab1" style="display:;">
								<? include 'couponlist_all.php';//전체 쿠폰 ?>
							</div>

							<div id="themeShopTab2" style="display:none;">
								<? include 'couponlist_alluse.php';//전체사용가능 쿠폰 ?>
							</div>

							<div id="themeShopTab3" style="display:none;">
								<? include 'couponlist_product.php';//상품별 쿠폰 ?>
							</div>

							<div id="themeShopTab4" style="display:none;">
								<? include 'couponlist_category.php';//카테고리별 쿠폰 ?>
							</div>
						</div>

						<?
							/*
							include 'couponlist_all.php';//전체 쿠폰
							include 'couponlist_alluse.php';//전체사용가능 쿠폰
							include 'couponlist_product.php';//상품별 쿠폰
							include 'couponlist_category.php';//카테고리별 쿠폰

							/*
							if( ($productcodeCNT+$couponCNT+$cate_i)==0 ){
								echo "
									<table border=0 align=center>
										<tr>
											<td align=center height=50>등록된 쿠폰이 없습니다.</td>
										</tr>
									</table>
								";
							}
							*/
						?>

					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td height="30"></td></tr>
	<tr>
		<td style="line-height:20px;">
			오프라인 매장이나 행사에서 지급받으신 오프라인 쿠폰 및 이용권이 있으신가요?<br />
			[오프라인 쿠폰등록]을 통해서 오프라인에서 발급받으신 쿠폰을 등록하시면 상품결제시 쿠폰으로 상품구매가 가능합니다.<br />
			※ 오프라인 쿠폰등록은 쇼핑몰 회원이거나 회원가입을 하셔야 등록 가능합니다.
			<a href="/front/mypage_coupon.php"><img src="/images/003/offcoupon_bt.gif" border="0" align="absmiddle" alt="" /></a>
		</td>
	</tr>
	<tr><td height="50"></td></tr>
</table>

<form name="couponform" method="POST" action="couponlist_process.php" target="couponlistProcessFrame">
	<input type=hidden name="mode" value="">
	<input type=hidden name="coupon_code" value="">
</form>

<iframe name="couponlistProcessFrame" id="couponlistProcessFrame" style="display:none"></iframe>


<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>