<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

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
	echo "<html><head><title></title></head><body onload=\"alert('해당 페이지가 존재하지 않습니다.');history.go(-1);\"></body></html>";exit;
}

if($newobj->member_type=="Y") {
	if(strlen($_ShopInfo->getMemid())==0) {
		Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
		exit;
	}
} else if($newobj->member_type=="G") {
	if(strlen($_ShopInfo->getMemid())==0 || $newobj->group_code!=$_ShopInfo->getMemgroup()) {
		if(strlen($_ShopInfo->getMemid())==0) {
			Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
			exit;
		} else {
			echo "<html><head><title></title></head><body onload=\"alert('해당 페이지 접근권한이 없습니다.');location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
		}
	}
}
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?></TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
if($newobj->menu_type=="Y") {
	include ($Dir.MainDir.$_data->menu_type.".php");
	echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	echo "<tr>\n";
	echo "	<td valign=top>\n";
	echo $newobj->body;
	echo "	</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	include ($Dir."lib/bottom.php");
} else if($newobj->menu_type=="T" && $_data->frame_type!="N") {
	include ($Dir.MainDir."nomenu.php");
	echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	echo "<tr>\n";
	echo "	<td valign=top>\n";
	echo $newobj->body;
	echo "	</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	include ($Dir."lib/bottom.php");
} else {
	echo $newobj->body;
}
?>

</BODY>
</HTML>