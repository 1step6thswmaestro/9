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

$type=$_POST["type"];
$searchdate=$_POST["searchdate"];
$print=$_POST["print"];

if(strlen($type)==0) $type="d";
if(strlen($searchdate)==0) $searchdate=date("Ymd");
if($type!="m" && strlen($searchdate)!=8) $searchdate=date("Ymd");
if($type=="d" && $searchdate==date("Ymd")) $timeview="NO";

$month= date("m");
$len=30;
if($type=="d") {
	$sql ="SELECT b.enginename,a.cnt as cnt,b.domain FROM tblcountersearchengine a,tblcountersearchdomain b ";
	$sql.="WHERE a.date='".$searchdate."' AND b.domain=a.domain ORDER BY cnt DESC";
} else if($type=="w") {
	$prevdate=date("Ymd",mktime(0,0,0,date("m"),date("d")-7,date("Y")));
	$sql ="SELECT SUM(a.cnt) as cnt,b.enginename,b.domain FROM tblcountersearchengine a,tblcountersearchdomain b ";
	$sql.="WHERE (a.date<='".$searchdate."' AND a.date>='".$prevdate."') AND b.domain=a.domain ";
	$sql.="GROUP BY b.enginename ORDER BY cnt DESC";
} else if($type=="m") {
	$date=substr($searchdate,0,6);
	if($searchdate>=date("Ym",mktime(0,0,0,date("m")-1,date("d"),date("Y")))) {
		$sql ="SELECT SUM(a.cnt) as cnt,b.enginename,b.domain FROM tblcountersearchengine a,tblcountersearchdomain b ";
		$sql.="WHERE a.date LIKE '".$date."%' AND b.domain=a.domain GROUP BY b.enginename ORDER BY cnt DESC";
	} else {
		$sql ="SELECT a.cnt,b.enginename,b.domain FROM tblcountersearchenginemonth a,tblcountersearchdomain b ";
		$sql.="WHERE a.date='".$date."' AND b.domain=a.domain ORDER BY cnt DESC";
	}
}

$sum=0;
$result = mysql_query($sql,get_db_conn());
$i=0;
while($row = mysql_fetch_object($result)){
	$time[$i]=$row->cnt;
	$domains[$i]=$row->domain;
	$page[$i++]=$row->enginename;
	if($max<$row->cnt) $max=$row->cnt;
	$sum+=$row->cnt;
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 통계분석 &gt; 외부 접근 경로 분석 &gt; <span class="2depth_select">검색엔진별 접근순위</span></td>
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
					<TD><IMG SRC="images/counter_searchenginerank_title.gif" ALT=""></TD>
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
						if($timeview=="NO") {
							echo "* <b><font color=\"#FF6633\">".date("Y년 m월 d일 H시 i분")."</font></b> 현재";
						} else {
							echo "* <b><font color=\"#FF6633\">".substr($searchdate,0,4)."년 ".substr($searchdate,4,2)."월 ".($type!="m"?substr($searchdate,6,2)."일":"")."</font></b> 전체";
						}
						echo " 검색엔진별 접근순위 입니다.";
?>
						</td>
						<td align=right>
						<A HREF="javascript:search_date('d')"><img src="images/counter_tab_day_<?=($type=="d"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						<A HREF="javascript:search_date('w')"><img src="images/counter_tab_week_<?=($type=="w"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						<A HREF="javascript:search_date('m')"><img src="images/counter_tab_month_<?=($type=="m"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<col width=60></col>
					<col width=></col>
					<col width=120></col>
					<col width=120></col>
					<tr><td colspan=4 height=2 bgcolor=#000000></td></tr>
					<TR>
						<TD class="table_cell" align=center><FONT color=#3d3d3d>NO</FONT></TD>
						<TD class="table_cell" align=center><FONT color=#3d3d3d>검색엔진명</FONT></TD>
						<TD class="table_cell" align=center><FONT color=#3d3d3d>방문자수</FONT></TD>
						<TD class="table_cell" align=center><FONT color=#3d3d3d>퍼센트</FONT></TD>
					</TR>
					<tr><td colspan=4 height=1 bgcolor=#EDEDED></td></tr>
<?
					$len=count($time); 
					for($i=0;$i<$len;$i++){
						$percent[$i]=$time[$i]/$sum*100;
						if($pos=strpos($percent[$i],".")) {
							$percent[$i]=substr($percent[$i],0,$pos+3);
						}
						echo "<tr>\n";
						echo "	<TD class=\"td_con2a\" align=center>".($i+1)."</td>\n";
						echo "	<TD class=\"td_con2a\" style=\"padding-left:5\">".$page[$i]." (".$domains[$i].")</td>\n";
						echo "	<TD class=\"td_con2a\" align=center><FONT color=\"#00769D\">".number_format($time[$i])."명</FONT></td>\n";
						echo "	<TD class=\"td_con2a\" align=center>".$percent[$i]."%</td>\n";
						echo "</tr>\n";
						echo "<tr><td colspan=4 height=1 bgcolor=#EDEDED></td></tr>\n";
					}
					if($len==0){
						echo "<tr bgcolor=#FFFFFF><td colspan=4 height=30 class=\"td_con2a\" align=center><font color=#3D3D3D>해당 자료가 없습니다.</font></td></tr>\n";
						echo "<tr><td colspan=4 height=1 bgcolor=#EDEDED></td></tr>\n";
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
						<?if($type=="d") {?>
						지난 접속통계 
						<select name=searchdate onchange="search_date('d')">
<?
						for($i=59;$i>=0;$i--) {
							$date=date("Ymd",mktime(0,0,0,date("m"),date("d")-$i,date("Y")));
							echo "<option value=\"".$date."\"";
							if($date==$searchdate) echo " selected";
							echo ">".substr($date,0,4)."년 ".substr($date,4,2)."월 ".substr($date,6,2)."일</option>\n";
						}
?>
						</select>
						<?}?>
						<?if($type=="m") {?>
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
						<?}?>
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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"
 class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><FONT color=#3d3d3d>당사 쇼핑몰을 외부에서 찾아 오는 경로중 검색엔진기준으로 순위를 알 수 있습니다.</FONT></td>
					</tr>
					<tr><td colspan=2 height=5></td></tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>
							<FONT color=#3d3d3d>수많은 검색엔진중에서 누가 가장 많이 당사 쇼핑몰에 고객을 보내주는지 분석할 수 있습니다.</FONT>
						</td>
					</tr>
					<tr><td colspan=2 height=5></td></tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>
							<FONT color=#3d3d3d>요즘 검색엔진(포탈사이트)에 쇼핑몰을 등록하기 위해서는 비용을 부담해야 합니다.<br>
							일단 가장 기본이 되는 카테고리 등록을 한 후에 가장 많은 고객을 보내 주는 포탈에 대해서는<br>
							보다 적극적인 마케팅(키워드 광고 등)을 전개할 수 있습니다.
							<br>
							또한, 유명 포탈사이트중 당사 쇼핑몰의 노출이 많이 없는 경우에 대한 대책도
							강구할 수 있습니다.</FONT>
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