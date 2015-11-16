<?
if(substr(getenv("SCRIPT_NAME"),-14)=="/leftevent.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}

$eventbody="";
$sql_design="SELECT * FROM ".$designnewpageTables." WHERE type='leftevent'";
$result_design=mysql_query($sql_design,get_db_conn());
if($row_design=mysql_fetch_object($result_design)){
	$designtype=$row_design->code;
	$eventbody = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	if($designtype==1) {
		$eventbody.= "<tr>\n";
		$eventbody.= "	<td align=center><img src=\"".$Dir.DataDir."shopimages/etc/".$row_design->filename."\"></td>\n";
		$eventbody.= "</tr>";
	}else if($designtype==2){
		$eventbody.= "<tr>\n";
		$eventbody.= "	<td>".$row_design->body."</td>\n";
		$eventbody.= "</tr>";
	}
	$eventbody.= "</table>";
}
mysql_free_result($result_design);
?>
