<?
include_once("header.php");

$code=$_REQUEST["code"];
if(strlen($code)>0) {
	$sql = "SELECT * FROM ".$designnewpageTables." WHERE type='newpage' AND code='".$code."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$isnew=true;
		unset($newobj);
		$newobj->subject=$row->subject;
		$newobj->menu_type=$row->leftmenu;
		$filename=explode("",$row->filename);
		$newobj->member_type=$filename[0];
		$newobj->menu_code=$filename[1];
		$newobj->body=$row->body;
		$newobj->body=str_replace("[DIR]",$Dir,$newobj->body);
		if(strlen($newobj->member_type)>1) {
			$newobj->group_code=$newobj->member_type;
			$newobj->member_type="G";
		}
	}
	mysql_free_result($result);
}
if($isnew!=true) {
	echo "<html><head><title></title></head><body onload=\"alert('�ش� �������� �������� �ʽ��ϴ�.');history.go(-1);\"></body></html>";exit;
}

if($newobj->member_type=="Y") {
	if(strlen($_ShopInfo->getMemid())==0) {
		Header("Location:/m/login.php?chUrl=".getUrl());
		exit;
	}
} else if($newobj->member_type=="G") {
	if(strlen($_ShopInfo->getMemid())==0 || $newobj->group_code!=$_ShopInfo->getMemgroup()) {
		if(strlen($_ShopInfo->getMemid())==0) {
			Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
			exit;
		} else {
			echo "<html><head><title></title></head><body onload=\"alert('�ش� ������ ���ٱ����� �����ϴ�.');location.href='/m/main.php'\"></body></html>";exit;
		}
	}
}
?>
<div id="content">
	<div class="h_area2">
		<h2><?=$newobj->subject?></h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
	</div>
	<div id="container">
		<?=$newobj->body?>
	</div>
</div>
<script>
	$('#container img').attr('width','100%');
</script>
<?
include_once("footer.php");
?>