<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$type=$_REQUEST["type"];
if(strlen($type)==0 || $type!="complete") $type="";

$tname0="auction_menu3.gif";
$tname1="auction_menu4.gif";
if($type=="complete") {
	$tname1="auction_menu4r.gif";
} else {
	$tname0="auction_menu3r.gif";
}
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - My Auction</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<?
if ($_data->title_type=="Y") {
	echo "<td><img src=\"".$Dir.DataDir."design/auction_title.gif\" border=\"0\" alt=\"경매\"></td>";
} else {
	echo "<td>\n";
	echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
	echo "<TR>\n";
	echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/auction_title_head.gif ALT=></TD>\n";
	echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/auction_title_bg.gif></TD>\n";
	echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/auction_title_tail.gif ALT=></TD>\n";
	echo "</TR>\n";
	echo "</TABLE>\n";
	echo "</td>\n";
}
?>
</tr>
<tr>
	<td style="padding-left:10px;padding-right:10px;">
	<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td align="right">
		<table cellpadding="0" cellspacing="0">
		<tr>
			<td><a href="auction.php"><img src="images/auction_menu1.gif" border="0"></a></td>
			<td><a href="auction.php?type=complete"><img src="images/auction_menu2.gif" border="0"></a></td>
			<td><a href="myauction.php"><img src="images/<?=$tname0?>" border="0"></a></td>
			<td><a href="myauction.php?type=complete"><img src="images/<?=$tname1?>" border="0"></a></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="2" background="images/line2.gif" width="100%"></td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td><img src="images/icon1.gif" border="0" align="absmiddle"><font color="#F02800"><b>&quot;<?=$_ShopInfo->getMemid()?>&quot;</b></font> 회원님이 참여하신 경매내역입니다.</td>
	</tr>
	<tr>
		<td height="2"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<col></col>
		<col width="100"></col>
		<col width="70"></col>
		<col width="70"></col>
		<col width="40"></col>
		<col width="100"></col>
		<col width="40"></col>
		<tr>
			<td height="2" colspan="7" bgcolor="#000000"></td>
		</tr>
		<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
			<td><font color="#333333"><b>상품명</b></font></td>
			<td><font color="#333333"><b>입찰일자</b></font></td>
			<td><font color="#333333"><b>입찰가</b></font></td>
			<td><font color="#333333"><b>현재가</b></font></td>
			<td><font color="#333333"><b>입찰수</b></font></td>
			<td><font color="#333333"><b>마감시간</b></font></td>
			<td><font color="#333333"><b>조회</b></font></td>
		</tr>
		<tr>
			<td height="1" colspan="7" bgcolor="#DDDDDD"></td>
		</tr>
		<?
			$type=$_REQUEST["type"];
			if(strlen($type)>0 && $type!="complete") $type="";

			$curdate=date("YmdHis");
			$sql = "SELECT a.auction_seq,a.start_date,a.end_date,a.auction_name,a.last_price,a.bid_cnt,a.access, ";
			$sql.= "b.price, b.date FROM tblauctioninfo a, tblauctionresult b ";
			$sql.= "WHERE a.auction_seq=b.auction_seq AND a.start_date=b.start_date ";
			if($type=="complete") {
				$sql.= "AND a.end_date < '".$curdate."' ";
			} else {
				$sql.= "AND a.end_date > '".$curdate."' ";
			}
			$sql.= "AND b.id='".$_ShopInfo->getMemid()."' ORDER BY b.date DESC ";
			$result=mysql_query($sql,get_db_conn());
			$i=0;
			while($row=mysql_fetch_object($result)) {
				if ($i % 2 == 0) $trbg = "#fcfcfc";
				else $trbg = "#f4f4f4";

				$rdate=mktime((substr($row->date,8,2)*1),(substr($row->date,10,2)*1),0,(substr($row->date,4,2)*1),(substr($row->date,6,2)*1),(substr($row->date,0,4)*1));
				$edate=mktime((substr($row->end_date,8,2)*1),(substr($row->end_date,10,2)*1),0,(substr($row->end_date,4,2)*1),(substr($row->end_date,6,2)*1),(substr($row->end_date,0,4)*1));

				echo "<tr align=\"center\" height=\"26\">\n";
				echo "	<td align=\"left\">&nbsp;<A HREF=\"auction_detail.php?view=1&seq=".$row->auction_seq."&start_date=".$row->start_date."&type=".$type."\"><font color=\"#333333\">".titleCut(35,$row->auction_name)."</font></A>&nbsp;</td>\n";
				echo "	<td><font color=\"#333333\">".date("Y-m-d H:i",$rdate)."</font></td>\n";
				echo "	<td><font color=\"#333333\">".number_format($row->price)."원</font></td>\n";
				echo "	<td><font color=\"#FF0000\">".number_format($row->last_price)."원</font></td>\n";
				echo "	<td><font color=\"#333333\">".number_format($row->bid_cnt)."</font></td>\n";
				echo "	<td><font color=\"#333333\">".date("Y-m-d H:i",$edate)."</font></td>\n";
				echo "	<td><font color=\"#333333\">".$row->access."</font></td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<td height=\"1\" colspan=\"7\" bgcolor=\"#DDDDDD\"></td>\n";
				echo "</tr>\n";
				$i++;
			}
			mysql_free_result($result);

			if($i==0) {
				echo "<tr><td height=\"30\" colspan=\"7\" align=\"center\"><font color=\"#333333\">입찰하신 내역이 없습니다.</font></td></tr>\n";
				echo "<tr>\n";
				echo "	<td height=\"1\" colspan=\"7\" bgcolor=\"#DDDDDD\"></td></td>\n";
				echo "</tr>\n";
			}
		?>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height="20"></td>
</tr>
</table>

<?=$onload?>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>