<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$mode=$_POST["mode"];

$type=$_REQUEST["type"];
if(strlen($type)==0 || $type!="complete") $type="";
$gong_col="8";
if($type=="complete") $gong_col="7";

if($mode=="cancel") {
	$seq=$_POST["seq"];

	$sql = "SELECT end_date FROM tblgonginfo WHERE gong_seq='".$seq."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		if($row->end_date<date("YmdHis")) {
			$onload="<script>alert(\"공동구매가 마감된 신청내역은 삭제할 수 없습니다.\");</script>";
		} else {
			$sql = "DELETE FROM tblgongresult WHERE gong_seq='".$seq."' AND id='".$_ShopInfo->getMemid()."' ";
			$delete=mysql_query($sql,get_db_conn());
			if($delete) {
				$sql = "SELECT SUM(buy_cnt) as bid_cnt FROM tblgongresult WHERE gong_seq='".$seq."' ";
				$result2=mysql_query($sql,get_db_conn());
				$row2=mysql_fetch_object($result2);
				$bid_cnt=(int)$row2->bid_cnt;
				mysql_free_result($result2);

				$sql = "UPDATE tblgonginfo SET ";
				$sql.= "bid_cnt		= ".$bid_cnt." ";
				$sql.= "WHERE gong_seq='".$seq."' ";
				mysql_query($sql,get_db_conn());
			}
			$onload="<script>alert(\"참여하신 공구내역을 취소하였습니다.\");</script>";
		}
	}
	mysql_free_result($result);
}

$tname0="gong_menu3.gif";
$tname1="gong_menu4.gif";
if($type=="complete") {
	$tname1="gong_menu4r.gif";
} else {
	$tname0="gong_menu3r.gif";
}

?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 나의 공동구매</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GongCancel(seq) {
	if(confirm("해당 공동구매 참여내역을 취소하시겠습니까?")) {
		document.form1.seq.value=seq;
		document.form1.mode.value="cancel";
		document.form1.submit();
	}
}
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<?
if ($_data->title_type=="Y") {
	echo "<td><img src=\"".$Dir.DataDir."design/gonggu_title.gif\" border=\"0\" alt=\"공동구매\"></td>\n";
} else {
	echo "<td>\n";
	echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
	echo "<TR>\n";
	echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/gonggu_title_head.gif ALT=></TD>\n";
	echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/gonggu_title_bg.gif></TD>\n";
	echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/gonggu_title_tail.gif ALT=></TD>\n";
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
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<tr>
			<td align="right">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td><a href="gonggu.php"><img src="images/gong_menu1.gif" border="0"></a></td>
				<td><a href="gonggu.php?type=complete"><img src="images/gong_menu2.gif" border="0"></a></td>
				<td><a href="mygonggu.php"><img src="images/<?=$tname0?>" border="0"></a></td>
				<td><a href="mygonggu.php?type=complete"><img src="images/<?=$tname1?>" border="0"></a></td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td width="100%" height="2" background="images/line2.gif"></td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td><font color="#F02800"><b>&quot;<?=$_ShopInfo->getMemid()?>&quot;</font> 회원님이 참여하신 공동구매 내역입니다.</b></td>
		</tr>
		<tr>
			<td>
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<col></col>
				<col width="100"></col>
				<col width="65"></col>
				<col width="65"></col>
				<col width="40"></col>
				<col width="100"></col>
				<col width="35"></col>
				<?if($type!="complete"){?>
				<col width="35"></col>
				<?}?>
				<tr>
					<td height="2" colspan="<?=$gong_col?>" bgcolor="#000000"></td>
				</tr>
				<tr height="30" align="center" bgcolor="#F8F8F8" style="letter-spacing:-0.5pt;">
					<td><font color="#333333"><b>상품명</b></font></td>
					<td><font color="#333333"><b>참여일자</b></font></td>
					<td><font color="#333333"><b>시작가</b></font></td>
					<td><font color="#333333"><b>현재가</b></font></td>
					<td><font color="#333333"><b>참여수</b></font></td>
					<td><font color="#333333"><b>마감시간</b></font></td>
					<td><font color="#333333"><b>수량</b></font></td>
					<?if($type!="complete"){?>
					<td><font color="#333333"><b>취소</b></font></td>
					<?}?>
				</tr>
				<tr>
					<td height="1" bgcolor="#DDDDDD" colspan="<?=$gong_col?>"></td>
				</tr>
<?
	$type=$_REQUEST["type"];
	if(strlen($type)>0 && $type!="complete") $type="";

	$curdate=date("YmdHis");
	$sql = "SELECT a.gong_seq,a.start_date,a.end_date,a.gong_name,a.start_price,a.down_price,a.mini_price, ";
	$sql.= "a.count,a.bid_cnt,b.process_gbn,b.buy_cnt,b.date FROM tblgonginfo a, tblgongresult b ";
	$sql.= "WHERE a.gong_seq=b.gong_seq ";
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

		$num=intval($row->bid_cnt/$row->count);
		$price=$row->start_price-($num*$row->down_price);
		if($price<$row->mini_price) $price=$row->mini_price;
		$rdate=mktime((substr($row->date,8,2)*1),(substr($row->date,10,2)*1),0,(substr($row->date,4,2)*1),(substr($row->date,6,2)*1),(substr($row->date,0,4)*1));
		$edate=mktime((substr($row->end_date,8,2)*1),(substr($row->end_date,10,2)*1),0,(substr($row->end_date,4,2)*1),(substr($row->end_date,6,2)*1),(substr($row->end_date,0,4)*1));

		echo "<tr height=\"26\" align=\"center\">\n";
		echo "	<td align=\"left\" style=\"padding-left:3px;\"><A HREF=\"gonggu_detail.php?view=1&seq=".$row->gong_seq."&type=".$type."\"><font color=\"#333333\">".titleCut(35,$row->gong_name)."</font></A></td>\n";
		echo "	<td><font color=\"#333333\">".date("Y-m-d H:i",$rdate)."</font></td>\n";
		echo "	<td><font color=\"#333333\">".number_format($row->start_price)."원</font></td>\n";
		echo "	<td><font color=\"#F02800\"><b>".number_format($price)."원</b></font></td>\n";
		echo "	<td><font color=\"#333333\">".number_format($row->bid_cnt)."개</font></td>\n";

		if ($type=="complete" && $row->process_gbn=="I")
			echo "	<td><font color=\"#FF0000\">마감</font></td>\n";
		else if ($row->process_gbn=="B")
			echo "	<td><font color=\"#FF0000\">입금확인</font></td>\n";
		else if ($row->process_gbn=="E")
			echo "	<td><font color=\"#FF0000\">배송</font></td>\n";
		else 
			echo "	<td><font color=\"#333333\">".date("Y-m-d H:i",$edate)."</font></td>\n";

		echo "	<td><font color=\"#333333\">".$row->buy_cnt."</font></td>\n";
		if($type!="complete") {
			echo "	<td><A HREF=\"javascript:GongCancel('".$row->gong_seq."')\"><img src=\"images/cancel.gif\" border=\"0\"></A></td>\n";
		}
		echo "</tr>\n";
		echo "<tr>\n";
		echo "	<td height=\"1\" bgcolor=\"#DDDDDD\" colspan=\"".$gong_col."\"></td></td>\n";
		echo "</tr>\n";
		
		$i++;
	}
	mysql_free_result($result);

	if($i==0) {
		echo "<tr><td height=\"30\" colspan=\"".$gong_col."\" align=\"center\"><font color=\"#333333\">참여하신 내역이 없습니다.</font></td></tr>\n";
		echo "<tr>\n";
		echo "	<td height=\"1\" bgcolor=\"#DDDDDD\" colspan=\"".$gong_col."\"></td></td>\n";
		echo "</tr>\n";
	}
?>
				</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
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
<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode value="">
<input type=hidden name=seq value="">
<input type=hidden name=type value="<?=$type?>">
</form>
<tr>
	<td height="20"></td>
</tr>
</table>
<?=$onload?>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>