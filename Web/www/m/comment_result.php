<?

	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include ($Dir."board/lib.inc.php");
	include ($Dir."board/file.inc.php");
$up_name=$_POST["up_name"];
$up_passwd=$_POST["up_passwd"];
$up_comment=$_POST["up_comment"];



if ($setup[use_comment] != "Y") {
	$errmsg="�ش� �Խ����� ��� ����� �������� �ʽ��ϴ�.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
}

if ($member[grant_comment]!="Y") {
	$errmsg="��۾��� ������ �����ϴ�.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
}

if(!eregi($_SERVER[HTTP_HOST],$_SERVER[HTTP_REFERER])) {
	$errmsg="�߸��� ��η� �����ϼ̽��ϴ�.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
}


if(isNull($up_comment)) {
	$errmsg="������ �Է��ϼž� �մϴ�.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
}

if(strlen($member[name])==0) {
	if(isNull($up_name)) {
		$errmsg="�̸��� �Է��ϼž� �մϴ�.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
	}
	if(isNull($up_passwd)) {
		$errmsg="��й�ȣ�� �Է��ϼž� �մϴ�.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
	}
} else {
	$up_name = $member[name];
}


$up_name = addslashes($up_name);
$up_comment = autoLink($up_comment);
$up_comment = addslashes($up_comment);

if ($setup[use_filter] == "1") {
	if (isFilter($setup[filter],$up_comment,$findFilter)) {
		$errmsg="��� ���ѵ� �ҷ��ܾ ����ϼ̽��ϴ�.\\n\\n�ٽ� Ȯ�� �Ͻʽÿ�.";
		echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
	}
}

$check = mysql_fetch_array(mysql_query("SELECT num FROM tblboard WHERE board='$board' AND num = '$num'",get_db_conn()));
if(!$check[0]) {
	$errmsg="���� ���� �������� �ʽ��ϴ�.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
}


// ���� ���

$filename = "";
if( $_FILES['img']['error'] == 0 AND $_FILES['img']['size'] > 0 ) {
	if( !eregi("image/", $_FILES['img']['type'] ) ) {
		echo "<html><head><title></title></head><body onload=\"alert('�̹��� ������ ���ϸ� ���ε� �����մϴ�.');history.go(-1);\"></body></html>";exit;
	}
	
	$filename = "cmt_".time()."_".preg_replace("^[a-zA-Z0-9\-]+$","",$_FILES['img']['name']);
	ProcessBoardDir($board,"create");
	move_uploaded_file($_FILES['img']['tmp_name'],DirPath.DataDir."shopimages/board/".$board."/".$filename);
}




$sql  = "INSERT INTO tblboardcomment (board,parent,name,passwd,ip,writetime,comment,id,file) VALUES ";
$sql .= "('".$board."','".$num."','".$up_name."','".$up_passwd."','".$_SERVER[REMOTE_ADDR]."','".time()."','".$up_comment."', '".$_ShopInfo->getMemid()."','".$filename."')";
$insert = mysql_query($sql,get_db_conn());

// �ڸ�Ʈ ������ ���ؼ� ����
$total=mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM tblboardcomment WHERE board='".$board."' AND parent='".$num."'",get_db_conn()));
mysql_query("UPDATE tblboard SET total_comment='".$total[0]."' WHERE board='".$board."' AND num='".$num."'",get_db_conn());


header("Location:board_view.php?num=$num&board=$board");exit;

?>