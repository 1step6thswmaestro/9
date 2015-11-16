<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "mo-1";
$MenuCode = "mobile";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

//$logoimagepath = $Dir.DataDir."shopimages/mobile/etc/";
//$directmenupath = $Dir.DataDir."shopimages/mobile/banner/";
$directmenupath = "../m/upload/";

$type=trim($_POST["type"]);

$up_logo=$_FILES["up_logo"];
$up_icon=$_FILES["up_icon"];
$up_image=$_FILES["up_image"];
$up_title=$_POST["up_title"];
$up_url_type=$_POST["up_url_type"];
$up_url=$_POST["up_url"];
$up_target=$_POST["up_target"];
$up_banner_loc=$_POST["up_banner_loc"];
$place=$_POST["place"];

$CurrentTime = date("YmdHis");

if ($type=="bannerdel") {
	if ($up_url) {
		$sql = "SELECT image FROM tblmobiledirectmenu ";
		$sql.= "WHERE date = '".$up_url."'";
		$result = mysql_query($sql,get_db_conn());
		
		if($row=mysql_fetch_object($result)) {
			if($row->image && file_exists($directmenupath.$row->image)) {
				@unlink($directmenupath.$row->image);
			}
		}
		mysql_free_result($result);
		$sql = "DELETE FROM tblmobiledirectmenu WHERE date = '".$up_url."'";
		mysql_query($sql,get_db_conn());
		$onload = "<script>alert('메뉴이미지 삭제가 완료되었습니다.');</script>";
	}
} else if ($type=="banneradd") {

	if($up_image[name] && $up_url) {

//		if (strpos($up_image[name],"html")==true || strpos($up_image[name],"php")==true || strpos($up_image[name],"htm"))  $up_image[name] = $up_image[name]."_";
	//	$banner_ext= strtolower(substr($up_image[name],-4));

		//if($banner_ext!=".gif" && $banner_ext!=".jpg" && $banner_ext!=".png"){
		//	$onload = "<script>alert (\"올리실 이미지는 gif파일만 가능합니다.\");</script>";
		//} else if ($up_image[size]>153600) {
		//	$onload = "<script>alert (\"올리실 이미지 용량은 150KB 이하의 파일만 가능합니다.\");</script>";
		//} else {
		$banner_ext= strtolower(substr($up_image[name],-4));
		if($banner_ext==".gif" || $banner_ext==".jpg" || $banner_ext==".png"){

			if($up_image[size]<153600){
				
				$writemode = false;
				$sql = "SELECT COUNT(*) as cnt FROM tblmobiledirectmenu ";
				$result = mysql_query($sql,get_db_conn());

				$row = mysql_fetch_object($result);

				mysql_free_result($result);
				$cnt=(int)$row->cnt;
				if(preg_match("/[\xA1-\xFE][\xA1-\xFE]/", $up_title)){
					if(strlen($up_title) <= 12){
						$writemode = true;
					}
				}else{
					if(strlen($up_title) <= 6){
						$writemode = true;
					}
				}
				if($writemode == true){
					if ($cnt<4) {
						$banner_name = "direct_menu_".date(His)."_".$up_image[name];
						move_uploaded_file($up_image[tmp_name],$directmenupath.$banner_name);
						chmod($directmenupath.$banner_name,0606);
						$sql = "INSERT tblmobiledirectmenu SET ";
						$sql.= "date		= '".$CurrentTime."', ";
						$sql.= "image		= '".$banner_name."', ";
						$sql.= "title		= '".substr($up_title,0,12)."', ";
						$sql.= "url_type	= '".$up_url_type."', ";
						$sql.= "url			= '".$up_url."', ";
						$sql.= "target		= '".$up_target."' ";

						//echo $sql;
						mysql_query($sql,get_db_conn());
						echo '<script>alert("메뉴이미지 등록이 완료되었습니다.");</script>';
					} else {
						echo '<script>alert("메뉴이미지 등록은 최대 4개까지만 등록이 가능합니다.");</script>';
					}

				}else{
				echo '<script>alert("타이틀은 글자수 6글자로 제한됩니다.");</script>';
				}
			}else{
				echo '<script>alert("업로드 파일 용량은 150KB로 제한됩니다.");</script>';
			}
		}else{
			echo '<script>alert("업로드 가능한 파일이 아닙니다.");</script>';
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
		$sql = "UPDATE tblmobiledirectmenu SET date='$date1$date' ";
		$sql.= "WHERE date = '".$banner[$i]."'";
		mysql_query($sql,get_db_conn());
	}
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">



function BannerDel(date) {
	if(confirm("메뉴이미지를 삭제하시겠습니까?")) {

		form2.type.value="bannerdel";
		form2.up_url.value = date;
		form2.submit();
	}
}



function BannerAdd() {
	if(!form2.up_image.value){
		alert('메뉴이미지를 등록하세요');
		form2.up_image.focus();
		return;
	}
	if(!form2.up_url.value){
		alert('메뉴이미지에 연결할 URL를 입력하세요. \n(예: www.abc.com)');
		form2.up_url.focus();
		return;
	}
	form2.type.value="banneradd";
	form2.submit();
}

function BannerSort(cnt){
	arr_sort = new Array();
	var val;
	for(i=1;i<=cnt;i++){
		val=form2.bannerplace[i].options[form2.bannerplace[i].selectedIndex].value;
		if (arr_sort[val]) {
			alert("순서가 중복되거나 잘못되었습니다.");
			return;
		} else {
			arr_sort[val] = form2.bannerdate[i].value;
		}
	}
	var result = arr_sort.join(",").substring(1);

	document.form2.place.value=result;
	document.form2.type.value="bannersort";
	document.form2.submit();
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
			<? include ("menu_mobile.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 모바일 &gt; <span class="2depth_select">메인 바로가기메뉴 설정</span></td>
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
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/mobile_main_direct_title.gif" border="0"></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN="2" background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">모바일 쇼핑몰 메인및 카테고리에 바로가기메뉴 아이콘을 등록하여 원하는 페이지로 링크(바로가기)하실 수 있습니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN="2" background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>




			<form name="form2" action="<?=$_SERVER[PHP_SELF]?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="type">
			<input type="hidden" name="place">
			<input type="hidden" name="bannerplace">
			<input type="hidden" name="bannerdate">

			<tr><td HEIGHT=30></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/mobile_main_direct_stitle01.gif"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td HEIGHT=3></td></tr>
			<tr>
				<td align="center">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td  bgcolor="#E8E8E8" style="padding:4pt;">
								<table cellpadding="0" cellspacing="0" width="100%" bgcolor="EFEFEF">
									<tr>
										<td width="100%" align="center">
										<div style="width:300px;padding:20px 0 20px 10px;background:#EFEFEF">
										<?
											$sql_t = "SELECT * FROM tblmobiledirectmenu ORDER BY date DESC";
											$result_t = mysql_query($sql_t,get_db_conn());
											while($row_t=mysql_fetch_object($result_t))
											{
												?>
												<div style="width:60px;height:60px;float:left;padding-right:10px"><a href="http://<?=$row_t->url?>" target="_blank"><img src="<?=$directmenupath.$row_t->image;?>" width=60 class="imgline1"></a></div>
												<?
											}
										?>
										</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td height="40"></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif"  colspan="4"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="33" align="center">순서</TD>
					<TD class="table_cell1" align="center" width="85">이미지</TD>
					<TD class="table_cell1" align="center">링크주소</TD>
					<TD class="table_cell1"  align="center" width="80">삭제</TD>
				</TR>
				<TR>
					<TD colspan="4"  background="images/table_con_line.gif"></TD>
				</TR>

<?
	$sql0 = "SELECT COUNT(*) as cnt FROM tblmobiledirectmenu ";
	$result = mysql_query($sql0,get_db_conn());
	$row = mysql_fetch_object($result);
	mysql_free_result($result);
	$cnt = $row->cnt;

	$sql = "SELECT * FROM tblmobiledirectmenu ORDER BY date DESC";
	$result = mysql_query($sql,get_db_conn());
	$count=1;
	while($row=mysql_fetch_object($result)){
		$image = $row->image;
		$url = $row->url;
?>


				<TR>
					<TD class="td_con" noWrap align="center">
					<select name=bannerplace class="select">
<?		for($i=1;$i<=$cnt;$i++){
			echo "<option value=\"".$i."\"";
			if($i==$count) echo " selected";
			echo ">".($i);
		}
?>
					</select><input type="hidden" name=bannerdate value="<?=$row->date?>"></TD>
					<TD class="td_con1" align="center"><img src="<?=$directmenupath.$image?>" width=60 class="imgline1"></TD>
					<TD class="td_con1">
					<?=$row->title?>
					<br />
					<a href=http<?=($row->url_type=="S"?"s":"")?>://<?=$url?> target="_blank"><font color=#0000a0>http<?=($row->url_type=="S"?"s":"")?>://<?=$url?></font></a></TD>
					<TD class="td_con1" align="center"><a href="javascript:BannerDel('<?=$row->date?>');"><img src="images/btn_del.gif" width="50" height="22" border="0"></a></TD>
				</TR>
				<TR>
					<TD colspan="4"  background="images/table_con_line.gif"></TD>
				</TR>
<?
		$count++;
	}
	mysql_free_result($result);
	if($cnt==0) {
		echo "<TR><td class=lineleft colspan=4 align=center><font color=#383838>등록된 메뉴이미지가 없습니다.</font></td></tr>";
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
				<td height="30"></td>
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
					<TD width="100%" class="notice_blue">1) <b>GIF(gif), JPG(jpg), PNG(png)파일만</b> 등록 가능합니다.<br>
					2) 등록 이미지의 사이즈는 가로 50px, 세로 50px를 권장하며 이와 상이 할 경우 가로 50px로 제한됩니다.</TD>
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
					<td  bgcolor="#ededed" style="padding:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD  colspan="2" height="35" background="images/blueline_bg.gif"><p align="center"><b><font color="#333333">메뉴이미지등록하기</font></b></TD>
						</TR>
						<TR>
							<TD colspan="2"  background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD width="148" class="table_cell"><b><img src="images/icon_point2.gif" width="8" height="11" border="0"></b>메뉴 이미지</TD>
							<TD width="596" class="td_con1"><input type=file name=up_image style="WIDTH: 98%" class="input"></TD>
						</TR>
						<TR>
							<TD colspan="2"  background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD width="148" class="table_cell"><b><img src="images/icon_point2.gif" width="8" height="11" border="0"></b>타이틀</TD>
							<TD width="596" class="td_con1"><input type=text name=up_title style="WIDTH: 150px" class="input"></TD>
						</TR>
						<TR>
							<TD colspan="2"  background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD width="148" class="table_cell"><b><img src="images/icon_point2.gif" width="8" height="11" border="0"></b>연결 URL</TD>
							<TD width="596" class="td_con1">
								http://
							 <input type=text name="up_url" value="" size="72"  onKeyUp="chkFieldMaxLen(200)" class="input" ></TD>
						</TR>
						<TR>
							<TD colspan="2"  background="images/table_con_line.gif"></TD>
						</TR>

						<input type="hidden" name="up_target" value="_self" />


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
			</form>

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
					<TD COLSPAN=3 width="100%" valign="top" class="menual_bg" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
					<col width=20></col>
					<col width=></col>
					<tr>
						<td align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><span class="font_dotline">바로가기 메뉴 설정</span></td>
					</tr>
					<tr>
						<td align="right">&nbsp;</td>
						<td class="space_top" style="letter-spacing:-0.5pt;">
						- 모바일샵 메인과, 카테고리에 노출됩니다.<br/>
						- 기본 가운데 왼쪽 됩니다.<br/>
						- 최대 4개 까지 등록하실 수 있습니다<br/>
						- 등록 가능한 이미지 용량 크기의 경우 150KB 까지 가능합니다 <br/>
						- 업로드 가능한 파일의 경우 파일포멧은 *.jpg, *.png, *.gif 파일만 가능합니다<br/>
						- 타이틀은 최대 6글자까지 작성가능합니다.<br/>
						</td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>

					</table>
					</TD>
					<TD background="images/manual_right1.gif"><IMG SRC="images/manual_right1.gif" WIDTH=18 HEIGHT="2" ALT=""></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
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



<? INCLUDE "copyright.php"; ?>