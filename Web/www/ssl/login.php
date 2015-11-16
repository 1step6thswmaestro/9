<?
if(getenv("HTTPS")!="on") {
	header("HTTP/1.0 404 Not Found");
	exit;
}

#회원로그인
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$type=$_POST["type"];
$shopurl=$_POST["shopurl"];
$id=$_POST["id"];
$passwd=$_POST["passwd"];

if(!eregi($shopurl,getenv("HTTP_REFERER"))) {
	exit;
}

if(strlen($type)==0) $type="login";
if(strlen($shopurl)==0 || strlen($id)==0 || strlen($passwd)==0) {
	echo "<html><head><title></title></head><body onload=\"alert('로그인 정보가 올바르지 않습니다.');history.go(-1);\"></body></html>";exit;
}

unset($passwd_type);
$sql = "SELECT passwd FROM tblmember WHERE id='".$id."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	if(substr($row->passwd,0,3)=="$1$") {
		$passwd_type="hash";
		$hashdata=$row->passwd;
	} else if(strlen($row->passwd)==16) {
		$passwd_type="password";
		$chksql = "SELECT PASSWORD('1') AS passwordlen ";
		$chkresult=mysql_query($chksql,get_db_conn());
		if($chkrow=mysql_fetch_object($chkresult)) {
			if(strlen($chkrow->passwordlen)==41 && substr($chkrow->passwordlen,0,1)=="*") {
				$passwd_type="old_password";
			}
		}
		mysql_free_result($chkresult);
	} else {
		$passwd_type="md5";
	}
} else {
	echo "<html><head><title></title></head><body onload=\"alert('아이디 또는 비밀번호가 틀립니다.');history.go(-1);\"></body></html>";exit;
}
mysql_free_result($result);

$sql = "SELECT member_out FROM tblmember WHERE id='".$id."' ";
if($passwd_type=="hash") {
	$sql.= "AND passwd='".crypt($passwd, $hashdata)."' ";
} else if($passwd_type=="password") {
	$sql.= "AND passwd=password('".$passwd."')";
} else if($passwd_type=="old_password") {
	$sql.= "AND passwd=old_password('".$passwd."')";
} else if($passwd_type=="md5") {
	$sql.= "AND passwd=md5('".$passwd."')";
}
$result = @mysql_query($sql,get_db_conn());
if($row=@mysql_fetch_object($result)) {
	if($row->member_out=="Y") {	//탈퇴한 회원
		echo "<html><head><title></title></head><body onload=\"alert('아이디 또는 비밀번호가 틀리거나 탈퇴한 회원입니다.');history.go(-1);\"></body></html>";exit;
	}
	$authidkey = md5(uniqid(""));
	$sql = "UPDATE tblmember SET authidkey='".$authidkey."' ";
	if($passwd_type=="hash" || $passwd_type=="password" || $passwd_type=="old_password") {
		$sql.= ",passwd='".md5($passwd)."' ";
	}
	$sql.= "WHERE id='".$id."' ";
	@mysql_query($sql,get_db_conn());

	if($type=="login") {
		echo "<html><head><title></title></head><body>\n";
		echo "<form name=form1 method=post action=\"http://".$shopurl."/".RootPath.FrontDir."loginproc.php\">\n";
		echo "<input type=hidden name=ssltype value=\"ssl\">\n";
		echo "<input type=hidden name=type value=\"".$type."\">\n";
		echo "<input type=hidden name=id value=\"".$id."\">\n";
		echo "<input type=hidden name=sessid value=\"".$authidkey."\">\n";
		echo "<input type=hidden name=nexturl value=\"".$HTTP_REFERER."\">\n";
		echo "</form>\n";
		echo "<script>document.form1.submit();</script>\n";
		echo "</body></html>\n";
	} else if($type=="adultlogin" || $type=="btblogin") {
		echo "<html><head><title></title></head><body>\n";
		echo "<form name=form1 method=post action=\"http://".$shopurl."/".RootPath."\">\n";
		echo "<input type=hidden name=ssltype value=\"ssl\">\n";
		echo "<input type=hidden name=type value=\"".$type."\">\n";
		echo "<input type=hidden name=id value=\"".$id."\">\n";
		echo "<input type=hidden name=sessid value=\"".$authidkey."\">\n";
		echo "</form>\n";
		echo "<script>document.form1.submit();</script>\n";
		echo "</body></html>\n";
	}
	exit;
} else {
	echo "<html><head><title></title></head><body onload=\"alert('아이디 또는 비밀번호가 틀립니다.');history.go(-1);\"></body></html>";exit;
}
@mysql_free_result($result);
?>