<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-4";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$shopname=$_shopdata->shopname;

$msg_list = array(
					"mem_join"=>"[".strip_tags($shopname)."] [NAME]님 회원 가입을 축하합니다.",
					"mem_order"=>"[".strip_tags($shopname)."] [NAME]님께서는 [PRODUCT]를 주문하셨습니다.",
					"mem_bank"=>"[NAME]님~ [PRICE]원 [ACCOUNT] 입금바랍니다. [".strip_tags($shopname)."]",
					"mem_bankok"=>"[".strip_tags($shopname)."] [DATE]의 주문이 입금확인 되었습니다. 바로 발송해 드리겠습니다.",
					"mem_delivery"=>"[".strip_tags($shopname)."]에서 [DATE]에 주문한 상품을 발송해 드렸습니다. 감사합니다.",
					"mem_delinum"=>"[".strip_tags($shopname)."] [DELICOM] 송장번호 : [DELINUM] 금일 발송처리 되었습니다.",
					"mem_birth"=>"[".strip_tags($shopname)."] [NAME]님 [DATE]은 태어나신 소중한 날입니다. 생일을 축하드립니다!",
					"mem_auth"=>"[NAME]님의 회원인증 처리가 완료되었습니다. [".strip_tags($shopname)."]",
					"mem_passwd"=>"[".$shopname."]홍길동님 ID : hong27 PW : gilddong입니다.",
					/*"admin_join"=>"홍길동님이 hong27라는 ID로 가입하셨습니다.",
					"admin_order"=>"홍길동님이 [상품1,상품2] 카드(현금) 구입하셨습니다.",
					"admin_cancel"=>"홍길동님께서 2007/01/01에 주문하신 주문을 취소하셨습니다.",
					"admin_board"=>"[테스트 게시판]에 신규글이 [제목]으로 입력되었습니다.",
					"admin_soldout"=>"[상품1]이 [홍길동]님 주문에 의해서 품절되었습니다.",*/
					"mem_gift"=>"[".$shopname."] 인증번호[AUTHCODE] [NAME]님이 상품권을 선물하셨습니다.",
					"socialshopping"=>"[".strip_tags($shopname)."] [PRODUCT_NAME]구매달성실패. 결제취소",
					"product_hongbo"=>"[".strip_tags($shopname)."] [URL]",
					"mem_present"=>"[".strip_tags($shopname)."] [URL] [NAME]님이 선물하셨습니다.",
					"mem_pester"=>"[NAME]님의 조르기: [URL] (상세내용 메일확인)",
					"mem_join_msg"=>"[NAME]님이 [ID] 라는 ID로 가입하셨습니다.",
					"pr_buy_msg"=>"[NAME]님이 [PRODUCT]를 [PAYTYPE] 구입하셨습니다.",
					"pr_cancel_msg"=>"[NAME]님께서 [DATE]에 주문하신 주문을 취소하셨습니다.",
					"pr_soldout_msg"=>"[PRODUCT]이 [NAME]님 주문에 의해서 품절되었습니다.",
					"new_post_msg"=>"[BOARD]에 신규글이 [TITLE]으로 입력되었습니다."
					);

$type=$_POST["type"];
$sms_id=$_POST["sms_id"];
$sms_authkey=$_POST["sms_authkey"];
$sms_uname=$_POST["sms_uname"];
$return_tel1=$_POST["return_tel1"];
$return_tel2=$_POST["return_tel2"];
$return_tel3=$_POST["return_tel3"];
if(strlen($return_tel1)>0 && strlen($return_tel2)>0 && strlen($return_tel3)>0) {
	$return_tel=$return_tel1."-".$return_tel2."-".$return_tel3;
}
$admin_tel1=$_POST["admin_tel1"];
$admin_tel2=$_POST["admin_tel2"];
$admin_tel3=$_POST["admin_tel3"];
if(strlen($admin_tel1)>0 && strlen($admin_tel2)>0 && strlen($admin_tel3)>0) {
	$admin_tel=$admin_tel1."-".$admin_tel2."-".$admin_tel3;
}
$subadmin1_tel1=$_POST["subadmin1_tel1"];
$subadmin1_tel2=$_POST["subadmin1_tel2"];
$subadmin2_tel3=$_POST["subadmin1_tel3"];
if(strlen($subadmin1_tel1)>0 && strlen($subadmin1_tel2)>0 && strlen($subadmin1_tel3)>0) {
	$subadmin1_tel=$subadmin1_tel1."-".$subadmin1_tel2."-".$subadmin1_tel3;
}
$subadmin2_tel1=$_POST["subadmin2_tel1"];
$subadmin2_tel2=$_POST["subadmin2_tel2"];
$subadmin2_tel3=$_POST["subadmin2_tel3"];
if(strlen($subadmin2_tel1)>0 && strlen($subadmin2_tel2)>0 && strlen($subadmin2_tel3)>0) {
	$subadmin2_tel=$subadmin2_tel1."-".$subadmin2_tel2."-".$subadmin2_tel3;
}
$subadmin3_tel1=$_POST["subadmin3_tel1"];
$subadmin3_tel2=$_POST["subadmin3_tel2"];
$subadmin3_tel3=$_POST["subadmin3_tel3"];
if(strlen($subadmin3_tel1)>0 && strlen($subadmin3_tel2)>0 && strlen($subadmin3_tel3)>0) {
	$subadmin3_tel=$subadmin3_tel1."-".$subadmin3_tel2."-".$subadmin3_tel3;
}
$check_sleep_time=$_POST["check_sleep_time"];
$sleep_time1=$_POST["sleep_time1"];
$sleep_time2=$_POST["sleep_time2"];
$mem_join=(strlen($_POST["mem_join"])>0?$_POST["mem_join"]:"N");
$mem_order=(strlen($_POST["mem_order"])>0?$_POST["mem_order"]:"N");
$mem_delivery=(strlen($_POST["mem_delivery"])>0?$_POST["mem_delivery"]:"N");
$mem_delinum=(strlen($_POST["mem_delinum"])>0?$_POST["mem_delinum"]:"N");
$mem_bank=(strlen($_POST["mem_bank"])>0?$_POST["mem_bank"]:"N");
$mem_bankok=(strlen($_POST["mem_bankok"])>0?$_POST["mem_bankok"]:"N");
$mem_bankokvender=(strlen($_POST["mem_bankokvender"])>0?$_POST["mem_bankokvender"]:"N");
$mem_birth=(strlen($_POST["mem_birth"])>0?$_POST["mem_birth"]:"N");
$mem_auth=(strlen($_POST["mem_auth"])>0?$_POST["mem_auth"]:"N");
$mem_passwd=(strlen($_POST["mem_passwd"])>0?$_POST["mem_passwd"]:"N");
$admin_join=(strlen($_POST["admin_join"])>0?$_POST["admin_join"]:"N");
$admin_order=(strlen($_POST["admin_order"])>0?$_POST["admin_order"]:"N");
$vender_order=(strlen($_POST["vender_order"])>0?$_POST["vender_order"]:"N");
$admin_cancel=(strlen($_POST["admin_cancel"])>0?$_POST["admin_cancel"]:"N");
$admin_soldout=(strlen($_POST["admin_soldout"])>0?$_POST["admin_soldout"]:"N");
$admin_board=(strlen($_POST["admin_board"])>0?$_POST["admin_board"]:"N");

$msg_mem_join=$_POST["msg_mem_join"];
$msg_mem_order=$_POST["msg_mem_order"];
$mem_delivery=$_POST["mem_delivery"];
$msg_mem_delinum=$_POST["msg_mem_delinum"];
$msg_mem_bank=$_POST["msg_mem_bank"];
$msg_mem_bankok=$_POST["msg_mem_bankok"];
$msg_mem_birth=$_POST["msg_mem_birth"];
$msg_mem_auth=$_POST["msg_mem_auth"];

//2015-02-02 관리자용 메시지
$mem_join_msg = $_POST['mem_join_msg'];
$pr_buy_msg = $_POST['pr_buy_msg'];
$pr_cancel_msg = $_POST['pr_cancel_msg'];
$pr_soldout_msg = $_POST['pr_soldout_msg'];
$new_post_msg = $_POST['new_post_msg'];

// 2012-03-09
$mem_gift=(strlen($_POST["mem_gift"])>0?$_POST["mem_gift"]:"N");
$msg_mem_gift=$_POST["msg_mem_gift"];
$use_mms=$_POST["use_mms"];
$socialshopping=(strlen($_POST["socialshopping"])>0?$_POST["socialshopping"]:"N");
$msg_socialshopping=$_POST["msg_socialshopping"];
$product_hongbo=(strlen($_POST["product_hongbo"])>0?$_POST["product_hongbo"]:"N");
$mem_present=(strlen($_POST["mem_present"])>0?$_POST["mem_present"]:"N");
$msg_mem_present=$_POST["msg_mem_present"];
$mem_pester=(strlen($_POST["mem_pester"])>0?$_POST["mem_pester"]:"N");
$msg_mem_pester=$_POST["msg_mem_pester"];

if ($type=="update") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER[PHP_SELF]);
	#######################################################################

	if(strlen($sms_id)>0 && strlen($sms_authkey)>0) {
		$smscountdata=getSmscount($sms_id,$sms_authkey);
		if(substr($smscountdata,0,2)=="OK") {
			$sql = "UPDATE tblsmsinfo SET ";
			$sql.= "id				= '".$sms_id."', ";
			$sql.= "authkey			= '".$sms_authkey."' ";
			mysql_query($sql,get_db_conn());
			$onload="<script>alert('SMS 기본환경 설정이 완료되었습니다.')</script>";
		} else {
			if(substr($smscountdata,0,2)=="NO") {
				$onload="<script>alert('SMS 회원 아이디가 존재하지 않습니다.\\n\\nSMS 회원 아이디를 정확히 입력하시기 바랍니다.');</script>";
			} else if(substr($smscountdata,0,2)=="AK") {
				$onload="<script>alert('SMS 회원 인증키가 일치하지 않습니다.\\n\\n인증키를 정확히 입력하시기 바랍니다.');</script>";
			} else {
				$onload="<script>alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.');</script>";
			}
		}
		$sql = "UPDATE tblsmsinfo SET ";
	} else {
		$sql = "UPDATE tblsmsinfo SET ";
		if(strlen($sms_id)>0) {
			$sql.= "id				= '".$sms_id."', ";
			$sql.= "authkey			= '', ";
		} else {
			$sql.= "id				= '', ";
			$sql.= "authkey			= '".$sms_authkey."', ";
		}
	}

	if ($check_sleep_time=="Y" || ($sleep_time1==$sleep_time2)) {
		$check_sleep_time1=$check_sleep_time2=0;
	} else {
		$check_sleep_time1=$sleep_time2;
		if($sleep_time1==0) $check_sleep_time2=23;
		else $check_sleep_time2=$sleep_time1-1;
	}

	$sql.= "sms_uname		= '".$sms_uname."', ";
	$sql.= "mem_join		= '".$mem_join."', ";
	$sql.= "mem_order		= '".$mem_order."', ";
	$sql.= "mem_delivery	= '".$mem_delivery."', ";
	$sql.= "mem_delinum		= '".$mem_delinum."', ";
	$sql.= "mem_bank		= '".$mem_bank."', ";
	$sql.= "mem_bankok		= '".$mem_bankok."', ";
	$sql.= "mem_bankokvender		= '".$mem_bankokvender."', ";
	$sql.= "mem_birth		= '".$mem_birth."', ";
	$sql.= "mem_auth		= '".$mem_auth."', ";
	$sql.= "mem_passwd		= '".$mem_passwd."', ";
	$sql.= "admin_join		= '".$admin_join."', ";
	$sql.= "admin_order		= '".$admin_order."', ";
	$sql.= "vender_order		= '".$vender_order."', ";
	$sql.= "admin_cancel	= '".$admin_cancel."', ";
	$sql.= "admin_board		= '".$admin_board."', ";
	$sql.= "admin_soldout	= '".$admin_soldout."', ";
	$sql.= "msg_mem_join	= '".$msg_mem_join."', ";
	$sql.= "msg_mem_order	= '".$msg_mem_order."', ";
	$sql.= "msg_mem_delivery= '".$msg_mem_delivery."', ";
	$sql.= "msg_mem_delinum	= '".$msg_mem_delinum."', ";
	$sql.= "msg_mem_bank	= '".$msg_mem_bank."', ";
	$sql.= "msg_mem_bankok	= '".$msg_mem_bankok."', ";
	$sql.= "msg_mem_birth	= '".$msg_mem_birth."', ";
	$sql.= "msg_mem_auth	= '".$msg_mem_auth."', ";
	$sql.= "admin_tel		= '".$admin_tel."', ";
	$sql.= "subadmin1_tel	= '".$subadmin1_tel."', ";
	$sql.= "subadmin2_tel	= '".$subadmin2_tel."', ";
	$sql.= "subadmin3_tel	= '".$subadmin3_tel."', ";
	$sql.= "sleep_time1		= '".$check_sleep_time1."', ";
	$sql.= "sleep_time2		= '".$check_sleep_time2."', ";
	$sql.= "use_mms			= '".$use_mms."', ";
	$sql.= "msg_mem_gift	= '".$msg_mem_gift."', ";
	$sql.= "mem_gift		= '".$mem_gift."', ";
	$sql.= "socialshopping	= '".$socialshopping."', ";
	$sql.= "msg_socialshopping	= '".$msg_socialshopping."', ";
	$sql.= "product_hongbo	= '".$product_hongbo."', ";
	$sql.= "mem_present		= '".$mem_present."', ";
	$sql.= "msg_mem_present	= '".$msg_mem_present."', ";
	$sql.= "mem_pester		= '".$mem_pester."', ";
	$sql.= "msg_mem_pester	= '".$msg_mem_pester."', ";
	$sql.= "return_tel		= '".$return_tel."', ";
	$sql.= "mem_join_msg	= '".$mem_join_msg."', ";
	$sql.= "pr_buy_msg		= '".$pr_buy_msg."', ";
	$sql.= "pr_cancel_msg	= '".$pr_cancel_msg."', ";
	$sql.= "pr_soldout_msg	= '".$pr_soldout_msg."', ";
	$sql.= "new_post_msg	= '".$new_post_msg."' ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert('SMS 기본환경 설정이 완료되었습니다.')</script>";
}

$sql = "SELECT * FROM tblsmsinfo ";
$result=mysql_query($sql,get_db_conn());
if ($row=mysql_fetch_object($result)) {
	$sms_id = $row->id;
	$sms_authkey = $row->authkey;
	$admin_tel = explode("-", $row->admin_tel);
	$subadmin1_tel = explode("-", $row->subadmin1_tel);
	$subadmin2_tel = explode("-", $row->subadmin2_tel);
	$subadmin3_tel = explode("-", $row->subadmin3_tel);
	$return_tel = explode("-",$row->return_tel);
	if(strlen($row->msg_mem_join)==0) $row->msg_mem_join=$msg_list[mem_join];
	if(strlen($row->msg_mem_order)==0) $row->msg_mem_order=$msg_list[mem_order];
	if(strlen($row->msg_mem_bankok)==0) $row->msg_mem_bankok=$msg_list[mem_bankok];
	if(strlen($row->msg_mem_delivery)==0) $row->msg_mem_delivery=$msg_list[mem_delivery];
	if(strlen($row->msg_mem_birth)==0) $row->msg_mem_birth=$msg_list[mem_birth];
	if(strlen($row->msg_mem_delinum)==0) $row->msg_mem_delinum=$msg_list[mem_delinum];
	if(strlen($row->msg_mem_bank)==0) $row->msg_mem_bank=$msg_list[mem_bank];
	if(strlen($row->msg_mem_auth)==0) $row->msg_mem_auth=$msg_list[mem_auth];
	if(strlen($row->msg_mem_gift)==0) $row->msg_mem_gift=$msg_list[mem_gift];
	if(strlen($row->msg_socialshopping)==0) $row->msg_socialshopping=$msg_list[socialshopping];
	if(strlen($row->msg_mem_present)==0) $row->msg_mem_present=$msg_list[mem_present];
	if(strlen($row->msg_mem_pester)==0) $row->msg_mem_pester=$msg_list[mem_pester];
	if(strlen($row->mem_join_msg)==0) $row->mem_join_msg=$msg_list[mem_join_msg];
	if(strlen($row->pr_buy_msg)==0) $row->pr_buy_msg=$msg_list[pr_buy_msg];
	if(strlen($row->pr_cancel_msg)==0) $row->pr_cancel_msg=$msg_list[pr_cancel_msg];
	if(strlen($row->pr_soldout_msg)==0) $row->pr_soldout_msg=$msg_list[pr_soldout_msg];
	if(strlen($row->new_post_msg)==0) $row->new_post_msg=$msg_list[new_post_msg];
	$sleep_time1=$row->sleep_time2;
	$sleep_time2=$row->sleep_time1;
} else {
	$sql = "INSERT INTO tblsmsinfo (sms_uname) VALUES ('".$_shopdata->shopname."')";
	$result=mysql_query($sql,get_db_conn());
}

if ($sleep_time1==0 && $sleep_time2==0) {
	$check_sleep_time="Y";
	$sleep_time1=$sleep_time2=0;
} else {
	$check_sleep_time="N";
	if($sleep_time1==23) $sleep_time1=0;
	else $sleep_time1=$sleep_time1+1;
}

?>

<? INCLUDE "header.php"; ?>

<style type="text/css">
<!--
TEXTAREA {  clip:   rect(   ); overflow: hidden; background-image:url('');font-family:굴림;}
.phone {  font-family:굴림; height: 80px; width: 173px;color: #191919;  FONT-SIZE: 9pt; font-style: normal; background-color: #A8E4ED;; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px}
-->
</style>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	for(i=1;i<=3;i++) {
		if(!IsNumeric(document.form1["return_tel"+i].value)) {
			alert("숫자만 입력하세요.");
			document.form1["return_tel"+i].focus();
			break; return;
		}
	}
	for(i=1;i<=3;i++) {
		if(!IsNumeric(document.form1["admin_tel"+i].value)) {
			alert("숫자만 입력하세요.");
			document.form1["admin_tel"+i].focus();
			break; return;
		}
	}
	for(i=1;i<=3;i++) {
		if(!IsNumeric(document.form1["subadmin1_tel"+i].value)) {
			alert("숫자만 입력하세요.");
			document.form1["subadmin1_tel"+i].focus();
			break; return;
		}
	}
	for(i=1;i<=3;i++) {
		if(!IsNumeric(document.form1["subadmin2_tel"+i].value)) {
			alert("숫자만 입력하세요.");
			document.form1["subadmin2_tel"+i].focus();
			break; return;
		}
	}
	for(i=1;i<=3;i++) {
		if(!IsNumeric(document.form1["subadmin3_tel"+i].value)) {
			alert("숫자만 입력하세요.");
			document.form1["subadmin3_tel"+i].focus();
			break; return;
		}
	}
	document.form1.type.value="update";
	document.form1.submit();
}
function CheckSleepTime(disabled) {
	document.form1.sleep_time1.disabled=disabled;
	document.form1.sleep_time2.disabled=disabled;
}
function cal_pre2(field,ismsg) {
	var strcnt,obj_msg,obj_len;
	var reserve=0;

	obj_msg = document.form1["msg_"+field];
	obj_len = document.form1["len_"+field];

	strcnt = cal_byte2(obj_msg.value);

	if(strcnt > 80&&document.form1.use_mms[1].checked==false)	{
		reserve = strcnt - 80;
		if(ismsg==true) {
			alert('메시지 내용은 80바이트를 넘을수 없습니다.\n\n작성하신 메세지 내용은 '+ reserve +'byte가 초과되었습니다.\n\n초과된 부분은 자동으로 삭제됩니다.');
		}
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

function cal_pre3(field,ismsg) {
	var strcnt,obj_msg,obj_len;
	var reserve=0;

	obj_msg = document.form1["msg_"+field];
	obj_len = document.form1["len_"+field];

	strcnt = cal_byte2(obj_msg.value);

	if(strcnt > 80)	{
		if(document.form1.use_mms[1].checked==true) {
			if(strcnt==81 && ismsg==true) alert('입력내용이 80byte가 넘어MMS로 전환됩니다');
			obj_len.value=strcnt;
		}
		else {
			reserve = strcnt - 80;
			if(ismsg==true) {
				alert('현재 MMS사용안함으로 설정되어 있습니다. 사용함으로 전환 후 사용가능합니다');
			}
			obj_msg.value = nets_check2(obj_msg.value);
			strcnt = cal_byte2(obj_msg.value);
			obj_len.value=strcnt;
		}
		return;
	}
	obj_len.value=strcnt;
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

</script>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed"  background="images/con_bg.gif">
		<col width=198></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top"  background="images/leftmenu_bg.gif">
			<? include ("menu_market.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 마케팅지원 &gt; SMS 발송/관리  &gt; <span class="2depth_select">SMS 기본환경 설정</span></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
        <td width="16"><img src="images/con_t_01.gif" width="16" height="16" border="0"></td>
        <td background="images/con_t_01_bg.gif"></td>
        <td width="16"><img src="images/con_t_02.gif" width="16" height="16" border="0"></td>
    </tr>
    <tr>
        <td width="16" background="images/con_t_04_bg1.gif"></td>
        <td bgcolor="#ffffff" style="padding:10px">






			<table cellpadding="0" cellspacing="0" width="100%">
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>

			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_smsconfig_title.gif"  ALT=""></TD>
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
					<TD width="100%" class="notice_blue">SMS 문자서비스 기본환경과  설정메뉴를 관리할 수 있습니다.</TD>
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
					<TD><IMG SRC="images/market_smsconfig_stitle1.gif"  ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif" WIDTH=7 HEIGHT=7 ALT=""></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif" WIDTH=8 HEIGHT=7 ALT=""></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue" valign="top"><IMG SRC="images/distribute_img.gif"></TD>
					<TD width="100%" class="notice_blue">1) <b><span class="font_orange">SMS 서비스는 유료 서비스로서 머니를 충전하셔야만 이용이 가능합니다.</b></span><br>2) 회신 전화번호는 SMS 발송시 회신전화번호로 찍히는 번호이니 관리자 휴대폰 번호를 입력하시기를 권장합니다.<br>3) 관리자 휴대폰 번호는 관리자에게 SMS 발송시 필요함으로 입력해 주세요.<br>4) <b>부운영자 휴대폰 번호를 입력하시면 관리자에게 SMS 발송시 동시에 발송이 됩니다.</b><br>5) SMS 임시중단 적용시 해당 시간동안 SMS는 발송이 안되며, 발송이 안되었던 메세지들은 임시중단이 종료된 후 일괄 발송됩니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif" WIDTH=7 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif" WIDTH=8 HEIGHT=8 ALT=""></TD>
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
				<TR>
					<TD background="images/table_top_line.gif" colspan=2></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">SMS 아이디</TD>
					<TD class="td_con1"><INPUT maxLength=20 size=40 name=sms_id value="<?=$row->id?>" class="input" style=width:30%>&nbsp;<span class="font_orange">＊SMS 가입시 신청하신 아이디를 입력하세요.</span></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">SMS 인증키</TD>
					<TD class="td_con1"><INPUT maxLength=32 size=40 name=sms_authkey value="<?=$row->authkey?>" class="input" style=width:30%>&nbsp;<span class="font_orange">＊SMS 회원 인증키를 정확히 입력하세요.</span></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">쇼핑몰 상점명</TD>
					<TD class="td_con1"><INPUT maxLength=20 size=40 name=sms_uname value="<?=$row->sms_uname?>" class="input" style=width:30%></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">회신 전화번호</TD>
					<TD class="td_con1">
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=3 size=5 name=return_tel1 value="<?=$return_tel[0]?>" class="input"> -
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=return_tel2 value="<?=$return_tel[1]?>" class="input"> -
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=return_tel3 value="<?=$return_tel[2]?>" class="input">&nbsp;<span class="font_orange">＊SMS 발송시 <B>기본 회신번호</B>로 지정됩니다.</span>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">관리자 휴대폰 번호</TD>
					<TD class="td_con1">
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=3 size=5 name=admin_tel1 value="<?=$admin_tel[0]?>" class="input"> -
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=admin_tel2 value="<?=$admin_tel[1]?>" class="input"> -
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=admin_tel3 value="<?=$admin_tel[2]?>" class="input">
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<tr>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">부운영자1 휴대폰 번호</TD>
					<TD class="td_con1">
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=3 size=5 name=subadmin1_tel1 value="<?=$subadmin1_tel[0]?>" class="input"> -
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=subadmin1_tel2 value="<?=$subadmin1_tel[1]?>" class="input"> -
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=subadmin1_tel3 value="<?=$subadmin1_tel[2]?>" class="input">
					</TD>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<tr>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">부운영자2 휴대폰 번호</TD>
					<TD class="td_con1">
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=3 size=5 name=subadmin2_tel1 value="<?=$subadmin2_tel[0]?>" class="input"> -
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=subadmin2_tel2 value="<?=$subadmin2_tel[1]?>" class="input"> -
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=subadmin2_tel3 value="<?=$subadmin2_tel[2]?>" class="input">
					</TD>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<tr>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">부운영자3 휴대폰 번호</TD>
					<TD class="td_con1">
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=3 size=5 name=subadmin3_tel1 value="<?=$subadmin3_tel[0]?>" class="input"> -
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=subadmin3_tel2 value="<?=$subadmin3_tel[1]?>" class="input"> -
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=subadmin3_tel3 value="<?=$subadmin3_tel[2]?>" class="input">
					</TD>
				</tr>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<tr>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">SMS 임시중단</TD>
					<TD class="td_con1">
					<INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" onclick=CheckSleepTime(true) type=radio value=Y name=check_sleep_time <?=($check_sleep_time=="Y"?"checked":"")?>>적용안함  &nbsp;&nbsp;
					<INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" onclick=CheckSleepTime(false) type=radio value=N name=check_sleep_time <?=($check_sleep_time=="N"?"checked":"")?>>적용
					<SELECT name=sleep_time1 class="select" style=width:70px>
<?
					for($i=0;$i<24;$i++){
						echo "<option value='".$i."'";
						if($i==$sleep_time1) echo " selected";
						echo ">".($i>12?"pm":"am")." ".substr("0".$i,-2)."</option>";
					}
?>
					</SELECT>
					시 부터
					<SELECT name=sleep_time2 class="select"  style=width:70px>
<?
					for($i=0;$i<24;$i++){
						echo "<option value='".$i."'";
						if($i==$sleep_time2) echo " selected";
						echo ">".($i>12?"pm":"am")." ".substr("0".$i,-2)."</option>";
					}
?>
					</SELECT>
					시 까지
					<?if($check_sleep_time=="Y")echo"<script>CheckSleepTime(true);</script>\n"; ?>
					</TD>
				</tr>
				<TR>
					<TD background="images/table_top_line.gif" colspan=2></TD>
				</TR>
				<tr>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">MMS사용</TD>
					<TD class="td_con1">
					<INPUT type="radio" name="use_mms" value="N" <?if($row->use_mms=="N") echo "checked"?>>사용안함
					<INPUT type="radio" name="use_mms" value="Y" <?if($row->use_mms=="Y") echo "checked"?>>사용함
					</TD>
				</tr>
				<TR>
					<TD background="images/table_top_line.gif" colspan=2></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="30">&nbsp;</td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_smsconfig_stitle2.gif" ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="262" valign="top">
					<table align="center" cellpadding="0" cellspacing="0" width="200">
					<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_join <?if($row->mem_join=="Y") echo "checked"?>>회원가입 축하메세지</td>
					</tr>
					<tr>
						<td>
						<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
						<TR>
							<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
						</TR>
						<TR>
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_join',true);" name=msg_mem_join rows=5 cols="26" onchange="cal_pre2('mem_join',true);"><?=$row->msg_mem_join?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif">
							<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_join size="3" class="input_hide">bytes (최대80 bytes)
							<SCRIPT>cal_pre2('mem_join',false);</SCRIPT>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
					<td width="262" valign="top">
					<table align="center" cellpadding="0" cellspacing="0" width="200">
					<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_order <?if($row->mem_order=="Y") echo "checked"?>>상품주문 안내메세지</td>
					</tr>
					<tr>
						<td>
						<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
						<TR>
							<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
						</TR>
						<TR>
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_order',true);" name=msg_mem_order rows=5 cols="26" onchange="cal_pre2('mem_order',true);"><?=$row->msg_mem_order?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif">
							<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_order size="3" class="input_hide">bytes (최대80 bytes)
							<SCRIPT>cal_pre2('mem_order',false);</SCRIPT>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
					<td width="262" valign="top">
					<table align="center" cellpadding="0" cellspacing="0" width="200">
					<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_delivery <?if($row->mem_delivery=="Y") echo "checked"?>>상품발송 안내메세지</td>
					</tr>
					<tr>
						<td>
						<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
						<TR>
							<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
						</TR>
						<TR>
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_delivery',true);" name=msg_mem_delivery rows=5 cols="26" onchange="cal_pre2('mem_delivery',true);"><?=$row->msg_mem_delivery?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif">
							<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_delivery size="3" class="input_hide">bytes (최대80 bytes)
							<SCRIPT>cal_pre2('mem_delivery',false);</SCRIPT>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td width="262" valign="top">&nbsp;</td>
					<td width="262" valign="top">&nbsp;</td>
					<td width="262" valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td width="262" height="85" valign="top">
					<table align="center" cellpadding="0" cellspacing="0" width="200">
					<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_delinum <?if($row->mem_delinum=="Y") echo "checked"?>>송장번호 안내메세지</td>
					</tr>
					<tr>
						<td>
						<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
						<TR>
							<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
						</TR>
						<TR>
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_delinum',true);" name=msg_mem_delinum rows=5 cols="26" onchange="cal_pre2('mem_delinum',true);"><?=$row->msg_mem_delinum?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif">
							<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_delinum size="3" class="input_hide">bytes (최대80 bytes)
							<SCRIPT>cal_pre2('mem_delinum',false);</SCRIPT>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td class="font_blue1">* 택배회사/송장번호는 상품발송시 내용을<br>&nbsp;&nbsp;자동 안내</td>
					</tr>
					</table>
					</td>
					<td width="262" height="85" valign="top">
					<table align="center" cellpadding="0" cellspacing="0" width="200">
					<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_bank <?if($row->mem_bank=="Y") echo "checked"?>>무통장입금 안내메세지</td>
					</tr>
					<tr>
						<td>
						<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
						<TR>
							<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
						</TR>
						<TR>
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_bank',true);" name=msg_mem_bank rows=5 cols="26" onchange="cal_pre2('mem_bank',true);"><?=$row->msg_mem_bank?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif">
							<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_bank size="3" class="input_hide">bytes (최대80 bytes)
							<SCRIPT>cal_pre2('mem_bank',false);</SCRIPT>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td class="font_blue1">* 계좌번호는 상품주문시, 고객이 선택한<br>&nbsp; 계좌번호 안내</td>
					</tr>
					</table>
					</td>
					<td width="262" height="85" valign="top">
					<table align="center" cellpadding="0" cellspacing="0" width="200">
					<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_bankok <?if($row->mem_bankok=="Y") echo "checked"?>>입금확인 안내메세지
						<br>
						<INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_bankokvender <?if($row->mem_bankokvender=="Y") echo "checked"?>>입점사에도 메세지 발송</td>
					</tr>
					<tr>
						<td>
						<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
						<TR>
							<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
						</TR>
						<TR>
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_bankok',true);" name=msg_mem_bankok rows=5 cols="26" onchange="cal_pre2('mem_bankok',true);"><?=$row->msg_mem_bankok?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif">
							<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_bankok size="3" class="input_hide">bytes (최대80 bytes)
							<SCRIPT>cal_pre2('mem_bankok',false);</SCRIPT>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td width="262" height="18" valign="top">&nbsp;</td>
					<td width="262" height="18" valign="top">&nbsp;</td>
					<td width="262" height="18" valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td width="262" height="35" valign="top">
					<table align="center" cellpadding="0" cellspacing="0" width="200">
					<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_birth <?if($row->mem_birth=="Y") echo "checked"?>>생일회원 자동메세지</td>
					</tr>
					<tr>
						<td>
						<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
						<TR>
							<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
						</TR>
						<TR>
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_birth',true);" name=msg_mem_birth rows=5 cols="26" onchange="cal_pre2('mem_birth',true);"><?=$row->msg_mem_birth?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif">
							<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_birth size="3" class="input_hide">bytes (최대80 bytes)
							<SCRIPT>cal_pre2('mem_birth',false);</SCRIPT>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
					<td width="262" height="35" valign="top">
					<table align="center" cellpadding="0" cellspacing="0" width="200">
					<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_auth <?if($row->mem_auth=="Y") echo "checked"?>>회원인증 안내메세지</td>
					</tr>
					<tr>
						<td>
						<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
						<TR>
							<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
						</TR>
						<TR>
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_auth',true);" name=msg_mem_auth rows=5 cols="26" onchange="cal_pre2('mem_auth',true);"><?=$row->msg_mem_auth?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif">
							<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_auth size="3" class="input_hide">bytes (최대80 bytes)
							<SCRIPT>cal_pre2('mem_auth',false);</SCRIPT></TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
					<td width="262" height="35" valign="top">
					<table align="center" cellpadding="0" cellspacing="0" width="200">
					<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_passwd <?if($row->mem_passwd=="Y") echo "checked"?>>비밀번호 분실 안내메세지</td>
					</tr>
					<tr>
						<td>
						<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
						<TR>
							<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
						</TR>
						<TR>
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_passwd',true);" name=msg_mem_passwd rows=5 cols="26" onchange="cal_pre2('mem_passwd',true);"><?=$msg_list[mem_passwd]?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif">
							<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_passwd size="3" class="input_hide">bytes (최대80 bytes)
							<SCRIPT>cal_pre2('mem_passwd',false);</SCRIPT>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td width="262" height="18" valign="top">&nbsp;</td>
					<td width="262" height="18" valign="top">&nbsp;</td>
					<td width="262" height="18" valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td width="262" height="35" valign="top">
					<table align="center" cellpadding="0" cellspacing="0" width="200">
					<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_gift <?if($row->mem_gift=="Y") echo "checked"?>>상품권선물하기 </td>
					</tr>
					<tr>
						<td>
						<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
						<TR>
							<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
						</TR>
						<TR>
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre3('mem_gift',true);" name=msg_mem_gift rows=5 cols="26" onchange="cal_pre3('mem_gift',true);"><?=$row->msg_mem_gift?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif">
							<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_gift size="3" class="input_hide">bytes
							<SCRIPT>cal_pre3('mem_gift',false);</SCRIPT>
							</TD>
						</TR>
						</TABLE>
						</td>
					  </tr>
					</table>
					</td>
					<td width="262" height="35" valign="top">
					<table align="center" cellpadding="0" cellspacing="0" width="200">
					<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=socialshopping <?if($row->socialshopping=="Y") echo "checked"?>>소셜쇼핑 구매달성 실패시 결제취소메세지 </td>
					</tr>
					<tr>
						<td>
						<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
						<TR>
							<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
						</TR>
						<TR>
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre3('socialshopping',true);" name=msg_socialshopping rows=5 cols="26" onchange="cal_pre3('socialshopping',true);"><?=$row->msg_socialshopping?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif">
							<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_socialshopping size="3" class="input_hide">bytes
							<SCRIPT>cal_pre3('socialshopping',false);</SCRIPT>
							</TD>
						</TR>
						</TABLE>
						</td>
					  </tr>
					</table>
					</td>
					<td width="262" height="35" valign="top">
					<table align="center" cellpadding="0" cellspacing="0" width="200">
					<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=product_hongbo <?if($row->product_hongbo=="Y") echo "checked"?>>상품홍보메세지 </td>
					</tr>
					<tr>
						<td>
						<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
						<TR>
							<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
						</TR>
						<TR>
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre3('product_hongbo',true);" name=msg_product_hongbo rows=5 cols="26" onchange="cal_pre3('product_hongbo',true);"><?=$msg_list[product_hongbo]?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif">
							<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_product_hongbo size="3" class="input_hide">bytes
							<SCRIPT>cal_pre3('product_hongbo',false);</SCRIPT>
							</TD>
						</TR>
						</TABLE>
						</td>
					  </tr>
					</table>
					</td>
				</tr>
				<tr>
					<td width="262" height="18" valign="top">&nbsp;</td>
					<td width="262" height="18" valign="top">&nbsp;</td>
					<td width="262" height="18" valign="top">&nbsp;</td>
				</tr>
				<tr>
					<td width="262" height="35" valign="top">
					<table align="center" cellpadding="0" cellspacing="0" width="200">
					<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_present <?if($row->mem_present=="Y") echo "checked"?>>상품 선물하기 메세지</td>
					</tr>
					<tr>
						<td>
						<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
						<TR>
							<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
						</TR>
						<TR>
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre3('mem_present',true);" name=msg_mem_present rows=5 cols="26" onchange="cal_pre3('mem_present',true);"><?=$row->msg_mem_present?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif">
							<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_present size="3" class="input_hide">bytes
							<SCRIPT>cal_pre3('mem_present',false);</SCRIPT>
							</TD>
						</TR>
						</TABLE>
						</td>
					  </tr>
					</table>
					</td>
					<td width="262" height="35" valign="top">
<? if($_shopdata->pester_state == "Y"){?>
					<table align="center" cellpadding="0" cellspacing="0" width="200">
					<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_pester <?if($row->mem_pester=="Y") echo "checked"?>>조르기요청 메세지</td>
					</tr>
					<tr>
						<td>
						<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
						<TR>
							<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
						</TR>
						<TR>
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre3('mem_pester',true);" name=msg_mem_pester rows=5 cols="26" onchange="cal_pre3('mem_pester',true);"><?=$row->msg_mem_pester?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif">
							<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_pester size="3" class="input_hide">bytes
							<SCRIPT>cal_pre3('mem_pester',false);</SCRIPT>
							</TD>
						</TR>
						</TABLE>
						</td>
					  </tr>
					</table>
<?}?>
					</td>
					<td width="262" height="35" valign="top"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="30">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/market_smsconfig_stitle3.gif" ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3px; padding-bottom:3px;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="262" valign="top">
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_join <?if($row->admin_join=="Y") echo "checked"?>>회원가입 통보메세지</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top">
									<TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_join',true);" name=mem_join_msg rows=5 cols="26" onchange="cal_pre2('admin_join',true);"><?=$row->mem_join_msg?></TEXTAREA>
								</TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_join size="3" class="input_hide">bytes (최대80 bytes)
								<SCRIPT>cal_pre2('admin_join',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
						</td>
						<td width="262" valign="top">
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23">
								<INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_order <?if($row->admin_order=="Y") echo "checked"?>>상품주문 통보메세지<br />
								<INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=vender_order <?if($row->vender_order=="Y") echo "checked"?>>입점 상품주문 통보메세지
							</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_order',true);" name=pr_buy_msg rows=5 cols="26" onchange="cal_pre2('admin_order',true);"><?=$row->pr_buy_msg?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_order size="3" class="input_hide">bytes (최대80 bytes)
								<SCRIPT>cal_pre2('admin_order',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
						</td>
						<td width="262" valign="top">
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_cancel <?if($row->admin_cancel=="Y") echo "checked"?>>주문취소 통보메세지</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_cancel',true);" name=pr_cancel_msg rows=5 cols="26" onchange="cal_pre2('admin_cancel',true);"><?=$row->pr_cancel_msg?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=41 name=len_admin_cancel size="3" class="input_hide">bytes (최대80 bytes)
								<SCRIPT>cal_pre2('admin_cancel',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td width="262" valign="top">&nbsp;</td>
						<td width="262" valign="top">&nbsp;</td>
						<td width="262" valign="top">&nbsp;</td>
					</tr>
					<tr>
						<td width="262" height="85" valign="top">
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_soldout <?if($row->admin_soldout=="Y") echo "checked"?>>상품품절 통보메세지</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_soldout',true);" name=pr_soldout_msg rows=5 cols="26" onchange="cal_pre2('mem_join',true);"><?=$row->pr_soldout_msg?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=41 name=len_admin_soldout size="3" class="input_hide">bytes (최대80 bytes)
								<SCRIPT>cal_pre2('admin_soldout',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						<tr>
							<td class="font_blue1">* 품절은 총 수량 품절시만 가능.<br>&nbsp;&nbsp;&nbsp;옵션별 수량 품절은 불가능</td>
						</tr>
						</table>
						</td>
						<td width="262" height="85" valign="top">
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_board <?if($row->admin_board=="Y") echo "checked"?>>게시판 신규게시글 통보메세지</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" WIDTH=200 HEIGHT="30" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="middle"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_board',true);" name=new_post_msg rows=5 cols="26" onchange="cal_pre2('admin_board',true);"><?=$row->new_post_msg?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=41 name=len_admin_board size="3" class="input_hide">bytes (최대80 bytes)
								<SCRIPT>cal_pre2('admin_board',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						<tr>
							<td class="font_blue1">* 신규 게시글인 경우에만 통보가능<br>&nbsp;&nbsp; (답변글 제외)</td>
						</tr>
						</table>
						</td>
						<td width="262" height="85" valign="top">&nbsp;</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td height="30"><hr size="1" color="#F3F3F3"></td>
			</tr>
			<tr>
				<td align=center><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
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
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;" class="menual_bg">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">SMS 기본 환경 설정</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- <b><span class="font_orange">SMS는 유료서비스로서 이용전 반드시 충전을 하셔야만 사용이 가능합니다. </b></span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- SMS 회원 수신용 기본메세지에 체크를 하시면 회원에게 메세지가 자동 발송됩니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- SMS 관리자 수신용 기본메세지에 체크를 하시면 관리자 및 부운영자에게 메세지가 자동 발송됩니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 메세지는 80byte까지 입력 가능하오니 넘지않도록 주의하시기 바랍니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 메세지 내용중 매타태크는 메세지 발송시 자동으로 해당 값으로 변경됨으로 충분히 고려하시고 메세지 작성을 하시기 바랍니다.</td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
					</tr>
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><font color="black"><b>회원가입 축하메세지</b></font></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[ID]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" width="100%" height="27">회원 ID로 변경되어 메세지 전송이 됩니다. (예:hong27)</TD>
						</TR>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[NAME]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%" height="27">회원 이름으로 변경되어 메세지 전송이 됩니다. (예:홍길동)</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">&nbsp;</td>
					</tr>
					<tr>
						<td width="20" align="right"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><font color="black"><b>상품주문 안내메세지</b></font></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[NAME]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" width="100%" height="27">회원 이름으로 변경되어 메세지 전송이 됩니다. (예:홍길동)</TD>
						</TR>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[PRODUCT]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%" height="27">주문 상품명으로 변경되어 메세지 전송이 됩니다. (예:구찌스타일 가방LT-3)</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">&nbsp;</td>
					</tr>
					<tr>
						<td width="20" align="right"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><b><font color="black">무통장입금 안내메세지</font></b></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[NAME]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" width="100%">회원 이름으로 변경되어 메세지 전송이 됩니다. (예:홍길동)</TD>
						</TR>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[PRICE]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" width="100%">상품구매금액으로 변경되어 메세지 전송 (예:50,000)</TD>
						</TR>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[ACCOUNT]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%" height="27">상품구매시 회원이 선택한 입금계좌번호로 변경되어 메세지 전송<br>(예:123456-78-901234 예금주:아무개)</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">&nbsp;</td>
					</tr>
					<tr>
						<td width="20" align="right"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><b><font color="black">입금확인/상품발송 안내메세지</font></b></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[NAME]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" width="100%">회원 이름으로 변경되어 메세지 전송이 됩니다. (예:홍길동)</TD>
						</TR>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[DATE]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" width="100%">해당 월/일로 	변경되어 메세지 전송 (예:04월 25일)</TD>
						</TR>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[PRICE]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%" height="27">상품구매금액으로 변경되어 메세지 전송 (예:50,000)</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">&nbsp;</td>
					</tr>
					<tr>
						<td width="20" align="right"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><b><font color="black">송장번호 안내메세지</font></b></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[NAME]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" width="100%">회원 이름으로 변경되어 메세지 전송이 됩니다. (예:홍길동)</TD>
						</TR>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[DATE]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" width="100%">해당 월/일로 변경되어 메세지 전송 (예:04월 25일)</TD>
						</TR>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[PRICE]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" width="100%">상품구매금액으로 변경되어 메세지 전송 (예:50,000)</TD>
						</TR>
						<tr>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[DELICOM]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" width="100%">택배회사명으로 변경되어 메세지 전송 (예:KGB택배)</TD>
						</tr>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[DELINUM]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%" height="27">송장번호로 변경되어 메세지 전송 (예:1234-5678-9012)</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">&nbsp;</td>
					</tr>
					<tr>
						<td width="20" align="right"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><b><font color="black">생일회원 자동메세지</font></b></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[NAME]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" width="100%" height="27">회원 이름으로 변경되어 메세지 전송이 됩니다. (예:홍길동)</TD>
						</TR>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[DATE]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%" height="27">해당 월/일로 변경되어 메세지 전송(예:04월 25일)-주민등록번호의 생일기준으로 데이터 추출</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td class="space_top">&nbsp;</td>
					</tr>
					<tr>
						<td width="20" align="right"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><b><font color="black">선물하기 자동메세지</font></b></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td class="space_top">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:silver; border-top-style:solid;;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[NAME]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" width="100%" height="27">회원 이름으로 변경되어 메세지 전송이 됩니다. (예:홍길동)</TD>
						</TR>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[AUTHCODE]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" width="100%" height="27">인증번호 (예. 8a5689 - a1ebe2)</TD>
						</TR>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[URL]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%" height="27">쇼핑몰 주소</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td class="space_top">&nbsp;</td>
					</tr>
					<tr>
						<td width="20" align="right"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><b><font color="black">공동구매 미달성시 결제취소 메세지</font></b></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td class="space_top">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[PRODUCT]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%" height="27">주문 상품명으로 변경되어 메세지 전송이 됩니다. (예:구찌스타일 가방LT-3)</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td class="space_top">&nbsp;</td>
					</tr>
					<tr>
						<td width="20" align="right"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td><b><font color="black">상품 홍보메세지</font></b></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td class="space_top">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[NAME]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" width="100%" height="27">회원 이름으로 변경되어 메세지 전송이 됩니다. (예:홍길동)</TD>
						</TR>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[PRODUCT]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" width="100%" height="27">홍보하는 상품명으로 변경되어 메세지 전송이 됩니다. (예:구찌스타일 가방LT-3</TD>
						</TR>
						<TR>
							<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[URL]</TD>
							<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%" height="27">상품 상세페이지 주소</TD>
						</TR>
						</TABLE>
						</td>
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

<? INCLUDE "copyright.php"; ?>