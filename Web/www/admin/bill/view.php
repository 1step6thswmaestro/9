<?
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/ext/func.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/hiworks/bill.class.php");

INCLUDE ("../access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-1";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	INCLUDE ("../AccessDeny.inc.php");
	exit;
}
#########################################################
$bill = new Bill();
if(_empty($bill->errmsg) && $bill->setBillByIdx($_POST["b_idx"])){
	//_pr($bill);	
	$bill->get_senderinfo();
}else{
	_alert($bill->errmsg,'0');
}
?>
<form method="post" name="frmLogin" action="http://billapi.hiworks.co.kr/auto_login.php" target="_hiddenFrame">
<input type=hidden name=domain value="<?=$bill->config['domain']?>">
<input type=hidden name=license_id value="<?=$bill->config['license_id']?>">
<input type=hidden name=license_no value="<?=$bill->config['license_no']?>">
<input type=hidden name=pType value="BILL">
</form>
<form method="get" action="http://group.hiworks.co.kr/getmall/bill/manage/viewdoc/<?=$bill->documentinfo['document_id']?>">
</form>
<IFRAME id="_hiddenFrame" name="_hiddenFrame" style="width:0;height:0; position:absolute; visibility:hidden;"></IFRAME>
<script type="text/javascript">
document.frmLogin.submit();
function goView(){
	document.location.replace ='http://group.hiworks.co.kr/getmall/bill/manage/viewdoc/<?=$bill->documentinfo['document_id']?>';
}
setTimeout(goView,1000);
</script>
