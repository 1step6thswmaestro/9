<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if( $preview===true ) {

	$sql = "SELECT * FROM `tbldesignnewpage_prev` WHERE `type` = 'useinfo' LIMIT 1;";
	$result = mysql_query($sql,get_db_conn());

	if($row=mysql_fetch_object($result)) {
		$useinfo = $row->body;
		$leftmenu = $row->leftmenu;
	}



} else {

	$sql = "SELECT useinfo FROM tbldesign ";
	$result = mysql_query($sql,get_db_conn());

	if($row=mysql_fetch_object($result)) {
		$useinfo=$row->useinfo;
		if (substr($useinfo,-2,1)=="") {
			$leftmenu = substr($useinfo,-1);
			$useinfo = substr($useinfo,0,-2);
		} else {
			$leftmenu = "Y";	//N:상단 타이틀 이미지 기본 사용, Y:상단 타이틀 이미지 내용속에 포함
		}

	}
	mysql_free_result($result);

	if (strpos(strtolower($useinfo),"table")!=false) $useinfo = "<pre>".$useinfo."</pre>";
	else $useinfo = ereg_replace("\n","<br>",$useinfo);

}

?>
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 이용안내</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
if ($leftmenu!="N") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/useinfo_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/useinfo_title.gif\" border=\"0\" alt=\"이용안내\"></td>\n";
	} else {
		echo "<td>\n";
		/*
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/useinfo_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/useinfo_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/useinfo_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		*/
		echo "<div class=\"subpageTitle\">이용안내</div>";
		echo "</td>\n";
	}
	echo "</tr>\n";
}
?>
<tr>
	<td align="center">
<?
	if(strlen($useinfo)>0) {
		echo $useinfo;
	} else {
		include($Dir.TempletDir."useinfo/useinfo".$_data->design_useinfo.".php");
	}
?>
	</td>
</tr>
</table>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>