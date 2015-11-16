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
$code=$_POST["code"];
$intitle=$_POST["intitle"];

if(strlen($code)==0) {
	$code="1";
}


if($code=="1") {
	$ptype="tag";
	$pmsg="인기태그";
} else if($code=="2") {
	$ptype="tagsearch";
	$pmsg="태그검색";
}

if($intitle=="Y") {
	$leftmenu="Y";
} else {
	$leftmenu="N";
}

$insertKey = $ptype;


$subject = $pmsg." 화면";

// 백업 / 복구
if ( $type=="store" OR $type=="restore" ) {
	$MSG = adminDesingBackup ( $type, $ptype, $body, $subject, '', '', $leftmenu );
	$onload="<script>alert(\"".$MSG."\");</script>";
}



if($type=="update" && strlen($body)>0 && preg_match("/^(1|2){1}/", $code)) {

	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='".$ptype."' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT tbldesignnewpage SET ";
		$sql.= "type		= '".$ptype."', ";
		$sql.= "subject		= '".$pmsg." 화면', ";
		$sql.= "leftmenu	= '".$leftmenu."', ";
		$sql.= "body		= '".$body."' ";
		mysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET ";
		$sql.= "leftmenu	= '".$leftmenu."', ";
		$sql.= "body		= '".$body."' ";
		$sql.= "WHERE type='".$ptype."' ";
		mysql_query($sql,get_db_conn());
	}
	mysql_free_result($result);

	$sql = "UPDATE tblshopinfo SET design_".$ptype."='U' ";
	mysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>alert(\"".$pmsg." 화면 디자인 수정이 완료되었습니다.\");</script>";
} else if($type=="delete" && preg_match("/^(1|2){1}/", $code)) {
	if($code=="1") {
		$ptype="tag";
		$pmsg="인기태그";
	} else if($code=="2") {
		$ptype="tagsearch";
		$pmsg="태그검색";
	}

	$sql = "DELETE FROM tbldesignnewpage WHERE type='".$ptype."' ";
	mysql_query($sql,get_db_conn());

	$sql = "UPDATE tblshopinfo SET design_".$ptype."='001' ";
	mysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>alert(\"".$pmsg." 화면 디자인 삭제가 완료되었습니다.\");</script>";
} else if($type=="clear" && preg_match("/^(1|2){1}/", $code)) {
	$intitle="";
	$body="";
	if($code=="1") {
		$sql = "SELECT body FROM tbldesigndefault WHERE type='tag' ";
	} else if($code=="2") {
		$sql = "SELECT body FROM tbldesigndefault WHERE type='tagsearch' ";
	}
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$body=$row->body;
	}
	mysql_free_result($result);
}




if($type!="clear" && preg_match("/^(1|2){1}/", $code)) {
	if($code=="1") $ptype="tag";
	else if($code=="2") $ptype="tagsearch";

	$body="";
	$intitle="";
	$sql = "SELECT leftmenu,body FROM tbldesignnewpage WHERE type='".$ptype."' ";
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
			alert("디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("디자인을 삭제하시겠습니까?")) {
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
			alert("디자인 내용을 입력하세요.");
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

function change_page(val) {
	document.form1.type.value="change";
	document.form1.submit();
}

//매크로 보기(팝업)
function macroview(){
	window.open("http://www.getmall.co.kr/macro/pages/tag_macro.html","tag_macro","height=800,width=680,scrollbars=no");
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt; <span class="2depth_select">태그 화면 꾸미기</span></td>
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
							<TD><IMG SRC="images/design_eachtag_title.gif" ALT=""></TD>
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
					<TD width="100%" class="notice_blue"><p>인기태그 및 태그검색 화면 디자인을 자유롭게 디자인 하실 수 있습니다.</p></TD>
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
					<TD><IMG SRC="images/design_eachtag_stitle1.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">
						&nbsp;&nbsp;<a href="javascript:macroview();"><img src="images/btn_macroview.gif" border="0" align="absmiddle" alt="" /></a>&nbsp;&nbsp;<a href="http://www.getmall.co.kr/macro/data/tag_design.zip"><img src="images/btn_defaultcode.gif" border="0" align="absmiddle" alt="" /></a>
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
			<tr>
				<td height="3"></td>
			</tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td style="padding-top:3pt;">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				<col width=139></col>
				<col width=></col>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">태그 화면 선택</TD>
					<TD class="td_con1"><select name=code onchange="change_page(options.value)" style="width:330" class="input">
					<option value="1" <?if($code=="1")echo"selected";?>>인기태그화면</option>
					<option value="2" <?if($code=="2")echo"selected";?>>태그검색화면</option>
					</select></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan="2"><TEXTAREA style="WIDTH: 100%; HEIGHT: 300px" name=body class="textarea"><?=htmlspecialchars($body)?></TEXTAREA><br><input type=checkbox name=intitle value="Y" <?if($intitle=="Y")echo"checked";?>> <b><span style="letter-spacing:-0.5pt;"><span class="font_orange">기본 타이틀 이미지 유지 - 타이틀 이하 부분부터 디자인 변경</span>(미체크시 기존 타이틀 이미지 없어짐으로 직접 편집하여 사용)</b></span></TD>
				</TR>
				</TABLE>
				</td>
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
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><p class="LIPoint"><B><span class="font_orange">태그 화면 매크로명령어</span></B>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</p></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><p>&nbsp;</p></td>
						<td >
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<col width=150></col>
						<col width=></col>

						<?if($code=="1"){?>

						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAGDATESTART]</td>
							<td class=td_con1 style="padding-left:5;">
							집계기간 (집계 시작일) <FONT class=font_blue>(예:집계기간 : [TAGDATESTART] ~ [TAGDATEEND])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAGDATEEND]</td>
							<td class=td_con1 style="padding-left:5;">
							집계기간 (집계 종료일) <FONT class=font_blue>(예:집계기간 : [TAGDATESTART] ~ [TAGDATEEND])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAGSORT1]</td>
							<td class=td_con1 style="padding-left:5;">
							"가나다순" 정렬 버튼 <FONT class=font_blue>(예:&lt;a href=[TAGSORT1]>가나다순 정렬&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAGSORT2]</td>
							<td class=td_con1 style="padding-left:5;">
							"인기순" 정렬 버튼 <FONT class=font_blue>(예:&lt;a href=[TAGSORT2]>인기순 정렬&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAGLIST]</td>
							<td class=td_con1 style="padding-left:5;">
							태그목록 <FONT class=font_blue>(예:&lt;a href=[TAGLIST]>태그목록&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAGSEARCHINPUT_입력폼 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							태그검색 입력폼 <FONT class=font_blue>(예:[TAGSEARCHINPUT_width:150px;])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAGSEARCHOK]</td>
							<td class=td_con1 style="padding-left:5;">
							태그검색 버튼 <FONT class=font_blue>(예:&lt;a href=[TAGSEARCHOK]>검색&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>

						<?}else if($code=="2"){?>

						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAGLINK]</td>
							<td class=td_con1 style="padding-left:5;">
							인기태그 바로가기 링크 <FONT class=font_blue>(예:&lt;a href=[TAGLINK]>인기태그 바로가기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAGSEARCHINPUT_입력폼 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							태그검색 입력폼 <FONT class=font_blue>(예:[TAGSEARCHINPUT_width:150px;])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAGSEARCHOK]</td>
							<td class=td_con1 style="padding-left:5;">
							태그검색 버튼 <FONT class=font_blue>(예:&lt;a href=[TAGSEARCHOK]>검색&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAGKEYWORD]</td>
							<td class=td_con1 style="padding-left:5;">
							태그 검색어 <FONT class=font_blue>(예:[TAGKEYWORD](으)로 태그가 등록되어 있는 상품입니다.)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAGTOTAL]</td>
							<td class=td_con1 style="padding-left:5;">
							태그검색 결과건수 <FONT class=font_blue>(예:총 [TAGTOTAL]건의 상품이 존재합니다)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[IFTAG]<br>[IFELSETAG]<br>[IFENDTAG]</td>
							<td class=td_con1 style="padding-left:5;">
							태그검색 결과가 있을 경우와 없을 경우
							<pre style="line-height:15px">
<font color=blue>   <B>[IFTAG]</B>
      태그검색 결과가 <FONT COLOR="red"><B>있을</B></FONT> 경우의 내용
   <B>[IFELSETAG]</B>
      태그검색 결과가 <FONT COLOR="red"><B>없을</B></FONT> 경우의 내용
   <B>[IFENDTAG]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[FORTAG]<br>[FORENDTAG]</td>
							<td class=td_con1 style="padding-left:5;">
							[FORTAG] 검색된 상품 한개에 대한 내용 [FORENDTAG]
							<pre style="line-height:15px">
<font color=blue>   [IFTAG]
       <B>[FORTAG]</B>상품 하나에 대한 내용 기술<B>[FORENDTAG]</B>
   [IFELSETAG]
       [TAGKEYWORD](으)로 태그가 등록된 상품이 없습니다.
   [IFENDTAG]</font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAG_PRIMG]</td>
							<td class=td_con1 style="padding-left:5;">
							상품이미지
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAG_PRNAME]</td>
							<td class=td_con1 style="padding-left:5;">
							상품명
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAG_ADDCODE]</td>
							<td class=td_con1 style="padding-left:5;">
							상품특이사항
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAG_PRTITLE]</td>
							<td class=td_con1 style="padding-left:5;">
							상품특이사항과 상품명 같이 사용
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAG_CONSUMPRICE]</td>
							<td class=td_con1 style="padding-left:5;">
							시중가격 <FONT class=font_blue>(예:[TAG_CONSUMPRICE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAG_SELLPRICE]</td>
							<td class=td_con1 style="padding-left:5;">
							판매가격 <FONT class=font_blue>(예:[TAG_SELLPRICE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAG_RESERVE]</td>
							<td class=td_con1 style="padding-left:5;">
							적립금 <FONT class=font_blue>(예:[TAG_RESERVE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAG_COUNT]</td>
							<td class=td_con1 style="padding-left:5;">
							등록 태그수 <FONT class=font_blue>(예:&lt;B>[TAG_COUNT]&lt;/B>개)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell bgcolor=#F0F0F0>
							<pre style="line-height:15px">
  [FORPRTAG_?]
  [TAGNAME] [IFETC][ENDETC]
  [FORENDPRTAG]</pre>
							</td>
							<td class=td_con1 style="padding-left:5;">
							해당상품 태그 한개에 대한 내용시작 <FONT class=font_orange>(_? : 출력할 태그 갯수)</font>
							<pre style="line-height:15px">
<font color=blue> <B>[FORPRTAG_3]</B>
     [TAGNAME] - 태그이름 [IFETC][ENDETC]- 태그이름 뒤 구분자 <br>
     <FONT class=font_blue>(예:[IFETC],[ENDETC])</font> <font color=red><B>결과</B> : 컴퓨터<B>,</B>노트북<B>,</B>데스크탑</font>
 <B>[FORENDPRTAG]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAG_QUICKVIEW]</td>
							<td class=td_con1 style="padding-left:5;">
							상품 퀵뷰 버튼 <FONT class=font_blue>(예:&lt;a href=[TAG_QUICKVIEW]>퀵뷰&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAG_WISH]</td>
							<td class=td_con1 style="padding-left:5;">
							위시리스트 담기 버튼 <FONT class=font_blue>(예:&lt;a href=[TAG_WISH]>위시리스트&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAG_BASKET]</td>
							<td class=td_con1 style="padding-left:5;">
							장바구니 담기 버튼 <FONT class=font_blue>(예:&lt;a href=[TAG_BASKET]>장바구니&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PAGE]</td>
							<td class=td_con1 style="padding-left:5;">
							페이지 표시
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>

						<?}?>

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