<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$maxcnt=10;
$quickmenu = array("U","001","002","003","004");

$type=$_POST["type"];
$mode=$_POST["mode"];
$up_quick_type=$_POST["up_quick_type"];
$num=$_POST["num"];
$used=$_POST["used"];
$design=$_POST["design"];
$x_to=$_POST["x_to"];
$y_to=$_POST["y_to"];
$x_size=$_POST["x_size"];
$y_size=$_POST["y_size"];
$scroll_auto=$_POST["scroll_auto"];
$title=$_POST["title"];
$content=$_POST["content"];
if(strlen($used)==0) $used="N";

$quick_type=(int)$_shopdata->quick_type;

if($mode=="update" && strlen($up_quick_type)>0) {
	if($quick_type!=$up_quick_type) {
		$sql = "UPDATE tblshopinfo SET quick_type = '".$up_quick_type."' ";
		mysql_query($sql,get_db_conn());
		$quick_type=$up_quick_type;

		DeleteCache("tblshopinfo.cache");
	}
	$onload="<script>alert('최근 본 상품 사용여부 설정이 완료되었습니다.');</script>";
}

if($type=="insert") {
	$sql = "SELECT COUNT(*) as cnt FROM tblquickmenu ";
	$result = mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);
	if($row->cnt<$maxcnt) {
		$sql = "INSERT tblquickmenu SET ";
		$sql.= "used		= 'N', ";
		$sql.= "reg_date	= '".date("YmdHis")."', ";
		$sql.= "design		= '".$design."', ";
		$sql.= "x_size		= '".$x_size."', ";
		$sql.= "y_size		= '".$y_size."', ";
		$sql.= "x_to		= '".$x_to."', ";
		$sql.= "y_to		= '".$y_to."', ";
		$sql.= "scroll_auto	= '".$scroll_auto."', ";
		$sql.= "title		= '".$title."', ";
		$sql.= "content		= '".$content."' ";
		mysql_query($sql,get_db_conn());
		$onload="<script>alert('Quick메뉴 등록이 완료되었습니다.');</script>";
		unset($type); unset($used);
		unset($design); unset($x_size); unset($y_size); unset($x_to);
		unset($y_to); unset($scroll_auto); unset($title); unset($content);
	} else {
		$onload="<script>alert('Quick메뉴 등록은 최대 ".$maxcnt."개 까지 등록 가능합니다.');</script>";
	}
} else if (($type=="modify_result" || $type=="modify") && strlen($num)>0) {
	$sql = "SELECT * FROM tblquickmenu WHERE num = '".$num."' ";
	$result = mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		mysql_free_result($result);
		if($type=="modify") {
			$used=$row->used;
			$design=$row->design;
			$x_size=$row->x_size;
			$y_size=$row->y_size;
			$x_to=$row->x_to;
			$y_to=$row->y_to;
			$scroll_auto=$row->scroll_auto;
			$title=$row->title;
			$content=$row->content;
		} else if($type=="modify_result") {
			$sql = "SELECT COUNT(*) as cnt, COUNT(IF(used='Y',1,NULL)) as cnt2 FROM tblquickmenu ";
			$sql.= "WHERE num!='".$num."' ";
			$result = mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			mysql_free_result($result);
			if($used=="Y" && $row->cnt2>=1) {
				$onload="<script>alert('Quick메뉴는 등록 목록중 1가지만 사용할 수 있습니다.');</script>";
			} else {
				$sql = "UPDATE tblquickmenu SET ";
				$sql.= "used		= '".$used."', ";
				$sql.= "design		= '".$design."', ";
				$sql.= "x_size		= '".$x_size."', ";
				$sql.= "y_size		= '".$y_size."', ";
				$sql.= "x_to		= '".$x_to."', ";
				$sql.= "y_to		= '".$y_to."', ";
				$sql.= "scroll_auto	= '".$scroll_auto."', ";
				$sql.= "title		= '".$title."', ";
				$sql.= "content		= '".$content."' ";
				$sql.= "WHERE num = '".$num."' ";
				mysql_query($sql,get_db_conn());
				$onload="<script>alert('Quick메뉴 수정이 완료되었습니다.');</script>";
				unset($type); unset($num);
				unset($used); unset($design); unset($x_size); unset($y_size); unset($x_to);
				unset($y_to); unset($scroll_auto); unset($title); unset($content);
			}
		}
	} else {
		mysql_free_result($result);
		$onload="<script>alert('수정하려는 Quick메뉴 정보가 존재하지 않습니다.');</script>";
	}
} else if ($type=="delete" && strlen($num)>0) {
	$sql = "SELECT * FROM tblquickmenu WHERE num = '".$num."' ";
	$result = mysql_query($sql,get_db_conn());
	$rows=mysql_num_rows($result);
	mysql_free_result($result);

	if($rows>0) {
		$sql = "DELETE FROM tblquickmenu WHERE num = '".$num."' ";
		mysql_query($sql,get_db_conn());
		$onload="<script>alert('해당 Quick메뉴를 삭제하였습니다.');</script>";
		unset($type); unset($num);
		unset($used); unset($design); unset($x_size); unset($y_size); unset($x_to);
		unset($y_to); unset($scroll_auto); unset($title); unset($content);
	}
}

if(strlen($type)==0) $type="insert";
$type_name="images/botteon_save.gif";
if($type=="modify" || $type=="modify_result") $type_name="images/btn_edit2.gif";

if($type=="insert") $used_disabled="disabled";
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>

<!-- 에디터용 파일 호출 -->
<script type="text/javascript" src="/gmeditor/js/jquery.js"></script>
<script type="text/javascript" src="/gmeditor/js/jquery.event.drag-2.0.min.js"></script>
<script type="text/javascript" src="/gmeditor/js/jquery.resizable.js"></script>
<script type="text/javascript" src="/gmeditor/js/ajax_upload.3.6.js"></script>
<script type="text/javascript" src="/gmeditor/js/ej.h2xhtml.js"></script>
<script type="text/javascript" src="/gmeditor/editor.js"></script>
<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jquery.autocomplete.css" />
<script language="javascript" type="text/javascript">
$(document).ready(function() {
	ejEditor();
});
</script>
<style type="text/css">
@import url("/gmeditor/common.css");
.productRegFormTbl{border-top:2px solid #333}
.productRegFormTbl th{ text-align:left; padding-left:25px; background:#f8f8f8 url(/admin/images/icon_point5.gif) 10px 50% no-repeat; border-bottom:1px solid #efefef; border-left:1px solid #efefef}
.productRegFormTbl td{padding-left:5px; border-bottom:1px solid #efefef; border-left:1px solid #efefef}
.productRegFormTbl caption{ text-align:left}
</style>
<!-- # 에디터용 파일 호출 -->

<script language="JavaScript">

var quickcnt = <?=count($quickmenu)?>;
function ChangeEditer(mode,obj){
	if (mode==form1.htmlmode.value) {
		return;
	} else {
		obj.checked=true;
		editor_setmode('content',mode);
	}
	form1.htmlmode.value=mode;
}

function CheckForm(type) {
	if(document.form1.x_to.value.length==0 || document.form1.y_to.value.length==0) {
		alert("Quick메뉴 위치 설정을 하세요.");
		document.form1.x_to.focus();
		return;
	}
	if(!IsNumeric(document.form1.x_to.value)) {
		alert("Quick메뉴 위치 설정값은 숫자만 입력 가능합니다.");
		document.form1.x_to.focus();
		return;
	}
	if(!IsNumeric(document.form1.y_to.value)) {
		alert("Quick메뉴 위치 설정값은 숫자만 입력 가능합니다.");
		document.form1.y_to.focus();
		return;
	}
	if(document.form1.x_size.value.length==0 || document.form1.y_size.value.length==0) {
		alert("Quick메뉴 크기 설정을 하세요.");
		document.form1.x_size.focus();
		return;
	}
	if(!IsNumeric(document.form1.x_size.value)) {
		alert("Quick메뉴 크기 설정값은 숫자만 입력 가능합니다.");
		document.form1.x_size.focus();
		return;
	}
	if(!IsNumeric(document.form1.y_size.value)) {
		alert("Quick메뉴 크기 설정값은 숫자만 입력 가능합니다.");
		document.form1.y_size.focus();
		return;
	}
	if(document.form1.scroll_auto[0].checked==false && document.form1.scroll_auto[1].checked==false) {
		alert("스크롤 타입을 선택하세요.");
		document.form1.scroll_auto[0].focus();
		return;
	}
	if(document.form1.title.value.length==0) {
		alert("Quick메뉴 제목을 입력하세요.");
		document.form1.title.focus();
		return;
	}

	design=false;
	for(i=quickcnt;i<document.form1.design.length;i++) {
		if(document.form1.design[i].checked==true) {
			design=true;
			break;
		}
	}
	if(!design) {
		alert("Quick메뉴 템플릿을 선택하세요.");
		return;
	}

	if(document.form1.content.value.length==0) {
		alert("Quick메뉴 내용을 입력하세요.");
		document.form1.content.focus();
		return;
	}
	if(type=="modify" || type=="modify_result") {
		if(!confirm("해당 Quick메뉴를 수정하시겠습니까?")) {
			return;
		}
		document.form1.type.value="modify_result";
	} else {
		document.form1.type.value="insert";
	}
	document.form1.submit();
}

function ModeSend(type,num) {
	if(type=="delete") {
		if(!confirm("해당 Quick메뉴를 삭제하시겠습니까?")) {
			return;
		}
	}
	document.form1.type.value=type;
	document.form1.num.value=num;
	document.form1.submit();
}

function ChangeDesign(tmp) {
	tmp=tmp + quickcnt;
	document.form1["design"][tmp].checked=true;
}

function ChangeQuickType() {
	if(!confirm("최근 본 상품 사용여부 설정을 저장하시겠습니까?")) {
		return;
	}
	up_quick_type="";
	for(i=0;i<document.form1.up_quick_type.length;i++) {
		if(document.form1.up_quick_type[i].checked==true) {
			up_quick_type=document.form1.up_quick_type[i].value;
			break;
		}
	}
	document.form2.up_quick_type.value=up_quick_type;
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
			<? include ("menu_market.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 마케팅지원 &gt; 이벤트/사은품 기능 설정 &gt; <span class="2depth_select">Quick메뉴 관리</span></td>
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
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=num value="<?=$num?>">
			<input type=hidden name=htmlmode value='wysiwyg'>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_quickmenu_title.gif" ALT=""></TD>
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
					<TD width="100%" class="notice_blue">쇼핑몰 전체페이지에서 항상 따라다니는 우측의 Quick메뉴를 관리할 수 있습니다. 쇼핑 편의 및 이벤트 홍보를 위해 활용하세요.</TD>
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
				<td><IMG SRC="images/market_quickmenu_stitle1.gif"  ALT=""></td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="100%">
						<TR>
							<TD bgcolor="#B9B9B9" height="1"></TD>
						</TR>
						<tr>
							<td>
								<table cellpadding="0" cellspacing="0" width="100%">
								<col width="160"></col>
								<col width=""></col>
									<TR>
										<TD class="table_cell"><b><img src="images/icon_point2.gif" border="0"></b>최근본 상품 기능 적용 여부</TD>
										<TD class="td_con1">
					<INPUT id=idx_quick_type1 type=radio value=0 name=up_quick_type <?if($quick_type=="0")echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_quick_type1>최근 본 상품도 사용함</LABEL>  &nbsp;&nbsp;&nbsp;
					<INPUT id=idx_quick_type2 type=radio value=1 name=up_quick_type <?if($quick_type=="1")echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_quick_type2>최근 본 상품은 사용안함</LABEL>
										</TD>
									</TR>
								</table>
							</td>
						</tr>
						<TR>
							<TD bgcolor="#B9B9B9" height="1"></TD>
						</TR>
					</table>


				</td>
			</tr>
			<tr><td height="20"></td></tr>







			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">1)<b> "최근 본 상품도 사용함"으로 설정할 경우 Quick메뉴와 출력 위치가 겹칠 수 있습니다. </b><br>2) 겹칠 경우 &quot;위치설정&quot;에서 상단 위치를 적절히 변경하시면 됩니다.</TD>
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
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:ChangeQuickType();"><img src="<?=$type_name?>" width="113" height="38" border="0"></a></td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_quickmenu_stitle2.gif" WIDTH="187" HEIGHT=31 ALT=""></TD>
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
				<col width=30></col>
				<col width=55></col>
				<col width=></col>
				<col width=65></col>
				<col width=80></col>
				<col width=60></col>
				<col width=60></col>
				<TR>
					<TD background="images/table_top_line.gif" colspan="7"></TD>
				</TR>
				<TR align=center>
					<TD class="table_cell">No</TD>
					<TD class="table_cell1">사용여부</TD>
					<TD class="table_cell1">Quick메뉴 제목</TD>
					<TD class="table_cell1">스크롤</TD>
					<TD class="table_cell1">등록일</TD>
					<TD class="table_cell1">수정</TD>
					<TD class="table_cell1">삭제</TD>
				</TR>
				<TR>
					<TD colspan="7" background="images/table_con_line.gif"></TD>
				</TR>
<?
				$colspan=7;
				$sql = "SELECT num, used, reg_date, title, scroll_auto FROM tblquickmenu ORDER BY num DESC ";
				$result = mysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=mysql_fetch_object($result)) {
					$cnt++;
					$reg_date = substr($row->reg_date,0,4)."/".substr($row->reg_date,4,2)."/".substr($row->reg_date,6,2);
					if($row->scroll_auto=="Y")	$scroll_auto_name = "자동스크롤";
					else if($row->scroll_auto=="N")	$scroll_auto_name = "위치고정";
					if($row->used=="Y")	$used_name = "사용중";
					else if($row->used=="N")	$used_name = "사용안함";
					echo "<TR>\n";
					echo "	<TD align=center class=\"td_con2\">".$cnt."</TD>\n";
					echo "	<TD align=center class=\"td_con1\">".$used_name."</TD>\n";
					echo "	<TD class=\"td_con1\">".$row->title."</TD>\n";
					echo "	<TD align=center class=\"td_con1\">".$scroll_auto_name."</TD>\n";
					echo "	<TD align=center class=\"td_con1\">".$reg_date."</TD>\n";
					echo "	<TD align=center class=\"td_con1\"><a href=\"javascript:ModeSend('modify','".$row->num."');\"><img src=\"images/btn_edit.gif\" width=\"50\" height=\"22\" border=\"0\"></a></TD>\n";
					echo "	<TD align=center class=\"td_con1\"><a href=\"javascript:ModeSend('delete','".$row->num."');\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></a></TD>\n";
					echo "</TR>\n";
					echo "<TR>\n";
					echo "	<TD colspan=".$colspan." background=\"images/table_con_line.gif\"></TD>\n";
					echo "</TR>\n";
				}
				mysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td class=td_con2 colspan=".$colspan." align=center>등록된 Quick메뉴가 없습니다.</td></tr>";
				}
?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="7"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=40></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_quickmenu_stitle3.gif" WIDTH="187" HEIGHT=31 ALT=""></TD>
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
				<col width=140></col>
				<col width=></col>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">사용여부</TD>
					<TD class="td_con1"><INPUT id=idx_used type=checkbox value=Y <?if($used=="Y")echo"checked";?> name=used <?=$used_disabled?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_used>사용함</LABEL><br>* 사용함으로 되어 있는 경우에만 나타납니다. (모든 페이지에 나타남)<br><span class="font_orange">* 사용함 기능은 등록후에 변경할 수 있습니다.&nbsp;</span></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">Quick메뉴 위치 설정</TD>
					<TD class="td_con1">왼쪽에서 <INPUT onkeyup="return strnumkeyup(this);" style="PADDING-LEFT: 5px" size=5 name=x_to value="<?=$x_to?>" class="input">픽셀 이동 후, 위쪽에서 <INPUT onkeyup="return strnumkeyup(this);" style="PADDING-LEFT: 5px" size=5 name=y_to value="<?=$y_to?>" class="input">픽셀 아래로 이동합니다.</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">Quick메뉴 크기 설정</TD>
					<TD class="td_con1">가로: <INPUT onkeyup="return strnumkeyup(this);" style="PADDING-LEFT: 5px" size=5 name=x_size value="<?=$x_size?>" class="input">픽셀,  &nbsp;세로: <INPUT onkeyup="return strnumkeyup(this);" style="PADDING-LEFT: 5px" size=5 name=y_size value="<?=$y_size?>" class="input">픽셀 <b><span class="font_orange">(</b><B>＊가로사이즈는 90px을 권장)</span></B></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">스크롤 선택</TD>
					<TD class="td_con1">
					<INPUT id=idx_scroll_auto1 type=radio value=Y name=scroll_auto <?if($scroll_auto=="Y")echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_scroll_auto1>화면 스크롤에 맞추어 자동 스크롤</LABEL>  &nbsp;&nbsp;&nbsp;
					<INPUT id=idx_scroll_auto2 type=radio value=N name=scroll_auto <?if($scroll_auto=="N")echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_scroll_auto2>위치 고정</LABEL><BR><span class="font_orange">＊자동 스크롤로 지정하는 경우, 고객은 항상 Quick메뉴를 볼 수 있습니다.</span>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<tr>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">Quick메뉴 제목</TD>
					<TD class="td_con1"><INPUT style="WIDTH:100%" name=title value="<?=$title?>" class="input"><br><span class="font_orange">＊관리목록에서만 사용합니다. 간단히 입력해 주세요.</span></TD>
				</tr>
				<tr>
					<TD colspan="2">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td bgcolor="#ededed" style="padding:4pt;">
						<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
						<tr>
							<td>
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD height="30" align=center background="images/blueline_bg.gif"><b><font color="#555555">템플릿 선택</font><span class="font_orange">(＊사이즈 90px 권장)</b></span></TD>
							</TR>
							<TR>
								<TD width="100%" background="images/table_con_line.gif"></TD>
							</TR>
							<TR>
								<TD width="100%" style="padding:0pt;">
								<TABLE cellSpacing=0 cellPadding="5" width="100%" border=0>
								<TR>
									<TD width="24" height="160" align=right valign="middle">
										<img src="images/btn_back.gif" onMouseover='moveright()' onMouseout='clearTimeout(righttime)' style="cursor:hand;" width="31" height="31" border="0">
									</TD>
									<TD width="720" height="160">
									<table width="100%" cellspacing="0" cellpadding="0" border="0">
									<tr height=170>
										<td id=temp style="visibility:hidden;position:absolute;top:0;left:0">
				<?
										echo "<script>";
										$jj=0;
										$menucontents = "";
										$menucontents .= "<table border=0 cellpadding=0 cellspacing=0><tr>";
										for($i=0;$i<count($quickmenu);$i++) {
											echo "thisSel = 'dotted #FFFFFF';";
											$menucontents .= "<td width='173'><p align='center'><input type=radio name='design' value='".$quickmenu[$i]."'";
											if($design==$quickmenu[$i]) $menucontents .= " checked";
											$menucontents .= "><img src='images/sample/quick".$quickmenu[$i].".gif' border=0 width=150 height=125 hspace='5' style='border-width:1pt; border-color:#FFFFFF; border-style:solid;' onMouseOver='changeMouseOver(this);' onMouseOut='changeMouseOut(this,thisSel);' style='cursor:hand;' onclick='ChangeDesign(".$i.");'></td>";
											$jj++;
										}
										$menucontents .= "</tr></table>";
										echo "</script>";
				?>

										<script language="JavaScript1.2">
										<!--
										function changeMouseOver(img) {
											 img.style.border='1 dotted #999999';
										}
										function changeMouseOut(img,dot) {
											 img.style.border="1 "+dot;
										}

										var menuwidth=650
										var menuheight=170
										var scrollspeed=10
										var menucontents="<nobr><?=$menucontents?></nobr>";

										var iedom=document.all||document.getElementById
										if (iedom)
											document.write(menucontents)
										var actualwidth=''
										var cross_scroll, ns_scroll
										var loadedyes=0
										function fillup(){
											if (iedom){
												cross_scroll=document.getElementById? document.getElementById("test2") : document.all.test2
												cross_scroll.innerHTML=menucontents
												actualwidth=document.all? cross_scroll.offsetWidth : document.getElementById("temp").offsetWidth
											}
											else if (document.layers){
												ns_scroll=document.ns_scrollmenu.document.ns_scrollmenu2
												ns_scroll.document.write(menucontents)
												ns_scroll.document.close()
												actualwidth=ns_scroll.document.width
											}
											loadedyes=1
										}
										window.onload=fillup

										function moveleft(){
											if (loadedyes){
												if (iedom&&parseInt(cross_scroll.style.left)>(menuwidth-actualwidth)){
													cross_scroll.style.left=parseInt(cross_scroll.style.left)-scrollspeed
												}
												else if (document.layers&&ns_scroll.left>(menuwidth-actualwidth))
													ns_scroll.left-=scrollspeed
											}
											lefttime=setTimeout("moveleft()",50)
										}

										function moveright(){
											if (loadedyes){
												if (iedom&&parseInt(cross_scroll.style.left)<0)
													cross_scroll.style.left=parseInt(cross_scroll.style.left)+scrollspeed
												else if (document.layers&&ns_scroll.left<0)
													ns_scroll.left+=scrollspeed
											}
											righttime=setTimeout("moveright()",50)
										}

										if (iedom||document.layers){
											with (document){
												write('<td valign=top>')
												if (iedom){
													write('<div style="position:relative;width:'+menuwidth+';">');
													write('<div style="position:absolute;width:'+menuwidth+';height:'+menuheight+';overflow:hidden;">');
													write('<div id="test2" style="position:absolute;left:0">');
													write('</div></div></div>');
												}
												else if (document.layers){
													write('<ilayer width='+menuwidth+' height='+menuheight+' name="ns_scrollmenu">')
													write('<layer name="ns_scrollmenu2" left=0 top=0></layer></ilayer>')
												}
												write('</td>')
											}
										}
										//-->
										</script>
										</td>
									</tr>
									</table>
									</TD>
									<TD width="27" height="160"><img src="images/btn_next.gif" onMouseover='moveleft()' onMouseout='clearTimeout(lefttime)' style="cursor:hand;" width="31" height="31" border="0"></TD>
								</TR>
								</TABLE>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</TD>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<tr>
					<TD colspan="2">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><TEXTAREA style="DISPLAY: yes; WIDTH: 100%" name=content rows="17" wrap=off class="textarea" lang="ej-editor1" ><?=$content?></TEXTAREA></td>
					</tr>
					</table>
					</TD>
				</tr>
				</TABLE>
				</td>
			</tr>
			<tr><td HEIGHT=10></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm('<?=$type?>');"><img src="<?=$type_name?>" width="113" height="38" border="0"></a></td>
			</tr>
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
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<col width=20></col>
					<col width=></col>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><b>팝업창 사용가이드</b></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">
						- Quick메뉴는 최대 10개 까지 등록 가능합니다.
						<br>- Quick메뉴는 등록 목록중 1가지만 사용할 수 있습니다.
						<br><span class="font_orange"><b>- 사용하지 않는 경우 모든 Quick메뉴를 사용하지 않음으로 변경하세요.</b></span>
						</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">&nbsp;</td>
					</tr>
					<tr>
						<td align="right"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_orange"><b>퀵메뉴 입력폼</b></span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0>[MYPAGE] </TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" width="100%">&lt;a href=[MYPAGE]&gt;마이페이지&lt;/a&gt;<br> </TD>
						</TR>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-top-color:rgb(222,222,222); border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0>[MEMBER] </TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-top-color:rgb(222,222,222); border-top-style:solid;" width="100%">&lt;a href=[MEMBER]&gt;회원가입/수정&lt;/a&gt;<br>  </TD>
						</TR>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[ORDER] </TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%">&lt;a href=[ORDER]&gt;주문조회&lt;/a&gt; </TD>
						</TR>
						</TABLE>
						</td>
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
			</form>

			<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=mode value="update">
			<input type=hidden name=up_quick_type value="">
			</form>
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