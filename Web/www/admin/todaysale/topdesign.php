<? include "header.php"; 
if($_POST['act'] == 'save'){
	$sql = "select body from tbldesigndefault where type='todaysale' limit 1";	
	$res = mysql_query($sql,get_db_conn());

	if($res && mysql_num_rows($res)){		
		/** 에디터 관련 파일 처리 추가 부분 */
		if(preg_match_all('/\/data\/editor\/([a-zA-Z0-9\.]+)/',mysql_result($res,0,0),$edtimg)){
			if(!preg_match_all('/\/data\/editor\/([a-zA-Z0-9\.]+)/',$body,$edimg)) $edimg[1] =array();
			foreach($edtimg[1] as $cimg){
				if(!in_array($cimg,$edimg[1])) @unlink($_SERVER['DOCUMENT_ROOT'].'/data/editor/'.$cimg);
			}
		}

		if(preg_match_all('/\/data\/editor_temp\/([a-zA-Z0-9\.]+)/',$body,$edimg)){
			foreach($edimg[1] as $timg){
				@rename($_SERVER['DOCUMENT_ROOT'].'/data/editor_temp/'.$timg,$_SERVER['DOCUMENT_ROOT'].'/data/editor/'.$timg);
			}
			$body = str_replace('/data/editor_temp/','/data/editor/',$body);
		}
		/** #에디터 관련 파일 처리 추가 부분 */
		
		
		
		$sql = "update tbldesigndefault set body='"._escape($body,false)."' where type='todaysale'";
	}else{
		/** 에디터 관련 파일 처리 추가 부분 */
		if(preg_match_all('/\/data\/editor_temp\/([a-zA-Z0-9\.]+)/',$body,$edimg)){
			foreach($edimg[1] as $timg){
				@rename($_SERVER['DOCUMENT_ROOT'].'/data/editor_temp/'.$timg,$_SERVER['DOCUMENT_ROOT'].'/data/editor/'.$timg);
			}
			$body = str_replace('/data/editor_temp/','/data/editor/',$body);
		}
		/** #에디터 관련 파일 처리 추가 부분 */
		
		
		$sql = "insert into tbldesigndefault set body='"._escape($body,false)."' , type='todaysale'";
	}
	mysql_query($sql,get_db_conn()) or die(mysql_error()); ?>
	<script language="javascript" type="text/javascript">
	alert('저장되었습니다.');
	document.location.replace('/admin/todaysale.php?mode=topdesign');
	</script>
	<?
	
}
$sql = "select * from tbldesigndefault where type='todaysale' limit 1";
if(false !== $res = mysql_query($sql,get_db_conn())){
	if(mysql_num_rows($res)){
		$tmp = mysql_fetch_assoc($res);
		$body = $tmp['body'];
	}
}
?>
<style type="text/css">
@import url("/css/common.css");
</style>
<script language="javascript" type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">

//-->
</SCRIPT>
<script type="text/javascript" src="calendar.js.php"></script>
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
.productRegFormTbl{border-top:2px solid #333}
.productRegFormTbl th{ text-align:left; padding-left:25px; background:#f8f8f8 url(/admin/images/icon_point5.gif) 10px 50% no-repeat; border-bottom:1px solid #efefef; border-left:1px solid #efefef}
.productRegFormTbl td{padding-left:5px; border-bottom:1px solid #efefef; border-left:1px solid #efefef}
.productRegFormTbl caption{ text-align:left}
</style>
<!-- # 에디터용 파일 호출 -->

<div style="background:url(images/title_bg.gif) repeat-x left bottom; padding-bottom:25px;"><IMG SRC="images/todaysale_title03.gif" ALT="투데이세일 상단디자인" /></div>
<div style="padding:0px 0px 20px 20px;" class="notice_blue">투데이세일 상단 디자인을 등록할 수 있습니다.</div>

<input type="button" onclick="javascript:document.location.reload();" value="새로고침" />
<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="mode" value="topdesign">
<input type="hidden" name="act" value="save">
<!--<div>투데이 세일 상단 디자인</div>-->
	<table cellpadding="0" cellspacing="0" width="100%" class="productRegFormTbl">
		<tr>
			<td><textarea name="body" style="width:100%; height:500px;" lang="ej-editor1" class="textarea"><?=$body?></textarea></td>
		</tr>
	</table>
	<div style="text-align:center; margin-top:20px;"><input type="image" src="images/botteon_save.gif" value="저장"></div>
</form>