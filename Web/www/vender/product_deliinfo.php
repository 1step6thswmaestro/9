<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");

$imagepath=$Dir.DataDir."shopimages/vender/";
$filename="aboutdeliinfo_".$_VenderInfo->getVidx().".gif";

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
		if ($up_image["size"]>100000) {
			echo "<script>alert ('이미지 용량은 100K를 넘을 수 없습니다.');location.href='".$_SERVER[PHP_SELF]."';</script>\n";
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
		$sql = "UPDATE tblvenderstore SET deli_info='".$deli_info."' ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		mysql_query($sql,get_db_conn());
		echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.reload()\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
	}
}

if(strlen($_venderdata->deli_info)>0) {
	$tempdeli_info=explode("=",$_venderdata->deli_info);
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
function formSubmit() {
	if(document.form1.deliinfook[0].checked==true) {
		if(confirm("배송/교환/환불정보 노출을 사용함으로 설정하시겠습니까?")) {
			document.form1.mode.value="update";
			document.form1.target="processFrame";
			document.form1.submit();
		}
	} else if(document.form1.deliinfook[1].checked==true) {
		if(confirm("배송/교환/환불정보 노출을 사용안함으로 설정하시겠습니까?")) {
			document.form1.mode.value="update";
			document.form1.target="processFrame";
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

<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
<col width=190></col>
<col width=20></col>
<col width=></col>
<col width=5></col>
<tr>
	<td width=190 valign=top nowrap background="images/minishop_leftbg.gif"><? include ("menu.php"); ?></td>
	<td width=20 nowrap></td>
	<td valign=top style="padding-top:20px">

	<table width="100%"  border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td><img src="images/product_deliinfo_title.gif"></td>
				</tr>
				<tr>
					<td height=5 background="images/minishop_titlebg.gif">
				</tr>
				</table>
			</td>
		</tr>
		<tr><td height=10></td></tr>
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td colspan=3 >


						<table cellpadding="10" cellspacing="1" width="100%" bgcolor="#EFEFF2">
							<tr>
								<td  bgcolor="#F5F5F9" style="padding:20px">
									<table border=0 cellpadding=0 cellspacing=0 width=100%>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">배송/교환/환불 정보를 입점사 정책에 맞게 작성합니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">배송/교환/환불 정보는 상품 상세정보 페이지 본문에 출력됩니다.</td>
										</tr>
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
			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=40></td></tr>
			<tr>
				<td>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>

				<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
				<input type=hidden name=mode>

				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 배송/교환/환불정보 노출여부 </td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=#cccccc></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=230></col>
				<col width=></col>
				<tr>
					<td align=center><img src="images/deliinfo_img.gif" border=0></td>
					<td valign=top>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr><td height=10></td></tr>
					<tr>
						<td align=center bgcolor=#F5F5F5 height=60>
						<input type=radio id="idx_deliinfook1" name=deliinfook value="Y" <?=($deliinfook=="Y"?" checked":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfook1>배송/교환/환불정보 노출함</label>
						<img width=10 height=0>
						<input type=radio id="idx_deliinfook2" name=deliinfook value="N" <?=($deliinfook!="Y"?" checked":"")?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfook2>배송/교환/환불정보 노출안함</label>
						</td>
					</tr>
					<tr><td height=20></td></tr>
					<tr>
						<td style="line-height:13pt">
						<span class="notice_blue"> &nbsp;&nbsp;[배송/교환/환불정보 노출함] 으로 선택을 하여도, 각각의 상품상세정보 입력시
						<br>&nbsp;&nbsp;개별적으로 정보 노출여부를 선택할 수 있습니다.</font>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 배송/교환/환불정보 입력</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=#cccccc></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td>
					<table border=0 cellpadding=7 cellspacing=1 width=100% bgcolor=#E7E7E7>
					<col width=140></col>
					<col width=></col>
					<tr>
						<td colspan=2 bgcolor=#F5F5F5>
						<input type=radio id="idx_deliinfotype1" name="deliinfotype" value="TEXT" onclick="ChangeType('TEXT')" <?=($deliinfotype=="TEXT"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfotype1><B>텍스트로 노출정보 입력</B></label> <span class="notice_blue">(상품 배송/교환/환불 항목별로 텍스트 입력이 가능합니다.)</font>
						</td>
					</tr>
					<tr>
						<td align=center bgcolor=#ffffff>배송정보</td>
						<td bgcolor=#ffffff>
						<textarea class=textarea name="deliinfotext1" style="width:100%;height:53" disabled><?=$deliinfotext1?></textarea>
						</td>
					</tr>
					<tr>
						<td align=center bgcolor=#ffffff>교환/환불정보</td>
						<td bgcolor=#ffffff>
						<textarea class=textarea name="deliinfotext2" style="width:100%;height:53" disabled><?=$deliinfotext2?></textarea>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=15></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=7 cellspacing=1 width=100% bgcolor=#E7E7E7>
					<col width=140></col>
					<col width=></col>
					<tr>
						<td colspan=2 bgcolor=#F5F5F5>
						<input type=radio id="idx_deliinfotype2" name="deliinfotype" value="IMAGE" onclick="ChangeType('IMAGE')" <?=($deliinfotype=="IMAGE"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfotype2><B>이미지로 노출정보 등록</B></label> <span class="notice_blue">(상품 배송/교환/환불정보를 이미지를 이용하여 노출하실 수 있습니다.)</font>
						</td>
					</tr>
					<tr>
						<td align=center bgcolor=#ffffff>노출정보 이미지 선택</td>
						<td bgcolor=#ffffff>
						<input type=file name="deliinfoimage" style="width:350" class="button" disabled> <span class="notice_blue">(100K 미만, GIF/JPG파일)</font>
<?
						if ($deliinfotype=="IMAGE") {
							if(file_exists($imagepath.$filename)==true) {
								$width=getimagesize($imagepath.$filename);
								if($width[0]>=490) $width=" width=490 ";
							}
							echo "<br><img width=0 height=10><br><img src=\"".$imagepath.$filename."\" ".$width.">\n";
						}
?>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=15></td></tr>
				<tr>
					<td>
					<table border=0 cellpadding=7 cellspacing=1 width=100% bgcolor=#E7E7E7>
					<col width=140></col>
					<col width=></col>
					<tr>
						<td colspan=2 bgcolor=#F5F5F5>
						<input type=radio id="idx_deliinfotype3" name="deliinfotype" value="HTML" onclick="ChangeType('HTML')" <?=($deliinfotype=="HTML"?" checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_deliinfotype3><B>HTML로 노출정보 입력</B></label> <span class="notice_blue">(상품 배송/교환/환불정보를 html을 이용하여 입력하실 수 있습니다.)</font>
						</td>
					</tr>
					<tr>
						<td align=center bgcolor=#ffffff>배송/교환/환불정보</td>
						<td bgcolor=#ffffff>
						<textarea class=textarea name="deliinfohtml" style="width:100%;height:200" disabled><?=$deliinfohtml?></textarea>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<tr>
					<td align=center>
					<A HREF="javascript:formSubmit()"><img src="images/btn_save01.gif" border=0></A>
					</td>
				</tr>

				</form>

				</table>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

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

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>