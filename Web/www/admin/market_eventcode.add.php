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

$mode=$_POST["mode"];
$code=$_POST["code"];
$title_type=$_POST["title_type"];
$title_body=stripslashes($_POST["title_body"]);
$child_all=$_POST["child_all"];
$imgtype=$_POST["imgtype"];

if(strlen($code)!=12) {
	$code=""; $title_type=""; $title_body=""; $child_all="";
	$disabled="disabled";
} else {
	$codeA=substr($code,0,3);
	$codeB=substr($code,3,3);
	$codeC=substr($code,6,3);
	$codeD=substr($code,9,3);
	$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' ";
	$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);
	if($row) {
		$type=$row->type;
		if(strlen($mode)==0) {
			$title_type=$row->title_type;
			$title_body=$row->title_body;
		}
	} else {
		$type=""; $title_type=""; $title_body=""; $child_all="";
		$disabled="disabled";
		$code="";
		$onload="<script>alert(\"카테고리 선택이 잘못 되었습니다.\");</script>";
	}
}

if($mode=="delete" && strlen($code)==12) {
	$likecode=$code;
	if($child_all=="1" && !ereg("X",$type)) {
		$likecode=$codeA;
		if($codeB!="000") $likecode.=$codeB;
		if($codeC!="000") $likecode.=$codeC;
	}

	$imagename = "CODE".$likecode."*";
	proc_matchfiledel($imagepath.$imagename);

	$sql = "UPDATE tblproductcode SET title_type=NULL,title_body=NULL ";
	$sql.= "WHERE codeA='".substr($likecode,0,3)."' ";
	if(strlen(substr($likecode,3,3))==3) $sql.= "AND codeB='".substr($likecode,3,3)."' ";
	if(strlen(substr($likecode,6,3))==3) $sql.= "AND codeC='".substr($likecode,6,3)."' ";
	if(strlen(substr($likecode,9,3))==3) $sql.= "AND codeD='".substr($likecode,9,3)."' ";
	$update = mysql_query($sql,get_db_conn());
	$onload="<script>alert(\"카테고리별 이벤트가 삭제되었습니다.\");</script>";
	$title_type=""; $title_body="";
} else if($mode=="modify" && strlen($code)==12) {
	$likecode=$code;
	if($child_all=="1" && !ereg("X",$type)) {
		$likecode=$codeA;
		if($codeB!="000") $likecode.=$codeB;
		if($codeC!="000") $likecode.=$codeC;
	}
	
	if($title_type!="image" || strlen($imgtype)==0 || ($title_type=="image" && strlen($_FILES["upfileimage"][name])>0)) {
		$imagename = "CODE".$likecode."*";
		proc_matchfiledel($imagepath.$imagename);
		unset($imagename);
	}
	if($title_type=="image") {
		if(strlen($imgtype)==0 || ($title_type=="image" && strlen($_FILES["upfileimage"][name])>0)) {
			$upfile=$_FILES["upfileimage"];

			if($upfile[size] < 153600) {
				if (strlen($upfile[name])>0 && file_exists($upfile[tmp_name])) {
					if($child_all=="1" && !ereg("X",$type)) {
						$sql = "SELECT CONCAT(codeA,codeB,codeC,codeD) as code FROM tblproductcode ";
						$sql.= "WHERE codeA='".substr($likecode,0,3)."' ";
						if(strlen(substr($likecode,3,3))==3) $sql.= "AND codeB='".substr($likecode,3,3)."' ";
						if(strlen(substr($likecode,6,3))==3) $sql.= "AND codeC='".substr($likecode,6,3)."' ";
						if(strlen(substr($likecode,9,3))==3) $sql.= "AND codeD='".substr($likecode,9,3)."' ";
						$result=mysql_query($sql,get_db_conn());
						unset($arrcode);
						while($row=mysql_fetch_object($result)) {
							if($code!=$row->code) {
								$arrcode[]=$row->code;
							}
						}
						mysql_free_result($result);
					}
					$ext = strtolower(substr($upfile[name],strlen($upfile[name])-3,3));
					if($ext=="gif" || $ext=="jpg"){
						$imagenameorg="CODE".$code.".gif";
						move_uploaded_file($upfile[tmp_name],$imagepath.$imagenameorg);
						chmod($imagepath.$imagenameorg,0666);
						for($i=0;$i<count($arrcode);$i++) {
							$imagename="CODE".$arrcode[$i].".gif";
							copy($imagepath.$imagenameorg, $imagepath.$imagename);
						}
					} else {
						$onload="<script>alert(\"이미지 등록은 gif, jpg 파일만 등록 가능합니다.\\n\\n확인 후 다시 등록하시기 바랍니다.\");</script>";
					}
				} else {
					$onload="<script>alert(\"이미지 선택이 안되었거나 잘못된 이미지 파일입니다.\\n\\n파일 확인 후 다시 등록하시기 바랍니다.\");</script>";
				}
			} else {
				$onload="<script>alert(\"이미지 등록은 최대 150KB 까지 등록이 가능합니다.\\n\\n이미지 용량을 줄여서 다시 등록하시기 바랍니다.\");</script>";
			}
		}
		if(strlen($onload)==0) {
			$sql = "UPDATE tblproductcode SET title_type='".$title_type."',title_body=NULL ";
			$sql.= "WHERE codeA='".substr($likecode,0,3)."' ";
			if(strlen(substr($likecode,3,3))==3) $sql.= "AND codeB='".substr($likecode,3,3)."' ";
			if(strlen(substr($likecode,6,3))==3) $sql.= "AND codeC='".substr($likecode,6,3)."' ";
			if(strlen(substr($likecode,9,3))==3) $sql.= "AND codeD='".substr($likecode,9,3)."' ";
			$update = mysql_query($sql,get_db_conn());
			$onload="<script>alert(\"카테고리별 상단/이벤트 수정이 완료되었습니다.\");</script>";
			$title_body="";
		}
	} else if($title_type=="html") {
		$likecode=$code;
		if($child_all=="1" && !ereg("X",$type)) {
			$likecode=$codeA;
			if($codeB!="000") $likecode.=$codeB;
			if($codeC!="000") $likecode.=$codeC;
		}

		$imagename = "CODE".$likecode."*";
		proc_matchfiledel($imagepath.$imagename);
		unset($imagename);

		$sql = "UPDATE tblproductcode SET title_type='".$title_type."',title_body='".addslashes($title_body)."' ";
		$sql.= "WHERE codeA='".substr($likecode,0,3)."' ";
		if(strlen(substr($likecode,3,3))==3) $sql.= "AND codeB='".substr($likecode,3,3)."' ";
		if(strlen(substr($likecode,6,3))==3) $sql.= "AND codeC='".substr($likecode,6,3)."' ";
		if(strlen(substr($likecode,9,3))==3) $sql.= "AND codeD='".substr($likecode,9,3)."' ";
		$update = mysql_query($sql,get_db_conn());
		$onload="<script>alert(\"카테고리별 상단/이벤트 수정이 완료되었습니다.\");</script>";
	} else {
		$likecode=$code;
		if($child_all=="1" && !ereg("X",$type)) {
			$likecode=$codeA;
			if($codeB!="000") $likecode.=$codeB;
			if($codeC!="000") $likecode.=$codeC;
		}

		$imagename = "CODE".$likecode."*";
		proc_matchfiledel($imagepath.$imagename);
		unset($imagename);

		$sql = "UPDATE tblproductcode SET title_type=NULL,title_body=NULL ";
		$sql.= "WHERE codeA='".substr($likecode,0,3)."' ";
		if(strlen(substr($likecode,3,3))==3) $sql.= "AND codeB='".substr($likecode,3,3)."' ";
		if(strlen(substr($likecode,6,3))==3) $sql.= "AND codeC='".substr($likecode,6,3)."' ";
		if(strlen(substr($likecode,9,3))==3) $sql.= "AND codeD='".substr($likecode,9,3)."' ";
		$update = mysql_query($sql,get_db_conn());
		$onload="<script>alert(\"카테고리별 상단/이벤트 수정이 완료되었습니다.\");</script>";
		$title_type=""; $title_body="";
	}
}

if($title_type=="image") {
	$imgtype="update"; // 이미지 수정모드
} else {
	$imgtype="";
}
?>

<? INCLUDE "header.php"; ?>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>
<SCRIPT LANGUAGE="JavaScript">
<!--
var shop="layer1";
var ArrLayer = new Array ("layer1","layer2","layer3");
function ViewLayer(gbn){
	if(document.all){
		for(i=0;i<ArrLayer.length;i++) {
			if (ArrLayer[i] == gbn)
				document.all[ArrLayer[i]].style.display="";
			else
				document.all[ArrLayer[i]].style.display="none";
		}
	} else if(document.getElementById){
		for(i=0;i<ArrLayer.length;i++) {
			if (ArrLayer[i] == gbn)
				document.getElementByld[ArrLayer[i]].style.display="";
			else
				document.getElementByld[ArrLayer[i]].style.display="none";
		}
	} else if(document.layers){
		for(i=0;i<ArrLayer.length;i++) {
			if (ArrLayer[i] == gbn)
				document.layers[ArrLayer[i]].display="";
			else
				document.layers[ArrLayer[i]].display="none";
		}
	}
	shop=gbn;
	parent_resizeIframe('ListFrame');
}

function Save() {
	if(document.form1.code.value.length!=12) {
		alert("상품카테고리를 선택하세요.");
		return;
	}
	if(document.form1.title_type[1].checked==true && document.form1.imgtype.value.length==0) {
		if(document.form1.upfileimage.value.length==0) {
			alert("등록할 이미지를 선택하세요.");
			document.form1.upfileimage.focus();
			return;
		}
	} else if(document.form1.title_type[2].checked==true) {
		if(document.form1.title_body.value.length==0) {
			alert("편집내용을 입력하세요.");
			document.form1.title_body.focus();
			return;
		}
	}
	if(confirm("수정하시겠습니까?")) {
		document.form1.mode.value="modify";
		document.form1.submit();
	}
}

function TitleDelete() {
	msg="해당 카테고리의 상단 디자인을 삭제하시겠습니까?\n\n삭제하시더라도 상품카테고리는 삭제되지 않습니다.";
	if(typeof(document.form1.child_all)=="object") {
		if(document.form1.child_all.checked==true) {
			msg="해당 카테고리 및 모든 하위카테고리의 상단 디자인을 삭제하시겠습니까?\n\n삭제하시더라도 상품카테고리는 삭제되지 않습니다.";
		}
	}
	if(confirm(msg)) {
		document.form1.mode.value="delete";
		document.form1.submit();
	}
}
//-->
</SCRIPT>
<table cellpadding="0" cellspacing="0" width="100%">
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
<input type=hidden name=imgtype value="<?=$imgtype?>">
<input type=hidden name=mode>
<input type=hidden name=code value="<?=$code?>">
<tr>
	<td width="473">
	<table cellpadding="0" cellspacing="0" width="100%" height="100%">
	<tr>
		<td width="100%" bgcolor="white"><IMG SRC="images/product_mainlist_text4.gif" border="0"></td>
	</tr>
	<tr>
		<td  HEIGHT=3></td>
	</tr>
	<tr>
		<td width="100%" bgcolor="eeeeee" HEIGHT=2 ALT=""></td>
	</tr>
	<tr>
		<td width="100%" height="100%" valign="top" style="border-bottom-width:2px; border-bottom-color:#eeeeee; border-bottom-style:solid;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%" style="padding-bottom:2pt;">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<col width=150></col>
			<col width=></col>
			<TR>
				<TD colspan=2 background="images/table_top_line.gif"></TD>
			</TR>
			<TR>
				<TD class="table_cell" nowrap><img src="images/icon_point2.gif" width="8" height="11" border="0">편집타입 선택</TD>
				<TD class="td_con1" width="100%">
				<INPUT id=idx_title_type1 onclick="ViewLayer('layer1')" type=radio value="" <?if(strlen($title_type)==0)echo"checked";?> name=title_type <?=$disabled?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_title_type1>없음</LABEL> 
				<INPUT id=idx_title_type2 onclick="ViewLayer('layer2')" type=radio value=image <?if($title_type=="image")echo"checked";?> name=title_type <?=$disabled?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_title_type2>이미지</LABEL> 
				<INPUT id=idx_title_type3 onclick="ViewLayer('layer3')" type=radio value=html <?if($title_type=="html")echo"checked";?> name=title_type <?=$disabled?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_title_type3>HTML편집</LABEL>
				</TD>
				<TD>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>
			<?if(strlen($code)==12 && !ereg("X",$type)){?>
			<TR>
				<TD class="table_cell" nowrap><img src="images/icon_point2.gif" width="8" height="11" border="0">하위카테고리 적용</td>
				<TD class="td_con1" width="100%"><input type=checkbox id="idx_child_all" name=child_all value="1" <?=$disabled?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_child_all>하위 모든카테고리 같은 설정값으로 적용</label></td>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>
			<?}?>
			<TR>
				<TD colspan=2>
				<div id=layer1 style="margin-left:0;display:hide; display:<?=(strlen($title_type)==0?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">

				</div>
				<div id=layer2 style="margin-left:0;display:hide; display:<?=($title_type=="image"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=150></col>
				<col width=></col>
				<TR>
					<TD class="table_cell" nowrap><img src="images/icon_point2.gif" width="8" height="11" border="0">이미지 파일</TD>
					<TD class="td_con1" width="100%"><INPUT type=file size=38 name=upfileimage style="width:100%" class="input" <?=$disabled?>><br><span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">* 이미지는 150KB 이하의 GIF, JPG만 가능</span></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan=2 style="padding:5" align="center">
<?
					if(strlen($code)==12 && $title_type=="image") {
						echo "<img src=\"".$imagepath."CODE".$code.".gif\" border=0 width=100% align=absmiddle>";
					} else {
						echo "<img src=\"images/code_eventnoimg.gif\" border=0 align=absmiddle>";
					}
?>
					</TD>
				</TR>
				</TABLE>				
				</div>
				<div id=layer3 style="margin-left:0;display:hide; display:<?=($title_type=="html"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<TR>
					<TD colspan="2"><TEXTAREA style="WIDTH:100%" name=title_body rows=8 wrap=off cols="86" class="textarea" <?=$disabled?>><?=htmlspecialchars($title_body)?></TEXTAREA></TD>
				</TR>
				</TABLE>
				</div>
				</TD>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD colspan=2 background="images/table_top_line.gif"></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
		<tr>
			<td align=center style="padding-top:2pt; padding-bottom:2pt;" height="22">
<?
			if($disabled == "disabled") {
				echo "<img src=\"images/btn_edit1.gif\" width=\"113\" height=\"38\" border=\"0\" hspace=\"0\" vspace=\"4\">";
				echo "<img src=\"images/btn_del3.gif\" width=\"113\" height=\"38\" border=\"0\" hspace=\"2\" vspace=\"4\">";
			} else {
				echo "<a href=\"javascript:Save();\"><img src=\"images/btn_edit2.gif\" width=\"113\" height=\"38\" border=\"0\" hspace=\"0\" vspace=\"4\"></a>";
				echo "<a href=\"javascript:TitleDelete();\"><img src=\"images/btn_del3.gif\" width=\"113\" height=\"38\" border=\"0\" hspace=\"2\" vspace=\"4\"></a>";
			}
?>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</form>
</table>
<?=$onload?>