<?
if(strlen($Dir)==0) {
	$Dir="../";
}
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if ($_data->frame_type=="N" || strlen($_data->frame_type)==0) {	//투프레임
	if ((strlen($_REQUEST["id"])>0 && strlen($_REQUEST["passwd"])>0) || $_REQUEST["type"]=="logout" || $_REQUEST["type"]=="exit") {
		include($Dir."lib/loginprocess.php");
		exit;
	}
}

if(file_exists($Dir.DataDir."shopimages/etc/logo.gif")) {
	$width = getimagesize($Dir.DataDir."shopimages/etc/logo.gif");
	$logo = "<img src=\"".$Dir.DataDir."shopimages/etc/logo.gif\" border=0 ";
	if($width[0]>200) $logo.="width=200 ";
	if($width[1]>65) $logo.="height=65 ";
	$logo.=">";
} else {
	$logo = "<img src=\"".$Dir."images/".$_data->icon_type."/logo.gif\" border=0>";
}

if ($_data->frame_type=="N") {
	$main_target="target=main";

	$result2 = mysql_query("SELECT rightmargin FROM tbltempletinfo WHERE icon_type='".$_data->icon_type."'",get_db_conn());
	if ($row2=mysql_fetch_object($result2)) $rightmargin=$row2->rightmargin;
	else $rightmargin=0;
	mysql_free_result($result2);

?>

<html>
<head>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<link rel="shortcut icon" href="<?=$Dir?>2010/favicon1.ico" >
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<? include($Dir."lib/style.php") ?>

</head>

<body topmargin=2 leftmargin=2 rightmargin=<?=$rightmargin?> marginheight=0 marginwidth=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">

<?
}

$leftbody="";
switch(substr(strrchr(getenv("SCRIPT_NAME"),"/"),1)) {
	case "productsearch.php":	//검색관련
		$design_type="SEA";
		break;
	case "productlist.php":	//상품 카테고리별
		$design_type=substr($_REQUEST["code"],0,3);
		break;
	case "productdetail.php":
		$design_type=(strlen($_REQUEST["code"])==12?substr($_REQUEST["code"],0,3):substr($_REQUEST["productcode"],0,3));
		break;
	case "board.php":
		$design_type="BOA";
		break;
	case "productblist.php":
		$design_type="BRL";
		break;
	case "productbmap.php":
		$design_type="BRM";
		break;
	case "basket.php":
	case "order.php":
	case "orderend.php":
		$design_type="ORD";
		break;

	case "mypage.php":
	case "mypage_coupon.php":
	case "mypage_memberout.php":
	case "mypage_orderlist.php":
	case "mypage_personal.php":
	case "mypage_reserve.php":
	case "mypage_usermodify.php":
	case "mypage_custsect.php":
	case "wishlist.php":
		$design_type="MYP";
		break;
	case "member_agree.php":
	case "member_join.php":
	case "login.php":
	case "findpwd.php":
		$design_type="MEM";
		break;

	case "community.php":
	case "newpage.php":
		if (strlen($newobj->menu_code)>0 && $newobj->menu_code!="MAI") $design_type=$newobj->menu_code; else $design_type="";
		break;

	case "index.php":
	case "main.php":
	case "productnew.php":
	case "producthot.php":
	case "productbest.php":
	case "productspecial.php":
		$design_type="MAI";
		break;
	default: 
		if (substr(getenv("SCRIPT_NAME"),0,10)=="/main/main") $design_type="MAI";
		else $design_type="";
}

$sql = "SELECT body FROM tbldesignnewpage_prev WHERE type='leftmenu'";
$result=mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$leftbody=$row->body;
	$leftbody=str_replace("[DIR]",$Dir,$leftbody);
	mysql_free_result($result);
} 

//공지사항
$match=array();
$default_lnotice=array("1","Y","Y","4","N","2");
if (preg_match("/\[NOTICE([0-9NY_]{1,9})\]/",$leftbody,$match)) {
	$match_array=explode("_",$match[1]);
	for ($i=0;$i<strlen($match_array[0]);$i++) {
		$default_lnotice[$i]=$match_array[0][$i];
	}
	$lnotice_yn="Y";
}
$lnotice_type=$default_lnotice[0];	// 공지사항 타입
$lnotice_title=$default_lnotice[1];	// 공지사항 타이틀표시여부
$lnotice_gan=$default_lnotice[2];		// 공지사항 사이 간격
$lnotice_new=$default_lnotice[3];		// 공지사항 신규 아이콘 사용여부
$lnotice_timegap=$default_lnotice[4]*24; // 공지사항 신규아이콘 지속 날짜
$lnotice_ganyes="YES";
$lnotice_titlelen=(($match_array[1]+0)>200)?"200":($match_array[1]+0); // 공지사항 글자의 길이

//컨텐츠정보
$match=array();
$default_linfo=array("1","Y","4");
if (preg_match("/\[INFO([0-9NY_]{1,7})\]/",$leftbody,$match)) {
	$match_array=explode("_",$match[1]);
	for ($i=0 ; $i < strlen($match_array[0]) ; $i ++) {
		$default_linfo[$i]=$match_array[0][$i];
	}
	$linfo_yn="Y";
}
$linfo_type=$default_linfo[0];	// 컨텐츠정보 타입
$linfo_title=$default_linfo[1];	// 컨텐츠정보 타이틀표시여부
$linfo_gan=$default_linfo[2];		// 컨텐츠정보 사이 간격
$linfo_ganyes="YES";
$linfo_titlelen=(($match_array[1]+0)>200)?"200":($match_array[1]+0); // 컨텐츠정보 글자의 길이


$shop_count=$_ShopInfo->getShopCount();
$searchkeyword="";
if($posnum=strpos($leftbody,"[SEARCHKEYWORD")) {
	$s_tmp=explode("_",substr($leftbody,$posnum+1,strpos($leftbody,"]",$posnum)-$posnum-1));
	$flength=(int)$s_tmp[1];
	if($flength==0) $flength=80;

	$searchkeyword="<input type=text name=search value=\"".$_POST["search"]."\" onkeydown=\"CheckKeyLeftSearch()\" style=\"width:$flength\">";
}
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
var quickview_path="<?=$Dir.FrontDir?>product.quickview.xml.php";
var quickfun_path="<?=$Dir.FrontDir?>product.quickfun.xml.php";
function sendmail() {
	window.open("<?=$Dir.FrontDir?>email.php","email_pop","height=100,width=100");
}
function estimate(type) {
	if(type=="Y") {
		window.open("<?=$Dir.FrontDir?>estimate_popup.php","estimate_pop","height=100,width=100,scrollbars=yes");
	} else if(type=="O") {
		if(typeof(top.main)=="object") {
			top.main.location.href="<?=$Dir.FrontDir?>estimate.php";
		} else {
			document.location.href="<?=$Dir.FrontDir?>estimate.php";
		}
	}
}
function privercy() {
	window.open("<?=$Dir.FrontDir?>privercy.php","privercy_pop","height=570,width=590,scrollbars=yes");
}
function order_privercy() {
	window.open("<?=$Dir.FrontDir?>privercy.php","privercy_pop","height=570,width=590,scrollbars=yes");
}
function logout() {
	location.href="<?=$Dir.MainDir?>main.php?type=logout";
}
function sslinfo() {
	window.open("<?=$Dir.FrontDir?>sslinfo.php","sslinfo","width=100,height=100,scrollbars=no");
}
function memberout() {
	if(typeof(top.main)=="object") {
		top.main.location.href="<?=$Dir.FrontDir?>mypage_memberout.php";
	} else {
		document.location.href="<?=$Dir.FrontDir?>mypage_memberout.php";
	}
}
function notice_view(type,code) {
	if(type=="view") {	
		window.open("<?=$Dir.FrontDir?>notice.php?type="+type+"&code="+code,"notice_view","width=450,height=450,scrollbars=yes");
	} else {
		window.open("<?=$Dir.FrontDir?>notice.php?type="+type,"notice_view","width=450,height=450,scrollbars=yes");
	}
}
function information_view(type,code) {
	if(type=="view") {	
		window.open("<?=$Dir.FrontDir?>information.php?type="+type+"&code="+code,"information_view","width=600,height=500,scrollbars=yes");
	} else {
		window.open("<?=$Dir.FrontDir?>information.php?type="+type,"information_view","width=600,height=500,scrollbars=yes");
	}
}
function GoPrdtItem(prcode) {
	window.open("<?=$Dir.FrontDir?>productdetail.php?productcode="+prcode,"prdtItemPop","WIDTH=800,HEIGHT=700 left=0,top=0,toolbar=yes,location=yes,directories=yse,status=yes,menubar=yes,scrollbars=yes,resizable=yes");
}

function lpoll_result(type,code) {
	if(type=="result") {
		k=0;
		for (i=0;i<document.lpoll_form.poll_sel.length;i++) {
			if(document.lpoll_form.poll_sel[i].checked) {
				url="<?=$Dir.FrontDir?>survey.php?type=result&survey_code="+code+"&val="+document.lpoll_form.poll_sel[i].value;
				k=1;
			}
		}
		if (k==1) {
			window.open(url,"survey","width=450,height=400,scrollbars=yes");
		} else {
			alert ("투표하실 항목을 선택해 주세요");return;
		}
	} else {
		window.open ("<?=$Dir.FrontDir?>survey.php?type=view&survey_code="+code,"survey","width=450,height=400,scrollbars=yes"); 
	}
}

<?if(substr($_data->layoutdata["MOUSEKEY"],3,1)=="Y"){?>
function funkeyclick() {
    if (navigator.appName=="Netscape" && (e.which==3 || e.which==2)) return;
    else if (navigator.appName=="Microsoft Internet Explorer" && (event.button==2 || event.button==3 || event.keyCode==93)) return;

    if(navigator.appName=="Microsoft Internet Explorer" && (event.ctrlKey && event.keyCode==78)) return false;
}
document.onmousedown=funkeyclick;
document.onkeydown=funkeyclick;
<?}?>
//-->
</SCRIPT>

<?
if(substr($_data->layoutdata["SHOPBGTYPE"],0,1)=="B") {			//배경색 설정
	echo "<style>\n";
	if(substr($_data->layoutdata["SHOPBGTYPE"],1,1)=="Y") {
		echo "#tableposition { background-color: transparent; }\n";
	} else {
		echo "#tableposition { background-color: #FFFFFF; }\n";
	}
	if(substr($_data->layoutdata["BGCOLOR"],0,1)=="N") {
		echo "BODY {background-color: ".(strlen(substr($_data->layoutdata["BGCOLOR"],1,7))==7?substr($_data->layoutdata["BGCOLOR"],1,7):"#FFFFFF")."}\n";
	} else {
		echo "BODY {background-color: transparent}\n";
	}
	echo "</style>\n";
} else if(substr($_data->layoutdata["SHOPBGTYPE"],0,1)=="I") {	//백그라운드 설정
	echo "<style>\n";
	if(substr($_data->layoutdata["SHOPBGTYPE"],1,1)=="N") {
		echo "#tableposition { background-color: #FFFFFF; }\n";
	} else {
		echo "#tableposition { background-color: transparent; }\n";
	}
	if(file_exists($Dir.DataDir."shopimages/etc/background.gif")) {
		echo "BODY {\n";
		echo "background-image: url('".$Dir.DataDir."shopimages/etc/background.gif');\n";
		$background_repeat=array("A"=>"repeat","B"=>"repeat-x","C"=>"repeat-y","D"=>"no-repeat");
		echo "background-repeat: ".$background_repeat[substr($_data->layoutdata["BACKGROUND"],2,1)].";\n";
		$background_position=array("A"=>"top left","B"=>"top center","C"=>"top right","D"=>"center left","E"=>"center center","F"=>"center right","G"=>"bottom left","H"=>"bottom center","I"=>"bottom right");
		echo "background-position: ".$background_position[substr($_data->layoutdata["BACKGROUND"],1,1)].";\n";
		if(substr($_data->layoutdata["BACKGROUND"],0,1)=="Y") {
			echo "background-attachment: fixed;\n";
		}
	}
	echo "</style>\n";
}
?>

<table border=0 width="<?=($_data->layoutdata["SHOPWIDTH"]>0?$_data->layoutdata["SHOPWIDTH"]:"900")?>" cellpadding=0 cellspacing=0 id="tableposition">
<tr>
	<td width=100% valign=top>
	<table border=0 cellpadding=0 cellspacing=0 width=100% height=100%>
	<col width=200></col>
	<col width=></col>
	<tr>
		<td valign=top>
		<!-- 개별디자인 왼쪽메뉴 시작 -->

<?
		if(strlen($leftbody)>0) {
			include ($Dir.MainDir."menu_text.php");
			include ($Dir."lib/leftevent.php");

			$pattern=array("(\[VISIT\])","(\[VISIT2\])","(\[RSS\])","(\[SEARCHFORMSTART\])","(\[SEARCHKEYWORD((\_){0,1})([0-9]{0,3})\])","(\[SEARCHOK\])","(\[SEARCHFORMEND\])","(\[EMAIL\])","(\[LOGIN\])","(\[LOGOUT\])","(\[MEMBEROUT\])","(\[LOGINFORM\])","(\[LOGINFORMU\])","(\[BRANDMAP\])","(\[REVIEW\])","(\[BASKET\])","(\[ORDER\])","(\[PRODUCTNEW\])","(\[PRODUCTBEST\])","(\[PRODUCTHOT\])","(\[PRODUCTSPECIAL\])","(\[RESERVEVIEW\])","(\[MYPAGE\])","(\[MEMBER\])","(\[AUCTION\])","(\[GONGGU\])","(\[ESTIMATE\])","(\[SHOPTEL([a-zA-Z0-9_\/\-.]{0,})\])","(\[BANNER\])","(\[LEFTEVENT\])","(\[NOTICE([1-4]{1})([YN]{0,1})([1-9]{0,1})([YN]{0,1})([1-9]{0,1})(\_){0,1}([0-9]{0,3})\])","(\[INFO([1-4]{1})([YN]{0,1})([1-9]{0,1})(\_){0,1}([0-9]{0,3})\])","(\[SPEITEM(\_N){0,}\])","(\[POLL(\_N){0,}\])","(\[PRLIST([a-zA-Z0-9_?\/\-.]+)\])","(\[BOARDLIST([a-zA-Z0-9_?\/\-.]+)\])","(\[BRANDLIST((\_){0,1})([0-9]{0,3})\])");

			if(strlen($_ShopInfo->getMemid())>0) {
				$replace=array($shop_count,$shop_count." (<a href=\"".$Dir.MainDir."main.php?type=logout\">Logout</a>)",$Dir.FrontDir."rssinfo.php","<form name=search_lform method=get action=\"".$Dir.FrontDir."productsearch.php\">",$searchkeyword,"javascript:LeftSearchCheck()","</form>","javascript:sendmail()","javascript:alert('로그인중입니다.');","javascript:logout()","javascript:memberout()",$left_loginform,$left_loginformu,$Dir.FrontDir."productbmap.php",$Dir.FrontDir."reviewall.php",$Dir.FrontDir."basket.php",$Dir.FrontDir."mypage_orderlist.php",$Dir.FrontDir."productnew.php",$Dir.FrontDir."productbest.php",$Dir.FrontDir."producthot.php",$Dir.FrontDir."productspecial.php",$Dir.FrontDir."mypage_reserve.php",$Dir.FrontDir."mypage.php",$Dir.FrontDir."mypage_usermodify.php",$Dir.AuctionDir."auction.php",$Dir.GongguDir."gonggu.php","javascript:estimate('".$_data->estimate_ok."')",$shoptel,$left_banner,$eventbody,$left_notice,$left_info,$lspeitem,$lpoll,$prlist,$boardlist,$brandlist);
			} else {
				$replace=array($shop_count,$shop_count,$Dir.FrontDir."rssinfo.php","<form name=search_lform method=get action=\"".$Dir.FrontDir."productsearch.php\">",$searchkeyword,"javascript:LeftSearchCheck()","</form>","javascript:sendmail()",$Dir.FrontDir."login.php?chUrl=".getUrl(),$Dir.FrontDir."login.php?chUrl=".getUrl(),$Dir.FrontDir."login.php?chUrl=".getUrl(),$left_loginform,$left_loginformu,$Dir.FrontDir."productbmap.php",$Dir.FrontDir."reviewall.php",$Dir.FrontDir."basket.php",$Dir.FrontDir."mypage_orderlist.php",$Dir.FrontDir."productnew.php",$Dir.FrontDir."productbest.php",$Dir.FrontDir."producthot.php",$Dir.FrontDir."productspecial.php",$Dir.FrontDir."mypage_reserve.php",$Dir.FrontDir."mypage.php",$Dir.FrontDir."member_agree.php",$Dir.AuctionDir."auction.php",$Dir.GongguDir."gonggu.php","javascript:estimate('".$_data->estimate_ok."')",$shoptel,$left_banner,$eventbody,$left_notice,$left_info,$lspeitem,$lpoll,$prlist,$boardlist,$brandlist);
			}
			
			array_push($pattern,"(\[TODAYSALE\])");
			array_push($replace,$Dir.TodaySaleDir);
	
			$leftbody = preg_replace($pattern,$replace,$leftbody);
		} else {
			//$leftbody="왼쪽메뉴 생성이 안되었습니다.";
		}
		echo $leftbody;
?>
		<span style="display:none;"><?=$_data->countpath?></span>
		<!-- 개별디자인 왼쪽메뉴 끝 -->
		</td>
		<td align=center valign=top nowrap>
<?
if ($_data->frame_type=="N") {
?>
</body>
</html>
<?
}
?>