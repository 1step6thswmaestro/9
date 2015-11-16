<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");
include_once($Dir."lib/ext/func.php");
####################### 페이지 접근권한 check ###############
$PageCode = "me-2";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################


if($_POST['mode'] == 'autoset'){

	$_POST['upday'] = preg_replace('/[^0-9]+/','',$_POST['upday']);

	if(!_empty($_POST['rangestart'])){
		$_POST['rangestart'] = preg_replace('/[^0-9]+/','',$_POST['rangestart']);
		if(!_isInt($_POST['rangestart']) || $_POST['rangestart'] > 30) $_POST['rangestart'] = 1;
	}

	if(!_empty($_POST['rangemonth'])){
		$_POST['rangemonth'] = preg_replace('/[^0-9]+/','',$_POST['rangemonth']);
		if(!_isInt($_POST['rangemonth']) || $_POST['rangemonth'] > 30) $_POST['rangemonth'] = 12;
	}

	if(!_empty($_POST['upday'])){
		$_POST['upday'] = preg_replace('/[^0-9]+/','',$_POST['upday']);
		if(!_isInt($_POST['upday']) || $_POST['upday'] > 30) $_POST['upday'] = 1;
	}
	if(!_empty($_POST['keepclass'])){
		$_POST['upday'] = preg_replace('/[^0-9]+/','',$_POST['upday']);
		if(!_isInt($_POST['upday']) || $_POST['upday'] > 30) $_POST['upday'] = 1;
	}
	$keyarr = array('rangestart','rangemonth','upday','keepclass','runtype');
	if(!_empty($_POST['rangestart']) && !_empty($_POST['rangemonth']) && !_empty($_POST['upday']) && !_empty($_POST['keepclass'])){
		foreach($keyarr as $name){
			$sql = "select count(*) from extra_conf where type='autogroup' and name='".$name."'";
			if(false !== $res = mysql_query($sql,get_db_conn())){
				if(mysql_result($res,0,0) > 0){
					$sql = "update extra_conf set value ='".$_POST[$name]."' where type='autogroup' and name='".$name."'";
				}else{
					$sql = "insert into extra_conf set type='autogroup',name='".$name."',value ='".$_POST[$name]."'";
				}
				mysql_query($sql,get_db_conn());
			}
		}
	}else{
		$sql = "delete * from extra_conf where type='autogroup'";
		mysql_query($sql,get_db_conn());
	}

?>
<script language="javascript" type="text/javascript">
alert('자동승급설정이 등록 되었습니다.');
document.location.replace('/admin/member_groupnew.php');
</script>
<?
exit;
}

$max=10;

$type=$_POST["type"];
$mode=$_POST["mode"];
$group_code=$_POST["group_code"];

$group_name=$_POST["group_name"];
$group_description=$_POST["group_description"];
$group_payment=$_POST["group_payment"];

$group_type=$_POST["group_type"];

$group_usemoney_R=$_POST["group_usemoney_R"];
$group_addmoney_R=$_POST["group_addmoney_R"];
$group_salerate_R=$_POST["group_salerate_R"];

$group_usemoney_S=$_POST["group_usemoney_S"];
$group_addmoney_S=$_POST["group_addmoney_S"];
$group_salerate_S=$_POST["group_salerate_S"];

$group_apply_gift=$_POST["group_apply_gift"];
$group_apply_coupon=$_POST["group_apply_coupon"];
$group_apply_reserve=$_POST["group_apply_reserve"];
$group_apply_use_reserve=$_POST["group_apply_use_reserve"];
//$group_card_commi=$_POST["group_card_commi"];
$group_carr_free_sec=$_POST["group_carr_free_sec"];
$group_carr_free_won=$_POST["group_carr_free_won"];
$group_carr_free_won_ex=$_POST["group_carr_free_won_ex"];
$group_carr_free = $group_carr_free_sec."|".$group_carr_free_won."|".$group_carr_free_won_ex;
$group_order_price=$_POST["group_order_price"];
$group_order_cnt=$_POST["group_order_cnt"];
$group_seller=$_POST["group_seller"];
$group_iossreserve=$_POST['iossreserve'];
$group_excp_auto=$_POST['group_excp_auto'];

$groupimg=$_FILES["groupimg"];

$reg_group=$_POST["reg_group"];

$use_auto_coupon=$_POST["use_auto_coupon"];

$groupCouponSendType=$_POST["groupCouponSendType"];
$groupCouponSendType_M=$_POST["groupCouponSendType_M"];
$groupCouponSendType_D=$_POST["groupCouponSendType_D"];

$imagepath=$Dir.DataDir."shopimages/etc/";

if ($type=="insert" || $type=="modify") {
	if ($group_type=="R" && strlen($group_addmoney_R)!=0) {
		$group_view="W";
		$group_addmoney=$group_addmoney_R;
		$group_usemoney=$group_usemoney_R;
	} else if ($group_type=="R" && strlen($group_salerate_R)!=0) {
		$group_view="P";
		$group_addmoney=$group_salerate_R;
		$group_usemoney=$group_usemoney_R;
	} else if ($group_type=="S" && strlen($group_addmoney_S)!=0) {
		$group_view="W";
		$group_addmoney=$group_addmoney_S;
		$group_usemoney=$group_usemoney_S;
	} else if ($group_type=="S" && strlen($group_salerate_S)!=0) {
		$group_view="P";
		$group_addmoney=$group_salerate_S;
		$group_usemoney=$group_usemoney_S;
	} else if($group_type=="M") {
		$group_view="X";
		$group_addmoney=0;
		$group_usemoney=0;
	}
	if(strlen($group_usemoney)==0) $group_usemoney=0;
}

if(strlen($reg_group)==0 && $type!="reg_group"){
	$reg_group=$_shopdata->group_code;
}

if ($type=="insert") {
	$sql = "SELECT MAX(mid(group_code,3,2))+1 as cnt, COUNT(*) as count FROM tblmembergroup ";
	$result = mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)){
		if($row->count>=$max){
			echo "<script>alert('등급은 최대 ".$max."개 까지만 등록가능합니다.');history.go(-1);</script>";
			mysql_free_result($result);
			exit;
		}else {
			$cnt=substr("0".($row->cnt),-2);
			$count=$row->count;
		}
		mysql_free_result($result);
	}
	if(strlen($cnt)==0 || $count==0) $cnt="01";

	$group_code=$group_type.$group_view.$cnt;

	if (strlen($groupimg["name"])>0) {
		if (strtolower(substr($groupimg["name"],-3,3))!="gif") {
			echo "<script>alert('등급이미지는 gif파일만 등록이 가능합니다.');history.go(-1);</script>";
			exit;
		} else if ($groupimg["size"]==0 || $groupimg["size"] > 153600) {
			echo "<script>alert('정상적인 파일이 아니거나 파일 용량이 너무 큽니다.\\n\\n다시 확인 후 등록하시기 바랍니다.');history.go(-1);</script>";
			exit;
		}
		$uploaded_img="groupimg_".$group_code.".gif";
		move_uploaded_file ($groupimg["tmp_name"], $imagepath.$uploaded_img);
		chmod($imagepath.$uploaded_img,0666);
	}

	$sql = "INSERT tblmembergroup SET ";
	$sql.= "group_code		= '".$group_code."', ";
	$sql.= "group_name		= '".$group_name."', ";
	$sql.= "group_description='".$group_description."', ";
	$sql.= "group_payment	= '".$group_payment."', ";
	$sql.= "group_usemoney	= '".$group_usemoney."', ";
	$sql.= "group_apply_gift= '".$group_apply_gift."', ";
	$sql.= "group_apply_coupon= '".$group_apply_coupon."', ";
	$sql.= "group_apply_reserve= '".$group_apply_reserve."', ";
	$sql.= "group_apply_use_reserve= '".$group_apply_use_reserve."', ";
	//$sql.= "group_card_commi= '".$group_card_commi."', ";
	$sql.= "group_carr_free= '".$group_carr_free."', ";
	$sql.= "group_order_price= '".$group_order_price."', ";
	$sql.= "group_order_cnt= '".$group_order_cnt."', ";
	//$sql.= "group_order_type= '".$group_order_type."', ";
	$sql.= "group_seller= '".$group_seller."', ";
	$sql.= "use_auto_coupon= '".$use_auto_coupon."', ";
	$sql.= "group_iossreserve= '".$group_iossreserve."', ";
	$sql.= "group_excp_auto= '".$group_excp_auto."', ";
	$sql.= "group_addmoney	= '".$group_addmoney."', ";
	$sql.= "groupCouponSendType	= '".$groupCouponSendType."', ";
	$sql.= "groupCouponSendType_M	= '".$groupCouponSendType_M."', ";
	$sql.= "groupCouponSendType_D	= '".$groupCouponSendType_D."' ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert('회원등급 등록이 완료되었습니다.');</script>";

	$log_content = "## 회원등급생성 - $group_code $group_payment $group_name $group_usemoney $group_addmoney";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

} else if ($type=="modify" && $mode=="result" && strlen($group_code)==4) {
	$group_code2=$group_type.$group_view.substr($group_code,2,2);
	if (strlen($groupimg["name"])>0) {
		if (strtolower(substr($groupimg["name"],-3,3))!="gif") {
			echo "<script>alert('등급이미지는 gif파일만 등록이 가능합니다.');history.go(-1);</script>";
			exit;
		} else if ($groupimg["size"]==0 || $groupimg["size"] > 153600) {
			echo "<script>alert('정상적인 파일이 아니거나 파일 용량이 너무 큽니다.\\n\\n다시 확인 후 등록하시기 바랍니다.');history.go(-1);</script>";
			exit;
		}
		if (file_exists($imagepath."groupimg_".$group_code.".gif")) {
			unlink ($imagepath."groupimg_".$group_code.".gif");
		}
		$uploaded_img="groupimg_".$group_code2.".gif";
		move_uploaded_file ($groupimg["tmp_name"], $imagepath.$uploaded_img);
		chmod($imagepath.$uploaded_img,0666);
	}
	$sql = "UPDATE tblmembergroup SET ";
	$sql.= "group_code		= '".$group_code2."', ";
	$sql.= "group_name		= '".$group_name."', ";
	$sql.= "group_description='".$group_description."', ";
	$sql.= "group_payment	= '".$group_payment."', ";
	$sql.= "group_usemoney	= '".$group_usemoney."', ";
	$sql.= "group_apply_gift= '".$group_apply_gift."', ";
	$sql.= "group_apply_coupon= '".$group_apply_coupon."', ";
	$sql.= "group_apply_reserve= '".$group_apply_reserve."', ";
	$sql.= "group_apply_use_reserve= '".$group_apply_use_reserve."', ";
	//$sql.= "group_card_commi= '".$group_card_commi."', ";
	$sql.= "group_carr_free= '".$group_carr_free."', ";
	$sql.= "group_order_price= '".$group_order_price."', ";
	$sql.= "group_order_cnt= '".$group_order_cnt."', ";
	//$sql.= "group_order_type= '".$group_order_type."', ";
	$sql.= "group_seller= '".$group_seller."', ";
	$sql.= "use_auto_coupon= '".$use_auto_coupon."', ";
	$sql.= "group_iossreserve= '".$group_iossreserve."', ";
	$sql.= "group_excp_auto= '".$group_excp_auto."', ";
	$sql.= "group_addmoney	= '".$group_addmoney."', ";
	$sql.= "groupCouponSendType	= '".$groupCouponSendType."', ";
	$sql.= "groupCouponSendType_M	= '".$groupCouponSendType_M."', ";
	$sql.= "groupCouponSendType_D	= '".$groupCouponSendType_D."' ";
	$sql.= "WHERE group_code = '".$group_code."' ";
	mysql_query($sql,get_db_conn());

	$log_content = "## 회원등급변경 - $group_code $group_payment $group_name $group_usemoney $group_addmoney";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	if ($group_code!=$group_code2) {
		$sql = "UPDATE tblmember SET group_code = '".$group_code2."' ";
		$sql.= "WHERE group_code = '".$group_code."' ";
		mysql_query($sql,get_db_conn());

		$sql = "UPDATE tblproductcode SET group_code = '".$group_code2."' ";
		$sql.= "WHERE group_code = '".$group_code."' ";
		mysql_query($sql,get_db_conn());

		$sql = "UPDATE tblboardadmin SET group_code = '".$group_code2."' ";
		$sql.= "WHERE group_code = '".$group_code."' ";
		mysql_query($sql,get_db_conn());

		$sql = "UPDATE tblproductgroupcode SET group_code = '".$group_code2."' ";
		$sql.= "WHERE group_code = '".$group_code."' ";
		mysql_query($sql,get_db_conn());
	}



		//회원할인율테이블 수정
		$sql = "UPDATE tblmemberdiscount SET group_code = '".$group_code2."' ";
		$sql.= "WHERE group_code = '".$group_code."' ";
		mysql_query($sql,get_db_conn());




	$onload="<script>alert('회원등급 수정이 완료되었습니다.');</script>";
	unset($type);
	unset($mode);
	unset($group_code);
} else if ($type=="delete" && strlen($group_code)==4) {
	$sql = "DELETE FROM tblmembergroup WHERE group_code = '".$group_code."' ";
	mysql_query($sql,get_db_conn());
	$sql = "DELETE FROM tblproductgroupcode WHERE group_code = '".$group_code."' ";
	mysql_query($sql,get_db_conn());
	$sql = "UPDATE tblmember SET group_code='' WHERE group_code = '".$group_code."' ";
	mysql_query($sql,get_db_conn());
	if($reg_group==$group_code){
		$sql = "UPDATE tblshopinfo SET group_code=NULL ";
		mysql_query($sql,get_db_conn());
		DeleteCache("tblshopinfo.cache");
	}
	if (file_exists($imagepath."groupimg_".$group_code.".gif")) {
		unlink ($imagepath."groupimg_".$group_code.".gif");
	}

	$sql = "SELECT productcode FROM tblproductgroupcode GROUP BY productcode ";
	$result=mysql_query($sql,get_db_conn());
	while($row=mysql_fetch_object($result)) {
		$group_check_code[]=$row->productcode;
	}
	mysql_free_result($result);

	if(count($group_check_code)>0) {
		$sql = "UPDATE tblproduct SET group_check='N' ";
		$sql.= "WHERE group_check='Y' ";
		$sql.= "AND productcode NOT IN ('".implode("','", $group_check_code)."') ";
		mysql_query($sql,get_db_conn());
	}

	$onload="<script>alert('해당 등급 삭제가 완료되었습니다.');</script>";
	unset($type);
	unset($group_code);
} else if ($type=="imgdel" && strlen($group_code)==4) {
	unlink ($imagepath."groupimg_".$group_code.".gif");
	$onload="<script>alert('해당등급 이미지 삭제가 완료되었습니다.');</script>";
	unset($type);
	unset($group_code);
} else if ($type=="reg_group") {
	$sql = "UPDATE tblshopinfo SET ";
	if(strlen($reg_group)==0) $sql.= "group_code = NULL ";
	else $sql.= "group_code = '".$reg_group."' ";
	mysql_query($sql,get_db_conn());
	$onload="<script>alert('신규 회원 가입시의 회원등급 등록이 완료되었습니다.');</script>";
	unset($type);
	DeleteCache("tblshopinfo.cache");
}

if(strlen($type)==0) $type="insert";


?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
<script language="javascript" type="text/javascript">
var $j = jQuery.noConflict();
</script>
<script language="javascript" type="text/javascript" src="/js/jquery-ui-1.9.2.custom.min.js"></script>
<link rel="stylesheet" href="/css/ui-lightness/jquery-ui-1.9.2.custom.min.css" type="text/css" />
<script language="JavaScript">
function CheckForm(type) {
	if (document.form1.group_name.value.length==0) {
		alert("등급명을 입력하세요");
		document.form1.group_name.focus();
		return;
	}
	if (document.form1.group_type[0].checked==false && document.form1.group_type[1].checked==false && document.form1.group_type[2].checked==false) {
		alert("등급속성을 선택하세요");
		document.form1.group_type[0].focus();
		return;
	}
	if (document.form1.group_type[0].checked==true) {
		if (document.form1.group_addmoney_R.value.length==0 && document.form1.group_salerate_R.value.length==0) {
			alert("추가적립 속성의 추가적립금을 입력하세요.");
			document.form1.group_addmoney_R.focus();
			return;
		}
		if (document.form1.group_addmoney_R.value.length!=0 && document.form1.group_salerate_R.value.length!=0) {
			alert("추가적립 방법은 둘중 하나만 입력하세요.");
			document.form1.group_addmoney_R.focus();
			return;
		}
		if(isNaN(document.form1.group_usemoney_R.value)==true){
			alert("숫자만 입력하시기 바랍니다.");
			document.form1.group_usemoney_R.focus();
			return;
		}
		if ((document.form1.group_addmoney_R.value.length!=0 && (isNaN(document.form1.group_addmoney_R.value)==true))
		   || (document.form1.group_salerate_R.value.length!=0 && (isNaN(document.form1.group_salerate_R.value)==true))) {
			alert("숫자만 입력하시기 바랍니다.");
			document.form1.group_addmoney_R.focus();
			return;
		}
	}
	if (document.form1.group_type[1].checked==true) {
		if (document.form1.group_addmoney_S.value.length==0 && document.form1.group_salerate_S.value.length==0) {
			alert("추가할인 속성의 추가할인 금액을 입력하세요.");
			document.form1.group_addmoney_S.focus();
			return;
		}
		if (document.form1.group_addmoney_S.value.length>1 && document.form1.group_salerate_S.value.length>1) {
			alert("추가할인 방법은 둘중 하나만 선택하세요.");
			document.form1.group_addmoney_S.focus();
			return;
		}
		if (isNaN(document.form1.iossreserve.value)==true) {
			alert("숫자만 입력하시기 바랍니다.");
			document.form1.iossreserve.focus();
			return;
		}
		if (isNaN(document.form1.group_usemoney_S.value)==true) {
			alert("숫자만 입력하시기 바랍니다.");
			document.form1.group_usemoney_S.focus();
			return;
		}
		if ((document.form1.group_addmoney_S.value.length!=0 && (isNaN(document.form1.group_addmoney_S.value)==true))
		   || (document.form1.group_salerate_S.value.length!=0 && (isNaN(document.form1.group_salerate_S.value)==true))) {
			alert("숫자만 입력하시기 바랍니다.");
			document.form1.group_addmoney_S.focus();
			return;
		}
	}
	if(type=="modify") {
		document.form1.mode.value="result";
	}
	document.form1.type.value=type;
	document.form1.submit();
}

var groupType = '';
function ChangeGroupType(val){
	arr_type = new Array("R","S","M");

	if(val == groupType){
		for(i=0;i<document.form1.group_type.length;i++){
			document.form1.group_type[i].checked=false;
			if(document.form1.group_type[i].value!="M"){
				document.form1["group_usemoney_"+arr_type[i]].disabled=false;
				document.form1["group_usemoney_"+arr_type[i]].style.background='#FFFFFF';
				document.form1["group_addmoney_"+arr_type[i]].disabled=false;
				document.form1["group_addmoney_"+arr_type[i]].style.background='#FFFFFF';
				document.form1["group_salerate_"+arr_type[i]].disabled=false;
				document.form1["group_salerate_"+arr_type[i]].style.background='#FFFFFF';
			}
		}
		groupType ='';
	}else{

		for(i=0;i<document.form1.group_type.length;i++){
			if (document.form1.group_type[i].value==val){
				groupType = val;
				document.form1.group_type[i].checked=true;
				if (document.form1.group_type[i].value!="M") {
					document.form1["group_usemoney_"+arr_type[i]].disabled=false;
					document.form1["group_usemoney_"+arr_type[i]].style.background='#FFFFFF';
					document.form1["group_addmoney_"+arr_type[i]].disabled=false;
					document.form1["group_addmoney_"+arr_type[i]].style.background='#FFFFFF';
					document.form1["group_salerate_"+arr_type[i]].disabled=false;
					document.form1["group_salerate_"+arr_type[i]].style.background='#FFFFFF';
				}
			} else {
				document.form1.group_type[i].checked=false;
				if (document.form1.group_type[i].value!="M") {
					document.form1["group_usemoney_"+arr_type[i]].disabled=true;
					document.form1["group_usemoney_"+arr_type[i]].style.background='#EFEFEF';
					document.form1["group_addmoney_"+arr_type[i]].disabled=true;
					document.form1["group_addmoney_"+arr_type[i]].style.background='#EFEFEF';
					document.form1["group_salerate_"+arr_type[i]].disabled=true;
					document.form1["group_salerate_"+arr_type[i]].style.background='#EFEFEF';
				}
			}
		}
	}
}


function GroupSend(type,code) {
	if (type=="delete") {
		if (!confirm("해당 등급을 삭제하시겠습니까?")) {
			return;
		}
	}
	if (type=="imgdel") {
		if (!confirm("해당 등급 이미지를 삭제하시겠습니까?")) {
			return;
		}
	}
	document.form2.type.value=type;
	document.form2.group_code.value=code;
	document.form2.submit();
}
</script>




<script type="text/javascript">
	// <![CDATA[
		var xmlhttp = false;
		xmlhttp = new XMLHttpRequest ();
		//xmlhttp.overrideMimeType ('text/xml');

		function couponListReload ( groupCode ) {
			var url = 'member_groupnew_couponList.php?groupCode=' + groupCode;
			document.getElementById('loadingIMG').style.display='block';
			xmlhttp.open('POST', url, true);
			xmlhttp.onreadystatechange = couponAreaView;
			xmlhttp.send(null);
		}

		function couponAreaView () {
			if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
				document.getElementById('couponArea').innerHTML = xmlhttp.responseText;
				document.getElementById('loadingIMG').style.display='none';
			}
		}


		var xmlhttp2 = false;
		xmlhttp2 = new XMLHttpRequest ();
		//xmlhttp.overrideMimeType ('text/xml');
		var GlobalgroupCode = '';

		function choiceCoupon ( groupCode, couponCode, checkeds ) {
			GlobalgroupCode = groupCode;
			var url = 'member_groupnew_couponAddPop_process.php?groupCode=' + groupCode + '&couponCode=' + couponCode + '&chk=' + checkeds;
			xmlhttp2.open('POST', url, true);
			xmlhttp2.onreadystatechange = getCHK;
			xmlhttp2.send(null);
		}

		function getCHK () {
			if ( xmlhttp2.readyState == 4 && xmlhttp2.status == 200 ) {
				//document.getElementById('chkMsg').innerHTML = xmlhttp2.responseText;
				couponListReload(GlobalgroupCode);
			}
		}



		// 회원등급 자동승급 수동실행
		function groupaoutoPop () {
			if( confirm("회원등급 자동승급을 수동실행 하시겠습니까?") ) {
				window.open('groupauto.php','groupauto','width=300,height=200,scrollbars=no');
			}
		}

	//]]>
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
			<? include ("menu_member.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 회원관리 &gt; 회원등급설정 &gt; <span class="2depth_select">회원등급 등록/수정/삭제</span></td>
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



<?
$asql = "select * from extra_conf where type='autogroup'";
if(false !== $ares = mysql_query($asql,get_db_conn())){
	$auto = array();
	while($trow = mysql_fetch_assoc($ares)){
		$auto[$trow['name']] = $trow['value'];
	}
}
?>




			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/member_groupnew_title.gif" ALT=""></TD>
</tr>
<tr>
					<TD width="100%" background="images/title_bg.gif"></TD>
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
					<TD width="100%" class="notice_blue">회원등급 신규등록/수정/삭제를 하실 수 있으며 등급별 권한설정이 가능합니다.</TD>
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


			<tr><td height=20></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/member_groupnew_stitle_auto.gif" ALT="자동승급설정"></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td>
				<span>
					<ul style="list-style:none; margin:6px 0px; padding-left:20px;">
						<? if(_array($auto) && $auto['runtype'] !='cron'){ ?>
						<li class="notice_blue"><img src="images/groupaoutoUp.jpg" alt="회원등급 자동승급 수동실행" style="cursor:pointer;" onclick="groupaoutoPop();"></li>						
						<? } ?>
						<li class="notice_blue">1) <span class="font_orange" style="font-size:11px;">자동 승급을 위해서는 선택하신 승급 실행 방식에 따라 서버단의 스케줄링 프로그램을 이용하거나 또는, 관리자가 직접 페이지를 호출 하는 방식을 통해 <b>/admin/groupauto.php</b> 파일을 지정일에 호출해 주셔야 합니다.</span></li>
						<li class="notice_blue">2) 1개의 항목이라도 공백으로 두시면 사용이 해제 됩니다.</li>
						<li class="notice_blue">3) 합산시작일과 합산반영일이 30을 넘거나 숫자이외의 문자로 입력되면 1 로 저장됩니다.</li>
						<li class="notice_blue">4) 합산기간은 1~12 의 범위를 벗어날 경우 12 로 입력 됩니다.</li>
						<li class="notice_blue">5) 누적금액 비교는 등급설정상의 누적 급액의 값보다 큰경우 해당 등급으로 승급됩니다.</li>
					</ul>
				</span>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR><TD colspan=2 background="images/table_top_line.gif"></TD></TR>

				<form name="autoGrade" method="post" action="<?=$_SERVER['PHP_SELF']?>">
				<input type="hidden" name="mode" value="autoset" />
				<tr>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">자동승급 설정</TD>
					<TD class="td_con1">
						
						매 <input type="text" name="rangestart" value="<?=$auto['rangestart']?>" style="width:40px;" class="input" />일(합산시작일) 부터
						<input type="text" name="rangemonth" value="<?=$auto['rangemonth']?>" style="width:40px;" class="input" />개월(합산기간) 간의
						누적구매금액 및 구매건수를 합산하여 조건 충족 시 자동등급변경 처리 하며,
						변경등급 유지기간은 <input type="text" name="keepclass" value="<?=$auto['keepclass']?>" style="width:40px;" class="input" /> 개월간입니다.
						<br />
						등급변경일은 이전변경등급 유지 기간만료 익월 <input type="text" name="upday" value="<?=$auto['upday']?>" style="width:40px;" class="input" />일 입니다. (변경등급 합산반영일)						
						<br />
						<br />
						<div style="border:1px solid #efefef">
						<span style="font-weight:bold">승급 실행 방식</span><br />
						<input type="radio" name="runtype" value="cron" <?=($auto['runtype']=='cron')?'checked':''?> /><span style="font-weight:bold">서버 스케줄러 자동실행</span>( 서버에서 cron 설정을 통해 /admin/groupauto.php 가 자동 실행 되도록 서버 관리자 등에게 요청 하셔서 설정하셔야 합니다.)<br />
						<input type="radio" name="runtype" value="user" <?=($auto['runtype']!='cron')?'checked':''?> /><span style="font-weight:bold">관리자 수동 페이지 호출</span>(서버단 설정이 어려운 호스팅사용자 등의 경우 승급실행 일에 위의 "회원 자동승급 수동 실행"버튼을 설정해 주셔야 합니다.- 버튼은 기능 설정후 표시 됩니다.)
						</div>
						<br /><br />

						1일 부터 6개월 누적금액기준설정하고, 변경등급 유지기간은 6개월간 설정, 등급변경 1일 일 경우.
						<br />
						=>
						매 <strong>1</strong>일(합산시작일) 부터 <strong>6</strong>개월(합산기간) 간의 누적구매금액 및 구매건수를 합산하여 조건 충족 시 자동등급변경 처리 하며, 변경등급 유지기간은 <strong>6</strong>개월간입니다.
						<br />
						등급변경일은 이전변경등급 유지 기간만료 익월 <strong>1</strong>일 입니다. (변경등급 합산반영일)
					</td>
				</tr>
				<TR><TD colspan="2" background="images/table_top_line.gif"></TD></TR>
				<tr><td colspan="2" align="center" style="padding-top:10px;"><input type="image" src="images/botteon_save.gif" value="적용" /></td></tr>
				</form>
				</table>
				</td>
			</tr>

			<tr><td height=20></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/member_groupnew_stitle1.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
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
					<TD width="100%" class="notice_blue">신규회원가입시 선택된 등급으로 자동 가입됩니다.</TD>
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
			<tr><td height=3></td></tr>
			<form name=form3 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type value="reg_group">
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">신규회원 가입시</TD>
					<TD class="td_con1" ><select name=reg_group style="width:350px" class="select">
						<option value="">선택등급 없음
<?
						$sql = "SELECT group_code,group_name FROM tblmembergroup ";
						$result = mysql_query($sql,get_db_conn());
						while($row = mysql_fetch_object($result)){
							echo "<option value=\"".$row->group_code."\"";
							if($reg_group==$row->group_code) echo " selected";
							echo ">".$row->group_name."</option>\n";
						}
?>
						</select> 에 자동으로 가입됩니다.
					</TD>
				</TR>
				<TR><TD colspan=2 background="images/table_top_line.gif"></TD></TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td align=center><a href="javascript:document.form3.submit()"><img src="images/botteon_save.gif" width="113" height="38" border="0" vspace="5"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/member_groupnew_stitle2.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"><TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD background="images/table_top_line.gif" colspan="10" height=1></TD>
				</TR>
				<TR align=center>
					<TD class="table_cell">No</TD>
					<TD class="table_cell1">IMG</TD>
					<TD class="table_cell1">등급명</TD>
					<TD class="table_cell1" width="30%">등급설명</TD>
					<TD class="table_cell1">추가적립&할인</TD>
					<TD class="table_cell1">혜택및제한</TD>
					<TD class="table_cell1">배송비</TD>
					<TD class="table_cell1">회원수</TD>
					<TD class="table_cell1">수정</TD>
					<TD class="table_cell1">삭제</TD>
				</TR>
				<TR>
					<TD colspan="10" background="images/table_con_line.gif"></TD>
				</TR>
<?
				$sql = "SELECT COUNT(*) as cnt, group_code FROM tblmember ";
				$sql.= "WHERE group_code != '' GROUP BY group_code ";
				$result=mysql_query($sql,get_db_conn());
				while($row=mysql_fetch_object($result)) {
					$cnt[$row->group_code]=$row->cnt;
				}
				mysql_free_result($result);

				$sql = "SELECT * FROM tblmembergroup ";
				$result = mysql_query($sql,get_db_conn());
				$i=0;
				while($row=mysql_fetch_object($result)) {
					$i++;
					$group_type=substr($row->group_code,0,1);
					$group_view=substr($row->group_code,1,1);
					$group_carr_free_sec = 	$group_carr_free_won = '';
					$tmpss = explode("|",$row->group_carr_free);
					$group_carr_free_sec = $tmpss[0];
					$group_carr_free_won = $tmpss[1];
					$group_carr_free_won_ex = $tmpss[2];
					?>


					<tr>
						<TD align=center class="td_con2"><?=$i?></td>
						<TD align=center class="td_con1">
							<? if(file_exists($imagepath."groupimg_".$row->group_code.".gif")){?><img src="<?=$imagepath?>groupimg_<?=$row->group_code?>.gif" align=absmiddle>
							<? }else{ ?> - <? } ?>
						<TD align=center class="td_con1">
							<span class="font_orange"><b><?=$row->group_name?></b></span>
						</TD>
						<TD align=center class="td_con1" width="30%"><NOBR><?=$row->group_description?></NOBR></TD>
						<TD align=center class="td_con1" style="text-align:left; padding-left:5px;">
							추가적립 : <span style="font-weight:bold" class="font_orange"><?
							if($group_type=="R") echo ((intval($row->group_usemoney) >0)?number_format($row->group_usemoney).'원 이상 구매시':'').number_format($row->group_addmoney).($group_view=="P"?"%":"원");
							else echo 'X'; ?></span><br>
							추가할인 : <span style="font-weight:bold" class="font_orange"><?
							if($group_type=="S") echo ((intval($row->group_usemoney) >0)?number_format($row->group_usemoney).'원 이상 구매시':'').number_format($row->group_addmoney).($group_view=="P"?"%":"원");
							else echo 'X'; ?></span>
							</TD>
						<TD align=center class="td_con1" style="text-align:left; padding-left:5px;">
							구매사은품 : <?=($row->group_apply_gift == 'Y'?'<span style="color:blue">지급</span>':'<span style="color:red">지급불가</span>')?><br />
							할인&nbsp;&nbsp;&nbsp;쿠폰 : <?=($row->group_apply_coupon == 'Y'?'<span style="color:blue">적용</span>':'<span style="color:red">적용불가</span>')?><br />
							적립금사용 : <?=($row->group_apply_use_reserve == 'Y'?'<span style="color:blue">사용가능</span>':'<span style="color:red">사용불가</span>')?><br />
							상품적립금 : <?=($row->group_apply_reserve == 'Y'?'<span style="color:blue">지급</span>':'<span style="color:red">지급불가</span>')?><br />

						</TD>
						<TD align=center class="td_con1">
							<? 	if($group_carr_free_sec == '1') echo '기본배송비';
							  	else if($group_carr_free_sec == '2') echo '무료';
							    else if($group_carr_free_sec == '3') echo '무료';
								if(intval($row->group_carr_free_won) >0) echo number_format($row->group_carr_free_won).'원 이상 구매시 무료배송';
								?>
						</TD>
						<TD align=center class="td_con1"><?=number_format($cnt[$row->group_code])?>명</td>
						<TD align=center class="td_con1"><a href="javascript:GroupSend('modify','<?=$row->group_code?>');"><img src="images/btn_edit.gif" width="50" height="22" border="0"></a></td>
						<TD align=center class="td_con1"><a href="javascript:GroupSend('delete','<?=$row->group_code?>');"><img src="images/btn_del.gif" width="50" height="22" border="0"></a></td>
					</tr>
					<tr>
						<TD colspan="10" background="images/table_con_line.gif"></TD>
					</tr>
					<?
					unset($group_type);
					unset($group_view);
				}
				mysql_free_result($result);
				if ($i==0) {
					echo "<tr><td class=\"td_con2\" colspan=\"8\" align=\"center\">등록된 회원등급이 없습니다.</td></tr>";
				}
?>
				<TR>
					<TD background="images/table_top_line.gif" colspan="10" height=1></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="36"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/member_groupnew_stitle3.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif"></TD>
					<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=3></td></tr>
<?
			if($type=="modify" && strlen($group_code)==4) {
				$sql = "SELECT * FROM tblmembergroup WHERE group_code = '".$group_code."' ";
				$result = mysql_query($sql,get_db_conn());
				if($row=mysql_fetch_object($result)) {
					$group_name=$row->group_name;
					$group_description=$row->group_description;
					$group_payment=$row->group_payment;
					$group_type=substr($row->group_code,0,1);
					$group_view=substr($row->group_code,1,1);
					$group_usemoney=$row->group_usemoney;
					$group_addmoney=$row->group_addmoney;
					$group_apply_gift=$row->group_apply_gift;
					$group_apply_coupon=$row->group_apply_coupon;
					$group_apply_reserve=$row->group_apply_reserve;
					$group_apply_use_reserve=$row->group_apply_use_reserve;
					//$group_card_commi=$row->group_card_commi;
					$group_carr_free=$row->group_carr_free;
					$tmpss = explode("|",$group_carr_free);
					$group_carr_free_sec = $tmpss[0];
					$group_carr_free_won = $tmpss[1];
					$group_carr_free_won_ex = $tmpss[2];
					$group_order_price=$row->group_order_price;
					$group_order_cnt=$row->group_order_cnt;
					//$group_order_type=$row->group_order_type;
					$group_seller=$row->group_seller;
					$group_iossreserve=$row->group_iossreserve;
					$group_excp_auto=$row->group_excp_auto;

				}
				mysql_free_result($result);
			} else {
				unset($group_name);
				unset($group_description);
				$group_payment = "N";
				unset($group_type);
				unset($group_view);
				unset($group_usemoney);
				unset($group_addmoney);
				$group_apply_gift = "Y";
				$group_apply_coupon = "Y";
				$group_apply_reserve = "Y";
				$group_apply_use_reserve = "Y";
				//$group_card_commi = 0;
				unset($group_carr_free);
				$group_carr_free_sec = 1;
				unset($group_carr_free_won);
				unset($group_order_price);
				unset($group_order_cnt);
				//$group_order_type = 3;
				$group_seller = "N";
				$group_iossreserve=0;
				$group_excp_auto='N';

			}









			// 누적 금액/구매건수 적용 조건
			if( strlen($_POST["group_order_type"]) > 0 ) {
				$group_order_type = $_POST["group_order_type"];

				$group_order_type_sql = "select count(*) from extra_conf where type='groupconf' AND `name` = 'group_order_type' ";
				if(false !== $group_order_type_res = mysql_query($group_order_type_sql,get_db_conn())){
					if(mysql_result($group_order_type_res,0,0) > 0){
						$sql = "update extra_conf set value ='".$group_order_type."' where type='groupconf' and name='group_order_type' ";
					}else{
						$sql = "insert into extra_conf set type='groupconf',name='group_order_type',value ='".$group_order_type."' ";
					}
					mysql_query($sql,get_db_conn());
				}
			}

			$group_order_type_sql = "select value from extra_conf where type='groupconf' AND `name` = 'group_order_type' ";
			$group_order_type_res = mysql_query($group_order_type_sql,get_db_conn());
			$group_order_type_row = mysql_fetch_assoc($group_order_type_res);
			$group_order_type = $group_order_type_row['value'];
			$group_order_type_sel  = array();
			$group_order_type_sel[$group_order_type] = "checked";









?>
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=mode>
			<input type=hidden name=group_code value="<?=$group_code?>">
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=139>
				<col width=>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">등급명</TD>
					<TD class="td_con1"><input type=text name=group_name value="<?=$group_name?>" maxlength=30 style="width:200px;" class="input"></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">등급설명</TD>
					<TD class="td_con1"><input type=text name=group_description value="<?=$group_description?>" maxlength=100 style="width:450" class="input">120자 이내</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">자동승급제외여부</TD>
					<TD class="td_con1"><input type="radio" name="group_excp_auto" value="N" id="group_excp_auto1" <?if($group_excp_auto !="Y")echo"checked";?> ><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=group_excp_auto1>자동승급연동</span></label> &nbsp;&nbsp;
						<input type="radio" name="group_excp_auto" value="Y" id="group_excp_auto2"  <?if($group_excp_auto=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=group_excp_auto2>자동승급대상에서제외</span></label></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">결제조건</TD>
					<TD class="td_con1">
					<input type=radio id="idx_group_payment1" name=group_payment value="N" <?if($group_payment=="N")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_group_payment1>현금/카드</label> &nbsp;&nbsp;&nbsp;
					<input type=radio id="idx_group_payment2" name=group_payment value="B" <?if($group_payment=="B")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_group_payment2>현금결제만</label> &nbsp;&nbsp;&nbsp;
					<input type=radio id="idx_group_payment3" name=group_payment value="C" <?if($group_payment=="C")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_group_payment3>카드결제만</label></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">등급속성</TD>
					<TD class="td_con1">
					<table cellpadding="0" cellspacing="0" width="99%">
					<col width=87></col>
					<col width=></col>
					<tr>
						<td><input type=checkbox id="idx_group_type1" name=group_type value="R" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?if($group_type=="R") echo "checked"?> onclick="ChangeGroupType(this.value)"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_group_type1><span class="font_orange"><B>추가적립</B></span></label></td>
						<td>회원이 <input type=text name=group_usemoney_R size=8 maxlength=8 value="<?if($group_type=="R") echo $group_usemoney?>" style="text-align:right" class="input">원 이상 구매시, 주문금액에서 <input type=text name=group_addmoney_R size=8 maxlength=8 value="<?if($group_type=="R" && $group_view=="W") echo $group_addmoney?>" style="text-align:right" class="input"><B><span class="font_orange">원</span> </B>또는 <input type=text name=group_salerate_R size=8 maxlength=8 value="<?if($group_type=="R" && $group_view=="P") echo $group_addmoney?>" style="text-align:right" class="input"><B><span class="font_orange">%</span></B>를 추가 적립합니다.</td>
					</tr>
					<tr>
						<td><input type=checkbox id="idx_group_type2" name=group_type value="S" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?if($group_type=="S") echo "checked"?> onclick="ChangeGroupType(this.value)"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_group_type2><span class="font_orange"><B>추가할인</B></span></label></td>
						<td>회원이 <input type=text name=group_usemoney_S size=8 maxlength=8 value="<?if($group_type=="S") echo $group_usemoney?>" style="text-align:right" class="input">원 이상 구매시, 주문금액에서 <input type=text name=group_addmoney_S size=8 maxlength=8 value="<?if($group_type=="S" && $group_view=="W") echo $group_addmoney?>" style="text-align:right" class="input"><B><span class="font_orange">원</span> </B>또는 <input type=text name=group_salerate_S size=8 maxlength=8 value="<?if($group_type=="S" && $group_view=="P") echo $group_addmoney?>" style="text-align:right" class="input"><B><span class="font_orange">%</span></B>를 추가 할인합니다.</td>
					</tr>
					</table>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">구매사은품</TD>
					<TD class="td_con1">
						<input type="radio" name="group_apply_gift" value="Y" id="group_gift1" <?if($group_apply_gift=="Y")echo"checked";?> ><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=group_gift1>지급</span></label> &nbsp;&nbsp;
						<input type="radio" name="group_apply_gift" value="N" id="group_gift2"  <?if($group_apply_gift=="N")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=group_gift2>지급불가</span></label>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">할인쿠폰</TD>
					<TD class="td_con1">
						<input type="radio" name="group_apply_coupon" value="Y" id="group_coupon1" <?if($group_apply_coupon=="Y")echo"checked";?> ><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=group_coupon1>적용</span></label> &nbsp;&nbsp;
						<input type="radio" name="group_apply_coupon" value="N" id="group_coupon2"  <?if($group_apply_coupon=="N")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=group_coupon2>적용불가</span></label>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>







				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">보유적립금사용</TD>
					<TD class="td_con1">
						<input type="radio" name="group_apply_use_reserve" value="Y" id="group_use_reserve1" <?if($group_apply_use_reserve=="Y")echo"checked";?> ><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=group_use_reserve1>사용가능</span></label> &nbsp;&nbsp;
						<input type="radio" name="group_apply_use_reserve" value="N" id="group_use_reserve2"  <?if($group_apply_use_reserve=="N")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=group_use_reserve2>사용불가</span></label>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품적립금</TD>
					<TD class="td_con1">
						<input type="radio" name="group_apply_reserve" value="Y" id="group_reserve1" <?if($group_apply_reserve=="Y")echo"checked";?> ><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=group_reserve1>지급</span></label> &nbsp;&nbsp;
						<input type="radio" name="group_apply_reserve" value="N" id="group_reserve2"  <?if($group_apply_reserve=="N")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=group_reserve2>지급불가</span></label>
					</TD>
				</TR>
				<?
					/*

				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">카드수수료율부과</TD>
					<TD class="td_con1">
						<input type=text name=group_card_commi value="<?=$group_card_commi?>" maxlength=10 style="width:30px;" class="input">% (0%시 면제) <span class="font_orange">* 현금구매와 카드결제에 대한 차별은 금지되어 있으며 법적 책임이 발생할 수 있습니다.</span>
					</TD>
				</TR>
				*/
				?>

				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">기본배송비금액설정</TD>
					<TD class="td_con1">
						<input type="radio" name="group_carr_free_sec" value="1" id="group_carr_free_sec1" <?if($group_carr_free_sec=="1")echo"checked";?> ><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=group_carr_free_sec1>기본배송비정책에따름</span></label>[<a href='/admin/shop_deli.php' target='shop_deli'>!</a>] &nbsp;&nbsp;
						<input type="radio" name="group_carr_free_sec" value="2" id="group_carr_free_sec2"  <?if($group_carr_free_sec=="2")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=group_carr_free_sec2>무료</span></label> &nbsp;&nbsp;
						<input type="radio" name="group_carr_free_sec" value="3" id="group_carr_free_sec3"  <?if($group_carr_free_sec=="3")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=group_carr_free_sec3>유료</span></label>
						<input type=text name=group_carr_free_won value="<?=$group_carr_free_won?>" maxlength=10 style="width:50px;" class="input">원 이상 구매시 무료배송
						( 본사 합산가격에 배송비(개별/무료/착불)상품 가격을 [<input type="radio" name="group_carr_free_won_ex" value="Y" <?=($group_carr_free_won_ex=="Y")?"checked":""?>>포함/<input type="radio" name="group_carr_free_won_ex" value="N" <?=($group_carr_free_won_ex=="N" OR empty($group_carr_free_won_ex))?"checked":""?>>미포함] 합니다.
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">누적 금액/구매건수 </TD>
					<TD class="td_con1">

						누적금액 : <input type=text name=group_order_price value="<?=$group_order_price?>" maxlength=10 style="width:80px;" class="input">원<br />
						누적구매 : <input type=text name=group_order_cnt value="<?=$group_order_cnt?>" maxlength=10 style="width:80px;" class="input">건<br />


						<?
							if( strlen($group_code) > 0 ){
						?>
						<DIV style="padding:10px;">
							<FIELDSET style="border-color:#000000;padding:5px;">
								<LEGEND><strong>누적 금액/구매건수 적용 조건</strong></LEGEND>

								<input type="radio" name="group_order_type" value="1" id="group_order_type1" <?=$group_order_type_sel[1]?>>
								<label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="group_order_type1">누적금액 만 충족 시 (누적금액이 0일경우 자동승급레벨에서 제외)</label>

								<br />
								<input type="radio" name="group_order_type" value="2" id="group_order_type2" <?=$group_order_type_sel[2]?>>
								<label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="group_order_type2">누적구매건수 만 충족 시 (누적구매가 0일경우 자동승급레벨에서 제외)</label>

								<br />
								<input type="radio" name="group_order_type" value="3" id="group_order_type3" <?=$group_order_type_sel[3]?>>
								<label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="group_order_type3">누적 구매금액과 누적구매건수가 모두 충족 시 (둘중 하나라도 0일경우 자동승급레벨에서 제외)</label>


								<!--
								<br />
								<input type="radio" name="group_order_type" value="4" id="group_order_type4" <?=$group_order_type_sel[4]?>>
								<label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="group_order_type4">누적 구매금액과 누적구매건수가 하나라도 충족 시</label>
								-->

								<span class="font_orange">
									<br /><strong>* 그룹전체에 적용되는 옵션입니다. 적용시 타 그룹에도 변경됩니다.</strong>
								</span>

							</FIELDSET>
						</DIV>
						<?
							}
						?>

					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>



				<!-- 승급시 자동 지급 적립금 -->
				<TR>
					<td class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">승급시 지급 적립금</td>
					<td class="td_con1">
						<input type="text" name="iossreserve" value="<?=$group_iossreserve?>" style="width:200px;" class="input"/><br/>
						*숫자만 입력하세요
					</td>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<!-- //승급시 자동 지급 적립금 -->





				<?
					if( strlen($group_code) > 0 ){
				?>

				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0" onload="couponListReload('<?=$group_code?>');">자동발급쿠폰 등록</TD>
					<TD class="td_con1" style="padding-left:0px; padding-right:0px;">
						<div style="height:40px;">
							<div style="float:left; margin:7px 4px;">
								<input type="radio" name="use_auto_coupon" value="0" <?=($row->use_auto_coupon == '0')?'checked="checked"':''?> />미사용&nbsp;
								<input type="radio" name="use_auto_coupon" value="1" <?=($row->use_auto_coupon != '0')?'checked="checked"':''?> />사용
							</div>
							<div style="float:left; margin:7px;"><img src="images/btn_coupon_insert.gif" border="0" style="cursor:pointer;" alt="쿠폰등록" class="addAutoCoupon" /></div>
							<div style="float:left; margin:7px 0px;"><img src="images/btn_refresh.gif" border="0" style="cursor:pointer;" alt="새로고침" onclick="couponListReload('<?=$group_code?>');" class="addAutoCouponRe" /></div>
						</div>
						<DIV id="loadingIMG" style="display:none;"><img src="/images/loading.gif"><br /><B>로딩중.....</B></DIV>
						<div id="couponArea" style="width:100%;"></div>

						<div id="couponOptArea" style="width:100%;">
							<input type="radio" name="groupCouponSendType" id="groupCouponSendType1" value="1" <?=($row->groupCouponSendType == '1' OR empty($row->groupCouponSendType))?'checked="checked"':''?> onclick="groupCouponSendType_optChk(this.checked,1);" />
								<label style='cursor:hand;TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=groupCouponSendType1>
									승급 시 최초 1회 자동쿠폰 발급등록 (상위 등급에서 등급하향 조정 후 기존 등급으로 상향승급 시는 발급안됨)
								</label>
							<BR />
							<input type="radio" name="groupCouponSendType" id="groupCouponSendType2" value="2" <?=($row->groupCouponSendType == '2')?'checked="checked"':''?> onclick="groupCouponSendType_optChk(this.checked,2);" />
								<label style='cursor:hand;TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=groupCouponSendType2>
									기간별 자동 쿠폰 발급설정
								</label>
							<BR />
							<DIV id="groupCouponSendType2_opt" style="display:<?=($row->groupCouponSendType == '2')?'block':'none'?>;">
								매 <input type="text" name="groupCouponSendType_M" value="<?=$row->groupCouponSendType_M?>" style="width:40px;" class="input" />개월마다 해당월 자동승급설정반영일<? /* <input type="text" name="groupCouponSendType_D" value="<?=$row->groupCouponSendType_D?>" style="width:40px;" class="input" /> */ ?>일에 자동 발급<BR />
								* 1개월의 경우 "1"만입력, "01"입력불가, 해당월은 "1"일에서 "28"일까지 등록가능<BR />
								* 등급산정기간과 맞게 쿠폰사용기간이 자동설정되어 발급됩니다.<BR />
								&nbsp;&nbsp;발급된 쿠폰적용기간 애 미사용 시 중복쿠폰으로 사용불가.
							</DIV>
						</div>

						<script language="javascript" type="text/javascript">

							// 쿠폰 등록 / 수정
							$j(function(){
								$j('input[name=group_apply_coupon]').on('change',toggleAuthCoupon);
								$j('input[name=use_auto_coupon]').on('change',toggleAuthCoupon);
								$j('.addAutoCoupon').on('click',function(){
									window.open('member_groupnew_couponAddPop.php?grp=<?=$group_code?>','addAutoCouponPop','width=1000,height=500,scrollbars=yes'); });
								toggleAuthCoupon();
							});

							function toggleAuthCoupon(){
								var mact = $j('input[name=group_apply_coupon]:checked').val() == 'Y';
								var act = false;
								if(mact){
									$j('input[name=use_auto_coupon]').removeAttr('disabled');
									act = $j('input[name=use_auto_coupon]:checked').val() == '1';
								}else{
									$j('input[name=use_auto_coupon]').attr('disabled','disabled');
								}
								if(act){
									$j('.addAutoCoupon').css('display','');
									$j('.addAutoCouponRe').css('display','');
									$j('#couponArea').css('display','');
									$j('#couponOptArea').css('display','');
								}else{
									$j('.addAutoCoupon').css('display','none');
									$j('.addAutoCouponRe').css('display','none');
									$j('#couponArea').css('display','none');
									$j('#couponOptArea').css('display','none');
								}
							}


							// 쿠폰발급 방식
							function groupCouponSendType_optChk ( t, o ) {
								groupCouponSendType2_opt.style.display=(t==true && o==2)?'block':'none';
							}
						</script>

					</TD>
				</TR>
				<?
					}
				?>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>








				<? /* 도매 회원 별도 관리로 인해 해당 칼럼은 사용 하지 않음
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">도매가적용여부</TD>
					<TD class="td_con1">
						<input type="radio" name="group_seller" value="Y" id="group_seller1" <?if($group_seller=="Y")echo"checked";?> ><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=group_seller1>적용</span></label> &nbsp;&nbsp;
						<input type="radio" name="group_seller" value="N" id="group_seller2"  <?if($group_seller=="N")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=group_seller2>미적용</span></label>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				*/ ?>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">메일링</TD>
					<TD class="td_con1"><input type=checkbox id="idx_group_type3" name=group_type value="M" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?if($group_type=="M") echo "checked"?> onclick="ChangeGroupType(this.value)"> <label style='cursor:hand;TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_group_type3>등급속성없이, 해당등급에 <B><span class="font_orange">단순 메일링(SMS)</span></B>만 합니다.</LABEL></TD>
				</TR>
				<TR><TD colspan="2" background="images/table_con_line.gif"></TD></TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">등급이미지</TD>
					<TD class="td_con1">
						<input type=file name=groupimg style="width:445px;" class="input"><br />
						* 권장크기 : 80*40 픽셀 [가로*세로])<br />
						<span class="font_orange">* 150KB 이하의 GIF(gif)이미지만 가능합니다.</span>
						<? if(file_exists($imagepath."groupimg_".$group_code.".gif")){?>
						<BR><BR><img src="<?=$imagepath?>groupimg_<?=$group_code?>.gif" align=absmiddle>&nbsp;&nbsp; | &nbsp;&nbsp;<A HREF="javascript:GroupSend('imgdel','<?=$group_code?>');"><img src="images/icon_del1.gif" border=0 align=absmiddle></A>
						<?}?>
					</TD>
				</TR>
				<TR><TD colspan=2 background="images/table_top_line.gif"></TD></TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<?if($type=="insert"){?>
			<tr><td align=center><a href="javascript:CheckForm('<?=$type?>');"><img src="images/botteon_make.gif" width="113" height="38" border="0" vspace="3"></a></td></tr>
			<?}else if($type=="modify"){?>
			<tr><td align=center><a href="javascript:CheckForm('<?=$type?>');"><img src="images/botteon_save.gif" width="113" height="38" border="0" vspace="3"></a></td></tr>
			<?}?>
			</form>
			<form name=form2 action="<?=$_SERVER[PHP_SELF]?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=group_code>
			</form>
			<tr><td height="20">&nbsp;</td></tr>
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
					<TD COLSPAN=3 width="100%" valign="top" class=menual_bg style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><p><span class="font_dotline">회원등급 기본정보 관리</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 회원등급 설정은 가격정책 또는 할인율 진행시 용이하게 운영할 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 회원등급에 따라 결제방법을(현금/카드, 현금결제만, 카드결제만) 선택할 수 있습니다. 가급적 현금/카드 결제를 권장합니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 배수로 추가적립 설정시 기본적립금에 배수가 적용됩니다. 예) 3배 추가적립 -  200원의 경우 600원 적립</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- 추가 적립금은 배송완료 후 자동 적립됩니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top">- %로 추가할인 설정시 10원 단위는 자동 절삭됩니다. 예) 4,360원의 경우 4,300원을 할인합니다.</td>
					</tr>
					<tr>
						<td colspan="2" height="20"></td>
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
			<tr><td height="50"></td></tr>
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
<?if($type=="modify") echo "<script>ChangeGroupType('".$group_type."')</script>";?>
<?=$onload?>

<? INCLUDE "copyright.php"; ?>