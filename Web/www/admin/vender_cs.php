<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "vd-1";
$MenuCode = "vender";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################






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

// 벤더 검색
if( $_POST['srchVender'] ) {
	$WHERE .= " AND `vender` = '".$_POST['srchVender']."'";
}

$ORDER_BY .= " ORDER BY `idx` DESC";



//리스트 세팅
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





unset($venderlist);
$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ";
$result=mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)) {
	$venderlist[$row->vender]=$row;
}
mysql_free_result($result);

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function searchForm() {
	document.sForm.submit();
}


function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}

function ViewCounsel(date) {
	window.open("about:blank","vendercounsel_pop","width=600,height=450,scrollbars=yes");
	document.form3.date.value=date;
	document.form3.submit();
}

function GoPage(block,gotopage) {
	document.pageForm.block.value = block;
	document.pageForm.gotopage.value = gotopage;
	document.pageForm.submit();
}


function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600");
	document.detailform.submit();
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 입점업체 관리  &gt; <span class="2depth_select">입점업체 CS 관리</span></td>
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
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_cs_title.gif" ALT="입점업체 CS 관리"></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height="21"></TD>
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
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">쇼핑몰 본사와 입점업체간의 상품에 대한 CS 관리를 하실 수 있습니다.</TD>
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
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_cs_stitle1.gif" ALT="입점업체 CS 관리 목록"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
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


									&nbsp;<U>벤더</U>&nbsp;
									<select class="select" name="srchVender">
										<option value="" <?=( $_POST['srchVender']=="" )?"selected":""?>>전체</option>
										<?
											foreach ( $venderlist as $var ) {
												$sel = ( $_POST['srchVender']==$var->vender )?"selected":"";
												echo "<option value='".$var->vender."' ".$sel.">".$var->com_name."(".$var->id.")</option>";
											}
										?>
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
					<tr><td height=20></td></tr>
					<tr>
						<td align="right" style="font-size:11px;">
							<img width="13" height="13" src="images/icon_8a.gif" border="0"/>총 주문수 : <B><?=number_format($t_count)?></B>건&nbsp;
							<img width="13" height="13" src="images/icon_8a.gif" border="0"/>현재 <B><?=$gotopage?>/<?=ceil($t_count/$setup[list_num])?></B> 페이지 &nbsp;&nbsp;
							<a href="http://www.getmall.co.kr/data/cs_manual.zip"><img src="images/btn_csmanual.gif" border="0" align="absmiddle" alt="CS관리 매뉴얼" /></a>
						</td>
					</tr>
					<tr><td height="5"></td></tr>
					<tr><td height="1" bgcolor="#cccccc"></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=1 width=100% bgcolor=E7E7E7 style="table-layout:fixed">
				<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
				<col width=100></col>
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
					<td><B>벤더</B></td>
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
					$sql = "SELECT * FROM `tbl_csManager` WHERE 1 ".$WHERE.$ORDER_BY." LIMIT ".($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
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
					<td align=center><?=$venderlist[$row['vender']]->com_name?>(<?=$venderlist[$row['vender']]->id?>)</td>
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
					<td style="padding:7px; line-height:11pt" title="<?=$row['adminMemo']?>">
						<a href="vender_cs_view.php?code=<?=$row['idx']?>"><?=($row['customer'] == 1)?"<font color=red>[고객등록]</font> ":""?><?=$row['title']?><?=($row['delivery']=="vender")?"<font color=blue>(업체배송)</font>":""?></a>
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

				<form name=detailform method="post" action="order_detail.php" target="orderdetail">
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
			<tr><td height=2></td></tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif">&nbsp;</TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" class="menual_bg" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">입점업체 상담게시판 관리</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 상담게시판은 본사와 입점사간에 1:1게시판 입니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 입점사 아이디 확인 [제목]클릭후 답변처리 할 수 있습니다.</td>
					</tr>
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

<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>

<form name=pageForm action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=scheck value="<?=$scheck?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<form name=form3 action="vender_counsel_pop.php" method=post target="vendercounsel_pop">
<input type=hidden name=date>
</form>

</table>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>