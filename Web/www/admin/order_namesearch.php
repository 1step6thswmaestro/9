<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-1";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 20;

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];
$popup=$_REQUEST["popup"];

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}

$searchtype=$_POST["searchtype"];
if(strlen($searchtype)==0) $searchtype="0";
if(!preg_match("/^(0|1)$/", $searchtype)) {
	$searchtype="0";
}

$s_check=$_POST["s_check"];
if(strlen($s_check)==0) $s_check="A";
if(!preg_match("/^(A|B|C|D|E|F|G|H|I)$/", $s_check)) {
	$s_check="A";
}
$search=$_POST["search"];
$searchprice=$_POST["searchprice"];
$gong_gbn=$_POST["gong_gbn"];
if(!preg_match("/^(Y|N)$/", $gong_gbn)) {
	$gong_gbn="N";
}

$type=$_POST["type"];
$ordercodes=substr($_POST["ordercodes"],0,-1);
$deli_gbn=$_POST["deli_gbn"];


if($type=="delete" && strlen($ordercodes)>0) {	//주문서 삭제
	$ordercode=ereg_replace(",","','",$ordercodes);
	mysql_query("INSERT INTO tblorderinfotemp SELECT * FROM tblorderinfo WHERE ordercode IN ('".$ordercode."')",get_db_conn());
	mysql_query("INSERT INTO tblorderproducttemp SELECT * FROM tblorderproduct WHERE ordercode IN ('".$ordercode."')",get_db_conn());
	mysql_query("INSERT INTO tblorderoptiontemp SELECT * FROM tblorderoption WHERE ordercode IN ('".$ordercode."')",get_db_conn());

	mysql_query("DELETE FROM tblorderinfo WHERE ordercode IN ('".$ordercode."')",get_db_conn());
	mysql_query("DELETE FROM tblorderproduct WHERE ordercode IN ('".$ordercode."')",get_db_conn());
	mysql_query("DELETE FROM tblorderoption WHERE ordercode IN ('".$ordercode."')",get_db_conn());

	$log_content = "## 주문내역 삭제 ## - 주문번호 : ".$ordercodes;
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	$onload="<script>alert('선택하신 주문내역을 삭제하였습니다.');</script>";
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(form) {
	if(shop=="layer1") {	//이름으로 검색
		if(form.search.value.length==0) {
			alert("검색어를 입력하세요.");
			form.search.focus();
			return;
		}
	} else if(shop=="layer2") {	//가격으로 검색
		if(form.searchprice.value.length==0) {
			alert("입금자 확인이 안된 무통장 입금 금액을 입력하세요.");
			form.searchprice.focus();
			return;
		}
		if(form.searchprice.value==0) {
			alert("입금자 확인이 안된 무통장 입금 금액을 입력하세요.");
			form.searchprice.focus();
			return;
		}
		if(!IsNumeric(form.searchprice.value)) {
			alert("무통장 입금금액은 숫자만 입력 가능합니다.");
			form.searchprice.focus();
			return;
		}
	}
	document.form1.action="order_namesearch.php";
	document.form1.submit();
}

var shop="<?=($searchtype=="0"?"layer1":"layer2")?>";
var ArrLayer = new Array ("layer1","layer2");
function ViewLayer(gbn){
	if(gbn=="layer2") {
		if(document.form1.gong_gbn[1].checked==true) {
			alert("가격으로 검색은 일반주문내역만 검색하실 수 있습니다.");
			document.form1.gong_gbn[0].checked=true;
			document.form1.s_check.disabled=false;
		}
	}
	if(document.all){
		for(i=0;i<2;i++) {
			if (ArrLayer[i] == gbn)
				document.all[ArrLayer[i]].style.display="";
			else
				document.all[ArrLayer[i]].style.display="none";
		}
	} else if(document.getElementById){
		for(i=0;i<2;i++) {
			if (ArrLayer[i] == gbn)
				document.getElementByld[ArrLayer[i]].style.display="";
			else
				document.getElementByld[ArrLayer[i]].style.display="none";
		}
	} else if(document.layers){
		for(i=0;i<2;i++) {
			if (ArrLayer[i] == gbn)
				document.layers[ArrLayer[i]].display="";
			else
				document.layers[ArrLayer[i]].display="none";
		}
	}
	shop=gbn;
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600");
	document.detailform.submit();
}

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

function MemberView(id){
	parent.topframe.ChangeMenuImg(4);
	document.member_form.search.value=id;
	document.member_form.submit();
}

function SenderSearch(sender) {
	document.sender_form.search.value=sender;
	document.sender_form.submit();
}

function CheckAll(){
	chkval=document.form2.allcheck.checked;
	cnt=document.form2.tot.value;
	for(i=1;i<=cnt;i++){
		document.form2.chkordercode[i].checked=chkval;
	}
}

function AddressPrint() {
	document.form1.action="order_address_excel.php";
	document.form1.submit();
	document.form1.action="";
}

function OrderExcel() {
	document.form1.action="order_excel.php";
	document.form1.submit();
	document.form1.action="";
}

function OrderDelete(ordercode) {
	if(confirm("해당 주문서를 삭제하시겠습니까?")) {
		document.idxform.type.value="delete";
		document.idxform.ordercodes.value=ordercode+",";
		document.idxform.submit();
	}
}

function OrderDeliPrint() {
	alert("운송장 출력은 준비중에 있습니다.");
}

function OrderCheckPrint() {
	document.printform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked==true) {
			document.printform.ordercodes.value+=document.form2.chkordercode[i].value.substring(1)+",";
		}
	}
	if(document.printform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	if(confirm("소비자용 주문서로 출력하시겠습니까?")) {
		document.printform.gbn.value="N";
	} else {
		document.printform.gbn.value="Y";
	}
	document.printform.target="hiddenframe";
	document.printform.submit();
}

function OrderCheckExcel() {
	document.checkexcelform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked==true) {
			document.checkexcelform.ordercodes.value+=document.form2.chkordercode[i].value.substring(1)+",";
		}
	}
	if(document.checkexcelform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	document.checkexcelform.action="order_excel.php";
	document.checkexcelform.submit();
}

function OrderSendSMS() {
	document.smsform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked==true) {
			document.smsform.ordercodes.value+="'"+document.form2.chkordercode[i].value.substring(1)+"',";
		}
	}
	if(document.smsform.ordercodes.value.length==0) {
		alert("SMS를 발송할 주문서를 선택하세요.");
		return;
	}
	window.open("about:blank","sendsmspop","width=220,height=350,scrollbars=no");
	document.smsform.type.value="order";
	document.smsform.submit();
}

function ViewGong(gong_seq) {
	parent.topframe.ChangeMenuImg(6);
	document.gong.gong_seq.value=gong_seq;
	document.gong.submit();
}

</script>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
		<?
			if( $popup != "OK" ) {
				echo "
					<col width=198></col>
					<col width=10></col>
					<col width=></col>
				";
			}
		?>
		<tr>
			<?
				if( $popup != "OK" ) {
					echo "<td valign=\"top\" background=\"images/leftmenu_bg.gif\">";
					include ("menu_order.php");
					echo "</td><td></td>";
				}
			?>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" width="16" height="12" border="0" valign=absmiddle>현재위치 : 주문/매출 &gt; 주문조회 및 배송관리 &gt; <span class="2depth_select">이름/가격별 외 주문조회</span></td>
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
					<TD><IMG SRC="images/order_namesearch_title.gif" ></TD>
					</tr>
					<tr>
					<TD width="100%" background="images/title_bg.gif" height=21></TD>
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
					<TD background="images/distribute_04.gif"><IMG SRC="images/distribute_04.gif" ></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue"><p>주문자 이름 및 주문가격 등으로 주문현황 및 주문내역을 확인하실 수 있습니다.</p></TD>
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
					<TD><IMG SRC="images/order_list_stitle1.gif" WIDTH="187" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>



			<?
				if( $popup != "OK" ) {
			?>
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
								<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">검색방법 선택</TD>
								<TD class="td_con1" ><input type=radio id="idx_searchtype1" name=searchtype value="0" onclick="ViewLayer('layer1')" <?if($searchtype=="0") echo "checked";?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype1>이름으로 검색</label>&nbsp;&nbsp;&nbsp;<input type=radio id="idx_searchtype2" name=searchtype value="1" onclick="ViewLayer('layer2')" <?if($searchtype=="1") echo "checked";?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype2>가격으로 검색</label></TD>
							</TR>
						</table>
						<div id=layer1 style="margin-left:0;display:hide; display:<?=($searchtype=="0"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD colspan="2" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
							</TR>
							<TR>
								<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">검색조건 및 입력</TD>
								<TD class="td_con1" >&nbsp;검색조건 : <select name=s_check class="select">
									<option value="A" <?if($s_check=="A")echo"selected";?>>주문자</option>
									<option value="B" <?if($s_check=="B")echo"selected";?>>수령인</option>
									<option value="C" <?if($s_check=="C")echo"selected";?>>아이디</option>
									<option value="D" <?if($s_check=="D")echo"selected";?>>주문번호</option>
									<option value="E" <?if($s_check=="E")echo"selected";?>>이메일</option>
									<option value="F" <?if($s_check=="F")echo"selected";?>>주소</option>
									<option value="G" <?if($s_check=="G")echo"selected";?>>전화번호</option>
									<option value="H" <?if($s_check=="H")echo"selected";?>>입금자명</option>
									<option value="I" <?if($s_check=="I")echo"selected";?>>송장번호</option>
									</select>&nbsp;&nbsp;&nbsp;&nbsp;검색어&nbsp;:&nbsp;<input type=text name=search value="<?=$search?>" size=50 class="input"></TD>
							</TR>
							<TR>
								<TD colspan="2" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
							</TR>
							<TR>
								<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">처리단계 선택</TD>
								<TD class="td_con1" >
								<?
								$ardg=array("\"\":전체선택","S:발송준비","Y:배송","N:미처리","C:주문취소","R:반송","D:취소요청","E:환불대기","H:배송(정산보류)");
								for($i=0;$i<count($ardg);$i++) {
									$tmp=split(":",$ardg[$i]);
									if($tmp[0]==$deli_gbn || (strlen($deli_gbn)==0 && $i==0)) {
										echo "<input type=radio id=\"idx_deli".$i."\" name=deli_gbn value=\"".$tmp[0]."\" checked style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none\"> <label style=\"cursor:hand; TEXT-DECORATION: none\" onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_deli".$i.">".$tmp[1]."</label>\n";
									} else {
										echo "<input type=radio id=\"idx_deli".$i."\" name=deli_gbn value=\"".$tmp[0]."\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none\"> <label style=\"cursor:hand; TEXT-DECORATION: none\" onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_deli".$i.">".$tmp[1]."</label>\n";
									}
									echo "&nbsp;&nbsp;&nbsp;\n";
								}
								?>
								</TD>
							</TR>
						</table>
						</div>
						<div id=layer2 style="margin-left:0;display:hide; display:<?=($searchtype=="1"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD colspan="2" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
							</TR>
							<TR>
								<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">주문금액 입력</TD>
								<TD class="td_con1" >&nbsp;<B>무통장 입금금액:</B> <input type=text name=searchprice value="<?=$searchprice?>" size=30 style="PADDING-RIGHT: 5px; TEXT-ALIGN: right" onkeyup="strnumkeyup(this);"class="input"> 원<br>&nbsp;<span class="font_orange">* 입금자 확인이 안될 경우 금액으로 조회가 가능합니다.</span></TD>
							</TR>
						</table>
						</div>
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD colspan="2"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
							</TR>
							<tr>
								<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">주문종류 구분</TD>
								<TD class="td_con1" ><input type=radio id="idx_gong_gbn1" name=gong_gbn value="N" <?if($gong_gbn=="N")echo"checked";?> onclick="this.form.s_check.disabled=false;"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_gong_gbn1>일반주문</label>&nbsp;&nbsp;&nbsp;<input type=radio id="idx_gong_gbn2" name=gong_gbn value="Y" <?if($gong_gbn=="Y")echo"checked";?> onclick="alert('공동구매 검색은 이름으로만 검색이 됩니다.');this.form.searchtype[0].checked=true;ViewLayer('layer1');this.form.s_check.disabled=true;"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_gong_gbn2>공동구매</label></TD>
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
				<td style="padding-top:4pt;"><p align="right"><a href="javascript:CheckForm(document.form1);"><img src="images/botteon_search.gif" width="113" height="38" border="0" hspace="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height="20"><p>&nbsp;</p></td>
			</tr>
			<?
				}
			?>


			<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<?
		if($gong_gbn=="N") {
			$curtime=time();
			$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");
			$qry = "WHERE 1=1 ";
			if($searchtype=="1") {	//주문금액으로 검색
				$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*180)))."' AND paymethod='B' ";
				$qry.= "AND deli_gbn='N' AND price='".$searchprice."' ";
			} else {	//이름으로 검색
				switch($s_check) {
					case "A":	//주문자
						if(strlen($search)>=6) {
							$qry.= "AND sender_name = '".$search."' ";
						} else {
							$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*180)))."' ";
							$qry.= "AND sender_name LIKE '".$search."%' ";
						}
						break;
					case "B":	//수령인
						if(strlen($search)>=6) {
							$qry.= "AND receiver_name = '".$search."' ";
						} else {
							$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*180)))."' ";
							$qry.= "AND receiver_name LIKE '".$search."%' ";
						}
						break;
					case "C":	//아이디
						$qry.= "AND id='".$search."' ";
						break;
					case "D":	//주문번호
						$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*180)))."' ";
						$qry.= "AND id LIKE 'X".$search."%' ";
						break;
					case "E":	//이메일
						$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*30)))."' ";
						$qry.= "AND sender_email LIKE '".$search."%' ";
						break;
					case "F":	//주소
						$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*30)))."' ";
						$qry.= "AND receiver_addr LIKE '%".$search."%' ";
						break;
					case "G":	//전화번호
						$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*30)))."' ";
						$qry.= "AND sender_tel LIKE '%".$search."%' ";
						break;
					case "H":	//입금자명
						$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*30)))."' ";
						$qry.= "AND order_msg LIKE '%입금자 : ".$search."%' ";
						break;
					case "I":	//송장번호
						$qry.= "AND ordercode>'".date("Ymd",($curtime-(60*60*24*10)))."' ";
						$qry.= "AND deli_num LIKE '".$search."%' ";
						break;
				}
				if(strlen($deli_gbn)>0)		$qry.= "AND deli_gbn='".$deli_gbn."' ";
			}

			if(strlen($search)>0 || strlen($searchprice)>0) {
				$sql = "SELECT COUNT(*) as t_count, SUM(price) as t_price FROM tblorderinfo ".$qry;
				$result = mysql_query($sql,get_db_conn());
				$row = mysql_fetch_object($result);
				$t_count = (int)$row->t_count;
				$t_price = (int)$row->t_price;
				mysql_free_result($result);
				$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

				$sql = "SELECT * FROM tblorderinfo ".$qry." ";
				$sql.= "ORDER BY ordercode DESC ";
				$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
				$result = mysql_query($sql,get_db_conn());
			} else {
				$t_count=0;
				$t_price=0;
				$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
			}
?>
			<tr>
				<td style="padding-bottom:3pt;"><p align="right"><img src="images/icon_8a.gif" width="13" height="13" border="0">총 주문수 : <B><?=number_format($t_count)?></B>건&nbsp;&nbsp;<img src="images/icon_8a.gif" width="13" height="13" border="0">합계금액 : <B><?=number_format($t_price)?></B>원&nbsp; <img src="images/icon_8a.gif" width="13" height="13" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup[list_num])?></b> 페이지</td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<input type=hidden name=chkordercode>
				<TR>
					<TD background="images/table_top_line.gif" width="761" colspan="9"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><p align="center"><input type=checkbox name=allcheck onclick="CheckAll()"></TD>
					<TD class="table_cell1"><p align="center">No</TD>
					<TD class="table_cell1"><p align="center">주문일자</TD>
					<TD class="table_cell1"><p align="center">주문자</TD>
					<TD class="table_cell1"><p align="center">ID/주문번호</TD>
					<TD class="table_cell1"><p align="center">결제방법</TD>
					<TD class="table_cell1"><p align="center">가격</TD>
					<TD class="table_cell1"><p align="center">처리여부</TD>
					<TD class="table_cell1"><p align="center">비고</TD>
				</TR>
				<TR>
					<TD colspan="9" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
				</TR>
<?
			$colspan=9;
			$curdate = date("YmdHi",mktime(date("H")-2,date("i"),0,date("m"),date("d"),date("Y")));
			$curdate5 = date("Ymd",mktime(0,0,0,date("m"),date("d")-5,date("Y")));
			$cnt=0;
			if(strlen($search)>0 || strlen($searchprice)>0) {
				$page_numberic_type=1;
				while($row=mysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
					$cnt++;
					$ordercode=$row->ordercode;
					$name=$row->sender_name;
					if(substr($row->ordercode,20)=="X") {	//비회원
						$strid = substr($row->id,1,6);
					} else {	//회원
						$strid = "<A HREF=\"javascript:MemberView('".$row->id."');\"><FONT COLOR=\"blue\">".$row->id."</FONT></A>";
					}
					$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";

					if (preg_match("/^(N|C|R|D)$/", $row->deli_gbn)) {
						if (preg_match("/^(O|Q){1}/", $row->paymethod) && strlen($row->bank_date)==0 && substr($row->ordercode,0,8)<=$curdate5) {	//가상계좌의 경우 미입금된 데이터에 대해서 5일이 지났을 경우 삭제
							#삭제가능
							$strdel = "<a href=\"javascript:OrderDelete('".$row->ordercode."');\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></a>";
							$delgbn="Y";
						} else if($row->deli_gbn!="C" && preg_match("/^(C|V){1}/", $row->paymethod) && substr($row->ordercode,0,12)>$curdate) { //주문취소가 아니고, 카드/계좌이체 건에 대해서 2시간 이전 데이터는 삭제 불가능
							#삭제 불가능
							$strdel = "<font color=#3D3D3D>--</font></td>";
							$delgbn="N";
						} else {
							if (preg_match("/^(Q|P){1}/", $row->paymethod) && $row->deli_gbn!="C") {	//매매보호 가상계좌/신용카드는 취소전엔 삭제가 불가능
								#삭제 불가능
								$strdel = "<font color=#3D3D3D>--</font></a>";
								$delgbn="N";
							} else if (strcmp($row->pay_flag,"0000")==0 && $row->pay_admin_proc!="C" && !preg_match("/^(V|O|Q){1}/", $row->paymethod)) {//신용카드/휴대폰 결제건은 취소 후 삭제가 가능
								#결제 취소 후 삭제 가능합니다!!
								$strdel = "<a href=\"javascript:alert('결제 취소 후 삭제가 가능합니다.');\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></a>";
								$delgbn="N";
							} else {
								#삭제 가능
								$strdel = "<a href=\"javascript:OrderDelete('".$row->ordercode."');\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></a>";
								$delgbn="Y";
							}
						}
					} else {
						#삭제 불가능
						$strdel = "--";
						$delgbn="N";
					}

					echo "<tr>\n";
					echo "	<TD class=\"td_con2\"><p align=\"center\"><input type=checkbox name=chkordercode value=\"".$delgbn.$row->ordercode."\"></td>\n";
					echo "	<TD class=\"td_con1\"><p align=\"center\"><A HREF=\"javascript:OrderDetailView('".$row->ordercode."');\">".$number."</A></td>\n";
					echo "	<TD class=\"td_con1\"><p align=\"center\">".$date."</td>\n";
					echo "	<TD class=\"td_con1\"><p align=\"center\"><A HREF=\"javascript:SenderSearch('".$name."');\">".$name."</A></p></td>\n";
					echo "	<TD class=\"td_con1\"><p align=\"center\"><span class=\"font_orange\"><b>".$strid."</b></span>
						/ <A HREF=\"javascript:OrderDetailView('".$row->ordercode."');\">{$row->ordercode}</a>
					</TD>\n";
					echo "	<TD class=\"td_con1\"><p align=\"center\"><b>".$arpm[substr($row->paymethod,0,1)]." ";
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
					echo "	</b></TD>\n";
					echo "	<TD class=\"td_con1\"><p align=\"right\"><b>".number_format($row->price)."&nbsp;</b></p></td>\n";
					echo "	<TD class=\"td_con1\"><p align=\"center\">&nbsp;";
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
					echo "	&nbsp;</p></td>\n";
					echo "	<TD class=\"td_con1\"><p align=\"center\">".$strdel."</p></td>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<TD colspan=\"9\" width=\"760\" background=\"images/table_con_line.gif\"><img src=\"images/table_con_line.gif\" width=\"4\" height=\"1\" border=\"0\"></TD>\n";
					echo "</tr>\n";
				}
				mysql_free_result($result);
			}

			if ($cnt==0) {
				$page_numberic_type="";
				echo "<tr><td class=\"td_con2\" colspan=".$colspan." align=center>검색된 주문내역이 없습니다.</td></tr>";
			}
?>
				<TR>
					<TD background="images/table_top_line.gif" width="761" colspan="9"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;"><p align="left"><a href="javascript:OrderDeliPrint();"><img src="images/btn_print.gif" width="127" height="38" border="0" hspace="1"></a>&nbsp;<a href="javascript:OrderCheckPrint();"><img src="images/btn_juprint.gif" width="127" height="38" border="0" hspace="0"></a>&nbsp;<a href="javascript:OrderCheckExcel();"><img src="images/btn_excel1.gif" width="127" height="38" border="0" hspace="1"></a>&nbsp;<a href="javascript:OrderSendSMS();"><img src="images/btn_sms.gif" width="127" height="38" border="0"></a></td>
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

		} else if($gong_gbn=="Y") {	//공동구매
?>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" width="761" colspan="9"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><p align="center">No</TD>
					<TD class="table_cell1"><p align="center">주문일자</TD>
					<TD class="table_cell1"><p align="center">주문자</TD>
					<TD class="table_cell1"><p align="center">ID</TD>
					<TD class="table_cell1"><p align="center">상품명</TD>
					<TD class="table_cell1"><p align="center">가격</TD>
				</TR>
				<TR>
					<TD colspan="6" width="760" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
				</TR>
<?
			$colspan=6;
			if(strlen($search)>0) {
				$sql = "SELECT a.gong_seq,a.gong_name,a.start_price,a.down_price,a.mini_price,a.count, ";
				$sql.= "a.bid_cnt,b.id,b.name,b.email,b.date FROM tblgonginfo a, tblgongresult b ";
				$sql.= "WHERE a.gong_seq=b.gong_seq AND b.process_gbn='I' AND b.name LIKE '%".$search."%' ";
				$result=mysql_query($sql,get_db_conn());
				$rows=mysql_num_rows($result);
				$cnt=0;
				while($row=mysql_fetch_object($result)) {
					$number=$rows-$cnt;
					$cnt++;
					$date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
					$num=intval($row->bid_cnt/$row->count);
					$price=$row->start_price-($num*$row->down_price);
					if($price<$row->mini_price) $price=$row->mini_price;
					$price=number_format($price)."원";

					echo "<tr>\n";
					echo "	<TD class=\"td_con2\"><p align=\"center\">".$number."</td>\n";
					echo "	<TD class=\"td_con1\"><p align=\"center\">".$date."</td>\n";
					echo "	<TD class=\"td_con1\"><p align=\"center\">&nbsp;<A HREF=\"javascript:alert('".$row->email."');\">".$row->name."</A>&nbsp;</td>\n";
					echo "	<TD class=\"td_con1\"><p align=\"center\">&nbsp;<A HREF=\"javascript:MemberView('".$row->id."');\"><FONT COLOR=\"blue\">".$row->id."</FONT></A>&nbsp;</td>\n";
					echo "	<TD class=\"td_con1\"><p align=\"left\"><nobr>&nbsp;<A HREF=\"javascript:ViewGong('".$row->gong_seq."');\">".$row->gong_name."</A>&nbsp;</td>\n";
					echo "	<TD class=\"td_con1\"><p align=\"right\">".$price."&nbsp;</td>\n";
					echo "</tr>\n";
				}
				mysql_free_result($result);
			}
			if ($cnt==0) {
				echo "<tr><td class=\"td_con2\" colspan=".$colspan." align=center>검색된 내역이 없습니다.</td></tr>";
			}
			echo "<TR><TD background=\"images/table_top_line.gif\" width=\"761\" colspan=\"9\"></TD></TR>";
		}
?>
				</table>
				</td>
			</tr>
			<input type=hidden name=tot value="<?=$cnt?>">
			</form>

			<form name=detailform method="post" action="order_detail.php" target="orderdetail">
			<input type=hidden name=ordercode>
			</form>

			<form name=idxform action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=searchtype value="<?=$searchtype?>">
			<input type=hidden name=gong_gbn value="<?=$gong_gbn?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=searchprice value="<?=$searchprice?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=deli_gbn value="<?=$deli_gbn?>">
			</form>

			<form name=smsform action="sendsms.php" method=post target="sendsmspop">
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=sender_form action="order_namesearch.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=printform action="order_print_pop.php" method=post target="ordercheckprint">
			<input type=hidden name=ordercodes>
			<input type=hidden name=gbn>
			</form>

			<form name=gong action="gong_gongchangelist.php" method=post>
			<input type=hidden name=gong_seq>
			</form>

			<form name=checkexcelform action="order_excel.php" method=post>
			<input type=hidden name=ordercodes>
			</form>

			<form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">이름/가격별 외 주문조회</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 주문자 이름 및 주문가격 등으로 주문현황 및 주문내역을 확인하실 수 있습니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 주문번호를 클릭하면 <b>주문상세내역</b>이 출력되며, 주문내역 확인 및 주문 처리가 가능합니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 에스크로(결제대금 예치제) 결제의 경우는 주문후 미입금시 5일뒤에 삭제가 가능합니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 카드실패 주문건은 2시간후에 삭제가 가능합니다.</p></td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">이름/가격별 외 주문조회 부가기능</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 운송장출력 : 체크된 주문건의 운송장을 일괄 출력합니다.(현재 서비스 준비중에 있습니다.)</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 주문서출력 : 체크된 주문건을 소비자용 주문서로 일괄 출력합니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 엑셀다운로드 : 체크된 주문건을 엑셀파일 형식으로 다운로드 받습니다.<br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;엑셀 주문서 항목 조절은 <a href="javascript:parent.topframe.GoMenu(5,'order_excelinfo.php');"><span class="font_blue">주문/매출 > 주문조회 및 배송관리 > 주문리스트 엑셀파일 관리</span></a> 에서 가능합니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- SMS 발송 : 체크된 모든 주문건에 대해 SMS 메제시가 발송며 중복된 휴대폰 번호는 1개로 간주됩니다.<br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;매크로를 사용하여 구매고객의 이름으로 SMS가 발송도 가능합니다. 예) [NAME] ====> 고객님</p></td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">이름/가격별 외 주문조회 주의사항</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 전화번호 : 전화번호로 조회시에 주문자의 전화번호만 검색합니다.<br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						전화번호 입력시 포함된 "-"으로인해 조회가 용이하지 않을 경우, 전화번호의 뒤 4자리로만 검색하시면 됩니다.<br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						예) 02-123-1234, 021231234 -> 1234로 검색
						</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 구매가격 : 가격으로 조회시 무통장 결제 중 미처리된 건에 대해서만 조회를 합니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 공동구매 : 공동구매 조회시 이름으로만 조회가 가능합니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 아이디 : 아이디로 조회시 해당 아이디를 정확히 입력하셔야만 조회가 가능합니다.<br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						예) 아이디 shoppingmall 의 경우 shopping으로는 조회가 안됨</p></td>
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
<?=$onload?>

<? INCLUDE "copyright.php"; ?>