<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cfg.php");

$b_idx=$_POST["b_idx"];

$sql = "SELECT * FROM tblorderbill WHERE b_idx=".$b_idx." ";
$result = mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$ordercode = $row->ordercode;
	$memid = $row->memid;
	$r_number = $row->companynum;
	$r_name = $row->companyname;
	$r_tnumber = $row->companytnum;
	$r_master = $row->companyowner;
	$r_address = $row->companyaddr;
	$r_condition = $row->companybiz;
	$r_item = $row->companyitem;
}else{
	echo "<html><head><title></title></head><body onload=\"alert('세금계산서 정보가 존재하지않습니다..');window.close();\"></body></html>";exit;
	exit;
}

$SBinfo = new Shop_Billinfo();
$SBinfo->baseinfo($memid);
$SBinfo->set_taxrate(TAXRATE);
$SBinfo->order_info($ordercode);

$year = substr($ordercode, 0 ,4);
$month = substr($ordercode, 4 ,2);
$day = substr($ordercode, 6 ,2);

?>
<html>
<head>
<title>전자세금계산서 신청정보</title>
<STYLE TYPE=text/css>
.c01 { 
	font-family: 굴림; 
	font-size: 9pt; 
	color: blue;
	font-weight: normal;
	background: white;
}
.c02 {
	font-family: 굴림; 
	font-size: 9pt; 
	color: red;
	font-weight: normal;
	background: white;
}
tr,td {
	color: black;
	font-weight: normal;
	font-family: 굴림; 
	font-size: 9pt; 
}
.nip {
	background=#FFFFFF;
	font-size:9pt;
	font-weight: bold;
	border:0x;
}
</STYLE>
<body bgcolor=#FFFFFF topmargin=10 leftmargin=10 marginwidth=0 marginheight=0 oncontextmenu="return false" 1oncontextmenu="printok();return false;">
<center>
<table border="0">
<tr>
<td height="20"></td>
</tr>
<tr>
<td>
	<table width=620 border=1 cellspacing=0 cellpadding=0 bordercolor=red style="table-layout:fixed">
	<tr>
		<td>
		<table width=100% border=1 cellspacing=0 cellpadding=0 bordercolor=red style="table-layout:fixed">
		<tr>
			<td colspan=2>
			<table width=100% height=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void style="table-layout:fixed">
			<tr>
				<td rowspan=2 width=70% height=40>
				<table border=0 cellspacing=0 cellpadding=0>
				<tr align=center>
					<td width=160></td>
					<td rowspan=2><font color=red size=3><b>세 금 계 산 서</b></font>&nbsp;&nbsp;</td>
					<td class=c02 rowspan=2><font size=5>(</font></td>
					<td class=c02>공 급 자</td>
					<td class=c02 rowspan=2><font size=5>)</font></td>
				</tr>
				<tr align=center>
					<td align=left class=c02>&nbsp;&nbsp;&nbsp;<font size=1 style="font-weight:normal">(별지 제 11호 서식)</font></td>
					<td class=c02>보 관 용</td>
				</tr>
				</table>
				</td>
				<td align=center width=10% class=c02> 책 번 호 </td>
				<td width=10% colspan=3 align=right class=c02>권</td>
				<td width=10% colspan=3 align=right class=c02>호</td>
			</tr>
			<tr>
				<td align=center class=c02> 일련번호 </td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td width=310 height=100%>
			<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void height=100% style="table-layout:fixed">
			<col width=18></col>
			<col width=54></col>
			<col width=></col>
			<col width=18></col>
			<col width=></col>
			<tr height=27 valign=middle>
				<td rowspan=4 class=c02>공 급 자</td>
				<td align=center class=c02 style="padding-top:3px"><nobr>등록번호</td>
				<td colspan=3 style="padding-left:5px;padding-top:3px"><B><font size=3><?=$SBinfo->s_number?></font></B></td>
			</tr>
			<tr height=35 valign=middle>
				<td align=center class=c02 style="padding-top:3px">상<img width=20 height=0>호<br>(법인명)</td>
				<td style="padding-left:5px;padding-top:3px"><B><?=$SBinfo->s_name?></B></td>
				<td align=center class=c02 style="padding-top:3px">성명</td>
				<td style="padding-left:5px"><B><?=$SBinfo->s_master?></B><img src=images/taxprint_sign.gif align=absmiddle hspace=2></td>
			</tr>
			<tr height=35 valign=middle>
				<td align=center class=c02 style="padding-top:3px">사 업 장<br>주<img width=20 height=0>소</td>
				<td colspan=3 style="padding-left:5px"><B><?=$SBinfo->s_address?></B></td>
			</tr>
			<tr height=35 valign=middle>
				<td align=center class=c02 style="padding-top:3px">업<img width=20 height=0>태</td>
				<td style="padding-left:5px;padding-top:3px"><B><?=$SBinfo->s_condition?></B></td>
				<td class=c02 style="padding-top:3px">종목</td>
				<td style="padding-left:5px;padding-top:3px"><B><?=$SBinfo->s_item?></B></td>
			</tr>
			</table>
			</td>
			<td width=310 height=100%>
			<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void height=100% style="table-layout:fixed">
			<col width=18></col>
			<col width=54></col>
			<col width=></col>
			<col width=18></col>
			<col width=></col>
			<form name=company2>
			<tr height=27 valign=middle>
				<td rowspan=4 class=c02>공 급 받 는 자</td>
				<td align=center class=c02 style="padding-top:3px"><nobr>등록번호</td>
				<td colspan=3 style="padding-left:5px;padding-top:3px"><B><font size=3><?=$r_number?></font></B></td>
			</tr>
			<tr height=35 valign=middle>
				<td align=center class=c02 style="padding-top:3px">상<img width=20 height=0>호<br>(법인명)</td>
				<td style="padding-left:5px;padding-top:3px"><B><?=$r_name?></B></td>
				<td align=center class=c02 style="padding-top:3px">성명</td>
				<td style="padding-left:5px"><B><?=$r_master?></B></td>
			</tr>
			<tr height=35 valign=middle>
				<td align=center class=c02 style="padding-top:3px">사 업 장<br>주<img width=20 height=0>소</td>
				<td colspan=3 style="padding-left:5px"><B><?=$r_address?></B></td>
			</tr>
			<tr height=35 valign=middle>
				<td align=center class=c02 style="padding-top:3px">업<img width=20 height=0>태</td>
				<td style="padding-left:5px;padding-top:3px"><B><?=$r_condition?></B></td>
				<td class=c02 style="padding-top:3px">종목</td>
				<td style="padding-left:5px;padding-top:3px"><B><?=$r_item?></B></td>
			</tr>
			</form>
			</table>
			</td>
		</tr>
		<tr>
			<td colspan=2>
			<table width=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void style="table-layout:fixed">
			<col width=36></col>
			<col width=18></col>
			<col width=18></col>
			<col width=234></col>
			<col width=199></col>
			<col width=></col>
			<tr align=center>
				<td colspan=3 class=c02>작성일</td>
				<td class=c02>공급가액</td>
				<td class=c02>세 액</td>
				<td class=c02>비 고</td>
			</tr>
			<tr align=center>
				<td class=c02>년</td>
				<td class=c02>월</td>
				<td class=c02>일</td>
				<td rowspan=2 style="padding:0px;" height=100%>
				<table width=100% height=100% border=1 cellspacing=0 cellpadding=0 bordercolor=red frame=void style="table-layout:fixed">
				<col width=27></col>
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<tr align=center>
					<td class=c02>공란</td>
					<td class=c02>백</td>
					<td class=c02>십</td>
					<td class=c02>억</td>	
					<td class=c02>천</td>	
					<td class=c02>백</td>	
					<td class=c02>십</td>	
					<td class=c02>만</td>	
					<td class=c02>천</td>	
					<td class=c02>백</td>	
					<td class=c02>십</td>	
					<td class=c02>일</td>	
				</tr>
	<? 
				$totalsale=$SBinfo->supplyprice;
				$totaltax=$SBinfo->tax;
				$totalsumprice=$SBinfo->supplyprice+$SBinfo->tax;
				$length=strlen($totalsale);
				$length2=strlen($totaltax);
	?>

				<tr align=center height=24>
					<td><?=11-$length?></td>
					<td><?=($length>=11?substr($totalsale,-11,1):"")?></td>
					<td><?=($length>=10?substr($totalsale,-10,1):"")?></td>
					<td><?=($length>=9?substr($totalsale,-9,1):"")?></td>
					<td><?=($length>=8?substr($totalsale,-8,1):"")?></td>
					<td><?=($length>=7?substr($totalsale,-7,1):"")?></td>
					<td><?=($length>=6?substr($totalsale,-6,1):"")?></td>
					<td><?=($length>=5?substr($totalsale,-5,1):"")?></td>
					<td><?=($length>=4?substr($totalsale,-4,1):"")?></td>
					<td><?=($length>=3?substr($totalsale,-3,1):"")?></td>
					<td><?=($length>=2?substr($totalsale,-2,1):"")?></td>
					<td><?=($length>=1?substr($totalsale,-1,1):"")?></td>
				</tr>
				</table>
				</td>
				<td rowspan=2 style="padding:0px;" height=100%>
				<table width=100% height=100% border=1 cellspacing=0 cellpadding=0 bordercolor=red frame=void style="table-layout:fixed">
				<col width=10%></col>
				<col width=10%></col>
				<col width=10%></col>
				<col width=10%></col>
				<col width=10%></col>
				<col width=10%></col>
				<col width=10%></col>
				<col width=10%></col>
				<col width=10%></col>
				<col width=10%></col>
				<tr align=center>
					<td class=c02>십</td>
					<td class=c02>억</td>	
					<td class=c02>천</td>	
					<td class=c02>백</td>	
					<td class=c02>십</td>	
					<td class=c02>만</td>	
					<td class=c02>천</td>	
					<td class=c02>백</td>	
					<td class=c02>십</td>	
					<td class=c02>일</td>	
				</tr>
				<tr align=center height=24>
					<td><?=($length2>=10?substr($totaltax,-10,1):"")?></td>
					<td><?=($length2>=9?substr($totaltax,-9,1):"")?></td>
					<td><?=($length2>=8?substr($totaltax,-8,1):"")?></td>
					<td><?=($length2>=7?substr($totaltax,-7,1):"")?></td>
					<td><?=($length2>=6?substr($totaltax,-6,1):"")?></td>
					<td><?=($length2>=5?substr($totaltax,-5,1):"")?></td>
					<td><?=($length2>=4?substr($totaltax,-4,1):"")?></td>
					<td><?=($length2>=3?substr($totaltax,-3,1):"")?></td>
					<td><?=($length2>=2?substr($totaltax,-2,1):"")?></td>
					<td><?=($length2>=1?substr($totaltax,-1,1):"")?></td>
				</tr>
				</table>
				</td>
				<td rowspan=2></td>
			</tr>
			<tr align=center height=24>
				<td><?=$year?></td>
				<td><?=$month?></td>
				<td><?=$day?></td>	
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td colspan=2>

			<table width=100% height=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void style="table-layout:fixed">
			<col width=18></col>
			<col width=18></col>
			<col width=></col>
			<col width=40></col>
			<col width=40></col>
			<col width=61></col>
			<col width=62></col>
			<col width=45></col>
			<tr align=center>
				<td class=c02>월</td>
				<td class=c02>일</td>
				<td class=c02>품목 / 규격</td>
				<td class=c02>단위</td>
				<td class=c02>수량</td>
				<td class=c02>단가</td>
				<td class=c02>공급가액</td>
				<td class=c02>세액</td>	
			</tr>
			<? for($i =0; $i<sizeof($SBinfo->productname);$i++){?>
			<tr align=center>
				<td style="font-size:8pt"><?=$month?></td>
				<td style="font-size:8pt"><?=$day;?></td>
				<td style="font-size:8pt"><?=$SBinfo->productname[$i]?></td>
				<td></td>
				<td style="font-size:8pt"><?=number_format($SBinfo->quantity[$i])?></td>
			<? if($addtax!="Y") {
				  $taxsum=round($productprice[$cnt]/(1+$taxrate/100));
				  $taxsumquantity=round($productprice[$cnt]*$quantity[$cnt]/(1+$taxrate/100));
				  $taxsumsale=$productprice[$cnt]*$quantity[$cnt]-$taxsumquantity;
			   } else {
				  $taxsum=$productprice[$cnt];
				  $taxsumquantity=$productprice[$cnt]*$quantity[$cnt];
				  $taxsumsale=$productprice[$cnt]*$quantity[$cnt]*($taxrate/100);
			   }
			?>
				<td align=right style="font-size:8pt"><?=number_format($SBinfo->taxsum[$i])?></td>
				<td align=right style="font-size:8pt"><?=number_format($SBinfo->taxsumquantity[$i])?></td>
				<td align=right style="font-size:8pt"><?=number_format($SBinfo->taxsumsale[$i]);?></td>
			</tr>
			<?}?>
			<tr><td colspan=8 align=center class=c02> ***** 이 하 여 백 ***** </td></tr>
			<tr align=center>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align=right>&nbsp;</td>
				<td align=right>&nbsp;</td>
				<td align=right>&nbsp;</td>
			</tr>
			</table>

			</td>
		</tr>
		<tr>
			<td colspan=2>

			<table width=100% height=100% border=1 cellspacing=0 cellpadding=2 bordercolor=red frame=void style="table-layout:fixed">
			<col width=></col>
			<col width=100></col>
			<col width=100></col>
			<col width=100></col>
			<col width=101></col>
			<col width=107></col>
			<tr align=center>
				<td class=c02>합계금액</td>
				<td class=c02>현금</td>
				<td class=c02>수표</td>
				<td class=c02>어음</td>
				<td class=c02>외상미수금</td>
				<td rowspan=2 class=c02>이 금액을 영수함</td>
			</tr>
			<tr align=center>
				<td align=right><B><?=number_format($totalsumprice)?></B>&nbsp;</td>	
				<td align=right></td>	
				<td align=right></td>	
				<td align=right></td>	
				<td align=right></td>	
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
</table>
</body>
</html>