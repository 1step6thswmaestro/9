<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
INCLUDE ("access.php");
include_once($Dir."lib/admin_more.php");

$mode=$_POST["mode"];

// ���� ���� ��ȸ jdy
$shop_more_info = getShopMoreInfo();
$account_rule = $shop_more_info['account_rule'];
// ���� ���� ��ȸ jdy

/* ������ ���� �߰� jdy */
$sql = "SELECT * FROM vender_more_info ";
$sql.= "WHERE vender='".$_VenderInfo->getVidx()."'";
$result=mysql_query($sql,get_db_conn());
$_vmdata=mysql_fetch_object($result);
mysql_free_result($result);
/* ������ ���� �߰� jdy */

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
		echo "<html></head><body onload=\"alert('��ǥ�� ������ ��Ȯ�� �Է��ϼ���.')\"></body></html>";exit;
	} else if(strlen($up_com_post)==0 || strlen($up_com_addr)==0) {
		echo "<html></head><body onload=\"alert('����� �ּҸ� ��Ȯ�� �Է��ϼ���.')\"></body></html>";exit;
	} else if(strlen($up_com_biz)==0) {
		echo "<html></head><body onload=\"alert('����� ���¸� ��Ȯ�� �Է��ϼ���.')\"></body></html>";exit;
	} else if(strlen($up_com_item)==0) {
		echo "<html></head><body onload=\"alert('����� ������ ��Ȯ�� �Է��ϼ���.')\"></body></html>";exit;
	} else if(strlen($up_com_tel)==0) {
		echo "<html></head><body onload=\"alert('ȸ�� ��ǥ��ȭ�� ��Ȯ�� �Է��ϼ���.')\"></body></html>";exit;
	} else if(strlen($up_p_name)==0) {
		echo "<html></head><body onload=\"alert('����ڸ��� ��Ȯ�� �Է��ϼ���.')\"></body></html>";exit;
	} else if(strlen($up_p_mobile)==0) {
		echo "<html></head><body onload=\"alert('����� �޴���ȭ�� ��Ȯ�� �Է��ϼ���.')\"></body></html>";exit;
	} else if(strlen($up_p_email)==0) {
		echo "<html></head><body onload=\"alert('����� �̸����� ��Ȯ�� �Է��ϼ���.')\"></body></html>";exit;
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


	// ��ǥ�̹��� ���
	if( $_FILES['com_image']['error'] == 0 AND $_FILES['com_image']['size'] > 0 AND eregi("image",$_FILES['com_image']['type']) AND $_POST['com_image_del'] != "OK" ) {
		$exte = explode(".",$_FILES['com_image']['name']);
		$exte = $exte[ count($exte)-1 ];
		$com_image_name = "comImgae_".$_VenderInfo->getVidx().".".$exte;
		move_uploaded_file($_FILES['com_image']['tmp_name'],$com_image_url.$com_image_name);
		$sql.= ", com_image = '".$com_image_name."' ";
	}

	//��ǥ�̹��� ����
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

		echo "<html></head><body onload=\"alert('��û�Ͻ� �۾��� �����Ͽ����ϴ�.');parent.location.reload()\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('��û�Ͻ� �۾��� ������ �߻��Ͽ����ϴ�.')\"></body></html>";exit;
	}
}
/* ���� ������ ���� jdy */
else if ($mode=="com"){

	$rq_commission_type = $_POST["rq_commission_type"];
	$rq_rate = $_POST["rq_rate"];
	$rq_name = $_POST["rq_name"];

	//�Ǹ� ������� ��� ������ ���濡���� ����� ���� ex) ��ü-> ����

	if ($rq_commission_type!=($_vmdata->commission_type)) {

		if ($_vmdata->commission_type == "1") {
			$up_history = "���������� -> ��ü������ ".$rq_rate."%�� �����û [����]";
		}else{
			$up_history = "��ü������ ".$_venderdata->rate."% -> ����������� �����û [����]";
		}
	}else{

		if ($_vmdata->commission_type != '') {
			if ($rq_commission_type != "1") {

				if ($rq_rate !=$_venderdata->rate) {
					$up_history = "��ü������ ".$_venderdata->rate."% -> ".$rq_rate."% �� �����û [����]";
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
		echo "<html></head><body onload=\"alert('��û�Ͻ� �۾��� �����Ͽ����ϴ�.');parent.location.reload()\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('��û�Ͻ� �۾��� ������ �߻��Ͽ����ϴ�.')\"></body></html>";exit;
	}

}
/* ���� ������ ���� jdy */

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
		alert("��ȣ(ȸ���)�� �Է��ϼ���.");
		return;
	}
	if(CheckLength(form.up_com_name)>30) {
		form.up_com_name.focus();
		alert("��ȣ(ȸ���)�� �ѱ�15�� ����30�� ���� �Է� �����մϴ�");
		return;
	}
	if (!form.up_com_num.value) {
		form.up_com_num.focus();
		alert("����ڵ�Ϲ�ȣ�� �Է��ϼ���.");
		return;
	}

	var bizno;
	var bb;
	bizno = form.up_com_num.value;
	bizno = bizno.replace("-","");
	bb = chkBizNo(bizno);
	if (!bb) {
		alert("�������� ���� ����ڵ�Ϲ�ȣ �Դϴ�.\n����ڵ�Ϲ�ȣ�� �ٽ� �Է��ϼ���.");
		form.up_com_num.value = "";
		form.up_com_num.focus();
		return;
	}
	if (!form.up_com_owner.value) {
		form.up_com_owner.focus();
		alert("��ǥ�� ������ �Է��ϼ���.");
		return;
	}
	if(CheckLength(form.up_com_owner)>12) {
		form.up_com_owner.focus();
		alert("��ǥ�� ������ �ѱ� 6���ڱ��� �����մϴ�");
		return;
	}
	if (!form.up_com_post1.value || !form.up_com_post2.value) {
		form.up_com_post1.focus();
		alert("�����ȣ�� �Է��ϼ���.");
		return;
	}
	if (!form.up_com_addr.value) {
		form.up_com_addr.focus();
		alert("����� �ּҸ� �Է��ϼ���.");
		return;
	}
	if(CheckLength(form.up_com_biz)>30) {
		form.up_com_biz.focus();
		alert("����� ���´� �ѱ� 15�ڱ��� �Է� �����մϴ�");
		return;
	}
	if(CheckLength(form.up_com_item)>30) {
		form.up_com_item.focus();
		alert("����� ������ �ѱ� 15�ڱ��� �Է� �����մϴ�");
		return;
	}
	if(form.up_com_tel1.value.length==0 || form.up_com_tel2.value.length==0 || form.up_com_tel3.value.length==0) {
		form.up_com_tel1.focus();
		alert("ȸ�� ��ǥ��ȭ�� �Է��ϼ���.");
		return;
	}
	if(!isNumber(form.up_com_tel1.value) || !isNumber(form.up_com_tel2.value) || !isNumber(form.up_com_tel3.value)) {
		form.up_com_tel1.focus();
		alert("��ȭ��ȣ�� ���ڸ� �Է��ϼ���.");
		return;
	}
	if(form.up_com_fax1.value.length>0 && form.up_com_fax2.value.length>0 && form.up_com_fax3.value.length>0) {
		if(!isNumber(form.up_com_fax1.value) || !isNumber(form.up_com_fax2.value) || !isNumber(form.up_com_fax3.value)) {
			form.up_com_fax1.focus();
			alert("�ѽ���ȣ�� ���ڸ� �Է��ϼ���.");
			return;
		}
	}
	if(form.up_p_name.value.length==0) {
		form.up_p_name.focus();
		alert("����� �̸��� �Է��ϼ���.");
		return;
	}
	if(form.up_p_mobile1.value.length==0 || form.up_p_mobile2.value.length==0 || form.up_p_mobile3.value.length==0) {
		form.up_com_tel1.focus();
		alert("����� �޴���ȭ�� �Է��ϼ���.");
		return;
	}
	if(!isNumber(form.up_p_mobile1.value) || !isNumber(form.up_p_mobile2.value) || !isNumber(form.up_p_mobile3.value)) {
		form.up_com_tel1.focus();
		alert("����� �޴���ȭ ��ȣ�� ���ڸ� �Է��ϼ���.");
		return;
	}
	if(form.up_p_email.value.length==0) {
		form.up_p_email.focus();
		alert("����� �̸����� �Է��ϼ���.");
		return;
	}
	if(!IsMailCheck(form.up_p_email.value)) {
		form.up_p_email.focus();
		alert("����� �̸����� ��Ȯ�� �Է��ϼ���.");
		return;
	}
	if(form.up_bank1.value.length==0 || form.up_bank2.value.length==0 || form.up_bank3.value.length==0) {
		alert("������� ���������� ��Ȯ�� �Է��ϼ���.");
		form.up_bank1.focus();
		return;
	}
	if(form.up_passwd.value.length>0 || form.up_passwd2.value.length>0) {
		if(form.up_passwd.value!=form.up_passwd.value) {
			alert("�����Ͻ� ��й�ȣ�� ��ġ���� �ʽ��ϴ�.");
			form.up_passwd2.focus();
			return;
		} else if(form.up_passwd.value.length<4) {
			alert("��й�ȣ�� ����, ���ڸ� ȥ���Ͽ� 4~12�� �̳��� �Է��ϼ���.");
			form.up_passwd.focus();
			return;
		}
	}


	if(confirm("�����Ͻ� ������ �����Ͻðڽ��ϱ�?")) {
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
			alert("�����Ḧ �Է����ּ���.");
			form.rq_rate.focus();
			return;
		}
	}

	if(form.rq_name.value.length==0) {
		alert("��û�ڸ� �Է����ּ���.");
		form.rq_name.focus();
		return;
	}


	if(confirm("������ ������ ��û�Ͻðڽ��ϱ�??")) {
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
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">������ ������ ���� �� ��Ÿ ���� ���� �Է��մϴ�.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">�Է��� ������ ���� ����Ʈ ������ü ������ �Էµ˴ϴ�.</td>
										</tr>
										<tr>
											<td class="notice_gray" height="20"><img src="images/icon_dot02.gif" border=0 hspace="4">������ �������� ��ǰ ó������[���/����/����/����]�� ���� �����ڰ� ���� �� ���� �մϴ�.</td>
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

			<!-- ó���� ���� ��ġ ���� -->
			<tr><td height=40></td></tr>
			<tr>
				<td >

				<table border=0 cellpadding=0 cellspacing=0 width=100%>

				<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>" enctype="multipart/form-data">
				<input type=hidden name=mode>

				<tr>
					<td><img src="images/venter_info_stitle01.gif" alt="������ü �⺻����" align="absmiddle"><font style="color:#2A97A7">('*'ǥ�ô� �ʼ��Է��Դϴ�)</font></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=2 bgcolor=#808080></td></tr>
				</table>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=140></col>
				<col width=></col>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> ��ȣ(ȸ���)</B></td>
					<td style=padding:7,10>
					<input type="text" class=input  name=up_com_name value="<?=$_venderdata->com_name?>" size=20 maxlength=30 disabled>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> ����ڵ�Ϲ�ȣ</B></td>
					<td style=padding:7,10>
					<input type="text" class=input  name=up_com_num value="<?=$_venderdata->com_num?>" size=20 maxlength=20 onkeyup="strnumkeyup(this)" disabled>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>

				<!--
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> ������ �������</B></td>
					<td style=padding:7,10>
						<input type="radio" name="com_nametech" value="1" />�����&nbsp;&nbsp;
						<input type="radio" name="com_nametech" value="0" checked />������
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				-->

				<tr>
					<td bgcolor="#F5F5F5" background="images/line01.gif" style="background-repeat:repeat-y; background-position:right; padding:9px;"><B><font color=red>*</font> ��ǥ�̹���</B></td>
					<td style="padding:7px 10px;">

						<div style="margin:5px;">
							<div style="float:left; margin:0px; padding:0px; font-size:0px;"><img src="<?=$com_image_url.$_venderdata->com_image?>" width="120" onerror="this.src='/images/no_img.gif';" style="border:1px solid #dddddd;" /></div>
							<div style="float:left; margin-top:5px; margin-left:10px;">
								<div>
									<span style="font-size:11px; color:#666666; line-height:15px; letter-spacing:-1px;">
										<strong>������� : </strong><input type="checkbox" name="com_nametech" value="1" <?=($_venderdata->com_nametech?"checked":"");?>><br /><br /><br />

										�� <b>������ �̹�����??</b>
										<div style="margin:5px 0px;"><img src="images/vender_nametek_sample.gif" style="border:1px solid #e5e5e5;" alt="" /></div>
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

						<!--
						<strong>������� : </strong><input type="checkbox" name="com_nametech" value="1" <?=($_venderdata->com_nametech?"checked":"");?>><br /><br />
						<img src="<?=$com_image_url.$_venderdata->com_image?>" onerror="this.src='/images/no_img.gif';">
						<input type="file" name="com_image" id="com_image">
						<input type="checkbox" name="com_image_del" id="com_image_del" value="OK" onclick="com_image.style.display=( this.checked ?'none':'inline')"><label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="com_image_del">����</label>
						<input type="hidden" name="com_image_del_file" value="<?=$com_image_url.$_venderdata->com_image?>">
						<br /> �̹��� ������ 100px * 100px ����.
						-->

					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>


				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> ��ǥ�� ����</B></td>
					<td style=padding:7,10>
					<input class=input type="text" name=up_com_owner value="<?=$_venderdata->com_owner?>" size=20 maxlength="12">
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td rowspan=2 bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> ����� �ּ�</B></td>
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
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> ����� ����</B></td>
					<td style=padding:7,10>
					<input type="text" class=input  name=up_com_biz value="<?=$_venderdata->com_biz?>" size=30 maxlength=30>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> ����� ����</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=up_com_item value="<?=$_venderdata->com_item?>" size=30 maxlength=30>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> ȸ�� ��ǥ��ȭ</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=up_com_tel1 value="<?=$com_tel[0]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)">-<input type=text class=input  name=up_com_tel2 value="<?=$com_tel[1]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)">-<input type=text class=input  name=up_com_tel3 value="<?=$com_tel[2]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)">
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>



				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B> ����ǸŽŰ�</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=ec_num value="<?=$_venderdata->ec_num?>" size=30 maxlength=30>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>


				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B> ����ڱ���</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=com_type value="<?=$_venderdata->com_type?>" size=30 maxlength=30>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>




				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>ȸ�� �ѽ���ȣ</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=up_com_fax1 value="<?=$com_fax[0]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)">-<input type=text class=input  name=up_com_fax2 value="<?=$com_fax[1]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)">-<input type=text class=input  name=up_com_fax3 value="<?=$com_fax[2]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)">
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>ȸ�� Ȩ������</B></td>
					<td style=padding:7,10>
					http://<input type=text class=input  name=up_com_homepage value="<?=$_venderdata->com_homepage?>" size=30 maxlength=50>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				</table>






				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=40></td></tr>
				<tr>
					<td><img src="images/venter_info_stitle02.gif" alt="��ü ����� ����" align="absmiddle"> <font style="color:#2A97A7">('*'ǥ�ô� �ʼ��Է��Դϴ�)</font></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=2 bgcolor=#808080></td></tr>
				</table>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=140></col>
				<col width=></col>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> ����� �̸�</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=up_p_name value="<?=$_venderdata->p_name?>" size=20 maxlength=20> &nbsp; <span class="notice_blue">* ���� ����� �̸��� ��Ȯ�� �Է��ϼ���.</font>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> ����� �޴���ȭ</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=up_p_mobile1 value="<?=$com_p_mobile[0]?>" size=4 maxlength=3 style="width:40" onkeyup="strnumkeyup(this)">-<input type=text class=input  name=up_p_mobile2 value="<?=$com_p_mobile[1]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)">-<input type=text class=input  name=up_p_mobile3 value="<?=$com_p_mobile[2]?>" size=4 maxlength=4 style="width:40" onkeyup="strnumkeyup(this)"></td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> ����� �̸���</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=up_p_email value="<?=$_venderdata->p_email?>" size=30 maxlength=50> &nbsp; <span class="notice_blue">* �ֹ�Ȯ�ν� ����� �̸��Ϸ� �뺸�˴ϴ�.</font>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>����� �μ���</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=up_p_buseo value="<?=$_venderdata->p_buseo?>" size=20 maxlength=20>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>����� ����</B></td>
					<td style=padding:7,10>
					<input type=text class=input  name=up_p_level value="<?=$_venderdata->p_level?>" size=20 maxlength=20>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=40></td></tr>
				<tr>
					<td><img src="images/venter_info_stitle04.gif" alt="�̴ϼ� �ID ����" align="absmiddle"></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=2 bgcolor=#808080></td></tr>
				</table>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=140></col>
				<col width=></col>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>� ID</td>
					<td style=padding:7,10>
					<B><?=$_venderdata->id?></B>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>��й�ȣ ����</B></td>
					<td style=padding:7,10>
					<input class=input  type=password name=up_passwd size=15> &nbsp; <span class="notice_blue">* ����, ���ڸ� ȥ���Ͽ� ���(4�� ~ 12��)</font>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>��й�ȣ Ȯ��</B></td>
					<td style=padding:7,10>
					<input class=input type=password name=up_passwd2 size=15> &nbsp; <span class="notice_blue">* ��й�ȣ�� ���������� ���� �Ͻ� ���� �����մϴ�.</font>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>��� ���� ����</B></td>
					<td style=padding:7,10>
					<input type=radio name=up_session value="N" id="idx_sessionN"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_sessionN">�α��� ���� ����</label><img width=5 height=0><input type=radio name=up_session value="Y" id="idx_sessionY"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_sessionY">�α��� ���� ����</label><br>
					<span class="notice_blue">* �α��� ���� ������ �ڽ��� ������ ��� ��ڵ��� ��α��� �� �̿��� �����մϴ�.</font>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				</table>



				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=40></td></tr>
				<tr>
					<td><img src="images/venter_info_stitle03.gif" alt="��ü ��������" align="absmiddle"> <font style="color:#2A97A7">('*'ǥ�ô� �ʼ��Է��Դϴ�)</font></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=2 bgcolor=#808080></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=140></col>
				<col width=></col>

				<? if ($account_rule!="1")  { ?>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>������ �����</td>
					<td style=padding:7,10>
					<B><? if ($_vmdata->commission_type=="1") { ?>��ǰ�� ������ ����<? }else{ ?>��ü��ǰ ���� ������ ����<? } ?></B>
					&nbsp;&nbsp;&nbsp;&nbsp;<button style="color:#ffffff;background-color:#000000;border:0;width:80px;height:25px;cursor:pointer" onclick="commissionDivView();">�����û</button>
					<div id="commission_div" style="position:absolute;width:450px;border:2px solid #acacac;background-color:#ffffff;z-index:999;padding:5px;display:none;">
						<div style="width:100%;text-align:right"><span style="border:1px solid gray;color:#ffffff;background-color:#000000;padding:2px 4px;cursor:pointer" onclick="commissionDivView('N');" >X</span></div>
						<div style="width:100%;margin-top:5px;">
							<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
								<col width=100 />
								<col width= />
							<tr><td height=2 colspan="2" bgcolor=#808080></td></tr>
							<tr>
								<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>�����</td>
								<td style=padding:7,10>
								<input type=radio name=rq_commission_type id=rq_commission_type0 value="1" onclick="selCommission('0');"> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=rq_commission_type0>��ǰ���� ������</label>
								&nbsp;&nbsp;
								<input type=radio name=rq_commission_type id=rq_commission_type1 value="0" onclick="selCommission('1');" checked> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=rq_commission_type1>��ü��ǰ ���� ������</label>
								</td>
								<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
								<tr id="commission_all" >
									<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>��ü ������</td>
									<td style=padding:7,10>
										<input type=text name=rq_rate value="" size=3 maxlength=3 onkeyup="strnumkeyup(this)" class=input>%
									</td>
								</tr>
								<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
								<tr id="commission_all" >
									<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>��û�� �̸�</td>
									<td style=padding:7,10>
										<input type=text name=rq_name value="" size=10 class=input>
									</td>
								</tr>
								<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
								<tr><td></td>
									<td style="padding-top:10px;text-align:right;"><span style="border:1px solid gray;color:#ffffff;background-color:#000000;padding:2px 4px;cursor:pointer" onclick="commissionRequest()">��û</span></td>
								</tr>
							</tr>
							</table>
						</div>
					</div>
					<? if ($_vmdata->commission_status=="1" || $_vmdata->commission_status=="2") {

							if ($_vmdata->rq_commission_type=="1") {
								$cm_value = "��ǰ�� ������";
							}else{
								$cm_value = "��ü��ǰ ���� ������ ".$_vmdata->rq_rate."%";
							}

							$cm_status = "";

							if ($_vmdata->commission_status=="1") {
								$cm_status = "��û ��";
							}else if ($_vmdata->commission_status=="2") {
								$cm_status = "��û �ź�";
							}

						?>
					<br/><br/>
					<span class="notice_blue"><?= $cm_value ?>�� <?= $cm_status ?></span>
					<? } ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<? if (!$_vmdata->commission_type) {?>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>��ü ������</td>
					<td style=padding:7,10>
					<B><?=(int)$_venderdata->rate?> %</B>
					&nbsp;&nbsp;&nbsp;&nbsp; <span class="notice_blue">* ����ǰ�� ���� ����˴ϴ�.</font>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<? } ?>

				<? } ?>

				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>��ǰ ó�� ����</td>
					<td style=padding:7,10>
					<input type=checkbox name=chk_prdt1 value="Y" <?if(substr($_venderdata->grant_product,0,1)=="Y")echo"checked";?> disabled>���
					<img width=5 height=0>
					<input type=checkbox name=chk_prdt2 value="Y" <?if(substr($_venderdata->grant_product,1,1)=="Y")echo"checked";?> disabled>����
					<img width=5 height=0>
					<input type=checkbox name=chk_prdt3 value="Y" <?if(substr($_venderdata->grant_product,2,1)=="Y")echo"checked";?> disabled>����
					<img width=5 height=0>
					<input type=checkbox name=chk_prdt4 value="Y" <?if(substr($_venderdata->grant_product,3,1)=="Y")echo"checked";?> disabled>���/������, ������ ����
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>���� ��ǰ�� ����</td>
					<td style=padding:7,10>
					<B><?=($_venderdata->product_max==0?"������ ��� ����":$_venderdata->product_max."�� ���� ��ǰ��� ����")?></B>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>�Ǹ� ������</td>
					<td style=padding:7,10>
					<B><?=(int)$_venderdata->rate?> %</B>
					&nbsp;&nbsp;&nbsp;&nbsp; <span class="notice_blue">* ���θ� ���翡�� �޴� ��ǰ�Ǹ� �������Դϴ�.</font>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B><font color=red>*</font> ���� ��������</td>
					<td style=padding:7,10>
					���� <input type=text class=input  name=up_bank1 value="<?=$bank_account[0]?>" size=10>
					<img width=5 height=0>
					���¹�ȣ <input type=text class=input  name=up_bank2 value="<?=$bank_account[1]?>" size=20>
					<img width=5 height=0>
					������ <input type=text class=input  name=up_bank3 value="<?=$bank_account[2]?>" size=15>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>������</td>
					<td style=padding:7,10>
					<B>�ſ� <?=(strlen($_venderdata->account_date)>0?$_venderdata->account_date."��":"")?></B>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
			<? /*�߰� jdy */?>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>�����</td>
					<td style=padding:7,10>
					<B><?=(strlen($_vmdata->close_date)>0?"�����Ϸ� ���� ".$_vmdata->close_date." ��������":"")?></B>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
			<? /*�߰� jdy */?>

				</form>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>��������å����<br/>�����丮</td>
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
			<!-- ó���� ���� ��ġ �� -->

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
