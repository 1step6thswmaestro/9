<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");


$csRowType = array("10"=>"맞교환출고","11"=>"상품변경 맞교환출고","12"=>"누락재발송","13"=>"서비스발송","21"=>"반품접수","31"=>"출고시유의사항");



// 검색

$WHERE = "";

// 상태 검색
switch ( $_POST['srchING'] ) {
	case "all" : // 전체
		$WHERE .= " ";
		break;
	case "end" : // 처리완료
		$WHERE .= " AND `completeRegDate` > 0";
		break;
	case "ing" : // 미처리
		$WHERE .= " AND `completeRegDate` = 0";
		break;
	default : // 기본 - 미처리
		$WHERE .= " AND `completeRegDate` = 0";
		break;
}

// 주문코드 검색
if( $_POST['srchOrder'] ) {
	$_POST['srchOrder'] = str_replace(" ","",$_POST['srchOrder']);
	$WHERE .= " AND `order` = '".$_POST['srchOrder']."'";
}


// 상품코드 검색
if( $_POST['srchProduct'] ) {
	$_POST['srchProduct'] = str_replace(" ","",$_POST['srchProduct']);
	$WHERE .= " AND `product` = '".$_POST['srchProduct']."'";
}


// 상품코드 검색
if( $_POST['srchType'] ) {
	$WHERE .= " AND `type` = '".$_POST['srchType']."'";
}


// 업체처리완료 건 제외
if( $_POST['venderEnd'] ) {
	$WHERE .= " AND `venderRegDate` = 0 ";
}

// 반품 건 제외
if( $_POST['excludeType21'] ) {
	$WHERE .= " AND `type` != '21' ";
}

// 장기간(3개월) 미처리건 제외
if( $_POST['old'] ) {
	$WHERE .= " AND `adminRegDate` > '".date("YmdHis",time()-2592000)."'";
}

$ORDER_BY .= " ORDER BY `idx` DESC";



$setup[page_num] = 10;
$setup[list_num] = 50;

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

$sql = "SELECT * FROM `tbl_csManager` WHERE 1 ".$WHERE;
$result = mysql_query($sql,get_db_conn());
$t_count = mysql_num_rows($result);
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
					<td><img src="images/order_cs_title.gif" alt="상품 CS 관리"></td>
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
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">입점사에서 등록한 상품에 대해서만 반품/교환처리를 확인할 수 있습니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">주문번호 클릭시 주문상품에 대한 상세정보를 확인할 수 있습니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">제목 클릭시 반품/교환 접수에 대한 상세정보를 확인할 수 있습니다.</td>
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
									&nbsp;<U>상태</U>&nbsp;
									<select name="srchING">
										<option value="all" <?=( $_POST['srchING']=="all" )?"selected":""?>>전체</option>
										<option value="ing" <?=( $_POST['srchING']=="ing" OR $_POST['srchING']=="" )?"selected":""?>>미처리</option>
										<option value="end" <?=( $_POST['srchING']=="end" )?"selected":""?>>처리완료</option>
									</select>

									&nbsp;<U>주문코드</U>&nbsp;
									<input type="text" name="srchOrder" value="<?=$_POST['srchOrder']?>" style="width:150px;">

									&nbsp;<U>상품코드</U>&nbsp;
									<input type="text" name="srchProduct" value="<?=$_POST['srchProduct']?>" style="width:150px;">

									&nbsp;<U>접수구분</U>&nbsp;
									<select class="select" name="srchType">
										<option value="" <?=( $_POST['srchType']=="" )?"selected":""?>>전체</option>
										<option value="10" <?=( $_POST['srchType']=="10" )?"selected":""?>>맞교환출고</option>
										<option value="11" <?=( $_POST['srchType']=="11" )?"selected":""?>>상품변경 맞교환출고</option>
										<option value="12" <?=( $_POST['srchType']=="12" )?"selected":""?>>누락재발송</option>
										<option value="13" <?=( $_POST['srchType']=="13" )?"selected":""?>>서비스발송</option>
										<option value="21" <?=( $_POST['srchType']=="21" )?"selected":""?>>반품접수</option>
										<option value="31" <?=( $_POST['srchType']=="31" )?"checked":""?>>출고시유의사항</option>
										<option value="39" <?=( $_POST['srchType']=="39" )?"checked":""?>>기타</option>
									</select>

									<A HREF="javascript:searchForm()"><img src=images/btn_inquery03.gif border=0 align=absmiddle alt="AND 검색"></A>
								</td>
							</tr>
							<tr><td height=5></td></tr>
							<tr>
								<td>
								&nbsp;
								<input type="checkbox" name="venderEnd" value="1" <?=($_POST['venderEnd'])?"checked":""?>> 업체처리완료 건 제외
								<input type="checkbox" name="excludeType21" value="1" <?=($_POST['excludeType21'])?"checked":""?>> 반품 건 제외
								<input type="checkbox" name="old" value="1" <?=($_POST['old'])?"checked":""?>> 장기간(3개월) 미처리건 제외
								</td>
							</tr>
							</table>



						</td>
					</tr>
					</table>
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
					<td align=right valign=bottom colspan=3>
					총 주문수 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;
					현재 <B><?=$gotopage?>/<?=ceil($t_count/$setup[list_num])?></B> 페이지
					</td>
				</tr>
				<tr><td colspan=3 height=1 bgcolor=#cccccc></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
				<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
				<col width=50></col>
				<col width=150></col>
				<col width=120></col>
				<col width=120></col>
				<col></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<tr height=32 align=center bgcolor=F5F5F5>
					<input type=hidden name=chkordercode>
					<td><B>구분</B></td>
					<td><B>주문번호</B></td>
					<td><B>고객명</B></td>
					<td><B>접수구분</B></td>
					<td><B>제목</B></td>
					<td><B>등록일</B></td>
					<td><B>업체처리일</B></td>
					<td><B>처리완료일</B></td>
				</tr>

				<?
					$colspan=8;
					$sql = "SELECT * FROM `tbl_csManager` WHERE `vender`=".$_VenderInfo->vidx.$WHERE.$ORDER_BY." LIMIT ".($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
					$result=mysql_query($sql,get_db_conn());
					$i=0;
					while($row=mysql_fetch_assoc($result)) {


						switch ( substr($row['type'],0,1) ) {
							case 1 : $csOrderType = "<font color='blue'>출고</font>"; break;
							case 2 : $csOrderType = "<font color='red'>반품</font>"; break;
							case 3 : $csOrderType = "기타"; break;
						}
				?>
				<tr bgcolor="#FFFFFF" onmouseover="this.style.background='#FEFBD1';" onmouseout="this.style.background='#FFFFFF'">
					<td align=center><?=$csOrderType?></td>
					<td align=center style="padding:3;line-height:11pt"><a href="javascript:OrderDetailView('<?=$row['order']?>')"><?=$row['order']?></a></td>
					<td align=center style="padding:3;line-height:11pt">
						<?
							if(substr($row['order'],-1)=="X") { //비회원
								echo $row['member']." (비회원)";;
							} else { // 회원
								$memberSQL = "SELECT `id`,`name` FROM `tblmember` WHERE `id`='".$row['member']."' LIMIT 1 ";
								$memberResult=mysql_query($memberSQL,get_db_conn());
								$memberRow=mysql_fetch_assoc ($memberResult);
								echo $memberRow['name']." (".$memberRow['id'].")";
							}
						?>
					</td>
					<td align=center style="padding:3;line-height:11pt">
						<?=$csRowType[$row['type']]?>
					</td>
					<td style="padding:3;line-height:11pt" title="<?=$row['adminMemo']?>">
						<a href="order_cs_view.php?code=<?=$row['idx']?>"><?=($row['customer'] == 1)?"<font color=red>[고객등록]</font> ":""?><?=$row['title']?><?=($row['delivery']=="vender")?"<font color=blue>(업체배송)</font>":""?></a>
					</td>
					<td align=center style="padding:3;line-height:11pt" title="<?=$row['adminRegDate']?>">
						<?=substr($row['adminRegDate'],0,10)?>
					</td>
					<td align=center style="padding:3;line-height:11pt" title="<?=$row['venderRegDate']?>">
						<?=($row['venderRegDate'] > 0)?substr($row['venderRegDate'],0,10):"-"?>
					</td>
					<td align=center style="padding:3;line-height:11pt" title="<?=$row['completeRegDate']?>">
						<?=($row['completeRegDate'] > 0)?substr($row['completeRegDate'],0,10):"-"?>
					</td>
				</tr>
				<?
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
<input type=hidden name=srchING value="<?=$srchING?>">
<input type=hidden name=srchOrder value="<?=$srchOrder?>">
<input type=hidden name=srchProduct value="<?=$srchProduct?>">
<input type=hidden name=srchType value="<?=$srchType?>">
<input type=hidden name=paystate value="<?=$paystate?>">
<input type=hidden name=deli_gbn value="<?=$deli_gbn?>">
<input type=hidden name=orderby value="<?=$orderby?>">
<input type=hidden name=block>
<input type=hidden name=gotopage>
</form>

</table>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>