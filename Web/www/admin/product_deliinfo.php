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

$imagepath=$Dir.DataDir."shopimages/etc/";
$filename="aboutdeliinfo.gif";

$mode=$_POST["mode"];

if($mode=="update") {
	$deliinfook=$_POST["deliinfook"];
	$deliinfotype=$_POST["deliinfotype"];

	if($deliinfotype!="IMAGE") {
		if(file_exists($imagepath.$filename)) {
			unlink($imagepath.$filename);
		}
	}

	if($deliinfotype=="TEXT") {
		$deliinfotext1=$_POST["deliinfotext1"];
		$deliinfotext2=$_POST["deliinfotext2"];
		$deli_info=$deliinfook."=".$deliinfotype."=".$deliinfotext1."=".$deliinfotext2;
	} else if($deliinfotype=="IMAGE") {
		//이미지 업로드 처리
		$up_image=$_FILES["deliinfoimage"];
		if ($up_image["size"]>153600) {
			echo "<script>alert ('이미지 용량은 150KB를 넘을 수 없습니다.');location.href='".$_SERVER[PHP_SELF]."';</script>\n";
			exit;
		}

		if (strlen($up_image[name])>0 && $up_image["size"]>0 && (strtolower(substr($up_image[name],strlen($up_image[name])-3,3))=="gif" || strtolower(substr($up_image[name],strlen($up_image[name])-3,3))=="jpg")) {
			$up_image[name]=$filename;
			if(file_exists($imagepath.$filename)) {
				unlink($imagepath.$filename);
			}
			move_uploaded_file($up_image[tmp_name],$imagepath.$up_image[name]);
			chmod($imagepath.$up_image[name],0606);
		}
		$deli_info=$deliinfook."=".$deliinfotype;
	} else if($deliinfotype=="HTML") {
		$deliinfohtml=$_POST["deliinfohtml"];
		$deli_info=$deliinfook."=".$deliinfotype."=".$deliinfohtml;
	}

	if(strlen($deli_info)>0) {
		$sql = "UPDATE tblshopinfo SET deli_info='".$deli_info."' ";
		mysql_query($sql,get_db_conn());
		DeleteCache("tblshopinfo.cache");
		$onload = "<script> alert('정보 수정이 완료되었습니다.'); </script>";
	}
}

$sql = "SELECT deli_info FROM tblshopinfo";
$result=mysql_query($sql,get_db_conn());
$_data=mysql_fetch_object($result);
mysql_free_result($result);

if(strlen($_data->deli_info)>0) {
	$tempdeli_info=explode("=",$_data->deli_info);
	$deliinfook=$tempdeli_info[0];
	$deliinfotype=$tempdeli_info[1];
	if($deliinfotype=="TEXT") {
		$deliinfotext1=$tempdeli_info[2];
		$deliinfotext2=$tempdeli_info[3];
	} else if($deliinfotype=="HTML") {
		$deliinfohtml=$tempdeli_info[2];
	}
} else {
	$deliinfook="N";
	$deliinfotype="TEXT";
}

if(strlen($deliinfotype)==0) $deliinfotype="TEXT";

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	if(document.form1.deliinfook[0].checked==true) {
		if(confirm("배송/교환/환불정보 노출을 사용함으로 설정하시겠습니까?")) {
			document.form1.mode.value="update";
			document.form1.submit();
		}
	} else if(document.form1.deliinfook[1].checked==true) {
		if(confirm("배송/교환/환불정보 노출을 사용안함으로 설정하시겠습니까?")) {
			document.form1.mode.value="update";
			document.form1.submit();
		}
	}
}
function ChangeType(type){
	if (type=="TEXT") {
		document.form1.deliinfotext1.disabled=false;
		document.form1.deliinfotext2.disabled=false;
		document.form1.deliinfoimage.disabled=true;
		document.form1.deliinfohtml.disabled=true;
	} else if(type=="IMAGE") {
		document.form1.deliinfotext1.disabled=true;
		document.form1.deliinfotext2.disabled=true;
		document.form1.deliinfoimage.disabled=false;
		document.form1.deliinfohtml.disabled=true;
	} else if(type=="HTML") {
		document.form1.deliinfotext1.disabled=true;
		document.form1.deliinfotext2.disabled=true;
		document.form1.deliinfoimage.disabled=true;
		document.form1.deliinfohtml.disabled=false;
	}
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
			<? include ("menu_product.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상품관리 &gt;카테고리/상품관리 &gt; <span class="2depth_select">교환/배송/환불정보 노출</span></td>
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
					<TD><IMG SRC="images/product_exposure_title.gif"></TD>
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
					<TD width="100%" class="notice_blue">배송/교환/환불정보 관련된 내용을 상품상세화면 하단에 공통적으로 노출할 수 있도록 설정하실 수 있습니다.</TD>
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
					<TD><IMG SRC="images/product_exposure_stitle01.gif"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
			<input type=hidden name=mode>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td bgcolor="#ededed" style="padding:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td>
						<TABLE cellSpacing=0 cellPadding="5" width="100%" border=0>
						<TR>
							<TD class="table_cell" style="padding-right:14;"><img src="images/product_exposure_img.gif" border="0"></TD>
							<TD width="100%" class="td_con1" height="90">
							<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td class="font_orange"><input type=radio id="idx_deliinfook1" name=deliinfook value="Y"<?=($deliinfook=="Y"?" checked":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfook1><b>교환/배송/환불정보 노출함</b></label>&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_deliinfook2" name=deliinfook value="N"<?=($deliinfook!="Y"?" checked":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfook2><b>교환/배송/환불정보 노출안함</b></label></td>
							</tr>
							<tr>
								<td height=15></td>
							</tr>
							<tr>
								<td style="letter-spacing:-0.5pt;">&nbsp;&nbsp;교환/배송/환불정보 노출은 등록된 모든 상품에 적용됩니다.<br>
								&nbsp;&nbsp;상품 개별마다 노출안함은 "상품기타설정 > 배송/교환/환불정보 노출안함" 에서 설정 가능합니다.</td>
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
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/product_exposure_stitle02.gif" ></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" colspan="2"><input type=radio id="idx_deliinfotype1" name="deliinfotype" value="TEXT" onclick="ChangeType('TEXT')" <?=($deliinfotype=="TEXT"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfotype1><span class="font_orange">텍스트로 노출정보 입력</span></label> (상품 배송/교환/환불 항목별로 텍스트 입력이 가능합니다.)</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan="2">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD width="139" bgcolor="white" style="padding-left:16pt;"><img src="images/icon_point2.gif" width="8" height="11" border="0">배송정보</TD>
						<TD class="td_con1">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><textarea name="deliinfotext1" rows="4" style="width:100%;" cols="20" disabled class="textarea"><?=$deliinfotext1?></textarea></td>
						</tr>
						</table>
						</TD>
					</TR>
					<TR>
						<TD colspan="2" background="images/table_con_line.gif"></TD>
					</TR>
					<TR>
						<TD width="139" bgcolor="white" style="padding-left:16pt;"><img src="images/icon_point2.gif" width="8" height="11" border="0">교환/환불정보</TD>
						<TD class="td_con1" >
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><textarea name="deliinfotext2" rows="4" style="width:100%;" cols="20" disabled class="textarea"><?=$deliinfotext2?></textarea></td>
						</tr>
						</table>
						</TD>
					</TR>
					</TABLE>
					</TD>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<tr>
					<TD colspan="2" class="table_cell"><input type=radio id="idx_deliinfotype2" name="deliinfotype" value="IMAGE" onclick="ChangeType('IMAGE')"<?=($deliinfotype=="IMAGE"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfotype2><span class="font_orange">이미지로 노출정보 등록</span></label> (상품 배송/교환/환불정보를 이미지를 이용하여 노출하실 수 있습니다.)</TD>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>										
				<tr>
					<TD colspan="2">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD width="139" bgcolor="white" style="padding-left:16pt;"><img src="images/icon_point2.gif" width="8" height="11" border="0">노출정보 이미지 선택</TD>
						<TD class="td_con1">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><input type=file name="deliinfoimage" style="width:400" disabled class="input"> <font color="#FF6600">(150KB 미만, GIF/JPG파일)</font>
<?
							if ($deliinfotype=="IMAGE") {
								if(file_exists($imagepath.$filename)==true) {
									$width=getimagesize($imagepath.$filename);
									if($width[0]>=585) $width=" width=585 ";
								}
								echo "<br><img width=0 height=10><br><img src=\"".$imagepath.$filename."\" ".$width.">\n";
							}
?>
							</td>
						</tr>
						</table>
						</TD>
					</TR>
					</TABLE>
					</TD>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>										
				<TR>
					<TD colspan="2" class="table_cell"><input type=radio id="idx_deliinfotype3" name="deliinfotype" value="HTML" onclick="ChangeType('HTML')"<?=($deliinfotype=="HTML"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfotype3><span class="font_orange">HTML로 노출정보 입력</span></label> (상품 배송/교환/환불정보를 html을 이용하여 입력하실 수 있습니다.)</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>													
				<TR>
					<TD colspan="2">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD width="139" bgcolor="white" style="padding-left:16pt;"><img src="images/icon_point2.gif" width="8" height="11" border="0">배송/교환/환불정보</TD>
						<TD class="td_con1">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td><textarea name="deliinfohtml" rows="15" STYLE="width:100%" cols="20" disabled class="textarea"><?=$deliinfohtml?></textarea></td>
						</tr>
						</table>
						</TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<tr><td height=10></td></tr>
				<tr>
					<td colspan="2" align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
				</tr>
				</form>
				<tr><td height=20></td></tr>
				<tr>
					<td colspan="2">
					<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
					<TR>
						<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 HEIGHT=45></TD>
						<TD><IMG SRC="images/manual_title.gif" WIDTH=113 HEIGHT=45></TD>
						<TD width="100%" background="images/manual_bg.gif"></TD>
						<TD background="images/manual_bg.gif"></TD>
						<TD><IMG SRC="images/manual_top2.gif" WIDTH=18 HEIGHT=45></TD>
					</TR>
					<TR>
						<TD background="images/manual_left1.gif"></TD>
						<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"  class="menual_bg">
						<table cellpadding="0" cellspacing="0" width="100%">
						<col width=20></col>
						<col width=></col>
						<tr>
							<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
							<td>[배송/교환/환불정보 노출함]으로 선택을 하여도, 각각의 상품상세정보 입력시 개별적으로 노출안함으로 설정할 수 있습니다.</td>
						</tr>
						<tr>
							<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
							<td>배송/교환/환불 정보는 상품상세페이지 상세설명 아래 출력되며, [텍스트/이미지/HTML] 선택하여 입력이 가능합니다.</td>
						</tr>
						</table>
						</TD>
						<TD background="images/manual_right1.gif"></TD>
					</TR>
					<TR>
						<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8></TD>
						<TD COLSPAN=3 background="images/manual_down.gif"></TD>
						<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8></TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				<tr>
					<td height="50" colspan="2"></td>
				</tr>
				</table>

				</td>
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
<script>ChangeType("<?=$deliinfotype?>");</script>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>