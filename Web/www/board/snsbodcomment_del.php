<?
header("Content-type:text/html; charset=euc-kr");
include "head.php";

if ($setup[use_comment] != "Y") {
	$errmsg="no reply";
}

if ($member[grant_comment]!="Y") {
	$errmsg="no authority";
}

$qry  = "SELECT * FROM tblboardcomment WHERE board='".$board."' AND parent='".$num."' AND num='".$c_num."' ";
$result1 = mysql_query($qry,get_db_conn());
$ok_result = mysql_num_rows($result1);

if ((!$ok_result) || ($ok_result == -1)) {
	$errmsg="nodata";
} else {
	$row1 = mysql_fetch_array($result1);
}

if(!$errmsg){
	if($member[admin]!="SU" && $row1[id]!= $_ShopInfo->getMemid()) {
		if (($row1[passwd]!=$_POST["up_passwd"]) && ($setup[passwd]!=$_POST["up_passwd"])) {
			$errmsg="no authority";
		}
	}else{
		$del_sql = "DELETE FROM tblboardcomment WHERE board='".$board."' AND parent='".$num."' AND num = '".$_POST["c_num"]."'";
		$delete = mysql_query($del_sql,get_db_conn());

		if ($delete) {
			@mysql_query("UPDATE tblboard SET total_comment = total_comment - 1 WHERE board='".$board."' AND num='".$num."'",get_db_conn());
			$errmsg="ok";
		}
	}
}
$return_data = array("result"=>$errmsg);
echo json_encode($return_data);
?>

