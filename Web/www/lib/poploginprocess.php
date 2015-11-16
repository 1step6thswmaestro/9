<?
//로그인,로그아웃,회원탈퇴 총괄
if(substr(getenv("SCRIPT_NAME"),-17)=="/loginprocess.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}


$type=$_REQUEST["type"];	//login,logout,exit
$id=$_POST["id"];
$passwd=$_POST["passwd"];

$ssltype=$_POST["ssltype"];
$sessid=$_POST["sessid"];
$nexturl=$_POST["nexturl"];

$history="-1";
$ssllogintype="";
if($ssltype=="ssl" && strlen($id)>0 && strlen($sessid)==32) {
	$ssllogintype="ssl";
	$history="-2";
}


//로그인을 안한상태에서 로그아웃 또는 회원탈퇴 시도시에....
if(strlen($_ShopInfo->getMemid())==0 && ($type=="logout" || $type=="exit")) {
	echo "<html><head><title></title></head><body onload=\"location.href='".$Dir.FrontDir."login.php'\"></body></html>";exit;
}


if($type=="exit") {
	if($_data->memberout_type=="N") {
		echo "<html><head><title></title></head><body onload=\"alert('회원탈퇴를 하실 수 없습니다.\\n\\n쇼핑몰 운영자에게 문의하시기 바랍니다.');history.go(-1)\"></body></html>";exit;
	}
	$sql = "SELECT name,email,mobile FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
}

if($type=="exit") {
	$result = mysql_query($sql,get_db_conn());
	if ($row=mysql_fetch_object($result)) {
		if($row->member_out=="Y") {
			echo "<html><head><title></title></head><body onload=\"alert('회원 아이디가 존재하지 않습니다.');history.go(-1)\"></body></html>";exit;
		}
		$exitname=$row->name;
		$exitemail=$row->email;
		$exitmobile=$row->mobile;
	} else {
		echo "<html><head><title></title></head><body onload=\"alert('회원 아이디가 존재하지 않습니다.');history.go(-1)\"></body></html>";exit;
	}
	mysql_free_result($result);
}

if ($type=="logout" || $type=="exit") { 
	if($type=="exit") {
		$state="N";
		if($_data->memberout_type=="O") {
			$sql = "SELECT COUNT(*) as cnt FROM tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
			$result= mysql_query($sql,get_db_conn());
			$row = mysql_fetch_object($result);
			if($row->cnt==0) {
				$sql ="DELETE FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
				$state="Y";
			} else {
				$sql = "UPDATE tblmember SET ";
				$sql.= "passwd			= '', ";
				$sql.= "resno			= '', ";
				$sql.= "email			= '', ";
				$sql.= "mobile			= '', ";
				$sql.= "news_yn			= 'N', ";
				$sql.= "age				= '', ";
				$sql.= "gender			= '', ";
				$sql.= "job				= '', ";
				$sql.= "birth			= '', ";
				$sql.= "home_post		= '', ";
				$sql.= "home_addr		= '', ";
				$sql.= "home_tel		= '', ";
				$sql.= "office_post		= '', ";
				$sql.= "office_addr		= '', ";
				$sql.= "office_tel		= '', ";
				$sql.= "memo			= '', ";
				$sql.= "reserve			= 0, ";
				$sql.= "joinip			= '', ";
				$sql.= "ip				= '', ";
				$sql.= "authidkey		= '', ";
				$sql.= "group_code		= '', ";
				$sql.= "member_out		= 'Y', ";
				$sql.= "etcdata			= '' ";
				$sql.= "WHERE id = '".$_ShopInfo->getMemid()."'";
				$state="V";
			}
			mysql_free_result($result);
			mysql_query($sql,get_db_conn());

			$sql = "DELETE FROM tblreserve WHERE id='".$_ShopInfo->getMemid()."'";
			mysql_query($sql,get_db_conn());
			$sql = "DELETE FROM tblcouponissue WHERE id='".$_ShopInfo->getMemid()."'";
			mysql_query($sql,get_db_conn());
			$sql = "DELETE FROM tblmemo WHERE id='".$_ShopInfo->getMemid()."'";
			mysql_query($sql,get_db_conn());
			$sql = "DELETE FROM tblrecommendmanager WHERE rec_id='".$_ShopInfo->getMemid()."'";
			mysql_query($sql,get_db_conn());
			$sql = "DELETE FROM tblrecomendlist WHERE id='".$_ShopInfo->getMemid()."'";
			mysql_query($sql,get_db_conn());
			$sql = "DELETE FROM tblpersonal WHERE id='".$_ShopInfo->getMemid()."'";
			mysql_query($sql,get_db_conn());

			$text = "<script>alert('해당 ID를 탈퇴처리해 드렸습니다.');</script>";
		} else {
			$text = "<script>alert('쇼핑몰에서 확인후 처리해 드립니다.');</script>";
		}
		$sql = "INSERT tblmemberout SET ";
		$sql.= "id				= '".$_ShopInfo->getMemid()."', ";
		$sql.= "name			= '".$exitname."', ";
		$sql.= "email			= '".$exitemail."', ";
		$sql.= "tel				= '".$exitmobile."', ";
		$sql.= "ip				= '".getenv("REMOTE_ADDR")."', ";
		$sql.= "state			= '".$state."', ";
		$sql.= "date			= '".date("YmdHis")."' ";
		mysql_query($sql,get_db_conn());
	}

	if($type=="logout") {
		$sql = "UPDATE tblmember SET authidkey='logout' WHERE id='".$_ShopInfo->getMemid()."' ";
		mysql_query($sql,get_db_conn());
	}

	$_ShopInfo->SetMemNULL();
	$_ShopInfo->Save();

	if(file_exists($Dir.DataDir."design/intro.htm")) {
		$url=$Dir."index.php";
	} else {
		$url=$Dir;
	}
	if($type=="exit") {
		echo $text;
	}

	if($_data->frame_type=="Y") {
		echo "<script>location.href='".$url."';</script>";
	} else {
		echo "<script>parent.location.href='".$url."';</script>";
	}
	exit;
}

if($ssllogintype!="ssl") {
	unset($passwd_type);
	$sql = "SELECT passwd FROM tblmember WHERE id='".$id."' ";
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
		echo "<html><head><title></title></head><body onload=\"alert('아이디 또는 비밀번호가 틀립니다.');history.go(".$history.");\"></body></html>";exit;
	}
	mysql_free_result($result);
}

$sql = "SELECT * FROM tblmember WHERE id='".$id."' ";
if($ssllogintype=="ssl") {
	$sql.= "AND authidkey='".$sessid."' ";
} else {
	if($passwd_type=="hash") {
		$sql.= "AND passwd='".crypt($passwd, $hashdata)."' ";
	} else if($passwd_type=="password") {
		$sql.= "AND passwd=password('".$passwd."')";
	} else if($passwd_type=="old_password") {
		$sql.= "AND passwd=old_password('".$passwd."')";
	} else if($passwd_type=="md5") {
		$sql.= "AND passwd=md5('".$passwd."')";
	}
}
$result = mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$memid=$row->id;
	$memname=$row->name;
	$mememail=$row->email;
	$memgroup=$row->group_code;
	$memreserve=$row->reserve;

	if($row->member_out=="Y") {	//탈퇴한 회원
		echo "<html><head><title></title></head><body onload=\"alert('아이디 또는 비밀번호가 틀리거나 탈퇴한 회원입니다.');history.go(".$history.")\"></body></html>";exit;
	}
	if ($_data->member_baro=="Y" && $row->confirm_yn=="N") { //관리자인증기능여부 및 회원인증 검사
		echo "<html><head><title></title></head><body onload=\"alert('쇼핑몰 운영자 인증 후 로그인이 가능합니다.\\n\\n전화로 문의바랍니다.\\n\\n".$_data->info_tel."');history.go(".$history.")\"></body></html>";exit;
	}
	if($_data->coupon_ok=="Y") {
		$date = date("YmdHis");
		$sql2 = "SELECT coupon_code FROM tblcouponissue WHERE id='".$memid."'";
		$result2 = mysql_query($sql2,get_db_conn());
		while($row2=mysql_fetch_object($result2)){
		  $coupon_code.="'".$row2->coupon_code."',";
		}
		$coupon_code = substr($coupon_code,0,-1);
		mysql_free_result($result2);

		$sql2 = "SELECT coupon_code, date_start, date_end, vender FROM tblcouponinfo ";
		$sql2.= "WHERE display='Y' AND issue_type='N' ";
		$sql2.= "AND (member='ALL' OR (member='".$memgroup."' AND member!='')) ";
		$sql2.= "AND (date_end>'".substr($date,0,10)."' OR date_end='') ";
		if(strlen($coupon_code)>0) $sql2.="AND coupon_code NOT IN (".$coupon_code.") ";

		$sql="INSERT INTO tblcouponissue (coupon_code,id,date_start,date_end,date) VALUES ";
		$couponcnt ="";
		$count=0;
		$result2 = mysql_query($sql2,get_db_conn());
		while($row2=mysql_fetch_object($result2)) {
			if($row2->date_start>0) {
				$date_start=$row2->date_start;
				$date_end=$row2->date_end;
			} else {
				$date_start = substr($date,0,10);
				$date_end = date("Ymd",mktime(0,0,0,substr($date,4,2),substr($date,6,2)+abs($row2->date_start),substr($date,0,4)))."23";
			}
			if($row2->vender>0) {	//입점업체에서 발급한 쿠폰의 경우에는 단골매장으로 등록한 회원에게만 지급
				$sql3 = "SELECT COUNT(*) as vr_cnt FROM tblregiststore ";
				$sql3.= "WHERE id='".$memid."' AND vender='".$row2->vender."' ";
				$result3=mysql_query($sql3,get_db_conn());
				$row3=mysql_fetch_object($result3);
				$vr_cnt=$row3->vr_cnt;
				mysql_free_result($result3);
				if($vr_cnt<=0) {
					continue;
				}
			}
			$sql.=" ('".$row2->coupon_code."','".$memid."','".$date_start."','".$date_end."','".$date."'),";
			$couponcnt="'".$row2->coupon_code."',";
			$count++;
		}
		mysql_free_result($result2);
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
} else {
	if($ssllogintype!="ssl") {
		echo "<html><head><title></title></head><body onload=\"alert('비밀번호가 틀립니다.');history.go(".$history.")\"></body></html>";exit;
	} else {
		echo "<html><head><title></title></head><body onload=\"history.go(".$history.")\"></body></html>";exit;
	}
}
mysql_free_result($result);

$authidkey = md5(uniqid(""));
$_ShopInfo->setMemid($memid);
$_ShopInfo->setAuthidkey($authidkey);
$_ShopInfo->setMemgroup($memgroup);
$_ShopInfo->setMemname($memname);
$_ShopInfo->setMemreserve($memreserve);
$_ShopInfo->setMememail($mememail);
$_ShopInfo->Save();

$sql = "UPDATE tblmember SET ";
$sql.= "authidkey		= '".$authidkey."', ";
if($passwd_type=="hash" || $passwd_type=="password" || $passwd_type=="old_password") {
	$sql.= "passwd		= '".md5($passwd)."', ";
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
	$sql = "INSERT INTO tblshopcountday (date,count,login_cnt,id_list) VALUES ('".$loginday."',1,1,'".$id_list."')";
	mysql_query($sql,get_db_conn());
}

if(strlen($nexturl)>0) {
	echo "<script>opener.location.reload();location.replace('".$nexturl."');</script>";
	//Header("Location:".$nexturl);
} else {
	echo "<script>opener.location.reload();window.close();</script>";
}
exit;
?>