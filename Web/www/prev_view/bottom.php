<?
if(strlen($Dir)==0) {
	$Dir="../";
}
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_main.php");

Header("Pragma: no-cache");

include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/eventpopup.php");

$mainpagemark = "Y"; // 메인 페이지
$selfcodefont_start = "<font class=\"mainselfcode\">"; //진열코드 폰트 시작
$selfcodefont_end = "</font>"; //진열코드 폰트 끝
?>
<!-- ShoppingMall Version <?=_IncomuShopVersionNo?>(<?=_IncomuShopVersionDate?>) //-->
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?></TITLE>
<link rel="P3Pv1" href="http://<?=$_ShopInfo->getShopurl()?>w3c/p3p.xml">
<link rel="shortcut icon" href="<?=$Dir?>2010/favicon1.ico" >
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
<?=$onload?>
//-->
</SCRIPT>
<?include($Dir."lib/style.php")?>

</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<center><script src="../Scripts/common.js" type="text/javascript"></script>
<script type="text/javascript" src="../Scripts/rolling.js"></script>
<link href="../css/in_style.css" rel="stylesheet" type="text/css" />
<link href="../css/new_style.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="../2010/favicon1.ico" >

<style type="text/css">
<!--
.style1 {font-family: "돋움체", "돋움";font-size: 12px;}
a {selector-dummy : expression(this.hideFocus=true);}
a:link {color:#909090;text-decoration: none;}
a:visited {color:#909090;text-decoration: none;}	
a:hover {color:#ce0000;text-decoration: none;}
-->
</style>
<?
$companynum="";

$sql = "SELECT * FROM tbldesignnewpage_prev WHERE type='bottom' ";
$result = mysql_query($sql,get_db_conn());
if($row = mysql_fetch_object($result)) {
	$type=2;
	$bottom_body=$row->body;
		
} 
mysql_free_result($result);

$arcompa=array("-"," ",".","_",",");
$arcomre=array("", "", "", "", "");
$companynum=str_replace($arcompa,$arcomre,$_data->companynum);

if(strlen($companynum)==13) {
	$companynum=substr($companynum,0,6)."-*******";
} else {
	$companynum=substr($companynum,0,3)."-".substr($companynum,3,2)."-".substr($companynum,5);
}
$bottom_body=str_replace("[DIR]",$Dir,$bottom_body);

$pattern=array("(\[URL\])","(\[NAME\])","(\[TEL\])","(\[INFOMAIL\])","(\[COMPANYNAME\])","(\[BIZNUM\])","(\[SALENUM\])","(\[OWNER\])","(\[PRIVERCY\])","(\[ADDRESS\])","(\[HOME\])","(\[USEINFO\])","(\[BASKET\])","(\[COMPANY\])","(\[ESTIMATE\])","(\[BOARD\])","(\[AUCTION\])","(\[GONGGU\])","(\[EMAIL\])","(\[RESERVEVIEW\])","(\[LOGIN\])","(\[LOGOUT\])","(\[PRIVERCYVIEW\])","(\[CONTRACT\])","(\[MEMBER\])","(\[MYPAGE\])","(\[ORDER\])","(\[RSS\])","(\[PRODUCTNEW\])","(\[PRODUCTBEST\])","(\[PRODUCTHOT\])","(\[PRODUCTSPECIAL\])");
$replacelogin=array("http://".$_ShopInfo->getShopurl()." target=_top",$_data->shopname,$_data->info_tel,$_data->info_email,$_data->companyname,$companynum,$_data->reportnum,$_data->companyowner,"<a href=\"mailto:".$_data->privercyemail."\">".$_data->privercyname."</a>",$_data->info_addr,$Dir.MainDir."main.php",$Dir.FrontDir."useinfo.php",$Dir.FrontDir."basket.php",$Dir.FrontDir."company.php","\"JavaScript:estimate()\"",$Dir.BoardDir."board.php?board=qna",$Dir.AuctionDir."auction.php",$Dir.GongguDir."gonggu.php","\"JavaScript:sendmail()\"",$Dir.FrontDir."mypage_reserve.php","\"JavaScript:alert('로그인중입니다.');\"",$Dir.MainDir."top.php?type=logout","\"JavaScript:privercy()\"",$Dir.FrontDir."agreement.php",$Dir.FrontDir."mypage_usermodify.php",$Dir.FrontDir."mypage.php",$Dir.FrontDir."mypage_orderlist.php",$Dir.FrontDir."rssinfo.php",$Dir.FrontDir."productnew.php",$Dir.FrontDir."productbest.php",$Dir.FrontDir."producthot.php",$Dir.FrontDir."productspecial.php");
$replacelogout=array("http://".$_ShopInfo->getShopurl()." target=_top",$_data->shopname,$_data->info_tel,$_data->info_email,$_data->companyname,$companynum,$_data->reportnum,$_data->companyowner,"<a href=\"mailto:".$_data->privercyemail."\">".$_data->privercyname."</a>",$_data->info_addr,$Dir.MainDir."main.php",$Dir.FrontDir."useinfo.php",$Dir.FrontDir."basket.php",$Dir.FrontDir."company.php","\"JavaScript:estimate()\"",$Dir.BoardDir."board.php?board=qna",$Dir.AuctionDir."auction.php",$Dir.GongguDir."gonggu.php","\"JavaScript:sendmail()\"",$Dir.FrontDir."mypage_reserve.php",$Dir.FrontDir."login.php?chUrl=".(isset($_REQUEST["chUrl"])?$_REQUEST["chUrl"]:""),"\"JavaScript:alert('먼저 로그인하세요.');\"","\"JavaScript:privercy()\"",$Dir.FrontDir."agreement.php",$Dir.FrontDir."member_agree.php",$Dir.FrontDir."mypage.php",$Dir.FrontDir."mypage_orderlist.php",$Dir.FrontDir."rssinfo.php",$Dir.FrontDir."productnew.php",$Dir.FrontDir."productbest.php",$Dir.FrontDir."producthot.php",$Dir.FrontDir."productspecial.php");


array_push($pattern,"(\[TODAYSALE\])");
array_push($replacelogin,$Dir.TodaySaleDir);
array_push($replacelogout,$Dir.TodaySaleDir);


if (strlen($_ShopInfo->getMemid())>0) {
	$bottom_body = preg_replace($pattern,$replacelogin,$bottom_body);
} else {
	$bottom_body = preg_replace($pattern,$replacelogout,$bottom_body);
}

?>
		<?if($type=="1"){?>
		<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<tr>
			<td><?=$bottom_body?></td>
		</tr>
		</table>
		<?}?>
		</td>
	</tr>
	</table>

	<?if($type=="2"){?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td><?=$bottom_body?></td>
	</tr>
	</table>
	<?}?>
	</td>

</tr>
</table>

<SCRIPT LANGUAGE="JavaScript">
<!--
var RightAreaAll=new Array();
function RightArea() {
	var argv = RightArea.arguments;   
	var argc = RightArea.arguments.length;
	
	this.classname		= "RightArea"
	this.debug			= false;
	this.id				= new String((argc > 0) ? argv[0] : "");
	this.x_to			= new String((argc > 1) ? argv[1] : "");
	this.y_to			= new String((argc > 2) ? argv[2] : "");
	this.scroll			= new String((argc > 3) ? argv[3] : "Y");
}
//-->
</SCRIPT>

<?
//오른쪽 최근 본 상품 및 Quick메뉴 시작
$right_body="";
$isRightBanner=false;
if($_data->quick_type==0) {
	//최근 본 상품 쿠키정보가 있는지 검사 후 있으면 아래 처리 ($_COOKIE[ViewProduct])
	//if(strlen($_COOKIE[ViewProduct])>0) {
		$isRightBanner=true;
		$sql = "SELECT * FROM tbldesignnewpage WHERE type='r_banner' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$tmp=explode("",$row->subject);
			$x_to=$tmp[0];	//왼쪽위치
			$y_to=$tmp[1];	//위쪽위치
			
			$scroll_auto=$row->leftmenu;	//스크롤 타입

			$right_body.="<div id=RightNewprdt style=\"position:absolute;display:hidden;\">\n";
			$right_body.="<script>var right_area=new RightArea(); right_area.id='RightNewprdt'; right_area.x_to='".$x_to."'; right_area.y_to='".$y_to."'; right_area.scroll='".$scroll_auto."'; RightAreaAll[RightAreaAll.length]=right_area; right_area=null;</script>\n";
			$right_body.="<script language=\"javascript\" src=\"".$Dir.FrontDir."right_newproduct.php\"></script>\n";
			$right_body.="</div>\n";
		}
		mysql_free_result($result);
	//}
}

$sql="SELECT * FROM tblquickmenu WHERE used='Y' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$isRightBanner=true;
	$right_body.="<div id=RightBanner style=\"position:absolute;display:hidden;\">\n";
	$right_body.="<script>var right_area=new RightArea(); right_area.id='RightBanner'; right_area.x_to='".$row->x_to."'; right_area.y_to='".$row->y_to."'; right_area.scroll='".$row->scroll_auto."'; RightAreaAll[RightAreaAll.length]=right_area; right_area=null;</script>\n";
	$right_body.="<script language=\"javascript\" src=\"".$Dir.FrontDir."right_quickmenu.php\"></script>\n";
	$right_body.="</div>\n";
}
mysql_free_result($result);

if($isRightBanner) {
	$right_body.= "<SCRIPT LANGUAGE=\"JavaScript\">\n";
	$right_body.= "<!--\n";
	$right_body.= "var isDOM = (document.getElementById ? true : false);\n";
	$right_body.= "var isIE4 = ((document.all && !isDOM) ? true : false);\n";
	$right_body.= "var isNS4 = (document.layers ? true : false);\n";
	$right_body.= "var isNS = navigator.appName == 'Netscape';\n";
	$right_body.= "window.onresize = WindowResize;\n";
	
	$right_body.= "function WindowResize(){\n";
	$right_body.= "	if (isNS4) {\n";
	$right_body.= "		for(i=0;i<RightAreaAll.length;i++) {\n";
	$right_body.= "			RightB = document[RightAreaAll[i].id];\n";
	$right_body.= "			RightB.top = top.pageYOffset + parseInt(RightAreaAll[i].y_to);\n";
	$right_body.= "			RightB.visibility = 'visible';\n";
	$right_body.= "			if(RightAreaAll[i].scroll=='Y') {\n";
	$right_body.= "				MoveRightBanner(i);\n";
	$right_body.= "			}\n";
	$right_body.= "		}\n";
	$right_body.= "	} else if (isDOM) {\n";
	$right_body.= "		for(i=0;i<RightAreaAll.length;i++) {\n";
	$right_body.= "			RightB = getRightObj(RightAreaAll[i].id);\n";
	$right_body.= "			RightB.style.top = (isNS ? window.pageYOffset : document.body.scrollTop) + parseInt(RightAreaAll[i].y_to);\n";
	$right_body.= "			RightB.style.left = (isNS ? window.pageXOffset+100 : document.all.tableposition.offsetLeft) + parseInt(RightAreaAll[i].x_to);\n";
	$right_body.= "			RightB.style.visibility = 'visible';\n";
	$right_body.= "			if(RightAreaAll[i].scroll=='Y') {\n";
	$right_body.= "				MoveRightBanner(i);\n";
	$right_body.= "			}\n";
	$right_body.= "		}\n";
	$right_body.= "	}\n";
	$right_body.= "}\n";

	$right_body.= "function getRightObj(id) {\n";
	$right_body.= "	if (isDOM) return document.getElementById(id);\n";
	$right_body.= "	if (isIE4) return document.all[id];\n";
	$right_body.= "	if (isNS4) return document.layers[id];\n";
	$right_body.= "}\n";

	$right_body.= "function MoveRightBanner(idx) {\n";
	$right_body.= "	var yMenuFrom, yMenuTo, yOffset, timeoutNextCheck;\n";
	$right_body.= "	if (isNS4) {\n";
	$right_body.= "		RightB = document[RightAreaAll[idx].id];\n";
	$right_body.= "		yMenuFrom   = RightB.top;\n";
	$right_body.= "		yMenuTo     = windows.pageYOffset + parseInt(RightAreaAll[idx].y_to);\n";
	$right_body.= "	} else if (isDOM) {\n";
	$right_body.= "		RightB = getRightObj(RightAreaAll[idx].id);\n";
	$right_body.= "		yMenuFrom   = parseInt (RightB.style.top, 10);\n";
	$right_body.= "		yMenuTo     = (isNS ? window.pageYOffset : document.body.scrollTop) + parseInt(RightAreaAll[idx].y_to);\n";
	$right_body.= "	}\n";
	$right_body.= "	timeoutNextCheck = 300;\n";
	$right_body.= "	if (yMenuFrom != yMenuTo) {\n";
	$right_body.= "		yOffset = Math.ceil(Math.abs(yMenuTo - yMenuFrom) / 20);\n";
	$right_body.= "		if (yMenuTo < yMenuFrom) yOffset = -yOffset;\n";
	$right_body.= "		if (isNS4) RightB.top += yOffset;\n";
	$right_body.= "		else if (isDOM) RightB.style.top = parseInt (RightB.style.top, 10) + yOffset;\n";
	$right_body.= "		timeoutNextCheck = 10;\n";
	$right_body.= "	}\n";
	$right_body.= "	setTimeout (\"MoveRightBanner(\"+idx+\")\", timeoutNextCheck);\n";
	$right_body.= "}\n";

	$right_body.= "if (isNS4) {\n";
	$right_body.= "	for(i=0;i<RightAreaAll.length;i++) {\n";
	$right_body.= "		RightB = document[RightAreaAll[i].id];\n";
	$right_body.= "		RightB.top = top.pageYOffset + parseInt(RightAreaAll[i].y_to);\n";
	$right_body.= "		RightB.visibility = 'visible';\n";
	$right_body.= "		if(RightAreaAll[i].scroll=='Y') {\n";
	$right_body.= "			MoveRightBanner(i);\n";
	$right_body.= "		}\n";
	$right_body.= "	}\n";
	$right_body.= "} else if (isDOM) {\n";
	$right_body.= "	for(i=0;i<RightAreaAll.length;i++) {\n";
	$right_body.= "		RightB = getRightObj(RightAreaAll[i].id);\n";
	$right_body.= "		RightB.style.top = (isNS ? window.pageYOffset : document.body.scrollTop) + parseInt(RightAreaAll[i].y_to);\n";
	$right_body.= "		RightB.style.left = (isNS ? window.pageXOffset+100 : document.all.tableposition.offsetLeft) + parseInt(RightAreaAll[i].x_to);\n";
	$right_body.= "		RightB.style.visibility = 'visible';\n";
	$right_body.= "		if(RightAreaAll[i].scroll=='Y') {\n";
	$right_body.= "			MoveRightBanner(i);\n";
	$right_body.= "		}\n";
	$right_body.= "	}\n";
	$right_body.= "}\n";

	$right_body.= "function RightNewprdtClose() {\n";
	$right_body.= "	if (isNS4) {\n";
	$right_body.= "		RightB=document['RightNewprdt'];\n";
	$right_body.= "		RightB.visibility='hidden';\n";
	$right_body.= "	} else if (isDOM) {\n";
	$right_body.= "		RightB = getRightObj('RightNewprdt');\n";
	$right_body.= "		RightB.style.visibility='hidden';\n";
	$right_body.= "	}\n";
	$right_body.= "}\n";
	$right_body.= "//-->\n";
	$right_body.= "</SCRIPT>\n";
}

echo $right_body;
?>
<?
if($_data->ETCTYPE["BOTTOMTOOLS"]!="Y" && strlen($_vscriptname)>0 && $_vscriptname!=FrontDir."order.php" && $_vscriptname!=FrontDir."orderend.php") {
	$bottomtools_width="100%";
	$bottomtools_height="238";
	$bottomtools_heightclose="29";
	$bottomtools_widthmain=($_data->layoutdata["SHOPWIDTH"]>0?$_data->layoutdata["SHOPWIDTH"]:"900");
	$bottomtools_background = "background:transparent url('".$Dir."images/common/btbackground.gif') repeat-x scroll 0 0;";

	$sql = "SELECT body FROM tbldesignnewpage WHERE type='bttoolsetc' ";
	$result = mysql_query($sql,get_db_conn());
	if($row = @mysql_fetch_object($result)) {	// 하단 폴로메뉴 전체 개별디자인 설정 적용
		mysql_free_result($result);
		unset($followetcdata);
		if(strlen($row->body)>0) {
			$followetctemp=explode("",$row->body);
			$followetccnt=count($followetctemp);
			if($followetccnt>1) {
				for ($followetci=0;$followetci<$followetccnt;$followetci++) {
					$followetctemp2=explode("=",$followetctemp[$followetci]);
					if(isset($followetctemp2[1])) {
						$followetcdata[$followetctemp2[0]]=$followetctemp2[1];
					} else {
						$followetcdata[$followetctemp2[0]]="";
					}
				}

				if(strlen($followetcdata["BTWIDTH"])>0 && strlen($followetcdata["BTWIDTH"])>0) {
					if(substr($followetcdata["BTWIDTH"],-1)=="%") {
						$bottomtools_width=((int)substr($followetcdata["BTWIDTH"],0,-1)).substr($followetcdata["BTWIDTH"],-1);
					} else {
						$bottomtools_width=(int)$followetcdata["BTWIDTH"];
					}
					$bottomtools_widthmain=(int)$followetcdata["BTWIDTHM"];
					$bottomtools_height=(int)$followetcdata["BTHEIGHT"];
					$bottomtools_heightclose=(int)(int)$followetcdata["BTHEIGHTC"];
					
					if($followetcdata["BTBGTYPE"]=="B") {
						if(strlen($followetcdata["BTBGCOLOR"])>0) {
							if($followetcdata["BTBGCLEAR"]=="Y") {
								$bottomtools_background = "background-color:transparent;";
							} else {
								$bottomtools_background = "background-color:".(strlen($followetcdata["BTBGCOLOR"])>0?$followetcdata["BTBGCOLOR"]:"#FFFFFF").";";
							}
						} else {
							$bottomtools_background = "background-color:#FFFFFF;";
						}
					} else if($followetcdata["BTBGTYPE"]=="I") {
						if(strlen($followetcdata["BTBGIMAGEREPET"])>0 && strlen($followetcdata["BTBGIMAGELOCAT"])>0 && file_exists($Dir.DataDir."shopimages/etc/btbackground.gif")) {
							$btbackground_repeatarr=array("A"=>"repeat","B"=>"repeat-x","C"=>"repeat-y","D"=>"no-repeat");
							$btbackground_positionarr=array("A"=>"top left","B"=>"top center","C"=>"top right","D"=>"center left","E"=>"center center","F"=>"center right","G"=>"bottom left","H"=>"bottom center","I"=>"bottom right");
							$bottomtools_background = "background:transparent url('".$Dir.DataDir."shopimages/etc/btbackground.gif') ".$btbackground_repeatarr[$followetcdata["BTBGIMAGEREPET"]]." scroll ".$btbackground_positionarr[$followetcdata["BTBGIMAGELOCAT"]].";";
						} else {
							$bottomtools_background = "background-color:#FFFFFF;";
						}
					} else {
						$bottomtools_background = "";
					}
				}
			}
		}
	}
	
	function setFontStyle($strtemp) {
		$s_tmpstyle="";
		if(strlen($strtemp)>0) {
			$strtemp_exp = explode("|",$strtemp);
			if(count($strtemp_exp)>0) {
				if(strlen($strtemp_exp[0])>0) {
					$s_tmpstyle="font-size:".$strtemp_exp[0].";";
				}
				if(strlen($strtemp_exp[1])>0) {
					$s_tmpstyle="color:".$strtemp_exp[1].";";
				}
				if(strlen($strtemp_exp[2])>0) {
					if($strtemp_exp[2]=="Y") {
						$s_tmpstyle="font-weight:bold;";
					} else {
						$s_tmpstyle="font-weight:normal;";
					}
				}
				if(strlen($strtemp_exp[3])>0) {
					if($strtemp_exp[3]=="Y") {
						$s_tmpstyle="text-decoration:underline;";
					} else {
						$s_tmpstyle="text-decoration:none;";
					}
				}
			}
		}
		return $s_tmpstyle;
	}
	$followgstyletoday="";
	$followsstyletoday="|#FF3C00|N|N;";
	$followgstylewishlist="";
	$followsstylewishlist="|#FF3C00|N|N";
	$followgstylebasket="";
	$followsstylebasket="|#FF3C00|N|N";
	$followgstylemember="";
	$followsstylemember="|#FF3C00|N|N";
	$followopenimg=$Dir."images/common/btopen.gif";
	$followcloseimg=$Dir."images/common/btclose.gif";
	$sql = "SELECT body FROM tbldesignnewpage WHERE type='bttools' ";
	$result = mysql_query($sql,get_db_conn());
	if($row = mysql_fetch_object($result)) {
		mysql_free_result($result);
		if(strlen($row->body)>0) {
			$bttoolsbarok = "Y";
			$bttools_body=str_replace("[DIR]",$Dir,$row->body);
			$num=strpos($bttools_body,"[TODAYCHANGE_");
			if($num!==false) {
				$s_tmp=explode("_",substr($bttools_body,$num+1,strpos($bttools_body,"]",$num)-$num-1));
				$followtodaylink="\"javascript:setFollowSelect('Today');\" id=\"TitleIdToday\" style=\"".setFontStyle($s_tmp[1])."\"";
				$followgstyletoday=$s_tmp[1];
				$followsstyletoday=$s_tmp[2];
			}
			$num=strpos($bttools_body,"[WISHLISTCHANGE_");
			if($num!==false) {
				$s_tmp=explode("_",substr($bttools_body,$num+1,strpos($bttools_body,"]",$num)-$num-1));
				$followwishlistlink="\"javascript:setFollowSelect('Wishlist');\" id=\"TitleIdWishlist\" style=\"".setFontStyle($s_tmp[1])."\"";
				$followgstylewishlist=$s_tmp[1];
				$followsstylewishlist=$s_tmp[2];
			}
			$num=strpos($bttools_body,"[BASKETCHANGE_");
			if($num!==false) {
				$s_tmp=explode("_",substr($bttools_body,$num+1,strpos($bttools_body,"]",$num)-$num-1));
				$followbasketlink="\"javascript:setFollowSelect('Basket');\" id=\"TitleIdBasket\" style=\"".setFontStyle($s_tmp[1])."\"";
				$followgstylebasket=$s_tmp[1];
				$followsstylebasket=$s_tmp[2];
			}
			$num=strpos($bttools_body,"[MEMBERCHANGE_");
			if($num!==false) {
				$s_tmp=explode("_",substr($bttools_body,$num+1,strpos($bttools_body,"]",$num)-$num-1));
				$followmemberlink="\"javascript:setFollowSelect('Member');\" id=\"TitleIdMember\" style=\"".setFontStyle($s_tmp[1])."\"";
				$followgstylemember=$s_tmp[1];
				$followsstylemember=$s_tmp[2];
			}
			$num=strpos($bttools_body,"[OPENCLOSEIMG_");
			if($num!==false) {
				$s_tmp=explode("_",substr($bttools_body,$num+1,strpos($bttools_body,"]",$num)-$num-1));
				$followopenlink="\"javascript:setFollowDivAction();\" id=\"FollowOpenCloseImg\"";
				$followopenimg=$s_tmp[1];
				$followcloseimg=$s_tmp[2];
			}
			$pattern=array("(\[TODAYCHANGE((\_){0,1})([0-9a-zA-Z\|\_\#]){0,}\])","(\[WISHLISTCHANGE((\_){0,1})([0-9a-zA-Z\|\_\#]){0,}\])","(\[BASKETCHANGE((\_){0,1})([0-9a-zA-Z\|\_\#]){0,}\])","(\[MEMBERCHANGE((\_){0,1})([0-9a-zA-Z\|\_\#]){0,}\])","(\[OPENCLOSEIMG([a-zA-Z0-9_?\/\-.]+)\])","(\[OPENCLOSECHANGE\])","(\[TODAYCNT\])","(\[WISHLISTCNT\])","(\[BASKETCNT\])");

			$replace=array($followtodaylink,$followwishlistlink,$followbasketlink,$followmemberlink,"\"".$followopenimg."\"",$followopenlink,"<span id=\"CountIdToday\"> </span>","<span id=\"CountIdWishlist\"> </span>","<span id=\"CountIdBasket\"> </span>");
			$bttools_body = preg_replace($pattern,$replace,$bttools_body);
		}
	}
?>
<div id="DefaultFollowLocat"></div>
<div id="FollowControlBar" style="position:absolute;z-index:10000;top:expression(document.body.scrollTop+document.body.clientHeight-this.clientHeight);left:0px;width:<?=$bottomtools_width?>;<?=$bottomtools_background?>overflow-x:visible;overflow-y:hidden;">
	<div style="left:0px;width:100%;overflow-x:visible;overflow-y:hidden;">
	<div id="FollowControlDiv" style="<?=($_data->align_type=="Y"?"":"left:0px;")?>width:<?=$bottomtools_widthmain?>;height:<?=$bottomtools_height?>;">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td id="FollowControlBarTd" height="<?=$bottomtools_heightclose?>">
<?
	if($bttoolsbarok=="Y") {
		echo $bttools_body;
	} else {
?>
		<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width="10"></col>
		<col width="130"></col>
		<col width="34"></col>
		<col width=""></col>
		<col width="34"></col>
		<col width="100"></col>
		<tr>
			<td style="background:url('<?=$Dir?>images/common/tab_left.gif') 0 0 no-repeat;"></td>
			<td width="130" style="background:transparent url('<?=$Dir?>images/common/tab_title_bg.gif') repeat-x scroll 0 0;"><img src="<?=$Dir?>images/common/tab_title.gif" border="0"></td>
			<td style="background:url('<?=$Dir?>images/common/tab_sort1.gif') 0 0 no-repeat;"></td>
			<td style="background:transparent url('<?=$Dir?>images/common/tab_sort_bg.gif') repeat-x scroll 0 0;" height="100%">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed">
			<col width=""></col>
			<col width="34"></col>
			<col width=""></col>
			<col width="34"></col>
			<col width=""></col>
			<col width="34"></col>
			<col width=""></col>
			<tr>
				<td style="text-align:center;vertical-align:bottom;padding-bottom:3px;font-size:11px;"><span id="TitleIdToday" style="cursor:hand;" onClick="setFollowSelect('Today');">최근 본 상품<span style="font-size:5px;padding-left:10px;padding-right:11px;">|</span><span id="CountIdToday" style="font-weight:bold;"> </span></span></td>
				<td style="background:url('<?=$Dir?>images/common/tab_sort2.gif') 0 0 no-repeat;"></td>
				<td style="text-align:center;vertical-align:bottom;padding-bottom:3px;font-size:11px;"><span id="TitleIdWishlist" style="cursor:hand;" onClick="setFollowSelect('Wishlist');">Wishlist<span style="font-size:5px;padding-left:10px;padding-right:11px;">|</span><span id="CountIdWishlist" style="font-weight:bold;"> </span></span></td>
				<td style="background:url('<?=$Dir?>images/common/tab_sort2.gif') 0 0 no-repeat;"></td>
				<td style="text-align:center;vertical-align:bottom;padding-bottom:3px;font-size:11px;"><span id="TitleIdBasket" style="cursor:hand;" onClick="setFollowSelect('Basket');">장바구니<span style="font-size:5px;padding-left:10px;padding-right:11px;">|</span><span id="CountIdBasket" style="font-weight:bold;"> </span></span></td>
				<td style="background:url('<?=$Dir?>images/common/tab_sort2.gif') 0 0 no-repeat;"></td>
				<td style="text-align:center;vertical-align:bottom;padding-bottom:3px;font-size:11px;"><span id="TitleIdMember" style="cursor:hand;" onClick="setFollowSelect('Member');">회원정보</span></td>
			</tr>
			</table>
			</td>
			<td style="background:url('<?=$Dir?>images/common/tab_sort3.gif') 0 0 no-repeat;"></td>
			<td style="text-align:right;vertical-align:bottom;font-size:11px;background:transparent url('<?=$Dir?>images/common/tab_title_bg.gif') repeat-x scroll 0 0;"><img src="<?=$Dir?>images/common/btopen.gif" id="FollowOpenCloseImg" border="0" style="cursor:hand;" onClick="setFollowDivAction();"></td>
		</tr>
		</table>
<?
	}
?>
		</td>
	</tr>
	<tr>
		<td><div id="FollowDivBasket" style="display:none;position:absolute;width:100%;"></div>
		<div id="FollowDivToday" style="display:none;position:absolute;width:100%;"></div>
		<div id="FollowDivWishlist" style="display:none;position:absolute;width:100%;"></div>
		<div id="FollowDivMember" style="display:none;position:absolute;width:100%;"></div></td>
	</tr>
	</table>
	</div>
	</div>
</div>

<script type="text/javascript">
<!--
// 하단 따라다니는 메뉴 변수 셋팅
var FollowCurrentDiv = "";			// 현재 선택 메뉴
var FollowDivArr = new Array("Member","Today","Wishlist","Basket"); // 메뉴, 마지막 배열값은 최초 선택된 값으로
var FollowFuncPath="<?=$Dir.FrontDir."follow.func.xml.php"?>"; // Ajax 호춯 파일
var FollowCloseHeight=0;			// Close 상태의 높이
var FollowOpenHeight=0;				// Open 상태의 높이
var FollowScrollHeightDefault=0;	// 스크롤 처리 필요 변수
var FollowDivTop=0;					// Open, Close 필요 변수
var FollowDivOffset=0;				// Open, Close 필요 변수
var FollowDivSetTObj;				// setTimeout 세션 변수
var FollowSStyleToday="<?=$followsstyletoday?>";			// 현재 선택 메뉴 글 색상
var FollowGStyleToday="<?=$followgstyletoday?>";			// 선택 메뉴를 제외한 글 색상
var FollowSStyleWishlist="<?=$followsstylewishlist?>";		// 현재 선택 메뉴 글 색상
var FollowGStyleWishlist="<?=$followgstylewishlist?>";		// 선택 메뉴를 제외한 글 색상
var FollowSStyleBasket="<?=$followsstylebasket?>";			// 현재 선택 메뉴 글 색상
var FollowGStyleBasket="<?=$followgstylebasket?>";			// 선택 메뉴를 제외한 글 색상
var FollowSStyleMember="<?=$followsstylemember?>";			// 현재 선택 메뉴 글 색상
var FollowGStyleMember="<?=$followgstylemember?>";			// 선택 메뉴를 제외한 글 색상
var FollowOpenImg="<?=$followopenimg?>";	//열림버튼이미지
var FollowCloseImg="<?=$followcloseimg?>";	//닫힘버튼이미지
var FollowSelectID="";				// 현재 선택된 메뉴 ID
if(typeof(setFollowInit)!="undefined") {
	setFollowInit(FollowDivArr);	// 기본 셋팅 호출
}
//-->
</script>

<?
}
?>