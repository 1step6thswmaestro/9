<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "go-1";
$MenuCode = "gong";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$imagepath=$Dir.DataDir."shopimages/product/";

function getDeligbn($arrdeli,$strdeli,$true=true) {
	$tempdeli=$arrdeli;
	$res=true;
	while(@list($key,$val)=@each($tempdeli)) {
		if($true==true) {
			if(!preg_match("/^(".$strdeli.")$/", $val)) {
				$res=false;
				break;
			}
		} else {
			if(preg_match("/^(".$strdeli.")$/", $val)) {
				$res=false;
				break;
			}
		}
	}
	return $res;
}

$productcode=$_POST["prcode"];
if(!$productcode){
	echo "<html></head><body onload=\"alert('잘못된 url 접근입니다.');history.back();\"></body></html>";
	exit;
}

if (strlen($productcode)>0) {
	$sql = "SELECT * FROM tblproduct P Left OUTER join tblproduct_social S ON P.productcode = S.pcode ";
	$sql.= "WHERE 1=1  ";
	$sql.= "AND productcode = '".$productcode."' ";
	$result = mysql_query($sql,get_db_conn());
	if ($_data = mysql_fetch_object($result)) {
		$social_pname = $_data->productname;
		$social_startdate = $_data->sell_startdate;
		$social_enddate = $_data->sell_enddate;
		$CurrentTime = $social_enddate;
	}
}

if(!$CurrentTime)
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

$search_start=$search_start?$search_start:date("Y-m-d",$social_startdate);
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (mktime(0,0,0,$tempend[1],$tempend[2],$tempend[0])-mktime(0,0,0,$tempstart[1],$tempstart[2],$tempstart[0]))/86400;
if ($termday>366) {
	echo "<script>alert('검색기간은 1년을 초과할 수 없습니다.');location='".$_SERVER[PHP_SELF]."';</script>";
	exit;
}

$qry_from = "tblorderinfo a ,tblorderproduct b ";
if(substr($search_s,0,8)==substr($search_e,0,8)) {
	$qry.= "WHERE a.ordercode LIKE '".substr($search_s,0,8)."%' ";
} else {
	$qry.= "WHERE a.ordercode>='".$search_s."' AND a.ordercode <='".$search_e."' ";
}
$qry.= "AND a.ordercode=b.ordercode ";
$qry.= "AND NOT (b.productcode LIKE 'COU%' OR b.productcode LIKE '999999%') ";
$qry.= "AND b.productcode='".$productcode."' ";


if(strlen($deli_gbn)>0)	$qry.= "AND a.deli_gbn='".$deli_gbn."' ";
if($paystate=="Y") {		//입금
	$qry.= "AND ((MID(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (MID(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000')) ";
} else if($paystate=="B") {	//미입금
	$qry.= "AND ((MID(a.paymethod,1,1) IN ('B','O','Q') AND (a.bank_date IS NULL OR a.bank_date='')) OR (MID(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag!='0000' AND a.pay_admin_proc='C')) ";
} else if($paystate=="C") {	//환불
	$qry.= "AND ((MID(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=9) OR (MID(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_flag='0000' AND a.pay_admin_proc='C')) ";
}
if(strlen($search)>0) {
	if($s_check=="cd") $qry.= "AND a.ordercode='".$search."' ";
	else if($s_check=="pn") {
		$qry.= "AND a.ordercode=b.ordercode ";
		$qry.= "AND NOT (b.productcode LIKE 'COU%' OR b.productcode LIKE '999999%') ";
		$qry.= "AND b.productname LIKE '%".$search."%' ";
		$qry_from.= ",tblorderproduct b";
	}
	else if($s_check=="mn") $qry.= "AND a.sender_name='".$search."' ";
	else if($s_check=="mi") $qry.= "AND a.id='".$search."' ";
	else if($s_check=="cn") $qry.= "AND a.id LIKE 'X".$search."%' ";
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

$t_count=0;
$t_price=0;
$sql = "SELECT COUNT(DISTINCT(a.ordercode)) as t_count FROM ".$qry_from." ".$qry." ";
$result = mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$t_count=$row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;


$sql = "SELECT vendercnt FROM tblshopcount ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$vendercnt=$row->vendercnt;
mysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	mysql_free_result($result);
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">
<?if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?}?>

function searchForm() {
	document.form1.action="social_order_list.php";
	document.form1.submit();
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600");
	document.detailform.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

function GoOrderby(orderby) {
	document.idxform.block.value = "";
	document.idxform.gotopage.value = "";
	document.idxform.orderby.value = orderby;
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

function ReserveInOut(id){
	window.open("about:blank","reserve_set","width=245,height=140,scrollbars=no");
	document.reserveform.target="reserve_set";
	document.reserveform.id.value=id;
	document.reserveform.type.value="reserve";
	document.reserveform.submit();
}

var clickno=0;
function MemoMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	obj._tid = setTimeout("MemoView(WinObj)",200);
}
function MemoView(WinObj) {
	WinObj.style.visibility = "visible";
}
function MemoMouseOut(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	WinObj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

function ProductMouseOver(Obj) {
	obj = event.srcElement;
	WinObj=document.getElementById(Obj);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.display = "";

}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	WinObj = document.getElementById(Obj);
	WinObj.style.display = "none";
	clearTimeout(obj._tid);
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

function OrderCheckDelete() {
	document.idxform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked==true) {
			if(document.form2.chkordercode[i].value.substring(0,1)=="N") {
				alert("삭제가 불가능한 주문서가 포함되어있습니다.");
				return;
			} else {
				document.idxform.ordercodes.value+=document.form2.chkordercode[i].value.substring(1)+",";
			}
		}
	}
	if(document.idxform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	if(confirm("선택하신 주문서를 삭제하시겠습니까? ")) {
		document.idxform.type.value="delete";
		document.idxform.submit();
	}
}

function ProductInfo(code,prcode,popup,chk) {
	document.form_reg.code.value=code;
	document.form_reg.prcode.value=prcode;
	document.form_reg.popup.value=popup;
	if (popup=="YES") {
		if(chk == "0") { document.form_reg.action="product_register.add.php";}
		else if(chk == "3") { document.form_reg.action="social_shopping2.php";}
		else {document.form_reg.action="product2_register.add.php";}
		document.form_reg.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_reg.action="product_register.php";
		document.form_reg.target="";
	}
	document.form_reg.submit();
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
			<? include ("menu_gong.php"); ?>
			</td>

			<td></td>
			<td valign="top">

<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 소셜·경매 &gt; 소셜쇼핑 &gt; <span class="2depth_select">판매종료 공동구매 주문서</span></td>
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
					<TD><IMG SRC="images/social_end_title.gif" ALT="판매종료 공동구매상품"></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
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
					<TD width="100%" class="notice_blue">
					▶ 판매상품 : <b><?=$social_pname?><br></b>
					▶ 판매기간 : <b><?=date("Y-m-d H:i:s",$social_startdate)?> ~ <?=date("Y-m-d H:i:s",$social_enddate)?><b>
					</TD>
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
				<td height="10"></td>
			</tr>
			<tr align="right">
				<td><a href="social_sell_result.php"><img src="images/boad_list1.gif" alt="목록보기"></a></td>
			</tr>
			<tr>
				<td height="10"></td>
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
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=prcode value="<?=$productcode?>">
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td  bgcolor="#EDEDED" style="padding:6pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">기간선택</TD>
							<TD class="td_con1">
							<input type=text name=search_start value="<?=$search_start?>" size=13 onfocus="this.blur();" OnClick="Calendar(this)"  class="input_selected"> ~ <input type=text name=search_end value="<?=$search_end?>" size=13 onfocus="this.blur();" OnClick="Calendar(this)"  class="input_selected">
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
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">결제상태</TD>
							<TD class="td_con1" ><select name="paystate" class="select">
<?
							$arps=array("\"\":전체선택","Y:입금","B:미입금","C:환불");
							for($i=0;$i<count($arps);$i++) {
								$tmp=split(":",$arps[$i]);
								echo "<option value=\"".$tmp[0]."\" ";
								if($tmp[0]==$paystate) echo "selected";
								echo ">".$tmp[1]."</option>\n";
							}
?>
							</select></TD>
						</TR>
						<TR>
							<TD colspan="2" background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">처리단계</TD>
							<TD class="td_con1" ><select name="deli_gbn" class="select">
<?
							$ardg=array("\"\":전체선택","S:발송준비","Y:배송","N:미처리","C:주문취소","R:반송","D:취소요청","E:환불대기","H:배송(정산보류)");
							for($i=0;$i<count($ardg);$i++) {
								$tmp=split(":",$ardg[$i]);
								echo "<option value=\"".$tmp[0]."\" ";
								if($tmp[0]==$deli_gbn) echo "selected";
								echo ">".$tmp[1]."</option>\n";
							}
?>
							</select></TD>
						</TR>
						<TR>
							<TD colspan="2" background="images/table_con_line.gif"></TD>
						</TR>
						<tr>
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">검색어</TD>
							<TD class="td_con1" ><select name="s_check" class="select">
							<option value="cd" <?if($s_check=="cd")echo"selected";?>>주문코드</option>
							<!--option value="pn" <?if($s_check=="pn")echo"selected";?>>상품명</option-->
							<option value="mn" <?if($s_check=="mn")echo"selected";?>>구매자성명</option>
							<option value="mi" <?if($s_check=="mi")echo"selected";?>>구매회원ID</option>
							<option value="cn" <?if($s_check=="cn")echo"selected";?>>비회원주문번호</option>
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
				<td style="padding-top:4pt;" align="right"><a href="javascript:searchForm();"><img src="images/botteon_search.gif" width="113" height="38" border="0"></a>&nbsp;<!-- <a href="javascript:OrderExcel();"><img src="images/btn_excel1.gif" width="127" height="38" border="0" hspace="1"></a>&nbsp;<a href="javascript:AddressPrint();"><img src="images/btn_adress.gif" width="130" height="38" border="0"></a> --></td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<tr>
				<td style="padding-bottom:3pt;">
<?
				$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");

				$sql = "SELECT a.* FROM ".$qry_from." ".$qry." ";
				$sql.= "GROUP BY a.ordercode ORDER BY a.ordercode ".$orderby." ";
				$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
				$result=mysql_query($sql,get_db_conn());

				$colspan=10;
				if($vendercnt>0) $colspan++;
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372"><img src="images/icon_8a.gif" width="13" height="13" border="0"><B>정렬 :
					<?if($orderby=="DESC"){?>
					<A HREF="javascript:GoOrderby('ASC');"><B><FONT class=font_orange>주문일자순↑</FONT></B></A>
					<?}else{?>
					<A HREF="javascript:GoOrderby('DESC');"><B><FONT class=font_orange>주문일자순↓</FONT></B></A>
					<?}?>
					</td>
					<td width="372" align="right"><img src="images/icon_8a.gif" width="13" height="13" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" width="13" height="13" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup[list_num])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				<col width=30></col>
				<col width=65></col>
				<col width=100></col>
				<?if($vendercnt>0){?>
				<col width=60></col>
				<?}?>
				<col width=></col>
				<col width=30></col>
				<col width=60></col>
				<col width=60></col>
				<col width=60></col>
				<col width=60></col>
				<col width=40></col>
				<input type=hidden name=chkordercode>
				<TR>
					<TD background="images/table_top_line.gif" width="761" colspan="<?=$colspan?>"></TD>
				</TR>
				<TR height=32>
					<TD class="table_cell5" align="center"><input type=checkbox name=allcheck onclick="CheckAll()"></TD>
					<TD class="table_cell6" align="center">주문일자</TD>
					<TD class="table_cell6" align="center">주문자 정보</TD>
					<?if($vendercnt>0){?>
					<TD class="table_cell6" align="center">입점업체</TD>
					<?}?>
					<TD class="table_cell6" align="center">상품명</TD>
					<TD class="table_cell6" align="center">수량</TD>
					<TD class="table_cell6" align="center">배송여부</TD>
					<TD class="table_cell6" align="center">결제금액</TD>
					<TD class="table_cell6" align="center">결제금액</TD>
					<TD class="table_cell6" align="center">처리단계</TD>
					<TD class="table_cell6" align="center">비고</TD>
				</TR>
				<TR>
					<TD colspan="<?=$colspan?>" background="images/table_con_line.gif"></TD>
				</TR>
<?
				$colspan=10;
				if($vendercnt>0) $colspan++;

				$curdate = date("YmdHi",mktime(date("H")-2,date("i"),0,date("m"),date("d"),date("Y")));
				$curdate5 = date("Ymd",mktime(0,0,0,date("m"),date("d")-5,date("Y")));
				$cnt=0;$imgcnt=0;
				$thisordcd="";
				$thiscolor="#FFFFFF";
				while($row=mysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);

					$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";
					$name=$row->sender_name;
					unset($stridX);
					unset($stridM);
					if(substr($row->ordercode,20)=="X") {	//비회원
						$stridX = substr($row->id,1,6);
					} else {	//회원
						$stridM = "<A HREF=\"javascript:MemberView('".$row->id."');\"><FONT COLOR=\"blue\">".$row->id."</FONT></A>";
					}
					if($thisordcd!=$row->ordercode) {
						$thisordcd=$row->ordercode;
						if($thiscolor=="#FFFFFF") {
							$thiscolor="#FEF8ED";
						} else {
							$thiscolor="#FFFFFF";
						}
					}

					$sql = "SELECT a.*, (select tinyimage from tblproduct b where a.productcode=b.productcode ) tinyimage  FROM tblorderproduct a WHERE ordercode='".$row->ordercode."' ";
					$sql.= "AND NOT (a.productcode LIKE 'COU%' OR a.productcode LIKE '999999%') ";
					if(strlen($search)>0 && $s_check=="pn") {
						$sql.= "AND a.productname LIKE '%".$search."%' ";
					}
/*
					$sql = "SELECT * FROM tblorderproduct WHERE ordercode='".$row->ordercode."' ";
					$sql.= "AND ordercode='".$row->ordercode."' ";
					$sql.= "AND NOT (productcode LIKE 'COU%' OR productcode LIKE '999999%') ";
					if(strlen($search)>0 && $s_check=="pn") {
						$sql.= "AND productname LIKE '%".$search."%' ";
					}
*/
					$result2=mysql_query($sql,get_db_conn());
					$jj=0;
					unset($prval);
					unset($arrdeli);
					while($row2=mysql_fetch_object($result2)) {
						$imgcnt++;
						if (strlen($row2->tinyimage)>0 && file_exists($imagepath.$row2->tinyimage)==true){
							$tinyImg= "<td class=\"td_con5\" align=center><img src='".$imagepath.$row2->tinyimage."' height=40 width=40 border=1 onMouseOver=\"ProductMouseOver('primage".$imgcnt."')\" onMouseOut=\"ProductMouseOut('primage".$imgcnt."');\">";
						} else {
							$tinyImg="<td class=\"td_con5\" align=center><img src=images/space01.gif >";
						}
						$tinyImg .="<div id=\"primage".$imgcnt."\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
						$tinyImg .=	"	<tr bgcolor=\"#FFFFFF\">\n";
						if (strlen($row2->tinyimage)>0 && file_exists($imagepath.$row2->tinyimage)==true){
							$tinyImg .=	"	<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$imagepath.$row2->tinyimage."\" border=\"0\"></td>\n";
						}
						$tinyImg .= "	</tr>\n";
						$tinyImg .= "	</table>\n";
						$tinyImg .= "</div></td>\n";

						$arrdeli[$row2->deli_gbn]=$row2->deli_gbn;
						if($jj>0) $prval.="<tr><td colspan=".($vendercnt>0?"5":"4")." height=1 bgcolor=#E7E7E7></tr>";
						$prval.="<tr>\n";
						if($vendercnt>0) {
							$prval.="	<td class=\"td_con5\" align=center style=\"font-size:8pt\">".(strlen($venderlist[$row2->vender]->vender)>0?"<B><a href=\"javascript:viewVenderInfo(".$row2->vender.")\">".$venderlist[$row2->vender]->id."</a></B>":"-")."</td>\n";
							$prval.=$tinyImg;
							$prval.="	<td class=\"td_con6\" style=\"font-size:8pt;padding:3;line-height:10pt\">".titleCut(58,$row2->productname)."";
						} else{
							$prval.=$tinyImg;
							$prval.="	<td class=\"td_con5\" style=\"font-size:8pt;padding:3;line-height:10pt\">".titleCut(58,$row2->productname)."";
						}
						if(substr($row2->productcode,-4)!="GIFT") {
							$prval.=" <a href=\"JavaScript:ProductInfo('".substr($row2->productcode,0,12)."','".$row2->productcode."','YES','".$row->gift."')\"><img src=images/newwindow.gif border=0 align=absmiddle></a>";
						}
						$prval.="	</td>\n";
						$prval.="	<td class=\"td_con6\" align=center style=\"font-size:8pt;\">".$row2->quantity."</td>\n";
						$prval.="	<td class=\"td_con6\" align=center style=\"font-size:8pt;padding:3\">";
						if($row->gift==0 || $row->gift==3) {
							switch($row2->deli_gbn) {
								case 'S': $prval.="발송준비";  break;
								case 'X': $prval.="배송요청";  break;
								case 'Y': $prval.="배송";  break;
								case 'D': $prval.="<font color=blue>취소요청</font>";  break;
								case 'N': $prval.="미처리";  break;
								case 'E': $prval.="<font color=red>환불대기</font>";  break;
								case 'C': $prval.="<font color=red>주문취소</font>";  break;
								case 'R': $prval.="반송";  break;
								case 'H': $prval.="배송(<font color=red>정산보류</font>)";  break;
							}
							if($row2->deli_gbn=="D" && strlen($row2->deli_date)==14) $prval.=" (배송)";
						}
						else {
							if($row->gift=='1') $prval.="<font style='color:#3399ff'>상품권<br />(선물하기)</font>";
							else if($row->gift=='2')  $prval.="<font style='color:#3399ff'>상품권<br />(본인구매)</font>";
						}
						$prval.="	</td>\n";
						$prval.="</tr>\n";
						$jj++;
					}
					mysql_free_result($result2);

					if (preg_match("/^(N|C|R|D)$/", $row->deli_gbn) && getDeligbn($arrdeli,"N|C|R|D",true)) {
						if (preg_match("/^(O|Q){1}/", $row->paymethod) && strlen($row->bank_date)==0 && substr($row->ordercode,0,8)<=$curdate5) {	//가상계좌의 경우 미입금된 데이터에 대해서 5일이 지났을 경우 삭제
							#삭제가능
							$strdel = "<a href=\"javascript:OrderDelete('".$row->ordercode."')\"><img src=\"images/bu_delete.gif\" border=\"0\" align=\"absmiddle\"></a>";
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
								$strdel = "<a href=\"javascript:alert('결제 취소 후 삭제가 가능합니다.')\"><img src=\"images/bu_delete.gif\" border=\"0\" align=\"absmiddle\"></a>";
								$delgbn="N";
							} else {
								#삭제 가능
								$strdel = "<a href=\"javascript:OrderDelete('".$row->ordercode."')\"><img src=\"images/bu_delete.gif\" border=\"0\" align=\"absmiddle\"></a>";
								$delgbn="Y";
							}
						}
					} else {
						#삭제 불가능
						$strdel = "--";
						$delgbn="N";
					}

					if($cnt>0)
					{
						echo "<tr>\n";
						echo "	<TD height=1 background=\"images/table_con_line.gif\" colspan=\"".$colspan."\"></TD>\n";
						echo "</tr>\n";
					}

					echo "<tr bgcolor=".$thiscolor." onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='".$thiscolor."'\">\n";
					echo "<td class=\"td_con5\" align=center style=\"font-size:8pt;line-height:10pt\"><input type=checkbox name=chkordercode value=\"".$delgbn.$row->ordercode."\"><br>".$number."</td>\n";
					echo "	<td class=\"td_con6\" align=center style=\"font-size:8pt;padding:3;line-height:11pt\"><A HREF=\"javascript:OrderDetailView('".$row->ordercode."')\">".$date."</A></td>\n";
					echo "	<td class=\"td_con6\" style=\"font-size:8pt;padding:3;line-height:11pt\">\n";
					echo "	주문자: <A HREF=\"javascript:SenderSearch('".$name."');\"><FONT COLOR=\"blue\">".$name."</font></A>";
					if(strlen($stridX)>0) {
						echo "<br> 주문번호: ".$stridX;
					} else if(strlen($stridM)>0) {
						echo "<br> 아이디: ".$stridM;
					}

					$order_msg=explode("[MEMO]",$row->order_msg);
					if(strlen($row->order_msg)>0 || $row->paymethod=="B") {
						echo "	<br> 메세지:&nbsp;<img src=\"images/btn_memo.gif\" width=\"35\" height=\"29\" border=\"0\" onMouseOver='MemoMouseOver($cnt)' onMouseOut=\"MemoMouseOut($cnt);\" align=\"absmiddle\">";
						echo "	<div id=memo".$cnt." style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
						echo "	<table width=400 border=0 cellspacing=0 cellpadding=0 bgcolor=#A47917>\n";
						echo "	<tr>\n";
						echo "		<td width=80 nowrap></td>\n";
						echo "		<td width=100%></td>\n";
						echo "	</tr>\n";
						if(strlen($order_msg[0])>0) {
							echo "	<tr>\n";
							echo "		<td align=right style=\"padding-right:5\"><font color=#ffffff>메 세 지 :</td>\n";
							echo "		<td style=\"padding-left:5;padding-right:10;line-height:12pt\"><font color=#FFFFFF>".strip_tags($order_msg[0])."</td>\n";
							echo "	</tr>";
						}
						if(strlen($order_msg[1])>0) {
							echo "	<tr><td colspan=2 height=10></td></tr>\n";
							echo "	<tr>\n";
							echo "		<td align=right style=\"padding-right:5\"><font color=#ffffff>주문메모 :</td>\n";
							echo "		<td style=\"padding-left:5;padding-right:10;line-height:12pt\"><font color=#FFFFFF>".strip_tags($order_msg[1])."</td>\n";
							echo "	</tr>";
						}
						if(strlen($order_msg[2])>0) {
							echo "	<tr><td colspan=2 height=10></td></tr>\n";
							echo "	<tr>\n";
							echo "		<td align=right style=\"padding-right:5\"><font color=#ffffff>알 리 미 :</td>\n";
							echo "		<td style=\"padding-left:5;padding-right:10;line-height:12pt\"><font color=#FFFFFF>".strip_tags($order_msg[2])."</td>\n";
							echo "	</tr>";
						}
						if($row->paymethod=="B") {
							echo "	<tr><td colspan=2 height=10></td></tr>\n";
							echo "	<tr>\n";
							echo "		<td align=right style=\"padding-right:5\"><font color=#ffffff>입금계좌 :</td>\n";
							echo "		<td style=\"padding-left:5;padding-right:10\"><font color=#FFFFFF>".$row->pay_data."</td>\n";
							echo "	</tr>\n";
						}
						echo "	</table>\n";
						echo "	</div>\n";
					}

					echo "	</td>\n";
					echo "	<td class=\"td_con6\" colspan=".($vendercnt>0?"4":"3")." height=100%>\n";
					echo "	<table border=0 cellpadding=0 cellspacing=0 width=100% height=100% style=\"table-layout:fixed\">\n";
					if($vendercnt>0) {
						echo "<col width=60></col>\n";
					}
					echo "	<col width=50></col>\n";
					echo "	<col width=></col>\n";
					echo "	<col width=30></col>\n";
					echo "	<col width=60></col>\n";
					echo $prval;
					echo "	</table>\n";
					echo "	</td>\n";
					echo "	<td class=\"td_con6\" align=center style=\"font-size:8pt;padding:3;line-height:12pt\">";
					echo $arpm[substr($row->paymethod,0,1)]."<br>";
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
					echo "	</td>\n";
					echo "	<td class=\"td_con6\" align=right style=\"font-size:8pt;padding:3\">".number_format($row->price)."</td>\n";
					echo "	<td class=\"td_con6\" align=center style=\"font-size:8pt;padding:3;line-height:11pt\">";
					switch($row->deli_gbn) {
						case 'S': echo "발송준비";  break;
						case 'X':
							if($row->gift=='1') echo "인증번호발송";
							else echo "배송요청";  break;
						case 'Y':
							if($row->gift=='1') echo "인증후적립완료";
							else if($row->gift=='2') echo "적립완료";
							else echo "배송";
						break;
						case 'D': echo "<font color=blue>취소요청</font>";  break;
						case 'N': echo "미처리";  break;
						case 'E': echo "<font color=red>환불대기</font>";  break;
						case 'C': echo "<font color=red>주문취소</font>";  break;
						case 'R': echo "반송";  break;
						case 'H': echo "배송(<font color=red>정산보류</font>)";  break;
					}
					if($row->deli_gbn=="D" && strlen($row->deli_date)==14) echo "<br>(배송)";
					if($row->deli_gbn=="R" && substr($row->ordercode,20)!="X") {
						echo "<br><button class=button2 style=\"width:45;color:blue\" onclick=\"ReserveInOut('".$row->id."');\">적립금</button>";
					}
					echo "	</td>\n";
					echo "	<td class=\"td_con6\" align=center>".$strdel."</td>\n";
					echo "</tr>\n";

					$cnt++;
				}
				mysql_free_result($result);
				if($cnt==0) {
					echo "<tr height=28 bgcolor=#FFFFFF><td colspan=".$colspan." align=center>조회된 내용이 없습니다.</td></tr>\n";
				}
?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="<?=$colspan?>"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;"><a href="javascript:OrderDeliPrint();"><img src="images/btn_print.gif" width="127" height="38" border="0" hspace="1"></a>&nbsp;<a href="javascript:OrderCheckPrint();"><img src="images/btn_juprint.gif" width="127" height="38" border="0" hspace="0"></a>&nbsp;<a href="javascript:OrderCheckExcel();"><img src="images/btn_excel1.gif" width="127" height="38" border="0" hspace="1"></a>&nbsp;<a href="javascript:OrderCheckDelete();"><img src="images/btn_judel.gif" width="127" height="38" border="0"></a></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
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
			<input type=hidden name=orderby value="<?=$orderby?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=paymethod value="<?=$paymethod?>">
			<input type=hidden name=paystate value="<?=$paystate?>">
			<input type=hidden name=deli_gbn value="<?=$deli_gbn?>">
			<input type=hidden name=prcode value="<?=$productcode?>">
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=sender_form action="order_namesearch.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=reserveform action="reserve_money.php" method=post>
			<input type=hidden name=type>
			<input type=hidden name=id>
			</form>

			<form name=printform action="order_print_pop.php" method=post target="ordercheckprint">
			<input type=hidden name=ordercodes>
			<input type=hidden name=gbn>
			</form>

			<form name=checkexcelform action="order_excel.php" method=post>
			<input type=hidden name=ordercodes>
			</form>

			<form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
			</form>

			<form name=form_reg action="product_register.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form>

			<?if($vendercnt>0){?>
			<form name=vForm action="vender_infopop.php" method=post>
			<input type=hidden name=vender>
			</form>
			<?}?>

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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="701"><span class="font_dotline">일자별 주문조회/배송</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 일자별 쇼핑몰의 모든 주문현황 및 주문내역을 확인/처리하실 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 주문번호를 클릭하면 <b>주문상세내역</b>이 출력되며, 주문내역 확인 및 주문 처리가 가능합니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 에스크로(결제대금 예치제) 결제의 경우는 주문후 미입금시 5일뒤에 삭제가 가능합니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 카드실패 주문건은 2시간후에 삭제가 가능합니다.</td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="701"><span class="font_dotline">일괄 처리 부가 기능</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 운송장출력 : 체크된 주문건의 운송장을 일괄 출력합니다.(현재 서비스 준비중에 있습니다.)</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 주문서출력 : 체크된 주문건을 소비자용 주문서로 일괄 출력합니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 엑셀다운로드 : 체크된 주문건을 엑셀파일 형식으로 다운로드 받습니다.<br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;엑셀 주문서 항목 조절은 <a href="javascript:parent.topframe.GoMenu(5,'order_excelinfo.php');"><span class="font_blue">주문/매출 > 주문조회 및 배송관리 > 주문리스트 엑셀파일 관리</span></a> 에서 가능합니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 주문서삭제 : 체크된 주문건을 일괄 삭제 합니다.</td>
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
</table>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>