<?
$Dir="./";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: cache");

$pk = trim($_REQUEST['pk']);

$old_shopurl=$_ShopInfo->getShopurl();
$url=getenv("HTTP_HOST");

if(strlen(RootPath)>0) {
	$hostscript=getenv("HTTP_HOST").getenv("SCRIPT_NAME");
	$pathnum=@strpos($hostscript,RootPath);
	$shopurl=substr($hostscript,0,$pathnum).RootPath;
} else {
	$shopurl=getenv("HTTP_HOST")."/";
}

//페이스북 img
$fbThumb = "http://".$shopurl."img/main/getmall_mark.jpg";
setcookie("url_id", "", 0, "/".RootPath, getCookieDomain());
setcookie("url_name", "", 0, "/".RootPath, getCookieDomain());

$sql = "SELECT * FROM tblshopinfo ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	mysql_free_result($result);
	$shopname=$row->shopname;
	$shoptitle=$row->shoptitle;
	$shopkeyword=$row->shopkeyword;
	$shopdescription=$row->shopdescription;
	$companyname=$row->companyname;
	$companynum=$row->companynum;
	$companyowner=$row->companyowner;
	$info_tel=$row->info_tel;
	$info_email=$row->info_email;

	$adult_type=$row->adult_type;
	$frame_type=$row->frame_type;
	$top_type=$row->top_type;
	$menu_type=$row->menu_type;
	$main_type=$row->main_type;
	$icon_type=$row->icon_type;

	$member_baro=$row->member_baro;
	$adultauth=$row->adultauth;

	$design_intro=$row->design_intro;

	$ssl_type=$row->ssl_type;
	$ssl_domain=$row->ssl_domain;
	$ssl_port=$row->ssl_port;
	$ssl_page=$row->ssl_page;

	unset($adultauthid);
	unset($adultauthpw);
	if(strlen($adultauth)>0) {
		$tempadult=explode("=",$adultauth);
		if($tempadult[0]=="Y") {
			$adultauthid=$tempadult[1];
			$adultauthpw=$tempadult[2];
		}
	}

	if ($adult_type=="Y") {
		$http_host = ereg_replace("www.","",getenv("HTTP_HOST"));
		$adult_meta = "<META http-equiv=\"PICS-label\" content='(PICS-1.1 \"http://service.icec.or.kr/rating.html\" l gen true for \"http://www.$http_host\" r (y 1))'>\n";
		$adult_meta = $adult_meta."<META http-equiv=\"PICS-label\" content='(PICS-1.1 \"http://service.icec.or.kr/rating.html\" l gen true for \"http://$http_host\" r (y 1))'>\n";
		$adult_meta = $adult_meta."<META http-equiv=\"PICS-label\" content='(PICS-1.1 \"http://www.safenet.ne.kr/rating.html\" l gen true for \"http://www.$http_host\" r (n 3 s 3 v 3 l 3 i 0 h 0))'>\n";
		$adult_meta = $adult_meta."<META http-equiv=\"PICS-label\" content='(PICS-1.1 \"http://www.safenet.ne.kr/rating.html\" l gen true for \"http://$http_host\" r (n 3 s 3 v 3 l 3 i 0 h 0))'>\n";
	} else {
		$adult_meta="";
	}

	if (strlen($old_shopurl)==0) {
		$sql = "UPDATE tblshopcount SET count = count+1 ";
		mysql_query($sql,get_db_conn());

		mysql_query("INSERT INTO tblshopcountday (date,count) VALUES ('".date("Ymd")."',1)",get_db_conn());
		if (mysql_errno()==1062) {
			mysql_query("UPDATE tblshopcountday SET count=count+1 WHERE date='".date("Ymd")."'",get_db_conn());
		}
		$_ShopInfo->setMemid("");
		$_ShopInfo->Save();
	}

	if (strlen($ref)==0) {
		$ref = strtolower(ereg_replace("http://","",getenv("HTTP_REFERER")));
		if (strpos($ref,"/") != false) $ref = substr($ref,0,strpos($ref,"/"));
	}
	if (strlen($ref)>0 && strlen($_ShopInfo->getRefurl())==0) {
		$sql2 = "SELECT * FROM tblpartner ";
		$sql2.= "WHERE (id='".$ref."' OR url LIKE '%".$ref."%') ";
		$result2 = mysql_query($sql2,get_db_conn());
		if ($row2=mysql_fetch_object($result2)) {
			mysql_query("UPDATE tblpartner SET hit_cnt = hit_cnt+1 WHERE url = '".$row2->url."'",get_db_conn());
			$_ShopInfo->setRefurl($row2->id);
			$_ShopInfo->Save();
		}
		mysql_free_result($result2);
	}

	#카운터
	$countpath="<img src=\"".$Dir.FrontDir."counter.php?ref=".urlencode(getenv("HTTP_REFERER"))."\" width=0 height=0>";

	$history="-1";
	$ssllogintype="";
	$ssladultchecktype="";
	if($_POST["ssltype"]=="ssl" && strlen($_POST["id"])>0 && strlen($_POST["sessid"])==32) {
		if($_POST["type"]=="adultlogin" || $_POST["type"]=="btblogin") {
			$ssllogintype="ssl";
		}
		$history="-2";
	} else if($_POST["type"]=="adultcheck" && $_POST["ssltype"]=="ssl" && strlen($_POST["sessid"])==64) {
		$ssladultchecktype="ssl";
		$history="-2";
	}

	//실명확인
	if ($adult_type=="Y" && ((strlen($_REQUEST["name"])>0 && strlen($_REQUEST["adult_no1"])>0 && strlen($_REQUEST["adult_no2"])>0 && eregi($url,getenv("HTTP_REFERER")." ")) || $ssladultchecktype=="ssl")) {
		unset($errmsg);
		if($ssladultchecktype=="ssl") {
			$secure_data=getSecureKeyData($_POST["sessid"]);
			if(!is_array($secure_data)) {
				echo "<html><head><title></title></head><body onload=\"alert('성인실명인증 정보가 잘못되었습니다.');history.go(".$history.");\"></body></html>";exit;
			}
			foreach($secure_data as $key=>$val) {
				${$key}=$val;
			}
		} else {
			$name=$_REQUEST["name"];
			$adult_no1=$_REQUEST["adult_no1"];
			$adult_no2=$_REQUEST["adult_no2"];
		}
		$resno=$adult_no1.$adult_no2;
		if(strlen($resno)!=13) {
			$errmsg="주민등록번호 입력이 잘못되었습니다.";
		} else if(!chkResNo($resno)) {
			$errmsg="잘못된 주민등록번호 입니다.\\n\\n확인 후 다시 입력하시기 바랍니다.";
		} else if(getAgeResno($resno)<19) {
			$errmsg="본 쇼핑몰은 성인만 이용가능합니다.";
		} else {
			if(strlen($adultauthid)>0) {
				include($Dir."lib/name_check.php");
				name_check($name, $adult_no1, $adult_no2, $adultauthid, $adultauthpw);
			}
		}
		if(strlen($errmsg)>0) {
			echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');history.go(-1);\"></body></html>";exit;
		} else {
			$_ShopInfo->setShopurl($shopurl);
			$_ShopInfo->Save();
			echo "<html><head><title></title></head><body onload=\"location.replace('".$Dir."')\"></body></html>";exit;
		}
	} else if ($adult_type=="Y" && $old_shopurl!=$shopurl) {
		if (file_exists($Dir.DataDir."design/intro.htm")==true) {
			echo $adult_meta;
			readfile($Dir.DataDir."design/intro.htm");
			echo $countpath;
			exit;
		}
		if (strlen($_REQUEST["adult_no1"])==0 || strlen($_REQUEST["adult_no2"])==0 || strlen($_REQUEST["name"])==0) {
			echo $adult_meta;
			include($Dir.TempletDir."adult/adult".$design_intro.".php");
			echo $countpath;
			exit;
		}
	//성인몰이고 회원로그인이 안되었을때
	} else if ($adult_type=="M" && (strlen($_REQUEST["id"])==0 || strlen($_REQUEST["passwd"])==0) && $ssllogintype!="ssl" && strlen($_ShopInfo->getMemid())==0) {
		echo $adult_meta;
		include($Dir.AdultDir."login.php");
		echo $countpath;
		exit;
	} else if ($adult_type=="B" && (strlen($_REQUEST["id"])==0 || strlen($_REQUEST["passwd"])==0) && $ssllogintype!="ssl" && strlen($_ShopInfo->getMemid())==0) {
		if (file_exists($Dir.DataDir."design/intro.htm")==true) {
			readfile($Dir.DataDir."design/intro.htm");
			echo $countpath;
			exit;
		}
		include($Dir.AdultDir."btblogin.php");
		echo $countpath;
		exit;
	//성인몰이고 회원 Id와 비밀번호 체크
	} else if (($adult_type=="M" || $adult_type=="B") && (strlen($_REQUEST["id"])>0 && strlen($_REQUEST["passwd"])>0) || $ssllogintype=="ssl") {
		if($ssllogintype!="ssl") {
			unset($passwd_type);
			$sql = "SELECT passwd FROM tblmember WHERE id='".$_REQUEST["id"]."' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				if(substr($row->passwd,0,3)=="$1$") {
					$passwd_type="hash";
					$hashdata=$row->passwd;
				} else if(strlen($row->passwd)==16) {
					$passwd_type="password";
					$chksql = "SELECT PASSWORD('1') AS passwordlen ";
					$chkresult=mysql_query($chksql,get_db_conn());
					if($chkrow=mysql_fetch_object($chkresult)) {
						if(strlen($chkrow->passwordlen)==41 && substr($chkrow->passwordlen,0,1)=="*") {
							$passwd_type="old_password";
						}
					}
					mysql_free_result($chkresult);
				} else {
					$passwd_type="md5";
				}
			} else {
				if($adult_type=="M")
					include($Dir.AdultDir."login.php");
				else if(file_exists($Dir.DataDir."design/intro.htm")==true)
					readfile($Dir.DataDir."design/intro.htm");
				else include($Dir.AdultDir."btblogin.php");
				echo $countpath;
				if($ssllogintype!="ssl") {
					echo "<script>alert('아이디 또는 비밀번호가 틀립니다.');</script>";
				}
				exit;
			}
			mysql_free_result($result);
		}

		$sql = "SELECT * FROM tblmember WHERE id='".$_REQUEST["id"]."' ";
		if($ssllogintype=="ssl") {
			$sql.= "AND authidkey='".$sessid."'";
		} else {
			if($passwd_type=="hash") {
				$sql.= "AND passwd='".crypt($_REQUEST["passwd"], $hashdata)."' ";
			} else if($passwd_type=="password") {
				$sql.= "AND passwd=password('".$_REQUEST["passwd"]."')";
			} else if($passwd_type=="old_password") {
				$sql.= "AND passwd=old_password('".$_REQUEST["passwd"]."')";
			} else if($passwd_type=="md5") {
				$sql.= "AND passwd=md5('".$_REQUEST["passwd"]."')";
			}
		}
		$result_login = mysql_query($sql,get_db_conn());
		if ($row_login=mysql_fetch_object($result_login)) { // ID와 비밀번호가 맞으면
			if ($member_baro=="Y" && $row_login->confirm_yn=="N") { //관리자인증기능여부 및 회원인증 검사
				echo "<script>alert('쇼핑몰 운영자 인증 후 로그인이 가능합니다.\\n\\n전화로 문의바랍니다.\\n\\n".$info_tel."');history.go(".$history.");</script>";
				exit;
			}

			if($row_login->member_out=="Y") {	//탈퇴한 회원
				if($adult_type=="M")
					include($Dir.AdultDir."login.php");
				else if(file_exists($Dir.DataDir."design/intro.htm")==true)
					readfile($Dir.DataDir."design/intro.htm");
				else include($Dir.AdultDir."btblogin.php");
				echo $countpath;
				echo "<script>alert('아이디 또는 비밀번호가 틀리거나 탈퇴한 회원입니다.');</script>";
				exit;
			}

			$_ShopInfo->setMemid($row_login->id);
			$_ShopInfo->setMemgroup($row_login->group_code);
			$_ShopInfo->setMemname($row_login->name);
			$_ShopInfo->setMememail($row_login->email);

			$authidkey = md5(uniqid(""));
			$_ShopInfo->setAuthidkey($authidkey);

			$_ShopInfo->setShopurl($shopurl);
			$_ShopInfo->Save();

			$sql = "UPDATE tblmember SET ";
			$sql.= "authidkey		= '".$authidkey."', ";
			if($passwd_type=="hash" || $passwd_type=="password" || $passwd_type=="old_password") {
				$sql.= "passwd		= '".md5($_REQUEST["passwd"])."', ";
			}
			$sql.= "ip				= '".getenv("REMOTE_ADDR")."', ";
			$sql.= "logindate		= '".date("YmdHis")."', ";
			$sql.= "logincnt		= logincnt+1 ";
			$sql.= "WHERE id='".$_ShopInfo->getMemid()."'";
			mysql_query($sql,get_db_conn());

			$loginday = date("Ymd");
			$sql = "SELECT id_list FROM tblshopcountday ";
			$sql.= "WHERE date='".$loginday."'";
			$result = mysql_query($sql,get_db_conn());
			if($row3 = mysql_fetch_object($result)){
				if(!strpos(" ".$row3->id_list,"".$_ShopInfo->getMemid()."")){
					$id_list=$row3->id_list.$_ShopInfo->getMemid()."";
					$sql = "UPDATE tblshopcountday SET id_list='".$id_list."',login_cnt=login_cnt+1 ";
					$sql.= "WHERE date='".$loginday."'";
					mysql_query($sql,get_db_conn());
				}
			} else {
				$id_list="".$_ShopInfo->getMemid()."";
				$sql = "INSERT INTO tblshopcountday (date,login_cnt,id_list) VALUES ('".$loginday."',1,'".$id_list."')";
				mysql_query($sql,get_db_conn());
			}
			echo "<script>location='".$Dir."'</script>"; exit;
		} else {	#아이디/비밀번호가 틀리다
			if($adult_type=="M")
				include($Dir.AdultDir."login.php");
			else if(file_exists($Dir.DataDir."design/intro.htm")==true)
				readfile($Dir.DataDir."design/intro.htm");
			else include($Dir.AdultDir."btblogin.php");
			echo $countpath;
			if($ssllogintype!="ssl") {
				echo "<script>alert('비밀번호가 틀립니다.');</script>";
			}
			exit;
		}
	}


	//인트로 검사
	$url_index = getenv("REQUEST_URI");
	if (file_exists($Dir.DataDir."design/intro.htm")==true && strpos($url_index,"index.php")==false) {
		readfile($Dir.DataDir."design/intro.htm");
		echo $countpath;
		exit;
	}

	$_ShopInfo->setShopurl($shopurl);
	$_ShopInfo->Save();

	if ($frame_type=="Y") {	//주소고정
		$top_height=0;
		$top_type="top";
	} else if ($top_type=="topp") {
		$result2 = mysql_query("SELECT top_height FROM tbldesign ",get_db_conn());
		if ($row2=mysql_fetch_object($result2)) $top_height=$row2->top_height;
		else $top_height=70;
		mysql_free_result($result2);
	} else if($top_type=="topeasy"){
		$result2 = mysql_query("SELECT top_ysize FROM tbldesign ",get_db_conn());
		if ($row2=mysql_fetch_object($result2)) $top_height=$row2->top_ysize;
		mysql_free_result($result2);
	} else {
		$result2 = mysql_query("SELECT top_height FROM tbltempletinfo WHERE icon_type='".$icon_type."'",get_db_conn());
		if ($row2=mysql_fetch_object($result2)) $top_height=$row2->top_height;
		else $top_height=70;
		mysql_free_result($result2);
	}
} else {
	mysql_free_result($result);

	//쇼핑몰 정보 등록이 안되었으니까 error 페이지 함수 호출
	error_msg("쇼핑몰 정보 등록이 안되었습니다.<br>쇼핑몰 설정을 먼저 하십시요",DirPath."install.php");
}

if(strpos(getenv("HTTP_REFERER"),getenv("HTTP_HOST"))>0) $ref="";

if (strlen($ref)>0) {
	$ref1="&ref=".urlencode(getenv("HTTP_REFERER"));
	if (strpos($suburl,"?")>0)  $ref2="&ref=".urlencode(getenv("HTTP_REFERER"));
	else $ref2="?ref=".urlencode(getenv("HTTP_REFERER"));
}

// 리퀘스트 퀄리 변수값 추가처리
parse_str($_SERVER['QUERY_STRING'],$queryData);
unset($queryData['productcode'],$queryData['brandcode'],$queryData['code']);
if(_array($queryData)) $ref1 = '&'.http_build_query($queryData).$ref1;
//# 리퀘스트 퀄리 변수값 추가처리

$connect_device = _getDeviceInfo();
$mobile=false;
if(strlen($brandcode)>0) {
	if(strlen($productcode)>0)
		$mainurl=FrontDir."productdetail.php?brandcode=".$brandcode."&productcode=".$productcode.$ref1;
	else if(strlen($code)>0)
		$mainurl=FrontDir."productblist.php?brandcode=".$brandcode."&code=".$code.$ref1;
	else
		$mainurl=FrontDir."productblist.php?brandcode=".$brandcode.$ref1;
} else if (strlen($productcode)>0)
	$mainurl=FrontDir."productdetail.php?productcode=".$productcode.$ref1;
else if(strlen($code)>0)
	$mainurl=FrontDir."productlist.php?code=".$code.$ref1;
else if($adult_type=="N" && strlen($suburl)>0)
	$mainurl=urldecode($suburl).$ref2;

else if(strlen($token)>0){

	// 추천인 회원가입 홍보 URL

	$sql = "SELECT id,name FROM tblmember WHERE url_id='".trim($token)."'";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$url_id =$row->id;
		$url_name =  $row->name;
		//session_register('url_id');
		//session_register('url_name');
		setcookie("url_id", $url_id, 0, "/".RootPath, getCookieDomain());
		setcookie("url_name", $url_name, 0, "/".RootPath, getCookieDomain());

		if($_ShopInfo->getMemid() == $row->id){
			echo "<script>alert('자기자신을 추천할수 없습니다.');</script>";
			echo "<html><head><title></title></head><body onload=\"location.replace('/')\"></body></html>";exit;
		}else{

			if($connect_device == 'M'){
				$mobile=true;
				$mainurl="/m/member_agree.php?".$ref1;
			}else{
				$mainurl=FrontDir."member_agree.php?".$ref1;
			}
		}
	}else{
		echo "<script>alert('유효하지 않은 친구추천 url로 접근하셨습니다.유효하지않은 추천 url로 가입하실 경우 추전 적립금이 지급되지않습니다.');</script>";
		$mainurl=FrontDir."member_agree.php?".$ref1;
	}


}else if(strlen($pk)>0){ // SNS 홍보
	$pks = snsPromoteAccess( $pk );
	if( $pks["access"] AND strlen($pks["pcode"])>0 ) {
		$mainurl=FrontDir."productdetail.php?productcode=".$pks["pcode"].$ref1;
	}else{
		echo "<script>alert('유효하지 않은 url로 접근하셨습니다.상품구매에 따른 추가 적립금이 지급되지않습니다.');</script>";
		echo "<html><head><title></title></head><body onload=\"location.replace('/')\"></body></html>";exit;
	}
}else if(strlen($gong)>0){
	$sql = "SELECT id,pcode FROM tblsnsGonggu WHERE code='".trim($gong)."'";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$recom_id =$row->id;
		$pcode =  $row->pcode;
		if($_ShopInfo->getMemid() != $recom_id){
			$mainurl=FrontDir."productdetail.php?productcode=".$pcode.$ref1;
		}else{
			$mainurl=FrontDir."productdetail.php?productcode=".$pcode.$ref1;
		}
	}else{
		echo "<script>alert('유효하지 않은 url로 접근하셨습니다.');</script>";
		echo "<html><head><title></title></head><body onload=\"location.replace('/')\"></body></html>";exit;
	}
}else if(strlen($prdt)>0){
	$sql = "SELECT * FROM tblproduct WHERE productcode='".$prdt."' AND display='Y' ";

	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$_pdata=$row;
		if(strlen($_pdata->tinyimage)>0 && file_exists(DataDir."shopimages/product/".$_pdata->tinyimage)) {
			$fbThumb = "http://".$_ShopInfo->getShopurl().DataDir."shopimages/product/".$_pdata->tinyimage;
		}
		$fbName = $_pdata->productname;
		$fbDesc = $_pdata->productname." ".$shopname."에서 상품정보를 확인하세요.";
		$mainurl=FrontDir."productdetail.php?productcode=".$prdt.$ref1;
	}else{
		echo "<script>alert('유효하지 않은 url로 접근하셨습니다.');</script>";
		echo "<html><head><title></title></head><body onload=\"location.replace('/')\"></body></html>";exit;
	}
}else if(strlen($pstr)>0){
	$sql = "SELECT id FROM tblpesterinfo WHERE code='".$pstr."' ";

	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$p_id=$row->id;
	}
	if(strlen($p_id)>0){
		$mainurl=FrontDir."order_j.php?pstr=".$pstr.$ref1;
	}else{
		echo "<script>alert('유효하지 않은 url로 접근하셨습니다.');</script>";
		echo "<html><head><title></title></head><body onload=\"location.replace('/')\"></body></html>";exit;
	}
}else if(strlen($gft_cd)>0){
	$sql = "SELECT count(1) cnt FROM tblpresentcode WHERE code='".$gft_cd."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$cnt=$row->cnt;
	}
	if($cnt>0){
		$mainurl=FrontDir."presentdetail.php?code=".$gft_cd;
		Header("Location: ".$mainurl);exit;
	}else{
		echo "<script>alert('유효하지 않은 url로 접근하셨습니다.');</script>";
		echo "<html><head><title></title></head><body onload=\"location.replace('/')\"></body></html>";exit;
	}
}else if(strlen($bcmt)>0){


	// 게시판 홍보 적립금
	$bcmtInfo = snsPromoteBoard( $bcmt );

	switch($bcmtInfo['err']){
		case 'no':
			$fbName = $bcmtInfo['fbName'];
			$fbDesc = $bcmtInfo['fbDesc'];
			$mainurl = $bcmtInfo['mainurl'].$ref1;
			break;
		case 'noBoard':
			echo "<script>alert('게시글이 존재하지않습니다.');</script>";
			echo "<html><head><title></title></head><body onload=\"location.replace('/')\"></body></html>";
			exit;
			break;
		case 'noHonbo':
			echo "<script>alert('유효하지 않은 url로 접근하셨습니다.');</script>";
			echo "<html><head><title></title></head><body onload=\"location.replace('/')\"></body></html>";
			exit;
			break;
		default :
			echo "<script>alert('잘못된 접근 입니다.');</script>";
			echo "<html><head><title></title></head><body onload=\"location.replace('/')\"></body></html>";
			exit;
	}


}else if($gonggu == "list"){
	$mainurl=FrontDir."gonggu_main.php".$ref1;
	$mainurl2=FrontDir."gonggu_main.php";
}else {
	$mainurl=MainDir."main.php".$ref2;
	$mainurl2=MainDir."main.php";
	if($_GET['pc'] == "ON") {
		$mainurl=MainDir."main.php?pc=ON";
	}
}

$mobileResult = mysql_query("select * from tblmobileconfig");
$mobileRow = mysql_fetch_array($mobileResult);


if($mobileRow[use_mobile_site] == "Y") {
	$connect_device = _getDeviceInfo();
	if($connect_device == 'M'){
		if($mobile){
			exit( Header("Location: ".$mainurl) );
		}else{

			if($mobileRow[use_auto_redirection]=="Y"){
				exit( Header("Location: ./m/") );
			}else{
				echo "<script>";
				echo "if(confirm(\"모바일샵으로 이동하시 겠습니까?\")){";
				echo "location.replace(\"./m/\");";
				echo "}else{";
				echo "location.replace(\"./main/main.php\");";
				echo "}";
				echo "</script>";
				exit;
			}
		}
	}else{
		//exit( Header("Location: ".$mainurl) );
	}

}else{
	//exit( Header("Location: ".$mainurl) );
}

//exit( Header("Location: ".$mainurl) );

if ($frame_type=="A") {	#원프레임 타입일 경우
	if(substr($mainurl2,0,(strlen(MainDir)+4))==MainDir."main"){
		$urlpath="Y";
		$ref=getenv("HTTP_REFERER");
		INCLUDE ($mainurl2);
	} else {
		Header("Location: ".$mainurl);
	}
	exit;
} else {
?>
<HTML>
<HEAD>
<TITLE><?=(strlen($shoptitle)>0?$shoptitle:$shopname)?></TITLE>
<?=$adult_meta?>
<link rel="P3Pv1" href="http://<?=$_ShopInfo->getShopurl()?>w3c/p3p.xml">
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" >
<meta name="description" content="<?=(strlen($shopdescription)>0?$shopdescription:$shoptitle)?>">
<meta name="keywords" content="<?=$shopkeyword?>">
<meta property="og:title" content="<?=(strlen($fbName)>0)? $fbName :(strlen($shopname)>0?$shopname:$shoptitle)?>"/>
<meta property="og:image" content="<?=$fbThumb?>"/>
<meta property="og:description" content="<?=(strlen($fbDesc)>0)? $fbDesc :(strlen($shopdescription)>0?$shopdescription:$shoptitle)?>"/>
</HEAD>
<frameset rows="<?=$top_height?>,*" border=0 MARGINWIDTH=0 MARGINHEIGHT=0>
<frame src="<?=MainDir.$top_type?>.php" name=topmenu MARGINWIDTH="0" MARGINHEIGHT="0" scrolling=no noresize>
<frame src="<?=$mainurl?>" name=main MARGINWIDTH="0" MARGINHEIGHT="0" scrolling=auto>
</frameset>
</HTML>
<?
}
?>