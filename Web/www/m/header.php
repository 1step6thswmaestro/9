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

	$usesite = isset($configRow['use_mobile_site'])?trim($configRow['use_mobile_site']):""; // 모바일 사이트 사용여부
	$skinname = isset($configRow['skin'])?trim($configRow['skin']):"defalut1"; // 모바일 사이트 스킨명
	$skinfile = isset($configRow['skin_css'])?trim($configRow['skin_css']):""; // 모바일 사이트 스킨별 CSS 파일
	$logofile = isset($configRow['logo'])?trim($configRow['logo']):""; // 모바일 사이트 상단 로고파일 명
	$iconfile = isset($configRow['icon'])?trim($configRow['icon']):""; // 모바일 사이트 아이콘 파일 명
	$text_copyright = isset($configRow['copyright_text'])?trim($configRow['copyright_text']):""; //텍스트 하단 카피라이터
	$image_copyright = isset($configRow['copyright_image'])?trim($configRow['copyright_image']):""; // 이미지 하단 카피라이터
	$mainsort = isset($configRow['main_item_sort'])?trim($configRow['main_item_sort']):""; // 메인 정렬 순서
	
	mysql_free_result($configRes);
}

//모바일사이트 사용여부
if($usesite=="N"){
	header("Location:/");
	exit;
}

$charset = "EUC-KR";
$shopname = $_data->shopname;
$configPATH = $Dir."m/upload/"; //상단 로고, 아이콘, 카피라이트 저장 경로
$skinPATH = $Dir."m/skin/".$skinname."/";
$logo = $configPATH.$logofile; // 로고
$icon = $configPATH.$iconfile; // 아이콘
$mobliePATH = $Dir."m/";

include($skinPATH."header.php");

if ($image_copyright != "") {
	$copyright = "<img src=".$configPATH.$image_copyright." border=0 style=\"border-width:1pt; border-color:rgb(235,235,235); border-style:solid;\">";
} else {
	$copyright = $text_copyright;
}
?>
