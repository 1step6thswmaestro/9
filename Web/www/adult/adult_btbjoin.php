<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$ip = getenv("REMOTE_ADDR");
$type=$_POST["type"];

$sql="SELECT * FROM tblshopinfo ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
mysql_free_result($result);

$ssl_type=$row->ssl_type;
$ssl_domain=$row->ssl_domain;
$ssl_port=$row->ssl_port;
$ssl_page=$row->ssl_page;

if($ssl_type=="Y" && $num=strpos(" ".$ssl_page,"MJOIN=")) {
	$is_ssl=substr($ssl_page,$num+5,1);
}

unset($adultauthid);
unset($adultauthpw);
if(strlen($row->adultauth)>0) {
	$tempadult=explode("=",$row->adultauth);
	if($tempadult[0]=="Y") {
		$adultauthid=$tempadult[1];
		$adultauthpw=$tempadult[2];
	}
}

$reserve_join=(int)$row->reserve_join;
$recom_ok=$row->recom_ok;
$recom_memreserve=(int)$row->recom_memreserve;
$recom_addreserve=(int)$row->recom_addreserve;
$recom_limit=$row->recom_limit;
if(strlen($recom_limit)==0) $recom_limit=9999999;
$group_code=$row->group_code;
$member_addform=$row->member_addform;
$coupon_ok=$row->coupon_ok;

$shopname=$row->shopname;
$shopurl=$row->shopurl;
$design_mail=$row->design_mail;
$join_msg=$row->join_msg;
$info_email=$row->info_email;

unset($straddform);
unset($scriptform);
if(strlen($member_addform)>0) {
	$straddform.="<tr height=25 bgcolor=#F4F4F4>\n";
	$straddform.="	<td colspan=4 align=center><B>추가정보를 입력하세요.</B></td>\n";
	$straddform.="</tr>\n";

	$fieldarray=explode("=",$member_addform);
	$num=sizeof($fieldarray)/3;
	for($i=0;$i<$num;$i++) {
		if (substr($fieldarray[$i*3],-1,1)=="^") {
			$fieldarray[$i*3]="<font color=red>＊</font>".substr($fieldarray[$i*3],0,strlen($fieldarray[$i*3])-1);
			$field_check[$i]="OK";
		}

		$straddform.="<tr height=25 bgcolor=#FFFFFF>\n";
		$straddform.="	<td align=right bgcolor=#FFFFFF style=\"padding-right:5\">".$fieldarray[$i*3]."</td>\n";
		$straddform.="	<td style=\"padding-left:5\" colspan=\"3\"><input type=text name=\"etc[".$i."]\" value=\"".$etc[$i]."\" size=\"".$fieldarray[$i*3+1]."\" maxlength=\"".$fieldarray[$i*3+2]."\" id=\"etc_".$i."\"></td>\n";
		$straddform.="</tr>\n";

		if ($field_check[$i]=="OK") {
			$scriptform.="	if (document.getElementById('etc_".$i."').value==0) {\n";
			$scriptform.="		alert('필수입력사항을 입력하세요.');\n";
			$scriptform.="		document.getElementById('etc_".$i."').focus();\n";
			$scriptform.="		return;\n";
			$scriptform.="	}\n";
		}
	}
}

if($type=="insert") {
	$history="-1";
	$sslchecktype="";
	if($_POST["ssltype"]=="ssl" && strlen($_POST["sessid"])==64) {
		$sslchecktype="ssl";
		$history="-2";
	}
	if($sslchecktype=="ssl") {
		$secure_data=getSecureKeyData($_POST["sessid"]);
		if(!is_array($secure_data)) {
			echo "<html><head><title></title></head><body onload=\"alert('보안인증 정보가 잘못되었습니다.');window.close();\"></body></html>";exit;
		}
		foreach($secure_data as $key=>$val) {
			${$key}=$val;
		}
	} else {
		$id=trim($_POST["id"]);
		$passwd1=$_POST["passwd1"];
		$passwd2=$_POST["passwd2"];
		$name=trim($_POST["name"]);
		$email=trim($_POST["email"]);
		$home_tel=trim($_POST["home_tel"]);
		$home_post1=trim($_POST["home_post1"]);
		$home_post2=trim($_POST["home_post2"]);
		$home_addr1=trim($_POST["home_addr1"]);
		$home_addr2=trim($_POST["home_addr2"]);
		$mobile=trim($_POST["mobile"]);
		$office_post1=trim($_POST["office_post1"]);
		$office_post2=trim($_POST["office_post2"]);
		$office_addr1=trim($_POST["office_addr1"]);
		$office_addr2=trim($_POST["office_addr2"]);
		$rec_id=trim($_POST["rec_id"]);
		$etc=$_POST["etc"];
	}

	$onload="";

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

	if($recom_ok=="Y" && strlen($rec_id)>0) {
		$sql = "SELECT COUNT(*) as cnt FROM tblmember WHERE id='".trim($rec_id)."' AND member_out!='Y' ";
		$rec_result = mysql_query($sql,get_db_conn());
		$rec_row = mysql_fetch_object($rec_result);
		$rec_num = $rec_row->cnt;
		mysql_free_result($rec_result);

		$rec_cnt=0;
		$sql = "SELECT rec_cnt FROM tblrecommendmanager WHERE rec_id='".trim($rec_id)."'";
		$rec_result = mysql_query($sql,get_db_conn());
		if($rec_row = mysql_fetch_object($rec_result)) {
			$rec_cnt = (int)$rec_row->rec_cnt;
		}
		mysql_free_result($rec_result);
	}

	if(strlen($onload)>0) {

	} else if(strlen(trim($id))==0) {
		$onload="아이디 입력이 잘못되었습니다.";
	} else if(!IsAlphaNumeric($id)) {
		$onload="아이디는 영문,숫자를 조합하여 4~12자 이내로 입력하셔야 합니다.";
	} else if(!eregi("(^[0-9a-zA-Z]{4,12}$)",$id)) {
		$onload="아이디는 영문,숫자를 조합하여 4~12자 이내로 입력하셔야 합니다.";
	} else if(strlen(trim($name))==0) {
		$onload="이름 입력이 잘못되었습니다.";
	} else if(strlen(trim($email))==0) {
		$onload="이메일을 입력하세요.";
	} else if(!ismail($email)) {
		$onload="이메일 입력이 잘못되었습니다.";
	} else if(strlen(trim($home_tel))==0) {
		$onload="집전화를 입력하세요.";
	} else if($rec_num==0 && strlen($rec_id)!=0) {
		$onload="추천인 ID 입력이 잘못되었습니다.";
	} else {
		if (strlen($adultauthid)>0 && strlen($name)>0 && strlen($resno1)>0 && strlen($resno2)>0) {
			include($Dir."lib/name_check.php");
			$onload=getNameCheck($name, $resno1, $resno2, $adultauthid, $adultauthpw);
		}
		if(!$onload) {
			$rsql = "SELECT id FROM tblmember WHERE resno='".$resno."'";
			$result2 = mysql_query($rsql,get_db_conn());
			$num = mysql_num_rows($result2);
			mysql_free_result($result2);
			if(!$onload) {
				$sql = "SELECT id FROM tblmember WHERE id='".$id."' ";
				$result=mysql_query($sql,get_db_conn());
				if($row=mysql_fetch_object($result)) {
					$onload="ID가 중복되었습니다.\\n\\n다른 아이디를 사용하시기 바랍니다.";
				}
				mysql_free_result($result);
			}
			if(!$onload) {
				$sql = "SELECT id FROM tblmemberout WHERE id='".$id."' ";
				$result=mysql_query($sql,get_db_conn());
				if($row=mysql_fetch_object($result)) {
					$onload="ID가 중복되었습니다.\\n\\n다른 아이디를 사용하시기 바랍니다.";
				}
				mysql_free_result($result);
			}
			if(!$onload) {
				$sql = "SELECT email FROM tblmember WHERE email='".$email."' ";
				$result=mysql_query($sql,get_db_conn());
				if($row=mysql_fetch_object($result)) {
					$onload="이메일이 중복되었습니다.\\n\\n다른 이메일을 사용하시기 바랍니다.";
				}
				mysql_free_result($result);
			}
			if(!$onload) {
				//insert
				$date=date("YmdHis");
				$gender=substr($resno2,0,1);
				$home_post=$home_post1.$home_post2;
				$home_addr=$home_addr1."=".$home_addr2;
				$office_post=$office_post1.$office_post2;
				$office_addr="";
				if(strlen($office_post)==6) $office_addr=$office_addr1."=".$office_addr2;

				$sql = "INSERT tblmember SET ";
				$sql.= "id			= '".$id."', ";
				$sql.= "passwd		= '".md5($passwd1)."', ";
				$sql.= "name		= '".$name."', ";
				$sql.= "resno		= '".$resno."', ";
				$sql.= "email		= '".$email."', ";
				$sql.= "mobile		= '".$mobile."', ";
				$sql.= "gender		= '".$gender."', ";
				$sql.= "home_post	= '".$home_post."', ";
				$sql.= "home_addr	= '".$home_addr."', ";
				$sql.= "home_tel	= '".$home_tel."', ";
				$sql.= "office_post	= '".$office_post."', ";
				$sql.= "office_addr	= '".$office_addr."', ";
				$sql.= "office_tel	= '".$office_tel."', ";
				$sql.= "reserve		= '".$reserve_join."', ";
				$sql.= "joinip		= '".$ip."', ";
				$sql.= "ip			= '".$ip."', ";
				$sql.= "date		= '".$date."', ";
				$sql.= "confirm_yn	= 'N', ";
				if($recom_ok=="Y" && $rec_num!=0 && $rec_cnt<$recom_limit && strlen($rec_id)>0) {
					$sql.= "rec_id	= '".$rec_id."', ";
				}
				if(strlen($group_code)>0) {
					$sql.= "group_code='".$group_code."', ";
				}
				$sql.= "etcdata		= '".$etcdata."' ";
				$insert=mysql_query($sql,get_db_conn());
				if (mysql_errno()==0) {
					if ($reserve_join>0) {
						$yn="Y";
						$sql = "INSERT tblreserve SET ";
						$sql.= "id			= '".$id."', ";
						$sql.= "reserve		= ".$reserve_join.", ";
						$sql.= "reserve_yn	= 'Y', ";
						$sql.= "content		= '가입축하 적립금입니다. 감사합니다.', ";
						$sql.= "orderdata	= '', ";
						$sql.= "date		= '".date("YmdHis",time()-1)."' ";
						$insert = mysql_query($sql,get_db_conn());
					}


					// 추천인 적립금
					if($recom_ok=="Y" && $rec_num!=0 && $rec_cnt<$recom_limit && strlen($rec_id)>0) {
						$arr = array();
						$arr['recomMem'] = $rec_id; // 추천인 아이디
						$arr['newMeme'] = $id; // 추천 받은 회원 아이디
						recommandJoin( $arr );
					}


					//쿠폰발생 (회원가입시 발급되는 쿠폰)
					if($coupon_ok=="Y") {
						$date = date("YmdHis");
						$sql = "SELECT coupon_code, date_start, date_end FROM tblcouponinfo ";
						$sql.= "WHERE display='Y' AND issue_type='M' ";
						$sql.= "AND (date_end>'".substr($date,0,10)."' OR date_end='')";
						$result = mysql_query($sql,get_db_conn());

						$sql="INSERT INTO tblcouponissue (coupon_code,id,date_start,date_end,date) VALUES ";
						$couponcnt ="";
						$count=0;
						
						while($row = mysql_fetch_object($result)) {
							if($row->date_start>0) {
								$date_start=$row->date_start;
								$date_end=$row->date_end;
							} else {
								$date_start = substr($date,0,10);
								$date_end = date("Ymd",mktime(0,0,0,substr($date,4,2),substr($date,6,2)+abs($row->date_start),substr($date,0,4)))."23";
							}
							$sql.=" ('".$row->coupon_code."','".$id."','".$date_start."','".$date_end."','".$date."'),";
							$couponcnt="'".$row->coupon_code."',";
							$count++;
						}
						mysql_free_result($result);
						if($count>0) {
							$sql = substr($sql,0,-1);
							mysql_query($sql,get_db_conn());
							if(!mysql_errno()) {
								$couponcnt = substr($couponcnt,0,-1);
								$sql = "UPDATE tblcouponinfo SET issue_no=issue_no+1 ";
								$sql.= "WHERE coupon_code IN (".$couponcnt.")";
								mysql_query($sql,get_db_conn());
								$msg = "회원 가입시 쿠폰이 발급되었습니다.";
							}
						}
					}

					//가입메일 발송 처리
					if(strlen($email)>0) {
						SendJoinMail($shopname, $shopurl, $design_mail, $join_msg, $info_email, $email, $name);
					}

					//가입 SMS 발송 처리
					$sql = "SELECT * FROM tblsmsinfo WHERE (mem_join='Y' OR admin_join='Y') ";
					$result= mysql_query($sql,get_db_conn());
					if($row=mysql_fetch_object($result)) {
						$sms_id=$row->id;
						$sms_authkey=$row->authkey;

						$admin_join=$row->admin_join;
						$mem_join=$row->mem_join;
						$msg_mem_join=$row->msg_mem_join;
						
						$pattern=array("(\[ID\])","(\[NAME\])");
						$replace=array($id,$name);
						$msg_mem_join=preg_replace($pattern,$replace,$msg_mem_join);
						$msg_mem_join=AddSlashes($msg_mem_join);

						$mem_join_msg=$row->mem_join_msg;
						$mem_join_msg=preg_replace($pattern, $replace, $mem_join_msg);
						$mem_join_msg=addslashes($mem_join_msg);
						$adminphone=$row->admin_tel;
						if(strlen($row->subadmin1_tel)>8) $adminphone.=",".$row->subadmin1_tel;
						if(strlen($row->subadmin2_tel)>8) $adminphone.=",".$row->subadmin2_tel;
						if(strlen($row->subadmin3_tel)>8) $adminphone.=",".$row->subadmin3_tel;

						$fromtel=$row->return_tel;
						mysql_free_result($result);

						$mobile=str_replace(" ","",$mobile);
						$mobile=str_replace("-","",$mobile);
						$adminphone=str_replace(" ","",$adminphone);
						$adminphone=str_replace("-","",$adminphone);

						$etcmessage="회원가입 축하메세지(회원)";
						$date=0;
						if($mem_join=="Y") {
							$temp=SendSMS($sms_id, $sms_authkey, $mobile, "", $fromtel, $date, $msg_mem_join, $etcmessage);
						}

						if($row->sleep_time1!=$row->sleep_time2) {
							$date="0";
							$time = date("Hi");
							if($row->sleep_time2<"12" && $time<=substr("0".$row->sleep_time2,-2)."59") $time+=2400;
							if($row->sleep_time2<"12" && $row->sleep_time1>$row->sleep_time2) $row->sleep_time2+=24;

							if($time<substr("0".$row->sleep_time1,-2)."00" || $time>=substr("0".$row->sleep_time2,-2)."59") {
								if($time<substr("0".$row->sleep_time1,-2)."00") $day = date("d");
								else $day=date("d")+1;
								$date = date("Y-m-d H:i:s",mktime($row->sleep_time1,0,0,date("m"),$day,date("Y")));
							}
						}
						$etcmessage="회원가입 축하메세지(관리자)";
						if($admin_join=="Y") {
							$temp=SendSMS($sms_id, $sms_authkey, $adminphone, "", $fromtel, $date, $mem_join_msg, $etcmessage);
						}
					}
					echo "<html><head><title></title></head><body onload=\"alert('등록되었습니다. 감사합니다.');window.close();\"></body></html>";exit;
				} else {
					$onload="ID가 중복되었거나 회원등록 중 오류가 발생하였습니다.";
				}
			}
		}
	}
	if(strlen($onload)>0) {
		echo "<html><head><title></title></head><body onload=\"alert('".$onload."');history.go(".$history.")\"></body></html>";exit;
	}
}
?>

<html>
<head>
<title>회원가입</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td	{font-family:"굴림,돋움";color:#4B4B4B;font-size:12px;line-height:17px;}
BODY,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:#635C5A;text-decoration:none;}
A:visited {color:#545454;text-decoration:none;}
A:active  {color:#5A595A;text-decoration:none;}
A:hover  {color:#545454;text-decoration:underline;}
.input{font-size:12px;BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT: 18px; BACKGROUND-COLOR: #ffffff;padding-top:2pt; padding-bottom:1pt; height:19px}
.select{color:#444444;font-size:12px;}
.textarea {border:solid 1;border-color:#e3e3e3;font-family:돋음;font-size:9pt;color:333333;overflow:auto; background-color:transparent}
</style>
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

function CheckForm() {
	form=document.form1;
	
	if(form.id.value.length==0) {
		alert("아이디를 입력하세요."); form.id.focus(); return;
	}
	if(form.id.value.length<4 || form.id.value.length>12) {
		alert("아이디는 4자 이상 12자 이하로 입력하셔야 합니다."); form.id.focus(); return;
	}
	if (CheckFormData(form.id.value)==false) {
   		alert("ID는 영문, 숫자를 조합하여 4~12자 이내로 등록이 가능합니다."); form.id.focus(); return;			
   	}
	if(form.passwd1.value.length==0) {
		alert("비밀번호를 입력하세요."); form.passwd1.focus(); return;
	}
	if(form.passwd1.value!=form.passwd2.value) {
		alert("비밀번호가 일치하지 않습니다."); form.passwd2.focus(); return;
	}
	if(form.name.value.length==0) {
		alert("고객님의 이름을 입력하세요."); form.name.focus(); return;
	}
	if(form.name.value.length>10) {
		alert("이름은 한글 5자, 영문 10자 이내로 입력하셔야 합니다."); form.name.focus(); return;
	}
	
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

<?=$scriptform?>

	form.type.value="insert";
<?if($ssl_type=="Y" && strlen($ssl_domain)>0 && strlen($ssl_port)>0 && $is_ssl=="Y") {?>
	form.action='https://<?=$ssl_domain?><?=($ssl_port!="443"?":".$ssl_port:"")?>/<?=RootPath.SecureDir?>adult_btbjoin.php';
<?}?>

	if(confirm("회원가입을 하겠습니까?"))
		form.submit();
	else
		return;
}

function f_addr_search(form,post,addr,gbn) {
	window.open("<?=$Dir.FrontDir?>addr_search.php?form="+form+"&post="+post+"&addr="+addr+"&gbn="+gbn,"f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");		
}

function idcheck() {
	window.open("<?=$Dir.FrontDir?>iddup.php?id="+document.form1.id.value,"","height=100,width=200");
}

//-->
</SCRIPT>
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" style="overflow-x:hidden">
<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<TR>
	<TD><IMG SRC="<?=$Dir.AdultDir?>images/b2b_join_title.gif" border="0"></TD>
</TR>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td background="<?=$Dir.AdultDir?>images/b2b_join_left01.gif"><IMG SRC="<?=$Dir.AdultDir?>images/b2b_join_left01.gif" border="0"></td>
		<td width="100%" style="padding:5px;" bgcolor="#FFFFFF">
		<table cellpadding="0" cellspacing="0" width="100%">
		<col align="right" style="padding-right:5px;"></col>
		<col width="100"></col>
		<col width="35" align="right" style="padding-right:5px;"></col>
		<col width="100"></col>
		<tr>
			<td colspan="4"><font color="#FF6600" style="font-size:8pt;"><b>(*)는 필수항목입니다.</b></font></td>
		</tr>
		<tr>
			<td HEIGHT="10" colspan="4" background="<?=$Dir.AdultDir?>images/adultintro_skin_line.gif"></td>
		</tr>
		<tr>
			<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>아이디</b></font></td>
			<td colspan="3"><input type=text name="id" value="<?=$id?>" maxlength="12" style="width:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"><a href="javascript:idcheck();"><img src="<?=$Dir.AdultDir?>images/adultintro_skin_btn1.gif" border="0" align="absmiddle" hspace="3"></a><br><font color="#FF6600" style="font-size:8pt;">(영문,숫자를 조합하여 4~12자 이내로 입력하세요)</font></td>
		</tr>
		<tr>
			<td HEIGHT="10" colspan="4" background="<?=$Dir.AdultDir?>images/adultintro_skin_line.gif"></td>
		</tr>
		<tr>
			<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>비밀번호</b></font></td>
			<td><input type=password name="passwd1" value="<?=$passwd1?>" maxlength="20" style="width:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>확인</b></font></td>
			<td><input type=password name="passwd2" value="<?=$passwd2?>" maxlength="20" style="width:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
		</tr>
		<tr>
			<td HEIGHT="10" colspan="4" background="<?=$Dir.AdultDir?>images/adultintro_skin_line.gif"></td>
		</tr>
		<tr>
			<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>이름</b></font></td>
			<td colspan="3"><input type=text name="name" value="<?=$name?>" maxlength="15" style="width:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
		</tr>
		<tr>
			<td HEIGHT="10" colspan="4" background="<?=$Dir.AdultDir?>images/adultintro_skin_line.gif"></td>
		</tr>
		<tr>
			<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>이메일</b></font></td>
			<td colspan="3"><input type=text name="email" value="<?=$email?>" maxlength="100" style="WIDTH:100%;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
		</tr>
		<tr>
			<td HEIGHT="10" colspan="4" background="<?=$Dir.AdultDir?>images/adultintro_skin_line.gif"></td>
		</tr>
		<tr>
			<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>집전화</b></font></td>
			<td colspan="3"><input type=text name="home_tel" value="<?=$home_tel?>" maxlength="15" style="width:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
		</tr>
		<tr>
			<td HEIGHT="10" colspan="4" background="<?=$Dir.AdultDir?>images/adultintro_skin_line.gif"></td>
		</tr>
		<tr>
			<td><font color="#F02800"><b>＊</b></font><font color="#000000"><b>집주소</b></font></td>
			<td colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><input type=text name="home_post1" value="<?=$home_post1?>" readonly style="width:30;BACKGROUND-COLOR:#F7F7F7;" class="input"> - <input type=text name="home_post2" value="<?=$home_post2?>" readonly style="width:30;BACKGROUND-COLOR:#F7F7F7;" class="input"><a href="javascript:f_addr_search('form1','home_post','home_addr1',2);"><img src="<?=$Dir.AdultDir?>images/adultintro_skin_btn3.gif" border="0" align="absmiddle" hspace="3"></a></td>
			</tr>
			<tr>
				<td><input type=text name="home_addr1" value="<?=$home_addr1?>" maxlength="100" readonly style="WIDTH:100%;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td><input type=text name="home_addr2" value="<?=$home_addr2?>" maxlength="100" style="WIDTH:100%;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td HEIGHT="10" colspan="4" background="<?=$Dir.AdultDir?>images/adultintro_skin_line.gif"></td>
		</tr>
		<tr>
			<td><font color="#000000"><b>비상전화(휴대폰)</b></font></td>
			<td colspan="3"><input type=text name="mobile" value="<?=$mobile?>" maxlength="15" style="width:120px;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
		</tr>
		<tr>
			<td HEIGHT="10" colspan="4" background="<?=$Dir.AdultDir?>images/adultintro_skin_line.gif"></td>
		</tr>
		<tr>
			<td><font color="#000000"><b>회사주소</b></font></td>
			<td colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><input type=text name="office_post1" value="<?=$office_post1?>" readonly style="width:30px;BACKGROUND-COLOR:#F7F7F7;" class="input"> - <input type=text name="office_post2" value="<?=$office_post2?>" readonly style="width:30px;BACKGROUND-COLOR:#F7F7F7;" class="input"><a href="javascript:f_addr_search('form1','office_post','office_addr1',2);"><img src="<?=$Dir.AdultDir?>images/adultintro_skin_btn3.gif" border="0" align="absmiddle" hspace="3"></a><br></td>
			</tr>
			<tr>
				<td><input type=text name="office_addr1" value="<?=$office_addr1?>" readonly maxlength="100" style="WIDTH:100%;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			<tr>
				<td><input type=text name="office_addr2" value="<?=$office_addr2?>" maxlength="100" style="WIDTH:100%;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
			</tr>
			</table>
			</td>
		</tr>
		<?if($recom_ok=="Y") {?>
		<tr>
			<td height="8" colspan="4" background="<?=$Dir.AdultDir?>images/adultintro_skin_line.gif"></td>
		</tr>
		<tr>
			<td><font color="#000000"><b>추천ID</b></font></td>
			<td colspan="3" style="padding-bottom:4pt;"><input type=text name="rec_id" value="<?=$rec_id?>" maxlength="12" style="width:120;BACKGROUND-COLOR:#F7F7F7;" class="input"></td>
		</tr>
		<?}?>
<?
	if(strlen($straddform)>0) {
		echo $straddform;
	}
?>
		<tr>
			<td width="438" colspan="4" height="25"><font color="#FF6600">★</font><font color="#FF6600" style="font-size:8pt;"> 기본배송지를 입력하면 상품구매시 자동으로 입력됩니다.</font></td>
		</tr>
		</table>
		</td>
		<td background="<?=$Dir.AdultDir?>images/b2b_join_left02.gif"><IMG SRC="<?=$Dir.AdultDir?>images/b2b_join_left02.gif" border="0"></td>
	</tr>
	</table>
	</TD>
</TR>
<TR>
	<TD><IMG SRC="<?=$Dir.AdultDir?>images/b2b_join_left03.gif" border="0"></TD>
</TR>
<tr>
	<TD align="center" style="padding-top:5px;padding-bottom:20px;"><A HREF="javascript:CheckForm();"><IMG SRC="<?=$Dir.AdultDir?>images/b2b_ok.gif" border="0"></a><A HREF="javascript:window.close();"><IMG SRC="<?=$Dir.AdultDir?>images/b2b_cancel.gif" border="0" hspace="5"></a></TD>
</tr>
<input type=hidden name=type value="">
<?if($ssl_type=="Y" && strlen($ssl_domain)>0 && strlen($ssl_port)>0 && $is_ssl=="Y") {?>
<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<?}?>
</form>
</TABLE>

<?=$onload?>

</body>
</html>