<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");
include_once($Dir."lib/admin_more.php");

####################### 페이지 접근권한 check ###############
$PageCode = "vd-1";
$MenuCode = "vender";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];

// 정산 기준 조회 jdy
$shop_more_info = getShopMoreInfo();
$account_rule = $shop_more_info['account_rule'];

$reserve_use = $shop_more_info['reserve_use'];
$coupon_use = $shop_more_info['coupon_use'];
// 정산 기준 조회 jdy


// 제휴문의에서 넘겨 받기
if( strlen($_GET['key']) > 0 ) {
	$key = $_GET['key'];
	$keySql = "SELECT * FROM `tblVenderProposal` WHERE `idx`=".$key." LIMIT 1 ; ";
	$keyResult=mysql_query($keySql,get_db_conn());
	$keyRow=mysql_fetch_object($keyResult);

		$up_com_name = $keyRow->company;
		$up_com_owner = $up_p_name = $keyRow->mng_name;
		//$zip = explode("-", $keyRow->comp_zip );
		$zip = $keyRow->comp_zip;
			$up_com_post1 = $zip;
			//$up_com_post2 = $zip[1];
		$up_com_addr = $keyRow->comp_addr1." ".$keyRow->comp_addr2;
		$tel = explode("-", $keyRow->mng_tell );
			$up_com_tel1 = $tel[0];
			$up_com_tel2 = $tel[1];
			$up_com_tel3 = $tel[2];
		$up_com_homepage = eregi_replace( "[a-z][a-z][a-z][a-z]://", "",  $keyRow->comp_site);
		$phone = explode("-", $keyRow->mng_phone );
			$up_p_mobile1 = $phone[0];
			$up_p_mobile2 = $phone[1];
			$up_p_mobile3 = $phone[2];
		$up_p_email = $keyRow->mng_mail;

}



if($type=="insert") {
	$up_disabled=$_POST["up_disabled"];
	$up_id=$_POST["up_id"];
	$up_passwd=$_POST["up_passwd"];
	$up_com_name=$_POST["up_com_name"];
	$up_com_num=$_POST["up_com_num"];
	$up_brand_name=$_POST["up_brand_name"];
	$up_com_owner=$_POST["up_com_owner"];
	$up_com_post1=$_POST["up_com_post1"];
	$up_com_post2="";//$_POST["up_com_post2"];
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

	$chk_prdt1=$_POST["chk_prdt1"];
	$chk_prdt2=$_POST["chk_prdt2"];
	$chk_prdt3=$_POST["chk_prdt3"];
	$chk_prdt4=$_POST["chk_prdt4"];
	$up_product_max=$_POST["up_product_max"];
	$up_rate=$_POST["up_rate"];
	$up_bank1=$_POST["up_bank1"];
	$up_bank2=$_POST["up_bank2"];
	$up_bank3=$_POST["up_bank3"];
	$up_account_date=$_POST["up_account_date"];

	$com_type=$_POST["com_type"];
	$ec_num=$_POST["ec_num"];

	/* 수수료 관련 추가 jdy */
	$up_commission_type = $_POST["up_commission_type"];
	$up_etc = $_POST["up_etc"];
	$up_admin_memo = $_POST["up_admin_memo"];
	if ($up_commission_type==1) {
		$up_rate = 0;
	}

	$up_close_date = $_POST["up_close_date"];


	$up_reserve_use=$_POST["up_reserve_use"];
	$up_coupon_use=$_POST["up_coupon_use"];

	$up_adjust_lastday=$_POST["adjust_lastday"];
	/* 수수료 관련 추가 jdy */

	$up_com_post="";
	/*if(strlen($up_com_post1)==3 && strlen($up_com_post2)==3) {
		$up_com_post=$up_com_post1.$up_com_post2;
	}*/
	$up_com_post=$up_com_post1;
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

	if($chk_prdt1!="Y") $chk_prdt1="N";
	if($chk_prdt2!="Y") $chk_prdt2="N";
	if($chk_prdt3!="Y") $chk_prdt3="N";
	if($chk_prdt4!="Y") $chk_prdt4="N";
	$grant_product=$chk_prdt1.$chk_prdt2.$chk_prdt3.$chk_prdt4;

	$bank_account="";
	if(strlen($up_bank1)>0 && strlen($up_bank2)>0 && strlen($up_bank3)>0) {
		$bank_account=$up_bank1."=".$up_bank2."=".$up_bank3;
	}


	$error="";
	if(strlen($up_id)<4 || strlen($up_id)>12) {
		$error="업체 아이디는 4자 이상 12자 이하로 입력하셔야 합니다.";
	} else if(IsAlphaNumeric($up_id)==false) {
		$error="업체 아이디는 영문, 숫자를 조합하여 4~12자 이내로 등록이 가능합니다.";
	} else if(strlen($up_passwd)==0) {
		$error="비밀번호를 입력하세요.";
	} else if(strlen($up_com_name)==0) {
		$error="회사명을 입력하세요.";
	//} else if(strlen($up_com_num)==0) {
	//	$error="사업자등록번호를 입력하세요.";
	} else if(strlen($up_brand_name)==0) {
		$error="미니샵명을 입력하세요.";
	//} else if(chkBizNo($up_com_num)==false) {
	//	$error="사업자등록번호를 정확히 입력하세요.";
	} else if(strlen($up_com_tel)==0) {
		$error="회사 대표전화를 정확히 입력하세요.";
	} else if(strlen($up_p_name)==0) {
		$error="담당자 이름을 입력하세요.";
	} else if(strlen($up_p_mobile)==0) {
		$error="담당자 휴대전화를 정확히 입력하세요.";
	} else if(strlen($up_p_email)==0) {
		$error="담당자 이메일을 입력하세요.";
	} else if(ismail($up_p_email)==false) {
		$error="담당자 이메일을 정확히 입력하세요.";
	} else if(strlen($up_close_date)==0) {
		/* 추가 jdy */
		$error="결산일을 입력해주세요.";
		/* 추가 jdy */
	}

	if(strlen($error)==0) {
		$sql = "SELECT id FROM tblvenderinfo WHERE id='".$up_id."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$error="업체 아이디가 중복되었습니다.";
		}
		mysql_free_result($result);

		if(strlen($error)==0) {
			$sql = "SELECT brand_name FROM tblvenderstore WHERE brand_name='".$up_brand_name."' ";
			$result=mysql_query($sql,get_db_conn());
			if($row=mysql_fetch_object($result)) {
				$error="미니샵명이 중복되었습니다.";
			}
			mysql_free_result($result);
		}

		if(strlen($error)==0) {
			$sql = "INSERT tblvenderinfo SET ";
			$sql.= "id				= '".$up_id."', ";
			$sql.= "passwd			= '".md5($up_passwd)."', ";
			$sql.= "grant_product	= '".$grant_product."', ";
			$sql.= "product_max		= '".$up_product_max."', ";
			$sql.= "rate			= '".$up_rate."', ";
			$sql.= "bank_account	= '".$bank_account."', ";
			$sql.= "account_date	= '".$up_account_date."', ";
			$sql.= "com_name		= '".$up_com_name."', ";
			$sql.= "com_num			= '".$up_com_num."', ";
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
			$sql.= "regdate			= '".date("YmdHis")."', ";
			$sql.= "disabled			= '".$up_disabled."', ";
			$sql.= "com_type		= '".$com_type."', ";
			$sql.= "ec_num			= '".$ec_num."' ";


			// 대표이미지 등록
			if( $_FILES['com_image']['error'] == 0 AND $_FILES['com_image']['size'] > 0 AND eregi("image",$_FILES['com_image']['type']) AND $_POST['com_image_del'] != "OK" ) {
				$exte = explode(".",$_FILES['com_image']['name']);
				$exte = $exte[ count($exte)-1 ];
				$com_image_name = "comImgae_".date("YmdHis").".".$exte;
				move_uploaded_file($_FILES['com_image']['tmp_name'],$com_image_url.$com_image_name);
				$sql.= ", com_image = '".$com_image_name."' ";
			}


			if(mysql_query($sql,get_db_conn())) {
				$sql = "SELECT LAST_INSERT_ID() ";
				$res = mysql_fetch_row(mysql_query($sql,get_db_conn()));
				$vender = $res[0];

				$sql = "INSERT tblvenderstore SET ";
				$sql.= "vender		= '".$vender."', ";
				$sql.= "id			= '".$up_id."', ";
				$sql.= "brand_name	= '".$up_brand_name."', ";
				$sql.= "skin		= '1,1,1' ";
				mysql_query($sql,get_db_conn());

				$sql = "INSERT tblvenderstorecount SET ";
				$sql.= "vender		= '".$vender."' ";
				mysql_query($sql,get_db_conn());


				/* 수수료 관련 추가 jdy */
				$sql = "INSERT vender_more_info SET ";
				$sql.= "vender			= '".$vender."', ";
				$sql.= "commission_type	= '".$up_commission_type."', ";
				$sql.= "rq_commission_type	= '0', ";
				$sql.= "rq_rate	= '0', ";
				$sql.= "commission_status = '0', ";
				$sql.= "close_date = '".$up_close_date."', ";
				$sql.= "etc				= '".$up_etc."', ";
				$sql.= "admin_memo		= '".$up_admin_memo."', ";
				$sql.= "reserve_use		= '".$up_reserve_use."', ";
				$sql.= "coupon_use		= '".$up_coupon_use."', ";
				$sql.= "adjust_lastday	= '".$up_adjust_lastday."' ";

				mysql_query($sql,get_db_conn());
				/* 수수료 관련 추가 jdy */

				$sql="UPDATE tblshopcount SET vendercnt=vendercnt+1 ";
				mysql_query($sql,get_db_conn());

				$log_content = "## 입점업체 신규등록 ## - 업체ID : ".$up_id;
				ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

				echo "<html></head><body onload=\"alert('업체 등록이 완료되었습니다.');location.href='vender_management.php'\"></body></html>";exit;
			} else {
				$error="입점업체 등록중 오류가 발생하였습니다.";
			}
		}
	}
	if(strlen($error)>0) {
		$onload="<script>alert('".$error."')</script>";
	}
}

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	form=document.form1;
	if(form.up_disabled[0].checked!=true && form.up_disabled[1].checked!=true) {
		alert("업체 승인여부를 선택하세요.");
		form.up_disabled[0].focus();
		return;
	}
	if(form.up_id.value.length==0) {
		alert("업체 아이디를 입력하세요."); form.up_id.focus(); return;
	}
	if(form.up_id.value.length<4) {
		alert("업체 아이디는 4자 이상 12자 이하로 입력하셔야 합니다."); form.up_id.focus(); return;
	}
	if (IsAlphaNumeric(form.up_id.value)==false) {
   		alert("업체 아이디는 영문, 숫자를 조합하여 4~12자 이내로 등록이 가능합니다."); form.up_id.focus(); return;
   	}
	if(form.up_passwd.value.length==0) {
		alert("비밀번호를 입력하세요."); form.up_passwd.focus(); return;
	}
	if(form.up_passwd.value!=form.up_passwd2.value) {
		alert("비밀번호가 일치하지 않습니다."); form.up_passwd2.focus(); return;
	}
	if(form.up_com_name.value.length==0) {
		alert("회사명을 입력하세요."); form.up_com_name.focus(); return;
	}
	if(form.up_brand_name.value.length==0) {
		alert("미니샵명을 입력하세요."); form.up_brand_name.focus(); return;
	}
	//if(form.up_com_num.value.length==0) {
	//	alert("사업자등록번호를 입력하세요."); form.up_com_num.focus(); return;
	//}
	//if(chkBizNo(form.up_com_num.value)==false) {
	//	alert("사업자등록번호가 잘못되었습니다."); form.up_com_num.focus(); return;
	//}
	if(form.up_com_tel1.value.length==0 || form.up_com_tel2.value.length==0 || form.up_com_tel3.value.length==0) {
		alert("회사 대표전화를 정확히 입력하세요."); form.up_com_tel1.focus(); return;
	}
	if(form.up_p_name.value.length==0) {
		alert("담당자 이름을 입력하세요."); form.up_p_name.focus(); return;
	}
	if(form.up_p_mobile1.value.length==0 || form.up_p_mobile2.value.length==0 || form.up_p_mobile3.value.length==0) {
		alert("담당자 휴대전화를 정확히 입력하세요."); form.up_p_mobile1.focus(); return;
	}
	if(form.up_p_email.value.length==0) {
		alert("담당자 이메일을 입력하세요."); form.up_p_email.focus(); return;
	}
	if(IsMailCheck(form.up_p_email.value)==false) {
		alert("담당자 이메일을 정확히 입력하세요."); form.up_p_email.focus(); return;
	}
	//jdy
	if(form.up_close_date.value.length==0) {
		alert("결산일을 입력하세요."); form.up_close_date.focus(); return;
	}
	if(form.up_close_date.value<1) {
		alert("결산일은 1보다 크게 입력하십시오."); form.up_close_date.focus(); return;
	}

	if(confirm("입점업체를 등록하시겠습니까?")) {
		document.form1.type.value="insert";
		document.form1.submit();
	}
}

function iddup() {
	id=document.form1.up_id;
	if(id.value.length==0) {
		alert("업체 아이디를 입력하세요.");
		id.focus();
		return;
	}
	window.open("vender_iddup.php?id="+id.value,"","height=100,width=300,toolbar=no,menubar=no,scrollbars=no,status=no");
}

function branddup() {
	brand=document.form1.up_brand_name;
	if(brand.value.length==0) {
		alert("미니샵명을 입력하세요.");
		brand.focus();
		return;
	}
	window.open("vender_branddup.php?brand_name="+brand.value,"","height=100,width=300,toolbar=no,menubar=no,scrollbars=no,status=no");
}

function f_addr_search(form,post,addr,gbn) {
	window.open("<?=$Dir.FrontDir?>addr_search.php?form="+form+"&post="+post+"&addr="+addr+"&gbn="+gbn,"f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");
}

function selCommission(num) {

	c_td = document.getElementById("commission_all")

	if (num==1) {
		c_td.style.display="inline"
	}else{
		c_td.style.display="none"
	}

}


function setAccountDate(setType) {

	setValue = "";

	if (setType == 0) {

		for (i=0;i<31;i++) {

			if (setValue=="") {
				setValue = i+1;
			}else{

				setValue = setValue+","+(i+1);
			}
		}

	}else if (setType == 1) {

		for (i=0;i<31;i=i+2) {

			if (setValue=="") {
				setValue = i+1;
			}else{

				setValue = setValue+","+(i+1);
			}
		}

	}else if (setType == 2) {

		for (i=1;i<31;i=i+2) {

			if (setValue=="") {
				setValue = i+1;
			}else{

				setValue = setValue+","+(i+1);
			}
		}
	}

	document.form1.up_account_date.value = setValue;

}

function adjustChecked(num) {


	adjust = document.getElementById("adjust_div");
	if (num==0) {
		adjust.style.display = "";
	}else{
		adjust.style.display = "none";
	}

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
			<? include ("menu_vender.php"); ?>
			</td>

			<td></td>
			<td valign="top">




<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="29" colspan="3">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 입점업체 관리 &gt; <span class="2depth_select">입점업체 신규등록</span></td>
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
			<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_new_title.gif"ALT=""></TD>
					</tr><tr>
					<TD width="100%" background="images/title_bg.gif" height="21"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
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
					<TD width="100%" class="notice_blue"><p>쇼핑몰에 입점할 업체를 신규로 등록하실 수 있습니다.</p></TD>
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
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_reg_stitle1.gif" ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
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
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">업체 승인</TD>
					<TD class="td_con1">
					<input type=radio name=up_disabled id=up_disabled0 value="0" <?if($up_disabled=="0")echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_disabled0>승인</label>
					<img width=20 height=0>
					<input type=radio name=up_disabled id=up_disabled1 value="1" <?if($up_disabled=="1" || strlen($up_disabled)==0)echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_disabled1>보류</label>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">업체 ID</TD>
					<TD class="td_con1">
					<input type=text name=up_id value="<?=$up_id?>" size=20 maxlength=12 class=input>
					<A class=board_list hideFocus style="selector-dummy: true" onfocus=this.blur(); href="javascript:iddup();"><IMG src="images/duple_check_img.gif" border=0 align="absmiddle"></A>
					&nbsp;&nbsp; <FONT class=font_orange>* 영문, 숫자를 혼용하여 사용(4자 ~ 12자)</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">패스워드</TD>
					<TD class="td_con1">
					<input type=password name=up_passwd value="" size=20 maxlength=12 class=input>
					&nbsp;&nbsp;
					<FONT class=font_orange>* 영문, 숫자를 혼용하여 사용(4자 ~ 12자)</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">패스워드 확인</TD>
					<TD class="td_con1">
					<input type=password name=up_passwd2 value="" size=20 maxlength=12 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_reg_stitle2.gif"  ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
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
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">네임텍 이미지</TD>
					<TD class="td_con1">

						<div style="margin:5px;">
							<div style="float:left; margin:0px; padding:0px; font-size:0px;"><img src="<?=$com_image_url.$_vdata->com_image?>" width="120" onerror="this.src='/images/no_img.gif';" style="border:1px solid #dddddd;" /></div>
							<div style="float:left; margin-top:5px; margin-left:10px;">
								<div>
									<span style="font-size:11px; color:#666666; line-height:15px; letter-spacing:-1px;">
										※ <b>네임텍 이미지는??</b><br />
										<img src="images/vender_nametek_sample.gif" style="border:1px solid #e5e5e5;" hspace="8" vspace="4" alt="" /><br />
										- 네임텍 이미지는 상품 목록 및 상세 페이지에서 입점사 정보 출력시 사용되는 이미지 입니다.<br />
										- 이미지 사이즈는 가로*세로 100px * 100px 을 권장드립니다.
									</span>
								</div>
								<div style="margin-top:10px;">
									<input type="file" name="com_image" id="com_image">
							</div>
						</div>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>

				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">상호 (회사명)</TD>
					<TD class="td_con1">
					<input type=text name=up_com_name value="<?=$up_com_name?>" size=20 maxlength=30 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">사업자등록번호</TD>
					<TD class="td_con1">
					<input type=text name=up_com_num value="<?=$up_com_num?>" size=20 maxlength=20 onkeyup="strnumkeyup(this)" class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">미니샵명</TD>
					<TD class="td_con1">
					<input type=text name=up_brand_name value="<?=$up_brand_name?>" size=20 maxlength=30 class=input>
					<A class=board_list hideFocus style="selector-dummy: true" onfocus=this.blur(); href="javascript:branddup();"><IMG src="images/duple_check_img.gif" border=0 align="absmiddle"></A>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">대표자 성명</TD>
					<TD class="td_con1">
					<input name=up_com_owner value="<?=$up_com_owner?>" size=20 maxlength="12" class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">회사 주소</TD>
					<TD class="td_con1">
					<input type=text name="up_com_post1" value="<?=$up_com_post1?>" size="5" maxlength="5" readonly class=input> <A class=board_list hideFocus style="selector-dummy: true" onfocus=this.blur(); href="javascript:f_addr_search('form1','up_com_post','up_com_addr',2);"><IMG src="images/order_no_uimg.gif" border=0 align="absmiddle"></A><br>
					<input type=text name="up_com_addr" value="<?=$up_com_addr?>" size=50 maxlength=150 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">사업자 업태</TD>
					<TD class="td_con1">
					<input type="text" name=up_com_biz value="<?=$up_com_biz?>" size=30 maxlength=30 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">사업자 종목</TD>
					<TD class="td_con1">
					<input type=text name=up_com_item value="<?=$up_com_item?>" size=30 maxlength=30 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>


				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">통신판매신고</TD>
					<TD class="td_con1">
					<input type=text name=ec_num value="" size=20 maxlength=20 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>



				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">사업자구분</TD>
					<TD class="td_con1">
					<input type=text name=com_type value="" size=20 maxlength=20 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>


				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">회사 대표전화</TD>
					<TD class="td_con1">
					<input type=text name=up_com_tel1 value="<?=$up_com_tel1?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_com_tel2 value="<?=$up_com_tel2?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_com_tel3 value="<?=$up_com_tel3?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">회사 팩스번호</TD>
					<TD class="td_con1">
					<input type=text name=up_com_fax1 value="<?=$up_com_fax1?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_com_fax2 value="<?=$up_com_fax2?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_com_fax3 value="<?=$up_com_fax3?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">회사 홈페이지</TD>
					<TD class="td_con1">
					http://<input type=text name=up_com_homepage value="<?=$up_com_homepage?>" size=30 maxlength=50 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_reg_stitle3.gif"  ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
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
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">담당자 이름</TD>
					<TD class="td_con1">
					<input type=text name=up_p_name value="<?=$up_p_name?>" size=20 maxlength=20 class=input> &nbsp; <FONT class=font_orange>* 입점 담당자 이름을 정확히 입력하세요.</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">담당자 휴대전화</TD>
					<TD class="td_con1">
					<input type=text name=up_p_mobile1 value="<?=$up_p_mobile1?>" size=4 maxlength=3 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_p_mobile2 value="<?=$up_p_mobile2?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_p_mobile3 value="<?=$up_p_mobile3?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">담당자 이메일</TD>
					<TD class="td_con1">
					<input type=text name=up_p_email value="<?=$up_p_email?>" size=30 maxlength=50 class=input> &nbsp; <FONT class=font_orange>* 주문확인시 담당자 이메일로 통보됩니다.</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">담당자 부서명</TD>
					<TD class="td_con1">
					<input type=text name=up_p_buseo value="<?=$up_p_buseo?>" size=20 maxlength=20 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">담당자 직위</TD>
					<TD class="td_con1">
					<input type=text name=up_p_level value="<?=$up_p_level?>" size=20 maxlength=20 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_reg_stitle4.gif" ALT=""></TD>
					<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
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
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<? if ($account_rule !="1") { ?>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">수수료 운영형태</TD>
					<TD class="td_con1">
					<input type=radio name=up_commission_type id=up_commission_type0 value="1" <?if($up_commission_type=="1") echo"checked";?> onclick="selCommission('0');"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_commission_type0>상품개별 수수료</label>
					<img width=20 height=0>
					<input type=radio name=up_commission_type id=up_commission_type1 value="0" <?if($up_commission_type=="0" || strlen($up_commission_type)==0) echo "checked";?>  onclick="selCommission('1');"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_commission_type1>전체상품 동일 수수료</label>
					<br><span class="font_blue">&nbsp;* 입점사와의 협정 판매수수료는 부가세 별도로 정산식 처리 시 산정됩니다.(입점사의 경우 과세, 간이과세, 면세, 개인사업자 등 복합적으로 입점가능하므로 판매수수료의 부가세는 별도로 정산시 산정하도록 설계되어 있음.)</span>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR id="commission_all">
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">전체 수수료</TD>
					<TD class="td_con1">
						<input type=text name=up_rate value="<?=$up_rate?>" size=3 maxlength=3 onkeyup="strnumkeyup(this)" class=input>%
						&nbsp;&nbsp;&nbsp;&nbsp; <FONT class=font_orange>* 모든상품에 동일 적용됩니다.</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<? } else { ?>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">수수료 운영형태</TD>
					<TD class="td_con1">
					<input type=hidden name=up_commission_type value="1" />
					<input type=hidden name=up_rate value="0" />
					상품개별 공급가
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<? } ?>

				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품 처리 권한</TD>
					<TD class="td_con1">
					<input type=checkbox name=chk_prdt1 id=idx_chk_prdt1 value="Y" <?if($chk_prdt1=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_chk_prdt1>등록</label>
					<img width=20 height=0>
					<input type=checkbox name=chk_prdt2 id=idx_chk_prdt2 value="Y" <?if($chk_prdt2=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_chk_prdt2>수정</label>
					<img width=20 height=0>
					<input type=checkbox name=chk_prdt3 id=idx_chk_prdt3 value="Y" <?if($chk_prdt3=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_chk_prdt3>삭제</label>
					<img width=50 height=0>
					<input type=checkbox name=chk_prdt4 id=idx_chk_prdt4 value="Y" <?if($chk_prdt4=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_chk_prdt4>등록/수정시, 관리자 인증</label>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">입점 상품수 제한</TD>
					<TD class="td_con1">
						<?if(strlen($up_product_max)==0)$up_product_max="50";?>
						<input type=text name=up_product_max value="<?=$up_product_max?>" size=3 onkeyup="strnumkeyup(this)" class=input>
						개 까지 상품등록 가능
						<br/>
						<FONT class=font_orange>* 무제한으로 설정할 시에는 <span class="font_blue">0</span>을 입력해주세요.</font>
						<!--
						<select name=up_product_max class="select">
						<option value="0" <?if($up_product_max==0)echo"selected";?>>무제한</option>
						<option value="50" <?if($up_product_max==50)echo"selected";?>>50</option>
						<option value="100" <?if($up_product_max==100)echo"selected";?>>100</option>
						<option value="150" <?if($up_product_max==150)echo"selected";?>>150</option>
						<option value="200" <?if($up_product_max==200)echo"selected";?>>200</option>
						<option value="250" <?if($up_product_max==250)echo"selected";?>>250</option>
						<option value="300" <?if($up_product_max==300)echo"selected";?>>300</option>
						</select> 개 까지 상품등록 가능
						-->
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<!--
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">판매 수수료</TD>
					<TD class="td_con1">
						<input type=text name=up_rate value="<?=$up_rate?>" size=3 maxlength=3 onkeyup="strnumkeyup(this)" class=input>%
						&nbsp;&nbsp;&nbsp;&nbsp; <FONT class=font_orange>* 쇼핑몰 본사에서 받는 상품판매 수수료를 입력하세요.</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				-->
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">정산 계좌정보</TD>
					<TD class="td_con1">
						은행
						<select name="up_bank1" class=input>
							<?
								$bankinfoArray  = calcSetBankinfo();
								foreach ( $bankinfoArray as $k => $v ){
									if( $up_bank1 == $v ) {
										$sel = "selected";
									} else{
										$sel = "";
									}
									echo "<option value='".$v."'  ".$sel.">".$v."</option>";
								}
							?>
						</select>
						<img width=20 height=0>
						계좌번호 <input type=text name=up_bank2 value="<?=$up_bank2?>" size=20 class=input>
						<img width=20 height=0>
						예금주 <input type=text name=up_bank3 value="<?=$up_bank3?>" size=15 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">정산일(매월)</TD>
					<TD class="td_con1">

						<input type="radio" name="adjust_lastday" id="adjust_lastday_0" value="0" onclick="adjustChecked('0')" checked="checked"> <label for="adjust_lastday_0">직접지정</label>&nbsp;&nbsp;
						<input type="radio" name="adjust_lastday" id="adjust_lastday_1" value="1" onclick="adjustChecked('1')"> <label for="adjust_lastday_0">매월마지막일</label>&nbsp;&nbsp;
						<input type="radio" name="adjust_lastday" id="adjust_lastday_2" value="2" onclick="adjustChecked('2')"> <label for="adjust_lastday_0">15일과 매월마지막일</label>

						<div id="adjust_div">
						<input type=text name=up_account_date value="<?=$up_account_date?>" size=75 class=input>일
						&nbsp;&nbsp;&nbsp;&nbsp;
						<span style="color:#ffffff;background-color:#000000;padding:2px 5px;cursor:pointer" onclick="setAccountDate(0)">매일</span>&nbsp;
						<span style="color:#ffffff;background-color:#000000;padding:2px 5px;cursor:pointer" onclick="setAccountDate(2)">짝수격일</span>&nbsp;
						<span style="color:#ffffff;background-color:#000000;padding:2px 5px;cursor:pointer" onclick="setAccountDate(1)">홀수격일</span>
						<br/>
						<FONT class=font_orange>* (복수기입시 10,20,30 과 같이 기입요망, 단일 기입시 29,30,31은 사용할수 없음.)</font>
						</div>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
			<? /* 추가 jdy */?>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">결산일</TD>
					<TD class="td_con1">
						<input type=text name=up_close_date value="<?=$up_close_date?>" size=10 class=input onkeyup="strnumkeyup(this)" >일
						&nbsp;&nbsp;&nbsp;&nbsp; <FONT class=font_orange>* (정산일보다 1주일전까지 주문을 정산할 경우 7을 입력. 1이상 입력)</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>

				<? if ($reserve_use || $coupon_use) {?>
				<tr>
					<td class="table_cell"><img src="images/icon_point5.gif" border="0">혜택 사용 여부</td>
					<td class="td_con1">

						<? if ($reserve_use) {?>
						<b>적립금 : </b>
						<input type=radio name=up_reserve_use id=up_reserve_use1 value="1" <?if($up_reserve_use=="1")echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_reserve_use1>사용</label>
						<img width=20 height=0>
						<input type=radio name=up_reserve_use id=up_reserve_use0 value="0" <?if($up_reserve_use=="0" || strlen($up_reserve_use)==0)echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_reserve_use0>사용 안함</label>
						<br/>
						<? } ?>

						<? if ($coupon_use) {?>
						<b>쿠폰 : </b>
						<input type=radio name=up_coupon_use id=up_coupon_use1 value="1" <?if($up_coupon_use=="1")echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_coupon_use1>사용</label>
						<img width=20 height=0>
						<input type=radio name=up_coupon_use id=up_coupon_use0 value="0" <?if($up_coupon_use=="0" || strlen($up_coupon_use)==0)echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_coupon_use0>사용 안함</label>
						<br/>
						<? } ?>
						<span class="font_blue">
						* 사용불가 체크 시 입점사는 혜택을 사용할 수 없으며 해당메뉴가 입점사 관리모드에 노출되지 않습니다.
						</span>
					</td>
				</tr>
				<tr>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</tr>
				<? } ?>

				<? if (!$reserve_use) { ?>
					<input type=hidden name=up_reserve_use value="0" />
				<? } ?>
				<? if (!$coupon_use) { ?>
					<input type=hidden name=up_coupon_use value="0" />
				<? } ?>

			<? /* 추가 jdy */?>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">기타사항</TD>
					<TD class="td_con1">
						<textarea name="up_etc" cols="80" rows="5" ><?= $up_etc ?></textarea>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">관리자메모</TD>
					<TD class="td_con1">
						<textarea name="up_admin_memo" cols="80" rows="5" ><?= $up_admin_memo ?></textarea>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>

				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" width="113" height="38" border="0"></a></td>
			</tr>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif">&nbsp;</TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" class="menual_bg" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td ><span class="font_dotline">입점업체 신규등록</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td  class="space_top" style="letter-spacing:-0.5pt;"><p>- 신규 입점업체 등록페이지 입니다.</p></td>
					</tr>
					<tr>
						<td  class="space_top"><span style="padding-left:13px">- 등록된 입점사 관리는 <a href="javascript:parent.topframe.GoMenu(1,'vender_management.php');"><span class="font_blue">입점관리 > 입점업체 관리 > 입점업체 정보관리</span></a> 페이지에서 관리합니다.</td>
					</tr>
					<tr><td height="20"></td></tr>




					<tr>
						<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">정산관련 용어정리</span></td>
					</tr>
					<tr>
						<td  class="space_top"><span style="padding-left:13px">- 정산금액 : 설정한 정산산정기간 동안의 입점업체 배송완료상품의 총 매출에서 판매수수료, 입점사지급 적립금, 쿠폰혜택을 빼고 배송료를 더한 금액을 산출한 실 결제금액<br/>
						<span style="padding-left:13px">- 정산기준일 : 거래된 매출 중 정산금액이 산정되는 기간<br/>
						<span style="padding-left:13px">- 결산일 : 정산기준일의 마지막 날짜(마감일)<br/>
						<span style="padding-left:13px">- 정산일 : 정산기준일의 정산금액을 입점업체에게 결제(입금)하는 날짜<br/>
						<span style="padding-left:13px">- 정산조회일 : 정산금액을 조회하는 날짜
						</td>
					</tr>
					<tr><td height="20"></td></tr>

					<tr>
						<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">정산산출 예) </span></td>
					</tr>
					<tr>
						<td  class="space_top">
						<span style="padding-left:13px">* A업체가 정산일이 매월10일 1회 이고, 결산일이 정산일로 부터 5일 이전인 경우<br/>
						<span style="padding-left:13px">- 정산기준일 : 이전달 6일 ~ 이번달5일<br/>
						<span style="padding-left:13px">- 결산일 : 매달 5일
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">복수정산 응용산출 예) </span></td>
					</tr>
					<tr>
						<td  class="space_top">
						<span style="padding-left:13px">* B업체가 정산일이 매월 5일, 10일, 15일, 20일, 25일, 30일  6회 이고, 결산일이 정산일로 부터 5일 이전인 경우<br/>
						<span style="padding-left:13px">- 정산기준일 : 지난달 26일~지난달 말일(5일 정산), 이번달 1일~이번달 5일(10일 정산), 6일~10일(15일 정산), 11일~15일(20일 정산), 16일~20일(25일 정산), 21일~ 25일(30일 정산)<br/>
						<span style="padding-left:13px">- 결산일 : 매달 말일, 5일, 10일, 15일, 20일, 25일
						</td>
					</tr>
					<tr><td height="20"></td></tr>


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
