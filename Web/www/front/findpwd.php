<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())>0) {
	echo "</head><body onload=\"alert('고객님께서는 로그인된 상태입니다.');location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
}

$sql = "SELECT id, authkey, mem_passwd FROM tblsmsinfo ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
if($row->mem_passwd=="Y") {
	$sms_id=$row->id;
	$sms_authkey=$row->authkey;

	$sms_uname=$row->sms_uname;
	$return_tel=$row->return_tel;
	$mem_passwd=$row->mem_passwd;
	$mess="핸드폰과 E-mail";
} else {
	$mess="E-mail";
}
mysql_free_result($result);


$mode=$_POST["mode"];

if($mode=="send") {
	$history="-1";
	$sslchecktype="";
	if($_POST["ssltype"]=="ssl" && strlen($_POST["sessid"])==64) {
		$sslchecktype="ssl";
		$history="-2";
	}
	if($sslchecktype=="ssl") {
		$secure_data=getSecureKeyData($_POST["sessid"]);
		if(!is_array($secure_data)) {
			echo "<html><head><title></title></head><body onload=\"alert('보안인증 정보가 잘못되었습니다.');history.go(".$history.");\"></body></html>";exit;
		}
		foreach($secure_data as $key=>$val) {
			${$key}=$val;
		}
	} else {
		$name=$_POST["name"];
		$jumin1=$_POST["jumin1"];
		$jumin2=$_POST["jumin2"];
		$email=$_POST["email"];
	}

	$jumin_md5=$jumin1.substr($jumin2,0,1)."[".md5(substr($jumin2,1))."]";

	$sql = "SELECT id,resno,email,mobile FROM tblmember WHERE name='".$name."' ";
	if($_data->resno_type!="N") {
		$sql.="AND (resno='".$jumin1.$jumin2."' OR resno='".$jumin_md5."') ";
	} else $sql.="AND email='".$email."' ";
	$result=mysql_query($sql,get_db_conn());

	if(mysql_num_rows($result)==0) {
		echo "</head><body onload=\"alert('입력하신 정보와 일치하는 아이디가 없습니다.');location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
	} else {
		$row=mysql_fetch_object($result);
		mysql_free_result($result);

		$passwd=substr(md5(rand(0,9999999)),0,8);

		$sql = "UPDATE tblmember SET passwd='".md5($passwd)."' ";
		if(strlen($row->resno)==41) {
			$sql.= ",resno='".$jumin1.$jumin2."' ";
		}
		$sql.= "WHERE id='".$row->id."' AND name='".$name."' ";
		if($_data->resno_type!="N") {
			if(strlen($row->resno)==41) {
				$sql.="AND resno='".$jumin_md5."' ";
			} else {
				$sql.="AND resno='".$jumin1.$jumin2."' ";
			}
		} else $sql.="AND email='".$email."' ";
		mysql_query($sql,get_db_conn());

		if($mem_passwd=="Y" && strlen($row->mobile)>0) {
			$smsmessage="[".strip_tags($_data->shopname)."]".$name."님 ID : ".$row->id." 임시 비밀번호 : ".$passwd."입니다. 로그인 후 비밀번호를 변경하시기 바랍니다.";
			$fromtel=$return_tel;
			$etcmessage="비번분실메세지(회원)";
			$date=0;
			$row->mobile=str_replace(" ","",$row->mobile);
			$row->mobile=str_replace("-","",$row->mobile);
			$temp=SendSMS($sms_id, $sms_authkey, $row->mobile, "", $fromtel, $date, $smsmessage, $etcmessage);

			$mess2=$row->email."로 메일을 ".$row->mobile."로 문자메세지를";
		} else {
			$mess2=$row->email."로 메일을 ";
		}
		if(strlen($row->email)>0) {
			SendPassMail($_data->shopname, $_data->shopurl, $_data->design_mail, $_data->info_email, $row->email, $name, $row->id, $passwd);
		}
		echo "</head><body onload=\"alert('".$mess2." 발송하였습니다.');location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
	}
}


$leftmenu="Y";
$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='findpwd'";
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
<TITLE><?=$_data->shoptitle?> - 비밀번호 찾기</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META http-equiv="X-UA-Compatible" content="IE=5" />
<META name="description" content="<?=(strlen($_data->shopdescription)>0?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function chkCtyNo(obj) {
	if (obj.length == 14) {
		var calStr1 = "2345670892345", biVal = 0, tmpCal, restCal;

		for (i=0; i <= 12; i++) {
			if (obj.substring(i,i+1) == "-")
				tmpCal = 1
			else
				biVal = biVal + (parseFloat(obj.substring(i,i+1)) * parseFloat(calStr1.substring(i,i+1)));
		}

		restCal = 11 - (biVal % 11);

		if (restCal == 11) {
			restCal = 1;
		}

		if (restCal == 10) {
			restCal = 0;
		}

		if (restCal == parseFloat(obj.substring(13,14))) {
			return true;
		} else {
			return false;
		}
	}
}

function strnumkeyup2(field) {
	if (!isNumber(field.value)) {
		alert("숫자만 입력하세요.");
		field.value=strLenCnt(field.value,field.value.length - 1);
		field.focus();
		return;
	}
	if (field.name == "jumin1") {
		if (field.value.length == 6) {
			form1.jumin2.focus();
		}
	}
}

function CheckForm() {
	try {
		if(document.form1.name.value.length==0) {
			alert("이름을 입력하세요.");
			document.form1.name.focus();
			return;
		}
	} catch (e) {return;}
<?if($_data->resno_type!="N"){?>
	try {
		jumin1=document.form1.jumin1;
		jumin2=document.form1.jumin2;

		if (jumin1.value.length==0) {
			alert("주민등록번호를 입력하세요.");
			jumin1.focus();
			return;
		}
		if (jumin2.value.length==0) {
			alert("주민등록번호를 입력하세요.");
			jumin2.focus();
			return;
		}

		var bb;
		bb = chkCtyNo(jumin1.value+"-"+jumin2.value);

		if (!bb) {
			alert("잘못된 주민등록번호 입니다.\n\n다시 입력하세요");
			jumin1.focus();
			return;
		}
	} catch (e) {return;}
<?}?>

	document.form1.mode.value="send";

<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["MLOST"]=="Y") {?>
	document.form1.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>findpwd.php';
<?}?>
	document.form1.submit();
}
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=mode value="">
<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["MLOST"]=="Y") {?>
<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<?}?>
<?
if ($leftmenu!="N") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/findpwd_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/findpwd_title.gif\" border=\"0\" alt=\"비밀번호 찾기\"></td>";
	} else {
		echo "<td>\n";
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/findpwd_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/findpwd_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/findpwd_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		echo "</td>\n";
	}
	echo "</tr>\n";
}

echo "<tr>\n";
echo "	<td align=\"center\">\n";
if($newdesign=="Y") {	//개별디자인
	$pattern=array("(\[NAME\])","(\[JUMIN1\])","(\[JUMIN2\])","(\[EMAIL\])","(\[OK\])","(\[LOGIN\])");
	$replace=array("<input type=text name=name value=\"\" maxlength=20 style=\"width:120\" class=input>","<input type=text name=jumin1 value=\"\" maxlength=6 style=\"width:50\" onkeyup=\"strnumkeyup2(this);\" class=input>","<input type=text name=jumin2 value=\"\" maxlength=7 onkeyup=\"strnumkeyup2(this);\" style=\"width:58\" class=input>","<input type=text name=email value=\"\" maxlength=50 style=\"width:180\" class=input>","\"JavaScript:CheckForm()\"",$Dir.FrontDir."login.php");
	$body=preg_replace($pattern,$replace,$body);
	echo $body;
} else {	//템플릿
	include($Dir.TempletDir."member/findpwd".$_data->design_member.".php");
}
echo "	</td>\n";
echo "</tr>\n";
?>
</form>
</table>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>