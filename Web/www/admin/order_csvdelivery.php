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

$imagepath=$Dir.DataDir;

$mode=$_POST["mode"];
$deli_company=$_POST["deli_company"];
$upfile=$_FILES["upfile"];

if($mode=="upload" && strlen($upfile[name])>0 && $upfile[size]>0) {
	$ext = strtolower(substr($upfile[name],strlen($upfile[name])-3,3));
	if($ext=="csv") {
		$filename="excelupfile.txt";
		copy($upfile[tmp_name],$imagepath.$filename);
		chmod($imagepath.$filename,0664);
		$onload="<script>alert(\"엑셀파일 등록이 완료되었습니다.\\n\\n등록파일 배송처리를 하시기 바랍니다.\");</script>";
	} else {
		$onload="<script>alert(\"파일형식이 잘못되어 업로드가 실패하였습니다.\\n\\n등록 가능한 파일은 엑셀(CSV) 파일만 등록 가능합니다.\");</script>";
	}
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(form) {
	if(form.deli_company.value.length==0) {
		alert("택배업체를 선택하세요.");
		form.deli_company.focus();
		return;
	}
	if(form.upfile.value.length==0) {
		alert("등록할 엑셀(CSV) 파일을 선택하세요.");
		form.upfile.focus();
		return;
	}
	form.mode.value="upload";
	form.submit();
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
			<? include ("menu_order.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" width="16" height="12" border="0" valign=absmiddle>현재위치 : 주문/매출 &gt; 주문조회 및 배송관리 &gt; <span class="2depth_select">주문리스트 일괄배송 관리</span></td>
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
					<TD><IMG SRC="images/order_csvdelivery_title.gif" border="0"></TD>
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
					<TD width="100%" class="notice_blue"><p>다수 주문건의 배송정보를 엑셀파일로 만들어 주문리스트에 일괄 반영하는 기능입니다.</p></TD>
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
					<TD><IMG SRC="images/order_csvdelivery_stitle1.gif" border="0"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<form name=detailform method="post" action="order_detail.php" target="orderdetail">
			<input type=hidden name=ordercode>
			</form>

			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
			<input type=hidden name=mode>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" width="153"><img src="images/table_top_line.gif"></TD>
					<TD background="images/table_top_line.gif"  ></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">택배업체 선택</TD>
					<TD class="td_con1" ><select name="deli_company" class="select" style="width:130px">
					<option value="">택배업체 선택</option>
<?
			$sql = "SELECT code, company_name FROM tbldelicompany ";
			$result=mysql_query($sql,get_db_conn());
			while($row=mysql_fetch_object($result)) {
				echo "<option value=\"".$row->code."\">".$row->company_name."</option>\n";
			}
			mysql_free_result($result);
?>
					</select></td>
				</TR>
				<TR>
					<TD colspan="2"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">엑셀파일 등록</TD>
					<TD class="td_con1" ><input type=file name=upfile style="width:60%" class="input"> <span class="font_orange">＊엑셀(CSV) 파일만 등록 가능합니다.</span></TD>
				</TR>
				<TR>
					<TD background="images/table_top_line.gif" width="153"><img src="images/table_top_line.gif"></TD>
					<TD background="images/table_top_line.gif" ></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td align="center" height=10></td>
			</tr>
			<tr>
				<td align="center"><p><a href="javascript:CheckForm(document.form1);"><img src="images/btn_fileup.gif" width="113" height="38" border="0"></a></p></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
	
	<?if($mode=="upload"){?>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/order_csvdelivery_stitle.gif" border="0"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=50></col>
				<col width=140></col>
				<col width=140></col>
				<col width=140></col>
				<col width=></col>
				<TR>
					<TD height="1" colspan="5" bgcolor="#B9B9B9"></TD>
				</TR>
				<TR align="center">
					<TD class="table_cell">번호</TD>
					<TD class="table_cell1">주문일자</TD>
					<TD class="table_cell1">주문자</TD>
					<TD class="table_cell1">송장번호</TD>
					<TD class="table_cell1">상태</TD>
				</TR>
				<TR>
					<TD height="1" colspan="5" bgcolor="#EDEDED"></TD>
				</TR>
<?
			$filepath=$imagepath.$filename;
			$fp=@fopen($filepath, "r");
			$i=1;
			while(!feof($fp)) {
				$buffer=fgets($fp,4096);
				if(strlen($buffer)>3) {
					$field=explode(",",$buffer);
					$date=substr($field[0],0,4)."/".substr($field[0],4,2)."/".substr($field[0],6,2)." (".substr($field[0],8,2).":".substr($field[0],10,2).")";
					echo "<tr align=center>\n";
					echo "	<td class=td_con2>".$i."</td>\n";
					echo "	<td class=td_con1>".$date."</td>\n";
					echo "	<td class=td_con1>".$field[1]."</td>\n";
					echo "	<td class=td_con1>".$field[2]."</td>\n";
					echo "	<td class=td_con1><iframe src=\"order_csvdelivery.process.php?type=init&ordercode=".trim($field[0])."&deli_com=".$deli_company."&deli_name=".urlencode($deli_name)."&deli_num=".$field[2]."\" style='width=100%;height=28px;font-size=15px;border:0 solid #FFFFFF;' scrolling='no' frameborder='NO'></iframe></td>\n";
					echo "</tr>\n";
					echo "<tr><TD height=\"1\" colspan=\"5\" bgcolor=\"#EDEDED\"></TD></tr>\n";
					$i++;
				}
			}
?>
				<TR>
					<TD height="1" colspan="5" bgcolor="#B9B9B9"></TD>
				</TR>
				</table>
				</td>
			</tr>
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
					<TD background="images/manual_left1.gif"><IMG SRC="images/manual_left1.gif" WIDTH=15 HEIGHT="5" ALT=""></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">주문리스트 일괄배송 관리</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top" style="letter-spacing:-0.5pt;"><p>- 주문 처리시 각 주문 별로 배송처리를 하지 않고, 다수의 주문을 선배송 후 배송정보를 엑셀파일(CSV)로 작성하여 일괄 적용하는 기능입니다.</p></td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">주문리스트 일괄배송 방법</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top" style="letter-spacing:-0.5pt;"><p>- ① 아래의 형식을 참고로 일괄배송 가능한 엑셀파일(확장자 CSV)을 작성합니다.<br>
						<b>&nbsp;&nbsp;</b><span class="font_orange">------------ 일괄배송 엑셀(CSV) 형식 ------------</span><br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_blue">주문번호,주문자,송장번호</span><br>
						<b>&nbsp;&nbsp;</b><span class="font_orange">--------------------------------------------------</span><br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;20070307154752877166,홍길동,11223344<br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;20070307160323501849,홍길동,55667788<br>
						<b>&nbsp;&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;20070307160929925273,홍길동,99001122<br>
						<b>&nbsp;&nbsp;</b><span class="font_orange">--------------------------------------------------</span>
						</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top" style="letter-spacing:-0.5pt;"><p>- ② 쇼핑몰에서 이용하는 택배업체 선택후, 작성한 배송정보 엑셀파일(csv)을 업로드합니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top" style="letter-spacing:-0.5pt;"><p>- ③ 파일을 등록하면 등록된 내용이 [배송정보 리스트]에 출력됩니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top" style="letter-spacing:-0.5pt;"><p>- ④ [배송처리하기] 버튼을 이용하여 적용합니다.</p></td>
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