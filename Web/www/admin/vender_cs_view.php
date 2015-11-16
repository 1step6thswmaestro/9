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


//N:미처리, X:배송요청, S:발송준비, Y:배송완료, C:주문취소, R:반송, D:취소요청, E:환불대기[가상계좌일 경우만]
$productState = array (
	"N"=>"미처리",
	"X"=>"배송요청",
	"S"=>"발송준비",
	"Y"=>"배송완료",
	"C"=>"주문취소",
	"R"=>"반송",
	"D"=>"취소요청",
	"E"=>"환불대기"
);


// 운송업체 리스트
$delicomlist=array();
$sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
$result=mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)) {
	$delicomlist[$row->code]=$row;
}
mysql_free_result($result);


unset($venderlist);
$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ";
$result=mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)) {
	$venderlist[$row->vender]=$row;
}
mysql_free_result($result);



// 검색

$WHERE = "";

// 상태 검색
switch ( $_POST['srchING'] ) {
	case "end" :
		$WHERE .= " AND `completeRegDate` > 0";
		break;
	case "ing" :
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



$sql = "SELECT * FROM `tbl_csManager` WHERE `idx`=".$_GET['code'];
$result=mysql_query($sql,get_db_conn());
$i=0;
$row=mysql_fetch_assoc($result);


	switch ( substr($row['type'],0,1) ) {
		case 1 : $csOrderType = "<font color='blue'>출고</font>"; $backOpt = "V"; break;
		case 2 : $csOrderType = "<font color='red'>반품</font>"; break;
		case 3 : $csOrderType = "기타"; break;
	}

	// 제품정보
	$productSQL = "SELECT * FROM `tblorderproduct` WHERE `ordercode`='".$row['order']."' AND `productcode`='".$row['product']."' LIMIT 1 ; ";
	$productResult=mysql_query($productSQL,get_db_conn());
	$productRow=mysql_fetch_assoc ($productResult);





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




// 저장
function CSsave ( f , o ) {
		if( o==1 ) {
			f.allComplete.value="completeRegDateOK";
			if( !confirm("\"처리완료\"를 누르시면 해당 CS의 완료처리가 됩니다.") ) return false;
		}
		f.action="vender_cs_process.php";
		f.method="POST";
		f.submit();
}


// 추가 정산비 저장
function CSPaySave ( f ) {
		f.action="vender_cs_process.php";
		f.method="POST";
		f.submit();
}



// CS 관리 팝업 - (주문코드, 상품코드, 벤더, 회원아이디)
function csManagerPop( order, product, vender ) {
	window.open( "cs_orderInsert.php?o="+order+"&p="+product+"&v="+vender , "csManagerInsert" , "width=620, height=500, menubar=no, status=no" );
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
				<td><IMG SRC="images/vender_cs_stitle2.gif" ALT="입점업체 CS등록 내용"></TD>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>









					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<col width=140></col>
					<col width=></col>
						<TR><TD colspan=2 background="images/table_top_line.gif"></TD></TR>
						<tr>
							<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">구분</td>
							<TD class="td_con1">
								<?=$csOrderType?> - <?=$csRowType[$row['type']]?>
							</td>
						</tr>
						<TR><TD colspan="2" background="images/table_con_line.gif"></TD></TR>
						<tr>
							<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">작성일</td>
							<TD class="td_con1">
								<?=$row['adminRegDate']?>
							</td>
						</tr>
						<TR><TD colspan="2" background="images/table_con_line.gif"></TD></TR>
						<tr>
							<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">주문번호</td>
							<TD class="td_con1">
								<a href="javascript:OrderDetailView('<?=$row['order']?>')"><?=$row['order']?></a>
							</td>
						</tr>
						<TR><TD colspan="2" background="images/table_con_line.gif"></TD></TR>
						<tr>
							<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">고객정보</td>
							<TD class="td_con1">
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
						</tr>
						<TR><TD colspan="2" background="images/table_con_line.gif"></TD></TR>
						<tr>
							<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">제목</td>
							<TD class="td_con1">
								<?=($row['customer'] == 1)?"<font color=red>[고객등록]</font> ":""?><?=$row['title']?><?=($row['delivery']=="vender")?"<font color=blue>(업체배송)</font>":""?>
							</td>
						</tr>
						<TR><TD colspan="2" background="images/table_con_line.gif"></TD></TR>
						<tr>
							<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">접수내용</td>
							<TD class="td_con1">
								<?=nl2br($row['adminMemo'])?>
							</td>
						</tr>
						<TR><TD colspan="2" background="images/table_con_line.gif"></TD></TR>
						<tr>
							<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">접수상품</td>
							<TD class="td_con1">

								<table border=0 cellpadding=0 cellspacing=1 bgcolor="#e7e7e7" width="98%" style="table-layout:fixed">
									<!-- <col width=100></col> -->
									<col width=130></col>
									<col width=70 style="padding:5px;"></col>
									<col width=></col>
									<col width=70></col>
									<col width=50></col>
									<tr align='center' bgcolor="#f5f5f5">
										<!-- <td>상태</td> -->
										<td>상품코드</td>
										<td>이미지</td>
										<td>상품명</td>
										<td>판매가</td>
										<td>수량</td>
									</tr>
									<tr align='center' bgcolor="#ffffff">
										<!-- <td><?=$productState[$productRow['deli_gbn']]?></td> -->
										<td>
											<?=$productRow['productcode']?><br>
											<a href="/?productcode=<?=$productRow['productcode']?>" target=_blank><img src="images/productregister_goproduct.gif" align=absmiddle border=0></a>
										</td>
										<td><img src="/data/shopimages/product/<?=$productRow['productcode']?>3.jpg" width="50" onerror="this.src='/images/no_img.gif';"></td>
										<td>
											<?=$productRow['productname']?>
											<?=($productRow['opt1_name'])?"(옵션1:".$productRow['opt1_name'].")":""?>
											<?=($productRow['opt2_name'])?"(옵션2:".$productRow['opt2_name'].")":""?>
										</td>
										<td><?=number_format($productRow['price'])?></td>
										<td><?=$productRow['quantity']?></td>
									</tr>
								</table>

							</td>
						</tr>



						<form name="adminCS_save_form">

						<TR><TD colspan="2" background="images/table_con_line.gif"></TD></TR>
						<tr>
							<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">처리내용</td>
							<TD class="td_con1">
								<?=nl2br($row['venderMemo'])?>
							</td>
						</tr>
						<TR><TD colspan="2" background="images/table_con_line.gif"></TD></TR>
						<tr>
							<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">관련운송장</td>
							<TD class="td_con1">
								<?
									if($row['completeRegDate'] > 0) {
										echo "[".$delicomlist[$row['deli_com']]->company_name."] ".$row['deli_num'];
									}else{
								?>
								<select name=deli_com style="width:90;height:18;font-size:8pt">
									<option value="">없음</option>
									<?
										foreach( $delicomlist as $code ) {
											$sel = ( $row['deli_com'] == $code->code ) ? "selected":"";
											echo "<option value=\"".$code->code."\" ".$sel.">".$code->company_name."</option>";
										}
									?>
								</select>
								<input type=text name=deli_num value="<?=$row['deli_num']?>" size=20 maxlength="20" class="input">
								<?
									}
								?>
							</td>
						</tr>

						<?
							if( $backOpt == "V" ) {
						?>
						<TR><TD colspan="2" background="images/table_con_line.gif"></TD></TR>
						<tr>
							<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">회수처리 내용</td>
							<TD class="td_con1">
								<?=nl2br($row['venderBackMemo'])?>
							</td>
						</tr>
						<TR><TD colspan="2" background="images/table_con_line.gif"></TD></TR>
						<tr>
							<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">회수운송장</td>
							<TD class="td_con1">
								<?
									if($row['completeRegDate'] > 0) {
										echo "[".$delicomlist[$row['back_deli_com']]->company_name."] ".$row['back_deli_num'];
									}else{
								?>
								<select name=back_deli_com style="width:90;height:18;font-size:8pt">
									<option value="">없음</option>
									<?
										foreach( $delicomlist as $code ) {
											$sel = ( $row['back_deli_com'] == $code->code ) ? "selected":"";
											echo "<option value=\"".$code->code."\" ".$sel.">".$code->company_name."</option>";
										}
									?>
								</select>
								<input type=text name=back_deli_num value="<?=$row['back_deli_num']?>" size=20 maxlength=20 class="input">
								<?
									}
								?>
							</td>
						</tr>
						<TR><TD colspan="2" background="images/table_con_line.gif"></TD></TR>
						<tr>
							<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">회수상태</td>
							<TD class="td_con1">
								<?=($row['backCHK'])?"회수 완료":"-"?>
							</td>
						</tr>
						<?
							}
						?>

						<input type="hidden" name="code" value="<?=$row['idx']?>">
						<input type="hidden" name="allComplete" value="">
						<input type="hidden" name="mode" value="adminCS_save">
						</form>


						<TR><TD colspan="2" background="images/table_con_line.gif"></TD></TR>
						<tr>
							<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">처리시간</td>
							<TD class="td_con1">
								<?=($row['venderRegDate'] > 0)?$row['venderRegDate']:"-"?>
							</td>
						</tr>


						<TR><TD colspan="2" background="images/table_con_line.gif"></TD></TR>
						<tr>
							<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">완료시간</td>
							<TD class="td_con1">
								<?=($row['completeRegDate'] > 0)?$row['completeRegDate']:"-"?>
							</td>
						</tr>
						<TR><TD colspan=2 background="images/table_top_line.gif"></TD></TR>

						<tr><td height="20"></td></tr>
						<tr>
							<td colspan="2" align="center">
								<?
									if($row['completeRegDate'] > 0) {
										echo "* 처리완료 된 CS 입니다.<br />";
									}else{
								?>
								<img src="images/btn_save2.gif" style="cursor:hand;" onclick="CSsave(adminCS_save_form,0);" alt="저장하기" /><!--저장-->
								<img src="images/btn_edit5.gif" style="cursor:hand;" onclick="csManagerPop('<?=$row['order']?>','<?=$row['product']?>','<?=$row['vender']?>'); return false;" alt="등록내용변경" /><!--등록내용변경-->
								<?
										if ( $row['venderRegDate'] > 0 ) {
								?>
								<img src="images/botteon_endprocess.gif" style="cursor:hand;" onclick="CSsave(adminCS_save_form,1);" alt="처리완료" />
								<?
										}
									}
								?>
								<a href="/admin/vender_cs.php"><img src="images/btn_list3.gif" border="0" alt="목록보기" /></a><!--목록보기-->
							</td>
						</tr>
						<tr><td height="40"></td></tr>
					</table>


					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><IMG SRC="images/vender_cs_stitle3.gif" ALT="입점업체 정산"></TD>
						</tr>
						<tr>
							<td height=3></td>
						</tr>
						<form name="adminCS_pay_save_form">

						<TR><TD colspan=2 background="images/table_top_line.gif"></TD></TR>
						<tr>
							<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">업체정산</td>
							<TD class="td_con1">

								<table border=0 cellpadding=0 cellspacing=1 width="98%" bgcolor="#e5e5e5" style="table-layout:fixed">
									<col width=150 bgcolor="#f5f5f5" style="padding:2px 0px 2px 10px;"></col>
									<col bgcolor="#ffffff" style="padding:2px 0px 2px 10px;"></col>
									<tr>
										<td>회수배송비</td>
										<td><input type="text" style="text-align:right; padding-right:4px;" name="deliPay" value="<?=$row['deliPay']?>" class="input"> 원</td>
									</tr>
									<tr>
										<td>추가정산배송비</td>
										<td><input type="text" style="text-align:right; padding-right:4px;" name="orderPay" value="<?=$row['orderPay']?>" class="input"> 원</td>
									</tr>
									<tr>
										<td>추가정산사유</td>
										<td><input type="text" style="width:96%;" name="orderPayMemo" value="<?=$row['orderPayMemo']?>" class="input"></td>
									</tr>
								</table>


							</td>
						</tr>
						<TR><TD colspan=2 background="images/table_top_line.gif"></TD></TR>


						<input type="hidden" name="code" value="<?=$row['idx']?>">
						<input type="hidden" name="mode" value="adminCS_pay_save">
						</form>


						<tr><td height="20"></td></tr>
						<tr>
							<td colspan="2" align="center">
								<img src="images/btn_save2.gif" style="cursor:hand;" onclick="CSPaySave(adminCS_pay_save_form);" alt="저장하기" /><!--저장-->
							</td>
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
						<td><span class="font_dotline">입점업체 CS 관리</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 상담게시판은 본사와 입점사간에 CS관리화면 입니다.</td>
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

<form name=detailform method="post" action="order_detail.php" target="orderdetail">
<input type=hidden name=ordercode>
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