<?
if(substr(getenv("SCRIPT_NAME"),-11)=="/banner.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}

$sql = "SELECT * FROM tblbanner ORDER BY date DESC ";
$result=mysql_query($sql,get_db_conn());
$bannerbody="";
while($row=mysql_fetch_object($result)) {
	$bannerbody.= "<tr><td align=center><A HREF=\"http".($row->url_type=="S"?"s":"")."://".$row->url."\" target=".$row->target."><img src=\"".$Dir.DataDir."shopimages/banner/".$row->image."\" border=\"".$row->border."\"></A></td></tr>\n";
	//$bannerbody.= "<tr><td height=2></td></tr>\n";
}
mysql_free_result($result);
if(strlen($bannerbody)>0) {
	$bannerbody = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n".$bannerbody;
	$bannerbody.= "</table>\n";
}
?>