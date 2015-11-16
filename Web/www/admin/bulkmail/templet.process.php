<?
// ajax json 을 통한 백그라운드 실행 처리용 파일
$Dir="../../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/class/bulkmail.php");

$bulkmail = new bulkmail();

include ("../access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-4";
$MenuCode = "market";
//DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
$result = array();
if(_DEMOSHOP=="OK" && getenv("REMOTE_ADDR")!=_ALLOWIP){
	$result['err'] = '데모버전에서는 테스트가 불가능 합니다.';
}else if(!$_usersession->isAllowedTask($PageCode)) {
	$result['err'] = '접근 권한이 없습니다.';
}else{
	array_walk($_REQUEST,'_iconvFromUtf8');		
	switch($_REQUEST['act']){
		case 'list':
			$res = $bulkmail->_templetList($_REQUEST['tpidx']);
			break;
		case 'read':
			$res = $bulkmail->_readTemplet($_REQUEST['idx']);
			break;
		case 'edit':
		case 'add':
		case 'modify':
			$res = $bulkmail->_editTemplet($_REQUEST);
			break;
		case 'delete':
			$res = $bulkmail->_deleteTemplet($_REQUEST['tpidx']);
			break;
		default:
			$result['err'] = '정의되지 않은 메서드 입니다.';
			break;
	}
	if(!$res['result']) $result['err'] = $res['msg'];
	else if(isset($res['items'])){
		$result['items'] = array();
		$result['items'] = $res['items'];
	}
	if(!isset($result['err']) || empty($result['err'])) $result['err'] = 'ok';
}

//array_walk($result,'_encode');		
exit(json_encode($result));

// 필요 함수 정리 - 향후 패치등에서 함수 통합 관리 필요
if(!function_exists('_encode')){
function _encode(&$value,$key){
	if(is_array($value)){
		array_walk($value,'_encode');
	}else{
		$value = iconv('EUC-KR','UTF-8',$value);
	}
}
}

if(!function_exists('_iconvFromUtf8')){
function _iconvFromUtf8(&$value,$key){
	if(is_array($value)){
		array_walk($value,'_iconvFromUtf8');
	}else{
		$value = iconv('UTF-8','EUC-KR',$value);
	}
}
}
?>
