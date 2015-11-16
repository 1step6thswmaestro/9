<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-1";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$imagepath = $Dir.DataDir."shopimages/etc/";

$type = $_POST["type"];
$mapimage = $_POST["mapimage"];
$up_introtype = $_POST["up_introtype"];

if ($type=="up") {
	$sql = "SELECT COUNT(*) as cnt FROM tbldesign ";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$cnt = $row->cnt;
	mysql_free_result($result);

	if ($cnt > 0) {
		$onload = "<script> alert('정보 수정이 완료되었습니다.'); </script>";
		$sql = "UPDATE tbldesign SET ";
	} else {
		$onload = "<script> alert('정보 등록이 완료되었습니다.'); </script>";
		$sql = "INSERT tbldesign SET ";
	}
	if ($up_introtype == "A") {
		$up_imageA = $_FILES["up_imageA"];
		if(strlen($up_imageA[name])>0 && $up_imageA[size]>0) {
			if ($up_imageA[name] && (strtolower(substr($up_imageA[name],strlen($up_imageA[name])-3,3))=="gif" || strtolower(substr($up_imageA[name],strlen($up_imageA[name])-3,3))=="jpg")) {
				if ($up_imageA[size]>153600) {
					$onload = "<script>alert (\"올리실 이미지 용량은 150KB 이하의 파일만 가능합니다.\");</script>";
					$up_imageA[name] = $mapimage;
				} else {
					$up_imageA[name] = "intromap_".$up_imageA[name];
					if ($mapimage) {
						@unlink($imagepath.$mapimage);
					}
					move_uploaded_file($up_imageA[tmp_name],$imagepath.$up_imageA[name]);
					chmod($imagepath.$up_imageA[name],0606);
				}
			} else {
				$onload = "<script>alert (\"올리실 이미지는 gif, jpg파일만 가능합니다.\");</script>";
				$up_imageA[name] = $mapimage;
			}
		} else {
			$up_imageA[name] = $mapimage;
		}

		$up_companyname = $_POST["up_companyname"];
		$up_shopname = $_POST["up_shopname"];
		$up_ownername = $_POST["up_ownername"];
		$up_owneremail = $_POST["up_owneremail"];
		$up_info_tel = $_POST["up_info_tel"];
		$up_info_fax = $_POST["up_info_fax"];
		$up_info_counsel = $_POST["up_info_counsel"];
		$up_info_email = $_POST["up_info_email"];
		$up_privercyname = $_POST["up_privercyname"];
		$up_privercyemail = $_POST["up_privercyemail"];
		$up_contentA = $_POST["up_contentA"];
		$up_history = $_POST["up_history"];
		$sql.= "introtype		= '".$up_introtype."', ";
		$sql.= "mapimage		= '".$up_imageA[name]."', ";
		$sql.= "companyname		= '".$up_companyname."', ";
		$sql.= "shopname		= '".$up_shopname."', ";
		$sql.= "ownername		= '".$up_ownername."', ";
		$sql.= "owneremail		= '".$up_owneremail."', ";
		$sql.= "info_tel		= '".$up_info_tel."', ";
		$sql.= "info_fax		= '".$up_info_fax."', ";
		$sql.= "info_counsel	= '".$up_info_counsel."', ";
		$sql.= "info_email		= '".$up_info_email."', ";
		$sql.= "privercyname	= '".$up_privercyname."', ";
		$sql.= "privercyemail	= '".$up_privercyemail."', ";
		$sql.= "content			= '".$up_contentA."', ";
		$sql.= "history			= '".$up_history."' ";
	} else if ($up_introtype == "B") {
		$up_imageB = $_FILES["up_imageB"];
		if(strlen($up_imageB[name])>0 && $up_imageB[size]>0) {
			if ($up_imageB[name] && (strtolower(substr($up_imageB[name],strlen($up_imageB[name])-3,3))=="gif" || strtolower(substr($up_imageB[name],strlen($up_imageB[name])-3,3))=="jpg")) {
				if ($up_imageB[size]>153600) {
					$onload = "<script>alert (\"올리실 이미지 용량은 150KB 이하의 파일만 가능합니다.\");</script>";
					$up_imageB[name] = $mapimage;
				} else {
					$up_imageB[name] = "intromap_".$up_imageB[name];
					if ($mapimage) {
						@unlink($imagepath.$mapimage);
					}
					move_uploaded_file($up_imageB[tmp_name],$imagepath.$up_imageB[name]);
					chmod($imagepath.$up_imageB[name],0606);
				}
			} else {
				$onload = "<script>alert (\"올리실 이미지는 gif, jpg파일만 가능합니다.\");</script>";
				$up_imageB[name] = $mapimage;
			}
		} else {
			$up_imageB[name] = $mapimage;
		}

		$up_mapalign = $_POST["up_mapalign"];
		$up_contentB = $_POST["up_contentB"];
		$sql.= "introtype		= '".$up_introtype."', ";
		$sql.= "mapalign		= '".$up_mapalign."', ";
		$sql.= "mapimage		= '".$up_imageB[name]."', ";
		$sql.= "content			= '".$up_contentB."' ";
	} else if ($up_introtype == "C") {
		$up_title = $_POST["up_title"];
		$up_mapalign = $_POST["up_mapalign"];
		$up_contentC = $_POST["up_contentC"];
		if ($up_title=="Y") $up_mapalign = "left";
		else $up_mapalign = "top";
		$sql.= "introtype		= '".$up_introtype."', ";
		$sql.= "mapalign		= '".$up_mapalign."', ";
		$sql.= "content			= '".$up_contentC."' ";
	}
	mysql_query($sql,get_db_conn());
} else if ($type=="del") {
	$sql = "UPDATE tbldesign SET ";
	$sql.= "mapalign	= '', ";
	$sql.= "mapimage	= '' ";
	mysql_query($sql_del,get_db_conn());
	@unlink($imagepath.$mapimage);
	$onload = "<script> alert('약도 이미지가 삭제되었습니다.'); </script>";
}

$sql = "SELECT * FROM tbldesign ";
$result = mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$introtype		= $row->introtype;
	$mapimage		= $row->mapimage;
	$mapalign		= $row->mapalign;
	$companyname	= $row->companyname;
	$shopname		= $row->shopname;
	$ownername		= $row->ownername;
	$owneremail		= $row->owneremail;
	$info_tel		= $row->info_tel;
	$info_fax		= $row->info_fax;
	$info_counsel	= $row->info_counsel;
	$info_email		= $row->info_email;
	$privercyname	= $row->privercyname;
	$privercyemail	= $row->privercyemail;
	$content		= $row->content;
	$history		= $row->history;

	${"chk_type".$introtype} = "checked";
	${"select_map_".$mapalign} = "selected";
}
mysql_free_result($result);

$sql = "SELECT * FROM tblshopinfo ";
$result = mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	if(!$companyname)		$companyname = $row->companyname;
	if(!$ownername)			$ownername = $row->companyowner;
	if(!$owneremail)		$owneremail = $row->applyemail;
	if(!$shopname)			$shopname = $row->shopname;
	if(!$privercyname)		$privercyname = $row->privercyname;
	if(!$privercyemail)		$privercyemail = $row->privercyemail;
	if(!$info_tel)			$info_tel = $row->info_tel;
	if(!$info_fax)			$info_fax = $row->info_fax;
	if(!$info_email)		$info_email = $row->info_email;
}
mysql_free_result($result);

?>

<? INCLUDE "header.php"; ?>


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
</style>
<!-- # 에디터용 파일 호출 -->

<script type="text/javascript" src="lib.js.php"></script>
<script language="Javascript1.2" src="htmlarea/editor.js"></script>
<script>
_editor_url = "htmlarea/";
var ArrLayer = new Array ("type_A","type_B","type_C");

function ViewIntro(type){
	if(document.all){
		for(i=0;i<3;i++) {
			if (ArrLayer[i] == type)
				document.all[ArrLayer[i]].style.display="";
			else
				document.all[ArrLayer[i]].style.display="none";
		}
	} else if(document.getElementById){
		for(i=0;i<3;i++) {
			if (ArrLayer[i] == type)
				document.getElementByld[ArrLayer[i]].style.display="";
			else
				document.getElementByld[ArrLayer[i]].style.display="none";
		}
	} else if(document.layers){
		for(i=0;i<3;i++) {
			if (ArrLayer[i] == type)
				document.layers[ArrLayer[i]].display="";
			else
				document.layers[ArrLayer[i]].display="none";
		}
	}
}

function ChangeEditer(mode,obj){
	if (mode==form1.htmlmode.value) {
		return;
	} else {
		obj.checked=true;
		editor_setmode('up_contentC',mode);
	}
	form1.htmlmode.value=mode;
}

function CheckForm(type) {
	var form = document.form1;
	form.type.value=type;
	form.submit();
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
			<? include ("menu_shop.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 상점 기본정보 설정 &gt; <span class="2depth_select">회사 소개 / 약도</span></td>
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
				<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<tr>
					<td><img src="images/shop_companyintro_title.gif"  ALT=""></td>
				</tr>
				</tr>
					<td width="100%" background="images/title_bg.gif" height="21"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<tr>
					<td><img src="images/distribute_01.gif" WIDTH=7 HEIGHT=7 ALT=""></td>
					<td COLSPAN=2 background="images/distribute_02.gif"></td>
					<td><img src="images/distribute_03.gif" WIDTH=8 HEIGHT=7 ALT=""></td>
				</tr>
				<tr>
					<td background="images/distribute_04.gif"><img src="images/distribute_04.gif" WIDTH=7 HEIGHT="4" ALT=""></td>
					<td class="notice_blue"><img src="images/distribute_img.gif"></td>
					<td width="100%" class="notice_blue">회사소개와 연혁, 약도등를 설정합니다.</b></td>
					<td background="images/distribute_07.gif"><img src="images/distribute_07.gif" WIDTH=8 HEIGHT="4" ALT=""></td>
				</tr>
				<tr>
					<td><img src="images/distribute_08.gif" WIDTH=7 HEIGHT=8 ALT=""></td>
					<td COLSPAN=2 background="images/distribute_09.gif"></td>
					<td><img src="images/distribute_10.gif" WIDTH=8 HEIGHT=8 ALT=""></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<tr>
					<td><img src="images/shop_companyintro_stitle1.gif" WIDTH="152" HEIGHT=31 ALT=""></td>
					<td width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</td>
					<td><img src="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=mapimage value="<?=$mapimage?>">
			<input type=hidden name=htmlmode value='wysiwyg'>
			<tr>
				<td height=10>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td  bgcolor="#EDEDED" style="padding:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<table cellSpacing=0 cellPadding="0" width="100%" border=0 style="margin:10pt;">
						<tr>
							<td width="238" align=center><img src="images/shop_companyintro_img1.gif" width="156" height="149" border="0"></td>
							<td width="238" align=center><img src="images/shop_companyintro_img2.gif" width="156" height="149" border="0"></td>
							<td width="238" align=center><img src="images/shop_companyintro_img3.gif" width="156" height="149" border="0"></td>
						</tr>
						<tr>
							<td width="238" align=center><input type=radio id="idx_introtype1" name=up_introtype value="A" onclick="ViewIntro('type_A')" <?=$chk_typeA?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_introtype1><b>회사소개 A타입</b></label></td>
							<td width="238" align=center><input type=radio id="idx_introtype2" name=up_introtype value="B" onclick="ViewIntro('type_B')" <?=$chk_typeB?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_introtype2><b>회사소개 B타입</b></label></td>
							<td width="238" align=center><input type=radio id="idx_introtype3" name=up_introtype value="C" onclick="ViewIntro('type_C')" <?=$chk_typeC?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_introtype3><b>HTML로 꾸미기</b></label></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="30">&nbsp;</td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td >
					<div id=type_A style="margin-left:0;display:hide; display:<?=($introtype=="A"?"":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
						<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<tr>
							<td><img src="images/shop_companyintro_stitle2.gif" WIDTH="152" HEIGHT=31 ALT=""></td>
							<td width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</td>
							<td><img src="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr><td height="3"></td></tr>
					<tr>
						<td>
						<table cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<td background="images/table_top_line.gif" ><img src="images/table_top_line.gif"></td>
							<td background="images/table_top_line.gif" ></td>
						</tr>
						<tr>
							<td class="table_cell"  colspan="2"><img src="images/icon_point2.gif" width="8" height="11" border="0"><span class=font_orange>회사소개 내용입력시 본문의 넓이에 맞게 문장의 길이를 조절해 주세요.(전체HTML, 부분HTML ,TEXT 모두 지원)</span></td>
						</tr>
						<tr>
							<td colspan="2"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
						</tr>
						<tr>
							<td width="100%" colspan="2" class="space"><textarea name=up_contentA rows=15 style="width:100%" wrap=off class="textarea" lang="ej-editor1"><?=$content?></textarea></td>
						</tr>
						<tr>
							<td background="images/table_top_line.gif" ><img src="images/table_top_line.gif"></td>
							<td background="images/table_top_line.gif" ></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr><td height="30"></td></tr>
					<tr>
						<td>






						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td valign="top">
							<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td>
								<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
								<tr>
									<td><img src="images/shop_companyintro_stitle3.gif" WIDTH="152" HEIGHT=31 ALT=""></td>
									<td width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</td>
									<td><img src="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></td>
								</tr>
								</table>
								</td>
							</tr>
							<tr><td height=3></td></tr>
							<tr>
								<td >
								<table cellSpacing=0 cellPadding=0 width="100%" border=0>
								<tr>
									<td background="images/table_top_line.gif" width="140"><img src="images/table_top_line.gif"></td>
									<td background="images/table_top_line.gif" ></td>
								</tr>
								<tr>
									<td class="table_cell" width="65"><img src="images/icon_point2.gif" width="8" height="11" border="0">회 사 명</td>
									<td class="td_con1"><input type=text name=up_companyname value="<?=$companyname?>" size=30 maxlength=30 onKeyDown="chkFieldMaxLen(30)" class="input"></td>
								</tr>
								<tr>
									<td colspan="2"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
								</tr>
								<tr>
									<td class="table_cell" width="65"><img src="images/icon_point2.gif" width="8" height="11" border="0">쇼 핑 몰</td>
									<td class="td_con1"><input type=text name=up_shopname value="<?=$shopname?>" size=30 maxlength=30 onKeyDown="chkFieldMaxLen(30)" class="input"></td>
								</tr>
								<tr>
									<td colspan="2"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
								</tr>
								<tr>
									<td class="table_cell" width="65"><img src="images/icon_point2.gif" width="8" height="11" border="0">대표이사</td>
									<td class="td_con1"><input type=text name=up_ownername value="<?=$ownername?>" size=20 maxlength=10 onKeyDown="chkFieldMaxLen(10)" class="input"></td>
								</tr>
								<tr>
									<td colspan="2"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
								</tr>
								<tr>
									<td class="table_cell" width="65"><img src="images/icon_point2.gif" width="8" height="11" border="0">E-mail</td>
									<td class="td_con1"><input type=text name=up_owneremail value="<?=$owneremail?>" size=30 maxlength=50 onKeyDown="chkFieldMaxLen(50)" class="input"></td>
								</tr>
								<tr>
									<td background="images/table_top_line.gif" width="85"><img src="images/table_top_line.gif"></td>
									<td background="images/table_top_line.gif" ></td>
								</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td>

<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td  bgcolor="#EDEDED" style="padding:4pt;">
								<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
								<tr>
									<td width="100%">
									<table cellSpacing=0 cellPadding="5" width="100%" border=0>
									<tr>
										<td  height="30" background="images/blueline_bg.gif" class="font_blue"><p align="center"><b>회사소개 이미지 또는 약도</b></p></td>
									</tr>
									<tr>
										<td  style="padding:5pt;" align="left">
										<?
										if ($mapimage && file_exists($imagepath.$mapimage)) {
											echo "<img src=\"".$imagepath.$mapimage."\"";
											$width = getimagesize($imagepath.$mapimage);
											if ($width[0]>=$width[1] && $width[0]>=350) echo " width=350 ";
											else if($width[1]>500) echo " height=350";
											echo ">";
										} else {
											echo "&nbsp";
											$del_disabled = "disabled";
										}
										?>
										</td>
									</tr>
									<tr>
										<td width="100%">
										<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td colspan=2 align="left"><input type=file name=up_imageA size=30 class="input"><INPUT onClick="CheckForm('del')" <?=$del_disabled?> style="width:80px" type=button value="이미지 삭제" class="submit1"></td>
										</tr>
										</table>
										* 등록 가능한 이미지 확장자는 <span class="font_orange">GIF(gif)</span>, <span class="font_orange">JPG(jpg)</span>만 가능합니다.<br>
										* 등록 가능한 이미지 용량은 <span class="font_orange">최대 150KB</span> 까지 가능합니다.<br>
										* 이미지 사이즈는 <span class="font_orange">가로 300픽셀, 세로는 제한없습니다</span>.
										</td>
									</tr>
									</table>
									</td>
								</tr>
								</table>
								</td>
							</tr>
							</table>

								</td>
							</tr>
							<tr>
								<td >
								<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
								<tr>
									<td><img src="images/shop_companyintro_stitle4.gif" WIDTH="152" HEIGHT=31 ALT=""></td>
									<td width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</td>
									<td><img src="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></td>
								</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td  height="3"></td>
							</tr>
							<tr>
								<td >
								<table cellSpacing=0 cellPadding=0 width="100%" border=0>
								<tr>
									<td background="images/table_top_line.gif" ><img src="images/table_top_line.gif"></td>
									<td background="images/table_top_line.gif" ></td>
								</tr>
								<tr>
									<td colspan="2"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
								</tr>
								<tr>
									<td width="100%" colspan="2" class="space"><textarea name=up_history rows=15 style="width:100%" wrap=off class="textarea" lang="ej-editor1"><?=$history ?></textarea></td>
								</tr>
								<tr>
									<td background="images/table_top_line.gif" ><img src="images/table_top_line.gif"></td>
									<td background="images/table_top_line.gif" ></td>
								</tr>
								</table>
								</td>
							</tr>
							</table>
							</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td height="30"></td>
					</tr>
					<tr>
						<td>
						<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<tr>
							<td><img src="images/shop_companyintro_stitle5.gif" WIDTH="152" HEIGHT=31 ALT=""></td>
							<td width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</td>
							<td><img src="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td height="3"></td>
					</tr>
					<tr>
						<td>
						<table cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<td background="images/table_top_line.gif" ><img src="images/table_top_line.gif"></td>
							<td background="images/table_top_line.gif"  colspan="3"></td>
						</tr>
						<tr>
							<td class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">전화번호</td>
							<td class="td_con1" ><input type=text name=up_info_tel value="<?=$info_tel?>" size=30 maxlength=50 onKeyDown="chkFieldMaxLen(50)" class="input"></td>
						</tr>
						<tr>
							<td colspan="2"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
						</tr>
						<tr>
							<td class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">팩 스</td>
							<td class="td_con1" ><input type=text name=up_info_fax value="<?=$info_fax?>" size=20 maxlength=20 onKeyDown="chkFieldMaxLen(20)" class="input"></td>
						</tr>
						<tr>
							<td colspan="2"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
						</tr>
						<tr>
							<td class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">상담시간</td>
							<td class="td_con1" ><input type=text name=up_info_counsel value="<?=$info_counsel ?>" size=40 maxlength=50 onKeyDown="chkFieldMaxLen(50)" class="input"> <span class=font_orange>예) 평일 09~18시, 토요일 09~12시</span></td>
						</tr>
						<tr>
							<td colspan="2"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
						</tr>
						<tr>
							<td class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">E-mail</td>
							<td class="td_con1" ><input type=text name=up_info_email value="<?=$info_email ?>" size=40 maxlength=50 onKeyDown="chkFieldMaxLen(50)" class="input"></td>
						</tr>
						<tr>
							<td colspan="2"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
						</tr>
						<tr>
							<td class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">개인정보 담당자</td>
							<td class="td_con1" ><input type=text name=up_privercyname value="<?=$privercyname?>" size=20 onKeyDown="chkFieldMaxLen(10)" class="input"></td>
						</tr>
						<tr>
							<td colspan="2"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
						</tr>
						<tr>
							<td class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">담당자 이메일</td>
							<td class="td_con1" ><input type=text name=up_privercyemail value="<?=$privercyemail?>" size=30 maxlength=50 onKeyDown="chkFieldMaxLen(50)" class="input"></td>
						</tr>
						<tr>
							<td background="images/table_top_line.gif" ><img src="images/table_top_line.gif"></td>
							<td background="images/table_top_line.gif"  colspan="3"></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</div>
					</td>
				</tr>
				<tr>
					<td >
					<div id=type_B style="margin-left:0;display:hide; display:<?=($introtype=="B"?"":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
						<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<tr>
							<td><img src="images/shop_companyintro_stitle2.gif" WIDTH="152" HEIGHT=31 ALT=""></td>
							<td width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</td>
							<td><img src="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td height="3"></td>
					</tr>
					<tr>
						<td>
						<table cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<td background="images/table_top_line.gif" ><img src="images/table_top_line.gif"></td>
							<td background="images/table_top_line.gif" ></td>
						</tr>
						<tr>
							<td class="table_cell"  colspan="2"><img src="images/icon_point2.gif" width="8" height="11" border="0"><span class=font_orange>회사소개 내용입력시 본문의 넓이에 맞게 문장의 길이를 조절해 주세요.(전체HTML, 부분HTML ,TEXT 모두 지원)</span></td>
						</tr>
						<tr>
							<td colspan="2"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
						</tr>
						<tr>
							<td width="100%" colspan="2" class="space"><textarea name=up_contentB rows=15 style="width:100%" wrap=off class="textarea" lang="ej-editor1"><?=$content?></textarea></td>
						</tr>
						<tr>
							<td background="images/table_top_line.gif" ><img src="images/table_top_line.gif"></td>
							<td background="images/table_top_line.gif" ></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td></td>
					</tr>
					<tr>
						<td height="30"></td>
					</tr>
					<tr>
						<td>
						<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<tr>
							<td><img src="images/shop_companyintro_stitle6.gif" WIDTH="152" HEIGHT=31 ALT=""></td>
							<td width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</td>
							<td><img src="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td height="3"></td>
					</tr>
					<tr>
						<td>
						<table cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<td background="images/table_top_line.gif" ><img src="images/table_top_line.gif"></td>
							<td background="images/table_top_line.gif" ></td>
						</tr>
						<tr>
							<td class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">회사소개<br>&nbsp;&nbsp;이미지 또는 약도</td>
							<td class="td_con1" ><select name=up_mapalign class="select">
								<option value="left" <?=$select_map_left?>>왼쪽
								<option value="right" <?=$select_map_right?>>오른쪽
								<option value="top" <?=$select_map_top?>>위
								<option value="bottom" <?=$select_map_bottom?>>아래
							</select> <input type=file name=up_imageB size=35 class="input"> <INPUT onclick="CheckForm('del')" type=button value="이미지 삭제" class="submit1" <?=$del_disabled?>></td>
						</tr>
						<tr>
							<td colspan="2"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
						</tr>
						<tr>
							<td width="742" colspan="2"><p align="center">
							<?
							if ($mapimage && file_exists($imagepath.$mapimage)) {
								echo "<img src=\"".$imagepath.$mapimage."\"";
								$width = getimagesize($imagepath.$mapimage);
								if ($width[0]>=$width[1] && $width[0]>=500) echo " width=500 ";
								else if($width[1]>500) echo " height=500";
								echo " style=\"border-width:1pt; border-color:rgb(0,153,204); border-style:solid;\">";
							} else {
								echo "&nbsp";
								$del_disabled = "disabled";
							}
							?>
							</td>
						</tr>
						<tr>
							<td background="images/table_top_line.gif" ><img src="images/table_top_line.gif"></td>
							<td background="images/table_top_line.gif" ></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</div>
					</td>
				</tr>
				<tr>
					<td >
					<div id=type_C style="margin-left:0;display:hide; display:<?=($introtype=="C"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
						<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<tr>
							<td><img src="images/shop_companyintro_stitle2.gif" WIDTH="152" HEIGHT=31 ALT=""></td>
							<td width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</td>
							<td><img src="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td height="3"></td>
					</tr>
					<tr>
						<td>
						<table cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<td background="images/table_top_line.gif" ><img src="images/table_top_line.gif"></td>
							<td background="images/table_top_line.gif" ></td>
						</tr>
						<tr>
							<td class="table_cell" width="136"><img src="images/icon_point2.gif" width="8" height="11" border="0"> 편집방법 선택</td>
							<td class="td_con1" width="584"><input type=radio name=chk_webedit checked onclick="JavaScript:ChangeEditer('wysiwyg',this)" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none">웹편집기로 입력하기(권장)  <input type=radio name=chk_webedit onclick="JavaScript:ChangeEditer('textedit',this);" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none">직접 HTML로 입력하기</td>
						</tr>
						<tr>
							<td colspan="2"  background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></td>
						</tr>
						<tr>
							<td width="100%" colspan="2" class="space"><textarea name=up_contentC rows=20 wrap=off style="width:100%; height:300" class="textarea" lang="ej-editor1"><?=$content?></textarea></td>
						</tr>
						<tr>
							<td colspan="2" height="2"></td>
						</tr>
						<tr>
							<td width="100%" colspan="2" class="space" align="center"><input type=checkbox id="idx_title1" name=up_title value="Y" <? if($mapalign!="top") echo "checked"?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_title1>페이지 상단 타이틀 이미지 포함</label></td>
						</tr>
						<tr>
							<td colspan="2" height="2"></td>
						</tr>
						<tr>
							<td background="images/table_top_line.gif" ><img src="images/table_top_line.gif"></td>
							<td background="images/table_top_line.gif" ></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td></td>
					</tr>
					</table>
					</div>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td align="center" height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('up');"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<tr>
					<td><img src="images/manual_top1.gif" border="0"></td>
					<td width="100%" background="images/manual_bg.gif"><img src="images/manual_title.gif" border="0"></td>
					<td><img src="images/manual_top2.gif" border="0"></td>
				</tr>
				<tr>
					<td background="images/manual_left1.gif"></td>
					<td style="padding-top:5px;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">디자인내용 입력 안내</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td class="space_top">html태그 없이 Text만 입력할 경우 가운데 정렬이 기본입니다.<br>Text&nbsp;/부분 html을 사용 할 경우 &nbsp;단락(br)은 엔터(Enter]키를 이용하시면 됩니다.<br>&nbsp;&nbsp;&nbsp;부분 html예)<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;img src=000.jpg&gt; ↘ (Enter)<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[MAINIMG] ↘ (Enter)<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;쇼핑몰을방문해주셔서 감사합니다.</td>
					</tr>
					<tr>
						<td height="20"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</td>
					</tr>
					</table>
					</td>
					<td background="images/manual_right1.gif"><img src="images/manual_right1.gif" WIDTH=18 HEIGHT="2" ALT=""></td>
				</tr>
				<tr>
					<td><img src="images/manual_left2.gif" border="0"></td>
					<td background="images/manual_down.gif"></td>
					<td><img src="images/manual_right2.gif" border="0"></td>
				</tr>
				</table>
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

<script language="javascript1.2">
//editor_generate('up_contentC');
</script>

<?= $onload ?>

<? INCLUDE "copyright.php"; ?>