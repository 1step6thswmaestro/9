<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/ext/func.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

// 쿠폰 디자인 수정
if(!_empty($_POST['mode'])){
	$sql="SELECT body FROM `tbldesigndefault` WHERE `type`='cpnlisttop' LIMIT 1;";
	if(false === $result=mysql_query($sql,get_db_conn())){
		_alert('DB 연동 오류 ','-1');
		exit;
	}
		
	if($_POST['mode'] != "delete" ){
		$body = $_POST['data'];
		$isupdate = false;
		if(mysql_num_rows($result)){
			$oribody = mysql_result($result,0,0);			
			$isupdate = true;
			/** 에디터 관련 파일 처리 추가 부분 */
			if(preg_match_all('/\/data\/editor\/([a-zA-Z0-9\.]+)/',$oribody,$edtimg)){
				if(!preg_match_all('/\/data\/editor\/([a-zA-Z0-9\.]+)/',$body,$edimg)) $edimg[1] =array();
				foreach($edtimg[1] as $cimg){
					if(!in_array($cimg,$edimg[1])) @unlink($_SERVER['DOCUMENT_ROOT'].'/data/editor/'.$cimg);
				}
			}
		}
	
		if(preg_match_all('/\/data\/editor_temp\/([a-zA-Z0-9\.]+)/',$body,$edimg)){		
			foreach($edimg[1] as $timg){
				@rename($_SERVER['DOCUMENT_ROOT'].'/data/editor_temp/'.$timg,$_SERVER['DOCUMENT_ROOT'].'/data/editor/'.$timg);
			}
			$body = str_replace('/data/editor_temp/','/data/editor/',$body);
		}
		/** #에디터 관련 파일 처리 추가 부분 */
		if($isupdate){
			$sql = "UPDATE `tbldesigndefault` SET `body`="._escape($body)." WHERE `type`='cpnlisttop'";
		}else{
			$sql = "insert into `tbldesigndefault` SET `type`='cpnlisttop',`body`="._escape($body)." ";
		}
		if(false === mysql_query($sql,get_db_conn())){
			echo mysql_error();
			exit;
			_alert('DB 연동중 오류가 발생 했습니다.','-1');
		}else{
			_alert('저장 되었습니다.','/admin/market_coupondesign.php');
		}
		exit;
	}else if( $_POST['mode'] == "delete" ){ //쿠폰 디자인 삭제	
		if(mysql_num_rows($result)){
			$oribody = mysql_result($result,0,0);
			/** 에디터 관련 파일 처리 추가 부분 */
			if(preg_match_all('/\/data\/editor\/([a-zA-Z0-9\.]+)/',$oridata,$edimg)){
				foreach($edimg[1] as $timg){
					@unlink($_SERVER['DOCUMENT_ROOT'].'/data/editor/'.$timg);
				}
			}
			/** #에디터 관련 파일 처리 추가 부분 */		
			if(false === mysql_query("delete from tbldesigndefault where `type`='cpnlisttop'",get_db_conn())){
				_alert('삭제중 오류가 발생했습니다.','-1');
			}
		}
		_alert('삭제 되었습니다.','/admin/market_coupondesign.php');
		exit;
	}
}

$body = '';
$sql="SELECT body FROM `tbldesigndefault` WHERE `type`='cpnlisttop' LIMIT 1;";
if(false === $result=mysql_query($sql,get_db_conn())){
}else if(mysql_num_rows($result)){
	$body = mysql_result($result,0,0);
}
include "header.php";
/*
?>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>
*/ ?>
<SCRIPT LANGUAGE="JavaScript">
<!--

function mSave() {
	if(confirm("수정하시겠습니까?")) {
		document.form1.mode.value="modify";
		document.form1.submit();
	}
}

function mDelete() {
	if(confirm("쿠폰모음 상단 디자인을 삭제하시겠습니까?")) {
		document.form1.mode.value="delete";
		document.form1.submit();
	}
}
//-->
</SCRIPT>

<!-- 에디터용 파일 호출 -->
<script type="text/javascript" src="/gmeditor/js/jquery.js"></script>
<script type="text/javascript" src="/gmeditor/js/jquery.event.drag-2.0.min.js"></script>
<script type="text/javascript" src="/gmeditor/js/jquery.resizable.js"></script>
<script type="text/javascript" src="/gmeditor/js/ajax_upload.3.6.js"></script>
<script type="text/javascript" src="/gmeditor/js/ej.h2xhtml.js"></script>
<script type="text/javascript" src="/gmeditor/editor.js"></script>
<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jquery.autocomplete.css" />
<script language="javascript" type="text/javascript">
$(document).ready(function() {
	ejEditor();
});
</script>
<style type="text/css">
@import url("/gmeditor/common.css");
</style>
<!-- # 에디터용 파일 호출 -->

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




<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 프로모션 &gt; 쿠폰발생 서비스 설정 &gt; <span class="2depth_select">쿠폰모음 상단 디자인</span></td>
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
		<td width="100%" bgcolor="#ffffff" style="padding:10px">


<div style="width:100%; background:url(images/title_bg.gif) left bottom repeat-x; margin-top:8px; margin-bottom:3px; padding-bottom:21px;"> <img src="images/market_offlinecoupontop_title.gif" alt="쿠폰모음 상단 디자인"> </div>
<span class="notice_blue" style=" display:block; padding-left:22px; padding-bottom:20px;">쿠폰모음 페이지 상단 디자인을 등록하실 수 있습니다.</span>

<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=mode>

<table cellpadding="0" cellspacing="0" width="100%" border="0">
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" width="100%" height="100%">
				<tr>
					<td width="100%" bgcolor="white"><img src="images/market_offlinecoupontop_stitle1.gif" alt="디자인 편집타입 선택" style="margin-bottom:3px;"></td>
				</tr>
				<tr><td  HEIGHT=3></td></tr>
				<tr><td width="100%" bgcolor="eeeeee" HEIGHT=2 ALT=""></td></tr>
				<tr>
					<td width="100%" height="100%" valign="top" style="border-bottom-width:2px; border-bottom-color:#eeeeee; border-bottom-style:solid;">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="100%" style="padding-bottom:2pt;">
									<TEXTAREA style="WIDTH:100%; height:300px;" name="data" wrap="off"  lang="ej-editor1"><?=htmlspecialchars($body)?></TEXTAREA>
								</td>
							</tr>
							<tr>
								<td align=center style="padding-top:2pt; padding-bottom:2pt;" height="22">
			<?
						if($disabled == "disabled") {
							echo "<img src=\"images/btn_edit1.gif\" width=\"113\" height=\"38\" border=\"0\" hspace=\"0\" vspace=\"4\">";
							echo "<img src=\"images/btn_del3.gif\" width=\"113\" height=\"38\" border=\"0\" hspace=\"2\" vspace=\"4\">";
						} else {
							echo "<a href=\"javascript:mSave();\"><img src=\"images/btn_edit2.gif\" width=\"113\" height=\"38\" border=\"0\" hspace=\"0\" vspace=\"4\"></a>";
							echo "<a href=\"javascript:mDelete();\"><img src=\"images/btn_del3.gif\" width=\"113\" height=\"38\" border=\"0\" hspace=\"2\" vspace=\"4\"></a>";
						}
			?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>




										</td><td width="16" background="images/con_t_02_bg.gif"></td></tr>
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
	<tr></tr>
</table>

<?=$onload?>

<? INCLUDE "copyright.php"; ?>