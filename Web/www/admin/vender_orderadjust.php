<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");
include_once($Dir."lib/admin_more.php");

####################### 페이지 접근권한 check ###############
$PageCode = "vd-3";
$MenuCode = "vender";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

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

$vender=$_REQUEST["vender"];
$s_check=$_POST["s_check"];
$search_date=$_REQUEST["search_date"];

$after_chk = "";
if ($search_date>$today) {
	$after_chk = "1";
}
/*
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];

$search_start=$search_start?$search_start:$period[1];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[1]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";
*/

$search_date = $search_date?$search_date:$today;
$search_d=$search_date?str_replace("-","",$search_date):str_replace("-","",$today);

$after_date = date("Y-m-d", strtotime($search_date." -1 day"));
$next_date = date("Y-m-d", strtotime($search_date." 1 day"));

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

$qty = "";
$h_sql = "";
$b_sql = "";

if(strlen($vender)>0) {
	
	$qry = getVenderOrderAdjustList($vender, $search_date, $s_check);
	
	//1개만 조회
	$sql = "SELECT COUNT(DISTINCT(ordercode)) as t_count,
	SUM(IF((productcode!='99999999990X' AND NOT (productcode LIKE 'COU%')), price,NULL)) as sumprice,
	SUM(cou_price) as sumcouprice, 
	SUM(reserve) as sumreserve, SUM(deli_price) as sumdeliprice, sum(adjust) as sumadjust,
	SUM(rate_price) as sum_rate_price,
	SUM(surtax) as sum_surtax

	FROM `order_adjust_detail` ".$qry;

}else{

	//오늘이 정산일인 업체 조회
	$venders = getVenderToTodayOrderAccount($search_date);
	
	$h_sql = "select * from (";
	$b_sql = ") t ";
	$sql = "";

	$end_i = count($venders)-1;
	$i=0;
	while (isset($venders[$i])) {


		$t_vender = $venders[$i];
		$sql .= "SELECT COUNT(DISTINCT(ordercode)) as t_count,
		SUM(IF((productcode!='99999999990X' AND NOT (productcode LIKE 'COU%')), price,NULL)) as sumprice,
		SUM(cou_price) as sumcouprice, 
		SUM(reserve) as sumreserve, SUM(deli_price) as sumdeliprice, sum(adjust) as sumadjust,
		SUM(rate_price) as sum_rate_price,
		SUM(surtax) as sum_surtax

		FROM `order_adjust_detail` ";
		$sql .= getVenderOrderAdjustList($t_vender, $search_date, $s_check);
		
		
		if ($i<$end_i) {
			$sql .= "
				union
			";
		}

		$i++;

	}

	$sql = $h_sql.$sql.$b_sql;
}

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

	$sum_rate_price +=(int)$row->sum_rate_price;
	$sum_surtax +=(int)$row->sum_surtax;
}
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

$venderlist=array();
$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
$result=mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)) {
	$venderlist[$row->vender]=$row;
}
mysql_free_result($result);

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

function OrderDetailView(ordercode,vender) {
	document.detailform.ordercode.value = ordercode;
	document.detailform.vender.value = vender;
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

function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}

function venderexcelForm() {
	document.sForm.action="vender_order_excel.php";
	document.sForm.submit();
}

function goCalendar(vender, year, month) {

	location.href="vender_calendar.php?vender="+vender+"&year="+year+"&month="+month;

}


function detailView_toVender(vender, date) {
	owin=window.open("about:blank","calendar_detailview","scrollbars=no,width=400,height=300");
	owin.focus();
	document.dForm.vender.value=vender;
	document.dForm.date.value=date;
	document.dForm.target="calendar_detailview";
	document.dForm.action="vender_calendar.detail.php";
	document.dForm.submit();
}

function adjustModify(ordercode, vender) {
	
	window.open("about:blank","adjustModify","scrollbars=yes,resizable=yes, width=900,height=500");

	document.aForm.ordercode.value=ordercode;
	document.aForm.vender.value=vender;
	document.aForm.action="vender_order_adjust_modify.php";
	document.aForm.target="adjustModify";
	document.aForm.submit();


}

function dayMove(n) {

	if (n==1) {
		document.sForm.search_date.value="<?= $next_date ?>";
	}else{
		document.sForm.search_date.value="<?= $after_date ?>";
	}
	
	searchForm()

}

</script>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
		<col width=198></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top"  background="images/leftmenu_bg.gif">
			<? include ("menu_vender.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 주문/정산 관리 &gt; <span class="2depth_select">입점업체 정산관리</span></td>
			</tr>
			</table>
		</td>
	</tr>   
	<tr>
        <td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_01_bg.gif"></td>
        <td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr>
        <td width="16" background="images/con_t_04_bg1.gif"></td>
        <td bgcolor="#ffffff" style="padding:10px">




			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_orderadjust_title.gif" ALT=""></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">입점업체별 모든 주문건에 대한 정산 예정 주문내역을 확인할 수 있습니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<form name=sForm action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=code value="<?=$code?>">
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td bgcolor="#ededed" style="padding:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">정산일</TD>
							<TD class="td_con1" >
								<input type=text name=search_date value="<?= $search_date?>" size=13 onfocus="this.blur();" OnClick="Calendar(this)" class="input_selected">
								<!--
								~ <input type=text name=search_end value="<?=$search_end?>" size=13 onfocus="this.blur();" OnClick="Calendar(this)" class="input_selected"> 
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
								-->
								<a href="javascript:dayMove();"><img src="images/btn_prev_day.gif" border="0" align="absmiddle" alt="이전날" /></a> <a href="javascript:dayMove(1);"><img src="images/btn_next_day.gif" border="0" align="absmiddle" alt="다음날" /></a>
							</TD>
						</TR>
						<TR>
							<TD colspan="2" background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">입점업체</TD>
							<TD class="td_con1" ><select name="vender" style="width:180" class="select">
							<option value=""> 모든 입점업체</option>
<?
							$tmplist=$venderlist;
							while(list($key,$val)=each($tmplist)) {
								if($val->delflag=="N") {
									echo "<option value=\"".$val->vender."\"";
									if($vender==$val->vender) echo " selected";
									echo ">".$val->id." - ".$val->com_name."</option>\n";
								}
							}
?>
							</select></TD>
						</TR>
						<TR>
							<TD colspan="2" background="images/table_con_line.gif"></TD>
						</TR>
						<!--
						<TR>
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">검색어</TD>
							<TD class="td_con1" ><select name=s_check style="width:94px" class="select">
							<option value="cd" <?if($s_check=="cd")echo"selected";?>>주문코드</option>
							<option value="mn" <?if($s_check=="mn")echo"selected";?>>구매자성명</option>
							<option value="mi" <?if($s_check=="mi")echo"selected";?>>구매회원ID</option>
							<option value="cn" <?if($s_check=="cn")echo"selected";?>>비회원주문번호</option>
							</select>
							<input type=text name=search value="<?=$search?>" style="width:183" class="input"></TD>
						</TR>
						-->
						<TR>
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">정산상태</TD>
							<TD class="td_con1" ><select name=s_check style="width:94px" class="select">
								<option value="" <?if($s_check=="")echo"selected";?>>전체</option>
								<option value="1" <?if($s_check=="1")echo"selected";?>>정산대기중</option>
								<option value="2" <?if($s_check=="2")echo"selected";?>>지급완료</option>
								<option value="3" <?if($s_check=="3")echo"selected";?>>처리완료</option>
							</select>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:15pt;" align="center"><a href="javascript:venderexcelForm();"><img src="images/btn_excel1.gif" width="127" height="38" border="0" hspace="1"></a><a href="javascript:searchForm();"><img src="images/botteon_search.gif" width="113" height="38" border="0"></a></td>
			</tr>
			</form>
			<tr><td height="30"></td></tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372" align="left"><img src="images/icon_8a.gif" width="13" height="13" border="0"><B>기간 내 합계</B></td>
					<td width="372"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<?
					$t_col = 6;
					if ($sum_surtax>0) {
						$t_col = 7;
					}
				?>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				<col width=></col>
				<col width=></col>
				<? if ($sum_surtax>0) { ?>
				<col width=></col>
				<? } ?>
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<col width=></col>
				<TR>
					<TD background="images/table_top_line.gif" colspan="<?= $t_col ?>"></TD>
				</TR>
				<TR>
					<TD class="table_cell" align="center">총 상품 판매액</TD>
					<TD class="table_cell1" align="center">총 수수료</TD>
					<? if ($sum_surtax>0) { ?>
					<TD class="table_cell1" align="center">수수료의 부가세</TD>
					<? } ?>
					<TD class="table_cell1" align="center">총 배송료</TD>
					<TD class="table_cell1" align="center">총 지급 적립금</TD>
					<TD class="table_cell1" align="center">총 쿠폰 할인액</TD>
					<!--
					<TD class="table_cell1" align="center">총 금액</TD>
					-->
					<TD class="table_cell1" align="center">총 정산 금액</TD>
				</TR>
				<TR>
					<TD colspan="6" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="td_con2" align="center"><B><?=number_format($sumprice)?>원</B></TD>
					<TD class="td_con1" align="center"><B><?=number_format($sum_rate_price)?>원
					</B></TD>
					<? if ($sum_surtax>0) { ?>
					<TD class="td_con1" align="center"><B><?=number_format($sum_surtax)?>원
					<? } ?>
					<TD class="td_con1" align="center"><B><?=($sumdeliprice>0?"+":"").number_format($sumdeliprice)?>원</B></TD>
					<TD class="td_con1" align="center"><B><?=($sumreserve>0?"-":"").number_format($sumreserve)?>원</B></TD>
					<TD class="td_con1" align="center"><B><?=number_format($sumcouprice)?>원</B></TD>
					<!--
					<TD class="td_con1" align="center"><B><?=number_format($sumprice+$sumdeliprice-($sumreserve-$sumcouprice))?>원</B></TD>
					-->
					<TD class="td_con1" align="center"><B><?=number_format($sumadjust)?>원</B></TD>
				</TR>
				<TR>
					<TD colspan="<?= $t_col ?>" background="images/table_con_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr><tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<!--
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				-->
				<TR>
					<TD colspan="5" background="images/manual_bg.gif"></TD>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" class="menual_bg" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td height="10"></td>
					</tr>
					<tr>
						<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">정산기준 안내</span></td>
					</tr>
					<tr>
						<td  class="space_top"><span class="font_orange" style="padding-left:13px">업체별 정산기준일(정산산정기간)에 해당하는  배송완료 매출 건에 한해 정산금액으로 산출합니다.</span><br/>
						<span style="padding-left:13px">정산금액산출 데이타는 매일 거래된 배송완료 건 기준 적용하여 정산기준일 분을 합산처리하며, 주문취소, 환불 건의 발생 시 정산일 이전 건의 경우 자동으로 산출되며 정산일 이후 건은 차기 정산금액에서 제외하고 산출합니다.<br/>
						<span style="padding-left:13px">정산조회일이 정산산정기간 중(결산일 이전)일 경우 정산상태는 “정산진행중”으로 표기됩니다.<br/>
						<span style="padding-left:13px"><b>예) A사의 정산일이 15일이고 정산기준일이 1일~10일 경우</b><br/>
						<span style="padding-left:13px"><b>운영자가 10일날</b> 정산금액 조회 시  “정산진행중”으로 표시되고, 11일 00시 이후 조회 시 정산시스템에서 금액산출이 완료되며 정산상태가 “정산대기중”으로 표시됩니다.

						</td>
					</tr>
					<tr><td height="5"></td></tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="40"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372" align="left"><img src="images/icon_8a.gif" width="13" height="13" border="0">
					<B>정렬방법:
					<select name=orderby onchange="GoOrderby(this.options[this.selectedIndex].value)" class="select">
					<option value="deli_date" <?if($orderby=="deli_date")echo"selected";?>>구입결정일</option>
					<option value="ordercode" <?if($orderby=="ordercode")echo"selected";?>>주문코드</option>
					</select>
					</td>
					<td  align="right"><img src="images/icon_8a.gif" width="13" height="13" border="0">총 주문수 : <B><?=number_format($t_count)?></B>건&nbsp;&nbsp;<img src="images/icon_8a.gif" width="13" height="13" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup[list_num])?></b> 페이지&nbsp;<a href="javascript:goCalendar('<?= $vender ?>','<?$tempend[0] ?>','<?= $tempend[1] ?>');"><img src="images/btn_calendar.gif" align="absmiddle" border="0" alt="정산 캘린더 보기" /></a></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td width="100%">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=70></col> <!-- 입점업체 -->
				<col width=135></col> <!-- 배송일/주문코드 -->
				<col width=110></col> <!-- 주문일 -->
				<col width=></col> <!-- 상품명 -->

				<col width=55></col> <!-- 총 배송료 -->
				<col width=60></col> <!-- 쿠폰할인 -->
				<col width=70></col> <!-- 결제금액 -->
				<col width=100></col> <!-- 정산금액 -->
				<col width=100></col> <!-- 정산상태 -->
				<TR>
					<TD background="images/table_top_line.gif" colspan="13"></TD>
				</TR>
				<TR height="32">
					<TD class="table_cell5" align="center">입점업체</TD>
					<TD class="table_cell6" align="center">구입결정일/주문코드</TD>
					<TD class="table_cell6" align="center">주문일자</TD>
					<TD align="center" colspan="5">
						<table border=0 cellpadding=0 cellspacing=0 width='100%'>
							<col width=></col>
							<col width=30></col>
							<col width=66></col>
							<col width=106></col>
							<col width=61></col>
							<tr height="32">
							<TD class="table_cell6" align="center">상품명</TD>
							<TD class="table_cell6" align="center">수량</TD>
							<TD class="table_cell6" align="center">판매금액</TD>
							<TD class="table_cell6" align="center">수수료 -> 금액 
							<? if($shop_relay=="1") {?>
							<br/><span style="font-size:11px">(수수료의 부가세)
							<? } ?>
							</TD>
							<TD class="table_cell6" align="center">적립금</TD>
							</tr>
						</table>
					</td>
					<TD class="table_cell6" align="center">배송료</TD>
					<TD class="table_cell6" align="center">쿠폰할인</TD>
					<TD class="table_cell6" align="center">결제금액</TD>
					<TD class="table_cell6" align="center">정산금액</TD>
					<TD class="table_cell6" align="center">정산상태</TD>
				</TR>
				<TR>
					<TD colspan="13" background="images/table_con_line.gif"></TD>
				</TR>
<?
		if($t_count>0) {
			/*
			$sql ="SELECT SUM(IF((b.productcode!='99999999990X' AND NOT (b.productcode LIKE 'COU%')), b.price*b.quantity,NULL)) as sumprice, (select round(rate, 2) from tblvenderinfo where vender = b.vender) as rate, ";
			$sql.= "SUM(b.reserve*b.quantity) as sumreserve, ";
			$sql.= "SUM(IF(b.productcode='99999999990X', b.price,NULL)) as sumdeliprice, ";
			$sql.= "SUM(IF(b.productcode LIKE 'COU%', b.price,NULL)) as sumcouprice, ";
			$sql.= "a.ordercode,a.deli_date, b.vender FROM tblorderinfo a, tblorderproduct b ".$qry." ";
			$sql.="GROUP BY a.ordercode,b.vender ORDER BY a.".$orderby." DESC ";
			$sql.="LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
			*/

			if(strlen($vender)>0) {

				$sql ="SELECT SUM(IF((productcode!='99999999990X' AND NOT (productcode LIKE 'COU%')), price,NULL)) as sumprice, ";
				$sql.= "SUM(reserve) as sumreserve, ";
				$sql.= "SUM(deli_price) as sumdeliprice, ";
				$sql.= "SUM(cou_price) as sumcouprice, ";
				$sql.= "ordercode, deli_date, vender, sum(adjust) as sumadjust FROM `order_adjust_detail` ".$qry." ";
				$sql.="GROUP BY ordercode, vender ";

			}else{

				//오늘이 정산일인 업체 조회
				$sql = "";

				$i=0;
				while (isset($venders[$i])) {

					$t_vender = $venders[$i];

					$sql.="SELECT SUM(IF((productcode!='99999999990X' AND NOT (productcode LIKE 'COU%')), price,NULL)) as sumprice, ";
					$sql.= "SUM(reserve) as sumreserve, ";
					$sql.= "SUM(deli_price) as sumdeliprice, ";
					$sql.= "SUM(cou_price) as sumcouprice, ";
					$sql.= "ordercode, deli_date, vender, sum(adjust) as sumadjust ";
					$sql.= " FROM `order_adjust_detail` o ";					
					$sql .= getVenderOrderAdjustList($t_vender, $search_date, $s_check);
					$sql.="GROUP BY ordercode, vender ";
					
					if ($i<$end_i) {
						$sql .= "
							union
						";
					}

					$i++;
				}

				$sql = $h_sql.$sql.$b_sql;

			}

			$sql .= " ORDER BY vender, ".$orderby." DESC ";

			$result=mysql_query($sql,get_db_conn());
			
			$vender_rowspan= array();
			$now_vender = "";
			while($row=mysql_fetch_object($result)) {
				
				if ($now_vender != $row->vender) {
					$now_vender = $row->vender;
					$vender_rowspan[$now_vender]=1;
				}else{
					$vender_rowspan[$now_vender]++;
				}
			}
			$result=mysql_query($sql,get_db_conn());

			$i=0;

			$thisordcd="";
			$thiscolor="#FFFFFF";			
			
			$dd=0;
			$now_vender = "";
			$vender_adjust = 0;


			$colspan=13;

			while($row=mysql_fetch_object($result)) {

				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
				$date = substr($row->deli_date,0,4)."/".substr($row->deli_date,4,2)."/".substr($row->deli_date,6,2)." (".substr($row->deli_date,8,2).":".substr($row->deli_date,10,2).")";
				$orderdate = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";
				
				if($thisordcd!=$row->ordercode) {
					$thisordcd=$row->ordercode;
					if($thiscolor=="#FFFFFF") {
						$thiscolor="#FEF8ED";
					} else {
						$thiscolor="#FFFFFF";
					}
				}

				echo "<tr bgcolor=".$thiscolor." onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='".$thiscolor."'\">\n";
				
				$row_span = 0;
				if ((int) $now_vender != (int) $row->vender) {
					$row_span=($vender_rowspan[$row->vender] * 2)+1;

					echo "	<td rowspan=\"".$row_span."\"  class=\"td_con6\" align=center style=\"font-size:8pt\">".(strlen($venderlist[$row->vender]->vender)>0?"<B><a href=\"javascript:viewVenderInfo(".$row->vender.")\">".$venderlist[$row->vender]->id."</a></B>":"-")."</td>\n";

					$vender_adjust = 0;
					$dd=1;
					
					$now_vender=$row->vender;
				}else{

					//$vender_adjust = $vender_adjust + $row->sumadjust;
					$dd++;
				}


				echo "	<td class=\"td_con5\" align=center style=\"font-size:8pt;line-height:12pt\"><A HREF=\"javascript:OrderDetailView('".$row->ordercode."',".$row->vender.")\">".$date."<br>".$row->ordercode."</A></td>\n";
				echo "	<td class=\"td_con6\" align=center style=\"font-size:8pt;line-height:12pt\">".$orderdate."</td>";
				echo "	<td class=\"td_con6\" colspan=\"5\">\n";
				
				echo "	<table border=0 cellpadding=0 cellspacing=0 width='100%'>\n";
				echo "	<col width=></col>\n";
				echo "	<col width=1></col>\n";
				echo "	<col width=30></col>\n";
				echo "	<col width=1></col>\n";
				echo "	<col width=60></col>\n";
				echo "	<col width=1></col>\n";
				echo "	<col width=100></col>\n";
				echo "	<col width=1></col>\n";
				echo "	<col width=55></col>\n";

				$sql = "SELECT o.*,
						a.account_rule, a.rate, a.cost, a.status,
						a.relay, a.rate_price, a.surtax
						FROM tblorderproduct o right join order_adjust_detail a
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
					echo "	<td style=\"font-size:8pt;padding:3;line-height:11pt\"><a href=\"/front/productdetail.php?productcode=".$row2->productcode."\" target=\"_blank\">".$row2->productname."</a></td>\n";
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

				$adjust_btn = "";
				$status_value = "";
				if ($status_chk==0) {
					//$adjust_btn = "<br/><a href=\"javascript:adjustModify('".$row->ordercode."','".$row->vender."');\"><span style='color:ffffff;background-color:1F497D;padding:3px 5px;margin-top:5px;'>수정관리이력</span></a>";
					$adjust_btn = "<br/><a href=\"javascript:adjustModify('".$row->ordercode."','".$row->vender."');\"><img src=\"images/btn_history.gif\" border=\"0\" alt=\"수정관리이력\" /></a>";
					
					//오늘날짜보다 이후면 처리못함
					if ($after_chk=="1") {
						$status_value = "정산진행중";
					}else{
						$status_value = "정산대기중";
					}

					$vender_adjust = $vender_adjust + $row->sumadjust;

				}else{
					$status_value = "정산처리";
					$adjust_btn = "<br/><a href=\"javascript:adjustModify('".$row->ordercode."','".$row->vender."');\"><img src=\"images/btn_history.gif\" border=\"0\" alt=\"수정관리이력\" /></a>";
				}

				echo "	</table>\n";
				echo "	</td>\n";
				echo "	<td class=\"td_con6\" align=right style=\"font-size:8pt;padding:3\">".($row->sumdeliprice>0?"+":"").number_format($row->sumdeliprice)."&nbsp;</td>\n";
				echo "	<td class=\"td_con6\" align=right style=\"font-size:8pt;padding:3\">".number_format($row->sumcouprice)."&nbsp;</td>\n";
				echo "	<td class=\"td_con6\" align=right style=\"font-size:8pt;padding:3\"><B>".number_format($row->sumprice+$row->sumdeliprice-($row->sumreserve-$row->sumcouprice))."</B>&nbsp;</td>\n";
				echo "	<td class=\"td_con6\" align=right style=\"font-size:8pt;padding:3\"><B>".number_format($row->sumadjust)."</B>&nbsp;</td>\n";
				
				echo "	<td class=\"td_con6\" align=center style=\"font-size:8pt;padding:3;\"><B>".$status_value.$adjust_btn."</B></td>\n";
			
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<TD height=1 background=\"images/table_con_line.gif\" colspan=\"".($colspan-3)."\"></TD>\n";
				echo "</tr>\n";
				

				if ($dd==$vender_rowspan[$row->vender]) {
		
						$sql = "select * from order_account_new where vender=".$row->vender." and date='".$search_d."'";
						$result2=mysql_query($sql,get_db_conn());
						$row2=mysql_fetch_object($result2);

						if ($row2->confirm=="Y") {
							$s_value = "처리완료<br/><A HREF=\"javascript:detailView_toVender('".$row->vender."',".$search_d.")\">".substr($row2->reg_date, 0, 10)."</a>";
						}else if ($row2->confirm=="N") {
							$s_value = "지급완료<br/><A HREF=\"javascript:detailView_toVender('".$row->vender."',".$search_d.")\">".substr($row2->reg_date, 0, 10)."</a>";
						}else{
							$s_value = "정산대기<br/><A HREF=\"javascript:detailView_toVender('".$row->vender."',".$search_d.")\"><img src=\"images/btn_calculate.gif\" alt=\"완료처리\" /></a>";
						}
						
						//오늘날짜보다 이후면 처리못함
						if ($after_chk=="1") {
							$s_value = "<span style='color:red;'>정산진행중</span>";
						}
						
						mysql_free_result($result2);
		
					echo "<tr>";
					echo "	<TD height=2 background=\"images/table_con_line.gif\" colspan=\"12\"></TD>";
					echo "</tr>";
		
					echo "<tr  style='height:50px'>";
					echo "	<td class=\"td_con6\" align=right style=\"font-size:8pt;padding:3\" colspan=\"12\">";

					if ($vender_adjust != 0) {

						$year = substr($search_date, 0, 4);
						$month = substr($search_date, 5, 2);
						$day = substr($search_date, 8, 2);
					
						$adjust_array = getVenderOrderAdjust($row->vender, $year, $month, $day);
						$adjust = $adjust_array['adjust'];
						$ad_start = $adjust_array['start_date'];
						$ad_end = $adjust_array['end_date'];
						
						$ad_start = substr($ad_start, 0, 4)."-".substr($ad_start, 4, 2)."-".substr($ad_start, 6, 2);
						$ad_end = substr($ad_end, 0, 4)."-".substr($ad_end, 4, 2)."-".substr($ad_end, 6, 2);


						echo "<B>정산예정금 (".$ad_start." ~ ".$ad_end.") 합계 : ".number_format($vender_adjust)."</B>&nbsp;";
					}

					echo "</td>\n";
					echo "	<td class=\"td_con6\" align=center style=\"font-size:8pt;padding:3\"><B>".$s_value."</B>&nbsp;</td>\n";
					echo "</tr>";
					echo "<tr>";
					echo "	<TD height=2 background=\"images/table_con_line.gif\" colspan=\"13\"></TD>";
					echo "</tr>";
				}


				$i++;
			}
			mysql_free_result($result);
			$cnt=$i;

			if($i>0) {
				$total_block = intval($pagecount / $setup[page_num]);
				if (($pagecount % $setup[page_num]) > 0) {
					$total_block = $total_block + 1;
				}
				$total_block = $total_block - 1;
				if (ceil($t_count/$setup[list_num]) > 0) {
					// 이전	x개 출력하는 부분-시작
					$a_first_block = "";
					if ($nowblock > 0) {
						$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=/images/minishop/btn_miniprev_end.gif border=0 align=absmiddle></a> ";
						$prev_page_exists = true;
					}
					$a_prev_page = "";
					if ($nowblock > 0) {
						$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><img src=/images/minishop/btn_miniprev.gif border=0 align=absmiddle></a> ";

						$a_prev_page = $a_first_block.$a_prev_page;
					}
					if (intval($total_block) <> intval($nowblock)) {
						$print_page = "";
						for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
							if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
								$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
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
								$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
							} else {
								$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
							}
						}
					}
					$a_last_block = "";
					if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
						$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
						$last_gotopage = ceil($t_count/$setup[list_num]);
						$a_last_block .= " <a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=/images/minishop/btn_mininext_end.gif border=0 align=absmiddle></a>";
						$next_page_exists = true;
					}
					$a_next_page = "";
					if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
						$a_next_page .= " <a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><img src=/images/minishop/btn_mininext.gif border=0 align=absmiddle></a>";
						$a_next_page = $a_next_page.$a_last_block;
					}
				} else {
					$print_page = "<B>[1]</B>";
				}
				$pageing=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
			}
		} else {
			echo "<tr height=28 bgcolor=#FFFFFF><td colspan=13 align=center>조회된 내용이 없습니다.</td></tr>\n";
		}
?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="13"></TD>
				</TR>
				</table>
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr>
					<td align=center style="padding-top:10"><?=$pageing?></td>
				</tr>
				</table>
				</td>
			</tr>
			<form name=detailform method="post" action="vender_orderdetail.php" target="vorderdetail">
			<input type=hidden name=ordercode>
			<input type=hidden name=vender>
			</form>

			<form name=pageForm method=post action="<?=$_SERVER[PHP_SELF]?>">
			<input type=hidden name=vender value="<?=$vender?>">
			
			<input type=hidden name=search_date value="<?=$search_date?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=orderby value="<?=$orderby?>">
			<input type=hidden name=block>
			<input type=hidden name=gotopage>
			</form>

			<form name=vForm action="vender_infopop.php" method=post>
			<input type=hidden name=vender>
			</form>
			
			<form name=dForm method=post>
			<input type=hidden name=vender>
			<input type=hidden name=date>
			</form>

			<form name=aForm method=post>
			<input type=hidden name=ordercode>
			<input type=hidden name=search_date value="<?= $search_date ?>">
			<input type=hidden name=vender>
			</form>

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" class="menual_bg" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">입점업체 정산관리</span></td>
					</tr>
					<tr>
						<td  class="space_top"><span style="padding-left:13px">- 입점업체별 주문건에 대한 정산내역을 확인할 수 있습니다.</td>
					</tr>
					<tr>
						<td  class="space_top"><span style="padding-left:13px">- 정렬방식 : 주문코드/구입결정일  선택할 수 있습니다.</td>
					</tr>
					<tr><td height="20"></td></tr>

					<tr>
						<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">용어정리</span></td>
					</tr>
					<tr>
						<td  class="space_top"><span style="padding-left:13px">- 정산금액 : 설정한 정산산정기간 동안의 입점업체 배송완료상품의 총 매출에서 판매수수료, 입점사지급 적립금, 쿠폰혜택을 빼고 배송료를 더한 금액을 산출한 실 결제금액<br/>
						<span style="padding-left:13px">- 정산기준일 : 거래된 매출 중 정산금액이 산정되는 기간<br/>
						<span style="padding-left:13px">- 결산일 : 정산기준일의 마지막 날짜(마감일)<br/>
						<span style="padding-left:13px">- 정산일 : 정산기준일의 정산금액을 입점업체에게 결제(입금)하는 날짜<br/>
						<span style="padding-left:13px">- 정산조회일 : 정산금액을 조회하는 날짜
						</td>
					</tr>
					<tr><td height="20"></td></tr>

					<tr>
						<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">정산산출 예) </span></td>
					</tr>
					<tr>
						<td  class="space_top">
						<span style="padding-left:13px">* A업체가 정산일이 매월10일 1회 이고, 결산일이 정산일로 부터 5일 이전인 경우<br/>
						<span style="padding-left:13px">- 정산기준일 : 이전달 6일 ~ 이번달5일<br/>
						<span style="padding-left:13px">- 결산일 : 매달 5일
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">복수정산 응용산출 예) </span></td>
					</tr>
					<tr>
						<td  class="space_top">
						<span style="padding-left:13px">* B업체가 정산일이 매월 5일, 10일, 15일, 20일, 25일, 30일  6회 이고, 결산일이 정산일로 부터 5일 이전인 경우<br/>
						<span style="padding-left:13px">- 정산기준일 : 지난달 26일~지난달 말일(5일 정산), 이번달 1일~이번달 5일(10일 정산), 6일~10일(15일 정산), 11일~15일(20일 정산), 16일~20일(25일 정산), 21일~ 25일(30일 정산)<br/>
						<span style="padding-left:13px">- 결산일 : 매달 말일, 5일, 10일, 15일, 20일, 25일
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">정산계산식</span></td>
					</tr>
					<tr>
						<td style="padding-left:13px;" class="menual_con">
						<b>1. 수수료 기준 운영</b>
							<div class="font_blue" style="padding-left:13px">
							1)상품판매 중계업체가 아닌 ① 경우 정산금액=상품판매금액-수수료금액+배송비-적립금-쿠폰할인/적립 <br/>
							2)상품판매 중개업체인 ② 경우 정산금액=상품판매금액-수수료금액-수수료의 부가세+배송비-적립금-쿠폰할인/적립
							</div>
						<b>2. 상품공급가 기준 운영</b>
							<div class="font_blue" style="padding-left:13px">
							1) 상품판매 중계업체가 아닌 ① 의 경우 정산금액= 판매상품 전체 공급가격+배송비-적립금-쿠폰할인/적립 <br/>
							2) 상품판매 중계업체인 ② 의 경우 정산금액=판매상품 전체 공급가격-(상품판매금액-상품공급가격)*0.1+배송비-적립금-쿠폰할인/적립 <br/>
								<span style="padding-left:13px">* (상품판매금액-상품공급가격)*0.1은 수수료의 부가세입니다.</span>
							</div>
							* 수수료 금액 = 판매금액x수수료율 <br/>
							* 적립금 및 쿠폰의 경우 발행 주체가 부담하는것을 원칭으로 합니다. <br/>
							* 회원등급별 혜택 등 기타 혜택은 판매운영사(본사)가 부담하는것을 원칙으로 합니다. <br/>
							* 배송료의 경우 입점사 설정한 배송정책을 따릅니다.
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">세금계산서 처리</span></td>
					</tr>
					<tr>
						<td style="padding-left:13px;" class="menual_con">
						* 상품판매 중계업체가 아닌 경우 전체 정산금액 기준 매입세금계산서를 입점사로부터 받음 <br/>
						* 상품판매 중계업체인 경우 상품판매수수료에 대한 부가세를 공제 후 정산하고 판매수수료에 대한 매출세금계산서를 입점사에게 발송, 입점사는 전체 판매금액에 대해 구매자에게 세금계산서 발송<br/>
						* 상품판매중개업체로 운영하는 경우는 과세 및 비과세(면세) 상품에 상관없이 모든 상품판매수수료의 부가세를 공제 후 정산처리됩니다.<br/>
                          <span style="padding-left:13px">- 판매중개업은 일반과세업종으로 중개 수수료의 부가세 납부의무가 있습니다.(*관련 구체적은 문의사항는 관할 세무서로 문의바랍니다.)
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</table>
</td>
        <td width="16" background="images/con_t_02_bg.gif"></td>
    </tr>
    <tr>
        <td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_04_bg.gif"></td>
        <td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr><td height="20"></td></tr>
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

<?=$onload?>

<? INCLUDE "copyright.php"; ?>