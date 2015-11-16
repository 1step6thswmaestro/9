<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())>0) {
	header("Location:mypage_usermodify.php");
	exit;
}


$leftmenu="Y";
$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='joinagree'";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$body=$row->body;
	$body=str_replace("[DIR]",$Dir,$body);
	$leftmenu=$row->leftmenu;
	$newdesign="Y";
}
mysql_free_result($result);

?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 회원가입</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<link href="/css/b2b_style.css" rel="stylesheet" type="text/css" />

</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>
<form name=form1 action="member_join2.php" method=post>
<table border=0 cellpadding=0 cellspacing=0 width=100%>

<?
if ($leftmenu!="N") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/memberjoin_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/memberjoin_title.gif\" border=\"0\" alt=\"회원가입\"></td>\n";
	} else {
		echo "<td>\n";
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/memberjoin_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/memberjoin_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/memberjoin_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		echo "</td>\n";
	}
	echo "</tr>\n";
}

?>
	<tr>
		<td height="150" align="center"><img src="<?=$Dir?>images/common/joinend_img.gif" alt="" /></td>
	</tr>
	<tr>
		<td align="center"><a href="<?=$Dir?>front/login.php"><img src="<?=$Dir?>images/common/btn_joinend1.gif" border="0" alt="" /></a><a href="/"><img src="<?=$Dir?>images/common/btn_joinend2.gif" border="0" alt="" /></a></td>
	</tr>
	<tr><td height="50"></td></tr>
</table>
</form>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>