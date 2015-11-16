<?
header("Content-type: text/html; charset=euc-kr");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include ("access.php");

$q = trim(strtolower($_GET["q"]));
if (strlen($q) >0 ) {
	$w = " and id LIKE '%".$q."%'";
}else{
	$w = "";
}

$sql = "select DISTINCT id as course_name, vender, com_name from tblvenderinfo where 1 ".$w;
$rsd = mysql_query($sql,get_db_conn());
while($rs = mysql_fetch_array($rsd)) {
	$cname = $rs['course_name'].' ('.$rs['com_name'].')';
	$vender = $rs['vender'];
	echo "$cname\n";
	//echo "$vender\n";
}
?>