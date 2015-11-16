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

if($_data->align_type=="Y") echo "<center>";

$topbody="";
$istopmenu=false;
if($_data->frame_type!="N") {	//원프레임에서만 상단세부메뉴 개별디자인이 가능하다. (기본메뉴는 가능)
	$design_type="";
	$sql = "SELECT body FROM tbldesignnewpage_prev WHERE type='topmenu'";
	$result=mysql_query($sql,get_db_conn());
	if ($row=mysql_fetch_object($result)) {
		$topbody=$row->body;
		$topbody=str_replace("[DIR]",$Dir,$topbody);
		$istopmenu=true;
	}
	mysql_free_result($result);
}

if(strlen($topbody)>0) {
	$match=array();
	$default_tnotice=array("1","Y","Y","4","N","2");
	if (preg_match("/\[NOTICE([0-9NY_]{1,9})\]/",$topbody,$match)) {
		$match_array=explode("_",$match[1]);
		for ($i=0;$i<strlen($match_array[0]);$i++) {
			$default_tnotice[$i]=$match_array[0][$i];
		}
		$tnotice_yn="Y";
	}
	$tnotice_type=$default_tnotice[0];	// 공지사항 타입
	$tnotice_title=$default_tnotice[1];	// 공지사항 타이틀표시여부
	$tnotice_gan=$default_tnotice[2];		// 공지사항 사이 간격
	$tnotice_new=$default_tnotice[3];		// 공지사항 신규 아이콘 사용여부
	$tnotice_timegap=$default_tnotice[4]*24; // 공지사항 신규아이콘 지속 날짜
	$tnotice_ganyes="YES";
	$tnotice_titlelen=(($match_array[1]+0)>200)?"200":($match_array[1]+0); // 공지사항 글자의 길이

	################# 공지사항 ##################
	$tnotice="";
	if($tnotice_yn=="Y") {
		$tnotice.="<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
		if($tnotice_title=="Y") {
			$tnotice.="<tr>\n";
			$tnotice.="	<td align=center><A HREF=\"javascript:notice_view('list','')\" onmouseover=\"window.status='공지사항조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=\"".$Dir.DataDir."design/main_notice_title.gif\" border=0 alt=\"공지사항\"></A></td>\n";
			$tnotice.="</tr>\n";
		}
		$tnotice.="<tr>\n";
		$tnotice.="	<td style=\"padding:5\">\n";
		$sql = "SELECT date,subject FROM tblnotice ORDER BY date DESC LIMIT ".$_data->main_notice_num;
		$result=mysql_query($sql,get_db_conn());
		$i=0;
		while($row=mysql_fetch_object($result)) {
			$i++;
			$date="[".substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."]";
			if($tnotice_new=="Y") {
				$ntime=mktime((substr($row->date,8,2)*1),(substr($row->date,10,2)*1),0,(substr($row->date,4,2)*1),(substr($row->date,6,2)*1),(substr($row->date,0,4)*1))+($tnotice_timegap*60*60);
				$nicon="";
				if($ntime>time()) $nicon=" <img src=\"".$Dir."images/common/new.gif\" border=0 align=absmiddle>";
			}
			$tnotice.="<table border=0 cellpadding=0 cellspacing=0>\n";
			$tnotice.="<tr><td>";
			if($tnotice_type=="1") {
				$nfstr="".$i.". ";
			} else if($tnotice_type=="2") {
				$nfstr="".$date." ";
			} else if($tnotice_type=="3") {
				$nfstr="<img src=\"".$Dir."images/noticedot.gif\" border=0 align=absmiddle>";
			} else if($tnotice_type=="4") {
				$nfstr="";
			}
			$tnotice.="<A HREF=\"javascript:notice_view('view','".$row->date."')\" onmouseover=\"window.status='공지사항조회';return true;\" onmouseout=\"window.status='';return true;\"><FONT class=\"mainnotice\">".$nfstr."".($tnotice_titlelen>0?titleCut($tnotice_titlelen,$row->subject):$row->subject)."</FONT></A>".$nicon;
			$tnotice.="</td></tr>\n";
			$tnotice.="<tr><td height=".$tnotice_gan."></td></tr>\n";
			$tnotice.="</table>\n";
		}
		mysql_free_result($result);
		if($i==0) {
			$tnotice.="<table border=0 cellpadding=0 cellspacing=0>\n";
			$tnotice.="<tr><td align=center class=\"mainnotice\">등록된 공지사항이 없습니다.</td></tr>";
			$tnotice.="</table>";
		}
		$tnotice.="	</td>\n";
		$tnotice.="</tr>\n";
		$tnotice.="</table>\n";
	}

	############### 로그인 관련 ###################
	if (strpos($topbody,"[LOGINFORM]")) {
		if (strlen($_ShopInfo->getMemid())>0) {
			if ($_ShopInfo->getMemreserve()>0) {
				$reserve_message= "현재적립금 : ".number_format($_ShopInfo->getMemreserve())."원";
			}
			$top_loginform="";
			$top_loginform.="<table border=0 cellpadding=0 cellspacing=0>\n";
			$top_loginform.="<tr>\n";
			$top_loginform.="	<td>\n";
			$top_loginform.="	<font color=orange><b>".$_ShopInfo->getMemname()."</b></font>님 환영합니다.<br>".$reserve_message."\n";
			$top_loginform.="	</td>\n";
			$top_loginform.="</tr>\n";
			$top_loginform.="<tr>\n";
			$top_loginform.="	<td align=center style=\"padding-top:10\">\n";
			$top_loginform.="	<A HREF=\"".$Dir.FrontDir."mypage_usermodify.php\" $main_target>정보수정</A> | <A HREF=\"".$Dir.MainDir."main.php?type=logout\" $main_target>로그아웃</A>\n";
			$top_loginform.="	</td>\n";
			$top_loginform.="</tr>\n";
			$top_loginform.="</table>\n";
		} else {
			$top_loginform ="<table border=0 cellpadding=0 cellspacing=0>\n";
			$top_loginform.="<form name=toploginform method=post action=".$Dir.MainDir."main.php $main_target>\n";
			$top_loginform.="<input type=hidden name=type value=login>\n";
			if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {
				$top_loginform.="<input type=hidden name=shopurl value=\"".getenv("HTTP_HOST")."\">\n";
				$top_loginform.="<input type=hidden name=sslurl value=\"https://".$_data->ssl_domain.($_data->ssl_port!="443"?":".$_data->ssl_port:"")."/".RootPath.SecureDir."login.php\">\n";
				$top_loginform.="<IFRAME id=toploginiframe name=toploginiframe style=\"display:none\"></IFRAME>";
			}
			$top_loginform.="<tr>\n";
			$top_loginform.="	<td>\n";
			$top_loginform.="	<table border=0 cellpadding=0 cellspacing=0>\n";
			$top_loginform.="	<tr>\n";
			$top_loginform.="		<td style=\"padding-left:4\"><input type=text name=id maxlength=20 style=\"width:100\"></td>\n";
			$top_loginform.="	</tr>\n";
			$top_loginform.="	<tr>\n";
			$top_loginform.="		<td style=\"padding-left:4\"><input type=password name=passwd maxlength=20 onkeydown=\"TopCheckKeyLogin()\" style=\"width:100\"></td>\n";
			$top_loginform.="	</tr>\n";
			$top_loginform.="	</table>\n";
			$top_loginform.="	</td>\n";
			$top_loginform.="	<td style=\"padding-left:5\"><a href=\"javascript:top_login_check()\"><img src=".$Dir."images/btn_login.gif border=0></a></td>\n";
			$top_loginform.="</tr>\n";
			$top_loginform.="<tr>\n";
			$top_loginform.="	<td colspan=2>";
			if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {
				$top_loginform.="	<input type=checkbox name=ssllogin value=Y><A HREF=\"javascript:sslinfo()\">보안 접속</A>";
			}
			$top_loginform.= "	</td>\n";
			$top_loginform.="</tr>\n";
			$top_loginform.="</form>\n";
			$top_loginform.="<tr>\n";
			$top_loginform.="	<td colspan=2 style=\"padding-left:4;padding-top:7\">\n";
			$top_loginform.="	<A HREF=\"/store/member_agree.php\" $main_target><B>회원가입</B></A> <B>|</B> <A HREF=\"".$Dir.FrontDir."findpwd.php\" $main_target><B>비밀번호 분실</B></A>\n";
			$top_loginform.="	</td>\n";
			$top_loginform.="</tr>\n";
			$top_loginform.="</table>\n";
		}
	} else if (strpos($topbody,"[LOGINFORMU]")) {
		if (strlen($_ShopInfo->getMemid())>0) {
			$sql = "SELECT body FROM tbldesignnewpage WHERE type='logoutform' ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			$top_loginformu=$row->body;
			$top_loginformu=str_replace("[DIR]",$Dir,$top_loginformu);
			mysql_free_result($result);
			$pattern_logout=array("(\[ID\])","(\[NAME\])","(\[RESERVE\])","(\[LOGOUT\])","(\[MEMBEROUT\])","(\[MEMBER\])","(\[MYPAGE\])","(\[TARGET\])");
			$replace_logout=array($_ShopInfo->getMemid(),$_ShopInfo->getMemname(),number_format($_ShopInfo->getMemreserve()),$Dir.MainDir."main.php?type=logout","javascript:memberout()",$Dir.FrontDir."mypage_usermodify.php",$Dir.FrontDir."mypage.php","");
			$top_loginformu = preg_replace($pattern_logout,$replace_logout,$top_loginformu);
		} else {
			$sql = "SELECT body FROM tbldesignnewpage WHERE type='loginform' ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			$top_loginformu=$row->body;
			$top_loginformu=str_replace("[DIR]",$Dir,$top_loginformu);
			mysql_free_result($result);
			$idfield="";
			if($posnum=strpos($top_loginformu,"[ID")) {
				$s_tmp=explode("_",substr($top_loginformu,$posnum+1,strpos($top_loginformu,"]",$posnum)-$posnum-1));
				$idflength=(int)$s_tmp[1];
				if($idflength==0) $idflength=80;

				$idfield="<input type=text name=id maxlength=20 style=\"width:$idflength\">";
			}
			$pwfield="";
			if($posnum=strpos($top_loginformu,"[PASSWD")) {
				$s_tmp=explode("_",substr($top_loginformu,$posnum+1,strpos($top_loginformu,"]",$posnum)-$posnum-1));
				$pwflength=(int)$s_tmp[1];
				if($pwflength==0) $pwflength=80;

				$pwfield="<input type=password name=passwd maxlength=20 onkeydown=\"TopCheckKeyLogin()\" style=\"width:$pwflength\">";
			}
			$pattern_login=array("(\[ID(\_){0,1}([0-9]{0,3})\])","(\[PASSWD(\_){0,1}([0-9]{0,3})\])","(\[SSLCHECK\])","(\[SSLINFO\])","(\[OK\])","(\[JOIN\])","(\[FINDPWD\])","(\[LOGIN\])","(\[TARGET\])");
			$replace_login=array($idfield,$pwfield,"<input type=checkbox name=ssllogin value=Y>","javascript:sslinfo()","javascript:top_login_check()",$Dir.FrontDir."member_agree.php $main_target",$Dir.FrontDir."findpwd.php $main_target",$Dir.FrontDir."login.php $main_target",$main_target);
			$top_loginformu = preg_replace($pattern_login,$replace_login,$top_loginformu);
		}
		$top_loginformu="<table border=0 cellpadding=0 cellspacing=0>\n<form name=toploginform method=post action=".$Dir.MainDir."main.php $main_target>\n<tr>\n<td>\n".$top_loginformu."\n<input type=hidden name=type value=login>\n";
		if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {
			$top_loginformu.="<input type=hidden name=shopurl value=\"".getenv("HTTP_HOST")."\">\n<input type=hidden name=sslurl value=\"https://".$_data->ssl_domain.($_data->ssl_port!="443"?":".$_data->ssl_port:"")."/".RootPath.SecureDir."login.php\">\n<IFRAME id=toploginiframe name=toploginiframe style=\"display:none\"></IFRAME>\n";
		}
		$top_loginformu.="</td>\n</tr>\n</form>\n</table>\n";
	}

	$searchkeyword="";
	if($posnum=strpos($topbody,"[SEARCHKEYWORD")) {
		$s_tmp=explode("_",substr($topbody,$posnum+1,strpos($topbody,"]",$posnum)-$posnum-1));
		$flength=(int)$s_tmp[1];
		if($flength==0) $flength=80;

		if($_data->search_info["autosearch"]=="Y" && $_data->frame_type!="N"){
			$searchkeyword = "<input type=text name=search value=\"\" style=\"width:$flength\" onfocus=\"return setTextBox(event, 0);\" onmousedown=\"setTextBox(event, 1);\" onkeydown=\"setTextBox(event, 1);\" autocomplete=\"off\">\n";
			$searchkeyword.="<div><div>\n";
			$searchkeyword.="<div id=search_bodylayer style='display:none'>\n";
			$searchkeyword.="<script type=text/javascript>\n";
			$searchkeyword.="	document.write(\"<iframe name=ifr_search src='/main/ifr_search.php' frameborder=0 marginwidth=0 marginheight=0 topmargin=0 scrolling=no></iframe>\");\n";
			$searchkeyword.="</script>\n";
			$searchkeyword.="</div>\n";
			$searchkeyword.="<script>SearchInit(\"search_tform\",".$flength.",0);</script>\n";
		} else {
			$searchkeyword="<input type=text name=search value=\"\" onkeydown=\"CheckKeyTopSearch()\" style=\"width:$flength\">";
		}
	}

	$bestskey="";
	if($_data->search_info["bestkeyword"]=="Y"){
		if($posnum=strpos($topbody,"[BESTSKEY")) {
			$s_tmp=explode("_",substr($topbody,$posnum+1,strpos($topbody,"]",$posnum)-$posnum-1));
			$maxkeylen=(int)$s_tmp[1];
			if($maxkeylen<=0) $maxkeylen=25;
			$keygbn=$s_tmp[2];
			$keystyle=$s_tmp[3];
			if(strlen($keystyle)>0) $keystyle="style=\"".$keystyle."\"";
			$bestskey=getSearchBestKeyword($main_target,$maxkeylen,$_data->search_info["keyword"],$keygbn,$keystyle);
		}
	}

	$logo="<a href=\"".$Dir.MainDir."main.php\" ".$main_target.">".$logo."</a>";
	$shop_count=$_ShopInfo->getShopCount();
	$pattern=array("(\[VISIT\])","(\[VISIT2\])","(\[HOME\])","(\[USEINFO\])","(\[MEMBER\])","(\[LOGIN\])","(\[LOGOUT\])","(\[MEMBEROUT\])","(\[LOGO\])","(\[LOGINFORM\])","(\[LOGINFORMU\])","(\[BASKET\])","(\[ORDER\])","(\[RESERVEVIEW\])","(\[MYPAGE\])","(\[REVIEW\])","(\[BOARD\])","(\[AUCTION\])","(\[GONGGU\])","(\[ESTIMATE\])","(\[COMPANY\])","(\[EMAIL\])","(\[PRODUCTNEW\])","(\[PRODUCTBEST\])","(\[PRODUCTHOT\])","(\[PRODUCTSPECIAL\])","(\[TAG\])","(\[RSS\])","(\[NOTICE([1-4]{1})([YN]{0,1})([1-9]{0,1})([YN]{0,1})([1-9]{0,1})(\_){0,1}([0-9]{0,3})\])","(\[SEARCHFORMSTART\])","(\[SEARCHKEYWORD((\_){0,1})([0-9]{0,3})\])","(\[SEARCHOK\])","(\[SEARCHFORMEND\])","(\[BESTSKEY(\_){0,1}([0-9]{0,3})(\_){0,1}([0-9a-zA-Z\.\-\:\;\=\#\,\|\/\<\>\ ]){0,}(\_){0,1}([0-9a-zA-Z\.\-\:\;\#\ ]){0,}\])");

	if(strlen($_ShopInfo->getMemid())>0) {
		$replace=array($shop_count,$shop_count." (<a href=\"".$Dir.MainDir."main.php?type=logout\">Logout</a>)",$Dir.MainDir."main.php $main_target",$Dir.FrontDir."useinfo.php $main_target",$Dir.FrontDir."member_agree.php $main_target","javascript:alert('로그인중입니다.')",$Dir.MainDir."main.php?type=logout","javascript:memberout()",$logo,$top_loginform,$top_loginformu,$Dir.FrontDir."basket.php $main_target",$Dir.FrontDir."mypage_orderlist.php $main_target",$Dir.FrontDir."mypage_reserve.php $main_target",$Dir.FrontDir."mypage.php $main_target",$Dir.FrontDir."reviewall.php $main_target",$Dir.BoardDir."board.php?board=qna $main_target",$Dir.AuctionDir."auction.php $main_target",$Dir.GongguDir."gonggu.php $main_target","javascript:estimate('".$_data->estimate_ok."')",$Dir.FrontDir."company.php $main_target","javascript:sendmail()",$Dir.FrontDir."productnew.php $main_target",$Dir.FrontDir."productbest.php $main_target",$Dir.FrontDir."producthot.php $main_target",$Dir.FrontDir."productspecial.php $main_target",$Dir.FrontDir."tag.php $main_target",$Dir.FrontDir."rssinfo.php $main_target",$tnotice,"<form name=search_tform method=get action=\"".$Dir.FrontDir."productsearch.php\" $main_target>",$searchkeyword,"javascript:TopSearchCheck()","</form>",$bestskey);

	} else {
		$replace=array($shop_count,$shop_count,$Dir.MainDir."main.php $main_target",$Dir.FrontDir."useinfo.php $main_target",$Dir.FrontDir."member_agree.php $main_target",$Dir.FrontDir."login.php $main_target",$Dir.FrontDir."login.php $main_target","javascript:memberout()",$logo,$top_loginform,$top_loginformu,$Dir.FrontDir."basket.php $main_target",$Dir.FrontDir."mypage_orderlist.php $main_target",$Dir.FrontDir."mypage_reserve.php $main_target",$Dir.FrontDir."mypage.php $main_target",$Dir.FrontDir."reviewall.php $main_target",$Dir.BoardDir."board.php?board=qna $main_target",$Dir.AuctionDir."auction.php $main_target",$Dir.GongguDir."gonggu.php $main_target","javascript:estimate('".$_data->estimate_ok."')",$Dir.FrontDir."company.php $main_target","javascript:sendmail()",$Dir.FrontDir."productnew.php $main_target",$Dir.FrontDir."productbest.php $main_target",$Dir.FrontDir."producthot.php $main_target",$Dir.FrontDir."productspecial.php $main_target",$Dir.FrontDir."tag.php $main_target",$Dir.FrontDir."rssinfo.php $main_target",$tnotice,"<form name=search_tform method=get action=\"".$Dir.FrontDir."productsearch.php\" $main_target>",$searchkeyword,"javascript:TopSearchCheck()","</form>",$bestskey);
		
	}
	
	array_push($pattern,"(\[TODAYSALE\])");
	array_push($replace,$Dir.TodaySaleDir);
	$topbody = preg_replace($pattern,$replace,$topbody);
} else {
	$topbody="상단메뉴 생성이 안되었습니다.";
}

echo $topbody;

if ($_data->frame_type=="N") {
?>
</body>
</html>
<?
}
?>