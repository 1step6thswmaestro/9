<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if($_data->estimate_ok!="Y" && $_data->estimate_ok!="O") {
	echo "<html></head><body onload=\"alert('견적서 기능 선택이 안되었습니다.');window.close();\"></body></html>";exit;
}

if($mode == "mailsubmit")
{
	$email = "school@arisuedu.co.kr";
	$subject = "견적 요청(".$from_name.")";
	$from_info = "
		<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" width=\"300\" bgcolor=\"#DDDDDD\">
		<col width=\"100\">
		<col width=\"200\">
		<tr>
			<td colspan=\"2\" bgcolor=\"#EFEFEF\">요청자 정보</td>
		</tr>
		<tr>
			<td bgcolor=\"#EFEFEF\">이름</td>
			<td bgcolor=\"#FFFFFF\">".$from_name."</td>
		</tr>
		<tr>
			<td bgcolor=\"#EFEFEF\">메일</td>
			<td bgcolor=\"#FFFFFF\">".$from_email."</td>
		</tr>
		<tr>
			<td bgcolor=\"#EFEFEF\">연락처</td>
			<td bgcolor=\"#FFFFFF\">".$from_tel."</td>
		</tr>
		</table>
	";
	$body = "<table border=0 cellpadding=0 cellspacing=0 width=700 style=\"table-layout:fixed\"><tr><td>".stripslashes($estimate_result)."</td></tr></table>";
	$body = $from_info."<BR><BR>".$body;
	$header = getMailHeader($from_name,$from_email);
	$mail_result = @mail($email,$subject,$body,$header);
	$mail_result = @mail($data->info_email,$subject,$body,$header);

	if($mail_result)
		$onload = "onload=\"alert('견적서 메일을 발송하였습니다.');\"";
}

$printval=$_POST["printval"];

if(strlen($printval)==0) {
	echo "<html></head><body onload=\"alert('선택된 상품이 없습니다.');window.close();\"></body></html>";exit;
}

$prlist="";
$arr_prcnt=array();
$arrval=explode("|",$printval);
for($i=0;$i<count($arrval);$i++) {
	$tmp=explode(",",$arrval[$i]);
	$tmp[1]=(int)$tmp[1];
	if(strlen($tmp[0])==18 && $tmp[1]>0) {
		$arr_prcnt[$tmp[0]]=$tmp[1];
		$prlist.=",".$tmp[0]."";
	}
}
$prlist=substr($prlist,1);
$prlist=ereg_replace(',','\',\'',$prlist);

if(strlen($prlist)==0) {
	echo "<html></head><body onload=\"alert('선택된 상품이 없습니다.');window.close();\"></body></html>";exit;
}

?>

<html>
<head>
<title>온라인견적서 인쇄하기</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?
	$estimate_result = "
	<style>
	td {font-family:돋음;color:787878;font-size:9pt;}
	tr {font-family:돋음;color:787878;font-size:9pt;}
	BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:787878;font-size:9pt;}
	</style>
	";
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
resizeTo(700,620);

function mail() {
	document.getElementById("mail_table").style.display="";
	form = document.mail_form;

	if(form.from_name.value=="") {
		alert("요청자 이름을 기입해주세요.");
		form.from_name.focus();
	} else if(form.from_email.value=="") {
		alert("요청자 메일을 기입해주세요.");
		form.from_email.focus();
	} else if(form.from_tel.value=="") {
		alert("요청자 연락처를 기입해주세요.");
		form.from_tel.focus();
	} else {
		form.mode.value="mailsubmit";
		form.submit();
		document.getElementById("mail_table").style.display="none;";
	}
}

function print_estimate() {
	document.getElementById("mail_table").style.display="none;";
	print();
}
//-->
</SCRIPT>
</head>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 <?=$onload?>>
<?
	$estimate_result .= "
	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">
	<tr>
		<td align=center>
		<table border=0 cellpadding=0 cellspacing=0 width=50%>
		<tr><td height=10></td></tr>
		<tr>
			<td align=center style=\"font-size:18\"><B>견 적 서</B></td>
		</tr>
		<tr><td height=10></td></tr>
		<tr><td height=1 bgcolor=#787878></td></tr>
		</table>
		</td>
	</tr>
	<tr><td height=10></td></tr>
	<tr>
		<td valign=bottom style=\"padding:7\">
		<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">
		<tr>
			<td width=40% valign=bottom>
			<table border=0 cellpadding=3 cellspacing=1 width=100% bgcolor=#787878>
			<col width=60></col>
			<col width=></col>
			<tr bgcolor=#FFFFFF>
				<td align=right style=\"padding-right:5\">견적일자</td>
				<td style=\"padding-left:5\">".date("Y")."년 ".date("m")."월 ".date("d")."일 ".date("H")."시 ".date("i")."분"."</td>
			</tr>
			<tr bgcolor=#FFFFFF>
				<td align=right style=\"padding-right:5\">유효기간</td>
				<td style=\"padding-left:5\">견적 후 일주일</td>
			</tr>
			</table>
			</td>
			<td width=7% nowrap></td>
			<td width=53%>
			<table border=0 cellpadding=3 cellspacing=1 width=100% bgcolor=#787878 style=\"table-layout:fixed\">
			<col width=100></col>
			<col width=></col>
			<tr bgcolor=#FFFFFF>
				<td align=right style=\"padding-right:5\">사업자등록번호</td>
				<td style=\"padding-left:5\">".$_data->companynum."</td>
			</tr>
			<tr bgcolor=#FFFFFF>
				<td align=right style=\"padding-right:5\">회 사 명</td>
				<td style=\"padding-left:5\">".$_data->companyname."</td>
			</tr>
			<tr bgcolor=#FFFFFF>
				<td align=right style=\"padding-right:5\">대표자 성명</td>
				<td style=\"padding-left:5\">".$_data->companyowner."</td>
			</tr>
			<tr bgcolor=#FFFFFF>
				<td align=right style=\"padding-right:5\">업태/종목</td>
				<td style=\"padding-left:5\">".$_data->companybiz." / ".$_data->companyitem."</td>
			</tr>
			<tr bgcolor=#FFFFFF>
				<td align=right style=\"padding-right:5\">사업장 주소</td>
				<td style=\"padding-left:5\">".$_data->companyaddr."</td>
			</tr>
			<tr bgcolor=#FFFFFF>
				<td align=right style=\"padding-right:5\">사업장 전화번호</td>
				<td style=\"padding-left:5\">".$_data->info_tel."</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style=\"padding:7\">
	";

	$sql = "SELECT a.productcode,a.productname,a.sellprice,a.production,a.selfcode ";
	$sql.= "FROM tblproduct AS a ";
	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "WHERE a.productcode IN ('".$prlist."') AND a.display='Y' ";
	$sql.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
	$sql.= "ORDER BY FIELD(a.productcode,'".$prlist."') ";
	$result=mysql_query($sql,get_db_conn());
	$cnt=0;
	$total=0;
	$price=0;
	$vat=0;
	$estlist="";
	while($row=mysql_fetch_object($result)) {
		$total+=$row->sellprice*$arr_prcnt[$row->productcode];
		$price+=round(($row->sellprice*$arr_prcnt[$row->productcode])/1.1);
		$vat+=(($row->sellprice*$arr_prcnt[$row->productcode])-round(($row->sellprice*$arr_prcnt[$row->productcode])/1.1));
		$cnt++;

		$estlist.="<tr bgcolor=#ffffff>\n";
		$estlist.="	<td align=center>".$cnt."</td>\n";
		$estlist.="	<td align=center>".viewselfcode($row->productname,$row->selfcode)."</td>\n";
		$estlist.="	<td align=center>".$row->production."</td>\n";
		$estlist.="	<td align=center>".$arr_prcnt[$row->productcode]."개</td>\n";
		$estlist.="	<td align=right style=\"padding-right:5\">".number_format($row->sellprice)."원</td>\n";
		$estlist.="	<td align=right style=\"padding-right:5\">".number_format(round(($row->sellprice*$arr_prcnt[$row->productcode])/1.1))."원</td>\n";
		$estlist.="	<td align=right style=\"padding-right:5\">".number_format((($row->sellprice*$arr_prcnt[$row->productcode])-round(($row->sellprice*$arr_prcnt[$row->productcode])/1.1)))."원</td>\n";
		$estlist.="</tr>\n";
	}
	mysql_free_result($result);

	$estimate_result .= "
		<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">
		<tr>
			<td width=50%>※ 아래와 같이 견적합니다.</td>
			<td width=50% align=right>견적합계 : \ ".number_format($total)."원, VAT포함</td>
		</tr>
		</table>
		<table border=0 cellpadding=3 cellspacing=1 width=100% bgcolor=#787878 style=\"table-layout:fixed\">
		<col width=30></col>
		<col width=></col>
		<col width=90></col>
		<col width=50></col>
		<col width=80></col>
		<col width=80></col>
		<col width=70></col>
		<tr bgcolor=#f4f4f4 height=25>
			<td align=center>No</td>
			<td align=center>상품명</td>
			<td align=center>제조사</td>
			<td align=center>수량</td>
			<td align=center>상품단가</td>
			<td align=center>상품금액</td>
			<td align=center>세액</td>
		</tr>
	";

	$estimate_result .= $estlist;
	if($cnt<15) {
		for($i=$cnt;$i<=15;$i++) {
			$estimate_result .= "<tr bgcolor=#FFFFFF>\n";
			$estimate_result .= "	<td>&nbsp;</td>\n";
			$estimate_result .= "	<td></td>\n";
			$estimate_result .= "	<td></td>\n";
			$estimate_result .= "	<td></td>\n";
			$estimate_result .= "	<td></td>\n";
			$estimate_result .= "	<td></td>\n";
			$estimate_result .= "	<td></td>\n";
			$estimate_result .= "</tr>\n";
		}
	}

	$estimate_result .= "
		</table>
		<table border=0 cellpadding=0 cellspacing=0>
		<tr><td height=2></td></tr>
		</table>
		<table border=0 cellpadding=3 cellspacing=1 width=100% bgcolor=#787878 style=\"table-layout:fixed\">
		<col width=30></col>
		<col width=></col>
		<col width=90></col>
		<col width=50></col>
		<col width=80></col>
		<col width=80></col>
		<col width=70></col>
		<tr bgcolor=#ffffff>
			<td colspan=5 align=right style=\"padding-right:5\">상품금액</td>
			<td colspan=2 align=right style=\"padding-right:5\">".number_format($price)."원</td>
		</tr>
		<tr bgcolor=#ffffff>
			<td colspan=5 align=right style=\"padding-right:5\">부가세(10%)</td>
			<td colspan=2 align=right style=\"padding-right:5\">".number_format($vat)."원</td>
		</tr>
		<tr bgcolor=#ffffff>
			<td colspan=5 align=right style=\"padding-right:5\">합 계</td>
			<td colspan=2 align=right style=\"padding-right:5\">".number_format($total)."원</td>
		</tr>
		<tr bgcolor=#f4f4f4>
			<td colspan=7 style=\"padding-left:5\">비 고</td>
		</tr>
		<tr bgcolor=#ffffff>
			<td colspan=7 style=\"padding:10\">
			".$_data->estimate_msg."&nbsp;
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	";

	echo $estimate_result;
?>

<form name="mail_form" method="post" action="<?=$PHP_SELF?>" onSubmit="return false;">
<input type="hidden" name="mode">
<input type="hidden" name="estimate_result" value="<?=htmlspecialchars($estimate_result)?>">
<input type="hidden" name="printval" value="<?=$printval?>">

<table id="mail_table" border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed" style="display:none;">
<tr>
	<td align="right" style="padding:7">
	<table border="0" cellpadding="3" cellspacing="1" width="300" bgcolor="#DDDDDD">
	<col width="100">
	<col width="200">
	<tr>
		<td colspan="2" bgcolor="#EFEFEF">요청자 정보</td>
	</tr>
	<tr>
		<td bgcolor="#EFEFEF">이름</td>
		<td bgcolor="#FFFFFF"><input type="text" name="from_name" style="width:200px;height:16px;font-size:11px;background-color:#FFFFFF;padding-top:2pt;padding-bottom:1pt;border:#D4D4D4 1px solid;"></td>
	</tr>
	<tr>
		<td bgcolor="#EFEFEF">메일</td>
		<td bgcolor="#FFFFFF"><input type="text" name="from_email" style="width:200px;height:16px;font-size:11px;background-color:#FFFFFF;padding-top:2pt;padding-bottom:1pt;border:#D4D4D4 1px solid;"></td>
	</tr>
	<tr>
		<td bgcolor="#EFEFEF">연락처</td>
		<td bgcolor="#FFFFFF"><input type="text" name="from_tel" style="width:200px;height:16px;font-size:11px;background-color:#FFFFFF;padding-top:2pt;padding-bottom:1pt;border:#D4D4D4 1px solid;"></td>
	</tr>
	</table>
	</td>
</tr>
</table>
</form>

<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
<tr>
	<td align=center style="padding-top:10">
	<A HREF="javascript:mail();"><img src="<?=$Dir?>images/common/estimate/icon_mail.gif" border=0></A>
	&nbsp;
	<A HREF="javascript:print_estimate();"><img src="<?=$Dir?>images/common/estimate/icon_print.gif" border=0></A>
	&nbsp;
	<A HREF="javascript:window.close();"><img src="<?=$Dir?>images/common/estimate/icon_close.gif" border=0></A>
	</td>
</tr>
<tr><td height=20></td></tr>
</table>

<script>//print();</script>
</body>
</html>