<?
$Dir="./";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$shopid=$_POST["shopid"];
$authkey=$_POST["authkey"];
$enckey=$_POST["enckey"];


if(strlen($shopid)==0 || strlen($authkey)==0 || strlen($enckey)==0) {
	echo "NO"; exit;
}
if($enckey!=getEncKey($shopid)) {
	echo "NO"; exit;
}

//sms 서버쪽에 id, authkey 확인
$resdata=getSmscount($shopid,$authkey);
if(substr($resdata,0,2)=="OK") {
	$sql = "SELECT * FROM tblsmsinfo ";
	$result=mysql_query($sql,get_db_conn());
	if ($row=mysql_fetch_object($result)) {
		$sql = "UPDATE tblsmsinfo SET ";
		$sql.= "id		= '".$shopid."', ";
		$sql.= "authkey	= '".$authkey."' ";
		mysql_query($sql,get_db_conn());
	} else {
		$sql = "INSERT INTO tblsmsinfo (id, authkey) VALUES ('".$shopid."','".$authkey."')";
		mysql_query($sql,get_db_conn());
	}

	echo "OK"; exit;
} else {
	echo "NO"; exit;
}
?>