<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}

$extconf = array();
if(false !== $eres = mysql_query("select * from extra_conf where type='memconf'",get_db_conn())){
	if(mysql_num_rows($eres)){
		while($erow = mysql_fetch_assoc($eres)){
			$extconf[$erow['name']] = $erow['value'];
		}
	}
}

$recom_ok=$_data->recom_ok;
$member_addform=$_data->member_addform;

$type=$_POST["type"];
$history="-1";
$sslchecktype="";
if($_POST["ssltype"]=="ssl" && strlen($_POST["sessid"])==64) {
	$sslchecktype="ssl";
	$history="-2";
}

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('회원 아이디가 존재하지 않습니다.');location.href='".$Dir.FrontDir."login.php';\"></body></html>";exit;
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('처음부터 다시 시작하시기 바랍니다.".$row->authidkey."');location.href='".$Dir.FrontDir."login.php';\"></body></html>";exit;
	}

	$id=$row->id;
	$wholesaletype = $row->wholesaletype;
	if($wholesaletype == 'Y'){
		$comp_num = $row->comp_num;
		$comp_owner = $row->comp_owner;
		$comp_type1 = $row->comp_type1;
		$comp_type2 = $row->comp_type2;
	}
	if($type!="modify") {
		$name=$row->name;
		$passwd1="";
		$passwd2="";
		if($_data->resno_type!="N") {
			$resno1=substr($row->resno,0,6);
			$resno2=substr($row->resno,6,7);
		}
		$email=$row->email;
		$home_tel=$row->home_tel;
		//$home_post1=substr($row->home_post,0,3);
		//$home_post2=substr($row->home_post,3,3);
		
		$zipcode_home = $row->home_post;
		
		$home_addr=stripslashes($row->home_addr);
		$home_addr_temp=explode("=",$home_addr);
		$home_addr1=$home_addr_temp[0];
		$home_addr2=stripslashes($home_addr_temp[1]);
		$mobile=$row->mobile;
		//$office_post1=substr($row->office_post,0,3);
		//$office_post2=substr($row->office_post,3,3);

		$zipcode_office = $row->office_post;
		$office_addr=stripslashes($row->office_addr);
		$office_addr_temp=explode("=",$office_addr);
		$office_addr1=$office_addr_temp[0];
		$office_addr2=stripslashes($office_addr_temp[1]);
		$etc=explode("=",$row->etcdata);

		if($row->news_yn=="Y") {
			$news_mail_yn="Y";
			$news_sms_yn="Y";
		} else if($row->news_yn=="M") {
			$news_mail_yn="Y";
			$news_sms_yn="N";
		} else if($row->news_yn=="S") {
			$news_mail_yn="N";
			$news_sms_yn="Y";
		} else if($row->news_yn=="N") {
			$news_mail_yn="N";
			$news_sms_yn="N";
		}
	} else {
		$name=$row->name;

		if($sslchecktype=="ssl") {
			$secure_data=getSecureKeyData($_POST["sessid"]);
			if(!is_array($secure_data)) {
				echo "<html><head><title></title></head><body onload=\"alert('보안인증 정보가 잘못되었습니다.');history.go(".$history.");\"></body></html>";exit;
			}
			foreach($secure_data as $key=>$val) {
				${$key}=$val;
			}
			if($_data->resno_type=="Y") {
				$resno1=substr($row->resno,0,6);
				$resno2=substr($row->resno,6,7);
			}
		} else {
			$oldpasswd=$_POST["oldpasswd"];
			$passwd1=$_POST["passwd1"];
			$passwd2=$_POST["passwd2"];
			if(($_data->resno_type=="M") || ($_data->resno_type=="Y" && (strlen($row->resno)==0 || strlen($row->resno)==41))) {
				$resno1=trim($_POST["resno1"]);
				$resno2=trim($_POST["resno2"]);
			} else if($_data->resno_type=="Y") {
				$resno1=substr($row->resno,0,6);
				$resno2=substr($row->resno,6,7);
			}
			$email=trim($_POST["email"]);
			$news_mail_yn=$_POST["news_mail_yn"];
			$news_sms_yn=$_POST["news_sms_yn"];
			$home_tel=trim($_POST["home_tel"]);
			//$home_post1=trim($_POST["home_post1"]);
			//$home_post2=trim($_POST["home_post2"]);
			$zipcode_home = trim($_POST["home_post1"]);
			$home_addr1=trim($_POST["home_addr1"]);
			$home_addr2=trim($_POST["home_addr2"]);
			$mobile=trim($_POST["mobile"]);
			//$office_post1=trim($_POST["office_post1"]);
			//$office_post2=trim($_POST["office_post2"]);
			$zipcode_office = trim($_POST["office_post1"]);
			$office_addr1=trim($_POST["office_addr1"]);
			$office_addr2=trim($_POST["office_addr2"]);
			$rec_id=trim($_POST["rec_id"]);
			$etc=$_POST["etc"];
		}
	}
	$oldresno=$row->resno;
	$oldemail=$row->email;
	$passwd=$row->passwd;
	$rec_id=$row->rec_id;
	if(strlen($rec_id)==0) {
		$str_rec="추천인 없음";
	} else {
		$str_rec=$rec_id;
	}
	if($recom_ok=="Y") {
		$sql = "SELECT rec_cnt FROM tblrecommendmanager ";
		$sql.= "WHERE rec_id='".$_ShopInfo->getMemid()."' ";
		$result2= mysql_query($sql,get_db_conn());
		if($row2=mysql_fetch_object($result2)) {
			$str_rec.=" <b><font color=#3A3A3A> ".$row2->rec_cnt."명이 당신을 추천하셨습니다.</font></b>";
		}
		mysql_free_result($result2);
	}
} else {
	$_ShopInfo->SetMemNULL();
	$_ShopInfo->Save();
	echo "<html><head><title></title></head><body onload=\"alert('회원 아이디가 존재하지 않습니다.');location.href='".$Dir.FrontDir."login.php';\"></body></html>";exit;
}
mysql_free_result($result);


unset($straddform);
unset($scriptform);
unset($stretc);
if(strlen($member_addform)>0) {
	$straddform.="<tr>";
	$straddform.="	<td height=\"10\" colspan=\"4\"></td>";
	$straddform.="</tr>";
	$straddform.="<tr height=\"23\" bgcolor=\"#585858\">\n";
	$straddform.="	<td colspan=4 align=center style=\"font-size:11px;\"><font color=\"FFFFFF\"><b>추가정보를 입력하세요.</b></font></td>\n";
	$straddform.="</tr>\n";
	$straddform.="<tr>";
	$straddform.="	<td height=\"5\" colspan=\"4\"></td>";
	$straddform.="</tr>";

	$fieldarray=explode("=",$member_addform);
	$num=sizeof($fieldarray)/3;
	for($i=0;$i<$num;$i++) {
		if (substr($fieldarray[$i*3],-1,1)=="^") {
			$fieldarray[$i*3]="<font color=\"#F02800\"><b>＊</b></font><font color=\"#000000\"><b>".substr($fieldarray[$i*3],0,strlen($fieldarray[$i*3])-1)."</b></font>";
			$field_check[$i]="OK";
		} else {
			$fieldarray[$i*3]="<font color=\"#000000\"><b>".$fieldarray[$i*3]."</b></font>";
		}

		$stretc.="<tr>\n";
		$stretc.="	<td align=\"left\" style=\"padding-left:14px\">".$fieldarray[$i*3]."</td>\n";

		$etcfield[$i]="<input type=text name=\"etc[".$i."]\" value=\"".$etc[$i]."\" size=\"".$fieldarray[$i*3+1]."\" maxlength=\"".$fieldarray[$i*3+2]."\" id=\"etc_".$i."\" style=\"BACKGROUND-COLOR:#F7F7F7;\" class=\"input\">";

		$stretc.="	<td colspan=\"3\">".$etcfield[$i]."</td>\n";
		$stretc.="</tr>\n";
		$stretc.="<tr>\n";
		$stretc.="	<td height=\"10\" colspan=\"4\" background=\"".$Dir."images/common/mbmodify/memberjoin_p_skin_line.gif\"></td>";
		$stretc.="</tr>\n";

		if ($field_check[$i]=="OK") {
			$scriptform.="try {\n";
			$scriptform.="	if (document.getElementById('etc_".$i."').value==0) {\n";
			$scriptform.="		alert('필수입력사항을 입력하세요.');\n";
			$scriptform.="		document.getElementById('etc_".$i."').focus();\n";
			$scriptform.="		return;\n";
			$scriptform.="	}\n";
			$scriptform.="} catch (e) {}\n";
		}
	}
	$straddform.=$stretc;
}

if($type=="modify") {
	$onload="";
	$resno=$resno1.$resno2;

	for($i=0;$i<10;$i++) {
		if(strpos($etc[$i],"=")) {
			$onload="추가정보에 입력할 수 없는 문자가 포함되었습니다.";
			break;
		}
		if($i!=0) {
			$etcdata=$etcdata."=";
		}
		$etcdata=$etcdata.$etc[$i];
	}

	if(strlen($onload)>0) {

	} else if($passwd!=md5($oldpasswd)) {
		$onload="기존 비밀번호가 일치하지 않습니다.";
	} else if($_data->resno_type=="M" && strlen(trim($resno))!=13) {
		$onload="주민등록번호 입력이 잘못되었습니다.";
	} else if($_data->resno_type=="M" && !chkResNo($resno)) {
		$onload="잘못된 주민등록번호 입니다.\\n\\n확인 후 다시 입력하시기 바랍니다.";
	} else if($_data->resno_type=="Y" && strlen($oldresno)==13 && $oldresno!=$resno) {
		$onload="주민등록번호 변경이 불가능합니다.";
	} else if($_data->resno_type=="M" && getAgeResno($resno)<14) {
		$onload="만 14세 미만의 아동은 법적대리인의 동의가 있어야 합니다!\\n\\n 당사 쇼핑몰로 연락주시기 바랍니다.";
	} else if($_data->resno_type=="M" && $_data->adult_type=="Y" && getAgeResno($resno)<19) {
		$onload="본 쇼핑몰은 성인만 이용가능하므로 해당 주민등록번호는 사용하실 수 없습니다.";
	} else if(strlen(trim($email))==0) {
		$onload="이메일을 입력하세요.";
	} else if(!ismail($email)) {
		$onload="이메일 입력이 잘못되었습니다.";
	} else if(strlen(trim($home_tel))==0) {
		$onload="집전화를 입력하세요.";
	} else if(strlen(trim($mobile))==0) {
		$onload="휴대전화를 입력하세요.";
	}else if(!preg_match('/[0-9]{5}/',$zipcode_home)) {
		$onload="집 우편번호 입력이 잘못되었습니다.";
	} else if(strlen($zipcode_office) > 0 && !preg_match('/[0-9]{5}/',$zipcode_office)){
		$onload="회사 우편번호 입력이 잘못되었습니다.";
	}else {
		unset($adultauthid);
		unset($adultauthpw);
		if(strlen($_data->adultauth)>0) {
			$tempadult=explode("=",$_data->adultauth);
			if($tempadult[0]=="Y") {
				$adultauthid=$tempadult[1];
				$adultauthpw=$tempadult[2];
			}
		}

		if ($_data->resno_type=="M" && strlen($adultauthid)>0 && strlen($name)>0 && strlen($resno1)>0 && strlen($resno2)>0 && ($oldresno!=$resno)) {
			include($Dir."lib/name_check.php");
			$onload=getNameCheck($name, $resno1, $resno2, $adultauthid, $adultauthpw);
		}
		if(!$onload) {
			$num=0;
			if($_data->resno_type=="M" && $oldresno!=$resno) {
				$rsql = "SELECT id FROM tblmember WHERE resno='".$resno."'";
				$result2 = mysql_query($rsql,get_db_conn());
				$num = mysql_num_rows($result2);
				mysql_free_result($result2);
			}
			if($num==0) {
				if($email!=$oldemail) {
					$sql = "SELECT email FROM tblmember WHERE email='".$email."' ";
					$result3=mysql_query($sql,get_db_conn());
					if($row3=mysql_fetch_object($result3)) {
						$onload="이메일이 중복되었습니다.\\n\\n다른 이메일을 사용하시기 바랍니다.";
					}
					mysql_free_result($result3);
				}
				if(!$onload) {
					$home_post=$zipcode_home;//$home_post1.$home_post2;
					$office_post=$zipcode_office;//$office_post1.$office_post2;
					if($news_mail_yn=="Y" && $news_sms_yn=="Y") {
						$news_yn="Y";
					} else if($news_mail_yn=="Y") {
						$news_yn="M";
					} else if($news_sms_yn=="Y") {
						$news_yn="S";
					} else {
						$news_yn="N";
					}

					$home_addr=mysql_escape_string($home_addr1)."=".mysql_escape_string($home_addr2);
					$office_addr="";
					if(strlen($office_post)==5) $office_addr=mysql_escape_string($office_addr1)."=".mysql_escape_string($office_addr2);

					$sql = "UPDATE tblmember SET ";
					if(strlen($passwd1)>0) {
						$sql.= "passwd		= '".md5($passwd1)."', ";
					}
					if(($_data->resno_type=="M" && $oldresno!=$resno) || ($_data->resno_type=="Y" && strlen($oldresno)!=13)) {
						$gender=substr($resno2,0,1);
						$sql.= "resno		= '".$resno."', ";
						$sql.= "gender		= '".$gender."', ";
					}
					$sql.= "email		= '".$email."', ";
					$sql.= "mobile		= '".$mobile."', ";
					$sql.= "news_yn		= '".$news_yn."', ";
					$sql.= "home_post	= '".$home_post."', ";
					$sql.= "home_addr	= '".$home_addr."', ";
					$sql.= "home_tel	= '".$home_tel."', ";
					$sql.= "office_post	= '".$office_post."', ";
					$sql.= "office_addr	= '".$office_addr."', ";
					$sql.= "office_tel	= '".$office_tel."', ";
					$sql.= "etcdata		= '".$etcdata."' ";
					$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
					$update=mysql_query($sql,get_db_conn());

					if($_ShopInfo->getMememail()!=$email) {
						$_ShopInfo->setMememail($email);
						$_ShopInfo->Save();
					}
					echo "<html><head><title></title></head><body onload=\"alert('개인정보 수정이 완료되었습니다.\\n\\n감사합니다.');location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
				}
			} else {
				$onload="주민번호가 중복되었습니다.";
			}
		}
	}
	if(strlen($onload)>0) {
		echo "<html><head><title></title></head><body onload=\"alert('".$onload."');history.go(".$history.")\"></body></html>";exit;
	}
}

if(strlen($news_mail_yn)==0) $news_mail_yn="Y";
if(strlen($news_sms_yn)==0) $news_sms_yn="Y";
$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$name=$row->name;
	$passwd1="";
	$passwd2="";
	if($_data->resno_type!="N") {
		$resno1=substr($row->resno,0,6);
		$resno2=substr($row->resno,6,7);
	}
	$email=$row->email;
	$home_tel=$row->home_tel;
	$home_post1=$row->home_post;
	//$home_post2=substr($row->home_post,3,3);

	$home_addr=stripslashes($row->home_addr);
	$home_addr_temp=explode("=",$home_addr);
	$home_addr1=$home_addr_temp[0];
	$home_addr2=stripslashes($home_addr_temp[1]);
	$mobile=$row->mobile;
	$office_post1=$row->office_post;
	//$office_post2=substr($row->office_post,3,3);
	$office_addr=stripslashes($row->office_addr);
	$office_addr_temp=explode("=",$office_addr);
	$office_addr1=$office_addr_temp[0];
	$office_addr2=stripslashes($office_addr_temp[1]);
	$etc=explode("=",$row->etcdata);

	if($row->news_yn=="Y") {
		$news_mail_yn="Y";
		$news_sms_yn="Y";
	} else if($row->news_yn=="M") {
		$news_mail_yn="Y";
		$news_sms_yn="N";
	} else if($row->news_yn=="S") {
		$news_mail_yn="N";
		$news_sms_yn="Y";
	} else if($row->news_yn=="N") {
		$news_mail_yn="N";
		$news_sms_yn="N";
	}

	switch($row->gender){
		case "1":
			$gender = "남자";
		break;
		case "2":
			$gender = "여자";
		break;
		default:
			$gender = "선택사항 없음";
		break;
	}
	(strlen($row->birth)>0)? $birth=$row->birth:$birth="입력정보 없음";
}
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 회원정보수정</TITLE>
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
	if (field.name == "resno1") {
		if (field.value.length == 6) {
			form1.resno2.focus();
		}
	}
}

function CheckFormData(data) {
	var numstr = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	var thischar;
	var count = 0;
	data = data.toUpperCase( data )

	for ( var i=0; i < data.length; i++ ) {
		thischar = data.substring(i, i+1 );
		if ( numstr.indexOf( thischar ) != -1 )
			count++;
	}
	if ( count == data.length )
		return(true);
	else
		return(false);
}

function AdultCheck(resno1,resno2) {
	gbn=resno2.substring(0,1);
	date=new Date();
	if(gbn=="3" || gbn=="4") {
		year="20"+resno1.substring(0,2);
	} else {
		year="19"+resno1.substring(0,2);
	}
	age=parseInt(date.getYear())-parseInt(year);
}


function CheckForm() {
	form=document.form1;

	if(form.oldpasswd.value.length==0) {
		alert("현재 사용중인 비밀번호를 입력하세요."); form.oldpasswd.focus(); return;
	}

	if(form.passwd1.value!=form.passwd2.value) {
		alert("신규비밀번호가 일치하지 않습니다."); form.passwd2.focus(); return;
	}

<?if(($_data->resno_type=="M") || ($_data->resno_type=="Y" && (strlen($oldresno)==0 || strlen($oldresno)==41))){?>
	resno1=form.resno1;
	resno2=form.resno2;
	if (resno1.value.length==0) {
		alert("주민등록번호를 입력하세요.");
		resno1.focus();
		return;
	}
	if (resno2.value.length==0) {
		alert("주민등록번호를 입력하세요.");
		resno2.focus();
		return;
	}

	var bb;
	bb = chkCtyNo(resno1.value+"-"+resno2.value);

	if (!bb) {
		alert("잘못된 주민등록번호 입니다.\n\n다시 입력하세요");
		resno1.focus();
		return;
	}
	if(AdultCheck(resno1.value,resno2.value)<14) {
		alert("만 14세 미만 아동의 주민번호는 이용할 수 없습니다.");
		return;
	}

	<?if($_data->adult_type=="Y"){?>
		if(AdultCheck(resno1.value,resno2.value)<19) {
			alert("주민등록번호가 잘못되었습니다.\n\n본 쇼핑몰은 성인만 이용가능합니다.");
			return;
		}
	<?}?>
<?}?>

	if(form.email.value.length==0) {
		alert("이메일을 입력하세요."); form.email.focus(); return;
	}
	if(!IsMailCheck(form.email.value)) {
		alert("이메일 형식이 맞지않습니다.\n\n확인하신 후 다시 입력하세요."); form.email.focus(); return;
	}
	if(form.home_tel.value.length==0) {
		alert("집전화번호를 입력하세요."); form.home_tel.focus(); return;
	}
	if(form.home_post1.value.length==0 || form.home_addr1.value.length==0) {
		alert("집주소를 입력하세요.");
		return;
	}
	if(form.home_addr2.value.length==0) {
		alert("집주소의 상세주소를 입력하세요."); form.home_addr2.focus(); return;
	}
	if(form.mobile.value.length==0) {
		alert("휴대전화를 입력하세요."); form.mobile.focus(); return;
	}

<?=$scriptform?>

	if(confirm("회원님의 개인정보를 수정하시겠습니까?")==true) {
		form.type.value="modify";

<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["MEDIT"]=="Y") {?>
		document.form1.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>mypage_usermodify.php';
<?}?>

		form.submit();
	}
}

function f_addr_search(form,post,addr,gbn) {
	window.open("<?=$Dir.FrontDir?>addr_search.php?form="+form+"&post="+post+"&addr="+addr+"&gbn="+gbn,"f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");
}
//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
	include ($Dir.MainDir.$_data->menu_type.".php");
	include_once("./mypage_groupinfo.php"); //회원등급 출력용
?>

<!-- 마이페이지-회원정보수정 상단 메뉴 -->
<div class="mypagemembergroup">
	<div class="groupinfotext">안녕하세요? <strong class="st1"><?=$_ShopInfo->getMemname()?></strong>님. 회원님의 등급은 <strong class="st2"><?=$groupname?></strong>입니다.</span></div>
	<div class="gruopinfogo"><a href="/front/newpage.php?code=1">회원정책보기 &gt;</a></div>
</div>
<table border="0" cellpadding="0" cellspacing="0" class="mypagetmenu">
	<tr>
		<td class="leftline"><a href="/front/mypage.php">마이페이지</a></td>
		<td class="leftline"><a href="/front/mypage_orderlist.php">주문내역</a></td>
		<td class="leftline"><a href="/front/mypage_personal.php">1:1 문의</a></td>
		<td class="leftline"><a href="/front/mypage_reserve.php">적립금</a></td>
		<td class="leftline"><a href="/front/wishlist.php">찜하기</a></td>
		<td class="leftline"><a href="/front/mypage_coupon.php">쿠폰내역</a></td>
		<? if($_data->recom_url_ok == "Y" || $_data->sns_ok == "Y"){ ?><td class="leftline"><a href="/front/mypage_promote.php">홍보관리</a></td><? } ?>
		<? if(getVenderUsed()==true) { ?><td class="leftline"><a href="/front/mypage_custsect.php">단골매장</a></td><? } ?>
		<td class="nowMyage"><a href="/front/mypage_usermodify.php">회원정보</a></td>
		<td><a href="/front/mypage_memberout.php">회원탈퇴</a></td>
	</tr>
</table>
<div class="currentTitle">
	<div class="titleimage">회원정보</div>
	<div class="current">홈 &gt; 마이페이지 &gt; <SPAN class="nowCurrent">회원정보</span></div>
</div>
<!-- 마이페이지-회원정보수정 상단 메뉴 -->




<table border="0" cellpadding="0" cellspacing="0" width="100%">
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=type value="">
<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["MEDIT"]=="Y") {?>
<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<?}?>

<?
$leftmenu="Y";
if($_data->design_mbmodify=="U") {
	$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='mbmodify'";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);
		$leftmenu=$row->leftmenu;
		$newdesign="Y";
	}
	mysql_free_result($result);
}

if ($leftmenu!="N") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/membermodify_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/membermodify_title.gif\" border=\"0\" alt=\"회원정보수정\"></td>\n";
	} else {
		echo "<td>\n";
		/*
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/membermodify_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/membermodify_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/membermodify_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		*/
		echo "</td>\n";
	}
	echo "</tr>\n";
}

echo "<tr>\n";
echo "	<td align=\"center\">\n";
include ($Dir.TempletDir."mbmodify/mbmodify".$_data->design_mbmodify.".php");
echo "	</td>\n";
echo "</tr>\n";
?>
</form>
</table>

<?=$onload?>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>