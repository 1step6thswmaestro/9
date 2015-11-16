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

$type=$_POST["type"];
$mode=$_POST["mode"];
$survey_code=$_POST["survey_code"];
$up_survey_content=$_POST["up_survey_content"];
$up_survey_select1=$_POST["up_survey_select1"];
$up_survey_select2=$_POST["up_survey_select2"];
$up_survey_select3=$_POST["up_survey_select3"];
$up_survey_select4=$_POST["up_survey_select4"];
$up_survey_select5=$_POST["up_survey_select5"];
$up_ip_yn=$_POST["up_ip_yn"];
$up_grant_write=$_POST["up_grant_write"];
$up_grant_comment=$_POST["up_grant_comment"];
$currentdate=date("YmdHis");

if($type=="insert" && strlen($up_survey_content)>0) {
	$grant_type = $up_grant_write.$up_grant_comment;

	$sql = "UPDATE tblsurveymain SET display = 'N' WHERE display = 'N' ";
	mysql_query($sql,get_db_conn());

	$sql = "INSERT tblsurveymain SET ";
	$sql.= "survey_code		= '".$currentdate."', ";
	$sql.= "time_start		= '".time()."', ";
	$sql.= "time_end		= '0', ";
	$sql.= "display			= 'Y', ";
	$sql.= "ip_yn			= '".$up_ip_yn."', ";
	$sql.= "grant_type		= '".$grant_type."', ";
	$sql.= "survey_content	= '".$up_survey_content."', ";
	$sql.= "survey_select1	= '".$up_survey_select1."', ";
	$sql.= "survey_select2	= '".$up_survey_select2."', ";
	$sql.= "survey_select3	= '".$up_survey_select3."', ";
	$sql.= "survey_select4	= '".$up_survey_select4."', ";
	$sql.= "survey_select5	= '".$up_survey_select5."' ";
	$insert=mysql_query($sql,get_db_conn());
	$onload="<script>alert('온라인투표 등록이 완료되었습니다.');</script>\n";
} else if ($type=="modify" && strlen($survey_code)>0) {
	if ($mode=="result") {
		$grant_type = $up_grant_write.$up_grant_comment;
		if ($up_display=="Y") {
			$sql = "UPDATE tblsurveymain SET display = 'N' WHERE display = 'N' ";
			mysql_query($sql,get_db_conn());
		}
		$sql = "UPDATE tblsurveymain SET ";
		$sql.= "display			= '".$up_display."', ";
		$sql.= "ip_yn			= '".$up_ip_yn."', ";
		$sql.= "grant_type		= '".$grant_type."', ";
		$sql.= "survey_content	= '".$up_survey_content."', ";
		$sql.= "survey_select1	= '".$up_survey_select1."', ";
		$sql.= "survey_select2	= '".$up_survey_select2."', ";
		$sql.= "survey_select3	= '".$up_survey_select3."', ";
		$sql.= "survey_select4	= '".$up_survey_select4."', ";
		$sql.= "survey_select5	= '".$up_survey_select5."' ";
		$sql.= "WHERE survey_code = '".$survey_code."' ";
		$update=mysql_query($sql,get_db_conn());
		$onload="<script>alert('온라인투표 수정이 완료되었습니다.');</script>\n";
		unset($type);
		unset($mode);
		unset($survey_code);
	} else {
		$sql = "SELECT * FROM tblsurveymain WHERE survey_code='".$survey_code."'";
		$result = mysql_query($sql,get_db_conn());
		$data = mysql_fetch_object($result);
		mysql_free_result($result);
		if (!$data) {
			$onload="<script>alert('수정하려는 투표가 존재하지 않습니다.');<script>";
			unset($type);
			unset($survey_code);
		} else {
			$grant_write=substr($data->grant_type,0,1);
			$grant_comment=substr($data->grant_type,1,1);
		}
	}
} else if ($type=="delete" && strlen($survey_code)>0) {
	$sql = "DELETE FROM tblsurveymain WHERE survey_code = '".$survey_code."' ";
	mysql_query($sql,get_db_conn());
	$sql = "DELETE FROM tblsurveyresult WHERE survey_code = '".$survey_code."' ";
	mysql_query($sql,get_db_conn());
	$onload="<script> alert('해당 온라인투표 삭제가 완료되었습니다.');</script>\n";
	unset($type);
	unset($survey_code);
}

if (strlen($type)==0) $type="insert";
if (strlen($grant_write)==0) $grant_write="Y";
if (strlen($grant_comment)==0) $grant_comment="Y";
if (strlen($data->ip_yn)==0) $data->ip_yn="N";
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(document.form1.up_survey_content.value.length==0) {
		document.form1.up_subject.focus();
		alert("투표 제목을 입력하세요");
		return;
	}
	if(type=="modify") {
		if(!confirm("해당 투표를 수정하시겠습니까?")) {
			return;
		}
		document.form1.mode.value="result";
	} else if (type=="insert") {
		if(!confirm("온라인투표를 등록하시겠습니까?")) {
			return;
		}
	}
	document.form1.type.value=type;
	document.form1.submit();
}
function SurveySend(type,code) {
	if(type=="delete") {
		if(!confirm("해당 투표를 삭제하시겠습니까?")) return;
	}
	document.form1.type.value=type;
	document.form1.survey_code.value=code;
	document.form1.submit();
}
function ViewSurvey(code) {
	var url;
	url="<?=$Dir.FrontDir?>survey.php?type=view&survey_code="+code;
	window.open (url,"survey","width=450,height=400,scrollbars=yes");
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 마케팅지원 &gt; 마케팅지원 &gt; <span class="2depth_select">온라인투표 관리</span></td>
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
			<input type=hidden name=survey_code value="<?=$survey_code?>">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_survey_title.gif" ALT=""></TD>
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
					<TD width="100%" class="notice_blue">온라인투표 관리메뉴  등록/수정/삭제 하실 수 있습니다.</TD>
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
					<TD><IMG SRC="images/market_survey_stitle1.gif" WIDTH="187" HEIGHT=31 ALT=""></TD>
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
				<col width=130></col>
				<col width=></col>
				<col width=50></col>
				<col width=60></col>
				<col width=60></col>
				<col width=60></col>
				<TR>
					<TD colspan=6 background="images/table_top_line.gif"></TD>
				</TR>
				<TR align=center>
					<TD class="table_cell">등록일자</TD>
					<TD class="table_cell1">투표제목</TD>
					<TD class="table_cell1">투표수</TD>
					<TD class="table_cell1">진행여부</TD>
					<TD class="table_cell1">수정</TD>
					<TD class="table_cell1">삭제</TD>
				</TR>
				<TR>
					<TD colspan="6" background="images/table_con_line.gif"></TD>
				</TR>
<?
				$colspan=6;
				$sql = "SELECT COUNT(*) as t_count FROM tblsurveymain ";
				$result = mysql_query($sql,get_db_conn());
				$row = mysql_fetch_object($result);
				$t_count = $row->t_count;
				mysql_free_result($result);
				$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

				$sql = "SELECT * FROM tblsurveymain ORDER BY survey_code DESC ";
				$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
				$result = mysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=mysql_fetch_object($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
					$str_date = substr($row->survey_code,0,4)."/".substr($row->survey_code,4,2)."/".substr($row->survey_code,6,2)." ".substr($row->survey_code,8,2).":".substr($row->survey_code,10,2).":".substr($row->survey_code,12,2);
					$sel_tot=$row->survey_cnt1+$row->survey_cnt2+$row->survey_cnt3+$row->survey_cnt4+$row->survey_cnt5;
					if ($row->display=="Y") $display="<span class=\"font_orange\"><b>진행중</b></span>";
					else $display="종료";
					echo "<TR>\n";
					echo "	<TD align=center class=\"td_con2\">".$str_date."</TD>\n";
					echo "	<TD class=\"td_con1\"><A HREF=\"javascript:ViewSurvey('".$row->survey_code."');\">".$row->survey_content."</A></TD>\n";
					echo "	<TD align=center class=\"td_con1\">".$sel_tot."</TD>\n";
					echo "	<TD align=center class=\"td_con1\">".$display."</TD>\n";
					echo "	<TD align=center class=\"td_con1\"><a href=\"javascript:SurveySend('modify','".$row->survey_code."');\"><img src=\"images/btn_edit.gif\" width=\"50\" height=\"22\" border=\"0\"></a></TD>\n";
					echo "	<TD align=center class=\"td_con1\"><a href=\"javascript:SurveySend('delete','".$row->survey_code."');\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></a></TD>\n";
					echo "</TR>\n";
					echo "<TR>\n";
					echo "	<TD colspan=".$colspan." background=\"images/table_con_line.gif\"></TD>\n";
					echo "</TR>\n";
					$cnt++;
				}
				mysql_free_result($result);

				if ($cnt==0) {
					echo "<tr><td class=td_con2 colspan=".$colspan." align=center>등록된 온라인투표가 존재하지 않습니다..</td></tr>";
				}
?>
				<TR>
					<TD colspan=6 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
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
			<tr><td height="30"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_survey_stitle2.gif" WIDTH="187" HEIGHT=31 ALT=""></TD>
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
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">투표제목</TD>
					<TD class="td_con1"><INPUT style="WIDTH:60%" name=up_survey_content class="input" value="<?=$data->survey_content?>"></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">보기1</TD>
					<TD class="td_con1"><INPUT style="WIDTH:40%" name=up_survey_select1 class="input" value="<?=$data->survey_select1?>"></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">보기2</TD>
					<TD class="td_con1"><INPUT style="WIDTH:40%" name=up_survey_select2 class="input" value="<?=$data->survey_select2?>"></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">보기3</TD>
					<TD class="td_con1"><INPUT style="WIDTH:40%" name=up_survey_select3 class="input" value="<?=$data->survey_select3?>"></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">보기4</TD>
					<TD class="td_con1"><INPUT style="WIDTH:40%" name=up_survey_select4 class="input" value="<?=$data->survey_select4?>"></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_con_line.gif"></TD>
				</TR>
				<tr>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">보기5</TD>
					<TD class="td_con1"><INPUT style="WIDTH:40%" name=up_survey_select5 class="input" value="<?=$data->survey_select5?>"></TD>
				</tr>
				<TR>
					<TD colspan=2 background="images/table_con_line.gif"></TD>
				</TR>
				<tr>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">IP 공개여부</TD>
					<TD class="td_con1">
					<INPUT type=radio value=Y name=up_ip_yn <? if($data->ip_yn=="Y") echo "checked" ?>>코멘트 작성자 IP 공개
					&nbsp;&nbsp;&nbsp;&nbsp;
					<INPUT type=radio value=N name=up_ip_yn <? if($data->ip_yn=="N") echo "checked" ?>>코멘트 작성자 IP 숨김
					</TD>
				</tr>
				<TR>
					<TD colspan=2 background="images/table_con_line.gif"></TD>
				</TR>
				<tr>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">투표 접근권한</TD>
					<TD class="td_con1">
					설문작성 : 
					<SELECT name=up_grant_write class="select">
					<OPTION value=Y <? if($grant_write=="Y") echo "selected"?>>누구나 가능</OPTION>
					<OPTION value=N <? if($grant_write=="N") echo "selected"?>>회원만 가능</OPTION>
					</SELECT>
					&nbsp;&nbsp;&nbsp; 코멘트작성 : 
					<SELECT name=up_grant_comment class="select">
					<OPTION value=Y <? if($grant_comment=="Y") echo "selected"?>>누구나 가능</OPTION>
					<OPTION value=N <? if($grant_comment=="N") echo "selected"?>>회원만 가능</OPTION>
					</SELECT>
					
					</TD>
				</tr>
				<TR>
					<TD colspan=2 background="images/table_con_line.gif"></TD>
				</TR>
				<?if($type=="modify"){?>
				<tr>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">투표 진행여부</TD>
					<TD class="td_con1">
					<INPUT type=radio value=Y name=up_display <? if($data->display=="Y") echo "checked" ?>>투표를 진행합니다.
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<INPUT type=radio value=N name=up_display <? if ($data->display=="N") echo "checked" ?>>투표 진행을 중단합니다.
					</TD>
				</tr>
				<?}?>
				<TR>
					<TD colspan=2 background="images/table_con_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
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
						<td><span class="font_dotline">온라인투표 관리</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 온라인 투표는 메인화면 템플릿에서 메인 우측에 기본으로 출력되게 설정돼 있습니다.<br>
						<b>&nbsp;&nbsp;</b><a href="javascript:parent.topframe.GoMenu(2,'design_main.php');"><span class="font_blue">디자인관리 > 템플릿-메인 및 카테고리 > 메인화면 템플릿</span></a></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 온라인 투표 중단은 해당 투표의 수정모드에서 투표 진행여부를 선택하면 됩니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 진행되지 않는 온라인 투표는 되도록 삭제 하세요.</td>
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