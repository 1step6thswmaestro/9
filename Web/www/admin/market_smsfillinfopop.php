<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>window.close();</script>";
	exit;
}

$sql = "SELECT id, authkey, return_tel FROM tblsmsinfo ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)){
	$sms_id=$row->id;
	$sms_authkey=$row->authkey;
}
mysql_free_result($result);

if(strlen($sms_id)==0 || strlen($sms_authkey)==0) {
	echo "<html></head><body onload=\"alert('SMS 기본환경 설정에서 SMS 아이디 및 인증키를 입력하시기 바랍니다.');opener.location.href='market_smsconfig.php';window.close();\"></body></html>";exit;
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

$t_count=0;
$smslistdata=array();

#########################################################
#														#
#			SMS서버와 통신 루틴 추가 (완료)				#
#														#
#########################################################
$query="block=".$block."&gotopage=".$gotopage;
$resdata=getSmsfillinfo($sms_id,$sms_authkey, $query);
if(substr($resdata,0,2)=="OK") {
	$tempdata=explode("=",$resdata);
	$t_count=$tempdata[1];
	$smslistdata=unserialize($tempdata[2]);
} else if(substr($resdata,0,2)=="NO") {
	$tempdata=explode("=",$resdata);
	$onload="<script>alert('".$tempdata[1]."');</script>";
} else {
	$onload="<script>alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.');</script>";
}


$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=euc-kr'>
<title>SMS 충전내역 확인</title>
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	try {
		if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
			event.keyCode = 0;
			return false;
		}
	} catch (e) {}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 75;

	window.resizeTo(oWidth,oHeight);
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
<TABLE WIDTH="450" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD width="450" height="31" background="images/win_titlebg.gif">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><img src="images/newtitle_icon.gif" border="0"></td>
			<td width="100%" background="images/member_mailallsend_imgbg.gif"><font color=FFFFFF><b>SMS 충전내역</b></font></td>
			<td align="right"><img src="images/member_mailallsend_img2.gif" width="20" height="31" border="0"></td>
		</tr>
		</table>
	</TD>
</TR>
<tr>
	<td width=100%>
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
	<input type=hidden name=type>
	<input type=hidden name=block>
	<input type=hidden name=gotopage>
	<TR>
		<TD style="padding-top:3pt; padding-bottom:3pt;">
		<table align="center" cellpadding="0" cellspacing="0" width="98%">
		<tr>
			<td>
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
			<col width=70></col>
			<col width=85></col>
			<col width=80></col>
			<col width=></col>
			<TR>
				<TD background="images/table_top_line.gif" colspan="4" height=1></TD>
			</TR>
			<TR>
				<TD class="table_cell" align="center">충전일자</TD>
				<TD class="table_cell1" align="center">충전금액</TD>
				<TD class="table_cell1" align="center">충전건수</TD>
				<TD class="table_cell1" align="center">결제내역</TD>
			</TR>
			<TR>
				<TD height="1" colspan="4" background="images/table_con_line.gif"></TD>
			</TR>
<?
			$colspan=4;
			$cnt=0;
			for($i=0;$i<count($smslistdata);$i++) {
				$row=$smslistdata[$i];
				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
				$str_date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
				echo "<tr align=center>\n";
				echo "	<td class=td_con2>".$str_date."</td>\n";
				echo "	<td class=td_con1>".number_format($row->price)."원</td>\n";
				echo "	<td class=td_con1>".$row->cntstr."</td>\n";
				echo "	<td class=td_con1>";
				if($row->paymethod=="B") {
					echo "무통장입금";
					if (strlen($row->bank_date)==9 && substr($row->bank_date,8,1)=="X") echo "[<font color=005000> 환불</font>]";
					else if (strlen($row->bank_date)>0 && $row->card_flag=="0000") echo " [<font color=004000>입금완료</font>]";
					else echo "[미입금]";
				} else if($row->paymethod=="C") {
					echo "신용카드";
					if ($row->card_flag=="0000" && $row->admin_card_flag=="Y") echo "[<font color=0000a0>결제완료</font>]";
				}
				echo "	</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<TD height=\"1\" colspan=\"4\" background=\"images/table_con_line.gif\"></TD>\n";
				echo "</tr>\n";
				$cnt++;
			}
			if ($cnt==0) {
				echo "<tr><td class=\"td_con2\" colspan=\"".$colspan."\" align=center>SMS 충전내역이 없습니다.</td></tr>";
			}
			echo "<tr><td colspan=".$colspan." height=1 background=\"images/table_top_line.gif\"></td></tr>\n";

			$total_block = intval($pagecount / $setup[page_num]);

			if (($pagecount % $setup[page_num]) > 0) {
				$total_block = $total_block + 1;
			}

			$total_block = $total_block - 1;

			if (ceil($t_count/$setup[list_num]) > 0) {
				// 이전	x개 출력하는 부분-시작
				$a_first_block = "";
				if ($nowblock > 0) {
					$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\">[1...]</a>&nbsp;&nbsp;";

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
							$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></font> ";
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
							$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></FONT> ";
						} else {
							$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
						}
					}
				}		// 마지막 블럭에서의 표시부분-끝


				$a_last_block = "";
				if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
					$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
					$last_gotopage = ceil($t_count/$setup[list_num]);

					$a_last_block .= "&nbsp;&nbsp;<a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\">[...".$last_gotopage."]</a>";

					$next_page_exists = true;
				}

				// 다음 10개 처리부분...

				$a_next_page = "";
				if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
					$a_next_page .= "&nbsp;&nbsp;<a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\">[next]</a>";

					$a_next_page = $a_next_page.$a_last_block;
				}
			} else {
				$print_page = "<B>1</B>";
			}
			echo "<tr><td colspan=".$colspan." height=10></td></tr>\n";
			echo "<tr>\n";
			echo "	<td colspan=".$colspan." align=center>\n";
			echo "		".$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
			echo "	</td>\n";
			echo "</tr>\n";
?>
			</td>
		</tr>
		</table>
		</TD>
	</TR>
	<tr>
		<td align=center><a href="javascript:window.close()"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="10" border=0></a></td>
	</tr>
	</form>
	</table>
	</td>
</tr>
</table>

<?=$onload?>

</body>
</html>