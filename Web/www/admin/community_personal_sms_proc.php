<?

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-1";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

//$mode = !_empty($_POST['mode'])?trim($_POST['mode']):"";
echo $numberlist = !_empty($_POST['numberlist'])?trim($_POST['numberlist']):"";
echo $used = !_empty($_POST['checksms'])?trim($_POST['checksms']):"";

$checkSQL ="SELECT * FROM personalboard_admin LIMIT 0, 1";

$query="";
if(false !== $checkRes = mysql_query($checkSQL,get_db_conn())){
	$checkRowcount = mysql_num_rows($checkRes);
	if($checkRowcount>0){
		$query = "UPDATE personalboard_admin SET smsused = '".$used."' , leavenumber='".$numberlist."' ";
	}else{
		$query = "INSERT personalboard_admin SET type='SMS', smsused='".$used."', leavenumber='".$numberlist."' ";
	}
}

if(strlen($query)>0 && strlen($used)>0){
	if(mysql_query($query,get_db_conn())){
		echo '<script>alert("설정되었습니다.");location.replace("./community_personal.php");</script>';exit;
	}else{
		echo '<script>alert("오류가 발생하여 취소되었습니다.");location.replace("./community_personal.php");</script>';exit;
	}
}
exit;
?>