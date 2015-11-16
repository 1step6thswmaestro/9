<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-1";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$up_shop_intro=$_POST["up_shop_intro"];
$up_image=$_FILES["up_image"];
$up_flash=$_FILES["up_flash"];

$imagepath = $Dir.DataDir."shopimages/etc/";

if ($type=="up") {
	if ($up_image[name]) {
		if (strtolower(substr($up_image[name],strlen($up_image[name])-3,3))!="gif") {
			$onload = "<script>alert (\"올리실 이미지는 gif파일만 가능합니다.\");</script>";
		} else if ($up_image[size]>153600) {
			$onload = "<script>alert (\"올리실 이미지 용량은 150KB 이하의 파일만 가능합니다.\");</script>";
		} else {
			$image_name="main_logo.gif";
			move_uploaded_file($up_image[tmp_name],"$imagepath$image_name");
			chmod("$imagepath$image_name",0606);
		}
	}

	if ($up_flash[name]) {
		if (strtolower(substr($up_flash[name],strlen($up_flash[name])-3,3))!="swf") {
			$onload = "<script>alert (\"올리실 플래쉬 파일은 swf파일만 가능합니다.\");</script>";
		} else if ($up_flash[size]>204800) {
			$onload = "<script>alert (\"올리실 플래쉬 파일의 용량은 200KB 이하의 swf파일만 가능합니다.\");</script>";
		} else {
			$flash_name="main_logo.swf";
			move_uploaded_file($up_flash[tmp_name],"$imagepath$flash_name");
			chmod("$imagepath$flash_name",0606);
		}
	}
} else if ($type=="img" && file_exists($imagepath."main_logo.gif")) {
	$img_url=$imagepath."main_logo.gif";
	unlink("$img_url");
	$onload = "<script>alert (\"이미지가 삭제되었습니다.\");</script>";
}else if ($type=="flash" && file_exists($imagepath."main_logo.swf")) {
	$img_url=$imagepath."main_logo.swf";
	unlink("$img_url");
	$onload = "<script>alert (\"플래쉬 파일이 삭제되었습니다.\");</script>";
}

if ($type=="up") {
	if (strlen($up_shop_intro) <= 10000) {
		$sql = "SELECT shop_intro FROM tblshopinfo ";
		$result = mysql_query($sql,get_db_conn());
		$flag = (boolean)mysql_num_rows($result);
		mysql_free_result($result);
		$sql = "UPDATE tblshopinfo SET ";
		$sql.= "shop_intro	= '".$up_shop_intro."' ";
		$update = mysql_query($sql,get_db_conn());
		DeleteCache("tblshopinfo.cache");

		if (!$onload) {
			$onload = "<script> alert('정보 수정이 완료되었습니다.'); </script>";
		}
	}
}

$sql = "SELECT shop_intro FROM tblshopinfo ";
$result = mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$shop_intro = $row->shop_intro;
}
mysql_free_result($result);

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script>
function CheckForm(gbn) {
	var form = document.form1;
	if(CheckLength(form.up_shop_intro)>10000){
		alert('메인 소개글은 한글5000자, 영문10000자 까지 입력 가능합니다.\n\n다시 확인하시기 바랍니다.');
		form.up_shop_intro.focus();
		return;
	}
	form.type.value=gbn;
	form.submit();
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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 상점 기본정보 설정 &gt; <span class="2depth_select">메인 타이틀 이미지 디자인</span></td>
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








			<table width="100%" cellpadding="0" cellspacing="0">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="images/shop_mainintro_title.gif" border="0"></td>
					</tr><tr>
					<td width="100%" background="images/title_bg.gif" height=21></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="images/shop_mainintro_stitle1.gif" border="0"></td>
					<td width="100%" background="images/shop_basicinfo_stitle_bg.gif"></td>
					<td><img src="images/shop_basicinfo_stitle_end.gif" border="0"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="images/distribute_01.gif" border="0"></td>
					<td COLSPAN="2" background="images/distribute_02.gif"></td>
					<td><img src="images/distribute_03.gif" border="0"></td>
				</tr>
				<tr>
					<td background="images/distribute_04.gif"></td>
					<td class="notice_blue"><img src="images/distribute_img.gif" border="0"></td>
					<td width="100%" class="notice_blue">1) 쇼핑몰 메인 중앙의 타이틀 이미지 디자인 영역을 관리합니다.<br>
					2) 이미지는 <b>한글, 특수문자, 공백 사용금지</b></span>.<br>
					3) 기존이미지는 삭제하지 않더라도 교체되면서 자동삭제됩니다.
					</td>
					<td background="images/distribute_07.gif"></td>
				</tr>
				<tr>
					<td><img src="images/distribute_08.gif" border="0"></td>
					<td COLSPAN="2" background="images/distribute_09.gif"></td>
					<td><img src="images/distribute_10.gif" border="0"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<col width="140"></col>
				<col width=""></col>
				<tr>
					<td height="1" colspan="2" bgcolor="#B9B9B9"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">이미지 등록</td>
					<td class="td_con1"><input type=file name="up_image" class="input" <? if (file_exists($imagepath."main_logo.gif")) { ?> style="width:82%"> <input type=button value="이미지삭제" onClick="CheckForm('img');" class="submit1"><?}else{?> style="width:500px"><?}?><br>
					* 등록 가능한 이미지는 파일 확장자 <span class="font_orange2">GIF(gif)</span> 만 가능하며 용량은 <span class="font_orange2">최대 150KB</span> 까지 가능합니다.<br>
					* 이미지 등록 후 디자인 입력란에 <span class="font_orange2">매크로명령어 [MAINIMG]</span>를 입력하면 화면에 출력됩니다.</td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<? if (file_exists($imagepath."main_logo.gif")) { ?>
				<?
					$width = getimagesize($imagepath."main_logo.gif");
					$imgwidth = $width[0];
					if ($imgwidth>=505) $imgwidth = 505;
				?>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">등록된 이미지 보기</td>
					<td class="td_con1"><img src="<?=$imagepath?>main_logo.gif" border="0" align="absmiddle" width="<?=$imgwidth?>"></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<? } ?>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">플래시 등록</td>
					<td class="td_con1"><input type=file name="up_flash" class="input" <? if (file_exists($imagepath."main_logo.swf")) { ?> style="width:82%"> <input type=button value="이미지삭제" onClick="CheckForm('flash');" class="submit1"><?}else{?> style="width:500px"><?}?><br>
					* 등록 가능한 플래시는 파일 확장자 <span class="font_orange2">SWF(swf)</span> 만 가능하며 용량은 <span class="font_orange2">최대 200KB</span> 까지 가능합니다.<br>* 플래시 등록 후 디자인 입력란에 <span class="font_orange2">매크로명령어 [MAINFLASH_가로X세로]</span>를 입력하면 화면에 출력됩니다.</td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<? if (file_exists($imagepath."main_logo.swf")) { ?>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">등록된 플래쉬 보기</td>
					<td class="td_con1"><OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" WIDTH="505" HEIGHT="150" id=myMovieName> 
					<PARAM NAME=movie VALUE="<?=$imagepath?>main_logo.swf"> 
					<PARAM NAME=quality VALUE="high"> 
					<PARAM NAME=bgcolor VALUE="#FFFFFF"> 

					<EMBED src="<?=$imagepath?>main_logo.swf" quality="high" bgcolor="#FFFFFF" WIDTH="505" HEIGHT="150" NAME="myMovieName" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>
					</OBJECT></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<? } ?>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0"><span class="font_orange">디자인 입력</span></td>
					<td class="td_con1">* 등록한 이미지의 <span class="font_orange2">매크로명령어만</span> 또는 <span class="font_orange2">매크로명령어+디자인</span>, <span class="font_orange2">매크로명령어 없이 디자인 내용만</span>을 입력 가능.<br>
					* 전체HTML, 부분HTML ,TEXT 모두 지원됩니다.</td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td  colspan="2" class="space"><textarea name="up_shop_intro" rows="10" cols="86" wrap="off" style="width:100%" onKeyDown="chkFieldMaxLen(10000)" class="textarea"><?=$shop_intro?></textarea></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#B9B9B9"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="images/distribute_01.gif" border="0"></td>
					<td width="100%" background="images/distribute_02.gif"></td>
					<td><img src="images/distribute_03.gif" border="0"></td>
				</tr>
				<tr>
					<td background="images/distribute_04.gif"></td>
					<td class="notice_blue" valign="top">
					<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td style="padding-left:10px;padding-left:10px;">
							<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td class="notice_blue">1) 등록한 이미지만 나오게 하기 : 디자인 입력란에  <b>매크로명령어</b>만 삽입.</td>
							</tr>
							<tr>
								<td class="notice_blue">2) 등록한 이미지+ 추가 디자인 : <b>매크로명령어</b> + 텍스트 또는 직접 디자인 내용만 삽입.</td>
							</tr>
							<tr>
								<td class="notice_blue">3) 등록한 이미지와 관계없이 직접 디자인  : <b>매크로명령어</b>을 넣지 않고 직접 디자인 내용만 삽입.</td>
							</tr>
							<tr>
								<td class="notice_blue">4) 디자인 입력란이 공란인 경우 :  "저희 쇼핑몰 url에 방문해주셔서 감사합니다."로 표기 됩니다.</td>
							</tr>
							<tr>
								<td class="notice_blue">5) 최고 한글 5,000자까지 입력  가능합니다.</td>
							</tr>
							<tr>
								<td class="notice_blue">6) 전체HTML, 부분HTML ,TEXT 모두 지원됩니다.</td>
							</tr>
							</table>
						</td>
						<td  valign="top" align=right><img src="images/table_1.gif" border="0"></td>
					</tr>
					</table>
					</td>
					<td background="images/distribute_07.gif"></td>
				</tr>
				<tr>
					<td><img src="images/distribute_08.gif" border="0"></td>
					<td background="images/distribute_09.gif"></td>
					<td><img src="images/distribute_10.gif" border="0"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('up');"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="images/manual_top1.gif" border="0"></td>
					<td width="100%" background="images/manual_bg.gif"><img src="images/manual_title.gif" border="0"></td>
					<td><img src="images/manual_top2.gif" border="0"></td>
				</tr>
				<tr>
					<td background="images/manual_left1.gif"></td>
					<td style="padding-top:5px;"  class="menual_bg">
					<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_orange2"><b>메인 타이틀 매크로명령어</b></span>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</td>
					</tr>
					<tr>
						<td style="padding-left:13px;" class="space_top">
						<table width="100%" cellpadding="0" cellspacing="0">
						<col width="150" height="28" align="right"></col>
						<col></col>
						<tr>
							<td height="1" colspan="2" bgcolor="#C0C0C0"></td>
						</tr>
						<tr>
							<td class="table_cell">[MAINIMG]</td>
							<td class="td_con1">일반 이미지</td>
						</tr>
						<tr>
							<td height="1" colspan="2" bgcolor="#E3E3E3"></td>
						</tr>
						<tr>
							<td class="table_cell">[MAINFLASH_300X500]</td>
							<td class="td_con1">플래시 파일</td>
						</tr>
						<tr>
							<td height="1" colspan="2" bgcolor="#C0C0C0"></td>
						</tr>
						</table>
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">디자인 내용 입력 안내</span></td>
					</tr>
					<tr>
						<td style="padding-left:13px;" class="space_top">등록된 이미지나 플래시외에 별도의 이미지를 이용하실 경우는 직접 이미지 소스를 입력하면 됩니다.<br>
						별도이미지 등록 : <span class="font_blue"><a href="javascript:parent.topframe.GoMenu(2,'design_webftp.php');">디자인관리 > 웹FTP, 디자인 옵션 설정 > 웹FTP/웹FTP팝업</a></span><br>
						html태그 없이 Text만 입력할 경우 가운데 정렬이 기본입니다.<br>
						Text&nbsp;/ 부분 html을 사용 할 경우 줄바꿈(br)은 엔터(Enter]키를 이용하시면 됩니다.<br>
						부분 html예)<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;img src=000.jpg&gt; ↘ (Enter)<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[MAINIMG] ↘ (Enter)<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;쇼핑몰을 방문해주셔서 감사합니다.</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">개별 메인 디자인 사용할 경우</span></td>
					</tr>
					<tr>
						<td style="padding-left:13px;" class="space_top"><span class="font_blue"><a href="javascript:parent.topframe.GoMenu(2,'design_eachmain.php');">디자인관리 > 개별디자인 - 메인 및 상하단 > 메인본문 꾸미기</a></span> 에서<br>
						메인타이틀 이미지 매크로 명령어 <span class="font_orange2">[SHOPINTRO]</span>을 사용하지 않은 경우 본 디자인은 출력되지 않습니다.</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td><img src="images/icon_8.gif" border="0" align="absmiddle">나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</td>
					</tr>
					</table>
					</td>
					<td background="images/manual_right1.gif"></td>
				</tr>
				<tr>
					<td><img src="images/manual_left2.gif" border="0"></td>
					<td background="images/manual_down.gif"></td>
					<td><img src="images/manual_right2.gif" border="0"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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
<?= $onload ?>
<? INCLUDE "copyright.php"; ?>