<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-5";
$MenuCode = "product";
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

$regdate = $_shopdata->regdate;
$CurrentTime = time();
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m",$CurrentTime)."-01";
$period[4] = date("Y",$CurrentTime)."-01-01";
$type=$_REQUEST["type"];
$reviewtype=$_REQUEST["reviewtype"];
$search_start=$_REQUEST["search_start"];
$search_end=$_REQUEST["search_end"];
$vperiod=(int)$_REQUEST["vperiod"];
$search=$_REQUEST["search"];
$date=$_REQUEST["date"];
$productcode=$_REQUEST["productcode"];
$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";
$s_check=$_REQUEST["s_check"];
$popup=$_REQUEST["popup"];

if(!$s_check) $s_check="0";

if($s_check=="2") {
	$search="";
	$search_style="disabled style=\"background:#f4f4f4\"";
}
${"check_s_check".$s_check} = "checked";
${"check_vperiod".$vperiod} = "checked";


$sql = "SELECT review_type FROM tblshopinfo ";
$result = mysql_query($sql,get_db_conn());
$row = mysql_fetch_object($result);
$review_type = $row->review_type;
if($row->review_type=="N") {
	echo "<script>alert(\"리뷰 기능이 설정이 안되었습니다.\");parent.topframe.location.href=\"JavaScript:GoMenu(1,'shop_review.php')\";</script>";exit;
}
mysql_free_result($result);

if ($type=="delete" && strlen($date)>0) {
	$sql = "DELETE FROM tblproductreview WHERE productcode = '".$productcode."' AND date = '".$date."' ";
	mysql_query($sql,get_db_conn());
	$onload = "<script> alert('해당 상품리뷰 삭제가 완료되었습니다.');</script>\n";
} else if ($type=="auth" && strlen($date)>0) {
	$sql = "UPDATE tblproductreview SET display = 'Y' ";
	$sql.= "WHERE productcode = '".$productcode."' AND date = '".$date."'";
	mysql_query($sql,get_db_conn());
	$onload = "<script> alert('해당 상품리뷰 인증이 완료되었습니다.');</script>\n";
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">
function CheckSearch() {
	s_check="";
	for(i=0;i<document.form1.s_check.length;i++) {
		if (document.form1.s_check[i].checked==true) {
			s_check=document.form1.s_check[i].value;
			break;
		}
	}
	if (s_check!="2") {
		if (document.form1.search.value.length<3) {
			if(document.form1.search.value.length==0) alert("검색어를 입력하세요.");
			else alert("검색어는 2글자 이상 입력하셔야 합니다.");
			document.form1.search.focus();
			return;
		}
	}
	document.form1.type.value="up";
	document.form1.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[1];
}

function OnChangeSearchType(val) {
	if (val==2) {
		document.form1.search.disabled=true;
		document.form1.search.style.background="#f4f4f4";
	} else {
		document.form1.search.disabled=false;
		document.form1.search.style.background="";
	}
}

function Searchid(id) {
	document.form1.type.value="up";
	document.form1.search.disabled=false;
	document.form1.search.style.background="";
	document.form1.search.value=id;
	document.form1.s_check[1].checked=true;
	document.form1.submit();
}
function SearchProduct(prname) {
	document.form1.type.value="up";
	document.form1.search.disabled=false;
	document.form1.search.style.background="#FFFFFF";
	document.form1.search.value=prname;
	document.form1.s_check[0].checked=true;
	document.form1.submit();
}


function MemberView(id){
	parent.topframe.ChangeMenuImg(4);
	document.form4.search.value=id;
	document.form4.submit();
}

function ProductInfo(code,prcode,popup) {
	document.form2.code.value=code;
	document.form2.prcode.value=prcode;
	document.form2.popup.value=popup;
	if (popup=="YES") {
		document.form2.action="product_register.add.php";
		document.form2.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form2.action="product_register.php";
		document.form2.target="";
	}
	document.form2.submit();
}
function ProductMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.primage"+cnt);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.visibility = "visible";
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	Obj = document.getElementById(Obj);
	Obj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}
function AuthReview(date,prcode) {
	if(confirm('해당 리뷰를 인증하시겠습니까?')){
		document.rForm.type.value="auth";
		document.rForm.date.value=date;
		document.rForm.productcode.value=prcode;
		document.rForm.submit();
	}
}
function DeleteReview(date,prcode) {
	if(confirm('해당 리뷰를 삭제하시겠습니까?')){
		document.rForm.type.value="delete";
		document.rForm.date.value=date;
		document.rForm.productcode.value=prcode;
		document.rForm.submit();
	}
}
function ReserveSet(id,date,prcode) {
	window.open("about:blank","reserve_set","width=250,height=150,scrollbars=no");
	document.form5.type.value="review";
	document.form5.id.value=id;
	document.form5.date.value=date;
	document.form5.productcode.value=prcode;
	document.form5.target="reserve_set";
	document.form5.submit();
}
function OrderInfo(id) {
	window.open("about:blank","orderinfo","width=400,height=320,scrollbars=no");
	document.orderform.target="orderinfo";
	document.orderform.id.value=id;
	document.orderform.submit();
}
function ReviewReply(date,prcode) {
	window.open("about:blank","reply","width=400,height=500,scrollbars=no");
	document.replyform.target="reply";
	document.replyform.date.value=date;
	document.replyform.productcode.value=prcode;
	document.replyform.submit();
}
function GoPage(block,gotopage) {
	document.rForm.block.value = block;
	document.rForm.gotopage.value = gotopage;
	document.rForm.submit();
}

//2015-03-09 적립금 관리
function ReserveInOut(id){
	window.open("about:blank","reserve_set","width=245,height=140,scrollbars=no");
	document.reserveform.target="reserve_set";
	document.reserveform.id.value=id;
	document.reserveform.type.value="reserve";
	document.reserveform.submit();
}

function ReserveInfo(id) {
	window.open("about:blank","reserve_info","width=500,height=400,scrollbars=yes");
	document.reserveinfo.id.value=id;
	document.reserveinfo.submit();
}

</script>

<!--2015-03-09 적립금 관리-->
<form name=reserveform action="reserve_money.php" method=post>
	<input type=hidden name=type>
	<input type=hidden name=id>
</form>

<form name=reserveinfo action="member_reservelist.php" method=post target=reserve_info>
	<input type=hidden name=id>
	<input type=hidden name=type>
</form>

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
			<?
				include ("menu_product.php");
			?>
			</td>
			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상품관리 &gt; 사은품/견적/기타관리 &gt; <span class="2depth_select">상품 리뷰 관리</span></td>
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
					<TD><IMG SRC="images/product_review_title.gif" ALT=""></TD>
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
					<TD width="100%" class="notice_blue">쇼핑몰 전체 상품들의 리뷰를 관리할 수 입니다.</TD>
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
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/product_review_stitle1.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=date>
			<input type=hidden name=productcode>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">검색조건 선택</TD>
					<TD class="td_con1" ><input type=radio name=s_check value="0" onClick="OnChangeSearchType(this.value);" id=idx_s_check0 <?=$check_s_check0?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check0>상품명으로 검색</label>&nbsp;&nbsp;<input type=radio name=s_check value="1" onClick="OnChangeSearchType(this.value);" id=idx_s_check1 <?=$check_s_check1?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check1>작성자로 검색</label>&nbsp;&nbsp;<input type=radio name=s_check value="2" onClick="OnChangeSearchType(this.value);" id=idx_s_check2 <?=$check_s_check2?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_s_check2>최다리뷰 작성자 20명</label></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">검색기간 선택</TD>
					<TD class="td_con1" ><input type=text name=search_start value="<?=$search_start?>" size=10 onfocus="this.blur();" OnClick="Calendar(this)" class="input_selected"> ~ <input type=text name=search_end value="<?=$search_end?>" size=10 onfocus="this.blur();" OnClick="Calendar(this)" class="input_selected">
					<input type=radio id=idx_vperiod0 name=vperiod value="0" checked style="BORDER-RIGHT: 0px; BORDER-TOP: 0px; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px" onclick="OnChangePeriod(this.value)" <?=$check_vperiod0?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_vperiod0>전체</label>
					<input type=radio id=idx_vperiod1 name=vperiod value="1" style="BORDER-RIGHT: 0px; BORDER-TOP: 0px; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px" onclick="OnChangePeriod(this.value)" <?=$check_vperiod1?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_vperiod1>오늘</label>
					<input type=radio id=idx_vperiod2 name=vperiod value="2" style="BORDER-RIGHT: 0px; BORDER-TOP: 0px; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px" onclick="OnChangePeriod(this.value)" <?=$check_vperiod2?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_vperiod2>1주일</label>
					<input type=radio id=idx_vperiod3 name=vperiod value="3" style="BORDER-RIGHT: 0px; BORDER-TOP: 0px; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px" onclick="OnChangePeriod(this.value)" <?=$check_vperiod3?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_vperiod3>이달</label>
					<input type=radio id=idx_vperiod4 name=vperiod value="4" style="BORDER-RIGHT: 0px; BORDER-TOP: 0px; BORDER-LEFT: 0px; BORDER-BOTTOM: 0px" onclick="OnChangePeriod(this.value)" <?=$check_vperiod4?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_vperiod4>올해</label></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">검색어 입력</TD>
					<TD class="td_con1" ><input name=search size=47 value="<?=$search?>" <?=$search_style?> class="input"> <select size=1 name=reviewtype class="select">
						<option value="ALL">전체리뷰</option>
						<option value="Y">인증된 리뷰</option>
						<option value="N">인증안된 리뷰</option>
						</select> <a href="javascript:CheckSearch();"><img src="images/btn_search2.gif" align=absmiddle width="50" height="25" border="0"></a>
					</TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/product_review_stitle2.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width="50"></col>
				<col width="100"></col>
				<col width=""></col>
				<col width="50"></col>
				<col width="50"></col>
				<col width="70"></col>		<!--2015-03-09 적립금 관리 추가-->
				<?if($review_type=="A"){?>
				<col width="50"></col>
				<?}?>
				<col width="50"></col>
				<?if($s_check!="2"){
					if($review_type=="A")
						$colspan=8;
					else
						$colspan=7;
				?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="<?=$colspan?>"></TD>
				</TR>
				<TR align=center>
					<TD class="table_cell">등록일</TD>
					<TD class="table_cell1">작성자 정보</TD>
					<TD class="table_cell1">상품명/리뷰 및 <FONT color=red>＊</FONT>답글</TD>
					<TD class="table_cell1">적립금</TD>
					<TD class="table_cell1">동일상품</TD>
					<TD class="table_cell1">적립금관리</TD>		<!--2015-03-09 적립금 관리 추가-->
					<?if($review_type=="A"){?>
					<TD class="table_cell1">인증</TD>
					<?}?>
					<TD class="table_cell1" width="51">삭제</TD>
				</TR>
				<TR>
					<TD colspan="<?=$colspan?>" background="images/table_con_line.gif"></TD>
				</TR>
<?
				$qry.= "WHERE a.productcode = b.productcode ";
				if ($reviewtype=="N") {
					$qry.= "AND a.display = 'Y' ";
				} else if ($reviewtype=="Y") {
					$qry.= "AND a.display='N' ";
				}
				$qry.= "AND a.date >= '".$search_s."' AND a.date <= '".$search_e."' ";
				if (strlen(trim($search))>2) {
					if($s_check=="0") {
						$qry.= "AND (b.productname LIKE '%".$search."%' || a.content LIKE '%".$search."%') ";
					} else if ($s_check=="1") {
						$qry.= "AND a.id = '".$search."' ";
					}
				}
				$sql = "SELECT COUNT(*) as t_count FROM tblproductreview a, tblproduct b ".$qry." ";
				$result = mysql_query($sql,get_db_conn());
				$row = mysql_fetch_object($result);
				mysql_free_result($result);
				$t_count = $row->t_count;
				$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

				$sql = "SELECT a.id,a.name,a.reserve,a.display,a.content,a.date,a.img,a.best,a.productcode,b.productname,b.tinyimage,b.selfcode,b.assembleuse ";
				$sql.= "FROM tblproductreview a, tblproduct b ".$qry." ";
				$sql.= "ORDER BY a.date DESC ";
				$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
				$result = mysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=mysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
					$contents=explode("=",$row->content);
					echo "<tr>\n";
					echo "	<TD align=center class=\"td_con2\">".substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."</td>\n";
					echo "	<TD class=\"td_con1\">";
					echo "	<NOBR><TABLE cellSpacing=0 cellPadding=0 border=0 width=\"100%\">";
					if (strlen($row->id)>0) {
						echo "	<TR><TD style=\"word-break:break-all;\"><img src=\"images/icon_name.gif\" width=\"41\" height=\"15\" border=\"0\" align=absMiddle> <A HREF=\"javascript:MemberView('".$row->id."');\">[<U>".$row->name."</U>]</A></td></tr>\n";
						echo "	<TR><TD><img src=\"images/icon_id.gif\" width=\"41\" height=\"15\" border=\"0\" align=absMiddle> <A HREF=\"javascript:Searchid('".$row->id."');\">[<U>".$row->id."</U>]</A></td></tr>\n";
						echo "	<TR><TD><img src=\"images/icon_order.gif\" width=\"41\" height=\"15\" border=\"0\" align=absMiddle> <A HREF=\"javascript:OrderInfo('".$row->id."');\">[<U>내역확인</U>]</A></td></tr>\n";
					} else {
						echo "	<TR><TD><img src=\"images/icon_name.gif\" width=\"41\" height=\"15\" border=\"0\" align=absMiddle> [<U>".$row->name."</U>]</td></tr>\n";
					}
					echo "	</table>\n";
					echo "	</td>\n";
					echo "	<TD class=\"td_con1\">";
					echo "	<table border=0 cellpadding=0 cellspacing=0>\n";
					echo "	<tr>\n";
					echo "		<td style=\"word-break:break-all;\">\n";
					echo "		<span onMouseOver='ProductMouseOver($cnt)' onMouseOut=\"ProductMouseOut('primage".$cnt."');\">";
					echo "		<img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\"><a href=\"JavaScript:ProductInfo('".substr($row->productcode,0,12)."','".$row->productcode."','')\"><font color=#3D3D3D><u>".$row->productname.($row->selfcode?"-".$row->selfcode:"")."</u></font></a>";
					echo "		&nbsp;<a href=\"JavaScript:ProductInfo('".substr($row->productcode,0,12)."','".$row->productcode."','YES')\"><IMG src=\"images/icon_newwin.gif\" align=absMiddle border=0 width=\"42\" height=\"18\"></a>";
					echo "		</span>\n";
					echo "		<div id=primage".$cnt." style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
					echo "		<table border=0 cellspacing=0 cellpadding=0 width=170>\n";
					echo "		<tr bgcolor=#FFFFFF>\n";
					if (strlen($row->tinyimage)>0) {
						echo "			<td align=center width=100% height=150 style=\"BORDER-RIGHT: #000000 1px solid; BORDER-TOP: #000000 1px solid; BORDER-LEFT: #000000 1px solid; BORDER-BOTTOM: #000000 1px solid\"><img src=".$Dir.DataDir."shopimages/product/".$row->tinyimage."></td>\n";
					} else {
						echo "			<td align=center width=100% height=150 style=\"BORDER-RIGHT: #000000 1px solid; BORDER-TOP: #000000 1px solid; BORDER-LEFT: #000000 1px solid; BORDER-BOTTOM: #000000 1px solid\"><img src=".$Dir."images/product_noimg.gif></td>\n";
					}
					echo "		</tr>\n";
					echo "		</table>\n";
					echo "		</div>\n";
					echo "		</td>\n";
					echo "	</tr>\n";
					echo "	<tr>\n";
					$checkbest = ($row->best == "Y")?'<font color="#FF0000"><b>[BEST]</b> </font>':'';
					$checkimg = (strlen($row->img)>0)?'<font color="#0000FF"><b>[PHOTO]</b> </font>':'';
					echo "		<td style=\"padding-top:3\"><img src=\"images/icon_review.gif\" border=\"0\" align=absMiddle hspace=\"2\"><a href=\"JavaScript:ReviewReply('".$row->date."','".$row->productcode."')\" title=\"".htmlspecialchars($contents[0])."\">".$checkbest.$checkimg.titleCut(38,htmlspecialchars($contents[0]))."</a> ";
					if(strlen($contents[1])>0) echo "<font color=red>＊</font>";
					echo "		</td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	</td>\n";
					echo "	<TD align=center class=\"td_con1\">";
					if(strlen($row->id)==0) {
						echo "<font color=red><B>X</B></font>";
					} else if ($row->reserve==0) {
						echo "<a href=\"javascript:ReserveSet('".$row->id."','".$row->date."','".$row->productcode."')\"><img src=\"images/icon_pointi.gif\" width=\"50\" height=\"33\" border=\"0\" valign=absmiddle></a>";
					} else {
						echo number_format($row->reserve);
					}
					echo "	</td>\n";
					echo "	<TD align=center class=\"td_con1\">&nbsp;<a href=\"javascript:SearchProduct('".$row->productname."');\"><img src=\"images/icon_review1.gif\" width=\"50\" height=\"33\" border=\"0\"></a></td>\n";
					if ($review_type=="A") {
						echo "	<TD align=center class=\"td_con1\" width=\"59\">";
						if($row->display=="Y") {
							echo "<B>Y</B>";
						} else {
							echo "	<a href=\"javascript:AuthReview('".$row->date."','".$row->productcode."');\"><img src=\"images/btn_ok2.gif\" width=\"35\" height=\"29\" border=\"0\" valign=absmiddle></a>";
						}
						echo "	</td>\n";
					}

					// 2015-03-09 적립금 관리
					echo "	<TD class=\"td_con1\">\n";
					echo "	<table cellpadding=\"0\" cellspacing=\"0\" align=\"center\">\n";
					echo "	<tr>\n";
					echo "		<td>\n";
					echo "		<A HREF=\"javascript:ReserveInOut('".$row->id."');\"><img src=\"images/btn_pm.gif\" width=\"35\" height=\"29\" border=\"0\"></A>";
					echo "		</td>\n";
					echo "		<td style=\"padding-left:1pt;\">\n";
					echo "		<A HREF=\"javascript:ReserveInfo('".$row->id."');\"><img src=\"images/btn_detail.gif\" width=\"35\" height=\"29\" border=\"0\"></A>";
					echo "		</td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	</TD>\n";

					echo "	<TD align=center class=\"td_con1\" width=\"59\">";
					echo "	<a href=\"javascript:DeleteReview('".$row->date."','".$row->productcode."');\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></a>";
					echo "	</td>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<TD colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</tr>\n";
					$cnt++;
				}
				mysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td class=\"td_con2\" colspan=".$colspan." align=center>검색된 리뷰 정보가 존재하지 않습니다.</td></tr>";
				}
?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="<?=$colspan?>"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td>
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
				echo "	<td align=center width=\"100%\" class=\"font_size\">\n";
				echo "		".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
				echo "	</td>\n";
				echo "</tr>\n";
?>
				<?}else{?>

				<TR>
					<TD background="images/table_top_line.gif" colspan="6"></TD>
				</TR>
				<TR align=center>
					<TD class="table_cell">No</TD>
					<TD class="table_cell1">이름</TD>
					<TD class="table_cell1">아이디</TD>
					<TD class="table_cell1">평균총점</TD>
					<TD class="table_cell1">리뷰갯수</TD>
					<TD class="table_cell1">적립금</TD>
				</TR>
				<TR>
					<TD colspan="6" background="images/table_con_line.gif"></TD>
				</TR>
<?
				$sql = "SELECT COUNT(*) as totcnt, SUM(marks) as totcnt2, id, name FROM tblproductreview ";
				$sql.= "WHERE id != '' AND date >= '".$search_s."' AND date <= '".$search_e."' ";
				if ($reviewtype=="N") {
					$sql.= "AND display = 'Y' ";
				} else if ($reviewtype=="Y") {
					$sql.= "AND display = 'N' ";
				}
				$sql.= "GROUP BY id ORDER BY totcnt DESC LIMIT 20 ";
				$result = mysql_query($sql,get_db_conn());
				$cnt = 0;
				while($row=mysql_fetch_object($result)) {
					$cnt++;
					echo "<tr>\n";
					echo "	<TD align=center class=\"td_con2\">".$cnt."</td>\n";
					echo "	<TD align=center class=\"td_con1\">&nbsp;<A HREF=\"javascript:MemberView('".$row->id."');\">".$row->name."</A></td>\n";
					echo "	<TD align=center class=\"td_con1\">&nbsp;<A HREF=\"javascript:Searchid('".$row->id."');\"><B>".$row->id."</B></A></td>\n";
					echo "	<TD align=center class=\"td_con1\">".(float) round($row->totcnt2/$row->totcnt,1)."</td>\n";
					echo "	<TD align=center class=\"td_con1\">".$row->totcnt."</td>\n";
					echo "	<TD align=center class=\"td_con1\"><A HREF=\"javascript:ReserveSet('".$row->id."','".date("YmdHis")."','');\"><img src=\"images/icon_pointi.gif\" width=\"50\" height=\"33\" border=\"0\" valign=absmiddle></A></td>\n";
					echo "</tr>\n";
				}
				mysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td class=lineleft colspan=6 align=center>검색된 리뷰 정보가 존재하지 않습니다.</td></tr>";
				}
?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="6"></TD>
				</TR>

				<?}?>

				</table>
				</td>
			</tr>
			</form>

			<form name=rForm action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=date>
			<input type=hidden name=productcode>
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=reviewtype value="<?=$reviewtype?>">
			<input type=hidden name=vperiod value="<?=$vperiod?>">
			</form>

			<form name=form2 action="product_register.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form>

			<form name=form3 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type value="<?=$type?>">
			<input type=hidden name=block>
			<input type=hidden name=gotopage>
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=productcode value="<?=$productcode?>">
			</form>

			<form name=form4 action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=orderform action="orderinfopop.php" method=post>
			<input type=hidden name=id>
			</form>

			<form name=replyform action="product_reviewreply.php" method=post>
			<input type=hidden name=date>
			<input type=hidden name=productcode>
			</form>

			<form name=form5 action="reserve_money.php" method=post>
			<input type=hidden name=type>
			<input type=hidden name=id>
			<input type=hidden name=date>
			<input type=hidden name=productcode>
			</form>
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
					<TD background="images/manual_bg.gif"></TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></td>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<col width=20></col>
					<col width=></col>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">상품 리뷰 관리</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 회원아이디로 검색시 정확한 아이디 입력을 하셔야만 검색이 됩니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- [회원이름] 클릭시 해당 회원의 정보를 확인하실 수 있습니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- [회원아이디] 클릭시 해당 회원아이디로 리뷰 검색이 이루어집니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- [내역확인] 클릭시 해당 회원의 구매내역을 확인할 수 있습니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 상품명을 클릭시 해당 상품 카테고리내 상품들의 정보를 확인하실 수 있습니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- [새창] 버튼 클릭시 해당 상품의 정보를 수정할 수 있습니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 리뷰 클릭시 해당 리뷰의 전체 내용 및 답변을 등록할 수 있습니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- [적립금 지급] 버튼 클릭시 해당 리뷰 작성자에게 적립금을 지급/차감할 수 있습니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- [다른리뷰 보기] 버튼 클릭시 해당 상품명으로 리뷰 검색이 이루어집니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- [삭제] 버튼 클릭시 해당 리뷰가 삭제됩니다.</td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">상품 리뷰 관리 주의사항</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 삭제된 리뷰는 복원되지 않으므로 신중히 처리하시기 바랍니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 적립금 지급으로 인한 적립/차감된 적립금은 복원되지 않으므로 신중히 처리하시기 바랍니다.</td>
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