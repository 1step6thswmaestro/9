<?
$Dir = "../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/func.php");
include_once($Dir."lib/class/pages.php");

include_once($Dir."m/inc/function.php");
//include_once("./inc/lib.php");

$configSQL = "SELECT * FROM tblmobileconfig ";
if(false !== $configRes = mysql_query($configSQL,get_db_conn())){	
	$configRow = mysql_fetch_assoc($configRes);

	$usesite = isset($configRow['use_mobile_site'])?trim($configRow['use_mobile_site']):""; // ����� ����Ʈ ��뿩��
	$skinname = isset($configRow['skin'])?trim($configRow['skin']):"defalut1"; // ����� ����Ʈ ��Ų��
	$skinfile = isset($configRow['skin_css'])?trim($configRow['skin_css']):""; // ����� ����Ʈ ��Ų�� CSS ����
	$logofile = isset($configRow['logo'])?trim($configRow['logo']):""; // ����� ����Ʈ ��� �ΰ����� ��
	$iconfile = isset($configRow['icon'])?trim($configRow['icon']):""; // ����� ����Ʈ ������ ���� ��
	$text_copyright = isset($configRow['copyright_text'])?trim($configRow['copyright_text']):""; //�ؽ�Ʈ �ϴ� ī�Ƕ�����
	$image_copyright = isset($configRow['copyright_image'])?trim($configRow['copyright_image']):""; // �̹��� �ϴ� ī�Ƕ�����
	$mainsort = isset($configRow['main_item_sort'])?trim($configRow['main_item_sort']):""; // ���� ���� ����
	
	mysql_free_result($configRes);
}

//����ϻ���Ʈ ��뿩��
if($usesite=="N"){
	header("Location:/");
	exit;
}

$charset = "EUC-KR";
$shopname = $_data->shopname;
$configPATH = $Dir."m/upload/"; //��� �ΰ�, ������, ī�Ƕ���Ʈ ���� ���
$skinPATH = $Dir."m/skin/".$skinname."/";
$logo = $configPATH.$logofile; // �ΰ�
$icon = $configPATH.$iconfile; // ������
$mobliePATH = $Dir."m/";

include($skinPATH."header.php");

if ($image_copyright != "") {
	$copyright = "<img src=".$configPATH.$image_copyright." border=0 style=\"border-width:1pt; border-color:rgb(235,235,235); border-style:solid;\">";
} else {
	$copyright = $text_copyright;
}
?>
