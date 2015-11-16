<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/ext/func.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())>0) {
	header("Location:mypage_usermodify.php");
	exit;
}

$ip = getenv("REMOTE_ADDR");

$reserve_join=(int)$_data->reserve_join;
$recom_ok=$_data->recom_ok;
$recom_url_ok=$_data->recom_url_ok;
$armemreserve=explode("", $_data->recom_memreserve_type);
$recom_memreserve=(int)$_data->recom_memreserve;
$recom_addreserve=(int)$_data->recom_addreserve;
$recom_limit=$_data->recom_limit;
if(strlen($recom_limit)==0) $recom_limit=9999999;
$group_code=$_data->group_code;
$member_addform=$_data->member_addform;

unset($adultauthid);
unset($adultauthpw);
if(strlen($_data->adultauth)>0) {
	$tempadult=explode("=",$_data->adultauth);
	if($tempadult[0]=="Y") {
		$adultauthid=$tempadult[1];
		$adultauthpw=$tempadult[2];
	}
}

$type=$_POST["type"];

$extconf = array();
if(false !== $eres = mysql_query("select * from extra_conf where type='memconf'",get_db_conn())){
	if(mysql_num_rows($eres)){
		while($erow = mysql_fetch_assoc($eres)){
			$extconf[$erow['name']] = $erow['value'];
		}
	}
}

unset($straddform);
unset($scriptform);
unset($stretc);
if(strlen($member_addform)>0) {
	$straddform.="<tr>";
	$straddform.="	<td height=\"10\" colspan=\"4\"></td>";
	$straddform.="</tr>";
	$straddform.="<tr height=\"23\" bgcolor=\"#585858\">\n";
	$straddform.="	<td colspan=4 align=center style=\"font-size:11px;\"><font color=\"FFFFFF\" ><b>추가정보를 입력하세요.</b></font></td>\n";
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
		$stretc.="	<td align=\"left\"  style=\"padding-left:14px\">".$fieldarray[$i*3]."</td>\n";

		$etcfield[$i]="<input type=text name=\"etc[".$i."]\" value=\"".$etc[$i]."\" size=\"".$fieldarray[$i*3+1]."\" maxlength=\"".$fieldarray[$i*3+2]."\" id=\"etc_".$i."\" class=\"input\" style=\"BACKGROUND-COLOR:#F7F7F7;\">";

		$stretc.="	<td colspan=\"3\">".$etcfield[$i]."</td>\n";
		$stretc.="</tr>\n";
		$stretc.="<tr>\n";
		$stretc.="	<td height=\"10\" colspan=\"4\" background=\"".$Dir."images/common/mbjoin/memberjoin_p_skin_line.gif\"></td>";
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
			echo "<html><head><title></title></head><body onload=\"alert('보안인증 정보가 잘못되었습니다.');history.go(".$history.");\"></body></html>";exit;
		}
		foreach($secure_data as $key=>$val) {
			${$key}=$val;
		}
	} else {
		$id=trim($_POST["id"]);
		$passwd1=$_POST["passwd1"];
		$passwd2=$_POST["passwd2"];
		$name=trim($_POST["name"]);
		$resno1=trim($_POST["resno1"]);
		$resno2=trim($_POST["resno2"]);
		$email=trim($_POST["email"]);
		$news_mail_yn=$_POST["news_mail_yn"];
		$news_sms_yn=$_POST["news_sms_yn"];
		$home_tel=trim($_POST["home_tel"]);
		$home_post1 = trim($_POST["home_post1"]);
		$home_addr1=trim($_POST["home_addr1"]);
		$home_addr2=trim($_POST["home_addr2"]);
		$mobile=trim($_POST["mobile"]);
		$office_post1 = trim($_POST["office_post1"]);
		$office_addr1=trim($_POST["office_addr1"]);
		$office_addr2=trim($_POST["office_addr2"]);
		$rec_id=trim($_POST["rec_id"]);
		$etc=$_POST["etc"];

		$birth=trim($_POST["birth"]);
		$gender=trim($_POST["gender"]);
		$mcode=trim($_POST["mcode"]);

		$vDiscrNo=trim($_POST["vDiscrNo"]);
		$uniqNo=trim($_POST["uniqNo"]);
		$scitype=trim($_POST["scitype"]);
		$sciReqNum=trim($_POST["sciReqNum"]);

	}

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

	} else if($_data->resno_type!="N" && strlen(trim($resno))!=13) {
		$onload="주민등록번호 입력이 잘못되었습니다.";
	} else if($_data->resno_type!="N" && !chkResNo($resno)) {
		$onload="잘못된 주민등록번호 입니다.\\n\\n확인 후 다시 입력하시기 바랍니다.";
	} else if($_data->resno_type!="N" && getAgeResno($resno)<14) {
		$onload="만 14세 미만의 아동은 회원가입시 법적대리인의 동의가 있어야 합니다!\\n\\n 당사 쇼핑몰로 연락주시기 바랍니다.";
	} else if($_data->resno_type!="N" && $_data->adult_type=="Y" && getAgeResno($resno)<19) {
		$onload="본 쇼핑몰은 성인만 이용가능하므로 회원가입을 하실 수 없습니다.";
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
	} else if(strlen(trim($mobile))==0) {
		$onload="휴대전화를 입력하세요.";
	} else if($rec_num==0 && strlen($rec_id)!=0) {
		$onload="추천인 ID 입력이 잘못되었습니다.";
	} else if(!preg_match('/[0-9]{5}/',$home_post1)) {
		$onload="집 우편번호 입력이 잘못되었습니다.";
	} else if(strlen($office_post1) > 0 && !preg_match('/[0-9]{5}/',$office_post1)){
		$onload="회사 우편번호 입력이 잘못되었습니다.";
	}else {
		if ($_data->resno_type!="N" && strlen($adultauthid)>0 && strlen($name)>0 && strlen($resno1)>0 && strlen($resno2)>0) {
			include($Dir."lib/name_check.php");
			$onload=getNameCheck($name, $resno1, $resno2, $adultauthid, $adultauthpw);
		}
		if(!$onload) {
			if($_data->resno_type!="N") {
				$rsql = "SELECT id FROM tblmember WHERE resno='".$resno."'";
				$result2 = mysql_query($rsql,get_db_conn());
				$num = mysql_num_rows($result2);
				mysql_free_result($result2);
				if ($num>0) {
					$onload="주민번호가 중복되었습니다.";
				}
			}
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



			//$gender = '';
			//$birth = '';
			if(!$onload) {

				if(in_array($gender,array('1','2'))){
					//$gender=$gender;
				}else if(!_empty($resno2)){
					$gender=substr($resno2,0,1);
				}

				if(!_empty($birth)){
					//$birth = $birth;
				}else if(!_empty($resno1)){
					$birth = (intval(substr($resno1,0,2)) < 60)?'20':'19'.substr($resno1,0,6);
				}

				$birth = preg_replace('/[^0-9]/','',$birth);

				if($extconf['reqgender'] == 'Y' && _empty($gender)){
					$onload = '성별을 필수 입력값 입니다.';
				}

				if(!$onload && $extconf['reqbirth'] == 'Y' && _empty($birth)){
					$onload = '생일은 필수 입력값 입니다.';
				}
			}

			if(!$onload) {
				//insert
				$date=date("YmdHis");

				$home_post=$home_post1;//$home_post1.$home_post2;
				$office_post=$office_post1;//$office_post1.$office_post2;
				if($news_mail_yn=="Y" && $news_sms_yn=="Y") {
					$news_yn="Y";
				} else if($news_mail_yn=="Y") {
					$news_yn="M";
				} else if($news_sms_yn=="Y") {
					$news_yn="S";
				} else {
					$news_yn="N";
				}
				if($_data->member_baro=="Y") $confirm_yn="N";
				else $confirm_yn="Y";

				//$home_addr=$home_addr1."=".$home_addr2;
				$home_addr=mysql_escape_string($home_addr1)."=".mysql_escape_string($home_addr2);
				$office_addr="";
				if(strlen($office_post)==5) $office_addr=mysql_escape_string($office_addr1)."=".mysql_escape_string($office_addr2);

				/* 추천인 입력 */
				$url_cnt = 1;
				while($url_cnt > 0){
					$tmpurlid = rand(10000,99999);
					$sql = "SELECT count(1) cnt FROM tblmember WHERE url_id='".$tmpurlid."'";
					$url_result = mysql_query($sql,get_db_conn());
					if($url_row = mysql_fetch_object($url_result)) {
						$url_cnt = (int)$url_row->cnt;
					}
					mysql_free_result($url_result);
				}
				$url_id = $tmpurlid;
				setcookie("my_url_id", $url_id, 0, "/".RootPath, getCookieDomain());
				setcookie("my_id", $id, 0, "/".RootPath, getCookieDomain());
				setcookie("my_name", $name, 0, "/".RootPath, getCookieDomain());
				setcookie("my_email", $email, 0, "/".RootPath, getCookieDomain());

				$sql = "INSERT tblmember SET ";
				$sql.= "id			= '".$id."', ";
				$sql.= "passwd		= '".md5($passwd1)."', ";
				$sql.= "name		= '".$name."', ";
				$sql.= "resno		= '".$resno."', ";
				$sql.= "email		= '".$email."', ";
				$sql.= "mobile		= '".$mobile."', ";
				$sql.= "news_yn		= '".$news_yn."', ";
				$sql.= "gender		= '".$gender."', ";
				$sql.= "birth		= '".$birth."', "; //
				// 본인인증
				if(!empty($vDiscrNo)) $sql.= "vDiscrNo			= '".$vDiscrNo."', ";
				$sql.= "uniqNo			= '".$uniqNo."', ";

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
				$sql.= "confirm_yn	= '".$confirm_yn."', ";
				if($recom_ok=="Y" && $rec_num!=0 && $rec_cnt<$recom_limit && strlen($rec_id)>0) {
					$sql.= "rec_id	= '".$rec_id."', ";
				}
				if(strlen($group_code)>0) {
					$sql.= "group_code='".$group_code."', ";
				}
				$sql.= "etcdata		= '".$etcdata."', ";
				$sql.= "url_id		= '".$url_id."', ";
				$sql.= "devices		= 'P' ";

				//exit($sql);

				$insert=mysql_query($sql,get_db_conn());
				if (mysql_errno()==0) {
					if ($reserve_join>0) {
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
					if($_data->coupon_ok=="Y") {
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
						SendJoinMail($_data->shopname, $_data->shopurl, $_data->design_mail, $_data->join_msg, $_data->info_email, $email, $name);
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

						$mem_join_msg = $row->mem_join_msg;
						$mem_join_msg = preg_replace($pattern, $replace, $mem_join_msg);
						$mem_join_msg = addslashes($mem_join_msg);

						//$smsmessage=$name."님이 ".$id."로 회원가입하셨습니다.";
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

					if($recom_url_ok =="Y"){
						$URL = $Dir.FrontDir."member_urlhongbo.php";
					}else{
						$URL = $Dir.MainDir."main.php";
					}
					echo "<html><head><title></title></head><body><script>alert('등록되었습니다.\\n".$msg."\\n감사합니다.');location.replace('".$URL."');</script></body></html>";
					exit;
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

if(strlen($news_mail_yn)==0) $news_mail_yn="Y";
if(strlen($news_sms_yn)==0) $news_sms_yn="Y";
?>

<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 회원가입</TITLE>
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
	resno1=form.resno1;
	resno2=form.resno2;
	gendercheck = "<?=$extconf['reqgender']?>";
	birthcheck = "<?=$extconf['reqbirth']?>";
	gendercount=0;
	if(gendercheck == "Y"){
		for(var i=0;i<form.gender.length;i++){
			if(form.gender[i].checked==true){
				gendercount++;
			}
		}
	}
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

<?if($_data->resno_type!="N"){?>
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
		alert("만 14세 미만의 아동은 회원가입시\n 법적대리인의 동의가 있어야 합니다!\n\n 당사 쇼핑몰로 연락주시기 바랍니다.");
		return;
	}

	<?if($_data->adult_type=="Y"){?>
		if(AdultCheck(resno1.value,resno2.value)<19) {
			alert("본 쇼핑몰은 성인만 이용가능하므로 회원가입을 하실 수 없습니다.");
			return;
		}
	<?}?>
<?}?>
	if(gendercheck == "Y" && gendercount <= 0){
		alert("성별을 선택하세요");form.gender.value.focus();return;
	}
	if(birthcheck == "Y" && form.birth.value==""){
		alert("생년월일을 입력하세요");form.birth.value.focus();return;
	}
	if(form.email.value.length==0) {
		alert("이메일을 입력하세요."); form.email.focus(); return;
	}
	if(!IsMailCheck(form.email.value)) {
		alert("이메일 형식이 맞지않습니다.\n\n확인하신 후 다시 입력하세요."); form.email.focus(); return;
	}
	if(form.mobile.value.length==0) {
		alert("휴대전화를 입력하세요."); form.mobile.focus(); return;
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


	if(form.idChk.value=="0") {
		alert("아이디 중복 체크를 하셔야 합니다!");
		idcheck();
		return;
	}
	if(form.mailChk.value=="0") {
		alert("이메일 중복 체크를 하셔야 합니다!");
		mailcheck();
		return;
	}

	form.type.value="insert";

<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["MJOIN"]=="Y") {?>
		form.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>member_join.php';
<?}?>
	if(confirm("회원가입을 하겠습니까?"))
		form.submit();
	else
		return;
}

function f_addr_search(form,post,addr,gbn) {
	window.open("<?=$Dir.FrontDir?>addr_search.php?form="+form+"&post="+post+"&addr="+addr+"&gbn="+gbn,"f_post","resizable=no,scrollbars=yes,x=100,y=200,width=430,height=350");
}

function idcheck() {
	form1.idChk.value="0";
	window.open("<?=$Dir.FrontDir?>iddup.php?id="+document.form1.id.value,"","height=150,width=200");
}


function mailcheck() {
	if(!IsMailCheck(form1.email.value)) {
		alert("이메일 형식이 맞지않습니다.\n\n확인하신 후 다시 입력하세요.");
		form1.email.focus();
		return;
	}
	form1.mailChk.value="0";
	window.open("<?=$Dir.FrontDir?>mailcheck.php?email="+document.form1.email.value,"","height=150,width=200");
}


//-->
</SCRIPT>
</HEAD>

<body<?=(substr($_data->layoutdata["MOUSEKEY"],0,1)=="Y"?" oncontextmenu=\"return false;\"":"")?><?=(substr($_data->layoutdata["MOUSEKEY"],1,1)=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=(substr($_data->layoutdata["MOUSEKEY"],2,1)=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<? include ($Dir.MainDir.$_data->menu_type.".php") ?>
<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
<input type=hidden name=type value="">
<input type=hidden name="idChk" value="0">
<input type=hidden name="mailChk" value="0">
<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["MJOIN"]=="Y") {?>
<input type=hidden name=shopurl value="<?=getenv("HTTP_HOST")?>">
<?}?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?
$leftmenu="Y";
if($_data->design_mbjoin=="U") {
	$sql="SELECT body,leftmenu FROM ".$designnewpageTables." WHERE type='mbjoin'";
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
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/memberjoin_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/memberjoin_title.gif\" border=\"0\" alt=\"회원가입\"></td>\n";
	} else {
		echo "<td>\n";
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC=".$Dir."images/".$_data->icon_type."/memberjoin_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background=".$Dir."images/".$_data->icon_type."/memberjoin_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC=".$Dir."images/".$_data->icon_type."/memberjoin_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		echo "</td>\n";
	}
	echo "</tr>\n";
}

echo "<tr>\n";
echo "	<td>\n";
include ($Dir.TempletDir."mbjoin/mbjoin".$_data->design_mbjoin.".php");
echo "	</td>\n";
echo "</tr>\n";
?>

</table>
</form>
<?=$onload?>

<? include ($Dir."lib/bottom.php") ?>

</BODY>
</HTML>