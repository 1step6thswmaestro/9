<?
$Dir="./";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$storeid=$_GET["storeid"];

$sql = "SELECT vender,disabled FROM tblvenderinfo ";
$sql.= "WHERE id='".$storeid."' AND delflag='N' ";
$result=mysql_query($sql,get_db_conn());
$mrow=mysql_fetch_object($result);
mysql_free_result($result);

if(!$mrow) {
	header("Location:".$Dir);
	exit;
}

##################################################
######## 해당 미니샵 방문 카운트 처리 추가 #######
##################################################
$sql = "INSERT INTO tblvenderstorevisittmp VALUES ('".$mrow->vender."','".date("Ymd")."','".getenv("REMOTE_ADDR")."') ";
mysql_query($sql,get_db_conn());
if (mysql_errno()!=1062) {
	$sql = "INSERT INTO tblvenderstorevisit VALUES ('".$mrow->vender."','".date("Ymd")."','1') ";
	mysql_query($sql,get_db_conn());
	if (mysql_errno()==1062) {
		$sql = "UPDATE tblvenderstorevisit SET cnt=cnt+1 WHERE vender='".$mrow->vender."' AND date='".date("Ymd")."' ";
		mysql_query($sql,get_db_conn());
	}
	$sql = "UPDATE tblvenderstorecount SET count_total=count_total+1 ";
	$sql.= "WHERE vender='".$mrow->vender."' ";
	mysql_query($sql,get_db_conn());
}

$mainurl=$Dir.FrontDir."minishop.php?sellvidx=".$mrow->vender."";
if ($_data->frame_type=="A") {	#원프레임 타입일 경우
	Header("Location: ".$mainurl);
	exit;
} else {
	if ($_data->frame_type=="Y") {	//주소고정
		$top_height=0;
		$_data->top_type="top";
	} else if ($_data->top_type=="topp") {
		$result2 = mysql_query("SELECT top_height FROM tbldesign",get_db_conn());
		if ($row2=mysql_fetch_object($result2)) $top_height=$row2->top_height;
		else $top_height=70;
		mysql_free_result($result2);
	} else if($_data->top_type=="topeasy"){
		$result2 = mysql_query("SELECT ysize FROM tbldesigneasytop",get_db_conn());
		if ($row2=mysql_fetch_object($result2)) $top_height=$row2->ysize;
		mysql_free_result($result2);
	} else {
		$result2 = mysql_query("SELECT top_height FROM tbltempletinfo WHERE icon_type='".$_data->icon_type."'",get_db_conn());
		if ($row2=mysql_fetch_object($result2)) $top_height=$row2->top_height;
		else $top_height=70;
		mysql_free_result($result2);
	}

	if ($_data->adult_type=="Y") {
		$http_host = ereg_replace("www.","",getenv("HTTP_HOST"));
		$adult_meta = "<META http-equiv=\"PICS-label\" content='(PICS-1.1 \"http://service.icec.or.kr/rating.html\" l gen true for \"http://www.$http_host\" r (y 1))'>\n";
		$adult_meta = $adult_meta."<META http-equiv=\"PICS-label\" content='(PICS-1.1 \"http://service.icec.or.kr/rating.html\" l gen true for \"http://$http_host\" r (y 1))'>\n";
		$adult_meta = $adult_meta."<META http-equiv=\"PICS-label\" content='(PICS-1.1 \"http://www.safenet.ne.kr/rating.html\" l gen true for \"http://www.$http_host\" r (n 3 s 3 v 3 l 3 i 0 h 0))'>\n";
		$adult_meta = $adult_meta."<META http-equiv=\"PICS-label\" content='(PICS-1.1 \"http://www.safenet.ne.kr/rating.html\" l gen true for \"http://$http_host\" r (n 3 s 3 v 3 l 3 i 0 h 0))'>\n";
	} else {
		$adult_meta="";
	}

?>
<HTML>
<HEAD>
<TITLE><?=(strlen($_data->shoptitle)>0?$_data->shoptitle:$_data->shopname)?></TITLE>
<?=$adult_meta?>
<meta http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<meta name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<meta name="keywords" content="<?=$_data->shopkeyword?>">
</HEAD>
<frameset rows="<?=$top_height?>,*" border=0 MARGINWIDTH=0 MARGINHEIGHT=0>
<frame src="<?=$Dir.MainDir.$_data->top_type?>.php" name=topmenu MARGINWIDTH="0" MARGINHEIGHT="0" scrolling=no noresize>
<frame src="<?=$mainurl?>" name=main MARGINWIDTH="0" MARGINHEIGHT="0" scrolling=auto>
</frameset>
</HTML>
<?
}
?>