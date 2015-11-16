<?
if(getenv("HTTPS")!="on") {
	header("HTTP/1.0 404 Not Found");
	exit;
}

#성인몰 실명인증
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$shopurl=$_POST["shopurl"];
$name=$_POST["name"];
$adult_no1=$_POST["adult_no1"];
$adult_no2=$_POST["adult_no2"];

if(!eregi($shopurl,getenv("HTTP_REFERER"))) {
	exit;
}

if(strlen($shopurl)==0 || strlen($name)==0 || strlen($adult_no1)==0 || strlen($adult_no2)==0) {
	echo "<html><head><title></title></head><body onload=\"alert('성인실명인증에 필요한 정보가 올바르지 않습니다.');history.go(-1)\"></body></html>";exit;
}

$errmsg="";
$resno=$adult_no1.$adult_no2;
if(strlen($resno)!=13) {
	$errmsg="주민등록번호 입력이 잘못되었습니다.";
} else if(!chkResNo($resno)) {
	$errmsg="잘못된 주민등록번호 입니다.\\n\\n확인 후 다시 입력하시기 바랍니다.";
} else if(getAgeResno($resno)<19) {
	$errmsg="본 쇼핑몰은 성인만 이용가능합니다.";
} else {
	//처리작업
	$procdata=array();
	$sessid=md5(uniqid(rand(),1)).md5(uniqid(rand(),1));
	$procdata["name"]=$name;
	$procdata["adult_no1"]=$adult_no1;
	$procdata["adult_no2"]=$adult_no2;
	$fp=fopen($Dir.DataDir."ssl/".$sessid.".temp","w");
	fputs($fp, serialize($procdata));
	fclose($fp);

	echo "<html><head><title></title></head><body>\n";
	echo "<form name=form1 method=post action=\"http://".$shopurl."/".RootPath."\">\n";
	echo "<input type=hidden name=type value=\"adultcheck\">\n";
	echo "<input type=hidden name=ssltype value=\"ssl\">\n";
	echo "<input type=hidden name=sessid value=\"".$sessid."\">\n";
	echo "</form>\n";
	echo "<script>document.form1.submit();</script>\n";
	echo "</body></html>\n";
	exit;
}

if(strlen($errmsg)>0) {
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1)\"></body></html>";exit;
}

?>