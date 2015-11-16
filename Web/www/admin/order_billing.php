<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/ext/func.php");
include_once($Dir."lib/hiworks/bill.class.php");
INCLUDE ("access.php");



####################### 페이지 접근권한 check ###############
$PageCode = "or-1";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$bill = new Bill();
if(!_empty($bill->errmsg)){
	_alert($bill->errmsg,'order_billinfo.php',false);
	exit;
}

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 20;

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
$qry = "";


$datecul = ($_REQUEST['date_type']=="dtype1")?'ordercode':'regdate';

$where = array();
if(!_empty($_REQUEST['search'])){
	switch($_REQUEST['search']){
		case 'ordercode':
		case 'memid':
			array_push($where,'b.'.$_REQUEST['s_check']." like '%".$_REQUEST['search']."%'");
			break;
		case 'companyname':
		case 'companynum':
			array_push($where,'c.'.$_REQUEST['s_check']." like '%".$_REQUEST['search']."%'");
			break;
	}
}

if(preg_match('/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/',$_REQUEST['search_start'],$mat)){
	if($_REQUEST['date_type']=="dtype2"){
		array_push($where,"b.ordercode>='".sprintf('%04d%02d%02d',intval($mat[1]),intval($mat[2]),intval($mat[3]))."'");
	}else{
		array_push($where,"b.regdate>='".sprintf('%04d-%02d-%02d',intval($mat[1]),intval($mat[2]),intval($mat[3]))."'");
	}
}


if(preg_match('/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/',$_REQUEST['search_end'],$mat)){
	if($_REQUEST['date_type']=="dtype2"){
		array_push($where,"b.ordercode <='".sprintf('%04d%02d%02d',intval($mat[1]),intval($mat[2]),intval($mat[3])+1)."'");
	}else{
		array_push($where,"b.regdate <='".sprintf('%04d-%02d-%02d',intval($mat[1]),intval($mat[2]),intval($mat[3]))."'");
	}
}
if(_array($where)) $qry = ' and '.implode(' and ',$where);
	
$sql = "SELECT COUNT(*) as t_count FROM bill_basic b inner join bill_company c using(bill_idx)  WHERE 1=1 ".$qry;

$result = mysql_query($sql,get_db_conn());
$row = mysql_fetch_object($result);
$t_count = (int)$row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;


/*
$sql = "SELECT * FROM tblorderbill ";
$sql.= "WHERE 1=1 ".$qry;*/
$sql = "SELECT * FROM bill_basic b inner join bill_company c using(bill_idx) inner join bill_document d using(bill_idx) ";
$sql.= "WHERE 1=1 ".$qry;
$sql.= "ORDER BY bill_idx desc ";
$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
//echo $sql; exit;
$result = mysql_query($sql,get_db_conn());

$items = array();

$docids = array();
$docstatus = array();

while($item=mysql_fetch_assoc($result)){
	$item['vno'] = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
	if(!_empty($item['document_id'])){		
		array_push($docids,$item['document_id']);
	}
	array_push($items,$item);
}
mysql_free_result($result);
if(_array($docids)) $docstatus = $bill->_checkDocumentStatus($docids);
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script>
function viewBill(bidx){
	document.billform.b_idx.value= bidx;
	//window.open("","winBill","scrollbars=yes,width=700,height=600");
	window.open("","winBill");
	document.billform.submit();
}
function sendBill(b_id){
	document.frmHiWork.bill_idx.value= b_id;
	document.frmHiWork.submit();
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
			<? include ("menu_order.php"); ?>
			</td>

			<td></td>
			<td valign="top">

<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 주문/매출 &gt; 주문조회 및 배송관리 &gt; <span class="2depth_select">전자세금계산서 발행 관리</span></td>
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
					<TD><IMG SRC="images/order_billing_title.gif" border="0"></TD>
				</TR>
				<TR>
					<TD width="100%" background="images/title_bg.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD width="100%" background="images/title_bg.gif">&nbsp;</TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"><IMG SRC="images/distribute_04.gif" ></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue"><p>쇼핑몰에서의 주문서에 대한 전자세금계산서 신청/발급내역을 확인 할 수 있습니다.</p></TD>
					<TD background="images/distribute_07.gif"><IMG SRC="images/distribute_07.gif" ></TD>
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
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/order_billing_stitle1.gif" ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" bgcolor="#ededed" style="padding:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">신청일자</TD>
							<TD class="td_con1" width="613">
							<select name="date_type" class="select">
							<option value="dtype1" <?if($date_type=="dtype1")echo"selected";?>>주문일</option>
							<option value="dtype2" <?if($date_type=="dtype2")echo"selected";?>>신청일</option>
							</select>
							<input type=text name=search_start value="<?=$search_start?>" size=13 onfocus="this.blur();" OnClick="Calendar(this)"  class="input_selected"> ~ <input type=text name=search_end value="<?=$search_end?>" size=13 onfocus="this.blur();" OnClick="Calendar(this)"  class="input_selected">
							</TD>
						</TR>
						<TR>
							<TD colspan="2" background="images/table_con_line.gif"></TD>
						</TR>
						<tr>
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">검색구분</TD>
							<TD class="td_con1" width="613"><select name="s_check" class="select">
							<option value="ordercode" <?if($s_check=="ordercode")echo"selected";?>>주문번호</option>
							<option value="companyname" <?if($s_check=="companyname")echo"selected";?>>상호</option>
							<option value="companynum" <?if($s_check=="companynum")echo"selected";?>>사업자번호</option>
							<option value="memid" <?if($s_check=="memid")echo"selected";?>>아이디</option>
							<option value="memname" <?if($s_check=="memname")echo"selected";?>>이름</option>
							</select>
							<input type=text name=search value="<?=$search?>" style="width:197" class="input"></TD>
						</tr>
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
				<td style="padding-top:4pt;" align="right"><input type="image" src="images/botteon_search.gif" ></td>
			</tr>
			</form>
			<tr>
				<td height=30></td>
			</tr>
			<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<tr>
				<td>
				<a href="javascript:loginHiworks()"><img src="images/btn_hiworks.gif" border="0" alt="하이웍스 로그인" /></a>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" width="761" colspan="8">
					</TD>
				</TR>
				<TR>
					<TD class="table_cell" width="30"><p align="center">번호</p></TD>
					<TD class="table_cell1" width="160"><p align="center">신청일</p></TD>
					<TD class="table_cell1" width=""><p align="center">주문번호</p></TD>
					<TD class="table_cell1" width="100"><p align="center">신청자ID</p></TD>
					<TD class="table_cell1" width="100"><p align="center">발행금액</p></TD>
					<TD class="table_cell1" width="120"><p align="center">사업자번호</p></TD>
					<TD class="table_cell1" width="100;"><p align="center">상태</p></TD>
					<TD class="table_cell1" width="100"><p align="center">발급</p></TD>
				</TR>
				<TR>
					<TD colspan="8" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
				</TR>
<?			if(count($items) < 1){ ?>
				<tr><td class="td_con2" colspan="8" align="center">검색된 내용이 없습니다.</td></tr>
<?			}else{
				foreach($items as $item){
					$viewid = (substr($item['ordercode'],-1,1) == "X" && strlen(trim($item['memid'])) == 0 )?"비회원":$item['memid'];
?>
				<TR>
					<TD class="td_con2"><p align="center"><?=$item['vno']?></p></TD>
					<TD class="td_con1"><p align="center"><?=$item['regdate']?></p></TD>
					<TD class="td_con1"><p align="center"><a href="javascript:viewBill('<?=$item['bill_idx']?>')"><?=$item['ordercode']?></a></p></TD>
					<TD class="td_con1"><p align="center"><?=$viewid?></p></TD>
					<TD class="td_con1"><p align="right"><?=number_format($item['supplyprice'] + $item['tax'])?></p></TD>
					<TD class="td_con1"><p align="center"><?=$item['r_number']?></p></TD>
					<TD class="td_con1"><p align="center">
					 <? if(_empty($item['document_id'])) echo $bill->_reqStatus($item['status']); 
					 	else{							
							if(_array($docstatus[$item['document_id']])){							
								$tmp = &$docstatus[$item['document_id']];
								if(_isInt($tmp['0'])){ // 국세청 연동
									switch($tmp['0']){
										case '5': echo '국세청전송실패'; break;
										case '4': echo '국세청전송완료'; break;
										default: echo '국세청전송중'; break;
									}								
								}else{
									echo $bill->_docStatus($tmp[0]); 
								}
							}
						}
					 ?>
					 </p>
					</TD>
					<TD class="td_con1"><p align="center">
						<? if(_empty($item['document_id'])){ ?>
							<img src="images/icon_cupon_bal.gif" alt="발급" style="cursor:pointer" onclick="sendBill('<?=$item['bill_idx']?>')" />
						<? }else{ echo '발행일 : '.$item['issue_date'].'<br>문서번호 : '.$item['document_id'];    } ?></p>
					</TD>
				</TR>
				<TR>
					<TD colspan="8" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
				</TR>
<?				}
			}		
?>
				<TR>
					<TD background="images/table_top_line.gif" width="761" colspan="8"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td><p>&nbsp;</p></td>
			</tr>
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
<?
		$total_block = intval($pagecount / $setup[page_num]);

		if (($pagecount % $setup[page_num]) > 0) {
			$total_block = $total_block + 1;
		}

		$total_block = $total_block - 1;

		if (ceil($t_count/$setup[list_num]) > 0) {
			// 이전	x개 출력하는 부분-시작
			$a_first_block = "";
			if ($nowblock > 0) {
				$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><IMG src=\"images/icon_first.gif\" border=0 align=\"absmiddle\"></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\">[prev]</a>&nbsp;&nbsp;";

				$a_prev_page = $a_first_block.$a_prev_page;
			}

			// 일반 블럭에서의 페이지 표시부분-시작

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
			}		// 마지막 블럭에서의 표시부분-끝


			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
				$last_gotopage = ceil($t_count/$setup[list_num]);

				$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><IMG src=\"images/icon_last.gif\" border=0 align=\"absmiddle\" width=\"17\" height=\"14\"></a>";

				$next_page_exists = true;
			}

			// 다음 10개 처리부분...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

				$a_next_page = $a_next_page.$a_last_block;
			}
		} else {
			$print_page = "<B>[1]</B>";
		}
		echo "<tr>\n";
		echo "	<td width=\"100%\" class=\"font_size\"><p align=\"center\">\n";
		echo "		".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
		echo "	</td>\n";
		echo "</tr>\n";
?>
				</table>
				</td>
			</tr>
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
							<TD background="images/manual_left1.gif"><IMG SRC="images/manual_left1.gif" WIDTH=15 HEIGHT="5" ALT=""></TD>
							<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
										<td width="701"><span class="font_dotline">전자세금계산서 발행 신청</span></td>
									</tr>
									<tr>
										<td width="20" align="right">&nbsp;</td>
										<td width="701" class="space_top" style="letter-spacing:-0.5pt;">
											<p>- 세금계산서 발행을 신청한 내역을 볼 수 있습니다.<br />
											- 실제 전자세금계산서 발행을 하이웍스 홈페이지를 통해 발행하실 수 있습니다.</p>
										</td>
									</tr>
								</table>
							</TD>
							<TD background="images/manual_right1.gif"><IMG SRC="images/manual_right1.gif" WIDTH=18 HEIGHT="2" ALT=""></TD>
						</TR>
						<TR>
							<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
							<TD COLSPAN=3 background="images/manual_down.gif"><IMG SRC="images/manual_down.gif" WIDTH="4" HEIGHT=8 ALT=""></TD>
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
<form name=billform method=post action="bill/view.php" target="winBill">
<input type=hidden name=b_idx value="">
</form>

<form method="post" name="frmHiWork" action="bill/process.php">
<input type=hidden name="bill_idx" value="">
</form>

<form method="post" name="frmLogin" action="http://billapi.hiworks.co.kr/auto_login.php">
<input type=hidden name=domain value="<?=$bill->config['domain']?>">
<input type=hidden name=license_id value="<?=$bill->config['license_id']?>">
<input type=hidden name=license_no value="<?=$bill->config['license_no']?>">
<input type=hidden name=pType value="BILL">
</form>


<IFRAME id="_hiddenFrame" name="_hiddenFrame" style="width:0;height:0; position:absolute; visibility:hidden;"></IFRAME>
<script type="text/javascript">
function loginHiworks(){
	window.open("about:blank","hiworks");
	document.frmLogin.target="hiworks";
	document.frmLogin.submit();
}

</script>
<?=$onload?>

<? INCLUDE "copyright.php"; ?>