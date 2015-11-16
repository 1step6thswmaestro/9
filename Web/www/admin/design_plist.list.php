<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-2";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 9;

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

$mode=$_POST["mode"];
$type=$_POST["type"];
$code=$_POST["code"];
$sch=$_POST["sch"];
$list_type=$_POST["list_type"];
$design=$_POST["design"];
$is_design=$_POST["is_design"];

if(strlen($sch)==0) {
	if(strlen($list_type)==0) {
		$sch="AL";
	} else {
		$sch=substr($list_type,0,2);
	}
}

if($sch!="AL" && $sch!="BL") $sch="AL";

$codeA=substr($code,0,3);
$codeB=substr($code,3,3);
$codeC=substr($code,6,3);
$codeD=substr($code,9,3);

if($mode=="update" && strlen($design)>0 && strlen($code)==12 && $code!="000000000000") {
	$sql = "UPDATE tblproductcode SET list_type='".$design."' ";
	$sql.= "WHERE codeA='".$codeA."' ";
	if($is_design=="1") {
		if($codeB!="000") {
			$sql.= "AND codeB='".$codeB."' ";
			if($codeC!="000") {
				$sql.= "AND codeC='".$codeC."' ";
				if($codeD!="000") {
					$sql.= "AND codeD='".$codeD."' ";
				}
			}
		}
	} else {
		$sql.= "AND codeB='".$codeB."' AND codeD='".$codeD."' ";
	}
	mysql_query($sql,get_db_conn());
	$list_type=$design;

	$onload="<script>parent.ModifyCodeDesign('".$code."','".$design."','".$is_design."');alert(\"카테고리별 화면 템플릿 변경이 완료되었습니다.\");</script>";
}
?>

<? INCLUDE "header.php"; ?>
<style>td {line-height:18pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('MainPrdtFrame')");</script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	ischk=false;
	if(typeof(document.form1.design.length)!="undefined") {
		for(i=0;i<document.form1.design.length;i++) {
			if(document.form1.design[i].checked==true) {
				ischk=true;
				break;
			}
		}
	} else {
		if(document.form1.design.checked==true) {
			ischk=true;
		}
	}
	if(!ischk) {
		alert("디자인 템플릿을 선택하세요.");
		return;
	}
	if(confirm("카테고리별 화면 템플릿을 변경하시겠습니까?")) {
		document.form1.mode.value="update";
		document.form1.submit();
	}
}

function SkinList() {
	document.form1.mode.value="";
	document.form1.block.value="";
	document.form1.gotopage.value="";
	document.form1.submit();
}

function GoPage(block,gotopage) {
	document.form1.mode.value="";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function ChangeDesign(tmp) {
	if(typeof(document.form1["design"][tmp])=="object") {
		document.form1["design"][tmp].checked=true;
		parent.design_preview(document.form1["design"][tmp].value);
	} else {
		document.form1["design"].checked=true;
		parent.design_preview(document.form1["design"].value);
	}
}

function changeMouseOver(img) {
	 img.style.border='1 dotted #999999';
}
function changeMouseOut(img,dot) {
	 img.style.border="1 "+dot;
}

//-->
</SCRIPT>

<table cellpadding="0" cellspacing="0" width="100%">
<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=type value="<?=$type?>">
<input type=hidden name=list_type value="<?=$list_type?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<tr>
	<td>
	<TABLE WIDTH=445 BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD><IMG <?if($sch=="AL")echo"SRC=\"images/img_tapstart.gif\""; else echo"SRC=\"images/img_tapstartr.gif\"";?> WIDTH=10 HEIGHT=30 ALT=""></TD>
		<TD width="202" <?if($sch=="AL")echo"background=\"images/img_tapbg.gif\" class=\"font_white\""; else echo"background=\"images/img_tapbgr.gif\"";?>><input type=radio id="idx_sch0" name="sch" value="AL" <?if($sch=="AL")echo"checked";?> onclick="SkinList()"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sch0>일반 쇼핑몰 디자인 타입</label></TD>
		<TD><IMG <?if($sch=="AL")echo"SRC=\"images/img_tap_end.gif\""; else echo"SRC=\"images/img_tap_endr.gif\"";?> WIDTH=10 HEIGHT=30 ALT=""></TD>
		<TD><IMG <?if($sch=="BL")echo"SRC=\"images/img_tapstart.gif\""; else echo"SRC=\"images/img_tapstartr.gif\"";?> WIDTH=10 HEIGHT=30 ALT=""></TD>
		<TD width="202" <?if($sch=="BL")echo"background=\"images/img_tapbg.gif\" class=\"font_white\""; else echo"background=\"images/img_tapbgr.gif\"";?>><input type=radio id="idx_sch1" name="sch" value="BL" <?if($sch=="BL")echo"checked";?> onclick="SkinList()"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sch1>가격 고정형 디자인 타입</label></TD>
		<TD><IMG <?if($sch=="BL")echo"SRC=\"images/img_tap_end.gif\""; else echo"SRC=\"images/img_tap_endr.gif\"";?> WIDTH=10 HEIGHT=30 ALT=""></TD>
	</TR>
	</TABLE>
	</TD>
</tr>
<tr>
	<td>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" bgcolor="#ededed" style="padding:4pt;">
		<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
		<tr>
			<td width="100%">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<TR>
				<TD align=center width="100%" height="30" background="images/blueline_bg.gif"><b><font color="#555555">템플릿 선택하기</font></b></TD>
			</TR>
			<TR>
				<TD width="100%" background="images/table_con_line.gif"></TD>
			</TR>
			<TR>
				<TD width="100%" style="padding:10pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%">
					<table cellpadding="0" cellspacing="0" width="100%">
<?
	$sql = "SELECT COUNT(*) as t_count FROM tblproductdesigntype "; 
	$sql.= "WHERE code LIKE '".$sch."%' ";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	mysql_free_result($result);
	$t_count = $row->t_count;
	$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

	$sql = "SELECT code FROM tblproductdesigntype ";
	$sql.= "WHERE code LIKE '".$sch."%' ORDER BY code ASC ";
	$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
	$result = mysql_query($sql,get_db_conn());
	$i=0;
	while($row=mysql_fetch_object($result)) {
		$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
		if($i==0) echo "<tr>\n";
		if($i>0 && $i%3==0) echo "</tr>\n<tr>\n";
		if($i%3==0) {
			echo "<td width=\"246\" align=center>";
		} else {
			echo "<td width=\"246\" align=center>";
		}
		echo "<img src=\"images/product/".$row->code.".gif\" border=0 width=\"150\" height=\"160\" class=\"imgline1\" onMouseOver='changeMouseOver(this);' onMouseOut=\"changeMouseOut(this,'dotted #FFFFFF');\" style='cursor:hand;' onclick='ChangeDesign(".$i.");'>";
		echo "<br><input type=radio id=\"idx_design".$i."\" name=design value=\"".$row->code."\" ";
		if($list_type==$row->code) echo "checked";
		echo " onclick=\"parent.design_preview('".$row->code."')\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none\">";
		echo "</td>\n";
		$i++;
	}
	mysql_free_result($result);
	if($i%3!=0) {
		for($j=(3-($i%3));$j<=3;$j++)	echo "<td width=\"246\" align=center>&nbsp;</td>\n";
	}
	if($i>0) {
		echo "</tr>\n";
	}
?>
					</table>
					</td>
				</tr>
				<tr>
					<td width="100%" height="25"><hr size="1" noshade color="#EBEBEB"></td>
				</tr>
				<tr>
					<td width="100%">
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
			$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\" align=\"absmiddle\"><IMG src=\"images/icon_first.gif\" border=0 align=\"absmiddle\"></a>&nbsp;&nbsp;";

			$prev_page_exists = true;
		}

		$a_prev_page = "";
		if ($nowblock > 0) {
			$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\" align=\"absmiddle\">[prev]</a>&nbsp;&nbsp;";

			$a_prev_page = $a_first_block.$a_prev_page;
		}

		// 일반 블럭에서의 페이지 표시부분-시작

		if (intval($total_block) <> intval($nowblock)) {
			$print_page = "";
			for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
				if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
					$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
				} else {
					$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\" align=\"absmiddle\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
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
					$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\" align=\"absmiddle\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
				}
			}
		}		// 마지막 블럭에서의 표시부분-끝


		$a_last_block = "";
		if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
			$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
			$last_gotopage = ceil($t_count/$setup[list_num]);

			$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\" align=\"absmiddle\"><IMG src=\"images/icon_last.gif\" border=0 align=\"absmiddle\" width=\"17\" height=\"14\"></a>";

			$next_page_exists = true;
		}

		// 다음 10개 처리부분...

		$a_next_page = "";
		if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
			$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\" align=\"absmiddle\">[next]</a>";

			$a_next_page = $a_next_page.$a_last_block;
		}
	} else {
		$print_page = "<B>[1]</B>";
	}
	echo "<tr>\n";
	echo "	<td width=\"100%\" class=\"font_size\"><p align=\"center\">\n";
	echo "		".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
	echo "	</td>\n";
	echo "</tr>\n";

?>
					</table>
					</td>
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
	</TD>
</tr>
<tr>
	<td height=10></td>
</tr>
<?if(strlen($type)>0 && !eregi("X",$type)){?>
<tr>
	<td align=center><input type=checkbox id="idx_design0" name=is_design value="1"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_design0><span class=font_orange><B>선택된 템플릿으로 하위카테고리 적용</B></span></label></td>
</tr>
<?}?>
<tr>
	<td height=20></td>
</tr>
<tr>
	<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
</tr>
</form>
</table>
<?=$onload?>

</body>
</html>