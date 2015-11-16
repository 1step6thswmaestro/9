<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "me-2";
$MenuCode = "member";
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
$sort=$_POST["sort"];

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
$id=$_POST["id"];
$sort=$_POST["sort"];
$group_code=$_POST["group_code"];
$search=$_POST["search"];

if($type=="delete") {
	$sql = "UPDATE tblmember SET group_code = '' WHERE id='".$id."' ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert('해당 등급에서 회원을 삭제하였습니다.');</script>";
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}
function GoGroupCode(){
	document.form1.type.value="";
	document.form1.submit();
}

function CheckSearch() {
	if (document.form1.search.value.length<2) {
		alert('특정회원 검색어는 2자 이상 입력하야 합니다. ');
		document.form1.search.focus();
		return;
	} else {
		document.form1.type.value="";
		document.form1.block.value="";
		document.form1.gotopage.value="";
		document.form1.submit();
	}
}

function CheckAll(){
	chkval=document.reserveform.allcheck.checked;
	cnt=document.reserveform.tot.value;
	for(i=1;i<=cnt;i++){
		document.reserveform.chkid[i].checked=chkval;
	}
}

function GroupMemberDelete(id) {
	if(!confirm('선택하신 회원을 해당 등급에서 삭제하시겠습니까?')) return;
	document.form1.id.value=id;
	document.form1.type.value="delete";
	document.form1.submit();
}

function ReserveInfo(id) {
	window.open("about:blank","reserve_info","height=400,width=400,scrollbars=yes");
	document.form2.id.value=id;
	document.form2.submit();
}

function OrderInfo(id) {
	window.open("about:blank","orderinfo","width=414,height=320,scrollbars=yes");
	document.orderform.target="orderinfo";
	document.orderform.id.value=id;
	document.orderform.submit();
}

function MemberMail(mail){
	document.mailform.rmail.value=mail;
	document.mailform.submit();
}

function reservein(){
	temp =document.reserveform.tot.value;
	allreserve="";
	for(i=1;i<=temp;i++){
		if(document.reserveform.chkid[i].checked==true) allreserve+="'"+document.reserveform.chkid[i].value+"',";
	}
	if(allreserve.length==0){
		alert('적립금을 적립할 회원을 선택하세요');
		if(temp!=0) document.reserveform.chkid[1].focus();
		return;
	}
	window.open("about:blank","reserve_set","width=245,height=140,scrollbars=no");
	document.reserveform.target="reserve_set";
	document.reserveform.allid.value=allreserve;
	document.reserveform.type.value="inreserve";
	document.reserveform.submit();
}

function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
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
			<? include ("menu_member.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 회원관리 &gt; 회원등급설정 &gt; <span class="2depth_select">등급별 회원 관리</span></td>
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
					<TD><IMG SRC="images/member_groupmem_title.gif"ALT=""></TD>
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
					<TD width="100%" class="notice_blue">등급별 등록된 회원정보를 조회/관리가 가능합니다.</TD>
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
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" bgcolor="#ededed" style="padding:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
						<input type=hidden name=type>
						<input type=hidden name=id>
						<input type=hidden name=sort>
						<input type=hidden name=block>
						<input type=hidden name=gotopage>
						<TR>
							<TD height="35" align=center background="images/blueline_bg.gif"><b><font color="#555555">회원검색하기</font></b></TD>
						</TR>
						<TR>
							<TD>
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<col width=138></col>
							<col width=></col>
							<TR>
								<TD colspan="2" background="images/table_con_line.gif"></TD>
							</TR>
							<TR>
								<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">회원등급 선택</TD>
								<TD class="td_con1"><select name=group_code onchange="GoGroupCode();" style="width:350" class="select">
									<option value="">해당 등급을 선택하세요.
<? 
			$sql = "SELECT * FROM tblmembergroup ";
			$result = mysql_query($sql,get_db_conn());
			$count = 0;
			while ($row=mysql_fetch_object($result)) {
				$grouptitle[$row->group_code]=$row->group_name;
				echo "<option value=\"".$row->group_code."\"";
				if ($group_code==$row->group_code) {
					$group_description=$row->group_description;
					echo " selected";
				}
				echo ">".$row->group_name."</option>\n";
			}
			mysql_free_result($result);
?>
									</select></TD>
							</TR>
							<TR>
								<TD colspan="2" background="images/table_con_line.gif"></TD>
							</TR>
							<tr>
								<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">회원등급 설명</TD>
								<TD class="td_con1">&nbsp;<?=$group_description?></TD>
							</tr>
							<TR>
								<TD colspan="2" background="images/table_con_line.gif"></TD>
							</TR>
							<TR>
								<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">특정회원 검색</TD>
								<TD class="td_con1"><input type=text name=search value="<?=$search?>" class="input" size="28"> <a href="javascript:CheckSearch();"><img src="images/btn_search3.gif" width="77" height="25" border="0" align=absmiddle></a>&nbsp;<span class="font_orange">*특정회원의 이름 또는 아이디를 입력하세요!</span></TD>
							</TR>
							</TABLE>
							</TD>
						</TR>
						</form>
						</TABLE>
						</td>
					</tr>
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
					<TD><IMG SRC="images/member_groupmem_stitle1.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<form name=reserveform action="reserve_money.php" method=post>
			<input type=hidden name=type>
			<input type=hidden name=allid>
			<tr>
				<td>
<?
		if (strlen($group_code)>0) {
			$sql = "SELECT COUNT(*) as t_count FROM tblmember ";
			$sql.= "WHERE group_code = '".$group_code."' ";
			if(strlen($search)!=0) $sql.= "AND (name LIKE '%".$search."%' OR id LIKE '%".$search."%')";
			$result = mysql_query($sql,get_db_conn());
			$row = mysql_fetch_object($result);
			$t_count = $row->t_count;
			mysql_free_result($result);
			$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
		}
?>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" colspan="9" height=1></TD>
				</TR>
				<input type=hidden name=chkid>
				<TR align=center>
					<TD class="table_cell"><input type=checkbox name=allcheck onclick="CheckAll()" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"></TD>
					<TD class="table_cell1">아이디</TD>
					<TD class="table_cell1">성명</TD>
					<TD class="table_cell1">성별</TD>
					<TD class="table_cell1">나이</TD>
					<TD class="table_cell1">구매내역</TD>
					<TD class="table_cell1">적립금</TD>
					<TD class="table_cell1">이메일</TD>
					<TD class="table_cell1">삭제</TD>
				</TR>
				<TR>
					<TD colspan="9" background="images/table_con_line.gif"></TD>
				</TR>
<?
		if (strlen($group_code)>0) {
			$sql = "SELECT id,email,reserve,name,MID(resno,1,2) as age,MID(resno,7,1) as sex, gender FROM tblmember ";
			$sql.= "WHERE group_code = '".$group_code."' ";
			if(strlen($search)>0) $sql.= "AND (name LIKE '%".$search."%' OR id LIKE '%".$search."%') ";
			if($sort=="reserve") $sql.= "ORDER BY reserve DESC ";
			else if($sort=="id") $sql.= "ORDER BY id ";
			else if($sort=="name") $sql.= "ORDER BY name DESC ";
			else $sql.= "ORDER BY date DESC ";
			$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
			$result = mysql_query($sql,get_db_conn());
			$cnt=0;
			$lineage=100+date("y");
			while($row=mysql_fetch_object($result)) {
				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
				$cnt++;
				
				switch($row->gender){
					case "1";
						$_gender = "남자";
					break;
					case "2";
						$_gender = "여자";
					break;
					default:
						$_gender = "-";
					break;
				}
				if($row->sex>2) $row->age+=99;
				echo "<tr>\n";
				echo "	<TD align=center class=\"td_con2\"><input type=checkbox name=chkid value=\"".$row->id."\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\"></td>\n";
				echo "	<TD align=center class=\"td_con1\"><span class=\"font_orange\"><b>".$row->id."</b></span></TD>\n";
				echo "	<TD align=center class=\"td_con1\"><NOBR>".$row->name."</td>\n";
				//echo "	<TD align=center class=\"td_con1\">";($row->sex%2==0?"여자":"남자")."</td>\n";
				echo "	<TD align=center class=\"td_con1\">";
				echo $_gender;
				echo "</td>\n";
				echo "	<TD align=center class=\"td_con1\">".(strlen($row->age)==0?"&nbsp;":$lineage-$row->age)."</td>\n";
				echo "	<TD align=center class=\"td_con1\"><a href=\"javascript:OrderInfo('".$row->id."');\"><img src=\"images/icon_expenditure.gif\" width=\"55\" height=\"15\" border=\"0\"></a></td>\n";
				echo "	<TD align=right class=\"td_con1\"><b><span class=\"font_orange\">".number_format($row->reserve)."원</span></b> &nbsp;<a href=\"javascript:ReserveInfo('".$row->id."');\"><img src=\"images/icon_expenditure.gif\" width=\"55\" height=\"15\" border=\"0\" vspace=\"1\"></a></td>\n";
				echo "	<TD align=center class=\"td_con1\"><a href=\"javascript:MemberMail('".$row->email."');\"><img src=\"images/icon_mail.gif\" width=\"55\" height=\"15\" border=\"0\"></a></td>\n";
				echo "	<TD align=center class=\"td_con1\"><a href=\"javascript:GroupMemberDelete('".$row->id."');\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></a></td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<TD colspan=\"9\" background=\"images/table_con_line.gif\"></TD>\n";
				echo "</tr>\n";
			}
			mysql_free_result($result);

			if ($cnt==0) {
				echo "<tr><td  class=\"td_con2\" colspan=9 align=center>등급내 검색된 회원이 없습니다.</td></tr>";
			}
		} else {
			echo "<tr><td  class=\"td_con2\" colspan=9 align=center>등급내 검색된 회원이 없습니다.</td></tr>";
		}
?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="9" height=1></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td><a href="javascript:reservein();"><img src="images/btn_point.gif" width="76" height="18" border="0" vspace="5"></a></td>
			</tr>
			<tr>
				<td>
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
			echo "	<td width=\"100%\" class=\"font_size\" align=\"center\">\n";
			echo "		".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
			echo "	</td>\n";
			echo "</tr>\n";
		
?>
				</table>
				</td>
			</tr>
			<input type=hidden name=tot value="<?=$cnt?>">
			</form>
			<form name=form2 action="member_reservelist.php" method=post target=reserve_info>
			<input type=hidden name=id>
			<input type=hidden name=type>
			</form>

			<form name=orderform action="orderinfopop.php" method=post>
			<input type=hidden name=id>
			</form>

			<form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
			</form>
			<tr>
				<td height="30"></td>
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
					<TD COLSPAN=3 width="100%" valign="top" class=menual_bg style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><p><span class="font_dotline">등급별 회원정보</span></p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 회원등급 선택시 해당 등급 회원들의 기본정보 및 구매내역, 적립금내역, 이메일 등을 확인할 수 있습니다.</p></td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><p><span class="font_dotline">등급회원 메일발송</span></p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 메일발송 선택시 선택한 회원에게만 개별발송됩니다.<br>
						<b>&nbsp;&nbsp;</b>등급별 또는 전체회원에게 메일을 발송할 경우에는 <a href="javascript:parent.topframe.GoMenu(3,'member_mailallsend.php');"><span class="font_blue">회원관리 > 회원관리 부가기능 > 단체메일 발송</span></a> 에서 발송가능합니다.</p></td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><p><span class="font_dotline">등급회원 삭제</span></p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- 등급별 검색회원 목록에서의 삭제는 회원탈퇴가 아닌 해당 회원의 등급만 삭제됩니다.</p></td>
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