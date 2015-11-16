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

if(_empty($bill->errmsg) && $bill->_issueSend($_POST)){
	_alert('발행 등록 되었습니다. - 하이웤스에서 최종 발행 처리 하시기 바랍니다.','-1');
	exit;
}
//_alert($bill->errmsg,'-1');



?>