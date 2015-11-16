<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$id=$_POST["id"];
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>회원 쿠폰 보유내역</title>
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	//var oHeight = document.all.table_body.clientHeight + 55;
	var oHeight = 400;

	window.resizeTo(oWidth,oHeight);
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="650" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD width="100%" height="31">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="29" height="31" background="images/member_mailallsend_imgbg.gif" style="padding-left:20px"></td>
		<td width="100%" background="images/member_mailallsend_imgbg.gif">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><b><font color="white"><?=$id?>회원님의 회원쿠폰 보유내역</b></font></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD style="padding-top:3pt; padding-bottom:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td width="100%">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TR>
			<TD background="images/table_top_line.gif" width="392" colspan="6" height=1></TD>
		</TR>
		<TR>
			<TD class="table_cell" align="center">쿠폰코드</TD>
			<TD class="table_cell1" align="center">쿠폰명</TD>
			<TD class="table_cell1" align="center">쿠폰적용상품</TD>
			<TD class="table_cell1" align="center">사용가능금액</TD>
			<TD class="table_cell1" align="center">할인/적립</TD>
			<TD class="table_cell1" align="center">유효기간</TD>
		</TR>
		<TR>
			<TD colspan="6" background="images/table_con_line.gif"></TD>
		</TR>
<?
	$sql = "SELECT a.coupon_code, a.coupon_name, a.sale_type, a.sale_money, a.bank_only, a.productcode, ";
	$sql.= "a.mini_price, a.use_con_type1, a.use_con_type2, a.use_point, b.date_start, b.date_end ";
	$sql.= "FROM tblcouponinfo a, tblcouponissue b ";
	$sql.= "WHERE b.id='".$id."' AND a.coupon_code=b.coupon_code ";
	$sql.= "AND (b.date_end>='".date("YmdH")."' OR b.date_end='') AND b.used='N' ";
	$result = mysql_query($sql,get_db_conn());
	$cnt=0;
	while($row=mysql_fetch_object($result)) {
		$cnt++;
		if($row->sale_type<=2) {
			$dan="%";
		} else {
			$dan="원";
		}
		if($row->sale_type%2==0) {
			$sale = "&nbsp;<img src=\"images/icon_off.gif\" width=\"28\" height=\"14\" border=\"0\">";
		} else {
			$sale = "&nbsp;<img src=\"images/icon_point6.gif\" width=\"28\" height=\"14\" border=\"0\">";
		}
		$prleng=strlen($row->productcode);
		if($row->productcode=="ALL") {
			$product="전체상품";
		} else {
			$product = "";
			$sql2 = "SELECT code_name FROM tblproductcode WHERE codeA='".substr($row->productcode,0,3)."' ";
			if(substr($row->productcode,3,3)!="000") {
				$sql2.= "AND (codeB='".substr($row->productcode,3,3)."' OR codeB='000') ";
				if(substr($row->productcode,6,3)!="000") {
					$sql2.= "AND (codeC='".substr($row->productcode,6,3)."' OR codeC='000') ";
					if(substr($row->productcode,9,3)!="000") {
						$sql2.= "AND (codeD='".substr($row->productcode,9,3)."' OR codeD='000') ";
					} else {
						$sql2.= "AND codeD='000' ";
					}
				} else {
					$sql2.= "AND codeC='000' ";
				}
			} else {
				$sql2.= "AND codeB='000' AND codeC='000' ";
			}
			$sql2.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
			$result2=mysql_query($sql2,get_db_conn());
			$i=0;
			while($row2=mysql_fetch_object($result2)) {
				if($i>0) $product.= " > ";
				$product.= $row2->code_name;
				$i++;
			}
			mysql_free_result($result2);

			if($prleng==18) {
				$sql2 = "SELECT productname as product FROM tblproduct WHERE productcode='".$row->productcode."' ";
				$result2 = mysql_query($sql2,get_db_conn());
				if($row2 = mysql_fetch_object($result2)) {
					$product.= " > ".$row2->product;
				}
				mysql_free_result($result2);
			}
			if($row->use_con_type2=="N") $product="[".$product."] 제외";
		}
		
		$date = substr($row->date_start,2,2).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)." ~ <br>".substr($row->date_end,2,2).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2);
		echo "<tr>\n";
		echo "	<TD class=\"td_con2\" align=\"center\">".$row->coupon_code."</td>\n";
		echo "	<TD class=\"td_con1\" align=\"center\">".$row->coupon_name."</td>\n";
		echo "	<TD class=\"td_con1\">".$product."</td>\n";
		echo "	<TD class=\"td_con1\" align=\"center\"><b><span class=\"font_orange\">".($row->mini_price=="0"?"제한 없음":number_format($row->mini_price)."원 이상")."</span></b></TD>\n";
		echo "	<TD class=\"td_con1\" align=\"center\"><font color=\"".($sale=="할인"?"#FF0000":"#0000FF")."\">".number_format($row->sale_money).$dan.$sale."</font></td>\n";
		echo "	<TD class=\"td_con1\">".$date."</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<TD colspan=\"6\" background=\"images/table_con_line.gif\"></TD>\n";
		echo "</tr>\n";
	}
	mysql_free_result($result);
	if($cnt==0) {
		echo "<tr><td class=\"td_con2\" colspan=\"6\" align=\"center\">보유한 쿠폰내역이 없습니다.</td></tr>\n";
	}
?>
		<TR>
			<TD background="images/table_top_line.gif" colspan="6" height=1></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD align="center"><a href="javascript:window.close()"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="10" border=0></a></TD>
</TR>
</form>
</TABLE>
</body>
</html>