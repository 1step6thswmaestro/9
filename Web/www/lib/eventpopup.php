<?
if(substr(getenv("SCRIPT_NAME"),-15)=="/eventpopup.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}

$curdate=date("Ymd");
unset($_layerdata);
$sql = "SELECT * FROM tbleventpopup WHERE start_date<='".$curdate."' AND end_date>='".$curdate."' ";
$result=mysql_query($sql,get_db_conn());
while($row=mysql_fetch_object($result)) {
	if($row->frame_type!="2") {	//팝업창일 경우에만 (레이어 타입 제외)
		$cookiename="eventpopup_".$row->num;
		if($row->end_date!=$_COOKIE[$cookiename]) {
			if($row->scroll_yn=="Y") $scroll="yes";
			else $scroll="no";
			$onload.=" if (\"".$row->end_date."\"!=getCookie(\"".$cookiename."\")) window.open('".$Dir.FrontDir."event.php?num=".$row->num."','event_".$row->num."','left=".$row->x_to.",top=".$row->y_to.",width=".$row->x_size.",height=".$row->y_size.",scrollbars=".$scroll."');\n";
		}
	} else {
		$_layerdata[]=$row;
	}
}
mysql_free_result($result);
?>