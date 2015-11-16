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

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}

$s_check=$_POST["s_check"];
if(strlen($s_check)==0) $s_check="1";
if(!preg_match("/^(1|3|6|12|99)$/", $s_check)) {
	$s_check="1";
}
$search=$_POST["search"];
$prcode=$_POST["prcode"];

$type=$_POST["type"];
$ordercodes=substr($_POST["ordercodes"],0,-1);

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
function CheckForm() {
	if(document.form1.prcode.selectedIndex==-1) {
		alert("조회된 상품을 선택하세요.");
		document.form1.prcode.focus();
		return;
	}
	document.form1.action="order_monthsearch.php";
	document.form1.submit();
}

function CheckSearch() {
	if(document.form1.search.value.length==0) {
		alert("조회하실 상품명을 입력하세요.");
		document.form1.search.focus();
		return;
	}
	document.form1.prcode.selectedIndex=-1;
	document.form1.submit();
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" width="16" height="12" border="0" valign=absmiddle>현재위치 : 주문/매출 &gt; 주문조회 및 배송관리 &gt; <span class="2depth_select">개월별 상품명 주문조회</span></td>
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
					<TD><IMG SRC="images/order_monthsearch_title.gif" ></TD>
					</tr><tr>
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
					<TD width="100%" class="notice_blue"><p>해당 상품을 주문한 주문서를 확인하실 수 있습니다.</p></TD>
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
							<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0"><b>상품명으로 검색</b></TD>
							<TD class="td_con1" >
							<table cellpadding="0" cellspacing="0" width="98%">
							<tr>
								<td height="18"><select name="s_check" class="select_selected">
								<option value="1" <?if($s_check=="1")echo"selected";?>>1개월 이내 주문</option>
								<option value="3" <?if($s_check=="3")echo"selected";?>>3개월 이내 주문</option>
								<option value="6" <?if($s_check=="6")echo"selected";?>>6개월 이내 주문</option>
								<option value="12" <?if($s_check=="12")echo"selected";?>>1년 이내 주문</option>
								<option value="99" <?if($s_check=="99")echo"selected";?>>1년 이후 주문</option>
								</select></td>
								<td width="100%" height="18" style="padding-left:5px;"><input type=text name=search value="<?=$search?>" size=50 STYLE="WIDTH:99%" class="input"></td>
								<td width="74" height="18"><p align="right"><a href="javascript:CheckSearch();"><img src="images/icon_search1.gif" width="74" height="25" border="0" hspace="0"></a></td>
							</tr>
							<tr>
								<td  height="18" colspan="3" class="font_orange" style="padding-top:4pt;"><p>* 조회하실 상품명을 입력하시고 상품조회버튼을 클릭해주세요!</p></td>
							</tr>
							</table>
							</TD>
						</TR>
						<TR>
							<TD colspan="2"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
						</TR>
						<TR>
							<TD width="100%" colspan="2" style="padding:5pt;"><select name=prcode size=7 style="width:100%" class="select">
<?
			if($s_check!="99") {
				$date=date("YmdHis",mktime(0,0,0,date("m")-$s_check,date("d"),date("Y")));
			} else {
				$date=date("YmdHis",mktime(0,0,0,date("m"),date("d"),date("Y")-1));
			}

			if(strlen($search)>0) {
				$sql = "SELECT productcode,productname FROM tblorderproduct ";
				if($s_check!="99") {
					$sql.= "WHERE ordercode >= '".$date."' ";
				} else {
					$sql.= "WHERE ordercode <= '".$date."' ";
				}
				$sql.= "AND productname LIKE '%".$search."%' GROUP BY productcode ";
				$result=mysql_query($sql,get_db_conn());
				while($row=mysql_fetch_object($result)) {
					if($prcode==$row->productcode) {
						echo "<option value=\"".$row->productcode."\" selected>".$row->productname."</option>\n";
					} else {
						echo "<option value=\"".$row->productcode."\">".$row->productname."</option>\n";
					}
				}
				mysql_free_result($result);
			}
?>
							</select></TD>
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
				<td style="padding-top:4pt;"><p align="right"><a href="javascript:CheckForm();"><img src="images/botteon_search.gif" width="113" height="38" border="0" hspace="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<?		
		if(strlen($prcode)>0) {
			$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");
			$sql = "SELECT DISTINCT COUNT(*) as t_count FROM tblorderproduct ";
			if($s_check!="99") {
				$sql.= "WHERE ordercode >= '".$date."' ";
			} else {
				$sql.= "WHERE ordercode <= '".$date."' ";
			}
			$sql.= "AND productcode='".$prcode."' ";
			$result = mysql_query($sql,get_db_conn());
			$row = mysql_fetch_object($result);
			$t_count = (int)$row->t_count;
			mysql_free_result($result);
			$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

			$sql = "SELECT a.* FROM tblorderinfo a, tblorderproduct b ";
			$sql.= "WHERE a.ordercode=b.ordercode ";
			if($s_check!="99") {
				$sql.= "AND b.ordercode >= '".$date."' ";
			} else {
				$sql.= "AND b.ordercode <= '".$date."' ";
			}
			$sql.= "AND b.productcode='".$prcode."' ";
			$sql.= "GROUP BY b.ordercode ORDER BY b.ordercode DESC ";
			$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
			#echo $sql; exit;
			$result = mysql_query($sql,get_db_conn());
		} else {
			$t_count=0;
			$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
		}
?>
			<tr>
				<td style="padding-bottom:3pt;"><p align="right"><img src="images/icon_8a.gif" width="13" height="13" border="0">총 주문수 : <B><?=number_format($t_count)?></B>건&nbsp;&nbsp;<img src="images/icon_8a.gif" width="13" height="13" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup[list_num])?></b> 페이지</td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif"  colspan="9"></TD>
				</TR>
				<input type=hidden name=chkordercode>
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
		$curdate = date("YmdHi",mktime(date("H")-2,date("i"),0,date("m"),date("d"),date("Y")));
		$curdate5 = date("Ymd",mktime(0,0,0,date("m"),date("d")-5,date("Y")));
		$cnt=0;
		if(strlen($prcode)>0) {
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
				echo "	<TD class=\"td_con1\"><p align=\"center\"><span class=\"font_orange\"><b>".$strid."</b></span></TD>\n";
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
				echo "	</b></td>\n";
				echo "	<TD class=\"td_con1\"><p align=\"right\"><b>".number_format($row->price)."&nbsp;</b></p></TD>\n";
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
				//if($row->deli_gbn=="R" && substr($row->ordercode,20)!="X") {
				//	echo "&nbsp;&nbsp;<a href=\"javascript:ReserveInOut('".$row->id."');\"><img src=\"images/icon_pointi.gif\" width=\"50\" height=\"33\" border=\"0\" align=\"absmiddle\"></a>";
				//}
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
			echo "<tr><td class=\"td_con2\" colspan=\"9\" align=\"center\">검색된 주문내역이 없습니다.</td></tr>";
		}
?>
				<TR>
					<TD background="images/table_top_line.gif"  colspan="9"></TD>
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
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=prcode value="<?=$prcode?>">
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=sender_form action="order_namesearch.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=smsform action="sendsms.php" method=post target="sendsmspop">
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
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
						<td ><span class="font_dotline">개월별 상품명 주문조회</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 해당 상품을 주문한 주문서를 확인하실 수 있습니다.</p></td>
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
						<td ><span class="font_dotline">개월별 상품명 주문조회 부가기능</span></td>
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