<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "st-1";
$MenuCode = "counter";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

 function is_korean($word){ 
	if(strlen($word) != 2) 
		return false; 
	$w1 = ord($word[0]); 
	$w2 = ord($word[1]); 
	
	if($w1 < 0x81 || $w1 > 0xC8 || $w2 > 0xFE || ($w2 >= 0x00 && $w2 <= 0x40) || ($w2 >= 0x5B && $w2 <= 0x60) || ($w2 >= 0x7B && $w2 <= 0x80) || ($w2 >= 0x00 && $w2 <= 0x40) || (($w1 >= 0xA1 && $w1 <=0xAF) && ($w2 >= 0xA1 && $w2 <= 0xFE)) || ($w1 == 0xC6 && ($w2 >= 0x53 && $w2 <= 0xA0)) || ($w1 >= 0xC7 && ($w2 >= 0x41 && $w2 <= 0xA0))) 
		return false; 
	else 
		return true; 
} 

$searchdate=$_POST["searchdate"];
$print=$_POST["print"];
$sdomain=$_POST["sdomain"];

if(strlen($searchdate)==0) $searchdate=date("Ym");
if($searchdate==date("Ym")) $nowdate="Y";

$month= date("m");
$len=50;
if(strlen($sdomain)==0) {
	$sql ="SELECT domain,search,cnt FROM tblcountersearchword ";
	$sql.="WHERE date='".$searchdate."' ORDER BY cnt DESC LIMIT ".$len;
} else {
	$sql ="SELECT domain,search,cnt FROM tblcountersearchword ";
	$sql.="WHERE date='".$searchdate."' AND domain='".$sdomain."' ORDER BY cnt DESC LIMIT ".$len;
}

$sum=0;
$result = mysql_query($sql,get_db_conn());
$i=0;
$searchi=0;
while($row = mysql_fetch_object($result)) {
	$time[$i]=$row->cnt;
	$searchdomain[$i]=$row->domain;
	$page[$i]=$row->search;
	if($max<$row->cnt) $max=$row->cnt;
	$sum+=$row->cnt;

	for($kk=0;$kk<$searchi;$kk++) if($searchdomain[$i]==$alldomain[$kk]) break;
	if($kk==$searchi) $alldomain[$searchi++]=$searchdomain[$i];      

	$i++;
}
mysql_free_result($result);

?>


<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function search_date(type) {
	document.form1.type.value=type;
	document.form1.submit();
}

function view_printpage(){
	window.open("about:blank","popviewprint","height=550,width=700,scrollbars=yes");
	document.form2.print.value="Y";
	document.form2.submit();
}

</script>
<?if($print!="Y"){?>
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
			<? include ("menu_counter.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 통계분석 &gt; 외부 접근 경로 분석 &gt; <span class="2depth_select">검색엔진 검색어 순위</span></td>
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
<?} else {?>
			<table cellpadding="5" cellspacing="0" width="680" style="table-layout:fixed">
<?}?>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/counter_searchkeywordrank_title.gif" ALT=""></TD>
					</tr>
<tr>
<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="40"></td></tr>
			<tr>
				<td align=center>

				<table cellpadding="0" cellspacing="0" width="100%">
				<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
				<input type=hidden name=type>
				<input type=hidden name=print value="<?=$print?>">
				<tr>
					<td style="font-size:11px;">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td>
<?
						if($nowdate=="Y") {
							echo "* <b><font color=\"#FF6633\">".date("Y년 m월 d일")."</font></b> 현재";
						} else {
							echo "* <b><font color=\"#FF6633\">".substr($searchdate,0,4)."년 ".substr($searchdate,4,2)."월</font></b>";
						}
						echo " 검색엔진별 검색어 순위 입니다.";
?>
						</td>
						<td align=right>
						<img src="images/counter_icon_searchname.gif" border=0 align=absmiddle>
						<select name=sdomain style="font-size=9pt;" onchange="search_date()">
						<option value="">전체조회</option>
<?
						for ($kk=0;$kk<$searchi;$kk++) {
							echo "<option value='".$alldomain[$kk]."' ";
							if ($sdomain==$alldomain[$kk]) echo "selected";
							echo ">".$alldomain[$kk]."</option>\n";
						}
?>
						</select>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td style="padding-top:3">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<col width=50></col>
					<col width=200></col>
					<col width=></col>
					<col width=110></col>
					<col width=110></col>
					<tr><td colspan=5 height=2 bgcolor=#000000></td></tr>
					<TR>
						<TD class="table_cell" align=center><FONT color=#3d3d3d>NO</FONT></TD>
						<TD class="table_cell" align=center><FONT color=#3d3d3d>검색사이트</FONT></TD>
						<TD class="table_cell" align=center><FONT color=#3d3d3d>검색어</FONT></TD>
						<TD class="table_cell" align=center><FONT color=#3d3d3d>방문자수</FONT></TD>
						<TD class="table_cell" align=center><FONT color=#3d3d3d>퍼센트</FONT></TD>
					</TR>
					<tr><td colspan=5 height=1 bgcolor=#EDEDED></td></tr>
<?
					$len=count($time); 
					for($i=0;$i<$len;$i++){
						$percent[$i]=$time[$i]/$sum*100;
						if($pos=strpos($percent[$i],".")) {
							$percent[$i]=substr($percent[$i],0,$pos+3);
						}

						if(!is_korean($page[$i])) {
							$tmps = iconv("utf-8","euc-kr",$page[$i]);
							if($tmps) $page[$i] = $tmps;
						}

						echo "<tr>\n";
						echo "	<TD class=\"td_con2a\" align=center>".($i+1)."</td>\n";
						echo "	<TD class=\"td_con2a\" style=\"padding-left:5\">".$searchdomain[$i]."</td>\n";
						echo "	<TD class=\"td_con2a\" align=center>".$page[$i]."</td>\n";
						echo "	<TD class=\"td_con2a\" align=center><FONT color=\"#00769D\">".number_format($time[$i])."명</FONT></td>\n";
						echo "	<TD class=\"td_con2a\" align=center>".$percent[$i]."%</td>\n";
						echo "</tr>\n";
						echo "<tr><td colspan=5 height=1 bgcolor=#EDEDED></td></tr>\n";
					}
					if($len==0){
						echo "<tr bgcolor=#FFFFFF><td colspan=5 height=30 class=\"td_con2a\" align=center><font color=#3D3D3D>해당 자료가 없습니다.</font></td></tr>\n";
						echo "<tr><td colspan=5 height=1 bgcolor=#EDEDED></td></tr>\n";
					}
?>
					</table>
					</td>
				</tr>
				<?if($print!="Y"){?>
				<TR>
					<TD width="100%" background="images/counter_blackline_bg.gif" height="30" align=right>
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td class="font_white" align=right>
						지난 접속통계 
						<select name=searchdate onchange="search_date('m')">
<?
						$cnt=11;  
						for($i=0;$i<=$cnt;$i++) {
							$date=date("Ym",mktime(0,0,0,date("m")-$i,1,date("Y")));
							echo "<option value=\"".$date."\"";
							if($date==$searchdate) echo " selected";
							echo ">".substr($date,0,4)."년 ".substr($date,4,2)."월</option>\n";
						}
?>
						</select>
						</td>
						<td align=right style="padding:0,5,0,5"><A HREF="javascript:view_printpage()"><img src="images/counter_btn_print.gif" width="90" height="20" border="0"></A></td>
					</tr>
					</table>
					</TD>
				</TR>
				<?} else {?>
				<TR>
					<td align=right style="padding:20,20,0,5"><A HREF="javascript:print()"><img src="images/counter_btn_print.gif" width="90" height="20" border="0"></A></td>
				</TR>
				<?}?>
				</form>
				</table>

				</td>
			</tr>
			<tr><td height="30"></td></tr>
<?if($print!="Y"){?>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 HEIGHT=45 ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 HEIGHT=45 ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<TD><IMG SRC="images/manual_top2.gif" WIDTH=18 HEIGHT=45 ALT=""></TD>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><FONT color=#3d3d3d>검색사이트에서 어떠한 키워드 검색을 통해서 방문하였는지 알 수 있습니다.</FONT></td>
					</tr>
					<tr><td colspan=2 height=5></td></tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>
							<FONT color=#3d3d3d>고객이 검색사이트에서 검색어에 따른 사이트 노출 분석을 할 수 있습니다.</FONT>
						</td>
					</tr>
					<tr><td colspan=2 height=5></td></tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>
							<FONT color=#3d3d3d>가장 많이 찾는 검색사이트에 해당 검색어의 키워드광고등을 통하여 동종사이트보다 노출 빈도를 많게 하여<br>
							광고효율을 극대화 시킬 수 있는 소중한 자료가 됩니다.</FONT>
						</td>
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
			<tr><td height="30"></td></tr>
<?}?>
			</table>
<?if($print!="Y"){?>
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

<form name=form2 method=post action="<?=$_SERVER[PHP_SELF]?>"  target=popviewprint>
<input type=hidden name=print>
<input type=hidden name=type value=<?=$type?>>
<input type=hidden name=searchdate value=<?=$searchdate?>>
</form>
</table>
<?=$onload?>
<? INCLUDE "copyright.php"; ?>
<?}?>