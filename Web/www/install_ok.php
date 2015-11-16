<?
$Dir="./";
include ($Dir."lib/init.php");
$install_state=true;
include ($Dir."lib/lib.php");
include ($Dir."schema.php");


if(file_exists($Dir.DataDir."config.php")) {
	error_msg("이미 /".RootPath.DataDir."config.php가 생성되어 있습니다.<br><br>재설치하려면 해당 파일을 지우세요");
}

if(fileperms($Dir.DataDir)!=16839 && fileperms($Dir.DataDir)!=16895) {
	error_msg("<font color=red>현재 707로 퍼미션이 되어 있지 않습니다.</font><br><font color=red><B>(/".RootPath.DataDir.")</B> 텔넷이나 FTP에서 퍼미션을 조정하세요.<font>");
}

if(fileperms($Dir.CashcgiDir."bin")!=16839 && fileperms($Dir.CashcgiDir."bin")!=16895) {
	error_msg("<font color=red>현재 707로 퍼미션이 되어 있지 않습니다.</font><br><font color=red><B>(/".RootPath.CashcgiDir."bin/)</B> 텔넷이나 FTP에서 퍼미션을 조정하세요.<font>");
}

$hostname=$_POST["hostname"];
$user_id=$_POST["user_id"];
$password=$_POST["password"];
$dbname=$_POST["dbname"];


// 호스트네임, 아이디, DB네임 공백여부 검사
if(is_blank($hostname))	error_msg("데이터베이스 Host Name을 입력하세요");
if(is_blank($user_id))	error_msg("데이터베이스 User ID를 입력하세요");
if(is_blank($dbname))	error_msg("데이터베이스 DB명을 입력하세요");

// DB에 커넥트 하고 DB NAME으로 select DB
$connect = @mysql_connect($hostname,$user_id,$password) or error_msg("MySQL-DB Connect<br>Error!!!");


if(mysql_error()) error_msg(mysql_error());
mysql_select_db($dbname, $connect) or error_msg("MySQL-DB Select<br>Error!!!","");


// 테이블 create
for($i=0;$i<count($tbllist);$i++) {
	mysql_query($tbllist[$i],$connect);
	mysql_query("set names euckr");
}


// 데이터 delete
for($i=0;$i<count($deletelist);$i++) {
	@mysql_query($deletelist[$i],$connect);
	mysql_query("set names euckr");
}

// 데이터 insert
for($i=0;$i<count($datalist);$i++) {
	@mysql_query($datalist[$i],$connect);
	mysql_query("set names euckr");
}


$data_dir=$Dir.DataDir;

// 파일로 DB정보 저장
$file=@fopen($data_dir."config.php","w") or error_msg("/".RootPath.DataDir."config.php 파일 생성 실패<br><br>디렉토리의 퍼미션을 707로 주십시요","");
@fwrite($file,"<?\n$hostname\n$user_id\n$password\n$dbname\n?>\n") or error_msg("/".RootPath.DataDir."config.php 파일 생성 실패<br><br>디렉토리의 퍼미션을 707로 주십시요","");
@fclose($file);


// data/ 디렉토리 내에 디렉토리 생성
@mkdir($data_dir."backup");			@chmod($data_dir."backup",0707);
@mkdir($data_dir."cache");			@chmod($data_dir."cache",0707);
@mkdir($data_dir."design");			@chmod($data_dir."design",0707);
@mkdir($data_dir."design_backup");			@chmod($data_dir."design_backup",0707);
@mkdir($data_dir."editor_temp");			@chmod($data_dir."editor_temp",0707);
@mkdir($data_dir."editor");			@chmod($data_dir."editor",0707);
@mkdir($data_dir."groupmail");		@chmod($data_dir."groupmail",0707);
@mkdir($data_dir."htm");		@chmod($data_dir."htm",0707);
@mkdir($data_dir."qrtemp");		@chmod($data_dir."qrtemp",0707);
@mkdir($data_dir."revert");		@chmod($data_dir."revert",0707);
@mkdir($data_dir."shopimages");		@chmod($data_dir."shopimages",0707);
@mkdir($data_dir."ssl");			@chmod($data_dir."ssl",0707);

@mkdir($data_dir."shopimages/auction");		@chmod($data_dir."shopimages/auction",0707);
@mkdir($data_dir."shopimages/banner");		@chmod($data_dir."shopimages/banner",0707);
@mkdir($data_dir."shopimages/board");		@chmod($data_dir."shopimages/board",0707);
@mkdir($data_dir."shopimages/gonggu");		@chmod($data_dir."shopimages/gonggu",0707);
@mkdir($data_dir."shopimages/product");		@chmod($data_dir."shopimages/product",0707);
@mkdir($data_dir."shopimages/productreview");		@chmod($data_dir."shopimages/productreview",0707);
@mkdir($data_dir."shopimages/multi");		@chmod($data_dir."shopimages/multi",0707);
@mkdir($data_dir."shopimages/vender");		@chmod($data_dir."shopimages/vender",0707);
@mkdir($data_dir."shopimages/etc");			@chmod($data_dir."shopimages/etc",0707);
@mkdir($data_dir."shopimages/giftbg");		@chmod($data_dir."shopimages/giftbg",0707);
@mkdir($data_dir."shopimages/multi");		@chmod($data_dir."shopimages/multi",0707);
@mkdir($data_dir."shopimages/mobile");		@chmod($data_dir."shopimages/mobile",0707);

@mkdir($data_dir."shopimages/board/notice");		@chmod($data_dir."shopimages/board/notice",0707);
@mkdir($data_dir."shopimages/board/qna");		@chmod($data_dir."shopimages/board/qna",0707);
@mkdir($data_dir."shopimages/board/faq");		@chmod($data_dir."shopimages/board/faq",0707);
@mkdir($data_dir."shopimages/board/event");		@chmod($data_dir."shopimages/board/event",0707);
@mkdir($data_dir."shopimages/board/storytalk");		@chmod($data_dir."shopimages/board/storytalk",0707);

@mkdir($data_dir."cache/sql");			@chmod($data_dir."cache/sql",0707);
@mkdir($data_dir."cache/rss");			@chmod($data_dir."cache/rss",0707);
@mkdir($data_dir."cache/main");			@chmod($data_dir."cache/main",0707);
@mkdir($data_dir."cache/product");		@chmod($data_dir."cache/product",0707);
@mkdir($data_dir."cache/name");			@chmod($data_dir."cache/name",0707);
@mkdir($data_dir."cache/board");		@chmod($data_dir."cache/board",0707);

@chmod("config.php",0707);

// 관리자 테이블 select 후 등록된 관리자가 없으면 다음 진행
$sql = "SELECT * FROM tblshopinfo ";
$result=mysql_query($sql,$connect);
mysql_query("set names euckr");
if($row=mysql_fetch_object($result)) {
	echo "<html></head><body onload=\"location.replace('".$Dir.AdminDir."')\"></body></html>";exit;
} else {
	echo "<html></head><body onload=\"location.replace('install2.php')\"></body></html>";exit;
}
mysql_close($connect);
?>