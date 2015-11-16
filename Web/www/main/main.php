<?
if(strlen($Dir)==0) $Dir="../";

include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/eventpopup.php");
include_once($Dir."lib/ext/product_func.php");

//모바일 자동 이동 사용 여부
$mobileResult = mysql_query("select * from tblmobileconfig");
$mobileRow = mysql_fetch_array($mobileResult);
if($mobileRow[use_auto_redirection]=="Y" && $mobileRow[use_mobile_site] == "Y") {

	// 모바일브라우저 체크 //
	$_SESSION[chk_BW]="none";
	if ( $_GET['pc'] == "ON" ) $_SESSION[chk_BW]="pc";
	if (eregi("PSP|Symbian|Nokia|LGT|mobile|Mobile|Mini|iphone|SAMSUNG|Windows Phone|Android|Galaxy", $_SERVER['HTTP_USER_AGENT']) AND $_SESSION[chk_BW]!="pc" ) $_SESSION[chk_BW]="mobile";

	if( $_SESSION[chk_BW] == "mobile" ){
		$Qs=(strlen($_SERVER["QUERY_STRING"])>0)?'?'.$_SERVER["QUERY_STRING"]:"";
		header("Location:http://".$_SERVER['SERVER_NAME']."/m/".$Qs);
		exit;
	}
}

$mainpagemark = "Y"; // 메인 페이지
$selfcodefont_start = "<font class=\"mainselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트 끝

include_once($Dir.MainDir.$_data->main_type.".php");

if($HTML_CACHE_EVENT=="OK") ob_end_flush();
?>