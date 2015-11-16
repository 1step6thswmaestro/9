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

function WriteEngine($engine, $file) {
	$filename = DirPath.DataDir."shopimages/etc/".$file;
	$success = false;

	if($fp=fopen($filename, "w")) {
		fputs($fp, serialize($engine));
		fclose($fp);
		$success=true;
	}
	return $success;
}

function ReadEngine($file) {
	$filename = DirPath.DataDir."shopimages/etc/".$file;

	if(file_exists($filename)==true) {
		if($fp=@fopen($filename, "r")) {
			$szdata=fread($fp, filesize($filename));
			fclose($fp);
			$engine=unserialize($szdata);
		}
	}
	return $engine;
}

$type=$_POST["type"];
$engine=$_POST["engine"];

if($type=="update") {
	$success = WriteEngine(&$engine, "engineinfo.db");

	if($success) {
		$onload="<script>alert('정상적으로 적용 됐습니다.');</script>";
	} else {
		$onload="<script>alert('예기치 못한 오류로 인해서 저장되지 못 했습니다.');history.go(-1);</script>";
	}
}

$engineval = ReadEngine("engineinfo.db");
?>

<? INCLUDE "header.php"; ?>
<script>try {parent.topframe.ChangeMenuImg(7);}catch(e){}</script>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">

function CheckForm(type) {
	if(confirm("가격비교페이지 관리 내용을 적용하겠습니까?"))
	{
		document.form1.type.value=type;
		document.form1.submit();
	}
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 마케팅지원 &gt; <span class="2depth_select">가격비교페이지 관리</span></td>
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
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_enginepage_title.gif" border="0"></TD>
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
					<TD width="100%" class="notice_blue">가격비교 서비스 업체에 제공할 상품 정보 페이지를 관리합니다.</TD>
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
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_enginepage_stitle1.gif" border="0"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
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
					<TD width="100%" class="notice_blue">가격비교 서비스 업체에 제공할 페이지를 선택해 주세요.</TD>
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
				<col width=20></col>
				<col width=30></col>
				<col width=150></col>
				<col width=></col>
				<col width=80></col>
				<TR>
					<TD colspan=5 background="images/table_top_line.gif"></TD>
				</TR>
				<TR align="center">
					<TD class="table_cell">No</TD>
					<TD class="table_cell1">사용</TD>
					<TD class="table_cell1">가격비교 업체명</TD>
					<TD class="table_cell1">가격비교 페이지 주소</TD>
					<TD class="table_cell1">미리보기</TD>
				</TR>
				<TR>
					<TD colspan=5 background="images/table_con_line.gif"></TD>
				</TR>
<?
				$colspan=5;
				
				$engine_unique = array("omi","naver","naversub","nawayo","yahoo","danawae","danawap","enuri","mymargin","bestbuyer","yavis","shopbinder","linkprice","plusmall","gaenawa");
				$engine_data = array(
				"오미"						=> "http://".$shopurl."shopping/omi_ufo.php",
				"네이버(전체)"				=> "http://".$shopurl."shopping/naver.php",
				"네이버(요약)"				=> "http://".$shopurl."shopping/naver_sub.php",
				"나와요"					=> "http://".$shopurl."shopping/nawayo.php",
				"야후"						=> "http://".$shopurl."shopping/yahoo.php",
				"다나와-가전,비가전"		=> "http://".$shopurl."shopping/danawa_elec.php",
				"다나와-PC"					=> "http://".$shopurl."shopping/danawa_pc.php",
				"에누리"					=> "http://".$shopurl."shopping/enuri.php",
				"마이마진"					=> "http://".$shopurl."shopping/mymargin.php",
				"베스트바이어"				=> "http://".$shopurl."shopping/bestbuyer.php",
				"야비스"					=> "http://".$shopurl."shopping/yavis.php",
				"샵바인더"					=> "http://".$shopurl."shopping/shopbinder.php",
				"링크프라이스"				=> "http://".$shopurl."shopping/linkprice.php",
				"플러스몰"					=> "http://".$shopurl."shopping/plusmall.php",
				"개나와(애견)"				=> "http://".$shopurl."shopping/gaenawa.php"
				);


				$cnt=0;
				while(list($key, $value) = each($engine_data)) {
					echo "<tr align=\"center\">\n";
					echo "	<td class=\"td_con2\">".($cnt+1)."</td>\n";
					echo "	<td class=\"td_con1\"><input type=\"checkbox\" name=\"engine[".$engine_unique[$cnt]."]\" value=\"checked\" ".$engineval[$engine_unique[$cnt]]."></td>\n";
					echo "	<td class=\"td_con1\">".$key."</td>\n";
					echo "	<td class=\"td_con1\" align=\"left\" style=\"".(strlen($engineval[$engine_unique[$cnt]])>0?"color:#00A0D5;font-weight:bold;":"")."\">&nbsp;&nbsp;".$value."</td>\n";
					echo "	<td class=\"td_con1\">&nbsp;".(strlen($engineval[$engine_unique[$cnt]])>0?"<a href=\"".$value."\" target=\"_blank\" style=\"".(strlen($engineval[$engine_unique[$cnt]])>0?"color:#00A0D5;font-weight:bold;":"")."\">[미리보기]</a>":"")."</td>\n";
					echo "</tr>\n";
					echo "<TR>\n";
					echo "	<TD colspan=".$colspan." background=\"images/table_con_line.gif\"></TD>\n";
					echo "</TR>\n";
					$cnt++;
				}
?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="5"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td align=center><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>
			<tr>
				<td height="30"></td>
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
						<td><span class="font_dotline">가격비교페이지 관리</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 사용에 체크된 가격비교 업체 페이지만 사용이 가능합니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 일부 가격비교 페이지의 경우 미리보기 데이타가 일부 출력이 안되는 부분이 있습니다. 서비스 이용과는 무관합니다.</td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top">- 가격비교 서비스는 해당 업체와 추가로 계약을 해야만 정상서비스가 이뤄집니다.</td>
					</tr>
					</table>
					</td>
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
			<tr><td height="50"></td></tr>
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