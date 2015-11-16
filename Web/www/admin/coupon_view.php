<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/class/coupon.php");
$coupon = new coupon();
if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$coupon_code=$_POST["coupon_code"];
$sql = "SELECT * FROM tblcouponinfo WHERE coupon_code = '".$coupon_code."' ";
$result = mysql_query($sql,get_db_conn());
if(!$row=mysql_fetch_object($result)) {
	echo "<script>alert('해당 쿠폰 정보가 존재하지 않습니다.');window.close();</script>";
	exit;
}
mysql_free_result($result);

if ( $row->use_point == "A" ) {
	$use_point = "쿠폰만 적용";
} else {
	$use_point = "동시적용";
}

if ( $row->etcapply_gift == "A" ) {
	$etcapply_gift = "본 쿠폰을 사용할 경우 사은품을 지급하지 않습니다.";
} else {
	$etcapply_gift = "본 쿠폰과 사은품을 동시 지급합니다.";
}

if ( $row->order_limit == "Y" ) {
	$order_limit = "중복 사용불가";
} else {
	$order_limit = "제한없이 중복사용가능";
}

if($row->date_start>0) {
	$date = substr($row->date_start,0,4).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)."[".substr($row->date_start,8,2).":00] ~ ".substr($row->date_end,0,4).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2)."[".substr($row->date_end,8,2).":00]";
	$date2 = substr($row->date_start,4,2)."/".substr($row->date_start,6,2)." ~ ".substr($row->date_end,4,2)."/".substr($row->date_end,6,2);
} else {
	$date = abs($row->date_start)."일동안";
	$date2 = date("m/d")." ~ ".date("m/d",mktime(0,0,0,date("m"),date("d")+abs($row->date_start),date("Y")));
}
if($row->sale_type<=2) {
	$dan="%";
} else {
	$dan="원";
}
if($row->sale_type%2==0) {
	$sale = "할인";
} else {
	$sale = "적립";
}
$prleng=strlen($row->productcode);
if($row->productcode=="ALL") {
	$product="전체상품";
} else {
	$arrproduct=explode(",",$row->productcode);
	for($a=0;$a<count($arrproduct);$a++) {
		if($a>0) $product.=", ";

		$prleng=strlen($arrproduct[$a]);
		if($prleng==12) {
			$sql2 = "SELECT code_name as product FROM tblproductcode WHERE codeA='".substr($arrproduct[$a],0,3)."' ";
			if(substr($arrproduct[$a],3,3)!="000") {
				$sql2.= "AND (codeB='".substr($arrproduct[$a],3,3)."' OR codeB='000') ";
				if(substr($arrproduct[$a],6,3)!="000") {
					$sql2.= "AND (codeC='".substr($arrproduct[$a],6,3)."' OR codeC='000') ";
					if(substr($arrproduct[$a],9,3)!="000") {
						$sql2.= "AND (codeD='".substr($arrproduct[$a],9,3)."' OR codeD='000') ";
					} else {
						$sql2.= "AND codeD='000' ";
					}
				} else {
					$sql2.= "AND codeC='000' ";
				}
			} else {
				$sql2.= "AND codeB='000' AND codeC='000' ";
			}
			$sql2.= "AND (type='L' or type='LX' or type='LM' or type='LMX') ";
			$sql2.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
			$result2 = mysql_query($sql2,get_db_conn());
			$i=0;
			while($row2=mysql_fetch_object($result2)) {
				if($i>0) $product.= " > ";
				$product.= $row2->product;
				$i++;
			}
			mysql_free_result($result2);
		}
		if($prleng==18) {
			$sql2 = "SELECT productname as product FROM tblproduct WHERE productcode='".$arrproduct[$a]."' ";
			$result2 = mysql_query($sql2,get_db_conn());
			if($row2 = mysql_fetch_object($result2)) {
				$product.= " > ".$row2->product;
			}
			mysql_free_result($result2);
		}
	}
	if($row->use_con_type2=="N") $product="[".$product."] 제외";
}
if($row->member=="ALL") {
	$membermsg = "[전체회원]";
} else if($row->member!="") {
	$sql2 = "SELECT group_name FROM tblmembergroup WHERE group_code='".$row->member."' ";
	$result2 = mysql_query($sql2,get_db_conn());
	if($row2 = mysql_fetch_object($result2)) $membermsg = "[회원등급 : ".$row2->group_name."]";
	else $membermsg = "[개별회원]";
	mysql_free_result($result2);
} else {
	$membermsg = "[개별회원]";
}
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>쿠폰정보 보기</title>
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
//document.onkeydown = CheckKeyPress;
//document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 75;

	window.resizeTo(oWidth,oHeight);
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<TABLE WIDTH="550" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><img src="images/coupon_view_title.gif" border="0" width="212" height="31"></td>
		<td width="100%" background="images/member_find_titlebg.gif">&nbsp;</td>
		<td align="right"><img src="images/member_find_titleimg.gif" width="20" height="31" border="0"></td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD background="images/member_zipsearch_bg.gif">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="18">&nbsp;</td>
		<td>&nbsp;</td>
		<td width="18">&nbsp;</td>
	</tr>
	<tr>
		<td width="18">&nbsp;</td>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%"><IMG height=9 src="images/icon_9.gif" width=13 border=0><b>쿠폰 기본정보</b></td>
		</tr>
		<tr>
			<td width="100%">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<col width=150></col>
			<col width=></col>
			<TR>
				<TD background="images/table_top_line.gif" colspan=2></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰코드<br></TD>
				<TD class="td_con1"><SPAN class=font_orange><B><?=$row->coupon_code?></B></SPAN></TD>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰이름</TD>
				<TD class="td_con1"><b><font color="black"><?=$row->coupon_name?><br></font></b></TD>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">유효기간</TD>
				<TD class="td_con1"><?=$date?><br></TD>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">할인/적립 속성</TD>
				<TD class="td_con1"><?=number_format($row->sale_money).$dan.$sale?> 쿠폰<br></TD>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 사용가능결제금액</TD>
				<TD class="td_con1"><?=$row->mini_price=="0"?"제한 없음":number_format($row->mini_price)."원 이상 구매시에만 사용가능"?><br></TD>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>
			<tr>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">결제방법 제한</TD>
				<TD class="td_con1"><?=$row->bank_only=="Y"?"현금 입금만 가능 (실시간 계좌이체 포함)":"제한 없음"?></TD>
			</tr>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>
			<tr>
				<TD class="table_cell">
					<img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 사용조건</TD>
				<TD class="td_con1"><?=$row->use_con_type1=="Y"?"다른 상품과 구매시에도 사용가능":"해당 상품 구매시에만 사용가능"?></TD>
			</tr>
			<TR>
				<TD background="images/table_top_line.gif" colspan=2></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		<tr>
			<td width="100%" height="25">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%"><IMG height=9 src="images/icon_9.gif" width=13 border=0><b><font color="black">쿠폰 부가정보</font></b></td>
		</tr>
		<tr>
			<td width="100%">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<col width=150></col>
			<col width=></col>
			<TR>
				<TD background="images/table_top_line.gif" colspan=2></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">적용 상품군</TD>
				<TD class="td_con1"><?=$product?><br></TD>
			</TR>


			<TR>
				<TD background="images/table_top_line.gif" colspan=2></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">회원등급별 혜택<br />(할인/적립)과 동시<br /> 적용 여부</TD>
				<TD class="td_con1"><?=$use_point?><br></TD>
			</TR>


			<TR>
				<TD background="images/table_top_line.gif" colspan=2></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">사은품 과 <br />동시적용여부</TD>
				<TD class="td_con1"><?=$etcapply_gift?><br></TD>
			</TR>


			<TR>
				<TD background="images/table_top_line.gif" colspan=2></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">하나의 주문에<br /> 중복사용가능 여부</TD>
				<TD class="td_con1"><?=$order_limit?><br></TD>
			</TR>


			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 발급조건</TD>
				<TD class="td_con1"><?=$coupon->issueTypes[$row->issue_type]." ".$membermsg?><br></TD>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>



			<?
				// 회원직접 다운로드
				if( $row->issue_type == "Y" ) {
			?>


			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">발행<?=(!in_array($row->issue_type,array('O','P')))?'가능':''?> 쿠폰 수</TD>
				<TD class="td_con1"><?=(!in_array($row->issue_type,array('O','P')))?($row->issue_tot_no=="0"?"무제한":number_format($row->issue_tot_no)."명"):number_format($row->issue_tot_no)?></TD>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>


			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 자동노출 여부</TD>
				<TD class="td_con1"><?=$row->detail_auto=="Y"?"노출함":"노출안함"?></TD>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>



			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">재 다운로드</TD>
				<TD class="td_con1"><?=$row->repeat_id=="Y"?"가능":"불가능"?></TD>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>
			<?
				}
			?>





			<?if($row->auto=="Y"){?>
			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품상세 자동노출</TD>
				<TD class="td_con1"><?=$row->detail_auto=="Y"?"Yes":"No"?></TD>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>
			<?}?>

			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">등급혜택과 동시적용</TD>
				<TD class="td_con1"><?=$row->use_point=="Y"?"Yes":"No"?></TD>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>
			<? if(!in_array($row->issue_type,array('O','P'))){ ?>
			<TR>
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">쿠폰 이미지</TD>
				<TD class="td_con1">
				<table border="0" cellpadding="0" cellspacing="0" width="352" style="table-layout:fixed;">
				<col width="5"></col>
				<col width=></col>
				<col width="5"></col>
				<tr>
					<td colspan="3"><IMG SRC="<?=$Dir?>images/common/coupon_table01.gif" border="0"></td>
				</tr>
				<tr>
					<td background="<?=$Dir?>images/common/coupon_table02.gif"></td>
					<td width="100%" style="padding:3pt;" background="<?=$Dir?>images/common/coupon_bg.gif">
					<table align="center" cellpadding="0" cellspacing="0">
					<tr>
						<td style="padding-bottom:4pt;"><IMG SRC="<?=$Dir?>images/common/coupon_title<?=$row->sale_type?>.gif" border="0"></td>
					</tr>
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><font color="#585858" style="font-size:11px;letter-spacing:-0.5pt;">유효기간 : <?=$date2?></font><?=($row->bank_only=="Y"?"<font color=\"#0000FF\">(현금결제만 가능)</font>":"")?></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="100%" align="right"><font color="#FF5000" style="font-family:sans-serif;font-size:48px;line-height:45px"><b><font color="#FF6600" face="강산체"><?=number_format($row->sale_money)?></font></b></td>
							<td><IMG SRC="<?=$Dir?>images/common/coupon_text<?=$row->sale_type?>.gif" border="0"></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
					<td background="<?=$Dir?>images/common/coupon_table04.gif"></td>
				</tr>
				<tr>
					<td colspan="3"><IMG SRC="<?=$Dir?>images/common/coupon_table03.gif" border="0"></td>
				</tr>
				</table>
				</TD>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>
			<? } ?>
			<tr>
				<TD class="table_cell" width="143"><img src="images/icon_point2.gif" width="8" height="11" border="0"><?=(!in_array($row->issue_type,array('O','P')))?'발생':'인증'?>된 쿠폰수</TD>
				<TD class="td_con1" width="349"><b><?=number_format($row->issue_no)?>개</b><?=(!in_array($row->issue_type,array('O','P')))?'(실 누적 발급 쿠폰 수)':''?></TD>
			</tr>
			<TR>
				<TD background="images/table_top_line.gif" colspan=2></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		</table>
		</td>
		<td width="18">&nbsp;</td>
	</tr>
	<tr>
		<td width="18">&nbsp;</td>
		<td align="center"><a href="javascript:window.close()"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="5" border=0></a></td>
		<td width="18">&nbsp;</td>
	</tr>
	</table>
	</TD>
</TR>
</TABLE>

</body>
</html>