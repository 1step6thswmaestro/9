<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

INCLUDE ("access.php");
//backup에 필요한 모듈 include
include ("../lib/backup.php");
include ("../lib/backup_config.php");
include ("../lib/lib.new.php");		//db클래스추가 by.jyh
$backup = new backup();
####################### 페이지 접근권한 check ###############
$PageCode = "de-8";
$MenuCode = "design";
$snavi3 = "디자인 복구하기";

/*
// 에러 리포팅 확인
error_reporting(E_ALL);
ini_set("display_errors", 1);
*/
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

// file_list 조회 함수
function myallfile($dir, $ext = ''){
	$file_arr = array();
	if (is_dir($dir)){
	    if ($dh = opendir($dir)){
	        while (($file = readdir($dh)) !== false){
	            $type = filetype($dir . $file);

	            if($type == 'file'){
	                if($ext != ''){
                        $ext = strtolower($ext);
                        $temp = explode('.',$file);
                        if(strtolower($temp[count($temp)-1]) == $ext) $file_arr[] = $dir.$file;
	                }
	                else    $file_arr[] = $dir.$file;
	            }
	            else if($type == 'dir' && ($file != '.' && $file != '..'))
	            {

	                $temp = myallfile($dir.$file.'/', $ext);
	                if(is_array($temp)){
	                	$file_arr = array_merge($file_arr, $temp);
	                }
	            }
	        }
	        closedir($dh);
	    }
	    return $file_arr;
	}
	return 0;
}

$path_root = $_SERVER['DOCUMENT_ROOT'].'data/revert/';
$revert_list = myallfile($path_root);

$type	= getInjection($_GET['type']);
$delno	= getInjection($_GET['delno']);
$file_name	= getInjection($_GET['file_name']);

//삭제
if($type == 'delete') {
	if(!$delno) $onload = "<script>alert(\"삭제할 백업이 선택되지 않았습니다.\");</script>";

	if($backup->design_delete($delno,$_ShopInfo->getId())) $onload = "<script>alert(\"삭제 되었습니다.\");</script>";
	else $onload = "<script>alert(\"삭제 중 오류가 발생 했습니다.\");</script>";

} elseif ($type == 'revert') {
	//DB복구기능
	if(!$delno) $onload = "<script>alert(\"복구할 백업이 선택되지 않았습니다.\");</script>";

	if($backup->design_revert($delno)) $onload = "<script>alert(\"디자인 복구가 완료 되었습니다.\");</script>";
	else $onload = "<script>alert(\"복구 중 오류가 발생 했습니다.\");</script>";

//파일업로드로 복구
} elseif ($type == 'file_revert') {
	$backup->setFileTgz($_FILES['dbl_ftpnm']);
	$result = $backup->file_revert();
	if($result=="Notzip") $onload = "<script>alert(\"올바른 복구파일이 아닙니다. 폴더명,파일확장자를 확인해 주세요.\");</script>";
	elseif($result=="Notsize") $onload = "<script>alert(\"8M 이하 압축파일만 업로드 가능합니다.\");</script>";
	elseif($result=="OK") $onload = "<script>alert(\"디자인 복구가 완료 되었습니다.\");</script>";
	else $onload = "<script>alert(\"복구 중 오류가 발생 했습니다.\");</script>";
//파일 수동 업로드로 복구
} elseif ($type == 'file_revert2') {
	$backup->setFileTgz($file_name);
	$result = $backup->file_revert('ftp');
	if($result=="Notzip") $onload = "<script>alert(\"올바른 복구파일이 아닙니다. 폴더명,파일확장자를 확인해 주세요.\");</script>";
	elseif($result=="OK") $onload = "<script>alert(\"디자인 복구가 완료 되었습니다.\");</script>";
	else $onload = "<script>alert(\"복구 중 오류가 발생 했습니다.\");</script>";
}
//백업목록 불러오기
$result = $db->select("tbldesign_backup_list",array(
						'field' => "*",
						'where' => "dbl_type = 'backup' and dbl_use = 'y'",
						"order" => "dbl_no desc" ));
?>

<?INCLUDE ("header.php"); ?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	var form = document.form1;
	var imgform = form.dbl_ftpnm.value;
	var ext = imgform.lastIndexOf(".");	//파일 확장자 자르기
	var len = imgform.length;	//파일이름의 길이
	var extnm = imgform.substring(ext+1);	//파일 이름의 마지막 확장자

	if(form.dbl_ftpnm.value=="") {
		alert("복구파일을 선택해주세요.");
		form.dbl_ftpnm.focus();
		return false;
	}

	if(extnm !="zip") {
		alert("첨부파일의 확장자는 zip만 업로드 가능합니다.");
		form.dbl_ftpnm.focus();
		return false;
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
			<? include ("menu_design.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; 디자인 백업 관리  &gt; <span class="2depth_select">복구하기</span></td>
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
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/design_backup_title.gif" ALT="디자인 백업하기"></TD>
					</tr>
<tr>
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
					<TD width="100%" class="notice_blue">백업된 디자인 데이터를 복구 할 수 있습니다.</TD>
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
					<TD><IMG SRC="images/design_revert_stitle.gif" ALT="복구하기"></TD>
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
					<TD class="notice_blue" valign="top"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">1) 복구시 해당 경로(/data/design/) 파일은 모두 삭제한 뒤 파일이 생성 되며, DB의 디자인 데이터도 모두 삭제되고 새로 입력됩니다.<BR>
					2) 복구 할 압축파일(zip)에 htm파일명, 경로가 백업 받았을 때와 동일해야 정상적으로 디자인 복구가 이루어 집니다.<BR>
					&nbsp;&nbsp;&nbsp;&nbsp;(백업받은 html 파일명 & 경로를 사용자 임의로 변경하면 디자인이 정상적으로 보여지지 않습니다.)<BR>
					3) htm 업로드 디자인 복구시 프로그램언어는 치환되서 적용 됩니다.<BR>
					4) 다운 받은 압축파일명은 <strong>절대 변경 하시면 안됩니다</strong>. 수동 복구시 복구가 <strong style="color:ff0000">불가능</strong>할 수 있습니다.</TD>
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
			<tr><td height="3"></td></tr>
			<form name=form1 action="?type=file_revert" method=post onsubmit="return CheckForm();" enctype='multipart/form-data'>
			<input type=hidden name=type>
			<tr>
				<td valign="top" align="center" style="padding-top:20px;">
					<table cellSpacing=0 cellPadding=0 width="100%" border="0" style="bottom-background">
						<tr>
							<td colspan="2" background="images/table_top_line.gif"></td>
						</tr>
						<tr>
							<td class="table_cell" width="20%"><img src="images/icon_point2.gif" border="0">복구파일(.zip)</td>
							<td class="td_con1" width="80%"><input type="file" name="dbl_ftpnm" id="dbl_ftpnm"  class="input" style="width:98%" /> </td>
						</tr>
						<tr>
							<td colspan="2" background="images/table_top_line.gif"></td>
						</tr>
						<tr><td height="10"></td></tr>
						<tr>
							<td colspan="2" style="height:35px;text-align:center" ><!--<input type="submit" style="width:200px;" value="복 구 하 기" />--><input type="image" src="images/botteon_restore.gif"></td>
						</tr>
					</table>
				</td>
			</tr>
			</form>
			<tr><td height="30"></td></tr>
			<tr>
				<td>
					<table width="100%" border=0 cellpadding=0 cellspacing=0>
						<tr>
							<td><img src="images/design_backup_revert_stitle2.gif" alt="데이터 수동 FTP 올린 후 복구하기"></td>
							<td width="100%"></td>
						</tr>
					</table>
				</td>
			</tr>
			<!-- $revert_list -->
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td valign="top">
					<table width="100%" cellspacing="0" cellpadding="0" border="0">
						<col width="5%"></col>
						<col width=""></col>
						<col width="10%"></col>
						<tr>
							<td colspan="6" background="images/table_top_line.gif"></td>
						</tr>
						<tr align=center>
							<td class="table_cell">No</td>
							<td class="table_cell1">백업이름</td>
							<td class="table_cell1">복구</td>
						</tr>
						<tr>
							<td colspan="6" background="images/table_con_line.gif"></td>
						</tr>

		<?
						if(count($revert_list) > 0 AND $revert_list != '0' ) {
							foreach($revert_list as $k => $v) {
								$list = explode("/", $v);
								$list_file_name = array_pop($list);

		?>
						<tr>
							<td class="td_con2" align="center"><?=$k+1?></td>
							<td class="td_con1" align="center"><?=$list_file_name?></td>
							<td class="td_con1" align="center"><a href="?type=file_revert2&file_name=<?=$list_file_name?>" onclick="if(!confirm('정말 복구 하시겠습니까?\n** 복구하시면 기존 디자인 데이터는 삭제 됩니다.**')) return false;">복구</a></td>
						</tr>
						<tr>
							<td colspan="4" background="images/table_con_line.gif"></td>
						</tr>
		<?
							}
						}
		?>
					</table>
				</td>
			</tr>
			<!-- 끝 -->
			<tr><td height=20></td></tr>
			<tr>
				<td>
					<table width="100%" border=0 cellpadding=0 cellspacing=0>
						<tr>
							<td><img src="images/design_backup_revert_stitle3.gif" alt="데이터 DB로 복구하기"></td>
							<td width="100%"></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td valign="top">
					<table width="100%" cellspacing="0" cellpadding="0" border="0">
						<col width="5%"></col>
						<col width=""></col>
						<col width="15%"></col>
						<col width="10%"></col>
						<col width="10%"></col>
						<col width="10%"></col>
						<tr>
							<td colspan="6" background="images/table_top_line.gif"></td>
						</tr>
						<tr align=center>
							<td class="table_cell">No</td>
							<td class="table_cell1">백업이름</td>
							<td class="table_cell1">일자</td>
							<td class="table_cell1">다운로드</td>
							<td class="table_cell1">복구</td>
							<td class="table_cell1">삭제</td>
						</tr>
						<tr>
							<td colspan="6" background="images/table_con_line.gif"></td>
						</tr>
		<?				if($result) {
							foreach($result as $k =>$v) {  ?>
						<tr>
							<td class="td_con2" align="center"><?=$k+1?></td>
							<td class="td_con1" align="center"><?=$v['dbl_subject']?></td>
							<td class="td_con1" align="center"><?=$v['dbl_date']?></td>
							<td class="td_con1" align="center"><a href="download.php?dir=<?=DataDir?>design_backup/&no=<?=$v['dbl_no']?>" target=_top>다운로드</a></td>
							<td class="td_con1" align="center"><a href="?type=revert&delno=<?=$v['dbl_no']?>" onclick="if(!confirm('정말 복구 하시겠습니까?\n** 복구하시면 기존 디자인 데이터는 삭제 됩니다.**')) return false;">복구</a></td>
							<td class="td_con1" align="center"><a href="?type=delete&delno=<?=$v['dbl_no']?>" onclick="if(!confirm('정말 삭제 하시겠습니까?')) return false;">삭제</a></td>
						</tr>
						<tr>
							<td colspan="6" background="images/table_con_line.gif"></td>
						</tr>
		<?
							}
						}
		?>
					</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"  class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="100%"><span class="font_dotline">데이터 복구시 압축파일 용량 : 10M이하</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 10MB를 초과할 시 FTP를 통해 수동으로 업로드 후 "데이터 수동 FTP 올린 후 복구하기" 에서 복구를 선택하세요.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 수동으로 FTP 시 업로드 시 경로는 /data/revert/ 입니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="100%" class="space_top" style="letter-spacing:-0.5pt;">- 수동으로 FTP 시 업로드 복원 시 필요 권한은 757 입니다.</td>
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
<? include ("copyright.php"); ?>
