<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

include_once($Dir."lib/ext/func.php");

####################### 페이지 접근권한 check ###############
$PageCode = "st-1";
$MenuCode = "counter";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$regdate=$_shopdata->regdate;

$today = date("Ymd");
$year=date("Y");
$month=date("m");
$day=date("d");
?>


<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>

<script type="text/javascript">
	function doResize(){
		document.getElementById("box").height = framename.document.body.scrollHeight;
	}
</script>


<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
		<col width=198></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top"  background="images/leftmenu_bg.gif">
			<? include ("menu_marketing.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : <span class="2depth_select">마케팅</span></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
        <td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_01_bg.gif"></td>
        <td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr>
        <td width="16" background="images/con_t_04_bg1.gif"></td>
        <td bgcolor="#ffffff" style="padding:10px;padding-left:15px">

			<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<tr><td height="8"></td></tr>
				<TR>
					<TD><IMG SRC="images/marketing_main_title.gif" ALT=""></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height=21></TD>
				</TR>
				<tr><td height="5"></td></tr>
			</TABLE>



			<table align="left" cellpadding="0" cellspacing="0" width="690" style="margin-left:25px;">
				<tr>
					<td height="15"></td>
				</tr>
				<tr>
					<td  id="box">



					<!--부가서비스 > 단계별 마케팅전략-->
					<iframe src="http://www.getmall.co.kr/front/marketing/marketing_main.php?site=<?=urlencode(readAuthKey().'#'.$_ShopInfo->getShopurl().'#'.rand())?>" WIDTH="100%" height="900" frameborder="0" scrolling="no" name="framename" onLoad="doResize()"></iframe>
					<!--부가서비스 > 단계별 마케팅전략-->



					</td>
				</tr>
				<tr>
					<td height="100"></td>
				</tr>
			</table>



		</td>
		<td width="16" background="images/con_t_02_bg.gif"></td>
	</tr>
	<tr>
		<td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
		<td background="images/con_t_04_bg.gif"></td>
		<td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
	</tr>
	<tr><td height="20"></td></tr>
</table>


			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>

<? INCLUDE "copyright.php"; ?>