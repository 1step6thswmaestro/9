<?
if(substr(getenv("SCRIPT_NAME"),-10)=="access.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}

$_PartnerInfo = new _PartnerInfo($_COOKIE[_pinfo]);

$url = getenv("HTTP_HOST");
if (strpos($url,FreeDomain)!=false) {
	$url = substr($url,0,strpos($url,"."));
}

$history="-1";
$ssllogintype="";
if($_POST["ssltype"]=="ssl" && strlen($_POST["id"])>0 && strlen($_POST["sessid"])==32) {
	$ssllogintype="ssl";
	$history="-2";
}

if (strlen($_POST["id"])>0 && (strlen($_POST["passwd"])>0 || $ssllogintype=="ssl")) {
	$sql = "SELECT * FROM tblpartner WHERE id='".$_POST["id"]."' ";
	if($ssllogintype=="ssl") {
		$sql.= "AND authkey='".$sessid."' ";
	} else {
		$sql.= "AND passwd='".$_POST["passwd"]."'";
	}
	$result = mysql_query($sql,get_db_conn());
	if ($row=mysql_fetch_object($result)) {
		$partner_id = $row->id;
		$partner_authkey = md5(uniqid(""));
		mysql_query("UPDATE tblpartner SET authkey='".$partner_authkey."' WHERE id='".$partner_id."'",get_db_conn());
		$_PartnerInfo->setJoindate($joindate);
		$_PartnerInfo->setPartnerid($partner_id);
		$_PartnerInfo->setPartnerauthkey($partner_authkey);
		$_PartnerInfo->Save();
	} else {
		echo "<script>alert('아이디 또는 패스워드가 틀립니다.');history.go(".$history.");</script>";
		mysql_free_result($result);
		exit;
	}
	mysql_free_result($result);
} else if (strlen($_PartnerInfo->getPartnerid())==0) {
	echo "<script>alert('로그인을 하셔야 이용이 가능합니다.');location.href='".$Dir.PartnerDir."';</script>";
	exit;
} else if($_GET["type"]=="logout") {
	$_PartnerInfo->setJoindate("");
	$_PartnerInfo->setPartnerid("");
	$_PartnerInfo->setPartnerauthkey("");
	$_PartnerInfo->Save();
	echo "<html><head><title></title></head><body onload=\"location.href='".$Dir.PartnerDir."'\"></body></html>";
	exit;
}

if (strlen($_PartnerInfo->getPartnerid())>0 && strlen($_PartnerInfo->getPartnerauthkey())>0) {
	$sql = "SELECT hit_cnt FROM tblpartner ";
	$sql.= "WHERE id='".$_PartnerInfo->getPartnerid()."' AND authkey='".$_PartnerInfo->getPartnerauthkey()."' ";
	$result = mysql_query($sql,get_db_conn());
	if ($row=mysql_fetch_object($result)) {
		$hit_cnt = $row->hit_cnt;
	} else {
		echo "<script>alert('로그인은 한명만 하실 수 있습니다.');location.href='".$Dir.PartnerDir."';</script>";
		mysql_free_result($result);
		exit;
	}
	mysql_free_result($result);
}
?>