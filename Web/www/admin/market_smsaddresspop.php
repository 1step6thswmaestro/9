<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
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

$addr_group=$_POST["addr_group"];
$search=$_POST["search"];
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>SMS 주소 등록/수정</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
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

function CheckAll(){
	chkval=document.form1.allcheck.checked;
	cnt=document.form1.tot.value;
	for(i=1;i<=cnt;i++){
		document.form1.tels_chk[i].checked=chkval;
	}
}

function SearchGroup() {
	document.form1.block.value="";
	document.form1.gotopage.value="";
	document.form1.search.value="";
	document.form1.submit();
}

function ToAddressAdd(tel_txt,tel_val) {
	try {
		if(tel_txt.length<12 || tel_txt.length>13) {
			alert("전화번호 입력이 잘못되었습니다. ("+tel_txt+")");
			return;
		}
		to_list=opener.document.form1.to_list;
		if(to_list.options.length>50) {
			alert("받는 사람은 1회 50명 까지 가능합니다.");
			return;
		}
		for(i=1;i<to_list.options.length;i++) {
			if(tel_val==to_list.options[i].value) {
				//alert("이미 추가된 번호입니다.\n\n다시 확인하시기 바랍니다.");
				return;
			}
		}

		new_option = opener.document.createElement("OPTION");
		new_option.text=tel_txt;
		new_option.value=tel_val;
		to_list.add(new_option);
		cnt=to_list.options.length - 1;
		to_list.options[0].text = "------------------- 수신목록("+cnt+") ----------------------";
	} catch (e) {

	}
}


function select(mobile) {
	tel_val=mobile.replace("-","");
	ToAddressAdd(mobile,tel_val);
}

function select_list() {
	issel=false;
	for(i=1;i<document.form1.tels_chk.length;i++) {
		if(document.form1.tels_chk[i].checked==true) {
			issel=true;
			tel_val=document.form1.tels_chk[i].value.replace("-","");
			ToAddressAdd(document.form1.tels_chk[i].value,tel_val);
		}
	}
	if(issel==false) {
		alert("선택하신 SMS번호가 없습니다.");
		return;
	}
}

function search_name() {
	document.form1.block.value="";
	document.form1.gotopage.value="";
	document.form1.submit();
}

function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" oncontextmenu="return false">
<TABLE WIDTH="400" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><img src="images/market_smsaddressbk_title.gif" border="0" width="212" height="31"></td>
		<td width="100%" background="images/member_mailallsend_imgbg.gif"></td>
		<td align="right"><img src="images/member_mailallsend_img2.gif" width="20" height="31" border="0"></td>
	</tr>
	</table>
	</TD>
</TR>
<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<tr>
	<TD style="padding:10pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><a href="javascript:select_list();"><img src="images/btn_select1a.gif" width="38" height="18" border="0"></a>&nbsp;&nbsp;그룹선택 <select name=addr_group onchange="SearchGroup();" class="select">
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
			</select>
		</td>
	</tr>
	<tr>
		<td>
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<input type=hidden name=tels_chk>
		<TR>
			<TD background="images/table_top_line.gif" colspan="3"></TD>
		</TR>
		<TR>
			<TD class="table_cell1" width="59" align="center"><input type=checkbox name=allcheck onclick="CheckAll()"></TD>
			<TD class="table_cell1" width="80" align="center">이름</TD>
			<TD class="table_cell1" width="174" align="center">휴대폰번호</TD>
		</TR>
		<TR>
			<TD colspan="3" background="images/table_con_line.gif"></TD>
		</TR>
<?
		$qry = "WHERE 1=1 ";
		if(strlen($addr_group)>0) $qry.= "AND addr_group='".$addr_group."' ";
		if(strlen($search)>0) $qry.= "AND name LIKE '".$search."%' ";

		$sql = "SELECT COUNT(*) as t_count FROM tblsmsaddress ".$qry;
		$result = mysql_query($sql,get_db_conn());
		$row = mysql_fetch_object($result);
		$t_count = $row->t_count;
		$pagecount = (($t_count - 1) / $setup[list_num]) + 1;
		mysql_free_result($result);

		$sql = "SELECT * FROM tblsmsaddress ".$qry." ";
		$sql.= "ORDER BY name ASC ";
		$sql.= "LIMIT " . ($setup[list_num] * ($gotopage - 1)) . ", " . $setup[list_num];
		$result=mysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=mysql_fetch_object($result)) {
			echo "<tr>\n";
			echo "	<TD class=\"td_con1\" align=\"center\"><input type=checkbox name=tels_chk value=\"".$row->mobile."\"></td>\n";
			echo "	<TD class=\"td_con1\" align=\"center\"><b><span class=\"font_orange\">".$row->name."</span></b></TD>\n";
			echo "	<TD class=\"td_con1\" align=\"center\"><A HREF=\"javascript:select('".$row->mobile."')\">".$row->mobile."</A></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<TD colspan=\"3\" background=\"images/table_con_line.gif\"></TD>\n";
			echo "</tr>\n";
			$cnt++;
		}
		mysql_free_result($result);
		if ($cnt==0) {
			echo "<tr><td class=\"td_con1\" colspan=3 align=center>조건에 맞는 내역이 존재하지 않습니다.</td></tr>";
		}
?>
		<TR>
			<TD background="images/table_top_line.gif" colspan="3"></TD>
		</TR>
		</TABLE>
		</td>
	</tr>
	<tr>
		<td height=10></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
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
		<tr>
			<td width="100%" class="main_sfont_non" height=10></td>
		</tr>
		<tr>
			<td width="100%" class="main_sfont_non">
			<table cellpadding="10" cellspacing="1" bgcolor="#DBDBDB" width="100%">
			<tr>
				<td width="859" bgcolor="white" align="center">이름검색 : <input type=text name=search value="<?=$search?>" size=30 class="input"> <a href="javascript:search_name();"><img alt=검색 align=absMiddle border=0 src="images/icon_search.gif"></a></td>
			</tr>
			<input type=hidden name=tot value="<?=$cnt?>">
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</TD>
</tr>
<TR>
	<TD align="center"><a href="javascript:window.close()"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="0" border=0 hspace="2"></a></TD>
</TR>
</form>
</TABLE>
<?=$onload?>

</body>
</html>