<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");
include_once($Dir."lib/admin_more.php");

####################### ������ ���ٱ��� check ###############
$PageCode = "vd-1";
$MenuCode = "vender";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_POST["mode"];
$vender=$_POST["vender"];

$disabled=$_POST["disabled"];
$s_check=$_POST["s_check"];
$search=$_POST["search"];

if($mode=="disabled" && strlen($vender)>0 && ($disabled=="0" || $disabled=="1")) {
	$sql = "UPDATE tblvenderinfo SET disabled='".$disabled."' ";
	$sql.= "WHERE vender='".$vender."' AND delflag='N' ";
	if(mysql_query($sql,get_db_conn())) {
		$log_content = "## ������ü ���λ��� ���� ## - ���� : ".$vender." , ���ο��� : ".($disabled==0?"����":"����")."";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

		echo "<html></head><body onload=\"alert('��û�Ͻ� �۾��� �����Ͽ����ϴ�.');parent.pageForm.submit();\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('��û�Ͻ� �۾��� ������ �߻��Ͽ����ϴ�.')\"></body></html>";exit;
	}
}

$qry = "WHERE delflag='N' ";
if($disabled=="Y") $qry.= "AND disabled='0' ";
else if($disabled=="N") $qry.= "AND disabled='1' ";
if(strlen($search)>0) {
	if($s_check=="id") $qry.= "AND id='".$search."' ";
	else if($s_check=="com_name") $qry.= "AND com_name LIKE '%".$search."%' ";
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
$sql = "SELECT COUNT(*) as t_count FROM tblvenderinfo ".$qry." ";
$result = mysql_query($sql,get_db_conn());
$row = mysql_fetch_object($result);
$t_count = $row->t_count;
mysql_free_result($result);
$pagecount = (($t_count - 1) / $setup[list_num]) + 1;


// ���� ���� ��ȸ jdy
$shop_more_info = getShopMoreInfo();
$account_rule = $shop_more_info['account_rule'];
// ���� ���� ��ȸ jdy

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

function VenderModify(vender) {
	document.form3.vender.value=vender;
	document.form3.action="vender_infomodify.php";
	document.form3.submit();
}

function VenderDetail(vender) {
	window.open("about:blank","venderdetail_pop","height=100,width=100,toolbar=no,menubar=no,scrollbars=yes,status=no");

	document.form2.vender.value=vender;
	document.form2.action="vender_detailpop.php";
	document.form2.target="venderdetail_pop";
	document.form2.submit();
}

function setVenderDisabled(vender,disabled) {
	if(disabled!="0" && disabled!="1") {
		alert("���λ��� ������ �߸��Ǿ����ϴ�.");
		return;
	}
	document.etcform.vender.value=vender;
	if(confirm("�ش� ������ü�� ���λ��¸� ["+(disabled=="0"?"ON":"OFF")+"] �Ͻðڽ��ϱ�?")) {
		document.etcform.mode.value="disabled";
		document.etcform.disabled.value=disabled;
		document.etcform.action="<?=$_SERVER[PHP_SELF]?>";
		document.etcform.target="processFrame";
		document.etcform.submit();
	}
}

function viewHistory(vender) {
	window.open("vender_ch_pop.php?vender="+vender,"history","height=400,width=780,toolbar=no,menubar=no,scrollbars=yes,status=no");

}

function loginVender(vender, pd) {

	window.open("","loginVender","");

	document.vForm.id.value=vender;
	document.vForm.passwd.value=pd;
	document.vForm.action="/vender/loginproc.php";
	document.vForm.target="loginVender";
	document.vForm.submit();
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
			<? include ("menu_vender.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">������ġ : �������� &gt; ������ü ���� &gt; <span class="2depth_select">������ü ��������</span></td>
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
					<TD><IMG SRC="images/vender_management_title.gif" ALT=""></TD>
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
					<TD width="100%" class="notice_blue"><p>���� ��ü�� ������ ����/���� �Ͻ� �� �ֽ��ϴ�.</p></TD>
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
							<TD height="35" background="images/blueline_bg.gif"><p align="center"><b><font color="#333333">���� ��ü �˻� ����</font></b></TD>
						</TR>
						<TR>
							<TD >
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD background="images/table_con_line.gif"></TD>
							</TR>
							<TR>
								<TD class="td_con1" style="padding-top:10pt;" align="center"><select name=disabled class="select">
									<option value="">����/����ü ��ü</option>
									<option value="Y" <?if($disabled=="Y")echo"selected";?>>���� ��ü�� �˻�</option>
									<option value="N" <?if($disabled=="N")echo"selected";?>>��� ��ü�� �˻�</option>
									</select>
									<select name="s_check" class="select">
									<option value="id" <?if($s_check=="id")echo"selected";?>>��ü ���̵�� �˻�</option>
									<option value="com_name" <?if($s_check=="com_name")echo"selected";?>>��ü������ �˻�</option>
									</select>
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
			<tr><td height="20"></td></tr>
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
			<tr><td height="5"></td></tr>
			<tr>
				<td style="padding-left:15px;">
					<span class="notice_blue">
						<b>��</b> ������ü <b>���̵�</b> Ŭ���� �ش� ������ü�� <b>�̴ϼ�</b>���� �ٷ� �̵��˴ϴ�.<br />
						<b>��</b> <b>������ ��ư</b> Ŭ���� �α��� ���� �ش� ������ü�� <b>������</b> �������� �ٷ� �̵��˴ϴ�.
					</span>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing="0" cellPadding="0" border="0" style="table-layout:fixed">
				<col width="50"></col>
				<col width="90"></col>
				<col width="90"></col>
				<col width=""></col>
				<col width="70"></col>
				<col width="120"></col>
				<col width="185"></col>
				<col width="130"></col>
				<col width="45"></col>
				<col width="45"></col>
				<col width="45"></col>
				<TR>
					<TD background="images/table_top_line.gif" colspan="11" height="1"></TD>
				</TR>
				<TR>
					<TD class="table_cell" align="center">��ȣ</TD>
					<TD class="table_cell1" align="center">��üID</TD>
					<TD class="table_cell1" align="center">ȸ���</TD>
					<TD class="table_cell1" align="center">ȸ����ȭ</TD>
					<TD class="table_cell1" align="center">����ڸ�</TD>
					<TD class="table_cell1" align="center">�޴���ȭ</TD>
					<TD style="BORDER-left:#E3E3E3 1pt solid;" align="center">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<col width="25%"></col>
							<col width="25%"></col>
							<col width="25%"></col>
							<col width="25%"></col>
							<tr height="18">
								<td colspan="4" class="table_cell" align="center">��ǰ����</td>
							</tr>
							<TR>
								<TD colspan="4" background="images/table_con_line.gif"></TD>
							</TR>
							<tr>
								<td class="table_cell" align="center">���</td>
								<td class="table_cell1" align="center">����</td>
								<td class="table_cell1" align="center">����</td>
								<td class="table_cell1" align="center">����</td>
							</tr>
						</table>
					</TD>
					<TD class="table_cell1" align="center">����������<br/>/������</TD>
					<TD class="table_cell1" align="center">����</TD>
					<TD class="table_cell1" align="center">��</TD>
					<TD class="table_cell1" align="center">����</TD>
				</TR>
				<TR>
					<TD colspan="11" align=center background="images/table_con_line.gif"><img src="images/table_con_line.gif" width="4" height="1" border="0"></TD>
				</TR>
<?
		$colspan=10;
		if($t_count>0) {
			/*
			$sql = "SELECT * FROM tblvenderinfo ".$qry." ";
			*/

			/* ������ ���� �߰� jdy */
			$sql = "SELECT i.*, m.commission_type FROM tblvenderinfo i left join vender_more_info m on i.vender=m.vender ".$qry." ";
			/* ������ ���� �߰� jdy */

			$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
			$result=mysql_query($sql,get_db_conn());
			$i=0;
			while($row=mysql_fetch_object($result)) {
				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$i);
				echo "<tr bgcolor=#FFFFFF onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='#FFFFFF'\">\n";
				echo "	<td class=\"td_con2\" align=center>".$number."</td>\n";

				/* ������ ���� �߰� jdy */
				echo "	<td class=\"td_con1\" align=center><A HREF=\"".$Dir.(MinishopType=="ON"?"minishop/":"minishop.php?storeid=").$row->id."\" target=_blank><b>".$row->id."</b></A>";
				echo " <br/><a href=\"javascript:loginVender('".$row->id."','".$row->passwd."');\"><span style='padding:3px 0px;'><img src=\"images/icon_venderlogin.gif\" alt=\"������\" /></span></a>";
				echo "	</td>\n";
				/* ������ ���� �߰� jdy */

				echo "	<td class=\"td_con1\" align=center>&nbsp;".$row->com_name."&nbsp;</td>\n";
				echo "	<td class=\"td_con1\" align=center>&nbsp;".$row->com_tel."&nbsp;</td>\n";
				echo "	<td class=\"td_con1\" align=center>&nbsp;".$row->p_name."&nbsp;</td>\n";
				echo "	<td class=\"td_con1\" align=center>&nbsp;".$row->p_mobile."&nbsp;</td>\n";
				echo "	<td style=\"BORDER-left:#E3E3E3 1pt solid;\" align=center>\n";
				echo "	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
				echo "	<col width=25%></col>\n";
				echo "	<col width=25%></col>\n";
				echo "	<col width=25%></col>\n";
				echo "	<col width=25%></col>\n";
				echo "	<tr>\n";
				echo "		<td class=\"td_con2\" align=center><B>".(substr($row->grant_product,0,1)=="Y"?"<span class=font_blue>Y</span>":"<span class=font_orange>N</span>")."</B></td>\n";
				echo "		<td class=\"td_con1\" align=center><B>".(substr($row->grant_product,1,1)=="Y"?"<span class=font_blue>Y</span>":"<span class=font_orange>N</span>")."</B></td>\n";
				echo "		<td class=\"td_con1\" align=center><B>".(substr($row->grant_product,2,1)=="Y"?"<span class=font_blue>Y</span>":"<span class=font_orange>N</span>")."</B></td>\n";
				echo "		<td class=\"td_con1\" align=center><B>".(substr($row->grant_product,3,1)=="Y"?"<span class=font_blue>Y</span>":"<span class=font_orange>N</span>")."</B></td>\n";
				echo "	</tr>\n";
				echo "	</table>\n";
				echo "	</td>\n";

				/* ������ ���� �߰� jdy */
				if ($account_rule != "1" ) {
				//������� ��ɽ�
					if ($row->commission_type=="1") {
						echo " <td class=\"td_con1\" align=center>��ǰ���� ������";
					}else{
						echo " <td class=\"td_con1\" align=center>��ü������ ".$row->rate." %";
					}
					echo "&nbsp;&nbsp;<img src=\"images/icon_history.gif\" style='cursor:pointer; border:0;' onclick=\"viewHistory('".$row->vender."')\">";
					echo "</td>\n";
				}else{
				//���ް��� ��ɽ� ������ ��ǰ����

					echo " <td class=\"td_con1\" align=center>��ǰ���� ���ް�</td>\n";
				}
				/* ������ ���� �߰� jdy */

				echo "	<td class=\"td_con1\" align=center><A HREF=\"javascript:VenderModify(".$row->vender.")\">[����]</A></td>\n";
				echo "	<td class=\"td_con1\" align=center><A HREF=\"javascript:VenderDetail(".$row->vender.")\">[��]</A></td>\n";
				echo "	<td class=\"td_con1\" align=center>";
				if($row->disabled=="0") {
					echo "<img src=images/icon_on.gif border=0 align=absmiddle style=\"cursor:hand\" onclick=\"setVenderDisabled('".$row->vender."','1')\">";
				} else {
					echo "<img src=images/icon_off.gif border=0 align=absmiddle style=\"cursor:hand\" onclick=\"setVenderDisabled('".$row->vender."','0')\">";
				}
				echo "	</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<TD colspan=\"11\" background=\"images/table_con_line.gif\"></TD>\n";
				echo "</tr>\n";
				$i++;
			}
			mysql_free_result($result);
		} else {
			echo "<tr><td class=td_con2 colspan=".$colspan." align=center>�˻��� ������ �������� �ʽ��ϴ�.</td></tr>";
		}
?>

				<TR>
					<TD background="images/table_top_line.gif" colspan="11"></TD>
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
			// ����	x�� ����ϴ� �κ�-����
			$a_first_block = "";
			if ($nowblock > 0) {
				$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='ù ������';return true\"><IMG src=\"images/icon_first.gif\" border=0 align=\"absmiddle\"></a>&nbsp;&nbsp;";

				$prev_page_exists = true;
			}

			$a_prev_page = "";
			if ($nowblock > 0) {
				$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='���� ".$setup[page_num]." ������';return true\">[prev]</a>&nbsp;&nbsp;";

				$a_prev_page = $a_first_block.$a_prev_page;
			}

			// �Ϲ� �������� ������ ǥ�úκ�-����

			if (intval($total_block) <> intval($nowblock)) {
				$print_page = "";
				for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
					if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
						$print_page .= "<span class=font_orange2><B>[".(intval($nowblock*$setup[page_num]) + $gopage)."]</B></span> ";
					} else {
						$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='������ : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
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
						$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='������ : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
					}
				}
			}		// ������ �������� ǥ�úκ�-��


			$a_last_block = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
				$last_gotopage = ceil($t_count/$setup[list_num]);

				$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='������ ������';return true\"><IMG src=\"images/icon_last.gif\" border=0 align=\"absmiddle\" width=\"17\" height=\"14\"></a>";

				$next_page_exists = true;
			}

			// ���� 10�� ó���κ�...

			$a_next_page = "";
			if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
				$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='���� ".$setup[page_num]." ������';return true\">[next]</a>";

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
			<? /* �α��� ���� �߰� jdy */?>
			<form name=vForm method=post>
			<input type=hidden name="id">
			<input type=hidden name="passwd">
			<input type=hidden name="admin_chk" value="1">
			</form>
			<? /* �α��� ���� �߰� jdy */?>
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
						<td ><span class="font_dotline">������ü ��������</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- ��ϵ� ������ü ����Ʈ�� �⺻���� ���������� Ȯ���� �� �ֽ��ϴ�.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- ������ ���������� [����] �̿��Ͽ� ������ �� �ֽ��ϴ�.</p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- ������ �̴ϼ� ���� URL�� <B><font class=font_orange><A HREF="http://<?=$_ShopInfo->getShopurl()?>vender/" target="_blank">http://<?=$_ShopInfo->getShopurl()?>vender/</A></font></B> �Դϴ�. </p></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top"><p>- �ش��ü �̴ϼ� URL�� <B><font class=font_orange>http://<?=$_ShopInfo->getShopurl().(MinishopType=="ON"?"minishop/":"minishop.php?storeid=")?>��üID</font></B> �Դϴ�. </p></td>
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