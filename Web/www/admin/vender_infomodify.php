<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
INCLUDE ("access.php");
include_once($Dir."lib/admin_more.php");
include_once($Dir.'service/subMallIche/bankcode.php');

####################### ������ ���ٱ��� check ###############
$PageCode = "vd-1";
$MenuCode = "vender";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("AccessDeny.inc.php");
	exit;
}
#########################################################

$vender=$_POST["vender"];

$sql = "SELECT a.*, b.brand_name FROM tblvenderinfo a, tblvenderstore b ";
$sql.= "WHERE a.vender='".$vender."' AND a.delflag='N' AND a.vender=b.vender ";
$result=mysql_query($sql,get_db_conn());
if(!$_vdata=mysql_fetch_object($result)) {
	echo "<script>alert('�ش� ��ü ������ �������� �ʽ��ϴ�.');history.go(-1);</script>";
	exit;
}
mysql_free_result($result);

$com_tel=explode("-",$_vdata->com_tel);
$com_fax=explode("-",$_vdata->com_fax);
$p_mobile=explode("-",$_vdata->p_mobile);
$bank_account=explode("=",$_vdata->bank_account);


/* ������ ���� �߰� jdy */
$sql = "SELECT * FROM vender_more_info ";
$sql.= "WHERE vender='".$vender."'";
$result=mysql_query($sql,get_db_conn());

$_vmdata=mysql_fetch_object($result);

mysql_free_result($result);
/* ������ ���� �߰� jdy */

// ���� ���� ��ȸ jdy
$shop_more_info = getShopMoreInfo();
$account_rule = $shop_more_info['account_rule'];

$reserve_use = $shop_more_info['reserve_use'];
$coupon_use = $shop_more_info['coupon_use'];
// ���� ���� ��ȸ jdy


$type=$_POST["type"];
if($type=="update") {

	$up_disabled=$_POST["up_disabled"];
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
	$com_nametech=$_POST["com_nametech"];


	/* ������ ���� �߰� jdy */
	$up_commission_type = $_POST["up_commission_type"];
	$up_etc = $_POST["up_etc"];
	$up_admin_memo = $_POST["up_admin_memo"];

	$up_close_date = $_POST["up_close_date"];


	$up_reserve_use=$_POST["up_reserve_use"];
	$up_coupon_use=$_POST["up_coupon_use"];

	$up_history = "";
	$updateChk = "";

	$up_adjust_lastday=$_POST["adjust_lastday"];

	$up_all_rate = $_POST["up_all_rate"];

	if ($account_rule != "1") {
	//�Ǹ� ������� ��� ������ ���濡���� ����� ���� ex) ��ü-> ����

		//$_vmdata ���� ������ �������� ����

		if ($_vmdata->vender) {
			if ($up_commission_type!=($_vmdata->commission_type)) {

				if ($_vmdata->commission_type == "1") {
					$up_history = "���������� -> ��ü������ ".$up_rate."%�� ���� [�����]";
				}else{

					$up_history = "��ü������ ".$_vdata->rate."% -> ����������� ���� [�����]";
					$up_rate = 0;
				}
				$updateChk = "1";
			}else{

				if ($_vmdata->commission_type != '') {
					if ($up_commission_type != "1") {

						if ($up_rate !=$_vdata->rate) {
							$up_history = "��ü������ ".$_vdata->rate."% -> ".$up_rate."% �� ���� [�����]";
							$updateChk = "1";
						}

					}else{
						$up_rate = 0;
					}
				}
			}
		}
	}else{
	//���ް��� ��� ��� ������ ���� ���ް� ���.

		$up_commission_type = 1;
		$up_rate = 0;
	}
	/* ������ ���� �߰� jdy */

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
	if(strlen($up_com_name)==0) {
		$error="ȸ����� �Է��ϼ���.";
	} else if(strlen($up_com_num)==0) {
		$error="����ڵ�Ϲ�ȣ�� �Է��ϼ���.";
	} else if(strlen($up_brand_name)==0) {
		$error="�̴ϼ����� �Է��ϼ���.";
	} else if(chkBizNo($up_com_num)==false) {
		$error="����ڵ�Ϲ�ȣ�� ��Ȯ�� �Է��ϼ���.";
	} else if(strlen($up_com_tel)==0) {
		$error="ȸ�� ��ǥ��ȭ�� ��Ȯ�� �Է��ϼ���.";
	} else if(strlen($up_p_name)==0) {
		$error="����� �̸��� �Է��ϼ���.";
	} else if(strlen($up_p_mobile)==0) {
		$error="����� �޴���ȭ�� ��Ȯ�� �Է��ϼ���.";
	} else if(strlen($up_p_email)==0) {
		$error="����� �̸����� �Է��ϼ���.";
	} else if(ismail($up_p_email)==false) {
		$error="����� �̸����� ��Ȯ�� �Է��ϼ���.";
	} else if(strlen($up_close_date)==0) {
		/* �߰� jdy */
		$error="������� �Է����ּ���.";
		/* �߰� jdy */
	}

	if(strlen($error)==0) {
		$sql = "SELECT brand_name FROM tblvenderstore WHERE vender!='".$vender."' AND brand_name='".$up_brand_name."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			$error="�̴ϼ����� �ߺ��Ǿ����ϴ�.";
		}
		mysql_free_result($result);

		if(strlen($error)==0) {


			$sql = "UPDATE tblvenderinfo SET ";
			if(strlen($up_passwd)>0) {
				$sql.= "passwd			= '".md5($up_passwd)."', ";
			}
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
			$sql.= "ec_num			= '".$ec_num."', ";
			$sql.= "com_nametech	= '".$com_nametech."' ";

			// ������ �̹��� ���
			if( $_FILES['com_image']['error'] == 0 AND $_FILES['com_image']['size'] > 0 AND eregi("image",$_FILES['com_image']['type']) AND $_POST['com_image_del'] != "OK" ) {
				$exte = explode(".",$_FILES['com_image']['name']);
				$exte = $exte[ count($exte)-1 ];
				$com_image_name = "comImgae_".date("YmdHis").".".$exte;
				move_uploaded_file($_FILES['com_image']['tmp_name'],$com_image_url.$com_image_name);
				$sql.= ", com_image = '".$com_image_name."' ";
			}

			//������ �̹��� ����
			if( $_POST['com_image_del'] == "OK" AND strlen($_POST['com_image_del_file']) > 0 ) {
				unlink($_POST['com_image_del_file']);
				$sql.= ", com_image = '' ";
			}


			$sql.= "WHERE vender='".$vender."' ";
			if(mysql_query($sql,get_db_conn())) {
				if($_vdata->brand_name!=$up_brand_name) {
					$sql = "UPDATE tblvenderstore SET ";
					$sql.= "brand_name	= '".$up_brand_name."' ";
					$sql.= "WHERE vender='".$vender."' ";
					mysql_query($sql,get_db_conn());
				}


				/* ������ ���� �߰� jdy */
				$sql = "select * from vender_more_info WHERE vender='".$vender."' ";
				$result = mysql_query($sql,get_db_conn());
				$data_lows = mysql_num_rows($result);
				$_vmdata=mysql_fetch_object($result);
				mysql_free_result($result);

				if ($data_lows > 0) {

					$sql = "UPDATE vender_more_info SET ";
					$sql.= "commission_type	= '".$up_commission_type."', ";

					if ($updateChk=="1") {
						$sql.= "commission_status = '0', ";
					}

					$sql.= "close_date				= '".$up_close_date."', ";
					$sql.= "etc				= '".$up_etc."', ";
					$sql.= "admin_memo		= '".$up_admin_memo."', ";
					$sql.= "reserve_use		= '".$up_reserve_use."', ";
					$sql.= "coupon_use		= '".$up_coupon_use."', ";
					$sql.= "adjust_lastday		= '".$up_adjust_lastday."' ";

					$sql.= "WHERE vender='".$vender."' ";
					mysql_query($sql,get_db_conn());

					if ($_vmdata->close_date != $up_close_date ) {
						$close_history = "����� ".$_vmdata->close_date."�Ͽ��� ".$up_close_date."�Ϸ� ����";
					}

				}else{

					$sql = "INSERT vender_more_info SET ";
					$sql.= "vender			= '".$vender."', ";
					$sql.= "commission_type	= '".$up_commission_type."', ";
					$sql.= "rq_commission_type	= '0', ";
					$sql.= "rq_rate	= '0', ";
					$sql.= "commission_status = '0', ";
					$sql.= "etc				= '".$up_etc."', ";
					$sql.= "admin_memo		= '".$up_admin_memo."', ";
					$sql.= "reserve_use		= '".$up_reserve_use."', ";
					$sql.= "close_date				= '".$up_close_date."', ";
					$sql.= "coupon_use		= '".$up_coupon_use."', ";
					$sql.= "adjust_lastday	= '".$up_adjust_lastday."' ";
					mysql_query($sql,get_db_conn());
				}

				if ($up_history !="") {
					$sql = "insert commission_history set ";
					$sql.= "vender	= '".$vender."', ";
					$sql.= "memo	= '".$up_history."', ";
					$sql.= "`type`	= '1', ";
					$sql.= "admin_id	= '".$_usersession->id."', ";
					$sql.= "reg_date	= now() ";

					mysql_query($sql,get_db_conn());
				}

				if ($close_history !="") {
					$sql = "insert commission_history set ";
					$sql.= "vender	= '".$vender."', ";
					$sql.= "memo	= '".$close_history."', ";
					$sql.= "`type`	= '1', ";
					$sql.= "admin_id	= '".$_usersession->id."', ";
					$sql.= "reg_date	= now() ";

					mysql_query($sql,get_db_conn());
				}

				$ad_his_chk = 0;

				if ($up_adjust_lastday==0 && $_vdata->account_date != $up_account_date) {
					$ad_his = "������ ".$_vdata->account_date."�Ͽ��� ".$up_account_date."�Ϸ� ����";
					$ad_his_chk++;
				}

				if ($_vmdata->adjust_lastday != $up_adjust_lastday) {

					$ad_his = "";
					switch($_vmdata->adjust_lastday) {
						case 0 : $ad_his = "�������� ������������ ";
							break;
						case 1 : $ad_his = "�������� �ſ��������Ͽ��� ";
							break;
						case 2 : $ad_his = "�������� 15�ϰ� �ſ��������Ͽ��� ";
							break;
					}

					switch($up_adjust_lastday) {
						case 0 : $ad_his .= "������������ ���� ( ".$up_account_date." ) ";
							break;
						case 1 : $ad_his .= "�ſ��������Ϸ� ����";
							break;
						case 2 : $ad_his .= "15�ϰ� �ſ��������Ϸ� ����";
							break;
					}

					$ad_his_chk++;
				}

				if ($ad_his_chk>0) {

					$sql = "insert commission_history set ";
					$sql.= "vender	= '".$vender."', ";
					$sql.= "memo	= '".$ad_his."', ";
					$sql.= "`type`	= '1', ";
					$sql.= "admin_id	= '".$_usersession->id."', ";
					$sql.= "reg_date	= now() ";

					mysql_query($sql,get_db_conn());

				}

				if ($up_commission_type == "1") {
					if ($up_all_rate>0) {
						setProductCommissionAll($vender, $up_all_rate, $_usersession->id);
					}
				}

				/* ������ ���� �߰� jdy */


				$log_content = "## ������ü ���� ���� ## - ��üID : ".$_vdata->id;
				ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);


				echo "<html></head><body onload=\"alert('��ü���� ������ �Ϸ�Ǿ����ϴ�.');parent.document.form3.submit();\"></body></html>";exit;
			} else {
				$error="������ü ����� ������ �߻��Ͽ����ϴ�.";
			}
		}
	}
	if(strlen($error)>0) {
		echo "<html></head><body onload=\"alert('".$error."');\"></body></html>";exit;
	}
} else if($type=="delete" && ($_POST["delete_gbn"]=="Y" || $_POST["delete_gbn"]=="N")) {
	$delete_gbn=$_POST["delete_gbn"];
	$sql = "SELECT COUNT(*) as cnt FROM tblorderproduct WHERE vender='".$vender."' ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	mysql_free_result($result);
	$cnt=$row->cnt;

	$sql="UPDATE tblshopcount SET vendercnt=vendercnt-1 ";
	mysql_query($sql,get_db_conn());

	if($cnt<=0) {
		mysql_query("DELETE FROM tblvenderinfo WHERE vender='".$vender."'",get_db_conn());
	} else {
		$sql = "UPDATE tblvenderinfo SET delflag='Y' ";
		$sql.= "WHERE vender='".$vender."' ";
		mysql_query($sql,get_db_conn());
	}
	mysql_query("DELETE FROM tblvenderstore WHERE vender='".$vender."'",get_db_conn());
	mysql_query("DELETE FROM tblvenderstorecount WHERE vender='".$vender."'",get_db_conn());
	mysql_query("DELETE FROM tblvenderstorevisit WHERE vender='".$vender."'",get_db_conn());
	mysql_query("DELETE FROM tblvendercodedesign WHERE vender='".$vender."'",get_db_conn());
	mysql_query("DELETE FROM tblregiststore WHERE vender='".$vender."'",get_db_conn());
	mysql_query("DELETE FROM tblvenderlog WHERE vender='".$vender."'",get_db_conn());
	mysql_query("DELETE FROM tblvenderthemecode vender='".$vender."'",get_db_conn());
	mysql_query("DELETE FROM tblvenderthemeproduct WHERE vender='".$vender."'",get_db_conn());
	mysql_query("DELETE FROM tblvenderspecialmain WHERE vender='".$vender."'",get_db_conn());
	mysql_query("DELETE FROM tblvenderspecialcode WHERE vender='".$vender."'",get_db_conn());
	mysql_query("DELETE FROM tblvendernotice WHERE vender='".$vender."'",get_db_conn());
	mysql_query("DELETE FROM tblvenderadminnotice WHERE vender='".$vender."'",get_db_conn());
	mysql_query("DELETE FROM tblvenderadminqna WHERE vender='".$vender."'",get_db_conn());
	mysql_query("DELETE FROM tblvenderaccount WHERE vender='".$vender."'",get_db_conn());
	mysql_query("DELETE FROM tblregiststore WHERE vender='".$vender."'",get_db_conn());

	/* ������ ���� �߰� jdy */
	mysql_query("DELETE FROM vender_more_info WHERE vender='".$vender."'",get_db_conn());
	/* ������ ���� �߰� jdy */

	mysql_query("optimize table tblvenderstorevisit");
	mysql_query("optimize table tblvenderlog");
	mysql_query("optimize table tblregiststore");

	//�̹��� ���� ����
	$storeimagepath=$Dir.DataDir."shopimages/vender/";
	proc_matchfiledel($storeimagepath."MAIN_".$vender.".*");
	proc_matchfiledel($storeimagepath."logo_".$vender.".*");
	proc_matchfiledel($storeimagepath.$vender."*");
	proc_matchfiledel($storeimagepath."aboutdeliinfo_".$vender."*");

	if($delete_gbn=="Y") {			//��ü ��ǰ ���� ����
		$sql = "SELECT productcode FROM tblproduct WHERE vender='".$vender."' ";
		$result=mysql_query($sql,get_db_conn());
		while($row=mysql_fetch_object($result)) {
			$prcode=$row->productcode;
			#�±װ��� �����
			$sql = "DELETE FROM tbltagproduct WHERE productcode = '".$prcode."'";
			mysql_query($sql,get_db_conn());

			#���� �����
			$sql = "DELETE FROM tblproductreview WHERE productcode = '".$prcode."'";
			mysql_query($sql,get_db_conn());

			#���ø���Ʈ �����
			$sql = "DELETE FROM tblwishlist WHERE productcode = '".$prcode."'";
			mysql_query($sql,get_db_conn());

			#���û�ǰ �����
			$sql = "DELETE FROM tblcollection WHERE productcode = '".$prcode."'";
			mysql_query($sql,get_db_conn());

			$sql = "DELETE FROM tblproducttheme WHERE productcode = '".$prcode."'";
			mysql_query($sql,get_db_conn());

			$sql = "DELETE FROM tblproduct WHERE productcode = '".$prcode."'";
			mysql_query($sql,get_db_conn());

			$sql = "DELETE FROM tblproductgroupcode WHERE productcode = '".$prcode."'";
			mysql_query($sql,get_db_conn());

			$delshopimage = $Dir.DataDir."shopimages/product/".$prcode."*";
			proc_matchfiledel($delshopimage);

			delProductMultiImg("prdelete","",$prcode);
		}
		mysql_free_result($result);

		$log_content = "## ������ü ���� ## - ��üID : ".$_vdata->id." , [��ü��ǰ ����]";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	} else if($delete_gbn=="N") {	//��ü ��ǰ ���θ� ���� ��ǰ���� ����
		$sql = "UPDATE tblproduct SET vender=0 ";
		$sql.= "WHERE vender='".$vender."' ";
		mysql_query($sql,get_db_conn());

		$log_content = "## ������ü ���� ## - ��üID : ".$_vdata->id." , [��ü��ǰ �����ǰ���� ����]";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
	}

	echo "<html></head><body onload=\"alert('�ش� ������ü ������ ������ �����Ǿ����ϴ�.');parent.document.form3.submit();\"></body></html>";exit;
}

$disabled=$_POST["disabled"];
$s_check=$_POST["s_check"];
$search=$_POST["search"];
$block=$_POST["block"];
$gotopage=$_POST["gotopage"];

?>

<? INCLUDE "header.php"; ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	form=document.form1;
	if(form.up_disabled[0].checked!=true && form.up_disabled[1].checked!=true) {
		alert("��ü ���ο��θ� �����ϼ���.");
		form.up_disabled[0].focus();
		return;
	}
	if(form.up_passwd.value.length>0) {
		if(form.up_passwd.value!=form.up_passwd2.value) {
			alert("������ ��й�ȣ�� ��ġ���� �ʽ��ϴ�."); form.up_passwd2.focus(); return;
		}
	}
	if(form.up_com_name.value.length==0) {
		alert("ȸ����� �Է��ϼ���."); form.up_com_name.focus(); return;
	}
	if(form.up_com_num.value.length==0) {
		alert("����ڵ�Ϲ�ȣ�� �Է��ϼ���."); form.up_com_num.focus(); return;
	}
	if(chkBizNo(form.up_com_num.value)==false) {
		alert("����ڵ�Ϲ�ȣ�� �߸��Ǿ����ϴ�."); form.up_com_num.focus(); return;
	}
	if(form.up_com_tel1.value.length==0 || form.up_com_tel2.value.length==0 || form.up_com_tel3.value.length==0) {
		alert("ȸ�� ��ǥ��ȭ�� ��Ȯ�� �Է��ϼ���."); form.up_com_tel1.focus(); return;
	}
	if(form.up_p_name.value.length==0) {
		alert("����� �̸��� �Է��ϼ���."); form.up_p_name.focus(); return;
	}
	if(form.up_p_mobile1.value.length==0 || form.up_p_mobile2.value.length==0 || form.up_p_mobile3.value.length==0) {
		alert("����� �޴���ȭ�� ��Ȯ�� �Է��ϼ���."); form.up_p_mobile1.focus(); return;
	}
	if(form.up_p_email.value.length==0) {
		alert("����� �̸����� �Է��ϼ���."); form.up_p_email.focus(); return;
	}
	if(IsMailCheck(form.up_p_email.value)==false) {
		alert("����� �̸����� ��Ȯ�� �Է��ϼ���."); form.up_p_email.focus(); return;
	}

	if(form.up_account_date.value=='29' || form.up_account_date.value=='30' || form.up_account_date.value=='31') {
		alert("�����Ϸ� ����� �� ���� ��¥ �Դϴ�."); form.up_account_date.focus(); return;
	}

	all_rate = document.getElementById("up_all_rate");
	up_c_type0 = document.getElementById("up_commission_type0");

	if (up_c_type0.checked) {
		if (typeof all_rate !="undefined") {

			if (all_rate.value != '' && Number(all_rate.value)>0) {

				if(!confirm("���� �����Ḧ "+all_rate.value+"% �� �����մϴ�. ����Ͻðڽ��ϱ�?")) {
					return;
				}
			}
		}
	}

	if(confirm("������ü ������ �����Ͻðڽ��ϱ�?")) {
		document.form1.type.value="update";
		document.form1.target="processFrame";
		document.form1.submit();
	}
}

function GoReturn() {
	document.form3.submit();
}

function CheckDelete() {
	if(confirm("�ش� ��ü�� ���� �����Ͻðڽ��ϱ�?")) {
		if(confirm("�ش� ��ü�� ��ǰ�� ���� �����Ͻðڽ��ϱ�?\n\n��ü ��ǰ�� ���� ������ ��� [Ȯ��]\n\n��ü ��ǰ�� ���θ� ���� ��ǰ���� �����Ͻ÷��� [���] ��ư�� ��������.")) {
			if(confirm("���� �ش� ��ü�� ��ǰ�� ��� �����Ͻðڽ��ϱ�?")) {
				document.form1.delete_gbn.value="Y";
				document.form1.type.value="delete";
				document.form1.target="processFrame";
				document.form1.submit();
			}
		} else {
			if(confirm("���� �ش� ��ü ���� �� ��ü ��ǰ�� ���θ� ���� ��ǰ���� �����Ͻðڽ��ϱ�?")) {
				document.form1.delete_gbn.value="N";
				document.form1.type.value="delete";
				document.form1.target="processFrame";
				document.form1.submit();
			}
		}
	}
}

function branddup(vender) {
	brand=document.form1.up_brand_name;
	if(brand.value.length==0) {
		alert("�̴ϼ����� �Է��ϼ���.");
		brand.focus();
		return;
	}
	window.open("vender_branddup.php?vender="+vender+"&brand_name="+brand.value,"","height=100,width=300,toolbar=no,menubar=no,scrollbars=no,status=no");
}

function f_addr_search(form,post,addr,gbn) {
	window.open("<?=$Dir.FrontDir?>addr_search.php?form="+form+"&post="+post+"&addr="+addr+"&gbn="+gbn,"f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");
}

function selCommission(num) {

	c_td = document.getElementById("commission_all")
	c_tr = document.getElementById("commission_tr")

	if (num==1) {
		c_td.style.display="inline"
		if ( typeof c_tr != "undefined" ) {
			c_tr.style.display="none"
		}
	}else{
		c_td.style.display="none"
		if ( typeof c_tr != "undefined" ) {
			c_tr.style.display="inline"
		}
	}

	if (num==0) {
		alert("�ʱ� ������ǰ ������� 0%�� �����Ǵ� �ݵ�� �Ʒ� ���������� �ϰ����������� ������ ���� �� �� ��ǰ�� �����Ḧ �����Ͻñ� �ٶ��ϴ�.\n��������ῡ ���� ����ݿ��� �Ѱ����ڰ� �ش��ǰ�ֹ��ܰ踦 ��ۿϷ�� ó���� ������ ����˴ϴ�.");
	}
}

function autoResize(ifr)
{

    var iframeHeight=ifr.contentWindow.document.body.scrollHeight;
    ifr.height=iframeHeight+20;

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
				<td height="28" class="link" align="left" background="images/con_link_bg.gif"><img src="images/top_link_house.gif" border="0" valign="absmiddle">������ġ : �������� &gt; ������ü ���� &gt; <span class="2depth_select">������ü �űԵ��</span></td>
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
			<input type=hidden name=delete_gbn>
			<input type=hidden name=vender value="<?=$vender?>">
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
					<TD width="100%" class="notice_blue">������ü�� ������ ����/���� �Ͻ� �� �ֽ��ϴ�.</TD>
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
					<TD><IMG SRC="images/vender_reg_stitle1.gif" HEIGHT=31 ALT=""></TD>
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
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>

				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">��ü ����</TD>
					<TD class="td_con1">
					<input type=radio name=up_disabled id=up_disabled0 value="0" <?if($_vdata->disabled=="0")echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_disabled0>����</label>
					<img width=20 height=0>
					<input type=radio name=up_disabled id=up_disabled1 value="1" <?if($_vdata->disabled=="1" || strlen($_vdata->disabled)==0)echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_disabled1>����</label>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">��ü ID</TD>
					<TD class="td_con1"><B><?=$_vdata->id?></B></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">�н����� ����</TD>
					<TD class="td_con1">
					<input type=password name=up_passwd value="" size=20 maxlength=12 class=input>
					&nbsp;&nbsp;
					<FONT class=font_orange>* ����, ���ڸ� ȥ���Ͽ� ���(4�� ~ 12��)</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">�н����� Ȯ��</TD>
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
					<TD><IMG SRC="images/vender_reg_stitle2.gif" HEIGHT=31 ALT=""></TD>
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
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">������ �̹���</TD>
					<TD class="td_con1">

						<div style="margin:5px;">
							<div style="float:left; margin:0px; padding:0px; font-size:0px;"><img src="<?=$com_image_url.$_vdata->com_image?>" width="120" onerror="this.src='/images/no_img.gif';" style="border:1px solid #dddddd;" /></div>
							<div style="float:left; margin-top:5px; margin-left:10px;">
								<div>
									<span style="font-size:11px; color:#666666; line-height:15px; letter-spacing:-1px;">
										<strong>������� : </strong><input type="checkbox" name="com_nametech" value="1" <?=($_vdata->com_nametech?"checked":"");?>><br /><br /><br />
										�� <b>������ �̹�����??</b><br />
										<img src="images/vender_nametek_sample.gif" style="border:1px solid #e5e5e5;" hspace="8" vspace="4" alt="" /><br />
										- ������ �̹����� ��ǰ ��� �� �� ���������� ������ ���� ��½� ���Ǵ� �̹��� �Դϴ�.<br />
										- �̹��� ������� ����*���� 100px * 100px �� ����帳�ϴ�.
									</span>
								</div>
								<div style="margin-top:10px;">
									<input type="file" name="com_image" id="com_image">
									<input type="checkbox" name="com_image_del" id="com_image_del" value="OK" onclick="com_image.style.display=( this.checked ?'none':'inline')"><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="com_image_del">����</label>
									<input type="hidden" name="com_image_del_file" value="<?=$com_image_url.$_vdata->com_image?>">
								</div>
							</div>
						</div>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">��ȣ (ȸ���)</TD>
					<TD class="td_con1">
					<input type=text name=up_com_name value="<?=$_vdata->com_name?>" size=20 maxlength=30 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">����ڵ�Ϲ�ȣ</TD>
					<TD class="td_con1">
					<input type=text name=up_com_num value="<?=$_vdata->com_num?>" size=20 maxlength=20 onkeyup="strnumkeyup(this)" class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">�̴ϼ���</TD>
					<TD class="td_con1">
					<input type=text name=up_brand_name value="<?=$_vdata->brand_name?>" size=20 maxlength=30 class=input>
					<A class=board_list hideFocus style="selector-dummy: true" onfocus=this.blur(); href="javascript:branddup();"><IMG src="images/duple_check_img.gif" border=0 align="absmiddle"></A>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">��ǥ�� ����</TD>
					<TD class="td_con1">
					<input name=up_com_owner value="<?=$_vdata->com_owner?>" size=20 maxlength="12" class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">ȸ�� �ּ�</TD>
					<TD class="td_con1">
					<input type=text name="up_com_post1" value="<?=$_vdata->com_post?>" size="5" maxlength="5" readonly class=input> <A class=board_list hideFocus style="selector-dummy: true" onfocus=this.blur(); href="javascript:f_addr_search('form1','up_com_post','up_com_addr',2);"><IMG src="images/order_no_uimg.gif" border=0 align="absmiddle"></A><br>
					<input type=text name="up_com_addr" value="<?=$_vdata->com_addr?>" size=50 maxlength=150 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">����� ����</TD>
					<TD class="td_con1">
					<input type="text" name=up_com_biz value="<?=$_vdata->com_biz?>" size=30 maxlength=30 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">����� ����</TD>
					<TD class="td_con1">
					<input type=text name=up_com_item value="<?=$_vdata->com_item?>" size=30 maxlength=30 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>



				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">����ǸŽŰ�</TD>
					<TD class="td_con1">
					<input type=text name=ec_num value="<?=$_vdata->ec_num?>" size=20 maxlength=20 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>



				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">����ڱ���</TD>
					<TD class="td_con1">
					<input type=text name=com_type value="<?=$_vdata->com_type?>" size=20 maxlength=20 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>



				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">ȸ�� ��ǥ��ȭ</TD>
					<TD class="td_con1">
					<input type=text name=up_com_tel1 value="<?=$com_tel[0]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_com_tel2 value="<?=$com_tel[1]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_com_tel3 value="<?=$com_tel[2]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">ȸ�� �ѽ���ȣ</TD>
					<TD class="td_con1">
					<input type=text name=up_com_fax1 value="<?=$com_fax[0]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_com_fax2 value="<?=$com_fax[1]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_com_fax3 value="<?=$com_fax[2]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">ȸ�� Ȩ������</TD>
					<TD class="td_con1">
					http://<input type=text name=up_com_homepage value="<?=$_vdata->com_homepage?>" size=30 maxlength=50 class=input>
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
					<TD><IMG SRC="images/vender_reg_stitle3.gif" HEIGHT=31 ALT=""></TD>
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
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">����� �̸�</TD>
					<TD class="td_con1">
					<input type=text name=up_p_name value="<?=$_vdata->p_name?>" size=20 maxlength=20 class=input> &nbsp; <FONT class=font_orange>* ���� ����� �̸��� ��Ȯ�� �Է��ϼ���.</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">����� �޴���ȭ</TD>
					<TD class="td_con1">
					<input type=text name=up_p_mobile1 value="<?=$p_mobile[0]?>" size=4 maxlength=3 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_p_mobile2 value="<?=$p_mobile[1]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input>-<input type=text name=up_p_mobile3 value="<?=$p_mobile[2]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)" class=input></TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">����� �̸���</TD>
					<TD class="td_con1">
					<input type=text name=up_p_email value="<?=$_vdata->p_email?>" size=30 maxlength=50 class=input> &nbsp; <FONT class=font_orange>* �ֹ�Ȯ�ν� ����� �̸��Ϸ� �뺸�˴ϴ�.</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">����� �μ���</TD>
					<TD class="td_con1">
					<input type=text name=up_p_buseo value="<?=$_vdata->p_buseo?>" size=20 maxlength=20 class=input>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">����� ����</TD>
					<TD class="td_con1">
					<input type=text name=up_p_level value="<?=$_vdata->p_level?>" size=20 maxlength=20 class=input>
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
				<TR>
					<TD colspan=2 background="images/table_top_line.gif"></TD>
				</TR>
				<? if ($account_rule !="1") { ?>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">������ �����</TD>
					<TD class="td_con1">
					<input type=radio name=up_commission_type id=up_commission_type0 value="1" <?if($_vmdata->commission_type=="1") echo"checked";?> onclick="selCommission('0');"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_commission_type0>��ǰ���� ������</label>
					<img width=20 height=0>
					<input type=radio name=up_commission_type id=up_commission_type1 value="0" <?if($_vmdata->commission_type=="0" || strlen($_vmdata->commission_type)==0) echo "checked";?>  onclick="selCommission('1');"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_commission_type1>��ü��ǰ ���� ������</label>
					<br><span class="font_blue">&nbsp;* ��������� ���� �Ǹż������ �ΰ��� ������ ����� ó�� �� ����.<br>
					&nbsp;&nbsp;(�������� ��� ����, ���̰���, �鼼, ���λ���� �� ���������� ���������ϹǷ� �Ǹż������� �ΰ����� ������ ����� ����)</span>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<? if ($account_rule !='1') { ?>
				<TR id="commission_tr" <? if ($_vmdata->commission_type!="1") { ?> style="display:none" <? } ?>>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">���� ������ �ϰ�����</TD>
					<TD class="td_con1">
						<input type=text name=up_all_rate id="up_all_rate" value="" size=3 maxlength=3 onkeyup="strnumkeyup(this)" class=input>%
						&nbsp;&nbsp;&nbsp;&nbsp; <FONT class=font_orange>* 0���� ū��ġ�� �Է��� ��� ��ϵǾ� �ִ� ��ǰ ��ο� �ϰ������� �����Ḧ �����ų�� �ֽ��ϴ�.</font>
					</TD>
				</TR>
				<? } ?>
				<TR id="commission_all" <? if ($_vmdata->commission_type=="1") { ?> style="display:none" <? } ?>>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">��ü ������</TD>
					<TD class="td_con1">
						<input type=text name=up_rate value="<?=$_vdata->rate?>" size=3 maxlength=3 onkeyup="strnumkeyup(this)" class=input>%
						&nbsp;&nbsp;&nbsp;&nbsp; <FONT class=font_orange>* ����ǰ�� ���� ����˴ϴ�.</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<? }else{ ?>
				<TR>
					<TD class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">������ �����</TD>
					<TD class="td_con1">
					<input type=hidden name=up_commission_type value="1" />
					<input type=hidden name=up_rate value="0" />
					��ǰ���� ���ް�
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<? } ?>

				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">��ǰ ó�� ����</TD>
					<TD class="td_con1">
					<input type=checkbox name=chk_prdt1 id=idx_chk_prdt1 value="Y" <?if(substr($_vdata->grant_product,0,1)=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_chk_prdt1>���</label>
					<img width=20 height=0>
					<input type=checkbox name=chk_prdt2 id=idx_chk_prdt2 value="Y" <?if(substr($_vdata->grant_product,1,1)=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_chk_prdt2>����</label>
					<img width=20 height=0>
					<input type=checkbox name=chk_prdt3 id=idx_chk_prdt3 value="Y" <?if(substr($_vdata->grant_product,2,1)=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_chk_prdt3>����</label>
					<img width=50 height=0>
					<input type=checkbox name=chk_prdt4 id=idx_chk_prdt4 value="Y" <?if(substr($_vdata->grant_product,3,1)=="Y")echo"checked";?>><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_chk_prdt4>���/������, ������ ����</label>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">���� ��ǰ�� ����(<?=$_vdata->product_max?>)</TD>
					<TD class="td_con1">
						<input type=text name=up_product_max value="<?=$_vdata->product_max?>" size=3 onkeyup="strnumkeyup(this)" class=input>
						�� ���� ��ǰ��� ����
						<br/>
						<FONT class=font_orange>* ���������� ������ �ÿ��� <span class="font_blue">0</span>�� �Է����ּ���.</font>
						<!--
						<select name=up_product_max class="select">
						<option value="0" <?if($_vdata->product_max==0)echo"selected";?>>������</option>
						<option value="50" <?if($_vdata->product_max==50)echo"selected";?>>50</option>
						<option value="100" <?if($_vdata->product_max==100)echo"selected";?>>100</option>
						<option value="150" <?if($_vdata->product_max==150)echo"selected";?>>150</option>
						<option value="200" <?if($_vdata->product_max==200)echo"selected";?>>200</option>
						<option value="250" <?if($_vdata->product_max==250)echo"selected";?>>250</option>
						<option value="300" <?if($_vdata->product_max==300)echo"selected";?>>300</option>
						</select> �� ���� ��ǰ��� ����
						-->
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<!--
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point2.gif" width="8" height="11" border="0">�Ǹ� ������</TD>
					<TD class="td_con1">
						<input type=text name=up_rate value="<?=$_vdata->rate?>" size=3 maxlength=3 onkeyup="strnumkeyup(this)" class=input>%
						&nbsp;&nbsp;&nbsp;&nbsp; <FONT class=font_orange>* ���θ� ���翡�� �޴� ��ǰ�Ǹ� �����Ḧ �Է��ϼ���.</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				-->
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">���� ��������</TD>
					<TD class="td_con1">
						����
						<select name="up_bank1" class=input>
							<?
								$bankinfoArray  = calcSetBankinfo();
								foreach ( $bankinfoArray as $k => $v ){
									if( $bank_account[0] == $v ) {
										$sel = "selected";
									} else{
										$sel = "";
									}
									echo "<option value='".$v."' ".$sel.">".$v."</option>";
								}
							?>
						</select>
						<img width=20 height=0>
						���¹�ȣ <input type=text name=up_bank2 value="<?=$bank_account[1]?>" size=20 class=input>
						<img width=20 height=0>
						������ <input type=text name=up_bank3 value="<?=$bank_account[2]?>" size=15 class=input>
						<BR />
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell" width="139"><img src="images/icon_point5.gif" width="8" height="11" border="0">������(�ſ�)</TD>
					<TD class="td_con1">

						<input type="radio" name="adjust_lastday" id="adjust_lastday_0" value="0" <? if ($_vmdata->adjust_lastday==0) {?>checked="checked"<? } ?> onclick="adjustChecked('0')"> <label for="adjust_lastday_0">��������</label>&nbsp;&nbsp;
						<input type="radio" name="adjust_lastday" id="adjust_lastday_1" value="1" <? if ($_vmdata->adjust_lastday==1) {?>checked="checked"<? } ?> onclick="adjustChecked('1')"> <label for="adjust_lastday_0">�ſ���������</label>&nbsp;&nbsp;
						<input type="radio" name="adjust_lastday" id="adjust_lastday_2" value="2" <? if ($_vmdata->adjust_lastday==2) {?>checked="checked"<? } ?> onclick="adjustChecked('2')"> <label for="adjust_lastday_0">15�ϰ� �ſ���������</label>

						<div id="adjust_div" <? if ($_vmdata->adjust_lastday>0) {?>style="display:none;"<?}?>>
						<input type=text name=up_account_date value="<?=$_vdata->account_date?>" size=75 class=input>��
						&nbsp;&nbsp;&nbsp;&nbsp;
						<span style="color:#ffffff;background-color:#000000;padding:4px 5px 2px 5px;cursor:pointer; font-size:11px; letter-spacing:-1px; font-weight:bold;" onclick="setAccountDate(0)">����</span>&nbsp;
						<span style="color:#ffffff;background-color:#000000;padding:4px 5px 2px 5px;cursor:pointer; font-size:11px; letter-spacing:-1px; font-weight:bold;" onclick="setAccountDate(2)">¦������</span>&nbsp;
						<span style="color:#ffffff;background-color:#000000;padding:4px 5px 2px 5px;cursor:pointer; font-size:11px; letter-spacing:-1px; font-weight:bold;" onclick="setAccountDate(1)">Ȧ������</span>
						<br/>
						<FONT class=font_orange>* �������Խ� 10,20,30 �� ���� ����, ���� ���Խ� 2���޷� ���� 29,30,31�� ����Ҽ� ����</font>
						</div>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
			<? /* �߰� jdy */?>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">�����</TD>
					<TD class="td_con1">������ ����
						<input type=text name=up_close_date value="<?=$_vmdata->close_date?>" size=10 class=input onkeyup="strnumkeyup(this)" >�� ������ ���
						&nbsp;&nbsp; <FONT class=font_orange>* (�����Ϻ��� 1������������ �ֹ��� ������ ��� 7�� �Է�. �ݵ�� 1���� ū���� �Է�, <b>������ �ϴ� �Ŵ��� ����</b>)</font>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>

				<? if ($reserve_use || $coupon_use) {?>
				<tr>
					<td class="table_cell"><img src="images/icon_point5.gif" border="0">���� ��� ����</td>
					<td class="td_con1">

						<? if ($reserve_use) {?>
						<b>������ : </b>
						<input type=radio name=up_reserve_use id=up_reserve_use1 value="1" <?if($_vmdata->reserve_use=="1")echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_reserve_use1>���</label>
						<img width=20 height=0>
						<input type=radio name=up_reserve_use id=up_reserve_use0 value="0" <?if($_vmdata->reserve_use=="0" || strlen($_vmdata->reserve_use)==0)echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_reserve_use0>��� ����</label>
						<br/>
						<? } ?>

						<? if ($coupon_use) {?>
						<b>���� : </b>
						<input type=radio name=up_coupon_use id=up_coupon_use1 value="1" <?if($_vmdata->coupon_use=="1")echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_coupon_use1>���</label>
						<img width=20 height=0>
						<input type=radio name=up_coupon_use id=up_coupon_use0 value="0" <?if($_vmdata->coupon_use=="0" || strlen($_vmdata->coupon_use)==0)echo"checked";?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_coupon_use0>��� ����</label>
						<br/>
						<? } ?>
						<span class="font_blue">
						* ���Ұ� üũ �� ������� ������ ����� �� ������ �ش�޴��� ������ ������忡 ������� �ʽ��ϴ�.
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

			<? /* �߰� jdy */?>

				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">��Ÿ����</TD>
					<TD class="td_con1">
						<textarea name="up_etc" cols="80" rows="5" ><?= $_vmdata->etc ?></textarea>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">�����ڸ޸�</TD>
					<TD class="td_con1">
						<textarea name="up_admin_memo" cols="80" rows="5" ><?= $_vmdata->admin_memo ?></textarea>
					</TD>
				</TR>
				<TR>
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR>
					<TD class="table_cell"><img src="images/icon_point5.gif" width="8" height="11" border="0">��������å����<br/>�����丮</TD>
					<TD class="td_con1">
					<iframe src="vender_ch_pop.php?vender=<?=$vender ?>&type=if" width="780" height="100" frameborder=0 framespacing=0 marginheight=0 marginwidth=0 scrolling=no vspace=0 onload="autoResize(this)" ></iframe>
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
				<td align="center">
					<a href="javascript:CheckForm();"><img src="images/btn_edit2.gif" width="113" height="38" border="0"></a>
					&nbsp;
					<a href="javascript:CheckDelete();"><img src="images/btn_infodelete.gif" width="113" height="38" border="0"></a>
				</td>
			</tr>
			</form>
			<form name="form3" method="post" action="vender_management.php">
			<input type=hidden name='vender' value="<?=$value?>">
			<input type=hidden name='disabled' value='<?=$disabled?>'>
			<input type=hidden name='s_check' value='<?=$s_check?>'>
			<input type=hidden name='search' value='<?=$search?>'>
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
			</form>
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
						<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">������� �������</span></td>
					</tr>
					<tr>
						<td  class="space_top"><span style="padding-left:13px">- ����ݾ� : ������ ��������Ⱓ ������ ������ü ��ۿϷ��ǰ�� �� ���⿡�� �Ǹż�����, ���������� ������, ���������� ���� ��۷Ḧ ���� �ݾ��� ������ �� �����ݾ�<br/>
						<span style="padding-left:13px">- ��������� : �ŷ��� ���� �� ����ݾ��� �����Ǵ� �Ⱓ<br/>
						<span style="padding-left:13px">- ����� : ����������� ������ ��¥(������)<br/>
						<span style="padding-left:13px">- ������ : ����������� ����ݾ��� ������ü���� ����(�Ա�)�ϴ� ��¥<br/>
						<span style="padding-left:13px">- ������ȸ�� : ����ݾ��� ��ȸ�ϴ� ��¥
						</td>
					</tr>
					<tr><td height="20"></td></tr>

					<tr>
						<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">������� ��) </span></td>
					</tr>
					<tr>
						<td  class="space_top">
						<span style="padding-left:13px">* A��ü�� �������� �ſ�10�� 1ȸ �̰�, ������� �����Ϸ� ���� 5�� ������ ���<br/>
						<span style="padding-left:13px">- ��������� : ������ 6�� ~ �̹���5��<br/>
						<span style="padding-left:13px">- ����� : �Ŵ� 5��
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td class="menual_con"><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">�������� ������� ��) </span></td>
					</tr>
					<tr>
						<td  class="space_top">
						<span style="padding-left:13px">* B��ü�� �������� �ſ� 5��, 10��, 15��, 20��, 25��, 30��  6ȸ �̰�, ������� �����Ϸ� ���� 5�� ������ ���<br/>
						<span style="padding-left:13px">- ��������� : ������ 26��~������ ����(5�� ����), �̹��� 1��~�̹��� 5��(10�� ����), 6��~10��(15�� ����), 11��~15��(20�� ����), 16��~20��(25�� ����), 21��~ 25��(30�� ����)<br/>
						<span style="padding-left:13px">- ����� : �Ŵ� ����, 5��, 10��, 15��, 20��, 25��
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
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>
<? INCLUDE "copyright.php"; ?>