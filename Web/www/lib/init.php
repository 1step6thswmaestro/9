<?

if(substr(getenv("SCRIPT_NAME"),-9)=="/init.php") {
	header("HTTP/1.0 404 Not Found");
	exit;
}
$install_state = false;
header("Content-Type: text/html; charset=euc-kr");
ini_set ( "display_errors",0 );

define("DirPath", $Dir);
define("RootPath", "");

define("AdminDir", "admin/");
define("MainDir", "main/");
define("AdultDir", "adult/");
define("AuctionDir", "auction/");
define("BoardDir", "board/");
define("FrontDir", "front/");
define("GongguDir", "gonggu/");
define("PartnerDir", "partner/");
define("RssDir", "rss/");
define("TempletDir", "templet/");
define("SecureDir", "ssl/");
define("VenderDir", "vender/");
define("CashcgiDir", "cash.cgi/");
define("AuthkeyDir", "authkey/");

define("DataDir", "data/");

define("TodaySaleDir", "todayshop/");

define("MinishopType", "OFF");

#암호/복호화 키입니다. (해당 쇼핑몰에서 꼭 수정하시기 바랍니다.)
define("enckey", "password");

#시스템 관리자 메일
define("AdminMail", "");



// 도매 가격 적용 상품 아이콘
$wholeSaleIconSet = "<img src='/images/common/wholeSaleIcon.gif' style=\"position:relative; top:0.2em;\" alt='' />";

//  벤더 대표 이미지 경로
$com_image_url = $Dir."data/shopimages/vender/";

?>