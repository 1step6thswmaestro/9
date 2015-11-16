<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$coderow=$row;
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('회원 아이디가 존재하지 않습니다.');location.href='".$Dir.FrontDir."login.php';\"></body></html>";exit;
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('처음부터 다시 시작하시기 바랍니다.');location.href='".$Dir.FrontDir."login.php';\"></body></html>";exit;
	}
	$url_id = $row->url_id;
}


$leftmenu="Y";
$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='memberout'";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$body=$row->body;
	$body=str_replace("[DIR]",$Dir,$body);
	$leftmenu=$row->leftmenu;
	$newdesign="Y";
}
mysql_free_result($result);

?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 마이페이지 > 나의홍보관리 > 나의 SNS채널 등록 및 홍보URL</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/jquery-1.4.2.min.js"></script>
<?include($Dir."lib/style.php")?>
</HEAD>
<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
	include ($Dir.MainDir.$_data->menu_type.".php");
	include_once("./mypage_groupinfo.php"); //회원등급 출력용
?>

<!-- 마이페이지-홍보관리 상단 메뉴 -->
<div class="mypagemembergroup">
	<div class="groupinfotext">안녕하세요? <strong class="st1"><?=$_ShopInfo->getMemname()?></strong>님. 회원님의 등급은 <strong class="st2"><?=$groupname?></strong>입니다.</span></div>
	<div class="gruopinfogo"><a href="/front/newpage.php?code=1">회원정책보기 &gt;</a></div>
</div>
<table border="0" cellpadding="0" cellspacing="0" class="mypagetmenu">
	<tr>
		<td class="leftline"><a href="/front/mypage.php">마이페이지</a></td>
		<td class="leftline"><a href="/front/mypage_orderlist.php">주문내역</a></td>
		<td class="leftline"><a href="/front/mypage_personal.php">1:1 문의</a></td>
		<td class="leftline"><a href="/front/mypage_reserve.php">적립금</a></td>
		<td class="leftline"><a href="/front/wishlist.php">찜하기</a></td>
		<td class="leftline"><a href="/front/mypage_coupon.php">쿠폰내역</a></td>
		<? if($_data->recom_url_ok == "Y" || $_data->sns_ok == "Y"){ ?><td class="nowMyage"><a href="/front/mypage_promote.php">홍보관리</a></td><? } ?>
		<? if(getVenderUsed()==true) { ?><td><a href="/front/mypage_custsect.php">단골매장</a></td><? } ?>
		<td><a href="/front/mypage_usermodify.php">회원정보</a></td>
		<td><a href="/front/mypage_memberout.php">회원탈퇴</a></td>
	</tr>
</table>
<div class="currentTitle">
	<div class="titleimage">홍보관리</div>
	<div class="current">홈 &gt; 마이페이지 &gt; <SPAN class="nowCurrent">홍보관리</span></div>
</div>
<!-- 마이페이지-홍보관리 상단 메뉴 -->



<table border="0" cellpadding="0" cellspacing="0" width="100%">

<?
//if ($leftmenu!="N") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/mypromote_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/mypromote_title.gif\" border=\"0\" alt=\"나의 홍보관리\"></td>\n";
	} else {
		echo "<td>\n";
		/*
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/mypromote_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/myreserve_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/myreserve_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		*/
		echo "</td>\n";
	}
	echo "</tr>\n";
//}

if($_data->design_mypage =="001")
	$designMypage = "3";
else if($_data->design_mypage =="002")
	$designMypage = "2";
else if($_data->design_mypage =="003")
	$designMypage = "1";
else
	$designMypage = "3";
?>
	<!--
	<tr>
		<td style="padding:5px;padding-top:0px;">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
							<TR>
								<TD><A HREF="<?=$Dir.FrontDir?>mypage.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu1.gif" BORDER="0"></A></TD>
								<TD><A HREF="<?=$Dir.FrontDir?>mypage_orderlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu2.gif" BORDER="0"></A></TD>
								<TD><A HREF="<?=$Dir.FrontDir?>mypage_personal.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu3.gif" BORDER="0"></A></TD>
								<TD><A HREF="<?=$Dir.FrontDir?>wishlist.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu4.gif" BORDER="0"></A></TD>
								<TD><A HREF="<?=$Dir.FrontDir?>mypage_reserve.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu5.gif" BORDER="0"></A></TD>
								<TD><A HREF="<?=$Dir.FrontDir?>mypage_coupon.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu6.gif" BORDER="0"></A></TD>
								<?if($_data->recom_url_ok == "Y" || $_data->sns_ok == "Y"){?><TD><A HREF="<?=$Dir.FrontDir?>mypage_promote.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu10r.gif" BORDER="0"></A></TD><? } ?>
								<TD><A HREF="<?=$Dir.FrontDir?>mypage_gonggu.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu11.gif" BORDER="0"></A></TD>
								<? if(getVenderUsed()==true) { ?><TD><A HREF="<?=$Dir.FrontDir?>mypage_custsect.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu9.gif" BORDER="0"></A></TD><? } ?>
								<TD><A HREF="<?=$Dir.FrontDir?>mypage_usermodify.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu7.gif" BORDER="0"></A></TD>
								<TD><A HREF="<?=$Dir.FrontDir?>mypage_memberout.php"><IMG SRC="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menu8.gif" BORDER="0"></A></TD>
								<TD width="100%" background="<?=$Dir?>images/common/mypersonal_skin<?=$designMypage?>_menubg.gif"></TD>
							</TR>
						</TABLE>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	-->
	<tr><td height="20"></td></tr>
	<tr>
	<td>
		<table align="center" cellpadding="0" cellspacing="0" width="100%">
<?
if($_data->recom_url_ok == "Y" ){
	$arRecomType = explode("", $_data->recom_memreserve_type);
	$sAddRecom = "";
	if($arRecomType[0] == "A"){
		$sAddRecom = "회원님의 URL주소를 통해 신규회원가입시 회원님에게 <span style='color:#f00;font-weight:bold'>".number_format($_data->recom_memreserve)."원의 적립금</span>이 지급됩니다.";
	}else if($arRecomType[0] == "B"){
		$sAddRecom = "회원님의 URL주소를 통해 가입한 회원의 첫 구매가 이루어지면 <span style='color:#f00;font-weight:bold'>";
		if($arRecomType[1] == "A"){
			if($arRecomType[2] == "N"){
				$sAddRecom .= number_format($_data->recom_memreserve)."원의";
			}else if($arRecomType[2] == "Y"){
				$sAddRecom .= "구매금액의 ".$_data->recom_memreserve."%의";
			}
		}else if($arRecomType[1] == "B"){
			$sAddRecom .= "구매금액에 따른";
		}
		$sAddRecom .= " 적립금</span>이 회원님에게 지급됩니다.";
	}
	$sql = "SELECT COUNT(*) as cnt FROM tblmember WHERE rec_id='".$_ShopInfo->getMemid()."'";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$recom_cnt = $row->cnt;
	mysql_free_result($result);
?>
			<tr>
				<td><img src="../images/design/promote_sstitle02.gif"></td>
			</tr>
			<? if(strlen($sAddRecom)>0){ ?>
			<tr>
				<td class="table_td"><IMG vspace=3 align=absMiddle src="../images/design/icon_star.gif"><?=$sAddRecom?></td>
			</tr>
			<? } ?>
			<tr>
				<td style="PADDING-BOTTOM: 6px; PADDING-LEFT: 6px; PADDING-RIGHT: 6px; PADDING-TOP: 6px" bgColor=#eaeaea>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=#ffffff>
						<TR>
							<TD style="PADDING-BOTTOM: 20px; PADDING-LEFT: 20px; PADDING-RIGHT: 20px; PADDING-TOP: 20px" width="100%">
								<TABLE cellSpacing=0 cellPadding=0 align=center width="100%">
									<TR>
										<TD style="LINE-HEIGHT: 25px; LETTER-SPACING: -0.5pt; FONT-SIZE: 23px" align="center"><FONT color=#ff6600><B><?=$_ShopInfo->getMemname()?>님만의 고유한 URL주소는<br>http://<?=$_ShopInfo->getShopurl()?>?token=<?=$url_id?>입니다.</B></FONT></TD>
										<TD align=right><a href="/front/member_urlhongbo.php"><img src="../images/design/promote_btn_01.gif" border=0></a></TD>
									</TR>
								</TABLE>
							</TD>
							<TD background="../images/common/wishlist/001/design_search_skin3_t4bg.gif"></TD>
						</TR>
					</TABLE>


				</td>
			</tr>
<?
}
if($_data->sns_ok == "Y") {
?>
			<tr>
				<td height="30"></td>
			</tr>
			<tr>
				<td><img src="../images/design/promote_sstitle01.gif"></td>
			</tr>
			<!--
			<?// if(ME2DAY_ID!="ME2DAY_ID"){?>
			<tr>
				<td>
					<table cellpadding="17" cellspacing="6" width="100%" bgcolor="#F6F6F6">
						<tr>
							<td bgcolor="white">
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td><img src="../images/design/promote_img_me2day.gif" width="200" height="54" border="0"></td>
										<td width="103" align="right"><a href="javascript:snsLogin('m');"><img src="../images/design/promote_btn_login.gif" width="103" height="54" border="0" id="meLoginBtn"></a></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<?//}?>
			-->
			<? if(TWITTER_ID !="TWITTER_ID"){?>
			<tr>
				<td>
					<table cellpadding="17" cellspacing="6" width="100%" bgcolor="#F6F6F6">
						<tr>
							<td bgcolor="white" style="padding:20px;">
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td><img src="../images/design/promote_img_twitter.gif" width="160" height="54" border="0"></td>
										<td width="103" align="right"><a href="javascript:snsLogin('t');"><img src="../images/design/promote_btn_login.gif" width="103" height="54" border="0" id="twLoginBtn"></a></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<?}?>
			<? if(FACEBOOK_ID!="FACEBOOK_ID"){?>
			<tr>
				<td>
					<table cellpadding="17" cellspacing="6" width="100%" bgcolor="#F6F6F6">
						<tr>
							<td bgcolor="white" style="padding:20px;">
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td><img src="../images/design/promote_img_facebook.gif" width="160" height="54" border="0"></td>
										<td width="103" align="right"><a href="javascript:snsLogin('f');"><img src="../images/design/promote_btn_login.gif" width="103" height="54" border="0" id="fbLoginBtn"></a></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
<?
			}
?>
			<tr>
				<td height="30"></td>
			</tr>
			<tr>
				<td><img src="../images/design/promote_sstitle03.gif" alt="나의 sns 추천내역"></td>
			</tr>
			<tr>
				<td height="2" bgcolor="#333333"></td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<tr>
				<td id="mypage_snsList">
				</td>
			</tr>
<?}?>
		</table>

	</td>
</tr>
<tr>
	<td height="30"></td>
</tr>
</table>
<?if($_data->sns_ok == "Y") {?>
<script type="text/javascript">
<!--
$j(document).ready( function () {

	snsInfo();
});

function snsInfo(){
	$j.post("snsAction.php", { "method": "snsLoginCheck" },
	 function(data){
		if (data.result == 'true') {
			if($j("#meLoginBtn").attr('src')){
				if ( data.me2day == "N" || data.me2day == undefined ) {
					$j("#meLoginBtn").attr('src',$j("#meLoginBtn").attr('src').replace('_logout','_login'));
				}
				else {
					$j("#meLoginBtn").attr('src',$j("#meLoginBtn").attr('src').replace('_login','_logout'));
				}
			}

			if($j("#twLoginBtn").attr('src')){
				if ( data.twitter == "N" || data.twitter == undefined ) {
					$j("#twLoginBtn").attr('src',$j("#twLoginBtn").attr('src').replace('_logout','_login'));
				}
				else {
					$j("#twLoginBtn").attr('src',$j("#twLoginBtn").attr('src').replace('_login','_logout'));
				}
			}
			if($j("#fbLoginBtn").attr('src')){
				if ( data.facebook == "N" || data.facebook == undefined ) {
					$j("#fbLoginBtn").attr('src',$j("#fbLoginBtn").attr('src').replace('_logout','_login'));
				}
				else {
					$j("#fbLoginBtn").attr('src',$j("#fbLoginBtn").attr('src').replace('_login','_logout'));
				}
			}
		}
		else if (data.result == 'nodata') {
			//alert("sns 채널정보 없음");
		}
	 }, "json");
}

function snsLogin(type){
	if(type == "m") {
		if($j("#meLoginBtn").attr("src").indexOf("_login.gif") >0){
			window.open("snsLogin.php?type="+type,  'snsLogin', 'width=800, height=500, top=0, left=0, scrollbars=yes');
		} else {
			$j("#meLoginBtn").attr('src',$j("#meLoginBtn").attr('src').replace('_logout','_login'));
			snsLogout(type);
		}
	}else if(type == "t") {
		if($j("#twLoginBtn").attr("src").indexOf("_login.gif") >0){
			window.open("snsLogin.php?type="+type,  'snsLogin', 'width=800, height=500, top=0, left=0, scrollbars=yes');
		} else {
			$j("#twLoginBtn").attr('src',$j("#twLoginBtn").attr('src').replace('_logout','_login'));
			snsLogout(type);
		}
	}else if(type == "f") {
		if($j("#fbLoginBtn").attr("src").indexOf("_login.gif") >0){
			window.open("facebook.php",  'snsLogin', 'width=1000, height=630, top=0, left=0, scrollbars=yes');
		} else {
			$j("#fbLoginBtn").attr('src',$j("#fbLoginBtn").attr('src').replace('_logout','_login'));
			snsLogout(type);
		}
	}
}

function snsLogout(type){
	$j.post("snsAction.php", { "method": "snsChange",  "sns_type":type,  "sns_state":"N" },
	 function(data){
		if (data.result == 'true') {
			//alert("Logout");
		}
		else {
			//alert("에러 발생 : " + data.message );
		}
	 }, "json");
}
function getSnsList(block ,pgid){
	$j.post(
		"mypageSnsList.php",
		{list_num :5,  type:2, gotopage :pgid, block :block},
		  function(data){
			$j("#mypage_snsList").html(data);
		}
	)
}
getSnsList();
//-->
</script>
<?}?>
<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>