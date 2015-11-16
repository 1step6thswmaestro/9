<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-4";
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
$addr_group=$_POST["addr_group"];
$mobile=$_POST["mobile"];

if($type=="group_delete" && strlen($addr_group)>0) {
	$sql = "DELETE FROM tblsmsaddress WHERE addr_group='".$addr_group."' ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert('삭제하였습니다.');</script>";
} else if($type=="delete" && strlen($mobile)>0) {
	$telval=substr($mobile,0,-3);
	$telval=str_replace("|=|","','",$telval);

	$sql = "DELETE FROM tblsmsaddress WHERE mobile IN ('".$telval."') ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert('삭제하였습니다.');</script>";
}

$qry = "WHERE 1=1 ";
if(strlen($addr_group)>0) $qry.= "AND addr_group='".$addr_group."' ";

$sql = "SELECT COUNT(*) as t_count FROM tblsmsaddress ".$qry;
$result = mysql_query($sql,get_db_conn());
$row = mysql_fetch_object($result);
$t_count = $row->t_count;
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
mysql_free_result($result);

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}

function AddressAdd(mobile) {
	if(mobile.length>0) {
		document.form2.type.value="update";
		document.form2.mobile.value=mobile;
	} else {
		document.form2.type.value="insert";
	}
	window.open("about:blank","addbookpop","width=350,height=3,scrollbars=no");
	document.form2.target="addbookpop";
	document.form2.action="market_smsaddbookpop.php";
	document.form2.submit();
}

function GroupDelete() {
	if(document.form1.addr_group.value.length==0) {
		alert("삭제할 그룹을 선택하세요.");
		document.form1.addr_group.focus();
		return;
	}
	if(confirm("해당 그룹에 속한 모든 번호를 삭제하시겠습니까?")) {
		document.form1.type.value="group_delete";
		document.form1.submit();
	}
}

function CheckAll(){
	chkval=document.form1.allcheck.checked;
	cnt=document.form1.tot.value;
	for(i=1;i<=cnt;i++){
		document.form1.tels_chk[i].checked=chkval;
	}
}

function check_del() {
	document.form1.mobile.value="";
	for(i=1;i<document.form1.tels_chk.length;i++) {
		if(document.form1.tels_chk[i].checked==true) {
			document.form1.mobile.value+=document.form1.tels_chk[i].value+"|=|";
		}
	}
	if(document.form1.mobile.value.length==0) {
		alert("선택하신 SMS번호가 없습니다.");
		return;
	}
	if(confirm("선택하신 SMS번호를 삭제하시겠습니까?")) {
		document.form1.type.value="delete";
		document.form1.submit();
	}
}

function SearchGroup(group) {
	document.form1.addr_group.value=group;
	document.form1.block.value="";
	document.form1.gotopage.value="";
	document.form1.mobile.value="";
	document.form1.submit();
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
			<? include ("menu_market.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 마케팅지원 &gt; SMS 발송/관리  &gt; <span class="2depth_select">SMS 주소록 관리</span></td>
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
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=mobile>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
					<TR>
						<TD><IMG SRC="images/market_smsaddressbook_title.gif" ALT=""></TD>
						</tr><tr>
<TD width="100%" background="images/title_bg.gif" height="21"></TD>
					</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
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
					<TD width="100%" class="notice_blue">휴대폰 번호로 SMS 주소록을 만들어 회원관리를 할 수 있습니다.</TD>
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
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td  height=3 style="padding-bottom:3pt;">
					그룹선택 
					<SELECT onchange="this.form.type.value='';this.form.submit();" name=addr_group class="select">
					<option value="">전체</option>
<?
					$sql = "SELECT addr_group FROM tblsmsaddress GROUP BY addr_group ";
					$result=mysql_query($sql,get_db_conn());
					while($row=mysql_fetch_object($result)) {
						echo "<option value=\"".$row->addr_group."\"";
						if($addr_group==$row->addr_group) echo " selected";
						echo ">".$row->addr_group."</option>\n";
					}
					mysql_free_result($result);
?>
					</SELECT>
					<a href="javascript:GroupDelete();"><img src="images/btn_groupdel.gif" width="89" height="18" border="0" align=absmiddle></a>
					</td>
					<td  height=3 style="padding-bottom:3pt;" align=right><img src="images/icon_8a.gif" width="13" height="13" border="0">총 등록 건수 : <B><?= $t_count ?></B>건 <img src="images/icon_8a.gif" width="13" height="13" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup[list_num])?></b></td>
				</tr>
				<tr>
					<td  colspan="2">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD background="images/table_top_line.gif" colspan="6"></TD>
					</TR>
					<TR>
						<TD class="table_cell1" align=center><INPUT onclick=CheckAll() type=checkbox name=allcheck></TD>
						<TD class="table_cell1" align="center">No</TD>
						<TD class="table_cell1" align="center">이름</TD>
						<TD class="table_cell1" align="center">그룹명</TD>
						<TD class="table_cell1" align="center">휴대폰번호</TD>
						<TD class="table_cell1" width="50%" align="center">기타메모</TD>
					</TR>
					<TR>
						<TD colspan="6" background="images/table_con_line.gif"></TD>
					</TR>
					<input type=hidden name=tels_chk>
<?
					$colspan=6;
					$sql = "SELECT * FROM tblsmsaddress ".$qry." ";
					$sql.= "ORDER BY date DESC ";
					$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
					$result = mysql_query($sql,get_db_conn());
					$cnt=0;
					while($row=mysql_fetch_object($result)) {
						$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
						echo "<TR>\n";
						echo "	<TD class=\"td_con1\" align=\"center\"><input type=checkbox name=tels_chk value=\"".$row->mobile."\"></TD>\n";
						echo "	<TD class=\"td_con1\" align=\"center\">".$number."</TD>\n";
						echo "	<TD class=\"td_con1\" align=\"center\"><A HREF=\"javascript:AddressAdd('".$row->mobile."')\"><b><span class=\"font_orange\">".$row->name."</span></a></A></TD>\n";
						echo "	<TD class=\"td_con1\" align=\"center\"><A HREF=\"javascript:SearchGroup('".$row->addr_group."')\">".$row->addr_group."</A></TD>\n";
						echo "	<TD class=\"td_con1\" align=\"center\">".$row->mobile."&nbsp;</TD>\n";
						echo "	<TD class=\"td_con1\" width=\"50%\" align=\"center\">&nbsp;".$row->memo."</TD>\n";
						echo "</TR>\n";
						echo "<TR>\n";
						echo "	<TD colspan=\"6\" background=\"images/table_con_line.gif\"></TD>\n";
						echo "</TR>\n";
						$cnt++;
					}
					mysql_free_result($result);

					if ($cnt==0) {
						echo "<tr><td class=td_con1 colspan=".$colspan." align=center>조건에 맞는 내역이 존재하지 않습니다.</td></tr>";
					}
?>
					<TR>
						<TD background="images/table_top_line.gif" colspan="6"></TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				<tr>
					<td width="803" height=3 colspan="2"></td>
				</tr>
				<tr>
					<td width="803" colspan="2">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%" class="font_size" align=left><a href="javascript:check_del();"><img src="images/btn_del2.gif" width="76" height="18" border="0"></a></td>
					</tr>
					<tr>
						<td width="100%" class="font_size" align="center">
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
				<input type=hidden name=tot value="<?=$cnt?>">
				</table>
				</td>
			</tr>
			<tr>
				<td height=6></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:AddressAdd('');"><img src="images/btn_smsupload.gif" width="156" height="38" border="0"></a></td>
			</tr>
			<tr>
				<td height="25">&nbsp;</td>
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
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">SMS 주소록 관리</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 신규그룹 생성은 [SMS 주소 신규등록 > 그룹선택 > 신규그룹]에 그룹명을 입력하시면 됩니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 이름 클릭시 해당 주소록의 정보를 변경하실 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 그룹명 클릭시 해당 그룹의 전체 주소록을 보실 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 그룹 삭제시, 해당 그룹에 속한 주소록도 같이 삭제됩니다.</td>
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
			</form>

			<form name=form2 method=post>
			<input type=hidden name=type>
			<input type=hidden name=mobile>
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