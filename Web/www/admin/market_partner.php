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

$max=20;
$type=$_POST["type"];
$partner_id=$_POST["partner_id"];
$up_url=$_POST["up_url"];
$up_id=$_POST["up_id"];
$up_passwd=$_POST["up_passwd"];

unset($onload);
if($type=="insert" && strlen($up_url)>0 && strlen($up_id)>0 && strlen($up_passwd)>0) {
	$sql = "SELECT COUNT(*) as cnt FROM tblpartner ";
	$result = mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$partner_cnt=$row->cnt;
	mysql_free_result($result);
	if($partner_cnt<$max) {
		if (!eregi("^[a-zA-Z0-9]*$", $up_id)) {
			$onload="<script>alert('ID는 영문/숫자만 입력 가능합니다.');history.go(-1);</script>";
		} else {
			$sql = "SELECT COUNT(*) as cnt FROM tblpartner ";
			$sql.= "WHERE id = '".$up_id."' ";
			$result = mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			if ($row->cnt!=0) {
				$onload="<script>alert('관리ID가 중복되었습니다.');history.go(-1);</script>";
			}
			mysql_free_result($result);
		}
		if (strlen($onload)==0) {
			$sql = "INSERT tblpartner SET ";
			$sql.= "id			= '".$up_id."', ";
			$sql.= "passwd		= '".$up_passwd."', ";
			$sql.= "url			= '".$up_url."', ";
			$sql.= "hit_cnt		= 0, ";
			$sql.= "authkey		= '' ";
			$insert = mysql_query($sql,get_db_conn());
			if ($insert) $onload="<script>alert('제휴사 등록이 완료되었습니다.');</script>";
		}
	} else {
		$onload="<script>alert('제휴사는 ".$max."개 까지 등록이 가능합니다.');</script>";
	}
} else if ($type=="delete" && strlen($partner_id)>0) {
	$sql = "DELETE FROM tblpartner WHERE id='".$partner_id."'";
	mysql_query($sql,get_db_conn());
	$onload="<script> alert('해당 제휴사가 삭제되었습니다.');</script>\n";
} else if ($type=="init" && strlen($partner_id)>0) {
	$sql = "UPDATE tblpartner SET hit_cnt=0 WHERE id='".$partner_id."'";
	mysql_query($sql,get_db_conn());
	$onload="<script> alert('해당 제휴사의 총 접속자 수를 0으로 초기화 하였습니다.');</script>\n";
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if (document.form1.up_url.value.length==0) {
		document.form1.up_url.focus();
		alert("제휴사 URL 또는 식별단어를 입력하세요.");
		return;
	}
	if (document.form1.up_id.value.length==0) {
		document.form1.up_id.focus();
		alert("제휴사 관리 아이디를 입력하세요.");
		return;
	}
	if (CheckLength(document.form1.up_id)>20) {
		document.form1.up_id.focus();
		alert("제휴사 관리 아이디는 20자 까지 입력 가능합니다.");
		return;
	}
	if (document.form1.up_passwd.value.length==0) {
		document.form1.up_passwd.focus();
		alert("제휴사 관리 패스워드를 입력하세요.");
		return;
	}
	if (CheckLength(document.form1.up_passwd)>20) {
		document.form1.up_passwd.focus();
		alert("제휴사 관리 패스워드는 20자 까지 입력 가능합니다.");
		return;
	}
	document.form1.type.value="insert";
	document.form1.submit();
}

function PartnerDelete(id) {
	if(confirm("해당 제휴사를 삭제하시겠습니까?")){
		document.form2.type.value="delete";
		document.form2.partner_id.value=id;
		document.form2.submit();
	}
}
function PartnerInit(id) {
	if(confirm("해당 제휴사 총 접속자 수를 초기화 하시겠습니까?")){
		document.form2.type.value="init";
		document.form2.partner_id.value=id;
		document.form2.submit();
	}
}
function PartnerOrder(id,pw) {
	document.form3.id.value=id;
	document.form3.passwd.value=pw;
	document.form3.submit();
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 마케팅지원 &gt; 마케팅지원 &gt; <span class="2depth_select">제휴마케팅 관리</span></td>
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
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_partner_title.gif" ALT=""></TD>
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
					<TD width="100%" class="notice_blue">제휴사 관리 및 제휴배너를 통한 접속자,주문통계를 확인하실 수 있습니다.</TD>
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
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_partner_stitle1.gif" WIDTH="187" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
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
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">
					1) &quot;초기화&quot; 버튼 클릭시 제휴사를 통한 방문 접속자가 &quot;0&quot;으로 초기화 됩니다.
					<br>2) &quot;주문조회&quot; 버튼 클릭시 제휴사를 통하여 방문한 고객의 주문조회를 하실 수 있습니다.
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
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=></col>
				<col width=120></col>
				<col width=80></col>
				<col width=70></col>
				<col width=90></col>
				<col width=90></col>
				<col width=65></col>
				<TR>
					<TD colspan=7 background="images/table_top_line.gif"></TD>
				</TR>
				<TR align=center>
					<TD class="table_cell">제휴사 URL 또는 식별단어</TD>
					<TD class="table_cell1">관리ID[비밀번호]</TD>
					<TD class="table_cell1">총접속자</TD>
					<TD class="table_cell1">오늘주문</TD>
					<TD class="table_cell1">주문조회</TD>
					<TD class="table_cell1">초기화</TD>
					<TD class="table_cell1">삭제</TD>
				</TR>
				<TR>
					<TD colspan="7" background="images/table_con_line.gif"></TD>
				</TR>
<?
				$today=date("Ymd");
				$sql = "SELECT a.id, a.passwd, a.url, a.hit_cnt, count(b.ordercode) as order_cnt ";
				$sql.= "FROM tblpartner a LEFT JOIN tblorderinfo b ON b.ordercode LIKE '".$today."%' ";
				$sql.= "AND b.partner_id=a.id GROUP BY a.id, a.passwd, a.url ";
				$result = mysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=mysql_fetch_object($result)) {
					$cnt++;
					echo "<TR>\n";
					echo "	<TD align=center class=\"td_con2\">".$row->url."</TD>\n";
					echo "	<TD align=center class=\"td_con1\"><b>".$row->id."</b> (<b><span class=\"font_orange\">".$row->passwd."</span></b>)</TD>\n";
					echo "	<TD align=center class=\"td_con1\">".number_format($row->hit_cnt)."</TD>\n";
					echo "	<TD align=center class=\"td_con1\"><span class=\"font_orange\"><b>".number_format($row->order_cnt)."</b></span></TD>\n";
					echo "	<TD align=center class=\"td_con1\"><a href=\"javascript:PartnerOrder('".$row->id."','".$row->passwd."');\"><img src=\"images/btn_search1.gif\" height=\"25\" border=\"0\"></a></TD>\n";
					echo "	<TD align=center class=\"td_con1\"><a href=\"javascript:PartnerInit('".$row->id."');\"><img src=\"images/btn_first.gif\" width=\"74\" height=\"25\" border=\"0\"></a></TD>\n";
					echo "	<TD align=center class=\"td_con1\"><a href=\"javascript:PartnerDelete('".$row->id."');\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></a></TD>\n";
					echo "</TR>\n";
					echo "<TR>\n";
					echo "	<TD colspan=\"7\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</TR>\n";
				}
				mysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td class=td_con2 colspan=7 align=center>등록된 제휴사가 존재하지 않습니다..</td></tr>";
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
					<TD><IMG SRC="images/market_partner_stitle2.gif" WIDTH="187" HEIGHT=31 ALT=""></TD>
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
				<col width=155></col>
				<col width=></col>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">제휴사URL 또는 식별단어</TD>
					<TD class="td_con1"><INPUT style="WIDTH:100%" maxLength=100 name=up_url class="input"></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">제휴사 관리 아이디</TD>
					<TD class="td_con1"><INPUT maxLength=20 name=up_id class="input"> <span class="font_orange">* 한글 입력 불가.영문,숫자조합</span></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">제휴사 관리 패스워드</TD>
					<TD class="td_con1"><INPUT maxLength=20 name=up_passwd class="input"></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>
			<tr>
				<td height="30">&nbsp;</td>
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
					<col width=20></col>
					<col width=></col>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><b>제휴사 실적조회 URL</b></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td style="padding-bottom:5pt;">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD background="images/table_top_line.gif" width="153"><img src="images/table_top_line.gif"></TD>
							<TD background="images/table_top_line.gif" width="607" ></TD>
						</TR>
						<TR>
							<TD class="table_cell" width="153"><B>제휴사 실적조회 URL</B></TD>
							<TD class="td_con1" width="600"><A href="http://<?=$shopurl.PartnerDir?>index.php" target=_blank><B><span class="font_blue">http://<?=$shopurl.PartnerDir?>index.php</span></B></A></TD>
						</TR>
						<TR>
							<TD background="images/table_top_line.gif" width="153"><img src="images/table_top_line.gif"></TD>
							<TD background="images/table_top_line.gif" width="607"></TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td align="right"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>제휴사에 알려주셔야 할 실적조회 URL입니다.<br></td>
					</tr>
					<tr>
						<td align="right"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td> 발급한 아이디/비번으로 로그인 하면 해당 제휴사를 통하여 방문한 고객의 주문내역을 확인할 수 있습니다. <br></td>
					</tr>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><b><span class="font_orange"> 제휴사에서의 쇼핑몰 링크방법 안내 </span></b><br><span class="font_orange">http://<?=$shopurl?>?ref=제휴사URL 또는 식별단어<br>
						예) 식별단어가 "partner" 일 경우 http://<?=$shopurl?>?ref=partner<br>
						<b>&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;제휴사URL이 "http://www.partner.com" 일 경우 http://<?=$shopurl?>?ref=http://www.partner.com<br>
						<b>&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;제휴사URL이 "www.partner.com" 일 경우 http://<?=$shopurl?>?ref=www.partner.com</span></td>
					</tr>
					<tr>
						<td align="right"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td> 위 방법과 같은 제휴를 통하여 수익을 창출하고, 그 수익에 대한 수수료를 제휴사에 배분하는 방식으로 운영</td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"><IMG SRC="images/manual_right1.gif" WIDTH=18 HEIGHT="2" ALT=""></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"><IMG SRC="images/manual_down.gif" WIDTH="4" HEIGHT=8 ALT=""></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</form>

			<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=partner_id>
			</form>

			<form name=form3 action="http://<?=$shopurl.PartnerDir?>order_search.php" method=post target=_blank>
			<input type=hidden name=id>
			<input type=hidden name=passwd>
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