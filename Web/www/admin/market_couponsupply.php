<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-3";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$sql = "SELECT id, authkey, return_tel FROM tblsmsinfo ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)){
	$return_tel = explode("-",$row->return_tel);
	$sms_id=$row->id;
	$sms_authkey=$row->authkey;
}
mysql_free_result($result);

$isdisabled="1";
$maxcount=0;
if(strlen($sms_id)==0 || strlen($sms_authkey)==0) {
	//$onload="<script>alert('SMS 회원가입 및 충전 후 SMS 기본환경 설정에서\\n\\nSMS 아이디 및 인증키를 입력하시기 바랍니다.');</script>";
	$isdisabled="0";
} else {
	$smscountdata=getSmscount($sms_id, $sms_authkey);
	if(substr($smscountdata,0,2)=="OK") {
		$maxcount=substr($smscountdata,3);
	} else if(substr($smscountdata,0,2)=="NO") {
		//$onload="<script>alert('SMS 회원 아이디가 존재하지 않습니다.\\n\\nSMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하시기 바랍니다.');</script>";
		$isdisabled="2";
	} else if(substr($smscountdata,0,2)=="AK") {
		//$onload="<script>alert('SMS 회원 인증키가 일치하지 않습니다.\\n\\nSMS 기본환경 설정에서 인증키를 정확히 입력하시기 바랍니다.');</script>";
		$isdisabled="3";
	} else {
		//$onload="<script>alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.');</script>";
		$isdisabled="4";
	}
}

$type=$_POST["type"];
$coupon_code=$_REQUEST["coupon_code"];
$userlist=$_POST["userlist"];
$gubun=$_REQUEST["gubun"];
$group_code=$_POST["group_code"];
$smscheck=$_POST["smscheck"];
$msg=$_POST["msg"];
$from_tel1=$_POST["from_tel1"];
$from_tel2=$_POST["from_tel2"];
$from_tel3=$_POST["from_tel3"];
$clicknum=$_POST["clicknum"];

$memberID=$_REQUEST["memberID"];

$popup=$_REQUEST["popup"];


if($type=="result") {
	if($gubun=="ALL" || $gubun=="GROUP") {
		if($gubun=="GROUP") {
			$member=substr($group_code,0,4);
		} else {
			$member="ALL";
		}
		$sql = "UPDATE tblcouponinfo SET member = '".$member."', display = 'Y' ";
		$sql.= "WHERE coupon_code = '".$coupon_code."' AND vender = '0' ";
		mysql_query($sql,get_db_conn());

		$log_content = "## 쿠폰발급 ## - 쿠폰코드 : $coupon_code 쿠폰 발급 : $member $gubun";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

		if($smscheck=="Y" && $maxcount>0) {
			unset($tel_list);
			unset($name_list);
			$fromtel=$from_tel1."-".$from_tel2."-".$from_tel3;
			$cnt=0;
			if($member=="ALL") {
				$sql = "SELECT mobile,name FROM tblmember WHERE (news_yn='Y' OR news_yn='S') AND mobile !='' ";
			} else {
				$sql = "SELECT mobile, name FROM tblmember WHERE group_code='".$member."' AND (news_yn='Y' OR news_yn='S') AND mobile != '' ";
			}
			$result=mysql_query($sql,get_db_conn());
			while($row = mysql_fetch_object($result)){
				$row->mobile=ereg_replace(",","",$row->mobile);
				$row->mobile=ereg_replace("-","",$row->mobile);
				if(strlen($row->mobile)<10 || strlen($row->mobile)>11){
				} else {
					$tel_list.=",".$row->mobile;
					$name_list.=",".ereg_replace(",","",$row->name);
					$cnt++;
				}
			}
			mysql_free_result($result);
			$tel_list=substr($tel_list,1);
			$name_list=substr($name_list,1);
			if($cnt <=$maxcount && $cnt>0) {
				$etcmsg="쿠폰발급 SMS 메세지 전송";
				$temp=SendSMS($sms_id, $sms_authkey, $tel_list, $name_list, $fromtel, 0, $msg, $etcmsg);
				$resmsg=explode("[SMS]",$temp);
				$mess = "\\n".$resmsg[1];
			} else if($cnt==0) {
				$mess="\\nSMS를 발송할 회원이 없습니다.";
			} else {
				$mess="\\nSMS 머니가 부족합니다. 충전후 이용하시기 바랍니다.";
			}
		} else if($smscheck=="Y") {
			$mess="\\nSMS 머니가 부족합니다. 충전후 이용하시기 바랍니다.";
		}
		echo "<script>alert('해당 쿠폰이 발급되었습니다.\\n로그인시 해당 회원에게 자동 발급됩니다.".$mess."');location='market_couponlist.php';</script>";
		exit;
	} else if($gubun=="MEMBER" || $gubun=="BIRTH") {

		if($gubun=="BIRTH") {
			$sql = "SELECT id FROM tblmember WHERE MID(resno,3,4) = '".date("md")."' ";
			$result=mysql_query($sql,get_db_conn());
			while($row = mysql_fetch_object($result)) {
				$userlist.=",".ereg_replace(",","",$row->id);
			}
			mysql_free_result($result);
		}
		$sql = "SELECT id FROM tblcouponissue WHERE coupon_code = '".$coupon_code."' ";
		$result = mysql_query($sql,get_db_conn());
		$i=0;

		while($row = mysql_fetch_object($result)) {

			$patten[$i]="(,".$row->id.",)";
			$replace[$i]=",";
			$i++;
		}
		
		mysql_free_result($result);
		if($i>0) $userlist = preg_replace($patten,$replace,$userlist.",");
		$userlist.=",";
		
		
		$aruser = explode(",",$userlist);
		$cnt = count($aruser)-1;

		
		
		
		if($cnt>=1) {
			$date = date("YmdHis");
			$sql = "SELECT date_start,date_end FROM tblcouponinfo ";
			$sql.= "WHERE coupon_code = '".$coupon_code."' AND vender='0' AND member = '' ";
			$result = mysql_query($sql,get_db_conn());
			if($row = mysql_fetch_object($result)){
				if($row->date_start>0) {
					$date_start=$row->date_start;
					$date_end=$row->date_end;
				} else {
					$date_start = substr($date,0,10);
					$date_end = date("Ymd",mktime(0,0,0,substr($date,4,2),substr($date,6,2)+abs($row->date_start),substr($date,0,4)))."23";
				}
				$sql = "INSERT INTO tblcouponissue (coupon_code,id,date_start,date_end,date) VALUES ";
				
				for($i=1;$i<$cnt;$i++){
					$sql.=" ('".$coupon_code."','".addslashes($aruser[$i])."','".$date_start."','".$date_end."','".$date."'),";
				}

				$sql=substr($sql,0,-1);
				mysql_query($sql,get_db_conn());

				if(!mysql_errno()) {
					$cnt--;
					$sql = "UPDATE tblcouponinfo SET display = 'Y', issue_no = issue_no+$cnt ";
					$sql.= "WHERE coupon_code = '".$coupon_code."'";
					mysql_query($sql,get_db_conn());
					if($smscheck=="Y" && $maxcount>0) {
						unset($tel_list);
						unset($name_list);
						$fromtel=$from_tel1."-".$from_tel2."-".$from_tel3;

						$userlist = substr($userlist,1,-1);
						$userlist = ereg_replace(",","','",$userlist);
						$sql = "SELECT mobile, name FROM tblmember WHERE id IN ('".$userlist."') AND mobile != '' ";
						$result=mysql_query($sql,get_db_conn());
						$cnt=0;
						while($row = mysql_fetch_object($result)){
							$row->mobile=ereg_replace(",","",$row->mobile);
							$row->mobile=ereg_replace("-","",$row->mobile);
							if(strlen($row->mobile)<10 || strlen($row->mobile)>11){
							} else {
								$tel_list.=",".$row->mobile;
								$name_list.=",".ereg_replace(",","",$row->name);
								$cnt++;
							}
						}
						$tel_list=substr($tel_list,1);
						$name_list=substr($name_list,1);
						if($cnt <=$maxcount && $cnt>0) {
							$etcmsg="쿠폰발급 SMS 메세지 전송";
							$temp=SendSMS($sms_id, $sms_authkey, $tel_list, $name_list, $fromtel, 0, $msg, $etcmsg);
							$resmsg=explode("[SMS]",$temp);
							$mess = "\\n".$resmsg[1].$sql;
						} else if($cnt==0) {
							$mess="\\nSMS를 발송할 회원이 없습니다.";
						} else {
							$mess="\\nSMS 머니가 부족합니다. 충전후 이용하시기 바랍니다.";
						}
					} else if($smscheck=="Y") {
						$mess="\\nSMS 머니가 부족합니다. 충전후 이용하시기 바랍니다.";
					}
					if( $popup == "OK" AND $gubun == "MEMBER" ) {
						$popClose = "self.close();";
					}
					echo "<script>alert('해당 쿠폰이 발급되었습니다.".$mess."');".$popClose."</script>";
				}
			} else {
				echo "<script>alert('쿠폰코드가 잘못되었습니다.');</script>";
			}
		} else {
			echo "<script>alert('쿠폰 발급할 회원이 없습니다.');</script>";
		}
	}
}
?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(form) {
	if(form.coupon_code.value.length==0) {
		alert("발급할 쿠폰 선택을 하셔야 합니다.");
		return;
	}
	if(form.gubun[1].checked==true && form.group_code.selectedIndex<=0) {
		alert("쿠폰 발급할 등급을 선택하시기 바랍니다.");
		form.group_code.focus();
		return;
	} else if (form.gubun[2].checked==true && form.alluser.options.length<=0) {
		alert("쿠폰 발급할 개별회원 추가를 하시기 바랍니다.");
		return;
	}

	if(form.gubun[2].checked==true) {
		form2.userlist.value="";
		for(i=2;i<form.alluser.options.length;i++) {			
			form.userlist.value+=","+form.alluser.options[i].value;
		}
	}

	if(form.smscheck.checked==true) {
		if(form.msg.value.length==0) {
			alert("전송할 메세지를 입력하세요.");
			form.msg.focus();
			return;
		}
		for(i=1;i<=3;i++) {
			if(form["from_tel"+i].value.length==0) {
				alert("보내는 사람 전화번호를 입력하세요.");
				form["from_tel"+i].focus();
				return;
			}
			if(!IsNumeric(form["from_tel"+i].value)) {
				alert("숫자만 입력하세요.");
				form["from_tel"+i].focus();
				break; return;
			}
		}
		from_tel=form.from_tel1.value+form.from_tel2.value+form.from_tel3.value;
		if(from_tel.length<8) {
			alert("보내는 사람 전화번호 입력이 잘못되었습니다.");
			form.from_tel1.focus();
			return;
		}
		if(form.gubun[1].checked==true) {
			if(<?=$maxcount?><form.group_mem.value){
				alert("SMS 머니가 부족합니다. 충전후 이용하시기 바랍니다.");
				return;
			}
		}
	} else if (!confirm("쿠폰발급과 동시에 SMS 동시발송 선택이 안되었습니다.\n\n쿠폰만 발급하시겠습니까?")) {
		form.smscheck.focus();
		return;
	}
	form.type.value="result";
	form.submit();
}

function ChoiceCoupon(code) {
	document.form1.type.value="choice";
	document.form1.coupon_code.value=code;
	document.form1.submit();
}

function CouponView(code) {
	window.open("about:blank","couponview","width=650,height=650,scrollbars=no");
	document.cform.coupon_code.value=code;
	document.cform.submit();
}

function DefaultFrom(checked) {
	if(checked==true) {
		document.form2.from_tel1.value="<?=$return_tel[0]?>";
		document.form2.from_tel2.value="<?=$return_tel[1]?>";
		document.form2.from_tel3.value="<?=$return_tel[2]?>";
	} else {
		document.form2.from_tel1.value="";
		document.form2.from_tel2.value="";
		document.form2.from_tel3.value="";
	}
}

function DefaultFromAdd() {
	if(document.form2.clicknum.checked==true) {
		document.form2.clicknum.checked=false;
		document.form2.from_tel1.value="";
		document.form2.from_tel2.value="";
		document.form2.from_tel3.value="";
	} else {
		document.form2.clicknum.checked=true;
		document.form2.from_tel1.value="<?=$return_tel[0]?>";
		document.form2.from_tel2.value="<?=$return_tel[1]?>";
		document.form2.from_tel3.value="<?=$return_tel[2]?>";
	}
}

function ChangeGroupCode() {
	val=document.form2.group_code.options[document.form2.group_code.selectedIndex].value;
	if(val!="") {
		document.form2.type.value="changegroup";
		document.form2.submit();
	}
}

function ChangeType(val) {
	if(val.length==0 || val=="ALL" || val=="BIRTH") {
		document.form2.group_code.disabled=true;
		document.form2.group_mem.disabled=true;
		document.form2.id.disabled=true;
		document.form2.search_mem.disabled=true;
		document.form2.search_mem.src="images/btn_memberidr.gif";
		document.form2.mem_add.disabled=true;
		document.form2.mem_add.src="images/btn_addr.gif";
		document.form2.mem_del.disabled=true;
		document.form2.mem_del.src="images/btn_del6r.gif";
		document.form2.alluser.disabled=true;
	} else if (val=="GROUP") {
		document.form2.id.disabled=true;
		document.form2.search_mem.disabled=true;
		document.form2.search_mem.src="images/btn_memberidr.gif";
		document.form2.mem_add.disabled=true;
		document.form2.mem_add.src="images/btn_addr.gif";
		document.form2.mem_del.disabled=true;
		document.form2.mem_del.src="images/btn_del6r.gif";
		document.form2.alluser.disabled=true;

		document.form2.group_code.disabled=false;
		document.form2.group_mem.disabled=false;
	} else if (val=="MEMBER") {
		document.form2.id.disabled=false;
		document.form2.search_mem.disabled=false;
		document.form2.search_mem.src="images/btn_memberid.gif";
		document.form2.mem_add.disabled=false;
		document.form2.mem_add.src="images/btn_add.gif";
		document.form2.mem_del.disabled=false;
		document.form2.mem_del.src="images/btn_del6.gif";
		document.form2.alluser.disabled=false;

		document.form2.group_code.disabled=true;
		document.form2.group_mem.disabled=true;
	}
}

function FindMember() {
	 document.form2.gubun[2].checked=true;
	 if(document.form2.coupon_code.value.length==0){
		alert('발급을 원하는 쿠폰을 먼저 선택하세요');
		return;
	 }
	 window.open("about:blank","findmember","width=250,height=150,scrollbars=yes");
	 document.mform.submit();
}

function addChar(aspchar) {
	document.form2.msg.value += aspchar;
	cal_pre2();
}

function cal_pre2() {
	obj_msg = document.form2.msg;
	obj_len = document.form2.len_msg;

	strcnt = cal_byte2(obj_msg.value);

	if(strcnt > 80)	{
		reserve = strcnt - 80;
		alert('메시지 내용은 80바이트를 넘을수 없습니다.\n\n작성하신 메세지 내용은 '+ reserve +'byte가 초과되었습니다.\n\n초과된 부분은 자동으로 삭제됩니다.');
		obj_msg.value = nets_check2(obj_msg.value);
		strcnt = cal_byte2(obj_msg.value);
		obj_len.value=strcnt;
		return;
	}
	obj_len.value=strcnt;
}

function cal_byte2(aquery) {
	var tmpStr;
	var temp = 0;
	var onechar;
	var tcount = 0;
	var reserve = 0;

	tmpStr = new String(aquery);
	temp = tmpStr.length;

	for(k=0; k<temp; k++) {
		onechar = tmpStr.charAt(k);
		if(escape(onechar).length > 4) {
			tcount += 2;
		} else {
			tcount ++;
		}
	}
	return tcount;
}

function nets_check2(aquery) {
	var temStr;
	var temp = 0;
	var onechar;
	var tcount;
	tcount = 0;

	tmpStr = new String(aquery);
	temp = tmpStr.length;

	for(k=0;k<temp;k++)	{
		onechar = tmpStr.charAt(k);

		if(escape(onechar).length > 4) {
			tcount += 2;
		} else {
			tcount++;
		}

		if(tcount > 80) {
			tmpStr = tmpStr.substring(0,k);
			break;
		}
	}
	return tmpStr;
}

function ToAdd() {
	id=document.form2.id.value;
	if(id.length==0) {
		alert("회원ID를 선택하시기 바랍니다.");
		FindMember();
		return;
	}
	alluser=document.form2.alluser;
	for(i=1;i<alluser.options.length;i++) {
		if(id==alluser.options[i].value) {
			alert("이미 추가된 ID입니다.\n\n다시 확인하시기 바랍니다.");
			document.form2.id.value="";
			return;
		}
	}

	new_option = document.createElement("OPTION");
	new_option.text=id;
	new_option.value=id;
	alluser.add(new_option);
	cnt=alluser.options.length - 1;
	alluser.options[0].text = "----------------------------- 회원 개별발급 목록("+cnt+") --------------------------------";
	document.form2.id.value="";
}

function ToDelete() {
	alluser=document.form2.alluser;
	for(i=1;i<alluser.options.length;i++) {
		if(alluser.options[i].selected==true){
			alluser.options[i]=null;
			cnt=alluser.options.length - 1;
			alluser.options[0].text = "----------------------------- 회원 개별발급 목록("+cnt+") --------------------------------";
			return;
		}
	}
	alert("삭제할 ID를 선택하세요.");
	alluser.focus();
}

</script>

<style type="text/css">
<!--
TEXTAREA {  clip:   rect(   ); overflow: hidden; background-image:url('');font-family:굴림;}
.phone {  font-family:굴림; height: 80px; width: 173px;color: #191919;  FONT-SIZE: 9pt; font-style: normal; background-color: #A8E4ED;; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px}
-->
</style>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">

		<?
			if( $popup != "OK" ) {
		?>
		<col width=198></col>
		<col width=10></col>
		<?
			}
		?>
		<col width=></col>
		<tr>

		<?
			if( $popup != "OK" ) {
		?>
			<td valign="top"  background="images/leftmenu_bg.gif">
			<?
				include ("menu_market.php");
			?>
			</td>

			<td></td>
		<?
			}
		?>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">

		<?
			if( $popup != "OK" ) {
		?>
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 마케팅지원 &gt; 쿠폰발행 서비스 설정 &gt; <span class="2depth_select">생성된 쿠폰 즉시발급</span></td>
			</tr>
			</table>
		</td>
	</tr>
		<?
			}
		?>
	<tr>
        <td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_01_bg.gif"></td>
        <td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr>
        <td width="16" background="images/con_t_04_bg1.gif"></td>
        <td bgcolor="#ffffff" style="padding:10px">








			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_couponsupply_title.gif" ALT=""></TD>
					</tr><tr>
<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">생성된 쿠폰은 (전체 회원 발급, 회원 등급별 발급, 회원 개별 발급) 구분해서 발급 할 수 있습니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_couponsy_stitle1.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=30></col>
				<col width=70></col>
				<col width=></col>
				<col width=70></col>
				<col width=80></col>
				<col width=100></col>
				<col width=70></col>
				<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
				<input type=hidden name=type>
				<input type=hidden name=coupon_code value="<?=$coupon_code?>">
				<input type=hidden name=popup value="<?=$popup?>">
				<input type=hidden name=memberID value="<?=$memberID?>">
				<input type=hidden name=gubun value="<?=$gubun?>">
				<TR>
					<TD background="images/table_top_line.gif" colspan="7"></TD>
				</TR>
				<TR>
					<TD class="table_cell" align="center">선택</TD>
					<TD class="table_cell1" align="center">쿠폰코드</TD>
					<TD class="table_cell1" align="center">쿠폰명</TD>
					<TD class="table_cell1" align="center">생성일</TD>
					<TD class="table_cell1" align="center">할인/적립</TD>
					<TD class="table_cell1" align="center">유효기간</TD>
					<TD class="table_cell1" align="center">쿠폰종류</TD>
				</TR>
				<TR>
					<TD colspan="7" background="images/table_con_line.gif"></TD>
				</TR>
<?
				$sql = "SELECT * FROM tblcouponinfo WHERE vender='0' ";
				$sql.= "AND issue_type = 'N' AND member = '' ";
				$result = mysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=mysql_fetch_object($result)) {
					$cnt++;
					if($row->sale_type<=2) $dan="%";
					else $dan="원";
					if($row->sale_type%2==0) $sale = "할인";
					else $sale = "적립";
					if($row->date_start>0) {
						$date = substr($row->date_start,2,2).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)." ~ ".substr($row->date_end,2,2).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2);
					} else {
						$date = abs($row->date_start)."일동안";
					}
					if($coupon_code==$row->coupon_code){
						$ment = "[NAME]님께 ".number_format($row->sale_money).$dan." ".$sale."쿠폰이 발급되었습니다! ".$shopurl;
					}
					echo "<TR>\n";
					echo "	<TD class=\"td_con2\" align=\"center\"><input type=checkbox name=ckbox ".($coupon_code==$row->coupon_code?"checked":"")." onclick=\"ChoiceCoupon('".$row->coupon_code."')\"></TD>\n";
					echo "	<TD class=\"td_con1\" align=\"center\"><A HREF=\"javascript:CouponView('".$row->coupon_code."');\"><B>".$row->coupon_code."</B></A></TD>\n";
					echo "	<TD class=\"td_con1\"><nobr>".$row->coupon_name."</TD>\n";
					echo "	<TD class=\"td_con1\" align=\"center\">".substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."</TD>\n";
					echo "	<TD class=\"td_con1\" align=\"center\"><span class=\"".($sale=="할인"?"font_orange":"font_blue")."\"><b>".number_format($row->sale_money).$dan." ".$sale."</b></span></TD>\n";
					echo "	<TD class=\"td_con1\" align=\"center\">".$date."</TD>\n";
					echo "	<TD class=\"td_con1\" align=\"center\"><img src=\"images/".($sale=="할인"?"icon_cupon1.gif":"icon_cupon2.gif")."\" width=\"61\" height=\"16\" border=\"0\"></TD>\n";
					echo "</TR>\n";
					echo "<TR>\n";
					echo "	<TD colspan=\"7\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</TR>\n";
				}
				mysql_free_result($result);
				if($cnt==0) {
					echo "<tr><td class=td_con2 colspan=7 align=center>발급된 쿠폰이 없습니다. 쿠폰을 생성하신 후 발급하시기 바랍니다.</td></tr>\n";
				}
?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="7"></TD>
				</TR>
				</form>
				</TABLE>

					<?
						// 팝업에서 쿠폰 바로 생성 버튼
						if( $popup=="OK" ) {
							//_pr($_REQUEST);
					?>
					<div style="text-align:center; margin:10px 0px;"><a href="market_couponnew.php?popup=<?=$popup?>&memberID=<?=$memberID?>&gubun=<?=$gubun?>"><img src="images/btn_cupon_make2.gif" alt="신규쿠폰 생성" /></a></div>
					<? } ?>
				</td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_couponsy_stitle2.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
				<input type=hidden name=type>
				<input type=hidden name=coupon_code value="<?=$coupon_code?>">
				<input type=hidden name=userlist>
				<input type=hidden name=popup value="<?=$popup?>">
				<TR>
					<TD background="images/table_top_line.gif" colspan=2></TD>
				</TR>
				<TR>
					<TD class="table_cell"><INPUT id=idx_gubun1 onclick="ChangeType(this.value) ;" type=radio value=ALL name=gubun <?=($gubun=="ALL"?"checked":"")?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_gubun1><B>전체 회원 발급</B></LABEL></TD>
					<TD class="td_con1">&nbsp;</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><INPUT id=idx_gubun2 onclick="ChangeType(this.value) ;" type=radio value=GROUP name=gubun <?=($gubun=="GROUP"?"checked":"")?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_gubun2><B>회원 등급별 발급</B></TD>
					<TD class="td_con1">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="50%">
						<SELECT style="WIDTH: 200px" <?=$group_disabled?> onchange=ChangeGroupCode(); name=group_code class="select">
<?
						if($gubun=="GROUP" && strlen($group_code)>0) {
							$sql = "SELECT COUNT(*) as cnt FROM tblmember ";
							$sql.= "WHERE group_code = '".$group_code."' GROUP BY group_code ";
							$result=mysql_query($sql,get_db_conn());
							$row=mysql_fetch_object($result);
							$groupcnt=$row->cnt;
							mysql_free_result($result);
						}

						$sql = "SELECT group_code, group_name FROM tblmembergroup ";
						$result = mysql_query($sql,get_db_conn());
						$count=0;
						while ($row=mysql_fetch_object($result)) {
							if($count==0) echo "<option value=\"\">해당 등급을 선택하세요.</option>\n";
							if(strlen($arcnt[$row->group_code])==0) $arcnt[$row->group_code]=0;
							echo "<option value=\"".$row->group_code."\" ";
							if($group_code==$row->group_code) echo " selected ";
							echo ">".$row->group_name."</option>\n";
							$count++;
						}
						mysql_free_result($result);
						if($count==0) echo "<option value=\"\">회원등급이 없습니다.</option>\n";
?>
						</SELECT>
						<INPUT style="PADDING-RIGHT: 5px; TEXT-ALIGN: right"  onfocus=this.blur(); size=6 name=group_mem value="<?=(int)$groupcnt?>" class="input">명
						</td>
						<td width="50%"><a href="javascript:parent.topframe.GoMenu('3','member_groupnew.php');"><img src="images/btn_group.gif" width="120" height="25" border="0"></a></td>
					</tr>
					</table>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" valign="top"><INPUT id=idx_gubun3 onclick="ChangeType(this.value) ;" type=radio value=MEMBER name=gubun <?=($gubun=="MEMBER"?"checked":"")?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_gubun3><B>회원 개별 발급</B></TD>
					<TD class="td_con1">
					<table cellpadding="0" cellspacing="0" border=0 width="100%">
					<tr>
						<td>회원ID: </td>
						<td width=200><INPUT style="WIDTH:155px" onfocus=blur() onclick=FindMember() name=id class="input" size="20" value=""></td>
						<td width="30"><a href="javascript:FindMember();"><img id=search_mem src="images/btn_memberid.gif" width="88" height="25" border="0" hspace="1"></a></td>
						<td width="30"><a href="javascript:ToAdd();"><img id=mem_add src="images/btn_add.gif" width="59" height="25" border="0"></a></td>
						<td><a href="javascript:ToDelete();"><img id=mem_del src="images/btn_del6.gif" width="59" height="25" border="0"></a></td>
					</tr>
					</table>
					<SELECT style="WIDTH: 100%" size=12 name=alluser class="select">
					<OPTION style="BACKGROUND-COLOR: #ffff00" value="">----------------------------- 회원 개별발급 목록(0) --------------------------------</OPTION>
					<OPTION value="<?=$memberID?>"><?=$memberID?></OPTION>
					</SELECT>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><INPUT id=idx_gubun4 onclick="ChangeType(this.value) ;" type=radio value=BIRTH name=gubun <?=($gubun=="BIRTH"?"checked":"")?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_gubun4><B>오늘 생일회원 발급</B></LABEL></TD>
					<TD class="td_con1"><span class="font_blue">(자동으로 검색해서 발급합니다.)</span></TD>
				</TR>
				</TABLE>
				<script>ChangeType('<?=$gubun?>');</script>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td  bgcolor="#ededed" style="padding:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD  align=center height="35" background="images/blueline_bg.gif" class="font_blue"><INPUT id=idx_smscheck type=checkbox value=Y <?if($isdisabled=="1" && $smscheck=="Y")echo "checked";?> name=smscheck <?if($isdisabled!="1")echo"disabled";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none;color:333333" onmouseout="style.textDecoration='none'" for=idx_smscheck><B>SMS 동시 발송</B></LABEL></TD>
						</TR>
						<TR>
							<TD background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD  style="padding:10pt;">
							<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="236">
								<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
								<TR>
									<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
								</TR>
								<TR>
									<TD height="90" background="images/sms_bg.gif" align=center valign="top">
										<TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_join',true);" name=msg rows=5 cols="26" onchange="cal_pre2('mem_join',true);" <?if($isdisabled!="1") echo "disabled";?>></TEXTAREA>
									</TD>
								</TR>
								<TR>
									<TD height="26" align=center background="images/sms_down_01.gif"><INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_msg size="3" class="input_hide">bytes (최대80 bytes)<SCRIPT>cal_pre2('mem_join',false);</SCRIPT></TD>
								</TR>
								</TABLE>
								</td>
								<td width="522" valign="top">
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="524"><b><span style="letter-spacing:-0.5pt;"><img src="images/icon_9.gif" width="13" height="9" border="0">보내는 사람</span></b></td>
								</tr>
								<tr>
									<td width="524" height="26">
									<span style="letter-spacing:-0.5pt;"><INPUT onkeyup="return strnumkeyup(this);" maxLength=3 size="7" name=from_tel1 value="<?=$from_tel1?>" class="input">
									-
									<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size="7" name=from_tel2 value="<?=$from_tel2?>" class="input">
									-
									<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size="7" name=from_tel3 value="<?=$from_tel3?>" class="input"><INPUT id=idx_clicknum onclick=DefaultFrom(this.checked) type=checkbox value=Y name=clicknum></span><b><span class="font_blue" style="letter-spacing:-0.5pt;"><a href="javascript:DefaultFromAdd();"><img src="images/btn_sms2.gif" width="37" height="16" border="0"></a><A HREF="market_smsfill.php"><img src="images/btn_sms1.gif" width="85" height="16" border="0" hspace="1"></a></span></b></td>
								</tr>
								<tr>
									<td width="524"></td>
								</tr>
								<tr>
									<td width="524" class="font_orange"><span style="letter-spacing:-0.5pt;">*</LABEL>SMS전송은 <b>SMS를 먼저 충전하신 후 이용이 가능</b>합니다.<br>*발송 메세지의 내용은 수정 가능합니다.</span></td>
								</tr>
								</table>
								</td>
							</tr>
							</table>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm(document.form2);"><img src="images/btn_cupon_make.gif" width="139" height="38" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height="25">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;"
 class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="701"><span class="font_dotline">생성된 쿠폰 즉시발급</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 전체회원, 등급별 회원에게 한번 발급된 쿠폰은 재발급이 안됩니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 회원 개별 발급시 회원 검색 후 반드시 추가버튼을 눌러주세요.</td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
			</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			<form name=mform action="member_find.php" method=post target=findmember>
			<input type=hidden name=formname value="form2">
			</form>

			<form name=cform action="coupon_view.php" method=post target=couponview>
			<input type=hidden name=coupon_code>
			</form>
			</table>
</td>
        <td width="16" background="images/con_t_02_bg.gif"></td>
    </tr>
    <tr>
        <td width="16"><img src="images/con_t_04.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_04_bg.gif"></td>
        <td width="16"><img src="images/con_t_03.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr><td height="20"></td></tr>
</table>

			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>
<? if(strlen($coupon_code)>0){ echo "<script>addChar('".$ment."')</script>"; }?>
<?
if($_shopdata->adult_type!="N"){
	echo "<script>document.form2.gubun[0].disabled=true;document.form2.gubun[1].disabled=true;document.form2.gubun[2].checked=true;</script>";
}
?>


<?
	if( $popup != "OK" ) {
		INCLUDE "copyright.php";
	}
?>