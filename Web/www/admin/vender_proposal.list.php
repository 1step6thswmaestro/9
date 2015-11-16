<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "vd-1";
$MenuCode = "vender";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$colspan=9;


$search=$_POST["search"];


if(strlen($search)>0) {
	$qry= "
			WHERE
				comp_site LIKE '%".$search."%'
				OR
				mng_name LIKE '%".$search."%'
				OR
				company LIKE '%".$search."%'
	";
}

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

$t_count=0;
$sql = "SELECT COUNT(*) as t_count FROM tblVenderProposal ".$qry." ";
$result = mysql_query($sql,get_db_conn());
$row = mysql_fetch_object($result);
$t_count = $row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;





// 문의 타입 삭제
if( strlen ( $_GET['delid'] ) > 0 AND $_GET['mode'] == "proposaldelete" ) {
	$proposaldeletesql = "DELETE FROM `tblVenderProposalType` WHERE `idx`=".$_GET['delid']." LIMIT 1 ; ";
	mysql_query($proposaldeletesql,get_db_conn());
	header("Location: vender_proposal.list.php");
}

// 문의 타입 입력
if( strlen ( $_POST['typeName'] ) > 0 AND $_POST['mode'] == "proposalInsert" ) {
	$proposalInsertsql = "INSERT `tblVenderProposalType` SET `name`='".$_POST['typeName']."'";
	mysql_query($proposalInsertsql,get_db_conn());
	header("Location: vender_proposal.list.php");
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function SearchVender() {
	document.sForm.submit();
}

function GoPage(block,gotopage) {
	document.pageForm.block.value=block;
	document.pageForm.gotopage.value=gotopage;
	document.pageForm.submit();
}

function popProposal( idx ) {
	window.open( "vender_proposal.view.php?"+idx, "popProposalB", "location=no,menubar=no,titlebar=no,toolbar=no,resizable=no,scrollbars=yes,width=650,height=650" );
}




// 제휴 입점 문의 타입
// 삭제
function proposalTypeDel ( idx ) {
	if( confirm("정말 삭제 하시 겠습니까?") ) {
		location.href='vender_proposal.list.php?delid='+idx+'&mode=proposaldelete';
	}
}
// 등록
function proposalTypeInsert ( f ) {
	if( f.typeName.value.length == 0 ) {
		alert('문의 타입명을 입력해 주세요!');
		f.typeName.focus();
		return;
	}
	f.method = "POST";
	f.submit();
}


</script>

<style>
	.add_question ul { list-style:none; margin:0px; padding:0px; }
	.add_question li { float:left; padding-right:30px; }
</style>

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
			<? include ("menu_vender.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 입점업체 관리 &gt; <span class="2depth_select">제휴 및 입점문의</span></td>
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
					<TD><IMG SRC="images/vender_proposal_title.gif" ALT="제휴 및 입점문의"></TD>
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
					<TD width="100%" class="notice_blue"><p>제휴 및 입점문의 정보를 수정/삭제 하실 수 있습니다.</p></TD>
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
				<td>
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td  bgcolor="#ededed" style="padding:4pt;">
								<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
									<tr>
										<td width="100%">


											<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
												<TR><TD background="images/table_con_line.gif"></TD></TR>
												<TR>
													<TD height="35" background="images/blueline_bg.gif"><p align="center"><b><font color="#333333">문의 타입 관리</font></b></TD>
												</TR>
												<TR>
													<TD>
														<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
															<col width=138></col>
															<col width=></col>
															<col width=108></col>
															<col width=206></col>
															<TR><TD colspan="4" background="images/table_con_line.gif"></TD></TR>
															<TR>
																<TD class="table_cell" style="padding-top:10pt;"><img src="images/icon_point2.gif" width="8" height="11" border="0">문의 타입 목록</TD>
																<TD class="td_con1">
																	<div class="add_question">
																		<ul>
																			<?
																				$sql = "SELECT * FROM `tblVenderProposalType` ";
																				$result=mysql_query($sql,get_db_conn());
																				while($row=mysql_fetch_object($result)) {
																					echo "<li>".$row->name." <img src='images/icon_del1.gif' onclick=\"proposalTypeDel(".$row->idx.");\" style='cursor:pointer; position:relative; top:0.2em;'></li>";
																				}
																			?>
																		<ul>
																	</div>
																</TD>
															</TR>
															<TR><TD colspan="4" background="images/table_con_line.gif"></TD></TR>
															<TR>
																<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">문의 타입 추가</TD>
																<TD class="td_con1">
																	<table border="0" cellpadding="0" cellspacing="0">
																		<form name="proposalTypeInsertFrom">
																		<TR>
																			<TD>
																				<input type="text" name="typeName" size="40" class="input">
																				<img src="images/btn_add1.gif" border="0" style="cursor:hand" onClick="proposalTypeInsert(proposalTypeInsertFrom)" align="absmiddle" alt="등록">
																			</TD>
																		</TR>
																		<input type="hidden" name="mode" value="proposalInsert">
																		</form>
																	</table>
																</TD>
															</TR>
														</table>
													</td>
												</tr>
											</TABLE>


										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>


			<tr>
				<td height="20"></td>
			</tr>
			<form name="sForm" method="post">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td  bgcolor="#ededed" style="padding:4pt;">
								<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
									<tr>
										<td width="100%">


											<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
												<TR>
													<TD background="images/table_con_line.gif"></TD>
												</TR>
												<TR>
													<TD height="35" background="images/blueline_bg.gif"><p align="center"><b><font color="#333333">제휴 및 입점문의 검색</font></b></TD>
												</TR>
												<TR>
													<TD >
													<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
													<TR>
														<TD background="images/table_con_line.gif"></TD>
													</TR>
													<TR>
														<TD class="td_con1" style="padding-top:10pt;" align="center">
															<input type=text name=search value="<?=$search?>" class="input">
															<img src=images/btn_inquery03.gif border=0 style="cursor:hand" onClick="SearchVender()" align="absmiddle">
														</TD>
													</TR>
													</TABLE>
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
			<tr>
				<td height="10"></td>
			</tr>
			</form>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_management_stitle1.gif" ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="5"></td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing="0" cellPadding="0" border="0" style="table-layout:fixed">
				<col width="50"></col>
				<col width="80"></col>
				<col width=""></col>
				<col width="100"></col>
				<col width="120"></col>
				<col width="120"></col>
				<col width="150"></col>
				<col width="100"></col>
				<col width="100"></col>
				<TR>
					<TD background="images/table_top_line.gif" colspan="<?=$colspan?>" height="1"></TD>
				</TR>
				<TR>
					<TD class="table_cell" align="center">번호</TD>
					<TD class="table_cell1" align="center">타입</TD>
					<TD class="table_cell1" align="center">회사명</TD>
					<TD class="table_cell1" align="center">담당자명</TD>
					<TD class="table_cell1" align="center">연락처</TD>
					<TD class="table_cell1" align="center">휴대폰</TD>
					<TD class="table_cell1" align="center">이메일</TD>
					<TD class="table_cell1" align="center">등록일</TD>
					<TD class="table_cell1" align="center">비고</TD>
				</TR>
				<TR>
					<TD colspan="<?=$colspan?>" align=center background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
				</TR>
<?

		if($t_count>0) {
			$sql = "SELECT * FROM `tblVenderProposal` ".$qry."";
			$sql.= " ORDER BY `idx` DESC ";
			$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
			$result=mysql_query($sql,get_db_conn());
			$i=0;
			while($row=mysql_fetch_object($result)) {
				$chkAdmin = ( $row->chk_date > 0 ) ? "":"#ff9900";
				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
				echo "<tr bgcolor=#FFFFFF onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='#FFFFFF'\">\n";
				echo "	<td class=\"td_con2\" align=center>&nbsp;".$number."&nbsp;</td>\n";
				echo "	<td class=\"td_con1\" align=center>&nbsp;".$row->type."&nbsp;</td>\n";
				echo "	<td class=\"td_con1\" align=center>&nbsp;".$row->company."&nbsp;</td>\n";
				echo "	<td class=\"td_con1\" align=center>&nbsp;".$row->mng_name ."&nbsp;</td>\n";
				echo "	<td class=\"td_con1\" align=center>&nbsp;".$row->mng_tell."&nbsp;</td>\n";
				echo "	<td class=\"td_con1\" align=center>&nbsp;".$row->mng_phone."&nbsp;</td>\n";
				echo "	<td class=\"td_con1\" align=center>&nbsp;".$row->mng_mail."&nbsp;</td>\n";
				echo "	<td class=\"td_con1\" align=center>&nbsp;".$row->reg_date."&nbsp;</td>\n";
				echo "	<td class=\"td_con1\" align=center bgcolor='".$chkAdmin."'>&nbsp;<button onclick=\"popProposal(".$row->idx.");\">상세보기</button>&nbsp;</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<TD colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></TD>\n";
				echo "</tr>\n";
				$i++;
			}
			mysql_free_result($result);
		} else {
			echo "<tr><td class=td_con2 colspan=".$colspan." align=center>검색된 정보가 존재하지 않습니다.</td></tr>";
		}
?>

				<TR>
					<TD background="images/table_top_line.gif" colspan="<?=$colspan?>"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="center">
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
		echo "<tr>\n";
		echo "	<td width=\"100%\" class=\"font_size\"><p align=\"center\">\n";
		echo "		".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
		echo "	</td>\n";
		echo "</tr>\n";
?>
				</table>
				</td>
			</tr>
			<form name=form2 method=post>
			<input type=hidden name=vender>
			</form>

			<form name="form3" method="post">
			<input type=hidden name='vender'>
			<input type=hidden name='disabled' value='<?=$disabled?>'>
			<input type=hidden name='s_check' value='<?=$s_check?>'>
			<input type=hidden name='search' value='<?=$search?>'>
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
			</form>

			<form name="pageForm" method="post">
			<input type=hidden name='disabled' value='<?=$disabled?>'>
			<input type=hidden name='s_check' value='<?=$s_check?>'>
			<input type=hidden name='search' value='<?=$search?>'>
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
			</form>

			<form name=etcform method=post action="<?=$_SERVER[PHP_SELF]?>">
			<input type=hidden name=mode>
			<input type=hidden name=vender>
			<input type=hidden name=disabled>
			</form>

			<tr>
				<td height=20></td>
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
					<TD COLSPAN=3 width="100%" valign="top" class="menual_bg" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">제휴 및 입점문의</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 제휴 및 입점문의 리스트와 기본적인 정보사항을 확인할 수 있습니다.</p></td>
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

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>