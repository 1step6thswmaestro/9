<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "st-1";
$MenuCode = "counter";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$postip=ip2long($_GET["postip"]);
$postmsg=$_GET["postmsg"];
$delnum=$_GET["delnum"];
$mode=$_GET["mode"];

// 차단 추가
if( $mode == "ipInsert" AND strlen($postip) > 6 ) {
	echo $ipInsertSQL = "INSERT `tblConnIP_block` SET `IP` = '".$postip."', `msg` = '".$postmsg."' ; ";
	mysql_query( $ipInsertSQL, get_db_conn() );
	//header("location:/admin/counter_NotAuthIP.php");
}

// 차단 삭제
if( $mode == "ipDelete" AND strlen($delnum) > 0 ) {
	$ipInsertSQL = "DELETE FROM `tblConnIP_block` WHERE `idx` = '".$delnum."' LIMIT 1 ; ";
	mysql_query( $ipInsertSQL, get_db_conn() );
	header("location:/admin/counter_NotAuthIP.php");
}
?>


<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>

<script type="text/javascript">
<!--
	// 차단 등록
	function sendCHK( f ) {
	if( f.postip.value.length < 8 ) {
		alert('IP를 입력하세요!');
		f.postip.focus();
		return false;
	}
	f.methode="GET";
	f.submit();
}

// 차단 삭제
function deleteIP ( no ) {
	if( confirm('정말 삭제 하시겠습니까?') ) {
		location.href="/admin/counter_NotAuthIP.php?mode=ipDelete&delnum="+no;
	}
}

//-->
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
			<? include ("menu_counter.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 통계분석 &gt; 접근 관리 &gt; <span class="2depth_select">차단 IP 관리</span></td>
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
						<TR><TD><IMG SRC="images/counter_notAuthIP_title.gif"ALT="IP 차단 관리"></TD></tr>
						<tr><TD width="100%" background="images/title_bg.gif" height="21"></TD></TR>
					</TABLE>
				</td>
			</tr>
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
					<TD width="100%" class="notice_blue"><p>쇼핑몰의 유해요소(광고글 등)가 되는 접근자 IP를 차단 및 관리할 수 있습니다.</p></TD>
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
			<tr><td height="40"></td></tr>

			<tr><TD><IMG SRC="images/shop_noiplist_stitle1.gif" ALT="차단IP 목록"></TD></tr>
			<tr><td height="6"></td></tr>
			<tr>
				<td align=center>

				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<table border=0 cellpadding=0 cellspacing=0 width="100%">
							<col width="120"></col>
							<col width=""></col>
							<col width="60"></col>
							<TR><TD colspan=3 background="images/table_top_line.gif"></TD></TR>
							<TR>
								<TD class="table_cell" align="center">차단된 IP</TD>
								<TD class="table_cell1" align="center">메모</TD>
								<TD class="table_cell1" align="center">삭제</TD>
							</TR>
							<?
								$ipListSQL = "SELECT * FROM `tblConnIP_block` ORDER BY `idx` ASC ;";
								$ipListResult = mysql_query( $ipListSQL , get_db_conn() );
								while ( $ipListRow = mysql_fetch_assoc ( $ipListResult ) ) {
							?>
							<tr><td colspan=3 height=1 bgcolor=#EDEDED></td></tr>
							<TR>
								<TD class="td_con2" align=center><FONT color=#3d3d3d><?=long2ip($ipListRow[IP])?></FONT></TD>
								<TD class="td_con1"><FONT color=#3d3d3d><?=$ipListRow[msg]?></FONT></TD>
								<TD class="td_con1" align=center><a href="#" onclick="deleteIP(<?=$ipListRow[idx]?>);"><img src="images/btn_del.gif" alt="삭제" /></a></TD>
							</TR>
							<?
								}
							?>
							<TR><TD colspan=3 background="images/table_top_line.gif"></TD></TR>
						</table>
					</tr>
				</table>
				</td>
			</tr>
			<tr><td height=40></td></tr>
			<tr>
				<td>
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr><td><IMG SRC="images/shop_noiplist_stitle2.gif" ALT="차단IP 정보"></TD></tr>
						<tr><td height=3></td></tr>
						<tr>
							<td>
								<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
									<TR>
										<TD><IMG SRC="images/distribute_01.gif"></TD>
										<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
										<TD><IMG SRC="images/distribute_03.gif"></TD>
									</TR>
									<TR>
										<TD background="images/distribute_04.gif"></TD>
										<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
										<TD width="100%" class="notice_blue">
											1) 차단IP를 정확하게 입력하시기 바랍니다.<br />
											2) 유동IP의 경우 잦은 IP 변경으로 쇼핑몰 접근이 차단될 수 있사오니 이점 유의하시기 바랍니다.
										</TD>
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
						<tr><td height=3></td></tr>
						<tr>
							<td>
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<form name="ipInsertFrom">
									<TR><TD colspan=2 background="images/table_top_line.gif"></TD></TR>
									<TR>
										<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">차단할 IP Address</TD>
										<TD class="td_con1"> <input type="text" maxlength="16" name="postip" size=25 class="input"> <span class=font_orange>예)211.235.123.120</span></td>
									</TR>
									<TR>
										<TD colspan="2" background="images/table_con_line.gif"></TD>
									</TR>
									<TR>
										<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">메모</TD>
										<TD class="td_con1" ><input name="postmsg" style="width:100%;" class="input"></textarea></td>
									</tr>
									<TR><TD colspan=2 background="images/table_top_line.gif"></TD></TR>
									<tr><td height=10></td></tr>
									<tr>
										<td class="font_white" align=center colspan="2">
											<a href="#" onclick="sendCHK(ipInsertFrom);"><img src="images/btn_badd2.gif" alt="" /></a>
										</td>
									</TR>
									<input type="hidden" name="mode" value="ipInsert">
									</form>
								</table>
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
							<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 HEIGHT=45 ALT=""></TD>
							<TD><IMG SRC="images/manual_title.gif" WIDTH=113 HEIGHT=45 ALT=""></TD>
							<TD width="100%" background="images/manual_bg.gif"></TD>
							<TD background="images/manual_bg.gif"></TD>
							<TD><IMG SRC="images/manual_top2.gif" WIDTH=18 HEIGHT=45 ALT=""></TD>
						</TR>
						<TR>
							<TD background="images/manual_left1.gif"></TD>
							<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg" >
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
										<td><FONT color=#3d3d3d>쇼핑몰 접근 제한 IP 설정.</FONT></td>
									</tr>
									<tr><td colspan=2 height=5></td></tr>
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
				<tr><td height="30"></td></tr>
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

<form name=form2 method=post action="<?=$_SERVER[PHP_SELF]?>"  target=popviewprint>
<input type=hidden name=print>
<input type=hidden name=type value=<?=$type?>>
<input type=hidden name=searchdate value=<?=$searchdate?>>
</form>
</table>
<?=$onload?>
<? INCLUDE "copyright.php"; ?>
