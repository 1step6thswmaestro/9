<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())>0) {
	header("Location:mypage_usermodify.php");
	exit;
}

$leftmenu="Y";
$sql="SELECT body,leftmenu FROM tbldesignnewpage WHERE type='joinagree'";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$body=$row->body;
	$body=str_replace("[DIR]",$Dir,$body);
	$leftmenu=$row->leftmenu;
	$newdesign="Y";
}
mysql_free_result($result);

$query_cfg = "select * from tblmobileconfig";
$result_cfg = mysql_query($query_cfg,get_db_conn());
$row_cfg = mysql_fetch_array($result_cfg);

$skin_name = $row_cfg[skin]; if($skin_name=="") {	$skin_name = "defalut1";	}
$upload_path = "../m/upload/";  //�ΰ�, ������, ī�Ƕ���Ʈ

//�� �⺻����
$row_shop = mysql_fetch_array(mysql_query("select shopname from tblshopinfo"));
?>


<? include "header.php"; ?>
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.js.php"></script>

<SCRIPT LANGUAGE="JavaScript">
	<!--
	function CheckForm() {
		if(!document.form1.agree || document.form1.agree.checked==false) {
			alert("ȸ������� �����ϼž� ȸ�������� �����մϴ�.");
			if(document.form1.agree) {
				document.form1.agree.focus();
			}
			return;
		} else if(!document.form1.agreep || document.form1.agreep.checked==false) {
			alert("���κ�ȣ��޹�ħ�� �����ϼž� ȸ�������� �����մϴ�.");
			if(document.form1.agreep) {
				document.form1.agreep.focus();
			}
			return;
		} else if(confirm("ȸ�������� ���� �ϰڽ��ϱ�?")) {
			document.form1.submit();
		} else {
			return;
		}
	}
	//-->
</SCRIPT>

<div class="h_area2">
	<h2>ȸ������</h2>
	<a href="main.php" class="btn_home" rel="external"><span class="vc">Ȩ</span></a>
	<a href="javascript:history.back()" class="btn_prev" rel="external"><span>����</span></a>
</div>
<?//include($Dir."lib/style.php")?>

<form name=form1 action="member_join.php" method=post>
<?
	$sql="SELECT agreement,privercy FROM tbldesign ";
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$agreement=$row->agreement;
	$privercy_exp=@explode("=", $row->privercy);
	$privercy=$privercy_exp[1];
	mysql_free_result($result);

	if(strlen($agreement)==0) {
		$buffer="";
		$fp=fopen($Dir.AdminDir."agreement.txt","r");
		if($fp) {
			while (!feof($fp)) {
				$buffer.= fgets($fp, 1024);
			}
		}
		fclose($fp);
		$agreement=$buffer;
	}

	$pattern=array("(\[SHOP\])","(\[COMPANY\])");
	$replace=array($_data->shopname, $_data->companyname);
	$agreement = preg_replace($pattern,$replace,$agreement);

	if(strlen($privercy)==0) {
		$buffer="";
		$fp=fopen($Dir.AdminDir."privercy2.txt","r");
		if($fp) {
			while (!feof($fp)) {
				$buffer.= fgets($fp, 1024);
			}
		}
		fclose($fp);
		$privercy=$buffer;
	}
?>

<div style="margin:10px; padding-bottom:10px; border-bottom:1px solid #e5e5e5;">
	ȸ�������� �Ͻø� �� ���θ����� �����ϴ� �̺�Ʈ�� ������ �� ������,<br />
	������ ��ǰ�� �̺�Ʈ �� �پ��� ������ ���Ϸ� ������ �� �ֽ��ϴ�.
</div>

<div><IMG src="<?=$Dir?>images/join_yak_01.gif" alt="" border=0 /></div>
<div style="margin:0px 10px; BORDER: #dfdfdf 1px solid; text-align:left">
	<DIV style="PADDING:8px; OVERFLOW-Y:auto; OVERFLOW-X:auto; HEIGHT:105px;"><?=$agreement?></DIV>
</div>
<div style="margin:5px 10px 20px 10px;"><INPUT id="idx_agree" type="checkbox" name="agree" style="border:none;"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_agree>���� ȸ������� �����մϴ�.</LABEL></div>

<div><IMG src="<?=$Dir?>images/join_yak_02.gif" alt="" border=0 /></div>
<div style="margin:0px 10px; BORDER: #dfdfdf 1px solid; text-align:left;">
	<DIV style="PADDING:8px; OVERFLOW-Y:auto; OVERFLOW-X: auto; HEIGHT: 105px"><?=$privercy?></DIV>
</div>
<div style="margin:5px 10px 15px 10px;"><INPUT id="idx_agreep" type="checkbox" name="agreep" style="border:none;"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_agreep>���� ����������޹�ħ�� �����մϴ�.</LABEL></div>
<div style="margin:20px; text-align:center;">
	<A HREF="javascript:CheckForm();" class="button blue bigrounded">ȸ������<!--<img src="<?=$Dir?>images/btn_mjoin.gif" border="0">--></a>
	<A HREF="javascript:history.go(-1);" class="button white bigrounded">��������<!--<img src="<?=$Dir?>images/btn_mback.gif" border="0" hspace="5">--></a>
</div>

</form>

<? include_once('footer.php'); ?>