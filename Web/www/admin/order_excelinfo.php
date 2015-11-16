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

$excel_ok=$_shopdata->excel_ok;
$excel_info=$_shopdata->excel_info;

$mode=$_POST["mode"];
$etccode=$_POST["etccode"];
$up_excel_ok=$_POST["up_excel_ok"];
$codes=$_POST["codes"];
$change=$_POST["change"];

if($mode=="insert" || $mode=="delete" || $mode=="sequence") {
	if($mode=="insert" && strlen($etccode)>0) {
		$excel_info=$excel_info.$etccode.",";
		$onload="<script>alert(\"선택하신 항목을 다운되는 주문서 항목에 추가하였습니다.\");</script>";
	} else if($mode=="delete" && strlen($etccode)>0) {
		$excel_info=str_replace(",".$etccode.",",",",$excel_info);
		$onload="<script>alert(\"선택하신 항목을 다운되는 주문서 항목에서 삭제하였습니다.\");</script>";
	} else if($mode=="sequence") {
		$excel_info=$codes;
		$onload="<script>alert(\"다운되는 주문서 항목 순서를 변경하였습니다.\");</script>";
	}
	if(ereg(",24,"," ".$excel_info)) {
		$pattern = array("(,21,)","(,22,)","(,23,)","(,25,)","(,26,)");
		$replacement = array(",",",",",",",",",");
		$excel_info=preg_replace($pattern,$replacement,$excel_info); 
	}
	$sql = "UPDATE tblshopinfo SET excel_info='".$excel_info."' ";
	mysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
} else if($mode=="exceltype" && strlen($up_excel_ok)>0) {
	$sql = "UPDATE tblshopinfo SET excel_ok='".$up_excel_ok."' ";
	mysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$excel_ok=$up_excel_ok;
	$onload="<script>alert(\"주문서 출력형식을 변경하였습니다.\");</script>";
}

$excel_name = array(
"일자",
"주문자",
"주문자 전화(XXXXXXXX)",
"주문자 전화(XX-XXXX-XXXX)",
"이메일",
"주문ID/주문번호",
"결제방법",
"결제상태",
"결제방법(상태)",
"주문금액",
"처리여부",
"받는사람",
"전화번호 비상전화",
"전화번호(XXXXXXXX)",
"비상전화(XXXXXXXX)",
"전화번호(XX-XXXX-XXXX)",
"비상전화(XX-XXXX-XXXX)",
"우편번호(XXXXXX)",
"우편번호(XXX-XXX)",
"주소",
"전달사항",
"상품명",
"옵션(특징포함)",
"갯수",
"상품명1-갯수-옵션 ^ 상품명2-갯수-옵션",
"상품가격",
"상품 적립금",
"배송료",
"사용적립금",
"입금일",
"배송일",
"주문관련메모(관리자)",
"고객알리미",
"상품명1-갯수-옵션^상품명2-갯수-옵션",
"송장번호",
"거래번호",
"상품코드",
"은행계좌(카드내역)",
"옵션",
"특징",
"상품명(태그제거안함)",
"전달사항(태그제거안함)",
"일자(시분초 표시)",
"상품별 처리여부",
"상품별 주문메세지",
"상품별 배송일",
"진열코드",
"거래처정보");

$cnt = count($excel_name);
$excel_info2=substr($excel_info,1,-1);
$arexcel_info = explode(",",$excel_info2);
$cnt2 = count($arexcel_info);

if(strlen($blank_info)==0) $blank_info=1;
$excel_info3=" ".$excel_info;
while($num = strpos($excel_info3,",O")) {
	$temp_info=ereg_replace(",","",substr($excel_info3,$num+2,2))+1;
	$excel_info3=substr($excel_info3,$num+2);
	if($temp_info>$blank_info) $blank_info=$temp_info;
}

for($i=1;$i<$blank_info;$i++) $excel_name["O$i"]="공백(셀 빈칸)";

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(form) {
	if(form.up_excel_ok[0].checked==false && form.up_excel_ok[1].checked==false) {
		alert("주문서 출력 형식을 선택하세요.");
		form.up_excel_ok[1].focus();
		return;
	}
	form.mode.value="exceltype";
	form.submit();
}

function SendMode(mode) {
	if (document.form1.noest.selectedIndex==-1 && mode=="insert") {
		alert("다운 가능한 주문서 항목을 선택하세요.");
		return;
	} else if(document.form1.est.selectedIndex==-1 && mode=="delete") {
		alert("다운되는 주문서 항목을 선택하세요.");
		return;
	}
	if (mode=="insert") {
		if (confirm("선택된 주문서 항목을 다운되는 주문서 항목에 추가하시겠습니까?")) {
			document.form1.mode.value=mode;
			document.form1.etccode.value=document.form1.noest.options[document.form1.noest.selectedIndex].value;
			document.form1.submit();
		}
	} else if (mode=="delete"){
		document.form1.etccode.value=document.form1.est.options[document.form1.est.selectedIndex].value;
		if (confirm("선택된 주문서 항목을 삭제하시겠습니까?")) {
			document.form1.mode.value=mode;
			document.form1.submit();
		}
	}
}

function move(gbn) {
	change_idx = document.form1.est.selectedIndex;
	if (change_idx<0) {
		alert("순서를 변경할 주문서 항목을 선택하세요.");
		return;
	}
	if (gbn=="up" && change_idx==0) {
		alert("선택하신 주문서 항목은 더이상 위로 이동되지 않습니다.");
		return;
	}
	if (gbn=="down" && change_idx==(document.form1.est.length-1)) {
		alert("선택하신 주문서 항목은 더이상 아래로 이동되지 않습니다.");
		return;
	}
	if (gbn=="up") idx = change_idx-1;
	else idx = change_idx+1;

	idx_value = document.form1.est.options[idx].value;
	idx_text = document.form1.est.options[idx].text;

	document.form1.est.options[idx].value = document.form1.est.options[change_idx].value;
	document.form1.est.options[idx].text = document.form1.est.options[change_idx].text;

	document.form1.est.options[change_idx].value = idx_value;
	document.form1.est.options[change_idx].text = idx_text;

	document.form1.est.selectedIndex = idx;
	document.form2.change.value="Y";
}

function MoveSave() {
	if (document.form2.change.value!="Y") {
		alert("순서변경을 하지 않았습니다.");
		return;
	}
	if (!confirm("현재의 순서대로 저장하시겠습니까?")) return;
	codes = "";
	for (i=0;i<=(document.form1.est.length-1);i++) {
		codes+=","+document.form1.est.options[i].value;
	}
	document.form2.codes.value = codes+",";
	document.form2.submit();
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 주문/매출 &gt; 주문조회 및 배송관리 &gt; <span class="2depth_select">주문리스트 엑셀파일 관리</span></td>
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
					<TD><IMG SRC="images/order_excelinfo_title.gif" border="0"></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height=21></TD>
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
					<TD width="100%" class="notice_blue">주문리스트를 엑셀파일로 다운로드할 경우, 주문리스트의 각 항목 및 배열순서를 설정할 수 있습니다.</TD>
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
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/order_excelinfo_stitle1.gif" border="0"></TD>
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
			<input type=hidden name=mode>
			<input type=hidden name=etccode>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=3 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="326" align="center"><b>다운로드 가능한 주문리스트 항목</b></TD>
					<TD class="table_cell1" width="47" align="center">&nbsp;</TD>
					<TD class="table_cell1" width="337" align="center" background="images/blueline_bg.gif"><b><span class="font_blue">다운로드 되는 주문리스트 항목</span></b></TD>
				</TR>
				<TR>
					<TD colspan="3" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD width="346" align="center" valign="top" style="padding:8pt;"><select name=noest size=17 style="width:100%;" class="select">
<?
					for($i=0;$i<$cnt;$i++){
						if(!ereg(",".$i.",",$excel_info)){
							echo "<option value=\"".$i."\">".$excel_name[$i]."\n";
						}
					}
					echo "<option value=\"O".$blank_info."\">공백(셀 빈칸)\n";
?>
					</select></TD>
					<TD class="td_con1" width="55" align="center"><a href="javascript:SendMode('insert');"><img src="images/icon_nero1.gif" width="50" height="46" border="0" vspace="2"></a><br><br><a href="javascript:SendMode('delete');"><img src="images/icon_nero2.gif" width="50" height="46" border="0" vspace="2"></a></TD>
					<TD class="td_con1" width="345" align="center" valign="top">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD>
						<table cellpadding="8" cellspacing="0" width="290" bgcolor="#ededed">
						<tr>
							<td width="286">
							<select name=est size=17 style="width:320px" class="select">
<?
							for($i=0;$i<$cnt2;$i++){
								echo "<option value=\"".$arexcel_info[$i]."\">".$excel_name[$arexcel_info[$i]]."\n";
							}
?>
							</select>
							</td>
						</tr>
						</table>
						</TD>
						<TD noWrap align=middle width=50 align="center"><a href="javascript:move('up');"><img src="images/code_up.gif" width="40" height="30" border="0" vspace="0"></a><br><img src="images/code_sort.gif" width="40" height="30" border="0" vspace="2"><br><a href="javascript:move('down');"><img src="images/code_down.gif" width="40" height="30" border="0" vspace="0"></a><br><br><a href="javascript:MoveSave();"><img src="images/code_save.gif" width="40" height="30" border="0" vspace="2"></a></TD>
					</TR>
					</TABLE>
					</TD>
				</TR>
				<TR>
					<TD colspan=3 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="40">* 복수상품 주문건이나 주문내용이 1열 이상인 경우, 공통 항목을 반복 출력합니다.&nbsp;&nbsp;<input type=radio name=up_excel_ok value="Y" <?if($excel_ok=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none">예&nbsp;&nbsp;<input type=radio name=up_excel_ok value="N" <?if($excel_ok=="N")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none">아니요</td>
			</tr>
			<TR>
				<TD  background="images/table_con_line.gif"></TD>
			</TR>
			<tr>
				<td align="center" height="10"></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm(document.form1);"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>
			</form>
			<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=mode value="sequence">
			<input type=hidden name=codes>
			<input type=hidden name=change value="N">
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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">주문리스트 엑셀파일 관리</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top" style="letter-spacing:-0.5pt;">- 주문리스트 엑셀 백업시 원하는 타입으로 각 항목 및 배열순서를 조정한 후 [저장하기] 버튼을 눌러 적용합니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top" style="letter-spacing:-0.5pt;">- 주문리스트 항목중 [상품명1-갯수-옵션^상품명2-갯수-옵션] 항목과 [상품명], [옵션], [갯수], [가격] 항목은 동시 적용이 불가능합니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top" style="letter-spacing:-0.5pt;">- 복수상품 주문건이나 주문내용이 1열 이상인 경우, 열마다 동일한 항목을 반복 출력할지, 공란으로 출력할지 설정합니다.</td>
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