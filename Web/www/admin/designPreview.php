<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-4";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################



$options = "";

$urls=( empty($_POST["urls"]) ) ? "/main/main.php" : $_POST["urls"];

if( $_POST["board"] ) {
	$filename=$_POST["board"];
	$code=$_POST["board"];
}

$code=$_POST["code"];
if( $_POST["bottom_type"] ) $code=$_POST["bottom_type"];

if( $_POST["type"] ) $type=$_POST["type"];
if( $_POST["mode"] ) $type=$_POST["mode"];

	switch ( $type ) {

		case "bmap":
			$urls="/front/productbmap.php";
			break;

		case "brlist":
			$urls="/front/productblist.php?brandcode=".$code;
			break;

		case "rssinfo":
			$urls="/front/rssinfo.php";
			break;

		case "reviewall":
			$urls="/front/reviewall.php";
			break;

		case "surveyview":
			$urls="/front/survey.php?type=result";
			break;

		case "surveylist":
			$urls="/front/survey.php";
			break;

		case "mycustsect":
			$urls="/front/mypage_custsect.php";
			break;

		case "mypersonal":
			$urls="/front/mypage_personal.php";
			break;

		case "myreserve":
			$urls="/front/mypage_reserve.php";
			break;

		case "mycoupon":
			$urls="/front/mypage_coupon.php";
			break;

		case "wishlist":
			$urls="/front/wishlist.php";
			break;

		case "orderlist":
			$urls="/front/mypage_orderlist.php";
			break;

		case "mypage":
			$urls="/front/mypage.php";
			break;

		case "memberout":
			$urls="/front/mypage_memberout.php";
			break;

		case "login":
			$urls="/front/login.php";
			break;

		case "findpwd":
			$urls="/front/findpwd.php";
			break;

		case "mbmodify":
			$urls="/front/mypage_usermodify.php";
			break;

		case "mbjoin":
			$urls="/front/member_join.php";
			break;

		case "joinagree":
			$urls="/front/member_agree.php";
			break;

		case "agreement":
			$urls="/front/agreement.php";
			break;

		case "useinfo":
			$urls="/front/useinfo.php";
			break;

		case "board":
			$urls="/board/board.php?board=".$filename;
			break;

		case "joinmail":
		case "ordermail":
		case "delimail":
		case "bankmail":
		case "passmail":
			$urls="/front/preview_mail.php?type=".$type;
			break;

		case "primgview":
			$urls="/front/primage_multiview.php?productcode=002001000000000013&scroll=yes";
			break;
		case "basket":
			$urls="/front/basket.php";
			break;
		case "search":
			$urls="/front/productsearch.php";
			break;
		case "prnew":
			$urls="/front/productnew.php";
			break;
		case "prbest":
			$urls="/front/productbest.php";
			break;
		case "prhot":
			$urls="/front/producthot.php";
			break;
		case "prspecial":
			$urls="/front/productspecial.php";
			break;
		case "tag":
		case "tagsearch":
			$urls="/front/tag.php";
			break;

		case "prlist":
		case "topmenu":
		case "leftmenu":
			$urls="/front/productlist.php?code=".$code;
			break;

		case "prdetail":
			if( strlen($code) > 0 ) {
				$SearchCode = ($code == "ALL" ) ? "" : $code;
				$prdetailRESULT = mysql_query( "SELECT `productcode` FROM `tblproduct` WHERE `productcode` LIKE '".$SearchCode."%' LIMIT 1; ", get_db_conn() );
				$prdetailROW = mysql_fetch_assoc ( $prdetailRESULT ) ;
				$productCode = $prdetailROW['productcode'];
			}
			$urls="/front/productdetail.php?productcode=".$productCode."";
			break;

		case "bottomTools":

			$imagepath = $Dir.DataDir."shopimages/etc/";
			$btimage_name="btbackground_prev.gif";


			if($code=="1") {
				$ptype="bttoolsetc";
				$pmsg="기본메인설정";

				$up_bottomtools_width=(int)$_POST["up_bottomtools_width"];
				$up_bottomtools_widthmain=(int)$_POST["up_bottomtools_widthmain"];
				$up_bottomtools_height=(int)$_POST["up_bottomtools_height"];
				$up_bottomtools_heightclose=(int)$_POST["up_bottomtools_heightclose"];

				if($up_bottomtools_width>0 || $up_bottomtools_height>0) {
					$up_bottomtools_width_type=($_POST["up_bottomtools_width_type"]=="%"?$_POST["up_bottomtools_width_type"]:"");

					$up_bottomtoolsbgtype=$_POST["up_bottomtoolsbgtype"];
					$up_bgcolor=$_POST["up_bgcolor"];
					$up_bgclear=$_POST["up_bgclear"];

					$up_bgimage = $_FILES['up_bgimage']['tmp_name'];
					$up_bgimage_type = $_FILES['up_bgimage']['type'];
					$up_bgimage_name = $_FILES['up_bgimage']['name'];
					$up_bgimage_size = $_FILES['up_bgimage']['size'];
					$up_bgimage_old = $_POST["up_bgimage_old"];

					$up_bgimagelocat=$_POST["up_bgimagelocat"];
					$up_bgimagerepet=$_POST["up_bgimagerepet"];

					if($up_bottomtoolsbgtype == "I") {
						if(strlen($up_bgimage)>0) {
							if (strlen($up_bgimage_name)>0 && strtolower(substr($up_bgimage_name,strlen($up_bgimage_name)-3,3))=="gif" && $up_bgimage_size<=153600) {
								move_uploaded_file($up_bgimage,$imagepath.$btimage_name);
								chmod($imagepath.$btimage_name,0664);
							} else {
								if (strlen($up_bgimage_name)>0) {
									$msg="올리실 이미지는 150KB 이하의 gif파일만 됩니다.";
								}
							}
						} else {
							if(strlen($up_bgimage_old)==0) {
								$msg="배경 이미지 파일이 선택되지 않았습니다.";
							}
						}
					} else if($up_bottomtoolsbgtype == "B" && strlen($up_bgcolor)==0){
						$msg="배경 색상이 선택되지 않았습니다.";
						@unlink($imagepath.$btimage_name);
					} else {
						@unlink($imagepath.$btimage_name);
					}

					$followetc_str="";
					if ($up_bottomtools_width>0){
						$followetc_str[] = "BTWIDTH=".$up_bottomtools_width.$up_bottomtools_width_type;
					}
					if ($up_bottomtools_widthmain>0){
						$followetc_str[] = "BTWIDTHM=".$up_bottomtools_widthmain;
					}
					if ($up_bottomtools_height>0){
						$followetc_str[] = "BTHEIGHT=".$up_bottomtools_height;
					}
					if ($up_bottomtools_heightclose>0){
						$followetc_str[] = "BTHEIGHTC=".$up_bottomtools_heightclose;
					}
					if(preg_match("/^(N|B|I){1}/", $up_bottomtoolsbgtype)) {
						if($up_bottomtoolsbgtype == "B" && strlen($up_bgcolor)>0) {
							$followetc_str[]= "BTBGTYPE=".$up_bottomtoolsbgtype;
							$followetc_str[]= "BTBGCLEAR=".$up_bgclear;
							$followetc_str[]= "BTBGCOLOR=#".$up_bgcolor;
						} else if($up_bottomtoolsbgtype == "I" && strlen($msg)==0) {
							$followetc_str[]= "BTBGTYPE=".$up_bottomtoolsbgtype;
							$followetc_str[]= "BTBGIMAGELOCAT=".$up_bgimagelocat;
							$followetc_str[]= "BTBGIMAGEREPET=".$up_bgimagerepet;
						} else {
							$followetc_str[]= "BTBGTYPE=N";
						}
					}

					if(count($followetc_str)>0) {
						$body=implode("",$followetc_str);
					} else {
						$body="";
					}
				}
			} else if($code=="2") {
				$ptype="bttools";
				$pmsg="기본메인화면";
			} else if($code=="3") {
				$ptype="bttoolstdy";
				$pmsg="최근 본 상품 본문";
			} else if($code=="4") {
				$ptype="bttoolswlt";
				$pmsg="WishList 본문";
			} else if($code=="5") {
				$ptype="bttoolsbkt";
				$pmsg="장바구니 본문";
			} else if($code=="6") {
				$ptype="bttoolsmbr";
				$pmsg="회원정보 본문";
			}

			copy( $imagepath.$btimage_name , $_FILES['up_bgimage']['tmp_name'] );

			$type = $ptype;

			break;
	}


//echo "<img src=".$imagepath.$btimage_name.">";

if( $_POST["main_body"] ) $body=$_POST["main_body"];
if( $_POST["top_body"] ) $body=$_POST["top_body"];
if( $_POST["bottom_body"] ) $body=$_POST["bottom_body"];


if( $_POST["code"] ) {


	switch($code) {
		case "SEA":
			$urls="/front/productsearch.php";
			break;
		case "productlist.php":
			$design_type=substr($_POST["code"],0,3);
			break;
		case "productdetail.php":
			$design_type=(strlen($_POST["code"])==12?substr($_POST["code"],0,3):substr($_POST["productcode"],0,3));
			break;
		case "BOA":
			$urls="/board/board.php?board=qna";
			break;
		case "BRL":
			$urls="/front/productblist.php";
			break;
		case "BRM":
			$urls="/front/productbmap.php";
			break;
		case "ORD":
			"basket.php";
			$urls="/front/order.php";
			"orderend.php";
			break;

		case "MYP":
			$urls="/front/mypage.php";
			"mypage_coupon.php";
			"mypage_memberout.php";
			"mypage_orderlist.php";
			"mypage_personal.php";
			"mypage_reserve.php";
			"mypage_usermodify.php";
			"mypage_custsect.php";
			"wishlist.php";
			break;
		case "MEM":
			$urls="/front/member_agree.php";
			"member_join.php";
			"login.php";
			"findpwd.php";
			break;
		case "MAI":
			"index.php";
			$urls="/main/main.php";
			"productnew.php";
			"producthot.php";
			"productbest.php";
			"productspecial.php";
			break;
		default:
			$urls=$urls;
	}
}


if($_POST["intitle"]=="Y" OR $_POST["added"] == "Y" ) {
	$leftmenu="Y";
} else {
	$leftmenu="N";
}

// 미리보기 테이블 저장
$returnSaveMSG = adminPreview ( $type, $body, $subject, $code, $filename, $leftmenu );

if ( eregi('\?', $urls) === false ) {
	$urls = $urls."?";
}

$urlView = "http://".$_SERVER['SERVER_NAME'].$urls."&preview".$options;
$pageView = "<iframe width='100%' height='600' src='".$urlView."'></iframe>";
//exit( $returnSaveMSG . $urlView . $pageView . _pr($_POST) . _pr($_FILES) );
header ( "Location: http://".$_SERVER['SERVER_NAME'].$urls."&preview".$options );



?>