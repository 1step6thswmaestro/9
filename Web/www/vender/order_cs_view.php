<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");


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

// 제품 수정
function GoPrdinfo(prcode,target) {
	document.form3.target="";
	document.form3.prcode.value=prcode;
	if(target.length>0) {
		document.form3.target=target;
	}
	document.form3.submit();
}


// 저장
function venderCSsave ( f , o ) {
	//if( confirm("\"처리완료\"를 누르셔야 완료처리가 됩니다.") ){
		if( o==1 ) {
			f.venderComplete.value="venderRegDateOK";
		}
		f.action="order_cs_process.php";
		f.method="POST";
		f.submit();
	//}
}
</script>

<style>
	.cs_ttd{width:140px; padding:5px 0px 5px 15px; background-color:#f5f5f5; font-weight:bold; border-right:1px solid #e7e7e7; border-bottom:1px solid #e7e7e7;}
	.cs_ctd{padding:5px 0px 5px 10px; border-bottom:1px solid #e7e7e7;}
</style>


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
			<tr><td height="2" bgcolor="#808080"></td></tr>
			<tr>
				<td>


					<table border=0 cellpadding=0 cellspacing=0 style="table-layout:fixed">
						<tr>
							<td class="cs_ttd">구분</td>
							<td class="cs_ctd">
								<?=$csOrderType?> - <?=$csRowType[$row['type']]?>
							</td>
						</tr>
						<tr>
							<td class="cs_ttd">작성일</td>
							<td class="cs_ctd">
								<?=$row['adminRegDate']?>
							</td>
						</tr>
						<tr>
							<td class="cs_ttd">주문번호</td>
							<td class="cs_ctd"><a href="javascript:OrderDetailView('<?=$row['order']?>')"><?=$row['order']?></a></td>
						</tr>
						<tr>
							<td class="cs_ttd">고객정보</td>
							<td class="cs_ctd">
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
						<tr>
							<td class="cs_ttd">제목</td>
							<td class="cs_ctd"><?=($row['customer'] == 1)?"<font color=red>[고객등록]</font> ":""?><?=$row['title']?><?=($row['delivery']=="vender")?"<font color=blue>(업체배송)</font>":""?></td>
						</tr>
						<tr>
							<td class="cs_ttd">접수내용</td>
							<td class="cs_ctd"><?=nl2br($row['adminMemo'])?></td>
						</tr>
						<tr>
							<td class="cs_ttd">접수상품</td>
							<td class="cs_ctd">

								<table border=0 cellpadding=0 cellspacing=1 bgcolor="#e7e7e7" width="98%" style="table-layout:fixed">
									<!-- <col width=100></col> -->
									<col width=130></col>
									<col width=60></col>
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
											<a href="javascript:GoPrdinfo('<?=$productRow['productcode']?>','_blank');"><?=$productRow['productcode']?></a><br>
											<a href="/?productcode=<?=$productRow['productcode']?>" target=_blank><img src="images/icon_goprdetail.gif" align=absmiddle border=0></a>
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











						<form name="venderCS_save_form">

						<tr>
							<td class="cs_ttd">처리내용</td>
							<td class="cs_ctd"><textarea name="venderMemo" style="width:100%; height:150px;"><?=$row['venderMemo']?></textarea></td>
						</tr>
						<tr>
							<td class="cs_ttd">관련운송장</td>
							<td class="cs_ctd"><? $delicomlist[$row['deli_com']]->company_name?>
								<select name=deli_com style="width:90;height:18;font-size:8pt">
									<option value="">없음</option>
									<?
										foreach( $delicomlist as $code ) {
											$sel = ( $row['deli_com'] == $code->code ) ? "selected":"";
											echo "<option value=\"".$code->code."\" ".$sel.">".$code->company_name."</option>";
										}
									?>
								</select>
								<input type=text name=deli_num value="<?=$row['deli_num']?>" size=20 maxlength=20 style="height:19;font-size:8pt">
							</td>
						</tr>

						<?
							if( $backOpt == "V" ) {
						?>
						<tr>
							<td class="cs_ttd">회수처리 내용</td>
							<td class="cs_ctd"><textarea name="venderBackMemo" style="width:100%; height:150px;"><?=$row['venderBackMemo']?></textarea></td>
						</tr>
						<tr>
							<td class="cs_ttd">회수운송장</td>
							<td class="cs_ctd">
								<select name=back_deli_com style="width:90;height:18;font-size:8pt">
									<option value="">없음</option>
									<?
										foreach( $delicomlist as $code ) {
											$sel = ( $row['back_deli_com'] == $code->code ) ? "selected":"";
											echo "<option value=\"".$code->code."\" ".$sel.">".$code->company_name."</option>";
										}
									?>
								</select>
								<input type=text name=back_deli_num value="<?=$row['back_deli_num']?>" size=20 maxlength=20 style="height:19;font-size:8pt">
							</td>
						</tr>
						<tr>
							<td class="cs_ttd">회수상태</td>
							<td class="cs_ctd">
								<?
									if ( $row['completeRegDate'] > 0 ) {
										echo ($row['backCHK'])?"회수완료":"-";
									} else {
								?>
								<input type="checkbox" name="venderBackCHK" value="1" <?=($row['backCHK'])?"checked":""?>>회수 완료
								<?
									}
								?>
							</td>
						</tr>
						<?
							}
						?>

						<input type="hidden" name="code" value="<?=$row['idx']?>">
						<input type="hidden" name="venderComplete" value="">
						<input type="hidden" name="mode" value="venderCS_save">
						</form>


						<tr>
							<td class="cs_ttd">처리시간</td>
							<td class="cs_ctd"><?=($row['venderRegDate'] > 0)?$row['venderRegDate']:"-"?></td>
						</tr>


						<tr>
							<td class="cs_ttd">완료시간</td>
							<td class="cs_ctd"><?=($row['completeRegDate'] > 0)?$row['completeRegDate']:"-"?></td>
						</tr>


						<tr>
							<td class="cs_ttd">업체정산</td>
							<td class="cs_ctd">

								<table border=0 cellpadding=0 cellspacing=1 bgcolor="#e7e7e7" width="98%" style="table-layout:fixed">
									<col width=150 bgcolor="#f5f5f5" style="padding-left:10px;"></col>
									<col bgcolor="#ffffff" style="padding-left:10px;"></col>
									<tr>
										<td>회수배송비</td>
										<td><?=number_format($row['deliPay'])?>원</td>
									</tr>
									<tr>
										<td>추가정산배송비</td>
										<td><?=number_format($row['orderPay'])?>원</td>
									</tr>
									<tr>
										<td>추가정산사유</td>
										<td><?=$row['orderPayMemo']?>&nbsp;</td>
									</tr>
								</table>

							</td>
						</tr>

						<tr><td height="20"></td></tr>
						<tr>
							<td colspan="2" align="center">
								<?
									if ( $row['completeRegDate'] > 0 ) {
										//echo "처리완료";
									} else {
								?>
								<input type="image" src="images/btn_save01.gif" onclick="venderCSsave(venderCS_save_form,0);">
								<input type="image" src="images/btn_complete.gif" onclick="venderCSsave(venderCS_save_form,1);">
								<?
									}
								?>
								<a href="/vender/order_cs.php"><img src="images/btn_list2.gif" border="0"></a>
							</td>
						</tr>
					</table>


				</td>
			</tr>
			<tr><td height="40"></td></tr>










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
</table>

<form name=detailform method="post" action="order_detail.php" target="vorderdetail">
<input type=hidden name=ordercode>
</form>

<form name=form3 method=post action="product_prdmodify.php">
<input type=hidden name=prcode>
</form>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>