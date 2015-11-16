<?
	if(strlen($Dir)==0) {
		$Dir="../";
	}
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	
	$_data->layoutdata["SHOPLEFTMENUWIDTH"] = (	$_data->layoutdata["SHOPLEFTMENUWIDTH"] =="")?"200":	$_data->layoutdata["SHOPLEFTMENUWIDTH"];
	//장바구니 상품 카운터
	$basketcount = _basketCount('tblbasket',$_ShopInfo->getTempkey());

	if ($_data->frame_type=="N" || strlen($_data->frame_type)==0) {	//투프레임
		if ((strlen($_REQUEST["id"])>0 && strlen($_REQUEST["passwd"])>0) || $_REQUEST["type"]=="logout" || $_REQUEST["type"]=="exit") {
			include($Dir."lib/loginprocess.php");
			exit;
		}
	}

	if(file_exists($Dir.DataDir."shopimages/etc/logo.gif")) {
		$width = getimagesize($Dir.DataDir."shopimages/etc/logo.gif");
		$logo = "<img src=\"".$Dir.DataDir."shopimages/etc/logo.gif\" border=0 ";
		if($width[0]>200) $logo.="width=200 ";
		if($width[1]>65) $logo.="height=65 ";
		$logo.=">";
	} else {
		$logo = "<img src=\"".$Dir."images/".$_data->icon_type."/logo.gif\" border=0>";
	}

	if ($_data->frame_type=="N") {
		$main_target="target=main";

		$result2 = mysql_query("SELECT rightmargin FROM tbltempletinfo WHERE icon_type='".$_data->icon_type."'",get_db_conn());
		if ($row2=mysql_fetch_object($result2)) $rightmargin=$row2->rightmargin;
		else $rightmargin=0;
		mysql_free_result($result2);

	$URL = $_SERVER['HTTP_HOST'];

?>

<html>
<head>
	<meta http-equiv="CONTENT-TYPE"	content="text/html;charset=EUC-KR">
	<META http-equiv="X-UA-Compatible" content="IE=5" >
	<script	type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
	<?include($Dir."lib/style.php") ;?>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
		function sendmail() {
			window.open("<?=$Dir.FrontDir?>email.php","email_pop","height=100,width=100");
		}
		function estimate(type) {
			if(type=="Y") {
				window.open("<?=$Dir.FrontDir?>estimate_popup.php","estimate_pop","height=100,width=100,scrollbars=yes");
			} else if(type=="O") {
				if(typeof(top.main)=="object") {
					top.main.location.href="<?=$Dir.FrontDir?>estimate.php";
				} else {
					document.location.href="<?=$Dir.FrontDir?>estimate.php";
				}
			}
		}

		function privercy() {
			window.open("<?=$Dir.FrontDir?>privercy.php","privercy_pop","height=570,width=590,scrollbars=yes");
		}

		function order_privercy() {
			window.open("<?=$Dir.FrontDir?>privercy.php","privercy_pop","height=570,width=590,scrollbars=yes");
		}

		function logout() {
			location.href="<?=$Dir.MainDir?>main.php?type=logout";
		}

		function sslinfo() {
			window.open("<?=$Dir.FrontDir?>sslinfo.php","sslinfo","width=100,height=100,scrollbars=no");
		}

		function memberout() {
			if(typeof(top.main)=="object") {
				top.main.location.href="<?=$Dir.FrontDir?>mypage_memberout.php";
			} else {
				document.location.href="<?=$Dir.FrontDir?>mypage_memberout.php";
			}
		}

		function notice_view(type,code) {
			if(type=="view") {
				window.open("<?=$Dir.FrontDir?>notice.php?type="+type+"&code="+code,"notice_view","width=450,height=450,scrollbars=yes");
			} else {
				window.open("<?=$Dir.FrontDir?>notice.php?type="+type,"notice_view","width=450,height=450,scrollbars=yes");
			}
		}

		function information_view(type,code) {
			if(type=="view") {
				window.open("<?=$Dir.FrontDir?>information.php?type="+type+"&code="+code,"information_view","width=600,height=500,scrollbars=yes");
			} else {
				window.open("<?=$Dir.FrontDir?>information.php?type="+type,"information_view","width=600,height=500,scrollbars=yes");
			}
		}

		function GoPrdtItem(prcode) {
			window.open("<?=$Dir.FrontDir?>productdetail.php?productcode="+prcode,"prdtItemPop","WIDTH=800,HEIGHT=700 left=0,top=0,toolbar=yes,location=yes,directories=yse,status=yes,menubar=yes,scrollbars=yes,resizable=yes");
		}
	//-->
	</SCRIPT>
</head>

<body rightmargin="<?=$rightmargin?>" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"	style="overflow-x: hidden;overflow-y:hidden;">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td>
<? 
	}

	if($_data->align_type=="Y")	echo "<center>";
	if ($_data->frame_type=="N") {
?>
			</td>
		</tr>
	</table>
</body>
</html>
<? } ?>

<? if($_data->align_type=="Y"){ ?>
	<div class="wrap" style="text-align:center;">
<? }else{ ?>
	<div class="wrap">
<? } ?>
		<div class="topLineMenu">
			<div style="width:<?=($_data->layoutdata["SHOPWIDTH"]>0?$_data->layoutdata["SHOPWIDTH"]:"980")?>px; margin:0 auto;">
				<div class="topFavorite">
					<span><a href="javascript:favorite();">즐겨찾기추가</a></span>
					<? if(strlen($_ShopInfo->getMemid())){ ?>
					<span style="padding:0px 6px; font-size:10px; color:#e5e5e5;">|</span>
					<span class="welcome"><?=$_ShopInfo->getMemid()?></span></b><font style="font-size:8pt;">님, 환영합니다.</font></span>
					<? } ?>
				</div>

				<form name="toploginform" method="post">
				<div class="topMemberMenu">
					<ul>
						<? if(strlen($_ShopInfo->getMemid())==0){ ######## 로그인을 안했다#######?>
						<input type=hidden name="id" size="10">
						<input type=hidden name="passwd" size="10" onkeydown="TopCheckKeyLogin();">
						<li class="firstLi"><a href="<?=$Dir.FrontDir?>login.php"	<?=$main_target?>>로그인</a></li>
						<li><a href="<?=$Dir.FrontDir?>member_agree.php" <?=$main_target?>>회원가입</a></li>
						<? if(isWholesale() == 'Y') { ?>
						<li><a href="<?=$Dir.FrontDir?>member_agree.php?memtype=C">도매회원신청</a></li>
						<? } ?>
						<? }else{ ########## 로그인을	하였다 ############?>
						<li class="firstLi"><a href="javascript:logout();">로그아웃</a></li>
						<li><a href="<?=$Dir.FrontDir?>mypage_usermodify.php"	<?=$main_target?>>정보수정</a></li>
						<? } ?>
						<li><a href="<?=$Dir.FrontDir?>mypage.php" <?=$main_target?>>마이페이지</a></li>
						<li><a href="<?=$Dir.FrontDir?>mypage_orderlist.php" <?=$main_target?>>주문배송조회</a></li>
						<li><a href="<?=$Dir.FrontDir?>basket.php" <?=$main_target?>><span class="basketText">장바구니(<b><?=$basketcount?></b>)</span></a></li>
						<!--<li><a href="javascript:sendmail();">고객센터</a></li>-->
						<li><a href="/front/community.php?code=1">고객센터</a></li>
					</ul>
				</div>
				</form>
			</div>
		</div>

		<div class="topLogoAndSearch">
			<div style="position:relative; width:<?=($_data->layoutdata["SHOPWIDTH"]>0?$_data->layoutdata["SHOPWIDTH"]:"980")?>px; margin:0 auto;">
				<div class="topLogo"><a href="<?=$Dir.MainDir?>main.php" <?=$main_target?>><?=$logo?></a></div>
				<div class="topTagAndSearch">
					<div class="topSearch">
						<form name="search_tform" method="get" action="<?=$Dir.FrontDir?>productsearch.php" <?=$main_target?>>
						<input type="text" name="search" value="<?=$_POST["search"]?>" onkeydown="CheckKeyTopSearch();" />
						<A class="txt" HREF="javascript:TopSearchCheck();">검색</a>
						</form>
						<div>
							<div class="topTagRss">
								<? if($_data->ETCTYPE["TAGTYPE"]!="N") { ?>
								<div><a href="<?=$Dir.FrontDir?>tag.php" <?=$main_target?>><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin3_top_teg.gif" border="0" alt="인기태그" /></a></div>
								<? } ?>
								<div><a href="<?=$Dir.FrontDir?>rssinfo.php" <?=$main_target?>><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin3_top_rss.gif" border="0" alt="RSS" /></a></div>
							</div>

							<? if($_data->search_info["bestkeyword"]=="Y"){ ?>
							<div style="float:left; margin-top:4px;">
								<IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin3_top_popular.gif" border="0" align="absmiddle">&nbsp;
								<font color="#9B9B9B" face="돋움" style="font-size:12pt;">
									<?
										$maxkeylen=36;
										$keygbn=",";
										$keystyle="style='color:#9B9B9B'";
										echo getSearchBestKeyword($main_target,$maxkeylen,$_data->search_info["keyword"],$keygbn,$keystyle);
									?>
								</font>
							</div>
							<? } ?>
						</div>
					</div>
				</div>
				<div class="topCommunity"><a href="<?=$Dir.FrontDir?>newpage.php?code=1"><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin3_top_benefit.gif" border="0" align="absmiddle" alt="" /></a></div>
			</div>
		</div>

		<div class="topPrMenu">
			<div style="width:<?=($_data->layoutdata["SHOPWIDTH"]>0?$_data->layoutdata["SHOPWIDTH"]:"980")?>px; margin:0 auto;">
				<div class="topPrMenuLeft">
					<ul>
						<li><a href="<?=$Dir.FrontDir?>productnew.php" <?=$main_target?>>신상품</a></li>
						<li><a href="<?=$Dir.FrontDir?>productbest.php" <?=$main_target?>>인기상품</a></li>
						<li><a href="<?=$Dir.FrontDir?>producthot.php" <?=$main_target?>>추천상품</a></li>
						<li><a href="<?=$Dir.FrontDir?>productspecial.php" <?=$main_target?>>특별상품</a></li>
						<li><a href="<?=$Dir.FrontDir?>productlist.php?code=002" <?=$main_target?>>기획전</a></li>
					</ul>
				</div>
				<div class="topPrMenuRight">
					<ul>
						<li><a href="<?=$Dir.FrontDir?>gonggu_main.php" <?=$main_target?>>공동구매</a></li>
						<li><a href="/todayshop/" <?=$main_target?>>투데이세일</a></li>
						<? if($_data->coupon_ok == 'Y'){ ?><li><a href="<?=$Dir.FrontDir?>couponlist.php" <?=$main_target?>>쿠폰모음</a></li><? } ?>
						<li><a href="<?=$Dir.FrontDir?>productgift.php" <?=$main_target?>>전용이용권구매</a></li>
						<li><a href="/front/community.php?code=2">커뮤니티</a></li>
					</ul>
				</div>
			</div>
		</div>

	</div>

<script language="javascript">
	//즐겨찾기 추가
	function favorite(){
		window.external.AddFavorite("http://<?=$URL?>","쇼핑몰솔루션은 겟몰!");
	}
</script>