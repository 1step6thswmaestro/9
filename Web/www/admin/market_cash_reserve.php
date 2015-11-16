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

$field = isset($_POST['field']) ? $_POST['field'] : $_GET['field'];
$search = isset($_POST['search']) ? $_POST['search'] : $_GET['search'];
if(!$field) $field = 'name';
if($search) {
	$where = " && INSTR({$field},'{$search}') ";
}
	
if($_POST['type']=='mod') {
	$uid = $_POST['uid'];
	if($uid) {
		$memo = $_POST['memo_'.$uid];
		$status = $_POST['status_'.$uid];

		$sql = "UPDATE tblcrinfo SET memo='{$memo}', status='{$status}' WHERE uid='{$uid}'";
		mysql_query($sql,get_db_conn());
	}
	else $onload="<script>alert('정보가 제대로 넘어오지 못했습니다.');</script>";
}

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

////////////////////////

?>

<? INCLUDE "header.php"; ?>
<script>try {parent.topframe.ChangeMenuImg(7);}catch(e){}</script>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">
function GoPage(block,gotopage) {
	document.form1.type.value = "";
	document.form1.uid.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function cgData(vls) {
	if(!vls) return;
	document.form1.uid.value = vls;
	document.form1.type.value = 'mod';
	document.form1.submit();
}

function schecked(){
	if (frm.search.value == ''){
		alert('검색어를 입력해주세요.');
		frm.search.focus();
	} 
	else {
		frm.submit();
	}
}

function search_default(){
	frm.field.value = "name";
	frm.search.value = "";
	frm.submit();
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 마케팅지원 &gt; <span class="2depth_select">적립금 현금전환리스트</span></td>
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
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=uid>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=field value="<?=$field?>">
			<input type=hidden name=search value="<?=$search?>">

			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_affiliate_title_r.gif" border="0"></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height=21></TD>
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
					<TD width="100%" class="notice_blue">적립금 현금전환 처리상태를 관리하실 수 있습니다.</TD>
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
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_Affiliate_stitle1_r.gif" border="0"></TD>
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
				<col width=30></col>
				<col width=105></col>
				<col width=></col>
				<col width=105></col>
				<col width=105></col>		
				<col width=60></col>		
				<TR>
					<TD colspan=6 background="images/table_top_line.gif"></TD>
				</TR>
				<TR align=center>
					<TD class="table_cell">No</TD>
					<TD class="table_cell1">신청일자</TD>
					<TD class="table_cell1">계좌정보</TD>
					<TD class="table_cell1">신청금액</TD>
					<TD class="table_cell1">처리상태</TD>
					<TD class="table_cell1">수정</TD>
				</TR>
				<TR>
					<TD colspan="6" background="images/table_con_line.gif"></TD>
				</TR>
<?
				$status_arr = array('A'=>'전환신청','B'=>'접수완료','C'=>'입금완료');
				$sql = "SELECT COUNT(*) as t_count FROM tblcrinfo where uid>0 {$where}";
				$result = mysql_query($sql,get_db_conn());
				$row = mysql_fetch_object($result);
				mysql_free_result($result);
				$t_count = $row->t_count;
				$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

				$sql = "SELECT * FROM tblcrinfo WHERE uid>0 {$where} ";
				$sql.= "ORDER BY uid DESC LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
				$result = mysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=mysql_fetch_array($result)) {
					$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
					$cnt++;

					$date= date("Y-m-d H:i",$row['signdate']);
					$bank = "{$row['bank_name']} : {$row['bank_num']} (예금주 : {$row['name']})";
					$price = number_format($row['price']);
					$status =$row['status'];
					$memo = stripslashes($row['memo']);

					echo "<TR align=center>\n";
					echo "	<TD class=\"td_con2\" rowspan=3>".$number."</TD>\n";
					echo "	<TD class=\"td_con1\">{$date}</TD>\n";
					echo "	<TD class=\"td_con1\">{$bank}</TD>\n";
					echo "	<TD class=\"td_con1\"><b>{$price}원</b></TD>\n";
					echo "	<TD class=\"td_con1\">
								<select name='status_{$row['uid']}'>";
				foreach ($status_arr as $k => $v) {
					if($status == $k) $sec = "selected";
					else $sec = "";
					echo " <option value='{$k}' {$sec}>{$v}</option>";
				}
					echo	"	</select>
							</TD>\n";		
					echo "	<TD class=\"td_con1\"  rowspan=3><a href='#' onclick='cgData({$row['uid']})'>수정</a></TD>\n";		
					echo "</TR>\n";
					echo "<TR>\n";
					echo "	<TD colspan=\"4\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</TR>\n";
					echo "<TR>\n";
					echo "	<TD class=\"td_con1\" colspan=\"4\" style='padding:4px;'>
								<input type='text' name='memo_{$row['uid']}' value='{$memo}' size=104  class=\"input\" />
							</TD>\n";
					echo "</TR>\n";
					echo "<TR>\n";
					echo "	<TD colspan=\"6\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</TR>\n";
				}
				mysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td class=td_con2 colspan=6 align=center>신청내역이 없습니다.</td></tr>";
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
			<tr>
				<td height="40"></td>
			</tr>	
			</form>
			<tr><td align="center">
					<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
					<input type=hidden name=block value="<?=$block?>">
					<input type=hidden name=gotopage value="<?=$gotopage?>">
					</form>

						<TABLE border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td class="main_sfont_non">
							<table cellpadding="10" cellspacing="0" bgcolor="#DBDBDB" width="100%">
							<form method=get name=frm action=<?=$PHP_SELF?>>
							<input type="hidden" name="board" value="<?=$board?>">
							<tr>
								<td bgcolor="#FFFFFF" align="center">
								<SELECT name="field" class="select">
								<OPTION value="name">예금주</OPTION>			
								<OPTION value="bank_num">계좌번호</OPTION>			
								</SELECT>
								<script>document.frm.field.value = '<?=$field;?>';</script>
								<INPUT class="input" size="30" name="search" value="<?=$search?>"> <a href="javascript:schecked();"><img src="images/icon_search.gif" alt="검색" align="absMiddle" border="0"></a><A href="javascript:search_default();"><IMG src="images/icon_search_clear.gif" align="absMiddle" border="0" hspace="2"></A></td>
							</tr>
							</FORM>
							</table>
							</td>
						</tr>
						</table>
			</td></tr>
			<tr>
				<td height="40"></td>
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

<?=$onload?>

<? INCLUDE "copyright.php"; ?>