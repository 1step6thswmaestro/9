<?
if(strlen($Dir)==0) $Dir="../";

include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if($one==1) {
	$sql = "SELECT * FROM tbleventpopup WHERE num='".$num."' ";
	$result = mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);
}

$cookiename="eventpopup_".$row->num;

if ($layer=="Y" && $row->end_date==$_COOKIE[$cookiename]) return;

if($layer!="Y") {
?>
<HTML>
<HEAD>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<TITLE><?=$row->title?></TITLE>
<style>
td {font-family:Tahoma;color:666666;font-size:9pt;}

tr {font-family:Tahoma;color:666666;font-size:9pt;}
BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
parent.window.moveTo('<?=$row->x_to?>','<?=$row->y_to?>');
//-->
</SCRIPT>
</HEAD>
<BODY STYLE="MARGIN:0; PADDING:0">
<?}?>

<CENTER>

<TABLE width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
<form name=event_form1 method=post action="<?=$Dir.FrontDir?>event.php">
<input type=hidden name=type value="close">
<input type=hidden name=num value="<?=$row->num?>">
<TR>
	<TD>
<?
	if($layer=="Y"){
		$check="<input type=checkbox name=no value=\"yes\" onclick=\"p_windowclose('".$cookiename."','1');\" style=\"border:none\">";
		$close="\"JavaScript:p_windowclose('".$cookiename."','0');\"";
	}else {
		$check="<input type=checkbox name=no value=\"yes\" style=\"border:none\">";
		$close="\"JavaScript:document.event_form1.submit()\"";
	}
	$pattern=array("(\[CHECK\])","(\[CLOSE\])");
	$replace=array($check,$close);
	$content=preg_replace($pattern,$replace,$row->content);
	echo $content;

?>
	</TD>
</TR>
</form>
</TABLE>

</CENTER>

<?if($layer!="Y"){?>
</BODY>
</HTML>
<?}?>