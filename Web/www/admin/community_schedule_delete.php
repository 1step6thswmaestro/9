<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(strlen($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$CurrentTime = time();

if ($sid && $return_page && $year && $month && $day) {
	$sql = "DELETE FROM tblschedule WHERE idx = '".$sid."' ";
	$delete = mysql_query($sql,get_db_conn());

	if ($delete) {
		echo "<script>alert('해당 일정을 삭제 하였습니다.');location='".$return_page."?year=".$year."&month=".$month."&day=".$day."';</script>";
		exit;
	} else {
		echo "<script>alert('일정 삭제중 오류가 발생하였습니다.');history.go(-1);</script>";
		exit;
	}
} else {
	echo "<script>alert('잘못된 경로의 일정입니다.');history.go(-1);</script>";
	exit;
}
?>