<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$chUrl=trim(urldecode($_REQUEST["chUrl"]));
if(strlen($_ShopInfo->getMemid())>0) {
	if (strlen($chUrl)>0) $onload=$chUrl;
	else $onload=$Dir.MainDir."main.php";
	Header("Location:".$onload);
	exit;
}

if(strpos($chUrl,"?") && (ereg("order.php",$chUrl) || ereg("order3.php",$chUrl))){
	$orderParm =  substr($chUrl, strpos($chUrl,"?"));
	$chUrl = substr($chUrl,0,strpos($chUrl,"?"));
}

$leftmenu="Y";
$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='login'";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$body=$row->body;
	$body=str_replace("[DIR]",$Dir,$body);
	$leftmenu=$row->leftmenu;
	$newdesign="Y";
}
mysql_free_result($result);

?>
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 회원로그인</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm() {
	try {
		if(document.form1.id.value.length==0) {
			alert("회원 아이디를 입력하세요.");
			document.form1.id.focus();
			return;
		}
		if(document.form1.passwd.value.length==0) {
			alert("비밀번호를 입력하세요.");
			document.form1.passwd.focus();
			return;
		}
		document.form1.target = "";
		<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {?>
		if(typeof document.form1.ssllogin!="undefined"){
			if(document.form1.ssllogin.checked==true) {
				document.form1.target = "loginiframe";
				document.form1.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>login.php';
			}
		}
		<?}?>
		document.form1.submit();
	} catch (e) {
		alert("로그인 페이지에 문제가 있습니다.\n\n쇼핑몰 운영자에게 문의하시기 바랍니다.");
	}
}

function CheckOrder() {
	if(document.form1.ordername.value.length==0) {
		alert("주문자 이름을 입력하세요.");
		document.form1.ordername.focus();
		return;
	}
	if(document.form1.ordercodeid.value.length==0) {
		alert("주문번호 6자리를 입력하세요.");
		document.form1.ordercodeid.focus();
		return;
	}
	if(document.form1.ordercodeid.value.length!=6) {
		alert("주문번호는 6자리입니다.\n\n다시 입력하세요.");
		document.form1.ordercodeid.focus();
		return;
	}
	document.form2.ordername.value=document.form1.ordername.value;
	document.form2.ordercodeid.value=document.form1.ordercodeid.value;
	window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
	document.form2.submit();
}

function CheckKeyForm1() {
	key=event.keyCode;
	if (key==13) {
		CheckForm();
	}
}

function CheckKeyForm2() {
	key=event.keyCode;
	if (key==13) {
		CheckOrder();
	}
}

//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=type value="">
<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {?>
<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<IFRAME id=loginiframe name=loginiframe style="display:none"></IFRAME>
<?}?>
<?
if ($leftmenu!="N") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/login_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/login_title.gif\" border=\"0\" alt=\"회원로그인\"></td>\n";
	} else {
		echo "<td>\n";
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/login_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/login_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/login_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		echo "</td>\n";
	}
	echo "</tr>\n";
}

echo "<tr>\n";
echo "	<td>\n";

$banner_body="";
$sql = "SELECT * FROM tblaffiliatebanner WHERE used='Y' ORDER BY rand() LIMIT 1 ";
$result=@mysql_query($sql,get_db_conn());
if($row=@mysql_fetch_object($result)) {
	$tempcontent=explode("=",$row->content);
	$banner_type=$tempcontent[0];
	if($banner_type=="Y") {
		$banner_target=$tempcontent[1];
		$banner_url=$tempcontent[2];
		$banner_image=$tempcontent[3];
		if(strlen($banner_image)>0 && file_exists($Dir.DataDir."shopimages/banner/".$banner_image)==true) {
			$banner_body="<A HREF=\"".$banner_url."\" target=\"".$banner_target."\"><img src=\"".$Dir.DataDir."shopimages/banner/".$banner_image."\" border=0></A>";
		}
	} else if($banner_type=="N") {
		$banner_body=$tempcontent[1];
	}
}
@mysql_free_result($result);

if($newdesign=="Y") {	//개별디자인
	//주문조회시 로그인
	if(substr($chUrl,-20)=="mypage_orderlist.php") {
		$body=str_replace("[IFORDER]","",$body);
		$body=str_replace("[ENDORDER]","",$body);
	} else {
		if(strlen(strpos($body,"[IFORDER]"))>0){
			$iforder=strpos($body,"[IFORDER]");
			$endorder=strpos($body,"[ENDORDER]");
			$body=substr($body,0,$iforder).substr($body,$endorder+10);
		}
	}

	//바로구매시 로그인
	//if(substr($chUrl,-9)=="order.php") {
	if($_data->member_buygrant=="U" && ( ereg("order.php",$chUrl) || ereg("order3.php",$chUrl) ) ) {
		$body=str_replace("[IFNOLOGIN]","",$body);
		$body=str_replace("[ENDNOLOGIN]","",$body);
	} else {
		if(strlen(strpos($body,"[IFNOLOGIN]"))>0){
			$iforder=strpos($body,"[IFNOLOGIN]");
			$endorder=strpos($body,"[ENDNOLOGIN]");
			$body=substr($body,0,$iforder).substr($body,$endorder+12);
		}
	}

	// SSL 체크박스 출력
	if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {
		$body=str_replace("[IFSSL]","",$body);
		$body=str_replace("[ENDSSL]","",$body);
	} else {
		if(strlen(strpos($body,"[IFSSL]"))>0){
			$ifssl=strpos($body,"[IFSSL]");
			$endssl=strpos($body,"[ENDSSL]");
			$body=substr($body,0,$ifssl).substr($body,$endssl+8);
		}
	}

	$pattern=array("(\[ID\])","(\[PASSWD\])","(\[SSLCHECK\])","(\[SSLINFO\])","(\[OK\])","(\[JOIN\])","(\[FINDPWD\])","(\[NOLOGIN\])","(\[ORDERNAME\])","(\[ORDERCODE\])","(\[ORDEROK\])","(\[BANNER\])");
	$replace=array("<input type=text name=id value=\"\" maxlength=\"20\">","<input type=\"password\" name=\"passwd\" value=\"\" maxlength=\"20\" onkeydown=\"CheckKeyForm1()\">","<input type=checkbox name=ssllogin value=Y>","javascript:sslinfo()","\"JavaScript:CheckForm()\"",$Dir.FrontDir."member_agree.php",$Dir.FrontDir."findpwd.php",$chUrl.$orderParm,"<input type=text name=ordername value=\"\" maxlength=20>","<input type=text name=ordercodeid value=\"\" maxlength=20>","\"javascript:CheckOrder()\"",$banner_body);
	$body=preg_replace($pattern,$replace,$body);
	echo $body;

} else {	//템플릿

	$buffer="";
	if(file_exists($Dir.TempletDir."member/login".$_data->design_member.".php")) {

		$fp=fopen($Dir.TempletDir."member/login".$_data->design_member.".php","r");
		if($fp) {
			while (!feof($fp)) {$buffer.= fgets($fp, 1024);}
		}
		fclose($fp);
		$body=$buffer;
	}

	//주문조회시 로그인
	if($_data->member_buygrant=="U" && substr($chUrl,-20)=="mypage_orderlist.php") {
		$body=str_replace("[IFORDER]","",$body);
		$body=str_replace("[ENDORDER]","",$body);
	} else {
		if(strlen(strpos($body,"[IFORDER]"))>0){
			$iforder=strpos($body,"[IFORDER]");
			$endorder=strpos($body,"[ENDORDER]");
			$body=substr($body,0,$iforder).substr($body,$endorder+10);
		}
	}

	//바로구매시 로그인
	//if($_data->member_buygrant=="U" && substr($chUrl,-9)=="order.php") {
	if($_data->member_buygrant=="U" && ( ereg("order.php",$chUrl) || ereg("order3.php",$chUrl) ) ) {
		$body=str_replace("[IFNOLOGIN]","",$body);
		$body=str_replace("[ENDNOLOGIN]","",$body);
	} else {
		if(strlen(strpos($body,"[IFNOLOGIN]"))>0){
			$iforder=strpos($body,"[IFNOLOGIN]");
			$endorder=strpos($body,"[ENDNOLOGIN]");
			$body=substr($body,0,$iforder).substr($body,$endorder+12);
		}
	}

	// SSL 체크박스 출력
	if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["LOGIN"]=="Y") {
		$body=str_replace("[IFSSL]","",$body);
		$body=str_replace("[ENDSSL]","",$body);
	} else {
		if(strlen(strpos($body,"[IFSSL]"))>0){
			$ifssl=strpos($body,"[IFSSL]");
			$endssl=strpos($body,"[ENDSSL]");
			$body=substr($body,0,$ifssl).substr($body,$endssl+8);
		}
	}
	
	$pattern=array("(\[DIR\])","(\[ID\])","(\[PASSWD\])","(\[SSLCHECK\])","(\[SSLINFO\])","(\[OK\])","(\[JOIN\])","(\[FINDPWD\])","(\[NOLOGIN\])","(\[ORDERNAME\])","(\[ORDERCODE\])","(\[ORDEROK\])","(\[BANNER\])");
	$replace=array($Dir,"<input type=text name=id value=\"\" maxlength=20 style=\"width:120\">","<input type=password name=passwd value=\"\" maxlength=20 style=\"width:120\" onkeydown=\"CheckKeyForm1()\">","<input type=checkbox name=ssllogin value=Y>","javascript:sslinfo()","\"JavaScript:CheckForm()\"",$Dir.FrontDir."member_agree.php",$Dir.FrontDir."findpwd.php",$chUrl.$orderParm,"<input type=text name=ordername value=\"\" maxlength=20 style=\"width:120\">","<input type=text name=ordercodeid value=\"\" maxlength=20 style=\"width:120\" onkeydown=\"CheckKeyForm2()\">","\"javascript:CheckOrder()\"",$banner_body);
	$body=preg_replace($pattern,$replace,$body);
	echo $body;
	
}
echo "	</td>\n";
echo "</tr>\n";
?>
</form>

<form name=form2 method=post action="<?=$Dir.FrontDir?>orderdetailpop.php" target="orderpop">
<input type=hidden name=ordername>
<input type=hidden name=ordercodeid>
</form>
</table>

<script>try{document.form1.id.focus();}catch(e){}</script>

<?=$onload?>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>