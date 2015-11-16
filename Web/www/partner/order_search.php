<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("access.php");

$joindate = $_PartnerInfo->getJoindate();
if(strlen($joindate)==0) $joindate=date("YmdHis");
$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m",$CurrentTime)."-01";
$period[3] = date("Y",$CurrentTime)."-01-01";
$period[4] = substr($joindate,0,4)."-".substr($joindate,4,2)."-".substr($joindate,6,2);

$up_paymethod=$_POST["up_paymethod"];
$up_deli_gbn=$_POST["up_deli_gbn"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=$_POST["vperiod"];

$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

${"check_vperiod".$vperiod} = "checked";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (mktime(0,0,0,$tempend[1],$tempend[2],$tempend[0])-mktime(0,0,0,$tempstart[1],$tempstart[2],$tempstart[0]))/86400;
if ($termday>31) {
	echo "<script>alert('검색기간은 1개월을 초과할 수 없습니다.');location='".$_SERVER[PHP_SELF]."';</script>";
	exit;
}

?>

<html>
<head>
<title>제휴사 주문현황</title>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<link rel="stylesheet" href="style.css">
<script type="text/javascript" src="calendar.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function OrderPrint() {
	if (confirm("해당 주문내역을 출력하시겠습니까?")) print();
}
function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}
function CheckForm() {
	document.form1.submit();
}
//-->
</SCRIPT>
</head>
<!--body oncontextmenu="return false" ondragstart="return false" onselectstart="return false" oncontextmenu="return false"-->
<body>
<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed;">
<tr>
	<td width=100%>
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<tr>
		<td width="100%" height="25"> <font color="#336699" size="3"><B>▒▒▒▒ <FONT COLOR="red">[<?=$_PartnerInfo->getPartnerid()?>]</FONT> 제휴사 주문현황 ▒▒▒▒</B></font> <img width=10 height=0> <font color="#cc0000">총 방문자수 :  <B><?=number_format($hit_cnt)?></B></font></td>
		<td width=150 align=right style="padding-right:0" nowrap><input type=button value="로그아웃" onclick="location.href='<?=$_SERVER[PHP_SELF]?>?type=logout'"></td>
	</tr>
	<tr><td height=1 bgcolor=#FF4800 colspan=2></td></tr>
	<tr><td height=1 bgcolor=#ffffff colspan=2></td></tr>
	<tr>
		<td colspan=2 style="padding-top:5;"><nobr>
		<LI> 검색조건을 선택하신 후 검색을 하시면 해당 기간동안 귀사를 통하여 방문한 사용자들의 주문현황을 확인할 수 있습니다. (1개월 이내로 검색 가능)
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr><td height=20></td></tr>
<tr>
	<td align=center>
	<table border=0 cellpadding=0 cellspacing=0 width=93%>
	<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
	<tr>
		<td class="lineleft" width="150" align=right bgcolor="#F0F0F0" style="padding-right:5" nowrap>결제방법 선택</td>
		<td class="line" width=100% bgcolor="#FFFFFF" style="padding-left:5;padding-top:3;padding-bottom:3">
		<select name=up_paymethod>
<?
		$arpm=array("\"\":전체선택","B:무통장","V:계좌이체","O:가상계좌","Q:가상계좌(매매보호)","C:신용카드",/*"P:신용카드(매매보호)",*/"M:핸드폰");
		for($i=0;$i<count($arpm);$i++) {
			$tmp=split(":",$arpm[$i]);
			echo "<option value=\"".$tmp[0]."\" ";
			if($tmp[0]==$up_paymethod) echo "selected";
			echo ">".$tmp[1]."</option>\n";
		}
?>
		</select>
		</td>
	</tr>
	<tr>
		<td class="lineleft" width="150" align=right bgcolor="#F0F0F0" style="padding-right:5" nowrap>처리상태 선택</td>
		<td class="line" width=100% bgcolor="#FFFFFF" style="padding-left:5;padding-top:3;padding-bottom:3">
		<select name=up_deli_gbn>
<?
		$ardg=array("\"\":전체선택","S:발송준비","Y:배송","N:미처리","C:주문취소","R:반송","D:취소요청");
		for($i=0;$i<count($ardg);$i++) {
			$tmp=split(":",$ardg[$i]);
			echo "<option value=\"".$tmp[0]."\" ";
			if($tmp[0]==$up_deli_gbn) echo "selected";
			echo ">".$tmp[1]."</option>\n";
		}
?>
		</select>
		</td>
	</tr>
	<tr>
		<td class="linebottomleft" width="150" align=right bgcolor="#F0F0F0" style="padding-right:5" nowrap>검색기간 선택</td>
		<td class="linebottom" width=100% bgcolor="#FFFFFF" style="padding-left:5">
		<input type=text name=search_start value="<?=$search_start?>" size=10 onfocus="this.blur();" OnClick="Calendar(this)" style="background:#efefef"> ~ <input type=text name=search_end value="<?=$search_end?>" size=10 onfocus="this.blur();" OnClick="Calendar(this)" style="background:#efefef">

		<input type=radio id=idx_vperiod0 name=vperiod value="0" style='border:0px;' onclick="OnChangePeriod(this.value)" <?=$check_vperiod0?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_vperiod0>오늘</label>
		<input type=radio id=idx_vperiod1 name=vperiod value="1" style='border:0px;' onclick="OnChangePeriod(this.value)" <?=$check_vperiod1?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_vperiod1>1주일</label>
		<input type=radio id=idx_vperiod2 name=vperiod value="2" style='border:0px;' onclick="OnChangePeriod(this.value)" <?=$check_vperiod2?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_vperiod2>이달</label>
		<img width=20 height=0>
		<input type=button value=" 조회하기 " onclick="CheckForm();">
		</td>
	</tr>
	</form>
	</table>
	</td>
</tr>
<tr><td height=10></td></tr>
<tr>
	<td align=center>
<?
	$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");

	$qry = "WHERE partner_id='".$_PartnerInfo->getPartnerid()."' ";
	if(substr($search_s,0,8)==substr($search_e,0,8)) {
		$qry.= "AND ordercode LIKE '".substr($search_s,0,8)."%' ";
	} else {
		$qry.= "AND ordercode>='".$search_s."' AND ordercode <='".$search_e."' ";
	}
	if(strlen($up_paymethod)>0)	$qry.= "AND paymethod LIKE '".$up_paymethod."%' ";
	if(strlen($up_deli_gbn)>0)		$qry.= "AND deli_gbn='".$up_deli_gbn."' ";
	$qry.= "AND ((paymethod='B' AND pay_flag='N') OR pay_flag='0000') ";

	$sql = "SELECT * FROM tblorderinfo ".$qry." ORDER BY ordercode";
	$result = mysql_query($sql,get_db_conn());
	$count=mysql_num_rows($result);
?>
	<table border=0 cellpadding=0 cellspacing=0 width=93%>
	<tr><td colspan=7>총 주문건수 : <?=$count?></td></tr>
	<tr bgcolor=#F0F0F0>
		<td class=lineleft width=50 align=center nowrap>No</td>
		<td class=line width=120 align=center nowrap>주문일자</td>
		<td class=line width=100% align=center>상품명</td>
		<td class=line width=100 align=center nowrap>주문자</td>
		<td class=line width=100 align=center nowrap>결제방법</td>
		<td class=line width=75 align=center nowrap>가격</td>
		<td class=line width=115 align=center nowrap>처리여부</td>
	</tr>
<?
	$i=0;
	$sumprice=0;
	while($row=mysql_fetch_object($result)) {
		$i++;
		$sumprice+=$row->price;
		$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";
		echo "<tr>\n";
		echo "	<td class=lineleft align=center>".$i."</td>\n";
		echo "	<td class=line align=center>".$date."</td>\n";
		echo "	<td class=line style=\"padding:2;line-height:12pt\">\n";
		$sql = "SELECT * FROM tblorderproduct WHERE ordercode='".$row->ordercode."' ";
		$result2 = mysql_query($sql,get_db_conn());
		$cnt = 0;
		while ($row2=mysql_fetch_object($result2)) { 
			if ($cnt++!=0) {
				echo "<br>";
			}
			echo "$row2->productname";
			if (strlen($row2->opt1_name)>0) echo "($row2->opt1_name)";
			if (strlen($row2->opt2_name)>0) echo "($row2->opt2_name)";
		}
		mysql_free_result($result2);
		echo "	</td>\n";
		echo "	<td class=line align=center>".$row->sender_name."</td>\n";
		echo "	<td class=line align=center>".$arpm[substr($row->paymethod,0,1)]." ";
		if(preg_match("/^(B){1}/", $row->paymethod)) {	//무통장
			if (strlen($row->bank_date)==9 && substr($row->bank_date,8,1)=="X") echo "<font color=005000> [환불]</font>";
			else if (strlen($row->bank_date)>0) echo " <font color=004000>[입금완료]</font>";
		} else if(preg_match("/^(V){1}/", $row->paymethod)) {	//계좌이체
			if (strcmp($row->pay_flag,"0000")!=0) echo " <font color=#757575>[결제실패]</font>";
			else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000> [환불]</font>";
			else if ($row->pay_flag=="0000") echo "<font color=0000a0> [결제완료]</font>";
		} else if(preg_match("/^(M){1}/", $row->paymethod)) {	//핸드폰
			if (strcmp($row->pay_flag,"0000")!=0) echo " <font color=#757575>[결제실패]</font>";
			else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000> [취소완료]</font>";
			else if ($row->pay_flag=="0000") echo "<font color=0000a0> [결제완료]</font>";
		} else if(preg_match("/^(O|Q){1}/", $row->paymethod)) {	//가상계좌
			if (strcmp($row->pay_flag,"0000")!=0) echo " <font color=#757575>[주문실패]</font>";
			else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000> [환불]</font>";
			else if ($row->pay_flag=="0000" && strlen($row->bank_date)==0) echo "<font color=red> [미입금]</font>";
			else if ($row->pay_flag=="0000" && strlen($row->bank_date)>0) echo "<font color=0000a0> [입금완료]</font>";
		} else {
			if (strcmp($row->pay_flag,"0000")!=0) echo " <font color=#757575>[카드실패]</font>";
			else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="N") echo "<font color=red> [카드승인]</font>";
			else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="Y") echo "<font color=0000a0> [결제완료]</font>";
			else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000> [취소완료]</font>";
		}
		echo "	</td>\n";
		echo "	<td class=line align=right>".number_format($row->price)."&nbsp;</td>\n";
		echo "	<td class=line align=center>&nbsp;";
		switch($row->deli_gbn) {
			case 'S': echo "발송준비";  break;
			case 'X': echo "배송요청";  break;
			case 'Y': echo "배송";  break;
			case 'D': echo "<font color=blue>취소요청</font>";  break;
			case 'N': echo "미처리";  break;
			case 'E': echo "<font color=red>환불대기</font>";  break;
			case 'C': echo "<font color=red>주문취소</font>";  break;
			case 'R': echo "반송";  break;
			case 'H': echo "배송(<font color=red>정산보류</font>)";  break;
		}
		if($row->deli_gbn=="D" && strlen($row->deli_date)==14) echo " (배송)";
		echo "	&nbsp;</td>\n";
		echo "</tr>\n";
	}
	mysql_free_result($result);
	if ($i==0) {
		echo "<tr><td class=lineleft colspan=7 align=center>검색된 주문내역이 없습니다.</td></tr>";
	}
	echo "<tr><td colspan=7 height=1 bgcolor=#dadada></td></tr>\n";
	echo "<tr><td colspan=7 align=right>합계 : <B>".number_format($sumprice)."</B>원</td></tr>\n";
?>
	<tr>
		<td colspan=7 style="padding-top:5">
		<LI> 신용카드 실패내역은 조회되지 않습니다.
		<LI> 구매자 신용정보 보호를 위하여 상세정보 조회는 지원하지 않습니다.
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr><td height=20></td></tr>
</table>
</body>
</html>