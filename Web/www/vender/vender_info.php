<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");
include_once($Dir."lib/admin_more.php");

$mode=$_POST["mode"];

// 정산 기준 조회 jdy
$shop_more_info = getShopMoreInfo();
$account_rule = $shop_more_info['account_rule'];
// 정산 기준 조회 jdy

/* 수수료 관련 추가 jdy */
$sql = "SELECT * FROM vender_more_info ";
$sql.= "WHERE vender='".$_VenderInfo->getVidx()."'";
$result=mysql_query($sql,get_db_conn());
$_vmdata=mysql_fetch_object($result);
mysql_free_result($result);
/* 수수료 관련 추가 jdy */

if($mode=="update") {
	$up_com_owner=$_POST["up_com_owner"];
	$up_com_post1=$_POST["up_com_post1"];
	$up_com_post2=$_POST["up_com_post2"];
	$up_com_addr=$_POST["up_com_addr"];
	$up_com_biz=$_POST["up_com_biz"];
	$up_com_item=$_POST["up_com_item"];
	$up_com_tel1=$_POST["up_com_tel1"];
	$up_com_tel2=$_POST["up_com_tel2"];
	$up_com_tel3=$_POST["up_com_tel3"];
	$up_com_fax1=$_POST["up_com_fax1"];
	$up_com_fax2=$_POST["up_com_fax2"];
	$up_com_fax3=$_POST["up_com_fax3"];
	$up_com_homepage=strtolower($_POST["up_com_homepage"]);

	$up_p_name=$_POST["up_p_name"];
	$up_p_mobile1=$_POST["up_p_mobile1"];
	$up_p_mobile2=$_POST["up_p_mobile2"];
	$up_p_mobile3=$_POST["up_p_mobile3"];
	$up_p_email=$_POST["up_p_email"];
	$up_p_buseo=$_POST["up_p_buseo"];
	$up_p_level=$_POST["up_p_level"];

	$up_passwd=$_POST["up_passwd"];

	$up_bank1=$_POST["up_bank1"];
	$up_bank2=$_POST["up_bank2"];
	$up_bank3=$_POST["up_bank3"];

	$up_session=$_POST["up_session"];

	$com_type=$_POST["com_type"];
	$ec_num=$_POST["ec_num"];
	$com_nametech=$_POST["com_nametech"];


	$up_com_post="";
	if(strlen($up_com_post1)==3 && strlen($up_com_post2)==3) {
		$up_com_post=$up_com_post1.$up_com_post2;
	}

	$up_com_tel="";
	$up_com_fax="";
	$up_p_mobile="";
	if(strlen($up_com_tel1)>0 && strlen($up_com_tel2)>0 && strlen($up_com_tel3)>0) {
		if(IsNumeric($up_com_tel1) && IsNumeric($up_com_tel2) && IsNumeric($up_com_tel3)) {
			$up_com_tel=$up_com_tel1."-".$up_com_tel2."-".$up_com_tel3;
		}
	}
	if(strlen($up_com_fax1)>0 && strlen($up_com_fax2)>0 && strlen($up_com_fax3)>0) {
		if(IsNumeric($up_com_fax1) && IsNumeric($up_com_fax2) && IsNumeric($up_com_fax3)) {
			$up_com_fax=$up_com_fax1."-".$up_com_fax2."-".$up_com_fax3;
		}
	}
	if(strlen($up_p_mobile1)>0 && strlen($up_p_mobile2)>0 && strlen($up_p_mobile3)>0) {
		if(IsNumeric($up_p_mobile1) && IsNumeric($up_p_mobile2) && IsNumeric($up_p_mobile3)) {
			$up_p_mobile=$up_p_mobile1."-".$up_p_mobile2."-".$up_p_mobile3;
		}
	}
	if(!ismail($up_p_email)) {
		$up_p_email="";
	}
	$up_com_homepage=str_replace("http://","",$up_com_homepage);

	$bank_account="";
	if(strlen($up_bank1)>0 && strlen($up_bank2)>0 && strlen($up_bank3)>0) {
		$bank_account=$up_bank1."=".$up_bank2."=".$up_bank3;
	}

	if(strlen($up_com_owner)==0) {
		echo "<html></head><body onload=\"alert('대표자 성명을 정확히 입력하세요.')\"></body></html>";exit;
	} else if(strlen($up_com_post)==0 || strlen($up_com_addr)==0) {
		echo "<html></head><body onload=\"alert('사업장 주소를 정확히 입력하세요.')\"></body></html>";exit;
	} else if(strlen($up_com_biz)==0) {
		echo "<html></head><body onload=\"alert('사업자 업태를 정확히 입력하세요.')\"></body></html>";exit;
	} else if(strlen($up_com_item)==0) {
		echo "<html></head><body onload=\"alert('사업자 종목을 정확히 입력하세요.')\"></body></html>";exit;
	} else if(strlen($up_com_tel)==0) {
		echo "<html></head><body onload=\"alert('회사 대표전화를 정확히 입력하세요.')\"></body></html>";exit;
	} else if(strlen($up_p_name)==0) {
		echo "<html></head><body onload=\"alert('담당자명을 정확히 입력하세요.')\"></body></html>";exit;
	} else if(strlen($up_p_mobile)==0) {
		echo "<html></head><body onload=\"alert('담당자 휴대전화를 정확히 입력하세요.')\"></body></html>";exit;
	} else if(strlen($up_p_email)==0) {
		echo "<html></head><body onload=\"alert('담당자 이메일을 정확히 입력하세요.')\"></body></html>";exit;
	}

	$sql = "UPDATE tblvenderinfo SET ";
	if(strlen($up_passwd)>=4) {
		$sql.= "passwd		= '".md5($up_passwd)."', ";
	}
	$sql.= "com_owner		= '".$up_com_owner."', ";
	$sql.= "com_post		= '".$up_com_post."', ";
	$sql.= "com_addr		= '".$up_com_addr."', ";
	$sql.= "com_biz			= '".$up_com_biz."', ";
	$sql.= "com_item		= '".$up_com_item."', ";
	$sql.= "com_tel			= '".$up_com_tel."', ";
	$sql.= "com_fax			= '".$up_com_fax."', ";
	$sql.= "com_homepage	= '".$up_com_homepage."', ";
	$sql.= "p_name			= '".$up_p_name."', ";
	$sql.= "p_mobile		= '".$up_p_mobile."', ";
	$sql.= "p_email			= '".$up_p_email."', ";
	$sql.= "p_buseo			= '".$up_p_buseo."', ";
	$sql.= "p_level			= '".$up_p_level."', ";
	$sql.= "bank_account	= '".$bank_account."', ";
	$sql.= "com_type		= '".$com_type."', ";
	$sql.= "ec_num			= '".$ec_num."', ";
	$sql.= "com_nametech			= '".$com_nametech."' ";


	// 대표이미지 등록
	if( $_FILES['com_image']['error'] == 0 AND $_FILES['com_image']['size'] > 0 AND eregi("image",$_FILES['com_image']['type']) AND $_POST['com_image_del'] != "OK" ) {
		$exte = explode(".",$_FILES['com_image']['name']);
		$exte = $exte[ count($exte)-1 ];
		$com_image_name = "comImgae_".$_VenderInfo->getVidx().".".$exte;
		move_uploaded_file($_FILES['com_image']['tmp_name'],$com_image_url.$com_image_name);
		$sql.= ", com_image = '".$com_image_name."' ";
	}

	//대표이미지 삭제
	if( $_POST['com_image_del'] == "OK" AND strlen($_POST['com_image_del_file']) > 0 ) {
		unlink($_POST['com_image_del_file']);
		$sql.= ", com_image = '' ";
	}

	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
	if(mysql_query($sql,get_db_conn())) {
		if($up_session == "Y") {
			$sql = "DELETE FROM tblvendersession WHERE authkey != '".$_VenderInfo->getAuthkey()."' AND vender = '".$_VenderInfo->getVidx()."' ";
			mysql_query($sql,get_db_conn());
		}

		echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.reload()\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
	}
}
/* 개별 수수료 관련 jdy */
else if ($mode=="com"){

	$rq_commission_type = $_POST["rq_commission_type"];
	$rq_rate = $_POST["rq_rate"];
	$rq_name = $_POST["rq_name"];

	//판매 수수료로 운영시 수수료 변경에대한 기록을 남김 ex) 전체-> 개별

	if ($rq_commission_type!=($_vmdata->commission_type)) {

		if ($_vmdata->commission_type == "1") {
			$up_history = "개별수수료 -> 전체수수료 ".$rq_rate."%로 변경요청 [입점]";
		}else{
			$up_history = "전체수수료 ".$_venderdata->rate."% -> 개별수수료로 변경요청 [입점]";
		}
	}else{

		if ($_vmdata->commission_type != '') {
			if ($rq_commission_type != "1") {

				if ($rq_rate !=$_venderdata->rate) {
					$up_history = "전체수수료 ".$_venderdata->rate."% -> ".$rq_rate."% 로 변경요청 [입점]";
					$updateChk = "1";
				}

			}
		}
	}

	$sql = "UPDATE vender_more_info SET ";
	$sql.= "rq_commission_type	= '".$rq_commission_type."', ";
	$sql.= "rq_rate	= '".$rq_rate."', ";
	$sql.= "commission_status = '1' ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";

	$err =0;
	if (!mysql_query($sql,get_db_conn())) {
		$err++;
	}

	if ($up_history !="") {
		$sql = "insert commission_history set ";
		$sql.= "vender	= '".$_VenderInfo->getVidx()."', ";
		$sql.= "memo	= '".$up_history."', ";
		$sql.= "`type`	= '1', ";
		$sql.= "rq_name	= '".$rq_name."', ";
		$sql.= "reg_date	= now() ";
	}

	if (!mysql_query($sql,get_db_conn())) {
		$err++;
	}

	if($err==0) {
		echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.reload()\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
	}

}
/* 개별 수수료 관련 jdy */

$com_tel=explode("-",$_venderdata->com_tel);
$com_fax=explode("-",$_venderdata->com_fax);
$com_p_mobile=explode("-",$_venderdata->p_mobile);
$bank_account=explode("=",$_venderdata->bank_account);

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function formSubmit() {
	var form = document.form1;
	if (!form.up_com_name.value) {
		form.up_com_name.focus();
		alert("상호(회사명)을 입력하세요.");
		return;
	}
	if(CheckLength(form.up_com_name)>30) {
		form.up_com_name.focus();
		alert("상호(회사명)은 한글15자 영문30자 까지 입력 가능합니다");
		return;
	}
	if (!form.up_com_num.value) {
		form.up_com_num.focus();
		alert("사업자등록번호를 입력하세요.");
		return;
	}

	var bizno;
	var bb;
	bizno = form.up_com_num.value;
	bizno = bizno.replace("-","");
	bb = chkBizNo(bizno);
	if (!bb) {
		alert("인증되지 않은 사업자등록번호 입니다.\n사업자등록번호를 다시 입력하세요.");
		form.up_com_num.value = "";
		form.up_com_num.focus();
		return;
	}
	if (!form.up_com_owner.value) {
		form.up_com_owner.focus();
		alert("대표자 성명을 입력하세요.");
		return;
	}
	if(CheckLength(form.up_com_owner)>12) {
		form.up_com_owner.focus();
		alert("대표자 성명은 한글 6글자까지 가능합니다");
		return;
	}
	if (!form.up_com_post1.value || !form.up_com_post2.value) {
		form.up_com_post1.focus();
		alert("우편번호를 입력하세요.");
		return;
	}
	if (!form.up_com_addr.value) {
		form.up_com_addr.focus();
		alert("사업장 주소를 입력하세요.");
		return;
	}
	if(CheckLength(form.up_com_biz)>30) {
		form.up_com_biz.focus();
		alert("사업자 업태는 한글 15자까지 입력 가능합니다");
		return;
	}
	if(CheckLength(form.up_com_item)>30) {
		form.up_com_item.focus();
		alert("사업자 종목은 한글 15자까지 입력 가능합니다");
		return;
	}
	if(form.up_com_tel1.value.length==0 || form.up_com_tel2.value.length==0 || form.up_com_tel3.value.length==0) {
		form.up_com_tel1.focus();
		alert("회사 대표전화를 입력하세요.");
		return;
	}
	if(!isNumber(form.up_com_tel1.value) || !isNumber(form.up_com_tel2.value) || !isNumber(form.up_com_tel3.value)) {
		form.up_com_tel1.focus();
		alert("전화번호는 숫자만 입력하세요.");
		return;
	}
	if(form.up_com_fax1.value.length>0 && form.up_com_fax2.value.length>0 && form.up_com_fax3.value.length>0) {
		if(!isNumber(form.up_com_fax1.value) || !isNumber(form.up_com_fax2.value) || !isNumber(form.up_com_fax3.value)) {
			form.up_com_fax1.focus();
			alert("팩스번호는 숫자만 입력하세요.");
			return;
		}
	}
	if(form.up_p_name.value.length==0) {
		form.up_p_name.focus();
		alert("담당자 이름을 입력하세요.");
		return;
	}
	if(form.up_p_mobile1.value.length==0 || form.up_p_mobile2.value.length==0 || form.up_p_mobile3.value.length==0) {
		form.up_com_tel1.focus();
		alert("담당자 휴대전화를 입력하세요.");
		return;
	}
	if(!isNumber(form.up_p_mobile1.value) || !isNumber(form.up_p_mobile2.value) || !isNumber(form.up_p_mobile3.value)) {
		form.up_com_tel1.focus();
		alert("담당자 휴대전화 번호는 숫자만 입력하세요.");
		return;
	}
	if(form.up_p_email.value.length==0) {
		form.up_p_email.focus();
		alert("담당자 이메일을 입력하세요.");
		return;
	}
	if(!IsMailCheck(form.up_p_email.value)) {
		form.up_p_email.focus();
		alert("담당자 이메일을 정확히 입력하세요.");
		return;
	}
	if(form.up_bank1.value.length==0 || form.up_bank2.value.length==0 || form.up_bank3.value.length==0) {
		alert("정산받을 계좌정보를 정확히 입력하세요.");
		form.up_bank1.focus();
		return;
	}
	if(form.up_passwd.value.length>0 || form.up_passwd2.value.length>0) {
		if(form.up_passwd.value!=form.up_passwd.value) {
			alert("변경하실 비밀번호가 일치하지 않습니다.");
			form.up_passwd2.focus();
			return;
		} else if(form.up_passwd.value.length<4) {
			alert("비밀번호는 영문, 숫자를 혼합하여 4~12자 이내로 입력하세요.");
			form.up_passwd.focus();
			return;
		}
	}


	if(confirm("변경하신 내용을 저장하시겠습니까?")) {
		form.mode.value="update";
		form.target="processFrame";
		form.submit();
	}
}

function f_addr_search(form,post,addr,gbn) {
	window.open("<?=$Dir.FrontDir?>addr_search.php?form="+form+"&post="+post+"&addr="+addr+"&gbn="+gbn,"f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");
}

function commissionDivView(v) {

	cm_div = document.getElementById('commission_div');

	if (v=='N') {
		cm_div.style.display="none";
	}else {
		if (cm_div.style.display=="none") {
			cm_div.style.display="";
		}else{
			cm_div.style.display="none";
		}
	}
}

function selCommission(num) {

	c_td = document.getElementById("commission_all")

	if (num==1) {
		c_td.style.display="inline"
	}else{
		c_td.style.display="none"
	}

}

function commissionRequest() {

	var form = document.form1;

	if(form.rq_commission_type[1].checked) {

		if(form.rq_rate.value.length==0) {
			alert("수수료를 입력해주세요.");
			form.rq_rate.focus();
			return;
		}
	}

	if(form.rq_name.value.length==0) {
		alert("요청자를 입력해주세요.");
		form.rq_name.focus();
		return;
	}


	if(confirm("수수료 변경을 요청하시겠습니까??")) {
		form.mode.value="com";
		form.target="processFrame";
		form.submit();
	}
}

</script>

<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
<col width=190></col>
<col width=20></col>
<col width=></col>
<col width=5></col>
<tr>
	<td width=190 valign=top nowrap background="images/minishop_leftbg.gif"><? include ("menu.php"); ?></td>
	<td width=20 nowrap></td>
	<td valign=top style="padding-top:20px">

	<table width="100%"  border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td><img src="images/venter_info_title.gif"></td>
				</tr>
				<tr>
					<td height=5 background="images/minishop_titlebg.gif">
				</tr>
				</table>
			</td>
		</tr>
		<tr><td height=10></td></tr>
		<tr>
			<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% >
				<tr>
					<td colspan=3 >


						<table cellpadding="10" cellspacing="1" width="100%" bgcolor="#EFEFF2">
							<tr>
								<td  bgcolor="#F5F5F9" style="padding:20px">
									<table border=0 cellpadding=0 cellspacing=0 width=100%>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">입점사 관리자 정보 및 기타 설정 값을 입력합니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">입력한 정보는 본사 사이트 입점업체 정보에 입력됩니다.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">입점사 관리자의 상품 처리권한[등록/수정/삭제/인증]은 본사 관리자가 승인 후 가능 합니다.</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>



					</td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=40></td></tr>
			<tr>
				<td >

				<table border=0 cellpadding=0 cellspacing=0 width=100%>

				<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>" enctype="multipart/form-data">
				<input type=hidden name=mode>

				<tr>
					<td><img src="images/venter_info_stitle01.gif" alt="입점업체 기본정보" align="absmiddle"><font style="color:#2A97A7">('*'표시는 필수입력입니다)</font></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=2 bgcolor=#808080></td></tr>
				</table>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=140></col>
				<col width=></col>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> 상호(회사명)</B></td>
					<td style=padding:7,10>
					<input type="text" class=input  name=up_com_name value="<?=$_venderdata->com_name?>" size=20 maxlength=30 disabled>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> 사업자등록번호</B></td>
					<td style=padding:7,10>
					<input type="text" class=input  name=up_com_num value="<?=$_venderdata->com_num?>" size=20 maxlength=20 onkeyup="strnumkeyup(this)" disabled>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>

				<!--
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> 네임텍 사용유무</B></td>
					<td style=padding:7,10>
						<input type="radio" name="com_nametech" value="1" />사용함&nbsp;&nbsp;
						<input type="radio" name="com_nametech" value="0" checked />사용안함
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				-->

				<tr>
					<td bgcolor="#F5F5F5" background="images/line01.gif" style="background-repeat:repeat-y; background-position:right; padding:9px;"><B><font color=red>*</font> 대표이미지</B></td>
					<td style="padding:7px 10px;">

						<div style="margin:5px;">
							<div style="float:left; margin:0px; padding:0px; font-size:0px;"><img src="<?=$com_image_url.$_venderdata->com_image?>" width="120" onerror="this.src='/images/no_img.gif';" style="border:1px solid #dddddd;" /></div>
							<div style="float:left; margin-top:5px; margin-left:10px;">
								<div>
									<span style="font-size:11px; color:#666666; line-height:15px; letter-spacing:-1px;">
										<strong>사용유무 : </strong><input type="checkbox" name="com_nametech" value="1" <?=($_venderdata->com_nametech?"checked":"");?>><br /><br /><br />

										※ <b>네임텍 이미지는??</b>
										<div style="margin:5px 0px;"><img src="images/vender_nametek_sample.gif" style="border:1px solid #e5e5e5;" alt="" /></div>
										- 네임텍 이미지는 상품 목록 및 상세 페이지에서 입점사 정보 출력시 사용되는 이미지 입니다.<br />
										- 이미지 사이즈는 가로*세로 100px * 100px 을 권장드립니다.
									</span>
								</div>
								<div style="margin-top:10px;">
									<input type="file" name="com_image" id="com_image">
									<input type="checkbox" name="com_image_del" id="com_image_del" value="OK" onclick="com_image.style.display=( this.checked ?'none':'inline')"><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="com_image_del">삭제</label>
									<input type="hidden" name="com_image_del_file" value="<?=$com_image_url.$_vdata->com_image?>">
								</div>
							</div>
						</div>

						<!--
						<strong>사용유무 : </strong><input type="checkbox" name="com_nametech" value="1" <?=($_venderdata->com_nametech?"checked":"");?>><br /><br />
						<img src="<?=$com_image_url.$_venderdata->com_image?>" onerror="this.src='/images/no_img.gif';">
						<input type="file" name="com_image" id="com_image">
						<input type="checkbox" name="com_image_del" id="com_image_del" value="OK" onclick="com_image.style.display=( this.checked ?'none':'inline')"><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="com_image_del">삭제</label>
						<input type="hidden" name="com_image_del_file" value="<?=$com_image_url.$_venderdata->com_image?>">
						<br /> 이미지 사이즈 100px * 100px 권장.
						-->

					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>


				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> 대표자 성명</B></td>
					<td style=padding:7,10>
					<input class=input type="text" name=up_com_owner value="<?=$_venderdata->com_owner?>" size=20 maxlength="12">
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td rowspan=2 bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> 사업장 주소</B></td>
					<td style=padding:7,10>
					<input class=input type="text" type=text name="up_com_post1" value="<?=substr($_venderdata->com_post,0,3)?>" size="3" maxlength="3" readonly> - <input type=text class=input  name="up_com_post2" value="<?=substr($_venderdata->com_post,3,3)?>" size="3" maxlength="3" readonly> <img src="images/btn_findpostno.gif" border=0 align=absmiddle style="cursor:hand" onClick="f_addr_search('form1','up_com_post','up_com_addr',2);">
					</td>
				</tr>
				<tr>
					<td style=padding:7,10>
					<input type=text class=input  name="up_com_addr" value="<?=$_venderdata->com_addr?>" size=50 maxlength=150>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> 사업자 업태</B></td>
					<td style=padding:7,10>
					<input type="text" class=input  name=up_com_biz value="<?=$_venderdata->com_biz?>" size=30 maxlength=30>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> 사업자 종목</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=up_com_item value="<?=$_venderdata->com_item?>" size=30 maxlength=30>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> 회사 대표전화</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=up_com_tel1 value="<?=$com_tel[0]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)">-<input type=text class=input  name=up_com_tel2 value="<?=$com_tel[1]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)">-<input type=text class=input  name=up_com_tel3 value="<?=$com_tel[2]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)">
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>



				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B> 통신판매신고</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=ec_num value="<?=$_venderdata->ec_num?>" size=30 maxlength=30>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>


				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B> 사업자구분</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=com_type value="<?=$_venderdata->com_type?>" size=30 maxlength=30>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>




				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>회사 팩스번호</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=up_com_fax1 value="<?=$com_fax[0]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)">-<input type=text class=input  name=up_com_fax2 value="<?=$com_fax[1]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)">-<input type=text class=input  name=up_com_fax3 value="<?=$com_fax[2]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)">
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>회사 홈페이지</B></td>
					<td style=padding:7,10>
					http://<input type=text class=input  name=up_com_homepage value="<?=$_venderdata->com_homepage?>" size=30 maxlength=50>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				</table>






				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=40></td></tr>
				<tr>
					<td><img src="images/venter_info_stitle02.gif" alt="업체 담당자 정보" align="absmiddle"> <font style="color:#2A97A7">('*'표시는 필수입력입니다)</font></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=2 bgcolor=#808080></td></tr>
				</table>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=140></col>
				<col width=></col>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> 담당자 이름</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=up_p_name value="<?=$_venderdata->p_name?>" size=20 maxlength=20> &nbsp; <span class="notice_blue">* 입점 담당자 이름을 정확히 입력하세요.</font>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> 담당자 휴대전화</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=up_p_mobile1 value="<?=$com_p_mobile[0]?>" size=4 maxlength=3 style="width:40" onkeyup="strnumkeyup(this)">-<input type=text class=input  name=up_p_mobile2 value="<?=$com_p_mobile[1]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)">-<input type=text class=input  name=up_p_mobile3 value="<?=$com_p_mobile[2]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)"></td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> 담당자 이메일</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=up_p_email value="<?=$_venderdata->p_email?>" size=30 maxlength=50> &nbsp; <span class="notice_blue">* 주문확인시 담당자 이메일로 통보됩니다.</font>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>담당자 부서명</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=up_p_buseo value="<?=$_venderdata->p_buseo?>" size=20 maxlength=20>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>담당자 직위</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=up_p_level value="<?=$_venderdata->p_level?>" size=20 maxlength=20>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=40></td></tr>
				<tr>
					<td><img src="images/venter_info_stitle04.gif" alt="미니샵 운영ID 관리" align="absmiddle"></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=2 bgcolor=#808080></td></tr>
				</table>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=140></col>
				<col width=></col>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>운영 ID</td>
					<td style=padding:7,10>
					<B><?=$_venderdata->id?></B>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>비밀번호 변경</B></td>
					<td style=padding:7,10>
					<input class=input  type=password name=up_passwd size=15> &nbsp; <span class="notice_blue">* 영문, 숫자를 혼용하여 사용(4자 ~ 12자)</font>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>비밀번호 확인</B></td>
					<td style=padding:7,10>
					<input class=input type=password name=up_passwd2 size=15> &nbsp; <span class="notice_blue">* 비밀번호는 정기적으로 변경 하실 것을 권장합니다.</font>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>운영자 세션 삭제</B></td>
					<td style=padding:7,10>
					<input type=radio name=up_session value="N" id="idx_sessionN"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_sessionN">로그인 세션 유지</label><img width=5 height=0><input type=radio name=up_session value="Y" id="idx_sessionY"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_sessionY">로그인 세션 삭제</label><br>
					<span class="notice_blue">* 로그인 세션 삭제시 자신을 제외한 모든 운영자들은 재로그인 후 이용이 가능합니다.</font>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				</table>



				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=40></td></tr>
				<tr>
					<td><img src="images/venter_info_stitle03.gif" alt="업체 관리정보" align="absmiddle"> <font style="color:#2A97A7">('*'표시는 필수입력입니다)</font></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=2 bgcolor=#808080></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=140></col>
				<col width=></col>

				<? if ($account_rule!="1")  { ?>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>수수료 운영형태</td>
					<td style=padding:7,10>
					<B><? if ($_vmdata->commission_type=="1") { ?>상품별 수수료 적용<? }else{ ?>전체상품 동일 수수료 적용<? } ?></B>
					&nbsp;&nbsp;&nbsp;&nbsp;<button style="color:#ffffff;background-color:#000000;border:0;width:80px;height:25px;cursor:pointer" onclick="commissionDivView();">변경요청</button>
					<div id="commission_div" style="position:absolute;width:450px;border:2px solid #acacac;background-color:#ffffff;z-index:999;padding:5px;display:none;">
						<div style="width:100%;text-align:right"><span style="border:1px solid gray;color:#ffffff;background-color:#000000;padding:2px 4px;cursor:pointer" onclick="commissionDivView('N');" >X</span></div>
						<div style="width:100%;margin-top:5px;">
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
								<col width=100 />
								<col width= />
							<tr><td height=2 colspan="2" bgcolor=#808080></td></tr>
							<tr>
								<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>운영형태</td>
								<td style=padding:7,10>
								<input type=radio name=rq_commission_type id=rq_commission_type0 value="1" onclick="selCommission('0');"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=rq_commission_type0>상품개별 수수료</label>
								&nbsp;&nbsp;
								<input type=radio name=rq_commission_type id=rq_commission_type1 value="0" onclick="selCommission('1');" checked> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=rq_commission_type1>전체상품 동일 수수료</label>
								</td>
								<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
								<tr id="commission_all" >
									<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>전체 수수료</td>
									<td style=padding:7,10>
										<input type=text name=rq_rate value="" size=3 maxlength=3 onkeyup="strnumkeyup(this)" class=input>%
									</td>
								</tr>
								<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
								<tr id="commission_all" >
									<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>요청자 이름</td>
									<td style=padding:7,10>
										<input type=text name=rq_name value="" size=10 class=input>
									</td>
								</tr>
								<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
								<tr><td></td>
									<td style="padding-top:10px;text-align:right;"><span style="border:1px solid gray;color:#ffffff;background-color:#000000;padding:2px 4px;cursor:pointer" onclick="commissionRequest()">요청</span></td>
								</tr>
							</tr>
							</table>
						</div>
					</div>
					<? if ($_vmdata->commission_status=="1" || $_vmdata->commission_status=="2") {

							if ($_vmdata->rq_commission_type=="1") {
								$cm_value = "상품별 수수료";
							}else{
								$cm_value = "전체상품 동일 수수료 ".$_vmdata->rq_rate."%";
							}

							$cm_status = "";

							if ($_vmdata->commission_status=="1") {
								$cm_status = "요청 중";
							}else if ($_vmdata->commission_status=="2") {
								$cm_status = "요청 거부";
							}

						?>
					<br/><br/>
					<span class="notice_blue"><?= $cm_value ?>로 <?= $cm_status ?></span>
					<? } ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<? if (!$_vmdata->commission_type) {?>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>전체 수수료</td>
					<td style=padding:7,10>
					<B><?=(int)$_venderdata->rate?> %</B>
					&nbsp;&nbsp;&nbsp;&nbsp; <span class="notice_blue">* 모든상품에 동일 적용됩니다.</font>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<? } ?>

				<? } ?>

				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상품 처리 권한</td>
					<td style=padding:7,10>
					<input type=checkbox name=chk_prdt1 value="Y" <?if(substr($_venderdata->grant_product,0,1)=="Y")echo"checked";?> disabled>등록
					<img width=5 height=0>
					<input type=checkbox name=chk_prdt2 value="Y" <?if(substr($_venderdata->grant_product,1,1)=="Y")echo"checked";?> disabled>수정
					<img width=5 height=0>
					<input type=checkbox name=chk_prdt3 value="Y" <?if(substr($_venderdata->grant_product,2,1)=="Y")echo"checked";?> disabled>삭제
					<img width=5 height=0>
					<input type=checkbox name=chk_prdt4 value="Y" <?if(substr($_venderdata->grant_product,3,1)=="Y")echo"checked";?> disabled>등록/수정시, 관리자 인증
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>입점 상품수 제한</td>
					<td style=padding:7,10>
					<B><?=($_venderdata->product_max==0?"무제한 등록 가능":$_venderdata->product_max."개 까지 상품등록 가능")?></B>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>판매 수수료</td>
					<td style=padding:7,10>
					<B><?=(int)$_venderdata->rate?> %</B>
					&nbsp;&nbsp;&nbsp;&nbsp; <span class="notice_blue">* 쇼핑몰 본사에서 받는 상품판매 수수료입니다.</font>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> 정산 계좌정보</td>
					<td style=padding:7,10>
					은행 <input type=text class=input  name=up_bank1 value="<?=$bank_account[0]?>" size=10>
					<img width=5 height=0>
					계좌번호 <input type=text class=input  name=up_bank2 value="<?=$bank_account[1]?>" size=20>
					<img width=5 height=0>
					예금주 <input type=text class=input  name=up_bank3 value="<?=$bank_account[2]?>" size=15>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>정산일</td>
					<td style=padding:7,10>
					<B>매월 <?=(strlen($_venderdata->account_date)>0?$_venderdata->account_date."일":"")?></B>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
			<? /*추가 jdy */?>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>결산일</td>
					<td style=padding:7,10>
					<B><?=(strlen($_vmdata->close_date)>0?"정산일로 부터 ".$_vmdata->close_date." 일전까지":"")?></B>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
			<? /*추가 jdy */?>

				</form>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>수수료정책변경<br/>히스토리</td>
					<td style=padding:7,10>
					<?
						getVenderCommissionHistory($_VenderInfo->getVidx(), 0);
					?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				</table>


				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<tr>
					<td align=center>
					<A HREF="javascript:formSubmit()"><img src="images/btn_save01.gif" border=0></A>
					</td>
				</tr>


				</table>

				<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

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
