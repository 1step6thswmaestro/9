<?
if(getenv("HTTPS")!="on") {
	header("HTTP/1.0 404 Not Found");
	exit;
}

#회원 아이디/패스워드 찾기
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$shopurl=$_POST["shopurl"];
$name=$_POST["name"];
$jumin1=$_POST["jumin1"];
$jumin2=$_POST["jumin2"];
$email=$_POST["email"];

if(strlen($shopurl)==0 || strlen($name)==0 || (strlen($email)==0 && (strlen($jumin1)==0 || strlen($jumin2)==0))) {
	echo "<html><head><title></title></head><body onload=\"alert('성인실명인증에 필요한 정보가 올바르지 않습니다.');history.go(-1)\"></body></html>";exit;
}

//처리작업
$procdata=array();
$sessid=md5(uniqid(rand(),1)).md5(uniqid(rand(),1));
$procdata["name"]=$name;
$procdata["jumin1"]=$jumin1;
$procdata["jumin2"]=$jumin2;
$procdata["email"]=$email;
$fp=fopen($Dir.DataDir."ssl/".$sessid.".temp","w");
fputs($fp, serialize($procdata));
fclose($fp);

echo "<html><head><title></title></head><body>\n";
echo "<form name=form1 method=post action=\"http://".$shopurl."/".RootPath.FrontDir."findpwd.php\">\n";
echo "<input type=hidden name=mode value=\"send\">\n";
echo "<input type=hidden name=ssltype value=\"ssl\">\n";
echo "<input type=hidden name=sessid value=\"".$sessid."\">\n";
echo "</form>\n";
echo "<script>document.form1.submit();</script>\n";
echo "</body></html>\n";
exit;

?>