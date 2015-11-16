<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-5";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$body=$_POST["body"];
$intitle=$_POST["intitle"];

if($intitle=="Y") {
	$leftmenu="Y";
} else {
	$leftmenu="N";
}


$subject = '장바구니 화면';

$insertKey = 'basket';

// 백업 / 복구
if ( $type=="store" OR $type=="restore" ) {
	$MSG = adminDesingBackup ( $type, 'basket', $body, $subject, '', '', $leftmenu );
	$MSG .= adminDesingBackup ( $type, 'design_basket', 'U', $subject, '', '', '', 'tblshopinfo', 'design_basket' );
	$onload="<script>alert(\"".$MSG."\");</script>";
}



if($type=="update" && strlen($body)>0) {

	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='basket' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT tbldesignnewpage SET ";
		$sql.= "type		= 'basket', ";
		$sql.= "subject		= '장바구니 화면', ";
		$sql.= "leftmenu	= '".$leftmenu."', ";
		$sql.= "body		= '".$body."' ";
		mysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET ";
		$sql.= "leftmenu	= '".$leftmenu."', ";
		$sql.= "body		= '".$body."' ";
		$sql.= "WHERE type='basket' ";
		mysql_query($sql,get_db_conn());
	}
	mysql_free_result($result);

	$sql = "UPDATE tblshopinfo SET design_basket='U' ";
	mysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>alert(\"장바구니 화면 디자인 수정이 완료되었습니다.\");</script>";
} else if($type=="delete") {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='basket' ";
	mysql_query($sql,get_db_conn());

	$sql = "UPDATE tblshopinfo SET design_basket='001' ";
	mysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>alert(\"장바구니 화면 디자인 삭제가 완료되었습니다.\");</script>";
} else if($type=="clear") {
	$intitle="";
	$body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='basket' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$body=$row->body;
	}
	mysql_free_result($result);
}

if($type!="clear") {
	$body="";
	$intitle="";
	$sql = "SELECT leftmenu,body FROM tbldesignnewpage WHERE type='basket' ";
	$result = mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$body=$row->body;
		$intitle=$row->leftmenu;
	} else {
		$intitle="Y";
	}
	mysql_free_result($result);
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.body.value.length==0) {
			alert("장바구니 화면 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("장바구니 화면 디자인을 삭제하시겠습니까?")) {
			document.form1.type.value=type;
			document.form1.submit();
		}
	} else if(type=="clear") {
		alert("기본값 복원 후 [적용하기]를 클릭하세요. 클릭 후 페이지에 적용됩니다.");
		document.form1.type.value=type;
		document.form1.submit();
	}



	// 백업
	if(type=="store") {
		if(confirm("<?=$subject?> 디자인을 백업하시겠습니까?\n\n적용하지 않으셨다면 \"적용하기\"로 적용 하신후 백업하시기 바랍니다.\n기존 저장된 백업소스를 대체합니다.")) {
			document.form1.type.value=type;
			document.form1.submit();
			return;
		}
	}
	// 복구
	if(type=="restore") {
		if(confirm("<?=$subject?> 디자인을 백업복구 하시겠습니까?\n\n복구 하게 되면 바로 디자인 적용 됩니다.")) {
			document.form1.type.value=type;
			document.form1.submit();
			return;
		}
	}

	// 미리보기
	if(type=="preview") {
		if(document.form1.body.value.length==0) {
			alert("상품검색 결과화면 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		document.form1.type.value='<?=$insertKey?>';
		document.form1.target="preview";
		document.form1.action="designPreview.php";
		document.form1.submit();
		document.form1.target="";
		document.form1.action="<?=$_SERVER[PHP_SELF]?>";
	}

}

//매크로 보기(팝업)
function macroview(){
	window.open("http://www.getmall.co.kr/macro/pages/basket_macro.html","basket_macro","height=800,width=680,scrollbars=no");
}

//-->
</SCRIPT>
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
			<? include ("menu_design.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt; <span class="2depth_select">장바구니 화면 꾸미기</span></td>
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
							<TD><IMG SRC="images/design_jang_title.gif" ALT=""></TD>
						</tr>
						<tr>
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
					<TD width="100%" class="notice_blue">장바구니 화면 디자인을 자유롭게 디자인 하실 수 있습니다.</TD>
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
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/design_jang_stitle.gif" WIDTH="250" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">
						&nbsp;&nbsp;<a href="javascript:macroview();"><img src="images/btn_macroview.gif" border="0" align="absmiddle" alt="" /></a>&nbsp;&nbsp;<a href="http://www.getmall.co.kr/macro/data/basket_design.zip"><img src="images/btn_defaultcode.gif" border="0" align="absmiddle" alt="" />
					</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
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
					<TD width="100%" class="notice_blue">1) 매뉴얼의 <b>매크로명령어</b>를 참조하여 디자인 하세요.</span><br>2) [기본값복원]+[적용하기], [삭제하기]하면 기본템플릿으로 변경(개별디자인 소스 삭제)됨 -> 템플릿 메뉴에서 원하는 템플릿 선택.<br>3) 기본값 복원이나 삭제하기 없이도 템플릿 선택하면 개별디자인은 해제됩니다.(개별디자인 소스는 보관됨)</TD>
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
			<tr><td height="3"></td></tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td style="padding-top:2px;"><textarea name=body style="WIDTH: 100%; HEIGHT: 300px" class="textarea"><?=htmlspecialchars($body)?></textarea><br><input type=checkbox name=intitle value="Y" <?if($intitle=="Y")echo"checked";?>> <b><span style="letter-spacing:-0.5pt;"><span class="font_orange">기본 타이틀 이미지 유지 - 타이틀 이하 부분부터 디자인 변경</span>(미체크시 기존 타이틀 이미지 없어짐으로 직접 편집하여 사용)</b></span></td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('clear');"><img src="images/botteon_bok.gif" width="124" height="38" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" width="113" height="38" border="0" hspace="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('preview');"><img src="images/botteon_prev.gif" width="113" height="38" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('store');"><img src="images/botteon_store.gif" border="0" hspace="2" alt="백업하기"></a>&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('restore');"><img src="images/botteon_restore.gif" border="0" hspace="2" alt="백업복원하기"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"  class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20"></td>
						<td >
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
						<col width=200></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center bgcolor=#F0F0F0>
							<B>원샷구매 관련 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15"><B>[ONE_START]</B></td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 시작 (원샷구매 사용시 첫부분에 꼭 들어가야함)
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONE_CODEA_선택박스 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 1차 카테고리 선택박스 <FONT class=font_blue>(예:[ONE_CODEA_width:150px;color:#000000;font-size:11px])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONE_CODEB_선택박스 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 2차 카테고리 선택박스 <FONT class=font_blue>(예:[ONE_CODEB_width:150px;color:#000000;font-size:11px])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONE_CODEC_선택박스 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 3차 카테고리 선택박스 <FONT class=font_blue>(예:[ONE_CODEC_width:150px;color:#000000;font-size:11px])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONE_CODED_선택박스 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 4차 카테고리 선택박스 <FONT class=font_blue>(예:[ONE_CODED_width:150px;color:#000000;font-size:11px])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONE_PRLIST_선택박스 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 상품 리스트 선택박스 <FONT class=font_blue>(예:[ONE_PRLIST_width:350px;color:#000000;font-size:11px])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONE_PRIMG]</td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 상품이미지
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONE_BASKET]</td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 장바구니 담기 <FONT class=font_blue>(예:&lt;a href=[ONE_BASKET]>장바구니 담기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15"><B>[ONE_END]</B></td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 끝 (원샷구매 사용시 마지막 부분에 꼭 들어가야함)
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center bgcolor=#F0F0F0>
							<B>장바구니 상품 관련 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15">[IFBASKET]<br>[IFELSEBASKET]<br>[IFENDBASKET]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							장바구니에 상품이 있을 경우와 없을 경우
							<pre style="line-height:15px">
<font class=font_blue>   <B>[IFBASKET]</B>
      장바구니에 상품이 <FONT COLOR="red"><B>있을</B></FONT> 경우의 내용
   <B>[IFELSEBASKET]</B>
      장바구니에 상품이 <FONT COLOR="red"><B>없을</B></FONT> 경우의 내용
   <B>[IFENDBASKET]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15">[FORBASKET]<br>[FORENDBASKET]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							[FORBASKET] 장바구니 상품 한개에 대한 내용 기술[FORENDBASKET]
							<pre style="line-height:15px">
<font class=font_blue>   [IFBASKET]
       <B>[FORBASKET]</B>상품 하나에 대한 내용 기술<B>[FORENDBASKET]</B>
   [IFELSEBASKET]
       장바구니에 담긴 상품이 없습니다.
   [IFENDBASKET]</font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ITEM_CHKBOX]</td>
							<td class=td_con1 width=100% style="padding-left:5;">장바구니 상품선택 체크박스</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_PRIMG]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							상품이미지
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_PRNAME]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							상품명
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_ADDCODE1]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							상품 특수값 ("-"포함)
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_ADDCODE2]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							상품 특수값 ("-"비포함)
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_RESERVE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							적립금 <FONT class=font_blue>(예:[BASKET_RESERVE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_SELLPRICE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							상품가격 <FONT class=font_blue>(예:[BASKET_SELLPRICE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[CATE_AUTH_ICON]</td>
							<td class=td_con1 width=100% style="padding-left:5;">사용제한 아이콘 표시</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_QUANTITY]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							수량 입력박스
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_QUP]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							수량증가 함수 <FONT class=font_blue>(예:&lt;a href=[BASKET_QUP]>수량증가&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_QDN]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							수량감소 함수 <FONT class=font_blue>(예:&lt;a href=[BASKET_QDN]>수량감소&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_QUPDATE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							수량적용(수정) <FONT class=font_blue>(예:&lt;a href=[BASKET_QUPDATE]>수정&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_PRICE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							주문금액 <FONT class=font_blue>(예:[BASKET_PRICE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[DELI_STR]</td>
							<td class=td_con1 width=100% style="padding-left:5;">배송비</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[COUPON_LIST]</td>
							<td class=td_con1 width=100% style="padding-left:5;">쿠폰 리스트</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_WISHLIST]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							위시리스트 담기 버튼 <FONT class=font_blue>(예:&lt;a href=[BASKET_WISHLIST]>위시리스트 담기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_DEL]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							장바구니에서 삭제 버튼 <FONT class=font_blue>(예:&lt;a href=[BASKET_DEL]>삭제&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_ETCIMG]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							상품특이사항 출력
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15">[IFOPTION]<br>[IFENDOPTION]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							장바구니 상품옵션 처리 (옵션이 있을 경우에만 옵션내용 출력)
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFOPTION]</B>
      상품옵션 내용 예) [BASKET_OPTION]
   <B>[IFENDOPTION]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_OPTION]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							상품옵션내용 <FONT class=font_blue>(예:옵션 : [BASKET_OPTION])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_PRODUCTPRICE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							상품 합계금액 <FONT class=font_blue>(예:상품 합계금액 : [BASKET_PRODUCTPRICE]원)</font>
							</td>
						</tr>
						<!--
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15">[IFPACKAGE]<br>[IFENDPACKAGE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							장바구니 상품 패키지 처리 (패키지가 있을 경우에만 패키지 내용 출력)
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFPACKAGE]</B>
      상품패키지 내용 예) [BASKET_PACKAGE]
   <B>[IFENDPACKAGE]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_PACKAGE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							패키지 정보 <FONT class=font_blue>(예:[BASKET_PACKAGE])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15">[IFPACKAGELIST]<br>[IFENDPACKAGELIST]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							장바구니 상품 패키지 구성 정보 처리 (패키지 구성 상품이 있을 경우에만 내용 출력)
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFPACKAGELIST]</B>
      상품패키지 내용 예) [BASKET_PACKAGELIST]
   <B>[IFENDPACKAGELIST]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_PACKAGELIST]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							패키지 구성 상품 정보 <FONT class=font_blue>(예:[BASKET_PACKAGELIST])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15">[IFASSEMBLE]<br>[IFENDASSEMBLE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							장바구니 코디/조립상품의 구성 (코디/조립상품이 있을 경우에만 구성내용 출력)
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFASSEMBLE]</B>
      코디/조립상품 내용 예) [BASKET_ASSEMBLE]
   <B>[IFENDASSEMBLE]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_ASSEMBLE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							코디/조립 구성 상품 정보 <FONT class=font_blue>(예:[BASKET_ASSEMBLE])</font>
							</td>
						</tr>
						-->
						<?if($_shopdata->ETCTYPE["VATUSE"]=="Y") { ?>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_PRODUCTVAT]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							VAT 합계금액 <FONT class=font_blue>(예:VAT 합계금액 : [BASKET_PRODUCTVAT]원)</font>
							</td>
						</tr>
						<? } ?>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_GROUPSTART]<br>[BASKET_GROUPEND]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							업체별 배송비/합계금액 내용
							<pre style="line-height:15px">
<font class=font_blue>   <B>[BASKET_GROUPSTART]</B>
      사용 예) 배송비 : [GROUP_DELIPRICE]원, 합계금액 : [GROUP_TOTPRICE]원
   <B>[BASKET_GROUPEND]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[GROUP_DELIPRICE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							업체별 배송비 <FONT class=font_blue>(예:상품 합계금액 : [GROUP_DELIPRICE]원)</font><br>[BASKET_GROUPSTART] [BASKET_GROUPEND]에 사용
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[GROUP_TOTPRICE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							업체별 합계금액 <FONT class=font_blue>(예:업체별 합계금액 : [GROUP_TOTPRICE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_TOTPRICE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							총 결제금액 <FONT class=font_blue>(예:총 결제금액 : [BASKET_TOTPRICE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_TOTRESERVE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							총 적립금 <FONT class=font_blue>(예:총 적립금 : [BASKET_TOTRESERVE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[GROUP_DISCOUNT]</td>
							<td class=td_con1 width=100% style="padding-left:5;">회원 등급별 할인 정보</td>
						</tr>
						<!--
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_PESTER]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							조르기 버튼 <FONT class=font_blue>(예:&lt;a href=[BASKET_PESTER]>조르기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_PRESENT]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							선물하기 버튼 <FONT class=font_blue>(예:&lt;a href=[BASKET_PRESENT]>선물하기&lt;/a>)</font>
							</td>
						</tr>
						-->
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_ORDER]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							주문하기 버튼 <FONT class=font_blue>(예:&lt;a href=[BASKET_ORDER]>주문하기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_SHOPPING]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							계속쇼핑 버튼 <FONT class=font_blue>(예:&lt;a href=[BASKET_SHOPPING]>계속쇼핑&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_CLEAR]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							장바구니 비우기 버튼 <FONT class=font_blue>(예:&lt;a href=[BASKET_CLEAR]>장바구니 비우기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<!--
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center bgcolor=#F0F0F0>
							<B>특별회원 관련 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15">[IFROYAL]<br>[IFENDROYAL]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							특별회원에 대한 내용 기술 (특별회월일 경우에만 내용 출력)
							<pre style="line-height:15px">
<font class=font_blue>   <B>[IFROYAL]</B>
      내용
   <B>[IFENDROYAL]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ROYAL_IMG]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							특별회원 이미지 표시
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ROYAL_MSG1]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							특별회원 관련 메세지1 - 자동출력
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ROYAL_MSG2]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							특별회원 관련 메세지2 - 자동출력
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						-->
						</table>
						</td>
					</tr>
					<tr>
						<td width="20" colspan="2"><p>&nbsp;</p></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><p class="LIPoint">나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</p></td>
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