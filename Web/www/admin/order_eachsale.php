<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-2";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$prcode=$_POST["prcode"];
if(strlen($prcode)==18) {
	$code=substr($prcode,0,12);
	$codeA=substr($code,0,3);
	$codeB=substr($code,3,3);
	$codeC=substr($code,6,3);
	$codeD=substr($code,9,3);
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script language="JavaScript">
var code="";
var prcode="";

function CodeProcessFun(_code) {
	if(_code=="out" || _code.length==0 || _code=="000000000000") {
		document.all["code_top"].style.background="#dddddd";
		selcode="";
		seltype="";

		if(_code!="out") {
			BodyInit('');
		} else {
			_code="";
		}
	} else {
		document.all["code_top"].style.background="#ffffff";
		BodyInit(_code);
	}

	if(selcode.length==12 && selcode!="000000000000") {
		document.form2.mode.value="";
		document.form2.code.value=selcode;
		document.form2.target="ListFrame";
		document.form2.action="order_eachsale.list.php";
		document.form2.submit();
	}
	prcode="";
}

</script>

<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 200;HEIGHT: 224;}
</STYLE>
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" width="16" height="12" border="0" valign=absmiddle>현재위치 : 주문/매출 &gt; 장바구니 및 매출 분석 &gt; <span class="2depth_select">개별상품 매출분석</span></td>
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
					<TD><IMG SRC="images/order_eachsale_title.gif" ALT=""></TD>
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
					<TD width="100%" class="notice_blue"><p>개별상품의 매출정보를 확인하실 수 있습니다.</p></TD>
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
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=code>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/order_basket_stitle1.gif" WIDTH="134" HEIGHT=31 ALT=""></TD>
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
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif"  colspan="3"><img src="images/table_top_line.gif" width="150" height="1" border="0"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="216" align="center"><p><b>상품카테고리 목록</b></p></TD>
					<TD class="table_cell1" width="43" align="center">&nbsp;</TD>
					<TD class="table_cell1"  align="center" background="images/blueline_bg.gif"><p><b><font color="#555555">등록된 상품목록</span></b></p></TD>
				</TR>
				<TR>
					<TD colspan="3"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
				</TR>
				<TR>
					<td width="222" height="100%" align=center valign=top style="padding-left:5px;padding-right:5px;">
					<DIV class=MsgrScroller id=contentDiv style="width=99%;height:394px;OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
					<DIV id=bodyList>
					<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor=FFFFFF>
					<tr>
						<td height=18><IMG SRC="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('out');">최상위 카테고리</span></td>
					</tr>
					<tr>
						<!-- 상품카테고리 목록 -->
						<td id="code_list" nowrap valign=top></td>
						<!-- 상품카테고리 목록 끝 -->
					</tr>
					</table>
					</DIV>
					</DIV>
					</TD>
					<TD class="td_con1" width="50" align="center"><img src="images/btn_next.gif" width="31" height="31" border="0" vspace="2"></TD>
					<TD class="td_con1" width="458" align="center" valign="top" height="100%"><IFRAME name="ListFrame" src="order_eachsale.list.php" width=100% height="100%" height=350 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></TD>
				</TR>
				<TR>
					<TD background="images/table_top_line.gif"  colspan="3"><img src="images/table_top_line.gif" width="150" height="1" border="0"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=15></td>
			</tr>
			<tr>
				<td><IFRAME name="AddFrame" src="order_eachsale.result.php" width=100% height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
			</tr>
			<tr>
				<td align="center"><p>&nbsp;</p></td>
			</tr>
			<IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
			</form>
			<form name=form2 action="" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
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
						<td ><span class="font_dotline">개별상품 매출분석</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top" style="letter-spacing:-0.5pt;"><p>- 주문리스트에 등록되어 있는 주문건을 기준으로 산출되며 배송/반송/미처리로 구분되어 출력됩니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top" style="letter-spacing:-0.5pt;"><p>- 카테고리 또는 상품을 선택후 기간별/지역별/성별/회원별/결제방법별 검색이 가능합니다.<br>
						<b>&nbsp;&nbsp;</b>지역별 검색의 경우 배송정보의 지역을 기준으로 합니다.<br>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">카테고리 및 상품 지정 검색방법</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top" style="letter-spacing:-0.5pt;"><p>① 특정 카테고리에 포함된 모든 상품의 매출을 분석시 해당 카테고리를 선택후 조회하기 버튼을 누르시면 됩니다.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top" style="letter-spacing:-0.5pt;"><p>② 특정 상품의 매출을 분석시 해당 상품을 선택후 조회하기 버튼을 누르시면 됩니다.</p></td>
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
<?
$sql = "SELECT * FROM tblproductcode WHERE type!='T' AND type!='TX' AND type!='TM' AND type!='TMX' ";
$sql.= "ORDER BY sequence DESC ";
include ("codeinit.php");
?>

<? INCLUDE "copyright.php"; ?>