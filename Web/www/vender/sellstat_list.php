<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");
include_once($Dir."lib/admin_more.php");

//중계업 여부 조회
$shop_more_info = getShopMoreInfo();
$shop_relay = $shop_more_info['relay'];
//중계업 여부 조회

$CurrentTime = time();
/*
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",mktime(0,0,0,date("m")-1,date("d"),date("Y")));
*/
$today = date("Y-m-d",$CurrentTime);

$orderby=$_POST["orderby"];
if($orderby!="deli_date" && $orderby!="ordercode") $orderby="deli_date";

$vender = $_VenderInfo->getVidx();
$s_check=$_POST["s_check"];
$search_date=$_POST["search_date"];

$after_chk = "";
if ($search_date>$today) {
	$after_chk = "1";
}
/*
$search=$_POST["search"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];

$search_start=$search_start?$search_start:$period[1];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[1]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";
*/

$search_date = $search_date?$search_date:$today;
$search_d=$search_date?str_replace("-","",$search_date):str_replace("-","",$today);

//${"check_vperiod".$vperiod} = "checked";

$tempstart = explode("-",$search_date);
$tempend = explode("-",$today);
$termday = (mktime(0,0,0,$tempend[1],$tempend[2],$tempend[0])-mktime(0,0,0,$tempstart[1],$tempstart[2],$tempstart[0]))/86400;
if ($termday>366) {
	echo "<script>alert('검색기간은 1년을 초과할 수 없습니다.');location='".$_SERVER[PHP_SELF]."';</script>";
	exit;
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

$t_count=0;
$sumprice=0;
$sumreserve=0;
$sumdeliprice=0;
$sumcouprice=0;
/*
$sql = "SELECT COUNT(DISTINCT(a.ordercode)) as t_count, (select rate from tblvenderinfo where vender = b.vender) as rate,";
$sql.= "SUM(IF((b.productcode!='99999999990X' AND NOT (b.productcode LIKE 'COU%')), b.price*b.quantity,NULL)) as sumprice, ";
$sql.= "SUM(IF(b.productcode LIKE 'COU%', b.price,NULL)) as sumcouprice, ";
$sql.= "SUM(b.reserve*b.quantity) as sumreserve, SUM(IF(b.productcode='99999999990X', b.price,NULL)) as sumdeliprice ";
$sql.= "FROM tblorderinfo a, tblorderproduct b ".$qry." GROUP BY a.ordercode,b.vender ";
*/


$qry = getVenderOrderAdjustList($vender, $search_date, $s_check);

//1개만 조회
$sql = "SELECT COUNT(DISTINCT(ordercode)) as t_count,
SUM(IF((productcode!='99999999990X' AND NOT (productcode LIKE 'COU%')), price,NULL)) as sumprice,
SUM(cou_price) as sumcouprice, 
SUM(reserve) as sumreserve, SUM(deli_price) as sumdeliprice, sum(adjust) as sumadjust
FROM `order_adjust_detail` ".$qry;



$result = mysql_query($sql,get_db_conn());
while($row = mysql_fetch_object($result)) {
	$t_count+=$row->t_count;
	$sumprice+=(int)$row->sumprice;
	$sumreserve+=(int)$row->sumreserve;
	$sumdeliprice+=(int)$row->sumdeliprice;
	$sumcouprice+=(int)$row->sumcouprice;
	/*
	$sum+=(int)(($row->sumprice+$row->sumdeliprice-$row->sumreserve-$row->sumcouprice)*(100-$row->rate)/100);
	*/
	$sumadjust +=(int)$row->sumadjust;
}
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
?>

<? INCLUDE "header.php"; ?>

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


function goCalendar(vender, year, month) {

	location.href="vender_calendar.php?vender="+vender+"&year="+year+"&month="+month;

}

</script>
<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed"  height="100%" >
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
					<td><img src="images/sellstat_list_title.gif"></td>
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
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">입점사가 등록한 상품에 대해서만  조회할 수 있습니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">입점사는 통계자료만 열람할 수 있으며, 통계자료 수정 및 삭제는 본사 관리자만 가능합니다.</td>
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
							&nbsp;<U>정산기준 만료일자</U>&nbsp; <input class="input" type=text name=search_date value="<?=$search_date?>" size=10 onfocus="this.blur();" OnClick="Calendar(this)" style="text-align:center;">
							<!--
							~ <input class="input" type=text name=search_end value="<?=$search_end?>" size=10 onfocus="this.blur();" OnClick="Calendar(this)" style="text-align:center;">
							&nbsp;
							<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
							<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
							<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
							<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							&nbsp;
							-->
							<A HREF="javascript:searchForm()"><img src=images/btn_inquery03.gif border=0 align=absmiddle></A>
							</td>
						</tr>
						<tr><td height=5></td></tr>
						<!--
						<tr>
							<td>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<U>검색어</U>&nbsp;
							<select name=s_check style="width:94px">
							<option value="cd" <?if($s_check=="cd")echo"selected";?>>주문코드</option>
							<option value="mn" <?if($s_check=="mn")echo"selected";?>>구매자성명</option>
							<option value="mi" <?if($s_check=="mi")echo"selected";?>>구매회원ID</option>
							<option value="cn" <?if($s_check=="cn")echo"selected";?>>비회원주문번호</option>
							</select>
							<input type=text name=search value="<?=$search?>" style="width:200" class="input">
							</td>
						</tr>
						-->
						<tr>
							<td>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<U>정산상태</U>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<select name=s_check style="width:94px" class="select">
								<option value="" <?if($s_check=="")echo"selected";?>>전체</option>
								<option value="1" <?if($s_check=="1")echo"selected";?>>정산대기중</option>
								<option value="2" <?if($s_check=="2")echo"selected";?>>지급완료</option>
								<option value="3" <?if($s_check=="3")echo"selected";?>>처리완료</option>
							</select>
							</TD>
						</TR>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</form>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr><td height=25></td></tr>
				<tr>
					<td><img src="images/sellstat_list_stitle01.gif" border=0 align=absmiddle alt="기간 내 합계"></td>
				</tr>
				<tr><td height=2></td></tr>
				<tr><td height=1 bgcolor=#cccccc></td></tr>
				</table>

				<table border=0 cellpadding=5 cellspacing=1 bgcolor=#D4D4D4 width=100% style="table-layout:fixed">
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<tr height=28 bgcolor=#FEFCDA>
					<td align=center><B>총 상품 판매액</B></td>
					<td align=center><B>총 수수료</B></td>
					<td align=center><B>총 배송료</B></td>
					<td align=center><B>총 지급 적립금</B></td>
					<td align=center><B>총 쿠폰 할인액</B></td>
					<!--
					<td align=center><B>총 금액</B></td>
					-->
					<td align=center><B>총 정산 금액</B></td>

				</tr>
				<tr bgcolor=#FFFFFF>
					<td align=right style="padding-right:10"><B><?=number_format($sumprice)?>원</B></td>
					<td align=right style="padding-right:10"><B><?=number_format($sumprice-$sumadjust)?>원</B></TD>
					<td align=right style="padding-right:10"><B><?=($sumdeliprice>0?"+":"").number_format($sumdeliprice)?>원</B></td>
					<td align=right style="padding-right:10"><B><?=($sumreserve>0?"-":"").number_format($sumreserve)?>원</B></td>
					<td align=right style="padding-right:10"><B><?=number_format($sumcouprice)?>원</B></td>
					<!--
					<td align=right style="padding-right:10"><B><?=number_format($sumprice+$sumdeliprice-($sumreserve-$sumcouprice))?>원</B></td>
					-->
					<td align=right style="padding-right:10"><B><?=number_format($sumadjust)?>원</B></td>
				</tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=130></col>
				<col width=200></col>
				<col width=></col>
				<tr><td colspan=3 height=20></td></tr>
				<tr>
					<td colspan=2 style="padding-bottom:2">
					<B>정렬방법</B> 
					<select name=orderby onchange="GoOrderby(this.options[this.selectedIndex].value)">
					<option value="deli_date" <?if($orderby=="deli_date")echo"selected";?>>구입결정일</option>
					<option value="ordercode" <?if($orderby=="ordercode")echo"selected";?>>주문코드</option>
					</select>
					</td>
					<td align=right valign=bottom>
					총 주문수 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;
					현재 <B><?=$gotopage?>/<?=ceil($t_count/$setup[list_num])?></B> 페이지
					</td>
				</tr>
				<tr><td colspan=3 height=1 bgcolor=#cccccc></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
				<col width=135></col> <!-- 구입결정일/주문코드 -->
				<col width=></col> <!-- 상품명 -->
				<col width=30></col> <!-- 수량 -->
				<col width=60></col> <!-- 판매금액 -->
				<col width=110></col> <!-- 수수료 -->
				<col width=55></col> <!-- 적립금 -->
				<col width=55></col> <!-- 총 배송료 -->
				<col width=60></col> <!-- 쿠폰 사용액 -->
				<col width=70></col> <!-- 결제금액 -->
				<col width=100></col> <!-- 정산금액 -->
				<col width=100></col> <!-- 정산상태 -->
				<tr height=32 align=center bgcolor=F5F5F5>
					<td align=center ><B>구입결정일/주문코드</B></td>
					<td align=center ><B>상품명</B></td>
					<td align=center ><B>수량</B></td>
					<td align=center ><B>판매금액</B></td>
					<td align=center ><B>수수료 -> 금액 
					<? if($shop_relay=="1") {?>
					<br/>(수수료의 부가세)
					<? } ?>
					</B></td>
					<td align=center ><B>적립금</B></td>
					<td align=center ><B>배송료</B></td>
					<td align=center ><B>쿠폰할인</B></td>
					<td align=center ><B>결제금액</B></td>
					<TD align=center ><B>정산금액</B></TD>
					<td align=center ><B>정산상태</B></td>
				</tr>
<?
				$colspan=11;
				$sql ="SELECT SUM(IF((productcode!='99999999990X' AND NOT (productcode LIKE 'COU%')), price,NULL)) as sumprice, ";
				$sql.= "SUM(reserve) as sumreserve, ";
				$sql.= "SUM(deli_price) as sumdeliprice, ";
				$sql.= "SUM(cou_price) as sumcouprice, ";
				$sql.= "ordercode, deli_date, vender, sum(adjust) as sumadjust FROM `order_adjust_detail` ".$qry." ";
				$sql.="GROUP BY ordercode, vender ORDER BY ".$orderby." DESC ";
				$sql.="LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];

				$result=mysql_query($sql,get_db_conn());
				$i=0;
				while($row=mysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
					$date = substr($row->deli_date,0,4)."/".substr($row->deli_date,4,2)."/".substr($row->deli_date,6,2)." (".substr($row->deli_date,8,2).":".substr($row->deli_date,10,2).")";

					echo "<tr bgcolor=#FFFFFF onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='#FFFFFF'\">\n";
					echo "	<td align=center style=\"font-size:8pt;line-height:12pt\"><A HREF=\"javascript:OrderDetailView('".$row->ordercode."')\">".$date."<br>".$row->ordercode."</A></td>\n";
					echo "	<td colspan=5>\n";
					echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% style=\"table-layout:fixed\">\n";
					echo "	<col width=></col>\n";
					echo "	<col width=1></col>\n";
					echo "	<col width=30></col>\n";
					echo "	<col width=1></col>\n";
					echo "	<col width=60></col>\n";
					echo "	<col width=1></col>\n";
					echo "	<col width=110></col>\n";
					echo "	<col width=1></col>\n";
					echo "	<col width=55></col>\n";

					$sql = "SELECT o.*,
							a.account_rule, a.rate, a.cost, a.status,
							a.relay, a.rate_price, a.surtax
							FROM tblorderproduct o left join order_adjust_detail a
							on o.ordercode=a.ordercode and o.productcode=a.productcode
							WHERE o.vender='".$row->vender."' AND o.ordercode='".$row->ordercode."' ";
					$sql.=  getVenderOrderAdjustListGoods($row->vender, $search_date);
					$sql.= "AND NOT (o.productcode LIKE 'COU%' OR o.productcode LIKE '999999%') ";

					$status_chk=0;
					$result2=mysql_query($sql,get_db_conn());
					$jj=0;
					while($row2=mysql_fetch_object($result2)) {

						$a_rule = $row2->account_rule;
						$rate = $row2->rate;
						$cost = $row2->cost;

						$relay = $row2->relay;
						$rate_price = $row2->rate_price;
						$surtax = $row2->surtax;

						$rate_val = 0;


						if ($a_rule =='1') {
							$rate_val = $row2->price*$row2->quantity - $cost." 원";
						}else{
							$rate_val = $rate." % ->".$rate_price." 원";
						}

						if ($relay == "1") {
							$rate_val .= "<br/>";
							$rate_val .= "(".$surtax."원)";
						}

						$s_value ="";

						if ($row2->status != 1) {
							$status_chk++;
						}

						if($jj>0) echo "<tr><td colspan=9 height=1 bgcolor=#E7E7E7></tr>";
						echo "<tr>\n";
						echo "	<td style=\"font-size:8pt;padding:3;line-height:11pt\">".$row2->productname."</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=center style=\"font-size:8pt\">".$row2->quantity."</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=right style=\"font-size:8pt;padding:3\">".number_format($row2->price*$row2->quantity)."&nbsp;</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=right style=\"font-size:8pt;padding:3\">".$rate_val."&nbsp;</td>\n";
						echo "	<td bgcolor=#E7E7E7></td>\n";
						echo "	<td align=right style=\"font-size:8pt;padding:3\">".($row2->reserve>0?"-":"").number_format($row2->reserve*$row2->quantity)."&nbsp;</td>\n";
						echo "</tr>\n";
						$jj++;
					}
					mysql_free_result($result2);
		

					/*
					$s_value = "";

					$sql = "select * from order_account_new where vender=".$row->vender." and date='".$search_d."'";
					$result2=mysql_query($sql,get_db_conn());
					$row2=mysql_fetch_object($result2);

					if ($row2->confirm=="Y") {
						$s_value = "처리완료<br/>".substr($row2->reg_date, 0, 10);
					}else if ($row2->confirm=="N") {
						$s_value = "지급완료<br/>".substr($row2->reg_date, 0, 10);
					}else{
						$s_value = "정산대기";
					}
					*/

					if ($status_chk==0) {
						//$status_value = "정산대기중";
						//오늘날짜보다 이후면 처리못함
						if ($after_chk=="1") {
							$status_value = "정산진행중";
						}else{
							$status_value = "정산대기중";
						}

					}else{
						$status_value = "정산처리";
					}

					echo "	</table>\n";
					echo "	</td>\n";
					echo "	<td align=right style=\"font-size:8pt;padding:3\">".($row->sumdeliprice>0?"+":"").number_format($row->sumdeliprice)."&nbsp;</td>\n";
					echo "	<td align=right style=\"font-size:8pt;padding:3\">".number_format($row->sumcouprice)."&nbsp;</td>\n";
					echo "	<td align=right style=\"font-size:8pt;padding:3\"><B>".number_format($row->sumprice+$row->sumdeliprice-($row->sumreserve-$row->sumcouprice))."</B>&nbsp;</td>\n";
					echo "	<td align=right style=\"font-size:8pt;padding:3\"><B>".number_format($row->sumadjust)."</B>&nbsp;</td>\n";
					echo "	<td align=center style=\"font-size:8pt;padding:3\"><B>".$status_value."</B>&nbsp;</td>\n";
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

<form name=detailform method="post" action="order_detail.php" target="vorderdetail">
<input type=hidden name=ordercode>
</form>

<form name=pageForm method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=search_date value="<?=$search_date?>">
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=s_check value="<?=$s_check?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=orderby value="<?=$orderby?>">
<input type=hidden name=block>
<input type=hidden name=gotopage>
</form>

</table>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>