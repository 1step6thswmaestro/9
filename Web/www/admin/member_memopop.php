<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$type=$_POST["type"];
$id=$_POST["id"];
$ordercode=$_POST["ordercode"];	//상품 상세화면에서 넘어오는 값
$date=$_POST["date"];
$up_memo=trim($_POST["up_memo"]);

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
	$sql = "DELETE FROM tblmemo WHERE id = '".$id."' AND date = '".$date."' ";
	mysql_query($sql,get_db_conn());
	if(strlen($ordercode)>0) {
		echo "<script>try {opener.formmemo.submit();window.close();} catch (e) {}</script>";
		exit;
	}
} else if($type=="update") {
	$sql = "UPDATE tblmember SET memo='".$up_memo."' WHERE id='".$id."' ";
	mysql_query($sql,get_db_conn());
	if(strlen($up_memo)>0) {
		$sql = "INSERT tblmemo SET ";
		$sql.= "id			= '".$id."', ";
		$sql.= "date		= '".date("YmdHis")."', ";
		$sql.= "memo		= '".$up_memo."' ";
		mysql_query($sql,get_db_conn());
	}
	if(strlen($ordercode)>0) {
		echo "<script>try {opener.formmemo.submit();window.close();} catch (e) {}</script>";
		exit;
	}
}
$sql = "SELECT name,memo FROM tblmember WHERE id = '".$id."' ";
$result = mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$name = $row->name; 
	$memo = $row->memo;
} else {
	echo "</head><body onload=\"alert('해당 회원이 존재하지 않습니다.');window.close();\"></body></html>";exit;
}
mysql_free_result($result);
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>운영자 메모 내역</title>
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
	var oHeight = document.all.table_body.clientHeight + 80;

	window.resizeTo(oWidth,oHeight);
}

function MemoUpdate(mode) {
	if(mode=="delete") document.form1.up_memo.value="";
	document.form1.type.value="update";
	document.form1.submit();
}

function MemoDelete(date) {
	if(confirm("해당 내역을 삭제하시겠습니까?")) {
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
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="550" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD height="31" background="images/member_mailallsend_imgbg.gif" >
	<table cellpadding="0" cellspacing="0" width="550">
	<tr>
		<td width="525">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="28">&nbsp;</td>
			<td><b><font color="#FFFFFF"><?=$name?>회원님에 대한 운영자 메모 내역</b></font></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD style="padding:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;" class="font_size"><span style="letter-spacing:-0.5pt;">해당 고객에 대한 특정사항을 메모하세요.고객의 주문서 상세내역에 표시됩니다.<br>(영문200자, 한글100자) 예)샘플 꼭 넣어달라고 연락왔었음.</span></td>
	</tr>
	<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
	<input type=hidden name=type>
	<input type=hidden name=block>
	<input type=hidden name=gotopage>
	<input type=hidden name=id value="<?=$id?>">
	<input type=hidden name=date>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr align=center>
			<td><input type=text name=up_memo value="<?=$memo?>" size="40" name=search class="input_selected" style="width:99%"></td>
			<td width="42"><a href="javascript:MemoUpdate('update');"><img src="images/btn_add2.gif" width="50" height="22" border="0" hspace="1"></a></td>
			<td width="42"><a href="javascript:MemoUpdate('delete');"><img src="images/btn_del.gif" width="50" height="22" border="0"></a></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;"><hr size="1" align="center" color="#EBEBEB"></td>
	</tr>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;"><img src="images/icon_9.gif" width="13" height="9" border="0"><b><font color="black">메모내역</b>(등록되어 있는 메모내역을 확인하실수 있습니다.)</font></td>
	</tr>
	<tr>
		<td>
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<col width=90></col>
		<col width=></col>
		<col width=65></col>
		<TR>
			<TD background="images/table_top_line.gif" colspan="3" height=1></TD>
		</TR>
		<TR align=center>
			<TD class="table_cell">날짜</TD>
			<TD class="table_cell1">메모</TD>
			<TD class="table_cell1">삭제</TD>
		</TR>
<?
		$colspan=3;
		echo "<TR><TD colspan=".$colspan." background=\"images/table_con_line.gif\"></TD></TR>";
		$sql = "SELECT COUNT(*) as t_count FROM tblmemo WHERE id = '".$id."' ";
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		$t_count = $row->t_count;
		mysql_free_result($result);
		$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

		$sql = "SELECT * FROM tblmemo WHERE id = '".$id."' ORDER BY date DESC ";
		$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
		$result = mysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=mysql_fetch_object($result)) {
			$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
			$str_date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
			echo "<tr>\n";
			echo "	<TD align=center class=\"td_con2\">".$str_date."</td>\n";
			echo "	<TD class=\"td_con1\">".$row->memo."</td>\n";
			echo "	<TD align=center class=\"td_con1\"><A HREF=\"javascript:MemoDelete('".$row->date."');\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></A></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<TD colspan=".$colspan." background=\"images/table_con_line.gif\"></TD>\n";
			echo "</tr>\n";
			$cnt++;
		}
		mysql_free_result($result);
		if ($cnt==0) {
			echo "<tr><TD class=\"td_con2\" colspan=".$colspan." align=center>운영자 메모 내역이 없습니다.</td></tr>";
		}
		echo "<TR><TD background=\"images/table_top_line.gif\" colspan=".$colspan." height=1></TD></TR>\n";
?>
		</TABLE>
		</td>
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
	echo "	<td height=\"30\" class=\"font_size\" align=center>\n";
	echo "		".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
	echo "	</td>\n";
	echo "</tr>\n";
?>
	</table>
	</TD>
</TR>
<TR>
	<TD align=center><a href="javascript:window.close()"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="2" border=0></a></TD>
</TR>
</TABLE>
</body>
</html>