<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include_once($Dir."lib/admin_more.php");


/* 추가 jdy */
$admin_chk = $_POST[admin_chk];
/* 추가 jdy */
/* 입점운영기본관리에서 입점기능을 사용하지 않도록 설정하면 로그인이 안됨 */

$_dataShopMoreInfo = getShopMoreInfo();

//function_use = 1 인경우 입점업체를 사용하는 것이나 추가내용이 아직 insert 안되었을수 있으니 값이 아무것도 없는 경우도 기능을 사용할수 있도록 판단한다.
if ($admin_chk!="1" && $_dataShopMoreInfo['function_use']=="0" && strlen($_dataShopMoreInfo['function_use'])>0) {
	echo "<script> alert(\"기능을 사용하실 수 없습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.\");location.href='/vender/'; </script>";
	exit;
}
/* 입점운영기본관리에서 입점기능을 사용하지 않도록 설정하면 로그인이 안됨 */

$_VenderInfo = new _VenderInfo($_COOKIE[_vinfo]);

$connect_ip = getenv("REMOTE_ADDR");

$id = $_POST[id];
$passwd = $_POST[passwd];

$ssltype=$_POST["ssltype"];
$sessid=$_POST["sessid"];

$history="-1";
$ssllogintype="";
if($ssltype=="ssl" && strlen($id)>0 && strlen($sessid)==32) {
	$ssllogintype="ssl";
	$history="-2";
}

if (strlen($id)>0 && (strlen($passwd)>0 || $ssllogintype=="ssl")) {
	$sql = "SELECT vender, id, disabled FROM tblvenderinfo ";
	$sql.= "WHERE id='".$id."' AND delflag='N' ";
	if($ssllogintype=="ssl") {
		$sql.= "AND authkey='".$sessid."' ";
	} else {

		//$sql.= "AND passwd=md5('".$passwd."') ";
		/* 변경 jdy */
		if ($admin_chk=="1") {
			$sql.= "AND passwd='".$passwd."' ";
		}else{
			$sql.= "AND passwd=md5('".$passwd."') ";
		}
		/* 변경 jdy */
	}
	$result=@mysql_query($sql,get_db_conn());
	if($row=@mysql_fetch_object($result)) {
		$vidx=$row->vender;
		$id = $row->id;
		$disabled = (int)$row->disabled;

		if ($disabled==1) {
			echo "<script> alert(\"해당 업체는 승인 대기상태이므로 로그인이 불가능합니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.\");history.go(".$history."); </script>";
			exit;
		} else {
			$authkey = md5(uniqid(""));

			$_VenderInfo->setVidx($vidx);
			$_VenderInfo->setId($id);
			$_VenderInfo->setAuthkey($authkey);
			$_VenderInfo->Save();

			$_ShopInfo->Save();

			$sql = "UPDATE tblvenderinfo SET authkey='', logindate='".date("YmdHis")."' ";
			$sql.= "WHERE id = '".$id."'";
			$update=@mysql_query($sql,get_db_conn());

			$sql = "INSERT tblvendersession SET ";
			$sql.= "authkey		= '".$authkey."', ";
			$sql.= "vender		= '".$vidx."', ";
			$sql.= "ip			= '".$connect_ip."', ";
			$sql.= "date		= '".date("YmdHis")."' ";
			mysql_query($sql,get_db_conn());

			$log_content = "로그인 : $id";
			$_VenderInfo->ShopVenderLog($vidx,$connect_ip,$log_content);
		}
	} else {
		echo "<script> alert(\"로그인 정보가 올바르지 않습니다.\\n\\n다시 확인하시기 바랍니다.\");history.go(".$history."); </script>";
		exit;
	}
	@mysql_free_result($result);
} else {
	$id = $_VenderInfo->getId();
	$vidx = $_VenderInfo->getVidx();
	$authkey = $_VenderInfo->getAuthkey();

	$sql = "SELECT a.vender FROM tblvenderinfo a, tblvendersession b WHERE a.vender='".$vidx."' AND a.id = '".$id."' ";
	$sql.= "AND a.delflag='N' AND a.vender=b.vender AND b.authkey = '".$authkey."' ";
	$result = @mysql_query($sql,get_db_conn());
	$rows = @mysql_num_rows($result);
	if ($rows <= 0) {
		$_VenderInfo->setId("");
		$_VenderInfo->setVidx("");
		$_VenderInfo->setAuthkey("");
		$_VenderInfo->Save();
		echo "<script> alert(\"정상적인 경로로 다시 접속하시기 바랍니다.\");history.go(".$history."); </script>";
		exit;
	}
	@mysql_free_result($result);
}
?>

<html>
<head>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<title>쇼핑몰 입점 관리자</title>
</head>
<frameset rows="*,0" border=0>
<frame src="main.php" name=bodyframe noresize scrolling=auto marginwidth=0 marginheight=0>
<frame src="blank.php" name=hiddenframe noresize scrolling=no marginwidth=0 marginheight=0>
</frameset>
</body>
</html>