<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-1";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_POST["mode"];
$board=$_POST["board"];

include ($Dir.BoardDir."file.inc.php");

//게시판 삭제
if($mode=="delete" && strlen($board)>0) {
	if($board=="qna" OR $board=="notice" OR $board=="faq") {
		$onload="<script>alert(\"기본적으로 제공되는 게시판은 삭제하실 수 없습니다.\");</script>";
	}

	$prqnaboard=getEtcfield($_shopdata->etcfield,"PRQNA");

	$sql = "DELETE FROM tblboardadmin WHERE board='".$board."' ";
	if(mysql_query($sql,get_db_conn())) {
		mysql_query("DELETE FROm tblboard WHERE board='".$board."'",get_db_conn());
		mysql_query("DELETE FROM tblboardcomment WHERE board='".$board."'",get_db_conn());
		ProcessBoardDir($board,"delete");

		$sql = "DELETE FROM tbldesignnewpage WHERE type='board' AND filename='".$board."' ";
		mysql_query($sql,get_db_conn());

		if($prqnaboard==$board) {
			$_shopdata->etcfield=setEtcfield($_shopdata->etcfield,"PRQNA","");
		}

		$onload="<script>alert(\"해당 게시판 및 게시물을 삭제하였습니다.\");</script>";
	} else {
		$onload="<script>alert(\"게시판 삭제중 오류가 발생하였습니다.\");</script>";
	}
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}

function ModifyBasicInfo(board) {
	window.open("","basicinfo","height=600,width=620,scrollbars=yes,resizable=no");
	document.form2.mode.value="";
	document.form2.board.value=board;
	document.form2.action="community_basicinfo_pop.php";
	document.form2.target="basicinfo";
	document.form2.submit();
	document.form2.board.value="";
	document.form2.action="";
	document.form2.target="";
}

function ModifySpecialInfo(board) {
	window.open("","specialinfo","height=600,width=620,scrollbars=yes,resizable=no");
	document.form2.mode.value="";
	document.form2.board.value=board;
	document.form2.action="community_specialinfo_pop.php";
	document.form2.target="specialinfo";
	document.form2.submit();
	document.form2.board.value="";
	document.form2.action="";
	document.form2.target="";
}

function BoardDesignInfo(board) {
	window.open("","designinfo","height=260,width=470,scrollbars=no,resizable=no");
	document.form2.mode.value="";
	document.form2.board.value=board;
	document.form2.action="community_designinfo_pop.php";
	document.form2.target="designinfo";
	document.form2.submit();
	document.form2.board.value="";
	document.form2.action="";
	document.form2.target="";
}

function BoardDelete(board) {
	msg="게시판을 삭제하시겠습니까?\n\n해당 게시판의 게시물도 모두 삭제됩니다.";
<?if(strlen($prqnaboard)>0){?>
	if(board=="<?=$prqnaboard?>") {
		msg="본 게시판은 상품QNA로 사용중입니다.\n\n게시판을 삭제하시겠습니까?\n\n해당 게시판의 게시물도 모두 삭제됩니다.";
	}
<?}?>
	if(confirm(msg)) {
		document.form2.mode.value="delete";
		document.form2.board.value=board;
		document.form2.submit();
	}
}

function BoardOrder() {
	window.open("community_order_pop.php","boardorder","height=350,width=400,scrollbars=no,resizable=no");
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
			<? include ("menu_community.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 커뮤니티 &gt; 커뮤니티 관리  &gt; <span class="depth2_select">게시판 리스트 관리</span></td>
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
					<TD><IMG SRC="images/community_list_title.gif" border="0"></TD>
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
					<TD width="100%" class="notice_blue">등록된 게시판의 기능/디자인 변경 및 삭제처리를 할 수 있습니다.</TD>
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
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/community_list_stitle2.gif" WIDTH="250" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif" WIDTH=7 HEIGHT=7 ALT=""></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif" WIDTH=8 HEIGHT=7 ALT=""></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif"></TD>
					<TD width="100%" class="notice_blue">
					1) <b>기본기능</b> : 게시판의 기본기능을 설정 할 수 있습니다.
					<br>2) <b><font class=font_orange>특수기능</font></b> : 게시판의 특수한 기능을 설정  할 수 있습니다.
					<!-- <br>3) <b><font class=font_orange>게시판 디자인 관리</font></b> : 게시판의 디자인을 변경할 수 있습니다. -->
					</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif" WIDTH=7 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif" WIDTH=8 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td></td>
			</tr>
			<tr>
				<td align=right><a href="javascript:BoardOrder();"><img src="images/icon_sort.gif" width="109" height="26" border="0" vspace="3"></a></td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=30></col>
				<col width=></col>
				<col width=90></col>
				<col width=60></col>
				<col width=95></col>
				<col width=100></col>
				<col width=50></col>
				<TR>
					<TD colspan=7 background="images/table_top_line.gif"></TD>
				</TR>
				<TR align=center>
					<TD class="table_cell">No</TD>
					<TD class="table_cell1">게시판 제목</TD>
					<TD class="table_cell1">게시판 형태</TD>
					<TD class="table_cell1">비밀번호</TD>
					<TD class="table_cell1">접근권한</TD>
					<TD class="table_cell1"><b>기능설정</b></TD>
					<TD class="table_cell1">삭제</TD>
				</TR>
				<TR>
					<TD colspan="7" background="images/table_con_line.gif"></TD>
				</TR>
<?
				unset($arr_skin);
				$sql = "SELECT MID(board_skin,1,1) as skin_code, COUNT(MID(board_skin,1,1)) as skin_cnt ";
				$sql.= "FROM tblboardskin GROUP BY skin_code ";
				$result=mysql_query($sql,get_db_conn());
				while($row=mysql_fetch_object($result)) {
					$arr_skin[$row->skin_code]=$row->skin_cnt;
				}
				mysql_free_result($result);

				$colspan=7;
				$arr_write=array("N"=>"회원/비회원","Y"=>"회원전용","A"=>"관리자전용");
				$arr_view=array("N"=>"회원/비회원","U"=>"비회원(목록)","Y"=>"회원전용");

				$sql = "SELECT * FROM tblboardadmin ORDER BY date DESC ";
				$result=mysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=mysql_fetch_object($result)) {
					$cnt++;
					unset($btypename);
					if(ereg("L",$row->board_skin)) $btypename="일반형 게시판";
					if(ereg("W",$row->board_skin)) $btypename="웹진형 게시판";
					if(ereg("I",$row->board_skin)) $btypename="앨범형 게시판";
					if(ereg("B",$row->board_skin)) $btypename="블로그형 게시판";

					unset($bwrite);
					unset($bview);
					if($row->grant_write!="A" && strlen($row->group_code)>0) {
						$bwrite="등급회원";
					} else {
						$bwrite=$arr_write[$row->grant_write];
					}
					if(strlen($row->group_code)>0) {
						$bview="등급회원";
					} else {
						$bview=$arr_view[$row->grant_view];
					}

					echo "<TR>\n";
					echo "	<TD class=\"td_con2\" align=\"center\">".$cnt."</TD>\n";
					echo "	<TD class=\"td_con1\">&nbsp;".$row->board_name."&nbsp;</TD>\n";
					echo "	<TD class=\"td_con1\" align=\"center\">&nbsp;".$btypename."</TD>\n";
					echo "	<TD class=\"td_con1\" align=\"center\">&nbsp;<B>".$row->passwd."</B>&nbsp;</td>\n";
					echo "	<TD class=\"td_con1\">\n";
					echo "	<table cellpadding=\"0\" cellspacing=\"0\" width=\"102\">\n";
					echo "	<col width=31></col><col width=></col>\n";
					echo "	<tr>\n";
					echo "		<td><img src=\"images/icon_write.gif\" width=\"28\" height=\"15\" border=\"0\"></td>\n";
					echo "		<td align=center>".$bwrite."</td>\n";
					echo "	</tr>\n";
					echo "	<tr>\n";
					echo "		<td><img src=\"images/icon_read.gif\" width=\"28\" height=\"15\" border=\"0\"></td>\n";
					echo "		<td align=center>".$bview."</td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	<TD class=\"td_con1\" align=\"center\">\n";
					echo "	<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">\n";
					//echo "	<col width=33%></col><col width=33%></col><col width=33%></col>\n";
					echo "	<TR>\n";
					echo "		<TD><a href=\"javascript:ModifyBasicInfo('".$row->board."');\"><IMG SRC=\"images/icon_function1.gif\" WIDTH=\"33\" HEIGHT=\"33\" ALT=\"기본기능\" border=\"0\"></a></TD>\n";
					echo "		<TD><a href=\"javascript:ModifySpecialInfo('".$row->board."');\"><IMG SRC=\"images/icon_function2.gif\" WIDTH=\"32\" HEIGHT=\"33\" ALT=\"특수기능\" hspace=\"2\" border=\"0\"></a></TD>\n";
					if($arr_skin[substr($row->board_skin,0,1)]>1) {
						//echo "	<TD><a href=\"javascript:BoardDesignInfo('".$row->board."');\"><IMG SRC=\"images/icon_function3.gif\" WIDTH=\"39\" HEIGHT=\"33\" ALT=\"게시판 디자인 관리\" border=\"0\"></a></TD>\n";
					} else {
						//echo "	<TD><IMG SRC=\"images/icon_function3r.gif\" WIDTH=\"39\" HEIGHT=\"33\" ALT=\"게시판 디자인 관리\" border=\"0\"></a></TD>\n";
					}
					echo "	</TR>\n";
					echo "	</TABLE>\n";
					echo "	</TD>\n";
					if($row->board=="qna" OR $row->board=="notice" OR $row->board=="faq") {
						echo "<TD class=\"td_con1\" align=center><img src=\"images/btn_del1.gif\" width=\"50\" height=\"22\" border=\"0\"></TD>\n";
					} else {
						echo "<TD class=\"td_con1\" align=center><a href=\"javascript:BoardDelete('".$row->board."');\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></a></TD>\n";
					}
					echo "</TR>\n";
					echo "<TR><TD colspan=".$colspan." background=\"images/table_con_line.gif\"></TD></TR>\n";
				}
				mysql_free_result($result);
				if ($cnt==0) {
					echo "<TR><TD class=\"td_con2\" colspan=".$colspan." align=center>등록된 게시판이 존재하지 않습니다.</TD></TR>";
				}
?>
				<TR>
					<TD colspan=7 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=40></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif">&nbsp;</TD>
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
						<td><span class="font_dotline">게시판 리스트 관리</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 게시판 순서 변경시 쇼핑몰에 출력되는 게시판의 순서가 변경됩니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 상품 QNA 게시판으로 설정된 게시판은 상품상세설명 페이지의 상품QNA 게시판으로 사용되며, 한개만 지정 가능합니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">- 게시판을 삭제할 경우 해당 게시물도 모두 삭제 됩니다.</td>
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
			<form name=form2 method=post action="<?=$_SERVER[PHP_SELF]?>">
			<input type=hidden name=mode>
			<input type=hidden name=board>
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