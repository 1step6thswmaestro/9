<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-1";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 20;

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

$imagepath=$Dir.DataDir."shopimages/etc/";

$type=$_POST["type"];
$date=$_POST["date"];
$old_image=$_POST["old_image"];
$up_subject=$_POST["up_subject"];
$up_content=$_POST["up_content"];
$up_image=$_FILES["up_image"];
$up_image_align=$_POST["up_image_align"];
$up_newdate=$_POST["up_newdate"];
$vdate = date("YmdHis");

if(strlen($up_subject)>0 && $type=="insert") {
	if (strlen($up_image["name"])>0 && (strtolower(substr($up_image["name"],strlen($up_image["name"])-3,3))=="gif" || strtolower(substr($up_image["name"],strlen($up_image["name"])-3,3))=="jpg") ) {
		if ($up_image["size"]<=153600) {
			$up_image["name"] = "cinfo".$up_image["name"];
			move_uploaded_file($up_image[tmp_name],$imagepath.$up_image["name"]);
			chmod($imagepath.$up_image["name"],0606);
		} else {
			$up_image["name"] = "";
		}
	}  else {
		$up_image["name"] = "";
	}
	$sql = "INSERT tblcontentinfo SET ";
	$sql.= "date		= '".$vdate."', ";
	$sql.= "subject		= '".$up_subject."', ";
	$sql.= "image_name	= '".$up_image["name"]."', ";
	$sql.= "image_align	= '".$up_image_align."', ";
	$sql.= "access		= 0, ";
	$sql.= "content		= '".$up_content."' ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert('정보(information) 등록이 완료되었습니다.');</script>\n";
} else if (strlen($date)>0 && $type=="modify") {
	if ($mode=="result") {
		if (strlen($up_image["name"])>0 && (strtolower(substr($up_image["name"],strlen($up_image["name"])-3,3))=="gif" || strtolower(substr($up_image["name"],strlen($up_image["name"])-3,3))=="jpg") ) {
			if ($up_image["size"]<=153600) {
				$up_image["name"] = "cinfo".$up_image["name"];
				if(strlen($old_image)>0 && file_exists($imagepath.$old_image)) unlink($imagepath.$old_image);
				move_uploaded_file($up_image[tmp_name],$imagepath.$up_image["name"]);
				chmod($imagepath.$up_image["name"],0606);
			} else {
				$up_image["name"] = $old_image;
			}
		} else {
			$up_image["name"] = $old_image;
		}
		$sql = "UPDATE tblcontentinfo SET ";
		$sql.= "image_name	= '".$up_image["name"]."', ";
		$sql.= "image_align	= '".$up_image_align."', ";
		$sql.= "subject		= '".$up_subject."', ";
		$sql.= "content		= '".$up_content."' ";
		if($up_newdate=="Y") $sql.= ", date = '".$vdate."' ";
		$sql.= "WHERE date = '".$date."' ";
		mysql_query($sql,get_db_conn());
		$onload="<script>alert('정보(information) 수정이 완료되었습니다.');</script>\n";
		unset($type);
		unset($mode);
		unset($date);
	} else {
		$sql = "SELECT * FROM tblcontentinfo WHERE date = '".$date."' ";
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		mysql_free_result($result);
		if ($row) {
			$subject = ereg_replace("\"","&quot;",$row->subject);
			$content = ereg_replace("\"","&quot;",$row->content);
			$image_name = $row->image_name;
			$image_align = $row->image_align;
		} else {
			$onload="<script>alert('수정하려는 정보(information)가 존재하지 않습니다.');<script>";
			unset($type);
			unset($date);
		}
	}
} else if (strlen($date)>0 && $type=="delete") {
	$sql = "SELECT * FROM tblcontentinfo WHERE date = '".$date."' ";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	mysql_free_result($result);
	if(strlen($row->image_name)>0) {
		if(file_exists($imagepath.$row->image_name)) unlink($imagepath.$row->image_name);
	}
	$sql = "DELETE FROM tblcontentinfo WHERE date = '".$date."' ";
	mysql_query($sql,get_db_conn());
	$onload="<script> alert('정보(information) 삭제가 완료되었습니다.');</script>\n";
	unset($type);
	unset($date);
} else if (strlen($date)>0 && $type=="imgdel") {
	$sql = "SELECT * FROM tblcontentinfo WHERE date = '".$date."' ";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	mysql_free_result($result);
	if(strlen($row->image_name)>0) {
		if(file_exists($imagepath.$row->image_name)) unlink($imagepath.$row->image_name);
		mysql_query("UPDATE tblcontentinfo SET image_name=NULL,image_align=NULL WHERE date='".$date."'",get_db_conn());
	}
	$onload="<script> alert('이미지 삭제가 완료되었습니다.');</script>\n";
	unset($type);
	unset($date);
}

if (strlen($type)==0) $type="insert";
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(document.form1.up_subject.value.length==0) {
		document.form1.up_subject.focus();
		alert("정보(information) 제목을 입력하세요");
		return;
	}
	if(document.form1.up_content.value.length==0) {
		document.form1.up_content.focus();
		alert("정보(information) 내용을 입력하세요");
		return;
	}
	if(type=="modify") {
		if(!confirm("해당 정보(information)를 수정하시겠습니까?")) {
			return;
		}
		document.form1.mode.value="result";
	}
	document.form1.type.value=type;
	document.form1.submit();
}
function ContentSend(type,date) {
	if(type=="delete") {
		if(!confirm("해당 정보(information)를 삭제하시겠습니까?")) return;
	}
	if(type=="imgdel") {
		if(!confirm("해당 정보(information)의 이미지를 삭제하시겠습니까?")) return;
	}
	document.form1.type.value=type;
	document.form1.date.value=date;
	document.form1.submit();
}
function GoPage(block,gotopage) {
	document.form2.block.value = block;
	document.form2.gotopage.value = gotopage;
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 마케팅지원 &gt; 마케팅지원 &gt; <span class="2depth_select">정보(information) 관리</span></td>
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
			<input type=hidden name=mode>
			<input type=hidden name=date value="<?=$date?>">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_contentinfo_title.gif" border="0"></TD>
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
					<TD width="100%" class="notice_blue">정보(information)를 등록/수정/삭제 하실 수 있습니다.</TD>
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
					<TD><IMG SRC="images/market_contentinfo_stitle1.gif" border="0"></TD>
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
				<col width=50></col>
				<col width=60></col>
				<col width=60></col>
				<TR>
					<TD colspan=5 background="images/table_top_line.gif"></TD>
				</TR>
				<TR align=center>
					<TD class="table_cell">등록일자</TD>
					<TD class="table_cell1">제목</TD>
					<TD class="table_cell1">조회</TD>
					<TD class="table_cell1">수정</TD>
					<TD class="table_cell1">삭제</TD>
				</TR>
				<TR>
					<TD colspan="5" background="images/table_con_line.gif"></TD>
				</TR>
<?
				$colspan=5;
				$sql = "SELECT COUNT(*) as t_count FROM tblcontentinfo ";
				$result = mysql_query($sql,get_db_conn());
				$row = mysql_fetch_object($result);
				$t_count = $row->t_count;
				mysql_free_result($result);
				$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

				$sql = "SELECT * FROM tblcontentinfo ORDER BY date DESC ";
				$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
				$result = mysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=mysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
					$str_date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)." ".substr($row->date,8,2).":".substr($row->date,10,2).":".substr($row->date,12,2);
					echo "<TR align=center>\n";
					echo "	<TD class=\"td_con2\">".$str_date."</TD>\n";
					echo "	<TD class=\"td_con1\" align=left>".$row->subject."</TD>\n";
					echo "	<TD class=\"td_con1\">".$row->access."</TD>\n";
					echo "	<TD class=\"td_con1\"><a href=\"javascript:ContentSend('modify','".$row->date."');\"><img src=\"images/btn_edit.gif\" width=\"50\" height=\"22\" border=\"0\"></a></TD>\n";
					echo "	<TD class=\"td_con1\"><a href=\"javascript:ContentSend('delete','".$row->date."');\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></a></TD>\n";
					echo "</TR>\n";
					echo "<TR>\n";
					echo	"	<TD colspan=".$colspan." background=\"images/table_con_line.gif\"></TD>\n";
					echo "</TR>\n";
					$cnt++;
				}
				mysql_free_result($result);

				if ($cnt==0) {
					echo "<tr><td class=td_con2 colspan=".$colspan." align=center>검색된 정보가 존재하지 않습니다.</td></tr>";
				}
?>
				<TR>
					<TD colspan=5 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align=center class="font_size">
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
							$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><IMG src=\"images/icon_first.gif\" border=0 align=\"absmiddle\"></a>&nbsp;&nbsp;";

							$prev_page_exists = true;
						}

						$a_prev_page = "";
						if ($nowblock > 0) {
							$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\">[prev]</a>&nbsp;&nbsp;";

							$a_prev_page = $a_first_block.$a_prev_page;
						}

						// 일반 블럭에서의 페이지 표시부분-시작

						if (intval($total_block) <> intval($nowblock)) {
							$print_page = "";
							for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
								if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
									$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
								} else {
									$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
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
									$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
								}
							}
						}		// 마지막 블럭에서의 표시부분-끝


						$a_last_block = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
							$last_gotopage = ceil($t_count/$setup[list_num]);

							$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><IMG src=\"images/icon_last.gif\" border=0 align=\"absmiddle\" width=\"17\" height=\"14\"></a>";

							$next_page_exists = true;
						}

						// 다음 10개 처리부분...

						$a_next_page = "";
						if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
							$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

							$a_next_page = $a_next_page.$a_last_block;
						}
					} else {
						$print_page = "<B>[1]</B>";
					}
?>
					<?=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page?>
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
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_contentinfo_stitle2.gif" border="0"></TD>
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
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">글제목</TD>
					<TD class="td_con1"><INPUT style="WIDTH: 100%" name=up_subject class="input" value="<?=$subject?>"></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">내용</TD>
					<TD class="td_con1"><TEXTAREA style="WIDTH: 100%; HEIGHT: 200px" name=up_content class="textarea"><? echo $content ?></TEXTAREA></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">이미지</TD>
					<TD class="td_con1">
					<SELECT name=up_image_align class="select">
					<option value="left" <? if ($image_align=="left") echo "selected" ?>>왼쪽정렬
					<option value="right" <? if ($image_align=="right") echo "selected" ?>>오른쪽정렬
					<option value="top" <? if ($image_align=="top") echo "selected" ?>>위로정렬
					<option value="bottom" <? if ($image_align=="bottom") echo "selected" ?>>아래로정렬
					</SELECT>
					<INPUT style="WIDTH: 65%" type=file name=up_image class="input">
					<?if(strlen($image_name)>0){?>
					<a href="javascript:ContentSend('imgdel','<?=$date?>');"><img src="images/myicon_upload_del.gif" border="0"></a><input type=hidden name=old_image value="<?=$image_name?>">
					<?}?><br><span class="font_orange" style="font-size:11px;letter-spacing:-0.5pt;">* 이미지는 150KB 이하의 GIF, JPG만 가능</span>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
<?
				if (strlen($image_name)>0) {
					if (file_exists($imagepath.$image_name)==true) {
						$width = getimagesize($imagepath.$image_name);
						if ($width[0]>=450) $width=" width=450 ";
					}
?>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">등록된 이미지</td>
					<TD class="td_con1">
					<img src="<?=$imagepath.$image_name?>" <?=$width?>>
					</td>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<?}?>
				<?if($type=="modify"){?>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">등록일 변경여부</TD>
					<TD class="td_con1"><INPUT id=idx_newdate type=checkbox CHECKED value=Y name=up_newdate>해당 공지사항 등록일을 현재시간으로 변경합니다. (최근 공지로 변경)</LABEL></TD>
				</TR>
				<?}?>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm('<?=$type?>');"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
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
						<td><span class="font_dotline">정보(information)관리</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 정보(information) 메뉴는 쇼핑몰 정보 또는 커뮤니티 기사를 제공하는 메뉴 입니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 정보(information)는 메인화면 템플릿에서 메인 우측에 기본으로 출력되게 설정돼 있습니다.<br>
						<b>&nbsp;&nbsp;</b><a href="javascript:parent.topframe.GoMenu(2,'design_main.php');"><span class="font_blue">디자인관리 > 템플릿-메인 및 카테고리 > 메인화면 템플릿</span></a></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 정보(information) 신규등록 또는 수정시 "등록일 변경여부"를 선택한 글은 정보(information) 출력시 최상단에 위치합니다.</td>
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
			<input type=hidden name=type>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
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