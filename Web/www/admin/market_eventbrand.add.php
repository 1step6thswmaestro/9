<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### ÆäÀÌÁö Á¢±Ù±ÇÇÑ check ###############
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
$title_body=$_POST["title_body"];

if(strlen($code)==0) {
	$code=""; $title_type=""; $title_body=""; $child_all="";
	$disabled="disabled";
} else {
	if((int)$code>0) {
		$sql = "SELECT * FROM tblproductbrand WHERE bridx='".$code."' ";
		$result=mysql_query($sql,get_db_conn());
		$row=mysql_fetch_object($result);
		mysql_free_result($result);
		if($row) {
			if(strlen($mode)==0) {
				$title_type=$row->title_type;
				$title_body=$row->title_body;
			}
		} else {
			$title_type=""; $title_body=""; $child_all="";
			$disabled="disabled";
			$code="";
			$onload="<script>alert(\"ºê·£µå ¼±ÅÃÀÌ Àß¸ø µÇ¾ú½À´Ï´Ù.\");</script>";
		}
	} else {
		$child_all="Y";
		if(strlen($mode)==0) {
			$title_type="";
			$title_body="";
		}
	}
}

if(strlen($onload)==0 && $mode=="modify" && strlen($code)>0) {
	if($child_all=="Y") {
		$sql = "SELECT bridx as code FROM tblproductbrand ";
		$qry = "";
		if(ereg("^[¤¡-¤¾]", $code)) {
			if($code == "¤¡") $qry.= "WHERE (brandname >= '¤¡' AND brandname < '¤¤') OR (brandname >= '°¡' AND brandname < '³ª') ";
			if($code == "¤¤") $qry.= "WHERE (brandname >= '¤¤' AND brandname < '¤§') OR (brandname >= '³ª' AND brandname < '´Ù') ";
			if($code == "¤§") $qry.= "WHERE (brandname >= '¤§' AND brandname < '¤©') OR (brandname >= '´Ù' AND brandname < '¶ó') ";
			if($code == "¤©") $qry.= "WHERE (brandname >= '¤©' AND brandname < '¤±') OR (brandname >= '¶ó' AND brandname < '¸¶') ";
			if($code == "¤±") $qry.= "WHERE (brandname >= '¤±' AND brandname < '¤²') OR (brandname >= '¸¶' AND brandname < '¹Ù') ";
			if($code == "¤²") $qry.= "WHERE (brandname >= '¤²' AND brandname < '¤µ') OR (brandname >= '¹Ù' AND brandname < '»ç') ";
			if($code == "¤µ") $qry.= "WHERE (brandname >= '¤µ' AND brandname < '¤·') OR (brandname >= '»ç' AND brandname < '¾Æ') ";
			if($code == "¤·") $qry.= "WHERE (brandname >= '¤·' AND brandname < '¤¸') OR (brandname >= '¾Æ' AND brandname < 'ÀÚ') ";
			if($code == "¤¸") $qry.= "WHERE (brandname >= '¤¸' AND brandname < '¤º') OR (brandname >= 'ÀÚ' AND brandname < 'Â÷') ";
			if($code == "¤º") $qry.= "WHERE (brandname >= '¤º' AND brandname < '¤»') OR (brandname >= 'Â÷' AND brandname < 'Ä«') ";
			if($code == "¤»") $qry.= "WHERE (brandname >= '¤»' AND brandname < '¤¼') OR (brandname >= 'Ä«' AND brandname < 'Å¸') ";
			if($code == "¤¼") $qry.= "WHERE (brandname >= '¤¼' AND brandname < '¤½') OR (brandname >= 'Å¸' AND brandname < 'ÆÄ') ";
			if($code == "¤½") $qry.= "WHERE (brandname >= '¤½' AND brandname < '¤¾') OR (brandname >= 'ÆÄ' AND brandname < 'ÇÏ') ";
			if($code == "¤¾") $qry.= "WHERE (brandname >= '¤¾' AND brandname < '¤¿') OR (brandname >= 'ÇÏ' AND brandname < 'É¡') ";
		} else if($code == "±âÅ¸") {
			$qry.= "WHERE (brandname < '¤¡' OR brandname >= '¤¿') AND (brandname < '°¡' OR brandname >= 'É¡') AND (brandname < 'a' OR brandname >= '{') AND (brandname < 'A' OR brandname >= '[') ";
		} else if(ereg("^[A-Z]", $code)) {
			$qry.= "WHERE brandname LIKE '".$code."%' OR brandname LIKE '".strtolower($code)."%' ";	
		}
		
		if(strlen($qry)>0) {
			$result=mysql_query($sql.$qry,get_db_conn());
			unset($arrcode);
			while($row=mysql_fetch_object($result)) {
				$arrcode[]=$row->code;
				if($title_type!="image") {
					@unlink($imagepath."BRD".$row->code.".gif");
				}
			}
			mysql_free_result($result);
		}
	} else {
		$arrcode[]=$code;
		if($title_type!="image") {
			@unlink($imagepath."BRD".$code.".gif");
		}
	}
	
	if(count($arrcode)==0) {
		$onload="<script>alert(\"Àû¿ëÇÒ ºê·£µå°¡ Á¸ÀçÇÏÁö ¾Ê½À´Ï´Ù.\");</script>";
	} else {
		if($title_type=="image") {
			$upfile=$_FILES["upfileimage"];
			if($upfile[size] < 153600) {
				if (strlen($upfile[name])>0 && file_exists($upfile[tmp_name])) {
					$ext = strtolower(substr($upfile[name],strlen($upfile[name])-3,3));
					if($ext=="gif" || $ext=="jpg"){
						$imagenameorg="BRD".$arrcode[0].".gif";
						move_uploaded_file($upfile[tmp_name],$imagepath.$imagenameorg);
						chmod($imagepath.$imagenameorg,0666);
						for($i=1;$i<count($arrcode);$i++) {
							$imagename="BRD".$arrcode[$i].".gif";
							copy($imagepath.$imagenameorg, $imagepath.$imagename);
						}
					} else {
						$onload="<script>alert(\"ÀÌ¹ÌÁö µî·ÏÀº gif, jpg ÆÄÀÏ¸¸ µî·Ï °¡´ÉÇÕ´Ï´Ù.\\n\\nÈ®ÀÎ ÈÄ ´Ù½Ã µî·ÏÇÏ½Ã±â ¹Ù¶ø´Ï´Ù.\");</script>";
					}
				} else {
					$onload="<script>alert(\"ÀÌ¹ÌÁö ¼±ÅÃÀÌ ¾ÈµÇ¾ú°Å³ª Àß¸øµÈ ÀÌ¹ÌÁö ÆÄÀÏÀÔ´Ï´Ù.\\n\\nÆÄÀÏ È®ÀÎ ÈÄ ´Ù½Ã µî·ÏÇÏ½Ã±â ¹Ù¶ø´Ï´Ù.\");</script>";
				}
			} else {
				$onload="<script>alert(\"ÀÌ¹ÌÁö µî·ÏÀº ÃÖ´ë 150KB ±îÁö µî·ÏÀÌ °¡´ÉÇÕ´Ï´Ù.\\n\\nÀÌ¹ÌÁö ¿ë·®À» ÁÙ¿©¼­ ´Ù½Ã µî·ÏÇÏ½Ã±â ¹Ù¶ø´Ï´Ù.\");</script>";
			}
			if(strlen($onload)==0) {
				$sql = "UPDATE tblproductbrand SET title_type='".$title_type."',title_body=NULL ";
				if($child_all=="Y") {
					$sql.= $qry;
					$onload="<script>alert(\"$code ºê·£µå ÀÏ°ý »ó´Ü/ÀÌº¥Æ® ¼öÁ¤ÀÌ ¿Ï·áµÇ¾ú½À´Ï´Ù.\");</script>";
					$title_type="";
					$title_body="";
				} else {
					$sql.= "WHERE bridx='".$code."' ";
					$onload="<script>alert(\"ºê·£µåº° »ó´Ü/ÀÌº¥Æ® ¼öÁ¤ÀÌ ¿Ï·áµÇ¾ú½À´Ï´Ù.\");</script>";
				}
				$update = mysql_query($sql,get_db_conn());
			}
		} else if($title_type=="html") {
			if(strlen($onload)==0) {
				$sql = "UPDATE tblproductbrand SET title_type='".$title_type."',title_body='".$title_body."' ";
				if($child_all=="Y") {
					$sql.= $qry;
					$onload="<script>alert(\"$code ºê·£µå ÀÏ°ý »ó´Ü/ÀÌº¥Æ® ¼öÁ¤ÀÌ ¿Ï·áµÇ¾ú½À´Ï´Ù.\");</script>";
					$title_type="";
					$title_body="";
				} else {
					$sql.= "WHERE bridx='".$code."' ";
					$onload="<script>alert(\"ºê·£µåº° »ó´Ü/ÀÌº¥Æ® ¼öÁ¤ÀÌ ¿Ï·áµÇ¾ú½À´Ï´Ù.\");</script>";
				}
				$update = mysql_query($sql,get_db_conn());
			}
		} else {
			if(strlen($onload)==0) {
				$sql = "UPDATE tblproductbrand SET title_type='".$title_type."',title_body='".$title_body."' ";
				if($child_all=="Y") {
					$sql.= $qry;
					$onload="<script>alert(\"$code ºê·£µå ÀÏ°ý »ó´Ü/ÀÌº¥Æ® ¼öÁ¤ÀÌ ¿Ï·áµÇ¾ú½À´Ï´Ù.\");</script>";
				} else {
					$sql.= "WHERE bridx='".$code."' ";
					$onload="<script>alert(\"ºê·£µåº° »ó´Ü/ÀÌº¥Æ® ¼öÁ¤ÀÌ ¿Ï·áµÇ¾ú½À´Ï´Ù.\");</script>";
				}
				$update = mysql_query($sql,get_db_conn());
				$title_type="";$title_body="";
			}
		}
	}
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
	if(!document.form1.code.value) {
		alert("»óÇ° ºê·£µå¸¦ ¼±ÅÃÇÏ¼¼¿ä.");
		return;
	}
	if(document.form1.title_type[1].checked==true) {
		if(document.form1.upfileimage.value.length==0) {
			alert("µî·ÏÇÒ ÀÌ¹ÌÁö¸¦ ¼±ÅÃÇÏ¼¼¿ä.");
			document.form1.upfileimage.focus();
			return;
		}
	} else if(document.form1.title_type[2].checked==true) {
		if(document.form1.title_body.value.length==0) {
			alert("ÆíÁý³»¿ëÀ» ÀÔ·ÂÇÏ¼¼¿ä.");
			document.form1.title_body.focus();
			return;
		}
	}
	if(confirm("¼öÁ¤ÇÏ½Ã°Ú½À´Ï±î?")) {
		document.form1.mode.value="modify";
		document.form1.submit();
	}
}

function TitleDelete() {
<?
	if($child_all == "Y") {
		echo "msg=\"".$code." ºê·£µå ÀÏ°ý »ó´Ü µðÀÚÀÎÀ» »èÁ¦ÇÏ½Ã°Ú½À´Ï±î?\\n\\n»èÁ¦ÇÏ½Ã´õ¶óµµ »óÇ° ºê·£µå´Â »èÁ¦µÇÁö ¾Ê½À´Ï´Ù.\";";
	} else {
		echo "msg=\"ÇØ´ç Ä«Å×°í¸®ÀÇ »ó´Ü µðÀÚÀÎÀ» »èÁ¦ÇÏ½Ã°Ú½À´Ï±î?\\n\\n»èÁ¦ÇÏ½Ã´õ¶óµµ »óÇ° ºê·£µå´Â »èÁ¦µÇÁö ¾Ê½À´Ï´Ù.\";";
	}
?>
	if(confirm(msg)) {
		document.form1.mode.value="delete";
		document.form1.submit();
	}
}
//-->
</SCRIPT>
<table cellpadding="0" cellspacing="0" width="100%">
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
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
		<td width="100%" height="100%" valign="top" style="border-bottom-width:2px; border-bottom-color:eeeeee; border-bottom-style:solid;">
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
				<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">ÆíÁýÅ¸ÀÔ ¼±ÅÃ</TD>
				<TD class="td_con1">
				<INPUT id=idx_title_type1 onclick="ViewLayer('layer1')" type=radio value="" <?if(strlen($title_type)==0)echo"checked";?> name=title_type <?=$disabled?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_title_type1>¾øÀ½</LABEL> 
				<INPUT id=idx_title_type2 onclick="ViewLayer('layer2')" type=radio value=image <?if($title_type=="image")echo"checked";?> name=title_type <?=$disabled?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_title_type2>ÀÌ¹ÌÁö</LABEL> 
				<INPUT id=idx_title_type3 onclick="ViewLayer('layer3')" type=radio value=html <?if($title_type=="html")echo"checked";?> name=title_type <?=$disabled?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_title_type3>HTMLÆíÁý</LABEL>
				</TD>
				<TD>
			</TR>
			<TR>
				<TD colspan="2" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD colspan=2>
				<div id=layer1 style="margin-left:0;display:hide; display:<?=(strlen($title_type)==0?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">

				</div>
				<div id=layer2 style="margin-left:0;display:hide; display:<?=($title_type=="image"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=150></col>
				<col width=></col>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">ÀÌ¹ÌÁö ÆÄÀÏ</TD>
					<TD class="td_con1"><INPUT type=file size=38 name=upfileimage style="width:100%" class="input" <?=$disabled?>><br><span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">* ÀÌ¹ÌÁö´Â 150KB ÀÌÇÏÀÇ GIF, JPG¸¸ °¡´É</span></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan=2 style="padding:5" align="center">
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<tr>
						<td align="center">
<?
					if((int)$code>0 && $title_type=="image") {
						echo "<img src=\"".$imagepath."BRD".$code.".gif\" border=0 width=100% align=absmiddle>";
					} else {
						echo "<img src=\"images/code_eventnoimg.gif\" border=0 align=absmiddle>";
					}
?>
						</td>
					</tr>
					</table>
					</TD>
				</TR>
				</TABLE>				
				</div>
				<div id=layer3 style="margin-left:0;display:hide; display:<?=($title_type=="html"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<TR>
					<TD colspan="2"><TEXTAREA style="WIDTH:100%" name=title_body rows=8 wrap=off cols="86" class="textarea" <?=$disabled?>><?=$title_body?></TEXTAREA></TD>
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
			} else {
				echo "<a href=\"javascript:Save();\"><img src=\"images/btn_edit2.gif\" width=\"113\" height=\"38\" border=\"0\" hspace=\"0\" vspace=\"4\"></a>";
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