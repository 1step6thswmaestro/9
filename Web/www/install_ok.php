<?
$Dir="./";
include ($Dir."lib/init.php");
$install_state=true;
include ($Dir."lib/lib.php");
include ($Dir."schema.php");


if(file_exists($Dir.DataDir."config.php")) {
	error_msg("�̹� /".RootPath.DataDir."config.php�� �����Ǿ� �ֽ��ϴ�.<br><br>�缳ġ�Ϸ��� �ش� ������ ���켼��");
}

if(fileperms($Dir.DataDir)!=16839 && fileperms($Dir.DataDir)!=16895) {
	error_msg("<font color=red>���� 707�� �۹̼��� �Ǿ� ���� �ʽ��ϴ�.</font><br><font color=red><B>(/".RootPath.DataDir.")</B> �ڳ��̳� FTP���� �۹̼��� �����ϼ���.<font>");
}

if(fileperms($Dir.CashcgiDir."bin")!=16839 && fileperms($Dir.CashcgiDir."bin")!=16895) {
	error_msg("<font color=red>���� 707�� �۹̼��� �Ǿ� ���� �ʽ��ϴ�.</font><br><font color=red><B>(/".RootPath.CashcgiDir."bin/)</B> �ڳ��̳� FTP���� �۹̼��� �����ϼ���.<font>");
}

$hostname=$_POST["hostname"];
$user_id=$_POST["user_id"];
$password=$_POST["password"];
$dbname=$_POST["dbname"];


// ȣ��Ʈ����, ���̵�, DB���� ���鿩�� �˻�
if(is_blank($hostname))	error_msg("�����ͺ��̽� Host Name�� �Է��ϼ���");
if(is_blank($user_id))	error_msg("�����ͺ��̽� User ID�� �Է��ϼ���");
if(is_blank($dbname))	error_msg("�����ͺ��̽� DB���� �Է��ϼ���");

// DB�� Ŀ��Ʈ �ϰ� DB NAME���� select DB
$connect = @mysql_connect($hostname,$user_id,$password) or error_msg("MySQL-DB Connect<br>Error!!!");


if(mysql_error()) error_msg(mysql_error());
mysql_select_db($dbname, $connect) or error_msg("MySQL-DB Select<br>Error!!!","");


// ���̺� create
for($i=0;$i<count($tbllist);$i++) {
	mysql_query($tbllist[$i],$connect);
	mysql_query("set names euckr");
}


// ������ delete
for($i=0;$i<count($deletelist);$i++) {
	@mysql_query($deletelist[$i],$connect);
	mysql_query("set names euckr");
}

// ������ insert
for($i=0;$i<count($datalist);$i++) {
	@mysql_query($datalist[$i],$connect);
	mysql_query("set names euckr");
}


$data_dir=$Dir.DataDir;

// ���Ϸ� DB���� ����
$file=@fopen($data_dir."config.php","w") or error_msg("/".RootPath.DataDir."config.php ���� ���� ����<br><br>���丮�� �۹̼��� 707�� �ֽʽÿ�","");
@fwrite($file,"<?\n$hostname\n$user_id\n$password\n$dbname\n?>\n") or error_msg("/".RootPath.DataDir."config.php ���� ���� ����<br><br>���丮�� �۹̼��� 707�� �ֽʽÿ�","");
@fclose($file);


// data/ ���丮 ���� ���丮 ����
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

// ������ ���̺� select �� ��ϵ� �����ڰ� ������ ���� ����
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