<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$type=$_POST["type"];
$id=$_POST["id"];
$date=$_POST["date"];

if(strlen($_ShopInfo->getId())==0 || strlen($id)==0){
	echo "<script>window.close();</script>";
	exit;
}

//리스트 세팅
$setup[page_num] = 5;
$setup[list_num] = 10;

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

if($type=="delete" && strlen($date)>0){
	$sql = "DELETE FROM tblreserve WHERE id = '".$id."' AND date = '".$date."' ";
	mysql_query($sql,get_db_conn());

	$log_content = "## 적립금 내역 삭제 ## - 아이디 : $id 금액";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
}
$sql = "SELECT reserve, name FROM tblmember WHERE id = '".$id."' ";
$result = mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$name = $row->name; 
	$reserve = $row->reserve;
}
mysql_free_result($result);
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>적립금 내역</title>
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 70;

	window.resizeTo(oWidth,oHeight);
}

function ReserveDelete(date) {
	if(confirm("해당 적립내역을 삭제하시겠습니까?")) {
		document.form1.type.value="delete";
		document.form1.date.value=date;
		document.form1.submit();
	}
}

function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden; overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<TABLE width="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD height="31" background="images/member_mailallsend_imgbg.gif">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="28"><p>&nbsp;</td>
		<td><p><b><font color="white"><?=$name?>회원님의 적립금 내역</b></font></td>
	</tr>
	</table>
	</TD>
</TR>
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=type>
<input type=hidden name=block>
<input type=hidden name=gotopage>
<input type=hidden name=id value="<?=$id?>">
<input type=hidden name=date>
<TR>
	<TD style="padding-top:3pt; padding-bottom:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt; text-align:right;"><font color="black"><span style="font-size:8pt;">* 현재 총</span><span style="font-size:8pt;" class="font_orange"><b><?=number_format($reserve)?>원</b></span><span style="font-size:8pt;">을 적립하셨습니다.</span></font></td>
	</tr>
	<tr>
		<td>
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TR><TD background="images/table_top_line.gif" colspan="4" height=1></TD></TR>
		<TR align=center>
			<TD class="table_cell">날짜</TD>
			<TD class="table_cell1">적립금</TD>
			<TD class="table_cell1">적립내역</TD>
			<TD class="table_cell1">삭제</TD>
		</TR>
<?
		$colspan=4;
		echo "<TR><TD colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></TD></TR>";
		$sql = "SELECT COUNT(*) as t_count FROM tblreserve WHERE id = '".$id."' ";
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		$t_count = $row->t_count;
		mysql_free_result($result);
		$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

		$sql = "SELECT * FROM tblreserve WHERE id = '".$id."' ORDER BY date DESC ";
		$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
		$result = mysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=mysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
			$str_date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
			echo "<tr>\n";
			echo "	<TD align=center class=\"td_con2\">".$str_date."</td>\n";
			echo "	<TD align=right class=\"td_con1\"><b><span class=\"font_orange\">".number_format($row->reserve)."원</span></b></TD>\n";
			echo "	<TD class=\"td_con1\" style=line-height:17px;>".nl2br($row->content)."</TD>\n";
			echo "	<TD align=center class=\"td_con1\"><A HREF=\"javascript:ReserveDelete('".$row->date."');\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></A></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<TD colspan=\"".$colspan."\" background=\"images/table_con_line.gif\"></TD>\n";
			echo "</tr>\n";
			$cnt++;
		}
		mysql_free_result($result);
		if ($cnt==0) {
			echo "<tr><TD align=center class=\"td_con2\" colspan=".$colspan.">적립금 내역이 없습니다.</td></tr>";
		}
		echo "<tr><TD background=\"images/table_top_line.gif\" colspan=\"".$colspan."\" height=1></td></tr>\n";
?>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td height="30" align=center><span style="font-size:8pt;" class="font_orange">*해당 적립내역을 삭제하셔도 실제 적립금은 변경되지 않습니다.</span></td>
	</tr>					
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
		echo "	<td><p align=\"center\">\n";
		echo "		".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
		echo "	</td>\n";
		echo "</tr>\n";
?>
	</table>
	</TD>
</TR>
<TR>
	<TD align=center><a href="javascript:window.close()"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="10" border=0></a></TD>
</TR>
</form>
</TABLE>
</body>
</html>