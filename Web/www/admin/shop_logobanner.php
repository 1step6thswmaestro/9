<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-2";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$logoimagepath = $Dir.DataDir."shopimages/etc/";
$bannerimagepath = $Dir.DataDir."shopimages/banner/";

$type=$_POST["type"];
$up_logo=$_FILES["up_logo"];
$up_image=$_FILES["up_image"];
$up_border=$_POST["up_border"];
$up_url_type=$_POST["up_url_type"];
$up_url=$_POST["up_url"];
$up_target=$_POST["up_target"];
$up_banner_loc=$_POST["up_banner_loc"];
$place=$_POST["place"];

$CurrentTime = date("YmdHis");

if ($type=="up") {
	if ($up_logo[name]) {
		if (strtolower(substr($up_logo[name],strlen($up_logo[name])-3,3))!="gif") {
			$onload = "<script>alert (\"올리실 이미지는 gif파일만 가능합니다.\");</script>";
		} else if ($up_logo[size]>153600) {
			$onload = "<script>alert (\"올리실 이미지 용량은 150KB 이하의 파일만 가능합니다.\");</script>";
		} else {
			move_uploaded_file($up_logo[tmp_name],$logoimagepath."logo.gif"); 
			chmod($logoimagepath."logo.gif",0606);
			$onload = "<script>alert('쇼핑몰 로고 등록이 완료되었습니다.');</script>";
		}
	}
	if ($up_banner_loc) {
		$sql = "UPDATE tblshopinfo SET banner_loc='".$up_banner_loc."' ";
		mysql_query($sql,get_db_conn());
		DeleteCache("tblshopinfo.cache");
		if(strlen($onload)==0) {
			$onload = "<script>alert('정보 수정이 완료되었습니다.');</script>";
		}
	}
} else if ($type=="logodel") {
	unlink($logoimagepath."logo.gif");
	$onload="<script>alert ('쇼핑몰 로고 삭제가 완료되었습니다.');</script>";
} else if ($type=="bannerdel") {
	if ($up_url) {
		$sql = "SELECT image FROM tblbanner ";
		$sql.= "WHERE date = '".$up_url."'";
		$result = mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			if($row->image && file_exists($bannerimagepath.$row->image)) {
				unlink($bannerimagepath.$row->image);
			}
		}
		mysql_free_result($result);
		$sql = "DELETE FROM tblbanner WHERE date = '".$up_url."'";
		mysql_query($sql,get_db_conn());
		$onload = "<script>alert('배너 삭제가 완료되었습니다.');</script>";
	}
} else if ($type=="banneradd") {
	if($up_image[name] && $up_url) {
		if (strpos($up_image[name],"html")==true || strpos($up_image[name],"php")==true || strpos($up_image[name],"htm")) $up_image[name] = $up_image[name]."_";
		$banner_ext= strtolower(substr($up_image[name],-4));
		if($banner_ext!=".gif" && $banner_ext!=".jpg" && $banner_ext!=".png"){
			$onload = "<script>alert (\"올리실 이미지는 gif파일만 가능합니다.\");</script>";
		} else if ($up_image[size]>153600) {
			$onload = "<script>alert (\"올리실 이미지 용량은 150KB 이하의 파일만 가능합니다.\");</script>";
		} else {
			$sql = "SELECT COUNT(*) as cnt FROM tblbanner ";
			$result = mysql_query($sql,get_db_conn());
			$row = mysql_fetch_object($result);
			mysql_free_result($result);
			$cnt=(int)$row->cnt;
			if ($cnt<10) {
				$banner_name = $up_image[name];
				move_uploaded_file($up_image[tmp_name],$bannerimagepath.$banner_name); 
				chmod($bannerimagepath.$banner_name,0606);
				$sql = "INSERT tblbanner SET ";
				$sql.= "date		= '".$CurrentTime."', ";
				$sql.= "image		= '".$banner_name."', ";
				$sql.= "border		= '".$up_border."', ";
				$sql.= "url_type	= '".$up_url_type."', ";
				$sql.= "url			= '".$up_url."', ";
				$sql.= "target		= '".$up_target."' ";
				mysql_query($sql,get_db_conn());
				$onload="<script>alert('배너 등록이 완료되었습니다.');</script>";
			} else {
				$onload="<script>alert('배너 등록은 최대 10개까지만 등록이 가능합니다.');</script>";
			}
		}
	}
} else if ($type=="bannersort") {
	$banner=explode(",",$place);
	$date1=date("Ym");
	$date=date("dHis");
	for($i=0;$i<count($banner);$i++){
		$date--;
		if (strlen($date)==7) $date="0".$date;
		else if (strlen($date)==6) $date="00".$date;
		$sql = "UPDATE tblbanner SET date='$date1$date' ";
		$sql.= "WHERE date = '".$banner[$i]."'";
		mysql_query($sql,get_db_conn());
	}
}

$sql = "SELECT banner_loc FROM tblshopinfo ";
$result = mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$banner_loc = $row->banner_loc;
}
mysql_free_result($result);
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script>
function CheckForm(type) {
	if (type=="logodel") {
		if (!confirm("쇼핑몰 로고를 삭제하시겠습니까?")) {
			return;
		}
	}
	form1.type.value=type;
	form1.submit();
}

function BannerDel(date) {
	if(confirm("배너를 삭제하시겠습니까?")) {
		form1.type.value="bannerdel";
		form1.up_url.value = date;
		form1.submit();
	}
}

function BannerAdd() {
	if(!form1.up_image.value){
		alert('배너 이미지를 등록하세요');
		form1.up_image.focus();
		return;
	}
	if(!form1.up_url.value){
		alert('배너에 연결할 URL를 입력하세요. \n(예: www.abc.com)');
		form1.up_url.focus();
		return;
	}
	form1.type.value="banneradd";
	form1.submit();
}

function BannerSort(cnt){
	arr_sort = new Array();
	var val;
	for(i=1;i<=cnt;i++){
		val=form1.bannerplace[i].options[form1.bannerplace[i].selectedIndex].value;
		if (arr_sort[val]) {
			alert("배너 순서가 중복되거나 잘못되었습니다.");
			return;
		} else {
			arr_sort[val] = form1.bannerdate[i].value;
		}
	}
	var result = arr_sort.join(",").substring(1);

	document.form1.place.value=result;
	document.form1.type.value="bannersort";
	document.form1.submit();
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
			<? include ("menu_shop.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 쇼핑몰 환경 설정 &gt; <span class="2depth_select">로고/배너 관리</span></td>
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
					<TD><IMG SRC="images/shop_logobanner_title.gif"  ALT=""></TD>
					</tr>
<tr>
<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
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
					<TD width="100%" class="notice_blue"><p>쇼핑몰의 로고 및 배너를 등록/관리하실 수 있습니다.</p></TD>
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
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_logobanner_stitle1.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=place>
			<input type=hidden name=bannerplace>
			<input type=hidden name=bannerdate>
			<tr>
				<td style="padding-top:3pt;">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD align=left width="745" bgColor=#ffffff colspan="2">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td class="table_cell" valign="top" width="523"><p><img src="images/shop_logobanner_img1.gif" width="177" height="149" border="0"></p></td>
						<td style="border-left-width:1pt; border-color:rgb(243,243,243); border-left-style:solid;" width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell" width="100%"><img src="images/icon_point2.gif" width="8" height="11" border="0"><b>로고이미지 업로드</b></TD>
						</TR>
						<TR>
							<TD width="100%" background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD  style="PADDING-RIGHT: 5px; PADDING-LEFT: 10px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=left width="100%" bgColor=#ffffff><input type=file name=up_logo style="WIDTH: 100%" class="input"><br>* 로고 이미지 크기는 180X65 사이즈의 GIF파일로 제작하세요.</TD>
						</TR>
						<TR>
							<TD class=linebottomleft style="PADDING-RIGHT: 5px; PADDING-LEFT: 10px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=left width="100%" bgColor=#ffffff><p>
							<? if (file_exists($logoimagepath."logo.gif")==true) {?>
							<img src="<?=$logoimagepath?>logo.gif" border=0 style="border-width:1pt; border-color:rgb(235,235,235); border-style:solid;"> <a href="javascript:CheckForm('logodel');"><img src="images/btn_del.gif" width="50" height="22" border="0" hspace="3"></a>
							<? } else { ?>
							등록된 로고가 없습니다.
							<? } ?></p></TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('up');"><img src="images/botteon_save.gif" width="113" height="38" border="0" vspace="3"></a></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_logobanner_stitle2.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td HEIGHT=3></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif"  colspan="4"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="33"><p align="center">순서</TD>
					<TD class="table_cell1"><p align="center">배너이미지</TD>
					<TD class="table_cell1" width="439"><p align="center">링크주소</TD>
					<TD class="table_cell1" width="80"><p align="center">삭제</TD>
				</TR>
				<TR>
					<TD colspan="4"  background="images/table_con_line.gif"></TD>
				</TR>
				
<?
	$sql0 = "SELECT COUNT(*) as cnt FROM tblbanner ";
	$result = mysql_query($sql0,get_db_conn());
	$row = mysql_fetch_object($result);
	mysql_free_result($result);
	$cnt = $row->cnt;

	$sql = "SELECT * FROM tblbanner ORDER BY date DESC";
	$result = mysql_query($sql,get_db_conn());
	$count=1;
	while($row=mysql_fetch_object($result)){
		$image = $row->image;
		$url = $row->url;
?>
				<TR>
					<TD class="td_con" noWrap align=middle width=60><p align="center">
					<select name=bannerplace class="select">
<?		for($i=1;$i<=$cnt;$i++){
			echo "<option value=\"".$i."\"";
			if($i==$count) echo " selected";
			echo ">".($i);
		}
?>
					</select><input type=hidden name=bannerdate value="<?=$row->date?>"></TD>
					<TD class="td_con1" width="151"><img src="<?=$bannerimagepath.$image?>" border="<?=$row->border?>" width=200 class="imgline"></TD>
					<TD class="td_con1" width="447"> <a href=http<?=($row->url_type=="S"?"s":"")?>://<?=$url?> target=<?=$row->target?>><font color=#0000a0>http<?=($row->url_type=="S"?"s":"")?>://<?=$url?></font></a></TD>
					<TD class="td_con1" width="88"><p align="center"><a href="javascript:BannerDel('<?=$row->date?>');"><img src="images/btn_del.gif" width="50" height="22" border="0"></a></p></TD>
				</TR>
				<TR>
					<TD colspan="4"  background="images/table_con_line.gif"></TD>
				</TR>
<?
		$count++;
	}
	mysql_free_result($result);
	if($cnt==0) {
		echo "<TR><td class=lineleft colspan=4 align=center><font color=#383838>등록된 배너가 없습니다.</font></td></tr>";
	}
?>
				
				</TABLE>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:0pt;">
<?
	if ($cnt > 0) {
		echo "<a href=\"javascript:BannerSort('$cnt');\"><img src=\"images/icon_sort1.gif\" border=\"0\"></a>\n";
	}
?>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">	
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue" valign="top"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue"><p>1) <b>GIF(gif), JPG(jpg), PNG(png)파일만</b> 등록 가능합니다.<br>
					2) 배너위치가 <b>좌측 하단일 경우 가로 200픽셀</b>을 권장, <b>우측 상단일 경우 가로 180픽셀</b> 권장(세로사이즈 제한 없음).</b><br>3) 이미지 용량 150KB 이하.</p></TD>
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
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td  bgcolor="#EDEDED" style="padding:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD  colspan="2" height="35" background="images/blueline_bg.gif"><p align="center"><b><font color="#333333">배너등록하기</font></b></TD>
						</TR>
						<TR>
							<TD colspan="2"  background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD width="148" class="table_cell"><b><img src="images/icon_point2.gif" width="8" height="11" border="0"></b>배너 이미지</TD>
							<TD  class="td_con1"><input type=file name=up_image style="WIDTH:384px;" class="input"></TD>
						</TR>
						<TR>
							<TD colspan="2"  background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD width="148" class="table_cell"><b><img src="images/icon_point2.gif" width="8" height="11" border="0"></b>연결 URL</TD>
							<TD  class="td_con1"><select name=up_url_type class="select">
								<option value="H">http://
								<option value="S">https://
							</select> <input type=text name=up_url size=50 maxlength=200 onKeyUp="chkFieldMaxLen(200)" class="input" ></TD>
						</TR>
						<TR>
							<TD colspan="2"  background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD width="148" class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">Target 및 Border</TD>
							<TD  class="td_con1">
							Target : <select name=up_target class="select">
<? 
	$target=array("_blank","_top","_parent","_self");
	for($i=0;$i<4;$i++){
		echo "<option value=\"".$target[$i]."\">".$target[$i];
	}
?>
							</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Border : <select name=up_border class="select">
<?
	for($i=0;$i<5;$i++){
		echo "<option value=\"".$i."\">".$i;
	}
?>
							</select>
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
			<tr>
				<td style="padding-top:3pt;"><p align="center"><a href="javascript:BannerAdd();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/shop_logobanner_stitle3.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td HEIGHT=3></td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD align=left width="745" bgColor=#ffffff colspan="2">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="150" class="table_cell"><p><img src="images/shop_logobanner_img2.gif" width="177" height="149" border="0"></p></td>
						<td width="560" class="td_con1">
							<input type=radio id="idx_banner_loc1" name=up_banner_loc value="L" <? if ($banner_loc=="L") echo "checked"; ?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_banner_loc1>좌측 하단</label>
							<!--<br><input type=radio id="idx_banner_loc2" name=up_banner_loc value="R" <? if ($banner_loc=="R") echo "checked"; ?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_banner_loc2><font color=#0256A8>②</font> 오른쪽 상단</label>-->
						</td>
					</tr>
					</table>
					</TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('up');"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>
			<tr>
				<td height="25">&nbsp;</td>
			</tr>
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
						<td ><span class="font_dotline">배너 개별디자인</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">
						- <a href="javascript:parent.topframe.GoMenu(2,'design_eachleftmenu.php');"><span class="font_blue">디자인관리 > 개별디자인 - 메인 및 상하단 > 왼쪽메뉴 꾸미기</span></a> 에서 직접 HTML로 디자인할 수 있습니다.<br>
						- <a href="javascript:parent.topframe.GoMenu(2,'design_easyleft.php');"><span class="font_blue">디자인관리 > Easy 디자인 관리 > Easy 왼쪽 메뉴 관리</span></a> 에서 직접 HTML로 디자인할 수 있습니다.</a>
						</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">&nbsp; </td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">Target 과 Bordor (새창과 이미지외곽 테두리)</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">
						- <b>Target</b><b>&nbsp;</b>: 정보를 출력할 윈도우나 프레임을 입력하는 속성.<br>
						&nbsp;&nbsp;&nbsp;<span class="font_orange">_blank</span> <b>&nbsp;</b>: 연결된 문서를 읽어 새로운 빈 윈도우에 표시한다.<br>
						&nbsp;&nbsp;&nbsp;<span class="font_orange">_top</span> &nbsp;&nbsp;<b>&nbsp;&nbsp;</b>: 연결된 문서를 읽어 최상위 윈도우에 표시한다.<br>
						&nbsp;&nbsp;&nbsp;<span class="font_orange">_parent</span> : 연결된 문서를 읽어 바로 위 부모창에 표시한다.<br>
						&nbsp;&nbsp;&nbsp;<span class="font_orange">_self</span> <b>&nbsp;&nbsp;&nbsp;</b>: 연결된 문서를 읽어 현재창에 표시한다.<br>
						<br>
						- <b>Border</b> : 이미지 외곽에 border 값만큼 두께의 테두리 라인이 생성됩니다.
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