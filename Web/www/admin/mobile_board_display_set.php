<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

?>
<? INCLUDE "header.php"; ?>

</SCRIPT>
<style>
	form{margin:0px; padding:0px;}
	.tbl_board_list_wrap{border-bottom:2px solid #939393;border-top:2px solid #939393;text-align:center}
	.tbl_board_list_wrap td{padding: 3px 0px}
	.td_board_lsit_title {border-bottom:1px solid #939393}
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
								<? include ("menu_mobile.php"); ?>
								</td>
								<td></td>
								<td valign="top">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td height="29" colspan="3">
												<table cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 모바일샵 &gt; <span class="2depth_select">게시판 설정</span></td>
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
												<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed">
													<tr><td height="8"></td></tr>
													<tr>
														<td>
															<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																<tr>
																	<td><img src="images/mobile_display_set.gif" alt="모바일 게시판 보기 설정"></td>
																	</tr><tr>
																	<td width="100%" background="images/title_bg.gif" height=21></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr><td height="3"></td></tr>
													<tr>
														<td style="padding-bottom:3pt;">
															<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
																<tr>
																	<td><img src="images/distribute_01.gif"></td>
																	<td COLSPAN=2 background="images/distribute_02.gif"></td>
																	<td><img src="images/distribute_03.gif"></td>
																</tr>
																<tr>
																	<td background="images/distribute_04.gif"><img src="images/distribute_04.gif" ></td>
																	<td class="notice_blue"><img src="images/distribute_img.gif" ></td>
																	<td width="100%" class="notice_blue">모바일샵 게시판 목록의 보이기 설정을 하실 수 있습니다.</td>
																	<td background="images/distribute_07.gif"><img src="images/distribute_07.gif" ></td>
																</tr>
																<tr>
																	<td><img src="images/distribute_08.gif"></td>
																	<td COLSPAN=2 background="images/distribute_09.gif"></td>
																	<td><img src="images/distribute_10.gif"></td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td height="20"></td>
													</tr>
													<tr>
														<td width="100%" height="100%" valign="top" style="padding-left:5px;padding-right:5px;">
															<table cellpadding="0" cellspacing="0" width="100%">
																<col width="40"/>
																<col width=""/>
																<col width="250"/>
																<col width="80"/>
																<tr><td background="images/table_top_line.gif" colSpan="4"></td></tr>
																<tr align="center">
																	<td class="table_cell">NO</td>
																	<td class="table_cell1">게시판 이름</td>
																	<td class="table_cell1">접근권한 설정</td>
																	<td class="table_cell1">사용 설정</td>
																</tr>
																<tr><td background="images/table_con_line.gif" colSpan="4"></td></tr>
															<?
																$boardListSQL = "SELECT board_name, board, grant_view, grant_mobile FROM tblboardadmin WHERE board IN('notice','qna','storytalk','event','share') ORDER BY FIELD(board,'notice','qna','storytalk','event','share') ASC";

																if(false !== $boardListRes = mysql_query($boardListSQL,get_db_conn())){
																	$boardListRowcount = mysql_num_rows($boardListRes);
																	
																	if($boardListRowcount>0){
																			$i=1;
																		while($boardListRow = mysql_fetch_assoc($boardListRes)){
																			$section = $boardListRow['board'];
																			$boardname = $boardListRow['board_name'];
																			$grant_view = $boardListRow['grant_view'];
																			$grant_mobile = $boardListRow['grant_mobile'];
																			$print_grant="";
																			switch($grant_view){
																				case "N":
																					$print_grant = "회원+비회원 목록, 글 보기 가능";
																				break;
																				case "U":
																					$print_grant = "비회원 목록보기만 가능";
																				break;
																				case "Y":
																					$print_grant = "회원만 가능";
																				break;
																			}
															?>
																<tr>
																	<td class="td_con2" align="center"><?=$i?></td>
																	<td class="td_con1" style="padding-left:10px;"><?=$boardname?></td>
																	<td class="td_con1" align="center"><?=$print_grant?></td>
																	<td class="td_con1" align="center">
																		<?if(strlen($grant_mobile) == 0 || $grant_mobile == "N"){?>
																			<a href="javascript:mobileBoardSet('<?=$section?>','U');">미사용중</a>
																		<?}else{?>
																			<a href="javascript:mobileBoardSet('<?=$section?>','C');">사용중</a>
																		<?}?>
																	</td>
																</tr>
																<tr><td background="images/table_con_line.gif" colSpan="4"></td></tr>
															<?
																			$i++;
																		}
																	}
																}
															?>
																<tr><td background="images/table_con_line.gif" colSpan="4"></td></tr>
															</table>
														</td>
													</tr>
													<tr><td height="50"></td></tr>
													<tr>
														<td>
															<table width="100%" border="0" cellpadding="0" cellspacing="0">
																<tr>
																	<td><img src="images/manual_top1.gif" width="15" height="45" alt=""></td>
																	<td><img src="images/manual_title.gif" width="113" height="45" alt=""></td>
																	<td width="100%" background="images/manual_bg.gif"></td>
																	<td background="images/manual_bg.gif"></td>
																	<td><img src="images/manual_top2.gif" width="18" height="45" alt=""></td>
																</tr>
																<tr>
																	<td background="images/manual_left1.gif"></td>
																	<td colspan=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
																		<table cellpadding="0" cellspacing="0" width="100%">
																			<col width=20></col>
																			<col width=></col>
																			<tr>
																				<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
																				<td><span class="font_dotline">모바일 게시판 보기 설정</span></td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top">- 모바일 게시판 보이기 설정으로 게시판 목록에서 생성된 각 게시판을 보여줄지 말지를 설정할 수 있습니다.</td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top">- 게시판 접근권한은 <a href="community_list.php">게시판 리스트 관리</a>메뉴에서 가능합니다.</td>
																			</tr>
																			<tr>
																				<td align="right">&nbsp;</td>
																				<td class="space_top">- 사용설정 영역의 버튼을 클릭하면 미사용/사용 부분으로 토글되어 설정됩니다.</td>
																			</tr>
																		</table>
																	</td>
																	<td background="images/manual_right1.gif"></td>
																</tr>
																<tr>
																	<td><img src="images/manual_left2.gif" width="15" height="8" alt=""></td>
																	<td colspan=3 background="images/manual_down.gif"></td>
																	<td><img src="images/manual_right2.gif" width="18" height="8" alt=""></td>
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
<form name="mobileBoard" action="./mobile_board_display_set_proc.php" method="post">
	<input type="hidden" name="section" value=""/>
	<input type="hidden" name="mode" value=""/>
</form>
<script>
	function mobileBoardSet(sec,mode){
		var _form = document.mobileBoard;

		_form.section.value = sec;
		_form.mode.value =mode;

		var msg=""; 
		if(_form.section.value !="" && _form.mode.value !=""){
			if(sec == "C"){
				msg = "모바일 보기 설정을 해제 하시겠습니까?";
			}else{
				msg = "모바일 보기 설정을 하시겠습니까?.";
			}

			if(confirm(msg)){
				_form.submit();
				return;
			}
		}else{
			alert("정상적인 경로로 접근하지 않았습니다.");
			return;
		}

		
	}
</script>
<? INCLUDE "copyright.php"; ?>