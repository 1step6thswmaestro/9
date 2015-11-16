<?
if(strlen($Dir)==0) {
	$Dir="../";
}
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$_data->layoutdata["SHOPLEFTMENUWIDTH"] = (	$_data->layoutdata["SHOPLEFTMENUWIDTH"] =="")?"200":	$_data->layoutdata["SHOPLEFTMENUWIDTH"];
if ($_data->frame_type=="N" || strlen($_data->frame_type)==0) {	//투프레임
	$_REQUEST["id"]=(isset($_REQUEST["id"])?$_REQUEST["id"]:"");
	$_REQUEST["passwd"]=(isset($_REQUEST["passwd"])?$_REQUEST["passwd"]:"");
	$_REQUEST["type"]=(isset($_REQUEST["type"])?$_REQUEST["type"]:"");

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

?>

<html>
<head>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<? include($Dir."lib/style.php") ?>
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

<body rightmargin="<?=$rightmargin?>" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" style="overflow-x:hidden;overflow-y:hidden;">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td background="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topbg.gif">
<?
}
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td background="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topbg.gif">
<?
if($_data->align_type=="Y") echo "<center>";
?>

	<table width="<?=($_data->layoutdata["SHOPWIDTH"]>0?$_data->layoutdata["SHOPWIDTH"]:"900")?>" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<col width="180"></col>
		<col></col>
		<tr valign="bottom">
			<td>
			<table border=0 cellpadding=0 cellspacing=0>
			<tr>
				<td height=65 valign=bottom><a href="<?=$Dir.MainDir?>main.php" <?=$main_target?>><?=$logo?></a></td>
			</tr>
			</table>
			</td>
			<td style="padding-bottom:8px;">
			<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr>
				<td style="padding-top:5pt;">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align="right">
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td><?if($_data->ETCTYPE["TAGTYPE"]!="N") {?><a href="<?=$Dir.FrontDir?>tag.php" <?=$main_target?>><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_top_teg.gif" border="0" valign="absmiddle"></a><?}?></td>
						<td><a href="<?=$Dir.FrontDir?>rssinfo.php" <?=$main_target?>><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_top_rss.gif" border="0" valign="absmiddle" hspace="5"></a></td>
						<?
							if(isWholesale() == 'Y') {
						?>
							<td><a HREF="<?=$Dir.FrontDir?>member_agree.php?memtype=C"><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_top_join2.gif" border="0" alt="도매회원신청"></a></td>
						<?
							}
						?>
						<td><a href="<?=$Dir.FrontDir?>basket.php" <?=$main_target?>><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenu5.gif" border="0"></a></td>
						<td><a href="<?=$Dir.FrontDir?>mypage_orderlist.php" <?=$main_target?>><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenu6.gif" border="0"></a></td>
						<td><a href="<?=$Dir.BoardDir?>board.php?board=qna" <?=$main_target?>><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenu7.gif" border="0"></a></td>
						<td><A HREF="javascript:sendmail();"><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenu8.gif" border="0"></a></td>
						<td><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenu_l.gif" border="0"></td>
						<td><a href="<?=$Dir.FrontDir?>productgift.php" <?=$main_target?>><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenu_m.gif" border="0"></a></td>
						<td><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenu_l.gif" border="0"></td>
						<?if($_data->recom_url_ok == "Y"){?><td><a href="javascript:win_hongboUrl()"><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenu_p.gif" border="0"></a></td><?}?>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" background="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenubg.gif">
				<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" width="100%">
					<tr>
						<td>
						<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0">
						<TR>
							<td><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenu_img.gif" border="0"></td>
							<TD><a href="<?=$Dir.FrontDir?>productnew.php" <?=$main_target?>><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenu1.gif" border="0" alt="신상품"></a></TD>
							<TD><a href="<?=$Dir.FrontDir?>productbest.php" <?=$main_target?>><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenu2.gif" border="0" alt="인기상품"></a></TD>
							<TD><a href="<?=$Dir.FrontDir?>producthot.php" <?=$main_target?>><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenu3.gif" border="0" alt="추천상품"></a></TD>
							<TD><a href="<?=$Dir.FrontDir?>productspecial.php" <?=$main_target?>><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenu4.gif" border="0" alt="특별상품"></a></TD>
							<td><a href="<?=$Dir.FrontDir?>gonggu_main.php" <?=$main_target?>><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenu12.gif" border="0" alt="공동구매"></a></td>
							<td><a href="<?=$Dir.FrontDir?>couponlist.php" <?=$main_target?>><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenu13.gif" border="0" alt="쿠폰모음"></a></td>
						</tr>
						</table>
						</td>
						<td align="right" width="100%">
						<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0">
						<form name=search_tform method=get action="<?=$Dir.FrontDir?>productsearch.php" <?=$main_target?>>
						<tr>
							<td valign="bottom" style="padding-left:2px;" style="padding-right:3px;">
							<INPUT type=text name="search" value="<?=(isset($_POST["search"])?$_POST["search"]:"")?>" size="18" onkeydown="CheckKeyTopSearch()" style="font-size:11px;background-color:#FFFFFF;padding-top:2pt;padding-bottom:1pt;border-top:#808080 1px solid;border-left:#808080 1px solid;border-bottom:#E8E8E8 1px solid;border-right:#E8E8E8 1px solid; width:200px; height:19px;">
							</td>
							<td><A HREF="javascript:TopSearchCheck()"><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_top_search.gif" border="0" align="absMiddle"></a></td>
						</tr>
						<?if($_data->search_info["bestkeyword"]=="Y"){?>
						<tr>
							<td colspan="2" valign="bottom" style="padding-left:2px;padding-top:2px;"><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_top_popular.gif" border="0" align="absMiddle">
							<font color="#E4E4E4" face="돋움" style="font-size:12pt;">
							<?
							$maxkeylen=30;
							$keygbn=",";
							$keystyle="style='color:#E4E4E4'";
							echo getSearchBestKeyword($main_target,$maxkeylen,$_data->search_info["keyword"],$keygbn,$keystyle);
							?>
							</font>
							</td>
						</tr>
						<?}?>
						</form>
						</table>
						</td>
						<td background="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenubg.gif"><IMG SRC="<?=$Dir?>images/<?=$_data->icon_type?>/main_skin1_topmenu_img1.gif" border="0"></td>
					</tr>
				</table>
			</td>
		</tr>
		</table>




		</td>
	</tr>
	<tr>
		<td height="5"></td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?
if($_data->align_type=="Y") echo "<center>";
if ($_data->frame_type=="N") {
?>
	</td>
</tr>
</table>
</body>
</html>
<?
}
?>