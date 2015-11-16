<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/ext/func.php");
include_once($Dir."lib/class/attendance.php");
$attendance = new attendance();

if(_empty($_ShopInfo->getMemid())){
	_alert('출석 체크 이벤트는 회원 전용 기능 입니다.',$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}
if(!_isInt($_REQUEST['aidx'])){
	_alert('출석 체크 이벤트가 선택 되지 않았습니다.','-1');
	exit;
}


$attendance->_set($_REQUEST['aidx']);
if($attendance->_get('status') != 1){
	_alert($attendance->_get('statusmsg').' 이벤트 입니다.','-1');
	exit;	
}

if(false === $attendance->_setStamp($_REQUEST['ment'])){
	_alert('Error : '.$attendance->_msg(),'-1');
	exit;	
}else{
	_alert('정상 처리 되었습니다.','-1');
	exit;	
}
?>