<?
session_start();
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",mktime(0,0,0,date("m")-1,date("d"),date("Y")));

$orderby=$_POST["orderby"];
if(strlen($orderby)==0) $orderby="DESC";

$paystate=$_POST["paystate"];
$deli_gbn=$_POST["deli_gbn"];
$s_check=$_POST["s_check"];
$search=$_POST["search"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];

$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

${"check_vperiod".$vperiod} = "checked";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (mktime(0,0,0,$tempend[1],$tempend[2],$tempend[0])-mktime(0,0,0,$tempstart[1],$tempstart[2],$tempstart[0]))/86400;
if ($termday>366) {
	echo "<script>alert('검색기간은 1년을 초과할 수 없습니다.');location='".$_SERVER[PHP_SELF]."';</script>";
	exit;
}

//tblorderinfo, tblorderproduct
$qry.= "WHERE a.ordercode=b.ordercode ";
$qry.= "AND b.vender='".$_VenderInfo->getVidx()."' ";
if(substr($search_s,0,8)==substr($search_e,0,8)) {
	$qry.= "AND a.ordercode LIKE '".substr($search_s,0,8)."%' ";
} else {
	$qry.= "AND a.ordercode>='".$search_s."' AND a.ordercode <='".$search_e."' ";
}
$qry.= "AND NOT (b.productcode LIKE 'COU%' OR b.productcode LIKE '999999%') ";
if(strlen($deli_gbn)>0)	$qry.= "AND b.deli_gbn='".$deli_gbn."' ";
if($paystate=="Y") {		//입금
	$qry.= "AND ((MID(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (MID(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000')) ";
} else if($paystate=="B") {	//미입금
	$qry.= "AND ((MID(a.paymethod,1,1) IN ('B','O','Q') AND (a.bank_date IS NULL OR a.bank_date='')) OR (MID(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag!='0000' AND a.pay_admin_proc='C')) ";
} else if($paystate=="C") {	//환불
	$qry.= "AND ((MID(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=9) OR (MID(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag='0000' AND a.pay_admin_proc='C')) ";
}
if(strlen($search)>0) {
	if($s_check=="cd") $qry.= "AND a.ordercode='".$search."' ";
	else if($s_check=="pn") $qry.= "AND b.productname LIKE '".$search."%' ";
	else if($s_check=="mn") $qry.= "AND a.sender_name='".$search."' ";
	else if($s_check=="mi") $qry.= "AND a.id='".$search."' ";
	else if($s_check=="cn") $qry.= "AND a.id='".$search."X' ";
}


$setup[page_num] = 10;
$setup[list_num] = 10;

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];
if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}

$sql = "SELECT COUNT(DISTINCT(a.ordercode)) as t_count FROM tblorderinfo a, tblorderproduct b ".$qry." ";
$result = mysql_query($sql,get_db_conn());
$row = mysql_fetch_object($result);
$t_count = $row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

?>

<? INCLUDE "header.php"; ?>
<style>
	#orderDeliForm{border:0px;padding:0px;margin:0px;}
</style>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">
function OnChangePeriod(val) {
	var pForm = document.sForm;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function searchForm() {
	document.sForm.submit();
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","vorderdetail","scrollbars=yes,width=800,height=600");
	document.detailform.submit();
}

function searchSender(name) {
	document.sForm.s_check.value="mn";
	document.sForm.search.value=name;
	document.sForm.submit();
}

function searchId(id) {
	document.sForm.s_check.value="mi";
	document.sForm.search.value=id;
	document.sForm.submit();
}

function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chkordercode[i].checked=chkval;
   }
}

function GoPage(block,gotopage) {
	document.pageForm.block.value=block;
	document.pageForm.gotopage.value=gotopage;
	document.pageForm.submit();
}

function GoOrderby(orderby) {
	document.pageForm.block.value = "";
	document.pageForm.gotopage.value = "";
	document.pageForm.orderby.value = orderby;
	document.pageForm.submit();
}

function AddressPrint() {
	document.sForm.action="order_address_excel.php";
	document.sForm.target="processFrame";
	document.sForm.submit();
	document.sForm.action="";
	document.sForm.target="";
}

function OrderExcel() {
	document.sForm.action="order_excel.php";
	document.sForm.target="processFrame";
	document.sForm.submit();
	document.sForm.target="";
	document.sForm.action="";
}

function OrderCheckExcel() {
	document.checkexcelform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked==true) {
			document.checkexcelform.ordercodes.value+=document.form2.chkordercode[i].value.substring(0)+",";
		}
	}
	if(document.checkexcelform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	document.checkexcelform.action="order_excel.php";
	document.checkexcelform.target="processFrame";
	document.checkexcelform.submit();
	document.checkexcelform.target="";
}

function OrderDeliExcel() {
	document.checkexcelform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked==true) {
			document.checkexcelform.ordercodes.value+=document.form2.chkordercode[i].value.substring(0)+",";
		}
	}
	if(document.checkexcelform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	document.checkexcelform.action="order_delivery.execel.php";
	document.checkexcelform.target="processFrame";
	document.checkexcelform.submit();
	document.checkexcelform.target="";
}


function OrderDeliCodeUpdate () {
	var a = 0 ;
	var OrderCodeList = "";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked==true) {
			var OrderCode = document.form2.chkordercode[i].value.substring(0);
			OrderCodeList += OrderCode+",";
			a++;
		}
	}

	if( a == 0 ) {
		alert("발송완료 & 송장번호를 입력 할 주문을 선택하세요!");
		return;
	} else {
		window.open("about:blank","OrderDeliCodeUpdatePop","width=717,height=640,scrollbars=yes");
		var F = document.OrderDeliCodeUpdatePopForm;
		F.ordercode.value=OrderCodeList;
		F.action="order_list.orderEnd.php";
		F.target="OrderDeliCodeUpdatePop";
		F.method="POST";
		F.submit();
	}
	F.ordercode.value='';
}
</script>
<table border=0 cellpadding=0 cellspacing=0 width=100% height="100%" style="table-layout:fixed">
<col width=190></col>
<col width=20></col>
<col width=></col>
<col width=20></col>
<tr>
	<td width=190 valign=top nowrap background="images/minishop_leftbg.gif"><? include ("menu.php"); ?></td>
	<td width=20 nowrap></td>
	<td valign=top style="padding-top:20px">

	<table width="100%"  border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td><img src="images/order_list_title.gif"></td>
				</tr>
				<tr>
					<td height=5 background="images/minishop_titlebg.gif">
				</tr>
				</table>
			</td>
		</tr>
		<tr><td height=10></td></tr>
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td colspan=3 >


						<table cellpadding="10" cellspacing="1" width="100%" bgcolor="#EFEFF2">
							<tr>
								<td  bgcolor="#F5F5F9" style="padding:20px">
									<table border=0 cellpadding=0 cellspacing=0 width=100%>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">입점사에서 등록한 상품만 주문관리 할 수 있습니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">주문일자 클릭시 상품에 대한 모든 정보를 확인할 수 있습니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">본사 쇼핑몰에서  상태변경시 입점사 주문조회 상태값이 자동 연동됩니다.(신규주문/입금확인/배송단계)</td>
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

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=40></td></tr>
			<tr>
				<td>
				


				
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<form name=sForm action="<?=$_SERVER[PHP_SELF]?>" method=post>
				<input type=hidden name=code value="<?=$code?>">
				<tr>
					<td valign=top bgcolor=D4D4D4 style=padding:1>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td valign=top bgcolor=F0F0F0 style=padding:10>
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
						<tr>
							<td>
							&nbsp;<U>기간선택</U>&nbsp; <input class=input type=text name=search_start value="<?=$search_start?>" size=10 onfocus="this.blur();" OnClick="Calendar(this)" style="text-align:center;"> ~ <input class=input type=text name=search_end value="<?=$search_end?>" size=10 onfocus="this.blur();" OnClick="Calendar(this)" style="text-align:center;">
							&nbsp;
							<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
							<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
							<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
							<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							&nbsp;&nbsp;&nbsp;
							<U>결제상태</U>&nbsp;
							<select name="paystate" style="">
<?
							$arps=array("\"\":전체선택","Y:입금","B:미입금","C:환불");
							for($i=0;$i<count($arps);$i++) {
								$tmp=split(":",$arps[$i]);
								echo "<option value=\"".$tmp[0]."\" ";
								if($tmp[0]==$paystate) echo "selected";
								echo ">".$tmp[1]."</option>\n";
							}
?>
							</select>
							&nbsp;
							<A HREF="javascript:searchForm()"><img src=images/btn_inquery03.gif border=0 align=absmiddle></A>
							</td>
						</tr>
						<tr><td height=5></td></tr>
						<tr>
							<td>
							&nbsp;<U>처리단계</U>&nbsp;
							<select name="deli_gbn"  style="">
<?
							$ardg=array("\"\":전체선택","S:발송준비","Y:배송","N:미처리","C:주문취소","R:반송","D:취소요청","E:환불대기","H:배송(정산보류)");
							for($i=0;$i<count($ardg);$i++) {
								$tmp=split(":",$ardg[$i]);
								echo "<option value=\"".$tmp[0]."\" ";
								if($tmp[0]==$deli_gbn) echo "selected";
								echo ">".$tmp[1]."</option>\n";
							}
?>
							</select>
							&nbsp;&nbsp;&nbsp;
							<U>검색어</U>&nbsp;
							<select name=s_check style=";width:94px">
							<option value="cd" <?if($s_check=="cd")echo"selected";?>>주문코드</option>
							<option value="pn" <?if($s_check=="pn")echo"selected";?>>상품명</option>
							<option value="mn" <?if($s_check=="mn")echo"selected";?>>구매자성명</option>
							<option value="mi" <?if($s_check=="mi")echo"selected";?>>구매회원ID</option>
							<option value="cn" <?if($s_check=="cn")echo"selected";?>>비회원주문번호</option>
							</select>
							<input type=text name=search value="<?=$search?>" style="width:183" class=input>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td align=right>
					<A HREF="javascript:OrderExcel()"><img src=images/btn_orderexceldown.gif border=0 align=absmiddle></A>
					<A HREF="javascript:AddressPrint()"><img src=images/btn_addressdown.gif border=0 align=absmiddle></A>
					</td>
				</tr>
				</form>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=130></col>
				<col width=200></col>
				<col width=></col>
				<tr><td colspan=3 height=20></td></tr>
				<tr>
					<td>
					<A HREF="javascript:OrderCheckExcel()"><img src=images/btn_orderexceldown.gif border=0 align=absmiddle></A>
					</td>
					<td valign=bottom style="padding-left:20">
					<B>정렬 :</B> 
					<?if($orderby=="DESC"){?>
					<A HREF="javascript:GoOrderby('ASC');" style="color:blue"><B>주문일자순<FONT COLOR="red">↑</FONT></B></A>
					<?}else{?>
					<A HREF="javascript:GoOrderby('DESC');" style="color:blue"><B>주문일자순<FONT COLOR="red">↓</FONT></B></A>
					<?}?>
					</td>
					<td align=right valign=bottom>
					총 주문수 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;
					현재 <B><?=$gotopage?>/<?=ceil($t_count/$setup[list_num])?></B> 페이지
					</td>
				</tr>
				<tr><td colspan=3 height=1 bgcolor=#cccccc></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
				<!-- 선택,번호,상품명,결제일,주문자,ID/주문번호,수량,판매금액,결제상태,처리여부 -->
				<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
				<col width=25></col>
				<col width=70></col>
				<col width=120></col>
				<col width=></col>
				<col width=35></col>
				<col width=60></col>
				<col width=90></col>
				<col width=80></col>
				<tr height=32 align=center bgcolor=F5F5F5>
					<input type=hidden name=chkordercode>
					<td><input type=checkbox name=allcheck onclick="CheckAll()"></td>
					<td><B>주문일자</B></td>
					<td><B>주문자 정보</B></td><!-- 주문자, ID, 주문번호 -->
					<td><B>상품명</B></td>
					<td><B>수량</B></td>
					<td><B>판매금액</B></td>
					<td><B>처리여부</B></td>
					<td><B>결제상태</B></td>
				</tr>
<?
				$colspan=8;
				$sql = "SELECT a.ordercode,a.id,a.paymethod,a.pay_data,a.bank_date,a.pay_flag,a.pay_auth_no, ";
				$sql.= "a.pay_admin_proc,a.escrow_result,a.sender_name,a.del_gbn ";
				$sql.= "FROM tblorderinfo a, tblorderproduct b ".$qry." ";
				$sql.= "GROUP BY a.ordercode ORDER BY a.ordercode ".$orderby." ";
				$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
				$result=mysql_query($sql,get_db_conn());
				$i=0;
				while($row=mysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
					$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";
					$name=$row->sender_name;
					unset($stridX);
					unset($stridM);
					if(substr($row->ordercode,20)=="X") {	//비회원
						$stridX = substr($row->id,1,6);
					} else {	//회원
						$stridM = "<A HREF=\"javascript:searchId('".$row->id."');\"><FONT COLOR=\"blue\">".$row->id."</FONT></A>";
					}
					echo "<tr bgcolor=#FFFFFF onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='#FFFFFF'\">\n";
					echo "	<td align=center><input type=checkbox name=chkordercode value=\"".$row->ordercode."\"></td>\n";
					echo "	<td align=center style=\"padding:3;line-height:11pt\"><A HREF=\"javascript:OrderDetailView('".$row->ordercode."')\">".$date."</A></td>\n";
					echo "	<td style=\"padding:3;line-height:11pt\">\n";
					echo "	주문자 : <A HREF=\"javascript:searchSender('".$name."');\"><FONT COLOR=\"blue\">".$name."</font></A>";
					if(strlen($stridX)>0) {
						echo "<br> 주문번호 : ".$stridX;
					} else if(strlen($stridM)>0) {
						echo "<br> 아이디 : ".$stridM;
					}
					echo "	</td>\n";
					echo "	<td colspan=4>\n";
					echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
					echo "	<col width=></col>\n";
					echo "	<col width=1></col>\n";
					echo "	<col width=35></col>\n";
					echo "	<col width=1></col>\n";
					echo "	<col width=60></col>\n";
					echo "	<col width=1></col>\n";
					echo "	<col width=90></col>\n";
					$sql = "SELECT * FROM tblorderproduct WHERE vender='".$_VenderInfo->getVidx()."' AND ordercode='".$row->ordercode."' ";
					$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
					if(strlen($deli_gbn)>0)	$sql.= "AND deli_gbn='".$deli_gbn."' ";
					if(strlen($search)>0 && $s_check=="pn") {
						$sql.= "AND productname LIKE '".$search."%' ";
					}
					$result2=mysql_query($sql,get_db_conn());
					$jj=0;
					while($row2=mysql_fetch_object($result2)) {
						if($jj>0) echo "<tr><td colspan=7 height=1 bgcolor=#E7E7E7></tr>";
						echo "<tr>\n";
						echo "	<td style=\"padding:3;line-height:11pt\">".$row2->productname."</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=center>".$row2->quantity."</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=right style=\"padding:3\">".number_format($row2->price*$row2->quantity)."&nbsp;</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=center style=\"padding:3\">";
						switch($row2->deli_gbn) {
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
						if($row2->deli_gbn=="D" && strlen($row2->deli_date)==14) echo " (배송)";
						echo "	</td>\n";
						echo "</tr>\n";
						$jj++;
					}
					mysql_free_result($result2);
					echo "	</table>\n";
					echo "	</td>\n";
					echo "	<td align=center style=\"padding:3;line-height:12pt\">";
					if(preg_match("/^(B){1}/", $row->paymethod)) {	//무통장
						echo "무통장<br>";
						if (strlen($row->bank_date)==9 && substr($row->bank_date,8,1)=="X") echo "<font color=005000>[환불]</font>";
						else if (strlen($row->bank_date)>0) {
							echo "<font color=004000>[입금완료]</font>";
						} else {
							echo "[입금대기]";
						}
					} else if(preg_match("/^(V){1}/", $row->paymethod)) {	//계좌이체
						echo "계좌이체<br>";
						if (strcmp($row->pay_flag,"0000")!=0) echo "<font color=#757575>[결제실패]</font>";
						else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000>[환불]</font>";
						else if ($row->pay_flag=="0000") {
							echo "<font color=0000a0>[결제완료]</font>";
						}
					} else if(preg_match("/^(M){1}/", $row->paymethod)) {	//핸드폰
						echo "핸드폰<br>";
						if (strcmp($row->pay_flag,"0000")!=0) echo "<font color=#757575>[결제실패]</font>";
						else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000>[취소완료]</font>";
						else if ($row->pay_flag=="0000") {
							echo "<font color=0000a0>[결제완료]</font>";
						}
					} else if(preg_match("/^(O|Q){1}/", $row->paymethod)) {	//가상계좌
						echo "가상계좌<br>";
						if (strcmp($row->pay_flag,"0000")!=0) echo "<font color=#757575>[주문실패]</font>";
						else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000>[환불]</font>";
						else if ($row->pay_flag=="0000" && strlen($row->bank_date)==0) echo "<font color=red>[미입금]</font>";
						else if ($row->pay_flag=="0000" && strlen($row->bank_date)>0) {
							echo "<font color=0000a0>[입금완료]</font>";
						}
					} else {
						echo "신용카드<br>";
						if (strcmp($row->pay_flag,"0000")!=0) echo "<font color=#757575>[카드실패]</font>";
						else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="N") echo "<font color=red>[카드승인]</font>";
						else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="Y") {
							echo "<font color=0000a0>[결제완료]</font>";
						}
						else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000>[취소완료]</font>";
					}
					echo "	</td>\n";
					echo "</tr>\n";
					$i++;
				}
				mysql_free_result($result);
				$cnt=$i;
				if($i==0) {
					echo "<tr height=28 bgcolor=#FFFFFF><td colspan=".$colspan." align=center>조회된 내용이 없습니다.</td></tr>\n";
				} else if($i>0) {
					$total_block = intval($pagecount / $setup[page_num]);
					if (($pagecount % $setup[page_num]) > 0) {
						$total_block = $total_block + 1;
					}
					$total_block = $total_block - 1;
					if (ceil($t_count/$setup[list_num]) > 0) {
						// 이전	x개 출력하는 부분-시작
						$a_first_block = "";
						if ($nowblock > 0) {
							$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev_end.gif border=0 align=absmiddle></a> ";
							$prev_page_exists = true;
						}
						$a_prev_page = "";
						if ($nowblock > 0) {
							$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev.gif border=0 align=absmiddle></a> ";

							$a_prev_page = $a_first_block.$a_prev_page;
						}
						if (intval($total_block) <> intval($nowblock)) {
							$print_page = "";
							for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
								if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
									$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></font> ";
								} else {
									$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
								}
							}
						} else {
							if (($pagecount % $setup[page_num]) == 0) {
								$lastpage = $setup[page_num];
							} else {
								$lastpage = $pagecount % $setup[page_num];
							}
							for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
								if (intval($nowblock*$setup[page_num]) + $gopage == intval($gotopage)) {
									$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></FONT> ";
								} else {
									$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
								}
							}
						}
						$a_last_block = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
							$last_gotopage = ceil($t_count/$setup[list_num]);
							$a_last_block .= " <a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext_end.gif border=0 align=absmiddle></a>";
							$next_page_exists = true;
						}
						$a_next_page = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$a_next_page .= " <a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext.gif border=0 align=absmiddle></a>";
							$a_next_page = $a_next_page.$a_last_block;
						}
					} else {
						$print_page = "<B>1</B>";
					}
					$pageing=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
				}
?>
				<input type=hidden name=tot value="<?=$cnt?>">
				</form>

				<form name=detailform method="post" action="order_detail.php" target="vorderdetail">
				<input type=hidden name=ordercode>
				</form>

				</table>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="margin-top:5px;">
					<tr>
						<td>
							<a href="javascript:OrderDeliCodeUpdate();"><img src="images/btn_orderDeliCodeUpload.gif" border="0" alt="발송대기주문 일괄배송 관리(팝업)"></a>
							<a href="javascript:OrderDeliExcel();"><img src="images/btn_orderDeliExcelPrint.gif" border="0" alt="배송정보 일괄 엑셀 처리 다운로드"></a>
							<a href="./order_csvdelivery.php"><img src="images/btn_orderDeliCodeExcelUpload.gif" border="0" alt="배송정보 일괄 엑셀 처리 다운로드"></a>
						</td>
					</tr>
				</table>
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td align=center style="padding-top:10"><?=$pageing?></td>
				</tr>
				</table>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->
			
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>

	</td>
</tr>

<form name=pageForm method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=paystate value="<?=$paystate?>">
<input type=hidden name=deli_gbn value="<?=$deli_gbn?>">
<input type=hidden name=orderby value="<?=$orderby?>">
<input type=hidden name=block>
<input type=hidden name=gotopage>
</form>

<form name=checkexcelform action="order_excel.php" method=post>
<input type=hidden name=ordercodes>
</form>

</table>
<!-- 송장번호 일괄 처리를 위한 폼 추가 시작 -->
<form id="orderDeliForm" name=OrderDeliCodeUpdatePopForm>
	<input type=hidden name=ordercode>
</form>
<!-- 송장번호 일괄 처리를 위한 폼 추가 끝 -->
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>