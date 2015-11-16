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

$year=substr($searchdate,0,4);
$mon=substr($searchdate,4,2);
$day=substr($searchdate,6,2);
$prevdate=date("Ymd",mktime(0,0,0,$mon,$day-1,$year));
$nextdate=date("Ymd",mktime(0,0,0,$mon,$day+1,$year));

?>


<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function search_date(type) {
	document.form1.type.value=type;
	document.form1.submit();
}

function change_date(gbn) {
	if(gbn=="P") {
		document.form1.searchdate.value="<?=$prevdate?>";
	} else if(gbn=="N") {
<?if($searchdate<date("Ymd")){?>
		document.form1.searchdate.value="<?=$nextdate?>";
<?}else {?>
		alert("가장 최근 목록 자료입니다.");
		return;
<?}?>
	}
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 통계분석 &gt; 그래프로 보는 통계분석 &gt; <span class="2depth_select">시간별 전체 접속통계</span></td>
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
					<TD><IMG SRC="images/counter_timetotal_title.gif"  ALT=""></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td align=center>

				<table cellpadding="0" cellspacing="0" width="100%">
				<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
				<input type=hidden name=type>
				<input type=hidden name=searchdate value="<?=$searchdate?>">
				<input type=hidden name=print value="<?=$print?>">
				<tr>
					<td align=center>
						<A HREF="javascript:search_date('d')"><img src="images/counter_tab_day_<?=($type=="d"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						<A HREF="javascript:search_date('w')"><img src="images/counter_tab_week_<?=($type=="w"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						<A HREF="javascript:search_date('m')"><img src="images/counter_tab_month_<?=($type=="m"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
					</td>
				</tr>
				<tr>
					<td align=center>

					<TABLE cellSpacing=0 cellPadding=0 width="85%" align="center">
					<TR>
						<TD 
						style="padding:4pt;" 						 bgColor=#ededed>
						<TABLE cellSpacing=0 cellPadding=0 width="100%" bgColor=white>
						<TR>
							<TD width="100%">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD style="padding-top:3pt; padding-bottom:3pt;">
								<TABLE cellSpacing=0 cellPadding=0 width="320" height=32 align=center>
								<TR>
									<?if($type=="d"){?>
									<TD><A HREF="javascript:change_date('P')"><IMG height=31 src="images/counter_btn_back.gif" width=31 border=0></A></TD>
									<?}?>
									<TD>
										<P style="LINE-HEIGHT: 200%" align=center><B><SPAN class=font_orange 
										style="font-size:13pt; letter-spacing:-1pt;">
										<?
										if($searchdate==date("Ymd") || $type=="w" || $type=="m") {
											echo date("Y년 m월 d일 H시 i분 현재");
										} else {
											echo $year."년 ".$mon."월 ".$day."일";
										}
										?>
										</SPAN></B></P>
									</TD>
									<?if($type=="d"){?>
									<TD><A HREF="javascript:change_date('N')"><IMG height=31 src="images/counter_btn_next.gif" width=31 border=0></A></TD>
									<?}?>
								</TR>
								</TABLE>
								</TD>
							</TR>
							</TABLE>
							</TD>
						</TR>
						</TABLE>
						</TD>
					</TR>
					</TABLE>

					</td>
				</tr>
				<tr><td height=15></td></tr>
				<tr>
					<td align=center><img src="graph/timetotal.php?type=<?=$type?>&date=<?=$searchdate?>"></td>
				</tr>
				<?if($print!="Y"){?>
				<tr><td height=10></td></tr>
				<TR>
					<TD width="100%" background="images/counter_blackline_bg.gif" height="30" align=right>
					<table cellpadding="0" cellspacing="0">
					<tr>
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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"  class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><FONT color=#3d3d3d>시간 흐름(일/주간/월간)에 따른 순방문자/페이지뷰/주문시도건수를 그래프로 한눈에 볼 수 있습니다.</FONT></td>
					</tr>
					<tr><td colspan=2 height=5></td></tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>
							<FONT color=#3d3d3d>시간 흐름에 따른 쇼핑몰 중요 데이터를 그림으로 쉽게 분석할 수 있습니다.</FONT>
						</td>
					</tr>
					<tr><td colspan=2 height=5></td></tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td>
							<FONT color=#3d3d3d>하루 하루 나타나는 데이터를 출력하여 모아 놓으면, 아주 소중한 쇼핑몰 운영가이드책이 될 수 있습니다.</FONT>
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