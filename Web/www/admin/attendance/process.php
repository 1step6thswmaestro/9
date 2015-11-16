<?
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include ("../access.php");
include_once($Dir."lib/ext/func.php");
include_once($Dir."lib/class/JSON.php");

include_once($Dir."lib/class/attendance.php");


switch($_POST['act']){
	case 'update':
	case 'add':
		$attendance = new attendance();
		if(false ===  $attendance->_setItem($_POST)){
			_alert($attendance->statusmsg,'-1');
		}else{
			_alert('저장 되었습니다.','/admin/market_attendance.php');
		}
		break;
	case 'getRewardItems':
		$result = array('msg'=>'success','items'=>array());
		$attendance = new attendance();
		if(false === $attendance->_set($_REQUEST['aidx'])) $result['msg'] = $attendance->_msg();
		else if(false === $items = $attendance->_getRewards()) $result['msg'] = $attendance->_msg();
		else{
			$vno = 1;
			foreach($items as $item){
				array_push($result['items'],$item);
			}
		}	
		$json = new Services_JSON();
		//if(PHP_VERSION > '5.2') array_walk($result,'_encode');
		//array_walk($result,'_encode');
		echo $json->encode($result);
		exit;
		break;
	case 'delete':
		$attendance = new attendance();
		$errmsg = '';
		if(false === $attendance->_deleteItems($_POST['aidxs'])) $errmsg = $attendance->_msg();
		if(!_empty($errmsg)){
			_alert($errmsg,'-1');
		}else{
			_alert('삭제 되었습니다.','/admin/market_attendance.php');
		}		
		break;
	case 'deleteStamp':
		$attendance = new attendance();
		$errmsg = '';
		if(false === $attendance->_set($_REQUEST['aidx'])) $errmsg = $attendance->_msg();
		if(_empty($errmsg) && false === $cnt = $attendance->_deleteStamp($_REQUEST)) $errmsg = $attendance->_msg();
		if(!_empty($errmsg)){
			_alert($errmsg,'-1');
		}else{
			_alert('삭제 되었습니다.','/admin/market_attendance.php?act=viewstamp&aidx='.$_REQUEST['aidx']);
		}		
		break;
}

?>