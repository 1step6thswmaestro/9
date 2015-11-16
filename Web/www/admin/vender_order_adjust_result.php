<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "vd-3";
$MenuCode = "vender";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",mktime(0,0,0,date("m")-1,date("d"),date("Y")));

$orderby=$_POST["orderby"];
if($orderby!="deli_date" && $orderby!="ordercode") $orderby="deli_date";

$vender=$_POST["vender"];
$s_check=$_POST["s_check"];
$search=$_POST["search"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];

$search_start=$search_start?$search_start:$period[1];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start):str_replace("-","",$period[1]);
$search_e=$search_end?str_replace("-","",$search_end):date("Y-m-d",$CurrentTime);

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (mktime(0,0,0,$tempend[1],$tempend[2],$tempend[0])-mktime(0,0,0,$tempstart[1],$tempstart[2],$tempstart[0]))/86400;
if ($termday>366) {
	echo "<script>alert('검색기간은 1년을 초과할 수 없습니다.');location='".$_SERVER[PHP_SELF]."';</script>";
	exit;
}


$qry .= "where date between '".$search_s."' and '".$search_e."' ";
$qry_2 .= "where com_date between '".$search_s."' and '".$search_e."' ";
if(strlen($vender)>0) {
	$qry.= " AND o.vender='".$vender."' ";
	$qry_2.= " AND vender='".$vender."' ";
} else {
	$qry.= " AND o.vender>0 ";
	$qry_2 .= " AND vender>0 ";
}
if(strlen($s_check)>0) {
	$qry.= " AND o.confirm='".$s_check."' ";
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

$sql = "select ifnull(count(*),0) as t_count from order_account_new o ".$qry;

$result = mysql_query($sql,get_db_conn());
while($row = mysql_fetch_object($result)) {
	$t_count+=$row->t_count;
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

function goOrderList(vender, date) {

	document.oForm.vender.value=vender;
	document.oForm.search_date.value=date;
	document.oForm.submit();

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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 주문/정산 관리 &gt; <span class="2depth_select">입점업체 정산내역</span></td>
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
					<TD width="100%" class="notice_blue">입점업체별 정산내역을 확인할 수 있습니다.</TD>
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
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">정산일자</TD>
							<TD class="td_con1" >
								<input type=text name=search_start value="<?=$search_start?>" size=13 onfocus="this.blur();" OnClick="Calendar(this)" class="input_selected"> ~ <input type=text name=search_end value="<?=$search_end?>" size=13 onfocus="this.blur();" OnClick="Calendar(this)" class="input_selected"> 
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">								
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
								<option value="N" <?if($s_check=="N")echo"selected";?>>지급완료</option>
								<option value="Y" <?if($s_check=="Y")echo"selected";?>>처리완료</option>
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
				<td style="padding-top:10pt;" align="center">
				<!--<a href="javascript:venderexcelForm();"><img src="images/btn_excel1.gif" width="127" height="38" border="0" hspace="1"></a>
				--><a href="javascript:searchForm();"><img src="images/botteon_search.gif" width="113" height="38" border="0"></a></td>
			</tr>
			</form>

			<tr>
				<td height="40"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372" align="left">

					</td>
					<td  align="right"><img src="images/icon_8a.gif" width="13" height="13" border="0">총 정산수 : <B><?=number_format($t_count)?></B>건&nbsp;&nbsp;<img src="images/icon_8a.gif" width="13" height="13" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup[list_num])?></b> 페이지
					<a href="javascript:goCalendar('<?= $vender ?>','<?$tempend[0] ?>','<?= $tempend[1] ?>');"><img src="images/btn_calendar.gif" align="absmiddle" border="0" alt="정산 캘린더 보기" />
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td width="100%">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 >
				<col width=></col> <!-- 배송일/주문코드 -->
				<col width=></col> <!-- 주문일 -->
				<col width=></col> <!-- 입점업체 -->
				<col width=></col> <!-- 상품명 -->
				<col width=></col> <!-- 수량 -->
				<col width=></col> <!-- 판매금액 -->
				<col width=></col> <!-- 적립금 -->
				<col width=></col> <!-- 총 배송료 -->
				<col width=></col> <!-- 쿠폰할인 -->
				<col width=></col> <!-- 쿠폰할인 -->
				<col width=></col> <!-- 총 금액 -->
				<col width=></col> <!-- 총 금액 -->
				<TR>
					<TD background="images/table_top_line.gif" colspan="12"></TD>
				</TR>
				<TR height="32">
					<TD class="table_cell5" align="center">정산일자</TD>
					<TD class="table_cell6" align="center">입점업체</TD>
					<TD class="table_cell6" align="center">결제비용</TD>
					<TD class="table_cell6" align="center">수수료</TD>
					<TD class="table_cell6" align="center">배송료</TD>
					<TD class="table_cell6" align="center">적립금</TD>
					<TD class="table_cell6" align="center">쿠폰할인</TD>
					<TD class="table_cell6" align="center">실결제</TD>
					<TD class="table_cell6" align="center">정산금액</TD>
					<TD class="table_cell6" align="center">정산상태</TD>
					<TD class="table_cell6" align="center">최종수정일</TD>
				</TR>
				<TR>
					<TD colspan="12" background="images/table_con_line.gif"></TD>
				</TR>
<?
		$colspan=12;
		if($t_count>0) {
			$sql ="select o.*, sum_price, sum_deli_price, sum_reserve, sum_cou_price, sum_adjust from order_account_new o
					left join 
				(select vender,
				ifnull(sum(price), 0) as sum_price,
				ifnull(sum(deli_price), 0) as sum_deli_price,
				ifnull(sum(reserve), 0) as sum_reserve,
				ifnull(sum(cou_price), 0) as sum_cou_price,
				ifnull(sum(adjust), 0) as sum_adjust,
				com_date from order_adjust_detail ".$qry_2."
				group by vender, com_date) d
				on o.vender=d.vender and o.date=d.com_date
				".$qry."
				order by o.vender, date ";
			$sql.="LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
			
			$result=mysql_query($sql,get_db_conn());
			$i=0;
			$thisordcd="";
			$thiscolor="#FFFFFF";
			while($row=mysql_fetch_object($result)) {
				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
				
				if($thisordcd!=$row->ordercode) {
					$thisordcd=$row->ordercode;
					if($thiscolor=="#FFFFFF") {
						$thiscolor="#FEF8ED";
					} else {
						$thiscolor="#FFFFFF";
					}
				}
				$total_price = $row->sum_price + $row->sum_deli_price - $row->sum_reserve - $row->sum_cou_price;
				$rate = $row->sum_price-$row->sum_adjust + $row->sum_deli_price - $row->sum_reserve - $row->sum_cou_price;
				
				$s_value = "";
				if ($row->confirm=="Y") {
					$s_value = "처리완료";
				}else{
					$s_value = "지급완료";
				}

				$date = substr($row->date,0,4)."-".substr($row->date,4,2)."-".substr($row->date,6,2);


				echo "<tr bgcolor=".$thiscolor." onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='".$thiscolor."'\">\n";

				echo "	<td class=\"td_con5\" align=center style=\"font-size:8pt;line-height:12pt\"><a href=\"javascript:goOrderList('".$row->vender."', '".$date."')\">".$date."</a></td>\n";
				echo "	<td class=\"td_con6\" align=center style=\"font-size:8pt\">".(strlen($venderlist[$row->vender]->vender)>0?"<B><a href=\"javascript:viewVenderInfo(".$row->vender.")\">".$venderlist[$row->vender]->id."</a></B>":"-")."</td>\n";

				echo "	<td class=\"td_con6\" align=right style=\"font-size:8pt;padding:3\"><b>".number_format($row->sum_price)."&nbsp;<b/></td>\n";
				echo "	<td class=\"td_con6\" align=right style=\"font-size:8pt;padding:3\"><b>".number_format($rate)."&nbsp;<b/></td>\n";
				echo "	<td class=\"td_con6\" align=right style=\"font-size:8pt;padding:3\">".number_format($row->sum_deli_price)."&nbsp;</td>\n";
				echo "	<td class=\"td_con6\" align=right style=\"font-size:8pt;padding:3\">".number_format($row->sum_reserve)."&nbsp;</td>\n";
				echo "	<td class=\"td_con6\" align=right style=\"font-size:8pt;padding:3\">".number_format($row->sum_cou_price)."&nbsp;</td>\n";
				echo "	<td class=\"td_con6\" align=right style=\"font-size:8pt;padding:3\">".number_format($total_price)."&nbsp;</td>\n";
				echo "	<td class=\"td_con6\" align=right style=\"font-size:8pt;padding:3\"><b>".number_format($row->sum_adjust)."&nbsp;<b/></td>\n";
				

				echo "	<td class=\"td_con6\" align=center style=\"font-size:8pt;line-height:12pt\"><A HREF=\"javascript:detailView_toVender('".$row->vender."',".$row->date.")\">".$s_value."</a></td>\n";
				echo "	<td class=\"td_con6\" align=center style=\"font-size:8pt;line-height:12pt\">".$row->reg_date."</td>\n";

				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<TD height=1 background=\"images/table_con_line.gif\" colspan=\"".$colspan."\"></TD>\n";
				echo "</tr>\n";
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
			echo "<tr height=28 bgcolor=#FFFFFF><td colspan=".$colspan." align=center>조회된 내용이 없습니다.</td></tr>\n";
		}
?>
						<TR>
							<TD background="images/table_top_line.gif" colspan="12"></TD>
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
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=orderby value="<?=$orderby?>">
			<input type=hidden name=block>
			<input type=hidden name=gotopage>
			</form>

			<form name=oForm method="post" action="vender_orderadjust.php">
			<input type=hidden name=vender>
			<input type=hidden name=search_date>
			</form>


			<form name=dForm method=post>
			<input type=hidden name=vender>
			<input type=hidden name=date>
			</form>

			<tr><td height="20"></td></tr>
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
						<td  class="space_top">- 입점업체별 주문건에 대한 정산내역을 확인할 수 있습니다.</td>
					</tr>
					<tr>
						<td  class="space_top">- 정렬방식 : 주문코드/구입결정일  선택할 수 있습니다.</td>
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
							*수수료 금액 = 판매금액x수수료율 <br/>
							*적립금 및 쿠폰의 경우 발행 주체가 부담하는것을 원칭으로 합니다. <br/>
							*회원등급별 혜택 등 기타 혜택은 판매운영사(본사)가 부담하는것을 원칙으로 합니다. <br/>
							*배송료의 경우 입점사 설정한 배송정책을 따릅니다.
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">세금계산서 처리</span></td>
					</tr>
					<tr>
						<td style="padding-left:13px;" class="menual_con">
						①의 경우 전체 정산금액 기준 매입세금계산서를 입점사로부터 받음 <br/>
						②의 경우 상품판매수수료에 대한 부가세를 공제 후 정산하고 판매수수료에 대한 매출세금계산서를 입점사에게 발송, 입점사는 전체 판매금액에 대해 구매자에게 세금계산서 발송
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