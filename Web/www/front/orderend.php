<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$ordercode=$_POST["ordercode"];

if(substr($ordercode,0,8)<=date("Ymd",mktime(0,0,0,date("m"),date("d")-3,date("Y")))) {
	echo "<html></head><body onload=\"alert('잘못된 경로로 접근하셨습니다.(0)'); location.href='".$Dir."'\"></body></html>";
	exit;
}

$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$_ord=$row;
	$gift_price=$_ord->price-$row->deli_price;

	$receiver_addr = explode('주소 : ',$_ord->receiver_addr);
	$zipCode  = explode('우편번호 : ',$receiver_addr[0]);

	$sql = "select mobile from tblmember where id='".$_ord->id."'";
	$resultm=mysql_query($sql,get_db_conn());
	if($rowm=mysql_fetch_object($resultm)) {
		if (strlen($rowm->mobile)>0) $mobile = $rowm->mobile;
		$mobile=explode("-",replace_tel(check_num($mobile)));

	}

} else {
	echo "<html></head><body onload=\"alert('잘못된 경로로 접근하셨습니다.(1)'); location.href='/'\"></body></html>";
	exit;
}
mysql_free_result($result);

if (preg_match("/^(V|O|Q|C|P|M)$/", $_ord->paymethod) && $_ord->deli_gbn=="C") {
	$_ord->pay_data = "결제 중 주문취소";
}

$gift_type=explode("|",$_data->gift_type);
$gift_cnt=0;
if (($_ord->paymethod=="B" || (preg_match("/^(V|O|Q|C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) && $_ord->deli_gbn=="N" && strlen($_ShopInfo->getGifttempkey())>0) {
	if ($gift_type[2]=="A" || strlen($gift_type[2])==0 || ($gift_type[2]=="B" && $_ord->paymethod=="B")) {
		if (($gift_type[0]=="M" && strlen($_ShopInfo->getMemid())>0) || $gift_type[0]=="C") { // 회원전용, 비회원+회원
			$sql = "SELECT COUNT(*) as gift_cnt FROM tblgiftinfo ";
			if($gift_type[1]=="N") {
				$sql.= "WHERE gift_startprice<=".$gift_price." AND gift_endprice>".$gift_price." ";
			} else  {
				$sql.= "WHERE gift_startprice<=".$gift_price." ";
			}
			$sql.= "AND (gift_quantity is NULL OR gift_quantity>0) ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			$gift_cnt=$row->gift_cnt;
			mysql_free_result($result);
		}
	}
}
$gift_cnt=0;
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 주문완료</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<link href="<?=$Dir?>css/endod_style.css" rel='stylesheet' type='text/css' />
<SCRIPT LANGUAGE="JavaScript">
<!--
function OrderDetailPrint(ordercode) {
	document.form2.ordercode.value=ordercode;
	document.form2.print.value="OK";
	window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
	document.form2.submit();
}

function setPackageShow(packageid) {
	if(packageid.length>0 && document.getElementById(packageid)) {
		if(document.getElementById(packageid).style.display=="none") {
			document.getElementById(packageid).style.display="";
		} else {
			document.getElementById(packageid).style.display="none";
		}
	}
}
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
<? /*<map id="endod_top" >
<area shape="rect" coords="761,121,909,155" href="#" />
<area shape="rect" coords="611,121,759,155" href="#" />
<area shape="rect" coords="461,121,609,155" href="#" />
</map> */ ?>
<?
if(substr($_data->design_order,0,1)=="T") {
	$_data->menu_type="nomenu";

}
include ($Dir.MainDir.$_data->menu_type.".php");
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<?

if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/orderend_title.gif")) {
	echo "<td><img src=\"".$Dir.DataDir."design/orderend_title.gif\" border=\"0\" alt=\"주문완료\"></td>\n";
} else {
	echo "<td>\n";
	/*
	echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
	echo "<TR>\n";
	echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/orderend_title_head.jpg usemap='#endod_top' ></TD>\n";
	echo "</TR>\n";
	echo "</TABLE>\n";*/
	echo "</td>\n";
}
?>
</tr>
<tr>
	<td align="center"><?
		//echo $Dir.TempletDir."orderend/orderend".$_data->design_order.".php";
		include ($Dir.TempletDir."orderend/orderend".$_data->design_order.".php");
	?></td>
</tr>
<? if($gift_cnt>0) {?>
<tr>
	<td align="center">
	<div id="gift_layer" style="position:absolute; width:381; height:228; z-index:1; visibility: hidden">
	<table border=0 cellpadding=0 cellspacing=0 width=381 height=228 background="">
	<tr>
		<td><img src="<?=$Dir?>images/common/gift_choicebg.gif" border="0" USEMAP="#gifimage"></td>
	</tr>
	</table>
	<MAP NAME="gifimage">
	<AREA SHAPE="rect" COORDS="332,12,377,27" HREF="javascript:gift_close();">
	<AREA SHAPE="rect" COORDS="229,179,324,207" HREF="javascript:getGift();">
	</MAP>
	</div>
	</td>
</tr>
<tr><td height="20"></td></tr>

<form name=giftform method=post action="<?=$Dir.FrontDir?>gift_choice.php" target="gift_popwin">
<input type=hidden name=gift_price value="<?=$gift_price?>">
<input type=hidden name=ordercode value="<?=$ordercode?>">
</form>

<SCRIPT LANGUAGE="JavaScript">
<!--
function gift_show() {
	gift_layer.style.posLeft=screen.availWidth/2-190;
	gift_layer.style.posTop=screen.availHeight/2-90;
	gift_layer.style.visibility="visible";
}
function gift_close() {
	gift_layer.style.visibility="hidden";
}
function getGift() {
	gift_close();
	gift_popwin = window.open("about:blank","gift_popwin","width=700,height=600,scrollbars=yes");
	if (!gift_popwin) gift_show();
	document.giftform.target="gift_popwin";
	document.giftform.submit();
	gift_popwin.focus();
}
getGift();
//-->
</SCRIPT>
<?}?>
</table>

<form name=form2 method=post action="<?=$Dir.FrontDir?>orderdetailpop.php" target="orderpop">
<input type=hidden name=ordercode>
<input type=hidden name=print>
</form>

<?=$onload?>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>