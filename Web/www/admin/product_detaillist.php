<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$code=$_POST["code"];
$codes=$_POST["codes"];

$exposed_list_num = $_shopdata->exposed_list;
if(strlen($exposed_list_num)==0) $exposed_list_num=",0,2,3,4,5,6,7,19,";

if ($type=="insert" || $type=="delete" || $type=="sequence") {
	if ($type=="insert") {
		$exposed_list_num = $exposed_list_num.$code.",";
		$onload="<script>alert('해당 노출 항목을 추가하였습니다.');</script>";
	} else if ($type=="delete") {
		$exposed_list_num = ereg_replace(",".$code.",",",",$exposed_list_num);
		$onload="<script>alert('해당 노출 항목을 삭제하였습니다.');</script>";
	} else if ($type=="sequence") {
		$exposed_list_num=$codes;
		$onload="<script>alert('해당 노출 항목의 순서를 변경하였습니다.');</script>";
	}
	$sql = "UPDATE tblshopinfo SET exposed_list = '".$exposed_list_num."' ";
	mysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
}

$exposed_list_name = array("제조회사","원산지","시중가격","판매가격","적립금","특이사항","수량(삭제불가)","옵션(삭제불가)","상품명","해외 화폐 가격","모델명","출시일","사용자정의스펙1","사용자정의스펙2","사용자정의스펙3","사용자정의스펙4","사용자정의스펙5","브랜드","진열코드","패키지(삭제불가)");

$cnt = count($exposed_list_name);

$exposed_list_num2=substr($exposed_list_num,1,-1);
$ar_exposed_list_num = explode(",",$exposed_list_num2);
$cnt2 = count($ar_exposed_list_num);

if(strlen($blanknum)==0) $blanknum=1;
$exposed_list_num3=" ".$exposed_list_num;
while($num = strpos($exposed_list_num3,",O")){
	$tempnum=ereg_replace(",","",substr($exposed_list_num3,$num+2,2))+1;
	$exposed_list_num3=substr($exposed_list_num3,$num+2);
	if($tempnum>$blanknum) $blanknum=$tempnum;	
}

for($i=1;$i<$blanknum;$i++) $exposed_list_name["O$i"]="공백(셀 빈칸)";

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function SendMode(mode) {
	if (document.form1.outexp.selectedIndex==-1 && mode=="insert") {
		alert("노출 항목에 추가할 항목을 선택하세요.");
		return;
	} else if(document.form1.inexp.selectedIndex==-1 && mode=="delete") {
		alert("노출 항목에서 삭제할 항목을 선택하세요.");
		return;
	}
	if (mode=="insert") {
		if (confirm("노출 항목을 추가하시겠습니까?")) {
			document.form1.type.value=mode;
			document.form1.code.value=document.form1.outexp.options[document.form1.outexp.selectedIndex].value;
			document.form1.submit();
		}
	} else if (mode=="delete"){
		document.form1.code.value=document.form1.inexp.options[document.form1.inexp.selectedIndex].value;
		if (document.form1.code.value!=6 && document.form1.code.value!=7 && document.form1.code.value!=19) {
			if (confirm("노출 항목을 삭제하시겠습니까?")) {
				document.form1.type.value=mode;
				document.form1.submit();
			}
		} else if (document.form1.code.value==6){
			alert("수량은 삭제 불가능합니다.");
			return;
		} else if (document.form1.code.value==7){
			alert("옵션은 삭제 불가능합니다.");
			return;
		} else if (document.form1.code.value==19){
			alert("패키지는 삭제 불가능합니다.");
			return;
		}
	}
}

function move(gbn) {
	change_idx = document.form1.inexp.selectedIndex;
	if (change_idx<0) {
		alert("순서를 변경할 항목을 선택하세요.");
		return;
	}
	if (gbn=="up" && change_idx==0) {
		alert("선택하신 항목은 더이상 위로 이동되지 않습니다.");
		return;
	}
	if (gbn=="down" && change_idx==(document.form1.inexp.length-1)) {
		alert("선택하신 항목은 더이상 아래로 이동되지 않습니다.");
		return;
	}
	if (gbn=="up") idx = change_idx-1;
	else idx = change_idx+1;

	idx_value = document.form1.inexp.options[idx].value;
	idx_text = document.form1.inexp.options[idx].text;

	document.form1.inexp.options[idx].value = document.form1.inexp.options[change_idx].value;
	document.form1.inexp.options[idx].text = document.form1.inexp.options[change_idx].text;

	document.form1.inexp.options[change_idx].value = idx_value;
	document.form1.inexp.options[change_idx].text = idx_text;

	document.form1.inexp.selectedIndex = idx;
	document.form2.change.value="Y";
}

function MoveSave() {
	if (document.form2.change.value!="Y") {
		alert("순서변경을 하지 않았습니다.");
		return;
	}
	if (!confirm("현재의 순서대로 저장하시겠습니까?")) return;
	codes = "";
	for (i=0;i<=(document.form1.inexp.length-1);i++) {
		codes+=","+document.form1.inexp.options[i].value;
	}
	codes+=",";
	document.form2.codes.value = codes;
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
			<? include ("menu_product.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상품관리 &gt;카테고리/상품관리 &gt; <span class="2depth_select">상품 스펙 노출관리</span></td>
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





			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/product_detaillist_title.gif"  ALT=""></TD>
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
					<TD width="100%" class="notice_blue">상품 상세페이지에서 노출되는 각상품의  상세항목 순서를 변경할 수 있습니다.</TD>
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
			<tr><td height="20"></td></tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=code>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=3 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" align="center"><b>상세조건 노출관리</b></TD>
					<TD class="table_cell1" align="center" width="50">&nbsp;</TD>
					<TD class="table_cell1" align="center" background="images/blueline_bg.gif"><b><span class="font_blue">현재 노출중인 항목 </span></b></TD>
				</TR>
				<TR>
					<TD colspan="3" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD align="center" valign="top" style="padding:5pt;">
					<select name=outexp size=17 style="WIDTH:310px" size=17 class="select">
<?
					for($i=0;$i<$cnt;$i++){
						if(!ereg(",".$i.",",$exposed_list_num)){
							echo "<option value=\"".$i."\">".$exposed_list_name[$i]."\n";
						}
					}
					echo "<option value=\"O".$blanknum."\">공백(셀 빈칸)\n";
?>
					</select>
					</TD>
					<TD class="td_con1" align="center" width="50"><a href="javascript:SendMode('insert');"><img src="images/icon_nero1.gif" width="50" height="46" border="0"></a><br><br><a href="javascript:SendMode('delete');"><img src="images/icon_nero2.gif" width="50" height="46" border="0" vspace="10"></a></TD>
					<TD class="td_con1" align="center" valign="top"  style="padding:5pt;">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD width="100%">
						<select name=inexp size=17 style="WIDTH:310px" class="select">
<?
						for($i=0;$i<$cnt2;$i++){
							echo "<option value=\"".$ar_exposed_list_num[$i]."\">".$exposed_list_name[$ar_exposed_list_num[$i]]."\n";
						}
?>
						</select>
						</TD>
						<TD noWrap align=middle width=50>
						<table cellpadding="0" cellspacing="0" width="34">
						<TR>
							<TD align=middle><A href="JavaScript:move('up');"><IMG src="images/code_up.gif" align=absMiddle border=0 width="40" height="30" vspace="2"></A></td>
						</tr>
						<TR>
							<TD align=middle><IMG src="images/code_sort.gif" width="40" height="30"></td>
						 </tr>
						<TR>
							<TD align=middle><A href="JavaScript:move('down');"><IMG src="images/code_down.gif" align=absMiddle border=0 width="40" height="30" vspace="2"></A></td>
						</tr>
						<tr>
							<td height="20"></td>
						</tr>
						<TR>
							<TD align=middle><A href="JavaScript:MoveSave();"><IMG src="images/code_save.gif" align=absMiddle border=0 width="40" height="30" vspace="2"></A></td>
						</tr>
						</table>
						</TD>
					</TR>
					</TABLE>
					</TD>
				</TR>
				<TR>
					<TD background="images/table_top_line.gif" colspan="3"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			</form>
			<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type value="sequence">
			<input type=hidden name=codes>
			<input type=hidden name=change value="N">
			</form>
			<tr>
				<td height="30"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 HEIGHT=45 ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 HEIGHT=45 ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<TD><IMG SRC="images/manual_top2.gif" WIDTH=18 HEIGHT=45 ALT=""></TD>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<col width=20></col>
					<col width=></col>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">상품스펙 노출 관리</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 진열순서 조정 후 [저장하기] 를 클릭해야만 적용됩니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 상품진열 템플릿 선택시 가격고정형 공동구매를 사용할 경우 상품스펙기능은 지원되지 않습니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 상품스펙 노출설정을 했어도 해당 스펙에 대한 정보를 입력하지 않으면 출력되지 않습니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 상품스펙 [삭제하기]는 스펙출력에서 미출력되며 스펙에 입력한 정보는 삭제되지 않습니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 공백(셀 빈칸)은 스펙과 스펙사이 구분할때 사용합니다.</td>
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
			<tr><td height="50"></td></tr>
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