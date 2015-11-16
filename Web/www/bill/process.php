<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/ext/func.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/hiworks/bill.class.php");

$bill = new Bill();


if($_POST['act'] =='mod'){

	if(false === $bill->_chgRequest($_POST)){
		_alert($bill->errmsg,'0');
	}else{
		_alert('정상 신청 되었습니다.','0');
	}
	
}else{
	if(false === $bill->_request($_POST)){
		_alert($bill->errmsg,'0');
	}else{
		_alert('정상 신청 되었습니다.','0');
	}
}

?>