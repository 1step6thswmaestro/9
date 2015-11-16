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

$imagepath=$Dir.DataDir."shopimages/etc/";

$type=$_POST["type"];
$eventloc=$_POST["eventloc"];
$old_image=$_POST["old_image"];
$up_design_type=$_POST["up_design_type"];
$up_body=chop($_POST["up_body"]);
$up_image=$_FILES["up_image"];

if($type=="up") {
	if ($up_design_type==1) {
		if ($up_image[name] && (strtolower(substr($up_image[name],strlen($up_image[name])-3,3))=="gif" || strtolower(substr($up_image[name],strlen($up_image[name])-3,3))=="jpg")) {
			if($up_image[size] < 153600) {
				$up_image[name]="eventprdetail".substr($up_image[name],-4);
				if(strlen($old_image)>0 && file_exists($imagepath.$old_image)) {
					unlink($imagepath.$old_image);
				}
				move_uploaded_file($up_image[tmp_name],$imagepath.$up_image[name]);
				chmod($imagepath.$up_image[name],0606);
			} else {
				$up_image[name] = $old_image;
			}
		} else {
			$up_image[name] = $old_image;
		}
	} else {
		@unlink($imagepath.$old_image);
	}

	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type = 'detailimg'";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	mysql_free_result($result);
	$cnt=$row->cnt;
	if ($cnt==1) {
		$sql="UPDATE tbldesignnewpage SET ";
		$sql.= "filename	= '".$up_image[name]."', ";
		$sql.= "body		= '".$up_body."', ";
		$sql.= "leftmenu	= '".$eventloc."', ";
		$sql.= "code		= '".$up_design_type."' ";
		$sql.= "WHERE type = 'detailimg'";
		$onload="<script>alert('내용 수정이 완료되었습니다.');</script>";
	} else {
		$sql="INSERT INTO tbldesignnewpage (type,subject,body,filename,leftmenu,code) VALUES ('detailimg','상품 상세 공통 이벤트','".$up_body."','".$up_image[name]."','".$eventloc."','".$up_design_type."')";
		$onload="<script>alert('내용 등록이 완료되었습니다.');</script>";
	}
	mysql_query($sql,get_db_conn());

} else if($type=="del") {
	$sql="DELETE FROM tbldesignnewpage WHERE type = 'detailimg'";
	$result = mysql_query($sql,get_db_conn());
	$onload="<script>alert('상품상세 공통이벤트 정보가 초기화 되었습니다.');</script>";
}

$sql = "SELECT body,filename,code,leftmenu FROM tbldesignnewpage WHERE type = 'detailimg' ";
$result = mysql_query($sql,get_db_conn());
if ($row = mysql_fetch_object($result)) {
	$body=$row->body;
	$filename=$row->filename;
	$design_type=$row->code;
	$eventloc=$row->leftmenu;
} else {
	$filename="";
	$design_type="1";
	$eventloc="";
}
mysql_free_result($result);
${"chk_type".$design_type} = "checked";
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>

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
function ChangeType(type){
	if (type==1) {
		document.form1.up_image.disabled=false;
		document.form1.up_body.disabled=true;
		document.form1.up_body.style.backgroundColor = '#EFEFEF';
		document.form1.up_image.style.backgroundColor = '#FFFFFF';
	} else {
		document.form1.up_image.disabled=true;
		document.form1.up_body.disabled=false;
		document.form1.up_body.style.backgroundColor = '#FFFFFF';
		document.form1.up_image.style.backgroundColor = '#EFEFEF';
	}
}
function del(){
	if (confirm("상품 상세 공통이벤트를 초기화 하시겠습니까?")) {
		document.form1.type.value="del";
		document.form1.submit();
	}
}
function CheckForm(){
	var eventloc = "";
	for(var i=0;i<form1.eventloc.length;i++){
		if(form1.eventloc[i].checked==true){
			eventloc=form1.eventloc[i].value;
			break;
		}
	}
	if(eventloc.length==0) {
		alert("공통 이벤트 위치를 선택하세요.");
		document.form1.eventloc[0].focus();
		return;
	}

	var design_type = "";
	for(var i=0;i<form1.up_design_type.length;i++){
		if(form1.up_design_type[i].checked==true){
			design_type=form1.up_design_type[i].value;
			break;
		}
	}
	if (design_type.length==0) {
		alert("이벤트 영역 디자인 타입을 선택하세요");
		form1.up_design_type[0].focus();
		return;
	} else if (design_type==1) {
		if (form1.up_image.value.length==0 && "<?=$filename?>"=="") {
			alert("이미지 파일을 선택하세요");
			form1.up_image.focus();
			return;
		}
		form1.up_body.value="";
	} else if (design_type==2) {
		if (form1.up_body.value.length==0) {
			alert("내용을 입력하세요");
			form1.up_body.focus();
			return;
		}
	}
	if (confirm("등록하시겠습니까?")) {
		document.form1.type.value="up";
		document.form1.submit();
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
			<? include ("menu_market.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 마케팅지원 &gt; 이벤트/사은품 기능 설정 &gt; <span class="2depth_select">상품 상세 공통 이벤트 관리</span></td>
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
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<TR>
							<TD><IMG SRC="images/market_eventprdetail_title.gif" ALT=""></TD>
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
							<TD width="100%" class="notice_blue">상품 상세페이지 정보란에  진행중인 이벤트를 표기할 수 있습니다.</TD>
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
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td bgcolor="#ededed" style="padding:4pt;">
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td width="100%" bgcolor="white">
											<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
												<TR>
													<TD align=center height="30" background="images/blueline_bg.gif"><b><font color=#555555>공통이벤트 위치 선택</span></b></TD>
												</TR>
												<TR>
													<TD width="100%" background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
												</TR>
												<TR>
													<TD width="100%" style="padding:10pt;">
														<table cellpadding="0" cellspacing="0" width="100%">
															<tr>
																<td width="240" align=center valign="top"><IMG src="images/market_detailevent1.gif" border=0 width="150" height="140" class="imgline"><br><INPUT id=idx_eventloc1 type=radio value=1 <?if($eventloc==1)echo"checked";?> name=eventloc><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_eventloc1>상품 스펙 바로 아래</label>&nbsp;</td>
																<td width="240" align=center valign="top"><IMG src="images/market_detailevent2.gif" border=0 width="150" height="140" class="imgline"><br><INPUT id=idx_eventloc2 type=radio value=2 <?if($eventloc==2)echo"checked";?> name=eventloc><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_eventloc2>상품 상세정보 바로 위</label>&nbsp;</td>
																<td width="240" align=center valign="top"><IMG src="images/market_detailevent3.gif" border=0 width="150" height="140" class="imgline"><br><INPUT id=idx_eventloc3 type=radio value=3 <?if($eventloc==3)echo"checked";?> name=eventloc><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_eventloc3>상품 상세정보 바로 아래</label>&nbsp;</td>
															</tr>
														</table>
													</TD>
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
			<tr><td height="40"></td></tr>
			<TR>
				<TD><IMG SRC="images/market_eventprdetail_st1.gif" WIDTH="197" HEIGHT=31 ALT=""></TD>
			</tr>
			<tr>
				<td>
					<TABLE WIDTH="100%" BORDER="0" CELLPADDING=0 CELLSPACING=0>
						<colgroup>
							<col width="150"></col>
							<col width=""></col>
						</colgroup>
						<tr><td background="images/table_top_line.gif" colSpan="2"></tr>
						<tr>
							<td class="table_cell">편집타입 선택</td>
							<td class="td_con1">
								<INPUT id="idx_design_type1" onclick="ChangeType(1)" type="radio" value="1" <?=$chk_type1?> name="up_design_type" /><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for="idx_design_type1">이미지로 등록하기</LABEL>
								<INPUT id="idx_design_type2" onclick="ChangeType(2)" type="radio" value="2" <?=$chk_type2?> name="up_design_type" /><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for="idx_design_type2">HTML로 편집하기</LABEL>
							</TD>
						</TR>
						<tr><td background="images/table_con_line.gif" colSpan="2"></tr>

						<tr>
							<td class="table_cell">이미지</td>
							<td class="td_con1">
								<!--이미지로 등록시 출력-->
								<INPUT style="WIDTH:500px;" type="file" name="up_image" class="input" /><br />
								<span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">* 이미지는 150KB 이하의 GIF, JPG만 가능</span>
								<?
								if (strlen($filename)>0) {
									if (file_exists($imagepath.$filename)==true) {
										$width = getimagesize($imagepath.$filename);
										if ($width[0]>=700) $width=" width=700 ";
									}
								?>
								<div style="margin:10px;"><img src="<?=$imagepath.$filename?>" <?=$width?> /></div>
								<input type="hidden" name="old_image" value="<?=$filename?>" />
								<? } ?>
								<!--이미지로 등록시출력 끝 -->
							</td>
						</tr>
						<tr><td background="images/table_con_line.gif" colSpan="2"></tr>

						<tr>
							<td class="table_cell">HTML 편집</td>
							<td class="td_con1">
								<!--html로 등록시 출력-->
								<TEXTAREA style="WIDTH:100%;" disabled name="up_body" rows="10" wrap="off" class="textarea"><?=$body?></TEXTAREA>
								<!--html로 등록시 출력 끝 -->
							</td>
						</tr>

						<tr><td background="images/table_top_line.gif" colSpan="2"></tr>
					</table>
				</td>
			</tr>
			<!-- <tr>
				<td><IMG SRC="images/market_eventprdetail_st1.gif" border="0"></td>
			</tr>
			<tr><td height="5"></td></tr>
			<tr>
				<td> -->
				<!--이미지로 등록시출력 끝 -->
				<!--html로 등록시 출력-->
				<!-- <TEXTAREA style="WIDTH:100%;" disabled name="up_body" rows="10" wrap="off" class="textarea" lang="ej-editor1" ><?=$body?></TEXTAREA> -->
				<!--html로 등록시 출력 끝 -->
				<!-- </td>
			</tr> -->
			<tr><td height=10></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a>&nbsp;&nbsp;<a href="javascript:del();"><img src="images/btn_initialization.gif" width="113" height="38" border="0" hspace="1"></a></td>
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
						<td><span class="font_dotline">상품상세 공통이벤트 관리 주의사항</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 상품상세 공통이벤트는 "상품 상세화면 템플릿"을 사용하는 모든 상품에 출력됩니다.<br>
						<b>&nbsp;&nbsp;</b><a href="javascript:parent.topframe.GoMenu(2,'design_pdetail.php');"><span class="font_blue">디자인 관리 > 템플릿-메인 및 카테고리 > 상품 상세화면 템플릿</span></a></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">
							- [초기화] 버튼 클릭시 모든 내용은 삭제되며 복원되지 않으므로 신중히 처리하시기 바랍니다.<br />
							- 개별디자인으로 상품상세 페이지를 처리할 경우 공통이벤트 위치 선택과 상관없이 매크로 출력부분에 상품상세 공통이벤트가 출력됩니다.<br />
							<b>&nbsp;&nbsp;</b><a href="javascript:parent.topframe.GoMenu(3,'design_eachpdetail.php');"><span class="font_blue">디자인 관리 > 개별디자인-메인, 카테고리 > 상품상세 화면 꾸미기</span></a>
						</td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">상품상세 공통이벤트 등록 방법</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">① 상품상세에 공통이벤트를 출력할 위치(상품 스펙 바로 아래, 상품 상세정보 바로 위, 상품 상세정보 바로 아래)를 선택합니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">② 디자인 타입을 선택 후 디자인을 입력합니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">③ 디자인을 모두 입력하였다면 [적용하기] 버튼을 클릭합니다.</td>
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
<script>ChangeType(<?=$design_type?>);</script>
<?=$onload?>
<? INCLUDE "copyright.php"; ?>